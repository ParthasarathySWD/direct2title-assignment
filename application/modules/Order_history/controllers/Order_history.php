<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_history extends MY_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('order_history_model');
		$this->load->model('Common_model');
		$this->load->config('keywords');
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
		// $data['content'] = 'index';
		// $this->load->view('page', $data);
		if(isset($_GET['OrderUID']))
		{
			$OrderUID = $_GET['OrderUID'];
      		$OrderUID = rtrim($OrderUID,'/');

			$OrderID = $this->common_model->GetOrderByID($OrderUID);
			$RelationalOrders = $this->Common_model->GetRelationalOrdersByID($OrderUID);
			$data['RelationalOrders'] = $RelationalOrders;
			$OrderNumber = $OrderID->OrderUID;

			if($OrderNumber == 0)
			{
			 	redirect(base_url().'my_orders');
			}

			$data['OrderUID'] = $_GET['OrderUID'];	

			$OrderUID = $_GET['OrderUID'];            
			$data['content'] = 'index';
			$data['order_details'] = $this->order_history_model->get_orderdetails($OrderUID);
			$UserUID= $this->session->userdata('UserUID');
			$data['OrderNumber']=$this->common_model->GetOrderNumberByOrderUID($OrderUID);
			$data['assignedorder'] = $this->order_history_model->get_assignedorderdetails($OrderUID);
			$data['revisionorders'] = $this->order_history_model->getrevisionorderdetails($OrderUID);
			$data['all_details'] = $this->order_history_model->GetAllAuditdetails($OrderUID);  
			$data['States'] = $this->common_model->GetStateDetails();
			$data['Sub_products'] = $this->common_model->GetSub_productDetails($ProductUID = '1');
			$data['Products'] = $this->common_model->GetProductDetails();
			$data['Cities'] = $this->common_model->GetCityDetails();
			$data['CountyDetails'] = $this->common_model->GetCountyDetails();
			$data['Prioritys'] = $this->common_model->GetPriorityDetails();
			$data['Ordertypes'] = $this->common_model->GetOrderTypeDetails();
			$data['Templates'] = $this->common_model->GetTemplateDetails();
			$data['Customers'] = $this->common_model->GetCustomerDetails();
			$data['Prop_roles'] = $this->common_model->GetPropertyrolesDetails();
			$data['TopBarDetails']= $this->common_model->GetTopBarDetailsByID($OrderUID,$UserUID);
			$data['TopBarDetails1']= $this->common_model->GetTopBarDetails($OrderUID);
			$data['CompleteDetails1']= $this->common_model->GetCompleteDetails($OrderUID);
			$data['CompleteDetails']= $this->common_model->GetCompleteDetailsByID($OrderUID,$UserUID);

            
			
		    $this->load->view('page', $data);

		}
		else
		{
			redirect(base_url().'my_orders');	
		}
	}



function getAllAudits(){
		$OrderUID = $this->input->post('OrderUID');
		if(count(explode(',',$OrderUID)) > 1){
			// $filter = $this->input->post('filter');
			$audits = [];
			foreach (explode(',',$OrderUID) as $key => $value) {
				$Note = $this->order_history_model->GetAllAuditdetails($value); 
				foreach ($Note as $key => $Nval) {
					array_push($audits,$Nval);
				}			
			}
			array_multisort(array_map('strtotime',array_column($audits,'DateTime')),
			SORT_DESC, 
			$audits);
			$data['all_details'] = $audits;
			$data['OrderUID'] = $OrderUID;
			$data['MultiOrders'] = 1;
			$html = $this->load->view('auditlog',$data,true);
			echo json_encode($html);
		}
		else{
			$data['all_details'] = $this->order_history_model->GetAllAuditdetails($OrderUID);
			$data['OrderUID'] = $OrderUID;
			$data['MultiOrders'] = 0;
			$html = $this->load->view('auditlog',$data,true);
			echo json_encode($html);
		}
	}









}
