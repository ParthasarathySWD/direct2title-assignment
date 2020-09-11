<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Check_address_variance_model extends CI_Model {

	
	function __construct()
	{ 
		parent::__construct();
	}

	function GetZipcodeSection2($P_Zipcode,$A_Zipcode,$U_Zipcode){

		if($P_Zipcode == $A_Zipcode && $A_Zipcode == $U_Zipcode && $U_Zipcode == $P_Zipcode)
			{
		        $PropertyOutputZipcode1 ='<span>'.$P_Zipcode.'</span>';
		        $AssessedOutputZipcode1 ='<span>'.$A_Zipcode.'</span>';
		        $USPSOutputZipcode1 ='<span>'.$U_Zipcode.'</span>';
			}
			elseif($P_Zipcode != $A_Zipcode && $A_Zipcode != $U_Zipcode && $U_Zipcode != $P_Zipcode)
			{		     
		        $PropertyOutputZipcode1 ='<span style="color:#000;">'.$P_Zipcode.'</span>';
		        $AssessedOutputZipcode1 ='<span style="color:#000;">'.$A_Zipcode.'</span>';
		        $USPSOutputZipcode1 ='<span style="color:#000;">'.$U_Zipcode.'</span>';

			}
			else
			{ 
		        if($P_Zipcode == $A_Zipcode)
		        {
		            if($P_Zipcode != $U_Zipcode && $A_Zipcode != $U_Zipcode)
		            {
		                $PropertyOutputZipcode1 ='<span>'.$P_Zipcode.'</span>';
		                $AssessedOutputZipcode1 ='<span>'.$A_Zipcode.'</span>';
		                $USPSOutputZipcode1 ='<span style="color:#000;">'.$U_Zipcode.'</span>';
		            }

		        }
		        if($P_Zipcode == $U_Zipcode)
		        {
		            if($P_Zipcode != $A_Zipcode && $U_Zipcode != $A_Zipcode)
		            {
		                $PropertyOutputZipcode1 ='<span>'.$P_Zipcode.'</span>';
		                $AssessedOutputZipcode1 ='<span style="color:#000;">'.$A_Zipcode.'</span>';
		                $USPSOutputZipcode1 ='<span>'.$U_Zipcode.'</span>';				        
		            }
		        }
		        if($A_Zipcode == $U_Zipcode)
		        {
		            if($P_Zipcode != $A_Zipcode && $U_Zipcode != $P_Zipcode)
		            {
		                $PropertyOutputZipcode1 ='<span style="color:#000;">'.$P_Zipcode.'</span>';
		                $AssessedOutputZipcode1 ='<span>'.$A_Zipcode.'</span>';
		                $USPSOutputZipcode1 ='<span>'.$U_Zipcode.'</span>';
		            }
		        }   
			}

			$result = array('PropertyOutputZipcode1'=>$PropertyOutputZipcode1,'AssessedOutputZipcode1'=>$AssessedOutputZipcode1,'USPSOutputZipcode1'=>$USPSOutputZipcode1);

			return $result;
	}

	function GetZipcodeVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code){


		$P_Zipcode = explode('-', $Property_Zip_Code);
		$A_Zipcode = explode('-', $Assessed_Zip_Code);
		$U_Zipcode = explode('-', $USPS_Zip_Code);

		if($P_Zipcode[1] != NULL){
			$P_Zipcode[1] = '-'.$P_Zipcode[1];
		} else{
			$P_Zipcode[1] = '';
		} 

		if($A_Zipcode[1] != NULL){
			$A_Zipcode[1] = '-'.$A_Zipcode[1];
		} else{
			$A_Zipcode[1] = '';
		}

		if($U_Zipcode[1] != NULL){
			$U_Zipcode[1] = '-'.$U_Zipcode[1];
		} else{
			$U_Zipcode[1] = '';
		}

		$ZipcodeVariance1 = $this->GetZipcodeSection2($P_Zipcode[1],$A_Zipcode[1],$U_Zipcode[1]);

		$P_Zipcode[1] = $ZipcodeVariance1['PropertyOutputZipcode1'];
		$A_Zipcode[1] = $ZipcodeVariance1['AssessedOutputZipcode1'];
		$U_Zipcode[1] = $ZipcodeVariance1['USPSOutputZipcode1']; 


		if($P_Zipcode[0] == $A_Zipcode[0] && $A_Zipcode[0] == $U_Zipcode[0] && $U_Zipcode[0] == $P_Zipcode[0])
			{
		        $PropertyOutputZipcode ='<span>'.$P_Zipcode[0].$P_Zipcode[1].'</span>';
		       
		        $AssessedOutputZipcode ='<span>'.$A_Zipcode[0].$A_Zipcode[1].'</span>';
		       
		        $USPSOutputZipcode ='<span>'.$U_Zipcode[0].$U_Zipcode[1].'</span>';
			}
			elseif($P_Zipcode[0] != $A_Zipcode[0] && $A_Zipcode[0] != $U_Zipcode[0] && $U_Zipcode[0] != $P_Zipcode[0])
			{		     
		        $PropertyOutputZipcode ='<span style="color:#ff3333;">'.$P_Zipcode[0].$P_Zipcode[1].'</span>';
		       
		        $AssessedOutputZipcode ='<span style="color:#ff3333;">'.$A_Zipcode[0].$A_Zipcode[1].'</span>';
		       
		        $USPSOutputZipcode ='<span style="color:#ff3333;">'.$U_Zipcode[0].$U_Zipcode[1].'</span>';

				$PropertyOutputComments .= $P_Zipcode[0];		
				$AssessedOutputComments .= $A_Zipcode[0];
		        $USPSOutputComments .= $U_Zipcode[0];

			}
			else
			{ 
		        if($P_Zipcode[0] == $A_Zipcode[0])
		        {
		            if($P_Zipcode[0] != $U_Zipcode[0] && $A_Zipcode[0] != $U_Zipcode[0])
		            {
		                $PropertyOutputZipcode ='<span>'.$P_Zipcode[0].'</span><span style="color:#000;">'.$P_Zipcode[1].'</span>';
		                $AssessedOutputZipcode ='<span>'.$A_Zipcode[0].'</span><span style="color:#000;">'.$A_Zipcode[1].'</span>';
		                $USPSOutputZipcode ='<span style="color:#ff3333;">'.$U_Zipcode[0].$U_Zipcode[1].'</span>';


				        $USPSOutputComments .= $U_Zipcode[0];
		            }

		        }
		        if($P_Zipcode[0] == $U_Zipcode[0])
		        {
		            if($P_Zipcode[0] != $A_Zipcode[0] && $U_Zipcode[0] != $A_Zipcode[0])
		            {
		                $PropertyOutputZipcode ='<span>'.$P_Zipcode[0].'</span><span style="color:#000;">'.$P_Zipcode[1].'</span>';;
		                $AssessedOutputZipcode ='<span style="color:#ff3333;">'.$A_Zipcode[0].$A_Zipcode[1].'</span>';
		                $USPSOutputZipcode ='<span>'.$U_Zipcode[0].'</span><span style="color:#000;">'.$U_Zipcode[1].'</span>';

						$AssessedOutputComments .= $A_Zipcode[0];
				        
		            }
		        }
		        if($A_Zipcode[0] == $U_Zipcode[0])
		        {
		            if($P_Zipcode[0] != $A_Zipcode[0] && $U_Zipcode[0] != $P_Zipcode[0])
		            {
		                $PropertyOutputZipcode ='<span style="color:#ff3333;">'.$P_Zipcode[0].$P_Zipcode[1].'</span>';
		                $AssessedOutputZipcode ='<span>'.$A_Zipcode[0].'</span><span style="color:#000;">'.$A_Zipcode[1].'</span>';
		                $USPSOutputZipcode ='<span>'.$U_Zipcode[0].'</span><span style="color:#000;">'.$U_Zipcode[1].'</span>';

		                $PropertyOutputComments .= $P_Zipcode[0];	
		            }
		        }   
			}

			$result = array('PropertyOutputZipcode'=>$PropertyOutputZipcode,'AssessedOutputZipcode'=>$AssessedOutputZipcode,'USPSOutputZipcode'=>$USPSOutputZipcode,'PropertyOutputComments'=>$PropertyOutputComments,'AssessedOutputComments'=>$AssessedOutputComments,'USPSOutputComments'=>$USPSOutputComments);

			return $result;

	}


		// Zipcode No variance 

	function GetZipcodeSection2_NoVariance($P_Zipcode,$A_Zipcode,$U_Zipcode){

		if($P_Zipcode == $A_Zipcode && $A_Zipcode == $U_Zipcode && $U_Zipcode == $P_Zipcode)
			{
		        $PropertyOutputZipcode1 ='<span>'.$P_Zipcode.'</span>';
		        $AssessedOutputZipcode1 ='<span>'.$A_Zipcode.'</span>';
		        $USPSOutputZipcode1 ='<span>'.$U_Zipcode.'</span>';
			}
			elseif($P_Zipcode != $A_Zipcode && $A_Zipcode != $U_Zipcode && $U_Zipcode != $P_Zipcode)
			{		     
		        $PropertyOutputZipcode1 ='<span style="color:#000;">'.$P_Zipcode.'</span>';
		        $AssessedOutputZipcode1 ='<span style="color:#000;">'.$A_Zipcode.'</span>';
		        $USPSOutputZipcode1 ='<span style="color:#000;">'.$U_Zipcode.'</span>';

			}
			else
			{ 
		        if($P_Zipcode == $A_Zipcode)
		        {
		            if($P_Zipcode != $U_Zipcode && $A_Zipcode != $U_Zipcode)
		            {
		                $PropertyOutputZipcode1 ='<span>'.$P_Zipcode.'</span>';
		                $AssessedOutputZipcode1 ='<span>'.$A_Zipcode.'</span>';
		                $USPSOutputZipcode1 ='<span style="color:#000;">'.$U_Zipcode.'</span>';
		            }

		        }
		        if($P_Zipcode == $U_Zipcode)
		        {
		            if($P_Zipcode != $A_Zipcode && $U_Zipcode != $A_Zipcode)
		            {
		                $PropertyOutputZipcode1 ='<span>'.$P_Zipcode.'</span>';
		                $AssessedOutputZipcode1 ='<span style="color:#000;">'.$A_Zipcode.'</span>';
		                $USPSOutputZipcode1 ='<span>'.$U_Zipcode.'</span>';				        
		            }
		        }
		        if($A_Zipcode == $U_Zipcode)
		        {
		            if($P_Zipcode != $A_Zipcode && $U_Zipcode != $P_Zipcode)
		            {
		                $PropertyOutputZipcode1 ='<span style="color:#000;">'.$P_Zipcode.'</span>';
		                $AssessedOutputZipcode1 ='<span>'.$A_Zipcode.'</span>';
		                $USPSOutputZipcode1 ='<span>'.$U_Zipcode.'</span>';
		            }
		        }   
			}

			$result = array('PropertyOutputZipcode1'=>$PropertyOutputZipcode1,'AssessedOutputZipcode1'=>$AssessedOutputZipcode1,'USPSOutputZipcode1'=>$USPSOutputZipcode1);

			return $result;
	}

	function GetZipcodeVariance_NoVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code){

		$P_Zipcode = explode('-', $Property_Zip_Code);
		$A_Zipcode = explode('-', $Assessed_Zip_Code);
		$U_Zipcode = explode('-', $USPS_Zip_Code);

		if($P_Zipcode[1] != NULL){
			$P_Zipcode[1] = '-'.$P_Zipcode[1];
		} else{
			$P_Zipcode[1] = '';
		} 

		if($A_Zipcode[1] != NULL){
			$A_Zipcode[1] = '-'.$A_Zipcode[1];
		} else{
			$A_Zipcode[1] = '';
		}

		if($U_Zipcode[1] != NULL){
			$U_Zipcode[1] = '-'.$U_Zipcode[1];
		} else{
			$U_Zipcode[1] = '';
		} 

		$ZipcodeVariance1 = $this->GetZipcodeSection2($P_Zipcode[1],$A_Zipcode[1],$U_Zipcode[1]);

		$P_Zipcode[1] = $ZipcodeVariance1['PropertyOutputZipcode1'];
		$A_Zipcode[1] = $ZipcodeVariance1['AssessedOutputZipcode1'];
		$U_Zipcode[1] = $ZipcodeVariance1['USPSOutputZipcode1'];

		if($P_Zipcode[0] == $A_Zipcode[0] && $A_Zipcode[0] == $U_Zipcode[0] && $U_Zipcode[0] == $P_Zipcode[0])
			{
		        $PropertyOutputZipcode ='<span>'.$P_Zipcode[0].$P_Zipcode[1].'</span>';
		       
		        $AssessedOutputZipcode ='<span>'.$A_Zipcode[0].$A_Zipcode[1].'</span>';
		       
		        $USPSOutputZipcode ='<span>'.$U_Zipcode[0].$U_Zipcode[1].'</span>';
			}
			elseif($P_Zipcode[0] != $A_Zipcode[0] && $A_Zipcode[0] != $U_Zipcode[0] && $U_Zipcode[0] != $P_Zipcode[0])
			{		     
		        $PropertyOutputZipcode ='<span style="color:#000;">'.$P_Zipcode[0].$P_Zipcode[1].'</span>';
		       
		        $AssessedOutputZipcode ='<span style="color:#000;">'.$A_Zipcode[0].$A_Zipcode[1].'</span>';
		       
		        $USPSOutputZipcode ='<span style="color:#000;">'.$U_Zipcode[0].$U_Zipcode[1].'</span>';

				$PropertyOutputComments .= $P_Zipcode[0];		
				$AssessedOutputComments .= $A_Zipcode[0];
		        $USPSOutputComments .= $U_Zipcode[0];

			}
			else
			{ 
		        if($P_Zipcode[0] == $A_Zipcode[0])
		        {
		            if($P_Zipcode[0] != $U_Zipcode[0] && $A_Zipcode[0] != $U_Zipcode[0])
		            {
		                $PropertyOutputZipcode ='<span>'.$P_Zipcode[0].'</span><span style="color:#000;">'.$P_Zipcode[1].'</span>';
		                $AssessedOutputZipcode ='<span>'.$A_Zipcode[0].'</span><span style="color:#000;">'.$A_Zipcode[1].'</span>';
		                $USPSOutputZipcode ='<span style="color:#000;">'.$U_Zipcode[0].$U_Zipcode[1].'</span>';


				        $USPSOutputComments .= $U_Zipcode[0];
		            }

		        }
		        if($P_Zipcode[0] == $U_Zipcode[0])
		        {
		            if($P_Zipcode[0] != $A_Zipcode[0] && $U_Zipcode[0] != $A_Zipcode[0])
		            {
		                $PropertyOutputZipcode ='<span>'.$P_Zipcode[0].'</span><span style="color:#000;">'.$P_Zipcode[1].'</span>';;
		                $AssessedOutputZipcode ='<span style="color:#000;">'.$A_Zipcode[0].$A_Zipcode[1].'</span>';
		                $USPSOutputZipcode ='<span>'.$U_Zipcode[0].'</span><span style="color:#000;">'.$U_Zipcode[1].'</span>';

						$AssessedOutputComments .= $A_Zipcode[0];
				        
		            }
		        }
		        if($A_Zipcode[0] == $U_Zipcode[0])
		        {
		            if($P_Zipcode[0] != $A_Zipcode[0] && $U_Zipcode[0] != $P_Zipcode[0])
		            {
		                $PropertyOutputZipcode ='<span style="color:#000;">'.$P_Zipcode[0].$P_Zipcode[1].'</span>';
		                $AssessedOutputZipcode ='<span>'.$A_Zipcode[0].'</span><span style="color:#000;">'.$A_Zipcode[1].'</span>';
		                $USPSOutputZipcode ='<span>'.$U_Zipcode[0].'</span><span style="color:#000;">'.$U_Zipcode[1].'</span>';

		                $PropertyOutputComments .= $P_Zipcode[0];	
		            }
		        }   
			}

			$result = array('PropertyOutputZipcode'=>$PropertyOutputZipcode,'AssessedOutputZipcode'=>$AssessedOutputZipcode,'USPSOutputZipcode'=>$USPSOutputZipcode,'PropertyOutputComments'=>$PropertyOutputComments,'AssessedOutputComments'=>$AssessedOutputComments,'USPSOutputComments'=>$USPSOutputComments);

			return $result;
	}

	//Correct Flow for Address Variance

	function CheckPropertyAddress_json($PropertyAddress_json,$Assessed_USPS_Merge){

		$PropertyAddressPresent = '';

		foreach ($Assessed_USPS_Merge as $key => $value) {

			$Property_result_Array = preg_grep ('~\\b' .$value. '\\b~i', $PropertyAddress_json);

            if($Property_result_Array)
            {
                $PropertyAddressPresent = 1;
            }

        }

        return $PropertyAddressPresent;
	}

	function CheckAssessedAddress_json($AssessedAddress_json,$Order_USPS_Merge){

		$AssessedAddressPresent = '';

		foreach ($Order_USPS_Merge as $key => $value) {

			$Assessed_result_Array = preg_grep ('~\\b' .$value. '\\b~i', $AssessedAddress_json);

            if($Assessed_result_Array)
            {
                $AssessedAddressPresent = 1;
            }

        }

        return $AssessedAddressPresent;
	}

	function CheckUSPSAddress_json($USPSAddress_json,$Order_Assessed_Merge){

		$USPSAddressPresent = '';

		foreach ($Order_Assessed_Merge as $key => $value) {

			$USPS_result_Array = preg_grep ('~\\b' .$value. '\\b~i', $USPSAddress_json);

            if($USPS_result_Array)
            {
                $USPSAddressPresent = 1;
            }

        }

        return $USPSAddressPresent;
	}

	function CheckVarianceAddress($OrderDetails){
		
		//Check JSON File Start

			$file_name=('Address_json.json');
			$json_products = file_get_contents(FCPATH.'assets/Address_json.json');
			$products = json_decode($json_products, true);
			$first_names = array_column($products, 'PrimaryStreetSuffixName');
			$second_names = array_column($products, 'PostalServiceStandardSuffixAbbr');
			$third_names = array_column($products, 'concat_data');
			$ArrayMerge = array_merge($first_names,$second_names,$third_names);

			$Abbr_Words_Not_Unique = [];
			$Abbr_Words = [];

			foreach ($products as $product) 
			{
				$ConCatData = [];

				foreach ($product as $key => $values) 
				{
					$ConCatData[] = $values;
				}

				$Abbr_Words_Not_Unique[] = implode(",", $ConCatData); 

			}

			foreach ($Abbr_Words_Not_Unique as $element) 
			{
				$ExplodeArray = array_unique(explode(",", $element)); 
				$Abbr_Words[] = implode(",", $ExplodeArray); 

			}

		//Check JSON File Ends

		
		$PropertyAddress = strtoupper(trim($OrderDetails['PropertyAddress1'])) . ' ' . strtoupper(trim($OrderDetails['PropertyAddress2']));
		$AssessedAddress = strtoupper(trim($OrderDetails['AssessedAddress1'])) . ' ' . strtoupper(trim($OrderDetails['AssessedAddress2']));
		$USPSAddress = strtoupper(trim($OrderDetails['USPSAddress1'])) . ' ' . strtoupper(trim($OrderDetails['USPSAddress2']));

		$OA = explode(" ",$PropertyAddress);
		$AA = explode(" ",$AssessedAddress);
		$UA = explode(" ",$USPSAddress);

		$OrderedAddress_OA = array_filter($OA, 'strlen');
		$AssessedAddress_AA = array_filter($AA, 'strlen');
		$USPSAddress_UA = array_filter($UA, 'strlen');


		/* Check Property Address Starts */

			$PropertyAddressFinal = '';
			$PropertyOutputComments = '';

			$Assessed_USPS_Merge = array_merge($AssessedAddress_AA,$USPSAddress_UA); 

			foreach ($OrderedAddress_OA as $key => $value) {

				$input = preg_quote(strtoupper($value), '~');
				$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

				if($result)
		        {
		        	$PropertyAddress_json = $result;
		        	$CheckPropertyAddress_json = $this->CheckPropertyAddress_json($PropertyAddress_json,$Assessed_USPS_Merge);
		        	if($CheckPropertyAddress_json == 1)
		        	{
		        		$PropertyAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
		        	}
		        	else
	                {			
						$PropertyOutputComments .= $value;
	                    $PropertyAddressFinal .= "<span style=color:#ff0000;>".$value.' '."</span>";
	                }
		        }
		        else
		        {
		        	if(in_array($value, array_intersect($AssessedAddress_AA,$USPSAddress_UA)))
	                {
	                    $PropertyAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
	                }
	                else
	                {			
						$PropertyOutputComments .= $value;
	                    $PropertyAddressFinal .= "<span style=color:#ff0000;>".$value.' '."</span>";
	                }
		        }
		    }

	    /* Check Property Address Ends */

	    /* Check Assessed Address Starts */

			$AssessedAddressFinal = '';
			$AssessedOutputComments = '';

			$Order_USPS_Merge = array_merge($USPSAddress_UA,$OrderedAddress_OA); 

			foreach ($AssessedAddress_AA as $key => $value) {

				$input = preg_quote(strtoupper($value), '~');
				$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

				if($result)
		        {
		        	$AssessedAddress_json = $result;
		        	$CheckAssessedAddress_json = $this->CheckAssessedAddress_json($AssessedAddress_json,$Order_USPS_Merge);
		        	if($CheckAssessedAddress_json == 1)
		        	{
		        		$AssessedAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
		        	}
		        	else
	                {			
						$AssessedOutputComments .= $value;
	                    $AssessedAddressFinal .= "<span style=color:#ff0000;>".$value.' '."</span>";
	                }
		        }
		        else
		        {
		        	if(in_array($value, array_intersect($USPSAddress_UA,$OrderedAddress_OA)))
	                {
	                    $AssessedAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
	                }
	                else
	                {			
						$AssessedOutputComments .= $value;
	                    $AssessedAddressFinal .= "<span style=color:#ff0000;>".$value.' '."</span>";
	                }
		        }
		    }

	    /* Check Assessed Address Ends */

	    /* Check USPS Address Starts */

			$USPSAddressFinal = '';
			$USPSOutputComments = '';

			$Order_Assessed_Merge = array_merge($AssessedAddress_AA,$OrderedAddress_OA); 

			foreach ($USPSAddress_UA as $key => $value) {

				$input = preg_quote(strtoupper($value), '~');
				$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

				//echo '<pre>';print_r($result);

				if($result)
		        {
		        	$USPSAddress_json = $result;
		        	$CheckUSPSAddress_json = $this->CheckUSPSAddress_json($USPSAddress_json,$Order_Assessed_Merge);
		        	if($CheckUSPSAddress_json == 1)
		        	{
		        		$USPSAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
		        	}
		        	else
	                {			
						$USPSOutputComments .= $value;
	                    $USPSAddressFinal .= "<span style=color:#ff0000;>".$value.' '."</span>";
	                }
		        }
		        else
		        {
		        	if(in_array($value, array_intersect($AssessedAddress_AA,$OrderedAddress_OA)))
	                {
	                    $USPSAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
	                }
	                else
	                {			
						$USPSOutputComments .= $value;
	                    $USPSAddressFinal .= "<span style=color:#ff0000;>".$value.' '."</span>";
	                }
		        }
		    }


		   
		/* Check USPS Address Ends */

		/* Get Zipcode, State, City, County Details  Starts */

		    $Property_Zip_Code = trim($OrderDetails['PropertyZipcode']);
			$Assessed_Zip_Code = trim($OrderDetails['AssessedZipcode']);
			$USPS_Zip_Code = trim($OrderDetails['USPSZipcode']);	

			$Assessed_State_Name = trim($OrderDetails['AssessedStateCode']);
			$Assessed_City_Name = trim($OrderDetails['AssessedCityName']);
			$Assessed_County_Name = trim($OrderDetails['AssessedCountyName']);

			$USPS_State_Name = trim($OrderDetails['USPSStateCode']);
			$USPS_City_Name = trim($OrderDetails['USPSCityName']);
			$USPS_County_Name = trim($OrderDetails['USPSCountyName']);	

			$Property_State_Name = trim($OrderDetails['PropertyStateCode']);
			$Property_City_Name = trim($OrderDetails['PropertyCityName']);
			$Property_County_Name = trim($OrderDetails['PropertyCountyName']);


			/*$Property_State_Name = $this->Address_model->GetStateDetails($Property_State_Name);
			$Property_State_Name = $Property_State_Name->StateCode;

			$Property_City_Name = $this->Address_model->GetCityDetails($Property_City_Name);
			$Property_City_Name = $Property_City_Name->CityName;

			$Property_County_Name = $this->Address_model->GetCountyDetails($Property_County_Name);
			$Property_County_Name = $Property_County_Name->CountyName;

			//Assessed Details
			
			$Assessed_State_Name = $this->Address_model->GetStateDetails($Assessed_State_Name);
			$Assessed_State_Name = $Assessed_State_Name->StateCode;

			$Assessed_City_Name = $this->Address_model->GetCityDetails($Assessed_City_Name);
			$Assessed_City_Name = $Assessed_City_Name->CityName;

			$Assessed_County_Name = $this->Address_model->GetCountyDetails($Assessed_County_Name);
			$Assessed_County_Name = $Assessed_County_Name->CountyName;

			//USPS Details

			if($USPS_State_Name == 0){ 
				$USPS_State_Name == '';
			} 
			else{
				$USPS_State_Name = $this->Address_model->GetStateDetails($USPS_State_Name);
				$USPS_State_Name = $USPS_State_Name->StateCode;
			}

			if($USPS_City_Name == 0){ 
				$USPS_City_Name == '';
			} 
			else{
				$USPS_City_Name = $this->Address_model->GetCityDetails($USPS_City_Name);
				$USPS_City_Name = $USPS_City_Name->CityName;
			}

			if($USPS_County_Name == 0){ 
				$USPS_County_Name == '';
			} 
			else{
				$USPS_County_Name = $this->Address_model->GetCountyDetails($USPS_County_Name);
				$USPS_County_Name = $USPS_County_Name->CountyName;
			}*/

		/* Get Zipcode, State, City, County Details  Ends */

		/* Check USPS Address Ends */

			if($Property_Zip_Code == $Assessed_Zip_Code && $Assessed_Zip_Code == $USPS_Zip_Code && $USPS_Zip_Code == $Property_Zip_Code)
			{
		        /*$PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
		       
		        $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
		       
		        $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';*/

		        $ZipcodeVariance = $this->GetZipcodeVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code);

				$Property_Zip_Code = $ZipcodeVariance['PropertyOutputZipcode'];
				$Assessed_Zip_Code = $ZipcodeVariance['AssessedOutputZipcode'];
				$USPS_Zip_Code = $ZipcodeVariance['USPSOutputZipcode'];

				$PropertyOutputComments .= $ZipcodeVariance['PropertyOutputComments'];
				$AssessedOutputComments .= $ZipcodeVariance['AssessedOutputComments'];
				$USPSOutputComments .= $ZipcodeVariance['USPSOutputComments'];

				$PropertyOutputZipcode = $Property_Zip_Code;
		        $AssessedOutputZipcode = $Assessed_Zip_Code;
		        $USPSOutputZipcode = $USPS_Zip_Code;
			}
			elseif($Property_Zip_Code != $Assessed_Zip_Code && $Assessed_Zip_Code != $USPS_Zip_Code && $USPS_Zip_Code != $Property_Zip_Code)
			{

				
				$ZipcodeVariance = $this->GetZipcodeVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code);

				$Property_Zip_Code = $ZipcodeVariance['PropertyOutputZipcode'];
				$Assessed_Zip_Code = $ZipcodeVariance['AssessedOutputZipcode'];
				$USPS_Zip_Code = $ZipcodeVariance['USPSOutputZipcode'];

				$PropertyOutputComments .= $ZipcodeVariance['PropertyOutputComments'];
				$AssessedOutputComments .= $ZipcodeVariance['AssessedOutputComments'];
				$USPSOutputComments .= $ZipcodeVariance['USPSOutputComments'];

				$PropertyOutputZipcode = $Property_Zip_Code;
		        $AssessedOutputZipcode = $Assessed_Zip_Code;
		        $USPSOutputZipcode = $USPS_Zip_Code;

				/*echo '<pre>';print_r($Property_Zip_Code);
				echo '<pre>';print_r($Assessed_Zip_Code);
				echo '<pre>';print_r($USPS_Zip_Code);

				exit;

				$PropertyOutputZipcode ='<span style="color:#ff3333;">'.$Property_Zip_Code.'</span>';
		       
		        $AssessedOutputZipcode ='<span style="color:#ff3333;">'.$Assessed_Zip_Code.'</span>';
		       
		        $USPSOutputZipcode ='<span style="color:#ff3333;">'.$USPS_Zip_Code.'</span>';
		        */


		        

				/*$PropertyOutputComments .= $Property_Zip_Code;		
				$AssessedOutputComments .= $Assessed_Zip_Code;
		        $USPSOutputComments .= $USPS_Zip_Code;*/

			}
			else
			{ 
		        if($Property_Zip_Code == $Assessed_Zip_Code)
		        {
		            if($Property_Zip_Code != $USPS_Zip_Code && $Assessed_Zip_Code != $USPS_Zip_Code)
		            {

		            	$ZipcodeVariance = $this->GetZipcodeVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code);

						$Property_Zip_Code = $ZipcodeVariance['PropertyOutputZipcode'];
						$Assessed_Zip_Code = $ZipcodeVariance['AssessedOutputZipcode'];
						$USPS_Zip_Code = $ZipcodeVariance['USPSOutputZipcode'];

						$PropertyOutputComments .= $ZipcodeVariance['PropertyOutputComments'];
						$AssessedOutputComments .= $ZipcodeVariance['AssessedOutputComments'];
						$USPSOutputComments .= $ZipcodeVariance['USPSOutputComments'];

						$PropertyOutputZipcode = $Property_Zip_Code;
				        $AssessedOutputZipcode = $Assessed_Zip_Code;
				        $USPSOutputZipcode = $USPS_Zip_Code;

		                /*$PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
		                $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
		                $USPSOutputZipcode ='<span style="color:#ff3333;">'.$USPS_Zip_Code.'</span>';


				        $USPSOutputComments .= $USPS_Zip_Code;*/
		            }

		        }
		        if($Property_Zip_Code == $USPS_Zip_Code)
		        {
		            if($Property_Zip_Code != $Assessed_Zip_Code && $USPS_Zip_Code != $Assessed_Zip_Code)
		            {

		            	$ZipcodeVariance = $this->GetZipcodeVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code);

						$Property_Zip_Code = $ZipcodeVariance['PropertyOutputZipcode'];
						$Assessed_Zip_Code = $ZipcodeVariance['AssessedOutputZipcode'];
						$USPS_Zip_Code = $ZipcodeVariance['USPSOutputZipcode'];

						$PropertyOutputComments .= $ZipcodeVariance['PropertyOutputComments'];
						$AssessedOutputComments .= $ZipcodeVariance['AssessedOutputComments'];
						$USPSOutputComments .= $ZipcodeVariance['USPSOutputComments'];

						$PropertyOutputZipcode = $Property_Zip_Code;
				        $AssessedOutputZipcode = $Assessed_Zip_Code;
				        $USPSOutputZipcode = $USPS_Zip_Code;

		                /*$PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
		                $AssessedOutputZipcode ='<span style="color:#ff3333;">'.$Assessed_Zip_Code.'</span>';
		                $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';


						$AssessedOutputComments .= $Assessed_Zip_Code;*/
				        
		            }
		        }
		        if($Assessed_Zip_Code == $USPS_Zip_Code)
		        {
		            if($Property_Zip_Code != $Assessed_Zip_Code && $USPS_Zip_Code != $Property_Zip_Code)
		            {
		                /*$PropertyOutputZipcode ='<span style="color:#ff3333;">'.$Property_Zip_Code.'</span>';
		                $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
		                $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';

		                $PropertyOutputComments .= $Property_Zip_Code;*/



		                $ZipcodeVariance = $this->GetZipcodeVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code);

						$Property_Zip_Code = $ZipcodeVariance['PropertyOutputZipcode'];
						$Assessed_Zip_Code = $ZipcodeVariance['AssessedOutputZipcode'];
						$USPS_Zip_Code = $ZipcodeVariance['USPSOutputZipcode'];

						$PropertyOutputComments .= $ZipcodeVariance['PropertyOutputComments'];
						$AssessedOutputComments .= $ZipcodeVariance['AssessedOutputComments'];
						$USPSOutputComments .= $ZipcodeVariance['USPSOutputComments'];

						$PropertyOutputZipcode = $Property_Zip_Code;
				        $AssessedOutputZipcode = $Assessed_Zip_Code;
				        $USPSOutputZipcode = $USPS_Zip_Code;


		            }
		        }   
			}

		/* Check Zipcode Ends */

		/* Check City Starts */	


		    if($Property_City_Name == $Assessed_City_Name && $Assessed_City_Name == $USPS_City_Name && $USPS_City_Name == $Property_City_Name)
		    {
		    
		        $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
		      
		        $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
		       
		        $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';
		    }
		    elseif($Property_City_Name != $Assessed_City_Name && $Assessed_City_Name != $USPS_City_Name && $USPS_City_Name != $Property_City_Name)
		    {
		        
		        $PropertyOutputCityName ='<span style="color:#ff3333;">'.$Property_City_Name.'</span>';
		       
		        $AssessedOutputCityName ='<span style="color:#ff3333;">'.$Assessed_City_Name.'</span>';
		        
		        $USPSOutputCityName ='<span style="color:#ff3333;">'.$USPS_City_Name.'</span>';

		        $PropertyOutputComments .= $Property_City_Name;		
				$AssessedOutputComments .= $Assessed_City_Name;
		        $USPSOutputComments .= $USPS_City_Name;
		    }
		    else
		    { 
		        if($Property_City_Name == $Assessed_City_Name)
		        {
		            if($Property_City_Name != $USPS_City_Name && $Assessed_City_Name != $USPS_City_Name)
		            {
		                $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
		                $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
		                $USPSOutputCityName ='<span style="color:#ff3333;">'.$USPS_City_Name.'</span>';


				        $USPSOutputComments .= $USPS_City_Name;
		            }

		        }
		        if($Property_City_Name == $USPS_City_Name)
		        {
		            if($Property_City_Name != $Assessed_City_Name && $USPS_City_Name != $Assessed_City_Name)
		            {
		                $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
		                $AssessedOutputCityName ='<span style="color:#ff3333;">'.$Assessed_City_Name.'</span>';
		                $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';

						$AssessedOutputComments .= $Assessed_City_Name;
		            }
		        }
		        if($Assessed_City_Name == $USPS_City_Name)
		        {
		            if($Property_City_Name != $Assessed_City_Name && $USPS_City_Name != $Property_City_Name)
		            {
		                $PropertyOutputCityName ='<span style="color:#ff3333;">'.$Property_City_Name.'</span>';
		                $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
		                $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';

		                $PropertyOutputComments .= $Property_City_Name;	
		            }
		        }   
		    }

		/* Check City Ends */	

		/* Check State Starts */	

		    if($Property_State_Name == $Assessed_State_Name && $Assessed_State_Name == $USPS_State_Name && $USPS_State_Name == $Property_State_Name)
		    {
		    
		        $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
		      
		        $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
		       
		        $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';
		    }
		    elseif($Property_State_Name != $Assessed_State_Name && $Assessed_State_Name != $USPS_State_Name && $USPS_State_Name != $Property_State_Name)
		    {
		        
		        $PropertyOutputStateName ='<span style="color:#ff3333;">'.$Property_State_Name.'</span>';
		       
		        $AssessedOutputStateName ='<span style="color:#ff3333;">'.$Assessed_State_Name.'</span>';
		        
		        $USPSOutputStateName ='<span style="color:#ff3333;">'.$USPS_State_Name.'</span>';


		        $PropertyOutputComments .= $Property_State_Name;		
				$AssessedOutputComments .= $Assessed_State_Name;
		        $USPSOutputComments .= $USPS_State_Name;
		    }
		    else
		    { 
		        if($Property_State_Name == $Assessed_State_Name)
		        {
		            if($Property_State_Name != $USPS_State_Name && $Assessed_State_Name != $USPS_State_Name)
		            {
		                $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
		                $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
		                $USPSOutputStateName ='<span style="color:#ff3333;">'.$USPS_State_Name.'</span>';
		          
		        		$USPSOutputComments .= $USPS_State_Name;

		            }

		        }
		        if($Property_State_Name == $USPS_State_Name)
		        {
		            if($Property_State_Name != $Assessed_State_Name && $USPS_State_Name != $Assessed_State_Name)
		            {
		                $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
		                $AssessedOutputStateName ='<span style="color:#ff3333;">'.$Assessed_State_Name.'</span>';
		                $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';

						$AssessedOutputComments .= $Assessed_State_Name;

		            }
		        }
		        if($Assessed_State_Name == $USPS_State_Name)
		        {
		            if($Property_State_Name != $Assessed_State_Name && $USPS_State_Name != $Property_State_Name)
		            {
		                $PropertyOutputStateName ='<span style="color:#ff3333;">'.$Property_State_Name.'</span>';
		                $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
		                $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';

		                $PropertyOutputComments .= $Property_State_Name;	
		            }
		        }   
		    }

		/* Check State Ends */	

		/* Check County Starts */	


		    if($Property_County_Name == $Assessed_County_Name && $Assessed_County_Name == $USPS_County_Name && $USPS_County_Name == $Property_County_Name)
		    {
		    
		        $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
		      
		        $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
		       
		        $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';
		    }

		    elseif($Property_County_Name != $Assessed_County_Name && $Assessed_County_Name != $USPS_County_Name && $USPS_County_Name != $Property_County_Name)
		    {
		        
		        $PropertyOutputCountyName ='<span style="color:#ff3333;">'.$Property_County_Name.'</span>';
		       
		        $AssessedOutputCountyName ='<span style="color:#ff3333;">'.$Assessed_County_Name.'</span>';
		        
		        $USPSOutputCountyName ='<span style="color:#ff3333;">'.$USPS_County_Name.'</span>';

		        $PropertyOutputComments .= $Property_County_Name;		
				$AssessedOutputComments .= $Assessed_County_Name;
		        $USPSOutputComments .= $USPS_County_Name;
		    }
		    else
		    { 
		        if($Property_County_Name == $Assessed_County_Name)
		        {
		            if($Property_County_Name != $USPS_County_Name && $Assessed_County_Name != $USPS_County_Name)
		            {
		                $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
		                $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
		                $USPSOutputCountyName ='<span style="color:#ff3333;">'.$USPS_County_Name.'</span>';


				        $USPSOutputComments .= $USPS_County_Name;
		            }

		        }
		        if($Property_County_Name == $USPS_County_Name)
		        {
		            if($Property_County_Name != $Assessed_County_Name && $USPS_County_Name != $Assessed_County_Name)
		            {
		                $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
		                $AssessedOutputCountyName ='<span style="color:#ff3333;">'.$Assessed_County_Name.'</span>';
		                $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';

						$AssessedOutputComments .= $Assessed_County_Name;
		            }
		        }
		        if($Assessed_County_Name == $USPS_County_Name)
		        {
		            if($Property_County_Name != $Assessed_County_Name && $USPS_County_Name != $Property_County_Name)
		            {
		                $PropertyOutputCountyName ='<span style="color:#ff3333;">'.$Property_County_Name.'</span>';
		                $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
		                $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';

		                $PropertyOutputComments .= $Property_County_Name;
		            }
		        }   
		    }

		/* Check County Ends */ 

		/* Check Variance County ALL Starts */

			if($Property_County_Name == $Assessed_County_Name && $Property_County_Name == $USPS_County_Name && $Assessed_County_Name == $USPS_County_Name)
	        {
	            $CountyVariance ='';
	        }
	        elseif($Property_County_Name != $Assessed_County_Name && $Property_County_Name != $USPS_County_Name && $Assessed_County_Name != $USPS_County_Name)
	        {
	            $Variance = $Assessed_County_Name.' & '.$USPS_County_Name;
	        	$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
	        }
	        elseif($USPS_County_Name !== $Assessed_County_Name)
	        {
	        	if($Property_County_Name !== $Assessed_County_Name && $Property_County_Name !== $USPS_County_Name){
	        		if($USPS_County_Name == '' ){
	        			$Variance = $Assessed_County_Name;
	        			$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
	        		}
	        		else{
	        			$Variance = $Assessed_County_Name.' & '.$USPS_County_Name;
	        			$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
	        		}
	            }
	            elseif($Property_County_Name !== $Assessed_County_Name){
	            	$Variance = $Assessed_County_Name;
	            	$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
	            }
	            elseif($Property_County_Name !== $USPS_County_Name){
	            	if($USPS_County_Name != '' ){
		            	$Variance = $USPS_County_Name;
		            	$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
		            }
		            else{
	        			$CountyVariance = '';
	        		}
	            }
	        }
	        elseif($USPS_County_Name == $Assessed_County_Name)
	        {
	        	if($Property_County_Name !== $Assessed_County_Name && $Property_County_Name !== $USPS_County_Name){
	        		if($USPS_County_Name == '' ){
	        			$Variance = $Assessed_County_Name;
	        			$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
	        		}
	        		elseif($Assessed_County_Name == $USPS_County_Name){
	        			$Variance = $Assessed_County_Name;
	        			$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
	        		}
	        		else{
	        			$Variance = $Assessed_County_Name.' & '.$USPS_County_Name;
	        			$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
	        		}
	            }
	            elseif($Property_County_Name !== $Assessed_County_Name){
	            	$Variance = $Assessed_County_Name;
	            	$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
	            }
	            elseif($Property_County_Name !== $USPS_County_Name){
	            	if($USPS_County_Name != '' ){
		            	$Variance = $USPS_County_Name;
		            	$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
		            }
		            else{
	        			$CountyVariance = '';
	        		}
	            }
	        }
	        else
	        {
	        	$Variance = $USPS_County_Name;
	        	$CountyVariance = '';
	        }	

        /* Check Variance County ALL Ends */

    	/* Check variance in Comments Starts */					

			if(trim($PropertyOutputComments) && trim($AssessedOutputComments) && trim($USPSOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = '';
                  }
                  //PropertyFinalOutput
                  //AssessedFinalOutput
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ', Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }
                  //AssessedFinalOutput
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS =' and USPS';
                  }
                  else
                  {
                    $USPS =' ';
                  }
                  //USPSFinalOutput
              }
              else if(trim($PropertyOutputComments) && trim($AssessedOutputComments))
              {		                      
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = ' ';
                  }
                  //PropertyFinalOutput
                  //AssessedFinalOutput
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ' and Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }

                  $USPS = ' ';
                  
                  //AssessedFinalOutput
              }
              else if(trim($AssessedOutputComments) && trim($USPSOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ' Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }
                  //AssessedFinalOutput
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS = ' and USPS';
                  }
                  else
                  {
                    $USPS = ' ';
                  }

                  $Ordered = ' ';
                  //USPSFinalOutput
              }
              else if(trim($USPSOutputComments) && trim($PropertyOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = ' ';
                  }
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS = ' and USPS';
                  }
                  else
                  {
                    $USPS = ' ';
                  }

                  $Assessed = ' ';
                  //USPSFinalOutput
              }
              else if(trim($USPSOutputComments))
              {
                      $CommentsAddress = 'Address Variance Found in';
                      $USPS = ' USPS';
                      $Ordered = ' ';
                      $Assessed = ' ';
              }
              else if(trim($AssessedOutputComments))
              {
					$CommentsAddress = 'Address Variance Found in';
					$USPS = ' ';
					$Ordered = ' ';
					$Assessed = ' Assessed';
			  }
              else if(trim($PropertyOutputComments))
              {
                      $CommentsAddress = 'Address Variance Found in';
                      $USPS = ' ';
                      $Assessed = ' ';
                      $Ordered = ' Ordered';
              }
              else
              {
                    $CommentsAddress = 'No Variance Found';
                    $Ordered = ' ';
					$Assessed = ' ';
					$USPS = ' ';
              }

        /* Check variance in Comments Ends */	             


	    $PostArray = new stdClass();

		$PostArray->PropertyAddress = strtoupper($PropertyAddressFinal);
		$PostArray->PropertyZipcode = strtoupper($PropertyOutputZipcode);
		$PostArray->PropertyStateUID = strtoupper($PropertyOutputStateName);
		$PostArray->PropertyCity = strtoupper($PropertyOutputCityName);
		$PostArray->PropertyCountyUID = strtoupper($PropertyOutputCountyName);

		$PostArray->AssessedAddress = strtoupper($AssessedAddressFinal);
		$PostArray->AssessedZipcode = strtoupper($AssessedOutputZipcode);
		$PostArray->AssessedStateUID = strtoupper($AssessedOutputStateName);
		$PostArray->AssessedCity = strtoupper($AssessedOutputCityName);
		$PostArray->AssessedCountyUID = strtoupper($AssessedOutputCountyName);

		$PostArray->USPSAddress = strtoupper($USPSAddressFinal);
		$PostArray->USPSZipcode = strtoupper($USPSOutputZipcode);
		$PostArray->USPSStateUID = strtoupper($USPSOutputStateName);
		$PostArray->USPSCity = strtoupper($USPSOutputCityName);
		$PostArray->USPSCountyUID = strtoupper($USPSOutputCountyName);

		$AddressVarianceDetails = (array)$PostArray;

		$CommentSection = $CommentsAddress.$Ordered.$Assessed.$USPS;

		$result = array('AddressVarianceDetails'=> $AddressVarianceDetails,'CommentSection'=>$CommentSection,'CountyVariance'=>$CountyVariance);

		return $result;

		//echo '<pre>';print_r($result);exit;

	}

	function CheckNoVarianceAddress($OrderDetails){
		
		//Check JSON File Start

			$file_name=('Address_json.json');
			$json_products = file_get_contents(FCPATH.'assets/Address_json.json');
			$products = json_decode($json_products, true);
			$first_names = array_column($products, 'PrimaryStreetSuffixName');
			$second_names = array_column($products, 'PostalServiceStandardSuffixAbbr');
			$third_names = array_column($products, 'concat_data');
			$ArrayMerge = array_merge($first_names,$second_names,$third_names);

			$Abbr_Words_Not_Unique = [];
			$Abbr_Words = [];

			foreach ($products as $product) 
			{
				$ConCatData = [];

				foreach ($product as $key => $values) 
				{
					$ConCatData[] = $values;
				}

				$Abbr_Words_Not_Unique[] = implode(",", $ConCatData); 

			}

			foreach ($Abbr_Words_Not_Unique as $element) 
			{
				$ExplodeArray = array_unique(explode(",", $element)); 
				$Abbr_Words[] = implode(",", $ExplodeArray); 

			}

		//Check JSON File Ends

		
		$PropertyAddress = strtoupper(trim($OrderDetails['PropertyAddress1'])) . ' ' . strtoupper(trim($OrderDetails['PropertyAddress2']));
		$AssessedAddress = strtoupper(trim($OrderDetails['AssessedAddress1'])) . ' ' . strtoupper(trim($OrderDetails['AssessedAddress2']));
		$USPSAddress = strtoupper(trim($OrderDetails['USPSAddress1'])) . ' ' . strtoupper(trim($OrderDetails['USPSAddress2']));

		$OA = explode(" ",$PropertyAddress);
		$AA = explode(" ",$AssessedAddress);
		$UA = explode(" ",$USPSAddress);

		$OrderedAddress_OA = array_filter($OA, 'strlen');
		$AssessedAddress_AA = array_filter($AA, 'strlen');
		$USPSAddress_UA = array_filter($UA, 'strlen');


		/* Check Property Address Starts */

			$PropertyAddressFinal = '';
			$PropertyOutputComments = '';

			$Assessed_USPS_Merge = array_merge($AssessedAddress_AA,$USPSAddress_UA); 

			foreach ($OrderedAddress_OA as $key => $value) {

				$input = preg_quote(strtoupper($value), '~');
				$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

				if($result)
		        {
		        	$PropertyAddress_json = $result;
		        	$CheckPropertyAddress_json = $this->CheckPropertyAddress_json($PropertyAddress_json,$Assessed_USPS_Merge);
		        	if($CheckPropertyAddress_json == 1)
		        	{
		        		$PropertyAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
		        	}
		        	else
	                {			
						$PropertyOutputComments .= $value;
	                    $PropertyAddressFinal .= "<span style=color:#000;>".$value.' '."</span>";
	                }
		        }
		        else
		        {
		        	if(in_array($value, $Assessed_USPS_Merge))
	                {
	                    $PropertyAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
	                }
	                else
	                {			
						$PropertyOutputComments .= $value;
	                    $PropertyAddressFinal .= "<span style=color:#000;>".$value.' '."</span>";
	                }
		        }
		    }

	    /* Check Property Address Ends */

	    /* Check Assessed Address Starts */

			$AssessedAddressFinal = '';
			$AssessedOutputComments = '';

			$Order_USPS_Merge = array_merge($USPSAddress_UA,$OrderedAddress_OA); 

			foreach ($AssessedAddress_AA as $key => $value) {

				$input = preg_quote(strtoupper($value), '~');
				$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

				if($result)
		        {
		        	$AssessedAddress_json = $result;
		        	$CheckAssessedAddress_json = $this->CheckAssessedAddress_json($AssessedAddress_json,$Order_USPS_Merge);
		        	if($CheckAssessedAddress_json == 1)
		        	{
		        		$AssessedAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
		        	}
		        	else
	                {			
						$AssessedOutputComments .= $value;
	                    $AssessedAddressFinal .= "<span style=color:#000;>".$value.' '."</span>";
	                }
		        }
		        else
		        {
		        	if(in_array($value, $Order_USPS_Merge))
	                {
	                    $AssessedAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
	                }
	                else
	                {			
						$AssessedOutputComments .= $value;
	                    $AssessedAddressFinal .= "<span style=color:#000;>".$value.' '."</span>";
	                }
		        }
		    }

	    /* Check Assessed Address Ends */

	    /* Check USPS Address Starts */

			$USPSAddressFinal = '';
			$USPSOutputComments = '';

			$Order_Assessed_Merge = array_merge($AssessedAddress_AA,$OrderedAddress_OA); 

			foreach ($USPSAddress_UA as $key => $value) {

				$input = preg_quote(strtoupper($value), '~');
				$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

				//echo '<pre>';print_r($result);

				if($result)
		        {
		        	$USPSAddress_json = $result;
		        	$CheckUSPSAddress_json = $this->CheckUSPSAddress_json($USPSAddress_json,$Order_Assessed_Merge);
		        	if($CheckUSPSAddress_json == 1)
		        	{
		        		$USPSAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
		        	}
		        	else
	                {			
						$USPSOutputComments .= $value;
	                    $USPSAddressFinal .= "<span style=color:#000;>".$value.' '."</span>";
	                }
		        }
		        else
		        {
		        	if(in_array($value, $Order_Assessed_Merge))
	                {
	                    $USPSAddressFinal .=  "<span style=color:#000;>".$value.' '."</span>";
	                }
	                else
	                {			
						$USPSOutputComments .= $value;
	                    $USPSAddressFinal .= "<span style=color:#000;>".$value.' '."</span>";
	                }
		        }
		    }

		/* Check USPS Address Ends */

		/* Get Zipcode, State, City, County Details  Starts */

		    $Property_Zip_Code = trim($OrderDetails['PropertyZipcode']);
			$Assessed_Zip_Code = trim($OrderDetails['AssessedZipcode']);
			$USPS_Zip_Code = trim($OrderDetails['USPSZipcode']);	

			$Assessed_State_Name = trim($OrderDetails['AssessedStateCode']);
			$Assessed_City_Name = trim($OrderDetails['AssessedCityName']);
			$Assessed_County_Name = trim($OrderDetails['AssessedCountyName']);

			$USPS_State_Name = trim($OrderDetails['USPSStateCode']);
			$USPS_City_Name = trim($OrderDetails['USPSCityName']);
			$USPS_County_Name = trim($OrderDetails['USPSCountyName']);	

			$Property_State_Name = trim($OrderDetails['PropertyStateCode']);
			$Property_City_Name = trim($OrderDetails['PropertyCityName']);
			$Property_County_Name = trim($OrderDetails['PropertyCountyName']);


			/*$Property_State_Name = $this->Address_model->GetStateDetails($Property_State_Name);
			$Property_State_Name = $Property_State_Name->StateCode;

			$Property_City_Name = $this->Address_model->GetCityDetails($Property_City_Name);
			$Property_City_Name = $Property_City_Name->CityName;

			$Property_County_Name = $this->Address_model->GetCountyDetails($Property_County_Name);
			$Property_County_Name = $Property_County_Name->CountyName;

			//Assessed Details
			
			$Assessed_State_Name = $this->Address_model->GetStateDetails($Assessed_State_Name);
			$Assessed_State_Name = $Assessed_State_Name->StateCode;

			$Assessed_City_Name = $this->Address_model->GetCityDetails($Assessed_City_Name);
			$Assessed_City_Name = $Assessed_City_Name->CityName;

			$Assessed_County_Name = $this->Address_model->GetCountyDetails($Assessed_County_Name);
			$Assessed_County_Name = $Assessed_County_Name->CountyName;

			//USPS Details

			if($USPS_State_Name == 0){ 
				$USPS_State_Name == '';
			} 
			else{
				$USPS_State_Name = $this->Address_model->GetStateDetails($USPS_State_Name);
				$USPS_State_Name = $USPS_State_Name->StateCode;
			}

			if($USPS_City_Name == 0){ 
				$USPS_City_Name == '';
			} 
			else{
				$USPS_City_Name = $this->Address_model->GetCityDetails($USPS_City_Name);
				$USPS_City_Name = $USPS_City_Name->CityName;
			}

			if($USPS_County_Name == 0){ 
				$USPS_County_Name == '';
			} 
			else{
				$USPS_County_Name = $this->Address_model->GetCountyDetails($USPS_County_Name);
				$USPS_County_Name = $USPS_County_Name->CountyName;
			}*/

		/* Get Zipcode, State, City, County Details  Ends */

		/* Check USPS Address Ends */

			if($Property_Zip_Code == $Assessed_Zip_Code && $Assessed_Zip_Code == $USPS_Zip_Code && $USPS_Zip_Code == $Property_Zip_Code)
			{
		        /*$PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
		       
		        $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
		       
		        $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';*/

		        $ZipcodeVariance = $this->GetZipcodeVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code);

				$Property_Zip_Code = $ZipcodeVariance['PropertyOutputZipcode'];
				$Assessed_Zip_Code = $ZipcodeVariance['AssessedOutputZipcode'];
				$USPS_Zip_Code = $ZipcodeVariance['USPSOutputZipcode'];

				$PropertyOutputComments .= $ZipcodeVariance['PropertyOutputComments'];
				$AssessedOutputComments .= $ZipcodeVariance['AssessedOutputComments'];
				$USPSOutputComments .= $ZipcodeVariance['USPSOutputComments'];

				$PropertyOutputZipcode = $Property_Zip_Code;
		        $AssessedOutputZipcode = $Assessed_Zip_Code;
		        $USPSOutputZipcode = $USPS_Zip_Code;
			}
			elseif($Property_Zip_Code != $Assessed_Zip_Code && $Assessed_Zip_Code != $USPS_Zip_Code && $USPS_Zip_Code != $Property_Zip_Code)
			{
				$ZipcodeVariance = $this->GetZipcodeVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code);

				$Property_Zip_Code = $ZipcodeVariance['PropertyOutputZipcode'];
				$Assessed_Zip_Code = $ZipcodeVariance['AssessedOutputZipcode'];
				$USPS_Zip_Code = $ZipcodeVariance['USPSOutputZipcode'];

				$PropertyOutputComments .= $ZipcodeVariance['PropertyOutputComments'];
				$AssessedOutputComments .= $ZipcodeVariance['AssessedOutputComments'];
				$USPSOutputComments .= $ZipcodeVariance['USPSOutputComments'];

				$PropertyOutputZipcode = $Property_Zip_Code;
		        $AssessedOutputZipcode = $Assessed_Zip_Code;
		        $USPSOutputZipcode = $USPS_Zip_Code;

				/*echo '<pre>';print_r($Property_Zip_Code);
				echo '<pre>';print_r($Assessed_Zip_Code);
				echo '<pre>';print_r($USPS_Zip_Code);

				exit;

				$PropertyOutputZipcode ='<span style="color:#000;">'.$Property_Zip_Code.'</span>';
		       
		        $AssessedOutputZipcode ='<span style="color:#000;">'.$Assessed_Zip_Code.'</span>';
		       
		        $USPSOutputZipcode ='<span style="color:#000;">'.$USPS_Zip_Code.'</span>';
		        */		        

				/*$PropertyOutputComments .= $Property_Zip_Code;		
				$AssessedOutputComments .= $Assessed_Zip_Code;
		        $USPSOutputComments .= $USPS_Zip_Code;*/

			}
			else
			{ 
		        if($Property_Zip_Code == $Assessed_Zip_Code)
		        {
		            if($Property_Zip_Code != $USPS_Zip_Code && $Assessed_Zip_Code != $USPS_Zip_Code)
		            {

		            	$ZipcodeVariance = $this->GetZipcodeVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code);

						$Property_Zip_Code = $ZipcodeVariance['PropertyOutputZipcode'];
						$Assessed_Zip_Code = $ZipcodeVariance['AssessedOutputZipcode'];
						$USPS_Zip_Code = $ZipcodeVariance['USPSOutputZipcode'];

						$PropertyOutputComments .= $ZipcodeVariance['PropertyOutputComments'];
						$AssessedOutputComments .= $ZipcodeVariance['AssessedOutputComments'];
						$USPSOutputComments .= $ZipcodeVariance['USPSOutputComments'];

						$PropertyOutputZipcode = $Property_Zip_Code;
				        $AssessedOutputZipcode = $Assessed_Zip_Code;
				        $USPSOutputZipcode = $USPS_Zip_Code;

		                /*$PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
		                $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
		                $USPSOutputZipcode ='<span style="color:#000;">'.$USPS_Zip_Code.'</span>';


				        $USPSOutputComments .= $USPS_Zip_Code;*/
		            }

		        }
		        if($Property_Zip_Code == $USPS_Zip_Code)
		        {
		            if($Property_Zip_Code != $Assessed_Zip_Code && $USPS_Zip_Code != $Assessed_Zip_Code)
		            {

		            	$ZipcodeVariance = $this->GetZipcodeVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code);

						$Property_Zip_Code = $ZipcodeVariance['PropertyOutputZipcode'];
						$Assessed_Zip_Code = $ZipcodeVariance['AssessedOutputZipcode'];
						$USPS_Zip_Code = $ZipcodeVariance['USPSOutputZipcode'];

						$PropertyOutputComments .= $ZipcodeVariance['PropertyOutputComments'];
						$AssessedOutputComments .= $ZipcodeVariance['AssessedOutputComments'];
						$USPSOutputComments .= $ZipcodeVariance['USPSOutputComments'];

						$PropertyOutputZipcode = $Property_Zip_Code;
				        $AssessedOutputZipcode = $Assessed_Zip_Code;
				        $USPSOutputZipcode = $USPS_Zip_Code;

		                /*$PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
		                $AssessedOutputZipcode ='<span style="color:#000;">'.$Assessed_Zip_Code.'</span>';
		                $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';


						$AssessedOutputComments .= $Assessed_Zip_Code;*/
				        
		            }
		        }
		        if($Assessed_Zip_Code == $USPS_Zip_Code)
		        {
		            if($Property_Zip_Code != $Assessed_Zip_Code && $USPS_Zip_Code != $Property_Zip_Code)
		            {
		                /*$PropertyOutputZipcode ='<span style="color:#000;">'.$Property_Zip_Code.'</span>';
		                $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
		                $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';

		                $PropertyOutputComments .= $Property_Zip_Code;*/

		                $ZipcodeVariance = $this->GetZipcodeVariance($Property_Zip_Code,$Assessed_Zip_Code,$USPS_Zip_Code);

						$Property_Zip_Code = $ZipcodeVariance['PropertyOutputZipcode'];
						$Assessed_Zip_Code = $ZipcodeVariance['AssessedOutputZipcode'];
						$USPS_Zip_Code = $ZipcodeVariance['USPSOutputZipcode'];

						$PropertyOutputComments .= $ZipcodeVariance['PropertyOutputComments'];
						$AssessedOutputComments .= $ZipcodeVariance['AssessedOutputComments'];
						$USPSOutputComments .= $ZipcodeVariance['USPSOutputComments'];

						$PropertyOutputZipcode = $Property_Zip_Code;
				        $AssessedOutputZipcode = $Assessed_Zip_Code;
				        $USPSOutputZipcode = $USPS_Zip_Code;

		            }
		        }   
			}

		/* Check Zipcode Ends */

		/* Check City Starts */	


		    if($Property_City_Name == $Assessed_City_Name && $Assessed_City_Name == $USPS_City_Name && $USPS_City_Name == $Property_City_Name)
		    {
		    
		        $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
		      
		        $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
		       
		        $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';
		    }
		    elseif($Property_City_Name != $Assessed_City_Name && $Assessed_City_Name != $USPS_City_Name && $USPS_City_Name != $Property_City_Name)
		    {
		        
		        $PropertyOutputCityName ='<span style="color:#000;">'.$Property_City_Name.'</span>';
		       
		        $AssessedOutputCityName ='<span style="color:#000;">'.$Assessed_City_Name.'</span>';
		        
		        $USPSOutputCityName ='<span style="color:#000;">'.$USPS_City_Name.'</span>';

		        $PropertyOutputComments .= $Property_City_Name;		
				$AssessedOutputComments .= $Assessed_City_Name;
		        $USPSOutputComments .= $USPS_City_Name;
		    }
		    else
		    { 
		        if($Property_City_Name == $Assessed_City_Name)
		        {
		            if($Property_City_Name != $USPS_City_Name && $Assessed_City_Name != $USPS_City_Name)
		            {
		                $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
		                $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
		                $USPSOutputCityName ='<span style="color:#000;">'.$USPS_City_Name.'</span>';


				        $USPSOutputComments .= $USPS_City_Name;
		            }

		        }
		        if($Property_City_Name == $USPS_City_Name)
		        {
		            if($Property_City_Name != $Assessed_City_Name && $USPS_City_Name != $Assessed_City_Name)
		            {
		                $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
		                $AssessedOutputCityName ='<span style="color:#000;">'.$Assessed_City_Name.'</span>';
		                $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';

						$AssessedOutputComments .= $Assessed_City_Name;
		            }
		        }
		        if($Assessed_City_Name == $USPS_City_Name)
		        {
		            if($Property_City_Name != $Assessed_City_Name && $USPS_City_Name != $Property_City_Name)
		            {
		                $PropertyOutputCityName ='<span style="color:#000;">'.$Property_City_Name.'</span>';
		                $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
		                $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';

		                $PropertyOutputComments .= $Property_City_Name;	
		            }
		        }   
		    }

		/* Check City Ends */	

		/* Check State Starts */	

		    if($Property_State_Name == $Assessed_State_Name && $Assessed_State_Name == $USPS_State_Name && $USPS_State_Name == $Property_State_Name)
		    {
		    
		        $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
		      
		        $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
		       
		        $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';
		    }
		    elseif($Property_State_Name != $Assessed_State_Name && $Assessed_State_Name != $USPS_State_Name && $USPS_State_Name != $Property_State_Name)
		    {
		        
		        $PropertyOutputStateName ='<span style="color:#000;">'.$Property_State_Name.'</span>';
		       
		        $AssessedOutputStateName ='<span style="color:#000;">'.$Assessed_State_Name.'</span>';
		        
		        $USPSOutputStateName ='<span style="color:#000;">'.$USPS_State_Name.'</span>';


		        $PropertyOutputComments .= $Property_State_Name;		
				$AssessedOutputComments .= $Assessed_State_Name;
		        $USPSOutputComments .= $USPS_State_Name;
		    }
		    else
		    { 
		        if($Property_State_Name == $Assessed_State_Name)
		        {
		            if($Property_State_Name != $USPS_State_Name && $Assessed_State_Name != $USPS_State_Name)
		            {
		                $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
		                $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
		                $USPSOutputStateName ='<span style="color:#000;">'.$USPS_State_Name.'</span>';
		          
		        		$USPSOutputComments .= $USPS_State_Name;

		            }

		        }
		        if($Property_State_Name == $USPS_State_Name)
		        {
		            if($Property_State_Name != $Assessed_State_Name && $USPS_State_Name != $Assessed_State_Name)
		            {
		                $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
		                $AssessedOutputStateName ='<span style="color:#000;">'.$Assessed_State_Name.'</span>';
		                $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';

						$AssessedOutputComments .= $Assessed_State_Name;

		            }
		        }
		        if($Assessed_State_Name == $USPS_State_Name)
		        {
		            if($Property_State_Name != $Assessed_State_Name && $USPS_State_Name != $Property_State_Name)
		            {
		                $PropertyOutputStateName ='<span style="color:#000;">'.$Property_State_Name.'</span>';
		                $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
		                $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';

		                $PropertyOutputComments .= $Property_State_Name;	
		            }
		        }   
		    }

		/* Check State Ends */	

		/* Check County Starts */	


		    if($Property_County_Name == $Assessed_County_Name && $Assessed_County_Name == $USPS_County_Name && $USPS_County_Name == $Property_County_Name)
		    {
		    
		        $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
		      
		        $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
		       
		        $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';
		    }

		    elseif($Property_County_Name != $Assessed_County_Name && $Assessed_County_Name != $USPS_County_Name && $USPS_County_Name != $Property_County_Name)
		    {
		        
		        $PropertyOutputCountyName ='<span style="color:#000;">'.$Property_County_Name.'</span>';
		       
		        $AssessedOutputCountyName ='<span style="color:#000;">'.$Assessed_County_Name.'</span>';
		        
		        $USPSOutputCountyName ='<span style="color:#000;">'.$USPS_County_Name.'</span>';

		        $PropertyOutputComments .= $Property_County_Name;		
				$AssessedOutputComments .= $Assessed_County_Name;
		        $USPSOutputComments .= $USPS_County_Name;
		    }
		    else
		    { 
		        if($Property_County_Name == $Assessed_County_Name)
		        {
		            if($Property_County_Name != $USPS_County_Name && $Assessed_County_Name != $USPS_County_Name)
		            {
		                $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
		                $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
		                $USPSOutputCountyName ='<span style="color:#000;">'.$USPS_County_Name.'</span>';


				        $USPSOutputComments .= $USPS_County_Name;
		            }

		        }
		        if($Property_County_Name == $USPS_County_Name)
		        {
		            if($Property_County_Name != $Assessed_County_Name && $USPS_County_Name != $Assessed_County_Name)
		            {
		                $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
		                $AssessedOutputCountyName ='<span style="color:#000;">'.$Assessed_County_Name.'</span>';
		                $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';

						$AssessedOutputComments .= $Assessed_County_Name;
		            }
		        }
		        if($Assessed_County_Name == $USPS_County_Name)
		        {
		            if($Property_County_Name != $Assessed_County_Name && $USPS_County_Name != $Property_County_Name)
		            {
		                $PropertyOutputCountyName ='<span style="color:#000;">'.$Property_County_Name.'</span>';
		                $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
		                $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';

		                $PropertyOutputComments .= $Property_County_Name;
		            }
		        }   
		    }

		/* Check County Ends */ 

		/* Check Variance County ALL Starts */

			if($Property_County_Name == $Assessed_County_Name && $Property_County_Name == $USPS_County_Name)
	        {
	            $CountyVariance ='';
	        }
	        elseif($USPS_County_Name !== $Assessed_County_Name)
	        {
	        	if($Property_County_Name !== $Assessed_County_Name && $Property_County_Name !== $USPS_County_Name){
	        		if($USPS_County_Name == '' ){
	        			$Variance = $Assessed_County_Name;
	        			$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
	        		}
	        		else{
	        			$Variance = $Assessed_County_Name.' & '.$USPS_County_Name;
	        			$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
	        		}
	            }
	            elseif($Property_County_Name !== $Assessed_County_Name){
	            	$Variance = $Assessed_County_Name;
	            	$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
	            }
	            elseif($Property_County_Name !== $USPS_County_Name){
	            	if($USPS_County_Name != '' ){
		            	$Variance = $USPS_County_Name;
		            	$CountyVariance = 'Ordered as ' .$Property_County_Name.' County, But Found as '.$Variance;
		            }
		            else{
	        			$CountyVariance = '';
	        		}
	            }
	        }
	        else
	        {
	        	$Variance = $USPS_County_Name;
	        	$CountyVariance = '';
	        }	

        /* Check Variance County ALL Ends */

    	/* Check variance in Comments Starts */					

			if(trim($PropertyOutputComments) && trim($AssessedOutputComments) && trim($USPSOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = '';
                  }
                  //PropertyFinalOutput
                  //AssessedFinalOutput
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ', Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }
                  //AssessedFinalOutput
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS =' and USPS';
                  }
                  else
                  {
                    $USPS =' ';
                  }
                  //USPSFinalOutput
              }
              else if(trim($PropertyOutputComments) && trim($AssessedOutputComments))
              {		                      
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = ' ';
                  }
                  //PropertyFinalOutput
                  //AssessedFinalOutput
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ' and Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }

                  $USPS = ' ';
                  
                  //AssessedFinalOutput
              }
              else if(trim($AssessedOutputComments) && trim($USPSOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ' Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }
                  //AssessedFinalOutput
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS = ' and USPS';
                  }
                  else
                  {
                    $USPS = ' ';
                  }

                  $Ordered = ' ';
                  //USPSFinalOutput
              }
              else if(trim($USPSOutputComments) && trim($PropertyOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = ' ';
                  }
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS = ' and USPS';
                  }
                  else
                  {
                    $USPS = ' ';
                  }

                  $Assessed = ' ';
                  //USPSFinalOutput
              }
              else if(trim($USPSOutputComments))
              {
                      $CommentsAddress = 'Address Variance Found in';
                      $USPS = ' USPS';
                      $Ordered = ' ';
                      $Assessed = ' ';
              }
              else if(trim($AssessedOutputComments))
              {
					$CommentsAddress = 'Address Variance Found in';
					$USPS = ' ';
					$Ordered = ' ';
					$Assessed = ' Assessed';
			  }
              else if(trim($PropertyOutputComments))
              {
                      $CommentsAddress = 'Address Variance Found in';
                      $USPS = ' ';
                      $Assessed = ' ';
                      $Ordered = ' Ordered';
              }
              else
              {
                    $CommentsAddress = 'No Variance Found';
                    $Ordered = ' ';
					$Assessed = ' ';
					$USPS = ' ';
              }

        /* Check variance in Comments Ends */	             


	    $PostArray = new stdClass();

		$PostArray->PropertyAddress = strtoupper($PropertyAddressFinal);
		$PostArray->PropertyZipcode = strtoupper($PropertyOutputZipcode);
		$PostArray->PropertyStateUID = strtoupper($PropertyOutputStateName);
		$PostArray->PropertyCity = strtoupper($PropertyOutputCityName);
		$PostArray->PropertyCountyUID = strtoupper($PropertyOutputCountyName);

		$PostArray->AssessedAddress = strtoupper($AssessedAddressFinal);
		$PostArray->AssessedZipcode = strtoupper($AssessedOutputZipcode);
		$PostArray->AssessedStateUID = strtoupper($AssessedOutputStateName);
		$PostArray->AssessedCity = strtoupper($AssessedOutputCityName);
		$PostArray->AssessedCountyUID = strtoupper($AssessedOutputCountyName);

		$PostArray->USPSAddress = strtoupper($USPSAddressFinal);
		$PostArray->USPSZipcode = strtoupper($USPSOutputZipcode);
		$PostArray->USPSStateUID = strtoupper($USPSOutputStateName);
		$PostArray->USPSCity = strtoupper($USPSOutputCityName);
		$PostArray->USPSCountyUID = strtoupper($USPSOutputCountyName);

		$AddressVarianceDetails = (array)$PostArray;

		$CommentSection = $CommentsAddress.$Ordered.$Assessed.$USPS;

		$result = array('AddressVarianceDetails'=> $AddressVarianceDetails,'CommentSection'=>$CommentSection,'CountyVariance'=>$CountyVariance);

		return $result;

		//echo '<pre>';print_r($result);exit;

	}


	//Old Flow for Address Variance

	function CheckVarianceAddressOld($OrderDetails){
		
		//Check JSON File Start

			$file_name=('Address_json.json');
			$json_products = file_get_contents(FCPATH.'assets/Address_json.json');
			$products = json_decode($json_products, true);
			$first_names = array_column($products, 'PrimaryStreetSuffixName');
			$second_names = array_column($products, 'PostalServiceStandardSuffixAbbr');
			$third_names = array_column($products, 'concat_data');
			$ArrayMerge = array_merge($first_names,$second_names,$third_names);

			$Abbr_Words_Not_Unique = [];
			$Abbr_Words = [];

			foreach ($products as $product) 
			{
				$ConCatData = [];

				foreach ($product as $key => $values) 
				{
					$ConCatData[] = $values;
				}

				$Abbr_Words_Not_Unique[] = implode(",", $ConCatData); 

			}

			foreach ($Abbr_Words_Not_Unique as $element) 
			{
				$ExplodeArray = array_unique(explode(",", $element)); 
				$Abbr_Words[] = implode(",", $ExplodeArray); 

			}

		//Check JSON File Ends

		
		/* Address Line 1 Check Variance Starts */

		
		    $Property_Address1= strtolower(trim($OrderDetails['PropertyAddress1']));

		    $a = explode(" ",$Property_Address1);

		    $Assessed_Address1 = strtolower(trim($OrderDetails['AssessedAddress1']));
		    $b = explode(" ",$Assessed_Address1);

		    $USPS_Address1 = strtolower(trim($OrderDetails['USPSAddress1']));
		    $c = explode(" ",$USPS_Address1);

		    $PropertyDiffWord1 ='';
		    $AssessedDiffWord1='';
		    $USPSDiffWord1 ='';

		    $PropertyDiffOutput ='';
            $PropertyOutputComments =" ";
            $array_merge_1 = array_merge(array_intersect_key($b,$c),array_intersect_key($c,$b)); 
            
            foreach ($a as $key => $value) {
                if(in_array($value, $array_merge_1))
                {
                    $PropertyDiffOutput .=  "<span style=color:#000;>".$value.' '."</span>";
	                $PropertyDiffWord1 = preg_quote(strtoupper($value), '~');
                }
                else
                {			
					$input = preg_quote(strtoupper($value), '~');
					$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

					if($result){
	                    $PropertyDiffWord1 = preg_quote(strtoupper($value), '~');
					}
					else{
						$PropertyOutputComments .= $value;
	                    $PropertyDiffOutput .= "<span style=color:#ff0000;>".$value.' '."</span>";
					}
                }
            }
            

            $AssessedDiffOutput ='';
            $AssessedOutputComments =" ";
            $array_merge_2 = array_merge(array_intersect_key($a,$c),array_intersect_key($c,$a)); 

            foreach ($b as $key => $value) {
                if(in_array($value, $array_merge_2))
                {
                    $AssessedDiffOutput .=  "<span style=color:#000;>".$value.' '."</span>";
                    $AssessedDiffWord1 = preg_quote(strtoupper($value), '~');
                }
                else
                {
					$input = preg_quote(strtoupper($value), '~');
					$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);
					
					if($result){
                    	$AssessedDiffWord1 = preg_quote(strtoupper($value), '~');
					}
					else{
						$AssessedOutputComments .= $value;
                    	$AssessedDiffOutput .= "<span style=color:#ff0000;>".$value.' '."</span>"; 
					}
                }
            }

            
            $USPSDiffOutput ='';
            $USPSOutputComments =" ";
            $array_merge_3 = array_merge(array_intersect_key($a,$b),array_intersect_key($b,$a));

          /*  echo '<pre>';print_r($array_merge_3);
            echo '<pre>';print_r($c);*/

            foreach ($c as $key => $value) {
                if(in_array($value, $array_merge_3))
                {
                    $USPSDiffOutput .=  "<span style=color:#000;>".$value.' '."</span>";
                    $USPSDiffWord1 = preg_quote(strtoupper($value), '~');
                }
                else
                {
                	$input = preg_quote(strtoupper($value), '~');
					$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);				

					if($result){
                    	$USPSDiffWord1 = preg_quote(strtoupper($value), '~');
					}
					else{
						$USPSOutputComments .= $value;
                    	$USPSDiffOutput .= "<span style=color:#ff0000;>".$value.' '."</span>"; 
					}
                }
            }

            /*echo '<pre>';print_r($PropertyDiffWord1);
            echo '<pre>';print_r($AssessedDiffWord1);
            echo '<pre>';print_r($USPSDiffWord1);

            exit;*/
       

			$Property_result_Array = preg_grep ('~\\b' .$PropertyDiffWord1. '\\b~i', $Abbr_Words);
			$Assessed_result_Array = preg_grep ('~\\b' .$AssessedDiffWord1. '\\b~i', $Abbr_Words);
			$USPS_result_Array = preg_grep ('~\\b' .$USPSDiffWord1. '\\b~i', $Abbr_Words);

			$OA = array_intersect_key($Property_result_Array,$Assessed_result_Array);
			$AU = array_intersect_key($USPS_result_Array,$Assessed_result_Array);
			$UO = array_intersect_key($Property_result_Array,$USPS_result_Array);

			if($OA != NULL && $AU != NULL && $UO != NULL ){

				$PropertyDiffOutput .= "<span style=color:#000;>".$PropertyDiffWord1.' '."</span>";
				$AssessedDiffOutput .= "<span style=color:#000;>".$AssessedDiffWord1.' '."</span>";
				$USPSDiffOutput .= "<span style=color:#000;>".$USPSDiffWord1.' '."</span>";
			}

			if($OA != NULL && $AU == NULL && $UO == NULL){
				$PropertyDiffOutput .= "<span style=color:#000;>".$PropertyDiffWord1.' '."</span>";
				$AssessedDiffOutput .= "<span style=color:#000;>".$AssessedDiffWord1.' '."</span>";
				$USPSDiffOutput .= "<span style=color:#ff0000;>".$USPSDiffWord1.' '."</span>";						

				$USPSOutputComments .= $USPSDiffWord1;
			}

			if($OA == NULL && $AU != NULL && $UO == NULL){
				$PropertyDiffOutput .= "<span style=color:#ff0000;>".$PropertyDiffWord1.' '."</span>";
				$AssessedDiffOutput .= "<span style=color:#000;>".$AssessedDiffWord1.' '."</span>";
				$USPSDiffOutput .= "<span style=color:#000;>".$USPSDiffWord1.' '."</span>";

				$PropertyOutputComments .= $PropertyDiffWord1;						
			}

			if($OA == NULL && $AU == NULL && $UO != NULL){
				$PropertyDiffOutput .= "<span style=color:#000;>".$PropertyDiffWord1.' '."</span>";
				$AssessedDiffOutput .= "<span style=color:#ff0000;>".$AssessedDiffWord1.' '."</span>";
				$USPSDiffOutput .= "<span style=color:#000;>".$USPSDiffWord1.' '."</span>";

				$AssessedOutputComments .= $AssessedDiffWord1;			
			}

			if($OA == NULL && $AU == NULL && $UO == NULL ){
				$PropertyDiffOutput .= "<span style=color:#ff0000;>".$PropertyDiffWord1.' '."</span>";
				$AssessedDiffOutput .= "<span style=color:#ff0000;>".$AssessedDiffWord1.' '."</span>";
				$USPSDiffOutput .= "<span style=color:#ff0000;>".$USPSDiffWord1.' '."</span>";

				$USPSOutputComments .= $USPSDiffWord1;
				$PropertyOutputComments .= $PropertyDiffWord1;		
				$AssessedOutputComments .= $AssessedDiffWord1;	
			}




		/* Address Line 1 Check Variance Ends */


		/* Address Line 2 Check Variance Starts */

			$Property_Address2 = trim($OrderDetails['PropertyAddress2']);
		    $Assessed_Address2 = trim($OrderDetails['AssessedAddress2']);
		    $USPS_Address2 = trim($OrderDetails['USPSAddress2']);

		    $Property_Address2= trim($OrderDetails['PropertyAddress2']);
		    $d = explode(" ",$Property_Address2);

		    $Assessed_Address2 = trim($OrderDetails['AssessedAddress2']);
		    $e = explode(" ",$Assessed_Address2);

		    $USPS_Address2 = trim($OrderDetails['USPSAddress2']);
		    $f = explode(" ",$USPS_Address2);


		    $PropertyDiffWord2 ='';
		    $AssessedDiffWord2='';
		    $USPSDiffWord2 ='';
        

		    if(trim($Property_Address2) == '' ||  trim($Assessed_Address2) == '' || trim($USPS_Address2) == '')
    		{

    			$PropertyDiffOutput2 ='';
	            $PropertyOutputComments .=" ";
	            $array_merge_4 = array_merge(array_intersect_key($e,$f),array_intersect_key($f,$e)); 

	            foreach ($d as $key => $value) {

	                if(array_key_exists($value, $array_merge_4))
	                {
	                    $PropertyDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $PropertyDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else
	                {
						$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$PropertyDiffWord2 = preg_quote(strtoupper($value), '~');
						}
						else{
							$PropertyOutputComments .= $value;
		                    $PropertyDiffOutput2 .= "<span style=color:#ff0000;>".$value.' '."</span>";
						}
	                }
	            }
	           
	            $AssessedDiffOutput2 ='';
	            $AssessedOutputComments .=" ";
	            $array_merge_5 = array_merge(array_intersect_key($d,$f),array_intersect_key($f,$d)); 
	            // echo'<pre>';print_r($array_merge_5);
	            foreach ($e as $key => $value) {
	                if(array_key_exists($value, $array_merge_5))
	                {
	                    $AssessedDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $AssessedDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else
	                {
	                	$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$AssessedDiffWord2 = preg_quote(strtoupper($value), '~');
						}
						else{
							$AssessedOutputComments .= $value;
		                    $AssessedDiffOutput2 .= "<span style=color:#ff0000;>".$value.' '."</span>";
						}
	                }
	            }
	        
	            $USPSDiffOutput2 ='';
	            $USPSOutputComments .=" ";
	            $array_merge_6 = array_merge(array_intersect_key($d,$e),array_intersect_key($e,$d));

	            foreach ($f as $key => $value) {
	                if(array_key_exists($value, $array_merge_6))
	                {
	                    $USPSDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $USPSDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else{

						$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$USPSDiffWord2 = preg_quote(strtoupper($value), '~');
						}
						else{
							$USPSOutputComments .= $value;
		                    $USPSDiffOutput2 .= "<span style=color:#ff0000;>".$value.' '."</span>";
						}

	                }
	            }
	            
    		}
    		else
    		{
    			$PropertyDiffOutput2 ='';
	            $PropertyOutputComments .=" ";
	            $array_merge_4 = array_merge(array_intersect_key($e,$f),array_intersect_key($f,$e)); 
	            foreach ($d as $key => $value) {
	                if(in_array($value, $array_merge_4))
	                {
	                    $PropertyDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $PropertyDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else
	                {
	                	$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$PropertyDiffWord2 = preg_quote(strtoupper($value), '~');									
						}
						else{
							$PropertyOutputComments .= $value;
		                    $PropertyDiffOutput2 .= "<span style=color:#ff0000;>".$value.' '."</span>";
						}
	                }
	            }

	            $AssessedDiffOutput2 ='';
	            $AssessedOutputComments .=" ";
	            $array_merge_5 = array_merge(array_intersect_key($d,$f),array_intersect_key($f,$d)); 
	            foreach ($e as $key => $value) {
	                if(in_array($value, $array_merge_5))
	                {
	                    $AssessedDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $AssessedDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else
	                {
	                	$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$AssessedDiffWord2 = preg_quote(strtoupper($value), '~');									
						}
						else{
							$AssessedOutputComments .= $value;
		                    $AssessedDiffOutput2 .= "<span style=color:#ff0000;>".$value.' '."</span>";
						}
	                }
	            }

	            $USPSDiffOutput2 ='';
	            $USPSOutputComments .=" ";
	            $array_merge_6 = array_merge(array_intersect_key($d,$e),array_intersect_key($e,$d));
	            foreach ($f as $key => $value) {
	                if(in_array($value, $array_merge_6))
	                {
	                    $USPSDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $USPSDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else
	                {
	                	$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$USPSDiffWord2 = preg_quote(strtoupper($value), '~');
						}
						else{
							$USPSOutputComments .= $value;
		                    $USPSDiffOutput2 .= "<span style=color:#ff0000;>".$value.' '."</span>";
						}

	                }
	            }
    		}

    		$Property_result_Array2 = preg_grep ('~\\b' .$PropertyDiffWord2. '\\b~i', $Abbr_Words);
			$Assessed_result_Array2 = preg_grep ('~\\b' .$AssessedDiffWord2. '\\b~i', $Abbr_Words);
			$USPS_result_Array2 = preg_grep ('~\\b' .$USPSDiffWord2. '\\b~i', $Abbr_Words);
			
			$OA2 = array_intersect_key($Property_result_Array2,$Assessed_result_Array2);
			$AU2 = array_intersect_key($USPS_result_Array2,$Assessed_result_Array2);
			$UO2 = array_intersect_key($Property_result_Array2,$USPS_result_Array2);

			if($OA2 != NULL && $AU2 != NULL && $UO2 != NULL ){

				$PropertyDiffOutput2 .= "<span style=color:#000;>".$PropertyDiffWord2.' '."</span>";
				$AssessedDiffOutput2 .= "<span style=color:#000;>".$AssessedDiffWord2.' '."</span>";
				$USPSDiffOutput2 .= "<span style=color:#000;>".$USPSDiffWord2.' '."</span>";

			}

			elseif($OA2 != NULL && $AU2 == NULL && $UO2 == NULL){
				$PropertyDiffOutput2 .= "<span style=color:#000;>".$PropertyDiffWord2.' '."</span>";
				$AssessedDiffOutput2 .= "<span style=color:#000;>".$AssessedDiffWord2.' '."</span>";
				$USPSDiffOutput2 .= "<span style=color:#ff0000;>".$USPSDiffWord2.' '."</span>";

				$USPSOutputComments .= $USPSDiffWord2;
			}

			elseif($OA2 == NULL && $AU2 != NULL && $UO2 == NULL){
				$PropertyDiffOutput2 .= "<span style=color:#ff0000;>".$PropertyDiffWord2.' '."</span>";
				$AssessedDiffOutput2 .= "<span style=color:#000;>".$AssessedDiffWord2.' '."</span>";
				$USPSDiffOutput2 .= "<span style=color:#000;>".$USPSDiffWord2.' '."</span>";

				$PropertyOutputComments .= $PropertyDiffWord2;
			}

			elseif($OA2 == NULL && $AU2 == NULL && $UO2 != NULL){
				$PropertyDiffOutput2 .= "<span style=color:#000;>".$PropertyDiffWord2.' '."</span>";
				$AssessedDiffOutput2 .= "<span style=color:#ff0000;>".$AssessedDiffWord2.' '."</span>";
				$USPSDiffOutput2 .= "<span style=color:#000;>".$USPSDiffWord2.' '."</span>";

				$AssessedOutputComments .= $AssessedDiffWord2;
			}

			elseif($OA2 == NULL && $AU2 == NULL && $UO2 == NULL ){
				$PropertyDiffOutput2 .= "<span style=color:#ff0000;>".$PropertyDiffWord2.' '."</span>";
				$AssessedDiffOutput2 .= "<span style=color:#ff0000;>".$AssessedDiffWord2.' '."</span>";
				$USPSDiffOutput2 .= "<span style=color:#ff0000;>".$USPSDiffWord2.' '."</span>";

				$USPSOutputComments .= $USPSDiffWord2;
				$PropertyOutputComments .= $PropertyDiffWord2;		
				$AssessedOutputComments .= $AssessedDiffWord2;
			}

    	/* Address Line 2 Check Variance Ends */


	    $Property_Zip_Code = trim($OrderDetails['PropertyZipcode']);
		$Assessed_Zip_Code = trim($OrderDetails['AssessedZipcode']);
		$USPS_Zip_Code = trim($OrderDetails['USPSZipcode']);	

		$Assessed_State_Name = trim($OrderDetails['AssessedState']);
		$Assessed_City_Name = trim($OrderDetails['AssessedCity']);
		$Assessed_County_Name = trim($OrderDetails['AssessedCounty']);

		$USPS_State_Name = trim($OrderDetails['USPSState']);
		$USPS_City_Name = trim($OrderDetails['USPSCity']);
		$USPS_County_Name = trim($OrderDetails['USPSCounty']);	

		$Property_State_Name = trim($OrderDetails['PropertyStateUID']);
		$Property_City_Name = trim($OrderDetails['PropertyCity']);
		$Property_County_Name = trim($OrderDetails['PropertyCountyUID']);

		$Property_State_Name = $this->Address_model->GetStateDetails($Property_State_Name);
		$Property_State_Name = $Property_State_Name->StateCode;

		$Property_City_Name = $this->Address_model->GetCityDetails($Property_City_Name);
		$Property_City_Name = $Property_City_Name->CityName;

		$Property_County_Name = $this->Address_model->GetCountyDetails($Property_County_Name);
		$Property_County_Name = $Property_County_Name->CountyName;

		//Assessed Details
		
		$Assessed_State_Name = $this->Address_model->GetStateDetails($Assessed_State_Name);
		$Assessed_State_Name = $Assessed_State_Name->StateCode;

		$Assessed_City_Name = $this->Address_model->GetCityDetails($Assessed_City_Name);
		$Assessed_City_Name = $Assessed_City_Name->CityName;

		$Assessed_County_Name = $this->Address_model->GetCountyDetails($Assessed_County_Name);
		$Assessed_County_Name = $Assessed_County_Name->CountyName;

		//USPS Details

		if($USPS_State_Name == 0){ 
			$USPS_State_Name == '';
		} 
		else{
			$USPS_State_Name = $this->Address_model->GetStateDetails($USPS_State_Name);
			$USPS_State_Name = $USPS_State_Name->StateCode;
		}

		if($USPS_City_Name == 0){ 
			$USPS_City_Name == '';
		} 
		else{
			$USPS_City_Name = $this->Address_model->GetCityDetails($USPS_City_Name);
			$USPS_City_Name = $USPS_City_Name->CityName;
		}

		if($USPS_County_Name == 0){ 
			$USPS_County_Name == '';
		} 
		else{
			$USPS_County_Name = $this->Address_model->GetCountyDetails($USPS_County_Name);
			$USPS_County_Name = $USPS_County_Name->CountyName;
		}


		//Zipcode Starts
		if($Property_Zip_Code == $Assessed_Zip_Code && $Assessed_Zip_Code == $USPS_Zip_Code && $USPS_Zip_Code == $Property_Zip_Code)
		{
	        $PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
	       
	        $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
	       
	        $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';
		}
		elseif($Property_Zip_Code != $Assessed_Zip_Code && $Assessed_Zip_Code != $USPS_Zip_Code && $USPS_Zip_Code != $Property_Zip_Code)
		{
	     
	        $PropertyOutputZipcode ='<span style="color:#ff3333;">'.$Property_Zip_Code.'</span>';
	       
	        $AssessedOutputZipcode ='<span style="color:#ff3333;">'.$Assessed_Zip_Code.'</span>';
	       
	        $USPSOutputZipcode ='<span style="color:#ff3333;">'.$USPS_Zip_Code.'</span>';

			$PropertyOutputComments .= $Property_Zip_Code;		
			$AssessedOutputComments .= $Assessed_Zip_Code;
	        $USPSOutputComments .= $USPS_Zip_Code;

		}
		else
		{ 
	        if($Property_Zip_Code == $Assessed_Zip_Code)
	        {
	            if($Property_Zip_Code != $USPS_Zip_Code && $Assessed_Zip_Code != $USPS_Zip_Code)
	            {
	                $PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
	                $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
	                $USPSOutputZipcode ='<span style="color:#ff3333;">'.$USPS_Zip_Code.'</span>';


			        $USPSOutputComments .= $USPS_Zip_Code;
	            }

	        }
	        if($Property_Zip_Code == $USPS_Zip_Code)
	        {
	            if($Property_Zip_Code != $Assessed_Zip_Code && $USPS_Zip_Code != $Assessed_Zip_Code)
	            {
	                $PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
	                $AssessedOutputZipcode ='<span style="color:#ff3333;">'.$Assessed_Zip_Code.'</span>';
	                $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';


					$AssessedOutputComments .= $Assessed_Zip_Code;
			        
	            }
	        }
	        if($Assessed_Zip_Code == $USPS_Zip_Code)
	        {
	            if($Property_Zip_Code != $Assessed_Zip_Code && $USPS_Zip_Code != $Property_Zip_Code)
	            {
	                $PropertyOutputZipcode ='<span style="color:#ff3333;">'.$Property_Zip_Code.'</span>';
	                $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
	                $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';

	                $PropertyOutputComments .= $Property_Zip_Code;	
	            }
	        }   
		}

		//Zipcode Ends		

		//City Starts

	    if($Property_City_Name == $Assessed_City_Name && $Assessed_City_Name == $USPS_City_Name && $USPS_City_Name == $Property_City_Name)
	    {
	    
	        $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
	      
	        $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
	       
	        $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';
	    }
	    elseif($Property_City_Name != $Assessed_City_Name && $Assessed_City_Name != $USPS_City_Name && $USPS_City_Name != $Property_City_Name)
	    {
	        
	        $PropertyOutputCityName ='<span style="color:#ff3333;">'.$Property_City_Name.'</span>';
	       
	        $AssessedOutputCityName ='<span style="color:#ff3333;">'.$Assessed_City_Name.'</span>';
	        
	        $USPSOutputCityName ='<span style="color:#ff3333;">'.$USPS_City_Name.'</span>';

	        $PropertyOutputComments .= $Property_City_Name;		
			$AssessedOutputComments .= $Assessed_City_Name;
	        $USPSOutputComments .= $USPS_City_Name;
	    }
	    else
	    { 
	        if($Property_City_Name == $Assessed_City_Name)
	        {
	            if($Property_City_Name != $USPS_City_Name && $Assessed_City_Name != $USPS_City_Name)
	            {
	                $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
	                $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
	                $USPSOutputCityName ='<span style="color:#ff3333;">'.$USPS_City_Name.'</span>';


			        $USPSOutputComments .= $USPS_City_Name;
	            }

	        }
	        if($Property_City_Name == $USPS_City_Name)
	        {
	            if($Property_City_Name != $Assessed_City_Name && $USPS_City_Name != $Assessed_City_Name)
	            {
	                $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
	                $AssessedOutputCityName ='<span style="color:#ff3333;">'.$Assessed_City_Name.'</span>';
	                $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';

					$AssessedOutputComments .= $Assessed_City_Name;
	            }
	        }
	        if($Assessed_City_Name == $USPS_City_Name)
	        {
	            if($Property_City_Name != $Assessed_City_Name && $USPS_City_Name != $Property_City_Name)
	            {
	                $PropertyOutputCityName ='<span style="color:#ff3333;">'.$Property_City_Name.'</span>';
	                $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
	                $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';

	                $PropertyOutputComments .= $Property_City_Name;	
	            }
	        }   
	    }
	    //City Ends

	    //State Starts

	    if($Property_State_Name == $Assessed_State_Name && $Assessed_State_Name == $USPS_State_Name && $USPS_State_Name == $Property_State_Name)
	    {
	    
	        $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
	      
	        $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
	       
	        $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';
	    }
	    elseif($Property_State_Name != $Assessed_State_Name && $Assessed_State_Name != $USPS_State_Name && $USPS_State_Name != $Property_State_Name)
	    {
	        
	        $PropertyOutputStateName ='<span style="color:#ff3333;">'.$Property_State_Name.'</span>';
	       
	        $AssessedOutputStateName ='<span style="color:#ff3333;">'.$Assessed_State_Name.'</span>';
	        
	        $USPSOutputStateName ='<span style="color:#ff3333;">'.$USPS_State_Name.'</span>';


	        $PropertyOutputComments .= $Property_State_Name;		
			$AssessedOutputComments .= $Assessed_State_Name;
	        $USPSOutputComments .= $USPS_State_Name;
	    }
	    else
	    { 
	        if($Property_State_Name == $Assessed_State_Name)
	        {
	            if($Property_State_Name != $USPS_State_Name && $Assessed_State_Name != $USPS_State_Name)
	            {
	                $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
	                $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
	                $USPSOutputStateName ='<span style="color:#ff3333;">'.$USPS_State_Name.'</span>';

	          
	        		$USPSOutputComments .= $USPS_State_Name;

	            }

	        }
	        if($Property_State_Name == $USPS_State_Name)
	        {
	            if($Property_State_Name != $Assessed_State_Name && $USPS_State_Name != $Assessed_State_Name)
	            {
	                $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
	                $AssessedOutputStateName ='<span style="color:#ff3333;">'.$Assessed_State_Name.'</span>';
	                $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';

					$AssessedOutputComments .= $Assessed_State_Name;

	            }
	        }
	        if($Assessed_State_Name == $USPS_State_Name)
	        {
	            if($Property_State_Name != $Assessed_State_Name && $USPS_State_Name != $Property_State_Name)
	            {
	                $PropertyOutputStateName ='<span style="color:#ff3333;">'.$Property_State_Name.'</span>';
	                $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
	                $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';

	                $PropertyOutputComments .= $Property_State_Name;	
	            }
	        }   
	    }
	    //State Ends

	   	//County Starts

	    if($Property_County_Name == $Assessed_County_Name && $Assessed_County_Name == $USPS_County_Name && $USPS_County_Name == $Property_County_Name)
	    {
	    
	        $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
	      
	        $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
	       
	        $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';
	    }

	    elseif($Property_County_Name != $Assessed_County_Name && $Assessed_County_Name != $USPS_County_Name && $USPS_County_Name != $Property_County_Name)
	    {
	        
	        $PropertyOutputCountyName ='<span style="color:#ff3333;">'.$Property_County_Name.'</span>';
	       
	        $AssessedOutputCountyName ='<span style="color:#ff3333;">'.$Assessed_County_Name.'</span>';
	        
	        $USPSOutputCountyName ='<span style="color:#ff3333;">'.$USPS_County_Name.'</span>';

	        $PropertyOutputComments .= $Property_County_Name;		
			$AssessedOutputComments .= $Assessed_County_Name;
	        $USPSOutputComments .= $USPS_County_Name;
	    }
	    else
	    { 
	        if($Property_County_Name == $Assessed_County_Name)
	        {
	            if($Property_County_Name != $USPS_County_Name && $Assessed_County_Name != $USPS_County_Name)
	            {
	                $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
	                $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
	                $USPSOutputCountyName ='<span style="color:#ff3333;">'.$USPS_County_Name.'</span>';


			        $USPSOutputComments .= $USPS_County_Name;
	            }

	        }
	        if($Property_County_Name == $USPS_County_Name)
	        {
	            if($Property_County_Name != $Assessed_County_Name && $USPS_County_Name != $Assessed_County_Name)
	            {
	                $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
	                $AssessedOutputCountyName ='<span style="color:#ff3333;">'.$Assessed_County_Name.'</span>';
	                $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';

					$AssessedOutputComments .= $Assessed_County_Name;
	            }
	        }
	        if($Assessed_County_Name == $USPS_County_Name)
	        {
	            if($Property_County_Name != $Assessed_County_Name && $USPS_County_Name != $Property_County_Name)
	            {
	                $PropertyOutputCountyName ='<span style="color:#ff3333;">'.$Property_County_Name.'</span>';
	                $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
	                $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';

	                $PropertyOutputComments .= $Property_County_Name;
	            }
	        }   
	    }
	    //County Ends				 

		if($Property_County_Name == $Assessed_County_Name && $Property_County_Name == $USPS_County_Name)
        {
            $CountyVariance ='All';
        }
        elseif($USPS_County_Name !== $Assessed_County_Name){

        	if($Property_County_Name !== $Assessed_County_Name && $Property_County_Name !== $USPS_County_Name){
        		$CountyVariance = $Assessed_County_Name.' & '.$USPS_County_Name;
            }
            elseif($Property_County_Name !== $Assessed_County_Name){
            	$CountyVariance = $Assessed_County_Name;
            }
            elseif($Property_County_Name !== $USPS_County_Name){
            	$CountyVariance = $USPS_County_Name;
            }
            
        }
        else
        {
        	$CountyVariance = $USPS_County_Name;
        }	



    	/* Check variance in Comments Starts */					

			if(trim($PropertyOutputComments) && trim($AssessedOutputComments) && trim($USPSOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = '';
                  }
                  //PropertyFinalOutput
                  //AssessedFinalOutput
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ', Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }
                  //AssessedFinalOutput
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS =' and USPS';
                  }
                  else
                  {
                    $USPS =' ';
                  }
                  //USPSFinalOutput
              }
              else if(trim($PropertyOutputComments) && trim($AssessedOutputComments))
              {		                      
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = ' ';
                  }
                  //PropertyFinalOutput
                  //AssessedFinalOutput
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ' and Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }

                  $USPS = ' ';
                  
                  //AssessedFinalOutput
              }
              else if(trim($AssessedOutputComments) && trim($USPSOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ' Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }
                  //AssessedFinalOutput
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS = ' and USPS';
                  }
                  else
                  {
                    $USPS = ' ';
                  }

                  $Ordered = ' ';
                  //USPSFinalOutput
              }
              else if(trim($USPSOutputComments) && trim($PropertyOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = ' ';
                  }
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS = ' and USPS';
                  }
                  else
                  {
                    $USPS = ' ';
                  }

                  $Assessed = ' ';
                  //USPSFinalOutput
              }
              else if(trim($USPSOutputComments))
              {
                      $CommentsAddress = 'Address Variance Found in';
                      $USPS = ' USPS';
                      $Ordered = ' ';
                      $Assessed = ' ';
              }
              else if(trim($AssessedOutputComments))
              {
					$CommentsAddress = 'Address Variance Found in';
					$USPS = ' ';
					$Ordered = ' ';
					$Assessed = ' Assessed';
			  }
              else if(trim($PropertyOutputComments))
              {
                      $CommentsAddress = 'Address Variance Found in';
                      $USPS = ' ';
                      $Assessed = ' ';
                      $Ordered = ' Ordered';
              }
              else
              {
                    $CommentsAddress = 'No Address Variance Found';
                    $Ordered = ' ';
					$Assessed = ' ';
					$USPS = ' ';
              }

        /* Check variance in Comments Ends */	


	    $PostArray = new stdClass();

	    //Property Address Starts

		    /* Proprerty Address Line 1 Starts */

			    $PropertyAddressMatch = strip_tags(strtoupper($PropertyDiffOutput));
			    $PropertyAddress11 = explode(' ', $PropertyAddressMatch);
			    $PropertyAddress1 = array_unique($PropertyAddress11);
			    $PropertyAddress1Final = array_diff_assoc($PropertyAddress11, $PropertyAddress1);//get difference

			    if($PropertyAddress1Final){
					$PostArray->PropertyAddress1 = implode(' ', $PropertyAddress1);				    	
			    }
			    else{
			    	$PostArray->PropertyAddress1 = strtoupper($PropertyDiffOutput);	
			    }

			/* Proprerty Address Line 1 Ends */

			/* Proprerty Address Line 2 Starts */


				$PropertyAddressMatch2 = strip_tags(strtoupper($PropertyDiffOutput2));
			    $PropertyAddress22 = explode(' ', $PropertyAddressMatch2);
			    $PropertyAddress2 = array_unique($PropertyAddress22);
			    $PropertyAddress2Final = array_diff_assoc($PropertyAddress22, $PropertyAddress2);//get difference

			    if($PropertyAddress2Final){
					$PostArray->PropertyAddress2 = implode(' ', $PropertyAddress2);				    	
			    }
			    else{
			    	$PostArray->PropertyAddress2 = strtoupper($PropertyDiffOutput2);	
			    }

			/* Proprerty Address Line 2 Ends */

		//Property Address Ends


	    //Assessed Address Starts

			/* Assessed Address Line 1 Starts */

			    $AssessedAddressMatch = strip_tags(strtoupper($AssessedDiffOutput));
			    $AssessedAddress11 = explode(' ', $AssessedAddressMatch);
			    $AssessedAddress1 = array_unique($AssessedAddress11);
			    $AssessedAddress1Final = array_diff_assoc($AssessedAddress11, $AssessedAddress1);//get difference 

			    if($AssessedAddress1Final){
					$PostArray->AssessedAddress1 = implode(' ', $AssessedAddress1);			    	
			    }
			    else{
					$PostArray->AssessedAddress1 = strtoupper($AssessedDiffOutput);		    	
			    }

			/* Assessed Address Line 1 Ends */

			/* Assessed Address Line 2 Starts */

				$AssessedAddressMatch2 = strip_tags(strtoupper($AssessedDiffOutput2));
			    $AssessedAddress22 = explode(' ', $AssessedAddressMatch2);
			    $AssessedAddress2 = array_unique($AssessedAddress22);
			    $AssessedAddress2Final = array_diff_assoc($AssessedAddress22, $AssessedAddress2);//get difference 

			    if($AssessedAddress2Final){
					$PostArray->AssessedAddress2 = implode(' ', $AssessedAddress2);			    	
			    }
			    else{
					$PostArray->AssessedAddress2 = strtoupper($AssessedDiffOutput2);		    	
			    }

			/* Assessed Address Line 2 Ends */

		//Assessed Address Ends

	    //USPS Address Starts

			/* USPS Address Line 1 Starts */

			    $USPSAddressMatch = strip_tags(strtoupper($USPSDiffOutput));
			    $USPSAddress11 = explode(' ', $USPSAddressMatch);
			    $USPSAddress1 = array_unique($USPSAddress11);
			    $USPSAddress1Final = array_diff_assoc($USPSAddress11, $USPSAddress1);//get difference 

			    if($USPSAddress1Final){
					$PostArray->USPSAddress1 = implode(' ', $USPSAddress1);			    	
			    }
			    else{
					$PostArray->USPSAddress1 = strtoupper($USPSDiffOutput);		    	
			    }

			/* Assessed Address Line 2 Starts */

				$USPSAddressMatch2 = strip_tags(strtoupper($USPSDiffOutput2));
			    $USPSAddress22 = explode(' ', $USPSAddressMatch2);
			    $USPSAddress2 = array_unique($USPSAddress22);
			    $USPSAddress2Final = array_diff_assoc($USPSAddress22, $USPSAddress2);//get difference 

			    if($USPSAddress2Final){
					$PostArray->USPSAddress2 = implode(' ', $USPSAddress2);			    	
			    }
			    else{
					$PostArray->USPSAddress2 = strtoupper($USPSDiffOutput2);		    	
			    }

			/* Assessed Address Line 2 Ends */

		//Assessed Address Ends

		$PostArray->PropertyZipcode = strtoupper($PropertyOutputZipcode);
		$PostArray->PropertyStateUID = strtoupper($PropertyOutputStateName);
		$PostArray->PropertyCity = strtoupper($PropertyOutputCityName);
		$PostArray->PropertyCountyUID = strtoupper($PropertyOutputCountyName);

		$PostArray->AssessedZipcode = strtoupper($AssessedOutputZipcode);
		$PostArray->AssessedStateUID = strtoupper($AssessedOutputStateName);
		$PostArray->AssessedCity = strtoupper($AssessedOutputCityName);
		$PostArray->AssessedCountyUID = strtoupper($AssessedOutputCountyName);

		$PostArray->USPSZipcode = strtoupper($USPSOutputZipcode);
		$PostArray->USPSStateUID = strtoupper($USPSOutputStateName);
		$PostArray->USPSCity = strtoupper($USPSOutputCityName);
		$PostArray->USPSCountyUID = strtoupper($USPSOutputCountyName);

		$AddressVarianceDetails = (array)$PostArray;
		

		$CommentSection = $CommentsAddress.$Ordered.$Assessed.$USPS;


		$result = array('AddressVarianceDetails'=> $AddressVarianceDetails,'CommentSection'=>$CommentSection,'CountyVariance'=>$CountyVariance);

		return $result;

		//echo '<pre>';print_r($result);exit;

	}

	function CheckNoVarianceAddressOld($OrderDetails){
		
		//Check JSON File Start

			$file_name=('Address_json.json');
			$json_products = file_get_contents(FCPATH.'assets/Address_json.json');
			$products = json_decode($json_products, true);
			$first_names = array_column($products, 'PrimaryStreetSuffixName');
			$second_names = array_column($products, 'PostalServiceStandardSuffixAbbr');
			$third_names = array_column($products, 'concat_data');
			$ArrayMerge = array_merge($first_names,$second_names,$third_names);

			$Abbr_Words_Not_Unique = [];
			$Abbr_Words = [];

			foreach ($products as $product) 
			{
				$ConCatData = [];

				foreach ($product as $key => $values) 
				{
					$ConCatData[] = $values;
				}

				$Abbr_Words_Not_Unique[] = implode(",", $ConCatData); 

			}

			foreach ($Abbr_Words_Not_Unique as $element) 
			{
				$ExplodeArray = array_unique(explode(",", $element)); 
				$Abbr_Words[] = implode(",", $ExplodeArray); 

			}

		//Check JSON File Ends

		
		/* Address Line 1 Check Variance Starts */

		
		    $Property_Address1= strtolower(trim($OrderDetails['PropertyAddress1']));

		    $a = explode(" ",$Property_Address1);

		    $Assessed_Address1 = strtolower(trim($OrderDetails['AssessedAddress1']));
		    $b = explode(" ",$Assessed_Address1);

		    $USPS_Address1 = strtolower(trim($OrderDetails['USPSAddress1']));
		    $c = explode(" ",$USPS_Address1);

		    $PropertyDiffWord1 ='';
		    $AssessedDiffWord1='';
		    $USPSDiffWord1 ='';

		    $PropertyDiffOutput ='';
            $PropertyOutputComments =" ";
            $array_merge_1 = array_merge(array_intersect_key($b,$c),array_intersect_key($c,$b)); 

            echo '<pre>';print_r($array_merge_1);
            echo '<pre>';print_r($a);
            
            foreach ($a as $key => $value) {
                if(in_array($value, $array_merge_1))
                {
                    $PropertyDiffOutput .=  "<span style=color:#000;>".$value.' '."</span>";
	                //$PropertyDiffWord1 = preg_quote(strtoupper($value), '~');
	                $PropertyDiffWord1 = 1;
                }
                else
                {			
					$input = preg_quote(strtoupper($value), '~');
					$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

					if($result){
	                    $PropertyDiffWord1 = preg_quote(strtoupper($value), '~');
					}
					else{
						$PropertyOutputComments .= $value;
	                    $PropertyDiffOutput .= "<span style=color:#000;>".$value.' '."</span>";
					}
                }
            }
            

            $AssessedDiffOutput ='';
            $AssessedOutputComments =" ";
            $array_merge_2 = array_merge(array_intersect_key($a,$c),array_intersect_key($c,$a)); 

            echo '<pre>';print_r($array_merge_2);
            echo '<pre>';print_r($b);

            foreach ($b as $key => $value) {
                if(in_array($value, $array_merge_2))
                {
                    $AssessedDiffOutput .=  "<span style=color:#000;>".$value.' '."</span>";
                    //$AssessedDiffWord1 = preg_quote(strtoupper($value), '~');
                    $AssessedDiffWord1 = 1;
                }
                else
                {
					$input = preg_quote(strtoupper($value), '~');
					$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);
					
					if($result){
                    	$AssessedDiffWord1 = preg_quote(strtoupper($value), '~');
					}
					else{
						$AssessedOutputComments .= $value;
                    	$AssessedDiffOutput .= "<span style=color:#000;>".$value.' '."</span>"; 
					}
                }
            }

            
            $USPSDiffOutput ='';
            $USPSOutputComments =" ";
            $array_merge_3 = array_merge(array_intersect_key($a,$b),array_intersect_key($b,$a));

            echo '<pre>';print_r($array_merge_3);
            echo '<pre>';print_r($c);

            foreach ($c as $key => $value) {
            echo '<pre>';print_r($value);
                if(in_array($value, $array_merge_3))
                {
                    $USPSDiffOutput .=  "<span style=color:#000;>".$value.' '."</span>";
                    //$USPSDiffWord1 = preg_quote(strtoupper($value), '~');
                    $USPSDiffWord1 = 2;
                }
                else
                {
                	$input = preg_quote(strtoupper($value), '~');
					$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);				

					if($result){
                    	$USPSDiffWord1 = preg_quote(strtoupper($value), '~');
					}
					else{
						$USPSOutputComments .= $value;
                    	$USPSDiffOutput .= "<span style=color:#000;>".$value.' '."</span>"; 
					}
                }
            }


           /* echo '<pre>';print_r($PropertyDiffWord1);
            echo '<pre>';print_r($AssessedDiffWord1);
            echo '<pre>';print_r($USPSDiffWord1);


			echo '<pre>';print_r($PropertyDiffOutput);
            echo '<pre>';print_r($AssessedDiffOutput);
            echo '<pre>';print_r($USPSDiffOutput);

            exit;*/

            
            

       

			$Property_result_Array = preg_grep ('~\\b' .$PropertyDiffWord1. '\\b~i', $Abbr_Words);
			$Assessed_result_Array = preg_grep ('~\\b' .$AssessedDiffWord1. '\\b~i', $Abbr_Words);
			$USPS_result_Array = preg_grep ('~\\b' .$USPSDiffWord1. '\\b~i', $Abbr_Words);

			$OA = array_intersect_key($Property_result_Array,$Assessed_result_Array);
			$AU = array_intersect_key($USPS_result_Array,$Assessed_result_Array);
			$UO = array_intersect_key($Property_result_Array,$USPS_result_Array);

			if($OA != NULL && $AU != NULL && $UO != NULL ){

				$PropertyDiffOutput .= "<span style=color:#000;>".$PropertyDiffWord1.' '."</span>";
				$AssessedDiffOutput .= "<span style=color:#000;>".$AssessedDiffWord1.' '."</span>";
				$USPSDiffOutput .= "<span style=color:#000;>".$USPSDiffWord1.' '."</span>";
			}

			if($OA != NULL && $AU == NULL && $UO == NULL){
				$PropertyDiffOutput .= "<span style=color:#000;>".$PropertyDiffWord1.' '."</span>";
				$AssessedDiffOutput .= "<span style=color:#000;>".$AssessedDiffWord1.' '."</span>";
				$USPSDiffOutput .= "<span style=color:#000;>".$USPSDiffWord1.' '."</span>";						

				$USPSOutputComments .= $USPSDiffWord1;
			}

			if($OA == NULL && $AU != NULL && $UO == NULL){
				$PropertyDiffOutput .= "<span style=color:#000;>".$PropertyDiffWord1.' '."</span>";
				$AssessedDiffOutput .= "<span style=color:#000;>".$AssessedDiffWord1.' '."</span>";
				$USPSDiffOutput .= "<span style=color:#000;>".$USPSDiffWord1.' '."</span>";

				$PropertyOutputComments .= $PropertyDiffWord1;						
			}

			if($OA == NULL && $AU == NULL && $UO != NULL){
				$PropertyDiffOutput .= "<span style=color:#000;>".$PropertyDiffWord1.' '."</span>";
				$AssessedDiffOutput .= "<span style=color:#000;>".$AssessedDiffWord1.' '."</span>";
				$USPSDiffOutput .= "<span style=color:#000;>".$USPSDiffWord1.' '."</span>";

				$AssessedOutputComments .= $AssessedDiffWord1;			
			}

			if($OA == NULL && $AU == NULL && $UO == NULL ){
				$PropertyDiffOutput .= "<span style=color:#000;>".$PropertyDiffWord1.' '."</span>";
				$AssessedDiffOutput .= "<span style=color:#000;>".$AssessedDiffWord1.' '."</span>";
				$USPSDiffOutput .= "<span style=color:#000;>".$USPSDiffWord1.' '."</span>";

				$USPSOutputComments .= $USPSDiffWord1;
				$PropertyOutputComments .= $PropertyDiffWord1;		
				$AssessedOutputComments .= $AssessedDiffWord1;	
			}




		/* Address Line 1 Check Variance Ends */


		/* Address Line 2 Check Variance Starts */

			$Property_Address2 = trim($OrderDetails['PropertyAddress2']);
		    $Assessed_Address2 = trim($OrderDetails['AssessedAddress2']);
		    $USPS_Address2 = trim($OrderDetails['USPSAddress2']);

		    $Property_Address2= trim($OrderDetails['PropertyAddress2']);
		    $d = explode(" ",$Property_Address2);

		    $Assessed_Address2 = trim($OrderDetails['AssessedAddress2']);
		    $e = explode(" ",$Assessed_Address2);

		    $USPS_Address2 = trim($OrderDetails['USPSAddress2']);
		    $f = explode(" ",$USPS_Address2);


		    $PropertyDiffWord2 ='';
		    $AssessedDiffWord2='';
		    $USPSDiffWord2 ='';
        

		    if(trim($Property_Address2) == '' ||  trim($Assessed_Address2) == '' || trim($USPS_Address2) == '')
    		{

    			$PropertyDiffOutput2 ='';
	            $PropertyOutputComments .=" ";
	            $array_merge_4 = array_merge(array_intersect_key($e,$f),array_intersect_key($f,$e)); 

	            foreach ($d as $key => $value) {

	                if(array_key_exists($value, $array_merge_4))
	                {
	                    $PropertyDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $PropertyDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else
	                {
						$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$PropertyDiffWord2 = preg_quote(strtoupper($value), '~');
						}
						else{
							$PropertyOutputComments .= $value;
		                    $PropertyDiffOutput2 .= "<span style=color:#000;>".$value.' '."</span>";
						}
	                }
	            }
	           
	            $AssessedDiffOutput2 ='';
	            $AssessedOutputComments .=" ";
	            $array_merge_5 = array_merge(array_intersect_key($d,$f),array_intersect_key($f,$d)); 
	            // echo'<pre>';print_r($array_merge_5);
	            foreach ($e as $key => $value) {
	                if(array_key_exists($value, $array_merge_5))
	                {
	                    $AssessedDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $AssessedDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else
	                {
	                	$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$AssessedDiffWord2 = preg_quote(strtoupper($value), '~');
						}
						else{
							$AssessedOutputComments .= $value;
		                    $AssessedDiffOutput2 .= "<span style=color:#000;>".$value.' '."</span>";
						}
	                }
	            }
	        
	            $USPSDiffOutput2 ='';
	            $USPSOutputComments .=" ";
	            $array_merge_6 = array_merge(array_intersect_key($d,$e),array_intersect_key($e,$d));

	            foreach ($f as $key => $value) {
	                if(array_key_exists($value, $array_merge_6))
	                {
	                    $USPSDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $USPSDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else{

						$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$USPSDiffWord2 = preg_quote(strtoupper($value), '~');
						}
						else{
							$USPSOutputComments .= $value;
		                    $USPSDiffOutput2 .= "<span style=color:#000;>".$value.' '."</span>";
						}

	                }
	            }
	            
    		}
    		else
    		{
    			$PropertyDiffOutput2 ='';
	            $PropertyOutputComments .=" ";
	            $array_merge_4 = array_merge(array_intersect_key($e,$f),array_intersect_key($f,$e)); 
	            foreach ($d as $key => $value) {
	                if(in_array($value, $array_merge_4))
	                {
	                    $PropertyDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $PropertyDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else
	                {
	                	$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$PropertyDiffWord2 = preg_quote(strtoupper($value), '~');									
						}
						else{
							$PropertyOutputComments .= $value;
		                    $PropertyDiffOutput2 .= "<span style=color:#000;>".$value.' '."</span>";
						}
	                }
	            }

	            $AssessedDiffOutput2 ='';
	            $AssessedOutputComments .=" ";
	            $array_merge_5 = array_merge(array_intersect_key($d,$f),array_intersect_key($f,$d)); 
	            foreach ($e as $key => $value) {
	                if(in_array($value, $array_merge_5))
	                {
	                    $AssessedDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $AssessedDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else
	                {
	                	$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$AssessedDiffWord2 = preg_quote(strtoupper($value), '~');									
						}
						else{
							$AssessedOutputComments .= $value;
		                    $AssessedDiffOutput2 .= "<span style=color:#000;>".$value.' '."</span>";
						}
	                }
	            }

	            $USPSDiffOutput2 ='';
	            $USPSOutputComments .=" ";
	            $array_merge_6 = array_merge(array_intersect_key($d,$e),array_intersect_key($e,$d));
	            foreach ($f as $key => $value) {
	                if(in_array($value, $array_merge_6))
	                {
	                    $USPSDiffOutput2 .=  "<span style=color:#000;>".$value.' '."</span>";
	                    $USPSDiffWord2 = preg_quote(strtoupper($value), '~');
	                }
	                else
	                {
	                	$input = preg_quote(strtoupper($value), '~');
						$result = preg_grep ('~\\b' .$input. '\\b~i', $Abbr_Words);

						if($result){
							$USPSDiffWord2 = preg_quote(strtoupper($value), '~');
						}
						else{
							$USPSOutputComments .= $value;
		                    $USPSDiffOutput2 .= "<span style=color:#000;>".$value.' '."</span>";
						}

	                }
	            }
    		}

    		$Property_result_Array2 = preg_grep ('~\\b' .$PropertyDiffWord2. '\\b~i', $Abbr_Words);
			$Assessed_result_Array2 = preg_grep ('~\\b' .$AssessedDiffWord2. '\\b~i', $Abbr_Words);
			$USPS_result_Array2 = preg_grep ('~\\b' .$USPSDiffWord2. '\\b~i', $Abbr_Words);
			
			$OA2 = array_intersect_key($Property_result_Array2,$Assessed_result_Array2);
			$AU2 = array_intersect_key($USPS_result_Array2,$Assessed_result_Array2);
			$UO2 = array_intersect_key($Property_result_Array2,$USPS_result_Array2);

			if($OA2 != NULL && $AU2 != NULL && $UO2 != NULL ){

				$PropertyDiffOutput2 .= "<span style=color:#000;>".$PropertyDiffWord2.' '."</span>";
				$AssessedDiffOutput2 .= "<span style=color:#000;>".$AssessedDiffWord2.' '."</span>";
				$USPSDiffOutput2 .= "<span style=color:#000;>".$USPSDiffWord2.' '."</span>";

			}

			elseif($OA2 != NULL && $AU2 == NULL && $UO2 == NULL){
				$PropertyDiffOutput2 .= "<span style=color:#000;>".$PropertyDiffWord2.' '."</span>";
				$AssessedDiffOutput2 .= "<span style=color:#000;>".$AssessedDiffWord2.' '."</span>";
				$USPSDiffOutput2 .= "<span style=color:#000;>".$USPSDiffWord2.' '."</span>";

				$USPSOutputComments .= $USPSDiffWord2;
			}

			elseif($OA2 == NULL && $AU2 != NULL && $UO2 == NULL){
				$PropertyDiffOutput2 .= "<span style=color:#000;>".$PropertyDiffWord2.' '."</span>";
				$AssessedDiffOutput2 .= "<span style=color:#000;>".$AssessedDiffWord2.' '."</span>";
				$USPSDiffOutput2 .= "<span style=color:#000;>".$USPSDiffWord2.' '."</span>";

				$PropertyOutputComments .= $PropertyDiffWord2;
			}

			elseif($OA2 == NULL && $AU2 == NULL && $UO2 != NULL){
				$PropertyDiffOutput2 .= "<span style=color:#000;>".$PropertyDiffWord2.' '."</span>";
				$AssessedDiffOutput2 .= "<span style=color:#000;>".$AssessedDiffWord2.' '."</span>";
				$USPSDiffOutput2 .= "<span style=color:#000;>".$USPSDiffWord2.' '."</span>";

				$AssessedOutputComments .= $AssessedDiffWord2;
			}

			elseif($OA2 == NULL && $AU2 == NULL && $UO2 == NULL ){
				$PropertyDiffOutput2 .= "<span style=color:#000;>".$PropertyDiffWord2.' '."</span>";
				$AssessedDiffOutput2 .= "<span style=color:#000;>".$AssessedDiffWord2.' '."</span>";
				$USPSDiffOutput2 .= "<span style=color:#000;>".$USPSDiffWord2.' '."</span>";

				$USPSOutputComments .= $USPSDiffWord2;
				$PropertyOutputComments .= $PropertyDiffWord2;		
				$AssessedOutputComments .= $AssessedDiffWord2;
			}

    	/* Address Line 2 Check Variance Ends */


	    $Property_Zip_Code = trim($OrderDetails['PropertyZipcode']);
		$Assessed_Zip_Code = trim($OrderDetails['AssessedZipcode']);
		$USPS_Zip_Code = trim($OrderDetails['USPSZipcode']);	

		$Assessed_State_Name = trim($OrderDetails['AssessedState']);
		$Assessed_City_Name = trim($OrderDetails['AssessedCity']);
		$Assessed_County_Name = trim($OrderDetails['AssessedCounty']);

		$USPS_State_Name = trim($OrderDetails['USPSState']);
		$USPS_City_Name = trim($OrderDetails['USPSCity']);
		$USPS_County_Name = trim($OrderDetails['USPSCounty']);	

		$Property_State_Name = trim($OrderDetails['PropertyStateUID']);
		$Property_City_Name = trim($OrderDetails['PropertyCity']);
		$Property_County_Name = trim($OrderDetails['PropertyCountyUID']);

		$Property_State_Name = $this->Address_model->GetStateDetails($Property_State_Name);
		$Property_State_Name = $Property_State_Name->StateCode;

		$Property_City_Name = $this->Address_model->GetCityDetails($Property_City_Name);
		$Property_City_Name = $Property_City_Name->CityName;

		$Property_County_Name = $this->Address_model->GetCountyDetails($Property_County_Name);
		$Property_County_Name = $Property_County_Name->CountyName;

		//Assessed Details
		
		$Assessed_State_Name = $this->Address_model->GetStateDetails($Assessed_State_Name);
		$Assessed_State_Name = $Assessed_State_Name->StateCode;

		$Assessed_City_Name = $this->Address_model->GetCityDetails($Assessed_City_Name);
		$Assessed_City_Name = $Assessed_City_Name->CityName;

		$Assessed_County_Name = $this->Address_model->GetCountyDetails($Assessed_County_Name);
		$Assessed_County_Name = $Assessed_County_Name->CountyName;

		//USPS Details

		if($USPS_State_Name == 0){ 
			$USPS_State_Name == '';
		} 
		else{
			$USPS_State_Name = $this->Address_model->GetStateDetails($USPS_State_Name);
			$USPS_State_Name = $USPS_State_Name->StateCode;
		}

		if($USPS_City_Name == 0){ 
			$USPS_City_Name == '';
		} 
		else{
			$USPS_City_Name = $this->Address_model->GetCityDetails($USPS_City_Name);
			$USPS_City_Name = $USPS_City_Name->CityName;
		}

		if($USPS_County_Name == 0){ 
			$USPS_County_Name == '';
		} 
		else{
			$USPS_County_Name = $this->Address_model->GetCountyDetails($USPS_County_Name);
			$USPS_County_Name = $USPS_County_Name->CountyName;
		}


		//Zipcode Starts
		if($Property_Zip_Code == $Assessed_Zip_Code && $Assessed_Zip_Code == $USPS_Zip_Code && $USPS_Zip_Code == $Property_Zip_Code)
		{
	        $PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
	       
	        $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
	       
	        $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';
		}
		elseif($Property_Zip_Code != $Assessed_Zip_Code && $Assessed_Zip_Code != $USPS_Zip_Code && $USPS_Zip_Code != $Property_Zip_Code)
		{
	     
	        $PropertyOutputZipcode ='<span style="color:#000;">'.$Property_Zip_Code.'</span>';
	       
	        $AssessedOutputZipcode ='<span style="color:#000;">'.$Assessed_Zip_Code.'</span>';
	       
	        $USPSOutputZipcode ='<span style="color:#000;">'.$USPS_Zip_Code.'</span>';

			$PropertyOutputComments .= $Property_Zip_Code;		
			$AssessedOutputComments .= $Assessed_Zip_Code;
	        $USPSOutputComments .= $USPS_Zip_Code;

		}
		else
		{ 
	        if($Property_Zip_Code == $Assessed_Zip_Code)
	        {
	            if($Property_Zip_Code != $USPS_Zip_Code && $Assessed_Zip_Code != $USPS_Zip_Code)
	            {
	                $PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
	                $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
	                $USPSOutputZipcode ='<span style="color:#000;">'.$USPS_Zip_Code.'</span>';


			        $USPSOutputComments .= $USPS_Zip_Code;
	            }

	        }
	        if($Property_Zip_Code == $USPS_Zip_Code)
	        {
	            if($Property_Zip_Code != $Assessed_Zip_Code && $USPS_Zip_Code != $Assessed_Zip_Code)
	            {
	                $PropertyOutputZipcode ='<span>'.$Property_Zip_Code.'</span>';
	                $AssessedOutputZipcode ='<span style="color:#000;">'.$Assessed_Zip_Code.'</span>';
	                $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';


					$AssessedOutputComments .= $Assessed_Zip_Code;
			        
	            }
	        }
	        if($Assessed_Zip_Code == $USPS_Zip_Code)
	        {
	            if($Property_Zip_Code != $Assessed_Zip_Code && $USPS_Zip_Code != $Property_Zip_Code)
	            {
	                $PropertyOutputZipcode ='<span style="color:#000;">'.$Property_Zip_Code.'</span>';
	                $AssessedOutputZipcode ='<span>'.$Assessed_Zip_Code.'</span>';
	                $USPSOutputZipcode ='<span>'.$USPS_Zip_Code.'</span>';

	                $PropertyOutputComments .= $Property_Zip_Code;	
	            }
	        }   
		}

		//Zipcode Ends		

		//City Starts

	    if($Property_City_Name == $Assessed_City_Name && $Assessed_City_Name == $USPS_City_Name && $USPS_City_Name == $Property_City_Name)
	    {
	    
	        $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
	      
	        $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
	       
	        $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';
	    }
	    elseif($Property_City_Name != $Assessed_City_Name && $Assessed_City_Name != $USPS_City_Name && $USPS_City_Name != $Property_City_Name)
	    {
	        
	        $PropertyOutputCityName ='<span style="color:#000;">'.$Property_City_Name.'</span>';
	       
	        $AssessedOutputCityName ='<span style="color:#000;">'.$Assessed_City_Name.'</span>';
	        
	        $USPSOutputCityName ='<span style="color:#000;">'.$USPS_City_Name.'</span>';

	        $PropertyOutputComments .= $Property_City_Name;		
			$AssessedOutputComments .= $Assessed_City_Name;
	        $USPSOutputComments .= $USPS_City_Name;
	    }
	    else
	    { 
	        if($Property_City_Name == $Assessed_City_Name)
	        {
	            if($Property_City_Name != $USPS_City_Name && $Assessed_City_Name != $USPS_City_Name)
	            {
	                $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
	                $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
	                $USPSOutputCityName ='<span style="color:#000;">'.$USPS_City_Name.'</span>';


			        $USPSOutputComments .= $USPS_City_Name;
	            }

	        }
	        if($Property_City_Name == $USPS_City_Name)
	        {
	            if($Property_City_Name != $Assessed_City_Name && $USPS_City_Name != $Assessed_City_Name)
	            {
	                $PropertyOutputCityName ='<span>'.$Property_City_Name.'</span>';
	                $AssessedOutputCityName ='<span style="color:#000;">'.$Assessed_City_Name.'</span>';
	                $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';

					$AssessedOutputComments .= $Assessed_City_Name;
	            }
	        }
	        if($Assessed_City_Name == $USPS_City_Name)
	        {
	            if($Property_City_Name != $Assessed_City_Name && $USPS_City_Name != $Property_City_Name)
	            {
	                $PropertyOutputCityName ='<span style="color:#000;">'.$Property_City_Name.'</span>';
	                $AssessedOutputCityName ='<span>'.$Assessed_City_Name.'</span>';
	                $USPSOutputCityName ='<span>'.$USPS_City_Name.'</span>';

	                $PropertyOutputComments .= $Property_City_Name;	
	            }
	        }   
	    }
	    //City Ends

	    //State Starts

	    if($Property_State_Name == $Assessed_State_Name && $Assessed_State_Name == $USPS_State_Name && $USPS_State_Name == $Property_State_Name)
	    {
	    
	        $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
	      
	        $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
	       
	        $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';
	    }
	    elseif($Property_State_Name != $Assessed_State_Name && $Assessed_State_Name != $USPS_State_Name && $USPS_State_Name != $Property_State_Name)
	    {
	        
	        $PropertyOutputStateName ='<span style="color:#000;">'.$Property_State_Name.'</span>';
	       
	        $AssessedOutputStateName ='<span style="color:#000;">'.$Assessed_State_Name.'</span>';
	        
	        $USPSOutputStateName ='<span style="color:#000;">'.$USPS_State_Name.'</span>';


	        $PropertyOutputComments .= $Property_State_Name;		
			$AssessedOutputComments .= $Assessed_State_Name;
	        $USPSOutputComments .= $USPS_State_Name;
	    }
	    else
	    { 
	        if($Property_State_Name == $Assessed_State_Name)
	        {
	            if($Property_State_Name != $USPS_State_Name && $Assessed_State_Name != $USPS_State_Name)
	            {
	                $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
	                $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
	                $USPSOutputStateName ='<span style="color:#000;">'.$USPS_State_Name.'</span>';

	          
	        		$USPSOutputComments .= $USPS_State_Name;

	            }

	        }
	        if($Property_State_Name == $USPS_State_Name)
	        {
	            if($Property_State_Name != $Assessed_State_Name && $USPS_State_Name != $Assessed_State_Name)
	            {
	                $PropertyOutputStateName ='<span>'.$Property_State_Name.'</span>';
	                $AssessedOutputStateName ='<span style="color:#000;">'.$Assessed_State_Name.'</span>';
	                $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';

					$AssessedOutputComments .= $Assessed_State_Name;

	            }
	        }
	        if($Assessed_State_Name == $USPS_State_Name)
	        {
	            if($Property_State_Name != $Assessed_State_Name && $USPS_State_Name != $Property_State_Name)
	            {
	                $PropertyOutputStateName ='<span style="color:#000;">'.$Property_State_Name.'</span>';
	                $AssessedOutputStateName ='<span>'.$Assessed_State_Name.'</span>';
	                $USPSOutputStateName ='<span>'.$USPS_State_Name.'</span>';

	                $PropertyOutputComments .= $Property_State_Name;	
	            }
	        }   
	    }
	    //State Ends

	   	//County Starts

	    if($Property_County_Name == $Assessed_County_Name && $Assessed_County_Name == $USPS_County_Name && $USPS_County_Name == $Property_County_Name)
	    {
	    
	        $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
	      
	        $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
	       
	        $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';
	    }

	    elseif($Property_County_Name != $Assessed_County_Name && $Assessed_County_Name != $USPS_County_Name && $USPS_County_Name != $Property_County_Name)
	    {
	        
	        $PropertyOutputCountyName ='<span style="color:#000;">'.$Property_County_Name.'</span>';
	       
	        $AssessedOutputCountyName ='<span style="color:#000;">'.$Assessed_County_Name.'</span>';
	        
	        $USPSOutputCountyName ='<span style="color:#000;">'.$USPS_County_Name.'</span>';

	        $PropertyOutputComments .= $Property_County_Name;		
			$AssessedOutputComments .= $Assessed_County_Name;
	        $USPSOutputComments .= $USPS_County_Name;
	    }
	    else
	    { 
	        if($Property_County_Name == $Assessed_County_Name)
	        {
	            if($Property_County_Name != $USPS_County_Name && $Assessed_County_Name != $USPS_County_Name)
	            {
	                $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
	                $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
	                $USPSOutputCountyName ='<span style="color:#000;">'.$USPS_County_Name.'</span>';


			        $USPSOutputComments .= $USPS_County_Name;
	            }

	        }
	        if($Property_County_Name == $USPS_County_Name)
	        {
	            if($Property_County_Name != $Assessed_County_Name && $USPS_County_Name != $Assessed_County_Name)
	            {
	                $PropertyOutputCountyName ='<span>'.$Property_County_Name.'</span>';
	                $AssessedOutputCountyName ='<span style="color:#000;">'.$Assessed_County_Name.'</span>';
	                $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';

					$AssessedOutputComments .= $Assessed_County_Name;
	            }
	        }
	        if($Assessed_County_Name == $USPS_County_Name)
	        {
	            if($Property_County_Name != $Assessed_County_Name && $USPS_County_Name != $Property_County_Name)
	            {
	                $PropertyOutputCountyName ='<span style="color:#000;">'.$Property_County_Name.'</span>';
	                $AssessedOutputCountyName ='<span>'.$Assessed_County_Name.'</span>';
	                $USPSOutputCountyName ='<span>'.$USPS_County_Name.'</span>';

	                $PropertyOutputComments .= $Property_County_Name;
	            }
	        }   
	    }
	    //County Ends				 

		if($Property_County_Name == $Assessed_County_Name && $Property_County_Name == $USPS_County_Name)
        {
            $CountyVariance ='All';
        }
        elseif($USPS_County_Name !== $Assessed_County_Name){

        	if($Property_County_Name !== $Assessed_County_Name && $Property_County_Name !== $USPS_County_Name){
        		$CountyVariance = $Assessed_County_Name.' & '.$USPS_County_Name;
            }
            elseif($Property_County_Name !== $Assessed_County_Name){
            	$CountyVariance = $Assessed_County_Name;
            }
            elseif($Property_County_Name !== $USPS_County_Name){
            	$CountyVariance = $USPS_County_Name;
            }
            
        }
        else
        {
        	$CountyVariance = $USPS_County_Name;
        }	



    	/* Check variance in Comments Starts */					

			if(trim($PropertyOutputComments) && trim($AssessedOutputComments) && trim($USPSOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = '';
                  }
                  //PropertyFinalOutput
                  //AssessedFinalOutput
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ', Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }
                  //AssessedFinalOutput
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS =' and USPS';
                  }
                  else
                  {
                    $USPS =' ';
                  }
                  //USPSFinalOutput
              }
              else if(trim($PropertyOutputComments) && trim($AssessedOutputComments))
              {		                      
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = ' ';
                  }
                  //PropertyFinalOutput
                  //AssessedFinalOutput
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ' and Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }

                  $USPS = ' ';
                  
                  //AssessedFinalOutput
              }
              else if(trim($AssessedOutputComments) && trim($USPSOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  if(trim($AssessedOutputComments))
                  {
                    $Assessed = ' Assessed';
                  }
                  else
                  {
                    $Assessed = ' ';
                  }
                  //AssessedFinalOutput
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS = ' and USPS';
                  }
                  else
                  {
                    $USPS = ' ';
                  }

                  $Ordered = ' ';
                  //USPSFinalOutput
              }
              else if(trim($USPSOutputComments) && trim($PropertyOutputComments))
              {
                  $CommentsAddress = 'Address Variance Found in';
                  //PropertyFinalOutput
                  if(trim($PropertyOutputComments))
                  {
                    $Ordered = ' Ordered';
                  }
                  else
                  {
                    $Ordered = ' ';
                  }
                  //USPSFinalOutput
                  if(trim($USPSOutputComments))
                  {
                    $USPS = ' and USPS';
                  }
                  else
                  {
                    $USPS = ' ';
                  }

                  $Assessed = ' ';
                  //USPSFinalOutput
              }
              else if(trim($USPSOutputComments))
              {
                      $CommentsAddress = 'Address Variance Found in';
                      $USPS = ' USPS';
                      $Ordered = ' ';
                      $Assessed = ' ';
              }
              else if(trim($AssessedOutputComments))
              {
					$CommentsAddress = 'Address Variance Found in';
					$USPS = ' ';
					$Ordered = ' ';
					$Assessed = ' Assessed';
			  }
              else if(trim($PropertyOutputComments))
              {
                      $CommentsAddress = 'Address Variance Found in';
                      $USPS = ' ';
                      $Assessed = ' ';
                      $Ordered = ' Ordered';
              }
              else
              {
                    $CommentsAddress = 'No Address Variance Found';
                    $Ordered = ' ';
					$Assessed = ' ';
					$USPS = ' ';
              }

        /* Check variance in Comments Ends */	


	    $PostArray = new stdClass();

	    //Property Address Starts

		    /* Proprerty Address Line 1 Starts */

			    $PropertyAddressMatch = strip_tags(strtoupper($PropertyDiffOutput));
			    $PropertyAddress11 = explode(' ', $PropertyAddressMatch);
			    $PropertyAddress1 = array_unique($PropertyAddress11);
			    $PropertyAddress1Final = array_diff_assoc($PropertyAddress11, $PropertyAddress1);//get difference

			    if($PropertyAddress1Final){
					$PostArray->PropertyAddress1 = implode(' ', $PropertyAddress1);				    	
			    }
			    else{
			    	$PostArray->PropertyAddress1 = strtoupper($PropertyDiffOutput);	
			    }

			/* Proprerty Address Line 1 Ends */

			/* Proprerty Address Line 2 Starts */


				$PropertyAddressMatch2 = strip_tags(strtoupper($PropertyDiffOutput2));
			    $PropertyAddress22 = explode(' ', $PropertyAddressMatch2);
			    $PropertyAddress2 = array_unique($PropertyAddress22);
			    $PropertyAddress2Final = array_diff_assoc($PropertyAddress22, $PropertyAddress2);//get difference

			    if($PropertyAddress2Final){
					$PostArray->PropertyAddress2 = implode(' ', $PropertyAddress2);				    	
			    }
			    else{
			    	$PostArray->PropertyAddress2 = strtoupper($PropertyDiffOutput2);	
			    }

			/* Proprerty Address Line 2 Ends */

		//Property Address Ends


	    //Assessed Address Starts

			/* Assessed Address Line 1 Starts */

			    $AssessedAddressMatch = strip_tags(strtoupper($AssessedDiffOutput));
			    $AssessedAddress11 = explode(' ', $AssessedAddressMatch);
			    $AssessedAddress1 = array_unique($AssessedAddress11);
			    $AssessedAddress1Final = array_diff_assoc($AssessedAddress11, $AssessedAddress1);//get difference 

			    if($AssessedAddress1Final){
					$PostArray->AssessedAddress1 = implode(' ', $AssessedAddress1);			    	
			    }
			    else{
					$PostArray->AssessedAddress1 = strtoupper($AssessedDiffOutput);		    	
			    }

			/* Assessed Address Line 1 Ends */

			/* Assessed Address Line 2 Starts */

				$AssessedAddressMatch2 = strip_tags(strtoupper($AssessedDiffOutput2));
			    $AssessedAddress22 = explode(' ', $AssessedAddressMatch2);
			    $AssessedAddress2 = array_unique($AssessedAddress22);
			    $AssessedAddress2Final = array_diff_assoc($AssessedAddress22, $AssessedAddress2);//get difference 

			    if($AssessedAddress2Final){
					$PostArray->AssessedAddress2 = implode(' ', $AssessedAddress2);			    	
			    }
			    else{
					$PostArray->AssessedAddress2 = strtoupper($AssessedDiffOutput2);		    	
			    }

			/* Assessed Address Line 2 Ends */

		//Assessed Address Ends

	    //USPS Address Starts

			/* USPS Address Line 1 Starts */

			    $USPSAddressMatch = strip_tags(strtoupper($USPSDiffOutput));
			    $USPSAddress11 = explode(' ', $USPSAddressMatch);
			    $USPSAddress1 = array_unique($USPSAddress11);
			    $USPSAddress1Final = array_diff_assoc($USPSAddress11, $USPSAddress1);//get difference 

			    if($USPSAddress1Final){
					$PostArray->USPSAddress1 = implode(' ', $USPSAddress1);			    	
			    }
			    else{
					$PostArray->USPSAddress1 = strtoupper($USPSDiffOutput);		    	
			    }

			/* Assessed Address Line 2 Starts */

				$USPSAddressMatch2 = strip_tags(strtoupper($USPSDiffOutput2));
			    $USPSAddress22 = explode(' ', $USPSAddressMatch2);
			    $USPSAddress2 = array_unique($USPSAddress22);
			    $USPSAddress2Final = array_diff_assoc($USPSAddress22, $USPSAddress2);//get difference 

			    if($USPSAddress2Final){
					$PostArray->USPSAddress2 = implode(' ', $USPSAddress2);			    	
			    }
			    else{
					$PostArray->USPSAddress2 = strtoupper($USPSDiffOutput2);		    	
			    }

			/* Assessed Address Line 2 Ends */

		//Assessed Address Ends

		$PostArray->PropertyZipcode = strtoupper($PropertyOutputZipcode);
		$PostArray->PropertyStateUID = strtoupper($PropertyOutputStateName);
		$PostArray->PropertyCity = strtoupper($PropertyOutputCityName);
		$PostArray->PropertyCountyUID = strtoupper($PropertyOutputCountyName);

		$PostArray->AssessedZipcode = strtoupper($AssessedOutputZipcode);
		$PostArray->AssessedStateUID = strtoupper($AssessedOutputStateName);
		$PostArray->AssessedCity = strtoupper($AssessedOutputCityName);
		$PostArray->AssessedCountyUID = strtoupper($AssessedOutputCountyName);

		$PostArray->USPSZipcode = strtoupper($USPSOutputZipcode);
		$PostArray->USPSStateUID = strtoupper($USPSOutputStateName);
		$PostArray->USPSCity = strtoupper($USPSOutputCityName);
		$PostArray->USPSCountyUID = strtoupper($USPSOutputCountyName);

		$AddressVarianceDetails = (array)$PostArray;
		

		$CommentSection = $CommentsAddress.$Ordered.$Assessed.$USPS;


		$result = array('AddressVarianceDetails'=> $AddressVarianceDetails,'CommentSection'=>$CommentSection,'CountyVariance'=>$CountyVariance);

		return $result;

		//echo '<pre>';print_r($result);exit;

	}

}
?>
