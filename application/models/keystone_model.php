<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Keystone_model extends CI_Model {
  
  function __construct()
  { 
    parent::__construct();
    $this->load->config('keywords');
    $this->load->model('common_model');
    $this->load->model('api/api_model');
    $this->load->model('Real_ec_model');
    $this->load->model('order_cancel/order_cancel_model');
    $this->load->library('session');
  }

  function GetNoteTypeUID($SectionName)
  {
    $this->db->select("*");
    $this->db->from('mreportsections');
    $this->db->where(array("mreportsections.SectionName"=>$SectionName));
    $query = $this->db->get();
    return $query->row();
  }

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

  function Keystone_AddNote($fieldArray,$CreatedByAPI,$EventCode) {
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $OrderUID = $fieldArray['OrderUID'];
    $InBoundUID = $fieldArray['InBoundUID'];

    if($EventCode == 'AddNote'){
      $NoteType = $this->GetNoteTypeUID('API Note');
    } else if($EventCode == 'AddNoteAck'){
      $NoteType = $this->GetNoteTypeUID('API Note Action');
    }

    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Comment,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    if ($this->db->trans_status() === FALSE)
    {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>'', 'message'=>'Error'));
    }
    else
    {
      $this->db->trans_commit();
      $this->db->insert("tordernotes", $insert_notes);
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'Add Note Inserted in an Organization'));
    } 

  }

  function AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, $EventCode){

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Notes,
      'NoteData' => $NoteData,
      'EventCode' => $EventCode,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);
  }

  function InsertDocuments($OrderUID,$Documents,$EventCode,$CreatedByAPI){
    $torders = $this->common_model->get_orderdetails($OrderUID);
    $OrderNumber = $torders->OrderNumber;

    $this->db->select('*')->from('musers');
    $this->db->where('LoginID', 'isgn');
    $query = $this->db->get();
    $UserName = $query->row()->UserName;
    $UserUID = $query->row()->UserUID;
    
    $document_count = 0;
    $searchdocumentcount = 0;
    foreach ($Documents as $key => $value) {
      $Document = $value['FileContent'];  
      $FileName = $value['FiletName'];  
      $FinalPDF = base64_decode($Document);   
      
      if($torders->OrderDocsPath!= NULL){
        $OrderDocs_Path = $torders->OrderDocsPath;
      }else{
        $date = date('Ymd');
        $OrderDocsPath = 'uploads/searchdocs/'.$date.'/'.$OrderNumber.'/';
        $query = $this->db->query("update torders SET OrderDocsPath =".$OrderDocsPath." Where OrderUID='".$OrderUID."' ");
        $OrderDocs_Path = $OrderDocsPath;
      }

      $this->db->like('DocumentFileName', $FileName);
      $this->db->where(array('OrderUID' => $OrderUID));
      $torderdocuments = $this->db->get('torderdocuments');
      $searchdocumentcount = $torderdocuments->num_rows();
      $searchdocumentcount += 1;

      $ApiDocumentFileName = $this->GetAvailFileName($FileName, '.pdf', $searchdocumentcount, $OrderUID);
      $file = FCPATH . $OrderDocs_Path . $ApiDocumentFileName;            

      if (!is_dir($OrderDocs_Path)) {
        mkdir($OrderDocs_Path, 0777, true);
      } 

      file_put_contents($file, $FinalPDF);

      /**
      * @description D2TINT-44 - DocType selected based on the keystone doctype
      * @author Yagavi G <yagavi.g@avanzegroup.com>
      * @since 2nd April 2020
      *
      */

      $FileDocType = $value['FileDocType'];  
      $FileDocTypeDesc = $value['FileDocTypeDesc'];  
      $KeystoneDocTypes = $this->config->item('KeystoneDocTypes');
      $TypeOfDocument = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $CreatedByAPI);
      $DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration($FileDocType, $CreatedByAPI);

      if(!empty($DocumentTypeUID)){
        $TypeOfDocument = $DocumentTypeUID;
      }

      $Insert = array(
        'OrderUID'=>$OrderUID,
        'DocumentTypeUID'=> $TypeOfDocument,
        'DocumentFileName'=>$ApiDocumentFileName,
        'DisplayFileName'=>$ApiDocumentFileName,
        'UploadedUserUID'=>$UserUID,
        'UploadedDate'=>date('Y-m-d H:i:s'),
        'TypeOfDocument'=>$TypeOfDocument,
        'DocumentCreatedDate'=>date('Y-m-d H:i:s'),
        'IsDocumentReceived'=> 1,
        'DocumentReceivedDateTime'=> date('Y-m-d H:i:s')
      );
      
      $res = $this->db->insert('torderdocuments',$Insert);
      if($res){
        $document_count++;
        $NoteData = '';
        $Notes='';
        $Notes.= $ApiDocumentFileName . ' is received from API <br> Action: '.$EventCode;

        if(!empty($FileDocType)){
          $Notes.='<br>Document Type: '.$FileDocType;
        }

        if(!empty($FileDocTypeDesc)){
          $Notes.='<br>Document Type Desc: '.$FileDocTypeDesc;
        }

        $this->AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, $EventCode);
        
        /*$this->load->model('title_closing/Closing_model');
        $closing_keystone_data['OrderUID'] = $OrderUID;
        if($EventCode == 'RequestForAttorneyPackage'){
          $closing_keystone_data['EventCode'] = 'ClosingPackageSentToNotary';
          $closing_keystone_data['EventComments'] = 'Closing Package Received';
          $this->Closing_model->SendClosingEventstoAPI($closing_keystone_data);
        }*/

        /*if($EventCode == 'DeliverClosingPackage'){
          $closing_keystone_data['EventCode'] = 'ReceiveClosingPackage';
          $closing_keystone_data['EventComments'] = 'Closing Package Received';
          $this->Closing_model->SendClosingEventstoAPI($closing_keystone_data);
        }*/
      }
    }

    return $document_count;
  }

 /**
  * @purpose : D2TINT-191 - To store the comment and attachment info in the same note
  *            copied the InsertDocuments function and changed
  * @author  : D.Samuel Prabhu
  * @Since   : 16 June 2020
  *
  */
  function InsertAttachmentDocuments($OrderUID,$Documents,$EventCode,$CreatedByAPI){
    $torders = $this->common_model->get_orderdetails($OrderUID);
    $OrderNumber = $torders->OrderNumber;

    $this->db->select('*')->from('musers');
    $this->db->where('LoginID', 'isgn');
    $query = $this->db->get();
    $UserName = $query->row()->UserName;
    $UserUID = $query->row()->UserUID;
    $Notes='';
    
    $document_count = 0;
    $searchdocumentcount = 0;
    foreach ($Documents as $key => $value) {
      $Document = $value['FileContent'];  
      $FileName = $value['FiletName'];  
      $FinalPDF = base64_decode($Document);   
      
      if($torders->OrderDocsPath!= NULL){
        $OrderDocs_Path = $torders->OrderDocsPath;
      }else{
        $date = date('Ymd');
        $OrderDocsPath = 'uploads/searchdocs/'.$date.'/'.$OrderNumber.'/';
        $query = $this->db->query("update torders SET OrderDocsPath =".$OrderDocsPath." Where OrderUID='".$OrderUID."' ");
        $OrderDocs_Path = $OrderDocsPath;
      }

      $this->db->like('DocumentFileName', $FileName);
      $this->db->where(array('OrderUID' => $OrderUID));
      $torderdocuments = $this->db->get('torderdocuments');
      $searchdocumentcount = $torderdocuments->num_rows();
      $searchdocumentcount += 1;

      $ApiDocumentFileName = $this->GetAvailFileName($FileName, '.pdf', $searchdocumentcount, $OrderUID);
      $file = FCPATH . $OrderDocs_Path . $ApiDocumentFileName;            

      if (!is_dir($OrderDocs_Path)) {
        mkdir($OrderDocs_Path, 0777, true);
      } 

      file_put_contents($file, $FinalPDF);

      /**
      * @description D2TINT-44 - DocType selected based on the keystone doctype
      * @author Yagavi G <yagavi.g@avanzegroup.com>
      * @since 2nd April 2020
      *
      */

      $FileDocType = $value['FileDocType'];  
      $FileDocTypeDesc = $value['FileDocTypeDesc'];  
      $KeystoneDocTypes = $this->config->item('KeystoneDocTypes');
      $TypeOfDocument = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $CreatedByAPI);
      $DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration($FileDocType, $CreatedByAPI);

      if(!empty($DocumentTypeUID)){
        $TypeOfDocument = $DocumentTypeUID;
      }

      $Insert = array(
        'OrderUID'=>$OrderUID,
        'DocumentTypeUID'=> $TypeOfDocument,
        'DocumentFileName'=>$ApiDocumentFileName,
        'DisplayFileName'=>$ApiDocumentFileName,
        'UploadedUserUID'=>$UserUID,
        'UploadedDate'=>date('Y-m-d H:i:s'),
        'TypeOfDocument'=>$TypeOfDocument,
        'DocumentCreatedDate'=>date('Y-m-d H:i:s'),
        'IsDocumentReceived'=> 1,
        'DocumentReceivedDateTime'=> date('Y-m-d H:i:s')
      );
      
      $res = $this->db->insert('torderdocuments',$Insert);
      if($res){
        $document_count++;
        $NoteData = '';
        
        $Notes.= $ApiDocumentFileName . ' is received from API <br> Action: '.$EventCode;

        if(!empty($FileDocType)){
          $Notes.='<br>Document Type: '.$FileDocType;
        }

        if(!empty($FileDocTypeDesc)){
          $Notes.='<br>Document Type Desc: '.$FileDocTypeDesc;
        }
        
        $Notes.='<br>';
       // $this->AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, $EventCode);
        /*        
        $this->load->model('title_closing/Closing_model');
        $closing_keystone_data['OrderUID'] = $OrderUID;
        if($EventCode == 'RequestForAttorneyPackage'){
          $closing_keystone_data['EventCode'] = 'ClosingPackageSentToNotary';
          $closing_keystone_data['EventComments'] = 'Closing Package Received';
          $this->Closing_model->SendClosingEventstoAPI($closing_keystone_data);
        }

        if($EventCode == 'DeliverClosingPackage'){
          $closing_keystone_data['EventCode'] = 'ReceiveClosingPackage';
          $closing_keystone_data['EventComments'] = 'Closing Package Received';
          $this->Closing_model->SendClosingEventstoAPI($closing_keystone_data);
        }*/
      }
    }

    return ['documentCount' => $document_count, 'Notes' => $Notes];
  }

  function Keystone_AddAttachment($fieldArray,$CreatedByAPI,$EventCode) {

    $document_count = 0;
    $OrderUID = $fieldArray['OrderUID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $Documents = $fieldArray['Documents'];    
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $fieldArray['ServiceReasonRemarks'];
    $LoanIdentifier = $fieldArray['LoanIdentifier'];
    $ClosingInfo = $fieldArray['ClosingInfo'];
    $NoteData = json_encode($TransactionID);

    //$document_count = $this->InsertDocuments($OrderUID,$Documents,$EventCode,$CreatedByAPI);

    /**
    * @purpose : D2TINT-191 - To store the comment and attachment info in the same note
    * @author  : D.Samuel Prabhu
    * @Since   : 16 June 2020
    *
    */
    $ins_doc = $this->InsertAttachmentDocuments($OrderUID,$Documents,$EventCode,$CreatedByAPI);
   
    $document_count = $ins_doc['documentCount'];     
    $Remarks        = $ins_doc['Notes'].'<br>';  

    /** End of D2TINT-191 changes **/

    if($document_count > 0){
      $Remarks .= 'Action:'.$EventCode.'<br> Comment: '.$fieldArray['Comment'].'<br> PDF Delivered By Keystone.<br>';

      if($ServiceReasonRemarks['ReasonCode']){
        $Remarks.= 'Code: '.$ServiceReasonRemarks['ReasonCode'].'<br> Reason: '.$ServiceReasonRemarks['ReasonCodeDescription'].'<br>';
      }

      if($LoanIdentifier){
        $Remarks.= 'Loan Information : <br>';
        $Remarks.= 'LoanIdentifier: '.$LoanIdentifier['LoanIdentifier'].'<br> Type: '.$LoanIdentifier['LoanIdentifier'].'<br> Desc: '.$LoanIdentifier['LoanIdentifierTypeOtherDescription'].'<br>';
      }

      if($ClosingInfo){
        $Remarks.= 'Closing Information : <br>';
        $Remarks.= 'Expiration Date: '.$ClosingInfo['ClosingDocumentsExpirationDate'].'<br> Expiration Time: '.$ClosingInfo['ClosingDocumentsExpirationTime'];
      }

      $this->AddNotes($Remarks, $NoteData, $OrderUID, $CreatedByAPI, $EventCode);
      $res = array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>$EventCode.' Event Inserted into Organization');
    }else{
      $res = array('status' => 'failed ','InBoundUID' =>$InBoundUID, 'message'=>$EventCode.' Event Inserted into Organization');
    }

    echo json_encode($res);
  }

  function Keystone_CancelOrder($DataArray,$CreatedByAPI) { 
    $TransactionID = $DataArray['TransactionID'];
    $StatusUID = $DataArray['StatusUID'];
    $OrderUID = $DataArray['OrderUID'];
    $InBoundUID = $DataArray['InBoundUID']; 
    $Comment = $DataArray['Comment'];
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $DataArray['ServiceReasonRemarks'];

    $canceldata['OrderUID']=$OrderUID;
    $canceldata['Remark']='Order Cancelled by API';
    $canceldata['ApprovalFunction']='Order Cancellation';
    $canceldata['ApprovalStatus']=0;
    $canceldata['IsReviewed']=1;
    $canceldata['RaisedDatetime']=date('Y-m-d H:i:s');
    $canceldata['RaisedByAPI']=$CreatedByAPI;
    $approval = $this->db->insert('torderapprovals',$canceldata);

    $source = $this->db->query("SELECT OrderSourceUID FROM tApiOrders WHERE TransactionID = '".$TransactionID."'")->row();
    $tcan['OrderUID'] = $OrderUID;
    $tcan['Remarks']='Order Cancelled by API';
    $tcan['IsApiOrder']=1;
    $tcan['RequestedBy']= $source->OrderSourceUID;
    $tcan['CancellationRequestTime']= date('Y-m-d H:i:s');
    $this->db->insert('tordercancel',$tcan);

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;
    /* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
    if($approval) {
      $ProductList = $fieldArray['ProductList'];
      $NoteData = json_encode($ProductList);
      $Notes = 'Order Cancellation Request from Keystone (Moved to Client Request) <br>'.'Action: CancelOrder <br>'.'Reason:'.$ServiceReasonRemarks['ReasonCodeDescription'].'<br> Remarks:'.$ServiceReasonRemarks['ReasonCommentText'].'<br> Comment: '.$Comment;
      $this->Real_ec_model->AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, 'CancelOrder');

      /* @desc D2T-1011 Audit Trail Added @author Yagavi G <yagavi.g@avanzegroup.com> @Since Sept 3rd 2020 */
      $audit_msg = 'Status: Waiting for Approval <br> Remarks:'.$Notes;
      $AuditData = array(
        'UserUID' => $this->common_model->GetIsgnUser(),
        'ModuleName' => 'Order Cancel Request',
        'OrderUID' => $OrderUID,
        'Feature' => $OrderUID,
        'Content' => $audit_msg,
        'DateTime' => date('Y-m-d H:i:s')
      );
      $this->common_model->Audittrail_insert($AuditData);

      echo json_encode(array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'CancelOrder Event Inserted in Isgn Organization'));
    } else {
      echo json_encode(array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }
  } 

  function Keystone_UpdateOrder($fieldArray,$CreatedByAPI,$EventCode){

    $this->db->trans_begin();
    $json_encode = json_encode($fieldArray);
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];

    if(!empty($Comment)){
      $Comment = 'Comment: '.$Comment;
    } else {
      $Comment = 'Comment: No Comments from Client';
    }

    $Remarks = $EventCode.' from Keystone (Moved to Client Request)<br>'.$Comment;
    $data_array['OrderUID']=$OrderUID;
    $data_array['Remark']= $EventCode.' from Keystone';
    $data_array['ApprovalFunction']= $EventCode;
    $data_array['ApprovalStatus']=0;
    $data_array['IsReviewed']=1;
    $data_array['RaisedDatetime']=date('Y-m-d H:i:s');
    $data_array['RaisedByAPI']=$CreatedByAPI;
    $data_array['APIContentData']=$json_encode;
    $approval = $this->db->insert('torderapprovals',$data_array);    

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'EventCode' => $EventCode, /* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
    }    
  }

  function Keystone_SuspendOrder($fieldArray,$CreatedByAPI){

    $this->db->trans_begin();
    $json_encode = json_encode($fieldArray);

    $TransactionID = $fieldArray['TransactionID'];
    $StatusUID = $fieldArray['StatusUID'];
    $OrderUID = $fieldArray['OrderUID'];
    $InBoundUID = $fieldArray['InBoundUID']; 
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $fieldArray['ServiceReasonRemarks'];    

    $NoteType = $this->api_model->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;
    $StartTime = date('Y-m-d H:i:s',strtotime('now'));

    $this->api_model->insert_start_delay_time($OrderUID,$StartTime,$Comment,$SectionUID,$CreatedByAPI);

    $data1['ModuleName']='Customer Delay Start by API-update';
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['DateTime']=date('y-m-d H:i:s');
    $data1['TableName']='mcustomerdelay';
    $data1['OrderUID']=$OrderUID;
    $data1['UserUID']=$this->api_model->GetIsgnUser();                        
    $this->common_model->Audittrail_insert($data1);

    $ProductList = $fieldArray['ProductList'];
    $NoteData = json_encode($ProductList);
    $Notes = 'Order On-Hold from Keystone - Starts Customer Delay <br>'.'Action: SuspendOrder <br>'.'Reason:'.$ServiceReasonRemarks['ReasonCodeDescription'].'<br> Remarks:'.$ServiceReasonRemarks['ReasonCommentText'].'<br> Comments: '.$Comment;
    $this->Real_ec_model->AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, 'SuspendOrder');

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode(array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'SuspendOrder Event Inserted in Isgn Organization'));
    }    
  }

  function Keystone_ResumeOrder($fieldArray,$CreatedByAPI){

    $this->db->trans_begin();
    $this->load->model('order_summary/Order_Summary_Model');
    $json_encode = json_encode($fieldArray);

    $TransactionID = $fieldArray['TransactionID'];
    $StatusUID = $fieldArray['StatusUID'];
    $OrderUID = $fieldArray['OrderUID'];
    $InBoundUID = $fieldArray['InBoundUID']; 
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }

    $NoteType = $this->api_model->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;
    $StopTime = date('Y-m-d H:i:s');

    $Order = $this->common_model->get_orderdetails($OrderUID);
    $holiday = $this->common_model->GetHolidays(); 
    foreach($holiday as $holiDate) {
      $holidays[] = $holiDate->HolidayDate;
    }
    $OrderDueDatetime = $this->api_model->OrderCustomerDelay_Add_with_DueDate($OrderUID,$Order->OrderDueDatetime,$holidays); 
    $this->Order_Summary_Model->UpdateOrderDueDateCustomerDelay($OrderUID,$OrderDueDatetime);

    $result = $this->api_model->update_stop_delay_time($OrderUID,$StopTime,$Comment,$SectionUID,$CreatedByAPI);

    if($result) {
      $ProductList = $fieldArray['ProductList'];
      $NoteData = json_encode($ProductList);
      $Notes = 'Order Resumed from Keystone - Stopped Customer Delay <br>'.'Action: ResumeOrder'.'<br> Comment: '.$Comment;
      $this->Real_ec_model->AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, 'ResumeOrder');

      $data1['ModuleName']='Customer Delay Stop by API-update';
      $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
      $data1['DateTime']=date('y-m-d H:i:s');
      $data1['TableName']='mcustomerdelay';
      $data1['OrderUID']=$OrderUID;
      $data1['UserUID']=$this->api_model->GetIsgnUser();                        
      $this->common_model->Audittrail_insert($data1);
    }

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode(array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'ResumeOrder Event Inserted in Isgn Organization'));
    }    
  }

  function Keystone_Escalation($fieldArray,$CreatedByAPI){

    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }

    $Remarks = 'Escalation Request from Keystone (Moved to Client Request) <br>Comment: '.$Comment;
    $data_array['OrderUID']=$OrderUID;
    $data_array['Remark']= 'Escalation Request from Keystone';
    $data_array['ApprovalFunction']= 'EscalationRequest';
    $data_array['ApprovalStatus']=0;
    $data_array['IsReviewed']=1;
    $data_array['RaisedDatetime']=date('Y-m-d H:i:s');
    $data_array['RaisedByAPI']=$CreatedByAPI;
    $approval = $this->db->insert('torderapprovals',$data_array);    

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'EventCode' => 'Escalation', /* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
    }    
  }

  function Keystone_ReopenCancelOrder($fieldArray,$CreatedByAPI) { 

    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }

    $torders = $this->common_model->get_row('torders', ['OrderUID'=>$OrderUID]);
    $StatusUID = $torders->StatusUID;
    $Cancelled = $this->config->item('keywords')['Cancelled'];

    if($StatusUID == $Cancelled){
      $Remarks = 'Order Reopen-Request from Keystone (Moved to Client Request) <br>Comment: '.$Comment;
      $data_array['OrderUID']=$OrderUID;
      $data_array['Remark']= 'Reopen Request from Keystone';
      $data_array['ApprovalFunction']= 'ReopenRequest';
      $data_array['ApprovalStatus']=0;
      $data_array['IsReviewed']=1;
      $data_array['RaisedDatetime']=date('Y-m-d H:i:s');
      $data_array['RaisedByAPI']=$CreatedByAPI;
      $approval = $this->db->insert('torderapprovals',$data_array);    

      $NoteType = $this->GetNoteTypeUID('API Note');
      $SectionUID = $NoteType->SectionUID;

      $insert_notes = array(
        'Note' => $Remarks,
        'SectionUID' => $SectionUID,
        'OrderUID' => $OrderUID,
        'EventCode' => 'OrderReopenRequest', /* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
        'RoleType' => '1,2,3,4,5,6,7,9,11,12',
        'CreatedByAPI' => $CreatedByAPI,
        'CreatedOn' => date('Y-m-d H:i:s')
      );

      $result = $this->db->insert("tordernotes", $insert_notes);

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
      }else{
        $this->db->trans_commit();
        echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
      }

    } else {
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'Message' => 'Unable to execute!! The Status of the Order is not in Cancel State.'));
    }
  } 

  function Keystone_ReopenCancelOrderApproved($fieldArray,$CreatedByAPI) { 

    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $torders = $this->common_model->get_row('torders', ['OrderUID'=>$OrderUID]);
    $StatusUID = $torders->StatusUID;
    $Cancelled = $this->config->item('keywords')['Cancelled'];

    if($StatusUID == $Cancelled){
      $CheckRevoke = $this->CheckOrderCancelRevoke($OrderUID);
      if($CheckRevoke){
        $this->db->trans_begin();
        $result = $this->order_cancel_model->RevokeOrderStatus($OrderUID);
        if($result) {
          $this->order_cancel_model->UpdateRevokedOrder($OrderUID,$Comment,$CreatedByAPI);
          $accept = $this->order_cancel_model->DeleteRevokedOrder($OrderUID);
          $NoteType = $this->GetNoteTypeUID('API Note');
          $SectionUID = $NoteType->SectionUID;
          $ProductList = $fieldArray['ProductList'];
          $NoteData = json_encode($ProductList);
          $Notes = 'Order Reopen-Request Approval from Keystone <br>'.'Action: OrderReopenApproved <br> Comment: '.$Comment;
          $this->Real_ec_model->AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, 'OrderReopenApproved');
        }

        if ($this->db->trans_status() === FALSE) {
          $this->db->trans_rollback();
          echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
        }else{
          $this->db->trans_commit();
          echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
        } 
      } else {
        echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'Message' => 'Unable to execute!! Order was not in revoke queue'));
      }
    } else {
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'Message' => 'Unable to execute!! The Status of the Order is not in Cancel State.'));
    }
  } 

  function Keystone_RevisionRequest($fieldArray,$CreatedByAPI) { 

    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $fieldArray['ServiceReasonRemarks'];
    $Documents = $fieldArray['Documents'];
    unset($fieldArray['Documents']);
    $json_encode = json_encode($fieldArray);
    $torders = $this->common_model->get_row('torders', ['OrderUID'=>$OrderUID]);
    $StatusUID = $torders->StatusUID;
    $Cancelled = $this->config->item('keywords')['Cancelled'];

    $Remarks = 'Revision Request from Keystone (Moved to Client Request) <br>'.'Action: RequestRevision <br>'.'Code: '.$ServiceReasonRemarks['ReasonCode'].'<br> Reason: '.$ServiceReasonRemarks['ReasonCodeDescription'].'<br> Remarks: '.$ServiceReasonRemarks['ReasonCommentText'].'<br> Comment: '.$fieldArray['Comment'];

    $data_array['OrderUID']=$OrderUID;
    $data_array['Remark']= 'Revision Request from Keystone';
    $data_array['ApprovalFunction']= 'ExceptionRaise';
    $data_array['ApprovalStatus']=0;
    $data_array['IsReviewed']=1;
    $data_array['RaisedDatetime']=date('Y-m-d H:i:s');
    $data_array['RaisedByAPI']=$CreatedByAPI;
    $data_array['APIContentData']=$json_encode;
    $approval = $this->db->insert('torderapprovals',$data_array);  

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);

    $this->InsertDocuments($OrderUID,$Documents,'RequestRevision',$CreatedByAPI);  

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
    }
  }

  function Keystone_Internal_CompletedByLender($fieldArray,$CreatedByAPI){
    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $fieldArray['ServiceReasonRemarks'];
    $Documents = $fieldArray['Documents'];

    $Remarks = 'Action: InternalSubordination-CompletedByLender <br> Comment: '.$Comment;

    if($Documents)
    {
      //$this->InsertDocuments($OrderUID,$Documents,'InternalSubordination-CompletedByLender',$CreatedByAPI);  
       /**
      * @purpose : D2TINT-191 - To store the comment and attachment info in the same note
      * @author  : D.Samuel Prabhu
      * @Since   : 22 June 2020
      *
      */ 
      $insDoc = $this->InsertAttachmentDocuments($OrderUID,$Documents,'InternalSubordination-CompletedByLender',$CreatedByAPI);  

      $Remarks .=   $insDoc['Notes'];  
    }

    /* Insert notes for the event */
    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'EventCode' => 'InternalSubordination-CompletedByLender',
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );    

    $result = $this->db->insert("tordernotes", $insert_notes);

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
    }
  }


  function Keystone_External_ApprovedByLender($fieldArray,$CreatedByAPI){

    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $fieldArray['ServiceReasonRemarks'];
    $Documents = $fieldArray['Documents'];

    $Remarks = 'Action: ExternalSubordination-ApprovedByLender <br> Comment: '.$Comment;

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    if($Documents)
    {     
      /**
      * @purpose : D2TINT-191 - To store the comment and attachment info in the same note
      * @author  : D.Samuel Prabhu
      * @Since   : 22 June 2020
      *
      */ 
      $insDoc = $this->InsertAttachmentDocuments($OrderUID,$Documents,'ExternalSubordination-ApprovedByLender',$CreatedByAPI);  

      $Remarks .= $insDoc['Notes'];
    }

    /* Insert notes for the event */
    $insert_notes = array(
      'Note' => $Remarks,
      'EventCode' => 'ExternalSubordination-ApprovedByLender',
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);


    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
    }
  }

  function Keystone_External_RevisionRequest($fieldArray,$CreatedByAPI){

    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $fieldArray['ServiceReasonRemarks'];
    $Documents = $fieldArray['Documents'];

    $Remarks = 'Action: ExternalSubordination-RevisionRequest <br>'.'Code: '.$ServiceReasonRemarks['ReasonCode'].'<br> Reason: '.$ServiceReasonRemarks['ReasonCodeDescription'].'<br> Comment: '.$Comment;


    if($Documents)
    {     
       /**
      * @purpose : D2TINT-191 - To store the comment and attachment info in the same note
      * @author  : D.Samuel Prabhu
      * @Since   : 22 June 2020
      *
      */ 
      $insDoc = $this->InsertAttachmentDocuments($OrderUID,$Documents,'ExternalSubordination-RevisionRequest',$CreatedByAPI); 

      $Remarks .=   $insDoc['Notes'];     
    }

    /* Insert the notes for the event */
    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'EventCode' => 'ExternalSubordination-RevisionRequest',
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
    }

  }

  function Keystone_DeliverLenderSS($fieldArray,$CreatedByAPI){

    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $fieldArray['ServiceReasonRemarks'];
    $Documents = $fieldArray['Documents'];

    $Remarks = 'Action: DeliverLenderSS <br>'.'Code: '.$ServiceReasonRemarks['ReasonCode'].'<br> Reason: '.$ServiceReasonRemarks['ReasonCodeDescription'].'<br> Comment: '.$fieldArray['Comment'];

    if($Documents){
       /**
      * @purpose : D2TINT-191 - To store the comment and attachment info in the same note
      * @author  : D.Samuel Prabhu
      * @Since   : 22 June 2020
      *
      */ 
      $insDoc = $this->InsertAttachmentDocuments($OrderUID,$Documents,'ExternalSubordination-RevisionRequest',$CreatedByAPI);  

      $Remarks .=   $insDoc['Notes'];
    }

    /* Insert notes for the event */

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'EventCode' => 'DeliverLenderSS',
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);


    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
    }

  }

  function Keystone_Closing($fieldArray,$CreatedByAPI, $EventCode){

    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $fieldArray['ServiceReasonRemarks'];
    $Documents = $fieldArray['Documents'];
    $LoanIdentifier = $fieldArray['LoanIdentifier'];
    $ClosingInfo = $fieldArray['ClosingInfo'];

    $Remarks = 'Action:'.$EventCode.'<br> Comment: '.$Comment.'<br>';
    if($ServiceReasonRemarks['ReasonCode']){
      $Remarks.= 'Code: '.$ServiceReasonRemarks['ReasonCode'].'<br> Reason: '.$ServiceReasonRemarks['ReasonCodeDescription'].'<br>';
    }

    if($LoanIdentifier){
      $Remarks.= 'Loan Information : <br>';
      $Remarks.= 'LoanIdentifier: '.$LoanIdentifier['LoanIdentifier'].'<br> Type: '.$LoanIdentifier['LoanIdentifier'].'<br> Desc: '.$LoanIdentifier['LoanIdentifierTypeOtherDescription'].'<br>';
    }

    if($ClosingInfo){
      $Remarks.= 'Closing Information : <br>';
      $Remarks.= 'Expiration Date: '.$ClosingInfo['ClosingDocumentsExpirationDate'].'<br> Expiration Time: '.$ClosingInfo['ClosingDocumentsExpirationTime'];
    }


    if($Documents)
    {     
      /**
      * @purpose : D2TINT-191 - To store the comment and attachment info in the same note
      * @author  : D.Samuel Prabhu
      * @Since   : 22 June 2020
      *
      */ 
      $insDoc = $this->InsertAttachmentDocuments($OrderUID,$Documents,$EventCode,$CreatedByAPI);  

      $Remarks .=   $insDoc['Notes'];    
    }

    /* Insert notes for the event */
    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'EventCode' => $EventCode,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
    }
  }

  function Keystone_RequestPayoff($fieldArray,$CreatedByAPI, $EventCode){

    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $fieldArray['ServiceReasonRemarks'];
    $Documents = $fieldArray['Documents'];
    $PayoffDetails = $fieldArray['PayoffDetails'];
    $NoteData = json_encode($PayoffDetails); 

    $SalesContractAmount = $PayoffDetails['Collaterals']['COLLATERAL']['SUBJECT_PROPERTY']['SALES_CONTRACTS']['SALES_CONTRACT']['SALES_CONTRACT_DETAIL']['SalesContractAmount'];

    $Remarks = 'Action:'.$EventCode.'<br> Comment: '.$Comment.'<br>';
    if($ServiceReasonRemarks['ReasonCode']){
      $Remarks.= '<b>REASON & REMARKS:</b> <br>';
      $Remarks.= 'Code: '.$ServiceReasonRemarks['ReasonCode'].'<br> Reason: '.$ServiceReasonRemarks['ReasonCodeDescription'].'<br>';
    }

    if($SalesContractAmount){
      $Remarks.= '<b>COLLATERALS:</b> <br>';
      $Remarks.= 'Sales Contract Amount: '.$SalesContractAmount.'<br>';
    }

    if($PayoffDetails['Liabilities']){
      $Remarks.= $this->GetLiabilities($PayoffDetails['Liabilities']);
    }
    if($PayoffDetails['Loans']){
      $Remarks.= $this->GetLoans($PayoffDetails['Loans']);
    }

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'EventCode' => $EventCode,
      'NoteData' => $NoteData,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);

    if($Documents){
      $this->InsertDocuments($OrderUID,$Documents,$EventCode,$CreatedByAPI);  
    }

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
    }
  }

  function GetBorrowerByName($OrderUID, $BorrowerName){
    $PRName =  strtolower(preg_replace('/\s+/', '', trim($BorrowerName)));
    $this->db->select("*");
    $this->db->from('torderpropertyroles');
    //$this->db->where(array("torderpropertyroles.PRName"=>$PRName,"torderpropertyroles.OrderUID"=>$OrderUID));
    $this->db->where("LOWER(REPLACE(torderpropertyroles.PRName,' ',''))='".$PRName."'",null,false);
    $this->db->where("torderpropertyroles.OrderUID",$OrderUID);
    $query = $this->db->get();
    return $query->row();
  }

  /**
    * Function to get Property Roles for the Order
    *
    * @param  OrderUID (int)
    * 
    * @author Yagavi G <yagavi.g@avanzegroup.com>
    * @return array response 
    * @since date July 15th 2020
    * @version Closing Queue Management
    *
    */ 

  function GetBorrowersByOrderUID($OrderUID){
    $PropertyRoleUID = array('5','7','27');
    $this->db->select("*");
    $this->db->from('torderpropertyroles');
    $this->db->where("torderpropertyroles.OrderUID",$OrderUID);
    $this->db->where_in("torderpropertyroles.PropertyRoleUID",$PropertyRoleUID);
    $query = $this->db->get();
    return $query->result();
  }

  function InsertAPISchedule($fieldArray,$CreatedByAPI){

    $OrderUID = $fieldArray['OrderUID'];
    $Comment = $fieldArray['Comment'];

    $InBoundUID = $fieldArray['InBoundUID'];
    $Documents = $fieldArray['Documents'];
    $ClosingInfo = $fieldArray['ClosingInfo'];
    $IndivialPartyListDetails = $fieldArray['IndivialPartyListDetails'];

    $ClosingLocation = $ClosingInfo['CLOSING_LOCATIONS']['CLOSING_LOCATION']['ADDRESS'];
    $ClosingScheduledDatetime = $ClosingInfo['CLOSING_REQUEST']['CLOSING_REQUEST_DETAIL']['ClosingScheduledDatetime'];
    
    $matched = array();
    $unmatched = array();
    $BorrowerUIDs =[];
    $BorrowerNames =[];

    foreach ($IndivialPartyListDetails as $key => $value) {
        $PartyName = explode(':', $value['BorrowerName']);
        $BorrowerName = trim($PartyName[1]);

        $torderpropertyroles = $this->GetBorrowerByName($OrderUID, $BorrowerName);
        if($torderpropertyroles)
        {
            $BorrowerUID = $torderpropertyroles->Id;
            $BorrowerUIDs[] = $BorrowerUID;
        }
        else
        {
            /* Desc: Checking of FirstName and LastName of Borrower in ReadyToSchedule Event Author: Yagavi G <yagavi.g@avanzegroup.com> Since: July 15th 2020 */
            $propertyroles = $this->GetBorrowersByOrderUID($OrderUID);
            $is_matched = 0;
            foreach ($propertyroles as $key => $value) {
                $main_PRName = $value->PRName;
                $Id = $value->Id;
                $str_m = strtoupper(preg_replace("/[^A-Za-z0-9 ]/", '', $main_PRName));
                $main_BorrowerName = explode(' ',trim($str_m));

                $str_BorrowerName = strtoupper(preg_replace("/[^A-Za-z0-9 ]/", '', $BorrowerName));
                $event_borrowers = explode(' ',trim($str_BorrowerName));

                $allvalues= [];
                foreach($event_borrowers as $key => $val){
                    if(in_array($val, $main_BorrowerName) == true){
                        $allvalues[] = 1;
                    } else {
                        $allvalues[] = 0;
                    }
                }

                if (count(array_unique($allvalues)) === 1) {
                    if($allvalues[0] == 1) {
                        $BorrowerUID = $Id;
                        $is_matched = 1;
                        $BorrowerUIDs[] = $BorrowerUID;
                    } /*else {
                        $BorrowerNames[] = $BorrowerName;
                    }*/
                }
            }

            if($is_matched == 0){
                $BorrowerNames[] = $BorrowerName;
            }
        }
    }

    $BrUID = implode(', ', array_filter($BorrowerUIDs));
    if($BrUID)
    {
      $tApiOrderSchedule = array(
        'OrderUID' => $OrderUID,
        'BorrowerUID' => $BrUID,
        'ClosingAddress1' => $ClosingLocation['AddressLineText'],
        'ClosingAddress2' => $ClosingLocation['AddressAdditionalLineText'],
        'ClosingCityName' => $ClosingLocation['CityName'],
        'ClosingCountyName' => $ClosingLocation['CountyName'],
        'ClosingStateCode' => $ClosingLocation['StateCode'],
        'ClosingZipcode' => $ClosingLocation['PostalCode'],
        'ClosingScheduledDatetime' => date('Y-m-d H:i:s', strtotime($ClosingScheduledDatetime)),
        'SpecialInstruction' => $Comment,
        'IsMatching' => 1,
        'CreatedBy' => $CreatedByAPI,
        'CreatedDateTime' => date('Y-m-d H:i:s')
      );
      $this->db->insert("tApiOrderSchedule", $tApiOrderSchedule);

      //Update signing address for borrowers
      /*$signingAddress=[
                          'SigningAddress1'=> $ClosingLocation['AddressLineText'],
                          'SigningAddress2'=> $ClosingLocation['AddressAdditionalLineText'],
                          'SigningCityName'=> $ClosingLocation['CityName'],
                          'SigningCountyName'=> $ClosingLocation['CountyName'],
                          'SigningStateCode'=>  $ClosingLocation['StateCode'],
                          'SigningZipCode'=>$ClosingLocation['PostalCode'] 
                      ];
      $this->db->where('OrderUID',$OrderUID);
      $this->db->where_in('Id',array_filter($BorrowerUIDs));
      $this->db->update('torderpropertyroles',$signingAddress);*/
    }    

    $Br = implode(', ', array_filter($BorrowerNames));
    if($Br)
    {
      $tApiOrderSchedule = array(
        'OrderUID' => $OrderUID,
        'BorrowerUID' => $Br,
        'ClosingAddress1' => $ClosingLocation['AddressLineText'],
        'ClosingAddress2' => $ClosingLocation['AddressAdditionalLineText'],
        'ClosingCityName' => $ClosingLocation['CityName'],
        'ClosingCountyName' => $ClosingLocation['CountyName'],
        'ClosingStateCode' => $ClosingLocation['StateCode'],
        'ClosingZipcode' => $ClosingLocation['PostalCode'],
        'ClosingScheduledDatetime' => date('Y-m-d H:i:s', strtotime($ClosingScheduledDatetime)),
        'SpecialInstruction' => $Comment,
        'IsMatching' => 0,
        'CreatedBy' => $CreatedByAPI,
        'CreatedDateTime' => date('Y-m-d H:i:s')
      );
      $this->db->insert("tApiOrderSchedule", $tApiOrderSchedule);
    }
    
    /** 
     * @purpose: Update tOrderClosingTemp table 
     * @author : D.Samuel Prabhu
     * @since  : 21 Aug 2020
     */
    $OrderClosingTemp = $this->db->select('*')->from('tOrderClosingTemp')->where('OrderUID',$OrderUID)->get()->row();
    $SigningDateTime = date('Y-m-d H:i:s', strtotime($ClosingScheduledDatetime));
    $tOrderClosingTemp = array(
      'OrderUID'           => $OrderUID,
      'SigningDateTime'    => $SigningDateTime,
      'SpecialInstruction'    => $Comment,
      'SigningAddress1'    => ($ClosingLocation['AddressLineText'] ?: ''),
      'SigningAddress2'    => ($ClosingLocation['AddressAdditionalLineText'] ?: ''),
      'SigningZipCode'     => ($ClosingLocation['PostalCode'] ?: ''),
      'SigningCityName'    => ($ClosingLocation['CityName'] ?: ''),
      'SigningStateCode'   => ($ClosingLocation['StateCode'] ?: ''),
      'SigningCountyName'  => ($ClosingLocation['CountyName'] ?: ''),
      'IsSigningAddress'   => 'Others',
    );
    
    /* remove empty elements from array */
    $tOrderClosingTemp = array_filter($tOrderClosingTemp);

    if(!empty($OrderClosingTemp))
    {
        unset($tOrderClosingTemp['OrderUID']);

        $this->db->where(["OrderUID"=>$OrderUID]);        
        $this->db->update('tOrderClosingTemp', $tOrderClosingTemp);
    }
    else
    {
        $this->db->insert("tOrderClosingTemp", $tOrderClosingTemp);       
    }
   
    /* End of tOrderClosingTemp table update*/
  }

  function CheckBorrowerAssignedForSchedule($OrderUID, $BorrowerUID){
    $ScheduleStatus = array('Assign', 'Reschedule');
    $this->db->select('*, tOrderSchedule.ScheduleUID')->from('tOrderSchedule');
    $this->db->join('tOrderScheduleBorrower','tOrderScheduleBorrower.ScheduleUID = tOrderSchedule.ScheduleUID','left');
    $this->db->where('tOrderSchedule.OrderUID', $OrderUID);
    $this->db->where('tOrderScheduleBorrower.BorrowerUID', $BorrowerUID);
    $this->db->where_in('tOrderSchedule.ScheduleStatus', $ScheduleStatus);
    $q = $this->db->get();
    return $q->row(); 
  }

  function UpdateReSchedulingInfo($fieldArray,$CreatedByAPI){
    $OrderUID = $fieldArray['OrderUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $InBoundUID = $fieldArray['InBoundUID'];
    $Documents = $fieldArray['Documents'];
    $ClosingInfo = $fieldArray['ClosingInfo'];
    $IndivialPartyListDetails = $fieldArray['IndivialPartyListDetails'];
    $ClosingLocation = $ClosingInfo['CLOSING_LOCATIONS']['CLOSING_LOCATION']['ADDRESS'];
    $ClosingScheduledDatetime = $ClosingInfo['CLOSING_REQUEST']['CLOSING_REQUEST_DETAIL']['ClosingScheduledDatetime'];
    $SigningDateTime = date('Y-m-d H:i:s', strtotime($ClosingScheduledDatetime));

    foreach ($IndivialPartyListDetails as $key => $value) {
      $PartyName = explode(':', $value['BorrowerName']);
      $BorrowerName = $PartyName[1];
      $torderpropertyroles = $this->GetBorrowerByName($OrderUID, $BorrowerName);
      if($torderpropertyroles){
        $BorrowerUID = $torderpropertyroles->Id;
        $CheckBorrrower = $this->CheckBorrowerAssignedForSchedule($OrderUID,$BorrowerUID);
        $ScheduleUID = $CheckBorrrower->ScheduleUID;

        $tOrderSchedule = array(
          "ModifiedByUserUID"=>$this->api_model->GetIsgnUser(),
          'ModifiedDateTime'=>date('Y-m-d H:i:s'),
          "SigningDateTime"=> $SigningDateTime,
          "ScheduleStatus"=> "Reschedule",
        );
        $this->db->where('OrderUID',$OrderUID);
        $this->db->where('ScheduleUID',$ScheduleUID);
        $this->db->update('tOrderSchedule',$tOrderSchedule);

        $tOrderSigning = array(
          "ModifiedUserUID"=>$this->api_model->GetIsgnUser(),
          'ModifiedDateTime'=>date('Y-m-d H:i:s'),
          "SignedDateTime"=> $SigningDateTime,
          "SigningStatus"=> "Reschedule",
        );

        $this->db->where('OrderUID',$OrderUID);
        $this->db->where('ScheduleUID',$ScheduleUID);
        $this->db->update('tOrderSign',$tOrderSigning); 

        $ClosingData = array(
          'IsSigningAddress'=>'Others',
          'SigningAddress1'=>$ClosingLocation['AddressLineText'],
          'SigningAddress2'=>$ClosingLocation['AddressAdditionalLineText'],
          'SigningZipCode'=>$ClosingLocation['PostalCode'],
          'SigningCityName'=>$ClosingLocation['CityName'],
          'SigningStateCode'=>$ClosingLocation['StateCode'],
          'SigningCountyName'=>$ClosingLocation['CountyName']
        );
        $this->db->where('OrderUID',$OrderUID);
        $this->db->where('ScheduleUID',$ScheduleUID);
        $this->db->update('tOrderClosing',$ClosingData);

        /* @purpose: To Update Signing Details in Summary @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: May 19th 2020 */
        $ClosingAddress = $this->GetScheduleInformation($fieldArray);
        $this->UpdateSigningDetails($OrderUID,$ClosingAddress,$BorrowerUID);       
        

      } else {
        //echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID,'Message' => 'There is no borrower mapped.'));exit;
      }
    }

    /* Update tOrderClosingTemp table */
    $OrderClosingTemp = $this->db->select('*')->from('tOrderClosingTemp')->where('OrderUID',$OrderUID)->get()->row();

    $tOrderClosingTemp = array(
      'OrderUID'           => $OrderUID,
      'SigningDateTime'    => $SigningDateTime,
      'SpecialInstruction' => $Comment,
      'SigningAddress1'    => ($ClosingLocation['AddressLineText'] ?: ''),
      'SigningAddress2'    => ($ClosingLocation['AddressAdditionalLineText'] ?: ''),
      'SigningZipCode'     => ($ClosingLocation['PostalCode'] ?: ''),
      'SigningCityName'    => ($ClosingLocation['CityName'] ?: ''),
      'SigningStateCode'   => ($ClosingLocation['StateCode'] ?: ''),
      'SigningCountyName'  => ($ClosingLocation['CountyName'] ?: ''),
      'IsSigningAddress'   => 'Others',
    );
    
    /* remove empty elements from array */
    $tOrderClosingTemp = array_filter($tOrderClosingTemp);

    if(!empty($OrderClosingTemp))
    {
        unset($tOrderClosingTemp['OrderUID']);

        $this->db->where(["OrderUID"=>$OrderUID]);        
        $this->db->update('tOrderClosingTemp', $tOrderClosingTemp);
    }
    else
    {
        $this->db->insert("tOrderClosingTemp", $tOrderClosingTemp);       
    }
   
    /* End of tOrderClosingTemp table update*/
  }

  function Keystone_ReadyToSchedule($fieldArray,$CreatedByAPI, $EventCode){

    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $fieldArray['ServiceReasonRemarks'];
    $Documents = $fieldArray['Documents'];
    $PartyListDetails = $fieldArray['PartyListDetails'];
    $LoanDetails = $fieldArray['LoanDetails'];
    $ClosingInfo = $fieldArray['ClosingInfo'];
    $IndivialPartyListDetails = $fieldArray['IndivialPartyListDetails'];
    $XML_Details = array_merge($LoanDetails, $ClosingInfo);
    $NoteData = json_encode($XML_Details); 

    /* Insert API Schedule data */
    $this->InsertAPISchedule($fieldArray,$CreatedByAPI);

    $Remarks = 'Action:'.$EventCode.'<br> Comment: '.$Comment.'<br>';
    if($ServiceReasonRemarks['ReasonCode']){
      $Remarks.= '<b>REASON & REMARKS:</b> <br>';
      $Remarks.= 'Code: '.$ServiceReasonRemarks['ReasonCode'].'<br> Reason: '.$ServiceReasonRemarks['ReasonCodeDescription'].'<br>';
    }
    if($LoanDetails){
      $Remarks.= $this->GetLoans($LoanDetails);
    }
    if($IndivialPartyListDetails){
      $Remarks.= $this->GetIndividualPartyListDetails($IndivialPartyListDetails);
    }
    if($ClosingInfo){
      $Remarks.= $this->GetClosingInfo($ClosingInfo);
    }

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'EventCode' => $EventCode,
      'NoteData' => $NoteData,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);

    if($Documents){
      $this->InsertDocuments($OrderUID,$Documents,$EventCode,$CreatedByAPI);  
    }

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
    }
  }

  function Keystone_RescheduledSigning($fieldArray,$CreatedByAPI, $EventCode){

    $this->db->trans_begin();
    $OrderUID = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $ServiceReasonRemarks = $fieldArray['ServiceReasonRemarks'];
    $Documents = $fieldArray['Documents'];
    $PartyListDetails = $fieldArray['PartyListDetails'];
    $LoanDetails = $fieldArray['LoanDetails'];
    $ClosingInfo = $fieldArray['ClosingInfo'];
    $IndivialPartyListDetails = $fieldArray['IndivialPartyListDetails'];
    $XML_Details = array_merge($LoanDetails, $ClosingInfo);
    $NoteData = json_encode($XML_Details);

    /* Insert API Schedule data */
    $this->UpdateReSchedulingInfo($fieldArray,$CreatedByAPI);

    $Remarks = 'Action:'.$EventCode.'<br> Comment: '.$Comment.'<br>';
    if($ServiceReasonRemarks['ReasonCode']){
      $Remarks.= '<b>REASON & REMARKS:</b> <br>';
      $Remarks.= 'Code: '.$ServiceReasonRemarks['ReasonCode'].'<br> Reason: '.$ServiceReasonRemarks['ReasonCodeDescription'].'<br>';
    }
    if($LoanDetails){
      $Remarks.= $this->GetLoans($LoanDetails);
    }

    if($IndivialPartyListDetails){
      $Remarks.= $this->GetIndividualPartyListDetails($IndivialPartyListDetails);
    }

    if($PartyListDetails){
      $Remarks.= $this->GetPartyListDetails($PartyListDetails);
    }

    if($ClosingInfo){
      $Remarks.= $this->GetClosingInfo($ClosingInfo);
    }

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'EventCode' => $EventCode,
      'NoteData' => $NoteData,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    $result = $this->db->insert("tordernotes", $insert_notes);

    if($Documents){
      $this->InsertDocuments($OrderUID,$Documents,$EventCode,$CreatedByAPI);  
    }

    if ($this->db->trans_status() === FALSE) {
      $this->db->trans_rollback();
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }else{
      $this->db->trans_commit();
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
    }
  }

  function GetLiabilities($Liabilities){

    $holder_details = $Liabilities['LIABILITY']['LIABILITY_HOLDER']['NAME'];
    $payoff_details = $Liabilities['LIABILITY']['PAYOFF'];

    $Remarks.= '<b>LIABILITIES:</b><br>';
    $Remarks.= '<u>Holder:</u> <br>';
    $Remarks.= 'Name: '.$holder_details['FirstName'].' '.$holder_details['MiddleName'].' '.$holder_details['LastName'].'<br>';
    $Remarks.= 'Functional Title Description: '.$holder_details['FunctionalTitleDescription'].'<br>';
    $Remarks.= '<u>Payoff:</u><br>';
    $Remarks.= 'PayoffAccountNumberIdentifier: '.$payoff_details['PayoffAccountNumberIdentifier'].'<br>';
    $Remarks.= 'PayoffAmount: '.$payoff_details['PayoffAmount'].'<br>';
    $Remarks.= 'PayoffOrderedDatetime: '.$payoff_details['PayoffOrderedDatetime'].'<br>';
    $Remarks.= 'PayoffPartialIndicator: '.$payoff_details['PayoffPartialIndicator'].'<br>';
    $Remarks.= 'PayoffPerDiemAmount: '.$payoff_details['PayoffPerDiemAmount'].'<br>';
    $Remarks.= 'PayoffRequestedByType: '.$payoff_details['PayoffRequestedByType'].'<br>';
    $Remarks.= 'PayoffRequestedByTypeOtherDescription: '.$payoff_details['PayoffRequestedByTypeOtherDescription'].'<br>';
    $Remarks.= 'EmployeeReference: '.$payoff_details['EXTENSION']['OTHER']['EmployeeReference'].'<br>';

    return $Remarks;
  }

  function GetLoans($Loans){

    $Remarks.= '<b>LOANS:</b><br>';
    if($Loans['LOAN']['ADJUSTMENT']['INTEREST_RATE_ADJUSTMENT']['INTEREST_RATE_LIFETIME_ADJUSTMENT_RULE']['MarginRatePercent']){
      $Remarks.= '<u>ADJUSTMENT</u><br>';
      $Remarks.= 'MarginRatePercent: '.$Loans['LOAN']['ADJUSTMENT']['INTEREST_RATE_ADJUSTMENT']['INTEREST_RATE_LIFETIME_ADJUSTMENT_RULE']['MarginRatePercent'].'<br>';
    }

    if($Loans['LOAN']['FEE_INFORMATION']['FEES_SUMMARY']['FEE_SUMMARY_DETAIL']['APRPercent']){
      $Remarks.= '<u>FEE_INFORMATION</u><br>';
      $Remarks.= 'APRPercent: '.$Loans['LOAN']['FEE_INFORMATION']['FEES_SUMMARY']['FEE_SUMMARY_DETAIL']['APRPercent'].'<br>';
    }

    if($Loans['LOAN']['LOAN_DETAIL']['InitialFixedPeriodEffectiveMonthsCount']){      
      $Remarks.= '<u>LOAN_DETAIL</u><br>';
      $Remarks.= 'InitialFixedPeriodEffectiveMonthsCount: '.$Loans['LOAN']['LOAN_DETAIL']['InitialFixedPeriodEffectiveMonthsCount'].'<br>';
    }

    if($Loans['LOAN']['LOAN_IDENTIFIERS']['LOAN_IDENTIFIER']['LoanIdentifier']){
      $Remarks.= '<u>LOAN_IDENTIFIERS</u><br>';
      $Remarks.= 'LoanIdentifier: '.$Loans['LOAN']['LOAN_IDENTIFIERS']['LOAN_IDENTIFIER']['LoanIdentifier'].'<br>';
      $Remarks.= 'LoanIdentifierType: '.$Loans['LOAN']['LOAN_IDENTIFIERS']['LOAN_IDENTIFIER']['LoanIdentifierType'].'<br>';
      $Remarks.= 'LoanIdentifierTypeOtherDescription: '.$Loans['LOAN']['LOAN_IDENTIFIERS']['LOAN_IDENTIFIER']['LoanIdentifierTypeOtherDescription'].'<br>';      
    }

    if($Loans['LOAN']['LOAN_PROGRAMS']['LOAN_PROGRAM']['LoanProgramIdentifier']){
      $Remarks.= '<u>LOAN_PROGRAMS</u><br>';
      $Remarks.= 'LoanProgramIdentifier: '.$Loans['LOAN']['LOAN_PROGRAMS']['LOAN_PROGRAM']['LoanProgramIdentifier'].'<br>';      
    }

    if($Loans['LOAN']['LTV']['LTVRatioPercent']){
      $Remarks.= '<u>LTV</u><br>';
      $Remarks.= 'LTVRatioPercent: '.$Loans['LOAN']['LTV']['LTVRatioPercent'].'<br>';      
    }

    if($Loans['LOAN']['LTV']['LTVRatioPercent']){
      $Remarks.= '<u>LTV</u><br>';
      $Remarks.= 'LTVRatioPercent: '.$Loans['LOAN']['LTV']['LTVRatioPercent'].'<br>';      
    }

    if($Loans['LOAN']['PAYMENT']['PAYMENT_RULE']['ScheduledFirstPaymentDate']){
      $Remarks.= '<u>PAYMENT</u><br>';
      $Remarks.= 'ScheduledFirstPaymentDate: '.$Loans['LOAN']['PAYMENT']['PAYMENT_RULE']['ScheduledFirstPaymentDate'].'<br>';      
    }

    if($Loans['LOAN']['REFINANCE']['RefinanceCashOutAmount']){
      $Remarks.= '<u>REFINANCE</u><br>';
      $Remarks.= 'RefinanceCashOutAmount: '.$Loans['LOAN']['REFINANCE']['RefinanceCashOutAmount'].'<br>';
      $Remarks.= 'RefinanceCashOutDeterminationType: '.$Loans['LOAN']['REFINANCE']['RefinanceCashOutDeterminationType'].'<br>';      
    }

    if($Loans['LOAN']['TERMS_OF_LOAN']['LoanPurposeType']){
      $Remarks.= '<u>TERMS_OF_LOAN</u><br>';
      $Remarks.= 'LienPriorityType: '.$Loans['LOAN']['TERMS_OF_LOAN']['LienPriorityType'].'<br>';
      $Remarks.= 'LoanPurposeType: '.$Loans['LOAN']['TERMS_OF_LOAN']['LoanPurposeType'].'<br>';
      $Remarks.= 'LoanPurposeTypeOtherDescription: '.$Loans['LOAN']['TERMS_OF_LOAN']['LoanPurposeTypeOtherDescription'].'<br>';
      $Remarks.= 'NoteAmount: '.$Loans['LOAN']['TERMS_OF_LOAN']['NoteAmount'].'<br>';
    }

    return $Remarks;
  }

  function GetClosingInfo($ClosingInfo){

    $ClosingAddress = $ClosingInfo['CLOSING_LOCATIONS']['CLOSING_LOCATION']['ADDRESS'];
    $ClosingLocationDetails = $ClosingInfo['CLOSING_LOCATIONS']['CLOSING_LOCATION']['CLOSING_LOCATION_DETAIL'];
    $ClosingRequest = $ClosingInfo['CLOSING_REQUEST'];

    $Remarks.= '<b>CLOSING LOCATIONS:</b><br>';
    $Remarks.= '<u>ADDRESS</u><br>';
    $Remarks.= 'Address1: '.$ClosingAddress['AddressLineText'].'<br>';
    $Remarks.= 'Address2: '.$ClosingAddress['AddressAdditionalLineText'].'<br>';
    $Remarks.= 'CityName: '.$ClosingAddress['CityName'].'<br>';
    $Remarks.= 'StateCode: '.$ClosingAddress['StateCode'].'<br>';
    $Remarks.= 'PostalCode: '.$ClosingAddress['PostalCode'].'<br>';
    $Remarks.= '<u>CLOSING_LOCATION_DETAIL</u><br>';
    $Remarks.= 'ClosingEventLocationType: '.$ClosingLocationDetails['ClosingEventLocationType'].'<br>';
    $Remarks.= 'ClosingEventLocationTypeOtherDescription: '.$ClosingLocationDetails['ClosingEventLocationTypeOtherDescription'].'<br>';
    $Remarks.= '<b>CLOSING REQUEST:</b><br>';
    $Remarks.= 'ClosingScheduledDatetime: '.date('m/d/Y H:i:s', strtotime($ClosingRequest['CLOSING_REQUEST_DETAIL']['ClosingScheduledDatetime'])) .'<br>';

    return $Remarks;
  }

  function GetPartyListDetails($Parties){

    $Remarks.= '<b>Party Lists:</b><br>';
    foreach ($Parties as $key => $value) {
      $c = $key+1;
      $Name = explode(':', $value['BorrowerName'.$c]);
      $Remarks.= '<u>Party:'.$c.'</u><br>';
      $Remarks.= 'BorrowerName: '.$Name[1].'<br>';
      $Remarks.= 'Email: '.$value['Email'.$c].'<br>';
      $Remarks.= 'HomeNumber: '.$value['HomeNumber'.$c].'<br>';
      $Remarks.= 'WorkNumber: '.$value['WorkNumber'.$c].'<br>';
    }
    return $Remarks;
  }

  function GetIndividualPartyListDetails($Parties){
    $Remarks.= '<b>Party Lists:</b><br>';
    foreach ($Parties as $key => $value) {
      $c = $key+1;
      $Name = explode(':', $value['BorrowerName']);
      $Remarks.= '<u>Party:'.'</u><br>';
      $Remarks.= 'BorrowerName: '.$Name[1].'<br>';
      $Remarks.= 'Email: '.$value['Email'].'<br>';
      $Remarks.= 'HomeNumber: '.$value['HomeNumber'].'<br>';
      $Remarks.= 'WorkNumber: '.$value['WorkNumber'].'<br>';
    }
    return $Remarks;
  }

  function Keystone_RevisionRequest_old($DataArray,$CreatedByAPI){ 

    $TransactionID = $DataArray['TransactionID'];
    $StatusUID = $DataArray['StatusUID'];
    $OrderUID = $DataArray['OrderUID'];
    $InBoundUID = $DataArray['InBoundUID']; 
    $Comment = $DataArray['Comment'];
    /*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }

    $ServiceReasonRemarks = $DataArray['ServiceReasonRemarks'];
    $Documents = $DataArray['Documents'];

    $this->db->trans_begin();
    $code = $ServiceReasonRemarks['ReasonCode'];
    $ApiStatusCodeDetails = $this->GetApiStatusCodeDetails($code,$CreatedByAPI); 

    if($ApiStatusCodeDetails) {

      $ApiRequestUID = $ApiStatusCodeDetails->ApiRequestUID;

      $tApiRequestStatus = array(
        "ApiRequestUID"=>$ApiRequestUID,
        "OrderUID"=>$OrderUID,
        "CreatedBy"=>$this->api_model->GetIsgnUser(),
        "ApiRequestComment"=>$Comment,
      );
      $this->db->insert('tApiRequestStatus', $tApiRequestStatus);    

      $Remarks = 'Exception Raised by Keystone <br>'.'Action: RequestRevision <br>'.'Code: '.$ServiceReasonRemarks['ReasonCode'].'<br> Reason: '.$ServiceReasonRemarks['ReasonCodeDescription'].'<br> Remarks: '.$ServiceReasonRemarks['ReasonCommentText'].'<br> Comment: '.$Comment;
      $NoteType = $this->GetNoteTypeUID('API Note');
      $SectionUID = $NoteType->SectionUID;

      /*Exception raise */
      $texceptions = array(
        "OrderUID"=>$OrderUID,
        "ExceptionUID"=>$SectionUID,
        "Remarks"=>$Remarks,
        "RaisedByAPI"=>$CreatedByAPI,
        "RaisedOn"=> Date('Y-m-d H:i:s',strtotime("now"))
      );

      /*Change Status*/
      $torders = $this->db->get_where('torders', array('OrderUID'=>$OrderUID))->row();
      $ExceptionRaised = $this->config->item('keywords')['Exception Raised']; 
      if($torders->StatusUID == $ExceptionRaised ) {
        $status = array("StatusUID"=>$ExceptionRaised,"IsPrint"=>NULL);
      } else{
        $status = array("StatusUID"=>$ExceptionRaised,"IsPrint"=>NULL,"IsExceptionOldStatusUID"=>$torders->StatusUID);        
      }

      $this->db->insert('texceptions', $texceptions);
      $this->db->where(array("torders.OrderUID"=>$OrderUID));
      $this->db->update('torders',$status);

      /**
      * @purpose : D2TINT-191 - To store the comment and attachment info in the same note
      * @author  : D.Samuel Prabhu
      * @Since   : 22 June 2020
      *
      */ 
      $insDoc = $this->InsertAttachmentDocuments($OrderUID,$Documents,'RequestRevision',$CreatedByAPI);
      $Remarks .=   $insDoc['Notes'];

      /* Insert notes for the event */
      $insert_notes = array(
        'Note' => $Remarks,
        'SectionUID' => $SectionUID,
        'OrderUID' => $OrderUID,
        'EventCode' => 'RequestRevision', /* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
        'RoleType' => '1,2,3,4,5,6,7,9,11,12',
        'CreatedByAPI' => $CreatedByAPI,
        'CreatedOn' => date('Y-m-d H:i:s')
      );


      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'message'=>'RequestRevision Event failed in Isgn Organization'));
      } else {
        $this->db->trans_commit();
        $this->db->insert("tordernotes", $insert_notes);
        echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'RequestRevision Event Inserted in Isgn Organization'));
      } 
    } else {
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'message'=>'Unable to execute!! Request Revision Code is not available'));
    }
  }

  function Keystone_RequestSSRevision($DataArray,$CreatedByAPI)
  { 
    $TransactionID = $DataArray['TransactionID'];
    $StatusUID = $DataArray['StatusUID'];
    $OrderUID = $DataArray['OrderUID'];
    $InBoundUID = $DataArray['InBoundUID']; 
    $Comment = $DataArray['Comment'];

    $OrderDetails = $this->common_model->get_orderdetails($OrderUID);
    $OrderSourceUID = $OrderDetails->OrderSourceUID;

    $Remarks = 'Exception Raised by Keystone: <br>'.$DataArray['Comment'];
    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    if ($this->db->trans_status() === FALSE)
    {
      $this->db->trans_rollback();
      return array('status' => 'failed','InBoundUID' =>$InBoundUID, 'message'=>'222 Event failed in Isgn Organization');
    }
    else
    {
      $this->db->trans_commit();
      $this->db->insert("tordernotes", $insert_notes);
      return array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'222 Event Inserted in Isgn Organization');
    } 
  } 

  function Keystone_ApproveSS($DataArray,$CreatedByAPI)
  { 
    $TransactionID = $DataArray['TransactionID'];
    $StatusUID = $DataArray['StatusUID'];
    $OrderUID = $DataArray['OrderUID'];
    $InBoundUID = $DataArray['InBoundUID']; 
    $Comment = $DataArray['Comment'];

    $OrderDetails = $this->common_model->get_orderdetails($OrderUID);
    $OrderSourceUID = $OrderDetails->OrderSourceUID;

    $Remarks = 'Exception Raised by Keystone: <br>'.$DataArray['Comment'];
    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    if ($this->db->trans_status() === FALSE)
    {
      $this->db->trans_rollback();
      return array('status' => 'failed','InBoundUID' =>$InBoundUID, 'message'=>'222 Event failed in Isgn Organization');
    }
    else
    {
      $this->db->trans_commit();
      $this->db->insert("tordernotes", $insert_notes);
      return array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'222 Event Inserted in Isgn Organization');
    } 
  } 

  function Keystone_DeliverFinalSS($DataArray,$CreatedByAPI)
  { 
    $TransactionID = $DataArray['TransactionID'];
    $StatusUID = $DataArray['StatusUID'];
    $OrderUID = $DataArray['OrderUID'];
    $InBoundUID = $DataArray['InBoundUID']; 
    $Comment = $DataArray['Comment'];

    $OrderDetails = $this->common_model->get_orderdetails($OrderUID);
    $OrderSourceUID = $OrderDetails->OrderSourceUID;

    $Remarks = 'Exception Raised by Keystone: <br>'.$DataArray['Comment'];
    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Remarks,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $CreatedByAPI,
      'CreatedOn' => date('Y-m-d H:i:s')
    );

    if($this->db->trans_status() === FALSE)
    {
      $this->db->trans_rollback();
      return array('status' => 'failed','InBoundUID' =>$InBoundUID, 'message'=>'222 Event failed in Isgn Organization');
    }
    else
    {
      $this->db->trans_commit();
      $this->db->insert("tordernotes", $insert_notes);
      return array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'222 Event Inserted in Isgn Organization');
    } 
  } 

  function CheckOrderCancelRevoke($OrderUID){
    $this->db->select ( '*' ); 
    $this->db->from ( 'tOrderRevoke' );
    $this->db->where ('tOrderRevoke.OrderUID',$OrderUID);
    $this->db->where ('tOrderRevoke.Status', 0);
    $query = $this->db->get(); 
    $res = $query->result();

    if($res){
      return true;
    } else {
      return false;
    }
  }

  function GetApiStatusCodeDetails($RequestCode,$CreatedByAPI){
    $this->db->select ( '*' ); 
    $this->db->from ( 'mApiRequest' );
    $this->db->where ('mApiRequest.RequestCode',$RequestCode);
    $this->db->where ('mApiRequest.ApiTitlePlatformUID',$CreatedByAPI);
    $query = $this->db->get();
    $res = $query->row();
    return $res;
  }


  function RequestToKeystone($OrderUID,$EventCode,$Remarks){

    $Is_Api = $this->CheckApiOrders($OrderUID);
    $Details = $this->GetInBoundTransactionDetails($OrderUID);
    $InBoundUID = $Details->InBoundUID;
    $TransactionID = $Details->TransactionID;
    $ApiOrderRequestUID = $Details->ApiOrderRequestUID;
    $OrderNumber = $Details->OrderNumber;

    $AppSourceName = $this->GetSourceName($OrderUID); 
    $SourceName = trim($AppSourceName->OrderSourceName);
    $CustomerAmount = $AppSourceName->CustomerAmount;
    $Integration = "Keystone";

    /* if($SourceName == "Keystone") {
      $Action = "EscalationResponse";
      $Integration = "Keystone";
    } else {
      $Integration = "";
    } */

    if($Is_Api){
      $url_send = $this->config->item("api_url");
      $data = array(
        'EventCode' => $EventCode,
        'InBoundUID' => $InBoundUID,
        'TransactionID' => $TransactionID,
        'ApiOrderRequestUID' => $ApiOrderRequestUID,
        'SourceName' => $Integration,
        'Comment' => $this->clean($Remarks),
        'OrderUID' => $OrderUID,
        'OrderNumber' => $OrderNumber,
      );

      $str_data = json_encode($data);
      $result = $this->sendPostData($url_send, $str_data);
    }
  }

  /* @purpose To get the scheduling information
  ** @author Yagavi G <yagavi.g@avanzegroup.com>
  ** @since 9th April 2020
  */

  function GetScheduleInformation($fieldArray){
    $OrderUID = $fieldArray['OrderUID'];
    $Comment = $fieldArray['Comment'];
    if(empty($Comment)){
      $Comment = 'No Comments from Client';
    }
    $InBoundUID = $fieldArray['InBoundUID'];
    $Documents = $fieldArray['Documents'];
    $ClosingInfo = $fieldArray['ClosingInfo'];
    $IndivialPartyListDetails = $fieldArray['IndivialPartyListDetails'];

    $ClosingLocation = $ClosingInfo['CLOSING_LOCATIONS']['CLOSING_LOCATION']['ADDRESS'];
    $ClosingScheduledDatetime = $ClosingInfo['CLOSING_REQUEST']['CLOSING_REQUEST_DETAIL']['ClosingScheduledDatetime'];
    $BorrowerName=[];

    foreach ($IndivialPartyListDetails as $key => $value) {
      $PartyName = explode(':', $value['BorrowerName']);
      $BorrowerName[] = $PartyName[1];
    }

    $BorrowerNames = implode(', ', $BorrowerName);
    $tApiOrderSchedule = array(
      'OrderUID' => $OrderUID,
      'BorrowerName' => $BorrowerNames,
      'ClosingAddress1' => $ClosingLocation['AddressLineText'],
      'ClosingAddress2' => $ClosingLocation['AddressAdditionalLineText'],
      'ClosingCityName' => $ClosingLocation['CityName'],
      'ClosingCountyName' => $ClosingLocation['CountyName'],
      'ClosingStateCode' => $ClosingLocation['StateCode'],
      'ClosingZipcode' => $ClosingLocation['PostalCode'],
      'ClosingScheduledDatetime' => date('Y-m-d H:i:s', strtotime($ClosingScheduledDatetime)),
      'SpecialInstruction' => $Comment
    );

    return $tApiOrderSchedule;
  }

  function UpdateSigningDetails($OrderUID,$ClosingDetails,$BorrowerUID=''){
    $this->db->select('*')->from('torders')->where('OrderUID', $OrderUID);
    $torders = $this->db->get()->row();

    if($torders){
      /* Property Address */
      $PropertyAddress1 = $torders->PropertyAddress1;
      $PropertyAddress2 = $torders->PropertyAddress2;
      $PropertyCityName = $torders->PropertyCityName;
      $PropertyCountyName = $torders->PropertyCountyName;
      $PropertyStateCode = $torders->PropertyStateCode;
      $PropertyZipcode = $torders->PropertyZipcode;

      /* Mailing Addess*/
      $this->db->select('*')->from('torderpropertyroles')->where('OrderUID', $OrderUID)->where('Id', $BorrowerUID);
      $torderpropertyroles = $this->db->get()->row();
      $MailingAddress1 = $torderpropertyroles->MailingAddress1;
      $MailingAddress2 = $torderpropertyroles->MailingAddress2;
      $MailingCityName = $torderpropertyroles->MailingCityName;
      $MailingCountyName = $torderpropertyroles->MailingCountyName;
      $MailingStateCode = $torderpropertyroles->MailingStateCode;
      $MailingZipCode = $torderpropertyroles->MailingZipCode;

      /*Signing Address */
      $ClosingAddress1 = $ClosingDetails['ClosingAddress1'];
      $ClosingAddress2 = $ClosingDetails['ClosingAddress2'];
      $ClosingCityName = $ClosingDetails['ClosingCityName'];
      $ClosingCountyName = $ClosingDetails['ClosingCountyName'];
      $ClosingStateCode = $ClosingDetails['ClosingStateCode'];
      $ClosingZipcode = $ClosingDetails['ClosingZipcode'];
      $SpecialInstruction = $ClosingDetails['SpecialInstruction'];

      //Fix to prevent inserting county as empty 
      if(empty($ClosingCountyName))
      {
          $this->load->model('order_complete/order_complete_model');
          if($ClosingZipcode){
            $CountyName = $this->order_complete_model->getCountyDetail($ClosingZipcode);
            $ClosingCountyName = $CountyName[0]->CountyName;
          }
      }

      if(
            strtolower(preg_replace('/\s+/','',$ClosingZipcode))   == strtolower(preg_replace('/\s+/','',$PropertyZipcode)) &&
            strtolower(preg_replace('/\s+/','',$ClosingStateCode)) == strtolower(preg_replace('/\s+/','',$PropertyStateCode)) &&
            strtolower(preg_replace('/\s+/','',$ClosingCityName))  == strtolower(preg_replace('/\s+/','',$PropertyCityName)) &&
            strtolower(preg_replace('/\s+/','',$ClosingAddress1))  == strtolower(preg_replace('/\s+/','',$PropertyAddress1)) 
            //&& strtolower(preg_replace('/\s+/','',$ClosingAddress2))    = strtolower(preg_replace('/\s+/','',$PropertyAddress2)) 
           // && strtolower(preg_replace('/\s+/','',$ClosingCountyName))  = strtolower(preg_replace('/\s+/','',$PropertyCountyName)) 

      ){ /* Check for Signing with Property Address - if matched enabled property radio button in summary for the particular borrower*/

          $address['IsSigningAddress']  = 'property';
          /*Notes:*/
          $Notes = "Signing address is same as property address for borrower".$torderpropertyroles->PRName.".<br/>";

      } else if (
                  strtolower(preg_replace('/\s+/','',$ClosingZipcode))    == strtolower(preg_replace('/\s+/','',$MailingZipCode)) &&
                  strtolower(preg_replace('/\s+/','',$ClosingStateCode))  == strtolower(preg_replace('/\s+/','',$MailingStateCode)) &&
                  strtolower(preg_replace('/\s+/','',$ClosingCityName))   == strtolower(preg_replace('/\s+/','',$MailingCityName)) &&
                  strtolower(preg_replace('/\s+/','',$ClosingAddress1))   == strtolower(preg_replace('/\s+/','',$MailingAddress1)) 
                   // && strtolower(preg_replace('/\s+/','',$ClosingAddress2))   = strtolower(preg_replace('/\s+/','',$MailingAddress2)) 
                   // && strtolower(preg_replace('/\s+/','',$ClosingCountyName)) = strtolower(preg_replace('/\s+/','',$MailingCountyName)) 

      ){ /* Check for Signing with Mailing Address for the borrower - if matched enabled mailing radio button in summary for the particular borrower*/

         $address['IsSigningAddress']  = 'mailing';
          /*Notes:*/
         $Notes = "Signing address is same as mailing address for borrower ".$torderpropertyroles->PRName.".<br/>";

      } else { /* Not matched with property and mailing - then enable other and update signing address in torderpropertyroles */

           $address = [
                          'SigningAddress1'   => $ClosingAddress1,
                          'SigningAddress2'   => $ClosingAddress2,
                          'SigningCityName'   => $ClosingCityName,
                          'SigningCountyName' => $ClosingCountyName,
                          'SigningStateCode'  => $ClosingStateCode,
                          'SigningZipCode'    => $ClosingZipcode,
                          'IsSigningAddress'  => 'others'

                      ];

          /*Notes: */
          $Notes = "Signing address is not matched with property & mailing address for borrower ". $torderpropertyroles->PRName.".<br/>";
          
          if(
                 $ClosingDetails['ClosingAddress1']   != $torderpropertyroles->SigningAddress1
              || $ClosingDetails['ClosingAddress2']   != $torderpropertyroles->SigningAddress2
              || $ClosingDetails['ClosingCityName']   != $torderpropertyroles->SigningCityName
              || $ClosingDetails['ClosingStateCode']  != $torderpropertyroles->SigningStateCode
              || $ClosingDetails['ClosingZipcode']    != $torderpropertyroles->SigningStateCode
              || $ClosingDetails['ClosingCountyName'] != $torderpropertyroles->SigningCountyName
            )
          {
              $Notes .= "Address1 is changed to ".(!empty($ClosingDetails['ClosingAddress1']) ? $ClosingDetails['ClosingAddress1'] : '-')." from ".(!empty($torderpropertyroles->SigningAddress1) ? $torderpropertyroles->SigningAddress1 : '-' )."<br/>";            
              $Notes .= "Address2 is changed to ".(!empty($ClosingDetails['ClosingAddress2']) ? $ClosingDetails['ClosingAddress2'] : '-')." from ".(!empty($torderpropertyroles->SigningAddress2) ? $torderpropertyroles->SigningAddress2 : '-' )."<br/>";
              $Notes .= "City is changed to ".(!empty($ClosingDetails['ClosingCityName']) ? $ClosingDetails['ClosingCityName'] : '-')." from ".(!empty($torderpropertyroles->SigningCityName) ? $torderpropertyroles->SigningCityName : '-' )."<br/>";
              $Notes .= "State is changed to ".(!empty($ClosingDetails['ClosingStateCode']) ? $ClosingDetails['ClosingStateCode'] : '-')." from ".(!empty($torderpropertyroles->SigningStateCode) ? $torderpropertyroles->SigningStateCode : '-' )."<br/>";
              $Notes .= "Zipcode is changed to ".(!empty($ClosingDetails['ClosingZipcode']) ? $ClosingDetails['ClosingZipcode'] : '-')." from ".(!empty($torderpropertyroles->SigningZipCode) ? $torderpropertyroles->SigningZipCode : '-' )."<br/>";
              $Notes .= "County is changed to ".(!empty($ClosingDetails['ClosingCountyName']) ? $ClosingDetails['ClosingCountyName'] : '-')." from ".(!empty($torderpropertyroles->SigningCountyName) ? $torderpropertyroles->SigningCountyName : '-' )."<br/>";         
          }
       
      }

       //Update Address
      if(!empty($address))
      {
          $address = array_filter($address);
          $this->db->where(['Id' => $BorrowerUID, 'OrderUID' => $OrderUID ]);
          $this->db->update('torderpropertyroles', $address);   
      }
      
      //Update notes
      if(!empty($Notes))
      {
        $SectionUID = $this->common_model->GetNoteTypeUID("System Note")->SectionUID;
        $LoggedDetails = $this->common_model->getLoggedDetails();

        $Notes .= "Assigned On: ". date('m/d/Y h:i A') ." <br>";
       // $Notes .= "Assigned By: ". $LoggedDetails->UserName ." <br>";

        $this->common_model->insertordernotes($OrderUID, $SectionUID, $Notes);       
      }
    }
  }

  function getApiScheduleUID($OrderUID,$BorrowerUID,$ClosingScheduledDatetime,$ClosingZipcode)
  {
      $this->db->select('ApiOrderScheduleUID');
      $this->db->from('tApiOrderSchedule');
      $this->db->where('OrderUID', $OrderUID);
      $this->db->where("REPLACE(`BorrowerUID`, ' ','') = '$BorrowerUID'", NULL, FALSE);
      $this->db->where('ClosingScheduledDatetime', $ClosingScheduledDatetime);
      $this->db->where('ClosingZipcode', $ClosingZipcode);

      return $this->db->get()->row()->ApiOrderScheduleUID;
  }


  /* @purpose: Check for Signing Address with Property Address @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: June 8th 2020 */
  function CheckSigningAddress($post){
    
    $OrderUID = $post['OrderUID'];
    $BorrowerUID = $post['BorrowerUID'];
    
    $this->db->select('*')->from('torders')->where('OrderUID', $OrderUID);
    $torders = $this->db->get()->row();

    if($torders){
      /* Property Address */
      $PropertyAddress1 = $torders->PropertyAddress1;
      $PropertyAddress2 = $torders->PropertyAddress2;
      $PropertyCityName = $torders->PropertyCityName;
      $PropertyCountyName = $torders->PropertyCountyName;
      $PropertyStateCode = $torders->PropertyStateCode;
      $PropertyZipcode = $torders->PropertyZipcode;
      $PropertyAddress = $PropertyAddress1.' '.$PropertyAddress2;

      /* Mailing Addess*/
      $this->db->select('*')->from('torderpropertyroles')->where('OrderUID', $OrderUID)->where('Id', $BorrowerUID);
      $torderpropertyroles = $this->db->get()->row();
      $MailingAddress1 = $torderpropertyroles->MailingAddress1;
      $MailingAddress2 = $torderpropertyroles->MailingAddress2;
      $MailingCityName = $torderpropertyroles->MailingCityName;
      $MailingCountyName = $torderpropertyroles->MailingCountyName;
      $MailingStateCode = $torderpropertyroles->MailingStateCode;
      $MailingZipCode = $torderpropertyroles->MailingZipCode;
      $MailingAddress = $MailingAddress1.' '.$MailingAddress2;

      /*Signing Address */
      $ClosingAddress1 = $post['SigningAddress1'];
      $ClosingAddress2 = $post['SigningAddress2'];
      $ClosingCityName = $post['SigningCityName'];
      $ClosingCountyName = $post['SigningCountyName'];
      $ClosingStateCode = $post['SigningStateCode'];
      $ClosingZipcode = $post['SigningZipCode'];
      $ClosingAddress = $ClosingAddress1.' '.$ClosingAddress2;

      /* Check for Signing with Property Address - if matched enabled property radio button in summary for the particular borrower*/
      if(
        strtolower(preg_replace('/\s+/','',$ClosingZipcode))   == strtolower(preg_replace('/\s+/','',$PropertyZipcode)) &&
        strtolower(preg_replace('/\s+/','',$ClosingStateCode)) == strtolower(preg_replace('/\s+/','',$PropertyStateCode)) &&
        strtolower(preg_replace('/\s+/','',$ClosingCityName))  == strtolower(preg_replace('/\s+/','',$PropertyCityName)) &&
        strtolower(preg_replace('/\s+/','',$ClosingAddress))  == strtolower(preg_replace('/\s+/','',$PropertyAddress)) 
      )
      {
        $address['SigningType']  = 'Property';
        $address['LocationType']  = 'SubjectProperty';
        $address['LocationTypeDesc']  = '';
      }
      /* Check for Signing with Mailing Address for the borrower-if matched enabled mailing radio button in summary for the particular borrower*/
      else if(
        strtolower(preg_replace('/\s+/','',$ClosingZipcode))    == strtolower(preg_replace('/\s+/','',$MailingZipCode)) &&
        strtolower(preg_replace('/\s+/','',$ClosingStateCode))  == strtolower(preg_replace('/\s+/','',$MailingStateCode)) &&
        strtolower(preg_replace('/\s+/','',$ClosingCityName))   == strtolower(preg_replace('/\s+/','',$MailingCityName)) &&
        strtolower(preg_replace('/\s+/','',$ClosingAddress))   == strtolower(preg_replace('/\s+/','',$MailingAddress))  

      ){ 
        $address['SigningType']  = 'Mailing';
        $address['LocationType']  = 'SignerCurrentResidence';
        $address['LocationTypeDesc']  = '';
      }
      else 
      { 
        $address['SigningType']  = 'Other';
        $address['LocationType']  = 'Other';
        $address['LocationTypeDesc']  = 'Other';
      }

      return $address;
    }
  }

  /**
  * @purpose : D2TINT-40 - NACK comment handling for event failure
  * @param   : fieldArray as array
  * @param   : CreatedByAPI as string
  * @param   : EventCode as string 
  * 
  * @author  : D.Samuel Prabhu
  * @since   : 29 July 2020
  **/

  function KeystoneEventFailure($fieldArray,$CreatedByAPI, $EventCode)
  {
    $OrderUID      = $fieldArray['OrderUID'];
    $TransactionID = $fieldArray['TransactionID']; 
    $reasonInfo    = $fieldArray['reasonInfo'];
    $Comment       = $fieldArray['Comment'];
    $InBoundUID    = $fieldArray['InBoundUID'];
  
    if(empty($Comment)){
      $Comment = 'Event failure from Keystone';
    }

   
    $Remarks = 'Action: '.$EventCode.'<br> Comment: '.$Comment.'<br><br>';

    if($reasonInfo['ReasonCode'])
    {
      $Remarks.= 'Reason Code: '.$reasonInfo['ReasonCode'].'<br>'; 
      $Remarks.= 'Reason Type: '.$reasonInfo['ReasonCodeDescription'].'<br>';
      $Remarks.= 'Reason Description: '.$reasonInfo['ReasonCommentText'].'<br>';
    }

    $ReasonCommentText= $reasonInfo['ReasonCommentText'];

    /* Get orders by TransactionID */
    $Orders = $this->db->select('OrderUID')
                   ->from('torders')
                   ->where('CustomerRefNum', $TransactionID)
                   ->get()->result(); 
    if(!empty($Orders))
    {
      /* Insert notes for event to all orders */
      $NoteType   = $this->GetNoteTypeUID('API Note');
      $SectionUID = $NoteType->SectionUID;

      $UserUID    = $this->db->select('*')->from('musers')->where('LoginID', 'isgn')->get()->row()->UserUID;

      /* Insert notes for event to all orders */
      $NoteType   = $this->GetNoteTypeUID('Event Failure');
      $FailureSectionUID = $NoteType->SectionUID;

      foreach ($Orders as $key => $Order) 
      {

        /* Notes Data */
        $insertNotes[] = array(
          'Note'         => $Remarks,
          'SectionUID'   => $SectionUID,
          'OrderUID'     => $Order->OrderUID,
          'RoleType'     => '1,2,3,4,5,6,7,9,11,12',
          'CreatedByAPI' => $CreatedByAPI,
          'CreatedOn'    => date('Y-m-d H:i:s')
        );

        /* Audit trail Data */
        $auditTrail[] = array(
          'UserUID'    => $UserUID,
          'ModuleName' => 'Api Event Failure', 
          'OrderUID'   => $Order->OrderUID,
          'Feature'    => $Order->OrderUID,
          'Content'    => $Remarks,
          'DateTime'   => date('Y-m-d H:i:s')
        ); 

        /* Notes Data */
        $FailureinsertNotes[] = array(
          'Note'         => $ReasonCommentText,
          'EventCode'    => $EventCode,
          'SectionUID'   => $FailureSectionUID,
          'OrderUID'     => $Order->OrderUID,
          'RoleType'     => '1,2,3,4,5,6,7,9,11,12',
          'CreatedByAPI' => $CreatedByAPI,
          'CreatedOn'    => date('Y-m-d H:i:s')
        );   
      }

      $this->db->trans_begin();

      /* Insert bulk records for notes */
      $result = $this->db->insert_batch("tordernotes", $insertNotes);
      $result = $this->db->insert_batch("tordernotes", $FailureinsertNotes);

      if ($this->db->trans_status() === FALSE) 
      {
        $this->db->trans_rollback();
        echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
      }
      else
      {
        $this->db->trans_commit();

        /* Insert bulk records for audit trail */
        $this->db->insert_batch("taudittrail", $auditTrail);

        echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID));
      }      
    }
    else
    {
       echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    }
  }


}?>
