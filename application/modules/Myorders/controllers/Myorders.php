<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Myorders extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
		// 	redirect(base_url().'users');
		// }else{
			// $this->load->model('users/Mlogin');
		$this->lang->load('keywords');
		$this->loggedid = $this->session->userdata('UserUID');
		$this->RoleUID = $this->session->userdata('RoleUID');
		$this->RoleType = $this->session->userdata('RoleType');
		$this->UserName = $this->session->userdata('UserName');
		$this->load->model('My_orders_model');
		$this->load->model('Common_model');

			// $this->load->model('myorders/Myorders_model');
			// $this->load->model('dashboard/dashboard_model');
		$this->load->model('real_ec_model');
		// }
	}	

	public function index()
	{
		$data['content'] = 'index';

		$data['Assignment'] = $this->My_orders_model->assignmentOrders();
		$this->load->view('page', $data);
	}
	/**
        *@description Function to Assignment OrderList
        *
        * @param $query
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @since 15/09/2020 
        * @version New Assignment 
        *
    */ 

	function GetAssignmentOrders()
	{ 

		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = trim($search['value']);
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');

		$post['column_order'] = array('OrderNumber', 'ProductName', 'SubProductName', 'PropertyAddress1','PropertyCityName','PropertyCountyName','PropertyStateCode','PropertyZipcode');
		$post['column_search'] = array('OrderNumber', 'ProductName', 'SubProductName', 'PropertyAddress1','PropertyCityName','PropertyCountyName','PropertyStateCode','PropertyZipcode');

		$AssignmentDetails = $this->My_orders_model->assignmentOrders();

		$wholeData = [];
		foreach ($AssignmentDetails as $key => $value) {
			$row = array();
			$row[] = $value->OrderNumber;
			$row[] = $value->ProductName.'/'.$value->SubProductName;
			$row[] = $value->PriorityName;

			$WorkInProgress= ['5','10','19','20','35','39','40','59','60','90'];
			if (in_array($value->StatusUID, $WorkInProgress)){ 
				$Status = '<span style="color:#fff;" class="ml-0 mr-0">Work In Progress</span>';
			}
			else{
				$Status = ' <span class="badge badge-primary"  style="font-size: 8pt; color: #fff; background: '.$value->StatusColor.'">'.$value->StatusName.'</span> ';
			}

			if($AssignmentDetails->StatusUID == 0){
				$Status ='<span class="badge badge-primary">New</span>';	
			}
			$row[] = $Status;
			$currentqueue = $this->Common_model->GetCurrentQueueStatus($AssignmentDetails->OrderUID);
			$row[] =$currentqueue;
			$row[] = $value->PropertyAddress1;
			$row[] = $value->PropertyCityName;
			$row[] =$value->PropertyCountyName;
			$row[] =$value->PropertyStateCode;
			$row[] =$value->PropertyZipcode;
			$row[] =$value->OrderEntryDatetime;
			$row[] = $value->OrderDueDatetime;

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
/**
        *@description Function to Excel Export
        *
        * @param $query
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @since 15/09/2020 
        * @version New Assignment 
        *
    */ 

public function excelExport()
{
	$this->load->library("Excel");
	$object = new PHPExcel();
	$object->setActiveSheetIndex(0);

	$table_columns = array("A", "B", "C", "D","E", "F", "G", "H", "I", "J");
	$styleArray = array('font'  => array('bold'  => true,'color' => array('rgb' => 'ffffff')));

		/**
			* Set Fill Color for Heading
			* Set Style for font color & font type
			**/
			foreach ($table_columns as  $value)
			{
				$object->getActiveSheet()->getStyle($value.'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
				->getStartColor()->setRGB('003366');
				$object->getActiveSheet()->getStyle($value.'1')->applyFromArray($styleArray);
			}

		// Set Heading Value
			$table_columns = array('A'=>'SNo','B'=>'Order Number','C'=>'Product/SubProduct', 'D'=>'Loan Number',
				'E'=>'Current Workflow','F'=>'Status','G'=>'Borrower Name','H'=>'Property Address','I'=>'Order DateTime','J'=>'Due DateTime'
			);

			foreach ($table_columns as $key => $value)
			{
				$object->getActiveSheet()->setCellValue($key.'1', $value);
			}

			$excelData =$this->My_orders_model->assignmentOrders();

			$i=1;
			$n=2;
			foreach ($excelData as  $row) {
				$object->getActiveSheet()->setCellValue('A'.$n, $i);
				$object->getActiveSheet()->setCellValue('B'.$n, $row->OrderNumber);
				$object->getActiveSheet()->setCellValue('C'.$n, $row->ProductName."/".$row->SubProductName);
				$object->getActiveSheet()->setCellValue('D'.$n, $row->LoanNumber);
				$currentqueue = $this->Common_model->GetCurrentQueueStatus($row->OrderUID);
				$object->getActiveSheet()->setCellValue('E'.$n, $currentqueue);		     
				$object->getActiveSheet()->setCellValue('F'.$n, $row->StatusName);		     
				$object->getActiveSheet()->setCellValue('G'.$n, $row->CustomerName);		     
				$object->getActiveSheet()->setCellValue('H'.$n, $row->PropertyCityName."/".$row->PropertyCountyName."/".$row->PropertyStateCode);		     
				$object->getActiveSheet()->setCellValue('I'.$n, $row->OrderEntryDatetime);		     
				$object->getActiveSheet()->setCellValue('J'.$n, $row->OrderDueDatetime);	

				$i++;
				$n++;	
			}

			$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="Myorders.xlsx"');
			header('Cache-Control: max-age=0');
			$object_writer->save('php://output');
		}


	}
