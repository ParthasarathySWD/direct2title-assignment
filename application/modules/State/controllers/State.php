<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class State extends MY_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('State_model');
		$this->load->model('Common_model');
		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
		// 	redirect(base_url().'users');
		// }
		// else{
		// 	$this->load->model('users/Mlogin');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');

		// }
    }	


	public function index()
	{
		$data['content'] = 'index';
		$data['StateDetails']= $this->State_model->GetStateDetails();
		$this->load->view('page', $data);
	}

	/*
	 * Function to Add Page Function
	 * @throws no exception
	 * @return Array
	 * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
	 * @since OCT 12 2020
	 */
	public function add()
	{
		$data['content'] = 'add';
		$data['StateDetails']= $this->State_model->GetStateDetails();
		$this->load->view('page', $data);
	}

	/*
	 * Function to Edit Page Function
	 * @throws no exception
	 * @return Array
	 * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
	 * @since OCT 12 2020
	 */
	public function edit()
	{ 
		$StateUID = $this->uri->segment(3);
		$data['content'] = 'edit_state';
		// $data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$data['StateDetails']=$this->State_model->GetStateDetailsById($StateUID);
		$data['Auditlog'] = $this->State_model->StateAuditlog();
		$this->load->view('page', $data);
	}


	/*
	 * Function to Save State Function
	 * @throws no exception
	 * @return Array
	 * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
	 * @since OCT 12 2020
	 */
	public function save_state()
	{
		$this->State_model->SaveStateInfo($_POST);
		// print_r($_POST);
		// exit;
		
	}

	/*
	 * Function to Update State Function
	 * @throws no exception
	 * @return Array
	 * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
	 * @since OCT 12 2020
	 */
	public function update_state()
	{
		$this->State_model->UpdateStateInfo($_POST);

	}	

}
