<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Printing_Orders_Model extends CI_Model {

	
	function __construct()
	{ 
		parent::__construct();
		$this->config->load('keywords');
	}


	function get_customer_group_id($user_in_groups)
	{
		$this->db->select ( 'GroupUID' ); 
		$this->db->from ( 'mgroupcustomers' );
		if($user_in_groups != null){
			$this->db->where_in ('mgroupcustomers.GroupUID',$user_in_groups);
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

	function get_print_orders($loggedid,$post)
	{
		$status[0] = $this->config->item('keywords')['Review Complete'];

		$this->db->select ("OrderNumber,torders.CustomerUID,CustomerNumber,CustomerName,OrderTypeName,StatusColor,PriorityName,morderstatus.StatusName, torders.OrderUID,morderpriority.PriorityUID,PriorityName,PropertyStateCode, torders.LoanNumber, CompleteDateTime AS ReviewCompleteDateTime, mProjects.ProjectName, torders.OrderDueDatetime AS Ymd_OrderDueDatetime", false);
		$this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);				 
		$this->db->from ( 'torders' );
		$this->db->join ( 'torderassignment', 'torders.OrderUID = torderassignment.OrderUID');
		$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID','left');
		$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID','left');
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID','left');
		$this->db->join ( 'msubproducts', 'msubproducts.SubProductUID = torders.SubProductUID' , 'left' );
		$this->db->join ( 'mstates', 'mstates.StateCode = torders.PropertyStateCode' , 'left' );
		$this->db->join('mProjects', 'mProjects.ProjectUID = torders.ProjectUID', 'left');
		$this->db->join ( 'mproducts', 'mproducts.ProductUID = msubproducts.ProductUID');		
		$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID');

		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserProducts = $this->common_model->_get_product_bylogin();
			if($UserProducts){
				$this->db->where('mproducts.ProductUID IN ('.$UserProducts.')', null, false);					
			}
		}

		if (sizeof(array_filter($post))!=0) 
		{
			// print_r(array_filter($post));
			if($post['OrderType'] != 'U'){
				$filter = $this->unprint_order_filter_keywords($post); 
				$this->db->where($filter, null, false);					
				$this->db->where('torders.IsPrint',NULL);
				$this->db->where('torders.ProjectUID !=0',NULL, false);
				$this->db->where_in ('torders.StatusUID',$status);
			}else{
				$filter = $this->printed_order_filter_keywords($post); 
				$this->db->where($filter, null, false);
				$this->db->where('torders.IsPrint',1);	
				$this->db->where('torders.StatusUID',100);			
			}
		} 
		else{
			$this->db->where('torders.IsPrint',NULL);  
			$this->db->where('torders.ProjectUID!=0',NULL, false);  
			$this->db->where_in ('torders.StatusUID',$status); 
		}
		$this->db->group_by('torders.OrderUID');
		$query = $this->db->get();
		// print $this->db->last_query();
		return $query->result();
	}

		function printed_order_filter_keywords($post)
		{
			$keywords =  array_filter($post);
			$like = array();
			foreach ($keywords as $key => $item) 
			{  
				if ($item != '') 
				{ 
					if($key=='SubProductUID')
					{
						$like[] = "torders.SubProductUID='".$item."'"; 
					}
					else if($key=='CustomerUID')
					{
						$like[] = "mcustomers.CustomerUID = '".$item."'"; 
					}
					else if($key=='FromDate' && !array_key_exists('ToDate',$keywords))
					{
						$like[] = "DATE(torderassignment.AssignedDatetime) = '".date('Y-m-d',strtotime($item))."'"; 
					}
					else if($key=='ToDate' && !array_key_exists('FromDate',$keywords))
					{
						$like[] = "DATE(torderassignment.AssignedDatetime) = '".date('Y-m-d',strtotime($item))."'"; 
					}
				}
			}
			if(array_key_exists('FromDate',$keywords) && array_key_exists('ToDate',$keywords)) 
			{
				$like[] = "DATE(torderassignment.AssignedDatetime) BETWEEN '".date('Y-m-d',strtotime($keywords['FromDate']))."' AND '".date('Y-m-d',strtotime($keywords['ToDate']))."'"; 
			}         
			$keyword_where = implode(' AND ', $like); 
			return $keyword_where; 
		}  

		function unprint_order_filter_keywords($post)
		{
			$keywords =  array_filter($post);
			$like = array();
			foreach ($keywords as $key => $item) 
			{  
				if ($item != '') 
				{ 
					if($key=='ProjectUID')
					{
						$like[] = "torders.ProjectUID='".$item."'"; 
					}
					else if($key=='CustomerUID')
					{
						$like[] = "mcustomers.CustomerUID = '".$item."'"; 
					}
					else if($key=='MERS')
					{
						if ($item=='2' || $item=='' || $item=='0') {
							$item='';
						}
						$like[] = "torders.IsMERS='".$item."'"; 
					}
					else if($key=='VP' && $item!='')
					{
						if ($item!=1) {
							$item=0;
						}

						$like[] = "mstates.IsVPAndAbove='".$item."'"; 
					}
					else if($key=='StateUID')
					{
						if ($item!="all") {
							$like[] = "mstates.StateUID='".$item."'"; 
						}

					}
				}
			}
       
			$keyword_where = implode(' AND ', $like); 
			return $keyword_where; 
		} 			

	function get_pending_reviewedorders($loggedid){

		$status[0] = $this->config->item('keywords')['Draft Complete'];
		$status[1] = $this->config->item('keywords')['Partial Draft Complete'];
		$status[2] = $this->config->item('keywords')['Review In Progress'];
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
		return $query->num_rows();
	}



	function get_next_review_orders($loggedid, $filter)
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
		AND 
		`torders`.`OrderUID` NOT IN (
		SELECT
		`OrderUID`
		FROM
		`torderassignment`
		WHERE
		(`torderassignment`.`WorkflowModuleUID` = 2 AND `torderassignment`.`AssignedToUserUID` = $loggedid )
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

    function GetProducts()
    {
    	/*FOR SUPERVISOR CHECK*/
    	if ($this->session->userdata('RoleType') == 6){
    		$UserProducts = $this->common_model->_get_product_bylogin();
    		if(!empty($UserProducts)){

    			$this->db->select('*');
    			$this->db->from('mproducts');
    			$this->db->where('ProductUID IN ('.$UserProducts.')', NULL, FALSE);
    			$this->db->where('Active',1);
    			return $this->db->get()->result();
    			
    		}
    		return array();
    	}else{
    		$this->db->select('*');
    		$this->db->from('mproducts');
    		$this->db->where('Active',1);
    		return $this->db->get()->result();
    	}
    } 

	function GetProjects()
	{
		$q = $this->db->get_where('mProjects',array('Active'=>1));
		return $q->result();
	} 

	function GetCustomers($login_id)
	{
		if(in_array($this->session->userdata('RoleType'),array(7,9))) 
		{
			$this->db->select('mcustomers.CustomerUID,mcustomers.CustomerName,mcustomers.CustomerNumber');
			$this->db->from('mgroupusers');
			$this->db->join('mgroupcustomers','mgroupcustomers.GroupUID = mgroupusers.GroupUID','left');
			$this->db->join('mcustomers','mcustomers.CustomerUID = mgroupcustomers.GroupCustomerUID','left');
			$this->db->where('GroupUserUID = '.$login_id.' AND mcustomers.Active = 1');
			return $this->db->get()->result();
		} 
		else if(in_array($this->session->userdata('RoleType'),array(8)))
		{
			$this->db->select('mcustomers.CustomerUID,mcustomers.CustomerName,mcustomers.CustomerNumber');
			$this->db->from('mcustomers');
			$this->db->join('musers','musers.CustomerUID = mcustomers.CustomerUID','left');
			$this->db->where('musers.UserUID',$login_id);
			$this->db->group_by('mcustomers.CustomerUID');
			return $this->db->get()->result();
		} else {


			/*FOR SUPERVISOR CHECK*/
			if ($this->session->userdata('RoleType') == 6){
				$UserProducts = $this->common_model->_get_product_bylogin();
				if($UserProducts){
					$UserCustomers = $this->common_model->_get_customers_bylogin($UserProducts);
					if($UserCustomers){
						$this->db->select('*');
						$this->db->from('mcustomers');
						$this->db->where('CustomerUID IN ('.$UserCustomers.')', NULL, FALSE); 
						return $this->db->get()->result();
					}

					return array();
					
				}else{
					return array();
				}
			}else{
				$this->db->where('Active',1);
				return $this->db->get('mcustomers')->result();
			}


		}

	}  	

    function get_assigned_users($OrderUID)
    {
		$this->db->select ( 'LoginID' ); 
		$this->db->from ( 'torderassignment' );
		$this->db->join ( 'musers', 'musers.UserUID = torderassignment.AssignedToUserUID','inner');
		$this->db->where ('torderassignment.WorkflowModuleUID',5);
		$this->db->where ('torderassignment.OrderUID',$OrderUID);
		$query = $this->db->get();
		$result =  $query->row();
		if(count($result) > 0){
			return $result->LoginID;
		}else{
			return '';
		}
    }

    function GetProjectByCustomerUID($CustomerUID)
    {
    	return $this->db->get_where('mProjects', array('CustomerUID'=>$CustomerUID))->result();
    }

    function GetOrderDocuments($OrderUID)
    {
    	$this->db->select('torderdocuments.*, musers.*, torders.OrderDocsPath')->from('torderdocuments');
    	$this->db->join('musers', 'musers.UserUID=torderdocuments.UploadedUserUID', 'left');
    	$this->db->join('torders', 'torders.OrderUID=torderdocuments.OrderUID', 'left');
    	$this->db->where('torderdocuments.OrderUID', $OrderUID);
    	$this->db->where('torderdocuments.TypeOfDocument!="Final Reports"', NULL, FALSE);
    	$query=$this->db->get();
    	return $query->result();
    }

    function GetmProject($ProjectUID)
    {
    	return $this->db->get_where('mProjects', ['ProjectUID'=>$ProjectUID, 'HasBarCode'=>1])->row();
    }

    function GetmProjectByProjectUID($ProjectUID)
    {
    	return $this->db->get_where('mProjects', ['ProjectUID'=>$ProjectUID])->row();
    }


    function GetOrdersGroupByState($OrderUIDs)
    {
    	$this->db->select('GROUP_CONCAT(torders.OrderUID) AS OrderuIDs, torders.PropertyStateCode, mProjects.ProjectName, mProjects.ProjectUID, mProjects.AssignmentCode', false);
    	$this->db->select('(CASE WHEN (torderinfo.IsMERSAssignor = 1 OR torderinfo.IsMERSEndorser = 1) THEN "MERS" ELSE "NON-MERS" END) AS MERS', false);
    	$this->db->from('torders');
    	$this->db->join('mProjects', 'mProjects.ProjectUID=torders.ProjectUID', 'left');
    	$this->db->join('torderinfo', 'torderinfo.OrderUID=torders.OrderUID');
    	$this->db->where_in('torders.OrderUID', $OrderUIDs);
    	$this->db->group_by('torders.PropertyStateCode');
    	$this->db->group_by('torders.ProjectUID');
    	$this->db->group_by('MERS');
    	$this->db->order_by('torders.PropertyStateCode', 'ASC');
    	$this->db->order_by('torders.ProjectUID', 'ASC');
    	$this->db->order_by('MERS', 'ASC');
    	return $this->db->get()->result();
    }

    function GetPrintingOrdersByOrderUIDs($OrderUIDs)
    {

    	// $OrderUIDs = explode(',', $OrderUIDs);
    	$this->db->select ("OrderNumber,torders.CustomerUID,CustomerNumber,CustomerName,StatusColor,morderstatus.StatusName, torders.OrderUID,PropertyStateCode, torders.LoanNumber, CompleteDateTime AS ReviewCompleteDateTime, mProjects.ProjectName", false);
    	$this->db->select('DATE_FORMAT(torders.OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);				 
    	$this->db->from ( 'torders' );
    	$this->db->join ( 'torderassignment', 'torders.OrderUID = torderassignment.OrderUID');
    	$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID','left');
    	$this->db->join('mProjects', 'mProjects.ProjectUID = torders.ProjectUID', 'left');
    	$this->db->join ( 'morderstatus', 'morderstatus.StatusUID = torders.StatusUID');
    	$this->db->where_in('torders.OrderUID', $OrderUIDs);
    	$this->db->group_by('torders.OrderUID');
    	return $this->db->get()->result();
    }

    function UpdateBulkPrintComplete($OrderUIDs=[])
    {
    	if (empty($OrderUIDs)) {
    		return false;
    	}

    	foreach ($OrderUIDs as $key => $OrderUID) {

    		$assign_data = array(
    			'OrderUID' => $OrderUID,
    			'WorkflowModuleUID' => '5',
    			'AssignedToUserUID' => $this->loggedid,
    			'AssignedDatetime' => Date('Y-m-d H:i:s',strtotime("now")),
    			'CompleteDateTime' => Date('Y-m-d H:i:s',strtotime("now")),
    			'AssignedByUserUID' => $this->loggedid,
    			'WorkflowStatus' => '5'
    		);

    		$inserted = $this->db->insert('torderassignment',$assign_data);

			//audit trail
    		$data1['ModuleName']='Printing'.' '.'workflow Status-Status';
    		$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    		$data1['DateTime']=date('y-m-d H:i:s');
    		$data1['TableName']='torderassignment';
    		$data1['OrderUID']=$orderUID;
    		$data1['UserUID']=$this->session->userdata('UserUID'); 
    		$data1['OldValue']=''; 
    		$data1['FieldUID']='729';
    		$data1['NewValue']=$this->session->userdata('UserName');
    		$this->common_model->Audittrail_insert($data1); 

    		$this->db->where('OrderUID',$OrderUID);
    		$res = $this->db->update('torders',array('StatusUID'=>'100','IsPrint'=>'1', 'OrderCompleteDateTime' => Date('Y-m-d H:i:s',strtotime("now"))));

    	}

    	$BulkPrintFile = $this->session->userdata('BulkPrintFile');
    	$this->session->unset_userdata('BulkPrintFile');

    	if (file_exists($BulkPrintFile) && is_file($BulkPrintFile)) {
    		unlink($BulkPrintFile);
    	}
    	return true;
    }

    function GetSignorVPAndAboveForSignors($Signors)
    {
    	$VP_Array = [];
    	foreach ($Signors as $key => $value) {
    		if($value->IsVPAndAbove == 1){
    			$VP_Array[] = "VP";
    		}
    		else{
    			$VP_Array[] = "Regular";	
    		}
    	}
    	return array_unique($VP_Array);
    }
}
?>
