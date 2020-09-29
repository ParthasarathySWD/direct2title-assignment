<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class review_orders_model extends CI_Model {

	
	function __construct()
	{ 
		parent::__construct();
		$this->config->load('keywords');
	}


	function get_user_group_id($loggedid)
	{
		$grp_id = [];
		$this->db->select ( 'GroupUID' ); 
		$this->db->from ( 'mgroupusers' );
		$this->db->where ('mgroupusers.GroupUserUID',$loggedid);

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserGroupUID = $this->common_model->_get_groups_bylogin();
			if($UserGroupUID): $this->db->where('mgroupusers.GroupUID IN ('.$UserGroupUID.')', null, false); else: $this->db->where('mgroupusers.GroupUID IN (0)', null, false); endif;
		}

		$query = $this->db->get();
		$groupids =  $query->result_array();

		if($groupids != null){
			foreach ($groupids as $key => $groupid) {
				$grp_id[] = $groupid['GroupUID'];
			}
		}
		
		return $grp_id;
	}

	//For customer groups in which loggedin user belongs
	function get_customer_ingroup($user_in_groups = '')
	{

		$grp_id = $this->get_customer_group_id($user_in_groups);
		$this->db->distinct();
		$this->db->select ( 'CustomerName,CustomerUID' ); 
		$this->db->from ( 'mgroupcustomers' );
		$this->db->join ( 'mcustomers', 'mcustomers.CustomerUID = mgroupcustomers.GroupCustomerUID' , 'left' );
		if($grp_id != null){
			$this->db->where_in ('mgroupcustomers.GroupUID',$grp_id);
		}

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserGroupUID = $this->common_model->_get_groups_bylogin();
			if($UserGroupUID): $this->db->where('mgroupcustomers.GroupUID IN ('.$UserGroupUID.')', null, false); else: $this->db->where('mgroupcustomers.GroupUID IN (0)', null, false); endif;
		}
		$this->db->group_by('mgroupcustomers.GroupCustomerUID');
		$query = $this->db->get();
		return $query->result();
	}

	function get_customer_group_id($user_in_groups)
	{
		$this->db->select ( 'GroupUID' ); 
		$this->db->from ( 'mgroupcustomers' );
		if($user_in_groups != null){
			$this->db->where_in ('mgroupcustomers.GroupUID',$user_in_groups);
		}

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserGroupUID = $this->common_model->_get_groups_bylogin();
			if($UserGroupUID): $this->db->where('mgroupcustomers.GroupUID IN ('.$UserGroupUID.')', null, false); else: $this->db->where('mgroupcustomers.GroupUID IN (0)', null, false); endif;
		}

		$query = $this->db->get();
		$groupids =  $query->result_array();

		$grp_id = [];
		if($groupids != null){
			foreach ($groupids as $key => $groupid) {
				$grp_id[] = $groupid['GroupUID'];
			}
		}
		return $grp_id;
	}

	function get_prod_by_groupuid($GroupUID){
		/*FOR SUPERVISOR CHECK*/
		$where = '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
		}

		$query = $this->db->query("SELECT mgroupcustomers.GroupCustomerProductUID as ProductUID,mgroupcustomers.GroupCustomerSubProductUID As SubProductUID,ProductName,SubProductName FROM mgroups LEFT JOIN mgroupcustomers ON mgroupcustomers.GroupUID = mgroups.GroupUID  LEFT JOIN msubproducts on msubproducts.SubProductUID = mgroupcustomers.GroupCustomerSubProductUID LEFT JOIN mproducts on mproducts.ProductUID = mgroupcustomers.GroupCustomerProductUID WHERE mgroups.GroupUID = $GroupUID $where  Group by mgroupcustomers.GroupCustomerProductUID");
		return $query->result();
	}
	function get_subprod_bygroup_product($GroupUID,$ProductUID){
		/*FOR SUPERVISOR CHECK*/
		$where = '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
		}

		$query = $this->db->query("SELECT mgroupcustomers.GroupCustomerProductUID as ProductUID,mgroupcustomers.GroupCustomerSubProductUID as SubProductUID,ProductName,SubProductName FROM mgroups LEFT JOIN mgroupcustomers ON mgroupcustomers.GroupUID = mgroups.GroupUID  LEFT JOIN msubproducts on msubproducts.SubProductUID = mgroupcustomers.GroupCustomerSubProductUID LEFT JOIN mproducts on mproducts.ProductUID = mgroupcustomers.GroupCustomerProductUID WHERE mgroups.GroupUID = $GroupUID AND mgroupcustomers.GroupCustomerProductUID = $ProductUID  $where GROUP BY msubproducts.SubProductUID");
		$SubProducts =  $query->result();



		$workflowroles = $this->common_model->getrole_workflows();
		$where = '';
		if($workflowroles != ''){
			$where .= 'AND mcustomerworkflowmodules.WorkflowModuleUID IN ('.$workflowroles.')';
		}

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
		}

		$query1 = $this->db->query("SELECT mworkflowmodules.WorkflowModuleUID,mworkflowmodules.WorkflowModuleName FROM mgroupcustomers LEFT JOIN mcustomerworkflowmodules ON mcustomerworkflowmodules.ProductUID = mgroupcustomers.GroupCustomerProductUID LEFT JOIN mworkflowmodules ON  mworkflowmodules.WorkflowModuleUID = mcustomerworkflowmodules.WorkflowModuleUID  WHERE mgroupcustomers.GroupUID = $GroupUID AND mgroupcustomers.GroupCustomerProductUID = $ProductUID AND mgroupcustomers.GroupCustomerUID = mcustomerworkflowmodules.CustomerUID AND mworkflowmodules.WorkflowModuleUID !=4 ".$where." GROUP BY mcustomerworkflowmodules.workflowModuleUID");
		$workflows =  $query1->result();
		return array($SubProducts,$workflows);
	}

	function get_reviewedorders($post)
	{
		$status[0] = $this->config->item('keywords')['Draft Complete'];
		$status[1] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[2] = $this->config->item('keywords')['Review In Progress'];
		$status[3] = $this->config->item('keywords')['Partial Review Complete'];
		$post['loggedid'] = $loggedid;

		$is_permission = $this->common_model->role_workflows();
		

		$groupin_user_logged = $this->get_user_group_id($loggedid);
		$CustomerUIDs = $this->get_customer_ingroup($groupin_user_logged);
		$cus_id =[];
		foreach ($CustomerUIDs as $key => $CustomerUID) {
			$cus_id[] = $CustomerUID->CustomerUID;
		}

		$Workflowstatus[0] = 0;
		$Workflowstatus[1] = 3;


		$this->db->select ("torders.OrderNumber,torders.CustomerUID,mcustomers.CustomerNumber,mcustomers.CustomerName,mordertypes.OrderTypeName,morderstatus.StatusColor,morderstatus.StatusName, torders.OrderUID,morderpriority.PriorityUID,morderpriority.PriorityName,torders.PropertyStateCode, mProjects.ProjectName, torders.OrderDueDatetime");
		$this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);				 
		$this->db->from ( 'torders' );
		$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID');
		$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID','left');
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID','left');
		$this->db->join ( 'mProjects', 'mProjects.ProjectUID = torders.ProjectUID', 'left');
		$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID');

		if (!in_array($this->RoleType, [1,2,3,4,5,6])) {			
			if(!empty($cus_id)){
				$this->db->where_in ('torders.CustomerUID',$cus_id);
			}
		}

		if ($is_permission->ReviewQueue == 2){
		$this->db->select('LoginID');				 

			$this->db->join('torderassignment','torders.OrderUID = torderassignment.OrderUID','left');
      $this->db->join ( 'musers', 'musers.UserUID = torderassignment.AssignedToUserUID','left');


		}else{
		$this->db->select('LoginID');				 

			$this->db->join('torderassignment','torders.OrderUID = torderassignment.OrderUID','left');
      $this->db->join ( 'musers', 'musers.UserUID = torderassignment.AssignedToUserUID','left');

			$this->db->where("torderassignment.AssignedToUserUID = " . $this->loggedid);
			$this->db->where('torderassignment.WorkflowModuleUID',4);
		}

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserSubProducts = $this->common_model->_get_Subproducts_bylogin($this->loggedid);
			if($UserSubProducts): $this->db->where('torders.SubProductUID IN ('.$UserSubProducts.')', null, false); else: $this->db->where('torders.SubProductUID IN (0)', null, false); endif;
		}
		// print_r($post['advancedsearch']); exit();
		if (is_array($post['advancedsearch']))
        {
            // if($post['advancedsearch']['PropertyStateCode']){
            //   $this->db->where('torders.PropertyStateCode',$post['advancedsearch']['PropertyStateCode']);
            // }
            if($post['advancedsearch']['SubProductUID']){
              $this->db->where('torders.SubProductUID',$post['advancedsearch']['SubProductUID']);
            }
            if($post['advancedsearch']['CustomerUID']){
              $this->db->where('torders.CustomerUID',$post['advancedsearch']['CustomerUID']);
            }
            if($post['advancedsearch']['ProjectUID']){
              $this->db->where('torders.ProjectUID',$post['advancedsearch']['ProjectUID']);
            }
            // if($post['advancedsearch']['FromDate']){
            //   $this->db->where('DATE(torders.OrderEntryDatetime) >= "'.date('Y-m-d', strtotime($post['advancedsearch']['FromDate'])).'"', NULL, false);
            // }
            // if($post['advancedsearch']['ToDate']){
            //   $this->db->where('DATE(torders.OrderEntryDatetime) <="'.date('Y-m-d', strtotime($post['advancedsearch']['ToDate'])).'"',NULL, false);
            // }
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

		if (!empty($post['order'])) 
        { 
        // here order processing 
          if($post['column_order'][$post['order'][0]['column']]!='')
          {
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);    
          }    
        } else if (isset($this->order)) {
          $order = $this->order;
          $this->db->order_by(key($order), $order[key($order)]);  
        }


      if ($post['length']!='') {
         $this->db->limit($post['length'], $post['start']);
      }
		$this->db->where_in ('torders.StatusUID',$status);
		$this->db->group_by('torders.OrderUID');
		$query = $this->db->get();
		return $query->result();
		// echo '<pre>';print_r($query->result());exit;
	}
	function get_subproducts_by_groupuids($GroupUIDs){
		/*FOR SUPERVISOR CHECK*/
		$where = '';
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
		}

		$query = $this->db->query("SELECT mgroupcustomers.GroupCustomerProductUID as ProductUID,mgroupcustomers.GroupCustomerSubProductUID As SubProductUID,ProductName,SubProductName FROM mgroups LEFT JOIN mgroupcustomers ON mgroupcustomers.GroupUID = mgroups.GroupUID  LEFT JOIN msubproducts on msubproducts.SubProductUID = mgroupcustomers.GroupCustomerSubProductUID LEFT JOIN mproducts on mproducts.ProductUID = mgroupcustomers.GroupCustomerProductUID WHERE mgroups.GroupUID IN ($GroupUIDs) $where  Group by mgroupcustomers.GroupCustomerSubProductUID");
		return $query->result();
	}

	function filter_reviewedorders($Customer,$Projct,$SubProduct,$loggedid)
	{
	
		$status[0] = $this->config->item('keywords')['Draft Complete'];

		$status[1] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[2] = $this->config->item('keywords')['Review In Progress'];
		$status[3] = $this->config->item('keywords')['Partial Review Complete'];



		$is_permission = $this->common_model->role_workflows();
		
		/*$groupin_user_logged = $this->get_user_group_id($loggedid);
		$CustomerUIDs = $this->get_customer_ingroup($groupin_user_logged);
		$cus_id =[];
		foreach ($CustomerUIDs as $key => $CustomerUID) {
			$cus_id[] = $CustomerUID->CustomerUID;
		}*/

		$Workflowstatus[0] = 0;
		$Workflowstatus[1] = 3;


		$this->db->select ("OrderNumber,torders.CustomerUID,CustomerNumber,CustomerName,OrderTypeName,StatusColor,PriorityName,morderstatus.StatusName, torders.OrderUID,morderpriority.PriorityUID,PriorityName,PropertyStateCode, mProjects.ProjectName");
		$this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);				 
		$this->db->from ( 'torders' );
		$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID');
		$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID','left');
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID','left');
		$this->db->join ( 'mProjects', 'mProjects.ProjectUID = torders.ProjectUID', 'left');
		$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID');

		if ($is_permission->ReviewQueue == 2){

			$this->db->join('torderassignment','torders.OrderUID = torderassignment.OrderUID','left');


		}else{

			$this->db->join('torderassignment','torders.OrderUID = torderassignment.OrderUID','left');
			$this->db->where("torderassignment.AssignedToUserUID = " . $this->loggedid);
			$this->db->where('torderassignment.WorkflowModuleUID',4);
			/*if($cus_id){
				$this->db->where_in ('torders.CustomerUID',$cus_id);
			}*/
		}

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserSubProducts = $this->common_model->_get_Subproducts_bylogin($this->loggedid);
			if($UserSubProducts): $this->db->where('torders.SubProductUID IN ('.$UserSubProducts.')', null, false); else: $this->db->where('torders.SubProductUID IN (0)', null, false); endif;
		}

		if($Customer!='')
		{
			$this->db->where_in('torders.CustomerUID',$Customer);
		}
		if($Project!=''){
			$this->db->where_in('torders.ProjectUID',$Project);
		}
		
		if($SubProduct!=''){
			$this->db->where_in('torders.SubProductUID',$SubProduct);
		}
		
		$this->db->where_in ('torders.StatusUID',$status);
		$this->db->group_by('torders.OrderUID');
		$query = $this->db->get();
		return $query->result();
	}
		function reset_reviewedorders($Customer,$Project,$SubProduct,$loggedid)
	{
		$status[0] = $this->config->item('keywords')['Draft Complete'];

		$status[1] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[2] = $this->config->item('keywords')['Review In Progress'];
		$status[3] = $this->config->item('keywords')['Partial Review Complete'];



		$groupin_user_logged = $this->get_user_group_id($loggedid);
		
		$CustomerUIDs = $this->get_customer_ingroup($groupin_user_logged);

		$is_permission = $this->common_model->role_workflows();



		$cus_id =[];
		foreach ($CustomerUIDs as $key => $CustomerUID) {
			$cus_id[] = $CustomerUID->CustomerUID;
		}

		$Workflowstatus[0] = 0;
		$Workflowstatus[1] = 3;


		$this->db->select ("OrderNumber,torders.CustomerUID,CustomerNumber,CustomerName,OrderTypeName,StatusColor,PriorityName,morderstatus.StatusName, torders.OrderUID,morderpriority.PriorityUID,PriorityName,PropertyStateCode, mProjects.ProjectName");
		$this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);				 
		$this->db->from ( 'torders' );
		$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID');
		$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID','left');
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID','left');
		$this->db->join ( 'mProjects', 'mProjects.ProjectUID = torders.ProjectUID', 'left');
		$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID');

		if ($is_permission->ReviewQueue == 2){

			$this->db->join('torderassignment','torders.OrderUID = torderassignment.OrderUID','left');


		}else{

			$this->db->join('torderassignment','torders.OrderUID = torderassignment.OrderUID','left');
			$this->db->where("torderassignment.AssignedToUserUID = " . $this->loggedid);
			$this->db->where('torderassignment.WorkflowModuleUID',4);
			if($cus_id){
				$this->db->where_in ('torders.CustomerUID',$cus_id);
			}
		}

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserSubProducts = $this->common_model->_get_Subproducts_bylogin($this->loggedid);
			if($UserSubProducts): $this->db->where('torders.SubProductUID IN ('.$UserSubProducts.')', null, false); else: $this->db->where('torders.SubProductUID IN (0)', null, false); endif;
		}

		
		$this->db->where_in ('torders.StatusUID',$status);
		$this->db->group_by('torders.OrderUID');
		$query = $this->db->get();
		return $query->result();
	}

	function GetCustomerProducts($CustomerUID)
{ 

 
  if($this->session->userdata('RoleType') == 6){
    $UserProducts = $this->common_model->_get_product_bylogin();
    if($UserProducts){
      $this->db->select('mproducts.ProductUID, mproducts.ProductName,msubproducts.SubProductUID,msubproducts.SubProductName');
      $this->db->from('msubproducts');
      $this->db->join ('mcustomerproducts','mcustomerproducts.SubProductUID = msubproducts.SubProductUID','left');
      $this->db->join ('mproducts','mcustomerproducts.ProductUID = mproducts.ProductUID','left');
      if($CustomerUID!='all')
      {
        $this->db->where('CustomerUID IN ('.$CustomerUID.')');
      }
      $this->db->where('mcustomerproducts.ProductUID IN ('.$UserProducts.')');
      $this->db->group_by("mproducts.ProductUID"); 

    }
  }else{
    if($CustomerUID!='all')
    {
      $this->db->select('mproducts.ProductUID, mproducts.ProductName,msubproducts.SubProductUID,msubproducts.SubProductName');
      $this->db->from('msubproducts');
      $this->db->join ('mcustomerproducts','mcustomerproducts.SubProductUID = msubproducts.SubProductUID','left');
      $this->db->join ('mproducts','mcustomerproducts.ProductUID = mproducts.ProductUID','left');
      $this->db->where('CustomerUID IN ('.$CustomerUID.')');
      $this->db->group_by("mproducts.ProductUID");
        return $this->db->get()->result();      
    } else {
     return 0; 
    }
  }
  
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
		return 0;
	}

}

function GetsubproductCustomer($customer,$ProductUID)
{
  if($customer!='')
  {
    $this->db->select('msubproducts.SubProductUID,msubproducts.SubProductName');
    $this->db->from('mcustomerproducts');
    $this->db->join ('msubproducts','mcustomerproducts.SubProductUID = msubproducts.SubProductUID','LEFT'); 
    $this->db->where('mcustomerproducts.CustomerUID IN ('.$customer.')');
    $this->db->where('mcustomerproducts.ProductUID IN ('.$ProductUID.')');
    $this->db->where('msubproducts.Active',1);
    $this->db->group_by('msubproducts.SubProductUID');
    return $this->db->get()->result(); 
  } else {
    $this->db->select('SubProductUID, SubProductName');
    $this->db->where('ProductUID IN ('.$ProductUID.')');
    $this->db->where('Active',1);
    $this->db->group_by('SubProductUID');
    $q = $this->db->get('msubproducts');
    return $q->result();
  }
}


	function get_pending_reviewedorders($loggedid){

		$status[0] = $this->config->item('keywords')['Draft Complete'];
		$status[1] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[2] = $this->config->item('keywords')['Review In Progress'];
		$status[3] = $this->config->item('keywords')['Partial Review Complete'];
		$this->db->select ( '*' );
		$this->db->from ( 'torders' );
		$this->db->join ( 'torderassignment', 'torderassignment.OrderUID = torders.OrderUID','left');
		$this->db->where_in('torders.StatusUID', $status);
		$this->db->where('torderassignment.AssignedToUserUID',$loggedid);
		$this->db->where('torderassignment.WorkflowStatus !=',5);
		$this->db->where('torderassignment.WorkflowStatus !=',4);
		$this->db->where ('torderassignment.WorkflowModuleUID',4);
		$this->db->group_by('torders.OrderUID');
		$query = $this->db->get();
		return $query->row();
	}



	function get_next_review_orders($filter,$iscount = false)
	{

		$filter_where = '';

		if(!in_array( $this->RoleType, $this->config->item( 'AdminSupervisor' ) )) {
			$filter_where = ' AND NOT EXISTS ( SELECT 1 FROM `torderassignment` WHERE `torderassignment`.`OrderUID` = `torders`.`OrderUID` AND `torderassignment`.`WorkflowModuleUID` = 2 AND `torderassignment`.`AssignedToUserUID` = '.$this->loggedid.')';
		}
		

		//Group Geo Conditions 
		$group_where = $this->Myorders_model->get_group_whereconditions($filter);
		$filter_where  .= !empty($group_where) ? " AND (".$group_where.") " : '';
 
		$sql = "SELECT
		*
		FROM
		`torders`
		JOIN `msubproducts` ON `msubproducts`.`SubProductUID` = `torders`.`SubProductUID`
		JOIN `mproducts` ON `mproducts`.`ProductUID` = `msubproducts`.`ProductUID`
		JOIN mcustomerworkflowmodules ON mcustomerworkflowmodules.CustomerUID = torders.CustomerUID
		AND mcustomerworkflowmodules.WorkflowModuleUID = 4
		AND mcustomerworkflowmodules.SubProductUID = torders.SubProductUID
	    WHERE 
		`torders`.`OrderUID` NOT IN (
		SELECT
		`OrderUID`
		FROM
		`torderassignment`
		WHERE
		(`torderassignment`.`WorkflowModuleUID` = 4 AND `torderassignment`.`AssignedToUserUID` IS NOT NULL )
		)

		AND `torders`.`OrderUID` NOT IN (
		SELECT
		`OrderUID`
		FROM
		`torderassignment`
		WHERE
		( `torderassignment`.`WorkflowModuleUID` != 4 AND `torderassignment`.`Workflowstatus` = 5 AND `torderassignment`.`SendToVendor` = '1' AND `torderassignment`.`VendorUID` IS NOT NULL AND `torderassignment`.`AssignedToUserUID` IS NOT NULL  AND (`torderassignment`.`QCCompletedDateTime` IS  NULL OR `torderassignment`.`QCCompletedDateTime` = '0000-00-00 00:00:00'))
		)
		AND `torders`.`StatusUID` IN (19,20) $filter_where GROUP BY `torders`.`OrderUID` ORDER BY FIELD(`torders`.`PriorityUID`,1,3,2),OrderEntryDatetime ASC";
		$query = $this->db->query($sql);

		//to fetch count
		if($iscount) {
			return $query->num_rows();
		}
		return $query->row(); 
	}

	function get_nextreview_orderall($filter)
	{  
		$group = $filter['GroupUID'];
		$product = $filter['ProductUID'];
		$subprod = $filter['SubProductUID'];
		$customer = $this->get_customeringroup($group);
		if(count($customer)>0)
		{
		  $CustomerUID = $customer->Customers;
		} else {
		  $CustomerUID = 0;	
		}

		$filter_where = '';
		if($group!='' && $product=='' && $subprod=='')
		{
		  $filter_where = 'AND torders.CustomerUID IN ('.$CustomerUID.')';
		} else if($group!='' && $product!='' && $subprod=='') {
		  $Subproduct = $this->get_customersubproduct($CustomerUID,$product);
		  $filter_where = 'AND torders.CustomerUID IN ('.$CustomerUID.') AND torders.SubProductUID IN ('.$Subproduct.')';	
		} else if($group!='' && $subprod!='') {
		  $filter_where = 'AND torders.CustomerUID IN ('.$CustomerUID.') AND torders.SubProductUID = '.$subprod;
		} 

		$sql = "SELECT
		*
		FROM
		`torders`
		JOIN mcustomerworkflowmodules ON mcustomerworkflowmodules.CustomerUID = torders.CustomerUID
		AND mcustomerworkflowmodules.WorkflowModuleUID = 4
		AND mcustomerworkflowmodules.SubProductUID = torders.SubProductUID
		WHERE
		`torders`.`OrderUID` NOT IN (
		SELECT
		`OrderUID`
		FROM
		`torderassignment`
		WHERE
		(`torderassignment`.`WorkflowModuleUID` = 4 AND `torderassignment`.`AssignedToUserUID` IS NOT NULL )
		)
		AND `torders`.`OrderUID` NOT IN (
		SELECT
		`OrderUID`
		FROM
		`torderassignment`
		WHERE
		( `torderassignment`.`WorkflowModuleUID` != 4 AND `torderassignment`.`Workflowstatus` = 5 AND `torderassignment`.`SendToVendor` = '1' AND `torderassignment`.`VendorUID` IS NOT NULL AND `torderassignment`.`AssignedToUserUID` IS NOT NULL  AND (`torderassignment`.`QCCompletedDateTime` IS NULL OR `torderassignment`.`QCCompletedDateTime` = '0000-00-00 00:00:00'))
		)
		AND `torders`.`StatusUID`IN (19,20) $filter_where GROUP BY `torders`.`OrderUID` ORDER BY FIELD(`torders`.`PriorityUID`,1,3,2),OrderEntryDatetime ASC";

		$query = $this->db->query($sql);
		return $query->row();
	}

	function get_customeringroup($GroupUID)
	{
	  $query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT `mcustomers`.`CustomerUID`) AS Customers FROM (`mgroupcustomers`) JOIN `mcustomers` ON `mcustomers`.`CustomerUID` = `mgroupcustomers`.`GroupCustomerUID` WHERE `mgroupcustomers`.`GroupUID`  =  $GroupUID  AND mcustomers.Active = 1 ORDER BY `mcustomers`.`CustomerName`  ");
	  return $query->row();
	}

	function get_customersubproduct($Customers, $ProductUID)
	{
	   $query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT SubProductUID) AS SubProducts FROM `mcustomerproducts` WHERE CustomerUID IN ($Customers) AND ProductUID = $ProductUID");
	   $subprod = $query->row();
	   if(count($subprod)>0)
	   {
	   	 return $subprod->SubProducts;
	   } else {
	   	 return 0;
	   }
	}

	function assign_review_orders($Order,$loggedid){

        $this->db->where('torderassignment.AssignedToUserUID IS NULL');
        $this->db->where('torderassignment.OrderUID',$Order->OrderUID);
        $this->db->where('torderassignment.WorkflowModuleUID',4);
        $q = $this->db->get('torderassignment');


        if ( $q->num_rows() > 0 ) 
        {
            $updatedata = array(
                'AssignedToUserUID' => $loggedid,
                'AssignedByUserUID' => $loggedid,
                'AssignedDatetime' =>  Date('Y-m-d H:i:s',strtotime("now")),
                'SelfManualAssign'=>'SELF'

            );
            $this->db->where('torderassignment.WorkflowModuleUID',4);
            $this->db->where('torderassignment.OrderUID',$Order->OrderUID);
            $inserted = $this->db->update('torderassignment',$updatedata);
        } else {

            $assign_data = array(
                'OrderUID' => $Order->OrderUID,
                'WorkflowModuleUID' => 4,
                'AssignedToUserUID' => $loggedid,
                'AssignedDatetime' =>  Date('Y-m-d H:i:s',strtotime("now")),
                'AssignedByUserUID' => $loggedid,
                'WorkflowStatus' => '0',
                'SelfManualAssign'=>'SELF'

            );
            $inserted = $this->db->insert('torderassignment',$assign_data);

        }

        if(isset($inserted)){
            return true;
        }else{
            return false;
        }

    }


    function get_assigned_users($OrderUID)
    {

      $this->db->select ( 'LoginID' ); 
      $this->db->from ( 'torderassignment' );
      $this->db->join ( 'musers', 'musers.UserUID = torderassignment.AssignedToUserUID');
      $this->db->where ('torderassignment.WorkflowModuleUID',4);
      $this->db->where ('torderassignment.OrderUID',$OrderUID);
      $query = $this->db->get();
      $result =  $query->row();
      if(count($result) > 0){
      	return $result->LoginID;
      }else{
      	return '';
      }
    }

    

	function get_customer_workflow($CustomerUID = '',$SubProductUID = '')
	{
		$this->db->distinct();
		$this->db->select ('CustomerUID,WorkflowModuleName,mcustomerworkflowmodules.workflowmoduleUID'); 
		$this->db->from ( 'mcustomerworkflowmodules' );
		$this->db->join ( 'mworkflowmodules', 'mcustomerworkflowmodules.workflowmoduleUID = mworkflowmodules.WorkflowModuleUID');
		$this->db->where ('mcustomerworkflowmodules.CustomerUID',$CustomerUID);
		$this->db->where ('mcustomerworkflowmodules.SubProductUID',$SubProductUID);
		$query = $this->db->get();
		return $query->result();
	}



	function get_customer_workflow_exclude_printing($CustomerUID = '', $SubProductUID = '')
	{
		$this->db->distinct();
		$this->db->select('CustomerUID,WorkflowModuleName,mcustomerworkflowmodules.workflowmoduleUID');
		$this->db->from('mcustomerworkflowmodules');
		$this->db->join('mworkflowmodules', 'mcustomerworkflowmodules.workflowmoduleUID = mworkflowmodules.WorkflowModuleUID');
		$this->db->where('mcustomerworkflowmodules.CustomerUID', $CustomerUID);
		$this->db->where('mcustomerworkflowmodules.SubProductUID', $SubProductUID);
		$this->db->where('mworkflowmodules.WorkflowModuleUID <> 5');
		$query = $this->db->get();
		return $query->result();
	}

	//count all
function count_all($post)
    {
     //    $this->db->select("torders.OrderUID,torders.OrderNumber, torders.LoanNumber, torders.LoanAmount, torders.PropertyStateCode, torderinfo.AssignorPrintName, torderinfo.AssigneePrintName, torderinfo.LenderPrintName, 
     //    CASE WHEN torderinfo.MortgageDated = '0000-00-00 00:00:00' THEN '' ELSE torderinfo.MortgageDated END AS MortgageDated, 
     //    CASE WHEN torderinfo.DeedOfTrustDated = '0000-00-00 00:00:00' THEN '' ELSE torderinfo.DeedOfTrustDated END AS DeedOfTrustDated,
     //    CASE WHEN torderinfo.RecordedDate = '0000-00-00 00:00:00' THEN '' ELSE torderinfo.RecordedDate END AS RecordedDate, 
     //    torderinfo.Book, torderinfo.Page, torderinfo.DocumentNumber, torderinfo.Comments, torderinfo.TaxID, torderinfo.LegalDescription, torderinfo.EndorserPrintName, torderinfo.PreparedBy,CASE WHEN morderstatus.StatusName = 'Review Complete' THEN 'Waiting for Print' WHEN morderstatus.StatusName = 'Work In Progress' THEN 'New' WHEN morderstatus.StatusName = 'Assigned' THEN 'New' ELSE morderstatus.StatusName END as StatusName,torders.ProjectUID,
     //    torders.SubProductUID,torders.CustomerUID,mProjects.ProjectName",false);
     //  $this->db->from('torders');
     //  $this->db->join('torderinfo','torderinfo.OrderUID = torders.OrderUID','left');
     //  $this->db->join('morderstatus','torders.StatusUID = morderstatus.StatusUID','left');
     //  $this->db->join('mProjects','mProjects.ProjectUID = torders.ProjectUID','left');
     //  $this->db->select("CONCAT(torders.LoanNumber,mProjects.AssignmentCode) AS BarCode", false);
     //  $where = array('torders.SubproductUID' => 65);
     //  $this->db->where($where);
     //  $this->db->where_not_in('torders.StatusUID','110');
      
     // if (is_array($post['advancedsearch']))  
     //    {
     //        if($post['advancedsearch']['PropertyStateCode']){
     //          $this->db->where('torders.PropertyStateCode',$post['advancedsearch']['PropertyStateCode']);
     //        }
     //        if($post['advancedsearch']['SubProductUID']){
     //          $this->db->where('torders.SubProductUID',$post['advancedsearch']['SubProductUID']);
     //        }
     //        if($post['advancedsearch']['CustomerUID']){
     //          $this->db->where('torders.CustomerUID',$post['advancedsearch']['CustomerUID']);
     //        }
     //        if($post['advancedsearch']['ProjectUID']){
     //          $this->db->where('torders.ProjectUID',$post['advancedsearch']['ProjectUID']);
     //        }
     //        if($post['advancedsearch']['FromDate']){
     //          $this->db->where('DATE(torders.OrderEntryDatetime) >= "'.date('Y-m-d', strtotime($post['advancedsearch']['FromDate'])).'"', NULL, false);
     //        }
     //        if($post['advancedsearch']['ToDate']){
     //          $this->db->where('DATE(torders.OrderEntryDatetime) <="'.date('Y-m-d', strtotime($post['advancedsearch']['ToDate'])).'"',NULL, false);
     //        }
         
     //    }


     //  $query = $this->db->count_all_results();
     //  return $query;

    	$status[0] = $this->config->item('keywords')['Draft Complete'];
		$status[1] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[2] = $this->config->item('keywords')['Review In Progress'];
		$status[3] = $this->config->item('keywords')['Partial Review Complete'];
		$post['loggedid'] = $loggedid;

		$is_permission = $this->common_model->role_workflows();
		

		$groupin_user_logged = $this->get_user_group_id($loggedid);
		$CustomerUIDs = $this->get_customer_ingroup($groupin_user_logged);
		$cus_id =[];
		foreach ($CustomerUIDs as $key => $CustomerUID) {
			$cus_id[] = $CustomerUID->CustomerUID;
		}

		$Workflowstatus[0] = 0;
		$Workflowstatus[1] = 3;


		$this->db->select ("OrderNumber,torders.CustomerUID,CustomerNumber,CustomerName,OrderTypeName,StatusColor,morderstatus.StatusName, torders.OrderUID,morderpriority.PriorityUID,PriorityName,PropertyStateCode, mProjects.ProjectName, torders.OrderDueDatetime");
		$this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);				 
		$this->db->from ( 'torders' );
		$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID');
		$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID','left');
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID','left');
		$this->db->join ( 'mProjects', 'mProjects.ProjectUID = torders.ProjectUID', 'left');
		$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID');

		if (!in_array($this->RoleType, [1,2,3,4,5,6])) {			
			if(!empty($cus_id)){
				$this->db->where_in ('torders.CustomerUID',$cus_id);
			}
		}

		if ($is_permission->ReviewQueue == 2){

			$this->db->join('torderassignment','torders.OrderUID = torderassignment.OrderUID','left');


		}else{

			$this->db->join('torderassignment','torders.OrderUID = torderassignment.OrderUID','left');
			$this->db->where("torderassignment.AssignedToUserUID = " . $this->loggedid);
			$this->db->where('torderassignment.WorkflowModuleUID',4);
		}

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserSubProducts = $this->common_model->_get_Subproducts_bylogin($this->loggedid);
			if($UserSubProducts): $this->db->where('torders.SubProductUID IN ('.$UserSubProducts.')', null, false); else: $this->db->where('torders.SubProductUID IN (0)', null, false); endif;
		}
if (is_array($post['advancedsearch']))
        {
            // if($post['advancedsearch']['PropertyStateCode']){
            //   $this->db->where('torders.PropertyStateCode',$post['advancedsearch']['PropertyStateCode']);
            // }
            if($post['advancedsearch']['SubProductUID']){
              $this->db->where('torders.SubProductUID',$post['advancedsearch']['SubProductUID']);
            }
            if($post['advancedsearch']['CustomerUID']){
              $this->db->where('torders.CustomerUID',$post['advancedsearch']['CustomerUID']);
            }
            if($post['advancedsearch']['ProjectUID']){
              $this->db->where('torders.ProjectUID',$post['advancedsearch']['ProjectUID']);
            }
            // if($post['advancedsearch']['FromDate']){
            //   $this->db->where('DATE(torders.OrderEntryDatetime) >= "'.date('Y-m-d', strtotime($post['advancedsearch']['FromDate'])).'"', NULL, false);
            // }
            // if($post['advancedsearch']['ToDate']){
            //   $this->db->where('DATE(torders.OrderEntryDatetime) <="'.date('Y-m-d', strtotime($post['advancedsearch']['ToDate'])).'"',NULL, false);
            // }
        }
		
		$this->db->where_in ('torders.StatusUID',$status);
		$this->db->group_by('torders.OrderUID');
		$query = $this->db->count_all_results();
      return $query;
    }

//count filtered
function count_filtered($post)
    {
      /*$this->db->select("torders.OrderUID,torders.OrderNumber, torders.LoanNumber, torders.LoanAmount, torders.PropertyStateCode, torderinfo.AssignorPrintName, torderinfo.AssigneePrintName, torderinfo.LenderPrintName, 
        CASE WHEN torderinfo.MortgageDated = '0000-00-00 00:00:00' THEN '' ELSE torderinfo.MortgageDated END AS MortgageDated, 
        CASE WHEN torderinfo.DeedOfTrustDated = '0000-00-00 00:00:00' THEN '' ELSE torderinfo.DeedOfTrustDated END AS DeedOfTrustDated,
        CASE WHEN torderinfo.RecordedDate = '0000-00-00 00:00:00' THEN '' ELSE torderinfo.RecordedDate END AS RecordedDate, 
        torderinfo.Book, torderinfo.Page, torderinfo.DocumentNumber, torderinfo.Comments, torderinfo.TaxID, torderinfo.LegalDescription, torderinfo.EndorserPrintName, torderinfo.PreparedBy,CASE WHEN morderstatus.StatusName = 'Review Complete' THEN 'Waiting for Print' WHEN morderstatus.StatusName = 'Work In Progress' THEN 'New' WHEN morderstatus.StatusName = 'Assigned' THEN 'New' ELSE morderstatus.StatusName END as StatusName,torders.ProjectUID,
        torders.SubProductUID,torders.CustomerUID,mProjects.ProjectName",false);
      $this->db->from('torders');
      $this->db->join('torderinfo','torderinfo.OrderUID = torders.OrderUID','left');
      $this->db->join('morderstatus','torders.StatusUID = morderstatus.StatusUID','left');
      $this->db->join('mProjects','mProjects.ProjectUID = torders.ProjectUID','left');
      $this->db->select("CONCAT(torders.LoanNumber,mProjects.AssignmentCode) AS BarCode", false);
      $where = array('torders.SubproductUID' => 65);
      $this->db->where($where);
      $this->db->where_not_in('torders.StatusUID','110');

        if (is_array($post['advancedsearch']))
        {
            if($post['advancedsearch']['PropertyStateCode']){
              $this->db->where('torders.PropertyStateCode',$post['advancedsearch']['PropertyStateCode']);
            }
            if($post['advancedsearch']['SubProductUID']){
              $this->db->where('torders.SubProductUID',$post['advancedsearch']['SubProductUID']);
            }
            if($post['advancedsearch']['CustomerUID']){
              $this->db->where('torders.CustomerUID',$post['advancedsearch']['CustomerUID']);
            }
            if($post['advancedsearch']['ProjectUID']){
              $this->db->where('torders.ProjectUID',$post['advancedsearch']['ProjectUID']);
            }
            if($post['advancedsearch']['FromDate']){
              $this->db->where('DATE(torders.OrderEntryDatetime) >= "'.date('Y-m-d', strtotime($post['advancedsearch']['FromDate'])).'"', NULL, false);
            }
            if($post['advancedsearch']['ToDate']){
              $this->db->where('DATE(torders.OrderEntryDatetime) <="'.date('Y-m-d', strtotime($post['advancedsearch']['ToDate'])).'"',NULL, false);
            }
         
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

          if (!empty($post['order'])) 
          { 
          // here order processing 
            if($post['column_order'][$post['order'][0]['column']]!='')
            {
              $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);    
            }    
          } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);  
          }   
        // $this->db->order_by('OrderEntryDatetime');
        $query = $this->db->get();
        return $query->num_rows();*/

        $status[0] = $this->config->item('keywords')['Draft Complete'];
		$status[1] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[2] = $this->config->item('keywords')['Review In Progress'];
		$status[3] = $this->config->item('keywords')['Partial Review Complete'];
		$post['loggedid'] = $loggedid;

		$is_permission = $this->common_model->role_workflows();
		

		$groupin_user_logged = $this->get_user_group_id($loggedid);
		$CustomerUIDs = $this->get_customer_ingroup($groupin_user_logged);
		$cus_id =[];
		foreach ($CustomerUIDs as $key => $CustomerUID) {
			$cus_id[] = $CustomerUID->CustomerUID;
		}

		$Workflowstatus[0] = 0;
		$Workflowstatus[1] = 3;


		$this->db->select ("OrderNumber,torders.CustomerUID,CustomerNumber,CustomerName,OrderTypeName,StatusColor,morderstatus.StatusName, torders.OrderUID,morderpriority.PriorityUID,PriorityName,PropertyStateCode, mProjects.ProjectName, torders.OrderDueDatetime");
		$this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);				 
		$this->db->from ( 'torders' );
		$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID');
		$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID','left');
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID','left');
		$this->db->join ( 'mProjects', 'mProjects.ProjectUID = torders.ProjectUID', 'left');
		$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID');

		if (!in_array($this->RoleType, [1,2,3,4,5,6])) {			
			if(!empty($cus_id)){
				$this->db->where_in ('torders.CustomerUID',$cus_id);
			}
		}

		if ($is_permission->ReviewQueue == 2){

			$this->db->join('torderassignment','torders.OrderUID = torderassignment.OrderUID','left');


		}else{

			$this->db->join('torderassignment','torders.OrderUID = torderassignment.OrderUID','left');
			$this->db->where("torderassignment.AssignedToUserUID = " . $this->loggedid);
			$this->db->where('torderassignment.WorkflowModuleUID',4);
		}

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserSubProducts = $this->common_model->_get_Subproducts_bylogin($this->loggedid);
			if($UserSubProducts): $this->db->where('torders.SubProductUID IN ('.$UserSubProducts.')', null, false); else: $this->db->where('torders.SubProductUID IN (0)', null, false); endif;
		}
if (is_array($post['advancedsearch']))
        {
            // if($post['advancedsearch']['PropertyStateCode']){
            //   $this->db->where('torders.PropertyStateCode',$post['advancedsearch']['PropertyStateCode']);
            // }
            if($post['advancedsearch']['SubProductUID']){
              $this->db->where('torders.SubProductUID',$post['advancedsearch']['SubProductUID']);
            }
            if($post['advancedsearch']['CustomerUID']){
              $this->db->where('torders.CustomerUID',$post['advancedsearch']['CustomerUID']);
            }
            if($post['advancedsearch']['ProjectUID']){
              $this->db->where('torders.ProjectUID',$post['advancedsearch']['ProjectUID']);
            }
            // if($post['advancedsearch']['FromDate']){
            //   $this->db->where('DATE(torders.OrderEntryDatetime) >= "'.date('Y-m-d', strtotime($post['advancedsearch']['FromDate'])).'"', NULL, false);
            // }
            // if($post['advancedsearch']['ToDate']){
            //   $this->db->where('DATE(torders.OrderEntryDatetime) <="'.date('Y-m-d', strtotime($post['advancedsearch']['ToDate'])).'"',NULL, false);
            // }
        }
		
		$this->db->where_in ('torders.StatusUID',$status);
		$this->db->group_by('torders.OrderUID');
		 $query = $this->db->get();
        return $query->num_rows();
    }

}
?>
