<?php
if(!defined('BASEPATH'))exit('No direct script access allowed');
class apis_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
		
	}

	function CheckAuthKey($AuthenticationKey)
	{
		$query = $this->db->query("SELECT * FROM `musers` WHERE Authkey = '".$AuthenticationKey."' ");
    	if($query->num_rows() > 0)
        {
            return true;
        }
        else{
            return false;
        }
	}

	function getLoginDetails($AuthenticationKey)
	{
		$UserDetails = [];
		$musers = $this->common_model->get_row('musers', ['AuthKey'=>$AuthenticationKey]);
		if (!empty($musers) ) 
		{
			$UserDetails['UserUID'] = $musers->UserUID;
			$UserDetails['AbstractorUID'] = $musers->AbstractorUID;
		}
		// $rejection['OrderFlag']=2;
    		// $rejection['RejectedByUserUID']=$UserDetails;

		return $UserDetails;

	}
//start
// function fetchRoleByUserUID($UserUID)
// {
// echo $UserUID;
// $data =$this->db->select('*')->from('musers')->where('AbstractorUID',$UserUID)->get()->result();
// print_r($data);
// die("dead");
// 		// return $data;

// 	// $Roles = $this->db->query("SELECT RoleUID FROM musers WHERE AbstractorUID =$UserUID");
// 	// print_r($Roles);exit;
// 	// $Role = $this->db->query("SELECT RoleType FROM mroles WHERE RoleUID =".$Roles->RoleUID)->row();
// 	// $RoleType = $Role->RoleType;
// 	// return $RoleType; whatever is should display right okay then we have given print_r in controller too.
// }
//end
	function login($Username,$Password)
	{
		$query = $this->db->query("SELECT * FROM musers WHERE LoginID ='".$Username."' and Password='".$Password."' and Active = 1 ");
		if($query->num_rows() >0)
		{
			$result = $query->result();
			foreach($result as $data)
			{
			  $UserUID = $data->UserUID;
			  $LoginID = $data->LoginID;
			  $UserName = $data->UserName;
			  $Password = $data->Password;
			  $RoleUID = $data->RoleUID;
			  $AuthenticationKey = $data->AuthKey;
			  $EmailID = $data->UserEmailID;
			  $ContactNo = $data->UserContactNo;
			  $AbstractorUID = $data->AbstractorUID;
			}
			$Role = $this->db->query("SELECT RoleName, RoleType FROM mroles WHERE RoleUID =".$RoleUID)->row();
			$RoleName = $Role->RoleName;
			
			
			$Session = random_string('alnum',20);
			$fieldArray = array('SessionValue' => $Session,'UserUID' => $UserUID,'LoggedDateTime' =>date('y-m-d H:i:s'));
			$this->db->insert('tmobilesessions', $fieldArray);
			$SessionUID =  $this->db->insert_id();
			$SessionVal =  $this->db->query("SELECT SessionValue FROM tmobilesessions WHERE SessionUID =".$SessionUID)->row();
			$SessionValue = $SessionVal->SessionValue;
			return array('UserUID' => $UserUID,'LoginID' => $LoginID,'EmailID' => $EmailID,'ContactNo' => $ContactNo,'AbstractorUID' => $AbstractorUID,'UserName' => $UserName,'RoleName' => $RoleName,'Password'=>$Password,'AuthenticationKey'=>$AuthenticationKey,'SessionValue'=>$SessionValue, 'RoleType'=>$Role->RoleType);
		}
		else
		{
			return false;
		}
	}

	function GetOrdersByUserID($AbstractorUID)
	{
		if (!empty($AbstractorUID)) 
		{
				$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'].','.$this->config->item('keywords')['Reopened Order'];

				$sql = "SELECT
							`CustomerName`,
							`OrderNumber`,
							`StatusName`,
							`torders`.`StatusUID`,
							`StatusColor`,
							`torderabstractor`.`AbstractorOrderUID` as OrderUID,
							`torders`.`OrderEntryDatetime` AS OrderEntryDatetime,
							`torders`.`OrderUID` AS tOrderUID,
							`morderpriority`.`PriorityName`,
							`morderpriority`.`TAT`,
							`morderpriority`.`PriorityUID`,
							`mproducts`.`ProductName`,
							`mproducts`.`ProductCode`,
							`msubproducts`.`SubProductCode`,
							`msubproducts`.`SubProductName`,
							`mordertypes`.`OrderTypeName`,
							`torders`.`LoanNumber`,
							DATE_FORMAT(
								torderabstractor.AssignedDateTime,
								'%m/%d/%Y %H:%i:%s'
							) AS AssignedDatetime,
							DATE_FORMAT(
								torders.OrderDueDateTime,
								'%m/%d/%Y %H:%i:%s'
							) AS OrderDueDateTime,
							DATE_FORMAT(
								torders.OrderEntryDatetime,
								'%m/%d/%Y %H:%i:%s'
							) AS OrderEntryDatetime,
							torders.PropertyAddress1,
							torders.PropertyAddress2,
							torders.PropertyZipcode,
							torders.PropertyStateCode,
							torders.PropertyCityName,
							torders.PropertyCountyName,
							tOrderClosing.SigningAddress1,
							tOrderClosing.SigningAddress2,
							tOrderClosing.SigningCityName,
							tOrderClosing.SigningStateCode,
							tOrderClosing.SigningCountyName,
							tOrderClosing.SigningZipCode,
							DATE_FORMAT(
								tOrderSchedule.SigningDateTime,
								'%m/%d/%Y %H:%i:%s'
							) AS SigningDateTime,
							morderstatus.StatusName,
							tOrderSchedule.ScheduleStatus,
							torderabstractor.OrderStatus
						FROM
							(`torders`)
						LEFT JOIN mordertypes ON `torders`.`OrderTypeUID` = `mordertypes`.`OrderTypeUID`
						LEFT JOIN `torderabstractor` ON `torders`.`OrderUID` = `torderabstractor`.`OrderUID` 
						LEFT JOIN `morderpriority` ON `morderpriority`.`PriorityUID` = `torders`.`PriorityUID`
						LEFT JOIN `mcustomers` ON `mcustomers`.`CustomerUID` = `torders`.`CustomerUID`
						LEFT JOIN `msubproducts` ON `msubproducts`.`SubProductUID` = `torders`.`SubProductUID`
						LEFT JOIN `mproducts` ON `mproducts`.`ProductUID` = `msubproducts`.`ProductUID`
						LEFT JOIN `morderstatus` ON `morderstatus`.`StatusUID` = `torders`.`StatusUID`
						LEFT JOIN `torderpropertyroles` ON `torderpropertyroles`.`OrderUID` = `torders`.`OrderUID`
						LEFT JOIN `tOrderSchedule` ON `tOrderSchedule`.`AbstractorOrderUID` = `torderabstractor`.`AbstractorOrderUID`
						LEFT JOIN `tOrderClosing` ON `tOrderClosing`.`ScheduleUID` = `tOrderSchedule`.`ScheduleUID`

						WHERE
							`torders`.`StatusUID` IN (".$statuses.")
						AND `torderabstractor`.`OrderStatus` != 5 AND `torderabstractor`.`AbstractorUID` = ".$AbstractorUID."

						
						GROUP BY
							`OrderUID`
						ORDER BY
							FIELD(
								`torders`.`PriorityUID`,
								3,
								1
							) DESC,
							`torders`.`OrderEntryDatetime` ASC";		

			$checkorders = $this->db->query($sql)->result_array();
			$my_orders = [];
			if(count($checkorders) > 0)
			{
				foreach ($checkorders as $key => $value) {
					$my_orders[] = $value;
					$Borrower = $this->db->query("SELECT  CONCAT_WS(' ', `torderpropertyroles`.`PRName` ) AS BorrowerName,torderpropertyroles.PRCellNumber AS CellNumber,torderpropertyroles.PRHomeNumber AS HomeNumber,torderpropertyroles.PRWorkNumber AS WorkNumber from torderpropertyroles WHERE torderpropertyroles.OrderUID=".$value['tOrderUID'])->result_array();
					if ($Borrower) {
						$my_orders[$key]['Borrower'] = $Borrower;
					}else{
						$my_orders[$key]['Borrower'] = '';
					}
				}
				 
			} 
			else 
			{
				$my_orders = array();
			}
		}
		else
		{
			$my_orders = array();
		}
		return $my_orders;
	}

	function GetOrdersForQuery($post, $AbstractorUID=0)
	{
			
		$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'].','.$this->config->item('keywords')['Reopened Order'];
			$my_orders = array();

		$Where_Abst = "";
		if (!empty($AbstractorUID)) 
		{
			$Where_Abst = "	AND `torderabstractor`.`AbstractorUID` = ".$AbstractorUID."";			
		}
		$like = "";
		if (!empty($post['search_value'])) {
			foreach ($post['column_search'] as $key => $item) { /* loop column*/
				/* if datatable send POST for search*/
				if ($key === 0) { /* first loop*/
					$like .= " AND ( ".$item." LIKE '%".$post['search_value']."%' ";
				} else {
					$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
				}
			}
			$like .= ") ";
		}

		$sql = "SELECT
		`CustomerName`,
		`OrderNumber`,
		`StatusName`,
		`torders`.`StatusUID`,
		`StatusColor`,
		`torderabstractor`.`AbstractorOrderUID` as OrderUID,
		`torderabstractor`.`OrderUID` as MainOrderUID,
		`torders`.`OrderEntryDatetime` AS OrderEntryDatetime,
		`morderpriority`.`PriorityName`,
		`morderpriority`.`TAT`,
		`morderpriority`.`PriorityUID`,
		`mproducts`.`ProductName`,
		`mproducts`.`ProductCode`,
		`msubproducts`.`SubProductCode`,
		`msubproducts`.`SubProductName`,
		DATE_FORMAT(
		torderabstractor.AssignedDateTime,
		'%m/%d/%Y %H:%i:%s'
		) AS AssignedDatetime,
		DATE_FORMAT(
		torders.OrderDueDateTime,
		'%m/%d/%Y %H:%i:%s'
		) AS OrderDueDateTime,
		DATE_FORMAT(
		torders.OrderEntryDatetime,
		'%m/%d/%Y %H:%i:%s'
		) AS OrderEntryDatetime,
		torders.PropertyAddress1,
		torders.PropertyAddress2,
		torders.PropertyZipcode,
		torders.PropertyStateCode,
		torders.PropertyCityName,
		torders.PropertyCountyName,
		tOrderClosing.SigningAddress1,
		tOrderClosing.SigningAddress2,
		tOrderClosing.SigningCityName,
		tOrderClosing.SigningStateCode,
		tOrderClosing.SigningCountyName,
		tOrderClosing.SigningZipCode,
		DATE_FORMAT(
		tOrderSchedule.SigningDateTime,
		'%m/%d/%Y %H:%i:%s'
		) AS SigningDateTime,
		torderpropertyroles.IsMailingAddress,
		torderpropertyroles.IsSigningAddress,
		morderstatus.StatusName,
		tOrderSchedule.ScheduleStatus,
		torderabstractor.OrderStatus,
		mtemplates.TemplateName,
		mordertypes.OrderTypeName,
		torders.LoanNumber
		FROM
		(`torders`)
		LEFT JOIN mordertypes ON `torders`.`OrderTypeUID` = `mordertypes`.`OrderTypeUID`
		JOIN `torderabstractor` ON `torders`.`OrderUID` = `torderabstractor`.`OrderUID` AND torderabstractor.OrderStatus != 5
		LEFT JOIN `morderpriority` ON `morderpriority`.`PriorityUID` = `torders`.`PriorityUID`
		LEFT JOIN `mcustomers` ON `mcustomers`.`CustomerUID` = `torders`.`CustomerUID`
		LEFT JOIN `msubproducts` ON `msubproducts`.`SubProductUID` = `torders`.`SubProductUID`
		LEFT JOIN `mproducts` ON `mproducts`.`ProductUID` = `msubproducts`.`ProductUID`
		LEFT JOIN `morderstatus` ON `morderstatus`.`StatusUID` = `torders`.`StatusUID`
		LEFT JOIN `mtemplates` ON `mtemplates`.`TemplateUID` = `torders`.`TemplateUID`
		LEFT JOIN `torderpropertyroles` ON `torderpropertyroles`.`OrderUID` = `torders`.`OrderUID`
		JOIN `tOrderSchedule` ON `tOrderSchedule`.`AbstractorOrderUID` = `torderabstractor`.`AbstractorOrderUID`
		JOIN `tOrderSign` ON `tOrderSign`.`ScheduleUID` = `tOrderSchedule`.`ScheduleUID`
		LEFT JOIN `tOrderClosing` ON `tOrderClosing`.`ScheduleUID` = `tOrderSchedule`.`ScheduleUID`
		WHERE
		`torders`.`StatusUID` IN (".$statuses.")
		" . $like . " " . $Where_Abst . " 
		GROUP BY
		`OrderNumber`
		ORDER BY
		FIELD(
		`torders`.`PriorityUID`,
		3,
		1
		) DESC,
		`torders`.`OrderEntryDatetime` ASC";		
		$checkorders = $this->db->query($sql)->result_array();

		$my_orders = [];
		if(count($checkorders) > 0)
		{
			foreach ($checkorders as $key => $value) {
				$my_orders[] = $value;
				$Borrower = $this->db->query("SELECT  CONCAT_WS(' ', `torderpropertyroles`.`PRName` ) AS BorrowerName,torderpropertyroles.PRCellNumber AS CellNumber,torderpropertyroles.PRHomeNumber AS HomeNumber,torderpropertyroles.PRWorkNumber AS WorkNumber from torderpropertyroles WHERE torderpropertyroles.OrderUID=".$value['MainOrderUID'])->result_array();
				if ($Borrower) {
					$my_orders[$key]['Borrower'] = $Borrower;
				}else{
					$my_orders[$key]['Borrower'] = '';
				}
				$MailingAddress = $this->GetMailingAddressDetails($value['MainOrderUID']);
				if ($MailingAddress) {
					$my_orders[$key]['MailingAddress'] = $MailingAddress;
				}else{
					$my_orders[$key]['MailingAddress'] = '';
				}
				$SigningAddress = $this->GetSigningAddressDetails($value['MainOrderUID']);
				if ($SigningAddress) {
					$my_orders[$key]['SigningAddress'] = $SigningAddress;
				}else{
					$my_orders[$key]['SigningAddress'] = '';
				}
			}

		} 
		else 
		{
			$my_orders = array();
		}
		return $my_orders;
}
  /**
        *@description Function to getOrderDetailsByOrderNumber
        * @param $query
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @since 13/08/2020 
        *
    */ 
 function GetOrderDetailsByOrderNumber($Getpost){

 	$this->db->select('*');
 	$this->db->from('torders');
 	$this->db->where('OrderNumber',$Getpost);
 	$query = $this->db->get()->row();
 	return $query;

 }

 /**
        *@description Function to GetOrderDetaills
        * @param $query
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @since 13/08/2020 
        *
    */
function GetOrderDetaillsByNew($GetAbstractorUID,$post)
{ 

	$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'].','.$this->config->item('keywords')['Reopened Order'].','.$this->config->item('keywords')['Cancelled'].','.$this->config->item('keywords')['Draft Complete'].','.$this->config->item('keywords')['Order Completed'].','.$this->config->item('keywords')['Billed'].','.$this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Clarified'].','.$this->config->item('keywords')['Raised Special Case'].','.$this->config->item('keywords')['Exception Clarified'].','.$this->config->item('keywords')['Exception Raised'].','.$this->config->item('keywords')['Flood Api'];


     $like = "";
		if (!empty($post['search_value'])) {
			foreach ($post['column_search'] as $key => $item) { /* loop column*/
				/* if datatable send POST for search*/
				if ($key === 0) { /* first loop*/
					$like .= " AND ( ".$item." LIKE '%".$post['search_value']."%' ";
				} else {
					$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
				}
			}
			$like .= ") ";
		}

	$sql = "SELECT
		`CustomerName`,
		`OrderNumber`,
		`morderstatus`.`StatusName`,
		`torders`.`StatusUID`,
		'#EC0808' AS StatusColor,
		`torders`.`OrderEntryDatetime` AS OrderEntryDatetime,
		`torders`.`OrderUID` AS MainOrderUID,
		`morderpriority`.`PriorityName`,
		`morderpriority`.`TAT`,
		`morderpriority`.`PriorityUID`,
		`mproducts`.`ProductName`,
		`mproducts`.`ProductCode`,
		`msubproducts`.`SubProductCode`,
		`msubproducts`.`SubProductName`,
		`torders`.`PropertyAddress1` AS PropertyAddress1,
		`torders`.`PropertyAddress2` AS PropertyAddress2,
		`torders`.`PropertyZipcode` AS PropertyZipcode,
		`torders`.`PropertyStateCode` AS PropertyStateCode,
		`torders`.`PropertyCityName` AS PropertyCityName,
		`torders`.`PropertyCountyName` AS PropertyCountyName, 
		`morderstatus`.`StatusName` AS ScheduleStatus,
		`torders`.`StatusUID` AS OrderStatus,
		`mtemplates`.TemplateName,
		`mordertypes`.`OrderTypeName`,
		`torders`.`LoanNumber`
		FROM (`torders`)
		LEFT JOIN mordertypes ON `torders`.`OrderTypeUID` = `mordertypes`.`OrderTypeUID`
		LEFT JOIN `morderpriority` ON `morderpriority`.`PriorityUID` = `torders`.`PriorityUID`
		LEFT JOIN `mcustomers` ON `mcustomers`.`CustomerUID` = `torders`.`CustomerUID`
		LEFT JOIN `msubproducts` ON `msubproducts`.`SubProductUID` = `torders`.`SubProductUID`
		LEFT JOIN `mproducts` ON `mproducts`.`ProductUID` = `msubproducts`.`ProductUID`
		LEFT JOIN `morderstatus` ON `morderstatus`.`StatusUID` = `torders`.`StatusUID`
		LEFT JOIN `mtemplates` ON `mtemplates`.`TemplateUID` = `torders`.`TemplateUID`
		
		WHERE
		`torders`.`StatusUID` IN (".$statuses.") " . $like . "
		 ";

		$checkorders = $this->db->query($sql)->result_array();
		// $MainOrderUID = $checkorders->tOrderUID;
		

		$my_orders = [];
		if(count($checkorders) > 0)
		{
			foreach ($checkorders as $key => $value) {
				$my_orders[] = $value;
				$Borrower = $this->db->query("SELECT  CONCAT_WS(' ', `torderpropertyroles`.`PRName` ) AS BorrowerName,torderpropertyroles.PRCellNumber AS CellNumber,torderpropertyroles.PRHomeNumber AS HomeNumber,torderpropertyroles.PRWorkNumber AS WorkNumber from torderpropertyroles WHERE torderpropertyroles.OrderUID=".$value['MainOrderUID'])->result_array();
				if ($Borrower) {
					$my_orders[$key]['Borrower'] = $Borrower;
				}else{
					$my_orders[$key]['Borrower'] = '';
				}
				$MailingAddress = $this->GetMailingAddressDetails($value['MainOrderUID']);
				if ($MailingAddress) {
					$my_orders[$key]['MailingAddress'] = $MailingAddress;
				}else{
					$my_orders[$key]['MailingAddress'] = '';
				}
				$SigningAddress = $this->GetSigningAddressDetails($value['MainOrderUID']);
				if ($SigningAddress) {
					$my_orders[$key]['SigningAddress'] = $SigningAddress;
				}else{
					$my_orders[$key]['SigningAddress'] = '';
				}


			}

		} 
		else 
		{
			$my_orders = array();
		}
		// // return $my_orders;

		// $MailingAddress = $this->GetMailingAddressDetails($GetAbstractorUID);

 
			return $my_orders;

}

/**
        *@description Function to getMailingAddressDetails
        * @param $query
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @since 14/08/2020 
        *
    */ 
function GetMailingAddressDetails($GetAbstractorUID){

	$GetMailStatus =$this->db->query("select * FROM torderpropertyroles  where torderpropertyroles.OrderUID ='".$GetAbstractorUID."' ")->result();
	$PropertyRoleDetails =[];
	foreach ($GetMailStatus as $key => $value) {

		if($value->IsMailingAddress == "others"){
          
			$row = array(
				'MailingAddress1' => $value->MailingAddress1,
				'MailingAddress2' => $value->MailingAddress2,
				'MailingCityName' => $value->MailingCityName,
				'MailingStateCode' => $value->MailingStateCode,
				'MailingCountyName' => $value->MailingCountyName,
				'MailingZipCode' =>$value->MailingZipCode,

			);
			array_push($PropertyRoleDetails,$row);
		}
		else{
		$GetMailOrders =$this->db->query("select * FROM torders  where torders.OrderUID ='".$GetAbstractorUID."' ")->result_array();

			$row = array(
				'MailingAddress1' => $GetMailOrders[0]['PropertyAddress1'],
				'MailingAddress2' =>  $GetMailOrders[0]['PropertyAddress2'],
				'MailingCityName' =>  $GetMailOrders[0]['PropertyCityName'],
				'MailingStateCode' =>  $GetMailOrders[0]['PropertyStateCode'],
				'MailingCountyName' => $GetMailOrders[0]['PropertyCountyName'],
				'MailingZipCode' =>  $GetMailOrders[0]['PropertyZipcode'],

			);
			array_push($PropertyRoleDetails,$row);
			
		}
	}

	return $PropertyRoleDetails;
}

	/**
        *@description Function to getSigningAddressDetails
        * @param $query
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @since 19/08/2020 
        *
    */ 

    function GetSigningAddressDetails($GetAbstractorUID){

	$GetSignStatus =$this->db->query("select * FROM torderpropertyroles  where torderpropertyroles.OrderUID ='".$GetAbstractorUID."' GROUP BY 'torderpropertyroles.SigningAddress1' ")->result();
	$SigningAddressDetails =[];
	foreach ($GetSignStatus as $key => $value) {

		if($value->IsSigningAddress == "others"){
          
			$row = array(
				'SigningAddress1' => $value->SigningAddress1,
				'SigningAddress2' => $value->SigningAddress2,
				'SigningCityName' => $value->SigningCityName,
				'SigningStateCode' => $value->SigningStateCode,
				'SigningCountyName' => $value->SigningCountyName,
				'SigningZipCode' =>$value->SigningZipCode,

			);
			array_push($SigningAddressDetails,$row);
		}
		elseif($value->IsSigningAddress == "mailing"){

			if($value->IsMailingAddress == "other" && $value->IsSigningAddress == "mailing")
          {
			$row = array(
				'SigningAddress1' => $value->MailingAddress1,
				'SigningAddress2' => $value->MailingAddress2,
				'SigningCityName' => $value->MailingCityName,
				'SigningStateCode' => $value->MailingStateCode,
				'SigningCountyName' => $value->MailingCountyName,
				'SigningZipCode' =>$value->MailingZipCode,

			);
			array_push($SigningAddressDetails,$row);
		}
		else{
			$GetSignOrders =$this->db->query("select * FROM torders  where torders.OrderUID ='".$GetAbstractorUID."' ")->result_array();
			$row = array(
				'SigningAddress1' => $GetSignOrders[0]['PropertyAddress1'],
				'SigningAddress2' =>  $GetSignOrders[0]['PropertyAddress2'],
				'SigningCityName' =>  $GetSignOrders[0]['PropertyCityName'],
				'SigningStateCode' =>  $GetSignOrders[0]['PropertyStateCode'],
				'SigningCountyName' => $GetSignOrders[0]['PropertyCountyName'],
				'SigningZipCode' =>  $GetSignOrders[0]['PropertyZipcode'],

			);
			array_push($SigningAddressDetails,$row);

		}

		}
		else{
		$GetSignOrders =$this->db->query("select * FROM torders  where torders.OrderUID ='".$GetAbstractorUID."' ")->result_array();

			$row = array(
				'SigningAddress1' => $GetSignOrders[0]['PropertyAddress1'],
				'SigningAddress2' =>  $GetSignOrders[0]['PropertyAddress2'],
				'SigningCityName' =>  $GetSignOrders[0]['PropertyCityName'],
				'SigningStateCode' =>  $GetSignOrders[0]['PropertyStateCode'],
				'SigningCountyName' => $GetSignOrders[0]['PropertyCountyName'],
				'SigningZipCode' =>  $GetSignOrders[0]['PropertyZipcode'],

			);
			array_push($SigningAddressDetails,$row);
			
		}
	}

	return $SigningAddressDetails;
}




	function get_order_assigned_users($data)
	{

		$Workflows = $this->get_all_Workflows();
		$is_vendor_login = $this->common_model->is_vendorlogin();
		$ret_data = [];

		foreach ($Workflows as $key => $Workflow) {

			$query=$this->db->query("SELECT OrderUID,WorkflowModuleUID,LoginID,SendToVendor,torderassignment.VendorUID as  AssignedVendorUID,VendorName,musers.VendorUID as VendorUID FROM `torderassignment` LEFT JOIN musers on musers.UserUID = torderassignment.AssignedToUserUID LEFT JOIN mvendors ON mvendors.VendorUID = torderassignment.VendorUID WHERE `OrderUID` = '".$data['OrderUID']."' AND (AssignedToUserUID IS NOT NULL OR SendToVendor = '1') AND `WorkflowModuleUID` = '".$Workflow['WorkflowModuleUID']."' ");
			$result = $query->row();




			if(count($result) > 0){

				$ret_data = '--';
				if($is_vendor_login){
					
					
					if($result->SendToVendor == '0' && ($result->VendorUID == ''|| $result->VendorUID == '0') ){
						$ret_data = '--';
					}else if( $result->AssignedVendorUID == $result->VendorUID){	

						$ret_data = $result->LoginID;
					}


				}else{	
					if($result->SendToVendor == '1' && $result->AssignedVendorUID != ''){

						$ret_data = strtok($result->VendorName, ' ');
					}else{
						
						$ret_data = $result->LoginID;
					}
				}

			}else{
				$ret_data = '--';
			}

			$ret[] =  $ret_data;

		}

		return  implode(" / ",$ret);

	}

	function get_all_Workflows(){

		$query = $this->db->get('mworkflowmodules');
		return $query->result_array();
	}

	function GetCompletedOrderListByUserID($AbstractorUID)
	{
	    $query=[];  
	  
	    $this->db->select ('CustomerName,CustomerNumber,OrderNumber,LoanNumber,StatusName,StatusColor,torderabstractor.AbstractorOrderUID as OrderUID,torders.PriorityUID,PriorityName,PropertyAddress1,PropertyZipcode,AttentionName,msubproducts.SubProductName, mproducts.ProductName,mordertypes.OrderTypeName,torders.PropertyCityName,torders.PropertyStateCode,torders.PropertyCountyName,tOrderClosing.SigningAddress1,tOrderClosing.SigningAddress2,tOrderClosing.SigningCityName,tOrderClosing.SigningStateCode,tOrderClosing.SigningCountyName,tOrderClosing.SigningZipCode,DATE_FORMAT(tOrderSchedule.SigningDateTime,("%m/%d/%Y %H:%i:%s")) AS SigningDateTime, torderabstractor.OrderStatus, torderabstractor.OrderUID as MainOrderUID');
	    $this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m/%d/%Y %H:%i:%s") as OrderEntryDatetime', FALSE);
	    $this->db->select('DATE_FORMAT(torders.OrderDueDatetime, "%m/%d/%Y %H:%i:%s") as OrderDueDatetime', FALSE);
	    $this->db->select('DATE_FORMAT(torders.OrderCompleteDateTime, "%m/%d/%Y %H:%i:%s") as OrderCompleteDateTime', FALSE);    
	    $this->db->from ('torderabstractor');
	    $this->db->join ('torders','torderabstractor.OrderUID = torders.OrderUID');
	    $this->db->join ('tOrderSchedule', 'tOrderSchedule.AbstractorOrderUID = torderabstractor.AbstractorOrderUID');
	    $this->db->join ('tOrderSign', 'tOrderSign.ScheduleUID = tOrderSchedule.ScheduleUID');
	    $this->db->join ('tOrderClosing','tOrderClosing.ScheduleUID = tOrderSchedule.ScheduleUID','left');
	    $this->db->join ('morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );  
	    $this->db->join ('mcustomers', 'mcustomers.CustomerUID = torders.CustomerUID' , 'left' );
	    $this->db->join ('msubproducts', 'msubproducts.SubProductUID = torders.SubProductUID' , 'left' );
	    $this->db->join ('mproducts', 'mproducts.ProductUID = msubproducts.ProductUID' , 'left' );    
	    $this->db->join ('morderstatus', 'morderstatus.StatusUID = torders.StatusUID' , 'left' );
	    $this->db->join ('mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
	    $this->db->where('tOrderSign.SigningStatus = "Sign"'); 
	    $this->db->where('tOrderSign.AbstractorUID',$AbstractorUID);
	    
	    $this->db->group_by('OrderUID');
	    $query = $this->db->get();
	    $checkorders = $query->result_array();
	    if ($checkorders) {
	    	foreach ($checkorders as $key => $value) {
	    		$my_orders[] = $value;
	    		$Borrower = $this->db->query("SELECT CONCAT_WS(' ', `torderpropertyroles`.`PRName` ) AS BorrowerName,torderpropertyroles.PRCellNumber AS CellNumber,torderpropertyroles.PRHomeNumber AS HomeNumber,torderpropertyroles.PRWorkNumber AS WorkNumber from torderpropertyroles WHERE torderpropertyroles.OrderUID=".$value['MainOrderUID'])->result_array();
					if ($Borrower) {
						$my_orders[$key]['Borrower'] = $Borrower;
					}else{
						$my_orders[$key]['Borrower'] = '';
					}
	    	}
	    }else{
	    	$my_orders = array();
	    }
	    return $my_orders;
	}

	function completedOrderListByOrderUID($OrderUID)
	{
	    $query=[];  
	    $status[0] = $this->config->item('keywords')['Review Complete'];
	    $status[1] = $this->config->item('keywords')['Order Completed'];
	  
	    $this->db->select ('CustomerName,CustomerNumber,OrderNumber,LoanNumber,StatusName,StatusColor,torders.OrderUID,torders.PriorityUID,PriorityName,PropertyAddress1,PropertyZipcode,AttentionName,msubproducts.SubProductName, mproducts.ProductName,mordertypes.OrderTypeName,torders.PropertyCityName,torders.PropertyStateCode,torders.PropertyCountyName');
	    $this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m/%d/%Y %H:%i:%s") as OrderEntryDatetime', FALSE);
	    $this->db->select('DATE_FORMAT(torders.OrderDueDatetime, "%m/%d/%Y %H:%i:%s") as OrderDueDatetime', FALSE);
	    $this->db->select('DATE_FORMAT(torders.OrderCompleteDateTime, "%m/%d/%Y %H:%i:%s") as OrderCompleteDateTime', FALSE);    
	    $this->db->from ('torders');
	    $this->db->join ('morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );  
	    $this->db->join ('mcustomers', 'mcustomers.CustomerUID = torders.CustomerUID' , 'left' );
	    $this->db->join ('msubproducts', 'msubproducts.SubProductUID = torders.SubProductUID' , 'left' );
	    $this->db->join ('mproducts', 'mproducts.ProductUID = msubproducts.ProductUID' , 'left' );    
	    $this->db->join ('morderstatus', 'morderstatus.StatusUID = torders.StatusUID' , 'left' );
	    $this->db->join ('mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
	    $this->db->join ('torderassignment','torderassignment.OrderUID = torders.OrderUID','left');
	    $this->db->where_in('torders.StatusUID',$status); 
	    $this->db->where('torders.OrderUID',$OrderUID);
	    $this->db->group_by('OrderUID');
	    $query = $this->db->get();
	    return $query->result();
	}

	function GetOrderDetailsByOrderUID($OrderUID,$AbstractorUID,$OrderUIDMain)
	{
          if(!empty($OrderUID)){
		   $this->db->select ( 'torderabstractor.OrderUID as MainOrderUID,torderabstractor.AbstractorOrderUID as OrderUID,torders.OrderNumber,torders.LoanNumber,torders.LoanAmount,torders.PropertyAddress1,torders.PropertyAddress2,torders.PropertyCityName,torders.PropertyCountyName,torders.PropertyStateCode,torders.PropertyZipcode,morderstatus.StatusName,morderstatus.StatusColor,DATE_FORMAT(torderabstractor.AssignedDateTime, ("%m/%d/%Y %H:%i:%s") ) as AssignedDatetime, DATE_FORMAT(torders.OrderDueDateTime, ("%m/%d/%Y %H:%i:%s") ) as OrderDueDateTime, DATE_FORMAT(torders.OrderEntryDatetime, ("%m/%d/%Y %H:%i:%s") ) as OrderEntryDatetime,torderabstractor.OrderStatus,tOrderSchedule.ScheduleStatus'); 
			}else{
			$this->db->select ( 'torders.OrderUID as MainOrderUID,torders.OrderNumber,torders.LoanNumber,torders.LoanAmount,torders.PropertyAddress1,torders.PropertyAddress2,torders.PropertyCityName,torders.PropertyCountyName,torders.PropertyStateCode,torders.PropertyZipcode,morderstatus.StatusName,morderstatus.StatusColor,DATE_FORMAT(torderabstractor.AssignedDateTime, ("%m/%d/%Y %H:%i:%s") ) as AssignedDatetime, DATE_FORMAT(torders.OrderDueDateTime, ("%m/%d/%Y %H:%i:%s") ) as OrderDueDateTime, DATE_FORMAT(torders.OrderEntryDatetime, ("%m/%d/%Y %H:%i:%s") ) as OrderEntryDatetime,torderabstractor.OrderStatus,tOrderSchedule.ScheduleStatus'); 	
			}
			$this->db->from ( 'torders' );
			$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID' , 'left' );
			$this->db->join ( 'msubproducts', 'torders.SubProductUID = msubproducts.SubProductUID' , 'left' );
			$this->db->join ( 'mproducts', 'msubproducts.ProductUID = mproducts.ProductUID' , 'left' );
			$this->db->join ( 'mtemplates', 'torders.TemplateUID = mtemplates.TemplateUID' , 'left' );
			$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
			$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );
			$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID' , 'left' );
			$this->db->join ( 'torderabstractor', 'torderabstractor.orderUID = torders.OrderUID' , 'left' );
			$this->db->join('tOrderSchedule','tOrderSchedule.OrderUID = torders.OrderUID','left');
			if(!empty($OrderUID)){
			$this->db->where ('torderabstractor.AbstractorOrderUID',$OrderUID);
			$this->db->where ('torderabstractor.AbstractorUID',$AbstractorUID);
			}else{
			$this->db->where ('torders.OrderUID',$OrderUIDMain);	
			}
			
			$query = $this->db->get();
			$SummaryDetails = $query->row();

			$MainOrderUID = $SummaryDetails->MainOrderUID;

			$this->db->select ('CONCAT_WS(" ", `torderpropertyroles`.`PRName` ) AS BorrowerName,torderpropertyroles.MailingAddress1,torderpropertyroles.MailingAddress2,torderpropertyroles.MailingCityName,torderpropertyroles.MailingStateCode,torderpropertyroles.MailingCountyName,torderpropertyroles.MailingZipCode ,torderpropertyroles.PRHomeNumber AS HomeNumber,torderpropertyroles.PRWorkNumber AS WorkNumber,torderpropertyroles.PRCellNumber AS CellNumber'); 
			$this->db->from ( 'torderpropertyroles' );
			$this->db->join ( 'torders','torderpropertyroles.OrderUID = torders.OrderUID' , 'left');
			$this->db->join ( 'mpropertyroles', 'mpropertyroles.PropertyRoleUID = torderpropertyroles.PropertyRoleUID' , 'left' );
			$this->db->join ( 'tOrderClosing', 'tOrderClosing.OrderUID = torders.OrderUID' , 'left' );
			$this->db->where ('torders.OrderUID',$MainOrderUID);
			$this->db->group_by('PRName');
			$query = $this->db->get();
			$PropertyRoleDetails =  $query->result_array();
          
			$IsMaill = $this->GetMailingAddressDetails($MainOrderUID);

				
			// $query = $this->db->query("SELECT `tOrderClosing`.`SigningAddress1`, `tOrderClosing`.`SigningAddress2`, `tOrderClosing`.`SigningCityName`, `tOrderClosing`.`SigningStateCode`, `tOrderClosing`.`SigningCountyName`, `tOrderClosing`.`SigningZipCode`, DATE_FORMAT(`tOrderSchedule`.`SigningDateTime`, '%m/%d/%Y %H:%i:%s') AS SigningDateTime FROM (`tOrderClosing`) LEFT JOIN `torders` ON `tOrderClosing`.`OrderUID` = `torders`.`OrderUID` LEFT JOIN `tOrderSchedule` ON `tOrderSchedule`.`OrderUID` = `torders`.`OrderUID` WHERE `torders`.`OrderUID` ='".$MainOrderUID."' AND `tOrderSchedule`.`ScheduleStatus`='Assign' GROUP BY `SigningAddress1` ORDER BY 'tOrderSchedule.SigningDateTime'");
			
			// $SigningDetails =  $query->result_array();

			$IsSign = $this->GetSigningAddressDetails($MainOrderUID);

			if(empty($IsSign)){	
			$IsSign = $this->GetSigningAddressDetails($OrderUIDMain);
			}

			$OrderDetails = array('SummaryDetails' => $SummaryDetails,'BorrowerDetails' =>$PropertyRoleDetails,'MailingAddress'=> $IsMaill,'SigningDetails' =>$IsSign);
			// $result = [];
			// foreach ($OrderDetails as $key => $value) {
			// 	$result[]=$value;
			// }
			return $OrderDetails;
	}
	/**
        *@Description Function to Roletype Based Order Search
        *
        * @param $query
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Role Based API Functionality 
        * @since 08/08/2020 
    */ 

function GetOrderDetailsByRole($OrderUID)
	{

          $this->db->select('*')->from('torderabstractor')->where('torderabstractor.AbstractorOrderUID',$OrderUID);
			$query = $this->db->get();
			$Order  =$query->row();
			$tOrderUID =$Order->OrderUID;

			$this->db->select ( 'torderabstractor.OrderUID As MainOrderUID,torderabstractor.AbstractorOrderUID as OrderUID,torders.OrderNumber,torders.LoanNumber,torders.LoanAmount,torders.PropertyAddress1,torders.PropertyAddress2,torders.PropertyCityName,torders.PropertyCountyName,torders.PropertyStateCode,torders.PropertyZipcode,morderstatus.StatusName,morderstatus.StatusColor,DATE_FORMAT(torderabstractor.AssignedDateTime, ("%m/%d/%Y %H:%i:%s") ) as AssignedDatetime, DATE_FORMAT(torders.OrderDueDateTime, ("%m/%d/%Y %H:%i:%s") ) as OrderDueDateTime, DATE_FORMAT(torders.OrderEntryDatetime, ("%m/%d/%Y %H:%i:%s") ) as OrderEntryDatetime,torderabstractor.OrderStatus' ); 
			$this->db->from ( 'torders' );
			$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID' , 'left' );
			$this->db->join ( 'msubproducts', 'torders.SubProductUID = msubproducts.SubProductUID' , 'left' );
			$this->db->join ( 'mproducts', 'msubproducts.ProductUID = mproducts.ProductUID' , 'left' );
			$this->db->join ( 'mtemplates', 'torders.TemplateUID = mtemplates.TemplateUID' , 'left' );
			$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
			$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );
			$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID' , 'left' );
			$this->db->join ( 'torderabstractor', 'torderabstractor.OrderUID = torders.OrderUID' , 'left' );
			$this->db->where ('torderabstractor.OrderUID',$tOrderUID);
			// $this->db->where ('torderabstractor.AbstractorUID',$AbstractorUID);

			$this->db->where('torders.OrderUID',$tOrderUID);
			$query = $this->db->get();
			$SummaryDetails = $query->row();

			$MainOrderUID = $SummaryDetails->MainOrderUID;

			$this->db->select ('CONCAT_WS(" ", `torderpropertyroles`.`PRName` ) AS BorrowerName,torderpropertyroles.PRHomeNumber AS HomeNumber,torderpropertyroles.PRWorkNumber AS WorkNumber,torderpropertyroles.PRCellNumber AS CellNumber'); 
			$this->db->from ( 'torderpropertyroles' );
			$this->db->join ( 'torders','torderpropertyroles.OrderUID = torders.OrderUID' , 'left');
			$this->db->join ( 'mpropertyroles', 'mpropertyroles.PropertyRoleUID = torderpropertyroles.PropertyRoleUID' , 'left' );
			// $this->db->join ( 'tOrderClosing', 'tOrderClosing.OrderUID = torders.OrderUID' , 'left' );
			$this->db->where ('torders.OrderUID',$MainOrderUID);
			$this->db->group_by('torderpropertyroles.PRName');
			$query = $this->db->get();
			$PropertyRoleDetails =  $query->result_array();

			$IsMaill = $this->GetMailingAddressDetails($MainOrderUID);

			// $this->db->select('*')->from('torderabstractor')->where('torderabstractor.AbstractorOrderUID',$OrderUID);
			// $query = $this->db->get();
			// $SignDetails  =$query->row();
			// $SignOrderUID =$SignDetails->OrderUID;

			// $query = $this->db->query("SELECT `tOrderClosing`.`SigningAddress1`, `tOrderClosing`.`SigningAddress2`, `tOrderClosing`.`SigningCityName`, `tOrderClosing`.`SigningStateCode`, `tOrderClosing`.`SigningCountyName`, `tOrderClosing`.`SigningZipCode`, DATE_FORMAT(`tOrderSchedule`.`SigningDateTime`, '%m/%d/%Y %H:%i:%s') AS SigningDateTime FROM (`tOrderClosing`) LEFT JOIN `torders` ON `tOrderClosing`.`OrderUID` = `torders`.`OrderUID` LEFT JOIN `tOrderSchedule` ON `tOrderSchedule`.`OrderUID` = `torders`.`OrderUID` WHERE `torders`.`OrderUID` =".$SignOrderUID);
			// $SigningDetails =  $query->result_array();
			$IsSign = $this->GetSigningAddressDetails($MainOrderUID);
			

			$OrderDetails = array('SummaryDetails' => $SummaryDetails,'BorrowerDetails' =>$PropertyRoleDetails,'MailingAddress'=> $IsMaill,'SigningDetails' =>$IsSign);
			// $result = [];
			// foreach ($OrderDetails as $key => $value) {
			// 	$result[]=$value;
			// }
			return $OrderDetails;
	}

	/**
        *@Description Function to OrderNumber Based Order Details
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version OrderNumber Based API Functionality 
        * @since 13/08/2020 
    */ 
	function GetOrderDetailsByRoleOrderUID($GetOrderUID)
	{ 

			$this->db->select ( 'torders.OrderUID as MainOrderUID, torders.OrderNumber,torders.LoanNumber,torders.LoanAmount,torders.PropertyAddress1,torders.PropertyAddress2,torders.PropertyCityName,torders.PropertyCountyName,torders.PropertyStateCode,torders.PropertyZipcode,morderstatus.StatusName,morderstatus.StatusColor, DATE_FORMAT(torders.OrderDueDateTime, ("%m/%d/%Y %H:%i:%s") ) as OrderDueDateTime, DATE_FORMAT(torders.OrderEntryDatetime, ("%m/%d/%Y %H:%i:%s") ) as OrderEntryDatetime' ); 
			$this->db->from ( 'torders' );
			$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID' , 'left' );
			$this->db->join ( 'msubproducts', 'torders.SubProductUID = msubproducts.SubProductUID' , 'left' );
			$this->db->join ( 'mproducts', 'msubproducts.ProductUID = mproducts.ProductUID' , 'left' );
			$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
			$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );
			$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID' , 'left' );

			$this->db->where('torders.OrderNumber',$GetOrderUID);
			$query = $this->db->get();
			$SummaryDetails = $query->row();

			

			$MainOrderUID = $SummaryDetails->MainOrderUID;

			$this->db->select ('CONCAT_WS(" ", `torderpropertyroles`.`PRName` ) AS BorrowerName,torderpropertyroles.PRHomeNumber AS HomeNumber,torderpropertyroles.PRWorkNumber AS WorkNumber,torderpropertyroles.PRCellNumber AS CellNumber'); 
			$this->db->from ( 'torderpropertyroles' );
			$this->db->join ( 'torders','torderpropertyroles.OrderUID = torders.OrderUID' , 'left');
			$this->db->join ( 'mpropertyroles', 'mpropertyroles.PropertyRoleUID = torderpropertyroles.PropertyRoleUID' , 'left' );
			$this->db->join ( 'tOrderClosing', 'tOrderClosing.OrderUID = torders.OrderUID' , 'left' );
			$this->db->where ('torders.OrderUID',$MainOrderUID);
			$this->db->group_by('torderpropertyroles.PRName');
			$query = $this->db->get();
			$PropertyRoleDetails =  $query->result_array();


			$IsMaill = $this->GetMailingAddressDetails($MainOrderUID);

			// $query = $this->db->query("SELECT `tOrderClosing`.`SigningAddress1`, `tOrderClosing`.`SigningAddress2`, `tOrderClosing`.`SigningCityName`, `tOrderClosing`.`SigningStateCode`, `tOrderClosing`.`SigningCountyName`, `tOrderClosing`.`SigningZipCode`, DATE_FORMAT(`tOrderSchedule`.`SigningDateTime`, '%m/%d/%Y %H:%i:%s') AS SigningDateTime FROM (`tOrderClosing`) LEFT JOIN `torders` ON `tOrderClosing`.`OrderUID` = `torders`.`OrderUID` LEFT JOIN `tOrderSchedule` ON `tOrderSchedule`.`OrderUID` = `torders`.`OrderUID` WHERE `torders`.`OrderUID` =".$MainOrderUID);
			// $SigningDetails =  $query->result_array();
			
			$IsSign = $this->GetSigningAddressDetails($MainOrderUID);

			$OrderDetails = array('SummaryDetails' => $SummaryDetails,'BorrowerDetails' =>$PropertyRoleDetails,'MailingAddress' =>$IsMaill,'SigningDetails' =>$IsSign);
			// $result = [];
			// foreach ($OrderDetails as $key => $value) {
			// 	$result[]=$value;
			// }
			return $OrderDetails;
	}




	function GetSearchListResults($searchKeywords)
	{
		$status = [];
		$status[] = $this->config->item('keywords')['New Order'];
		$status[] = $this->config->item('keywords')['Reopened Order'];
		$status[] = $this->config->item('keywords')['Order Assigned'];
		$status[] = $this->config->item('keywords')['Order Work In Progress'];
		$status[] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[] = $this->config->item('keywords')['Draft Complete'];
		$status[] = $this->config->item('keywords')['Review In Progress'];
		$status[] = $this->config->item('keywords')['Partial Review Complete'];
		$status[] = $this->config->item('keywords')['Review Complete'];
		$status[] = $this->config->item('keywords')['Exception Raised'];
		$status[] = $this->config->item('keywords')['Exception Clarified'];
		$status[] = $this->config->item('keywords')['Raised Special Case'];
		$status[] = $this->config->item('keywords')['Clarified'];
		$status[] = $this->config->item('keywords')['Order Exported'];
		$status[] = $this->config->item('keywords')['Order Completed'];
		$status[] = $this->config->item('keywords')['Cancelled'];
		$status[] = $this->config->item('keywords')['Billed'];

		$this->db->select ('*');
		$this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m/%d/%Y %H:%i:%s") as Date', FALSE);	 
		$this->db->from ( 'torders');
		$this->db->join ( 'torderpropertyroles', 'torderpropertyroles.OrderUID = torders.OrderUID' , 'left');
		$this->db->join ( 'mpropertyroles', 'mpropertyroles.PropertyRoleUID = torderpropertyroles.PropertyRoleUID' , 'left' );
		$this->db->join ( 'mcustomers', 'mcustomers.CustomerUID = torders.CustomerUID' , 'left' );
		$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID' , 'left' );
		$this->db->join ( 'mtemplates', 'mtemplates.TemplateUID = torders.TemplateUID' , 'left' );
		$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
		$this->db->join ( 'msubproducts', 'msubproducts.SubProductUID = torders.SubProductUID' , 'left' );
		$this->db->join ( 'mproducts', 'mproducts.ProductUID = msubproducts.ProductUID' , 'left' );
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );

		$this->db->where_in('torders.StatusUID', $status);
		if($searchKeywords){
			$this->db->where($this->_like_array($searchKeywords));
		}

		$this->db->group_by('torders.OrderUID');
		$query = $this->db->get();
		return $query->result();
	}

	function _like_array($query)
	{
		$array = "(`torders`.`OrderNumber` LIKE '%$query%' OR `msubproducts`.`SubProductName` LIKE '%$query%' OR `mordertypes`.`OrderTypeName` LIKE '%$query%' OR `morderpriority`.`PriorityName` LIKE '%$query%' OR `mcustomers`.`CustomerName` LIKE '%$query%' OR `torders`.`OrderNumber` LIKE '%$query%' OR `torders`.`AltORderNumber` LIKE '%$query%' OR `torders`.`LoanNumber` LIKE '%$query%' OR `torders`.`LoanAmount` LIKE '%$query%' OR `torders`.`CustomerRefNum` LIKE '%$query%' OR `torders`.`PropertyAddress1` LIKE '%$query%' OR `torders`.`PropertyAddress2` LIKE '%$query%' OR `torders`.`PropertyCityName`  LIKE '%$query%' OR `torders`.`PropertyStateCode` LIKE '%$query%' OR `torders`.`PropertyZipcode` LIKE '%$query%' OR `torders`.`OrderInfoNotes` LIKE '%$query%' OR `mtemplates`.`TemplateName` LIKE '%$query%' OR `mtemplates`.`TemplateCode` LIKE '%$query%' OR `morderstatus`.`StatusName` LIKE '%$query%' OR `morderstatus`.`StatusUID` LIKE '%$query%' OR `torders`.`APN` LIKE '%$query%' OR `torders`.`EmailReportTo` LIKE '%$query%' OR `torders`.`OrderEntryDatetime` LIKE '%$query%' OR `torders`.`PropertyCountyName` LIKE '%$query%' OR `torderpropertyroles`.`PRName` LIKE '$query')";
		return $array;
	}

	function checkloginexist($loginid)
    {
    	$query = $this->db->query("SELECT LoginID,UserEmailID FROM musers WHERE LoginID ='$loginid' OR UserEmailID = '$loginid' ");
    	if($query->num_rows() > 0)
        {
            $result = $query->result();
			return $result;
        }
        else{
            return false;
        }
    }

    function SaveDynamicAccessCode($Email,$DynamicAccessCode)
	{
		$query = $this->db->query("UPDATE musers SET DynamicAccessCode = '$DynamicAccessCode' WHERE UserEmailID ='$Email' ");
		if($this->db->affected_rows() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function GetUserName($loggedid)
	{
	   $this->db->where('UserUID',$loggedid);
	   $q = $this->db->get('musers')->row();
	   return $q->UserName;
	}

	function CheckAccessCode($accesscode)
    {
    	$query = $this->db->query("SELECT * FROM musers WHERE DynamicAccessCode ='$accesscode' ");
    	if($query->num_rows() > 0)
        {
			return true;
        }
        else{
            return false;
        }
    }

    function updatePassword($accesscode,$cpassword)
	{
		$Roles = $this->db->query("SELECT RoleUID FROM musers WHERE DynamicAccessCode =".$accesscode)->row();
	    $Role = $this->db->query("SELECT RoleType FROM mroles WHERE RoleUID =".$Roles->RoleUID)->row();
	    $RoleType = $Role->RoleType;
	    $this->db->select('PasswordExpire');
	    $this->db->from('morganizations');
	    $data = $this->db->get()->row();
	    $days = $data->PasswordExpire;
	    if(in_array($RoleType, array(8,13,14,15)))
	    {
	        $LoginExpiry = NULL;
	    }else{
	        $LoginExpiry = date("Y-m-d H:i:s", time() + $days*24*60*60);
	    }
		$fieldArray = array(
	          "DynamicAccessCode"=>'',  
	          "Password"=>$cpassword,
	          "LoginExpiry"=>$LoginExpiry
	        );
		$this->db->where(array("DynamicAccessCode"=>$accesscode));    
        $result = $this->db->update('musers', $fieldArray);
		if($this->db->affected_rows() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	  function CheckOldPassword($oldpassword,$UserUID)
	  {
	    $query = $this->db->query("SELECT * FROM musers WHERE UserUID ='$UserUID' ");

	    if($query->num_rows() > 0)
	    {
	      $result = $query->result();
	      foreach($result as $data)
	      {
	        $Pass = $data->Password;
	      }
	      $EncPassword = md5($oldpassword);
	      if($Pass == $EncPassword)
	      {
	        
	        return true;
	      }
	      else
	      {
	        
	        return false;
	      }
	    }
	  }

	function changePassword($UserUID,$cpassword)
	{
      $Roles = $this->db->query("SELECT RoleUID FROM musers WHERE UserUID =".$UserUID)->row();
      $Role = $this->db->query("SELECT RoleType FROM mroles WHERE RoleUID =".$Roles->RoleUID)->row();
      $RoleType = $Role->RoleType;
      $this->db->select('PasswordExpire');
      $this->db->from('morganizations');
      $data = $this->db->get()->row();
      $days = $data->PasswordExpire;
      if(in_array($RoleType, array(8,13,14,15))){
       	  $LoginExpiry = NULL;
      }else{
          $LoginExpiry = date("Y-m-d H:i:s", time() + $days*24*60*60);
      }
	  $query = $this->db->query("UPDATE musers SET Password = '$cpassword', FirstLogin='0',LoginExpiry='$LoginExpiry' WHERE UserUID ='$UserUID' ");
	    if($this->db->affected_rows() > 0){
	      return true;
	    }
	    else{
	      return false;
	    }
    }

    function Checkloginidexist($loginid,$UserUID)
    {
    	$query = $this->db->query("SELECT * FROM musers WHERE LoginID ='$loginid' AND UserUID <> '$UserUID'");

    	if($query->num_rows() > 0)
        {
			return true;
        }
        else{
            return false;
        }
    }

    function Checkemailexist($UserEmailID,$UserUID)
    {
    	$query = $this->db->query("SELECT * FROM musers WHERE UserEmailID ='$UserEmailID' AND UserUID <> '$UserUID'");
    	if($query->num_rows() > 0)
        {
			return true;
        }
        else{
            return false;
        }
    }

    function UpdateProfileInformation($Profile)
    {
    	$this->db->where(array("UserUID"=>$Profile['UserUID']));    
        $result = $this->db->update('musers', $Profile);
        if($this->db->affected_rows() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
    }

    function GetProfileInformation($UserUID)
    {
    	$this->db->select('*');
	    $this->db->from('musers');
	    $this->db->where(array("UserUID"=>$UserUID));
	    return $this->db->get()->result();
    }

    /*function OrderComplete($UserUID,$OrderUID)
    {
    	$fieldArray = array(
	          "WorkflowStatus"=> 5,  
	          "AssignedByUserUID"=>$UserUID,
	          "CompleteDateTime"=>date('y-m-d H:i:s')
	        );
		$this->db->where(array("OrderUID"=>$OrderUID,"WorkflowModuleUID"=>1));    
        $result = $this->db->update('torderassignment', $fieldArray);
        
		if($this->db->affected_rows() > 0)
		{
			$sql=$this->db->query("update torders set StatusUID=72 where OrderUID=10611");
			return true;
		}
		else
		{
			return false;
		}
    }*/

    /*@Desc Order Complete @Author Jainulabdeen @Since May 29 2020*/

    function OrderComplete($UserUID,$OrderUID)
    {
		$fieldArray = array(
		"OrderStatus"=> 5,  
		"CompletedDateTime"=>date('y-m-d H:i:s')
		);
		$this->db->where(array("AbstractorOrderUID"=>$OrderUID));    
		$result = $this->db->update('torderabstractor', $fieldArray);

		$this->db->where(array("AbstractorOrderUID"=>$OrderUID));
		$query2 = $this->db->get('tOrderSchedule');
		$tOrderSchedule =  $query2->row()->ScheduleUID;

		$this->db->where(array("ScheduleUID"=>$tOrderSchedule));
		$query1 = $this->db->get('tOrderSign');
		$tOrderSign =  $query1->row()->SignUID;

		
		if($this->db->affected_rows() > 0)
		{
			$sql=$this->db->query("update tOrderSign set SigningStatus='Sign' where SignUID=".$tOrderSign);
			$sql=$this->db->query("update tOrderSchedule set ScheduleStatus='Complete' where ScheduleUID=".$tOrderSchedule);
			return true;
		}
		else
		{
			return false;
		}
    }

   function Gettordersby_UID($orderUID)
   {
    $query = $this->db->get_where('torders', array('OrderUID' => $orderUID));
    return $query->result();
   }

   function CheckSearchDateExists($OrderUID)
   {
     $query = $this->db->query("SELECT EXISTS(SELECT * FROM torderdocuments WHERE OrderUID = '$OrderUID') as CheckDates");
     return $query->row();
   }
   function UploadDocuments($data)
   {
	  $this->db->insert('torderdocuments', $data);
	  return $this->db->affected_rows();
   }

   function Gettorders($data)
   { 
	 $this->db->where(array("OrderUID"=>$data['OrderUID']));
	 $query = $this->db->get('torders');
	 return $query->row_array();
   }

   function GetOrderInfoNotes($AbstractorOrderUID)
   {  
	   	$this->db->select('tordernotes.NoteUID,Note,tordernotes.OrderUID,tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,musers.UserName,musers.UserUID,mreportsections.SectionName,mreportsections.SectionColor,mreportsections.ForeColor,tordernotes.AttachedFile,tordernotes.RoleType,tordernotes.AbstractorUID,msystemnotes.SystemNotesUID,msystemnotes.SystemNotes,tordernotes.CreatedByAPI');
	   	$this->db->from('tordernotes');
	   	$this->db->join ( 'musers', 'tordernotes.CreatedByUserUID = musers.UserUID' , 'left' );
	   	$this->db->join ( 'msystemnotes', 'tordernotes.SystemNotesUID = msystemnotes.SystemNotesUID' , 'left' );
	   	$this->db->join ( 'mreportsections', 'tordernotes.SectionUID = mreportsections.SectionUID' , 'left' );
	   	$this->db->where (array('tordernotes.OrderUID'=>$AbstractorOrderUID));
	    $this->db->order_by('tordernotes.CreatedOn Desc');
	    return $this->db->get()->result();
	   }


    function GetUserRoleTypeDetails($UserUID)
    {
        $this->db->select("RoleType,RoleName");
        $this->db->from('musers');
        $this->db->join ( 'mroles', 'musers.RoleUID = mroles.RoleUID' , 'inner' );
        $this->db->where(array("musers.UserUID"=>$UserUID));  
        $query = $this->db->get();
        return $query->row();
  	}

    function CheckUserUID($OrderUID)
	{
	   $query = $this->db->query("SELECT EXISTS(SELECT * FROM tordernotes WHERE OrderUID = '$OrderUID') as CheckNotes;");
	   return $query->row();
	}

    function GetChatDetailsByuserUID($OrderUID)
    {
        $this->db->distinct();
        $this->db->select("tordernotes.CreatedByUserUID");
        $this->db->from('tordernotes');
        $this->db->join('musers','tordernotes.CreatedByUserUID=musers.UserUID','left');
        $this->db->join('mreportsections','tordernotes.SectionUID=mreportsections.SectionUID','left');
        $this->db->where(array("tordernotes.OrderUID"=>$OrderUID));  
        //$this->db->group_by('tordernotes.Note');
        $query = $this->db->get();
        return $query->row();
  	}
     
  	function GetAbstractorUID($UserUID){

  		$this->db->select('*');
  		$this->db->from('musers');
  		$this->db->where('UserUID',$UserUID);
  		$query = $this->db->get()->row();
  		return $query;
  	}
  	public function getDocumentOrderUID($AbstractorOrderUID)
    {
    	$this->db->select('OrderUID');
  		$this->db->from('torderabstractor');
  		$this->db->where('AbstractorOrderUID',$AbstractorOrderUID);
  		$query = $this->db->get()->row();
  		return $query;

        // return $this->db->select('AbstractorOrderUID')->from('torderabstractor')->where('OrderUID',$MainOrderUID)->get()->row()->AbstractorOrderUID;
    }

  	function GetAbstractorDocuments($filterDocument)
  	{ 
	   $this->db->select ( 'torderdocuments.OrderUID as OrderUID,torders.OrderDocsPath,torderdocuments.DocumentFileName,torderdocuments.DisplayFileName,torderdocuments.extension,CONCAT_WS("",torders.OrderDocsPath,torderdocuments.DocumentFileName ) AS DocumentPath,torderdocuments.TypeOfDocument AS DocType' ); 
       $this->db->from ( 'torderdocuments' );
       $this->db->join ( 'mdocumenttypes','mdocumenttypes.DocumentTypeUID = torderdocuments.TypeOfDocument' , 'left' );
       $this->db->join ( 'msearchmodes', 'msearchmodes.SearchModeUID = torderdocuments.SearchModeUID' , 'left' );
       $this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID' , 'left' );
       $this->db->where(array("torderdocuments.OrderUID"=>$filterDocument));
       $this->db->where_in("TypeOfPermissions",array(0,1,2,3,4,5,6,7));
       $this->db->order_by("torderdocuments.DocumentCreatedDate asc");
       $query = $this->db->get();
       return $query->result();
  	}

  	function GetDocumentsByUserUID($UserUID)
  	{ 
	   $this->db->select ( 'torders.OrderDocsPath,torderdocuments.DocumentFileName,torderdocuments.DisplayFileName,torderdocuments.extension,CONCAT_WS("",torders.OrderDocsPath,torderdocuments.DocumentFileName ) AS DocumentPath' ); 
	   $this->db->select ('CASE WHEN torderdocuments.TypeOfDocument = "ClosingPackage" THEN "Shipping Doc" ELSE "Others" END AS DocType', false);
       $this->db->from ( 'torderdocuments' );
       $this->db->join ( 'mdocumenttypes', 'mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID' , 'left' );
       $this->db->join ( 'msearchmodes', 'msearchmodes.SearchModeUID = torderdocuments.SearchModeUID' , 'left' );
       $this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID' , 'left' );
       $this->db->where(array("torderdocuments.UploadedUserUID"=>$UserUID));
       $this->db->where_in("TypeOfPermissions",array(0,1,2,3,4,5,6,7));
       $this->db->order_by("torderdocuments.DocumentCreatedDate asc");
       $query = $this->db->get();
       return $query->result();
  	}


  	function SaveAbstractorInfoNotes($NotesArray)
  	{
  		$this->db->insert('tordernotes', $NotesArray);
  		if($this->db->affected_rows() > 0)
  		{
  			return true;
  		}else{
  			return false;
  		}

  	}

  	function UpdateProfileImage($UserUID,$destination_path)
  	{
  		$this->db->set('Avatar',$destination_path);
  		$this->db->where(array("UserUID"=>$UserUID));    
        $result = $this->db->update('musers');
        if($this->db->affected_rows() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
  	}

// Madhuri 27/08/2019

  	    function GetShippingReasons()
	{
	   $query = $this->db->query("SELECT * FROM mShippingReason ORDER BY ShippingReasonUID");
	   return $query->result();
	}

	  /*function Change_order_status($OrderUID,$status, $UserUID)
  {
    $data['OrderUID'] = $OrderUID;
    $data['AssignedToUserUID'] = $UserUID;
    $data['WorkflowStatus'] = 0;
    $data['AssignedDatetime'] = date('Y-m-d H:i:s');
    $OrderFlag = array("torderassignment.OrderFlag"=>1,"torderassignment.AssignedToUserUID"=>$data['AssignedToUserUID'],"torderassignment.AssignedDatetime"=>$data['AssignedDatetime'],"torderassignment.WorkflowStatus"=>$status);
    $this->db->where(array("torderassignment.OrderUID"=>$data['OrderUID'],"torderassignment.WorkflowModuleUID"=>1));
    $res = $this->db->update('torderassignment',$OrderFlag);
    $sql=$this->db->query("update torders set StatusUID=15 where OrderUID=".$data['OrderUID']);
    return $res;
  }*/ 

  /*@Desc accept order @Author Jainulabdeen @Since May 29 2020*/

  function Change_order_status($OrderUID,$status, $UserUID)
  {
    $data['OrderUID'] = $OrderUID;
    $data['AssignedToUserUID'] = $UserUID;
    $data['WorkflowStatus'] = 0;
    $data['AssignedDatetime'] = date('Y-m-d H:i:s');
    $OrderFlag = array("torderabstractor.OrderFlag"=>1,"torderabstractor.AbstractorUID"=>$data['AssignedToUserUID'],"torderabstractor.AssignedDatetime"=>$data['AssignedDatetime'],"torderabstractor.OrderStatus"=>$status);
    $this->db->where(array("torderabstractor.OrderUID"=>$data['OrderUID']));
    $res = $this->db->update('torderabstractor',$OrderFlag);
    $sql=$this->db->query("update torders set StatusUID=15 where OrderUID=".$data['OrderUID']);
    return $res;
  }

 
 

    function GetMAXAbstractorOrderUID($OrderUID, $AbstractorUID)
  {
    $this->db->select('MAX(AbstractorOrderUID) AS AbstractorOrderUID');
    return $this->db->get_where('torderabstractor', array('OrderUID'=>$OrderUID, 'AbstractorUID'=>$AbstractorUID))->row()->AbstractorOrderUID;
  }

    function rejectorder_in_queue($OrderUID,$UserUID,$rejection)
  {
    $this->load->model('fees_pricing/fees_pricing_model');
    $this->load->model('order_search/ordersearch_model');

    $OrderUIDs = $this->db->query("SELECT * FROM torderabstractor WHERE AbstractorOrderUID = '".$OrderUID."' ")->row()->OrderUID;
    $AbstractorOrderUID = $OrderUID;

    $AbstractorUID=$this->db->get_where('musers', array('UserUID'=>$UserUID))->row()->AbstractorUID;

    $rejection['OrderFlag']=2;
    $rejection['RejectedByUserUID']=$UserUID;
    $rejection['RejectedDateTime']= Date('Y-m-d H:i:s',strtotime("now"));

    $this->db->select('*')->from('torderabstractor');
    $this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
    $torderabstractor_array=$this->db->get()->row_array();

    $torderabstractor_array['AbstractorStatusCode']='Unassigned';
    unset($torderabstractor_array['AbstractorOrderUID']);
    $torderabstractor_array['ApprovalStatus']=0;
    $torderabstractor_array['OldAbstractorOrderUID']=$AbstractorOrderUID;
    $torderabstractor_array['RejectedByUserUID']=$UserUID;
    $torderabstractor_array['RejectedDateTime']=date('Y-m-d H:i:s');
    $torderabstractor_array['CompletedDateTime']='0000-00-00 00:00:00';
    $this->db->insert('torderabstractorunassign', $torderabstractor_array);    
    $unassignabstractororderuid = $this->db->insert_id();
    $this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
    $this->db->delete('torderabstractor');

    $tOrderSchedule = $this->common_model->get_row('tOrderSchedule', ['AbstractorOrderUID'=>$AbstractorOrderUID]);

    if (!empty($tOrderSchedule)) 
    {
    	$this->common_model->save('tOrderSchedule', ['ScheduleStatus'=>'Canceled'], ['ScheduleUID'=>$tOrderSchedule->ScheduleUID]);
    	$this->common_model->save('tOrderSign', ['SigningStatus'=>'Cancel'], ['ScheduleUID'=>$tOrderSchedule->ScheduleUID]);
    }
    $delete=$this->ordersearch_model->ClearAbstractorByAbstractorOrderUID($AbstractorOrderUID);

    $this->ordersearch_model->clear_assigned_abstractor($OrderUIDs,$AbstractorUID);

    $abstractorfees=$this->fees_pricing_model->get_Abstractor_fee($OrderUIDs);


    if (count($abstractorfees) >0) {
      $this->ordersearch_model->UpdateAbstractorFees($OrderUIDs, $abstractorfees->AbstractorFee, $abstractorfees->AbstractorCopyCost, $abstractorfees->AbstractorAdditionalFee,$abstractorfees->AbstractorActualFee);
    }
    else{
      $this->ordersearch_model->UpdateAbstractorFees($OrderUIDs, 0, 0, 0,0);
    }
    
    $this->common_model->change_abstractorchanges_payments($OrderUIDs,$AbstractorOrderUID,$unassignabstractororderuid);
    return '1';
  }

/*@Description:CANCEL SIGNING PROCESSES LIST STARTS
   * @param $query
        * 
        * @throws no exception
        * @author Shruti <shruti.vs@avanzegroup.com>
        * @return JSON Output <string> 
	 * @version Closign API Search Functionality 
	*/

function GetCancelOrdersListByUserID($AbstractorUID,$AuthenticationKey)
{
	
	if(!empty($AbstractorUID)){
		
		$statuses = $this->config->item('keywords')['Cancelled'];

		$sql = "SELECT
		`CustomerName`,
		`OrderNumber`,
		'Cancelled' AS StatusName,
		`torders`.`StatusUID`,
		'#EC0808' AS StatusColor,
		`torderabstractorunassign`.`AbstractorOrderUID` as OrderUID,
		`torders`.`OrderEntryDatetime` AS OrderEntryDatetime,
		`torders`.`OrderUID` AS tOrderUID,
		`morderpriority`.`PriorityName`,
		`morderpriority`.`TAT`,
		`morderpriority`.`PriorityUID`,
		`mproducts`.`ProductName`,
		`mproducts`.`ProductCode`,
		`msubproducts`.`SubProductCode`,
		`msubproducts`.`SubProductName`,
		`mordertypes`.`OrderTypeName`,
		DATE_FORMAT(
		torderabstractorunassign.AssignedDateTime,
		'%m/%d/%Y %H:%i:%s'
		) AS AssignedDatetime,
		DATE_FORMAT(
		torders.OrderDueDateTime,
		'%m/%d/%Y %H:%i:%s'
		) AS OrderDueDateTime,
		DATE_FORMAT(
		torders.OrderEntryDatetime,
		'%m/%d/%Y %H:%i:%s'
		) AS OrderEntryDatetime,
		torders.PropertyAddress1,
		torders.PropertyAddress2,
		torders.PropertyZipcode,
		torders.PropertyStateCode,
		torders.PropertyCityName,
		torders.PropertyCountyName,
		tOrderClosing.SigningAddress1,
		tOrderClosing.SigningAddress2,
		tOrderClosing.SigningCityName,
		tOrderClosing.SigningStateCode,
		tOrderClosing.SigningCountyName,
		tOrderClosing.SigningZipCode,
		DATE_FORMAT(
		tOrderSchedule.SigningDateTime,
		'%m/%d/%Y %H:%i:%s'
	) AS SigningDateTime,
	tOrderSchedule.ScheduleStatus,
	torderabstractorunassign.OrderStatus
	FROM
	(`torderabstractorunassign`)
	JOIN `torders` ON `torders`.`OrderUID` = `torderabstractorunassign`.`OrderUID`
	LEFT JOIN mordertypes ON `torders`.`OrderTypeUID` = `mordertypes`.`OrderTypeUID`
	LEFT JOIN `morderpriority` ON `morderpriority`.`PriorityUID` = `torders`.`PriorityUID`
	LEFT JOIN `mcustomers` ON `mcustomers`.`CustomerUID` = `torders`.`CustomerUID`
	LEFT JOIN `msubproducts` ON `msubproducts`.`SubProductUID` = `torders`.`SubProductUID`
	LEFT JOIN `mproducts` ON `mproducts`.`ProductUID` = `msubproducts`.`ProductUID`
	LEFT JOIN `morderstatus` ON `morderstatus`.`StatusUID` = `torders`.`StatusUID`
	LEFT JOIN `torderpropertyroles` ON `torderpropertyroles`.`OrderUID` = `torders`.`OrderUID`
	JOIN `tOrderSchedule` ON `tOrderSchedule`.`AbstractorOrderUID` = `torderabstractorunassign`.`AbstractorOrderUID`
	JOIN `tOrderClosing` ON `tOrderClosing`.`ScheduleUID` = `tOrderSchedule`.`ScheduleUID`
	WHERE `torders`.`StatusUID` NOT IN (".$statuses.")
	AND `torderabstractorunassign`.`AbstractorUID` = ".$AbstractorUID."
	AND mproducts.IsClosingProduct = 1
	GROUP BY torderabstractorunassign.AbstractorOrderUID";
						// ORDER BY
						// 	FIELD(
						// 		`torders`.`PriorityUID`,
						// 		3,
						// 		1
						// 	) DESC,
						// 	`torders`.`OrderEntryDatetime` ASC";		

	$checkorders = $this->db->query($sql)->result_array();

	$my_orders = [];
	if(count($checkorders) > 0)
	{
		foreach ($checkorders as $key => $value) {
			$my_orders[] = $value;
			$Borrower = $this->db->query("SELECT  CONCAT_WS(' ', `torderpropertyroles`.`PRName` ) AS BorrowerName,torderpropertyroles.PRCellNumber AS CellNumber,torderpropertyroles.PRHomeNumber AS HomeNumber,torderpropertyroles.PRWorkNumber AS WorkNumber from torderpropertyroles WHERE torderpropertyroles.OrderUID=".$value['tOrderUID'])->result_array();

			if ($Borrower) {
				$my_orders[$key]['Borrower'] = $Borrower;
			}else{
				$my_orders[$key]['Borrower'] = '';
			}
		}

	} 
	else 
	{
		return false;
				// $my_orders = array();
	}
}
else
{
	return false;
			//$my_orders = array();
}
return $my_orders;



}


  /*@Description:CANCEL SIGNING PROCESSES STARTS
   * @param $query
        * 
        * @throws no exception
        * @author Shruti <shruti.vs@avanzegroup.com>
        * @return JSON Output <string> 
	 * @version Closign API Search Functionality 
	*/


function cancelSigningProcess($OrderUID,$UserUID,$rejection)
	{
	
	$OrderUIDs = $this->db->query("SELECT * FROM torderabstractor WHERE AbstractorOrderUID = '".$OrderUID."' ")->row()->OrderUID;
	
	
	$AbstractorUID=$this->db->get_where('musers', array('UserUID'=>$UserUID))->row()->AbstractorUID;

	$rejection['OrderFlag']=2;
	$rejection['RejectedByUserUID']=$UserUID;
	$rejection['RejectedDateTime']= Date('Y-m-d H:i:s',strtotime("now"));

   		$this->db->where('OrderUID', $OrderUIDs);
		$this->db->delete('torderabstractorunassign'); 

	$torderabstractor_array=$this->db->select('*')->from('torderabstractor')->where('AbstractorOrderUID', $AbstractorOrderUID)->get()->row();


	$torderabstractor_array = array('OrderUID' => $OrderUIDs,
					'AbstractorUID' => $AbstractorUID,		
					'AbstractorStatusCode' => Unassigned,
					'AbstractorOrderUID'=> $OrderUID,
					'OrderStatus' =>110,
					'ApprovalStatus'=> 0,
					'RejectedByUserUID'=> $UserUID,
					'RejectedDateTime'=> date('Y-m-d H:i:s'),
					'CompletedDateTime'=> date('Y-m-d H:i:s')
					);
		
	
		$this->db->insert('torderabstractorunassign', $torderabstractor_array); 
		$unassignabstractororderuid = $this->db->insert_id();
		$this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
		$this->db->delete('torderabstractor');   
			
		$tOrderSchedule = $this->common_model->get_row('tOrderSchedule', ['AbstractorOrderUID'=>$AbstractorOrderUID]);
	       $ScheduleUID = $tOrderSchedule->ScheduleUID;
		
		if(!empty($tOrderSchedule))
		{
		$this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
		$this->db->update('tOrderSchedule', array('ScheduleStatus'=>'Cancelled'));
		$this->db->update('tOrderSign', array('SigningStatus'=>'Cancel'));
        }
        
        // Cancel Sining Status changed
        $this->db->where('OrderUID',$OrderUIDs);
		$this->db->update('torderabstractor',array('OrderStatus'=>'110'));

		// $this->db->where('OrderUID',$OrderUIDs);
		// $this->db->update('tOrders',array('StatusUID'=>'110'));
		//$delete=$this->ClearAbstractorByAbstractorOrderUID($AbstractorOrderUID);
		
		$this->clear_assigned_abstractor($OrderUID,$AbstractorUID);

	return true;
		
   }

//    function ClearAbstractorByAbstractorOrderUID($AbstractorOrderUID)
// 	{
// 	  $this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
// 	   $this->db->delete('torderabstractor');
// 	  return $this->db->affected_rows();
// 	}

	function clear_assigned_abstractor($OrderUID,$AbstractorUID){

		$result = $this->db->query("SELECT IF( EXISTS(SELECT * FROM `torderassignment` JOIN musers on musers.UserUID = torderassignment.AssignedToUserUID where OrderUID = '".$OrderUID."' and WorkflowModuleUID = 1  AND AssignedToUserUID IN (SELECT UserUID FROM mabstractor WHERE AbstractorUID = '".$AbstractorUID."')  ) , 1, 0) AS EXIST")->row();
     
		if($result->EXIST == '1'){
			$this->db->query("UPDATE `torderassignment` SET AssignedToUserUID = NULL,AssignedByUserUID = NULL,AssignedDatetime = NULL,SelfManualAssign = NULL WHERE OrderUID = '".$OrderUID."' and WorkflowModuleUID = '1' ");
		}
		if($this->db->affected_rows()){
			return true;
		}else{
			return false;
		}
	}

  //CANCEL SIGNING PROCESSES END

  
  //CANCEL ORDER 
  function cancelorders($OrderUID,$UserUID,$rejection){
	
    $this->load->model('fees_pricing/fees_pricing_model');
    $this->load->model('order_search/ordersearch_model');

    $OrderUIDs = $this->db->query("SELECT * FROM torderabstractor WHERE AbstractorOrderUID = '".$OrderUID."' ")->row()->OrderUID;
    $AbstractorOrderUID = $OrderUID;

    if (!empty($OrderUIDs)) {

    $AbstractorUID=$this->db->get_where('musers', array('UserUID'=>$UserUID))->row()->AbstractorUID;


    $rejection['OrderFlag']=2;
    $rejection['RejectedByUserUID']=$UserUID;
    $rejection['RejectedDateTime']= Date('Y-m-d H:i:s',strtotime("now"));

    $this->db->select('*')->from('torderabstractor');
    $this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
    $torderabstractor_array=$this->db->get()->row_array();

    $torderabstractor_array['AbstractorStatusCode']='Unassigned';
    unset($torderabstractor_array['AbstractorOrderUID']);
    $torderabstractor_array['ApprovalStatus']=0;
    $torderabstractor_array['OldAbstractorOrderUID']=$AbstractorOrderUID;
    $torderabstractor_array['RejectedByUserUID']=$UserUID;
    $torderabstractor_array['RejectedDateTime']=date('Y-m-d H:i:s');
    $torderabstractor_array['CompletedDateTime']='0000-00-00 00:00:00';
    $this->db->insert('torderabstractorunassign', $torderabstractor_array);    
    $unassignabstractororderuid = $this->db->insert_id();
    $this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
    $this->db->delete('torderabstractor');

    $tOrderSchedule = $this->common_model->get_row('tOrderSchedule', ['AbstractorOrderUID'=>$AbstractorOrderUID]);

    if (!empty($tOrderSchedule)) 
    {
    	$this->common_model->save('tOrderSchedule', ['ScheduleStatus'=>'Canceled'], ['ScheduleUID'=>$tOrderSchedule->ScheduleUID]);
    	$this->common_model->save('tOrderSign', ['SigningStatus'=>'Cancel'], ['ScheduleUID'=>$tOrderSchedule->ScheduleUID]);
    }

    $delete=$this->ordersearch_model->ClearAbstractorByAbstractorOrderUID($AbstractorOrderUID);

    $this->ordersearch_model->clear_assigned_abstractor($OrderUIDs,$AbstractorUID);

    $abstractorfees=$this->fees_pricing_model->get_Abstractor_fee($OrderUIDs);


    if (count($abstractorfees) >0) {
      $this->ordersearch_model->UpdateAbstractorFees($OrderUIDs, $abstractorfees->AbstractorFee, $abstractorfees->AbstractorCopyCost, $abstractorfees->AbstractorAdditionalFee,$abstractorfees->AbstractorActualFee);
    }
    else{
      $this->ordersearch_model->UpdateAbstractorFees($OrderUIDs, 0, 0, 0,0);
    }
    
    $this->common_model->change_abstractorchanges_payments($OrderUIDs,$AbstractorOrderUID,$unassignabstractororderuid);

    return true;
    	
    }
    else
    {
    	return false;
    }
  }

	function GetPendingOrdersByUserID($loggedid,$AuthenticationKey)
	{

		if(!empty($loggedid))
		{
			$Roles = $this->db->query("SELECT UserUID,RoleUID,CustomerUID FROM musers WHERE AuthKey = '".$AuthenticationKey."' ")->row();
		    $UserUID = $Roles->UserUID;
		    $CustomerUID = $Roles->CustomerUID;
		    $Role = $this->db->query("SELECT RoleType FROM mroles WHERE RoleUID =".$Roles->RoleUID)->row();
		    $RoleType = $Role->RoleType;

				$statuses = $this->config->item('keywords')['Order Assigned'];

				$sql = "SELECT `CustomerName`, `OrderNumber`, `StatusName`,`torders`.`StatusUID`,`StatusColor`, `torders`.`OrderUID`, `torders`.`OrderEntryDatetime` as OrderEntryDatetime, `morderpriority`.`PriorityName`,`morderpriority`.`TAT`,`morderpriority`.`PriorityUID`, `mproducts`.`ProductName`, `mproducts`.`ProductCode`, `msubproducts`.`SubProductCode`, `msubproducts`.`SubProductName`,`mordertypes`.`OrderTypeName`,DATE_FORMAT(torderassignment.AssignedDatetime, '%m/%d/%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(torders.OrderDueDateTime, '%m/%d/%Y %H:%i:%s') as OrderDueDateTime, DATE_FORMAT(torders.OrderEntryDatetime, '%m/%d/%Y %H:%i:%s') as OrderEntryDatetime ,torders.PropertyAddress1,torders.PropertyAddress2,torders.PropertyZipcode, torders.PropertyStateCode,torders.PropertyCityName,torders.PropertyCountyName,tOrderClosing.SigningAddress1,tOrderClosing.SigningAddress2,tOrderClosing.SigningCityName,tOrderClosing.SigningStateCode,tOrderClosing.SigningCountyName,tOrderClosing.SigningZipCode,DATE_FORMAT(tOrderSchedule.SigningDateTime,'%m/%d/%Y %H:%i:%s') AS SigningDateTime FROM (`torders`) LEFT JOIN mordertypes ON `torders`.`OrderTypeUID` = `mordertypes`.`OrderTypeUID` LEFT JOIN `torderassignment` ON `torders`.`OrderUID` = `torderassignment`.`OrderUID`  LEFT JOIN `morderpriority` ON `morderpriority`.`PriorityUID` = `torders`.`PriorityUID` LEFT JOIN `mcustomers` ON `mcustomers`.`CustomerUID` = `torders`.`CustomerUID` LEFT JOIN `msubproducts` ON `msubproducts`.`SubProductUID` = `torders`.`SubProductUID` LEFT JOIN `mproducts` ON `mproducts`.`ProductUID` = `msubproducts`.`ProductUID` LEFT JOIN `morderstatus` ON `morderstatus`.`StatusUID` = `torders`.`StatusUID` LEFT JOIN `tOrderSchedule` ON `tOrderSchedule`.`OrderUID` = `torders`.`OrderUID`  LEFT JOIN `tOrderClosing` ON `tOrderClosing`.`OrderUID` = `torders`.`OrderUID` WHERE `torders`.`StatusUID` IN (".$statuses.") AND `torderassignment`.`AssignedToUserUID` = ".$loggedid." AND torderassignment.WorkflowModuleUID !=4 $like GROUP BY `OrderUID` ORDER BY FIELD(`torders`.`PriorityUID`,3,1) DESC, `torders`.`OrderEntryDatetime` ASC";

		}
		else{

			$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].
			','.$this->config->item('keywords')['Cancelled'].','.$this->config->item('keywords')['Exception Raised'];

			$sql = "SELECT `CustomerName`, `OrderNumber`, `StatusName`,`torders`.`StatusUID`,`StatusColor`, `torders`.`OrderUID`, `torders`.`OrderEntryDatetime` as OrderEntryDatetime, `morderpriority`.`PriorityName`,`morderpriority`.`TAT`,`morderpriority`.`PriorityUID`, `mproducts`.`ProductName`, `mproducts`.`ProductCode`, `msubproducts`.`SubProductCode`, `msubproducts`.`SubProductName`,`mordertypes`.`OrderTypeName`,DATE_FORMAT(torderassignment.AssignedDatetime, '%m/%d/%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(torders.OrderDueDateTime, '%m/%d/%Y %H:%i:%s') as OrderDueDateTime, DATE_FORMAT(torders.OrderEntryDatetime, '%m/%d/%Y %H:%i:%s') as OrderEntryDatetime,torders.PropertyAddress1,torders.PropertyAddress2,torders.PropertyZipcode, torders.PropertyStateCode,torders.PropertyCityName,torders.PropertyCountyName FROM (`torders`) LEFT JOIN mordertypes ON `torders`.`OrderTypeUID` = `mordertypes`.`OrderTypeUID`  LEFT JOIN `torderassignment` ON `torders`.`OrderUID` = `torderassignment`.`OrderUID`  LEFT JOIN `morderpriority` ON `morderpriority`.`PriorityUID` = `torders`.`PriorityUID`  LEFT JOIN `mcustomers` ON `mcustomers`.`CustomerUID` = `torders`.`CustomerUID` LEFT JOIN `msubproducts` ON `msubproducts`.`SubProductUID` = `torders`.`SubProductUID` LEFT JOIN `mproducts` ON `mproducts`.`ProductUID` = `msubproducts`.`ProductUID` LEFT JOIN `morderstatus` ON `morderstatus`.`StatusUID` = `torders`.`StatusUID` WHERE `torders`.`StatusUID` NOT IN (".$statuses.") $like GROUP BY `OrderUID` ORDER BY FIELD(`torders`.`PriorityUID`,3,1) DESC, `torders`.`OrderEntryDatetime` ASC"; 

		}

		$checkorders = $this->db->query($sql)->result_array(); 

		foreach ($checkorders as $key => $value) 
		{
			$assignedusers =  $this->get_order_assigned_users($value);
			if(count($assignedusers) > 0)
			{
				$checkorders[$key]['LoginID'] = $assignedusers;
			} else {
				$checkorders[$key]['LoginID'] = '';
			}
			//Instead of loggedid we need to pass Authentication Key.

			if(in_array($RoleType,array(1,2,3,4,5,6,8,13)) == False)
			{
				$assigned = $this->common_model->get_assigned_workflows($value['OrderUID'],$UserUID);
				$completed = $this->common_model->get_completed_workflows($value['OrderUID'],$UserUID);
				$assigned_orderss = [];
				$completed_orderss = [];
				foreach ($assigned as $keys => $values) {
					$assigned_orderss[] = $values['WorkflowModuleUID'];
				}
				foreach ($completed as $keyss => $valuess) {
					$completed_orderss[] = $valuess['WorkflowModuleUID'];
				}
				$assigned_workflows = [];
				$completed_workflows = [];
				foreach ($assigned as $keys => $values) {
					$assigned_workflows[] = $values['OrderUID'];
				}
				foreach ($completed as $keyss => $valuess) {
					$completed_workflows[] = $valuess['OrderUID'];
				}
				if($assigned_orderss === array_intersect($assigned_orderss, $completed_orderss) && $completed_orderss === array_intersect($completed_orderss, $assigned_orderss)) {

					if($assigned_workflows === array_intersect($assigned_workflows, $completed_workflows) && $completed_workflows === array_intersect($completed_workflows, $assigned_workflows)) {
						unset($checkorders[$key]);
					} 
				}   
			}
		}
		$checkorders = array_values($checkorders);
		if ($checkorders) {
	    	foreach ($checkorders as $key => $value) {
	    		$my_orders[] = $value;
	    		$Borrower = $this->db->query("SELECT  CONCAT_WS(' ', `torderpropertyroles`.`PRName` ) AS BorrowerName,torderpropertyroles.PRCellNumber AS CellNumber,torderpropertyroles.PRHomeNumber AS HomeNumber,torderpropertyroles.PRWorkNumber AS WorkNumber from torderpropertyroles WHERE torderpropertyroles.OrderUID=".$value['OrderUID'])->result_array();
					if ($Borrower) {
						$my_orders[$key]['Borrower'] = $Borrower;
					}else{
						$my_orders[$key]['Borrower'] = '';
					}
	    	}
	    }else{
	    	$my_orders = array();
	    }
		return $my_orders;
	}


 /**
        *@Description Function to GetDeedList Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Role Based API Functionality 
        * @since 19/08/2020 
    */ 
  function GetDeedDetailsByOrderUID($OrderUID){
  
    $this->db->select("*");
    $this->db->from('torderdeeds');
    $this->db->join('mdocumenttypes','torderdeeds.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
    $this->db->join('mestateinterests','torderdeeds.EstateInterestUID=mestateinterests.EstateInterestUID','left');
    $this->db->where(array("torderdeeds.OrderUID"=>$OrderUID));   
    $this->db->order_by('torderdeeds.DeedPosition',"ASC");
     $query = $this->db->get();
    return $query->result();
  }

 /**
        *@Description Function to GetDeedDetails Based DeedID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Role Based API Functionality 
        * @since 21/08/2020 
    */ 


 function GetDeedDetailsByID($OrderUID)
 {

 	$this->db->select ('torderdeeds.DeedSNo,torderdeeds.DeedPosition,torderdeeds.DeedDated,torderdeeds.DeedRecorded,torderdeeds.DeedSale,torderdeeds.MannerofTitle,torderdeeds.ConsiderationAmount,torderdeeds.PlatandPage,torderdeeds.LotandBlock,torderdeeds.CertificateNo,torderdeeds.DocumentNo,torderdeeds.InstrumentNo,torderdeeds.DeedComments,torderdeeds.Deed_DBVTypeUID_1,torderdeeds.Deed_DBVTypeUID_2,mdocumenttypes.DocumentTypeName,mestateinterests.EstateInterestName,mtenancytype.TenancyName,torderdeeds.Deed_DBVTypeValue_1,torderdeeds.Deed_DBVTypeValue_2,torderdeeds.Township');
 	$this->db->from('torderdeeds');
 	$this->db->join('mdocumenttypes','torderdeeds.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
 	$this->db->join('mestateinterests','torderdeeds.EstateInterestUID=mestateinterests.EstateInterestUID','left');
 	$this->db->join('mtenancytype','torderdeeds.TenancyUID=mtenancytype.TenancyUID','left');

 	$this->db->where(array("torderdeeds.DeedSNo"=>$OrderUID));
 	$query = $this->db->get()->row_array();

 	$MortgageDBV1 = $query['Deed_DBVTypeUID_1'];
 	$MortgageDBV2 = $query['Deed_DBVTypeUID_2'];
 	$DeedSNo = $query['DeedSNo'];

 	$query1 = $this->db->select('DBVTypeName as DBVTypeName1')->from('mmortgagedbvtypes')->where('DBVTypeUID',$MortgageDBV1)->get()->row_array();
 	$query2 = $this->db->select('DBVTypeName as DBVTypeName2')->from('mmortgagedbvtypes')->where('DBVTypeUID',$MortgageDBV2)->get()->row_array();

 	$query3 = $this->db->select('PartyName')->from('torderdeedparties')->where('DeedSNo',$DeedSNo)->get()->result_array();
 	$Grantor = $query3['0']['PartyName'];
 	$PartyName = $query3['1']['PartyName'];


 	if ($query1) {
 		$query['DBVTypeName1'] = $query1['DBVTypeName1'];
 	}else{
 		$query['DBVTypeName1'] = '';
 	}
 	if ($query2) {
 		$query['DBVTypeName2'] = $query2['DBVTypeName2'];
 	}else{
 		$query['DBVTypeName2'] = '';
 	}
 	if ($Grantor) {
 		$query['Grantor'] = $Grantor;
 	}else{
 		$query['Grantor'] = '';
 	}
 	if ($PartyName) {
 		$query['PartyName'] = $PartyName;
 	}else{
 		$query['PartyName'] = '';
 	}

 	return $query; 
 }

/**
        *@Description Function to GetMortgageDetails Based MortgageID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Role Based API Functionality 
        * @since 21/08/2020 
    */ 

 function GetMortgageDetailsByID($OrderUID){
   $this->db->select ('tordermortgages.OrderUID,tordermortgages.MortgageSNo,tordermortgages.MortgagePosition,tordermortgages.IsOpenEnded,tordermortgages.AdditionalInfo,tordermortgages.Trustee1,tordermortgages.Trustee2,tordermortgages.MortgageComments,tordermortgages.MortgageDated,tordermortgages.MortgageRecorded,tordermortgages.MortgageMaturityDate,tordermortgages.MortgageAmount,tordermortgages.Mortgage_DBVTypeUID_1,tordermortgages.Mortgage_DBVTypeValue_1,tordermortgages.Mortgage_DBVTypeUID_2,tordermortgages.Mortgage_DBVTypeValue_2,tordermortgages.LienTypeUID,mlientypes.LienTypeName');

   $this->db->from('tordermortgages');
   $this->db->join('mdocumenttypes','tordermortgages.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
   $this->db->join('mlientypes','tordermortgages.LienTypeUID=mlientypes.LienTypeUID','left');
     

   $this->db->where(array("tordermortgages.MortgageSNo"=>$OrderUID));
   $query = $this->db->get()->row_array();
  
   $MortgageDBV1 = $query['Mortgage_DBVTypeUID_1'];
   $MortgageDBV2 = $query['Mortgage_DBVTypeUID_2'];
  
   $MortgageSNo = $query['MortgageSNo'];
   $OrderUID = $query['OrderUID'];

   $query1 = $this->db->select('DBVTypeName as DBVTypeName1')->from('mmortgagedbvtypes')->where('DBVTypeUID',$MortgageDBV1)->get()->row_array();
 	$query2 = $this->db->select('DBVTypeName as DBVTypeName2')->from('mmortgagedbvtypes')->where('DBVTypeUID',$MortgageDBV2)->get()->row_array();

 	$query3 = $this->db->select('PartyName')->from('tordermortgageparties')->where('MortgageSNo',$MortgageSNo)->get()->result_array();
 	$Grantor = $query3['0']['PartyName'];
 	$PartyName = $query3['1']['PartyName'];

 	if ($query1) {
 		$query['DBVTypeName1'] = $query1['DBVTypeName1'];
 	}else{
 		$query['DBVTypeName1'] = '';
 	}
 	if ($query2) {
 		$query['DBVTypeName2'] = $query2['DBVTypeName2'];
 	}else{
 		$query['DBVTypeName2'] = '';
 	}
 	if ($Grantor) {
 		$query['Mortgagors'] = $Grantor;
 	}else{
 		$query['Mortgagors'] = '';
 	}
 	if ($PartyName) {
 		$query['Mortgagees'] = $PartyName;
 	}else{
 		$query['Mortgagees'] = '';
 	}

 	$Document = $this->GetMortgageAssignment($OrderUID,$MortgageSNo);
 	

 	if ($Document) {
 		$query['AssignmentDocument'] = $Document;
 		
 		// $query['SubDBVTypeName1'] = $Document->SubDBVTypeName1;
 		// $query['SubDBVTypeName2'] = $Document->SubDBVTypeName2;

 	}else{
 		$query['AssignSubDocument'] = '';
 	}

    return $query;
 }

/**
        *@Description Function to GetMortgageAssignment Based MortgageNo,OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Role Based API Functionality 
        * @since 21/08/2020 
    */ 

function GetMortgageAssignment($OrderUID, $MortgageSNo)
{
	$this->db->select('tordermortgageassignment.*,msubdocumentmortgages.*,mdocumenttypes.DocumentTypeName')->from('tordermortgageassignment');
	$this->db->order_by("tordermortgageassignment.SubMortgagePosition");
	$this->db->join('msubdocumentmortgages', 'tordermortgageassignment.DocumentTypeUID=msubdocumentmortgages.DocumentTypeUID', 'left');
	$this->db->join('mdocumenttypes','tordermortgageassignment.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
	$this->db->join('tordermortgages','tordermortgageassignment.OrderUID=tordermortgages.OrderUID','left');
	$query=$this->db->where(array('tordermortgageassignment.OrderUID' => $OrderUID, 'tordermortgageassignment.MortgageSNo'=>$MortgageSNo))->get()->row_array();
 


	$SubMortgageDBV1 = $query['Subdocument_DBVTypeUID_1'];
	$SubMortgageDBV2 = $query['Subdocument_DBVTypeUID_2'];
	
	$query1 = $this->db->select('DBVTypeName as DBVTypeName1')->from('mmortgagedbvtypes')->where('DBVTypeUID', $SubMortgageDBV1)->get()->row_array();
	$query2 = $this->db->select('DBVTypeName as DBVTypeName2')->from('mmortgagedbvtypes')->where('DBVTypeUID',$SubMortgageDBV2)->get()->row_array();


	if ($query1) {
		$query['SubDBVTypeName1'] = $query1['DBVTypeName1'];
	}else{
		$query['SubDBVTypeName1'] = '';
	}
	if ($query2) {
		$query['SubDBVTypeName2'] = $query2['DBVTypeName2'];
	}else{
		$query['SubDBVTypeName2'] = '';
	}

	return $query;
}
 

 /**
        *@Description Function to GetMortgageList Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Role Based API Functionality 
        * @since 19/08/2020 
    */ 
  function GetMortgageDetails($OrderUID)
  {
    $this->db->select("*");
    $this->db->from('tordermortgages');
    $this->db->join('mdocumenttypes','tordermortgages.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
    $this->db->join('mlientypes','tordermortgages.LienTypeUID=mlientypes.LienTypeUID','left');
    $this->db->where(array("tordermortgages.OrderUID"=>$OrderUID));     
    $this->db->order_by('tordermortgages.MortgagePosition',"ASC");     
    $query = $this->db->get();
    return $query->result();

  }

/**
        *@Description Function to GetPropertyRoles Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version GetPropertyRoles  Based API Functionality 
        * @since 24/08/2020 
    */ 

 function GetPropertyRoleDetails($OrderUID)
 {
 	$this->db->select("*");
    $this->db->from('torderpropertyinfo');
    $this->db->where(array("torderpropertyinfo.OrderUID"=>$OrderUID));
    $query = $this->db->get();
    return $query->row();
 }

/**
        *@Description Function to GetDeedList Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Role Based API Functionality 
        * @since 21/08/2020 
    */ 
function GetJudgementListByOrderUID($OrderUID){

	$this->db->select("torderjudgements.*,mdocumenttypes.DocumentTypeName");
	$this->db->from('torderjudgements');
	$this->db->join('mdocumenttypes','torderjudgements.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
	$this->db->where(array("torderjudgements.OrderUID"=>$OrderUID));   
	$this->db->order_by('torderjudgements.JudgementSNo',"ASC");
	$query = $this->db->get();
	return $query->result_array();
}


/**
        *@Description Function to GetJudmentDetails Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version GetJudmentDetails Based API Functionality 
        * @since 21/08/2020 
    */ 

 function getJudgementDetailsByOrderUID($OrderUID){

 	// $Plaintiff = $this->config->item('PartyTypeUID')['Plaintiff'];
		// $JudgementSNo=0;

		$this->db->select("torderjudgementparties.*,morderpartytypes.PartyTypeName,mpropertyroles.PropertyRoleName");
		$this->db->from('torderjudgementparties');
    	$this->db->join('morderpartytypes','torderjudgementparties.PartyTypeUID=morderpartytypes.PartyTypeUID','left');
    	$this->db->join('mpropertyroles','torderjudgementparties.PropertyRoleUID=mpropertyroles.PropertyRoleUID','left');
		$this->db->where("torderjudgementparties.JudgementSNo",$OrderUID);
		$query = $this->db->get()->result_array();

		$JudgementSNo =$query[0]['JudgementSNo'];

        $OrderJudgment = $this->db->select('*')->from('torderjudgements')->where('JudgementSNo', $JudgementSNo)->get()->row_array();

        $DocumentTypeName=$this->db->select('DocumentTypeName')->from('mdocumenttypes')->where('DocumentTypeUID', $OrderJudgment['DocumentTypeUID'])->get()->row();
    	$OrderJudgment['DocumentTypeName'] =$DocumentTypeName->DocumentTypeName;

    	$JudgementDBVTypeName1 = $this->db->select('DBVTypeName as DBVTypeName1')->from('mmortgagedbvtypes')->where('DBVTypeUID', $OrderJudgment['Judgement_DBVTypeUID_1'])->get()->row();
    	$JudgementDBVTypeName2 = $this->db->select('DBVTypeName as DBVTypeName2')->from('mmortgagedbvtypes')->where('DBVTypeUID',$OrderJudgment['Judgement_DBVTypeUID_2'])->get()->row();	
       
      $OrderJudgment['JudgementDBVTypeName1'] =$JudgementDBVTypeName1->DBVTypeName1;
      $OrderJudgment['JudgementDBVTypeName2'] =$JudgementDBVTypeName2->DBVTypeName2;

       $JudgementDetails = array('JudgementPropertyDetatails' => $query,'JudgementDetails' =>$OrderJudgment);

        return $JudgementDetails;
     
    	// return array_merge($query,$OrderJudgment);

 }

/**
        *@Description Function to GetLinesList Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version GetLinesList Based API Functionality 
        * @since 21/08/2020 
    */ 
function GetLinesListByOrderUID($OrderUID){

    // $Liens = $this->config->item('DocumentTypeUID')['Liens'];

    $this->db->select("*");
    $this->db->from('torderleins');
    $this->db->join('mdocumenttypes','torderleins.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
    $this->db->where(array("torderleins.OrderUID"=>$OrderUID));      
    $this->db->order_by('torderleins.LienPosition',"ASC");  
    $query = $this->db->get()->result(); 
    return $query;

}

/**
        *@Description Function to GetLinesDetails Based LinesNo
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version GetLinesDetails Based API Functionality 
        * @since 19/08/2020 
    */ 
function GetLinesDetailsByLinesNo($GetOrderUID){

	$this->db->select("torderleins.*,mdocumenttypes.DocumentTypeName");
    $this->db->from('torderleins');
    $this->db->join('mdocumenttypes','torderleins.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
    $this->db->join('mmortgagedbvtypes','torderleins.Lien_DBVTypeUID_1=mmortgagedbvtypes.DBVTypeUID','left');
    $this->db->where("torderleins.LeinSNo",$GetOrderUID);
    $query = $this->db->get()->row_array();


   $LinesDBVTypeName1 = $this->db->select('DBVTypeName as LinesDBVTypeName1')->from('mmortgagedbvtypes')->where('DBVTypeUID', $query['Lien_DBVTypeUID_1'])->get()->row();
    	$LinesDBVTypeName2 = $this->db->select('DBVTypeName as LinesDBVTypeName2')->from('mmortgagedbvtypes')->where('DBVTypeUID',$query['Lien_DBVTypeUID_2'])->get()->row();	
       
      $query['LinesDBVTypeName1'] =$LinesDBVTypeName1->LinesDBVTypeName1;
      $query['LinesDBVTypeName2'] =$LinesDBVTypeName2->LinesDBVTypeName2;

    return $query;
}

  /**
        *@Description Function to GetHistory Based OrderUID
        *
        * @throws no exception
        * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
        * @return JSON Output <string> 
        * @version Role Based API Functionality 
        * @since 19/08/2020 
    */ 

  function GetAllAuditdetails($OrderUID){
  	$OrderUID=rtrim($OrderUID,'/');
  	$test=$this->db->query("SELECT `taudittrail`.*, `taudittrail`.`TableName` as tables ,`mfields`.*, `musers`.`UserName`, `mroles`.`RoleName`, `torders`.`OrderNumber`
  		FROM `taudittrail`
  		JOIN `musers` ON `musers`.`UserUID`=`taudittrail`.`UserUID` 
  		JOIN `mroles` ON `mroles`.`RoleUID`=`musers`.`RoleUID` 
  		LEFT JOIN `torders` ON `torders`.`OrderUID`=`taudittrail`.`OrderUID` 
  		LEFT JOIN `mfields` ON `mfields`.`FieldUID`=`taudittrail`.`FieldUID` 
  		WHERE `taudittrail`.`OrderUID` = $OrderUID AND (`mfields`.`FieldUID` <> '1149' OR `mfields`.`FieldUID` is NULL) ORDER BY `taudittrail`.`AuditUID` DESC ");
  	return $test->result();

  }


	function updatesignDetails($fieldArray,$SignedUID,$OrderUID,$UserUID){

 $this->db->select("*");
 $this->db->from('tOrderSign');
 $this->db->where('SignUID',$SignedUID);
 $this->db->where('OrderUID',$OrderUID);
 $query = $this->db->get()->row();

 $oldvalue1 = $query->SigningFeedback;
 $oldValue2 = $query->SigningComment;
 $oldValue3 = $query->SigningAcceptedComment;
 $oldValue4 = $query->DayOfSigningComment;
 $oldValue5 = $query->PostSigningComment;

 $SigningDoneOld = $query->IsSigningDone;
 $Shippedold = $query->IsShipped;
 $SigningAcceptOld = $query->IsSigningAccepted;
 $DayOfSigningOld= $query->IsDayOfSigning;
 $PostSigningOld = $query->PostSigningComment;

 $SigningDoneOlder = ($SigningDoneOld == 1) ? 'Yes' : 'No';
 $Shippedoldolder = ($Shippedold == 1) ? 'Yes' : 'No';
 $SigningAcceptOlder = ($SigningAcceptOld == 1) ? 'Yes' : 'NO';
 $DayOfSigningOlder = ($DayOfSigningOld == 1) ? 'Yes' : 'NO';
 $PostSigningOlder = ($PostSigningOld == 1) ? 'Yes' : 'NO';

	  $this->db->where('SignUID',$SignedUID);
	  $this->db->update('tOrderSign', $fieldArray);

	  $this->db->select("*");
	  $this->db->from('tOrderSign');
	  $this->db->where('SignUID',$SignedUID);
	  $this->db->where('OrderUID',$OrderUID);
	  $query = $this->db->get()->row();

	  $newvalue1 = $query->SigningFeedback;
	  $SignedDateTime = $query->SignedDateTime;
	  $newvalue2 = $query->SigningComment;
	  $newValue3 = $query->SigningAcceptedComment;
	  $newValue4 = $query->DayOfSigningComment;
	  $newValue5 = $query->PostSigningComment;

     $IsSigningDoneNew = $query->IsSigningDone;
     $IsShippedNew = $query->IsShipped;
     $IsSigningAcceptedNew = $query->IsSigningAccepted;
     $DayOfSigningNew = $query->IsDayOfSigning;
     $PostSigningNew =  $query->PostSigningComment;
  
     $IsSigningDoneNewValue = ($IsSigningDoneNew == 1) ? 'Yes' : 'NO';
     $IsShippedNewValue = ($IsShippedNew == 1) ? 'Yes' : 'NO';
     $SigningAcceptNewValue = ($IsSigningAcceptedNew == 1) ? 'Yes' : 'NO';
     $DayOfSigningNewValue = ($DayOfSigningNew == 1) ? 'Yes' : 'NO';
     $PostSigningNewValue = ($PostSigningNew == 1) ? 'Yes' : 'NO';

    $signedFeedback = 'Signing FeedBack Comment ('.$SignedDateTime.') is Changed from "'.$oldvalue1.'" to "'.$newvalue1.'"';
    $SigningComment = 'Signing Done Comment ('.$SignedDateTime.') is Changed from "'.$oldValue2.'" to "'.$newvalue2.'"';
    $SigningAcceptedComment = 'Signing Assignment Comment ('.$SignedDateTime.') is Changed from "'.$oldValue3.'" to "'.$newValue3.'"';
    $DayOfSigningComment = 'Confirmation Signing Comment ('.$SignedDateTime.') is Changed from "'.$oldValue4.'" to "'.$newValue4.'"';
    $PostSigningComment = 'Post Signing Comment ('.$SignedDateTime.') is Changed from "'.$oldValue5.'" to "'.$newValue5.'"';
    $IsSigningDone = 'Signing Done is Changed from "'.$SigningDoneOlder.'" to "'.$IsSigningDoneNewValue.'"';
    $IsShipped = 'Shipping Done is Changed from "'.$Shippedoldolder.'" to "'.$IsShippedNewValue.'"';
    $IsSigningAccepted = 'SigningAccepted is Changed from "'.$SigningAcceptOlder.'" to "'.$SigningAcceptNewValue.'"';
    $IsDayOfSigning = 'Confirmation Day of Signing is Changed from "'.$DayOfSigningOlder.'" to "'.$DayOfSigningNewValue.'"';
    $PostSigning = 'PostSigning is Changed from "'.$PostSigningOlder.'" to "'.$PostSigningNewValue.'"';
 



	  if($oldvalue1!=$newvalue1){
	  	//echo '<pre>';print_r($newvalue1);exit;
	    $data1['ModuleName']='Signing';
	    $data1['Content']= $signedFeedback;
        $data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']= date('y-m-d H:i:s');
        $data1['TableName']='tOrderSign';
        $data1['OrderUID'] = $OrderUID;
        $data1['UserUID']=$UserUID; 
        $this->common_model->Audittrail_insert($data1);
       
	}
       if($oldValue2!=$newvalue2){
	    $data1['ModuleName']='Signing';
	    $data1['Content']= $SigningComment;
        $data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']= date('y-m-d H:i:s');
        $data1['TableName']='tOrderSign';
        $data1['OrderUID'] = $OrderUID;
        $data1['UserUID']=$UserUID; 
		$this->common_model->Audittrail_insert($data1);
      }
       if($oldValue3!=$newValue3){
	    $data1['ModuleName']='Signing';
	    $data1['Content']= $SigningAcceptedComment;
        $data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']= date('y-m-d H:i:s');
        $data1['TableName']='tOrderSign';
        $data1['OrderUID'] = $OrderUID;
        $data1['UserUID']=$UserUID; 
		$this->common_model->Audittrail_insert($data1);
      }
       if($oldValue4!=$newValue4){
	    $data1['ModuleName']='Signing';
	    $data1['Content']= $DayOfSigningComment;
        $data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']= date('y-m-d H:i:s');
        $data1['TableName']='tOrderSign';
        $data1['OrderUID'] = $OrderUID;
        $data1['UserUID']=$UserUID; 
		$this->common_model->Audittrail_insert($data1);
      }
      if($oldValue5!=$newValue5){
	    $data1['ModuleName']='Signing';
	    $data1['Content']= $PostSigningComment;
        $data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']= date('y-m-d H:i:s');
        $data1['TableName']='tOrderSign';
        $data1['OrderUID'] = $OrderUID;
        $data1['UserUID']=$UserUID; 
		$this->common_model->Audittrail_insert($data1);
      }
       if($SigningDoneOlder!=$IsSigningDoneNewValue){
	    $data1['ModuleName']='Signing';
	    $data1['Content']= $IsSigningDone;
        $data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']= date('y-m-d H:i:s');
        $data1['TableName']='tOrderSign';
        $data1['OrderUID'] = $OrderUID;
        $data1['UserUID']=$UserUID; 
		$this->common_model->Audittrail_insert($data1);
      }
       if($Shippedoldolder!=$IsShippedNewValue){
	    $data1['ModuleName']='Signing';
	    $data1['Content']= $IsShipped;
        $data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']= date('y-m-d H:i:s');
        $data1['TableName']='tOrderSign';
        $data1['OrderUID'] = $OrderUID;
        $data1['UserUID']=$UserUID; 
		$this->common_model->Audittrail_insert($data1);
      }
       if($SigningAcceptOlder!=$SigningAcceptNewValue){
	    $data1['ModuleName']='Signing';
	    $data1['Content']= $IsSigningAccepted;
        $data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']= date('y-m-d H:i:s');
        $data1['TableName']='tOrderSign';
        $data1['OrderUID'] = $OrderUID;
        $data1['UserUID']=$UserUID; 
		$this->common_model->Audittrail_insert($data1);
      }

      if($DayOfSigningOlder!=$DayOfSigningNewValue){
	    $data1['ModuleName']='Signing';
	    $data1['Content']= $IsDayOfSigning;
        $data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']= date('y-m-d H:i:s');
        $data1['TableName']='tOrderSign';
        $data1['OrderUID'] = $OrderUID;
        $data1['UserUID']=$UserUID; 
		$this->common_model->Audittrail_insert($data1);
      }
       if($PostSigningOlder!=$PostSigningNewValue){
	    $data1['ModuleName']='Signing';
	    $data1['Content']= $PostSigning;
        $data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
        $data1['DateTime']= date('y-m-d H:i:s');
        $data1['TableName']='tOrderSign';
        $data1['OrderUID'] = $OrderUID;
        $data1['UserUID']=$UserUID; 
		$this->common_model->Audittrail_insert($data1);
      }
	  return true;
}

function GetScheduleDate($OrderUID){
	$this->db->select('*');
	$this->db->from('tOrderSchedule');
	$this->db->where('OrderUID',$OrderUID);
	$query = $this->db->get()->row();
     return $query;
}


function getScheduleData($AbstractorUID)
{
		$status = array('Reschedule', 'Assign');
		$this->db->select('torders.OrderUID,torders.OrderNumber,torders.LoanNumber,tOrderSchedule.*')->from('tOrderSchedule');
		$this->db->join('torders','torders.OrderUID = tOrderSchedule.OrderUID','left');
		$this->db->where('tOrderSchedule.AbstractorUID',$AbstractorUID);
		$this->db->where_in("ScheduleStatus",$status);
		$result = $this->db->get()->result();  
		return $result;

}


	 function SaveShippingDet($post,$UserUID)
	{
		$data['SenderType'] =$post['SenderType'];
		$data['RecipientType'] =$post['RecipientType'];

		$data['RecipientBorrowerUID'] =$post['RecipientBorrowerUID'];
		if ($data['SenderType'] == 'Customer') 
		{
			$CustomerUID = $post['SenderCustomerUID'];
			$data['SenderCustomerUID'] = $post['SenderCustomerUID'];
			$CustomerDetails = $this->db->select('*')->from('mcustomers')->where('CustomerUID',$CustomerUID)->get()->row();
			$data['SenderCompanyName'] = $CustomerDetails->CustomerName;
		}
		else if ($data['SenderType'] == 'Borrower') 
		{
			$SenderBorrowerUID = $post['SenderBorrowerUID'];
			$BorrowerDetails = $this->db->select('*')->from('torderpropertyroles')->where('id',$SenderBorrowerUID)->get()->row();
			$data['SenderBorrowerUID'] = $post['SenderBorrowerUID'];
			$data['SenderCompanyName'] = $BorrowerDetails->PRName;
			
		}
		else if ($data['SenderType'] == 'Parties') 
		{
			$SenderOrderClosingPartyUID = $post['SenderOrderClosingPartyUID'];
			$PartiesDetails = $this->db->select('*')->from('tOrderParties')->where('OrderClosingPartyUID',$SenderOrderClosingPartyUID)->get()->row();
			$data['SenderOrderClosingPartyUID'] = $post['SenderOrderClosingPartyUID'];
			$data['SenderCompanyName'] = $PartiesDetails->PartyName;
			
		}
		else
		{
			$data['SenderCompanyName'] = $post['SenderCompanyName'];
			$data['SenderAddress1'] = $post['SenderAddress1'];
			$data['SenderAddress2'] = $post['SenderAddress2'];
			$data['SenderCityName'] = $post['SenderCityName'];
			$data['SenderStateCode'] = $post['SenderStateCode'];
			$data['SenderZipCode'] = $post['SenderZipCode'];
		}

		
		if ($data['RecipientType'] == 'Customer') {
			$CustomerUID = $post['RecipientCustomerUID'];
			$data['RecipientCustomerUID'] =$post['RecipientCustomerUID'];
			$CustomerDetails = $this->db->select('*')->from('mcustomers')->where('CustomerUID',$CustomerUID)->get()->row();
			$data['RecipientCompanyName'] = $CustomerDetails->CustomerName;
			// echo '<pre>'; print_r($CustomerDetails->CustomerName); exit;
		}
		else if ($data['RecipientType'] == 'Borrower') {
			$RecipientBorrowerUID = $post['RecipientBorrowerUID'];
			$BorrowerDetails = $this->db->select('*')->from('torderpropertyroles')->where('id',$RecipientBorrowerUID)->get()->row();
			$data['RecipientBorrowerUID'] = $post['RecipientBorrowerUID'];
			$data['RecipientCompanyName'] = $BorrowerDetails->PRName;
			
		}
		else if ($data['RecipientType'] == 'Parties') {
			$RecipientOrderClosingPartyUID = $post['RecipientOrderClosingPartyUID'];
			$PartiesDetails = $this->db->select('*')->from('tOrderParties')->where('OrderClosingPartyUID',$RecipientOrderClosingPartyUID)->get()->row();
			$data['RecipientOrderClosingPartyUID'] = $post['RecipientOrderClosingPartyUID'];
			$data['RecipientCompanyName'] = $PartiesDetails->PartyName;
			
		}
		else{
			$data['RecipientCompanyName'] = $post['RecipientCompanyName'];
			$data['RecipientAddress1'] = $post['RecipientAddress1'];
			$data['RecipientAddress2'] = $post['RecipientAddress2'];
			$data['RecipientCityName'] = $post['RecipientCityName'];
			$data['RecipientStateCode'] = $post['RecipientStateCode'];
			$data['RecipientZipCode'] = $post['RecipientZipCode'];
		}
		$data['ServiceType'] = $post['ServiceType'];
		$data['ShippingReasonUID'] = $post['ShippingReasonUID'];
		$data['TrackingNumber'] = $post['TrackingNumber'];
		$data['Notes'] =$post['Notes'];
		$data['OrderUID'] =$post['OrderUID'];
		$data['IsShipped'] =$post['IsShipped'] ? 1 : 0;
		$data['ShippingStatus'] =$post['ShippingStatus'];
		
		
		// echo '<pre>'; print_r($data); exit;
		if ($post['ShippingUID'] == '' || empty($post['ShippingUID'])) {

			$this->db->insert('tOrderShipping',$data);

		    $OrderUID = $post['OrderUID'];

			$this->db->select('*');
			$this->db->from('tOrderShipping');
			$this->db->where('OrderUID',$OrderUID);
			$query = $this->db->get()->row();

			$IsShippeddata = ($query->IsShipped == 1) ? 'Yes' : 'No';

			$senderDetails = 'Sender Type: '.$query->SenderType.'<br>Sender Name: '.$query->SenderCompanyName.'<br>Shipping Status:'.$query->ShippingStatus.'<br>Instruction: '.$query->Notes.'<br>IsShipped: '.$IsShippeddata.'<br>Tracking Number: '.$query->TrackingNumber.'  Inserted';

			$RecipientDetails = 'Recipient type: '.$query->RecipientType.'<br>Recipient Name: '.$query->RecipientCompanyName.'<br>Shipping Status:'.$query->ShippingStatus.'<br>Instruction: '.$query->Notes.'<br>IsShipped: '.$IsShippeddata.'<br>Tracking Number: '.$query->TrackingNumber.' Inserted';           

			$data1['ModuleName']='Sender Shipping';
			$data1['Content']= $senderDetails;
			$data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']= date('y-m-d H:i:s');
			$data1['TableName']='tOrderShipping';
			$data1['OrderUID'] = $OrderUID;
			$data1['UserUID']=$UserUID; 
			$this->common_model->Audittrail_insert($data1);

			$data1['ModuleName']='Recipient Shipping';
			$data1['Content']= $RecipientDetails;
			$data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']= date('y-m-d H:i:s');
			$data1['TableName']='tOrderShipping';
			$data1['OrderUID'] = $OrderUID;
			$data1['UserUID']=$UserUID; 
			$this->common_model->Audittrail_insert($data1);

			if ($this->db->affected_rows() > 0) {
				return 1;
			}
			else{
				return 0;
			}
		}
		else{

			$OrderUID = $post['OrderUID'];
            $this->db->select('*');
            $this->db->from('tOrderShipping');
            $this->db->where('ShippingUID',$post['ShippingUID']);
            $this->db->where('OrderUID',$OrderUID);
            $querydata = $this->db->get()->row();

            $SelectSenderOldValue = $querydata->SenderType;
            $SelectSenderNameold = $querydata->SenderCompanyName;
            $ShippingStatusOld = $querydata->ShippingStatus;
            $RecipitentTypeold = $querydata->RecipientType;
            $TrackingNumberOld = $querydata->TrackingNumber;
            $IsShippedOld = ($querydata->IsShipped == 1) ? 'Yes' : 'No';
            $NotesOld = $querydata->Notes;

			$this->db->where('ShippingUID',$post['ShippingUID']);
			$this->db->update('tOrderShipping',$data);
			
			 $this->db->select('*');
            $this->db->from('tOrderShipping');
            $this->db->where('ShippingUID',$post['ShippingUID']);
            $this->db->where('OrderUID',$OrderUID);
            $query = $this->db->get()->row();

            $SelectSenderNewValue = $query->SenderType;
            $SelectSenderNameNew = $query->SenderCompanyName;
            $ShippingStatusNew = $query->ShippingStatus;
            $RecipitentTypeNew = $query->RecipientType;
            $TrackingNumberNew = $query->TrackingNumber;
            $IsShippedNew = ($query->IsShipped == 1) ? 'Yes' : 'No';
            $NotesNew = $query->Notes;

            $SelectSender = 'Sender Type Changed from '.$SelectSenderOldValue.' to '.$SelectSenderNewValue;
            $SenderCompanyName = 'Sender Name Changed from '.$SelectSenderNameold.' to '.$SelectSenderNameNew;
            $RecipitentType = 'Recipitent Type Changed from '.$RecipitentTypeold.' to '.$RecipitentTypeNew;
            $ShippingStatus = 'Shipping Status Changed From '.$ShippingStatusOld.' to '.$ShippingStatusNew;
            $TrackingNumber = 'Tracking Number Changed From  '.$TrackingNumberOld.' to '.$TrackingNumberNew;
            $IsShipped = 'Is Shipping is  Changed From '.$IsShippedOld.' to '.$IsShippedNew;
            $Notes = 'Commend Changed From '.$NotesOld.' to '.$NotesNew;

             
            if($SelectSenderOldValue!=$SelectSenderNewValue)
            {

            $data1['ModuleName']='Sender Shipping';
			$data1['Content']= $SelectSender;
			$data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']= date('y-m-d H:i:s');
			$data1['TableName']='tOrderShipping';
			$data1['OrderUID'] = $OrderUID;
			$data1['UserUID']=$UserUID; 
			$this->common_model->Audittrail_insert($data1);
		}
		   
		if($SelectSenderNameold!=$SelectSenderNameNew)
		{
			$data1['ModuleName']='Sender Shipping';
			$data1['Content']= $SenderCompanyName;
			$data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']= date('y-m-d H:i:s');
			$data1['TableName']='tOrderShipping';
			$data1['OrderUID'] = $OrderUID;
			$data1['UserUID']=$UserUID; 
			$this->common_model->Audittrail_insert($data1);
		}
		
            if($RecipitentTypeold!=$RecipitentTypeNew)
            {
            $data1['ModuleName']='Recipitent Shipping';
			$data1['Content']= $RecipitentType;
			$data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']= date('y-m-d H:i:s');
			$data1['TableName']='tOrderShipping';
			$data1['OrderUID'] = $OrderUID;
			$data1['UserUID']=$UserUID; 
			$this->common_model->Audittrail_insert($data1);
           }

             if($ShippingStatusOld!=$ShippingStatusNew)
            {
            $data1['ModuleName']='Shipping';
			$data1['Content']= $ShippingStatus;
			$data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']= date('y-m-d H:i:s');
			$data1['TableName']='tOrderShipping';
			$data1['OrderUID'] = $OrderUID;
			$data1['UserUID']=$UserUID; 
			$this->common_model->Audittrail_insert($data1);
           }

            if($TrackingNumberOld!=$TrackingNumberNew)
            {
            $data1['ModuleName']='Shipping';
			$data1['Content']= $TrackingNumber;
			$data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']= date('y-m-d H:i:s');
			$data1['TableName']='tOrderShipping';
			$data1['OrderUID'] = $OrderUID;
			$data1['UserUID']=$UserUID; 
			$this->common_model->Audittrail_insert($data1);
           }


            if($IsShippedOld!=$IsShippedNew)
            {
            $data1['ModuleName']='Shipping';
			$data1['Content']= $IsShipped;
			$data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']= date('y-m-d H:i:s');
			$data1['TableName']='tOrderShipping';
			$data1['OrderUID'] = $OrderUID;
			$data1['UserUID']=$UserUID; 
			$this->common_model->Audittrail_insert($data1);
           }
          
           if($NotesOld!=$NotesNew)
            {
            $data1['ModuleName']='Shipping';
			$data1['Content']= $Notes;
			$data1['IpAddreess']= $_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']= date('y-m-d H:i:s');
			$data1['TableName']='tOrderShipping';
			$data1['OrderUID'] = $OrderUID;
			$data1['UserUID']=$UserUID; 
			$this->common_model->Audittrail_insert($data1);
           }

			return 2;
			
		}
	}


  /**
    *@description Function to fetch Fee Variance Data
    *
    * @param $Post (Array)
    * 
    * @throws no exception
    * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
    * @return Mixed(Array) 
    * @since 27.2.2020 
    * @version Open Action Report 
    *
  */ 
  function _getClosingQueueOrders($post)
  {
    /*Query*/



    $statuses[] = $this->config->item('keywords')['Order Assigned'];
    $statuses[] = $this->config->item('keywords')['Order Work In Progress'];
    $statuses[] = $this->config->item('keywords')['New Order'];
    $statuses[] = $this->config->item('keywords')['Partial Review Complete'];
    $statuses[] = $this->config->item('keywords')['Partial Draft Complete'];
    $statuses[] = $this->config->item('keywords')['Review In Progress'];
    $statuses[] = $this->config->item('keywords')['Reopened Order'];




    $this->db->from('torders');


    $this->db->join("tOrderQueues","tOrderQueues.OrderUID=torders.OrderUID AND tOrderQueues.QueueStatus = 'Pending'");
    $this->db->join("mQueues","tOrderQueues.QueueUID=mQueues.QueueUID");
    $this->db->join('musers b','tOrderQueues.RaisedByUserUID = b.UserUID','left');
    $this->db->join('moverridereasons','tOrderQueues.RaisedReasonUID = moverridereasons.OverrideReasonUID','left');

    $this->db->join('morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID', 'left'); 
    $this->db->join('mcustomers', 'mcustomers.CustomerUID = torders.CustomerUID', 'left'); 
    $this->db->join('msubproducts', 'msubproducts.SubProductUID = torders.SubProductUID', 'left'); 
    $this->db->join('mproducts', 'mproducts.ProductUID = msubproducts.ProductUID', 'left');  
    $this->db->join('mProjects', 'mProjects.ProjectUID = torders.ProjectUID', 'left'); 
    $this->db->join('tOrderClosing', 'tOrderClosing.OrderUID = torders.OrderUID', 'left'); 
    $this->db->join('tOrderSign', 'tOrderSign.OrderUID = torders.OrderUID AND tOrderSign.SigningStatus NOT IN ("Cancel")', 'left'); 
    $this->db->join('tOrderSchedule', 'tOrderSign.ScheduleUID = tOrderSchedule.ScheduleUID', 'left'); 
    $this->db->join('torderabstractor', 'torderabstractor.AbstractorOrderUID = tOrderSchedule.AbstractorOrderUID', 'left'); 
    $this->db->join('morderstatus', 'morderstatus.StatusUID = torders.StatusUID', 'left'); 
    $this->db->join('torderpropertyroles', 'torderpropertyroles.OrderUID = torders.OrderUID AND torderpropertyroles.PropertyRoleUID = "'.$this->config->item('Propertyroles')['Borrowers'].'"', 'left');
    $this->db->where_in('torders.StatusUID', $statuses);




    $this->db->select("CustomerNumber,CustomerName,LoanNumber, OrderNumber, AltORderNumber, torders.StatusUID,torders.PropertyZipcode, torders.OrderUID, torders.OrderEntryDatetime as OrderEntryDatetime, morderpriority.PriorityName,morderpriority.TAT,morderpriority.PriorityUID, mproducts.ProductName, mproducts.ProductCode, msubproducts.SubProductCode, msubproducts.SubProductName, DATE_FORMAT(torders.OrderDueDatetime, '%m/%d/%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(torders.OrderEntryDatetime, '%m/%d/%Y %H:%i:%s') as OrderEntryDatetime ,TRIM(CONCAT_WS(' ',TRIM(torders.PropertyAddress1),TRIM(torders.PropertyAddress2), TRIM(torders.PropertyCityName), TRIM(torders.PropertyCountyName), TRIM(torders.PropertyStateCode), TRIM(torders.PropertyZipcode))) AS PropertyAddress , torders.PropertyStateCode,torders.IsInhouseExternal,torders.PropertyCityName,torders.PropertyCountyName,torders.CustomerAmount, mProjects.ProjectName,GROUP_CONCAT(Distinct PRName SEPARATOR ', ') AS BorrowerNames,torders.CustomerUID,torders.SubProductUID,mproducts.IsSelfAssign,TRIM(CONCAT_WS(' ',TRIM(tOrderClosing.SigningAddress1),', ',TRIM(tOrderClosing.SigningAddress2), ', ',TRIM(tOrderClosing.SigningCityName), ', ',TRIM(tOrderClosing.SigningStateCode), ' ' ,TRIM(tOrderClosing.SigningZipCode))) AS SigningLocation,tOrderSign.SignedDateTime, tOrderClosing.SigningStateCode", false);
    $this->db->select('(CASE WHEN tOrderSign.ScheduleUID IS NOT NULL THEN (SELECT (CASE WHEN mabstractor.AbstractorCompanyName IS NOT NULL THEN mabstractor.AbstractorCompanyName ELSE CONCAT_WS(" ", mabstractor.AbstractorFirstName, mabstractor.AbstractorLastName) END) AS vname FROM tOrderSchedule JOIN torderabstractor ON torderabstractor.AbstractorOrderUID = tOrderSchedule.AbstractorOrderUID JOIN mabstractor ON mabstractor.AbstractorUID = torderabstractor.AbstractorUID WHERE tOrderSchedule.ScheduleUID = tOrderSign.ScheduleUID LIMIT 1) ELSE "" END)  AS VendorName', false);
    $this->db->select('(CASE WHEN tOrderSign.ScheduleUID IS NOT NULL THEN (SELECT mabstractorcontact.ContactName FROM tOrderSchedule JOIN torderabstractor ON torderabstractor.AbstractorOrderUID = tOrderSchedule.AbstractorOrderUID JOIN mabstractorcontact ON mabstractorcontact.AbstractorContactUID = torderabstractor.ContactUID WHERE tOrderSchedule.ScheduleUID = tOrderSign.ScheduleUID LIMIT 1) ELSE "" END)  AS VendorContactName', false);
    $this->db->select("mQueues.QueueName, b.UserName AS RaisedBy, torderabstractor.OrderStatus");



    /********* Order By Section ************/
    $this->db->_protect_identifiers=false;
    $this->db->order_by('-tOrderSign.SignedDateTime ASC, torders.OrderUID ASC');
    $this->db->order_by('FIELD(`torders`.`PriorityUID`,3,1) DESC');
    $this->db->_protect_identifiers=true;


    

    /********* Group BY Section ************/
    $this->db->group_by('torders.OrderUID');
    $this->db->group_by('tOrderQueues.QueueUID');


    /* ***** Filter Search ****** */
    if (isset($post['AbstractorUID']) && !empty($post['AbstractorUID'])) 
    {
    	$this->db->where('torderabstractor.AbstractorUID', $post['AbstractorUID']);
    }

    if (!empty($post['search_value'])) {
    	$like = "";
    	foreach ($post['column_search'] as $key => $item) { /* loop column*/
    		/* if datatable send POST for search*/
              if ($key === 0) { // first loop
              	$like .= "( ".$item." LIKE '%".$post['search_value']."%' ";
              } else {
              	$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
              }
          }
          $like .= ") ";
          $this->db->where($like, null, false);
      }

      return $this->db->get()->result();

  }


 

 

  //GET SCEHDULE Borrowers 
  /**
   * For Reschedule API
   * shruti.vs@avanzegroup.com
   */
  public function getScheduleBorrowers($ScheduleUID)
	{
		$this->db->select('*');
		$this->db->from('tOrderScheduleBorrower');
		$this->db->join('torderpropertyroles', 'tOrderScheduleBorrower.BorrowerUID = torderpropertyroles.Id');
		$this->db->join('mpropertyroles', 'torderpropertyroles.PropertyRoleUID = mpropertyroles.PropertyRoleUID');
		$this->db->where('tOrderScheduleBorrower.ScheduleUID', $ScheduleUID);
		return $this->db->get()->result();
	}

	function GetOrderPropertyRolesByID($BorrowerID) {
		$this->db->select ( '*' ); 
		$this->db->from ( 'torderpropertyroles' );
		$this->db->where ('torderpropertyroles.Id', $BorrowerID);
		$query = $this->db->get();
		return $query->row();
	}

	function getSigningAddress($OrderUID,$BorrowerID)
	{
		$signstatus = $this->db->select('IsSigningAddress')->from('tOrderClosing')->where('OrderUID',$OrderUID)->get()->row();
		if($signstatus->IsSigningAddress == 'Property Address')
		{
			$result = $this->common_model->get_orderdetails($OrderUID);
			$data = array(
				'Address1'=>$result->PropertyAddress1,
				'ZipCode'=>$result->PropertyZipcode,
				'CityName'=>$result->PropertyCityName,
				'CountyName'=>$result->PropertyCountyName,
				'StateCode'=>$result->PropertyStateCode);
		}
		else if($signstatus->IsSigningAddress == 'Current Address')
		{
			$result = $this->db->select('MailingAddress1,MailingZipCode,MailingCityName,MailingCountyName,MailingStateCode')->from('tOrderClosing')->where(array('OrderUID'=>$OrderUID,'IsSigningAddress'=>'Current Address'))->get()->row();
			$data = array(
				'Address1'=>$result->MailingAddress1,
				'ZipCode'=>$result->MailingZipCode,
				'CityName'=>$result->MailingCityName,
				'CountyName'=>$result->MailingCountyName,
				'StateCode'=>$result->MailingStateCode);
		}
		else if($signstatus->IsSigningAddress == 'Delivery Address') 
		{
			
			$this->db->select('IsDeliveryAddress');
			$this->db->from('torderpropertyroles');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('Id',$BorrowerID);
			$Deliverystatus = $this->db->get()->row();

			if($Deliverystatus->IsDeliveryAddress == 'Property Address')
			{
				$result = $this->common_model->get_orderdetails($OrderUID);
				$data = array(
					'Address1'=>$result->PropertyAddress1,
					'ZipCode'=>$result->PropertyZipCode,
					'CityName'=>$result->PropertyCityName,
					'CountyName'=>$result->PropertyCountyName,
					'StateCode'=>$result->PropertyStateCode);
			}
			else if($Deliverystatus->IsDeliveryAddress == 'Mailing Address')
			{
				$result = $this->db->select('MailingAddress1,MailingZipCode,MailingCityName,MailingCountyName,MailingStateCode')->from('torderpropertyroles')->where(array('OrderUID'=>$OrderUID,'id'=>$BorrowerID))->get()->row();
				$data = array(
					'Address1'=>$result->MailingAddress1,
					'ZipCode'=>$result->MailingZipCode,
					'CityName'=>$result->MailingCityName,
					'CountyName'=>$result->MailingCountyName,
					'StateCode'=>$result->MailingStateCode);
			}
			else if($Deliverystatus->IsDeliveryAddress == 'Others') 
			{
				$result = $this->db->select('DeliveryAddress1,DeliveryZipCode,DeliveryCityName,DeliveryCountyName,DeliveryStateCode')->from('torderpropertyroles')->where(array('OrderUID'=>$OrderUID,'id'=>$BorrowerID))->get()->row();
				$data = array('Address1' => $result->DeliveryAddress1,
					'ZipCode' => $result->DeliveryZipCode,
					'CityName' => $result->DeliveryCityName,
					'CountyName' => $result->DeliveryCountyName,
					'StateCode' => $result->DeliveryStateCode);
			}
		}
		else if($signstatus->IsSigningAddress == 'Others') 
		{
			$result = $this->db->select('SigningAddress1,SigningZipCode,SigningCityName,SigningCountyName,SigningStateCode')->from('tOrderClosing')->where(array('OrderUID'=>$OrderUID,'IsSigningAddress'=>'Others'))->get()->row();
			$data = array('Address1' => $result->SigningAddress1,
				'ZipCode' => $result->SigningZipCode,
				'CityName' => $result->SigningCityName,
				'CountyName' => $result->SigningCountyName,
				'StateCode' => $result->SigningStateCode);
		}
		return $data;
	}


	function CheckSigningAddress($post){
    
		$OrderUID = $post['OrderUID'];
		$BorrowerUID = $post['BorrowerUID'];
		
		$this->db->select('*')->from('torders')->where('OrderUID', $OrderUID);
		$torders = $this->db->get()->row();
	   
		if($torders){
		  /* Property Address */
		  $PropertyAddress1 = $torders->PropertyAddress1;
		  $PropertyAddress2 = $torders->PropertyAddress2;
		  $PropertyCityName = $torders->PropertyCityName;
		  $PropertyCountyName = $torders->PropertyCountyName;
		  $PropertyStateCode = $torders->PropertyStateCode;
		  $PropertyZipcode = $torders->PropertyZipcode;
		  $PropertyAddress = $PropertyAddress1.' '.$PropertyAddress2;
	   
		  /* Mailing Addess*/
		  $this->db->select('*')->from('torderpropertyroles')->where('OrderUID', $OrderUID)->where('Id', $BorrowerUID);
		  $torderpropertyroles = $this->db->get()->row();
		  $MailingAddress1 = $torderpropertyroles->MailingAddress1;
		  $MailingAddress2 = $torderpropertyroles->MailingAddress2;
		  $MailingCityName = $torderpropertyroles->MailingCityName;
		  $MailingCountyName = $torderpropertyroles->MailingCountyName;
		  $MailingStateCode = $torderpropertyroles->MailingStateCode;
		  $MailingZipCode = $torderpropertyroles->MailingZipCode;
		  $MailingAddress = $MailingAddress1.' '.$MailingAddress2;
	   
		  /*Signing Address */
		  $ClosingAddress1 = $post['SigningAddress1'];
		  $ClosingAddress2 = $post['SigningAddress2'];
		  $ClosingCityName = $post['SigningCityName'];
		  $ClosingCountyName = $post['SigningCountyName'];
		  $ClosingStateCode = $post['SigningStateCode'];
		  $ClosingZipcode = $post['SigningZipCode'];
		  $ClosingAddress = $ClosingAddress1.' '.$ClosingAddress2;
	   
		  /* Check for Signing with Property Address - if matched enabled property radio button in summary for the particular borrower*/
		  if(
		    strtolower(preg_replace('/\s+/','',$ClosingZipcode))   == strtolower(preg_replace('/\s+/','',$PropertyZipcode)) &&
		    strtolower(preg_replace('/\s+/','',$ClosingStateCode)) == strtolower(preg_replace('/\s+/','',$PropertyStateCode)) &&
		    strtolower(preg_replace('/\s+/','',$ClosingCityName))  == strtolower(preg_replace('/\s+/','',$PropertyCityName)) &&
		    strtolower(preg_replace('/\s+/','',$ClosingAddress))  == strtolower(preg_replace('/\s+/','',$PropertyAddress)) 
		  )
		  {
		    $address['SigningType']  = 'Property';
		    $address['LocationType']  = 'SubjectProperty';
		    $address['LocationTypeDesc']  = '';
		  }
		  /* Check for Signing with Mailing Address for the borrower-if matched enabled mailing radio button in summary for the particular borrower*/
		  else if(
		    strtolower(preg_replace('/\s+/','',$ClosingZipcode))    == strtolower(preg_replace('/\s+/','',$MailingZipCode)) &&
		    strtolower(preg_replace('/\s+/','',$ClosingStateCode))  == strtolower(preg_replace('/\s+/','',$MailingStateCode)) &&
		    strtolower(preg_replace('/\s+/','',$ClosingCityName))   == strtolower(preg_replace('/\s+/','',$MailingCityName)) &&
		    strtolower(preg_replace('/\s+/','',$ClosingAddress))   == strtolower(preg_replace('/\s+/','',$MailingAddress))  
	   
		  ){ 
		    $address['SigningType']  = 'Mailing';
		    $address['LocationType']  = 'SignerCurrentResidence';
		    $address['LocationTypeDesc']  = '';
		  }
		  else 
		  { 
		    $address['SigningType']  = 'Other';
		    $address['LocationType']  = 'Other';
		    $address['LocationTypeDesc']  = 'Other';
		  }
	   
		  return $address;
		}
	     }


	function UpdateSigningDetails($OrderUID,$ClosingDetails,$BorrowerUID='')
	{
		$this->db->select('*')->from('torders')->where('OrderUID', $OrderUID);
		$torders = $this->db->get()->row();
	   
		if($torders){
		  /* Property Address */
		  $PropertyAddress1 = $torders->PropertyAddress1;
		  $PropertyAddress2 = $torders->PropertyAddress2;
		  $PropertyCityName = $torders->PropertyCityName;
		  $PropertyCountyName = $torders->PropertyCountyName;
		  $PropertyStateCode = $torders->PropertyStateCode;
		  $PropertyZipcode = $torders->PropertyZipcode;
	   
		  /* Mailing Addess*/
		  $this->db->select('*')->from('torderpropertyroles')->where('OrderUID', $OrderUID)->where('Id', $BorrowerUID);
		  $torderpropertyroles = $this->db->get()->row();
		  $MailingAddress1 = $torderpropertyroles->MailingAddress1;
		  $MailingAddress2 = $torderpropertyroles->MailingAddress2;
		  $MailingCityName = $torderpropertyroles->MailingCityName;
		  $MailingCountyName = $torderpropertyroles->MailingCountyName;
		  $MailingStateCode = $torderpropertyroles->MailingStateCode;
		  $MailingZipCode = $torderpropertyroles->MailingZipCode;
	   
		  /*Signing Address */
		  $ClosingAddress1 = $ClosingDetails['ClosingAddress1'];
		  $ClosingAddress2 = $ClosingDetails['ClosingAddress2'];
		  $ClosingCityName = $ClosingDetails['ClosingCityName'];
		  $ClosingCountyName = $ClosingDetails['ClosingCountyName'];
		  $ClosingStateCode = $ClosingDetails['ClosingStateCode'];
		  $ClosingZipcode = $ClosingDetails['ClosingZipcode'];
		  $SpecialInstruction = $ClosingDetails['SpecialInstruction'];
	   
		  //Fix to prevent inserting county as empty 
		  if(empty($ClosingCountyName))
		  {
		      $this->load->model('order_complete/order_complete_model');
		      $CountyName = $this->order_complete_model->getCountyDetail($ClosingZipcode);
		      $ClosingCountyName = $CountyName[0]->CountyName;
		  }
	   
		  if(
			 strtolower(preg_replace('/\s+/','',$ClosingZipcode))   == strtolower(preg_replace('/\s+/','',$PropertyZipcode)) &&
			 strtolower(preg_replace('/\s+/','',$ClosingStateCode)) == strtolower(preg_replace('/\s+/','',$PropertyStateCode)) &&
			 strtolower(preg_replace('/\s+/','',$ClosingCityName))  == strtolower(preg_replace('/\s+/','',$PropertyCityName)) &&
			 strtolower(preg_replace('/\s+/','',$ClosingAddress1))  == strtolower(preg_replace('/\s+/','',$PropertyAddress1)) 
			 //&& strtolower(preg_replace('/\s+/','',$ClosingAddress2))    = strtolower(preg_replace('/\s+/','',$PropertyAddress2)) 
			// && strtolower(preg_replace('/\s+/','',$ClosingCountyName))  = strtolower(preg_replace('/\s+/','',$PropertyCountyName)) 
	   
		  ){ /* Check for Signing with Property Address - if matched enabled property radio button in summary for the particular borrower*/
	   
		      $address['IsSigningAddress']  = 'property';
		      /*Notes:*/
		      $Notes = "Signing address is same as property address for borrower".$torderpropertyroles->PRName.".<br/>";
	   
		  } else if (
				strtolower(preg_replace('/\s+/','',$ClosingZipcode))    == strtolower(preg_replace('/\s+/','',$MailingZipCode)) &&
				strtolower(preg_replace('/\s+/','',$ClosingStateCode))  == strtolower(preg_replace('/\s+/','',$MailingStateCode)) &&
				strtolower(preg_replace('/\s+/','',$ClosingCityName))   == strtolower(preg_replace('/\s+/','',$MailingCityName)) &&
				strtolower(preg_replace('/\s+/','',$ClosingAddress1))   == strtolower(preg_replace('/\s+/','',$MailingAddress1)) 
				 // && strtolower(preg_replace('/\s+/','',$ClosingAddress2))   = strtolower(preg_replace('/\s+/','',$MailingAddress2)) 
				 // && strtolower(preg_replace('/\s+/','',$ClosingCountyName)) = strtolower(preg_replace('/\s+/','',$MailingCountyName)) 
	   
		  ){ /* Check for Signing with Mailing Address for the borrower - if matched enabled mailing radio button in summary for the particular borrower*/
	   
		     $address['IsSigningAddress']  = 'mailing';
		      /*Notes:*/
		     $Notes = "Signing address is same as mailing address for borrower ".$torderpropertyroles->PRName.".<br/>";
	   
		  } else { /* Not matched with property and mailing - then enable other and update signing address in torderpropertyroles */
	   
			$address = [
					 'SigningAddress1'   => $ClosingAddress1,
					 'SigningAddress2'   => $ClosingAddress2,
					 'SigningCityName'   => $ClosingCityName,
					 'SigningCountyName' => $ClosingCountyName,
					 'SigningStateCode'  => $ClosingStateCode,
					 'SigningZipCode'    => $ClosingZipcode,
					 'IsSigningAddress'  => 'others'
	   
				    ];
	   
		      /*Notes: */
		      $Notes = "Signing address is not matched with property & mailing address for borrower ". $torderpropertyroles->PRName.".<br/>";
		      
		      if(
			      $ClosingDetails['ClosingAddress1']   != $torderpropertyroles->SigningAddress1
			   || $ClosingDetails['ClosingAddress2']   != $torderpropertyroles->SigningAddress2
			   || $ClosingDetails['ClosingCityName']   != $torderpropertyroles->SigningCityName
			   || $ClosingDetails['ClosingStateCode']  != $torderpropertyroles->SigningStateCode
			   || $ClosingDetails['ClosingZipcode']    != $torderpropertyroles->SigningStateCode
			   || $ClosingDetails['ClosingCountyName'] != $torderpropertyroles->SigningCountyName
			 )
		      {
			   $Notes .= "Address1 is changed to ".(!empty($ClosingDetails['ClosingAddress1']) ? $ClosingDetails['ClosingAddress1'] : '-')." from ".(!empty($torderpropertyroles->SigningAddress1) ? $torderpropertyroles->SigningAddress1 : '-' )."<br/>";            
			   $Notes .= "Address2 is changed to ".(!empty($ClosingDetails['ClosingAddress2']) ? $ClosingDetails['ClosingAddress2'] : '-')." from ".(!empty($torderpropertyroles->SigningAddress2) ? $torderpropertyroles->SigningAddress2 : '-' )."<br/>";
			   $Notes .= "City is changed to ".(!empty($ClosingDetails['ClosingCityName']) ? $ClosingDetails['ClosingCityName'] : '-')." from ".(!empty($torderpropertyroles->SigningCityName) ? $torderpropertyroles->SigningCityName : '-' )."<br/>";
			   $Notes .= "State is changed to ".(!empty($ClosingDetails['ClosingStateCode']) ? $ClosingDetails['ClosingStateCode'] : '-')." from ".(!empty($torderpropertyroles->SigningStateCode) ? $torderpropertyroles->SigningStateCode : '-' )."<br/>";
			   $Notes .= "Zipcode is changed to ".(!empty($ClosingDetails['ClosingZipcode']) ? $ClosingDetails['ClosingZipcode'] : '-')." from ".(!empty($torderpropertyroles->SigningZipCode) ? $torderpropertyroles->SigningZipCode : '-' )."<br/>";
			   $Notes .= "County is changed to ".(!empty($ClosingDetails['ClosingCountyName']) ? $ClosingDetails['ClosingCountyName'] : '-')." from ".(!empty($torderpropertyroles->SigningCountyName) ? $torderpropertyroles->SigningCountyName : '-' )."<br/>";         
		      }
		   
		  }
	   
		   //Update Address
		  if(!empty($address))
		  {
		      $address = array_filter($address);
		      $this->db->where(['Id' => $BorrowerUID, 'OrderUID' => $OrderUID ]);
		      $this->db->update('torderpropertyroles', $address);   
		  }
		  
		  //Update notes
		  if(!empty($Notes))
		  {
		    $SectionUID = $this->common_model->GetNoteTypeUID("System Note")->SectionUID;
		    $LoggedDetails = $this->common_model->getLoggedDetails();
	   
		    $Notes .= "Assigned On: ". date('m/d/Y h:i A') ." <br>";
		   // $Notes .= "Assigned By: ". $LoggedDetails->UserName ." <br>";
	   
		    $this->common_model->insertordernotes($OrderUID, $SectionUID, $Notes);       
		  }
		}
	}
	   
// Ends

}
