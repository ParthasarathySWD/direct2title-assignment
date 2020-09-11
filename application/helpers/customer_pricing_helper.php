<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer_pricing {
	
	function __construct()
	{
		$this->CI =& get_instance();

	}

	function get_Customer_Pricings($Order)
	{	 
		$customer_pricingUID = $this->help_get_Customer_Pricing($Order->CustomerUID);
		$datass = new stdclass();
		$datass->PricingUID = $customer_pricingUID;
		$datass->SubProductUID = $Order->SubProductUID; 
		$datass->PropertyCountyName = $Order->PropertyCountyName;
		$datass->PropertyStateCode = $Order->PropertyStateCode; 
		$pricing = $this->help_checkproductpricings($datass);  
		if(!empty($pricing)){
		  return $pricing['row']->Pricing;
		}
		return false;
	}
/*@Desc return Customer pricing row to get IsQuote @Author Jainulabdeen @Since May 20 2020*/
	function get_Customer_Pricings_Quote($Order, $check = '')
	{	
		if($check == 1){
		$customer_pricingUID = $this->help_get_Customer_Pricing($Order['customer']);
		$datass = new stdclass();
		$datass->PricingUID = $customer_pricingUID;
		$datass->PropertyCountyName = $Order['PropertyCountyName'];
		$datass->PropertyStateCode = $Order['PropertyStateCode']; 
		foreach ($Order['SubProductUID'] as $key => $value) {
			$datass->SubProductUID = $value; 
			$pricing[] = $this->help_checkproductpricings($datass)['row'];
		}
		if(!empty($pricing)){
		  return $pricing;
		}
		return false;
		
		}  else {
			$customer_pricingUID = $this->help_get_Customer_Pricing($Order->CustomerUID);
		$datass = new stdclass();
		$datass->PricingUID = $customer_pricingUID;
		$datass->SubProductUID = $Order->SubProductUID; 
		$datass->PropertyCountyName = $Order->PropertyCountyName;
		$datass->PropertyStateCode = $Order->PropertyStateCode; 
		$pricing = $this->help_checkproductpricings($datass);
		if(!empty($pricing)){
		  return $pricing['row'];
		}
		return false;
		}
		
	}

	function help_get_Customer_Pricing($CustomerUID)
	{
		$db_query = $this->CI->db->query("SELECT PricingUID FROM mcustomers WHERE CustomerUID ='$CustomerUID'"); 
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
	   $this->CI->db->where('CountyUID IS NULL');
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
		$this->CI->db->where('CountyUID IS NULL');
		$this->CI->db->where('StateUID IS NULL');
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

	/* D-2-T18 FOR REVERSE MORTGAGE PRICING*/

	function get_Customer_ReverseMortgagePricing($CustomerUID,$SubProductUID)
	{	 
		$CustomerProducts = $this->CI->db->select('mcustomers.PricingUID,mcustomerproducts.GradingType')->join ( 'mcustomers', 'mcustomerproducts.CustomerUID = mcustomers.CustomerUID')->where(array('mcustomerproducts.CustomerUID'=>$CustomerUID,'mcustomerproducts.SubProductUID'=>$SubProductUID))->get('mcustomerproducts')->row();
		if(!empty($CustomerProducts)) {
			if($CustomerProducts->GradingType == 'Yes') {

				$this->CI->db->select ( 'Pricing' );
				$this->CI->db->from ( 'mpricingproducts');
				$this->CI->db->join ( 'mGrading', 'mGrading.GradingUID = mpricingproducts.GradingUID');
				$this->CI->db->where('PricingUID',$CustomerProducts->PricingUID);
				$this->CI->db->where('GradingName','Review');
				$query = $this->CI->db->get();
				$pricingrow =  $query->row();
				if(!empty($pricingrow)) {
					return $pricingrow->Pricing;
				}
			}
		}
		return ZERO;
	}

	/*function get_Customer_ReverseMortgagePricing($CustomerUID)
	{	 
		$this->CI->db->select ( 'DefaultGradingAmount' );
		$this->CI->db->from ( 'mGrading');
		$this->CI->db->where('GradingName','Review');
		$query = $this->CI->db->get();
		$pricingrow =  $query->row();
		if(!empty($pricingrow)) {
			return $pricingrow->DefaultGradingAmount;
		}
		
		return ZERO;
	}*/

/**
	*@description Customer Pricing Info
	*
	* @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
	* @since July 27 2020
	* @version Order Entry
	*
*/
	function GetCustomerPricing($Order){

		$customer_pricingUID = $this->help_get_Customer_Pricing($Order['customer']);
		$datass = new stdclass();
		$datass->PricingUID = $customer_pricingUID;
		$datass->PropertyCountyName = $Order['PropertyCountyName'];
		$datass->PropertyStateCode = $Order['PropertyStateCode']; 
		foreach ($Order['SubProductUID'] as $key => $value) {
			$datass->SubProductUID = $value; 
			$pricing[] = $this->help_checkproductpricings($datass)['row']->Pricing;
		}
		if(!empty($pricing)){
		  return $pricing;
		}
		return false;
	}
	/*End*/

}
?>
