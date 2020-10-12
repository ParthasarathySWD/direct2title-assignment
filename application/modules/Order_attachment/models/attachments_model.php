<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Attachments_model extends CI_Model {


  function __construct()
  { 
    parent::__construct();
  }

  function GetOrderNumber($OrderUID){
    $this->db->where(array("OrderUID"=>$OrderUID)); 
    $query = $this->db->get('torders');
    return $query->row();
  }

  function GetOrderSourceName($OrderSourceUID){
    if($OrderSourceUID){
      $this->db->select ( '*' ); 
      $this->db->from ( 'torders' );
      $this->db->join ( 'mApiTitlePlatform', 'mApiTitlePlatform.OrderSourceUID = torders.OrderSourceUID' , 'left' );
      $this->db->where(array("torders.OrderSourceUID"=>$OrderSourceUID));
      $query = $this->db->get();
      $torders = $query->row();
      $OrderSourceName = $torders->OrderSourceName;
      return $OrderSourceName;
    }
  }
  function Getsearchsites($OrderUID){

   $query = $this->db->query("SELECT msearchmodes.* FROM msearchmodes JOIN mcountysearchmodes ON mcountysearchmodes.SearchModeUID = msearchmodes.SearchModeUID JOIN mcounties ON mcounties.CountyUID = mcountysearchmodes.CountyUID JOIN mcities ON mcities.CountyUID =mcounties.CountyUID  JOIN torders ON torders.PropertyZipcode = mcities.ZipCode WHERE torders.OrderUID = '".$OrderUID."' AND msearchmodes.SearchModeUID<>5 AND msearchmodes.SearchModeUID <>6 
    UNION 
    SELECT msearchmodes.* FROM msearchmodes WHERE SearchModeUID>=5 AND SearchModeUID<7");

   return $query->result();
 }
  function CheckOrderAssignedtoAbstractor($OrderUID){

    $torders = $this->Common_model->get_orderdetails($OrderUID);
    $AbstractorUID = $torders->AbstractorUID;
    /* @author Yagavi G <yagavi.g@avanzegroup.com> @purpose Send Package Button to show only for Avanze Integration @since April 17th 2020*/
    if($AbstractorUID){
      $this->db->select('*')->from('mabstractor');
      $this->db->where('AbstractorUID', $AbstractorUID);
      $torderabstractor=$this->db->get()->row();
      $OrderSourceUID = $torderabstractor->OrderSourceUID;
      if($OrderSourceUID){
        $this->db->select_max('ApiOutBoundOrderUID');
        $this->db->from('tApiOutBoundOrders');
        $this->db->join ( 'mApiTitlePlatform', 'mApiTitlePlatform.OrderSourceUID = tApiOutBoundOrders.OrderSourceUID' , 'left' );
        $this->db->where(array("tApiOutBoundOrders.OrderUID"=>$OrderUID,"tApiOutBoundOrders.OrderSourceUID"=>$OrderSourceUID,"mApiTitlePlatform.OrderSourceName"=>'Avanze'));
        $query = $this->db->get();
        $tApiOutBoundOrders = $query->row();
        if($tApiOutBoundOrders){
          $ApiOutBoundOrderUID = $tApiOutBoundOrders->ApiOutBoundOrderUID;
          $result = array('ApiOutBoundOrderUID' => $ApiOutBoundOrderUID);
          return $result;
        } else {
          return false;
        }
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  function GetMaxCompleteTimeFrmOrderWorkflow($OrderUID,$UserUID)
  {
    $query = $this->db->query("SELECT MAX(CompleteDateTime) AS CompleteDateTime FROM torderassignment
      LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID
      WHERE torderassignment.WorkflowModuleUID
      IN (1,2,3) AND torderassignment.OrderUID = '$OrderUID' AND torderassignment.AssignedToUserUID = '$UserUID';");
    return $query->row();
  }

  function GetCreatedUser($OrderUID){    

    $OrderSearch = $this->config->item('WorkflowModule')['OrderSearch'];

    $this->db->select ( '*' ); 
    $this->db->from ( 'torderassignment' );
    $this->db->join ( 'musers', 'musers.UserUID = torderassignment.AssignedToUserUID' , 'left' );
    $this->db->where(array("torderassignment.OrderUID"=>$OrderUID,"torderassignment.WorkflowModuleUID"=>$OrderSearch));
    $query = $this->db->get();
    return $query->row();
  }


  function GetSearchDocumentDetailsByID($OrderUID)
  {

   if(in_array($this->session->userdata('RoleType'),array(1,2,3,4,5,6,7,12)))
   {
     $this->db->select ( 'torderdocuments.*, torders.OrderDocsPath,torders.OrderNumber, mdocumenttypes.*, msearchmodes.*' ); 
     $this->db->from ( 'torderdocuments' );
     $this->db->join ( 'mdocumenttypes', 'mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID' , 'left' );
     $this->db->join ( 'msearchmodes', 'msearchmodes.SearchModeUID = torderdocuments.SearchModeUID' , 'left' );
     $this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID' , 'left' );
     $this->db->where(array("torderdocuments.OrderUID"=>$OrderUID,"torderdocuments.IsDocumentApproval"=>1));
     $this->db->order_by("torderdocuments.DocumentCreatedDate asc");
     $query = $this->db->get();
     return $query->result();
   }
   else
   {
     $this->db->select ( 'torderdocuments.*, torders.OrderDocsPath, mdocpermissiontype.PermissionRoleType, mdocumenttypes.*, msearchmodes.*' ); 
     $this->db->from ( 'torderdocuments' );
     $this->db->join ( 'mdocumenttypes', 'mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID' , 'left' );
     $this->db->join ( 'msearchmodes', 'msearchmodes.SearchModeUID = torderdocuments.SearchModeUID' , 'left' );
     $this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID' , 'left' );
     $this->db->join ( 'mdocpermissiontype', 'mdocpermissiontype.PermissionTypeUID = torderdocuments.TypeofPermissions' );
     $this->db->where(array("torderdocuments.OrderUID"=>$OrderUID));

     $this->db->order_by("torderdocuments.DocumentCreatedDate asc");

     $query = $this->db->get();
     $documents =  $query->result();
     $RoleType=$this->session->userdata('RoleType');
//Filter Documents based on permission
     foreach ($documents as $key => $value) 
     {
      if($RoleType == 8)
      {
        if(!(in_array($RoleType, explode(',', $value->PermissionRoleType)) || $value->TypeOfDocument=='Final Reports'))
        {
          unset($documents[$key]);
        }
      }
      else
      {
        if(!in_array($RoleType, explode(',', $value->PermissionRoleType)))
        {
          unset($documents[$key]);
        }
      }
    }
    return $documents;
  }
}

function GetOrderByID($OrderUID){

 $query = $this->db->query("SELECT EXISTS(SELECT OrderUID FROM torders WHERE OrderUID = '$OrderUID') as OrderUID;
  ");

 return $query->row();
}


function GetOrderDocsPath($OrderUID){
  $this->db->where(array("OrderUID"=>$OrderUID));
  $query = $this->db->get('torders');
  return $query->row();
}

function GetDocumentFileName($OrderUID)
{
  $keyword="invoice";
  $query = $this->db->query("SELECT count(DocumentFileName) As DocumentFileName FROM torderdocuments WHERE DocumentFileName LIKE '%$keyword%' and orderUID=$OrderUID ");
  return $query->row()->DocumentFileName;
}
function Inserttorderdocuments($data) 
{
  $this->db->insert('torderdocuments', $data);
  $rowcount = $this->db->affected_rows();
  if($rowcount>0)
  {
    $response['status'] = 'Success';
    $response['msg'] = 'Attachment Successfully add to Documents';
  }
  else
  {
    $response['status'] = 'Failed';
    $response['msg'] = 'Unable to add to Attachment'; 
  }
  return $response;
}
function  get_torders($OrderUID = '')
{    
  $q = $this->db->query("SELECT torders.*,torderpropertyinfo.*,torderdocuments.*,msubproducts.*,mproducts.*,mcustomers.*, torders.PropertyCountyName AS CountyName, torders.PropertyCityName AS CityName,mcustomers.CustomerName, torders.PropertyStateCode AS StateCode, torders.PropertyStateCode AS StateName,
    (SELECT GROUP_CONCAT(PartyName) FROM tordermortgageparties t2 WHERE PartyTypeUID = 2 AND t2.MortgageSNo = tordermortgages.MortgageSNo) AS Mortgagee,
    (SELECT GROUP_CONCAT(PartyName) FROM torderdeedparties t2 WHERE PartyTypeUID = 4 AND t2.DeedSNo = torderdeeds.DeedSNo) AS Grantee,
    (SELECT Max(DeedRecorded) FROM `torderdeeds` WHERE OrderUID = '$OrderUID' LIMIT 1) AS LastDeedRecorded,
    (SELECT StateCode FROM mstates WHERE mstates.StateUID = mcustomers.CustomerStateUID) AS CustomerStateCode,
    (SELECT CountyName FROM mcounties WHERE mcounties.CountyUID = mcustomers.CustomerCountyUID) AS CustomerCountyName,
    (SELECT CityName FROM mcities WHERE mcities.CityUID = mcustomers.CustomerCityUID) AS CustomerCityName,
    (SELECT PRName FROM torderpropertyroles WHERE PropertyRoleUID = 5 and  OrderUID='$OrderUID' LIMIT 1) AS ReportBorrowerName

    FROM torders
    LEFT JOIN torderdeeds ON torderdeeds.OrderUID = torders.OrderUID
    LEFT JOIN tordermortgages ON tordermortgages.OrderUID = torders.OrderUID
                        /*LEFT JOIN mcounties ON mcounties.CountyUID = torders.PropertyCountyUID 
                        LEFT JOIN mstates ON mstates.StateUID = torders.PropertyStateUID 
                        LEFT JOIN mcities ON mcities.CityUID = torders.PropertyCity*/
                        LEFT JOIN mcustomers ON mcustomers.CustomerUID = torders.CustomerUID
                        LEFT JOIN torderpropertyinfo ON torderpropertyinfo.OrderUID = torders.OrderUID
                        LEFT JOIN msubproducts ON msubproducts.SubProductUID = torders.SubProductUID
                        LEFT JOIN mproducts ON mproducts.ProductUID = msubproducts.ProductUID
                        LEFT JOIN torderdocuments ON torderdocuments.OrderUID = torders.OrderUID
                        WHERE torders.OrderUID = '$OrderUID' ");
  return $q->row();
}
function getBorrowers($OrderUID)
{
  $query = $this->db->query("SELECT PRName FROM torderpropertyroles WHERE PropertyRoleUID = 5 and  OrderUID='$OrderUID' ");
  return $query->result();
}

public function GetFileNameAlready($FileName, $ext, $itr, $OrderUID)
{
  $DocumentFileName=$FileName.$itr.$ext;
  $query=$this->db->get_where('torderdocuments',array('OrderUID'=>$OrderUID,
   'DocumentFileName'=>$DocumentFileName));
  $numrows=$query->num_rows();
  if($numrows==0)
  { 
    return $DocumentFileName;
  }
  $itr+=1;
  return $this->GetFileNameAlready($FileName, $ext, $itr,$OrderUID);
}

public function GetAvailFileName($FileName, $ext, $itr, $OrderUID)
{
  $DocumentFileName=$FileName.$itr.$ext;
  if ($itr==0) {
    $DocumentFileName=$FileName.$ext;
  }
  $DocumentFileName=preg_replace("/[^a-z0-9\_\-\.\s]/i", '', $DocumentFileName);
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
function saveAttachmentEditDetails($PostArray,$OrderNo,$OrderUID)
{

  $OrderDocsPath = $this->GetOrderDocsPath($OrderUID);
  $OrderDocsPath = $OrderDocsPath->OrderDocsPath;

  /* $date = date('Ymd');

    $Datecreated = Date('Ymd',strtotime($PostArray['Datecreated']));
    
    $OrderDocsPath = './uploads/searchdocs/'.$Datecreated.'/'.$OrderNo."/";*/

    // $OrderDocsPath = './uploads/searchdocs/'.$OrderNo."/";

    

    $OldValue = $PostArray["OldValue"];
    $NewValue = $PostArray["OldValue"];


    $AttachmentOldValue = FCPATH.$OrderDocsPath.$OldValue;
    $AttachmentNewValue = FCPATH.$OrderDocsPath.$NewValue;

    rename($AttachmentOldValue,$AttachmentNewValue);

    $data1['ModuleName']='Attachment Rename_update';
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['DateTime']=date('y-m-d H:i:s');
    $data1['TableName']='torderdocuments';
    $data1['UserUID']=$this->session->userdata('UserUID');
    $data1['OldValue']=$OldValue; 
    $data1['NewValue']=$NewValue;
    $data1['OrderUID']=$OrderUID;
    $data1['FieldUID']='805';                                
    $this->common_model->Audittrail_insert($data1);


    $query = $this->db->query("UPDATE torderdocuments set " . $PostArray["column"] . " = '".$PostArray["editval"]."' WHERE  DocumentFileName='".$PostArray["DocumentFileName"]."'");

    return $query;
  }

  function Gettorders($OrderUID)
  { 
   $this->db->where(array("OrderUID"=>$OrderUID));
   $query = $this->db->get('torders');
   return $query->row_array();
 }


 function GetOrderInformation($OrderUID)
 { 
  $this->db->select('*')->from('torders');
  $this->db->join('mstates', 'torders.PropertyStateUID=mstates.StateUID','left');
  $this->db->join('mcounties', 'torders.PropertyCountyUID=mcounties.CountyUID','left');
  $this->db->join('mcities', 'torders.PropertyCity=mcities.CityUID','left');
  $this->db->where(array("OrderUID"=>$OrderUID));
  $query = $this->db->get();
  return $query->row();
}

function GetOrderPropertyRoles($OrderUID)
{ 
  $this->db->select('torderpropertyroles.PRName')->from('torders');
  $this->db->join('torderpropertyroles', 'torders.OrderUID=torderpropertyroles.OrderUID','left');
  $this->db->where(array("torders.OrderUID"=>$OrderUID, 'torderpropertyroles.PropertyRoleUID'=>5));
  $query = $this->db->get();
  return $query->result();
}


function GetEmailIdByCheckBox($RoleType = '',$OrderUID= '')
{
  if($RoleType == 1){

    $query = $this->db->query("SELECT * FROM `musers` 
      LEFT JOIN mroles ON musers.RoleUID = mroles.RoleUID 
      WHERE musers.RoleUID = '$RoleType'");
    return $query->result();

  }

  elseif ($RoleType == 6) {

    $query = $this->db->query("SELECT * FROM `musers` 
      LEFT JOIN mroles ON musers.RoleUID = mroles.RoleUID 
      WHERE musers.RoleUID = '$RoleType'");
    return $query->result();

  }

  elseif($RoleType == 7){

    $UserUID = $this->session->userdata('UserUID');

    $query = $this->db->query("SELECT * FROM `musers` 
      LEFT JOIN torderassignment ON musers.UserUID = torderassignment.AssignedToUserUID 
      WHERE torderassignment.OrderUID = '$OrderUID'");
    return $query->result();

  }

  elseif ($RoleType == 8) {

    $query = $this->db->query("SELECT * FROM `mcustomers`       
      LEFT JOIN torders ON torders.CustomerUID = mcustomers.CustomerUID 
      WHERE torders.OrderUID = '$OrderUID'");
    return $query->result();
  }

}

function delete_attachment($orderUID,$DocumentFileName)
{
  $this->db->where('OrderUID', $orderUID);
  $this->db->where('DocumentFileName', $DocumentFileName);
  $this->db->delete('torderdocuments');
  if($this->db->affected_rows() > 0)
  {
    return true;
  }
  else
  {
    return false;

  }
}

function delete_attachment_by_approval($orderUID,$DocumentTypeUID,$DocumentType,$DocumentFileName,$loggedid,$DeletedRequestDateTime,$DeleteComment)
{
  echo "string";exit();
  $ApprovalFunction = 'Document Delete';
  $ApprovalStatus = '0';
  $data = array(
    'OrderUID' => $orderUID,
    'ApprovalFunction' => $ApprovalFunction,
    'RaisedByUserUID' => $loggedid,
    'RaisedDatetime' => $DeletedRequestDateTime,
    'ApprovalStatus' => $ApprovalStatus,
    'DocumentTypeUID' => $DocumentTypeUID,
    'DocumentFileName' => $DocumentFileName,
    'TypeOfDocument' => $DocumentType,
    'Remark'=>$DeleteComment //@Desc Delete comment @Author Jainulabdeen @On Apr 16 2020
  );

  $this->db->insert('torderapprovals', $data);
  if($this->db->affected_rows() > 0)
  {
    return true;
  }
  else
  {
    return false;
  }
}

function GetUserName($id)
{
  $query = $this->db->query("SELECT UserName FROM musers WHERE UserUID ='$id' ");
  $res = $query->row();
  return $res->UserName;
}
function Getdocuments($OrderUID)
{
  $query = $this->db->query("SELECT * FROM torderdocuments WHERE OrderUID ='$OrderUID' ");
  return $query->row();
}
function CheckMailSendExist($OrderrUID)
{
  $this->db->select('IsMailSend')->from('torders');
  $this->db->where(array("OrderUID"=>$OrderrUID));
  $query = $this->db->get();
  $res =  $query->row();
  return $res->IsMailSend;
}

function get_customer_vendorpricing($OrderUID){
  $this->db->select ( '*' ); 
  $this->db->from ( 'torders' );
  $this->db->where ('torders.OrderUID',$OrderUID);
  $this->db->join('mcustomers', 'mcustomers.CustomerUID = torders.CustomerUID');
  $this->db->join('mabstractor', 'mabstractor.AbstractorUID = torders.AbstractorUID','left');
  $query = $this->db->get();
  return $query->row();
}

function getApioutboundorders($OrderUID)
{ 
    $this->db->select("*");
    $this->db->from('mApiTitlePlatform');
    $this->db->where(array("mApiTitlePlatform.OrderSourceName"=>'pto'));
    $query = $this->db->get();
    $SourceName = $query->row();
    $OrderSourceUID = $SourceName->OrderSourceUID;

    $Status = array('Accepted');
    $this->db->select('ApiOutBoundOrderUID');
    $this->db->from('tApiOutBoundOrders');
    $this->db->where(array("tApiOutBoundOrders.OrderUID"=>$OrderUID,"tApiOutBoundOrders.OrderSourceUID"=>$OrderSourceUID));
    $this->db->where_in('Status',$Status); 
    $query = $this->db->get()->result();
    return $query;
}

/**
  *@description Function to return borrowers email array
  *
  * @param OrderUID URI Segment
  * 
  * @throws no exception
  * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
  * @return Array 
  * @since 31/12/2019 
  * @version Vendor Management 
  *
*/ 
function getBorrowerEmails($OrderUID){
  /*Code*/

  $this->db->select('WS_CONCAT("", Email, "(","Borrower", ")")', false);
  $this->db->from('torderpropertyroles');
  $this->db->where('OrderUID', $OrderUID);
  $this->db->where('torderpropertyroles.PropertyRoleUID IN (5,7,27)', NULL, FALSE);
  $this->db->where('Email IS NOT NULL', NULL, FALSE);
  $this->db->where("Email  NOT REGEXP '^[^@]+@[^@]+\.[^@]{2,}$'", NULL, FALSE);
  return $this->db->get()->result();
}

/**
  *@description Function to return Vendor email array
  *
  * @param OrderUID URI Segment
  * 
  * @throws no exception
  * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
  * @return Array 
  * @since 31/12/2019 
  * @version Vendor Management 
  *
*/ 
function getVendorEmails($OrderUID){
  /*Code*/

  $this->db->select('WS_CONCAT("", Email, "(","Borrower", ")")', false);
  $this->db->from('torderpropertyroles');
  $this->db->where('OrderUID', $OrderUID);
  $this->db->where('torderpropertyroles.PropertyRoleUID IN (5,7,27)', NULL, FALSE);
  $this->db->where('Email IS NOT NULL', NULL, FALSE);
  $this->db->where("Email  NOT REGEXP '^[^@]+@[^@]+\.[^@]{2,}$'", NULL, FALSE);
  return $this->db->get()->result();
}

/**
  * @description Getting All Emails Related to Order and all organizational user emails
  * @param OrderUID
  * @throws no exception
  * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
  * @return Array of Objects 
  * @since  02/1/2020
  * @version  Vendor Management
  */ 

  function getOrderMails($OrderUID,$UserType){
    // Customer
    $this->db->select('CustomerPContactEmailID, CONCAT_WS("","(",CustomerName,")", "(Customer) ", mcustomers.CustomerPContactEmailID) AS Email, CustomerName, CustomerNumber, ', false);
    $this->db->from('torders');
    $this->db->join('mcustomers','mcustomers.CustomerUID = torders.CustomerUID','left');
    $this->db->where('mcustomers.CustomerPContactEmailID is NOT NULL', NULL, FALSE);
    $this->db->where('torders.OrderUID',$OrderUID);
    $OrderCustomersPContactEmail = $this->db->get()->result();

    // Customer
    $this->db->select('CustomerOrderAckEmailID, CONCAT_WS("","(",CustomerName,")", "(Customer) ", mcustomers.CustomerOrderAckEmailID) AS Email, CustomerName, CustomerNumber, ', false);
    $this->db->from('torders');
    $this->db->join('mcustomers','mcustomers.CustomerUID = torders.CustomerUID','left');
    $this->db->where('mcustomers.CustomerPContactEmailID is NOT NULL', NULL, FALSE);
    $this->db->where('torders.OrderUID',$OrderUID);
    $OrderCustomerOrderAckEmail = $this->db->get()->result();

    $OrderCustomers = array_filter(array_merge($OrderCustomersPContactEmail, $OrderCustomerOrderAckEmail));

    // All Customer
    $this->db->select('CONCAT_WS("","(",musers.UserName,")", "(Customer) ", musers.UserEmailID) AS Email', false);
    $this->db->from('mcustomers');
    $this->db->join('torders', 'torders.CustomerUID = mcustomers.CustomerUID');
    $this->db->join('musers', 'musers.CustomerUID = mcustomers.CustomerUID');
    $this->db->where('(musers.UserEmailID is NOT NULL OR musers.UserEmailID <> "")', NULL, FALSE);
    $this->db->where('torders.OrderUID',$OrderUID);
    $AllCustomers = $this->db->get()->result();
    $AllCustomers = array_filter(array_merge($OrderCustomers, $AllCustomers));

    // Borrowers torderpropertyroles
    $this->db->select('mpropertyroles.PropertyRoleName, PREmailID AS Email, PRName, PRTitle', false);
    $this->db->from('torderpropertyroles');
    $this->db->join('mpropertyroles', 'torderpropertyroles.PropertyRoleUID = mpropertyroles.PropertyRoleUID');
    $this->db->where('OrderUID',$OrderUID);
    $this->db->where('PREmailID is NOT NULL', NULL, FALSE);
    $OrderBorrowers = $this->db->get()->result();

    $BorrowerEmail = [];
    foreach ($OrderBorrowers as $key => $borrower) {
      $email = explode(",", $borrower->Email);
      foreach ($email as $key => $e) {
        if (!empty($e)) {
          $borrower->Email = "(".$borrower->PRName.")(".$borrower->PropertyRoleName.")".$e;
          $BorrowerEmail[] = $borrower;
        }
      }
    }

    // Vendors torderabstractor
    $this->db->select('mabstractorcontact.Email AS cEmail, CONCAT_WS("", mabstractor.AbstractorFirstName, " ", mabstractor.AbstractorLastName) AS AbstractorName, mabstractor.AbstractorCompanyName, musers.UserEmailID, musers.UserName, musers.LoginID, mabstractor.Email, mabstractor.PaypalEmailID', false);
    $this->db->from('torderabstractor');
    $this->db->join('mabstractorcontact','mabstractorcontact.AbstractorContactUID = torderabstractor.ContactUID', 'left');
    $this->db->join('mabstractor','mabstractor.AbstractorUID = torderabstractor.AbstractorUID', 'left');
    $this->db->join('musers','musers.UserUID = mabstractor.UserUID', 'left');
    $this->db->group_by('mabstractorcontact.Email');
    $this->db->where('torderabstractor.OrderUID',$OrderUID);
    $OrderActiveAbstractors = $this->db->get()->result();
    $OrderActiveAbstractorEmails = [];
    $UniqueEmail = [];
    $UniquecEmail = [];
    $UniqueUserEmailID = [];
    $UniquePaypalEmailID = [];

    foreach ($OrderActiveAbstractors as $key => $value) {
      if (!empty($value->Email) && !in_array($value->Email, $UniqueEmail)) {
        
        $obj = new stdClass();
        if(!empty($value->AbstractorName)){
          $obj->Email = "(".$value->AbstractorName.")(Vendor)".$value->Email;
        } else {
          $obj->Email = "(Vendor)".$value->Email;
        }
        $obj->AbstractorName = $value->AbstractorName;
        $obj->AbstractorCompanyName = $value->AbstractorCompanyName;

        $obj->LoginID = $value->LoginID;
        $obj->UserName = $value->UserName;

        $OrderActiveAbstractorEmails[] = $obj;
        $UniqueEmail[] = $value->Email;        

      }
      
      if (!empty($value->cEmail) && !in_array($value->cEmail, $UniquecEmail)) {
        $obj = new stdClass();
        if(!empty($value->cName)){
          $obj->Email = "(".$value->cName.")(Vendor)".$value->cEmail;
        } else {
          $obj->Email = "(Vendor)".$value->cEmail;
        }
        $obj->AbstractorName = $value->AbstractorName;
        $obj->AbstractorCompanyName = $value->AbstractorCompanyName;

        $obj->LoginID = $value->LoginID;
        $obj->UserName = $value->UserName;

        $OrderActiveAbstractorEmails[] = $obj;
        $UniquecEmail[] = $value->cEmail;

      }
      
      if(!empty($value->UserEmailID) && !in_array($value->UserEmailID, $UniqueUserEmailID)) {
        $obj = new stdClass();
        if(!empty($value->cName)){
          $obj->Email = "(".$value->UserName.")(Vendor)".$value->UserEmailID;
        } else {
          $obj->Email = "(Vendor)".$value->UserEmailID;
        }
        $obj->AbstractorName = $value->AbstractorName;
        $obj->AbstractorCompanyName = $value->AbstractorCompanyName;
        $obj->LoginID = $value->LoginID;
        $obj->UserName = $value->UserName;
        $OrderActiveAbstractorEmails[] = $obj;
        $UniqueUserEmailID[] = $value->UserEmailID;

      }      

      if(!empty($value->PaypalEmailID) && !in_array($value->PaypalEmailID, $UniquePaypalEmailID)) {
        $obj = new stdClass();
        if(!empty($value->AbstractorName)){
        $obj->Email = "(".$value->AbstractorName.")(Vendor)".$value->PaypalEmailID;
        } else {
        $obj->Email = "(Vendor)".$value->PaypalEmailID;
        }
        $obj->AbstractorName = $value->AbstractorName;
        $obj->AbstractorCompanyName = $value->AbstractorCompanyName;
        $obj->LoginID = $value->LoginID;
        $obj->UserName = $value->UserName;
        $OrderActiveAbstractorEmails[] = $obj;
        $UniquePaypalEmailID[] = $value->PaypalEmailID;

      }
    }


    /*Vendor Unassigned*/
    $this->db->select('mabstractorcontact.Email AS cEmail, CONCAT_WS("", mabstractor.AbstractorFirstName, " ", mabstractor.AbstractorLastName) AS AbstractorName, mabstractor.AbstractorCompanyName, musers.UserEmailID, musers.UserName, musers.LoginID, mabstractor.Email', false);
    $this->db->from('torderabstractorunassign');
    $this->db->join('mabstractorcontact','mabstractorcontact.AbstractorContactUID = torderabstractorunassign.ContactUID', 'left');
    $this->db->join('mabstractor','mabstractor.AbstractorUID = mabstractorcontact.AbstractorUID');
    $this->db->join('musers','musers.UserUID = mabstractor.UserUID', 'left');
    $this->db->group_by('mabstractorcontact.Email');
    $this->db->where('torderabstractorunassign.OrderUID',$OrderUID);
    $OrderUnassignAbstractors = $this->db->get()->result();


    $OrderUnAssignAbstractorEmails = [];
    $aUniqueEmail = [];
    $aUniquecEmail = [];
    $aUniqueUserEmailID = [];
    $aUniquePaypalEmailID = [];


    foreach ($OrderUnassignAbstractors as $key => $value) {
      if (!empty($value->Email) && !in_array($value->Email, $aUniqueEmail)) {
        
        $obj = new stdClass();
        if(!empty($value->AbstractorName)){
          $obj->Email = "(".$value->AbstractorName.")(Vendor)".$value->Email;
        } else {
          $obj->Email = "(Vendor)".$value->Email;
        }
        $obj->AbstractorName = $value->AbstractorName;
        $obj->AbstractorCompanyName = $value->AbstractorCompanyName;
        $OrderUnAssignAbstractorEmails[] = $obj;
        $aUniqueEmail[] = $value->Email;

      }
      
      if (!empty($value->cEmail) && !in_array($value->cEmail, $aUniquecEmail)) {
        
        $obj = $value;
        if(!empty($value->cName)){
          $obj->Email = "(".$value->cName.")(Vendor)".$value->cEmail;
        } else {
          $obj->Email = "(Vendor)".$value->cEmail;
        }
        $obj->AbstractorName = $value->AbstractorName;
        $obj->AbstractorCompanyName = $value->AbstractorCompanyName;
        $OrderUnAssignAbstractorEmails[] = $obj;
        $aUniquecEmail[] = $value->cEmail;

      }
      
      if(!empty($value->UserEmailID) && !in_array($value->UserEmailID, $aUniqueUserEmailID)) {
        $obj = $value;
        if(!empty($value->cName)){
          $obj->Email = "(".$value->UserName.")(Vendor)".$value->UserEmailID;
        } else {
          $obj->Email = "(Vendor)".$value->UserEmailID;
        }
        $obj->AbstractorName = $value->AbstractorName;
        $obj->AbstractorCompanyName = $value->AbstractorCompanyName;
        $obj->LoginID = $value->LoginID;
        $obj->UserName = $value->UserName;
        $OrderUnAssignAbstractorEmails[] = $obj;
        $aUniqueUserEmailID[] = $value->UserEmailID;

      }
    }

    $OrderAbstractors = array_merge($OrderActiveAbstractorEmails, $OrderUnAssignAbstractorEmails);

     // All Vendors
    $this->db->select('CONCAT_WS("","(", mabstractorcontact.ContactName, ")","(Vendor) ", mabstractorcontact.Email) AS Email, CONCAT_WS("", mabstractor.AbstractorFirstName, " ", mabstractor.AbstractorLastName) AS AbstractorName, mabstractor.AbstractorCompanyName', false);
    $this->db->from('mabstractorcontact');
    $this->db->where('(mabstractorcontact.AbstractorUID IN (SELECT AbstractorUID FROM torderabstractor WHERE OrderUID = "'.$OrderUID.'") OR mabstractorcontact.AbstractorUID IN (SELECT AbstractorUID FROM torderabstractorunassign WHERE OrderUID = "'.$OrderUID.'") )', NULL, FALSE);
    $this->db->join('mabstractor','mabstractor.AbstractorUID = mabstractorcontact.AbstractorUID');
    $this->db->where('(mabstractorcontact.Email is NOT NULL OR mabstractorcontact.Email <> "")', NULL, FALSE);
    $this->db->group_by('mabstractorcontact.Email');
    $AllActiveAbstractors = $this->db->get()->result();

     // All Vendors
    $this->db->select('CONCAT_WS("","(", musers.UserName, ")","(Vendor) ", musers.UserEmailID) AS Email, CONCAT_WS("", mabstractor.AbstractorFirstName, " ", mabstractor.AbstractorLastName) AS AbstractorName, mabstractor.AbstractorCompanyName', false);
    $this->db->from('mabstractor');
    $this->db->join('musers','mabstractor.AbstractorUID = musers.AbstractorUID');
    $this->db->where('(mabstractor.AbstractorUID IN (SELECT AbstractorUID FROM torderabstractor WHERE OrderUID = "'.$OrderUID.'") OR mabstractor.AbstractorUID IN (SELECT AbstractorUID FROM torderabstractorunassign WHERE OrderUID = "'.$OrderUID.'") )', NULL, FALSE);
    $this->db->where('(musers.UserEmailID is NOT NULL OR musers.UserEmailID <> "")', NULL, FALSE);
    $AllActiveAbstractorUsers = $this->db->get()->result();

     // All Vendors
    $this->db->select('CONCAT_WS("","(", mabstractor.AbstractorFirstName, ")","(Vendor) ", mabstractor.Email) AS Email, CONCAT_WS("", mabstractor.AbstractorFirstName, " ", mabstractor.AbstractorLastName) AS AbstractorName, mabstractor.AbstractorCompanyName', false);
    $this->db->from('mabstractor');
    $this->db->where('(mabstractor.AbstractorUID NOT IN (SELECT AbstractorUID FROM torderabstractor WHERE OrderUID = "'.$OrderUID.'") AND mabstractor.AbstractorUID NOT IN (SELECT AbstractorUID FROM torderabstractorunassign WHERE OrderUID = "'.$OrderUID.'") )', NULL, FALSE);
    $this->db->where('(mabstractor.Email is NOT NULL OR mabstractor.Email <> "")', NULL, FALSE);
    $this->db->group_by('mabstractor.Email');
    $AllActiveAbstractorEmail = $this->db->get()->result();

    $AllActiveAbstractors = array_filter(array_merge($AllActiveAbstractors, $AllActiveAbstractorUsers, $AllActiveAbstractorEmail));

    // Organization users
    $this->db->select('CONCAT_WS("","(", musers.UserName, ")","(", mroles.RoleName, ") ", musers.UserEmailID) AS Email, UserName, LoginID', false);
		$this->db->from('musers');
    $this->db->join('mroles','mroles.RoleUID = musers.RoleUID','left');
		$this->db->where('(UserEmailID IS NOT NULL OR UserEmailID <> "")', NULL, FALSE);
    $this->db->where('(musers.UserUID IN (SELECT UserUID FROM taudittrail WHERE OrderUID = "'.$OrderUID.'"))', NULL, FALSE);
    $OrderOrgUsers = $this->db->get()->result();

     // Organization users
    $this->db->select('CONCAT_WS("","(", musers.UserName, ")","(", mroles.RoleName, ") ", musers.UserEmailID) AS Email, UserName, LoginID', false);
		$this->db->from('musers');
    $this->db->join('mroles','mroles.RoleUID = musers.RoleUID','left');
		$this->db->where('(UserEmailID IS NOT NULL OR UserEmailID <> "")', NULL, FALSE);
    $this->db->where('(mroles.RoleType IN (1,2,3,4,5,6,7,11,12))', NULL, FALSE);
    $AllOrgUsers = $this->db->get()->result();

    $Emails = [];
    // OrderClients
    $Emails['OrderClients'] = $OrderCustomers;
    // OrderVendors
    $Emails['OrderVendors'] = $OrderAbstractors;
    // OrderBorrowers
    $Emails['OrderBorrowers'] = $BorrowerEmail;
    // OrderOrgUsers
    $Emails['OrderOrgUsers'] = $OrderOrgUsers;

    // ClientContacts
    $Emails['ClientContacts'] = $AllCustomers;
    // VendorContacts
    $Emails['VendorContacts'] = $AllActiveAbstractors;
    // OrganizationContacts
    $Emails['OrganizationContacts'] = $AllOrgUsers;

    return $Emails;

  }

  /**
    * @description Order Mail Audit
    * @param OrderUID
    * @throws no exception
    * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
    * @return nothing 
    * @since  3/1/2020
    * @version  Order level mail  DI1-T83
    */ 
   function getOrderMailLogs($OrderUID){
    $OrderUID = explode(',',$OrderUID);
    $this->db->select('*');
		$this->db->from('taudittrail');
		$this->db->where("(ModuleName='OrderMailLog')");
		$this->db->where_in('taudittrail.Feature',$OrderUID);
		$this->db->join('musers','musers.UserUID = taudittrail.UserUID','left');
		$this->db->order_by('DateTime','desc');
		return $this->db->get()->result();
   }

   function getorderlevel_mailtemplates(){
   	return $this->db->query("SELECT * FROM (`mEmailTemplate`) WHERE `Selecttype` = 'Order' AND mEmailTemplate.EmailTemplateName IS NOT NULL")->result();
	}	

  /**
  * @description Checking of Validation for API
  * @param OrderUID
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @return nothing 
  * @since  25 March 2020
  * @version  
  */ 

  function CheckValidationForAPI($OrderUID){
    $OrderSourceName = $this->Common_model->GetOrderSourceNameByOrderUID($OrderUID);
    $this->db->select("*");
    $this->db->from('torderlegaldescription');
    $this->db->where(array("torderlegaldescription.OrderUID"=>$OrderUID));
    $query = $this->db->get();
    $torderlegaldescription = $query->row();
    $LegalDescription = $torderlegaldescription->LegalDescription;
    $res = '';
    if($OrderSourceName == 'RealEC'){
      if(!empty(trim($LegalDescription))){
        $res ='legal';
      } 
    } else {
      $res = 'legal';
    }  
    return $res;
  }
  /**
* Description Verify User
*
* @created date 4.16.2020
* @author jainulabdeen.b
**/
  function VerifyPassword($userid)
  {
    $this->db->where('UserUID',$userid);
    return $this->db->get('musers')->row();
  }
}
?>
