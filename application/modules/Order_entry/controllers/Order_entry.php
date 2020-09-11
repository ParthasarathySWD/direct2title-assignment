<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_entry extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('USPS');
		// Check if username exists in session
		if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
			redirect(base_url().'users');
		}
		else{
			$this->load->model('Orderentry_model');
			$this->load->library(array('form_validation'));
			$this->load->helper('form');
			$this->lang->load('keywords');
			$this->load->helper('customer_pricing');
			// $this->load->model('users/Mlogin');
			// $this->loggedid = $this->session->userdata('UserUID');
			// $this->RoleUID = $this->session->userdata('RoleUID');
			// $this->UserName = $this->session->userdata('UserName');
			// $this->RoleType = $this->session->userdata('RoleType');
			$this->UserUID ='3287';
			$this->loggedid ='techorg';
			$this->RoleUID ='1';
			$this->UserName ='techorg';
			$this->RoleType ='Administrator';
		}

	}

	public function index()
	{
		$data['content'] = 'index';
		$data['OrderSummaryID'] = 0;
		$data['data'] = array('menu'=>'OrderEntry','title'=>'Order Entry','link'=>array('OrderEntry'));
		$data['States'] = $this->common_model->GetStateDetails();
		$data['Prioritys'] = $this->common_model->GetPriorityDetails();
		$data['Ordertypes'] = $this->common_model->GetOrderTypeDetails();
		$data['TransactionTypeDetails'] = $this->common_model->GetTransactionTypeDetails();
		$data['PropertyTypeDetails'] = $this->common_model->GetPropertyTypeDetails();
		$data['BorrowerDetails'] = $this->common_model->GetBorrowerDetailsDescription();
		$data['documentstatus'] = $this->config->item('RMS_DocumentStatus');


		if(in_array($this->session->userdata('RoleType'),array(1,2,3,4,5)))
		{
			$data['Customers'] = $this->common_model->GetCustomerDetails();
			$data['bulk_Customers'] = $this->common_model->GetCustomerDetails();

		} else if(in_array($this->session->userdata('RoleType'),array(8))) {
			$data['Customers'] = $this->Orderentry_model->Get_Assign_Customer($this->loggedid);
			$data['bulk_Customers'] = $this->Orderentry_model->Get_Assign_Customer($this->loggedid);

		}else if(in_array($this->session->userdata('RoleType'),array(6))) {
			$data['Customers'] = $this->common_model->_get_customersdata_bylogin($this->loggedid);
			$data['bulk_Customers'] = $data['Customers'];

		} else {
			$data['Customers'] = $this->Orderentry_model->get_customer_ingroup($this->loggedid);
			$data['bulk_Customers'] = $this->Orderentry_model->get_customer_ingroup($this->loggedid);
		}


		if(isset($_GET['OrderUID']))
		{
			$OrderID = $_GET['OrderUID'];
			$data['OrderSummaryID'] = $OrderID;
			$data['OrderEntryCopy'] = $this->Orderentry_model->GetOrderEntryCopyDetailsbyUID($OrderID);
			$ProductUID = $data['OrderEntryCopy']->ProductUID;
			$CustomerUID = $data['OrderEntryCopy']->CustomerUID;

			$payload['CustomerUID'] = $this->input->get('CustomerUID');
			$payload['ProductUID'] = $this->input->get('ProductUID');
			$payload['SubProductUID'] = $this->input->get('SubProductUID');
			if (!empty($payload['CustomerUID'])) {
				$CustomerUID = $payload['CustomerUID'];
				$data['OrderEntryCopy']->CustomerUID = $CustomerUID;
			}
			if (!empty($payload['ProductUID'])) {
				$ProductUID = $payload['ProductUID'];
				$data['OrderEntryCopy']->ProductUID = $ProductUID;
			}
			if (!empty($payload['SubProductUID'])) {
				$SubProductUID = $payload['SubProductUID'];
				$data['OrderEntryCopy']->SubProductUID = $SubProductUID;
			}

			// if (!empty($ClosingProducts = $this->Orderentry_model->getClosingProductUIDs())) {
			// 	if (in_array($ProductUID, $ClosingProducts)) {
			// 		$torders = $this->common_model->get_row('torders',['OrderUID'=>$OrderUID]);
			// 		if ($torders->IsInhouseExternal) {
			// 			$SubProductUID = $this->config->item('SubProducts')['Onlilne L&V & Closing'];
			// 		}
			// 		else{
			// 			$SubProductUID = $this->config->item('SubProducts')['Field L&V & Closing'];
			// 		}
			// 		$data['OrderEntryCopy']->SubProductUID = $SubProductUID;						
			// 	}
				
			// }
			$data['CheckX1Order'] = $this->Orderentry_model->GetPreferedSites($OrderID) ;
			$data['order_details'] = $this->common_model->get_orderdetails($OrderID);
			
			$data['Product']= $this->Orderentry_model->GetCustomerProductDetails($CustomerUID);
			$data['Sub_products'] = $this->Orderentry_model->GetCustomerSubProductDetails($CustomerUID,$ProductUID);
			$data['encrypt'] = new AesCtr();
			
		}	
		$data['Prop_roles'] = $this->common_model->GetPropertyrolesDetails();
		$data['bundleddetails'] = $this->Orderentry_model->get_bundleddetails();
		$data['is_vendor_login'] = $this->common_model->is_vendorlogin();
		$data['Name'] = $this->UserName;
		$data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$data['IsUpper'] = $this->common_model->GetOrganizations();

			$data['UserUID'] ='3287';
			$data['RoleUID'] ='1';
			 $data['UserName'] ='techorg';
			 $data['RoleType'] ='Administrator';

		// $data['UserUID'] = $this->loggedid;
		// $data['RoleUID'] = $this->RoleUID;
		// $data['RoleType'] = $this->RoleType;
		$this->load->view('page', $data);
		$this->load->view('page', $data);
	}
	public function bulk_entry()
	{
		$data['content'] = 'bulkentry';
		$this->load->view('page', $data);
	}
}
