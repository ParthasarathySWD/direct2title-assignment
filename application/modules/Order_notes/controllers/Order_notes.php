<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_notes extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
		// 	redirect(base_url().'users');
		// }else{
		// 	$this->load->model('users/Mlogin');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');
			$this->load->model('Notes_model');
			$this->load->model('Common_model');
			$this->load->model('real_ec_model');
			$this->load->model('Api_common_model');
			$this->load->model('Pabs_model');
			$this->load->library('Excel');
			// if (($this->session->userdata('scope_order_id') == NULL)) {
			// 	redirect(base_url().'my_orders');
			// }	

		// }
	}	

	public function index()
	{
		if(isset($_GET['OrderUID']))
		{
			$OrderUID = $_GET['OrderUID'];

			$OrderID = $this->Common_model->GetOrderByID($OrderUID);

			$OrderNumber = $OrderID->OrderUID;
			if($OrderNumber == 0){
				redirect(base_url().'my_orders');
			}
			
			$UserUID = $this->session->userdata('UserUID');
			
			$OrderrUID = $_GET['OrderUID'];
			
			$OrderUID = str_replace('/', '', $OrderrUID);
			
			$data['OrderUID'] = $OrderUID;

			$RelationalOrders = $this->Common_model->GetRelationalOrdersByID($OrderUID);
			$data['RelationalOrders'] = $RelationalOrders;
			$UserUID = $this->session->userdata('UserUID');
			// $filter = 'All';
			// $notes = $this->Notes_model->get_notes($OrderUID,$this->session->userdata('UserUID'),$filter);
			// $data['notes'] = $notes;
			// $data['sections'] = $this->Notes_model->get_Sections();

			// $data['AuditHistories'] = $this->Notes_model->getOrderAuditHistorys($OrderUID);
			$data['content'] = 'index';
			// $data['Action']="ADD";
			// $data['CaseSen'] = $this->common_model->get_CaseSen($OrderUID)->CaseSensitivity;
			// $data['data']=array('menu'=>'Notes','title'=>'Notes','link'=>array('Notes'));

			// $data['order_details'] = $this->Common_model->get_orderdetails($OrderUID);

			// $data['TopBarDetails']= $this->Common_model->GetTopBarDetailsByID($OrderUID,$UserUID);
			// $data['TopBarDetails1']= $this->common_model->GetTopBarDetails($OrderUID);
			// $data['CompleteDetails1']= $this->common_model->GetCompleteDetails($OrderUID);
			// $data['CompleteDetails']= $this->common_model->GetCompleteDetailsByID($OrderUID,$UserUID);
			// $data['RoleDetails']= $this->Notes_model->GetRole();
			// $data['is_vendor_login'] = $this->common_model->is_vendorlogin();
   //    		$data['ChatDetails']= $this->Notes_model->GetChatDetails($OrderUID);

   //   		$data['followups']= $this->Notes_model->get_torderfollowup($OrderUID);



			$data['Name'] = $this->UserName;
			// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);

			$this->load->view('page', $data);
		}
		else
		{
			redirect(base_url().'my_orders');	
		}
	}
}
