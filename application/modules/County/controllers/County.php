<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class County extends MY_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('County_model');
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
		$this->load->view('page', $data);
	}
	public function add()
	{
		$data['content'] = 'add';
		// $data['CountyDetails']= $this->County_model->GetCountyDetails();
		$data['StateDetails']= $this->Common_model->GetStateDetails();
		$this->load->view('page', $data);
	}
	public function edit($CountyUID)
	{	
		$CountyUID = $this->uri->segment(3);
		$data['content'] = 'edit_county';
		// $data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$data['CountyDetails']=$this->County_model->GetCountyDetailsById($CountyUID);
		$data['StateDetails']= $this->Common_model->GetStateDetails();
		$this->load->view('page',$data);
	}
	public function save_county()
	{
		$this->County_model->saveCountyDetails($_POST);
		/*print_r($_POST);
		exit;*/
		
	}

	public function UpdateCountyStatus()
	{
		$this->County_model->saveCountyEditDetails($_POST);
		/*print_r($_POST);
		exit;*/
	}
	function ajax_list()
	{
        $data = $this->process_get_data();
        $post = $data['post'];
        $output = array(
          "draw" => $post['draw'],
          "recordsTotal" => $this->County_model->count_all($post),
          "recordsFiltered" =>  $this->County_model->count_filtered($post),
          "data" => $data['data'],
        );
        unset($post);
        unset($data);
        echo json_encode($output);        
    }
    
    function process_get_data()
    {
        $post = $this->get_post_input_data(); 
        /*@desc added table names front of column names 
        @ZohoID: DI1-T164
        @author Jainulabdeen 
        @changed on feb 6 2020*/
        $post['column_order'] = array('mcounties.CountyName','mcounties.CountyCode', 'mstates.StateName', 'mstates.TimeZone');
        $post['column_search'] = array('mcounties.CountyName','mcounties.CountyCode', 'mstates.StateName', 'mstates.TimeZone');
        /****************/
        $list = $this->County_model->GetCountyDetails($post);
        $data = array();
        $no = $post['start'];
        
        foreach ($list as $county) {
            $no++;
            $row =  $this->county_table_data($county, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
                );
    }
    
    function get_post_input_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        /*@desc remove single quotes and double quotes
        @author Jainulabdeen 
        @changed on feb 6 2020*/
        $post['search_value'] = str_replace(['[', ']', '"', '\''], "", $search['value']); 
        /**********/
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        return $post;
    }
    
    function county_table_data($county, $no)
    {
    	if($county->Active==1)
    	{
           // $status = '<span style="text-align: center;width:100%;" class="btn btn-rounded btn-space">
           //          <div class="switch-button switch-button-xs" onClick="showEditStatus('.$county->CountyUID.',0);">
           //            <input type="checkbox" name="Active'.$county->CountyUID.'" id="Active'.$county->CountyUID.'" value="'.$county->CountyUID.'" checked="true">
           //            <span><label for="Active'.$county->CountyUID.'"></label></span>
           //          </div>
           //        </span>';

    		$status ='<label class="custom-switch">
    		<input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked="">
    		<span class="custom-switch-indicator"></span>
    		</label>';

    	} else {
		  // $status = '<span style="text-align: center;width:100%;" class="btn btn-rounded btn-space" >
	   //                  <div class="switch-button switch-button-xs"onClick="showEditStatus('.$county->CountyUID.',1);">
	   //                    <input type="checkbox" name="Active'.$county->CountyUID.'" id="Active'.$county->CountyUID.'" value="'.$county->CountyUID.'">
	   //                    <span><label for="Active'.$county->CountyUID.'"></label></span>
	   //                  </div>
			 //        </span>';

    		$status ='<label class="custom-switch">
    		<input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked="">
    		<span class="custom-switch-indicator"></span>
    		</label>';


    	}

        $row = array(); 
        $row[] = '<label class="custom-control custom-checkbox">
        		  <input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">
        		  <span class="custom-control-label"></span>
        		  </label>';
        $row[] = $county->CountyName;
        // $row[] = $county->CountyCode;
        $row[] = $county->StateName;
        $row[] = $county->TimeZone;
        $row[] = $status;
        // $row[] = '<span style="text-align: center;width:100%;"><a title="Edit" href="'.base_url("county/edit_county/".$county->CountyUID).'" class="btn edit_btn"><span class="glyphicon glyphicon-edit"></span></a></span> 
        // 	<span title="Delete" data-value="'.$county->CountyUID.'" class="btn remove_btn delete CountyDeleteBtn"><span class="glyphicon glyphicon-trash"></span>';

        $row[]='<div class="item-action dropdown ml-2">
        <a href="javascript:void(0)" data-toggle="dropdown" style="font-weight: bold; color: #464bac; ">View County<i class="fe fe-more-vertical" style="vertical-align: middle; font-size: 20px !important;"></i></a>
        <div class="dropdown-menu dropdown-menu-right dropdown-new-menu" style="margin-top: 10px;">
        <a href="'.base_url("county/edit/".$county->CountyUID).'" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View County </a>
        <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-cloud-download"></i> Export</a>
        <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-copy"></i> Copy to</a>
        <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon icon-close"></i> In Active</a>
        </div>
        </div>';
        return $row;
    }

}
