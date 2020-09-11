<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Westcor_model extends CI_Model {
	
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

	function GetIsgnUser(){
		$this->db->select('*')->from('musers');
		$this->db->where('LoginID', 'isgn');
		$query=$this->db->get();
		$UserUID=$query->row()->UserUID;
		return $UserUID;
	}

	function clean($string) {
		$find = array("&","<",">");
		$replace = array("&amp;","&lt;","&gt;");
		$res = htmlspecialchars($string);
		$str = html_entity_decode($res);
		$html_str = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $string);
		return $html_str;
	}

	function GetNoteTypeUID($SectionName)
	{
		$this->db->select("*");
		$this->db->from('mreportsections');
		$this->db->where(array("mreportsections.SectionName"=>$SectionName));
		$query = $this->db->get();
		return $query->row();
	}

	function GetNotes($OrderUID)
	{
		$this->db->select("*");
		$this->db->from('tordernotes');
		$this->db->where(array("tordernotes.OrderUID"=>$OrderUID));
		$query = $this->db->get();
		return $query->row();
	}

	function toAddNote($fieldArray,$CreatedByAPI) {
		$this->db->trans_begin();
		$TransactionID = $fieldArray['TransactionID'];
		$StatusUID = $fieldArray['StatusUID'];
		$OrderUID = $fieldArray['OrderUID'];
		$InBoundUID = $fieldArray['InBoundUID']; 
		$Comment = $fieldArray['Comment'];
		/*@author Yagavi G <yagavi.g@avanzegroup.com> @purpose If comments are empty from client; manual comments to be added*/
		if(empty($Comment)){
			$Comment = 'No Comments from Client';
		}
		$OrderUID = $fieldArray['OrderUID'];
		$InBoundUID = $fieldArray['InBoundUID'];

		$NoteType = $this->GetNoteTypeUID('API Note');
		$SectionUID = $NoteType->SectionUID;

		$insert_notes = array(
			'Note' => $Comment,
			'SectionUID' => $SectionUID,
			'EventCode' => 'AT05',
			'OrderUID' => $OrderUID,
			'RoleType' => '1,2,3,4,5,6,7,9,11,12',
			'CreatedByAPI' => $CreatedByAPI,
			'CreatedOn' => date('Y-m-d H:i:s')
		);
		$this->db->insert("tordernotes", $insert_notes);

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			echo json_encode( array('status' => 'failed','InBoundUID' =>'', 'message'=>'Error'));
		}
		else
		{
			$this->db->trans_commit();
			echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'Add Note Inserted in an Organization'));
		} 
	}

	function toCancelOrder($DataArray,$CreatedByAPI) { 
		$TransactionID = $DataArray['TransactionID'];
		$StatusUID = $DataArray['StatusUID'];
		$OrderUID = $DataArray['OrderUID'];
		$InBoundUID = $DataArray['InBoundUID']; 
		$Comment = $DataArray['Comment'];
		if(empty($Comment)){
			$Comment = 'No Comments from Client';
		}

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
			$Notes = 'Order Cancellation Request from Westocr (Moved to Client Request) <br>'.'Action: AT07 <br> Comment: '.$Comment;
			$this->Real_ec_model->AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, 'AT07');

			$AuditData = array(
				'UserUID' => $this->api_model->GetIsgnUser(),
				'ModuleName' => 'Cancel Request',
				'OrderUID' => $OrderUID,
				'Feature' => $OrderUID,
				'IpAddreess' => $_SERVER['REMOTE_ADDR'],
				'Content' => 'Cancel Request from Westocr [Moved to Client Request].<br>Comment: '.$Comment,
				'DateTime' => date('Y-m-d H:i:s')
			);
			$this->common_model->Audittrail_insert($AuditData);

			echo json_encode(array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'AT07 Event Inserted in Isgn Organization'));
		} else {
			echo json_encode(array('status' => 'failed','InBoundUID' =>$InBoundUID));
		}
	}

	function toSuspendOrder($fieldArray,$CreatedByAPI){
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

		$NoteType = $this->api_model->GetNoteTypeUID('API Note');
		$SectionUID = $NoteType->SectionUID;
		$StartTime = date('Y-m-d H:i:s',strtotime('now'));

		$this->api_model->insert_start_delay_time($OrderUID,$StartTime,$Comment,$SectionUID,$CreatedByAPI);

		$AuditData = array(
			'UserUID' => $this->api_model->GetIsgnUser(),
			'ModuleName' => 'Client Delay',
			'OrderUID' => $OrderUID,
			'Feature' => $OrderUID,
			'IpAddreess' => $_SERVER['REMOTE_ADDR'],
			'Content' => 'Client Delay Start by Westocr. <br>Comment: '.$Comment,
			'DateTime' => date('Y-m-d H:i:s')
		);
		$this->common_model->Audittrail_insert($AuditData);

		$NoteData = '';
		$Notes = 'Order On-Hold from Westocr - Starts Client Delay <br>'.'Action: AT06 <br> Comments: '.$Comment;
		$this->AddNotes($Notes, $OrderUID, $CreatedByAPI, 'AT06');

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
		}else{
			$this->db->trans_commit();
			echo json_encode(array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'AT06 Event Inserted in Isgn Organization'));
		}    
	}

	function toResumeOrder($fieldArray,$CreatedByAPI){
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
			$Notes = 'Order Resumed from Westocr - Stopped Client Delay <br>'.'Action: AT08'.'<br> Comment: '.$Comment;
			$this->Real_ec_model->AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, 'AT08');

			$AuditData = array(
				'UserUID' => $this->api_model->GetIsgnUser(),
				'ModuleName' => 'Client Delay Stops',
				'OrderUID' => $OrderUID,
				'Feature' => $OrderUID,
				'IpAddreess' => $_SERVER['REMOTE_ADDR'],
				'Content' => 'Client Delay Stops by Westocr. <br>Comment: '.$Comment,
				'DateTime' => date('Y-m-d H:i:s')
			);
			$this->common_model->Audittrail_insert($AuditData);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
		}else{
			$this->db->trans_commit();
			echo json_encode(array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'AT08 Event Inserted in Isgn Organization'));
		}    
	}

	function GetOrderSourceName($OrderSourceUID){
		$this->db->select("*");
		$this->db->from('mApiTitlePlatform');
		$this->db->where(array("mApiTitlePlatform.OrderSourceUID"=>$OrderSourceUID));     
		$query = $this->db->get();
		return $query->row();
	}

	function GetUserName($UserUID){
		$this->db->select("*");
		$this->db->from('musers');
		$this->db->where(array("musers.UserUID"=>$UserUID));     
		$query = $this->db->get();
		return $query->row();
	}

	// Checking whether the order is API Order or Not //
	
	function CheckApiOrders($OrderUID){
		$this->db->select("*"); 
		$this->db->from('torders');
		$this->db->join ( 'msubproducts', 'torders.SubProductUID = msubproducts.SubProductUID' , 'left' );
		$this->db->where(array("torders.APIOrder"=>1,"torders.OrderUID"=>$OrderUID));
		$query = $this->db->get();
		$Is_Api = $query->row();

		return $Is_Api;
	}

	// Checking Inbound Transaction Details //

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

	function AddNotes($Notes, $OrderUID, $CreatedByAPI, $EventCode){

		$NoteType = $this->GetNoteTypeUID('API Note');
		$SectionUID = $NoteType->SectionUID;

		$insert_notes = array(
			'Note' => $Notes,
			'EventCode' => $EventCode,
			'SectionUID' => $SectionUID,
			'OrderUID' => $OrderUID,
			'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
			'CreatedByAPI' => $CreatedByAPI,
			'CreatedOn' => date('Y-m-d H:i:s')
		);

		$result = $this->db->insert("tordernotes", $insert_notes);
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

	function GetApiStatusCodeDetails($RequestCode){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mApiRequest' );
		$this->db->where ('mApiRequest.RequestCode',$RequestCode);
		$query = $this->db->get();
		$res = $query->row();
		return $res;
	}
}?>
