<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Notary_model extends CI_Model {
  
  function __construct()
  { 
    parent::__construct();
    $this->load->config('keywords');
    $this->load->model('Common_model');
    $UserUID = $this->session->userdata('UserUID');
  }

  /************************************************** D2T to API Functions *******************************************************/

  function GetLocalTimeZoneByStateCode($ClosingInsertData,$post){
    $SigningDate = date("m/d/Y", strtotime($post['SigningDate']));
    $SigningTime = date("H:i:s", strtotime($post['SigningTime']));
    $SigningDateTime = date('m/d/Y H:i:s', strtotime($SigningDate.' '.$SigningTime));

    $this->db->select ('*'); 
    $this->db->from ('mstates');
    $this->db->where(array('mstates.StateCode'=>$ClosingInsertData['SigningStateCode']));
    $query = $this->db->get();
    $mstates = $query->row();
    if($mstates){
      $TimeZone = $mstates->TimeZone;
      $this->db->select('*')->from('mTimeZones')->where(array('mTimeZones.TimeZoneName'=>$TimeZone));
      $query = $this->db->get();
      $mTimeZones = $query->row();
      $PHP_TimeZone_Name = $mTimeZones->PHP_TimeZone_Name;

      date_default_timezone_set($PHP_TimeZone_Name);
      date_default_timezone_get();
      $ConvertedDateTime = date(DATE_W3C,strtotime($SigningDateTime));
      $LocalTimeZoneDateTime = date('m/d/Y H:i:s', strtotime($ConvertedDateTime));
      $res = array('LocalTimeZoneDateTime' => $LocalTimeZoneDateTime, 'TimeZone' => $PHP_TimeZone_Name );
      return $res;
    }
  }

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

  function GetAbstractorDetails($AbstractorUID){
    $this->db->select('*')->from('mabstractor');
    $this->db->where('AbstractorUID', $AbstractorUID);
    return $this->db->get()->row();
  }

  function insert_api_orders($OrderUID,$SourceName,$OrderDetails,$abstractor_details)
  {
    $this->db->select("*");
    $this->db->from('mApiTitlePlatform');
    $this->db->where(array("mApiTitlePlatform.OrderSourceName"=>$SourceName));
    $query = $this->db->get();
    $SourceName = $query->row();

    $Api_Order = array(
      'AbstractorUID'=>$abstractor_details['AbstractorUID'],
      'AbstractorOrderUID'=>$abstractor_details['AbstractorOrderUID'],
      'OrderUID'=>$OrderUID,
      'OrderNumber'=>$OrderDetails->OrderNumber,
      'OrderSourceUID'=>$SourceName->OrderSourceUID,
      'OrderTypeUID'=>$OrderDetails->OrderTypeUID,
      'Status'=>'New',
      'CreatedDateTime'=>Date('Y-m-d H:i:s',strtotime("now")),
      'CreatedBy'=> $this->session->userdata('UserUID')
    );

    $this->db->insert('tApiOutBoundOrders',$Api_Order); 
    $ApiOutBoundOrderUID = $this->db->insert_id();
    return $ApiOutBoundOrderUID;
  }

  function get_ScheduleBorrowers($ScheduleUID)
  {
    $this->db->select('BorrowerUID');
    $this->db->where('ScheduleUID', $ScheduleUID);
    $this->db->from('tOrderScheduleBorrower');
    $result = $this->db->get()->result_array(); 
    return array_column($result, 'BorrowerUID');
  }

  function GetBorrowerByID($OrderUID,$BorrowerID)
  {
    $BorrowerID = implode(',', $BorrowerID);
    $query = $this->db->query("SELECT * FROM torderpropertyroles WHERE PropertyRoleUID = 5 and OrderUID = '$OrderUID' and Id IN (".$BorrowerID.")");
    return $query->result();
  }

  function sendPostData($url, $post, $OrgCode, $OrderUID)
  {
    $Orders = json_decode($post);
    $OrderSourceName = $Orders->SourceName;
    $Documents = $Orders->Documents;
    $DocumentFileSend = $Orders->DocumentFileSend;
    $ApiOutBoundOrderUID = $Orders->ApiOutBoundOrderUID;
    if(in_array($Orders->EventCode, array('CancelOrder','UpdateOrder')))
    {
      $this->db->where('ApiOutBoundOrderUID',$ApiOutBoundOrderUID);
      $rows = $this->db->get('tApiOutBoundOrders')->row();
      $Orders->ProviderOrderNumber = $rows->ProviderOrderNumber;
    }
    $post = json_encode($Orders);

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
      // echo '<pre>';print_r($response);exit;

      $SuccessMsg = explode('$', $response);
      $API = $SuccessMsg[0];
      $Action = trim($SuccessMsg[1]);
      $InBoundUID = trim($SuccessMsg[2]);
      $TransactionNO = trim($SuccessMsg[3]);
      $Status = trim($SuccessMsg[4]);
      $OrderID = trim($SuccessMsg[5]);
      $Message = trim($SuccessMsg[6]);
      $Notes = '';
      if($Action === 'PlaceOrder') {
        $Notes.= 'PlaceOrder is sent to NotaryGo API<br>';
        if($Message){
          $Notes.= 'OrderID: '.$OrderID.'<br>';
          $Notes.= 'Message: '.$Message;
        }
        if($Status == 'Success'){
          $res['ProviderOrderNumber'] = $OrderID;
          $res['Status'] = 'Accepted';
        } else {
          $res['Status'] = 'Rejected';
        }
        $res['IsSendtoAPI'] = 1;
        $this->db->where('ApiOutBoundOrderUID',$ApiOutBoundOrderUID);
        $this->db->update('tApiOutBoundOrders',$res);
        $Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$ApiOutBoundOrderUID,$Message);
      } else if($Action === 'CancelOrder') {
        $Notes.= 'CancelOrder is sent to NotaryGo API<br>';
        if($Message){
          $Notes.= 'OrderID: '.$OrderID.'<br>';
          $Notes.= 'Message: '.$Message;
        }
        if($Status == 'Success' && $Message == 'ORDER HAS BEEN CANCELED') {
          //$res['ProviderOrderNumber'] = $OrderID;
          $res['Status'] = 'Cancelled';
          $res['IsSendtoAPI'] = 1;
          $this->db->where('ApiOutBoundOrderUID',$ApiOutBoundOrderUID);
          $this->db->update('tApiOutBoundOrders',$res);
        }
        $Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,'',$Message);
      } else if($Action === 'OrderStatus'){        
        $Notes.= 'OrderStatus is sent to NotaryGo API<br>';
        if($Message){
          $Notes.= 'Message: '.$Message;
        }
        $Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,'',$Message);
      } else if($Action === 'UpdateOrder'){
        $Notes.= 'OrderUpdate is sent to NotaryGo API<br>';
        if($Message){
          $Notes.= 'Message: '.$Message;
        }
        $Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,'',$Message);
      } else if($Action === 'Document'){
        $Notes.= $DocumentFileSend.' - Document is sent to NotaryGo API<br>';
        if($Message){
          $Notes.= 'Message: '.$Message;
        }
        $Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,'',$Message);
      }

      return $Result;
    }
  }

  function UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$ApiOutBoundOrderUID='',$Message)
  {
    if(!empty($ApiOutBoundOrderUID))
    {
      $Ack_Insert['IsSendtoAPI'] = 1;
      $this->db->where('ApiOutBoundOrderUID', $ApiOutBoundOrderUID);
      $this->db->update('tApiOutBoundOrders', $Ack_Insert);
    }

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Notes,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
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
    $this->common_model->Audittrail_insert($data1);

    $response = $Message;
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
    
  function AddNotes($Notes, $OrderUID, $CreatedByAPI){

    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Notes,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
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

  function GetAbstractorForNotaryGo($OrderUID,$AbstractorUID,$AbstractorOrderUID){
    $this->db->select ( '*' );
    $this->db->from ( 'tApiOutBoundOrders' );
    $this->db->join ( 'torders', 'torders.OrderUID = tApiOutBoundOrders.OrderUID' );
    $this->db->join ( 'mApiTitlePlatform', 'mApiTitlePlatform.OrderSourceUID = tApiOutBoundOrders.OrderSourceUID' , 'left' );
    $this->db->where(array("tApiOutBoundOrders.OrderUID"=>$OrderUID,"tApiOutBoundOrders.AbstractorOrderUID"=> $AbstractorOrderUID,"mApiTitlePlatform.OrderSourceName"=> "Notary"));
    $query = $this->db->get();
    $tApiOutBoundOrders =  $query->row();
    return $tApiOutBoundOrders;
  }

  /* Start - Notary Go Changes*/

  function CheckAPINotary($AbstractorUID){
    $this->db->select ( '*' );
    $this->db->from ( 'mabstractor' );
    $this->db->join ( 'mApiTitlePlatform', 'mApiTitlePlatform.OrderSourceUID = mabstractor.NotaryOrderSourceUID' , 'left' );
    $this->db->where(array("mabstractor.AbstractorUID"=>$AbstractorUID,"mApiTitlePlatform.OrderSourceName"=> "Notary"));
    $query = $this->db->get();
    return $query->row()->IsAPINotary;
  }

  function GettOrderScheduleDetails($ScheduleUID){
    $this->db->select ( '*' );
    $this->db->from ( 'tOrderSchedule' );
    $this->db->join ( 'torders', 'torders.OrderUID = tOrderSchedule.OrderUID' );
    $this->db->where(array("tOrderSchedule.ScheduleUID"=>$ScheduleUID));
    $query = $this->db->get();
    return $query->row();
  }

  function GetAbstractorByAbstractorOrderUID($AbstractorOrderUID){
    $this->db->select ( '*' );
    $this->db->from ( 'torderabstractor' );
    $this->db->where(array("torderabstractor.AbstractorOrderUID"=>$AbstractorOrderUID));
    $query = $this->db->get();
    return $query->row();
  }

  function GettOrderClosingDetails($ScheduleUID){
    $this->db->select ( '*' );
    $this->db->from ( 'tOrderClosing' );
    $this->db->where(array("tOrderClosing.ScheduleUID"=>$ScheduleUID));
    $query = $this->db->get();
    return $query->row();
  }

  function UpdateRealECNotaryInformation($OrderUID, $ScheduleUID){

    /* Starts -- Update Order to Notary Go */
    $this->load->model('schedule/Schedule_model');
    $Borrowers = $this->Schedule_model->getScheduleBorrowers($ScheduleUID);
    $tOrderSchedule = $this->GettOrderScheduleDetails($ScheduleUID);
    $tOrderScheduleClosing = $this->GettOrderClosingDetails($ScheduleUID);

    $ClosingInsertData = array(
      'OrderUID'=>$OrderUID,
      'ScheduleUID'=>$ScheduleUID,
      'SigningAddress1'=>$tOrderScheduleClosing->SigningAddress1,
      'SigningAddress2'=>$tOrderScheduleClosing->SigningAddress2,
      'SigningZipCode'=>$tOrderScheduleClosing->SigningZipCode,
      'SigningCityName'=>$tOrderScheduleClosing->SigningCityName,
      'SigningCountyName'=>$tOrderScheduleClosing->SigningCountyName,
      'SigningStateCode'=>$tOrderScheduleClosing->SigningStateCode,
      'SigningAddressNotes'=>$tOrderScheduleClosing->SigningAddressNotes,
      'SigningDateTime'=>$tOrderScheduleClosing->SigningDateTime,
    );
    
    $SigningDateTime = $tOrderSchedule->SigningDateTime;
    $SigningDate = date('Y-m-d' , strtotime($tOrderSchedule->SigningDateTime));
    $SigningTime = date('H:i:s' , strtotime($tOrderSchedule->SigningDateTime));

    $mabstractor_details = $this->GetAbstractorByAbstractorOrderUID($tOrderSchedule->AbstractorOrderUID);
    $IsAPINotary = $this->CheckAPINotary($mabstractor_details->AbstractorUID);
    if($IsAPINotary == 1){
      $AbstractorUID = $mabstractor_details->AbstractorUID;
      $AbstractorOrderUID = $mabstractor_details->AbstractorOrderUID;      
      $OrderDetails = $this->common_model->get_orderdetails($OrderUID);

      $this->db->select('*')->from('musers');
      $this->db->where('LoginID', 'isgn');
      $query=$this->db->get();
      $UserName=$query->row()->UserName;
      $UserID=$query->row()->UserUID;

      $UserDetails = $this->common_model->GetUserDetailsByUser($UserID);
      $UserName = $UserDetails->UserName;
      $abstractor_details['AbstractorUID'] = $AbstractorUID;
      $abstractor_details['AbstractorOrderUID'] = $AbstractorOrderUID;
      $tApiOutBoundOrders = $this->GetAbstractorForNotaryGo($OrderUID,$AbstractorUID,$AbstractorOrderUID);
      $url_send = $this->config->item("api_url");
      $OrgCode = 'isgn';
      $data = array(
        'ApiOutBoundOrderUID' => $tApiOutBoundOrders->ApiOutBoundOrderUID,
        'ScheduleID' => $ScheduleUID,
        'OrderUID' => $OrderUID,
        'UserName' => $UserName,
        'BorrowersList' => $Borrowers,
        'OrderDetails' => $OrderDetails,
        'SigningAddr' => $ClosingInsertData,
        'SigningDate' => $SigningDate,
        'SigningTime' => $SigningTime,
        'Documents' => '',
        'EventCode' => 'UpdateOrder',
        'SourceName'=>'Notary'
      );

      $str_data = json_encode($data);
      $this->Notary_model->sendPostData($url_send,$str_data,$OrgCode,$OrderUID);
      /* Ends -- Update Order to Notary Go */
    }
  }

  /* End - Notary Go Changes*/

  /**
  * @description Function to check the status of the signing agent and estimated fee
  * @param AbstractorOrderUID
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @since 13th May 2020
  *
  */ 
  function CheckAgentEstimatedFeeStatus($AbstractorOrderUID){

    $this->db->select('tOrderSchedule.ScheduleUID, torderabstractor.AbstractorUID, torderabstractor.OrderUID, torderabstractor.AbstractorOrderUID, mabstractorfee.Fee, tOrderSchedule.SigningAgent, tOrderSchedule.EstimateFee', false);
    $this->db->from('torderabstractor'); 
    $this->db->join('tOrderSchedule', 'tOrderSchedule.AbstractorOrderUID = torderabstractor.AbstractorOrderUID');
    $this->db->join('mabstractorfee', 'mabstractorfee.AbstractorPricingProductUID = torderabstractor.AbstractorPricingProductUID');
    $this->db->where('torderabstractor.AbstractorOrderUID', $AbstractorOrderUID);
    $tOrderSchedule = $this->db->get()->row();

    if(!empty($tOrderSchedule)){
      $AbstractorUID = $tOrderSchedule->AbstractorUID;

      /* Estimated Fee Calculation */
      $ActualFee = $tOrderSchedule->Fee;
      $EstimateFee = $tOrderSchedule->EstimateFee;
      $FinalFee = '-';
      $IsFeeExceed = 0;
      if(!empty($EstimateFee)){
        $FinalFee = '<strong>$'.$EstimateFee.'</strong>';
        if($EstimateFee > $ActualFee){
          $IsFeeExceed = 1;
          $FinalFee = '<strong class="notary-sparkle" title="Fee Exceeded" data-actualfee= "'.$ActualFee.'">$'.$EstimateFee.'</strong>';
        }
      }

      /* Check Agent Status */
      $SigningAgent = trim($tOrderSchedule->SigningAgent);
      $AgentStatus = '';
      $IsAgentProhibited = 0;
      if(!empty($SigningAgent)){
        $AgentStatus = '<strong> ( '.$SigningAgent.' ) </strong>';

        //$ContactDetails = $this->GetVendorContactDetails($AbstractorUID);
        $ContactDetails = $this->GetVendorContactDetails($SigningAgent);
        if(!empty($ContactDetails)){
          $UserNames = [];
          foreach ($ContactDetails as $key => $value) {
            $UserNames[] = $value->UserName;
          }

          if(in_array($SigningAgent, $UserNames) == TRUE) {
            $this->db->select('*');
            $this->db->from('mabstractorcontact'); 
            $this->db->join('musers', 'musers.UserName = mabstractorcontact.ContactName');
            $this->db->where('musers.UserName', $SigningAgent);
            $this->db->where('mabstractorcontact.Status', 'Prohibited');
            $mabstractorcontact = $this->db->get()->row();

            if(!empty($mabstractorcontact))
            {
              $IsAgentProhibited = 1;
              $AgentStatus = '<strong class="notary-sparkle" title="Agent Prohibited"> ( '.$SigningAgent.' ) </strong>';
              $ContactStatus = 'Prohibited';
            }
            else
            { 
              /* To check the contact status @author: D.Samuel Prabhu @since: 01 Aug 2020 */
              $this->db->select('mabstractorcontact.AbstractorContactUID, ContactName, mabstractorcontact.Status');
              $this->db->from('mabstractorcontact');              
              $this->db->where('mabstractorcontact.ContactName', $SigningAgent);
              $this->db->where('mabstractorcontact.AbstractorUID', $AbstractorUID);
              $this->db->where('mabstractorcontact.Status !=', 'Prohibited');
              $abstractorContact = $this->db->get()->row();

              $ContactStatus = $abstractorContact->Status;

              /* if status is active then save vendor individual contact */
              if(strtolower($abstractorContact->Status)=='active')
              {
                //Adding individual contact
                $result = $this->SaveVendorIndividualContact($abstractorContact->AbstractorContactUID, $AbstractorOrderUID);
                
                if($result['success']==1)
                {
                  $AgentStatus = '<strong class="badge badge-success" title="Agent Active"> ('.$SigningAgent.' ) </strong>';
                } 
                else
                {
                   $AgentStatus = '<strong class="badge badge-danger" title="Agent Active"> ('.$SigningAgent.' ) </strong>';
                }            
                
              } /* End if save vendor individual contact */
              else if(strtolower($abstractorContact->Status)=='new')
              {
                 $AgentStatus = '<strong class="badge badge-primary" title="Agent New"> ('.$SigningAgent.' ) </strong>';
              }
            }
          }
        } else {
          $ContactStatus = 'New';
        }
      }

      $result = array('EstimateFee' => $FinalFee, 'SigningAgent' => $AgentStatus, 'ActualFee' => $ActualFee, 'NotaryFee' => $EstimateFee, 'IsFeeExceed' => $IsFeeExceed, 'IsAgentProhibited' => $IsAgentProhibited, 'ContactStatus'=> $ContactStatus);
      return $result;
    }
  }

  /* @purpose: To get additional contacts for vendor  @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: 14th May 2020*/
  function GetVendorContactDetails_Old($VendorUID){
    $this->db->select("*, 
      mabstractorcontact.ContactName AS ContactName,
      mabstractorcontact.DesignationUID AS Designation, 
      mabstractorcontact.Department AS Department,
      mabstractorcontact.OfficeNo AS OfficeNo,
      mabstractorcontact.Email AS ContactEmail,
      musers.LoginID AS LoginID, 
      musers.UserName AS UserName
      ");
    $this->db->from('mabstractor');
    $this->db->join('mabstractorcontact','mabstractorcontact.AbstractorUID = mabstractor.AbstractorUID','inner'); 
    $this->db->join('musers','musers.UserUID = mabstractorcontact.UserUID AND musers.IsVendorPrimaryLogin != 1','left');
    $this->db->where(array("mabstractor.AbstractorUID"=>$VendorUID));  
    $this->db->where("musers.IsVendorPrimaryLogin !=",1); 
    $query = $this->db->get();
    $ContactsArr = $query->result();
    $ContactsColumn = array_column($query->result(),'UserUID');

    $this->db->select("musers.UserUID,LoginID,UserName");
    $this->db->from('musers');
    $this->db->join('mabstractor','mabstractor.AbstractorUID = musers.AbstractorUID','left'); 
    $this->db->where("musers.IsVendorPrimaryLogin !=",1);    
    $this->db->where(array("mabstractor.AbstractorUID"=>$VendorUID,'musers.Active'=>1));  
    if(!empty($ContactsColumn)){
      $this->db->where_not_in('musers.UserUID',$ContactsColumn);  
    } 
    $query = $this->db->get();

    $UsersArr = $query->result();
    $Datas = array_merge($ContactsArr,$UsersArr);
    return $Datas;
  }

  function GetVendorContactDetails($UserName){
    $this->db->select("musers.UserUID,LoginID,UserName");
    $this->db->from('musers');
    $this->db->where(array('musers.Active'=>1));  
    $this->db->where(array('musers.UserName'=>$UserName));  
    $query = $this->db->get();
    $Datas = $query->result();
    return $Datas;
  }


  /**
  * @purpose : To save vendor individual contact
  * @author  : D.Samuel Prabhu
  * @since   : 01 Aug 2020
  * @param   : VendorIndividualContactUID - AbstractorContactUID from mabstractorcontact table for the order
  * @param   : AbstractorOrderUID         - from torderabstractor table for the order
  *
  * @return  bool - true/false
  **/
  function SaveVendorIndividualContact($VendorIndividualContactUID, $AbstractorOrderUID)
  {

    $ch = curl_init();
    $curlConfig = array(
      CURLOPT_URL            => base_url('order_search/save_vendorindividualcontact/'),
      CURLOPT_POST           => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POSTFIELDS     => array(
        'vendorindividualcontactuid' => $VendorIndividualContactUID,
        'AbstractorOrderUID' => $AbstractorOrderUID,
      )
    );
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    curl_close($ch);

    if(!empty($response))
    {
      return json_decode($response,true);
    }
  }

}?>
