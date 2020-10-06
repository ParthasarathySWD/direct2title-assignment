<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Customers_Model extends CI_Model {

	
	function __construct()
	{ 
		parent::__construct();
		$this->config->load('keywords');
	}

	function GetExcludeAbstractorDtails(){
		$this->db->select("*");
		$this->db->from('mabstractor');
		//$this->db->join('musers','mabstractor.AbstractorUID=musers.AbstractorUID','inner');
		$this->db->where('mabstractor.IsPrivate',0);  
		$query = $this->db->get();
		return $query->result();
	}


	function GetSettlementType()
	{
		$this->db->select('*');
		$this->db->from('mSettlementType');
		$this->db->where('mSettlementType.SettlementSectionUID ',4);
		$query = $this->db->get();
		return $query->result();
	}


	function DeleteCustomerSettlementDefault($CustomerSettlementDefaultUID){
      $this->db->where('CustomerSettlementDefaultUID', $CustomerSettlementDefaultUID);
      $PreviousData = $this->db->get('mCustomerSettlementDefaults')->row();

      $this->db->where('CustomerSettlementDefaultUID',$CustomerSettlementDefaultUID);
      $res = $this->db->delete('mCustomerSettlementDefaults');
      if($res){
        $Row = '';
        $Row .= (!empty($PreviousData->CustomerUID))?'Customer:'.$this->common_model->getCustomerRowbyUID($PreviousData->CustomerUID)->CustomerName:'';
        $Row .= (!empty($PreviousData->ProductUID))?'-- Product:'.$this->common_model->getProductRowbyUID($PreviousData->ProductUID)->ProductName:'';
        $Row .= (!empty($PreviousData->SubProductUID))?'-- SubProduct:'.$this->common_model->getSubProductRowbyUID($PreviousData->SubProductUID)->SubProductName:'';
        $Row .= (!empty($PreviousData->OrderTypeUID))?'-- AssignmentType:'.$this->common_model->GetOrderTypeDetailsById($PreviousData->OrderTypeUID):'';
        $msg = '<b>Client Product assignment </b> Deleted: <b>'.$Row.'</b>';
        $this->common_model->InsertAuditLog($PreviousData->SettlementTypeUID,$msg,'settlement_type');
      }
      return $res;
    }

	public function saveClientProduct($data,$Id='')
    {

       if(empty($Id))
       {
          $this->db->where($data);
          $duplicate = $this->db->get('mCustomerSettlementDefaults')->num_rows(); 
          if(empty($duplicate))
          {
            if($this->db->insert('mCustomerSettlementDefaults', $data))
            {
              $Row = '';
              $Row .= (!empty($data['CustomerUID']))?'Customer:'.$this->common_model->getCustomerRowbyUID($data['CustomerUID'])->CustomerName:' ';
              $Row .= (!empty($data['ProductUID']))?'-- Product:'.$this->common_model->getProductRowbyUID($data['ProductUID'])->ProductName:' ';
              $Row .= (!empty($data['SubProductUID']))?'-- SubProduct:'.$this->common_model->getSubProductRowbyUID($data['SubProductUID'])->SubProductName:' ';
              $Row .= (!empty($data['OrderTypeUID']))?'-- AssignmentType:'.$this->common_model->GetOrderTypeDetailsById($data['OrderTypeUID']):' ';
              $msg = '<b>Customer settlement default</b> Created: <b>'.$Row.'</b>';
              $this->common_model->InsertAuditLog($data['CustomerUID'],$msg,'Customers');
               return 1;
            } else {
               return 0;
            }
          } else {
            return 'duplicate';
          }
       } else {
       
         $this->db->where('CustomerSettlementDefaultUID <>',$Id);
         $this->db->where('CustomerUID',$data['CustomerUID']);
         $this->db->where($data);
         $duplicate = $this->db->get('mCustomerSettlementDefaults')->num_rows();
         if(empty($duplicate))
         {
          $this->db->where('CustomerSettlementDefaultUID',$Id);     
          $PreviousData = $this->db->get('mCustomerSettlementDefaults')->row();  
          $audit_msg = '';
          if($PreviousData->CustomerUID != $data['CustomerUID']
          || $PreviousData->ProductUID != $data['ProductUID']
          || $PreviousData->SubProductUID != $data['SubProductUID']
          || $PreviousData->OrderTypeUID != $data['OrderTypeUID']
          ){
            $PRow = '';$CRow = '';
            $PRow .= (!empty($PreviousData->CustomerUID))?'Customer:'.$this->common_model->getCustomerRowbyUID($PreviousData->CustomerUID)->CustomerName:' ';
            $PRow .= (!empty($PreviousData->ProductUID))?'-- Product:'.$this->common_model->getProductRowbyUID($PreviousData->ProductUID)->ProductName:' ';
            $PRow .= (!empty($PreviousData->SubProductUID))?'-- SubProduct:'.$this->common_model->getSubProductRowbyUID($PreviousData->SubProductUID)->SubProductName:' ';
            $PRow .= (!empty($PreviousData->OrderTypeUID))?'-- AssignmentType:'.$this->common_model->GetOrderTypeDetailsById($PreviousData->OrderTypeUID):' ';
            $CRow .= (!empty($data['CustomerUID']))?'Customer:'.$this->common_model->getCustomerRowbyUID($data['CustomerUID'])->CustomerName:' ';
            $CRow .= (!empty($data['ProductUID']))?'-- Product:'.$this->common_model->getProductRowbyUID($data['ProductUID'])->ProductName:' ';
            $CRow .= (!empty($data['SubProductUID']))?'-- SubProduct:'.$this->common_model->getSubProductRowbyUID($data['SubProductUID'])->SubProductName:' ';
            $CRow .= (!empty($data['OrderTypeUID']))?'-- AssignmentType:'.$this->common_model->GetOrderTypeDetailsById($data['OrderTypeUID']):' ';
            $audit_msg = '<b>Client Product settlement </b> Updated: '.$PRow.'</b> <b>-></b> '.$CRow;
          }
              
            $this->db->where('CustomerSettlementDefaultUID',$Id);  
            if($this->db->update('mCustomerSettlementDefaults',$data))
            {

              if($audit_msg != ''){
                $this->common_model->InsertAuditLog($data['CustomerUID'],$audit_msg,'Customers');
              }           

              return 1;
            } else {
              return 0;
            }
         } else {
            return 'duplicate';
         }
       } 
    }


     public function get_client_product_assignment($CustomerUID)
    {
      $this->db->select("mCustomerSettlementDefaults.*,mSettlementType.SettlementTypeName,mproducts.ProductName,msubproducts.SubProductName");
      $this->db->from('mCustomerSettlementDefaults');
      $this->db->join('mSettlementType','mSettlementType.SettlementTypeUID = mCustomerSettlementDefaults.SettlementTypeUID','left');
      $this->db->join('mproducts','mproducts.ProductUID = mCustomerSettlementDefaults.ProductUID','left');
      $this->db->join('msubproducts','msubproducts.SubProductUID = mCustomerSettlementDefaults.SubProductUID','left');
      //$this->db->join('mordertypes','mordertypes.OrderTypeUID = mSettlementTypeClientProducts.OrderTypeUID','left');
      $this->db->where('mCustomerSettlementDefaults.CustomerUID',$CustomerUID);
      $query = $this->db->get();
      return $query->result();
    }

	function GetPrivateAbstractorDtails(){
		$this->db->select("*");
		$this->db->from('mabstractor');
		//$this->db->join('musers','mabstractor.AbstractorUID=musers.AbstractorUID','inner');
		$this->db->where('mabstractor.IsPrivate',1);  
		$query = $this->db->get();
		return $query->result();
	}

	function CheckCustomerExcludeAbstractorDtails($AbstractorUID,$CustomerUID){
		$this->db->select("*");
		$this->db->from('MCustomerAbstractor');
		$this->db->where('MCustomerAbstractor.CustomerUID',$CustomerUID);  
		$this->db->where('MCustomerAbstractor.AbstractorUID',$AbstractorUID);  
		$this->db->where('MCustomerAbstractor.ExcludeAbstractor',1);  
		$query = $this->db->get();
		return $query->row();
	}

	function CheckCustomerPrivateAbstractorDtails($AbstractorUID,$CustomerUID){
		$this->db->select("*");
		$this->db->from('MCustomerAbstractor');
		$this->db->where('MCustomerAbstractor.CustomerUID',$CustomerUID);  
		$this->db->where('MCustomerAbstractor.AbstractorUID',$AbstractorUID);  
		$this->db->where('MCustomerAbstractor.ExcludeAbstractor',0);  
		$query = $this->db->get();
		return $query->row();
	}

	function GetCustomerPricingListDetailsByPricingUID($PricingUID){

		$this->db->select("*");
		$this->db->from('mpricingproducts');
		$this->db->join('mpricing','mpricingproducts.PricingUID=mpricing.PricingUID','left');
		$this->db->join('mcounties','mpricingproducts.CountyUID=mcounties.CountyUID','left');
		$this->db->join('mstates','mpricingproducts.StateUID=mstates.StateUID','left');
		$this->db->join('msubproducts','mpricingproducts.SubProductUID=msubproducts.SubProductUID','left');
		$this->db->where_in('mpricing.PricingType',array('C','RM','GRADE'));
		$this->db->where('mpricing.PricingUID',$PricingUID);  
		$query = $this->db->get();
		return $query->result();
	}

	function GetCustomerPricingListDetails(){

		$this->db->select("*");
		$this->db->from('mpricingproducts');
		$this->db->join('mpricing','mpricingproducts.PricingUID=mpricing.PricingUID','left');
		$this->db->join('mcounties','mpricingproducts.CountyUID=mcounties.CountyUID','left');
		$this->db->join('mstates','mpricingproducts.StateUID=mstates.StateUID','left');
		$this->db->join('msubproducts','mpricingproducts.SubProductUID=msubproducts.SubProductUID','left');
		$this->db->where_in('mpricing.PricingType',array('C','RM','GRADE'));
		$query = $this->db->get();
		return $query->result();
	}


	function GetAllWorkflowModule(){

		$this->db->select("WorkflowModuleUID");
		$this->db->from('mworkflowmodules');
		$query = $this->db->get();
		return $query->result();
	}

	function GetCustomerDetails()
	{
		$this->db->select('*, mcustomers.Active as CustStatus ');	
		$this->db->from('mcustomers');
		$this->db->join ('mstates', 'mcustomers.CustomerStateUID = mstates.StateUID' , 'left' );  
		$this->db->join ('mcounties', 'mcustomers.CustomerCountyUID = mcounties.CountyUID' , 'left' );  
		$this->db->join ('mcities', 'mcustomers.CustomerCityUID = mcities.CityUID' , 'left' ); 
		$this->db->join ('mpricing', 'mcustomers.PricingUID = mpricing.PricingUID' , 'left' ); 
		return $this->db->get()->result();
	}

	function GetParentCompanyDetails()
	{
		$this->db->select('*, mcustomers.Active');	
		$this->db->from('mcustomers');
		$this->db->join ('mstates', 'mcustomers.CustomerStateUID = mstates.StateUID' , 'left' );  
		$this->db->join ('mcounties', 'mcustomers.CustomerCountyUID = mcounties.CountyUID' , 'left' );  
		$this->db->join ('mcities', 'mcustomers.CustomerCityUID = mcities.CityUID' , 'left' ); 
		$this->db->where(array("mcustomers.ParentCompany"=>1));   
		return $this->db->get()->result();
	}

	function GetParentCompanyNameDetails($ParentCompanyUID)
	{

		$this->db->select('mcustomers.CustomerName');	
		$this->db->from('mcustomers');
		$this->db->where(array("mcustomers.CustomerUID"=>$ParentCompanyUID));   
		return $this->db->get()->row();


	}

	function GetPricingByCustomer($CustomerUID)
	{
		$this->db->select("*");
		$this->db->from('mcustomers');
		$this->db->join ('mpricing', 'mcustomers.PricingUID = mpricing.PricingUID' , 'left' );
		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$query = $this->db->get();
		return $query->result();
	}

	/*function GetProductByTemplate($ProductUID)
	{
		$this->db->select("*");
		$this->db->from('mtemplates');
		$this->db->join ('mproducts', 'mtemplates.ProductUID = mproducts.ProductUID' , 'left' );
		$this->db->where(array("mtemplates.ProductUID"=>$ProductUID));
		$query = $this->db->get();
		$ProductTemplate = $query->result();

		return $ProductTemplate;
	}*/

	function GetProductByTemplate($ProductUID)
	{
		$this->db->select("*");
		$this->db->from('mtemplates');
		$this->db->join ('mproducts', 'mtemplates.ProductUID = mproducts.ProductUID' , 'left' );
		$this->db->like('mtemplates.ProductUID', $ProductUID);
		$query = $this->db->get();
		$ProductTemplate = $query->result();

		return $ProductTemplate;
	}

	function GetCustomerDetailsByUID($CustomerUID)
	{
		$this->db->select('*, mcustomers.Active');	
		$this->db->from('mcustomers');
		$this->db->join ('mgroups', 'mcustomers.GroupUID = mgroups.GroupUID' , 'left' ); 
		$this->db->where(array("mcustomers.CustomerUID"=>$CustomerUID)); 
		return $this->db->get()->row();
	}


	function GetCustomerProductDetailsById($CustomerUID,$ProductUID)
	{ 
		/*$this->db->distinct('mcustomerproducts.ProductUID');
		$this->db->from('mcustomerproducts');
		$this->db->join('mproducts','mcustomerproducts.ProductUID=mproducts.ProductUID','left');
		$this->db->where(array("mcustomerproducts.CustomerUID"=>$CustomerUID));
		$query = $this->db->get();*/

		$query =  $this->db->query("SELECT DISTINCT(`ProductUID`) FROM `mcustomerproducts` WHERE `CustomerUID` = $CustomerUID");
		return $query->row(); 
	}  

	function GetCustomersubProductDetailById($CustomerUID,$SubProductUID)
	{

		$this->db->select("*");
		$this->db->from('mcustomerproducts');
		$this->db->where("CustomerUID",$CustomerUID);
		$this->db->where("SubProductUID",$SubProductUID);
		$query = $this->db->get();
		return $query->row();
	}  


	function Chk_Duplicate_entry($data)
	{
		$q = $this->db->get_where('mcustomers', $data);
		$record = $q->result();
		if(count($record)>0)
		{
			return 1;
		} else {
			return 0;
		}
	}

	function store_DB($data)
	{
		$query = $this->db->insert('mcustomers',$data);
		$insert_id = $this->db->insert_id();
		return $insert_id;
	}

	function Add_CustomerProduct($CustProduct)
	{
		if($this->db->insert('mcustomerproducts',$CustProduct))
		{
			return 1;
		} else {
			return 0;
		} 
	}

	function Add_CustomerTemplate($CustTemp)
	{
		if($this->db->insert('mcustomertemplates',$CustTemp))
		{
			return 1;
		} else {
			return 0;
		} 
	}

	function Add_CustomerWorkflow($Custwrk)
	{
		if($this->db->insert('mcustomerworkflowmodules',$Custwrk))
		{
			return 1;
		} else {
			return 0;
		} 
	}

	function update_customer($data, $id)
	{
		$this->db->where('CustomerUID', $id);
		$query = $this->db->update('mcustomers', $data);	
		return $query;
	}

	function get_customers()
	{
		$this->db->select('*, mcustomers.Active as CustStatus');	
		$this->db->from('mcustomers');
		$this->db->join ('mstates', 'mcustomers.CustomerStateUID = mstates.StateUID' , 'left' );  
		$this->db->join ('mcounties', 'mcustomers.CustomerCountyUID = mcounties.CountyUID' , 'left' );  
		$this->db->join ('mcities', 'mcustomers.CustomerCityUID = mcities.CityUID' , 'left' );  
		return $this->db->get()->result();
	}

	function get_customers_by_id($id)
	{
		$this->db->select('*, mcustomers.Active as CustStatus');	
		$this->db->from('mcustomers');
		$this->db->join ('mstates', 'mcustomers.CustomerStateUID = mstates.StateUID' , 'left' );  
		$this->db->join ('mcounties', 'mcustomers.CustomerCountyUID = mcounties.CountyUID' , 'left' );  
		$this->db->join ('mcities', 'mcustomers.CustomerCityUID = mcities.CityUID' , 'left' );  
		$this->db->where('mcustomers.CustomerUID', $id);
		return $this->db->get()->result();	
	}

	function GetGroups(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mgroups');
		return $query->result();
	}


	function saveCustomerDetails($Template_Workflow,$CustomersDetails,$Prod_SubProducts,$priorityarray,$DefaultProductSubCode,$DefaultProductSubValue)
	{
		$ParentCompany = isset($CustomersDetails['ParentCompany'])? 1:0;
		$UserUID = $this->session->userdata('UserUID');

		$GroupUID=$CustomersDetails['GroupUID'];

		if(empty($GroupUID))
		{
			$result=$this->db->get_where('mgroups', array('Active'=> 1, 'DefaultGroup'=> 1));
			$Group=$result->row();
			$GroupUID=$Group->GroupUID;
		}

		$fieldArray = array(
			"CustomerUID"=>$CustomersDetails['CustomerUID'],
			"CustomerNumber"=>$CustomersDetails['CustomerNumber'],
			"SAPReportName"=>$CustomersDetails['SAPReportName'],
			"CustomerName"=>$CustomersDetails['CustomerName'],
			"CustomerPContactName"=>$CustomersDetails['CustomerPContactName'],
			"CustomerPContactMobileNo"=>$CustomersDetails['CustomerPContactMobileNo'],
			"CustomerPContactEmailID"=>$CustomersDetails['CustomerPContactEmailID'],
			"CustomerOrderAckEmailID"=>$CustomersDetails['CustomerOrderAckEmailID'],
			"CustomerAddress1"=>$CustomersDetails['CustomerAddress1'],
			"CustomerAddress2"=>$CustomersDetails['CustomerAddress2'],   
			"CustomerZipCode"=>$CustomersDetails['CustomerZipCode'],  
			"CustomerStateUID"=>$CustomersDetails['CustomerStateUID'],
			"CustomerCityUID"=>$CustomersDetails['CustomerCityUID'],
			"CustomerCountyUID"=>$CustomersDetails['CustomerCountyUID'],
			"CustomerOfficeNo"=>$CustomersDetails['CustomerOfficeNo'],
			"CustomerFaxNo"=>$CustomersDetails['CustomerFaxNo'],
			"CustomerWebsite"=>$CustomersDetails['CustomerWebsite'],
			"AppUsageCost"=>$CustomersDetails['AppUsageCost'],
			"PricingUID"=>$CustomersDetails['PricingUID'],
			"PriorityUID"=>$CustomersDetails['PriorityUID'],
			"DefaultTemplateUID"=>$CustomersDetails['DefaultTemplateUID'],
			"TaxCertificateRequired"=>$CustomersDetails['TaxCertificateRequired'],
			"ParentCompany"=>$ParentCompany,
			"ParentCompanyUID"=>$CustomersDetails['ParentCompanyUID'],
			"BundlePricing"=>isset($CustomersDetails['BundlePricing'])? 1:0,
			"AdverseConditionsEnabled"=>isset($CustomersDetails['AdverseConditionsEnabled'])? 1:0,
			"AutoBilling"=>isset($CustomersDetails['AutoBilling'])? 1:0,
			"SubCategoryGrading"=>isset($CustomersDetails['SubCategoryGrading'])? 1:0,
			"GradingType"=>isset($CustomersDetails['GradingType']) ? $CustomersDetails['GradingType']: 'State/County',

			// "CustomerEmail" =>$SaveEmail,
		  		//"CustomerLogo"=>$CustomersDetails['CustomerLogo'],
			"CreatedByUserUID"=>$UserUID,   
			"CreatedOn"=>date('Y-m-d h:i:s'),  
			"Active"=>1,
			"ParentCompanyCheck"=>1
		);

		$result = $this->db->insert('mcustomers', $fieldArray);
		$CustomerUID = $this->db->insert_id();
		$data1['ModuleName']='Customers_add';
		$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
		$data1['DateTime']=date('y-m-d H:i:s');
		$data1['TableName']='mcustomers';
		$data1['UserUID']=$this->session->userdata('UserUID');                
		$this->common_model->Audittrail_insert($data1);


		$result=$this->db->get_where('mgroupcustomers', array('Active'=> 1, 'GroupUID'=> $GroupUID, 'GroupCustomerUID'=> $CustomerUID));
		$groupcustomer_count=$result->num_rows();

		if($groupcustomer_count==0)
		{	
			$groupcustomers['GroupUID']=$GroupUID;
			$groupcustomers['GroupCustomerUID']=$CustomerUID;
			$groupcustomers['Active']=1;
			$this->db->insert('mgroupcustomers', $groupcustomers);
		}

		$Product_SubProucts = $this->saveProduct_SubProuctsDetails($Prod_SubProducts,$Template_Workflow,$CustomerUID,$priorityarray);  
		$this->saveDefaultProductSubProduct($CustomerUID,$DefaultProductSubCode,$DefaultProductSubValue); 
		return $CustomerUID;
	}

	function SaveCustomerInformation($CustomerUID,$Path)
	{
		if($CustomerUID == '')
		{
			$query = $this->db->query("SELECT max(CustomerUID) as CustomerUID FROM mcustomers");
			$res = $query->row();
			$CustomerUID = $res->CustomerUID;
			$this->db->set('CustomerInformation',$Path);
			$this->db->where('CustomerUID', $CustomerUID);
			$this->db->update('mcustomers'); 
			
		}
		else
		{
			$CustomerUID = $CustomerUID;
			$this->db->set('CustomerInformation',$Path);
			$this->db->where('CustomerUID', $CustomerUID);
			$this->db->update('mcustomers'); 
		}
		
	}

	function saveOrderTypeCustomers($CustomerUID,$OrderTypeUID,$TargetPath)
	{

		if($CustomerUID == '')
		{
			$query = $this->db->query("SELECT max(CustomerUID) as CustomerUID FROM mcustomers");
			$res = $query->row();
			$CustomerUID = $res->CustomerUID;
			$ordertypearray = array(
				"CustomerUID"=>$CustomerUID,
				"OrderTypeUID"=>$OrderTypeUID,
				"DocumentName"=>$TargetPath
			);
			$this->db->insert('mcustomerordertypedoc',$ordertypearray);
			if($this->db->affected_rows() > 0)
			{
				return true;
			}
			else
			{
				return false;
			}


		}
		else{
			$ordertypearray = array(
				"CustomerUID"=>$CustomerUID,
				"OrderTypeUID"=>$OrderTypeUID,
				"DocumentName"=>$TargetPath
			);
			$this->db->insert('mcustomerordertypedoc',$ordertypearray);
			if($this->db->affected_rows() > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	function saveDefaultProductSubProduct($CustomerUID,$DefaultProductSubCode,$DefaultProductSubValue)
	{
		$array = array(
			"CustomerUID"=>$CustomerUID,
			"DefaultProductSubCode"=>$DefaultProductSubCode,
			"DefaultProductSubValue"=>$DefaultProductSubValue
		);
		$this->db->insert('mcustomerdefaultproduct',$array);
	}

	function UpdateOrderTypeCustomersDocument($OrderTypeDocUID,$TargetPath)
	{

		$this->db->set('DocumentName',$TargetPath);
		$this->db->where('OrderTypeDocUID', $OrderTypeDocUID);
		$this->db->update('mcustomerordertypedoc'); 
		if($this->db->affected_rows() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function UpdateOrderTypeCustomers($OrderTypeUID,$OrderTypeDocUID)
	{
		$this->db->set('OrderTypeUID',$OrderTypeUID);
		$this->db->where('OrderTypeDocUID', $OrderTypeDocUID);
		$this->db->update('mcustomerordertypedoc'); 
	}

	function GetOrderTypeUID($CustomerUID)
	{
		$query = $this->db->query("SELECT * FROM mcustomerordertypedoc WHERE CustomerUID ='$CustomerUID' ");
		return $query->result();
	}

	function GetOrderTypeAll($OrderTypeDocUID)
	{
		$query = $this->db->query("SELECT * FROM mcustomerordertypedoc WHERE OrderTypeDocUID ='$OrderTypeDocUID' ");
		return $query->row();
	}

	function GetOrderTypeName($OrderTypeUID)
	{
		$query = $this->db->query("SELECT * FROM mordertypes WHERE OrderTypeUID ='$OrderTypeUID' ");
		return $query->result();
	}

	function GetOrderTypes()
	{
		$query = $this->db->query("SELECT * FROM mordertypes ");
		return $query->result();
	}

	function GetOrderTypeDocUID()
	{
		$query = $this->db->query("SELECT OrderTypeDocUID FROM mcustomerordertypedoc WHERE CustomerUID ='33' ");
		return $query->result_array();
	}


	function saveProduct_SubProuctsDetails($Prod_SubProducts,$Template_Workflow,$CustomerUID ='',$priorityarray)
	{
		foreach ($Prod_SubProducts as $key => $value) 
		{	
			if(empty($value['SubProductUID'][0])){
				return true;
			}
			else
			{
				$entry_array = array();
				$count = count($value['SubProductUID']);
				$SubProductUID = $value['SubProductUID']; 
				$ProductUID = $value['ProductUID']; 
				for($i=0; $i<$count; $i++)  
				{
					$entry_array[] = array(
						"CustomerUID"=>$CustomerUID,
						'SubProductUID' => $SubProductUID[$i],
						'ProductUID' => $ProductUID
					);
				}
				$this->db->insert_batch('mcustomerproducts', $entry_array);
			}
		}

		foreach ($Template_Workflow as $key => $value) 
		{	
			if(empty($value['SubProductUID'][0]))
			{
				return true;
			}
			else
			{
				$entry_array = array();
				$count = count($value['SubProductUID']);
				$SubProductUID = $value['SubProductUID']; 
				$ProductUID = $value['ProductUID']; 
				$TemplateUID = $value['TemplateUID']; 
				for($i=0; $i<$count; $i++)  
				{
					$entry_array[] = array(
						"CustomerUID"=>$CustomerUID,
						'ProductUID' => $ProductUID,
						'SubProductUID' => $SubProductUID[$i],
						'TemplateUID' => $TemplateUID
					);
				}
				$this->db->insert_batch('mcustomertemplates', $entry_array);
			}
		}

		foreach ($Template_Workflow as $keys => $values) 
		{	
			foreach ($values as $key => $value) 
			{
				if(is_array($value))
				{
					foreach ($value as $key1 => $value1) 
					{
						$data = array(
							"CustomerUID"=>$CustomerUID,
							'ProductUID' => $values['ProductUID'],
							'SubProductUID' => $values['SubProductUID'],
							'WorkflowModuleUID' => $value1
						);
						$this->db->insert('mcustomerworkflowmodules', $data);
					}

				}
			}
		}

		foreach ($priorityarray as $priorityarraykey => $priorityarrayvalue) 
		{
			foreach ($priorityarrayvalue['priorityhour'] as $key => $value) 
			{
				if($value != '')
				{
					$data = array(
						"CustomerUID"=>$CustomerUID,
						'ProductUID' => $priorityarrayvalue['ProductUID'],
						'SubProductUID' => $priorityarrayvalue['SubProductUID'],
						'PriorityUID' => $key,
						'PriorityTime' => $value,
						'SkipOrderOpenDate' => $priorityarrayvalue['SkipOrderOpenDate'][$key],
					);
					$this->db->insert('mcustomerproducttat', $data);
				}
			}
		}
	}

	function UpdateCustomerDetails($Template_Workflow,$CustomersDetails,$Prod_SubProducts,$priorityarray,$DefaultProductSubCode,$DefaultProductSubValue,$ExplodeDeleteDocumentID)
	{

		$ParentCompany = isset($CustomersDetails['ParentCompany'])? 1:0; 

		$UserUID = $this->session->userdata('UserUID');

		$data1['ModuleName']='Customers-update';
		$data1['IpAddreess']=$_SERVER['REMOTE_ADDR'];
		$data1['DateTime']=date('y-m-d H:i:s');
		$data1['TableName']='mcustomers';
		$data1['UserUID']=$this->session->userdata('UserUID');    

		$this->db->select('*');
		$this->db->from('mcustomers');
		$this->db->where(array("CustomerUID"=>$CustomersDetails['CustomerUID']));                
		$oldvalue=$this->db->get('')->row_array();

		$fieldArray = array(

			"CustomerUID"=>$CustomersDetails['CustomerUID'],
			"CustomerNumber"=>$CustomersDetails['CustomerNumber'],
			"SAPReportName"=>$CustomersDetails['SAPReportName'],
			"CustomerName"=>$CustomersDetails['CustomerName'],
			"CustomerPContactName"=>$CustomersDetails['CustomerPContactName'],
			"CustomerPContactMobileNo"=>$CustomersDetails['CustomerPContactMobileNo'],
			"CustomerPContactEmailID"=>$CustomersDetails['CustomerPContactEmailID'],
			"CustomerOrderAckEmailID"=>$CustomersDetails['CustomerOrderAckEmailID'],
			"CustomerAddress1"=>$CustomersDetails['CustomerAddress1'],
			"CustomerAddress2"=>$CustomersDetails['CustomerAddress2'],   
			"CustomerZipCode"=>$CustomersDetails['CustomerZipCode'],  
			"CustomerStateUID"=>$CustomersDetails['CustomerStateUID'],
			"CustomerCityUID"=>$CustomersDetails['CustomerCityUID'],
			"CustomerCountyUID"=>$CustomersDetails['CustomerCountyUID'],
			"CustomerOfficeNo"=>$CustomersDetails['CustomerOfficeNo'],
			"CustomerFaxNo"=>$CustomersDetails['CustomerFaxNo'],
			"CustomerWebsite"=>$CustomersDetails['CustomerWebsite'],
			"AppUsageCost"=>$CustomersDetails['AppUsageCost'],
			"PricingUID"=>$CustomersDetails['PricingUID'],
			"PriorityUID"=>$CustomersDetails['PriorityUID'],
			"DefaultTemplateUID"=>$CustomersDetails['DefaultTemplateUID'],
			"TaxCertificateRequired"=>$CustomersDetails['TaxCertificateRequired'],
			"ParentCompany"=>$ParentCompany,
			"ParentCompanyUID"=>$CustomersDetails['ParentCompanyUID'],
			// "CustomerEmail" =>$SaveEmail,
			"CreatedByUserUID"=>$UserUID,   
			"CreatedOn"=>date('Y-m-d h:i:s'),  
			"Active"=>1,
			"ParentCompanyCheck"=>1

		);

		$this->db->where(array("CustomerUID"=>$CustomersDetails['CustomerUID']));        
		$result = $this->db->update('mcustomers', $fieldArray);

		$this->db->select('*');
		$this->db->from('mcustomers');
		$this->db->where(array("CustomerUID"=>$CustomersDetails['CustomerUID']));           
		$newvalue = $this->db->get('')->row_array();

		$this->common_model->Audittrail_diff($newvalue,$oldvalue,$data1);


		$CustomerUID=$CustomersDetails['CustomerUID'];

		$Product_SubProucts = $this->UpdateProduct_SubProuctsDetails($Prod_SubProducts,$Template_Workflow,$CustomerUID,$priorityarray);
		$this->updateDefaultProductSubProduct($CustomerUID,$DefaultProductSubCode,$DefaultProductSubValue);        
		$this->deletedocument($ExplodeDeleteDocumentID,$CustomerUID); 
		return $CustomerUID;

	}

	function deletedocument($ExplodeDeleteDocumentID,$CustomerUID='')
	{


		foreach ($ExplodeDeleteDocumentID as $key => $value) 
		{
			$this->db->select('GROUP_CONCAT(OrderTypeUID) AS Type, DocumentName');
			$this->db->where('OrderTypeDocUID',$value);
			$type = $this->db->get('mcustomerordertypedoc')->row();

			$this->db->select('GROUP_CONCAT(OrderTypeName) AS OrderType');
			$this->db->where('OrderTypeUID IN ('.$type->Type.')',NULL,FALSE);
			$ord = $this->db->get('mordertypes')->row();

			$NewOrderTypeData = array(
				'UserUID' => $this->loggedid,
				'ModuleName' => 'Customers',
				'Feature' => $CustomerUID,
				'Content' => htmlentities('Order Type <b>'.$ord->OrderType.'</b> is removed and File: <b>'.basename($type->DocumentName).'</b> also removed.'),
				'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($NewOrderTypeData);

			$this->db->where(array("OrderTypeDocUID"=>$value));
			$res = $this->db->delete('mcustomerordertypedoc');
		}

	}


	function updateDefaultProductSubProduct($CustomerUID,$DefaultProductSubCode,$DefaultProductSubValue)
	{


		// $this->db->set('DefaultProductSubCode',$DefaultProductSubCode);
		// $this->db->set('DefaultProductSubValue',$DefaultProductSubValue);
		// $this->db->where('CustomerUID', $CustomerUID);
		// $this->db->update('mcustomerdefaultproduct'); 

		$default = [''=>'Empty',1=>'Default Subproduct',2=>'Most Processed Subproduct for the Month',3=>'Most Processed Subproduct So far'];
		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$oldSubProduct = $this->db->get('mcustomerdefaultproduct')->row();
		
		if($oldSubProduct->DefaultProductSubCode <> $DefaultProductSubCode)
		{
			$msg = 'Customer Default SubProduct changed from <b>'.$default[$oldSubProduct->DefaultProductSubCode].'</b> to <b>'.$default[$DefaultProductSubCode].'</b>';
			$InsetData = array(
				'UserUID' => $this->loggedid,
				'ModuleName' => 'Customers',
				'Feature' => $CustomerUID,
				'Content' => htmlentities($msg),
				'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($InsetData);
		}	

		$oldProduct = array_filter(explode(',', $oldSubProduct->DefaultProductSubValue));
		$newProduct = array_filter(explode(',', $DefaultProductSubValue));
		foreach ($oldProduct as $key => $value) 
		{
			if(!in_array($value, $newProduct))
			{
				$this->db->select('SubProductName');
				$this->db->where_in('SubProductUID', $value);
				$sub = $this->db->get('msubproducts')->row();

				$msg = 'Customer Default SubProduct removed: <b>'.$sub->SubProductName.'</b>';
				$InsetData = array(
					'UserUID' => $this->loggedid,
					'ModuleName' => 'Customers',
					'Feature' => $CustomerUID,
					'Content' => htmlentities($msg),
					'DateTime' => date('Y-m-d H:i:s'));
				$this->common_model->InsertAuditTrail($InsetData);
			}	
		}

		foreach ($newProduct as $sval) 
		{
			if(!in_array($sval, $oldProduct))
			{
				$this->db->select('SubProductName');
				$this->db->where('SubProductUID', $sval);
				$sub = $this->db->get('msubproducts')->row();
				$msg = 'Customer Default SubProduct added: <b>'.$sub->SubProductName.'</b>';
				$InsetData = array(
					'UserUID' => $this->loggedid,
					'ModuleName' => 'Customers',
					'Feature' => $CustomerUID,
					'Content' => htmlentities($msg),
					'DateTime' => date('Y-m-d H:i:s'));
				$this->common_model->InsertAuditTrail($InsetData);
			}
		}

		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$res = $this->db->delete('mcustomerdefaultproduct');

		$data = array(
			"CustomerUID"=>$CustomerUID,
			'DefaultProductSubCode' => $DefaultProductSubCode,
			'DefaultProductSubValue' => $DefaultProductSubValue,		
		);
		$this->db->insert('mcustomerdefaultproduct', $data);
		
	}


	function UpdateProduct_SubProuctsDetails($Prod_SubProducts,$Template_Workflow,$CustomerUID ='',$priorityarray)
	{

		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$res = $this->db->delete('mcustomerproducts');

		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$res = $this->db->delete('mcustomertemplates');

		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$res = $this->db->delete('mcustomerworkflowmodules');

		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$res = $this->db->delete('mcustomerproducttat');




		foreach ($priorityarray as $priorityarraykey => $priorityarrayvalue) {


			foreach ($priorityarrayvalue['priorityhour'] as $key => $value) {


				if($value != ''){

					$data = array(
						"CustomerUID"=>$CustomerUID,
						'ProductUID' => $priorityarrayvalue['ProductUID'],
						'SubProductUID' => $priorityarrayvalue['SubProductUID'],
						'PriorityUID' => $key,
						'PriorityTime' => $value,
						'SkipOrderOpenDate' => $priorityarrayvalue['SkipOrderOpenDate'][$key],
					);
					$this->db->insert('mcustomerproducttat', $data);
				}

			}
		}


		foreach ($Prod_SubProducts as $key => $value) 
		{	
			if(empty($value['SubProductUID'][0])){
				return true;
			}

			else{
				$entry_array = array();
				$count = count($value['SubProductUID']);
				$SubProductUID = $value['SubProductUID']; 
				$ProductUID = $value['ProductUID']; 
				for($i=0; $i<$count; $i++)  
				{
					$entry_array[] = array(
						"CustomerUID"=>$CustomerUID,
						'SubProductUID' => $SubProductUID[$i],
						'ProductUID' => $ProductUID
					);
				}
				$this->db->insert_batch('mcustomerproducts', $entry_array);
			}

		}

		foreach ($Template_Workflow as $key => $value) {	


			if(empty($value['SubProductUID'][0])){
				return true;
			}

			else{
				$entry_array = array();
				$count = count($value['SubProductUID']);
				$SubProductUID = $value['SubProductUID']; 
				$ProductUID = $value['ProductUID']; 
				$TemplateUID = $value['TemplateUID']; 
				for($i=0; $i<$count; $i++)  
				{
					$entry_array[] = array(
						"CustomerUID"=>$CustomerUID,
						'ProductUID' => $ProductUID,
						'SubProductUID' => $SubProductUID,
						'TemplateUID' => $TemplateUID
					);
				}
				$this->db->insert_batch('mcustomertemplates', $entry_array);
			}

		}


		foreach ($Template_Workflow as $keys => $values) {	

			foreach ($values as $key => $value) {
				if(is_array($value)){

					foreach ($value as $key1 => $value1) {
						$data = array(
							"CustomerUID"=>$CustomerUID,
							'ProductUID' => $values['ProductUID'],
							'SubProductUID' => $values['SubProductUID'],
							'WorkflowModuleUID' => $value1
						);
						$this->db->insert('mcustomerworkflowmodules', $data);
					}

				}
			}

		}




	}


	function getzipcontents($CustomerZipCode = '')
	{
		if($CustomerZipCode){			
			$query = $this->db->query("SELECT * FROM `mcities` 
				LEFT JOIN mstates ON mcities.StateUID = mstates.StateUID 
				LEFT JOIN mcounties ON mcities.StateUID = mcounties.StateUID 
				AND mcities.CountyUID = mcounties.CountyUID
				WHERE mcities.ZipCode = '$CustomerZipCode'");
			return $query->row();
		} else {
			return false;
		}
	}

	function Get_Customer_SubProduct_ById($CustomerUID)
	{
		$this->db->select("*,mcustomerproducts.PriorityUID as SubproductPriorityUID");
		$this->db->from('mcustomerproducts');
		$this->db->join ( 'msubproducts', 'msubproducts.SubProductUID = mcustomerproducts.SubProductUID' , 'left' );
		$this->db->join ( 'mproducts', 'mproducts.ProductUID = mcustomerproducts.ProductUID' , 'left' );
		$this->db->where("CustomerUID",$CustomerUID);
		$query = $this->db->get();
		return $query->result_array();
	}

	function Get_Customer_SubProduct_ById_Prod($CustomerUID)
	{
		$this->db->select("*");
		$this->db->from('mcustomerproducts');
		$this->db->join ( 'msubproducts', 'msubproducts.SubProductUID = mcustomerproducts.SubProductUID' , 'left' );
		$this->db->join ( 'mproducts', 'mproducts.ProductUID = mcustomerproducts.ProductUID' , 'left' );
		$this->db->where("CustomerUID",$CustomerUID);
		$this->db->group_by("mcustomerproducts.ProductUID");
		$query = $this->db->get();
		return $query->result_array();
	} 	


	function get_customer_template_details($CustomerUID,$ProductUID,$SubProductUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mcustomertemplates' );
		$this->db->join ( 'msubproducts', 'msubproducts.SubProductUID = mcustomertemplates.SubProductUID' , 'left' );
		$this->db->join ( 'mproducts', 'mproducts.ProductUID = mcustomertemplates.ProductUID' , 'left' );
		$this->db->where('mcustomertemplates.SubProductUID',$SubProductUID);
		$this->db->where('mcustomertemplates.ProductUID',$ProductUID);
		$this->db->where('mcustomertemplates.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->row();

	}

	function get_customer_workflow_details($CustomerUID,$ProductUID,$SubProductUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mcustomerworkflowmodules' );
		$this->db->join ( 'mworkflowmodules', 'mworkflowmodules.WorkflowModuleUID = mcustomerworkflowmodules.WorkflowModuleUID' , 'left' );
		$this->db->join ( 'msubproducts', 'msubproducts.SubProductUID = mcustomerworkflowmodules.SubProductUID' , 'left' );
		$this->db->join ( 'mproducts', 'mproducts.ProductUID = mcustomerworkflowmodules.ProductUID' , 'left' );
		$this->db->where('mcustomerworkflowmodules.SubProductUID',$SubProductUID);
		$this->db->where('mcustomerworkflowmodules.ProductUID',$ProductUID);
		$this->db->where('mcustomerworkflowmodules.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->result_array();

	}

	function get_customer_optionalworkflow_details($CustomerUID,$ProductUID,$SubProductUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mcustomeroptionalworkflowmodules' );
		$this->db->join ( 'mworkflowmodules', 'mworkflowmodules.WorkflowModuleUID = mcustomeroptionalworkflowmodules.WorkflowModuleUID' , 'left' );
		$this->db->join ( 'msubproducts', 'msubproducts.SubProductUID = mcustomeroptionalworkflowmodules.SubProductUID' , 'left' );
		$this->db->join ( 'mproducts', 'mproducts.ProductUID = mcustomeroptionalworkflowmodules.ProductUID' , 'left' );
		$this->db->where('mcustomeroptionalworkflowmodules.SubProductUID',$SubProductUID);
		$this->db->where('mcustomeroptionalworkflowmodules.ProductUID',$ProductUID);
		$this->db->where('mcustomeroptionalworkflowmodules.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->result_array();

	}

	function get_customer_subproduct_details($CustomerUID,$ProductUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mcustomerproducts' );
		$this->db->join ( 'mproducts', 'mproducts.ProductUID = mcustomerproducts.ProductUID' , 'inner' );
		$this->db->join ( 'msubproducts', 'msubproducts.SubProductUID = mcustomerproducts.SubProductUID' , 'inner' );
		$this->db->where('mcustomerproducts.ProductUID',$ProductUID);
		$this->db->where('mcustomerproducts.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->result_array();

	}	


	function delete_customer($Id)
	{
		$query = $this->db->query("DELETE FROM mcustomers WHERE CustomerUID ='$Id'");

		if($this->db->affected_rows() > 0)
		{

			$this->db->select('*');
			$this->db->from('mcustomers');
			$this->db->where('CustomerUID',$Id);
			$query= $this->db->get('')->row(); 

			$data1['ModuleName']=$query->CustomerName.' '.'Customers_delete';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='mcustomers';
			$data1['UserUID']=$this->session->userdata('UserUID');               
			$this->common_model->Audittrail_insert($data1);
			
			return true;
		}
		else
		{
			return false;
		}
	}

	/*function ChangeParentCompanyByCustID($CustomerUID,$ParentCompanyUID){

		$ParentCompany = array("ParentCompany"=>$ParentCompanyUID);
		$ParentCompanyUID = array("ParentCompanyUID"=>0);

		$this->db->where(array("mcustomers.CustomerUID"=>$CustomerUID));
       	$this->db->update('mcustomers',$ParentCompany);

        $this->db->where(array("mcustomers.ParentCompanyUID"=>$CustomerUID));
        $result = $this->db->update('mcustomers',$ParentCompanyUID);

        return $result;

    }*/


    function ChangeParentCompanyByCustID($CustomerUID,$ParentCompanyCheck){

    	$ParentCompanyCheck = array("ParentCompanyCheck"=>$ParentCompanyCheck);
    	$STR = "";
    	if($ParentCompanyCheck == 1){
    		$STR = 'Parent Company Changed to <b>Yes</b>';
    	}else{
    		$STR = 'Parent Company Changed to <b>No</b>';
    	}

    	$InsetData = array(
    		'UserUID' => $this->loggedid,
    		'ModuleName' => 'Customers',
    		'Feature' => $CustomerUID,
    		'Content' => htmlentities($STR),
    		'DateTime' => date('Y-m-d H:i:s'));
    	$this->common_model->InsertAuditTrail($InsetData);

    	$this->db->where(array("mcustomers.CustomerUID"=>$CustomerUID));
    	$result = $this->db->update('mcustomers',$ParentCompanyCheck);

    	return $result;

    }

    function get_prioritieshours_date($CustomerUID,$PriorityUID,$ProductUID,$SubProductUID){

    	$this->db->select('*');
    	$this->db->from ( 'mcustomerproducttat' );

    	$this->db->where(array("CustomerUID"=>$CustomerUID,'PriorityUID'=>$PriorityUID,'ProductUID'=>$ProductUID,'SubProductUID'=>$SubProductUID));
    	$result = $this->db->get();

    	return $result->row();
    }

    function GetmcustomerdefaultProduct($CustomerUID)
    {
    	$query = $this->db->query("SELECT DefaultProductSubCode FROM mcustomerdefaultproduct WHERE CustomerUID ='$CustomerUID' ");
    	$result = $query->row();
    	return $result->DefaultProductSubCode;
    }
    function getCustomerDefaultProduct($CustomerUID)
    {

    	$query = $this->db->query("SELECT DefaultProductSubValue FROM mcustomerdefaultproduct WHERE CustomerUID ='$CustomerUID' ");
    	$data = $query->row();
    	if(!empty($data) && !empty($data->DefaultProductSubValue))
    	{
    		$SubProductUID = $data->DefaultProductSubValue;
    		$query = $this->db->query("SELECT SubProductUID,SubProductName FROM msubproducts WHERE SubProductUID IN ($SubProductUID)");
    		return $query->result_array();
    	}
    	else{
    		return false;
    	}

    }



    function saveAllCustomerDetails($CustomersDetails,$Prod_SubProducts,$Workflows_Array,$Priority_Array,$Template_Array,$DefaultProductSubCode,$DefaultProductSubValue,$CustomerApiDetails,$AutoBilling_Array,$vendorindividualcontact_array)
    {

    	$ParentCompany = isset($CustomersDetails['ParentCompany'])? 1:0;
    	$UserUID = $this->session->userdata('UserUID');

    	$GroupUID=$CustomersDetails['GroupUID'];

    	if(empty($GroupUID))
    	{
    		$result=$this->db->get_where('mgroups', array('Active'=> 1, 'DefaultGroup'=> 1));
    		$Group=$result->row();
    		$GroupUID=$Group->GroupUID;
    	}

    	$LoacationDetails =$this->getzipcontents($CustomersDetails['CustomerZipCode']);

    	$fieldArray = array(
    		"CustomerUID"=>$CustomersDetails['CustomerUID'],
    		"CustomerNumber"=>$CustomersDetails['CustomerNumber'],
    		"SAPReportName"=>$CustomersDetails['SAPReportName'],
    		"CustomerName"=>$CustomersDetails['CustomerName'],
    		"CustomerPContactName"=>$CustomersDetails['CustomerPContactName'],
    		"CustomerPContactMobileNo"=>$CustomersDetails['CustomerPContactMobileNo'],
    		"CustomerPContactEmailID"=>$CustomersDetails['CustomerPContactEmailID'],
    		"CustomerOrderAckEmailID"=>$CustomersDetails['CustomerOrderAckEmailID'],
    		"CustomerAddress1"=>$CustomersDetails['CustomerAddress1'],
    		"CustomerAddress2"=>$CustomersDetails['CustomerAddress2'],   
    		"CustomerZipCode"=>$CustomersDetails['CustomerZipCode'],  
    		"CustomerStateUID"=>$LoacationDetails->StateUID,
    		"CustomerCityUID"=>$LoacationDetails->CityUID,
    		"CustomerCountyUID"=>$LoacationDetails->CountyUID,
    		"CustomerOfficeNo"=>$CustomersDetails['CustomerOfficeNo'],
    		"CustomerFaxNo"=>$CustomersDetails['CustomerFaxNo'],
    		"CustomerWebsite"=>$CustomersDetails['CustomerWebsite'],
    		"AppUsageCost"=>$CustomersDetails['AppUsageCost'],
    		"PricingUID"=>$CustomersDetails['PricingUID'],
    		"PriorityUID"=>$CustomersDetails['PriorityUID'],
    		"DefaultTemplateUID"=>$CustomersDetails['DefaultTemplateUID'],
    		"TaxCertificateRequired"=>$CustomersDetails['TaxCertificateRequired'],
    		"ParentCompany"=>$ParentCompany,
    		"ParentCompanyUID"=>$CustomersDetails['ParentCompanyUID'],
    		"BundlePricing"=>isset($CustomersDetails['BundlePricing'])? 1:0,
    		"AdverseConditionsEnabled"=>isset($CustomersDetails['AdverseConditionsEnabled'])? 1:0,
    		"AutoBilling"=>isset($CustomersDetails['AutoBilling'])? 1:0,
			// "CustomerEmail" =>$SaveEmail,
		  		//"CustomerLogo"=>$CustomersDetails['CustomerLogo'],
    		"CreatedByUserUID"=>$UserUID,   
    		"CreatedOn"=>date('Y-m-d h:i:s'),  
    		"Active"=>1,
    		"ParentCompanyCheck"=>1
    	);

    	$this->db->trans_begin();

    	$result = $this->db->insert('mcustomers', $fieldArray);
    	$CustomerUID = $this->db->insert_id();

    	$InsetData = array(
    		'UserUID' => $this->loggedid,
    		'ModuleName' => 'Customers',
    		'Feature' => $CustomerUID,
    		'Content' => htmlentities('Customer: <b>'.$CustomersDetails['CustomerName'].'</b> is Created'),
    		'DateTime' => date('Y-m-d H:i:s'));
    	$this->common_model->InsertAuditTrail($InsetData);


    	$result=$this->db->get_where('mgroupcustomers', array('Active'=> 1, 'GroupUID'=> $GroupUID, 'GroupCustomerUID'=> $CustomerUID));
    	$groupcustomer_count=$result->num_rows();

    	if($groupcustomer_count==0)
    	{	
    		$groupcustomers['GroupUID']=$GroupUID;
    		$groupcustomers['GroupCustomerUID']=$CustomerUID;
    		$groupcustomers['Active']=1;
    		$this->db->insert('mgroupcustomers', $groupcustomers);
    	}

    	$AllCustomerMappingDetails = $this->saveAllCustomerMappingDetails($Prod_SubProducts,$Workflows_Array,$Priority_Array,$Template_Array,$CustomerUID,$AutoBilling_Array,$vendorindividualcontact_array);  
    	$this->saveDefaultProductSubProduct($CustomerUID,$DefaultProductSubCode,$DefaultProductSubValue); 
    	$this->saveCustomerApiInfo($CustomerUID,$CustomerApiDetails);

    	if ($this->db->trans_status() === FALSE)
    	{
    		$this->db->trans_rollback();
    		return false;
    	}else{
    		$this->db->trans_commit();
    		return $CustomerUID;
    	}
    }

    function saveAllCustomerMappingDetails($Prod_SubProducts,$Workflows_Array,$Priority_Array,$Template_Array,$CustomerUID,$AutoBilling_Array,$vendorindividualcontact_array)
    {
    	foreach ($Prod_SubProducts as $key => $value) 
    	{	
    		if(empty($value['SubProductUID'][0])){
    			return true;
    		}
    		else
    		{
    			$entry_array = array();
    			$count = count($value['SubProductUID']);
    			$SubProductUID = $value['SubProductUID']; 
    			$ProductUID = $value['ProductUID']; 
    			$ServiceTypeUID = $value['ServiceTypeUID']; 
    			$EmailAlert = $value['EmailAlert']; 
    			$OrderPolicyType = $value['OrderPolicyType']; 
    			for($i=0; $i<$count; $i++)  
    			{
    				$customer=$this->db->query('SELECT * FROM mcustomers WHERE CustomerUID='.$CustomerUID.'')->row();
    				$product=$this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$ProductUID.'')->row();
    				$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$SubProductUID[$i].'')->row();
					//RMS changes w.r.t.SubProduct
    				$entry_array[] = array(
    					"CustomerUID"=>$CustomerUID,
    					'SubProductUID' => $SubProductUID[$i],
    					'ProductUID' => $ProductUID,
    					'BulkImportFormat' => ($subproduct->RMS == 1) ? 'D2T-ReverseMortgage':'D2T-Standard',
    					'BulkImportTemplateName' => ($subproduct->RMS == 1) ? 'D2T-ReverseMortgage-BulkFormat.xlsx':'D2T-Std-BulkFormat.xlsx',
    					'AutoBilling' => ($AutoBilling_Array[$SubProductUID[$i]]) ? ($AutoBilling_Array[$SubProductUID[$i]]) : 0,
    					'VendorIndividualContact' => ($vendorindividualcontact_array[$SubProductUID[$i]]) ? ($vendorindividualcontact_array[$SubProductUID[$i]]) : 0,
    					'ServiceTypeUID' => (!empty($ServiceTypeUID) ? $ServiceTypeUID : null ),
    					'EmailAlert' => (!empty($EmailAlert) ? $EmailAlert : null ),
    					'OrderPolicyType' => (!empty($OrderPolicyType) ? $OrderPolicyType : null ),
    				);

    				$data1['ModuleName']=$customer->CustomerName.'-->'.$product->ProductName.'-->'.$subproduct->SubProductName.' '.'customer product subproduct_add';
    				$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    				$data1['DateTime']=date('y-m-d H:i:s');
    				$data1['TableName']='mcustomerproducts';
    				$data1['UserUID']=$this->session->userdata('UserUID');                
    				$this->common_model->Audittrail_insert($data1);
    			}

    			$this->db->insert_batch('mcustomerproducts', $entry_array);
    		}
    	}

    	foreach ($Template_Array as $key => $value) 
    	{	
    		if(empty($value['SubProductUID'][0]))
    		{
    			return true;
    		}
    		else
    		{
    			$entry_array = array();
    			$count = count($value['SubProductUID']);
    			$SubProductUID = $value['SubProductUID']; 
    			$ProductUID = $value['ProductUID']; 
    			$TemplateUID = $value['TemplateUID']; 
    			for($i=0; $i<$count; $i++)  
    			{

    				$customer=$this->db->query('SELECT * FROM mcustomers WHERE CustomerUID='.$CustomerUID.'')->row();
    				$product=$this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$ProductUID.'')->row();
    				$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$SubProductUID.'')->row();
					if(!empty($TemplateUID)){ // @auth Uba @desc Template not menatory for customer bug Fixed 
						$template=$this->db->query('SELECT * FROM mtemplates WHERE TemplateUID='.$TemplateUID.'')->row();
					//RMS changes w.r.t.SubProduct
						$entry_array[] = array(
							"CustomerUID"=>$CustomerUID,
							'ProductUID' => $ProductUID,
							'SubProductUID' => $SubProductUID,
							'TemplateUID' => $TemplateUID,
							'BulkImportFormat' => ($subproduct->RMS == 1) ? 'D2T-ReverseMortgage':'D2T-Standard',
							'BulkImportTemplateName' => ($subproduct->RMS == 1) ? 'D2T-ReverseMortgage-BulkFormat.xlsx':'D2T-Std-BulkFormat.xlsx'
						);
						$data1['ModuleName']=$customer->CustomerName.'-->'.$product->ProductName.'-->'.$subproduct->SubProductName.'-->'.$template->TemplateName.' '.'customer product subproduct template_add';
						$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
						$data1['DateTime']=date('y-m-d H:i:s');
						$data1['TableName']='mcustomertemplates';
						$data1['UserUID']=$this->session->userdata('UserUID');                
						$this->common_model->Audittrail_insert($data1);
					}
				}
				if(!empty($entry_array)){
					$this->db->insert_batch('mcustomertemplates', $entry_array);
				}
			}
		}

		foreach ($Workflows_Array as $keys => $values) 
		{

			$data = array(
				"CostCenterUID"=>(int)$values['CostCenterUID'],
				"CaseSensitivity"=>$values['CaseSensitivity'],
				"OrderSourceUID"=>(int)$values['OrderSourceUID'],
				"ServiceTypeUID"=>(int)$values['ServiceTypeUID'],
				"EmailAlert"=>(int)$values['EmailAlert'],
				"OrderPolicyType"=>$values['OrderPolicyType'],

			);

			// Customer mapped with Api Audit Log
			$this->db->select('*');
			$this->db->from('mcustomerproducts');
			$this->db->where(array('SubProductUID'=>$values['SubProductUID'],'ProductUID'=>$values['ProductUID']));
			$PreviousProduct = $this->db->get()->row();
			$ProductName=$this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$values['ProductUID'].'')->row()->ProductName;
			$SubProductName=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$values['SubProductUID'].'')->row()->SubProductName;
			
			if(!empty($values['OrderSourceUID'])){ 
				$ApiName = $this->db->query('SELECT OrderSourceName FROM mApiTitlePlatform WHERE OrderSourceUID='.$values['OrderSourceUID'].'')->row()->OrderSourceName;
			}
			if(!empty($ApiName)&&!empty($ProductName)&&!empty($SubProductName)){
			// Audit Log Api Assignment
				$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
					,'Content' => htmlentities('<b>'.$ApiName.'</b> API is <b>Configured</b> for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b>'));
				$this->common_model->InsertAuditTrail($InsetData);
			}
			$this->db->where(array('SubProductUID'=>$values['SubProductUID'],'SubProductUID'=>$values['SubProductUID'],'ProductUID'=>$values['ProductUID']));
			$this->db->update('mcustomerproducts', $data);
			// Customer mapped with Api Audit Log END

			foreach ($values['OptionalWorkflowModuleUID'] as $key1 => $value1) 
			{
				$data = array(
					"CustomerUID"=>$CustomerUID,
					'ProductUID' => $values['ProductUID'],
					'SubProductUID' => $values['SubProductUID'],
					'WorkflowModuleUID' => $value1
				);
				$this->db->insert('mcustomeroptionalworkflowmodules', $data);

				$customer=$this->db->query('SELECT * FROM mcustomers WHERE CustomerUID='.$CustomerUID.'')->row();
				$product=$this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$values['ProductUID'].'')->row();
				$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$values['SubProductUID'].'')->row();
				$workflowmodule=$this->db->query('SELECT * FROM mworkflowmodules WHERE WorkflowModuleUID='.$value1.'')->row();

				$data1['ModuleName']=$customer->CustomerName.'-->'.$product->ProductName.'-->'.$subproduct->SubProductName.'-->'.$workflowmodule->WorkflowModuleName.' '.'customer product subproduct workflowmodule_add';
				$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
				$data1['DateTime']=date('y-m-d H:i:s');
				$data1['TableName']='mcustomerworkflowmodules';
				$data1['UserUID']=$this->session->userdata('UserUID');                
				$this->common_model->Audittrail_insert($data1);
			}


			foreach ($values['WorkflowModuleUID'] as $key1 => $value1) 
			{
				$data = array(
					"CustomerUID"=>$CustomerUID,
					'ProductUID' => $values['ProductUID'],
					'SubProductUID' => $values['SubProductUID'],
					'WorkflowModuleUID' => $value1
				);
				$this->db->insert('mcustomerworkflowmodules', $data);

				$customer=$this->db->query('SELECT * FROM mcustomers WHERE CustomerUID='.$CustomerUID.'')->row();
				$product=$this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$values['ProductUID'].'')->row();
				$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$values['SubProductUID'].'')->row();
				$workflowmodule=$this->db->query('SELECT * FROM mworkflowmodules WHERE WorkflowModuleUID='.$value1.'')->row();

				$data1['ModuleName']=$customer->CustomerName.'-->'.$product->ProductName.'-->'.$subproduct->SubProductName.'-->'.$workflowmodule->WorkflowModuleName.' '.'customer product subproduct workflowmodule_add';
				$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
				$data1['DateTime']=date('y-m-d H:i:s');
				$data1['TableName']='mcustomerworkflowmodules';
				$data1['UserUID']=$this->session->userdata('UserUID');                
				$this->common_model->Audittrail_insert($data1);
			}

			foreach ($values['billing_trigger'] as $billing_triggerkey => $billing_triggervalue) 
			{
				$data = array(
					"CustomerUID"=>$CustomerUID,
					'ProductUID' => $values['ProductUID'],
					'SubProductUID' => $values['SubProductUID'],
					'WorkflowModuleUID' => $billing_triggervalue
				);
				$this->db->insert('mCustomerBillingTrigger', $data);

				$customer=$this->db->query('SELECT * FROM mcustomers WHERE CustomerUID='.$CustomerUID.'')->row();
				$product=$this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$values['ProductUID'].'')->row();
				$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$values['SubProductUID'].'')->row();
				$workflowmodule=$this->db->query('SELECT * FROM mworkflowmodules WHERE WorkflowModuleUID='.$value1.'')->row();

				$data1['ModuleName']=$customer->CustomerName.'-->'.$product->ProductName.'-->'.$subproduct->SubProductName.'-->'.$workflowmodule->WorkflowModuleName.' '.'customer product subproduct billingtrigger_add';
				$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
				$data1['DateTime']=date('y-m-d H:i:s');
				$data1['TableName']='mCustomerBillingTrigger';
				$data1['UserUID']=$this->session->userdata('UserUID');                
				$this->common_model->Audittrail_insert($data1);
			}

		}


		foreach ($Priority_Array as $priorityarraykey => $priorityarrayvalue) 
		{

			/*@author Naveenkumar @purpose set defaulted Priority values in subproduct*/ 
			$customerproducts_data = array(
				"PriorityUID"=>(int)$priorityarrayvalue['Sub_PriorityUID']
			);
			$this->db->where(array('CustomerUID'=>$CustomerUID,'SubProductUID'=>$priorityarrayvalue['SubProductUID'],'ProductUID'=>$priorityarrayvalue['ProductUID']));

			$this->db->update('mcustomerproducts', $customerproducts_data);
			

			foreach ($priorityarrayvalue['priorityhour'] as $key => $value) 
			{
				if($value != '')
				{
					$data = array(
						"CustomerUID"=>$CustomerUID,
						'ProductUID' => $priorityarrayvalue['ProductUID'],
						'SubProductUID' => $priorityarrayvalue['SubProductUID'],
						'PriorityUID' => $key,
						'PriorityTime' => $value,
						'SkipOrderOpenDate' => $priorityarrayvalue['SkipOrderOpenDate'][$key],
					);
					$this->db->insert('mcustomerproducttat', $data);

					$customer=$this->db->query('SELECT * FROM mcustomers WHERE CustomerUID='.$CustomerUID.'')->row();
					$product=$this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$priorityarrayvalue['ProductUID'].'')->row();
					$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$priorityarrayvalue['SubProductUID'].'')->row();
					$priority=$this->db->query('SELECT * FROM morderpriority WHERE PriorityUID='.$key.'')->row();

					$data1['ModuleName']=$customer->CustomerName.'-->'.$product->ProductName.'-->'.$subproduct->SubProductName.'-->'.$priority->PriorityName.' '.'customer product subproduct priority tat_add';
					$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
					$data1['DateTime']=date('y-m-d H:i:s');
					$data1['TableName']='mcustomerproducttat';
					$data1['UserUID']=$this->session->userdata('UserUID');                
					$this->common_model->Audittrail_insert($data1);
				}
			}
		}
	}

	function saveCustomerApiInfo($CustomerUID,$CustomerApiDetails){
	// @auth Ubakarasamy @desc Audit Log is Added 	
		$this->db->select('*');
		$this->db->from('mCustomerApiInfo');
		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$PreviousCustomerApiInfos = $this->db->get()->result();
		$ApiName = '';
	$PresentArr = [];// Previous 
	$CurrentArr = [];// Current
	foreach ($PreviousCustomerApiInfos as $key => $Api) {
		$str = $Api->CustomerUID.','.$Api->ProductUID.','.$Api->SubProductUID.'';
		array_push($PresentArr,$str);
	}


	$this->db->where(array("CustomerUID"=>$CustomerUID));
	$this->db->delete('mCustomerApiInfo');

	foreach ($CustomerApiDetails as $key => $value) 
	{ 
		if(empty($value['SubProductUID'][0])){
			return true;
		}
		else
		{
			$count = count($value['SubProductUID']);
			$SubProductUID = $value['SubProductUID']; 
			$ProductUID = $value['ProductUID']; 

			$ProductName = $this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$ProductUID.'')->row()->ProductName;
			for($i=0; $i<$count; $i++)  
			{
				if(!empty($value['Integration'])){
					$Result = $this->db->query('SELECT OrderSourceName FROM mApiTitlePlatform WHERE OrderSourceUID='.$value['Integration'].'')->row();
					$ApiName = $Result->OrderSourceName;
				}
				$SubProductName = $this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$SubProductUID[$i].'')->row()->SubProductName;
				$str2 = $CustomerUID.','.$ProductUID.','.$SubProductUID[$i].'';
				array_push($CurrentArr,$str2);
				if(!in_array($str2,$PresentArr)&&!empty($ApiName)){
				//Added
					$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
						,'Content' => htmlentities(' <b>'.$ApiName.'</b> API is <b>Configured</b> for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b>'));
					$this->common_model->InsertAuditTrail($InsetData);
				}

				$entry_array = array(
					"CustomerUID" => $CustomerUID,
					'SubProductUID' => $SubProductUID[$i],
					'ProductUID' => $ProductUID,
					'UserName' => $value['UserName'],
					'Password' => $value['Password'],
					'ClientID' => $value['ClientID'],
					'ClientKey' => $value['ClientKey'],
					'OrderSourceUID' => $value['Integration'],
					'OrderSourceName' => $Result->OrderSourceName,
					'Comment' => $value['Comments'],
					'Createdby' => $this->session->userdata('UserUID'),
					'CreatedDate' => date('y-m-d H:i:s')
				);

				if(!empty($PreviousCustomerApiInfos)){
					foreach ($PreviousCustomerApiInfos as $key => $ApiInfo) {
						$msg = '';
						if($ApiInfo->CustomerUID == $CustomerUID 
							&& $ApiInfo->SubProductUID == $SubProductUID[$i] 
							&& $ApiInfo->ProductUID == $ProductUID ){

						  // Client Product Api Info updated
							if($ApiInfo->UserName != $value['UserName']){
								$msg = 'Client Product API Info <b>UserName</b> changed from <b>'.$ApiInfo->UserName.'</b> to <b>'.$value['UserName'].'</b>';
								$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
									,'Content' => htmlentities($msg.' for Product <b>'.$ProductName.' and SubProduct '.$SubProductName.'</b>'));
								$this->common_model->InsertAuditTrail($InsetData);
							}
							if($ApiInfo->Password != $value['Password']){
								$msg = 'Client Product API Info <b>Password</b> changed';
								$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
									,'Content' => htmlentities($msg.' for Product <b>'.$ProductName.' and SubProduct '.$SubProductName.'</b>'));
								$this->common_model->InsertAuditTrail($InsetData);
							}
							if($ApiInfo->ClientID != $value['ClientID']){
								$msg = 'Client Product API Info <b>ClientID</b> changed from <b>'.$ApiInfo->ClientID.'</b> to <b>'.$value['ClientID'].'</b>';
								$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
									,'Content' => htmlentities($msg.' for Product <b>'.$ProductName.' and SubProduct '.$SubProductName.'</b>'));
								$this->common_model->InsertAuditTrail($InsetData);
							}
							if($ApiInfo->ClientKey != $value['ClientKey']){
								$msg = 'Client Product API Info <b>ClientKey</b> changed from <b>'.$ApiInfo->ClientKey.'</b> to <b>'.$value['ClientKey'].'</b>';
								$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
									,'Content' => htmlentities($msg.' for Product <b>'.$ProductName.' and SubProduct '.$SubProductName.'</b>'));
								$this->common_model->InsertAuditTrail($InsetData);
							}
							if($ApiInfo->OrderSourceName != $Result->OrderSourceName){
								$msg = 'Client Product API Info <b>API Type</b> changed from <b>'.$ApiInfo->OrderSourceName.'</b> to <b>'.$Result->OrderSourceName.'</b>';
								$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
									,'Content' => htmlentities($msg.' for Product <b>'.$ProductName.' and SubProduct '.$SubProductName.'</b>'));
								$this->common_model->InsertAuditTrail($InsetData);
							}
							if($ApiInfo->Comment != $value['Comments']){
								$msg = 'Client Product API Info <b>Comment</b> changed from <b>'.$ApiInfo->Comment.'</b> to <b>'.$value['Comments'].'</b>';
								$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
									,'Content' => htmlentities($msg.' for Product <b>'.$ProductName.' and SubProduct '.$SubProductName.'</b>'));
								$this->common_model->InsertAuditTrail($InsetData);
							}
						}
					}
				}


				$this->db->insert('mCustomerApiInfo', $entry_array);

			// Map OrderSourceUID In mcustomerproducts
				$this->db->where('CustomerUID',$CustomerUID);
				$this->db->where('ProductUID',$ProductUID);
				$this->db->where('SubProductUID',$SubProductUID[$i]);
				$this->db->update('mcustomerproducts',array('OrderSourceUID'=>$value['Integration']));
			}
		}
	}

	if(!empty($PresentArr) && !empty($CurrentArr)){
		foreach ($PresentArr as $key => $value) {
			if(in_array($value,$CurrentArr)){
				$Val = explode(',',$value);
				$ProductName = $this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$Val[1].'')->row()->ProductName;
				$SubProductName = $this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$Val[2].'')->row()->SubProductName;
					//Removed
				if(!empty($ApiName)){
					$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
						,'Content' => htmlentities('<b>'.$ApiName.'</b> API Configuration is <b>Removed</b> for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b>'));
					$this->common_model->InsertAuditTrail($InsetData);
				}
			}
		}
	}else if(!empty($PresentArr) && empty($CurrentArr)){
			// Removed
		foreach ($PresentArr as $key => $value) {
			$Val = explode(',',$value);
			$ProductName = $this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$Val[1].'')->row()->ProductName;
			$SubProductName = $this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$Val[2].'')->row()->SubProductName;
					//Removed
			if(!empty($ApiName)){
				$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
					,'Content' => htmlentities( '<b>'.$ApiName.'</b> API Configuration is <b>Removed</b> for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b>'));
				$this->common_model->InsertAuditTrail($InsetData);
			}
		}
	}
}


function UpdateAllCustomerDetails($CustomersDetails,$Prod_SubProducts,$Workflows_Array,$Priority_Array,$Template_Array,$DefaultProductSubCode,$DefaultProductSubValue,$ExplodeDeleteDocumentID,$CustomerApiDetails,$AutoBilling_Array,$vendorindividualcontact_array)
{
	$ParentCompany = isset($CustomersDetails['ParentCompany'])? 1:0;
	$UserUID = $this->session->userdata('UserUID');

		/*$GroupUID=$CustomersDetails['GroupUID'];

		if(empty($GroupUID))
		{
			$result=$this->db->get_where('mgroups', array('Active'=> 1, 'DefaultGroup'=> 1));
			$Group=$result->row();
			$GroupUID=$Group->GroupUID;
		}*/

		$LoacationDetails =$this->getzipcontents($CustomersDetails['CustomerZipCode']);

		$fieldArray = array(
			"CustomerUID"=>$CustomersDetails['CustomerUID'],
			"CustomerNumber"=>$CustomersDetails['CustomerNumber'],
			"SAPReportName"=>$CustomersDetails['SAPReportName'],
			"CustomerName"=>$CustomersDetails['CustomerName'],
			"CustomerPContactName"=>$CustomersDetails['CustomerPContactName'],
			"CustomerPContactMobileNo"=>$CustomersDetails['CustomerPContactMobileNo'],
			"CustomerPContactEmailID"=>$CustomersDetails['CustomerPContactEmailID'],
			"CustomerOrderAckEmailID"=>$CustomersDetails['CustomerOrderAckEmailID'],
			"CustomerAddress1"=>$CustomersDetails['CustomerAddress1'],
			"CustomerAddress2"=>$CustomersDetails['CustomerAddress2'],   
			"CustomerZipCode"=>$CustomersDetails['CustomerZipCode'],  
			"CustomerStateUID"=>$LoacationDetails->StateUID,
			"CustomerCityUID"=>$LoacationDetails->CityUID,
			"CustomerCountyUID"=>$LoacationDetails->CountyUID,
			"CustomerOfficeNo"=>$CustomersDetails['CustomerOfficeNo'],
			"CustomerFaxNo"=>$CustomersDetails['CustomerFaxNo'],
			"CustomerWebsite"=>$CustomersDetails['CustomerWebsite'],
			"AppUsageCost"=>$CustomersDetails['AppUsageCost'],
			"PricingUID"=>$CustomersDetails['PricingUID'],
			"PriorityUID"=>$CustomersDetails['PriorityUID'],
			"DefaultTemplateUID"=>$CustomersDetails['DefaultTemplateUID'],
			"TaxCertificateRequired"=>$CustomersDetails['TaxCertificateRequired'],
			"ParentCompany"=>$ParentCompany,
			"ParentCompanyUID"=>($ParentCompany==1)?$CustomersDetails['ParentCompanyUID']:NULL,
			"BundlePricing"=>isset($CustomersDetails['BundlePricing'])? 1:0,
			"AdverseConditionsEnabled"=>isset($CustomersDetails['AdverseConditionsEnabled'])? 1:0,
			"AutoBilling"=>isset($CustomersDetails['AutoBilling'])? 1:0,
			"CreatedByUserUID"=>$UserUID,   
			"CreatedOn"=>date('Y-m-d h:i:s'),  
			"Active"=>1,
			"ParentCompanyCheck"=>1
		);	

		$this->db->trans_begin();


		/**************** FOR AUDIT RAIL FEATURE *************************/

		$AuditIgnoreArray = [
			'CustomerUID',
	// 'PricingUID',
			'CreatedByUserUID',
			'CreatedOn'
		];
// CHECK BOX LIST		
		$CheckBoxList = [
			'ParentCompany',
			'Active',
			'BundlePricing',
			'AdverseConditionsEnabled',
			'AutoBilling'
		];
// PriorityUID ParentCompanyUID
		foreach ($fieldArray as $Fieldkey => $Fieldvalue) {
			if(!in_array($Fieldkey,$AuditIgnoreArray)){
				$Changed = $this->common_model->CheckAudit(
					'CustomerUID',
					$CustomersDetails['CustomerUID'],
					'mcustomers',
					$Fieldkey,
					$Fieldvalue
				);

				if(in_array($Fieldkey,$CheckBoxList) && (int)$Changed == 1 && $Fieldvalue == 0){
					$Changed = 'Yes';
					$Fieldvalue = 'No';
// INSERT AUDIT RAIL			
					$InsetData = array(
						'UserUID' => $this->loggedid,
						'ModuleName' => 'Customers',
						'OldValue' => $Changed,
						'NewValue' => $Fieldvalue,
						'Feature' => $CustomersDetails['CustomerUID'],
						'Content' => htmlentities('<b>'.$Fieldkey.'</b> Changed from <b>'.$Changed.'</b> to <b>'.$Fieldvalue.'</b>'),
						'DateTime' => date('Y-m-d H:i:s'));
					$this->common_model->InsertAuditTrail($InsetData);
				}else if(in_array($Fieldkey,$CheckBoxList) && $Changed == 0 && $Fieldvalue == 1){

					if($Changed != 'FALSE'){
						$Changed = 'No';
						$Fieldvalue = 'Yes';
	// INSERT AUDIT RAIL
						$InsetData = array(
							'UserUID' => $this->loggedid,
							'ModuleName' => 'Customers',
							'OldValue' => $Changed,
							'NewValue' => $Fieldvalue,
							'Feature' => $CustomersDetails['CustomerUID'],
							'Content' => htmlentities('<b>'.$Fieldkey.'</b> Changed from <b>'.$Changed.'</b> to <b>'.$Fieldvalue.'</b>'),
							'DateTime' => date('Y-m-d H:i:s'));
						$this->common_model->InsertAuditTrail($InsetData);
					}

				}

//PriorityUID
				if($Fieldkey == 'PriorityUID'){
					$Fieldkey = 'Priority';
					$Prioritys = $this->common_model->GetPriorityDetails();
					foreach ($Prioritys as $Priority) {
						if($Priority->PriorityUID == $Fieldvalue){
							$Fieldvalue = $Priority->PriorityName;
						}
						if($Priority->PriorityUID == $Changed){
							$Changed = $Priority->PriorityName;
						}
					}
				}
// DefaultTemplateUID
				if($Fieldkey == 'DefaultTemplateUID'){
					$Fieldkey = 'Client Template';
					if(!empty($Fieldvalue) && ($Changed != 'FALSE') && !empty($Changed)){
						$Fieldvalue = $this->db->query("SELECT * FROM mtemplates WHERE TemplateUID = ".$Fieldvalue."")->row()->TemplateName;
						$Changed = $this->db->query("SELECT * FROM mtemplates WHERE TemplateUID = ".$Changed."")->row()->TemplateName;
					}else if(!empty($Fieldvalue) && empty($Changed)){
						$Changed = 'NA';
						$Fieldvalue = $this->db->query("SELECT * FROM mtemplates WHERE TemplateUID = ".$Fieldvalue."")->row()->TemplateName;
					}
				}
//State County City
				if($Fieldkey == 'CustomerCityUID'){
					$Fieldkey = 'City';
					$Fieldvalue = $this->common_model->GetCitybyUID($Fieldvalue)[0]->CityName;
					$Changed = $this->common_model->GetCitybyUID($Changed)[0]->CityName;
				}
				if($Fieldkey == 'CustomerCountyUID'){
					$Fieldkey = 'County';
					$Fieldvalue = $this->common_model->GetCountybyUID($Fieldvalue)[0]->CountyName;
					$Changed = $this->common_model->GetCountybyUID($Changed)[0]->CountyName;
				}
				if($Fieldkey == 'CustomerStateUID'){
					$Fieldkey = 'State';
					$Fieldvalue = $this->common_model->GetStatebyUID($Fieldvalue)[0]->StateName;
					$Changed = $this->common_model->GetStatebyUID($Changed)[0]->StateName;
				}

				/**** */
//PricingUID
				if($Fieldkey == 'PricingUID'){
					$this->db->select('PricingName');
					$this->db->from('mpricing');	
					$this->db->where('PricingUID',$fieldArray['PricingUID']);
					$Pricing1 = $this->db->get()->row();

					$this->db->select('PricingName');
					$this->db->from('mpricing');
					$this->db->where('PricingUID',$Changed);
					$Pricing2 = $this->db->get()->row();
					$Fieldkey = 'Pricing';
					$Fieldvalue = $Pricing1->PricingName;
					$Changed = $Pricing2->PricingName;
				}
//ParentCompanyUID
				if($Fieldkey == 'ParentCompanyUID'){
					$Fieldkey = 'Parent Company Name';
					$Fieldvalue = $this->GetParentCompanyNameDetails($Fieldvalue)->CustomerName;
					$Changed = $this->GetParentCompanyNameDetails($Changed)->CustomerName;
					if(empty($Changed)){$Changed='NA';}
					if(empty($Fieldvalue)){$Fieldvalue='NA';}
				}

				if($Changed != 'FALSE' && !in_array($Fieldkey,$CheckBoxList)){
					if(trim($Fieldvalue) != trim($Changed)){
						if($Fieldkey == 'SAPReportName'){$Fieldkey='Client BankAccountNumber';}
						if($Fieldkey == 'CustomerPContactName'){$Fieldkey='Client ContactName';}
						if($Fieldkey == 'CustomerPContactMobileNo'){$Fieldkey='Client ContactMobileNo';}
						if($Fieldkey == 'CustomerPContactEmailID'){$Fieldkey='Client ContactEmailID';}
						if($Fieldkey == 'CustomerOrderAckEmailID'){$Fieldkey='Client Order Acknowledge EmailID';}
						if($Changed != "" && $Fieldvalue != ""){
		// INSERT AUDIT RAIL			
							$InsetData = array(
								'UserUID' => $this->loggedid,
								'ModuleName' => 'Customers',
								'OldValue' => $Changed,
								'NewValue' => $Fieldvalue,
								'Feature' => $CustomersDetails['CustomerUID'],
								'Content' => htmlentities('<b>'.$Fieldkey.'</b> Changed from <b>'.$Changed.'</b> to <b>'.$Fieldvalue.'</b>'),
								'DateTime' => date('Y-m-d H:i:s'));
							$this->common_model->InsertAuditTrail($InsetData);
						}

					}	
				}
			}
		}


		$this->db->where(array("CustomerUID"=>$CustomersDetails['CustomerUID']));        
		$result = $this->db->update('mcustomers', $fieldArray);


		/**************** FOR AUDIT RAIL FEATURE END *************************/

		/*$result=$this->db->get_where('mgroupcustomers', array('Active'=> 1, 'GroupUID'=> $GroupUID, 'GroupCustomerUID'=> $CustomerUID));
		$groupcustomer_count=$result->num_rows();

		if($groupcustomer_count==0)
		{	
			$groupcustomers['GroupUID']=$GroupUID;
			$groupcustomers['GroupCustomerUID']=$CustomerUID;
			$groupcustomers['Active']=1;
			$this->db->insert('mgroupcustomers', $groupcustomers);
		}
*/
		$CustomerUID=$CustomersDetails['CustomerUID'];

		$AllCustomerMappingDetails = $this->UpdateAllCustomerMappingDetails($Prod_SubProducts,$Workflows_Array,$Priority_Array,$Template_Array,$CustomerUID,$AutoBilling_Array,$vendorindividualcontact_array);

		$this->updateDefaultProductSubProduct($CustomerUID,$DefaultProductSubCode,$DefaultProductSubValue);    
		if(count(array_filter($ExplodeDeleteDocumentID))>0) {    
			$this->deletedocument($ExplodeDeleteDocumentID,$CustomerUID); 
		}
		$this->saveCustomerApiInfo($CustomerUID,$CustomerApiDetails);
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}else{
			$this->db->trans_commit();
			return $CustomerUID;
		}	

	}

	function UpdateAllCustomerMappingDetails($Prod_SubProducts,$Workflows_Array,$Priority_Array,$Template_Array,$CustomerUID,$AutoBilling_Array,$vendorindividualcontact_array)
	{
		$customer=$this->db->query('SELECT * FROM mcustomers WHERE CustomerUID='.$CustomerUID.'')->row();


		//@author Naveenkumar @purpose Audit track.
		foreach ($Priority_Array as $priorityarraykey => $priorityarrayvalue) {
			if($priorityarrayvalue['Sub_PriorityUID']) {

				$this->db->select('PriorityUID');
				$this->db->from('mcustomerproducts');
				$this->db->where('CustomerUID', $CustomerUID);
				$this->db->where('SubProductUID', $priorityarrayvalue['SubProductUID']);
				$this->db->where('ProductUID', $priorityarrayvalue['ProductUID']);
				/*$this->db->where('PriorityUID', $priorityarrayvalue['Sub_PriorityUID']);*/
				$exists_priority = $this->db->get()->result();

				if(!empty($exists_priority)) {

					foreach ($exists_priority as $key => $SubPriorityUID) {
						if($SubPriorityUID->PriorityUID != $priorityarrayvalue['Sub_PriorityUID']) {

							//get priority value
							if($SubPriorityUID->PriorityUID){
								$oldvalue = $this->common_model->get_prioritytime($SubPriorityUID->PriorityUID);
							}
							if($priorityarrayvalue['Sub_PriorityUID']){
								$newvalue = $this->common_model->get_prioritytime($priorityarrayvalue['Sub_PriorityUID']);
							}
							$oldvalue = $oldvalue ? $oldvalue->PriorityName : "";
							$newvalue = $newvalue ? $newvalue->PriorityName : "";

							//get subproduct name
							$this->db->select ( 'SubProductName' );
							$this->db->from ( 'msubproducts' );
							$this->db->where ('SubProductUID',$priorityarrayvalue['SubProductUID']);
							$query = $this->db->get();
							$SubProduct = $query->row();

							if(!empty($SubProduct)){
								$SubProductName = $SubProduct ? $SubProduct->SubProductName : "";
							}

							$Fieldkey = 'PriorityUID';

							// INSERT AUDIT RAIL			
							$InsetData = array(
								'UserUID' => $this->loggedid,
								'ModuleName' => 'Customers',
								'OldValue' => $oldvalue,
								'NewValue' => $newvalue,
								'Feature' => $CustomerUID,
								'Content' => htmlentities('<b>'.$SubProductName.'/'.$Fieldkey.'</b> Changed from <b>'.$oldvalue.'</b> to <b>'.$newvalue.'</b>'),
								'DateTime' => date('Y-m-d H:i:s'));
							$this->common_model->InsertAuditTrail($InsetData);
						}

					}

				}
			}
		}

		// Customer Products
		$CustomerProducts = [];
		$this->db->select('*');
		$this->db->from('mcustomerproducts');
		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$CProducts = $this->db->get()->result_array();
		$OldAutoBillingArr = array_column($CProducts, "AutoBilling", "SubProductUID");
		$CustomerProducts = array_column($CProducts,'ProductUID');
		$CustomerSubProducts = array_column($CProducts,'SubProductUID'); 
		$New_Prod_SubProducts = array_column($Prod_SubProducts,'ProductUID');
		$New_SubProducts = array_column($Prod_SubProducts,'SubProductUID');
		$New_SubProducts_arr = [];
		foreach($New_SubProducts as $key){foreach($key as $key2){array_push($New_SubProducts_arr,$key2);}}
		foreach ($CustomerProducts as $keyvalue) {
			if(!in_array($keyvalue,$New_Prod_SubProducts)){
				// REMOVED PRODUCT
				$product = $this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$keyvalue.'')->row();
				$InsetData = array(
					'UserUID' => $this->loggedid,
					'ModuleName' => 'Customers',
					'Feature' => $CustomerUID,
					'Content' => htmlentities('Product: <b>'.$product->ProductName.'</b> is Removed'),
					'DateTime' => date('Y-m-d H:i:s'));
				$this->common_model->InsertAuditTrail($InsetData);
			}
		}

		foreach ($CustomerSubProducts as $SubPr) {
			if(!in_array($SubPr,$New_SubProducts_arr)){
				// REMOVED SUB PRODUCT
				$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$SubPr.'')->row();
				$InsetData = array(
					'UserUID' => $this->loggedid,
					'ModuleName' => 'Customers',
					'Feature' => $CustomerUID,
					'Content' => htmlentities('Sub Product: <b>'.$subproduct->SubProductName.'</b> is Removed'),
					'DateTime' => date('Y-m-d H:i:s'));
				$this->common_model->InsertAuditTrail($InsetData);
			}
		}

		/**************************** Customer Api Mapping Audit*/
		foreach ($Workflows_Array as $keys => $values) 
		{	
			if($values['SubProductUID'] && $values['ProductUID'])
			{
				// Customer mapped with Api Audit Log
				$str = '';
				$this->db->select('*');
				$this->db->from('mcustomerproducts');
				$this->db->where(array('SubProductUID'=>$values['SubProductUID'],'ProductUID'=>$values['ProductUID'],'CustomerUID' => $CustomerUID));
				$PreviousProduct = $this->db->get()->row();
				$ProductName=$this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$values['ProductUID'].'')->row()->ProductName;
				$SubProductName=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$values['SubProductUID'].'')->row()->SubProductName;
				if((!empty($PreviousProduct->OrderSourceUID) && !empty($values['OrderSourceUID'])) && 
					($PreviousProduct->OrderSourceUID != $values['OrderSourceUID'])){
					$ApiName1 = $this->db->query('SELECT OrderSourceName FROM mApiTitlePlatform WHERE OrderSourceUID='.$PreviousProduct->OrderSourceUID.'')->row()->OrderSourceName;
				$ApiName2 = $this->db->query('SELECT OrderSourceName FROM mApiTitlePlatform WHERE OrderSourceUID='.(int)$values['OrderSourceUID'].'')->row()->OrderSourceName;
				$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> API Platform is <b>Reconfigured</b> to <b>'.$ApiName2.'</b> from <b>'.$ApiName1.'</b>';
			}else if(empty($PreviousProduct->OrderSourceUID) && !empty($values['OrderSourceUID'])){
				$ApiName2 = $this->db->query('SELECT OrderSourceName FROM mApiTitlePlatform WHERE OrderSourceUID='.(int)$values['OrderSourceUID'].'')->row()->OrderSourceName;
				$str = '<b>'.$ApiName2.'</b> API is <b>Configured</b> for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b>';
			}
			if($str != ''){
				// Audit Log Api Assignment
				$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
					,'Content' => htmlentities($str));
				$this->common_model->InsertAuditTrail($InsetData);
				$str = '';
			}

            //ServiceType    
		// 	if((!empty($PreviousProduct->ServiceTypeUID) && !empty($values['ServiceTypeUID'])) && 
		// 		($PreviousProduct->ServiceTypeUID != $values['ServiceTypeUID'])){
		// 		$ServiceTypeName1 = $this->db->query('SELECT ServiceTypeName FROM mServiceType WHERE ServiceTypeUID='.$PreviousProduct->ServiceTypeUID.'')->row()->ServiceTypeName;
		// 	$ServiceTypeName2 = $this->db->query('SELECT ServiceTypeName FROM mServiceType WHERE ServiceTypeUID='.(int)$values['ServiceTypeUID'].'')->row()->ServiceTypeName;
		// 	$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> ServiceType is <b>changed</b> to <b>'.$ServiceTypeName2.'</b> from <b>'.$ServiceTypeName1.'</b>';
		// }else if(empty($PreviousProduct->ServiceTypeUID) && !empty($values['ServiceTypeUID'])){
		// 	$ServiceTypeName2 = $this->db->query('SELECT ServiceTypeName FROM mServiceType WHERE ServiceTypeUID='.(int)$values['ServiceTypeUID'].'')->row()->ServiceTypeName;
		// 	$str = '<b>'.$ServiceTypeName2.'</b> ServiceType is <b>changed </b> for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b>';
		// }
		if($str != ''){
				// Audit Log Service type 
			$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
				,'Content' => htmlentities($str));
			$this->common_model->InsertAuditTrail($InsetData);
			$str = '';
		}


			// Email Alert
		if(!empty($PreviousProduct->EmailAlert) && empty($values['EmailAlert'])){
			$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> EmailAlert is <b>Changed</b> from <b>Yes</b> to <b>No</b>';
		}else if(empty($PreviousProduct->EmailAlert) && !empty($values['EmailAlert'])){
			$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> EmailAlert is <b>Changed</b> from <b>No</b> to <b>Yes</b>';
		}
		if($str != ''){
							// EmailAlert Audit
			$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
				,'Content' => htmlentities($str));
			$this->common_model->InsertAuditTrail($InsetData);
			$str = '';
		}


			// CostCenter
		if((!empty($PreviousProduct->CostCenterUID) && !empty($values['CostCenterUID']))
			&& ($PreviousProduct->CostCenterUID != $values['CostCenterUID'])){
			$CostCenterName1 = $this->db->query('SELECT CostCenterName FROM mCostCenter WHERE CostCenterUID='.$PreviousProduct->CostCenterUID.'')->row()->CostCenterName;
		$CostCenterName2 = $this->db->query('SELECT CostCenterName FROM mCostCenter WHERE CostCenterUID='.$values['CostCenterUID'].'')->row()->CostCenterName;
		$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> CostCenter is <b>Changed</b> from <b>'.$CostCenterName1.'</b> to <b>'.$CostCenterName2.'</b>';
	}else if(empty($PreviousProduct->CostCenterUID) && !empty($values['CostCenterUID'])){
		$CostCenterName2 = $this->db->query('SELECT CostCenterName FROM mCostCenter WHERE CostCenterUID='.(int)$values['CostCenterUID'].'')->row()->CostCenterName;
		$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> CostCenter is <b>Changed</b> from <b>NA</b> to <b>'.$CostCenterName2.'</b>';
	}
	if($str != ''){
				// Costcenter Audit
		$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
			,'Content' => htmlentities($str));
		$this->common_model->InsertAuditTrail($InsetData);
		$str = '';
	}
			// Case Sensitive
	if($values['CaseSensitivity'] == 'LowerCase'){
		$values['CaseSensitivity'] = 'TitleCase';
	}

	if($PreviousProduct->CaseSensitivity == 'LowerCase'){
		$PreviousProduct->CaseSensitivity = 'TitleCase';
	}
	if((!empty($PreviousProduct->CaseSensitivity) && !empty($values['CaseSensitivity']))
		&& ($PreviousProduct->CaseSensitivity != $values['CaseSensitivity'])){
		$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> Case Sensitive is <b>Changed</b> from <b>'.$PreviousProduct->CaseSensitivity.'</b> to <b>'.$values['CaseSensitivity'].'</b>';
}else if(empty($PreviousProduct->CaseSensitivity) && !empty($values['CaseSensitivity'])){
	$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> Case Sensitive is <b>Changed</b> from <b>NA</b> to <b>'.$values['CaseSensitivity'].'</b>';
}
if($str != ''){
				// Case Sensitive Audit
	$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
		,'Content' => htmlentities($str));
	$this->common_model->InsertAuditTrail($InsetData);
	$str = '';
}
			// AutoBilling
if(!empty($PreviousProduct->AutoBilling) && empty($values['AutoBilling'])){
	$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> AutoBilling is <b>Changed</b> from <b>Yes</b> to <b>No</b>';
}else if(empty($PreviousProduct->AutoBilling) && !empty($values['AutoBilling'])){
	$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> AutoBilling is <b>Changed</b> from <b>No</b> to <b>Yes</b>';
}
if($str != ''){
				// AutoBilling Audit
	$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
		,'Content' => htmlentities($str));
	$this->common_model->InsertAuditTrail($InsetData);
	$str = '';
}
}
			// VendorIndividualContact
if(!empty($PreviousProduct->VendorIndividualContact) && empty($values['VendorIndividualContact'])){
	$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> VendorIndividualContact is <b>Changed</b> from <b>Yes</b> to <b>No</b>';
}else if(empty($PreviousProduct->VendorIndividualContact) && !empty($values['VendorIndividualContact'])){
	$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> VendorIndividualContact is <b>Changed</b> from <b>No</b> to <b>Yes</b>';
}
			// SettlemetStatements
if($PreviousProduct->SettlemetStatements != $values['SettlemetStatements']){
	$str = 'for Product <b>'.$ProductName.'</b> and SubProduct <b>'.$SubProductName.'</b> SettlemetStatements is <b>Changed</b> from <b>'.$PreviousProduct->SettlemetStatement.'</b> to <b>'.$values['SettlemetStatements'].'</b>';
}
if($str != ''){
				// VendorIndividualContact Audit
	$InsetData = array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $CustomerUID,'DateTime' => date('Y-m-d H:i:s')
		,'Content' => htmlentities($str));
	$this->common_model->InsertAuditTrail($InsetData);
	$str = '';
}

}

/**************************** Customer Api Mapping Audit End*/

$this->db->where(array("CustomerUID"=>$CustomerUID));
$res = $this->db->delete('mcustomerproducts');

		//customer products
foreach ($Prod_SubProducts as $key => $value) 
{	
	if(empty($value['SubProductUID'][0])){
		return true;
	}
	else
	{
		$entry_array = array();
		$count = count($value['SubProductUID']);
		$SubProductUID = $value['SubProductUID']; 
		$ProductUID = $value['ProductUID']; 
		$customer=$this->db->query('SELECT * FROM mcustomers WHERE CustomerUID='.$CustomerUID.'')->row();
		$product = $this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$ProductUID.'')->row();
		$ArSubProducts = "";
		for($i=0; $i<$count; $i++)  
		{
			$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$SubProductUID[$i].'')->row();
			if($i != 0){
				$ArSubProducts .= ', '.$subproduct->SubProductName;
			}else{
				$ArSubProducts .= $subproduct->SubProductName;
			}
			if(!in_array($SubProductUID[$i],$CustomerSubProducts)) {
						// ADDED SUB PRODUCT
				$InsetData = array(
					'UserUID' => $this->loggedid,
					'ModuleName' => 'Customers',
					'Feature' => $CustomerUID,
					'Content' => htmlentities('Sub Product: <b>'.$subproduct->SubProductName.'</b> is Added'),
					'DateTime' => date('Y-m-d H:i:s'));
				$this->common_model->InsertAuditTrail($InsetData);
			} 


			/*RMS CHECK IF PRODUCT SUBPRODUCT ALREADY EXISTS*/
			$CustomerProductrow = multi_array_search_keyvalues($CProducts, ['ProductUID'=>$ProductUID,'SubProductUID'=>$SubProductUID[$i]]);
			if(!empty($CustomerProductrow)) {
						//row exists insert with old data
				$entry_array[] = array(
					"CustomerUID"=>$CustomerUID,
					'SubProductUID' => $SubProductUID[$i],
					'ProductUID' => $ProductUID,
					'BulkImportFormat' => ($subproduct->RMS) ? 'D2T-ReverseMortgage':'D2T-Standard',
					'BulkImportTemplateName' => ($subproduct->RMS) ? 'D2T-ReverseMortgage-BulkFormat.xlsx':'D2T-Std-BulkFormat.xlsx',
					'AutoBilling' => ($AutoBilling_Array[$SubProductUID[$i]]) ? ($AutoBilling_Array[$SubProductUID[$i]]) : 0,
					'VendorIndividualContact' => ($vendorindividualcontact_array[$SubProductUID[$i]]) ? ($vendorindividualcontact_array[$SubProductUID[$i]]) : 0,
					'CostCenterUID' => (int)$value['CostCenterUID'],
					'SettlementStatements' => implode(',',$value['SettlementStatements']),
					'SubCategoryGrading' => $CustomerProductrow[0]['SubCategoryGrading'],
					'GradingType' => $CustomerProductrow[0]['GradingType']
				);

			} else {
						//row insert with new data
				$entry_array[] = array(
					"CustomerUID"=>$CustomerUID,
					'SubProductUID' => $SubProductUID[$i],
					'ProductUID' => $ProductUID,
					'BulkImportFormat' => ($subproduct->RMS) ? 'D2T-ReverseMortgage':'D2T-Standard',
					'BulkImportTemplateName' => ($subproduct->RMS) ? 'D2T-ReverseMortgage-BulkFormat.xlsx':'D2T-Std-BulkFormat.xlsx',
					'AutoBilling' => ($AutoBilling_Array[$SubProductUID[$i]]) ? ($AutoBilling_Array[$SubProductUID[$i]]) : 0,
					'VendorIndividualContact' => ($vendorindividualcontact_array[$SubProductUID[$i]]) ? ($vendorindividualcontact_array[$SubProductUID[$i]]) : 0,
					'CostCenterUID' => (int)$value['CostCenterUID'],
					'SettlementStatements' => implode(',',$value['SettlementStatements']),
					'SubCategoryGrading' => $CustomerProductrow[0]['SubCategoryGrading'],
					'GradingType' => $CustomerProductrow[0]['GradingType']
				);
			}
		}

		if(!in_array($ProductUID,$CustomerProducts)){
					// NEW ONE
			$InsetData = array(
				'UserUID' => $this->loggedid,
				'ModuleName' => 'Customers',
				'Feature' => $CustomerUID,
				'Content' => htmlentities('Product Added: '.$product->ProductName.' with Sub Products: '.$ArSubProducts.''),
				'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($InsetData);
		}

		$this->db->insert_batch('mcustomerproducts', $entry_array);
	}
}
		// Customer Templates
		//OLD TEMPLATES ARRAy
$Oldtemplates = [];
$this->db->select('*');
$this->db->from('mcustomertemplates');
$this->db->where(array("CustomerUID"=>$CustomerUID));
$Oldtemplates = $this->db->get()->result_array();

$this->db->where(array("CustomerUID"=>$CustomerUID));
$res = $this->db->delete('mcustomertemplates');

		// Customer Workflow
$Customer_Workflows_Array = [];
$this->db->select('*');
$this->db->from('mcustomerworkflowmodules');
$this->db->where(array("CustomerUID"=>$CustomerUID));
$Customer_Workflows_ = $this->db->get()->result_array();
$Customer_Workflows_Array = array_column($Customer_Workflows_,'WorkflowModuleUID');

$this->db->where(array("CustomerUID"=>$CustomerUID));
$res = $this->db->delete('mcustomerworkflowmodules');

$CustomerOptionalworkflowmodules = [];
$this->db->select('*');
$this->db->from('mcustomeroptionalworkflowmodules');
$this->db->where(array("CustomerUID"=>$CustomerUID));
$CustomerOptionalworkflowmodules = $this->db->get()->result_array();
$CustomerOptionalworkflow_Array = array_column($CustomerOptionalworkflowmodules,'WorkflowModuleUID');

$this->db->where(array("CustomerUID"=>$CustomerUID));
$res = $this->db->delete('mcustomeroptionalworkflowmodules');


$mCustomerBillingTrigger = [];
$this->db->select('*');
$this->db->from('mCustomerBillingTrigger');
$this->db->where(array("CustomerUID"=>$CustomerUID));
$mCustomerBillingTrigger = $this->db->get()->result();

$this->db->where(array("CustomerUID"=>$CustomerUID));
$this->db->delete('mCustomerBillingTrigger');

		// Customer Product TAT
$CustomerProducttat = [];
$CustomerProducttat = $this->db->query("SELECT mproducts.ProductUID, mproducts.ProductName, msubproducts.SubProductName, msubproducts.SubProductUID, mcustomerproducttat.SkipOrderOpenDate, mcustomerproducttat.PriorityTime, mcustomerproducttat.PriorityUID FROM (mcustomerproducttat) LEFT JOIN mproducts ON mproducts.ProductUID = mcustomerproducttat.ProductUID LEFT JOIN msubproducts ON msubproducts.SubProductUID = mcustomerproducttat.SubProductUID WHERE CustomerUID = ".$CustomerUID." ORDER BY FIELD(mcustomerproducttat.SkipOrderOpenDate, 1,3,2)")->result();

$this->db->where(array("CustomerUID"=>$CustomerUID));
$res = $this->db->delete('mcustomerproducttat');



foreach ($Template_Array as $key => $value) 
{	
	if(empty($value['SubProductUID'][0]))
	{
		return true;
	}
	else
	{
		$entry_array = array();
		$count = count($value['SubProductUID']);
		$SubProductUID = $value['SubProductUID']; 
		$ProductUID = $value['ProductUID']; 
		$TemplateUID = $value['TemplateUID']; 
		for($i=0; $i<$count; $i++)  
		{
			$entry_array[] = array(
				"CustomerUID"=>$CustomerUID,
				'ProductUID' => $ProductUID,
				'SubProductUID' => $SubProductUID,
				'TemplateUID' => $TemplateUID
			);
			$customer=$this->db->query('SELECT * FROM mcustomers WHERE CustomerUID='.$CustomerUID.'')->row();
			$product=$this->db->query('SELECT * FROM mproducts WHERE ProductUID='.$ProductUID.'')->row();
			$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$SubProductUID.'')->row();
			if(!empty($TemplateUID)){
				$template=$this->db->query('SELECT * FROM mtemplates WHERE TemplateUID='.$TemplateUID.'')->row();
			}
			$NewOne = false;
			$TMsg = '';
			foreach($Oldtemplates as $OldTemplate){
				if($OldTemplate['CustomerUID'] == $CustomerUID 
					&& $OldTemplate['SubProductUID'] == $SubProductUID
					&& $OldTemplate['TemplateUID'] != $TemplateUID){
					if(!empty($OldTemplate['TemplateUID'])){	
						$templateOld=$this->db->query('SELECT * FROM mtemplates WHERE TemplateUID='.$OldTemplate['TemplateUID'].'')->row();
					}	
					$NewOne = true;
					if(!empty($templateOld) && !empty($template)){	
						$TMsg = 'Template for Sub Product <b>'.$subproduct->SubProductName.'</b> is Changed from <b>'.$templateOld->TemplateName.'</b> to <b>'.$template->TemplateName.'</b>';
					}else if(empty($templateOld) && !empty($template)){
						$TMsg = 'Template for Sub Product <b>'.$subproduct->SubProductName.'</b> is Changed from <b>NA</b> to <b>'.$template->TemplateName.'</b>';
					}else if(!empty($templateOld) && empty($template)){
						$TMsg = 'Template for Sub Product <b>'.$subproduct->SubProductName.'</b> is Changed from <b>'.$templateOld->TemplateName.'</b> to <b>NA</b>';
					}
				}
			}
			if($NewOne == true && $TMsg != ""){
						// CHANGED
				$InsetData = array(
					'UserUID' => $this->loggedid,
					'ModuleName' => 'Customers',
					'Feature' => $CustomerUID,
					'Content' => htmlentities($TMsg),
					'DateTime' => date('Y-m-d H:i:s'));
				$this->common_model->InsertAuditTrail($InsetData);
			}

		}
		$this->db->insert_batch('mcustomertemplates', $entry_array);
	}
}


foreach ($Workflows_Array as $keys => $values) 
{	
	if($values['SubProductUID'] && $values['ProductUID'])
	{
			// Customer mapped with Api Audit Log END
		$this->db->where(array('CustomerUID'=>$CustomerUID,'SubProductUID'=>$values['SubProductUID'],'ProductUID'=>$values['ProductUID']));
		$this->db->update('mcustomerproducts', array("CostCenterUID"=>$values['CostCenterUID'],"OrderSourceUID"=>$values['OrderSourceUID'],"ServiceTypeUID"=>$values['ServiceTypeUID'],"CaseSensitivity"=>$values['CaseSensitivity'], "EmailAlert"=>$values['EmailAlert'], "OrderPolicyType"=>$values['OrderPolicyType'],"SettlementStatements"=>implode(',',$values['SettlementStatements'])));
	}
			// Customer Worflows Audit Start
	$PrW = [];
	foreach ($Customer_Workflows_ as $key => $Customer_Workflow) {
		if($Customer_Workflow->ProductUID == $values['ProductUID']
			&& $Customer_Workflow->SubProductUID == $values['SubProductUID']
			&& $Customer_Workflow->CustomerUID == $CustomerUID){
			array_push($PrW,$Customer_Workflow->WorkflowModuleUID);
	}
}
			// Customer Worflows INSERT
foreach ($values['WorkflowModuleUID'] as $key1 => $value1) 
{
	$data = array(
		"CustomerUID"=>$CustomerUID,
		'ProductUID' => $values['ProductUID'],
		'SubProductUID' => $values['SubProductUID'],
		'WorkflowModuleUID' => $value1
	);
	$this->db->insert('mcustomerworkflowmodules', $data);
}
if(!empty($PrW)){
	$workflowmodule1=$this->db->query('SELECT GROUP_CONCAT(mworkflowmodules.WorkflowModuleName) AS WorkflowModuleName FROM mworkflowmodules WHERE mworkflowmodules.WorkflowModuleUID IN ('.implode(',',$PrW).')')->row()->WorkflowModuleName;
}else{
	$workflowmodule1 = 'NA';
}
if(!empty($values['WorkflowModuleUID'])){
	$workflowmodule2=$this->db->query('SELECT GROUP_CONCAT(mworkflowmodules.WorkflowModuleName) AS WorkflowModuleName FROM mworkflowmodules WHERE mworkflowmodules.WorkflowModuleUID IN ('.implode(',',$values['WorkflowModuleUID']).')')->row()->WorkflowModuleName;
}else{
	$workflowmodule2 = 'NA';
}
if($workflowmodule1 != $workflowmodule2){
				// Changed
	$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$values['SubProductUID'].'')->row();
	$InsetData = array(
		'UserUID' => $this->loggedid,
		'ModuleName' => 'Customers',
		'Feature' => $CustomerUID,
		'Content' => htmlentities('for Sub Product: <b>'.$subproduct->SubProductName.'</b> WorkFlows: Changed from <b>'.$workflowmodule1.'</b> To <b>'.$workflowmodule2.'</b>'),
		'DateTime' => date('Y-m-d H:i:s'));
	$this->common_model->InsertAuditTrail($InsetData);
}
			// Customer Worflows Audit END

			// Customer Optional Worflows Audit Start
$PrOW = [];
foreach ($CustomerOptionalworkflowmodules as $key => $CustomerOptionalworkflowmodule) {
	if($CustomerOptionalworkflowmodule->ProductUID == $values['ProductUID']
		&& $CustomerOptionalworkflowmodule->SubProductUID == $values['SubProductUID']
		&& $CustomerOptionalworkflowmodule->CustomerUID == $CustomerUID){
		array_push($PrOW,$CustomerOptionalworkflowmodule->WorkflowModuleUID);
}
}
			// Customer Optional Worflows INSERT
foreach ($values['OptionalWorkflowModuleUID'] as $key1 => $value1) 
{
	$data = array(
		"CustomerUID"=>$CustomerUID,
		'ProductUID' => $values['ProductUID'],
		'SubProductUID' => $values['SubProductUID'],
		'WorkflowModuleUID' => $value1
	);
	$this->db->insert('mcustomeroptionalworkflowmodules', $data);
}
if(!empty($PrOW)){
	$workflowmodule1=$this->db->query('SELECT GROUP_CONCAT(mworkflowmodules.WorkflowModuleName) AS WorkflowModuleName FROM mworkflowmodules WHERE mworkflowmodules.WorkflowModuleUID IN ('.implode(',',$PrOW).')')->row()->WorkflowModuleName;
}else{
	$workflowmodule1 = 'NA';
}
if(!empty($values['OptionalWorkflowModuleUID'])){
	$workflowmodule2=$this->db->query('SELECT GROUP_CONCAT(mworkflowmodules.WorkflowModuleName) AS WorkflowModuleName FROM mworkflowmodules WHERE mworkflowmodules.WorkflowModuleUID IN ('.implode(',',$values['OptionalWorkflowModuleUID']).')')->row()->WorkflowModuleName;
}else{
	$workflowmodule2 = 'NA';
}
if($workflowmodule1 != $workflowmodule2){
				// Changed
	$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$values['SubProductUID'].'')->row();
	$InsetData = array(
		'UserUID' => $this->loggedid,
		'ModuleName' => 'Customers',
		'Feature' => $CustomerUID,
		'Content' => htmlentities('for Sub Product: <b>'.$subproduct->SubProductName.'</b> Optional WorkFlows: Changed from <b>'.$workflowmodule1.'</b> To <b>'.$workflowmodule2.'</b>'),
		'DateTime' => date('Y-m-d H:i:s'));
	$this->common_model->InsertAuditTrail($InsetData);
}
			// Customer Optional Worflows Audit End
		//	Customer Billing Audit
$PrB = [];
foreach ($mCustomerBillingTrigger as $key => $BillingTrigger) {
	if($BillingTrigger->ProductUID == $values['ProductUID']
		&& $BillingTrigger->SubProductUID == $values['SubProductUID']
		&& $BillingTrigger->CustomerUID == $CustomerUID){
		array_push($PrB,$BillingTrigger->WorkflowModuleUID);
}
}
foreach ($values['billing_trigger'] as $billing_triggerkey => $billing_triggervalue) 
{
	$data = array(
		"CustomerUID"=>$CustomerUID,
		'ProductUID' => $values['ProductUID'],
		'SubProductUID' => $values['SubProductUID'],
		'WorkflowModuleUID' => $billing_triggervalue
	);
	$this->db->insert('mCustomerBillingTrigger', $data);		
}
if(!empty($PrB)){
	$workflowmodule1=$this->db->query('SELECT GROUP_CONCAT(mworkflowmodules.WorkflowModuleName) AS WorkflowModuleName FROM mworkflowmodules WHERE mworkflowmodules.WorkflowModuleUID IN ('.implode(',',$PrB).')')->row()->WorkflowModuleName;
}else{
	$workflowmodule1 = 'NA';
}
if(!empty($values['billing_trigger'])){
	$workflowmodule2=$this->db->query('SELECT GROUP_CONCAT(mworkflowmodules.WorkflowModuleName) AS WorkflowModuleName FROM mworkflowmodules WHERE mworkflowmodules.WorkflowModuleUID IN ('.implode(',',$values['billing_trigger']).')')->row()->WorkflowModuleName;
}else{
	$workflowmodule2 = 'NA';
}
if($workflowmodule1 != $workflowmodule2){
				// Changed
	$subproduct=$this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$values['SubProductUID'].'')->row();
	$InsetData = array(
		'UserUID' => $this->loggedid,
		'ModuleName' => 'Customers',
		'Feature' => $CustomerUID,
		'Content' => htmlentities('for Sub Product: <b>'.$subproduct->SubProductName.'</b> billing_trigger: Changed from <b>'.$workflowmodule1.'</b> To <b>'.$workflowmodule2.'</b>'),
		'DateTime' => date('Y-m-d H:i:s'));
	$this->common_model->InsertAuditTrail($InsetData);
}
}	

		// Capture audit logs for TAT Priority and Timing changes
$Priority = [1=>'Rush',2=>'Normal',3=>'ASAP'];	
$TAT = [1=>'Standard SLA',2=>'SLA pre/post 3 PM',3=>'Skip Order Entry date'];
foreach ($CustomerProducttat as $keys => $val) 
{
	foreach ($Priority_Array as $prtykey => $prtyvalue)
	{	
		if($val->SkipOrderOpenDate <> $prtyvalue['SkipOrderOpenDate'][$val->PriorityUID] && $val->SubProductUID == $prtyvalue['SubProductUID'])
		{
			$InsetData = array(
				'UserUID' => $this->loggedid,
				'ModuleName' => 'Customers',
				'Feature' => $CustomerUID,
				'Content' => htmlentities('TAT Changed from <b>'.$TAT[$val->SkipOrderOpenDate].'</b> to <b>'.$TAT[$prtyvalue['SkipOrderOpenDate'][$val->PriorityUID]].'</b> for Priority: <b>'.$Priority[$val->PriorityUID].'</b>, In Sub Product <b>'.$val->SubProductName.'</b>'),
				'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($InsetData);
		}

		if($val->PriorityTime <> $prtyvalue['priorityhour'][$val->PriorityUID] && $val->SubProductUID == $prtyvalue['SubProductUID'])
		{
			$InsetData = array(
				'UserUID' => $this->loggedid,
				'ModuleName' => 'Customers',
				'Feature' => $CustomerUID,
				'Content' => htmlentities('TAT Time Changed from <b>'.$val->PriorityTime.'</b> to <b>'.$prtyvalue['priorityhour'][$val->PriorityUID].'</b> for Priority: <b>'.$Priority[$val->PriorityUID].'</b>, In Sub Product <b>'.$val->SubProductName.'</b>'),
				'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($InsetData);
		}
	} 
}

foreach ($Priority_Array as $priorityarraykey => $priorityarrayvalue) 
{
	/*@author Naveenkumar @purpose set defaulted Priority values in subproduct*/ 
	$customerproducts_data = array(
		"PriorityUID"=>(int)$priorityarrayvalue['Sub_PriorityUID']
	);
	$this->db->where(array('CustomerUID'=>$CustomerUID,'SubProductUID'=>$priorityarrayvalue['SubProductUID'],'ProductUID'=>$priorityarrayvalue['ProductUID']));

	$this->db->update('mcustomerproducts', $customerproducts_data);

	foreach ($priorityarrayvalue['priorityhour'] as $key => $value) 
	{
		if($value != '')
		{
			$data = array(
				"CustomerUID"=>$CustomerUID,
				'ProductUID' => $priorityarrayvalue['ProductUID'],
				'SubProductUID' => $priorityarrayvalue['SubProductUID'],
				'PriorityUID' => $key,
				'PriorityTime' => $value,
				'SkipOrderOpenDate' => $priorityarrayvalue['SkipOrderOpenDate'][$key],
			);
					// echo '<pre>';print_r($data);
			$this->db->insert('mcustomerproducttat', $data);



		}
	}
}
}

function getDefaultSubValue($CustomerUID)
{
	$query = $this->db->query("SELECT DefaultProductSubValue FROM mcustomerdefaultproduct WHERE CustomerUID ='$CustomerUID' ");
	$result = $query->row();
	return $result->DefaultProductSubValue;
}

function getSelectedSubProduct($SelectedSubProduct)
{
	if($SelectedSubProduct !=''){

		$query = $this->db->query("SELECT * FROM msubproducts WHERE SubProductUID IN ($SelectedSubProduct)");
		return $query->result();

	}else{
		return '';
	}
}

function GetSourceApi(){
	$this->db->select("*");
	$this->db->from('mApiTitlePlatform');
		//$this->db->join('musers','mabstractor.AbstractorUID=musers.AbstractorUID','inner');
	$this->db->where('mApiTitlePlatform.Active',1);  
	$query = $this->db->get();
	return $query->result();   	
}

function GetCustomerApiInfo($CustomerUID){
	$this->db->select("*");
	$this->db->from('mCustomerApiInfo');
		//$this->db->join('musers','mabstractor.AbstractorUID=musers.AbstractorUID','inner');
	$this->db->where('CustomerUID',$CustomerUID);  
	$this->db->group_by('ProductUID');
	$query = $this->db->get();
	return $query->result();     	
}

function GetCustomerApiInfoByPro($CustomerUID,$ProductUID){
	$this->db->select("*");
	$this->db->from('mCustomerApiInfo');
		//$this->db->join('musers','mabstractor.AbstractorUID=musers.AbstractorUID','inner');
	$this->db->where('CustomerUID',$CustomerUID); 
	$this->db->where('ProductUID',$ProductUID); 
	$this->db->group_by('ProductUID');
	$query = $this->db->get();
	return $query->row();     	
}

  //D-2-T18 Fetch gradings - impediments - subcatgoryimpediments
public function get_gradings()
{
	return $this->db->get('mGrading')->result();
}

public function get_impediments()
{
	return $this->db->get('mImpediment')->result();
}

public function get_subcategoryimpediments()
{
	return $this->db->get('mImpedimentSubCategory')->result();
} 

public function GetCustomerImpedimentByUID($ImpedimentCustomerUID)
{
	$this->db->select('*');
	$this->db->from ('mImpedimentCustomer');
	$this->db->where('mImpedimentCustomer.ImpedimentCustomerUID', $ImpedimentCustomerUID);
	$query = $this->db->get();
	return $query->row();
}

public function get_Customers_customerImpediments_byCustomerUID($CustomerUID,$SubProductUID)
{
	$this->db->select('ImpedimentName,ImpedimentSubCategoryName,GradingName,mcustomers.GradingType,SubCategoryGrading,mImpedimentCustomer.*');
	$this->db->from ('mImpedimentCustomer');
	$this->db->join('mcustomers','mcustomers.CustomerUID = mImpedimentCustomer.CustomerUID');
	$this->db->join ( 'mGrading', 'mGrading.GradingUID = mImpedimentCustomer.GradingUID' , 'left' );
	$this->db->join ( 'mImpediment', 'mImpediment.ImpedimentUID = mImpedimentCustomer.ImpedimentUID' , 'left' );
	$this->db->join ( 'mImpedimentSubCategory', 'mImpedimentSubCategory.ImpedimentSubCategoryUID = mImpedimentCustomer.ImpedimentSubCategoryUID' , 'left' );
	$this->db->where('mImpedimentCustomer.CustomerUID', $CustomerUID);
	$this->db->where('mImpedimentCustomer.SubProductUID', $SubProductUID);
	$query = $this->db->get();
	return $query->result();
}

public function get_Customers_customerImpediment_ByUID($ImpedimentCustomerUID)
{
	$this->db->select('ImpedimentName,ImpedimentSubCategoryName,GradingName,mcustomers.GradingType,SubCategoryGrading,mImpedimentCustomer.*');
	$this->db->from ('mImpedimentCustomer');
	$this->db->join('mcustomers','mcustomers.CustomerUID = mImpedimentCustomer.CustomerUID');
	$this->db->join ( 'mGrading', 'mGrading.GradingUID = mImpedimentCustomer.GradingUID' , 'left' );
	$this->db->join ( 'mImpediment', 'mImpediment.ImpedimentUID = mImpedimentCustomer.ImpedimentUID' , 'left' );
	$this->db->join ( 'mImpedimentSubCategory', 'mImpedimentSubCategory.ImpedimentSubCategoryUID = mImpedimentCustomer.ImpedimentSubCategoryUID' , 'left' );
	$this->db->where('mImpedimentCustomer.ImpedimentCustomerUID', $ImpedimentCustomerUID);
	$query = $this->db->get();
	return $query->row();
}

function update_gradetype($updatearray,$CustomersDetails,$SubProductdetails){
	if(!empty($CustomersDetails) && !empty($SubProductdetails)) {
		if($updatearray['GradingType'] != $SubProductdetails['GradingType']){
			$this->db->where('CustomerUID',$CustomersDetails->CustomerUID);        
			$this->db->where('SubProductUID',$SubProductdetails['SubProductUID']);        
			$this->db->delete('mImpedimentCustomer');
		}

		$this->db->where('CustomerUID',$CustomersDetails->CustomerUID);        
		$this->db->where('SubProductUID',$SubProductdetails['SubProductUID']);        
		$this->db->update('mcustomerproducts', $updatearray);

		$InsertData = array(
			'UserUID' => $this->loggedid,
			'ModuleName' => 'customerproductgrading setup',
			'Feature' => $CustomersDetails->CustomerUID,
			'Content' => htmlentities($CustomersDetails->CustomerName.' for the subproduct <b>'.$SubProductdetails['SubProductName'].'</b> '.'GradeType Changed to '.$updatearray['GradingType']),
			'DateTime' => date('Y-m-d H:i:s'));
		$this->common_model->InsertAuditTrail($InsertData);
	}
}


public function SavegradingData($data)
{
	$this->db->insert('mImpedimentCustomer', $data);
	return $this->db->insert_id();
}

public function check_grading($GradingUID)
{
	$this->db->where('GradingUID', $GradingUID);
	$count = $this->db->get('mImpedimentCustomer')->num_rows();
	if ($count) {
		return true;
	}
	return false;
}

public function UpdategradingData($data, $ImpedimentCustomerUID)
{
	$this->db->where('ImpedimentCustomerUID', $ImpedimentCustomerUID);
	$this->db->update('mImpedimentCustomer', $data);
	return true;
}

function check_grading_exists($ImpedimentCustomerUID) {
	return $this->db->query("SELECT EXISTS(SELECT 1 FROM mImpedimentCustomer WHERE ImpedimentCustomerUID = '".$ImpedimentCustomerUID."') AS isavailable")->row()->isavailable;
}

function check_grading_types_exists($SubCategoryGrading,$ImpedimentUID,$ImpedimentSubCategoryUID,$CustomerUID,$SubProductUID) {	
	$sql = '';
	if($SubCategoryGrading == 1) {
		$sql = ($ImpedimentSubCategoryUID != '') ? 'AND ImpedimentSubCategoryUID = '.$ImpedimentSubCategoryUID : 'AND (ImpedimentSubCategoryUID IS NULL OR ImpedimentSubCategoryUID = "0" )';
	}
	return $this->db->query("SELECT EXISTS(SELECT 1 FROM mImpedimentCustomer WHERE ImpedimentUID = '".$ImpedimentUID."' AND CustomerUID = '".$CustomerUID."' AND SubProductUID = '".$SubProductUID."' ".$sql.") AS isavailable")->row()->isavailable;
}

function check_updategrading_types_exists($SubCategoryGrading,$ImpedimentUID,$ImpedimentSubCategoryUID,$ImpedimentCustomerUID,$CustomerUID,$SubProductUID) {
	$sql = '';
	if($SubCategoryGrading == 1) {
		$sql = ($ImpedimentSubCategoryUID != '') ? 'AND ImpedimentSubCategoryUID = '.$ImpedimentSubCategoryUID : 'AND (ImpedimentSubCategoryUID IS NULL OR ImpedimentSubCategoryUID = "0" )';
	}
	return $this->db->query("SELECT EXISTS(SELECT 1 FROM mImpedimentCustomer WHERE ImpedimentUID = '".$ImpedimentUID."' AND ImpedimentCustomerUID != '".$ImpedimentCustomerUID."' AND CustomerUID = '".$CustomerUID."' AND SubProductUID = '".$SubProductUID."' ".$sql.") AS isavailable")->row()->isavailable;
}

function check_grading_types($SubCategoryGrading,$ImpedimentUID,$ImpedimentSubCategoryUID,$CustomerUID,$SubProductUID) {
	$sql = '';
	if($SubCategoryGrading == 1) {
		$sql = ($ImpedimentSubCategoryUID != '') ? 'AND ImpedimentSubCategoryUID = '.$ImpedimentSubCategoryUID : 'AND (ImpedimentSubCategoryUID IS NULL OR ImpedimentSubCategoryUID = "0" )';
	}
	return $this->db->query("SELECT ImpedimentCustomerUID FROM mImpedimentCustomer WHERE  ImpedimentUID = '".$ImpedimentUID."' AND CustomerUID = '".$CustomerUID."' AND SubProductUID = '".$SubProductUID."' ".$sql." ")->row();
}

function delete_grading_byImpedimentCustomerUID($ImpedimentCustomerUID)
{
	$this->db->where('ImpedimentCustomerUID',$ImpedimentCustomerUID);
	$this->db->delete('mImpedimentCustomer');
	return true;
}

function customerhistorydetails($post){
	$CustomerUID = $post['CustomerUID'];
	$this->db->select('DateTime,Content,UserName');
	$this->db->select('"Client" AS ModuleName',false);
	$this->db->from('taudittrail');
	$this->db->join('musers','musers.UserUID = taudittrail.UserUID','left');
	$this->db->where_in('ModuleName',array('Customers','CustomerTaskConf'));
	$this->db->where('taudittrail.Feature',$CustomerUID);
	$this->db->order_by('DateTime','desc');

	if ($post['length'] != '' && $post['length'] != -1) {
		$this->db->limit($post['length'], $post['start']);
	}
	return $this->db->get()->result();
}

function customerhistorydetailsCount($post){
	$CustomerUID = $post['CustomerUID'];
	$this->db->select('DateTime,Content,UserName');
	$this->db->select('"Client" AS ModuleName',false);
	$this->db->from('taudittrail');
	$this->db->join('musers','musers.UserUID = taudittrail.UserUID','left');
	$this->db->where('ModuleName','Customers');
	$this->db->where('taudittrail.Feature',$CustomerUID);
	$this->db->order_by('DateTime','desc');
	return $this->db->get()->num_rows();
}

	/**
	*@description function to fetch billing trigger for product and subproduct
	*
	* @param int CustomerUID, ProductUID and SubProductUID
	* 
	* @throws no exception
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return nothing 
	* @since  3 december 2020 
	* @version DI1-T56 Customer Billing Triggers
	*
	*/ 

	function get_customer_billingtrigger_details($CustomerUID,$ProductUID,$SubProductUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mCustomerBillingTrigger' );
		$this->db->join ( 'mworkflowmodules', 'mworkflowmodules.WorkflowModuleUID = mCustomerBillingTrigger.WorkflowModuleUID' , 'left' );
		$this->db->join ( 'msubproducts', 'msubproducts.SubProductUID = mCustomerBillingTrigger.SubProductUID' , 'left' );
		$this->db->join ( 'mproducts', 'mproducts.ProductUID = mCustomerBillingTrigger.ProductUID' , 'left' );
		$this->db->where('mCustomerBillingTrigger.SubProductUID',$SubProductUID);
		$this->db->where('mCustomerBillingTrigger.ProductUID',$ProductUID);
		$this->db->where('mCustomerBillingTrigger.CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return $query->result_array();

	}

	/**
	* Function -  fetch RMS Subproducts based on customer
	*
	* @param params & types used -- CustomerUID
	* 
	* @throws exception snippet is not valid or present
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return json response valid or invalid 
	* @since Friday 17 January 2020
	* @version task id and name
	*
	*/ 
	function get_rmssubproducts($CustomerUID) {
		$this->db->select('mcustomerproducts.SubProductUID,SubProductName,msubproducts.RMS,msubproducts.ProductUID,mcustomerproducts.SubCategoryGrading,mcustomerproducts.GradingType');
		$this->db->from('mcustomerproducts');
		$this->db->join('msubproducts','msubproducts.SubProductUID=mcustomerproducts.SubProductUID');
		$this->db->where("mcustomerproducts.CustomerUID",$CustomerUID);
		$this->db->where("msubproducts.RMS",1);
		return $this->db->get()->result_array();
	}

	/**
	* Function -  fetch RMS Subproducts based on customer and subproduct
	*
	* @param params & types used -- CustomerUID and Subproductuid
	* 
	* @throws exception snippet is not valid or present
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return json response valid or invalid 
	* @since Friday 17 January 2020
	* @version task id and name
	*
	*/ 
	function get_rmssubproductsbyuid($CustomerUID,$SubProductUID) {
		$this->db->select('mcustomerproducts.SubProductUID,SubProductName,msubproducts.RMS,msubproducts.ProductUID,mcustomerproducts.SubCategoryGrading,mcustomerproducts.GradingType');
		$this->db->from('mcustomerproducts');
		$this->db->join('msubproducts','msubproducts.SubProductUID=mcustomerproducts.SubProductUID');
		$this->db->where("mcustomerproducts.CustomerUID",$CustomerUID);
		$this->db->where("mcustomerproducts.SubProductUID",$SubProductUID);
		$this->db->where("msubproducts.RMS",1);
		return $this->db->get()->row_array();
	}


	function SaveCustomerPricing($CustomerUID,$PricingUID){
		$msg = "";

		$this->db->select('PricingUID');
		$this->db->from('mcustomers');
		$this->db->where('CustomerUID',$CustomerUID);
		$Customer = $this->db->get()->row();
		if(!empty($Customer->PricingUID)&&!empty($PricingUID)){
			$Pricing1 = $this->db->query("SELECT PricingName FROM mpricing WHERE PricingUID = ".$Customer->PricingUID."")->row()->PricingName;
			$Pricing2 = $this->db->query("SELECT PricingName FROM mpricing WHERE PricingUID = ".$PricingUID."")->row()->PricingName;
			$msg = "Customer Pricing is changed from <b>".$Pricing1."</b> to <b>".$Pricing2."</b>";
		}else{
			if(!empty($PricingUID)){
				$Pricing2 = $this->db->query("SELECT PricingName FROM mpricing WHERE PricingUID = ".$PricingUID."")->row()->PricingName;
				$msg = "Customer Pricing <b>".$Pricing2."</b> saved";
			}
		}
		$this->db->where('CustomerUID',$CustomerUID);
		$this->db->update('mcustomers',array('PricingUID'=>$PricingUID));
		
		if($msg != ""){
		// Audit
			$InsetData = array(
				'UserUID' => $this->loggedid,
				'ModuleName' => 'Customers',
				'Feature' => $CustomerUID,
				'Content' => htmlentities($msg),
				'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($InsetData);
			return true;
		}
		return false;
	}


	function _getCustomerPassthru_query($post, $countall=false, $count_filtered=false)
	{
		$this->db->select("mCustomerPassThru.*");
		$this->db->from('mCustomerPassThru');
		if (!empty($post['search']['value']) || !empty($post['order'])) {
			$this->db->join('mcustomers','mcustomers.CustomerUID = mCustomerPassThru.CustomerUID','left');
			$this->db->join('mcities','mcities.ZipCode = mCustomerPassThru.ZipCode AND mCustomerPassThru.ZipCode!=0','left');
			$this->db->join('mcounties','mcounties.CountyUID = mCustomerPassThru.CountyUID AND mCustomerPassThru.CountyUID!=0','left');
			$this->db->join('moverridereasons','moverridereasons.OverrideReasonUID = mCustomerPassThru.OverrideReasonUID','left');
			$this->db->join('mstates','mstates.StateUID = mCustomerPassThru.StateUID OR mstates.StateUID=mcities.StateUID','left');
		}
		$this->db->where('mCustomerPassThru.CustomerUID', $post['CustomerUID']);

		if(! empty($post['StateUID'])) {
			$this->db->where('`mCustomerPassThru.`StateUID`',$post['StateUID']);
		}

		if(! empty($post['CountyUID'])) {
			$this->db->where('`mCustomerPassThru`.`CountyUID`',$post['CountyUID']);
		}

		if(! empty($post['CityUID'])) {
			$this->db->where('`mCustomerPassThru`.`CityUID`',$post['CityUID']);
		}

		if(! empty($post['ZipCode'])) {
			$this->db->where('`mCustomerPassThru`.`ZipCode`',$post['ZipCode']);
		}

		if(! empty($post['OverrideReasonUID'])) {
			$this->db->where('`mCustomerPassThru`.`OverrideReasonUID`',$post['OverrideReasonUID']);
		}

		if($countall == false) {
			if (isset($post['search']['value']) && !empty($post['search']['value'])) {
				$like = "";
				foreach ($post['column_search'] as $key => $item) { // loop column 
					// if datatable send POST for search
					if ($key === 0) { // first loop
						$like .= "( ".$item." LIKE '%".$post['search']['value']."%' "; 
					} else {
						$like .= " OR ".$item." LIKE '%".$post['search']['value']."%' ";    
					}
				}
				$like .= ") ";
				$this->db->where($like, null, false);
			}

			if (!empty($post['order'])) 
			{ 
				// here order processing 
				if($post['column_order'][$post['order'][0]['column']]!='')
				{
					$this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);    
				}    
			} 
			else{
				$this->db->order_by('mCustomerPassThru.PassThruUID', 'ASC');
			}
		}

		if ($countall == true || $count_filtered == true) {
			return $this->db->count_all_results();
		}

		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		return $this->db->get()->result();
	}

	function getPassThruExists($CustomerUID,$StateUID,$CountyUID,$ZipCode,$PassThruUID = false,$CityUID, $OverrideReasonUID)
	{
		$where = [];
		$querywhere = false;

		if(!empty($PassThruUID)) {
			$where[] = ' mCustomerPassThru.PassThruUID != '.$PassThruUID;
		}

		if(!empty($CustomerUID)){
			$where[] = '(mCustomerPassThru.CustomerUID = '.$CustomerUID.')';
		}
		else{
			$where[] = '(mCustomerPassThru.CustomerUID IS NULL OR mCustomerPassThru.CustomerUID = 0)';

		}

		if(!empty($StateUID)) {
			$where[] = '(mCustomerPassThru.StateUID = '.$StateUID.')';
		}  else {
			$where[] = '(mCustomerPassThru.StateUID IS NULL OR mCustomerPassThru.StateUID = 0)';
		}

		if (!empty($CountyUID)) {
			$where[] ='(mCustomerPassThru.CountyUID = '.$CountyUID.')';
		}
		else{
			$where[] = '(mCustomerPassThru.CountyUID IS NULL OR mCustomerPassThru.CountyUID = 0)';
		}

		if (!empty($ZipCode)) {
			$where[] = '(mCustomerPassThru.ZipCode = '.$ZipCode.')';
		}
		else {
			$where[] = '(mCustomerPassThru.ZipCode IS NULL OR mCustomerPassThru.ZipCode = 0)';
		}

		if (!empty($CityUID)) {
			$where[] ='(mCustomerPassThru.CityUID = '.$CityUID.')';
		}
		else{
			$where[] = '(mCustomerPassThru.CityUID IS NULL OR mCustomerPassThru.CityUID = 0)';
		}

		if (!empty($OverrideReasonUID)) {
			$where[] ='(mCustomerPassThru.OverrideReasonUID = '.$OverrideReasonUID.')';
		}
		else{
			$where[] = '(mCustomerPassThru.OverrideReasonUID IS NULL OR mCustomerPassThru.OverrideReasonUID = 0)';
		}

		if(!empty($where)) {
			$querywhere = ' AND '. implode(' AND ', $where);
		}

		return $this->db->query("SELECT mCustomerPassThru.*,mstates.StateName,mstates.StateCode,mcounties.CountyName,mcustomers.CustomerName,mcities.CityUID,mcities.CityName,moverridereasons.OverrideReasonDescription
			FROM mCustomerPassThru 
			LEFT JOIN mstates ON mstates.StateUID = mCustomerPassThru.StateUID
			LEFT JOIN mcounties ON mcounties.CountyUID = mCustomerPassThru.CountyUID
			LEFT JOIN mcustomers ON mcustomers.CustomerUID = mCustomerPassThru.CustomerUID
			LEFT JOIN mcities ON mcities.CityUID = mCustomerPassThru.CityUID
			LEFT JOIN moverridereasons ON moverridereasons.OverrideReasonUID = mCustomerPassThru.OverrideReasonUID
			WHERE mCustomerPassThru.CustomerUID = ".$CustomerUID." $querywhere")->result();

	}

	function deleteExistPassThru($CustomerUID,$StateUID,$CountyUID,$ZipCode,$PassThruUID = false,$CityUID, $OverrideReasonUID)
	{
		$where = [];
		$querywhere = false;

		if(!empty($PassThruUID)) {
			$where[] = ' mCustomerPassThru.PassThruUID != '.$PassThruUID;
		}

		if(!empty($CustomerUID)){
			$where[] = '(mCustomerPassThru.CustomerUID = '.$CustomerUID.')';
		}
		else{
			$where[] = '(mCustomerPassThru.CustomerUID IS NULL OR mCustomerPassThru.CustomerUID = 0)';

		}

		if(!empty($StateUID)) {
			$where[] = '(mCustomerPassThru.StateUID = '.$StateUID.')';
		}  else {
			$where[] = '(mCustomerPassThru.StateUID IS NULL OR mCustomerPassThru.StateUID = 0)';
		}


		if (!empty($CountyUID)) {
			$where[] ='(mCustomerPassThru.CountyUID = '.$CountyUID.')';
		}
		else{
			$where[] = '(mCustomerPassThru.CountyUID IS NULL OR mCustomerPassThru.CountyUID = 0)';
		}

		if (!empty($ZipCode)) {
			$where[] = '(mCustomerPassThru.ZipCode = '.$ZipCode.')';
		}
		else {
			$where[] = '(mCustomerPassThru.ZipCode IS NULL OR mCustomerPassThru.ZipCode = 0)';
		}

		if (!empty($CityUID)) {
			$where[] ='(mCustomerPassThru.CityUID = '.$CityUID.')';
		}
		else{
			$where[] = '(mCustomerPassThru.CityUID IS NULL OR mCustomerPassThru.CityUID = 0)';
		}

		if (!empty($OverrideReasonUID)) {
			$where[] ='(mCustomerPassThru.OverrideReasonUID = '.$OverrideReasonUID.')';
		}
		else{
			$where[] = '(mCustomerPassThru.OverrideReasonUID IS NULL OR mCustomerPassThru.OverrideReasonUID = 0)';
		}


		if(!empty($where)) {
			$querywhere = ' AND '. implode(' AND ', $where);
		}
		
		if(!empty($CustomerUID))
		{
			return $this->db->query("DELETE FROM mCustomerPassThru WHERE CustomerUID = '".$CustomerUID."' $querywhere");
		}

	}


	function InsertPassThru($data,$CustomerUID){
		$this->db->trans_start();
		$data['CustomerUID'] = $CustomerUID;
		$data['CreatedOn'] = date('Y-m-d H:i:s');
		$data['CreatedByUserUID'] = $this->loggedid;
		$data['ModifiedByUserUID'] = $this->loggedid;
		$data['ModifiedOn'] =  date('Y-m-d H:i:s');
		$this->db->insert('mCustomerPassThru', $data);
		
		$msg = 'PassThrough Cost <b>$'.$data['Cost'].'</b> is created with </b>';

		if(!empty($data['OverrideReasonUID'])){
			$OverrideReason = $this->db->query("SELECT * FROM moverridereasons WHERE OverrideReasonUID = ".$data['OverrideReasonUID']."")->row()->OverrideReasonDescription;
			$msg .= ' FeeType: <b>'.$OverrideReason.'</b>';
		}

		if(!empty($data['StateUID'])){
			$State = $this->common_model->GetStatebyUID($data['StateUID'])[0]->StateName;
			$msg .= ' State: <b>'.$State.'</b>';
		}

		if(!empty($data['CountyUID'])){
			$County = $this->common_model->GetCountybyUID($data['CountyUID'])[0]->CountyName;
			$msg .= ' County: <b>'.$County.'</b>';
		}

		if(!empty($data['CityUID'])){
			$City =  $this->common_model->GetCitybyUID($data['CityUID'])[0]->CityName;
			$msg .= ' City: <b>'.$City.'</b>';
		}

		if(!empty($data['ZipCode'])){
			$msg .= ' ZipCode: <b>'.$data['ZipCode'].'</b>';
		}
		
		

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return FALSE;
		} else {

			$this->db->trans_commit();
			if($msg != ""){
				$InsetData = array(
					'UserUID' => $this->loggedid,
					'ModuleName' => 'Customers',
					'Feature' => $CustomerUID,
					'Content' => htmlentities($msg),
					'DateTime' => date('Y-m-d H:i:s'));
				$this->common_model->InsertAuditTrail($InsetData);
			}
			return TRUE;
		}
	}


	function UpdatePassThru($data,$CustomerUID){
		$this->db->trans_start();
		$data['ModifiedByUserUID'] = $this->loggedid;
		$data['ModifiedOn'] =  date('Y-m-d H:i:s');
		if(!empty($data['PassThruUID'])){
			$PreviousPassThru = $this->db->query("SELECT * FROM mCustomerPassThru WHERE PassThruUID = ".$data['PassThruUID']."")->row();
		}

		
		$this->db->where('PassThruUID',$data['PassThruUID']);
		$this->db->update('mCustomerPassThru', $data);

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return FALSE;
		}else {
			$msg = '';
			$Changed = false;

			if((!empty($PreviousPassThru->OverrideReasonUID)&&!empty($data['OverrideReasonUID']))
				&& ($PreviousPassThru->OverrideReasonUID!=$data['OverrideReasonUID'])){ 
				$Changed = true;
			$OverrideReason = $this->db->query("SELECT * FROM moverridereasons WHERE OverrideReasonUID = ".$PreviousPassThru->OverrideReasonUID."")->row()->OverrideReasonDescription;
			$OverrideReason2 = $this->db->query("SELECT * FROM moverridereasons WHERE OverrideReasonUID = ".$data['OverrideReasonUID']."")->row()->OverrideReasonDescription;
			$msg .= '<b>FeeType</b> changed from <b>'.$OverrideReason.'</b> To <b>'.$OverrideReason2.'</b>';	
		}else if(!empty($PreviousPassThru->OverrideReasonUID)&&empty($data['OverrideReasonUID'])){
			$Changed = true;
			$OverrideReason = $this->db->query("SELECT * FROM moverridereasons WHERE OverrideReasonUID = ".$PreviousPassThru->OverrideReasonUID."")->row()->OverrideReasonDescription;
			$msg .= '<b>FeeType</b> changed from <b>'.$OverrideReason.'</b> To <b>NA</b>';	
		}else if(empty($PreviousPassThru->OverrideReasonUID)&&!empty($data['OverrideReasonUID'])){
			$Changed = true;
			$OverrideReason2 = $this->db->query("SELECT * FROM moverridereasons WHERE OverrideReasonUID = ".$data['OverrideReasonUID']."")->row()->OverrideReasonDescription;
			$msg .= '<b>FeeType</b> changed from <b>NA</b> To <b>'.$OverrideReason2.'</b>';	
		}else{
			if(!empty($data['OverrideReasonUID'])){
				$OverrideReason = $this->db->query("SELECT * FROM moverridereasons WHERE OverrideReasonUID = ".$data['OverrideReasonUID']."")->row()->OverrideReasonDescription;
				$msg .= 'For FeeType: <b>'.$OverrideReason.'</b>';
			}
		}
		if((!empty($PreviousPassThru->StateUID)&&!empty($data['StateUID']))
			&& ($PreviousPassThru->StateUID != $data['StateUID'])){
			$Changed = true;
		$state = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$PreviousPassThru->StateUID."")->row()->StateName;
		$state2 = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$data['StateUID']."")->row()->StateName;
		$msg .= ' <b>State</b> changed from <b>'.$state.'</b> To <b>'.$state2.'</b>';	
	}else if(!empty($PreviousPassThru->StateUID)&&empty($data['StateUID'])){
		$Changed = true;
		$state = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$PreviousPassThru->StateUID."")->row()->StateName;
		$msg .= ' <b>State</b> changed from <b>'.$state.'</b> To <b>NA</b>';	
	}else if(empty($PreviousPassThru->StateUID)&&!empty($data['StateUID'])){
		$Changed = true;
		$state2 = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$data['StateUID']."")->row()->StateName;
		$msg .= ' <b>State</b> changed from <b>NA</b> To <b>'.$state2.'</b>';	
	}else{
		if(!empty($data['StateUID'])){
			$state = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$data['StateUID']."")->row()->StateName;
			$msg .= ' For State: <b>'.$state.'</b>';
		}
	}

	if((!empty($PreviousPassThru->CountyUID)&&!empty($data['CountyUID']))
		&& ($PreviousPassThru->CountyUID!=$data['CountyUID'])){
		$Changed = true;
	$County = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$PreviousPassThru->CountyUID."")->row()->CountyName;
	$County2 = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$data['CountyUID']."")->row()->CountyName;
	$msg .= ' <b>County</b> changed from <b>'.$County.'</b> To <b>'.$County2.'</b>';	
}else if(!empty($PreviousPassThru->CountyUID)&&empty($data['CountyUID'])){
	$Changed = true;
	$County = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$PreviousPassThru->CountyUID."")->row()->CountyName;
	$msg .= ' <b>County</b> changed from <b>'.$County.'</b> To <b>NA</b>';	
}else if(empty($PreviousPassThru->CountyUID)&&!empty($data['CountyUID'])){
	$Changed = true;
	$County2 = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$data['CountyUID']."")->row()->CountyName;
	$msg .= ' <b>County</b> changed from <b>NA</b> To <b>'.$County2.'</b>';	
}else{
	if(!empty($data['CountyUID'])){
		$County = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$PreviousPassThru->CountyUID."")->row()->CountyName;
		$msg .= ' For County: <b>'.$County.'</b>';
	}
}

if((!empty($PreviousPassThru->CityUID)&&!empty($data['CityUID']))
	&& ($PreviousPassThru->CityUID!=$data['CityUID'])){
	$Changed = true;
$City = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$PreviousPassThru->CityUID."")->row()->CityName;
$City2 = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$data['CityUID']."")->row()->CityName;
$msg .= ' <b>City</b> changed from <b>'.$City.'</b> To <b>'.$City2.'</b>';	
}else if(!empty($PreviousPassThru->CityUID)&&empty($data['CityUID'])){
	$Changed = true;
	$City = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$PreviousPassThru->CityUID."")->row()->CityName;
	$msg .= ' <b>City</b> changed from <b>'.$City.'</b> To <b>NA</b>';	
}else if(empty($PreviousPassThru->CityUID)&&!empty($data['CityUID'])){
	$City2 = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$data['CityUID']."")->row()->CityName;
	$msg .= ' <b>City</b> changed from <b>NA</b> To <b>'.$City2.'</b>';	
}else{
	if(!empty($data['CityUID'])){
		$City = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$data['CityUID']."")->row()->CityName;
		$msg .= ' For City: <b>'.$City.'</b>';
	}
}

if((!empty($PreviousPassThru->ZipCode)&&!empty($data['ZipCode']))
	&& ($PreviousPassThru->ZipCode!=$data['ZipCode'])){
	$Changed = true;
$msg .= ' <b>ZipCode</b> changed from <b>'.$PreviousPassThru->ZipCode.'</b> To <b>'.$data['ZipCode'].'</b>';	
}else if(!empty($PreviousPassThru->ZipCode)&&empty($data['ZipCode'])){
	$Changed = true;
	$msg .= ' <b>ZipCode</b> changed from <b>'.$PreviousPassThru->ZipCode.'</b> To <b>NA</b>';	
}else if(empty($PreviousPassThru->ZipCode)&&!empty($data['ZipCode'])){
	$Changed = true;
	$msg .= ' <b>ZipCode</b> changed from <b>NA</b> To <b>'.$data['ZipCode'].'</b>';	
}else{
	if(!empty($data['ZipCode'])){
		$msg .= ' For ZipCode: <b>'.$data['ZipCode'].'</b>';
	}
}

if((!empty($PreviousPassThru->Cost)&&!empty($data['Cost']))
	&& ($PreviousPassThru->Cost!=$data['Cost'])){
	$Changed = true;
$msg .= ' <b>Cost</b> changed from <b>'.$PreviousPassThru->Cost.'</b> To <b>'.$data['Cost'].'</b>';	
}else if(!empty($PreviousPassThru->Cost)&&empty($data['Cost'])){
	$Changed = true;
	$msg .= ' <b>Cost</b> changed from <b>'.$PreviousPassThru->Cost.'</b> To <b>NA</b>';	
}else if(empty($PreviousPassThru->Cost)&&!empty($data['Cost'])){
	$Changed = true;
	$msg .= ' <b>Cost</b> changed from <b>NA</b> To <b>'.$data['Cost'].'</b>';	
}else{
	if(!empty($data['Cost'])){
		$msg .= ' For Cost: <b>$'.$data['Cost'].'</b>';
	}
}
if($msg != "" && $Changed == true){
	$msg = 'PassThrough Cost '.$msg;
	$InsetData = array(
		'UserUID' => $this->loggedid,
		'ModuleName' => 'Customers',
		'Feature' => $CustomerUID,
		'Content' => htmlentities($msg),
		'DateTime' => date('Y-m-d H:i:s'));
	$this->common_model->InsertAuditTrail($InsetData);
}

$this->db->trans_commit();

return TRUE;
}
}


function DeletePassThru($PassThruUID){
		//audit trail
	$this->db->select('*');
	$this->db->from('mCustomerPassThru');
	$this->db->where(array("PassThruUID"=>$PassThruUID));
	$value = $this->db->get()->row();

	$this->db->where(array("PassThruUID"=>$PassThruUID));    
	$this->db->delete('mCustomerPassThru');

	$msg = "";

	if(!empty($value->OverrideReasonUID)){
		$OverrideReason = $this->db->query("SELECT * FROM moverridereasons WHERE OverrideReasonUID = ".$value->OverrideReasonUID."")->row()->OverrideReasonDescription;
		$msg .= 'For FeeType: <b>'.$OverrideReason.'</b>';
	}
	if(!empty($value->StateUID)){
		$state = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$value->StateUID."")->row()->StateName;
		$msg .= ' For State: <b>'.$state.'</b>';
	}
	if(!empty($value->CountyUID)){
		$county = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$value->CountyUID."")->row()->StateName;
		$msg .= ' For County: <b>'.$county.'</b>';
	}
	if(!empty($value->CityUID)){
		$city = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$value->CityUID."")->row()->StateName;
		$msg .= ' For City: <b>'.$city.'</b>';
	}
	if(!empty($value->ZipCode)){
		$msg .= ' For ZipCode: <b>'.$value->ZipCode.'</b>';
	}
	if(!empty($value->Cost)){
		$msg .= ' with Cost: <b>$'.$value->Cost.'</b>';
	}
	$msg .= ' is Deleted';

	if($this->db->affected_rows() > 0)
	{
		if($msg != ""){
			$InsetData = array(
				'UserUID' => $this->loggedid,
				'ModuleName' => 'Customers',
				'Feature' => $value->CustomerUID,
				'Content' => htmlentities($msg),
				'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($InsetData);
		}
		return TRUE;
	}else{
		return FALSE;
	}
}


function getBillingTriggers($CustomerUID){
	$this->db->select('*');
	$this->db->from('mCustomerBillingTriggers');
	$this->db->where('CustomerUID',$CustomerUID);
	return $this->db->get()->result();
}

/** CUSTOMER BILLINGS */

function _getCustomerBillingTrigger_query($post, $countall=false, $count_filtered=false)
{
	$this->db->select("mCustomerBillingTriggers.*");
	$this->db->from('mCustomerBillingTriggers');
	if (!empty($post['search']['value']) || !empty($post['order'])) {
		$this->db->join('mcustomers','mcustomers.CustomerUID = mCustomerBillingTriggers.CustomerUID','left');
		$this->db->join('mcities','mcities.ZipCode = mCustomerBillingTriggers.ZipCode AND mCustomerBillingTriggers.ZipCode!=0','left');
		$this->db->join('mcounties','mcounties.CountyUID = mCustomerBillingTriggers.CountyUID AND mCustomerBillingTriggers.CountyUID!=0','left');
		$this->db->join('moverridereasons','moverridereasons.OverrideReasonUID = mCustomerBillingTriggers.OverrideReasonUID','left');
		$this->db->join('mstates','mstates.StateUID = mCustomerBillingTriggers.StateUID OR mstates.StateUID=mcities.StateUID','left');
	}
	$this->db->where('mCustomerBillingTriggers.CustomerUID', $post['CustomerUID']);

	if(! empty($post['StateUID'])) {
		$this->db->where('`mCustomerBillingTriggers.`StateUID`',$post['StateUID']);
	}

	if(! empty($post['CountyUID'])) {
		$this->db->where('`mCustomerBillingTriggers`.`CountyUID`',$post['CountyUID']);
	}

	if(! empty($post['CityUID'])) {
		$this->db->where('`mCustomerBillingTriggers`.`CityUID`',$post['CityUID']);
	}

	if(! empty($post['ZipCode'])) {
		$this->db->where('`mCustomerBillingTriggers`.`ZipCode`',$post['ZipCode']);
	}
	if(! empty($post['WorkflowModuleUID'])) {
		$this->db->where('`mCustomerBillingTriggers`.`WorkflowModuleUID`',$post['WorkflowModuleUID']);
	}
	if(! empty($post['BillingPeriod'])) {
		$this->db->where('`mCustomerBillingTriggers`.`BillingPeriod`',$post['BillingPeriod']);
	}


	if($countall == false) {
		if (isset($post['search']['value']) && !empty($post['search']['value'])) {
			$like = "";
				foreach ($post['column_search'] as $key => $item) { // loop column 
					// if datatable send POST for search
					if ($key === 0) { // first loop
						$like .= "( ".$item." LIKE '%".$post['search']['value']."%' "; 
					} else {
						$like .= " OR ".$item." LIKE '%".$post['search']['value']."%' ";    
					}
				}
				$like .= ") ";
				$this->db->where($like, null, false);
			}

			if (!empty($post['order'])) 
			{ 
				// here order processing 
				if($post['column_order'][$post['order'][0]['column']]!='')
				{
					$this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);    
				}    
			} 
			else{
				$this->db->order_by('mCustomerBillingTriggers.CustomerBillingTriggerUID', 'ASC');
			}
		}

		if ($countall == true || $count_filtered == true) {
			return $this->db->count_all_results();
		}

		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}
		return $this->db->get()->result();
	}

	function getBillingExists($CustomerUID,$ProductUID,$SubProductUID,$StateUID,$CountyUID,$ZipCode,$CustomerBillingTriggerUID = false,$CityUID, $WorkflowModuleUID, $BillingPeriod)
	{
		$where = [];
		$querywhere = false;

		if(!empty($CustomerBillingTriggerUID)) {
			$where[] = ' mCustomerBillingTriggers.CustomerBillingTriggerUID != '.$CustomerBillingTriggerUID;
		}

		if(!empty($ProductUID)) {
			$where[] = '(mCustomerBillingTriggers.ProductUID = '.$ProductUID.')';
		}  else {
			$where[] = '(mCustomerBillingTriggers.ProductUID IS NULL OR mCustomerBillingTriggers.ProductUID = 0)';
		}

		if(!empty($SubProductUID)) {
			$where[] = '(mCustomerBillingTriggers.SubProductUID = '.$SubProductUID.')';
		}  else {
			$where[] = '(mCustomerBillingTriggers.SubProductUID IS NULL OR mCustomerBillingTriggers.SubProductUID = 0)';
		}
		if(!empty($BillingPeriod)) {
			$where[] = '(mCustomerBillingTriggers.BillingPeriod = "'.$BillingPeriod.'")';
		}  else {
			$where[] = '(mCustomerBillingTriggers.BillingPeriod IS NULL)';
		}

		if(!empty($StateUID)) {
			$where[] = '(mCustomerBillingTriggers.StateUID = '.$StateUID.')';
		}  else {
			$where[] = '(mCustomerBillingTriggers.StateUID IS NULL OR mCustomerBillingTriggers.StateUID = 0)';
		}

		if (!empty($CountyUID)) {
			$where[] ='(mCustomerBillingTriggers.CountyUID = '.$CountyUID.')';
		}
		else{
			$where[] = '(mCustomerBillingTriggers.CountyUID IS NULL OR mCustomerBillingTriggers.CountyUID = 0)';
		}

		if (!empty($ZipCode)) {
			$where[] = '(mCustomerBillingTriggers.ZipCode = '.$ZipCode.')';
		}
		else {
			$where[] = '(mCustomerBillingTriggers.ZipCode IS NULL OR mCustomerBillingTriggers.ZipCode = 0)';
		}

		if (!empty($CityUID)) {
			$where[] ='(mCustomerBillingTriggers.CityUID = '.$CityUID.')';
		}
		else{
			$where[] = '(mCustomerBillingTriggers.CityUID IS NULL OR mCustomerBillingTriggers.CityUID = 0)';
		}
		if (!empty($WorkflowModuleUID)) {
			
			$where[] ='(mCustomerBillingTriggers.WorkflowModuleUID = "'.$WorkflowModuleUID.'")';
		}
		else{
			$where[] = '(mCustomerBillingTriggers.WorkflowModuleUID IS NULL OR mCustomerBillingTriggers.WorkflowModuleUID = 0)';
		}

		if(!empty($where)) {
			$querywhere = ' AND '. implode(' AND ', $where);
		}

		return $this->db->query("SELECT mCustomerBillingTriggers.*,mproducts.ProductName,msubproducts.SubProductName,mstates.StateName,mstates.StateCode,mcounties.CountyName,mcustomers.CustomerName,mcities.CityUID,mcities.CityName
			FROM mCustomerBillingTriggers 	
			LEFT JOIN mstates ON mstates.StateUID = mCustomerBillingTriggers.StateUID
			LEFT JOIN mproducts ON mproducts.ProductUID = mCustomerBillingTriggers.ProductUID
			LEFT JOIN msubproducts ON msubproducts.SubProductUID = mCustomerBillingTriggers.SubProductUID
			LEFT JOIN mcounties ON mcounties.CountyUID = mCustomerBillingTriggers.CountyUID
			LEFT JOIN mcustomers ON mcustomers.CustomerUID = mCustomerBillingTriggers.CustomerUID
			LEFT JOIN mcities ON mcities.CityUID = mCustomerBillingTriggers.CityUID
			WHERE mCustomerBillingTriggers.CustomerUID = ".$CustomerUID." $querywhere",false)->result();

	}

	function UpdateBillingTrigger($data,$CustomerUID){
		$this->db->trans_start();
		$data['ModifiedByUserUID'] = $this->loggedid;
		$data['ModifiedOn'] =  date('Y-m-d H:i:s');

		if(!empty($data['CustomerBillingTriggerUID'])){
			$PreviousBillingTrigger = $this->db->query("SELECT * FROM mCustomerBillingTriggers WHERE CustomerBillingTriggerUID = ".$data['CustomerBillingTriggerUID']."")->row();
		}
		
		$this->db->where('CustomerBillingTriggerUID',$data['CustomerBillingTriggerUID']);
		$this->db->update('mCustomerBillingTriggers', $data);

		$this->db->trans_complete();

		if($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return FALSE;
		}else {
			$msg = '';
			$Changed = false;

			// Billing Trigger
			if((!empty($PreviousBillingTrigger->WorkflowModuleUID)&&!empty($data['WorkflowModuleUID']))
				&& ($PreviousBillingTrigger->WorkflowModuleUID != $data['WorkflowModuleUID'])){
				$Changed = true;
			$WorkflowModuleUID1 = $this->db->query("SELECT WorkflowModuleName FROM mworkflowmodules WHERE WorkflowModuleUID IN (".$PreviousBillingTrigger->WorkflowModuleUID.")")->row()->WorkflowModuleName;
			$WorkflowModuleUID2 = $this->db->query("SELECT WorkflowModuleName FROM mworkflowmodules WHERE WorkflowModuleUID IN (".$data['WorkflowModuleUID'].")")->row()->WorkflowModuleName;
			$msg .= ' <b>Billing Trigger</b> changed from <b>'.$WorkflowModuleUID1.'</b> To <b>'.$WorkflowModuleUID2.'</b>';	
		}else if(!empty($PreviousBillingTrigger->WorkflowModuleUID)&&empty($data['WorkflowModuleUID'])){
			$Changed = true;
			$WorkflowModuleUID1 = $this->db->query("SELECT WorkflowModuleName FROM mworkflowmodules WHERE WorkflowModuleUID IN (".$PreviousBillingTrigger->WorkflowModuleUID.")")->row()->WorkflowModuleName;
			$msg .= ' <b>Billing Trigger</b> changed from <b>'.$WorkflowModuleUID1.'</b> To <b>NA</b>';	
		}else if(empty($PreviousBillingTrigger->WorkflowModuleUID)&&!empty($data['WorkflowModuleUID'])){
			$Changed = true;
			$WorkflowModuleUID2 = $this->db->query("SELECT WorkflowModuleName FROM mworkflowmodules WHERE WorkflowModuleUID IN (".$data['WorkflowModuleUID'].")")->row()->WorkflowModuleName;
			$msg .= ' <b>Billing Trigger</b> changed from <b>NA</b> To <b>'.$WorkflowModuleUID2.'</b>';	
		}else{
			if(!empty($data['WorkflowModuleUID'])){
				$WorkflowModuleUID2 = $this->db->query("SELECT WorkflowModuleName FROM mworkflowmodules WHERE WorkflowModuleUID IN (".$data['WorkflowModuleUID'].")")->row()->WorkflowModuleName;
				$msg .= ' For Billing Trigger: <b>'.$WorkflowModuleUID2.'</b>';
			}
		}

			// Product
		if((!empty($PreviousBillingTrigger->ProductUID)&&!empty($data['ProductUID']))
			&& ($PreviousBillingTrigger->ProductUID != $data['ProductUID'])){
			$Changed = true;
		$ProductName1 = $this->db->query("SELECT ProductName FROM mproducts WHERE ProductUID = ".$PreviousBillingTrigger->ProductUID."")->row()->ProductName;
		$ProductName2 = $this->db->query("SELECT ProductName FROM mproducts WHERE ProductUID = ".$data['ProductUID']."")->row()->ProductName;
		$msg .= ' Product changed from <b>'.$ProductName1.'</b> To <b>'.$ProductName2.'</b>';	
	}else if(!empty($PreviousBillingTrigger->ProductUID)&&empty($data['ProductUID'])){
		$Changed = true;
		$ProductName1 = $this->db->query("SELECT ProductName FROM mproducts WHERE ProductUID = ".$PreviousBillingTrigger->ProductUID."")->row()->ProductName;
		$msg .= ' Product changed from <b>'.$ProductName1.'</b> To <b>NA</b>';	
	}else if(empty($PreviousBillingTrigger->ProductUID)&&!empty($data['ProductUID'])){
		$Changed = true;
		$ProductName2 = $this->db->query("SELECT ProductName FROM mproducts WHERE ProductUID = ".$data['ProductUID']."")->row()->ProductName;
		$msg .= ' Product changed from <b>NA</b> To <b>'.$ProductName2.'</b>';	
	}else{
		if(!empty($data['ProductUID'])){
			$ProductName2 = $this->db->query("SELECT ProductName FROM mproducts WHERE ProductUID = ".$data['ProductUID']."")->row()->ProductName;
			$msg .= ' For Product: <b>'.$ProductName2.'</b>';
		}
	}

			// SubProduct
	if((!empty($PreviousBillingTrigger->SubProductUID)&&!empty($data['SubProductUID']))
		&& ($PreviousBillingTrigger->SubProductUID != $data['SubProductUID'])){
		$Changed = true;
	$SubProductName1 = $this->db->query("SELECT SubProductName FROM msubproducts WHERE SubProductUID = ".$PreviousBillingTrigger->SubProductUID."")->row()->ProductName;
	$SubProductName2 = $this->db->query("SELECT SubProductName FROM msubproducts WHERE SubProductUID = ".$data['SubProductUID']."")->row()->ProductName;
	$msg .= ' SubProduct changed from <b>'.$SubProductName1.'</b> To <b>'.$SubProductName2.'</b>';	
}else if(!empty($PreviousBillingTrigger->SubProductUID)&&empty($data['SubProductUID'])){
	$Changed = true;
	$SubProductName1 = $this->db->query("SELECT ProductName FROM msubproducts WHERE SubProductUID = ".$PreviousBillingTrigger->SubProductUID."")->row()->ProductName;
	$msg .= ' SubProduct changed from <b>'.$SubProductName1.'</b> To <b>NA</b>';	
}else if(empty($PreviousBillingTrigger->SubProductUID)&&!empty($data['SubProductUID'])){
	$Changed = true;
	$SubProductName2 = $this->db->query("SELECT SubProductName FROM msubproducts WHERE SubProductUID = ".$data['SubProductUID']."")->row()->ProductName;
	$msg .= ' SubProduct changed from <b>NA</b> To <b>'.$SubProductName2.'</b>';	
}else{
	if(!empty($data['SubProductUID'])){
		$SubProductName2 = $this->db->query("SELECT SubProductName FROM msubproducts WHERE SubProductUID = ".$data['SubProductUID']."")->row()->ProductName;
		$msg .= ' For SubProduct: <b>'.$SubProductName2.'</b>';
	}
}

			// BillingPeriod
if((!empty($PreviousBillingTrigger->BillingPeriod)&&!empty($data['BillingPeriod']))
	&& ($PreviousBillingTrigger->BillingPeriod != $data['BillingPeriod'])){
	$Changed = true;
$msg .= ' BillingPeriod changed from <b>'.$PreviousBillingTrigger->BillingPeriod.'</b> To <b>'.$data['BillingPeriod'].'</b>';	
}else if(!empty($PreviousBillingTrigger->BillingPeriod)&&empty($data['BillingPeriod'])){
	$Changed = true;
	$msg .= ' BillingPeriod changed from <b>'.$PreviousBillingTrigger->BillingPeriod.'</b> To <b>NA</b>';	
}else if(empty($PreviousBillingTrigger->BillingPeriod)&&!empty($data['BillingPeriod'])){
	$Changed = true;
	$msg .= ' BillingPeriod changed from <b>NA</b> To <b>'.$data['BillingPeriod'].'</b>';	
}else{
	if(!empty($data['SubProductUID'])){
		$msg .= ' For BillingPeriod: <b>'.$data['BillingPeriod'].'</b>';
	}
}


if((!empty($PreviousBillingTrigger->StateUID)&&!empty($data['StateUID']))
	&& ($PreviousBillingTrigger->StateUID != $data['StateUID'])){
	$Changed = true;
$state = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$PreviousBillingTrigger->StateUID."")->row()->StateName;
$state2 = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$data['StateUID']."")->row()->StateName;
$msg .= ' State changed from <b>'.$state.'</b> To <b>'.$state2.'</b>';	
}else if(!empty($PreviousBillingTrigger->StateUID)&&empty($data['StateUID'])){
	$Changed = true;
	$state = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$PreviousBillingTrigger->StateUID."")->row()->StateName;
	$msg .= ' State changed from <b>'.$state.'</b> To <b>NA</b>';	
}else if(empty($PreviousBillingTrigger->StateUID)&&!empty($data['StateUID'])){
	$Changed = true;
	$state2 = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$data['StateUID']."")->row()->StateName;
	$msg .= ' State changed from <b>NA</b> To <b>'.$state2.'</b>';	
}else{
	if(!empty($data['StateUID'])){
		$state = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$data['StateUID']."")->row()->StateName;
		$msg .= ' For State: <b>'.$state.'</b>';
	}
}

if((!empty($PreviousBillingTrigger->CountyUID)&&!empty($data['CountyUID']))
	&& ($PreviousBillingTrigger->CountyUID!=$data['CountyUID'])){
	$Changed = true;
$County = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$PreviousBillingTrigger->CountyUID."")->row()->CountyName;
$County2 = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$data['CountyUID']."")->row()->CountyName;
$msg .= ' County changed from <b>'.$County.'</b> To <b>'.$County2.'</b>';	
}else if(!empty($PreviousBillingTrigger->CountyUID)&&empty($data['CountyUID'])){
	$Changed = true;
	$County = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$PreviousBillingTrigger->CountyUID."")->row()->CountyName;
	$msg .= ' County changed from <b>'.$County.'</b> To <b>NA</b>';	
}else if(empty($PreviousBillingTrigger->CountyUID)&&!empty($data['CountyUID'])){
	$Changed = true;
	$County2 = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$data['CountyUID']."")->row()->CountyName;
	$msg .= ' County changed from <b>NA</b> To <b>'.$County2.'</b>';	
}else{
	if(!empty($data['CountyUID'])){
		$County = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$PreviousBillingTrigger->CountyUID."")->row()->CountyName;
		$msg .= ' For County: <b>'.$County.'</b>';
	}
}

if((!empty($PreviousBillingTrigger->CityUID)&&!empty($data['CityUID']))
	&& ($PreviousBillingTrigger->CityUID!=$data['CityUID'])){
	$Changed = true;
$City = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$PreviousBillingTrigger->CityUID."")->row()->CityName;
$City2 = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$data['CityUID']."")->row()->CityName;
$msg .= ' City changed from <b>'.$City.'</b> To <b>'.$City2.'</b>';	
}else if(!empty($PreviousBillingTrigger->CityUID)&&empty($data['CityUID'])){
	$Changed = true;
	$City = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$PreviousBillingTrigger->CityUID."")->row()->CityName;
	$msg .= ' City changed from <b>'.$City.'</b> To <b>NA</b>';	
}else if(empty($PreviousBillingTrigger->CityUID)&&!empty($data['CityUID'])){
	$City2 = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$data['CityUID']."")->row()->CityName;
	$msg .= ' City changed from <b>NA</b> To <b>'.$City2.'</b>';	
}else{
	if(!empty($data['CityUID'])){
		$City = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$data['CityUID']."")->row()->CityName;
		$msg .= ' For City: <b>'.$City.'</b>';
	}
}

if((!empty($PreviousBillingTrigger->ZipCode)&&!empty($data['ZipCode']))
	&& ($PreviousBillingTrigger->ZipCode!=$data['ZipCode'])){
	$Changed = true;
$msg .= ' ZipCode changed from <b>'.$PreviousBillingTrigger->ZipCode.'</b> To <b>'.$data['ZipCode'].'</b>';	
}else if(!empty($PreviousBillingTrigger->ZipCode)&&empty($data['ZipCode'])){
	$Changed = true;
	$msg .= ' ZipCode changed from <b>'.$PreviousBillingTrigger->ZipCode.'</b> To <b>NA</b>';	
}else if(empty($PreviousBillingTrigger->ZipCode)&&!empty($data['ZipCode'])){
	$Changed = true;
	$msg .= ' ZipCode changed from <b>NA</b> To <b>'.$data['ZipCode'].'</b>';	
}else{
	if(!empty($data['ZipCode'])){
		$msg .= ' For ZipCode: <b>'.$data['ZipCode'].'</b>';
	}
}

if($msg != "" && $Changed == true){
	$msg = $msg;
	$InsetData = array(
		'UserUID' => $this->loggedid,
		'ModuleName' => 'Customers',
		'Feature' => $CustomerUID,
		'Content' => htmlentities($msg),
		'DateTime' => date('Y-m-d H:i:s'));
	$this->common_model->InsertAuditTrail($InsetData);
}

$this->db->trans_commit();

return TRUE;
}
}


function InsertBillingTrigger($data,$CustomerUID){
	$this->db->trans_start();
	$data['CustomerUID'] = $CustomerUID;
	$data['CreatedOn'] = date('Y-m-d H:i:s');
	$data['CreatedByUserUID'] = $this->loggedid;
	$data['ModifiedByUserUID'] = $this->loggedid;
	$data['ModifiedOn'] =  date('Y-m-d H:i:s');
	
	$this->db->select('*');
	$this->db->from('mCustomerBillingTriggers');
	$this->db->where('ProductUID',$data['ProductUID']);
	$this->db->where('SubProductUID',$data['SubProductUID']);
	if(!empty($data['StateUID'])){
		$this->db->where('StateUID',$data['StateUID']);
	}
	if(!empty($data['CountyUID'])){
		$this->db->where('CountyUID',$data['CountyUID']);
	}
	if(!empty($data['CityUID'])){
		$this->db->where('CityUID',$data['CityUID']);
	}
	if(!empty($data['ZipCode'])){
		$this->db->where('ZipCode',$data['ZipCode']);
	}
	if(!empty($data['WorkflowModuleUID'])){
		$this->db->where('WorkflowModuleUID',$data['WorkflowModuleUID']);
	}
	$Exists = $this->db->get()->result();
	if(empty($Exists)){
		$this->db->insert('mCustomerBillingTriggers', $data);
	}

	if(!empty($data['WorkflowModuleUID'])){
		$WorkflowModuleUID = $this->db->query("SELECT WorkflowModuleName FROM mworkflowmodules WHERE WorkflowModuleUID = ".$data['WorkflowModuleUID']."")->row()->WorkflowModuleName;
	}else{
		$WorkflowModuleUID = '';
	}
	$msg = 'BillingTrigger <b>'.$WorkflowModuleUID.'</b> is created for </b>';

	if(!empty($data['ProductUID'])){
		$ProductName = $this->db->query("SELECT ProductName FROM mproducts WHERE ProductUID = ".$data['ProductUID']."")->row()->ProductName;
		$msg .= ' Product: <b>'.$ProductName.'</b>';
	}

	if(!empty($data['SubProductUID'])){
		$SubProductName = $this->db->query("SELECT SubProductName FROM msubproducts WHERE SubProductUID = ".$data['SubProductUID']."")->row()->SubProductName;
		$msg .= ' SubProduct: <b>'.$SubProductName.'</b>';
	}

	if(!empty($data['BillingPeriod'])){
		$msg .= ' BillingPeriod: <b>'.$data['BillingPeriod'].'</b>';
	}

	$msg .= ' With ';
	if(!empty($data['StateUID'])){
		$State = $this->common_model->GetStatebyUID($data['StateUID'])[0]->StateName;
		$msg .= ' State: <b>'.$State.'</b>';
	}

	if(!empty($data['CountyUID'])){
		$County = $this->common_model->GetCountybyUID($data['CountyUID'])[0]->CountyName;
		$msg .= ' County: <b>'.$County.'</b>';
	}

	if(!empty($data['CityUID'])){
		$City =  $this->common_model->GetCitybyUID($data['CityUID'])[0]->CityName;
		$msg .= ' City: <b>'.$City.'</b>';
	}

	if(!empty($data['ZipCode'])){
		$msg .= ' ZipCode: <b>'.$data['ZipCode'].'</b>';
	}



	$this->db->trans_complete();

	if($this->db->trans_status() === FALSE)
	{
		$this->db->trans_rollback();
		return FALSE;
	} else {

		$this->db->trans_commit();
		if($msg != ""){
			$InsetData = array(
				'UserUID' => $this->loggedid,
				'ModuleName' => 'Customers',
				'Feature' => $CustomerUID,
				'Content' => htmlentities($msg),
				'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($InsetData);
		}
		return TRUE;
	}
}


function DeleteBillingTrigger($CustomerBillingTriggerUID){
		//audit trail
	$this->db->select('*');
	$this->db->from('mCustomerBillingTriggers');
	$this->db->where(array("CustomerBillingTriggerUID"=>$CustomerBillingTriggerUID));
	$value = $this->db->get()->row();

	$this->db->where(array("CustomerBillingTriggerUID"=>$CustomerBillingTriggerUID));    
	$this->db->delete('mCustomerBillingTriggers');

	$msg = "";

	if(!empty($value->WorkflowModuleUID)){
		$WorkflowModuleUID = $this->db->query("SELECT GROUP_CONCAT(WorkflowModuleName) AS WorkflowModuleName FROM mworkflowmodules WHERE WorkflowModuleUID IN (".$value->WorkflowModuleUID.")")->row()->WorkflowModuleName;
		$msg .= 'For BillingTrigger: <b>'.$WorkflowModuleUID.'</b> for';
	}

	if(!empty($value->ProductUID)){
		$Product = $this->db->query("SELECT * FROM mproducts WHERE ProductUID = ".$value->ProductUID."")->row()->ProductName;
		$msg .= ' For Product: <b>'.$Product.'</b>';
	}

	if(!empty($value->SubProductUID)){
		$SubProduct = $this->db->query("SELECT * FROM msubproducts WHERE SubProductUID = ".$value->SubProductUID."")->row()->SubProductName;
		$msg .= ' For SubProduct: <b>'.$SubProduct.'</b>';
	}

	if(!empty($value->StateUID)){
		$state = $this->db->query("SELECT * FROM mstates WHERE StateUID = ".$value->StateUID."")->row()->StateName;
		$msg .= ' For State: <b>'.$state.'</b>';
	}
	if(!empty($value->CountyUID)){
		$county = $this->db->query("SELECT * FROM mcounties WHERE CountyUID = ".$value->CountyUID."")->row()->CountyName;
		$msg .= ' For County: <b>'.$county.'</b>';
	}
	if(!empty($value->CityUID)){
		$city = $this->db->query("SELECT * FROM mcities WHERE CityUID = ".$value->CityUID."")->row()->CityName;
		$msg .= ' For City: <b>'.$city.'</b>';
	}
	if(!empty($value->ZipCode)){
		$msg .= ' For ZipCode: <b>'.$value->ZipCode.'</b>';
	}
	$msg .= ' is Deleted';

	if($this->db->affected_rows() > 0)
	{
		if($msg != ""){
			$InsetData = array(
				'UserUID' => $this->loggedid,
				'ModuleName' => 'Customers',
				'Feature' => $value->CustomerUID,
				'Content' => htmlentities($msg),
				'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($InsetData);
		}
		return TRUE;
	}else{
		return FALSE;
	}
}

function getSubProductsByProductUID($ProductUID){
	$this->db->select('*');
	$this->db->from('msubproducts');
	$this->db->where('ProductUID',$ProductUID);
	return $this->db->get()->result();
}


function deleteExistBilling($CustomerUID,$ProductUID,$SubProductUID,$StateUID,$CountyUID,$ZipCode,$CustomerBillingTriggerUID = false,$CityUID,$WorkflowModuleUID,$BillingPeriod)
{
	$where = [];
	$querywhere = false;

	if(!empty($CustomerBillingTriggerUID)) {
		$where[] = ' mCustomerBillingTriggers.CustomerBillingTriggerUID != '.$CustomerBillingTriggerUID;
	}

	if(!empty($ProductUID)) {
		$where[] = '(mCustomerBillingTriggers.ProductUID = '.$ProductUID.')';
	}  else {
		$where[] = '(mCustomerBillingTriggers.ProductUID IS NULL OR mCustomerBillingTriggers.ProductUID = 0)';
	}

	if(!empty($BillingPeriod)) {
		$where[] = '(mCustomerBillingTriggers.BillingPeriod = "'.$BillingPeriod.'")';
	}  else {
		$where[] = '(mCustomerBillingTriggers.BillingPeriod IS NULL OR mCustomerBillingTriggers.BillingPeriod = "")';
	}

	if(!empty($SubProductUID)) {
		$where[] = '(mCustomerBillingTriggers.SubProductUID = '.$SubProductUID.')';
	}  else {
		$where[] = '(mCustomerBillingTriggers.SubProductUID IS NULL OR mCustomerBillingTriggers.SubProductUID = 0)';
	}
	
	if(!empty($StateUID)) {
		$where[] = '(mCustomerBillingTriggers.StateUID = '.$StateUID.')';
	}  else {
		$where[] = '(mCustomerBillingTriggers.StateUID IS NULL OR mCustomerBillingTriggers.StateUID = 0)';
	}

	if (!empty($CountyUID)) {
		$where[] ='(mCustomerBillingTriggers.CountyUID = '.$CountyUID.')';
	}
	else{
		$where[] = '(mCustomerBillingTriggers.CountyUID IS NULL OR mCustomerBillingTriggers.CountyUID = 0)';
	}

	if (!empty($ZipCode)) {
		$where[] = '(mCustomerBillingTriggers.ZipCode = '.$ZipCode.')';
	}
	else {
		$where[] = '(mCustomerBillingTriggers.ZipCode IS NULL OR mCustomerBillingTriggers.ZipCode = 0)';
	}

	if (!empty($CityUID)) {
		$where[] ='(mCustomerBillingTriggers.CityUID = '.$CityUID.')';
	}
	else{
		$where[] = '(mCustomerBillingTriggers.CityUID IS NULL OR mCustomerBillingTriggers.CityUID = 0)';
	}

	if (!empty($WorkflowModuleUID)) {
		$where[] ='(mCustomerBillingTriggers.WorkflowModuleUID = "'.$WorkflowModuleUID.'")';
	}
	else{
		$where[] = '(mCustomerBillingTriggers.WorkflowModuleUID IS NULL OR mCustomerBillingTriggers.WorkflowModuleUID = 0)';
	}

	if(!empty($where)) {
		$querywhere = ' AND '. implode(' AND ', $where);
	}

	if(!empty($CustomerUID))
	{
		return $this->db->query("DELETE FROM mCustomerBillingTriggers WHERE CustomerUID = '".$CustomerUID."' $querywhere");
	}

}

/******************************************** TASK MANAGEMENT ***************************************************/

function GetTaskandWorkflowDetails(){
	$this->db->select("*");
	$this->db->from("mTask");
	$query = $this->db->get();
	$tasks = $query->result();

	$this->db->select("*");
	$this->db->from('mworkflowmodules');
	$this->db->where(array("mworkflowmodules.WorkflowModuleUID !="=>'5'));
	$query2 = $this->db->get();
	$workflows = $query2->result();
	return array('tasks'=>$tasks,'workflows'=>$workflows);
}

function getAllTasks($SubProductUID,$CustomerUID){
	/*Query*/
	$this->db->select('*, mTask.TaskUID');
	$this->db->from('mTask'); 
	$this->db->join('tCustomerSubProductTasks', 'tCustomerSubProductTasks.TaskUID = mTask.TaskUID AND tCustomerSubProductTasks.SubProductUID = "' . $SubProductUID . '" AND tCustomerSubProductTasks.CustomerUID = "' . $CustomerUID . '"', 'left');
	return $this->db->get()->result();
}


function saveSubProductTasks($PostArray,$SubProductUID,$CustomerUID){
	$res = 0;
	if(!empty($SubProductUID) && !empty($CustomerUID))
	{	

		foreach ($PostArray as $key => $value){
			$Farr = array(
				'SubProductUID'=>$SubProductUID,
				'CustomerUID'=>$CustomerUID,
				'TaskUID'=>$value['TaskUID'],
				'AutoCreation'=>!empty($value['auto_creation'])?1:0,
				'PreviousTaskUID'=>$value['previous_task'],
				'Action'=>$value['actiontype']
			);
				if(!empty($value['CustomerSubProductTaskUID'])){ // UPDATE METHOD
					if($value['Active'] == 1){
						$this->db->select('*');
						$this->db->from('tCustomerSubProductTasks');
						$this->db->where('CustomerSubProductTaskUID	',$value['CustomerSubProductTaskUID']);
						$PreviousOne = $this->db->get()->row();

						$this->db->where('CustomerSubProductTaskUID	',$value['CustomerSubProductTaskUID']);
						$this->db->update('tCustomerSubProductTasks',$Farr);
						$SubProducName = $this->common_model->getsubproductbyUID($SubProductUID)->SubProductName;
						if(!empty($value['TaskUID'])){
							$TaskShortDescription = $this->db->query("SELECT ShortDescription FROM mTask WHERE TaskUID = ".$value['TaskUID']."")->row()->ShortDescription;
						}else{
							$TaskShortDescription = '';
						}
						$msg = '<b>TaskAutoConfiguration</b> for SubProduct: <b>'.$SubProducName.'</b> with Task: <b>'.$TaskShortDescription.'</b> ';
						$Changed = false;
						if((!empty($PreviousOne->PreviousTaskUID) && !empty($value['previous_task']))
							&& ($PreviousOne->PreviousTaskUID != $value['previous_task'])){
							$PreviousTaskShortDescription1 = $this->db->query("SELECT ShortDescription FROM mTask WHERE TaskUID = ".$PreviousOne->PreviousTaskUID."")->row()->ShortDescription;
						$PreviousTaskShortDescription2 = $this->db->query("SELECT ShortDescription FROM mTask WHERE TaskUID = ".$value['previous_task']."")->row()->ShortDescription;
						$msg .= ' with PreviousTask: is changed from <b>'.$PreviousTaskShortDescription1.'</b> to <b>'.$PreviousTaskShortDescription2.'</b>';
						$Changed = true;
					}else if(empty($PreviousOne->PreviousTaskUID) && !empty($value['previous_task'])){
						$PreviousTaskShortDescription2 = $this->db->query("SELECT ShortDescription FROM mTask WHERE TaskUID = ".$value['previous_task']."")->row()->ShortDescription;
						$msg .= ' with PreviousTask: is changed from <b>NA</b> to <b>'.$PreviousTaskShortDescription2.'</b>';
						$Changed = true;
					}else if(!empty($PreviousOne->PreviousTaskUID) && empty($value['previous_task'])){
						$PreviousTaskShortDescription1 = $this->db->query("SELECT ShortDescription FROM mTask WHERE TaskUID = ".$PreviousOne->PreviousTaskUID."")->row()->ShortDescription;
						$msg .= ' with PreviousTask: is changed from <b>'.$PreviousTaskShortDescription1.'</b> to <b>NA</b>';
						$Changed = true;
					}else{
						$msg .= ' with PreviousTask: <b>'.$PreviousTaskShortDescription2.'</b>';
					}

					if(!empty($PreviousOne->AutoCreation) && empty($value['auto_creation'])){
						$msg .= ' with AutoCreation: changed from <b>Yes</b> to <b>No</b>';
						$Changed = true;
					}else if(empty($PreviousOne->AutoCreation) && !empty($value['auto_creation'])){
						$msg .= ' with AutoCreation: changed from <b>No</b> to <b>Yes</b>';
						$Changed = true;
					}else{
						$AutoC = !empty($value['auto_creation'])?'Yes':'No';
						$msg .= ' with AutoCreation: <b>'.$AutoC.'</b>';
					}

					if((!empty($PreviousOne->Action) && !empty($value['actiontype']))
						&& ($PreviousOne->Action != $value['actiontype'])){
						$msg .= ' with Action: is changed from <b>'.$PreviousOne->Action.'</b> to <b>'.$value['actiontype'].'</b>';
					$Changed = true;
				}else if(empty($PreviousOne->Action) && !empty($value['actiontype'])){
					$msg .= ' with Action: is changed from <b>NA</b> to <b>'.$value['actiontype'].'</b>';
					$Changed = true;
				}else if(!empty($PreviousOne->Action) && empty($value['actiontype'])){
					$msg .= ' with Action: is changed from <b>'.$PreviousOne->Action.'</b> to <b>NA</b>';
					$Changed = true;
				}else{
					$msg .= ' with Action: <b>'.$value['actiontype'].'</b>';
				}

				if($Changed == true){
					$InsetData = array(
						'UserUID' => $this->loggedid,
						'ModuleName' => 'CustomerTaskConf',
						'Feature' => $CustomerUID,
						'Content' => htmlentities($msg.' is Updated'),
						'DateTime' => date('Y-m-d H:i:s'));
					$this->common_model->InsertAuditTrail($InsetData);
				}
				$res++;
			}else{
				if(!empty($value['CustomerSubProductTaskUID'])){
					$this->db->where('CustomerSubProductTaskUID', $value['CustomerSubProductTaskUID']);
					$this->db->delete('tCustomerSubProductTasks');
					$res++;
				}
				$SubProducName = $this->common_model->getsubproductbyUID($SubProductUID)->SubProductName;
				if(!empty($value['TaskUID'])){
					$TaskShortDescription = $this->db->query("SELECT ShortDescription FROM mTask WHERE TaskUID = ".$value['TaskUID']."")->row()->ShortDescription;
				}else{
					$TaskShortDescription = '';
				}
				if(!empty($value['previous_task'])){
					$PreviousTaskShortDescription = $this->db->query("SELECT ShortDescription FROM mTask WHERE TaskUID = ".$value['previous_task']."")->row()->ShortDescription;
					$PreviousTaskShortDescription = ' with PreviousTask: <b>'.$TaskShortDescription.'</b>';
				}else{
					$PreviousTaskShortDescription = '';
				}
				$AutoC = !empty($value['auto_creation'])?'Yes':'No';
				$msg = '<b>TaskAutoConfiguration</b> for SubProduct: <b>'.$SubProducName.'</b> with Task: <b>'.$TaskShortDescription.'</b> AutoCreation: <b>'.$AutoC.'</b>'.$PreviousTaskShortDescription.' with Action: <b>'.$value['actiontype'].'</b> is <b>Removed</b>';
				$InsetData = array(
					'UserUID' => $this->loggedid,
					'ModuleName' => 'CustomerTaskConf',
					'Feature' => $CustomerUID,
					'Content' => htmlentities($msg),
					'DateTime' => date('Y-m-d H:i:s'));
				$this->common_model->InsertAuditTrail($InsetData);
			}
				}else{ // INSERT METHID
					if($value['Active'] == 1){
						if($this->db->insert('tCustomerSubProductTasks',$Farr)){
							$SubProducName = $this->common_model->getsubproductbyUID($SubProductUID)->SubProductName;
							if(!empty($value['TaskUID'])){
								$TaskShortDescription = $this->db->query("SELECT ShortDescription FROM mTask WHERE TaskUID = ".$value['TaskUID']."")->row()->ShortDescription;
							}else{
								$TaskShortDescription = '';
							}
							if(!empty($value['previous_task'])){
								$PreviousTaskShortDescription = $this->db->query("SELECT ShortDescription FROM mTask WHERE TaskUID = ".$value['previous_task']."")->row()->ShortDescription;
								$PreviousTaskShortDescription = ' with PreviousTask: '.$TaskShortDescription;
							}else{
								$PreviousTaskShortDescription = '';
							}
							$AutoC = !empty($value['auto_creation'])?'Yes':'No';
							$msg = '<b>TaskAutoConfiguration</b> for SubProduct: <b>'.$SubProducName.'</b> with Task: <b>'.$TaskShortDescription.'</b> AutoCreation: <b>'.$AutoC.'</b>'.$PreviousTaskShortDescription.' with Action: <b>'.$value['actiontype'].'</b> is <b>Created</b>';
							$InsetData = array(
								'UserUID' => $this->loggedid,
								'ModuleName' => 'CustomerTaskConf',
								'Feature' => $CustomerUID,
								'Content' => htmlentities($msg),
								'DateTime' => date('Y-m-d H:i:s'));
							$this->common_model->InsertAuditTrail($InsetData);
						}
						$res++;
					}
				}
			}

		}  
		return $res;
	}
	
	/******************************************** TASK MANAGEMENT END***************************************************/

	/**
		* @description Save Contact Data
		* @param Form Data
		* @throws Response
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return array 
		* @since  Apr 2 2020
		* @version  Client Advanced Setup CD-HUD and Packaging v1
		*/ 
		function saveContact($Post){
			
			
		// Insert Contact Profile Data
			$State = $this->common_model->get_row('mstates',array('StateName'=>$Post['ContactState']));
			$City = $this->common_model->get_row('mcities',array('CityName'=>$Post['ContactCity']));
			$FieldArray = [
				'CustomerUID'=>$Post['CustomerUID'],
				'ContactTypeUID'=>$Post['ContactTypeUID'],
				'DesignationUID'=>$Post['DesignationUID'],
				'FirstName'=>$Post['ContactFirstName'],
				'LastName'=>$Post['ContactLastName'],
				'JobTitle'=>$Post['ContactJobTitle'],
				'CompanyName'=>$Post['ContactCompanyName'],
				'NMLSID'=>$Post['NMLSID'],
				'StateLicenseID'=>$Post['ContactStateLicenseID'],
				'TimeZone'=>$Post['ContactTimeZone'],
				'IsPrimary'=>(isset($Post['ContactPrimaryCheck']))?1:0,
				'IsDifferentAddress'=>(isset($Post['ContcatAddressDifferent']))?1:0,
				'HasDelivery'=>(isset($Post['ContactDeliveryCheck']))?1:0,
				'AddressLine1'=>$Post['ContactAddressLine1'],
				'AddressLine2'=>$Post['ContactAddressLine2'],
				'ZipCode'=>$Post['ContactZipCode'],
				'StateUID'=>(!empty($State))?$State->StateUID:NULL,
				'CityUID'=>(!empty($City))?$City->CityUID:NULL,
				'IsActive'=>1,
				'UserUID'=>NULL
			];
			$this->db->insert('mClientContact',$FieldArray);
			$res = $this->db->insert_id();
		// Created Audit
			$this->common_model->InsertAuditTrail(
				array(
					'UserUID' => $this->loggedid,
					'ModuleName' => 'Customers',
					'Feature' => $Post['CustomerUID'],
					'Content' => htmlentities(' <b>Contact</b> <b>'.$Post['ContactFirstName'].'</b> Created'),
					'DateTime' => date('Y-m-d H:i:s')
				));


			$ContactMethods = $Post['ContactMethodName'];
		// Insert OR Update User
			$ContcatRequireLogin = (isset($Post['ContactRequireLogin']))?1:0;
			if($ContcatRequireLogin == 1){
				if(!empty($Post['ContactUserName']) && !empty($Post['ContactUserLoginID'])){
					$EncryptedPassword = md5($Post['ContactUserPassword']);
					if(!empty($Post['ContactUserUID'])){
					// Update User
						$UserfieldArray = array(
							'UserName'=>$Post['ContactUserName'],
							'Password'=>$EncryptedPassword,
							'LoginID'=>$Post['ContactUserLoginID'],
						);
						$this->db->where('UserUID',$Post['ContactUserUID']);
						$this->db->update('musers',$UserfieldArray);
						$this->common_model->InsertAuditTrail(
							array(
								'UserUID' => $this->loggedid,
								'ModuleName' => 'Customers',
								'Feature' => $Post['CustomerUID'],
								'Content' => htmlentities(' <b>Contact User</b> -- Updated'),
								'DateTime' => date('Y-m-d H:i:s')
							));
						if(!empty($Post['ContactUserUID'])){
							$userid = $Post['ContactUserUID'];
						}		
					}else{
					// Create User
						$UserfieldArray = array(
							'UserName'=>$Post['ContactUserName'],
							'Password'=>$EncryptedPassword,
							'LoginID'=>$Post['ContactUserLoginID'],
							'CustomerUID'=>$Post['CustomerUID'],
							'Active'=>1
						);
						$this->db->insert('musers',$UserfieldArray);
						$userid = $this->db->insert_id();
						$this->common_model->InsertAuditTrail(
							array(
								'UserUID' => $this->loggedid,
								'ModuleName' => 'Customers',
								'Feature' => $Post['CustomerUID'],
								'Content' => htmlentities(' <b>Contact User</b> <b>'.$Post['ContactUserName'].'</b> Created'),
								'DateTime' => date('Y-m-d H:i:s')
							));
					}
					$this->db->where('ClientContactUID',$res);
					$this->db->update('mClientContact',array('UserUID'=>$userid));
				}
			}else{
			// Deactivate User
				if(!empty($Post['ContactUserUID'])){
					$this->db->where('UserUID',$Post['ContactUserUID']);
					$this->db->update('musers',array('Active'=>0));
					$this->common_model->InsertAuditTrail(
						array(
							'UserUID' => $this->loggedid,
							'ModuleName' => 'Customers',
							'Feature' => $Post['CustomerUID'],
							'Content' => htmlentities('Contact <b>User</b> <b>'.$Post['ContactUserName'].'</b> Deactivated'),
							'DateTime' => date('Y-m-d H:i:s')
						));
				}
			}

		// Insert Contact Methods
			if(!empty($ContactMethods)){
				foreach ($ContactMethods as $key => $value) {
					$FieldArr = [
						'CustomerUID'=>$Post['CustomerUID'],
						'ClientContactUID'=>$res,
						'ContactMethodName'=> $Post['ContactMethodName'][$key],
						'ContactMethodUID'=> $Post['ContactMethodUID'][$key],
						'Details'=> $Post['contactMethodDetail'][$key],
						'Ext'=> (isset($Post['contactMethodDetailExt'][$key]))?$Post['contactMethodDetailExt'][$key]:NULL
					];
					$this->db->insert('mClientContactMethod',$FieldArr);
					$this->common_model->InsertAuditTrail(
						array(
							'UserUID' => $this->loggedid,
							'ModuleName' => 'Customers',
							'Feature' => $Post['CustomerUID'],
							'Content' => htmlentities('<b>ContactMethod</b> <b>'.$Post['ContactMethodName'][$key].' -- '.$Post['contactMethodDetail'][$key].'</b> Added'),
							'DateTime' => date('Y-m-d H:i:s')
						));
				}
			}

		// Insert Contact Delivery
			$ContactDeliveryMethods = $Post['ContactMethod'];
			if(!empty($ContactDeliveryMethods)){
				foreach ($ContactDeliveryMethods as $key => $value) {
					if(!empty($Post['ContactMethod'][$key]) && !empty($Post['DocumentTypeUID'][$key]) && !empty($Post['SecondaryContactMethod'][$key])){
						$FieldArr = [
							'ClientContactUID'=>$res,
							'PrimaryMethodUID'=> $Post['ContactMethod'][$key],
							'DocumentTypeUID'=> $Post['ContactMethodUID'][$key],
							'SecondaryMethodUID'=> $Post['SecondaryContactMethod'][$key],
						];
						$this->db->insert('mClientContactDelivery',$FieldArr);
						$this->common_model->InsertAuditTrail(
							array(
								'UserUID' => $this->loggedid,
								'ModuleName' => 'Customers',
								'Feature' => $Post['CustomerUID'],
								'Content' => htmlentities('ContactDelivery '.$Post['ContactMethodName'][$key].' Added'),
								'DateTime' => date('Y-m-d H:i:s')
							));
					}
				}
			}

			return $res;
		}


	/**
		* @description Update Contact Data
		* @param Form Data
		* @throws Response
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return array 
		* @since  Apr 2 2020
		* @version  Client Advanced Setup CD-HUD and Packaging v1
		*/ 
		function updateContact($Post){

			$TabType = $this->input->post('TabType');
			if($TabType == 'ContactDetails'){
			// Previous Data
				$this->db->where('ClientContactUID',$Post['ClientContactUID']);
				$PrevoiusContact = $this->db->get('mClientContact')->row();
			// Update Contact Profile Data
				$State = $this->common_model->get_row('mstates',array('StateName'=>$Post['ContactState']));
				$City = $this->common_model->get_row('mcities',array('CityName'=>$Post['ContactCity']));
				$FieldArray = [
					'CustomerUID'=>$Post['CustomerUID'],
					'ContactTypeUID'=>$Post['ContactTypeUID'],
					'DesignationUID'=>$Post['DesignationUID'],
					'FirstName'=>$Post['ContactFirstName'],
					'LastName'=>$Post['ContactLastName'],
					'JobTitle'=>$Post['ContactJobTitle'],
					'CompanyName'=>$Post['ContactCompanyName'],
					'NMLSID'=>$Post['NMLSID'],
					'StateLicenseID'=>$Post['ContactStateLicenseID'],
					'TimeZone'=>$Post['ContactTimeZone'],
					'IsPrimary'=>(isset($Post['ContactPrimaryCheck']))?1:0,
					'IsDifferentAddress'=>(isset($Post['ContcatAddressDifferent']))?1:0,
					'HasDelivery'=>(isset($Post['ContactDeliveryCheck']))?1:0,
					'AddressLine1'=>$Post['ContactAddressLine1'],
					'AddressLine2'=>$Post['ContactAddressLine2'],
					'ZipCode'=>$Post['ContactZipCode'],
					'StateUID'=>(!empty($State))?$State->StateUID:NULL,
					'CityUID'=>(!empty($City))?$City->CityUID:NULL,
					'IsActive'=>(isset($Post['ContcatIsActive']))?1:0
				];
				$this->db->where('ClientContactUID',$Post['ClientContactUID']);
				$res = $this->db->update('mClientContact',$FieldArray);
				$AuditStrs = [];
				$this->common_model->InsertAuditTrail(
					array(
						'UserUID' => $this->loggedid,
						'ModuleName' => 'Customers',
						'Feature' => $Post['CustomerUID'],
						'Content' => htmlentities('Contact <b>User</b> <b>'.(!isset($Post['ContactFirstName']))?$Post['ContactFirstName']:''.'</b> Updated'),
						'DateTime' => date('Y-m-d H:i:s')
					));
			// Insert OR Update User
				$ContcatRequireLogin = (isset($Post['ContactRequireLogin']))?1:0;
				if($ContcatRequireLogin == 1 && !empty($Post['ContactUserName']) && !empty($Post['ContactUserLoginID'])){
					$EncryptedPassword = md5($Post['ContactUserPassword']);
					if(!empty($Post['ContactUserUID'])){
					// Update User
						$UserfieldArray = array(
							'UserName'=>$Post['ContactUserName'],
							'Password'=>$EncryptedPassword,
							'LoginID'=>$Post['ContactUserLoginID'],
						);
						$this->db->where('UserUID',$Post['ContactUserUID']);
						$this->db->update('musers',$UserfieldArray);
						$this->common_model->InsertAuditTrail(
							array(
								'UserUID' => $this->loggedid,
								'ModuleName' => 'Customers',
								'Feature' => $Post['CustomerUID'],
								'Content' => htmlentities('Contact <b>User</b> Updated'),
								'DateTime' => date('Y-m-d H:i:s')
							));
						if(!empty($Post['ContactUserUID'])){
							$userid = $Post['ContactUserUID'];
						}		
					}else{
					// Create User
						$UserfieldArray = array(
							'UserName'=>$Post['ContactUserName'],
							'Password'=>$EncryptedPassword,
							'LoginID'=>$Post['ContactUserLoginID'],
							'CustomerUID'=>$Post['CustomerUID'],
							'Active'=>1
						);
						$this->db->insert('musers',$UserfieldArray);
						$userid = $this->db->insert_id();
						$this->common_model->InsertAuditTrail(
							array(
								'UserUID' => $this->loggedid,
								'ModuleName' => 'Customers',
								'Feature' => $Post['CustomerUID'],
								'Content' => htmlentities('Contact <b>User</b> <b>'.$Post['ContactUserName'].'</b> Deactivated'),
								'DateTime' => date('Y-m-d H:i:s')
							));
					}
					$this->db->where('ClientContactUID',$Post['ClientContactUID']);
					$this->db->update('mClientContact',array('UserUID'=>$userid));

				}else{
				// Deactivate User
					if(!empty($Post['ContactUserUID'])){
						$this->db->where('UserUID',$Post['ContactUserUID']);
						$this->db->update('musers',array('Active'=>0));
						$this->common_model->InsertAuditTrail(
							array(
								'UserUID' => $this->loggedid,
								'ModuleName' => 'Customers',
								'Feature' => $Post['CustomerUID'],
								'Content' => htmlentities('Contact <b>User</b> <b>'.$Post['ContactUserName'].'</b> Deactivated'),
								'DateTime' => date('Y-m-d H:i:s')
							));
					}
				}


			}else if($TabType == 'ContactMethods'){
			// Update Contact Methods
				$ContactMethods = $Post['ContactMethodName'];
				if(!empty($ContactMethods)){
					foreach ($ContactMethods as $key => $value) {
						$FieldArr = [
							'CustomerUID'=>$Post['CustomerUID'],
							'ClientContactUID'=>$Post['ClientContactUID'],
							'ContactMethodName'=> $Post['ContactMethodName'][$key],
							'ContactMethodUID'=> $Post['ContactMethodUID'][$key],
							'Details'=> $Post['contactMethodDetail'][$key],
							'Ext'=> (isset($Post['contactMethodDetailExt'][$key]))?$Post['contactMethodDetailExt'][$key]:NULL
						];
						if(!empty($Post['ClientContactMethodUID'][$key])){
							$PreviousData = $this->common_model->get_row('mClientContactMethod',array('ClientContactMethodUID'=>$Post['ClientContactMethodUID'][$key]));
							$this->db->where('ClientContactMethodUID',$Post['ClientContactMethodUID'][$key]);
							$res = $this->db->update('mClientContactMethod',$FieldArr);
						// Contact method Audit UPDATE
							if($PreviousData->ContactMethodName != $Post['ContactMethodName'][$key]){
								$this->common_model->InsertAuditTrail(array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $Post['CustomerUID'],'Content' => htmlentities('ContactMethod Name Changed '.$PreviousData->ContactMethodName.' -> '.$Post['ContactMethodName'][$key].''),'DateTime' => date('Y-m-d H:i:s')));
							}else if($PreviousData->Details != $Post['contactMethodDetail'][$key]){
								$this->common_model->InsertAuditTrail(array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $Post['CustomerUID'],'Content' => htmlentities('ContactMethod Detail Changed '.$PreviousData->Details.' -> '.$Post['contactMethodDetail'][$key].''),'DateTime' => date('Y-m-d H:i:s')));
							}else if(!empty($PreviousData->ContactMethodUID) && !empty($Post['ContactMethodUID'][$key]) && $PreviousData->ContactMethodUID != $Post['ContactMethodUID'][$key]){
								$First = $this->common_model->get_row('mContactMethod',array('ContactMethodUID'=>$PreviousData->ContactMethodUID));
								$Second = $this->common_model->get_row('mContactMethod',array('ContactMethodUID'=>$Post['ContactMethodUID'][$key]));
								$this->common_model->InsertAuditTrail(array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $Post['CustomerUID'],'Content' => htmlentities('ContactMethod Type Changed '.$First->ContactMethodName.' -> '.$Second->ContactMethodName.''),'DateTime' => date('Y-m-d H:i:s')));
							}
						}else{
							$res = $this->db->insert('mClientContactMethod',$FieldArr);
						// Contact method Audit INSERT
							$this->common_model->InsertAuditTrail(
								array(
									'UserUID' => $this->loggedid,
									'ModuleName' => 'Customers',
									'Feature' => $Post['CustomerUID'],
									'Content' => htmlentities('<b>ContactMethod</b> <b>'.$Post['ContactMethodName'][$key].' -- '.$Post['contactMethodDetail'][$key].'</b> Added'),
									'DateTime' => date('Y-m-d H:i:s')
								));
						}
					}
				}
			}else if($TabType == 'ContactDeliveries'){
			// Update Contact Delivery
				$ContactDeliveryMethods = $Post['ContactMethod'];
				if(!empty($ContactDeliveryMethods)){
					foreach ($ContactDeliveryMethods as $key => $value) {
						if(!empty($Post['ContactMethod'][$key]) && !empty($Post['DocumentTypeUID'][$key]) && !empty($Post['SecondaryContactMethod'][$key])){
							$FieldArr = [
								'ClientContactUID'=>$Post['ClientContactUID'][$key],
								'PrimaryMethodUID'=> $Post['ContactMethod'][$key],
								'DocumentTypeUID'=> $Post['DocumentTypeUID'][$key],
								'SecondaryMethodUID'=> $Post['SecondaryContactMethod'][$key]
							];
							if($Post['ClientContactDeliveryUID'][$key]){
								$this->db->where('ClientContactDeliveryUID',$Post['ClientContactDeliveryUID'][$key]);
								$previous = $this->db->get('mClientContactDelivery')->row();
								$this->db->where('ClientContactDeliveryUID',$Post['ClientContactDeliveryUID'][$key]);
								$res = $this->db->update('mClientContactDelivery',$FieldArr);
							// Contact delivery Audit UPDATE
							// PrimaryMethodUID
								if($previous->PrimaryMethodUID != $Post['ContactMethod'][$key]){
									$Pm = 'NA'; 
									if(!empty($previous->PrimaryMethodUID)){
										$Pm = $this->common_model->get_row('mContactMethod',array('ContactMethodUID'=>$previous->PrimaryMethodUID))->ContactMethodName;
									}
									$Cm = 'NA'; 
									if(!empty($Post['ContactMethod'][$key])){
										$Cm = $this->common_model->get_row('mContactMethod',array('ContactMethodUID'=>$Post['ContactMethod'][$key]))->ContactMethodName;
									}
									$this->common_model->InsertAuditTrail(array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $Post['CustomerUID'],'Content' => htmlentities('ContactDelivery PrimaryMethod Changed '.$Pm.' -> '.$Cm.''),'DateTime' => date('Y-m-d H:i:s')));
								}
							// CecondartyMethodUID
								if($previous->SecondaryMethodUID != $Post['SecondaryContactMethod'][$key]){
									$Pm = 'NA'; 
									if(!empty($previous->SecondaryMethodUID)){
										$Pm = $this->common_model->get_row('mContactMethod',array('ContactMethodUID'=>$previous->SecondaryMethodUID))->ContactMethodName;
									}
									$Cm = 'NA'; 
									if(!empty($Post['ContactMethod'][$key])){
										$Cm = $this->common_model->get_row('mContactMethod',array('ContactMethodUID'=>$Post['SecondaryContactMethod'][$key]))->ContactMethodName;
									}
									$this->common_model->InsertAuditTrail(array('UserUID' => $this->loggedid,'ModuleName' => 'Customers','Feature' => $Post['CustomerUID'],'Content' => htmlentities('ContactDelivery SecondaryMethod Changed '.$Pm.' -> '.$Cm.''),'DateTime' => date('Y-m-d H:i:s')));
								}

							}else{
								$res = $this->db->insert('mClientContactDelivery',$FieldArr);
								$Primary = $this->common_model->GetContactMethod($Post['ContactMethod'][$key]);
								$Secondary = $this->common_model->GetContactMethod($Post['SecondaryContactMethod'][$key]);
								if(!empty($Primary) && !empty($Secondary)){
								// Contact delivery Audit INSERT
									$this->common_model->InsertAuditTrail(
										array(
											'UserUID' => $this->loggedid,
											'ModuleName' => 'Customers',
											'Feature' => $Post['CustomerUID'],
											'Content' => htmlentities('ContactDelivery - PrimaryMethod: '.(!empty($Primary))?$Primary->ContactMethodName:'NA'.' - SecondaryMethod: '.(!empty($Secondary))?$Secondary->ContactMethodName:'NA'.' Added'),
											'DateTime' => date('Y-m-d H:i:s')
										));
								}
							}
						}
					}
				}
			}

			return $res;

		}

	/**
		* @description Getting Contact List
		* @param CustomerUID
		* @throws 
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return array 
		* @since  Apr 2 2020
		* @version  Client Advanced Setup CD-HUD and Packaging v1
		*/ 
		function customercontact_list($post){
			$CustomerUID = $post['CustomerUID'];
			$this->db->select("mcustomers.CustomerUID,mClientContact.*, mDesignation.DesignationName,mContactType.ContactTypeName
				");
		$this->db->from('mcustomers'); // 1) abstractor - USERUID 2) abtractorUID in musers 3) abstractorcontact UserUID - musers 
		$this->db->join('mClientContact','mClientContact.CustomerUID = mcustomers.CustomerUID','inner');	
		$this->db->join('musers','musers.UserUID = mClientContact.UserUID','left');
		$this->db->join('mDesignation','mDesignation.DesignationUID = mClientContact.DesignationUID','left');
		$this->db->join('mContactType','mContactType.ContactTypeUID = mClientContact.ContactTypeUID','left');
		$this->db->where(array("mcustomers.CustomerUID"=>$CustomerUID));  
		// $this->db->group_by("mabstractorcontact.UserUID");
		$query = $this->db->get();
		$ContactsArr = $query->result();
		$ContactsColumn = array_column($query->result(),'UserUID');

		$this->db->select("musers.UserUID as UserUID,musers.LoginID,musers.UserName");
		$this->db->from('musers');  
		$this->db->where(array("musers.CustomerUID"=>$CustomerUID,'musers.Active'=>1));
		if(!empty(array_filter($ContactsColumn))){
			$this->db->where_not_in('musers.UserUID',$ContactsColumn);  
		} 
		$query = $this->db->get();

		$UsersArr = $query->result();
		$Datas = array_merge($ContactsArr,$UsersArr);

		return $Datas;

	}

	/**
		* @description Getting Contact Count
		* @param CustomerUID
		* @throws 
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return array 
		* @since  Apr 2 2020
		* @version  Client Advanced Setup CD-HUD and Packaging v1
		*/ 
		function customercontact_count($post){
			$CustomerUID = $post['CustomerUID'];
			$this->db->select('mClientContact.*,mDesignation.DesignationName');
			$this->db->from('mClientContact');
			$this->db->join('mDesignation','mDesignation.DesignationUID = mClientContact.DesignationUID','left');
			$this->db->where('CustomerUID',$CustomerUID);
			return $this->db->get()->num_rows();
		}

	/**
		* @description Getting Contact Single
		* @param CustomerUID
		* @throws 
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return array 
		* @since  Apr 2 2020
		* @version  Client Advanced Setup CD-HUD and Packaging v1
		*/ 
		function get_client_contact($Post){
			$ClientContactUID = $Post['ClientContactUID'];
			$UserUID = $Post['UserUID'];
			$CustomerUID = $Post['CustomerUID'];

			$this->db->select('mClientContact.*,mstates.StateName,mcities.CityName,musers.LoginID,musers.UserName');
			$this->db->from('mClientContact');
			$this->db->where('ClientContactUID',$ClientContactUID);
			$this->db->join('musers','musers.UserUID = mClientContact.UserUID','left');
			$this->db->join('mstates','mstates.StateUID = mClientContact.StateUID','left');
			$this->db->join('mcities','mcities.CityUID = mClientContact.CityUID','left');
			$ContactDetail = $this->db->get()->row_array();

			$this->db->select("musers.UserUID,musers.LoginID,musers.UserName");
			$this->db->from('musers');
			$this->db->join('mcustomers','mcustomers.CustomerUID = musers.CustomerUID','left');	    
			$this->db->where(array("musers.UserUID"=>$UserUID,'musers.Active'=>1));
			$query = $this->db->get();

			$UsersArr = $query->row_array();
			$Datas = array_merge( 
				$ContactDetail, $UsersArr
			); 
			if(!empty($ClientContactUID)){
				$this->db->select('*');
				$this->db->from('mClientContactMethod');
				$this->db->where('ClientContactUID',$ClientContactUID);
				$ContactMethods = $this->db->get()->result();

				$this->db->select('mClientContactDelivery.*,Meth1.ContactMethodName as PrimaryMethodName,Meth2.ContactMethodName as SecondaryMethodName');
				$this->db->from('mClientContactDelivery');
				$this->db->join('mClientContactMethod Meth1','mClientContactDelivery.PrimaryMethodUID = Meth1.ClientContactMethodUID AND mClientContactDelivery.ClientContactUID = Meth1.ClientContactUID','left');
				$this->db->join('mClientContactMethod Meth2','mClientContactDelivery.SecondaryMethodUID = Meth2.ClientContactMethodUID AND mClientContactDelivery.ClientContactUID = Meth2.ClientContactUID','left');
				$this->db->where('mClientContactDelivery.ClientContactUID',$ClientContactUID);
				$ContactDeliveries = $this->db->get()->result();
			}else{
				$ContactMethods = [];
				$ContactDeliveries = [];
			}

			return array(
				'ContactDetail'=>$Datas,
				'ContactMethods'=>$ContactMethods,
				'ContactDeliveries'=>$ContactDeliveries
			);
		}
	}
	?>
