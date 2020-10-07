<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Common_model extends CI_Model {


	function __construct()
	{
		parent::__construct();
		$this->load->config('keywords');
		$UserUID = $this->session->userdata('UserUID');
		$this->RoleUID = $this->session->userdata('RoleUID');
		$this->loggedid = $this->session->userdata('UserUID');
		$this->RoleType = $this->session->userdata('RoleType');
		if ($UserUID != '') {
			$this->set_lastactivitydatetime($UserUID);
		}
	}

	/* @purpose: To get fields from master data @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: May 21st 2020 */
	public function GetMaterFieldList($OrderUID, $CustomerUID, $SubProductUID){
		$this->db->select('*')->from('tOrders')->where ('tOrders.OrderUID', $OrderUID);
		$tOrders = $this->db->get()->row();
		$OrderSourceUID = $tOrders->OrderSourceUID;

		$this->db->select('*')->from('mCustomerFields');
		$this->db->join('mfields','mfields.FieldUID=mCustomerFields.FieldUID');
		$this->db->where(array('mCustomerFields.CustomerUID'=>$CustomerUID,'mCustomerFields.SubProductUID'=>$SubProductUID));
		if(!empty($OrderSourceUID) || $OrderSourceUID != 0){
			$this->db->where('OrderSourceUID', $OrderSourceUID);
		}
		$this->db->where('mfields.Active', 1);
		$mCustomerFields=$this->db->get()->result();

		return $mCustomerFields;
	}
	
	/*@purpose: To get the multiples for number @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: May 21st 2020*/
	function CheckNumber($count, $Multiply){
		if ($count % $Multiply != 0){
			return 0;
		} else {
			return 1;
		}
	}

	public function CreateDirectoryToPath($Path = '')
	{
		if (empty($Path)) 
		{
			die('No Path to create directory');
		}
		if (!file_exists($Path)) {
			if (!mkdir($Path, 0777, true)) die('Unable to create directory');
		}
		chmod($Path, 0777);
	}

	public function updateUser($data, $UserUID)
	{
	   $this->db->where('UserUID', $UserUID);
	   $this->db->update('musers', $data);
	}

	public function GetAbstractorOrderDetailsByAbstractorOrderUID($AbstractorOrderUID=0)
	{
		$loggedid = $this->session->userdata('UserUID');

		$this->db->select("tOrders.*, torderabstractor.*, torderassignment.*,mSubProducts.*, mProducts.*,mOrderTypes.*,mOrderPriority.PriorityName, mOrderStatus.*, mabstractor.*", false);
		$this->db->from ( 'torderabstractor' );
		$this->db->join ( 'torderassignment', 'torderassignment.OrderUID = torderabstractor.OrderUID' , 'inner' );
		$this->db->join ( 'mabstractor', 'torderabstractor.AbstractorUID = mabstractor.AbstractorUID' , 'inner' );
		$this->db->join ( 'tOrders', 'torderabstractor.OrderUID = tOrders.OrderUID' , 'inner' );
		$this->db->join ( 'tOrderPropertyRoles', 'tOrders.OrderUID = tOrderPropertyRoles.OrderUID' , 'left' );
		$this->db->join ( 'mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID' , 'left' );
		$this->db->join ( 'mOrderTypes', 'torderabstractor.OrderTypeUID = mOrderTypes.OrderTypeUID' , 'left' );
		$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
		$this->db->where( 'torderabstractor.AbstractorOrderUID',$AbstractorOrderUID);
		$query = $this->db->get();
		return $query->row();
	}

	public function GetCancelledAbstractorOrderDetailsByAbstractorOrderUID($AbstractorOrderUID=0)
	{
		$loggedid = $this->session->userdata('UserUID');
		$mabstractor=$this->db->get_where('mabstractor', array('UserUID'=>$loggedid))->row();
		$AbstractorUID=$mabstractor->AbstractorUID;
		$this->db->select("tOrders.*, torderabstractorunassign.*, torderassignment.*,mSubProducts.*, mProducts.*,mOrderTypes.*,mOrderPriority.PriorityName, mOrderStatus.*, mabstractor.*", false);
		$this->db->from ( 'torderabstractorunassign' );
		$this->db->join ( 'torderassignment', 'torderassignment.OrderUID = torderabstractorunassign.OrderUID' , 'inner' );
		$this->db->join ( 'mabstractor', 'torderabstractorunassign.AbstractorUID = mabstractor.AbstractorUID' , 'inner' );
		$this->db->join ( 'tOrders', 'torderabstractorunassign.OrderUID = tOrders.OrderUID' , 'inner' );
		$this->db->join ( 'tOrderPropertyRoles', 'tOrders.OrderUID = tOrderPropertyRoles.OrderUID' , 'inner' );
		$this->db->join ( 'mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID' , 'left' );
		$this->db->join ( 'mOrderTypes', 'torderabstractorunassign.OrderTypeUID = mOrderTypes.OrderTypeUID' , 'left' );
		$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
		$this->db->where( 'torderabstractorunassign.AbstractorOrderUID',$AbstractorOrderUID);
		$query = $this->db->get();
		return $query->row();
	}

	function GetAbstractorOrderDetails()
	{
		$loggedid = $this->session->userdata('UserUID');
		$CurrentUser = $this->GetUserDetailsByUser($loggedid);
		$status[0] = $this->config->item('keywords')['Cancelled'];
		$this->db->select("tOrders.*, torderabstractor.*, mabstractor.*,mOrderStatus.*,mOrderTypes.*,mOrderPriority.PriorityName", false);
		$this->db->from ( 'torderassignment' );
		$this->db->join ( '(SELECT * FROM torderabstractor ORDER BY AbstractorOrderUID DESC) AS torderabstractor', 'torderassignment.OrderUID = torderabstractor.OrderUID' , 'inner' );
		$this->db->join ( 'mabstractor', 'torderabstractor.AbstractorUID = mabstractor.AbstractorUID' , 'left' );
		$this->db->join ( 'tOrders', 'torderabstractor.OrderUID = tOrders.OrderUID' , 'inner' );
		$this->db->join ( 'tOrderPropertyRoles', 'tOrders.OrderUID = tOrderPropertyRoles.OrderUID' , 'left' );
		$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
		$this->db->join ( 'mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID' , 'left' );
		$this->db->join ( 'mTemplates', 'tOrders.TemplateUID = mTemplates.TemplateUID' , 'left' );
		$this->db->join ( 'mOrderTypes', 'torderabstractor.OrderTypeUID = mOrderTypes.OrderTypeUID' , 'left' );
		$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
		/*$this->db->where( 'torderassignment.AssignedToUserUID = '.$loggedid.' OR torderabstractor.AbstractorUID = '.$CurrentUser->AbstractorUID.'',NULL,FALSE);*/
		$this->db->where( 'torderassignment.WorkflowModuleUID', 1);
		$this->db->where( 'torderassignment.WorkflowStatus <>', 5 );
		$this->db->where('torderabstractor.OrderStatus != 5');

		$this->db->where_not_in('tOrders.StatusUID', $status);
		$this->db->where('torderabstractor.AbstractorUID',$CurrentUser->AbstractorUID);
		$this->db->group_by('torderabstractor.AbstractorOrderUID');
		$this->db->order_by('torderabstractor.DueDateTime', 'ASC');
		$this->db->order_by('tOrders.OrderNumber', 'ASC');
		$query = $this->db->get();
		return $query->result();
	}


	function GetCompletedAbstractorOrderDetails()
	{
		$loggedid = $this->session->userdata('UserUID');
		$this->db->select("*, torderabstractor.AbstractorActualFee,torderabstractor.AbstractorFee,torderabstractor.AbstractorAdditionalFee,torderabstractor.AbstractorCopyCost,torderabstractor.OperatorType, mabstractor.*", false);
		$this->db->from ( 'torderabstractor' );
		$this->db->join ( 'mabstractor', 'torderabstractor.AbstractorUID = mabstractor.AbstractorUID' , 'inner' );
		$this->db->join ( 'tOrders', 'torderabstractor.OrderUID = tOrders.OrderUID' , 'inner' );
		$this->db->join ( 'tOrderPropertyRoles', 'tOrders.OrderUID = tOrderPropertyRoles.OrderUID' , 'inner' );
		$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
		$this->db->join ( 'mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID' , 'left' );
		$this->db->join ( 'mTemplates', 'tOrders.TemplateUID = mTemplates.TemplateUID' , 'left' );
		$this->db->join ( 'mOrderTypes', 'torderabstractor.OrderTypeUID = mOrderTypes.OrderTypeUID' , 'left' );
		$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
		//$this->db->where( 'torderassignment.AssignedToUserUID', $loggedid);
		$this->db->where( 'torderabstractor.WorkflowStatus ', 5 );
		$this->db->order_by('torderabstractor.AbstractorOrderUID', 'DESC');
		$query = $this->db->get();
		return $query->result();
	}

	function GetAbstractorOrderDetailsByOrderUID($OrderUID)
	{
		$loggedid = $this->session->userdata('UserUID');
		$mabstractor=$this->db->get_where('mabstractor', array('UserUID'=>$loggedid))->row();
		$AbstractorUID=$mabstractor->AbstractorUID;
		$this->db->select("*, torderabstractor.AbstractorActualFee,torderabstractor.AbstractorFee,torderabstractor.AbstractorAdditionalFee,torderabstractor.AbstractorCopyCost,torderabstractor.OperatorType, mabstractor.*", false);
		$this->db->from ( 'torderassignment' );
		$this->db->join ( 'torderabstractor', 'torderassignment.OrderUID = torderabstractor.OrderUID' , 'inner' );
		$this->db->join ( 'mabstractor', 'torderabstractor.AbstractorUID = mabstractor.AbstractorUID' , 'inner' );
		$this->db->join ( 'tOrders', 'torderabstractor.OrderUID = tOrders.OrderUID' , 'inner' );
		$this->db->join ( 'tOrderPropertyRoles', 'tOrders.OrderUID = tOrderPropertyRoles.OrderUID' , 'inner' );
		$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
		$this->db->join ( 'mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID' , 'left' );
		$this->db->join ( 'mTemplates', 'tOrders.TemplateUID = mTemplates.TemplateUID' , 'left' );
		$this->db->join ( 'mOrderTypes', 'torderabstractor.OrderTypeUID = mOrderTypes.OrderTypeUID' , 'left' );
		$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
		$this->db->where( 'torderabstractor.OrderUID',$OrderUID);
		$this->db->where( 'torderassignment.AssignedToUserUID', $loggedid);
		$this->db->where( 'torderassignment.WorkflowModuleUID', 1);
		$this->db->where( 'torderassignment.WorkflowStatus <>', 5 );
		$query = $this->db->get();
		return $query->row();
	}

	function GetCompletedAbstractorOrderDetailsByOrderUID($OrderUID)
	{
		$loggedid = $this->session->userdata('UserUID');
		$mabstractor=$this->db->get_where('mabstractor', array('UserUID'=>$loggedid))->row();
		$AbstractorUID=$mabstractor->AbstractorUID;
		$this->db->select("*, torderabstractor.AbstractorActualFee,torderabstractor.AbstractorFee,torderabstractor.AbstractorAdditionalFee,torderabstractor.AbstractorCopyCost,torderabstractor.OperatorType, mabstractor.*", false);
		$this->db->from ( 'torderabstractor' );
		$this->db->join ( 'torderassignment', 'torderabstractor.OrderUID = torderassignment.OrderUID' , 'left' );
		$this->db->join ( 'mabstractor', 'torderabstractor.AbstractorUID = mabstractor.AbstractorUID' , 'left' );
		$this->db->join ( 'tOrders', 'torderabstractor.OrderUID = tOrders.OrderUID' , 'left' );
		$this->db->join ( 'tOrderPropertyRoles', 'tOrders.OrderUID = tOrderPropertyRoles.OrderUID' , 'left' );
		$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
		$this->db->join ( 'mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID' , 'left' );
		$this->db->join ( 'mTemplates', 'tOrders.TemplateUID = mTemplates.TemplateUID' , 'left' );
		$this->db->join ( 'mOrderTypes', 'torderabstractor.OrderTypeUID = mOrderTypes.OrderTypeUID' , 'left' );
		$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
		$this->db->where( 'torderabstractor.OrderUID',$OrderUID);
		//$this->db->where( 'torderabstractor.DocumentReceived',0);
		$this->db->where( 'torderabstractor.AbstractorUID',$AbstractorUID);
		$this->db->where( 'torderabstractor.OrderStatus', 5 );
		//$this->db->where( 'torderabstractor.CompletedDateTime<>','0000-00-00 00:00:00');
		//$this->db->where( 'torderabstractor.OrderStatus<>',5, false);
		//$this->db->where( 'torderassignment.WorkflowModuleUID', 1);
		//$this->db->where( 'torderabstractor.IsOrderComplete', 1 );
		//$this->db->where( 'torderassignment.WorkflowStatus <>', 5 );
		//$this->db->where( 'torderassignment.OrderFlag <>', 2 );
		//$this->db->group_by('torderassignment.OrderUID');
		$this->db->order_by('tOrders.OrderNumber', 'DESC');
		$query = $this->db->get();
		return $query->row();
	}

	function GetCancelledAbstractorOrderDetailsByOrderUID($OrderUID)
	{
		$loggedid = $this->session->userdata('UserUID');
		$mabstractor=$this->db->get_where('mabstractor', array('UserUID'=>$loggedid))->row();
		$AbstractorUID=$mabstractor->AbstractorUID;
		$this->db->select("*,torderabstractorunassign.AbstractorActualFee,torderabstractorunassign.AbstractorFee,torderabstractorunassign.AbstractorAdditionalFee,torderabstractorunassign.AbstractorCopyCost,torderabstractorunassign.OperatorType, mabstractor.*");
		$this->db->from ( 'torderabstractorunassign' );
		$this->db->join ( 'torderassignment', 'torderabstractorunassign.OrderUID = torderassignment.OrderUID' , 'left' );
		$this->db->join ( 'mabstractor', 'torderabstractorunassign.AbstractorUID = mabstractor.AbstractorUID' , 'left' );
		$this->db->join ( 'tOrders', 'torderabstractorunassign.OrderUID = tOrders.OrderUID' , 'left' );
		$this->db->join ( 'tOrderPropertyRoles', 'tOrders.OrderUID = tOrderPropertyRoles.OrderUID' , 'left' );
		$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
		$this->db->join ( 'mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID' , 'left' );
		$this->db->join ( 'mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID' , 'left' );
		$this->db->join ( 'mTemplates', 'tOrders.TemplateUID = mTemplates.TemplateUID' , 'left' );
		$this->db->join ( 'mOrderTypes', 'torderabstractorunassign.OrderTypeUID = mOrderTypes.OrderTypeUID' , 'left' );
		$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
		$this->db->where( 'torderabstractorunassign.OrderUID',$OrderUID);
		$this->db->where( 'torderabstractorunassign.AbstractorUID',$AbstractorUID);
		$this->db->order_by('tOrders.OrderNumber', 'DESC');
		$query = $this->db->get();
		return $query->row();
	}

	function IsAbstractorFeePendingForApproval($OrderUID)
	{
		$FeeCount=$this->db->query("SELECT * FROM torderabstractor
			WHERE OrderUID=" . $OrderUID . " AND (ApprovalStatus=1 OR ApprovalStatus=2)");
		return $FeeCount->num_rows();
	}

	function GetAPIWorkflow($OrderUID,$DynamicWorkflow){
		$this->db->select("*");
		$this->db->from('tOrderWorkflow');
		$this->db->where(array("OrderUID"=>$OrderUID,"WorkflowUID"=>$DynamicWorkflow));
		$query = $this->db->get();
		return $query->row();
	}

	function CheckIsDynamicProduct($ProductUID){

		$this->db->select("*");
		$this->db->from('mProducts');
		$this->db->where(array("ProductUID"=>$ProductUID));
		$query = $this->db->get();
		return $query->row();
	}

	function Page_Tourtips($page)
	{
		$this->db->where('TipPage',$page);
		$this->db->order_by('TipPosition','ASC');
		$q = $this->db->get('tourtipdetails');
		return $q->result();
	}

	function GetExceptionList($OrderUID){
		$this->db->select("*");
		$this->db->from('texceptions');
		$this->db->join ( 'mexceptions', 'mexceptions.ExceptionUID = texceptions.ExceptionUID' , 'left' );
		$this->db->join ( 'musers', 'musers.UserUID = texceptions.RaisedByUserUID' , 'left' );
		$this->db->where(array("texceptions.OrderUID"=>$OrderUID));
		$query = $this->db->get();
		return $query->result();
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

	function CheckPasswordPolicy()
	{
		$this->db->select_max("PasswordPolicy");
		$this->db->limit(1);
		$q = $this->db->get('morganizations')->row();
		return $q->PasswordPolicy;
	}

	function GetProductUIDBySubProductUID($SubProductUID){

		$this->db->select("*");
		$this->db->from('mSubProducts');
		$this->db->where(array("mSubProducts.SubProductUID"=>$SubProductUID));
		$query = $this->db->get();
		return $query->row();

	}

	function Verify_Password($UserUID, $Password)
	{
		$query = $this->db->query("SELECT * FROM musers WHERE UserUID = '$UserUID' AND Password = '$Password'");
		if($query->num_rows() >0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	function GetWorkflowModuleBy_UserUID_OrderUID($loggedid,$OrderUID)
	{

		if($loggedid != ''){
			$this->db->select ('WorkflowModuleUID');
			$this->db->from ('torderassignment');
			$this->db->where ('AssignedToUserUID',$loggedid);
			$this->db->where ('OrderUID',$OrderUID);
			$query = $this->db->get();
			$result = $query->result();
			foreach ($result as $data) {
				$WorkflowModuleUID[] = $data->WorkflowModuleUID;
			}

			return $WorkflowModuleUID;
		}


	}

	function get_workflow_id($MenuURL)
	{

		$this->db->select ( '*' );
		$this->db->from ( 'msubmenuworkflowmodules' );
		$this->db->where ('MenuURL',$MenuURL);
		$query = $this->db->get();
		return  $query->row();
	}


	function GetTaxInstallmentDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mtaxcertinstallments');
		return $query->result();
	}

	function GetCompleteDetailsStatusByID($OrderUID,$UserUID)
	{
		/*$query = $this->db->query("SELECT * FROM torderassignment
		LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID
		WHERE torderassignment.WorkflowModuleUID
		IN (1,2,3) AND torderassignment.OrderUID = '$OrderUID' AND torderassignment.AssignedToUserUID = '$UserUID';");
		return $query->result();*/

		$query = $this->db->query("SELECT MAX(CompleteDateTime) AS CompleteDateTime FROM torderassignment
			LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID
			WHERE torderassignment.WorkflowModuleUID
			IN (1,2,3) AND torderassignment.OrderUID = '$OrderUID' AND torderassignment.AssignedToUserUID = '$UserUID';");
		return $query->row();
	}

	function GetTenancyDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mtenancytype');
		return $query->result();
	}

	function GetRoleTypeDetails($RoleUID)
	{
		$this->db->select("*");
		$this->db->from('mroletype');
		$this->db->where(array("mroletype.RoleTypeUID"=>$RoleUID,"Active"=>1));
		$query = $this->db->get();
		return $query->row();
	}

	function GetOrderNumberByOrderUID($OrderUID)
	{
		$this->db->select("*");
		$this->db->from('tOrders');
		$this->db->where(array("tOrders.OrderUID"=>$OrderUID));
		$query = $this->db->get();
		return $query->row();
	}

	function GetCustomerByUserUID($UserUID)
	{
		$this->db->select("CustomerUID");
		$this->db->from('musers');
		$this->db->where(array("UserUID"=>$UserUID));
		$query = $this->db->get();
		return $query->row();
	}

	function Get_ExceptionsList()
	{
		$query = $this->db->get('mexceptions');
		return $query->result();
	}

	function GetOrderstatus($OrderUID){

		$this->db->where(array("StatusUID"=>20,"OrderUID"=>$OrderUID));
		$query = $this->db->get('tOrders');
		return $query->row();
	}

	function GetOrderAbstractorDetails($OrderUID){

		$query = $this->db->query("SELECT EXISTS(SELECT * FROM torderabstractor WHERE OrderUID = '$OrderUID') as CheckFeePricing;
			");
		return $query->row();
	}

	function GetActiveAbstractors()
	{
		$this->db->select('AbstractorUID,AbstractorCompanyName,AbstractorNo, AbstractorFirstName, AbstractorLastName');
		$this->db->where('AbstractorStatus','Active');
		return $this->db->get('mabstractor')->result();
	}

	function GetHolidays() {
		$query = $this->db->get('mholidaylist');
		return $query->result();
	}

	function GetCustomerPricingDetails(){
		$this->db->where_in('mpricing.PricingType',array('C','RM','GRADE'));
		$query = $this->db->get('mpricing');
		return $query->result();

	}

	function GetSubproductByProduct($ProductUID)
	{
		$this->db->select("*");
		$this->db->from('mSubProducts');
		$this->db->where(array("ProductUID"=>$ProductUID));
		$query = $this->db->get();
		return $query->result();
	}

	function GetGroupDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mgroups');
		return $query->result();
	}

	function GetVendorDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mvendors');
		return $query->result();
	}
	/**
	*@author Jainulabdeen
	*@purpose get Order Assignment Types
	*/
	function GettOrderTypeDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mOrderTypes');
		return $query->result();
	}
	function GetVendorsDetails(){

		$query = $this->db->get('mabstractor');
		return $query->result();
	}
	function GetVendorPricingDetails(){

		$this->db->where(array("PricingType"=>'V'));
		$query = $this->db->get('mpricing');
		return $query->result();
	}

	function GetTAtOrders($TATtiming,$PriorityUID)
	{
		$sql = "select GROUP_CONCAT(CASE WHEN (TIME_FORMAT(TIMEDIFF(NOW(),OrderEntryDatetime),'%H')>$TATtiming AND StatusUID<>100) THEN OrderUID END) AS TATUID from tOrders WHERE PriorityUID = $PriorityUID";
		return $this->db->query($sql)->row();
	}
	function GetCustomerDelayByOrder($OrderUID)
	{
		$this->db->where('OrderUID',$OrderUID);
		$this->db->where("CustomerDelayStopTime","0000-00-00 00:00:00");
		$q = $this->db->get("mcustomerdelay");
		if($q->num_rows()>0)
		{
			return 1;
		} else {
			return 0;
		}
	}
	function GetCustomerCustomerPricingListDetails(){

		$this->db->select("*");
		$this->db->from('mpricingproducts');
		$this->db->join('mpricing','mpricingproducts.PricingUID=mpricing.PricingUID','left');
		$this->db->join('mCounties','mpricingproducts.CountyUID=mCounties.CountyUID','left');
		$this->db->join('mStates','mpricingproducts.StateUID=mStates.StateUID','left');
		$this->db->join('mSubProducts','mpricingproducts.SubProductUID=mSubProducts.SubProductUID','left');
		$this->db->where_in('mpricing.PricingType',array('C','RM','GRADE'));
		$query = $this->db->get();
		return $query->result();
	}

	function GetVendorVendorPricingListDetails(){

		$this->db->select("*");
		$this->db->from('mpricingproducts');
		$this->db->join('mpricing','mpricingproducts.PricingUID=mpricing.PricingUID','left');
		$this->db->join('mCounties','mpricingproducts.CountyUID=mCounties.CountyUID','left');
		$this->db->join('mStates','mpricingproducts.StateUID=mStates.StateUID','left');
		$this->db->join('mSubProducts','mpricingproducts.SubProductUID=mSubProducts.SubProductUID','left');
		$this->db->where(array("mpricing.PricingType"=>'V'));
		$query = $this->db->get();
		return $query->result();
	}


	function GetWorkflowDetaiils(){

		// $this->db->select("*");
		// $this->db->from('mworkflowmodules');
		$this->db->where(array("mworkflowmodules.WorkflowModuleUID !="=>'5'));
		$query = $this->db->get('mworkflowmodules');
		return $query->result();
	}


	function GetStateDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mStates');
		return $query->result();
	}


	function GetStatebyAbstractorAssignment($AbstractorAssignment){

		$this->db->where(array("Active"=>1, 'AbstractorAssignment'=>$AbstractorAssignment));
		$query = $this->db->get('mStates');
		return $query->result();
	}

	function GetCityDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mCities');
		return $query->result();
	}

	function GetCountyDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mCounties');
		return $query->result();
	}

	function GetCountryDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mCounties');
		return $query->result();
	}

	function GetCountiesDetails() {

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mCounties');
		return $query->result();
	}




	function GetProductDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mProducts');
		return $query->result();
	}

	function GetSub_productDetails($ProductUID = ''){

		if($ProductUID != ''){
			$this->db->where(array("Active"=>1,"ProductUID" => $ProductUID));
		}else{
			$this->db->where(array("Active"=>1));
		}

		$query = $this->db->get('mSubProducts');
		return $query->result();
	}

	function Get_SubProduct(){

		return $this->db->select('*')->from('mSubProducts')->get()->result();
		
	}


	function GetPriorityDetails()
	{
		$query = $this->db->query("SELECT * FROM mOrderPriority WHERE Active = 1 ORDER BY FIELD(PriorityUID, 1,3,2)");
		return $query->result();
	}

	// function GetInsuranceType()
	// {
	// 	return $this->db->select('*')->from('mInsuranceType')->get()->result();
	// }

	function GetTemplateDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mTemplates');
		return $query->result();
	}

	/**
	* Function get service type details
	* @author D.Samuel Prabhu
	* @since  18 March 2020
	*/
	// function GetServiceTypeDetails(){

	// 	$this->db->where(array("Active"=>1));
	// 	$query = $this->db->get('mServiceType');
	// 	return $query->result();
	// }

	function GetCustomerDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mCustomers');
		return $query->result();
	}



	/**
	*@author Naveenkumar S
	*@purpose get subproducts
	*/
	function GetSubProducts(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mSubProducts');
		return $query->result();
	}


	/**
	*@author Naveenkumar S
	*@purpose get customer based on customer id
	*/
	function GetCustomerDetailsBasedOnID($CustomerUID){

		$this->db->where(array("Active"=>1,"CustomerUID"=>$CustomerUID));
		$query = $this->db->get('mCustomers');
		return $query->result();
	}

	/**
	*@author Naveenkumar S
	*@purpose get vendors.
	*/
	function GetVendors(){

		$this->db->where(array("OverAllStatus"=>"Active"));
		$query = $this->db->get('mabstractor');
		return $query->result();
	}


	function GetVendorUserDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mvendors');
		return $query->result();
	}

	function GetPropertyrolesDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mpropertyroles');
		return $query->result();
	}

	function GetUserDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('musers');
		return $query->result();
	}

	function GetUserDetailswithRoleName(){
		$this->db->select('musers.UserUID,musers.UserName,mroles.RoleName');
		$this->db->from("musers");
		$this->db->join("mroles","mroles.RoleUID = musers.RoleUID","LEFT");
		$this->db->where("musers.Active",1);
		$query = $this->db->get();
		return $query->result();
	}

	function GetOrderTypeDetails($post=NULL,$OrderTypes=NULL){

		//@author Naveenkumar @purpose Get order types based on vendor type.
		if(isset($post['RoleCategory']) && !empty($post['RoleCategory']))
		{
			if($post['RoleCategory'] == "Abstractor")
			{
				$RoleCategoryUID = 1;
			}elseif ($post['RoleCategory'] == "Attorney") {
				$RoleCategoryUID = 3;
			}else {
				$RoleCategoryUID = 2;
			}	
		}


		if(isset($post['RoleCategory']) && !empty($post['RoleCategory']))
		{
			$this->db->where("(RoleCategoryUID='' OR RoleCategoryUID IS NULL OR RoleCategoryUID like '%".$RoleCategoryUID."%')");
		}

		if(isset($post['RoleCategoryUID']) && !empty($post['RoleCategoryUID']))
		{	
			$orvalue = is_array($post['RoleCategoryUID']) ? "OR FIND_IN_SET(" . implode( ",RoleCategoryUID) OR FIND_IN_SET(", $post['RoleCategoryUID']) . ",RoleCategoryUID)" : " OR FIND_IN_SET({$post['RoleCategoryUID']},RoleCategoryUID)";

			$this->db->where("(RoleCategoryUID='' OR RoleCategoryUID IS NULL ".$orvalue.' )');
		}

		//@author praveen kumar ---
		if(isset($post['SubProductUID']) && !empty($post['SubProductUID']))
		{
			$this->db->where("(SubProductUIDs='' OR SubProductUIDs IS NULL OR FIND_IN_SET({$post['SubProductUID']},SubProductUIDs))");
		} // @Desc Changed from ProductUID to SubProductUIDs @Author Jainulabdeen @Updated Aug 31 2020
		if($OrderTypes){
			$this->db->where_in('OrderTypeUID',$OrderTypeUID);
		}

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mOrderTypes');
		return $query->result();
	}

	 //Deed Master data

	function GetEstateInterestDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mestateinterests');
		return $query->result();
	}

	function GetSubDocumentTypeDeedDetails(){

		$Deed = $this->config->item('DocumentTypeUID')['Deeds'];

		$this->db->where(array("Active"=>1, "DocumentCategoryUID"=> $Deed));
		$query = $this->db->get('mdocumenttypes');
		return $query->result();
	}

		//Mortgage Master data

	function GetSubDocumentTypeMortgageDetails(){

		$Mortgage = $this->config->item('DocumentTypeUID')['Mortgages'];
		$this->db->where(array("Active"=>1, "DocumentCategoryUID"=> $Mortgage));
		$query = $this->db->get('mdocumenttypes');
		return $query->result();
	}

	function GetLienTypeDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mlientypes');
		return $query->result();
	}

	function GetDBVTypeDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mmortgagedbvtypes');
		return $query->result();
	}

		// Property Info Master data

	function GetPropertyUseDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mpropertyuse');
		return $query->result();
	}

	function GetPropertyClassDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mpropertyclass');
		return $query->result();
	}

	function GetMaritalStatusDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mmaritalstatus');
		return $query->result();
	}

		// Tax Master data

	function GetTaxBasisDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mtaxcertbasis');
		return $query->result();
	}

	function GetTaxStatusDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mtaxstatus');
		return $query->result();
	}

	function GetTaxExemptionDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mtaxexemptions');
		return $query->result();
	}

	function GetTaxAuthorityDetails(){

		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mtaxauthority');
		return $query->result();
	}

	function GetSubDocumentTypeJudgmentDetails(){

		$Judgment = $this->config->item('DocumentTypeUID')['Judgment'];
		$this->db->where(array("Active"=>1, "DocumentTypeUID"=> $Judgment));
		$query = $this->db->get('morderpartytypes');
		return $query->result();
	}


	function GetPlaintiffDetails()
	{

		$Plaintiff = $this->config->item('PartyTypeUID')['Plaintiff'];
		$JudgementSNo=0;

		$this->db->select("*");
		$this->db->from('torderjudgementparties');
		$this->db->where(array("PartyTypeUID"=>$Plaintiff,"JudgementSNo" =>$JudgementSNo));
		$query = $this->db->get();
		return $query->result();
	}


	function GetDefendentDetails()
	{

		$Defendent = $this->config->item('PartyTypeUID')['Defendent'];

		$this->db->select("*");
		$this->db->from('torderjudgementparties');
		$this->db->where(array("PartyTypeUID"=>$Defendent,"JudgementSNo" =>0));
		$query = $this->db->get();
		return $query->result();
	}

	function formatSizeUnits($bytes)
	{
		if ($bytes >= 1073741824)
		{
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		}
		elseif ($bytes >= 1048576)
		{
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		}
		elseif ($bytes >= 1024)
		{
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		}
		elseif ($bytes > 1)
		{
			$bytes = $bytes . ' bytes';
		}
		elseif ($bytes == 1)
		{
			$bytes = $bytes . ' byte';
		}
		else
		{
			$bytes = '0 bytes';
		}

		return $bytes;
	}

	function getDocumentPath_IfExist($OrderUID)
	{

		$this->db->select ( 'tOrders.*');
		$this->db->from ( 'tOrders' );
		$this->db->where(array("tOrders.OrderUID"=>$OrderUID));
		$query = $this->db->get();
		$docpath = $query->result();

		if(file_exists(FCPATH.$docpath[0]->OrderDocsPath.$docpath[0]->OrderNumber.'_Search.pdf'))
		{
			$size = filesize(FCPATH.$docpath[0]->OrderDocsPath.$docpath[0]->OrderNumber.'_Search.pdf');
			$filesize = $this->common_model->formatSizeUnits($size);
			return '<td><a href="'. base_url("order_search/ViewDocument/".$OrderUID) .'" style="text-decoration: underline; color: blue;" id="searchpdf">Order Search</a></td><td>'.$filesize.'</td>';
		}
		else if(file_exists(FCPATH.$docpath[0]->OrderDocsPath.$docpath[0]->OrderNumber.'_Search.zip'))
		{
			$size = filesize(FCPATH.$docpath[0]->OrderDocsPath.$docpath[0]->OrderNumber.'_Search.zip');
			$filesize = $this->common_model->formatSizeUnits($size);
			return '<td><a href="'. base_url() . $docpath[0]->OrderDocsPath.$docpath[0]->OrderNumber.'_Search.zip' .'" style="text-decoration: underline; color: blue;" id="">Order Search</a></td><td>'.$filesize.'</td>';
		}
		else return '<td>No Search Package available</td><td>0KB</td>';

	}

	function get_orderdetails($OrderUID)
	{
		if($OrderUID){

			$this->db->select ( '*,tOrders.OrderUID,tOrders.AgentPricing AS Agent, tOrders.UnderWritingPricing AS UnderWriting, tOrders.InsuranceType AS InsuranceTypePrice,tOrderPropertyRoles.PRName AS Borrower_Name,mProducts.ProductUID,mSubProducts.RMS,mStates.StateUID','mSubProducts.SubProductUID');
			$this->db->from ( 'tOrders' );
			$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
			$this->db->join ( 'mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID' , 'left' );
			$this->db->join ( 'mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID' , 'left' );
			$this->db->join ( 'mTemplates', 'tOrders.TemplateUID = mTemplates.TemplateUID' , 'left' );
			$this->db->join ( 'mOrderTypes', 'mOrderTypes.OrderTypeUID = tOrders.OrderTypeUID' , 'left' );
			$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
			$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
			$this->db->join ( 'tOrderPropertyRoles', 'tOrders.OrderUID = tOrderPropertyRoles.OrderUID' , 'left' );
			$this->db->join ( 'mStates', 'tOrders.PropertyStateCode = mStates.StateCode' , 'left' );
			$this->db->where ('tOrders.OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->row();
		}

	}

	/*Case Sensitivity*/
	function get_CaseSen($OrderUID)
	{
		if($OrderUID){

			$this->db->select('mCustomerProducts.CaseSensitivity'); 
			$this->db->from('mCustomerProducts');
			$this->db->join ( 'tOrders', 'tOrders.CustomerUID = mCustomerProducts.CustomerUID AND tOrders.SubProductUID = mCustomerProducts.SubProductUID' , 'left' );
			$this->db->where ('tOrders.OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->row();
		}

	}
	/*End*/

	function GetMultiplePricing($ProductUID){

		$this->db->select("*");
		$this->db->from('mProducts');
		$this->db->where(array("mProducts.ProductUID"=>$ProductUID));
		$query = $this->db->get();
		return $query->row();

	}


	function GetCityByState($statedata,$county='') 
	{
		if(is_array($statedata))
		{
		  $this->db->where_in('StateUID',$statedata);	
		} else {
		  $this->db->where('StateUID',$statedata);	
		}

		if(!empty($county)) {
		  if(is_array($county))
		  {
			$this->db->where_in('CountyUID',$county);	
		  } else {
			$this->db->where('CountyUID',$county);	
		  }
		}
		return $this->db->get('mCities')->result();	
	}

	function hasCustomerProduct($CustomerUID,$ProductUID='',$SubProductUID='')  
	{
	   $this->db->where('CustomerUID',$CustomerUID);	
	   if(!empty($ProductUID))
	   {
	     $this->db->where('ProductUID',$ProductUID);	
	   }
	   if(!empty($SubProductUID))
	   {
	     $this->db->where('SubProductUID',$SubProductUID);	
	   }
	   return $this->db->get('mCustomerProducts')->num_rows();  	
	}

	function hasCounty($State,$CountyUID)
	{
	  $this->db->where('StateUID', $State);
	  $this->db->where('CountyUID', $CountyUID);
	  return $this->db->get('mCounties')->num_rows();
	}

	function hasCity($State, $CountyUID, $CityUID)
	{
	  $this->db->where('StateUID', $State);
	  if(!empty($CountyUID)) {
	   $this->db->where('CountyUID', $CountyUID);
	  }
	  $this->db->where('CityUID', $CityUID);
	  return $this->db->get('mCities')->num_rows();
	}

	function hasCityZipCode($State, $CountyUID, $CityUID, $ZipCode)
	{
	  $this->db->where('StateUID', $State);
	  if(!empty($cityUID)) {
	   $this->db->where('CityUID', $CityUID);
	  }
	  if(!empty($CountyUID)) {
	   $this->db->where('CountyUID', $CountyUID);
	  }
	  $this->db->where('ZipCode', $ZipCode);
	  return $this->db->get('mCities')->num_rows();
	}

	function GetCountyByState($statedata) {
		$query = $this->db->get_where('mCounties',$statedata);
		return $query->result();
	}

	function GetStatebyUID($stateUID) {
		$query = $this->db->get_where('mStates', array('StateUID' => $stateUID));
		return $query->result();
	}

	function GetStatebyCode($StateCode) {
		$query = $this->db->get_where('mStates', array('StateCode' => $StateCode));
		return $query->row();
	}

	function GetCitybyUID($cityUID) {
		$query = $this->db->get_where('mCities', array('CityUID' => $cityUID));
		return $query->result();
	}

	function GetCountybyUID($countyUID) {
		$query = $this->db->get_where('mCounties', array('CountyUID' => $countyUID));
		return $query->result();
	}


	function GetCityNamebyUID($countyUID) {
		$query = $this->db->get_where('mCities', array('CityUID' => $countyUID));
		return $query->result();
	}

	/**
		*Function getting settlementtype data by settlementtype uid
		*@author Anees M A <anees.ma@avanzegroup.com>
		*@return array
		*@since:19/08/2020
		*@LastModified:19/08/2020 
		*/
	function GetSettlementTypebyUID($settlementTypebyUID) {
		$query = $this->db->get_where('mSettlementType', array('SettlementTypeUID' => $settlementTypebyUID));
		return $query->row();
	}

	/**
		*Function getting property role data by propertyrole uid
		*@author Anees M A <anees.ma@avanzegroup.com>
		*@return array
		*@since:19/08/2020
		*@LastModified:19/08/2020 
		*/
	function GetPropertyRolebyUID($propertyRoleUID ) {
		$query = $this->db->get_where('mPropertyRoles', array('PropertyRoleUID' => $propertyRoleUID));
		return $query->row();
	}



	// function GetCustomerbyUID($countyUID) {
	// 	$query = $this->db->get_where('mCounties', array('CountyUID' => $countyUID));
	// 	return $query->result();
	// }


	function GettOrdersbyUID($orderUID) {
		$query = $this->db->get_where('tOrders', array('OrderUID' => $orderUID));
		return $query->result();
	}

	function GettOrdersby_UID($orderUID) {
		$query = $this->db->get_where('tOrders', array('OrderUID' => $orderUID));
		return $query->result();
	}

	function GettOrders($orderUID) {
		$query = $this->db->get_where('tOrders', array('OrderUID' => $orderUID));
		return $query->row();
	}

	function Chk_Duplicate_entry($data)
	{
		$q = $this->db->get_where('mvendors', $data);
		$record = $q->result();
		if(count($record)>0)
		{
			return 1;
		} else {
			return 0;
		}
	}

	function customer_exists($CustomerName)
	{
		$query = $this->db->get_where('mCustomers', array('CustomerName' => $CustomerName));
		return $query->num_rows();
	}

	function state_exist($StateName)
	{
		$query = $this->db->get_where('mStates', array('StateName' => $StateName));
		return $query->num_rows();
	}


	function GetAddressDetails($OrderUID)
	{
		if($OrderUID){

			$query = $this->db->query("SELECT
				CONCAT(`tOrders`.`PropertyAddress1` ,`tOrders`.`PropertyAddress2`,`tOrders`.`PropertyStateCode`,`tOrders`.`PropertyCountyName`,`tOrders`.`PropertyCityName` ,`tOrders`.`PropertyZipcode` ) Address
				FROM   `tOrders`
				WHERE OrderUID =$OrderUID;");
			return $query->row();
		}
	}

	function GetMapAddress($OrderUID)
	{
		if($OrderUID){

			$query = $this->db->query("SELECT
				CONCAT_WS(',',`tOrders`.`PropertyAddress1` ,`tOrders`.`PropertyAddress2`,`tOrders`.`PropertyStateCode`,`tOrders`.`PropertyCountyName`,`tOrders`.`PropertyCityName` ,`tOrders`.`PropertyZipcode` ) Address
				FROM   `tOrders`
				WHERE OrderUID =$OrderUID;");
			return $query->row();
		}
	}


	function GetMenuBarDetails($CustomerUID,$SubProductUID,$OrderUID)
	{
		$query = $this->db->query("SELECT DISTINCT * FROM mMenu
			INNER JOIN (
			SELECT * FROM mcustomerworkflowmodules
			UNION ALL
			SELECT tOrders.CustomerUID, torderoptionalworkflows.WorkflowModuleUID, 1, tOrders.SubProductUID	FROM tOrders
			INNER JOIN torderoptionalworkflows ON torderoptionalworkflows.OrderUID = tOrders.OrderUID
			AND torderoptionalworkflows.SubproductUID = tOrders.SubProductUID
			WHERE	tOrders.OrderUID = '".$OrderUID."'
			) as customerworkflows
			ON customerworkflows.WorkflowModuleUID = mMenu.WorkflowModuleUID
			WHERE
			/*mMenu.WorkflowModuleUID IN (1, 2, 3,)
									AND*/ 
			customerworkflows.CustomerUID = '".$CustomerUID."'
			AND customerworkflows.SubProductUID = '".$SubProductUID."'
			ORDER BY `MenuPosition`");
		$result=$query->result();
	     $final=[];
	   
      if(in_array($this->session->userdata('RoleType'), array(1,2,3,4,5,6,7))) {
		if(in_array($result->WorkflowModuleUID, 3)){
		  $final = $result;		 
		}
		  else{
	          $this->db->select('*');
	          $this->db->from('tOrders');
	          $this->db->where('OrderUID',$OrderUID);
	          $this->db->where('IsTaxcert','1');
	          $data=$this->db->get()->row();
         
          if($data){
	          $this->db->select('*');
	          $this->db->from('mMenu');
	          $this->db->where('WorkflowModuleUID','3');
	          $taxdata=$this->db->get()->result();
	          $final=array_merge($result,$taxdata);       
	          
             
          }else{
          	 $final = $result;
          }
      }

       return  $finalresult = (object) $final;
		 }else{
		 	return $result;
		 }

	
	
	
	
	}

	function GetOptionalWorkflowModules($CustomerUID, $SubProductUID, $OrderUID)
	{
		$statusuids=implode(',', array(
										$this->config->item('keywords')['Order Completed'],
										$this->config->item('keywords')['Cancelled'],
										$this->config->item('keywords')['Billed']
									));

		return $this->db->query("SELECT * FROM	mcustomeroptionalworkflowmodules
									LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID=mcustomeroptionalworkflowmodules.WorkflowModuleUID
									WHERE mcustomeroptionalworkflowmodules.WorkflowModuleUID NOT IN (
											SELECT WorkflowModuleUID
											FROM torderoptionalworkflows
											INNER JOIN tOrders ON tOrders.OrderUID=torderoptionalworkflows.OrderUID
											WHERE torderoptionalworkflows.OrderUID = ".$OrderUID." AND torderoptionalworkflows.SubProductUID = ".$SubProductUID.") AND mcustomeroptionalworkflowmodules.CustomerUID=".$CustomerUID." AND mcustomeroptionalworkflowmodules.SubProductUID=".$SubProductUID."
										ORDER BY mcustomeroptionalworkflowmodules.WorkflowModuleUID")->result();
	}

	function GetTopBarDetails($OrderUID)
	{
		/*$query = $this->db->query("SELECT * FROM torderassignment
		LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID
		LEFT JOIN `mMenu` ON mMenu.WorkflowModuleUID = torderassignment.WorkflowModuleUID
		WHERE torderassignment.WorkflowModuleUID
		IN (1,2,3) AND torderassignment.OrderUID = '$OrderUID'
		ORDER BY `MenuPosition`;");

		return $query->result();*/


		$query = $this->db->query("SELECT * FROM mMenu
			INNER JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = mMenu.WorkflowModuleUID
			WHERE mMenu.WorkflowModuleUID
			IN (1,2,3)
			ORDER BY `MenuPosition`;");
		return $query->result();
	}


	function GetTopBarDetailsByID($OrderUID,$UserUID)
	{
		/*$query = $this->db->query("SELECT * FROM torderassignment
		LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID
		LEFT JOIN `mMenu` ON mMenu.WorkflowModuleUID = torderassignment.WorkflowModuleUID
		WHERE torderassignment.WorkflowModuleUID
		IN (1,2,3) AND torderassignment.OrderUID = '$OrderUID' AND torderassignment.AssignedToUserUID = '$UserUID'
		ORDER BY `MenuPosition`;");
		return $query->result();*/

		/* $query = $this->db->query("SELECT * FROM torderassignment
		LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID
		LEFT JOIN `mMenu` ON mMenu.WorkflowModuleUID = torderassignment.WorkflowModuleUID
		WHERE torderassignment.WorkflowModuleUID
		IN (1,2,3) AND torderassignment.OrderUID = '$OrderUID'
		ORDER BY `MenuPosition`;");

		return $query->result();*/


		$query = $this->db->query("SELECT * FROM mMenu
			INNER JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = mMenu.WorkflowModuleUID
			WHERE mMenu.WorkflowModuleUID
			IN (1,2,3)
			ORDER BY `MenuPosition`;");
		return $query->result();

	}



	function GetCompleteDetails($OrderUID)
	{
		$query = $this->db->query("SELECT * FROM torderassignment
			LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID
			LEFT JOIN `msubmenuworkflowmodules` ON msubmenuworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID
			WHERE torderassignment.WorkflowModuleUID
			IN (1,2,3) AND torderassignment.OrderUID = '$OrderUID'
			ORDER BY `MenuPosition`;");

		return $query->result();
	}

	function GetCompleteDetailsByID($OrderUID,$UserUID)
	{
		$query = $this->db->query("SELECT * FROM torderassignment
			LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID
			LEFT JOIN `msubmenuworkflowmodules` ON msubmenuworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID
			WHERE torderassignment.WorkflowModuleUID
			IN (1,2,3) AND torderassignment.OrderUID = '$OrderUID' AND torderassignment.AssignedToUserUID = '$UserUID'
			ORDER BY `MenuPosition`;");

		return $query->result();
	}

	function GetOrderByID($OrderUID)
	{

		$query = $this->db->query("SELECT EXISTS(SELECT OrderUID FROM tOrders WHERE OrderUID = '$OrderUID') as OrderUID;
			");

		return $query->row();
	}


	function GetOrderByOrderNumber($OrderNumber)
	{

		$query = $this->db->query("SELECT OrderUID FROM tOrders WHERE OrderNumber = '$OrderNumber'");

		return $query->row();
	}

	function GetOrderByAbstractorOrderUID($AbstractorOrderUID)
	{

		$query = $this->db->query("SELECT EXISTS(SELECT OrderUID FROM torderabstractor WHERE AbstractorOrderUID = '$AbstractorOrderUID') as IsExist;
			");

		return $query->row();
	}

	function GetWorkflowstatusByID($OrderUID,$UserUID)
	{

		/* $query = $this->db->query("SELECT COUNT(*) as  Workflowstatus FROM `torderassignment` WHERE WorkflowStatus = 5 AND OrderUID = '$OrderUID' AND AssignedToUserUID = '$UserUID' AND WorkflowModuleUID IN (1,2,3) ");*/

		$query = $this->db->query("SELECT COUNT(*) as  Workflowstatus FROM `torderassignment` WHERE WorkflowStatus = 5 AND OrderUID = '$OrderUID' AND AssignedToUserUID = '$UserUID' AND WorkflowModuleUID IN (1,2,3) ");

		return $query->row();
	}

	function GetTotalWorkflowByID($OrderUID,$UserUID)
	{

		$query = $this->db->query("SELECT COUNT(*) as  TotalWorkflow FROM `torderassignment` WHERE  OrderUID = '$OrderUID' AND AssignedToUserUID = '$UserUID' AND WorkflowModuleUID IN (1,2,3) ");

		return $query->row();
	}


	function GetWorkflowstatus($OrderUID,$UserUID)
	{

		$query = $this->db->query("SELECT COUNT(*) as  Workflowstatus FROM `torderassignment` WHERE WorkflowStatus = 5 AND OrderUID = '$OrderUID' AND AssignedToUserUID = '$UserUID' AND WorkflowModuleUID IN (1,2,3) ");

		return $query->row();
	}

	function GetTotalWorkflow($OrderUID,$UserUID)
	{

		$query = $this->db->query("SELECT COUNT(*) as  TotalWorkflow FROM `torderassignment` WHERE  OrderUID = '$OrderUID' AND AssignedToUserUID = '$UserUID' AND WorkflowModuleUID IN (1,2,3) ");

		return $query->row();
	}



	function GetCountyByStateUID($StateUID) {

		if(is_array($StateUID))
		{
		  $this->db->where_in('StateUID', $StateUID);
		  return $this->db->get('mCounties')->result();	
		} else {
	  	  $query = $this->db->get_where('mCounties', array('StateUID' => $StateUID));
	  	  return $query->result();
		}
	}


	function GetCityByStateUID($StateUID) {

		$query = $this->db->get_where('mCities', array('StateUID' => $StateUID));

		return $query->result();

	}

	function GetCityByStateCountyUID($StateUID,$CountyUID) {

		$query = $this->db->get_where('mCities', array('StateUID' => $StateUID,'CountyUID' => $CountyUID));

		return $query->result();

	}


	function GetZipcodeByStateCountyUID($StateUID,$CountyUID) 
	{
		if(is_array($statedata))
		{
		  $this->db->where_in('StateUID',$statedata);	
		} else {
		  $this->db->where('StateUID',$statedata);	
		}
		if(is_array($county))
		{
		  $this->db->where_in('CountyUID',$county);	
		} else {
		  $this->db->where('CountyUID',$county);	
		}
		return $this->db->get('mCities')->result();
	}


	function GetZipcodeByStateCountyCityUID($CityUID, $StateUID=NULL, $CountyUID=NULL) {

		$filter = [];
		if (!empty($CityUID)) {
			$filter['CityUID'] = $CityUID;
		}
		if (!empty($StateUID)) {
			$filter['StateUID'] = $StateUID;			
		}
		if (!empty($CountyUID)) {
			$filter['CountyUID'] = $CountyUID;
		}
		$query = $this->db->get_where('mCities', $filter);


		return $query->result();

	}



	function GetZipcodeByStateUID($StateUID) {

		if(is_array($StateUID))
		{
		  $this->db->where_in('StateUID', $StateUID);	
		  return $this->db->get('mCities')->result();	
		} else {
		  $query = $this->db->get_where('mCities', array('StateUID' => $StateUID));
		  return $query->result();
		}

	}



	function isLogin(){

		if($this->session->userdata('scope_logged_in') != "1")
		{
			redirect(base_url()."login/logout");
		}
	}

	function get_roles($RoleUID)
	{
		$this->db->select ( '*' );
		$this->db->from ( 'mroles' );

		$this->db->where ('mroles.RoleUID',$RoleUID);

		$query = $this->db->get();

		return $query->result_array();

	}

	function addlastviewedOrder($OrderUID,$UserUID)
	{
		$OrderUID = rtrim($OrderUID, ',');

		$this->db->where('UserUID',$UserUID);

		$query = $this->db->get('tlastviewedorders');

		$datas = $query->row_array();

		$add_data = $this->json_conversion_lastviewedorders($datas,$OrderUID);

		$add_data = rtrim($add_data, ',');


		$add_datas = array('OrderUID'=>$add_data,'UserUID'=>$UserUID);



		// $tLastViewedOrders = json_encode($query->result_array());
		// echo '<pre>';print_r($query->result_array());exit;

		if ( $query->num_rows() > 0 )
		{
			$this->db->where('UserUID',$UserUID);
			$this->db->update('tlastviewedorders',$add_datas);
		} else {
			// $this->db->set('UserUID', $UserUID);
			$this->db->insert('tlastviewedorders',$add_datas);
		}
	}


	function json_conversion_lastviewedorders($datas,$OrderUID)
	{


		if(count($datas) > 0){

			$OrderUIDs = $datas['OrderUID'];

		}else{

			$OrderUIDs = array();

		}



		if(count($OrderUIDs) > 0){

			$OrderUIDs_array =  explode(',', $OrderUIDs);


			if(in_array($OrderUID,$OrderUIDs_array)){

				unset($OrderUIDs_array[array_search($OrderUID, $OrderUIDs_array)]);

				array_unshift($OrderUIDs_array,$OrderUID);

			}else{

				array_unshift($OrderUIDs_array,$OrderUID);
			}

			$Orders = implode(",", $OrderUIDs_array);

		}else{
			$Orders = $OrderUID;
		}


		return $Orders;

	}


	function GetassignmentTAtOrders($TATtiming,$PriorityUID,$OrderUID)

	{
		$sql = "select TIME_FORMAT(TIMEDIFF(NOW(),OrderEntryDatetime),'%H')>$TATtiming AND StatusUID<>100 AS TATUID from tOrders WHERE PriorityUID = $PriorityUID AND OrderUID = $OrderUID";

		return $this->db->query($sql)->row();
	}


	function cal_due_date($assigneddate,$date)
	{


	// $date = date("Y-m-d H:i:s", strtotime('+'.$date.' hours', strtotime($assigneddate)));

		$addhours = '+'.$date.' hours';
		$datetime = date("Y-m-d H:i:s", strtotime($addhours, strtotime($assigneddate)));


		return $datetime;

	}


	function get_prioritytime($PriorityUID){
		$this->db->select ( '*' );
		$this->db->from ( 'mOrderPriority' );
		$this->db->where ('PriorityUID',$PriorityUID);
		$query = $this->db->get();
		return  $query->row();
	}


	function GetCount()
	{
		$query = $this->db->query("SELECT COUNT(*) AS COUNT FROM tordercancel WHERE CancelStatus ='0' ");
		$res =  $query->row();
		return $res->COUNT;
	}

	function Get_sections(){

		$query = $this->db->get('mreportsections');
		return $query->result();
	}


	function get_onholdWorkflow($OrderUID){

		$loggedid = $this->session->userdata('UserUID');
		$this->db->select ( 'Group_concat(WorkflowModuleName) as WorkflowModuleName,WorkflowStatus' );
		$this->db->from ( 'torderassignment' );
		$this->db->join ( 'mworkflowmodules', 'mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID' , 'inner' );
	//$this->db->where('torderassignment.AssignedToUserUID',$loggedid);
		$this->db->where('torderassignment.WorkflowStatus',4);
		$this->db->where('torderassignment.OrderUID',$OrderUID);

		$query = $this->db->get();
		$res =  $query->row();

		return $res;
	}

	function GetCustomerDelayOrderQueue()
	{
		$this->RoleUID = $this->session->userdata('RoleUID');
		$query = $this->db->query("SELECT CustomerDelayOrderQueue FROM `mroles` WHERE RoleUID = '".$this->RoleUID."' ");
		$res =  $query->row();
		return $res->CustomerDelayOrderQueue;
	}

	function GetOnHoldOrderQueue()
	{
		$this->RoleUID = $this->session->userdata('RoleUID');
		$query = $this->db->query("SELECT OnHoldOrderQueue FROM `mroles` WHERE RoleUID = '".$this->RoleUID."' ");
		$res =  $query->row();
		return $res->OnHoldOrderQueue;
	}

	function GetOnHoldQueue()
	{
		$this->RoleUID = $this->session->userdata('RoleUID');
		$query = $this->db->query("SELECT OnHoldQueue FROM `mroles` WHERE RoleUID = '".$this->RoleUID."' ");
		$res =  $query->row();
		return $res->OnHoldQueue;
	}

	function GetMyOrdersQueue()
	{
		$this->RoleUID = $this->session->userdata('RoleUID');
		$query = $this->db->query("SELECT MyOrdersQueue FROM `mroles` WHERE RoleUID = '".$this->RoleUID."' ");
		$res =  $query->row();
		return $res->MyOrdersQueue;
	}

	function GetExceptionQueue()
	{
		$this->RoleUID = $this->session->userdata('RoleUID');
		$query = $this->db->query("SELECT ExceptionQueue FROM `mroles` WHERE RoleUID = '".$this->RoleUID."' ");
		$res =  $query->row();
		return $res->ExceptionQueue;
	}

	function GetCompletedQueue()
	{
		$this->RoleUID = $this->session->userdata('RoleUID');
		$query = $this->db->query("SELECT CompletedQueue FROM `mroles` WHERE RoleUID = '".$this->RoleUID."' ");
		$res =  $query->row();
		return $res->CompletedQueue;
	}

	function GetReviewQueue()
	{
		$this->RoleUID = $this->session->userdata('RoleUID');
		$query = $this->db->query("SELECT ReviewQueue FROM `mroles` WHERE RoleUID = '".$this->RoleUID."' ");
		$res =  $query->row();
		return $res->ReviewQueue;
	}

	function GetCustomerDelayQueue()
	{
		$this->RoleUID = $this->session->userdata('RoleUID');
		$query = $this->db->query("SELECT CustomerDelayQueue FROM `mroles` WHERE RoleUID = '".$this->RoleUID."' ");
		$res =  $query->row();
		return $res->CustomerDelayQueue;
	}

	function GetCancellationQueue()
	{
		$this->RoleUID = $this->session->userdata('RoleUID');
		$query = $this->db->query("SELECT CancellationQueue FROM `mroles` WHERE RoleUID = '".$this->RoleUID."' ");
		$res =  $query->row();
		return $res->CancellationQueue;
	}

	function GetCancellationStatus($OrderUID = false)
	{
		if(isset($_GET['OrderUID'])){
			$OrderUID = rtrim($_GET['OrderUID'],'/');
		}elseif ( $this->uri->segment(3) != '') {
			$OrderUID = $this->uri->segment(3);
		}
		if(!empty($OrderUID)) {
			$query = $this->db->query("SELECT StatusUID FROM tOrders WHERE OrderUID = '$OrderUID' and StatusUID = '110' ");
			if($query->num_rows() > 0)
			{
				return '1';
			}
			else{
				return '0';
			}
		}else{
			return '0';
		}
	}

	function GetCompletedStatus($OrderUID = false)
	{

		if(isset($_GET['OrderUID'])){
			$OrderUID = rtrim($_GET['OrderUID'],'/');
		}elseif ( $this->uri->segment(3) != '') {
			$OrderUID = $this->uri->segment(3);
		}

		if(!empty($OrderUID)) {
			$query = $this->db->query("SELECT StatusUID FROM tOrders WHERE OrderUID = '$OrderUID' and StatusUID = '100' ");
			if($query->num_rows() > 0)
			{
				return '1';
			}
			else{
				return '0';
			}
		}else{
			return '0';
		}

	}

	function GetCompletedBilledOrders($OrderUID='')
	{
		if (!is_numeric($OrderUID)) {
			if(isset($_GET['OrderUID'])){
				$OrderUID = rtrim($_GET['OrderUID'],'/');
			}elseif ( $this->uri->segment(3) != '') {
				$OrderUID = $this->uri->segment(3);
			}
		}

		$query = $this->db->query("SELECT StatusUID FROM tOrders WHERE OrderUID = '$OrderUID' and StatusUID >= '100' ");
		if($query->num_rows() > 0)
		{
			return '1';
		}
		else{
			return '0';
		}

	}
	function get_prioritytime_for_duedate($CustomerUID,$SubProductUID,$PriorityUID){
		$this->db->select ( '*' );
		$this->db->from ( 'mcustomerproducttat' );
		$this->db->where ('PriorityUID',$PriorityUID);
		$this->db->where ('SubProductUID',$SubProductUID);
		$this->db->where ('CustomerUID',$CustomerUID);
		$query = $this->db->get();
		return  $query->row();
	}

	function get_morder_priority($PriorityUID){
		$this->db->select ( '*' );
		$this->db->from ( 'mOrderPriority' );
		$this->db->where ('PriorityUID',$PriorityUID);
		$query = $this->db->get();
		return  $query->row();
	}


	function get_holidays(){
		$this->db->select ( '*' );
		$this->db->from ( 'mholidaylist' );
		$query = $this->db->get();
		return  $query->result_array();
	}


	/*function Get_TaxAuthority_by_StateCounty($StateUID,$countyUID){
		$this->db->where(array("Active"=>1,"StateUID"=>$StateUID,"CountyUID"=>$countyUID));
		$query = $this->db->get('mtaxauthority');
		return $query->result();
	}*/

	function Get_TaxAuthority_by_StateCounty($PropertyStateCode,$PropertyCountyName)
	{

		$mStates=$this->db->get_where('mStates', array('StateCode' => $PropertyStateCode))->row();

		$mCounties=$this->db->get_where('mCounties', array('StateUID'=>$mStates->StateUID, 'CountyName'=>$PropertyCountyName))->row();

		if(!empty($mCounties) && !empty($mStates))
		{
			$this->db->where(array("Active"=>1,"StateUID"=>$mStates->StateUID,"CountyUID"=>$mCounties->CountyUID));
			$this->db->or_where('IsDefault', 1);
			$query = $this->db->get('mtaxauthority');
			return $query->result();
		}
		else
		{
			return [];
		}
	}

	function is_selfassign($loggedid){
		$is_selfassign = 0;
		$this->db->select ( '*' );
		$this->db->from ('musers');
		$this->db->where ('UserUID',$loggedid);
		$query = $this->db->get();
		$result = $query->row();
		if(count($result)>0){
			if($result->AutoAssign == '1'){
				$is_selfassign = 1;
			}
		}
		return $is_selfassign;
	}



	function time_ago($time)
	{
		$time_ago = strtotime($time);
		$current_time = time();
		$date=date('Y-m-d h:i:s');
	// echo '<pre>';print_r($date);exit;
		$time_difference = $current_time - $time_ago;
		$seconds = $time_difference;
		$minutes      = round($seconds / 60 );           // value 60 is seconds
		$hours           = round($seconds / 3600);           //value 3600 is 60 minutes * 60 sec
		$days          = round($seconds / 86400);          //86400 = 24 * 60 * 60;
		$weeks          = round($seconds / 604800);          // 7*24*60*60;
		$months          = round($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60
		$years          = round($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60
		if($seconds <= 60)
		{
			if($seconds<0){

			}else{
				return "$seconds sec";
			}

		}
		else if($minutes <=60)
		{
			if($minutes==1)
			{
				return "$minutes min";
			}
			else
			{
				if($minutes<1){
					return "$seconds seconds";
				} else {
					return "$minutes Min ";
				}
			}
		}
		else if($hours <=24)
		{
			if($hours==1)
			{
				return "1 Hrs ";
			}
			else
			{
				return "$hours hours";
			}
		}
		else if($days <= 7)
		{
			if($days==1)
			{
				return "$days days";
			}
			else
			{
				if($days<1){
					return "$hours hours";
				}
				else{
					return "$days Days ";
				}
			}
		}
		else if($weeks <= 4.3) //4.3 == 52/12
		{
			return "$days days";
		}
		else if($months <=12)
		{
			return "$days days ";

		}
		else
		{
			if($years==1)
			{
				return "1 Year";
			}
			else
			{

				return "$years Years";

			}
		}
	}



	function select_role_workflows(){

		$RoleUID = $this->session->userdata('RoleUID');

		$this->db->select('*');
		$this->db->where('RoleUID',$RoleUID);
		$query = $this->db->get('mroles');
		$result =  $query->row();


		$return_workflows = [];

		if($result->OrderSearch == '1'){
			$return_workflows['1'] =  'Order Search';
		}
		if($result->OrderTyping == '1'){
			$return_workflows['2'] =  'Order Typing';
		}
		if($result->OrderTaxCert == '1'){
			$return_workflows['3'] =  'Order TaxCert';
		}
		if($result->OrderReview == '1'){
			//$return_workflows['4'] =  'Order Review';
		}
		return $return_workflows;
	}


	function get_role_workflows(){

		$RoleUID = $this->session->userdata('RoleUID');

		$this->db->select('*');
		$this->db->where('RoleUID',$RoleUID);
		$query = $this->db->get('mroles');
		$result =  $query->row();


		$return_workflows = [];

		if($result->OrderSearch == '1'){
			$return_workflows[] =  '1';
		}
		if($result->OrderTyping == '1'){
			$return_workflows[] =  '2';
		}
		if($result->OrderTaxCert == '1'){
			$return_workflows[] =  '3';
		}
		if($result->OrderReview == '1'){
			//$return_workflows[] =  '4';
		}
		return $return_workflows;
	}


	function role_workflows(){

		$RoleUID = $this->session->userdata('RoleUID');

		$this->db->select('*');
		$this->db->where('RoleUID',$RoleUID);
		$query = $this->db->get('mroles');
		$result =  $query->row();
		return $result;
	}


	function Get_OrderBy_UserUID($loggedid){
		$this->db->select ('OrderUID as AssignOrderUID');
		$this->db->from ('torderassignment');
		$this->db->where ('AssignedToUserUID',$loggedid);
		$query = $this->db->get();
		$result = $query->result();
		foreach ($result as $data) {
			$OrderUID[] = $data->AssignOrderUID;
		}
		return $OrderUID;
	}

	function GetOrderstatusBy_OrderUID($OrderUID){
		$query = $this->db->query("SELECT b.StatusName FROM tOrders a, mOrderStatus b WHERE a.StatusUID = b.StatusUID and
			a.OrderUID ='".$OrderUID."' ");
		$result =  $query->result();
		$Status = '';
		foreach($result as $row){
			$Status = $row->StatusName;
		}
		return $Status;
	}

	function GetCustomerContactName($UserUID)
	{
		$this->db->select('*');
		$this->db->from ( 'mCustomers' );
		$this->db->join ( 'musers', 'musers.CustomerUID = mCustomers.CustomerUID' , 'left' );
		$this->db->where('musers.UserUID',$UserUID);
		$query = $this->db->get();
		$value = $query->row();
		if($value->CustomerUID == 12)
		{

			$this->db->select('CustomerPContactName');
			$this->db->from ( 'mCustomers' );
			$this->db->join ( 'musers', 'musers.CustomerUID = mCustomers.CustomerUID' , 'left' );
			$this->db->where('musers.UserUID',$UserUID);
			$query = $this->db->get();
			$value = $query->row();
			return $value->CustomerPContactName;
		}
		else{
			$this->db->select('UserName AS CustomerPContactName');
			$this->db->where('UserUID',$UserUID);
			$q = $this->db->get('musers')->row();
			return $q->CustomerPContactName;
		}
	}

//SideBar Functions
	function GetDocumentFileName($OrderUID)
	{
		$this->db->select('torderdocuments.*, tOrders.OrderDocsPath, tOrders.OrderNumber, mdocumenttypes.*, musers.UserName,torderdocuments.UploadedDate')->from('torderdocuments');
		$this->db->join('tOrders', 'tOrders.OrderUID=torderdocuments.OrderUID', 'left');
		$this->db->join('mdocumenttypes', 'mdocumenttypes.DocumentTypeUID=torderdocuments.DocumentTypeUID', 'left');
		$this->db->join('musers', 'musers.UserUID=torderdocuments.UploadedUserUID', 'left');
    $this->db->where('torderdocuments.TypeOfDocument <>','Screenshot');
		$this->db->where('torderdocuments.OrderUID', $OrderUID);
		$this->db->order_by('DisplayFileName');
		$query=$this->db->get();
		return $query->result();
	}


	function GetOrderMortgages($OrderUID)
	{
		$this->db->select('*')->from('tordermortgages');
		$this->db->join('mdocumenttypes', 'mdocumenttypes.DocumentTypeUID=tordermortgages.DocumentTypeUID', 'left');
		$this->db->where('tordermortgages.OrderUID', $OrderUID);
		$this->db->order_by('MortgageSNo');
		$query=$this->db->get();
		return $query->result();
	}


	function GetSideBarOrderDeeds($OrderUID)
	{
		$this->db->select('*')->from('torderdeeds');
		$this->db->join('mdocumenttypes', 'mdocumenttypes.DocumentTypeUID=torderdeeds.DocumentTypeUID', 'left');
		$this->db->where('torderdeeds.OrderUID', $OrderUID);
		$this->db->order_by('DeedSNo');
		$query=$this->db->get();
		return $query->result();
	}



	function GetSideBarOrderJudgements($OrderUID)
	{
		$this->db->select('*')->from('torderjudgements');
		$this->db->join('mdocumenttypes', 'mdocumenttypes.DocumentTypeUID=torderjudgements.DocumentTypeUID', 'left');
		$this->db->where('torderjudgements.OrderUID', $OrderUID);
		$this->db->order_by('JudgementSNo');
		$query=$this->db->get();
		return $query->result();
	}



	function GetSideBarOrderLiens($OrderUID)
	{
		$this->db->select('*')->from('torderleins');
		$this->db->join('mdocumenttypes', 'mdocumenttypes.DocumentTypeUID=torderleins.DocumentTypeUID', 'left');
		$this->db->where('torderleins.OrderUID', $OrderUID);
		$this->db->order_by('LeinSNo');
		$query=$this->db->get();
		return $query->result();
	}

	function GetSideBarOrderTaxes($OrderUID)
	{
		$this->db->select('*')->from('tordertaxcerts');
		$this->db->join('mdocumenttypes', 'mdocumenttypes.DocumentTypeUID=tordertaxcerts.DocumentTypeUID', 'left');
		$this->db->where('tordertaxcerts.OrderUID', $OrderUID);
		$this->db->order_by('TaxCertSNo');
		$query=$this->db->get();
		return $query->result();
	}


	function GetWorkflowModules($OrderUID)
	{
		$this->db->order_by('WorkflowModuleUId');
		$result = $this->db->get_where('torderassignment', array('OrderUID'=> $OrderUID));
		return $result->result();

	}

	function GettOrderPropertyRoles($OrderUID)
	{
		$this->db->select('tOrderPropertyRoles.*,  mpropertyroles.*')->from('tOrderPropertyRoles');
		$this->db->join('mpropertyroles','mpropertyroles.PropertyRoleUID=tOrderPropertyRoles.PropertyRoleUID', 'left');
		$this->db->where('tOrderPropertyRoles.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->result();

	}

	function GetCustomerInformationByOrderUID($OrderUID)
	{
		$this->db->select('mCustomers.*')->from('tOrders');
		$this->db->join('mCustomers', 'mCustomers.CustomerUID=tOrders.CustomerUID');
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->row();

	}


	function GetOrderDetailsbyUID($orderUID) {
		$this->db->select('tOrders.*, tOrders.PropertyCityName as CityName, tOrders.PropertyCountyName as CountyName, tOrders.PropertyStateCode as StateName, tOrders.PropertyStateCode as StateCode, mOrderTypes.OrderTypeName, mSubProducts.SubProductName,mProducts.ProductUID,mProducts.IsRMSProduct,mSubProducts.RMS')->from('tOrders');
		$this->db->join('mOrderTypes','mOrderTypes.OrderTypeUID=tOrders.OrderTypeUID', 'left');
		$this->db->join('mSubProducts','mSubProducts.SubProductUID=tOrders.SubProductUID', 'left');
		$this->db->join ( 'mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID' , 'left' );
		$this->db->where('tOrders.OrderUID',$orderUID);
		$query = $this->db->get();
		return $query->result();
	}


	function filesize_formatted($path)
	{
		$size = filesize($path);
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$power = $size > 0 ? floor(log($size, 1024)) : 0;
		return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
	}

	function Get_TaxAuthority_by_Id($TaxAuthorityUID){
		$this->db->where(array("Active"=>1,"TaxAuthorityUID"=>$TaxAuthorityUID));
		$query = $this->db->get('mtaxauthority');
		return $query->row();
	}

	function Get_borrowers_by_OrderUID($OrderUID){
		$query = $this->db->query("SELECT GROUP_CONCAT(PRName) AS BorrowerNames FROM tOrderPropertyRoles WHERE PropertyRoleUID = 5 and
			OrderUID = '".$OrderUID."' ");
		$result = $query->row();
		return $result;
	}


	function Getmtats(){
		$this->db->select('TATUID,TATName');
		$this->db->from('mtat');
		$this->db->where('Active',1);
		$query = $this->db->get();
		return $query->result();
	}

	function is_customerpricing_abstractorfee(){
		$this->db->select('CustomerPricing,AbstractorFee');
		$this->db->from('mroles');
		$this->db->where('Active',1);
		$query = $this->db->get();
		return $query->row();
	}

	function Audittrail_insert($data){
	// $this->set_lastactivitydatetime($this->session->userdata('UserUID'));
         
		$this->db->insert('taudittrail',$data);
	// echo '<pre>';print_r($data);exit;

	}

	function Audittrail_diff($newvalue,$oldvalue,$data1){
		$data1=$data1;
		$data1['NewValue']=(array_diff_assoc($newvalue, $oldvalue));
		$data1['OldValue']=(array_diff_assoc($oldvalue, $newvalue));
		// echo '<pre>';print_r($data1['NewValue']);exit;
		foreach($data1['NewValue'] as $key=>$value){
			$data1['NewValue']=$value;
			$data1['FieldUID']=$key;

			$FieldUID=$this->common_model->GetFieldUID($data1['FieldUID'],$data1['TableName']);
			if($FieldUID!=''){
				$data1['FieldUID']=isset($FieldUID)?$FieldUID:'';
			}else{
				$ele=[];
				$ele['FieldName']=$key;
				$ele['FieldKeyword']='%%'.$key.'%%';
				$ele['TableName']=$data1['TableName'];

				if(!empty($ele['FieldName']) && !empty($ele['FieldKeyword']) && !empty(	$ele['TableName'])){
					$res = $this->db->insert('mfields',$ele);
					$FieldUID=$this->common_model->GetFieldUID($data1['FieldUID'],$data1['TableName']);
				}
				$data1['FieldUID']=isset($FieldUID)?$FieldUID:$key;

			}

			$data1['OldValue']=$oldvalue[$key];
			$this->common_model->Audittrail_insert($data1);
		}
	}

	function GetFieldUID($FieldName,$TableName){

		$this->db->select('*');
		$this->db->from('mfields');
		$this->db->where('mfields.FieldName',$FieldName);
		$this->db->where('mfields.TableName',$TableName);
		$query=$this->db->get('')->row_array();
		return $query['FieldUID'];

	}

	function get_defaultsubproduct($CustomerUID){

		$query = $this->db->query("SELECT * FROM `mCustomers` join mcustomerdefaultproduct on mcustomerdefaultproduct.CustomerUID = mCustomers.CustomerUID where `DefaultProductSubCode` = 1 and mCustomers.CustomerUID = $CustomerUID");
		return $query->row();
	}

	function get_defaultsvendor($VendorUID){

		$query = $this->db->query("SELECT * FROM `mvendors` join mvendorsproducts on mvendorsproducts.VendorUID = mvendors.VendorUID LEFT JOIN mSubProducts ON mvendorsproducts.SubProductUID = mSubProducts.SubProductUID where `mvendors.Active` = 1 and mvendors.VendorUID = $VendorUID");
		return $query->row();
	}


	function get_customerbyuser($loggedid){

		$this->db->select('*,musers.CustomerUID')->from('musers');
		$this->db->where('UserUID',$loggedid);
		$this->db->join('mCustomers','musers.CustomerUID = mCustomers.CustomerUID','LEFT');
		return $this->db->get()->row();
	}


	function getsubproductbyUID($SubProductUID){
		$this->db->select('*')->from('mSubProducts');
		$this->db->where('SubProductUID',$SubProductUID);
		return $this->db->get()->row();
	}


	function get_defaulttemplate_bycustomerUID($CustomerUID){
		$this->db->select('*')->from('mCustomers');
		$this->db->where('CustomerUID',$CustomerUID);
		$this->db->join('mTemplates','mTemplates.TemplateUID = mCustomers.DefaultTemplateUID','left');
		return $this->db->get()->row();
	}

	function getmonholddetails()
	{
		$this->db->select("*");
		$this->db->from('monholddetails');
		$query = $this->db->get();
		return $query->result();
	}


	function is_workflow_completed($OrderUID,$WorkflowUID)
	{
		$this->db->select ( 'torderassignment.OrderUID' );
		$this->db->from ( 'torderassignment' );
		// $this->db->where ('torderassignment.AssignedToUserUID',$loggedid);
		$this->db->where ('torderassignment.WorkflowModuleUID',$WorkflowUID);
		$this->db->where ('torderassignment.WorkflowStatus',5);
		$this->db->where ('torderassignment.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->num_rows();
	}


	function GetProductSubProduct()
	{
		$this->db->select("*");
		$this->db->from('mProducts');
		$this->db->join('mSubProducts','mSubProducts.ProductUID=mProducts.ProductUID');
		$query = $this->db->get();
		return $query->result();
	}


	function get_assigned_workflows($OrderUID,$loggedid){
		$this->db->select ( 'torderassignment.OrderUID,WorkflowModuleUID' );
		$this->db->from ( 'torderassignment' );
		$this->db->where ('torderassignment.AssignedToUserUID',$loggedid);
		$this->db->where ('torderassignment.WorkflowModuleUID !=',4);
		$this->db->where ('torderassignment.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_completed_workflows($OrderUID,$loggedid){
		$this->db->select ( 'torderassignment.OrderUID,WorkflowModuleUID' );
		$this->db->from ( 'torderassignment' );
		$this->db->where ('torderassignment.AssignedToUserUID',$loggedid);
		$this->db->where ('torderassignment.WorkflowModuleUID !=',4);
		$this->db->where ('torderassignment.WorkflowStatus',5);
		$this->db->where ('torderassignment.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->result_array();
	}

	function GetOrderTypeDetailsById($OrderTypeUID)
	{
		$this->db->select("OrderTypeName");
		$this->db->where(array("Active"=>1,"OrderTypeUID"=>$OrderTypeUID));
		$query = $this->db->get('mOrderTypes');
		$res = $query->row();
		return $res->OrderTypeName;
	}

	function GetRolePricingdetails($RoleUID){
		$this->db->select('*');
		$this->db->from('mroles');
		$this->db->where('RoleUID',$RoleUID);
		return $this->db->get('')->row();
	}

	function getrole_workflows(){

		$RoleUID = $this->session->userdata('RoleUID');

		$this->db->select('*');
		$this->db->where('RoleUID',$RoleUID);
		$query = $this->db->get('mroles');
		$result =  $query->row();


		$return_workflows = [];

		if($result->OrderSearch == '1'){
			$return_workflows[] =  1;
		}
		if($result->OrderTyping == '1'){
			$return_workflows[] =  2;
		}
		if($result->OrderTaxCert == '1'){
			$return_workflows[] =  3;
		}

		if($result->OrderScheduling == '1'){
			$return_workflows[] =  9;
		}

		if($result->OrderSigning == '1'){
			$return_workflows[] =  10;
		}

		if($result->OrderShipping == '1'){
			$return_workflows[] =  11;
		}
		if($result->OrderDocReview == '1'){
			$return_workflows[] =  6;
		}
		if($result->OrderImpedience == '1'){
			$return_workflows[] =  7;
		}

		$keywords_imploded = "'" . implode ( "', '", $return_workflows ) . "'";

		return $keywords_imploded;
	}

	function get_inhouse_external($OrderTypeUID,$StateUID,$CountyUID){
		$query = $this->db->query("SELECT IF( EXISTS(SELECT * FROM `tabstractormode` where OrderTypeUID = '".$OrderTypeUID."' and StateUID = '".$StateUID."' and CountyUID = '".$CountyUID."' limit 1 ) , 0, 1) AS exist");
		$result = $query->row();
		return $result->exist;
	}

	function get_inhouse_external_forzipcode($OrderTypeUID,$StateUID,$ZipCode){
		$ZipCode = addslashes($ZipCode);
		$query = $this->db->query("SELECT IF( EXISTS(SELECT * FROM `tabstractormode` where OrderTypeUID = '".$OrderTypeUID."' and StateUID = '".$StateUID."' and ZipCode = '".$ZipCode."' limit 1 ) , 0, 1) AS exist");
		$result = $query->row();
		return $result->exist;
	}

	function is_summary_enabled($OrderUID,$loggedid){
		$this->db->select('*');
		$this->db->from ( 'torderassignment' );
		$this->db->where('torderassignment.AssignedToUserUID',$loggedid);
		$this->db->where('torderassignment.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->num_rows();
	}

	function get_workflow_nameby_url($MenuURL){

		$this->db->select('*');
		$this->db->from('msubmenuworkflowmodules');
		$this->db->join('mworkflowmodules', 'mworkflowmodules.WorkflowModuleUID=msubmenuworkflowmodules.WorkflowModuleUID');

		$this->db->where('MenuURL',$MenuURL);
		$query = $this->db->get();
		return $query->row();
	}

	function check_useris_assigned($OrderUID,$WorkflowModuleUID,$UserUID){
		$this->db->select('*');
		$this->db->from('torderassignment');
		$this->db->where ('torderassignment.AssignedToUserUID',$UserUID);
		$this->db->where ('torderassignment.WorkflowModuleUID',$WorkflowModuleUID);
	//$this->db->where ('torderassignment.WorkflowStatus',5);
		$this->db->where ('torderassignment.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->row();
	}

	function check_is_assigned($OrderUID,$WorkflowModuleUID)
	{
		$this->db->select('IsReviewed,AssignedToUserUID');
		$this->db->from('torderassignment');
		$this->db->where ('torderassignment.WorkflowModuleUID',$WorkflowModuleUID);
		$this->db->where ('torderassignment.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->row();
	}
	function check_order_assigned($OrderUID,$WorkflowModuleUID)
	{
		$this->db->select('IsReviewed,AssignedToUserUID');
		$this->db->from('torderassignment');
		$this->db->where ('torderassignment.WorkflowModuleUID',$WorkflowModuleUID);
		$this->db->where('AssignedToUserUID IS NOT NULL', NULL, FALSE);
		$this->db->where ('torderassignment.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->row();
	}

	function is_holdorder($OrderUID,$WorkflowModuleUID){

		$this->db->select('*');
		$this->db->from ( 'torderassignment' );
		$this->db->where('torderassignment.WorkflowModuleUID',$WorkflowModuleUID);
		$this->db->where('torderassignment.Workflowstatus',4);
		$this->db->where('torderassignment.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->num_rows();
	}

	function is_reviewholdorder($OrderUID,$WorkflowModuleUID){
		$this->db->select('*');
		$this->db->from ( 'torderassignment' );
		$this->db->where('torderassignment.WorkflowModuleUID',$WorkflowModuleUID);
		$this->db->where('torderassignment.Workflowstatus',4);
		$this->db->where('torderassignment.OrderUID',$OrderUID);
		$query = $this->db->get();
		return $query->num_rows();
	}

	function get_myorders_by_cust_id($loggedid = '',$CustomerUID)
	{

		$status[0] = $this->config->item('keywords')['Cancelled'];
		$status[1] = $this->config->item('keywords')['Order Completed'];
		$status[2] = $this->config->item('keywords')['Exception Raised'];

		$this->db->select ( 'COUNT(*)' );
		$this->db->select('DATE_FORMAT(tOrders.OrderDueDateTime, "%m-%d-%Y %H:%i:%s") as OrderDueDateTime', FALSE);
		$this->db->select('DATE_FORMAT(OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);
		$this->db->from ( 'tOrders' );
		$this->db->join ( 'torderassignment', 'torderassignment.OrderUID = tOrders.OrderUID','left');
		$this->db->join ( 'mworkflowmodules', 'mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID','left');
		$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = tOrders.CustomerUID','left');
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID','left');
		$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = tOrders.SubProductUID','left');

		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mSubProducts.ProductUID','left');

		$this->db->where_not_in('tOrders.StatusUID', $status);
		$this->db->where('tOrders.CustomerUID',$CustomerUID);

		$this->db->group_by('tOrders.OrderUID');
		$this->db->order_by('tOrders.OrderUID,OrderNumber', 'DESC');
		$query = $this->db->get();
		return $query->num_rows();
	}

	/*function GetMyOrdersCount()
	{
		$loggedid = $this->session->userdata('UserUID');

		if(in_array($this->session->userdata('RoleType'),array(8)))
		{
			$customer = $this->GetCustomerByUserUID($loggedid);
			return $this->get_myorders_by_cust_id($loggedid,$customer->CustomerUID);
		}

		if($this->common_model->GetMyOrdersQueue() == 1)
		{
			$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'];

			$sql = "SELECT `CustomerName`, `OrderNumber`, `StateName`, `StatusName`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`, `mStates`.`StateCode`,DATE_FORMAT(torderassignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDateTime, '%m-%d-%Y %H:%i:%s') as OrderDueDateTime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime FROM (`tOrders`) LEFT JOIN `torderassignment` ON `tOrders`.`OrderUID` = `torderassignment`.`OrderUID` JOIN `mStates` ON `tOrders`.`PropertyStateUID` = `mStates`.`StateUID` JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` IN (".$statuses.") AND `torderassignment`.`AssignedToUserUID` = ".$loggedid." AND torderassignment.WorkflowModuleUID !=4 GROUP BY `OrderUID` ORDER BY FIELD(tOrders.PriorityUID,1,3,2),OrderEntryDatetime DESC,tOrders.OrderUID ASC ";
		}
		else
		{
			$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].
			','.$this->config->item('keywords')['Cancelled'];

			$sql = "SELECT `CustomerName`, `OrderNumber`, `StateName`, `StatusName`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`, `mStates`.`StateCode`,DATE_FORMAT(torderassignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDateTime, '%m-%d-%Y %H:%i:%s') as OrderDueDateTime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime FROM (`tOrders`)  LEFT JOIN `torderassignment` ON `tOrders`.`OrderUID` = `torderassignment`.`OrderUID` JOIN `mStates` ON `tOrders`.`PropertyStateUID` = `mStates`.`StateUID` JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.")  GROUP BY `OrderUID` ORDER BY FIELD(tOrders.PriorityUID,1,3,2),OrderEntryDatetime DESC,tOrders.OrderUID ASC ";
		}

		$query = $this->db->query($sql);
		$checkorders = $query->result_array();
		$count = 0;
		foreach ($checkorders as $key => $value)
		{
			if(in_array($this->session->userdata('RoleType'),array(1,2,3,4,5,6)) == False)
			{
				$assigned = $this->common_model->get_assigned_workflows($value['OrderUID'],$loggedid);
				$completed = $this->common_model->get_completed_workflows($value['OrderUID'],$loggedid);

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

				if($assigned_orderss === array_intersect($assigned_orderss, $completed_orderss) && $completed_orderss === array_intersect($completed_orderss, $assigned_orderss))
				{
					if($assigned_workflows === array_intersect($assigned_workflows, $completed_workflows) && $completed_workflows === array_intersect($completed_workflows, $assigned_workflows))
					{
						unset($checkorders[$key]);
					}
				}
				$count = count($checkorders);
				} else {
					$count++;
				}
			}
			return $count;
		}*/


		function GetMyOrdersCount()
		{

			$is_vendor_login = $this->common_model->is_vendorlogin();
			$loggedid = $this->session->userdata('UserUID');
			if($is_vendor_login)
			{

				$logged_details = $this->common_model->get_logged_details();
				$vendors  = $this->get_vendors($logged_details,$loggedid);
				if(count($vendors) > 0)
				{
					return $this->get_vendor_countall_orders($loggedid,$vendors[0]->VendorUID);
				}

			}
			else
			{


				if(in_array($this->session->userdata('RoleType'),array(8)))
				{
					$customer = $this->GetCustomerByUserUID($loggedid);
					return $this->get_myorders_by_cust_id($loggedid,$customer->CustomerUID);
				}

				/*FOR SUPERVISOR CHECK*/
				$where= '';
				if ($this->session->userdata('RoleType') == 6){
					$UserProducts = $this->common_model->_get_product_bylogin();
					if($UserProducts): $where .= ' AND `mProducts`.`ProductUID` IN ('.$UserProducts.')'; else:  $where .= ' AND `mProducts`.`ProductUID` IN (NULL)'; endif;
				}

				if($this->common_model->GetMyOrdersQueue() == 1)
				{
					$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'];

					$sql = "SELECT `CustomerName`, `OrderNumber`, `StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `tOrders`.`OrderEntryDatetime` as OrderEntryDatetime, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(torderassignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDateTime, '%m-%d-%Y %H:%i:%s') as OrderDueDateTime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime , tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName FROM (`tOrders`) LEFT JOIN `torderassignment` ON `tOrders`.`OrderUID` = `torderassignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` IN (".$statuses.") AND `torderassignment`.`AssignedToUserUID` = ".$loggedid." AND torderassignment.WorkflowModuleUID !=4 $where GROUP BY `OrderUID` ORDER BY FIELD(tOrders.PriorityUID,1,3,2),OrderEntryDatetime DESC,tOrders.OrderUID ASC";
				}
				else
				{
					$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].
					','.$this->config->item('keywords')['Cancelled'].','.$this->config->item('keywords')['Exception Raised'];


					if (!in_array($this->session->userdata('RoleType'), [1,2,3,4,5,6])) {
						$usergroups = $this->common_model->get_user_group_id($this->loggedid);

						if (!empty($usergroups)) {

							$usergroups = implode(',', $usergroups);
				// $groupcustomers = $this->common_model->get_customer_ingroup($usergroups);
							/*@Desc Group Setup Changed @Author Jainulabdeen @Updated Aug 7 2020*/
							$this->load->model('myorders/Myorders_model');
							$loggedin_userproducts = $this->Myorders_model->get_subproducts_by_groupuids($usergroups);
							$where .= 'AND '.$loggedin_userproducts;
							/*if (!empty($loggedin_userproducts)) {
								$subproductuids='';
								foreach ($loggedin_userproducts as $key => $value) {
									$subproductuids = $value->SubProductUID . ', ';
								}
								$subproductuids = rtrim($subproductuids,  ', ');


								$where .= " AND (tOrders.SubProductUID IN (".$subproductuids.") OR `torderassignment`.`AssignedToUserUID` = ".$loggedid." AND torderassignment.WorkflowModuleUID !=4) ";
					// echo $where; exit;
							}*/
							/*End*/
						}
					}

					$sql = "SELECT `CustomerName`, `OrderNumber`, `StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `tOrders`.`OrderEntryDatetime` as OrderEntryDatetime, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(torderassignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDateTime, '%m-%d-%Y %H:%i:%s') as OrderDueDateTime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime, tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName FROM (`tOrders`)  LEFT JOIN `torderassignment` ON `tOrders`.`OrderUID` = `torderassignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.") $where GROUP BY `OrderUID` ORDER BY FIELD(tOrders.PriorityUID,1,3,2),OrderEntryDatetime DESC,tOrders.OrderUID ASC";

				}

				$query = $this->db->query($sql);
				$checkorders = $query->result_array();
				$count = 0;
				$mroles = $this->common_model->get_roles($this->RoleUID);

				foreach ($checkorders as $key => $value)
				{
					if(in_array($this->session->userdata('RoleType'),array(1,2,3,4,5,6)) == False)
					{
						if (!empty($mroles) && $mroles[0]['MyOrdersQueue'] == 1) {

							$assigned = $this->common_model->get_assigned_workflows($value['OrderUID'],$loggedid);
							$completed = $this->common_model->get_completed_workflows($value['OrderUID'],$loggedid);

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

							if($assigned_orderss === array_intersect($assigned_orderss, $completed_orderss) && $completed_orderss === array_intersect($completed_orderss, $assigned_orderss))
							{
								if($assigned_workflows === array_intersect($assigned_workflows, $completed_workflows) && $completed_workflows === array_intersect($completed_workflows, $assigned_workflows))
								{

									if($checkorders[$key]['StatusUID']!='49'){
										unset($checkorders[$key]);
									}

								}
							}
							$count = count($checkorders);
						}
						else{
							$count++;
						}
					} else {
						$count++;
					}
				}
				return $count;
			}
		}

		function GetAssignedWorkflowByWorkflowUID($OrderUID, $WorkflowModuleUID)
		{
			$this->db->select('*')->from('torderassignment');
			$this->db->where(array('OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID,'WorkflowStatus'=>5));
			$this->db->where('(IsRevision IS NULL OR IsRevision=0)', NULL, false);
			return $this->db->get()->row();
		}

		function CheckIsWorkflowAssigned($OrderUID, $WorkflowModuleUID)
		{
			$this->db->select('*')->from('torderassignment');
			$this->db->where(array('OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID,'AssignedToUserUID'=>$this->session->userdata('UserUID')));
			return $this->db->get()->row();
		}

		function CheckReviseEnabledForUser($OrderUID, $WorkflowModuleUID)
		{
			$this->db->select('torderassignment.*, torderrevision.*, musers.UserName')->from('torderassignment');
			$this->db->join('torderrevision','torderassignment.OrderUID=torderrevision.OrderUID AND torderassignment.WorkflowModuleUID=torderrevision.WorkflowModuleUID');
			$this->db->join('musers','musers.UserUID=torderrevision.AssignedToUserUID', 'left');
			$this->db->where('torderassignment.IsRevision', 1);
			$this->db->where('torderassignment.OrderUID', $OrderUID);
			$this->db->where('torderassignment.WorkflowModuleUID', $WorkflowModuleUID);
			$this->db->where('torderrevision.AssignedToUserUID', $this->loggedid);
			$this->db->where('torderrevision.CompleteDateTime IS NULL',NULL, false);
			return $this->db->get()->row();
		}

		function CheckReviseEnabled($OrderUID, $WorkflowModuleUID)
		{
			$this->db->select('torderassignment.*, torderrevision.*, musers.UserName')->from('torderassignment');
			$this->db->join('torderrevision','torderassignment.OrderUID=torderrevision.OrderUID AND torderassignment.WorkflowModuleUID=torderrevision.WorkflowModuleUID');
			$this->db->join('musers','musers.UserUID=torderrevision.AssignedToUserUID', 'left');
			$this->db->where('torderassignment.IsRevision', 1);
			$this->db->where('torderassignment.OrderUID', $OrderUID);
			$this->db->where('torderassignment.WorkflowModuleUID', $WorkflowModuleUID);
			$this->db->where('torderrevision.CompleteDateTime IS NULL',NULL, false);
			return $this->db->get()->row();
		}
		function get_vendor_countall_orders($loggedid,$VendorUID)
		{
			if($this->common_model->GetMyOrdersQueue() == 1)
			{

				$statuses = $this->config->item('keywords')['Order Assigned'].','.$this->config->item('keywords')['Order Work In Progress'].','.$this->config->item('keywords')['New Order'].','.$this->config->item('keywords')['Partial Review Complete'].','.$this->config->item('keywords')['Partial Draft Complete'].','.$this->config->item('keywords')['Review In Progress'];

				$sql = "SELECT `CustomerName`, `OrderNumber`, `StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(torderassignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDateTime, '%m-%d-%Y %H:%i:%s') as OrderDueDateTime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime , tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName FROM (`tOrders`) LEFT JOIN `torderassignment` ON `tOrders`.`OrderUID` = `torderassignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID` LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` IN (".$statuses.") AND `torderassignment`.`AssignedToUserUID` = ".$loggedid." AND torderassignment.WorkflowModuleUID !=4 AND torderassignment.SendToVendor = '1' AND VendorUID  = '".$VendorUID."' AND tOrders.OrderUID IN (select OrderUID from torderassignment where AssignedToUserUID=$loggedid and SendToVendor='1' and (QCCompletedDateTime='0000-00-00 00:00:00' OR QCCompletedDateTime IS NULL) AND OrderFlag <>2)  GROUP BY `OrderUID` ORDER BY FIELD(tOrders.PriorityUID,1,3,2),OrderEntryDatetime DESC,tOrders.OrderUID ASC ";
			}
			else{

				$statuses = $this->config->item('keywords')['Order Exported'].','.$this->config->item('keywords')['Order Completed'].
				','.$this->config->item('keywords')['Cancelled'];

				$sql = "SELECT `CustomerName`, `OrderNumber`, `StatusName`,`tOrders`.`StatusUID`,`StatusColor`, `tOrders`.`OrderUID`, `mOrderPriority`.`PriorityName`,`mOrderPriority`.`TAT`,`mOrderPriority`.`PriorityUID`, `mProducts`.`ProductName`, `mProducts`.`ProductCode`, `mSubProducts`.`SubProductCode`, `mSubProducts`.`SubProductName`,DATE_FORMAT(torderassignment.AssignedDatetime, '%m-%d-%Y %H:%i:%s') as AssignedDatetime, DATE_FORMAT(tOrders.OrderDueDateTime, '%m-%d-%Y %H:%i:%s') as OrderDueDateTime, DATE_FORMAT(tOrders.OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime, tOrders.PropertyStateCode,tOrders.PropertyCityName,tOrders.PropertyCountyName FROM (`tOrders`)  LEFT JOIN `torderassignment` ON `tOrders`.`OrderUID` = `torderassignment`.`OrderUID`  LEFT JOIN `mOrderPriority` ON `mOrderPriority`.`PriorityUID` = `tOrders`.`PriorityUID`  LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mSubProducts` ON `mSubProducts`.`SubProductUID` = `tOrders`.`SubProductUID` LEFT JOIN `mProducts` ON `mProducts`.`ProductUID` = `mSubProducts`.`ProductUID` LEFT JOIN `mOrderStatus` ON `mOrderStatus`.`StatusUID` = `tOrders`.`StatusUID` WHERE `tOrders`.`StatusUID` NOT IN (".$statuses.") AND torderassignment.SendToVendor = '1' AND VendorUID  = '".$VendorUID."' AND tOrders.OrderUID IN (select OrderUID from torderassignment where SendToVendor='1' and (QCCompletedDateTime='0000-00-00 00:00:00' OR QCCompletedDateTime IS NULL) AND OrderFlag <>2)    GROUP BY `OrderUID`  ORDER BY FIELD(tOrders.PriorityUID,1,3,2),OrderEntryDatetime DESC,tOrders.OrderUID ASC ";

			}

			$query = $this->db->query($sql);
		// return $query->result_array();

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

				if(in_array($this->session->userdata('RoleType'),array(1,2,3,4,5,6,13)) == False)
				{
					$assigned = $this->common_model->get_assigned_workflows($value['OrderUID'],$this->loggedid);
					$completed = $this->common_model->get_completed_workflows($value['OrderUID'],$this->loggedid);
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

			if(count($checkorders) > 0){
				$my_orders = $checkorders;
			} else {
				$my_orders = array();
			}

			return sizeof($my_orders);
		}



		function get_all_Workflows()
		{
			$query = $this->db->get('mworkflowmodules');
			return $query->result_array();
		}

		function get_order_assigned_users($data)
		{
			$Workflows = $this->get_all_Workflows();
			$is_vendor_login = $this->common_model->is_vendorlogin();
			$ret_data = [];

			foreach ($Workflows as $key => $Workflow) {

				$query=$this->db->query("SELECT LoginID,SendToVendor,torderassignment.VendorUID as  AssignedVendorUID,VendorName,musers.VendorUID as VendorUID FROM `torderassignment` LEFT JOIN musers on musers.UserUID = torderassignment.AssignedToUserUID LEFT JOIN mvendors ON mvendors.VendorUID = torderassignment.VendorUID WHERE `OrderUID` = '".$data['OrderUID']."' AND AssignedToUserUID IS NOT NULL AND `WorkflowModuleUID` = '".$Workflow['WorkflowModuleUID']."' ");
				$result = $query->row();
				if(count($result) > 0){

					if($is_vendor_login ){

						$ret_data = '--';

						if($result->SendToVendor == '0' && $result->VendorUID == '' ){
							$ret_data = '--';
						}else if( $result->AssignedVendorUID == $result->VendorUID){

							$ret_data = $result->LoginID;
						}


					}else{
						if($result->SendToVendor == '1' && $result->VendorUID != ''){

							$ret_data = $result->VendorName;
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

		function get_customer_ingroup($user_in_groups = '')
		{

			$grp_id = $this->get_customer_group_id($user_in_groups);
			$this->db->distinct();
			$this->db->select ( 'CustomerName,CustomerUID' );
			$this->db->from ( 'mGroupCustomers' );
			$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = mGroupCustomers.GroupCustomerUID' , 'left' );
			if($grp_id != null){
				$this->db->where_in ('mGroupCustomers.GroupUID',$grp_id);
			}
			/*FOR SUPERVISOR CHECK*/
			if ($this->session->userdata('RoleType') == 6){
				$UserGroupUID = $this->common_model->_get_groups_bylogin();
				if($UserGroupUID): $this->db->where('mGroupCustomers.GroupUID IN ('.$UserGroupUID.')', null, false); else: $this->db->where('mGroupCustomers.GroupUID IN (0)', null, false); endif;
			}
			$this->db->group_by('mGroupCustomers.GroupCustomerUID');
			$query = $this->db->get();
			return $query->result();


		}

		function get_customer_group_id($user_in_groups)
		{
			$this->db->select ( 'GroupUID' );
			$this->db->from ( 'mGroupCustomers' );
			if($user_in_groups != null){
				$this->db->where_in ('mGroupCustomers.GroupUID',$user_in_groups);
			}
			/*FOR SUPERVISOR CHECK*/
			if ($this->session->userdata('RoleType') == 6){
				$UserGroupUID = $this->common_model->_get_groups_bylogin();
				if($UserGroupUID): $this->db->where('mGroupCustomers.GroupUID IN ('.$UserGroupUID.')', null, false); else: $this->db->where('mGroupCustomers.GroupUID IN (0)', null, false); endif;
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


		function get_customer_subproduct($CustomerUIDs){
			if($CustomerUIDs != ''){
				$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT SubProductUID) AS SubProductUIDs FROM `mCustomerProducts` WHERE CustomerUID IN ($CustomerUIDs)");
				return $query->row();
			}
			return array();
		}

		function GetReviewOrdersCount()
		{

			$loggedid = $this->session->userdata('UserUID');
			$status[0] = $this->config->item('keywords')['Draft Complete'];

			$status[1] = $this->config->item('keywords')['Partial Draft Complete'];
			$status[2] = $this->config->item('keywords')['Review In Progress'];
				$status[3] = $this->config->item('keywords')['Partial Review Complete'];


			$is_permission = $this->common_model->role_workflows();

			$groupin_user_logged = $this->get_user_group_id($loggedid);
			$CustomerUIDs = $this->get_customer_ingroup($groupin_user_logged);
			$cus_id =[];
			foreach ($CustomerUIDs as $key => $CustomerUID) {
				$cus_id[] = $CustomerUID->CustomerUID;
			}

	
			$Workflowstatus[0] = 0;
			$Workflowstatus[1] = 3;



			$this->db->select ("COUNT(*)");
			$this->db->from ( 'tOrders' );
			$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID');
			$this->db->join ( 'mOrderTypes', 'mOrderTypes.OrderTypeUID = tOrders.OrderTypeUID','left');
			$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID','left');
			$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID');
			if ($is_permission->ReviewQueue == 2){

				$this->db->join('torderassignment','tOrders.OrderUID = torderassignment.OrderUID','left');

			}else{

				$this->db->join('torderassignment','tOrders.OrderUID = torderassignment.OrderUID','left');
				$this->db->where('torderassignment.WorkflowModuleUID',4);
				/*if($cus_id){
					$this->db->where_in ('tOrders.CustomerUID',$cus_id);
				}*/
				$this->db->where("torderassignment.AssignedToUserUID = $loggedid");
			}

			/*FOR SUPERVISOR CHECK*/
			if ($this->session->userdata('RoleType') == 6){
				$UserSubProducts = $this->common_model->_get_Subproducts_bylogin($loggedid);
				if($UserSubProducts): $this->db->where('tOrders.SubProductUID IN ('.$UserSubProducts.')', null, false); else: $this->db->where('tOrders.SubProductUID IN (0)', null, false); endif;
			}


			if (!in_array($this->RoleType, [1,2,3,4,5,6])) {			
				if(!empty($cus_id)){
					$this->db->where_in ('tOrders.CustomerUID',$cus_id);
				}
			}

			$this->db->where_in ('tOrders.StatusUID',$status);
			$this->db->group_by('tOrders.OrderUID');
			$query = $this->db->get();
			return $query->num_rows();


		}

		function getCancelOrdersCount()
		{
			$loggedid = $this->session->userdata('UserUID');
			if($this->common_model->GetCancellationQueue() == 2)
			{
				$query = $this->db->query("SELECT COUNT(*) AS CancelOrdersCount FROM tordercancel LEFT JOIN tOrders ON tordercancel.OrderUID = tOrders.OrderUID  LEFT JOIN mOrderPriority ON mOrderPriority.PriorityUID = tOrders.PriorityUID  WHERE tordercancel.CancelStatus != 0  ");
			}
			else if($this->common_model->GetCancellationQueue() == 1)
			{

				$query = $this->db->query("SELECT COUNT(DISTINCT(OrderNumber)) AS CancelOrdersCount,tordercancel.OrderUID,tordercancel.Remarks,tordercancel.CancelStatus,PriorityName FROM tordercancel
					LEFT JOIN tOrders ON tordercancel.OrderUID = tOrders.OrderUID
					LEFT JOIN torderassignment ON torderassignment.OrderUID = tordercancel.OrderUID
					LEFT JOIN mOrderPriority ON mOrderPriority.PriorityUID = tOrders.PriorityUID
					WHERE
					tordercancel.CancelStatus != 0
					AND torderassignment.AssignedToUserUID = '$loggedid'");
			}
			$res = $query->row();
			return $res->CancelOrdersCount;
		}

		function getCompletedOrdersCount()
		{
			$loggedid = $this->session->userdata('UserUID');
			$query=[];
			$ReviewComplete = $this->config->item('keywords')['Review Complete'];
			$OrderComplete= $this->config->item('keywords')['Order Completed'];

			$status[0] = $this->config->item('keywords')['Review Complete'];
			$status[1] = $this->config->item('keywords')['Order Completed'];
		//$status = $this->config->item('keywords')['Order Completed'];
			$this->db->select('count(DISTINCT(OrderNumber)) AS CompletedOrdersCount');
			$this->db->select ( 'CustomerName,OrderNumber,tOrders.PropertyStateCode as StateName,StatusName,StatusColor,tOrders.OrderUID,tOrders.PriorityUID,PriorityName' );
			$this->db->select('DATE_FORMAT(tOrders.OrderEntryDatetime, "%m-%d-%Y") as AssignedDatetime', FALSE);
			$this->db->from ( 'tOrders');
			$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
			$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = tOrders.CustomerUID' , 'left' );
			$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
			$this->db->join ('torderassignment','torderassignment.OrderUID = tOrders.OrderUID','left');
			$this->db->where_in('tOrders.StatusUID',$status);
			if($this->common_model->GetCompletedQueue() == 1)
			{
				$this->db->where('torderassignment.AssignedToUserUID',$loggedid);
			}
			$this->db->group_by('OrderUID');
			$query = $this->db->get();
			$res = $query->result();

			$Count = 0;
			foreach ($res as $key => $value){
				$Count += $value->CompletedOrdersCount;
			}
			return $Count;
		}

		function getOnHoldOrdersCount()
		{
			$loggedid = $this->session->userdata('UserUID');
			$status[0] = $this->config->item('keywords')['Order Assigned'];
			$status[1] = $this->config->item('keywords')['Order Work In Progress'];
			$this->db->select('count(DISTINCT(OrderNumber)) AS OnHoldOrdersCount');
			$this->db->select ( 'tOrders.OrderUID,OrderNumber,tOrders.CustomerUID,CustomerName,OrderTypeName,PriorityName,tOrders.SubProductUID,mSubProducts.SubProductName,mProducts.ProductUID,mProducts.ProductName,mProducts.ProductCode,mSubProducts.SubProductCode,
				tOrders.PropertyStateCode as StateCode, tOrders.PropertyStateCode as StateName ,WorkflowModuleName,StatusName,mOrderPriority.PriorityUID' );
			$this->db->select('DATE_FORMAT(torderassignment.AssignedDatetime, "%m-%d-%Y %H:%i:%s") as AssignedDatetime', FALSE);
			$this->db->select("DATE_FORMAT(OrderEntryDatetime, '%m-%d-%Y %H:%i:%s') as OrderEntryDatetime", FALSE);
			$this->db->select('DATE_FORMAT(tOrders.OrderDueDateTime, "%m-%d-%Y %H:%i:%s") as OrderDueDateTime', FALSE);
			//$this->db->select('DATE_FORMAT((TIMEDIFF(NOW(),torderassignment.OnholdDateTime)),"%H:%i:%s") as OnholdTime',FALSE);
			//$this->db->select("CONCAT(
			//  FLOOR(HOUR(TIMEDIFF(NOW(), torderassignment.OnholdDateTime)) / 24), ' days ',
			//  MOD(HOUR(TIMEDIFF(NOW(), torderassignment.OnholdDateTime)), 24), ' hours ',
			//  MINUTE(TIMEDIFF(NOW(), torderassignment.OnholdDateTime)), ' minutes') as OnholdTime",FALSE);
			$this->db->select ( 'Group_concat(WorkflowModuleName) as WorkflowModuleNamess' );


			$this->db->from ( 'torderassignment' );
			$this->db->join ( 'tOrders', 'torderassignment.OrderUID = tOrders.OrderUID','left');
			$this->db->join ( 'musers', 'torderassignment.AssignedToUserUID = musers.UserUID','left');
			$this->db->join ( 'mworkflowmodules', 'mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID','left');
			$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = tOrders.CustomerUID','left');
			$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID','left');
			$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = tOrders.SubProductUID');
			$this->db->join ( 'mOrderTypes', 'mOrderTypes.OrderTypeUID = tOrders.OrderTypeUID');
			$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID');
			$this->db->join ( 'mProducts', 'mProducts.ProductUID = mSubProducts.ProductUID');
		//$this->db->join('mreportsections','torderassignment.SectionUID=mreportsections.SectionUID');

			$this->db->where_in('tOrders.StatusUID', $status);
			$this->db->where('torderassignment.WorkflowStatus',4);

		// if (in_array($this->session->userdata('RoleType'),array('1','2','3','4','5','6','10')) == 0) {
		//  $this->db->where('torderassignment.AssignedToUserUID',$loggedid);
		// }

			if($this->common_model->GetOnHoldQueue() == 1)
			{

				$this->db->where('torderassignment.AssignedToUserUID',$loggedid);
			}

			$this->db->group_by('tOrders.OrderUID');
			$query = $this->db->get();
			$res = $query->result();
			$Count = 0;
			foreach ($res as $key => $value){
				$Count += $value->OnHoldOrdersCount;
			}
			return $Count;
		}



		function getExceptionOrderCount()
		{

			$loggedid = $this->session->userdata('UserUID');
			$status[0] = $this->config->item('keywords')['Exception Raised'];
			$groupin_user_logged = $this->get_user_group_id($loggedid);
			$CustomerUIDs = $this->get_customer_ingroup($groupin_user_logged);
			$cus_id =[];
			foreach ($CustomerUIDs as $key => $CustomerUID) {
				$cus_id[] = $CustomerUID->CustomerUID;
			}

			$Workflowstatus[0] = 0;
			$Workflowstatus[1] = 3;
			$Workflowstatus[2] = 5;

			$this->db->select ("tOrders.OrderNumber,tOrders.OrderUID,tOrders.StatusUID,tOrders.CustomerUID,CustomerName,OrderTypeName,StatusColor,PriorityName,mOrderStatus.StatusName,mOrderStatus.StatusColor, tOrders.OrderUID,texceptions.RaisedByUserUID,texceptions.RaisedOn,musers.UserName,mOrderPriority.PriorityUID,LoanNumber,PropertyAddress1,tOrders.PropertyStateCode,tOrders.PropertyCountyName");
			$this->db->select('DATE_FORMAT(tOrders.OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);
			$this->db->from ( 'texceptions');
			$this->db->join ( 'tOrders', 'texceptions.OrderUID = tOrders.OrderUID' , 'left' );
			$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
			$this->db->join ( 'mOrderTypes', 'mOrderTypes.OrderTypeUID = tOrders.OrderTypeUID' , 'left' );
			$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
			$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
			$this->db->join ( 'musers', 'musers.UserUID = texceptions.RaisedByUserUID' , 'left' );
	// if($this->common_model->GetExceptionQueue() == 1)
	// {
	//    $userid = $this->session->userdata('UserUID');
	//    $this->db->join('torderassignment','tOrders.OrderUID = torderassignment.OrderUID');
	//    $this->db->where("torderassignment.AssignedToUserUID = $userid AND torderassignment.WorkflowModuleUID = 4");
	//    $this->db->where_in ('torderassignment.Workflowstatus',$Workflowstatus);
	// }
	// else if($this->common_model->GetExceptionQueue() == 2)
	// {
	//    $this->db->join('torderassignment','tOrders.OrderUID = torderassignment.OrderUID');
	//    // $this->db->where("torderassignment.AssignedToUserUID = torderassignment.WorkflowModuleUID = 4");
	//    $this->db->where_in ('torderassignment.Workflowstatus',$Workflowstatus);
	// }

			if($cus_id){
				$this->db->where_in ('tOrders.CustomerUID',$cus_id);
			}
			$this->db->where_in ('texceptions.RaisedByUserUID',$this->session->userdata('UserUID'));
			$this->db->where_in ('texceptions.IsCustomNotifySeen',0);
			$this->db->group_by('tOrders.OrderUID');
			$query = $this->db->get();
			return $query->num_rows();
		}

		function GetMyApprovalsCount()
		{
			$this->db->select('*')->from('torderapprovals');
			$this->db->join('tOrders','tOrders.OrderUID = torderapprovals.OrderUID','left');
			$this->db->join('mCustomers','tOrders.CustomerUID = mCustomers.CustomerUID','left');
			$this->db->join('torderassignment','torderassignment.OrderUID = torderapprovals.OrderUID','left');
			$this->db->where('ApprovalStatus',0);
			$this->db->where('ApprovalFunction NOT IN ("Order Cancellation")');
			$this->db->where('torderapprovals.ExceptionType',0);
			$this->db->where('torderapprovals.IsReviewed',1);
			$this->db->group_by('torderapprovals.OrderUID');
			$q = $this->db->get();
			return $q->num_rows();
		}

		public function GetSearchCompleteStatus_ByOrderUID($OrderUID='')
		{
			$searchstatus=$this->db->get_where('torderassignment', array('OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>'1', 'WorkflowStatus'=>5));
			$rowcount=$searchstatus->num_rows();
			if($rowcount>0)
			{
				$abstractors=$this->db->get_where('torderabstractor', array('OrderUID'=>$OrderUID, 'DocumentReceived'=>1, 'IsSendtoAbstractor'=>1));
				$abstractor_num_rows=$abstractors->num_rows();
				if($abstractor_num_rows>0)
				{
					return true;
				}
				else return false;
			}
			else return false;
		}

		public function GetSearchCompleteStatus_ByOrderUID_WithAbstractor($OrderUID='')
		{
			$this->db->select('*')->from('torderabstractor');
			$this->db->join('torderassignment','torderabstractor.OrderUID=torderassignment.OrderUID','inner');
			$this->db->where(array('torderassignment.OrderUID'=>$OrderUID, 'torderassignment.WorkflowModuleUID'=>'1', 'torderassignment.WorkflowStatus'=>5));
			$this->db->where(array('torderabstractor.OrderUID'=>$OrderUID, 'DocumentReceived'=>1));
			$searchstatus=$this->db->get();
			$rowcount=$searchstatus->num_rows();
			if($rowcount>0)
				return true;
			else return false;
		}



		function set_loggedin($UserUID){
			$sessiontime = $this->session->sess_expiration;
			$LoggedDateTime = date("Y-m-d H:i:s", time() + $sessiontime);
			$this->db->query("UPDATE musers SET LastActivityDateTime = '".$LoggedDateTime."',LoggedDateTime = '".Date('Y-m-d H:i:s',strtotime('now'))."' WHERE UserUID = '".$UserUID."' ");
			if($this->db->affected_rows() > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}


		function set_lastactivitydatetime($UserUID){
			$this->db->query("UPDATE musers SET LastActivityDateTime = '".Date('Y-m-d H:i:s',strtotime('now'))."' WHERE UserUID = '".$UserUID."' ");
			if($this->db->affected_rows() > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function check_user_online($UserUID){
			$user = $this->db->query("SELECT * FROM musers WHERE UserUID = '".$UserUID."' ")->row();
			$current_time = strtotime(date("Y-m-d H:i:s"));
			$time_period = floor(round(abs($current_time - strtotime($user->LastActivityDateTime))/60,2));
			$Organization_lastactivityinterval = $this->db->query("SELECT ActivityInterval FROM morganizations WHERE OrgUID = 1")->row();

			if($time_period < $Organization_lastactivityinterval->ActivityInterval){

				/*user online*/
				return true;
			}else{
				/*user Offline*/
				return false;
			}
		}


		function completed_status_order($OrderUID){
			$query = $this->db->query("SELECT GROUP_CONCAT(WorkflowModuleName) AS WorkflowModuleName FROM tOrders LEFT JOIN torderassignment ON tOrders.OrderUID = torderassignment.OrderUID AND torderassignment.WorkflowModuleUID NOT IN (SELECT WorkflowModuleUID FROM torderassignment WHERE OrderUID = $OrderUID AND SendToVendor = '1' AND (QCCompletedDateTime IS NULL OR torderassignment.QCCompletedDateTime = '0000-00-00 00:00:00')) LEFT JOIN mworkflowmodules ON torderassignment.WorkflowModuleUID = mworkflowmodules.WorkflowModuleUID WHERE  WorkflowStatus = 5 AND tOrders.OrderUID = $OrderUID");
			$res = $query->row();
			return $res;
		}

		function check_order_is_assignedtouser($OrderUID){
			$loggedid = $this->session->userdata('UserUID');
			$this->db->select('*');
			$this->db->from('torderassignment');
			$this->db->where ('torderassignment.AssignedToUserUID',$loggedid);
			$this->db->where ('torderassignment.Workflowstatus',0);
			$this->db->where ('torderassignment.OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->num_rows();
		}


		function insert_zipcode($data){
			$this->db->trans_begin();


			$citydata = array(
				'CityName'=>$data['Modal_PropertyCity'],
				'StateUID'=>$data['Modal_PropertyState'],
				'CountyUID'=>$data['Modal_PropertyCounty'],
				'ZipCode'=>$data['Modal_PropertyZipcode'],
				'Active'=>1,

			);

			$this->db->insert('mCities',$citydata);

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


		function check_zip_exists($zipcode,$CityName) {


			$this->db->select('*');
			$this->db->from('mCities');
			$this->db->where('ZipCode', $zipcode);
			$this->db->where('CityName', $CityName);
			$query = $this->db->get();
			$num = $query->num_rows();

			if ($num > 0) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

	// Self assign order
		function get_workflow_onholdorder($MenuURL)
		{
			$this->db->select ( '*' );
			$this->db->from ( 'msubmenuworkflowmodules' );
			$this->db->where ('MenuURL',$MenuURL);
			$query = $this->db->get();
			return  $query->row();
		}

		function SelfAssignOrderToUser($OrderUID,$WorkflowUID,$UserUID)
		{
			if($this->check_has_workflow($UserUID,$WorkflowUID)) {

				$assign_data = array(
					'OrderUID' => $OrderUID,
					'WorkflowModuleUID' => $WorkflowUID,
					'AssignedToUserUID' => $UserUID,
					'AssignedDatetime' =>  Date('Y-m-d H:i:s',strtotime("now")),
					'AssignedByUserUID' => $UserUID,
					'WorkflowStatus' => '3',
					'OrderFlag'=>1,
				);

				$is_vendor_login = $this->common_model->is_vendorlogin();

				if($is_vendor_login){
					$assign_data['SendTovendor'] = '1';
				}else{
					$assign_data['SendTovendor'] = '0';
					$assign_data['VendorUID'] = '';
					$assign_data['VendorAssignedByUserUID'] = '';
					$assign_data['VendorAssignedDateTime'] = '';
					$assign_data['VendorCompletedDateTime'] = '';
					$assign_data['QCCompletedDateTime'] = NULL;
					$assign_data['QCAssignedByUserUID'] = NULL;
					$assign_data['QCAssignedToUserUID'] = NULL;
					$assign_data['QCAssignedDateTime'] = NULL;
				}
				$inserted = $this->db->insert('torderassignment',$assign_data);
				if($this->db->affected_rows()>0)
				{
					//insert sla action details for workflow completed start
					if($WorkflowUID == $this->config->item('WorkflowModuleUID')['OrderSearch'])
					{
						if($this->Check_InternolExternol($OrderUID))
						{
							$this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['WorkflowModuleUID'][$WorkflowUID]['Vendor_Assigned'],$UserUID);
						}
						else
						{
							$this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['WorkflowModuleUID'][$WorkflowUID]['Assigned'],$UserUID);
						}
					}
					else
					{
						$this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['WorkflowModuleUID'][$WorkflowUID]['Assigned'],$UserUID);
					}
					//insert sla action details end
					
					$StatusUID = $this->get_order_status($OrderUID);
					if($StatusUID == $this->config->item('keywords')['New Order'] || $StatusUID ==  $this->config->item('keywords')['Reopened Order'] || $StatusUID ==  $this->config->item('keywords')['Order Assigned'] ){
						$data['StatusUID'] = $this->config->item('keywords')['Order Work In Progress'];
						$this->db->where('OrderUID',$OrderUID);
						$this->db->update('tOrders',$data);
					}
					return 1;
				} else {
					return 0;
				}

			}else{
				return 0;
			}
		}
	// self assign orders

		function UpdateSelfAssignOrderToUser($OrderUID,$WorkflowUID,$UserUID)
		{
			$selfassign='';
			$this->db->select('*');
			$this->db->from('torderassignment');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('WorkflowModuleUID',$WorkflowUID);
			$result=$this->db->get()->row();
			if($result->AssignedToUserUID==$UserUID){
				$selfassign='1';
			}else{
				$selfassign='0';
			}


			if($this->check_has_workflow($UserUID,$WorkflowUID)) {
				if($selfassign=='0'){
					$assign_data = array(
						'AssignedToUserUID' => $UserUID,
						'AssignedDatetime' =>  Date('Y-m-d H:i:s',strtotime("now")),
						'AssignedByUserUID' => $UserUID,
						'CompleteDateTime'=>null,
						'WorkflowStatus' => '3',
						'OrderFlag'=>1
					);
				}else{
					$assign_data = array(
						'AssignedToUserUID' => $UserUID,
						'WorkflowStatus' => '3',
						'CompleteDateTime'=>null,
						'OrderFlag'=>1
					);
				}


				$is_vendor_login = $this->common_model->is_vendorlogin();

				if($is_vendor_login){
					$assign_data['SendTovendor'] = '1';
				}else{
					$assign_data['SendTovendor'] = '0';
					$assign_data['VendorUID'] = '';
					$assign_data['VendorAssignedByUserUID'] = '';
					$assign_data['VendorAssignedDateTime'] = '';
					$assign_data['VendorCompletedDateTime'] = '';
					$assign_data['QCCompletedDateTime'] = NULL;
					$assign_data['QCAssignedByUserUID'] = NULL;
					$assign_data['QCAssignedToUserUID'] = NULL;
					$assign_data['QCAssignedDateTime'] = NULL;
				}

				$where['OrderUID'] = $OrderUID;
				$where['WorkflowModuleUID'] = $WorkflowUID;
				$this->db->where($where);
				$inserted = $this->db->update('torderassignment',$assign_data);

				//insert sla action details for workflow completed start
				if($WorkflowUID == $this->config->item('WorkflowModuleUID')['OrderSearch'])
				{
					if($this->Check_InternolExternol($OrderUID))
					{
						$this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['WorkflowModuleUID'][$WorkflowUID]['Vendor_Assigned'],$UserUID);
					}
					else
					{
						$this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['WorkflowModuleUID'][$WorkflowUID]['Assigned'],$UserUID);
					}
				}
				else
				{
					$this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['WorkflowModuleUID'][$WorkflowUID]['Assigned'],$UserUID);
				}
				//insert sla action details end

				if($inserted)
				{
					$StatusUID = $this->get_order_status($OrderUID);
					if($StatusUID == $this->config->item('keywords')['New Order'] || $StatusUID ==  $this->config->item('keywords')['Reopened Order'] || $StatusUID ==  $this->config->item('keywords')['Order Assigned'] ){
						$data['StatusUID'] = $this->config->item('keywords')['Order Work In Progress'];
						$this->db->where('OrderUID',$OrderUID);
						$this->db->update('tOrders',$data);
					}
					return 1;
				} else {
					return 0;
				}

			}else{
				return 0;
			}
		}
	// update self assign unassigned orders

		function check_order_is_assigned($OrderUID, $WorkflowModuleUID = "")
		{
			$this->db->select('*');
			$this->db->from('torderassignment');
			$this->db->where ('torderassignment.OrderUID',$OrderUID);
			$this->db->where ('(torderassignment.AssignedToUserUID IS NOT NULL OR torderassignment.AssignedToUserUID != 0)');
			$this->db->group_by('WorkflowModuleUID');
			$query = $this->db->get();
			return $query->num_rows();
		}

		function check_order_is_assigned_to_workflow($OrderUID, $WorkflowModuleUID = "")
		{
			$this->db->select('*');
			$this->db->from('torderassignment');
			$this->db->where ('torderassignment.OrderUID',$OrderUID);

			if ($WorkflowModuleUID) {
				$this->db->where('torderassignment.WorkflowModuleUID', $WorkflowModuleUID);
				$this->db->where('torderassignment.AssignedToUserUID IS NOT NULL');
			}
			$query = $this->db->get();
			return $query->num_rows();
		}

		function Check_Order_ParticularWorkflowCompleted($OrderUID, $WorkflowUID, $WorkflowStatus)
		{
			$Workflow = implode(',', $WorkflowUID);
			$Status = implode(',', $WorkflowStatus);
			$q = $this->db->query("SELECT * FROM torderassignment WHERE OrderUID = $OrderUID AND WorkflowModuleUID IN ($Workflow) AND WorkflowStatus IN ($Status)")->num_rows();
			return $q;
		}

		function check_is_assignedforreview($OrderUID,$WorkflowModuleUID,$loggedid)
		{
			$this->db->select('AssignedToUserUID');
			$this->db->from('torderassignment');
			$this->db->where ('torderassignment.OrderUID',$OrderUID);
			$this->db->where ('torderassignment.WorkflowModuleUID',$WorkflowModuleUID);
			$this->db->where ('torderassignment.AssignedToUserUID',$loggedid);
			$this->db->where('AssignedToUserUID IS NOT NULL', NULL, FALSE);
			$query = $this->db->get();
			return $query->num_rows();
		}

		function CheckOrderCancelRole($UserUID)
		{
			$this->db->select("OrderCancel");
			$this->db->from('mroles');
			$this->db->join('musers','musers.RoleUID=mroles.RoleUID');
			$this->db->where(array("musers.UserUID"=>$UserUID));
			$query = $this->db->get();
			$res =  $query->row();
			return $res->OrderCancel;
		}

		function check_has_workflow($UserUID,$WorkflowModuleUID){


			$query = $this->db->query("SELECT * FROM `mroles` JOIN musers on musers.RoleUID = mroles.RoleUID  WHERE  musers.UserUID = '".$UserUID."' ");
			$result =  $query->row();

			$has_workflow = FALSE;

			if($result->OrderSearch == '1' && $WorkflowModuleUID ==  $this->config->item('WorkflowModuleUID')['OrderSearch']){
				$has_workflow =  TRUE;
			}
			if($result->OrderTyping == '1' && $WorkflowModuleUID ==  $this->config->item('WorkflowModuleUID')['Typing']){
				$has_workflow =  TRUE;
			}
			if($result->OrderTaxCert == '1'  && $WorkflowModuleUID ==  $this->config->item('WorkflowModuleUID')['TaxCert']){
				$has_workflow =  TRUE;
			}
			if($result->OrderScheduling == '1'  && $WorkflowModuleUID == $this->config->item('WorkflowModuleUID')['Scheduling']){
				$has_workflow =  TRUE;
			}
			if($result->OrderSigning== '1'  && $WorkflowModuleUID == $this->config->item('WorkflowModuleUID')['Signing']){
				$has_workflow =  TRUE;
			}
			if($result->OrderShipping == '1'  && $WorkflowModuleUID == $this->config->item('WorkflowModuleUID')['Shipping']){
				$has_workflow =  TRUE;
			}
			if($result->OrderDocReview == '1'  && $WorkflowModuleUID == $this->config->item('WorkflowModuleUID')['Document_Review']){
				$has_workflow =  TRUE;
			}
			if($result->OrderImpedience== '1'  && $WorkflowModuleUID == $this->config->item('WorkflowModuleUID')['Impediments']){
				$has_workflow =  TRUE;
			}

			return $has_workflow;
		}

		function  GetRoleType($RoleUID){

			$this->db->select('*');
			$this->db->from('mroles');
			$this->db->where('mroles.RoleUID',$RoleUID);
			$query=$this->db->get('')->row();
			return $query->RoleType;


		}

		// Reverse Workflow Functions Starts
		function GetCompletedWorkflows($OrderUID){

			$query=$this->db->query("SELECT * FROM `torderassignment`
				LEFT JOIN `mworkflowmodules` ON `torderassignment`.`WorkflowModuleUID`=`mworkflowmodules`.`WorkflowModuleUID`
				WHERE `torderassignment`.`WorkflowModuleUID`!=4
				AND `torderassignment`.`WorkflowStatus` =  5
				AND `torderassignment`.`AssignedToUserUID`!= ''
				AND `torderassignment`.`OrderUID` =  '$OrderUID'");
			return $query->result();
		}


		//get user from particular group
		function get_user_ingroup($loggedid)
		{
			$grp_id = $this->get_groupuser_id($loggedid);
			$this->db->distinct();
			$this->db->select ( 'mgroups.GroupUID,mgroups.GroupName,UserUID,UserName' );
			$this->db->from ( 'mgroupusers' );
			$this->db->join ( 'musers', 'musers.UserUID = mgroupusers.GroupUserUID');
			$this->db->join ( 'mgroups', 'mgroups.GroupUID = mgroupusers.GroupUID');
			if(count($grp_id) > 0){
				$this->db->where_in ('mgroupusers.GroupUID',$grp_id);
			}
			$this->db->where ('mgroups.Active',1);
			$this->db->group_by('mgroupusers.GroupUserUID');

			$query = $this->db->get();
			return $query->result();
		}
		function get_groupuser_id($loggedid)
		{
			$grp_id = [];
			$this->db->select ( 'mgroups.GroupUID' );
			$this->db->from ( 'mgroupusers' );
			$this->db->join ( 'mgroups', 'mgroups.GroupUID = mgroupusers.GroupUID');
			$this->db->where ('mgroupusers.GroupUserUID',$loggedid);
			$this->db->where ('mgroups.Active',1);
			$this->db->order_by('mgroups.GroupName');

			$query = $this->db->get();
			$groupids =  $query->result_array();
			if(count($groupids) > 0 ){
				foreach ($groupids as $key => $groupid) {
					$grp_id[] = $groupid['GroupUID'];
				}
			}
			return $grp_id;
		}

		function get_groupsname($loggedid = '')
		{
			$this->db->distinct();
			$this->db->select ( 'mgroups.GroupUID,mgroups.GroupName' );
			$this->db->from ( 'mgroupusers' );
			$this->db->join ( 'musers', 'musers.UserUID = mgroupusers.GroupUserUID');
			$this->db->join ( 'mgroups', 'mgroupusers.GroupUID = mgroups.GroupUID');
			$this->db->where ('mgroupusers.GroupUserUID',$loggedid);
			$this->db->where ('mgroups.Active',1);
			$this->db->group_by('mgroupusers.GroupUID');
			$this->db->order_by('mgroups.GroupName');

			$query = $this->db->get();
			return $query->result();
		}


		function get_groups(){
			$this->db->select ( 'mgroups.GroupUID,mgroups.GroupName' );
			$this->db->from ( 'mgroups' );
			$this->db->where ('mgroups.Active',1);
			$this->db->order_by('mgroups.GroupName');
			$query = $this->db->get();

			return $query->result();
		}

		function get_customername()
		{
			$this->db->distinct();
			$this->db->select ('CustomerName,CustomerUID' );
			$this->db->from ('mCustomers' );
			$this->db->order_by('mCustomers.CustomerName');

			$query = $this->db->get();
			return $query->result();
		}


		// for users in assign
		function get_groupusersbyid($groupids = '')
		{
			$this->db->select ( 'mgroups.GroupUID,mgroups.GroupName,UserUID,UserName' );
			$this->db->from ( 'mgroupusers' );
			$this->db->join ( 'musers', 'musers.UserUID = mgroupusers.GroupUserUID');
			$this->db->join ( 'mgroups', 'mgroups.GroupUID = mgroupusers.GroupUID');
			if($groupids){
				$this->db->where_in ('mgroupusers.GroupUID',$groupids);
			}
			$this->db->where ('mgroups.Active',1);
		// $this->db->group_by('mgroupusers.GroupUserUID');
			$query = $this->db->get();
			return $query->result();
		}

		function get_group_allusers($UserUIDs){

			$UID = [];
			if(count($UserUIDs) > 0 ){
				foreach ($UserUIDs as $key => $UserUID) {
					$UID[] = $UserUID->UserUID;
				}
			}

			$UserUID = implode(',', $UID);

			if($UserUID !=''){

				$where = 'AND a.UserUID IN ('.$UserUID.')';
				$query = $this->db->query("select * from (select * from musers)a LEFT JOIN(select * from mroles where RoleType IN (1,2,3,4,5,6))b ON a.RoleUID=b.RoleUID where b.RoleUID IS NOT NULL UNION ALL select * from (select * from musers)a LEFT JOIN(select * from mroles where RoleType=7)b ON a.RoleUID=b.RoleUID where b.RoleType IS NOT NULL ".$where." ORDER BY UserName ASC");

				return  $query->result();
			}
			return array();
		}

		function save_reverse_workflow($Workflow_selected, $Workflows, $OrderUID, $Remarks)
		{
			date_default_timezone_set('US/Eastern');
			$is_any_workflowreassigned=0;
			foreach ($Workflows as $key => $workflow) {
				if($Workflow_selected[$workflow->WorkflowModuleUID]!='')
				{

					$data=[];
					$is_workflowassigned=$this->check_previous_assigned_workflow($workflow->WorkflowModuleUID, $OrderUID);
					$data['AssignedToUserUID']= $Workflow_selected[$workflow->WorkflowModuleUID]!="" ? $Workflow_selected[$workflow->WorkflowModuleUID]: NULL;
					$data['AssignedByUserUID']=$this->loggedid;
					$data['AssignedDatetime']=date('Y-m-d H:i:s');
					$data['SendToVendor']=0;
					$data['VendorUID']=NULL;
					$data['VendorDueDate']=NULL;
					$data['VendorAssignedByUserUID']=NULL;
					$data['VendorAssignedDateTime']=NULL;
					$data['VendorCompletedDateTime']=NULL;
					$data['QCCompletedDateTime']=NULL;
					$data['QCAssignedByUserUID']=NULL;
					$data['QCAssignedToUserUID']=NULL;
					$data['QCAssignedDateTime']=NULL;
					$data['Workflowstatus']=0;
					if(count($is_workflowassigned)>0)
					{
						$is_any_workflowreassigned++;
						$this->db->set($data)->where(array('OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$workflow->WorkflowModuleUID))
						->update('torderassignment');
					}
					else
					{
						$is_any_workflowreassigned++;
						$data['OrderUID']=$OrderUID;
						$data['WorkflowModuleUID']=$workflow->WorkflowModuleUID;
						$this->db->insert('torderassignment', $data);
					}

					if($Workflow_selected[$workflow->WorkflowModuleUID]!="")
					{
						$UserName=$this->db->get_where('musers', array('UserUID'=>$data['AssignedToUserUID']))->row()->UserName;
						$Note= $workflow->WorkflowModuleName . ' Module Reassigned to ' . $UserName;
													 //order reassignment
						$data1['ModuleName']=$workflow->WorkflowModuleName.' '.'Reverse Workflow_add';
						$data1['IpAddreess']=$_SERVER['REMOTE_ADDR'];
						$data1['DateTime']=date('y-m-d H:i:s');
						$data1['OrderUID']=$OrderUID;
						$data1['TableName']='torderassessment';
						$data1['UserUID']=$this->session->userdata('UserUID');
						$this->common_model->Audittrail_insert($data1);

					}
					else
					{
						$Note= $workflow->WorkflowModuleName . ' Module Unassigned';
					}
					// $this->db->insert('tordernotes', array('SystemNotesUID' => 0,
					// 								   'RoleType'=>'1,2,3,4,5,6,7,9,11,12',
					// 								   'CreatedByUserUID'=>$this->loggedid,
					// 								   'CreatedOn'=>date('Y-m-d H:i:s'),
					// 								   'OrderUID'=>$OrderUID,
					// 								   'SectionUID'=>1,
					// 								   'Note'=>$Note));

				}

			}
			if($is_any_workflowreassigned>0)
			{
				$status = $this->config->item('keywords')['Order Work In Progress'];
				$this->db->where('OrderUID', $OrderUID);
				$this->db->update('tOrders', array('StatusUID'=>$status));

				$this->db->insert('tordernotes', array('SystemNotesUID' => 0, 'SectionUID'=>13, 'RoleType'=>'1,2,3,4,5,6,7,9,11,12', 'CreatedByUserUID'=>$this->loggedid, 'CreatedOn'=>date('Y-m-d H:i:s'), 'OrderUID'=>$OrderUID, 'Note'=>$Remarks));
			}

			return array('status'=>'Success', 'msg'=>'Order Reverse Successfull');
		}


		function check_previous_assigned_workflow($WorkflowModuleUID, $OrderUID)
		{
			$this->db->select('WorkflowModuleUID')->from('torderassignment');
			$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
			$this->db->where('OrderUID', $OrderUID);
			$query=$this->db->get();
			return $query->result();
		}

		function get_reverse_workflow_users($loggedid)
		{
			$user_in_groups = $this->get_user_ingroup($loggedid);

			$groupin_user_logged = $this->get_groupuser_id($loggedid);

			$user_groups =[];
			$customers =[];
			$customer_in_groups = [];



			if (in_array($this->session->userdata('RoleType'),array('1','2','3','4','5','6')) == 0) {

				$user_groups = $this->get_groupsname($loggedid);
				$customers = $this->get_customer_ingroup($groupin_user_logged);

				$customer_in_groups = count($groupin_user_logged) > 0 ? $this->get_customer_ingroup($groupin_user_logged[0]): [];


			}else{

				$user_groups = $this->get_groups();
				$customers = $this->get_customername();
				$customer_in_groups = count($user_groups) > 0 ? $this->get_customer_ingroup($user_groups[0]->GroupUID): [];

			}


			if(count($user_groups) >0){

				$GroupUIDs[0]=0;
				foreach ($user_groups as $key => $value) {
					$GroupUIDs[]=$value->GroupUID;
				}

				$group_users = $this->get_groupusersbyid($GroupUIDs);
				$assign_users = $this->get_group_allusers($group_users);


			}else{

				$assign_users = [];

			}

			return $assign_users;

		}

		function get_role_byrole_uid($RoleUID)
		{
			$this->db->select ( '*' );
			$this->db->from ( 'mroles' );

			$this->db->where ('mroles.RoleUID',$RoleUID);

			$query = $this->db->get();

			return $query->row();

		}





		function Addtordermonitordata($OrderUID,$loggedid){
			$currentdate=date('Y-m-d H:i:s');

			$this->db->select('*');
			$this->db->from('tordermonitor');
			$this->db->where('UserUID',$loggedid);
			$query = $this->db->get();
			$value= $query->num_rows();

			$data=array('OrderUID'=>$OrderUID,
				'UserUID'=>$loggedid,
				'ActivityTime'=>$currentdate,
			);
			$data1=array(
				'ActivityTime'=>$currentdate,
				'OrderUID'=>$OrderUID,

			);

			if($value > 0){
				$this->db->where('UserUID',$loggedid);
				$this->db->update('tordermonitor',$data1);

			}else{

				$this->db->insert('tordermonitor',$data);
			}


		}

		function Getworkflow($OrderUID){
			$query = $this->db->query("SELECT COUNT(*) as  TotalWorkflow FROM `torderassignment` WHERE  OrderUID = '$OrderUID'  AND WorkflowModuleUID IN (1,2,3) ");

			return $query->row();
		}

		function Getworkflowbyworkflowmoduleuid($WorkflowModuleUID)
		{
			return $this->db->get_where('mworkflowmodules', array('WorkflowModuleUID'=>$WorkflowModuleUID))->row();
		}

		function GetOptionalWorkflowsByOrderUID($OrderUID)
		{
			$this->db->select('Distinct torderoptionalworkflows.*', false)->from('torderoptionalworkflows');
			$this->db->join('tOrders', 'torderoptionalworkflows.OrderUID=tOrders.OrderUID AND torderoptionalworkflows.SubProductUID=tOrders.SubProductUID', 'inner');
			$this->db->where('torderoptionalworkflows.OrderUID', $OrderUID);
			$this->db->order_by('WorkflowModuleUID', 'ASC');
			return $this->db->get()->result();
		}

		function GetCustomerWorkflowsByOrderUID($OrderUID)
		{
			$this->db->select('Distinct mcustomerworkflowmodules.*', false)->from('mcustomerworkflowmodules');
			$this->db->join('tOrders', 'mcustomerworkflowmodules.CustomerUID=tOrders.CustomerUID AND mcustomerworkflowmodules.SubProductUID=tOrders.SubProductUID', 'inner');
			$this->db->where('OrderUID', $OrderUID);
			$this->db->group_by('WorkflowModuleUID');
			$this->db->order_by('WorkflowModuleUID', 'ASC');
			return $this->db->get()->result();
		}

		function checkandchangeorderstatus($OrderUID, $tOrders)
		{
			if ($tOrders->StatusUID==$this->config->item('keywords')['Draft Complete']) {
				$this->db->where('OrderUID', $OrderUID);
				$this->db->update('tOrders', array('StatusUID'=>$this->config->item('keywords')['Partial Draft Complete']));
			}

			if ($tOrders->StatusUID==$this->config->item('keywords')['Order Completed']) {
				$this->db->where('OrderUID', $OrderUID);
				$this->db->update('tOrders', array('StatusUID'=>$this->config->item('keywords')['Partial Review Complete']));

				//Change Review
				$this->db->where('OrderUID', $OrderUID);
				$this->db->where( 'WorkflowModuleUID',4);
				$this->db->update('torderassignment', array('WorkflowStatus'=>3));

			}
		}

		function GetAssigneddworkflow($OrderUID){
			$query = $this->db->query("SELECT COUNT(*) as  AssignedWorkflow FROM `torderassignment` WHERE  OrderUID = '$OrderUID'  AND WorkflowModuleUID IN (1,2,3) AND WorkflowStatus='0'");
			return $query->row();

		}

		function GetAssignedDate($OrderUID){
			$query = $this->db->query("SELECT MAX(DATE_FORMAT(AssignedDatetime, '%Y-%m-%d')) as  AssignedDatetime FROM `torderassignment` WHERE  OrderUID = '$OrderUID'  AND WorkflowModuleUID IN (1,2,3) AND WorkflowStatus='0'");
			return $query->row();
		}

		/*check vendor login*/
		function is_vendorlogin(){
			$loggedid = $this->session->userdata('UserUID');

			$query = $this->db->query("SELECT EXISTS(SELECT RoleType FROM `mroles` JOIN musers on `musers`.`RoleUID` = `mroles`.`RoleUID`  WHERE `musers`.`UserUID` = '".$loggedid."' AND RoleType IN ('13','14') LIMIT 1) AS is_vendor");
			$result =  $query->row();


			if($result->is_vendor == 1){
				return true;
			}else{
				return false;
			}
		}


		function get_logged_details(){
			$loggedid = $this->session->userdata('UserUID');
			$query = $this->db->query("SELECT musers.VendorUID,musers.UserUID,UserName,RoleName,RoleType FROM `mroles` JOIN musers on `musers`.`RoleUID` = `mroles`.`RoleUID`  WHERE `musers`.`UserUID` = '".$loggedid."' AND RoleType IN ('13','14') LIMIT 1");
			$result =  $query->row();
			return $result;
		}

		function get_vendor_workflow($VendorUID){
			$query = $this->db->query("SELECT mworkflowmodules.WorkflowModuleUID,mworkflowmodules.WorkflowModuleName,CanIndependentWorkflowModule,DependentWorkflowModule,IsExternalAbstraction FROM mvendorsworkflowmodules JOIN mvendors ON mvendors.VendorUID = mvendorsworkflowmodules.VendorUID JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = mvendorsworkflowmodules.WorkflowModuleUID WHERE mvendorsworkflowmodules.VendorUID = '".$VendorUID."' GROUP BY mworkflowmodules.WorkflowModuleUID ");
			return $result = $query->result();
		}

		function get_vendor_assigned_workflow($VendorUID,$OrderUID){
			$query = $this->db->query("SELECT mworkflowmodules.WorkflowModuleUID, mworkflowmodules.WorkflowModuleName FROM mworkflowmodules JOIN torderassignment ON torderassignment.WorkflowModuleUID = mworkflowmodules.WorkflowModuleUID WHERE torderassignment.VendorUID = '".$VendorUID."' AND torderassignment.SendToVendor = '1' AND torderassignment.Workflowstatus != '5' AND  torderassignment.OrderUID='".$OrderUID."' GROUP BY mworkflowmodules.WorkflowModuleUID ");
			return $result = $query->result();
		}

		function  GetRoleTypebyuid($RoleUID){

			$this->db->select('*');
			$this->db->from('mroles');
			$this->db->where('mroles.RoleUID',$RoleUID);
			return $this->db->get('')->row();
		}
		function GetExceptionOrderStatus($OrderUID){
			$this->db->select('*');
			$this->db->from('tOrders');
			$this->db->where('StatusUID',49);
			$this->db->where('OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->num_rows();
		}

		/*function getCustomerRequestOrderCount()
		{
			$loggedid = $this->session->userdata('UserUID');
			$status[0] = $this->config->item('keywords')['Exception Raised'];

			$groupin_user_logged = $this->get_user_group_id($loggedid);
			$CustomerUIDs = $this->get_customer_ingroup($groupin_user_logged);

		//$status[1] = $this->config->item('keywords')['Review In Progress'];

			/*$status[2] = $this->config->item('keywords')['Order Assigned'];
			$status[3] = $this->config->item('keywords')['Order Work In Progress'];
		//$status[2] = $this->config->item('keywords')['Review Complete'];

			$cus_id =[];
			foreach ($CustomerUIDs as $key => $CustomerUID) {
				$cus_id[] = $CustomerUID->CustomerUID;
			}

			$Workflowstatus[0] = 0;
			$Workflowstatus[1] = 3;
			$Workflowstatus[2] = 5;

			$this->db->select('count(DISTINCT(OrderNumber)) AS ExceptionOrderCount');
			$this->db->select ("tOrders.OrderNumber,tOrders.OrderUID,tOrders.StatusUID,tOrders.CustomerUID,CustomerName,OrderTypeName,StatusColor,PriorityName,mOrderStatus.StatusName,mOrderStatus.StatusColor, tOrders.OrderUID,texceptions.RaisedByUserUID,texceptions.RaisedOn,musers.UserName,mOrderPriority.PriorityUID,LoanNumber,PropertyAddress1,tOrders.PropertyStateCode,tOrders.PropertyCountyName");
			$this->db->select('DATE_FORMAT(tOrders.OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);
			$this->db->from ( 'texceptions' );
			$this->db->join ( 'tOrders', 'texceptions.OrderUID = tOrders.OrderUID' , 'left' );
			$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
			$this->db->join ( 'mOrderTypes', 'mOrderTypes.OrderTypeUID = tOrders.OrderTypeUID' , 'left' );
			$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
			$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
			$this->db->join ( 'musers', 'musers.UserUID = texceptions.RaisedByUserUID' , 'left' );


		// if(in_array($this->session->userdata('RoleType'),array(7)))
		// {
		//   $userid = $this->session->userdata('UserUID');
		//   $this->db->join('torderassignment','tOrders.OrderUID = torderassignment.OrderUID');
		//   $this->db->where("torderassignment.AssignedToUserUID = $userid AND torderassignment.WorkflowModuleUID = 4");
		//   $this->db->where_in ('torderassignment.Workflowstatus',$Workflowstatus);
		// }

			// if(tOrders.StatusUID != 0){
			if($this->common_model->GetExceptionQueue() == 1)
			{
				$userid = $this->session->userdata('UserUID');
				$this->db->join('torderassignment','tOrders.OrderUID = torderassignment.OrderUID');
				$this->db->where("torderassignment.AssignedToUserUID = $userid AND torderassignment.WorkflowModuleUID = 4");
				$this->db->where_in ('torderassignment.Workflowstatus',$Workflowstatus);
			}
			else if($this->common_model->GetExceptionQueue() == 2)
			{
				$this->db->join('torderassignment','tOrders.OrderUID = torderassignment.OrderUID');
			 // $this->db->where("torderassignment.AssignedToUserUID = torderassignment.WorkflowModuleUID = 4");
				$this->db->where_in ('torderassignment.Workflowstatus',$Workflowstatus);
			}
			// }

			if($cus_id){
				$this->db->where_in ('tOrders.CustomerUID',$cus_id);
			}
			$this->db->where_in ('tOrders.StatusUID',49);
			$this->db->where_in ('texceptions.IsClear',0);
			$this->db->group_by('tOrders.OrderUID');
			$query = $this->db->get();
			$res = $query->result();
			$Count = 0;
			foreach ($res as $key => $value){
				$Count += $value->ExceptionOrderCount;
			}
			return $Count;
		}*/

		function getCustomerRequestOrderCount()
		{
				$loggedid = $this->session->userdata('UserUID');
			$status[0] = $this->config->item('keywords')['Exception Raised'];

			$groupin_user_logged = $this->get_user_group_id($loggedid);
			$CustomerUIDs = $this->get_customer_ingroup($groupin_user_logged);

			$cus_id =[];
			foreach ($CustomerUIDs as $key => $CustomerUID) {
				$cus_id[] = $CustomerUID->CustomerUID;
			}

			$Workflowstatus[0] = 0;
			$Workflowstatus[1] = 3;
			$Workflowstatus[2] = 5;

			$this->db->select('count(DISTINCT(OrderNumber)) AS ExceptionOrderCount');
			$this->db->select ("tOrders.OrderNumber,tOrders.OrderUID,tOrders.StatusUID,tOrders.CustomerUID,CustomerName,OrderTypeName,StatusColor,PriorityName,mOrderStatus.StatusName,mOrderStatus.StatusColor, tOrders.OrderUID,texceptions.RaisedByUserUID,texceptions.RaisedOn,musers.UserName,mOrderPriority.PriorityUID,LoanNumber,PropertyAddress1,tOrders.PropertyStateCode,tOrders.PropertyCountyName");
			$this->db->select('DATE_FORMAT(tOrders.OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);
			$this->db->from ( 'texceptions' );
			$this->db->join ( 'tOrders', 'texceptions.OrderUID = tOrders.OrderUID' , 'left' );
			$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
			$this->db->join ( 'mOrderTypes', 'mOrderTypes.OrderTypeUID = tOrders.OrderTypeUID' , 'left' );
			$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
			$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
			$this->db->join ( 'musers', 'musers.UserUID = texceptions.RaisedByUserUID' , 'left' );

			if($this->common_model->GetExceptionQueue() == 1)
			{
				$userid = $this->session->userdata('UserUID');
				$this->db->join('torderassignment','tOrders.OrderUID = torderassignment.OrderUID');
				$this->db->where("torderassignment.AssignedToUserUID = $userid AND torderassignment.WorkflowModuleUID = 4");
				$this->db->where_in ('torderassignment.Workflowstatus',$Workflowstatus);
			}
			else if($this->common_model->GetExceptionQueue() == 2)
			{
				$this->db->join('torderassignment','tOrders.OrderUID = torderassignment.OrderUID');
				$this->db->where_in ('torderassignment.Workflowstatus',$Workflowstatus);
			}

			if($cus_id)
			{
			 $this->db->where_in ('tOrders.CustomerUID',$cus_id);
			}
			/*FOR SUPERVISOR CHECK*/
			if (in_array($this->RoleType, $this->config->item('SubProduct_RoleType'))) {
				$UserSubProducts = $this->common_model->_get_Subproducts_bylogin($this->loggedid);
				if($UserSubProducts): $this->db->where('tOrders.SubProductUID IN ('.$UserSubProducts.')', null, false); else: $this->db->where('tOrders.SubProductUID IN (0)', null, false); endif;
			}

			$this->db->where_in ('tOrders.StatusUID',49);
			$this->db->where_in ('texceptions.IsClear',0);
			$this->db->group_by('tOrders.OrderUID');
			$query = $this->db->get();
			$res = $query->result();
			$Count = 0;
			foreach ($res as $key => $value){
				$Count += $value->ExceptionOrderCount;
			}
			return $Count;
		}

		function GetCustomerRequestQueue()
		{
			$this->RoleUID = $this->session->userdata('RoleUID');
			$query = $this->db->query("SELECT CustomerRequestQueue FROM `mroles` WHERE RoleUID = '".$this->RoleUID."' ");
			$res =  $query->row();
			return $res->CustomerRequestQueue;
		}

		function check_workflow($OrderUID,$WorkflowModuleUID){
			$this->db->select('*');
			$this->db->from('torderassignment');
			$this->db->where('WorkflowModuleUID',$WorkflowModuleUID);
			$this->db->where('Workflowstatus',3);
			$this->db->where('OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->num_rows();
		}

		function check_exception_status($OrderUID){
			$this->db->select('*');
			$this->db->from('texceptions');
			$this->db->where('IsNotesSeen',0);
			$this->db->where('IsClear',1);
			$this->db->where('OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->num_rows();
		}
		function check_revieworder_is_assignedtouser($OrderUID){
			$loggedid = $this->session->userdata('UserUID');
			$Completed=$this->db->query("SELECT COUNT(*) as  CompletedWorkflow FROM `torderassignment` WHERE  OrderUID = '$OrderUID'  AND WorkflowModuleUID IN (1,2,3) AND WorkflowStatus IN(5) ")->num_rows();

			if($Completed > 0){
				$this->db->select('*');
				$this->db->from('torderassignment');
				$this->db->join('texceptions','texceptions.OrderUID=torderassignment.OrderUID','inner');
				$this->db->join('tOrders','tOrders.OrderUID=torderassignment.OrderUID','inner');
				$this->db->where ('torderassignment.AssignedToUserUID',$loggedid);
				$this->db->where ('torderassignment.OrderUID',$OrderUID);
				$this->db->where ('tOrders.StatusUID',35);
				$this->db->where ('texceptions.IsClear',0);
				$query = $this->db->get();
				return $query->num_rows();
			}else{
				return '0';
			}
		}

		function GetRole_Details($RoleUID)
		{
			$this->db->select("*");
			$this->db->from('mroles');
			$this->db->where(array("mroles.RoleUID"=>$RoleUID));
			$query = $this->db->get();
			return $query->row();
		}


		function get_vendorprioritytime_for_duedate($VendorUID,$SubProductUID,$PriorityUID){
			$this->db->select ( '*' );
			$this->db->from ( 'mvendorpriority' );
			$this->db->where ('PriorityUID',$PriorityUID);
			$this->db->where ('SubProductUID',$SubProductUID);
			$this->db->where ('VendorUID',$VendorUID);
			$query = $this->db->get();
			return  $query->row();
		}

		function GetDocumentPermissionType()
		{
			$query = $this->db->get('mdocpermissiontype');
			return $query->result();
		}

		function  check_order_completed($OrderUID,$VendorUID,$WorkflowModuleUID){
			$this->db->select ( '*' );
			$this->db->from ( 'torderassignment' );
			$this->db->where (array('torderassignment.WorkflowModuleUID !='=>'4','torderassignment.WorkflowModuleUID'=>$WorkflowModuleUID,'torderassignment.WorkflowStatus'=>'5','torderassignment.SendToVendor'=>'1','torderassignment.VendorUID'=>$VendorUID,'torderassignment.OrderUID'=>$OrderUID));
			$this->db->where("(torderassignment.QCCompletedDateTime IS NULL OR torderassignment.QCCompletedDateTime='0000-00-00 00:00:00')");
			$query = $this->db->get();
			return  $query->num_rows();

		}


		function get_vendor_workflowfororder($OrderUID,$VendorUID){

			$query = $this->db->query("SELECT * FROM (`torderassignment`) LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID  WHERE `torderassignment`.`WorkflowModuleUID` != '4' AND `torderassignment`.`WorkflowStatus` = '5' AND `torderassignment`.`SendToVendor` = '1' AND `torderassignment`.`VendorUID`  = '".$VendorUID."'  AND (torderassignment.QCCompletedDateTime IS NULL OR torderassignment.QCCompletedDateTime='0000-00-00 00:00:00') AND `torderassignment`.`OrderUID` = '".$OrderUID."' GROUP BY torderassignment.WorkflowModuleUID ");
			return  $query->result();
		}

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

		function check_qc_assigned($OrderUID,$VendorUID,$WorkflowModuleUID)
		{
			$loggedid = $this->session->userdata('UserUID');
			$this->db->select ( '*' );
			$this->db->from ( 'torderassignment' );
			$this->db->where (array('torderassignment.QCAssignedToUserUID'=>$loggedid,'torderassignment.WorkflowModuleUID !='=>'4','torderassignment.WorkflowModuleUID '=>$WorkflowModuleUID,'torderassignment.WorkflowStatus'=>'5','torderassignment.SendToVendor'=>'1','torderassignment.VendorUID'=>$VendorUID,'torderassignment.QCCompletedDateTime'=>null,'torderassignment.OrderUID'=>$OrderUID));
			$query = $this->db->get();
			return  $query->num_rows();
		}

		function GetVendorPricingQueue()
		{
			$this->RoleUID = $this->session->userdata('RoleUID');
			$query = $this->db->query("SELECT VendorPricing FROM `mroles` WHERE RoleUID = '".$this->RoleUID."' ");
			$res =  $query->row();
			return $res->VendorPricing;
		}

		function GetVendorUIDbyWorkflow($workflowModuleUID,$OrderUID){
			$this->db->select ( 'VendorUID' );
			$this->db->from ( 'torderassignment' );
			$this->db->where (array('WorkflowModuleUID '=>$workflowModuleUID,'SendToVendor'=>'1','OrderUID'=>$OrderUID));
			$query = $this->db->get();
			//print $this->db->last_query();
			return  $query->row();

		}

		function GetRolePermissions($RoleUID){
			$this->db->select('*');
			$this->db->from('mroles');
			$this->db->where('RoleUID',$RoleUID);
			$result= $this->db->get()->row();
			return $result;
		}

		function vendor_completed_status_order($OrderUID,$VendorUID){
			$query = $this->db->query("SELECT GROUP_CONCAT( DISTINCT (WorkflowModuleName)) AS WorkflowModuleName FROM tOrders LEFT JOIN torderassignment ON tOrders.OrderUID = torderassignment.OrderUID LEFT JOIN mworkflowmodules ON torderassignment.WorkflowModuleUID = mworkflowmodules.WorkflowModuleUID WHERE WorkflowStatus = 5 AND tOrders.OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID IN ( SELECT WorkflowModuleUID FROM torderassignment WHERE OrderUID = $OrderUID AND SendToVendor = '1' AND VendorUID = $VendorUID AND ( QCCompletedDateTime IS NOT NULL OR torderassignment.QCCompletedDateTime != '0000-00-00 00:00:00' ))");
			$res = $query->row();
			return $res;
		}

		function is_abstractorassign($OrderUID){
			$this->db->select('*');
			$this->db->from('torderassignment');
			$this->db->where('SelfManualAssign','External');
			$this->db->where('WorkflowModuleUID','1');
			$this->db->where('OrderUID',$OrderUID);
			$assign=$this->db->get()->num_rows();
			if($assign > 0){
				$this->db->select('*');
				$this->db->from('torderassignment');
				$this->db->where('WorkflowStatus','5');
				$this->db->where('WorkflowModuleUID','1');
				$this->db->where('OrderUID',$OrderUID);
				$query=$this->db->get()->num_rows();

				if($query > 0){
					return '0';
				}else{
					return $assign;
				}

			}else{
				return $assign;
			}
		}

		function GetAbstractorStatusReason(){
			$this->db->select('*');
			$this->db->from('MAbstractorStatusReason');
			$result= $this->db->get()->result();
			return $result;
		}
	/**
	*@description Function to get overridereasons.
	*
	* 
	* 
	* @throws no exception
	* @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
	* @return 
	* @since 4 April 2020
	* @version overridereasons
	*
	*/
		function Get_moverridereasons(){
			$this->db->select('*');
			$this->db->from('moverridereasons');
			$result= $this->db->get()->result();
			return $result;
		}
	/*End*/
		function Abstracter_diff($newvalue,$oldvalue,$data1){
			$data1=$data1;
			$data1['NewValue']=(array_diff_assoc($newvalue, $oldvalue));
			$data1['OldValue']=(array_diff_assoc($oldvalue, $newvalue));

			foreach($data1['NewValue'] as $key=>$value){
				$data1['NewValue']=$value;
				$data1['FieldUID']=$key;

				$FieldUID=$this->common_model->GetFieldUID($data1['FieldUID'],$data1['TableName']);
				if($FieldUID!=''){
					$data1['FieldUID']=isset($FieldUID)?$FieldUID:'';

				}else{
					$ele='';
					$ele['FieldName']=$key;
					$ele['FieldKeyword']='%%'.$key.'%%';
					$ele['TableName']=$data1['TableName'];

					$res = $this->db->insert('mfields',$ele);


					$FieldUID=$this->common_model->GetFieldUID($data1['FieldUID'],$data1['TableName']);
					$data1['FieldUID']=isset($FieldUID)?$FieldUID:$key;
				}

				$data1['OldValue']=$oldvalue[$key];
				$this->common_model->Abstracter_insert($data1);
			}

		}

		function Abstracter_insert($data){
		// echo '<pre>';print_r($data['FieldUID']);exit;
			$oldvalue='';
			if($data['FieldUID']=='36'){
				$this->db->select('*');
				$this->db->from('mCounties');
				$this->db->where('CountyUID',$data['OldValue']);
				$oldstate=$this->db->get()->row();
				$oldvalue=$oldstate->StateUID;
				$data1['ModuleName']=$data['ModuleName'];
				$data1['AbstractorUID']=$data['AbstractorUID'];
				$data1['FieldUID']='11';
				$data1['OldValue']=$oldvalue;
				$data1['NewValue']=$data['StateUID'];
				$data1['OrderTypeUID']=$data['OrderTypeUID'];
				$data1['ModifiedBy']=$data['ModifiedBy'];
				$data1['ModifiedDate']=$data['ModifiedDate'];
				$data1['CountyUID']=$data['CountyUID'];
				$data1['TableName']=$data['TableName'];
					// echo '<pre>';print_r($data1);exit;
				$this->db->insert('mabstractorhistory',$data1);
			}
			$this->db->insert('mabstractorhistory',$data);


		}

	function GetCurrentQueueStatus($OrderUID){
			$search='';
			$type='';
			$tax='';
			$review='';
			$Orderstatus='';
			$currentqueue='';

			//search
			$this->db->select('*');
			$this->db->from('torderassignment');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('WorkflowModuleUID',1);
			$search= $this->db->get()->row();
			//tax
			$this->db->select('*');
			$this->db->from('torderassignment');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('WorkflowModuleUID',2);
			$type= $this->db->get()->row();
					//tax
			$this->db->select('*');
			$this->db->from('torderassignment');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('WorkflowModuleUID',3);
			$tax= $this->db->get()->row();
					 //orderStatus
			$this->db->select('*');
			$this->db->from('tOrders');
			$this->db->where('OrderUID',$OrderUID);
			$Orderstatus= $this->db->get()->row();


			if($search->WorkflowStatus!=5){
				if($Orderstatus->IsInhouseExternal!=0){
					$currentqueue='External Search';
				}else{
					$currentqueue='Internal Search';
				}
			}else{
				if($search->SendToVendor=='1' && $search->QCCompletedDateTime==''){
					$currentqueue='Search Qc In Progress';
				}else{
					if($type->WorkflowStatus!='5'){
						$currentqueue='Typing';
					}else{
						if($type->SendToVendor=='1' && $type->QCCompletedDateTime==''){
							$currentqueue='Typing Qc In Progress';
						}else{
							if($tax->WorkflowStatus!='5'){
								$currentqueue='Review Pending Tax';
							}else{
								if($tax->SendToVendor=='1' && $tax->QCCompletedDateTime==''){
									$currentqueue='Tax Qc In Progress';
								}else{
									$currentqueue='Review';

								}
							}
						}
					}
				}
			}

			$DraftComplete = $this->config->item('keywords')['Draft Complete']; 
			// echo '<pre>';print_r($DraftComplete);exit; 20
			$PartialDraftComplete = $this->config->item('keywords')['Partial Draft Complete'];
			// echo '<pre>';print_r($PartialDraftComplete);exit; 19
			$ReviewInProgress = $this->config->item('keywords')['Review In Progress'];
			// echo '<pre>';print_r($ReviewInProgress);exit; 35

			if($Orderstatus->StatusUID== $DraftComplete || $Orderstatus->StatusUID== $PartialDraftComplete || $Orderstatus->StatusUID== $ReviewInProgress){
				$currentqueue='Review In Progress';
			}

				if($Orderstatus->StatusUID=='39'){
				$currentqueue='Tax';
			}
			if($Orderstatus->StatusUID=='100'){
				 $currentqueue='Completed';
			}
			if($Orderstatus->StatusUID=='110'){
				$currentqueue='Cancelled';
			}
			if($Orderstatus->StatusUID=='49'){
				 $currentqueue='Exception Raised';
			}
			if($Orderstatus->StatusUID=='40'){
				$currentqueue='Printing';
			}

			/* @desc Check Cancel Pending Request @author Yagavi G <yagavi.g@avanzegroup.com> @since Sept 3rd 2020 */
			$PendingCancelRequest = $this->CheckOrderCancelPendingRequest($OrderUID);
			if(!empty($PendingCancelRequest)){
				$currentqueue = 'Client Cancellation Request Pending';
			}
			return $currentqueue;
		}

		function GetUserEmailFromExceptionList($OrderUID){
			$loggedid = $this->session->userdata('UserUID');
			$this->db->select("*");
			$this->db->from('texceptions');
			$this->db->join ( 'tOrders', 'tOrders.OrderUID = texceptions.OrderUID');
			$this->db->join ( 'mexceptions', 'mexceptions.ExceptionUID = texceptions.ExceptionUID' , 'left' );
			$this->db->join ( 'musers', 'musers.UserUID = texceptions.RaisedByUserUID' , 'left' );
			$this->db->join ( 'mroles', 'mroles.RoleUID = musers.RoleUID');
			$this->db->where(array("texceptions.OrderUID"=>$OrderUID,'RoleType'=>'8'));
			$this->db->order_by('ExceptionSNo','DESC');
			$query = $this->db->get();
			return $query->row();
		}

		function org_default_copycost()
		{
			$this->db->select_max("DefaultAbstractorCopyCost");
			$this->db->limit(1);
			$query = $this->db->get('morganizations')->row();
			return $query->DefaultAbstractorCopyCost;
		}

		function get_order_status($OrderUID){
			$this->db->select('StatusUID');
			$this->db->from('tOrders');
			$this->db->where('OrderUID', $OrderUID);
			return $this->db->get()->row()->StatusUID;
		}
	/**
	*@description Function to get vendor designation List.
	*
	* 
	* 
	* @throws no exception
	* @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
	* @return 
	* @since Monday 9 March 2020
	* @version Vendor Designation
	*
	*/
		function GetDesignation(){
		$this->db->select('*');
		$this->db->from('mDesignation');
		$this->db->where('Active',1); //@Desc Where active only added @Author Jain On Apr 16 2020 
		$result= $this->db->get()->result();
		return $result;
		}


		public function IsAbstractorAssigned($OrderUID)
		{
			$this->db->select('*');
			$this->db->from('torderassignment');
			$this->db->where('WorkflowModuleUID', 1);
			$this->db->where('OrderUID', $OrderUID);
			$this->db->where('SelfManualAssign', 'External');
			$this->db->where('WorkflowStatus !=5', NULL, false);
			return $this->db->get()->num_rows();
				// return $query->result();
		}


		public function IsAbstractorAssignedSearch($OrderUID)
		{
			$this->db->select('*');
			$this->db->from('torderabstractor');
			$this->db->where('OrderUID', $OrderUID);
			$this->db->where('OrderStatus !=5', NULL, false);
			return $this->db->get()->num_rows();
				// return $query->result();
		}
		public function GetAbstractorOrderUID($OrderUID)
		{
			$this->db->select('AbstractorOrderUID')->from('torderabstractor');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->order_by('torderabstractor.AbstractorOrderUID', 'DESC');
			return $this->db->get()->row()->AbstractorOrderUID;
		}

		/*Desc Get all AbstractorOrderUID @Author Jainulabdeen @Since May 20 2020*/
		public function GetAbstractorsOrderUID($OrderUID)
		{
			$this->db->select('AbstractorOrderUID')->from('torderabstractor');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where('OrderStatus !=',5); //@Desc get not completed data only @Author Jainulabdeen @Updated on July 24 2020 
			$this->db->order_by('torderabstractor.AbstractorOrderUID', 'DESC');
			return $this->db->get()->result();
		}

/*	public function IsAbstractorFeeApproved($OrderUID)
	{
			$this->db->select('AbstractorOrderUID')->from('torderabstractor');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->where_not_in('ApprovalStatus', array(3,4));
			return $this->db->get()->num_rows();
	}
*/

	public function IsAbstractorFeeNotApproved($OrderUID)
	{
		$this->db->select('ApprovalUID')->from('torderapprovals');
		$this->db->where('OrderUID',$OrderUID);
		$this->db->where("(ApprovalFunction = 'AbstractorPricingOverride' OR  ApprovalFunction = 'AbstractorPricingAdjustments')");
		$this->db->where('ApprovalStatus', 0);
		return $this->db->get()->num_rows();
	}

	public function IsAbstractorFeeNotApprovedForAbstractor($AbstractorOrderUID)
	{
		$this->db->select('ApprovalUID')->from('torderapprovals');
		$this->db->where('AbstractorOrderUID',$AbstractorOrderUID);
		$this->db->where("(ApprovalFunction = 'AbstractorPricingOverride' OR  ApprovalFunction = 'AbstractorPricingAdjustments')");
		$this->db->where('ApprovalStatus', 0);
		return $this->db->get()->num_rows();
	}


	function check_api_order($OrderUID){
		$this->db->select('*');
		$this->db->from('tOrders');
		$this->db->where('OrderUID',$OrderUID);
		$this->db->where('APIOrder',1);
						//$result= $this->db->get()->result();
		$query = $this->db->get()->num_rows();
		if($query > 0){
			return true;
		}else{
			return false;
		}
	}


	function insert_tmailreport($data){

		$data['IsMailSend'] = 1;
		$data['MailSendByUserUID'] = $this->session->userdata('UserUID');
		$data['MailSendDateTime'] = date('Y-m-d H:i:s');
		$data['MailDeliveryStatus'] = 0;

		$this->db->insert('temailreport',$data);

	}

	function get_insertid_temailreport(){
		$data =  $this->db->query("SELECT `AUTO_INCREMENT` AS lastid FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".$this->db->database."' AND TABLE_NAME   = 'temailreport' ")->row();
		return $data->lastid;
	}


	function CheckAbstractorCancelOrder($OrderUID){
		$this->db->select('*');
		$this->db->from('torderabstractorunassign');
		$this->db->where('OrderUID',$OrderUID);
		$query = $this->db->get()->num_rows();
		if($query > 0){
			return true;
		}else{
			return false;
		}
	}

	public function get_torderabstractor_by_AbstractorOrderUID($AbstractorOrderUID)
	{
		return $this->db->get_where('torderabstractor',array('AbstractorOrderUID' => $AbstractorOrderUID))->row();
	}

	public function get_torderapproval_for_abstractor($AbstractorOrderUID, $UserUID)
	{
		$this->db->select('*')->from('torderapprovals');
		$this->db->where(array('AbstractorOrderUID' => $AbstractorOrderUID, 'RaisedByUserUID'=>$UserUID));
		$this->db->where('ApprovalStatus', 0);
		$this->db->order_by('ApprovalUID', 'DESC');
		return $this->db->get()->num_rows();
	}

	// function GetApiSourceName($OrderUID){
	// 	$this->db->select('*');
	// 	$this->db->from('tOrders');
	// 	$this->db->join('mApiTitlePlatform','mApiTitlePlatform.OrderSourceUID=tOrders.OrderSourceUID','inner');
	// 	$this->db->where('OrderUID',$OrderUID);
	// 	$this->db->where('APIOrder',1);
	// 	$result= $this->db->get()->row();
	// 	return $result;
	// }

	function is_duplicateorder($OrderUID)
	{
		$OrderDetails = $this->common_model->get_orderdetails($OrderUID);
		$PropertyAddress1 = $OrderDetails->PropertyAddress1;
		$PropertyAddress2 = $OrderDetails->PropertyAddress2;
		$PropertyCityName = $OrderDetails->PropertyCityName;
		$PropertyStateCode = $OrderDetails->PropertyStateCode;
		$PropertyZipcode = $OrderDetails->PropertyZipcode;
		$PropertyCountyName = $OrderDetails->PropertyCountyName;
		$OrderUID = $OrderDetails->OrderUID;

		$Address = strtoupper(trim(trim($PropertyAddress1).' '.trim($PropertyAddress2)));

		$query = $this->db->query(" SELECT *,TRIM(CONCAT_WS(' ',TRIM(PropertyAddress1),TRIM(PropertyAddress2))) AS whole_name FROM tOrders
			WHERE tOrders.OrderUID <> '$OrderUID'
			AND tOrders.PropertyZipcode = '$PropertyZipcode'
			AND tOrders.PropertyCityName = '$PropertyCityName'
			AND tOrders.PropertyStateCode = '$PropertyStateCode'
			AND tOrders.PropertyCountyName = '$PropertyCountyName'
			GROUP BY tOrders.OrderUID
			HAVING whole_name LIKE '$Address'");
		$res = $query->num_rows();

		return $res;
	}

	function is_vendor_assigned_workflow($OrderUID,$WorkflowModuleUID){
		$query = $this->db->query ("SELECT * FROM `torderassignment` LEFT JOIN `mvendors` ON `mvendors`.`VendorUID` = `torderassignment`.`VendorUID` WHERE  `OrderUID` = $OrderUID AND `WorkflowModuleUID` = $WorkflowModuleUID AND SendToVendor = '1' ");
		$result =  $query->num_rows();
		if($result > 0){
			return true;
		}
		return false;
	}

	function is_abstractor_assigned_workflow($OrderUID,$WorkflowModuleUID){
		$query = $this->db->query ("SELECT * FROM `torderassignment`  WHERE  `OrderUID` = $OrderUID AND `WorkflowModuleUID` = $WorkflowModuleUID AND SelfManualAssign = 'EXTERNAL' ");
		$result =  $query->num_rows();
		if($result > 0){
			return true;
		}
		return false;
	}

	function is_abstractor_login(){
		$UserUID = $this->session->userdata('UserUID');
		$query = $this->db->query("SELECT EXISTS (SELECT `RoleType`, `RoleName` FROM (`musers`) INNER JOIN `mroles` ON `musers`.`RoleUID` = `mroles`.`RoleUID` WHERE `musers`.`UserUID` = $UserUID AND RoleType='15') AS abstractor_login ");
		return $query->row();
	}


	function get_allabstractors(){
		$this->db->select('*');
		$this->db->from('mabstractor');
		$result= $this->db->get()->result();
		return $result;
	}


	function GetavatarById($UserUID)
	{
		$this->db->select("Avatar");
		$this->db->from('musers');
		$this->db->where(array("musers.UserUID"=>$UserUID));
		$query = $this->db->get();
		return $query->row();
	}

	/*check vendor login*/
	function is_customerlogin(){
		$loggedid = $this->session->userdata('UserUID');

		$query = $this->db->query("SELECT EXISTS(SELECT RoleType FROM `mroles` JOIN musers on `musers`.`RoleUID` = `mroles`.`RoleUID`  WHERE `musers`.`UserUID` = '".$loggedid."' AND RoleType IN ('8') LIMIT 1) AS is_vendor");
		$result =  $query->row();


		if($result->is_vendor == 1){
			return true;
		}else{
			return false;
		}
	}

	function getcustomer_logged_details(){
		$loggedid = $this->session->userdata('UserUID');
		$query = $this->db->query("SELECT musers.CustomerUID,musers.UserUID,UserName,RoleName,RoleType FROM `mroles` JOIN musers on `musers`.`RoleUID` = `mroles`.`RoleUID`  WHERE `musers`.`UserUID` = '".$loggedid."' AND RoleType = '8' LIMIT 1");
		$result =  $query->row();
		return $result;
	}


	function get_mapapi_key(){
		$this->db->select ( 'ApiMapKey' );
		$this->db->from ( 'morganizations' );
		$query = $this->db->get();
		if($query->num_rows() > 0){
			return $query->row()->ApiMapKey;
		}else{
			return null;
		}
	}


	function GetAvailFileName($FileName, $ext, $itr, $OrderUID)
	{
		if ($itr == 0) {			
			$DocumentFileName=$FileName.$ext;
		}
		else{
			$DocumentFileName=$FileName.$itr.$ext;			
		}
		$DocumentFileName = preg_replace("/[^a-z0-9\_\-\.]/i", '', $DocumentFileName);
		$query=$this->db->get_where('torderdocuments', array('OrderUID'=>$OrderUID,
			'DocumentFileName'=>$DocumentFileName));
		$numrows=$query->num_rows();
		if($numrows==0)
		{
			return $DocumentFileName;
		}
		$itr+=1;
		return $this->GetAvailFileName($FileName, $ext, $itr, $OrderUID);
	}

	function GetUserDetailsByUser($UserUID){
		$this->db->select("*");
		$this->db->from('musers');
		$this->db->where(array("musers.UserUID"=>$UserUID));
		$query = $this->db->get();
		return $query->row();
	}

	function get_settings($SettingUID)
	{
		$query = $this->db->query("SELECT * FROM msetting WHERE SettingUID = '".$SettingUID."' ");
		return $query->row();
	}

	function check_doc_bymail($OrderUID){
		$query = $this->db->query("SELECT EXISTS(SELECT * FROM torderdocuments WHERE OrderUID = '".$OrderUID."' AND IsDocumentReceived = 1 GROUP BY OrderUID) as IsReceived ");
		return $query->row();
	}
	function GetDownloaddata($UserUID){
			 $this->db->select('*');
			 $this->db->from('treportdownloadtracker');
			 $this->db->where('UserUID',$UserUID);
			 $this->db->where('IsRead',0);
			 $this->db->order_by('ReportTrackerUID DESC');
		 return  $this->db->get()->result();
	}

	function GetMinoritiesForMonth()
	{
		$sql='SELECT (SELECT COUNT(1) AS MinorityOwned FROM torderabstractor
						JOIN mabstractor ON mabstractor.AbstractorUID=torderabstractor.AbstractorUID
						WHERE MONTH(torderabstractor.AssignedDateTime) = MONTH(CURRENT_DATE())
						AND mabstractor.MinorityOwned=1) AS MinorityOwned,
					(SELECT COUNT(1) AS WomenOwned FROM torderabstractor
									JOIN mabstractor ON mabstractor.AbstractorUID=torderabstractor.AbstractorUID
									WHERE MONTH(torderabstractor.AssignedDateTime) = MONTH(CURRENT_DATE())
									AND (mabstractor.WomanOwned=1)) AS WomanOwned,
					(SELECT COUNT(1) AS DisabledOwned FROM torderabstractor
									JOIN mabstractor ON mabstractor.AbstractorUID=torderabstractor.AbstractorUID
									WHERE MONTH(torderabstractor.AssignedDateTime) = MONTH(CURRENT_DATE())
									AND mabstractor.DisabledOwned=1) AS DisabledOwned,
					(SELECT COUNT(1) AS DisabledVeteranOwned FROM torderabstractor
									JOIN mabstractor ON mabstractor.AbstractorUID=torderabstractor.AbstractorUID
									WHERE MONTH(torderabstractor.AssignedDateTime) = MONTH(CURRENT_DATE())
									AND mabstractor.DisabledVeteranOwned=1) AS DisabledVeteranOwned,
					(SELECT Count(1) AS TotalOrders FROM torderabstractor
								WHERE MONTH(torderabstractor.AssignedDateTime) = MONTH(CURRENT_DATE())) AS TotalOrders';

		$sql='SELECT mMinorityCertification.MinorityDisplayName, mMinorityCertification.MinorityCertificationCode,
		(
		SELECT
		COUNT(1) AS MinorityOwned
		FROM
		torderabstractor
		JOIN mabstractor ON mabstractor.AbstractorUID = torderabstractor.AbstractorUID
		WHERE
		MONTH (
		torderabstractor.AssignedDateTime
		) = MONTH (CURRENT_DATE())
		AND (CASE WHEN EXISTS( SELECT mVendorMinorityCertification.VendorUID 
		FROM mVendorMinorityCertification 
		WHERE mVendorMinorityCertification.VendorUID = mabstractor.AbstractorUID
		AND mVendorMinorityCertification.MinorityCertificationUID = mMinorityCertification.MinorityCertificationUID
		AND mVendorMinorityCertification.IsEnabled = 1
		AND mVendorMinorityCertification.ExpirationDate >= CURDATE()
		)
		THEN TRUE
		ELSE FALSE
		END)
		) AS MinorityCount
		FROM mMinorityCertification
		WHERE mMinorityCertification.Active = 1';
		return $this->db->query($sql)->result();
	}

	/**
		*@description Function to getMonthTotalAbstractor
		*
		*
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return Row 
		* @since 16.4.2020 
		* @version Minority Owned 
		*
	*/ 
	function getMonthTotalAbstractor(){
		
		return $this->db->query("SELECT Count(1) AS TotalOrders FROM torderabstractor
								WHERE MONTH(torderabstractor.AssignedDateTime) = MONTH(CURRENT_DATE())")->row()->TotalOrders;
	}

	function get_notifications(){
		if (in_array($this->session->userdata('RoleType'),array('1','2','3','4','5','6'))) {
			$query = $this->db->query("SELECT IsRead,FollowUpUID,OrderNumber,FollowUpType,torderfollowup.OrderUID,FollowUpStatus,Comments,Remarks,FollowUpPriority,`b`.`UserName` AS CreatedByUserName,`c`.`UserName` AS CompletedByUserName,`d`.`UserName` AS StartedByUserName, DATE_FORMAT(torderfollowup.FollowupDateTime, '%m-%d-%Y %H:%i:%s') as FollowupDateTime, DATE_FORMAT(torderfollowup.CreateOnDateTime, '%m-%d-%Y %H:%i:%s') as CreateOnDateTime, DATE_FORMAT(torderfollowup.FollowUpEndTime, '%m-%d-%Y %H:%i:%s') as FollowUpEndTime, DATE_FORMAT(torderfollowup.FollowUpStartTime, '%m-%d-%Y %H:%i:%s') as FollowUpStartTime,
				CASE FollowUpType
				WHEN 'Customer' THEN (SELECT CustomerName FROM mCustomers WHERE CustomerUID = torderfollowup.UserUID)
				ELSE `a`.`UserName`
				END as UserName,
				torderfollowup.UserUID AS UserUID
				 FROM (`torderfollowup`) JOIN tOrders ON tOrders.OrderUID = torderfollowup.OrderUID LEFT JOIN `musers` a ON `a`.`UserUID` = `torderfollowup`.`UserUID` LEFT JOIN `musers` b ON `b`.`UserUID` = `torderfollowup`.`CreatedByUserUID` LEFT JOIN `musers` c ON `c`.`UserUID` = `torderfollowup`.`CompletedByUserUID` LEFT JOIN `musers` d ON `d`.`UserUID` = `torderfollowup`.`StartedByUserUID` WHERE   DATE(NOW()) = DATE(torderfollowup.FollowupDateTime) AND FollowUpStatus IN ('New','Started','Completed')
				AND FollowUpPriority IN ('Rush', 'ASAP', 'Normal') ORDER BY FIELD(FollowUpPriority,'Rush', 'ASAP', 'Normal')

				  ,IsRead ASC LIMIT 25
				");
				/* Updated ORDER BY FIELD(`torderfollowup`.`FollowUpPriority`,'rush','asap','normal')
				*  Line for ordering priorities
				*/
			return $query->result();
		}else{
			$query = $this->db->query("SELECT IsRead,FollowUpUID,OrderNumber,FollowUpType,torderfollowup.OrderUID,FollowUpStatus,Comments,Remarks,FollowUpPriority,`b`.`UserName` AS CreatedByUserName,`c`.`UserName` AS CompletedByUserName,`d`.`UserName` AS StartedByUserName, DATE_FORMAT(torderfollowup.FollowupDateTime, '%m-%d-%Y %H:%i:%s') as FollowupDateTime, DATE_FORMAT(torderfollowup.CreateOnDateTime, '%m-%d-%Y %H:%i:%s') as CreateOnDateTime, DATE_FORMAT(torderfollowup.FollowUpEndTime, '%m-%d-%Y %H:%i:%s') as FollowUpEndTime, DATE_FORMAT(torderfollowup.FollowUpStartTime, '%m-%d-%Y %H:%i:%s') as FollowUpStartTime,
				CASE FollowUpType
				WHEN 'Customer' THEN (SELECT CustomerName FROM mCustomers WHERE CustomerUID = torderfollowup.UserUID)
				ELSE `a`.`UserName`
				END as UserName,
				torderfollowup.UserUID AS UserUID
				 FROM (`torderfollowup`) JOIN tOrders ON tOrders.OrderUID = torderfollowup.OrderUID LEFT JOIN `musers` a ON `a`.`UserUID` = `torderfollowup`.`UserUID` LEFT JOIN `musers` b ON `b`.`UserUID` = `torderfollowup`.`CreatedByUserUID` LEFT JOIN `musers` c ON `c`.`UserUID` = `torderfollowup`.`CompletedByUserUID` LEFT JOIN `musers` d ON `d`.`UserUID` = `torderfollowup`.`StartedByUserUID` WHERE  FollowUpStatus IN ('New','Started','Completed') AND (`torderfollowup`.`CreatedByUserUID` = ".$this->loggedid." OR `torderfollowup`.`CompletedByUserUID` = ".$this->loggedid." OR `torderfollowup`.`StartedByUserUID` = ".$this->loggedid.") AND DATE(NOW()) = DATE(torderfollowup.FollowupDateTime) 
				 AND FollowUpPriority IN ('Rush', 'ASAP', 'Normal') ORDER BY FIELD(FollowUpPriority,'Rush', 'ASAP', 'Normal')
				,IsRead ASC LIMIT 25
				");
				/* Updated ORDER BY FIELD(`torderfollowup`.`FollowUpPriority`,'rush','asap','normal')
				*  Line for ordering priorities
				*/
			return $query->result();
		}


	}
 // @Desc Common Notification @Auth Uba @On May 28 2020
	function get_common_notifications(){
		$this->db->select('*');
		$this->db->from('tnotifications');
		$this->db->where('UserUID',$this->loggedid);
		$this->db->where('IsSeen',0);
		$this->db->order_by('CreatedDateTime','DESC');
		return $this->db->get()->result();
	}
 // @Desc Common Notification @Auth Uba @On May 28 2020
	function create_common_notification($UserUID,$Message,$RedirectTo){
		$Notification = array(
			'UserUID'=>$UserUID,
			'Message'=>$Message,
			'RedirectTo'=>$RedirectTo,
			'CreatedDateTime'=>date('y-m-d H:i:s'),
		);
		return $this->db->insert('tnotifications',$Notification);
	}
 // @Desc Common Notification @Auth Uba @On May 28 2020
	function seen_common_notification($NotificationUID){
		$this->db->where('NotifiactionUID',$NotificationUID);
		$res = $this->db->update('tnotifications',array('IsSeen'=>1,'SeenDateTime'=>date('y-m-d H:i:s')));
		if($res){
			return $this->db->where('NotifiactionUID',$NotificationUID)->get('tnotifications')->row();
		}
	}

	function get_approval_notifications(){
		$this->db->select('torderapprovals.*, musers.UserName as RequestedUserName');
		$this->db->from('torderapprovals');
		$this->db->join('musers','torderapprovals.RaisedByUserUID = musers.UserUID','left');
		$this->db->where('ApprovalFunction','Group Assignment');
		$this->db->where('RequestedToUserUID',$this->loggedid);
		$this->db->where('IsReviewed',1);
		return $this->db->get()->result();
	}


	function time_elapsed_string($datetime, $full = false) {
		$datetime = date('Y-m-d H:i:s', strtotime(str_replace('-', '/', $datetime)));
		if($datetime){
			$now = new DateTime;
			$time = strtotime($datetime);
			$mod_datetime = date("Y-d-m H:i:s", $time);

			$ago = new DateTime($datetime);
			$diff = $now->diff($ago);
			$diff->w = floor($diff->d / 7);
			$diff->d -= $diff->w * 7;

			$string = array(
				'y' => 'year',
				'm' => 'month',
				'w' => 'week',
				'd' => 'day',
				'h' => 'hour',
				'i' => 'minute',
				's' => 'second',
			);
			foreach ($string as $k => &$v) {
				if ($diff->$k) {
					$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
				} else {
					unset($string[$k]);
				}
			}

			if (!$full) $string = array_slice($string, 0, 1);
			return $string ? implode(', ', $string) . ' ago' : 'just now';
		}else{
			return false;
		}
	}

	function Get_customavatarById($UserUID)
	{
		$this->db->select("Avatar");
		$this->db->from('musers');
		$this->db->where(array("musers.UserUID"=>$UserUID));
		$query = $this->db->get();
		if(count($result  = $query->row()) > 0){
			return $result->Avatar;
		}else{
			return false;
		}
	}

	//for get caps setting
	function GetOrganizations(){
		$this->db->select('*');
		$this->db->from('morganizations');
		return $this->db->get()->row();
	}

	// function GetOrdersourceDetails($OrderSourceUID){
	// 	$this->db->select('mApiTitlePlatform.*')->from('mApiTitlePlatform');
	// $this->db->join('tOrders', 'tOrders.OrderSourceUID=mApiTitlePlatform.OrderSourceUID', 'left');
	// $this->db->where('mApiTitlePlatform.OrderSourceUID', $OrderSourceUID);
	// $query=$this->db->get();
	// return $query->row();
	// }

	function CheckAutoSuggestionInput($CurrentTargetID)
	{
			$this->db->select ('IsAutoSuggestion');
			$this->db->from ( 'mfields' );
			$this->db->where(array("FieldName"=>$CurrentTargetID));
			$query = $this->db->get();
			return $query->row()->IsAutoSuggestion;
	}

	function CheckAutoSuggestionOrganizationLevel()
	{
			$this->db->select ('IsAutoSuggestion');
			$this->db->from ( 'morganizations' );
			$query = $this->db->get();
			return $query->row()->IsAutoSuggestion;
	}

	function GetApiSourceStatus($OrderUID) {
		$tOrders = $this->db->get_where('tOrders', array('OrderUID'=>$OrderUID))->row();
		$OrderSourceUID = $tOrders->OrderSourceUID;
		if($OrderSourceUID){
			$mApiTitlePlatform = $this->db->get_where('mApiTitlePlatform', array('ApiTitlePlatformUID'=>$OrderSourceUID))->row();
			$OrderSourceName = trim($mApiTitlePlatform->OrderSourceName);
			if($OrderSourceName == 'Stewart'){
				return 2;
			} else if($OrderSourceName == 'Keystone'){
				return 2;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}


public function GetAbstractorByUID($AbstractorUID)
{
	return $this->db->query("SELECT `mabstractor`.*, `mabstractor`.`AbstractorUID` As AbstractorUID,
					`mabstractor`.`AbstractorNo` As AbstractorNo,
					CONCAT_WS(' ',`mabstractor`.`AbstractorFirstName`, `mabstractor`.`AbstractorMiddleName`, `mabstractor`.`AbstractorLastName`) AS AbstractorName,
				 `mabstractor`.`Mobile` As Mobile,
				 `mabstractor`.`Email` As Email,
				 `mabstractor`.`Mobile` As Mobile,
				 `mabstractor`.`ZipCode` As ZipCode,
				 `c`.CountyName AS AbstractorCounty,
				 `city`.CityName AS AbstractorCity,
				 `vc`.CountyName AS AbstractorVisitingCounty,
				 `s`.StateName AS AbstractorState,
				 `s`.StateCode AS AbstractorStateCode,
				 `vs`.StateName AS AbstractorVisitingState,
					TRIM(CONCAT_WS(' ',TRIM( `mabstractor`.`AddressLine1`),TRIM( `mabstractor`.`AddressLine2`))) AS Address,
								`mabstractor`.`AbstractorCompanyName` AS CompanyName,
								`mabstractor`.`LookUpCompany` AS LookUpCompany,
								`mabstractor`.`FaxNo` As FaxNo
	 FROM (`mabstractor`)
	 LEFT JOIN `mCounties` AS c ON `c`.`CountyUID`=`mabstractor`.`CountyUID`
	 LEFT JOIN `mCounties` AS vc ON `vc`.`CountyUID`=`mabstractor`.`VisitingCountyUID`
	 LEFT JOIN `mCities` AS city ON `city`.`CityUID`=`mabstractor`.`CityUID`
	 LEFT JOIN `mStates` AS s ON `s`.`StateUID`=`mabstractor`.`StateUID`
	 LEFT JOIN `mStates` AS vs ON `vs`.`StateUID`=`mabstractor`.`VisitingStateUID`
	 WHERE `mabstractor`.`AbstractorUID`=".$AbstractorUID."
	 group by `mabstractor`.`AbstractorUID`")->row();

}

function GetStatesByStateUID($StateUIDs)
{
	$this->db->where_in('StateUID', $StateUIDs);
	return $this->db->get('mStates')->result();
}

function GetmcitiesbyZipCodes($ZipCodes)
{
	$this->db->where_in('ZipCode', $ZipCodes);
	return $this->db->get('mCities')->result();
}

function GetmCountiesbyCoutyUID($CountyUIDs)
{
	$this->db->where_in('CountyUID', $CountyUIDs);
	return $this->db->get('mCounties')->result();
}

function searchForId($id, $array) {
	foreach ($array as $key => $val) {
		if ($val->StateUID == $id) {
			return $array[$key];
		}
	}
	return null;
}

function is_arrayobject_value_exist($id, $slug, $array) {
	foreach ($array as $key => $val) {
		if ($val->$slug == $id) {
			return $array[$key];
		}
	}
	return null;
}

function CheckPrintingWorkFlow($CustomerUID,$SubProductUID,$OrderUID)
{
	$RoleUID = $this->session->userdata('RoleUID');
	$roles = $this->get_roles_for_printing($RoleUID);

	if($roles->CanPrinting == 1){
		$StatusUID = $this->config->item('keywords')['Review Complete'];
		$this->db->select('*')->from('tOrders');
		$this->db->join('mcustomerworkflowmodules', 'tOrders.CustomerUID=mcustomerworkflowmodules.CustomerUID', 'inner');
		$this->db->where('mcustomerworkflowmodules.CustomerUID', $CustomerUID);
		$this->db->where('mcustomerworkflowmodules.SubProductUID', $SubProductUID);
		$this->db->where('mcustomerworkflowmodules.WorkflowModuleUID', '5');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->where('tOrders.StatusUID', $StatusUID);
		$query=$this->db->get();
		return $query->row();
	}
}

function get_roles_for_printing($RoleUID)
{
	$this->db->select ( '*' );
	$this->db->from ( 'mroles' );
	$this->db->where ('mroles.RoleUID',$RoleUID);
	$query = $this->db->get();
	return $query->row();
}

function CheckFinalReportExist($OrderUID){
	$query = $this->db->query("SELECT OrderUID,DocumentFileName FROM `torderdocuments` WHERE TypeOfDocument = 'Final Reports' AND OrderUID = $OrderUID ORDER BY DocumentCreatedDate DESC LIMIT 1");
	if($query->num_rows() >0)
	{
		return $query->row();
	}
	else
	{
		return false;
	}
}

	function GetPrintingOrdersCount()
	{

		$loggedid = $this->session->userdata('UserUID');
		$status[0] = $this->config->item('keywords')['Review Complete'];

		$this->db->select ("OrderNumber,tOrders.CustomerUID,CustomerNumber,CustomerName,OrderTypeName,StatusColor,PriorityName,mOrderStatus.StatusName, tOrders.OrderUID,mOrderPriority.PriorityUID,PriorityName,PropertyStateCode, tOrders.LoanNumber, CompleteDateTime AS ReviewCompleteDateTime", false);
		$this->db->select('DATE_FORMAT(tOrders.OrderEntryDatetime, "%m-%d-%Y %H:%i:%s") as OrderEntryDatetime', FALSE);
		$this->db->from ( 'tOrders' );
		$this->db->join ( 'torderassignment', 'tOrders.OrderUID = torderassignment.OrderUID');
		$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID','left');
		$this->db->join ( 'mOrderTypes', 'mOrderTypes.OrderTypeUID = tOrders.OrderTypeUID','left');
		$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID','left');
		$this->db->join ( 'mSubProducts', 'mSubProducts.SubProductUID = tOrders.SubProductUID' , 'left' );
		$this->db->join ( 'mStates', 'mStates.StateCode = tOrders.PropertyStateCode' , 'left' );
		$this->db->join ( 'mProducts', 'mProducts.ProductUID = mSubProducts.ProductUID');
		$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID');


		/*FOR SUPERVISOR CHECK*/
		if ($this->session->userdata('RoleType') == 6){
			$UserSubProducts = $this->common_model->_get_Subproducts_bylogin($this->loggedid);
			if($UserSubProducts): $this->db->where('tOrders.SubProductUID IN ('.$UserSubProducts.')', null, false); else: $this->db->where('tOrders.SubProductUID IN (0)', null, false); endif;
		}

		$this->db->where('tOrders.IsPrint',NULL);
		$this->db->where('tOrders.ProjectUID!=0',NULL, false);
		$this->db->where_in ('tOrders.StatusUID',$status);
		$this->db->group_by('tOrders.OrderUID');

		$query = $this->db->get();
		return $query->num_rows();
	}


function is_printingorder($OrderUID){
	$this->db->select('*');
	$this->db->from ( 'tOrders' );
	$this->db->where('tOrders.StatusUID','40');
	$this->db->where('tOrders.OrderUID',$OrderUID);
	$query = $this->db->get();
	return $query->num_rows();
}

function GetProjectSignor($ProjectUID, $IsMERS=0, $StateUID)
{
	$this->db->select('*')->from('mProjectSignor');
	$this->db->join('mSignor', 'mSignor.SignorUID = mProjectSignor.SignorUID');
	$this->db->join('mSignorCoverage', 'mSignorCoverage.SignorUID = mProjectSignor.SignorUID');
	$this->db->where('IsMERSSignor', $IsMERS);
	$this->db->where('mSignorCoverage.StateUID', $StateUID);
	return $this->db->where('ProjectUID', $ProjectUID)->get()->result();
}

function GetProjectNotary($ProjectUID)
{
	$this->db->select('*')->from('mProjectNotary');
	$this->db->join('mNotary', 'mNotary.NotaryUID = mProjectNotary.NotaryUID');
	return $this->db->where('ProjectUID', $ProjectUID)->get()->result();
}


function GetProjectSignorName($ProjectUID, $IsMERS=0, $StateUID)
{
	$this->db->select('mSignor.SignorUID, mSignor.SignorName, mSignor.IsVPandAbove')->from('mProjectSignor');
	$this->db->join('mSignor', 'mSignor.SignorUID = mProjectSignor.SignorUID');
	$this->db->join('mSignorCoverage', 'mSignorCoverage.SignorUID = mProjectSignor.SignorUID');
	$this->db->where('IsMERSSignor', $IsMERS);
	$this->db->where('mSignorCoverage.StateUID', $StateUID);
	return $this->db->where('ProjectUID', $ProjectUID)->get()->result();
}

function GetProjectNotaryName($ProjectUID)
{
	$this->db->select('mNotary.NotaryUID, NotaryName')->from('mProjectNotary');
	$this->db->join('mNotary', 'mNotary.NotaryUID = mProjectNotary.NotaryUID');
	return $this->db->where('ProjectUID', $ProjectUID)->get()->result();
}

function GetTemplateByOrderinFo($OrderUID){

		$this->db->select("*");
		$this->db->from('torderinfo');
		$this->db->where(array("OrderUID"=>$OrderUID));
		$query = $this->db->get();
		return $query->row();

}


function GetProjectDetails($ProductUID,$CustomerUID){

	$this->db->select('*');
	$this->db->from ( 'mProjects' );
	$this->db->join ( 'mProducts', 'mProducts.ProductUID = mProjects.ProductUID','left');
	$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = mProjects.CustomerUID','left');
	$this->db->where('mProjects.CustomerUID',$CustomerUID);
	$this->db->where('mProjects.ProductUID',$ProductUID);
	//$this->db->where('mProjects.Active',1);
	$query = $this->db->get();
	$result =  $query->result();
	return $result;
}


function GetSubProductDetails($SubProductUID){
	$this->db->where(array("Active"=>1,"SubProductUID" => $SubProductUID));
	$query = $this->db->get('mSubProducts');
	return $query->row();
}


function GetSignorBySignorUID($SignorUID)
{
	return $this->db->get_where('mSignor', array('SignorUID'=> $SignorUID))->row();
}

function GetNotaryByNotaryUID($NotaryUID)
{
	$this->db->select('*')->from('mNotary');
	$this->db->where(array('NotaryUID'=> $NotaryUID));
	return $this->db->get()->row();
}

// function GetApiRequestStatus($OrderUID){

// 	$tOrders = $this->db->get_where('tOrders', array('OrderUID'=>$OrderUID))->row();
// 	$OrderSourceUID = $tOrders->OrderSourceUID;
// 	$mApiTitlePlatform = $this->db->get_where('mApiTitlePlatform', array('ApiTitlePlatformUID'=>$OrderSourceUID))->row();
// 	$OrderSourceName = $mApiTitlePlatform->OrderSourceName;

// 	$this->db->select('*');
// 	$this->db->from ( 'tApiRequestStatus' );
// 	$this->db->join ( 'mApiRequest', 'mApiRequest.ApiRequestUID = tApiRequestStatus.ApiRequestUID','left');
// 	$this->db->join ( 'mApiTitlePlatform', 'mApiTitlePlatform.ApiTitlePlatformUID = mApiRequest.ApiTitlePlatformUID','left');
// 	$this->db->where('tApiRequestStatus.OrderUID',$OrderUID);
// 	$this->db->where('tApiRequestStatus.IsCompleted','0');
// 	$this->db->where('mApiRequest.ApiTitlePlatformUID',$OrderSourceUID);

// 	if($OrderSourceName == 'Stewart'){
// 		$this->db->where('mApiRequest.RequestType','Dispute');
// 	} else if($OrderSourceName == 'Keystone'){
// 		$this->db->where('mApiRequest.RequestType','RevisionRequest');
// 	}

// 	$query = $this->db->get();
// 	return $query->result();
// }

	/*function _getCounty_StateUID_ZipCode($ZipCode,$StateCode) {

		$query = $this->db->query("SELECT CountyName FROM `mCities` JOIN mStates ON mStates.StateUID = mCities.StateUID JOIN mCounties ON mCounties.CountyUID = mStates.StateUID WHERE mCities.ZipCode = '".$ZipCode."' AND StateCode = '".$StateCode."' ");
		return $query->row();

	}*/

	function _getCounty_StateUID_ZipCode($ZipCode,$StateCode) {

		$this->db->select('*');
		$this->db->from ( 'mCounties' );
		$this->db->join ( 'mCities', 'mCities.CountyUID = mCounties.CountyUID','INNER');
		$this->db->join ( 'mStates', 'mStates.StateUID = mCities.StateUID','INNER');
		$this->db->where('mStates.StateCode',$StateCode);
		$this->db->where('mCities.ZipCode',$ZipCode);
		$query = $this->db->get();
		return $query->row();
	}

	function _get_product_bylogin(){
		$UserUID = $this->loggedid;
		$query = $this->db->query("SELECT GROUP_CONCAT(ProductUID SEPARATOR ',') AS ProductUID FROM (`mUserProduct`) WHERE `mUserProduct`.`UserUID` = '".$UserUID."'");
		$result = $query->row();
		if(!empty($result)){
			return $result->ProductUID;
		}
		return null;
	}

	function _get_loginCustomers($ProductUID)
	{
		if($ProductUID){
			$this->db->select('mCustomerProducts.CustomerUID,CustomerName,CustomerNumber');
			$this->db->from ( 'mCustomerProducts' );
			$this->db->join ( 'mCustomers', 'mCustomers.CustomerUID = mCustomerProducts.CustomerUID');
			$this->db->where_in('mCustomerProducts.ProductUID',$ProductUID);
			$this->db->group_by('mCustomerProducts.CustomerUID');
			$query = $this->db->get();
			return $query->result();
		}

	}

	
	function _get_loginProjects()
	{
	         $this->db->select('*');
			$this->db->from('mProjects');
			return $this->db->get()->result();
	}


	function _get_loginProducts($ProductUID)
	{
		if($ProductUID){
			$this->db->select('mCustomerProducts.ProductUID,ProductName');
			$this->db->from ( 'mCustomerProducts' );
			$this->db->join ( 'mProducts', 'mProducts.ProductUID = mCustomerProducts.ProductUID');
			$this->db->where('mCustomerProducts.ProductUID in ('.$ProductUID.')');
			$this->db->group_by('mCustomerProducts.ProductUID');
			$query = $this->db->get();
			return $query->result();
		}

	}

	function _get_subproduct_bylogin($ProductUID){
		$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT SubProductUID SEPARATOR ',') AS SubProductUID FROM (`mCustomerProducts`) WHERE `mCustomerProducts`.`ProductUID` IN (".$ProductUID.")");
		$result = $query->row();
		if(!empty($result)){
			return $result->SubProductUID;
		}
		return null;
	}

	function _get_customers_bylogin($ProductUID){
		$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT CustomerUID SEPARATOR ',') AS CustomerUID FROM (`mCustomerProducts`) WHERE `mCustomerProducts`.`ProductUID` IN (".$ProductUID.")");
		$result = $query->row();
		if(!empty($result)){
			return $result->CustomerUID;
		}
		return null;
	}

	function _get_customersdata_bylogin($UserUID){
		$query = $this->db->query("SELECT CustomerNumber,mCustomers.CustomerUID,CustomerName FROM (`mUserProduct`) JOIN mCustomerProducts on mCustomerProducts.ProductUID = mUserProduct.ProductUID JOIN mCustomers ON mCustomers.CustomerUID =  mCustomerProducts.CustomerUID WHERE `mUserProduct`.`UserUID` = '".$UserUID."' GROUP BY mCustomers.CustomerUID ORDER BY CustomerName ASC");
		$result = $query->result();
		return $result;
	}

	function _get_Subproducts_bylogin($UserUID){
		$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT SubProductUID SEPARATOR ',') AS SubProductUID FROM (`mUserProduct`) JOIN mCustomerProducts on mCustomerProducts.ProductUID = mUserProduct.ProductUID WHERE `mUserProduct`.`UserUID` = '".$UserUID."'");
		$result = $query->row();
		if(!empty($result)){
			return $result->SubProductUID;
		}
		return null;
	}

	function _get_groups_bylogin(){
		$UserUID = $this->loggedid;
		$query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT GroupUID SEPARATOR ',') AS GroupUID FROM (`mUserProduct`) JOIN mgroupcustomers on mgroupcustomers.GroupCustomerProductUID = mUserProduct.ProductUID   WHERE `mUserProduct`.`UserUID` = '".$UserUID."'");
		$result = $query->row();
		if(!empty($result)){
			return $result->GroupUID;
		}
		return null;
	}


	// Clear Exception Functions

	function GetFinalReportsList($OrderUID){

			$this->db->select ( '*,tOrders.OrderDocsPath' );
			$this->db->from ( 'torderdocuments' );
			$this->db->join ( 'mdocumenttypes', 'mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID' , 'left' );
			$this->db->join ( 'msearchmodes', 'msearchmodes.SearchModeUID = torderdocuments.SearchModeUID' , 'left' );
			$this->db->join ( 'tOrders', 'tOrders.OrderUID = torderdocuments.OrderUID' , 'left' );
			$this->db->where(array("torderdocuments.OrderUID"=>$OrderUID));
			$this->db->where(array("torderdocuments.TypeOfDocument"=>'Final Reports'));
			$this->db->order_by("torderdocuments.DocumentCreatedDate DESC");
			$query = $this->db->get();
			return $query->result();
	}

	function GetUserName($id)
	{
		$query = $this->db->query("SELECT UserName FROM musers WHERE UserUID ='$id' ");
		$res = $query->row();
		return $res->UserName;
	}

	function GetInternalExceptionList($OrderUID){
		$this->db->select("*");
		$this->db->from('texceptions');
		$this->db->join ( 'mexceptions', 'mexceptions.ExceptionUID = texceptions.ExceptionUID' , 'left' );
		$this->db->join ( 'musers', 'musers.UserUID = texceptions.RaisedByUserUID' , 'left' );
		$this->db->where(array("texceptions.OrderUID"=>$OrderUID));
		$this->db->where(array("texceptions.RaisedByAPI"=> 0));
		$this->db->where(array("texceptions.IsClear"=> 0));
		$query = $this->db->get();
		return $query->result();
	}

	function GetDocmentTypeFromTemplate($TemplateUID,$SubProductUID){
		$this->db->select("*");
		$this->db->from('mTemplates');
		$this->db->join ( 'mtemplatemapping', 'mtemplatemapping.TemplateUID = mTemplates.TemplateUID' , 'left' );
		$this->db->where(array("mtemplatemapping.TemplateUID"=>$TemplateUID));
		$this->db->where(array("mtemplatemapping.SubProductUID"=>$SubProductUID));
		$query = $this->db->get();
		$DocumentType = $query->row();
		return $DocumentType->DocumentType;
	}

	function GetProductByOrderUID($OrderUID)
	{
		$this->db->select('mSubProducts.*, mProducts.*')->from('tOrders');
		$this->db->join('mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID');
		$this->db->join('mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		return $this->db->get()->row();
	}


	public function GetIsAdverseConditions($CustomerUID){

		$this->db->select("*");
		$this->db->from('mCustomers');
		$this->db->where(array("CustomerUID"=>$CustomerUID));
		$query = $this->db->get();
		return $query->row()->AdverseConditionsEnabled;
	}


	public function CheckAdverseConditionsEnabledForProduct($ProductUID){
		$this->db->select("*");
		$this->db->from('mProducts');
		$this->db->where(array("ProductUID"=>$ProductUID));
		$query = $this->db->get();
		return $query->row();
	}

	function _get_subproducts_for_login()
	{
		$ProductUIDs = $this->_get_product_bylogin();
		$ProductUIDs = explode(',', $ProductUIDs);
		if($ProductUIDs){
			$this->db->select('Group_Concat(SubProductUID) AS SubProductUID', false);
			$this->db->from('mSubProducts');
			$this->db->where_in('ProductUID', $ProductUIDs);
			// print_r($this->db->get()->row());
			return $this->db->get()->row();
		}
		return [];
	}
	
	// Reverse Workflow Functions Starts
	function GetAllCompletedWorkflows($OrderUID){
		
		$query=$this->db->query("
		SELECT mworkflowmodules.WorkflowModuleUID, mworkflowmodules.WorkflowModuleName 
		FROM `torderassignment`
		LEFT JOIN `mworkflowmodules` ON `torderassignment`.`WorkflowModuleUID`=`mworkflowmodules`.`WorkflowModuleUID`
		WHERE `torderassignment`.`WorkflowStatus` =  5
		AND `torderassignment`.`AssignedToUserUID`!= ''
		AND `torderassignment`.`WorkflowModuleUID` != 5 
		AND `torderassignment`.`OrderUID` =  '$OrderUID'
		UNION ALL
		SELECT mcustomerworkflowmodules.WorkflowModuleUID, mworkflowmodules.WorkflowModuleName
		FROM `tOrders`
		JOIN mcustomerworkflowmodules ON mcustomerworkflowmodules.CustomerUID=tOrders.CustomerUID AND mcustomerworkflowmodules.SubProductUID=tOrders.SubProductUID
		JOIN mworkflowmodules ON mcustomerworkflowmodules.WorkflowModuleUID = mworkflowmodules.WorkflowModuleUID
		WHERE mcustomerworkflowmodules.WorkflowModuleUID = ".$this->config->item('WorkflowModuleUID')['Printing']." AND tOrders.OrderUID = ".$OrderUID."
		AND tOrders.IsPrint = 1
		");
		return $query->result();


	}

	/*INSERT ACTUAL PAYMENT DETAILS IN torderpayment*/
	function insert_actualorderpaymentdata($OrderUID){

		$tOrders = $this->db->get_where('tOrders', array('OrderUID'=>$OrderUID))->row();
		/*CustomerFee row*/
		$reportdata = array('OrderUID' => $OrderUID,'CustomerUID'=>$tOrders->CustomerUID,'BeneficiaryType'=>'Customer','CustomerActualAmount' => $tOrders->CustomerActualAmount,'CustomerAdditionalAmount' => $tOrders->CustomerAdditionalAmount,'CustomerAmount' => $tOrders->CustomerAmount,'ApprovalFunction' => 'CustomerActualPricing','AgentPricing'=>$tOrders->AgentPricing,'UnderWritingPricing'=>$tOrders->UnderWritingPricing,'IsBilled' => $tOrders->IsBilled,'ModifiedByUserUID' => $this->session->userdata('UserUID'),'ModifiedDateTime'=>date('Y-m-d h:i:s'),'ModifiedAmount'=>0);
		$this->db->insert('tOrderPayments',$reportdata);


		/*AbstractorPricing row*/
		$assignedabstractors = $this->db->get_where('torderabstractor', array('OrderUID'=>$OrderUID))->result();

		foreach ($assignedabstractors as $assignedabstractorkey => $assignedabstractor) {

			$reportdata = array('OrderUID' => $OrderUID,'AbstractorUID'=>$assignedabstractor->AbstractorUID,'AbstractorOrderUID'=>$assignedabstractor->AbstractorOrderUID,'BeneficiaryType'=>'Abstractor','AbstractorActualFee' => $assignedabstractor->AbstractorActualFee,'AbstractorAdditionalFee' => $assignedabstractor->AbstractorAdditionalFee,'AbstractorFee' => $assignedabstractor->AbstractorFee,'AbstractorCopyCost' => $assignedabstractor->AbstractorCopyCost,'ApprovalFunction' => 'AbstractorActualPricing','OperatorType'=>$assignedabstractor->OperatorType,'IsBilled' => $tOrders->IsBilled,'ModifiedByUserUID' => $this->session->userdata('UserUID'),'ModifiedDateTime'=>date('Y-m-d h:i:s'),'ModifiedAmount'=>0);
			$this->db->insert('tOrderPayments',$reportdata);
			
		}
	}


		function getorganization_sapemail(){
		$this->db->select('SAPtoEmail');
		$this->db->from('morganizations');
		return $this->db->get()->row();
	}

	function checkorder_isbilled($OrderUID){
		$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM tOrders WHERE OrderUID = '".$OrderUID."' AND IsBilled = 0) as billed");
		return $query->row()->billed;
	}
	function GetFieldsDetails()
	{
		$this->db->where(array("Active"=>1));
		$query = $this->db->get('mfields');
		return $query->result();
	}

	function formatNum($num) {
  	$intnum = (float) $num; // or (float) if you'd rather
  	return (($intnum >= 0) ? '+' : '') . number_format((float)$num, 2, '.', ''); // implicit cast back to string
	}

	function CurrencyformatNum($num) {
		$decimalnumber = $num;
  	$num = (float) $num; // or (float) if you'd rather
    return (($num >= 0) ? '+' : '') . number_format((float)$decimalnumber, 2, '.', ''); // implicit cast back to string and number to decimal places
  }

  function enable_reverseworkflow($OrderUID){
  	$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM tOrders JOIN mSubProducts ON mSubProducts.SubProductUID = tOrders.SubProductUID JOIN mProducts ON mProducts.ProductUID = mSubProducts.ProductUID  WHERE OrderUID = '".$OrderUID."' AND (StatusUID NOT IN (100,115,110,49) OR IsDynamicProduct  = 1 )) as isexists");
		return $query->row()->isexists;
  }

  function isexist_customer_payments($OrderUID){
  	$query = $this->db->query("SELECT EXISTS (SELECT * FROM `tOrderPayments` WHERE OrderUID = '".$OrderUID."' AND ApprovalFunction = 'CustomerActualPricing') AS isexists");
  	return $query->row()->isexists;
  }

  function isexist_abstractor_payments($OrderUID,$AbstractorOrderUID){
  	$query = $this->db->query("SELECT EXISTS (SELECT * FROM `tOrderPayments` WHERE OrderUID = '".$OrderUID."' AND AbstractorOrderUID = '".$AbstractorOrderUID."' AND ApprovalFunction = 'AbstractorActualPricing') AS isexists");
  	return $query->row()->isexists;
  }

  function insert_customerpaymentdata($OrderUID, $ScheduleUID = ''){

  	if($this->isexist_customer_payments($OrderUID) != 1){ 
  		$tOrders = $this->db->get_where('tOrders', array('OrderUID'=>$OrderUID))->row();
  		/*CustomerFee row*/
  		$reportdata = array('OrderUID' => $OrderUID,'CustomerUID'=>$tOrders->CustomerUID,'BeneficiaryType'=>'Customer','CustomerActualAmount' => $tOrders->CustomerActualAmount,'CustomerAdditionalAmount' => $tOrders->CustomerAdditionalAmount,'CustomerAmount' => $tOrders->CustomerAmount,'ApprovalFunction' => 'CustomerActualPricing','AgentPricing'=>$tOrders->AgentPricing,'UnderWritingPricing'=>$tOrders->UnderWritingPricing,'IsBilled' => $tOrders->IsBilled,'ModifiedByUserUID' => $this->session->userdata('UserUID'),'ModifiedDateTime'=>date('Y-m-d h:i:s'),'ModifiedAmount'=>0, 'ScheduleUID'=>$ScheduleUID);
  		$this->db->insert('tOrderPayments',$reportdata);
  	}

  }

  function insert_abstractorpaymentdata($OrderUID){
  	/*AbstractorPricing row*/
  	$assignedabstractors = $this->db->get_where('torderabstractor', array('OrderUID'=>$OrderUID))->result();
  	$unassignedabstractors = $this->db->get_where('torderabstractorunassign', array('OrderUID'=>$OrderUID,'IsFeeAdjusted'=>1))->result();
  	$abstractors = array_merge($assignedabstractors,$unassignedabstractors);
  	$tOrders = $this->db->get_where('tOrders', array('OrderUID'=>$OrderUID))->row();

  	foreach ($abstractors as $abstractorkey => $abstractor) {
  		if($this->isexist_abstractor_payments($OrderUID,$abstractor->AbstractorOrderUID) != 1){ 

  			if($tOrders->IsBilled == 2){
  				$bill = 1;
  				$this->db->query("UPDATE tOrders SET IsBilled = ".$bill." WHERE OrderUID = ".$OrderUID." ");
  			} else {
  				$bill =$tOrders->IsBilled;
  			}
  			$reportdata = array('OrderUID' => $OrderUID,'AbstractorUID'=>$abstractor->AbstractorUID,'AbstractorOrderUID'=>$abstractor->AbstractorOrderUID,'BeneficiaryType'=>'Abstractor','AbstractorActualFee' => $abstractor->AbstractorActualFee,'AbstractorAdditionalFee' => $abstractor->AbstractorAdditionalFee,'AbstractorFee' => $abstractor->AbstractorFee,'AbstractorCopyCost' => $abstractor->AbstractorCopyCost,'ApprovalFunction' => 'AbstractorActualPricing','OperatorType'=>$abstractor->OperatorType,'IsBilled' => $bill,'ModifiedByUserUID' => $this->session->userdata('UserUID'),'ModifiedDateTime'=>date('Y-m-d h:i:s'),'ModifiedAmount'=>0);
  			$this->db->insert('tOrderPayments',$reportdata);
  		}
  	}
  }

  function insert_attorneypaymentdata($OrderUID, $PricingType = "NotaryActualPricing"){
  	/*AbstractorPricing row*/
  	$assignedabstractors = $this->db->get_where('torderabstractor', array('OrderUID'=>$OrderUID))->result();
  	$unassignedabstractors = $this->db->get_where('torderabstractorunassign', array('OrderUID'=>$OrderUID,'IsFeeAdjusted'=>1))->result();
  	$abstractors = array_merge($assignedabstractors,$unassignedabstractors);
  	$tOrders = $this->db->get_where('tOrders', array('OrderUID'=>$OrderUID))->row();

  	foreach ($abstractors as $abstractorkey => $abstractor) {
  		if($this->isexist_abstractor_payments($OrderUID,$abstractor->AbstractorOrderUID) != 1){ 

  			if($tOrders->IsBilled == 2){
  				$bill = 1;
  				$this->db->query("UPDATE tOrders SET IsBilled = ".$bill." WHERE OrderUID = ".$OrderUID." ");
  			} else {
  				$bill =$tOrders->IsBilled;
  			}
  			$reportdata = array('OrderUID' => $OrderUID,'AbstractorUID'=>$abstractor->AbstractorUID,'AbstractorOrderUID'=>$abstractor->AbstractorOrderUID,'BeneficiaryType'=>'Abstractor','AbstractorActualFee' => $abstractor->AbstractorActualFee,'AbstractorAdditionalFee' => $abstractor->AbstractorAdditionalFee,'AbstractorFee' => $abstractor->AbstractorFee,'AbstractorCopyCost' => $abstractor->AbstractorCopyCost,'ApprovalFunction' => $PricingType,'OperatorType'=>$abstractor->OperatorType,'IsBilled' => $bill,'ModifiedByUserUID' => $this->session->userdata('UserUID'),'ModifiedDateTime'=>date('Y-m-d h:i:s'),'ModifiedAmount'=>0);
  			$this->db->insert('tOrderPayments',$reportdata);
  		}
  	}
  }

  function updateabstractorfee_payments($OrderUID,$AbstractorOrderUID,$Iscancelled=0){


  	if($Iscancelled == '1'){
  		$query = $this->db->query("SELECT SUM(CONCAT(`tOrderPayments`.`OperatorType`, '', `tOrderPayments`.`AbstractorAdditionalFee`)) AS AbstractorAdditionalFee,SUM(tOrderPayments.AbstractorCopyCost) AS AbstractorCopyCost,(SUM(CONCAT(`tOrderPayments`.`OperatorType`, '', `tOrderPayments`.`AbstractorAdditionalFee`)) + SUM(tOrderPayments.AbstractorCopyCost)+torderabstractorunassign.AbstractorActualFee) AS TotalAbstractorFee FROM tOrderPayments JOIN torderabstractorunassign ON torderabstractorunassign.AbstractorOrderUID = tOrderPayments.AbstractorOrderUID WHERE tOrderPayments.OrderUID = '".$OrderUID."' AND tOrderPayments.AbstractorOrderUID = '".$AbstractorOrderUID."'  AND ApprovalFunction IN ('AbstractorActualPricing','AbstractorPricingAdjustments','AbstractorPricingOverride')");
  	}else{
  		$query = $this->db->query("SELECT SUM(CONCAT(`tOrderPayments`.`OperatorType`, '', `tOrderPayments`.`AbstractorAdditionalFee`)) AS AbstractorAdditionalFee,SUM(tOrderPayments.AbstractorCopyCost) AS AbstractorCopyCost,(SUM(CONCAT(`tOrderPayments`.`OperatorType`, '', `tOrderPayments`.`AbstractorAdditionalFee`)) + SUM(tOrderPayments.AbstractorCopyCost)+torderabstractor.AbstractorActualFee) AS TotalAbstractorFee FROM tOrderPayments JOIN torderabstractor ON torderabstractor.AbstractorOrderUID = tOrderPayments.AbstractorOrderUID WHERE tOrderPayments.OrderUID = '".$OrderUID."' AND tOrderPayments.AbstractorOrderUID = '".$AbstractorOrderUID."'  AND ApprovalFunction IN ('AbstractorActualPricing','AbstractorPricingAdjustments','AbstractorPricingOverride')");
  	}
  	$result =  $query->row();

  	$tOrders_abstractor['AbstractorFee'] = $result->TotalAbstractorFee; 
  	$tOrders_abstractor['OperatorType'] = '+'; 
  	$tOrders_abstractor['AbstractorAdditionalFee'] = $result->AbstractorAdditionalFee;
  	$tOrders_abstractor['AbstractorCopyCost'] = $result->AbstractorCopyCost;
  	$this->db->where('AbstractorOrderUID', $AbstractorOrderUID);

  	if($Iscancelled == '1'){
  		$this->db->update('torderabstractorunassign', $tOrders_abstractor);
  	}else{
  		$this->db->update('torderabstractor', $tOrders_abstractor);
  	}
  }

    function updatecustomerfee_payments($OrderUID){
	
  	$query = $this->db->query("SELECT SUM(CONCAT(`tOrderPayments`.`OperatorType`, '', `tOrderPayments`.`CustomerAdditionalAmount`)) AS CustomerAdditionalAmount,SUM(CONCAT(`tOrderPayments`.`OperatorType`, '', `tOrderPayments`.`CustomerAdditionalAmount`))+ CASE WHEN tOrders.CustomerActualAmount IS NULL THEN 0 ELSE tOrders.CustomerActualAmount END +SUM(tOrderPayments.AgentPricing)+SUM(tOrderPayments.UnderWritingPricing) AS CustomerTotalAmount,SUM(tOrderPayments.UnderWritingPricing) AS UnderWritingPricing,SUM(tOrderPayments.AgentPricing)  AS AgentPricing FROM tOrderPayments JOIN tOrders ON tOrders.OrderUID = tOrderPayments.OrderUID  WHERE tOrderPayments.OrderUID = '".$OrderUID."'  AND BeneficiaryType = 'Customer' ");
  	$result =  $query->row();
  	$tOrders['CustomerAdditionalAmount'] = $result->CustomerAdditionalAmount; 
  	$tOrders['OperatorType'] = '+'; 
  	$tOrders['CustomerAmount'] = $result->CustomerTotalAmount;
  	$tOrders['UnderWritingPricing'] = $result->UnderWritingPricing;
  	$tOrders['AgentPricing'] = $result->AgentPricing;

  	$this->db->where('OrderUID', $OrderUID);
  	$this->db->update('tOrders', $tOrders);
  }

  function delete_abstractorchanges_payments($OrderUID,$AbstractorOrderUID){
    $this->db->where('OrderUID', $OrderUID);
    $this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
    $this->db->delete('tOrderPayments');

    //$this->db->where('OrderUID', $OrderUID);
    //$this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
    //$this->db->delete('torderapprovals');
  }


  function get_subproducts_by_groupuids($GroupUIDs){
  	/*FOR SUPERVISOR CHECK*/
  	$where = '';
  	if ($this->session->userdata('RoleType') == 6){
  		$UserProducts = $this->common_model->_get_product_bylogin();
  		if($UserProducts): $where .= ' AND GroupCustomerProductUID IN ('.$UserProducts.')'; else: return array(); endif;
  	}

  	$query = $this->db->query("SELECT mgroupcustomers.GroupCustomerProductUID as ProductUID,mgroupcustomers.GroupCustomerSubProductUID As SubProductUID,ProductName,SubProductName FROM mgroups LEFT JOIN mgroupcustomers ON mgroupcustomers.GroupUID = mgroups.GroupUID  LEFT JOIN mSubProducts on mSubProducts.SubProductUID = mgroupcustomers.GroupCustomerSubProductUID LEFT JOIN mProducts on mProducts.ProductUID = mgroupcustomers.GroupCustomerProductUID WHERE mgroups.GroupUID IN ($GroupUIDs) $where  Group by mgroupcustomers.GroupCustomerSubProductUID");
  	return $query->result();
  }

  function change_abstractorchanges_payments($OrderUID,$AbstractorOrderUID,$UnassignedAstractorUID){

  	$data['OldAbstractorOrderUID'] = $AbstractorOrderUID; 
  	$data['AbstractorOrderUID'] = $UnassignedAstractorUID; 
  	$data['ModifiedByUserUID'] = $this->session->userdata('UserUID'); 
  	$data['ModifiedDateTime'] = date('Y-m-d h:i:s'); 
  	$data['ModifiedAmount'] = 0; 
	$data['IsAbstractorCancelled'] = 1;

  	$this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
  	$this->db->where('OrderUID', $OrderUID);
  	$this->db->update('tOrderPayments', $data);
  }

  function update_billed_payments($OrderUID){
  	$unassignedabstractors = $this->db->select('GROUP_CONCAT(AbstractorOrderUID) AS AbstractorOrderUID')->get_where('torderabstractorunassign', array('OrderUID'=>$OrderUID,'IsFeeAdjusted'=>0))->row();

  	if(!empty($unassignedabstractors->AbstractorOrderUID)){

  		$this->db->query("UPDATE tOrderPayments SET IsBilled = 1 WHERE OrderUID = ".$OrderUID." AND (AbstractorOrderUID IS NULL OR AbstractorOrderUID NOT IN ('".$unassignedabstractors->AbstractorOrderUID."')) AND (IsBilled = 0  OR IsBilled IS NULL )");
  	}else{
  		$this->db->query("UPDATE tOrderPayments SET IsBilled = 1 WHERE OrderUID = ".$OrderUID." AND (IsBilled = 0  OR IsBilled IS NULL)");
  	}

  }

  function delete_customer_payments($OrderUID){
  	$this->db->where('OrderUID', $OrderUID);
    $this->db->where('BeneficiaryType', 'Customer');
    //$this->db->where_in('ApprovalFunction',array('CustomerActualPricing','CustomerPricingOverride','CustomerPricingAdjustments'));
    $this->db->delete('tOrderPayments');
  }

  function decline_abstractorapprovals($OrderUID,$AbstractorOrderUID){
  	$data['ApprovalStatus'] = 2;
  	$data['ApprovedByUserUID'] = $this->loggedid;
  	$data['ApprovedDatetime'] = date('Y-m-d H:i:s');
  	$data['Notes'] = 'System declined';

  	$tOrders_abstractor['ApprovalStatus'] = 4;
  	$this->db->where('AbstractorOrderUID', $AbstractorOrderUID);
  	$this->db->update('torderabstractor', $tOrders_abstractor);

  	$this->db->where('OrderUID',$OrderUID);
  	$this->db->where('ApprovalStatus','0');
  	$this->db->where('AbstractorOrderUID',$AbstractorOrderUID);
  	$this->db->where_in('ApprovalFunction',array('AbstractorPricingOverride','AbstractorPricingAdjustments'));
  	$this->db->update('torderapprovals', $data);
  }

  function decline_customerapprovals($OrderUID){
  	$data['ApprovalStatus'] = 2;
  	$data['ApprovedByUserUID'] = $this->loggedid;
  	$data['ApprovedDatetime'] = date('Y-m-d H:i:s');
  	$data['Notes'] = 'System declined';

  	$this->db->where('OrderUID',$OrderUID);
  	$this->db->where('ApprovalStatus','0');
  	$this->db->where_in('ApprovalFunction',array('CustomerPricingOverride','CustomerPricingAdjustments'));
  	$this->db->update('torderapprovals', $data);
  }

  function GetOrdersourceName($OrderSourceUID){
  	$this->db->select("*");
  	$this->db->from('mApiTitlePlatform');
  	$this->db->where(array("OrderSourceUID"=>$OrderSourceUID));
  	$query = $this->db->get();
  	return $query->row();
  }


  /*^^^^ Mischellaneous Functions Can be used any where with simple parameters Starts^^^ */


	// Return Result.
	public function get($_table_name, $filter = NULL, $_order_by=NULL, $_group_by=NULL)
	{
		// CHECK TABLE IS NOT EMPTY
		if (empty($_table_name)) {
			return false;
		}

		// DECIDE IF FILTER TABLE BY ARRAY OR STRING
		if (is_array($filter) && !empty($filter)) {
			$this->db->where($filter);			
		}
		elseif (!empty($filter) && !empty($keyword)) {
			$this->db->where($filter, $keyword);
		}

		// DECIDE IF ORDER BY TABLE USING ARRAY OR STRING
		if (is_array($_order_by) && !empty($_order_by)) {
			foreach ($_order_by as $key => $value) {
				if ($value=='DESC') {
					$this->db->order_by($key, $value);
				}
				else{
					$this->db->order_by($key, 'ASC');
				}
			}
		}

		// DECIDE IF GROUP BY TABLE USING ARRAY OR STRING
		if (is_array($_group_by) && !empty($_group_by)) {
			foreach ($_group_by as $key => $value) {
				$this->db->group_by($value);
			}
		}
		else if (!empty($_group_by)) {
			$this->db->group_by($_group_by);
		}

		return $this->db->get($_table_name)->result();


	}

	// Return Row.
	public function get_row($_table_name, $filter = NULL, $_order_by=NULL, $_group_by=NULL)
	{

		// CHECK TABLE IS NOT EMPTY
		if (empty($_table_name)) {
			return false;
		}

		// DECIDE IF FILTER TABLE BY ARRAY OR STRING
		if (is_array($filter) && !empty($filter)) {
			$this->db->where($filter);			
		}
		elseif (!empty($filter) && !empty($keyword)) {
			$this->db->where($filter, $keyword);
		}

		// DECIDE IF ORDER BY TABLE USING ARRAY OR STRING
		if (is_array($_order_by) && !empty($_order_by)) {
			foreach ($_order_by as $key => $value) {
				if ($value=='DESC') {
					$this->db->order_by($key, $value);
				}
				else{
					$this->db->order_by($key, 'ASC');
				}

			}
		}

		// DECIDE IF GROUP BY TABLE USING ARRAY OR STRING
		if (is_array($_group_by) && !empty($_group_by)) {
			foreach ($_group_by as $key => $value) {
				$this->db->group_by($value);
			}
		}
		else if (!empty($_group_by)) {
			$this->db->group_by($_group_by);
		}

		return $this->db->get($_table_name)->row();

	}

	public function save($_table_name, $data, $primarykey = NULL, $value = NULL)
	{
		// Insert
		if ($primarykey === NULL) {
			if ( !empty($data) && ( is_array($data) || is_object($data) ) ) {
				$this->db->insert($_table_name, $data);
				$id =  $this->db->insert_id();
			}
			return $id;			
		}
		// Update
		else{
			if ( !empty($data) && ( is_array($data) || is_object($data) ) ) {
				$this->db->set($data);
				if (is_array($primarykey) && empty($value)) {					
					$this->db->where($primarykey);
					$this->db->update($_table_name);
					$id = true;
				}
				elseif($primarykey != null && $value != null ){
					$this->db->where($primarykey, $value);
					$this->db->update($_table_name);
					$id = $value;
				}

				return $id;			
			}
		return false;			
		}

	}
	// Delete
	public function delete($_table_name, $primary_key, $value)
	{
		// Insert
		if ($primary_key != NULL && $value != NULL) {
			$this->db->where($primary_key, $value);
			$this->db->from($_table_name);
			$this->db->delete();
			return true;
		}
		return false;
	}

	function return_arrayobject_key($id, $slug, $array) {
		foreach ($array as $key => $val) {
			if ($val->$slug == $id) {
				return $key;
			}
		}
		return NULL;
	}

	function return_array_key($id, $array) {
		foreach ($array as $key => $val) {
			if ($val == $id) {
				return $key;
			}
		}
		return NULL;
	}

  /*^^^^ Mischellaneous Functions Can be used any where with simple parameters Ends^^^ */

  function cleanString($string)
  {
		// strip slashes
  	$string = trim($string,'/');
  	$string = stripslashes($string);
    $string = preg_replace("/[^a-zA-Z0-9]+/", "", html_entity_decode($string, ENT_QUOTES));
  	return $string;
  }

  function get_workflow_detailbyuid($WorkflowModuleUID){
  	$this->db->select('*');
  	$this->db->from('mworkflowmodules');
  	$this->db->where('WorkflowModuleUID',$WorkflowModuleUID);
  	$query = $this->db->get();
  	return $query->row();
  }

  function Customer_workflow_exists($OrderUID,$WorkflowModuleUID)
  {
  	$query = $this->db->query("SELECT EXISTS (SELECT 1 FROM (`mcustomerworkflowmodules`) INNER JOIN `tOrders` ON `mcustomerworkflowmodules`.`CustomerUID`=`tOrders`.`CustomerUID` AND mcustomerworkflowmodules.SubProductUID=tOrders.SubProductUID WHERE `OrderUID` = '".$OrderUID."' AND `WorkflowModuleUID` = '".$WorkflowModuleUID."' ORDER BY `WorkflowModuleUID` ASC) AS available");
  	return $query->row()->available;
  }

  //assign workflow to users
  function assign_workflow($OrderUID,$WorkflowUID,$loggedid)
  {
  	$assign_data = array(
  		'OrderUID' => $OrderUID,
  		'WorkflowModuleUID' => $WorkflowUID,
  		'AssignedDatetime' =>  Date('Y-m-d H:i:s',strtotime("now")),
  		'AssignedByUserUID' => $loggedid,
  		'AssignedToUserUID' => $loggedid,
  		'WorkflowStatus' => 0,
  		'OrderFlag'=>1,
  	);
  	$is_vendor_login = $this->common_model->is_vendorlogin();

  	if($is_vendor_login){
  		$assign_data['SendTovendor'] = '1';
  	}else{
  		$assign_data['SendTovendor'] = '0';
  		$assign_data['VendorUID'] = '';
  		$assign_data['VendorAssignedByUserUID'] = '';
  		$assign_data['VendorAssignedDateTime'] = '';
  		$assign_data['VendorCompletedDateTime'] = '';
  		$assign_data['QCCompletedDateTime'] = NULL;
  		$assign_data['QCAssignedByUserUID'] = NULL;
  		$assign_data['QCAssignedToUserUID'] = NULL;
  		$assign_data['QCAssignedDateTime'] = NULL;
  	}
  	$inserted = $this->db->insert('torderassignment',$assign_data);
  	if($this->db->affected_rows()>0)
  	{
  		return 1;
  	} else {
  		return 0;
  	}
  }
 

  //check workflow completed in torderassignment table
  function isworkflow_completed($OrderUID, $WorkflowModuleUID){
  	$query = $this->db->query("SELECT EXISTS (SELECT 1 FROM torderassignment WHERE OrderUID = '".$OrderUID."' AND WorkflowModuleUID = '".$WorkflowModuleUID."' AND Workflowstatus = 5) AS completed ");
  	return $query->row()->completed;
  }

  function get_order_detail($OrderUID)
  {

  	$this->db->select ( 'tOrders.CustomerUID AS CustomerUID,CustomerName,CustomerRefNum,tOrders.SubProductUID AS SubproductUID,tOrders.OwnerOccupancy,tOrders.SigningLocalDate,mProducts.ProductUID AS ProductUID,ProductName,StatusUID,tOrders.OrderUID AS OrderUID,LoanNumber,OrderNumber,PropertyAddress1,PropertyAddress2,PropertyZipcode,PropertyCityName,PropertyCountyName,PropertyStateCode, mSubProducts.ScheduleDuration,mSubProducts.IsRefinance,tOrders.APIOrder');
  	$this->db->from ( 'tOrders' );
  	$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
  	$this->db->join ( 'mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID' , 'left' );
  	$this->db->join ( 'mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID' , 'left' );
  	$this->db->where ('tOrders.OrderUID',$OrderUID);
  	$query = $this->db->get();
  	return $query->row();

	}

	public function GetBorrowerDetails($OrderUID)
	{

		// $this->db->select('Id,PRName');
		// $this->db->from('tOrderPropertyRoles');
		// $this->db->join('tOrderscheduleBorrower','tOrderscheduleBorrower.BorrowerUID=tOrderPropertyRoles.Id','left');
		// // $this->db->join('tOrderschedule','tOrderschedule.ScheduleUID=tOrderscheduleBorrower.ScheduleUID','left');
		// // $Notinstatus = array('Reschedule','Assign');
		// // $this->db->where_not_in('tOrderschedule.ScheduleStatus',$Notinstatus);
		// $this->db->where("NOT EXISTS (SELECT 1 FROM tOrderschedule WHERE tOrderschedule.OrderUID = tOrderPropertyRoles.OrderUID AND tOrderschedule.ScheduleStatus in('Reschedule','Assign'))",NULL,FALSE);
		// $this->db->where('tOrderPropertyRoles.OrderUID',$OrderUID);
		// $this->db->group_by('tOrderPropertyRoles.Id');
		// return $this->db->get()->result();
		$sql ="SELECT `Id`, `PRName` FROM (`tOrderPropertyRoles`) WHERE  NOT EXISTS (SELECT BorrowerUID FROM tOrderschedule JOIN tOrderscheduleBorrower ON tOrderscheduleBorrower.ScheduleUID = tOrderschedule.ScheduleUID  WHERE tOrderschedule.ScheduleStatus IN ('Reschedule','Assign') AND tOrderPropertyRoles.Id = BorrowerUID) AND `tOrderPropertyRoles`.`OrderUID` = '".$OrderUID."' AND tOrderPropertyRoles.PropertyRoleUID IN (5,7,27) ";
		$query = $this->db->query($sql);

		return $query->result();
	}

	/*Datatable search*/

	function Datatable_Search($post)
	{

		if (!empty($post['search']['value'])) {
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
	}

	/*Datatable OrderBy*/	
	function Datatable_OrderBy($post)
	{

		if (!empty($post['order']))
		{
      	// here order processing
			if(!empty($post['order'][0]['column']) && count($post['column_order']) > $post['order'][0]['column'])
			{
				$this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);
			}
		} 
	}

	//AuditTrail Added Code Begin
	public function InsertAuditTrail($InsetData)
	{
		$this->db->insert('taudittrail',$InsetData);
	}
	//AuditTrail Added Code End

	public function GetOrganizationLocation()
	{
		$this->db->select('*');
		$this->db->from('tOrganizationLocation');
		$this->db->where('tOrganizationLocation.OrgUID',1);
	 	return	$this->db->get()->result();
	}

	function getBorrowerCurrentAddress($BorrowerUID, $ReturnType = "String")
	{
		$tOrderPropertyRoles = $this->common_model->get_row('tOrderPropertyRoles', ['Id'=>$BorrowerUID]);
		$currentaddress = [];

		if (!empty($tOrderPropertyRoles)) {
			
			if ($tOrderPropertyRoles->IsMailingAddress == 'property') {
				$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$tOrderPropertyRoles->OrderUID]);
				$currentaddress = array(
					'AddressLine1'=>$tOrders->PropertyAddress1,
					'AddressLine2'=>$tOrders->PropertyAddress2,
					'CityName'=>$tOrders->PropertyCityName,
					'CountyName'=>$tOrders->PropertyCountyName,
					'StateCode'=>$tOrders->PropertyStateCode,
					'ZipCode'=>$tOrders->PropertyZipcode,
					'MailingAddressNotes'=>$tOrderPropertyRoles->MailingAddressNotes,
				);
			}
			else if($tOrderPropertyRoles->IsMailingAddress == 'others'){
				$currentaddress = array(
					'AddressLine1'=>$tOrderPropertyRoles->MailingAddress1,
					'AddressLine2'=>$tOrderPropertyRoles->MailingAddress2,
					'CityName'=>$tOrderPropertyRoles->MailingCityName,
					'CountyName'=>$tOrderPropertyRoles->MailingCountyName,
					'StateCode'=>$tOrderPropertyRoles->MailingStateCode,
					'ZipCode'=>$tOrderPropertyRoles->MailingZipCode,
					'MailingAddressNotes'=>$tOrderPropertyRoles->MailingAddressNotes,
				);

			}
		}
		if ($ReturnType == 'String') {
			return implode(", ", array_filter($currentaddress));			
		}
		else {
			return $currentaddress;
		}
	}

	function getBorrowerSigningAddress($OrderUID, $BorrowerUID, $ReturnType = "String", $ScheduleUID="")
	{

		if ($BorrowerUID == 'generic') {
			$currentaddress = $this->common_model->gettOrderClosingTempAddress($OrderUID);
			if ($ReturnType == 'String') {
				return implode(", ", array_filter($currentaddress));
			}
			else {
				return $currentaddress;
			}
		}

		if ($BorrowerUID == 'default') {
			$currentaddress = $this->common_model->getScheduleAddressFormatted($ScheduleUID);
			if ($ReturnType == 'String') {
				return implode(", ", array_filter($currentaddress));
			}
			else {
				return $currentaddress;
			}
		}

		$tOrderPropertyRoles = $this->common_model->get_row('tOrderPropertyRoles', ['Id'=>$BorrowerUID]);
		$currentaddress = [];
		if (!empty($tOrderPropertyRoles)) {
			
			$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$tOrderPropertyRoles->OrderUID]);
			if ($tOrderPropertyRoles->IsSigningAddress == 'property'/* || empty($tOrderPropertyRoles->IsSigningAddress)*/ ) {
				$currentaddress = array(
					'AddressLine1'=>$tOrders->PropertyAddress1,
					'AddressLine2'=>$tOrders->PropertyAddress2,
					'CityName'=>$tOrders->PropertyCityName,
					'CountyName'=>$tOrders->PropertyCountyName,
					'StateCode'=>$tOrders->PropertyStateCode,
					'ZipCode'=>$tOrders->PropertyZipcode,
					'MailingAddressNotes'=>!empty($tOrderPropertyRoles->SigningAddressNotes) ? $tOrderPropertyRoles->SigningAddressNotes : $tOrders->AddressNotes,
					'SpecialInstruction'=>!empty($tOrderPropertyRoles->SigningSpecialInstruction) ? $tOrderPropertyRoles->SigningSpecialInstruction : $tOrders->SpecialInstruction,
				);
			}
			else if($tOrderPropertyRoles->IsSigningAddress == 'mailing'){

				if ($tOrderPropertyRoles->IsMailingAddress == 'property') {

					$currentaddress = array(
						'AddressLine1'=>$tOrders->PropertyAddress1,
						'AddressLine2'=>$tOrders->PropertyAddress2,
						'CityName'=>$tOrders->PropertyCityName,
						'CountyName'=>$tOrders->PropertyCountyName,
						'StateCode'=>$tOrders->PropertyStateCode,
						'ZipCode'=>$tOrders->PropertyZipcode,
						'MailingAddressNotes'=>!empty($tOrderPropertyRoles->SigningAddressNotes) ? $tOrderPropertyRoles->SigningAddressNotes : $tOrders->AddressNotes,
						'SpecialInstruction'=>!empty($tOrderPropertyRoles->SigningSpecialInstruction) ? $tOrderPropertyRoles->SigningSpecialInstruction : $tOrders->SpecialInstruction,
					);
					
				}
				else{
					$currentaddress = array(
						'AddressLine1'=>$tOrderPropertyRoles->MailingAddress1,
						'AddressLine2'=>$tOrderPropertyRoles->MailingAddress2,
						'CityName'=>$tOrderPropertyRoles->MailingCityName,
						'CountyName'=>$tOrderPropertyRoles->MailingCountyName,
						'StateCode'=>$tOrderPropertyRoles->MailingStateCode,
						'ZipCode'=>$tOrderPropertyRoles->MailingZipCode,
						'MailingAddressNotes'=>!empty($tOrderPropertyRoles->MailingAddressNotes) ? $tOrderPropertyRoles->MailingAddressNotes : $tOrders->AddressNotes,
						'SpecialInstruction'=>!empty($tOrderPropertyRoles->SigningSpecialInstruction) ? $tOrderPropertyRoles->SigningSpecialInstruction : $tOrders->SpecialInstruction,

					);
				}

			}
			else if($tOrderPropertyRoles->IsSigningAddress == 'others'){
				$currentaddress = array(
					'AddressLine1'=>$tOrderPropertyRoles->SigningAddress1,
					'AddressLine2'=>$tOrderPropertyRoles->SigningAddress2,
					'CityName'=>$tOrderPropertyRoles->SigningCityName,
					'CountyName'=>$tOrderPropertyRoles->SigningCountyName,
					'StateCode'=>$tOrderPropertyRoles->SigningStateCode,
					'ZipCode'=>$tOrderPropertyRoles->SigningZipCode,
					'MailingAddressNotes'=>!empty($tOrderPropertyRoles->SigningAddressNotes) ? $tOrderPropertyRoles->SigningAddressNotes : $tOrders->AddressNotes,
					'SpecialInstruction'=>!empty($tOrderPropertyRoles->SigningSpecialInstruction) ? $tOrderPropertyRoles->SigningSpecialInstruction : $tOrders->SpecialInstruction,

				);

			}
		}
		if ($ReturnType == 'String') {
			return implode(", ", array_filter($currentaddress));
		}
		else {
			return $currentaddress;
		}
	}

	function getAllScheduleDetailsForOrder($OrderUID)
	{
		$this->db->select('*, GROUP_CONCAT(PRName SEPARATOR " and ") AS Borrowers, DATE_FORMAT(tOrderschedule.SigningDateTime, "%m/%d/%Y %h:%i %p") AS fSigningDateTime', false);
		$this->db->from('tOrderschedule');
		$this->db->join('tOrderscheduleBorrower', 'tOrderschedule.ScheduleUID = tOrderscheduleBorrower.ScheduleUID');
		$this->db->join('tOrdersign', 'tOrderschedule.ScheduleUID = tOrdersign.ScheduleUID');
		$this->db->join('tOrderPropertyRoles', 'tOrderPropertyRoles.Id = tOrderscheduleBorrower.BorrowerUID');
		$this->db->join('mpropertyroles', 'tOrderPropertyRoles.PropertyRoleUID = mpropertyroles.PropertyRoleUID');
		$this->db->where('tOrderschedule.OrderUID', $OrderUID);
		$this->db->where_not_in('tOrderschedule.ScheduleStatus', ['Cancel']);
		$this->db->group_by('tOrderschedule.ScheduleUID');
		return $this->db->get()->result();
	}

	function get_plainorderenabled()
	{	
		$query = $this->db->query("SELECT EXISTS (SELECT 1 FROM musers WHERE UserUID = '".$this->loggedid."' AND IsNewMyOrder = 1 ) AS IsNewMyOrder ");
		return $query->row()->IsNewMyOrder;
	}

	function WorkflowQueues_Datatable_Search($post)
	{

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
	}

	function WorkflowQueues_Datatable_OrderBy($post)
	{

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
        else{
          $this->db->order_by('tOrders.OrderNumber', 'ASC');
        }

	}

	function getGroupsCustomersByUserUID($UserUID)
	{
		$groupusers = $this->common_model->get('mgroupusers', ['GroupUserUID'=>$UserUID]);
		$GroupUID = $GroupCustomerUID = [0];
		foreach ($groupusers as $key => $groupuser) {
			$GroupUID[] = $groupuser->GroupUID;
		}

		$this->db->where_in('GroupUID', $GroupUID);
		$groupcustomers = $this->db->get('mgroupcustomers')->result();

		foreach ($groupcustomers as $key => $value) {
			$GroupCustomerUID[] = $value->GroupCustomerUID;
		}

		return $GroupCustomerUID;
	}


	function getGroupsSubProductByUserUID($UserUID)
	{

		$groupusers = $this->common_model->get('mgroupusers', ['GroupUserUID'=>$UserUID]);

		$GroupUID = $GroupCustomerSubProductUID = [0];
		foreach ($groupusers as $key => $groupuser) {
			$GroupUID[] = $groupuser->GroupUID;
		}

		$this->db->where_in('GroupUID', $GroupUID);
		$groupcustomers = $this->db->get('mgroupcustomers')->result();

		foreach ($groupcustomers as $key => $value) {
			$GroupCustomerSubProductUID[] = $value->GroupCustomerSubProductUID;
		}

		return $GroupCustomerSubProductUID;
	}


	function get_mdoctypes()
	{
		$this->db->select('*');
		$this->db->from('mDocType');
		$this->db->order_by('DocumentName');
		return $this->db->get()->result();
	}

	function get_assignable_customer_workflow($CustomerUID = '', $SubProductUID = '')
	{
		$this->db->distinct();
		$this->db->select('CustomerUID,WorkflowModuleName,mcustomerworkflowmodules.WorkflowModuleUID');
		$this->db->from('mcustomerworkflowmodules');
		$this->db->join('mworkflowmodules', 'mcustomerworkflowmodules.workflowmoduleUID = mworkflowmodules.WorkflowModuleUID');
		$this->db->where('mcustomerworkflowmodules.CustomerUID', $CustomerUID);
		$this->db->where('mcustomerworkflowmodules.SubProductUID', $SubProductUID);
		$this->db->where_not_in('mcustomerworkflowmodules.WorkflowModuleUID', $this->config->item('NotAssignableWorkflows'));
		$query = $this->db->get();
		return $query->result();
	}

	public function getAllAssignedVendors($OrderUID)
	{

		$sql = "SELECT mabstractor.*, torderabstractor.*, tOrderClosing.*, torderabstractor.AbstractorOrderUID,
				mabstractor.AbstractorUID,mabstractor.AbstractorFirstName, 
				CASE WHEN torderabstractor.RoleCategoryUID = 3 THEN 'Notary' WHEN torderabstractor.RoleCategoryUID = 2 THEN	'Attorney' ELSE	'Abstractor' END AS Type,
				torderabstractor.AssignedDateTime
				FROM
				torderabstractor
				JOIN mabstractor ON mabstractor.AbstractorUID = torderabstractor.AbstractorUID
				JOIN tOrderschedule ON torderabstractor.AbstractorOrderUID = tOrderschedule.AbstractorOrderUID
				JOIN tOrderClosing ON tOrderClosing.ScheduleUID = tOrderschedule.ScheduleUID
				WHERE
				torderabstractor.OrderUID = '".$OrderUID."'
				ORDER BY AssignedDateTime";

		return $this->db->query($sql)->result();
	}

	function getProductDetailsBySubProductUID($SubProductUID)
	{
		$this->db->select('*');
		$this->db->from('mSubProducts');
		$this->db->join('mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID');
		$this->db->where('mSubProducts.SubProductUID', $SubProductUID);
		return $this->db->get()->row();
	}

	// function get_statesearchmode($State,$County)
	// {
	// 	$query = $this->db->query("SELECT SearchModeName FROM mStatesearchMode JOIN mCounties ON mCounties.CountyUID = mStatesearchMode.CountyUID JOIN mStates ON mStates.StateUID = mStatesearchMode.StateUID WHERE StateCode = '".trim($State)."' AND CountyName = '".trim($County)."' ");
	// 	$result =  $query->row();

	// 	if(empty($result)) {
	// 		$query = $this->db->query("SELECT SearchModeName FROM mStatesearchMode JOIN mStates ON mStates.StateUID = mStatesearchMode.StateUID WHERE StateCode = '".trim($State)."' AND (mStatesearchMode.CountyUID = 0 OR mStatesearchMode.CountyUID IS NULL) ");
	// 		$result =  $query->row();
	// 	}

	// 	return $result;
	// }

	function get_pending_workflows_to_complete($CustomerUID = '', $SubProductUID = '', $OrderUID)
	{
		$this->db->distinct();
		$this->db->select('mcustomerworkflowmodules.CustomerUID,mworkflowmodules.WorkflowModuleName,mcustomerworkflowmodules.WorkflowModuleUID');
		$this->db->from('mcustomerworkflowmodules');
		$this->db->join('mworkflowmodules', 'mcustomerworkflowmodules.workflowmoduleUID = mworkflowmodules.WorkflowModuleUID');
		$this->db->join('tOrders', 'mcustomerworkflowmodules.CustomerUID = tOrders.CustomerUID AND mcustomerworkflowmodules.SubProductUID = tOrders.SubProductUID', 'left');
		$this->db->join('torderassignment', 'tOrders.OrderUID = torderassignment.OrderUID AND mcustomerworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID', 'left');
		$this->db->where('mcustomerworkflowmodules.CustomerUID', $CustomerUID);
		$this->db->where('mcustomerworkflowmodules.SubProductUID', $SubProductUID);
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->where('torderassignment.WorkflowStatus != 5', NULL, FALSE);
		$this->db->where_not_in('mcustomerworkflowmodules.WorkflowModuleUID', $this->config->item('NotAssignableWorkflows'));
		$query = $this->db->get();
		return $query->result();
		
	}

	function gettOrderClosingTempAddress($OrderUID)
	{

		$tOrderClosingTemp = $this->common_model->gettTempClosingOrderDetailsForOrder($OrderUID);
		$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		if (!empty($tOrderClosingTemp)) {			
			$currentaddress = array(

				'AddressLine1'=>$tOrderClosingTemp->MailingAddress1,
				'AddressLine2'=>$tOrderClosingTemp->MailingAddress2,
				'ZipCode'=>$tOrderClosingTemp->MailingZipCode,
				'CityName'=>$tOrderClosingTemp->MailingCityName,
				'CountyName'=>$tOrderClosingTemp->MailingCountyName,
				'StateCode'=>$tOrderClosingTemp->MailingStateCode,
				'MailingAddressNotes'=>!empty($tOrderClosingTemp->MailingAddressNotes) ? $tOrderClosingTemp->MailingAddressNotes : $tOrders->AddressNotes,
				'SpecialInstruction'=>!empty($tOrderClosingTemp->SpecialInstruction) ? $tOrderClosingTemp->SpecialInstruction : $tOrders->SpecialInstruction,
			);

			return $currentaddress;
		}

		return [];
	}

	function getScheduleAddressFormatted($ScheduleUID)
	{

		$tOrderClosing = $this->common_model->get_row('tOrderClosing', ['ScheduleUID'=>$ScheduleUID]);
		$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		if (!empty($tOrderClosing)) {			
			$currentaddress = array(

				'AddressLine1'=>$tOrderClosing->SigningAddress1,
				'AddressLine2'=>$tOrderClosing->SigningAddress2,
				'ZipCode'=>$tOrderClosing->SigningZipCode,
				'CityName'=>$tOrderClosing->SigningCityName,
				'CountyName'=>$tOrderClosing->SigningCountyName,
				'StateCode'=>$tOrderClosing->SigningStateCode,
				'MailingAddressNotes'=>!empty($tOrderClosing->SigningAddressNotes) ? $tOrderClosing->SigningAddressNotes : $tOrders->AddressNotes,
				'SpecialInstruction'=>!empty($tOrderClosing->SpecialInstruction) ? $tOrderClosing->SpecialInstruction : $tOrders->SpecialInstruction,
			);

			return $currentaddress;
		}

		return [];
	}

	/*	DI1-T10 -  Function to fetch the role level pricing limit
	Added on 1 Nov 2019
	parameters required @permission name & @Value to be checked
	*/
	function check_role_permissions($PermissionName,$AdditionalFee = 0,$CopyCost = 0)
	{
		$permissionrow = $this->GetRoleTypebyuid($this->RoleUID);
		if(!empty($permissionrow)) {
			if(array_key_exists($PermissionName, $permissionrow)) {

				if($PermissionName == 'AbstractorFeeLimit' ) {
					if($permissionrow->{$PermissionName} >= $AdditionalFee && $permissionrow->{$PermissionName} >= $CopyCost) {
						return true;
					}
				} elseif ($PermissionName == 'CustomerPricingLimit') {
					if($permissionrow->{$PermissionName} >= $AdditionalFee) {
						return true;
					}

				} else {
					if($permissionrow->{$PermissionName} == 1) {
						return true;
					}
				}

			}

		}
		return false;
	}

  function validateDate($date, $format = 'Y-m-d')
  {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
  }
  
  function GetUserDetailsByCustomerUID($CustomerUID){
    $this->db->select("*");
    $this->db->from('mCustomers');
    $this->db->where(array("mCustomers.CustomerUID"=>$CustomerUID));
    $query = $this->db->get();
    return $query->row();
  }
  
  function _get_subproduct_byproductcustomer($CustomerUID,$ProductUID)
  {
    $custwhere = '';
    if($CustomerUID != ''){
      $custwhere = "AND CustomerUID = '".$CustomerUID."'";
    }
    $query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT SubProductUID SEPARATOR ',') AS SubProductUID FROM (`mCustomerProducts`) WHERE `mCustomerProducts`.`ProductUID` IN (".$ProductUID.") $custwhere ");
    $result = $query->row();
    if(!empty($result)){
      return $result->SubProductUID;
    }
    return null;
  }


	/* D-2-T9 CREATE PACKAGE NUMBER BASED ON LOAN NUMBER*/
	function is_packageuidavailable($OrderUID)
	{	
		$query = $this->db->query("SELECT EXISTS (SELECT 1 FROM tOrders WHERE OrderUID = '".$OrderUID."' AND (PackageUID IS NOT NULL OR PackageUID <> '')) AS available ");
		return $query->row()->available;
	}


	/* D-2-T9 CREATE PACKAGE NUMBER BASED ON LOAN NUMBER*/
	function create_new_package_number($OrderUID,$LoanNumber)
	{
		//create new package number
		$date = date("y");
		$datequery = date("Y");
		$currdate = Date('Y-m-d H:i:s',strtotime("now"));
		$id = sprintf("%05d", 0);
		$PackageNumber = false;

		$code = $this->config->item('PackageCode');
		$lastOrderNo = $code . $date . $id;
		$last_row = $this->db->select('PackageNumber')->where("(PackageNumber <> '' AND PackageNumber IS NOT NULL)",null,false)->where('YEAR(CreatedDateTime)',$datequery)->order_by('PackageUID', "DESC")->limit(1)->get('tOrderPackage')->row();

		if (!empty($last_row)) {
			$lastOrderNo = $last_row->PackageNumber;
		}

		$db_2digitdate = substr($lastOrderNo, strlen($code), strlen((string)$date));
		if ($date == $db_2digitdate) {
			
			if(!empty($LoanNumber)) {
				//loann number matches with the existing loan number
				$this->db->select('tOrderPackage.PackageUID')->from('tOrders');
				$this->db->join('tOrderPackage','tOrderPackage.PackageUID = tOrders.PackageUID');
				$this->db->where('LoanNumber',$LoanNumber);
				$this->db->where("(tOrderPackage.PackageUID <> '' AND tOrderPackage.PackageUID IS NOT NULL)",null,false);
				$this->db->where('YEAR(CreatedDateTime)',$datequery);
				$this->db->order_by('tOrderPackage.PackageUID', "DESC");
				$this->db->limit(1);
				$package_no_available = $this->db->get()->row();
				if(!empty($package_no_available)) {
					if(!empty($package_no_available->PackageUID)) {
						return $package_no_available->PackageUID;
					}
				}	
			}

			$lastOrderNosliced = substr($lastOrderNo, (strlen($code) + strlen($date)));
			$id = sprintf("%05d", (int)$lastOrderNosliced + 1);
		} else {
			$id = sprintf("%05d", 1);
		}

		if(!empty($LoanNumber)) {
			$PackageNumber = $code . $date . $id.'-'.$LoanNumber;
		} else  {
			$PackageNumber = $code . $date . $id;
		}

		if(!empty($PackageNumber)) {
			$packagealreadyexists = $this->db->select('PackageUID')->where('PackageNumber',$PackageNumber)->limit(1)->get('tOrderPackage')->row();
			if(!empty($packagealreadyexists)) {
				return $packagealreadyexists->PackageUID;
			}
			//tOrderPackage data
			$packagedata = array(
				'PackageNumber'=>$PackageNumber,
				'CreatedDateTime'=>$currdate,
				'CreatedByUserUID'=>$this->loggedid,
			);
			$this->db->insert('tOrderPackage',$packagedata);
			return $this->db->insert_id();
		}

	}

	/* D-2-T9 GENEARATE PACKAGE NUMBER BASED ON LOAN NUMBER*/
	function save_package_number($OrderUID,$LoanNumber)
	{
		$PackageUID = false;
		if(!empty($OrderUID)) {
			$is_packageuidavailable = $this->is_packageuidavailable($OrderUID);
			if($is_packageuidavailable == 1) {
				return false;
			}				

			//check package already created
			$PackageUID = $this->create_new_package_number($OrderUID,$LoanNumber);

			//insert PackagUID in torder
			if(!empty($PackageUID)) {
				$this->db->set('PackageUID', $PackageUID); 
				$this->db->where('OrderUID', $OrderUID);   
				$this->db->update('tOrders'); 
			}
			return true;
		}
		return false;
	}

	function updateClosingCustomerPricing($OrderUID)
	{
		$closing_pricing = new Customer_closing_pricing();
		$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		$this->db->select('tOrderschedule.ScheduleUID, torderabstractor.AbstractorUID, tOrderClosing.*');
		$this->db->select('CASE WHEN IsNotary = 1 THEN "Notary" ELSE CASE WHEN IsAttorney = 1 THEN "Attorney" END END AS Type', false);
		$this->db->from('tOrderschedule');
		$this->db->join('torderabstractor', 'torderabstractor.AbstractorOrderUID = tOrderschedule.AbstractorOrderUID');
		$this->db->join('mabstractor', 'torderabstractor.AbstractorUID = mabstractor.AbstractorUID');
		$this->db->join('tOrderClosing', 'tOrderschedule.ScheduleUID = tOrderClosing.ScheduleUID');
		$this->db->where('tOrderschedule.OrderUID', $OrderUID);
		$this->db->order_by('tOrderschedule.ScheduleUID', 'ASC');
		$tOrderschedule = $this->db->get()->row();

		if (!empty($tOrderschedule)) {
			
			$mRoleCategory = $this->common_model->get_row('mRoleCategory', ['RoleCategoryName'=>$tOrderschedule->Type]);
			if (!empty($mRoleCategory)) {
				$Order = $tOrders;
				$Order->PropertyStateCode = $tOrderschedule->SigningStateCode;
				$Order->PropertyCountyName = $tOrderschedule->SigningCountyName;
				$Order->RoleCategoryUID = $mRoleCategory->RoleCategoryUID;
				$customer_pricing = $closing_pricing->get_Closing_Pricings($Order);

				$tOrderPayments = $this->common_model->get_row('tOrderPayments', ['ScheduleUID'=>$tOrderschedule->ScheduleUID]);

				$reportdata = array('OrderUID' => $OrderUID,'CustomerActualAmount' => $customer_pricing->Pricing,'CustomerAdditionalAmount' => 0,'CustomerAmount' => $customer_pricing->Pricing,'ApprovalFunction' => 'CustomerActualPricing','ModifiedByUserUID' => $this->session->userdata('UserUID'),'ModifiedDateTime'=>date('Y-m-d h:i:s'),'ModifiedAmount'=>$tOrderPayments->CustomerAmount - $customer_pricing->Pricing);

				$this->common_model->save('tOrderPayments', $reportdata, ['ScheduleUID'=>$tOrderschedule->ScheduleUID]);


			}
		}
	}


	function updateClosingScheduleCustomerPricing($OrderUID, $ScheduleUID)
	{
		$closing_pricing = new Customer_closing_pricing();
		$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		$tOrderPayments = $this->common_model->get_row('tOrderPayments', ['ScheduleUID'=>$ScheduleUID]);

		$this->db->select('tOrderschedule.ScheduleUID, torderabstractor.AbstractorUID, tOrderClosing.*');
		$this->db->select('CASE WHEN IsNotary = 1 THEN "Notary" ELSE CASE WHEN IsAttorney = 1 THEN "Attorney" END END AS Type', false);
		$this->db->from('tOrderschedule');
		$this->db->join('torderabstractor', 'torderabstractor.AbstractorOrderUID = tOrderschedule.AbstractorOrderUID');
		$this->db->join('mabstractor', 'torderabstractor.AbstractorUID = mabstractor.AbstractorUID');
		$this->db->join('tOrderClosing', 'tOrderschedule.ScheduleUID = tOrderClosing.ScheduleUID');
		$this->db->where('tOrderschedule.ScheduleUID', $ScheduleUID);
		$this->db->order_by('tOrderschedule.ScheduleUID', 'ASC');
		$tOrderschedule = $this->db->get()->row();

		if (!empty($tOrderschedule)) {
			
			$mRoleCategory = $this->common_model->get_row('mRoleCategory', ['RoleCategoryName'=>$tOrderschedule->Type]);
			if (!empty($mRoleCategory)) {
				$Order = $tOrders;
				$Order->PropertyStateCode = $tOrderschedule->SigningStateCode;
				$Order->PropertyCountyName = $tOrderschedule->SigningCountyName;
				$Order->RoleCategoryUID = $mRoleCategory->RoleCategoryUID;
				$customer_pricing = $closing_pricing->get_Closing_Pricings($Order);

				if (!empty($tOrderPayments)) {
					
					if ($tOrderPayments->ApprovalFunction == 'CustomerPricingAdjustments') {

						$reportdata = array('OrderUID' => $OrderUID,'CustomerActualAmount' => $tOrders->CustomerActualAmount,'CustomerAdditionalAmount' => $customer_pricing->DualClosingFee,'CustomerAmount' => ($tOrders->CustomerAmount - $tOrderPayments->CustomerAdditionalAmount) + $customer_pricing->DualClosingFee,'ApprovalFunction' => 'CustomerPricingAdjustments','ModifiedByUserUID' => $this->session->userdata('UserUID'),'ModifiedDateTime'=>date('Y-m-d h:i:s'),'ModifiedAmount'=>$tOrderPayments->CustomerAmount - $customer_pricing->DualClosingFee);

						$this->common_model->save('tOrderPayments', $reportdata, ['ScheduleUID'=>$ScheduleUID]);

					}
					else if ($tOrderPayments->ApprovalFunction == 'CustomerActualPricing'){

						$this->common_model->save('tOrders',  ['CustomerActualAmount' => $customer_pricing->Pricing], ['OrderUID'=>$OrderUID]);
						$reportdata = array('OrderUID' => $OrderUID,'CustomerActualAmount' => $customer_pricing->Pricing,'CustomerAdditionalAmount' => 0,'CustomerAmount' => $customer_pricing->Pricing,'ApprovalFunction' => 'CustomerActualPricing','ModifiedByUserUID' => $this->session->userdata('UserUID'),'ModifiedDateTime'=>date('Y-m-d h:i:s'),'ModifiedAmount'=>$tOrderPayments->CustomerAmount - $customer_pricing->Pricing);

						$this->common_model->save('tOrderPayments', $reportdata, ['ScheduleUID'=>$ScheduleUID]);

					}
				}

			}
		}

		$this->db->where(['OrderUID'=>$OrderUID, 'BeneficiaryType'=>'Customer']);
		$this->db->order_by('tOrderPayments.PaymentsUID', 'ASC');
		$customerpricing = $this->db->get('tOrderPayments')->result();
		$CustomerAmount = 0;
		foreach ($customerpricing as $key => $price) {
			$fees = [];
			if ($key == 0) {
				$CustomerAmount = $price->CustomerAmount; continue;
			}
			if ($price->OperatorType == '-') {
				$fees['CustomerActualAmount'] = $CustomerAmount;
				$fees['CustomerAmount'] = $CustomerAmount - $price->CustomerAdditionalAmount;
				$CustomerAmount = $CustomerAmount - $price->CustomerAdditionalAmount;
			}
			else{
				$fees['CustomerActualAmount'] = $CustomerAmount;
				$fees['CustomerAmount'] = $CustomerAmount + $price->CustomerAdditionalAmount;
				$CustomerAmount = $CustomerAmount + $price->CustomerAdditionalAmount;
			}
			if (!empty($fees) && !empty($price->PaymentsUID)) {
				$this->common_model->save('tOrderPayments', $fees, ['PaymentsUID'=>$price->PaymentsUID]);				
			}
		}


		$this->common_model->updatecustomerfee_payments($OrderUID);

	}


	function getRoleCategory($AbstractorUID)
	{
		$this->db->select('CASE WHEN IsNotary = 1 THEN "Notary" ELSE CASE WHEN IsAttorney = 1 THEN "Attorney" ELSE "Abstractor" END END AS Type', false);
		$this->db->from('mabstractor');
		$this->db->where('mabstractor.AbstractorUID', $AbstractorUID);
		return $this->db->get()->row()->Type;

	}

	function getActiveRoleCategory()
	{
	  $this->db->where('Active',1);
	  return $this->db->get('mRoleCategory')->result();
	}

	function getEmailTemplateOrderLevel()
	{
	  $this->db->where('Selecttype','Order');
      return $this->db->get('mEmailTemplate')->result();	
	}

	function gettTempClosingOrderDetailsForOrder($OrderUID)
	{
		$this->db->select('SigningAddress1 AS MailingAddress1,SigningAddress2 AS MailingAddress2,SigningZipCode AS MailingZipCode,SigningCityName AS MailingCityName,SigningCountyName AS MailingCountyName,SigningStateCode AS MailingStateCode, SigningAddressNotes AS MailingAddressNotes, SpecialInstruction As SpecialInstruction', false);
		$this->db->from('tOrderClosingTemp');
		$this->db->where('OrderUID', $OrderUID);
		$this->db->where('SigningAddress1 IS NOT NULL');
		$this->db->where('SigningZipCode IS NOT NULL');
		$this->db->where('SigningCityName IS NOT NULL');
		$this->db->where('SigningStateCode IS NOT NULL');

		$ClosingAddress = $this->db->get()->row();
		return $ClosingAddress;
	}


	function getCustomerSchedule($CustomerUID)
	{
		$this->db->select('mCustomerBranches.BranchName,mCustomerBranches.BranchUID,mStates.StateCode');
		$this->db->from('mCustomerBranches');
		$this->db->join('mStates','mStates.StateUID = mCustomerBranches.StateUID','left');
		// $this->db->where('mStates.StateCode',$stateCode->PropertyStateCode);
		$this->db->where('mCustomerBranches.CustomerUID',$CustomerUID);
		$this->db->where('mCustomerBranches.Active',1);
		return $this->db->get()->result();

	}

	
	function getClosingCurrentQueue($OrderUID)
	{

		$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		if (!empty($tOrders) && in_array($tOrders->StatusUID, [$this->config->item('keywords')['Order Completed']])  ) {
			return $this->config->item('Closign Current Queue')[16];	
		}
		if (!empty($tOrders) && in_array($tOrders->StatusUID, [$this->config->item('keywords')['Cancelled']])  ) {
			return $this->config->item('Closign Current Queue')[17];	
		}

		/* @desc Check Cancel Pending Request @author Yagavi G <yagavi.g@avanzegroup.com> @since Sept 3rd 2020 */
		$PendingCancelRequest = $this->CheckOrderCancelPendingRequest($OrderUID);
		if(!empty($PendingCancelRequest)){
			return $this->config->item('Closign Current Queue')[18];
		}

		/*Check is Schedule is completed*/
		$sql = "SELECT CASE WHEN EXISTS(
		SELECT OrderUID FROM torderassignment WHERE OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Scheduling']."' AND WorkflowStatus = 5)
		THEN 1 ELSE 0
		END AS ScheduleComplete,
		CASE WHEN EXISTS(
		SELECT tOrderschedule.OrderUID FROM tOrderschedule
		JOIN torderabstractor ON torderabstractor.AbstractorOrderUID = tOrderschedule.AbstractorOrderUID 
		JOIN tOrderscheduleBorrower ON tOrderscheduleBorrower.ScheduleUID=tOrderschedule.ScheduleUID
		WHERE tOrderschedule.OrderUID = $OrderUID AND tOrderschedule.ScheduleStatus NOT IN ('Cancel', 'Complete')) 
		THEN 1 ELSE 0 
		END AS SettlementAgentAssigned,
		CASE WHEN EXISTS(
		SELECT OrderUID FROM tOrdersign WHERE OrderUID = $OrderUID AND tOrdersign.SigningStatus IN ('Cancel')) 
		THEN 1 ELSE 0 
		END AS SigningCancelled,
		CASE WHEN EXISTS(
		SELECT OrderUID FROM tOrdersign WHERE OrderUID = $OrderUID AND tOrdersign.SigningStatus IN ('sign')) 
		THEN 1 ELSE 0 
		END AS SigningCompleted,
		CASE WHEN EXISTS(
		SELECT OrderUID FROM tOrdershipping WHERE OrderUID = $OrderUID AND tOrdershipping.IsShipped = 1) 
		THEN 1 ELSE 0 
		END AS ShipmentDone,
		CASE WHEN EXISTS( 
		SELECT OrderUID FROM torderassignment WHERE torderassignment.OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Signing']."' AND torderassignment.WorkflowStatus = 5)
		THEN 1 ELSE 0
		END AS SigningWorkflowCompleted,
		CASE WHEN 
			NOT EXISTS( SELECT OrderUID FROM torderassignment WHERE torderassignment.OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Signing']."')
		THEN 1 ELSE 0
		END AS Signing_NotAssigned,
		CASE WHEN EXISTS( SELECT OrderUID FROM torderassignment WHERE torderassignment.OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Signing']."' AND torderassignment.WorkflowStatus = 5)
		THEN 1 ELSE 0 
		END AS Signing_Completed,
		CASE WHEN EXISTS( SELECT OrderUID FROM torderassignment WHERE torderassignment.OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Shipping']."' )
		THEN 1 ELSE 0 
		END AS Shipment_of_title,
		CASE WHEN EXISTS( SELECT OrderUID FROM torderassignment WHERE torderassignment.OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Shipping']."' AND torderassignment.WorkflowStatus = 5)
		THEN 1 ELSE 0 
		END AS Shipment_complete";
		$result = $this->db->query($sql)->row();

		if ($result->SettlementAgentAssigned == 0 && $result->SigningCancelled == 1 && $result->SigningCompleted == 0 ) {
			return $this->config->item('Closign Current Queue')[5];	
		}
		if (($result->SettlementAgentAssigned == 0 && $result->SigningCompleted == 1) || $result->SigningWorkflowCompleted == 1 ) {
			return $this->config->item('Closign Current Queue')[1];	
		}

		
		if ($result->SettlementAgentAssigned == 1 ) {

			/*Individual Signing Status*/
			$this->db->select('*')->from('tOrdersign');
			$this->db->where(['OrderUID'=>$OrderUID]);
			$this->db->where_not_in('SigningStatus', ['Cancel', 'Sign']);
			$this->db->order_by('SignUID', 'DESC');
			$this->db->limit(1);
			$SigningDetails = $this->db->get()->result();
			$Currentworkflows = [];
			foreach ($SigningDetails as $key => $value) {
				
				if ((empty($value->IsDocstoLender) || $value->IsDocstoLender == '1') && $value->IsPostClosingComplete=='1' && $value->IsCriticalDocsBack=='1' && $value->IsSignConfirmDone=='1' && $value->Is2hoursCheck=='1' && $value->IsLogisticsCall=='1' && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[6]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && $value->IsCriticalDocsBack=='1' && $value->IsSignConfirmDone=='1' && $value->Is2hoursCheck=='1' && $value->IsLogisticsCall=='1' && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[7]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && $value->IsSignConfirmDone=='1' && $value->Is2hoursCheck=='1' && $value->IsLogisticsCall=='1' && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[8]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && $value->Is2hoursCheck=='1' && $value->IsLogisticsCall=='1' && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[9]->Queue;	

				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && empty($value->Is2hoursCheck) && $value->IsLogisticsCall=='1' && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[10]->Queue;	

				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && empty($value->Is2hoursCheck) && empty($value->IsLogisticsCall) && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[11]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && empty($value->Is2hoursCheck) && empty($value->IsLogisticsCall) && empty($value->IsDocsShipped) && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[12]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && empty($value->Is2hoursCheck) && empty($value->IsLogisticsCall) && empty($value->IsDocsShipped)&& empty($value->IsAssigned) && $value->IsPreClosingComplete=='1' ) {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[13]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && empty($value->Is2hoursCheck) && empty($value->IsLogisticsCall) && empty($value->IsDocsShipped) && empty($value->IsAssigned) && empty($value->IsPreClosingComplete)) {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[14]->Queue;	


				}


			}
			if (!empty($Currentworkflows)) {
				$Queue = implode(",", $Currentworkflows);
				return (object)['Queue'=>$Queue, 'color'=>'#428933'];				
			}
		}

		if ($result->SettlementAgentAssigned == 1) {
			return $this->config->item('Closign Current Queue')[4];
		}

		if (($result->Shipment_of_title == 1 && $result->Shipment_complete == 1) || $result->SigningWorkflowCompleted == 1 ) {
			return $this->config->item('Closign Current Queue')[2];	
		}

		if (($result->Shipment_of_title == 1) ) {
			return $this->config->item('Closign Current Queue')[15];	
		}

		$this->db->select('WorkflowModuleUID');
		$this->db->from('torderassignment');
		$this->db->where('OrderUID',$OrderUID);
		$this->db->where('WorkflowStatus',5);
		$this->db->order_by('WorkflowModuleUID','DESC');
		$query =$this->db->get()->row();

		if($query->WorkflowModuleUID == 9){
			return $this->config->item('Closign Current Queue')[3];	
		}
		else if($query->WorkflowModuleUID == 10){
			return $this->config->item('Closign Current Queue')[1];	
		}
		else if($query->WorkflowModuleUID == 11){
			return $this->config->item('Closign Current Queue')[2];	
		}

		if($result->SettlementAgentAssigned == 1){
			return $this->config->item('Closign Current Queue')[2];	
		}

		if($result->SettlementAgentAssigned == 1){		
			return $this->config->item('Closign Current Queue')[4];
		} 
      return $this->config->item('Closign Current Queue')[0];

	}
  //datatable search and orderby
  function datatable_countfilteredsearch_orderby($post)
  {
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

  }


  function GetNoteTypeUID($SectionName)
  {
    $this->db->select("*");
    $this->db->from('mreportsections');
    $this->db->where(array("mreportsections.SectionName"=>$SectionName));
    $query = $this->db->get();
    return $query->row();
  }

  function insertordernotes($OrderUID, $SectionUID, $Notes)
  {
  	$insert_notes = array(
  		'Note' => $Notes,
  		'SectionUID' => $SectionUID,
  		'OrderUID' => $OrderUID,
  		'RoleType' => '1,2,3,4,5,6,7,9,11,12',
  		'CreatedByUserUID' => $this->session->userdata('UserUID'),
  		'CreatedByAPI' => $this->session->userdata('UserUID'),
  		'CreatedOn' => date('Y-m-d H:i:s')
  	);       

  	$this->db->insert("tordernotes", $insert_notes);
  }

  function getLoggedDetails()
  {
  	$UserUID = $this->session->userdata('UserUID');
  	return $this->db->query("SELECT musers.VendorUID,musers.UserUID,musers.UserName,mroles.RoleName,mroles.RoleType FROM `mroles` JOIN musers on `musers`.`RoleUID` = `mroles`.`RoleUID`  WHERE `musers`.`UserUID` = '".$UserUID . "'")->row();
  }


  function getScheduleAddress($ScheduleUID)
  {
  	$this->db->select('CONCAT_WS(" ",SigningAddress1,SigningAddress2,SigningZipCode,SigningCityName,SigningCountyName,SigningStateCode) AS Mailingaddress', false);
  	$this->db->from('tOrderClosing');
  	$this->db->where('ScheduleUID', $ScheduleUID);
  	return $this->db->get()->row();
  }

  /*
 * Author: Ubakarasamy
 * Function: CheckAudit
 * Description: Cheack Previous Value and Current Value Same Or Not
 * And Insert Audit 
 */
function CheckAudit($PrimaryKey,$PrimaryKeyValue,$TableName,$FieldName,$CurrentValue){
	if($this->db->field_exists($FieldName, $TableName)){
		$this->db->select($FieldName);
		$this->db->from($TableName);
		$this->db->where($PrimaryKey,$PrimaryKeyValue);
		$query = $this->db->get();
		$Val = $query->row()->$FieldName;
		if(!empty($Val) && $Val != $CurrentValue){
			return $Val;
		}else if(!empty($Val) && $Val == $CurrentValue){
			return 'FALSE';
		}else if(empty($Val) && !empty($CurrentValue)){
			return '';
		}
	}else{
		return 'FALSE';
	}
  }

  function getStateRowbyUID($StateUID)
  {
  	return $this->db->where('StateUID', $StateUID)->get('mStates')->row();
  }


   function getCustomerRowbyUID($CustomerUID)
  {
  	return $this->db->where('CustomerUID', $CustomerUID)->get('mCustomers')->row();
  }

   function getCityRowbyUID($CityUID)
  {
  	return $this->db->where('CityUID', $CityUID)->get('mCities')->row();
  }
 
  function getCitiesRowbyStateUID($StateUID)
  {
  	return $this->db->where('StateUID', $StateUID)->get('mCities')->row();
  } 

 	function getOrderTypesbyUID($OrderTypeUID)
  {
  	return $this->db->where_in('OrderTypeUID', $OrderTypeUID)->get('mOrderTypes')->result();
  }

  function getCustomerbyUID($CustomerUID)
  {
  	$this->db->select('*');
  	$this->db->from('mCustomers');
  	$this->db->where_in('CustomerUID', $CustomerUID);
  	return $this->db->get()->result();
  }

  function get_countiesbycountyuids($CountyUIDs)
  {
  	$this->db->select('CountyUID,CountyName,mStates.StateUID,StateName,StateCode')->from('mCounties');
  	$this->db->join('mStates','mStates.StateUID = mCounties.StateUID');
  	$this->db->where_in('mCounties.CountyUID', $CountyUIDs);
  	$this->db->group_by('mCounties.CountyUID');
  	return $this->db->get()->result();
  }

  function get_countiesbyzipcodes($ZipCodes)
  {
  	$this->db->distinct('CountyUID')->from('mCities');
  	$this->db->where_in('mCities.ZipCode', $ZipCodes);
  	return $this->db->get()->result();
  }


  function get_countiesbycityuids($CityUIDs)
  {
  	$this->db->distinct('CountyUID')->from('mCities');
  	$this->db->where_in('mCities.ZipCode', $CityUIDs);
  	return $this->db->get()->result();
  }


  function get_citiesbyzipcodes($ZipCodes)
  {
  	$this->db->select('CityUID,CityName,mStates.StateUID,StateName,StateCode,ZipCode,mCities.CountyUID,mCounties.CountyName')->from('mCities');
  	$this->db->join('mStates','mStates.StateUID = mCities.StateUID');
  	$this->db->join('mCounties','mCounties.countyUID = mCities.countyUID','LEFT');
  	$this->db->where_in('mCities.ZipCode', $ZipCodes);
  	$this->db->group_by('mCities.CityUID');
  	return $this->db->get()->result();
  }

  function get_citiesbycityuids($CityUIDs)
  {
  	$this->db->select('CityUID,CityName,mStates.StateUID,StateName,StateCode,ZipCode,mCities.CountyUID,mCounties.CountyName')->from('mCities');
  	$this->db->join('mStates','mStates.StateUID = mCities.StateUID');
  	$this->db->join('mCounties','mCounties.countyUID = mCities.countyUID','LEFT');
  	$this->db->where_in('mCities.CityUID', $CityUIDs);
  	return $this->db->get()->result();
  }

  function getCitiesbyStateUID($StateUID)
  {
  	return $this->db->where('StateUID', $StateUID)->get('mCities')->result();
  } 

  function getDistinctCitiesbyStateUID($StateUID)
  {
  	return $this->db->where('StateUID', $StateUID)->get('mCities')->result();
  } 
  /**
	  * @description Insert Audit Log
	  * @param Feature: (EG: VendorUID) , Content: Audit String 
	  * @throws no exception
	  * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
	  * @return nothing 
	  * @since  
	  * @version  
	  */ 

	function InsertAuditLog($UID,$STR,$ModuleName){
		$InsetData = array(
			'UserUID' => $this->loggedid,
			'ModuleName' => $ModuleName,
			'Feature' => $UID,
			'Content' => htmlentities($STR),
			'DateTime' => date('Y-m-d H:i:s'));
			$this->common_model->InsertAuditTrail($InsetData);
	}
	
 	/**
	*@description Function to get abstractor individual contact details
	*
	* @param int AbstractorUID
	* 
	* @throws no exception
	* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	* @return nothing 
	* @since 30 december 2019 
	* @version  D-1-T278
	*
	*/ 
	function get_allindividualcontactdetails_abstractor($AbstractorUID)
	{
		$CASEWHEN = [];
		$DesignationUID = $this->db->query("SELECT DesignationUID FROM mDesignation WHERE DesignationName LIKE 'Vendor'")->row()->DesignationUID; //@Desc Get DesignationUID for Vendor @Author Jainulabdeen @Updated June 19 2020
		$this->db->select('mabstractorcontact.AbstractorContactUID');
		$this->db->select('CASE WHEN mabstractorcontact.ContactName != "" THEN mabstractorcontact.ContactName ELSE mabstractorcontact.Email END AS ContactName', false);
		$this->db->from('mabstractorcontact');
		$this->db->where('mabstractorcontact.Status !=', 'Prohibited');
		$this->db->where('mabstractorcontact.DesignationUID', $DesignationUID); //@Desc Get Contact for Vendor only @Author Jainulabdeen @Updated June 19 2020
		$this->db->where('mabstractorcontact.AbstractorUID',$AbstractorUID);
		/*@Desc contact name or email not empty means show @Author Jainulabdeen @Updated on June 12 2020*/
		$CASEWHEN[] =  " mabstractorcontact.ContactName != '' ";
		$CASEWHEN[] =  " mabstractorcontact.Email != '' ";
		$WHERE = " ( " . implode(" OR ", $CASEWHEN) . " ) ";
		$this->db->where($WHERE, NULL, FALSE);
		$this->db->order_by('mabstractorcontact.ContactName', 'ASC');
		/*End*/
		return $this->db->get()->result();
	}

	/**
	* @description Get Users Signature
	* @param UserUID
	* @throws no exception
	* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
	* @return Users Signature text and File 
	* @since  2/1/2020
	* @version  
		*/ 

	function GetUserSignature($UserUID){
		$this->db->select('EmailSignatureText,EmailSignatureFile,DrawnEmailSignature');
		$this->db->from('musers');
		$this->db->where('UserUID',$UserUID);
		return $this->db->get()->row();
	}
	


	/**
	* Function - Customer Pricing validation
	*
	* @param int OrderUID required
	* 
	* @throws exception OrderUID is not valid or present
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return json response valid or invalid 
	* @since 04 jan 2020 
	* @version DI1-T82 Customer/Vendor Fee & Pricing validation
	*
	*/ 
	function process_customerpricing_validation($OrderUID)
	{
		//bypass validation
		return true;
		if(!empty($OrderUID))
		{
			$validrow = $this->db->query("SELECT EXISTS(SELECT 1 FROM tOrders WHERE OrderUID = {$OrderUID} AND 
				COALESCE(CustomerAmount,0) = (
				SELECT  COALESCE(SUM(CASE WHEN  ApprovalFunction = 'CustomerActualPricing'  THEN CustomerActualAmount  END) + SUM(CASE WHEN  ApprovalFunction != 'CustomerActualPricing'  THEN CustomerAdditionalAmount END) ,0)
				FROM tOrderPayments WHERE BeneficiaryType='Customer' AND  OrderUID = {$OrderUID}
				)
				) AS valid 
				")->row()->valid;
			
			//check customer pricing is same
			if($validrow)
			{
				//if valid return true
				return true;
			}
		}

		//not valid return false
		return false;
	}

	/**
	* Function - Vendor Fee validation
	*
	* @param int OrderUID required
	* 
	* @throws exception OrderUID is not valid or present
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return json response valid or invalid 
	* @since 04 jan 2020 
	* @version DI1-T82 Customer/Vendor Fee & Pricing validation
	*
	*/ 
	function process_vendorfee_validation($OrderUID)
	{
		//bypass validation
		return true;
		if(!empty($OrderUID))
		{

			$validrow = $this->db->query("SELECT EXISTS( SELECT COALESCE(AbstractorFee,0) FROM tOrders WHERE OrderUID = {$OrderUID} AND COALESCE(AbstractorFee,0) = ( 
				SELECT COALESCE(SUM(CASE WHEN  ApprovalFunction = 'AbstractorActualPricing'  AND (IsAbstractorCancelled = 0 OR (IsAbstractorCancelled = 1 AND torderabstractorunassign.IsFeeAdjusted = 1))
				THEN tOrderPayments.AbstractorActualFee END),0) + COALESCE(SUM(CASE WHEN  ApprovalFunction != 'AbstractorActualPricing' THEN tOrderPayments.AbstractorCopyCost END),0) + COALESCE(SUM(CASE WHEN ApprovalFunction != 'AbstractorActualPricing' THEN tOrderPayments.AbstractorAdditionalFee END),0)  FROM tOrderPayments LEFT JOIN torderabstractor ON torderabstractor.AbstractorOrderUID = tOrderPayments.AbstractorOrderUID AND tOrderPayments.IsAbstractorCancelled = 0  LEFT JOIN torderabstractorunassign ON torderabstractorunassign.AbstractorOrderUID = tOrderPayments.AbstractorOrderUID AND tOrderPayments.IsAbstractorCancelled = 1 AND IsFeeAdjusted = 1 WHERE BeneficiaryType = 'Abstractor'  AND tOrderPayments.OrderUID = {$OrderUID} 
				) ) AS valid
				")->row()->valid;


			if($validrow)
			{
				//if valid return true
				return true;
			}

		}

		//not valid return false
		return false;
	}

	/**
		*@description Function to getall OrderDocuments by DocumentFileName
		*
		* @param OrderUID(int), FileName(Array)
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return ObjectArray 
		* @since 6/1/2020 
		* @version Vendor Management 
		*
	*/ 
	function getallOrderDocumentsByFileName($OrderUID, $FileNames){

		if (empty($FileNames)) {
			return [];
		}
		$this->db->select('tOrders.OrderDocsPath, torderdocuments.DocumentFileName, torderdocuments.DisplayFileName');
		$this->db->from('torderdocuments');
		$this->db->join('tOrders', 'torderdocuments.OrderUID = tOrders.OrderUID');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->where_in('torderdocuments.DocumentFileName', $FileNames);
		return $this->db->get()->result();
	}

	/**
	*@description function to check individual contact enabled for customer in subproduct level
	*
	* @param int OrderUID
	* 
	* @throws no exception
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return nothing 
	* @since 07 jan 2020 
	* @version  D-1-T278
	*
	*/ 
  function get_customerindividualcontactneeded($OrderUID)
  {
  	//VendorIndividualContact
		$query = $this->db->query("SELECT EXISTS (SELECT * FROM (`tOrders`) JOIN `mCustomerProducts` ON `tOrders`.`CustomerUID` = `mCustomerProducts`.`CustomerUID` AND tOrders.SubProductUID = mCustomerProducts.SubProductUID WHERE `tOrders`.`OrderUID` = '{$OrderUID}' AND `VendorIndividualContact` = 1) AS needed ");
		return $query->row()->needed;
  }

  function check_individualcontactneeded_tabstractor($OrderUID)
  {
  	$query = $this->db->query("SELECT EXISTS (SELECT * FROM (`torderabstractor`) JOIN `mabstractor` ON `mabstractor`.`AbstractorUID` = `torderabstractor`.`AbstractorUID`  WHERE `torderabstractor`.`OrderUID` = '{$OrderUID}' AND `IndividualContact` = 1 AND ContactUID = '') as needed");
  	return $query->row()->needed;
  }

  function get_individualcontactneeded_tabstractor($OrderUID)
  {
  	//VendorIndividualContact
  	$query = $this->db->query("SELECT ContactUID,torderabstractor.AbstractorUID,AbstractorFirstName,AbstractorOrderUID FROM (`torderabstractor`) JOIN `mabstractor` ON `mabstractor`.`AbstractorUID` = `torderabstractor`.`AbstractorUID`  WHERE `torderabstractor`.`OrderUID` = '{$OrderUID}' AND `IndividualContact` = 1 ");
  	$assignedvendors =  $query->result();

  	$html = '<div class="row">';

  	$success = FALSE;
  	foreach ($assignedvendors as $key => $assignedvendor) 
  	{
  		if(empty($assignedvendor->ContactUID))
  		{
  			/*@Desc Get Borrower Name @Author Jainulabdeen @Updated June 19 2020*/
  			$query1 = $this->db->query("SELECT tOrderPropertyRoles.PRName FROM (`tOrderschedule`) JOIN `tOrderscheduleBorrower` ON `tOrderschedule`.`ScheduleUID` = `tOrderscheduleBorrower`.`ScheduleUID` JOIN `tOrderPropertyRoles` ON `tOrderPropertyRoles`.`Id` = `tOrderscheduleBorrower`.`BorrowerUID` WHERE `tOrderschedule`.`OrderUID` = '{$OrderUID}' AND `tOrderschedule`.`AbstractorUID` = '{$assignedvendor->AbstractorUID}' AND `tOrderschedule`.`ScheduleStatus` != 'Cancel' ");
  			$BorrowerName =  $query1->result();
  			/*End*/
  			$success = TRUE;
  			$individualcontactdetails = $this->get_allindividualcontactdetails_abstractor($assignedvendor->AbstractorUID);
  			 

  			$html .= '<div class="col-sm-12"><div class="panel-heading" style="margin: 0;padding-top: 0px;padding-bottom: 5px;border-bottom: 1px solid #eee;"><span style="font-size: 11px;"><strong>Individual Contact - '.$assignedvendor->AbstractorFirstName.' - '.$BorrowerName[0]->PRName.'</strong></span></div>
  			<button class="btn btn-sm btn-space btn-social btn-color btn-success AddNewContact pull-right" style="text-align: right;" data-AbstractorOrderUID='.$assignedvendor->AbstractorOrderUID.' data-AbstractorUID='.$assignedvendor->AbstractorUID.'>Create New Contact ?</button>
  			<div class="vendormultiple-individualcontactdiv"><div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is_error"><select class="mdl-textfield__input input-xs select2vendorcontact mdl-select2 vendormultiple-individualcontact" name="vendormultiple-individualcontact" data-AbstractorOrderUID='.$assignedvendor->AbstractorOrderUID.'>
  			<option value=""></option>';
  			foreach ($individualcontactdetails as $key => $individualcontactdetail) 
  			{
  				
  				$html .= '<option value="'.$individualcontactdetail->AbstractorContactUID.'" '.(($individualcontactdetail->AbstractorContactUID == $assignedvendor->ContactUID) ? "selected" : "").'>'.$individualcontactdetail->ContactName.'</option>';
  				
  			}
  			$html .=' </select> <label class="mdl-textfield__label" for="vendorindividualcontact">Select Individual Contact<span style="color: red"> *</span></label> <span class="mdl-textfield__error form_error"></span> </div> </div></div>';
  		}
  	}

  	$html .= '</div>';
  	return array('success'=>$success,'html'=>$html);


  }


	/**
		*@description Function to check is billing trigger workflow
		*
		* @param $OrderUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return bool 
		* @since 7/1/2020 
		* @version Vendor Mgmt 
		*
	*/ 
	function isBillingTriggerEnable($OrderUID, $WorkflowModuleUID){

		$order_details = $this->Common_model->get_orderdetails($OrderUID);

		$billingtriggerenabled = $this->get_subproductbillingtrigger_workflowuids($order_details, $order_details->CustomerUID,$order_details->ProductUID,$order_details->SubProductUID, $WorkflowModuleUID);
		$vendorbillingtriggerenabled = $this->Common_model->getBillingTriggeredVendorOrders($OrderUID,$WorkflowModuleUID);
		if ( !empty($billingtriggerenabled) || !empty($vendorbillingtriggerenabled) ) 
		{
			return true;
		}
		return false;
	}


	/**
		*@description Function to return order Quote Fees Enabled or Not
		*
		* @param $OrderUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return bool 
		* @since 7/1/2020 
		* @version Vendor Mgmt 
		*
	*/ 
	function isQuoteFeeEnabled($OrderUID){
		/*Query*/

		/*Client Quote*/
		$this->db->select('1', false);
		$this->db->from('tOrders'); 
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->where('tOrders.IsQuote', 1);
		$tOrders = $this->db->get()->result();

		$this->db->select('1', false);
		$this->db->from('torderabstractor'); 
		$this->db->where('torderabstractor.OrderUID', $OrderUID);
		$this->db->where('torderabstractor.IsQuote', 1);
		$activeabstractors = $this->db->get()->result();

		$this->db->select('1', false);
		$this->db->from('torderabstractorunassign'); 
		$this->db->where('torderabstractorunassign.OrderUID', $OrderUID);
		$this->db->where('torderabstractorunassign.IsQuote', 1);
		$inactiveabstractors = $this->db->get()->result();

		return count(array_merge($activeabstractors, $inactiveabstractors, $tOrders)) > 0 ? true : false;
	}

	/**
		*@description Function to return order Quote Fees Enabled or Not
		*
		* @param $OrderUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return bool 
		* @since 7/1/2020 
		* @version Vendor Mgmt 
		*
	*/ 
	function isVendorQuoteFeeEnabled($OrderUID){
		/*Query*/

		$this->db->select('1', false);
		$this->db->from('torderabstractor'); 
		$this->db->where('torderabstractor.OrderUID', $OrderUID);
		$this->db->where('torderabstractor.IsQuote', 1);
		$activeabstractors = $this->db->get()->result();

		/*$this->db->select('1', false);
		$this->db->from('torderabstractorunassign'); 
		$this->db->where('torderabstractorunassign.OrderUID', $OrderUID);
		$this->db->where('torderabstractorunassign.IsQuote', 1);
		$inactiveabstractors = $this->db->get()->result();

		return count(array_merge($activeabstractors, $inactiveabstractors)) > 0 ? true : false;*/ //@Desc After cancel vendor should not valid as quote @Auth Jainulabdeen @Updated May 23 2020
		return count($activeabstractors) > 0 ? true : false;
	}

	/**
		*@description Function to return client order Quote Fees Enabled or Not
		*
		* @param $OrderUID
		* 
		* @throws no exception
		* @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
		* @return bool 
		* @since 5/21/2020 
		* @version Client Mgmt 
		*
	*/ 
	function isQuoteFeeEnabledClient($OrderUID){
		/*Query*/
		$this->db->select('1', false);
		$this->db->from('tOrders'); 
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->where('tOrders.IsQuote', 1);
		$active = $this->db->get()->result();

		return count($active) > 0 ? true : false;
	}



	/**
	* @description function to fetch billing trigger
	*
	* @param int CustomerUID, ProductUID and SubProductUID
	* 
	* @throws no exception
	* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	* @return nothing 
	* @since  7/1/2020 
	* @version DI1-T56 Customer Billing Triggers
	*
	*/

	function get_subproductbillingtrigger_workflowuids($OrderDetails, $CustomerUID,$ProductUID,$SubProductUID, $WorkflowModuleUIDs)
	{


		$State=$this->common_model->GetStatebyCode($OrderDetails->PropertyStateCode);
		$StateUID = '';
		$CountyUID = '';
		$CityUID = [];
		$ZipCode = $OrderDetails->PropertyZipcode;

		if (!empty($State) && !empty($State->StateUID)) {
			$StateUID = $State->StateUID;    
		}

		$CountyUID = $this->common_model->getCountyUIDByStateUIDAndCountyName($StateUID, $OrderDetails->PropertyCountyName)->CountyUID;
		$CityUID = array_column($this->common_model->getCityUIDByStateUIDAndCountyUIDAndCityName($StateUID, $CountyUID, $OrderDetails->PropertyCityName), "CityUID");
		$CityUID[] = 0;
		$CityUID = implode(",", $CityUID);


		$CASEWHEN = [];
		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND   mCustomerBillingTriggers.ProductUID = '".$ProductUID."' AND mCustomerBillingTriggers.SubProductUID = '".$SubProductUID."' AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND mCustomerBillingTriggers.CountyUID = '".$CountyUID."' AND mCustomerBillingTriggers.ZipCode = '".$ZipCode."' AND mCustomerBillingTriggers.CityUID IN (".$CityUID.") ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND  mCustomerBillingTriggers.ProductUID = '".$ProductUID."' AND mCustomerBillingTriggers.SubProductUID = '".$SubProductUID."' AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND mCustomerBillingTriggers.CountyUID = '".$CountyUID."' AND mCustomerBillingTriggers.ZipCode = '".$ZipCode."' AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND  mCustomerBillingTriggers.ProductUID = '".$ProductUID."' AND mCustomerBillingTriggers.SubProductUID = '".$SubProductUID."' AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND mCustomerBillingTriggers.CountyUID = '".$CountyUID."' AND ( mCustomerBillingTriggers.ZipCode = '0' OR mCustomerBillingTriggers.ZipCode IS NULL ) AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND  mCustomerBillingTriggers.ProductUID = '".$ProductUID."' AND mCustomerBillingTriggers.SubProductUID = '".$SubProductUID."' AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND ( mCustomerBillingTriggers.CountyUID = '0' OR mCustomerBillingTriggers.CountyUID IS NULL ) AND ( mCustomerBillingTriggers.ZipCode = '0' OR mCustomerBillingTriggers.ZipCode IS NULL ) AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND  mCustomerBillingTriggers.ProductUID = '".$ProductUID."' AND mCustomerBillingTriggers.SubProductUID = '".$SubProductUID."' AND ( mCustomerBillingTriggers.StateUID = '0' OR mCustomerBillingTriggers.StateUID IS NULL ) AND ( mCustomerBillingTriggers.CountyUID = '0' OR mCustomerBillingTriggers.CountyUID IS NULL ) AND ( mCustomerBillingTriggers.ZipCode = '0' OR mCustomerBillingTriggers.ZipCode IS NULL ) AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND  mCustomerBillingTriggers.ProductUID = '".$ProductUID."' AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND ( mCustomerBillingTriggers.StateUID = '0' OR mCustomerBillingTriggers.StateUID IS NULL ) AND ( mCustomerBillingTriggers.CountyUID = '0' OR mCustomerBillingTriggers.CountyUID IS NULL ) AND ( mCustomerBillingTriggers.ZipCode = '0' OR mCustomerBillingTriggers.ZipCode IS NULL ) AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND  ( mCustomerBillingTriggers.ProductUID = '0' OR mCustomerBillingTriggers.ProductUID IS NULL ) AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND ( mCustomerBillingTriggers.StateUID = '0' OR mCustomerBillingTriggers.StateUID IS NULL ) AND ( mCustomerBillingTriggers.CountyUID = '0' OR mCustomerBillingTriggers.CountyUID IS NULL ) AND ( mCustomerBillingTriggers.ZipCode = '0' OR mCustomerBillingTriggers.ZipCode IS NULL ) AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";


		/* **** SubProduct Null **** */
		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND   mCustomerBillingTriggers.ProductUID = '".$ProductUID."' AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND mCustomerBillingTriggers.CountyUID = '".$CountyUID."' AND mCustomerBillingTriggers.ZipCode = '".$ZipCode."' AND mCustomerBillingTriggers.CityUID IN (".$CityUID.") ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND  mCustomerBillingTriggers.ProductUID = '".$ProductUID."' AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND mCustomerBillingTriggers.CountyUID = '".$CountyUID."' AND mCustomerBillingTriggers.ZipCode = '".$ZipCode."' AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND  mCustomerBillingTriggers.ProductUID = '".$ProductUID."' AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND mCustomerBillingTriggers.CountyUID = '".$CountyUID."' AND ( mCustomerBillingTriggers.ZipCode = '0' OR mCustomerBillingTriggers.ZipCode IS NULL ) AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND  mCustomerBillingTriggers.ProductUID = '".$ProductUID."' AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND ( mCustomerBillingTriggers.CountyUID = '0' OR mCustomerBillingTriggers.CountyUID IS NULL ) AND ( mCustomerBillingTriggers.ZipCode = '0' OR mCustomerBillingTriggers.ZipCode IS NULL ) AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND  mCustomerBillingTriggers.ProductUID = '".$ProductUID."' AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND ( mCustomerBillingTriggers.StateUID = '0' OR mCustomerBillingTriggers.StateUID IS NULL ) AND ( mCustomerBillingTriggers.CountyUID = '0' OR mCustomerBillingTriggers.CountyUID IS NULL ) AND ( mCustomerBillingTriggers.ZipCode = '0' OR mCustomerBillingTriggers.ZipCode IS NULL ) AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";


		/* **** Product Null **** */
		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND  ( mCustomerBillingTriggers.ProductUID = '0' OR mCustomerBillingTriggers.ProductUID IS NULL ) AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND mCustomerBillingTriggers.CountyUID = '".$CountyUID."' AND mCustomerBillingTriggers.ZipCode = '".$ZipCode."' AND mCustomerBillingTriggers.CityUID IN (".$CityUID.") ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND ( mCustomerBillingTriggers.ProductUID = '0' OR mCustomerBillingTriggers.ProductUID IS NULL ) AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND mCustomerBillingTriggers.CountyUID = '".$CountyUID."' AND mCustomerBillingTriggers.ZipCode = '".$ZipCode."' AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND ( mCustomerBillingTriggers.ProductUID = '0' OR mCustomerBillingTriggers.ProductUID IS NULL ) AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND mCustomerBillingTriggers.CountyUID = '".$CountyUID."' AND ( mCustomerBillingTriggers.ZipCode = '0' OR mCustomerBillingTriggers.ZipCode IS NULL ) AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND ( mCustomerBillingTriggers.ProductUID = '0' OR mCustomerBillingTriggers.ProductUID IS NULL ) AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND mCustomerBillingTriggers.StateUID = '".$StateUID."' AND ( mCustomerBillingTriggers.CountyUID = '0' OR mCustomerBillingTriggers.CountyUID IS NULL ) AND ( mCustomerBillingTriggers.ZipCode = '0' OR mCustomerBillingTriggers.ZipCode IS NULL ) AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$CASEWHEN[] = "( CASE WHEN ( mCustomerBillingTriggers.CustomerUID = '".$CustomerUID."' AND mCustomerBillingTriggers.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND ( mCustomerBillingTriggers.ProductUID = '0' OR mCustomerBillingTriggers.ProductUID IS NULL ) AND ( mCustomerBillingTriggers.SubProductUID = '0' OR mCustomerBillingTriggers.SubProductUID IS NULL ) AND ( mCustomerBillingTriggers.StateUID = '0' OR mCustomerBillingTriggers.StateUID IS NULL ) AND ( mCustomerBillingTriggers.CountyUID = '0' OR mCustomerBillingTriggers.CountyUID IS NULL ) AND ( mCustomerBillingTriggers.ZipCode = '0' OR mCustomerBillingTriggers.ZipCode IS NULL ) AND ( mCustomerBillingTriggers.CityUID = '0' OR mCustomerBillingTriggers.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		$WHERE = " ( " . implode(" OR ", $CASEWHEN) . " ) ";

		$this->db->select('Distinct mCustomerBillingTriggers.BillingPeriod, mCustomerBillingTriggers.CustomerUID', false);
		$this->db->from('mCustomerBillingTriggers'); 
		$this->db->where($WHERE, NULL, FALSE);
		return $this->db->get()->result_array();
	}

	function getAPiAuthKey()
	{
		return $this->db->select('*')->from('morganizations')->get()->row();
	}


	/**
	*@description function to fetch ordertypedetails by name
	*
	* @param string ordertypename
	* 
	* @throws no exception
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return nothing 
	* @since 09 jan 2020 
	* @version  fetch ordertypebyname
	*
	*/ 

	function get_ordertyperow_byname($OrderTypeName,$post = NULL)
	{
		$this->db->select('*');
		$this->db->from ('mOrderTypes');
		$this->db->where('OrderTypeName', $OrderTypeName);
		if($post)
		{
			if($post['RoleCategory'] == "Abstractor")
			{
				$RoleCategoryUID = 1;
			}elseif ($post['RoleCategory'] == "Attorney") {
				$RoleCategoryUID = 3;
			}else {
				$RoleCategoryUID = 2;
			}	
		}
		if(isset($post['RoleCategoryUID']) && !empty($post['RoleCategoryUID']))
		{	
			$orvalue = is_array($post['RoleCategoryUID']) ? "OR FIND_IN_SET(" . implode( ",RoleCategoryUID) OR FIND_IN_SET(", $post['RoleCategoryUID']) . ",RoleCategoryUID)" : " OR FIND_IN_SET({$post['RoleCategoryUID']},RoleCategoryUID)";

			$this->db->where("(RoleCategoryUID='' OR RoleCategoryUID IS NULL ".$orvalue.' )');
		}
		
		if(isset($post['RoleCategory']) && !empty($post['RoleCategory']))
		{
			$this->db->where("(RoleCategoryUID='' OR RoleCategoryUID IS NULL OR RoleCategoryUID like '%".$RoleCategoryUID."%')");
		}
		$query = $this->db->get();
		return $query->row();
	}

/**
	* @description function to fetch all status 
	*
	* @param 
	* 
	* @throws no exception
	* @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
	* @return all status 
	* @since  9/1/2020 
	* @version 
	*
	*/
	
	function GetAllStatus(){
		$query = $this->db->get('mOrderStatus');
		return $query->result();
	}

	function GetAllWorkflow(){
		$query = $this->db->get('mworkflowmodules');
		return $query->result();
	}

	/**
	* Function -  fetch vendor counties by zipcode
	*
	* @param (string) zipcode and (int)stateuid 
	* 
	* @throws exception snippet is not valid or present
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return json response valid or invalid 
	* @since date 11 Jan 2020 
	* @version DI1-T109 -Vendor Fee Zipcode Modifications
	*
	*/ 

	function get_countyby_statecode_zipcode($stateuid,$zipcode) {
		$this->db->select('mCounties.CountyUID, mCities.CityUID');
		$this->db->from ( 'mCounties' );
		$this->db->join ( 'mCities', 'mCities.CountyUID = mCounties.CountyUID','INNER');
		$this->db->where_in('mCities.StateUID',$stateuid);
		$this->db->where_in('mCities.ZipCode',$zipcode);
		$query = $this->db->get();
		return $query->result();
	}


	function get_countyby_statecode_cityuid($stateuid,$cityuid) {

		$this->db->select('mCounties.CountyUID, mCities.CityUID');
		$this->db->from ( 'mCounties' );
		$this->db->join ( 'mCities', 'mCities.CountyUID = mCounties.CountyUID','INNER');
		$this->db->where_in('mCities.StateUID',$stateuid);
		$this->db->where_in('mCities.CityUID',$cityuid);
		$query = $this->db->get();
		return $query->result();
	}


	function get_countyby_cityuid($cityuid) {

		$this->db->select('mCities.CountyUID');
		$this->db->from ( 'mCities' );
		$this->db->where_in('mCities.CityUID',$cityuid);
		$query = $this->db->get();
		return $query->result();
	}
	

	/**
	* Function -  fetch vendor zipcode by county
	*
	* @param stateuid(int) and countyuid(int)
	* 
	* @throws exception snippet is not valid or present
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return json response valid or invalid 
	* @since date 11 Jan 2020 
	* @version DI1-T109 - Vendor Fee Zipcode Modifications
	*
	*/ 

	function get_zipcodeby_state_county($stateuid,$countyuid) {

		$this->db->select('mCities.ZipCode, mCities.CityUID, mCities.CityName');
		$this->db->from ( 'mCities' );
		$this->db->where_in('mCities.StateUID',$stateuid);
		if($countyuid) {
			$this->db->where_in('mCities.CountyUID',$countyuid);
		}
		$query = $this->db->get();
		return $query->result();
	}


	function get_zipcodeby_state_county_city($stateuid,$countyuid,$cityuid) {

		$this->db->select('mCities.ZipCode');
		$this->db->from ( 'mCities' );
		$this->db->where_in('mCities.StateUID',$stateuid);
		if($countyuid) {
			$this->db->where_in('mCities.CountyUID',$countyuid);
		}
		if($cityuid) {
			$this->db->where_in('mCities.CityUID',$cityuid);
		}
		$query = $this->db->get();
		return $query->result();
	}


	function get_cityby_state_county($stateuid,$countyuid) {

		$this->db->select('mCities.CityUID,mCities.CityName');
		$this->db->from ( 'mCities' );
		$this->db->where_in('mCities.StateUID',$stateuid);
		if($countyuid) {
			$this->db->where_in('mCities.CountyUID',$countyuid);
		}
		$query = $this->db->get();
		return $query->result();
	}

	/**
	* Function -  fetch vendor cities by zipcode & county
	*
	* @param  stateuid(int),countyuid(int) and zipcode(string)
	* 
	* @throws exception snippet is not valid or present
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return json response valid or invalid 
	* @since date 13 Jan 2020 
	* @version  DI1-T109 - Vendor Fee Zipcode Modifications
	*
	*/ 

	function isvalid_cities_by_county_zipcode($stateuid,$countyuid,$zipcode) {

		$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM (`mCities`) WHERE `mCities`.`StateUID` = {$stateuid} AND `mCities`.`countyuid` = {$countyuid} AND `mCities`.`zipcode` = {$zipcode}) AS available");
		return $query->row()->available;
	}

/**
	*@description Function to get AutoCreation Task on Workflow Lvl
	*
	* @param $UserTaskUID
	* 
	* @throws no exception
	* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	* @return Array 
	* @since Date 
	* @version Task AutoCompletion 
	*
*/ 
	function getAutoCompletionTask_WorkflowLevel($OrderUID, $WorkflowModuleUID, $TaskAction){

		$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$OrderUID]);

		if (!empty($tOrders) && !empty($tOrders->SubProductUID)) {

			/*Query*/
			$this->db->select('*');
			$this->db->from('tCustomerSubProductTasks'); 
			$this->db->join('mTask', 'tCustomerSubProductTasks.TaskUID = mTask.TaskUID');
			$this->db->where('tCustomerSubProductTasks.SubProductUID', $tOrders->SubProductUID);
			if (!empty($WorkflowModuleUID)) {
				$this->db->where('tCustomerSubProductTasks.WorkflowModuleUID', $WorkflowModuleUID);				
			}
			$this->db->where('tCustomerSubProductTasks.Action', $TaskAction);
			$this->db->where('tCustomerSubProductTasks.AutoCreation', 1);
			return $this->db->get()->result();

		}
		return [];
	}

/**
	*@description Function to Create AutoCreation Tasks on worflow Lvl
	*
	* @param $UserTaskUID
	* 
	* @throws no exception
	* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	* @return bool 
	* @since 13/1/2020 
	* @version Task AutoCreation 
	*
*/ 
	function AutoCreateTask_WorkflowLevel($OrderUID, $WorkflowModuleUID, $Action){
		$TaskStatus_Array = $this->config->item('TaskStatus');
		$TaskAction = $TaskStatus_Array[$Action];


		$AutoCreationTasks= $this->common_model->getAutoCompletionTask_WorkflowLevel($OrderUID, $WorkflowModuleUID, $TaskAction);

		foreach ($AutoCreationTasks as $key => $value) {
			$mTask = $this->common_model->get_row('mTask', ['TaskUID'=>$value->TaskUID]);

			$data['OrderUID'] = $OrderUID;
			$data['TaskStatus'] = 'Open';
			$data['TaskID'] = $value->TaskUID;
			$data['Description'] = $value->Description;
			$data['AddedDateTime'] = date('Y-m-d H:i:s');
			$data['ModifiedDateTime'] = date('Y-m-d H:i:s');
			$data['AssignedTo'] = $this->loggedid;
			$data['AddedByUserUID'] = $this->loggedid;
			$data['AdditionalAuditLog'] = ' Auto Created By Task <b>' . $mTask->ShortDescription . '</b> while status changed to <b>' . $Action . '</b>';


			$this->common_model->save_task($data);
		}

	}




/**
	*@description Function to Create Tasks on worflow Lvl
	*
	* @param Array of tUserTask
	* 
	* @throws no exception
	* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
	* @return bool 
	* @since 13/1/2020 
	* @version Task AutoCreation 
	*
*/ 
	function save_task($data){

		$COMPLETEDDate = "";
		if($data['TaskStatus'] == 'Completed'){
          $COMPLETEDDate = date('Y-m-d H:i:s');
          $CompletedUserID = $data['AssignedTo'];
		}
		$AddedDateTime = ($data['AddedDateTime'] == NULL) ? $data['AddedDateTime'] : Date('Y-m-d h:i:s',strtotime($data['AddedDateTime']));
		/*$AssignedDateTime = ($data['AssignedDateTime'] == NULL) ? $data['AssignedDateTime'] : Date('Y-m-d h:i:s',strtotime($data['AssignedDateTime']));*/
		$ModifiedDateTime = ($data['ModifiedDateTime'] == NULL) ? $data['ModifiedDateTime'] : Date('Y-m-d h:i:s',strtotime($data['ModifiedDateTime']));

		$fieldArray = array(
			"TaskID" => $data['TaskID'],
			"OrderUID" => $data['OrderUID'],
			"TaskStatus" => $data['TaskStatus'],
			"Description" => $data['Description'],
			"AssignedTo" => $data['AssignedTo'],
			"AddedDateTime" => $AddedDateTime,
			"AddedByUserUID" => $data['AddedByUserUID'],
			"AssignedDateTime" => date('y-m-d H:i:s'),
			"AssignedByUserUID" => $data['AssignedByUserUID'],
			"CompletedDateTime" => $COMPLETEDDate,
			"CompletedByUserUID" => $CompletedUserID,
			"ModifiedDateTime" => $ModifiedDateTime,
			"ModifiedByUserUID" => $data['ModifiedByUserUID'],
			);
		if (empty($data['AdditionalAuditLog'])) {
			$data['AddedDateTime'] = NULL;
		}
		$this->db->insert('tUserTask', $fieldArray);
		$LastTaskUID = $this->db->insert_id();

//insert audit log
		$User = $this->common_model->GetUserDetailsByUser($data['AssignedTo'])->UserName;
		$Content = 'Task: <b>'.$data['Description'].'</b> is Created';
		if (!empty($User)) {
			$Content .= ' and Assigned to <b>'.$User.'</b>';
		}

		/*Add Additional Audit log if AutoCreation task*/
		if (!empty($data['AdditionalAuditLog'])) {
			$Content .= $data['AdditionalAuditLog'];
		}

	
	$InsertData = array(
		'UserUID' => $this->loggedid,
		'ModuleName' => 'Task',
		'OrderUID' => $data['OrderUID'],
		'Feature' => $LastTaskUID,
		'Content' => htmlentities($Content),
		'DateTime' => date('Y-m-d H:i:s'));
	$this->common_model->InsertAuditTrail($InsertData);
		return true;
	}

	/**
		*@description Function to get Incomplete Tasks for an Order
		*
		* @param $OrderUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return Array 
		* @since Date 
		* @version Task AutoCreation 
		*
	*/ 
	function getInCompleteTasks($OrderUID){
		/*Query*/
		$this->db->select('*', false);
		$this->db->from('tUserTask'); 
		$this->db->where('tUserTask.TaskStatus != "COMPLETED"', NULL, FALSE);
		return $this->db->get()->result();
	}

	/**
		*@description Function to get Incomplete Issues for an Order
		*
		* @param $OrderUID
		* 
		* @throws no exception
		* @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
		* @return Array 
		* @since Feb 2 2020 
		* @version Issue AutoCreation 
		*
	*/ 
	function getInCompleteIssues($OrderUID, $WorkflowModuleName){
		/*Query*/
		$this->db->select('tOrderIssueTypes.*', false);
		$this->db->from('tOrderIssueTypes'); 
		$this->db->join('tOrderIssues','tOrderIssues.OrderIssueUID = tOrderIssueTypes.OrderIssueUID','LEFT');
		$this->db->join('mworkflowmodules','FIND_IN_SET(mworkflowmodules.WorkflowModuleUID,tOrderIssueTypes.IssueWorkflows)','LEFT',FALSE);
		$this->db->where('mworkflowmodules.WorkflowModuleName',$WorkflowModuleName);
		$this->db->where('tOrderIssues.OrderUID',$OrderUID);
		$this->db->where('tOrderIssueTypes.IssueStatusUID != "'.$this->config->item('IssueStatus')['Completed'].'"', NULL, FALSE);
		$this->db->where('tOrderIssueTypes.IssueStatusUID != "'.$this->config->item('IssueStatus')['Closed'].'"', NULL, FALSE);
		$this->db->where('tOrderIssueTypes.IssueStatusUID != "'.$this->config->item('IssueStatus')['Cancelled'].'"', NULL, FALSE);
		return $this->db->get()->result();
	}

	/**
	  *@description Function to get Orders status
		*
		* @param null
		* 
		* @throws no exception
		* @author Periyasamy <periyasamy.s@avanzegroup.com>
		* @return Object 
		*
	*/
	function GetOrderstatusDetails()
	{
		return $this->db->get('mOrderStatus')->result();
	}


	/**********@author praveen kumar - Friday 17 January 2020**********/
	function get_customerdetails_byuid($customeruid){ 
		$this->db->where(array("Active"=>1,"CustomerUID"=>$customeruid));
		$query = $this->db->get('mCustomers');
		return $query->row();
	}
    		/**
	  *@description Function to get Schedule by scheduleUID
		*
		* @param scheduleUID int
		* 
		* @author D.Samuel Prabhu
		* @return Object 
		*
	*/
    function getScheduleByID($ScheduleUID)
	{
		$this->db->select('*');
		$this->db->from('tOrderschedule'); 
		$this->db->where('tOrderschedule.ScheduleUID', $ScheduleUID);
		return $this->db->get()->row();
	}


	function datatable_rawqueryearch($post)
	{

		$likeQuery = "AND ";
		foreach ($post['column_search'] as $key => $item) { // loop column 
			// if datatable send POST for search
			if ($key === 0) { // first loop
				$likeQuery .= "( ".$item." LIKE '%".$post['search_value']."%' "; 
			} else {
				$likeQuery .= " OR ".$item." LIKE '%".$post['search_value']."%' ";    
			}
		}
		$likeQuery .= ") ";
		return $likeQuery;
		
	}

	function datatable_rawqueryorderby($post)
	{
		// here order processing
		if ($post['column_order'][$post['order'][0]['column']] != '') {
			return 'ORDER BY '.$post['column_order'][$post['order'][0]['column']].' '.$post['order'][0]['dir'];
		}
		
	}


	/*** 
	 * Function    : apiOrdersCount()
     * Description : To get count of apiorders, inboundorders, outboundorders count by orderuid
     * Date        : 23/01/2020
     * Author      : D.Samuel Prabhu
     * @Parameter  : OrderUID Int 
     *
	*/



  //   function apiOrdersCount($OrderUID)
  //   {
  //   	$this->db->from('tApiOrders');
  //   	$this->db->where('OrderUID',$OrderUID);
		// $query = $this->db->get();
		// $apiOrders = $query->num_rows();

		// $this->db->from('tApiInboundOrders');
		// $this->db->where('OrderUID',$OrderUID);
		// $query = $this->db->get();
		// $inboundOrders = $query->num_rows();

		// $this->db->from('tApiOutBoundOrders');
		// $this->db->where('OrderUID',$OrderUID);
		// $query = $this->db->get();
		// $outboundOrders = $query->num_rows();
        
  //       $total = $apiOrders + $inboundOrders + $outboundOrders;

		// return array('total' => $total, 'apiOrders' =>$apiOrders, 'inboundOrders' =>$inboundOrders, 'outboundOrders' =>$outboundOrders);
  //   }

    function getsystemNotes(){
    	$this->db->select('*');
    	$this->db->from('mreportsections');
    	$this->db->where('SectionUID',1);
    	$query = $this->db->get()->row();
    	return $query;
    }


  /**
  * Function -  Reverse Mortgage Modifications
  *
  * @param  (int) CustomerUID, (int) SubProductUID
  * 
  * @throws exception snippet is not valid or present
  * @author Praveen Kumar <praveen.kumar@avanzegroup.com>
  * @return json response valid or invalid 
  * @since Monday 03 February 2020
  * @version Reverse Mortgage Modifications
  *
	*/ 
  function customer_gradebysubproduct($CustomerUID,$SubProductUID)
  {
  	return $this->db->select('mCustomerProducts.GradingType,mCustomerProducts.SubCategoryGrading')->where(array('CustomerUID'=>$CustomerUID,'SubProductUID'=>$SubProductUID))->get('mCustomerProducts')->row();

  }

  /**
  * Function -  Reverse Mortgage Modifications Order fetch in mCustomerProducts
  *
  * @param  (int) CustomerUID, (int) SubProductUID
  * 
  * @throws exception snippet is not valid or present
  * @author Praveen Kumar <praveen.kumar@avanzegroup.com>
  * @return json response valid or invalid 
  * @since Monday 03 February 2020
  * @version Reverse Mortgage Modifications
  *
	*/ 

  function get_rmsorderdetails($OrderUID)
  {
  	$this->db->select ( 'tOrders.OrderUID,tOrders.Grade,tOrders.OrderDueDatetime,tOrders.GradeAmount,tOrders.ImpedimentDateTime,tOrders.OriginalOrderDueDateTime,tOrders.CustomerUID,tOrders.SubProductUID,tOrders.CustomerAmount,tOrders.IsBilled,tOrders.CustomerActualAmount,tOrders.DocumentStatus');
  	$this->db->select ('mCustomers.PricingUID,mSubProducts.RMS,mCustomerProducts.ProductUID,mCustomerProducts.GradingType,mCustomerProducts.SubCategoryGrading');
  	$this->db->from ( 'tOrders' );
  	$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
  	$this->db->join ( 'mCustomerProducts', 'mCustomerProducts.CustomerUID = tOrders.CustomerUID  AND mCustomerProducts.SubProductUID = tOrders.SubProductUID ' , 'left' );
  	$this->db->join ( 'mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID' , 'left' );
  	$this->db->where ('tOrders.OrderUID',$OrderUID);
  	$query = $this->db->get();
  	return $query->row();
  }

  /**
  * Function -  Reverse Mortgage Modifications -RMS Subproducts
  *
  * @param  (int) CustomerUID, (int) SubProductUID
  * 
  * @throws exception snippet is not valid or present
  * @author Praveen Kumar <praveen.kumar@avanzegroup.com>
  * @return json response valid or invalid 
  * @since Monday 03 February 2020
  * @version Reverse Mortgage Modifications
  *
	*/ 
  function get_rmssubproducts(){

  	$this->db->where(array('RMS'=>1,'Active'=>1));
  	$query = $this->db->get('mSubProducts');
  	return $query->result();
  }

     /**
    	*@description Function to getOrderCountyUID
    	*
    	* @param $OrderUID
    	* 
    	* @throws no exception
    	* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
    	* @return Object 
    	* @since 3.2.2020 
    	* @version Order CheckListItems 
    	*
    */ 
	function getCountyUID_ByOrderUID($OrderUID)
	{
		$this->db->select('CountyUID')->from('tOrders');
		$this->db->join('mStates', 'mStates.StateCode=tOrders.PropertyStateCode');
		$this->db->join('mCounties', 'mCounties.CountyName=tOrders.PropertyCountyName AND mStates.StateUID=mCounties.StateUID');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$County = $this->db->get()->row();

		if (!empty($County)) {
			return $County->CountyUID;
		}
		else{
			return NULL;
		}

	}

    /**
    	*@description Function to getOrderCityUID
    	*
    	* @param $OrderUID
    	* 
    	* @throws no exception
    	* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
    	* @return Object 
    	* @since 3.2.2020 
    	* @version Order CheckListItems 
    	*
    */ 
	function getCityUID_ByOrderUID($OrderUID)
	{
		$this->db->select('mCities.CityUID')->from('tOrders');
		$this->db->join('mStates', 'mStates.StateCode=tOrders.PropertyStateCode');
		$this->db->join('mCounties', 'mCounties.CountyName=tOrders.PropertyCountyName AND mStates.StateUID=mCounties.StateUID');
		$this->db->join('mCities', 'mCities.CountyUID=mCounties.CountyUID AND mStates.StateUID = mCities.StateUID AND mCities.CityName = tOrders.PropertyCityName');
		$this->db->where('tOrders.OrderUID', $OrderUID);
		$City = implode(", ", array_filter( array_column($this->db->get()->result_array(), 'CityUID') ) );

		if (!empty($City)) {
			return $City;
		}
		else{
			return 0;
		}

	}


	/**
    	*@description Function to X1 GetTransactionTypeDetails,GetPropertyTypeDetails,GetBorrowerDetailsDescription
    	*
    	* @param No Parameters
    	* 
    	* @throws no exception
    	* @author Yagavi G <yagavi.g@avanzegroup.com>
    	* @return Object 
    	* @since 4.2.2020 
    	*
    */ 

	function GetTransactionTypeDetails()
	{
		return $this->db->select('*')->from('mTransactionType')->where('Active','1')->get()->result();
	}
	function GetPropertyTypeDetails()
	{
		return $this->db->select('*')->from('mpropertyuse')->where('Active',1)->get()->result();
	}
	function GetBorrowerDetailsDescription()
	{
		return $this->db->select('*')->from('mBorrowerType')->where('Active',1)->get()->result();
	}

	/**
    	*@description Function to get EventLogOrders
    	*
    	* @param OrderUID
    	* 
    	* @throws no exception
    	* @author Yagavi G <yagavi.g@avanzegroup.com>
    	* @return Object 
    	* @since 4.2.2020 
    	*
    */ 

	function CheckEventLogOrders($OrderUID)
	{
		$ApiOutboundStatus = array('50','100');
		$this->db->select('*');
		$this->db->from('tOrderEventLog');
		$this->db->join('tApiOutBoundOrders','tApiOutBoundOrders.ApiOutBoundOrderUID = tApiOutBoundOrders.ApiOutBoundOrderUID');
		$this->db->join('tOrders','tOrders.OrderUID = tApiOutBoundOrders.OrderUID','left');
		$this->db->where('tOrderEventLog.OrderUID',$OrderUID);
		$this->db->where('tApiOutBoundOrders.Status', 'Accepted');
		$this->db->where_not_in('tApiOutBoundOrders.ApiOutboundStatus', $ApiOutboundStatus);
		$this->db->where('tOrders.OrderUID',$OrderUID);
		$this->db->group_by('tApiOutBoundOrders.ApiOutBoundOrderUID');
		$tOrderEventLog = $this->db->get()->row();
		return $tOrderEventLog;
	}

	function get_Product_SubProduct()
	{
		$this->db->select('*');
		$this->db->from('mSubProducts');
		$this->db->join('mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID');
		$this->db->where('mSubProducts.SubProductUID', $SubProductUID);
		$this->db->where('mSubProducts.Active', 1);
		$this->db->where('mProducts.Active', 1);
		return $this->db->get()->result();
	}

	function GetX1EnabledforCustomerSubproduct($OrderUID){
		$order_details = $this->get_orderdetails($OrderUID);
		$CustomerUID = $order_details->CustomerUID;
		$SubProductUID = $order_details->SubProductUID;
		$OrderSourceUID = $this->db->select('OrderSourceUID')->from('mApiTitlePlatform')->where('OrderSourceName','X1')->get()->row()->OrderSourceUID;
		$result = $this->db->select('*')->from('mCustomerProducts')->where(array('CustomerUID'=>$CustomerUID,'SubProductUID'=>$SubProductUID,'OrderSourceUID'=>$OrderSourceUID))->get()->row();
		if(!empty($result))
		{
			return $result;
		}
	}

	/**
    	*@description Function to check the X1 search mode enabled for the order
    	*
    	* @param OrderUID
    	* 
    	* @throws no exception
    	* @author Yagavi G <yagavi.g@avanzegroup.com>
    	* @return Object 
    	* @since 7.2.2020 
    	*
    */ 

	function GetX1Order($OrderUID) 
	{
		$tOrders=$this->db->get_where('tOrders', array('OrderUID'=>$OrderUID))->row();
		$mStates=$this->db->get_where('mStates', array('StateCode' => $tOrders->PropertyStateCode))->row();
		$mCounties=$this->db->get_where('mCounties', array('StateUID'=>$mStates->StateUID, 'CountyName'=>$tOrders->PropertyCountyName))->row();
		if(!empty($mCounties))
		{
			$query = $this->db->query("SELECT *, CASE WHEN msearchmodes.SearchModeUID = '6' THEN 
				mcountysearchmodes.WebsiteURL ELSE msearchmodes.SearchSiteURL END AS SiteURL 
				FROM mcountysearchmodes
				LEFT JOIN msearchmodes ON mcountysearchmodes.SearchModeUID = msearchmodes.SearchModeUID 
				WHERE mcountysearchmodes.CountyUID = '". $mCounties->CountyUID ."'and msearchmodes.SearchSiteURL = 'X1' AND msearchmodes.SearchModeUID <> 5
				Order By FIELD(SearchModeName, 'Free', 'Paid', 'Others', 'Abstractor')");

			$data = $query->row();
		}
		return $data;
	}

	// function CheckX1ApiOutboundOrders($OrderUID)
	// {
	// 	$this->db->select('*');
	// 	$this->db->from('tApiOutBoundOrders');
	// 	$this->db->join('tOrders','tOrders.OrderUID = tApiOutBoundOrders.OrderUID','left');
	// 	$this->db->join('mApiTitlePlatform','mApiTitlePlatform.OrderSourceUID = tApiOutBoundOrders.OrderSourceUID');
	// 	$this->db->where('tApiOutBoundOrders.OrderUID',$OrderUID);
	// 	$this->db->where('mApiTitlePlatform.OrderSourceName','X1');
	// 	$tApiOutBoundOrders = $this->db->get()->result();

	// 	if(empty($tApiOutBoundOrders)){
	// 		return true;
	// 	} /*else {			
	// 		$Status = [];
	// 		foreach ($tApiOutBoundOrders as $key => $value) {
	// 			$Status[] = $value->Status;
	// 		}

	// 		if (count(array_unique($Status)) <= 1) { 
	// 			if($Status[0] == 'Cancelled') {
	// 				return $tApiOutBoundOrders;
	// 			}
	// 		}
	// 	}*/
	// }

	/**
		*@description Function to getVendorOrderProcessedToday
		*
		* @param $AbstractorUID, $AbstractorPricingProductUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return int 
		* @since 7.2.2020 
		* @version Vendor Mgmt . Search & Schedule 
		*
	*/ 

  public function getAbstractor_OrderProcessed($AbstractorUID, $AbstractorPricingProductUID)
  {
    $mabstractorfee = $this->common_model->get_row('mabstractorfee', ['AbstractorPricingProductUID'=>$AbstractorPricingProductUID]);

    if (!empty($mabstractorfee)) {

      $where = [];
      if (!empty($mabstractorfee->OrderTypeUID)) {
        $where[] = "( mabstractorfee.OrderTypeUID = '".$mabstractorfee->OrderTypeUID."' ) ";
      }
      else{
       $where[] = "( mabstractorfee.OrderTypeUID = '0' OR mabstractorfee.OrderTypeUID IS NULL ) "; 
      }

      if (!empty($mabstractorfee->CustomerUID)) {
        $where[] = "( mabstractorfee.CustomerUID = '".$mabstractorfee->CustomerUID."' ) ";
      }
      else{
       $where[] = "( mabstractorfee.CustomerUID = '0' OR mabstractorfee.CustomerUID IS NULL ) "; 
      }


      if (!empty($mabstractorfee->StateUID)) {
        $where[] = "( mabstractorfee.StateUID = '".$mabstractorfee->StateUID."' ) ";
      }


      if (!empty($mabstractorfee->CountyUID)) {
        $where[] = "( mabstractorfee.CountyUID = '".$mabstractorfee->CountyUID."' ) ";
      }

      if (!empty($mabstractorfee->CityUID)) {
        $where[] = "( mabstractorfee.CityUID = '".$mabstractorfee->CityUID."' ) ";
      }

      if (!empty($mabstractorfee->ZipCode)) {
        $where[] = "( mabstractorfee.ZipCode = '".$mabstractorfee->ZipCode."' ) ";
      }


      $where[] = " ( mabstractorfee.AbstractorUID = '".$AbstractorUID."' ) ";


      $this->db->select('1 AS row', false);
      $this->db->from('torderabstractor');
      $this->db->where('torderabstractor.AbstractorPricingProductUID IN ( SELECT AbstractorPricingProductUID FROM mabstractorfee WHERE '.implode(" AND ", $where).')', NULL, FALSE);
      //$this->db->where_not_in('torderabstractor.OrderStatus', [5]);
      $this->db->where('DATE(AssignedDateTime) = CURDATE()', NULL, FALSE);

      return $this->db->get()->num_rows();

    }

    return 0;
  }

  /**
  	*@description Function to getCityByStateCountyCityName
  	*
  	* @param $StateUID, $CountyUID, $CityName
  	* 
  	* @throws no exception
  	* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
  	* @return object 
  	* @since 7.2.2020 
  	* @version Vendor Mgmt - Search, Schedule 
  	*
  */ 
  function getCityByStateCountyCityName($StateUID, $CountyUID, $CityName){
  	/*Query*/
  	$this->db->select('mCities.CityUID');
  	$this->db->from('mCities'); 
  	$this->db->where('mCities.StateUID', $StateUID);
  	$this->db->where('mCities.CountyUID', $CountyUID);
  	$this->db->where('mCities.CityName', $CityName);
  	$City = implode(", ", array_filter(array_column( $this->db->get()->result_array(),'CityUID' ) ) );

  	if (!empty($City)) {
  		return $City;
  	}
  	else{
  		return 0;
  	}

  }

  /**
	* Function -  check valid order
	*
	* @param  int orderuid
	* 
	* @throws exception snippet is not valid or present
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return json response valid or invalid 
	* @since date Saturday 08 February 2020
	*
	*/ 

	function isvalid_order($orderuid) {

		$query = $this->db->query("SELECT EXISTS(SELECT 1 FROM (`tOrders`) WHERE `tOrders`.`OrderUID` = {$orderuid} ) AS available");
		return $query->row()->available;
	}


	function isslatriggered_order($orderuid,$slareportuid) {

		$query = $this->db->query("SELECT * FROM (`tSLAReport`) WHERE `tSLAReport`.`OrderUID` = {$orderuid} AND `tSLAReport`.`SLAReportUID` = {$slareportuid} ");
		return $query->row();
	}

	function triggerorder_insertsla($insertdata) {

		$insertdata['CreatedDateTime'] = date('y-m-d H:i:s');
		$insertdata['ModifiedDateTime'] = date('y-m-d H:i:s');
		$this->db->insert('tSLAReport',$insertdata);
	}

	function triggerorder_updatesla($tOrdersLAReportUID,$updatedata) {

		$updatedata['ModifiedDateTime'] = date('y-m-d H:i:s');
		$this->db->where('tOrdersLAReportUID',$tOrdersLAReportUID);
		$this->db->update('tSLAReport',$updatedata);
	}

	function get_slaorderdetails($orderuid)
	{
		$this->db->select ( '*');
		$this->db->from ( 'tOrders' );
		$this->db->where ('tOrders.OrderUID',$orderuid);
		$query = $this->db->get(); 
		return $query->row();
	}

	function get_slalists($OrderDetails)
	{
		$query = $this->db->query("SELECT * FROM `mSLAReport` LEFT JOIN mSLAReportCustomerProduct  ON mSLAReportCustomerProduct.SLAReportUID = mSLAReport.SLAReportUID LEFT JOIN mSLAReportPriority  ON mSLAReportPriority.SLAReportUID = mSLAReportPriority.SLAReportUID WHERE CustomerUID  = {$OrderDetails->CustomerUID} AND SubproductUID = {$OrderDetails->SubProductUID} AND mSLAReportPriority.PriorityUID = {$OrderDetails->PriorityUID}
		");
		return $query->result();
	}


 /**
  	*Function to update the order number in api when product/sub-product is changed
  	*
  	* @param $OrderUID, $OrderUID
  	* 
  	* @throws no exception
  	* @author D.Samuel Prabhu
  	* @return array
  	* @since 8.2.2020 
  	*
  */ 

function UpdateApiOrderNumber($OrderUID, $OrderNumber)
{
	$APIOrder = $this->db->select('*')
						-> where('OrderUID', $OrderUID)
					->limit(1)->get('tApiOrders')->row();

	if(!empty($APIOrder))
	{
		$Detail['OrderUID'] = $OrderUID;
		$Detail['InBoundUID'] = $APIOrder->InBoundUID;
		$Detail['OrderNumber'] = $OrderNumber;
		
		$res = $this->sendRequest('api/UpdateOrderNumber',$Detail);
      
        if($res)
        {
        	return true;
        }
        else
        {
        	return false;
        }

	}
}

/**
* Function to send $post values via curl function to api
*
* @param $post - array of post values
* @param $path - path of api controller
* 
* @throws no exception
* @author D.Samuel Prabhu
* @return json
* @since 8.2.2020 
*
*/ 

function sendRequest($path, $post)
{
    $data = json_encode($post);
    $url = $this->config->item('api_url').$path;
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 3000,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "authorization: isgn",
        "cache-control: no-cache",
        "content-type: application/json",
      )
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl); 

    return ($response ? $response : false); 
}

	/**
		* @description Get Products By Customer
		* @param CustomerUID
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return array 
		* @since  2/11/2020
		* @version  
		*/ 
		function getProductsByCustomer($CustomerUID){
			if($CustomerUID)
			{
				$this->db->select('mCustomerProducts.ProductUID,mProducts.ProductName');
				$this->db->from('mCustomerProducts');
				if(is_array($CustomerUID))
				{
				  $this->db->where_in('mCustomerProducts.CustomerUID',$CustomerUID);
				} else {
				  $this->db->where('mCustomerProducts.CustomerUID',$CustomerUID);
				}
				$this->db->join('mProducts','mProducts.ProductUID = mCustomerProducts.ProductUID','LEFT');
				$this->db->group_by('ProductUID');
				return $this->db->get()->result();
			} else {
				$this->db->select('mProducts.ProductUID,mProducts.ProductName');
				$this->db->from('mProducts');
				return $this->db->get()->result();
			}
		}
	
	/**
		* @description Get SubProducts By Customer
		* @param ProductUID
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @author Periyasamy S 
		* @return array 
		* @since  2/11/2020
		* @version  
		*/ 
		function getSubProductsByProduct($ProductUID,$CustomerUID='')
		{
			$this->db->select('mSubProducts.SubProductUID,mSubProducts.SubProductName');
			$this->db->from('mSubProducts');
			if(!empty($ProductUID)) 
			{
			  if(is_array($ProductUID))
			  {
			    $this->db->where_in("mSubProducts.ProductUID",$ProductUID);
			  }	else {
			    $this->db->where("mSubProducts.ProductUID",$ProductUID);
			  } 
		 	} 
		 	if(!empty($CustomerUID))
		 	{
		 	  $this->db->join('mCustomerProducts','mCustomerProducts.SubProductUID = mSubProducts.SubProductUID','INNER');	
		 	  if(is_array($CustomerUID))
		 	  {
		 	  	$this->db->where_in('mCustomerProducts.CustomerUID',$CustomerUID);
		 	  } else {
		 	    $this->db->where('mCustomerProducts.CustomerUID',$CustomerUID);
		 	  }
		 	}
		 	$this->db->group_by('mSubProducts.SubProductUID');
			return $this->db->get()->result();
		}

	/**
		* @description Get ZipCodes By City
		* @param CityUID
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return array 
		* @since  2/12/2020
		* @version  
		*/ 
		function getCityZipCodes($CityUID){
			$this->db->select('ZipCode');
			$this->db->from('mCities');
			$this->db->where('CityUID',$CityUID);
			return $this->db->get()->result();
		}

		/**
		* @description Get Multiple ZipCodes By CityName
		* @param CityName
		* @throws no exception
		* @author Periyasamy S
		* @return array 
		* @since  27/02/2020
		* @version  
		*/ 
		function getZipCodeByCityName($CityUID,$StateUID='',$CountyUID='')
		{
		  $this->db->select('ZipCode');
		  $this->db->from('mCities');
		  if(!empty($StateUID))
		  {
		  	if(is_array($StateUID))
		  	{
		  	  $this->db->where_in('StateUID', $StateUID);
		  	} else {
		  	  $this->db->where('StateUID', $StateUID);	
		  	}
		  }
		  if(!empty($CountyUID))
		  {
		  	if(is_array($CountyUID))
		  	{
		  	  $this->db->where_in('CountyUID', $CountyUID);	
		  	} else {
		  	  $this->db->where('CountyUID', $CountyUID);	
		  	}
		  }
		  if(!empty($CityUID))
		  {
		    $this->db->where_in('CityUID',$CityUID);
		  }
		  return $this->db->get()->result();
		}

		/**
		* @description Get Vendor Types
		* @author Periyasamy S
		* @param null
		* @throws no exception
		* @return object 
		* @since  05/03/2020
		*/ 
		function getRoleCategorys($Orderby='')
		{
		   $this->db->where('Active',1);
		   if(!empty($Orderby))
		   {
		   	 $this->db->_protect_identifiers = FALSE;
		   	 // $this->db->order_by('FIELD(RoleCategoryUID,'.$Orderby.')','DESC');
		   	 $this->db->_protect_identifiers = true;
		   }
		   return $this->db->get('mRoleCategory')->result();
		}

		/**
		* @description Get Vendor Types By UID
		* @author Periyasamy S
		* @param UID
		* @throws no exception
		* @return object 
		* @since  09/03/2020
		*/ 
		function getRoleCategorysbyUID($RoleCategoryUID,$Multiple='')
		{
		  if(!empty($Multiple) && !empty($RoleCategoryUID))// Empty validated @Uba @On Jul 6 2020
		  {
		  	$this->db->select('GROUP_CONCAT(RoleCategoryName) AS RoleCategoryName');
		  	$this->db->where('RoleCategoryUID IN ('.$RoleCategoryUID.')',NULL,FALSE);
		  }	else {
		    $this->db->where('RoleCategoryUID',$RoleCategoryUID);
		  }
		   return $this->db->get('mRoleCategory')->row();
		}

		/**
		* @description Get Vendor Type Fields
		* @author Periyasamy S
		* @param RoleCategoryUID
		* @throws no exception
		* @return object 
		* @since  05/03/2020
		*/ 
		function getRoleCategoryFields($Where)
		{
		   $this->db->where($Where);
		   return $this->db->get('mRoleCategoryFields')->result();
		}

		/**
		* @description Get Vendor Type Fields Values
		* @author Periyasamy S
		* @param Custom Columns
		* @throws no exception
		* @return object 
		* @since  10/03/2020
		*/ 
		function getRoleCategoryFieldValues($Where)
		{
		   $this->db->where($Where);
		   $this->db->from('mVendorDetails');
		   $this->db->join('mRoleCategoryFields','mRoleCategoryFields.FieldUID = mVendorDetails.FieldUID','LEFT');
		   return $this->db->get()->row();
		}

		/**
		* @description Get Vendor Type Fields Values
		* @author Periyasamy S
		* @param Custom Columns
		* @throws no exception
		* @return object 
		* @since  10/03/2020
		*/ 
		function getVendorLicenseDate($Where)
		{
		   $this->db->where($Where);
		   $this->db->from('mVendorLicenseExpiryDate');
		   return $this->db->get()->row();
		}

		/**
		* @description Get Any Table Column
		* @author Periyasamy S
		* @param TableName
		* @throws no exception
		* @return object 
		* @since  04/03/2020
		* @version  
		*/ 
		function getTableColumns($tableName)
		{
		   return $this->db->query('SHOW COLUMNS FROM '.$tableName)->result();
		}

		/**
		* @description Get Any Table Value with PrimaryKey
		* @author Periyasamy S
		* @param Table Records
		* @throws no exception
		* @return object 
		* @since  04/03/2020
		* @version  
		*/ 
		function getTableValue($TableName)
		{
		   return $this->db->query(" SELECT *,(SELECT @columnID:=`COLUMN_NAME` AS columnID FROM `information_schema`.`COLUMNS` WHERE (`TABLE_NAME` = '".$TableName."') AND (`COLUMN_KEY` = 'PRI') GROUP BY COLUMN_KEY) AS PK FROM ".$TableName)->result();
		}

		/**
		* @description Get Any Table Particular Column value
		* @param ColumnValue
		* @throws no exception
		* @author Periyasamy S
		* @return array 
		* @since  29/02/2020
		* @version  
		*/ 
		function getTableColumnValue($TableName,$PrimaryKey,$PrimaryKeyValue,$ColumnName)
		{
		  $this->db->select($ColumnName);
		  $this->db->from($TableName);
		  $this->db->where($PrimaryKey,$PrimaryKeyValue);
		  $query = $this->db->get();
		  $Val = $query->row()->$ColumnName;	
		  if(empty($Val))
		  {
		  	return 'N/A';
		  } else {
		  	return $Val;
		  }
		}

		/**
		* Function -  SLA ACTIONS LIST
		*
		* @param  int orderuid
		* 
		* @throws exception snippet is not valid or present
		* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
		* @return json response valid or invalid 
		* @since Thursday 13 February 2020
		*
		*/ 
		function get_slaactions()
		{
			$this->db->select('*');
			$this->db->from('mSLAActions');
			return $this->db->get()->result();
		}


		/**
		* Function -  insert sla details
		*
		* @param  int orderuid, int ActionUID, int  AssignedToUserUID
		* @author alwin <alwin.l@avanzegroup.com>
		* @return nothing 
		* @since Thursday 15 February 2020
		*
		*/ 

		function insert_slaaction($OrderUID,$ActionUID,$AssignedToUserUID,$OrderTypeUID = '')
		{
			//trigger DocumentPackage slaaction start
			// @author alwin <alwin.l@avanzegroup.com>
			//trigger DocumentPackage slaaction end 
			// $this->InsertDocumentPackageTrigger($ActionUID,$OrderUID,$AssignedToUserUID);

			$sla_noteddata = array(
				'OrderUID' => $OrderUID,
				'ActionUID' => $ActionUID,
				'ActionedByUserUID' => $AssignedToUserUID,
				'OrderTypeUID' => $OrderTypeUID, //@Desc OrderTypeUID added @Author Jainulabdeen @Updated on June 3 2020
				'ActionDateTime' => date('Y-m-d H:i:s'),
			);
			$this->db->insert('tOrdersLAActions',$sla_noteddata);

		}

		/**
		* Function -  get externol order
		*
		* @param  int orderuid
		* @author alwin <alwin.l@avanzegroup.com>
		* @return boolean 
		* @since Thursday 15 February 2020
		*
		*/ 

		function Check_InternolExternol($OrderUID)
		{
			$result = $this->db->select('*')->from('tOrders')->where(array('OrderUID' => $OrderUID, 'IsInhouseExternal' => 1))->get()->row();
			if(!empty($result))
			{
				return true;
			}
			else
			{
				return  false;
			}
		}



		/**
			*@description Function to getVendor_PassThruCost
			*
			* @param $AbstractorUID, $AbstractorPricingProductUID
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return int 
			* @since 18.2.2020 
			* @version Vendor PassThru Cost 
			*
		*/ 

		public function getVendor_PassThruCost($order_details, $AbstractorPricingProductUID, $FeeReasonUID = NULL)
		{
			$mabstractorfee = $this->common_model->get_row('mabstractorfee', ['AbstractorPricingProductUID'=>$AbstractorPricingProductUID]);
			$torderabstractor = $this->common_model->get_row('torderabstractor', ['AbstractorPricingProductUID'=>$AbstractorPricingProductUID]);
			if (!empty($torderabstractor)) {			
				$tOrderschedule = $this->common_model->get_row('tOrderschedule', ['AbstractorOrderUID'=>$torderabstractor->AbstractorOrderUID]);
				if (!empty($tOrderschedule)) {
					$tOrderClosing = $this->common_model->get_row('tOrderClosing', ['ScheduleUID'=>$tOrderschedule->ScheduleUID]);
					$order_details->PropertyZipcode = $tOrderClosing->SigningZipCode;
					$order_details->PropertyCityName = $tOrderClosing->SigningCityName;
					$order_details->PropertyCountyName = $tOrderClosing->SigningCountyName;
					$order_details->PropertyStateCode = $tOrderClosing->SigningStateCode;
				}
			}
			else {
				$torderabstractorunassign = $this->common_model->get_row('torderabstractorunassign', ['AbstractorPricingProductUID'=>$AbstractorPricingProductUID]);
				if (!empty($torderabstractorunassign)) {			
					$tOrderschedule = $this->common_model->get_row('tOrderschedule', ['AbstractorOrderUID'=>$torderabstractorunassign->OldAbstractorOrderUID]);
					if (!empty($tOrderschedule)) {
						$tOrderClosing = $this->common_model->get_row('tOrderClosing', ['ScheduleUID'=>$tOrderschedule->ScheduleUID]);
						$order_details->PropertyZipcode = $tOrderClosing->SigningZipCode;
						$order_details->PropertyCityName = $tOrderClosing->SigningCityName;
						$order_details->PropertyCountyName = $tOrderClosing->SigningCountyName;
						$order_details->PropertyStateCode = $tOrderClosing->SigningStateCode;
					}

				}
			}

			$State=$this->common_model->GetStatebyCode($order_details->PropertyStateCode);

			if (!empty($State) && !empty($State->StateUID)) {
				$AddressUIDs->StateUID = $State->StateUID;    
			}


			$this->db->select('CountyUID')->from('tOrders');
			$this->db->join('mStates', 'mStates.StateCode=tOrders.PropertyStateCode');
			$this->db->join('mCounties', 'mCounties.CountyName=tOrders.PropertyCountyName AND mStates.StateUID=mCounties.StateUID');
			$this->db->where('tOrders.OrderUID', $tOrders->OrderUID);
			$AddressUIDs->CountyUID=$this->db->get()->row()->CountyUID;


			$AddressUIDs->CityUID = $this->common_model->getCityUID_ByOrderUID($tOrders->OrderUID);
			$AddressUIDs->ZipCode = $order_details->PropertyZipcode;


			 $AddressUIDs = $this->_getCounty_StateUID_ZipCode($order_details->PropertyZipcode,$order_details->PropertyStateCode);

			if (!empty($mabstractorfee)) {

				$conditionwhere = [];
				if (!empty($mabstractorfee->OrderTypeUID)) {
					$conditionwhere['OrderTypeUID'] = "( mVendorPassThru.OrderTypeUID = '".$mabstractorfee->OrderTypeUID."' ) ";
				}
				else{
					$conditionwhere['OrderTypeUID'] = "( mVendorPassThru.OrderTypeUID = '0' OR mVendorPassThru.OrderTypeUID IS NULL ) "; 
				}

				if (!empty($order_details->CustomerUID)) {
					$conditionwhere['CustomerUID'] = "( mVendorPassThru.CustomerUID = '".$order_details->CustomerUID."' ) ";
				}
				else{
					$conditionwhere['CustomerUID'] = "( mVendorPassThru.CustomerUID = '0' OR mVendorPassThru.CustomerUID IS NULL ) "; 
				}


				if (!empty($AddressUIDs->StateUID)) {
					$conditionwhere['StateUID'] = "( mVendorPassThru.StateUID = '".$AddressUIDs->StateUID."' ) ";
				}
				else {
					$conditionwhere['StateUID'] = "( mVendorPassThru.StateUID = 0 OR mVendorPassThru.StateUID IS NULL ) ";	
				}


				if (!empty($AddressUIDs->CountyUID)) {
					$conditionwhere['CountyUID'] = "( mVendorPassThru.CountyUID = '".$AddressUIDs->CountyUID."' ) ";
				}
				else {
					$conditionwhere['CountyUID'] = "( mVendorPassThru.CountyUID = 0 OR mVendorPassThru.CountyUID IS NULL ) ";	
				}

				if (!empty($AddressUIDs->CityUID)) {
						$conditionwhere['CityUID'] = "( mVendorPassThru.CityUID IN (".$AddressUIDs->CityUID.") ) ";
				}
				else {
					$conditionwhere['CityUID'] = "( mVendorPassThru.CityUID = 0 OR mVendorPassThru.CityUID IS NULL ) "; 
				}

				if (!empty($AddressUIDs->ZipCode)) {
					$conditionwhere['ZipCode'] = "( mVendorPassThru.ZipCode = '".$AddressUIDs->ZipCode."' ) ";
				}
				else {
					$conditionwhere['ZipCode'] = "( mVendorPassThru.ZipCode = 0 OR mVendorPassThru.ZipCode IS NULL ) ";	
				}

				if (!empty($FeeReasonUID)) {
					$conditionwhere['FeeReasonUID'] = "( mVendorPassThru.FeeReasonUID = '".$FeeReasonUID."' ) ";
				}
				else {
					$conditionwhere['FeeReasonUID'] = "( mVendorPassThru.FeeReasonUID = 0 OR mVendorPassThru.FeeReasonUID IS NULL ) ";	
				}


				$conditionwhere['VendorUID'] = " ( mVendorPassThru.VendorUID = '".$mabstractorfee->AbstractorUID."' ) ";



				$conditionwhere['CustomerUID_Empty'] = "( mVendorPassThru.CustomerUID = '0' OR mVendorPassThru.CustomerUID IS NULL ) "; 
				$conditionwhere['FeeReasonUID_Empty'] = "( mVendorPassThru.FeeReasonUID = 0 OR mVendorPassThru.FeeReasonUID IS NULL ) ";	
				$conditionwhere['StateUID_Empty'] = "( mVendorPassThru.StateUID = 0 OR mVendorPassThru.StateUID IS NULL ) ";	
				$conditionwhere['CountyUID_Empty'] = "( mVendorPassThru.CountyUID = 0 OR mVendorPassThru.CountyUID IS NULL ) ";	
				$conditionwhere['CityUID_Empty'] = "( mVendorPassThru.CityUID = 0 OR mVendorPassThru.CityUID IS NULL ) "; 
				$conditionwhere['ZipCode_Empty'] = "( mVendorPassThru.ZipCode = 0 OR mVendorPassThru.ZipCode IS NULL ) ";	


				$CASEWHEN = [];

				/*Scenario All Present*/
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";


				/* Fee Present, Customer Present, Geo Volatile*/
				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";



				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";


				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

				
				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID_Empty'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";




				/*Scenario Geo All Present, Fee Present, Customer Absent*/
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID_Empty'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";


				/*Scenario Geo All Present, Fee Absenet, Customer Present*/
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";


				/*Scenario Geo All Present, Fee Present, Customer Absent*/
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID_Empty'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";


				/* Fee Present, Customer Absent, Geo Volatile */
				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID_Empty'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";



				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID_Empty'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

				
				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID_Empty'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

				
				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID_Empty'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID_Empty'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";


				/* Fee Absent, Customer Present, Geo Volatile */
				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";



				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

				
				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

				
				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID_Empty'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";


				/* Fee Absent, Customer Absent, Geo Volatile */
				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID_Empty'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";



				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID_Empty'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

				
				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID_Empty'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

				
				/*Scenario */
				$Scenario = array_filter(array( $conditionwhere['VendorUID'], $conditionwhere['OrderTypeUID'], $conditionwhere['CustomerUID_Empty'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID_Empty'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']));
				$CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mVendorPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";




				$FeeFilter_Query = implode("", $CASEWHEN);



				foreach ($CASEWHEN as $key => $value) {
					if ($key == 0) {
						$FeeFilter_Query .= " '' 
						END) ";

					}
					else {
						$FeeFilter_Query .= "
						END)";
					}
				}

				$FeeFilter_Query .= " AS Cost";


				$this->db->select($FeeFilter_Query, false);
				$this->db->from('mVendorPassThru');
				$this->db->where("VendorUID", $mabstractorfee->AbstractorUID);
				$this->db->where("OrderTypeUID", $mabstractorfee->OrderTypeUID);

				return $this->db->get()->row();

			}

			return (object)[];
		}
/**
    *@description Function to fetchCustomerPassThruCost
    *
    * @param OrderUID, CustomerReason, CustomerAmount
    * 
    * @throws no exception
    * @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
    * @return JSON 
    * @since 3.3.2020 
    * @version Customer PassThru Cost 
    *
  */ 
  function getCustomer_PassThruCost($OrderUID,$CustomerAmount,$CustomerReason){
  	// echo '<pre>';print_r($OrderUID);echo '<pre>';print_r($CustomerAmount);echo '<pre>';print_r($CustomerReason);exit;
    $this->db->select('PropertyZipcode, PropertyStateCode, CustomerUID');
    $this->db->from('tOrders');
    $this->db->where('OrderUID', $OrderUID);
    $PropAddress = $this->db->get()->row();
    $AddressUIDs = $this->_getCounty_StateUID_ZipCode($PropAddress->PropertyZipcode,$PropAddress->PropertyStateCode);

    if (!empty($PropAddress)) {

        $conditionwhere = [];
        /*Fee Type*/
        $conditionwhere['CustomerUID'] = "( mCustomerPassThru.CustomerUID = '".$PropAddress->CustomerUID."' ) ";
        if (!empty($CustomerReason)) {
          $conditionwhere['FeeReasonUID'] = "( mCustomerPassThru.OverrideReasonUID = '".$CustomerReason."' ) ";
        }
        else{
          $conditionwhere['FeeReasonUID'] = "( mCustomerPassThru.OverrideReasonUID = '0' OR mCustomerPassThru.OverrideReasonUID IS NULL ) "; 
        }


        /*State*/
        if (!empty($AddressUIDs->StateUID)) {
          $conditionwhere['StateUID'] = "( mCustomerPassThru.StateUID = '".$AddressUIDs->StateUID."' ) ";
        }
        else {
          $conditionwhere['StateUID'] = "( mCustomerPassThru.StateUID = 0 OR mCustomerPassThru.StateUID IS NULL ) "; 
        }

        /*County*/
        if (!empty($AddressUIDs->CountyUID)) {
          $conditionwhere['CountyUID'] = "( mCustomerPassThru.CountyUID = '".$AddressUIDs->CountyUID."' ) ";
        }
        else {
          $conditionwhere['CountyUID'] = "( mCustomerPassThru.CountyUID = 0 OR mCustomerPassThru.CountyUID IS NULL ) "; 
        }

        /*City*/
        if (!empty($AddressUIDs->CityUID)) {
            $conditionwhere['CityUID'] = "( mCustomerPassThru.CityUID = '".$AddressUIDs->CityUID."' ) ";
        }
        else {
          $conditionwhere['CityUID'] = "( mCustomerPassThru.CityUID = 0 OR mCustomerPassThru.CityUID IS NULL ) "; 
        }

        /*Zipcode*/
        if (!empty($AddressUIDs->ZipCode)) {
          $conditionwhere['ZipCode'] = "( mCustomerPassThru.ZipCode = '".$AddressUIDs->ZipCode."' ) ";
        }
        else {
          $conditionwhere['ZipCode'] = "( mCustomerPassThru.ZipCode = 0 OR mCustomerPassThru.ZipCode IS NULL ) "; 
        }



        $conditionwhere['CustomerUID_Empty'] = "( mCustomerPassThru.CustomerUID = '0' OR mCustomerPassThru.CustomerUID IS NULL ) "; 
        $conditionwhere['FeeReasonUID_Empty'] = "( mCustomerPassThru.OverrideReasonUID = 0 OR mCustomerPassThru.OverrideReasonUID IS NULL ) ";	
        $conditionwhere['StateUID_Empty'] = "( mCustomerPassThru.StateUID = 0 OR mCustomerPassThru.StateUID IS NULL ) ";	
        $conditionwhere['CountyUID_Empty'] = "( mCustomerPassThru.CountyUID = 0 OR mCustomerPassThru.CountyUID IS NULL ) ";	
        $conditionwhere['CityUID_Empty'] = "( mCustomerPassThru.CityUID = 0 OR mCustomerPassThru.CityUID IS NULL ) "; 
        $conditionwhere['ZipCode_Empty'] = "( mCustomerPassThru.ZipCode = 0 OR mCustomerPassThru.ZipCode IS NULL ) ";	





        $CASEWHEN = [];

        /* All Present*/
        /*Scenario */
        $Scenario = array_filter( array($conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode']) );
        $CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

        /* Fee Present, Customer Present, Geo Volatile*/
        /*Scenario */
        $Scenario = array_filter( array($conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode_Empty']) );
        $CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";


        /*Scenario */
        $Scenario = array_filter( array($conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']) );
        $CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

        /*Scenario */
        $Scenario = array_filter( array($conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']) );
        $CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

        /*Scenario */
        $Scenario = array_filter( array($conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID'], $conditionwhere['StateUID_Empty'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']) );
        $CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";


        /*Scenario Geo All Present, Fee Absenet, Customer Present*/
        $Scenario = array_filter( array($conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode']) );
        $CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

        /* Fee Absent, Customer Present, Geo Volatile */
        /*Scenario */
        $Scenario = array_filter( array($conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID'], $conditionwhere['ZipCode_Empty']) );
        $CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";


        /*Scenario */
        $Scenario = array_filter( array($conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']) );
        $CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

        /*Scenario */
        $Scenario = array_filter( array($conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']) );
        $CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";

        /*Scenario */
        $Scenario = array_filter( array($conditionwhere['CustomerUID'], $conditionwhere['FeeReasonUID_Empty'], $conditionwhere['StateUID_Empty'],  $conditionwhere['CountyUID_Empty'], $conditionwhere['CityUID_Empty'], $conditionwhere['ZipCode_Empty']) );
        $CASEWHEN[] = "(CASE WHEN EXISTS( SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) THEN (SELECT Cost FROM mCustomerPassThru WHERE " . implode(" AND ", $Scenario) . " ) ELSE ";



        $FeeFilter_Query = implode("", $CASEWHEN);



        foreach ($CASEWHEN as $key => $value) {
        	if ($key == 0) {
        		$FeeFilter_Query .= " '' 
        		END) ";

        	}
        	else {
        		$FeeFilter_Query .= "
        		END)";
        	}
        }

        $FeeFilter_Query .= " AS Cost";

        $this->db->select($FeeFilter_Query, false);
        $this->db->from('mCustomerPassThru');
        $this->db->where("CustomerUID", $PropAddress->CustomerUID);

        return $this->db->get()->row();

      }

      return (object)[];
  }
		/**
			*@description Function to Order workflows
			*
			* @param $OrderUID
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return Array 
			* @since 24.2.2020 
			* @version Issue Management 
			*
		*/ 
		function getOrderWorkflows($OrderUID){
			/*Query*/
			$this->db->select('mworkflowmodules.*');
			$this->db->from('tOrders'); 
			$this->db->join('mcustomerworkflowmodules', 'mcustomerworkflowmodules.CustomerUID = tOrders.CustomerUID AND mcustomerworkflowmodules.SubProductUID = tOrders.SubProductUID');
			$this->db->join('mworkflowmodules', 'mcustomerworkflowmodules.WorkflowModuleUID = mworkflowmodules.WorkflowModuleUID');
			$this->db->where('OrderUID', $OrderUID);
			$this->db->order_by('mworkflowmodules.WorkflowModuleUID', 'ASC');
			$CustomerWorkflows =  $this->db->get()->result();

			/*Query*/
			$this->db->select('mworkflowmodules.*');
			$this->db->from('torderoptionalworkflows'); 
			$this->db->join('mworkflowmodules', 'torderoptionalworkflows.WorkflowModuleUID = mworkflowmodules.WorkflowModuleUID');
			$this->db->where('OrderUID', $OrderUID);
			$this->db->order_by('mworkflowmodules.WorkflowModuleUID', 'ASC');
			$OptionalWorkflows = $this->db->get()->result();

			return $this->my_array_unique( array_merge( $CustomerWorkflows, $OptionalWorkflows ) );



		}

		/**
			*@description Function to filter unique elements from object arrays
			*
			* @param ObjectArray
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return Array 
			* @since 24.2.2020 
			* @version Issue Mgmt 
			*
		*/ 


		function my_array_unique($array, $keep_key_assoc = false){
			$duplicate_keys = array();
			$tmp = array();       

			foreach ($array as $key => $val){
        // convert objects to arrays, in_array() does not support objects
				if (is_object($val))
					$val = (array)$val;

				if (!in_array($val, $tmp))
					$tmp[] = $val;
				else
					$duplicate_keys[] = $key;
			}

			foreach ($duplicate_keys as $key)
				unset($array[$key]);

			return $keep_key_assoc ? $array : array_values($array);
		}

		/**
			*@description Function to getOrderProcessUIDByMenuURL
			*
			* @param $MenuURL
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return ProcessUID 
			* @since 25.2.2020 
			* @version Edit Process 
			*
		*/ 
		function getOrderProcessUIDByMenuURL($slug="ProcessURLUID", $MenuURL = "")
		{
			if (empty($MenuURL)) 
			{
				$MenuURL = implode("/", array_filter([$this->uri->segment(1), $this->uri->segment(2)]));
			}
			if (!empty($MenuURL)) {
				return $this->common_model->get_row('mProcessURL', ['URL'=>$MenuURL])->$slug;
			}
			else {
				return 0;
			}
		}

		/**
			*@description Function to getOrderProcessCSS
			*
			* @param $ProcessUID, $StartEnd
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return String 
			* @since 25.2.2020 
			* @version Edit Process 
			*
		*/ 
		function getOrderProcessCSS($ProcessURLUID, $TYPE="CSS"){
			$this->config->load('editprocesscss');
			if (empty($ProcessURLUID) ) {
				$URLSegment = $this->uri->segment(1);
				return $this->config->item('process')[$TYPE][$URLSegment];
			}
			return $this->config->item('process')[$TYPE][$ProcessURLUID];
		}

		/**
			*@description Function to getWorkflowPagesInWorkflowWise
			*
			*
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return Array 
			* @since 04-09-2020 
			* @version Edit Feature 
			*
		*/ 
		function getWorkflowPagesInWorkflowWise()
		{
			
			$this->db->select('WorkflowModuleUID, MenuURL');
			$this->db->from('mMenu'); 
			$this->db->where('WorkflowModuleUID > 0');
			$result = $this->db->get()->result();

			$WorkflowMenu = [];
			foreach ($result as $key => $value) {
				$WorkflowMenu[$value->WorkflowModuleUID][] = $value->MenuURL;
			}
			return $WorkflowMenu;
		}

		/**
		* @description Function to Eventlog count
		*
		* @throws  no exception
		* @author  D.Samuel Prabhu
		* @return  row count as String 
		* @since   26 FEB 2020 
		* @version API
		*/
		// function GetEventlogCount()
		// {
		// 	$this->db->select('*')->from('tOrderEventLog');
		// 	$q = $this->db->get();
		// 	return $q->num_rows();
		// }


		/**
			*@description Function to checkUserHasPermission
			*
			* @param $MenuURL
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return bool 
			* @since 26.2.2020 
			* @version Edit Process 
			*
		*/ 
		function checkUserHasOrderPermission($OrderUID, $WorkflowModuleUID_OR_ModuleName){
			$RoleType = $this->session->userdata('RoleType');


			/* **** Check is Non Workflow Pages **** */
			$WhiteListed_URLs = $this->config->item('OrderEdit_IncludeModules');

			if (in_array( strtolower( $WorkflowModuleUID_OR_ModuleName ), $WhiteListed_URLs)) 
			{
				return true;
			}

			$mroles = $this->common_model->get_row('mroles', ['RoleUID'=>$this->session->userdata('RoleUID')]);


			if ( in_array( $this->RoleType, $this->config->item( 'AdminSupervisor' ) ) ) 
			{

				if (!empty($WorkflowModuleUID_OR_ModuleName) && $mroles->{$this->config->item('WorkflowPermissions')[$WorkflowModuleUID_OR_ModuleName]} == 1) {
					return true;					
				}

			}
			else{


				if (!empty($WorkflowModuleUID_OR_ModuleName) && !empty($OrderUID)) {
					if ( !empty($this->common_model->get_row('torderassignment', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$WorkflowModuleUID_OR_ModuleName, 'AssignedToUserUID'=>$this->loggedid] ) ) ) {
						return true;
						
					}
					else if ( !empty($this->common_model->get_row('torderassignment', ['OrderUID'=>$OrderUID, 'WorkflowModuleUID'=>$this->config->item('WorkflowModuleUID')['Review'], 'AssignedToUserUID'=>$this->loggedid] ) ) ) {
						return true;
						
					}
				}

			}

			return false;
		}

		/**
			*@description Function to checkUserHasProcessStartAvailable
			*
			* @param $OrderUID
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return bool 
			* @since 26.2.2020 
			* @version Edit Process 
			*
		*/ 
		function checkUserHasProcessStartAvailable($OrderUID, $WorkflowModuleUID_OR_ModuleName)
		{
			/*Query*/


			/* **** Check is Non Workflow Pages **** */
			$WhiteListed_URLs = $this->config->item('OrderEdit_IncludeModules');

			/* *** Flag to check its a non workflow module *** */
			$Nonworkflowmodule = false;
			if (in_array( strtolower( $WorkflowModuleUID_OR_ModuleName ), $WhiteListed_URLs)) 
			{
				$Nonworkflowmodule = true;
			}

			if (empty($OrderUID) || (empty($WorkflowModuleUID_OR_ModuleName) && $Nonworkflowmodule == false )) {
				return false;
			}

			$this->db->select('tOrderEditProcess.OrderUID', false);
			$this->db->from('tOrderEditProcess'); 
			$this->db->where('OrderUID', $OrderUID);
			if ($Nonworkflowmodule == false) 
			{
				$this->db->where('WorkflowModuleUID', $WorkflowModuleUID_OR_ModuleName);		
			}
			else
			{
				$this->db->where("ModuleName", $WorkflowModuleUID_OR_ModuleName);
			}
			$this->db->where('UserUID', $this->loggedid);
			$this->db->where('StartDateTime IS NOT NULL');
			$this->db->where('EndDateTime IS NULL');
			if( $this->db->get()->num_rows() == 0){
				return true;
			}
			else {
				return false;
			}
		}

		/**
			*@description Function to checkUserHasProcessStartAvailable
			*
			* @param $OrderUID
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return bool 
			* @since 26.2.2020 
			* @version Edit Process 
			*
		*/ 
		function getRealTimeEnableEditUserName($OrderUID, $WorkflowModuleUID_OR_ModuleName)
		{
			/*Query*/

			/* **** Check is Non Workflow Pages **** */
			$WhiteListed_URLs = $this->config->item('OrderEdit_IncludeModules');

			/* *** Flag to check its a non workflow module *** */
			$Nonworkflowmodule = false;
			if (in_array( strtolower( $WorkflowModuleUID_OR_ModuleName ), $WhiteListed_URLs)) 
			{
				$Nonworkflowmodule = true;
			}

			if (empty($OrderUID) || (empty($WorkflowModuleUID_OR_ModuleName) && $Nonworkflowmodule == false )) {
				return false;
			}

			$this->db->select('tOrderEditProcess.OrderUID, musers.UserName', false);
			$this->db->from('tOrderEditProcess'); 
			$this->db->join('musers', 'musers.UserUID = tOrderEditProcess.UserUID');
			$this->db->where('OrderUID', $OrderUID);
			if ($Nonworkflowmodule == false) 
			{
				$this->db->where('WorkflowModuleUID', $WorkflowModuleUID_OR_ModuleName);		
			}
			else
			{
				$this->db->where("ModuleName", $WorkflowModuleUID_OR_ModuleName);
			}
			$this->db->where("tOrderEditProcess.UserUID != '".$this->loggedid."'");
			$this->db->where('StartDateTime IS NOT NULL');
			$this->db->where('EndDateTime IS NULL');
			$get = $this->db->get();
			if( $get->num_rows() > 0){
				return $get->row()->UserName;
			}
			else {
				return "";
			}
		}

		/**
			*@description Function to checkUserHasProcessEndAvailable
			*
			* @param $OrderUID
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return bool 
			* @since 26.2.2020 
			* @version Edit Process 
			*
		*/ 
		function checkUserHasProcessEndAvailable($OrderUID){
			
			$WorkflowModuleUID = $this->common_model->getOrderProcessUIDByMenuURL('WorkflowModuleUID');
			if (!empty($OrderUID) && empty($WorkflowModuleUID)) {
				return false;
			}

			$this->db->select('1', false);
			$this->db->from('tOrderEditProcess'); 
			$this->db->where('OrderUID', $OrderUID);
			$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
			$this->db->where('UserUID', $this->loggedid);
			$this->db->where('StartDateTime IS NOT NULL');
			$this->db->where('EndDateTime IS NULL');
			if( $this->db->get()->num_rows() > 0){
				return true;
			}
			else {
				return false;
			}
		}


		/**
			*@description Function to checkUserHasProcessStartAvailable
			*
			* @param $OrderUID
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return bool 
			* @since 26.2.2020 
			* @version Edit Process 
			*
		*/ 
		function checkUserOrderEditDisabled($OrderUID){
			/*Query*/
			$this->db->select('tOrderEdit.OrderUID', false);
			$this->db->from('tOrderEdit'); 
			$this->db->where('OrderUID', $OrderUID);
			$this->db->where('UserUID', $this->loggedid);
			$this->db->where('StartDateTime IS NOT NULL');
			$this->db->where('EndDateTime IS NULL');
			if( $this->db->get()->num_rows() == 0){
				return true;
			}
			else {
				return false;
			}
		}


		/**
			*@description Function to updateAutoClosedOrders
			*
			* @param $OrderUID
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return void 
			* @since 21.4.2020 
			* @version Edit Process 
			*
		*/ 
		function updateAutoClosedOrders($OrderUID, $loggedid=0){

			if (empty($OrderUID)) {
				return;
			}

			if (empty($this->loggedid)) 
			{
				$this->loggedid = $loggedid;
			}
			/*Query*/
			$this->db->select('MAX(EndDateTime)', false);
			$this->db->from('tOrderEdit'); 
			$this->db->where('OrderUID', $OrderUID);
			$this->db->where('UserUID', $this->loggedid);
			$this->db->where('StartDateTime IS NOT NULL');
			$this->db->where('EndDateTime IS NOT NULL');

			/*Fetch genereted child query as string rather than execute */
			$subquery = $this->db->_compile_select();
			$this->db->_reset_select();

			$this->db->select('OrderEditUID, EndDateTime', false);
			$this->db->from('tOrderEdit'); 
			$this->db->where('OrderUID', $OrderUID);
			$this->db->where('UserUID', $this->loggedid);
			$this->db->where('StartDateTime IS NOT NULL');			
			$this->db->where('EndDateTime IS NOT NULL');			
			$this->db->where('EndDateTime = (' . $subquery .')');			

			$result = $this->db->get()->row();

			if (!empty($result) && !empty($result->EndDateTime) && date('Y-m-d H:i:s', strtotime($result->EndDateTime ) ) >  date( 'Y-m-d H:i:s', strtotime("now") - 2 ) ) {

				$this->db->where("OrderEditUID", $result->OrderEditUID);
				$this->db->update("tOrderEdit", ["EndDateTime"=>NULL]);


				$UpdateDetails["EndDateTime"] = NULL;
				$UpdateDetails["CompleteType"] = NULL;
				$this->db->where("CompleteType", "Auto");
				$this->db->where("OrderUID", $OrderUID);
				$this->db->where("UserUID", $this->loggedid);
				$this->db->where("StartDateTime IS NOT NULL AND EndDateTime IS NOT NULL");
				$this->db->where("EndDateTime BETWEEN  DATE_SUB('".$result->EndDateTime."', INTERVAL 2 SECOND) AND DATE_ADD('".$result->EndDateTime."', INTERVAL 1 SECOND) ");
				$this->db->update("tOrderEditProcess", $UpdateDetails);
	
			}


				$DeleteData = array(
					'UserUID' => $this->loggedid,
					'OrderUID' => $OrderUID,
					'ModuleName' => 'EditOrder',
					'Feature' => 'Disable');
				$this->db->where($DeleteData);
				$this->db->where('DateTime > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 2 SECOND)');
				$this->db->delete('taudittrail');
		}


		/**
			*@description Function to updateAutoClosedOrders
			*
			* @param $OrderUID
			* 
			* @throws no exception
			* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
			* @return void 
			* @since 21.4.2020 
			* @version Edit Process 
			*
		*/ 
		function reOpenClosedEditOrders($OrderUID, $WorkflowModuleUID = 0)
		{

			if (empty($OrderUID)) {
				return;
			}

			/*Query*/
			$this->db->select('MAX(EndDateTime)', false);
			$this->db->from('tOrderEdit'); 
			$this->db->where('OrderUID', $OrderUID);
			$this->db->where('UserUID', $this->loggedid);
			$this->db->where('StartDateTime IS NOT NULL');
			$this->db->where('EndDateTime IS NOT NULL');

			/*Fetch genereted child query as string rather than execute */
			$subquery = $this->db->_compile_select();
			$this->db->_reset_select();

			$this->db->select('OrderEditUID, EndDateTime', false);
			$this->db->from('tOrderEdit'); 
			$this->db->where('OrderUID', $OrderUID);
			$this->db->where('UserUID', $this->loggedid);
			$this->db->where('StartDateTime IS NOT NULL');			
			$this->db->where('EndDateTime IS NOT NULL');			
			$this->db->where('EndDateTime = (' . $subquery .')');			

			$result = $this->db->get()->row();

			if ( !empty($result) ) {

				$this->db->where("OrderEditUID", $result->OrderEditUID);
				$this->db->update("tOrderEdit", ["EndDateTime"=>NULL]);

				$isOrderUpdated = $this->db->affected_rows();

				$UpdateDetails["EndDateTime"] = NULL;
				$UpdateDetails["CompleteType"] = NULL;
				$this->db->where("CompleteType", "Auto");
				$this->db->where("OrderUID", $OrderUID);
		
				/*Check */
				if (!empty($WorkflowModuleUID) )
				{
					if ( is_numeric($WorkflowModuleUID)) 
					{
						$this->db->where("WorkflowModuleUID", $WorkflowModuleUID);
					}
					else
					{
						$this->db->where("ModuleName", $WorkflowModuleUID);
					}

				}

				$this->db->where("UserUID", $this->loggedid);
				$this->db->where("StartDateTime IS NOT NULL AND EndDateTime IS NOT NULL");
				$this->db->where("EndDateTime BETWEEN  DATE_SUB('".$result->EndDateTime."', INTERVAL 2 SECOND) AND DATE_ADD('".$result->EndDateTime."', INTERVAL 1 SECOND) ");
				$this->db->update("tOrderEditProcess", $UpdateDetails);

				$isProcessUpdated = $this->db->affected_rows();

				$DeleteData = array(
					'UserUID' => $this->loggedid,
					'OrderUID' => $OrderUID,
					'ModuleName' => 'EditOrder',
					'Feature' => 'Disable');
				$this->db->where($DeleteData);
				$this->db->where('DateTime > DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 5 SECOND)');
				$this->db->delete('taudittrail');

				if ($isOrderUpdated && $isProcessUpdated) 
				{
					return true;
				}
	
			}
			return false;

		}

	/**
		*@description Function to saveOrderEditProcessLogs
		*
		* @param $OrderUID, $ProcessUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return bool 
		* @since 21.4.2020 
		* @version Edit Feature 
		*
	*/ 
	function saveOrderEditProcessLogs($OrderUID, $ProcessUID, $SkipTransaction = false){
		
		if (empty($OrderUID) || empty($ProcessUID)) {
			return false;
		}

		$mProcesses = $this->common_model->get_row('mProcesses', ['ProcessUID'=>$ProcessUID]);

		if (empty($mProcesses)) {
			return false;
		}

		$WorkflowModuleUID = $mProcesses->WorkflowModuleUID;

		$this->db->select('StartDateTime', false);
		$this->db->from("tOrderEditProcess");
		$this->db->where("OrderUID", $OrderUID);
		$this->db->where("WorkflowModuleUID", $WorkflowModuleUID);
		$this->db->where("UserUID", $this->loggedid);
		$this->db->where("StartDateTime IS NOT NULL AND EndDateTime IS NULL");
		$result = $this->db->get()->row();

		if (empty($result)) {
			return false;
		}

		$Fields['OrderUID'] 			=	$OrderUID;
		$Fields['WorkflowModuleUID']	=	$WorkflowModuleUID;
		$Fields['ProcessUID'] 			=	$ProcessUID;
		$Fields['UserUID'] 				=	$this->loggedid;
		$Fields['StartDateTime'] 		=	date('Y-m-d H:i:s', strtotime($result->StartDateTime));
		$Fields['EndDateTime']	 		=	date('Y-m-d H:i:s');

		
		if ($SkipTransaction == false) {
			/*Transaction Begin*/
			$this->db->trans_begin();			
		}
		
		
		$this->db->insert('tOrderEditProcessLog', $Fields);
		
		
		if ($SkipTransaction == false) {
			/*verify is valid transaction*/
			if ($this->db->trans_status()) {
				/*commit transaction*/
				$this->db->trans_commit();
				return true;
			}
			else{
				/*rollback transaction*/
				$this->db->trans_rollback();
				return false;
			}
			
		}

		return true;

	}

	/**
	* Function -  Function to get workflow priorization for listing orders
	*
	* @param post filter workflow for workflowuid
	* 
	* @throws exception snippet is not valid or present
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return order by query -- generated
	* @since Thursday 27 February 2020
	*
	*/	

	function get_listworkflowprioritization($post)
	{
		$this->db->select('mWorkflowPrioritization.WorkflowUID,mWorkflowTypePrioritization.CustomerUID,mWorkflowTypePrioritization.ProductUID,mWorkflowTypePrioritization.SubProductUID,mWorkflowTypePrioritization.Priority,mStates.StateCode,mCounties.CountyName,mCities.CityName,mWorkflowTypePrioritization.Zipcode');
		$this->db->from('mWorkflowTypePrioritization'); 
		$this->db->join('mWorkflowPrioritization', 'mWorkflowPrioritization.WorkflowPrioritizationUID = mWorkflowTypePrioritization.WorkflowPrioirtizationUID');
		$this->db->join('mStates','mStates.StateUID = mWorkflowTypePrioritization.State','left');
		$this->db->join('mCounties','mCounties.CountyUID = mWorkflowTypePrioritization.County','left');
		$this->db->join('mCities','mCities.CityUID = mWorkflowTypePrioritization.City','left');

		$this->db->where('mWorkflowPrioritization.PrioritizationType', 'Order');
		$this->db->where('mWorkflowPrioritization.IsActive', 1);
		$this->db->where('mWorkflowPrioritization.OverrideRush', 1); //@Desc Override Rush @Author Jainulabdeen @Updated July 10 2020
		$this->db->order_by('mWorkflowPrioritization.Rank', 'ASC');
		$this->db->order_by('mWorkflowTypePrioritization.Rank', 'ASC');
		$Prioritizationrows = $this->db->get()->result();
		//generate where
		return $this->generate_workflowprioritization_orderby($Prioritizationrows);
	
	}

	/**
	* Function -  Function to get override workflow priorization for listing orders
	*
	* @param post filter workflow for workflowuid
	* 
	* @throws exception snippet is not valid or present
	* @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
	* @return order by query -- generated
	* @since July 10 2020
	*
	*/	

	function get_listoverrideworkflowprioritization($post)
	{
		$this->db->select('mWorkflowPrioritization.WorkflowUID,mWorkflowTypePrioritization.CustomerUID,mWorkflowTypePrioritization.ProductUID,mWorkflowTypePrioritization.SubProductUID,mWorkflowTypePrioritization.Priority,mStates.StateCode,mCounties.CountyName,mCities.CityName,mWorkflowTypePrioritization.Zipcode');
		$this->db->from('mWorkflowTypePrioritization'); 
		$this->db->join('mWorkflowPrioritization', 'mWorkflowPrioritization.WorkflowPrioritizationUID = mWorkflowTypePrioritization.WorkflowPrioirtizationUID');
		$this->db->join('mStates','mStates.StateUID = mWorkflowTypePrioritization.State','left');
		$this->db->join('mCounties','mCounties.CountyUID = mWorkflowTypePrioritization.County','left');
		$this->db->join('mCities','mCities.CityUID = mWorkflowTypePrioritization.City','left');

		$this->db->where('mWorkflowPrioritization.PrioritizationType', 'Order');
		$this->db->where('mWorkflowPrioritization.IsActive', 1);
		$this->db->where('mWorkflowPrioritization.OverrideRush', 0); 
		$this->db->order_by('mWorkflowPrioritization.Rank', 'ASC');
		$this->db->order_by('mWorkflowTypePrioritization.Rank', 'ASC');
		$Prioritizationrows = $this->db->get()->result();
		//generate where
		return $this->generate_workflowprioritization_orderby($Prioritizationrows);
	
	}

	function getnextorderworkflowprioritization($WorkflowUID)
	{
		$this->db->select('mWorkflowPrioritization.WorkflowUID,mWorkflowTypePrioritization.CustomerUID,mWorkflowTypePrioritization.ProductUID,mWorkflowTypePrioritization.SubProductUID,mWorkflowTypePrioritization.Priority,mStates.StateCode,mCounties.CountyName,mCities.CityName,mWorkflowTypePrioritization.Zipcode');
		$this->db->from('mWorkflowTypePrioritization'); 
		$this->db->join('mWorkflowPrioritization', 'mWorkflowPrioritization.WorkflowPrioritizationUID = mWorkflowTypePrioritization.WorkflowPrioirtizationUID');
		$this->db->join('mStates','mStates.StateUID = mWorkflowTypePrioritization.State','left');
		$this->db->join('mCounties','mCounties.CountyUID = mWorkflowTypePrioritization.County','left');
		$this->db->join('mCities','mCities.CityUID = mWorkflowTypePrioritization.City','left');

		$this->db->where('mWorkflowPrioritization.PrioritizationType', 'Order');
		$this->db->where('mWorkflowPrioritization.WorkflowUID', $WorkflowUID);
		$this->db->where('mWorkflowPrioritization.IsActive', 1);
		$this->db->order_by('mWorkflowPrioritization.Rank', 'ASC');
		$this->db->order_by('mWorkflowTypePrioritization.Rank', 'ASC');
		$Prioritizationrows = $this->db->get()->result();
		//generate where
		return $this->generate_workflowprioritization_orderby($Prioritizationrows);
	
	}

	function generate_workflowprioritization_orderby($Prioritizationrows)
	{
		$Prioritization_where = [];
		if(!empty($Prioritizationrows)) {
			foreach ($Prioritizationrows as $key => $Prioritizationrow) {
				/*workflow statement*/
				$Loop_where = [];
				$workflow = " AND FIND_IN_SET('".$Prioritizationrow->WorkflowUID."',(SELECT GROUP_CONCAT(DISTINCT WorkflowModuleUID)	 FROM mcustomerworkflowmodules WHERE `tOrders`.`CustomerUID` = `mcustomerworkflowmodules`.`CustomerUID` AND mProducts.ProductUID = mcustomerworkflowmodules.ProductUID AND tOrders.SubProductUID = mcustomerworkflowmodules.SubProductUID))";

				if(empty($Prioritizationrow->WorkflowUID))
				{ 
					$workflow= ""; 
				} /*@Desc workflow not assigned @Author Jainulabdeen @Updated on June 9 2020*/
				if(isset($Prioritizationrow->CustomerUID) && !empty($Prioritizationrow->CustomerUID) 
					&& isset($Prioritizationrow->ProductUID) && !empty($Prioritizationrow->ProductUID) 
					&& isset($Prioritizationrow->SubProductUID) && !empty($Prioritizationrow->SubProductUID) 
					&& isset($Prioritizationrow->Priority) && !empty($Prioritizationrow->Priority) 
					&& isset($Prioritizationrow->StateCode) && !empty($Prioritizationrow->StateCode)
					&& isset($Prioritizationrow->CountyName) && !empty($Prioritizationrow->CountyName) 
					&& isset($Prioritizationrow->CityName) && !empty($Prioritizationrow->CityName) 
					&& isset($Prioritizationrow->Zipcode) && !empty($Prioritizationrow->Zipcode)) 
				{


					$Loop_where[] = 'tOrders.CustomerUID='.$Prioritizationrow->CustomerUID.' AND mSubProducts.ProductUID = '.$Prioritizationrow->ProductUID.' AND tOrders.SubProductUID = '.$Prioritizationrow->SubProductUID.' AND tOrders.PriorityUID = '.$Prioritizationrow->Priority.' AND tOrders.PropertyStateCode = "'.$Prioritizationrow->StateCode.'" AND tOrders.PropertyCountyName = "'.$Prioritizationrow->CountyName.'" AND tOrders.PropertyCityName = "'.$Prioritizationrow->CityName.'" AND tOrders.PropertyZipcode = '.$Prioritizationrow->Zipcode.' '.$workflow;	
				}
				elseif (isset($Prioritizationrow->CustomerUID) && !empty($Prioritizationrow->CustomerUID) 
					&& isset($Prioritizationrow->ProductUID) && !empty($Prioritizationrow->ProductUID) 
					&& isset($Prioritizationrow->SubProductUID) && !empty($Prioritizationrow->SubProductUID) 
					&& isset($Prioritizationrow->Priority) && !empty($Prioritizationrow->Priority) 
					&& isset($Prioritizationrow->StateCode) && !empty($Prioritizationrow->StateCode)
					&& isset($Prioritizationrow->CountyName) && !empty($Prioritizationrow->CountyName) 
					&& isset($Prioritizationrow->CityName) && !empty($Prioritizationrow->CityName) 
				)
				{
					$Loop_where[] = 'tOrders.CustomerUID='.$Prioritizationrow->CustomerUID.' AND mSubProducts.ProductUID = '.$Prioritizationrow->ProductUID.' AND tOrders.SubProductUID = '.$Prioritizationrow->SubProductUID.' AND tOrders.PriorityUID = '.$Prioritizationrow->Priority.' AND tOrders.PropertyStateCode = "'.$Prioritizationrow->StateCode.'" AND tOrders.PropertyCountyName = "'.$Prioritizationrow->CountyName.'" AND tOrders.PropertyCityName = "'.$Prioritizationrow->CityName.'" '.$workflow;	
				}
				elseif (isset($Prioritizationrow->CustomerUID) && !empty($Prioritizationrow->CustomerUID) 
					&& isset($Prioritizationrow->ProductUID) && !empty($Prioritizationrow->ProductUID) 
					&& isset($Prioritizationrow->SubProductUID) && !empty($Prioritizationrow->SubProductUID) 
					&& isset($Prioritizationrow->Priority) && !empty($Prioritizationrow->Priority) 
					&& isset($Prioritizationrow->StateCode) && !empty($Prioritizationrow->StateCode)
					&& isset($Prioritizationrow->CountyName) && !empty($Prioritizationrow->CountyName) 
				)
				{
					$Loop_where[] = 'tOrders.CustomerUID='.$Prioritizationrow->CustomerUID.' AND mSubProducts.ProductUID = '.$Prioritizationrow->ProductUID.' AND tOrders.SubProductUID = '.$Prioritizationrow->SubProductUID.' AND tOrders.PriorityUID = '.$Prioritizationrow->Priority.' AND tOrders.PropertyStateCode = "'.$Prioritizationrow->StateCode.'" AND tOrders.PropertyCountyName = "'.$Prioritizationrow->CountyName.'" '.$workflow;	
				}
				elseif (isset($Prioritizationrow->CustomerUID) && !empty($Prioritizationrow->CustomerUID) 
					&& isset($Prioritizationrow->ProductUID) && !empty($Prioritizationrow->ProductUID) 
					&& isset($Prioritizationrow->SubProductUID) && !empty($Prioritizationrow->SubProductUID) 
					&& isset($Prioritizationrow->Priority) && !empty($Prioritizationrow->Priority) 
					&& isset($Prioritizationrow->StateCode) && !empty($Prioritizationrow->StateCode)
				)
				{
					$Loop_where[] = 'tOrders.CustomerUID='.$Prioritizationrow->CustomerUID.' AND mSubProducts.ProductUID = '.$Prioritizationrow->ProductUID.' AND tOrders.SubProductUID = '.$Prioritizationrow->SubProductUID.' AND tOrders.PriorityUID = '.$Prioritizationrow->Priority.' AND tOrders.PropertyStateCode = "'.$Prioritizationrow->StateCode.'" '.$workflow;	
				}
				elseif (isset($Prioritizationrow->CustomerUID) && !empty($Prioritizationrow->CustomerUID) 
					&& isset($Prioritizationrow->ProductUID) && !empty($Prioritizationrow->ProductUID) 
					&& isset($Prioritizationrow->SubProductUID) && !empty($Prioritizationrow->SubProductUID) 
					&& isset($Prioritizationrow->Priority) && !empty($Prioritizationrow->Priority) 
				)
				{
					$Loop_where[] = 'tOrders.CustomerUID='.$Prioritizationrow->CustomerUID.' AND mSubProducts.ProductUID = '.$Prioritizationrow->ProductUID.' AND tOrders.SubProductUID = '.$Prioritizationrow->SubProductUID.' AND tOrders.PriorityUID = '.$Prioritizationrow->Priority.' '.$workflow;	
				}
				elseif (isset($Prioritizationrow->CustomerUID) && !empty($Prioritizationrow->CustomerUID) 
					&& isset($Prioritizationrow->ProductUID) && !empty($Prioritizationrow->ProductUID) 
					&& isset($Prioritizationrow->SubProductUID) && !empty($Prioritizationrow->SubProductUID) 
				)
				{

					$Loop_where[] = 'tOrders.CustomerUID='.$Prioritizationrow->CustomerUID.' AND mSubProducts.ProductUID = '.$Prioritizationrow->ProductUID.' AND tOrders.SubProductUID = '.$Prioritizationrow->SubProductUID.' '.$workflow;	
				}elseif (isset($Prioritizationrow->CustomerUID) && !empty($Prioritizationrow->CustomerUID) 
					&& isset($Prioritizationrow->ProductUID) && !empty($Prioritizationrow->ProductUID) 
				)
				{
					$Loop_where[] = 'tOrders.CustomerUID='.$Prioritizationrow->CustomerUID.' AND mSubProducts.ProductUID = '.$Prioritizationrow->ProductUID.' '.$workflow;	
				}elseif (isset($Prioritizationrow->CustomerUID) && !empty($Prioritizationrow->CustomerUID) && isset($Prioritizationrow->Priority) && !empty($Prioritizationrow->Priority) )
				{
					$Loop_where[] = 'tOrders.CustomerUID='.$Prioritizationrow->CustomerUID.' AND tOrders.PriorityUID = '.$Prioritizationrow->Priority.' '.$workflow;	
				}elseif (isset($Prioritizationrow->CustomerUID) && !empty($Prioritizationrow->CustomerUID))
				{
					$Loop_where[] = 'tOrders.CustomerUID='.$Prioritizationrow->CustomerUID.' '.$workflow;	
				}
				elseif (isset($Prioritizationrow->ProductUID) && !empty($Prioritizationrow->ProductUID))
				{
					$Loop_where[] = 'mProducts.ProductUID='.$Prioritizationrow->ProductUID.' '.$workflow;	
				}
				elseif (isset($Prioritizationrow->SubProductUID) && !empty($Prioritizationrow->SubProductUID))
				{
					$Loop_where[] = 'tOrders.SubProductUID='.$Prioritizationrow->SubProductUID.' '.$workflow;	
				}
				elseif (isset($Prioritizationrow->PriorityUID) && !empty($Prioritizationrow->PriorityUID))
				{
					$Loop_where[] = 'tOrders.PriorityUID='.$Prioritizationrow->PriorityUID.' '.$workflow;	
				}
				elseif (isset($Prioritizationrow->StateCode) && !empty($Prioritizationrow->StateCode))
				{
					$Loop_where[] = 'tOrders.PropertyStateCode="'.$Prioritizationrow->StateCode.'" '.$workflow;	
				}
				elseif (isset($Prioritizationrow->CountyName) && !empty($Prioritizationrow->CountyName))
				{
					$Loop_where[] = 'tOrders.PropertyCountyName="'.$Prioritizationrow->CountyName.'" '.$workflow;	
				}
				elseif (isset($Prioritizationrow->CityName) && !empty($Prioritizationrow->CityName))
				{
					$Loop_where[] = 'tOrders.PropertyCityName="'.$Prioritizationrow->CityName.'" '.$workflow;	
				}
				elseif (isset($Prioritizationrow->Zipcode) && !empty($Prioritizationrow->Zipcode))
				{
					$Loop_where[] = 'tOrders.PropertyZipcode="'.$Prioritizationrow->Zipcode.'" '.$workflow;	
				}


				if(!empty($Loop_where)) {
					$Prioritization_where[] = '( CASE WHEN ( ' . implode(" AND ", $Loop_where) . '  ) THEN 0 ELSE 1  END ) ASC';
				}
			}
		}
		
		if(!empty($Prioritization_where)) {
			return implode(" , ",$Prioritization_where);
		}

	}



	/**
	* Function -  Function to get workflow priorization for listing orders
	*
	* @param post filter workflow for workflowuid
	* 
	* @throws exception snippet is not valid or present
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return order by query -- generated
	* @since Thursday 27 February 2020
	*
	*/	

	function getVendorPrioritization($post, $Type = "OrderBy")
	{
		$this->db->select('mWorkflowPrioritization.WorkflowUID,mWorkflowTypePrioritization.CustomerUID,mWorkflowTypePrioritization.ProductUID,mWorkflowTypePrioritization.SubProductUID,mWorkflowTypePrioritization.Priority,mStates.StateCode,mCounties.CountyName,mCities.CityName,mWorkflowTypePrioritization.Zipcode,mWorkflowTypePrioritization.State,mWorkflowTypePrioritization.County,mWorkflowTypePrioritization.City,mWorkflowTypePrioritization.VendorUID,mWorkflowPrioritization.PrioritizationName,mWorkflowPrioritization.AssignmentTypeUID, mWorkflowPrioritization.PrioritizationName, mWorkflowTypePrioritization.PrioritizationUID');
		$this->db->from('mWorkflowTypePrioritization'); 
		$this->db->join('mWorkflowPrioritization', 'mWorkflowPrioritization.WorkflowPrioritizationUID = mWorkflowTypePrioritization.WorkflowPrioirtizationUID');
		$this->db->join('mStates','mStates.StateUID = mWorkflowTypePrioritization.State','left');
		$this->db->join('mCounties','mCounties.CountyUID = mWorkflowTypePrioritization.County','left');
		$this->db->join('mCities','mCities.CityUID = mWorkflowTypePrioritization.City','left');

		$this->db->where('mWorkflowPrioritization.PrioritizationType', 'Vendor');
		$this->db->where('mWorkflowPrioritization.IsActive', 1);
		$this->db->order_by('mWorkflowPrioritization.Rank', 'ASC');
		$this->db->order_by('mWorkflowTypePrioritization.Rank', 'ASC');
		$Prioritizationrows = $this->db->get()->result();
		//generate where
		if ($Type == "SELECT") {
			return $this->generate_vendorprioritization_select($Prioritizationrows, $post);
		}
		else if ($Type == "OrderBy"){
			return $this->generate_vendorprioritization_orderby($Prioritizationrows, $post);			
		}

	}


	function generate_vendorprioritization_orderby($Prioritizationrows, $post)
	{
		$Prioritization_where = [];
		if(!empty($Prioritizationrows)) {
			foreach ($Prioritizationrows as $key => $Prioritizationrow) {

				$WHERE = [];
				/*Vendor Check*/
				$WHERE[] = "AbstractorUID = '" . $Prioritizationrow->VendorUID . "'";
				/*Assignment Type Check*/
				if(!empty($Prioritizationrow->AssignmentTypeUID)){ //@Desc not empty assignment type @Author Jainulabdeen @Updated on June 9 2020
				$WHERE[] = "OrderTypeUID = '" . $Prioritizationrow->AssignmentTypeUID . "'";
				}

				if( !empty($post['CustomerUID']) && !empty($Prioritizationrow->CustomerUID) ) 
				{

					$WHERE[] = $post['CustomerUID'] . ' = '.$Prioritizationrow->CustomerUID;
				}
				

				if( !empty($post['ProductUID']) && !empty($Prioritizationrow->ProductUID) ) 
				{

					$WHERE[] = $post['ProductUID'] . ' = '.$Prioritizationrow->ProductUID;
				}
				

				if( !empty($post['SubProductUID']) && !empty($Prioritizationrow->SubProductUID) ) 
				{

					$WHERE[] = $post['SubProductUID'] . ' = '.$Prioritizationrow->SubProductUID;
				}
				

				if( !empty($post['PriorityUID']) && !empty($Prioritizationrow->PriorityUID) ) 
				{

					$WHERE[] = $post['PriorityUID'] . ' = '.$Prioritizationrow->Priority;
				}
				
				if( !empty($post['CityUID']) && !empty($Prioritizationrow->City) ) 
				{

					$WHERE[] = "'". $Prioritizationrow->City . "' IN (" . $post['CityUID'] . ")";
				}
				

				if( !empty($post['StateUID']) && !empty($Prioritizationrow->State) ) 
				{

					$WHERE[] = $post['StateUID'].'='.$Prioritizationrow->State;
				}
				

				if( !empty($post['CountyUID']) && !empty($Prioritizationrow->County) ) 
				{

					$WHERE[] = $post['CountyUID'].'='.$Prioritizationrow->County;
				}
				


				if( !empty($post['ZipCode']) && !empty($Prioritizationrow->Zipcode) ) 
				{

					$WHERE[] = $post['ZipCode'].'='.$Prioritizationrow->Zipcode;
				}
				


				if (!empty($WHERE)) {
					$Prioritization_where[] = '( CASE WHEN ( ' . implode(" AND ", $WHERE) . ' ) THEN 0 ELSE 1  END ) ASC';
				}

				unset($WHERE);

			}
		}
		if(!empty($Prioritization_where)) {
			return implode(" , ",$Prioritization_where);
		}

	}

	function generate_vendorprioritization_select($Prioritizationrows, $post)
	{
		$Prioritization_where = [];
		if(!empty($Prioritizationrows)) {
			foreach ($Prioritizationrows as $key => $Prioritizationrow) {

				$WHERE = [];
				/*Vendor Check*/
				$WHERE[] = "AbstractorUID = '" . $Prioritizationrow->VendorUID . "'";
				/*Assignment Type Check*/
				$WHERE[] = "OrderTypeUID = '" . $Prioritizationrow->AssignmentTypeUID . "'";

				if( !empty($post['CustomerUID']) && !empty($Prioritizationrow->CustomerUID) ) 
				{

					$WHERE[] = $post['CustomerUID'] . ' = '.$Prioritizationrow->CustomerUID;
				}
				

				if( !empty($post['ProductUID']) && !empty($Prioritizationrow->ProductUID) ) 
				{

					$WHERE[] = $post['ProductUID'] . ' = '.$Prioritizationrow->ProductUID;
				}
				

				if( !empty($post['SubProductUID']) && !empty($Prioritizationrow->SubProductUID) ) 
				{

					$WHERE[] = $post['SubProductUID'] . ' = '.$Prioritizationrow->SubProductUID;
				}
				

				if( !empty($post['PriorityUID']) && !empty($Prioritizationrow->PriorityUID) ) 
				{

					$WHERE[] = $post['PriorityUID'] . ' = '.$Prioritizationrow->Priority;
				}
				
				if( !empty($post['CityUID']) && !empty($Prioritizationrow->City) ) 
				{

					$WHERE[] = "'". $Prioritizationrow->City . "' IN (" . $post['CityUID'] . ")";
				}
				

				if( !empty($post['StateUID']) && !empty($Prioritizationrow->State) ) 
				{

					$WHERE[] = $post['StateUID'].'='.$Prioritizationrow->State;
				}
				

				if( !empty($post['CountyUID']) && !empty($Prioritizationrow->County) ) 
				{

					$WHERE[] = $post['CountyUID'].'='.$Prioritizationrow->County;
				}
				


				if( !empty($post['ZipCode']) && !empty($Prioritizationrow->Zipcode) ) 
				{

					$WHERE[] = $post['ZipCode'].'='.$Prioritizationrow->Zipcode;
				}
				


				if (!empty($WHERE)) {
					$Prioritization_where[] = '( CASE WHEN ( ' . implode(" AND ", $WHERE) . ' ) THEN "'.$Prioritizationrow->PrioritizationUID.'" ELSE ';
				}

				unset($WHERE);

			}
		}
		if(!empty($Prioritization_where)) {

			$CASE_WHEN = implode("", $Prioritization_where);

			foreach ($Prioritization_where as $key => $value) {
				if ($key == 0) {
					$CASE_WHEN .= " '' 
					END) ";

				}
				else {
					$CASE_WHEN .= "
					END)";
				}
			}

			return $CASE_WHEN;
		}
		return "";

	}

	function GetOrdersourceNameByOrderUID($OrderUID){
		$tOrders = $this->db->get_where('tOrders', array('OrderUID'=>$OrderUID))->row();
		$OrderSourceUID = $tOrders->OrderSourceUID;
		$OrderSourceName = '';
		if($OrderSourceUID){
			$mApiTitlePlatform = $this->db->get_where('mApiTitlePlatform', array('ApiTitlePlatformUID'=>$OrderSourceUID))->row();
			$OrderSourceName = $mApiTitlePlatform->OrderSourceName;
		}
		return $OrderSourceName;
	}

	/**
	* Function -  mvendor types
	*
	* @param  -
	* 
	* @throws no exception
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return vendor type result
	* @since Wednesday 04 March 2020
	*
	*/
	function get_mRoleCategory(){

		return $this->db->where(array("Active"=>1))->order_by('RoleCategoryName','ASC')->get('mRoleCategory')->result();
	}

	/**
	* Function - vendor tracker permission for role category
	*
	* @param  -
	* 
	* @throws no exception
	* @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
	* @return vendor type result
	* @since 15 July 2020
	*
	*/
	function get_mRoleCategoryPermission(){

		$this->db->select('mRoleCategory.RoleCategoryUID,mRoleCategory.RoleCategoryName');
		$this->db->from('mRoleCategory');
		$this->db->join('mroles','FIND_IN_SET(mRoleCategory.RoleCategoryUID, mroles.RoleCategoryUID)','LEFT',FALSE);
		$this->db->where('mRoleCategory.Active',1);
		$this->db->where('mroles.RoleUID',$this->RoleUID);
		$this->db->order_by('mRoleCategory.RoleCategoryUID','ASC');
		$this->db->group_by('mRoleCategory.RoleCategoryUID');
		return $this->db->get()->result();
	}

	/*
	* @description Get SubProducts By Product
	* @param 
	* @throws no exception
	* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
	* @return nothing 
	* @since  
	* @version  
	*/ 
	function getSubProductsByProductUID($ProductUID){
		$this->db->select('*');
		$this->db->from('mSubProducts');
		$this->db->where('ProductUID',$ProductUID);
		return $this->db->get()->result();
	}

	/**
	* Function -  mvendor types
	*
	* @param  - OrderTypeUID required
	* 
	* @throws no exception
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return vendor type result
	* @since Saturday 07 March 2020
	*
	*/

	function get_mRoleCategorybyOrderType($OrderTypeUID) {
		if(empty($OrderTypeUID)) return [];
		return $this->db->query("SELECT mRoleCategory.RoleCategoryUID,RoleCategoryName, (CASE WHEN (SELECT ETA FROM mOrderTypeETA o WHERE o.OrderTypeUID = mOrderTypes.OrderTypeUID AND o.RoleCategoryUID = mRoleCategory.RoleCategoryUID AND ( ETA != 0 OR ETA IS NOT NULL ) ) THEN 1 ELSE 0 END ) AS VendorETA, AbstractorETA,AttorneyETA,NotaryETA FROM mOrderTypes LEFT JOIN mRoleCategory ON FIND_IN_SET(mRoleCategory.RoleCategoryUID, mOrderTypes.RoleCategoryUID) WHERE mRoleCategory.Active = 1 AND mOrderTypes.OrderTypeUID = {$OrderTypeUID}  GROUP BY mRoleCategory.RoleCategoryUID ORDER BY RoleCategoryName ASC")->result();
	}

	/**
	* Function -  mvendor types
	*
	* @param  - OrderTypeUID RoleCategoryUID required
	* 
	* @throws no exception
	* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
	* @return vendor type result
	* @since 20 03 2020
	*
	*/

	function get_ordertype_ETA($RoleCategoryUID,$OrderTypeUID){
		if(empty($OrderTypeUID) || empty($RoleCategoryUID)) return [];
		return $this->db->query("SELECT ETA FROM mOrderTypeETA WHERE OrderTypeUID = {$OrderTypeUID} AND RoleCategoryUID = {$RoleCategoryUID}")->row();
	}

	
	/**
	*Function get rolecategoryUID using name
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Tuesday 24 March 2020
	*/
	function getRoleCategorysbyName($RoleCategoryName,$Multiple='')
	{
		if(!empty($Multiple))
		{
			$this->db->select('GROUP_CONCAT(RoleCategoryUID) AS RoleCategoryUID');
			$this->db->where('RoleCategoryName IN ('.$RoleCategoryName.')',NULL,FALSE);
		}	else {
			$this->db->where('RoleCategoryName',$RoleCategoryName);
		}
		$this->db->where('mRoleCategory.Active',1);
		return $this->db->get('mRoleCategory')->result();
	}

	/**
		*@description Function to getOrderDetailsByOrderUID
		*
		* @param $OrderUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return Object 
		* @since 27.3.2020 
		* @version Attachments Email Setup 
		*
	*/ 
	function getOrderDetailsByOrderUID($OrderUID){
		if($OrderUID){

			$this->db->select ( 'tOrders.*, mCustomers.CustomerName, mCustomers.CustomerNumber, mProducts.ProductName, mProducts.ProductUID, mSubProducts.SubProductName, mSubProducts.SubProductCode, mOrderTypes.OrderTypeName, mOrderPriority.PriorityName, mOrderStatus.StatusName, GROUP_CONCAT( PRName SEPARATOR "\\\" ) AS BorrowerName, mProducts.IsClosingProduct ');
			$this->db->from ( 'tOrders' );
			$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
			$this->db->join ( 'mSubProducts', 'tOrders.SubProductUID = mSubProducts.SubProductUID' , 'left' );
			$this->db->join ( 'mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID' , 'left' );
			$this->db->join ( 'mTemplates', 'tOrders.TemplateUID = mTemplates.TemplateUID' , 'left' );
			$this->db->join ( 'mOrderTypes', 'mOrderTypes.OrderTypeUID = tOrders.OrderTypeUID' , 'left' );
			$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
			$this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
			$this->db->join ( 'tOrderPropertyRoles', 'tOrders.OrderUID = tOrderPropertyRoles.OrderUID' , 'left' );
			$this->db->where ('tOrders.OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->row();
		}
		return (Object)[];
	}
	/**
	*@description Function to save new vendor contact
	*
	* @param $data
	* 
	* @throws no exception
	* @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
	* @return 
	* @since 29.4.2020 
	* @version Vendor Contact Setup
	*
	*/ 

	function SaveVendorContact($data){

	$UserName = $data['LoginID'];
	$LoginID = $data['LoginID'];
	$Password = $data['Password'];
	$ContactEmail = $data['ContactEmail'];
	$HasLogin = $data['HasLogin'];
	$ContactName = $data['ContactName'];
	$Designation = $data['Designation'];
	$OfficeNo = $data['OfficeNo'];
	$CellNo = $data['CellNo'];
	$userStatus = $data['Status'];
	$VendorUID = $data['VendorUID'];
	// $ContactFaxNo = $data['ContactFaxNo'];
	// $Department = $data['Department'];
	// $userRemarks = $data['Remarks'];

			if(!empty($Password) && !empty($LoginID) && !empty($HasLogin)){
		//Create User
		$datas=array('UserName'=>$UserName,
		'LoginID'=>$LoginID,
		'Password'=>md5($Password),
		'UserEmailID'=>$ContactEmail,
		'RoleUID'=>'23',
		'UserContactNo'=>$CellNo,
		'AbstractorUID'=>$VendorUID,
		"CreatedOn"=>date('y-m-d H:i:s'),
		"CreatedByUserUID"=>$this->loggedid,
		"Active"=>1,
		"FirstLogin"=>1,
		// 'UserFaxNo'=>$ContactFaxNo,
		"LoginExpiry"=>date('Y-m-d', strtotime(date('Y-m-d'). ' + 45 days'))
		);

		$this->db->insert('musers', $datas);
		$UserUID =  $this->db->insert_id();

		// Audit User Insert
		$STR = '<b>Vendor Contact User: '.$UserName.'</b> Created';

		$STR .= ' with UserName: '.$UserName.' and LoginID: '.$LoginID.'';

		$this->common_model->InsertAuditLog($VendorUID,$STR,'VendorContactUser');
}
		// Create Contact 
		$ContactArr = array(
		'AbstractorUID'=>$VendorUID,
		'ContactName'=>$ContactName,
		'DesignationUID'=>$Designation,
		'Email'=>$ContactEmail,
		'OfficeNo'=>$OfficeNo,
		'CellNo'=>$CellNo,
		'Status'=>$userStatus,
		'UserUID'=>$UserUID, 
		// 'FaxNo'=>$ContactFaxNo,
		// 'Department'=>$Department,
		// 'Remarks'=>$userRemarks
		);

		$this->db->insert('mabstractorcontact', $ContactArr);

		// Audit Contact Insert
		$STR = '<b>Vendor Contact: '.$ContactName.'</b> Created';

		if(!empty($Designation)){
			$DesignationName = $this->db->select('DesignationName')->from('mDesignation')->where('DesignationUID',$Designation)->get()->row()->DesignationName;
			$STR .= ' with Designation: '.$DesignationName;
		}

		if(!empty($Department)){
			$STR .= ' with Department: '.$Department;
		}

		if(!empty($ContactEmail)){
			$STR .= ' with ContactEmail: '.$ContactEmail;
		}

		if(!empty($OfficeNo)){
			$STR .= ' with OfficeNo: '.$OfficeNo;
		}

		if(!empty($ContactFaxNo)){ 
			$STR .= ' with ContactFaxNo: '.$ContactFaxNo;
		}

		if(!empty($CellNo)){
			$STR .= ' with CellNo: '.$CellNo;
		}

		if(!empty($userRemarks)){
			$STR .= ' with Remarks: '.$userRemarks;
		}

		$this->common_model->InsertAuditLog($VendorUID,$STR,'VendorContactUser');

			return $VendorUID; exit();
	}

	/**
		* @description Getting ContactTypes
		* @param 
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return array 
		* @since  Apr 2 2020
		* @version  Client Advanced Setup CD-HUD and Packaging v1
		*/ 
	function GetContactTypes(){
		$this->db->select('*');
		$this->db->from('mContactType');
		return $this->db->get()->result();
	}
	
	
	/**
		* @description Getting ContactMethods
		* @param 
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return array 
		* @since  Apr 2 2020
		* @version  Client Advanced Setup CD-HUD and Packaging v1
		*/ 
	function GetContactMethods(){
		$this->db->select('*');
		$this->db->from('mContactMethod');
		return $this->db->get()->result();
	} 

	/**
		* @description Getting TimeZones
		* @param 
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return array 
		* @since  Apr 2 2020
		* @version  Client Advanced Setup CD-HUD and Packaging v1
		*/ 
	function GetTimeZones(){
		$this->db->select('*');
		$this->db->from('mTimeZones');
		return $this->db->get()->result();
	} 
	
    /** 
	 * @author Periyasamy
	 * @return Object
	 * @since 02/04/2020
	*/
	function getReportSchedules()
	{
	  $this->db->where('Active', 1);
      return $this->db->get('mReportDeliverySchedule')->result();
	}

	/**
		*@description Function to getWorkflowNameByWorkflowUID
		*
		* @param $WorkflowModuleUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return String 
		* @since 3.4.2020 
		* @version Edit Feature 
		*
	*/ 
	function getWorkflowNameByWorkflowUID($WorkflowModuleUID){
		if (empty($WorkflowModuleUID)) {
			return "";
		}

		/*Query*/
		$this->db->select('WorkflowModuleName');
		$this->db->from('mworkflowmodules'); 
		$this->db->where('WorkflowModuleUID', $WorkflowModuleUID);
		return $this->db->get()->row()->WorkflowModuleName;
	}

	/**
		* @description Getting ContactMethod
		* @param 
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return array 
		* @since  Apr 2 2020
		* @version  Client Advanced Setup CD-HUD and Packaging v1
		*/ 
		function GetContactMethod($ContactMethodUID){
			$this->db->select('*');
			$this->db->from('mContactMethod');
			$this->db->where('ContactMethodUID',$ContactMethodUID);
			return $this->db->get()->row();
		} 


	/**
		*@description Function to vendorOwnedMinorities
		*
		* @param $VendorUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return Array 
		* @since 16.4.2020 
		* @version Minority Management 
		*
	*/ 
	function vendorOwnedMinorities($VendorUID){
		
		if (empty(!$VendorUID)) {
			return [];
		}
		$this->db->select('mMinorityCertification.MinorityCertificationCode, mMinorityCertification.MinorityDisplayName,  mVendorMinorityCertification.ExpirationDate');
		$this->db->from('mVendorMinorityCertification'); 
		$this->db->join('mMinorityCertification', 'mVendorMinorityCertification.MinorityCertificationUID = mMinorityCertification.MinorityCertificationUID');
		$this->db->where('mVendorMinorityCertification.VendorUID', $VendorUID);
		return $this->db->get()->result();
	}

	/**
    * @author Periyasamy S
    * @since 17/04/2020
    * @desc List active minority certifications
    */
	function getMinorityCertification()
	{
		return $this->db->select('*')->from('mMinorityCertification')->where('Active',1)->get()->result();
	}


	/**
	*Function county by countyuid
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Friday 17 April 2020
	*/

	function getCountyRowbyUID($CountyUID)
	{
		return $this->db->where('CountyUID', $CountyUID)->get('mCounties')->row();
	}

	/**
	*Function product by productuid
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Friday 17 April 2020
	*/
	function getProductRowbyUID($ProductUID)
	{
		return $this->db->where('ProductUID', $ProductUID)->get('mProducts')->row();
	}

	/**
	*Function subproduct by subproductuid
	*@author Praveen Kumar <praveen.kumar@avanzegroup.com>
	*@since Friday 17 April 2020
	*/
	function getSubProductRowbyUID($SubProductUID)
	{
		return $this->db->where('SubProductUID', $SubProductUID)->get('mSubProducts')->row();
	}


	/**
	*Function iterate JSON array and build sql query
	*@author Parthasarathy <parthasarathy.m@avanzegroup.com>
	*@since Thursday 23 April 2020
	*/
	function extractQueryFromRules($Rules, $Conditions, $_operator = "")
	{
		if ( is_array($Rules) ) {
			
			$arr_str = [];
			$opr = "";
			if (!empty($_operator)) {
				$opr = $_operator;				
			}
			foreach ($Rules as $key => $val) {
				
				if (in_array($key, ["AND", "OR"]) && !empty($key)) {
					$opr = $key;					
				}
				if ( is_array($val) ) {
					$arr_str[] = $this->extractQueryFromRules($val, $Conditions, $opr);
				}
				else{
					if (!empty($Conditions[$val])) {
						$arr_str[] = $Conditions[$val];
					}
				}
			}

			return !empty($arr_str) ? " ( " . implode(" " . $opr . " ", $arr_str) . " ) " : "";
		}
		else {
			return $Rules;
		}
	}


	/**
		*@description Function to getCountyUIDByStateUIDAndCountyName
		*
		* @param $StateUID, $CountyUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return row 
		* @since 28.04.2020 
		* @version Billing Trigger D2T-1 
		*
	*/ 
	function getCountyUIDByStateUIDAndCountyName($StateUID, $CountyName){
		
		$this->db->select('CountyUID');
		$this->db->from('mCounties'); 
		$this->db->where('mCounties.CountyName', $CountyName);
		$this->db->where('mCounties.StateUID', $StateUID);
		return $this->db->get()->row();
	}


	/**
		*@description Function to getCityUIDByStateUIDAndCountyUIDAndCityName
		*
		* @param $StateUID, $CountyUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return row 
		* @since 28.04.2020 
		* @version Billing Trigger D2T-1 
		*
	*/ 
	function getCityUIDByStateUIDAndCountyUIDAndCityName($StateUID, $CountyUID, $CityName){
		
		$this->db->select('CityUID');
		$this->db->from('mCities'); 
		$this->db->where('mCities.CityName', $CityName);
		$this->db->where('mCities.CountyUID', $CountyUID);
		$this->db->where('mCities.StateUID', $StateUID);
		return $this->db->get()->result_array();
	}


	function Resware_client_mail($OrderUID,$ActionName,$Remarks,$Attachments = [],$Multiple = FALSE){

		$currentuser = $this->GetUserDetailsByUser($this->loggedid);
		if(empty($currentuser)){ // API User
			$this->db->select('*')->from('musers')->where('LoginID', 'isgn');
			$query=$this->db->get();
			$currentuser=$query->row();
		}
		$order_details = $this->get_orderdetails($OrderUID);
		if(!empty($order_details->AltORderNumber)){
			$this->config->load('email', FALSE, TRUE);
			$this->load->library('email');
			$this->load->helper('email');
			$this->email->set_mailtype("html");
			$this->email->from($this->config->item('mail_from'));

			$OrgReswareMail = $this->common_model->get_row('morganizations',array('OrgCode'=>'9874'))->EmailTriggers;
			if(!empty($OrgReswareMail)){
				$this->email->to($OrgReswareMail);
			}

			$this->db->select('*');
			$this->db->from('mEmailTemplate');
			$this->db->where('Selecttype','ClientReswareEmail');
			$Template = $this->db->get()->row();
			if(!empty($Template)){
				$messageData = $Template->Body;
				$subjectData = $Template->Subject;
			}else{
				$messageData = '%TriggerActionName% for D2T Order %OrderAltNo% <br> Triggered by User: %CurrentUser% <br> %DateTime% <br> %TriggerActionData%';
				$subjectData = '%TriggerActionName% for D2T Order %OrderAltNo%';
			}
			$messageData = str_replace('%TriggerActionName%', $ActionName, $messageData);
			$messageData = str_replace('%CurrentUser%', $currentuser->UserName, $messageData);
			$messageData = str_replace('%OrderAltNo%', $order_details->AltORderNumber, $messageData);
			$messageData = str_replace('%DateTime%', date('m/d/Y H:i a'), $messageData);
			$subjectData = str_replace('%TriggerActionName%', $ActionName, $subjectData);
			$subjectData = str_replace('%OrderAltNo%', $order_details->AltORderNumber, $subjectData);
			if(empty(trim($Remarks))){
				$messageData = str_replace('%TriggerActionData%', '', $messageData);
			}else{
				$messageData = str_replace('%TriggerActionData%', $Remarks, $messageData);
			}

			$this->email->subject($subjectData);
			$this->email->message($messageData);
			$TEMPLATE_ATTACH_CONTENT = '';
			$attachment_doc = array();
			if($Multiple && !empty($Attachments)){
				foreach ($Attachments as $Attachment) {
					$TEMPLATE_ATTACH_CONTENT .= base64_encode(FCPATH.$Attachment['path']);
					$attachment_doc[$Attachment['path']] =  $Attachment['filename'];
					$this->email->attach(base_url().$Attachment['path'],'attachment',$Attachment['filename']);
				}
			}else if(!empty($Attachments)){
				$TEMPLATE_ATTACH_CONTENT .= base64_encode(FCPATH.$Attachment['path']);
				$attachment_doc[$Attachments['path']] =  $Attachments['filename'];
				$this->email->attach(base_url().$Attachments['path'],'attachment',$Attachments['filename']);
			}
			$attachment_docs = implode(", ", $attachment_doc);
			if($this->email->send()){
						$content = '
						From: TEMPLATE_FROM_ADDRESS
						MIME-Version: 1.0
						To: TEMPLATE_TO_ADDRESS
						Subject: TEMPLATE_SUBJECT
						Content-Type: multipart/mixed; boundary="080107000800000609090108"
						
						This is a message with multiple parts in MIME format.
						--080107000800000609090108
						Content-Type: text/html; charset="UTF-8"
						Content-Transfer-Encoding: 8bit
						
						TEMPLATE_BODY
						
						--080107000800000609090108
						Content-Type: application/octet-stream;name="TEMPLATE_ATTACH_FILENAME"
						Content-Transfer-Encoding: base64
						Content-Disposition: attachment;filename="TEMPLATE_ATTACH_FILENAME"
						
						TEMPLATE_ATTACH_CONTENT
						--080107000800000609090108
						';
						$content = str_replace("TEMPLATE_FROM_ADDRESS", $this->config->item('mail_from'), $content);
						$content = str_replace("TEMPLATE_TO_ADDRESS", $this->input->post('toemail'), $content);
						$content = str_replace("TEMPLATE_BODY", $messageData, $content);
						$content = str_replace("TEMPLATE_SUBJECT", $subjectData, $content);
						$content = str_replace("TEMPLATE_ATTACH_CONTENT", $TEMPLATE_ATTACH_CONTENT, $content);
					
						$L_name = md5(Date('Y-m-d H:i:s'));
						$LPath = 'uploads/EmailLogs/'.$L_name.'.eml';
						
						$handle =fopen($LPath,'w');
						fwrite($handle, $content);
						fclose($handle);
						$Content = json_encode(['documents'=>$attachment_doc,
						'subject'=>$subjectData,
						'senttomails'=>array($this->config->item('ReswareEmail')),
						'senttobcc'=>[], // Bcc should not listed in audit 
						'eml_file'=>$LPath]);
						$InsetData = array(
							'UserUID' => $this->loggedid,
							'ModuleName' => 'OrderMailLog',
							'OrderUID' => $OrderUID,
							'Feature' => $OrderUID,
							'NewValue'=> $messageData,
							'Content' => $Content,
							'DateTime' => date('Y-m-d H:i:s'));
						$this->common_model->InsertAuditTrail($InsetData);

				return true;
			}else{
				return false;
			}
		}
	}
	
	/**
		* @description Getting Relational Orders
		* @param OrderUID
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return Array 
		* @since  May 19 2020
		* @version Jira D2T-228
		*/ 
	function GetRelationalOrdersByID($OrderUID){
		$order_details = $this->get_orderdetails($OrderUID);
		$LastNo = explode('-',$order_details->OrderNumber);
		$this->db->select('tOrders.OrderUID,tOrders.OrderNumber,tOrders.SubProductUID,mSubProducts.SubProductName');
		$this->db->from('tOrders');
		$this->db->join('mSubProducts','mSubProducts.SubProductUID = tOrders.SubProductUID','left');
		$this->db->like('OrderSequence', substr($LastNo[0],1), 'both');
		$this->db->order_by('OrderUID',"ASC");
		return $this->db->get()->result();
	}


	/* ** Assigned Vendor Details Starts *** */

	
  /**
    *@description Function to getAssignedAbstractorDetails
    *
    * @param $AbstractorOrderUID
    * 
    * @throws no exception
    * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
    * @return Object 
    * @since 27.4.2020 
    * @version Billing Trigger - D2T -1 
    *
  */ 
  function getAssignedAbstractorDetails($AbstractorOrderUID){
  	

  	$this->db->select('torderabstractor.*, mabstractor.*, musers.UserName AS BilledByUserName', false);
  	$this->db->from('torderabstractor');
  	$this->db->join('mabstractor', 'torderabstractor.AbstractorUID = mabstractor.AbstractorUID');
  	$this->db->join('musers', 'torderabstractor.VendorBilledByUserUID = musers.UserUID', 'left');
  	$this->db->where('AbstractorOrderUID', $AbstractorOrderUID);

  	return $this->db->get()->row();

  }
	
	/* ** Assigned Vendor Details Ends *** */




	/**
		*@description Function to getBillingTriggeredVendorOrders
		*
		* @param $torderabstractors
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return Array 
		* @since 28.4.2020 
		* @version Billing Trigger 
		*
	*/ 
	function getBillingTriggeredVendorOrders($OrderUID, $WorkflowModuleUIDs){
	
		if ( empty($OrderUID) || empty($WorkflowModuleUIDs)) {
			return [];
		}

		$this->db->select('torderabstractor.AbstractorOrderUID,torderabstractor.AbstractorUID, torderabstractor.OrderTypeUID, torderabstractor.IsVendorBilled, torderabstractor.VendorBilledDateTime, mabstractor.AbstractorFirstName, tOrders.OrderUID, tOrders.SubProductUID, mProducts.ProductUID, tOrders.CustomerUID');
		$this->db->select('CASE WHEN mabstractor.AbstractorCompanyName IS NOT NULL OR mabstractor.AbstractorCompanyName != "" THEN mabstractor.AbstractorCompanyName ELSE CONCAT_WS(mabstractor.AbstractorFirstName, " ", mabstractor.AbstractorLastName) END AS AbstractorFirstNameOrCompanyName', false);

		$this->db->select('CASE WHEN EXISTS( SELECT tOrderschedule.AbstractorOrderUID FROM tOrderschedule WHERE tOrderschedule.AbstractorOrderUID = torderabstractor.AbstractorOrderUID ) THEN ( SELECT SigningStateCode FROM tOrderClosing WHERE ScheduleUID = ( SELECT tOrderschedule.ScheduleUID FROM tOrderschedule WHERE tOrderschedule.AbstractorOrderUID = torderabstractor.AbstractorOrderUID ) ) ELSE tOrders.PropertyStateCode END AS StateCode', false);

		$this->db->select('CASE WHEN EXISTS( SELECT tOrderschedule.AbstractorOrderUID FROM tOrderschedule WHERE tOrderschedule.AbstractorOrderUID = torderabstractor.AbstractorOrderUID ) THEN ( SELECT SigningCountyName FROM tOrderClosing WHERE ScheduleUID = ( SELECT tOrderschedule.ScheduleUID FROM tOrderschedule WHERE tOrderschedule.AbstractorOrderUID = torderabstractor.AbstractorOrderUID ) ) ELSE tOrders.PropertyCountyName END AS CountyName', false);
		
		$this->db->select('CASE WHEN EXISTS( SELECT tOrderschedule.AbstractorOrderUID FROM tOrderschedule WHERE tOrderschedule.AbstractorOrderUID = torderabstractor.AbstractorOrderUID ) THEN ( SELECT SigningZipCode FROM tOrderClosing WHERE ScheduleUID = ( SELECT tOrderschedule.ScheduleUID FROM tOrderschedule WHERE tOrderschedule.AbstractorOrderUID = torderabstractor.AbstractorOrderUID ) ) ELSE tOrders.PropertyZipcode END AS ZipCode', false);
		
		$this->db->select('CASE WHEN EXISTS( SELECT tOrderschedule.AbstractorOrderUID FROM tOrderschedule WHERE tOrderschedule.AbstractorOrderUID = torderabstractor.AbstractorOrderUID ) THEN ( SELECT SigningCityName FROM tOrderClosing WHERE ScheduleUID = ( SELECT tOrderschedule.ScheduleUID FROM tOrderschedule WHERE tOrderschedule.AbstractorOrderUID = torderabstractor.AbstractorOrderUID ) ) ELSE tOrders.PropertyCityName END AS CityName', false);
		
		$this->db->from('tOrders');
		$this->db->join('mSubProducts', 'mSubProducts.SubProductUID = tOrders.SubProductUID');
		$this->db->join('mProducts', 'mSubProducts.ProductUID = mProducts.ProductUID');
		$this->db->join('torderabstractor', 'torderabstractor.OrderUID = tOrders.OrderUID');
		$this->db->join('mabstractor', 'torderabstractor.AbstractorUID = mabstractor.AbstractorUID');

		$this->db->where('tOrders.OrderUID', $OrderUID);
		$this->db->where('torderabstractor.IsVendorBilled', 0);
		$vendororders = $this->db->get()->result();

		$BillableVendors = [];
		foreach ($vendororders as $key => $abs) {	
			
			$AbstractorUID = $abs->AbstractorUID;
			$ProductUID = $abs->ProductUID;
			$SubProductUID = $abs->SubProductUID;
			$OrderTypeUID = $abs->OrderTypeUID;
			$State=$this->common_model->GetStatebyCode($abs->StateCode);
			$StateUID = '';
			$CountyUID = '';
			$CityUID = [];
			$ZipCode = $abs->ZipCode;
			$CustomerUID = $abs->CustomerUID;

			if (!empty($State) && !empty($State->StateUID)) {
				$StateUID = $State->StateUID;    
			}

		    $CountyUID = $this->common_model->getCountyUIDByStateUIDAndCountyName($StateUID, $abs->CountyName)->CountyUID;
		    $CityUID = array_column($this->common_model->getCityUIDByStateUIDAndCountyUIDAndCityName($StateUID, $CountyUID, $abs->CityName), "CityUID");
		    $CityUID[] = 0;
		    $CityUID = implode(",", $CityUID);

		    $CASEWHEN = [];
		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND  mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND mAssignmentTypeBillingTrigger.SubProductUID = '".$SubProductUID."' AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND mAssignmentTypeBillingTrigger.ZipCode = '".$ZipCode."' AND mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND mAssignmentTypeBillingTrigger.SubProductUID = '".$SubProductUID."' AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND 
		    	mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") AND 
		    	( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) ) THEN TRUE ELSE FALSE END )";
		    
		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND mAssignmentTypeBillingTrigger.SubProductUID = '".$SubProductUID."' AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND mAssignmentTypeBillingTrigger.SubProductUID = '".$SubProductUID."' AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND mAssignmentTypeBillingTrigger.SubProductUID = '".$SubProductUID."' AND ( mAssignmentTypeBillingTrigger.StateUID = '0' OR mAssignmentTypeBillingTrigger.StateUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.StateUID = '0' OR mAssignmentTypeBillingTrigger.StateUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND ( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.StateUID = '0' OR mAssignmentTypeBillingTrigger.StateUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";


		    /* **** SubProduct Null **** */
		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND  mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND mAssignmentTypeBillingTrigger.ZipCode = '".$ZipCode."' AND mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND 
		    	mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") AND 
		    	( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) ) THEN TRUE ELSE FALSE END )";
		    
		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.StateUID = '0' OR mAssignmentTypeBillingTrigger.StateUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";


		    /* **** Product Null **** */
		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND ( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND mAssignmentTypeBillingTrigger.ZipCode = '".$ZipCode."' AND mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND 		    	mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") AND 
		    	( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) ) THEN TRUE ELSE FALSE END )";
		    
		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN ( mAssignmentTypeBillingTrigger.CustomerUID = '".$CustomerUID."' AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.StateUID = '0' OR mAssignmentTypeBillingTrigger.StateUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";


		    /*Without CustomerUID*/

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND  mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND mAssignmentTypeBillingTrigger.SubProductUID = '".$SubProductUID."' AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND mAssignmentTypeBillingTrigger.ZipCode = '".$ZipCode."' AND mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND mAssignmentTypeBillingTrigger.SubProductUID = '".$SubProductUID."' AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND 
		    	mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") AND 
		    	( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) ) THEN TRUE ELSE FALSE END )";
		    
		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND mAssignmentTypeBillingTrigger.SubProductUID = '".$SubProductUID."' AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND mAssignmentTypeBillingTrigger.SubProductUID = '".$SubProductUID."' AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND mAssignmentTypeBillingTrigger.SubProductUID = '".$SubProductUID."' AND ( mAssignmentTypeBillingTrigger.StateUID = '0' OR mAssignmentTypeBillingTrigger.StateUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.StateUID = '0' OR mAssignmentTypeBillingTrigger.StateUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND ( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.StateUID = '0' OR mAssignmentTypeBillingTrigger.StateUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";


		    /* **** SubProduct Null **** */
		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND  mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND mAssignmentTypeBillingTrigger.ZipCode = '".$ZipCode."' AND mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND 
		    	mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") AND 
		    	( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) ) THEN TRUE ELSE FALSE END )";
		    
		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.StateUID = '0' OR mAssignmentTypeBillingTrigger.StateUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";


		    /* **** Product Null **** */
		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND ( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND mAssignmentTypeBillingTrigger.ZipCode = '".$ZipCode."' AND mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND
				mAssignmentTypeBillingTrigger.CityUID IN (".$CityUID.") 
		     	AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) ) THEN TRUE ELSE FALSE END )";
		    
		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND mAssignmentTypeBillingTrigger.CountyUID = '".$CountyUID."' AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND mAssignmentTypeBillingTrigger.StateUID = '".$StateUID."' AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";

		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND( mAssignmentTypeBillingTrigger.ProductUID = '0' OR mAssignmentTypeBillingTrigger.ProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.SubProductUID = '0' OR mAssignmentTypeBillingTrigger.SubProductUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.StateUID = '0' OR mAssignmentTypeBillingTrigger.StateUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";


		    $CASEWHEN[] = "( CASE WHEN (  ( mAssignmentTypeBillingTrigger.CustomerUID IS NULL OR mAssignmentTypeBillingTrigger.CustomerUID = 0 ) AND mAssignmentTypeBillingTrigger.WorkflowModuleUID IN (".implode(',', $WorkflowModuleUIDs).") AND mAssignmentTypeBillingTrigger.AssignmentTypeUID = '".$OrderTypeUID."' AND mAssignmentTypeBillingTrigger.ProductUID = '".$ProductUID."' AND mAssignmentTypeBillingTrigger.SubProductUID = '".$SubProductUID."' AND ( mAssignmentTypeBillingTrigger.StateUID = '0' OR mAssignmentTypeBillingTrigger.StateUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.CountyUID = '0' OR mAssignmentTypeBillingTrigger.CountyUID IS NULL ) AND ( mAssignmentTypeBillingTrigger.ZipCode = '0' OR mAssignmentTypeBillingTrigger.ZipCode IS NULL ) AND ( mAssignmentTypeBillingTrigger.CityUID = '0' OR mAssignmentTypeBillingTrigger.CityUID IS NULL ) ) THEN TRUE ELSE FALSE END )";




		    $WHERE = " ( " . implode(" OR ", $CASEWHEN) . " ) ";

		    $this->db->select('mAssignmentTypeBillingTrigger.CustomerUID, mAssignmentTypeBillingTrigger.BillingPeriod');
		    $this->db->from('mAssignmentTypeBillingTrigger'); 
		    $this->db->where($WHERE, NULL, FALSE);
		    $result = $this->db->get();
		    if ($result->num_rows() > 0) 
		    {
		    	$abs->BillingPeriod = array_column($result->result_array(), "BillingPeriod");
		    	$BillableVendors[] = $abs;
		    }

		}

		return $BillableVendors;

	}
	//@Desc Get no of rows additional pricing added for customer @Author Jainulabdeen @Since May 23 2020
function getCustomerAddPricing($OrderUID){
	$query = $this->db->select('*')->from('tOrderPayments');
	$this->db->where('OrderUID', $OrderUID);
    $this->db->where('BeneficiaryType', 'Customer');
    $this->db->where_in('ApprovalFunction',array('CustomerPricingOverride','CustomerPricingAdjustments'));
    return $query->get()->num_rows();
}

function getAuditLog($id, $Type)
{
  return $this->db->select('*')->order_by('AuditUID','DESC')->where('Feature',$id)->where_in('ModuleName',$Type)->from('taudittrail')->get()->result();
}

function getDocumentTypes()
{
  $this->db->where('Active', 1);
  return $this->db->get('mdocumenttypes')->result();
}

function getDocumentTypeByUID($DocumentTypeUID)
{
  $this->db->where('DocumentTypeUID', $DocumentTypeUID);
  return $this->db->get('mdocumenttypes')->row();
}

	// @Desc get Order Validation Triggers @Auth Uba @On May 30 2020 
	function getValidationTriggers(){
		$this->db->select('*');
		$this->db->from('mValidationTriggerFields');
		$this->db->where('Active',1);
		return $this->db->get()->result();
	}

	function getmdocumenttypes()
	{
		//return $this->db->select('*')->from('mdocumenttypes')->where('Active',1)->get()->result();

		$this->db->select('*');
		$this->db->from('mdocumenttypes');
		$this->db->join('mDocumentCategory','mdocumenttypes.DocumentCategoryUID = mDocumentCategory.DocumentCategoryUID','LEFT');
		$this->db->where(array('mDocumentCategory.OrderSourceUID'=> 0,'mdocumenttypes.Active'=> 1));
		$mdocumenttypes = $this->db->get()->result();
		return $mdocumenttypes;
	}

	/* @purpose: Get Keystone Doctypes @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: June 12 2020 */
	function GetDocumentTypesOfKeystone($OrderSourceUID)
	{

		$this->db->select('*');
		$this->db->from('mdocumenttypes');
		$this->db->join('mDocumentCategory','mdocumenttypes.DocumentCategoryUID = mDocumentCategory.DocumentCategoryUID','LEFT');
		$this->db->where(array('mDocumentCategory.OrderSourceUID'=> 0,'mdocumenttypes.Active'=> 1));
		$Defaultmdocumenttypes = $this->db->get()->result();

		$this->db->select('*');
		$this->db->from('mdocumenttypes');
		$this->db->join('mDocumentCategory','mdocumenttypes.DocumentCategoryUID = mDocumentCategory.DocumentCategoryUID','LEFT');
		$this->db->where(array('mDocumentCategory.OrderSourceUID'=> $OrderSourceUID,'mdocumenttypes.Active'=> 1));
		$APImdocumenttypes = $this->db->get()->result();

		$res = array_merge($Defaultmdocumenttypes,$APImdocumenttypes);
		return $res;
	}

	function getDocNotes($OrderUID,$OrderDocumentUID)
	{
		$this->db->select('tOrderDocumentNotes.*,musers.UserName,mdocumenttypes.DocumentTypeName');
		$this->db->from('tOrderDocumentNotes');
		$this->db->join('musers','musers.UserUID = tOrderDocumentNotes.CreatedByUserUID','LEFT');
		$this->db->join('torderdocuments','torderdocuments.OrderDocumentUID = tOrderDocumentNotes.OrderDocumentUID','LEFT');
		$this->db->join('mdocumenttypes','mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID','LEFT');
		$this->db->where(array('tOrderDocumentNotes.OrderUID'=>$OrderUID,'tOrderDocumentNotes.OrderDocumentUID'=>$OrderDocumentUID));
		$data['DocNotes'] = $this->db->get()->result();

		$this->db->select('*');
		$this->db->from('torderdocuments');
		$this->db->join('mdocumenttypes','mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID','LEFT');
		$this->db->where(array('torderdocuments.OrderUID'=>$OrderUID,'torderdocuments.OrderDocumentUID'=>$OrderDocumentUID));
		$data['OrderDocs'] = $this->db->get()->row();

		return $this->load->view('documentNotes',$data);

	}

	function GetOrderFieldsFileNameConvention($OrderUID,$DocumentNamingConvention,$DocFileName){
		$order_detail = $this->getOrderDetailsByOrderUID($OrderUID);
		$Strings = [];
		if(count(explode(',',$DocumentNamingConvention)) > 1){
			$Strings2 = explode(',',$DocumentNamingConvention);
			if(!empty($Strings2)){
				foreach ($Strings2 as $key => $value) {
					$Strings[] = $value;
				}
			}
		}else if($DocumentNamingConvention){
			$Strings[] = $DocumentNamingConvention;
		}

		$FinalName = '';

		// Loop: finding names @Uba @On 3 Aug 2020
		
		foreach($Strings as $key => $String){
			if($key != 0){
				$FinalName .= '-';
			}
			if(!empty($String)){
				$Row = $this->db->select('*')->from('mfields')->where(array('FieldName'=>$String,'IsDocumentNamingConvention'=>1))->get()->row();
				if(!empty($Row)){
					if(!empty($Row->TableName) && $Row->TableName != 'tOrders' && $this->db->field_exists('OrderUID',$Row->TableName)){
						// Check from another table having OrderUID as Key
						$this->db->select($Row->FieldName);
						$this->db->from('tOrders');
						$this->db->join($Row->TableName,''.$Row->TableName.'.OrderUID = tOrders.OrderUID','left');
						$this->db->where('OrderUID',$OrderUID);
						$Output = $this->db->get()->row();
					}
					if(!empty($Row->TableName) && $Row->TableName != 'tOrders' && !empty($Row->DataFromTableKey) &&  $this->db->field_exists($Row->DataFromTableKey,'tOrders')){
						// Check from another table, tOrders has different key values				
						$this->db->select($Row->FieldName);
						$this->db->from('tOrders');
						$this->db->join($Row->TableName,''.$Row->TableName.'.'.$Row->DataFromTableKey.' = tOrders.'.$Row->DataFromTableKey.'','left');
						$this->db->where('tOrders.OrderUID',$OrderUID);
						$Output = $this->db->get()->row();
					}
					else if(!empty($Row->TableName) && $Row->TableName == 'tOrders' && !empty($Row->FieldName) &&  $this->db->field_exists($Row->FieldName,'tOrders')){
						// get from same table - tOrders
						$this->db->select($Row->FieldName);
						$this->db->from('tOrders');
						$this->db->where('OrderUID',$OrderUID);
						$Output = $this->db->get()->row();
					}else if(property_exists($order_detail,$Row->FieldName)){
						// Else Check order details
						$Output = $order_detail;
					}
				}
				if(property_exists($Output,$String)){
					$FinalName .= $Output->$String;
				}else if(!empty($Borrowers)){ // All Borrowers
					$FinalName .= $Borrowers;
				}
			}
		}

		$ext = pathinfo($DocFileName, PATHINFO_EXTENSION);

		if(!empty($FinalName)){
			return $FinalName.'.'.$ext;
		}else{
			return $DocFileName;
		}
		
	}

	
	/**
		* @description Getting Document Packaging Fields
		* @param no params
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return Array 
		* @since  Aug 3 2020
		* @version Doc packging
		*/ 
	function getDocumentPackagingFields(){
		$this->db->select('*');
		$this->db->from('mfields');
		$this->db->where('IsDocumentTypeField',1);
		$this->db->order_by('FieldUID','ASC');
		$DocumentNames = $this->db->get()->result();
		return array_column($DocumentNames,'FieldName');
	}

/**
		* @description Replacing Document Packaging Fields
		* @param $OrderUID,$TemplateData
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return TemplateData 
		* @since  Aug 3 2020
		* @version Doc packging
		*/
	function DocumentPackagingOrderFieldReplace($OrderUID,$TemplateData){
		
		$DocPackagingFields = $this->getDocumentPackagingFields();
		$TemplateDataReceived = $TemplateData;
		$ReplacedFields = [];	
		foreach($DocPackagingFields as $key => $String){
			$Output = '';
			if( strpos( $TemplateData, '%%'.$String.'%%' ) !== false) { // If template has string
				
			$Row = $this->db->select('*')->from('mfields')->where(array('FieldName'=>$String,'IsDocumentNamingConvention'=>1))->get()->row();
			if(!empty($Row)){	
					if(!empty($Row->TableName) && $Row->TableName != 'tOrders' && !empty($Row->DataFromTableKey)){
						// Check from another table, tOrders has different key values				
						$this->db->select($Row->TableName.'.'.$Row->FieldName);
						$this->db->from('tOrders');
						$this->db->join($Row->TableName,''.$Row->TableName.'.'.$Row->DataFromTableKey.' = tOrders.'.$Row->DataFromTableKey.'','left');
						$this->db->where('tOrders.OrderUID',$OrderUID);
						$Output = $this->db->get()->row();
						
					}
					else if(!empty($Row->TableName) && $Row->TableName != 'tOrders' && !empty($Row->DifferentKeyName)){
						// Check from another table having OrderUID as Key
						$this->db->select($Row->TableName.'.'.$Row->DifferentKeyName);
						$this->db->from('tOrders');
						$this->db->join($Row->TableName,''.$Row->TableName.'.OrderUID = tOrders.OrderUID','left');
						$this->db->where('tOrders.OrderUID',$OrderUID);
						$Output = $this->db->get()->row();
					
					}
					else if(!empty($Row->TableName) && $Row->TableName != 'tOrders'){
						// Check from another table having OrderUID as Key
						$this->db->select($Row->TableName.'.'.$Row->FieldName);
						$this->db->from('tOrders');
						$this->db->join($Row->TableName,''.$Row->TableName.'.OrderUID = tOrders.OrderUID','left');
						$this->db->where('tOrders.OrderUID',$OrderUID);
						$Output = $this->db->get()->row();
					}
					else if(!empty($Row->TableName) && $Row->TableName == 'tOrders'){
						// get from same table - tOrders
						$this->db->select($Row->FieldName);
						$this->db->from('tOrders');
						$this->db->where('OrderUID',$OrderUID);
						$Output = $this->db->get()->row();
					}
				if(!empty($Row->DifferentKeyName) && property_exists($Output,$Row->DifferentKeyName)){
					$str = $Row->DifferentKeyName;
					$FinalName = $Output->$str;
					$Row->ReplacedVal = $FinalName;
					$val = '<span class="mceNonEditable">'.$FinalName.'</span>';
					$TemplateData = str_replace('%%'.$Row->FieldName.'%%',$val,$TemplateData);
					array_push($ReplacedFields,$Row);
				}else if(property_exists($Output,$String)){
					// Replace TemplateData
					$FinalName = $Output->$String;
					$Row->ReplacedVal = $FinalName;
					$val = '<span class="mceNonEditable">'.$FinalName.'</span>';
					$TemplateData = str_replace('%%'.$Row->FieldName.'%%',$val,$TemplateData);
					array_push($ReplacedFields,$Row);
				}
			}
			}else{
				$TemplateData = str_replace('%%'.$String.'%%','',$TemplateData);
			}
		}
		
		return array('FormBody'=>$TemplateData,'ReplacedFields'=>$ReplacedFields);
	}

	function IsClosingOrder($ProductUID)
	{
	  $this->db->where('ProductUID', $ProductUID);
	  $this->db->where('IsClosingProduct',1);
	  $IsClosing = $this->db->get('mProducts')->num_rows();
	  if($IsClosing>0)
	  {
	  	return 'Closing';
	  } else {
	  	return 'Title';
	  }
	}

	// function getDocumentPackagesByOrder($CustomerUID='0',$ProductUID='0',$SubProductUID='0',$OrderTypeUID='0',$StateUID='0',$CountyUID='0',$CityUID='0',$ZipCode='0')
	// {
	// 	// Empty Validation Added for CountyUID,CityUID @Uba @On 31 Jul 2020
	// 	$CountyUID = (!empty($CountyUID)) ? $CountyUID : 0;
	// 	$CityUID = (!empty($CityUID)) ? $CityUID : 0;
	// 	$ProductCategory = $this->IsClosingOrder($ProductUID);
	// 	return $this->db->query("SELECT mDocPackages.DocPackageUID,mDocPackages.DocPackageName,mDocPackages.ProductCategory,mDocPackages.DefaultView, COUNT(mDocPackageDocumentTypes.DocumentTypeUID) AS NoDoc, GROUP_CONCAT(mDocPackageDocumentTypes.DocumentTypeUID) AS DocumentTypes, mDocPackages.Merge, mDocPackages.SecureDelivery FROM ( SELECT (CASE WHEN CustomerUID = '$CustomerUID' AND ProductUID = '$ProductUID' AND SubProductUID = '$SubProductUID' AND OrderTypeUID = 0 THEN DocPackageUID WHEN CustomerUID = '$CustomerUID' AND ProductUID = 0 AND SubProductUID = 0 AND OrderTypeUID = 0 THEN DocPackageUID WHEN  CustomerUID = '$CustomerUID' AND ProductUID = '$ProductUID' AND SubProductUID = '$SubProductUID' AND OrderTypeUID = '$OrderTypeUID' THEN DocPackageUID WHEN CustomerUID = '$CustomerUID' AND OrderTypeUID = '$OrderTypeUID' AND ProductUID = 0 AND SubProductUID = 0 THEN DocPackageUID WHEN CustomerUID = '$CustomerUID' AND ProductUID = '$ProductUID' AND SubProductUID = 0 AND OrderTypeUID = 0 THEN DocPackageUID ELSE NULL END) AS PackageUID FROM mDocPackageProducts 
	// 		UNION 
	// 		SELECT (CASE WHEN StateUID = '$StateUID' AND CountyUID = 0 AND CityUID = 0 AND ZipCode = 0 THEN DocPackageUID WHEN StateUID = '$StateUID' AND CountyUID IN ($CountyUID) AND CityUID = 0 AND ZipCode = 0 THEN DocPackageUID WHEN StateUID = '$StateUID' AND CountyUID IN ($CountyUID) AND CityUID IN($CityUID) AND ZipCode = 0 THEN DocPackageUID WHEN StateUID = '$StateUID' AND CountyUID IN ($CountyUID) AND CityUID IN($CityUID) AND ZipCode = '$ZipCode' THEN DocPackageUID WHEN StateUID = '$StateUID' AND CountyUID = 0 AND CityUID = 0 AND ZipCode = '$ZipCode' THEN DocPackageUID WHEN StateUID = '$StateUID' AND CountyUID = 0 AND CityUID IN ($CityUID) AND ZipCode = '$ZipCode' THEN DocPackageUID WHEN StateUID = '$StateUID' AND CountyUID = 0 AND CityUID IN ($CityUID) AND ZipCode = 0 THEN DocPackageUID ELSE NULL END) FROM mDocPackageGeographic
	// 	)package INNER JOIN mDocPackages ON mDocPackages.DocPackageUID = PackageUID AND ProductCategory = '$ProductCategory' LEFT JOIN mDocPackageDocumentTypes ON mDocPackageDocumentTypes.DocPackageUID = PackageUID WHERE PackageUID IS NOT NULL GROUP BY PackageUID")->result();
	// }

	function getDocumentTypeByOrder($CustomerUID=NULL,$ProductUID=NULL,$SubProductUID=NULL,$OrderTypeUID=NULL,$StateUID=NULL,$CountyUID=NULL,$CityUID='0',$ZipCode='0')
	{
		// Empty Validation Added for CountyUID,CityUID @Uba @On 31 Jul 2020
		$CountyUID = (!empty($CountyUID)) ? $CountyUID : NULL;
		$CityUID = (!empty($CityUID)) ? $CityUID : NULL;
		$ProductCategory = $this->IsClosingOrder($ProductUID);
		return $this->db->query("SELECT mdocumenttypes.DocumentTypeUID, mdocumenttypes.DocumentTypeName FROM ( SELECT (CASE WHEN CustomerUID = '$CustomerUID' AND ProductUID = '$ProductUID' AND SubProductUID = '$SubProductUID' AND OrderTypeUID IS NULL THEN DocumentTypeUID WHEN CustomerUID = '$CustomerUID' AND ProductUID IS NULL AND SubProductUID IS NULL AND OrderTypeUID IS NULL THEN DocumentTypeUID WHEN CustomerUID = '$CustomerUID' AND ProductUID = '$ProductUID' AND SubProductUID = '$SubProductUID' AND OrderTypeUID = '$OrderTypeUID' THEN DocumentTypeUID WHEN CustomerUID = '$CustomerUID' AND OrderTypeUID = '$OrderTypeUID' AND ProductUID IS NULL AND SubProductUID IS NULL THEN DocumentTypeUID WHEN CustomerUID = '$CustomerUID' AND ProductUID = '$ProductUID' AND SubProductUID IS NULL AND OrderTypeUID IS NULL THEN DocumentTypeUID ELSE NULL END) AS DocumentTypeUID FROM mDocTypeClientProducts UNION SELECT (CASE WHEN StateUID = '$StateUID' AND CountyUID IS NULL AND CityUID IS NULL AND ZipCode IS NULL THEN DocumentTypeUID WHEN StateUID = '$StateUID' AND CountyUID IN (5) AND CityUID IS NULL AND ZipCode IS NULL THEN DocumentTypeUID WHEN StateUID = '$StateUID' AND CountyUID IN (5) AND CityUID IN($CityUID) AND ZipCode IS NULL THEN DocumentTypeUID WHEN StateUID = '$StateUID' AND CountyUID IN (5) AND CityUID IN($CityUID) AND ZipCode = '$ZipCode' THEN DocumentTypeUID WHEN StateUID = '$StateUID' AND CountyUID IS NULL AND CityUID IS NULL AND ZipCode = '$ZipCode' THEN DocumentTypeUID WHEN StateUID = '$StateUID' AND CountyUID IS NULL AND CityUID IN ($CityUID) AND ZipCode = '$ZipCode' THEN DocumentTypeUID WHEN StateUID = '$StateUID' AND CountyUID IS NULL AND CityUID IN ($CityUID) AND ZipCode IS NULL THEN DocumentTypeUID ELSE NULL END) FROM mDocTypeGio)doctype INNER JOIN mdocumenttypes ON mdocumenttypes.DocumentTypeUID = doctype.DocumentTypeUID AND mdocumenttypes.ProductCategory = '$ProductCategory' LEFT JOIN torderdocuments ON torderdocuments.DocumentTypeUID = doctype.DocumentTypeUID WHERE doctype.DocumentTypeUID IS NOT NULL GROUP BY doctype.DocumentTypeUID")->result();
	}

	function getDocumentTypeMissingByOrder($PackageUIDs,$OrderUID)
	{
		if(empty($PackageUIDs)){
			return [];
		}
		$this->db->select('GROUP_CONCAT(DocumentTypeUID) as DocumentTypeUIDs');
		$this->db->from('mDocPackageDocumentTypes');
		$this->db->where_in('DocPackageUID',$PackageUIDs);
		$DocumentTypeUIDs = explode(',',$this->db->get()->row()->DocumentTypeUIDs);

		$this->db->select('*');
		$this->db->from('torderdocuments');
		$this->db->where('OrderUID',$OrderUID);
		$this->db->where('DocumentTypeUID !=',0);
		$this->db->group_by('DocumentTypeUID');
		$Documents = array_column($this->db->get()->result(),'DocumentTypeUID');
		$DocumentsMiss = array_diff($DocumentTypeUIDs,$Documents);

		if(empty($DocumentsMiss)){
			return [];
		}
		$this->db->select('*');
		$this->db->from('mdocumenttypes');
		$this->db->where_in('DocumentTypeUID',$DocumentsMiss);
		return $this->db->get()->result();
	}

	function getDocTypeProductMatchs($CustomerUID,$ProductUID,$SubProductUID,$OrderTypeUID){
		$this->db->select('GROUP_CONCAT(DocumentTypeUID) as DocumentTypeUID');
		$this->db->from('mDocTypeClientProducts');
		$this->db->where('CustomerUID',$CustomerUID);
		$this->db->where('ProductUID',$ProductUID);
		$this->db->where('SubProductUID',$SubProductUID);
		$this->db->where('OrderTypeUID',$OrderTypeUID);
		return $this->db->get()->row()->DocumentTypeUID;
	}

	function getDocTypeGioMatchs($StateUID,$CountyUID,$CityUID,$ZipCode){
		$this->db->select('GROUP_CONCAT(DocumentTypeUID) as DocumentTypeUID');
		$this->db->from('mDocTypeClientProducts');
		$this->db->where('StateUID',$StateUID);
		$this->db->where('CountyUID',$CountyUID);
		$this->db->where('CityUID',$CityUID);
		$this->db->where('ZipCode',$ZipCode);
		return $this->db->get()->row()->DocumentTypeUID;
	}

	// function getDocTypesByPackageUID($DocPackageUID){
	// 	$this->db->where('DocPackageUID',$DocPackageUID);
	// 	$mDocPackageProducts = $this->db->get('mDocPackageProducts')->result();

	// 	$this->db->where('DocPackageUID',$DocPackageUID);
	// 	$mDocTypeGio = $this->db->get('mDocPackageGeographic')->result();

	// 	if(empty($mDocPackageProducts) && empty($mDocTypeGio)){
	// 		$this->db->where('Active', 1);
	// 		return $this->db->get('mdocumenttypes')->result();
	// 	}
		
	// 	$this->db->select('GROUP_CONCAT(DocumentTypeUID) as DocumentTypeUIDs');
	// 	$this->db->from('mDocPackageProducts');
	// 	$this->db->join('mDocTypeClientProducts','mDocTypeClientProducts.CustomerUID = mDocPackageProducts.CustomerUID AND mDocTypeClientProducts.ProductUID = mDocPackageProducts.ProductUID AND mDocTypeClientProducts.SubProductUID = mDocPackageProducts.SubProductUID AND mDocTypeClientProducts.OrderTypeUID = mDocPackageProducts.OrderTypeUID','left');
	// 	$this->db->where('DocPackageUID',$DocPackageUID);
	// 	$ClientProducts = $this->db->get()->row()->DocumentTypeUIDs;
	// 	$ClientProducts = explode(',',$ClientProducts);

	// 	$this->db->select('GROUP_CONCAT(DocumentTypeUID) as DocumentTypeUIDs');
	// 	$this->db->from('mDocPackageGeographic');
	// 	$this->db->join('mDocTypeGio','mDocTypeGio.StateUID = mDocPackageGeographic.StateUID AND mDocTypeGio.CountyUID = mDocPackageGeographic.CountyUID AND mDocTypeGio.CityUID = mDocPackageGeographic.CityUID AND mDocTypeGio.ZipCode = mDocPackageGeographic.ZipCode','left');
	// 	$this->db->where('DocPackageUID',$DocPackageUID);
	// 	$Gio = $this->db->get()->row()->DocumentTypeUIDs;
	// 	$Gio = explode(',',$Gio);

	// 	$DocumentTypeUIDs = array_filter(array_unique(array_merge($ClientProducts,$Gio)));
	// 	if(empty($DocumentTypeUIDs)){
	// 		return [];
	// 	}else{
	// 		$this->db->where('Active', 1);
	// 		$this->db->where_in('DocumentTypeUID', $DocumentTypeUIDs);
	// 		return $this->db->get('mdocumenttypes')->result();
	// 	}
	// }

	function getPackageStatus($DocPackageUID,$OrderUID)
	{
	  $sts = $this->db->query("SELECT CASE WHEN EXISTS(SELECT DocumentTypeUID FROM tDocPackageDocumentTypes WHERE OrderUID = '$OrderUID' AND DocPackageUID = '$DocPackageUID') THEN 'Revised' WHEN EXISTS(SELECT DocumentTypeUID FROM torderdocuments WHERE OrderUID = '$OrderUID' AND DocumentTypeUID IN (SELECT DocumentTypeUID FROM mDocPackageDocumentTypes WHERE DocPackageUID = '$DocPackageUID') GROUP BY DocumentTypeUID HAVING COUNT(DocumentTypeUID) > 1) THEN 'Updated' ELSE 'Original' END as PackageStatus")->row(); 	
	  return $sts->PackageStatus;
	}

	function getDocumentsByUID($OrderUID,$DocumentUID)
	{
	  if(empty($DocumentUID)) { return []; }	
	  $this->db->select('tOrders.OrderDocsPath, torderdocuments.*,mdocumenttypes.DocumentNamingConvention');
	  $this->db->from('torderdocuments');
	  $this->db->join('tOrders', 'torderdocuments.OrderUID = tOrders.OrderUID');
	  $this->db->join('mdocumenttypes', 'mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID');
	  $this->db->where('tOrders.OrderUID', $OrderUID);
	  $this->db->where_in('torderdocuments.OrderDocumentUID', $DocumentUID);
	  return $this->db->get()->result();
	}

	// function InsertDocumentPackageTrigger($ActionUID,$OrderUID,$AssignedToUserUID)
	// {
	// 	//get order details
	// 	$order_details = $this->get_orderdetails($OrderUID);
	// 	$CountyUID = $this->getCountyUID_ByOrderUID($OrderUID);
	// 	$CityUIDs = $this->getCityUID_ByOrderUID($OrderUID);

	// 	//get doc package details
	// 	$DocPackage = $this->getDocumentPackagesByOrder($order_details->CustomerUID,$order_details->ProductUID,$order_details->SubProductUID,$order_details->OrderTypeUID,$order_details->StateUID,$CountyUID,$CityUIDs,$order_details->PropertyZipcode);
	// 	//loop for insert multiple doc package rows
	// 	foreach ($DocPackage as $key => $value) 
	// 	{
	// 	//get mDocPackageDelivery details
	// 		$DocPackageDelivery = $this->db->select('*')->from('mDocPackageDelivery')->where(array('DocPackageUID'=>$value->DocPackageUID,'Auto'=>1,'DeliveryTrigger'=>$ActionUID))->get()->row();
				
	// 			if(!empty($DocPackageDelivery))
	// 			{
	// 			//insert tDocumentDeliveryQueue
	// 				$tDocumentDeliveryQueue = array(
	// 					'OrderUID' => $OrderUID,
	// 					'PackageUID' => $DocPackageDelivery->DocPackageUID,
	// 					'DocumentTypeUIDs' => $value->DocumentTypes,
	// 					'SlaActionUID' => $ActionUID,
	// 					'DeliveryStatus' => 'NotSend',
	// 					'DeliveryType' => $DocPackageDelivery->DeliveryMode,
	// 					'CreatedOn'=> date('Y-m-d H:i:s'),
	// 					'CreatedBy' => $AssignedToUserUID, 
	// 				);
	// 				$this->db->insert('tDocumentDeliveryQueue',$tDocumentDeliveryQueue);				
	// 			}
	// 	}
	// }


	function getClosingCurrentQueue_MobileApp($OrderUID)
	{

		$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		if (!empty($tOrders) && in_array($tOrders->StatusUID, [$this->config->item('keywords')['Cancelled']])  ) {
			return $this->config->item('Closign Current Queue')[17];	
		}

		/*Check is Schedule is completed*/
		$sql = "SELECT CASE WHEN EXISTS(
		SELECT OrderUID FROM torderassignment WHERE OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Scheduling']."' AND WorkflowStatus = 5)
		THEN 1 ELSE 0
		END AS ScheduleComplete,
		CASE WHEN EXISTS(
		SELECT tOrderschedule.OrderUID FROM tOrderschedule
		JOIN torderabstractor ON torderabstractor.AbstractorOrderUID = tOrderschedule.AbstractorOrderUID 
		JOIN tOrderscheduleBorrower ON tOrderscheduleBorrower.ScheduleUID=tOrderschedule.ScheduleUID
		WHERE tOrderschedule.OrderUID = $OrderUID AND tOrderschedule.ScheduleStatus NOT IN ('Cancel', 'Complete')) 
		THEN 1 ELSE 0 
		END AS SettlementAgentAssigned,
		CASE WHEN EXISTS(
		SELECT OrderUID FROM tOrdersign WHERE OrderUID = $OrderUID AND tOrdersign.SigningStatus IN ('Cancel')) 
		THEN 1 ELSE 0 
		END AS SigningCancelled,
		CASE WHEN EXISTS(
		SELECT OrderUID FROM tOrdersign WHERE OrderUID = $OrderUID AND tOrdersign.SigningStatus IN ('sign')) 
		THEN 1 ELSE 0 
		END AS SigningCompleted,
		CASE WHEN EXISTS(
		SELECT OrderUID FROM tOrdershipping WHERE OrderUID = $OrderUID AND tOrdershipping.IsShipped = 1) 
		THEN 1 ELSE 0 
		END AS ShipmentDone,
		CASE WHEN EXISTS( 
		SELECT OrderUID FROM torderassignment WHERE torderassignment.OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Signing']."' AND torderassignment.WorkflowStatus = 5)
		THEN 1 ELSE 0
		END AS SigningWorkflowCompleted,
		CASE WHEN 
			NOT EXISTS( SELECT OrderUID FROM torderassignment WHERE torderassignment.OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Signing']."')
		THEN 1 ELSE 0
		END AS Signing_NotAssigned,
		CASE WHEN EXISTS( SELECT OrderUID FROM torderassignment WHERE torderassignment.OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Signing']."' AND torderassignment.WorkflowStatus = 5)
		THEN 1 ELSE 0 
		END AS Signing_Completed,
		CASE WHEN EXISTS( SELECT OrderUID FROM torderassignment WHERE torderassignment.OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Shipping']."' )
		THEN 1 ELSE 0 
		END AS Shipment_of_title,
		CASE WHEN EXISTS( SELECT OrderUID FROM torderassignment WHERE torderassignment.OrderUID = $OrderUID AND torderassignment.WorkflowModuleUID = '".$this->config->item('WorkflowModuleUID')['Shipping']."' AND torderassignment.WorkflowStatus = 5)
		THEN 1 ELSE 0 
		END AS Shipment_complete";
		$result = $this->db->query($sql)->row();

		if ($result->SettlementAgentAssigned == 0 && $result->SigningCancelled == 1 && $result->SigningCompleted == 0 ) {
			return $this->config->item('Closign Current Queue')[5];	
		}
		if (($result->SettlementAgentAssigned == 0 && $result->SigningCompleted == 1) || $result->SigningWorkflowCompleted == 1 ) {
			return $this->config->item('Closign Current Queue')[1];	
		}

		
		if ($result->SettlementAgentAssigned == 1 ) {

			/*Individual Signing Status*/
			$this->db->select('*')->from('tOrdersign');
			$this->db->where(['OrderUID'=>$OrderUID]);
			$this->db->where_not_in('SigningStatus', ['Cancel', 'Sign']);
			$this->db->order_by('SignUID', 'DESC');
			$this->db->limit(1);
			$SigningDetails = $this->db->get()->result();
			$Currentworkflows = [];
			foreach ($SigningDetails as $key => $value) {
				
				if ((empty($value->IsDocstoLender) || $value->IsDocstoLender == '1') && $value->IsPostClosingComplete=='1' && $value->IsCriticalDocsBack=='1' && $value->IsSignConfirmDone=='1' && $value->Is2hoursCheck=='1' && $value->IsLogisticsCall=='1' && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[6]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && $value->IsCriticalDocsBack=='1' && $value->IsSignConfirmDone=='1' && $value->Is2hoursCheck=='1' && $value->IsLogisticsCall=='1' && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[7]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && $value->IsSignConfirmDone=='1' && $value->Is2hoursCheck=='1' && $value->IsLogisticsCall=='1' && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[8]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && $value->Is2hoursCheck=='1' && $value->IsLogisticsCall=='1' && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[9]->Queue;	

				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && empty($value->Is2hoursCheck) && $value->IsLogisticsCall=='1' && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[10]->Queue;	

				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && empty($value->Is2hoursCheck) && empty($value->IsLogisticsCall) && $value->IsDocsShipped=='1' && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[11]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && empty($value->Is2hoursCheck) && empty($value->IsLogisticsCall) && empty($value->IsDocsShipped) && $value->IsAssigned=='1' && $value->IsPreClosingComplete=='1') {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[12]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && empty($value->Is2hoursCheck) && empty($value->IsLogisticsCall) && empty($value->IsDocsShipped)&& empty($value->IsAssigned) && $value->IsPreClosingComplete=='1' ) {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[13]->Queue;	
				}
				else if (empty($value->IsDocstoLender) && empty($value->IsPostClosingComplete) && empty($value->IsCriticalDocsBack) && empty($value->IsSignConfirmDone) && empty($value->Is2hoursCheck) && empty($value->IsLogisticsCall) && empty($value->IsDocsShipped) && empty($value->IsAssigned) && empty($value->IsPreClosingComplete)) {
					$Currentworkflows[] =  $this->config->item('Closign Current Queue')[14]->Queue;	


				}


			}
			if (!empty($Currentworkflows)) {
				$Queue = implode(",", $Currentworkflows);
				return (object)['Queue'=>$Queue, 'color'=>'#428933'];				
			}
		}

		if ($result->SettlementAgentAssigned == 1) {
			return $this->config->item('Closign Current Queue')[4];
		}

		if (($result->Shipment_of_title == 1 && $result->Shipment_complete == 1) || $result->SigningWorkflowCompleted == 1 ) {
			return $this->config->item('Closign Current Queue')[2];	
		}

		if (($result->Shipment_of_title == 1) ) {
			return $this->config->item('Closign Current Queue')[15];	
		}

		$this->db->select('WorkflowModuleUID');
		$this->db->from('torderassignment');
		$this->db->where('OrderUID',$OrderUID);
		$this->db->where('WorkflowStatus',5);
		$this->db->order_by('WorkflowModuleUID','DESC');
		$query =$this->db->get()->row();

		if($query->WorkflowModuleUID == 9){
			return $this->config->item('Closign Current Queue')[3];	
		}
		else if($query->WorkflowModuleUID == 10){
			return $this->config->item('Closign Current Queue')[1];	
		}
		else if($query->WorkflowModuleUID == 11){
			return $this->config->item('Closign Current Queue')[2];	
		}

		if($result->SettlementAgentAssigned == 1){
			return $this->config->item('Closign Current Queue')[2];	
		}

		if($result->SettlementAgentAssigned == 1){		
			return $this->config->item('Closign Current Queue')[4];
		} 
      return $this->config->item('Closign Current Queue')[0];

	}
	/**
		* @description Update On Fieldd Change
		* @param FieldName OrderUID
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return nothing 
		* @since  May 09 2020
		* @version  
		*/ 
		function updateOrderDocTypesFieldchange($FieldName,$OrderUID){
			$this->db->select('*');
			$this->db->from('torderdocuments');
			$this->db->where('OrderUID',$OrderUID);
			$this->db->like('ReplacedFields',$FieldName,'both');
			$query = $this->db->get()->result();
	
			if(!empty($query)){
				$this->db->where_in('OrderDocumentUID',array_column($query,'OrderDocumentUID'));
				$this->db->update('torderdocuments',array('IsChanged'=>1,'DocumentStatus'=>'ToUpdate'));
			}
	}

	/** 
    * @param OrderUID, Type
    * @author Periyasamy S <periyasamy.s@avanzegroup.com>
    *  @since 10/06/2020
    */
	function getMailOrderLevelClientType($OrderUID, $Type)
	{
	  $Type = (empty($Type)) ? 0 : $Type;	
	  return $this->db->query('SELECT CONCAT_WS("", "(", CustomerName, ")", "(Customer) ", mClientContactMethod.Details) AS Email, CustomerName, CustomerNumber FROM (`tOrders`) LEFT JOIN `mCustomers` ON `mCustomers`.`CustomerUID` = `tOrders`.`CustomerUID` LEFT JOIN `mClientContact` ON `mCustomers`.`CustomerUID` = `mClientContact`.`CustomerUID` LEFT JOIN mClientContactMethod ON mClientContactMethod.ClientContactUID = mClientContact.ClientContactUID INNER JOIN mContactMethod ON mContactMethod.ContactMethodUID = mClientContactMethod.ContactMethodUID AND mContactMethod.IsMail = 1 WHERE `mClientContact`.`ContactTypeUID` IN ('.$Type.')  AND `tOrders`.`OrderUID` = '.$OrderUID.' AND mClientContactMethod.Details != "" GROUP BY `Email`')->result();
	}

	/** 
    * @param OrderUID, RoleType
    * @author Periyasamy S <periyasamy.s@avanzegroup.com>
    *  @since 10/06/2020
    */
	function getMailOrderLevelRoleCategory($OrderUID, $RoleCategory)
	{
	  if(!empty($RoleCategory))
	  {
	  	$RoleCat = explode(',',$RoleCategory);	
	  	foreach ($RoleCat as $key => $value) {
	  		$rwh[] = 'FIND_IN_SET('.$value.', mabstractor.RoleCategoryUID)';	
	  	}
	  	$where = '( '.implode(' OR ', $rwh).' )';
	  }	else {
	  	$where = '';
	  }

	  $this->db->select('mabstractorcontact.Email AS Email,CONCAT_WS("", mabstractor.AbstractorFirstName, " ", mabstractor.AbstractorLastName) AS AbstractorName,mabstractor.AbstractorCompanyName,mabstractorcontact.Email, mabstractor.PaypalEmailID', false);
	  $this->db->from('torderabstractor');
	  $this->db->join('mabstractorcontact','mabstractorcontact.AbstractorContactUID = torderabstractor.ContactUID', 'left');
	  $this->db->join('mabstractor','mabstractor.AbstractorUID = torderabstractor.AbstractorUID', 'left');
	  $this->db->where('mabstractor.OverAllStatus','Active');
	  if(!empty($where)) {
       $this->db->where($where,NULL,FALSE);
	  }
	  $this->db->where('mabstractor.Email IS NOT NULL',NULL,FALSE);
	  $this->db->where('torderabstractor.OrderUID',$OrderUID);
	  $this->db->group_by('mabstractorcontact.Email');
	  return $this->db->get()->result();
	}

	/** 
    * @param typeogdoc
    * @author alwin l <alwin.l@avanzegroup.com>
    *  @since 11/06/2020
    */
	function getDocumentTypeUIDByDocTYpe($typeofDoc)
	{
		return $this->db->select('DocumentTypeUID')->from('mdocumenttypes')->where(array('DocumentTypeName'=>$typeofDoc,'Active'=>1))->get()->row()->DocumentTypeUID;
	}

	/** 
    * @param typeogdoc
    * @author alwin l <alwin.l@avanzegroup.com>
    *  @since 12/06/2020
    */
	function getDocTypeByDocumentTypeUID($DocumentTypeUID)
	{
		return $this->db->select('DocumentTypeName')->from('mdocumenttypes')->where(array('DocumentTypeUID'=>$DocumentTypeUID,'Active'=>1))->get()->row()->DocumentTypeName;
	}
	/* @purpose: Get document type name by document UID  @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: June 12 2020 */
	function GetDocumentTypeNameByUID($DocumentTypeUID)
	{
		return $this->db->select('*')->from('mdocumenttypes')->where(array('DocumentTypeUID'=>$DocumentTypeUID,'Active'=>1))->get()->row()->DocumentTypeCode;
	}

	function GetDocumentTypeCodeByUID($DocumentTypeUID)
	{
		return $this->db->select('*')->from('mdocumenttypes')->where(array('DocumentTypeUID'=>$DocumentTypeUID,'Active'=>1))->get()->row()->DocumentTypeCode;
	}

	function GetDocumentTypeUIDByCode($DocumentTypeCode)
	{
		return $this->db->select('*')->from('mdocumenttypes')->where(array('DocumentTypeCode'=>$DocumentTypeCode,'Active'=>1))->get()->row()->DocumentTypeUID;
	}

	function getDocumentTypeUIDByDocTypeIntegration($DocumentTypeName,$OrderSourceUID)
	{
		$this->db->select('*');
		$this->db->from('mdocumenttypes');
		$this->db->join('mDocumentCategory','mdocumenttypes.DocumentCategoryUID = mDocumentCategory.DocumentCategoryUID','LEFT');
		$this->db->where(array('mDocumentCategory.OrderSourceUID'=> $OrderSourceUID,'mdocumenttypes.Active'=> 1,'mdocumenttypes.DocumentTypeName'=> $DocumentTypeName));
		$mdocumenttypes = $this->db->get()->row()->DocumentTypeUID;
		return $mdocumenttypes;
	}

	function GetDocumentTypeUIDByCode_Integration($DocumentTypeCode,$OrderSourceUID)
	{
		$this->db->select('*');
		$this->db->from('mdocumenttypes');
		$this->db->join('mDocumentCategory','mdocumenttypes.DocumentCategoryUID = mDocumentCategory.DocumentCategoryUID','LEFT');
		$this->db->where(array('mDocumentCategory.OrderSourceUID'=> $OrderSourceUID,'mdocumenttypes.Active'=> 1,'mdocumenttypes.DocumentTypeCode'=> $DocumentTypeCode));
		$mdocumenttypes = $this->db->get()->row()->DocumentTypeUID;
		return $mdocumenttypes;
	}

	/**
		*@description Function to get Revise Queues
		*
		* @param $OrderUID, $CustomerUID, $SubProductUID
		* 
		* @throws no exception
		* @author Periyasamy <periyasamy.s@avanzegroup.com>
		* @return bool 
		* @since 16/6/2020 
		* @version Closing Dynamic Queues 
		*
	*/ 
	function getReverseQueue($QueueUID, $CustomerUID, $SubProductUID)
	{
	   $this->db->select('mWorkflowQueues.ReverseQueueUIDs, GROUP_CONCAT(DISTINCT mQueues.QueueName) AS QueueName, GROUP_CONCAT(DISTINCT mWorkflowQueues.TriggerQueueUIDs) AS TriggerQueueUIDs');
	   $this->db->from('mWorkflowQueues');
	   $this->db->join('mQueues','FIND_IN_SET(mQueues.QueueUID, mWorkflowQueues.ReverseQueueUIDs)','INNER',FALSE);
	   $this->db->where('mWorkflowQueues.QueueUID', $QueueUID);	
	   // $this->db->where('mWorkflowQueues.CustomerUID', $CustomerUID);	
	   // $this->db->where('mWorkflowQueues.SubProductUID', $SubProductUID);	
	   return $this->db->get()->row();
	}	

	/**
		*@description Function to get Revise Queues
		*
		* @param $OrderUID, $CustomerUID, $SubProductUID
		* 
		* @throws no exception
		* @author Periyasamy <periyasamy.s@avanzegroup.com>
		* @return bool 
		* @since 16/6/2020 
		* @version Closing Dynamic Queues 
		*
	*/ 
	function getQueueNames($QueueUIDs)
	{
		if (empty($QueueUIDs)) 
		{
			return [];
		}
		$this->db->select('mQueues.QueueName');
		$this->db->from('mQueues');
		$this->db->where_in('mQueues.QueueUID', $QueueUIDs);
		$result = $this->db->get()->result_array();

		return array_column($result, "QueueName");
	}	

	/**
		*@description Function to insert WorkflowQueues
		*
		* @param $OrderUID, $CustomerUID, $SubProductUID, $ModuleName
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return bool 
		* @since 15/6/2020 
		* @version Closing Dynamic Queues 
		*
	*/ 
	function insertWorkflowQueues($OrderUID, $CustomerUID, $SubProductUID, $ModuleName, $ScheduleUID, $SkipTransaction = false)
	{
		
		$this->db->select('*');
		$this->db->from('mWorkflowQueues');
/*		$this->db->where('CustomerUID', $CustomerUID);
		$this->db->where('SubProductUID', $SubProductUID);*/
		$this->db->where('ModuleName', $ModuleName);
		$QueueUIDs = $this->db->get()->result();

		if ($SkipTransaction == false) 
		{
			
			/*Transaction Begin*/
			$this->db->trans_begin();

			foreach ($QueueUIDs as $key => $queueuid) {


				/*$torderqueues = [];
				$torderqueues['OrderUID'] = $OrderUID;
				$torderqueues['QueueUID'] = $queueuid->QueueUID;
				$torderqueues['ScheduleUID'] = $ScheduleUID;
				$torderqueues['RaisedReasonUID'] = 1;
				$torderqueues['RaisedRemarks'] = $ModuleName;
				$torderqueues['RaisedDateTime'] = date('Y-m-d H:i:s');
				$torderqueues['RaisedByUserUID'] = $this->loggedid;

				$this->common_model->save('tOrderQueues', $torderqueues);*/
				if(!empty($queueuid->CompleteQueueUIDs)) // Complete Previous Queue
				{
				  if(empty($ScheduleUID)) {
				   $ScheduleUID	= 0;
				  } 
				  $CompleteQueues = explode(',', $queueuid->CompleteQueueUIDs);	
				  if (!empty($CompleteQueues)) 
				  {
				  	$cmp_queue = array('QueueStatus'=>'Completed');
				  	$this->db->where('OrderUID',$OrderUID);
				  	$this->db->where('( ScheduleUID = '.$ScheduleUID.' OR ScheduleUID = 0)',NULL,FALSE);
				  	$this->db->where_in('QueueUID',$CompleteQueues,FALSE);
				  	$this->db->update('tOrderQueues',$cmp_queue);
				  }

				} 
				if(!empty($queueuid->TriggerQueueUIDs)) { // Trigger Insert New Queue
				  insertTriggerQueues($ScheduleUID,$CustomerUID,$SubProductUID,$OrderUID,$queueuid->TriggerQueueUIDs);
				}

			}

		}
		
		if ($SkipTransaction == false) 
		{
			/*verify is valid transaction*/
			if ($this->db->trans_status()) {
				/*commit transaction*/
				$this->db->trans_commit();
				return true;
			}
			else{
				/*rollback transaction*/
				$this->db->trans_rollback();
				return false;
			}
			
		}
		else
		{
			return true;
		}
		

	}

	/**
	 *@description Function to get Vendor Particular Contact Details
	 *
	 * @param $AbstractorUID, $ContactUID
	 * @throws no exception
	 * @author Periyasamy <Periyasamy.s@avanzegroup.com>
	 * @return Get Vendor Contact
	 * @since 19/6/2020 
	*/ 
	function getVendorContactByUID($AbstractorUID, $ContactUID)
	{
	   $this->db->select('mabstractorcontact.*,mDesignation.DesignationName');	
	   $this->db->where('AbstractorContactUID', $ContactUID);	
	   $this->db->where('AbstractorUID', $AbstractorUID);
	   $this->db->join('mDesignation','mDesignation.DesignationUID = mabstractorcontact.DesignationUID','LEFT');
	   return $this->db->get('mabstractorcontact')->row();	
	}

	/**
	 *@description Function to Complete WorkflowQueues
	 *
	 * @param $OrderUID, $CustomerUID, $SubProductUID
	 * 
	 * @throws no exception
	 * @author Periyasamy <Periyasamy.s@avanzegroup.com>
	 * @return Completed Queue 
	 * @since 16/6/2020 
	 * @version Closing Dynamic Queues 
	 *
	*/ 
	function getCompleteQueue($QueueUID, $CustomerUID, $SubProductUID)
	{
	   $this->db->where('WorkflowModuleUID IS NULL');	
	   $this->db->where('ModuleName IS NULL');	
	   $this->db->where('QueueUID', $QueueUID);	
	   // $this->db->where('SubProductUID', $SubProductUID);	
	   // $this->db->where('CustomerUID', $CustomerUID);	
	   $result = $this->db->get('mWorkflowQueues')->row();
	   if(is_object($result))
	   {
	   	 return $result;
	   } else {
	   	 return [];
	   }
	}

	function getOrderQueueWorkflows($OrderUID){
		$this->db->select('mQueues.WorkflowModuleUID');
		$this->db->from('tOrderQueues');
		$this->db->join('mQueues','mQueues.QueueUID = tOrderQueues.QueueUID','left');
		$this->db->where('tOrderQueues.OrderUID',$OrderUID);
		$this->db->group_by('mQueues.WorkflowModuleUID');
		return $this->db->get()->result();
	}

	/**
		*@description Function to completeClosingWorkflow
		*
		* @param $OrderUID, $WorkflowModuleUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return bool 
		* @since 21-6-2020 
		* @version Dynamic Queues 
		*
	*/ 
	function completeClosingWorkflow($OrderUID, $WorkflowModuleUID)
	{
	

		$ClosingWorkflows = [];
		$ClosingWorkflows[] = $this->config->item('WorkflowModuleUID')['Scheduling'];
		$ClosingWorkflows[] = $this->config->item('WorkflowModuleUID')['Signing'];
		$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		$this->db->where(['CustomerUID'=>$tOrders->CustomerUID, 'SubProductUID'=>$tOrders->SubProductUID]);
		$workflows = $this->db->get('mcustomerworkflowmodules')->result_array();

		$CustomerWorkflows = array_filter(array_column($workflows, "WorkflowModuleUID"));

		/*Check is scheduling workflow*/
		if (!in_array($WorkflowModuleUID, $ClosingWorkflows) || !in_array($WorkflowModuleUID, $CustomerWorkflows)) {
			return false;
		}

		/*Transaction Begin*/
		$this->db->trans_begin();
		
		
		$assignuser = $this->common_model->check_is_assigned($OrderUID,$WorkflowModuleUID);  
		if(count($assignuser) == 0) {
			$is_order_assigned = $this->common_model->SelfAssignOrderToUser($OrderUID,$WorkflowModuleUID,$this->loggedid); 
		} else {
			$is_order_assigned = $this->common_model->UpdateSelfAssignOrderToUser($OrderUID,$WorkflowModuleUID,$this->loggedid); 
		}


		$workflowstatus = array("WorkflowStatus"=>5,"CompleteDateTime"=>Date('Y-m-d H:i:s',strtotime("now")));

		
		$this->db->where(array("torderassignment.OrderUID"=>$OrderUID,"torderassignment.WorkflowModuleUID !=" => 4));
		$this->db->where_in("torderassignment.WorkflowModuleUID",$WorkflowModuleUID);
		$this->db->update('torderassignment',$workflowstatus);

		
		if (in_array($this->config->item('WorkflowModuleUID')['Scheduling'], [$WorkflowModuleUID])) {

			$this->db->where('OrderUID', $OrderUID);
			$this->db->where_not_in('ScheduleStatus', ['Cancel', 'Complete']);
			$tOrderschedule = $this->db->get('tOrderschedule')->result();

			/*Complete all scheudles*/
			foreach ($tOrderschedule as $key => $value) {
				$this->common_model->save('tOrderschedule', ['ScheduleStatus'=>'Complete'], ['ScheduleUID'=>$value->ScheduleUID]);

				$this->common_model->save('torderabstractor', ['OrderStatus'=>5, 'CompletedDateTime'=>date('Y-m-d H:i:s')], ['AbstractorOrderUID'=>$value->AbstractorOrderUID]);
			}
		}

		if (in_array($this->config->item('WorkflowModuleUID')['Signing'], [$WorkflowModuleUID])) {


			$this->db->where('OrderUID', $OrderUID);
			$this->db->where_not_in('SigningStatus', ['Cancel', 'Sign']);
			$tOrdersign = $this->db->get('tOrdersign')->result();

			$ScheduleUIDs = [];
			foreach ($tOrdersign as $key => $value) {
				$ScheduleUIDs[] = $value->ScheduleUID;
			}


			/* @author Parthasarathy M insert workflowqueues at triggers */
			$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$OrderUID]);

			$this->common_model->insertWorkflowQueues($OrderUID, $tOrders->CustomerUID, $tOrders->SubProductUID, "Signing Complete", 0);



			$this->db->where('OrderUID', $OrderUID);
			$this->db->where_not_in('SigningStatus', ['Cancel', 'Sign']);
			$this->db->update('tOrdersign', ['SigningStatus'=>'Sign']);

			//insert sla action details for sign complete
			$WorkflowUID = $this->config->item('WorkflowModuleUID')['Signing'];
			/*@Desc insert_slaaction with OrderTypeUID @Author Jainulabdeen @Updated on June 5 2020*/
			$this->db->where(array("OrderStatus !="=>5,"OrderStatus !=" => 4,"OrderUID"=>$OrderUID));
			$tOrders = $this->db->get('torderabstractor')->result();
			foreach ($tOrders as $key => $value) {
				$this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['WorkflowModuleUID'][$WorkflowUID]['Completed'],$this->session->userdata('UserUID'),$value->OrderTypeUID);
			}

			/*insert sla action details end*/

			if (!empty($ScheduleUIDs)) {				
				$this->db->where_in('ScheduleUID', $ScheduleUIDs);
				$tOrderschedule = $this->db->get('tOrderschedule')->result();
			}

			foreach ($tOrderschedule as $key => $value) {
				$this->common_model->save('tOrderschedule', ['ScheduleStatus'=>'Complete'], ['ScheduleUID'=>$value->ScheduleUID]);

				/*insert sla action details for sign complete*/
				$WorkflowUID = $this->config->item('WorkflowModuleUID')['Scheduling'];
				/*@Desc insert_slaaction with OrderTypeUID @Author Jainulabdeen @Updated on June 5 2020*/
				$this->db->where(array("OrderStatus !="=>5,"OrderStatus !=" => 4,"OrderUID"=>$OrderUID));
				$tOrders = $this->db->get('torderabstractor')->result();
				foreach ($tOrders as $key => $value) {
					$this->common_model->insert_slaaction($OrderUID,$this->config->item('SLAaction')['WorkflowModuleUID'][$WorkflowUID]['Completed'],$this->session->userdata('UserUID'),$value->OrderTypeUID);
				}

							//insert sla action details end

				$this->common_model->save('torderabstractor', ['OrderStatus'=>5, 'CompletedDateTime'=>date('Y-m-d H:i:s')], ['AbstractorOrderUID'=>$value->AbstractorOrderUID]);
			}


		}	

		$mworkflowmodules = $this->common_model->get_row('mworkflowmodules', ['WorkflowModuleUID'=>$WorkflowModuleUID]);

		if (!empty($mworkflowmodules)) 
		{
			
			$data1['ModuleName']=$mworkflowmodules->WorkflowModuleName.' '.'Complete-Status';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='torderassignment';
			$data1['OrderUID']=$OrderUID;
			$data1['UserUID']=$this->loggedid; 
			$data1['OldValue']=''; 
			$data1['FieldUID']='732';
			$data1['NewValue']='Completed';                 
			$this->common_model->Audittrail_insert($data1);
		}

		/*verify is valid transaction*/
		if ($this->db->trans_status()) {
			/*commit transaction*/
			$this->db->trans_commit();
			return true;
		}
		else{
			/*rollback transaction*/
			$this->db->trans_rollback();
			return false;
		}


	}

	/**
		*@description Function to closingCurrentWorkflow
		*
		* @param $OrderUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return Status<String> 
		* @since 21-5-2020 
		* @version Dynamic Queues 
		*
	*/ 
	function closingCurrentWorkflow($OrderUID)
	{
		$tOrders = $this->common_model->get_row('tOrders', ['OrderUID'=>$OrderUID]);
		/* @desc Check Cancel Pending Request @author Yagavi G <yagavi.g@avanzegroup.com> @since Sept 3rd 2020 */
		$PendingCancelRequest = $this->CheckOrderCancelPendingRequest($OrderUID);
		if(!empty($PendingCancelRequest)){
			return $this->config->item('Closign Current Queue')[18];
		}

		if (!empty($tOrders) && in_array($tOrders->StatusUID, [$this->config->item('keywords')['Order Completed']])  ) {
			return $this->config->item('Closign Current Queue')[16];
		}
		if (!empty($tOrders) && in_array($tOrders->StatusUID, [$this->config->item('keywords')['Cancelled']])  ) {
			return $this->config->item('Closign Current Queue')[17];
		}

		$StatusColors = [];
		$StatusColors["New"] = "#4285f4";
		$StatusColors['Scheduling'] = "#2ECC71";
		$StatusColors['Pre-Closing'] = "#428933";
		$StatusColors['Signing'] = "#6600EF";
		$StatusColors['Post-Closing'] = "#428933";
		$StatusColors['Order Complete'] = "#428933";

		$Status['Queue'] = "New";
		$Status['color'] = $StatusColors['New'];


		if (empty($OrderUID)) 
		{
			return (Object)$Status;
		}
		$WorkflowQueue = [];
		$WorkflowQueue['New'] = [
			$this->config->item('Queues')['Order Accept']
		];

		$WorkflowQueue['Scheduling'] = [
			$this->config->item('Queues')['Ready to Schedule/Reschedule'],
			$this->config->item('Queues')['Awaiting Vendor']		
		];

		$WorkflowQueue['Pre-Closing'] = [
			$this->config->item('Queues')['Awaiting Closing Instructions'],
			$this->config->item('Queues')['Prelim CD/HUD'],
			$this->config->item('Queues')['Final CD/HUD Waiting'],
			$this->config->item('Queues')['Final CD/HUD'],
		];

		$WorkflowQueue['Signing'] = [
			$this->config->item('Queues')['Awaiting Vendor'],
			$this->config->item('Queues')['Loan Docs Packaging'],
			$this->config->item('Queues')['Awaiting Final Lender Docs'],
			$this->config->item('Queues')['Docs Sent to Notary/Attorney'],
		];

		$WorkflowQueue['Post-Closing'] = [
			$this->config->item('Queues')['Awaiting Signing'],
			$this->config->item('Queues')['Pre-Disbursement'],
			$this->config->item('Queues')['Awaiting Executed Closing Docs'],
			$this->config->item('Queues')['Collateral Shipped'],
			$this->config->item('Queues')['Review for Funding'],
			$this->config->item('Queues')['Not Clear to Fund'],
			$this->config->item('Queues')['Awaiting Posting'],
			$this->config->item('Queues')['Disbursements'],
			$this->config->item('Queues')['Send to Recording'],
			$this->config->item('Queues')['Complete Recording'],
			$this->config->item('Queues')['Order Complete'],

		];


		foreach ($WorkflowQueue as $key => $queue) {

			if (!empty($queue)) 
			{
				$this->db->where_in("QueueUID", $queue);
				$this->db->where('OrderUID', $OrderUID);
				$this->db->where('QueueStatus', 'Pending');
				$result = $this->db->get('tOrderQueues')->row();

				if (!empty($result)) 
				{
					$Status['Queue'] = $key;
					$Status['color'] = $StatusColors[$key];
					break;
				}
				
			}
		}

		return (Object)$Status;
	}

	/**
	* @desc Check Cancel Pending Request
	* @param Customer,Product,SubProduct
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @since Sept 3rd 2020 
	*/

	function CheckOrderCancelPendingRequest($OrderUID)
	{
		$this->db->select('*');
		$this->db->from('torderapprovals');
		$this->db->join('tOrders','tOrders.OrderUID = torderapprovals.OrderUID');
		$this->db->where('torderapprovals.OrderUID',$OrderUID);
		$this->db->where('torderapprovals.ApprovalStatus', '0');
		$this->db->where('torderapprovals.ApprovalFunction', 'Order Cancellation');
		$this->db->where('tOrders.StatusUID !=', '110');
		return $this->db->get()->row();
	}

	// @Desc Group User Mail D2T-631 @Uba @On Jun 27 2020
	function GetGroupUserEmails($GroupUIDs){
		if(!empty($GroupUIDs)){
			$this->db->select('musers.UserEmailID as Email');
			$this->db->from('mgroupusers');
			$this->db->join('musers','musers.UserUID = mgroupusers.GroupUserUID');
			$this->db->where_in('mgroupusers.GroupUID',$GroupUIDs);
			$GroupUsers = $this->db->get()->result();
			return array_unique(array_column($GroupUsers,'Email'));
		}else{
			return [];
		}
	}

	/**
  * @author Ubakarasamy
  * @return WorkflowModules
  * @param Customer,Product,SubProduct
  */
  function GetWorkflowsByCustomerProductSubProduct($CustomerUID,$ProductUID,$SubProductUID)
  {

      $this->db->select('mworkflowmodules.*');
      $this->db->from('mcustomerworkflowmodules'); 
      $this->db->join('mworkflowmodules','mworkflowmodules.WorkflowModuleUID = mcustomerworkflowmodules.WorkflowModuleUID','LEFT');
	  
	  if(!empty($CustomerUID)){
	  	$this->db->where_in('mcustomerworkflowmodules.CustomerUID',$CustomerUID);
	  }
      if(!empty($ProductUID)) {
        $this->db->where_in('mcustomerworkflowmodules.ProductUID',$ProductUID);
      }

      if(!empty($SubProductUID)) {
        $this->db->where_in('mcustomerworkflowmodules.SubProductUID',$SubProductUID);
      }
      $this->db->group_by('mworkflowmodules.WorkflowModuleUID');
      return $this->db->get()->result();
    
    return [];
  }


		//author alwin.l 7/13/2020
		//get workflow for customer default workflow
  	function get_group_workflows($UserUID)
	{
		/*FOR SUPERVISOR CHECK*/
		$where = '';
		$workflowroles = $this->common_model->getrole_workflows();


		if (in_array($this->RoleType, $this->config->item('SubProduct_RoleType'))) {
			//supervisor role
			$UserProducts = $this->common_model->_get_product_bylogin();
			if(!empty($UserProducts)): $where .= ' WHERE  mcustomerworkflowmodules.ProductUID IN ('.$UserProducts.')'; else: return array(); endif;

			// if($workflowroles != ''){
			// 	$where .= ' AND mworkflowmodules.WorkflowModuleUID IN ('.$workflowroles.')';
			// }

			$query = $this->db->query("SELECT mcustomerworkflowmodules.WorkflowModuleUID,WorkflowModuleName FROM mgroupcustomers JOIN mcustomerworkflowmodules ON mcustomerworkflowmodules.CustomerUID = mgroupcustomers.GroupCustomerUID AND mcustomerworkflowmodules.ProductUID = mgroupcustomers.GroupCustomerProductUID AND mcustomerworkflowmodules.SubProductUID = mgroupcustomers.GroupCustomerSubProductUID JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = mcustomerworkflowmodules.WorkflowModuleUID  ".$where." GROUP BY mcustomerworkflowmodules.WorkflowModuleUID");
			return $query->result_array();

		} else if (in_array($this->RoleType, $this->config->item('Agent'))) {
			//agent role
			if(!empty($workflowroles)) {
				$where .= ' AND mworkflowmodules.WorkflowModuleUID IN ('.$workflowroles.')';
			}
			$query = $this->db->query("SELECT mcustomerworkflowmodules.WorkflowModuleUID,WorkflowModuleName FROM mgroupcustomers JOIN mcustomerworkflowmodules ON mcustomerworkflowmodules.CustomerUID = mgroupcustomers.GroupCustomerUID AND mcustomerworkflowmodules.ProductUID = mgroupcustomers.GroupCustomerProductUID AND mcustomerworkflowmodules.SubProductUID = mgroupcustomers.GroupCustomerSubProductUID JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = mcustomerworkflowmodules.WorkflowModuleUID WHERE mgroupcustomers.GroupUID IN ( SELECT GroupUID FROM mgroupusers WHERE mgroupusers.GroupUserUID = '".$UserUID."' ) ".$where." GROUP BY mcustomerworkflowmodules.WorkflowModuleUID");
			return $query->result_array();

		}else if($this->common_model->GetMyOrdersQueue() == 1) {
			//own permissions
			if(!empty($workflowroles)) {
				$where .= ' AND mworkflowmodules.WorkflowModuleUID IN ('.$workflowroles.')';
			}
			$query = $this->db->query("SELECT mcustomerworkflowmodules.WorkflowModuleUID,WorkflowModuleName FROM mgroupcustomers JOIN mcustomerworkflowmodules ON mcustomerworkflowmodules.CustomerUID = mgroupcustomers.GroupCustomerUID AND mcustomerworkflowmodules.ProductUID = mgroupcustomers.GroupCustomerProductUID AND mcustomerworkflowmodules.SubProductUID = mgroupcustomers.GroupCustomerSubProductUID JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = mcustomerworkflowmodules.WorkflowModuleUID WHERE mgroupcustomers.GroupUID IN ( SELECT GroupUID FROM mgroupusers WHERE mgroupusers.GroupUserUID = '".$UserUID."' ) ".$where." GROUP BY mcustomerworkflowmodules.WorkflowModuleUID");
			return $query->result_array();

		}else{
			//admin role
			$query = $this->db->query('SELECT * FROM mworkflowmodules');
			return $query->result_array();
		}

	}

	function GetUserDefaultQueue($UserUID)
	{
		return $this->db->select('DefaultQueue')->from('musers')->where('UserUID',$UserUID)->get()->row()->DefaultQueue;

	}


	
   /**
    * @description Common way for filtering Product
    * @param ProductUID, CustomerUID, Workflow, Priority
    * @throws no exception
    * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
    * @return ARRAY 
    * @since  20 Jul 2020
    * @version  Doc Packaging
    */

	function productFiltering($CustomerUID,$Workflow,$ProductCategory){

		$this->db->select('mProducts.ProductUID,mProducts.ProductName');
		if(!empty($CustomerUID)){
			$this->db->from('mcustomerworkflowmodules');
			$this->db->join('mProducts','mProducts.ProductUID = mcustomerworkflowmodules.ProductUID','LEFT');
			// Condition: Customer
			if(!empty($CustomerUID) && is_array($CustomerUID))
			{
				$this->db->where_in('mcustomerworkflowmodules.CustomerUID',$CustomerUID);
			} else {
				$this->db->where('mcustomerworkflowmodules.CustomerUID',$CustomerUID);
			}
			// Condition: Customer Workflow
			if($Workflow)
			{
				$this->db->where('mcustomerworkflowmodules.WorkflowModuleUID',$Workflow);
			}
		}else{
			$this->db->from('mProducts');
		}

		// Condition: ProductCategory
		if($ProductCategory != '' && $ProductCategory == 'Title')
		{
			$this->db->where('(mProducts.IsClosingProduct IS NULL OR mProducts.IsClosingProduct = 0) AND (mProducts.IsRMSProduct IS NULL OR mProducts.IsRMSProduct = 0)',NULL,FALSE);
		}
		else if($ProductCategory != '' && $ProductCategory == 'Closing')
		{
			$this->db->where('mProducts.IsClosingProduct',1);
		}else if($ProductCategory != '' && $ProductCategory == 'RMS')
		{
			$this->db->where('mProducts.IsRMSProduct',1);
		}
		
		$this->db->group_by('ProductUID');
		return $this->db->get()->result();
	}


	 /**
    * @description Common way for filtering SubProduct
    * @param ProductUID, CustomerUID, Workflow, Priority
    * @throws no exception
    * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
    * @return ARRAY 
    * @since  20 Jul 2020
    * @version  Doc Packaging
    */

	function subproductFiltering($ProductUID,$CustomerUID='',$ProductCategory='')
		{
			$this->db->select('mSubProducts.SubProductUID,mSubProducts.SubProductName');
			$this->db->from('mSubProducts');

			if(!empty(array_filter($ProductUID))) 
			{
			  if(is_array($ProductUID))
			  {
			    $this->db->where_in("mSubProducts.ProductUID",$ProductUID);
			  }	else {
			    $this->db->where("mSubProducts.ProductUID",$ProductUID);
			  } 
		 	} 
		 	if(!empty($CustomerUID))
		 	{
		 	  $this->db->join('mCustomerProducts','mCustomerProducts.SubProductUID = mSubProducts.SubProductUID','INNER');	
		 	  if(is_array($CustomerUID))
		 	  {
		 	  	$this->db->where_in('mCustomerProducts.CustomerUID',$CustomerUID);
		 	  } else {
		 	    $this->db->where('mCustomerProducts.CustomerUID',$CustomerUID);
		 	  }
			}
			if(!empty($ProductCategory))
		 	{
				$this->db->join('mProducts','mProducts.ProductUID = mSubProducts.ProductUID','left');	
				// Condition: ProductCategory
				if($ProductCategory != '' && $ProductCategory == 'Title')
				{
					$this->db->where('(mProducts.IsClosingProduct IS NULL OR mProducts.IsClosingProduct = 0) AND (mProducts.IsRMSProduct IS NULL OR mProducts.IsRMSProduct = 0)',NULL,FALSE);
				}
				else if($ProductCategory != '' && $ProductCategory == 'Closing')
				{
					$this->db->where('mProducts.IsClosingProduct',1);
				}else if($ProductCategory != '' && $ProductCategory == 'RMS')
				{
					$this->db->where('mProducts.IsRMSProduct',1);
				}
			}
				
		 	$this->db->group_by('mSubProducts.SubProductUID');
			return $this->db->get()->result();
		}


	/**
    * @description Common way for Datatable handling
    * @param Datatable params
    * @throws no exception
    * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
    * @return ARRAY 
    * @since  25 Jul 2020
    * @version  May CR
    */
	function get_post_input_data()
	{
		$post['length'] = $this->input->post('length');
		$post['start'] = $this->input->post('start');
		$search = $this->input->post('search');
		$post['search_value'] = $search['value'];
		$post['order'] = $this->input->post('order');
		$post['draw'] = $this->input->post('draw');
		return $post;
	}


  /**
  * @purpose : To get api event workflow settings based on CustomerUID and SubProductUID
  *
  * @param CustomerUID as string
  * @param SubProductUID as string
  * @param EventCode as string
  * 
  * @throws no exception
  * @author D.Samuel Prabhu <samuel.prabhu@avanzegroup.com>
  * @return array 
  * @since 27 July 2020 
  *
  */ 

  function GetApiEventQueueDetail($CustomerUID,$SubProductUID,$EventCode)
  {
      $this->db->select('*'); 
      $this->db->from('mAPIEventQueues');
      $this->db->where (['mAPIEventQueues.CustomerUID' => $CustomerUID, 'mAPIEventQueues.SubProductUID' => $SubProductUID, 'EventCode' => $EventCode]);
      $query = $this->db->get();
      return $query->row(); 
  }

  /**
  * @purpose : To get workflow settings based on CustomerUID and SubProductUID
  *
  * @param CustomerUID as string
  * @param SubProductUID as string
  * @param QueueUID as string
  * 
  * @throws no exception
  * @author D.Samuel Prabhu <samuel.prabhu@avanzegroup.com>
  * @return array 
  * @since 27 July 2020 
  *
  */ 

  function GetWorkflowEventQueueDetail($CustomerUID,$SubProductUID, $QueueUID)
  {
      $this->db->select('*'); 
      $this->db->from('mWorkflowQueues');
      $this->db->where (['mWorkflowQueues.CustomerUID' => $CustomerUID, 'mWorkflowQueues.SubProductUID' => $SubProductUID, 'mWorkflowQueues.QueueUID' => $QueueUID]);
      $query = $this->db->get();
      return $query->row(); 
  }

/**
    * @description getting Document naming convention fields
    * @param no params
    * @throws no exception
    * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
    * @return ARRAY 
    * @since  31 Jul 2020
    * @version  Doc Packaging
    */
	function getDocumentNamingConventions()
	{
		$this->db->select('*');
		$this->db->from('mfields');
		$this->db->where('IsDocumentNamingConvention',1);
		$this->db->group_by('FieldName');
		$DocumentNames = $this->db->get()->result();
		return array_column($DocumentNames,'FieldName');
	}

	/**
    * @description getting Document naming convention fields
    * @param no params
    * @throws no exception
    * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
    * @return ARRAY 
    * @since  31 Jul 2020
    * @version  Doc Packaging
    */
	function getDocumentFields()
	{
		$this->db->select('*');
		$this->db->from('mfields');
		$this->db->where('IsDocumentTypeField',1);
		// $this->db->where('Active',1);
		$this->db->group_by('FieldName');
		return $this->db->get()->result();
	}


	/**
    * @desc Insert workflow queue comments
    * @param OrderUID (int), Notes (String)
    * @throws no exception
    * @author Yagavi G <yagavi.g@avanzegroup.com>
    * @return nothing 
    * @since  Aug 6th 2020
    * @version  Closing Queue Management
    */
	function InsertQueueCommentsForOrder($OrderUID,$Notes){
		if($Notes){
			$NoteType = $this->GetNoteTypeUID('Manually Completed Workflow');
			$SectionUID = $NoteType->SectionUID;
			$UserUID = $this->session->userdata('UserUID');

			if(empty($UserUID)){
				$this->db->select('*')->from('musers')->where('LoginID', 'isgn');
				$query=$this->db->get();
				$UserUID = $query->row()->UserUID;
			}

			$this->db->trans_begin();
			$insert_notes = array(
				'Note' => $Notes,
				'SectionUID' => $SectionUID,
				'OrderUID' => $OrderUID,
				'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
				'CreatedByUserUID' => $UserUID,
				'CreatedOn' => date('Y-m-d H:i:s')
			);
			$this->db->insert("tordernotes", $insert_notes);

			/* On success insert audit trail */
			$AuditData = array(
				'UserUID' =>  $UserUID,
				'ModuleName' => 'Manually Completed Workflow',
				'OrderUID' => $OrderUID,
				'Feature' => $OrderUID,
				'Content' => $Notes,
				'DateTime' => date('Y-m-d H:i:s')
			);

			$this->Audittrail_insert($AuditData);

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
	}

	/**
    * @desc Check Event Code Status
    * @param EventCode (String)
    * @throws no exception
    * @author Yagavi G <yagavi.g@avanzegroup.com>
    * @return nothing 
    * @since  Aug 6th 2020
    * @version  Closing Queue Management
    */
	function CheckEventCodeStatus($EventCode,$OrderUID,$QueueUID='') {
		$this->db->select ( '*' ); 
		$this->db->from ( 'tOrderQueues' );
		$this->db->where ('tOrderQueues.EventCode', $EventCode);
		$this->db->where ('tOrderQueues.OrderUID', $OrderUID);
		if($QueueUID){
			$this->db->where ('tOrderQueues.QueueUID', $QueueUID);
		}
		$query = $this->db->get();
		return $query->row();
	}


	/**
	* @purpose : Check the active queues in workflows
	*
	* @param OrderUID as string
	* 
	* @throws no exception
	* @author Yagavi G<yagavi.g@avanzegroup.com>
	* @return QueueUID
	* @since 6th Aug 2020 
	*
	*/ 
	function GetActiveRecordsQueues($OrderUID){
	  	$this->db->select ( 'Group_concat(QueueUID) as QueueUID' );
	  	$this->db->from ( 'tOrderQueues' );
	  	$this->db->where('tOrderQueues.QueueStatus', 'Pending');
	  	$this->db->where('tOrderQueues.OrderUID',$OrderUID);
	  	$query = $this->db->get();
	  	$tOrderQueues =  $query->row();
	  	$QueueUID = $tOrderQueues->QueueUID;
	  	return $QueueUID;
	}

	/**
	* @purpose : Get Customer Product Mapping details
	*
	* @param SubproductUID as int
	* @param CustometUID as int
	* 
	* @throws no exception
	* @author Yagavi G<yagavi.g@avanzegroup.com>
	* @return row
	* @since 7th Aug 2020 
	*
	*/ 
	function GetCustomerProductDetailsByCustomerSubproductUID($where)
	{
		$this->db->select('*')->from('mCustomerProducts');
		$this->db->where($where);
		return $this->db->get()->row();
	}

	/**
		*@description Function to getCustomerProducts
		*
		* @param $CustomerUID
		* 
		* @throws no exception
		* @author Parthasarathy <parthasarathy.m@avanzegroup.com>
		* @return Array 
		* @since 8-8-2020 
		* @version Title commitment 
		*
	*/ 
	function getCustomerProducts($CustomerUID, $ProductUID = NULL){
		/*Query*/
		$this->db->select('*');
		$this->db->from('mCustomerProducts'); 
		$this->db->join('mProducts', 'mProducts.ProductUID = mCustomerProducts.ProductUID', 'left');
		$this->db->join('mSubProducts', 'mProducts.ProductUID = mSubProducts.ProductUID', 'left');
		$this->db->where('mCustomerProducts.CustomerUID', $CustomerUID);
		if (!empty($ProductUID)) {
			$this->db->where('mCustomerProducts.ProductUID', $ProductUID);
			$this->db->group_by('mSubProducts.SubProductUID');
		}
		else
		{
			$this->db->group_by('mProducts.ProductUID');
		}
		return $this->db->get()->result();
	}

	/**
		*@description Function to getCustomerSubProducts
		*
		* @param $CustomerUID $ProductUID
		* 
		* @throws no exception
		* @author Jainulabdeen <jainuladbeen.b@avanzegroup.com>
		* @return Array 
		* @since Aug 31 2020 
		* @version Title commitment 
		*
	*/ 
	function getCustomerSubProducts($CustomerUID, $ProductUID = NULL){
		/*Query*/
		$this->db->select('*');
		$this->db->from('mCustomerProducts'); 
		$this->db->join('mProducts', 'mProducts.ProductUID = mCustomerProducts.ProductUID', 'left');
		$this->db->join('mSubProducts', 'mProducts.ProductUID = mSubProducts.ProductUID', 'left');
		$this->db->where('mCustomerProducts.CustomerUID', $CustomerUID);
		if (!empty($ProductUID)) {
			$this->db->where_in('mCustomerProducts.ProductUID', $ProductUID);
			$this->db->group_by('mSubProducts.SubProductUID');
		}
		else
		{
			$this->db->group_by('mSubProducts.SubProductUID');
		}
		return $this->db->get()->result();
	}

	/**
	* @purpose : Get Title Orders based on the Transaction UID [Customer Reference Number]
	*
	* @param OrderUID as int
	* 
	* @throws no exception
	* @author Yagavi G<yagavi.g@avanzegroup.com>
	* @return row
	* @since 10th Aug 2020 
	*
	*/ 
	function GetTitleOrdersByOrderUID($OrderUID)
	{
		$this->db->select('*')->from('tOrders')->where('OrderUID',$OrderUID);
		$tOrders = $this->db->get()->row();
		if($tOrders){
			$CustomerRefNum = $tOrders->CustomerRefNum;
			$this->db->select('*');
			$this->db->from('tOrders');
			$this->db->join('mSubProducts','mSubProducts.SubProductUID = tOrders.SubProductUID');
			$this->db->join('mProducts','mProducts.ProductUID = mSubProducts.ProductUID');
			//$this->db->where('mProducts.IsTitleProduct',1);
			$this->db->where('tOrders.OrderUID !=',$OrderUID);
			$this->db->where('tOrders.CustomerRefNum',$CustomerRefNum);
			return $this->db->get()->row();
		}
	}

	/**
	* @purpose : Get Signing Completed DateTime
	*
	* @param OrderUID as int
	* 
	* @throws no exception
	* @author Yagavi G<yagavi.g@avanzegroup.com>
	* @return row
	* @since 12th Aug 2020 
	*
	*/ 
	function GetSigningCompletedDateTimeForClosingOrder($OrderUID)
	{
		$QueueUID = $this->config->item('Queues')['Awaiting Signing'];
		$this->db->select('*')->from('tOrderQueues')->where('OrderUID',$OrderUID)->where('QueueUID',$QueueUID)->where('QueueStatus',"Completed");
		$tOrderQueues = $this->db->get()->row();
		return $tOrderQueues;
	}

	/**
	* @purpose : Issue policy queue auditlog and notes
	* @param data as array
	* 
	* @throws no exception
	* @author D.Samuel Prabhu
	* @return nothing
	* @since 12 Aug 2020 
	*
	*/ 

	function IssuePolicyQueueLog($data)
	{
	  if(!empty($data))
	  {
	  	    /* get order details */
			$ClosingOrder = $this->GetOrderNumberByOrderUID($data['ClosingOrder']);
			$TitleOrder   = $this->GetOrderNumberByOrderUID($data['TitleOrder']);
	        
	        /* Closing order section */
			$ClosingComments = 'By completing of ';

			if(isset($data['Queue']))
			{
				$ClosingComments .= $data['Queue'].' queue.';
			}
			else if(isset($data['Event']))
			{
				$ClosingComments .= $data['Event'].' event.';
			}

			$ClosingComments .= 'The respective title order <b>'.$TitleOrder->OrderNumber.'</b> will be moved to Issue Policy Queue.';


			/*Insert Notes for Closing Order */
			$SectionUID = $this->GetNoteTypeUID("System Note")->SectionUID;
		    $this->insertordernotes($ClosingOrder->OrderUID, $SectionUID, $ClosingComments);

			/* Insert Audit trail for closing order*/
			$closingAudit = array(
				'UserUID'    => $this->loggedid,
				'ModuleName' => 'OrderQueue',
				'OrderUID'   => $ClosingOrder->OrderUID,
				'Feature'    => $ClosingOrder->OrderUID,
				'Content'    => $ClosingComments,
				'DateTime'   => date('Y-m-d H:i:s')
			);
			$this->Audittrail_insert($closingAudit);
	        
	        /* Title order section */
			$TitleComments = 'This order has been moved to Issue Policy Queue, as the respective closing order <b>'.$ClosingOrder->OrderNumber.'</b> ';
			
			if(isset($data['Queue']))
			{
				$TitleComments .= $data['Queue'].' queue completed.';
			}
			else if(isset($data['Event']))
			{
				$TitleComments .= $data['Event'].' event completed.';
			}


			/*Insert notes for title order */
		    $this->insertordernotes($TitleOrder->OrderUID, $SectionUID, $TitleComments);

			/* Insert Audit trail for title order*/
			$titleAudit = array(
				'UserUID'    => $this->loggedid,
				'ModuleName' => 'OrderQueue',
				'OrderUID'   => $TitleOrder->OrderUID,
				'Feature'    => $TitleOrder->OrderUID,
				'Content'    => $TitleComments,
				'DateTime'   => date('Y-m-d H:i:s')
			);
			$this->Audittrail_insert($titleAudit);	  	
	  }

	}

	/**
	* @description Vendor tracker Role Category
	* @param no param
	* @throws no exception
	* @author mohindarkumar-v <mohindar.kumar@avanzegroup.com>
	* @return array
	* @since 14-AUG-2020
	*/
	function _getRoleCategories(){

		$this->db->select('mRoleCategory.RoleCategoryUID,mRoleCategory.RoleCategoryName');
		$this->db->from('mRoleCategory');		
		$this->db->where('mRoleCategory.Active', 1);		
		$this->db->order_by('mRoleCategory.RoleCategoryUID', 'ASC');
		$this->db->group_by('mRoleCategory.RoleCategoryUID');
		return $this->db->get()->result();
	}
	/** end */

	/**
	* @description GetOrganizationRoles
	* @author alwin.l <alwin.l@avanzegroup.com>
	* @return array
	* @since 18-AUG-2020
	*/
	public function GetOrganizationRoles()
	{
		$this->db->select('RoleUID, RoleType, RoleName')->from('mroles');
		$this->db->where_in('RoleType', array(1,2,3,4,5,6,7,11,12));
		$this->db->order_by('RoleUID');
		return $this->db->get()->result();
	}

	/**
	* @description GetOrderFollowupTypes
	* @author alwin.l <alwin.l@avanzegroup.com>
	* @return array
	* @since 18-AUG-2020
	*/
	function GetOrderFollowupTypes()
	{
		$this->db->select('*');
		$this->db->from('mFollowupTypes');
		$query = $this->db->get();
		return $query->result();
	}

	/**
	* @description get_NotesSections
	* @author alwin.l <alwin.l@avanzegroup.com>
	* @return array
	* @since 18-AUG-2020
	*/
	function get_NotesSections()
	{
		$this->db->select("*");
		$this->db->from('mreportsections');
		$query = $this->db->get();
		return $query->result();
	}

		/**
	* @description GetTaskStatus
	* @author alwin.l <alwin.l@avanzegroup.com>
	* @return array
	* @since 18-AUG-2020
	*/
	function GetTaskStatus()
	{
		$this->db->select("*");
		$this->db->from("mTask");
		$query = $this->db->get();
		return $query->result();
	}

	/**
	* @description get_Orguser
	* @author alwin.l <alwin.l@avanzegroup.com>
	* @return array
	* @since 18-AUG-2020
	*/
	function get_Orguser()
	{
		$this->db->where('Active',1);
		$this->db->where('AbstractorUID',0);
		$this->db->where('VendorUID',0);
		$this->db->where('CustomerUID',0);
		return $this->db->get('musers')->result();
	}


	/**
	  * @description Insert Audit Log
	  * @param 
	  * @throws no exception
	  * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
	  * @return nothing 
	  * @since  20/08/2020
	  * @version  
	  */ 

	function getSettlementsByCustomerSubProduct($CustomerUID,$SubProductUID){
		return $this->db->where(array('CustomerUID'=>$CustomerUID,'SubProductUID'=>$SubProductUID))->get('mCustomerProducts')->row();
	}

/**
  * @description Get Amount based on Settlement
  * @param Settlement data
  * @throws no exception
  * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
  * @return nothing 
  * @since  20/08/2020
  * @version  
  */ 
	function getAmountBasedOnSettlementType($data,$total=false,$Filter=[]){
		$Amount = '';
		$Amount = array_sum(array_column($data,'Amount'));
		$Amount = $data->Amount;
			
		return moneyFormatter($Amount);
	}
	

/**
  * @description delete sla actions
  * @param 
  * @throws no exception
  * @author parthasarathy <parthasarathy.m@avanzegroup.com>
  * @return int 
  * @since  22/08/2020
  * @version  
  */ 
	function deleteSLAActions($SlaActionUID, $OrderUID, $OrderTypeUID=NULL, $ActionDateTime)
	{
		if ( empty($SlaActionUID) || empty($OrderUID) || empty($ActionDateTime) ) {
			return 0;
		}
		$this->db->where("ActionDateTime BETWEEN  DATE_SUB('".$ActionDateTime."', INTERVAL 2 SECOND) AND DATE_ADD('".$ActionDateTime."', INTERVAL 1 SECOND) ");
		$this->db->where('ActionUID', $SlaActionUID);
		$this->db->where('OrderUID', $OrderUID);
		if (!empty($OrderTypeUID)) {
			$this->db->where('OrderTypeUID', $OrderTypeUID);
		}
		$this->db->delete('tOrdersLAActions');

		return $this->db->affected_rows();


	}

	/**
  * @description Get Multiple Role Details
  * @param OrderUID, RoleUID
  * @throws no exception
  * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
  * @return nothing 
  * @since  31/08/2020
  * @version  
  */ 

	function getAllPropertyRoleDetails($OrderUID,$PropertyRoleUID){
		$BDetails = '';
		$BorrowerDetails = $this->db->where(array('OrderUID'=>$OrderUID,'PropertyRoleUID'=>$PropertyRoleUID))->get('tOrderPropertyRoles')->result();
		/*if(!empty($BorrowerDetails)){
			foreach ($BorrowerDetails as $key => $value) {
				$BDetails .= $value->PRName.', '.$value->MailingAddress1.', '.$value->MailingCityName.', '.$value->MailingStateCode.$value->MailingCountyName;
			}
		}*/
		//return $BDetails;
		return $BorrowerDetails;
	}


	/**
	* @description Get SourcePoint User Details
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @return nothing 
	* @since  Sept 3rd 2020
	*/ 
	function GetIsgnUser(){
		$this->db->select('*')->from('musers');
		$this->db->where('LoginID', 'isgn');
		$query=$this->db->get();
		$UserUID=$query->row()->UserUID;
		return $UserUID;
	}

	/**
	* @description Get LoginID User Details
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @return nothing 
	* @since  Sept 4th 2020
	*/ 
	function GetLoginIdByUserUID($UserUID){
		$this->db->select('*')->from('musers');
		$this->db->where('UserUID', $UserUID);
		$query=$this->db->get();
		$LoginID=$query->row()->LoginID;
		return $LoginID;
	}

	/**
        * @description Get Order Sub Workflows Using WorkflowUID
        * @author mohindarkumar-v <mohindar.kumar@avanzegroup.com>
        * @param WorkflowModuleUID
        * @throws no exception
        * @return array
        * @since 19-08-2020
        * @version Title Commitment
    */
    function getOrdersubWorkFlowMenus($WorkflowUID)
    {
    	$this->db->select('*');
    	$this->db->from('msubmenuworkflowmodules');
    	$this->db->where_in('msubmenuworkflowmodules.WorkflowModuleUID', $WorkflowUID);
    	$this->db->order_by('msubmenuworkflowmodules.MenuPosition', 'ASC');
    	$query = $this->db->get();

    	return $query->result_array();
    }
    /** end **/

	/**
	* @description Get Count of all client request orders
	* @throws no exception
	* @author Yagavi G <yagavi.g@avanzegroup.com>
	* @since  Sept 4th 2020
	*/ 
	function ClientRequestCountDetails(){
		$loggedid = $this->session->userdata('UserUID');
		$post = '';

		/* Exception Order */
		$this->load->model('customer_request/customer_request_model');
		$ExceptionOrders = $this->customer_request_model->get_reviewedorders();
		$ExceptionOrderCount = count($ExceptionOrders);

		/* Document Approval */
		$this->load->model('customer_request/my_approval_model');
		$DocumentApprovalOrders = $this->my_approval_model->my_approvals($loggedid,'Document Approval',$post);
		$DocumentApprovalCount = count($DocumentApprovalOrders);

		/* Cancel Order Request */
		$CancelRequestOrders = $this->my_approval_model->my_approvals($loggedid,'Order Cancellation',$post);
		$CancelRequestCount = count($CancelRequestOrders);

		/* RealEC Request */
		$closing = array('Schedule', "Reschedule",'Notes');
		$RealECRequestOrders = $this->my_approval_model->closing_approvals($loggedid,$closing,$post);
		$RealECRequestCount = count($RealECRequestOrders);

		/* Keystone Request */
		$pages = array('UpdateOrderInfo','RescheduleRequired','EscalationRequest','ReopenRequest','ExceptionRaise','AddNote','AddNoteAck');
		$KeystoneRequestOrders = $this->my_approval_model->get_approvals($loggedid,$pages,$post);
		$KeystoneRequestCount = count($KeystoneRequestOrders);

		$RoleUID = $this->session->userdata('RoleUID');
		$RolePermissionDetails = $this->common_model->get_roles($RoleUID); 

		$CountExceptionOrder = 0; $CountDocumentApproval = 0; $CountCancelRequest = 0; $CountRealECRequest = 0; $CountKeystoneRequest = 0;
		foreach ($RolePermissionDetails as $key => $RoleValue) {
			if($RoleValue['CustomerRequest'] == 1) {
				$CountExceptionOrder = $ExceptionOrderCount;
			}
			if($RoleValue['ClientDocumentApproval'] == 1) {
				$CountDocumentApproval = $DocumentApprovalCount;
			}
			if($RoleValue['ClientCancellationRequest'] == 1) {
				$CountCancelRequest = $CancelRequestCount;
			}
			if($RoleValue['ClientIntegrationRequest'] == 1) {
				$CountRealECRequest = $RealECRequestCount;
				$CountKeystoneRequest = $KeystoneRequestCount;
			}
		}

		/* Over all count of client request */
		$OverAllCountOfClientRequest = $CountExceptionOrder+$CountDocumentApproval+$CountCancelRequest+$CountRealECRequest+$CountKeystoneRequest;

		$CountArray = array('ClientRequestCount' => $OverAllCountOfClientRequest,'ExceptionOrders' => $ExceptionOrderCount,'DocumentApprovalOrders' => $DocumentApprovalCount,'CancelRequestOrders' => $CancelRequestCount,'RealECRequestOrders' => $RealECRequestCount,'KeystoneRequestOrders' => $KeystoneRequestCount);
		return $CountArray;
	}


   /**
	* @description D2TINT-273: Integration User Name Changes
	* @throws no exception
	* @author Shivaraja N <shivaraja.n@avanzegroup.com>
	* @since  Sept 4th 2020
	*/ 
	function CheckIntegratedOrder($OrderUID)
	{
		$this->db->from('mApiTitlePlatform');
		$this->db->join ('tOrders', 'mApiTitlePlatform.OrderSourceUID= tOrders.OrderSourceUID');
		$this->db->where(array("tOrders.APIOrder"=>1,"tOrders.OrderUID"=>$OrderUID));
		return $this->db->get()->row()->OrderSourceName;
	}


	/**
	* @description Audit Log for Change / Reverse Queue
	* @throws no exception
	* @author Jainulabdeen <jainulabdeen.b@avanzegroup.com>
	* @since  Sept 9th 2020
	*/ 
	function QueueStatusAudit($QueueUID,$QueueStatus,$OrderUID, $QueueModuleName=''){

		$this->db->select('*');
		$this->db->from('mWorkflowQueues');
		$this->db->where('mWorkflowQueues.QueueUID', $QueueUID);
		$mWorkflowQueues = $this->db->get()->row();

		if(empty($QueueModuleName)){
			if($QueueStatus == 'Change'){
				$this->db->select('GROUP_CONCAT(mQueues.QueueName) AS TriggerQueueNames');
				$this->db->from('mQueues');
				$this->db->where_in('mQueues.QueueUID', explode(",", $mWorkflowQueues->TriggerQueueUIDs));
				$TriggerQueueNames = $this->db->get()->row()->TriggerQueueNames;
			} else {
				$this->db->select('GROUP_CONCAT(mQueues.QueueName) AS TriggerQueueNames');
				$this->db->from('mQueues');
				$this->db->where_in('mQueues.QueueUID', explode(",", $mWorkflowQueues->ReverseQueueUIDs));
				$TriggerQueueNames = $this->db->get()->row()->TriggerQueueNames;
			}

			$this->db->select('GROUP_CONCAT(mQueues.QueueName) AS ActualCompleteQueueNames');
			$this->db->from('mQueues');
			$this->db->where_in('mQueues.QueueUID', explode(",", $mWorkflowQueues->ActualCompleteQueueUIDs));
			$ActualCompleteQueueNames = $this->db->get()->row()->ActualCompleteQueueNames;
		} else {
			if($QueueModuleName == 'Schedule') {
				$TriggerQueueNames = 'Awaiting Vendor';
				$ActualCompleteQueueNames = 'Ready to Schedule/Reschedule';
			} else if ($QueueModuleName == 'Schedule Contact') {
				$TriggerQueueNames = '';
				$ActualCompleteQueueNames = 'Awaiting Vendor';
			}
		}

		$this->db->select('QueueName');
		$this->db->from('mQueues');
		$this->db->where('QueueUID', $QueueUID);
		$QueueName = $this->db->get()->row()->QueueName;

		/*Audit Start*/
		if(!empty($ActualCompleteQueueNames) || !empty($TriggerQueueNames)){

			$audit_msg = 'By Completing <b>'.$QueueName.'</b> Queue <br>';
		}

		if(!empty($ActualCompleteQueueNames))
		{
			$audit_msg.= '<b>Order Cleared from:</b> '.$ActualCompleteQueueNames.'<br>';
		}

		if(!empty($TriggerQueueNames))
		{
			$audit_msg.= '<b>Order Moved to:</b> '.$TriggerQueueNames.'<br>';
		}

		$AuditData = array(
		'UserUID' => $this->loggedid,
		'ModuleName' => 'OrderQueue',
		'OrderUID' => $OrderUID,
		'Feature' => $OrderUID,
		'Content' => $audit_msg,
		'DateTime' => date('Y-m-d H:i:s'));
		$this->common_model->Audittrail_insert($AuditData);
		/*Audit End*/
	}

}?>
