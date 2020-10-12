<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class My_orders_model extends CI_Model {


	function __construct()
	{ 
		parent::__construct();
		$this->config->load('keywords');
	}

	function GetRoles(){

		$query = $this->db->get('mRoles');
		return $query->result();
	}

	function GetCheckCustomFields($UserUID){

		$query = $this->db->query("SELECT EXISTS(SELECT * FROM mCustomSortColumns WHERE CustomSortByUserUID = '$UserUID') as CheckCustomFields;
			");
		return $query->row();
	}

	function GetUserByRoleUID($RoleUID = '')
	{
		$User = $this->db->query("SELECT * FROM `mUsers` 
			WHERE mUsers.RoleUID = '$RoleUID'")->result_array(); 
		return array('User'=>$User);
	}

	function GetUserName($UserUID = '')
	{
		$query = $this->db->query("SELECT * FROM `mUsers` 
			WHERE mUsers.UserUID = '$UserUID'");
		return $query->row();
	}

	function GetCustomerByUserUID($UserUID){
		$this->db->select("CustomerUID");
		$this->db->from('mUsers');
		$this->db->where(array("UserUID"=>$UserUID));
		$query = $this->db->get();
		return $query->row();
	}

	function get_myorders($loggedid = '')
	{
		$status[0] = $this->config->item('keywords')['Order Assigned'];
		$status[1] = $this->config->item('keywords')['Order Work In Progress'];
		$status[2] = $this->config->item('keywords')['Partial Review Complete'];
		$status[3] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[4] = $this->config->item('keywords')['Review In Progress'];
		$status[5] = $this->config->item('keywords')['Reopened Order'];
		
    //$status[2] = $this->config->item('keywords')['Complete'];

		$this->db->select ( 'CustomerName,OrderNumber,StatusName,StatusColor,tOrders.StatusUID,tOrderAssignment.OrderUID,mProducts.ProductName,mProducts.ProductCode,mSubProducts.SubProductCode,PropertyStateCode,VendorAssignedDateTime' );
		$this->db->select('DATE_FORMAT(tOrderAssignment.AssignedDatetime, "%m-%d-%Y %H:%i:%s") as AssignedDatetime', FALSE);
		$this->db->select("DATE_FORMAT(OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", FALSE);
		$this->db->select('DATE_FORMAT(tOrders.OrderDueDatetime, "%m-%d-%Y %H:%i:%s") as OrderDueDatetime', FALSE);    
		$this->db->from ( 'tOrderAssignment' );
		$this->db->join ( 'tOrders', 'tOrderAssignment.OrderUID = tOrders.OrderUID' , 'left' );
		$this->db->join ( 'mUsers', 'tOrderAssignment.AssignedToUserUID = mUsers.UserUID' , 'left' );
		$this->db->join ( 'mWorkflowModules', 'mWorkflowModules.WorkflowModuleUID = tOrderAssignment.WorkflowModuleUID' , 'left' );
		$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = tOrders.CustomerUID' , 'left' );
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
		$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = tOrders.SubProductUID' , 'left' );

		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mSubProducts.ProductUID' , 'left' );

		$this->db->where_in('tOrders.StatusUID', $status);

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $this->db->where('mProducts.ProductUID IN ('.$UserProducts.')', null, false); else: return $this->db->where('mProducts.ProductUID IN (0)', null, false); endif;
		}

		$this->db->where('tOrderAssignment.AssignedToUserUID',$loggedid);
		
		$this->db->group_by('OrderUID,AssignedToUserUID');
		$query = $this->db->get();
		return $query->result_array();
	}

function GetCustomerProducts($CustomerUID='')
{ 


if($this->session->userdata('RoleType') == 6){
$UserProducts = $this->common_model->_get_product_bylogin();
if($UserProducts){
$this->db->select('mProducts.ProductUID, mProducts.ProductName,mSubProducts.SubProductUID,mSubProducts.SubProductName');
$this->db->from('mSubProducts');
$this->db->join ('mCustomerProducts','mCustomerProducts.SubProductUID = mSubProducts.SubProductUID','left');
$this->db->join ('mProducts','mCustomerProducts.ProductUID = mProducts.ProductUID','left');
if($CustomerUID!='all')
{
$this->db->where('CustomerUID IN ('.$CustomerUID.')');
}
$this->db->where('mCustomerProducts.ProductUID IN ('.$UserProducts.')');
$this->db->group_by("mProducts.ProductUID"); 
return $this->db->get()->result(); 
}
else{
return [];
}
}else{
if($CustomerUID!='')
{
$this->db->select('mProducts.ProductUID, mProducts.ProductName,mSubProducts.SubProductUID,mSubProducts.SubProductName');
$this->db->from('mSubProducts');
$this->db->join ('mCustomerProducts','mCustomerProducts.SubProductUID = mSubProducts.SubProductUID','left');
$this->db->join ('mProducts','mCustomerProducts.ProductUID = mProducts.ProductUID','left');
$this->db->where('CustomerUID IN ('.$CustomerUID.')');
$this->db->group_by("mProducts.ProductUID"); 
return $this->db->get()->result(); 
} else {
return [];
}
}
// return $this->db->get()->result(); 
}
function GetCustomerProjects($CustomerUID='')
{ 
	if($CustomerUID!='')
	{
		$this->db->select('ProjectUID, ProjectName');
		$this->db->from('mProjects');
		$this->db->where('CustomerUID',$CustomerUID);
		$this->db->group_by("ProjectUID"); 
		return $this->db->get()->result();  
	}
	else
	{
		return [];
	}

}

function GetsubproductCustomer($customer,$ProductUID)
{
  if($customer!='')
  {
    $this->db->select('mSubProducts.SubProductUID,mSubProducts.SubProductName');
    $this->db->from('mCustomerProducts');
    $this->db->join ('mSubProducts','mCustomerProducts.SubProductUID = mSubProducts.SubProductUID','LEFT'); 
    $this->db->where('mCustomerProducts.CustomerUID IN ('.$customer.')');
    $this->db->where('mCustomerProducts.ProductUID IN ('.$ProductUID.')');
    $this->db->where('mSubProducts.Active',1);
    $this->db->group_by('mSubProducts.SubProductUID');
    return $this->db->get()->result(); 
  } else {
    $this->db->select('SubProductUID, SubProductName');
    $this->db->where('ProductUID IN ('.$ProductUID.')');
    $this->db->where('Active',1);
    $this->db->group_by('SubProductUID');
    $q = $this->db->get('mSubProducts');
    return $q->result();
  }
}

	function GetTBodyContent($UserUID,$DynTable,$Sort,$VendorUID = '')
	{
		/*vendor change Starts*/
		$where = '';
		if($VendorUID != ''){
			$where = "AND tOrderAssignment.WorkflowModuleUID !=4 AND tOrderAssignment.SendToVendor = '1' AND tOrderAssignment.VendorUID  = '".$VendorUID."'";
		}
		/*vendor change Ends*/

		if($this->common_model->GetMyOrdersQueue() == 1)
		{
			$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'].','.$this->config->item('keywords')['Reopened Order'];
			$query = $this->db->query("SELECT tOrders.OrderUID,mOrderPriority.TAT,mOrderPriority.PriorityUID,mOrderStatus.StatusName,mOrderStatus.StatusColor,tOrders.StatusUID,".$DynTable."
				FROM (`tOrders`) 
				LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID` 
				LEFT JOIN `tOrderPropertyRoles` ON `tOrders`.`OrderUID` = `tOrderPropertyRoles`.`OrderUID`
				LEFT JOIN `mTemplates` ON `tOrders`.`TemplateUID` = `mTemplates`.`TemplateUID` 
				LEFT JOIN `mOrderTypes` ON `tOrders`.`OrderTypeUID` = `mOrderTypes`.`OrderTypeUID` 
				LEFT JOIN `mUsers` ON `tOrderAssignment`.`AssignedToUserUID` = `mUsers`.`UserUID` 
				LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` 
				LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` 
				LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` 
				LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` 
				LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` 
				WHERE `tOrders`.`StatusUID` IN (".$statuses.") AND `tOrderAssignment`.`AssignedToUserUID` = ".$UserUID." ".$where." 
				AND tOrderAssignment.WorkflowModuleUID !=4 
				GROUP BY tOrders.`OrderUID` 
				ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC ,".$Sort."");
		}
		else
		{
			$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].','.$this->config->item('keywords')['Cancelled'].','.$this->config->item('keywords')['Exception Raised'];

			$query = $this->db->query("SELECT tOrders.OrderUID,mOrderPriority.TAT,mOrderPriority.PriorityUID,mOrderStatus.StatusName,mOrderStatus.StatusColor,tOrders.StatusUID,".$DynTable."
				FROM (`tOrders`)  
				LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID` 
				LEFT JOIN `tOrderPropertyRoles` ON `tOrders`.`OrderUID` = `tOrderPropertyRoles`.`OrderUID` 
				LEFT JOIN `mTemplates` ON `tOrders`.`TemplateUID` = `mTemplates`.`TemplateUID` 
				LEFT JOIN `mOrderTypes` ON `tOrders`.`OrderTypeUID` = `mOrderTypes`.`OrderTypeUID` 
				LEFT JOIN `mUsers` ON `tOrderAssignment`.`AssignedToUserUID` = `mUsers`.`UserUID` 
				LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  
				LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` 
				LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` 
				LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` 
				LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` 
				WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.") ".$where."  
				GROUP BY tOrders.`OrderUID` 
				ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC,".$Sort."");
		}

		return $query->result_array();
	}

	function GetOrderByField($UserUID)
	{
		$this->db->distinct();
		$this->db->select ( '*' );
		$this->db->from ( 'mCustomSortColumns' );
		$this->db->where('mCustomSortColumns.CustomSortByUserUID',$UserUID);
		$this->db->order_by('mCustomSortColumns.FieldPosition');
		$query = $this->db->get();
		return $query->row();
	}

	function GetTHeadContent($UserUID)
	{
		$this->db->select ( 'FieldFormName' );
		$this->db->from ( 'mCustomSortColumns' );
		$this->db->where('mCustomSortColumns.CustomSortByUserUID',$UserUID);
		$this->db->order_by('mCustomSortColumns.FieldPosition');
		$query = $this->db->get();
		return $query->result_array();
	}

	function GetCustomTablevalues($UserUID){

		$sql = "SELECT COUNT(1) AS COUNT FROM mCustomSortColumns WHERE CustomSortByUserUID = $UserUID";
		$query = $this->db->query($sql);
		$res= $query->result();
		return $res;
	}

	function GetCustomFieldNameByUserUID($UserUID){

		$this->db->select ( 'FieldName' );
		$this->db->from ( 'mCustomSortColumns' );
		$this->db->where('mCustomSortColumns.CustomSortByUserUID',$UserUID);
		$this->db->order_by('mCustomSortColumns.FieldPosition');
		$query = $this->db->get();
		return $query->result_array();
	}

	function GetCustomFieldName($UserUID){

		$this->db->select ( '*' );
		$this->db->from ( 'mCustomSortColumns' );
		$this->db->where('mCustomSortColumns.CustomSortByUserUID',$UserUID);
		$this->db->order_by('mCustomSortColumns.FieldPosition');
		$query = $this->db->get();
		return $query->result();
	}

	function Count_get_myorders_by_cust_id($loggedid = '',$CustomerUID)
	{

		$status[0] = $this->config->item('keywords')['Cancelled'];
		$status[1] = $this->config->item('keywords')['Order Completed'];
		$status[2] = $this->config->item('keywords')['Exception Raised'];


		$this->db->select ( 'CustomerName,tOrders.OrderUID,OrderNumber,StatusName,tOrders.StatusUID,StatusColor,mProducts.ProductName,mProducts.ProductCode,mSubProducts.SubProductCode,mSubProducts.SubProductName,PropertyAddress1,PropertyZipcode,LoanNumber,tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName' );
		$this->db->select('DATE_FORMAT(tOrders.OrderDueDatetime, "%m-%d-%Y %H:%i:%s") as OrderDueDatetime', FALSE);    
		$this->db->select('DATE_FORMAT(OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);
		$this->db->select('DATE_FORMAT(OrderCompleteDateTime, "%m-%d-%Y %H:%i:%s") as OrderCompleteDateTime', FALSE);
		$this->db->from ( 'tOrders' );
		$this->db->join ( 'tOrderAssignment', 'tOrderAssignment.OrderUID = tOrders.OrderUID','left');
		$this->db->join ( 'mWorkflowModules', 'mWorkflowModules.WorkflowModuleUID = tOrderAssignment.WorkflowModuleUID','left');
		$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = tOrders.CustomerUID','left');
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID','left');
		$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = tOrders.SubProductUID','left');
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mSubProducts.ProductUID','left');

		$this->db->where_not_in('tOrders.StatusUID', $status);
		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $this->db->where('mProducts.ProductUID IN ('.$UserProducts.')', null, false); else: return $this->db->where('mProducts.ProductUID IN (0)', null, false); endif;
		}

		$this->db->group_by('tOrders.OrderUID');
		$this->db->order_by('OrderUID,OrderNumber', 'DESC');
		$query = $this->db->get();
		return $query->num_rows();
	}

	function filter_get_myorders_by_cust_id($loggedid = '',$CustomerUID, $post='')
	{

		$status[0] = $this->config->item('keywords')['Cancelled'];
		$status[1] = $this->config->item('keywords')['Order Completed'];
		$status[2] = $this->config->item('keywords')['Exception Raised'];

		$this->db->select ( 'CustomerName,tOrders.OrderUID,OrderNumber,StatusName,tOrders.StatusUID,StatusColor,mProducts.ProductName,mProducts.ProductCode,mSubProducts.SubProductCode,mSubProducts.SubProductName,PropertyAddress1,PropertyZipcode,LoanNumber,tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName' );
		$this->db->select('DATE_FORMAT(tOrders.OrderDueDatetime, "%m-%d-%Y %H:%i:%s") as OrderDueDatetime', FALSE);    
		$this->db->select('DATE_FORMAT(OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);
		$this->db->select('DATE_FORMAT(OrderCompleteDateTime, "%m-%d-%Y %H:%i:%s") as OrderCompleteDateTime', FALSE);
		$this->db->from ( 'tOrders' );
		$this->db->join ( 'tOrderAssignment', 'tOrderAssignment.OrderUID = tOrders.OrderUID','left');
		$this->db->join ( 'mWorkflowModules', 'mWorkflowModules.WorkflowModuleUID = tOrderAssignment.WorkflowModuleUID','left');
		$this->db->join ( 'tOrderPropertyRoles', 'tOrderPropertyRoles.OrderUID = tOrders.OrderUID','left');
		$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = tOrders.CustomerUID','left');
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID','left');
		$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = tOrders.SubProductUID','left');
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mSubProducts.ProductUID','left');
		$this->db->where_not_in('tOrders.StatusUID', $status);
		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $this->db->where('mProducts.ProductUID IN ('.$UserProducts.')', null, false); else: return $this->db->where('mProducts.ProductUID IN (0)', null, false); endif;
		}

		$this->db->group_by('tOrders.OrderUID');
		$this->db->order_by('OrderUID,OrderNumber', 'DESC');
		
		if (!empty($post['search_value'])) {
			$like = "";
			foreach ($post['column_search'] as $key => $item) { // loop column 
            // if datatable send POST for search
				if ($key === 0) { // first loop
					$like .= "( ".$item." LIKE '%".$post['search_value']."%' "; 
				} else {
					$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";    
				}
			}
			$like .= ") ";
			$this->db->where($like, null, false);
		}

		$query = $this->db->get();
		return $query->num_rows();

	}  

	function get_myorders_by_cust_id($loggedid = '',$CustomerUID, $post='')
	{

		$status[0] = $this->config->item('keywords')['Cancelled'];
		$status[1] = $this->config->item('keywords')['Order Completed'];
		$status[2] = $this->config->item('keywords')['Exception Raised'];


		$this->db->select ( 'CustomerName,tOrders.OrderUID,OrderNumber,StatusName,tOrders.StatusUID,StatusColor,mProducts.ProductName,mProducts.ProductCode,mSubProducts.SubProductCode,mSubProducts.SubProductName,PropertyAddress1,PropertyZipcode,LoanNumber,tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName' );
		$this->db->select('DATE_FORMAT(tOrders.OrderDueDatetime, "%m-%d-%Y %H:%i:%s") as OrderDueDatetime', FALSE);    
		$this->db->select('DATE_FORMAT(OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);
		$this->db->select('DATE_FORMAT(OrderCompleteDateTime, "%m-%d-%Y %H:%i:%s") as OrderCompleteDateTime', FALSE);
		$this->db->select('tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime', FALSE);
		$this->db->from ( 'tOrders' );
		$this->db->join ( 'tOrderAssignment', 'tOrderAssignment.OrderUID = tOrders.OrderUID','left');
		$this->db->join ( 'mWorkflowModules', 'mWorkflowModules.WorkflowModuleUID = tOrderAssignment.WorkflowModuleUID','left');
		$this->db->join ( 'tOrderPropertyRoles', 'tOrderPropertyRoles.OrderUID = tOrders.OrderUID','left');
		$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = tOrders.CustomerUID','left');
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID','left');
		$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = tOrders.SubProductUID','left');
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mSubProducts.ProductUID','left');
		$this->db->where_not_in('tOrders.StatusUID', $status);
		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $this->db->where('mProducts.ProductUID IN ('.$UserProducts.')', null, false); else: return $this->db->where('mProducts.ProductUID IN (0)', null, false); endif;
		}

		$this->db->group_by('tOrders.OrderUID');
		$this->db->order_by('OrderUID,OrderNumber', 'DESC');

		if ($post['length']!='') {
			$this->db->limit($post['length'], $post['start']);
		}

		if (!empty($post['search_value'])) {
			$like = "";
			foreach ($post['column_search'] as $key => $item) { // loop column 
            // if datatable send POST for search
				if ($key === 0) { // first loop
					$like .= "( ".$item." LIKE '%".$post['search_value']."%' "; 
				} else {
					$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";    
				}
			}
			$like .= ") ";
			$this->db->where($like, null, false);
		}

		$query = $this->db->get();
		return $query->result_array();

	}


	function Change_order_status($OrderUID,$status,$flag,$UserUID)
	{
		// if($flag == 1){
		// 	$set_data = array(
		// 		'OrderFlag' => $flag,
		// 		'WorkflowStatus' => $status
		// 	);
		// }else{
		// 	$set_data = array(
		// 		'WorkflowStatus' => $status
		// 	);        
		// }

		$set_data = array(
			'OrderFlag' => $flag,
			'WorkflowStatus' => $status
		);		

		$where = array(
			'OrderUID'  => $OrderUID,
			'AssignedToUserUID' => $UserUID,
			'WorkflowStatus' => 0,
		);

		$this->db->set($set_data)->where($where)->update('tOrderAssignment');
		if($this->db->affected_rows() > 0)
		{
			$this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['WorkflowModuleUID'][$workflowuid]['Assigned'],$UserUID);
        // $data1['ModuleName']='Order Cancel Requst Status-update';
        // $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
        // $data1['DateTime']=date('y-m-d H:i:s');
        // $data1['TableName']='tordercancel';
        // $data1['OrderUID']=$orderUID;
        // $data1['UserUID']=$this->session->userdata('UserUID'); 
        // $data1['OldValue']=''; 
        // $data1['FieldUID']='1138';
        // $data1['NewValue']='110';                 
        // $this->common_model->Audittrail_insert($data1); 
			return true;
		}
		else
		{
			return false;
		}              

	}

	function RejectAssignedOrder($OrderUID,$UserUID,$VendorUID,$Remarks,$Reason){
		$this->db->trans_begin();

		$filter_workflow = $this->db->query("SELECT GROUP_CONCAT(WorkflowModuleUID) AS WorkflowModuleUIDS FROM `tOrderAssignment` WHERE OrderUID = '".$OrderUID."' AND VendorUID = '".$VendorUID."' ")->row()->WorkflowModuleUIDS;
		$set_data = array(
			'AssignedToUserUID' => NULL,
			'AssignedDatetime' => NULL,
			'OrderFlag' => 2,
			'SendToVendor' => 0,
			'VendorUID' => NULL,			
			'VendorWorkModule' => NULL			
		);

		$where = array(
			'OrderUID'  => $OrderUID,
			// 'WorkflowStatus' => 0,
			'VendorUID' => $VendorUID
		);

		$this->db->set($set_data)->where($where)->update('tOrderAssignment');
		$reject_data = array(
			'OrderUID' => $OrderUID,
			'VendorUID' => $VendorUID,
			'WorkflowModuleUID' => $filter_workflow,
			'RejectedByUserUID' => $UserUID,
			'RejectedDatetime' =>  Date('Y-m-d H:i:s',strtotime("now")),
			'Remarks' => $Remarks,
			'Reason'=> $Reason
		);

		$this->db->insert('tvendorreject',$reject_data);
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return true;
		}				
	}

	function get_notes_count($loggedid,$OrderUID)
	{

		$query = $this->db->query("SELECT count(*) AS unread FROM `tordernotes` LEFT JOIN `tOrderNotifications` ON `tOrderNotifications`.`NoteUID` = `tordernotes`.`NoteUID` WHERE `tOrderNotifications`.`ReadStatus` = '0' AND `tOrderNotifications`.`RecepientUserUID` = '$loggedid' AND `tordernotes`.`SectionUID` != '' AND `tordernotes`.`OrderUID` = '$OrderUID' ");

		$unread =  $query->row();

		$query1 = $this->db->query("SELECT count(*) AS filecount FROM `tordernotes` LEFT JOIN `tOrderNotifications` ON `tOrderNotifications`.`NoteUID` = `tordernotes`.`NoteUID` WHERE `tOrderNotifications`.`ReadStatus` = '0' AND `tOrderNotifications`.`RecepientUserUID` = '$loggedid' AND `tordernotes`.`SectionUID` != '' AND `tordernotes`.`OrderUID` = '$OrderUID' AND `tordernotes`.`AttachedFile` IS NOT NULL  ");
		$filecount =  $query1->row();

		$unread1 = count($unread) > 0 ? $unread->unread : NULL ;
		$filecount1 = count($filecount) > 0 ? $filecount->filecount : NULL ;
		$result_array = array('unread'=>$unread1,'filecount'=>$filecount1);
		return $result_array;
	}


	function get_Workflowassigned($OrderUID){

		$loggedid = $this->session->userdata('UserUID');
		$this->db->select ( 'Group_concat(WorkflowModuleName) as WorkflowModuleName,WorkflowStatus' );
		$this->db->from ( 'tOrderAssignment' );
		$this->db->join ( 'mWorkflowModules', 'mWorkflowModules.WorkflowModuleUID = tOrderAssignment.WorkflowModuleUID' , 'inner' );
		$this->db->where('tOrderAssignment.AssignedToUserUID',$loggedid);
		$this->db->where('tOrderAssignment.OrderUID',$OrderUID);

		$query = $this->db->get();
		$res =  $query->row();

		return $res;
	}


	function get_Workflowassignedbyid($OrderUID){

		$loggedid = $this->session->userdata('UserUID');
		$this->db->select ( 'Group_concat(WorkflowModuleName) as WorkflowModuleName' );
		$this->db->from ( 'tOrderAssignment' );
		$this->db->join ( 'mWorkflowModules', 'mWorkflowModules.WorkflowModuleUID = tOrderAssignment.WorkflowModuleUID' , 'inner' );
		$this->db->where('tOrderAssignment.OrderUID',$OrderUID);

		$query = $this->db->get();
		$res =  $query->row();

		return $res;
	}

	function count_check_order($loggedid,$post)
	{  

		/*FOR SUPERVISOR CHECK*/
		$where= '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			
			if($UserProducts): $where .= ' AND `mProducts`.`ProductUID` IN ('.$UserProducts.')'; else: return array(); endif;
		}

		$filter = '';
		if (!empty($post['CustomerUID'])) {
			$filter .= ' AND tOrders.CustomerUID IN ('.$post['CustomerUID'].')';
		}
		if (!empty($post['ProjectUID'])) {
			$filter .= ' AND tOrders.ProjectUID IN ('.$post['ProjectUID'].')';
		}
		if (!empty($post['SubProductUID'])) {
			$filter .= ' AND tOrders.SubProductUID IN ('.$post['SubProductUID'].')';
		}

		if($this->common_model->GetMyOrdersQueue() == 1)
		{
			$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'].','.$this->config->item('keywords')['Reopened Order'];


			$sql = "SELECT `tOrders`.`OrderUID`, `tOrderAssignment`.`WorkflowModuleUID` FROM (`tOrders`) LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` IN (".$statuses.") AND `tOrderAssignment`.`AssignedToUserUID` = ".$loggedid." AND tOrderAssignment.WorkflowModuleUID !=4  $where $filter GROUP BY `OrderUID`";
		} else {

			$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].
			','.$this->config->item('keywords')['Cancelled'].','.$this->config->item('keywords')['Exception Raised'];


			if (!in_array($this->session->userdata('RoleType'), [1,2,3,4,5,6])) {
				$usergroups = $this->common_model->get_user_group_id($this->loggedid);

				if (!empty($usergroups)) {

					$usergroups = implode(',', $usergroups);
				// $groupcustomers = $this->common_model->get_customer_ingroup($usergroups);
					$loggedin_userproducts = $this->My_orders_model->get_subproducts_by_groupuids($usergroups);
					/*@Desc Group Setup Changed @Author Jainulabdeen @Updated Aug 8 2020*/
					$where .= 'AND '.$loggedin_userproducts;
					/*if (!empty($loggedin_userproducts)) {
						$subproductuids='';
						foreach ($loggedin_userproducts as $key => $value) {
							$subproductuids = $value->SubProductUID . ', ';
						}
						$subproductuids = rtrim($subproductuids,  ', ');


						$where .= " AND (tOrders.SubProductUID1 IN (".$subproductuids.") OR `tOrderAssignment`.`AssignedToUserUID` = ".$loggedid." AND tOrderAssignment.WorkflowModuleUID !=4) ";
					// echo $where; exit;
					}*/
					/*End*/
				}
			}

			$sql = "SELECT  `tOrders`.`OrderUID`, `tOrderAssignment`.`WorkflowModuleUID` FROM (`tOrders`)  LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.") $where $filter GROUP BY `OrderUID` "; 

		}

		$checkorders = $this->db->query($sql)->result_array();
		$mRoles = $this->common_model->get_roles($this->RoleUID);
		foreach ($checkorders as $key => $value) 
		{

			if(in_array($this->session->userdata('RoleType'),array(1,2,3,4,5,6,13)) == False)
			{
				if (!empty($mRoles) && $mRoles[0]['MyOrdersQueue'] == 1) {
					$assigned = $this->common_model->get_assigned_workflows($value['OrderUID'],$this->loggedid);
					$completed = $this->common_model->get_completed_workflows($value['OrderUID'],$this->loggedid);
					$assigned_orderss = [];
					$completed_orderss = [];
					$assigned_workflows = [];
					$completed_workflows = [];
					foreach ($assigned as $keys => $values) {
						$assigned_orderss[] = $values['WorkflowModuleUID'];
						$assigned_workflows[] = $values['OrderUID'];
					}
					foreach ($completed as $keyss => $valuess) {
						$completed_orderss[] = $valuess['WorkflowModuleUID'];
						$completed_workflows[] = $valuess['OrderUID'];
					}
					if($assigned_orderss === array_intersect($assigned_orderss, $completed_orderss) && $completed_orderss === array_intersect($completed_orderss, $assigned_orderss)) {

						if($assigned_workflows === array_intersect($assigned_workflows, $completed_workflows) && $completed_workflows === array_intersect($completed_workflows, $assigned_workflows)) {
							unset($checkorders[$key]);
						} 
					}   
				}
			}
		}

		if(count($checkorders) > 0){
			$my_orders = $checkorders; 
		} else {
			$my_orders = array();
		}

		return sizeof($my_orders);
		
	}

	function filter_check_order($loggedid,$post)
	{ 
		if(!empty($post['search_value'])) 
		{
			$like = "AND ";
			foreach ($post['column_search'] as $key => $item) { // loop column 
            // if datatable send POST for search
				if ($key === 0) { // first loop
					$like .= "( ".$item." LIKE '%".$post['search_value']."%' "; 
				} else {
					$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";    
				}
			}
			$like .= ") ";
		} else {
			$like = "";
		}

		$filter = '';
		if (!empty($post['CustomerUID'])) {
			$filter .= ' AND tOrders.CustomerUID IN ('.$post['CustomerUID'].')';
		}
		if (!empty($post['ProjectUID'])) {
			$filter .= ' AND tOrders.ProjectUID IN ('.$post['ProjectUID'].')';
		}
		if (!empty($post['SubProductUID'])) {
			$filter .= ' AND tOrders.SubProductUID IN ('.$post['SubProductUID'].')';
		}

		/*FOR SUPERVISOR CHECK*/
		$where = '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND `mProducts`.`ProductUID` IN ('.$UserProducts.')'; else: return array(); endif;
		}  


		if($this->common_model->GetMyOrdersQueue() == 1)
		{
			$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'].','.$this->config->item('keywords')['Reopened Order'];

			$sql = "SELECT `tOrders`.`OrderUID`, `tOrderAssignment`.`WorkflowModuleUID` FROM (`tOrders`) LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `tOrderPropertyRoles` ON (`tOrderPropertyRoles`.`OrderUID` = `tOrders`.`OrderUID`  AND `tOrderPropertyRoles`.`PropertyRoleUID` = ".$this->config->item('Propertyroles')['Borrowers'].")  LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` IN (".$statuses.") AND `tOrderAssignment`.`AssignedToUserUID` = ".$loggedid." AND tOrderAssignment.WorkflowModuleUID !=4  $where $like $filter GROUP BY `OrderUID` ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC";
		}
		else{

			$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].
			','.$this->config->item('keywords')['Cancelled'].','.$this->config->item('keywords')['Exception Raised'];



			if (!in_array($this->session->userdata('RoleType'), [1,2,3,4,5,6])) {
				$usergroups = $this->common_model->get_user_group_id($this->loggedid);

				if (!empty($usergroups)) {

					$usergroups = implode(',', $usergroups);
				// $groupcustomers = $this->common_model->get_customer_ingroup($usergroups);
					$loggedin_userproducts = $this->My_orders_model->get_subproducts_by_groupuids($usergroups);
					/*@Desc Group Setup Changed @Author Jainulabdeen @Updated Aug 13 2020*/
					$where .= 'AND '.$loggedin_userproducts;
					/*if (!empty($loggedin_userproducts)) {
						$subproductuids='';
						foreach ($loggedin_userproducts as $key => $value) {
							$subproductuids = $value->SubProductUID . ', ';
						}
						$subproductuids = rtrim($subproductuids,  ', ');


						$where .= " AND (tOrders.SubProductUID IN (".$subproductuids.") OR `tOrderAssignment`.`AssignedToUserUID` = ".$loggedid." AND tOrderAssignment.WorkflowModuleUID !=4) ";
					// echo $where; exit;
					}*/
					/*End*/
				}
			}
			$sql = "SELECT `tOrders`.`OrderUID`, `tOrderAssignment`.`WorkflowModuleUID` FROM (`tOrders`)  LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `tOrderPropertyRoles` ON (`tOrderPropertyRoles`.`OrderUID` = `tOrders`.`OrderUID`  AND `tOrderPropertyRoles`.`PropertyRoleUID` = ".$this->config->item('Propertyroles')['Borrowers'].")  LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.") $where $like $filter GROUP BY `OrderUID` ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC"; 

		}

		$checkorders = $this->db->query($sql)->result_array();
		$mRoles = $this->common_model->get_roles($this->RoleUID);

		foreach ($checkorders as $key => $value) 
		{
			if(in_array($this->session->userdata('RoleType'),array(1,2,3,4,5,6,13)) == False)
			{
				if (!empty($mRoles) && $mRoles[0]['MyOrdersQueue'] == 1) {
					$assigned = $this->common_model->get_assigned_workflows($value['OrderUID'],$this->loggedid);
					$completed = $this->common_model->get_completed_workflows($value['OrderUID'],$this->loggedid);
					$assigned_orderss = [];
					$completed_orderss = [];
					$assigned_workflows = [];
					$completed_workflows = [];
					foreach ($assigned as $keys => $values) {
						$assigned_orderss[] = $values['WorkflowModuleUID'];
						$assigned_workflows[] = $values['OrderUID'];
					}
					foreach ($completed as $keyss => $valuess) {
						$completed_orderss[] = $valuess['WorkflowModuleUID'];
						$completed_workflows[] = $valuess['OrderUID'];
					}
					if($assigned_orderss === array_intersect($assigned_orderss, $completed_orderss) && $completed_orderss === array_intersect($completed_orderss, $assigned_orderss)) {

						if($assigned_workflows === array_intersect($assigned_workflows, $completed_workflows) && $completed_workflows === array_intersect($completed_workflows, $assigned_workflows)) {
							unset($checkorders[$key]);
						} 
					}   
				}
			}
		}

		if(count($checkorders) > 0){
			$my_orders = $checkorders; 
		} else {
			$my_orders = array();
		}

		return sizeof($my_orders);
	}        


	function check_order($loggedid,$post)
	{ 
		// here order processing
		if (!empty($post['order']) && isset($post['column_order'][$post['order'][0]['column']]) && !empty($post['column_order'][$post['order'][0]['column']])) {

			$order_by = 'ORDER BY '.$post['column_order'][$post['order'][0]['column']].' '.$post['order'][0]['dir'];

		} else if( ( isset($post['workflowprioritization']) && !empty($post['workflowprioritization']) ) || (isset($post['overrideworkflowprioritization']) && !empty($post['overrideworkflowprioritization']) ) ) {

			if(isset($post['workflowprioritization']) && !empty($post['workflowprioritization'])) {

				$order_by = ' ORDER BY '.$post['workflowprioritization'].'';

			}


			if(isset($post['overrideworkflowprioritization']) && !empty($post['overrideworkflowprioritization'])) 
			{
				if (empty($order_by)) 
				{
					
					$order_by = ' ORDER BY ( CASE WHEN `tOrders`.`PriorityUID` = 1 THEN 1 ELSE 0 END ) DESC,  '.$post['overrideworkflowprioritization'];
				}
				else
				{
									
					$order_by .= ' , ( CASE WHEN `tOrders`.`PriorityUID` = 1 THEN 1 ELSE 0 END ) DESC, '.$post['overrideworkflowprioritization'];

				}


			}
			/*Override Rush End*/


			$order_by .= ' , FIELD(`tOrders`.`PriorityUID`,3,1) DESC  , `tOrders`.`OrderEntryDatetime` ASC';


		} else {

			$order_by = 'ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC';

		}



		if(isset($post['overrideworkflowprioritization']) && !empty($post['overrideworkflowprioritization'])) 
		{
			if (empty($order_by)) 
			{
				
				$order_by = ' ORDER BY '.$post['overrideworkflowprioritization'].',FIELD(`tOrders`.`PriorityUID`,3,1) DESC  , `tOrders`.`OrderEntryDatetime` ASC';
			}
			else
			{
								
				$order_by = ' ,'.$post['overrideworkflowprioritization'].',FIELD(`tOrders`.`PriorityUID`,3,1) DESC  , `tOrders`.`OrderEntryDatetime` ASC';

			}


		}

		if ($post['length']!='') {
			$limit = 'LIMIT '.$post['start'].','.$post['length'];
		} else {
			$limit = '';
		}

		if(!empty($post['search_value'])) 
		{
			$like = "AND ";
			foreach ($post['column_search'] as $key => $item) { // loop column 
          // if datatable send POST for search
				if ($key === 0) { // first loop
					$like .= "( ".$item." LIKE '%".$post['search_value']."%' "; 
				} else {
					$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";    
				}
			}
			$like .= ") ";
		} else {
			$like = "";
		}  

		$filter = '';
		if (!empty($post['CustomerUID'])) {
			$filter .= ' AND tOrders.CustomerUID IN ('.$post['CustomerUID'].')';
		}
		if (!empty($post['ProjectUID'])) {
			$filter .= ' AND tOrders.ProjectUID IN ('.$post['ProjectUID'].')';
		}
		if (!empty($post['SubProductUID'])) {
			$filter .= ' AND tOrders.SubProductUID IN ('.$post['SubProductUID'].')';
		}

		/*FOR SUPERVISOR CHECK*/
		$where = '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND `mProducts`.`ProductUID` IN ('.$UserProducts.')'; else: return array(); endif;
		}

		if($this->common_model->GetMyOrdersQueue() == 1)
		{
			$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'].','.$this->config->item('keywords')['Reopened Order'];




			$sql = "SELECT `CustomerNumber`,`CustomerName`,`tOrders`.`CustomerUID`,`tOrders`.`SubProductUID`,`mProducts`.`IsSelfAssign`,`LoanNumber`, `OrderNumber`, `AltORderNumber`, `StatusName`,`StatusName`,`tOrders`.`StatusUID`,`tOrders`.`PropertyZipcode`,`StatusColor`, `tOrders`.`OrderUID`, `tOrders`.`OrderEntryDatetime` as OrderEntryDatetime, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`,`mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(tOrderAssignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime ,TRIM(CONCAT_WS(' ',TRIM(tOrders.PropertyAddress1),TRIM(tOrders.PropertyAddress2))) AS whole_name , tOrders.PropertyStateCode,tOrders.IsInhouseExternal,tOrders.PropertyCityName,tOrders.PropertyCountyName,mabstractor.AbstractorNo,mabstractor.AbstractorCompanyName,mabstractor.AbstractorFirstName,tOrders.AbstractorFee,tOrders.CustomerAmount,`mOrderTypes`.`OrderTypeName`,VendorAssignedDateTime, `mProjects`.`ProjectName`,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`) LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mabstractor` ON `mabstractor`.`AbstractorUID` = `tOrders`.`AbstractorUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `tOrderPropertyRoles` ON `tOrders`.`OrderUID` = `tOrderPropertyRoles`.`OrderUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` LEFT JOIN `mOrderTypes` ON `mOrderTypes`.`OrderTypeUID` = `tOrders`.`OrderTypeUID`  LEFT JOIN `mProjects` ON `mProjects`.`ProjectUID` = `tOrders`.`ProjectUID` WHERE `tOrders`.`StatusUID` IN (".$statuses.") AND `tOrderAssignment`.`AssignedToUserUID` = ".$loggedid." AND tOrderAssignment.WorkflowModuleUID !=4 $where $like $filter GROUP BY `OrderUID` ".$order_by." ";
		} else {

			$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].
			','.$this->config->item('keywords')['Cancelled'].','.$this->config->item('keywords')['Exception Raised'];


			if (!in_array($this->session->userdata('RoleType'), [1,2,3,4,5,6])) {
				$usergroups = $this->common_model->get_user_group_id($this->loggedid);

				if (!empty($usergroups)) {

					$usergroups = implode(',', $usergroups);
				// $groupcustomers = $this->common_model->get_customer_ingroup($usergroups);
					$loggedin_userproducts = $this->My_orders_model->get_subproducts_by_groupuids($usergroups);
					/*@Desc Group Setup Changed @Author Jainulabdeen @Updated Aug 8 2020*/
					$where .= 'AND '.$loggedin_userproducts;
					/*if (!empty($loggedin_userproducts)) {
						$subproductuids='';
						foreach ($loggedin_userproducts as $key => $value) {
							$subproductuids = $value->SubProductUID . ', ';
						}
						$subproductuids = rtrim($subproductuids,  ', ');


						$where .= " AND (tOrders.SubProductUID IN (".$subproductuids.") OR `tOrderAssignment`.`AssignedToUserUID` = ".$loggedid." AND tOrderAssignment.WorkflowModuleUID !=4) ";
					// echo $where; exit;
					}*/
				}
			}

			$sql = "SELECT `CustomerNumber`,`CustomerName`, `OrderNumber`,`AltORderNumber`,`tOrders`.`CustomerUID`,`tOrders`.`SubProductUID`,`mProducts`.`IsSelfAssign`,`LoanNumber`, `StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`,`tOrders`.`PropertyZipcode`, `tOrders`.`OrderEntryDatetime` as OrderEntryDatetime, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(tOrderAssignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime, TRIM(CONCAT_WS(' ',TRIM(tOrders.PropertyAddress1),TRIM(tOrders.PropertyAddress2))) AS whole_name ,tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.IsInhouseExternal,tOrders.PropertyCountyName,mabstractor.AbstractorNo,mabstractor.AbstractorCompanyName,mabstractor.AbstractorFirstName,tOrders.AbstractorFee,tOrders.CustomerAmount,mOrderTypes.OrderTypeName,tOrders.PropertyZipcode,VendorAssignedDateTime, `mProjects`.`ProjectName`,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`)  LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `tOrderPropertyRoles` ON `tOrders`.`OrderUID` = `tOrderPropertyRoles`.`OrderUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mabstractor` ON `mabstractor`.`AbstractorUID` = `tOrders`.`AbstractorUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` LEFT JOIN `mOrderTypes` ON `mOrderTypes`.`OrderTypeUID` = `tOrders`.`OrderTypeUID` LEFT JOIN `mProjects` ON `mProjects`.`ProjectUID` = `tOrders`.`ProjectUID` WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.") $where $like $filter GROUP BY `OrderUID` ".$order_by." "; 

		}

		$query = $this->db->query($sql);
		return $query->result_array();
	}

function GetSelectedCustomProducts($RoleUID)
{
  if($RoleUID==8)
  {
    $this->db->select('mProducts.ProductUID, mProducts.ProductName,mSubProducts.SubProductUID,mSubProducts.SubProductName');
    $this->db->from('mCustomerProducts');
    $this->db->join ('mProducts','mCustomerProducts.ProductUID = mProducts.ProductUID','left');
    $this->db->join ('mSubProducts','mCustomerProducts.SubProductUID = mSubProducts.SubProductUID','left');
    $this->db->join ('mUsers','mUsers.CustomerUID=mCustomerProducts.CustomerUID','inner');
    $this->db->where('mUsers.UserUID',$this->session->userdata('UserUID'));
    $this->db->group_by('mProducts.ProductUID');
    return $this->db->get()->result(); 
  } else {
    return '';
  }
}


	function lastviewed_orders($loggedid)
	{

		$data = $this->db->query("SELECT `OrderUID` FROM `tlastviewedorders` WHERE UserUID = '".$loggedid."' ")->row();

		if(count($data) > 0){

			$OrderUIDs = $data->OrderUID;

			if($OrderUIDs != ''){

				$OrderUIDs =  rtrim($OrderUIDs, ',');

				/*FOR SUPERVISOR CHECK*/
				$productwhere = '';
				if ($this->session->userdata('RoleType') == 6){
					$UserProducts = $this->common_model->_get_product_bylogin();
					if($UserProducts): $productwhere .= ' AND `mProducts`.`ProductUID` IN ('.$UserProducts.')'; else: return array(); endif;
				}
$where='';
				if($this->common_model->GetMyOrdersQueue() == 1)
				{

					$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'].','.$this->config->item('keywords')['Reopened Order'];
					$where .= "WHERE `tOrders`.`StatusUID` IN (".$statuses.") AND `tOrders`.`OrderUID` IN (".$OrderUIDs.") $productwhere GROUP BY `OrderUID` ORDER BY FIELD(tOrders.OrderUID,".$OrderUIDs."),FIELD(tOrders.PriorityUID,'3','1') DESC,PriorityUID, `tOrders`.`OrderEntryDatetime` ASC LIMIT 10";
					
				}else{

					$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].','.$this->config->item('keywords')['Cancelled'].','.$this->config->item('keywords')['Exception Raised'];
					$where .= "WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.") AND `tOrders`.`OrderUID` IN (".$OrderUIDs.") $productwhere GROUP BY `OrderUID` ORDER BY FIELD(tOrders.OrderUID,".$OrderUIDs."),FIELD(tOrders.PriorityUID,'3','1') DESC,PriorityUID, `tOrders`.`OrderEntryDatetime` ASC LIMIT 10";
					
				}




				$sql = "SELECT `CustomerNumber`,`CustomerName`, `OrderNumber`, `StatusName`, `tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`, tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`) LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` ".$where." ";



				$query = $this->db->query($sql);
				return $query->result_array();

			}else{
				return array();
			}

		}else{

			return array();

		}
	}



	function get_orderbyid($OrderUID)
	{
		if($OrderUID){
			$this->db->select ( '*' ); 
			$this->db->select('DATE_FORMAT(OrderEntryDatetime, "%m-%d-%Y") as OrderEntryDatetime', FALSE);
			$this->db->from ( 'tOrders' );
			$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
			$this->db->join ( 'mOrderTypes', 'mOrderTypes.OrderTypeUID = tOrders.OrderTypeUID' , 'left' );
			$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
			$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = tOrders.SubProductUID' , 'left' );
			$this->db->join ( 'mProducts', 'mProducts.ProductUID = mSubProducts.ProductUID' , 'left' );
			$this->db->where ('tOrders.OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->row();
		}

	}

	function customer_workflow($CustomerUID,$SubProductUID){
		$this->db->distinct();
		$this->db->select ('CustomerUID,WorkflowModuleName,mCustomerWorkflowModules.workflowmoduleUID'); 
		$this->db->from ( 'mCustomerWorkflowModules' );
		$this->db->join ( 'mWorkflowModules', 'mCustomerWorkflowModules.workflowmoduleUID = mWorkflowModules.WorkflowModuleUID' , 'left' );
		$this->db->where ('mCustomerWorkflowModules.CustomerUID',$CustomerUID);
		$this->db->where ('mCustomerWorkflowModules.SubProductUID',$SubProductUID);
		$query = $this->db->get();
		$workflowmodules =  $query->result_array();
		return $workflowmodules;
	}

	function is_workflow_assigned($OrderUID,$WorkflowUID){

		$this->db->select ( '*' ); 
		$this->db->from ( 'tOrderAssignment');
		$this->db->where ( 'WorkflowModuleUID',$WorkflowUID);
		$this->db->where('AssignedToUserUID is NOT NULL', NULL, FALSE);
		$this->db->where ( 'OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->num_rows();
	}

	function assign_selectedorders($orderUID,$loggedid,$customer_workflow,$filter_workflow)
	{
		$status = $this->config->item('keywords')['Order Assigned'];

		$workflows = $this->common_model->get_role_workflows();
		$order_details = $this->common_model->get_orderdetails($orderUID);

		$this->db->trans_begin();

		foreach ($customer_workflow as $workflow_key => $workflow_value) {
			if((in_array($workflow_value['workflowmoduleUID'], $workflows)) &&  ($workflow_value['workflowmoduleUID'] == $filter_workflow )){
				if(($workflow_value['workflowmoduleUID'] != '4') && ($this->is_workflow_assigned($orderUID,$workflow_value['workflowmoduleUID']) == 0)) {





					$this->db->where('tOrderAssignment.AssignedToUserUID IS NULL');
					$this->db->where('tOrderAssignment.OrderUID',$orderUID);
					$this->db->where('tOrderAssignment.WorkflowModuleUID',$filter_workflow);
					$this->db->where('tOrderAssignment.WorkflowStatus !=',5);
					$q = $this->db->get('tOrderAssignment');


					if ( $q->num_rows() > 0 ) 
					{
						$updatedata = array(
							'AssignedToUserUID' => $loggedid,
							'AssignedByUserUID' => $loggedid,
							'AssignedDatetime' =>  Date('Y-m-d H:i:s',strtotime("now")),
							'WorkflowStatus' => '0',
							'SelfManualAssign'=>'SELF'

						);
						$this->db->where('tOrderAssignment.WorkflowModuleUID',$filter_workflow);
						$this->db->where('tOrderAssignment.OrderUID',$orderUID);
						$inserted = $this->db->update('tOrderAssignment',$updatedata);
					} else {

						$assign_data = array(
							'OrderUID' => $orderUID,
							'WorkflowModuleUID' => $filter_workflow,
							'AssignedToUserUID' => $loggedid,
							'AssignedDatetime' =>  Date('Y-m-d H:i:s',strtotime("now")),
							'AssignedByUserUID' => $loggedid,
							'WorkflowStatus' => '0',
							'SelfManualAssign'=>'SELF'

						);
						$inserted = $this->db->insert('tOrderAssignment',$assign_data);

					}

					if($order_details->StatusUID == $this->config->item('keywords')['New Order'] || $order_details->StatusUID == $this->config->item('keywords')['Reopened Order'] || $order_details->StatusUID == $this->config->item('keywords')['Order Assigned']){
						$orders_data = array(
							'LastTouchDateTime' => Date('Y-m-d H:i:s',strtotime("now")),
							'LastModifiedByUserUID' => $loggedid,
							'StatusUID' => $status,
						);
					}else{
						$orders_data = array(
							'LastTouchDateTime' => Date('Y-m-d H:i:s',strtotime("now")),
							'LastModifiedByUserUID' => $loggedid,
						);
					}
					if(isset($inserted)){
						$this->db->set($orders_data)
						->where('OrderUID', $orderUID)
						->update('tOrders');
					}

				}

			}
		}
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}
		else
		{
			$this->db->trans_commit();
			return true;
		}

	}


	function get_customer_pendingorders($CustomerUID)
	{
  // $Customers = $this->common_model->GetCustomerDetails();

		$status = $this->config->item('keywords')['New Order'];

		$this->db->select('*');

		$this->db->from ( 'tOrders' );
		$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID');

		$this->db->where("tOrders.OrderUID NOT IN (SELECT DISTINCT `tOrderAssignment`.`OrderUID` FROM `tOrderAssignment`)",NULL, false);

		$this->db->where ( 'tOrders.CustomerUID',$CustomerUID);

		$this->db->where ("tOrders.StatusUID",$status);

		$query = $this->db->get();

		return $query->num_rows();


	}


	function customer_workflowby_Cusid($CustomerUID){


		$this->db->distinct();
		$this->db->select ('CustomerUID,WorkflowModuleName,mCustomerWorkflowModules.workflowmoduleUID'); 
		$this->db->from ( 'mCustomerWorkflowModules' );
		$this->db->join ( 'mWorkflowModules', 'mCustomerWorkflowModules.workflowmoduleUID = mWorkflowModules.WorkflowModuleUID' , 'left' );
		$this->db->where ('mCustomerWorkflowModules.CustomerUID',$CustomerUID);
      // $this->db->where ('mCustomerWorkflowModules.SubProductUID',$SubProductUID);
		$query = $this->db->get();


		$workflowmodules =  $query->result();
		return $workflowmodules;
	}


	function Get_pending_orders($loggedid)
	{

		$group_id = $this->db->query("SELECT GROUP_CONCAT(GroupUID SEPARATOR ',') as group_id FROM mgroupusers where GroupUserUID = $loggedid")->row(); 


		if(count($group_id->group_id) != '' )
		{



			$groupIDs = $group_id->group_id; 

			$cust_id = $this->db->query("SELECT GROUP_CONCAT(DISTINCT GroupCustomerUID SEPARATOR ',') as cust_id FROM mGroupCustomers where GroupUID IN ($groupIDs)")->row();

			if($cust_id->cust_id != '')
			{
				$customer_id = $cust_id->cust_id;

				$status = $this->config->item('keywords')['New Order'];

				$this->db->select ( "*"); 
				$this->db->select("DATE_FORMAT(OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", FALSE);
				$this->db->from ( "tOrders" );
				$this->db->join ( "mCustomers", "tOrders.CustomerUID = mCustomers.CustomerUID");
				$this->db->where("tOrders.CustomerUID IN (".$customer_id.")",NULL, false);
				$this->db->where ("tOrders.StatusUID",$status);
				$query = $this->db->get();

				return $query->result_array();

			}else{
				return array();
			}
		}else{
			return array();
		}

	}


	function GetCustomersbyOrders($loggedid){
		$group_id = $this->db->query("SELECT GROUP_CONCAT(GroupUID SEPARATOR ',') as group_id FROM mgroupusers where GroupUserUID = $loggedid")->row(); 
    // echo '<pre>';print_r($group_id->group_id);exit;

		if(count($group_id->group_id) != '' )
		{

			$groupIDs = $group_id->group_id; 

			$cust_id = $this->db->query("SELECT GROUP_CONCAT(DISTINCT GroupCustomerUID SEPARATOR ',') as cust_id FROM mGroupCustomers where GroupUID IN ($groupIDs)")->row();

			if($cust_id->cust_id != '')
			{
				$customer_id = $cust_id->cust_id;

				$status = $this->config->item('keywords')['New Order'];

				$this->db->distinct(); 
				$this->db->select ("CustomerUID,CustomerName"); 
				$this->db->from ( "mCustomers" );
				$this->db->where("mCustomers.CustomerUID IN (".$customer_id.")",NULL, false);
				$query = $this->db->get();

				return $query->result_array();

			}else{
				return array();
			}
		}else{

			return array();
		}
	}

	function cancel_order($orderUID,$Remarks,$Reason,$loggedid,$CancellationRequestDateTime)
	{
		$ApprovalFunction = 'Order Cancellation';
		$ApprovalStatus = '0';
		$query = $this->db->query('INSERT INTO  torderapprovals(OrderUID,ApprovalFunction,RaisedByUserUID,RaisedDatetime,ApprovalStatus,Remark)VALUES
			("'.$orderUID.'","'.$ApprovalFunction.'","'.$loggedid.'","'.$CancellationRequestDateTime.'","'.$ApprovalStatus.'","'.$Remarks.'")');
		if($this->db->affected_rows() > 0)
		{
			$this->db->query('INSERT INTO tordercancel(OrderUID,Remarks,ReasonUID,RequestedBy,CancelStatus,CancellationRequestTime)VALUES("'.$orderUID.'","'.$Remarks.'","'.$Reason.'","'.$loggedid.'",0,"'.$CancellationRequestDateTime.'")'); //@Desc Reason added @Author Jainulabdeen @Updated on 4-7-2020

			$data1['ModuleName']='Order Cancel Requst Status-update';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='tordercancel';
			$data1['OrderUID']=$orderUID;
			$data1['UserUID']=$this->session->userdata('UserUID'); 
			$data1['OldValue']=''; 
			$data1['FieldUID']='1138';
			$data1['NewValue']='110';                 
			$this->common_model->Audittrail_insert($data1); 

			/*@Desc Save Audit Log for Cancel Order with reason and Remarks
			@Author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
			@Added on Apr 10 2020*/	
			$msg = '<b>Order is Cancelled</b>';
			if(!empty($Remarks)){
				$msg .= ' with <b>Remarks : '.$Remarks.'</b>';
			}
			if(!empty($Reason)){
				$ReasonCon = $this->db->select('*')->from('moverridereasons')->where('OverrideReasonUID',$Reason)->get('')->row()->OverrideReasonDescription;

				$msg .= ' and <b>Reason : '.$ReasonCon.'</b>';
			}
		
			$InsetData = array(
				'UserUID' => $this->loggedid,
				'ModuleName' => 'OrderCancel',
				'OrderUID' => $orderUID,
				'Content' => htmlentities($msg),
				'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($InsetData);
			/*End Audit Log For Cancel Order*/
			return true;
		}
		else
		{
			return false;
		}
	}

	function approve_cancel_order($orderUID,$Remarks,$Reason,$loggedid,$CancellationRequestDateTime)
	{
		$query = $this->db->query('INSERT INTO tordercancel(OrderUID,Remarks,ReasonUID,RequestedBy,ApprovedBy,CancelStatus,CancellationRequestTime,CancellationApproveDeclineTime)VALUES("'.$orderUID.'","'.$Remarks.'","'.$Reason.'","'.$loggedid.'","'.$loggedid.'",1,"'.$CancellationRequestDateTime.'","'.$CancellationRequestDateTime.'")'); //@Desc Reason added @Author Jainulabdeen @Updated on 4-7-2020
		if($this->db->affected_rows() > 0)
		{
			$this->db->set('StatusUID','110')->where('OrderUID',$orderUID)->update('tOrders');
			$this->real_ec_model->CancelApiOrder($orderUID,$Remarks);

			$this->load->model('api_abstractor/Api_abstractor_model');
			$this->Api_abstractor_model->GenerateXMLSamples($orderUID,'AT07',$Remarks);

			$data1['ModuleName']='OrderStatus-update';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='tordercancel';
			$data1['OrderUID']=$orderUID;
			$data1['UserUID']=$this->session->userdata('UserUID'); 
			$data1['OldValue']=''; 
			$data1['FieldUID']='1138';
			$data1['NewValue']='110';                 
			$this->common_model->Audittrail_insert($data1); 
			
			/*@Desc Save Audit Log for Cancel Order with reason and Remarks
			@Author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
			@Added on Apr 10 2020*/	
			$msg = '<b>Order is Cancelled</b>';
			if(!empty($Remarks)){
				$msg .= ' with <b>Remarks : '.$Remarks.'</b>';
			}
			if(!empty($Reason)){
				$ReasonCon = $this->db->select('*')->from('moverridereasons')->where('OverrideReasonUID',$Reason)->get('')->row()->OverrideReasonDescription;

				$msg .= ' and <b>Reason : '.$ReasonCon.'</b>';
			}
		
			$InsetData = array(
				'UserUID' => $this->loggedid,
				'ModuleName' => 'OrderCancel',
				'OrderUID' => $orderUID,
				'Content' => htmlentities($msg),
				'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($InsetData);
			/*End Audit Log For Cancel Order*/
			return true;
		}
		else
		{
			return false;
		}
	}

	function getCancelOrderStatus($OrderUID)
	{
		$query = $this->db->query("SELECT OrderUID FROM tordercancel WHERE CancelStatus = '1' and OrderUID = '".$OrderUID."' ");
		$res = $query->row_array();
		return $res;

	}

	function getSearchCompleteStatus($orderUID)
	{
		$query = $this->db->query("SELECT WorkflowStatus FROM `tOrderAssignment` WHERE WorkflowModuleUID = 1 and WorkflowStatus = 5 and OrderUID = $orderUID ");
		if($query->num_rows() > 0)
		{
			return true;
		}
		else{
			return false;
		}
	}

	function CheckCancelOrderExist($orderUID)
	{
		$query = $this->db->query("SELECT OrderUID FROM `torderapprovals` WHERE OrderUID = $orderUID and ApprovalStatus = '0' and ApprovalFunction = 'Order Cancellation' ");
		if($query->num_rows() > 0)
		{
			return true;
		}
		else{
			return false;
		}
	}

	function get_onhold_orders($loggedid)
	{
		$status[0] = $this->config->item('keywords')['Order Assigned'];
		$status[1] = $this->config->item('keywords')['Order Work In Progress'];
		$status[2] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[3] = $this->config->item('keywords')['Partial Review Complete'];
		$status[4] = $this->config->item('keywords')['Review In Progress'];
		$status[5] = $this->config->item('keywords')['Reopened Order'];
    //$status[2] = $this->config->item('keywords')['New Order'];

		$this->db->select ( 'tOrderAssignment.OrderUID,OrderAssignmentUID' );
		$this->db->from ( 'tOrderAssignment' );
		$this->db->join ( 'tOrders', 'tOrderAssignment.OrderUID = tOrders.OrderUID');

		$this->db->where_in('tOrders.StatusUID', $status);
		$this->db->where('tOrderAssignment.AssignedToUserUID',$loggedid);
		$this->db->where('tOrderAssignment.WorkflowStatus',4);
		$this->db->where('tOrderAssignment.WorkflowModuleUID !=',4);
		$this->db->group_by('tOrders.OrderUID');
		$query = $this->db->get();
		return $query->result();
	}

	function check_pending_orders($OnholdUIDs,$loggedid)
	{

		$status[0] = $this->config->item('keywords')['Order Assigned'];
		$status[1] = $this->config->item('keywords')['Reopened Order'];
		$status[2] = $this->config->item('keywords')['Order Work In Progress'];
		$status[3] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[4] = $this->config->item('keywords')['Partial Review Complete'];
		$status[5] = $this->config->item('keywords')['Review In Progress'];
    //$status[2] = $this->config->item('keywords')['New Order'];

		$this->db->select ( '*' );
		$this->db->from ( 'tOrderAssignment' );
		$this->db->join ( 'tOrders', 'tOrderAssignment.OrderUID = tOrders.OrderUID');

		$this->db->where_in('tOrders.StatusUID', $status);

		if(count($OnholdUIDs) > 0){
			$this->db->where_not_in('tOrders.OrderUID', $OnholdUIDs);
		}
		$this->db->where('tOrderAssignment.AssignedToUserUID',$loggedid);
		$this->db->where('tOrderAssignment.WorkflowStatus !=',4);
		$this->db->where('tOrderAssignment.WorkflowStatus !=',5);
		$this->db->group_by('tOrders.OrderUID');
		$query = $this->db->get();
		return $query->result();
	}

	function getnext_order_all($postarray){

		$filter_workflow = $postarray['filter_workflow'];
		$GroupUID = $postarray['GroupUID'];
		$SubProductUID = $postarray['SubProductUID'];

		$status1 = $this->config->item('keywords')['New Order'];
		$status2 = $this->config->item('keywords')['Order Assigned'];
		$status3 = $this->config->item('keywords')['Order Work In Progress'];
		$status4 = $this->config->item('keywords')['Partial Draft Complete'];
		$status5 = $this->config->item('keywords')['Partial Review Complete'];
		$status6 = $this->config->item('keywords')['Review In Progress'];
		$status7 = $this->config->item('keywords')['Reopened Order'];


		$subproduct_where  = '';
		if($SubProductUID != ''){
			$subproduct_where  .= "AND mGroupCustomers.GroupCustomerSubProductUID = '".$SubProductUID."' ";
		}else{
			$customer_ids = $this->get_customeringroup($GroupUID);
			$cus_subproducts = [];
			if(count($customer_ids) > 0){

				$cus_subproducts = $this->get_customerproductandsubproduct($customer_ids);
			}
			

			if(count($cus_subproducts) > 0){
				if($cus_subproducts->SubProductUIDs !='' ){
					$subproduct_where .= 'AND mGroupCustomers.GroupCustomerSubProductUID IN ('.$cus_subproducts->SubProductUIDs.')';
				}
			}
			
		}

		$check_workflow_permissions = $this->check_workflow_permissions($filter_workflow);

		$where  = '';
		if(count($check_workflow_permissions) > 0){

			if($check_workflow_permissions->DependentWorkflowModule != ''){

				$where .= "AND `tOrders`.`OrderUID` IN (SELECT `OrderUID` FROM `tOrderAssignment` WHERE  `WorkflowModuleUID`  = $check_workflow_permissions->DependentWorkflowModule AND WorkflowStatus  = 5)";
			}
		}

		$this->db->select('GROUP_CONCAT(DISTINCT `OrderUID`) as  OrderUID '); 
		$this->db->where('torderunassignment.AssignedToUserUID',$this->session->userdata('UserUID'));
		$q = $this->db->get('torderunassignment');
		$unassigned = $q->row();

		if ( $unassigned->OrderUID != '' ) 
		{
			$where .= "AND tOrders.OrderUID NOT IN ($unassigned->OrderUID)";
		}

		/*--- INHOUSE ORDERS FOR SEARCH ----*/
		if($filter_workflow == '1' ){
			$where .= "AND tOrders.IsInhouseExternal = '0' ";
		}

		//orderby processing workflow prioritization
		$workflowprioritization = $this->common_model->getnextorderworkflowprioritization($filter_workflow);
		if(isset($workflowprioritization) && !empty($workflowprioritization)) {

			$order_by = ' ORDER BY '.$workflowprioritization.',FIELD(`tOrders`.`PriorityUID`,3,1) DESC  , `tOrders`.`OrderEntryDatetime` ASC';

		} else {

			$order_by = 'ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC';

		}


      $query = $this->db->query("SELECT `tOrders`.`OrderUID`, `OrderNumber`, `tOrders`.`CustomerUID`, `CustomerName`, `OrderTypeName`, `StateName`, `PriorityName`, `tOrders`.`SubProductUID`, `mSubProducts`.`SubProductName`, `mProducts`.`ProductUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mstates`.`StateCode`, `mOrderStatus`.`StatusName`, DATE_FORMAT(OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime, DATE_FORMAT(`tOrders`.`OrderDueDatetime`, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime ,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`) LEFT JOIN `mstates` ON `PropertyStateUID` = `mstates`.`StateUID` JOIN `mCustomers` ON `tOrders`.`CustomerUID` = `mCustomers`.`CustomerUID` LEFT JOIN `mOrderTypes` ON `mOrderTypes`.`OrderTypeUID` = `tOrders`.`OrderTypeUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `tOrderAssignment` ON  `tOrderAssignment`.`OrderUID` = `tOrders`.`OrderUID` LEFT JOIN `mGroupCustomers` ON  `mGroupCustomers`.`GroupCustomerSubProductUID` = `tOrders`.`SubProductUID` WHERE `tOrders`.`OrderUID` NOT IN (SELECT `OrderUID` FROM `tOrderAssignment` WHERE  `WorkflowModuleUID` ='".$filter_workflow."' AND AssignedToUserUID IS NOT NULL) AND tOrders.OrderUID NOT IN (SELECT tOrderAbstractor.OrderUID FROM tOrderAbstractor LEFT JOIN tOrderAssignment ON tOrderAssignment.OrderUID = tOrderAbstractor.OrderUID WHERE DocumentReceived = 0 AND (tOrderAssignment.WorkflowModuleUID = 1 AND tOrderAssignment.WorkflowStatus != 5)) AND tOrders.SubProductUID = mGroupCustomers.GroupCustomerSubProductUID  AND mGroupCustomers.GroupUID = '".$GroupUID."' ".$subproduct_where." AND `tOrders`.`StatusUID` IN ('".$status1."', '".$status2."', '".$status3."','".$status4."','".$status5."','".$status6."') ".$where."  ".$order_by." ");

		$result =  $query->result();

		if(count($result)>0){

			return array("message"=>"Order Assigned","status"=>"success","data"=>$result[0],'Error'=>'0');

		}else{

			return array("message"=>"No Orders","status"=>"danger","data"=>"",'Error'=>'1');

		}
	}


	function get_assigned_orders($loggedid)
	{
		$status[0] = $this->config->item('keywords')['Order Assigned'];
		$status[1] = $this->config->item('keywords')['Order Work In Progress'];
		$status[2] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[3] = $this->config->item('keywords')['Partial Review Complete'];
		$status[4] = $this->config->item('keywords')['Review In Progress'];
		$status[5] = $this->config->item('keywords')['Reopened Order'];

		$this->db->select ( 'tOrders.OrderUID' ); 
		$this->db->from ( 'tOrderAssignment' );
		$this->db->join ( 'tOrders', 'tOrderAssignment.OrderUID = tOrders.OrderUID');
		$this->db->where ('tOrderAssignment.AssignedToUserUID',$loggedid);
		$this->db->where_in('tOrders.StatusUID', $status);
		$this->db->where ('tOrderAssignment.WorkflowModuleUID !=',4);
		$this->db->where ('tOrderAssignment.WorkflowStatus !=',5);
		$this->db->group_by('tOrderAssignment.OrderUID');
		$query = $this->db->get();
		return $query->result();
	}  

	function check_workflow_completed($OrderUID,$WorkflowUID,$loggedid)
	{

		$this->db->select ( 'tOrderAssignment.OrderUID' ); 
		$this->db->from ( 'tOrderAssignment' );
		$this->db->join ( 'tOrders', 'tOrderAssignment.OrderUID = tOrders.OrderUID');
      //$this->db->where ('tOrderAssignment.AssignedToUserUID',$loggedid);
		$this->db->where ('tOrderAssignment.WorkflowModuleUID',$WorkflowUID);
		$this->db->where ('tOrderAssignment.WorkflowStatus',5);
		$this->db->where ('tOrderAssignment.OrderUID',$OrderUID);
		$this->db->group_by('tOrderAssignment.OrderUID');
		$query = $this->db->get();
		return $query->result();
	}


	function check_searchdocument($OrderUID){
		$this->db->select ( '*' ); 
		$this->db->from ( 'torderdocuments' );
		$this->db->where ('OrderUID',$OrderUID);
		$this->db->where ('IsReport',0);
		$query = $this->db->get();
		return $query->num_rows();
	}


	function get_next_order($loggedid,$postarray){

		$filter_workflow = $postarray['filter_workflow'];
		$GroupUID = $postarray['GroupUID'];


		$SubProductUID = $postarray['SubProductUID'];

		$subproduct_where  = '';
		if($SubProductUID != ''){
			$subproduct_where  .= "AND mGroupCustomers.GroupCustomerSubProductUID = '".$SubProductUID."' ";
		}else{
			$customer_ids = $this->get_customeringroup($GroupUID);
			$cus_subproducts = [];
			if(count($customer_ids) > 0){

				$cus_subproducts = $this->get_customerproductandsubproduct($customer_ids);
			}
			

			if(count($cus_subproducts) > 0){
				if($cus_subproducts->SubProductUIDs !='' ){
					$subproduct_where .= 'AND mGroupCustomers.GroupCustomerSubProductUID IN ('.$cus_subproducts->SubProductUIDs.')';
				}
			}
			
		}



		/*checking workflow permissions ---dependent workflow module*/
		$check_workflow_permissions = $this->check_workflow_permissions($filter_workflow);
		$where  = '';
		if(count($check_workflow_permissions) > 0){

			if($check_workflow_permissions->DependentWorkflowModule != ''){

				$where .= "AND `tOrders`.`OrderUID` IN (SELECT `OrderUID` FROM `tOrderAssignment` WHERE  `WorkflowModuleUID`  = $check_workflow_permissions->DependentWorkflowModule AND WorkflowStatus  = 5)";
			}
		}

		$group_id = $this->db->query("SELECT GROUP_CONCAT(GroupUID SEPARATOR ',') as group_id FROM mgroupusers where GroupUserUID = $loggedid")->row(); 
		if(count($group_id->group_id) != '' )
		{
			


			$this->db->select('GROUP_CONCAT(DISTINCT `OrderUID`) as  OrderUID '); 
			$this->db->where('torderunassignment.AssignedToUserUID',$this->session->userdata('UserUID'));
			$q = $this->db->get('torderunassignment');
			$unassigned = $q->row();

			if ( $unassigned->OrderUID != '' ) 
			{

				$where .= "AND tOrders.OrderUID NOT IN ($unassigned->OrderUID)";

			}

			$groupIDs = $group_id->group_id; 

			$cust_id = $this->db->query("SELECT GROUP_CONCAT(DISTINCT GroupCustomerUID SEPARATOR ',') as cust_id FROM mGroupCustomers where GroupUID IN ($groupIDs)")->row();

			if($cust_id->cust_id != '')
			{
				$customer_id = $cust_id->cust_id;

				$status1 = $this->config->item('keywords')['New Order'];
				$status2 = $this->config->item('keywords')['Order Assigned'];
				$status3 = $this->config->item('keywords')['Order Work In Progress'];
				$status4 = $this->config->item('keywords')['Partial Draft Complete'];
				$status5 = $this->config->item('keywords')['Partial Review Complete'];
				$status6 = $this->config->item('keywords')['Review In Progress'];
				$status7 = $this->config->item('keywords')['Reopened Order'];


				/*--- INHOUSE ORDERS FOR SEARCH ----*/
				if($filter_workflow == '1' ){
					$where .= "AND tOrders.IsInhouseExternal = '0' ";
				}
				//orderby processing workflow prioritization
				$workflowprioritization = $this->common_model->getnextorderworkflowprioritization($filter_workflow);
				if(isset($workflowprioritization) && !empty($workflowprioritization)) {

					$order_by = ' ORDER BY '.$workflowprioritization.',FIELD(`tOrders`.`PriorityUID`,3,1) DESC  , `tOrders`.`OrderEntryDatetime` ASC';

				} else {

					$order_by = 'ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC';
				}

				$query = $this->db->query("SELECT `tOrders`.`OrderUID`, `OrderNumber`, `tOrders`.`CustomerUID`, `CustomerName`, `OrderTypeName`, `PriorityName`, `tOrders`.`SubProductUID`, `mSubProducts`.`SubProductName`, `mProducts`.`ProductUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mOrderStatus`.`StatusName`, DATE_FORMAT(OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime, DATE_FORMAT(`tOrders`.`OrderDueDatetime`, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`)  JOIN `mCustomers` ON `tOrders`.`CustomerUID` = `mCustomers`.`CustomerUID` LEFT JOIN `mOrderTypes` ON `mOrderTypes`.`OrderTypeUID` = `tOrders`.`OrderTypeUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `tOrderAssignment` ON  `tOrderAssignment`.`OrderUID` = `tOrders`.`OrderUID` JOIN `mGroupCustomers` ON `mGroupCustomers`.`GroupCustomerUID` = `tOrders`.`CustomerUID` WHERE `tOrders`.`CustomerUID` IN ($customer_id) AND `tOrders`.`OrderUID` NOT IN (SELECT `OrderUID` FROM `tOrderAssignment` WHERE  `WorkflowModuleUID` ='".$filter_workflow."' AND AssignedToUserUID IS NOT NULL ) 
					AND `tOrders`.`OrderUID` NOT IN (SELECT `OrderUID` FROM `tOrderAssignment` WHERE  `WorkflowModuleUID` ='".$filter_workflow."' AND SendToVendor = '1') AND tOrders.SubProductUID = mGroupCustomers.GroupCustomerSubProductUID AND mGroupCustomers.GroupUID = '".$GroupUID."'  ".$subproduct_where." AND `tOrders`.`StatusUID` IN ('".$status1."', '".$status2."', '".$status3."','".$status4."','".$status5."','".$status6."','".$status7."') ".$where." AND tOrders.OrderUID NOT IN (SELECT tOrderAbstractor.OrderUID FROM tOrderAbstractor LEFT JOIN tOrderAssignment ON tOrderAssignment.OrderUID = tOrderAbstractor.OrderUID WHERE DocumentReceived = 0 AND (tOrderAssignment.WorkflowModuleUID = 1 AND tOrderAssignment.WorkflowStatus != 5))  GROUP BY `tOrders`.`OrderUID`  ".$order_by."");

				$Orders =  $query->result();
				if(count($Orders)>0){

					return array("message"=>"Order Assigned","status"=>"success","data"=>$Orders[0],'Error'=>'0');

				}else{

					return array("message"=>"No Orders","status"=>"danger","data"=>"",'Error'=>'1');

				}


			}else{
				return array("message"=>"No customer in group","status"=>"danger","data"=>"",'Error'=>'1');

			}



		} else {
			return array("message"=>"User Not in Group","status"=>"danger","data"=>"",'Error'=>'1');

		}



	}


	function get_assigned_users($OrderUID,$loggedid)
	{

		$this->db->select ( 'Group_concat(LoginID) as LoginID' ); 
		$this->db->from ( 'tOrderAssignment' );
		$this->db->join ( 'mUsers', 'mUsers.UserUID = tOrderAssignment.AssignedToUserUID');
    //$this->db->where ('tOrderAssignment.AssignedToUserUID',$loggedid);
		$this->db->where ('tOrderAssignment.WorkflowModuleUID !=',4);
		$this->db->where ('tOrderAssignment.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->row();
	}

	function check_workflow_permissions($WorkflowModuleUID){
		$this->db->select ( "a.WorkflowModuleUID,a.WorkflowModuleName,a.CanIndependentWorkflowModule,a.DependentWorkflowModule,b.WorkflowModuleName as DependentWorkflowModulename" ); 
		$this->db->from ( "mWorkflowModules as a" );
		$this->db->join ( 'mWorkflowModules as b', 'b.workflowmoduleUID = a.DependentWorkflowModule' , 'left' );
		$this->db->where ("a.WorkflowModuleUID",$WorkflowModuleUID);
		$query = $this->db->get();
		return $query->row();

	}

	function get_groupsby_loggedid(){

		$where = '';
		$loggedid = $this->session->userdata('UserUID');
		if (in_array($this->session->userdata('RoleType'),array(1,2,3,4,5,6)) == FALSE){
			$where = 'AND GroupUserUID = '.$loggedid.'';
		}


		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
		}

		$query = $this->db->query("SELECT DISTINCT mgroups.GroupUID,GroupName FROM mgroups LEFT JOIN mgroupusers on mgroupusers.GroupUID = mgroups.GroupUID LEFT JOIN mGroupCustomers ON mGroupCustomers.GroupUID = mgroups.GroupUID WHERE mgroups.Active=1 and GroupType = 'C' ".$where." ORDER BY GroupName ASC ");
		$result =  $query->result();
		return $result;


	}




	function get_prod_by_groupuid($GroupUID){
		/*FOR SUPERVISOR CHECK*/
		$where = '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
		}

		$query = $this->db->query("SELECT mGroupCustomers.GroupCustomerProductUID as ProductUID,mGroupCustomers.GroupCustomerSubProductUID As SubProductUID,ProductName,SubProductName FROM mgroups LEFT JOIN mGroupCustomers ON mGroupCustomers.GroupUID = mgroups.GroupUID  LEFT JOIN mSubProducts on mSubProducts.SubProductUID = mGroupCustomers.GroupCustomerSubProductUID LEFT JOIN mProducts on mProducts.ProductUID = mGroupCustomers.GroupCustomerProductUID WHERE mgroups.GroupUID = $GroupUID $where  Group by mGroupCustomers.GroupCustomerProductUID");
		return $query->result();
	}

	function get_products_by_groupuids($GroupUIDs){
		/*FOR SUPERVISOR CHECK*/
		$where = '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
		}

		$query = $this->db->query("SELECT mGroupCustomers.GroupCustomerProductUID as ProductUID,mGroupCustomers.GroupCustomerSubProductUID As SubProductUID,ProductName,SubProductName FROM mgroups LEFT JOIN mGroupCustomers ON mGroupCustomers.GroupUID = mgroups.GroupUID  LEFT JOIN mSubProducts on mSubProducts.SubProductUID = mGroupCustomers.GroupCustomerSubProductUID LEFT JOIN mProducts on mProducts.ProductUID = mGroupCustomers.GroupCustomerProductUID WHERE mgroups.GroupUID IN ($GroupUIDs) $where  Group by mGroupCustomers.GroupCustomerProductUID");
		return $query->result();
	}

	function get_subproducts_by_groupuids($GroupUIDs){
		/*FOR SUPERVISOR CHECK*/
		$where = '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
		}
		/*@Desc Group Setup Changed @Author Jainulabdeen @Updated Aug 8 2020*/
		/*$query = $this->db->query("SELECT mGroupCustomers.GroupCustomerProductUID as ProductUID,mGroupCustomers.GroupCustomerSubProductUID As SubProductUID,ProductName,SubProductName FROM mgroups LEFT JOIN mGroupCustomers ON mGroupCustomers.GroupUID = mgroups.GroupUID  LEFT JOIN mSubProducts on mSubProducts.SubProductUID = mGroupCustomers.GroupCustomerSubProductUID LEFT JOIN mProducts on mProducts.ProductUID = mGroupCustomers.GroupCustomerProductUID WHERE mgroups.GroupUID IN ($GroupUIDs) $where  Group by mGroupCustomers.GroupCustomerSubProductUID");
		return $query->result();*/

		$where_new = [];
		$GroupUIDArr = explode(',', $GroupUIDs);
		foreach ($GroupUIDArr as $key => $value) {
			$where_new[] = $this->get_group_whereconditions($value);
		}
		return !empty($where_new) ? '('.implode(') OR (', $where_new).')' : '';
		/*End*/

	}

	function get_subprod_bygroup_product($GroupUID,$ProductUID){
		/*FOR SUPERVISOR CHECK*/
		$where = '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
		}

		$query = $this->db->query("SELECT mGroupCustomers.GroupCustomerProductUID as ProductUID,mGroupCustomers.GroupCustomerSubProductUID as SubProductUID,ProductName,SubProductName FROM mgroups LEFT JOIN mGroupCustomers ON mGroupCustomers.GroupUID = mgroups.GroupUID  LEFT JOIN mSubProducts on mSubProducts.SubProductUID = mGroupCustomers.GroupCustomerSubProductUID LEFT JOIN mProducts on mProducts.ProductUID = mGroupCustomers.GroupCustomerProductUID WHERE mgroups.GroupUID = $GroupUID AND mGroupCustomers.GroupCustomerProductUID = $ProductUID  $where GROUP BY mSubProducts.SubProductUID");
		$SubProducts =  $query->result();



		$workflowroles = $this->common_model->getrole_workflows();
		$where = '';
		if($workflowroles != ''){
			$where .= 'AND mCustomerWorkflowModules.WorkflowModuleUID IN ('.$workflowroles.')';
		}

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
		}

		$query1 = $this->db->query("SELECT mWorkflowModules.WorkflowModuleUID,mWorkflowModules.WorkflowModuleName FROM mGroupCustomers LEFT JOIN mCustomerWorkflowModules ON mCustomerWorkflowModules.ProductUID = mGroupCustomers.GroupCustomerProductUID LEFT JOIN mWorkflowModules ON  mWorkflowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID  WHERE mGroupCustomers.GroupUID = $GroupUID AND mGroupCustomers.GroupCustomerProductUID = $ProductUID AND mGroupCustomers.GroupCustomerUID = mCustomerWorkflowModules.CustomerUID AND mWorkflowModules.WorkflowModuleUID !=4 ".$where." GROUP BY mCustomerWorkflowModules.workflowModuleUID");
		$workflows =  $query1->result();
		return array($SubProducts,$workflows);
	}


	function get_workflowbygroups($GroupUID,$ProductUID,$SubProductUID){

		$workflowroles = $this->common_model->getrole_workflows();

		$where = '';

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
		}

		if($workflowroles != ''){
			$where .= ' AND mCustomerWorkflowModules.WorkflowModuleUID IN ('.$workflowroles.')';

      $query = $this->db->query("SELECT mWorkflowModules.WorkflowModuleUID,WorkflowModuleName FROM mGroupCustomers LEFT JOIN mCustomerWorkflowModules ON mCustomerWorkflowModules.CustomerUID = mGroupCustomers.GroupCustomerUID LEFT JOIN mProducts on mProducts.ProductUID = mGroupCustomers.GroupCustomerProductUID LEFT JOIN mSubProducts on mSubProducts.SubProductUID = mGroupCustomers.GroupCustomerSubProductUID JOIN mWorkflowModules ON mWorkflowModules.WorkflowModuleUID = mCustomerWorkflowModules.WorkflowModuleUID WHERE mCustomerWorkflowModules.ProductUID = $ProductUID AND mCustomerWorkflowModules.SubProductUID = $SubProductUID AND mCustomerWorkflowModules.WorkflowModuleUID !=4 AND mGroupCustomers.GroupUID = $GroupUID ".$where." GROUP BY mCustomerWorkflowModules.WorkflowModuleUID");
		    return $query->result();
    }

		return array();

	}

	function get_Workflowassignedseperation($OrderUID){

		$loggedid = $this->session->userdata('UserUID');
		$this->db->select ( 'tOrderAssignment.WorkflowModuleUID,WorkflowModuleName,WorkflowStatus' );
		$this->db->from ( 'tOrderAssignment' );
		$this->db->join ( 'mWorkflowModules', 'mWorkflowModules.WorkflowModuleUID = tOrderAssignment.WorkflowModuleUID' , 'inner' );
		$this->db->where('tOrderAssignment.AssignedToUserUID',$loggedid);
		$this->db->where('tOrderAssignment.OrderUID',$OrderUID);

		$query = $this->db->get();
		$res =  $query->result();

		return $res;
	}


	function get_all_Workflows(){

		$query = $this->db->get('mWorkflowModules');
		return $query->result_array();
	}



	function get_order_assigned_users($data,$Workflows,$is_vendor_login)
	{

		$ret_data = [];

		foreach ($Workflows as $key => $Workflow) {

			$query=$this->db->query("SELECT OrderUID,WorkflowModuleUID,LoginID,SendToVendor,tOrderAssignment.VendorUID as  AssignedVendorUID,VendorName,mUsers.VendorUID as VendorUID FROM `tOrderAssignment` LEFT JOIN mUsers on mUsers.UserUID = tOrderAssignment.AssignedToUserUID LEFT JOIN mvendors ON mvendors.VendorUID = tOrderAssignment.VendorUID WHERE `OrderUID` = '".$data['OrderUID']."' AND (AssignedToUserUID IS NOT NULL OR SendToVendor = '1') AND `WorkflowModuleUID` = '".$Workflow['WorkflowModuleUID']."' ");
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

	function export_option($loggedid)
	{
		if($this->common_model->GetMyOrdersQueue() == 1)
		{
			$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'].','.$this->config->item('keywords')['Reopened Order'];

			$sql = "SELECT `CustomerNumber`,`CustomerName`, `OrderNumber`, `AltORderNumber`, `StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `tOrders`.`OrderEntryDatetime` as OrderEntryDatetime, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(tOrderAssignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime , tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,VendorAssignedDateTime,mProjects.ProjectName,tOrders.LoanNumber,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`) LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` LEFT JOIN `mProjects` ON `mProjects`.`ProjectUID` = `tOrders`.`ProjectUID` WHEREmCustomersme`,DATE_FORMAT(tOrderAssignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime, tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,VendorAssignedDateTime,mProjects.ProjectName,tOrders.LoanNumber,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`)  LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` LEFT JOIN `mProjects` ON `mProjects`.`ProjectUID` = `tOrders`.`ProjectUID`  WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.") $like GROUP BY `OrderUID` ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC"; 

		}

		$query = $this->db->query($sql);
		return $query->result_array();
	}


	function getexcel_myorders_by_cust_id($loggedid = '',$CustomerUID)
	{

		$status[0] = $this->config->item('keywords')['Cancelled'];
		$status[1] = $this->config->item('keywords')['Order Completed'];
		$status[2] = $this->config->item('keywords')['Exception Raised'];


		$this->db->select ( 'CustomerName,tOrders.OrderUID,OrderNumber, AltORderNumber,StatusName,tOrders.StatusUID,StatusColor,mProducts.ProductName,mProducts.ProductCode,mSubProducts.SubProductCode,mSubProducts.SubProductName,PropertyAddress1,PropertyZipcode,LoanNumber,tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,mProjects.ProjectName,tOrders.LoanNumber' );
		$this->db->select('DATE_FORMAT(tOrders.OrderDueDatetime, "%m-%d-%Y %H:%i:%s") as OrderDueDatetime', FALSE);    
		$this->db->select('DATE_FORMAT(OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);
		$this->db->select('DATE_FORMAT(OrderCompleteDateTime, "%m-%d-%Y %H:%i:%s") as OrderCompleteDateTime', FALSE);
		$this->db->select('tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime', FALSE);
		$this->db->from ( 'tOrders' );
		$this->db->join ( 'tOrderAssignment', 'tOrderAssignment.OrderUID = tOrders.OrderUID','left');
		$this->db->join ( 'mWorkflowModules', 'mWorkflowModules.WorkflowModuleUID = tOrderAssignment.WorkflowModuleUID','left');
		$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = tOrders.CustomerUID','left');
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID','left');
		$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = tOrders.SubProductUID','left');
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mSubProducts.ProductUID','left');
		$this->db->join ( 'mProjects', 'mProjects.ProjectUID = tOrders.ProjectUID','left');
		$this->db->where_not_in('tOrders.StatusUID', $status);
		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		$this->db->group_by('tOrders.OrderUID');
		$this->db->order_by('OrderUID,OrderNumber', 'DESC');
		$query = $this->db->get();
		return $query->result_array();


	}

	/*for vendors*/

	function get_vendors($logged_details,$loggedid = ''){


		if($loggedid && isset($logged_details->VendorUID)){


			if($logged_details->VendorUID != ''){

				$this->db->select ( 'VendorUID,VendorName' ); 
				$this->db->from ( 'mvendors');
				$this->db->where(array('VendorUID'=>$logged_details->VendorUID,'Active'=>1));
				$query = $this->db->get();
				return $query->result();
			}else{
				return (object)[];
			}
		}else{
			$this->db->select ( 'VendorUID,VendorName' ); 
			$this->db->from ( 'mvendors');
			$this->db->where('Active',1);
			$query = $this->db->get();
			return $query->result();
		}

		return (object)[];

	}


	function get_vendor_uids($vendors){

		$VendorUIDS = [];
		foreach ($vendors as $key => $vendor) {
			$VendorUIDS[] = $vendor->VendorUID;
		}
		$VendorUIDS = implode(',', $VendorUIDS);
		return $VendorUIDS;
	}



	function get_vendor_groups($is_vendor_login,$VendorUIDS,$loggedid){

		if($is_vendor_login){


			if($VendorUIDS != ''){
				if (in_array($this->session->userdata('RoleType'),array('13'))) {
					/*vendor Supervisor*/


					$query  = $this->db->query("SELECT mGroupVendors.GroupUID,GroupName FROM mGroupVendors LEFT JOIN mgroups ON mgroups.GroupUID =  mGroupVendors.GroupUID WHERE GroupType = 'V' AND mgroups.Active = 1 AND VendorUID IN (".$VendorUIDS.")   ");

					return $query->result();
				}elseif(in_array($this->session->userdata('RoleType'),array('14'))){
					/*Vendor Agent */

					$query  =$this->db->query("SELECT DISTINCT `mgroups`.`GroupUID`, `mgroups`.`GroupName` FROM (`mgroupusers`) JOIN `mUsers` ON `mUsers`.`UserUID` = `mgroupusers`.`GroupUserUID` LEFT JOIN `mgroups` ON `mgroupusers`.`GroupUID` = `mgroups`.`GroupUID` WHERE `mgroupusers`.`GroupUserUID` = '".$loggedid."' AND `mgroups`.`Active` = 1 AND GroupType = 'V' GROUP BY `mgroupusers`.`GroupUID` ORDER BY `mgroups`.`GroupName` ");

					return $query->result();

				}
			}
			return (object)[];

		}else{


			$query = $this->db->query("SELECT GroupUID,GroupName FROM mgroups WHERE GroupType = 'V' AND mgroups.Active = 1 ");

			return $query->result();
		}

		return (object)[];


	}


	function get_vendor_users($is_vendor_login,$loggedid,$GroupUID,$vendors){
		if($is_vendor_login){

			if(count($vendors) > 0){
				$query = $this->db->query("SELECT * FROM mUsers JOIN mGroupVendors ON mGroupVendors.VendorUID = mUsers.VendorUID LEFT JOIN mRoles on mRoles.RoleUID = mUsers.RoleUID WHERE mUsers.Active = 1 AND mGroupVendors.VendorUID = '".$vendors[0]->VendorUID."' GROUP BY mUsers.UserUID");

				return  $query->result();
			}else{
				return (object)[];
			}
		}else{
			$query = $this->db->query("SELECT mGroupVendors.VendorUID,VendorName,OrderSearch,OrderTyping,OrderTaxCert,OrderReview FROM mGroupVendors LEFT JOIN mvendors on mvendors.VendorUID = mGroupVendors.VendorUID LEFT JOIN mUsers ON mUsers.VendorUID = mGroupVendors.VendorUID LEFT JOIN mRoles ON mRoles.RoleUID = mUsers.RoleUID  WHERE mGroupVendors.GroupUID = '".$GroupUID."' GROUP BY mvendors.VendorUID ");
			return  $query->result();
		}
	}


	function get_customer_ingroup_by_vendors($groupids)
	{

		if(count($groupids) > 0){
			$this->db->distinct();
			$this->db->select ( 'CustomerName,CustomerUID' ); 
			$this->db->from ( 'mGroupCustomers' );
			$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = mGroupCustomers.GroupCustomerUID');
			$this->db->where_in ('mGroupCustomers.GroupUID',$groupids);
			$this->db->group_by('mGroupCustomers.GroupCustomerUID');
			$this->db->order_by('mCustomers.CustomerName');
			$query = $this->db->get();
			return $query->result();
		}else{
			return (object)[];
		}
	}

	function get_customeruid_format($CustomerUIDs){

		$cus_id = [];
		if(count($CustomerUIDs) > 0 ){
			foreach ($CustomerUIDs as $key => $CustomerUID) {
				$cus_id[] = $CustomerUID->CustomerUID;
			}
		}
		return $cus_id;
	}



	function get_vendor_getnextorder($loggedid,$postarray,$customer_uids,$VendorUID){
		$filter_workflow = $postarray['filter_workflow'];
		$GroupUID = $postarray['GroupUID'];
		$SubProductUID = $postarray['SubProductUID'];

		$subproduct_where  = '';
		if($SubProductUID != ''){
			$subproduct_where  .= "AND mGroupCustomers.GroupCustomerSubProductUID = '".$SubProductUID."' ";
		}else{
			$customer_ids = $this->get_customeringroup($GroupUID);
			$cus_subproducts = [];
			if(count($customer_ids) > 0){

				$cus_subproducts = $this->get_customerproductandsubproduct($customer_ids);
			}
			

			if(count($cus_subproducts) > 0){
				if($cus_subproducts->SubProductUIDs !='' ){
					$subproduct_where .= 'AND mGroupCustomers.GroupCustomerSubProductUID IN ('.$cus_subproducts->SubProductUIDs.')';
				}
			}
		}

		$check_workflow_permissions = $this->check_workflow_permissions($filter_workflow);
		$where  = '';
		if(count($check_workflow_permissions) > 0){

			if($check_workflow_permissions->DependentWorkflowModule != ''){

				$where .= "AND `tOrders`.`OrderUID` IN (SELECT `OrderUID` FROM `tOrderAssignment` WHERE  `WorkflowModuleUID`  = $check_workflow_permissions->DependentWorkflowModule AND WorkflowStatus  = 5)";
			}
		}

		$this->db->select('GROUP_CONCAT(DISTINCT `OrderUID`) as  OrderUID '); 
		$this->db->where('torderunassignment.AssignedToUserUID',$this->session->userdata('UserUID'));
		$q = $this->db->get('torderunassignment');
		$unassigned = $q->row();

		if ( $unassigned->OrderUID != '' ) 
		{
			$where .= "AND tOrders.OrderUID NOT IN ($unassigned->OrderUID)";
		}


		$customer_uids = implode(',', $customer_uids);
		if($customer_uids != '')
		{
			$customer_id = $customer_uids;
			$status1 = $this->config->item('keywords')['New Order'];
			$status2 = $this->config->item('keywords')['Order Assigned'];
			$status3 = $this->config->item('keywords')['Order Work In Progress'];
			$status4 = $this->config->item('keywords')['Partial Draft Complete'];
			$status5 = $this->config->item('keywords')['Partial Review Complete'];
			$status6 = $this->config->item('keywords')['Review In Progress'];

			/*--- INHOUSE ORDERS FOR SEARCH ----*/
			if($filter_workflow == '1' ){
				$where .= "AND tOrders.IsInhouseExternal = '0' ";
			}


			$query = $this->db->query("SELECT `tOrders`.`OrderUID`, `OrderNumber`, `tOrders`.`CustomerUID`, `CustomerName`, `OrderTypeName`, `PriorityName`, `tOrders`.`SubProductUID`, `mSubProducts`.`SubProductName`, `mProducts`.`ProductUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mOrderStatus`.`StatusName`, DATE_FORMAT(OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime, DATE_FORMAT(`tOrders`.`OrderDueDatetime`, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime , tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`)  JOIN `mCustomers` ON `tOrders`.`CustomerUID` = `mCustomers`.`CustomerUID` LEFT JOIN `mOrderTypes` ON `mOrderTypes`.`OrderTypeUID` = `tOrders`.`OrderTypeUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `tOrderAssignment` ON  `tOrderAssignment`.`OrderUID` = `tOrders`.`OrderUID` LEFT JOIN `mGroupCustomers` ON  `mGroupCustomers`.`GroupCustomerSubProductUID` = `tOrders`.`SubProductUID` WHERE  `tOrders`.`CustomerUID` IN ($customer_id) AND `tOrders`.`OrderUID` NOT IN (SELECT `OrderUID` FROM `tOrderAssignment` WHERE  `WorkflowModuleUID` ='".$filter_workflow."' AND AssignedToUserUID IS NOT NULL) AND `tOrders`.`StatusUID` IN ('".$status1."', '".$status2."', '".$status3."','".$status4."','".$status5."','".$status6."') ".$where." AND tOrders.OrderUID NOT IN (SELECT tOrderAbstractor.OrderUID FROM tOrderAbstractor LEFT JOIN tOrderAssignment ON tOrderAssignment.OrderUID = tOrderAbstractor.OrderUID WHERE DocumentReceived = 0 AND (tOrderAssignment.WorkflowModuleUID ='".$filter_workflow."' AND tOrderAssignment.WorkflowStatus != 5)) AND tOrders.OrderUID IN (SELECT OrderUID FROM tOrderAssignment WHERE WorkflowModuleUID = '".$filter_workflow."' AND SendToVendor = '1' AND VendorUID =  '".$VendorUID."' AND OrderFlag <>2) AND tOrders.SubProductUID = mGroupCustomers.GroupCustomerSubProductUID AND mGroupCustomers.GroupUID = '".$GroupUID."'  ".$subproduct_where." ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC");

			$Orders =  $query->result();

			if(count($Orders)>0){

				return array("message"=>"Order Assigned","status"=>"success","data"=>$Orders[0],'Error'=>'0');

			}else{

				return array("message"=>"No Orders","status"=>"danger","data"=>"",'Error'=>'1');

			}


		}else{
			return array("message"=>"No customer in group","status"=>"danger","data"=>"",'Error'=>'1');

		}
	}


	function get_vendor_orders($loggedid,$post,$VendorUID)
	{

		$limit = '';
		if ($post['length']!='') {
			// $limit = 'LIMIT '.$post['start'].','.$post['length'];
		} else {
			$limit = '';
		}

		if(!empty($post['search_value'])) 
		{
			$like = "AND ";
			foreach ($post['column_search'] as $key => $item) { 
						// if datatable send POST for search

				if ($key === 0) { // first loop
					if ($item=='OrderEntryDatetime' || $item=='OrderDueDatetime') {
						$like .= "( date(".$item.") = '".date('Y-m-d', strtotime($post['search_value']))."' ";
					}
					else{
						$like .= "( ".$item." LIKE '%".$post['search_value']."%' ";
					}
				} else {
					if ($item=='OrderEntryDatetime' || $item=='OrderDueDatetime') {
						$like .= "OR date(".$item.") = '".date('Y-m-d', strtotime($post['search_value']))."' ";
					}
					else{
						$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";
					}
				}
			}
			$like .= ") ";
		} else {
			$like = "";
		}
		if($this->common_model->GetMyOrdersQueue() == 1)
		{

			$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'];

			$sql = "SELECT `CustomerNumber`,`CustomerName`, `OrderNumber`, `AltORderNumber`, `StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(tOrderAssignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime , tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,VendorAssignedDateTime,PropertyZipcode,TRIM(CONCAT_WS(' ',TRIM(tOrders.PropertyAddress1),TRIM(tOrders.PropertyAddress2))) AS whole_name,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`) LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `tOrderPropertyRoles` ON `tOrders`.`OrderUID` = `tOrderPropertyRoles`.`OrderUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` IN (".$statuses.") AND `tOrderAssignment`.`AssignedToUserUID` = ".$loggedid." AND tOrderAssignment.WorkflowModuleUID !=4 AND tOrderAssignment.SendToVendor = '1' AND tOrderAssignment.VendorUID  = '".$VendorUID."' AND tOrders.OrderUID IN (select OrderUID from tOrderAssignment where AssignedToUserUID=$loggedid and SendToVendor='1' and (QCCompletedDateTime='0000-00-00 00:00:00' OR QCCompletedDateTime IS NULL) AND OrderFlag <>2) $like GROUP BY `OrderUID` ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC ";
		}
		else{

			$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].
			','.$this->config->item('keywords')['Cancelled'];

			$sql = "SELECT `CustomerNumber`,`CustomerName`, `OrderNumber`,`AltORderNumber`,`StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(tOrderAssignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime, tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,VendorAssignedDateTime,PropertyZipcode,TRIM(CONCAT_WS(' ',TRIM(tOrders.PropertyAddress1),TRIM(tOrders.PropertyAddress2))) AS whole_name,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`)  LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `tOrderPropertyRoles` ON `tOrders`.`OrderUID` = `tOrderPropertyRoles`.`OrderUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.") AND tOrderAssignment.SendToVendor = '1' AND tOrderAssignment.VendorUID  = '".$VendorUID."'  AND tOrders.OrderUID IN (select OrderUID from tOrderAssignment where SendToVendor='1' and (QCCompletedDateTime='0000-00-00 00:00:00' OR QCCompletedDateTime IS NULL) AND OrderFlag <>2)  $like  GROUP BY `OrderUID` ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC"; 

		}

		$query = $this->db->query($sql);
		return $query->result_array();
	}
	function get_vendor_countfiltered_orders($loggedid,$post,$VendorUID,$Workflows,$is_vendor_login)
	{

		$limit = '';
		if ($post['length']!='') {
			// $limit = 'LIMIT '.$post['start'].','.$post['length'];
		} else {
			$limit = '';
		}

		if(!empty($post['search_value'])) 
		{
			$like = "AND ";
			foreach ($post['column_search'] as $key => $item) { 
						// if datatable send POST for search
				if ($key === 0) { // first loop
					$like .= "( ".$item." LIKE '%".$post['search_value']."%' "; 
				} else {
					$like .= " OR ".$item." LIKE '%".$post['search_value']."%' ";    
				}
			}
			$like .= ") ";
		} else {
			$like = "";
		} 
		if($this->common_model->GetMyOrdersQueue() == 1)
		{

			$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'];

			$sql = "SELECT `CustomerName`, `OrderNumber`, `StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(tOrderAssignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime , tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`) LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `tOrderPropertyRoles` ON `tOrders`.`OrderUID` = `tOrderPropertyRoles`.`OrderUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` IN (".$statuses.") AND `tOrderAssignment`.`AssignedToUserUID` = ".$loggedid." AND tOrderAssignment.WorkflowModuleUID !=4 AND tOrderAssignment.SendToVendor = '1' AND tOrderAssignment.VendorUID  = '".$VendorUID."'  AND tOrders.OrderUID IN (select OrderUID from tOrderAssignment where AssignedToUserUID=$loggedid and SendToVendor='1' and (QCCompletedDateTime='0000-00-00 00:00:00' OR QCCompletedDateTime IS NULL)  AND OrderFlag <>2)  $like GROUP BY `OrderUID` ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC ";
		}
		else{

			$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].
			','.$this->config->item('keywords')['Cancelled'];

			$sql = "SELECT `CustomerName`, `OrderNumber`, `StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(tOrderAssignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime, tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`)  LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `tOrderPropertyRoles` ON `tOrders`.`OrderUID` = `tOrderPropertyRoles`.`OrderUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.") AND tOrderAssignment.SendToVendor = '1' AND tOrderAssignment.VendorUID  = '".$VendorUID."'  AND tOrders.OrderUID IN (select OrderUID from tOrderAssignment where SendToVendor='1' and (QCCompletedDateTime='0000-00-00 00:00:00' OR QCCompletedDateTime IS NULL) AND OrderFlag <>2)  $like  GROUP BY `OrderUID` ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC"; 

		}

		$query = $this->db->query($sql);
		// return $query->result_array();

		$checkorders = $this->db->query($sql)->result_array(); 

		foreach ($checkorders as $key => $value) 
		{
			if(in_array($this->session->userdata('RoleType'),array(1,2,3,4,5,6,13)) == False)
			{
				$assigned = $this->common_model->get_assigned_workflows($value['OrderUID'],$this->loggedid);
				$completed = $this->common_model->get_completed_workflows($value['OrderUID'],$this->loggedid);
				$assigned_orderss = [];
				$completed_orderss = [];
				$assigned_workflows = [];
				$completed_workflows = [];
				foreach ($assigned as $keys => $values) {
					$assigned_orderss[] = $values['OrderUID'];
					$assigned_workflows[] = $values['WorkflowModuleUID'];
				}
				foreach ($completed as $keyss => $valuess) {
					$completed_orderss[] = $valuess['OrderUID'];
					$completed_workflows[] = $valuess['WorkflowModuleUID'];
				}
				if($assigned_orderss === array_intersect($assigned_orderss, $completed_orderss) && $completed_orderss === array_intersect($completed_orderss, $assigned_orderss)) {

					if($assigned_workflows === array_intersect($assigned_workflows, $completed_workflows) && $completed_workflows === array_intersect($completed_workflows, $assigned_workflows)) {
						unset($checkorders[$key]);
					} 
				}   
			}
		}

		if(count($checkorders) > 0){
			$my_orders = $checkorders; 
		} else {
			$my_orders = array();
		}

		return sizeof($my_orders);
	}
	function get_vendor_countall_orders($loggedid,$post,$VendorUID,$Workflows,$is_vendorlogin)
	{

		if($this->common_model->GetMyOrdersQueue() == 1)
		{

			$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'];

			$sql = "SELECT `CustomerNumber`,`CustomerName`, `OrderNumber`, `StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(tOrderAssignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime , tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,VendorAssignedDateTime,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`) LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `tOrderPropertyRoles` ON `tOrders`.`OrderUID` = `tOrderPropertyRoles`.`OrderUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` IN (".$statuses.") AND `tOrderAssignment`.`AssignedToUserUID` = ".$loggedid." AND tOrderAssignment.WorkflowModuleUID !=4 AND tOrderAssignment.SendToVendor = '1' AND tOrderAssignment.VendorUID  = '".$VendorUID."' AND tOrders.OrderUID IN (select OrderUID from tOrderAssignment where AssignedToUserUID=$loggedid and SendToVendor='1' and (QCCompletedDateTime='0000-00-00 00:00:00' OR QCCompletedDateTime IS NULL) AND OrderFlag <>2) GROUP BY `OrderUID` ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC ";
		}
		else{

			$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].
			','.$this->config->item('keywords')['Cancelled'];

			$sql = "SELECT `CustomerNumber`,`CustomerName`, `OrderNumber`, `StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(tOrderAssignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime, tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,VendorAssignedDateTime,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`)  LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `tOrderPropertyRoles` ON `tOrders`.`OrderUID` = `tOrderPropertyRoles`.`OrderUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.") AND tOrderAssignment.SendToVendor = '1' AND tOrderAssignment.VendorUID  = '".$VendorUID."'  AND tOrders.OrderUID IN (select OrderUID from tOrderAssignment where SendToVendor='1' and (QCCompletedDateTime='0000-00-00 00:00:00' OR QCCompletedDateTime IS NULL) AND OrderFlag <>2)  GROUP BY `OrderUID` ORDER BY FIELD(`tOrders`.`PriorityUID`,3,1) DESC, `tOrders`.`OrderEntryDatetime` ASC"; 

		}

		$query = $this->db->query($sql);
		// return $query->result_array();

		$checkorders = $this->db->query($sql)->result_array(); 

		foreach ($checkorders as $key => $value) 
		{

			if(in_array($this->session->userdata('RoleType'),array(1,2,3,4,5,6,13)) == False)
			{
				$assigned = $this->common_model->get_assigned_workflows($value['OrderUID'],$this->loggedid);
				$completed = $this->common_model->get_completed_workflows($value['OrderUID'],$this->loggedid);
				$assigned_orderss = [];
				$completed_orderss = [];
				$assigned_workflows = [];
				$completed_workflows = [];
				foreach ($assigned as $keys => $values) {
					$assigned_orderss[] = $values['OrderUID'];
					$assigned_workflows[] = $values['WorkflowModuleUID'];
				}
				foreach ($completed as $keyss => $valuess) {
					$completed_orderss[] = $valuess['OrderUID'];
					$completed_workflows[] = $valuess['WorkflowModuleUID'];
				}
				if($assigned_orderss === array_intersect($assigned_orderss, $completed_orderss) && $completed_orderss === array_intersect($completed_orderss, $assigned_orderss)) {

					if($assigned_workflows === array_intersect($assigned_workflows, $completed_workflows) && $completed_workflows === array_intersect($completed_workflows, $assigned_workflows)) {
						unset($checkorders[$key]);
					} 
				}   
			}
		}

		if(count($checkorders) > 0){
			$my_orders = $checkorders; 
		} else {
			$my_orders = array();
		}

		return sizeof($my_orders);
	}

	function vendor_lastviewed_orders($loggedid,$VendorUID)
	{

		$data = $this->db->query("SELECT `OrderUID` FROM `tlastviewedorders` WHERE UserUID = '".$loggedid."' ")->row();


		if(count($data) > 0){

			$OrderUIDs = $data->OrderUID;

			if($OrderUIDs != ''){

				$OrderUIDs =  rtrim($OrderUIDs, ',');

				if($this->common_model->GetMyOrdersQueue() == 1)
				{

					$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'];

					$where = "AND `tOrders`.`StatusUID` IN (".$statuses.") AND `tOrders`.`OrderUID` IN (".$OrderUIDs.") AND tOrderAssignment.SendToVendor = '1' AND tOrderAssignment.VendorUID  = '".$VendorUID."'  GROUP BY `OrderUID` ORDER BY FIELD(tOrders.OrderUID,".$OrderUIDs."),FIELD(tOrders.PriorityUID,1,3,2) LIMIT 10";

				}else{

					$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].
					','.$this->config->item('keywords')['Cancelled'];

					$where = "AND `tOrders`.`StatusUID` NOT IN (".$statuses.") AND `tOrders`.`OrderUID` IN (".$OrderUIDs.") AND tOrderAssignment.SendToVendor = '1' AND tOrderAssignment.VendorUID  = '".$VendorUID."'  GROUP BY `OrderUID` ORDER BY FIELD(tOrders.OrderUID,".$OrderUIDs."),FIELD(tOrders.PriorityUID,1,3,2) LIMIT 10";

				}

				$sql = "SELECT `CustomerName`, `OrderNumber`, `StatusName`, `tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`, DATE_FORMAT(tOrders.OrderDueDatetime, '%m-%d-%Y %H:%i:%s') as OrderDueDatetime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime, tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName,tOrders.OrderDueDatetime AS Ymd_OrderDueDatetime FROM (`tOrders`) LEFT JOIN `tOrderAssignment` ON `tOrders`.`OrderUID` = `tOrderAssignment`.`OrderUID` LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE tOrders.OrderUID IN (select OrderUID from tOrderAssignment where  SendToVendor='1' and (QCCompletedDateTime='0000-00-00 00:00:00' OR QCCompletedDateTime IS NULL) AND OrderFlag <>2) ".$where." ";



				$query = $this->db->query($sql);
				return $query->result_array();

			}else{
				return array();
			}

		}else{

			return array();

		}
	}

		function get_vendor_assigned_datetime($OrderUID,$is_vendor_login,$logged_details){

		$Workflows = $this->get_vendorworkflow($logged_details->VendorUID);
		$ret_data = [];

		foreach ($Workflows as $key => $Workflow) {

			$query=$this->db->query("SELECT tOrderAssignment.VendorUID,SendToVendor,DATE_FORMAT(AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime FROM `tOrderAssignment` LEFT JOIN mvendors ON mvendors.VendorUID = tOrderAssignment.VendorUID WHERE `OrderUID` = '".$OrderUID."' AND AssignedToUserUID IS NOT NULL AND `WorkflowModuleUID` = '".$Workflow['WorkflowModuleUID']."' ");
			$result = $query->row();
			
			$ret_data = '--';
			if(count($result) > 0){

				if($is_vendor_login ){
					if($result->AssignedDatetime != '' && $result->SendToVendor == '1' && $result->VendorUID == $logged_details->VendorUID){
						$ret_data = $result->AssignedDatetime;
					}				

				}else{

					$ret_data = '--';
				}

			}
			$ret[] =  $ret_data;

		}


		return  implode(" / ",$ret);
	}

	function get_vendorworkflow($VendorUID){
		$query = $this->db->query("SELECT mWorkflowModules.WorkflowModuleUID,mWorkflowModules.WorkflowModuleName,CanIndependentWorkflowModule,DependentWorkflowModule,IsExternalAbstraction FROM mvendorsworkflowmodules JOIN mvendors ON mvendors.VendorUID = mvendorsworkflowmodules.VendorUID JOIN mWorkflowModules ON mWorkflowModules.WorkflowModuleUID = mvendorsworkflowmodules.WorkflowModuleUID WHERE mvendorsworkflowmodules.VendorUID = '".$VendorUID."' GROUP BY mWorkflowModules.WorkflowModuleUID ");
		return $result = $query->result_array();
	}


	function get_assigned_workflow_users($OrderUID,$is_vendor_login,$logged_details){


		if($is_vendor_login){

			$Workflows = $this->get_vendorworkflow($logged_details->VendorUID);

		}else{
			$Workflows = $this->common_model->get_all_Workflows();

		}
		$ret_data = [];

		foreach ($Workflows as $key => $Workflow) {

			$query=$this->db->query("SELECT RoleType,LoginID,SendToVendor,tOrderAssignment.VendorUID as  AssignedVendorUID,VendorName,mUsers.VendorUID as VendorUID  FROM `tOrderAssignment` LEFT JOIN mUsers on mUsers.UserUID  = tOrderAssignment.AssignedToUserUID LEFT JOIN mvendors ON mvendors.VendorUID = tOrderAssignment.VendorUID LEFT JOIN mRoles ON mRoles.RoleUID = mUsers.RoleUID WHERE `OrderUID` = '".$OrderUID."'  AND `WorkflowModuleUID` = '".$Workflow['WorkflowModuleUID']."' AND ( AssignedToUserUID IS NOT NULL OR SendToVendor = '1' ) ");
			$result = $query->row();
			$ret_data = '--';
			if(count($result) > 0){

				if($is_vendor_login ){
					
					$ret_data = '--';
					if($result->SendToVendor == '0'  ){
						$ret_data = '--';
					}else if( $result->AssignedVendorUID == $logged_details->VendorUID){	
						if($result->LoginID == ''){
							$ret_data = strtok($result->VendorName, ' ');
						}else if($result->RoleType == '13' || $result->RoleType == '14'){
							$ret_data = $result->LoginID;
						}else{
							$ret_data = '--';
						}
					}


				}else{	
					if($result->SendToVendor == '1' ){

						$ret_data = strtok($result->VendorName, " ");
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

	function get_vendor_ordered_datetime($OrderUID,$is_vendor_login,$logged_details){

		$Workflows = $this->get_vendorworkflow($logged_details->VendorUID);
		$ret_data = [];

		foreach ($Workflows as $key => $Workflow) {

			$query=$this->db->query("SELECT tOrderAssignment.VendorUID,SendToVendor,DATE_FORMAT(VendorAssignedDateTime, '%m-%d-%Y %H:%i:%s') as VendorAssignedDateTime FROM `tOrderAssignment` LEFT JOIN mvendors ON mvendors.VendorUID = tOrderAssignment.VendorUID WHERE `OrderUID` = '".$OrderUID."' AND  `WorkflowModuleUID` = '".$Workflow['WorkflowModuleUID']."' AND ( AssignedToUserUID IS NOT NULL OR SendToVendor = '1' )");
			$result = $query->row();
			
			$ret_data = '--';
			if(count($result) > 0){

				if($is_vendor_login ){
					if($result->VendorAssignedDateTime != '' && $result->SendToVendor == '1' && $result->VendorUID == $logged_details->VendorUID){
						$ret_data = $result->VendorAssignedDateTime;
					}				

				}else{

					$ret_data = '--';
				}

			}
			$ret[] =  $ret_data;

		}


		return  implode(" / ",$ret);
	}
	function get_vendor_due_datetime($OrderUID,$is_vendor_login,$logged_details){

		$Workflows = $this->get_vendorworkflow($logged_details->VendorUID);
		$ret_data = [];

		foreach ($Workflows as $key => $Workflow) {

			$query=$this->db->query("SELECT tOrderAssignment.VendorUID,SendToVendor,DATE_FORMAT(VendorDueDate, '%m-%d-%Y %H:%i:%s') as VendorDueDate FROM `tOrderAssignment` LEFT JOIN mvendors ON mvendors.VendorUID = tOrderAssignment.VendorUID WHERE `OrderUID` = '".$OrderUID."' AND  `WorkflowModuleUID` = '".$Workflow['WorkflowModuleUID']."' AND ( AssignedToUserUID IS NOT NULL OR SendToVendor = '1' )");
			$result = $query->row();
			
			$ret_data = '--';
			if(count($result) > 0){

				if($is_vendor_login ){
					if($result->VendorDueDate != '' && $result->SendToVendor == '1' && $result->VendorUID == $logged_details->VendorUID){
						$ret_data = $result->VendorDueDate;
					}				

				}else{

					$ret_data = '--';
				}

			}
			$ret[] =  $ret_data;

		}


		return  implode(" / ",$ret);
	}

	function get_customerproductandsubproduct($Customers){
		if($Customers->CustomerUIDs != ''){
			$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT SubProductUID) AS SubProductUIDs FROM `mCustomerProducts` WHERE CustomerUID IN ($Customers->CustomerUIDs)");
			return $query->row();
		}
		return array();
	}

	function get_customeringroup($GroupUID){
		
		$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT `mCustomers`.`CustomerUID`) AS CustomerUIDs FROM (`mGroupCustomers`) JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `mGroupCustomers`.`GroupCustomerUID` WHERE `mGroupCustomers`.`GroupUID`  =  $GroupUID  AND mCustomers.Active = 1 ORDER BY `mCustomers`.`CustomerName`  ");
		return $query->row();
	}

	function check_assigned_to_agent($OrderUID,$logged_details){
		$this->db->select ( '*' );
		$this->db->from ( 'tOrderAssignment' );
		$this->db->where('tOrderAssignment.AssignedToUserUID !=', 'NULL');
		$this->db->where('tOrderAssignment.OrderUID',$OrderUID);
		$this->db->where('tOrderAssignment.VendorUID',$logged_details->VendorUID);

		$query = $this->db->get();
		$res =  $query->num_rows();

		return $res;		
	}
	 function SearchCheckWorkflowStatus($OrderUID){

  $this->db->select('*');
  $this->db->from('tOrderAssignment');
  $this->db->where('OrderUID',$OrderUID);
  $this->db->where('WorkflowModuleUID',1);
  return $this->db->get()->row();
}

function TypingCheckWorkflowStatus($OrderUID){

  $this->db->select('*');
  $this->db->from('tOrderAssignment');
  $this->db->where('OrderUID',$OrderUID);
  $this->db->where('WorkflowModuleUID',2);
  return $this->db->get()->row();
}

function TaxingCheckWorkflowStatus($OrderUID){

  $this->db->select('*');
  $this->db->from('tOrderAssignment');
  $this->db->where('OrderUID',$OrderUID);
  $this->db->where('WorkflowModuleUID',3);
  return $this->db->get()->row();
}
function SearchStatus($OrderUID){
	$this->db->select('*');
	$this->db->from('tOrders');
	$this->db->where('OrderUID',$OrderUID);
	return $this->db->get()->row();

}

function GetPRNAME($OrderUID){
	$query=$this->db->query('SELECT GROUP_CONCAT(DISTINCT `tOrderPropertyRoles`.`PRName`) AS Borrower FROM tOrderPropertyRoles WHERE OrderUID='.$OrderUID.'');
	return $query->row();
	
}



function get_customer_workflow($CustomerUID = '', $SubProductUID = '', $Filtered_Workflow = [])
{
	$this->db->distinct();
	$this->db->select('CustomerUID,WorkflowModuleName,mCustomerWorkflowModules.workflowmoduleUID');
	$this->db->from('mCustomerWorkflowModules');
	$this->db->join('mWorkflowModules', 'mCustomerWorkflowModules.workflowmoduleUID = mWorkflowModules.WorkflowModuleUID');
	$this->db->where('mCustomerWorkflowModules.CustomerUID', $CustomerUID);
	$this->db->where('mCustomerWorkflowModules.SubProductUID', $SubProductUID);
	$this->db->where('mCustomerWorkflowModules.SubProductUID', $SubProductUID);
	if (!empty($Filtered_Workflow)) {
		$this->db->where_in('mCustomerWorkflowModules.WorkflowModuleUID', $Filtered_Workflow);
	}
	$query = $this->db->get();
	return $query->result();
}

function getassignmentbyorderworkflow($OrderUID, $WorkflowModuleUID)
{
	$this->db->select('*')->from('tOrderAssignment');
	$this->db->join('mUsers','mUsers.UserUID = tOrderAssignment.AssignedToUserUID', 'left');
	$this->db->where('tOrderAssignment.OrderUID', $OrderUID);
	$this->db->where('tOrderAssignment.WorkflowModuleUID', $WorkflowModuleUID);
	$this->db->where('(tOrderAssignment.AssignedToUserUID IS NOT NULL OR tOrderAssignment.AssignedToUserUID != 0)', NULL, false);
	return $this->db->get()->row();
}

function selfassign_order($tOrderAssignment, $OrderUID, $workflowuid)
{
	$this->db->where('OrderUID', $OrderUID);
	$this->db->where('WorkflowModuleUID', $workflowuid);
	$is_assigned = $this->db->get('tOrderAssignment')->row();

	$this->db->trans_begin();
	if (empty($is_assigned)) {
		$this->db->insert('tOrderAssignment', $tOrderAssignment);
	}
	else{

		if (empty($is_assigned->AssignedToUserUID)) {
			$this->db->where('OrderUID', $OrderUID);
			$this->db->where('WorkflowModuleUID', $workflowuid);
			$this->db->update('tOrderAssignment', $tOrderAssignment);			
		}
		else{
			$this->db->trans_rollback();
			return false;
		}
	}

	if ($this->db->trans_status()===false) {
		$this->db->trans_rollback();
		return false;
	}
	else{
		$this->db->trans_commit();
		return true;
	}
}

function GetProducts()
{
	$q = $this->db->get_where('mProducts',array('Active'=>1));
	return $q->result();
}


  function GetCustomers($login_id)
  {
    if(in_array($this->session->userdata('RoleType'),array(7,9))) 
    {
      $this->db->select('mCustomers.CustomerUID,mCustomers.CustomerName,mCustomers.CustomerNumber');
      $this->db->from('mgroupusers');
      $this->db->join ('mGroupCustomers','mGroupCustomers.GroupUID = mgroupusers.GroupUID','left');
      $this->db->join ('mCustomers','mCustomers.CustomerUID = mGroupCustomers.GroupCustomerUID','left');
      $this->db->where('GroupUserUID = '.$login_id.' AND mCustomers.Active = 1');
      $this->db->group_by('mCustomers.CustomerUID');
      return $this->db->get()->result();
    } 
    else if(in_array($this->session->userdata('RoleType'),array(8)))
    {
      $this->db->select('mCustomers.CustomerUID,mCustomers.CustomerName,mCustomers.CustomerNumber');
      $this->db->from('mCustomers');
      $this->db->join ('mUsers','mUsers.CustomerUID = mCustomers.CustomerUID','left');
      $this->db->where('mUsers.UserUID',$login_id);
      $this->db->group_by('mCustomers.CustomerUID');
      return $this->db->get()->result();
    } else {
      $this->db->where('Active',1);
      return $this->db->get('mCustomers')->result();
    }

  }

  /*@Desc Group Where Conditions @Author Jainulabdeeb @On Aug 8 2020*/
	function get_group_whereconditions($post)
	{
		$where = [];

			$this->db->select('*');
			$this->db->from('mGroupCustomers');
			$this->db->where('mGroupCustomers.GroupUID',$post);
			$groupparams  = $this->db->get()->result_array();
			if(!empty($groupparams)) {
				foreach ($groupparams as $groupparamvalue) {
					$data = [];
					$data['filter_workflow'] = $groupparamvalue['GroupWorkflowModuleUID'];
					$data['GroupUID'] = $groupparamvalue['GroupUID'];
					$data['CustomerUID'] = $groupparamvalue['GroupCustomerUID'];
					$data['ProductUID'] = $groupparamvalue['GroupCustomerProductUID']; 
					$data['SubProductUID'] = $groupparamvalue['GroupCustomerSubProductUID']; 
					$data['StateUID'] = $groupparamvalue['GroupStateUID']; 
					$data['CountyUID'] = $groupparamvalue['GroupCountyUID']; 
					$data['CityUID'] = $groupparamvalue['GroupCityUID']; 
					$data['ZipCode'] = $groupparamvalue['GroupZipCode']; 
					$where[] = $this->group_whereconditions($data);
				}
			}
		
  	return !empty($where) ? '('.implode(') OR (', $where).')' : '';
	}

	function group_whereconditions($post)
	{
		$where = [];
		if(isset($post['CustomerUID']) && !empty($post['CustomerUID'])) {
			$where[] = '(tOrders.CustomerUID = '.$post['CustomerUID'].')';
		}

		if(isset($post['ProductUID']) && !empty($post['ProductUID'])) {
			$where[] = '(mSubProducts.ProductUID = '.$post['ProductUID'].')';
			$this->db->select('GROUP_CONCAT(GroupCustomerUID) AS GroupCustomerUID');
			$this->db->from('mGroupCustomers');
			$this->db->where('mGroupCustomers.GroupUID',$post['GroupUID']);
			$this->db->where('mGroupCustomers.GroupCustomerProductUID',$post['ProductUID']);
			$groupparams  = $this->db->get()->row();
			if(!empty($groupparams) && !empty($groupparams->GroupCustomerUID)) {
				$where[] = '(tOrders.CustomerUID IN ('.$groupparams->GroupCustomerUID.'))';
			}
		}

		if(isset($post['SubProductUID']) && !empty($post['SubProductUID'])) {
			$where[] = '(mSubProducts.SubProductUID = '.$post['SubProductUID'].')';
		}

		if(isset($post['StateUID']) && !empty($post['StateUID'])) {
			$State = $this->common_model->getStateRowbyUID($post['StateUID']);
			if(!empty($State) && !empty($State->StateCode)) {
				$where[] = '(tOrders.PropertyStateCode = "'.$State->StateCode.'")';
			}
		} 

		if(isset($post['CountyUID']) && !empty($post['CountyUID'])) {
			$County = $this->common_model->getCountyRowbyUID($post['CountyUID']);
			if(!empty($County) && !empty($County->CountyName)) {
				$where[] = '(tOrders.PropertyCountyName = "'.$County->CountyName.'")';
			}
		} 

		if(isset($post['CityUID']) && !empty($post['CityUID'])) {
			$city = $this->common_model->getCityRowbyUID($post['CityUID']);
			if(!empty($city) && !empty($city->CityName)) {
				$where[] = '(tOrders.PropertyCityName = "'.$city->CityName.'")';
			}
		} 

		if(isset($post['ZipCode']) && !empty($post['ZipCode'])) {
			$where[] = '(tOrders.PropertyZipcode = '.$post['ZipCode'].')';
		} 

		return implode(' AND  ', $where);
	}
/*End*/

function assignmentOrders(){
	$this->db->select("*");
	$this->db->from('tOrders');
	$this->db->join ('mSubProducts','mSubProducts.SubProductUID = tOrders.SubProductUID','left');
    $this->db->join ('mProducts','mProducts.ProductUID = mSubProducts.ProductUID','left');
    $this->db->join ('mOrderStatus','mOrderStatus.StatusUID = tOrders.StatusUID','left');
	$this->db->join ('mOrderPriority','mOrderPriority.PriorityUID = tOrders.PriorityUID','left');
	$ProductUID=7;
	$this->db->where('mProducts.ProductUID',$ProductUID);
	$this->db->order_by('OrderUID', 'asc');
	$query = $this->db->get();
	return $query->result();

}

}?>
