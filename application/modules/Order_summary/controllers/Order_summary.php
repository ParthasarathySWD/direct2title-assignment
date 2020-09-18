<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
class Order_summary extends MY_Controller {

	function __construct()
		{
			parent::__construct();
			$this->load->library('USPS');
			// $this->load->model('common_model');
			$this->load->helper('customer_pricing');
			$this->load->library(array('form_validation'));

			// if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
			// 	redirect(base_url().'users');
			// }
			// else{
				// $this->load->model('users/Mlogin');
				$this->load->model('Order_entry/Orderentry_model');
				// $this->load->model('fees_pricing/fees_pricing_model');
				// $this->load->model('attachments_new/attachments_new_model');
				$this->lang->load('keywords');
				$this->loggedid = $this->session->userdata('UserUID');
				$this->RoleUID = $this->session->userdata('RoleUID');
				$this->UserName = $this->session->userdata('UserName');
				$this->load->model('Order_Summary_Model');
				$this->load->model('common_model'); 
				date_default_timezone_set('US/Eastern');
				$this->load->model('real_ec_model');
			// }
		}	


	public function index()
	{
		// $data['content'] = 'index';
		if(isset($_GET['OrderUID']))
			{
			//$OrderUID = $this->session->userdata("scope_order_id");

				// $this->load->model('schedule/Schedule_model');
				$OrderUID = $_GET['OrderUID'];

				$OrderID = $this->common_model->GetOrderByID($OrderUID);

				$OrderNumber = $OrderID->OrderUID;

				if($OrderNumber == 0)
				{
					redirect(base_url().'Myorders');
				}

			//$OrderUID = $this->session->userdata("scope_order_id");

				$OrderrUID = $_GET['OrderUID'];	
				$OrderUID = str_replace('/', '', $OrderrUID);

				$data['OrderUID'] = $OrderUID;	
				$data['CaseSen'] = $this->common_model->get_CaseSen($OrderUID)->CaseSensitivity;
			// $OrderUID = $_GET['OrderUID'];		
				$UserUID = $this->session->userdata('UserUID');

				$data['content'] = 'index';

				$status = $this->Order_Summary_Model->ChangeOrderStatus($OrderUID);

				$data['States'] = $this->common_model->GetStateDetails();
				$data['Sub_products'] = $this->common_model->GetSub_productDetails($ProductUID = '1');
				$data['Products'] = $this->common_model->GetProductDetails();
				$data['Prioritys'] = $this->common_model->GetPriorityDetails();
				$data['InsuranceTypeDet'] = $this->common_model->GetInsuranceType();
			//$data['Templates'] = $this->common_model->GetTemplateDetails();
				$data['Customers'] = $this->common_model->GetCustomerDetails();
				$data['Prop_roles'] = $this->common_model->GetPropertyrolesDetails();

				$data['TransactionTypeDetails'] = $this->common_model->GetTransactionTypeDetails();
				$data['PropertyTypeDetails'] = $this->common_model->GetPropertyTypeDetails();
				$data['BorrowerDetails'] = $this->common_model->GetBorrowerDetailsDescription();
				$data['IsX1ResubmitOrder'] = $this->common_model->CheckEventLogOrders($OrderUID);
				$data['CheckX1Order'] = $this->Order_Summary_Model->GetX1Order($OrderUID);
				$data['CheckX1OrderInMyapproval'] = $this->Order_Summary_Model->CheckX1OrderInMyapproval($OrderUID);
				$data['X1Enabled'] = $this->common_model->GetX1EnabledforCustomerSubproduct($OrderUID);
				/*Query Chagned as per Closing Requirement done by Parthasarathy*/
				/*$data['Ordertypes'] = $this->Order_Summary_Model->GetOrdersOrderTypes($OrderUID);*/
			//$data['CompleteDetails']= $this->common_model->GetCompleteDetails($OrderUID);

				$data['order_details'] = $this->common_model->get_orderdetails($OrderUID);
				
				$data['CustomerSchedule']= $this->Order_Summary_Model->getCustomerSchedule($data['order_details']);
				$data['order_import_data'] = $this->Order_Summary_Model->get_torderimportdata($OrderUID);			
				$data['prop_details'] = $this->Order_Summary_Model->get_propdetails($OrderUID);

			// $data['InsuranceTypeDetails'] = array('1'=>'1','2'=>'2');
				$data['mPartner'] = $this->Order_Summary_Model->get_allpartners();

				$OrderSourceUID = $data['order_details']->OrderSourceUID;

				$data['notesection'] = $this->Order_Summary_Model->GetNotessection();

				if($OrderSourceUID == 2){
					$data['ApiRequest'] = $this->Order_Summary_Model->GetStewartApiRequest();
				} 


				$torders=$this->common_model->GettordersbyUID($OrderUID);

				$data['OptionalWorkflows'] = $this->common_model->GetOptionalWorkflowModules($torders[0]->CustomerUID, $torders[0]->SubProductUID, $OrderUID);

				$CustomerUID=$torders[0]->CustomerUID;
				$SubProductUID=$torders[0]->SubProductUID;
			// $OrderUID=$this->input->post('OrderUID');
				$PropertyZipcode=$torders[0]->PropertyZipcode;
				$CityUID=$torders[0]->PropertyCity;
				$CountyUID=$torders[0]->PropertyCountyUID;
				$StateUID=$torders[0]->PropertyStateUID;
				$ProductUID=1;
				$OrderTypeUID=$torders[0]->OrderTypeUID;
				$PriorityUID=$torders[0]->PriorityUID;
				$SubProduct = $this->common_model->GetSubProductDetails($SubProductUID);
				$ProductUID = $SubProduct->ProductUID;
				/**********@author praveen kumar - 10 JAN 2019**********/
				$data['Ordertypes'] = $this->common_model->GetOrderTypeDetails(['SubProductUID'=>$SubProductUID]);	// @Desc Changed from ProductUID to SubProductUIDs @Author Jainulabdeen @Updated Aug 31 2020

				$data['Customer']=$this->Order_Summary_Model->GetCustomerDetails($CustomerUID);
				$data['CustProducts']=$this->Order_Summary_Model->GetCustomerProducts($CustomerUID);
				$data['CustSubProducts']=$this->Order_Summary_Model->GetCustomerSubProductByID($CustomerUID, $SubProductUID, $ProductUID);
				$data['CustPricing']=$this->Order_Summary_Model->GetCustomerProductPricing($CustomerUID,$SubProductUID, $ProductUID, $StateUID, $CountyUID);
				$data['CustTemplate']=$this->Order_Summary_Model->GetCustomerTemplate($CustomerUID, $SubProductUID, $ProductUID);
				$data['CustWorkflowModules']=$this->Order_Summary_Model->GetCustomerWorkflows($CustomerUID, $SubProductUID, $ProductUID);

				$data['CustPriorities']=$this->Order_Summary_Model->GetCustomerPriorities($CustomerUID, $SubProductUID, $ProductUID, $PriorityUID);
				$data['CustOrderTypeDocs']=$this->Order_Summary_Model->GetCustomerOrderTypeDoc($CustomerUID, $OrderTypeUID);
				$data['CustDefaultProduct']=$this->Order_Summary_Model->GetCustomerDefaultSubProducts($CustomerUID);

				/*@purpose: To show additional fields for an order  @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: May 21st 2020*/
				$data['Fields'] = $this->common_model->GetMaterFieldList($OrderUID, $CustomerUID, $SubProductUID);

				$data['ProjectDetails']=$this->common_model->GetProjectDetails($ProductUID,$CustomerUID);

				// $data['Templates'] = $this->GetTemplateDetailsByProduct($ProductUID,$OrderUID);

				$data['pricing_details'] = $this->Order_Summary_Model->get_customer_vendorpricing($OrderUID);

				$CopyOrderDetails=$this->Order_Summary_Model->GetDuplicateDetails($data['order_details']);
				$data['CopyCountOrder'] = count($CopyOrderDetails);

				$data['Name'] = $this->UserName;
				// $data['Elapsetime'] = $this->gettime($OrderUID);
				$data['is_customerpricing_abstractorfee'] = $this->common_model->is_customerpricing_abstractorfee($this->RoleUID);
				$data['RoleDetails'] = $this->common_model->GetRole_Details($this->RoleUID);
				$data['encrypt'] = new AesCtr();
				$data['ParentCompanyDetails'] = $this->Order_Summary_Model->get_parentpartnerdetails();

			// @author Praveen Kumar -- DI1-T81 - check the bill & complete workflow exists
				$customerworkflowuids = array_column($data['CustWorkflowModules'], 'WorkflowModuleUID');
				$data['Billcompletebutton'] = 0;
				if(in_array($this->config->item('WorkflowModuleUID')['Bill_Complete'], $customerworkflowuids) && ($data['order_details']->StatusUID != $this->config->item('keywords')['Order Completed'] && $data['order_details']->StatusUID != $this->config->item('keywords')['Billed'] && $data['order_details']->StatusUID != $this->config->item('keywords')['Cancelled']))
				{

					$data['Billcompletebutton'] = 1;

				}

				if ($data['order_details']->IsClosingProduct == 1) {
					$data['tOrderClosingTemp'] = $this->common_model->get_row('tOrderClosingTemp', ['OrderUID'=>$OrderUID]);
				}
				$data['documentstatus'] = $this->config->item('RMS_DocumentStatus');

            //condition to display xml reader button 
			//$data['apiOrderCount'] = $this->common_model->apiOrdersCount($OrderUID);

				$this->load->view('page', $data);
			}
			else
			{
				redirect(base_url().'Myorders');	
			}
		// $this->load->view('page', $data);
	}


	function save_roles()
		{	
			error_reporting(0);
			$data['content'] = 'index';
			$data['data'] = array('menu'=>'OrderSummary','title'=>'Order Summary','link'=>array('OrderSummary'));
			$post = $this->input->post();

			$chk_mailing = $this->input->post('chk_mailing');
			$chk_Signing = $this->input->post('chk_Signing');
			$OrderDetails = $this->input->post();
				// str_replace to remove special char like '<>/\"
			$OrderDetails = str_replace(array('\'', '"', '<', '>', '\\'), '', $OrderDetails); 
			$OrderUID = $OrderDetails['OrderUID'];
			if ($this->input->server('REQUEST_METHOD') === 'POST'){

				$this->form_validation->set_error_delimiters('', '');
			//$this->form_validation->set_rules('OrderNumber', '', 'required');
			// $this->form_validation->set_rules('AltORderNumber', '', 'required');
			// $this->form_validation->set_rules('LoanNumber', '', 'required');
			// $this->form_validation->set_rules('PropertyRoleUID[]', '', 'required');
			// $this->form_validation->set_rules('PRName[]', '', 'required');
				$c = 1;
				foreach ($_POST['PropertyRoleUID'] as $key => $value){
					if(is_null($value) || $value == '')
					{
						$this->form_validation->set_rules('PropertyRoleUID'.$c, '', 'required');
						$data1[]= 'PropertyRoleUID'.$c;
					}
					$c++;
				}
			// $this->form_validation->set_rules('PRName[]', '', 'required');
				$n = 1;
				foreach (str_replace(' ','',$this->input->post('PRName')) as $key => $value){
					if(is_null($value) || $value == '')
					{
						$this->form_validation->set_rules('PRName'.$n, '', 'required');
						$data2[]= 'PRName'.$n;
					}
					$n++;
				}
				/*$this->form_validation->set_rules('customer', '', 'required');*/
				$this->form_validation->set_rules('ProductUID', '', 'required');
				$this->form_validation->set_rules('SubProductUID', '', 'required');
			//$this->form_validation->set_rules('OrderTypeUID', '', 'required');
			// $this->form_validation->set_rules('PriorityUID', '', 'required');
				$this->form_validation->set_rules('PropertyAddress1', '', 'required');
			// $this->form_validation->set_rules('PropertyAddress2', '', 'required');
				$this->form_validation->set_rules('PropertyCityName', '', 'required');
				$this->form_validation->set_rules('PropertyStateCode', '', 'required');
				$this->form_validation->set_rules('PropertyCountyName', '', 'required');
				$this->form_validation->set_rules('PropertyZipcode', '', 'required');
			// $this->form_validation->set_rules('MailingAddress1', '', 'required');
			// $this->form_validation->set_rules('TemplateUID', '', 'required');
			// $this->form_validation->set_rules('APN', '', 'required');
			// $this->form_validation->set_rules('EmailReportTo', '', 'required');
				$this->form_validation->set_message('required', 'This Field is required');
				$ProductUIDs = $this->input->post('ProductUID');
				$Products = $this->db->where_in('ProductUID', $ProductUIDs)->where('IsClosingProduct', 1)->get('mproducts')->result();
				
				/*author parthasarathy hidden this as per requirment on 8/5/20*/
				/*if(!empty($Products)){
					$SigningDate = $this->input->post('SigningDate');
					if(!empty($SigningDate)){
						$currentDate = date('m/d/Y');
						$SignDate = strtotime($SigningDate);
						$date = strtotime($currentDate);
						if($SignDate < $date){
							$result = array('message' =>'Signing date should be greater than or equal to current date');
							echo json_encode($result);exit;
						}
					}
				}*/

				//for x1 api orders 
				$CustomerUID = $this->input->post('customer');
				$SubProductUID = $this->input->post('SubProductUID');
				$X1result =$this->CheckX1Order($CustomerUID,$SubProductUID);
				if(!empty($X1result))
				{
					$this->form_validation->set_rules('BorrowerType', '', 'required');
					$this->form_validation->set_rules('PropertyType', '', 'required');
					$this->form_validation->set_rules('TransactionType', '', 'required');
				}

				foreach ($chk_mailing as $key => $mailingtype) {

					if ($mailingtype == 'others') {
						$this->form_validation->set_rules('MailingAddress1['.($key - 1).']', '', 'required');
						$this->form_validation->set_rules('MailingZipCode['.($key - 1).']', '', 'required');
						$this->form_validation->set_rules('MailingCityName['.($key - 1).']', '', 'required');
						$this->form_validation->set_rules('MailingStateCode['.($key - 1).']', '', 'required');
						$this->form_validation->set_rules('MailingCountyName['.($key - 1).']', '', 'required');
					}
				}

				foreach ($chk_Signing as $key => $signingtype) {

					if ($signingtype == 'others') {
						$this->form_validation->set_rules('SigningAddress1['.($key - 1).']', '', 'required');
						$this->form_validation->set_rules('SigningZipCode['.($key - 1).']', '', 'required');
						$this->form_validation->set_rules('SigningCityName['.($key - 1).']', '', 'required');
						$this->form_validation->set_rules('SigningStateCode['.($key - 1).']', '', 'required');
						$this->form_validation->set_rules('SigningCountyName['.($key - 1).']', '', 'required');
					}
				}

				if ($this->form_validation->run() == TRUE) {

				//$OrderNo = $this->Order_Summary_Model->OrderNumber();
					$LoanAmount = $this->input->post('LoanAmount');

					/* Validate StateCode based on zipcode and city,county selections */
					if(empty(validateState($OrderDetails['PropertyZipcode'],$OrderDetails['PropertyCountyName'],$OrderDetails['PropertyStateCode'])))
					{
						echo json_encode(['validation_error' => 1,'message'=>'State is invalid','PropertyStateCode'=>'0']); exit;
					}	


					$usps_address = new stdClass();

					$StateUID = $this->Order_Summary_Model->GetStateDetails($OrderDetails['PropertyStateUID']);
					$StateUID = $StateUID->StateCode;

					$City = $this->Order_Summary_Model->GetCityDetails($OrderDetails['PropertyCity']);
					$City = $City->CityName;

					$CountyUID = $this->Order_Summary_Model->GetCountyDetails($OrderDetails['PropertyCountyUID']);
					$CountyUID = $CountyUID->CountyName;

					$usps_address->PropertyAddress1 = $OrderDetails['PropertyAddress1'];
					$usps_address->PropertyAddress2 = $OrderDetails['PropertyAddress2'];
					$usps_address->PropertyZipcode = $OrderDetails['PropertyZipcode'];
					$usps_address->PropertyStateUID = $OrderDetails['PropertyStateCode'];
					$usps_address->PropertyCity = $OrderDetails['PropertyCityName'];
					$usps_address->PropertyCountyUID = $OrderDetails['PropertyCountyName'];

					$addresses = array(

						'0' => array(
							/*'firm_name' => '',*/
							'address1' => $usps_address->PropertyAddress1,
							'address2' => $usps_address->PropertyAddress2,
							'city' => $usps_address->PropertyCity,
							'state' => $usps_address->PropertyStateUID,
							'zip5' => $usps_address->PropertyZipcode,
							'zip4' => ''
						)
					);


					$status = $this->Order_Summary_Model->ChangeOrderStatusCheck($OrderUID);


				/*$order_id = $this->Order_Summary_Model->insert_order($OrderDetails,$LoanAmount,$status);
				$result = array("validation_error" => 0,"id" => $order_id,'message'=>'Successful ');
*/
				//RUN ADDRESS STANDARDIZATION REQUEST
				$verified_address = $this->usps->address_standardization($addresses);
				// OUTPUT RESULTS	
				$PropertyAddresses = $verified_address->Address->Error->Description;
				
				if($PropertyAddresses == null){

					$UspsAddress = $verified_address;

					$USPS = $usps_address;
					$order_id = $this->Order_Summary_Model->insert_order($OrderDetails,$LoanAmount,$UspsAddress,$USPS,$status);
					$Msg = $this->lang->line('Success');
					$result = array("validation_error" => 0,"id" => $order_id,'message'=>$Msg);

					
				}else{

					$usps_address_empty = $verified_address;
					$USPS_empty = [];
					$order_id = $this->Order_Summary_Model->insert_order($OrderDetails,$LoanAmount,$usps_address_empty,$USPS_empty,$status);

					$Msg = $this->lang->line('Success');
					$result = array("validation_error" => 0,"id" => $order_id,'message'=> $Msg);
					
				}


				/**
				* @purpose: D2TINT-105 - To update alternate number if it is api title order
				*           and alternate order number not empty
				* @author : D.Samuel Prabhu <samuel.prabhu@avanzegroup.com>
				* @since  : 21 July 2020
				*/
				if(!empty($OrderDetails['AltORderNumber']) && $result['validation_error']==0)
				{
					$this->updateAlternateNo($OrderDetails['OrderUID']);
				}

				echo json_encode($result);

			}else{
				$Msg = $this->lang->line('Empty_Validation');
				$datas1 =[];
				foreach ($data1 as $key => $value) {
					$datas1[$value] = form_error($value);
				}
				$datas2 = [];
				foreach ($data2 as $key => $value) 
				{
					$datas2[$value] = form_error($value);
				}
				$data = array(
					'validation_error' => 1,
					'message' => $Msg, 
					// 'PropertyRoleUID' => form_error('PropertyRoleUID[]'),
					// 'PRName' => form_error('PRName[]'),
					/*'customer' => form_error('customer'),*/
					'ProductUID' => form_error('ProductUID'),
					'SubProductUID' => form_error('SubProductUID'),
					//'OrderTypeUID' => form_error('OrderTypeUID'),
					// 'PriorityUID' => form_error('PriorityUID'),
					'PropertyAddress1' => form_error('PropertyAddress1'),
					// 'PropertyAddress2' => form_error('PropertyAddress2'),
					'PropertyCityName' => form_error('PropertyCityName'),
					'PropertyStateCode' => form_error('PropertyStateCode'),
					'PropertyCountyName' => form_error('PropertyCountyName'),
					'PropertyZipcode' => form_error('PropertyZipcode'),
					// 'MailingAddress1' => form_error('MailingAddress1'),
					// 'TemplateUID' => form_error('TemplateUID'),
					// 'APN' => form_error('APN'),
					// 'EmailReportTo' => form_error('EmailReportTo'),
				);

				if(!empty($X1result))
				{
					$X1DataValidation = array(
						'BorrowerType' => form_error('BorrowerType'),
						'PropertyType' => form_error('PropertyType'),
						'TransactionType' => form_error('TransactionType'),
					);
				}

				$datas = array_merge($datas1,$datas2);
				$Merged = array_merge($datas,$data,$X1DataValidation);


				foreach ($chk_mailing as $key => $mailingtype) {
					
					if ($mailingtype == 'others') {

						$Merged['MailingAddressValidation'][($key - 1)]['MailingAddress1'] = form_error('MailingAddress1['.($key - 1).']');
						$Merged['MailingAddressValidation'][($key - 1)]['MailingZipCode'] = form_error('MailingZipCode['.($key - 1).']');
						$Merged['MailingAddressValidation'][($key - 1)]['MailingCityName'] = form_error('MailingCityName['.($key - 1).']');
						$Merged['MailingAddressValidation'][($key - 1)]['MailingStateCode'] = form_error('MailingStateCode['.($key - 1).']');
						$Merged['MailingAddressValidation'][($key - 1)]['MailingCountyName'] = form_error('MailingCountyName['.($key - 1).']');
					}
				}


				foreach ($chk_Signing as $key => $signingtype) {
					
					if ($signingtype == 'others') {

						$Merged['SigningAddressValidation'][($key - 1)]['SigningAddress1'] = form_error('SigningAddress1['.($key - 1).']');
						$Merged['SigningAddressValidation'][($key - 1)]['SigningZipCode'] = form_error('SigningZipCode['.($key - 1).']');
						$Merged['SigningAddressValidation'][($key - 1)]['SigningCityName'] = form_error('SigningCityName['.($key - 1).']');
						$Merged['SigningAddressValidation'][($key - 1)]['SigningStateCode'] = form_error('SigningStateCode['.($key - 1).']');
						$Merged['SigningAddressValidation'][($key - 1)]['SigningCountyName'] = form_error('SigningCountyName['.($key - 1).']');
					}
				}

				foreach ($Merged['MailingAddressValidation'] as $key1 => $mailaddress) {
					
					foreach ($mailaddress as $key2 => $value) {
						if(is_null($value) || $value == '')
							unset($Merged['MailingAddressValidation'][$key1][$key2]);

					}

					if (empty($Merged['MailingAddressValidation'][$key1])) {
						unset($Merged['MailingAddressValidation'][$key1]);
					}
				}


				foreach ($Merged['SigningAddressValidation'] as $key1 => $mailaddress) {
					
					foreach ($mailaddress as $key2 => $value) {
						if(is_null($value) || $value == '')
							unset($Merged['SigningAddressValidation'][$key1][$key2]);

					}

					if (empty($Merged['SigningAddressValidation'][$key1])) {
						unset($Merged['SigningAddressValidation'][$key1]);
					}
				}


				foreach($Merged as $key=>$value)
				{
					if(is_null($value) || $value == '')
						unset($Merged[$key]);
				}
				echo json_encode($Merged);
			}
		}
	}



function CheckX1Order($CustomerUID,$SubProductUID) {
		$OrderSourceUID = $this->db->select('OrderSourceUID')->from('mApiTitlePlatform')->where('OrderSourceName','X1')->get()->row()->OrderSourceUID;
		if (empty($OrderSourceUID)) 
		{
			return 0;
		}
		
		$result = $this->db->select('*')->from('mcustomerproducts')->where(array('CustomerUID'=>$CustomerUID,'SubProductUID'=>$SubProductUID,'OrderSourceUID'=>$OrderSourceUID))->get()->row();
		if(!empty($result))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}







}
