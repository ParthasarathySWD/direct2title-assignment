<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_info extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Order_info_model');
		$this->load->model('Common_model');
		$this->load->library(array('form_validation'));
    	$this->load->helper(array('form', 'url'));
    	$this->load->library('session');
    	$this->lang->load('keywords');

		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
		// 	redirect(base_url().'users');
		// }
		// else
		// {
			// $this->load->model('users/Mlogin');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');
		// }
    }	

	public function index()
	{
		// $data['content'] = 'fieldmapping';
		// $this->load->view('page', $data);

		if(isset($_GET['OrderUID']))
		{
			$OrderUID = $_GET['OrderUID'];
			$OrderID = $this->Common_model->GetOrderByID($OrderUID);
			$OrderNumber = $OrderID->OrderUID;
			if($OrderNumber == 0){
			 	redirect(base_url().'Myorders');
			}
			
			$UserUID = $this->session->userdata('UserUID');		
			$OrderrUID = $_GET['OrderUID'];
			$OrderUID = str_replace('/', '', $OrderrUID);
			$data['OrderUID'] = $OrderUID;			
			$data['order_details'] = $this->Common_model->get_orderdetails($OrderUID);
			$data['content'] = 'fieldmapping';
			$torders = $this->Order_info_model->Gettordersby_UID($OrderUID);
            $subproductuid=$torders[0]->SubProductUID;
            $countyuid=$torders[0]->PropertyCountyUID;
            $stateuid=$torders[0]->PropertyStateUID;

            $data['Orderinfo'] = $this->db->query('SELECT * FROM torderinfo WHERE OrderUID='.$OrderUID.'')->row();
			$data['MappedFieldBySubProduct'] = $this->Order_info_model->GetMappedFieldBySubProduct($subproductuid);
            $data['TemplateMappingList']=$this->Order_info_model->TemplatemappingList($torders);
            $data['TemplateDetails']=$this->Order_info_model->GetTemplateMappingByOrderUID($torders);

            $data['DynTemplateUID'] = $data['TemplateDetails']->TemplateUID;
            $data['FieldRow'] = $data['TemplateDetails']->FieldRow;

            //$UpdateTOrderTable = $this->UpdateTOrderTable($OrderUID,$DynTemplateUID,$FieldRow);

			//$data['Fields'] = $this->Order_info_model->GetFieldsByTemplateMappingUID($torders);
			$data['Name'] = $this->UserName;
			$data['Action']='Mapping_EDIT';
  			// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);

  			$data['AttachmentDetails'] = $this->Order_info_model->GetAttachmentDetailsForCopy($OrderUID);

			$this->load->view('page', $data);
		}
		else
		{
			redirect(base_url().'Myorders');	
		}
	}


}
