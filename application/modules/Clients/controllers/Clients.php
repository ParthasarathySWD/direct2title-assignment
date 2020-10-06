<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
class Clients extends MY_Controller {
	function __construct()
	{
		parent::__construct();

		$this->load->library(array('form_validation'));
		$this->load->helper(array('form', 'url'));

		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
		// 	redirect(base_url().'users');
		// }
		// else{
		// 	$this->load->model('users/Mlogin');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');
			$this->load->model('Customers_Model');
			$this->lang->load('keywords');
		// }
	}	

	public function index()
	{
		$data['content'] = 'index';
		$data['CustomerDetails']= $this->Customers_Model->GetCustomerDetails();
		$data['CustomersTotal']= count($this->common_model->get('mcustomers',array()));
		$data['CustomersActive']= count($this->common_model->get('mcustomers',array('Active'=>1)));
		$data['CustomersInActive']= count($this->common_model->get('mcustomers',array('Active'=>0)));
		$data['Name'] = $this->UserName;
		$this->load->view('page', $data);
	}
	public function add()
	{
		$data['content'] = 'add';
		$data['Action'] = 'AddDetails';
		// $this->db->select('*')->from('mCostCenter');
		// $data['getcost_center'] = $this->db->get()->result();		
		$data['Sub_products'] = $this->common_model->GetSub_productDetails();
		$data['SubProducts'] = $this->common_model->GetSub_productDetails();
		$data['Products'] = $this->common_model->GetProductDetails();
		$data['SubdProducts'] = $this->common_model->GetProductDetails();
		$data['Prioritys'] = $this->common_model->GetPriorityDetails();
		$data['Ordertypes'] = $this->common_model->GetOrderTypeDetails();
		$data['Templates'] = $this->common_model->GetTemplateDetails();
		$data['Customers'] = $this->Customers_Model->GetCustomerDetails();
		$data['ParentCompanyDetails'] = $this->Customers_Model->GetParentCompanyDetails();
		$data['PricingDetails'] = $this->common_model->GetCustomerPricingDetails();
		$data['TemplateDetails'] = $this->common_model->GetTemplateDetails();
		// $data['ServiceTypeDetails'] = $this->common_model->GetServiceTypeDetails();
		$data['WorkflowDetaiils'] = $this->GetWorkflowDetaiils();
		$data['Prop_roles'] = $this->common_model->GetPropertyrolesDetails();
		$data['Groups'] = $this->Customers_Model->GetGroups();
		$data['AllWorkflowModule'] = $this->Customers_Model->GetAllWorkflowModule();
		$data['mtats'] = $this->common_model->Getmtats();
		$data['SourceAPI']= $this->Customers_Model->GetSourceApi();
		$data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$this->load->view('page', $data);
	}
	function GetWorkflowDetaiils(){
		$this->db->select("*");
		$this->db->from('mworkflowmodules');
		$query = $this->db->get();
		return $query->result();
	}

	 function getzip()
   {
    if ($this->input->server('REQUEST_METHOD') === 'POST'){
     $CustomerZipCode = $this->input->post('CustomerZipCode');
     $details = $this->Customers_Model->getzipcontents($CustomerZipCode);
     echo json_encode($details);

   }
 }


	public function Edit()
	{
		$CustomerUID = $this->uri->segment(3);
		$data['content'] = 'edit';
		$data['Action'] = 'EditDetails';
		$data['UID'] =$CustomerUID;
		$data['Customers'] = $this->Customers_Model->GetCustomerDetailsByUID($CustomerUID);
		$CustomerZipCode = $data['Customers']->CustomerZipCode;
		$data['LocationDetails'] = $this->Customers_Model->getzipcontents($CustomerZipCode);
		$data['Prioritys'] = $this->common_model->GetPriorityDetails();
		$data['Templates'] = $this->common_model->GetTemplateDetails();
		$data['Groups'] = $this->Customers_Model->GetGroups();
		$Prod_SubProd = $this->Customers_Model->Get_Customer_SubProduct_ById_Prod($CustomerUID);
		$this->load->view('page', $data);
	}
	public function contacts()
	{
		$data['content'] = 'contacts';
		$ClientID= $this->uri->segment(3);
		$data['Action'] = (!empty($ClientID))?'EditDetails':'AddDetails';
		$this->load->view('page', $data);
	}
	public function pricing()
	{
		$ClientID= $this->uri->segment(3);
		$data['content'] = 'pricing';
		$data['Action'] = (!empty($ClientID))?'EditDetails':'AddDetails';
		$this->load->view('page', $data);
	}
	public function products()
	{
		$ClientID= $this->uri->segment(3);
		$data['content'] = 'products';
		$data['Action'] = (!empty($ClientID))?'EditDetails':'AddDetails';
		$data['Customers'] = $this->Customers_Model->GetCustomerDetailsByUID($CustomerUID);
		$this->load->view('page', $data);
	}
	public function workflows()
	{
		$data['content'] = 'workflows';
		$ClientID= $this->uri->segment(3);
		$data['Action'] = (!empty($ClientID))?'EditDetails':'AddDetails';
		$this->load->view('page', $data);
	}
	public function task_management()
	{
		$data['content'] = 'task-management';
		$ClientID= $this->uri->segment(3);
		$data['Action'] = (!empty($ClientID))?'EditDetails':'AddDetails';
		$this->load->view('page', $data);
	}
	public function priority_tat()
	{
		$data['content'] = 'priority-tat';
		$ClientID= $this->uri->segment(3);
		$data['Action'] = (!empty($ClientID))?'EditDetails':'AddDetails';
		$this->load->view('page', $data);
	}
	public function pass_through_cost()
	{
		$data['content'] = 'pass-through-cost';
		$ClientID= $this->uri->segment(3);
		$data['Action'] = (!empty($ClientID))?'EditDetails':'AddDetails';
		$this->load->view('page', $data);
	}
	public function billing()
	{
		$data['content'] = 'billing';
		$ClientID= $this->uri->segment(3);
		$data['Action'] = (!empty($ClientID))?'EditDetails':'AddDetails';
		$this->load->view('page', $data);
	}
	public function audit_log()
	{
		$ClientID= $this->uri->segment(3);
		$data['content'] = 'audit-log';
		$data['Action'] = (!empty($ClientID))?'EditDetails':'AddDetails';
		$this->load->view('page', $data);
	}


}
