<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pabs_model extends CI_Model {
  
  function __construct()
  { 
    parent::__construct();
    $this->load->config('keywords');
    $this->load->model('Common_model');
    $UserUID = $this->session->userdata('UserUID');
  }

  /************************************************** D2T to API Functions *******************************************************/

  function GetOrderType($OrderTypeUID){
    $this->db->select ( '*' ); 
    $this->db->from ( 'mordertypes' );
    $this->db->where(array('mordertypes.OrderTypeUID'=>$OrderTypeUID));
    $query = $this->db->get();
    return $query->row()->OrderTypeName;
  }

  function GetCountyStateUID($CountyName,$StateCode){
    $this->db->select ( '*' ); 
    $this->db->from ( 'mcounties' );
    $this->db->join('mstates','mstates.StateUID=mcounties.StateUID');
    $this->db->where(array('mcounties.CountyName'=>$CountyName, 'mstates.StateCode'=>$StateCode));
    $query = $this->db->get();
    return $query->row();
  }

  function GetBorrowers($OrderUID)
  {
    $query = $this->db->query("SELECT * FROM torderpropertyroles WHERE PropertyRoleUID = 5 and  OrderUID='$OrderUID' ");
    return $query->result();
  }

  function SendOrderToAPI($OrderUID, $AbstractorUID, $torderabstractor, $Attachment, $Upload_Files, $Upload_FilesName, $ApiOutBoundOrderUID,$UserUID){

    $UserDetails = $this->common_model->GetUserDetailsByUser($UserUID);
    $UserName = $UserDetails->UserName;

    $OrderDetails = $this->common_model->get_orderdetails($OrderUID);
    $AbstractorDetails = $this->GetAbstractorDetails($AbstractorUID);
    $AbstractorNo = $AbstractorDetails->AbstractorNo;
    $OrderTypeName = $this->GetOrderType($torderabstractor['OrderTypeUID']);

    $torderabstractor['AbstractorNo'] = $AbstractorNo;
    $torderabstractor['OrderTypeName'] = $OrderTypeName;

    $CountyName=$OrderDetails->PropertyCountyName;
    $StateCode=$OrderDetails->PropertyStateCode;
    $CountyStateDetails = $this->GetCountyStateUID($CountyName,$StateCode);

    $CountyFIPS = $CountyStateDetails->CountyFIPSCode;
    $CountyFIPSCode = substr($CountyFIPS, 2);
    $StateFIPSCode = $CountyStateDetails->FIPSCode;

    $OrderDetails->CountyFIPSCode = $CountyFIPSCode;
    $OrderDetails->StateFIPSCode = $StateFIPSCode;

    $BorrowersList = $this->GetBorrowers($OrderUID);

    $Comment = '';

    $Content = [];
    $Content_FileName = [];

    $FileName = $OrderDetails->OrderNumber . ' - Abstractor OrderForm.pdf';
    $text = file_get_contents($Attachment);
    $Content[] = base64_encode($text);
    $Content_FileName[] = $FileName;

    foreach ($Upload_Files as $key => $value) {
      $path = file_get_contents($value);
      $Content[] = base64_encode($path);
    }

    foreach ($Upload_FilesName as $key => $value) {
      $Content_FileName[] = $value;      
    }

    $Documents = array();

    foreach($Content_FileName as $key => $value){
      $Documents[$key]['FileName'] = $value;
    }
    
    foreach($Content as $keys => $value){
      $Documents[$keys]['Content'] = $value;
    }

    $url_send = $this->config->item("api_url");
    $OrgCode = 'isgn';

    $data = array(
      'ApiOutBoundOrderUID' => $ApiOutBoundOrderUID,
      'OrderUID' => $OrderUID,
      'UserName' => $UserName,
      'BorrowersList' => $BorrowersList,
      'OrderDetails' => $OrderDetails,
      'AbstractorDetails' => $torderabstractor,
      'Documents' => $Documents,
      'Comment' => $Comment,
      'EventCode' => 'AT01',
      'SourceName'=>'Pabs'
    );

    $str_data = json_encode($data);

    $Result = $this->sendPostData($url_send, $str_data, $OrgCode,$OrderUID);

    return $Result;
  }

  function GetAbstractorDetails($AbstractorUID){
    $this->db->select('*')->from('mabstractor');
    $this->db->where('AbstractorUID', $AbstractorUID);
    return $this->db->get()->row();
  }

  function sendPostData($url, $post, $OrgCode, $OrderUID){

    $Orders = json_decode($post);
    $OrderSourceName = $Orders->SourceName;
    $Documents = $Orders->Documents;
    $ApiOutBoundOrderUID = $Orders->ApiOutBoundOrderUID;

    $this->db->select("*");
    $this->db->from('mApiTitlePlatform');
    $this->db->where(array("mApiTitlePlatform.OrderSourceName"=>trim($OrderSourceName)));
    $query = $this->db->get();
    $SourceName = $query->row();

    $OrderSourceUID = $SourceName->OrderSourceUID;
    $APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 3000,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $post,
      CURLOPT_HTTPHEADER => array(
       "authorization: ".$APiAuthKeyDetails->APIAuthKey,
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
      //echo "cURL Error #:" . $err;
    } else {

      $SuccessMsg = explode('$', $response);
      $API = $SuccessMsg[0];
      $Action = trim($SuccessMsg[1]);
      $InBoundUID = trim($SuccessMsg[2]);
      $TransactionNO = trim($SuccessMsg[3]);
			/* 	NF262	 Pabs Notes update*/
      $ResponseResult = trim($SuccessMsg[4]);

      if($Action === 'AT01'){
        if($ResponseResult == 'Success'){
          $Notes = ' is sent to Pabs API';
          $Result = $this->UpdateSuccessReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$Documents,$ApiOutBoundOrderUID);
        } else if($ResponseResult == 'Failed'){
          $Notes = ' is failed';
          $Result = $this->UpdateFailedReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$Documents,$ApiOutBoundOrderUID);
        }
        return $Result; exit;
      } 
    }
  }

  function UpdateSuccessReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$Documents,$ApiOutBoundOrderUID )
  {
    $Ack_Insert['IsSendtoAPI'] = 1;
    $this->db->where('ApiOutBoundOrderUID', $ApiOutBoundOrderUID);
    $this->db->update('tApiOutBoundOrders', $Ack_Insert);

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
                  'Note' => $Action.$Notes,
                  'SectionUID' => $SectionUID,
                  'OrderUID' => $OrderUID,
                  'RoleType' => '1,2,3,4,5,6,7,9,11,12',
                  'CreatedByAPI' => $OrderSourceUID,
                  'CreatedOn' => Date('Y-m-d H:i:s',strtotime("now"))
                  );       

    $this->db->insert("tordernotes", $insert_notes);

    $this->db->select('*')->from('musers');
    $this->db->where('LoginID', 'isgn');
    $query=$this->db->get();
    $UserName=$query->row()->UserName;
    $UserID=$query->row()->UserUID;

    $data1['ModuleName']=$Notes.'-insert';
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['DateTime']=Date('Y-m-d H:i:s',strtotime("now"));
    $data1['TableName']='tordernotes';
    $data1['OrderUID']=$OrderUID;
    $data1['UserUID']=$UserID;                        
    $this->Common_model->Audittrail_insert($data1);

    $response = 'success';
    return $response;
  }

  function UpdateFailedReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$Documents,$ApiOutBoundOrderUID )
  {
    $Ack_Insert['IsSendtoAPI'] = 0;
    $this->db->where('ApiOutBoundOrderUID', $ApiOutBoundOrderUID);
    $this->db->update('tApiOutBoundOrders', $Ack_Insert);

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
                  'Note' => $Action.$Notes,
                  'SectionUID' => $SectionUID,
                  'OrderUID' => $OrderUID,
                  'RoleType' => '1,2,3,4,5,6,7,9,11,12',
                  'CreatedByAPI' => $OrderSourceUID,
                  'CreatedOn' => Date('Y-m-d H:i:s',strtotime("now"))
                  );       

    $this->db->insert("tordernotes", $insert_notes);

    $this->db->select('*')->from('musers');
    $this->db->where('LoginID', 'isgn');
    $query=$this->db->get();
    $UserName=$query->row()->UserName;
    $UserID=$query->row()->UserUID;

    $data1['ModuleName']=$Notes.'-insert';
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['DateTime']=Date('Y-m-d H:i:s',strtotime("now"));
    $data1['TableName']='tordernotes';
    $data1['OrderUID']=$OrderUID;
    $data1['UserUID']=$UserID;                        
    $this->Common_model->Audittrail_insert($data1);

    $response = 'failed';
    return $response;
  }

  function GetNoteTypeUID($SectionName)
  {
    $this->db->select("*");
    $this->db->from('mreportsections');
    $this->db->where(array("mreportsections.SectionName"=>$SectionName));
    $query = $this->db->get();
    return $query->row();
  }


  /************************************************** API to D2T Functions *******************************************************/

  function toAbsAcceptOrder($fieldArray,$CreatedByAPI,$OrderUID){   

    $this->load->model('Abstractor_My_Orders/Abstractor_My_Orders_model');

    $OrderNumber = $fieldArray['OrderNumber'];
    $TransactionID = $fieldArray['TransactionID'];
    $ProviderOrderNbr = $fieldArray['ProviderOrderNbr'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Status = 3;
    $Action = 'Accept';
    $Details = $this->GetAbstractorUserUIDByOrderUID($OrderUID,$Action);
    $AbstractorUID = $Details['AbstractorUID'];
    $UserUID = $Details['UserUID'];

    if($Details){
      $AbstractorUID = $Details['AbstractorUID'];
      $ApiOutBoundOrderUID = $Details['ApiOutBoundOrderUID'];
      $UserUID = $Details['UserUID'];

      $Ack_Insert['IsSendtoAPI'] = 1;
      $this->db->where('ApiOutBoundOrderUID', $ApiOutBoundOrderUID);
      $this->db->update('tApiOutBoundOrders', $Ack_Insert);

      $result = $this->Abstractor_My_Orders_model->Change_order_status($OrderUID,$Status,$UserUID);
      if($result){
      
        $Ack_Insert['ProviderOrderNumber'] = $ProviderOrderNbr[0];
        $Ack_Insert['AcceptedDateTime'] = Date('Y-m-d H:i:s',strtotime("now"));
        $Ack_Insert['AcceptedBy'] = $CreatedByAPI;
        $Ack_Insert['Status'] = 'Accepted';
        $this->db->where('OrderUID', $OrderUID);
        $this->db->where('Status', 'New');
        $this->db->update('tApiOutBoundOrders', $Ack_Insert);

        $Notes = 'Search Order Accepted By API Abstractor. <br>Action: AT02 <br>Comments:'.$Comment;
        $this->AddNotes($Notes, $OrderUID, $CreatedByAPI, $InBoundUID);

        $ModuleName = $Notes. ' -insert';
        $TableName = 'torderabstractor';
        $this->AuditTrailFunc($ModuleName, $TableName, $UserUID, $OrderUID);

        $res = array('success' => 1,'InBoundUID' => $InBoundUID);
      }else{
        $res = array('success' => 0,'InBoundUID' => $InBoundUID);
      } 
    } else {
      $res = array('success' => 0,'InBoundUID' => $InBoundUID,'message' => 'Item Already Received');
    }

    echo json_encode($res);
  }

  function toAbsRejectOrder($fieldArray,$CreatedByAPI,$OrderUID){

    $this->load->model('Abstractor_My_Orders/Abstractor_My_Orders_model');

    $OrderNumber = $fieldArray['OrderNumber'];
    $TransactionID = $fieldArray['TransactionID'];
    $ProviderOrderNbr = $fieldArray['ProviderOrderNbr'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    $Action = 'Reject';
    $Details = $this->GetAbstractorUserUIDByOrderUID($OrderUID,$Action);
    $AbstractorUID = $Details['AbstractorUID'];
    $UserUID = $Details['UserUID'];

    if($Details){
      $AbstractorUID = $Details['AbstractorUID'];
      $UserUID = $Details['UserUID'];
      $ApiOutBoundOrderUID = $Details['ApiOutBoundOrderUID'];

      $NoteType = $this->GetNoteTypeUID('API Note');
      $SectionUID = $NoteType->SectionUID;

      $rejection=array('Remarks'=>$Comment,'Reason'=>$SectionUID, 'OrderFlag'=>2);

      $result = $this->Abstractor_My_Orders_model->rejectorder_in_queue($OrderUID,$UserUID,$rejection);
      if($result){

       $this->db->where(array('ApiOutBoundOrderUID' => $ApiOutBoundOrderUID ));
       $this->db->update('tApiOutBoundOrders', array('Status'=>'Rejected'));

        $Ack_Insert['RejectedDateTime'] = Date('Y-m-d H:i:s',strtotime("now"));
        $Ack_Insert['RejectedBy'] = $CreatedByAPI;
        $Ack_Insert['Status'] = 'Rejected';
        $this->db->where('OrderUID', $OrderUID);
        $this->db->where('Status', 'New');
        $this->db->update('tApiOutBoundOrders', $Ack_Insert);

        $Notes = 'Search Order Rejected By API Abstractor. <br>Action: AT03 <br>Comments:'.$Comment;
        $this->AddNotes($Notes, $OrderUID, $CreatedByAPI, $InBoundUID);

        $ModuleName = $Notes. ' -insert';
        $TableName = 'torderabstractor';
        $this->AuditTrailFunc($ModuleName, $TableName, $UserUID, $OrderUID);

        $res = array('success' => 1,'InBoundUID' => $InBoundUID);
      }else{
        $res = array('success' => 0,'InBoundUID' => $InBoundUID);
      }
    } else {
      $res = array('success' => 0,'InBoundUID' => $InBoundUID);
    }

    echo json_encode($res);
  }

  function toAbsComments($fieldArray,$CreatedByAPI,$OrderUID){

    $OrderNumber = $fieldArray['OrderNumber'];
    $TransactionID = $fieldArray['TransactionID'];
    $ProviderOrderNbr = $fieldArray['ProviderOrderNbr'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Comment,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'InBoundUID' => $InBoundUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);

    if($result){

      $Notes = 'Notes Send By API Abstractor. <br>Action: AT05 <br>Comments:'.$Comment;
      $this->AddNotes($Notes, $OrderUID, $CreatedByAPI);

      $ModuleName = $Notes. ' -insert';
      $TableName = 'torderabstractor';
      $this->AuditTrailFunc($ModuleName, $TableName, $UserUID, $OrderUID);

      $res = array('success' => 1,'InBoundUID' => $InBoundUID);
    }else{
      $res = array('success' => 0,'InBoundUID' => $InBoundUID);
    }

    echo json_encode($res);
  }

  function toAbsOnHold($fieldArray,$CreatedByAPI,$OrderUID){

    $OrderNumber = $fieldArray['OrderNumber'];
    $TransactionID = $fieldArray['TransactionID'];
    $ProviderOrderNbr = $fieldArray['ProviderOrderNbr'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;
    $Action = 'On-Hold';
    $Details = $this->GetAbstractorUserUIDByOrderUID($OrderUID,$Action);
    $AbstractorUID = $Details['AbstractorUID'];
    $UserUID = $Details['UserUID'];

    if($Details){
      $AbstractorUID = $Details['AbstractorUID'];
      $UserUID = $Details['UserUID'];

      $MenuUrl = 'order_search';
      $Type = $this->getmonholddetails();

      $HoldOrder = $this->submitholdorder($OrderUID,$Comment,$Type,$SectionUID,$MenuUrl,$UserUID);

      if($HoldOrder == 1) {

        $Notes = 'On-Hold By API Abstractor. <br>Action: AT06 <br>Comments:'.$Comment;
        $this->AddNotes($Notes, $OrderUID, $CreatedByAPI, $InBoundUID);

        $ModuleName = $Notes. ' -insert';
        $TableName = 'torderabstractor';
        $this->AuditTrailFunc($ModuleName, $TableName, $UserUID, $OrderUID);

        $res = array('success' => 1,'InBoundUID' => $InBoundUID);

      } else {
        $res = array('success' => 0,'InBoundUID' => $InBoundUID);
      }
    } else {
      $res = array('success' => 0,'InBoundUID' => $InBoundUID);
    }

    echo json_encode($res);
  }
  
  function toAbsResume($fieldArray,$CreatedByAPI,$OrderUID){

    $OrderNumber = $fieldArray['OrderNumber'];
    $TransactionID = $fieldArray['TransactionID'];
    $ProviderOrderNbr = $fieldArray['ProviderOrderNbr'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;
    $Action = 'Resume';
    $Details = $this->GetAbstractorUserUIDByOrderUID($OrderUID,$Action);
    $AbstractorUID = $Details['AbstractorUID'];
    $UserUID = $Details['UserUID'];

    if($Details){
      $AbstractorUID = $Details['AbstractorUID'];
      $UserUID = $Details['UserUID'];

      $MenuUrl = 'order_search';

      $ResumeOrder = $this->changestatusholdorder($OrderUID,$MenuUrl,$UserUID);

      if($ResumeOrder == 1) {
        $Notes = 'Order Resumed By API Abstractor. <br>Action: AT08 <br>Comments:'.$Comment;
        $this->AddNotes($Notes, $OrderUID, $CreatedByAPI,$InBoundUID);

        $ModuleName = $Notes.'-insert';
        $TableName = 'torderabstractor';
        $this->AuditTrailFunc($ModuleName, $TableName, $UserUID, $OrderUID);

        $res = array('success' => 1,'InBoundUID' => $InBoundUID);

      } else {
        $res = array('success' => 0,'InBoundUID' => $InBoundUID);
      }
    } else {
      $res = array('success' => 0,'InBoundUID' => $InBoundUID);
    }

    echo json_encode($res);
  }

  function toAbsCancel($fieldArray,$CreatedByAPI,$OrderUID){

    $OrderNumber = $fieldArray['OrderNumber'];
    $TransactionID = $fieldArray['TransactionID'];
    $ProviderOrderNbr = $fieldArray['ProviderOrderNbr'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    $Action = 'Cancel';
    $Details = $this->GetAbstractorUserUIDByOrderUID($OrderUID,$Action);
    $AbstractorUID = $Details['AbstractorUID'];
    $UserUID = $Details['UserUID'];
    $AbstractorOrderUID = $Details['torderabstractor']->AbstractorOrderUID;

    if($Details){
      $AbstractorUID = $Details['AbstractorUID'];
      $UserUID = $Details['UserUID'];
      $AbstractorOrderUID = $Details['torderabstractor']->AbstractorOrderUID;
      $ApiOutBoundOrderUID = $Details['ApiOutBoundOrderUID'];

      $this->load->model('order_search/ordersearch_model');

      $this->db->trans_begin();

      $this->db->select('AbstractorUID')->from('torderabstractor');
      $this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
      $torderabstractor=$this->db->get()->row();

      $this->db->select('*')->from('torderabstractor');
      $this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
      $torderabstractor_array=$this->db->get()->row_array();

      $torderabstractor_array['AbstractorStatusCode']='Unassigned';
      unset($torderabstractor_array['AbstractorOrderUID']);
      $torderabstractor_array['ApprovalStatus']=0;
      $torderabstractor_array['RejectedByUserUID']=$UserUID;
      $torderabstractor_array['RejectedDateTime']=date('Y-m-d H:i:s');

      $this->db->insert('torderabstractorunassign', $torderabstractor_array);    

      $this->db->select('UserUID')->from('musers');
      $this->db->where('AbstractorUID', $torderabstractor->AbstractorUID);
      $mabstractor=$this->db->get()->row();

      $delete=$this->ordersearch_model->ClearAbstractorByAbstractorOrderUID($AbstractorOrderUID);

      $this->ordersearch_model->clear_assigned_abstractor($OrderUID,$AbstractorUID);

      $this->load->model('fees_pricing/fees_pricing_model');
      $abstractorfees=$this->fees_pricing_model->get_Abstractor_fee($OrderUID);

      if (count($abstractorfees) >0) {
        $this->ordersearch_model->UpdateAbstractorFees($OrderUID, $abstractorfees->AbstractorFee, $abstractorfees->AbstractorCopyCost, $abstractorfees->AbstractorAdditionalFee,$abstractorfees->AbstractorActualFee);
      }
      else{
        $this->ordersearch_model->UpdateAbstractorFees($OrderUID, 0, 0, 0,0);
      }

      $this->db->where(array('ApiOutBoundOrderUID' => $ApiOutBoundOrderUID ));
      $this->db->update('tApiOutBoundOrders', array('Status'=>'Cancelled')); 

      if ($this->db->trans_status()===FALSE) {
        $this->db->trans_rollback();
        $res = array('success' => 0,'InBoundUID' => $InBoundUID);
      }
      else{

        $Notes = 'Abstractor Order Cancelled By API Abstractor. <br>Action: AT07 <br>Comments:'.$Comment;
        $this->AddNotes($Notes, $OrderUID, $CreatedByAPI, $InBoundUID);

        $ModuleName = $Notes.'-insert';
        $TableName = 'torderabstractor';
        $this->AuditTrailFunc($ModuleName, $TableName, $UserUID, $OrderUID);

        $this->db->trans_commit();
        $res = array('success' => 1,'InBoundUID' => $InBoundUID);
      }
    } else {
      $res = array('success' => 0,'InBoundUID' => $InBoundUID);
    }
      
    echo json_encode($res);
  }

  function toAbsPDFDelivery($fieldArray,$CreatedByAPI,$OrderUID){

    $OrderNumber = $fieldArray['OrderNumber'];
    $TransactionID = $fieldArray['TransactionID'];
    $ProviderOrderNbr = $fieldArray['ProviderOrderNbr'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    $Documents = $fieldArray['Documents'];
    $Action = 'PDF-Delivery';
    $Details = $this->GetAbstractorUserUIDByOrderUID($OrderUID,$Action);
    $AbstractorUID = $Details['AbstractorUID'];
    $UserUID = $Details['UserUID'];

    if($Details){
      $AbstractorUID = $Details['AbstractorUID'];
      $UserUID = $Details['UserUID'];

      $torders = $this->common_model->get_orderdetails($OrderUID);

      $document_count = 0;

      foreach ($Documents as $key => $value) {

        $Document = $value['Content'];  
        $FileName = $value['FileName'];  
        $FinalPDF = base64_decode($Document);   
        $Plus = $i+1;

        if($torders->OrderDocsPath!= NULL)
        {
          $OrderDocs_Path = $torders->OrderDocsPath;
        }else{

          $date = date('Ymd');
          $OrderDocsPath = 'uploads/searchdocs/'.$date.'/'.$OrderNumber.'/';
          $query = $this->db->query("update torders SET OrderDocsPath =".$OrderDocsPath." Where OrderUID='".$OrderUID."' ");

          $OrderDocs_Path = $OrderDocsPath;
        }

        $this->db->like('DocumentFileName', $FileName);
        $this->db->where(array('OrderUID' => $OrderUID));
        $torderdocuments=$this->db->get('torderdocuments');
        $searchdocumentcount=$torderdocuments->num_rows();
        $searchdocumentcount+=1;

        $ApiDocumentFileName = $this->GetAvailFileName($FileName, '.pdf', $searchdocumentcount, $OrderUID);

        $file = FCPATH . $OrderDocs_Path . $ApiDocumentFileName;            

        if (!is_dir($OrderDocs_Path)) {
          mkdir($OrderDocs_Path, 0777, true);
        } 
        
        file_put_contents($file, $FinalPDF);
        $DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode('Search');
        $Insert = array(
          'OrderUID'=>$OrderUID,
          'DocumentTypeUID'=>$DocumentTypeUID,
          'SearchModeUID'=>'5',
          'DocumentFileName'=>$ApiDocumentFileName,
          'DisplayFileName'=>$ApiDocumentFileName,
          'UploadedUserUID'=>$UserUID,
          'UploadedDate'=>date('Y-m-d H:i:s'),
          'AbstractorUID'=>$AbstractorUID,
          'TypeOfDocument'=> $DocumentTypeUID,
          'DocumentCreatedDate'=>date('Y-m-d H:i:s'),
          'IsDocumentReceived'=> 1,
          'DocumentReceivedDateTime'=> date('Y-m-d H:i:s')
        );

        $res = $this->db->insert('torderdocuments',$Insert);

        if($res){
          $document_count++;

          $Notes = $ApiDocumentFileName . 'is received from Pabs API';
          $this->AddNotes($Notes, $OrderUID, $CreatedByAPI);

          $ModuleName = $ApiDocumentFileName . 'is received from Pabs API -insert';
          $TableName = 'torderdocuments';
          $this->AuditTrailFunc($ModuleName, $TableName, $UserUID, $OrderUID);
        }
      }

      if($document_count>0){
        $Notes = 'PDF Delivered By API Abstractor. <br>Action: AT04 <br>Comments: '.$Comment;
        $this->AddNotes($Notes, $OrderUID, $CreatedByAPI, $InBoundUID);

        $res = array('success' => 1,'InBoundUID' => $InBoundUID);
      }else{
        $res = array('success' => 0,'InBoundUID' => $InBoundUID);
      } 
    } else {
      $res = array('success' => 0,'InBoundUID' => $InBoundUID);
    }

    echo json_encode($res);
  }

  function toXMLDelivery($fieldArray,$CreatedByAPI,$OrderUID){

    $OrderNumber = $fieldArray['OrderNumber'];
    $TransactionID = $fieldArray['TransactionID'];
    $ProviderOrderNbr = $fieldArray['ProviderOrderNbr'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    $Documents = $fieldArray['Documents'];
    $OrderDetails = $fieldArray['OrderDetails'];
    $Action = 'XML-Delivery';
    $Details = $this->GetAbstractorUserUIDByOrderUID($OrderUID,$Action);
    $AbstractorUID = $Details['AbstractorUID'];
    $UserUID = $Details['UserUID'];

    if($Details){

      $AbstractorUID = $Details['AbstractorUID'];
      $UserUID = $Details['UserUID'];
      $Fees = $OrderDetails['Fees'];
      $SearchDate = $OrderDetails['SearchDate'];
      $EffectiveDate = $OrderDetails['EffectiveDate'];
      $LegalDescription = $OrderDetails['LegalDescription']['LongLegalDesc'];
      $DeedList = $OrderDetails['DeedList'];
      $MortgageList = $OrderDetails['MortgageList'];
      $LienList = $OrderDetails['LienList'];
      $JudgementList = $OrderDetails['JudgementList'];
      $TaxRecords = $OrderDetails['TaxRecords'];
      $AssessedAddress = $OrderDetails['AssessedAddress'];

      $LegalDescription = is_array($LegalDescription) ? '' : $LegalDescription; 

      if($LegalDescription){
        $last_char = substr(trim($LegalDescription), -1); 
        if($last_char !== "."){
          $LegalDescription = $LegalDescription.".";
        }
      }


      $SearchDate = is_array($SearchDate) ? '' : $SearchDate; 
      $SearchDate = ($SearchDate == '') ? $SearchDate : Date('Y-m-d h:i:s',strtotime($SearchDate));

      $EffectiveDate = is_array($EffectiveDate) ? '' : $EffectiveDate; 
      $EffectiveDate = ($EffectiveDate == '') ? $EffectiveDate : Date('Y-m-d h:i:s',strtotime($EffectiveDate));

      $DeedData = '';
      $MortgageData = '';
      $LienData = '';
      $JudgementData = '';
      $TaxData = '';

      foreach ($DeedList as $key => $value) {
        if($value[0]){
          $DeedData = $value;
        } else {
          $DeedData = [];
          $DeedData[] = $value;
        }
      }

      foreach ($MortgageList as $key => $value) {
        if($value[0]){
          $MortgageData = $value;
        } else {
          $MortgageData = [];
          $MortgageData[] = $value;
        }
      }

      foreach ($LienList as $key => $value) {
        if($value[0]){
          $LienData = $value;
        } else {
          $LienData = [];
          $LienData[] = $value;
        }
      }

      foreach ($JudgementList as $key => $value) {
        if($value[0]){
          $JudgementData = $value;
        } else {
          $JudgementData = [];
          $JudgementData[] = $value;
        }
      }

      foreach ($TaxRecords as $key => $value) {
        if($value[0]){
          $TaxData = $value;
        } else {
          $TaxData = [];
          $TaxData[] = $value;
        }
      }

      $this->db->trans_begin();

      /* Updating Date Values in tOrderDocuments*/

      $SearchOrder = array(
        "SearchFromDate"=>$SearchDate,
        "SearchAsOfDate"=>$EffectiveDate
      );

      $this->db->where(array("OrderUID"=>$OrderUID)); 
      $torderdocuments = $this->db->update('torderdocuments', $SearchOrder);

      /* Updating Legal Description*/

      $LegalData = array(
        "OrderUID"=>$OrderUID,
        "LegalDescription"=>$LegalDescription
      );

      $CheckLegalValue = $this->CheckLegalTableValue($OrderUID);
      $CheckLegalValue =  $CheckLegalValue->CheckLegalValue;

      if($CheckLegalValue == 0){
        $torderlegaldescription = $this->db->insert('torderlegaldescription', $LegalData);
      } else{
        $this->db->where(array("OrderUID"=>$OrderUID)); 
        $torderlegaldescription = $this->db->update('torderlegaldescription', $LegalData);
      }

      /* Inserting Deed Values */

      foreach ($DeedData as $key => $value) {

        $DocumentTypeUID = $this->config->item('DocumentTypeUID')['Deeds'];
        $DocumentTypeName = $value['DeedType'];
        $GrantorList = is_array($value['GrantorList']['Grantor']['PrimaryName']) ? '' : $value['GrantorList']['Grantor']['PrimaryName'];
        $GranteeList = is_array($value['GranteeList']['Grantee']['PrimaryName']) ? '' : $value['GranteeList']['Grantee']['PrimaryName'];
        $subDocument = $this->get_DocumentTypeUID($DocumentTypeUID,$DocumentTypeName);

        $Dated = is_array($value['Dated']) ? '' : $value['Dated']; 
        $RecordingDate = is_array($value['RecordingDate']) ? '' : $value['RecordingDate']; 
        $DocumentNo = is_array($value['DocumentNumber']) ? '' : $value['DocumentNumber']; 

        $DeedType = is_array($value['DeedType']) ? '' : $value['DeedType']; 
        $DocumentTypeUID = $subDocument['DocumentTypeUID'];
        $DeedDated = ($Dated == '') ? $Dated : Date('Y-m-d',strtotime($Dated));
        $DeedRecorded = ($RecordingDate == '') ? $RecordingDate : Date('Y-m-d',strtotime($RecordingDate));
        $ConsiderationAmount = is_array($value['Amount']) ? '0.00' : $value['Amount']; 
        $DocumentNo = ($DocumentNo == '') ? ' ' : $DocumentNo;
        $Book = is_array($value['Book']) ? '' : $value['Book']; 
        $Page = is_array($value['Page']) ? '' : $value['Page']; 
        $Instrument = is_array($value['Instrument']) ? ' ' : $value['Instrument']; 
        $Township = is_array($value['Township']) ? ' ' : $value['Township']; 
        $DeedComments = is_array($value['Comment']) ? ' ' : $value['Comment']; 

        $BooKPage  = '';
        if($Book == '' && $Page == ''){
          $BooKPage  = '';
        } else{
          $BooKPage  = $Book.'/'.$Page;
        }

        $deed_datas['OrderUID'] = $OrderUID;
        $deed_datas['DeedType'] = $DeedType;
        $deed_datas['DocumentTypeUID'] = $DocumentTypeUID;
        $deed_datas['DeedDated'] = isset($DeedDated) ? $DeedDated : '';
        $deed_datas['DeedRecorded'] = isset($DeedRecorded) ? $DeedRecorded : '';
        $deed_datas['ConsiderationAmount'] = isset($ConsiderationAmount) ? $ConsiderationAmount : '';
        $deed_datas['DocumentNo'] = isset($DocumentNo) ? $DocumentNo : '';
        $deed_datas['Deed_DBVTypeUID_1'] = 2;
        $deed_datas['Deed_DBVTypeValue_1'] = isset($BooKPage) ? $BooKPage : '';
        $deed_datas['Deed_DBVTypeUID_2'] = 7;
        $deed_datas['Deed_DBVTypeValue_2'] = isset($Instrument) ? $Instrument : '';
        $deed_datas['Township'] = isset($Township) ? $Township : '';
        $deed_datas['DeedComments'] = isset($DeedComments) ? $DeedComments : '';

        $this->db->insert('torderdeeds', $deed_datas);
        $DeedSNo = $this->db->insert_id();

        $Grantor = $this->config->item('PartyTypeUID')['Grantor'];
        $Grantee = $this->config->item('PartyTypeUID')['Grantee'];

        $GrantorData = array(
          "OrderUID"=>$OrderUID,
          "DeedSNo"=>$DeedSNo,
          "PartyTypeUID"=>$Grantor,
          "PartyName"=>isset($GrantorList) ? $GrantorList : '',
        );
        $this->db->insert('torderdeedparties', $GrantorData);

        $GranteeData = array(
          "OrderUID"=>$OrderUID,
          "DeedSNo"=>$DeedSNo,
          "PartyTypeUID"=>$Grantee,
          "PartyName"=>isset($GranteeList) ? $GranteeList : '',
        );
        $this->db->insert('torderdeedparties', $GranteeData);
      }

      /* Inserting Mortgage Values */

      foreach ($MortgageData as $key => $value) {

        $DocumentTypeUID = $this->config->item('DocumentTypeUID')['Mortgages'];
        $DocumentTypeName = $value['MortgageType'];
        $subDocument = $this->get_DocumentTypeUID($DocumentTypeUID,$DocumentTypeName);

        $MortgagorList = is_array($value['Mortgagor']) ? ' ' : $value['Mortgagor']; 
        $MortgageeList = is_array($value['Mortgagee']) ? ' ' : $value['Mortgagee']; 
        $Trustee = is_array($value['Trustee']) ? ' ' : $value['Trustee']; 
        $LienType = is_array($value['LienType']) ? ' ' : $value['LienType']; 
        $OpenEnded = is_array($value['OpenEnded']) ? ' ' : $value['OpenEnded']; 
        $Book = is_array($value['Book']) ? '' : $value['Book']; 
        $Page = is_array($value['Page']) ? '' : $value['Page']; 
        $Instrument = is_array($value['Instrument']) ? ' ' : $value['Instrument']; 
        $Dated = is_array($value['Dated']) ? '' : $value['Dated']; 
        $RecordingDate = is_array($value['RecordingDate']) ? '' : $value['RecordingDate']; 
        $MaturityDate = is_array($value['MaturityDate']) ? '' : $value['MaturityDate']; 
        $Amount = is_array($value['Amount']) ? '0.00' : $value['Amount']; 
        $AdditionalInfo = is_array($value['AdditionalInfo']) ? '' : $value['AdditionalInfo']; 
        $Comment = is_array($value['Comment']) ? ' ' : $value['Comment']; 

        $LienType = $this->GetLienType($LienType);
        $IsOpenEnded = ($OpenEnded == 'Yes') ? 1 : 0;
        $LienTypeUID = ($LienType == '') ? 0 : $LienType;

        $MortgageType = is_array($value['MortgageType']) ? '' : $value['MortgageType'];
        $DocumentTypeUID = $subDocument['DocumentTypeUID'];
        $MortgageDated = ($Dated == '') ? $Dated : Date('Y-m-d',strtotime($Dated));
        $MortgageRecorded = ($RecordingDate == '') ? $RecordingDate : Date('Y-m-d',strtotime($RecordingDate));
        $MortgageMaturityDate = ($MaturityDate == '') ? $MaturityDate : Date('Y-m-d',strtotime($MaturityDate));

        $BooKPage  = '';
        if($Book == '' && $Page == ''){
          $BooKPage  = '';
        } else{
          $BooKPage  = $Book.'/'.$Page;
        }
      
        $mort_data['OrderUID'] = $OrderUID;
        $mort_data['DocumentTypeUID'] = $DocumentTypeUID;
        $mort_data['MortgageType'] = $MortgageType;
        $mort_data['LienTypeUID'] = isset($LienTypeUID) ? $LienTypeUID : '';
        $mort_data['Trustee1'] = isset($Trustee) ? $Trustee : '';
        $mort_data['IsOpenEnded'] = isset($IsOpenEnded) ? $IsOpenEnded : '';
        $mort_data['AdditionalInfo'] = isset($AdditionalInfo) ? $AdditionalInfo : '';
        $mort_data['Mortgage_DBVTypeUID_1'] = 2;
        $mort_data['Mortgage_DBVTypeValue_1'] = isset($BooKPage) ? $BooKPage : '';
        $mort_data['Mortgage_DBVTypeUID_2'] = 7;
        $mort_data['Mortgage_DBVTypeValue_2'] = isset($Instrument) ? $Instrument : '';
        $mort_data['MortgageDated'] = isset($MortgageDated) ? $MortgageDated : '';
        $mort_data['MortgageRecorded'] = isset($MortgageRecorded) ? $MortgageRecorded : '';
        $mort_data['MortgageMaturityDate'] = isset($MortgageMaturityDate) ? $MortgageMaturityDate : '';
        $mort_data['MortgageAmount'] = isset($Amount) ? $Amount : '';
        $mort_data['MortgageComments'] = isset($Comment) ? $Comment : '';

        $this->db->insert('tordermortgages', $mort_data);
        $MortgageSNo = $this->db->insert_id();

        $Mortgagor = $this->config->item('PartyTypeUID')['Mortgagor'];
        $Mortgagee = $this->config->item('PartyTypeUID')['Mortgagee'];

        $MortgagorData = array(
          "OrderUID"=>$OrderUID,
          "MortgageSNo"=>$MortgageSNo,
          "PartyTypeUID"=>$Mortgagor,
          "PartyName"=>isset($MortgagorList) ? $MortgagorList : '',
        );
        $this->db->insert('tordermortgageparties', $MortgagorData);

        $MortgageeData = array(
          "OrderUID"=>$OrderUID,
          "MortgageSNo"=>$MortgageSNo,
          "PartyTypeUID"=>$Mortgagee,
          "PartyName"=>isset($MortgageeList) ? $MortgageeList : '',
        );
        $this->db->insert('tordermortgageparties', $MortgageeData);
      }

      /* Inserting Lien Values */

      foreach ($LienData as $key => $value) {

        $DocumentTypeUID = $this->config->item('DocumentTypeUID')['Liens'];
        $DocumentTypeName = $value['LienType'];
        $subDocument = $this->get_DocumentTypeUID($DocumentTypeUID,$DocumentTypeName);

        $Holder = is_array($value['Holder']) ? ' ' : $value['Holder']; 
        $ExecutedBy = is_array($value['ExecutedBy']) ? ' ' : $value['ExecutedBy']; 
        $Trustee = is_array($value['Trustee']) ? ' ' : $value['Trustee']; 
        $LeinAmount = is_array($value['Amount']) ? '0.00' : $value['Amount']; 
        $Book = is_array($value['Book']) ? '' : $value['Book']; 
        $Page = is_array($value['Page']) ? '' : $value['Page']; 
        $DocumentNumber = is_array($value['DocumentNumber']) ? ' ' : $value['DocumentNumber']; 
        $Dated = is_array($value['Dated']) ? '' : $value['Dated']; 
        $Filed = is_array($value['Filed']) ? '' : $value['Filed']; 
        $Recorded = is_array($value['Recorded']) ? '' : $value['Recorded']; 
        $LeinComments = is_array($value['Comments']) ? ' ' : $value['Comments']; 

        $DocumentTypeUID = $subDocument['DocumentTypeUID'];
        $LeinDated = ($Dated == '') ? $Dated : Date('Y-m-d',strtotime($Dated));
        $LeinFiled = ($Filed == '') ? $Filed : Date('Y-m-d',strtotime($Filed));
        $LeinRecorded = ($Recorded == '') ? $Recorded : Date('Y-m-d',strtotime($Recorded));
        $LeinType = is_array($value['LeinType']) ? '' : $value['LeinType'];

        $BooKPage  = '';
        if($Book == '' && $Page == ''){
          $BooKPage  = '';
        } else{
          $BooKPage  = $Book.'/'.$Page;
        }
      
        $lien_data['OrderUID'] = $OrderUID;
        $lien_data['DocumentTypeUID'] = $DocumentTypeUID;
        $lien_data['LeinType'] = $LeinType;
        $lien_data['Holder'] = isset($Holder) ? $Holder : '';
        $lien_data['ExecutedBy'] = isset($ExecutedBy) ? $ExecutedBy : '';
        $lien_data['Trustee'] = isset($Trustee) ? $Trustee : '';
        $lien_data['LeinAmount'] = isset($LeinAmount) ? $LeinAmount : '';
        $lien_data['Lien_DBVTypeUID_1'] = 2;
        $lien_data['Lien_DBVTypeValue_1'] = isset($BooKPage) ? $BooKPage : '';
        $lien_data['Lien_DBVTypeUID_2'] = 6;
        $lien_data['Lien_DBVTypeValue_2'] = isset($DocumentNumber) ? $DocumentNumber : '';
        $lien_data['LeinDated'] = isset($LeinDated) ? $LeinDated : '';
        $lien_data['LeinFiled'] = isset($LeinFiled) ? $LeinFiled : '';
        $lien_data['LeinRecorded'] = isset($LeinRecorded) ? $LeinRecorded : '';
        $lien_data['LeinComments'] = isset($LeinComments) ? $LeinComments : '';

        $this->db->insert('torderleins', $lien_data);
      }

      /* Inserting Judgement Values */

      foreach ($JudgementData as $key => $value) {

        $DocumentTypeUID = $this->config->item('DocumentTypeUID')['Judgment'];
        $DocumentTypeName = $value['JudgementType'];
        $subDocument = $this->get_DocumentTypeUID($DocumentTypeUID,$DocumentTypeName);

        $PlaintiffList = is_array($value['Plaintiff']) ? ' ' : $value['Plaintiff']; 
        $DefendentList = is_array($value['Defendent']) ? ' ' : $value['Defendent']; 
        $JudgementAmount = is_array($value['Amount']) ? '0.00' : $value['Amount']; 
        $CaseNo = is_array($value['CaseNo']) ? ' ' : $value['CaseNo']; 
        $Book = is_array($value['Book']) ? '' : $value['Book']; 
        $Page = is_array($value['Page']) ? '' : $value['Page']; 
        $Instrument = is_array($value['Instrument']) ? ' ' : $value['Instrument']; 
        $Dated = is_array($value['Dated']) ? '' : $value['Dated']; 
        $Filed = is_array($value['Filed']) ? '' : $value['Filed']; 
        $Recorded = is_array($value['Recorded']) ? '' : $value['Recorded']; 
        $JudgementComments = is_array($value['Comments']) ? ' ' : $value['Comments']; 

        $DocumentTypeUID = $subDocument['DocumentTypeUID'];
        $JudgementDated = ($Dated == '') ? $Dated : Date('Y-m-d',strtotime($Dated));
        $JudgementFiled = ($Filed == '') ? $Filed : Date('Y-m-d',strtotime($Filed));
        $JudgementRecorded = ($Recorded == '') ? $Recorded : Date('Y-m-d',strtotime($Recorded));
        $JudgementType = is_array($value['JudgementType']) ? '' : $value['JudgementType'];

        $BooKPage  = '';
        if($Book == '' && $Page == ''){
          $BooKPage  = '';
        } else{
          $BooKPage  = $Book.'/'.$Page;
        }
      
        $judg_data['OrderUID'] = $OrderUID;
        $judg_data['DocumentTypeUID'] = $DocumentTypeUID;
        $judg_data['JudgementType'] = $JudgementType;
        $judg_data['JudgementAmount'] = isset($JudgementAmount) ? $JudgementAmount : '';
        $judg_data['Judgement_DBVTypeUID_1'] = 2;
        $judg_data['Judgement_DBVTypeValue_1'] = isset($BooKPage) ? $BooKPage : '';
        $judg_data['Judgement_DBVTypeUID_2'] = 7;
        $judg_data['Judgement_DBVTypeValue_2'] = isset($DocumentNumber) ? $DocumentNumber : '';
        $judg_data['JudgementDated'] = isset($JudgementDated) ? $JudgementDated : '';
        $judg_data['JudgementFiled'] = isset($JudgementFiled) ? $JudgementFiled : '';
        $judg_data['JudgementRecorded'] = isset($JudgementRecorded) ? $JudgementRecorded : '';
        $judg_data['JudgementComments'] = isset($JudgementComments) ? $JudgementComments : '';

        $this->db->insert('torderjudgements', $judg_data);
        $JudgementSNo=$this->db->insert_id();

        $Plaintiff = $this->config->item('PartyTypeUID')['Plaintiff'];
        $Defendent = $this->config->item('PartyTypeUID')['Defendent'];

        $PlaintiffData = array(
          "OrderUID"=>$OrderUID,
          "JudgementSNo"=>$JudgementSNo,
          "PartyTypeUID"=>$Plaintiff,
          "PartyName"=>isset($PlaintiffList) ? $PlaintiffList : '',
        );

        $this->db->insert('torderjudgementparties', $PlaintiffData);

        $DefendentData = array(
          "OrderUID"=>$OrderUID,
          "JudgementSNo"=>$JudgementSNo,
          "PartyTypeUID"=>$Defendent,
          "PartyName"=>isset($DefendentList) ? $DefendentList : '',
        );

        $this->db->insert('torderjudgementparties', $DefendentData);
      }

      /* Inserting Tax Values */

      foreach ($TaxData as $key => $value) {

        $DocumentTypeUID = $this->config->item('DocumentTypeUID')['Taxes'];
        $DocumentTypeName = $value['TaxType'];
        $subDocument = $this->get_DocumentTypeUID($DocumentTypeUID,$DocumentTypeName);
        $ExemptionList = $value['ExemptionList']; 

        /*Assessment Insert Starts*/

        $AssessmentYear = is_array($value['AssessmentYear']) ? ' ' : $value['AssessmentYear']; 
        $AssessmentLand = is_array($value['AssessmentLand']) ? '0.00' : $value['AssessmentLand']; 
        $AssessmentTotal = is_array($value['AssessmentTotal']) ? '0.00' : $value['AssessmentTotal']; 

        $assessment_data['OrderUID'] = $OrderUID;
        $assessment_data['AssessedYear'] = $AssessmentYear;
        $assessment_data['Land'] = $AssessmentLand;
        $assessment_data['TotalValue'] = $AssessmentTotal;


        $CheckAssessmentValue = $this->CheckAssessmentTableValue($OrderUID);
        $CheckAssessmentValue =  $CheckAssessmentValue->CheckAssessmentValue;

        if($CheckAssessmentValue == 0){
          $this->db->insert('torderassessment', $assessment_data);
        } 

        /*Assessment Insert Ends*/

        $PaidThru = is_array($value['PaidThru']) ? '' : $value['PaidThru']; 

        $TaxBasis = is_array($value['TaxBasis']) ? '' : $value['TaxBasis']; 
        $PropertyClass = is_array($value['PropertyClass']) ? '' : $value['PropertyClass']; 
        $TaxBasis = $this->GetTaxBasis($TaxBasis);
        $PropertyClass = $this->GetPropertyClass($PropertyClass);

        $TaxType = is_array($value['TaxType']) ? '' : $value['TaxType'];
        $ParcelNumber = is_array($value['TaxID']) ? '' : $value['TaxID']; 
        $AmountDelinquent = is_array($value['DelinquentAmount']) ? '0.00' : $value['DelinquentAmount']; 
        $GoodThroughDate = ($PaidThru == '') ? $PaidThru : Date('Y-m-d',strtotime($PaidThru));
        $TaxComments = is_array($value['Comment']) ? ' ' : $value['Comment']; 
        $TaxBasisUID = ($TaxBasis == '') ? 0 : $TaxBasis;
        $PropertyClassUID = ($PropertyClass == '') ? 0 : $PropertyClass;

        $CaseNo = is_array($value['CaseNo']) ? ' ' : $value['CaseNo']; 
        $Book = is_array($value['Book']) ? '' : $value['Book']; 
        $Page = is_array($value['Page']) ? '' : $value['Page']; 
        $Instrument = is_array($value['Instrument']) ? ' ' : $value['Instrument']; 
        $Dated = is_array($value['Dated']) ? '' : $value['Dated']; 
        $Filed = is_array($value['Filed']) ? '' : $value['Filed']; 
        $Recorded = is_array($value['Recorded']) ? '' : $value['Recorded']; 

        $DocumentTypeUID = $subDocument['DocumentTypeUID'];
        $JudgementDated = ($Dated == '') ? $Dated : Date('Y-m-d',strtotime($Dated));
        $JudgementFiled = ($Filed == '') ? $Filed : Date('Y-m-d',strtotime($Filed));
        $JudgementRecorded = ($Recorded == '') ? $Recorded : Date('Y-m-d',strtotime($Recorded));
      
        $tax_data['OrderUID'] = $OrderUID;
        $tax_data['DocumentTypeUID'] = $DocumentTypeUID;
        $tax_data['TaxType'] = $TaxType;
        $tax_data['ParcelNumber'] = isset($ParcelNumber) ? $ParcelNumber : '';
        $tax_data['TaxBasisUID'] = isset($TaxBasisUID) ? $TaxBasisUID : '';
        $tax_data['PropertyClassUID'] = isset($PropertyClassUID) ? $PropertyClassUID : '';
        $tax_data['AmountDelinquent'] = isset($AmountDelinquent) ? $AmountDelinquent : '';
        $tax_data['GoodThroughDate'] = isset($GoodThroughDate) ? $GoodThroughDate : '';
        $tax_data['TaxComments'] = isset($TaxComments) ? $TaxComments : '';

        $this->db->insert('tordertaxcerts', $tax_data);
        $TaxCertSNo = $this->db->insert_id();

        /*Tax Installment Insert Starts*/

        $TaxInstallmentList = $value['TaxInstallmentList']; 

        foreach ($TaxInstallmentList as $key => $value) {
          if($value[0]){
            $TaxInstallmentData = $value;
          } else {
            $TaxInstallmentData = [];
            $TaxInstallmentData[] = $value;
          }
        }

        foreach ($TaxInstallmentData as $key => $value) {

          $TaxYear = is_array($value['TaxYear']) ? ' ' : $value['TaxYear'];
          $BaseAmount = is_array($value['BaseAmount']) ? '0.00' : $value['BaseAmount'];
          $Status = is_array($value['Status']) ? '' : $value['Status'];
          $PaidAmount = is_array($value['PaidAmount']) ? '0.00' : $value['PaidAmount'];
          $PaidDate = is_array($value['PaidDate']) ? '' : $value['PaidDate'];
          $NextDue = is_array($value['NextDueDate']) ? '' : $value['NextDueDate'];

          $TaxStatus = $this->GetTaxStatus($Status);
          $TaxStatusUID = ($TaxStatus == '') ? 0 : $TaxStatus;
          $DatePaid = ($PaidDate == '') ? $PaidDate : Date('Y-m-d',strtotime($PaidDate));
          $NextDueDate = ($NextDue == '') ? $NextDue : Date('Y-m-d',strtotime($NextDue));

          $tax_install_data['OrderUID'] =  $OrderUID;
          $tax_install_data['TaxCertSNo'] =  $TaxCertSNo;
          $tax_install_data['TaxYear'] =  isset($TaxYear) ? $TaxYear : '';
          $tax_install_data['GrossAmount'] =  isset($BaseAmount) ? $BaseAmount : '';
          $tax_install_data['TaxStatusUID'] =  isset($TaxStatusUID) ? $TaxStatusUID : '';
          $tax_install_data['AmountPaid'] =  isset($PaidAmount) ? $PaidAmount : '';
          $tax_install_data['DatePaid'] =  isset($DatePaid) ? $DatePaid : '';
          $tax_install_data['NextDueDate'] =  isset($NextDueDate) ? $NextDueDate : '';

          $this->db->insert('tordertaxinstallment', $tax_install_data);
        }
        /*Tax Installment Insert Ends*/

        /*Tax Exemption Insert Starts*/

        foreach ($ExemptionList as $key => $value) {
          if($value[0]){
            $TaxExemptData = $value;
          } else {
            $TaxExemptData = [];
            $TaxExemptData[] = $value;
          }
        }

        foreach ($TaxExemptData as $key => $value) {

          $ExemptName = is_array($value['Name']) ? '' : $value['Name'];
          $TaxExemption= $this->GetTaxExemption($ExemptName);

          $TaxExemptionUID = ($TaxExemption == '') ? 0 : $TaxExemption;
          $TaxAmount = is_array($value['Amount']) ? '0.00' : $value['Amount'];
          $tax_exempt_data['OrderUID'] =  $OrderUID;
          $tax_exempt_data['TaxCertSNo'] =  $TaxCertSNo;
          $tax_exempt_data['TaxExemptionUID'] =  isset($TaxExemptionUID) ? $TaxExemptionUID : '';
          $tax_exempt_data['TaxAmount'] =  isset($TaxAmount) ? $TaxAmount : '';

          $this->db->insert('tordertaxexemptions', $tax_exempt_data);
        }

        /*Tax Exemption Insert Ends*/
        
      }

      /*Assessment Insert Starts*/

      $AssessedAddress1 = is_array($AssessedAddress['AssessedAddr1']) ? ' ' : $AssessedAddress['AssessedAddr1']; 
      $AssessedAddress2 = is_array($AssessedAddress['AssessedAddr2']) ? ' ' : $AssessedAddress['AssessedAddr2']; 
      $AssessedZipcode = is_array($AssessedAddress['AssessedZip']) ? ' ' : $AssessedAddress['AssessedZip']; 
      $AssessedStateCode = is_array($AssessedAddress['AssessedState']) ? ' ' : $AssessedAddress['AssessedState']; 
      $AssessedCityName = is_array($AssessedAddress['AssessedCity']) ? ' ' : $AssessedAddress['AssessedCity']; 
      $AssessedCountyName = is_array($AssessedAddress['AssessedCounty']) ? ' ' : $AssessedAddress['AssessedCounty']; 

      $AssessedAddress2 = ($AssessedAddress2 == '') ? ' ' : $AssessedAddress2;

      $address_data['OrderUID'] = $OrderUID;
      $address_data['AssessedAddress1'] = $AssessedAddress1;
      $address_data['AssessedAddress2'] = $AssessedAddress2;
      $address_data['AssessedZipcode'] = $AssessedZipcode;
      $address_data['AssessedStateCode'] = $AssessedStateCode;
      $address_data['AssessedCityName'] = $AssessedCityName;
      $address_data['AssessedCountyName'] = $AssessedCountyName;


      $CheckAddressValue = $this->CheckAddressTableValue($OrderUID);
      $CheckAddressValue =  $CheckAddressValue->CheckAddressValue;

      if($CheckAddressValue == 0){
        $this->db->insert('torderaddress', $address_data);
      } else{
        $this->db->where(array("OrderUID"=>$OrderUID)); 
        $this->db->update('torderaddress', $address_data);
      }

      /*Assessment Insert Ends*/  

      if ($this->db->trans_status() === FALSE){
        $this->db->trans_rollback();
        $res = array('success' => 0,'InBoundUID' => $InBoundUID);
      } else {

        $Notes = 'XML Delivered By API Abstractor. <br>Action: AT09';
        $this->AddNotes($Notes, $OrderUID, $CreatedByAPI,$InBoundUID);

        $ModuleName = $Notes.'-insert';
        $TableName = 'torderabstractor';
        $this->AuditTrailFunc($ModuleName, $TableName, $UserUID, $OrderUID);

        $this->db->trans_commit();
        $res = array('success' => 1,'InBoundUID' => $InBoundUID);
      }
    }  else {
      $res = array('success' => 0,'InBoundUID' => $InBoundUID);
    }
    echo json_encode($res); 
  }

  function toAbsComplete($fieldArray,$CreatedByAPI,$OrderUID){

    $this->load->model('Abstractor_Order_Search/Abstractor_Order_Search_model');

    $OrderNumber = $fieldArray['OrderNumber'];
    $TransactionID = $fieldArray['TransactionID'];
    $ProviderOrderNbr = $fieldArray['ProviderOrderNbr'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;
    $Action = 'Complete';
    $Details = $this->GetAbstractorUserUIDByOrderUID($OrderUID,$Action);

    if($Details){

      $AbstractorUID = $Details['AbstractorUID'];
      $UserUID = $Details['UserUID'];
      $AbstractorOrderUID = $Details['torderabstractor']->AbstractorOrderUID;
      $ApiOutBoundOrderUID = $Details['ApiOutBoundOrderUID'];

      $torderabstractor=$this->common_model->get_torderabstractor_by_AbstractorOrderUID($AbstractorOrderUID);

      $isfeeapproved=$this->common_model->IsAbstractorFeeNotApprovedForAbstractor($AbstractorOrderUID);

      if ($isfeeapproved >0) {
        $add="";
        $res = array('success' => 0,'InBoundUID' => $InBoundUID);
        echo json_encode($res); exit;
      }

      date_default_timezone_set('US/Eastern');
      $this->db->trans_begin();

      $this->db->where(array('OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>1));
      $this->db->update('torderassignment', array('WorkflowStatus'=>5,'CompleteDateTime'=>date('Y-m-d H:i:s')));
      $this->Abstractor_Order_Search_model->changeOrderStatus($OrderUID);

      $tordabs['DocumentReceived'] = '1';
      $tordabs['AbstractorReceivedDateTime']=date('Y-m-d H:i:s');
      $tordabs['CompletedDateTime']=date('Y-m-d H:i:s');
      $tordabs['IsOrderComplete']=1;
      $tordabs['OrderStatus']=5;

      $updates = $this->Abstractor_Order_Search_model->updatetorderabstractor($AbstractorOrderUID, $tordabs);

      $this->db->where(array('ApiOutBoundOrderUID' => $ApiOutBoundOrderUID ));
      $this->db->update('tApiOutBoundOrders', array('Status'=>'Completed'));

      if ($this->db->trans_status() === FALSE)
      {
        $this->db->trans_rollback();
        $res = array('success' => 0,'InBoundUID' => $InBoundUID);
      }
      else
      {
        $this->db->trans_commit();
        $Notes = 'Search Completed By API Abstractor. <br>Action: AT10 <br>Comments: '.$Comment;
        $this->AddNotes($Notes, $OrderUID, $CreatedByAPI, $InBoundUID);

        $ModuleName = $Notes.'-insert';
        $TableName = 'torderabstractor';
        $this->AuditTrailFunc($ModuleName, $TableName, $UserUID, $OrderUID);
        $res = array('success' => 1,'InBoundUID' => $InBoundUID);
      }
    } else {
      $res = array('success' => 0,'InBoundUID' => $InBoundUID);
    }
    
    echo json_encode($res);
  }

  /* XML Functions Starts */

    function get_DocumentTypeUID($DocumentTypeUID,$DocumentTypeName){

      $DocumentTypeName = is_array($DocumentTypeName) ? ' ' : $DocumentTypeName;

      $subDoc = '';
      $this->db->select('*');
      $this->db->from('mdocumenttypes');
      $this->db->where(array('DocumentCategoryUID' => $DocumentTypeUID,'DocumentTypeName' => $DocumentTypeName));
      $subDocumentTypeDetails = $this->db->get()->row();

      $subDocumentType = $subDocumentTypeDetails->DocumentTypeUID;
      $DocumentTypeName = $subDocumentTypeDetails->DocumentTypeName;

      if($subDocumentType){
        $DocumentTypeUID = $subDocumentType;
        $subDoc = array('DocumentTypeUID' => $DocumentTypeUID, 'DocumentTypeName' => $DocumentTypeName);
        return $subDoc;
      } else{
        $DocumentTypeName = 'Others';
        $subDoc = $this->get_DocumentTypeUID($DocumentTypeUID,$DocumentTypeName);
        return $subDoc;
      }
    }
    
    function GetLienType($LienTypeName){
      $this->db->select('*');
      $this->db->from('mlientypes');
      $this->db->where(array('LienTypeName' => $LienTypeName));
      $LienTypeUID = $this->db->get()->row()->LienTypeUID;
      return $LienTypeUID;
    }

    function GetTaxBasis($TaxBasisName){
      $this->db->select('*');
      $this->db->from('mtaxcertbasis');
      $this->db->where(array('TaxBasisName' => $TaxBasisName));
      $TaxBasisUID = $this->db->get()->row()->TaxBasisUID;
      return $TaxBasisUID;
    }

    function GetPropertyClass($PropertyClassName){
      $this->db->select('*');
      $this->db->from('mpropertyclass');
      $this->db->where(array('PropertyClassName' => $PropertyClassName));
      $PropertyClassUID = $this->db->get()->row()->PropertyClassUID;
      return $PropertyClassUID;
    }

    function GetTaxStatus($TaxStatusName){
      $this->db->select('*');
      $this->db->from('mtaxstatus');
      $this->db->where(array('TaxStatusName' => $TaxStatusName));
      $TaxStatusUID = $this->db->get()->row()->TaxStatusUID;
      return $TaxStatusUID;
    }

    function GetTaxExemption($TaxExemptionName){
      $this->db->select('*');
      $this->db->from('mtaxexemptions');
      $this->db->where(array('TaxExemptionName' => $TaxExemptionName));
      $TaxExemptionUID = $this->db->get()->row()->TaxExemptionUID;
      return $TaxExemptionUID;
    }

    function CheckAddressTableValue($OrderUID){
      $query = $this->db->query("SELECT EXISTS(SELECT * FROM torderaddress WHERE OrderUID = '$OrderUID') as CheckAddressValue;
        ");
      return $query->row();
    }

     function CheckLegalTableValue($OrderUID){
      $query = $this->db->query("SELECT EXISTS(SELECT * FROM torderlegaldescription WHERE OrderUID = '$OrderUID') as CheckLegalValue;
        ");
      return $query->row();
    }

    function CheckAssessmentTableValue($OrderUID){
      $query = $this->db->query("SELECT EXISTS(SELECT * FROM torderassessment WHERE OrderUID = '$OrderUID') as CheckAssessmentValue;
        ");
      return $query->row();
    }

  /* XML Functions Ends */

  /* PDF Functions Starts */

    public function GetAvailFileName($FileName, $ext, $itr, $OrderUID)
    {
      $DocumentFileName=$FileName.'_'.$itr.$ext;
      $query=$this->db->get_where('torderdocuments', array('OrderUID'=>$OrderUID,
       'DocumentFileName'=>$DocumentFileName));
      $numrows=$query->num_rows();
      if($numrows==0)
      { 
        return $DocumentFileName;
      }
      $itr+=1;
      return $this->GetAvailFileName($FileName, $ext, $itr);
    }

  /* PDF Functions Starts */

  /* On-Hold Functions Starts */

    function submitholdorder($OrderUID,$remarkstext,$Type,$section,$MenuUrl,$UserUID){

      $this->load->model('Onhold_orders/Onhold_orders_model');

      if($remarkstext != "" && $section  != "" && $MenuUrl != "")
      {
        $Workflow = $this->common_model->get_workflow_onholdorder($MenuUrl);
        if(count($Workflow)>0)
        {
          $WorkflowUID = $Workflow->WorkflowModuleUID;
          $assignuser = $this->common_model->check_is_assigned($OrderUID,$WorkflowUID);  
          if(empty($assignuser))
          {
            $this->common_model->SelfAssignOrderToUser($OrderUID,$WorkflowUID,$UserUID); 
          }  
        }

        $Workflow = $this->Onhold_orders_model->get_workflow_onholdorder($MenuUrl);
        if(count($Workflow) >0){

          $res = $this->changeto_onhold($section,$Type,$remarkstext,$Workflow,$OrderUID,$UserUID);

          if($res != ''){

                    $WorkflowName=$this->Onhold_orders_model->GetWorkFlowName($Workflow->WorkflowModuleUID);
                    $data1['ModuleName']=$WorkflowName->WorkflowModuleName.'WorkFlow Status-changed';
                    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
                    $data1['DateTime']=date('y-m-d H:i:s');
                    $data1['TableName']='torderassignment';
                    $data1['OrderUID']=$OrderUID;
                    $data1['UserUID']=$UserUID; 
                    $data1['OldValue']=''; 
                    $data1['FieldUID']='732';
                    $data1['NewValue']='Onhold';                 
                    $this->common_model->Audittrail_insert($data1);  

            return 1;  

          }else{
            return 0;    
          }
        }else{
          return 0;    
        }
      }else{
        return 0;    
      }
    }

    function changeto_onhold($section,$Type,$remarkstext,$Workflow,$OrderUID,$loggedid) {

      $data = array(
        'WorkflowStatus' => '4',
      );

      $onholdinsertdata = array(
        'OrderUID'=>$OrderUID,
        'Remarks' => $remarkstext,
        'SectionUID' => $section,
        'OnHoldTypeUID' => $Type,
        'OnholdDateTime' =>  Date('Y-m-d H:i:s',strtotime("now")),
        'WorkflowModuleUID' => $Workflow->WorkflowModuleUID,
        'AssignedToUserUID' => $loggedid,
      );

      $this->db->trans_begin();
      $this->db->insert('torderonholdhistory',$onholdinsertdata);
      $this->db->set($data)->where(array('OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$Workflow->WorkflowModuleUID))->update('torderassignment');

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return false;
      }
      else {
        $this->db->trans_commit();
        return true;
      }
    }

    function getmonholddetails()
    {
      $this->db->select("*");
      $this->db->from('monholddetails');
      $query = $this->db->get();
      return $query->row()->OnHoldTypeUID;
    }

  /* On-Hold Functions Ends */

  /* Resume Functions Starts */

    function changestatusholdorder($OrderUID,$MenuUrl,$UserUID)
    {
      $this->load->model('Onhold_orders/Onhold_orders_model');

      $Workflow = $this->Onhold_orders_model->get_workflow_onholdorder($MenuUrl);

      if($OrderUID != '' && $MenuUrl != ''){

        if(count($Workflow) >0){

          $res = $this->unholdorder($OrderUID,$Workflow->WorkflowModuleUID);

          if($res != ''){

            $WorkflowName=$this->Onhold_orders_model->GetWorkFlowName($Workflow->WorkflowModuleUID);
            $data1['ModuleName']=$WorkflowName->WorkflowModuleName.'WorkFlow Onhold Status-changed';
            $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
            $data1['DateTime']=date('y-m-d H:i:s');
            $data1['TableName']='torderassignment';
            $data1['OrderUID']=$OrderUID;
            $data1['UserUID']=$UserUID; 
            $data1['OldValue']=''; 
            $data1['FieldUID']='732';
            $data1['NewValue']='Unhold';                 
            $this->common_model->Audittrail_insert($data1);
            return 1; 
          }else{
            return 0;    
          }
        }

      } else {
        return 0;
      }
    }

    function unholdorder($OrderUID,$WorkflowModuleUID)
    {
      $data = array(
        'WorkflowStatus' => '3',
      );

      $onholdupdatedata = array(
        'ReleaseDateTime' =>  Date('Y-m-d H:i:s',strtotime("now")),
      );

      $this->db->trans_begin();
      $this->db->set($onholdupdatedata)->where(array('OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID,'ReleaseDateTime'=>null))->update('torderonholdhistory');
      $this->db->set($data)->where(array('OrderUID'=>$OrderUID,'WorkflowModuleUID'=>$WorkflowModuleUID))->update('torderassignment');

      if ($this->db->trans_status() === FALSE){
        $this->db->trans_rollback();
        return false;
      } else {
        $this->db->trans_commit();
        return true;
      }
    }

  /* Resume Functions Ends */

  /* Search Cmplete Functions Starts */

    function CheckStatus($OrderUID,$currenturl,$UserUID){

      $this->load->model('Order_complete/Order_complete_model');

      $workflow_popname  = 'Search';
      $TaxPricingStatus = '';

      $is_vendor_login = $this->common_model->is_vendorlogin();

      $order_details = $this->common_model->get_orderdetails($OrderUID);

      if($OrderUID && $UserUID && $currenturl != '')
      {
        //check Order Assigned to vendor 
        $Workflowbyname  = $this->common_model->get_workflow_nameby_url($currenturl);

        if(count($Workflowbyname) == 0){
          return array("status" => 0,'message'=>'Workflow Error');exit;
        }

        /* Get Workflow permissions */
        $workflow_permissions = $this->Order_complete_model->check_workflow_permissions($Workflowbyname->WorkflowModuleUID);

        if(count($Workflowbyname) > 0 && !$is_vendor_login){

          if ($Workflowbyname->WorkflowModuleUID == 1) {
            $assigned_user_nums= $this->common_model->IsAbstractorAssigned($OrderUID);
            if ($assigned_user_nums > 0) {
              $isfeeapproved=$this->common_model->IsAbstractorFeeNotApproved($OrderUID);

              if ($isfeeapproved>0) {
                return array("status" => 3,"data" => '','message'=>'<div class="panel-heading panel-heading-divider">Abstractor Fee Approval is Pending. Please Check.</div>');exit;
              }
            }
          }
        }

        //Check Review Onhold
        $checkreviewholdstatus = $this->common_model->is_reviewholdorder($OrderUID,4);
        $AssignOrderUID = $this->input->post('OrderUID');
        $MenuUrl = $currenturl;
        $Workflow = $this->common_model->get_workflow_onholdorder($MenuUrl); 

        if(count($Workflow)>0)
        {
          $WorkflowUID = $Workflow->WorkflowModuleUID;
          $assignuser = $this->common_model->check_is_assigned($AssignOrderUID,$WorkflowUID);  
          if(empty($assignuser))
          {
            $this->common_model->SelfAssignOrderToUser($AssignOrderUID,$WorkflowUID,$UserUID); 
          } else {
            if(empty($assignuser->AssignedToUserUID))
            {
              $this->common_model->UpdateSelfAssignOrderToUser($AssignOrderUID,$WorkflowUID,$UserUID);  
            }
          }   
        }

        $to_complete_workflowuids = [];
        $to_complete_workflowuids[] = $Workflowbyname->WorkflowModuleUID;
        $is_completed = $this->Order_complete_model->complete_workflow($OrderUID,$to_complete_workflowuids,$UserUID,$workflow_permissions,$checkreviewholdstatus,$TaxPricingStatus);      

        $result = ($is_completed['Error'] == 0) ? array('status'=>2,'message'=>$is_completed['message']) : array('status'=>0,'message'=>'Please try Again');

        return $result; exit;
      }
    }

  /* Search Cmplete Functions Ends */

  function GetAbstractorUserUIDByOrderUID($OrderUID,$Action){

    $this->db->select("*");
    $this->db->from('mApiTitlePlatform');
    $this->db->where(array("mApiTitlePlatform.OrderSourceName"=>'Pabs'));
    $query = $this->db->get();
    $SourceName = $query->row();
    $OrderSourceUID = $SourceName->OrderSourceUID;

    $this->db->select_max('ApiOutBoundOrderUID');
    $this->db->from('tApiOutBoundOrders');
    $this->db->where(array("tApiOutBoundOrders.OrderUID"=>$OrderUID,"tApiOutBoundOrders.OrderSourceUID"=>$OrderSourceUID));
    $query = $this->db->get();
    $ApiOutBoundOrderUID = $query->row()->ApiOutBoundOrderUID;

    if($Action === 'Accept'){
      $Status = array('New');
    } elseif($Action === 'PDF-Delivery'){
      $Status = array('New', 'Accepted','Completed');
    }else {
      $Status = array('New', 'Accepted');
    }

    $this->db->select("*");
    $this->db->from('tApiOutBoundOrders');
    $this->db->where(array("tApiOutBoundOrders.ApiOutBoundOrderUID"=>$ApiOutBoundOrderUID));       
    $this->db->where_in('Status',$Status);       
    $query = $this->db->get();
    $tApiOutBoundOrders = $query->row();

    if($tApiOutBoundOrders){

      $AbstractorUID = $tApiOutBoundOrders->AbstractorUID;
      $ApiOutBoundOrderUID = $tApiOutBoundOrders->ApiOutBoundOrderUID;

      $this->db->select_max('AbstractorOrderUID');
      $this->db->from('torderabstractor');
      $this->db->where(array("torderabstractor.OrderUID"=>$OrderUID,"torderabstractor.AbstractorUID"=>$AbstractorUID));       
      $query = $this->db->get();
      $AbstractorOrderUID = $query->row()->AbstractorOrderUID;

      $this->db->select("*");
      $this->db->from('torderabstractor');
      $this->db->where(array("torderabstractor.OrderUID"=>$OrderUID,"torderabstractor.AbstractorUID"=>$AbstractorUID,"torderabstractor.AbstractorOrderUID"=>$AbstractorOrderUID));       
      $query = $this->db->get();
      $torderabstractor = $query->row();

      $mabstractor = $this->db->get_where('mabstractor', array('AbstractorUID'=> $AbstractorUID))->row();
      $UserUID = $mabstractor->UserUID;

      $result = array('AbstractorUID' => $AbstractorUID , 'UserUID' => $UserUID , 'ApiOutBoundOrderUID' => $ApiOutBoundOrderUID, 'torderabstractor' => $torderabstractor );

      return $result;

    } else {
      return false;
    }

  }

  function AddNotes($Notes, $OrderUID, $CreatedByAPI, $InBoundUID=null){

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Notes,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'InBoundUID' => $InBoundUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);
  }

  function AuditTrailFunc($ModuleName, $TableName, $UserUID, $OrderUID){

    $data1['ModuleName'] = $ModuleName;
    $data1['IpAddreess'] = $_SERVER['REMOTE_ADDR']; 
    $data1['DateTime'] = date('y-m-d H:i:s');
    $data1['TableName'] = $TableName;
    $data1['OrderUID'] = $OrderUID;
    $data1['UserUID'] = $UserUID;                        
    $res = $this->common_model->Audittrail_insert($data1);

    return $res;
  }

}?>
