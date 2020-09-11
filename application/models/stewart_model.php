<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Stewart_model extends CI_Model {
	
	function __construct()
	{ 
		parent::__construct();
		$this->load->config('keywords');
    $this->load->model('Common_model');
	}

  function StewartCancelRequest($fieldArray,$CreatedByAPI){

    $TransactionID = $fieldArray['TransactionID'];
    $StatusUID = $fieldArray['Action'];
    $OrderUID = $fieldArray['OrderUID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $SubProductCode = $fieldArray['SubProductCode'];

    $code = $fieldArray['code'];
    $ApiStatusCodeDetails = $this->GetApiStatusCodeDetails($code); 
    $ApiRequestUID = $ApiStatusCodeDetails->ApiRequestUID;
    $RequestDesc = $ApiStatusCodeDetails->RequestDesc;

    $Remarks = 'Cancel Request from Stewart <br> Code: '.$code.'<br> Text: '.$RequestDesc;

    $canceldata['OrderUID']=$OrderUID;
    $canceldata['Remark']='Order Cancelled by API';
    $canceldata['ApprovalFunction']='Order Cancellation';
    $canceldata['ApprovalStatus']=0;
    $canceldata['IsReviewed']=1;
    $canceldata['RaisedDatetime']=date('Y-m-d H:i:s');
    $canceldata['RaisedByAPI']=$CreatedByAPI;
    $approval = $this->db->insert('torderapprovals',$canceldata);

    $source = $this->db->query("SELECT OrderSourceUID FROM tApiOrders WHERE TransactionID = '".$fieldArray['TransactionID']."'")->row();
    $tcan['OrderUID'] = $OrderUID;
    $tcan['Remarks']='Order Cancelled by API';
    $tcan['IsApiOrder']=1;
    $tcan['RequestedBy']= $source->OrderSourceUID;
    $tcan['CancellationRequestTime']= date('Y-m-d H:i:s');
    $result = $this->db->insert('tordercancel',$tcan);

    $NoteType = $this->api_model->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    if($result){
      $cmd = 'Status: Waiting for Approval <br> Remarks:'.$Remarks;
      $note['OrderUID'] = $OrderUID;
      $note['EventCode'] = 'cancel_request'; /* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
      $note['SectionUID'] = $SectionUID;
      $note['InBoundUID'] = $InBoundUID;
      $note['Note'] = $cmd;
      $note['RoleType'] = '1,2,3,4,5,6,7,8';
      $note['CreatedByAPI'] = $CreatedByAPI;
      $note['CreatedOn'] = date('Y-m-d H:i:s',strtotime('now'));
      $res = $this->db->insert('tordernotes',$note);

      /* @desc D2T-1011 Audit Trail Added @author Yagavi G <yagavi.g@avanzegroup.com> @Since Sept 3rd 2020 */
      $audit_msg = 'Status: Waiting for Approval <br> Remarks:'.$Remarks;
      $AuditData = array(
        'UserUID' => $this->common_model->GetIsgnUser(),
        'ModuleName' => 'Order Cancel Request',
        'OrderUID' => $OrderUID,
        'Feature' => $OrderUID,
        'Content' => $audit_msg,
        'DateTime' => date('Y-m-d H:i:s')
      );
      $this->common_model->Audittrail_insert($AuditData);
    }

    if($approval)
    {
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID, 'Action'=>'cancel_complete_confirm', 'SubProductCode'=>$SubProductCode,'TransactionID' =>$TransactionID));
    } else {
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'Action'=>'cancel_complete_error', 'SubProductCode'=>$SubProductCode,'TransactionID' =>$TransactionID));
    }
  }

  function StewartAdditionalInfoReply($fieldArray,$CreatedByAPI){

  	// Customer Delay Release in D2T Side

    $TransactionID = $fieldArray['TransactionID'];
    $StatusUID = $fieldArray['Action'];
    $OrderUID = $fieldArray['OrderUID'];
    $InBoundUID = $fieldArray['InBoundUID'];
    $Comment = $fieldArray['Comment'];
    $code = $fieldArray['code'];
    $SubProductCode = $fieldArray['SubProductCode'];

    $NoteType = $this->api_model->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;
    $StopTime = date('Y-m-d H:i:s');

    $Order = $this->common_model->get_orderdetails($OrderUID);
    $holiday = $this->common_model->GetHolidays(); 
    foreach($holiday as $holiDate)
    {
      $holidays[] = $holiDate->HolidayDate;
    }

    $ApiStatusCodeDetails = $this->GetApiStatusCodeDetails($code); 
    $ApiRequestUID = $ApiStatusCodeDetails->ApiRequestUID;
    $RequestDesc = $ApiStatusCodeDetails->RequestDesc;

    $this->db->select('*')->from('musers');
    $this->db->where('LoginID', 'isgn');
    $query=$this->db->get();
    $UserName=$query->row()->UserName;
    $UserID=$query->row()->UserUID;

    $fieldArray = array(
                        "CompletedBy"=>$UserID,
                        "IsCompleted"=>1,
                        "ApiRequestComment"=>$Comment,
                        "CompletedDate"=>date('Y-m-d H:i:s',strtotime('now')),
                      );
    $this->db->where(array('ApiRequestUID' => $ApiRequestUID, 'OrderUID' => $OrderUID , "IsCompleted"=>0));
    $res = $this->db->update('tApiRequestStatus', $fieldArray);

    if($res)
    {
      $OrderDueDatetime = $this->api_model->OrderCustomerDelay_Add_with_DueDate($OrderUID,$Order->OrderDueDatetime,$holidays); 
      $this->Order_Summary_Model->UpdateOrderDueDateCustomerDelay($OrderUID,$OrderDueDatetime); 

      $Remarks = 'Additional Reply from Stewart <br> Code: '.$code.'<br> Text: '.$RequestDesc.'<br> Comments: '.$Comment;

      $result = $this->api_model->update_stop_delay_time($OrderUID,$StopTime,$Comment,$SectionUID,$CreatedByAPI);
      $additional_info_notes = $this->insert_notes_additional_reply($OrderUID,$StopTime,$Remarks,$SectionUID,$CreatedByAPI,  $InBoundUID);

      if($result)
      {
        $data1['ModuleName']='Customer Delay Stop by API-update';
        $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']=date('y-m-d H:i:s');
        $data1['TableName']='mcustomerdelay';
        $data1['OrderUID']=$OrderUID;
        $data1['UserUID']=$this->session->userdata('UserUID');                        
        $this->common_model->Audittrail_insert($data1);
        echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID, 'Action'=>'additional_info_reply_confirm', 'SubProductCode'=>$SubProductCode,'TransactionID' =>$TransactionID));
      } elseif ($additional_info_notes) {
        echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID, 'Action'=>'additional_info_reply_confirm', 'SubProductCode'=>$SubProductCode,'TransactionID' =>$TransactionID));
      } else{
        echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'Action'=>'additional_info_reply_error', 'SubProductCode'=>$SubProductCode,'TransactionID' =>$TransactionID));
      }
    } else {
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'Action'=>'additional_info_reply_error', 'SubProductCode'=>$SubProductCode,'TransactionID' =>$TransactionID));
    }
  }


  function insert_notes_additional_reply($OrderUID,$StopTime,$StopRemarks,$StopNoteType,$CreatedByAPI, $InBoundUID=null)
  {
    $cus['OrderUID'] = $OrderUID;
    $cus['CustomerDelayStopTime'] = $StopTime;
    $cus['CustomerDelayStopRemarks'] = $StopRemarks;
    $cus['CustomerDelayStopNoteType'] = $StopNoteType;
    $cus['StoppedByUserUID'] = $this->session->userdata('UserUID');
    
    $note['OrderUID'] = $cus['OrderUID'];
    $note['EventCode'] = 'additional_info_reply'; /* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
    $note['InBoundUID'] = $InBoundUID;
    $note['SectionUID'] = $cus['CustomerDelayStopNoteType'];
    $note['RoleType'] = '1,6,7,8';
    $note['Note'] = $cus['CustomerDelayStopRemarks'];
    $note['CreatedByAPI'] = $CreatedByAPI;
    $note['CreatedOn'] = date('Y-m-d H:i:s',strtotime('now'));
    $res = $this->db->insert('tordernotes',$note);

    if($res)
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  function GetApiStatusCodeDetails($RequestCode){
    $this->db->select ( '*' ); 
    $this->db->from ( 'mApiRequest' );
    $this->db->where ('mApiRequest.RequestCode',$RequestCode);
    $query = $this->db->get();
    $res = $query->row();
    return $res;
  }

  function StewartDisputeRequest($fieldArray,$CreatedByAPI,$SectionUID)
  { 
    $TransactionID = $fieldArray['TransactionID'];
    $StatusUID = $fieldArray['StatusUID'];
    $OrderUID = $fieldArray['OrderUID'];
    $InBoundUID = $fieldArray['InBoundUID']; 
    $Comment = $fieldArray['Comment']; 
    $text = $fieldArray['text']; 
    $code = $fieldArray['code']; 
    $dispute_request_number = $fieldArray['dispute_request_number']; 
    $dispute_request_datetime = $fieldArray['dispute_request_datetime']; 

    $ApiStatusCodeDetails = $this->GetApiStatusCodeDetails($code); 
    $ApiRequestUID = $ApiStatusCodeDetails->ApiRequestUID;

    $this->db->select('*')->from('musers');
    $this->db->where('LoginID', 'isgn');
    $query=$this->db->get();
    $UserName=$query->row()->UserName;
    $UserID=$query->row()->UserUID;

    $fieldArray = array(
                        "ApiRequestUID"=>$ApiRequestUID,
                        "OrderUID"=>$OrderUID,
                        "CreatedBy"=>$UserID,
                        "ApiRequestRef"=>$dispute_request_number,
                        "ApiRequestComment"=>$Comment,
                      );
    $res = $this->db->insert('tApiRequestStatus', $fieldArray);

    $Remarks = 'Code :'.$code.'<br> Comments :'.$Comment.'-'.$text.'<br> Dispute Request Number :' .$dispute_request_number .'<br> Dispute Request DateTime :'.$dispute_request_datetime;

    if($SectionUID!='')
    {
        // Exception raise 
        $fieldArray = array(
          "OrderUID"=>$OrderUID,
          "ExceptionUID"=>$SectionUID,
          "Remarks"=>$Remarks,
          "RaisedByAPI"=>$CreatedByAPI,
          "RaisedOn"=> Date('Y-m-d H:i:s',strtotime("now"))
        );
        
       //Change STatus
       $torders = $this->db->get_where('torders', array('OrderUID'=>$OrderUID))->row();
       $ExceptionRaised = $this->config->item('keywords')['Exception Raised']; 
       if($torders->StatusUID == $ExceptionRaised ) {
        $status = array("StatusUID"=>$ExceptionRaised,"IsPrint"=>NULL);
       } else{
        $status = array("StatusUID"=>$ExceptionRaised,"IsPrint"=>NULL,"IsExceptionOldStatusUID"=>$torders->StatusUID);        
       }
       $this->db->trans_begin();
       $this->db->insert('texceptions', $fieldArray);
       $this->db->where(array("torders.OrderUID"=>$OrderUID));
       $this->db->update('torders',$status);

        $insert_notes = array(
            'Note' => $Remarks,
            'SectionUID' => $SectionUID,
            'OrderUID' => $OrderUID,
            "EventCode"=>'dispute_request', /* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
            'InBoundUID' => $InBoundUID,
            'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
            'CreatedByAPI' => $CreatedByAPI,
            'CreatedOn' => date('Y-m-d H:i:s')
          );

       if ($this->db->trans_status() === FALSE)
       {
         $this->db->trans_rollback();
         echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'Action'=>'dispute_request_error','TransactionID' =>$TransactionID));
       }
       else
       {
         $this->db->trans_commit();
         $this->db->insert("tordernotes", $insert_notes);
         echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID, 'Action'=>'dispute_request_confirm','TransactionID' =>$TransactionID));
       } 
    } else {
      echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'Action'=>'dispute_request_error','TransactionID' =>$TransactionID));
    }


    /*$Exception = $this->db->query("SELECT count(1) as raised FROM texceptions WHERE OrderUID = $OrderUID AND IsClear=0 AND OrderUID NOT IN (SELECT count(1) as raised FROM texceptions WHERE OrderUID = $OrderUID AND IsClear=1)")->row();
    $ExRaised = $Exception->raised;
    if($ExRaised==0)
    {
      if($Comment!='' && $SectionUID!='')
      {
          // Exception raise 
          $fieldArray = array(
            "OrderUID"=>$OrderUID,
            "ExceptionUID"=>$SectionUID,
            "Remarks"=>$Comment .'-'.  $text .'-'. $code .'-'. $dispute_request_number .'-'. $dispute_request_datetime,
            "RaisedByAPI"=>$CreatedByAPI,
            "RaisedOn"=> Date('Y-m-d H:i:s',strtotime("now"))
          );
          
         //Change STatus
         $ExceptionRaised = $this->config->item('keywords')['Exception Raised']; 
         $status = array("StatusUID"=>$ExceptionRaised);
         $this->db->trans_begin();
         $this->db->insert('texceptions', $fieldArray);
         $this->db->where(array("torders.OrderUID"=>$OrderUID));
         $this->db->update('torders',$status);

          $insert_notes = array(
              'Note' => $Comment .'-'.  $text .'-'. $code .'-'. $dispute_request_number .'-'. $dispute_request_datetime,
              'SectionUID' => $SectionUID,
              'OrderUID' => $OrderUID,
              'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
              'CreatedByAPI' => $CreatedByAPI,
              'CreatedOn' => date('Y-m-d H:i:s')
            );

         if ($this->db->trans_status() === FALSE)
         {
           $this->db->trans_rollback();
           return json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'Action'=>'dispute_request_error','TransactionID' =>$TransactionID));
         }
         else
         {
           $this->db->trans_commit();
           $this->db->insert("tordernotes", $insert_notes);
           return json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID, 'Action'=>'dispute_request_confirm','TransactionID' =>$TransactionID));
         } 
      } else {
        return json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'Action'=>'dispute_request_error','TransactionID' =>$TransactionID));
      }
    } else {
      return json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID, 'Action'=>'dispute_request_error','TransactionID' =>$TransactionID));
    }*/
  }

  function StewartPastDue($fieldArray,$CreatedByAPI){

  	// Past Due
    $array = array('StatusUID' => 110 );
    $this->db->where(array("torders.OrderUID"=>$OrderUID));
    $result = $this->db->update('torders',$array);

    $canceldata['OrderUID']=$OrderUID;
    $canceldata['Remarks']='Order Cancelled by API';
    $canceldata['RequestedBy']=0;
    $canceldata['ApprovedBy']=0;
    $canceldata['CancelStatus']=1;
    $canceldata['CancellationRequestTime']=date('Y-m-d H:i:s');
    $canceldata['CancellationApproveDeclineTime']=date('Y-m-d H:i:s');

    $this->db->insert('tordercancel', $canceldata);
    /* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
    $notes_data = array('OrderUID'=>$OrderUID,'EventCode'=>'past_due','SectionUID'=>26,'Roletype'=>'1,2,3,4,5,6,7,8,9,11,12','CreatedOn'=> Date('Y-m-d H:i:s',strtotime("now")),'CreatedByUserUID'=>0,'CreatedByAPI' => $CreatedByAPI,'Note'=>'Order Cancelled by API','InBoundUID' =>$InBoundUID);
    $result = $this->db->insert('tordernotes',$notes_data);
    if($result){
      echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID, 'Action'=>'cancel_complete','TransactionID' =>$TransactionID));
    }
  }

  // Dispute PDF Replies 

  function GetOrderDetails($OrderUID)
  {
    if($OrderUID){
      $this->db->select ( '*' ); 
      $this->db->from ( 'torders' );
      $this->db->join ( 'torderlegaldescription', 'torderlegaldescription.OrderUID = torders.OrderUID' , 'left' );
      $this->db->join ( 'torderdocuments', 'torderdocuments.OrderUID = torders.OrderUID' , 'left' );
      $this->db->where ('torders.OrderUID',$OrderUID);
      $this->db->group_by('torderdocuments.OrderUID'); 
      $query = $this->db->get();
      $OrderDetails = $query->row();

      $data = json_encode($OrderDetails);

      return $OrderDetails;
    }
  }

  function CheckApiOrders($OrderUID){
    $this->db->select("*"); 
    $this->db->from('torders');
    $this->db->where(array("torders.APIOrder"=>1,"torders.OrderUID"=>$OrderUID));
    $query = $this->db->get();
    $Is_Api = $query->row();

    return $Is_Api;
  }

  function GetInBoundTransactionDetails($OrderUID){
    $this->db->select("*"); 
    $this->db->from('tApiOrders');
    $this->db->where(array("tApiOrders.OrderUID"=>$OrderUID));
    $query = $this->db->get();
    $data = $query->row();

    return $data;
  }

  function GetSourceName($OrderUID)
  {
    $this->db->select("*");
    $this->db->from('torders');
    $this->db->join('mApiTitlePlatform','torders.OrderSourceUID=mApiTitlePlatform.OrderSourceUID','left');
    $this->db->where(array("torders.OrderUID"=>$OrderUID));       
    $query = $this->db->get();
    return $query->row();
  } 

  function GetFinalReportToAPI($OrderUID)
  {
    if($OrderUID){
      $this->db->select ( 'torders.OrderUID,torders.OrderNumber, mtemplates.TemplateName, mtemplates.TemplateUID' ); 
      $this->db->from ( 'torders' );
      $this->db->join ( 'mtemplates', 'torders.TemplateUID = mtemplates.TemplateUID',"left");
      $this->db->where ('torders.OrderUID',$OrderUID);
      $query = $this->db->get();
      $Result = $query->row();
      return $Result;
    }
  }

  function GetAttachmentToAPI($OrderUID,$DocFileName)
  {
    if($OrderUID){
      $this->db->select ( 'torderdocuments.*, torders.OrderDocsPath, mtemplates.TemplateName' ); 
      $this->db->from ( 'torderdocuments' );
      $this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID',"left");
      $this->db->join ( 'mtemplates', 'torders.TemplateUID = mtemplates.TemplateUID',"left");
      $this->db->where ('torderdocuments.OrderUID',$OrderUID);
      $this->db->where ('torderdocuments.DocumentFileName',$DocFileName);
      $query = $this->db->get();
      $Result = $query->row();
      return $Result;
    }
  }

  function sendPostData($url, $post){
     $APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
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
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }
  }

  function SendDisputeReplyPDF($OrderUID,$DocumentFileName,$TotalFiles){

    $OrderNumbers = $this->GetOrderDetails($OrderUID);
    $OrderNumber = $OrderNumbers->OrderNumber;

    $Is_Api = $this->CheckApiOrders($OrderUID);
    $Details = $this->GetInBoundTransactionDetails($OrderUID);
    $InBoundUID = $Details->InBoundUID;
    $TransactionID = $Details->TransactionID;

    $AppSourceName = $this->GetSourceName($OrderUID); 
    $SourceName = trim($AppSourceName->OrderSourceName);

    $col_name = array();
      $col_val = array();

    if($Is_Api){

      $url_send = $this->config->item("api_url");

      $FinalProductDetails = $this->GetFinalReportToAPI($OrderUID);

      foreach($DocumentFileName as $key=>$value) {
        $Documents = [];

        $DocFileName = $value;
        $OrderDocDetails = $this->GetAttachmentToAPI($OrderUID,$DocFileName);

        $OrderNumber = $OrderDocDetails->OrderNumber;
        $OrderDocsPath = $OrderDocDetails->OrderDocsPath;
        $FileName = $OrderDocDetails->DocumentFileName;
        $TemplateName = $OrderDocDetails->TemplateName;
        $DocumentCreatedDate = $OrderDocDetails->DocumentCreatedDate;
        $text = file_get_contents(FCPATH.$OrderDocsPath.$FileName);
        $Content = base64_encode($text);

        $obj= [];
        $obj['DocumentFileName']=$FileName;
        $obj['TemplateName']=$TemplateName;
        $obj['DocumentCreatedDate']=$DocumentCreatedDate;
        $obj['Content']=$Content;

        array_push($Documents, $obj);

        if($SourceName=='RealEC') {
            array_push($col_name, 'EventCode');
            array_push($col_val, 150);          
        } else if($SourceName=='Stewart') {
            array_push($col_name, 'EventCode');
            array_push($col_val, 'product_reply_report'); 
        }

        array_push($col_name, 'InBoundUID');
        array_push($col_val, $InBoundUID);

        array_push($col_name, 'TransactionID');
        array_push($col_val, $TransactionID);

        array_push($col_name, 'Documents');
        array_push($col_val, $Documents);

        array_push($col_name, 'SourceName');
        array_push($col_val, $SourceName);

        $column_name = $col_name;
        $column_value = $col_val;   

        $data = array_combine($column_name, $column_value);

        $str_data = json_encode($data);

        $result = $this->sendPostData($url_send, $str_data);

        $fieldArray = array(
             "IsMailSend"=> 1,
             "MailSendBy"=>$this->session->userdata('UserUID'),
             "MailSendDateTime"=>date('Y-m-d H:i:s'),
          );
        $this->db->where('OrderUID',$OrderUID);
        $query = $this->db->update('torders',$fieldArray);
      }       
    }

    echo json_encode(array('success'=>1,'message'=>'Exception Cleared','data'=>''));exit;

  }


}?>
