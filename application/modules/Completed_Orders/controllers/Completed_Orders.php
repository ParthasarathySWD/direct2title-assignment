<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Completed_Orders extends MY_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('completed_orders_model');
		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL)))
		// {
		// 	redirect(base_url().'users');
		// }else{
		// 	$this->load->model('users/Mlogin');
			$this->lang->load('keywords');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');

		// }
	}	


	public function index()
	{
		// $data['content'] = 'index';
		// $this->load->view('page', $data);
		$login_id = $this->loggedid;  
		$data['RoleUID'] = $this->RoleUID; 
		$data['content'] = 'index';
		$data['data']=array('menu'=>'CompletedOrders','title'=>'CompletedOrders','link'=>array('CompletedOrders'));
		$data['CompleteOrders'] =  $this->completed_orders_model->get_complete_orders($login_id);
		$data['Complete']=$this->completed_orders_model->CompleteOrders();
		$data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$this->load->view('page', $data); 
	}


	function GetCompletedOrders()
	{ 

		$login_id = $this->loggedid;  
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');

		$post['column_order'] = array('OrderNumber', 'ProductName', 'SubProductName', 'PropertyAddress1','PropertyCityName','PropertyCountyName','PropertyStateCode','PropertyZipcode');
		$post['column_search'] = array('OrderNumber', 'ProductName', 'SubProductName', 'PropertyAddress1','PropertyCityName','PropertyCountyName','PropertyStateCode','PropertyZipcode');


		$AssignmentDetails = $this->completed_orders_model->CompleteOrders();
		

		$wholeData = [];
		foreach ($AssignmentDetails as $key => $value) {
			$row = array();
			$row[] = $value->OrderNumber;
			$row[] = $value->ProductName.'/'.$value->SubProductName;
			$row[] =$value->CustomerName;
			$row[] =$value->OrderEntryDatetime;
			$row[] = $value->OrderDueDatetime;
			$row[] = $value->PriorityName;
			$row[] =$value->LoanNumber;
			$row[] = $value->PropertyAddress1;
			$row[] = $value->PropertyCityName;
			$row[] =$value->PropertyCountyName;
			$row[] =$value->PropertyStateCode;
			$row[] =$value->PropertyZipcode;
			$row[] =$value->OrderTypeName;
			$row[] =$value->AttentionName;
			$row[] =' <a href="'.base_url('Order_summary?OrderUID='.$value->OrderUID).'" class="btn btn-sm btn-icon button-edit text-primary" title="Edit"  data-edit= '.$value->OrderUID.'><i class="icon-pencil"></i></a><button class="btn btn-sm btn-icon text-danger" title="Delete"  data-delete= '.$value->OrderUID.'><i class="icon-trash"></i></button>';
			$i++;
			array_push($wholeData, $row);
		}

		$data =  array(
			'AssignmentTableList' => ($wholeData),
			'post' => $post
		);

		$post = $data['post'];
		$count_all = count($AssignmentDetails);
		// print_r($count_all);exit();
		//print_r($count_all);
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => count($AssignmentDetails),
			"recordsFiltered" => $count_all,
			"data" => $data['AssignmentTableList'],
		);

		unset($post);
		unset($data);
		echo json_encode($output);
	}
	public function add()
	{
		$data['content'] = 'add';
		$this->load->view('page', $data);
	}
}
