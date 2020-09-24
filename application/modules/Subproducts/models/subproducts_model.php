<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Subproducts_model extends CI_Model {

	
  function __construct()
    { 
        parent::__construct();
    }


    function GetFieldsByFieldRowSubProduct($FieldRow,$SubProductUID){

      $this->db->select('mfields.FieldUID');
      $this->db->from('mproductfield');
      $this->db->join('mfields','mfields.FieldUID = mproductfield.FieldUID','LEFT');
      $this->db->where(array("SubProductUID"=>$SubProductUID));
      $this->db->where(array("FieldRow"=>$FieldRow));
      $query = $this->db->get();
      return $query->result();
    }

    function GetIsDefaultFieldRow($SubProductUID,$FieldRow){

      $this->db->select('*');
      $this->db->from('mtemplatemapping');
      $this->db->where(array("SubProductUID"=>$SubProductUID));
      $this->db->where(array("FieldRow"=>$FieldRow));
      $query = $this->db->get();
      return $query->row()->IsDefault;
    }


    function DeleteMappingFieldsTemplates($FieldRow,$SubProductUID)
    {
      $this->db->where(array("FieldRow"=>$FieldRow,"SubProductUID"=>$SubProductUID));
      $this->db->delete('mproductfield'); 
      $this->db->where(array("FieldRow"=>$FieldRow,"SubProductUID"=>$SubProductUID));
      $res = $this->db->delete('mtemplatemapping');

      if($res)
        $data=array("validation_error"=>1);
      else
        $data=array("validation_error"=>0);

        echo json_encode($data);
    }

    function DeleteOldMappedFieldsByFieldRow($SubProductUID,$FieldRow)
    {
      $this->db->where(array("FieldRow"=>$FieldRow,"SubProductUID"=>$SubProductUID));
      $this->db->delete('mproductfield'); 
      $this->db->where(array("FieldRow"=>$FieldRow,"SubProductUID"=>$SubProductUID));
      $res = $this->db->delete('mtemplatemapping');

      if($res)
      {
        return $SubProductUID;
      } 
      else 
      {
        return 0;
      }

    }

    function EditMappingFieldsTemplates($FieldRow,$SubProductUID,$MappingLevel)
    {

      $query_fields = $this->db->query("SELECT GROUP_CONCAT(DISTINCT mproductfield.SubProductUID) AS SubProductUID, 
                                  GROUP_CONCAT(DISTINCT mproductfield.FieldUID) AS FieldUID, 
                                  GROUP_CONCAT(DISTINCT mproductfield.StateUID) AS StateUID, 
                                  GROUP_CONCAT(DISTINCT mproductfield.CountyUID) AS CountyUID, 
                                  GROUP_CONCAT(DISTINCT mproductfield.MappingLevel) AS MappingLevel
                                  FROM mproductfield 
                                  LEFT JOIN mtemplatemapping ON mproductfield.SubProductUID=mtemplatemapping.SubProductUID AND mproductfield.StateUID=mtemplatemapping.StateUID AND mproductfield.CountyUID=mtemplatemapping.CountyUID 
                                  LEFT JOIN mtemplates ON `mtemplatemapping`.`TemplateUID`=`mtemplates`.`TemplateUID` 
                                  LEFT JOIN msubproducts ON `mtemplatemapping`.`SubProductUID`=`msubproducts`.`SubProductUID` 
                                  WHERE mproductfield.SubProductUID='$SubProductUID' AND mproductfield.FieldRow='$FieldRow'
                                  GROUP BY mproductfield.FieldRow");
      $Fields = $query_fields->result();

      $query_template = $this->db->query("SELECT GROUP_CONCAT(DISTINCT mtemplatemapping.SubProductUID) AS SubProductUID, 
                                GROUP_CONCAT(DISTINCT mtemplatemapping.TemplateUID) AS TemplateUID, 
                                GROUP_CONCAT(DISTINCT mtemplatemapping.TemplateFileName) AS TemplateFileName, 
                                GROUP_CONCAT(DISTINCT mtemplatemapping.StateUID) AS StateUID, 
                                GROUP_CONCAT(DISTINCT mtemplatemapping.CountyUID) AS CountyUID,
                                GROUP_CONCAT(DISTINCT mtemplatemapping.DocumentType) AS DocumentType,
                                GROUP_CONCAT(DISTINCT mtemplatemapping.FieldRow) AS FieldRow,mtemplates.TemplateName,msubproducts.SubProductName
                                FROM mtemplatemapping
                                LEFT JOIN mproductfield ON mtemplatemapping.SubProductUID=mproductfield.SubProductUID AND mtemplatemapping.StateUID=mproductfield.StateUID AND mtemplatemapping.CountyUID=mproductfield.CountyUID 
                                LEFT JOIN mtemplates ON `mtemplatemapping`.`TemplateUID`=`mtemplates`.`TemplateUID` 
                                LEFT JOIN msubproducts ON `mtemplatemapping`.`SubProductUID`=`msubproducts`.`SubProductUID` 
                                WHERE mtemplatemapping.SubProductUID= '$SubProductUID' AND mtemplatemapping.FieldRow='$FieldRow'
                                GROUP BY mtemplatemapping.FieldRow");

      $Template = $query_template->result();

      $result = array();
      foreach($Fields as $key=>$val){ 
          $val1 = (array) $Template[$key]; 
          $val2 = (array) $Fields[$key]; 

          $result[] = array_merge($val1,$val2);
      }

      return $result;
    }

    function GetSubproductLevelMapping($SubProductUID)
    {
      $this->db->select("group_concat(FieldUID) as Fields, MappingLevel");
      $this->db->from('mproductfield'); 
      $this->db->where('SubProductUID',$SubProductUID);
      $query = $this->db->get();
      return $query->row(); 
    }  

    function GetFieldRowBySubProductUID($SubProductUID){

      $this->db->select_max('FieldRow');
      $this->db->from('mtemplatemapping');
      $this->db->where(array("SubProductUID"=>$SubProductUID));
      $query = $this->db->get();
      return $query->row();
    }

    function GetTemplateFormatByFieldRowUsingTemplate($FieldRow,$SubProductUID,$TemplateUID){

      $this->db->distinct('mtemplatemapping.FieldRow');
      $this->db->select('*');
      $this->db->from('mtemplatemapping');
      $this->db->where(array("FieldRow"=>$FieldRow,"SubProductUID"=>$SubProductUID,"TemplateUID"=>$TemplateUID));
      $query = $this->db->get();
      return $query->row();
    }

    function GetTemplateFormatByFieldRow($FieldRow,$SubProductUID){

      $this->db->distinct('mtemplatemapping.FieldRow');
      $this->db->select('*');
      $this->db->from('mtemplatemapping');
      $this->db->where(array("FieldRow"=>$FieldRow,"SubProductUID"=>$SubProductUID));
      $query = $this->db->get();
      return $query->row();
    }

    function GetStatesBySubProduct($StateUID){

      $this->db->select('StateCode');
      $this->db->from('mstates');
      $this->db->where(array("mstates.StateUID"=>$StateUID));
      $query = $this->db->get();
      return $query->row();
    }

    function GetCountyBySubProduct($CountyUID){

      $this->db->select('CountyName');
      $this->db->from('mcounties');
      $this->db->where(array("mcounties.CountyUID"=>$CountyUID));
      $query = $this->db->get();
      return $query->row();
    }

    function GetFieldBySubProduct($FieldUID){

      $this->db->select('FieldName');
      $this->db->from('mfields');
      $this->db->where(array("mfields.FieldUID"=>$FieldUID));
      $query = $this->db->get();
      return $query->row();
    }

    function GetTemplateDetailsBySubProductUID($SubProductUID){

      $ProductUID = $this->GetProductBySubProduct($SubProductUID);
      $ProductUID = $ProductUID->ProductUID;

      $this->db->select("*");
      $this->db->from('mtemplates');
      $this->db->where(array("Active"=>1,"IsDynamicTemplate"=>1,"ProductUID"=>$ProductUID));
      $query = $this->db->get();
      return $query->result();
    }

    function GetProductBySubProduct($SubProductUID){

      $this->db->select("*");
      $this->db->from('msubproducts');
      $this->db->where(array("SubProductUID"=>$SubProductUID));
      $query = $this->db->get();
      return $query->row();

    }

    function GetMappingDetails($SubProductUID){

      $this->db->select("*");
      $this->db->from('mproductfield');
      $this->db->where(array("SubProductUID"=>$SubProductUID));
      $this->db->group_by('mproductfield.FieldRow');
      $query = $this->db->get();
      return $query->result();

    }

  function GetTemplateMappingDetails($SubProductUID){

    $query_fields = $this->db->query("SELECT GROUP_CONCAT(DISTINCT mproductfield.SubProductUID) AS SubProductUID, 
                                GROUP_CONCAT(DISTINCT mproductfield.FieldUID) AS FieldUID, 
                                GROUP_CONCAT(DISTINCT mproductfield.StateUID) AS StateUID, 
                                GROUP_CONCAT(DISTINCT mproductfield.CountyUID) AS CountyUID, 
                                GROUP_CONCAT(DISTINCT mproductfield.MappingLevel) AS MappingLevel
                                FROM mproductfield 
                                LEFT JOIN mtemplatemapping ON mproductfield.SubProductUID=mtemplatemapping.SubProductUID AND mproductfield.StateUID=mtemplatemapping.StateUID AND mproductfield.CountyUID=mtemplatemapping.CountyUID 
                                LEFT JOIN mtemplates ON `mtemplatemapping`.`TemplateUID`=`mtemplates`.`TemplateUID` 
                                LEFT JOIN msubproducts ON `mtemplatemapping`.`SubProductUID`=`msubproducts`.`SubProductUID` 
                                WHERE mproductfield.SubProductUID='$SubProductUID'
                                GROUP BY mproductfield.FieldRow
                                ORDER BY mproductfield.StateUID");
    $Fields = $query_fields->result();

    $query_template = $this->db->query("SELECT GROUP_CONCAT(DISTINCT mtemplatemapping.SubProductUID) AS SubProductUID, 
                              GROUP_CONCAT(DISTINCT mtemplatemapping.TemplateUID) AS TemplateUID, 
                              GROUP_CONCAT(DISTINCT mtemplatemapping.TemplateFileName) AS TemplateFileName, 
                              GROUP_CONCAT(DISTINCT mtemplatemapping.StateUID) AS StateUID, 
                              GROUP_CONCAT(DISTINCT mtemplatemapping.CountyUID) AS CountyUID,
                              GROUP_CONCAT(DISTINCT mtemplatemapping.DocumentType) AS DocumentType, 
                              GROUP_CONCAT(DISTINCT mtemplatemapping.FieldRow) AS FieldRow,
                              GROUP_CONCAT(DISTINCT mtemplatemapping.IsDefault) AS IsDefault,
                              mtemplates.TemplateName,msubproducts.SubProductName
                              FROM mtemplatemapping
                              LEFT JOIN mproductfield ON mtemplatemapping.SubProductUID=mproductfield.SubProductUID AND mtemplatemapping.StateUID=mproductfield.StateUID AND mtemplatemapping.CountyUID=mproductfield.CountyUID 
                              LEFT JOIN mtemplates ON `mtemplatemapping`.`TemplateUID`=`mtemplates`.`TemplateUID` 
                              LEFT JOIN msubproducts ON `mtemplatemapping`.`SubProductUID`=`msubproducts`.`SubProductUID` 
                              WHERE mtemplatemapping.SubProductUID= '$SubProductUID'
                              GROUP BY mtemplatemapping.FieldRow
                              ORDER BY mtemplatemapping.StateUID");

    $Template = $query_template->result();

    $result = array();
    foreach($Fields as $key=>$val){ 
        $val1 = (array) $Template[$key]; 
        $val2 = (array) $Fields[$key]; 

        //$result[] = array_merge($val1,$val2);
        $result[] = $val1;
    }

    $result_array = array_filter($result);

    return $result_array;

  }

  function CheckMappingLevel($SubProductUID){

    $query_fields = $this->db->query("SELECT GROUP_CONCAT(DISTINCT mproductfield.SubProductUID) AS SubProductUID, 
                                GROUP_CONCAT(DISTINCT mproductfield.FieldUID) AS FieldUID, 
                                GROUP_CONCAT(DISTINCT mproductfield.StateUID) AS StateUID, 
                                GROUP_CONCAT(DISTINCT mproductfield.CountyUID) AS CountyUID, 
                                GROUP_CONCAT(DISTINCT mproductfield.MappingLevel) AS MappingLevel
                                FROM mproductfield 
                                LEFT JOIN mtemplatemapping ON mproductfield.SubProductUID=mtemplatemapping.SubProductUID AND mproductfield.StateUID=mtemplatemapping.StateUID AND mproductfield.CountyUID=mtemplatemapping.CountyUID 
                                LEFT JOIN mtemplates ON `mtemplatemapping`.`TemplateUID`=`mtemplates`.`TemplateUID` 
                                LEFT JOIN msubproducts ON `mtemplatemapping`.`SubProductUID`=`msubproducts`.`SubProductUID` 
                                WHERE mproductfield.SubProductUID='$SubProductUID' And mproductfield.MappingLevel= 1
                                GROUP BY mproductfield.FieldRow");
    $Fields = $query_fields->num_rows();

    if($Fields>0)
    {
      return 1;
    }
    else
    {
      return 0;
    }
  }

  function GetTemplateDetails(){

    $this->db->select("*");
    $this->db->from('mtemplates');
    $this->db->where(array("Active"=>1,"IsDynamicTemplate"=>1));
    $query = $this->db->get();
    return $query->result();
  }

  function CheckFirstTempalteMapping($ProductUID,$SubProductName){

    $this->db->select("*");
    $this->db->from('msubproducts');
    $this->db->where(array("ProductUID"=>$ProductUID,"SubProductName"=>$SubProductName));
    $query = $this->db->get();
    return $query->row();
  }

  function CheckIsDynamicProduct($ProductUID){
    $this->db->select("*");
    $this->db->from('mproducts');
    $this->db->where(array("ProductUID"=>$ProductUID));
    $query = $this->db->get();
    return $query->row();
  }


  function GetFieldsByTemplateMappingUID($FieldUID){
    $this->db->select("mfields.*,mproductfield.FieldPosition");
    $this->db->from('mfields');
    $this->db->join('mproductfield','mproductfield.FieldUID = mfields.FieldUID','LEFT');
    $this->db->where('mfields.FieldUID',$FieldUID);
    $query = $this->db->get();
    $Fields = $query->row();

    return $Fields;

   /* $Fields = [];
    foreach ($FieldUID as $key => $value) {
      $this->db->select("*");
      $this->db->from('mfields');
      $this->db->where('mfields.FieldUID',$value);
      $query = $this->db->get();
      $Fields[] = $query->row();
    }
    return $Fields;*/
  }

  function GetTemplateFormat($TemplateUID){
    $this->db->select("*");
    $this->db->from('mtemplates');
    $this->db->where('mtemplates.TemplateUID',$TemplateUID);
    $query = $this->db->get();
    $result = $query->row();
    return $result;
  }

   function GetCountyByStateUID($StateUID)
  {
    $this->db->select("mcounties.CountyUID, mcounties.CountyName, mstates.StateCode");
    $this->db->from('mcounties');
    $this->db->join('mstates','mstates.StateUID = mcounties.StateUID','LEFT');
    $this->db->where("mcounties.StateUID IN (".$StateUID.")");
    $query = $this->db->get();
    return $query->result();  
  }
 
  function GetTemplateByProduct($ProductUID){
    $this->db->select("*");
    $this->db->from('mtemplates');
    $this->db->where(array("ProductUID"=>$ProductUID,"Active"=>1,"IsDynamicTemplate"=>1));
    $query = $this->db->get();
    return $query->result();  
  }  

  function GetMFields()
  {
    $this->db->select("*");
    $this->db->from('mfields'); 
    $this->db->where(array("IsDynamic"=>1));
    $query = $this->db->get();
    return $query->result();    
  }

  function GetStateMappedFieldRows($SubProductUID)
  {
    $this->db->select('count(TemplateUID) as row');
    $this->db->where('SubProductUID',$SubProductUID);
    $q = $this->db->get('mtemplatemapping')->row();
    return $q;
  }

  function GetRowBySubProduct($SubProductUID, $MappingLevel)
  {
    $this->db->select('COUNT(DISTINCT FieldRow) as rowcount');
    $this->db->from('mproductfield'); 
    $this->db->where('mproductfield.SubProductUID',$SubProductUID);
    $this->db->where('mproductfield.MappingLevel',$MappingLevel); 
    $q = $this->db->get();
    return $q->row();
  }

  function GetMappedStatesBySubProduct($SubProductUID, $row, $MappingLevel)
  {
    $this->db->select('group_concat(DISTINCT mtemplatemapping.StateUID) as StateUID, group_concat(DISTINCT mproductfield.FieldUID) as Fields,  mtemplatemapping.TemplateUID as TemplateUID');
    $this->db->from('mproductfield');
    $this->db->join('mtemplatemapping','mtemplatemapping.SubProductUID = mproductfield.SubProductUID AND mtemplatemapping.StateUID = mproductfield.StateUID','LEFT');
    $this->db->where('mproductfield.MappingLevel',$MappingLevel);
    $this->db->where('mproductfield.SubProductUID',$SubProductUID);
    $this->db->where('mproductfield.FieldRow',$row);
    $this->db->where('mtemplatemapping.FieldRow',$row);
    $q = $this->db->get()->row();
    return $q;
  }

  function GetMappedCountyBySubProduct($SubProductUID, $row, $MappingLevel)
  {
    $this->db->select('group_concat(DISTINCT mtemplatemapping.StateUID) as StateUID, group_concat(DISTINCT mtemplatemapping.CountyUID) as CountyUID, group_concat(DISTINCT mproductfield.FieldUID) as Fields,  mtemplatemapping.TemplateUID as TemplateUID');
    $this->db->from('mproductfield');
    $this->db->join('mtemplatemapping','mtemplatemapping.SubProductUID = mproductfield.SubProductUID AND mtemplatemapping.StateUID = mproductfield.StateUID AND mtemplatemapping.CountyUID = mproductfield.CountyUID','LEFT');
    $this->db->where('mproductfield.MappingLevel',$MappingLevel);
    $this->db->where('mproductfield.SubProductUID',$SubProductUID);
    $this->db->where('mproductfield.FieldRow',$row);
    $this->db->where('mtemplatemapping.FieldRow',$row);
    $q = $this->db->get()->row();
    return $q;
  }

  function GetSelectedTemplates($TemplateUID, $SubProductUID)
  {
    $this->db->select('group_concat(DISTINCT mproductfield.FieldUID) as Fields, group_concat(DISTINCT mtemplatemapping.StateUID) as StateUID');
    $this->db->from('mproductfield');
    $this->db->join('mtemplatemapping','mproductfield.StateUID = mtemplatemapping.StateUID','LEFT');
    $this->db->where('mtemplatemapping.SubProductUID',$SubProductUID);
    $this->db->where('mtemplatemapping.TemplateUID',$TemplateUID);
    return $this->db->get()->row();
  }


  function SaveTemplateInformation($TemplateUID,$Path)
  {
    if($TemplateUID == '')
    {
      $query = $this->db->query("SELECT max(TemplateUID) as TemplateUID FROM mtemplates");
      $res = $query->row();
      $TemplateUID = $res->TemplateUID;
      $fieldArray = array(
        "TemplatePath"=>$Path['TemplatePath'],
        "TemplateTypeFile"=>$Path['TemplateTypeFile'],
        "TemplateFileName"=>$Path['TemplateFileName']
      );

      $this->db->where(array("TemplateUID"=>$TemplateUID));        
      $result = $this->db->update('mtemplates', $fieldArray);

    }
    else
    {
      $TemplateUID = $TemplateUID;
      $fieldArray = array(
        "TemplatePath"=>$Path['TemplatePath'],
        "TemplateTypeFile"=>$Path['TemplateTypeFile'],
        "TemplateFileName"=>$Path['TemplateFileName']
      );

      $this->db->where(array("TemplateUID"=>$TemplateUID));        
      $result = $this->db->update('mtemplates', $fieldArray);
    }

    
  }

  function SaveTemplate($data)
   {
     if($this->db->insert('mtemplates',$data)==true)
     {

      $TemplateUID=$this->db->insert_id();

                    $data1['ModuleName']='template_add';
                    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
                    $data1['DateTime']=date('y-m-d H:i:s');
                    $data1['TableName']='mtemplates';
                    $data1['UserUID']=$this->session->userdata('UserUID');                
                    $this->common_model->Audittrail_insert($data1);
       return $TemplateUID;
     } 
     else {
       return 0;
     }
   }

  function SaveMappingField($data)
  {
    $data['Active'] = 1;
    if($this->db->insert('mproductfield',$data) == TRUE)
    {
      return 1;
    } else {
      return 0;
    }   
  }

  function SaveMappingTemplate($data)
  {
    if($this->db->insert('mtemplatemapping',$data))
    {
      return 1;
    } else {
      return 0;
    }
  } 




   // Old Sub products



  function SubProductCode()
  {

    $query = $this->db->query("SELECT MAX(`SubProductUID`) AS `AUTO_INCREMENT` FROM `msubproducts`");
    $res = $query->row();
    $id = sprintf("%03d",$res->AUTO_INCREMENT+1);
    $SubProduct="S";
    $auto_number=$SubProduct."".$id;

    return $auto_number;

  }

    
    function GetSubproductDetails(){

        $this->db->select("*,msubproducts.Active");
        $this->db->from('msubproducts');
        $this->db->join('mproducts','msubproducts.ProductUID=mproducts.ProductUID','left');
        $query = $this->db->get();
        return $query->result();
	}

  function GetSubproductDetailsById($SubProductUID)
  {
    $this->db->select("*,msubproducts.Active");
    $this->db->from('msubproducts');
    $this->db->join('mproducts','msubproducts.ProductUID=mproducts.ProductUID','left');
    $this->db->where(array("msubproducts.SubProductUID"=>$SubProductUID));
    $query = $this->db->get();
    return $query->row();
  }

  function GetProductDetails(){
        $this->db->select("*");
        $this->db->from('mproducts');
        $query = $this->db->get();
        return $query->result();
  }

   function saveSubproductDetails($PostArray)
	{

    if($PostArray['SubProductUID']==0)
    {
    $UserLoggin = $this->loggedid = $this->session->userdata('UserUID');

    $Active=1;


		$fieldArray = array(
          "SubProductCode"=>$PostArray['SubProductCode'],
          "SubProductUID"=>$PostArray['SubProductUID'],
					"ProductUID"=>$PostArray['ProductUID'],
          "OrderTypeUID"=>$PostArray['OrderTypeUID'],
          "PriorityUID"=>$PostArray['PriorityUID'],
          "SubProductName"=>$PostArray['SubProductName'],
          "ReportHeading"=>$PostArray['ReportHeading'],
          "ScheduleDuration"=>$PostArray['ScheduleDuration'],
          "RMS"=> !empty($PostArray['RMS']) ? 1 : 0,
          "IsRefinance"=> !empty($PostArray['IsRefinance']) ? 1 : 0,//Author: Anees M A, Date: 250/2020
          "CreatedOn"=>date('Y-m-d H:i:s'),
          "ModifiedByUserUID"=>$UserLoggin,
          "ModifiedOn"=>date('Y-m-d H:i:s'),
					"Active"=>$Active
				);

       $res = $this->db->insert('msubproducts', $fieldArray);
      
 if(!empty($this->db->insert_id())){
// Audit Capture on CREATION 
$InsetData = array(
  'UserUID' => $this->loggedid,
  'ModuleName' => 'SubProduct',
  'Feature' => $this->db->insert_id(),
  'Content' => htmlentities('SubProduct: <b>'.$PostArray['SubProductName'].'</b> Created'),
  'DateTime' => date('Y-m-d H:i:s'));
$this->common_model->InsertAuditTrail($InsetData);
}

 
 // TASK Management Subproduct Level
  foreach ($PostArray['choose'] as $key => $value){

      $Farr = array(
        'SubProductUID'=>$PostArray['SubProductUID'],
        'TaskUID'=>$PostArray['TaskUID'][$key],
        'AutoCreation'=>!empty($PostArray['auto_creation'][$key])?1:0,
        'PreviousTaskUID'=>$PostArray['previous_task'][$key],
        'Action'=>$PostArray['actiontype'][$key]
      );

    if(!empty($PostArray['SubProductTaskUID'][$key])){ // UPDATE METHOD
      foreach ($Farr as $keyAA => $valueAA) {
        $STRR = '';
          // CHACK WITH PREVIOUS VALUE FOR CHANGE
        $Changed = $this->common_model->CheckAudit(
          'SubProductTaskUID',
          $PostArray['SubProductTaskUID'][$key],
          'mSubProductTasks',
          $keyAA,
          $valueAA
        );
        if($keyAA == 'AutoCreation' && (int)$Changed == 1 && $valueAA == 0){
          $STRR .= '<b>'.$keyAA.'</b> Changed from <b>Yes</b> to <b>No</b>';
        }else if($keyAA == 'AutoCreation' && (int)$Changed == 0 && $valueAA == 1){
            $STRR .= '<b>'.$keyAA.'</b> Changed from <b>No</b> to <b>Yes</b>';
        }
        // SubProductUID
        if($keyAA == 'SubProductUID' && $Changed != 'FALSE' && $Changed != ""){
          $Changed = $this->db->query("SELECT * FROM msubproducts WHERE SubProductUID = ".$Changed."")->row()->SubProductName;
          $valueAA = $this->db->query("SELECT * FROM msubproducts WHERE SubProductUID = ".$valueAA."")->row()->SubProductName;
          $STRR .= '<b>'.$keyAA.'</b> Changed from <b>'.$Changed.'</b> to <b>'.$valueAA.'</b>';
        }
        // TaskUID || PreviousTaskUID
        if(($keyAA == 'TaskUID' || $keyAA == 'PreviousTaskUID') && $Changed != 'FALSE' && $Changed != ""){
          $Changed = $this->db->query("SELECT * FROM mTask WHERE TaskUID = ".$Changed."")->row()->ShortDescription;
          $valueAA = $this->db->query("SELECT * FROM mTask WHERE TaskUID = ".$valueAA."")->row()->ShortDescription;
          if($keyAA == 'TaskUID'){
            $STRR .= '<b>Task</b> Changed from <b>'.$Changed.'</b> to <b>'.$valueAA.'</b>';
          }
          if($keyAA == 'PreviousTaskUID'){
            $STRR .= '<b>PreviousTask</b> Changed from <b>'.$Changed.'</b> to <b>'.$valueAA.'</b>';
          }
          
        }
        // WorkflowModuleUID
        if($keyAA == 'WorkflowModuleUID' && $Changed != 'FALSE' && $Changed != ""){
          $Changed = $this->db->query("SELECT * FROM mworkflowmodules WHERE WorkflowModuleUID = ".$Changed."")->row()->WorkflowModuleName;
          $valueAA = $this->db->query("SELECT * FROM mworkflowmodules WHERE WorkflowModuleUID = ".$valueAA."")->row()->WorkflowModuleName;
          $STRR .= '<b>'.$keyAA.'</b> Changed from <b>'.$Changed.'</b> to <b>'.$valueAA.'</b>';
        }
        if($STRR != ''){
          // INSERT AUDIT RAIL
          $InsetData = array(
          'UserUID' => $this->loggedid,
          'ModuleName' => 'SubProduct',
          'Feature' => $PostArray['SubProductUID'],
          'Content' => htmlentities($STRR),
          'DateTime' => date('Y-m-d H:i:s'));
          $this->common_model->InsertAuditTrail($InsetData);
        }
      

      }
      $this->db->where('SubProductTaskUID	',$PostArray['SubProductTaskUID'][$key]);
      $this->db->update('mSubProductTasks',$Farr);
    }else{ // INSERT METHID
       $this->db->insert('mSubProductTasks',$Farr);
      if(!empty($this->db->insert_id())){
        // Audit Capture on CREATION 
        $InsetData = array(
          'UserUID' => $this->loggedid,
          'ModuleName' => 'SubProduct',
          'Feature' => $this->db->insert_id(),
          'Content' => htmlentities('SubProduct Task: <b>'.$PostArray['TaskUID'][$key].'</b> Created'),
          'DateTime' => date('Y-m-d H:i:s'));
        $this->common_model->InsertAuditTrail($InsetData);
      }
        $this->db->insert('mSubProductTasks',$Farr);
    }
  }
       if($res)
          $data=array("msg"=>"Sub Products are Added Successfully","type"=>"color success");
       else
          $data=array("msg"=>"error","type"=>"error");

          echo json_encode($data);
	}

  else{

     $UserLoggin = $this->loggedid = $this->session->userdata('UserUID');

    $Active=isset($PostArray['Active'])? 1:0;
    $fieldArray = array(
          "SubProductCode"=>$PostArray['SubProductCode'],
          "SubProductUID"=>$PostArray['SubProductUID'],
          "ProductUID"=>$PostArray['ProductUID'],
          "OrderTypeUID"=>$PostArray['OrderTypeUID'],
          "PriorityUID"=>$PostArray['PriorityUID'],
          "SubProductName"=>$PostArray['SubProductName'],
          "ReportHeading"=>$PostArray['ReportHeading'],
          "ScheduleDuration"=>$PostArray['ScheduleDuration'],
          "RMS"=> !empty($PostArray['RMS']) ? 1 : 0,
          "CreatedOn"=>date('Y-m-d H:i:s'),
          "ModifiedByUserUID"=>$UserLoggin,
          "ModifiedOn"=>date('Y-m-d H:i:s'),
          "Active"=>$Active
        );
 // Audit Capture on UPDATION
 $defFields = array('SubProductCode','SubProductName','ReportHeading','ScheduleDuration');

 foreach ($fieldArray as $keyAA => $valueAA) {
  $STRR = '';
   // CHACK WITH PREVIOUS VALUE FOR CHANGE
$Changed = $this->common_model->CheckAudit(
  'SubProductUID',
  $PostArray['SubProductUID'],
  'msubproducts',
  $keyAA,
  $valueAA
);
// Default Fields
if(in_array($keyAA,$defFields) && $Changed != 'FALSE' && $Changed != ""){
  $STRR = '<b>'.$keyAA.'</b> Changed from <b>'.$Changed.'</b> to <b>'.$valueAA.'</b>';
}
// OrderTypeUID 
if($keyAA == 'OrderTypeUID' && $Changed != 'FALSE' && $Changed != "" && !empty($Changed)){
  if ($Changed != 'NA') {
    $Changed = $this->db->query("SELECT * FROM mordertypes WHERE OrderTypeUID = '".$Changed."'")->row()->OrderTypeName;
  }

  if($valueAA != 'FALSE' && $valueAA != "" && !empty($valueAA)){
    $valueAA = $this->db->query("SELECT * FROM mordertypes WHERE OrderTypeUID = ".$valueAA."")->row()->OrderTypeName;
  }
  $STRR .= '<b>'.$keyAA.'</b> Changed from <b>'.$Changed.'</b> to <b>'.$valueAA.'</b>';
}
// ProductUID
if($keyAA == 'ProductUID' && $Changed != 'FALSE' && $Changed != ""){
  if ($Changed != 'NA') {
    
    $Changed = $this->db->query("SELECT * FROM mproducts WHERE ProductUID = ".$Changed."")->row()->ProductName;
  }
$valueAA = $this->db->query("SELECT * FROM mproducts WHERE ProductUID = ".$valueAA."")->row()->ProductName;
$STRR .= '<b>'.$keyAA.'</b> Changed from <b>'.$Changed.'</b> to <b>'.$valueAA.'</b>';
}
// PriorityUID 
if($keyAA == 'PriorityUID' && $Changed != 'FALSE' && $Changed != ""){
  if ($Changed != 'NA') {
    
    $Changed = $this->db->query("SELECT * FROM morderpriority WHERE PriorityUID = ".$Changed."")->row()->PriorityName;
  }
  $valueAA = $this->db->query("SELECT * FROM morderpriority WHERE PriorityUID = ".$valueAA."")->row()->PriorityName;
  $STRR .= '<b>Priority </b> Changed from <b>'.$Changed.'</b> to <b>'.$valueAA.'</b>';
}
// Active
if($keyAA == 'Active' && (int)$Changed == 1 && $valueAA == 0){
  $STRR .= '<b>'.$keyAA.'</b> Changed from <b>Yes</b> to <b>No</b>';
}else if($keyAA == 'Active' && (int)$Changed == 0 && $valueAA == 1){
    $STRR .= '<b>'.$keyAA.'</b> Changed from <b>No</b> to <b>Yes</b>';
  }
}

if($STRR != ''){
    // INSERT AUDIT RAIL
  $InsetData = array(
    'UserUID' => $this->loggedid,
    'ModuleName' => 'SubProduct',
    'Feature' => $PostArray['SubProductUID'],
    'Content' => htmlentities($STRR),
    'DateTime' => date('Y-m-d H:i:s'));
  $this->common_model->InsertAuditTrail($InsetData);
  }

    $this->db->where(array("SubProductUID"=>$PostArray['SubProductUID']));
       $res = $this->db->update('msubproducts', $fieldArray);
      
  // echo '<pre>';print_r($PostArray);exit;

   // TASK Management Subproduct Level

  /*Delete Unselected SubProductTasks*/
  $this->db->select('SubProductTaskUID');
  $this->db->from('mSubProductTasks');
  $this->db->where('SubProductUID', $PostArray['SubProductUID']);
  $SubProductTaskUIDs = array_column($this->db->get()->result_array(), 'SubProductTaskUID');

  $CurrentSubProductTaskUIDs = [];
  foreach ($PostArray['choose'] as $key => $value){
    $CurrentSubProductTaskUIDs[] = $PostArray['SubProductTaskUID'][$key];    
  }

  $DeletedSubProductTaskUIDs = array_diff($SubProductTaskUIDs, $CurrentSubProductTaskUIDs);



  if (!empty($DeletedSubProductTaskUIDs)) {

    $this->db->where_in('SubProductTaskUID', $DeletedSubProductTaskUIDs);
    $this->db->delete('mSubProductTasks');
  }

  foreach ($PostArray['choose'] as $key => $value){

      $Farr = array(
        'SubProductUID'=>$PostArray['SubProductUID'],
        'TaskUID'=>$PostArray['TaskUID'][$key],
        'AutoCreation'=>!empty($PostArray['auto_creation'][$key])?1:0,
        'PreviousTaskUID'=>$PostArray['previous_task'][$key],
        'Action'=>$PostArray['actiontype'][$key]
      );
      if(!empty($PostArray['SubProductTaskUID'][$key])){ // UPDATE METHOD
        $this->db->where('SubProductTaskUID	',$PostArray['SubProductTaskUID'][$key]);
        $this->db->update('mSubProductTasks',$Farr);
      }else{ // INSERT METHID
        $this->db->insert('mSubProductTasks',$Farr);
      }
    }

       if($res)

            $data=array("msg"=>"Sub Products are Updated Successfully","type"=>"color success");
       else
            $data=array("msg"=>"error","type"=>"color danger");

          echo json_encode($data);

  }  
}

function delete_subproducts($Id)
{
  $query = $this->db->query("DELETE FROM msubproducts WHERE SubProductUID ='$Id' ");
  if($this->db->affected_rows() > 0)
  {
                  $data1['ModuleName']='subproduct-delete';
                  $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
                  $data1['DateTime']=date('y-m-d H:i:s');
                  $data1['TableName']='msubproducts';
                  $data1['UserUID']=$this->session->userdata('UserUID');                
                  $this->common_model->Audittrail_insert($data1);
    return true;
  }
  else
  {
    return false;
  }
}


function GetSubProductName($SubProductCode = '')
  {
    $query = $this->db->query("SELECT * FROM `msubproducts` 
      LEFT JOIN mproducts ON msubproducts.ProductUID = mproducts.ProductUID 
      WHERE msubproducts.SubProductCode = '$SubProductCode'");

    return $query->row();
  }


  function GetSubProductUID($SubProductCode){

    $query = $this->db->query("SELECT EXISTS(SELECT * FROM msubproducts WHERE SubProductCode = '$SubProductCode') as SubProductCheck;
      ");

    return $query->row();
  }

  function getSelectedProductSubProduct($ProductUID)
  {
        $this->db->select("*,msubproducts.Active");
        $this->db->from('msubproducts');
        $this->db->join('mproducts','msubproducts.ProductUID=mproducts.ProductUID','left');
        $this->db->where('msubproducts.ProductUID',$ProductUID);
        $query = $this->db->get();
        return $query->result();
  }

  /**
    * @description Get all task details
    * @param 
    * @throws no exception
    * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
    * @return nothing 
    * @since  
    * @version  
    */ 
  function GetTaskandWorkflowDetails(){
		$this->db->select("*");
    $this->db->from("mTask");
		$query = $this->db->get();
    $tasks = $query->result();

    $this->db->select("*");
		$this->db->from('mworkflowmodules');
		$this->db->where(array("mworkflowmodules.WorkflowModuleUID !="=>'5'));
		$query2 = $this->db->get();
    $workflows = $query2->result();
    
    return array('tasks'=>$tasks,'workflows'=>$workflows);
    
  }
  /**
    * @description Get All Tasks By SubProduct
    * @param SubProductUID
    * @throws no exception
    * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
    * @return All Tasks referance to SubProduct 
    * @since  13/1/2020
    * @version  Task Management
    */ 
  function getSubproductTasks($SubProductUID){
    $this->db->select('*');
    $this->db->from('mSubProductTasks');
    $this->db->where('SubProductUID',$SubProductUID);
    return $this->db->get()->result();
  }

  /**
    * @description Get All Audits with referance to SubProduct
    * @param SubProductUID
    * @throws no exception
    * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
    * @return AUditLogs for SubProduct 
    * @since  14/1/2020
    * @version  Task Management
    */ 
  function getSubproductLogs($SubProductUID){
    $this->db->select('*');
    $this->db->from('taudittrail');
    $this->db->join('musers','musers.UserUID = taudittrail.UserUID','left');  
    $this->db->where('Feature',$SubProductUID);
    $this->db->where('ModuleName','SubProduct');
    $this->db->order_by('taudittrail.AuditUID','DESC');
    return $this->db->get()->result();
  }

  /**
    *@description Function to get SubProduct Tasks
    *
    * @param $SubProductUID
    * 
    * @throws no exception
    * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
    * @return Array 
    * @since 15/1/2020 
    * @version Task Management 
    *
  */ 
  function getAllTasks($SubProductUID){
    /*Query*/
    $this->db->select('*, mTask.TaskUID');
    $this->db->from('mTask'); 
    $this->db->join('mSubProductTasks', 'mSubProductTasks.TaskUID = mTask.TaskUID AND mSubProductTasks.SubProductUID = "' . $SubProductUID . '"', 'left');
    return $this->db->get()->result();
  }


}
?>
