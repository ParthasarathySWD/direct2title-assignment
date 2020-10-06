<?php
// defined('BASEPATH') OR exit('No direct script access allowed');
// ini_set('display_errors', 1);
error_reporting(E_ALL);
class Order_entry extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('USPS');
		// Check if username exists in session
		if (($this->session->userdata('UserUID') == NULL) && (($this->session->userdata('RoleUID') == NULL))){
			redirect(base_url().'users');
		}
		else{
			$this->load->model('Orderentry_model');
			$this->load->model('Common_model');
			$this->load->library(array('form_validation'));
			$this->load->helper('form');
			$this->lang->load('keywords');
			$this->load->helper('customer_pricing');
			/* No need to do static state managament here*/
			// $this->load->model('users/Mlogin');
			$this->loggedid = $this->session->userdata('UserUID');
			$this->RoleUID = $this->session->userdata('RoleUID');
			$this->UserName = $this->session->userdata('UserName');
			$this->RoleType = $this->session->userdata('RoleType');
		/*	$this->UserUID ='3287';
			$this->loggedid ='techorg';
			$this->RoleUID ='1';
			$this->UserName ='techorg';
			$this->RoleType ='Administrator';
*/		}

	}

	public function index()
	{
		$data['content'] = 'index';
		$data['OrderSummaryID'] = 0;
		$data['data'] = array('menu'=>'OrderEntry','title'=>'Order Entry','link'=>array('OrderEntry'));
		$data['States'] = $this->common_model->GetStateDetails();
		$data['Prioritys'] = $this->common_model->GetPriorityDetails();
		$data['Ordertypes'] = $this->common_model->GetOrderTypeDetails();
		// $data['TransactionTypeDetails'] = $this->common_model->GetTransactionTypeDetails();
		// $data['PropertyTypeDetails'] = $this->common_model->GetPropertyTypeDetails();
		// $data['BorrowerDetails'] = $this->common_model->GetBorrowerDetailsDescription();
		$data['documentstatus'] = $this->config->item('RMS_DocumentStatus');


		if(in_array($this->session->userdata('RoleType'),array(1,2,3,4,5)))
		{
			$data['Customers'] = $this->common_model->GetCustomerDetails();
			$data['bulk_Customers'] = $this->common_model->GetCustomerDetails();

		} else if(in_array($this->session->userdata('RoleType'),array(8))) {
			$data['Customers'] = $this->Orderentry_model->Get_Assign_Customer($this->loggedid);
			$data['bulk_Customers'] = $this->Orderentry_model->Get_Assign_Customer($this->loggedid);

		}else if(in_array($this->session->userdata('RoleType'),array(6))) {
			$data['Customers'] = $this->common_model->_get_customersdata_bylogin($this->loggedid);
			$data['bulk_Customers'] = $data['Customers'];

		} else {
			$data['Customers'] = $this->Orderentry_model->get_customer_ingroup($this->loggedid);
			$data['bulk_Customers'] = $this->Orderentry_model->get_customer_ingroup($this->loggedid);
		}


		if(isset($_GET['OrderUID']))
		{
			$OrderID = $_GET['OrderUID'];
			$data['OrderSummaryID'] = $OrderID;
			$data['OrderEntryCopy'] = $this->Orderentry_model->GetOrderEntryCopyDetailsbyUID($OrderID);
			$ProductUID = $data['OrderEntryCopy']->ProductUID;
			$CustomerUID = $data['OrderEntryCopy']->CustomerUID;

			$payload['CustomerUID'] = $this->input->get('CustomerUID');
			$payload['ProductUID'] = $this->input->get('ProductUID');
			$payload['SubProductUID'] = $this->input->get('SubProductUID');
			if (!empty($payload['CustomerUID'])) {
				$CustomerUID = $payload['CustomerUID'];
				$data['OrderEntryCopy']->CustomerUID = $CustomerUID;
			}
			if (!empty($payload['ProductUID'])) {
				$ProductUID = $payload['ProductUID'];
				$data['OrderEntryCopy']->ProductUID = $ProductUID;
			}
			if (!empty($payload['SubProductUID'])) {
				$SubProductUID = $payload['SubProductUID'];
				$data['OrderEntryCopy']->SubProductUID = $SubProductUID;
			}

			// if (!empty($ClosingProducts = $this->Orderentry_model->getClosingProductUIDs())) {
			// 	if (in_array($ProductUID, $ClosingProducts)) {
			// 		$torders = $this->common_model->get_row('torders',['OrderUID'=>$OrderUID]);
			// 		if ($torders->IsInhouseExternal) {
			// 			$SubProductUID = $this->config->item('SubProducts')['Onlilne L&V & Closing'];
			// 		}
			// 		else{
			// 			$SubProductUID = $this->config->item('SubProducts')['Field L&V & Closing'];
			// 		}
			// 		$data['OrderEntryCopy']->SubProductUID = $SubProductUID;						
			// 	}
				
			// }
			$data['CheckX1Order'] = $this->Orderentry_model->GetPreferedSites($OrderID) ;
			$data['order_details'] = $this->common_model->get_orderdetails($OrderID);
			
			$data['Product']= $this->Orderentry_model->GetCustomerProductDetails($CustomerUID);
			$data['Sub_products'] = $this->Orderentry_model->GetCustomerSubProductDetails($CustomerUID,$ProductUID);
			$data['encrypt'] = new AesCtr();
			
		}	
		$data['Prop_roles'] = $this->common_model->GetPropertyrolesDetails();
		// $data['bundleddetails'] = $this->Orderentry_model->get_bundleddetails();
		$data['is_vendor_login'] = $this->common_model->is_vendorlogin();
		$data['Name'] = $this->UserName;
		// $data['Menu'] = $this->Mlogin->DynamicMenu($this->RoleUID,$this->UserName);
		$data['IsUpper'] = $this->common_model->GetOrganizations();

		// $data['UserUID'] = $this->loggedid;
		// $data['RoleUID'] = $this->RoleUID;
		// $data['RoleType'] = $this->RoleType;
		$this->load->view('page', $data);
	}

 	/**
        *@description Function to Getproductsubproduct
        *
        * @param $query
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @since 12/09/2020 
        * @version D2T Assignment 
        *
    */ 
	function getproductsubproduct()
	{
		$CustomerUID = $this->input->post('CustomerUID');

		if($CustomerUID !=''){
			$value =  $this->Orderentry_model->get_customer_product_details($CustomerUID);
			$priority = $this->Orderentry_model->GetPriorityDetails();
			//$IsFlood = $this->Orderentry_model->CheckIsFlood($CustomerUID);
			$returnvalue = array('data'=>$value,'priority'=>$priority,'success'=>'1');
		}else{
			$returnvalue = array('data'=>array('Products'=>'','SubProducts'=> '','CustomerDetails'=>''),'success'=>'0');
		}
		echo json_encode($returnvalue);
	}
	/**
        * @author Sathis Kannan P
        * @purpose get SubProduct, Project, Priority
        * @param ProductUID,CustomerUID
        * @return JSON
        * @date 02-JULY-2020
        * @version Client Portal New Theme
    */

function get_subproduct()
	{
		error_reporting(0);
		$ProductUID = $this->input->get('ProductUID');
		$CustomerUID = $this->input->get('CustomerUID');
		
		// $IsFlood = $this->Orderentry_model->CheckIsFlood($CustomerUID,$ProductUID);

		// $DynamicProduct = $this->common_model->CheckIsDynamicProduct($ProductUID);
		// $IsDynamicProduct = $DynamicProduct->IsDynamicProduct;

		//@author Naveenkumar @purpose get priority deatails
		$priority = $this->Orderentry_model->GetPriorityDetails();

		if($ProductUID)
		{
			$SubProduct = $this->Orderentry_model->GetSub_productDetails($ProductUID,$CustomerUID);
			$Project = $this->common_model->GetProjectDetails($ProductUID,$CustomerUID);

			$Return = array('SubProduct'=>$SubProduct,'IsDynamicProduct'=>$IsDynamicProduct,'Project'=>$Project,'priority'=>$priority);
			echo json_encode($Return);

		} else {
			echo json_encode('');
		}
	}


	/**
        * @author Sathis Kannan P
        * @purpose GetZipcode Details
        * @param ZipcodeUID
        * @return JSON
        * @date 12-JULY-2020
        * @version D2T Assignment
    */

		function GetZipCodeDetails()
	{ 
		$Zipcode = $this->input->get('Zipcode');
		//if match zipcode inside special char echo empty
		if (preg_match('/[^\d]+/', $Zipcode) || (strlen($Zipcode) != 5 && strlen($Zipcode) != 9 ))
		{
			echo json_encode(array('City'=>'','success'=>0,'State'=>'','County'=>'')); exit();
		}
		
		if(!empty($Zipcode))
		{
			// 5 digits less then zipcode add 0 prefix for missing count
			$Zipcode = strtok($Zipcode,'-');
			/*if(strlen($Zipcode)<5)
		    {
		      $missing = 5 - strlen($Zipcode);	
		      $Zipcode = str_pad($Zipcode, $missing + strlen($Zipcode), '0', STR_PAD_LEFT);
		    } else {
		      $Zipcode = substr($Zipcode, 0,5);	
		  }*/

		  if (strlen($Zipcode) > 5) {
		  	$Zipcode = substr($Zipcode, 0,5);	
		  }

		  $City = $this->Orderentry_model->getCityDetail($Zipcode);
		  $State = $this->Orderentry_model->getStateDetail($Zipcode);
		  $County = $this->Orderentry_model->getCountyDetail($Zipcode);
		  
		  if(count($State) > 0 && count($County) > 0 && count($City) > 0){

		  	echo json_encode(array('City'=>$City,'success'=>1,'State'=>$State,'County'=>$County));
		  }else{
		  	echo json_encode(array('details'=>'','success'=>0));

		  }

		}else{
			echo json_encode(array('details'=>'','success'=>0));
		}
	}	
function insert()
	{
		error_reporting(0);
		/**
            * @description Add XSS-Clean Input Security
            * @author Mohindarkumar <mohindar.kumar@avanzegroup.com>
            * @since 23-07-2020 
            * @version CustomerPortal
        */
        $OrderDetails = $this->input->post();
        $OrderDetails = $this->security->xss_clean($OrderDetails);
        /** end */

		/* Libraries */
		// $this->load->model('Order_attachments/Attachments_model');
		$data['content'] = 'index';
		$data['data'] = array('menu'=>'OrderEntry','title'=>'Order Entry','link'=>array('OrderEntry'));
		if ($this->input->server('REQUEST_METHOD') === 'POST')
		{
			

			$this->form_validation->set_error_delimiters('', '');

			// if($_POST['IsFlood'] == 1){
			// 	$this->form_validation->set_rules('LoanNumber','', 'required');
			// }
			// $c = 1;
			// foreach ($_POST['PropertyRoleUID'] as $key => $value){
			// 	if(is_null($value) || $value == '')
			// 	{
			// 		$this->form_validation->set_rules('PropertyRoleUID'.$c, '', 'required');
			// 		$data1[]= 'PropertyRoleUID'.$c;
			// 	}
			// 	$c++;
			// }
			// $n = 1;

			// foreach (str_replace(' ','',$this->input->post('PRName')) as $key => $value){
			// 	if(is_null($value) || $value == '')
			// 	{
			// 		$this->form_validation->set_rules('PRName'.$n, '', 'required');
			// 		$data2[]= 'PRName'.$n;
			// 	}
			// 	$n++;
			// }

			
			// $this->form_validation->set_rules('customer', '', 'required',array(
                //         'required' => '%s field is required.',
                // ));
			// $this->form_validation->set_rules('ProductUID', '', 'required');
			// $this->form_validation->set_rules('SubProductUID', '', 'required');
			$this->form_validation->set_rules('PropertyAddress1', '', 'required');
			$this->form_validation->set_rules('PropertyCityName', '', 'required');
			$this->form_validation->set_rules('PropertyStateCode', '', 'required');
			$this->form_validation->set_rules('PropertyCountyName', '', 'required');
			// $this->form_validation->set_rules('PropertyZipcode', '', 'required');
			// $this->form_validation->set_rules('SigningDate', '', 'callback_vailidSigningDate');
			// $this->form_validation->set_rules('SigningTime', '', 'callback_vailidSigningTime');
			$this->form_validation->set_message('required', 'This Field is required');

			$ProductUIDs = $this->input->post('ProductUID');
			$Products = $this->db->where_in('ProductUID', $ProductUIDs)->where('IsClosingProduct', 1)->get('mproducts')->result();

			/* Signing Date Validation */

			if(!empty($Products)){
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
			}

			//for x1 api orders 
			$CustomerUID = $this->input->post('customer');
			$SubProductUID = $this->input->post('SubProductUID');
			// $X1result =$this->Orderentry_model->CheckX1Order($CustomerUID,$ProductUIDs[0],$SubProductUID[0]);
			// if($X1result)
			// {
			// 	$this->form_validation->set_rules('BorrowerType', '', 'required');
			// 	$this->form_validation->set_rules('PropertyType', '', 'required');
			// 	$this->form_validation->set_rules('TransactionType', '', 'required');
			// }

			$chk_mailing = $this->input->post('chk_mailing');

			// if (!empty($Products)) {
			foreach ($chk_mailing as $key => $mailingtype) {

				if ($mailingtype == 'No') {
					$this->form_validation->set_rules('MailingAddress1['.$key.']', '', 'required');
					$this->form_validation->set_rules('MailingZipCode['.$key.']', '', 'required');
					$this->form_validation->set_rules('MailingCityName['.$key.']', '', 'required');
					$this->form_validation->set_rules('MailingStateCode['.$key.']', '', 'required');
					$this->form_validation->set_rules('MailingCountyName['.$key.']', '', 'required');
				}
			}
			// }

			$chk_Signing = $this->input->post('chk_Signing');

			if (!empty($Products)) {
				foreach ($chk_Signing as $key => $signingtype) {

					if ($signingtype == 'Other') {
						$this->form_validation->set_rules('SigningAddress1['.$key.']', '', 'required');
						$this->form_validation->set_rules('SigningZipCode['.$key.']', '', 'required');
						$this->form_validation->set_rules('SigningCityName['.$key.']', '', 'required');
						$this->form_validation->set_rules('SigningStateCode['.$key.']', '', 'required');
						$this->form_validation->set_rules('SigningCountyName['.$key.']', '', 'required');
					}
				}
			}
			if ($this->form_validation->run() == TRUE) {


				$CustomersEmailReportTo = $this->Orderentry_model->Get_Assign_Customer($this->loggedid);

				
				$OrderDetails['OldEmailReportTo'] = $CustomersEmailReportTo['0']->CustomerOrderAckEmailID;

				/* Validate StateCode based on zipcode and city,county selections */
				if(empty(validateState($OrderDetails['PropertyZipcode'],$OrderDetails['PropertyCountyName'],$OrderDetails['PropertyStateCode'])))
				{
					echo json_encode(['validation_error' => 1,'message'=>'State is invalid','PropertyStateCode'=>'0']); exit;
				}

				// str_replace to remove special char like '<>/\"
				$OrderDetails = str_replace(array('\'', '"', '<', '>', '\\'), '', $OrderDetails); 

				$CustomerUID = $this->CustomerUID;
				
				
				$PropertyRoles = [];
				foreach ($OrderDetails['PropertyRoleUID'] as $key => $prop) {
					$obj = new stdClass();
					$obj->PRName = $OrderDetails['PRName'][$key];
					$obj->PropertyRoleUID = $OrderDetails['PropertyRoleUID'][$key];
					$PropertyRoles[] = $obj;
				}
				
				$is_duplicate = $this->Orderentry_model->find_is_duplicate($OrderDetails['PropertyAddress1'],$OrderDetails['PropertyAddress2'],$OrderDetails['PropertyZipcode'],$OrderDetails['PropertyCityName'],$OrderDetails['PropertyCountyName'],$OrderDetails['PropertyStateCode'],$OrderDetails['LoanNumber'], $OrderDetails['SubProductUID'], $OrderDetails['ProductUID'], $CustomerUID, $PropertyRoles);

				if(count($is_duplicate) > 0 && $OrderDetails['Skip_duplicate'] == 0){

					$TATOrdersUIDs = "1";
					$selected_customer = $this->Orderentry_model->Get_customer_details($this->parameters['CustomerUID']);

					$html = '<div class="col-md-12  md-editor" style="border-radius: 10px;margin-bottom: 10px;border: 1px dashed #ccc;">


					<div class="row">
					<label for="Address1" class="col-sm-4 control-label" style="font-weight: bold"> Address 1 : <span style="font-weight: 100"> '.$OrderDetails['PropertyAddress1'].' </span></label>

					<label for="Address2" class="col-sm-4 control-label" style="font-weight: bold"> Address 2 : <span style="font-weight: 100"> '.$OrderDetails['PropertyAddress2'].'</span></label>

					<label for="PropertyZipcode" class="col-sm-4 control-label" style="font-weight: bold"> ZipCode : <span style="font-weight: 100"> '.$OrderDetails['PropertyZipcode'].' </span> </label>

					<label for="PropertyCityName" class="col-sm-4 control-label" style="font-weight: bold"> City : <span style="font-weight: 100"> '.$OrderDetails['PropertyCityName'].'</span> </label>

					<label for="PropertyCountyName" class="col-sm-4 control-label" style="font-weight: bold"> County : <span style="font-weight: 100"> '.$OrderDetails['PropertyCountyName'].' </span> </label>

					<label for="PropertyStateCode" class="col-sm-4 control-label" style="font-weight: bold"> State : <span style="font-weight: 100"> '.$OrderDetails['PropertyStateCode'].'</span> </label>
					</div>
					</div>
					<div class="col-md-12 text-left">

					<div class="fancy-checkbox">
					<label><input id="Customeronly" type="checkbox" name="customer_filter" data-customeruid="'.$selected_customer->CustomerUID.'"><span>'.$selected_customer->CustomerName.'</span></label>
					</div>


					</div>';
					$html .= '<div class="col-sm-12" style="max-height:300px;overflow:auto;">
					<table class="table table-hover table-custom spacing5 bg-table duplicate_table">
					<thead>
					<tr>
					<th class="text-left">OrderNumber</th>
					<th class="text-left">Client</th>
					<th class="text-left">Product/SubProduct</th>
					<th class="text-left">Borrower Name</th>
					<th class="text-left">Loan Number </th>
					<th class="text-left">Open date</th>
					<th class="text-left">Close date</th>
					<th class="text-left">Order Status</th>
					<th class="text-right">Action</th>
					</tr>
					</thead>
					<tbody>';



					foreach ($is_duplicate as $key => $value) {

						$onholdworkflow = $this->Common_model->get_onholdWorkflow($value->OrderUID);


						if($value->PriorityUID == '3')
						{

							$OrderNumber = '<td class="text-left"><a href="'.base_url('Login/order_session/'.$value->OrderUID).'" class="'.(in_array($value->OrderUID, $TATOrdersUIDs) ? "text-danger" : "text-primary").'" >'.$value->OrderNumber.'</a> <img src="'.base_url().'assets/img/asap.png" title="'.$value->PriorityName.'" height="20px" width="20px"></td>';

						}else if($value->PriorityUID == '1'){

							$OrderNumber = '<td class="text-left"><a href="'.base_url('Login/order_session/'.$value->OrderUID).'" class="'.(in_array($value->OrderUID, $TATOrdersUIDs) ? "text-danger" : "text-primary").'">'.$value->OrderNumber.'</a> <img src="'.base_url().'assets/img/rush.png" title="'.$value->PriorityName.'" height="20px" width="20px"></td>';

						}else{

							$OrderNumber = '<td class="text-left"><a href="'.base_url('Login/order_session/'.$value->OrderUID).'" class="'.(in_array($value->OrderUID, $TATOrdersUIDs) ? "text-danger" : "text-primary").'">'.$value->OrderNumber.'</a></td>';

						}
						if($onholdworkflow->WorkflowModuleName != '') {

							$Status = '<span class="btn btn-rounded btn-sm" style="font-size: 10px; color:#fff; background: #ff8600;">'.$onholdworkflow->WorkflowModuleName.'-OnHold</span>';

						}else{

							$Status = ' <span class="btn btn-rounded btn-sm "  style="font-size: 8pt; color: #fff; background: '.$value->StatusColor.'">'.$value->StatusName.'</span> ';

						}
						$html .= '<tr data-customeruid = "'.$value->CustomerUID.'">
						'.$OrderNumber.'
						<td class="text-left">'.$value->CustomerNumber.' / '.$value->CustomerName.'</td>
						<td class="text-left">'.substr($value->ProductName, 0, 1).'-'.$value->SubProductName.'</td>
						<td class="text-left">'.$this->Orderentry_model->Getborrowername($value->OrderUID).'</td>
						<td class="text-left">'.$value->LoanNumber.'</td>
						<td class="text-left">'.$value->OrderEntryDateTime.'</td>
						<td class="text-left">'.$value->OrderCompleteDateTime.'</td>
						<td class="text-left">'.$Status.'</td>
						<td class="text-right"><a href="'.base_url().'Login/order_session/'.$value->OrderUID.'" target="_blank" type ="button" class="btn btn-sm btn-default text-primary"><i class="icon-note" aria-hidden="true"></i></a></td>
						</tr>';

					}
					$html .='</tbody></table></div>';
					$data = array(
						'validation_error' => 2,
						'html'=>$html,
					);
					echo json_encode($data);exit;
				}



				$usps_address = new stdClass();

				$StateUID = $this->Orderentry_model->GetStateDetails($OrderDetails['PropertyStateUID']);
				$StateUID = $StateUID->StateCode;

				$City = $this->Orderentry_model->GetCityDetails($OrderDetails['PropertyCity']);
				$City = $City->CityName;

				$CountyUID = $this->Orderentry_model->GetCountyDetails($OrderDetails['PropertyCountyUID']);
				$CountyUID = $CountyUID->CountyName;

				$usps_address->PropertyAddress1 = $OrderDetails['PropertyAddress1'];
				$usps_address->PropertyAddress2 = $OrderDetails['PropertyAddress2'];
				$usps_address->PropertyZipcode = $OrderDetails['PropertyZipcode'];
				$usps_address->PropertyStateUID = $OrderDetails['PropertyStateCode'];
				$usps_address->PropertyCity = $OrderDetails['PropertyCityName'];
				$usps_address->PropertyCountyUID = $OrderDetails['PropertyCountyName'];

				$addresses = array(

					'0' => array(
						'address1' => $usps_address->PropertyAddress1,
						'address2' => $usps_address->PropertyAddress2,
						'city' => $OrderDetails['PropertyCityName'],
						'state' => $OrderDetails['PropertyStateCode'],
						'zip5' => $usps_address->PropertyZipcode,
						'zip4' => ''
					)
				);


				$CopyOrderDetails = $this->Orderentry_model->GetOrderDetailsForCopy($OrderDetails);

				foreach($CopyOrderDetails as $key=>$value)
				{
					$PropertyAddress1 = $value->PropertyAddress1;
					$PropertyAddress2 = $value->PropertyAddress2;

					$Address1 = $OrderDetails['PropertyAddress1'];
					$Address2 = $OrderDetails['PropertyAddress2'];
					$ZipCode = $OrderDetails['PropertyZipcode'];
					$City = $OrderDetails['PropertyCityName'];
					$County = $OrderDetails['PropertyCountyName'];
					$State = $OrderDetails['PropertyStateCode'];

					$Address_entry = strtoupper(trim($Address1.' '.$Address2));
					$Address = strtoupper(trim($PropertyAddress1.' '.$PropertyAddress2));

					similar_text($Address,$Address_entry,$percent);

					$DatabaseAddress = '';

					if($percent > 60){
						$DatabaseAddress = $Address;
					}
				}




				/**
			    * Redirect to the order in the success of the order - Single Order Entry
			    * @author : Yagavi G <yagavi.g@avanzegroup.com>
			    * @since  : 24 Feb 2020
			    */ 

				/* Start - Redirect to the order in the success of the order - Single Order Entry*/
				$Rep_msg = $this->Orderentry_model->insert_order($OrderDetails, $DatabaseAddress);
				$SingleOrderUID = $Rep_msg['OrderUID'];

				$id = '';
				$UploadFailureLog = [];

				if(!empty($SingleOrderUID)){
					// $id = $SingleOrderUID;

					 /**
				        * @author Mohindarkumar V
				        * @purpose Multiple Order Entry File Upload
				        * @throws nothing
				        * @date 09-JULY-2020
				        * @version Client Portal New Theme
				     */
					foreach ($SingleOrderUID as $key => $value) {

						if (isset($_FILES['file'])) 
						{
							$Filecount = count($_FILES['file']['name']);
							$Files = $_FILES['file'];
							$Payload_DisplayFileName = $this->input->post('filename');
							$Payload_TypeOfDocument = $this->input->post('TypeOfDocument');
							$Payload_Comments = $this->input->post('Comments');
							$Payload_postition = $this->input->post('postition');
							for ($f=0; $f < $Filecount; $f++) 
							{ 
								$DocumentFileName	= $this->Attachments_model->GetAvailFileName_Enhanced(basename($Files['name'][$f]), $value);

								$DocsPath = $this->Common_model->createOrderSearchDocsFolder($value);
								if ( !empty($DocsPath) ) 
								{
									$is_uploaded = move_uploaded_file($Files['tmp_name'][$f], $DocsPath . $DocumentFileName);
									if ( $Files['error'][$f] == UPLOAD_ERR_OK && $is_uploaded ) 
									{
										$torderdocs['OrderUID'] 			= $value;
										$torderdocs['DocumentFileName']		= $DocumentFileName;
										$torderdocs['DisplayFileName'] 		= $Payload_DisplayFileName[$f];
										$torderdocs['TypeOfDocument'] 		= $Payload_TypeOfDocument[$f];
										$torderdocs['Comments'] 			= $Payload_Comments[$f];
										$torderdocs['TypeOfPermissions']	= 1;
										$torderdocs['extension']			= end(explode(".", $Files['name'][$f]));
										$torderdocs['Position']				= $Payload_postition[$f];
										$torderdocs['DocumentTypeUID']		= 0;
										$torderdocs['SearchAsOfDate']		= "0000-00-00 00:00:00";
										$torderdocs['SearchFromDate']		= "0000-00-00 00:00:00";
										$torderdocs['DocumentCreatedDate']	= date('Y-m-d H:i:s');

										$retdata = $this->Attachments_model->StoreDocuments($torderdocs);

									}
									else
									{
										$UploadFailureLog[] = $Files['name'][$f] . " not able to upload because " . $Files['error'][$f];
									}
								}
							}

						}

					}

					/** END */				


				}

				/* End - Redirect to the order in the success of the order - Single Order Entry*/
				$result = array("validation_error" => 0,"id" => $SingleOrderUID,'message'=>$Rep_msg['Rep_msg'], 'OrderNumber'=>$Rep_msg['OrderNumber']);
			// print_r($Rep_msg['OrderNumber']);exit();
				echo json_encode($result);

			}

			else{

				$Msg=$this->lang->line('Empty_Validation');
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
					'message' =>$Msg,
					'test' 	=> $CopyOrderDetails,
					'customer' => form_error('customer'),
					'PropertyAddress1' => form_error('PropertyAddress1'),
					'PropertyCityName' => form_error('PropertyCityName'),
					'PropertyStateCode' => form_error('PropertyStateCode'),
					'PropertyCountyName' => form_error('PropertyCountyName'),
					'PropertyZipcode' => form_error('PropertyZipcode'),
					'LoanNumber' => form_error('LoanNumber'),
					'SigningDate' => form_error('SigningDate'),
					'SigningTime' => form_error('SigningTime'),


				);
				$X1DataValidation = [];

				if($X1result)
				{
					$X1DataValidation = array(
						'BorrowerType' => form_error('BorrowerType'),
						'PropertyType' => form_error('PropertyType'),
						'TransactionType' => form_error('TransactionType'),
					);
				}

				$datas = array_merge($datas1,$datas2);
				$Merged = array_merge($datas,$data,$X1DataValidation);
				// if (!empty($Products)) {

				foreach ($chk_mailing as $key => $mailingtype) {

					if ($mailingtype == 'No') {

						$Merged['MailingAddressValidation'][$key]['MailingAddress1'] = form_error('MailingAddress1['.$key.']');
						$Merged['MailingAddressValidation'][$key]['MailingZipCode'] = form_error('MailingZipCode['.$key.']');
						$Merged['MailingAddressValidation'][$key]['MailingCityName'] = form_error('MailingCityName['.$key.']');
						$Merged['MailingAddressValidation'][$key]['MailingStateCode'] = form_error('MailingStateCode['.$key.']');
						$Merged['MailingAddressValidation'][$key]['MailingCountyName'] = form_error('MailingCountyName['.$key.']');
					}
				}
				// }

				if (!empty($Products)) {

					foreach ($chk_Signing as $key => $signingtype) {

						if ($signingtype == 'Other') {

							$Merged['SigningAddressValidation'][$key]['SigningAddress1'] = form_error('SigningAddress1['.$key.']');
							$Merged['SigningAddressValidation'][$key]['SigningZipCode'] = form_error('SigningZipCode['.$key.']');
							$Merged['SigningAddressValidation'][$key]['SigningCityName'] = form_error('SigningCityName['.$key.']');
							$Merged['SigningAddressValidation'][$key]['SigningStateCode'] = form_error('SigningStateCode['.$key.']');
							$Merged['SigningAddressValidation'][$key]['SigningCountyName'] = form_error('SigningCountyName['.$key.']');
						}
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


	public function bulk_entry()
	{
		$data['content'] = 'bulkentry';

		if(in_array($this->session->userdata('RoleType'),array(1,2,3,4,5)))
		{
			$data['Customers'] = $this->common_model->GetCustomerDetails();
			$data['bulk_Customers'] = $this->common_model->GetCustomerDetails();

		} else if(in_array($this->session->userdata('RoleType'),array(8))) {
			$data['Customers'] = $this->Orderentry_model->Get_Assign_Customer($this->loggedid);
			$data['bulk_Customers'] = $this->Orderentry_model->Get_Assign_Customer($this->loggedid);

		}else if(in_array($this->session->userdata('RoleType'),array(6))) {
			$data['Customers'] = $this->common_model->_get_customersdata_bylogin($this->loggedid);
			$data['bulk_Customers'] = $data['Customers'];

		} else {
			$data['Customers'] = $this->Orderentry_model->get_customer_ingroup($this->loggedid);
			$data['bulk_Customers'] = $this->Orderentry_model->get_customer_ingroup($this->loggedid);
		}


		$this->load->view('page', $data);
	}
	 /**
		 * @author Sathis Kannan P
	     * @purpose Bulk Entry CustomerUID to fetch productName
	     * @throws nothing
	     * @date 16-09-2020
		 * @version New Assignment
	 **/
	function subproduct_by_customer_product(){

		$CustomerUID = $this->input->post('CustomerUID');



		if($CustomerUID != ''){

			$psub = $this->Orderentry_model->subproduct_by_customer_product($CustomerUID);
		
			echo json_encode(array("message"=>'',"status"=>"","Products"=>$psub[0],'SubProducts'=>$psub[1],'Projects'=>$psub[2],'Error'=>'0'));exit;
				exit();
		}else{
			echo json_encode(array("message"=>'select Group',"status"=>"danger","data"=>"",'Error'=>'1',"Products"=>'','SubProducts'=>'','Projects'=>''));exit;
		}

}
/**
		 * @author Sathis Kannan P
	     * @purpose Bulk Entry CustomerUID,ProductUID to fetch SubproductName
	     * @throws nothing
	     * @date 16-09-2020
		 * @version New Assignment
	 **/
function get_subproduct_by_product_customer()
{
	$CustomerUID = $this->input->post('CustomerUID');
	$ProductUID = $this->input->post('ProductUID');

	if($ProductUID != '' && $CustomerUID != ''){
		$SubProducts = $this->Orderentry_model->GetSubproduct_By_Productandcustomer($CustomerUID,$ProductUID);
		echo json_encode(array('SubProducts'=>$SubProducts[0],'Projects'=>$SubProducts[1]));
	}else{
		echo json_encode(array('SubProducts'=>$SubProducts[0],'Projects'=>$SubProducts[1]));
	}
}

public function bulkentrypreviewfile($filename)
{
	$file = FCPATH.'assets/previewfile/'.$filename;
	if(file_exists($file)){
		if (ob_get_contents()) ob_end_clean();
		header("Content-Description: File Transfer");
		header("Content-Type: application/octet-stream");
		header('Content-Disposition: attachment; filename="'.basename($file).'"');
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Type: application/force-download");
		header("Content-Type: application/download");
		header("Content-Length: ".filesize($file));
		readfile($file);
		exit;
	}

}
function preview_bulkentry()
	{
		if(isset($_FILES['file'])){
			$lib = $this->load->library('Excel');

			$inputFile = $_FILES['file']['tmp_name'];

			$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
			$temp = explode(".", $_FILES["file"]["name"]);

			$allowedExts = array("xlsx","xls");

			$extension = end($temp);

			if( in_array($extension, $allowedExts)) {

				try {

					$inputFileType = PHPExcel_IOFactory::identify($inputFile);
					$objReader = PHPExcel_IOFactory::createReader($inputFileType);
					$worksheets = $objReader->listWorkSheetNames($inputFile);
					$objReader->setLoadSheetsOnly($worksheets[0]);
					$objReader->setReadDataOnly(true);
					$objPHPExcel = $objReader->load($inputFile);

				}
				catch(Exception $e)
				{

					$msg = 'Error Uploading file';
					echo json_encode(array('error'=>'1','message'=>$msg));
					exit;
				}

				$CustomerUID = $this->input->post('CustomerUID');
				$ProductUID = $this->input->post('ProductUID');
				$SubProductUID = $this->input->post('SubProductUID');
				$bundledsubproductenabled = $this->input->post('bundledsubproductenabled');

				$X1result =$this->Orderentry_model->CheckX1Order($CustomerUID,$ProductUID,$SubProductUID);
				if($X1result)
				{
					$BorrowerType = $this->input->post('BorrowerType');
					$PropertyType = $this->input->post('PropertyType');
					$TransactionType = $this->input->post('TransactionType');
				}

				$IsFlood = $this->Orderentry_model->CheckIsFlood($CustomerUID,$ProductUID);


				if($CustomerUID =='' || $ProductUID =='' ){
					echo json_encode(array('error'=>'1','message'=>'Select the Required Fields'));exit;
				}
				if($X1result)
				{
					if($TransactionType =='' || $PropertyType ==''|| $BorrowerType ==''){
						echo json_encode(array('error'=>'1','message'=>'Select the Required Fields'));exit;
					}
				}

				//checking for bundled subproduct

				$objWorksheet = $objPHPExcel->getActiveSheet();
        		//excel with first row header, use header as key
				$highestRow = $objWorksheet->getHighestDataRow();
				$highestColumn = $objWorksheet->getHighestDataColumn();


				$headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);
				$headingsArray = $headingsArray[1];


				//declaring column variables
				$column = array('SubProduct'=>4,'LoanNumber'=>6);

				$arrayCode = $highlightarraykey = array();
				$r = -1;
				$headingArray = array();
				for ($row = 2; $row <= $highestRow; ++$row) {
					$dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
					if($this->isEmptyRow(reset($dataRow))) { continue; } // skip empty row
					++$r;

					$i = 4;
					$arrayCode[$r][0] = '';
					$arrayCode[$r][1] = '';
					$arrayCode[$r][2] = '';
					$arrayCode[$r][3] = '';

					//Remove bundle subproduct highlight
					foreach($headingsArray as $columnKey => $columnHeading) {
						$arrayCode[$r][$i] = $dataRow[$row][$columnKey];

						/*if($i == $column['LoanNumber'] && $bundledsubproductenabled == 1) {
							if(!empty($dataRow[$row][$columnKey])) {
								//check bundled subproduct enabled and loan number matches with the previous order
								$bundledsubproduct = $this->Orderentry_model->bulk_bundle_subproductmatch(['LoanNumber'=>$arrayCode[$r][$column['LoanNumber']],'ProductUID'=>$ProductUID,'CustomerUID'=>$CustomerUID]);
								if(!empty($bundledsubproduct['SubProductName'])) {	
									$arrayCode[$r][$column['SubProduct']] = (!empty($bundledsubproduct['SubProductName'])) ? $bundledsubproduct['SubProductName'] : $arrayCode[$r][$column['SubProduct']];
									$highlightarraykey[$r] = (!empty($bundledsubproduct['Order'])) ? $bundledsubproduct['Order']->OrderNumber.$this->lang->line('BundleSubproductOrder') : $this->lang->line('BundleSubproductNoOrder'); 
								}
							}
						}*/

						$i++;
					}

				}

				array_unshift($headingsArray, "Order Type", "Priority","Customer/Client","Product");

				$returnvalue = False;


				//$files_upload = $_FILES['MIME_FILES'];
				$files_upload= $this->input->post('MIME_FILES');

				foreach ($arrayCode as $i => $v) {
					$is_available = false;
					foreach ($files_upload as $key => $filename) {

						$LoanNumber = $arrayCode[$i][5];
						$arraycount = count($v);

						$dotposition = strripos($filename, '.');
						$documentname = substr($filename, 0, $dotposition);

						if (strpos(strtolower($documentname), strtolower($LoanNumber))!==false) {
							$is_available = true;
							$obj = new stdClass();
							$obj->LoanNumber = $LoanNumber;
							$obj->DocumentName = $filename;
							$FileUploadPreview[] = $obj;
						}
					}
				}






				//$arrayCode = array_map('array_filter', $arrayCode);

				?>

				<div class="tab-container">
					<!-- <ul class="nav nav-tabs nav-tabs-warning">
						<li class="active"><a href="#home2" data-toggle="tab">Data Preview</a></li>
						<li><a href="#profile2" data-toggle="tab">File Preview</a></li>
					</ul> -->

					 <ul class="nav nav-tabs b-none">
                                    <li class="nav-item active"><a class="nav-link active" data-toggle="tab" href="#home2"> Data Preview</a></li>
                                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#profile2">File Preview</a></li>
                                </ul>

					<div class="tab-content">
						<div id="home2" class="tab-pane active cont">
							<div class="row">
								<p class="xs-mt-10 xs-mb-10 mt-3">
									<span class="borderradius label pull-right" style="background-color: #00BFFF;">BorrowerZipcode</span>
									<span class="borderradius label pull-right" style="background-color: #6A5ACD;">BorrowerCity</span>
									<span class="borderradius label pull-right" style="background-color: #0000FF;">BorrowerState</span>

									<span class="borderradius label pull-right" style="background-color: #AA00FF;">Borrower</span>
									<span class="borderradius label pull-right" style="background-color: #BDA601;">Zipcode</span>
									<span class="borderradius label pull-right" style="background-color: #074D1E;">City</span>
									<span class="borderradius label pull-right" style="background-color: #02BD5A;">State</span>
									<span class="borderradius label pull-right" style="background-color: #BF6105;">Sub Product</span>
									<span class="borderradius label pull-right" style="background-color: #757575;">Empty Field</span>
									<?php if($IsFlood == '1'){ ?>
										<span class="borderradius label pull-right" style="background-color: #ff5c33;">Loan Number</span>
									<?php } ?>
								</p>
							</div>

							<div class="row">
								<div class="table-responsive defaultfontsize">
									<table class="table table-striped table-hover display nowrap"  id="table-bulkorder">
										<thead>
											<tr>

												<?php

												foreach ($headingsArray as $key => $value) {
													?><th><?php echo $value; ?></th><?php
												}

												?>
											</tr>
										</thead>
										<tbody>

											<?php


											$SubProduct_check = [];

											foreach ($arrayCode as $i => $v) {



												//$SubProductUID = $this->input->post('SubProductUID');

												$SubProduct_check[$i] = False;

												$msubproducts = array();

												if($v[4] == '' && $SubProductUID == '')
												{
													$default_subproducts = $this->common_model->get_defaultsubproduct($CustomerUID);
													$msubproductt = explode(",", $default_subproducts->DefaultProductSubValue);
													if(count($msubproductt)== 1){
														$msubproducts = $this->common_model->getsubproductbyUID($msubproductt[0]);
														if(count($msubproducts) == 0){
															$SubProduct_check[$i] = false;
														}else{
															$SubProductUID = $msubproducts->SubProductUID;
														}
													}else{
														$SubProduct_check[$i] = false;
													}

												}elseif($v[4] !=''){

													$msubproducts = $this->Orderentry_model->get_sub_product($v[4]);
													if(count($msubproducts) >0){
														$SubProductUID = $msubproducts->SubProductUID;
														$SubProduct_check[$i] = true;
													}else{
														$SubProduct_check[$i] = false;
													}

												}elseif ($SubProductUID !='') {
													$msubproducts = $this->common_model->getsubproductbyUID($SubProductUID);

													if(count($msubproducts) >0){
														$SubProductUID = $msubproducts->SubProductUID;
														$SubProduct_check[$i] = true;
													}else{
														$SubProduct_check[$i] = false;
													}
												}

												if(count($msubproducts) > 0){

													$mcustomerproducts[$i] = $this->Orderentry_model->get_all_in_customerproduct($CustomerUID,$ProductUID,$SubProductUID);

													if(count($mcustomerproducts[$i]) > 0){
														$SubProduct_check[$i] = true;
														$arrayCode[$i][0] = $mcustomerproducts[$i]->OrderTypeName;
														$arrayCode[$i][1] = "Normal";
														$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
														$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
														$arrayCode[$i][4] = $mcustomerproducts[$i]->SubProductName;
													}else{
														$SubProduct_check[$i] = false;
														$mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
														$arrayCode[$i][0] = '';
														$arrayCode[$i][1] = '';
														$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
														$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
														$arrayCode[$i][4] = $msubproducts->SubProductName;
													}
												}else{
													$mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
													$arrayCode[$i][0] = '';
													$arrayCode[$i][1] = '';
													$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
													$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
													$arrayCode[$i][4] = $v[4];

												}

											}


											foreach($arrayCode as $i => $a)
											{
												/*Assigning  fields for each column - consolidating hardcode*/
												$count = count($a);
												$field_count = 20;
												$field_countycolumn = 11;
												$field_templatecolumn = 16;
												$Loancolumn = $a[6];
												$Citycolumn = $a[10];
												$Countycolumn = $a[11];
												$Statecolumn = $a[12];
												$Zipcolumn = $a[13];
												$Templatecolumn = $a[16];
												$Sellercolumn = $a[19];
												$Firstborrowernamecolumn = $a[20];
												//for missing fields

												$chk_mailing1 = $a[26];

												$IsMailingAddress1 = $a[26]=  ( !empty($chk_mailing1) && trim(strtoupper($chk_mailing1)) ==trim('NO') ? 'others':'property');
												

												if($IsMailingAddress1=='others')
												{
													$MailingAddress1 	= $a[27];
													$MailingZipCode1	= $a[31];
													$MailingCityName1	= $a[28];
													$MailingStateCode1	= $a[30];
													$MailingCountyName1	= $a[29];

						//Validate state
													if(empty(validateState($MailingZipCode1,$MailingCountyName1,$MailingStateCode1)))
													{
														$MailingStateCode1 = '';
													}	

                        //County validation
													if($MailingCountyName1 == ''){
														if(empty($this->common_model->_getCounty_StateUID_ZipCode($MailingZipCode1,$MailingStateCode1)))
														{
															$MailingCountyName1 = '';
														}
													}
												}
												else
												{
													$MailingAddress1    = $a[27] = '';
													$MailingZipCode1    = $a[31] = '';
													$MailingCityName1   = $a[28] = '';
													$MailingStateCode1  = $a[30] = '';
													$MailingCountyName1 = $a[29] = '';
												}

					//If borrower1 address is not same
												$chk_mailing2 = $a[38];

												$IsMailingAddress2 = $a[38]=  ( !empty($chk_mailing2) && trim(strtoupper($chk_mailing2)) ==trim('NO') ? 'others':'property');

												if($IsMailingAddress2=='others')
												{
													$MailingAddress2 	= $a[39];
													$MailingZipCode2	= $a[43];
													$MailingCityName2	= $a[40];
													$MailingStateCode2	= $a[42];
													$MailingCountyName2	= $a[41];

						//Validate state
													if(empty(validateState($MailingZipCode2,$MailingCountyName2,$MailingStateCode2)))
													{
														$MailingStateCode2 = '';
													}	

                        //County validation
													if($MailingCountyName2 == ''){
														if(empty($this->common_model->_getCounty_StateUID_ZipCode($MailingZipCode2,$MailingStateCode2)))
														{
															$MailingCountyName2 = '';
														}
													}
												}
												else
												{
													$MailingAddress2    = $a[39] = '';
													$MailingZipCode2    = $a[43] = '';
													$MailingCityName2   = $a[40] = '';
													$MailingStateCode2  = $a[42] = '';
													$MailingCountyName2 = $a[41] = '';
												}

												// check state has valid based on city,county and zip code
												if(empty(validateState($Zipcolumn,$Countycolumn,$Statecolumn)))
												{
													$Statecolumn = '';
												}

												$highlighttr =  array_key_exists($i, $highlightarraykey) ? 'title="'.$highlightarraykey[$i].'" class="blinkbordertr"' : '' ;


												if(count($arrayCode[$i]) >= $field_count){

													if((count($arrayCode[$i])+4) % 6 != 0) {
														?> <tr <?php echo $highlighttr; ?> style="background-color: #757575; color: #fff;"> <?php
														foreach ($arrayCode[$i] as $key => $value) {

															?>
															<td><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
															<?php
														}

														?> </tr> <?php

													}else{

														if((count($a) >= $field_count )) {

															/*GET COUNTY BY ZIPCODE AND STATE*/
															if($Countycolumn == ''){

																$County = $this->common_model->_getCounty_StateUID_ZipCode($Zipcolumn,$Statecolumn);

																if(!empty($County)){
																	$arrayCode[$i][$field_countycolumn] = $County->CountyName;
																	$Countycolumn = $County->CountyName;
																}
															}



															$template = $this->Orderentry_model->get_template($Templatecolumn);

															if($Templatecolumn == ''){
																$default_template = $this->common_model->get_defaulttemplate_bycustomerUID($mcustomerproducts[$i]->CustomerUID);

																if(count($default_template) > 0){
																	$arrayCode[$i][$field_templatecolumn] = $default_template->TemplateName;
																	$template = $default_template;
																}
															}


															if($SubProduct_check[$i] == False){ ?>
																<tr <?php echo $highlighttr; ?> style="background-color: #BF6105; color: #fff;"> <?php
																foreach ($arrayCode[$i] as $key => $value) {

																	?>
																	<td ><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
																	<?php
																} ?> </tr> <?php
																/* Validating seller or borrower mandatory */
															}elseif($Sellercolumn == '' && $arrayCode[$i][$field_count] == ''){ ?>
																<tr <?php echo $highlighttr; ?> style="background-color: #AA00FF; color: #fff;"> <?php
																foreach ($arrayCode[$i] as $key => $value) {

																	?>
																	<td ><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
																	<?php
																} ?> </tr> <?php
															}elseif ($Zipcolumn == '' || !$this->Orderentry_model->getzipdetails($Zipcolumn) ) {
																?> <tr <?php echo $highlighttr; ?> style="background-color: #BDA601; color: #fff;"> <?php
																foreach ($arrayCode[$i] as $key => $value) {

																	?>
																	<td><?php
																		if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	}
																	  ?></td>
																	<?php
																}

																?> </tr> <?php


															}elseif ($Citycolumn == '' || !$this->Orderentry_model->get_city($Citycolumn,substr($Zipcolumn,0,5))) { // City Match with zip code
																?> <tr <?php echo $highlighttr; ?> style="background-color: #074D1E; color: #fff;"> <?php
																foreach ($arrayCode[$i] as $key => $value) {

																	?>
																	<td><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
																	<?php
																}

																?> </tr> <?php


															} elseif ($Statecolumn == '') {
																?> <tr <?php echo $highlighttr; ?> style="background-color: #02BD5A; color: #fff;"> <?php
																foreach ($arrayCode[$i] as $key => $value) {

																	?>
																	<td><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
																	<?php
																}

																?> </tr> <?php


															} elseif (($IsMailingAddress1=='others' && empty($MailingStateCode1)) ||($IsMailingAddress2=='others' && empty($MailingStateCode2))) {
																?> <tr <?php echo $highlighttr; ?> style="background-color: #0000FF; color: #fff;"> <?php
																foreach ($arrayCode[$i] as $key => $value) {

																	?>
																	<td><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
																	<?php
																}

																?> </tr> <?php


															}elseif (($IsMailingAddress1=='others' && empty($MailingCityName1)) ||($IsMailingAddress2=='others' && empty($MailingCityName2))) {
																?> <tr <?php echo $highlighttr; ?> style="background-color: #6A5ACD; color: #fff;"> <?php
																foreach ($arrayCode[$i] as $key => $value) {

																	?>
																	<td><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
																	<?php
																}

																?> </tr> <?php


															}
															elseif (($IsMailingAddress1=='others' && empty($MailingZipCode1)) ||($IsMailingAddress2=='others' && empty($MailingZipCode2))) {
																?> <tr <?php echo $highlighttr; ?> style="background-color: #00BFFF; color: #fff;"> <?php
																foreach ($arrayCode[$i] as $key => $value) {

																	?>
																	<td><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
																	<?php
																}

																?> </tr> <?php


															} else if ($IsFlood == '1') {
																if($Loancolumn==''){
																	?> <tr <?php echo $highlighttr; ?> style="background-color: #ff5c33; color: #fff;"> <?php
																	foreach ($arrayCode[$i] as $key => $value) {

																		?>
																		<td><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
																		<?php

																	}	?>
																	</tr> <?php
																}

															}else{
																?>
																<tr <?php echo $highlighttr; ?> style="color: #090809;"> <?php
																foreach ($arrayCode[$i] as $key => $value) {

																	?>
																	<td><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
																	<?php
																}

																?> </tr> <?php
															}




														}else{
															?> <tr <?php echo $highlighttr; ?> style="background-color: #757575; color: #fff;"> <?php
															foreach ($arrayCode[$i] as $key => $value) {

																?>
																<td><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
																<?php
															}

															?> </tr> <?php
														}

													}
												}else{

													?> <tr <?php echo $highlighttr; ?> style="background-color: #757575; color: #fff;"> <?php
													foreach ($arrayCode[$i] as $key => $value) {

														?>
														<td><?php if($i == 13 && strlen($value) == 9){
																		echo substr($value,0,5).'-'.substr($value,5,8);
																	}else{
																		echo $value;

																	} ?></td>
														<?php
													}

													?>
												</tr>

												<?php

											}

										}

										?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div id="profile2" class="tab-pane cont">
						<div class="tablescroll defaultfontsize">
							<table class="table table-striped table-hover"  id="table-preview2">
								<thead>
									<tr>
										<th>SNO</th>
										<th>LOAN NUMBER</th>
										<th>DOCUMENT NAME</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($FileUploadPreview as $key => $file) { ?>
										<tr>
											<td><?php echo $key + 1; ?></td>
											<td><?php echo $file->LoanNumber; ?></td>
											<td><?php echo $file->DocumentName; ?></td>
										</tr>

									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<?php

		}else{
			echo json_encode(array('error'=>'1','message'=>'Please Upload Valid File'));
		}

	}else{
		echo json_encode(array('error'=>'1','message'=>'Please upload File'));
	}

}


function save_bulkentry()
{
	if(isset($_FILES['file'])){
		$lib = $this->load->library('Excel');

		$inputFile = $_FILES['file']['tmp_name'];

		$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
		$temp = explode(".", $_FILES["file"]["name"]);

		$allowedExts = array("xlsx","xls");

		$extension = end($temp);

		if( in_array($extension, $allowedExts)) {

			try {

				$inputFileType = PHPExcel_IOFactory::identify($inputFile);
				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
				$worksheets = $objReader->listWorkSheetNames($inputFile);
				$objReader->setLoadSheetsOnly($worksheets[0]);
					// $objReader->setReadDataOnly(true);
				$objPHPExcel = $objReader->load($inputFile);

				$FileName = 'BulkImportSheet_'.date("d_m_Y H_i_s").'.xlsx';
				$UploadFilePath = 'uploads/BulkLoanImportSheet/'.$FileName;
				$BulkImportUploadFilePath = 'BulkLoanImportSheet/'.$FileName;
				if (!file_exists('uploads/BulkLoanImportSheet')) 
				{
					mkdir('uploads/BulkLoanImportSheet', 0777, true);
				}

				move_uploaded_file($inputFile, $UploadFilePath);

			}
			catch(Exception $e)
			{

				$msg = 'Error Uploading file';
				echo json_encode(array('error'=>'1','message'=>$msg));
				exit;
			}

			$error = 0;
			$success = 0;
			$CustomerUID = $this->input->post('CustomerUID');
			$ProductUID = $this->input->post('ProductUID');
			$SubProductUID = $this->input->post('SubProductUID');

			$X1result =$this->Orderentry_model->CheckX1Order($CustomerUID,$ProductUID,$SubProductUID);

			if($X1result)
			{
				$BorrowerType = $this->input->post('BorrowerType');
				$PropertyType = $this->input->post('PropertyType');
				$TransactionType = $this->input->post('TransactionType');
			}

			$IsFlood = $this->Orderentry_model->CheckIsFlood($CustomerUID,$ProductUID);

			if($CustomerUID =='' || $ProductUID ==''){
				echo json_encode(array('error'=>'1','message'=>'Select the Required Fields'));exit;
			}
			
			if($X1result)
			{
				if($TransactionType =='' || $PropertyType ==''|| $BorrowerType ==''){
					echo json_encode(array('error'=>'1','message'=>'Select the Required Fields'));exit;
				}
			}

			$objWorksheet = $objPHPExcel->getActiveSheet();
        		//excel with first row header, use header as key
			$highestRow = $objWorksheet->getHighestDataRow();
			$highestColumn = $objWorksheet->getHighestDataColumn();


			$headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);
			$headingsArray = $headingsArray[1];

			$arrayCode = array();
			$r = -1;
			$headingArray = array();
			for ($row = 2; $row <= $highestRow; ++$row) {
				$dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
					if($this->isEmptyRow(reset($dataRow))) { continue; } // skip empty row
					++$r;
					$arrayCode[$r][0] = '';
					$arrayCode[$r][1] = '';
					$arrayCode[$r][2] = '';
					$arrayCode[$r][3] = '';
					$i = 4;
					foreach($headingsArray as $columnKey => $columnHeading) {

						$arrayCode[$r][$i] = $dataRow[$row][$columnKey];
						$i++;
					}

				}


				$SubProduct_check = [];

				foreach ($arrayCode as $i => $v) {

					$SubProductUID = $this->input->post('SubProductUID');

					$SubProduct_check[$i] = False;

					$msubproducts = array();

					if($v[4] == '' && $SubProductUID == '')
					{
						$default_subproducts = $this->common_model->get_defaultsubproduct($CustomerUID);
						$msubproductt = explode(",", $default_subproducts->DefaultProductSubValue);
						if(count($msubproductt)== 1){
							$msubproducts = $this->common_model->getsubproductbyUID($msubproductt[0]);
							if(count($msubproducts) == 0){
								$SubProduct_check[$i] = false;
							}else{
								$SubProductUID = $msubproducts->SubProductUID;
							}
						}else{
							$SubProduct_check[$i] = false;
						}

					}elseif($v[4] !=''){

						$msubproducts = $this->Orderentry_model->get_sub_product($v[4]);
						if(count($msubproducts) >0){
							$SubProductUID = $msubproducts->SubProductUID;
							$SubProduct_check[$i] = true;
						}else{
							$SubProduct_check[$i] = false;
						}

					}elseif ($SubProductUID !='') {
						$msubproducts = $this->common_model->getsubproductbyUID($SubProductUID);

						if(count($msubproducts) >0){
							$SubProductUID = $msubproducts->SubProductUID;
							$SubProduct_check[$i] = true;
						}else{
							$SubProduct_check[$i] = false;
						}
					}


					if(count($msubproducts) > 0){

						$mcustomerproducts[$i] = $this->Orderentry_model->get_all_in_customerproduct($CustomerUID,$ProductUID,$SubProductUID);

						if(count($mcustomerproducts[$i]) > 0){
							$SubProduct_check[$i] = true;
							$arrayCode[$i][0] = $mcustomerproducts[$i]->OrderTypeName;
							$arrayCode[$i][1] = "Normal";
							$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
							$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
							$arrayCode[$i][4] = $mcustomerproducts[$i]->SubProductName;
						}else{
							$SubProduct_check[$i] = false;
							$mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
							$arrayCode[$i][0] = '';
							$arrayCode[$i][1] = '';
							$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
							$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
							$arrayCode[$i][4] = $msubproducts->SubProductName;
						}
					}else{
						$mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
						$arrayCode[$i][0] = '';
						$arrayCode[$i][1] = '';
						$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
						$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
						$arrayCode[$i][4] = '';

					}
				}
				//array_unshift($headingsArray, "Order Type", "Priority","Customer/Client","Product");

				$returnvalue = False;


					// $arrayCode = array_map('array_filter', $arrayCode);

				$FailedData=[];
				$InsertedOrderUID = '';

				$html = '';
				$html .='<div class="tab-container">';
				$html .= '<ul class="nav nav-tabs nav-tabs-success">';
				$html .= '<li class="active"><a href="#success-table" data-toggle="tab"><strong>Imported&nbsp;<i class="fa fa-check-circle" style="color:green;"></i></strong></a></li>';
				$html .= '<li><a href="#error-data" data-toggle="tab"><strong>Not Imported&nbsp;<i class="fa fa-times-circle-o" style="color:red;"></i></strong></a></li>';
				$html .= '</ul>';
				$html .= '<div class="tab-content">';
				$html .= '<div id="success-table" class="tab-pane active cont">';

				$html.='<div class="btn-group" style="margin-bottom: 20pt;">
				<button type="button" class="btn btn-success" id="pdfimport">PDF</button>
				<button type="button" id="excelimport" class="btn btn-success">Excel</button>
				</div>';
				$html .= '<div class="col-sm-12">';
				$html .= '<div class="table-responsive panel panel-default panel-table table-responsive defaultfontsize">';
				$html .= '<table class="table table-striped table-hover" id="importdata">';
				$html .= '<tr>';
				$html .= '<th>Order Number</th>';
				$html .= '<th>Order Type</th>';
				$html .= '<th>Priority</th>';
				$html .= '<th>Customer/Client</th>';
				$html .= '<th>Product</th>';
				$html .= '<th>Sub Product</th>';
				$html .= '<th>Alternate order Number</th>';
				$html .= '<th>Loan Number</th>';
				$html .= '<th>Loan Amount</th>';
				$html .= '<th>Customer Reference Number</th>';
				$html .= '<th>Property Address</th>';
				$html .= '<th>Property City</th>';
				$html .= '<th>Property County</th>';
				$html .= '<th>Property State</th>';
				$html .= '<th>Property Zip Code</th>';
				$html .= '<th>APN</th>';
				$html .= '<th>Additional Info</th>';
				$html .= '<th>Template</th>';
				$html .= '<th>Email Report to</th>';
				$html .= '<th>Attention Name</th>';
				$html .= '<th>Seller</th>';
				$html .= '<th>Borrower Name 1</th>';
				$html .= '<th>Email 1</th>';
				$html .= '<th>Home Number 1</th>';
				$html .= '<th>Work Number 1</th>';
				$html .= '<th>Cell Number 1</th>';
				$html .= '<th>Social 1</th>';
				/*$html .= '<th>Same as Property Address</th>';
				$html .= '<th>Borrower1 Address</th>';
				$html .= '<th>Borrower1  City</th>';
				$html .= '<th>Borrower1  County</th>';
				$html .= '<th>Borrower1  State</th>';
				$html .= '<th>Borrower1  Zip Code</th>';*/
				$html .= '<th>Borrower Name 2</th>';
				$html .= '<th>Email 2</th>';
				$html .= '<th>Home Number 2</th>';
				$html .= '<th>Work Number 2</th>';
				$html .= '<th>Cell Number 2</th>';
				$html .= '<th>Social 2</th>';
				/*$html .= '<th>Same as Property Address</th>';
				$html .= '<th>Borrower2 Address</th>';
				$html .= '<th>Borrower2  City</th>';
				$html .= '<th>Borrower2  County</th>';
				$html .= '<th>Borrower2  State</th>';
				$html .= '<th>Borrower2  Zip Code</th>';*/
				$html .= '</tr>';
				foreach($arrayCode as $i => $a)
				{
					// str_replace to remove special char like '<>/\"
					$a = str_replace(array('\'', '"', '<', '>', '/', '\\'), '', $a); 

					/*Assigning fields for each column - Consolidating hardcode values*/
					$count = count($a);
					$field_count = 20;
					$field_Countycolumn = 11;
					$field_countcolumn = 20;
					$Altordernocolumn = $a[5];
					$Loancolumn = $a[6];
					$Loanamountcolumn = $a[7];
					$Customerrefnocolumn = $a[8];
					$Address1column = $a[9];
					$Citycolumn = $a[10];
					$Countycolumn = $a[11];
					$Statecolumn = $a[12];
					$Zipcolumn = $a[13];
					$Apncolumn = $a[14];
					$Additionalinfocolumn = $a[15];
					$Templatecolumn = $a[16];
					$Emailreporttocolumn = $a[17];
					$Attentioncolumn = $a[18];
					$Sellercolumn = $a[19];
					$Firstborrowernamecolumn = $a[20];

					/* @author Parthasarathy commented because not matching with live bulk import template*/
                  /* 
					//If borrower1 address is not same
					$chk_mailing1 = $a[26];

                    $IsMailingAddress1 = $a[26]=  ( !empty($chk_mailing1) && trim(strtoupper($chk_mailing1)) ==trim('NO') ? 'others':'property');

					if($IsMailingAddress1=='others')
					{
						$MailingAddress1 	= $a[27];
						$MailingZipCode1	= $a[31];
						$MailingCityName1	= $a[28];
						$MailingStateCode1	= $a[30];
						$MailingCountyName1	= $a[29];

						//Validate state
						if(empty(validateState($MailingZipCode1,$MailingCountyName1,$MailingStateCode1)))
						{
						  $MailingStateCode1 = '';
						}	

                        //County validation
						if($MailingCountyName1 == ''){
							if(empty($this->common_model->_getCounty_StateUID_ZipCode($MailingZipCode1,$MailingStateCode1)))
							{
								$MailingCountyName1 = '';
							}
						}
					}
					else
					{
						$MailingAddress1    = $a[27] = '';
						$MailingZipCode1    = $a[31] = '';
						$MailingCityName1   = $a[28] = '';
						$MailingStateCode1  = $a[30] = '';
						$MailingCountyName1 = $a[29] = '';
					}
					            
					//If borrower1 address is not same
					$chk_mailing2 = $a[38];

                    $IsMailingAddress2 = $a[38]=  ( !empty($chk_mailing2) && trim(strtoupper($chk_mailing2)) ==trim('NO') ? 'others':'property');

					if($IsMailingAddress2=='others')
					{
						$MailingAddress2 	= $a[39];
						$MailingZipCode2	= $a[43];
						$MailingCityName2	= $a[40];
						$MailingStateCode2	= $a[42];
						$MailingCountyName2	= $a[41];

						//Validate state
						if(empty(validateState($MailingZipCode2,$MailingCountyName2,$MailingStateCode2)))
						{
						  $MailingStateCode2 = '';
						}	

                        //County validation
						if($MailingCountyName2 == ''){
							if(empty($this->common_model->_getCounty_StateUID_ZipCode($MailingZipCode2,$MailingStateCode2)))
							{
								$MailingCountyName2 = '';
							}
						}
					}
					else
					{
						$MailingAddress2    = $a[39] = '';
						$MailingZipCode2    = $a[43] = '';
						$MailingCityName2   = $a[40] = '';
						$MailingStateCode2  = $a[42] = '';
						$MailingCountyName2 = $a[41] = '';
					}*/
					
     
					/**
					*  Function altered to encrypt social security number
					*  
					*  @author: D.Samuel Prabhu	
					*  @since : 11 Feb 2020
					*/
					$secret = $this->config->item('encryption_key');
					$encrypt = new AesCtr();
					$EncrptSocialNumber = $encrypt->encrypt($a[25], $secret, 256);

                    $a[25] = $EncrptSocialNumber;
                    $a[31] = $encrypt->encrypt($a[37], $secret, 256);

					//end of encryption

					// validate state insvalid or invalid
					if(empty(validateState($Zipcolumn,$Countycolumn,$Statecolumn)))
					{
					  $Statecolumn = '';
					}

					//for missing fields
					if(count($arrayCode[$i]) >= $field_count){

						if((count($arrayCode[$i])+4) % 6 != 0) {

							$error = $error + 1;

						}else{

							if((count($a) >= $field_count )) {

								/*GET COUNTY BY ZIPCODE AND STATE*/
								if($Countycolumn == ''){

									$County = $this->common_model->_getCounty_StateUID_ZipCode($Zipcolumn,$Statecolumn);

									if(!empty($County)){
										$arrayCode[$i][$field_Countycolumn] = $County->CountyName;
										$Countycolumn = $County->CountyName;
									}
								}

								$template = $this->Orderentry_model->get_template($Templatecolumn);

								if($Templatecolumn == ''){
									$default_template = $this->common_model->get_defaulttemplate_bycustomerUID($mcustomerproducts[$i]->CustomerUID);
									if(count($default_template) > 0){
										$template = $default_template;
									}
								}								


								if($SubProduct_check[$i] == False){
									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);
									/* Save - Borrower or Seller mandatory */
								}elseif($Sellercolumn == '' && $Firstborrowernamecolumn == ''){

									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);

								}elseif ($Statecolumn == '') {

									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);

								}elseif ($Citycolumn == '') {

									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);

								}elseif ($Zipcolumn == '') {

									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);

								}elseif ($IsFlood == '1') {
									if ($Loancolumn == '') {
										$error = $error + 1;
										array_push($FailedData, $arrayCode[$i]);
									}
								}
								/*elseif($IsMailingAddress1=='others' && $MailingAddress1 == '')
								{
									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);
								}
								elseif($IsMailingAddress1=='others' && $MailingCityName1 == '')
								{
									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);
								}
								elseif($IsMailingAddress1=='others' && $MailingStateCode1 == '')
								{
									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);
								}
								elseif($IsMailingAddress1=='others' && $MailingCountyName1 == '')
								{
									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);
								}
								elseif($IsMailingAddress1=='others' && $MailingZipCode1 == '')
								{
									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);
								}
								elseif($IsMailingAddress2=='others' && $MailingAddress2 == '')
								{
									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);
								}elseif($IsMailingAddress2=='others' && $MailingCityName2 == '')
								{
									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);
								}elseif($IsMailingAddress2=='others' && $MailingStateCode2 == '')
								{
									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);
								}elseif($IsMailingAddress2=='others' && $MailingCountyName2 == '')
								{
									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);
								}elseif($IsMailingAddress2=='others' && $MailingZipCode2 == '')
								{
									$error = $error + 1;
									array_push($FailedData, $arrayCode[$i]);
								}*/
								/*elseif($IsMailingAddress1=='others')
								{
									if ($MailingAddress1 == '' || $MailingCityName1 == '' || $MailingStateCode1 == '' || $MailingCountyName1 == '' || $MailingZipCode1 == '') {
										$error = $error + 1;
										array_push($FailedData, $arrayCode[$i]);
									}	


										

								}							
				         		elseif($IsMailingAddress2=='others')
								{
									if ($MailingAddress2 == '' || $MailingCityName2 == '' || $MailingStateCode2 == '' || $MailingCountyName2 == '' || $MailingZipCode2 == '') {

										$error = $error + 1;
										array_push($FailedData, $arrayCode[$i]);
									}


								}*/
								else
								{
										

									$data['OrderTypeUID'] = $mcustomerproducts[$i]->OrderTypeUID;
									$data['PriorityUID'] = 2;
									$data['CustomerUID'] = $mcustomerproducts[$i]->CustomerUID;
									$data['SubProductUID'] = $mcustomerproducts[$i]->SubProductUID;
									$data['OrderNumber'] = '';
									$data['AltORderNumber'] = $Altordernocolumn;
									$data['LoanNumber'] = $Loancolumn;
									$data['LoanAmount'] = $Loanamountcolumn;
									$data['CustomerRefNum'] = $Customerrefnocolumn;
									$data['PropertyAddress1'] = $Address1column;
									if(strlen($Zipcolumn) == 9 && $Zipcolumn{5} != '-'){
										$Zipcolumn = substr($Zipcolumn,0,5).'-'.substr($Zipcolumn,5,8);
									}
									$data['PropertyZipcode'] = $Zipcolumn;
									$data['APN'] = $Apncolumn;
									$data['AdditionalInfo'] = $Additionalinfocolumn;
									$data['EmailReportTo'] = $Emailreporttocolumn;
									$data['PropertyStateCode'] = $Statecolumn;
									$data['PropertyCityName'] = $Citycolumn;
									$data['PropertyCountyName'] = $Countycolumn;
									$data['AttentionName'] = $Attentioncolumn;
									$data['PRSocialNumber'] = $EncrptSocialNumber;
									$data['BulkImportSheet'] = $BulkImportUploadFilePath;

									if($TransactionType !='' || $PropertyType !=''|| $BorrowerType !='')
									{
										$data['TransactionType'] = $TransactionType;
										$data['PropertyType'] = $PropertyType;
										$data['BorrowerType'] = $BorrowerType;
										$data['ProductUID'] = $ProductUID;
									}


									if(count($template) > 0){

										$data['TemplateUID'] = $template->TemplateUID;
									}else{
										$data['TemplateUID'] = null;
									}

									/*********
									If EmailReportTo fields is empty then CustomerPContactEmailID will get updated
									**********/

									if($Emailreporttocolumn == '')
									{
										$data['EmailReportTo'] = $mcustomerproducts[$i]->CustomerPContactEmailID;
									}
									else
									{
										$data['EmailReportTo'] = $Emailreporttocolumn;
									}
									$ProjectUID = $this->input->post('ProjectUID');

									$data['ProjectUID'] = $ProjectUID;

									/**************/

									$files_upload = $_FILES['MIME_FILES'];

									$result = $this->Orderentry_model->savebulkentry_order($data,$a,$files_upload);
									$InsertedOrderUID .= $result.',';
									$OrderNumber=$this->common_model->GetOrderNumberByOrderUID($result);
									$OrderUID=$OrderNumber->OrderUID;
									$OrderNo=$OrderNumber->OrderNumber;

									if($result) {

										$success = $success + 1;

										/**
									    * Function altered to add notes for bulk order
									    * @author : D.Samuel Prabhu
									    * @since  : 10 Feb 2019
									    */ 
									    
										 $Notes ="Order ".$OrderNumber->OrderNumber." created on ".date('m-d-Y H:i:s');

									     $SectionUID = $this->common_model->GetNoteTypeUID("Order Entry Info")->SectionUID;
										
										 $this->common_model->insertordernotes($OrderNumber->OrderUID, $SectionUID, $Notes);

										 //End of notes insert

										$results=$this->Orderentry_model->SelectImportedData($result);
										if($results)
										{
											foreach ($results as $key=> $value)
											{
												$data = $this->Orderentry_model->GetPropertyrolesBorrowerDetails($value->OrderUID);
												$Seller = $this->Orderentry_model->GetPropertyrolesSellerDetails($value->OrderUID);
												$SellerName1 = '';
												if(!empty($Seller)){
													$SellerName1 = $Seller[0]['PRName'];
												}
                                                
                                                //For decrypting social number
												$encrypt = new AesCtr();
		
												$PRName1 = $data[0]['PRName'];
												$PREmailID1 = $data[0]['PREmailID'];
												$PRHomeNumber1 = $data[0]['PRHomeNumber'];
												$PRWorkNumber1 = $data[0]['PRWorkNumber'];
												$PRCellNumber1 = $data[0]['PRCellNumber'];

												//decrypting social number
												$PRSocialNumber1 = 	$encrypt->decrypt($data[0]['PRSocialNumber'],$this->config->item('encryption_key'),256);
												/* @author Parthasarathy commented because not matching with live bulk import template*/
												
												/*$IsSameAddress1 	=  ($data[0]['IsMailingAddress']=='property'?'YES' :'NO');
												$BorrowerAddress1 	= $data[0]['MailingAddress1'];
												$BorrowerZipcode1 	= $data[0]['MailingZipCode'];
												$BorrowerCity1 		= $data[0]['MailingCityName'];
												$BorrowerState1 	= $data[0]['MailingStateCode'];
												$BorrowerCounty1 	= $data[0]['MailingCountyName'];*/

												$PRName2 			= $data[1]['PRName'];
												$PREmailID2 		= $data[1]['PREmailID'];
												$PRHomeNumber2 		= $data[1]['PRHomeNumber'];
												$PRWorkNumber2 		= $data[1]['PRWorkNumber'];
												$PRCellNumber2 		= $data[1]['PRCellNumber'];
												//decrypting social number

												$PRSocialNumber2 = 	$encrypt->decrypt($data[1]['PRSocialNumber'],$this->config->item('encryption_key'),256);
												/*$IsSameAddress2 	=  ($data[1]['IsMailingAddress']=='property'?'YES' :'NO');
												$BorrowerAddress2 	= $data[1]['MailingAddress1'];
												$BorrowerZipcode2 	= $data[1]['MailingZipCode'];
												$BorrowerCity2 		= $data[1]['MailingCityName'];
												$BorrowerState2 	= $data[1]['MailingStateCode'];
												$BorrowerCounty2 	= $data[1]['MailingCountyName'];*/

												$html.= '<tr>';
												$html.= '<td>'.$OrderNo.'</td>';
												$html.= '<td>'.$value->OrderTypeName.'</td>';
												$html.= '<td>'.$value->PriorityName.'</td>';
												$html.= '<td>'.$value->CustomerName.'</td>';
												$html.= '<td>'.$value->ProductName.'</td>';
												$html.= '<td>'.$value->SubProductName.'</td>';
												$html.= '<td>'.$value->AltORderNumber	.'</td>';
												$html.= '<td>'.$value->LoanNumber.'</td>';
												$html.= '<td>'.$value->LoanAmount.'</td>';
												$html.= '<td>'.$value->CustomerRefNum.'</td>';
												$html.= '<td>'.$value->PropertyAddress1.','.$value->PropertyAddress2.'</td>';
												$html.= '<td>'.$value->PropertyCityName.'</td>';
												$html.= '<td>'.$value->PropertyCountyName.'</td>';
												$html.= '<td>'.$value->PropertyStateCode.'</td>';
												$html.= '<td>'.$value->PropertyZipcode.'</td>';
												$html.= '<td>'.$value->APN.'</td>';
												$html.= '<td></td>';
												$html.= '<td>'.$value->TemplateName.'</td>';
												$html.= '<td>'.$value->EmailReportTo.'</td>';
												$html.= '<td>'.$value->AttentionName.'</td>';
												$html.= '<td>'.$SellerName1.'</td>';
												$html.= '<td>'.$PRName1.'</td>';
												$html.= '<td>'.$PREmailID1.'</td>';
												$html.= '<td>'.$PRHomeNumber1.'</td>';
												$html.= '<td>'.$PRWorkNumber1.'</td>';
												$html.= '<td>'.$PRCellNumber1.'</td>';
												$html.= '<td>'.$PRSocialNumber1.'</td>';
												/*$html.= '<td>'.$IsSameAddress1.'</td>';
												$html.= '<td>'.$BorrowerAddress1.'</td>';
												$html.= '<td>'.$BorrowerCity1.'</td>';
												$html.= '<td>'.$BorrowerCounty1.'</td>';
												$html.= '<td>'.$BorrowerState1.'</td>';
												$html.= '<td>'.$BorrowerZipcode1.'</td>';*/
												$html.= '<td>'.$PRName2.'</td>';
												$html.= '<td>'.$PREmailID2.'</td>';
												$html.= '<td>'.$PRHomeNumber2.'</td>';
												$html.= '<td>'.$PRWorkNumber2.'</td>';
												$html.= '<td>'.$PRCellNumber2.'</td>';
												$html.= '<td>'.$PRSocialNumber2.'</td>';
												/*$html.= '<td>'.$IsSameAddress2.'</td>';
												$html.= '<td>'.$BorrowerAddress2.'</td>';
												$html.= '<td>'.$BorrowerCity2.'</td>';
												$html.= '<td>'.$BorrowerCounty2.'</td>';
												$html.= '<td>'.$BorrowerState2.'</td>';
												$html.= '<td>'.$BorrowerZipcode2.'</td>';*/
												$html.= '</tr>';
											}
										}
									}
									else{
										$error = $error + 1;
										// print_r($FailedData);
									}
								}

							}
							else
							{
								$error = $error + 1;
								// print_r($FailedData);

							}
						}
					}
				}

				$html .= '</table></div></div></div>';
				// echo $html;

				$html .= '<div id="error-data" class="tab-pane cont">';
				$html.='<div class="btn-group" style="margin-bottom: 20pt;">
				<button type="button" class="btn btn-success" id="pdferror">PDF</button>
				<button type="button" id="excelerror" class="btn btn-success">Excel</button>
				</div>';
				$html .= '<div class="col-sm-12">';
				$html .= '<div class="table-responsive panel panel-default panel-table table-responsive defaultfontsize">';
				$html .= '<table class="table table-striped table-hover">';
				$html .= '<tr>';
				$html .= '<th>Order Type</th>';
				$html .= '<th>Priority</th>';
				$html .= '<th>Customer/Client</th>';
				$html .= '<th>Product</th>';
				$html .= '<th>Sub Product</th>';
				$html .= '<th>Alternate order Number</th>';
				$html .= '<th>Loan Number</th>';
				$html .= '<th>Loan Amount</th>';
				$html .= '<th>Customer Reference Number	</th>';
				$html .= '<th>Property Address</th>';
				$html .= '<th>Property City</th>';
				$html .= '<th>Property County</th>';
				$html .= '<th>Property State</th>';
				$html .= '<th>Property Zip Code</th>';
				$html .= '<th>APN</th>';
				$html .= '<th>Additional Info</th>';
				$html .= '<th>Template</th>';
				$html .= '<th>Email Report to</th>';
				$html .= '<th>Attention Name</th>';
				$html .= '<th>Seller</th>';
				$html .= '<th>Borrower Name 1</th>';
				$html .= '<th>Email 1</th>';
				$html .= '<th>Home Number 1</th>';
				$html .= '<th>Work Number 1</th>';
				$html .= '<th>Cell Number 1</th>';
				$html .= '<th>Social 1</th>';
				/*$html .= '<th>Same as Property Address</th>';
				$html .= '<th>Borrower1 Address</th>';
				$html .= '<th>Borrower1  City</th>';
				$html .= '<th>Borrower1  County</th>';
				$html .= '<th>Borrower1  State</th>';
				$html .= '<th>Borrower1  Zip Code</th>';*/
				$html .= '<th>Borrower Name 2</th>';
				$html .= '<th>Email 2</th>';
				$html .= '<th>Home Number 2</th>';
				$html .= '<th>Work Number 2</th>';
				$html .= '<th>Cell Number 2</th>';
				$html .= '<th>Social 2</th>';
				/*$html .= '<th>Same as Property Address</th>';
				$html .= '<th>Borrower2 Address</th>';
				$html .= '<th>Borrower2  City</th>';
				$html .= '<th>Borrower2  County</th>';
				$html .= '<th>Borrower2  State</th>';
				$html .= '<th>Borrower2  Zip Code</th>';*/
				$html .= '</tr>';

				foreach ($FailedData as $key => $value)
				{
					$html.= '<tr>';
					$html.= '<td>'.$value[0].'</td>';
					$html.= '<td>'.$value[1].'</td>';
					$html.= '<td>'.$value[2].'</td>';
					$html.= '<td>'.$value[3].'</td>';
					$html.= '<td>'.$value[4].'</td>';
					$html.= '<td>'.$value[5].'</td>';
					$html.= '<td>'.$value[6].'</td>';
					$html.= '<td>'.$value[7].'</td>';
					$html.= '<td>'.$value[8].'</td>';
					$html.= '<td>'.$value[9].'</td>';
					$html.= '<td>'.$value[10].'</td>';
					$html.= '<td>'.$value[11].'</td>';
					$html.= '<td>'.$value[12].'</td>';
					$html.= '<td>'.$value[13].'</td>';
					$html.= '<td>'.$value[14].'</td>';
					$html.= '<td>'.$value[15].'</td>';
					$html.= '<td>'.$value[16].'</td>';
					$html.= '<td>'.$value[17].'</td>';
					$html.= '<td>'.$value[18].'</td>';
					$html.= '<td>'.$value[19].'</td>';
					$html.= '<td>'.$value[20].'</td>';
					$html.= '<td>'.$value[21].'</td>';
					$html.= '<td>'.$value[22].'</td>';
					$html.= '<td>'.$value[23].'</td>';
					$html.= '<td>'.$value[24].'</td>';
					/*$html.= '<td>'.$value[25].'</td>';
					$html.= '<td>'.$value[26].'</td>';
					$html.= '<td>'.$value[27].'</td>';
					$html.= '<td>'.$value[28].'</td>';
					$html.= '<td>'.$value[29].'</td>';
					$html.= '<td>'.$value[30].'</td>';*/
					$html.= '<td>'.$value[31].'</td>';
					$html.= '<td>'.$value[32].'</td>';
					$html.= '<td>'.$value[33].'</td>';
					$html.= '<td>'.$value[34].'</td>';
					$html.= '<td>'.$value[35].'</td>';
					$html.= '<td>'.$value[36].'</td>';
					$html.= '<td>'.$value[37].'</td>';
					/*$html.= '<td>'.$value[38].'</td>';
					$html.= '<td>'.$value[39].'</td>';
					$html.= '<td>'.$value[40].'</td>';
					$html.= '<td>'.$value[41].'</td>';
					$html.= '<td>'.$value[42].'</td>';
					$html.= '<td>'.$value[43].'</td>';*/
					$html.= '</tr>';
				}
				$html .= '</table>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '</div>';
				$html .= '<input type="hidden" value="'.$InsertedOrderUID.'" name="InsertedOrderUID[]" id="InsertedOrderUID">';
				echo $html;


					// $curl = curl_init();

					// curl_setopt_array($curl, array(
					//   CURLOPT_URL => "https://www.ordersportal.isgnsolutions.com/tOrderPricingModify",
					//   CURLOPT_RETURNTRANSFER => true,
					//   CURLOPT_ENCODING => "",
					//   CURLOPT_MAXREDIRS => 10,
					//   CURLOPT_TIMEOUT => 300,
					//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					//   CURLOPT_CUSTOMREQUEST => "GET",
					//   CURLOPT_HTTPHEADER => array(
					//     "Cache-Control: no-cache"
					//   ),
					// ));

					// $response = curl_exec($curl);
					// $err = curl_error($curl);

					// curl_close($curl);

					// if ($err) {
					//   echo "cURL Error #:" . $err;
					// } else {
					//   echo $response;
					// }

				// echo '<h3 style="margin:0"><span class="text-success modal-main-icon mdi mdi-check"></span> Success  '.$success .'</h3>';
				// echo '<h3 style="margin:0"><span class="text-danger modal-main-icon mdi mdi-close"></span> Error  '.$error .'</h3>';


			}else{
				echo json_encode(array('error'=>'1','message'=>'Please Upload Valid File'));
			}
		}else{
			echo json_encode(array('error'=>'1','message'=>'Please upload File'));
		}

	}


	// function save_bulkentry()
	// {
	// 	if(isset($_FILES['file'])){
	// 		$lib = $this->load->library('Excel');

	// 		$inputFile = $_FILES['file']['tmp_name'];

	// 		$extension = strtoupper(pathinfo($inputFile, PATHINFO_EXTENSION));
	// 		$temp = explode(".", $_FILES["file"]["name"]);

	// 		$allowedExts = array("xlsx","xls");

	// 		$extension = end($temp);

	// 		if( in_array($extension, $allowedExts)) {

	// 			try {

	// 				$inputFileType = PHPExcel_IOFactory::identify($inputFile);
	// 				$objReader = PHPExcel_IOFactory::createReader($inputFileType);
	// 				$worksheets = $objReader->listWorkSheetNames($inputFile);
	// 				$objReader->setLoadSheetsOnly($worksheets[0]);
	// 				// $objReader->setReadDataOnly(true);
	// 				$objPHPExcel = $objReader->load($inputFile);

	// 			}
	// 			catch(Exception $e)
	// 			{

	// 				$msg = 'Error Uploading file';
	// 				echo json_encode(array('error'=>'1','message'=>$msg));
	// 				exit;
	// 			}

	// 			$error = 0;
	// 			$success = 0;
	// 			$CustomerUID = $this->input->post('CustomerUID');
	// 			$ProductUID = $this->input->post('ProductUID');
	// 			$SubProductUID = $this->input->post('SubProductUID');

	// 			if($CustomerUID =='' || $ProductUID ==''){
	// 				echo json_encode(array('error'=>'1','message'=>'Select the Required Fields'));exit;
	// 			}

	// 			$objWorksheet = $objPHPExcel->getActiveSheet();
 //        		//excel with first row header, use header as key
	// 			$highestRow = $objWorksheet->getHighestDataRow();
	// 			$highestColumn = $objWorksheet->getHighestDataColumn();


	// 			$headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);
	// 			$headingsArray = $headingsArray[1];





	// 			$arrayCode = array();
	// 			$r = -1;
	// 			$headingArray = array();
	// 			for ($row = 2; $row <= $highestRow; ++$row) {
	// 				$dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
	// 				++$r;
	// 				$arrayCode[$r][0] = '';
	// 				$arrayCode[$r][1] = '';
	// 				$arrayCode[$r][2] = '';
	// 				$arrayCode[$r][3] = '';
	// 				$i = 4;
	// 				foreach($headingsArray as $columnKey => $columnHeading) {

	// 					$arrayCode[$r][$i] = $dataRow[$row][$columnKey];
	// 					$i++;
	// 				}

	// 			}


	// 			$SubProduct_check = [];

	// 			foreach ($arrayCode as $i => $v) {

	// 				$SubProductUID = $this->input->post('SubProductUID');

	// 				$SubProduct_check[$i] = False;

	// 				$msubproducts = array();

	// 				if($v[4] == '' && $SubProductUID == '')
	// 				{
	// 					$default_subproducts = $this->common_model->get_defaultsubproduct($CustomerUID);
	// 					$msubproductt = explode(",", $default_subproducts->DefaultProductSubValue);
	// 					if(count($msubproductt)== 1){
	// 						$msubproducts = $this->common_model->getsubproductbyUID($msubproductt[0]);
	// 						if(count($msubproducts) == 0){
	// 							$SubProduct_check[$i] = false;
	// 						}else{
	// 							$SubProductUID = $msubproducts->SubProductUID;
	// 						}
	// 					}else{
	// 						$SubProduct_check[$i] = false;
	// 					}

	// 				}elseif($v[4] !=''){

	// 					$msubproducts = $this->Orderentry_model->get_sub_product($v[4]);
	// 					if(count($msubproducts) >0){
	// 						$SubProductUID = $msubproducts->SubProductUID;
	// 						$SubProduct_check[$i] = true;
	// 					}else{
	// 						$SubProduct_check[$i] = false;
	// 					}

	// 				}elseif ($SubProductUID !='') {
	// 					$msubproducts = $this->common_model->getsubproductbyUID($SubProductUID);

	// 					if(count($msubproducts) >0){
	// 						$SubProductUID = $msubproducts->SubProductUID;
	// 						$SubProduct_check[$i] = true;
	// 					}else{
	// 						$SubProduct_check[$i] = false;
	// 					}
	// 				}


	// 				if(count($msubproducts) > 0){

	// 					$mcustomerproducts[$i] = $this->Orderentry_model->get_all_in_customerproduct($CustomerUID,$ProductUID,$SubProductUID);

	// 					if(count($mcustomerproducts[$i]) > 0){
	// 						$SubProduct_check[$i] = true;
	// 						$arrayCode[$i][0] = $mcustomerproducts[$i]->OrderTypeName;
	// 						$arrayCode[$i][1] = "Normal";
	// 						$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
	// 						$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
	// 						$arrayCode[$i][4] = $mcustomerproducts[$i]->SubProductName;
	// 					}else{
	// 						$SubProduct_check[$i] = false;
	// 						$mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
	// 						$arrayCode[$i][0] = '';
	// 						$arrayCode[$i][1] = '';
	// 						$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
	// 						$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
	// 						$arrayCode[$i][4] = $msubproducts->SubProductName;
	// 					}
	// 				}else{
	// 					$mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
	// 					$arrayCode[$i][0] = '';
	// 					$arrayCode[$i][1] = '';
	// 					$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
	// 					$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
	// 					$arrayCode[$i][4] = '';

	// 				}
	// 			}


	// 			//array_unshift($headingsArray, "Order Type", "Priority","Customer/Client","Product");



	// 			$returnvalue = False;

	// 				//$arrayCode = array_map('array_filter', $arrayCode);




	// 			foreach($arrayCode as $i => $a)
	// 			{



	// 				$count = count($a);
	// 				$field_count = 17;



	// 				//for missing fields
	// 				if(count($arrayCode[$i]) >= $field_count){

	// 					if((count($arrayCode[$i])+1) % 6 != 0) {

	// 						$error = $error + 1;

	// 					}else{

	// 						if((count($a) >= 17 )) {


	// 							$CityName = $a[8];

	// 							$CountyName = $a[9];
	// 							$StateName = $a[10];

	// 							$TemplateName = $a[14];



	// 							$template = $this->Orderentry_model->get_template($TemplateName);



	// 							if($TemplateName == ''){
	// 								$default_template = $this->common_model->get_defaulttemplate_bycustomerUID($mcustomerproducts[$i]->CustomerUID);
	// 								if(count($default_template) > 0){
	// 									$template = $default_template;
	// 								}
	// 							}



	// 							$mstates = $this->Orderentry_model->get_state($StateName);
	// 							$mcounties = [];
	// 	                        if(count($mstates) > 0){
	// 	                            $mcounties = $this->Orderentry_model->get_county($mstates->StateUID,$CountyName);
	// 	                        }

	// 							$mcities = $this->Orderentry_model->get_city($CityName,$a[11]);


	// 							if($SubProduct_check[$i] == False){
	// 								$error = $error + 1;
	// 							}else
	// 							if($a[17] == ''){

	// 								$error = $error + 1;

	// 							}elseif (count($mstates) == 0) {

	// 								$error = $error + 1;


	// 							}elseif (count($mcounties) == 0) {

	// 								$error = $error + 1;

	// 							}elseif (count($mcities) == 0) {

	// 								$error = $error + 1;


	// 							}else{

	// 								$data['OrderTypeUID'] = $mcustomerproducts[$i]->OrderTypeUID;
	// 								$data['PriorityUID'] = 2;
	// 								$data['CustomerUID'] = $mcustomerproducts[$i]->CustomerUID;
	// 								$data['SubProductUID'] = $mcustomerproducts[$i]->SubProductUID;
	// 								$data['OrderNumber'] = $this->Orderentry_model->Order_Number($mcustomerproducts[$i]->SubProductUID);
	// 								$data['LoanNumber'] = $a[5];
	// 								$data['LoanAmount'] = $a[6];
	// 								$data['PropertyAddress1'] = $a[7];
	// 								$data['PropertyZipcode'] = $a[11];
	// 								$data['APN'] = $a[12];
	// 								$data['AdditionalInfo'] = $a[13];
	// 								$data['EmailReportTo'] = $a[15];
	// 								$data['PropertyStateUID'] = $mstates->StateUID;
	// 								$data['PropertyCity'] = $mcities->CityUID;
	// 								$data['PropertyCountyUID'] = $mcounties->CountyUID;
	// 								$data['AttentionName'] = $a[16];


	// 								if(count($template) > 0){

	// 									$data['TemplateUID'] = $template->TemplateUID;
	// 								}else{
	// 									$data['TemplateUID'] = null;
	// 								}

	// 								$result = $this->Orderentry_model->savebulkentry_order($data,$a);

	// 								if($result) {


	// 									$success = $success + 1;

	// 								}else{
	// 									$error = $error + 1;
	// 								}



	// 							}
	// 						}else{

	// 							$error = $error + 1;

	// 						}
	// 					}
	// 				}
	// 			}


	// 			echo '<h3 style="margin:0"><span class="text-success modal-main-icon mdi mdi-check"></span> Success  '.$success .'</h3>';
	// 			echo '<h3 style="margin:0"><span class="text-danger modal-main-icon mdi mdi-close"></span> Error  '.$error .'</h3>';


	// 		}else{
	// 			echo json_encode(array('error'=>'1','message'=>'Please Upload Valid File'));
	// 		}
	// 	}else{
	// 		echo json_encode(array('error'=>'1','message'=>'Please upload File'));
	// 	}

	// }

	function text_preview_bulkentry()
	{
		// echo '<pre>';print_r('test');exit;
		if($this->input->post('bulk_order_details') != '') {

			$inputdata = $this->input->post('bulk_order_details');
			$returnvalue = False;
			$orders_yts = 0;
			$duplicate_order_id = 0;
			$duplicate_row = 0;
			$column_empty = 0;
			$element_empty = 0;


			$CustomerUID = $this->input->post('CustomerUID');
			$ProductUID = $this->input->post('ProductUID');
			$SubProductUID = $this->input->post('SubProductUID');


			if($CustomerUID =='' || $ProductUID ==''){
				echo json_encode(array('error'=>'1','message'=>'Select the Required Fields'));exit;
			}

			$arrayCode = array();
			$rows = explode("\n", $inputdata);
			$rows = array_filter($rows);


			foreach($rows as $idx => $row)
			{
				$row = explode( "\t", $row );

							//to get rid of first item (the number)
							//comment it if you don't need.
							//array_shift ( $row );

				foreach( $row as $field )
				{
					//to clean up $ sign

					$field = trim( $field, "$ ");

					$arrayCode[$idx][0] = '';
					$arrayCode[$idx][1] = '';
					$arrayCode[$idx][2] = '';
					$arrayCode[$idx][3] = '';
					$arrayCode[$idx][] = $field;
				}
			}



			$SubProduct_check = [];
			$FileUploadPreview = [];
			$files_upload= $this->input->post('MIME_FILES');

			foreach ($arrayCode as $i => $v) {

				$SubProductUID = $this->input->post('SubProductUID');

				$SubProduct_check[$i] = False;

				$msubproducts = array();

				if($v[4] == '' && $SubProductUID == '')
				{
					$default_subproducts = $this->common_model->get_defaultsubproduct($CustomerUID);
					$msubproductt = explode(",", $default_subproducts->DefaultProductSubValue);
					if(count($msubproductt)== 1){
						$msubproducts = $this->common_model->getsubproductbyUID($msubproductt[0]);
						if(count($msubproducts) == 0){
							$SubProduct_check[$i] = false;
						}else{
							$SubProductUID = $msubproducts->SubProductUID;
						}
					}else{
						$SubProduct_check[$i] = false;
					}

				}elseif($v[4] !=''){

					$msubproducts = $this->Orderentry_model->get_sub_product($v[4]);
					if(count($msubproducts) >0){
						$SubProductUID = $msubproducts->SubProductUID;
						$SubProduct_check[$i] = true;
					}else{
						$SubProduct_check[$i] = false;
					}

				}elseif ($SubProductUID !='') {
					$msubproducts = $this->common_model->getsubproductbyUID($SubProductUID);

					if(count($msubproducts) >0){
						$SubProductUID = $msubproducts->SubProductUID;
						$SubProduct_check[$i] = true;
					}else{
						$SubProduct_check[$i] = false;
					}
				}


				if(count($msubproducts) > 0){

					$mcustomerproducts[$i] = $this->Orderentry_model->get_all_in_customerproduct($CustomerUID,$ProductUID,$SubProductUID);

					if(count($mcustomerproducts[$i]) > 0){
						$SubProduct_check[$i] = true;
						$arrayCode[$i][0] = $mcustomerproducts[$i]->OrderTypeName;
						$arrayCode[$i][1] = "Normal";
						$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
						$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
						$arrayCode[$i][4] = $mcustomerproducts[$i]->SubProductName;
					}else{
						$SubProduct_check[$i] = false;
						$mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
						$arrayCode[$i][0] = '';
						$arrayCode[$i][1] = '';
						$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
						$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
						$arrayCode[$i][4] = $msubproducts->SubProductName;
					}
				}else{
					$mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
					$arrayCode[$i][0] = '';
					$arrayCode[$i][1] = '';
					$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
					$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
					$arrayCode[$i][4] = $v[4];

				}

				$is_available = false;
				$Loanfield_count = 6;
				foreach ($files_upload as $key => $filename) {

					$LoanNumber = $arrayCode[$i][$Loanfield_count];
					$arraycount = count($v);

					$dotposition = strripos($filename, '.');
					$documentname = substr($filename, 0, $dotposition);

					if (strpos(strtolower($documentname), strtolower($LoanNumber))!==false) {
						$is_available = true;
						$obj = new stdClass();
						$obj->LoanNumber = $LoanNumber;
						$obj->DocumentName = $filename;
						$FileUploadPreview[] = $obj;
					}
				}


			}

			$headingcount = 0;
			$counts = 0;
			foreach ($arrayCode as $key => $value) {


				if(count($value) > $counts ){
					$counts = count($value);
					$headingcount = count(array_chunk($value, 6));
				}
			}

			$tableheadcount = $headingcount - 4;
			?>
			<div class="tab-container">
				<ul class="nav nav-tabs nav-tabs-warning">
					<li class="active"><a href="#home2" data-toggle="tab">Data Preview</a></li>
					<li><a href="#profile2" data-toggle="tab">File Preview</a></li>
				</ul>

				<div class="tab-content">
					<div id="home2" class="tab-pane active cont">
						<div class="row">
							<p class="xs-mt-10 xs-mb-10 ">
								<span class="borderradius label pull-right" style="background-color: #AA00FF;">Borrower</span>
								<span class="borderradius label pull-right" style="background-color: #BDA601;">Zipcode</span>
								<span class="borderradius label pull-right" style="background-color: #074D1E;">City</span>
								<span class="borderradius label pull-right" style="background-color: #02BD5A;">State</span>
								<span class="borderradius label pull-right" style="background-color: #757575;">Empty Field</span>
							</p>
						</div>

						<div class="row">
							<div class="panel panel-default panel-table table-responsive defaultfontsize">
								<table class="table table-striped table-hover"  id="table-bulkorder">
									<thead>
										<tr>
											<th>Order Type</th>
											<th>Priority</th>
											<th>Customer/Client</th>
											<th>Product</th>
											<th>Sub Product</th>
											<th>Alternate order Number	</th>
											<th>Loan Number</th>
											<th>Loan Amount</th>
											<th>Customer Reference Number	</th>
											<th>Property Address</th>
											<th>Property City</th>
											<th>Property County</th>
											<th>Property State</th>
											<th>Property Zip Code</th>
											<th>APN</th>
											<th>Additional Info</th>
											<th>Template</th>
											<th>Email Report to</th>
											<th>Attention Name</th>
											<th>Seller</th>


											<?php for ($i=1; $i <= $tableheadcount; $i++) {  ?>
												<th>BorrowerName</th>
												<th>Email</th>
												<th>Home No</th>
												<th>Work No</th>
												<th>Cell No</th>
												<th>Social No</th>
											<?php } ?>

										</tr>
									</thead>
									<tbody>

										<?php
										foreach($arrayCode as $i => $a)
										{
											/*Assigning field for each column*/
											$count = count($a);
											$field_count = 20;
											$field_countycolumn = 11;
											$field_templatecolumn = 16;
											$Loancolumn = $a[6];
											$Citycolumn = $a[10];
											$Countycolumn = $a[11];
											$Statecolumn = $a[12];
											$Zipcolumn = $a[13];
											$Templatecolumn = $a[16];
											$Sellercolumn = $a[19];
												//for missing fields
											if(count($arrayCode[$i]) >= $field_count){

												if((count($arrayCode[$i])+4) % 6 != 0) {
													?> <tr style="background-color: #757575; color: #fff;"> <?php
													foreach ($arrayCode[$i] as $key => $value) {

														?>
														<td><?php echo $value; ?></td>
														<?php
													}

													?> </tr> <?php

												}else{



													if((count($a) >= $field_count )) {


														/*GET COUNTY BY ZIPCODE AND STATE*/
														if($Countycolumn == ''){

															$County = $this->common_model->_getCounty_StateUID_ZipCode($Zipcolumn,$Statecolumn);

															if(!empty($County)){
																$arrayCode[$i][$field_countycolumn] = $County->CountyName;
																$Countycolumn = $County->CountyName;
															}
														}



														$template = $this->Orderentry_model->get_template($Templatecolumn);

														if($Templatecolumn == ''){
															$default_template = $this->common_model->get_defaulttemplate_bycustomerUID($mcustomerproducts[$i]->CustomerUID);

															if(count($default_template) > 0){
																$arrayCode[$i][$field_templatecolumn] = $default_template->TemplateName;
																$template = $default_template;
															}
														}

														/*$mstates = $this->Orderentry_model->get_state($StateCode);

														$mcounties = [];
								                        if($StateCode > 0){
								                            $mcounties = $this->Orderentry_model->get_county($StateCode,$CountyName);
								                        }

								                        $mcities = $this->Orderentry_model->get_city($CityName,$a[11]);*/

								                        if($SubProduct_check[$i] == False){ ?>
								                        	<tr style="background-color: #BF6105; color: #fff;"> <?php
								                        	foreach ($arrayCode[$i] as $key => $value) {

								                        		?>
								                        		<td ><?php echo $value; ?></td>
								                        		<?php
								                        	} ?> </tr> <?php
								                        }else

								                        if($Sellercolumn == '' && $arrayCode[$i][$field_count] == ''){ ?>
								                        	<tr style="background-color: #AA00FF; color: #fff;"> <?php
								                        	foreach ($arrayCode[$i] as $key => $value) {

								                        		?>
								                        		<td ><?php echo $value; ?></td>
								                        		<?php
								                        	} ?> </tr> <?php
								                        }elseif ($Statecolumn == '') {
								                        	?> <tr style="background-color: #02BD5A; color: #fff;"> <?php
								                        	foreach ($arrayCode[$i] as $key => $value) {

								                        		?>
								                        		<td><?php echo $value; ?></td>
								                        		<?php
								                        	}

								                        	?> </tr> <?php


								                        }elseif ($Citycolumn == '') {
								                        	?> <tr style="background-color: #074D1E; color: #fff;"> <?php
								                        	foreach ($arrayCode[$i] as $key => $value) {

								                        		?>
								                        		<td><?php echo $value; ?></td>
								                        		<?php
								                        	}

								                        	?> </tr> <?php


								                        }elseif ($Zipcolumn == '') {
								                        	?> <tr style="background-color: #BDA601; color: #fff;"> <?php
								                        	foreach ($arrayCode[$i] as $key => $value) {

								                        		?>
								                        		<td><?php echo $value; ?></td>
								                        		<?php
								                        	}

								                        	?> </tr> <?php


								                        }else{
								                        	?> <tr style="color: #090809;"> <?php
								                        	foreach ($arrayCode[$i] as $key => $value) {

								                        		?>
								                        		<td><?php echo $value; ?></td>
								                        		<?php
								                        	}

								                        	?> </tr> <?php
								                        }




								                      }else{
								                      	?> <tr style="background-color: #757575; color: #fff;"> <?php
								                      	foreach ($arrayCode[$i] as $key => $value) {

								                      		?>
								                      		<td><?php echo $value; ?></td>
								                      		<?php
								                      	}

								                      	?> </tr> <?php
								                      }

								                    }
								                  }else{

								                  	?> <tr style="background-color: #757575; color: #fff;"> <?php
								                  	foreach ($arrayCode[$i] as $key => $value) {

								                  		?>
								                  		<td><?php echo $value; ?></td>
								                  		<?php
								                  	}

								                  	?>
								                  </tr>

								                  <?php

								                }

								              }

								              ?>
								            </tbody>

								          </table>
								        </div>
								      </div>
								    </div>

								    <div id="profile2" class="tab-pane cont">
								    	<div class="tablescroll defaultfontsize">
								    		<table class="table table-striped table-hover"  id="table-preview2">
								    			<thead>
								    				<tr>
								    					<th>SNO</th>
								    					<th>LOAN NUMBER</th>
								    					<th>DOCUMENT NAME</th>
								    				</tr>
								    			</thead>
								    			<tbody>
								    				<?php foreach ($FileUploadPreview as $key => $file) { ?>
								    					<tr>
								    						<td><?php echo $key + 1; ?></td>
								    						<td><?php echo $file->LoanNumber; ?></td>
								    						<td><?php echo $file->DocumentName; ?></td>
								    					</tr>

								    				<?php } ?>
								    			</tbody>
								    		</table>
								    	</div>
								    </div>
								  </div>
								</div>
								<?php

							}else{
								echo json_encode(array('error'=>'1','message'=>'Please Fill the Required Field'));
							}

						}



						function text_save_bulkentry()
						{
							if($this->input->post('bulk_order_details') != '') {

								$inputdata = $this->input->post('bulk_order_details');
					//$inputdata = trim($this->input->post('bulk_order_details'));
								$error = 0;
								$success = 0;


								$arrayCode = array();
								$rows = explode("\n", $inputdata);

								$rows = array_filter($rows);
								$CustomerUID = $this->input->post('CustomerUID');
								$ProductUID = $this->input->post('ProductUID');
								$SubProductUID = $this->input->post('SubProductUID');
								$ProjectUID = $this->input->post('ProjectUID');


								if($CustomerUID =='' || $ProductUID ==''){
									echo json_encode(array('error'=>'1','message'=>'Select the Required Fields'));exit;
								}


								foreach($rows as $idx => $row)
								{
									$row = explode( "\t", $row );

							//to get rid of first item (the number)
							//comment it if you don't need.
							//array_shift ( $row );

									foreach( $row as $field )
									{
							//to clean up $ sign
										$field = trim( $field, "$ ");
										$arrayCode[$idx][0] = '';
										$arrayCode[$idx][1] = '';
										$arrayCode[$idx][2] = '';
										$arrayCode[$idx][3] = '';
										$arrayCode[$idx][] = $field;
									}
								}

								$SubProduct_check = [];

								foreach ($arrayCode as $i => $v) {

									$SubProductUID = $this->input->post('SubProductUID');

									$SubProduct_check[$i] = False;

									$msubproducts = array();

									$mcustomerproducts[$i] = array();

									if($v[4] == '' && $SubProductUID == '')
									{
										$default_subproducts = $this->common_model->get_defaultsubproduct($CustomerUID);
										$msubproductt = explode(",", $default_subproducts->DefaultProductSubValue);
										if(count($msubproductt)== 1){
											$msubproducts = $this->common_model->getsubproductbyUID($msubproductt[0]);
											if(count($msubproducts) == 0){
												$SubProduct_check[$i] = false;
											}else{
												$SubProductUID = $msubproducts->SubProductUID;
											}
										}else{
											$SubProduct_check[$i] = false;
										}

									}elseif($v[4] !=''){

										$msubproducts = $this->Orderentry_model->get_sub_product($v[4]);
										if(count($msubproducts) >0){
											$SubProductUID = $msubproducts->SubProductUID;
											$SubProduct_check[$i] = true;
										}else{
											$SubProduct_check[$i] = false;
										}

									}elseif ($SubProductUID !='') {
										$msubproducts = $this->common_model->getsubproductbyUID($SubProductUID);

										if(count($msubproducts) >0){
											$SubProductUID = $msubproducts->SubProductUID;
											$SubProduct_check[$i] = true;
										}else{
											$SubProduct_check[$i] = false;
										}
									}


									if(count($msubproducts) > 0){

										$mcustomerproducts[$i] = $this->Orderentry_model->get_all_in_customerproduct($CustomerUID,$ProductUID,$SubProductUID);

										if(count($mcustomerproducts[$i]) > 0){

											$SubProduct_check[$i] = true;
											$arrayCode[$i][0] = $mcustomerproducts[$i]->OrderTypeName;
											$arrayCode[$i][1] = "Normal";
											$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
											$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
											$arrayCode[$i][4] = $mcustomerproducts[$i]->SubProductName;
										}else{
											$SubProduct_check[$i] = false;
											$mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
											$arrayCode[$i][0] = '';
											$arrayCode[$i][1] = '';
											$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
											$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
											$arrayCode[$i][4] = $msubproducts->SubProductName;
										}
									}else{
										$SubProduct_check[$i] = false;
										$mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
										$arrayCode[$i][0] = '';
										$arrayCode[$i][1] = '';
										$arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
										$arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
										$arrayCode[$i][4] = '';

									}
								}

								$FailedData=[];
								$InsertedOrderUID = '';

								$html = '';
								$html .='<div class="tab-container">';

								$html .= '<ul class="nav nav-tabs nav-tabs-success">';
								$html .= '<li class="active"><a href="#success-table" data-toggle="tab"><strong>Success&nbsp;<i class="fa fa-check-circle" style="color:green;"></i></strong></a></li>';
								$html .= '<li><a href="#error-data" data-toggle="tab"><strong>Error&nbsp;<i class="fa fa-times-circle-o" style="color:red;"></i></strong></a></li>';
								$html .= '</ul>';
								$html .= '<div class="tab-content">';
								$html .= '<div id="success-table" class="tab-pane active cont">';

								$html.='<div class="btn-group" style="margin-bottom: 20pt;">
								<button type="button" class="btn btn-success" id="pdfimport">PDF</button>
								<button type="button" id="excelimport" class="btn btn-success">Excel</button>
								</div>';
								$html .= '<div class="col-sm-12">';
								$html .= '<div class="table-responsive panel panel-default panel-table table-responsive defaultfontsize">';
								$html .= '<table class="table table-striped table-hover" id="importdata">';
								$html .= '<tr>';
								$html .= '<th>Order Number</th>';
								$html .= '<th>Order Type</th>';
								$html .= '<th>Priority</th>';
								$html .= '<th>Customer/Client</th>';
								$html .= '<th>Product</th>';
								$html .= '<th>Sub Product</th>';
								$html .= '<th>Alternate order Number</th>';
								$html .= '<th>Loan Number</th>';
								$html .= '<th>Loan Amount</th>';
								$html .= '<th>Customer Reference Number</th>';
								$html .= '<th>Property Address</th>';
								$html .= '<th>Property City</th>';
								$html .= '<th>Property County</th>';
								$html .= '<th>Property State</th>';
								$html .= '<th>Property Zip Code</th>';
								$html .= '<th>APN</th>';
								$html .= '<th>Additional Info</th>';
								$html .= '<th>Template</th>';
								$html .= '<th>Email Report to</th>';
								$html .= '<th>Attention Name</th>';
								$html .= '<th>Seller</th>';
								$html .= '<th>Borrower Name 1</th>';
								$html .= '<th>Email 1</th>';
								$html .= '<th>Home Number 1</th>';
								$html .= '<th>Work Number 1</th>';
								$html .= '<th>Cell Number 1</th>';
								$html .= '<th>Social 2</th>';
								$html .= '<th>Borrower Name 2</th>';
								$html .= '<th>Email 2</th>';
								$html .= '<th>Home Number 2</th>';
								$html .= '<th>Work Number 2</th>';
								$html .= '<th>Cell Number 2</th>';
								$html .= '<th>Social 2</th>';
								$html .= '</tr>';


								foreach($arrayCode as $i => $a)
								{
									/*Assigning field for each column*/
									$count = count($a);
									$field_count = 20;
									$field_Countycolumn = 11;
									$field_countcolumn = 20;
									$Altordernocolumn = $a[5];
									$Loancolumn = $a[6];
									$Loanamountcolumn = $a[7];
									$Customerrefnocolumn = $a[8];
									$Address1column = $a[9];
									$Citycolumn = $a[10];
									$Countycolumn = $a[11];
									$Statecolumn = $a[12];
									$Zipcolumn = $a[13];
									$Apncolumn = $a[14];
									$Additionalinfocolumn = $a[15];
									$Templatecolumn = $a[16];
									$Emailreporttocolumn = $a[17];
									$Attentioncolumn = $a[18];
									$Sellercolumn = $a[19];
									$Firstborrowernamecolumn = $a[20];

									//for missing fields
									if(count($arrayCode[$i]) >= $field_count){

										if((count($arrayCode[$i])+4) % 6 != 0) {

											$error = $error + 1;

										}else{

											if((count($a) >= $field_count )) {


												/*GET COUNTY BY ZIPCODE AND STATE*/
												if($Countycolumn == ''){

													$County = $this->common_model->_getCounty_StateUID_ZipCode($Zipcolumn,$Statecolumn);

													if(!empty($County)){
														$arrayCode[$i][$field_countycolumn] = $County->CountyName;
														$Countycolumn = $County->CountyName;
													}
												}


												$template = $this->Orderentry_model->get_template($Templatecolumn);



												if($Templatecolumn == ''){
													$default_template = $this->common_model->get_defaulttemplate_bycustomerUID($mcustomerproducts[$i]->CustomerUID);
													if(count($default_template) > 0){
														$template = $default_template;
													}
												}


												if($SubProduct_check[$i] == False){

													$error = $error +1;
													array_push($FailedData, $arrayCode[$i]);

												}elseif ($Sellercolumn == '' && $Firstborrowernamecolumn == ''){

													$error = $error + 1;
													array_push($FailedData, $arrayCode[$i]);

												}elseif ($Statecolumn == '') {

													$error = $error + 1;
													array_push($FailedData, $arrayCode[$i]);


												}elseif ($Citycolumn == '') {

													$error = $error + 1;
													array_push($FailedData, $arrayCode[$i]);


												}elseif ($Zipcolumn == '') {

													$error = $error + 1;
													array_push($FailedData, $arrayCode[$i]);


												}else{

													$data['OrderTypeUID'] = $mcustomerproducts[$i]->OrderTypeUID;
													$data['PriorityUID'] = 2;
													$data['CustomerUID'] = $mcustomerproducts[$i]->CustomerUID;
													$data['SubProductUID'] = $mcustomerproducts[$i]->SubProductUID;
													$data['OrderNumber'] = '';
													$data['AltORderNumber'] = $Altordernocolumn;
													$data['LoanNumber'] = $Loancolumn;
													$data['LoanAmount'] = $Loanamountcolumn;
													$data['CustomerRefNum'] = $Customerrefnocolumn;
													$data['PropertyAddress1'] = $Address1column;
													$data['PropertyZipcode'] = $Zipcolumn;
													$data['APN'] = $Apncolumn;
													$data['AdditionalInfo'] = $Additionalinfocolumn;
													$data['EmailReportTo'] = $Emailreporttocolumn;
													$data['PropertyStateCode'] = $Statecolumn;
													$data['PropertyCityName'] = $Citycolumn;
													$data['PropertyCountyName'] = $Countycolumn;
													$data['AttentionName'] = $Attentioncolumn;


													if(count($template) > 0){

														$data['TemplateUID'] = $template->TemplateUID;
													}else{
														$data['TemplateUID'] = null;
													}

							/*********
							If EmailReportTo fields is empty then CustomerPContactEmailID will get updated
							**********/

							if($Emailreporttocolumn == '')
							{
								$data['EmailReportTo'] = $mcustomerproducts[$i]->CustomerPContactEmailID;
							}
							else
							{
								$data['EmailReportTo'] = $Emailreporttocolumn;
							}

							$ProjectUID = $this->input->post('ProjectUID');
							$data['ProjectUID'] = $ProjectUID;

							/**************/

							$files_upload = $_FILES['MIME_FILES'];
							$result = $this->Orderentry_model->savebulkentry_order($data,$a,$files_upload);
							$InsertedOrderUID .= $result.',';

							if($result) {

								$success = $success + 1;
								$results=$this->Orderentry_model->SelectImportedData($result);
								if($results)
								{
									foreach ($results as $key=> $value)
									{
										$data = $this->Orderentry_model->GetPropertyrolesBorrowerDetails($value->OrderUID);
										$Seller = $this->Orderentry_model->GetPropertyrolesSellerDetails($value->OrderUID);
										$SellerName1 = '';
										if(!empty($Seller)){
											$SellerName1 = $Seller[0]['PRName'];
										}
										$PRName1 = $data[0]['PRName'];
										$PREmailID1 = $data[0]['PREmailID'];
										$PRHomeNumber1 = $data[0]['PRHomeNumber'];
										$PRWorkNumber1 = $data[0]['PRWorkNumber'];
										$PRCellNumber1 = $data[0]['PRCellNumber'];
										$PRSocialNumber1 = $data[0]['PRSocialNumber'];
										$PRName2 = $data[1]['PRName'];
										$PREmailID2 = $data[1]['PREmailID'];
										$PRHomeNumber2 = $data[1]['PRHomeNumber'];
										$PRWorkNumber2 = $data[1]['PRWorkNumber'];
										$PRCellNumber2 = $data[1]['PRCellNumber'];
										$PRSocialNumber2 = $data[1]['PRSocialNumber'];

										$html.= '<tr>';
										$html.= '<td>'.$value->OrderNumber.'</td>';
										$html.= '<td>'.$value->OrderTypeName.'</td>';
										$html.= '<td>'.$value->PriorityName.'</td>';
										$html.= '<td>'.$value->CustomerName.'</td>';
										$html.= '<td>'.$value->ProductName.'</td>';
										$html.= '<td>'.$value->SubProductName.'</td>';
										$html.= '<td>'.$value->AltORderNumber.'</td>';
										$html.= '<td>'.$value->LoanNumber.'</td>';
										$html.= '<td>'.$value->LoanAmount.'</td>';
										$html.= '<td>'.$value->CustomerRefNum.'</td>';
										$html.= '<td>'.$value->PropertyAddress1.','.$value->PropertyAddress2.'</td>';
										$html.= '<td>'.$value->PropertyCityName.'</td>';
										$html.= '<td>'.$value->PropertyCountyName.'</td>';
										$html.= '<td>'.$value->PropertyStateCode.'</td>';
										$html.= '<td>'.$value->PropertyZipcode.'</td>';
										$html.= '<td>'.$value->APN.'</td>';
										$html.= '<td></td>';
										$html.= '<td>'.$value->TemplateName.'</td>';
										$html.= '<td>'.$value->EmailReportTo.'</td>';
										$html.= '<td>'.$value->AttentionName.'</td>';
										$html.= '<td>'.$SellerName1.'</td>';
										$html.= '<td>'.$PRName1.'</td>';
										$html.= '<td>'.$PREmailID1.'</td>';
										$html.= '<td>'.$PRHomeNumber1.'</td>';
										$html.= '<td>'.$PRWorkNumber1.'</td>';
										$html.= '<td>'.$PRCellNumber1.'</td>';
										$html.= '<td>'.$PRSocialNumber1.'</td>';
										$html.= '<td>'.$PRName2.'</td>';
										$html.= '<td>'.$PREmailID2.'</td>';
										$html.= '<td>'.$PRHomeNumber2.'</td>';
										$html.= '<td>'.$PRWorkNumber2.'</td>';
										$html.= '<td>'.$PRCellNumber2.'</td>';
										$html.= '<td>'.$PRSocialNumber2.'</td>';
										$html.= '</tr>';
									}
								}

							}else{
								$error = $error + 1;
							}



						}
					}else{

						$error = $error + 1;

					}
				}
			}
		}

		/*echo '<h3 style="margin:0"><span class="text-success modal-main-icon mdi mdi-check"></span> Success  '.$success .'</h3>';
		echo '<h3 style="margin:0"><span class="text-danger modal-main-icon mdi mdi-close"></span> Error  '.$error .'</h3>';*/

		$html .= '</table></div></div></div>';
				// echo $html;

		$html .= '<div id="error-data" class="tab-pane cont">';
		$html.='<div class="btn-group" style="margin-bottom: 20pt;">
		<button type="button" class="btn btn-success" id="pdferror">PDF</button>
		<button type="button" id="excelerror" class="btn btn-success">Excel</button>
		</div>';
		$html .= '<div class="col-sm-12">';
		$html .= '<div class="table-responsive panel panel-default panel-table table-responsive defaultfontsize">';
		$html .= '<table class="table table-striped table-hover">';
		$html .= '<tr>';
		$html .= '<th>Order Number</th>';
		$html .= '<th>Order Type</th>';
		$html .= '<th>Priority</th>';
		$html .= '<th>Customer/Client</th>';
		$html .= '<th>Product</th>';
		$html .= '<th>Sub Product</th>';
		$html .= '<th>Alternate order Number</th>';
		$html .= '<th>Loan Number</th>';
		$html .= '<th>Loan Amount</th>';
		$html .= '<th>Customer Reference Number</th>';
		$html .= '<th>Property Address</th>';
		$html .= '<th>Property City</th>';
		$html .= '<th>Property County</th>';
		$html .= '<th>Property State</th>';
		$html .= '<th>Property Zip Code</th>';
		$html .= '<th>APN</th>';
		$html .= '<th>Additional Info</th>';
		$html .= '<th>Template</th>';
		$html .= '<th>Email Report to</th>';
		$html .= '<th>Attention Name</th>';
		$html .= '<th>Seller</th>';
		$html .= '<th>Borrower Name 1</th>';
		$html .= '<th>Email 1</th>';
		$html .= '<th>Home Number 1</th>';
		$html .= '<th>Work Number 1</th>';
		$html .= '<th>Cell Number 1</th>';
		$html .= '<th>Social 2</th>';
		$html .= '<th>Borrower Name 2</th>';
		$html .= '<th>Email 2</th>';
		$html .= '<th>Home Number 2</th>';
		$html .= '<th>Work Number 2</th>';
		$html .= '<th>Cell Number 2</th>';
		$html .= '<th>Social 2</th>';
		$html .= '</tr>';
		// print_r($FailedData);
		foreach ($FailedData as $key => $value)
		{
			$html.= '<tr>';
			$html.= '<td>'.$value[0].'</td>';
			$html.= '<td>'.$value[1].'</td>';
			$html.= '<td>'.$value[2].'</td>';
			$html.= '<td>'.$value[3].'</td>';
			$html.= '<td>'.$value[4].'</td>';
			$html.= '<td>'.$value[5].'</td>';
			$html.= '<td>'.$value[6].'</td>';
			$html.= '<td>'.$value[7].'</td>';
			$html.= '<td>'.$value[8].'</td>';
			$html.= '<td>'.$value[9].'</td>';
			$html.= '<td>'.$value[10].'</td>';
			$html.= '<td>'.$value[11].'</td>';
			$html.= '<td>'.$value[12].'</td>';
			$html.= '<td>'.$value[13].'</td>';
			$html.= '<td>'.$value[14].'</td>';
			$html.= '<td>'.$value[15].'</td>';
			$html.= '<td>'.$value[16].'</td>';
			$html.= '<td>'.$value[17].'</td>';
			$html.= '<td>'.$value[18].'</td>';
			$html.= '<td>'.$value[19].'</td>';
			$html.= '<td>'.$value[20].'</td>';
			$html.= '<td>'.$value[21].'</td>';
			$html.= '<td>'.$value[22].'</td>';
			$html.= '<td>'.$value[23].'</td>';
			$html.= '<td>'.$value[24].'</td>';
			$html.= '<td>'.$value[25].'</td>';
			$html.= '<td>'.$value[26].'</td>';
			$html.= '<td>'.$value[27].'</td>';
			$html.= '<td>'.$value[28].'</td>';
			$html.= '<td>'.$value[29].'</td>';
			$html.= '<td>'.$value[30].'</td>';
			$html.= '<td>'.$value[31].'</td>';
			$html.= '</tr>';
		}
		$html .= '</table>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<input type="hidden" value="'.$InsertedOrderUID.'" name="InsertedOrderUID[]" id="InsertedOrderUID">';
		echo $html;




	}else{
		echo json_encode(array('error'=>'1','message'=>'Please Fill the Required Field'));
	}
}


	function isEmptyRow($row) {
		foreach($row as $cell){
			if (null !== $cell) return false;
		}
		return true;
	}


}
