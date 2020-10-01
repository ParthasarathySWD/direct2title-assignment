<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class completed_orders_model extends CI_Model {

	
	function __construct()
	{ 
		parent::__construct();
		$this->config->load('keywords');
	}

	function get_complete_orders($user_id = '')
	{
		//$status[0] = $this->config->item('keywords')['Complete'];	
		$query=[];	
		$ReviewComplete = $this->config->item('keywords')['Review Complete'];
		$OrderComplete= $this->config->item('keywords')['Order Completed'];

		$status[0] = $this->config->item('keywords')['Review Complete'];
		$status[1] = $this->config->item('keywords')['Order Completed'];
		//$status = $this->config->item('keywords')['Order Completed'];
	
		$this->db->select ( 'CustomerName,OrderNumber,StateName,StatusName,StatusColor,torders.OrderUID,torders.PriorityUID,PriorityName' );
		$this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m-%d-%Y") as AssignedDatetime', FALSE);	 
		$this->db->from ( 'torders');
		$this->db->join ( 'mstates', 'torders.PropertyStateUID = mstates.StateUID' , 'left' );  
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );  
		$this->db->join ( 'mcustomers', 'mcustomers.CustomerUID = torders.CustomerUID' , 'left' );
		$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID' , 'left' );
		$this->db->join ('torderassignment','torderassignment.OrderUID = torders.OrderUID','left');
		$this->db->where_in('torders.StatusUID',$status); 
		if($this->common_model->GetCompletedQueue() == 1)
		{
			$this->db->where('torderassignment.AssignedToUserUID',$user_id);
		}
		$this->db->group_by('OrderUID');
		$query = $this->db->get();
		return $query->result();

		return $query;
		
		/*if($this->session->userdata('RoleUID') == 6)  
		{
		

			$group_id = $this->db->query("SELECT GROUP_CONCAT(GroupUID SEPARATOR ',') as group_id FROM mgroupusers where GroupUserUID = $user_id")->row();
			$gids= $group_id->group_id;  

			$cust_id = $this->db->query("SELECT GROUP_CONCAT(GroupCustomerUID SEPARATOR ',') as cust_id FROM mgroupcustomers where GroupUID IN ($gids)")->row();
			$customer_id = $cust_id->cust_id; 

			$query = $this->db->query("SELECT CustomerName, OrderNumber, StateName, StatusName, torderassignment.OrderUID, DATE_FORMAT(torderassignment.AssignedDatetime, '%m-%d-%Y') as AssignedDatetime FROM (torderassignment) LEFT JOIN torders ON torderassignment.OrderUID = torders.OrderUID LEFT JOIN mstates ON torders.PropertyStateUID = mstates.StateUID LEFT JOIN musers ON torderassignment.AssignedToUserUID = musers.UserUID LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID LEFT JOIN mcustomers ON mcustomers.CustomerUID = torders.CustomerUID LEFT JOIN morderstatus ON morderstatus.StatusUID = torders.StatusUID WHERE  torders.CustomerUID IN (".$customer_id.") AND torders.StatusUID = ".$ReviewComplete." GROUP BY OrderUID, AssignedToUserUID"); 
			return $query->result();
		}

		if($this->session->userdata('RoleUID') == 7)  
		{
			$this->db->select ( 'CustomerName,OrderNumber,StateName,StatusName,torderassignment.OrderUID' );
			$this->db->select('DATE_FORMAT(torderassignment.AssignedDatetime, "%m-%d-%Y") as AssignedDatetime', FALSE);	 
			$this->db->from ( 'torderassignment' );
			$this->db->join ( 'torders', 'torderassignment.OrderUID = torders.OrderUID' , 'left' );
			$this->db->join ( 'mstates', 'torders.PropertyStateUID = mstates.StateUID' , 'left' );
			$this->db->join ( 'musers', 'torderassignment.AssignedToUserUID = musers.UserUID' , 'left' );
			$this->db->join ( 'mworkflowmodules', 'mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID' , 'left' );
			$this->db->join ( 'mcustomers', 'mcustomers.CustomerUID = torders.CustomerUID' , 'left' );
			$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID' , 'left' );
			$this->db->where_in('torders.StatusUID',$status); 
			$this->db->where('torderassignment.AssignedToUserUID',$user_id);
			$this->db->group_by('OrderUID,AssignedToUserUID');
			$query = $this->db->get();
			return $query->result();
		}
		return $query;*/
	}

	function ChangeOrderStatus($OrderUID){

		$OrderExported = $this->config->item('keywords')['Order Exported'];
		$OrderCompleted = $this->config->item('keywords')['Order Completed'];

		$status = array("StatusUID"=>$OrderCompleted);

		$this->db->where(array("torders.OrderUID"=>$OrderUID));
        $this->db->update('torders',$status);

	}


		function  CompleteOrders(){
		$status[0] = $this->config->item('keywords')['Review Complete'];
		$status[1] = $this->config->item('keywords')['Order Completed'];
		$this->db->select ( 'CustomerName,OrderNumber,LoanNumber,StateName,StatusName,PropertyAddress1,PropertyStateCode,PropertyCityName,PropertyCountyName,PropertyZipcode,OrderEntryDatetime,OrderDueDatetime,OrderCompleteDateTime,ProductName,SubProductName,StatusColor,AttentionName,torders.OrderUID,torders.PriorityUID,PriorityName,mordertypes.OrderTypeName' );
		// $this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m-%d-%Y") as AssignedDatetime', FALSE);	 
		$this->db->from ( 'torders');
		$this->db->join ( 'mstates', 'torders.PropertyStateUID = mstates.StateUID' , 'left' ); 
		$this->db->join ('msubproducts', 'msubproducts.SubProductUID = torders.SubProductUID' , 'left' );
		$this->db->join ('mproducts', 'mproducts.ProductUID = msubproducts.ProductUID' , 'left' );    
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );  
		$this->db->join ( 'mcustomers', 'mcustomers.CustomerUID = torders.CustomerUID' , 'left' );
		$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID' , 'left' );
		$this->db->join ('mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
		$this->db->join ('torderassignment','torderassignment.OrderUID = torders.OrderUID','left');
		$this->db->where_in('torders.StatusUID',$status ); 

		$this->db->group_by('OrderUID');
		// $this->db->limit(10);
		// if ($post['length']!='') {
		// $this->db->limit($post['length'], $post['start']);
		// }
		$query = $this->db->get();
		return $query->result();

		// return $query;

	}

	function CompleteOrdersCoutall(){
		$status[0] = $this->config->item('keywords')['Review Complete'];
		$status[1] = $this->config->item('keywords')['Order Completed'];
		$this->db->select ( 'CustomerName,OrderNumber,LoanNumber,StateName,StatusName,PropertyAddress1,PropertyStateCode,PropertyCityName,PropertyCountyName,PropertyZipcode,OrderEntryDatetime,OrderDueDatetime,OrderCompleteDateTime,ProductName,SubProductName,StatusColor,AttentionName,torders.OrderUID,torders.PriorityUID,PriorityName,mordertypes.OrderTypeName' );
		$this->db->from ( 'torders');
		$this->db->join ( 'mstates', 'torders.PropertyStateUID = mstates.StateUID' , 'left' ); 
		$this->db->join ('msubproducts', 'msubproducts.SubProductUID = torders.SubProductUID' , 'left' );
		$this->db->join ('mproducts', 'mproducts.ProductUID = msubproducts.ProductUID' , 'left' );    
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );  
		$this->db->join ( 'mcustomers', 'mcustomers.CustomerUID = torders.CustomerUID' , 'left' );
		$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID' , 'left' );
		$this->db->join ('mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
		$this->db->join ('torderassignment','torderassignment.OrderUID = torders.OrderUID','left');
		$this->db->where_in('torders.StatusUID',$status ); 

		$this->db->group_by('OrderUID');	
		$query = $this->db->count_all_results();
		return $query;
	}

	function CompleteOrdersFiltered(){
	$this->CompleteOrders();   
	$query = $this->db->get();
	return $query->num_rows();
}
}
?>
