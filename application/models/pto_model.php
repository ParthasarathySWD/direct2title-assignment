<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Pto_model extends CI_Model {
	
	function __construct()
	{ 
		parent::__construct();
		$this->load->config('keywords');
    $this->load->model('common_model');
    $this->load->library('session');
    
	}

  #<---------------ApiPtoCurlFunction---Start------04/16/2019---------->
  //pto curl function
  function apiPto($OrderDetails,$OrderUID)
  {
    $APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
    $OrderDetails['SourceName'] = 'PTO';
    $ApiOutBoundOrderUID = $OrderDetails['ApiOutBoundOrderUID'];
    $OrderSourceName = $OrderDetails['SourceName'];
    //select mApiTitlePlatform data
    $this->db->select("*");
    $this->db->from('mApiTitlePlatform');
    $this->db->where(array("mApiTitlePlatform.OrderSourceName"=>trim($OrderSourceName)));
    $query = $this->db->get();
    $SourceName = $query->row();
    $OrderSourceUID = $SourceName->OrderSourceUID;
    $OrderDetails=json_encode($OrderDetails);
    //get api url from config file
    $url_send = $this->config->item("api_url"); 
      //Curl function to title api  
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url_send,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS =>$OrderDetails,
      CURLOPT_HTTPHEADER => array(
        "authorization: ".$APiAuthKeyDetails->APIAuthKey,
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
      //curl fun is fails
    if ($err) 
    {
    }
    //curl fun is true
    else {
      $SuccessMsg = explode('$', $response);
      $API = $SuccessMsg[0];
      $Action = trim($SuccessMsg[1]);
      $InBoundUID = trim($SuccessMsg[2]);
      $TransactionNO = trim($SuccessMsg[3]);
      $Status = trim($SuccessMsg[4]);
      $PtoOrderId=trim($SuccessMsg[5]);
      //response status is true
      if($Status == "success"){
        //pto event code is order-request
        if($Action === 'order-request'){
          $Notes = 'orderRequest sent- to PTO API';
          $UpdteDetails=array('IsSendtoAPI'=>1,'Status'=>'Accepted','ProviderOrderNumber'=>$PtoOrderId);
          //update success data
          $Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$ApiOutBoundOrderUID,$UpdteDetails);
          return $Result; exit;
        } 
      } 
      //update order failure data failure
      else
      {
        $Notes = 'orderRequest not sent - to PTO API';
        $UpdteData=array('IsSendtoAPI'=>0,'Status'=>'Rejected');
        $Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$ApiOutBoundOrderUID,$UpdteData);
        return $Status ; exit;
      }

    }       
  }
#<---------------ApiPtoCutlFunction---End----------------->

#<---------------insert mApiTitlePlatform---Start----------------->
  function insert_pto_order($OrderUID,$Integration){
    //get order details from comon model
    $OrderDetails = $this->common_model->get_orderdetails($OrderUID);
    $this->db->select("*");
    $this->db->from('mApiTitlePlatform');
    $this->db->where(array("mApiTitlePlatform.OrderSourceName"=>trim($Integration)));
    $query = $this->db->get();
    $SourceName = $query->row();

    $Api_Order = array(
      'OrderUID'=>$OrderUID,
      'OrderNumber'=>$OrderDetails->OrderNumber,
      'OrderSourceUID'=>$SourceName->OrderSourceUID,
      'Status'=>'New',
      'CreatedDateTime'=>Date('Y-m-d H:i:s',strtotime("now")),
      'CreatedBy'=> $this->session->userdata('UserUID'),
      'AcceptedDateTime'=>Date('Y-m-d H:i:s',strtotime("now")),
      'AcceptedBy'=> $SourceName->OrderSourceUID,
    );
    //insert tApiOutBoundOrders 
    $this->db->insert('tApiOutBoundOrders',$Api_Order);
    //get the  insert_id and return to the index function
    $ApiOutBoundOrderUID = $this->db->insert_id();
    return $ApiOutBoundOrderUID;
  }
  #<----------------EndFun----------------->

  #<---------------UpdateReportSend---Start---04/16/2019-------------->
  function UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$ApiOutBoundOrderUID,$UpdteDetails)
  {
    //update tApiOutBoundOrders
    $this->db->where('ApiOutBoundOrderUID', $ApiOutBoundOrderUID);
    $this->db->update('tApiOutBoundOrders', $UpdteDetails);
    //get API Note type
    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

    $insert_notes = array(
      'Note' => $Notes,
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $OrderSourceUID,
      'CreatedOn' => Date('Y-m-d H:i:s',strtotime("now"))
    );       
    //insert api notes in tordernotes
    $this->db->insert("tordernotes", $insert_notes);
    $this->db->select('*')->from('musers');
    $this->db->where('LoginID', 'isgn');
    $query=$this->db->get();
    $UserName=$query->row()->UserName;
    $UserID=$query->row()->UserUID;

    $data1['ModuleName']=$Notes;
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['DateTime']=Date('Y-m-d H:i:s',strtotime("now"));
    $data1['TableName']='tordernotes';
    $data1['OrderUID']=$OrderUID;
    $data1['UserUID']=$this->session->userdata('UserUID'); 
    //insert data to  Audittrail table                      
    $this->Common_model->Audittrail_insert($data1);

    $response = 'success';
    return $response;
  }
#<----------------EndFun----------------->

  function GetNoteTypeUID($SectionName)
  {
    $this->db->select("*");
    $this->db->from('mreportsections');
    $this->db->where(array("mreportsections.SectionName"=>$SectionName));
    $query = $this->db->get();
    return $query->row();
  }

  function GetAbstractorUserUIDByOrderUID($OrderUID){
    $this->db->select("*");
    $this->db->from('mApiTitlePlatform');
    $this->db->where(array("mApiTitlePlatform.OrderSourceName"=>'pto'));
    $query = $this->db->get();
    $SourceName = $query->row();
    $OrderSourceUID = $SourceName->OrderSourceUID;

    $this->db->select_max('ApiOutBoundOrderUID');
    $this->db->from('tApiOutBoundOrders');
    $this->db->where(array("tApiOutBoundOrders.OrderUID"=>$OrderUID,"tApiOutBoundOrders.OrderSourceUID"=>$OrderSourceUID));
    $query = $this->db->get()->row();
    $ApiOutBoundOrderUID = $query->ApiOutBoundOrderUID;
    $ProviderOrderNumber = $this->db->select('ProviderOrderNumber')->from('tApiOutBoundOrders')->where('ApiOutBoundOrderUID',$ApiOutBoundOrderUID)->get()->row();
    $Status = array('New', 'Accepted');

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

      $result = array('AbstractorUID' => $AbstractorUID , 'UserUID' => $UserUID , 'ApiOutBoundOrderUID' => $ApiOutBoundOrderUID, 'torderabstractor' => $torderabstractor ,'ProviderOrderNumber' => $ProviderOrderNumber->ProviderOrderNumber);

      return $result;

    } else {
      return false;
    }

  }

  function InsertAbstractorDetails($OrderUID)
  {
    $Details = $this->GetAbstractorUserUIDByOrderUID($OrderUID);
    if($Details){
      $AbstractorUID = $Details['AbstractorUID'];
      $UserUID = $Details['UserUID'];
      $AbstractorOrderUID = $Details['torderabstractor']->AbstractorOrderUID;
      $ApiOutBoundOrderUID = $Details['ApiOutBoundOrderUID'];

      $torderabstractor=$this->common_model->get_torderabstractor_by_AbstractorOrderUID($AbstractorOrderUID);

      date_default_timezone_set('US/Eastern');
      $this->db->trans_begin();

      $tordabs['DocumentReceived'] = '1';
      $tordabs['AbstractorReceivedDateTime']=date('Y-m-d H:i:s');
      $tordabs['CompletedDateTime']=date('Y-m-d H:i:s');
      $tordabs['IsOrderComplete']=1;
      $tordabs['OrderStatus']=5;
      $this->load->model('Abstractor_Order_Search/Abstractor_Order_Search_model');
      $updates = $this->Abstractor_Order_Search_model->updatetorderabstractor($AbstractorOrderUID, $tordabs);
      if ($this->db->trans_status() === FALSE)
      {
        $this->db->trans_rollback();

      }
      else
      {
        $this->db->trans_commit();
        $NoteType = $this->GetNoteTypeUID('API Note');
        $SectionUID = $NoteType->SectionUID;
        $Notes = 'Completed By API Attorney';
        $InBoundUID = (isset($tordabs['InBoundUID']) ? $tordabs['InBoundUID'] : null);

        $insert_notes = array(
          'Note' => $Notes,
          'EventCode' => 'pto-finalDocument',
          'SectionUID' => $SectionUID,
          'OrderUID' => $OrderUID,
          'InBoundUID' => $InBoundUID,
          'RoleType' => '1,2,3,4,5,6,7,9,11,12',
          'CreatedByAPI' => 'Pto',
          'CreatedOn' => date('Y-m-d H:i:s')
        );       

        $this->db->insert("tordernotes", $insert_notes);

        $this->db->select('*')->from('musers');
        $this->db->where('LoginID', 'isgn');
        $query=$this->db->get();
        $UserName=$query->row()->UserName;
        $UserID=$query->row()->UserUID;

        $data1['ModuleName']='Completed By API Attorny'.'-insert';
        $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']=date('y-m-d H:i:s');
        $data1['TableName']='torderabstractor';
        $data1['OrderUID']=$OrderUID;
        $data1['UserUID']=$UserID;                   
        $res=$this->Audittrail_insert($data1);

      }
    } 
  }

  //cancel order 
  function ptoOrderCancel($OrderDetails,$OrderUID)
  {
    $OrderDetails['SourceName'] = 'PTO';
    //get api url from config file
    $OrderDetails=json_encode($OrderDetails);
    $url_send = $this->config->item("api_url"); 
     $this->db->select("*");
    $this->db->from('mApiTitlePlatform');
    $this->db->where(array("mApiTitlePlatform.OrderSourceName"=> 'PTO'));
    $query = $this->db->get();
    $SourceName = $query->row();
    $OrderSourceUID = $SourceName->OrderSourceUID;

    //Curl function to title api
    $APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();  
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url_send,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS =>$OrderDetails,
      CURLOPT_HTTPHEADER => array(
        "authorization: ".$APiAuthKeyDetails->APIAuthKey,
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    $NoteType = $this->GetNoteTypeUID('API Note');
    $SectionUID = $NoteType->SectionUID;

     $insert_notes = array(
      'Note' => 'Order-Cancelled',
      'SectionUID' => $SectionUID,
      'OrderUID' => $OrderUID,
      'RoleType' => '1,2,3,4,5,6,7,9,11,12',
      'CreatedByAPI' => $OrderSourceUID,
      'CreatedOn' => Date('Y-m-d H:i:s',strtotime("now"))
    );       
    //insert api notes in tordernotes
    $this->db->insert("tordernotes", $insert_notes);

  }

  function Audittrail_insert($data){
    $this->db->insert('taudittrail',$data);
  }
}?>
