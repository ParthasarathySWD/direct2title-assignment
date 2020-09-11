<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vendor_pricing {
	
	function __construct()
	{
		$this->CI =& get_instance();

	}

	function get_Vendor_Pricings($OrderUID,$WorkflowModuleUID,$TaxPricingStatus)
	{
			$Workflow = '';
			foreach ($WorkflowModuleUID as $key => $value) {
				$Workflow .= $value;
			}
			
		  $this->CI->db->select ("*");
		  $this->CI->db->from ( 'torderassignment');
		  $this->CI->db->join('torders','torders.OrderUID = torderassignment.OrderUID','left');
		  $this->CI->db->where(array("torderassignment.OrderUID"=>$OrderUID,"torderassignment.WorkflowModuleUID"=>$Workflow)); 
	   	  $query = $this->CI->db->get();
		  $Order =  $query->row();
		  $vendor_pricingUID = $this->help_get_Vendor_Pricing($Order->VendorUID);
		  $datass = new stdclass();
		  $datass->PricingUID = $vendor_pricingUID;
		  $datass->SubProductUID = $Order->SubProductUID; 
		  $datass->PropertyCountyName = $Order->PropertyCountyName;
		  $datass->PropertyStateCode = $Order->PropertyStateCode; 
		  $pricing = $this->help_checkproductpricings($datass);  
		  if(count($pricing) > 0)
		  {
		  	if($TaxPricingStatus == 'WebTaxPricing')
		  	{
			  return $pricing['row']->WebTaxPricing;
		  	}
		  	elseif($TaxPricingStatus == 'CallTaxPricing')
		  	{
		  	  return $pricing['row']->CallTaxPricing;
		  	}
		  	elseif($TaxPricingStatus == 'SearchPricing')
		  	{
		  		return $pricing['row']->SearchPricing;
		  	}
		  	elseif($TaxPricingStatus == 'TypingPricing')
		  	{
		  		return $pricing['row']->TypingPricing;
		  	}
		  	elseif($TaxPricingStatus == 'ReviewPricing')
		  	{
		  		return $pricing['row']->ReviewPricing;
		  	}
		  }
		 return false;
	}

	function help_get_Vendor_Pricing($VendorUID)
	{
		$db_query = $this->CI->db->query("SELECT PricingUID FROM mvendors WHERE VendorUID ='$VendorUID'"); 
		$result = $db_query->row();
		return $result->PricingUID;
	}


	function help_checkproductpricings($data)
	{

		$returndata = [];
		$returndata['message'] = '';
		$returndata['error'] = 0;
		$returndata['row'] = [];
 	
 		if($data->PropertyCountyName!='' || $data->PropertyStateCode!='')
 		{
		  $StateUID = $this->GetStateUID_byStateName($data->PropertyStateCode);
		  $CountyUID = $this->GetCountyUID_byCountyName($StateUID->StateUID,$data->PropertyCountyName);
	 	  $PropertyStateUID = $StateUID->StateUID;
		  $PropertyCountyUID = $CountyUID->CountyUID;
		} else {
		  $PropertyStateUID = '';
		  $PropertyCountyUID = '';
		}

		// 1st - check for matching state, county and sub product
		$statecountyproductexists = $this->help_if_statecountyproductexists($data,$PropertyStateUID,$PropertyCountyUID);
		if(count($statecountyproductexists) > 0)
		{
 			$returndata['message'] = 'Pricing For this State County Product already Exists';
	  		$returndata['error'] = 1;
			$returndata['row'] = $statecountyproductexists;
			return $returndata; 
		} else { // 2nd - check for matching state and sub product
		    $stateproductexists = $this->help_if_stateproductexists($data,$PropertyStateUID);
			if(count($stateproductexists) > 0)
			{ 
			  $returndata['message'] = 'Pricing For this State Product already Exists';
			  $returndata['error'] = 1;
			  $returndata['row'] = $stateproductexists;
			  return $returndata;  
			} else {
		    // 3rd - national level and sub product check for national pricings
		    $national_exists = $this->help_check_national($data);
		    if(count($national_exists) > 0)
		    {
  			  $returndata['message'] = 'National Pricing already Exists';
			  $returndata['error'] = 1;
			  $returndata['row'] = $national_exists;
			  return $returndata; 
		    }
		  }
		} 

	}

	function GetStateUID_byStateName($StateCode)
	{
	  $this->CI->db->select ( 'StateUID' );
	  $this->CI->db->from ( 'mstates');
	  $this->CI->db->where( 'StateCode',$StateCode); 
   	  $query = $this->CI->db->get();
	  return $query->row();
	}

	function GetCountyUID_byCountyName($StateUID,$CountyName)
	{
	  $this->CI->db->select ( 'CountyUID' );
	  $this->CI->db->from ( 'mcounties');
	  $this->CI->db->where( 'StateUID',$StateUID); 
	  $this->CI->db->where( 'CountyName',$CountyName); 
   	  $query = $this->CI->db->get();
	  return $query->row();
	}

	// 1st - check for matching state, county and sub product
	function help_if_statecountyproductexists($data,$PropertyStateUID,$PropertyCountyUID)
	{
	  $this->CI->db->select ( '*' );
	  $this->CI->db->from ( 'mpricingproducts');
	  $this->CI->db->where( 'mpricingproducts.StateUID',$PropertyStateUID);
	  $this->CI->db->where( 'mpricingproducts.CountyUID',$PropertyCountyUID);
	  $this->CI->db->where( 'mpricingproducts.SubProductUID',$data->SubProductUID);
	  $this->CI->db->where('PricingUID',$data->PricingUID);

   	  $query = $this->CI->db->get();
	  return $query->row();
	}
	// 2nd - check for matching state and sub product
	function help_if_stateproductexists($data,$PropertyStateUID)
	{
	   $this->CI->db->select ( '*' );
	   $this->CI->db->from ( 'mpricingproducts');
	   $this->CI->db->where( 'mpricingproducts.StateUID',$PropertyStateUID);
	   $this->CI->db->where('CountyUID',0);
	   $this->CI->db->where( 'mpricingproducts.SubProductUID',$data->SubProductUID);
	   $this->CI->db->where('PricingUID',$data->PricingUID);

	   $query = $this->CI->db->get();
	   return $query->row();
	}
	// 3rd - national level and sub product
	function help_check_national($data)
	{
		$this->CI->db->select ( '*' );
		$this->CI->db->from ( 'mpricingproducts');
		$this->CI->db->where('CountyUID',0);
		$this->CI->db->where('StateUID',0);
		$this->CI->db->where('mpricingproducts.SubProductUID', $data->SubProductUID);
		$this->CI->db->where('PricingUID',$data->PricingUID);

		$query = $this->CI->db->get();
		return $query->row();
	}
	
	/*function help_if_statecountyexists($data)
	{
	  $this->CI->db->select ( '*' );  
	  $this->CI->db->from ( 'mpricingproducts');
	  $this->CI->db->where( 'mpricingproducts.StateUID',$data->PropertyStateUID);
	  $this->CI->db->where( 'mpricingproducts.CountyUID',$data->PropertyCountyUID);
	  $this->CI->db->where('SubProductUID IS NULL', null, false);
	  $this->CI->db->where('PricingUID',$data->PricingUID);
  	  $query = $this->CI->db->get();
	  return $query->row();
	}*/


	/*function help_if_stateexists($data)
	{
	   $this->CI->db->select ( '*' );
	   $this->CI->db->from ( 'mpricingproducts');
	   $this->CI->db->where ( 'mpricingproducts.StateUID',$data->PropertyStateUID);
	   $this->CI->db->where('CountyUID IS NULL', null, false);
	   $this->CI->db->where('SubProductUID IS NULL', null, false);
	   $this->CI->db->where('PricingUID',$data->PricingUID);
	   $query = $this->CI->db->get();
	   return $query->row();
	}*/


}
?>