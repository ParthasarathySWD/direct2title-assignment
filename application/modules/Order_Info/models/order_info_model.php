<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Order_info_model extends CI_Model {

  function __construct()
  { 
      parent::__construct();
  }

  function GetUserDetails($UserUID){

    $this->db->select ( '*' );
    $this->db->from ( 'musers' );
    $this->db->where(array("musers.UserUID"=>$UserUID));
    $query = $this->db->get();
    $res = $query->row();
    return $res;
  }

  function GetProjectDetailsForUpper($ProjectUID){

    $this->db->select ( '*' );
    $this->db->from ( 'mProjects' );
    $this->db->where(array("mProjects.ProjectUID"=>$ProjectUID));
    $query = $this->db->get();
    $res = $query->row();
    return $res;
  }


  function GetAttachmentDetailsForCopy($OrderUID){

    $this->db->select ( '*,torders.OrderDocsPath' );
    $this->db->from ( 'torderdocuments' );
    $this->db->join ( 'mdocumenttypes', 'mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID' , 'left' );
    $this->db->join ( 'msearchmodes', 'msearchmodes.SearchModeUID = torderdocuments.SearchModeUID' , 'left' );
    $this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID' , 'left' );
    $this->db->where(array("torderdocuments.OrderUID"=>$OrderUID));
    $this->db->order_by("torderdocuments.Position asc, torderdocuments.SearchModeUID asc");
    $query = $this->db->get();
    return $query->result();
  }

  function GetStateDetails($PropertyStateCode){
    $this->db->select ( '*' );
    $this->db->from ( 'mstates' );
    $this->db->where(array("mstates.StateCode"=>$PropertyStateCode));
    $query = $this->db->get();
    return $query->row();
  }

  function GetPropertyAddressDetails($OrderUID,$InputFieldName){

    $this->db->select ( $InputFieldName );
    $this->db->from ( 'torders' );
    $this->db->where(array("torders.OrderUID"=>$OrderUID));
    $query = $this->db->get();
    $res = $query->row();
    $FieldName = $res->$InputFieldName;
    return $FieldName;
  }

  function GetProjectDetails($ProjectUID,$InputFieldName){

    $this->db->select ( $InputFieldName );
    $this->db->from ( 'mProjects' );
    $this->db->where(array("mProjects.ProjectUID"=>$ProjectUID));
    $query = $this->db->get();
    $res = $query->row();
    $FieldName = $res->$InputFieldName;
    return $FieldName;
  }

  function get_orderdetails($OrderUID)
  {
    if($OrderUID){
      $this->db->select ( '*' ); 
      $this->db->from ( 'torders' );
      $this->db->join ( 'mstates', 'mstates.StateUID = torders.PropertyStateUID' , 'left' );
      $this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID' , 'left' );
      $this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
      $this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );
      $this->db->join ( 'mcities', 'mcities.CityUID = torders.PropertyCity' , 'left' );
      $this->db->join ( 'mcounties', 'mcounties.CountyUID = torders.PropertyCountyUID' , 'left' );
      $this->db->where ('torders.OrderUID',$OrderUID);
      $query = $this->db->get();
      return $query->row();
    }
    
  }

  function GetParentMasterData($ParentTable){
      $this->db->select ( '*' ); 
      $this->db->from ( $ParentTable );
      $query = $this->db->get();
      return $query->result();
  }

  function GetCountyStateUID($CountyName,$StateCode){
      $this->db->select ( '*' ); 
      $this->db->from ( 'mcounties' );
      $this->db->join('mstates','mstates.StateUID=mcounties.StateUID');
      $this->db->where(array('mcounties.CountyName'=>$CountyName, 'mstates.StateCode'=>$StateCode));
      $query = $this->db->get();
      return $query->row();
  }
 
  function GetFieldsByTemplateMappingUID($torders)
  { 
    $subproductuid=$torders[0]->SubProductUID;
    $CountyName=$torders[0]->PropertyCountyName;
    $StateCode=$torders[0]->PropertyStateCode;

    $CountyStateDetails = $this->GetCountyStateUID($CountyName,$StateCode);

    $countyuid = $CountyStateDetails->CountyUID;
    $stateuid = $CountyStateDetails->StateUID;
    $TemplateUID=$torders[0]->TemplateUID;
    $FieldRow=$torders[0]->TemplateFieldRow;

    //$FieldRow = $this->GetFieldRow($torders);

    $this->db->select('*')->from('mproductfield');
    $this->db->join('mfields','mfields.FieldUID=mproductfield.FieldUID');
    $this->db->where(array('mproductfield.FieldRow'=>$FieldRow,'mproductfield.SubProductUID'=>$subproductuid, 'mproductfield.CountyUID'=>$countyuid, 'mproductfield.StateUID'=>$stateuid));
    $this->db->group_by('mproductfield.FieldUID');
    $this->db->order_by('mproductfield.FieldPosition');
    $ProductFields=$this->db->get()->result();

    if(empty($ProductFields))
    {
      $this->db->select('*')->from('mproductfield');
      $this->db->join('mfields','mfields.FieldUID=mproductfield.FieldUID');
      $this->db->where(array('mproductfield.FieldRow'=>$FieldRow,'mproductfield.SubProductUID'=>$subproductuid, 'mproductfield.CountyUID'=>'0', 'mproductfield.StateUID'=>$stateuid));
      $this->db->group_by('mproductfield.FieldUID');
      $this->db->order_by('mproductfield.FieldPosition');
      $ProductFields=$this->db->get()->result();

      if(empty($ProductFields))
      {
        $this->db->select('*')->from('mproductfield');
        $this->db->join('mfields','mfields.FieldUID=mproductfield.FieldUID');
        $this->db->where(array('mproductfield.FieldRow'=>$FieldRow,'mproductfield.SubProductUID'=>$subproductuid, 'mproductfield.CountyUID'=>'0', 'mproductfield.StateUID'=>'0'));
        $this->db->group_by('mproductfield.FieldUID');
        $this->db->order_by('mproductfield.FieldPosition');
        $ProductFields=$this->db->get()->result();
      }
    }

    return $ProductFields;
  }

  function GetFieldRow($torders){
    $Template=$this->GetTemplateMappingByOrderUID($torders);
    $DynTemplateUID = $Template->TemplateUID;
    $FieldRow = $Template->FieldRow;
    return $FieldRow;
  }
  
  function Gettordersby_UID($orderUID) {
    $query = $this->db->get_where('torders', array('OrderUID' => $orderUID));
    return $query->result();
  }

  function GetTemplateMappingByOrderUID($torders)
  {
    $subproductuid=$torders[0]->SubProductUID;
    $CountyName=$torders[0]->PropertyCountyName;
    $StateCode=$torders[0]->PropertyStateCode;

    $CountyStateDetails = $this->GetCountyStateUID($CountyName,$StateCode);

    $countyuid = $CountyStateDetails->CountyUID;
    $stateuid = $CountyStateDetails->StateUID;

    $Templatemapping=$this->db->get_where('mtemplatemapping', array('SubProductUID' => $subproductuid, 'StateUID'=>$stateuid, 'CountyUID'=>$countyuid))->row();

    if(empty($Templatemapping))
    {
      $Templatemapping=$this->db->get_where('mtemplatemapping', array('SubProductUID' => $subproductuid, 'StateUID'=>$stateuid, 'CountyUID'=>'0'))->row();

      if(empty($Templatemapping))
      {
        $Templatemapping=$this->db->get_where('mtemplatemapping', array('SubProductUID' => $subproductuid, 'StateUID'=>'0', 'CountyUID'=>'0'))->row();
      }
    }

    return $Templatemapping;
  }

  function GetTemplateMappingByOrderUIDFieldRow($torders)
  {
    $subproductuid=$torders[0]->SubProductUID;
    $CountyName=$torders[0]->PropertyCountyName;
    $StateCode=$torders[0]->PropertyStateCode;
    $FieldRow=$torders[0]->TemplateFieldRow;

    $CountyStateDetails = $this->GetCountyStateUID($CountyName,$StateCode);

    $countyuid = $CountyStateDetails->CountyUID;
    $stateuid = $CountyStateDetails->StateUID;

    $Templatemapping=$this->db->get_where('mtemplatemapping', array('FieldRow' => $FieldRow,'SubProductUID' => $subproductuid, 'StateUID'=>$stateuid, 'CountyUID'=>$countyuid))->row();

    if(empty($Templatemapping))
    {
      $Templatemapping=$this->db->get_where('mtemplatemapping', array('FieldRow' => $FieldRow,'SubProductUID' => $subproductuid, 'StateUID'=>$stateuid, 'CountyUID'=>'0'))->row();

      if(empty($Templatemapping))
      {
        $Templatemapping=$this->db->get_where('mtemplatemapping', array('FieldRow' => $FieldRow,'SubProductUID' => $subproductuid, 'StateUID'=>'0', 'CountyUID'=>'0'))->row();
      }
    }

    return $Templatemapping;
  }

 
 function GettorderdetailsByUID($OrderUID){
  $this->db->select('torders.*,');
  $this->db->from('torders');
  // $this->db->join( 'torders', 'torders.OrderUID = torderdocuments.OrderUID' , 'left' );
  $this->db->where('torders.OrderUID',$OrderUID);
  return $this->db->get('')->row();
 }

 function GetMappedFieldBySubProduct($SubProductUID){
 	$this->db->select('mproductfield.*,');
 	$this->db->from('mproductfield');
  $this->db->join( 'mfields', 'mfields.FieldUID = mproductfield.FieldUID' , 'left' );
 	$this->db->where('mproductfield.SubProductUID',$SubProductUID);
 	return $this->db->get('')->result();
 }

  function GetDynamicDetails($OrderUID){  
    $this->db->select('*');
    $this->db->from('torders');
    $this->db->where('torders.OrderUID',$OrderUID);
    return $result= $this->db->get('')->row();
  }

  function GetDynamicField($SubProductUID){
    $this->db->select('*')->from('mproductfield');
    $this->db->join('mfields','mfields.FieldUID=mproductfield.FieldUID');
    $this->db->where('mproductfield.SubProductUID',$SubProductUID);
    $this->db->group_by('mproductfield.FieldUID');
    $this->db->order_by('mfields.FieldUID');
    return  $ProductFields=$this->db->get()->result();
  }

function GetTableValue($TableName,$FieldName,$OrderUID){
      
      if($TableName!='torders'){
		      $this->db->select(''.$TableName.'.'.$FieldName.' as value');
		      $this->db->from(''.$TableName.'');
		      $this->db->join('torders','torders.orderUID='.$TableName.'.orderUID');
		      $this->db->where('torders.OrderUID',$OrderUID);
		     $FieldValue= $this->db->get('')->row();
             return  $FieldValue;
             

      }else{
      	   
      	      $this->db->select(''.$TableName.'.'.$FieldName.' as value');
		      $this->db->from(''.$TableName.'');
		      $this->db->where('torders.OrderUID',$OrderUID);
		      return $this->db->get('')->row();
           
   }
}

  function GetAssignorData($ProjectUID){
    $this->db->select ( '*' ); 
    $this->db->from ( 'mProjectAssignor' );
    $this->db->join('mAssignor','mAssignor.AssignorUID=mProjectAssignor.AssignorUID');
    $this->db->where(array('mProjectAssignor.ProjectUID'=>$ProjectUID));
    $query = $this->db->get();
    return $query->result();
  }

  function GetAssignorDataPrintName($ProjectUID){
    $this->db->select ( 'AssignorPrintName' ); 
    $this->db->from ( 'mProjectAssignor' );
    $this->db->join('mAssignor','mAssignor.AssignorUID=mProjectAssignor.AssignorUID');
    $this->db->where(array('mProjectAssignor.ProjectUID'=>$ProjectUID));
    $query = $this->db->get();
    return $query->row();
  }

  function GetAssigneeData($ProjectUID){
    $this->db->select ( '*' ); 
    $this->db->from ( 'mProjectAssignee' );
    $this->db->join('mAssignee','mAssignee.AssigneeUID=mProjectAssignee.AssigneeUID');
    $this->db->where(array('mProjectAssignee.ProjectUID'=>$ProjectUID));
    $query = $this->db->get();
    return $query->result();
  }

  function GetAssigneeDataPrintName($ProjectUID){
    $this->db->select ( 'AssigneePrintName' ); 
    $this->db->from ( 'mProjectAssignee' );
    $this->db->join('mAssignee','mAssignee.AssigneeUID=mProjectAssignee.AssigneeUID');
    $this->db->where(array('mProjectAssignee.ProjectUID'=>$ProjectUID));
    $query = $this->db->get();
    return $query->row();
  }

  function GetLenderData($ProjectUID){
    $this->db->select ( '*' ); 
    $this->db->from ( 'mProjectLender' );
    $this->db->join('mLender','mLender.LenderUID=mProjectLender.LenderUID');
    $this->db->where(array('mProjectLender.ProjectUID'=>$ProjectUID));
    $query = $this->db->get();
    return $query->result();
  }

  function GetLenderDataPrintName($ProjectUID){
    $this->db->select ( 'LenderPrintName' ); 
    $this->db->from ( 'mProjectLender' );
    $this->db->join('mLender','mLender.LenderUID=mProjectLender.LenderUID');
    $this->db->where(array('mProjectLender.ProjectUID'=>$ProjectUID));
    $query = $this->db->get();
    return $query->row();
  }

  function GetEndorserData($ProjectUID){
    $this->db->select ( '*' ); 
    $this->db->from ( 'mProjectEndorser' );
    $this->db->join('mEndorser','mEndorser.EndorserUID=mProjectEndorser.EndorserUID');
    $this->db->where(array('mProjectEndorser.ProjectUID'=>$ProjectUID));
    $query = $this->db->get();
    return $query->result();
  }

  function GetEndorserDataPrintName($ProjectUID){
    $this->db->select ( 'EndorserPrintName' ); 
    $this->db->from ( 'mProjectEndorser' );
    $this->db->join('mEndorser','mEndorser.EndorserUID=mProjectEndorser.EndorserUID');
    $this->db->where(array('mProjectEndorser.ProjectUID'=>$ProjectUID));
    $query = $this->db->get();
    return $query->row();
  }

  function GetEndorserDetails($EndorserName){
    $this->db->select ( '*' ); 
    $this->db->from ( 'mEndorser' );
    $this->db->where(array('mEndorser.EndorserName'=>$EndorserName));
    $query = $this->db->get();
    return $query->row();
  }

  function TemplatemappingList($torders)
  {
    $subproductuid=$torders[0]->SubProductUID;
    $CountyName=$torders[0]->PropertyCountyName;
    $StateCode=$torders[0]->PropertyStateCode;
    $ProjectUID=$torders[0]->ProjectUID;

    $CountyStateDetails = $this->GetCountyStateUID($CountyName,$StateCode);
    $countyuid = $CountyStateDetails->CountyUID;
    $stateuid = $CountyStateDetails->StateUID;

    $this->db->select ( '*' ); 
    $this->db->from ( 'mtemplatemapping' );
    $this->db->join('mtemplates','mtemplates.TemplateUID=mtemplatemapping.TemplateUID');
    $this->db->join('mTemplateProject','mTemplateProject.TemplateUID=mtemplates.TemplateUID');
    $this->db->where(array('mTemplateProject.ProjectUID' => $ProjectUID,'SubProductUID' => $subproductuid, 'StateUID'=>$stateuid, 'CountyUID'=>$countyuid));
    $query = $this->db->get();
    $Templatemapping = $query->result();

    if(empty($Templatemapping))
    {

      $this->db->select ( '*' ); 
      $this->db->from ( 'mtemplatemapping' );
      $this->db->join('mtemplates','mtemplates.TemplateUID=mtemplatemapping.TemplateUID');
      $this->db->join('mTemplateProject','mTemplateProject.TemplateUID=mtemplates.TemplateUID');
      $this->db->where(array('mTemplateProject.ProjectUID' => $ProjectUID,'SubProductUID' => $subproductuid, 'StateUID'=>$stateuid, 'CountyUID'=>'0'));
      $query = $this->db->get();
      $Templatemapping = $query->result();

      if(empty($Templatemapping))
      {
        $this->db->select ( '*' ); 
        $this->db->from ( 'mtemplatemapping' );
        $this->db->join('mtemplates','mtemplates.TemplateUID=mtemplatemapping.TemplateUID');
        $this->db->join('mTemplateProject','mTemplateProject.TemplateUID=mtemplates.TemplateUID');
        $this->db->where(array('mTemplateProject.ProjectUID' => $ProjectUID,'SubProductUID' => $subproductuid, 'StateUID'=>'0', 'CountyUID'=>'0'));
        $query = $this->db->get();
        $Templatemapping = $query->result();       
      }
    }

    return $Templatemapping;
  }

  function GetTemplateMappingFields($TemplatesMappingUID){
    $this->db->select ( '*' ); 
    $this->db->from ( 'mtemplatemapping' );
    $this->db->where(array('TemplatesMappingUID' => $TemplatesMappingUID));
    $query = $this->db->get();
    $Templatemapping = $query->row();

    $FieldRow = $Templatemapping->FieldRow;
    $StateUID = $Templatemapping->StateUID;
    $CountyUID = $Templatemapping->CountyUID;
    $SubProductUID = $Templatemapping->SubProductUID;

    $this->db->select ( '*' ); 
    $this->db->from ( 'mproductfield' );
    $this->db->join('mfields','mfields.FieldUID=mproductfield.FieldUID');
    $this->db->where(array('FieldRow' => $FieldRow,'StateUID' => $StateUID,'CountyUID' => $CountyUID,'SubProductUID' => $SubProductUID));
    $query = $this->db->get();
    $Fields = $query->result();

    return $Fields;
  }
  function GetOrderInfoStyle($id)
  {
    $this->db->select ( 'IsOrderAlign' ); 
    $this->db->from ( 'musers' );
    $this->db->where(array('UserUID' => $id));
    $query = $this->db->get();
    $Fields = $query->row('IsOrderAlign');

    return $Fields;
  }

}
?>
