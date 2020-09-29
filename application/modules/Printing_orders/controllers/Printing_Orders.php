<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Printing_Orders extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		// error_reporting(E_ALL);
		// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
		// 	redirect(base_url().'users');
		// }
		// else
		// {
		// 	$this->load->model('users/Mlogin');
			$this->lang->load('keywords');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');
			$this->load->model('Printing_Orders_Model');
			$this->load->model('Myorders/My_orders_model');

		// }
	}	


	public function index()
	{
		// $data['content'] = 'index';
		$login_id = $this->loggedid;  
		$data['products'] = $this->Printing_Orders_Model->GetProducts();
		$data['customers'] = $this->Printing_Orders_Model->GetCustomers($login_id);  
		$data['States'] = $this->common_model->GetStateDetails();  

		$data['content'] = 'advance_printing_orders';
		$data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$this->load->view('page', $data);
	}
	function GetPrintingOrders(){
		$loggedid = $this->loggedid; 
		//$data['content'] = 'index';
		$data['is_selfassign'] = $this->common_model->is_selfassign($loggedid);
		$data['printing_orders'] = $this->Printing_Orders_Model->get_print_orders($loggedid,$this->input->post());
		foreach ($data['printing_orders'] as $key => $value) 
		{
			$assignedusers =  $this->Printing_Orders_Model->get_assigned_users($value->OrderUID);
			$data['printing_orders'][$key]->LoginID = $assignedusers;
		}

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

		$data['OrderUID']='';
		$data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$this->load->view('index', $data);
	}


	function GetProjectByCustomer()
	{
		$CustomerUID= $this->input->post('CustomerUID');
		if (!is_numeric($CustomerUID)) {
			echo json_encode(array('validation_error'=>1, 'message'=>'Invalid Message')); exit;
		}
		$data['Projects']=$this->Printing_Orders_Model->GetProjectByCustomerUID($CustomerUID);
		$data['validation_error']=0;
		$data['message']="success";

		echo json_encode($data);


	}	


	function GetPrintingOrdersByOrderUID(){
		$loggedid = $this->loggedid; 
		//$data['content'] = 'index';
		$OrderUID=$this->input->post('OrderUID');
		$torders=$this->common_model->Gettorders($OrderUID);

		if (empty($torders)) {
			json_encode(array('validation_error'=>1, 'message'=>'Invalid Order !!!')); exit;
		}
		$mstates=$this->common_model->GetStatebyCode($torders->PropertyStateCode);

		$post['OrderType']='P';
		$post['ProjectUID']=$torders->ProjectUID;
		$post['CustomerUID']=$torders->CustomerUID;
		$post['MERS']=$torders->IsMERS;
		$post['StateUID']=$mstates->StateUID;
		$post['VP']=$mstates->IsVPAndAbove;


		$data['OrderUID']=$OrderUID;
		$data['printing_orders'] = $this->Printing_Orders_Model->get_print_orders($loggedid,$post);
		foreach ($data['printing_orders'] as $key => $value) 
		{
			$assignedusers =  $this->Printing_Orders_Model->get_assigned_users($value->OrderUID);
			$data['printing_orders'][$key]->LoginID = $assignedusers;
		}

		if ($post['CustomerUID']!='' && is_numeric($post['CustomerUID'])) {
			$content['Projects']=$this->Printing_Orders_Model->GetProjectByCustomerUID($post['CustomerUID']);
		}
		else{
			$content['Projects']=[];	
		}

		$content['CustomerUID']=$post['CustomerUID'];
		$content['ProjectUID']=$post['ProjectUID'];
		$content['StateUID']=$post['StateUID'];
		if ($post['MERS']==1) {
			$content['MERS']=1;
		}
		elseif ($post['MERS']==0) {
			$content['MERS']=2;
		}
		else{
			$content['MERS']='';
		}

		if ($post['VP']==1) {
			$content['VP']=1;
		}
		elseif ($post['VP']==0) {
			$content['VP']=2;
		}
		else{
			$content['VP']='';
		}

		$content['html']=$this->load->view('index', $data, true);
		$content['validation_error']=0;
		echo json_encode($content);
	}
	function ShowPrintModal()
	{
		$OrderDocuments = $this->input->post('OrderDocuments');

		if (empty($OrderDocuments)) {
			echo json_encode(array('validation_error'=>1, 'messge'=>'No Data Available')); exit;
		}

		$torders=$this->common_model->Gettorders($OrderDocuments[0]['OrderUID']);
		$OrderUID = $OrderDocuments[0]['OrderUID'];

		$tOrderInfoDetails = $this->db->query('SELECT * FROM torderinfo WHERE OrderUID='.$OrderUID.'')->row();
		$IsMERSAssignor = $tOrderInfoDetails->IsMERSAssignor;
		$IsMERSEndorser = $tOrderInfoDetails->IsMERSEndorser;

		$IsMERS = 0;
		if($IsMERSAssignor == 1 || $IsMERSEndorser == 1 ){
			$IsMERS = 1;
		}

		if (empty($torders) || empty($torders->ProjectUID)) {
			echo json_encode(array('validation_error'=>1, 'messge'=>'Invalid Data !!!')); exit;	
		}

		$OrderUIDs=[];
		foreach ($OrderDocuments as $file) {
			$OrderUIDs[]=$file['OrderUID'];
		}

		$CountyName=$torders->PropertyCountyName;
		$StateCode=$torders->PropertyStateCode;

		$CountyStateDetails = $this->GetCountyStateUID($CountyName,$StateCode);

		$CountyUID = $CountyStateDetails->CountyUID;
		$StateUID = $CountyStateDetails->StateUID;

		$data['alldocuments']=$alldocuments;
		$data['OrderUIDs']=$OrderUIDs;
		$data['Sigors'] = $this->common_model->GetProjectSignor($torders->ProjectUID, $IsMERS, $StateUID);
		$data['Notarys'] = $this->common_model->GetProjectNotary($torders->ProjectUID);
		$data['Title']="Print Document";
		if (count($OrderUIDs) == 1) {
			$data['OrderDocuments'] = $this->Printing_Orders_Model->GetOrderDocuments($OrderUIDs[0]);
		}
		else{
			$data['OrderDocuments'] = [];	
		}
		$this->load->view('print_popup', $data);

	}

	function ShowBulkPrintModal()
	{
		$OrderDocuments = $this->input->post('OrderDocuments');
		$OrderUIDs = [];
		if (empty($OrderDocuments)) {
			echo json_encode(array('validation_error'=>1, 'messge'=>'No Data Available')); exit;
		}

		
		foreach ($OrderDocuments as $key => $value) {
			$OrderUIDs[] = $value['OrderUID'];
		}

		// print_r($OrderDocuments); exit;
		
		$GroupOrders = $this->Printing_Orders_Model->GetOrdersGroupByState($OrderUIDs);
		// echo count($GroupOrders);
		foreach ($GroupOrders as $key => $sourcerow) {
			

			if (isset($GroupOrders[$key])) {

				$GroupOrders[$key]->MERS_Array = [];
				$GroupOrders[$key]->MERS_Array[] = $sourcerow->MERS;

				$OrderUIDs = explode(",", $sourcerow->OrderuIDs);

				if (!empty($OrderUIDs)) {

					// Calculate is MERS Or NON-MERS
					if($sourcerow->MERS == 'MERS'){
						$Source_IsMERS = 1;
					}
					else{
						$Source_IsMERS = 0;	
					}

					$torders=$this->common_model->Gettorders($OrderUIDs[0]);
					$CountyName=$torders->PropertyCountyName;
					$StateCode=$torders->PropertyStateCode;

					$CountyStateDetails = $this->GetCountyStateUID($CountyName,$StateCode);

					$CountyUID = $CountyStateDetails->CountyUID;
					$StateUID = $CountyStateDetails->StateUID;

					$Sigors = $this->common_model->GetProjectSignorName($torders->ProjectUID, $Source_IsMERS,$StateUID);
					$Notarys = $this->common_model->GetProjectNotaryName($torders->ProjectUID);
					
					$GroupOrders[$key]->IsVPAndAbove = $this->Printing_Orders_Model->GetSignorVPAndAboveForSignors($Sigors);

					foreach ($GroupOrders as $childkey => $childvalue) {
						if($childkey > $key){

							// Calculate is MERS Or NON-MERS
							if($childvalue->MERS == 'MERS'){
								$child_IsMERS = 1;
							}
							else{
								$child_IsMERS = 0;	
							}

							$Destination_OrderUIDs = explode(",", $GroupOrders[$childkey]->OrderuIDs);

							$Destination_torders=$this->common_model->Gettorders($Destination_OrderUIDs[0]);
							$CountyName=$Destination_torders->PropertyCountyName;
							$StateCode=$Destination_torders->PropertyStateCode;

							$CountyStateDetails = $this->GetCountyStateUID($CountyName,$StateCode);

							$CountyUID = $CountyStateDetails->CountyUID;
							$StateUID = $CountyStateDetails->StateUID;

							$Destination_Sigors = $this->common_model->GetProjectSignorName($Destination_torders->ProjectUID, $child_IsMERS,$StateUID);
							$Destination_Notarys = $this->common_model->GetProjectNotaryName($Destination_torders->ProjectUID);


							if ($Sigors == $Destination_Sigors && $Notarys == $Destination_Notarys) {
								$GroupOrders[$key]->OrderuIDs .= "," . $GroupOrders[$childkey]->OrderuIDs;
								
								// Add MERS/Non-Mers data
								$GroupOrders[$key]->MERS_Array[] = $childvalue->MERS;
								// Get VP for Signors
								$Destination__Signor_VP = $this->Printing_Orders_Model->GetSignorVPAndAboveForSignors($Destination_Sigors);

								$GroupOrders[$key]->IsVPAndAbove = array_unique(array_merge($GroupOrders[$key]->IsVPAndAbove, $Destination__Signor_VP));
								
								unset($GroupOrders[$childkey]);
								
							}
						}
					}
					
				}
			}

		}
		$data["TabData"] = $GroupOrders;

		// echo "<pre>"; print_r($GroupOrders);exit;

		$html = $this->load->view('partial_views/bulkprint_partialviews', $data, true);

		echo json_encode(['validation_error'=>0, 'messge'=>"Orders Fetch Successfull", 'html'=>$html]);


	}

	function GetCountyStateUID($CountyName,$StateCode){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mcounties' );
		$this->db->join('mstates','mstates.StateUID=mcounties.StateUID');
		$this->db->where(array('mcounties.CountyName'=>$CountyName, 'mstates.StateCode'=>$StateCode));
		$query = $this->db->get();
		return $query->row();
	}
	public function GenerateDocument()
	{

		$OrderUIDs=$this->input->post('OrderUID');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		/*$this->form_validation->set_rules('Signor', '', 'required');
		$this->form_validation->set_rules('Notary', '', 'required');*/
		//$this->form_validation->set_rules('Witness1', '', 'required');
		//$this->form_validation->set_rules('Witness2', '', 'required');
		$this->form_validation->set_message('required', 'This Field is required');

		$SignorUID=$this->input->post('Signor');
		$NotaryUID=$this->input->post('Notary');
		$Witness1=$this->input->post('Witness1');
		$Witness2=$this->input->post('Witness2');
		$mergedocs=$this->input->post('mergedocs');
		// $Date=date('m/d/Y');

		$Date=date('jS \D\A\Y \O\F F, Y');
		//$Date=date('m/d/Y');
		
		$Date=strtoupper($Date);
		/*if ($this->form_validation->run() == TRUE) {*/
			$this->load->model('reports/Report_model');
			$mSignor=$this->common_model->GetSignorBySignorUID($SignorUID);
			$mNotary=$this->common_model->GetNotaryByNotaryUID($NotaryUID);
			$Signor=$mSignor->SignorName;
			$Notary=$mNotary->NotaryPrintName;
			$DateOfExpiration=$mNotary->NotaryDateOfExpiration;
			$NotaryDateOfExpiration = '';
			if($DateOfExpiration){
				$NotaryDateOfExpiration = ($DateOfExpiration == "0000-00-00 00:00:00" ) ? '' : Date('m/d/Y',strtotime($DateOfExpiration));
			}

			$SignorTitle=$mSignor->SignorTitle;
			$NotaryStateName=$mNotary->StatePrintName;
			$NotaryCountyName=$mNotary->CountyPrintName;
			$NotaryAck=$mNotary->NotaryAck;
			
			$NotaryDetails = $this->GetNotaryAckByNotaryStateName($mNotary->StateName);

			$oNotaryAck = $NotaryDetails->NotaryAck;
			$IncorporatedState = $NotaryDetails->IncorporatedState;

			foreach ($OrderUIDs as $key => $OrderUID) {
				
				$GetTemplate = $this->Report_model->GetTemplateByOrderinFo($OrderUID);
				$doc = FCPATH.$GetTemplate->FilePathName;

				if (empty($GetTemplate->EndorserUID)) {
					$EndorserPrintName = $GetTemplate->EndorserPrintName;
				}
				else{
					$mEndorser=$this->db->get_where('mEndorser', array('EndorserUID'=>$GetTemplate->EndorserUID))->row();
					$EndorserPrintName=$mEndorser->EndorserPrintName;					
				}

				$torders=$this->common_model->Gettorders($OrderUID);
			    $OrderDocs_Path = $torders->OrderDocsPath; 

			    if($torders->PropertyStateCode == 'NY'){
			    	$pNotaryDetails = $this->GetNotaryAckByNotaryStateName('New York');
			    	$NotaryAck = $pNotaryDetails->NotaryAck;
			    }
			    else{
			    	$NotaryAck = $oNotaryAck;
			    }
			    $this->load->model('order_info/Order_info_model');

			    $torders = $this->common_model->Gettorders($OrderUID);
			    $OrderinfoDetails = $this->GetOrderinfoDetails($OrderUID);
				$mProjects = $this->Printing_Orders_Model->GetmProject($torders->ProjectUID);

			    $ProjectUID = $torders->ProjectUID;
				$IsUppercase = $this->Order_info_model->GetProjectDetailsForUpper($ProjectUID);
			    $Field = $this->Order_info_model->GetDynamicField($torders->SubProductUID);

			    $MERSAddress = '';
			    if ($torders->IsMERS) {
			    	$mstates = $this->common_model->GetStatebyCode($torders->PropertyStateCode);
			    	$MERSAddress = $mstates->MERSAddress1 . ', ' . $mstates->MERSAddress2 . ', ' . $mstates->MERSCityName . ' - ' . $mstates->MERSZipCode;
			    }

			    if (!empty($MERSAddress)) {
			    	$MERSAddress = ' of whose address is <span style="text-transform: uppercase;">' . $MERSAddress . '</span>';
			    }

			    if (!file_exists($doc)) {
					$doc = FCPATH . 'uploads/SampleTemplate/'.$torders->OrderNumber.'/'.$torders->OrderNumber.'-WithOut_Key_Report.php';
			    }

		        $date = date('Ymd');
			    if(empty($OrderDocs_Path))
			    {
			    	$query = $this->db->query("update torders SET OrderDocsPath ='uploads/searchdocs/".$date."/".$torders->OrderNumber."/"."' Where OrderUID=".$OrderUID);
			    	$OrderDocs_Path = 'uploads/searchdocs/'.$date.'/'.$torders->OrderNumber."/";
			    }

				$SavePreviewOrderNumber=$torders->OrderNumber;

				$GetTemplateUID = $this->Report_model->GetTemplateUID($OrderUID);
				$PageSize = $GetTemplateUID->PageSize;
				$MarginLeft = $GetTemplateUID->MarginLeft;
				$MarginRight = $GetTemplateUID->MarginRight;
				$MarginTop = $GetTemplateUID->MarginTop;
				$MarginBottom = $GetTemplateUID->MarginBottom;

				$FirstMarginTop = $GetTemplateUID->FirstMarginTop;
		        $FirstMarginBottom = $GetTemplateUID->FirstMarginBottom;
		        $FirstMarginRight = $GetTemplateUID->FirstMarginRight;
		        $FirstMarginLeft = $GetTemplateUID->FirstMarginLeft;

				if($FirstMarginTop){
		            $FirstTop = $FirstMarginTop;
		        } else {
		            $FirstTop = $MarginTop;
		        }

		        if($FirstMarginBottom){
		            $FirstBottom = $FirstMarginBottom;
		        } else {
		            $FirstBottom = $MarginBottom;
		        }

		        if($FirstMarginRight){
		            $FirstRight = $FirstMarginRight;
		        } else {
		            $FirstRight = $MarginRight;
		        }

		        if($FirstMarginLeft){
		            $FirstLeft = $FirstMarginLeft;
		        } else {
		            $FirstLeft = $MarginLeft;
		        }

				$filecontents = file_get_contents($doc);

				$filecontents = str_replace( 'style="color: blue;"'  , 'style="color: black;"' , $filecontents );

				// echo '<pre>'; print_r($filecontents); exit;

				$filecontents = str_replace( '<span style="color: black;" data-keyword="NotaryAck">&nbsp;</span>' , '<span data-keyword="NotaryAck">'.$NotaryAck.'</span>' , $filecontents );

				$filecontents = str_replace( '<span style="color: black;" data-keyword="Witness1">&nbsp;</span>' , '<span data-keyword="Witness1">'.$Witness1.'</span>' , $filecontents );
				$filecontents = str_replace( '<span style="color: black;" data-keyword="Witness2">&nbsp;</span>' , '<span data-keyword="Witness2">'.$Witness2.'</span>' , $filecontents );
				$filecontents = str_replace( '<span style="color: black;" data-keyword="Signor">&nbsp;</span>' , '<span data-keyword="Signor">'.$Signor.'</span>' , $filecontents );
				$filecontents = str_replace( '<span style="color: black;" data-keyword="Notary">&nbsp;</span>' , '<span data-keyword="Notary">'.$Notary.'</span>' , $filecontents );

				if($mProjects->IsPrintDate == 1){
					$filecontents = str_replace( '<span style="color: black;" data-keyword="PrintDate">&nbsp;</span>' , '<span data-keyword="PrintDate">'.$Date.'</span>' , $filecontents );
				} else {
					$filecontents = str_replace( '<span style="color: black;" data-keyword="PrintDate">&nbsp;</span>' , '<span data-keyword="PrintDate">__________</span>' , $filecontents );
				}			
				


				$filecontents = str_replace( '<span style="color: black;" data-keyword="IncorporatedState">&nbsp;</span>' , '<span data-keyword="IncorporatedState">'.$IncorporatedState.'</span>' , $filecontents );
				$filecontents = str_replace( '<span style="color: black;" data-keyword="NotaryDateOfExpiration">&nbsp;</span>' , '<span data-keyword="NotaryDateOfExpiration">'.$NotaryDateOfExpiration.'</span>' , $filecontents );
				

				$filecontents = str_replace( '<span style="color: black;" data-keyword="NotaryStateName">&nbsp;</span>' , '<span data-keyword="NotaryStateName">'.$NotaryStateName.'</span>' , $filecontents );
				$filecontents = str_replace( '<span style="color: black;" data-keyword="NotaryCountyName">&nbsp;</span>' , '<span data-keyword="NotaryCountyName">'.$NotaryCountyName.'</span>' , $filecontents );
				$filecontents = str_replace( '<span style="color: black;" data-keyword="SignorTitle">&nbsp;</span>' , '<span data-keyword="SignorTitle">'.$SignorTitle.'</span>' , $filecontents );
				$filecontents = str_replace( '<span style="color: black;" data-keyword="EndorserPrintName">&nbsp;</span>' , '<span data-keyword="EndorserPrintName">'.$EndorserPrintName.'</span>' , $filecontents );
				$filecontents = str_replace( '<span style="color: black;" data-keyword="MERSAddress">&nbsp;</span>' , '<span data-keyword="MERSAddress">'.$MERSAddress.'</span>' , $filecontents );

				if($mProjects->IsPrintDate == 1){
					$filecontents = str_replace( '%%PrintDate%%' , '<span data-keyword="PrintDate">'.$Date.'</span>' , $filecontents );
				} else {
					$filecontents = str_replace( '%%PrintDate%%' , '<span data-keyword="PrintDate">__________</span>' , $filecontents );
				}
				


				$filecontents = str_replace( '%%Signor%%' , '<span data-keyword="Signor">'.$Signor.'</span>' , $filecontents );
				$filecontents = str_replace( '%%SignorTitle%%' , '<span data-keyword="SignorTitle">'.$SignorTitle.'</span>' , $filecontents );
				$filecontents = str_replace( '%%EndorserPrintName%%' , '<span data-keyword="EndorserPrintName">'.$EndorserPrintName.'</span>' , $filecontents );
				$filecontents = str_replace( '%%MERSAddress%%' , '<span data-keyword="MERSAddress">'.$MERSAddress.'</span>' , $filecontents );
				$filecontents = str_replace( '%%Witness1%%' , '<span data-keyword="Witness1">'.$Witness1.'</span>' , $filecontents );
				$filecontents = str_replace( '%%Witness2%%' , '<span data-keyword="Witness2">'.$Witness2.'</span>' , $filecontents );
				$filecontents = str_replace( '%%Notary%%' , '<span data-keyword="Notary">'.$Notary.'</span>' , $filecontents );
				$filecontents = str_replace( '%%NotaryDateOfExpiration%%' , '<span data-keyword="NotaryDateOfExpiration">'.$NotaryDateOfExpiration.'</span>' , $filecontents );
				$filecontents = str_replace( '%%NotaryStateName%%' , '<span data-keyword="NotaryStateName">'.$NotaryStateName.'</span>' , $filecontents );
				$filecontents = str_replace( '%%NotaryCountyName%%' , '<span data-keyword="NotaryCountyName">'.$NotaryCountyName.'</span>' , $filecontents );
				$filecontents = str_replace( '%%SignorTitle%%' , '<span data-keyword="SignorTitle">'.$SignorTitle.'</span>' , $filecontents );
				$filecontents = str_replace( '%%IncorporatedState%%' , '<span data-keyword="IncorporatedState">'.$IncorporatedState.'</span>' , $filecontents );


				/*Assignor & Assignee Details repopulate */
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssignorPrintName">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssignorPrintName">'.$OrderinfoDetails->AssignorPrintName.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssignorAddress1">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssignorAddress1">'.$OrderinfoDetails->AssignorAddress1.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssignorAddress2">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssignorAddress2">'.$OrderinfoDetails->AssignorAddress2.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssignorCityName">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssignorCityName">'.$OrderinfoDetails->AssignorCityName.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssignorStateName">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssignorStateName">'.$OrderinfoDetails->AssignorStateName.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssignorZipCode">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssignorZipCode">'.$OrderinfoDetails->AssignorZipCode.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssignorPrintName">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssignorPrintName">'.$OrderinfoDetails->AssignorPrintName.'</span>' , $filecontents );


				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssigneePrintName">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssigneePrintName">'.$OrderinfoDetails->AssigneePrintName.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssigneeAddress1">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssigneeAddress1">'.$OrderinfoDetails->AssigneeAddress1.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssigneeAddress2">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssigneeAddress2">'.$OrderinfoDetails->AssigneeAddress2.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssigneeCityName">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssigneeCityName">'.$OrderinfoDetails->AssigneeCityName.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssigneeStateName">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssigneeStateName">'.$OrderinfoDetails->AssigneeStateName.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssigneeZipCode">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssigneeZipCode">'.$OrderinfoDetails->AssigneeZipCode.'</span>' , $filecontents );
				$filecontents = preg_replace( '/<span style="color: black;" data-keyword="AssigneePrintName">(.*?)<\/span>/' , '<span style="color: black;" data-keyword="AssigneePrintName">'.$OrderinfoDetails->AssigneePrintName.'</span>' , $filecontents );



				$this->db->where('OrderUID', $OrderUID);
				$this->db->update('torderinfo', ['Signor'=> $Signor, 'Notary'=>$Notary, 'Witness1'=>$Witness1, 'Witness2'=> $Witness2, 'SignorTitle'=>$SignorTitle]);



				// if (!empty($mProjects)) {
				    $barcodesource = $this->BarcodeGenerator($torders);

					$filecontents = str_replace( '<span style="color: black;" data-keyword="Barcode">&nbsp;</span>' , '<img src="'.$barcodesource.'" data-keyword="Barcode"/>' , $filecontents );
				// }
				// echo $filecontents; exit;

				foreach($Field as $f)
		        {  
		        	$FieldName = $f->FieldName;
		        	if($IsUppercase->IsUpperCase=='1') {
		        		$post_name = strtoupper($OrderinfoDetails->$FieldName);
		        	} else {
		        		$post_name = $OrderinfoDetails->$FieldName;
		        	}

		        	if($f->FieldName === 'MIN' )
		        	{
		        		if($post_name){
		        			$filecontents = str_replace( '%%'.$f->FieldKeyword.'%%'  , '<span data-keyword="'.$f->FieldName.'"> MIN:'.$post_name.'</span>' , $filecontents );
		        		} else {
		        			$filecontents = str_replace( '%%'.$f->FieldKeyword.'%%'  , '<span data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $filecontents );
		        		}
		        	}

		        	else if($f->FieldName === 'MERSPhoneNo' )
		        	{
		        		if($post_name){
		        			$filecontents = str_replace( '%%'.$f->FieldKeyword.'%%'  , '<span data-keyword="'.$f->FieldName.'"> MERS Phone :'.$post_name.'</span>', $filecontents );
		        		} else {
		        			$filecontents = str_replace( '%%'.$f->FieldKeyword.'%%'  , '<span data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $filecontents );
		        		}
		        	} 

		        	else if($f->FieldName === 'Comments' )
		        	{
		        		if($post_name){
		        			$filecontents = str_replace( '%%'.$f->FieldKeyword.'%%'  , '<span data-keyword="'.$f->FieldName.'"> Comments :'.$post_name.'</span>', $filecontents );
		        		} else {
		        			$filecontents = str_replace( '%%'.$f->FieldKeyword.'%%'  , '<span data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $filecontents );
		        		}
		        	} 

		        	else if($f->FieldName === 'LegalDescription' )
		        	{
		        		if($post_name){
		        			$filecontents = str_replace( '%%'.$f->FieldKeyword.'%%'  , '<span data-keyword="'.$f->FieldName.'"> Legal Description :'.$post_name.'</span>', $filecontents );
		        		} else {
		        			$filecontents = str_replace( '%%'.$f->FieldKeyword.'%%'  , '<span data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $filecontents );
		        		}
		        	}

		          	else if($f->FieldDataType == 'textarea')
		            {
		                $filecontents = str_replace('%%'.$f->FieldKeyword.'%%'  , '<span data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $filecontents );                
		            }

		            elseif($f->FieldDataType == 'input')
		            {
		                $filecontents = str_replace('%%'.$f->FieldName.'%%',  '<span data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $filecontents );
		            }
		            
		            elseif($f->FieldDataType == 'date'){
		            	$filecontents = str_replace( '%%'.$f->FieldKeyword.'%%'  , '<span data-keyword="'.$f->FieldName.'">'.$post_name.'</span>' , $filecontents );
		        	}
		        } 

				$filecontents.= "  <style> @page :first {margin-top:".$FirstTop."mm;margin-bottom:".$FirstBottom."mm;margin-left:".$FirstLeft."mm;margin-right:".$FirstRight."mm; }  @page {sheet-size:".$PageSize.";header: html_MyCustomHeader;footer: html_MyCustomFooter;margin-top:".$MarginTop."mm;margin-bottom:".$MarginBottom."mm;margin-left:".$MarginLeft."mm;margin-right:".$MarginRight."mm; }</style> ";


				$this->load->library('pdf');
				$param = '"en-GB-x","'.$PageSize.'","","","'.$MarginLeft.'","'.$MarginRight.'","'.$MarginTop.'","'.$MarginBottom.'",6,3';


				$torderpropertyroles=$this->common_model->Gettorderpropertyroles($OrderUID);
				$propertyroles = [];
				foreach ($torderpropertyroles as $key => $pr) {
					array_push($propertyroles, $pr->PRName);
				}
				$borrower_name = implode('\\', $propertyroles);
				$firstborrowername = $propertyroles[0];

				$mProjects = $this->Printing_Orders_Model->GetmProjectByProjectUID($torders->ProjectUID);
				// Document File Name i.e LN0001_suresh.pdf
				$DocumentFileName=($torders->LoanNumber != '' ? $torders->LoanNumber : $torders->OrderNumber ). $mProjects->AssignmentCode;

				$data['OrderUID'] = $OrderUID;

				$reportfilenamecount=0;
				$data['DocumentFileName'] = $this->common_model->GetAvailFileName($DocumentFileName,'.pdf',$reportfilenamecount, $OrderUID);

				$data['extension']='pdf';
				$data['IsReport'] = 1;
				$data['TypeOfDocument'] = 'Final Reports';
				$data['DocumentCreatedDate'] = date('Y-m-d H:i:s');
				$data['UploadedUserUID'] =  $this->loggedid;
				$data['DisplayFileName'] =  $data['DocumentFileName'];

				$this->load->model('order_search/ordersearch_model');

				$checkdates= $this->ordersearch_model->CheckSearchDateExists($data['OrderUID']);
				$checkdates = $checkdates->CheckDates;

				if ($checkdates == 0) {
					$data['SearchAsOfDate'] = "0000-00-00 00:00:00";
					$data['SearchFromDate'] = "0000-00-00 00:00:00";
				}
				else
				{
					$this->db->select('MAX(SearchAsOfDate) AS SearchAsOfDate, MAX(SearchFromDate) AS SearchFromDate', false)->from('torderdocuments');
					$this->db->where('OrderUID',$data['OrderUID']);
					$result=$this->db->get();
					$torderdocuments=$result->row();

					$data['SearchAsOfDate']=$torderdocuments->SearchAsOfDate;
					$data['SearchFromDate']=$torderdocuments->SearchFromDate;
				}


				$result = $this->db->insert('torderdocuments', $data);

				if (!is_dir(FCPATH.$OrderDocs_Path)) {
					if (!mkdir(FCPATH.$OrderDocs_Path, 0777, true)) {
						die('Unable to Create Directory');
					}
				}

				unset($pdf);
				$pdf = $this->pdf->load($param);
				$pdf->SetTitle($DocumentFileName);
				$pdf->packTableData = true;
				$pdf->shrink_tables_to_fit = 0;
				$html = mb_convert_encoding($filecontents, 'UTF-8', 'UTF-8');
				$pdf->WriteHTML($html); 
				$page_count = $pdf->page;
				$dir=FCPATH.$OrderDocs_Path.$data['DocumentFileName'];
				$pdf->Output($dir, 'F');
				$pdffiles= [];

				if (!empty($mergedocs)) {
					$pdffiles[] = FCPATH.$OrderDocs_Path.$data['DocumentFileName'];
				}
				foreach ($mergedocs as $key => $value) {
					# code...
					$pdffiles[] = FCPATH.$OrderDocs_Path.$value;	
				}
				$outputName = FCPATH.$OrderDocs_Path.$DocumentFileName . '_temp.pdf';

				$cmd = 'gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile="'.$outputName.'" ';
				//Add each pdf file to the end of the command
				foreach($pdffiles as $file) {
					$cmd .= '"'. $file.'" ';
				}

				$cmd .= '-c "[ /Title ('.$DocumentFileName.') /DOCINFO pdfmark" -f ';
				if (!empty($pdffiles)) {
					$result = shell_exec($cmd);
					chmod($outputName, 0777);
					// shell_exec('pdftk file.pdf update_info in.info output newfile.pdf')
					file_put_contents(FCPATH.$OrderDocs_Path.$data['DocumentFileName'], file_get_contents($outputName));
					chmod(FCPATH.$OrderDocs_Path.$data['DocumentFileName'], 0777);
					
				}
				


				

				$previewpdf = base_url().$OrderDocs_Path.$data['DocumentFileName'];
			}
			echo json_encode(array('validation_error'=>0, 'message'=>'Print Completed', 'source'=>$previewpdf, 'title'=>$DocumentFileName));
		/*}*/
		/*else{
			$data = array(
				'validation_error' => 1,
				'message' =>'Please Fill The Required Fields',
				'Signor' => form_error('Signor'),
				'Notary' => form_error('Notary'),
				'Witness1' => form_error('Witness1'),
				'Witness2' => form_error('Witness2')
			);
			foreach($data as $key=>$value)
			{
				if(is_null($value) || $value == '')
					unset($data[$key]);
			}
			echo json_encode($data);
		}*/
	}


	function merge_download()
	{	
		$this->load->library('PDFMerger1');

		$documents = $this->input->post('documentfilenames');
		//$email = $this->load->library('email');

		$pdfmerger=new pdfmerger();
		$pdffiles=array();
		$propertyroles=[];
		foreach ($documents as $doc) {

			$sql = " SELECT * FROM torders 
			WHERE OrderUID = '".$doc['orderid']."' ";

			$query = $this->db->query($sql);
			$torders = $query->row();

			if(file_exists(FCPATH. $torders->OrderDocsPath. $doc['documentname']))
			{
				$pdfmerger->addPDF(FCPATH. $torders->OrderDocsPath. $doc['documentname'], 'all');
			}
		}

		$savefilename = 'MergedFinalReport.pdf';

		$outputName = FCPATH. $torders->OrderDocsPath.$savefilename;

		if ($pdfmerger->merge('S',$outputName)) {
			//echo 'success';
		}

	}


	function SingleDownload()
	{	
		$this->load->helper('download');

		$documents = $this->input->post('documentname');
		$orderid = $this->input->post('orderid');

		$sql = " SELECT * FROM torders 
		WHERE OrderUID = '" . $orderid ."' ";

		$query = $this->db->query($sql);
		$torders = $query->row();

		if(file_exists(FCPATH. $torders->OrderDocsPath. $documents))
		{

			$path = FCPATH. $torders->OrderDocsPath. $documents;
			$filename = $documents;
			header('Content-Transfer-Encoding: binary');  // For Gecko browsers mainly
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
			header('Accept-Ranges: bytes');  // For download resume
			header('Content-Length: ' . filesize($path));  // File size
			header('Content-Encoding: none');
			header('Content-Type: application/pdf');  // Change this mime type if the file is not PDF
			header('Content-Disposition: attachment; filename=' . $filename);  // Make the browser display the Save As dialog
			readfile($path);
		}
	}

	function merge_print(){
		$this->load->library('PDFMerger1');

		$documents = $this->input->post('documentfilenames');
		//$email = $this->load->library('email');
		$pdfmerger=new pdfmerger();
		$pdffiles=array();
		$propertyroles=[];
		foreach ($documents as $doc) {

			$sql = " SELECT * FROM torders 
			WHERE OrderUID = '" . $doc['orderid'] ."' ";

			$query = $this->db->query($sql);
			$torders = $query->row();

			if(file_exists(FCPATH. $torders->OrderDocsPath. $doc['documentname']))
			{
				$pdfmerger->addPDF(FCPATH. $torders->OrderDocsPath. $doc['documentname'], 'all');
			}
		}

		$savefilename = 'MergedFinalReport.pdf';

		$outputName = FCPATH. $torders->OrderDocsPath.$savefilename;
		// echo $pdfmerger->merge('',$outputName);
		if ($pdfmerger->merge('file',$outputName)) {
			
		}		
		echo $PDFPath = base_url().$torders->OrderDocsPath.$savefilename;
	}
		function print_complete(){
		$documents = $this->input->post('documentfilenames');
		$WorkflowName = 'Printing';
		foreach ($documents as $doc) {
			$OrderUID = $doc['orderid'];

			$this->db->query("UPDATE torders SET IsPrint = '1' WHERE OrderUID = '".$doc['orderid']."' ");

			$assign_data = array(
				'OrderUID' => $OrderUID,
				'WorkflowModuleUID' => '5',
				'AssignedToUserUID' => $this->loggedid,
				'AssignedDatetime' => Date('Y-m-d H:i:s',strtotime("now")),
				'CompleteDateTime' => Date('Y-m-d H:i:s',strtotime("now")),
				'AssignedByUserUID' => $this->loggedid,
				'WorkflowStatus' => '5'
			);

			$inserted = $this->db->insert('torderassignment',$assign_data);

			//audit trail
			$data1['ModuleName']='Printing'.' '.'workflow Status-Status';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='torderassignment';
			$data1['OrderUID']=$orderUID;
			$data1['UserUID']=$this->session->userdata('UserUID'); 
			$data1['OldValue']=''; 
			$data1['FieldUID']='729';
			$data1['NewValue']=$username->UserName; 
			$this->common_model->Audittrail_insert($data1); 

			$this->db->where('OrderUID',$OrderUID);
			$res = $this->db->update('torders',array('StatusUID'=>'100','IsPrint'=>'1', 'OrderCompleteDateTime' => Date('Y-m-d H:i:s',strtotime("now"))));
			if($res){
				$res = array('status' => 1 ,'message'=> 'Order is Completed' );
			} else {
				$res = array('status' => 0 ,'message'=> 'Unable to Complete this Order');
			}		
		}
		echo json_encode($res);
	}


}
