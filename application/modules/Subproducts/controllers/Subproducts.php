<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subproducts extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('Subproducts_model');
		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
		// 	redirect(base_url().'users');
		// }
		// else{
		// 	$this->load->model('users/Mlogin');
			$this->load->library(array('form_validation'));
			$this->load->model('common_model');
			$this->load->helper(array('form', 'url'));
    		// $this->load->model('fields/fields_model');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');
			$this->lang->load('keywords');

		// }
	}	

	public function index()
	{
		$data['content'] = 'index';
		$data['SubproductDetails']= $this->Subproducts_model->GetSubproductDetails();
		$data['ProductDetails']= $this->Subproducts_model->GetProductDetails();
		$data['OrderTypeDetails'] = $this->common_model->GetOrderTypeDetails();
		$data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$this->load->view('page', $data);
	}
	public function add()
	{
		$data['content'] = 'add';
		$data['SubproductDetails']= $this->Subproducts_model->GetSubproductDetails();
		$data['ProductDetails']= $this->Subproducts_model->GetProductDetails();
		$data['SubProductCode']= $this->Subproducts_model->SubProductCode();
		$data['OrderTypeDetails'] = $this->common_model->GetOrderTypeDetails(['SubProductUID'=>9223372036854775808]);
		$data['PriorityDetails'] = $this->common_model->GetPriorityDetails();
		$data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$this->load->view('page', $data);
	}
	public function edit_subproducts($SubProductUID)
	{	
		$data['content'] = 'add';
        $data['Action']="EDIT";
		$data['ProductDetails']= $this->Subproducts_model->GetProductDetails();
		$data['States'] = $this->common_model->GetStateDetails();
		$data['mfields'] = $this->Subproducts_model->GetMFields();
		$data['OrderTypeDetails'] = $this->common_model->GetOrderTypeDetails(['SubProductUID'=>$SubProductUID]);
		$data['PriorityDetails'] = $this->common_model->GetPriorityDetails();
		$data['SubproductDetails']=$this->Subproducts_model->GetSubproductDetailsById($SubProductUID);
		$data['ProductLevelMapped']= $this->Subproducts_model->GetSubproductLevelMapping($SubProductUID);
		$data['TemplateDetails'] = $this->Subproducts_model->GetTemplateDetailsBySubProductUID($SubProductUID);
		$data['TemplateMappingDetails'] = $this->Subproducts_model->GetTemplateMappingDetails($SubProductUID);	
		$data['CheckMappingLevel']= $this->Subproducts_model->CheckMappingLevel($SubProductUID);
		// $data['Table'] = $this->fields_model->GetTables();
		// $data['MasterTable'] = $this->fields_model->GetMasterTables();
		$data['Tasks'] = $this->Subproducts_model->GetTaskandWorkflowDetails()['tasks'];
		$data['Workflows'] = $this->Subproducts_model->GetTaskandWorkflowDetails()['workflows'];
		$data['SubProductTasks'] = $this->Subproducts_model->getAllTasks($SubProductUID);
		$data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$this->load->view('page',$data);
	}

	/**
    * @description Get Selected SubProduct Details
    * @param ProductUID
    * @throws HTML
    * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
    * @return SubProduct Details 
    * @since  23/09/2020
    * @version  D2T New Assignment
    */
	function get_SelectedSubProduct()
	{
		$ProductUID = $this->input->post('ProductUID');
		if($ProductUID)
		{
			$SelectedSubProducts = $this->Subproducts_model->getSelectedProductSubProduct($ProductUID);
		}
		else{
			$SelectedSubProducts= $this->Subproducts_model->GetSubproductDetails();
		}
		$html = '';
		$i=1;
		foreach ($SelectedSubProducts as $key => $row)
		{
			$html .= '<tr>';
			$html .= '<td style="text-align: center;">'.$i.'</td>';
			$html .= '<td style="text-align: left;">'.$row->SubProductCode.'</td>
			<td style="text-align: left;">'.$row->SubProductName.'</td>
			<td style="text-align: left;">'.$row->ProductName.'</td>
			<td style="text-align: left;">'.$row->ReportHeading.'</td>';
			$html .= '<td><span style="text-align: center;width:100%;" class="btn btn-rounded btn-xs btn-space">
			<div class="switch-button  switch-button-xs ">';
			if($row->Active==1)
			{
				$html .='<input type="checkbox" id="'.$row->SubProductUID.'" value="1" class="status" checked="true">';
			}
			elseif($row->Active==0)
			{
				$html .='<input type="checkbox" id="'.$row->SubProductUID.'" value="0" class="status">';
			}

			$html .='<span><label for="'.$row->SubProductUID.'"></label></span></div></span></td>';

			$html .='<td><span style="text-align: center;width:100%;display: inline-block;">
			<a title="Edit" href="'. base_url()."subproducts/edit_subproducts/".$row->SubProductUID.'" class="btn edit_btn">
			<span class="glyphicon glyphicon-edit"></span>
			</a>

			<a title="Delete" href="" data-value="'.$row->SubProductUID.'" class="btn remove_btn btnDelete"><span class="glyphicon glyphicon-trash"></span>
			</a>
			</span>
			</td>';
			$html .= '</tr>';
			$i++;  
		}
		echo $html;
	}
	/**
    * @description Delete Selected SubProduct Details
    * @param ID
    * @throws JSON
    * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
    * @return SubProduct Details 
    * @since  23/09/2020
    * @version  D2T New Assignment
    */
	public function delete_subproducts()
	{
		$Id = $this->input->post('Id');
		$result = $this->Subproducts_model->delete_subproducts($Id);
		if($result)
		{
			$Msg = $this->lang->line('Delete');
			$res = array("validation_error" => 0,'message' => $Msg);
			
		}
		else
		{
			$Msg = $this->lang->line('City_Processing_Error');
			$res = array("validation_error" => 1,'message' => $Msg);;
		}
		echo json_encode($res);
	}
	/**
    * @description SubProduct Mapped Functionality 
    * @param SubProductUID
    * @throws JSON
    * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
    * @return SubProduct Details 
    * @since  23/09/2020
    * @version  D2T New Assignment
    */
	function check_subproduct_mapped_order(){
		$SubProductUID = $this->input->post('SubProductUID');

		$this->db->select('*');
		$this->db->from('torders');
		$this->db->where('torders.SubProductUID', $SubProductUID);
		$query = $this->db->get();
		$count = $query->num_rows();

		if($count > 0){
			$result = array('success'=> 1, 'Count' => $count);
		} else {
			$result = array('success'=> 0, 'Count' => $count);			
		}

		echo json_encode($result);
	}

	/**
    * @description SubProduct to Change Status
    * @param SubProductUID,Status
    * @throws JSON
    * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
    * @return SubProduct Details 
    * @since  23/09/2020
    * @version  D2T New Assignment
    */
	function ajax_changestatus()
	{
		$SubProductUID = $this->input->post('SubProductUID');
		$data['Active'] = $this->input->post('status');
		$this->db->where('SubProductUID',$SubProductUID);
		if($this->db->update('msubproducts',$data))
		{
			$Msg = $this->lang->line('Search_Status');
			$res = array("validation_error" => 0,'message' => $Msg);;
		} else {
			$Msg = $this->lang->line('Search_Status_Validation');
			$res = array("validation_error" => 0,'message' => $Msg);
		}
		echo json_encode($res);
	}

	/**
    * @description Save SubProduct Details
    * @param Form Details
    * @throws JSON
    * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
    * @return SubProduct Details 
    * @since  23/09/2020
    * @version  D2T New Assignment
    */
	public function save_subproducts()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$this->form_validation->set_error_delimiters('', '');
			$this->form_validation->set_message('required', 'This Field is required');
			$this->form_validation->set_rules('SubProductName', '', 'required');
			$this->form_validation->set_rules('ProductUID', '', 'required');
			if ($this->form_validation->run() == TRUE) {

				$this->Subproducts_model->saveSubproductDetails($this->input->post());
			}
			else{
				$Msg=$this->lang->line('Empty_Validation');
				$data = array(
					'validation_error' => 1,
					'message' =>$Msg, 
					'SubProductName' => form_error('SubProductName'),
					'ProductUID' => form_error('ProductUID'),
					
				);
				foreach($data as $key=>$value)
				{
					if(is_null($value) || $value == '')
						unset($data[$key]);
				}
				echo json_encode($data);
			}

		}
	}


	function Again_Update_SubProduct()
	{
		$SubProductUID = $this->input->post('SubProductUID');
		$SubProductDetails_array = $this->input->post('SubProductDetails');
		$SubProductLevel_Array_array = $this->input->post('SubProductLevel_Array');
		$StateLevel_Array_array = $this->input->post('StateLevel_Array');
		$CountyLevel_Array_array = $this->input->post('CountyLevel_Array');
		$FieldPosition = $this->input->post('FieldPosition');

		$SubProductDetails = $SubProductDetails_array[0];
		$SubProductLevel_Array = $SubProductLevel_Array_array[0];
		$StateLevel_Array = $StateLevel_Array_array[0];
		$CountyLevel_Array = $CountyLevel_Array_array[0];


		$Mapping = $SubProductDetails['MappingLevel'];
		
		$FieldRow = $this->Subproducts_model->GetFieldRowBySubProductUID($SubProductUID); 
		$row_before = $FieldRow->FieldRow;
		$row = $row_before + 1;

		if($Mapping==1)
		{

			$FieldRow_UID  = $SubProductLevel_Array['FieldRow'];
			$IsDefault = $this->Subproducts_model->GetIsDefaultFieldRow($SubProductUID,$FieldRow_UID);
			$AfterDeleteFieldRow = $this->Subproducts_model->DeleteOldMappedFieldsByFieldRow($SubProductUID,$FieldRow_UID);

			$SubproductUID = $this->input->post('SubProductUID');
			$Fields['SubproductUID'] = $SubproductUID;
			$Fields['MappingLevel'] = $Mapping;
			$FieldUID  = $SubProductLevel_Array['FieldUID'];
			$DocumentType  = $SubProductLevel_Array['DocumentType'];
			$TemplateUID  = $SubProductLevel_Array['TemplateUID'];
			$TemplateFileName  = $SubProductLevel_Array['TemplateFileName'];		

			foreach($FieldUID as $field)
			{
				$Fields['FieldUID'] = $field;
				$Fields['FieldRow'] = $row;
				$mapfield = $this->Subproducts_model->SaveMappingField($Fields);
			}

			$FilePath = "uploads/SubProductLevelTemplates/";
    		mkdir($FilePath, 0777, true);

			$file=FCPATH . $FilePath.'SubProductlevel_Template-'.$SubproductUID.'-'.$TemplateUID.'-'.$row.'.php';
			file_put_contents($file, $TemplateFileName);

			$SubproductUID = $this->input->post('SubProductUID');
			$tempte['SubproductUID'] = $SubproductUID;
			$tempte['TemplateUID'] = $TemplateUID;
			$tempte['DocumentType'] = $DocumentType;
			$tempte['TemplateFileName'] = $file;
			$tempte['FieldRow'] = $row;
			$tempte['IsDefault'] = $IsDefault;
			$stored = $this->Subproducts_model->SaveMappingTemplate($tempte);

			if($stored==1 && $mapfield ==1)
			{
				$res = array("validation_error" => 0, 'message' => 'Sub Product Added Successfully', 'color'=>'success','SubProductUID'=>$SubproductUID);
			}
		}

		if($Mapping==2)
		{
			$FieldRow_UID  = $StateLevel_Array['FieldRow'];
			$IsDefault = $this->Subproducts_model->GetIsDefaultFieldRow($SubProductUID,$FieldRow_UID);
			$AfterDeleteFieldRow = $this->Subproducts_model->DeleteOldMappedFieldsByFieldRow($SubProductUID,$FieldRow_UID);
			
	  	  	$SubproductUID = $this->input->post('SubProductUID');
			$StateUID  = $StateLevel_Array['StateUID'];
			$FieldUID  = $StateLevel_Array['FieldUID'];
			$DocumentType  = $StateLevel_Array['DocumentType'];
			$TemplateUID  = $StateLevel_Array['TemplateUID'];
			$TemplateFileName  = $StateLevel_Array['TemplateFileName'];

				foreach($StateUID as $State)
				{
					foreach($FieldUID as $field)
					{
						$Fields['SubproductUID'] = $SubproductUID;
						$Fields['MappingLevel'] = $Mapping;
						$Fields['StateUID'] = $State;
						$Fields['FieldUID'] = $field;
						$Fields['FieldRow'] = $row;
						$mapfield = $this->Subproducts_model->SaveMappingField($Fields);
					}

					$TemplateFileName  = $StateLevel_Array['TemplateFileName'];

					$FilePath = "uploads/StateLevelTemplates/";
    				mkdir($FilePath, 0777, true);

					$file=FCPATH . $FilePath.'Statelevel_Template-'.$SubproductUID.'-'.$TemplateUID.'-'.$row.'.php';
					file_put_contents($file, $TemplateFileName);

					$temp_Map['SubproductUID'] = $SubproductUID;
					$temp_Map['StateUID'] = $State;
					$temp_Map['TemplateUID'] = $TemplateUID;
					$temp_Map['DocumentType'] = $DocumentType;
					$temp_Map['TemplateFileName'] = $file;
					$temp_Map['FieldRow'] = $row;
					$temp_Map['IsDefault'] = $IsDefault;
					$mapTemplate = $this->Subproducts_model->SaveMappingTemplate($temp_Map); 

				}

			if($mapTemplate==1 && $mapfield ==1)
			{
				$res = array("validation_error" => 0, 'message' => 'Sub Product Added Successfully', 'color'=>'success','SubProductUID'=>$SubproductUID);
			}
		}

		if($Mapping==3)
		{
			$FieldRow_UID  = $CountyLevel_Array['FieldRow'];
			$IsDefault = $this->Subproducts_model->GetIsDefaultFieldRow($SubProductUID,$FieldRow_UID);
			$AfterDeleteFieldRow = $this->Subproducts_model->DeleteOldMappedFieldsByFieldRow($SubProductUID,$FieldRow_UID);
	  	  	$SubproductUID = $this->input->post('SubProductUID');

			$StateUID  = $CountyLevel_Array['StateUID'];
			$CountyUID  = $CountyLevel_Array['CountyUID'];
			$FieldUID  = $CountyLevel_Array['FieldUID'];
			$DocumentType  = $CountyLevel_Array['DocumentType'];
			$TemplateUID  = $CountyLevel_Array['TemplateUID'];
			$TemplateFileName  = $CountyLevel_Array['TemplateFileName'];
			
			foreach($StateUID as $State)
			{
				foreach($FieldUID as $field)
				{
					$Fields['SubproductUID'] = $SubproductUID;
					$Fields['MappingLevel'] = $Mapping;
					$Fields['StateUID'] = $State;
					$Fields['FieldUID'] = $field;
					$Fields['FieldRow'] = $row;
					foreach($CountyUID as $county)
					{
						$Fields['CountyUID'] = $county;
						$mapfield = $this->Subproducts_model->SaveMappingField($Fields);
					}
				}	

				$TemplateFileName  = $CountyLevel_Array['TemplateFileName'];

				$FilePath = "uploads/CountyLevelTemplates/";
    			mkdir($FilePath, 0777, true);

				$file=FCPATH.$FilePath.'Countylevel_Template-'.$SubproductUID.'-'.$TemplateUID.'-'.$row.'.php';
				file_put_contents($file, $TemplateFileName);								

				$temp_Map['SubproductUID'] = $SubproductUID;
				$temp_Map['StateUID'] = $State;
				$temp_Map['TemplateUID'] = $TemplateUID;
				$temp_Map['DocumentType'] = $DocumentType;
				$temp_Map['TemplateFileName'] = $file;
				$temp_Map['FieldRow'] = $row;
				$temp_Map['IsDefault'] = $IsDefault;

				foreach($CountyUID as $county)
				{
					$temp_Map['CountyUID'] = $county;
					$mapTemplate = $this->Subproducts_model->SaveMappingTemplate($temp_Map); 
				}

			}
			
			if($mapTemplate==1 && $mapfield ==1)
			{
				$res = array("validation_error" => 0, 'message' => 'Sub Product Added Successfully', 'color'=>'success','SubProductUID'=>$SubproductUID);
			}	
		}

		foreach ($FieldPosition as $key => $value) {
			$this->UpdatePositionInProductFieldTable($value['Position'],$value['FieldUID'],$row);
		}

		echo json_encode($res);
	}


	function Update_SubProduct_test()
	{
		$SubProductUID = $this->input->post('SubProductUID');
		$SubProductDetails_array = $this->input->post('SubProductDetails');
		$SubProductLevel_Array_array = $this->input->post('SubProductLevel_Array');
		$StateLevel_Array_array = $this->input->post('StateLevel_Array');
		$CountyLevel_Array_array = $this->input->post('CountyLevel_Array');

		$SubProductDetails = $SubProductDetails_array[0];
		$SubProductLevel_Array = $SubProductLevel_Array_array[0];
		$StateLevel_Array = $StateLevel_Array_array[0];
		$CountyLevel_Array = $CountyLevel_Array_array[0];

		$Mapping = $SubProductDetails['MappingLevel'];
		$SubProductUID = $SubProductDetails['SubProductUID'];
		$data['SubProductCode'] = $SubProductDetails['SubProductCode'];
		$data['SubProductName'] = $SubProductDetails['SubProductName'];
		$data['ProductUID'] = $SubProductDetails['ProductUID'];
		$data['ReportHeading'] = $SubProductDetails['ReportHeading'];
		$data['CreatedOn'] = date('Y-m-d H:i:s');
		$data['Active'] = 1;
		
		$FieldRow = $this->Subproducts_model->GetFieldRowBySubProductUID($SubProductUID); 
		$row_before = $FieldRow->FieldRow;
		$row = $row_before + 1;

		if($Mapping==1)
		{
			$SubproductUID = $SubProductDetails['SubProductUID'];
			$Fields['SubproductUID'] = $SubproductUID;
			$Fields['MappingLevel'] = $Mapping;
			$FieldUID  = $SubProductLevel_Array['FieldUID'];
			$DocumentType  = $SubProductLevel_Array['DocumentType'];
			$TemplateUID  = $SubProductLevel_Array['TemplateUID'];
			$TemplateFileName  = $SubProductLevel_Array['TemplateFileName'];

			foreach($FieldUID as $field)
			{
				$Fields['FieldUID'] = $field;
				$Fields['FieldRow'] = $row;
				$mapfield = $this->Subproducts_model->SaveMappingField($Fields);				
			}

			$FilePath = "uploads/SubProductLevelTemplates/";
    		mkdir($FilePath, 0777, true);

			$file=FCPATH . $FilePath.'SubProductlevel_Template-'.$SubproductUID.'-'.$TemplateUID.'-'.$row.'.php';
			file_put_contents($file, $TemplateFileName);

			$tempte['SubproductUID'] = $SubproductUID;
			$tempte['TemplateUID'] = $TemplateUID;
			$tempte['DocumentType'] = $DocumentType;
			$tempte['TemplateFileName'] = $file;
			$tempte['FieldRow'] = $row;

			$stored = $this->Subproducts_model->SaveMappingTemplate($tempte);

			if($stored==1 && $mapfield ==1)
			{
				$res = array("validation_error" => 0, 'message' => 'Sub Product Added Successfully', 'color'=>'success','SubProductUID'=>$SubproductUID);
			}
		}

		if($Mapping==2)
		{
	  	  	$SubproductUID = $SubProductDetails['SubProductUID'];
			$StateUID  = $StateLevel_Array['StateUID'];
			$FieldUID  = $StateLevel_Array['FieldUID'];
			$DocumentType  = $StateLevel_Array['DocumentType'];
			$TemplateUID  = $StateLevel_Array['TemplateUID'];
			$TemplateFileName  = $StateLevel_Array['TemplateFileName'];

				foreach($StateUID as $State)
				{
					foreach($FieldUID as $field)
					{
						$Fields['SubproductUID'] = $SubproductUID;
						$Fields['MappingLevel'] = $Mapping;
						$Fields['StateUID'] = $State;
						$Fields['FieldUID'] = $field;
						$Fields['FieldRow'] = $row;
						$mapfield = $this->Subproducts_model->SaveMappingField($Fields);
					}

					$TemplateFileName  = $StateLevel_Array['TemplateFileName'];

					$FilePath = "uploads/StateLevelTemplates/";
    				mkdir($FilePath, 0777, true);

					$file=FCPATH . $FilePath.'Statelevel_Template-'.$SubproductUID.'-'.$TemplateUID.'-'.$row.'.php';
					file_put_contents($file, $TemplateFileName);

					$temp_Map['SubproductUID'] = $SubproductUID;
					$temp_Map['StateUID'] = $State;
					$temp_Map['TemplateUID'] = $TemplateUID;
					$temp_Map['DocumentType'] = $DocumentType;
					$temp_Map['TemplateFileName'] = $file;
					$temp_Map['FieldRow'] = $row;
					$mapTemplate = $this->Subproducts_model->SaveMappingTemplate($temp_Map); 

				}

			if($mapTemplate==1 && $mapfield ==1)
			{
				$res = array("validation_error" => 0, 'message' => 'Sub Product Added Successfully', 'color'=>'success','SubProductUID'=>$SubproductUID);
			}
		}

		if($Mapping==3)
		{
	  	  	$SubproductUID = $SubProductDetails['SubProductUID'];
			$StateUID  = $CountyLevel_Array['StateUID'];
			$CountyUID  = $CountyLevel_Array['CountyUID'];
			$FieldUID  = $CountyLevel_Array['FieldUID'];
			$DocumentType  = $CountyLevel_Array['DocumentType'];
			$TemplateUID  = $CountyLevel_Array['TemplateUID'];
			$TemplateFileName  = $CountyLevel_Array['TemplateFileName'];
			
			foreach($StateUID as $State)
			{
				foreach($FieldUID as $field)
				{
					$Fields['SubproductUID'] = $SubproductUID;
					$Fields['MappingLevel'] = $Mapping;
					$Fields['StateUID'] = $State;
					$Fields['FieldUID'] = $field;
					$Fields['FieldRow'] = $row;
					foreach($CountyUID as $county)
					{
						$Fields['CountyUID'] = $county;
						$mapfield = $this->Subproducts_model->SaveMappingField($Fields);
					}
				}	

				$TemplateFileName  = $CountyLevel_Array['TemplateFileName'];

				$FilePath = "uploads/CountyLevelTemplates/";
    			mkdir($FilePath, 0777, true);

				$file=FCPATH.$FilePath.'Countylevel_Template-'.$SubproductUID.'-'.$TemplateUID.'-'.$row.'.php';
				file_put_contents($file, $TemplateFileName);								

				$temp_Map['SubproductUID'] = $SubproductUID;
				$temp_Map['StateUID'] = $State;
				$temp_Map['TemplateUID'] = $TemplateUID;
				$temp_Map['DocumentType'] = $DocumentType;
				$temp_Map['TemplateFileName'] = $file;
				$temp_Map['FieldRow'] = $row;

				foreach($CountyUID as $county)
				{
					$temp_Map['CountyUID'] = $county;
					$mapTemplate = $this->Subproducts_model->SaveMappingTemplate($temp_Map); 
				}

			}
			
			if($mapTemplate==1 && $mapfield ==1)
			{
				$res = array("validation_error" => 0, 'message' => 'Sub Product Added Successfully', 'color'=>'success','SubProductUID'=>$SubproductUID);
			}	
		}

		$FieldPosition = $this->input->post('FieldPosition');

		foreach ($FieldPosition as $key => $value) {
			$this->UpdatePositionInProductFieldTable($value['Position'],$value['FieldUID'],$row);
		}

		echo json_encode($res);
	}
function GetSubProductName()
	{
		if ($this->input->server('REQUEST_METHOD') === 'POST'){
			$SubProductCode = $this->input->post('SubProductCode');

			$SubProductCheck = $this->Subproducts_model->GetSubProductUID($SubProductCode);

			$SubProductCheck = $SubProductCheck->SubProductCheck;

			if($SubProductCheck == 1){
				$SubProductCode = $this->input->post('SubProductCode');
				$details = $this->Subproducts_model->GetSubProductName($SubProductCode);
				$Msg = $this->lang->line('Successfull');
				$result = array("validation_error" => 0,"details" => $details,'message'=>$Msg);
			}
			else{
				$details = $this->Subproducts_model->GetProductDetails();
				$Msg = $this->lang->line('Successfull');
				$result = array("validation_error" => 1,"details" => $details,'message'=>$Msg);
			}

			echo json_encode($result);
			
			/*$details = $this->Subproducts_model->GetSubProductName($SubProductCode);*/

		}
	}
	function CopyMappingFields(){

		$FieldRow = $this->input->post('FieldRow');
		$SubProductUID = $this->input->post('SubProductUID');
		$result = $this->Subproducts_model->GetFieldsByFieldRowSubProduct($FieldRow,$SubProductUID);

		echo json_encode($result);

	}
	function EditMappingFieldsTemplates()
	{
		$FieldRow = $this->input->post('FieldRow');
		$SubProductUID = $this->input->post('SubProductUID');
		$MappingLevel = $this->input->post('MappingLevel');
		$result = $this->Subproducts_model->EditMappingFieldsTemplates($FieldRow,$SubProductUID,$MappingLevel);

		$GetTemplate=$this->Subproducts_model->GetTemplateFormatByFieldRow($FieldRow,$SubProductUID);
        $file = $GetTemplate->TemplateFileName;

        $Rep_msg = file_get_contents($file);

		$details = $result[0];

		$Result_MappingLevel = $details['MappingLevel'];

		if($Result_MappingLevel == 1){

			$FieldName = [];
			$FieldUID = $details['FieldUID'];

            $FieldUID_Explode = explode(',', $FieldUID);

            foreach ($FieldUID_Explode as $key => $value) {
              $FieldName[] = $this->Subproducts_model->GetFieldBySubProduct($value); 

            }

            $flattenArray = [];

			foreach ($FieldName as $childArray) {
			    foreach ($childArray as $value) {
			        $flattenArray[] = $value;
			    }
			}

			$Field_Content = array_combine($flattenArray, $FieldUID_Explode);


		    $data=array("MappingLevel"=>1,"details"=>$details,"Field_Content"=>$Field_Content,"Rep_msg"=>$Rep_msg);	

		    echo json_encode($data);

		}

		if($Result_MappingLevel == 2){

			//Fields

			$FieldName = [];
			$FieldUID = $details['FieldUID'];

            $FieldUID_Explode = explode(',', $FieldUID);

            foreach ($FieldUID_Explode as $key => $value) {
              $FieldName[] = $this->Subproducts_model->GetFieldBySubProduct($value); 

            }

            $FieldflattenArray = [];

			foreach ($FieldName as $childArray) {
			    foreach ($childArray as $value) {
			        $FieldflattenArray[] = $value;
			    }
			}

			$Field_Content = array_combine($FieldflattenArray, $FieldUID_Explode);

			//State

			$StateName = [];
			$StateUID = $details['StateUID'];

            $StateUID_Explode = explode(',', $StateUID);

            foreach ($StateUID_Explode as $key => $value) {
              $StateName[] = $this->Subproducts_model->GetStatesBySubProduct($value); 

            }

            $StateflattenArray = [];

			foreach ($StateName as $childArray) {
			    foreach ($childArray as $value) {
			        $StateflattenArray[] = $value;
			    }
			}

			$State_Content = array_combine($StateflattenArray, $StateUID_Explode);


		    $data=array("MappingLevel"=>2,"details"=>$details,"Field_Content"=>$Field_Content,"State_Content"=>$State_Content,"Rep_msg"=>$Rep_msg);	

		    echo json_encode($data);

		}

		if($Result_MappingLevel == 3){

			//Fields

			$FieldName = [];
			$FieldUID = $details['FieldUID'];

            $FieldUID_Explode = explode(',', $FieldUID);

            foreach ($FieldUID_Explode as $key => $value) {
              $FieldName[] = $this->Subproducts_model->GetFieldBySubProduct($value); 

            }

            $FieldflattenArray = [];

			foreach ($FieldName as $childArray) {
			    foreach ($childArray as $value) {
			        $FieldflattenArray[] = $value;
			    }
			}

			$Field_Content = array_combine($FieldflattenArray, $FieldUID_Explode);

			//State

			$StateName = [];
			$StateUID = $details['StateUID'];

            $StateUID_Explode = explode(',', $StateUID);

            foreach ($StateUID_Explode as $key => $value) {
              $StateName[] = $this->Subproducts_model->GetStatesBySubProduct($value); 

            }

            $StateflattenArray = [];

			foreach ($StateName as $childArray) {
			    foreach ($childArray as $value) {
			        $StateflattenArray[] = $value;
			    }
			}

			$State_Content = array_combine($StateflattenArray, $StateUID_Explode);

			//County

			$CountyName = [];
			$CountyUID = $details['CountyUID'];

            $CountyUID_Explode = explode(',', $CountyUID);

            foreach ($CountyUID_Explode as $key => $value) {
              $CountyName[] = $this->Subproducts_model->GetCountyBySubProduct($value); 

            }

            $CountyflattenArray = [];

			foreach ($CountyName as $childArray) {
			    foreach ($childArray as $value) {
			        $CountyflattenArray[] = $value;
			    }
			}

			$County_Content = array_combine($CountyflattenArray, $CountyUID_Explode);

		    $data=array("MappingLevel"=>3,"details"=>$details,"Field_Content"=>$Field_Content,"State_Content"=>$State_Content,"County_Content"=>$County_Content,"Rep_msg"=>$Rep_msg);	

		    echo json_encode($data);

		}
	}

	function EditFieldsAdding(){

		$FieldUID = $this->input->post('FieldPosition');
		$TemplateUID = $this->input->post('TemplateUID');
		$DynamincFieldUID = $this->input->post('DynamincFieldUID');

		$flattenArray = [];

		foreach ($FieldUID as $childArray) {
			foreach ($childArray as $value) {
				$flattenArray[] = $value;
			}
		}

		$Tbody_Fields = '';

		$mfields = $this->Subproducts_model->GetMFields();

		foreach($flattenArray as $field)
	  	{
			foreach ($mfields as $key => $row) {
		  		if($row->FieldUID == $field){

					$Fields = $this->Subproducts_model->GetFieldsByTemplateMappingUID($field);
	               
					$Tbody_Fields .='<tr style="background-color: #cdf7bf66;" data-fielduid="'.$Fields->FieldUID.'" data-fieldname="'.$Fields->FieldName.'">
					<td>' .$Fields->FieldName. '</td>
					<td class="text-center ">
	                    <div class="be-checkbox has-primary be-checkbox-color inline">
							<input type="checkbox" class="CheckFields" name="'.$Fields->FieldUID.'" data-value="'. $Fields->FieldUID.'" id="'. $Fields->FieldUID.'" data-display-name="'. $Fields->FieldName.'" data-keyword="'. $Fields->FieldKeyword.'" checked>
							<label for="'.$Fields->FieldUID.'" ></label>
						</div>
					</td>
					</tr>';
					$selected = 1; 
				}
		  	}
	    }

		foreach ($mfields as $key => $row) {

            $selected = 0;

			foreach($flattenArray as $field)
		  	{
		  		if($row->FieldUID == $field){
					$selected = 1; 
				}

		  	} 
		  	if($selected == 0)
		  	{
		  		$Tbody_Fields .='<tr data-fielduid="'.$row->FieldUID.'" data-fieldname="'.$row->FieldName.'">
					<td>' .$row->FieldName. '</td>
					<td class="text-center ">
						<div class="be-checkbox has-primary be-checkbox-color inline">
							<input type="checkbox" class="CheckFields" name="'.$row->FieldUID.'" data-value="'. $row->FieldUID.'" id="'. $row->FieldUID.'" data-display-name="'. $row->FieldName.'">
							<label for="'.$row->FieldUID.'" ></label>
						</div>
					</td>
					</tr>';
			}
		}


	  	$result = array("file" => $file,"Rep_msg" => $Rep_msg,"query" => $Tbody_Fields,"DynamincFieldUID" => $DynamincFieldUID,"TemplateUID" => $TemplateUID);

	  	echo json_encode($result);
	}
function previewtemplate(){

		$FieldUID = $this->input->post('FieldUID');
		$TemplateUID = $this->input->post('TemplateUID');
		$DynamincFieldUID = $this->input->post('DynamincFieldUID');
		
        $GetTemplate=$this->Subproducts_model->GetTemplateFormat($TemplateUID);
        $file = FCPATH. $GetTemplate->TemplatePath;

        $Rep_msg = file_get_contents($file);      


       // $file = $GetTemplate->TemplateTypeFile;



        /*$document_file = FCPATH.$GetTemplate->TemplateTypeFile;
		$text_from_doc = shell_exec('/usr/local/bin/antiword '.$document_file);*/


       /* $FileName = $GetTemplate->TemplateFileName;

        $target_path = FCPATH."assets/templates/temp/".$FileName.".php";*/

		$Tbody_Fields = '';

		$mfields = $this->Subproducts_model->GetMFields();

		foreach($FieldUID as $field)
		{
            $selected = 0;
			foreach ($mfields as $key => $row) {
			
		  		if($row->FieldUID == $field){

					$Fields = $this->Subproducts_model->GetFieldsByTemplateMappingUID($field);
                   
					$Tbody_Fields .='<tr style="background-color: #cdf7bf66;" data-fielduid="'.$Fields->FieldUID.'">
					<td>' .$Fields->FieldName. '</td>
					<td class="text-center">
					<button class="btn btn-space btn-social btn-color choose btn-xs" style="background-color: #7da74ffa;color: #fff;border: 1px solid #54a03b;" data-value="'. $Fields->FieldUID.'" data-display-name="'. $Fields->FieldName.'" data-keyword="'. $Fields->FieldKeyword.'"> <i class="fa fa-forward" aria-hidden="true" style="padding: 0;margin: 0;"></i></button>
					</td>
					</tr>';
					$selected = 1; 
				}
		  	} 
		  	/*if($selected == 0)
		  	{
		  		$Tbody_Fields .='<tr>
					<td>' .$row->FieldName. '</td>
					<td class="text-center">
					<button class="btn btn-space btn-social btn-color choose btn-xs" style="background-color: #7da74ffa;color: #fff;border: 1px solid #54a03b;" data-value="'. $row->FieldUID.'" data-display-name="'. $row->FieldName.'" data-keyword="'. $row->FieldKeyword.'"> <i class="fa fa-forward" aria-hidden="true" style="padding: 0;margin: 0;"></i></button>
					</td>
					</tr>';
			}*/
		}

	  	$result = array("file" => $file,"Rep_msg" => $Rep_msg,"query" => $Tbody_Fields,"DynamincFieldUID" => $DynamincFieldUID,"TemplateUID" => $TemplateUID);

	  	echo json_encode($result);
	}



		function Editpreviewtemplate(){

		$FieldRow = $this->input->post('FieldRow');
		$FieldUID = $this->input->post('FieldUID');
		$TemplateUID = $this->input->post('TemplateUID');
		$DynamincFieldUID = $this->input->post('DynamincFieldUID');
		$SubProductUID = $this->input->post('SubProductUID');

        $GetTemplate=$this->Subproducts_model->GetTemplateFormatByFieldRowUsingTemplate($FieldRow,$SubProductUID,$TemplateUID);
        if($GetTemplate){
   	    	$file = $GetTemplate->TemplateFileName;
   	    	$Rep_msg = file_get_contents($file);
        } else {
        	$Template=$this->Subproducts_model->GetTemplassteFormat($TemplateUID);
	        $file = FCPATH. $Template->TemplatePath;
	        $Rep_msg = file_get_contents($file);
        }

		$Tbody_Fields = '';

		$FieldRows = $this->GetFieldsByPosition($FieldUID,$FieldRow,$SubProductUID);

		$FieldRowsArray = [];

		foreach ($FieldRows as $childArray) {
		    foreach ($childArray as $value) {
		        $FieldRowsArray[] = $value;
		    }
		}

		/*echo '<pre>';print_r($FieldUID);
		echo '<pre>';print_r('***********************************');
		echo '<pre>';print_r($FieldRows);
		echo '<pre>';print_r('***********************************');

		echo '<pre>';print_r($FieldRowsArray);exit;*/

		$mfields = $this->Subproducts_model->GetMFields();


		foreach($FieldRowsArray as $field)
		{

			foreach ($mfields as $key => $row) {

            $selected = 0;

		  		if($row->FieldUID == $field){

					$Fields = $this->Subproducts_model->GetFieldsByTemplateMappingUID($field);

					$Tbody_Fields .='<tr style="background-color: #cdf7bf66;" data-postion="'.$Fields->FieldPosition.'" data-fielduid="'.$Fields->FieldUID.'">
					<td>' .$Fields->FieldName. '</td>
					<td class="text-center">
					<button class="btn btn-space btn-social btn-color choose btn-xs" style="background-color: #7da74ffa;color: #fff;border: 1px solid #54a03b;" data-value="'. $Fields->FieldUID.'"  data-display-name="'. $Fields->FieldName.'" data-keyword="'. $Fields->FieldKeyword.'" > <i class="fa fa-forward" aria-hidden="true" style="padding: 0;margin: 0;"></i></button>
					</td>
					</tr>';
					$selected = 1; 
				}
		  	} 
		  	/*if($selected == 0)
		  	{
		  		$Tbody_Fields .='<tr>
					<td>' .$row->FieldName. '</td>
					<td class="text-center">
					<button class="btn btn-space btn-social btn-color choose btn-xs" style="background-color: #7da74ffa;color: #fff;border: 1px solid #54a03b;" data-value="'. $row->FieldUID.'" data-display-name="'. $row->FieldName.'" data-keyword="'. $row->FieldKeyword.'"> <i class="fa fa-forward" aria-hidden="true" style="padding: 0;margin: 0;"></i></button>
					</td>
					</tr>';
			}*/
		}

	  	$result = array("Rep_msg" => $Rep_msg,"query" => $Tbody_Fields,"DynamincFieldUID" => $DynamincFieldUID,"TemplateUID" => $TemplateUID);

	  	//echo $Tbody_Fields;
	  	echo json_encode($result);

	  	/*
		$data['Action']="Mapping_EDIT";
		$data['Fields']=$this->Subproducts_model->GetFieldsByTemplateMappingUID($FieldUID);
		$data['TemplateUID']=$this->input->post('TemplateUID');
		$this->load->view('add_subproducts',$data);*/
	}
	function GetFieldsByPosition($FieldUID,$FieldRow,$SubProductUID){
		$this->db->select("mproductfield.FieldUID");
	    $this->db->from('mfields');
	    $this->db->join('mproductfield','mproductfield.FieldUID = mfields.FieldUID','INNER');
	    $this->db->where_in('mproductfield.FieldUID',$FieldUID);
	    $this->db->where('mproductfield.FieldRow',$FieldRow);
	    $this->db->where('mproductfield.SubProductUID',$SubProductUID);
	    $this->db->order_by('mproductfield.FieldPosition', "ASC");
	    $this->db->group_by('mproductfield.FieldUID');
	    $query = $this->db->get();
	    $Fields = $query->result();
	    return $Fields;
	}

	function ResetSubProductTemplate(){
		$TemplateUID = $this->input->post('TemplateUID');
		
        $GetTemplate=$this->Subproducts_model->GetTemplateFormat($TemplateUID);
        $file = FCPATH. $GetTemplate->TemplatePath;

        $Rep_msg = file_get_contents($file);

        $result = array("file" => $file,"Rep_msg" => $Rep_msg);

	  	echo json_encode($result);
	}

}
