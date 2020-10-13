<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
class City extends MY_Controller {
	function __construct()
	{
		parent::__construct();
		ini_set('memory_limit', '-1');
		$this->load->model('City_model');
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
		$data['Total'] = count($this->Common_model->GetCityDetails());
		$this->load->view('page', $data);
	}
   /*
   * Function to Add City Page Function
   * @throws no exception
   * @return Array
   * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
   * @since OCT 12 2020
   */
	public function add()
	{
		$data['content'] = 'add';
		$data['CityDetails']= $this->Common_model->GetCityDetails();
		$data['CountyDetails']= $this->Common_model->GetCountyDetails();
		$data['StateDetails']= $this->Common_model->GetStateDetails();
		// $data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$this->load->view('page', $data);
	}
   /*
   * Function to Edit City Page Function
   * @throws no exception
   * @return Array
   * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
   * @since OCT 12 2020
   */
	public function edit()
	{
		$CityUID = $this->uri->segment(3);
		$data['content'] = 'edit_city';
		$data['CityDetails']=$this->City_model->GetCityDetailsById($CityUID);
		$data['CountyDetails']= $this->Common_model->GetCountyDetails();
		$data['StateDetails']= $this->Common_model->GetStateDetails();
		$this->load->view('page', $data);
	}

   /*
   * Function to Save City Details Function
   * @throws no exception
   * @return Array
   * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
   * @since OCT 12 2020
   */
	public function save_city()
	{
		$this->City_model->saveCityDetails($_POST); 
	}
   /*
   * Function to Update City Details Function
   * @throws no exception
   * @return Array
   * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
   * @since OCT 12 2020
   */
	public function UpdateCityStatus()
	{
		$this->City_model->saveCityEditDetails($_POST); 
	}
	
   /*
   * Function to Ajax to City Details Function
   * @throws no exception
   * @return Array
   * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
   * @since OCT 12 2020
   */
	function ajax_list()
	{
        $data = $this->process_get_data();
        $post = $data['post'];
        $output = array(
          "draw" => $post['draw'],
          "recordsTotal" => $this->City_model->count_all($post),
          "recordsFiltered" =>  $this->City_model->count_filtered($post),
          "data" => $data['data'],
        );
        unset($post);
        unset($data);
        echo json_encode($output);        
    }

     /*
   * Function to Get City Details Table Function
   * @throws no exception
   * @return Array
   * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
   * @since OCT 12 2020
   */
    
    function process_get_data()
    {
        $post = $this->get_post_input_data(); 
        $post['column_order'] = array('','CityName','CountyName', 'StateName', 'ZipCode','','');
        $post['column_search'] = array('CityName','CountyName', 'StateName', 'ZipCode');
        
        $list = $this->City_model->GetCityDetails($post);
        $data = array();
        $no = $post['start'];
        
        foreach ($list as $cities) {
            $no++;
            $row =  $this->cities_table_data($cities, $no);
            $data[] = $row;
        }
        
        return array(
                'data' => $data,
                'post' => $post
                );
    }
    
     /*
   * Function to Get Input Values Function
   * @throws no exception
   * @return Array
   * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
   * @since OCT 12 2020
   */
    function get_post_input_data(){
        $post['length'] = $this->input->post('length');
        $post['start'] = $this->input->post('start');
        $search = $this->input->post('search');
        $post['search_value'] = $search['value'];
        $post['order'] = $this->input->post('order');
        $post['draw'] = $this->input->post('draw');
        return $post;
    }
    
    /*
   * Function to Get City Details Function
   * @throws no exception
   * @return Array
   * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
   * @since OCT 12 2020
   */
    function cities_table_data($cities, $no)
    {
    	if($cities->Active==1)
    	{
           // $status = '<span style="text-align: center;width:100%;" class="btn btn-rounded btn-space">
           //          <div class="switch-button switch-button-xs" onClick="showEditStatus('.$cities->CityUID.',0);">
           //            <input type="checkbox" name="Active'.$cities->CityUID.'" id="Active'.$cities->CityUID.'" value="'.$cities->CityUID.'" checked="true">
           //            <span><label for="Active'.$cities->CityUID.'"></label></span>
           //          </div>
           //        </span>'

                $status =  '<label class="custom-switch" for="Active'.$cities->CityUID.'">
                   <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" name="Active'.$cities->CityUID.'" id="Active'.$cities->CityUID.'" value="'.$cities->CityUID.'" checked="true">
                   <span class="custom-switch-indicator"></span>
                  </label>';
    	} else {
		  // $status = '<span style="text-align: center;width:100%;" class="btn btn-rounded btn-space" >
	   //                  <div class="switch-button switch-button-xs" onClick="showEditStatus('.$cities->CityUID.',1);">
	   //                    <input type="checkbox" name="Active'.$cities->CityUID.'" id="Active'.$cities->CityUID.'" value="'.$cities->CityUID.'">
	   //                    <span><label for="Active'.$cities->CityUID.'"></label></span>
	   //                  </div>
			 //        </span>';

			     $status =    '<label class="custom-switch" for="Active'.$cities->CityUID.'">
                   <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" name="Active'.$cities->CityUID.'" id="Active'.$cities->CityUID.'" value="'.$cities->CityUID.'">
                   <span class="custom-switch-indicator"></span>
                  </label>';
    	}

        $row = array();
        $row[] ='<label class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1"><span class="custom-control-label"></span></label>';
        // $row[] = $no; 
        $row[] = $cities->CityName;
        $row[] = $cities->CountyName;
        $row[] = $cities->StateName;
        $row[] = $cities->ZipCode;
        $row[] = $status;
        // $row[] = '<span style="text-align: center;width:100%;">

        // <a title="Edit" href="'.base_url("city/edit_city/".$cities->CityUID).'" class="btn edit_btn"><span class="glyphicon glyphicon-edit"></span></a></span> 
        // 	<span title="Delete" data-value="'.$cities->CityUID.'" class="btn delete remove_btn CityDeleteBtn"><span class="glyphicon glyphicon-trash"></span>';
        $row[] =' <div class="item-action dropdown ml-2">
                  <a href="javascript:void(0)" data-toggle="dropdown" style="font-weight: bold; color: #464bac; ">View City<i class="fe fe-more-vertical" style="vertical-align: middle; font-size: 20px !important;"></i></a>
                  <div class="dropdown-menu dropdown-menu-right dropdown-new-menu" style="margin-top: 10px;">
                      <a href="'.base_url("city/edit/".$cities->CityUID).'" class="dropdown-item"><i class="dropdown-icon fa fa-eye"></i> View City </a>
                      <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-cloud-download"></i> Export</a>
                      <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fa fa-copy"></i> Copy to</a>
                      <a href="javascript:void(0)" class="dropdown-item"><i class="dropdown-icon fe fe-check-circle"></i> Active</a>
                   	</div>
                  </div>';
        return $row;
    }
}
