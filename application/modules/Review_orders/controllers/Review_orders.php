<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Review_orders extends MY_Controller {
	function __construct()
	{
		parent::__construct();
		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
		// 	redirect(base_url().'users');
		// }
		// else
		// {
		// 	$this->load->model('users/Mlogin');
			$this->lang->load('keywords');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->RoleType = $this->session->userdata('RoleType');
			$this->UserName = $this->session->userdata('UserName');
			$this->load->model('review_orders_model');
			$this->load->model('Myorders/My_orders_model');
			//load myorders model
			// $this->load->model('Myorders/Myorders_model');

		// }
	}	

	public function index()
	{
		// $data['content'] = 'index';
		$loggedid = $this->loggedid; 
		$UserUID = $this->session->userdata('UserUID');
		$data['content'] = 'index';
		$data['data']=array('menu'=>'Review_Orders','title'=>'Review Orders','link'=>array('Review Orders'));
		$data['is_selfassign'] = $this->common_model->is_selfassign($loggedid);
		// $data['review_orders'] = $this->Review_Orders_Model->get_reviewedorders($loggedid);
		/*echo '<pre>';print_r($data['review_orders']);exit;*/
		// foreach ($data['review_orders'] as $key => $value) 
		// {
		// 	$assignedusers =  $this->Review_Orders_Model->get_assigned_users($value->OrderUID);
		// 	$data['review_orders'][$key]->LoginID = $assignedusers;
		// }

		$data['controller'] = $this;
		$is_vendor_login = $this->common_model->is_vendorlogin();
		$data['is_vendor_login'] = $is_vendor_login;
		if($is_vendor_login)
		{
			$logged_details = $this->common_model->get_logged_details();
			$data['logged_details'] = $logged_details;
			$vendors  = $this->My_orders_model->get_vendors($logged_details,$this->loggedid);
			$vendoruids = $this->My_orders_model->get_vendor_uids($vendors);
			$data['groupsbyloggedid']  = $this->My_orders_model->get_vendor_groups($is_vendor_login,$vendoruids,$this->loggedid); 
		}else{
			$data['groupsbyloggedid'] = $this->My_orders_model->get_groupsby_loggedid(); 
		}
		$data['products'] = '';
		$data['customers'] = '';
		$data['CustomerProjects'] = $this->common_model->_get_loginProjects();
		if($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts){
				$data['products'] =  $this->common_model->_get_loginProducts($UserProducts); 
				$data['customers'] = $this->common_model->_get_loginCustomers($UserProducts);
			}
		}else{
			$data['products'] =  $this->My_orders_model->GetProducts();
			$data['customers'] = $this->My_orders_model->GetCustomers($loggedid);
		}

		$data['Name'] = $this->UserName;
		$this->load->view('page', $data);
	}
	public function add()
	{
		$data['content'] = 'add';
		$this->load->view('page', $data);
	}





	//ajax list
	function assignment_product_report_ajax_list()
	{
		$post['loggedid'] = $this->loggedid; 
		//Advanced Search
		$post['advancedsearch'] = $this->input->post('formData');
    	$post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
    	//get_post_input_data
    	//column order
        $post['column_order'] = array('torders.OrderNumber','mcustomers.CustomerNumber','mcustomers.CustomerName',
        	// 'BarCode',
        	'mProjects.ProjectName','musers.LoginID',
        	// 'torders.LoanNumber','torders.LoanAmount',
        	'torders.PropertyStateCode',
        	// 'torderinfo.AssignorPrintName',
        	// 'torderinfo.AssigneePrintName',
        	// 'torderinfo.LenderPrintName','torderinfo.MortgageDated','torderinfo.DeedOfTrustDated','torderinfo.RecordedDate','torderinfo.Book','torderinfo.Page','torderinfo.DocumentNumber','torderinfo.Comments','torderinfo.TaxID','torderinfo.LegalDescription','torderinfo.EndorserPrintName','torderinfo.PreparedBy',
        	'morderstatus.StatusName');
        $post['column_search'] = array('torders.OrderNumber','mcustomers.CustomerNumber','mcustomers.CustomerName',
        	// 'BarCode',
        	'mProjects.ProjectName','musers.LoginID',
        	// 'torderinfo.LoanNumber','torders.LoanAmount',
        	'torders.PropertyStateCode',
        	// 'torderinfo.AssignorPrintName','torderinfo.AssigneePrintName','torderinfo.LenderPrintName','torderinfo.MortgageDated','torderinfo.DeedOfTrustDated','torderinfo.RecordedDate','torderinfo.Book','torderinfo.Page','torderinfo.DocumentNumber','torderinfo.Comments','torderinfo.TaxID','torderinfo.LegalDescription','torderinfo.EndorserPrintName','torderinfo.PreparedBy',
        	'morderstatus.StatusName');
        //column order

        // $list = $this->assignment_product_report_model->assignment_product_report_ajax_list($post);
        $list = $this->review_orders_model->get_reviewedorders($post);
       
        // print_r($list);exit();
        $no = $post['start'];
        $myorderslist = [];
		foreach ($list as $myorders)
        {
		        $row = array();

		        $row[] = $myorders->OrderNumber; 
		        $row[] =  $myorders->CustomerNumber.' / '.$myorders->CustomerName ;
            // $row[] = $myorders->BarCode;
            $row[] = $myorders->ProjectName;
            // $row[] = $myorders->LoanNumber;
            // $row[] = $myorders->LoanAmount;
            $row[] = $myorders->PropertyStateCode;
            $row[] = 
            '<span class="btn btn-rounded btn-xs statusbutton btn-custom-sm" style="color: #fff;border-radius:25px;background: '.$myorders->StatusColor.'">'
            .$myorders->StatusName.
            '<span>';
             // $row[] = $myorders->AssigneePrintName;
            $row[] = $this->common_model->GetCurrentQueueStatus($myorders->OrderUID); 
            $row[] = $myorders->LoginID;
$completed_workflowstatus = $this->common_model->completed_status_order($myorders->OrderUID);

            $row[] = $completed_workflowstatus->WorkflowModuleName;

            // $row[] = $myorders->DeedOfTrustDated;
            // $row[] = $myorders->RecordedDate;
            // $row[] = $myorders->Book;
            // $row[] = $myorders->Page;
            // $row[] = $myorders->DocumentNumber;
            // $row[] = $myorders->Comments;
            // $row[] = $myorders->TaxID;
            // $row[] = $myorders->LegalDescription;
            // $row[] = $myorders->EndorserPrintName;
            $row[] = $myorders->OrderEntryDatetime;
            $Action = '<a class="btn edit_btn" title="Edit Order" href="'.base_url() . 'review_orders/redirect_review/'. $myorders->OrderUID.'">
                            <i class="icon-pencil"></i>
                          </a>';
            $Action .= '<a class="btn edit_btn" title="View Order" href="'.base_url() . 'users/order_session/'. $myorders->OrderUID.'?mode=view">
                            <i class="fa fa-eye">
                            </i>
                          </a>';

            $row[] = $Action;
  		        $myorderslist[] = $row;
        }

        $data =  array(
        	'myorderslist' => $myorderslist,
        	'post' => $post
        );

		$post = $data['post'];
		$output = array(
			"draw" => $post['draw'],
			"recordsTotal" => $this->review_orders_model->count_all($post),
			"recordsFiltered" =>  $this->review_orders_model->count_filtered($post),
			"data" => $data['myorderslist'],
		);
		unset($post);
		unset($data);

		echo json_encode($output);
	}
	
}
