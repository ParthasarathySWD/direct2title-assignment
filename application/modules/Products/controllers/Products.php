<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Products_model');
		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
		// 	redirect(base_url().'users');
		// }
		// else{
		// 	$this->load->model('users/Mlogin');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');
			$this->lang->load('keywords');

		// }
    }	

	public function index()
	{
		// $data['content'] = 'index';
		// $this->load->view('page', $data);

		$data['content'] = 'index';
		$data['ProductsDetails']= $this->Products_model->GetProductsDetails();

		/*$s= $this->Products_model->ProductCode();
		echo '<pre>';print_r($s);exit;*/

		$data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$this->load->view('page', $data);
	}
	// public function add()
	// {
	// 	$data['content'] = 'add';
	// 	$this->load->view('page', $data);
	// }


	public function add_products()
	{
		$data['content'] = 'add';
		$data['Action']="ADD";
		$data['ProductsDetails']= $this->Products_model->GetProductsDetails();
		$data['ProductCode']= $this->Products_model->ProductCode();
		$data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$this->load->view('page',$data);
	}

	public function edit_products($ProductUID)
	{	
		$data['content'] = 'add';
		$data['Action']="EDIT";
		$data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$data['ProductsDetails']=$this->Products_model->GetProductsDetailsById($ProductUID);
		$this->load->view('page',$data);
	}

	 function auditlog()
	{
		$data['content'] = 'auditlog';
		$this->load->view('page', $data);
	}


	 function save_products()
	{
		$this->Products_model->saveProductsDetails($_POST);
		/*print_r($_POST);
		exit;*/
		
	}

	function GetProductName()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$ProductCode = $this->input->post('ProductCode');
			$details = $this->Products_model->GetProductName($ProductCode);
			echo json_encode($details);
		}
	}
	
}
