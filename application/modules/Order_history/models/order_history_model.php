<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class order_history_model extends CI_Model {
	
	function __construct()
	{ 
		parent::__construct();
		$this->config->load('keywords');
	}

	    function GetCityDetails($post,$OrderUID)
    {
      $this->_getCitydetail_query($post,$OrderUID);
      if ($post['length']!='') {
         $this->db->limit($post['length'], $post['start']);
      }
      $query = $this->db->get();
      return $query->result();
}
	function GetAuditdetails($OrderUID,$module){
		$this->db->select('tAuditTrail.*,mFields.*,mUsers.UserName,tOrders.OrderNumber');
		$this->db->from('tAuditTrail');
		$this->db->join('mFields','mFields.FieldUID=tAuditTrail.FieldUID');
		$this->db->join('mUsers','mUsers.UserUID=tAuditTrail.UserUID');
		$this->db->join('tOrders','tOrders.OrderUID=tAuditTrail.OrderUID');
		$this->db->where('tAuditTrail.OrderUID',$OrderUID);
		$this->db->like('tAuditTrail.ModuleName',$module);
		// $this->db->order_by('torderdeeds.DeedPosition',"DESC");
		$this->db->order_by('tAuditTrail.DateTime','DESC');
		$query=$this->db->get();
		return $query->result();
	}
	function GetAllAuditdetails($OrderUID){
    $OrderUID=rtrim($OrderUID,'/');
    $test=$this->db->query("SELECT `tAuditTrail`.*, `tAuditTrail`.`TableName` as tables ,`mFields`.*, `mUsers`.`UserName`, `mRoles`.`RoleName`, `tOrders`.`OrderNumber`, `mUsers`.`UserUID`
                           FROM `tAuditTrail`
                           JOIN `mUsers` ON `mUsers`.`UserUID`=`tAuditTrail`.`UserUID` 
                           JOIN `mRoles` ON `mRoles`.`RoleUID`=`mUsers`.`RoleUID` 
                           LEFT JOIN `tOrders` ON `tOrders`.`OrderUID`=`tAuditTrail`.`OrderUID` 
                           LEFT JOIN `mFields` ON `mFields`.`FieldUID`=`tAuditTrail`.`FieldUID` 
                           WHERE `tAuditTrail`.`OrderUID` = $OrderUID AND (`mFields`.`FieldUID` <> '1149' OR `mFields`.`FieldUID` is NULL) ORDER BY `tAuditTrail`.`AuditUID` DESC ");
      return $test->result();
  
	}

	function get_orderdetails($OrderUID)
	{
		if($OrderUID){
			$this->db->select ( '*,tOrders.OrderUID as OrderUID' ); 
			$this->db->from ( 'tOrders' );
			$this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
			$this->db->join ( 'mOrderTypes', 'mOrderTypes.OrderTypeUID = tOrders.OrderTypeUID' , 'left' );
			$this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
      $this->db->join ( 'tOrderAssignment', 'tOrderAssignment.OrderUID = tOrders.OrderUID' , 'left' );
      $this->db->join ( 'mWorkflowModules', 'mWorkflowModules.WorkflowModuleUID = tOrderAssignment.WorkflowModuleUID' , 'left' );
       $this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
			$this->db->where ('tOrders.OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->row();
      
		}
		
	}

  function _getCitydetail_query($post,$OrderUID)
  {

  //   $this->db->select('tAuditTrail.*,mFields.*,mUsers.UserName,mRoles.RoleName,mWorkflowModules.WorkflowModuleName,tOrderAssignment.*');
		// $this->db->from('tAuditTrail');
		// $this->db->join('mFields','mFields.FieldUID=tAuditTrail.FieldUID','left');   
		// $this->db->join('mUsers','mUsers.UserUID=tAuditTrail.UserUID','left');
		// $this->db->join('mRoles','mRoles.RoleUID=mUsers.RoleUID','left');
  //   $this->db->join('tOrderAssignment','tOrderAssignment.OrderUID=tAuditTrail.OrderUID','left');
  //   $this->db->join('mWorkflowModules','mWorkflowModules.WorkflowModuleUID=tOrderAssignment.WorkflowModuleUID','left'); 
  //   $this->db->where('mFields.FieldUID <> ','1149');   
		// $this->db->where('tAuditTrail.OrderUID',$OrderUID);	
  //   $this->db->group_by('tAuditTrail.AuditUID');

  /*$this->db->select('tAuditTrail.*,mFields.*,mUsers.UserName,mRoles.RoleName,mWorkflowModules.WorkflowModuleName,tOrderAssignment.*');

   $this->db->from('tAuditTrail');
   $this->db->join('mFields','tAuditTrail.FieldUID=mFields.FieldUID','left');  
   $this->db->join('mUsers','tAuditTrail.UserUID=mUsers.UserUID','left');  
   $this->db->join('mRoles','mUsers.RoleUID=mRoles.RoleUID','left');  
   $this->db->join('tOrderAssignment','tAuditTrail.OrderUID=tOrderAssignment.OrderUID','left');  
   $this->db->join('mWorkflowModules','tAuditTrail.WorkflowModuleUID=mWorkflowModules.WorkflowModuleUID','left');  
   $this->db->where('mFields.FieldUID <> ','1149');  
   $this->db->where('tAuditTrail.OrderUID',$OrderUID);    
   $this->db->group_by('tAuditTrail.AuditUID');*/


      $this->db->select('*');
      $this->db->from('tAuditTrail');
      $this->db->join('tOrders','tAuditTrail.OrderUID=tAuditTrail.OrderUID','left'); 
      $this->db->join('mFields','tAuditTrail.FieldUID=mFields.FieldUID','left');  
      $this->db->join('mUsers','tAuditTrail.UserUID=mUsers.UserUID','left');  
      $this->db->join('mRoles','mUsers.RoleUID=mRoles.RoleUID','left');  
      $this->db->join('tOrderAssignment','tAuditTrail.OrderUID=tOrderAssignment.OrderUID','left');  
      $this->db->join('mWorkflowModules','tOrderAssignment.WorkflowModuleUID=mWorkflowModules.WorkflowModuleUID','left');  
      $this->db->where('tAuditTrail.OrderUID',$OrderUID);
      $this->db->group_by('tAuditTrail.AuditUID');
      $this->db->order_by('tAuditTrail.AuditUID  DESC');
     
      
      if(!empty($post['where'])){
        $this->db->where($post['where']);
      }
        
      // foreach ($post['where_in'] as $index => $value){
      //    $this->db->where_in($index, $value);
      // }
        
      if (!empty($post['search_value'])) {
      	// echo '<pre>';print_r('test');exit;
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

      if (!empty($post['order'])) { // here order processing 
        $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);    
      } else if (isset($this->order)) {
        $order = $this->order;
        $this->db->order_by(key($order), $order[key($order)]);  
      }
  }

  function count_all($post,$OrderUID){
    $this->_count_all_city($post,$OrderUID);
    $query = $this->db->count_all_results();
    return $query;
  }
  
  function _count_all_city($post,$OrderUID){
    $this->db->select('tAuditTrail.*,mFields.*,mUsers.UserName,mRoles.RoleName');
		$this->db->from('tAuditTrail');
		$this->db->join('mFields','mFields.FieldUID=tAuditTrail.FieldUID','inner');
		$this->db->join('mUsers','mUsers.UserUID=tAuditTrail.UserUID');
		$this->db->join('mRoles','mRoles.RoleUID=mUsers.RoleUID');
		$this->db->where('tAuditTrail.OrderUID',$OrderUID);
		$this->db->where('mFields.FieldUID <>','1149');
		$this->db->group_by('tAuditTrail.AuditUID');
	return	$query=$this->db->get()->result();
    // if($post['where']!='')
    // {
    //   $this->db->where($post['where']);
    // }
    // foreach ($post['where_in'] as $index => $value){
    //   $this->db->where_in($index, $value);
    // }
  }
  
  function count_filtered($post,$OrderUID ){
    $this->_getCitydetail_query($post,$OrderUID);    
    $query = $this->db->get();
    return $query->num_rows();
  }

  function GetCityDetailsById($CityUID)
  {
    $this->db->select("*,mCities.Active");
    $this->db->from('mCities');
    $this->db->join('mCounties','mCities.CountyUID=mCounties.CountyUID','left');
    $this->db->join('mStates','mCities.StateUID=mStates.StateUID','left');
    $this->db->where(array("mCities.CityUID"=>$CityUID));
    $query = $this->db->get();
    return $query->row();
  }

  function GetAssignedUserName($AssignedToUserUID){
    
       $this->db->select('*');
       $this->db->from('mUsers');
       $this->db->where('mUsers.UserUID',$AssignedToUserUID);
      return  $this->db->get('')->row();
  }
  function get_assignedorderdetails($OrderUID)
  {

    $this->db->select ('tOrderAssignment.*,mWorkflowModules.*,mOrderStatus.*,mUsers.*,mVendors.*,tOrders.OrderEntryDatetime'); 
    $this->db->select('tOrderAssignment.AssignedDatetime');
    $this->db->select('tOrders.OrderDueDatetime');   
    $this->db->select('tOrders.OrderEntryDatetime'); 
    $this->db->select('tOrderOnholdHistory.OnholdDateTime');   
    $this->db->select('tOrderAssignment.CompleteDateTime');      
    $this->db->from ( 'tOrders' );
    $this->db->join ( 'mCustomers', 'tOrders.CustomerUID = mCustomers.CustomerUID' , 'left' );
    $this->db->join ( 'mOrderTypes', 'mOrderTypes.OrderTypeUID = tOrders.OrderTypeUID' , 'left' );
    $this->db->join ( 'mOrderPriority', 'mOrderPriority.PriorityUID = tOrders.PriorityUID' , 'left' );
    $this->db->join ( 'tOrderAssignment', 'tOrderAssignment.OrderUID = tOrders.OrderUID' , 'left' );
    $this->db->join ( 'tOrderOnholdHistory', 'tOrderOnholdHistory.OrderUID = tOrderAssignment.OrderUID AND tOrderAssignment.WorkflowModuleUID=tOrderOnholdHistory.WorkflowModuleUID' , 'left' );
    $this->db->join ( 'mUsers', 'mUsers.UserUID = tOrderAssignment.AssignedToUserUID' , 'left' );
    $this->db->join ( 'mWorkflowModules', 'mWorkflowModules.WorkflowModuleUID = tOrderAssignment.WorkflowModuleUID' , 'left' );
    $this->db->join ( 'mOrderStatus', 'mOrderStatus.StatusUID = tOrders.StatusUID' , 'left' );
    $this->db->join ( 'mVendors', 'mVendors.VendorUID = tOrderAssignment.VendorUID','left');
    $this->db->where ('tOrders.OrderUID',$OrderUID);
    $this->db->group_by ('tOrderAssignment.WorkflowModuleUID');
    $query = $this->db->get();
    return $query->result();
      
    }
    
    function getrevisionorderdetails($OrderUID)
    {
      $this->db->select ('tOrderRevision.*, mUsers.UserName, mWorkflowModules.WorkflowModuleName');
      $this->db->select('DATE_FORMAT(tOrderRevision.AssignedDateTime, "%d-%m-%Y %H:%i:%s") as AssignedDatetime', FALSE);
      $this->db->select('DATE_FORMAT(tOrderRevision.CompleteDateTime, "%d-%m-%Y %H:%i:%s") as CompleteDatetime', FALSE);   
      $this->db->from ( 'tOrderRevision' );
      $this->db->join ( 'mUsers', 'mUsers.UserUID = tOrderRevision.AssignedToUserUID' , 'left' );
      $this->db->join ( 'mWorkflowModules', 'mWorkflowModules.WorkflowModuleUID = tOrderRevision.WorkflowModuleUID' , 'left' );
      $this->db->where ('tOrderRevision.OrderUID',$OrderUID);
      $query = $this->db->get();
      return $query->result();

    }
    function getUserActivity($UserID){


  $this->db->select('*');
  $this->db->from('mUsers');
  $this->db->where('mUsers.UserUID',$UserID);
  $role=$this->db->get('')->row();
  if($role->RoleUID >=1 && $role->RoleUID <=5){
     $this->db->select('*');
     $this->db->from('tAuditTrail');
     $this->db->join('mFields', 'mFields.FieldUID = tAuditTrail.FieldUID' , 'inner' );
     $this->db->join('tOrders', 'tOrders.OrderUID = tAuditTrail.OrderUID' , 'inner' );
     $this->db->join('mUsers', 'mUsers.UserUID = tAuditTrail.UserUID' , 'inner');
     $this->db->where('tAuditTrail.FieldUID !=','1149');
     $this->db->order_by('tAuditTrail.AuditUID DESC');
     return $this->db->get('')->result();
  }else{
    $this->db->select('*');
    $this->db->from('tAuditTrail');
    $this->db->join('mFields', 'mFields.FieldUID = tAuditTrail.FieldUID' , 'left' );
    $this->db->join('tOrders', 'tOrders.OrderUID = tAuditTrail.OrderUID' , 'left' );
    $this->db->join('mUsers', 'mUsers.UserUID = tAuditTrail.UserUID' , 'left' );
    $this->db->where('tAuditTrail.UserUID',$UserID);   
    $this->db->order_by('tAuditTrail.AuditUID DESC');
    return $user=$this->db->get('')->result();
  }

   
}
function getCustomFields_old($field_new,$table,$wherefield,$wherevalue_new,$wherevalue_old){
  $wherefield_new=$wherefield;
    if($wherefield=='PropertyStateUID' || $wherefield=='AssessedState'){
      $wherefield_new='StateUID';
    }
    if($wherefield=='PropertyCountyUID' || $wherefield=='AssessedCounty'){
      $wherefield_new='CountyUID';
    }
    if($wherefield=='PropertyCity' || $wherefield=='AssessedCity'){
      $wherefield_new='CityUID';
    }
    if($wherefield=='AssignedByUserUID' || $wherefield=='AssignedToUserUID'){
      $wherefield_new='UserUID';
    }
    if($wherefield=='Deed_DBVTypeUID_1' || $wherefield=='Deed_DBVTypeUID_2' ||  $wherefield=='Deed_DBVTypeValue_1' || $wherefield=='Deed_DBVTypeValue_2'){
      $wherefield_new='DBVTypeUID';
    }
    $this->db->select(''.$field_new.'');
    $this->db->from(''.$table.'');
    $this->db->where(''.$wherefield_new.'',$wherevalue_new);
    $new_value=$this->db->get('')->row();

    $this->db->select(''.$field_new.'');
    $this->db->from(''.$table.'');
    $this->db->where(''.$wherefield_new.'',$wherevalue_old);
    $old_value=$this->db->get('')->row();
    $output='';
   if($wherefield != 'AssignedToUserUID'){

       
    return $output.='<div class="content">
                          <ul style="padding-left: 0px;font-size: 10px;font-family:Helvetica;font-weight:bold;font-color:black;">
                            <li class="notification notification" style=""><a href="#">
                                <div class="image ""><img src="'.base_url().'assets/img/avatar7.png" alt="Avatar" height="12" width="11"></div>
                                <div class="notification-info" id="content_class"><div class="text"><span  style="font-size: 11px;font-family:Helvetica;font-weight:bold;" >'.$field_new.' '.' as'.' '.' '.$old_value->$field_new.' '.'changed'.'  to  '.' '.$new_value->$field_new.'';
}else{
     return $output.='<div class="content">
                          <ul style="padding-left: 0px;font-size: 10px;font-family:Helvetica;font-weight:bold;">
                            <li class="notification notification"><a href="#">
                                <div class="image ""><img src="'.base_url().'assets/img/avatar7.png" alt="Avatar" height="12" width="12"></div>
                                <div class="notification-info" id="content_class"><div class="text"><span  style="font-size: 11px;font-family:Helvetica;font-weight:bold;">'.$new_value->$field_new.' '.'Assigned';
}
}
function getCustomFields($ModuleName,$field_new,$table,$wherefield,$wherevalue_new,$wherevalue_old){
  $wherefield_new=$wherefield;
       if($wherefield=='AssignedByUserUID' || $wherefield=='AssignedToUserUID'){
      $wherefield_new='UserUID';
    }
    if($wherefield=='Deed_DBVTypeUID_1' || $wherefield=='Deed_DBVTypeUID_2' ||  $wherefield=='Deed_DBVTypeValue_1' || $wherefield=='Deed_DBVTypeValue_2'){
      $wherefield_new='DBVTypeUID';
    }
     if($wherefield=='Subdocument_DBVTypeUID_1' || $wherefield=='Subdocument_DBVTypeUID_2'){
      $wherefield_new='DocumentTypeUID';
    }
    $this->db->select(''.$field_new.'');
    $this->db->from(''.$table.'');
    $this->db->where(''.$wherefield_new.'',$wherevalue_new);
    $new_value=$this->db->get('')->row();

    $this->db->select(''.$field_new.'');
    $this->db->from(''.$table.'');
    $this->db->where(''.$wherefield_new.'',$wherevalue_old);
    $old_value=$this->db->get('')->row();
    $output='';
   if($wherefield != 'AssignedToUserUID'){

       
    return $output.='<div class="content">
                          <ul style="padding-left: 0px;font-size: 10px;font-family:Helvetica;font-weight:bold;font-color:black;">
                            <li class="notification notification" style=""><a href="#">
                                <div class="image ""><img src="'.base_url().'assets/img/avatar7.png" alt="Avatar" height="12" width="11"></div>
                                <div class="notification-info" id="content_class"><div class="text"><span  style="font-size: 11px;font-family:Helvetica;font-weight:bold;" >'.$ModuleName.'-'.$field_new.' '.' as'.' '.' '.$old_value->$field_new.' '.'changed'.'  to  '.' '.$new_value->$field_new.'';
}else{
     return $output.='<div class="content">
                          <ul style="padding-left: 0px;font-size: 10px;font-family:Helvetica;font-weight:bold;">
                            <li class="notification notification"><a href="#">
                                <div class="image ""><img src="'.base_url().'assets/img/avatar7.png" alt="Avatar" height="12" width="12"></div>
                                <div class="notification-info" id="content_class"><div class="text"><span  style="font-size: 11px;font-family:Helvetica;font-weight:bold;">'.$new_value->$field_new.' '.'Assigned';
}
}

    function getCustomFieldshistory($field_new,$table,$wherefield,$wherevalue_new,$wherevalue_old){
     $wherefield_new=$wherefield;
    
    if($wherefield=='AssignedByUserUID' || $wherefield=='AssignedToUserUID'){
      $wherefield_new='UserUID';
    }
    if($wherefield=='Deed_DBVTypeUID_1' || $wherefield=='Deed_DBVTypeUID_2' ||  $wherefield=='Deed_DBVTypeValue_1' || $wherefield=='Deed_DBVTypeValue_2'){
      $wherefield_new='DBVTypeUID';
    }
     if($wherefield=='Subdocument_DBVTypeUID_1' || $wherefield=='Subdocument_DBVTypeUID_2'){
      $wherefield_new='DocumentTypeUID';
    }
  
    $this->db->select(''.$field_new.'');
    $this->db->from(''.$table.'');
    $this->db->where(''.$wherefield_new.'',$wherevalue_new);
    $new_value=$this->db->get('')->row();

    $this->db->select(''.$field_new.'');
    $this->db->from(''.$table.'');
    $this->db->where(''.$wherefield_new.'',$wherevalue_old);
    $old_value=$this->db->get('')->row();
   
    $output='';
   if($wherefield != 'AssignedToUserUID'){
      
    return $output.=$field_new.' as '.$old_value->$field_new.' '.'changed'.'  to  '.' '.$new_value->$field_new.'';
}else{
      return $output.= $wherevalue_new.' '.'Assigned';
}


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
}
