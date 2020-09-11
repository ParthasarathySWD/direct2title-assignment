<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Loanspq_model extends CI_Model {

	function __construct()
	{ 
		parent::__construct();
		$this->load->config('keywords');
		$UserUID = $this->session->userdata('UserUID');
	}

	function LoansPQCancelRequest($OrderUID,$Remarks){

		$OrderDetails = $this->common_model->get_orderdetails($OrderUID);
		$OrderSourceUID = $OrderDetails->OrderSourceUID;

		$this->db->select("*");
		$this->db->from('mApiTitlePlatform');
		$this->db->where(array("mApiTitlePlatform.OrderSourceUID"=>$OrderSourceUID));
		$query = $this->db->get();
		$SourceName = $query->row();
		$CreatedByAPI = $SourceName->OrderSourceUID;
		$OrderSourceName = $SourceName->OrderSourceName;

		$this->db->trans_begin();

		$Remarks = 'Cancel Request from LoansPQ - '. $Remarks;
		$canceldata['OrderUID']=$OrderUID;
		$canceldata['Remark']='Order Cancelled by API - LoansPQ';
		$canceldata['ApprovalFunction']='Order Cancellation';
		$canceldata['ApprovalStatus']=0;
		$canceldata['IsReviewed']=1;
		$canceldata['RaisedDatetime']=date('Y-m-d H:i:s');
		$canceldata['RaisedByAPI']=$CreatedByAPI;
		$approval = $this->db->insert('torderapprovals',$canceldata);

		$tcan['OrderUID'] = $OrderUID;
		$tcan['Remarks']='Order Cancelled by API';
		$tcan['IsApiOrder']=1;
		$tcan['RequestedBy']= $CreatedByAPI;
		$tcan['CancellationRequestTime']= date('Y-m-d H:i:s');
		$result = $this->db->insert('tordercancel',$tcan);

		$NoteType = $this->GetNoteTypeUID('API Note');
		$SectionUID = $NoteType->SectionUID;
		$InBoundUID = (isset($NoteType->InBoundUID) ? $NoteType->InBoundUID:null);

		$note['OrderUID'] = $OrderUID;
		$note['SectionUID'] = $SectionUID;
		$note['InBoundUID'] = $InBoundUID;
		$note['Note'] = $Remarks;
		$note['RoleType'] = '1,2,3,4,5,6,7,8';
		$note['CreatedByAPI'] = $CreatedByAPI;
		$note['CreatedOn'] = date('Y-m-d H:i:s',strtotime('now'));
		$res = $this->db->insert('tordernotes',$note);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();
			return array('status' => 'failed', 'Action'=>'cancel_complete_error', 'Msg'=>'Error');
		}else{
			$this->db->trans_commit();
			return array('status' => 'success', 'Action'=>'cancel_complete_confirm', 'Msg'=>'Cancel Request is sent successfully');
		}
	}

	function LoansPQCustomerDelay($DataArray){ 

		$OrderUID = $DataArray['OrderUID'];
		$InBoundUID = $DataArray['InBoundUID'];

		$OrderDetails = $this->common_model->get_orderdetails($OrderUID);
		$OrderSourceUID = $OrderDetails->OrderSourceUID;

		$this->db->select("*");
		$this->db->from('mApiTitlePlatform');
		$this->db->where(array("mApiTitlePlatform.OrderSourceUID"=>$OrderSourceUID));
		$query = $this->db->get();
		$SourceName = $query->row();
		$CreatedByAPI = $SourceName->OrderSourceUID;
		$OrderSourceName = $SourceName->OrderSourceName;

		$Remarks = 'Notes (Exception Raised) - '.$DataArray['CustomerNotes'];
		$NoteType = $this->GetNoteTypeUID('API Note');
		$SectionUID = $NoteType->SectionUID;

		/*Exception raise */
		$fieldArray = array(
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

		$this->db->trans_begin();
		$this->db->insert('texceptions', $fieldArray);
		$this->db->where(array("torders.OrderUID"=>$OrderUID));
		$this->db->update('torders',$status);

		$insert_notes = array(
			'Note' => $Remarks,
			'SectionUID' => $SectionUID,
			'OrderUID' => $OrderUID,
			'InBoundUID' => $InBoundUID,
			'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
			'CreatedByAPI' => $CreatedByAPI,
			'CreatedOn' => date('Y-m-d H:i:s')
		);

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return array('status' => 'failed', 'Action'=>'dispute_request_error', 'Msg'=>'Error');
		}
		else
		{
			$this->db->trans_commit();
			$this->db->insert("tordernotes", $insert_notes);
			return array('status' => 'success', 'Action'=>'dispute_request_confirm', 'Msg'=>'Request sent successfully');
		} 
	}

	function LoansPQInsertNotes($DataArray){ 

		$OrderUID = $DataArray['OrderUID'];
		$InBoundUID = $DataArray['InBoundUID'];
		$OrderDetails = $this->common_model->get_orderdetails($OrderUID);
		$OrderSourceUID = $OrderDetails->OrderSourceUID;

		$this->db->trans_begin();

		$this->db->select("*");
		$this->db->from('mApiTitlePlatform');
		$this->db->where(array("mApiTitlePlatform.OrderSourceUID"=>$OrderSourceUID));
		$query = $this->db->get();
		$SourceName = $query->row();
		$CreatedByAPI = $SourceName->OrderSourceUID;
		$OrderSourceName = $SourceName->OrderSourceName;

		$Remarks = 'Notes - '.$DataArray['CustomerNotes'];
		$NoteType = $this->GetNoteTypeUID('API Note');
		$SectionUID = $NoteType->SectionUID;

		$insert_notes = array(
			'Note' => $Remarks,
			'SectionUID' => $SectionUID,
			'OrderUID' => $OrderUID,
			'InBoundUID' => $InBoundUID,
			'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
			'CreatedByAPI' => $CreatedByAPI,
			'CreatedOn' => date('Y-m-d H:i:s')
		);

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return array('status' => 'failed', 'Action'=>'note_error', 'Msg'=>'Error');
		}
		else
		{
			$this->db->trans_commit();
			$this->db->insert("tordernotes", $insert_notes);
			return array('status' => 'success', 'Action'=>'note_confirm', 'Msg'=>'Notes sent successfully');
		} 
	}

	function GetNoteTypeUID($SectionName)
	{
		$this->db->select("*");
		$this->db->from('mreportsections');
		$this->db->where(array("mreportsections.SectionName"=>$SectionName));
		$query = $this->db->get();
		return $query->row();
	}


	function toStatusPoll($fieldArray,$CreatedByAPI){

	}

}?>
