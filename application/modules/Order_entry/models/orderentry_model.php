<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class Orderentry_model extends CI_Model {
	function __construct()
	{
		parent::__construct();
		$this->config->load('keywords');
	}
// NF223 - Quick Order Entry
	function GetOrderEntryCopyDetailsbyUID($orderUID) {
		$this->db->select('tOrders.*, tOrders.PropertyCityName as CityName, tOrders.PropertyCountyName as CountyName, tOrders.PropertyStateCode as StateName, tOrders.PropertyStateCode as StateCode, mordertypes.OrderTypeName, mSubProducts.SubProductName,mProducts.ProductUID')->from('tOrders');
		$this->db->join('mSubProducts','mSubProducts.SubProductUID=tOrders.SubProductUID');
		$this->db->join('mProducts','mProducts.ProductUID = mSubProducts.ProductUID','left');
		$this->db->join('mOrderTypes','mordertypes.OrderTypeUID=tOrders.OrderTypeUID', 'left');
		$this->db->where('tOrders.OrderUID',$orderUID);
		$query = $this->db->get()->row();
		return $query;
	}
	

	function GetPropertyRoleEntryCopyDetailsbyUID($orderUID) {
		$this->db->select('*')->from('tOrderPropertyRoles');
		$this->db->where('OrderUID',$orderUID);
		$query = $this->db->get()->result();
		return $query;
	}

	function GetStateDetails($StateUID){

		$this->db->select("StateCode");
		$this->db->from('mStates');
		$this->db->where(array("Active"=>1,"mStates.StateUID"=>$StateUID));
		$query = $this->db->get();
		return $query->row();

	}

	function GetCityDetails($CityUID){

		$this->db->select("CityName");
		$this->db->from('mCities');
		$this->db->where(array("Active"=>1,"mCities.CityUID"=>$CityUID));
		$query = $this->db->get();
		return $query->row();

	}

	function GetCountyDetails($CountyUID){

		$this->db->select("CountyName");
		$this->db->from('mCounties');
		$this->db->where(array("Active"=>1,"mCounties.CountyUID"=>$CountyUID));
		$query = $this->db->get();
		return $query->row();

	}
	function GetPriorityDetails()
	{
		$this->db->where(array("Active"=>1));
		$query = $this->db->get('morderpriority');
		return $query->result_array();
	}

	function GetSubProductOrderType($SubProductUID)
	{
		$this->db->where('mSubProducts.SubProductUID',$SubProductUID);
		$this->db->select('mSubProducts.OrderTypeUID, mordertypes.OrderTypeName')->from('mSubProducts');
		$this->db->join('mordertypes','mordertypes.OrderTypeUID = mSubProducts.OrderTypeUID','LEFT');
		return $this->db->get()->row();
	}

	function get_customer_subproduct_template($where)
	{
		$this->db->select('*')->from('mcustomertemplates');
		$this->db->where($where);
		$this->db->join('mtemplates','mcustomertemplates.TemplateUID = mtemplates.TemplateUID');
		return $this->db->get()->row();
	}

	function get_customer_default_template($CustomerUID)
	{
		$this->db->select('DefaultTemplateUID, TemplateName, TemplateUID')->from('mCustomers');
		$this->db->where('CustomerUID',$CustomerUID);
		$this->db->join('mtemplates','mtemplates.TemplateUID = mCustomers.DefaultTemplateUID');
		return $this->db->get()->row();
	}

	function GetSub_productDetails($ProductUID, $CustomerUID)
	{
		$this->db->select('mSubProducts.SubProductUID, mSubProducts.SubProductName,mSubProducts.RMS')->from('mSubProducts');
		$this->db->join('mCustomerProducts','mSubProducts.SubProductUID = mCustomerProducts.SubProductUID','LEFT');
		$this->db->where('mCustomerProducts.CustomerUID',$CustomerUID);
		$this->db->where('mCustomerProducts.ProductUID',$ProductUID);
		$q = $this->db->get();
		return $q->result();
	}

	function insert_order($data,$DatabaseAddress){
		// add slashes for special char 
		// foreach ($data as $key => $value) {
		// 	$data[$key] = addslashes($data[$key]);
		// }
		$PreviousOrderUID = (isset($data['PreviousOrderUID']))?$data['PreviousOrderUID']:NULL;

		//D-2-T23 state issuer fetched
		$data['Issuer'] = $this->lang->line('Inhouse_addtable');
		// $searchmoderesult = $this->common_model->get_statesearchmode($data['PropertyStateCode'],$data['PropertyCountyName']);
		// if(!empty($searchmoderesult)) {
		// 	$data['Issuer'] = (!empty($searchmoderesult->SearchModeName)) ? $searchmoderesult->SearchModeName : $this->lang->line('Inhouse_addtable');
		// }
		$date = date('Ymd');

		$IsDuplicateOrder = ($DatabaseAddress == NUll) ? 0 : 1 ;
		$this->IsTaxcert=isset($data['IsTaxcert'])? 1:0;

		$this->AltORderNumber = $data['AltORderNumber'];
		$this->LoanNumber = $data['LoanNumber'];
		$this->LoanAmount = $data['LoanAmount'];
		$this->CashOutAmount = $data['CashOutAmount'];
		$this->CustomerRefNum = $data['CustomerRefNum'];
		$this->CustomerUID = $data['customer'];
		$this->PropertyAddress1 = $data['PropertyAddress1'];
		$this->PropertyAddress2 = $data['PropertyAddress2'];
		$this->PropertyCityName = $data['PropertyCityName'];
		$this->PropertyStateCode = $data['PropertyStateCode'];
		$this->PropertyCountyName = $data['PropertyCountyName'];
		$this->PropertyZipcode = $data['PropertyZipcode'];
		$this->EmailReportTo = $data['EmailReportTo'];
		$this->AttentionName = $data['AttentionName'];
		$this->SpecialInstruction = $data['SpecialInstruction'];
		$this->AddressNotes = $data['AddressNotes'];
		$this->APN = $data['APN'];
		$this->OwnerOccupancy = $data['OwnerOccupancy'];
		$this->IsDuplicateOrder = $IsDuplicateOrder;
		$this->StatusUID = $this->config->item('keywords')['New Order'];
		$this->OrderEntryDatetime = Date('Y-m-d H:i:s',strtotime("now"));
		$this->OrderCreatedByUserUID = $this->session->userdata('UserUID');

		if (isset($data['DocumentStatus'])) 
		{
			$this->DocumentStatus = $data['DocumentStatus'];
		}

		$pricing = new Customer_pricing();


		$mStates=$this->db->get_where('mStates', array('StateCode' => $this->PropertyStateCode))->row();

		$mCounties=$this->db->get_where('mCounties', array('StateUID'=>$mStates->StateUID, 'CountyName'=>$this->PropertyCountyName))->row();

		$OrderNos = [];
		//OrderNumber Change -Product Indexs @Auth Uba @On may 18 2020 
		if(!empty($PreviousOrderUID)){
			$last_row_seq = $this->db->select('OrderNumber,OrderSequence,OrderUID')->where('OrderUID',$PreviousOrderUID)->get('tOrders')->row();
			$LastNo = explode('-',$last_row_seq->OrderNumber);
			$last_row_sequence = $this->db->select('OrderNumber,OrderSequence,OrderUID')->like('OrderNumber', substr($LastNo[0],3), 'both')->order_by('OrderUID',"DESC")->limit(1)->get('tOrders')->row();
			
			$ANo = explode('-',$last_row_sequence->OrderNumber);
			$PreviousIncrement = $ANo[1];
			$OrderNo = substr($ANo[0],1);
			$OrderSequence = substr($ANo[0],1);
			// Order Year based issue fixed @Uba @On Jul 30 2020
			$OrderNo = date("y").substr($OrderNo,2);
			$OrderSequence = date("y").substr($OrderSequence,2);
			
		}else{
			$PreviousIncrement = 0;
			$OrderNo = $this->Order_Number(NULL,$PreviousOrderUID); 
			$OrderNo = substr($OrderNo,1);
			$OrderSequence = $this->OrderSequence(NULL,$PreviousOrderUID);
		}

		foreach ($data['ProductUID'] as $key => $value) {
			$ProductIndex = $key+1+$PreviousIncrement;
			if(!empty($data['ProjectUID'][$key])) {
				$mDocTypes = $this->db->get_where('mProjectDocType',array('ProjectUID'=>$data['ProjectUID'][$key]))->result();
			}
			$this->db->trans_begin();
			$this->SubProductUID = $data['SubProductUID'][$key];
			$this->PriorityUID = $data['PriorityUID'][$key];
			$this->ProjectUID = isset($data['ProjectUID'][$key]) ? $data['ProjectUID'][$key] : '0';
			//$this->IsMERS = isset($data['MERS'][$key]) ? 1 : 0;

			if($ProductIndex > 1){
				$this->IsChildOrder = 1;
			}

			$this->db->select('ProductCode');
			$this->db->from('mSubProducts');
			$this->db->join('mProducts','mProducts.ProductUID = mSubProducts.ProductUID');
			$this->db->where('SubProductUID', $data['SubProductUID'][$key]);
			$query = $this->db->get();
			$result = $query->row();

			$code = strtoupper($result->ProductCode);
			
			$this->OrderNumber = $code.$OrderNo.'-'.$ProductIndex;
			/* NF216 - Duplicate Order number restriction */
			
			$this->OrderSequence = $OrderSequence.'-'.$ProductIndex;
			
			$mSubProducts = $this->common_model->get_row('mSubProducts', ['SubProductUID'=>$this->SubProductUID]);
			$IsClosingProduct = $this->Orderentry_model->IsClosingProduct($mSubProducts->ProductUID);

			$this->OrderTypeUID = $this->GetSubProductOrderType($this->SubProductUID)->OrderTypeUID;
			$where['SubProductUID'] = $this->SubProductUID;
			$where['CustomerUID'] = $this->CustomerUID;
			$this->TemplateUID = $this->get_customer_subproduct_template($where)->TemplateUID;
			/*@Desc Customer Pricing Update D2T-540 @Author Jainulabdeen @Updated May 20 2020*/
			$CustomerAmountQuote = $pricing->get_Customer_Pricings_Quote($this);
			$CustomerAmount = $CustomerAmountQuote->Pricing;
			$CustomerQuote = $CustomerAmountQuote->IsQuote;
			/*End*/
			if (empty($IsClosingProduct)) {
				$this->CustomerAmount = $CustomerAmount;
				$this->CustomerActualAmount = $CustomerAmount;
				
			}

			//RMS based on subproduct - pricing 
			if(!empty($mSubProducts)) {
				if($mSubProducts->RMS == 1){
					$CustomerAmount = $pricing->get_Customer_ReverseMortgagePricing($this->CustomerUID,$this->SubProductUID);
					$this->CustomerAmount = $CustomerAmount;
					$this->CustomerActualAmount = $CustomerAmount;					
				}
			}


			if ($mStates->SearchType==1) {
				$this->IsInhouseExternal=0;
			}
			elseif ($mStates->SearchType==2) {
			 	$this->IsInhouseExternal=1;
		 	}
		 	else{

		 		if ($mStates->AbstractorAssignment==1) {
					$this->IsInhouseExternal = $this->common_model->get_inhouse_external($this->OrderTypeUID,$mStates->StateUID,$mCounties->CountyUID);
		 		}
		 		else{
		 			$this->IsInhouseExternal = $this->common_model->get_inhouse_external_forzipcode($this->OrderTypeUID,$mStates->StateUID,$data['PropertyZipcode']);
		 		}
		 	}

			$CheckOrderType = substr($OrderNo, 0,1);
			if($CheckOrderType == 'F'){
				$this->IsInhouseExternal = 0;
			}

			// $this->OrderDueDateTime = calculate_duedate($this->OrderEntryDatetime,$this->CustomerUID,$this->SubProductUID,$this->PriorityUID);
			// $this->OriginalOrderDueDateTime = $this->OrderDueDateTime;
			$this->OrderDocsPath ='uploads/searchdocs/'.$date.'/'.$OrderNo.'/';


			if (!empty($IsClosingProduct)) {
				$this->BranchUID = $data['BranchUID'];
			}


			// $x1CustomerCheck = $this->CheckX1Order($this->CustomerUID,$mSubProducts->ProductUID,$this->SubProductUID);
			// if($x1CustomerCheck)
			// {
			// 	$this->BorrowerType = $data['BorrowerType'];
			// 	$this->PropertyType = $data['PropertyType'];
			// 	$this->TransactionType = $data['TransactionType'];
			// }

			$this->IsQuote = $CustomerQuote; /*@Desc Customer Pricing Update D2T-540 @Author Jainulabdeen @Updated May 20 2020*/

			/* desc: Insert Order Policy Type 
			** author: Yagavi G <yagavi.g@avanzegroup.com>
			** since: Aug 7th 2020
			*/
			$where_policy_type['SubProductUID'] = $this->SubProductUID;
			$where_policy_type['CustomerUID'] = $this->CustomerUID;
			$this->OrderPolicyType = $this->common_model->GetCustomerProductDetailsByCustomerSubproductUID($where_policy_type)->OrderPolicyType;

			$query = $this->db->insert('tOrders',$this);
			$insert_id = $this->db->insert_id();	

			if (!empty($IsClosingProduct))
			{
				$this->common_model->insertWorkflowQueues($insert_id, $this->CustomerUID, $this->SubProductUID, "Order import", 0, false);
			}

			/*INSERT CUSTOMER ACTUAL PRICING*/
			if (empty($IsClosingProduct)) {
				$this->common_model->insert_customerpaymentdata($insert_id);				
			}

			if (!empty($IsClosingProduct)) {

				if (!empty($data['SigningDate']) && !empty($data['SigningTime'])) {

					$SigningDateTime = DateTime::createFromFormat("m/d/Y" , $data['SigningDate']);
					$SigningDate = $SigningDateTime->format('Y-m-d');
					$seconds =':00';
					$SigningTime = date("H:i", strtotime($data['SigningTime']));
					$SigningDateTime = date('Y-m-d H:i:s', strtotime($SigningDate . ' ' .$SigningTime.$seconds));

					$tOrderClosingTemp['OrderUID'] = $insert_id;
					$tOrderClosingTemp['SigningDateTime'] = $SigningDateTime;

					$this->common_model->save('tOrderClosingTemp', $tOrderClosingTemp);
				}
			}

			$this->db->select('OrderUID');
			$this->db->from('tOrders');
			$this->db->where('OrderNumber',$this->OrderNumber);
			$newvalue=$this->db->get('')->row_array();

			$data1['ModuleName']='orderentry-insert';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR'];
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='tOrders';
			$data1['OrderUID'] = $newvalue['OrderUID'];
			$data1['UserUID']=$this->session->userdata('UserUID');
			$this->common_model->Audittrail_insert($data1);

			$property_role = $this->saveProperty_Details($data,$insert_id);

			/*Inserting mDocType in tOrderDocType*/
			$insertdocdata = [];
			if(!empty($mDocTypes)){
				foreach ($mDocTypes as $key => $mDocType) {
					$insertdocdata[$key]['OrderUID'] = $insert_id;
					$insertdocdata[$key]['DocTypeUID'] = $mDocType->DocTypeUID;
				}
			}
			if(!empty($insertdocdata)){
				$this->db->insert_batch('tOrderDocType', $insertdocdata);
			}

			/*Insert Order Import*/
			$this->Insert_tOrderImport($data,$insert_id);

			/* D-2-T9 GENERATE PACKAGE NUMBER BASED ON LOAN NUMBER*/
			$this->common_model->save_package_number($insert_id,$this->LoanNumber);

			/*-----Generate Map Start---*/
			$this->generate_map($insert_id);
			/*-----Generate Map End---*/

			/*----- Add Auto Create Task on Order Open Start---*/
			$this->common_model->AutoCreateTask_WorkflowLevel($insert_id, NULL, $this->config->item('OrderLevelStatus')['Order Open']);
			/*----- Add Auto Create Task on Order Open End---*/

			/**
			* @author Alwin L
			* @purpose check and x1 orders to api
			**/

			/* Start - Placing Order to X1 */			
			$X1SearchMode = $this->GetPreferedSites($insert_id) ;
			if(!empty($X1SearchMode) && $x1CustomerCheck)
			{
				$this->load->model('x1/X1_model');

				/**
				* @purpose: D2TINT-279 - Implementing X1 Test Cases Default Data
				* @author : D.Samuel Prabhu
				* @since  : 03 Sep 2020
				*/
				/* If it is production server then send to x1 */ 
				if($this->X1_model->isProductionServer()) 
				{
					$result = $this->X1_model->PlaceOrder_X1($insert_id,'single');					
				}
				else
				{ /* If it is not production server then send test data to x1 */
					$result = $this->X1_model->PlaceX1TestOrder($insert_id,'single');
				}
			}
			/* End - Placing Order to X1 */

			//insert sla action details for single entry start
			$this->common_model->insert_slaaction($insert_id,$this->config->item('SLAaction')['Order Placed'],$this->loggedid);
			//insert sla action details end

			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
			}else{
				$this->db->trans_commit();
				$OrderNos[] = $this->OrderNumber;
				$OrderUIDs[] = $insert_id;
			}

		}

		$OrderNumbers =  implode(", ",$OrderNos);
		$Msg = $this->lang->line('Order_Save');
		$Rep_msg=str_replace("<<Order Number>>", $OrderNumbers , $Msg);

	    /**
	    * Function altered to add notes
	    * @author : D.Samuel Prabhu
	    * @since  : 10 Feb 2019
	    */ 
        if($OrderNumbers)
        {
        	$Notes ="Order ".$OrderNumbers." created on ".date('m-d-Y H:i:s');
            $SectionUID = $this->common_model->GetNoteTypeUID("Order Entry Info")->SectionUID;
			$this->common_model->insertordernotes($newvalue['OrderUID'], $SectionUID, $Notes);
        }

        /**
	    * Redirect to the order in the success of the order - Single Order Entry
	    * @author : Yagavi G <yagavi.g@avanzegroup.com>
	    * @since  : 24 Feb 2020
	    */ 
	    /* Start - Redirect to the order in the success of the order - Single Order Entry*/
        $OrderCount = count($OrderUIDs);
        if($OrderCount == 1){
        	$order_id = $OrderUIDs[0];
        } else {
        	$order_id = '';
        }
        /* End - Redirect to the order in the success of the order - Single Order Entry*/

        $OrderResponse = array('OrderUID' => $order_id, 'Rep_msg' => $Rep_msg, 'OrderNumber' => $OrderNumbers);
		return $OrderResponse;
	}

	// Import Order via Email - Order Insert Model
	function email_insert_order($data,$DatabaseAddress){
		//D-2-T23 state issuer fetched
		$data['Issuer'] = $this->lang->line('Inhouse_addtable');
		$searchmoderesult = $this->common_model->get_statesearchmode($data['PropertyStateCode'],$data['PropertyCountyName']);
		if(!empty($searchmoderesult)) {
			$data['Issuer'] = (!empty($searchmoderesult->SearchModeName)) ? $searchmoderesult->SearchModeName : $this->lang->line('Inhouse_addtable');
		}
		$date = date('Ymd');
		$insert_id = "";

		$IsDuplicateOrder = ($DatabaseAddress == NUll) ? 0 : 1 ;
		$this->IsTaxcert=isset($data['IsTaxcert'])? 1:0;

		$this->AltORderNumber = $data['AltORderNumber'];
		$this->LoanNumber = $data['LoanNumber'];
		$this->LoanAmount = $data['LoanAmount'];
		$this->CashOutAmount = $data['CashOutAmount'];
		$this->CustomerRefNum = $data['CustomerRefNum'];
		$this->CustomerUID = $data['customer'];
		$this->PropertyAddress1 = $data['PropertyAddress1'];
		$this->PropertyAddress2 = $data['PropertyAddress2'];
		$this->PropertyCityName = $data['PropertyCityName'];
		$this->PropertyStateCode = $data['PropertyStateCode'];
		$this->PropertyCountyName = $data['PropertyCountyName'];
		$this->PropertyZipcode = $data['PropertyZipcode'];
		$this->EmailReportTo = $data['EmailReportTo'];
		$this->AttentionName = $data['AttentionName'];
		$this->APN = $data['APN'];
		$this->IsDuplicateOrder = $IsDuplicateOrder;
		$this->StatusUID = $this->config->item('keywords')['New Order'];;
		$this->OrderEntryDatetime = Date('Y-m-d H:i:s',strtotime("now"));
		$pricing = new Customer_pricing();



		if (isset($data['DocumentStatus'])) 
		{
			$this->DocumentStatus = $data['DocumentStatus'];
		}

		if (isset($data['FHA'])) 
		{
			$this->FHA = $data['FHA'];
		}

		$mStates=$this->db->get_where('mStates', array('StateCode' => $this->PropertyStateCode))->row();

		$mCounties=$this->db->get_where('mCounties', array('StateUID'=>$mStates->StateUID, 'CountyName'=>$this->PropertyCountyName))->row();


		$OrderNos = [];
		foreach ($data['ProductUID'] as $key => $value) {
		$this->db->trans_begin();
			$this->SubProductUID = $data['SubProductUID'][$key];
			$this->PriorityUID = $data['PriorityUID'][$key];
			$this->ProjectUID = isset($data['ProjectUID'][$key]) ? $data['ProjectUID'][$key] : '0';
			//$this->IsMERS = isset($data['MERS'][$key]) ? 1 : 0;
			$OrderNo = $this->Order_Number($this->SubProductUID);
			$this->OrderNumber = $OrderNo;
			/* NF216 - Duplicate Order number restriction */
			$OrderSequence = $this->OrderSequence($this->SubProductUID);
			$this->OrderSequence = $OrderSequence;


			$this->OrderTypeUID = $this->GetSubProductOrderType($this->SubProductUID)->OrderTypeUID;
			$where['SubProductUID'] = $this->SubProductUID;
			$where['CustomerUID'] = $this->CustomerUID;
			$this->TemplateUID = $this->get_customer_subproduct_template($where)->TemplateUID;
			// $CustomerAmount = $pricing->get_Customer_Pricings($this);
			// $this->CustomerAmount = $CustomerAmount;
			// $this->CustomerActualAmount = $CustomerAmount;
			/*@Desc Customer Pricing Update D2T-540 @Author Jainulabdeen @Updated May 21 2020*/
			$CustomerAmountQuote = $pricing->get_Customer_Pricings_Quote($this);
			$this->CustomerAmount = $CustomerAmountQuote->Pricing;
			$this->CustomerActualAmount = $this->CustomerAmount;
			$this->IsQuote = $CustomerAmountQuote->IsQuote;
			/*End*/
			if ($mStates->SearchType==1) {
				$this->IsInhouseExternal=0;
			}
			elseif ($mStates->SearchType==2) {
			 	$this->IsInhouseExternal=1;
		 	}
		 	else{

		 		if ($mStates->AbstractorAssignment==1) {
					$this->IsInhouseExternal = $this->common_model->get_inhouse_external($this->OrderTypeUID,$mStates->StateUID,$mCounties->CountyUID);
		 		}
		 		else{
		 			$this->IsInhouseExternal = $this->common_model->get_inhouse_external_forzipcode($this->OrderTypeUID,$mStates->StateUID,$data['PropertyZipcode']);
		 		}
		 	}

			$CheckOrderType = substr($OrderNo, 0,1);
			if($CheckOrderType == 'F'){
				$this->IsInhouseExternal = 0;
			}

			$this->OrderDueDateTime = calculate_duedate($this->OrderEntryDatetime,$this->CustomerUID,$this->SubProductUID,$this->PriorityUID);
			$this->OrderDocsPath ='uploads/searchdocs/'.$date.'/'.$OrderNo.'/';

			/* desc: Insert Order Policy Type 
			** author: Yagavi G <yagavi.g@avanzegroup.com>
			** since: Aug 7th 2020
			*/
			$where_policy_type['SubProductUID'] = $this->SubProductUID;
			$where_policy_type['CustomerUID'] = $this->CustomerUID;
			$this->OrderPolicyType = $this->common_model->GetCustomerProductDetailsByCustomerSubproductUID($where_policy_type)->OrderPolicyType;

			$query = $this->db->insert('tOrders',$this);
			$insert_id = $this->db->insert_id();

			/*INSERT CUSTOMER ACTUAL PRICING*/
			$this->common_model->insert_customerpaymentdata($insert_id);

			/*Insert Order Import*/
			$this->Insert_tOrderImport($data,$insert_id);

			$this->db->select('OrderUID');
			$this->db->from('tOrders');
			$this->db->where('OrderNumber',$this->OrderNumber);
			$newvalue=$this->db->get('')->row_array();


			$data1['ModuleName']='email-orderentry-insert';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR'];
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='tOrders';
			$data1['OrderUID'] = $newvalue['OrderUID'];
			$data1['UserUID']=$this->session->userdata('UserUID');
			$this->common_model->Audittrail_insert($data1);

			$property_role = $this->saveProperty_Details($data,$insert_id);

			/* D-2-T9 GENERATE PACKAGE NUMBER BASED ON LOAN NUMBER*/
			$this->common_model->save_package_number($insert_id,$this->LoanNumber);

			/*-----Generate Map Start---*/
			$this->generate_map($insert_id);
			/*-----Generate Map End---*/


			/*----- Add Auto Create Task on Order Open Start---*/
			$this->common_model->AutoCreateTask_WorkflowLevel($insert_id, NULL, $this->config->item('OrderLevelStatus')['Order Open']);
			/*----- Add Auto Create Task on Order Open End---*/

			//insert sla action details for email entry start
			$this->common_model->insert_slaaction($insert_id,$this->config->item('SLAaction')['Order Placed'],$this->session->userdata('UserUID'));
			//insert sla action details end

			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
			}else{
				$this->db->trans_commit();
				$OrderNos[] = $this->OrderNumber;
			}

		}

		$OrderNumbers =  implode(",",$OrderNos);
		$Msg = $this->lang->line('Order_Save');
		$Rep_msg=str_replace("<<Order Number>>", $OrderNumbers , $Msg);
		return ["OrderUID"=>$insert_id, "OrderNumber"=>$OrderNumbers];

	}


	// Import Order via Email - Order Insert Model
	function email_update_order($data,$DatabaseAddress,$tOrders){
		//D-2-T23 state issuer fetched
		$data['Issuer'] = $this->lang->line('Inhouse_addtable');
		$searchmoderesult = $this->common_model->get_statesearchmode($data['PropertyStateCode'],$data['PropertyCountyName']);
		if(!empty($searchmoderesult)) {
			$data['Issuer'] = (!empty($searchmoderesult->SearchModeName)) ? $searchmoderesult->SearchModeName : $this->lang->line('Inhouse_addtable');
		}
		$date = date('Ymd');
		$insert_id = "";

		$IsDuplicateOrder = ($DatabaseAddress == NUll) ? 0 : 1 ;
		$this->IsTaxcert=isset($data['IsTaxcert'])? 1:0;

		$this->AltORderNumber = $data['AltORderNumber'];
		$this->LoanNumber = $data['LoanNumber'];
		$this->LoanAmount = $data['LoanAmount'];
		$this->CashOutAmount = $data['CashOutAmount'];
		$this->CustomerRefNum = $data['CustomerRefNum'];
		$this->CustomerUID = $data['customer'];
		$this->PropertyAddress1 = $data['PropertyAddress1'];
		$this->PropertyAddress2 = $data['PropertyAddress2'];
		$this->PropertyCityName = $data['PropertyCityName'];
		$this->PropertyStateCode = $data['PropertyStateCode'];
		$this->PropertyCountyName = $data['PropertyCountyName'];
		$this->PropertyZipcode = $data['PropertyZipcode'];
		$this->EmailReportTo = $data['EmailReportTo'];
		$this->AttentionName = $data['AttentionName'];
		$this->APN = $data['APN'];
		$this->IsDuplicateOrder = $IsDuplicateOrder;
		$this->StatusUID = $this->config->item('keywords')['New Order'];;
		$this->OrderEntryDatetime = Date('Y-m-d H:i:s',strtotime("now"));
		$pricing = new Customer_pricing();


		$mStates=$this->db->get_where('mStates', array('StateCode' => $this->PropertyStateCode))->row();

		$mCounties=$this->db->get_where('mCounties', array('StateUID'=>$mStates->StateUID, 'CountyName'=>$this->PropertyCountyName))->row();


		$OrderNos = [];
		foreach ($data['ProductUID'] as $key => $value) {
		$this->db->trans_begin();
			$this->SubProductUID = $data['SubProductUID'][$key];
			$this->PriorityUID = $data['PriorityUID'][$key];
			$this->ProjectUID = isset($data['ProjectUID'][$key]) ? $data['ProjectUID'][$key] : '0';
			//$this->IsMERS = isset($data['MERS'][$key]) ? 1 : 0;
			$OrderNo = $tOrders->OrderNumber;


			$this->OrderTypeUID = $this->GetSubProductOrderType($this->SubProductUID)->OrderTypeUID;
			$where['SubProductUID'] = $this->SubProductUID;
			$where['CustomerUID'] = $this->CustomerUID;
			$this->TemplateUID = $this->get_customer_subproduct_template($where)->TemplateUID;
			// $CustomerAmount = $pricing->get_Customer_Pricings($this);
			// $this->CustomerAmount = $CustomerAmount;
			// $this->CustomerActualAmount = $CustomerAmount;
			/*@Desc Customer Pricing Update D2T-540 @Author Jainulabdeen @Updated May 21 2020*/
			$CustomerAmountQuote = $pricing->get_Customer_Pricings_Quote($this);
			$this->CustomerAmount = $CustomerAmountQuote->Pricing;
			$this->CustomerActualAmount = $this->CustomerAmount;
			$this->IsQuote = $CustomerAmountQuote->IsQuote;
			/*End*/
			if ($mStates->SearchType==1) {
				$this->IsInhouseExternal=0;
			}
			elseif ($mStates->SearchType==2) {
			 	$this->IsInhouseExternal=1;
		 	}
		 	else{

		 		if ($mStates->AbstractorAssignment==1) {
					$this->IsInhouseExternal = $this->common_model->get_inhouse_external($this->OrderTypeUID,$mStates->StateUID,$mCounties->CountyUID);
		 		}
		 		else{
		 			$this->IsInhouseExternal = $this->common_model->get_inhouse_external_forzipcode($this->OrderTypeUID,$mStates->StateUID,$data['PropertyZipcode']);
		 		}
		 	}

			$CheckOrderType = substr($OrderNo, 0,1);
			if($CheckOrderType == 'F'){
				$this->IsInhouseExternal = 0;
			}

			$this->OrderDueDateTime = calculate_duedate($this->OrderEntryDatetime,$this->CustomerUID,$this->SubProductUID,$this->PriorityUID);
			$this->OrderDocsPath ='uploads/searchdocs/'.$date.'/'.$OrderNo.'/';
			$this->db->where('OrderUID', $tOrders->OrderUID);
			$query = $this->db->update('tOrders',$this);
			$insert_id = $tOrders->OrderUID;

			/*INSERT CUSTOMER ACTUAL PRICING*/
			$this->common_model->delete( 'tOrderPayments', 'OrderUID', $insert_id);
			$this->common_model-> insert_customerpaymentdata($insert_id);

			/*Insert Order Import*/
			$this->Insert_tOrderImport($data,$insert_id);

			$this->db->select('OrderUID');
			$this->db->from('tOrders');
			$this->db->where('OrderNumber',$this->OrderNumber);
			$newvalue=$this->db->get('')->row_array();


			$data1['ModuleName']='orderentry-insert';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR'];
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='tOrders';
			$data1['OrderUID'] = $newvalue['OrderUID'];
			$data1['UserUID']=$this->session->userdata('UserUID');
			$this->common_model->Audittrail_insert($data1);

			$this->db->where('OrderUID', $insert_id);
			$this->db->delete('tOrderPropertyRoles');
			$property_role = $this->saveProperty_Details($data,$insert_id);

			/* D-2-T9 GENERATE PACKAGE NUMBER BASED ON LOAN NUMBER*/
			$this->common_model->save_package_number($insert_id,$this->LoanNumber);

			/*-----Generate Map Start---*/
			$this->generate_map($insert_id);
			/*-----Generate Map End---*/

			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
			}else{
				$this->db->trans_commit();
			}

		}

		return ["OrderUID"=>$insert_id, "OrderNumber"=> $OrderNo];

	}
/* Order Import via email end */
	function SelectExcelImportedData($OrderUID='')
	{
		if($OrderUID !='')
		{

			$this->db->select ( '*' );
			$this->db->from ( 'tOrders' );
			$this->db->join('mordertypes','mordertypes.OrderTypeUID = tOrders.OrderTypeUID','LEFT');
			$this->db->join('morderpriority','morderpriority.PriorityUID = tOrders.PriorityUID','LEFT');
			$this->db->join('mCustomers','mCustomers.CustomerUID = tOrders.CustomerUID','LEFT');
			$this->db->join('mSubProducts','mSubProducts.SubProductUID = tOrders.SubProductUID','LEFT');
			$this->db->join('mProducts','mProducts.ProductUID = mSubProducts.ProductUID','LEFT');
			$this->db->join('mCities','mCities.CityUID = tOrders.PropertyCity','LEFT');
			$this->db->join('mStates','mStates.StateUID = tOrders.PropertyStateUID','LEFT');
			$this->db->join('mCounties','mCounties.CountyUID = tOrders.PropertyCountyUID','LEFT');
			$this->db->join('mtemplates','mtemplates.TemplateUID = tOrders.TemplateUID','LEFT');
			$this->db->join('tOrderPropertyRoles','tOrderPropertyRoles.OrderUID = tOrders.OrderUID','LEFT');
			$this->db->where_in('tOrders.OrderUID',$OrderUID);
			$this->db->group_by('tOrders.OrderUID');
			$query = $this->db->get();
			return $query->result();
		}
		else{
			echo "No Data Available";
		}
	}

	function GetExcelPropertyrolesDetails($OrderUID='')
	{
		if($OrderUID !=''){
			$this->db->select ( '*' );
			$this->db->from ( 'tOrderPropertyRoles' );
			$this->db->where_in('OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->result_array();
		}
		else{
			echo "No Data Available";
		}
	}


	function saveProperty_Details($data,$insert_id ='')
	{

	 /* $Address = $UspsAddress;
		$array = (array)$Address;

		$main_array = $array['Address'];
		$sub_array = (array)$main_array;*/

		$order_details = $this->common_model->get_orderdetails($insert_id);

		if(empty($data['PropertyRoleUID'][0])){
			return true;
		}else{
			$entry_array = array();
			$count = count($data['PropertyRoleUID']);
			$PropertyRoleUID = $data['PropertyRoleUID'];
			$PRName = $data['PRName'];
			$PRTitle = $data['PRTitle'];
			$PREmailID = $data['PREmailID'];
			$PRHomeNumber = $data['PRHomeNumber'];
			$PRWorkNumber = $data['PRWorkNumber'];
			$PRCellNumber = $data['PRCellNumber'];
			$PRSocialNumber = $data['PRSocialNumber'];
			$MailingAddress1 = isset($data['MailingAddress1']) ? $data['MailingAddress1'] : NULL; 
			$MailingAddress2 = isset($data['MailingAddress2']) ? $data['MailingAddress2'] : NULL; 
			$MailingZipCode = isset($data['MailingZipCode']) ? $data['MailingZipCode'] : NULL; 
			$MailingCityName = isset($data['MailingCityName']) ? $data['MailingCityName'] : NULL; 
			$MailingStateCode = isset($data['MailingStateCode']) ? $data['MailingStateCode'] : NULL;
			$MailingCountyName = isset($data['MailingCountyName']) ? $data['MailingCountyName'] : NULL; 
			$MailingAddressNotes = isset($data['MailingAddressNotes']) ? $data['MailingAddressNotes'] : NULL;
			$chk_mailing = isset($data['chk_mailing']) ? $data['chk_mailing'] : NULL; 

			$SigningAddress1 = isset($data['SigningAddress1']) ? $data['SigningAddress1'] : NULL; 
			$SigningAddress2 = isset($data['SigningAddress2']) ? $data['SigningAddress2'] : NULL; 
			$SigningZipCode = isset($data['SigningZipCode']) ? $data['SigningZipCode'] : NULL; 
			$SigningCityName = isset($data['SigningCityName']) ? $data['SigningCityName'] : NULL; 
			$SigningStateCode = isset($data['SigningStateCode']) ? $data['SigningStateCode'] : NULL;
			$SigningCountyName = isset($data['SigningCountyName']) ? $data['SigningCountyName'] : NULL; 
			$SigningAddressNotes = isset($data['SigningAddressNotes']) ? $data['SigningAddressNotes'] : NULL;
			$SigningSpecialInstruction = isset($data['SigningSpecialInstruction']) ? $data['SigningSpecialInstruction'] : NULL;
			$chk_Signing = $data['chk_Signing']; 

			$propertyroles_array = [];

			for($i=0; $i<$count; $i++)
			{
				$secret = $this->config->item('encryption_key');
				$encrypt = new AesCtr();
				$EncrptSocialNumber = $encrypt->encrypt($PRSocialNumber[$i], $secret, 256);

				$entry_array = array(
					"OrderUID"=>$insert_id,
					'PropertyRoleUID' => $PropertyRoleUID[$i],
					'PRName' => $PRName[$i],
					'PRTitle' => $PRTitle[$i],
					'PREmailID' => str_replace(['[', ']', '"', '\''], "",  $PREmailID[$i]),
					'PRHomeNumber' => $PRHomeNumber[$i],
					'PRWorkNumber' => $PRWorkNumber[$i],
					'PRCellNumber' => $PRCellNumber[$i],
					'PRSocialNumber' => $EncrptSocialNumber,
				);

				$entry_array['IsMailingAddress'] = $chk_mailing[$i];
				if ($chk_mailing[$i] == 'property') {
					$entry_array['MailingAddress1'] = NULL;
					$entry_array['MailingAddress2'] = NULL;
					$entry_array['MailingZipCode'] = NULL;
					$entry_array['MailingCityName'] = NULL;
					$entry_array['MailingStateCode'] = NULL;
					$entry_array['MailingCountyName'] = NULL;
					$entry_array['MailingAddressNotes'] = NULL;
				}
				else{
					$entry_array['MailingAddress1'] = $MailingAddress1[$i];
					$entry_array['MailingAddress2'] = $MailingAddress2[$i];
					$entry_array['MailingZipCode'] = $MailingZipCode[$i];
					$entry_array['MailingCityName'] = $MailingCityName[$i];
					$entry_array['MailingStateCode'] = $MailingStateCode[$i];
					$entry_array['MailingCountyName'] = $MailingCountyName[$i];
					$entry_array['MailingAddressNotes'] = $MailingAddressNotes[$i];
				} 

				if ($order_details->IsClosingProduct == 1) {
					$chk_Signing[$i] = !empty($chk_Signing[$i]) ? $chk_Signing[$i] : 'property';
					$entry_array['IsSigningAddress'] = $chk_Signing[$i];
					if ($chk_Signing[$i] == 'property') {

						$entry_array['SigningAddress1'] = NULL;
						$entry_array['SigningAddress2'] = NULL;
						$entry_array['SigningZipCode'] = NULL;
						$entry_array['SigningCityName'] = NULL;
						$entry_array['SigningStateCode'] = NULL;
						$entry_array['SigningCountyName'] = NULL;
						$entry_array['SigningAddressNotes'] = NULL;
						$entry_array['SigningSpecialInstruction'] = NULL;
					}
					else if ($chk_Signing[$i] == 'mailing') {

						$entry_array['SigningAddress1'] = NULL;
						$entry_array['SigningAddress2'] = NULL;
						$entry_array['SigningZipCode'] = NULL;
						$entry_array['SigningCityName'] = NULL;
						$entry_array['SigningStateCode'] = NULL;
						$entry_array['SigningCountyName'] = NULL;
						$entry_array['SigningAddressNotes'] = NULL;
						$entry_array['SigningSpecialInstruction'] = NULL;
					}
					else{

						$entry_array['SigningAddress1'] = $SigningAddress1[$i];
						$entry_array['SigningAddress2'] = $SigningAddress2[$i];
						$entry_array['SigningZipCode'] = $SigningZipCode[$i];
						$entry_array['SigningCityName'] = $SigningCityName[$i];
						$entry_array['SigningStateCode'] = $SigningStateCode[$i];
						$entry_array['SigningCountyName'] = $SigningCountyName[$i];
						$entry_array['SigningAddressNotes'] = $SigningAddressNotes[$i];
						$entry_array['SigningSpecialInstruction'] = $SigningSpecialInstruction[$i];
					} 
				}

				$propertyroles_array[] = $entry_array;
				$data1['ModuleName']=$PRName[$i].' '.'propertyroles-insert';
				$data1['IpAddreess']=$_SERVER['REMOTE_ADDR'];
				$data1['DateTime']=date('y-m-d H:i:s');
				$data1['TableName']='tOrderPropertyRoles';
				$data1['OrderUID']=$insert_id;
				$data1['UserUID']=$this->session->userdata('UserUID');
				$this->common_model->Audittrail_insert($data1);
			}


			if (!empty($propertyroles_array)) {
				$this->db->insert_batch('tOrderPropertyRoles', $propertyroles_array);				
			}

		}

/*    if($USPS == NULL){
			return true;
		}
		else{
			$fieldArray = array(
				"OrderUID"=>$insert_id,
				"USPSAddress1"=>$sub_array['Address2'],
				"USPSAddress2"=>'',
				"USPSZipcode"=>$USPS->PropertyZipcode,
				"USPSState"=>$USPS->PropertyStateUID,
				"USPSCity"=>$USPS->PropertyCity,
				"USPSCounty"=>$USPS->PropertyCountyUID
			);

			$query = $this->db->insert('torderaddress', $fieldArray);
		}*/

	}


	function getzipcontents($zipcode = '')
	{
		$zipcode=str_replace('-', '', $zipcode);
		$query = $this->db->query("SELECT * FROM `mCities`
			LEFT JOIN mStates ON mCities.StateUID = mStates.StateUID
			LEFT JOIN mCounties ON mCities.StateUID = mCounties.StateUID
			AND mCities.CountyUID = mCounties.CountyUID
			WHERE mCities.ZipCode = '$zipcode'");
		$result=$query->row();


		if(empty($result)){
			$zipcode_new=substr("$zipcode", 0, 5);

			$query = $this->db->query("SELECT * FROM `mCities`
				LEFT JOIN mStates ON mCities.StateUID = mStates.StateUID
				LEFT JOIN mCounties ON mCities.StateUID = mCounties.StateUID
				AND mCities.CountyUID = mCounties.CountyUID
				WHERE mCities.ZipCode  LIKE '$zipcode_new%'");
			return $query->row();
		}else{

			return $result;
		}
	}



	function getzipdetails($Zipcode)
	{
	
		// $this->load->model('Order_Complete/Order_complete_model');

		if(strlen($Zipcode) > 5){ // Zip Validation added @Auth Uba On May 22 2020
			if(strlen($Zipcode) == 10 && substr($Zipcode, 5, 1) == '-'){ // Digit 10 and has - @ 6 Pos

			}else if(strlen($Zipcode) == 9 && substr($Zipcode, 5, 1) != '-'){ // Digit 9 and should not - @ 6 Pos

			}else {
				return false;
			}
		}
		//if match zipcode inside special char echo empty
		else if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+]/', $Zipcode) || (strlen($Zipcode) != 5))
		{
    		return false;
		}
		
		if(!empty($Zipcode))
		{
			// 5 digits less then zipcode add 0 prefix for missing count
			/*$Zipcode = strtok($Zipcode,'-');
			if(strlen($Zipcode)<5)
		    {
		      $missing = 5 - strlen($Zipcode);	
		      $Zipcode = str_pad($Zipcode, $missing + strlen($Zipcode), '0', STR_PAD_LEFT);
		    } else {
		      $Zipcode = substr($Zipcode, 0,5);	
		    }*/

		    if (strlen($Zipcode) > 5) {
		    	$Zipcode = substr($Zipcode, 0,5);	
		    }
		    
  	// 		$City = $this->Order_complete_model->getCityDetail($Zipcode);
			// $State = $this->Order_complete_model->getStateDetail($Zipcode);
			// $County = $this->Order_complete_model->getCountyDetail($Zipcode);

			$City = $this->getCityDetail($Zipcode);
			$State = $this->getStateDetail($Zipcode);
			$County = $this->getCountyDetail($Zipcode);
			if(count($State) > 0 && count($County) > 0 && count($City) > 0){

				return true;
			}else{
				return false;
				
			}

		}else{
			return false;
		}
	}
	function getCityDetail($zipcode) {

      /*$this->db->join('mStates','mCities.StateUID=mStates.StateUID','left');
      $this->db->join('mCounties','mCities.StateUID=mCounties.StateUID','left');*/
      $query = $this->db->get_where('mCities', array('ZipCode' => $zipcode));
      return $query->result();

  }

  function getStateDetail($zipcode) {

  	$this->db->join('mStates','mCities.StateUID=mStates.StateUID','left');
  	$query = $this->db->get_where('mCities', array('ZipCode' => $zipcode));
  	return $query->result();
  }

  function getCountyDetail($zipcode) {

  	$this->db->join('mCounties','mCities.CountyUID=mCounties.CountyUID','left');
  	$query = $this->db->get_where('mCities', array('ZipCode' => $zipcode));
  	return $query->result();
  }

	function get_mordertypes($OrderTypeName){

		$this->db->where(array("Active"=>1,"OrderTypeName"=>$OrderTypeName));
		$query = $this->db->get('mordertypes');
		return $query->row();
	}

	function get_state($StateCode){

		/*$this->db->where(array("Active"=>1,"StateCode"=>$StateCode));*/
		$this->db->where(array("StateCode"=>$StateCode));
		$query = $this->db->get('mStates');
		return $query->row();
	}

	function get_city($CityName,$zipcode){

		$this->db->like('CityName', $CityName);
		$this->db->where(array("Active"=>1,"ZipCode"=>$zipcode));
		$query = $this->db->get('mCities');
		return $query->row();
	}

	function get_county($StateUID,$CountyName){

		$this->db->where(array("Active"=>1,"CountyName"=>$CountyName,"StateUID"=>$StateUID));
		$query = $this->db->get('mCounties');
		return $query->row();
	}


	function get_product($ProductName){

		$this->db->where(array("Active"=>1,"ProductName"=>$ProductName));
		$query = $this->db->get('mProducts');
		return $query->row();
	}


	function get_customer($CustomerName){

		$this->db->where(array("Active"=>1,"CustomerName"=>$CustomerName));
		$query = $this->db->get('mCustomers');
		return $query->row();
	}

	function get_priority($PriorityName){

		$this->db->where(array("Active"=>1,"PriorityName"=>$PriorityName));
		$query = $this->db->get('morderpriority');
		return $query->row();
	}

	function get_sub_product($SubProductName = ''){

		$query = $this->db->query("SELECT * FROM (`mSubProducts`) WHERE `Active` = 1 AND (`SubProductName` = '$SubProductName' OR `SubProductCode` = '$SubProductName')");
		return $query->row();
	}


	function get_template($TemplateName){

		$query = $this->db->query("SELECT * FROM (`mtemplates`) WHERE `Active` = 1 AND (`TemplateName` = '$TemplateName' OR `TemplateCode` = '$TemplateName')");
		return $query->row();
	}

	function get_propertyroles($PropertyRoleName){
		$this->db->where(array("Active"=>1,"PropertyRoleName"=>$PropertyRoleName));
		$query = $this->db->get('mpropertyroles');
		return $query->row();
	}


	function savebulkentry_order($data,$arrayCode,$files){
		// add slashes for special char 
		// foreach ($data as $key => $value) {
		// 	$data[$key] = addslashes($data[$key]);
		// }

		//D-2-T23 state issuer fetched
		$data['Issuer'] = $this->lang->line('Inhouse_addtable');
		// $searchmoderesult = $this->common_model->get_statesearchmode($data['PropertyStateCode'],$data['PropertyCountyName']);
		// if(!empty($searchmoderesult)) {
		// 	$data['Issuer'] = (!empty($searchmoderesult->SearchModeName)) ? $searchmoderesult->SearchModeName : $this->lang->line('Inhouse_addtable');
		// }
		$field_count = 20; 
		$Sellercolumn = $arrayCode[19];
		$this->AltORderNumber = $data['AltORderNumber'];
		$this->LoanNumber = $data['LoanNumber'];
		$this->LoanAmount = $data['LoanAmount'];
		$this->CustomerRefNum = $data['CustomerRefNum'];
		$this->CustomerUID = $data['CustomerUID'];
		$this->SubProductUID = $data['SubProductUID'];
		$this->PriorityUID = $data['PriorityUID'];
		$this->ProjectUID = $data['ProjectUID'];
		$this->OrderTypeUID = $data['OrderTypeUID'];
		$this->PropertyAddress1 = $data['PropertyAddress1'];
		$this->PropertyCityName = $data['PropertyCityName'];
		$this->PropertyStateCode = $data['PropertyStateCode'];
		$this->PropertyCountyName = $data['PropertyCountyName'];
		$this->PropertyZipcode = $data['PropertyZipcode'];
		$this->TemplateUID = $data['TemplateUID'];
		$this->EmailReportTo = $data['EmailReportTo'];
		$this->AttentionName = $data['AttentionName'];
		$this->APN = $data['APN'];
		$this->OwnerOccupancy = $data['OwnerOccupancy'];
		$this->StatusUID = $this->config->item('keywords')['New Order'];
		$this->OrderEntryDatetime = Date('Y-m-d H:i:s',strtotime("now"));
		$this->LastTouchDateTime = Date('Y-m-d H:i:s',strtotime("now"));
		$this->LastModifiedByUserUID = $this->session->userdata('UserUID');
		$this->OrderCreatedByUserUID = $this->session->userdata('UserUID');
		$this->BulkImportSheet = $data['BulkImportSheet'];

		if($data['TransactionType'] !='' || $data['PropertyType'] !=''|| $data['BorrowerType'] !='')
		{
			$this->BorrowerType = $data['BorrowerType'];
			$this->PropertyType = $data['PropertyType'];
			$this->TransactionType = $data['TransactionType'];
		}

		$pricing = new Customer_pricing();
		
		// $this->CustomerAmount = $pricing->get_Customer_Pricings($this);
		// $this->CustomerActualAmount = $this->CustomerAmount;
		/*@Desc Customer Pricing Update D2T-540 @Author Jainulabdeen @Updated May 21 2020*/
		$CustomerAmountQuote = $pricing->get_Customer_Pricings_Quote($this);
		$this->CustomerAmount = $CustomerAmountQuote->Pricing;
		$this->CustomerActualAmount = $this->CustomerAmount;
		$this->IsQuote = $CustomerAmountQuote->IsQuote;
		/*End*/
		$mStates=$this->db->get_where('mStates', array('StateCode' => $this->PropertyStateCode))->row();

		$mCounties=$this->db->get_where('mCounties', array('StateUID'=>$mStates->StateUID, 'CountyName'=>$this->PropertyCountyName))->row();

		if ($mStates->SearchType==1) {
			$this->IsInhouseExternal=0;
		}
		elseif ($mStates->SearchType==2) {
			$this->IsInhouseExternal=1;
		}
		else{
			if ($mStates->AbstractorAssignment==1) {
				$this->IsInhouseExternal = $this->common_model->get_inhouse_external($this->OrderTypeUID,$mStates->StateUID,$mCounties->CountyUID);
			}
			else{
				$this->IsInhouseExternal = $this->common_model->get_inhouse_external_forzipcode($this->OrderTypeUID,$mStates->StateUID,$data['PropertyZipcode']);
			}
		}


		$this->OrderDueDateTime = calculate_duedate($this->OrderEntryDatetime,$this->CustomerUID,$this->SubProductUID,$this->PriorityUID);
		$propkey_array = array('PRName','PREmailID','PRHomeNumber','PRWorkNumber','PRCellNumber','PRSocialNumber');
		$this->db->trans_begin();
		$this->OrderNumber = $this->Order_Number($data['SubProductUID']).'-1';
		/* NF216 - Duplicate Order number restriction */
		$this->OrderSequence = $this->OrderSequence($data['SubProductUID']).'-1';
		$date = date('Ymd');
		$this->OrderDocsPath ='uploads/searchdocs/'.$date.'/'.$this->OrderNumber.'/';

		/* desc: Insert Order Policy Type 
		** author: Yagavi G <yagavi.g@avanzegroup.com>
		** since: Aug 7th 2020
		*/
		$where_policy_type['SubProductUID'] = $this->SubProductUID;
		$where_policy_type['CustomerUID'] = $this->CustomerUID;
		$this->OrderPolicyType = $this->common_model->GetCustomerProductDetailsByCustomerSubproductUID($where_policy_type)->OrderPolicyType;

		$query = $this->db->insert('tOrders',$this);
		$lastinsertid = $this->db->insert_id();	

		$mSubProducts = $this->common_model->get_row('mSubProducts', ['SubProductUID'=>$this->SubProductUID]);
		$IsClosingProduct = $this->Orderentry_model->IsClosingProduct($mSubProducts->ProductUID);

		if (!empty($IsClosingProduct))
		{
			$this->common_model->insertWorkflowQueues($lastinsertid, $this->CustomerUID, $this->SubProductUID, "Order import", 0, false);
		}


		/* @purpose: D2T-55: Get Default Template for the order @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: 13th May 2020 */
		$template_data['TemplateUID'] = $this->GetTemplateUIDByCustomerUID($data['CustomerUID'],$data['SubProductUID']);
		$this->db->where('OrderUID', $lastinsertid);
		$this->db->update('tOrders',$template_data);
		
		$data1['ModuleName']='orderentry-insert';
		$data1['IpAddreess']=$_SERVER['REMOTE_ADDR'];
		$data1['DateTime']=date('y-m-d H:i:s');
		$data1['TableName']='tOrders';
		$data1['OrderUID'] = $lastinsertid;
		$data1['UserUID']=$this->session->userdata('UserUID');
		$this->common_model->Audittrail_insert($data1);
		
		/*INSERT CUSTOMER ACTUAL PRICING*/
		$this->common_model->insert_customerpaymentdata($lastinsertid);

		if(!empty($Sellercolumn)){
			$sellerdata['OrderUID'] = $lastinsertid; 
			$sellerdata['PropertyRoleUID'] = $this->config->item('Propertyroles')['Sellers']; 
			$sellerdata['PRName'] = $Sellercolumn; 
			$this->db->insert('tOrderPropertyRoles', $sellerdata);
		}

		$property_array = array();
			$propkey_array = array('PRName','PREmailID','PRHomeNumber','PRWorkNumber','PRCellNumber','PRSocialNumber');
			/*$propkey_array = array('PRName','PREmailID','PRHomeNumber','PRWorkNumber','PRCellNumber','PRSocialNumber','IsMailingAddress','MailingAddress1','MailingZipCode','MailingCityName','MailingStateCode','MailingCountyName');*/
		$sliced = array_slice($arrayCode, $field_count, count($arrayCode));
		/*$sliced_array = array_chunk($sliced, 12);*/
		$sliced_array = array_chunk($sliced, 6);
		//$sliced_array = array_map('array_filter', $sliced_array);
		//$sliced_array = array_filter($sliced_array);
		$entry_array = [];
		foreach ($sliced_array as $sliced_key => $sliced_value)
		{
			if($sliced_value[0] != '' && trim($sliced_value[0]) != '')
			{
				$entry_array[$sliced_key]['OrderUID'] = $lastinsertid;
				$entry_array[$sliced_key]['PropertyRoleUID'] = $this->config->item('Propertyroles')['Borrowers'];
				foreach ($sliced_value as $key => $value){
					$entry_array[$sliced_key][$propkey_array[$key]] = $value;
				}
			}
		}

		foreach ($entry_array as $key => $value) {
			$PropertyRoles=array_filter($value, function($value) { return !is_null($value) && $value !== ''; });
			if(!empty($PropertyRoles)){
			$this->db->insert('tOrderPropertyRoles', $PropertyRoles);
		  }
		}

	/*	if(!empty($entry_array)){
			$this->db->insert_batch('tOrderPropertyRoles', $PropertyRoles);
		}*/

		$OrderNumber=$this->OrderNumber;
        $OrderUID=$lastinsertid;

        $date = date('Ymd');
        $Path = FCPATH .'uploads/searchdocs/'.$date.'/'.$OrderNumber.'/';
        $viewPath = 'uploads/searchdocs/'.$date.'/'.$OrderNumber.'/';

        if (!is_dir($Path)) {
          mkdir($Path, 0777, true);
        }

		$LoanNumber = $data['LoanNumber'];

		if(isset($files)){

			foreach ($files['name'] as $key => $value) {
				$dotposition = strripos($value, '.');
				$documentname = substr($value, 0, $dotposition);

				if (strpos(strtolower($documentname), strtolower($LoanNumber)) !== false) {
					$this->NormalFileUpload($files['tmp_name'][$key], $Path . $value, $OrderUID);

					    $tDocuments = array(
									    	'OrderUID'=>$OrderUID,
									    	'DocumentFileName'=>$value,
										    'DisplayFileName'=>$value,
										    'UploadedDate'=>date('Y-m-d H:i:s'),
										    'DocumentCreatedDate'=>date('Y-m-d H:i:s'),
										    'UploadedUserUID'=>$this->loggedid
										);

					    $this->db->insert('torderdocuments', $tDocuments);
				}
			}
		}

		/* D-2-T9 GENERATE PACKAGE NUMBER BASED ON LOAN NUMBER*/
		$this->common_model->save_package_number($OrderUID,$this->LoanNumber);

		/*-----Generate Map Start---*/
		$this->generate_map($lastinsertid);
		/*-----Generate Map End---*/

		/*----- Add Auto Create Task on Order Open Start---*/
		$this->common_model->AutoCreateTask_WorkflowLevel($OrderUID, NULL, $this->config->item('OrderLevelStatus')['Order Open']);
		/*----- Add Auto Create Task on Order Open End---*/

		/**
		* @author Alwin L
		* @purpose check and place x1 orders to api
		* @author Yagavi G <yagavi.g@avanzegrouo.com>
		* @purpose Disabiling bulk orders to X1
		* @since April 9th 2020
		**/

		/* Start - Placing Order to X1 */	
		
		/*$x1CustomerCheck = $this->CheckX1Order($this->CustomerUID,$data['ProductUID'],$data['SubProductUID']);		
		$X1SearchMode = $this->GetPreferedSites($lastinsertid) ;
		if(!empty($X1SearchMode) && $x1CustomerCheck)
		{
			$this->load->model('x1/X1_model');
			$result = $this->X1_model->PlaceOrder_X1($OrderUID,'bulk');
		}*/
		/* End - Placing Order to X1 */

		//insert sla action details for bulk entry start
		$this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['Order Placed'],$this->loggedid);
		//insert sla action details end

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return $lastinsertid;;
		}

	}

	/* @purpose: Get TemplateUID from Client Setup @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: 13th May 2020 */
	function GetTemplateUIDByCustomerUID($CustomerUID,$SubProductUID) {
		$this->db->select('TemplateUID');
		$this->db->from('mcustomertemplates');
		$this->db->where('CustomerUID',$CustomerUID);
		$this->db->where('SubProductUID',$SubProductUID);
		$SubproductTemplate = $this->db->get()->row();
		$TemplateUID = $SubproductTemplate->TemplateUID;

		if(empty($TemplateUID)){
			$this->db->select('*')->from('mCustomers');
			$this->db->where('CustomerUID',$CustomerUID);
			$this->db->join('mtemplates','mtemplates.TemplateUID = mCustomers.DefaultTemplateUID','left');
			$mCustomers = $this->db->get()->row();
			$TemplateUID = $mCustomers->TemplateUID;
		}
		return $TemplateUID;
	}

	function GetOrderCheck(){

		$year=date('Ymd');
		$query = $this->db->query("SELECT EXISTS(SELECT OrderNumber FROM `tOrders` WHERE OrderNumber LIKE '$year%') as CheckOrderNumber;
			");
		return $query->row();
	}

	function OrderNumber()
	{

		$CheckOrderNumber = $this->GetOrderCheck();
		$CheckOrderNumber = $CheckOrderNumber->CheckOrderNumber;


		if($CheckOrderNumber == 1){

			$year=date('Ymd');
			$query = $this->db->query("SELECT MAX(`OrderNumber`) AS `AUTO_INCREMENT` FROM `tOrders` WHERE OrderNumber LIKE '$year%'");
			$res = $query->row();

			$auto_number = $res->AUTO_INCREMENT + 1;
			return $auto_number;
		}
		else{

			$year=date('Ymd');
			$query = $this->db->query("SELECT MAX(`OrderNumber`) AS `AUTO_INCREMENT` FROM `tOrders` WHERE OrderNumber LIKE '$year%'");
			$res = $query->row();

			$id = sprintf("%06d",$res->AUTO_INCREMENT+1);
			$year=date('Ymd');
			$auto_number=$year."".$id;

			return $auto_number;

		}

	}


	function get_product_details($CustomerUID)
	{

		$SubProductUIDs = $this->db->query("SELECT GROUP_CONCAT(SubProductUID SEPARATOR ',') as SubProductUIDs FROM mCustomerProducts where CustomerUID = $CustomerUID")->row();
		$SubProductUIDs = $SubProductUIDs->SubProductUIDs;

		$Products = [];
		$SubProducts = [];
		$Products = $this->common_model->GetProductDetails();

		$ProductUID = count($Products) > 0 ? $Products[0]->ProductUID : '';

		$SubProducts = $this->common_model->GetSub_productDetails($ProductUID);


		if($SubProductUIDs !=''){
			$ProductUIDs = $this->db->query("SELECT GROUP_CONCAT(ProductUID SEPARATOR ',') as ProductUIDs FROM mSubProducts where SubProductUID IN ($SubProductUIDs)")->row();
			$ProductUIDs = $ProductUIDs->ProductUIDs;



			$this->db->select ( 'SubProductUID,SubProductName' );
			$this->db->from ( 'mSubProducts' );
			$this->db->where_in ('mSubProducts.ProductUID',$ProductUIDs);
			$query = $this->db->get();
			$SubProducts =  $query->result();

			$this->db->select ( 'ProductUID,ProductName' );
			$this->db->from ( 'mProducts' );
			$this->db->where_in ('mProducts.ProductUID',$ProductUIDs);
			$querys = $this->db->get();
			$Products =  $querys->result();
		}
		$this->db->select ( '*' );
		$this->db->from ( 'mCustomers' );
		$this->db->where ('mCustomers.CustomerUID',$CustomerUID);
		$queryss = $this->db->get();
		$CustomerDetails =  $queryss->row();

		$this->db->select ( '*' );
		$this->db->from ( 'mUsers' );
		$this->db->where ('mUsers.UserUID',$this->session->userdata('UserUID'));
		$queryss = $this->db->get();
		$UserDetails =  $queryss->row();

		return array('Products'=>$Products,'SubProducts'=> $SubProducts,'CustomerDetails'=>$CustomerDetails,'UserDetails'=>$UserDetails);

	}

	function Get_Assign_Customer($user_id)
	{
		$this->db->select('*,mUsers.CustomerUID')->from('mUsers');
		$this->db->where('UserUID',$user_id);
		$this->db->join('mCustomers','mUsers.CustomerUID = mCustomers.CustomerUID','LEFT');
		return $this->db->get()->result();
	}

	function get_customer_ingroup($user_id)
	{
		$group_id = $this->db->query("SELECT GROUP_CONCAT(GroupUID SEPARATOR ',') as group_id FROM mgroupusers where GroupUserUID = $user_id")->row();



		$gids= $group_id->group_id;

		if($gids !=''){
			$cust_id = $this->db->query("SELECT GROUP_CONCAT(GroupCustomerUID SEPARATOR ',') as cust_id FROM mgroupcustomers where GroupUID IN ($gids)")->row();

			$cust_ids = $cust_id->cust_id;

			if($cust_ids !=''){

				$result = $this->db->query("SELECT `CustomerName`, `CustomerUID`, `CustomerNumber` FROM (`mCustomers`) WHERE `mCustomers`.`CustomerUID` IN ($cust_ids) ");
				return $result->result();


			}
		}else{
			return '';
		}



	}

	function GetCustomerProductDetails($CustomerUID)
	{
		$this->db->select('mProducts.*,mProducts.ProductUID,mProducts.ProductName,mSubProducts.SubProductUID,mSubProducts.SubProductName,mProducts.IsRMSProduct,mSubProducts.RMS')->from('mCustomerProducts');
		$this->db->join('mSubProducts','mSubProducts.SubProductUID=mCustomerProducts.SubProductUID', 'left');
		$this->db->join('mProducts','mProducts.ProductUID = mCustomerProducts.ProductUID','left');
		$this->db->where('mCustomerProducts.CustomerUID',$CustomerUID);
		$this->db->group_by('mCustomerProducts.ProductUID');
		$query = $this->db->get()->result();
		return $query;
	}

	function GetCustomerSubProductDetails($CustomerUID,$ProductUID)
	{
		$this->db->select('mProducts.ProductUID,mProducts.ProductName,mSubProducts.SubProductUID,mSubProducts.SubProductName,mProducts.IsRMSProduct,mSubProducts.RMS')->from('mCustomerProducts');
		$this->db->join('mSubProducts','mSubProducts.SubProductUID=mCustomerProducts.SubProductUID', 'left');
		$this->db->join('mProducts','mProducts.ProductUID = mCustomerProducts.ProductUID','left');
		$this->db->where('mCustomerProducts.CustomerUID',$CustomerUID);
		$this->db->where('mCustomerProducts.ProductUID',$ProductUID);
		$query = $this->db->get()->result();
		return $query;
	}

	function text_savebulkentry_order($data){
		//D-2-T23 state issuer fetched
		$data['Issuer'] = $this->lang->line('Inhouse_addtable');
		$searchmoderesult = $this->common_model->get_statesearchmode($data['PropertyStateCode'],$data['PropertyCountyName']);
		if(!empty($searchmoderesult)) {
			$data['Issuer'] = (!empty($searchmoderesult->SearchModeName)) ? $searchmoderesult->SearchModeName : $this->lang->line('Inhouse_addtable');
		}
		$this->OrderNumber = $data['OrderNumber'];
// $this->AltORderNumber = $data['AltORderNumber'];
		$this->LoanNumber = $data['LoanNumber'];
		$this->LoanAmount = $data['LoanAmount'];
// $this->CustomerRefNum = $data['CustomerRefNum'];
		$this->CustomerUID = $data['CustomerUID'];
		$this->SubProductUID = $data['SubProductUID'];
		$this->PriorityUID = $data['PriorityUID'];
		$this->PriorityUID = $data['PriorityUID'];
		$this->OrderTypeUID = $data['OrderTypeUID'];
		$this->PropertyAddress1 = $data['PropertyAddress1'];
		// $this->PropertyAddress2 = $data['PropertyAddress2'];
		$this->PropertyCityName = $data['PropertyCityName'];
		$this->PropertyStateCode = $data['PropertyStateCode'];
		$this->PropertyCountyName = $data['PropertyCountyName'];
		$this->PropertyZipcode = $data['PropertyZipcode'];
		$this->TemplateUID = $data['TemplateUID'];
		$this->EmailReportTo = $data['EmailReportTo'];
		$this->APN = $data['APN'];
		$this->StatusUID = $this->config->item('keywords')['New Order'];
		$this->OrderEntryDatetime = Date('Y-m-d H:i:s',strtotime("now"));

		$mStates=$this->db->get_where('mStates', array('StateCode' => $this->PropertyStateCode))->row();

		$mCounties=$this->db->get_where('mCounties', array('StateUID'=>$mStates->StateUID, 'CountyName'=>$this->PropertyCountyName))->row();

		if ($mStates->SearchType==1) {
			$this->IsInhouseExternal=0;
		}
		elseif ($mStates->SearchType==2) {
		 	$this->IsInhouseExternal=1;
		 }
		 else{
		 	if ($mStates->AbstractorAssignment==1) {
		 		$this->IsInhouseExternal = $this->common_model->get_inhouse_external($this->OrderTypeUID,$mStates->StateUID,$mCounties->CountyUID);
		 	}
		 	else{
		 		$this->IsInhouseExternal = $this->common_model->get_inhouse_external_forzipcode($this->OrderTypeUID,$mStates->StateUID,$data['PropertyZipcode']);
		 	}

		 }


		$this->OrderDueDateTime = calculate_duedate($this->OrderEntryDatetime,$this->CustomerUID,$this->SubProductUID,$this->PriorityUID);
		$pricing = new Customer_pricing();
		// $this->CustomerAmount = $pricing->get_Customer_Pricings($this);
		// $this->CustomerActualAmount = $this->CustomerAmount;
		/*@Desc Customer Pricing Update D2T-540 @Author Jainulabdeen @Updated May 21 2020*/
		$CustomerAmountQuote = $pricing->get_Customer_Pricings_Quote($this);
		$this->CustomerAmount = $CustomerAmountQuote->Pricing;
		$this->CustomerActualAmount = $this->CustomerAmount;
		$this->IsQuote = $CustomerAmountQuote->IsQuote;
		/*End*/

		/* desc: Insert Order Policy Type 
		** author: Yagavi G <yagavi.g@avanzegroup.com>
		** since: Aug 7th 2020
		*/
		$where_policy_type['SubProductUID'] = $this->SubProductUID;
		$where_policy_type['CustomerUID'] = $this->CustomerUID;
		$this->OrderPolicyType = $this->common_model->GetCustomerProductDetailsByCustomerSubproductUID($where_policy_type)->OrderPolicyType;

		$query = $this->db->insert('tOrders',$this);

		$insert_id = $this->db->insert_id();

		/*INSERT CUSTOMER ACTUAL PRICING*/
		$this->common_model->insert_customerpaymentdata($insert_id);

		/*Insert Order Import*/
		$this->Insert_tOrderImport($data,$insert_id);

		/* D-2-T9 GENERATE PACKAGE NUMBER BASED ON LOAN NUMBER*/
		$this->common_model->save_package_number($insert_id,$this->LoanNumber);

		/*-----Generate Map Start---*/
		$this->generate_map($insert_id);
		/*-----Generate Map End---*/

		return $insert_id;
	}

	function text_save_bulkentry_prop($arrayCode,$lastinsertid)
	{
		$property_array = array();
		$propkey_array = array('PRName','PREmailID','PRHomeNumber','PRWorkNumber','PRCellNumber','PRSocialNumber');
		$sliced = array_slice($arrayCode, 17, count($arrayCode));
		$sliced_array = array_chunk($sliced, 6);

		$entry_array = [];
		foreach ($sliced_array as $sliced_key => $sliced_value) {
			$entry_array[$sliced_key]['OrderUID'] = $lastinsertid;
			$entry_array[$sliced_key]['PropertyRoleUID'] = $this->config->item('Propertyroles')['Borrowers'];
			foreach ($sliced_value as $key => $value) {
				$entry_array[$sliced_key][$propkey_array[$key]] = $value;

			}

		}
		if(count($entry_array)>0){
			$this->db->insert_batch('tOrderPropertyRoles', $entry_array);
		}

		return true;

	}


	function gettemplatecontents($CustomerUID)
	{
		$query = $this->db->query("SELECT * FROM mtemplates t1,mCustomers t2
			WHERE t1.TemplateUID = t2.DefaultTemplateUID AND t2.CustomerUID = '$CustomerUID' ");

		return $query->row();
	}

	function get_customer_product_details($CustomerUID)
	{
		/*FOR SUPERVISOR CHECK*/
		$where  ='';
		$Products = '';
		$SubProducts = '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();

			if($UserProducts){
				$this->db->select('group_concat(DISTINCT ProductUID) as CProduct, group_concat(DISTINCT SubProductUID) as CSubProduct');
				$this->db->from('mCustomerProducts');
				$this->db->where('mCustomerProducts.ProductUID IN ('.$UserProducts.')', null, false);
				$this->db->where('CustomerUID',$CustomerUID);
				$customer = $this->db->get()->row();
			}
		}else{

			$this->db->select('group_concat(DISTINCT ProductUID) as CProduct, group_concat(DISTINCT SubProductUID) as CSubProduct');
			$this->db->from('mCustomerProducts');
			$this->db->where('CustomerUID',$CustomerUID);
			$customer = $this->db->get()->row();

		}


		if(!empty($customer)){
			$Product = $customer->CProduct;
			$SubProduct = $customer->CSubProduct;
			$Products = $this->db->query("SELECT * FROM mProducts WHERE mProducts.ProductUID IN ($Product)")->result_array();
		}


		// For Customers Only
		$this->RoleUID = $this->session->userdata('RoleUID');
		if($this->RoleUID == 8)
		{
			$query = $this->db->query("SELECT * FROM mCustomerDefaultProduct WHERE CustomerUID ='$CustomerUID' ");
			$data = $query->row();
			$DefaultProductSubCode = $data->DefaultProductSubCode;
			if($DefaultProductSubCode == 1)
			{
				$SubProductUID = $data->DefaultProductSubValue;
				$SubProducts = $this->db->query("SELECT SubProductUID,SubProductName,ProductUID FROM mSubProducts
					WHERE SubProductUID IN ($SubProductUID)")->result_array();

			}
			else if($DefaultProductSubCode == 2)
			{
				$month=date("m");
				$SubProducts = $this->db->query("SELECT tOrders.SubProductUID,SubProductName FROM tOrders LEFT JOIN  mSubProducts ON mSubProducts.SubProductUID = tOrders.SubProductUID WHERE tOrders.CustomerUID ='$CustomerUID' and MONTH(tOrders.OrderEntryDatetime) = '$month'GROUP BY tOrders.SubProductUID ORDER BY COUNT(tOrders.SubProductUID) DESC")->result_array();

			}
			else
			{
				$year=date("Y");
				$SubProducts = $this->db->query("SELECT tOrders.SubProductUID,SubProductName FROM tOrders LEFT JOIN  mSubProducts ON mSubProducts.SubProductUID = tOrders.SubProductUID WHERE tOrders.CustomerUID ='$CustomerUID' and YEAR(tOrders.OrderEntryDatetime) = $year GROUP BY tOrders.SubProductUID  ORDER BY COUNT(tOrders.SubProductUID) DESC")->result_array();

			}
		}
		// For Customers Only
		else
		{
			if(!empty($SubProduct)){
				$SubProducts = $this->db->query("SELECT * FROM mSubProducts WHERE mSubProducts.SubProductUID IN ($SubProduct)")->result_array();
			}
			
		}
		$this->db->select ( '*' );
		$this->db->from ( 'mCustomers' );
		$this->db->where ('mCustomers.CustomerUID',$CustomerUID);
		$queryss = $this->db->get();
		$CustomerDetails =  $queryss->row();

		$this->db->select ( '*' );
		$this->db->from ( 'mUsers' );
		$this->db->where ('mUsers.UserUID',$this->session->userdata('UserUID'));
		$queryss = $this->db->get();
		$UserDetails =  $queryss->row();

		$CustomerBranches= $this->common_model->getCustomerSchedule($CustomerUID);

		return array('Products'=>$Products,'SubProducts'=> $SubProducts,'CustomerDetails'=>$CustomerDetails,'UserDetails'=>$UserDetails, 'CustomerBranches'=>$CustomerBranches);
	}



	function GetOrderDetailsForCopy($OrderDetail){

		$PropertyAddress1 =  $OrderDetail['PropertyAddress1'];
		$PropertyAddress2 = $OrderDetail['PropertyAddress2'];
		$PropertyCity = $OrderDetail['PropertyCity'];
		$PropertyStateUID = $OrderDetail['PropertyStateUID'];
		$PropertyZipcode = $OrderDetail['PropertyZipcode'];
		$PropertyCountyUID = $OrderDetail['PropertyCountyUID'];

		$this->db->select ( '*' );
		$this->db->from ( 'tOrders' );
		$this->db->where(array("tOrders.PropertyCity"=>$PropertyCity,"tOrders.PropertyCountyUID"=>$PropertyCountyUID,"tOrders.PropertyStateUID"=>$PropertyStateUID,"tOrders.PropertyZipcode"=>$PropertyZipcode ));
		$query = $this->db->get();
		return $query->result();

	}

	function getCustomerInfo($CustomerUID){
		$this->db->select('*');
		$this->db->from('mCustomers');
		$this->db->join ( 'mStates', 'mStates.StateUID =mCustomers.CustomerStateUID' , 'left' );
		$this->db->join ( 'mCities', 'mCities.CityUID = mCustomers.CustomerCityUID' , 'left' );
		$this->db->join ( 'mCounties', 'mCounties.CountyUID = mCustomers.CustomerCountyUID' , 'left');
		$this->db->like('mCustomers.CustomerName',$CustomerUID);
		$this->db->or_like('mCustomers.CustomerNumber',$CustomerUID);
		return $this->db->get('')->result_array();

	}


	function GetCustomerDetailsByCustomerUID($CustomerUID){

		$this->db->select('*');
		$this->db->from('mCustomers');
		$this->db->join ( 'mStates', 'mStates.StateUID =mCustomers.CustomerStateUID' , 'left' );
		$this->db->join ( 'mCities', 'mCities.CityUID = mCustomers.CustomerCityUID' , 'left' );
		$this->db->join ( 'mCounties', 'mCounties.CountyUID = mCustomers.CustomerCountyUID' , 'left');
		$this->db->where('mCustomers.CustomerUID',$CustomerUID);
		return $this->db->get('')->row_array();

	}


	function Order_Number($SubProductUID,$PreviousOrderUID = NULL){
		if(!empty($SubProductUID)){
			$this->db->select('ProductCode');
			$this->db->from('mSubProducts');
			$this->db->join('mProducts','mProducts.ProductUID = mSubProducts.ProductUID');
			$this->db->where('SubProductUID', $SubProductUID);
			$query = $this->db->get();
			$result = $query->row();
			$code = $result->ProductCode;
		}else{
			$code = '0';
		}
			$date =  date("y");
			$id = sprintf("%06d",0);
			$lastOrderNo = $code.$date.$id;
			
			if(!empty($PreviousOrderUID)){
				// Order Number year fixed for previous Year Orders @Uba @On Jul 2020 
				$last_row_seq = $this->db->select('OrderSequence,OrderNumber,OrderUID')->where('OrderUID',$PreviousOrderUID)->get('tOrders')->row();
				$LastNo = explode('-',$last_row_seq->OrderNumber);
				$CenterSequence = substr($LastNo[0],3);

				$last_row_sequence = $this->db->select('OrderSequence,OrderNumber,OrderUID')->like('OrderNumber', $CenterSequence, 'both')->order_by('OrderUID',"DESC")->limit(1)->get('tOrders')->row();
				$ANo = explode('-',$last_row_sequence->OrderNumber);
				$lastOrderNo = date("y").substr($ANo[0],2); // Year based Order Fixed
			}else{
				$last_row = $this->db->select('OrderNumber,OrderUID')->where('IsChildOrder !=',1)->order_by('OrderUID',"DESC")->limit(1)->get('tOrders')->row();
		
				if(!empty($last_row)){
					if(strpos($last_row->OrderNumber, '-') !== false){
						$ANo = explode('-',$last_row->OrderNumber);
						$lastOrderNo = $ANo[0];
					}else{
						$lastOrderNo = $last_row->OrderNumber;
					}
		
				}
			}
	
			$db_2digitdate = substr($lastOrderNo, strlen($code), strlen($date));
			if($date == $db_2digitdate){
				$lastOrderNosliced = substr($lastOrderNo,(strlen($code)+strlen($date)));
				$id = sprintf("%06d",$lastOrderNosliced+1);
				$OrderNumber = $code.$date.$id;
			}else{
				$id = sprintf("%06d",1);
				$OrderNumber = $code.$date.$id;
	
			}
		
		return $OrderNumber;

	}

/* NF216 - Duplicate Order number restriction */
	function OrderSequence($SubProductUID,$PreviousOrderUID = NULL){
		if(!empty($SubProductUID)){
			$this->db->select('ProductCode');
			$this->db->from('mSubProducts');
			$this->db->join('mProducts','mProducts.ProductUID = mSubProducts.ProductUID');
			$this->db->where('SubProductUID', $SubProductUID);
			$query = $this->db->get();
			$result = $query->row();
			$code = $result->ProductCode;
		}else{
			$code = '0';
		}
		$date =  date("y");
		$id = sprintf("%06d",0);
		$lastOrderNo = $code.$date.$id;
		
		
		if(!empty($PreviousOrderUID)){
			$last_row_seq = $this->db->select('OrderSequence,OrderNumber,OrderUID')->where('OrderUID',$PreviousOrderUID)->get('tOrders')->row();
			$LastNo = explode('-',$last_row_seq->OrderNumber);
			$last_row_sequence = $this->db->select('OrderSequence,OrderNumber,OrderUID')->like('OrderSequence', substr($LastNo[0],1), 'both')->order_by('OrderUID',"DESC")->limit(1)->get('tOrders')->row();
			$ANo = explode('-',$last_row_sequence->OrderNumber);
			$lastOrderNo = date("y").substr($ANo[0],2); // Year based Order Fixed @Uba @On 30 Jul 2020
		}else{
			$last_row = $this->db->select('OrderNumber,OrderUID')->where('IsChildOrder !=',1)->order_by('OrderUID',"DESC")->limit(1)->get('tOrders')->row();
	
			if(!empty($last_row)){
				if(strpos($last_row->OrderNumber, '-') !== false){
					$ANo = explode('-',$last_row->OrderNumber);
					$lastOrderNo = $ANo[0];
				}else{
					$lastOrderNo = $last_row->OrderNumber;
				}
			}
		}
		$db_2digitdate =substr($lastOrderNo, strlen($code), strlen($date));
		if($date == $db_2digitdate){
			$lastOrderNosliced = substr($lastOrderNo,(strlen($code)+strlen($date)));
			$id = sprintf("%06d",$lastOrderNosliced+1);
			$OrderNumber = $date.$id;
		}else{
			$id = sprintf("%06d",1);
			$OrderNumber = $date.$id;
		}
		return $OrderNumber;
	}

	function get_all_in_customerproduct($CustomerUID,$ProductUID,$SubProductUID)
	{
		$this->db->select("*");
		$this->db->from('mCustomerProducts');
		$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID =mCustomerProducts.CustomerUID' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID =mCustomerProducts.ProductUID' , 'left' );
		$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = mCustomerProducts.SubProductUID' , 'left' );
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = mSubProducts.PriorityUID' , 'left' );
		$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = mSubProducts.OrderTypeUID' , 'left' );
		$this->db->where(array("mCustomerProducts.CustomerUID"=>$CustomerUID,"mCustomerProducts.ProductUID"=>$ProductUID,"mCustomerProducts.SubProductUID"=>$SubProductUID));
		$query = $this->db->get();
		return $query->row();
	}

	function get_all_in_customerproductsubproducts($CustomerUID,$ProductUID,$SubProductUID)
	{
		$this->db->select("*");
		$this->db->from('mCustomerProducts');
		$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID =mCustomerProducts.CustomerUID' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID =mCustomerProducts.ProductUID' , 'left' );
		$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = mCustomerProducts.SubProductUID' , 'left' );
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = mSubProducts.PriorityUID' , 'left' );
		$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = mSubProducts.OrderTypeUID' , 'left' );
		$this->db->where(array("mCustomerProducts.CustomerUID"=>$CustomerUID,"mCustomerProducts.SubProductUID"=>$SubProductUID));
		$query = $this->db->get();
		return $query->row();
	}


	function get_customer_product($CustomerUID,$ProductUID)
	{
		$this->db->select("*");
		$this->db->from('mCustomerProducts');
		$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID =mCustomerProducts.CustomerUID' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID =mCustomerProducts.ProductUID' , 'left' );
		$this->db->where(array("mCustomerProducts.CustomerUID"=>$CustomerUID,"mCustomerProducts.ProductUID"=>$ProductUID));
		$query = $this->db->get();
		return $query->row();
	}



	function GetSubproduct_By_Productandcustomer($CustomerUID,$ProductUID)
	{

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts) {
				$this->db->select("*");
				$this->db->from('mCustomerProducts');
				$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID =mCustomerProducts.SubProductUID' , 'left' );
				$this->db->where(array("mCustomerProducts.CustomerUID"=>$CustomerUID,"mCustomerProducts.ProductUID"=>$ProductUID));
				$this->db->where('mCustomerProducts.ProductUID IN ('.$UserProducts.')', null, false);
				$query = $this->db->get();
				$details = $query->result();
			}
		}else{

			$this->db->select("*");
			$this->db->from('mCustomerProducts');
			$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID =mCustomerProducts.SubProductUID' , 'left' );
			$this->db->where(array("mCustomerProducts.CustomerUID"=>$CustomerUID,"mCustomerProducts.ProductUID"=>$ProductUID));
			$query = $this->db->get();
			$details = $query->result();

		}

		$this->db->select("*");
		$this->db->from('mProjects');
		$this->db->where(array("mProjects.CustomerUID"=>$CustomerUID,"mProjects.ProductUID"=>$ProductUID));
		$query = $this->db->get();
		$Projects = $query->result();

		return array($details,$Projects);
	}


	function get_customer_byuid($CustomerUID){

		$this->db->where(array("Active"=>1,"CustomerUID"=>$CustomerUID));
		$query = $this->db->get('mCustomers');
		return $query->row();
	}

	function subproduct_by_customer_product($CustomerUID){

		$Products = [];
		$SubProducts = [];
		$Projects = [];
		/*FOR SUPERVISOR CHECK*/
		$productwhere= '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $productwhere .= ' AND `mProducts`.`ProductUID` IN ('.$UserProducts.')'; else: return array(); endif;
		}
		$query = $this->db->query("SELECT * FROM (`mCustomerProducts`) LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` =`mCustomerProducts`.`ProductUID` WHERE `mCustomerProducts`.`CustomerUID` = '".$CustomerUID."' $productwhere GROUP BY mProducts.ProductUID");
		$products  =  $query->result_array();

		return array($products,$SubProducts,$Projects);
	}


	function get_county_bystate($StateUID){
		$query = $this->db->query("SELECT * FROM mCounties WHERE StateUID = '".$StateUID."' AND Active = 1 ");
		return $query->result();
	}

	function SelectImportedData($OrderUID='')
	{
		if($OrderUID !='')
		{
			$this->db->select ( '*' );
			$this->db->from ( 'tOrders' );
			$this->db->join('mordertypes','mordertypes.OrderTypeUID = tOrders.OrderTypeUID','LEFT');
			$this->db->join('morderpriority','morderpriority.PriorityUID = tOrders.PriorityUID','LEFT');
			$this->db->join('mCustomers','mCustomers.CustomerUID = tOrders.CustomerUID','LEFT');
			$this->db->join('mSubProducts','mSubProducts.SubProductUID = tOrders.SubProductUID','LEFT');
			$this->db->join('mProducts','mProducts.ProductUID = mSubProducts.ProductUID','LEFT');
			$this->db->join('mtemplates','mtemplates.TemplateUID = tOrders.TemplateUID','LEFT');
			$this->db->join('tOrderPropertyRoles','tOrderPropertyRoles.OrderUID = tOrders.OrderUID','LEFT');
/* OrderUID array validation */
			if(is_array($OrderUID)){
				$this->db->where_in('tOrders.OrderUID',$OrderUID);
			}else{
				$this->db->where('tOrders.OrderUID',$OrderUID);
			}
			$this->db->group_by('tOrders.OrderUID');
			$query = $this->db->get();
			return $query->result();
		}
		else{
			echo "No Data Available";
		}
	}

	function GetPropertyrolesDetails($OrderUID='')
	{
		if($OrderUID !=''){
			$this->db->select ( '*' );
			$this->db->from ( 'tOrderPropertyRoles' );
			$this->db->where('OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->result_array();
		}
		else{
			echo "No Data Available";
		}
	}

	function find_is_duplicate($PropertyAddress1,$PropertyAddress2,$PropertyZipcode,$PropertyCityName,$PropertyCountyName,$PropertyStateCode,$LoanNumber, $SubProductUIDs, $ProductUIDs, $CustomerUID=0, $PropertyRoles, $OrderUID=0){


		if (empty($CustomerUID) || empty($SubProductUIDs) || empty($PropertyRoles) ) {
			return [];
		}


		$Address = strtoupper(trim(trim($PropertyAddress1).' '.trim($PropertyAddress2)));
		$res_array = [];


		$PropRoles_WHERE = [];
		foreach ($PropertyRoles as $key => $role) {
			$PropRoles_WHERE[] = "( tOrderPropertyRoles.PropertyRoleUID = '" . $role->PropertyRoleUID . "' AND tOrderPropertyRoles.PRName = '" . $role->PRName . "' )";
		}

		$PropRoleWHERE = !empty($PropRoles_WHERE) ? implode(" OR ", $PropRoles_WHERE) : "(tOrderPropertyRoles.OrderUID IS NULL)";
		$OrderUID_Filter = "";
		if (!empty($OrderUID)) {
			$OrderUID_Filter = "AND ( tOrders.OrderUID <> '".$OrderUID."' )";
		}

		$SubProductUIDs = !empty($SubProductUIDs) ? implode(", ", $SubProductUIDs) : "0";
		$ProductUIDs = !empty($ProductUIDs) ? implode(", ", $ProductUIDs) : "0";
		

		/* *** Extract Query From JSON begins *** */
		$conditions = [];

		$conditions["PropertyAddress"] = "(tOrders.PropertyZipcode = '$PropertyZipcode'
		AND tOrders.PropertyCityName = '$PropertyCityName'
		AND tOrders.PropertyStateCode = '$PropertyStateCode'
		AND tOrders.PropertyCountyName = '$PropertyCountyName'
		AND trim(CONCAT_WS(' ', UPPER(PropertyAddress1), UPPER(PropertyAddress2))) = '$Address'	)";

		$conditions["LoanNumber"] = "(tOrders.LoanNumber LIKE '$LoanNumber' AND tOrders.LoanNumber !='')";
		$conditions["SubProduct"] = "(tOrders.SubProductUID IN ($SubProductUIDs) )";
		$conditions["Product"] = "(mProducts.ProductUID IN ($ProductUIDs) )";
		$conditions["Customer"] = "(tOrders.CustomerUID = '$CustomerUID')";
		$conditions["OrderUID"] = "(tOrders.OrderUID <> '$OrderUID')";
		$conditions["PropertyRoles"] = $PropRoleWHERE;


		$morganizations = $this->common_model->get_row('morganizations', ['OrgUID'=>1]);

		$rules = [];
		if (!empty($morganizations) && !empty($morganizations->OrderEntryDuplicateRules)) {
			$rules = json_decode($morganizations->OrderEntryDuplicateRules, true);
		}

		if (empty($rules)) {
			return [];
		}

		$WHEREQuery = $this->common_model->extractQueryFromRules($rules, $conditions);

		if (empty($WHEREQuery)) {
			return [];
		}
		/* *** Extract Query From JSON ends *** */


		$query = $this->db->query(" SELECT tOrders.OrderUID,OrderNumber,CustomerNumber,PropertyStateCode,PropertyCountyName,PropertyCityName,PropertyZipcode,PropertyAddress1,PropertyAddress2,PropertyCountyName,tOrders.CustomerUID,CustomerName,tOrders.PriorityUID,PriorityName,tOrders.SubProductUID,SubProductName,ProductName,LoanNumber,DATE_FORMAT(OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDateTime,DATE_FORMAT(OrderCompleteDateTime, '%m-%d-%Y %H:%i:%s') as OrderCompleteDateTime,tOrders.StatusUID,StatusName,StatusColor,
			TRIM(CONCAT_WS(' ',TRIM(PropertyAddress1),TRIM(PropertyAddress2))) AS whole_name FROM tOrders
			JOIN mCustomers ON mCustomers.CustomerUID = tOrders.CustomerUID
			JOIN morderpriority ON morderpriority.PriorityUID = tOrders.PriorityUID
			JOIN mSubProducts ON mSubProducts.SubProductUID = tOrders.SubProductUID
			JOIN mProducts ON mProducts.ProductUID = mSubProducts.ProductUID
			JOIN morderstatus ON morderstatus.StatusUID = tOrders.StatusUID
			JOIN tOrderPropertyRoles ON tOrderPropertyRoles.OrderUID = tOrders.OrderUID
			WHERE 
			".$WHEREQuery."
			GROUP BY tOrders.OrderUID");
		return $query->result();

	}

	function findduplicate($PropertyAddress1,$PropertyAddress2,$PropertyZipcode,$PropertyCityName,$PropertyCountyName,$PropertyStateCode,$LoanNumber, $SubProductUIDs, $CustomerUID=0, $PropertyRoles, $ProductUIDs, $OrderUID=0){


		$Address = strtoupper(trim(trim($PropertyAddress1).' '.trim($PropertyAddress2)));
		$res_array = [];


		$PropRoles_WHERE = [];
		foreach ($PropertyRoles as $key => $role) {
			$PropRoles_WHERE[] = "( tOrderPropertyRoles.PropertyRoleUID = '" . $role->PropertyRoleUID . "' AND tOrderPropertyRoles.PRName = '" . $role->PRName . "' )";
		}

		$PropRoleWHERE = !empty($PropRoles_WHERE) ? implode(" OR ", $PropRoles_WHERE) : "(tOrderPropertyRoles.OrderUID IS NULL)";

		$OrderUID_Filter = "";
		if (!empty($OrderUID)) {
			$OrderUID_Filter = "AND ( tOrders.OrderUID <> '".$OrderUID."' )";
		}

		$SubProductUIDs = !empty($SubProductUIDs) ? implode(", ", $SubProductUIDs) : "0";
		$ProductUIDs = !empty($ProductUIDs) ? implode(", ", $ProductUIDs) : "0";
		

		/* *** Extract Query From JSON begins *** */
		$conditions = [];

		$conditions["PropertyAddress"] = "(tOrders.PropertyZipcode = '$PropertyZipcode'
		AND tOrders.PropertyCityName = '$PropertyCityName'
		AND tOrders.PropertyStateCode = '$PropertyStateCode'
		AND tOrders.PropertyCountyName = '$PropertyCountyName'
		AND trim(CONCAT_WS(' ', UPPER(PropertyAddress1), UPPER(PropertyAddress2))) = '$Address'	)";

		$conditions["LoanNumber"] = "(tOrders.LoanNumber LIKE '$LoanNumber' AND tOrders.LoanNumber !='')";
		$conditions["SubProduct"] = "(tOrders.SubProductUID IN ($SubProductUIDs) )";
		$conditions["Product"] = "(mProducts.ProductUID IN ($ProductUIDs) )";
		$conditions["Customer"] = "(tOrders.CustomerUID = '$CustomerUID')";
		if (!empty($OrderUID)) {
			$conditions["OrderUID"] = "(tOrders.OrderUID <> '$OrderUID')";			
		}
		$conditions["PropertyRoles"] = $PropRoleWHERE;


		$morganizations = $this->common_model->get_row('morganizations', ['OrgUID'=>1]);

		$rules = [];
		if (!empty($morganizations) && !empty($morganizations->OrderSummaryDuplicateRules)) {
			$rules = json_decode($morganizations->OrderSummaryDuplicateRules, true);
		}

		if (empty($rules)) {
			return [];
		}

		$WHEREQuery = $this->common_model->extractQueryFromRules($rules, $conditions);
		if (empty($WHEREQuery)) {
			return [];
		}
		/* *** Extract Query From JSON ends *** */


		$query = $this->db->query(" SELECT tOrders.OrderUID,OrderNumber,CustomerNumber,PropertyStateCode,PropertyCountyName,PropertyCityName,PropertyZipcode,PropertyAddress1,PropertyAddress2,PropertyCountyName,tOrders.CustomerUID,CustomerName,tOrders.PriorityUID,PriorityName,tOrders.SubProductUID,SubProductName,ProductName,LoanNumber,DATE_FORMAT(OrderEntryDateTime, '%m-%d-%Y %H:%i:%s') as OrderEntryDateTime,DATE_FORMAT(OrderCompleteDateTime, '%m-%d-%Y %H:%i:%s') as OrderCompleteDateTime,tOrders.StatusUID,StatusName,StatusColor,
			TRIM(CONCAT_WS(' ',TRIM(PropertyAddress1),TRIM(PropertyAddress2))) AS whole_name FROM tOrders
			JOIN mCustomers ON mCustomers.CustomerUID = tOrders.CustomerUID
			JOIN morderpriority ON morderpriority.PriorityUID = tOrders.PriorityUID
			JOIN mSubProducts ON mSubProducts.SubProductUID = tOrders.SubProductUID
			JOIN mProducts ON mProducts.ProductUID = mSubProducts.ProductUID
			JOIN morderstatus ON morderstatus.StatusUID = tOrders.StatusUID
			JOIN tOrderPropertyRoles ON tOrderPropertyRoles.OrderUID = tOrders.OrderUID
			WHERE 
			".$WHEREQuery."
			GROUP BY tOrders.OrderUID");
		return $query->result();

	}


	function Getborrowername($OrderUID){
		$query = $this->db->query("SELECT GROUP_CONCAT(`tOrderPropertyRoles`.`PRName`) AS Borrower FROM tOrderPropertyRoles WHERE OrderUID=".$OrderUID." AND PropertyRoleUID = 5 ");
		return $query->row()->Borrower;

	}

	function Get_customer_details($CustomerUID){
		$query = $this->db->query("SELECT CustomerUID,CustomerName FROM mCustomers WHERE CustomerUID=".$CustomerUID." ");
		return $query->row();

	}

	// function CheckIsFlood($CustomerUID,$ProductUID){

	// 	$this->db->select ( '*' );
	// 	$this->db->from ( 'mCustomerApiInfo' );
	// 	$custom_where = array('CustomerUID'=>$CustomerUID,'ProductUID'=>$ProductUID,'OrderSourceName'=>'WoltersKluwer');
	// 	$this->db->where($custom_where);
	// 	$query = $this->db->get();
	// 	if($query->num_rows() > 0){
	// 		return 1;
	// 	}else{
	// 		return 0;
	// 	}

	// }

	function Get_OrderNumber($OrderUID)
	{
		$this->db->select("OrderNumber");
		$this->db->from('tOrders');
		$this->db->where(array("tOrders.OrderUID"=>$OrderUID));
		$query = $this->db->get();
		return $query->row()->OrderNumber;
	}

	function generate_map($OrderUID){

		$this->load->library('googlemaps');
		$this->load->library('googlestaticmap');
		$this->load->library('googlestaticmapmarker');

		$apikey = $this->common_model->get_mapapi_key();
		$OrderNo = $this->Get_OrderNumber($OrderUID);
		if($apikey){

			$addr = $this->common_model->GetAddressDetails($OrderUID);
			$Address = $addr->Address;

			$map = $this->common_model->GetMapAddress($OrderUID);
			$mapAddress = $map->Address;

			$address = $Address;

			$config['center'] = $address;
			$config['zoom'] = 15;
			$config['apiKey'] = $apikey;
			$this->googlemaps->initialize($config);

			$marker = array();
			$marker['position'] = $address;
			$this->googlemaps->add_marker($marker);


			$data['map'] = $this->googlemaps->create_map();
			$lat = $data['map']['markers']['marker_0'] ['latitude'];
			$lng = $data['map']['markers']['marker_0'] ['longitude'];


			$oStaticMap = new GoogleStaticMap();
			$oStaticMap->{"sAPIKey"} = $apikey;


			$oStaticMap->setCenter($mapAddress)
			->setHeight(300)
			->setWidth(640)
			->setZoom(15)
			->setFormat('png');

			$oStaticMap->setMarker(array(
				'longitude' =>$lng,
				'latitude' => $lat
			));

			$oMarker = new GoogleStaticMapMarker();
			$oMarker->setColor('red')
			->setSize('large')
			->setLongitude($lng)
			->setLatitude($lat)
			->setLabel('B');

			$MapAddress = $oStaticMap->setMarker($oMarker);

			$src = $MapAddress;
			$time = time();

			$desFolder = FCPATH.'uploads/maps/';

			$imageName = $OrderNo.'.PNG';

			if (!is_dir($desFolder)) {
				mkdir($desFolder, 0777, true);
				chmod($desFolder, 0777);
			}

			if (file_exists($imageName))
			{
				return false;
			}
			else
			{
				$imagePath = $desFolder.$imageName;
				file_put_contents($imagePath,file_get_contents($src));
			}

		}

		return true;
	}

	function savetext_bulkentryorders_Assignment($torder,$torderpropdatas,$torderimports,$files,$torderinfo){
		$insertpropdata = [];
		$insertimportdata = [];

		//D-2-T23 state issuer fetched
		$insertimportdata['Issuer'] = $this->lang->line('Inhouse_addtable');
		$searchmoderesult = $this->common_model->get_statesearchmode($torder->PropertyStateCode,$torder->PropertyCountyName);
		if(!empty($searchmoderesult)) {
			$insertimportdata['Issuer'] = (!empty($searchmoderesult->SearchModeName)) ? $searchmoderesult->SearchModeName : $this->lang->line('Inhouse_addtable');
		}

		$torder->StatusUID = $this->config->item('keywords')['New Order'];
		$torder->OrderEntryDatetime = Date('Y-m-d H:i:s',strtotime("now"));
		$torder->LastTouchDateTime = Date('Y-m-d H:i:s',strtotime("now"));
		$torder->LastModifiedByUserUID = $this->session->userdata('UserUID');

		$pricing = new Customer_pricing();
		// $torder->CustomerAmount = $pricing->get_Customer_Pricings($torder);
		// $torder->CustomerActualAmount = $torder->CustomerAmount;
		/*@Desc Customer Pricing Update D2T-540 @Author Jainulabdeen @Updated May 21 2020*/
		$CustomerAmountQuote = $pricing->get_Customer_Pricings_Quote($this);
		$torder->CustomerAmount = $CustomerAmountQuote->Pricing;
		$torder->CustomerActualAmount = $torder->CustomerAmount;
		$torder->IsQuote = $CustomerAmountQuote->IsQuote;
		/*End*/

		$mStates=$this->db->get_where('mStates', array('StateCode' => $torder->PropertyStateCode))->row();

		$mCounties=$this->db->get_where('mCounties', array('StateUID'=>$mStates->StateUID, 'CountyName'=>$torder->PropertyCountyName))->row();

		if ($mStates->SearchType==1) {
			$torder->IsInhouseExternal=0;
		}
		elseif ($mStates->SearchType==2) {
			$torder->IsInhouseExternal=1;
		}
		else{
			if ($mStates->AbstractorAssignment==1) {
				$torder->IsInhouseExternal = $this->common_model->get_inhouse_external($torder->OrderTypeUID,$mStates->StateUID,$mCounties->CountyUID);
			}
			else{
				$torder->IsInhouseExternal = $this->common_model->get_inhouse_external_forzipcode($torder->OrderTypeUID,$mStates->StateUID,$torder->PropertyZipcode);
			}
		}


		$torder->OrderDueDateTime = calculate_duedate($torder->OrderEntryDatetime,$torder->CustomerUID,$torder->SubProductUID,$torder->PriorityUID);
		$this->db->trans_begin();
		$torder->OrderNumber = $this->Order_Number($torder->SubProductUID);
		/* NF216 - Duplicate Order number restriction */
		$torder->OrderSequence = $this->OrderSequence($torder->SubProductUID);
		$date = date('Ymd');
		$torder->OrderDocsPath ='uploads/searchdocs/'.$date.'/'.$torder->OrderNumber.'/';

		/* desc: Insert Order Policy Type 
		** author: Yagavi G <yagavi.g@avanzegroup.com>
		** since: Aug 7th 2020
		*/
		$where_policy_type['SubProductUID'] = $torder->SubProductUID;
		$where_policy_type['CustomerUID'] = $torder->CustomerUID;
		$torder->OrderPolicyType = $this->common_model->GetCustomerProductDetailsByCustomerSubproductUID($where_policy_type)->OrderPolicyType;

		$query = $this->db->insert('tOrders',$torder);

		$lastinsertid = $this->db->insert_id();

		/*INSERT CUSTOMER ACTUAL PRICING*/
		$this->common_model->insert_customerpaymentdata($lastinsertid);

		foreach ($torderpropdatas as $torderpropdata) {
			if(!empty($torderpropdata['PRName']) && trim($torderpropdata['PRName']) != ''){
				$insertpropdata[] = array(
					'PropertyRoleUID'      => $torderpropdata['PropertyRoleUID'],
					'PRName'          => $torderpropdata['PRName'],
					'OrderUID'          => $lastinsertid,
				);
			}
		}

		if(!empty($insertpropdata)){
			$this->db->insert_batch('tOrderPropertyRoles', $insertpropdata);
		}


		foreach ($torderimports as $torderimport) {
			$insertimportdata['ServicerLoanNumber'] = $torderimport['ServicerLoanNumber'];
			$insertimportdata['AlternateReferenceNumber'] = $torderimport['AlternateReferenceNumber'];
			$insertimportdata['OriginationDate'] = $torderimport['OriginationDate'];
			$insertimportdata['ModBalance'] = $torderimport['ModBalance'];
			$insertimportdata['ModDate'] = $torderimport['ModDate'];
			$insertimportdata['OrderUID'] = $lastinsertid;
		}


		if(!empty($insertimportdata)){
			$this->db->insert('tOrderImport', $insertimportdata);
		}

		if(!empty($torderinfo)){
			$Borrowernames = array_column($torderpropdatas, 'PRName');
			$torderinfo->OrderUID = $lastinsertid;
			$torderinfo->PropertyAddress1 = $torder->PropertyAddress1;
			$torderinfo->PropertyCountyName = $torder->PropertyCountyName;
			$torderinfo->PropertyCityName = $torder->PropertyCityName;
			$torderinfo->PropertyStateCode = $torder->PropertyStateCode;
			$torderinfo->PropertyZipCode = $torder->PropertyZipcode;
			$torderinfo->BorrowerName = join(' AND ', array_filter(array_merge(array(join(', ', array_slice($Borrowernames, 0, -1))), array_slice($Borrowernames, -1)), 'strlen'));
			$this->db->insert('torderinfo', $torderinfo);
		}


        $OrderNumber=$torder->OrderNumber;
        $OrderUID=$lastinsertid;

        $date = date('Ymd');
        $Path = FCPATH .'uploads/searchdocs/'.$date.'/'.$OrderNumber.'/';
        $viewPath = 'uploads/searchdocs/'.$date.'/'.$OrderNumber.'/';

        if (!is_dir($Path)) {
          mkdir($Path, 0777, true);
        }

		$LoanNumber = $torder->LoanNumber;

		if(isset($files)){

			foreach ($files['name'] as $key => $value) {
				$dotposition = strripos($value, '.');
				$documentname = substr($value, 0, $dotposition);

				if (strpos(strtolower($documentname), strtolower($LoanNumber)) !== false) {
					$this->NormalFileUpload($files['tmp_name'][$key], $Path . $value, $OrderUID);

					    $tDocuments = array(
									    	'OrderUID'=>$OrderUID,
									    	'DocumentFileName'=>$value,
										    'DisplayFileName'=>$value,
										    'UploadedDate'=>date('Y-m-d H:i:s'),
										    'DocumentCreatedDate'=>date('Y-m-d H:i:s'),
										    'UploadedUserUID'=>$this->loggedid
										);

					    $this->db->insert('torderdocuments', $tDocuments);
				}
			}
		}

		/* D-2-T9 GENERATE PACKAGE NUMBER BASED ON LOAN NUMBER*/
		$this->common_model->save_package_number($OrderUID,$torder->LoanNumber);

		/*-----Generate Map Start---*/
		$this->generate_map($lastinsertid);
		/*-----Generate Map End---*/

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return $lastinsertid;;
		}

	}

	function Gettorderimportdata($OrderUID='')
	{
		if($OrderUID !='')
		{

			$this->db->select ( '*' );
			$this->db->from ( 'tOrderImport' );
			$this->db->where('tOrderImport.OrderUID',$OrderUID);
			$this->db->group_by('tOrderImport.OrderUID');
			$query = $this->db->get();
			return $query->row();
		}
		else{
			echo "No Data Available";
		}
	}


	public function NormalFileUpload($File, $PATH, $OrderUID)
	{
		if (is_uploaded_file($File)) {
			if (move_uploaded_file($File, $PATH)) {
				return true;
			}

		}
		return false;
	}

	/*ASSIGNMENT BULK ENTRY ASSIGNOR,ASSIGNEE AND ENDORSER*/
	function exists_Assignor_Name($fieldname){
		$query = $this->db->query("SELECT EXISTS(SELECT * FROM `mAssignor` WHERE `AssignorName` LIKE '%".$fieldname."%' OR `AssignorPrintName` LIKE '%".$fieldname."%' LIMIT 1) AS fieldexists ");
		return $query->row()->fieldexists;
	}

	function exists_Assignee_Name($fieldname){
		$query = $this->db->query("SELECT EXISTS(SELECT * FROM `mAssignee` WHERE `AssigneeName` LIKE '%".$fieldname."%' OR `AssigneePrintName` LIKE '%".$fieldname."%' LIMIT 1) AS fieldexists ");
		return $query->row()->fieldexists;
	}

	function exists_Endorser_Name($fieldname){
		$query = $this->db->query("SELECT EXISTS(SELECT * FROM `mEndorser` WHERE `EndorserName` LIKE '%".$fieldname."%' OR `EndorserPrintName` LIKE '%".$fieldname."%' LIMIT 1) AS fieldexists ");
		return $query->row()->fieldexists;
	}

	function get_Assignor_Name($fieldname){
		$query = $this->db->query("SELECT * FROM `mAssignor` WHERE `AssignorName` LIKE '%".$fieldname."%' OR `AssignorPrintName` LIKE '%".$fieldname."%' LIMIT 1");
		return $query->row();
	}

	function get_Assignee_Name($fieldname){
		$query = $this->db->query("SELECT * FROM `mAssignee` WHERE `AssigneeName` LIKE '%".$fieldname."%' OR `AssigneePrintName` LIKE '%".$fieldname."%' LIMIT 1");
		return $query->row();
	}

	function get_Endorser_Name($fieldname){
		$query = $this->db->query("SELECT * FROM `mEndorser` WHERE `EndorserName` LIKE '%".$fieldname."%' OR `EndorserPrintName` LIKE '%".$fieldname."%' LIMIT 1");
		return $query->row();
	}

	function Gettorderinfodata($OrderUID='')
	{
		if($OrderUID !='')
		{
			$this->db->select ( 'EndorserName,AssigneeName,AssignorName' );
			$this->db->from ( 'torderinfo' );
			$this->db->where('torderinfo.OrderUID',$OrderUID);
			$this->db->group_by('torderinfo.OrderUID');
			$query = $this->db->get();
			return $query->row();
		}
		else{
			echo "No Data Available";
		}
	}
	function GetPropertyrolesBorrowerDetails($OrderUID)
	{
		$this->db->select ( '*' );
		$this->db->from ( 'tOrderPropertyRoles' );
		$this->db->where('OrderUID',$OrderUID);
		$this->db->where('PropertyRoleUID',$this->config->item('Propertyroles')['Borrowers']);
		$query = $this->db->get();
		return $query->result_array();
	}

	function GetPropertyrolesSellerDetails($OrderUID)
	{
			$this->db->select ( '*' );
			$this->db->from ( 'tOrderPropertyRoles' );
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('PropertyRoleUID',$this->config->item('Propertyroles')['Sellers']);
			$query = $this->db->get();
			return $query->result_array();
	}

	/*For fee upload check*/
/* NF203 - Fee Upload Under Order Entry Screen */
	function isexist_ordernumber($OrderNumber){
		$query = $this->db->query("SELECT * FROM `tOrders` WHERE OrderNumber = '".$OrderNumber."' ");
		return $query->row();
	}

/* NF203 - Check Alternate Order Number */
	function isexist_altordernumber($AltORderNumber){
		$query = $this->db->query("SELECT * FROM `tOrders` WHERE AltORderNumber = '".$AltORderNumber."' ");
		return $query->row();
	}

	function isexist_abstractor($OrderUID,$AbstractorNo,$AbstractorName,$OrderTypeName){
		$query = $this->db->query("SELECT * FROM `torderabstractor` JOIN mordertypes ON mordertypes.OrderTypeUID = torderabstractor.OrderTypeUID JOIN mabstractor ON mabstractor.AbstractorUID = torderabstractor.AbstractorUID WHERE OrderUID =  '".$OrderUID."' AND ( AbstractorNo LIKE '%".$AbstractorNo."%' AND CONCAT_WS(' ',AbstractorFirstName,AbstractorLastName) LIKE '%".$AbstractorName."%' AND OrderTypeName LIKE '%".$OrderTypeName."%') ");
		return $query->row();
	}

	function check_feeuploadduplicate($Orderrow){
		if(!empty($Orderrow)){
			$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM cOrderFeeUpload WHERE OrderUID = '".$Orderrow->OrderUID."' ) as duplicate");
			//$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM cOrderFeeUpload WHERE OrderUID = '".$Orderrow->OrderUID."' AND OrderNumber = '".$Orderrow->OrderNumber."' AND AltOrderNumber =  '".$Orderrow->AltOrderNumber."' ) as duplicate");
			return $query->row()->duplicate;
		}else{
			return 0;
		}
	}

	function check_pricingadjustments($OrderUID,$arrayvalue,$columnvariables,$Abstractordetail){

		if($OrderUID != false && $arrayvalue[$columnvariables['AbstractorNumber']] != '' || $arrayvalue[$columnvariables['AbstractorName']] != '' && $arrayvalue[$columnvariables['CustomerFee']] != ''){

			//if customer fee and abstractor fee 
			if(!empty($Abstractordetail)) {
				$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM tOrderPayments WHERE OrderUID = '".$OrderUID."' AND ((ApprovalFunction IN ('AbstractorPricingOverride','AbstractorPricingAdjustments','CustomerPricingAdjustments') && AbstractorOrderUID = '".$Abstractordetail->AbstractorOrderUID."') OR ApprovalFunction IN(".$this->config->item('pricing_customer_Approvalfunction')." )) ) as avail");
				return $query->row()->avail;
			}

			return 0;
		}else if($OrderUID != false && $arrayvalue[$columnvariables['AbstractorNumber']] != '' || $arrayvalue[$columnvariables['AbstractorName']] != ''){
			//if abstractor fee 
			if(!empty($Abstractordetail)) {
				$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM tOrderPayments WHERE OrderUID = '".$OrderUID."' AND ApprovalFunction IN ('AbstractorPricingOverride','AbstractorPricingAdjustments') AND AbstractorOrderUID = '".$Abstractordetail->AbstractorOrderUID."' ) as avail");
				return $query->row()->avail;
			}

			return 0;

		}else if($OrderUID != false && $arrayvalue[$columnvariables['CustomerFee']] != ''){
			//if customer fee only 
			$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM tOrderPayments WHERE OrderUID = '".$OrderUID."' AND ApprovalFunction  IN ('CustomerPricingOverride','CustomerPricingAdjustments') ) as avail");
			return $query->row()->avail;

		}

		return 0;
	}

	function check_isbilled($OrderUID){
		$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM tOrders WHERE OrderUID = '".$OrderUID."' AND IsBilled IN (1, 2)) as billed");
		return $query->row()->billed;
	}

	function check_iscompleted($OrderUID)
	{
		$this->db->where('OrderUID', $OrderUID);	
		$this->db->where('StatusUID',100);
		return $this->db->get('tOrders')->num_rows();
	}

/* NF203 - Insert fee upload data */
	function save_feeuploaddata($data){
		$this->db->trans_begin();

		$OrderUID = $data['OrderUID'];
		
		//save abstractorpricing
		if(!empty($data['AbstractorOrderUID']) && !empty($data['AbstractorAmount']) && empty($data['OnlyComplete'])){

			/*Remove all existing approvals*/
			$this->common_model->decline_abstractorapprovals($OrderUID,$data['AbstractorOrderUID']);

			$fieldArray = array(
				"AbstractorActualFee"=> $data['AbstractorAmount'],
				"AbstractorFee"=> $data['AbstractorAmount'],
				"AbstractorAdditionalFee"=> 0,
				"AbstractorCopyCost"=> 0,
				"IsQuote"=> 0,
			);
			$this->db->where('AbstractorOrderUID',$data['AbstractorOrderUID']);
			$this->db->where('OrderUID',$OrderUID);
			$this->db->update('torderabstractor',$fieldArray);


			$fieldArray = array(
				"AbstractorActualFee"=> $data['AbstractorAmount'],
				"AbstractorFee"=> $data['AbstractorAmount'],
				"AbstractorAdditionalFee"=> 0,
				"AbstractorCopyCost"=> 0,
				"ModifiedByUserUID"=> $this->session->userdata('UserUID'),
				"ModifiedByUserUID"=> date('Y-m-d H:i:s')
			);
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('AbstractorOrderUID',$data['AbstractorOrderUID']);
			$this->db->where('ApprovalFunction','AbstractorActualPricing');
			$this->db->update('tOrderPayments',$fieldArray);

			/*NF203 - updating all abstractor fee in tOrders*/
			$query = $this->db->query("SELECT (SELECT COALESCE(SUM(t.AbstractorActualFee),0) FROM torderabstractor t WHERE t.OrderUID = $OrderUID)
				+
				(SELECT COALESCE(SUM(u.AbstractorActualFee),0) FROM torderabstractorunassign u WHERE u.OrderUID = $OrderUID AND IsFeeAdjusted = 1) AS TotalActualAbstractorFee,

				(SELECT COALESCE(SUM(t.AbstractorCopyCost),0) FROM torderabstractor t WHERE t.OrderUID = $OrderUID)
				+
				(SELECT COALESCE(SUM(u.AbstractorCopyCost),0) FROM torderabstractorunassign u WHERE u.OrderUID = $OrderUID AND IsFeeAdjusted = 1) AS TotalAbstractorCopyCost,

				(SELECT COALESCE(SUM(CASE WHEN t.OperatorType = '-' THEN  t.AbstractorAdditionalFee*-1 ELSE t.AbstractorAdditionalFee END ),0) FROM torderabstractor t WHERE t.OrderUID = $OrderUID)
				+
				(SELECT COALESCE(SUM(CASE WHEN u.OperatorType = '-' THEN  u.AbstractorAdditionalFee*-1 ELSE u.AbstractorAdditionalFee END ),0) FROM torderabstractorunassign u WHERE u.OrderUID = $OrderUID AND IsFeeAdjusted = 1) AS TotalAbstractorAdditionalFee,

				(SELECT COALESCE(SUM(CASE WHEN t.OperatorType = '-' THEN (t.AbstractorActualFee+t.AbstractorCopyCost)-t.AbstractorAdditionalFee ELSE t.AbstractorAdditionalFee+(t.AbstractorActualFee+t.AbstractorCopyCost) END),0) FROM torderabstractor t WHERE t.OrderUID = $OrderUID)
				+
				(SELECT COALESCE(SUM(CASE WHEN u.OperatorType = '-' THEN (u.AbstractorActualFee+u.AbstractorCopyCost)-u.AbstractorAdditionalFee ELSE u.AbstractorAdditionalFee+(u.AbstractorActualFee+u.AbstractorCopyCost) END),0) FROM torderabstractorunassign u WHERE u.OrderUID = $OrderUID AND IsFeeAdjusted = 1) AS TotalAbstractorFee

				");

			$TotalAbstractorFees =  $query->row();


			if(!empty($TotalAbstractorFees)){

				$this->db->where(array('OrderUID' => $OrderUID ));
				$this->db->update('tOrders', array('AbstractorFee'=>$TotalAbstractorFees->TotalAbstractorFee, 'AbstractorCopyCost'=>$TotalAbstractorFees->TotalAbstractorCopyCost, 'AbstractorActualFee'=>$TotalAbstractorFees->TotalActualAbstractorFee, 'AbstractorAdditionalFee'=>$TotalAbstractorFees->TotalAbstractorAdditionalFee));

			}

		}

	/* DI1-T2 New column added in excel sheet */
    if(strtoupper($data['ToBeComplete']) == 'YES')
    {
      $update['OrderCompleteDateTime'] = date('Y-m-d H:i:s');
      $update['LastModifiedByUserUID'] = 768;
      $update['StatusUID'] = 100;
      $this->db->where(array('OrderUID' => $OrderUID ));
      $this->db->update('tOrders', $update);

      //insert sla action details for completed start
      $this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['Completed'],$this->session->userdata('UserUID'));
	 //insert sla action details end
      
      $assign['WorkflowStatus'] = 5;
      $assign['CompleteDateTime'] = date('Y-m-d H:i:s');
      $this->db->where('OrderUID', $OrderUID);
      $this->db->where('WorkflowStatus <> ', 5);
      $this->db->update('torderassignment', $assign);
    }
      
		//save customer pricing
		if(!empty($data['SubscriberFee']) && empty($data['OnlyComplete'])){
			/*Remove all existing approvals*/
      $this->common_model->decline_customerapprovals($OrderUID);

			$fieldArray = array(
				"CustomerActualAmount"=> $data['SubscriberFee'],
				"CustomerAmount"=> $data['SubscriberFee'],
				"CustomerAdditionalAmount"=> 0,
			);

			$this->db->where('OrderUID',$OrderUID);
			$query = $this->db->update('tOrders',$fieldArray);

			$fieldArray = array(
				"CustomerActualAmount"=> $data['SubscriberFee'],
				"CustomerAmount"=> $data['SubscriberFee'],
				"CustomerAdditionalAmount"=> 0,
				"ModifiedByUserUID"=> $this->session->userdata('UserUID'),
				"ModifiedByUserUID"=> date('Y-m-d H:i:s')

			);
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('ApprovalFunction','CustomerActualPricing');
			$query = $this->db->update('tOrderPayments',$fieldArray);
		}

		/*bill order*/
		if(ucfirst($data['ToBeBilled']) == 'Yes'){
			$fieldArray = array(
				"IsBilled"=> 1,
				"BillingDateTime"=>date('Y-m-d H:i:s'),
				"BilledbyUserUID"=>$this->session->userdata('UserUID'),
			);

			$this->db->where('OrderUID',$OrderUID);
			$query = $this->db->update('tOrders',$fieldArray);

			//insert sla action details for Billed start
			$this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['Billed'],$this->session->userdata('UserUID'));
		    //insert sla action details end

			/*CHECK if order is Inserted in torderpayments*/

				
			/* @Desc Bill all assigned vendors
			@author Parthasarathy
			@Created on May 07 2020
			*/

			$this->db->select('torderabstractor.AbstractorOrderUID');
			$this->db->from('torderabstractor');
			$this->db->where('torderabstractor.OrderUID', $OrderUID);
			$this->db->where('(IsVendorBilled = 0 OR IsVendorBilled IS NULL)');
			$abstractortobilled = $this->db->get()->result();
		
			
			foreach ($abstractortobilled as $key => $value) {

				$torderabstractordata['IsVendorBilled'] = 1;
				$torderabstractordata['VendorBilledDateTime'] = date('Y-m-d H:i:s');
				$torderabstractordata['VendorBilledByUserUID'] = $this->loggedid;
				$this->db->where('AbstractorOrderUID', $value->AbstractorOrderUID);
				$this->db->where('(IsVendorBilled = 0 OR IsVendorBilled IS NULL)');
				$this->db->update('torderabstractor', $torderabstractordata);

				$this->load->model('order_search/ordersearch_model');
				$abstractordetails = $this->ordersearch_model->getAssignedAbstractorDetails($value->AbstractorOrderUID);
				$AbstractorName = !empty($abstractordetails->AbstractorCompanyName) ? $abstractordetails->AbstractorCompanyName : $abstractordetails->AbstractorFirstName . " " . $abstractordetails->AbstractorLastName;


				$Auditdata['Content']= '<b>' .  $AbstractorName . '</b> Vendor Billed';
				$Auditdata['ModuleName']='Abstractor Document';
				$Auditdata['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
				$Auditdata['DateTime']=date('Y-m-d H:i:s');
				$Auditdata['TableName']='torderabstractor';
				$Auditdata['OrderUID']=$OrderUID;
				$Auditdata['UserUID']=$this->session->userdata('UserUID');                
				$this->common_model->Audittrail_insert($Auditdata);

			}


			$this->common_model->insert_customerpaymentdata($OrderUID);
			$this->common_model->insert_abstractorpaymentdata($OrderUID);

			$this->common_model->update_billed_payments($OrderUID);
		}

		/*NF203 - save `cOrderFeeUpload` */
		$insertdata = new StdClass();
		$insertdata->OrderUID = $data['OrderUID'];
		$insertdata->OrderNumber = $data['OrderNumber'];
		$insertdata->AltORderNumber = $data['AltORderNumber'];
		$insertdata->AbstractorNo = $data['AbstractorNo'];
		$insertdata->AbstractorName = $data['AbstractorName'];
		$insertdata->AssignmentType = $data['AssignmentType'];
		$insertdata->AbstractorAmount = $data['AbstractorAmount'];
		$insertdata->SubscriberFee = $data['SubscriberFee'];
		$insertdata->ToBeBilled = $data['ToBeBilled'];
		$insertdata->ToBeComplete = $data['ToBeComplete'];
		$insertdata->UploadedDateTime = date('Y-m-d H:i:s');
		$insertdata->UploadedByUserUID = $this->session->userdata('UserUID');
		$this->db->insert('cOrderFeeUpload',$insertdata);
		$FeeuploadUID = $this->db->insert_id();


		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}else{
			$this->db->trans_commit();
			return $FeeuploadUID;
		}
	}

	function get_feeuploadimported($OrderUID){
		$this->db->select('tOrders.OrderUID,tOrders.OrderNumber,tOrders.OrderEntryDatetime,tOrders.OrderCompleteDateTime,mSubProducts.SubProductCode,mCustomers.CustomerPContactName,tOrders.CustomerUID,tOrders.LoanNumber,mCustomers.CustomerName,mCustomers.CustomerNumber,GROUP_CONCAT(DISTINCT tOrderPropertyRoles.PRName) as PRName,tOrders.StatusUID,morderstatus.StatusName,morderstatus.StatusName,morderstatus.StatusColor,tOrders.PropertyAddress1,tOrders.PropertyAddress2,tOrders.PropertyCityName,tOrders.PropertyCountyName,tOrders.PropertyStateCode,tOrders.PropertyZipcode,tOrders.CustomerAmount,mordertypes.OrderTypeName,tOrders.AbstractorCopyCost,tOrders.AbstractorFee,tOrders.AbstractorAdditionalFee,tOrders.MailSendBy,mCustomers.CustomerPContactEmailID,tOrders.MailSendBy,mSubProducts.SubProductName,mUsers.UserName as OrderBilledUserName, tOrders.OrderDueDatetime,tOrders.OrderDueDatetime,tApiOrders.TransactionID,tOrders.AltORderNumber,tOrders.CustomerRefNum,tOrders.IsBilled');
		$this->db->select("CASE WHEN tOrders.StatusUID=100 THEN tOrders.OrderCompleteDateTime ELSE '' END as OrderCompleteDateTime",FALSE);  
    $this->db->select("CASE WHEN tOrders.StatusUID=100 THEN 'Yes' ELSE 'No' END as OrderComplete",FALSE);  
		$this->db->select("BillingDateTime as BillingDateTime",FALSE);  
		$this->db->select("COALESCE(HOUR(TIMEDIFF(OrderCompleteDateTime, OrderEntryDatetime)),0) as RetTime",FALSE);  
		$this->db->from('tOrders');
		$this->db->join('mCustomers','mCustomers.CustomerUID = tOrders.CustomerUID','LEFT');
		$this->db->join('tOrderPropertyRoles','tOrderPropertyRoles.OrderUID = tOrders.OrderUID','LEFT');
		$this->db->join('mSubProducts','mSubProducts.SubProductUID = tOrders.SubProductUID','LEFT');
		$this->db->join('morderstatus','morderstatus.StatusUID = tOrders.StatusUID','LEFT');
		$this->db->join('mordertypes','mordertypes.OrderTypeUID = tOrders.OrderTypeUID','LEFT'); 
		$this->db->join('torderabstractor','torderabstractor.AbstractorUID = tOrders.AbstractorUID','LEFT'); 
		$this->db->join('mabstractor','mabstractor.AbstractorUID = tOrders.AbstractorUID','LEFT'); 
		$this->db->join('mUsers','mUsers.UserUID = tOrders.BilledbyUserUID','LEFT');
		$this->db->join('tApiOrders','tApiOrders.OrderUID = tOrders.OrderUID','LEFT');
		$this->db->where('tOrders.StatusUID != 110',NULL,FALSE);  
		//$this->db->where('tOrders.IsBilled != 0',NULL,FALSE);
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->row();
	}

	public function IsClosingProduct($ProductUID)
	{
		$this->db->select('IsClosingProduct', false);
		$this->db->from('mProducts');
		$this->db->where('mProducts.ProductUID', $ProductUID);
		return $this->db->get()->row()->IsClosingProduct;

	}

	//function to check if selected customer has bundled subproduct
	// function get_bundleddetails()
	// {
 //    $this->db->select('GROUP_CONCAT(DISTINCT CustomerUID) AS CustomerUID , GROUP_CONCAT( DISTINCT CurrentProductUID) AS CurrentProductUID ')->from('mBundleSubProduct');
 //    return $this->db->get()->row();
	// }

	//function to check if loan numbert matching with the previous order
	function check_duplicateloanorder_exists($BundleSubProductUID,$LoanNumber) {
		$query = $this->db->query("SELECT OrderUID,OrderNumber FROM tOrders WHERE LoanNumber = '".$LoanNumber."'  AND SubProductUID = '".$BundleSubProductUID."' ORDER BY OrderUID DESC");
		return $query->row();
	}


	//function to return subproduct by matching the loan number with the previous orders 
	//if matched return currentsuproductuid else defaultsubproductuid
  // function get_bundled_customer_product_subproduct($post)
  // {

  //   $this->db->select('mBundleSubProduct.*')->from('mBundleSubProduct');
  //   $this->db->where('mBundleSubProduct.CustomerUID',$post['CustomerUID']);
  //   $this->db->where('mBundleSubProduct.CurrentProductUID',$post['ProductUID']);
  //   $bundleddetails = $this->db->get()->result();
  //   $len = count($bundleddetails);
  //   foreach ($bundleddetails as $key => $bundleddetail) {
  //     $isexist = $this->check_duplicateloanorder_exists($bundleddetail->BundleSubProductUID,$post['LoanNumber']); 
  //     if(!empty($isexist)) {

  //       return ['Order'=>$isexist,'SubProductUID'=>$bundleddetail->CurrentSubProductUID];
  //     } 

  //     //last loop
  //     if ($key == $len - 1) {
  //       return ['Order'=>$isexist,'SubProductUID'=>$bundleddetail->DefaultSubProductUID];
  //     }
  //   }

  //   return ['Order'=>'','SubProductUID'=>''];
  // }


  //bulkentry return subproductname for the matching loan number with the previous order 
  // function bulk_bundle_subproductmatch($post) {
  // 	$bundledoldorder = $this->Orderentry_model->get_bundled_customer_product_subproduct($post);
  // 	$bundledoldorder['SubProductName'] = '';
  // 	if(!empty($bundledoldorder)) {
  // 		if(!empty($bundledoldorder['SubProductUID'])) {
  // 			$SubProduct = $this->get_subproductname_byuid($bundledoldorder['SubProductUID']);
  // 			if(!empty($SubProduct)) {
  // 				$bundledoldorder['SubProductName'] = $SubProduct->SubProductName;
  // 			}
  // 		}

  // 	}
  // 	return $bundledoldorder;
  // }

  //function to get subproductname by subproductuid
  function get_subproductname_byuid($SubProductUID) {
  	$this->db->select('*');
  	$this->db->from('mSubProducts');
  	$this->db->where('mSubProducts.SubProductUID',$SubProductUID);
  	return $this->db->get()->row();
  }
//D-2-T2 Order Entry
	function get_productbyuid($ProductUID){
		$this->db->select("ProductUID,ProductName");
		$this->db->from('mProducts');
		$this->db->where(array("Active"=>1,"ProductUID"=>$ProductUID));
		return $this->db->get()->row();
	}

	function get_customerbyuid($CustomerUID){
		$this->db->select("CustomerName,CustomerUID");
		$this->db->from('mCustomers');
		$this->db->where(array("Active"=>1,"CustomerUID"=>$CustomerUID));
		return $this->db->get()->row();
	}

	function get_firstborrowerdetails($OrderUID)
	{
		$this->db->select ( '*' );
		$this->db->from ( 'tOrderPropertyRoles' );
		$this->db->where('OrderUID',$OrderUID);
		$this->db->where('PropertyRoleUID',$this->config->item('Propertyroles')['Borrowers']);
		$this->db->order_by('Id');
		$query = $this->db->get();
		return $query->row();
	}

	function reverse_savebulkentry($data,$files){
		$insertdata = new StdClass();
		$timportdata = new StdClass();
		$borrowerinsertdata = [];
		$mDocTypes = [];

		$insertdata->OrderTypeUID = $data['validation_OrderTypeUID'];
		$insertdata->PriorityUID = $data['validation_PriorityUID'];
		$insertdata->CustomerUID = $data['validation_CustomerUID'];
		$insertdata->SubProductUID = $data['validation_SubProductUID'];
		$insertdata->ProjectUID = $data['validation_ProjectUID'];

		$insertdata->AltORderNumber = $data['AlternateorderNumber'];
		$insertdata->LoanNumber = $data['LoanNumber'];
		$insertdata->LoanAmount = $data['LoanAmount'];
		$insertdata->CustomerRefNum = $data['CustomerReferenceNumber'];
		$insertdata->PropertyAddress1 = $data['PropertyAddress'];
		$insertdata->PropertyCityName = $data['PropertyCity'];
		$insertdata->PropertyCountyName = $data['PropertyCounty'];
		$insertdata->PropertyStateCode = $data['PropertyState'];
		$insertdata->PropertyZipcode = $data['PropertyZipCode'];
		$insertdata->APN = $data['APN'];
		$insertdata->EmailReportto = $data['EmailReportto'];
		$insertdata->AttentionName = $data['AttentionName'];

		if (isset($data['DocumentStatus'])) 
		{
			$insertdata->DocumentStatus = $data['DocumentStatus'];
		}

		if (isset($data['FHA'])) 
		{
			$timportdata->FHA = $data['FHA'];
		}


		//timport table data
		$timportdata->Investor = $data['Investor'];
		$timportdata->MCAAmount = $data['MCAAmount'];
		$timportdata->MCAPercentage = $data['MCA%'];
		$timportdata->MCABuckets = $data['MCABucket'];
		$timportdata->Agent = $data['Agent'];
		$timportdata->TitleCompany = $data['TitleCompany'];
		$timportdata->TitleUnderwriter = $data['TitleUnderwriter'];
		$timportdata->PolicyNumber = $data['PolicyNumber'];
		$timportdata->SearchOrderedDate = (!empty($data['SearchOrderDate'])) ? Date('Y-m-d H:i:s',strtotime($data['SearchOrderDate'])) : null;
		$timportdata->PolicyApproveDate = (!empty($data['PolicyApproveDate'])) ? Date('Y-m-d H:i:s',strtotime($data['PolicyApproveDate'])) : null;
		$timportdata->ClientKickBackDate = (!empty($data['ClientKickBackDate'])) ? Date('Y-m-d H:i:s',strtotime($data['ClientKickBackDate'])) : null;
		$timportdata->ClientKickBackComments = $data['ClientKickBackComments'];
		$timportdata->AdditionalNotes = $data['AdditionalInfo'];

		$insertdata->DocumentStatus = $data['DocumentStatus'];
		$insertdata->StatusUID = $this->config->item('keywords')['New Order'];
		$insertdata->OrderEntryDatetime = Date('Y-m-d H:i:s',strtotime("now"));
		$insertdata->LastTouchDateTime = Date('Y-m-d H:i:s',strtotime("now"));
		$insertdata->LastModifiedByUserUID = $this->session->userdata('UserUID');

		$pricing = new Customer_pricing();
		$insertdata->CustomerAmount = $pricing->get_Customer_ReverseMortgagePricing($insertdata->CustomerUID,$insertdata->SubProductUID);
		$insertdata->CustomerActualAmount = $insertdata->CustomerAmount;


		$mStates=$this->db->get_where('mStates', array('StateCode' => $insertdata->PropertyStateCode))->row();

		$mCounties=$this->db->get_where('mCounties', array('StateUID'=>$mStates->StateUID, 'CountyName'=>$insertdata->PropertyCountyName))->row();

		//D-2-T23 state issuer fetched
		$timportdata->Issuer = $this->lang->line('Inhouse_addtable');
		$searchmoderesult = $this->common_model->get_statesearchmode($insertdata->PropertyStateCode,$insertdata->PropertyCountyName);
		if(!empty($searchmoderesult)) {
			$timportdata->Issuer = (!empty($searchmoderesult->SearchModeName)) ? $searchmoderesult->SearchModeName : $this->lang->line('Inhouse_addtable');
		}

		if(!empty($insertdata->ProjectUID)) {
			$mDocTypes = $this->db->get_where('mProjectDocType',array('ProjectUID'=>$insertdata->ProjectUID))->result();
		}

		if ($mStates->SearchType==1) {
			$insertdata->IsInhouseExternal=0;
		}
		elseif ($mStates->SearchType==2) {
			$insertdata->IsInhouseExternal=1;
		}
		else{
			if ($mStates->AbstractorAssignment==1) {
				$insertdata->IsInhouseExternal = $this->common_model->get_inhouse_external($insertdata->OrderTypeUID,$mStates->StateUID,$mCounties->CountyUID);
			}
			else{
				$insertdata->IsInhouseExternal = $this->common_model->get_inhouse_external_forzipcode($insertdata->OrderTypeUID,$mStates->StateUID,$insertdata->PropertyZipcode);
			}
		}


		$insertdata->OrderDueDateTime = calculate_duedate($insertdata->OrderEntryDatetime,$insertdata->CustomerUID,$insertdata->SubProductUID,$insertdata->PriorityUID);
		$insertdata->OriginalOrderDueDateTime = $insertdata->OrderDueDateTime;
		$propkey_array = array('PRName','PREmailID','PRHomeNumber','PRWorkNumber','PRCellNumber','PRSocialNumber');
		$this->db->trans_begin();
		$insertdata->OrderNumber = $this->Order_Number($insertdata->SubProductUID).'-1';
		$OrderNumber = $insertdata->OrderNumber;

		/* NF216 - Duplicate Order number restriction */
		$insertdata->OrderSequence = $this->OrderSequence($insertdata->SubProductUID).'-1';
		$date = date('Ymd');
		$insertdata->OrderDocsPath ='uploads/searchdocs/'.$date.'/'.$insertdata->OrderNumber.'/';

		/* desc: Insert Order Policy Type 
		** author: Yagavi G <yagavi.g@avanzegroup.com>
		** since: Aug 7th 2020
		*/
		$where_policy_type['SubProductUID'] = $insertdata->SubProductUID;
		$where_policy_type['CustomerUID'] = $insertdata->CustomerUID;
		$insertdata->OrderPolicyType = $this->common_model->GetCustomerProductDetailsByCustomerSubproductUID($where_policy_type)->OrderPolicyType;

		$query = $this->db->insert('tOrders',$insertdata);

		$OrderUID = $this->db->insert_id();

		if(!empty($timportdata)){
			$timportdata->OrderUID = $OrderUID;
			$this->db->insert('tOrderImport',$timportdata);
		}

		if(!empty($data['Seller'])){
			$sellerdata['OrderUID'] = $OrderUID; 
			$sellerdata['PropertyRoleUID'] = $this->config->item('Propertyroles')['Sellers']; 
			$sellerdata['PRName'] = $data['Seller']; 
			$this->db->insert('tOrderPropertyRoles', $sellerdata);
		}

		if(!empty($data['BorrowerName1'])){
			$borrowerinsertdata[0]['OrderUID'] = $OrderUID; 
			$borrowerinsertdata[0]['PropertyRoleUID'] =  $this->config->item('Propertyroles')['Borrowers'];
			$borrowerinsertdata[0]['PRName'] =  $data['BorrowerName1'];
			$borrowerinsertdata[0]['PREmailID'] =  $data['Email1'];
			$borrowerinsertdata[0]['PRHomeNumber'] =  $data['HomeNumber1'];
			$borrowerinsertdata[0]['PRWorkNumber'] =  $data['WorkNumber1'];
			$borrowerinsertdata[0]['PRCellNumber'] =  $data['CellNumber1'];
			$borrowerinsertdata[0]['PRSocialNumber'] =  $data['Social1'];
		}

		if (!empty($data['BorrowerName2'])) {
			$borrowerinsertdata[1]['OrderUID'] = $OrderUID; 
			$borrowerinsertdata[1]['PropertyRoleUID'] =  $this->config->item('Propertyroles')['Borrowers'];
			$borrowerinsertdata[1]['PRName'] =  $data['BorrowerName2'];
			$borrowerinsertdata[1]['PREmailID'] =  $data['Email2'];
			$borrowerinsertdata[1]['PRHomeNumber'] =  $data['HomeNumber2'];
			$borrowerinsertdata[1]['PRWorkNumber'] =  $data['WorkNumber2'];
			$borrowerinsertdata[1]['PRCellNumber'] =  $data['CellNumber2'];
			$borrowerinsertdata[1]['PRSocialNumber'] =  $data['Social2'];
		}

		if(!empty($borrowerinsertdata)){
			$this->db->insert_batch('tOrderPropertyRoles', $borrowerinsertdata);
		}
		
		/*INSERT CUSTOMER ACTUAL PRICING*/
		$this->common_model->insert_customerpaymentdata($OrderUID);


		$date = date('Ymd');
		$Path = FCPATH .'uploads/searchdocs/'.$date.'/'.$OrderNumber.'/';
		$viewPath = 'uploads/searchdocs/'.$date.'/'.$OrderNumber.'/';

		if (!is_dir($Path)) {
			mkdir($Path, 0777, true);
		}

		if(isset($files) && !empty($files)) {

			foreach ($files['name'] as $key => $value) {
				$dotposition = strripos($value, '.');
				$documentname = substr($value, 0, $dotposition);

				if (strpos(strtolower($documentname), strtolower($insertdata->LoanNumber)) !== false) {
					$this->NormalFileUpload($files['tmp_name'][$key], $Path . $value, $OrderUID);

					$tDocuments = array(
						'OrderUID'=>$OrderUID,
						'DocumentFileName'=>$value,
						'DisplayFileName'=>$value,
						'UploadedDate'=>date('Y-m-d H:i:s'),
						'DocumentCreatedDate'=>date('Y-m-d H:i:s'),
						'UploadedUserUID'=>$this->loggedid
					);

					$this->db->insert('torderdocuments', $tDocuments);
				}
			}
		}

		/*Inserting mDocType in tOrderDocType*/
		$insertdocdata = [];
		if(!empty($mDocTypes)){
			foreach ($mDocTypes as $key => $mDocType) {
				$insertdocdata[$key]['OrderUID'] = $OrderUID;
				$insertdocdata[$key]['DocTypeUID'] = $mDocType->DocTypeUID;
			}
		}
		if(!empty($insertdocdata)){
			$this->db->insert_batch('tOrderDocType', $insertdocdata);
		}

		/* D-2-T9 GENERATE PACKAGE NUMBER BASED ON LOAN NUMBER*/
		$this->common_model->save_package_number($OrderUID,$insertdata->LoanNumber);

		/*-----Generate Map Start---*/
		$this->generate_map($OrderUID);
		/*-----Generate Map End---*/
			
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return array('OrderUID'=>$OrderUID,'OrderNumber'=>$OrderNumber);
		}

	}

	function get_customerproject($CustomerUID,$ProductUID)
	{
		$this->db->select("*");
		$this->db->from('mProjects');
		$this->db->where(array("mProjects.CustomerUID"=>$CustomerUID,"mProjects.ProductUID"=>$ProductUID));
		$query = $this->db->get();
		return $query->row();
	}

	function get_projectdetail($ProjectUID)
	{
		$this->db->select("*");
		$this->db->from('mProjects');
		$this->db->where("mProjects.ProjectUID",$ProjectUID);
		$query = $this->db->get();
		return $query->row();
	}

	function get_subproductbyname($SubProductName){
		$this->db->where(array("Active"=>1,"SubProductName" => trim($SubProductName)));
		$query = $this->db->get('mSubProducts');
		return $query->row();
	}

	//  D-2-T14 RMS code merge -- fix
	function Insert_tOrderImport($data,$OrderUID ='') {
		if(!empty($OrderUID)) {
			//timport data
			$updatetimport = new Stdclass();
			$updatetimport->OrderUID = $OrderUID;
			$updatetimport->Investor = isset($data['Investor']) ? $data['Investor'] : null;
			$updatetimport->MCAPercentage = isset($data['MCAPercentage']) ? $data['MCAPercentage'] : null;
			$updatetimport->MCAAmount = isset($data['MCAAmount']) ? $data['MCAAmount'] : 0.00;
			$updatetimport->MCABuckets = 	isset($data['MCABuckets']) ? $data['MCABuckets'] : null;
			$updatetimport->Agent = isset($data['Agent']) ? $data['Agent'] : null;
			$updatetimport->TitleCompany = isset($data['TitleCompany']) ? $data['TitleCompany'] : null;
			$updatetimport->TitleUnderwriter = isset($data['TitleUnderwriter']) ? $data['TitleUnderwriter'] : null;
			$updatetimport->PolicyNumber = isset($data['PolicyNumber']) ? $data['PolicyNumber'] : null;
			$updatetimport->Issuer = isset($data['Issuer']) ? $data['Issuer'] : null;
			$updatetimport->ClientKickBackComments = isset($data['ClientKickBackComments']) ? $data['ClientKickBackComments'] : null;
			$updatetimport->SearchOrderedDate = isset($data['SearchOrderedDate']) && !empty($data['SearchOrderedDate']) ? Date('Y-m-d',strtotime($data['SearchOrderedDate'])) : null;
			$updatetimport->PolicyApproveDate = isset($data['PolicyApproveDate']) && !empty($data['PolicyApproveDate'])  ? Date('Y-m-d',strtotime($data['PolicyApproveDate'])) : null;
			$updatetimport->ClientKickBackDate = isset($data['ClientKickBackDate']) && !empty($data['ClientKickBackDate']) ? Date('Y-m-d',strtotime($data['ClientKickBackDate'])) : null;

			if (isset($data['FHA'])) 
			{
				$updatetimport->FHA = $data['FHA'];
			}


			$this->db->where('OrderUID',$OrderUID);    
			$timportquery = $this->db->get('tOrderImport');
			if ( $timportquery->num_rows() > 0 )
			{
				$this->db->where('OrderUID',$OrderUID);    
				$this->db->update('tOrderImport',$updatetimport);
			} else {
				$this->db->insert('tOrderImport',$updatetimport);
			}
		}
	}

	/**
	* @author Alwin L
	* @purpose Check X1 order
	*/

	// function CheckX1Order($CustomerUID,$ProductUID,$SubProductUID) {
	// 	$OrderSourceUID = $this->db->select('OrderSourceUID')->from('mApiTitlePlatform')->where('OrderSourceName','X1')->get()->row()->OrderSourceUID;
	// 	if (empty($OrderSourceUID)) 
	// 	{
	// 		return 0;
	// 	}
		
	// 	$result = $this->db->select('*')->from('mCustomerProducts')->where(array('CustomerUID'=>$CustomerUID,'SubProductUID'=>$SubProductUID,'OrderSourceUID'=>$OrderSourceUID))->get()->row();
	// 	if(!empty($result))
	// 	{
	// 		return 1;
	// 	}
	// 	else
	// 	{
	// 		return 0;
	// 	}
	// }

	/**
	* @author Naveenkumar
	* @purpose subproducts based get priority
	*/

	function checkPrioritySubproduct($CustomerUID,$ProductUID,$SubProductUID) {
		$this->db->select('PriorityUID');
		$this->db->from('mCustomerProducts');
		$this->db->where('CustomerUID',$CustomerUID);
		$this->db->where('ProductUID',$ProductUID);
		$this->db->where('SubProductUID',$SubProductUID);
		return $this->db->get()->row();
	}

	/**
	* @author Naveenkumar
	* @purpose get priority based on customer
	*/
	function checkPriorityCustomer($CustomerUID) {
		$this->db->select('PriorityUID');
		$this->db->from('mCustomers');
		$this->db->where('CustomerUID',$CustomerUID);
		return $this->db->get()->row()->PriorityUID;
	}

	/**
	* @author Naveenkumar
	* @purpose get priority based on Msubproduct
	*/

	function checkPriorityMSubproduct($ProductUID,$SubProductUID) {
		$this->db->select('PriorityUID');
		$this->db->from('mSubProducts');
		$this->db->where('ProductUID',$ProductUID);
		$this->db->where('SubProductUID',$SubProductUID);
		return $this->db->get()->row()->PriorityUID;
	}

	/**
	*@description function to check Vendor Quote needed
	*
	* @param int OrderUID
	* 
	* @throws no exception
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return nothing 
	* @since 07 jan 2020 
	* @version DI1-T59 Vendor Quote
	*
	*/
	function check_vendorquotebeforebilling($OrderUID){
		$query = $this->db->query("SELECT  GROUP_CONCAT(CONCAT_WS(' ',`mabstractor`.`AbstractorFirstName`,`mabstractor`.`AbstractorMiddleName`, `mabstractor`.`AbstractorLastName`,'(',RoleCategoryName,')','for the Assignment Type',OrderTypeName,'QUOTE FEE action pending') ) AS quotetext  FROM (`torderabstractor`) JOIN `mabstractor` ON `mabstractor`.`AbstractorUID` = `torderabstractor`.`AbstractorUID`  JOIN mordertypes ON mordertypes.OrderTypeUID = torderabstractor.OrderTypeUID LEFT JOIN mRoleCategory ON mRoleCategory.RoleCategoryUID = torderabstractor.RoleCategoryUID  WHERE `torderabstractor`.`OrderUID` = {$OrderUID} AND `IndividualContact` = 1 AND IsQuote = 1
			");
		return $query->row()->quotetext;
	}

	/**
	*@description function to check Client Quote needed
	*
	* @param int OrderUID
	* 
	* @throws no exception
	* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	* @return nothing 
	* @since 22 MAY 2020 
	* @version D2T-1 Vendor Quote
	*
	*/
	function check_clientquotebeforebilling($OrderUID)
	{
		$query = $this->db->query("SELECT  CONCAT_WS(' ','Client',`mCustomers`.`CustomerName`,'QUOTE FEE action pending') AS quotetext  FROM (`tOrders`) JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` WHERE `tOrders`.`OrderUID` = {$OrderUID} AND IsQuote = 1
			");
		return $query->row()->quotetext;
	}

	function GetPreferedSites($OrderUID) 
	{

		$tOrders=$this->db->get_where('tOrders', array('OrderUID'=>$OrderUID))->row();

		$mStates=$this->db->get_where('mStates', array('StateCode' => $tOrders->PropertyStateCode))->row();

		$mCounties=$this->db->get_where('mCounties', array('StateUID'=>$mStates->StateUID, 'CountyName'=>$tOrders->PropertyCountyName))->row();

		// if(!empty($mCounties))
		// { 	


		// 	$query = $this->db->query("SELECT *, CASE WHEN msearchmodes.SearchModeUID = '6' THEN 
		// 		mcountysearchmodes.WebsiteURL ELSE msearchmodes.SearchSiteURL END AS SiteURL 
		// 		FROM mcountysearchmodes
		// 		LEFT JOIN msearchmodes ON mcountysearchmodes.SearchModeUID = msearchmodes.SearchModeUID 
		// 		WHERE mcountysearchmodes.CountyUID = '". $mCounties->CountyUID ."'and msearchmodes.SearchSiteURL = 'X1' AND msearchmodes.SearchModeUID <> 5
		// 		Order By FIELD(SearchModeName, 'Free', 'Paid', 'Others', 'Abstractor')");

		// 	$data = $query->row();
		// }

		return $data;

	}

/**
	*@description Customer Pricing Info
	*
	* @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
	* @since July 27 2020
	* @version Order Entry
	*
*/
function GetCustomerPricing($data){
	$pricing = new Customer_pricing();
	$CustomerAmountQuote = $pricing->GetCustomerPricing($data);
	return $CustomerAmountQuote;
}
/*End*/

/*
	*@description To Find Duplicate SubProducts
	*
	* @param int ProductUIDs Array, SubProductUIDs Array, PreviousOrderUID
	* 
	* @throws no exception
	* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
	* @return Repeated subproducts Array 
	* @since 27 Jul 2020
	* @version May CR
	*
	*/
	function findProductDuplicate($Products,$SubProducts,$OrderUID = FALSE){
		$OrderSubProducts = [];
		if($OrderUID != FALSE){
			$OrderSubProducts = array_column($this->common_model->GetRelationalOrdersByID($OrderUID),'SubProductUID');
		}
		$subproducts = array();
		$duplicates = array();
		
		if(!empty($SubProducts)){
			foreach($SubProducts as $val => $SubProduct){
				if(!in_array($SubProduct,$subproducts)){
					array_push($subproducts,$SubProduct);
				}else{
					array_push($duplicates,$SubProduct);
				}
			}
		}
		foreach($OrderSubProducts as $val => $SubProduct){
			if(!in_array($SubProduct,$subproducts)){
				array_push($subproducts,$SubProduct);
			}else{
				array_push($duplicates,$SubProduct);
			}
		}	
		return array_unique($duplicates);
	}

}?>
