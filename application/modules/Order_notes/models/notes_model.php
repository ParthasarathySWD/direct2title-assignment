<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Notes_model extends CI_Model {


    
  function __construct()
  { 
    parent::__construct();
  }

  function ApiNotes($NoteUID,$EventCode){

    $notes = array(
      "EventCode"=>$EventCode,
    );

    $this->db->where(array("tordernotes.NoteUID"=>$NoteUID));
    $res = $this->db->update('tordernotes',$notes);
  }

  function get_Sections()
  {
    $this->db->select("*");
    $this->db->from('mreportsections');
    $query = $this->db->get();
    return $query->result();
  }

  function GetNoteTypeUID($NoteUID)
  {
    $this->db->select("*");
    $this->db->from('tordernotes');
    $this->db->join('mreportsections','tordernotes.SectionUID=mreportsections.SectionUID','left');
    $this->db->where(array("tordernotes.NoteUID"=>$NoteUID));
    $query = $this->db->get();
    return $query->row();
  }

  function GetNoteTypeName($SectionUID)
  {
    $this->db->select("*");
    $this->db->from('mreportsections');
    $this->db->where(array("mreportsections.SectionUID"=>$SectionUID));
    $query = $this->db->get();
    return $query->row();
  }

    function GetRoleDetails($RoleType){

        $this->db->select("*");
        $this->db->from('mroles');
        $this->db->where(array("mroles.RoleType"=>$RoleType));  
        $query = $this->db->get();
        return $query->row();
    }

    function GetRole(){

        $this->db->select("*");
        $this->db->from('mroles');
        $query = $this->db->get();
        return $query->result();
    }

     function GetChatDetails($OrderUID){

        $this->db->select("*,tordernotes.CreatedOn");
        $this->db->from('tordernotes');
        $this->db->join('musers','tordernotes.CreatedByUserUID=musers.UserUID','left');
        $this->db->join('mreportsections','tordernotes.SectionUID=mreportsections.SectionUID','left');
        $this->db->where(array("tordernotes.OrderUID"=>$OrderUID));  
        $this->db->where('(tordernotes.IsPrivate = 0 OR tordernotes.IsPrivate = '.$this->loggedid.')');
        $query = $this->db->get();
        return $query->result();
  }


  function GetChatDetailsByuserUID($OrderUID){

        $this->db->distinct();
        $this->db->select("tordernotes.CreatedByUserUID");
        $this->db->from('tordernotes');
        $this->db->join('musers','tordernotes.CreatedByUserUID=musers.UserUID','left');
        $this->db->join('mreportsections','tordernotes.SectionUID=mreportsections.SectionUID','left');
        $this->db->where(array("tordernotes.OrderUID"=>$OrderUID));  
        $this->db->where('(tordernotes.IsPrivate = 0 OR tordernotes.IsPrivate = '.$this->loggedid.')');
        //$this->db->group_by('tordernotes.Note');
        $query = $this->db->get();
        return $query->row();
  }


/*  function get_notes($OrderUID,$loggedid)
  {
       $this->db->select('tordernotes.NoteUID,Note,tordernotes.OrderUID,tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,musers.UserName,mreportsections.SectionName,mreportsections.SectionColor,tordernotes.AttachedFile,tordernotes.RoleType');
        $this->db->from('tordernotes');
        $this->db->join ( 'musers', 'tordernotes.CreatedByUserUID = musers.UserUID' , 'left' );
        $this->db->join ( 'mreportsections', 'tordernotes.SectionUID = mreportsections.SectionUID' , 'left' );
        $this->db->where (array('tordernotes.OrderUID'=>$OrderUID));
        $this->db->order_by('tordernotes.CreatedOn');
        $query = $this->db->get();
        return $query->result();
  }*/


  function GetUserRoleTypeDetails($UserUID){

        $this->db->select("RoleType,RoleName,CanFollowUp");
        $this->db->from('musers');
        $this->db->join ( 'mroles', 'musers.RoleUID = mroles.RoleUID' , 'inner' );
        $this->db->where(array("musers.UserUID"=>$UserUID));  
        $query = $this->db->get();
        return $query->row();
  }


  function CheckUserUID($OrderUID)
  {
      $query = $this->db->query("SELECT EXISTS(SELECT * FROM tordernotes WHERE OrderUID = '$OrderUID') as CheckNotes;
          ");

          return $query->row();
  }


  /*function get_notes($OrderUID,$loggedid)
  {

    $UserUID = $loggedid;
    $CreatedByUserUID = null;
    $UserRole = $this->GetUserRoleTypeDetails($UserUID);
    $UserRoleType = $UserRole->RoleType;

    $CheckUserUID = $this->CheckUserUID($OrderUID);  
    $CheckNotes = $CheckUserUID->CheckNotes;

    if($CheckNotes == 1){

         $CreatedUserUID = $this->GetChatDetailsByuserUID($OrderUID);
         $CreatedByUserUID = $CreatedUserUID->CreatedByUserUID;

    }

    if ($UserUID == $CreatedByUserUID && $UserRoleType == 6) 
    {

        $this->db->select('tordernotes.NoteUID,Note,tordernotes.OrderUID,tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,musers.UserName,musers.UserUID,mreportsections.SectionName,mreportsections.SectionColor,mreportsections.ForeColor,tordernotes.AttachedFile,tordernotes.RoleType,msystemnotes.SystemNotes');
        $this->db->from('tordernotes');
        $this->db->join ( 'musers', 'tordernotes.CreatedByUserUID = musers.UserUID' , 'left' );
        $this->db->join ( 'msystemnotes', 'tordernotes.SystemNotesUID = msystemnotes.SystemNotesUID' , 'left' );
        $this->db->join ( 'mreportsections', 'tordernotes.SectionUID = mreportsections.SectionUID' , 'left' );
        $this->db->where (array('tordernotes.OrderUID'=>$OrderUID));
        $this->db->order_by('tordernotes.CreatedOn');
        $query = $this->db->get();
        return $query->result();
    }

    elseif ($UserUID == $CreatedByUserUID && $UserRoleType == 1) 
    {

       $this->db->select('tordernotes.NoteUID,Note,tordernotes.OrderUID,tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,musers.UserName,musers.UserUID,mreportsections.SectionName,mreportsections.SectionColor,mreportsections.ForeColor,tordernotes.AttachedFile,tordernotes.RoleType,msystemnotes.SystemNotes');
        $this->db->from('tordernotes');
        $this->db->join ( 'musers', 'tordernotes.CreatedByUserUID = musers.UserUID' , 'left' );
        $this->db->join ( 'msystemnotes', 'tordernotes.SystemNotesUID = msystemnotes.SystemNotesUID' , 'left' );
        $this->db->join ( 'mreportsections', 'tordernotes.SectionUID = mreportsections.SectionUID' , 'left' );
        $this->db->where (array('tordernotes.OrderUID'=>$OrderUID));
        $this->db->order_by('tordernotes.CreatedOn');
        $query = $this->db->get();
        return $query->result();
    }

    elseif ($UserUID == $CreatedByUserUID && $UserRoleType == 7) 
    {

      $query = $this->db->query("SELECT tordernotes.NoteUID,tordernotes.Note,tordernotes.OrderUID,
                                tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,
                                musers.UserName,musers.UserUID,mreportsections.SectionName,mreportsections.ForeColor,mreportsections.SectionColor,
                                tordernotes.AttachedFile,tordernotes.RoleType
                                FROM tordernotes 
                                LEFT JOIN musers ON musers.UserUID = tordernotes.CreatedByUserUID
                                LEFT JOIN `mreportsections` ON mreportsections.SectionUID = tordernotes.SectionUID
                                WHERE tordernotes.OrderUID = '$OrderUID'  AND ( tordernotes.RoleType IN (1,6,7,8) OR tordernotes.CreatedByUserUID = '$UserUID' )
                                ORDER BY tordernotes.CreatedOn;

                                ");

        return $query->result();
    }

    elseif ($UserUID == $CreatedByUserUID) 
    {

      $query = $this->db->query("SELECT tordernotes.NoteUID,tordernotes.Note,tordernotes.OrderUID,
                                tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,
                                musers.UserName,musers.UserUID,mreportsections.SectionName,mreportsections.SectionColor,mreportsections.ForeColor,
                                tordernotes.AttachedFile,tordernotes.RoleType
                                FROM tordernotes 
                                LEFT JOIN musers ON musers.UserUID = tordernotes.CreatedByUserUID
                                LEFT JOIN `mreportsections` ON mreportsections.SectionUID = tordernotes.SectionUID
                                WHERE tordernotes.CreatedByUserUID = '$UserUID' AND tordernotes.OrderUID = '$OrderUID' AND tordernotes.RoleType IN (1,6,7,8)
                                ORDER BY tordernotes.CreatedOn;

                                ");

        return $query->result();
    }

    elseif ($UserRoleType == 7) 
    {

      $query = $this->db->query("SELECT tordernotes.NoteUID,tordernotes.Note,tordernotes.OrderUID,
                                tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,
                                musers.UserName,musers.UserUID,mreportsections.SectionName,mreportsections.SectionColor,mreportsections.ForeColor,
                                tordernotes.AttachedFile,tordernotes.RoleType
                                FROM tordernotes 
                                LEFT JOIN musers ON musers.UserUID = tordernotes.CreatedByUserUID
                                LEFT JOIN `mreportsections` ON mreportsections.SectionUID = tordernotes.SectionUID
                                WHERE tordernotes.RoleType LIKE '%$UserRoleType%' AND tordernotes.OrderUID = '$OrderUID' 
                                ORDER BY tordernotes.CreatedOn;

                                ");

        return $query->result();
    }

    elseif ($UserRoleType == 1) {

        $this->db->select('tordernotes.NoteUID,Note,tordernotes.OrderUID,tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,musers.UserName,musers.UserUID,mreportsections.SectionName,mreportsections.SectionColor,mreportsections.ForeColor,tordernotes.AttachedFile,tordernotes.RoleType,msystemnotes.SystemNotes');
        $this->db->from('tordernotes');
        $this->db->join ( 'musers', 'tordernotes.CreatedByUserUID = musers.UserUID' , 'left' );
        $this->db->join ( 'msystemnotes', 'tordernotes.SystemNotesUID = msystemnotes.SystemNotesUID' , 'left' );
        $this->db->join ( 'mreportsections', 'tordernotes.SectionUID = mreportsections.SectionUID' , 'left' );
        $this->db->where (array('tordernotes.OrderUID'=>$OrderUID));
        $this->db->order_by('tordernotes.CreatedOn');
        $query = $this->db->get();
        return $query->result();
    }

    elseif ($UserRoleType == 6) {

        $this->db->select('tordernotes.NoteUID,Note,tordernotes.OrderUID,tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,musers.UserName,musers.UserUID,mreportsections.SectionName,mreportsections.SectionColor,mreportsections.ForeColor,tordernotes.AttachedFile,tordernotes.RoleType,msystemnotes.SystemNotes');
        $this->db->from('tordernotes');
        $this->db->join ( 'musers', 'tordernotes.CreatedByUserUID = musers.UserUID' , 'left' );
        $this->db->join ( 'msystemnotes', 'tordernotes.SystemNotesUID = msystemnotes.SystemNotesUID' , 'left' );
        $this->db->join ( 'mreportsections', 'tordernotes.SectionUID = mreportsections.SectionUID' , 'left' );
        $this->db->where (array('tordernotes.OrderUID'=>$OrderUID));
        $this->db->order_by('tordernotes.CreatedOn');
        $query = $this->db->get();
        return $query->result();
    }

    elseif ($UserRoleType == 8) {

      $query = $this->db->query("SELECT tordernotes.NoteUID,tordernotes.Note,tordernotes.OrderUID,
                                tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,
                                musers.UserName,musers.UserUID,mreportsections.SectionName,mreportsections.SectionColor,mreportsections.ForeColor,
                                tordernotes.AttachedFile,tordernotes.RoleType
                                FROM tordernotes 
                                LEFT JOIN musers ON musers.UserUID = tordernotes.CreatedByUserUID
                                LEFT JOIN `mreportsections` ON mreportsections.SectionUID = tordernotes.SectionUID
                                WHERE tordernotes.RoleType LIKE '%$UserRoleType%' AND tordernotes.OrderUID = '$OrderUID' 
                                ORDER BY tordernotes.CreatedOn;");

        return $query->result();
    }

  }
*/

  function get_notes($OrderUID,$loggedid,$filter)
 {
   $UserUID = $loggedid;
   $CreatedByUserUID = null;
   $UserRole = $this->GetUserRoleTypeDetails($UserUID);
   $UserRoleType = $UserRole->RoleType;

   $CheckUserUID = $this->CheckUserUID($OrderUID);  
   $CheckNotes = $CheckUserUID->CheckNotes;

   if($CheckNotes == 1 && $UserRoleType !=8){
     $CreatedUserUID = $this->GetChatDetailsByuserUID($OrderUID);
     $CreatedByUserUID = $CreatedUserUID->CreatedByUserUID;
   }
   $where = 'AND';

   if($CheckNotes == 1){

     if($UserRoleType !=8)
     {
       $this->db->select('tordernotes.NoteUID,Note,tordernotes.OrderUID,torders.OrderNumber,tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,musers.UserName,musers.UserUID,mreportsections.SectionName,mreportsections.SectionColor,mreportsections.ForeColor,tordernotes.AttachedFile,tordernotes.RoleType,msystemnotes.SystemNotes,mApiTitlePlatform.OrderSourceName');
       $this->db->from('tordernotes');
       $this->db->join ( 'musers', 'tordernotes.CreatedByUserUID = musers.UserUID' , 'left' );
       $this->db->join ( 'torders', 'torders.OrderUID = tordernotes.OrderUID' , 'left' );
       $this->db->join ( 'msystemnotes', 'tordernotes.SystemNotesUID = msystemnotes.SystemNotesUID' , 'left' );
       $this->db->join ( 'mreportsections', 'tordernotes.SectionUID = mreportsections.SectionUID' , 'left' );
       $this->db->join ( 'mApiTitlePlatform', 'tordernotes.CreatedByAPI = mApiTitlePlatform.OrderSourceUID' , 'left' );

       // $this->db->where (array('tordernotes.OrderUID'=>$OrderUID));
       if($UserRoleType == 15)
       {
        
            $Abstractor = $this->db->query("SELECT AbstractorUID FROM musers WHERE UserUID = '".$UserUID."' ")->row();
        $AbstractorUID = $Abstractor->AbstractorUID;
          $this->db->where (array('tordernotes.OrderUID'=>$OrderUID,'tordernotes.AbstractorUID'=>$AbstractorUID));
       }
       else
       {
          $this->db->where (array('tordernotes.OrderUID'=>$OrderUID));
       }
       if($filter != 'All')
       {
        $this->db->where('tordernotes.SectionUID',$filter);
       }
       $this->db->where('(tordernotes.IsPrivate = 0 OR tordernotes.IsPrivate = '.$this->loggedid.')');
       $this->db->order_by('tordernotes.CreatedOn');
       $query = $this->db->get();
       return $query->result();
     }
     else
     {
      if($filter != 'All')
      {
        $where.= ' tordernotes.SectionUID = '.$filter;
      }
       $query = $this->db->query("SELECT tordernotes.NoteUID,Note,tordernotes.OrderUID,tordernotes.SectionUID,
                                 tordernotes.CreatedByUserUID,tordernotes.CreatedOn,musers.UserName,musers.UserUID,
                                 mreportsections.SectionName,mreportsections.SectionColor,mreportsections.ForeColor,
                                 tordernotes.AttachedFile,tordernotes.RoleType,msystemnotes.SystemNotes,mApiTitlePlatform.OrderSourceName
                                 FROM tordernotes
                                 LEFT JOIN musers ON musers.UserUID = tordernotes.CreatedByUserUID
                                 LEFT JOIN `mreportsections` ON mreportsections.SectionUID = tordernotes.SectionUID
                                 LEFT JOIN `msystemnotes` ON msystemnotes.SystemNotesUID = tordernotes.SystemNotesUID
                                 LEFT JOIN `mApiTitlePlatform` ON mApiTitlePlatform.OrderSourceUID = tordernotes.CreatedByAPI
                                 WHERE (tordernotes.IsPrivate = 0 OR tordernotes.IsPrivate = ".$this->loggedid.") ".$where." tordernotes.RoleType LIKE '%8%' AND tordernotes.OrderUID = '$OrderUID'
                                 ORDER BY tordernotes.CreatedOn;");
       return $query->result();
     }
   }
 }

 function getBulkShippingNotes($OrderUID)
 {
      $sql="SELECT 
  tordernotes.NoteUID,  tordernotes.Note,  tordernotes.OrderUID, tordernotes.SectionUID, tordernotes.CreatedByUserUID, 
  tordernotes.CreatedOn, musers.UserName, musers.UserUID, mreportsections.SectionName, mreportsections.SectionColor, 
  mreportsections.ForeColor, tordernotes.AttachedFile, tordernotes.RoleType, msystemnotes.SystemNotes, 'UPS' AS OrderSourceName, 
  '' AS Filename
FROM (tordernotes)
LEFT JOIN musers ON tordernotes.CreatedByUserUID = musers.UserUID
LEFT JOIN msystemnotes ON tordernotes.SystemNotesUID = msystemnotes.SystemNotesUID
LEFT JOIN mreportsections ON tordernotes.SectionUID = mreportsections.SectionUID
WHERE tordernotes.CreatedByAPI IN (SELECT DISTINCT BinUID FROM tBinOrders WHERE  OrderUID= '$OrderUID') AND tordernotes.SectionUID =  '26' 
ORDER BY tordernotes.CreatedOn";

      return $this->db->query($sql)->result();
 }

  function GetRoleUID($Customers){

   if($Customers == 1){
     $query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(RoleType)) as RoleType FROM `mroles` ORDER BY `RoleUID` ASC");
     return $query->row();
   }
   else{
     $query = $this->db->query("SELECT GROUP_CONCAT(DISTINCT(RoleType)) as RoleType FROM `mroles` where RoleType<>8 ORDER BY `RoleUID` ASC");
     return $query->row();
   }
   
  }

  function save_note($OrderUID,$SectionUID,$message,$loggedid,$filename,$RoleTypeUID,$SectionColor,$AbstractorUID,$OrderIssueTypeUID='',$IsPrivate='')
  {


    $RoleType = null;

    $NoteType = $this->GetNoteTypeName($SectionUID);
    $NoteTypeName = $NoteType->SectionName;

    if ($NoteTypeName === 'API Note Action') {
      $Label = '<b>ACTION</b> - ';
      $note_message = $Label.$message;       
    } else {
      $note_message = $message;
    }


    /*$RoleType[] = $RoleTypeUID->Admin ? 1 : null;
    $RoleType[] = $RoleTypeUID->Supervisor ? 6 : null;
    $RoleType[] = $RoleTypeUID->Agents ? 7 : null;
    $RoleType[] = $RoleTypeUID->Customers ? 8 : null;

    $RoleType_Merge = array_filter($RoleType, 'strlen' );

    $RoleType_Merge = implode($RoleType_Merge, ',');*/

    $Customers = $RoleTypeUID->Customers ? 1 : 0;

    $RoleType_Merge = $this->GetRoleUID($Customers);
    $RoleType_Merge = $RoleType_Merge->RoleType;
    if(!empty($AbstractorUID))
    {
      $RoleType_Merge = 15;
    }


    $this->OrderUID = $OrderUID;
    $this->SectionUID = $SectionUID;
    //$this->SectionColor = "#".$SectionColor;
    $this->Note = $note_message;
    $this->AttachedFile = $filename;
    $this->RoleType = $RoleType_Merge;
    $this->CreatedByUserUID = $loggedid;
    $this->AbstractorUID = $AbstractorUID;
    $this->CreatedOn = Date('Y-m-d H:i:s',strtotime("now"));
    if(!empty($OrderIssueTypeUID)) {
     $this->OrderIssueTypeUID = $OrderIssueTypeUID;
    }
    
    if(!empty($IsPrivate)) {
     $this->IsPrivate = $IsPrivate;
    }  

    $query = $this->db->insert('tordernotes',$this);
    $insert_id = $this->db->insert_id();
    $data = $this->get_notebyid($insert_id);
    $UserUID =$this->session->userdata('UserUID');
    
     $fieldArray = array(
     "OrderUID"=>$OrderUID,
     "ExceptionUID"=>'1',
     "Remarks"=>$message,
     "RaisedByUserUID"=>$UserUID,
     "RaisedOn"=> Date('Y-m-d H:i:s',strtotime("now"))
   );


   $torders = $this->db->get_where('torders', array('OrderUID'=>$OrderUID))->row();

    if($torders->StatusUID == '49'){
      $status = array("StatusUID"=>'49');
    }else{
      $status = array(
        "StatusUID"=>'49',
        "IsExceptionOldStatusUID"=>$torders->StatusUID,
      );
    }
     
     if($SectionUID=='24') {
      $this->db->insert('texceptions', $fieldArray);
      $this->db->where(array("torders.OrderUID"=>$OrderUID));
      $this->db->update('torders',$status);
    
       /*  @desc Send Resware Trigger Mail On Exception @Uba @On 30 Apr 2020
      *  @Param OrderUID, Action Name, Remarks, Attachments  */
      if(in_array($this->session->userdata('RoleType'),array(8))){
        if(!empty($filename)){
          $GetFileName = explode('/',$filename);
          $ReswareMailSent = $this->common_model->Resware_client_mail($OrderUID,' Client Exception Raised ','NoteType: '.$NoteTypeName.', Note: '.$note_message,array('path'=>$filename,'filename'=>end($GetFileName))); 
        }else{
          $ReswareMailSent = $this->common_model->Resware_client_mail($OrderUID,' Client Exception Raised ','NoteType: '.$NoteTypeName.', Note: '.$note_message,NULL); 
        }
      }
    

      /* @author Parthasarathy <parthasarathy.m@avanzegroup.com> purpose insert notifications for all customer users */

      $CustomerUsers = $this->common_model->get('musers', ['CustomerUID'=>$torders->CustomerUID, 'Active'=>1]);
      foreach ($CustomerUsers as $key => $user) 
      {
        $Message = '<span class="user-name"> #'.$torders->OrderNumber.'</span> Request Raised';
        $this->common_model->create_common_notification($user->UserUID,$Message,'order_summary?OrderUID='.$torders->OrderUID);
      }

      $data1['ModuleName']='Exception Raised'.'-insert';
      $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
      $data1['DateTime']=date('y-m-d H:i:s');
      $data1['TableName']='texceptions';
      $data1['OrderUID']=$OrderUID;
      $data1['UserUID']=$this->session->userdata('UserUID'); 
      $data1['OldValue']=''; 
      $data1['FieldUID']='275';
      $data1['NewValue']='Exception  Raised'; 
      $this->common_model->Audittrail_insert($data1);  
    }
    
    $data1['ModuleName']='Notes_add';
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['OrderUID']=$OrderUID; 
    $data1['DateTime']=date('y-m-d H:i:s');
    $data1['TableName']='tordernotes';
    $data1['UserUID']=$this->session->userdata('UserUID');                
    $this->common_model->Audittrail_insert($data1);
    return $data;    
  }

  function get_notebyid($NoteUID)
  {
    $this->db->select('tordernotes.NoteUID,Note,tordernotes.OrderUID,tordernotes.SectionUID,tordernotes.CreatedByUserUID,tordernotes.CreatedOn,musers.UserName,mreportsections.SectionName,mreportsections.SectionColor,mreportsections.ForeColor,tordernotes.AttachedFile,tordernotes.RoleType');
    $this->db->from('tordernotes');
    $this->db->join ( 'musers', 'tordernotes.CreatedByUserUID = musers.UserUID' , 'left' );
    $this->db->join ( 'mreportsections', 'tordernotes.SectionUID = mreportsections.SectionUID' , 'left' );
    $this->db->where('tordernotes.NoteUID',$NoteUID);
    $query = $this->db->get();
    return $query->row();
  }

  function DeleteNotes($NoteUID,$OrderUID)
  {
    $Agent = array('7');
    $Customer = array('8');
    $Supervisor = array('6');
    $Admin = array('1','2','3','4','5');

    $UserUID = $this->session->userdata('UserUID');
    $RoleType = $this->session->userdata('RoleType');

    $RaisedDatetime = Date("Y-m-d H:i:s");

    if (in_array($RoleType, $Admin))
    { 
        $query = $this->db->query("DELETE FROM tordernotes WHERE NoteUID ='$NoteUID'");
        return $query;
    }
    elseif(in_array($RoleType, $Supervisor)){
        $query = $this->db->query("DELETE FROM tordernotes WHERE NoteUID ='$NoteUID'");
        return $query;
    } 
    elseif(in_array($RoleType, $Agent)){
        $ApprovalFunction = 'Notes';
        $ApprovalStatus = '0';
        $query = $this->db->query('INSERT INTO  torderapprovals(OrderUID,ApprovalFunction,RaisedByUserUID,RaisedDatetime,ApprovalStatus)VALUES
     ("'.$OrderUID.'","'.$ApprovalFunction.'","'.$UserUID.'","'.$RaisedDatetime.'","'.$ApprovalStatus.'")');
        return $query;
    }

    elseif(in_array($RoleType, $Customer)){
        $ApprovalFunction = 'Notes';
        $ApprovalStatus = '0';
        $query = $this->db->query('INSERT INTO  torderapprovals(OrderUID,ApprovalFunction,RaisedByUserUID,RaisedDatetime,ApprovalStatus)VALUES
     ("'.$OrderUID.'","'.$ApprovalFunction.'","'.$UserUID.'","'.$RaisedDatetime.'","'.$ApprovalStatus.'")');
        return $query;
    }

  }

  function DeleteNotesByUID($NoteUID,$OrderUID){
    
    $query = $this->db->query("DELETE FROM tordernotes WHERE NoteUID ='$NoteUID'");
                  $data1['ModuleName']='Notes_delete';
                  $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
                  $data1['DateTime']=date('y-m-d H:i:s');
                  $data1['TableName']='tordernotes';
                  $data1['OrderUID']=$OrderUID;
                  $data1['UserUID']=$this->session->userdata('UserUID');                
                  $this->common_model->Audittrail_insert($data1);
    return $query;

  }

  function InsertApprovalsNotesByUID($NoteUID,$OrderUID){

    $ApprovalFunction = 'Notes';
    $ApprovalStatus = '0';
    $UserUID = $this->session->userdata('UserUID');
    $RoleType = $this->session->userdata('RoleType');
    $RaisedDatetime = Date("Y-m-d H:i:s");

    $query = $this->db->query('INSERT INTO  torderapprovals(OrderUID,NoteUID,ApprovalFunction,RaisedByUserUID,RaisedDatetime,ApprovalStatus)VALUES
     ("'.$OrderUID.'","'.$NoteUID.'","'.$ApprovalFunction.'","'.$UserUID.'","'.$RaisedDatetime.'","'.$ApprovalStatus.'")');
    
    return $query;

  }

  function GetMessageType($NoteUID){
    
    $query = $this->db->query("SELECT EXISTS(SELECT * FROM torderapprovals WHERE NoteUID = '$NoteUID' AND ApprovalStatus = 0) as CheckMessage;
            ");
    return $query->row();

  }

  function GetAssigned($OrderUID)
 {
      $query  = $this->db->query("SELECT *  FROM `torders` WHERE  OrderUID = '$OrderUID'");
      $result =$query->row();
      $this->db->select('*');
      $this->db->from('mcustomerworkflowmodules');
      $this->db->where('SubProductUID',$result->SubProductUID);
      $this->db->where('CustomerUID',$result->CustomerUID);
      return $this->db->get()->num_rows();
 }
 function GetCompletedworkflow($OrderUID){
 
  $query = $this->db->query("SELECT COUNT(*) as  CompletedWorkflow FROM `torderassignment` WHERE  OrderUID = '$OrderUID'  AND WorkflowModuleUID IN (1,2,3,4) AND WorkflowStatus IN(5) ");
    return $query->row();
}

function ChangeOrderStatus($OrderUID,$Orderstatus){
  $this->db->where('OrderUID',$OrderUID);
  $this->db->update('torders',$Orderstatus);

  $torders = $this->db->get_where('torders', array('OrderUID'=>$OrderUID))->row();

     $status = array(
       "Isclear"=>'1',
    
      );
  
  $this->db->Where('OrderUID',$OrderUID);
  $this->db->Where('Isclear',0);
  $this->db->update('texceptions',$status);




  /* @author Parthasarathy <parthasarathy.m@avanzegroup.com> purpose insert notifications for all customer users */

  $CustomerUsers = $this->common_model->get('musers', ['CustomerUID'=>$torders->CustomerUID, 'Active'=>1]);
  foreach ($CustomerUsers as $key => $user) 
  {
    $Message = '<span class="user-name"> #'.$torders->OrderNumber.'</span> Request Cleared';
    $this->common_model->create_common_notification($user->UserUID,$Message,'order_summary?OrderUID='.$torders->OrderUID);
  }

  $data1['ModuleName']='Exception Cleared'.'-insert';
  $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
  $data1['DateTime']=date('y-m-d H:i:s');
  $data1['TableName']='texceptions';
  $data1['OrderUID']=$OrderUID;
  $data1['UserUID']=$this->session->userdata('UserUID'); 
  $data1['OldValue']=''; 
  $data1['FieldUID']='275';
  $data1['NewValue']='Exception  Cleared'; 
  $this->common_model->Audittrail_insert($data1);
}

function GetCompleteddate($OrderUID){
  $this->db->select('DATE_FORMAT(torders.OrderCompleteDateTime, "%Y-%m-%d %H:%i:%s") as OrderCompleteDateTime', FALSE); 
  $this->db->from('torders');
  $this->db->where('OrderUID',$OrderUID);
 return $this->db->get()->row();
}

function SearchCheckWorkflowStatus($OrderUID){

  $this->db->select('*');
  $this->db->from('torderassignment');
  $this->db->where('OrderUID',$OrderUID);
  $this->db->where('WorkflowModuleUID',1);
  return $this->db->get()->row();
}

function TypingCheckWorkflowStatus($OrderUID){

  $this->db->select('*');
  $this->db->from('torderassignment');
  $this->db->where('OrderUID',$OrderUID);
  $this->db->where('WorkflowModuleUID',2);
  return $this->db->get()->row();
}

function TaxingCheckWorkflowStatus($OrderUID){

  $this->db->select('*');
  $this->db->from('torderassignment');
  $this->db->where('OrderUID',$OrderUID);
  $this->db->where('WorkflowModuleUID',3);
  return $this->db->get()->row();
}
function SearchPermissions(){
  $this->db->select('CanIndependentWorkflowModule');
  $this->db->from('mworkflowmodules');
  $this->db->where('WorkflowModuleUID',1);
  return $this->db->get()->row();
}
function TypingPermissions(){
  $this->db->select('CanIndependentWorkflowModule');
  $this->db->from('mworkflowmodules');
  $this->db->where('WorkflowModuleUID',2);
  return $this->db->get()->row();
}
function TaxingPermissions(){
  $this->db->select('CanIndependentWorkflowModule');
  $this->db->from('mworkflowmodules');
  $this->db->where('WorkflowModuleUID',3);
  return $this->db->get()->row();
}

function GetOrdersAbstractors($OrderUID)
{
  $this->db->select('AbstractorUID');
  $this->db->from('torderabstractor');
  $this->db->where('OrderUID',$OrderUID);
  return $this->db->get()->result_array();
}

function GetAbstractorsDetails($StringAppend)
{
  $this->db->select('*');
  $this->db->from('mabstractor');
  $this->db->where_in('AbstractorUID',$StringAppend);
  return $this->db->get()->result();
}


function get_torderfollowup($OrderUID){

  $query = $this->db->query("SELECT *,`b`.`UserName` AS CreatedByUserName, 
    FollowUpType,
    torderfollowup.FollowupDateTime as FollowupDateTime, torderfollowup.CreateOnDateTime as CreateOnDateTime,
    CASE FollowUpUserType 
    WHEN 'Customer' THEN (SELECT CustomerName FROM mcustomers WHERE CustomerUID = torderfollowup.UserUID)
    ELSE `a`.`UserName`
    END as UserName FROM (`torderfollowup`) LEFT JOIN `musers` a ON `a`.`UserUID` = `torderfollowup`.`UserUID` LEFT JOIN `musers` b ON `b`.`UserUID` = `torderfollowup`.`CreatedByUserUID` LEFT JOIN `torders` ON `torders`.`OrderUID` = '.$OrderUID.' WHERE `torderfollowup`.`OrderUID` = '".$OrderUID."' ORDER BY `CreateOnDateTime` DESC
    ");
  return $query->result();

   // $His['FollowupDateTime'] = date("m-d-Y h A",strtotime(str_replace('-','/',$His['FollowupDateTime'])));
}

function save_followups($data)
{
  if(isset($data['form_followupdate']) && !empty($data['form_followupdate']))
  {    
    $FollowupDateTime = DateTime::createFromFormat("m/d/Y" , $data['form_followupdate']);
    $FollowupDateTime = $FollowupDateTime->format('Y-m-d');
  } else {
    $FollowupTime = '00-00-0000';
  }
  $seconds =':00';
  $SigningTime = date("H:i", strtotime($data['FollowupTime']));
  $FollowupDateTime = date('Y-m-d H:i:s', strtotime($FollowupDateTime . ' ' .$SigningTime.$seconds));

  if(!empty($data['OrderIssueTypeUID'])){
    $fieldArray = array(
      "OrderUID"=>$data['OrderUID'],
      "OrderIssueTypeUID"=>$data['OrderIssueTypeUID'],
      "FollowUpType"=>$data['form_Followuptype'],
      "FollowUpUserType"=>$data['form_type'],
      "Comments"=>$data['form_comments'],
      "UserUID"=>$data['form_users'],
      "FollowUpStatus"=>'New',
      "FollowupDateTime"=> $FollowupDateTime,
      "CreatedByUserUID"=>$this->loggedid,
      "CreateOnDateTime"=> Date('Y-m-d H:i:s',strtotime('now')),
      "FollowUpPriority"=>$data['form_followup_priority']
    );
  }else{
    $fieldArray = array(
      "OrderUID"=>$data['OrderUID'],
      "FollowUpType"=>$data['form_Followuptype'],
      "FollowUpUserType"=>$data['form_type'],
      "Comments"=>$data['form_comments'],
      "UserUID"=>$data['form_users'],
      "FollowUpStatus"=>'New',
      "FollowupDateTime"=> $FollowupDateTime,
      "CreatedByUserUID"=>$this->loggedid,
      "CreateOnDateTime"=> Date('Y-m-d H:i:s',strtotime('now')),
      "FollowUpPriority"=>$data['form_followup_priority']
    );
  }
  $this->db->insert('torderfollowup', $fieldArray);

 

  /*Followup Audit Trail Begin*/
  $ContEnt = array(
    'FollowUpUID'=>$this->db->insert_id(), // Last Inserted FollowUP
    'body'=>'New Followup Created'
  );
  $InsetData = array('OrderUID' => $data['OrderUID'],
  'UserUID' => $this->loggedid,
  'ModuleName' => 'Followup',
  'Content' => json_encode($ContEnt),
  'DateTime' => date('Y-m-d H:i:s'));
$this->common_model->InsertAuditTrail($InsetData);

 $this->InsertNewFollowupType($data['form_Followuptype']);
/*Followup Audit Trail End*/
  return true;
}

function get_usersbytype($OrderUID,$type){

  if($type == "Customer"){

    $query  = $this->db->query("SELECT `torders`.`CustomerUID` as UserUID, `CustomerName` as UserName FROM torders JOIN `mcustomers` ON `mcustomers`.`CustomerUID` = `torders`.`CustomerUID` WHERE `OrderUID` = '".$OrderUID."' ");

    return $query->result();

  }elseif ($type == "Abstractor") {

    $query = $this->db->query("SELECT `mabstractor`.`UserUID` as UserUID,CONCAT(COALESCE(mabstractor.AbstractorFirstName,''), ' ', COALESCE(mabstractor.AbstractorLastName,'')) as UserName FROM torderabstractor JOIN `mabstractor` ON `mabstractor`.`AbstractorUID` = `torderabstractor`.`AbstractorUID` WHERE `torderabstractor`.`OrderUID` = '".$OrderUID."' GROUP BY `torderabstractor`.`AbstractorUID` ");

    return $query->result();

  }elseif ($type == "Vendor") {

    $query = $this->db->query("SELECT UserName,AssignedToUserUID As UserUID FROM torderassignment JOIN musers on musers.UserUID = torderassignment.AssignedToUserUID WHERE `OrderUID` = '".$OrderUID."' AND torderassignment.VendorUID != '0' AND SendToVendor = '1' ");
    return $query->result();
   
  }elseif ($type == "Organization") {

    $query = $this->db->query("SELECT UserName, UserUID FROM musers JOIN mroles on mroles.RoleUID = musers.RoleUID WHERE RoleType IN (1,2,3,4,5,6,7) AND musers.Active = 1 ");
    return $query->result();

  }else {

    $query = $this->db->query("SELECT UserName, UserUID FROM musers WHERE musers.UserUID = '".$this->loggedid."' ");
    return $query->result();

  }

}

function getfollowup_uid($FollowUpUID){
  $this->db->select('*');
  $this->db->select('DATE_FORMAT(torderfollowup.FollowupDateTime, "%m/%d/%Y %h:%i %p") as FollowupDateTime', FALSE);
  $this->db->select('DATE_FORMAT(torderfollowup.FollowupDateTime, "%m/%d/%Y") as EditFollowupDate', FALSE);
  $this->db->select('DATE_FORMAT(torderfollowup.FollowupDateTime, "%h:%i %p") as EditFollowupTime', FALSE);
  $this->db->from('torderfollowup');
  $this->db->where('FollowUpUID',$FollowUpUID);
  return $this->db->get()->row();
}

function updatefollowup_uid($data){
  $FollowupDateTime = NULL;
  if(!empty($data['form_followupdate'])){
    $FollowupDateTime = DateTime::createFromFormat("m/d/Y" , $data['form_followupdate']);
    $FollowupDateTime = $FollowupDateTime->format('Y-m-d');
  }
  $seconds =':00';
  if(!empty($data['FollowupTime'])){
    $SigningTime = date("H:i", strtotime($data['FollowupTime']));
    $FollowupDateTime = date('Y-m-d H:i:s', strtotime($FollowupDateTime . ' ' .$SigningTime.$seconds));
  }

  $datas = array(
    "FollowUpUserType"=>$data['form_type'],
    "FollowUpType"=>$data['form_Followuptype'],
    "UserUID"=>$data['form_users'],
    "FollowupDateTime"=> $FollowupDateTime,
    "Comments"=>$data['form_comments'],
    "FollowUpPriority"=>$data['form_followup_priority']
  );


  // $this->InsertNewFollowupType($data['form_Followuptype']);

  $UpdatedFields = '';
  /*Followup Audit Trail Begin*/

$this->db->select('FollowUpUserType,OrderUID');
$this->db->from('torderfollowup');
$this->db->where('FollowUpUID',$data['FollowUpUID']);
$Result1 = $this->db->get()->row();
if($Result1->FollowUpUserType != $data['form_type']){ // USER TYPE
  $UpdatedFields .= ' FollowUpUserType is changed from <b>'. $Result1->FollowUpUserType. '</b> To <b>' .$data['form_type'].'</b>';
}


$this->db->select('UserUID');
$this->db->from('torderfollowup');
$this->db->where('FollowUpUID',$data['FollowUpUID']);
$Result2 = $this->db->get()->row();
if($Result2->UserUID != $data['form_users']){ // USER
  if($Result2->FollowUpUserType == 'Customer'){
    $u1 = $this->common_model->GetCustomerDetailsBasedOnID((int)$Result2->UserUID)[0]->CustomerName;
  }else{
    $u1 = $this->common_model->GetUserDetailsByUser((int)$Result2->UserUID)->UserName;
  }
  if($data['form_type'] == 'Customer'){
    $u2 = $this->common_model->GetCustomerDetailsBasedOnID($data['form_users'])[0]->CustomerName;
  }else{
    $u2 = $this->common_model->GetUserDetailsByUser($data['form_users'])->UserName;
  }
  $UpdatedFields .= ' UserName is changed from <b>'.$u1. '</b> To <b>' .$u2.'</b>';
}
if($Result1->FollowUpUserType != $data['form_type']){ // IF PREVIOUS USER IS CUSTOMER 
  if($Result1->FollowUpUserType == 'Customer'){
    $u1 = $this->common_model->GetCustomerDetailsBasedOnID((int)$Result2->UserUID)[0]->CustomerName;
    $u2 = $this->common_model->GetUserDetailsByUser($data['form_users'])->UserName;
    $UpdatedFields .= ' UserName is changed from <b>'.$u1. '</b> To <b>' .$u2.'</b>';
  }
}

$this->db->select('FollowupDateTime');
$this->db->from('torderfollowup');
$this->db->where('FollowUpUID',$data['FollowUpUID']);
$Result3 = $this->db->get()->row(); // DATETIME
if(d2t_format_date(str_replace('-','/',$Result3->FollowupDateTime)) != d2t_format_date(str_replace('-','/',$FollowupDateTime))) {
  $UpdatedFields .= ' FollowupDateTime is changed from <b>'. d2t_format_date(str_replace('-','/',$Result3->FollowupDateTime)). '</b> To <b>' .d2t_format_date(str_replace('-','/',$FollowupDateTime)).'</b>';
}

$this->db->select('Comments');
$this->db->from('torderfollowup');
$this->db->where('FollowUpUID',$data['FollowUpUID']);
$Result4 = $this->db->get()->row();

if($Result4->Comments != $data['form_comments']){ // COMMENTS
  $UpdatedFields .= ' Comments is changed from <b>'. $Result4->Comments. '</b> To <b>' .$data['form_comments'].'</b>';
}

$this->db->select('FollowUpPriority');
$this->db->from('torderfollowup');
$this->db->where('FollowUpUID',$data['FollowUpUID']);
$Result5 = $this->db->get()->row();
if($Result5->FollowUpPriority != $data['form_followup_priority']){ // PRIORITY
  $UpdatedFields .= ' FollowUpPriority is changed from <b>'. $Result5->FollowUpPriority. '</b> To <b>' .$data['form_followup_priority'].'</b>';
}

$this->db->select('FollowUpType');
$this->db->from('torderfollowup');
$this->db->where('FollowUpUID',$data['FollowUpUID']);
$Result6 = $this->db->get()->row();
if($Result6->FollowUpType != $data['form_Followuptype']){ // FOLLOWUP TYPE
  $UpdatedFields .= ' FollowUpType is changed from <b>'. $Result6->FollowUpType. '</b> To <b>' .$data['form_Followuptype'].'</b>';
}


$this->db->where("FollowUpUID",$data['FollowUpUID'])->update('torderfollowup',$datas);

$ContEnt = array(
  'FollowUpUID'=>$data['FollowUpUID'],
  'body'=>$UpdatedFields
);
if($UpdatedFields != ''){ // AUDIT INSERT
$InsetData = array('OrderUID' => $Result1->OrderUID,
  'UserUID' => $this->loggedid,
  'ModuleName' => 'Followup',
  'Content' => json_encode($ContEnt),
  'DateTime' => date('Y-m-d H:i:s'));
$this->common_model->InsertAuditTrail($InsetData);
}

/*Followup Audit Trail End*/

  if ($this->db->trans_status() === FALSE)
  {
    $this->db->trans_rollback();
    return false;
  }else{
    $this->db->trans_commit();
    return true;
  }
}

function getOrderAuditHistorys($OrderUID){
  $this->db->select('*');
  $this->db->from('taudittrail');
  $this->db->where('OrderUID',$OrderUID);
  $this->db->where('ModuleName','Followup');
  $this->db->order_by('DateTime','DESC');
  $query = $this->db->get();
  return $query->result();

}

function checkWithPrevious($orderID,$Key,$Val){
  $this->db->select($Key);
  $this->db->from('torderfollowup');
  $this->db->where('OrderUID',$orderID);
  $this->db->where($Key,$Val);
  $Result = $this->db->get()->row();
  if(!empty($Result)){
    return $Result->$Key;
  }else{
    return false;
  }
}

function cancel_followup($FollowUpUID,$Remarks){


  $datas = array(
    "FollowUpStatus"=>'Cancelled',
    "Comments"=>$Remarks,
    "CancelledByUserUID"=>$this->loggedid,
    "CancelledOnDateTime"=> Date('Y-m-d H:i:s',strtotime('now')),
  );

  $this->db->trans_begin();

  $this->db->where('FollowUpUID',$FollowUpUID);
  $this->db->update('torderfollowup',$datas);

  $this->db->select('*');
  $this->db->from('torderfollowup');
  $this->db->where('FollowUpUID',$FollowUpUID);
  $result = $this->db->get('')->row();

  /*Followup Audit Trail Start*/
  $STRR = '';
  if(!empty($Remarks)){
    $STRR = 'Followup Cancelled with Comments: <b>'.$Remarks.'</b>';
  }else{
    $STRR = 'Followup Cancelled';
  }
  $ContEnt = array(
    'FollowUpUID'=>$FollowUpUID,  
    'body'=> $STRR 
  );
  
    $InsetData = array('OrderUID' => $result->OrderUID,
    'UserUID' => $this->loggedid,
    'ModuleName' => 'Followup',
    'Content' => json_encode($ContEnt),
    'DateTime' => date('Y-m-d H:i:s'));
  $this->common_model->InsertAuditTrail($InsetData);
  /*Followup Audit Trail End*/
  

  if ($this->db->trans_status() === FALSE)
  {
    $this->db->trans_rollback();
    return false;
  }else{
    $this->db->trans_commit();
    return true;
  }
}

function complete_followup($FollowUpUID,$Remarks){


  $datas = array(
    "FollowUpStatus"=>'Completed',
    "CompletedByUserUID"=>$this->loggedid,
    "Comments"=>$Remarks,
    "FollowUpEndTime"=>Date('Y-m-d H:i:s',strtotime('now')),
    "IsRead"=>0,

  );

  $this->db->trans_begin();

  $this->db->where('FollowUpUID',$FollowUpUID);
  $this->db->update('torderfollowup',$datas);

  $this->db->select('*');
  $this->db->from('torderfollowup');
  $this->db->where('FollowUpUID',$FollowUpUID);
  $result = $this->db->get('')->row();

  $STRR = 'Followup Completed';
  if($Remarks != ""){
    $STRR .= ' with Comments: <b>'.$Remarks.'</b>';
  }
    /*Followup Audit Trail Start*/
    $ContEnt = array(
      'FollowUpUID'=>$FollowUpUID,
      'body'=>$STRR
    );
    
      $InsetData = array('OrderUID' => $result->OrderUID,
      'UserUID' => $this->loggedid,
      'ModuleName' => 'Followup',
      'Content' => json_encode($ContEnt),
      'DateTime' => date('Y-m-d H:i:s'));
    $this->common_model->InsertAuditTrail($InsetData);
    /*Followup Audit Trail End*/

  if ($this->db->trans_status() === FALSE)
  {
    $this->db->trans_rollback();
    return false;
  }else{
    
    $this->db->trans_commit();
    return true;
  }
}

function start_followup($FollowUpUID){


  $datas = array(
    "FollowUpStatus"=>'Started',
    "StartedByUserUID"=>$this->loggedid,
    "FollowUpStartTime"=>Date('Y-m-d H:i:s',strtotime('now')),
    "IsRead"=>0,
  );

  $this->db->trans_begin();

  $this->db->where('FollowUpUID',$FollowUpUID);
  $this->db->update('torderfollowup',$datas);

  $this->db->select('*');
  $this->db->from('torderfollowup');
  $this->db->where('FollowUpUID',$FollowUpUID);
  $result = $this->db->get('')->row();
  $data1['DateTime'] = date('y-m-d H:i:s');
  $data1['TableName'] = 'torderfollowup';
  $data1['OrderUID'] = $result->OrderUID;
  $data1['UserUID'] = $this->session->userdata('UserUID');               
  $this->common_model->Audittrail_insert($data1);

    /*Followup Audit Trail Start*/
    $ContEnt = array(
      'FollowUpUID'=>$FollowUpUID,
      'body'=>'Followup Started'
    );
    
      $InsetData = array('OrderUID' => $result->OrderUID,
      'UserUID' => $this->loggedid,
      'ModuleName' => 'Followup',
      'Content' => json_encode($ContEnt),
      'DateTime' => date('Y-m-d H:i:s'));
    $this->common_model->InsertAuditTrail($InsetData);
    /*Followup Audit Trail End*/




  if ($this->db->trans_status() === FALSE)
  {
    $this->db->trans_rollback();
    return false;
  }else{

    $this->db->trans_commit();
    return true;
  }

}


function view_followup($FollowUpUID,$OrderUID){
  $query1 = $this->db->query("SELECT IsRead,OrderNumber,FollowUpType,OrderIssueTypeUID,torderfollowup.UserUID,FollowUpUserType,FollowUpPriority,torderfollowup.OrderUID,FollowUpStatus,Comments,Remarks,`b`.`UserName` AS CreatedByUserName,`c`.`UserName` AS CompletedByUserName,`d`.`UserName` AS StartedByUserName,`e`.`UserName` AS CancelledByUserName, DATE_FORMAT(torderfollowup.FollowupDateTime, '%m/%d/%Y %h:%i %p') as FollowupDateTime, DATE_FORMAT(torderfollowup.CreateOnDateTime, '%m/%d/%Y %H:%i %p') as CreateOnDateTime, DATE_FORMAT(torderfollowup.FollowUpEndTime, '%m/%d/%Y %H:%i %p') as FollowUpEndTime, DATE_FORMAT(torderfollowup.FollowUpStartTime, '%m/%d/%Y %H:%i %p') as FollowUpStartTime, DATE_FORMAT(torderfollowup.CancelledOnDateTime, '%m/%d/%Y %H:%i %p') as CancelledOnDateTime,
    CASE FollowUpType 
    WHEN 'Customer' THEN (SELECT CustomerName FROM mcustomers WHERE CustomerUID = torderfollowup.UserUID)
    ELSE `a`.`UserName`
    END as UserName FROM (`torderfollowup`) JOIN torders ON torders.OrderUID = torderfollowup.OrderUID LEFT JOIN `musers` a ON `a`.`UserUID` = `torderfollowup`.`UserUID` LEFT JOIN `musers` b ON `b`.`UserUID` = `torderfollowup`.`CreatedByUserUID` LEFT JOIN `musers` c ON `c`.`UserUID` = `torderfollowup`.`CompletedByUserUID` LEFT JOIN `musers` d ON `d`.`UserUID` = `torderfollowup`.`StartedByUserUID`  LEFT JOIN `musers` e ON `e`.`UserUID` = `torderfollowup`.`CancelledByUserUID` WHERE `FollowUpUID` = '".$FollowUpUID."' ORDER BY `CreateOnDateTime` DESC
    ");

    $Res1 = $query1->row();

    $query2 = $this->db->query("SELECT * FROM taudittrail WHERE `OrderUID` = ".$Res1->OrderUID." AND `Content`IS NOT NULL ORDER BY `DateTime` DESC");
$allHis = $query2->result_array();
$NArr = [];
foreach ($allHis as $His) {
  $user  = $this->common_model->GetUserDetailsByUser($His['UserUID']);
  $His['UserName'] = $user->UserName;

    $dt = json_decode($His['Content']);
   if((int)$dt->FollowUpUID == (int)$FollowUpUID){
    array_push($NArr,$His);
  }
}
  $Res = array('data' => $query1->row(),
               'fhistory' => $NArr);
  return $Res;
}

function view_allfollowups($FollowUpUID){
  $query1 = $this->db->query("SELECT IsRead,OrderNumber,FollowUpType,OrderIssueTypeUID,tOrderIssues.OrderIssueUID,FollowUpUserType,torderfollowup.UserUID,FollowUpPriority,torderfollowup.OrderUID,FollowUpStatus,Comments,Remarks,`b`.`UserName` AS CreatedByUserName,`c`.`UserName` AS CompletedByUserName,`d`.`UserName` AS StartedByUserName,`e`.`UserName` AS CancelledByUserName, DATE_FORMAT(torderfollowup.FollowupDateTime, '%m/%d/%Y %h:%i %p') as FollowupDateTime, DATE_FORMAT(torderfollowup.FollowupDateTime, '%m/%d/%Y') as FollowupDate,DATE_FORMAT(torderfollowup.FollowupDateTime, '%h:%i %p') as FollowupTime, DATE_FORMAT(torderfollowup.CreateOnDateTime, '%m/%d/%Y %H:%i %p') as CreateOnDateTime, DATE_FORMAT(torderfollowup.FollowUpEndTime, '%m/%d/%Y %H:%i %p') as FollowUpEndTime, DATE_FORMAT(torderfollowup.FollowUpStartTime, '%m/%d/%Y %H:%i %p') as FollowUpStartTime, DATE_FORMAT(torderfollowup.CancelledOnDateTime, '%m/%d/%Y %H:%i %p') as CancelledOnDateTime,
    CASE FollowUpType 
    WHEN 'Customer' THEN (SELECT CustomerName FROM mcustomers WHERE CustomerUID = torderfollowup.UserUID)
    ELSE `a`.`UserName`
    END as UserName FROM (`torderfollowup`) JOIN torders ON torders.OrderUID = torderfollowup.OrderUID LEFT JOIN tOrderIssues ON FIND_IN_SET(torderfollowup.OrderIssueTypeUID, tOrderIssues.OrderIssueTypeUIDs) LEFT JOIN `musers` a ON `a`.`UserUID` = `torderfollowup`.`UserUID` LEFT JOIN `musers` b ON `b`.`UserUID` = `torderfollowup`.`CreatedByUserUID` LEFT JOIN `musers` c ON `c`.`UserUID` = `torderfollowup`.`CompletedByUserUID` LEFT JOIN `musers` d ON `d`.`UserUID` = `torderfollowup`.`StartedByUserUID`  LEFT JOIN `musers` e ON `e`.`UserUID` = `torderfollowup`.`CancelledByUserUID` WHERE `FollowUpUID` = '".$FollowUpUID."' ORDER BY `CreateOnDateTime` DESC
    ");
   $FollowUp = $query1->row();
  
$query2 = $this->db->query("SELECT * FROM taudittrail WHERE `OrderUID` = ".$FollowUp->OrderUID." AND `Content`IS NOT NULL ORDER BY `DateTime` DESC");
$allHis = $query2->result_array();
$NArr = [];
foreach ($allHis as $His) {
  $user  = $this->common_model->GetUserDetailsByUser($His['UserUID']);
  $His['UserName'] = $user->UserName;
    $dt = json_decode($His['Content']);
   if((int)$dt->FollowUpUID == (int)$FollowUpUID){
    array_push($NArr,$His);
  }
}
$Res = array('data' => $query1->row(),
'fhistory' => $NArr);
return $Res;
}


function changenotification_read($FollowUpUID){

  $datas = array(
    "IsRead"=>1,
  );

  $this->db->where('FollowUpUID',$FollowUpUID);
  $this->db->update('torderfollowup',$datas);

}

function get_editusersbytype($OrderUID,$type,$UserUID){

  if($type == "Customer"){

    $query  = $this->db->query("SELECT `torders`.`CustomerUID` as UserUID, `CustomerName` as UserName FROM torders JOIN `mcustomers` ON `mcustomers`.`CustomerUID` = `torders`.`CustomerUID` WHERE `OrderUID` = '".$OrderUID."' ");

    return $query->result();

  }elseif ($type == "Abstractor") {

    $query = $this->db->query("SELECT `mabstractor`.`UserUID` as UserUID,CONCAT(COALESCE(mabstractor.AbstractorFirstName,''), ' ', COALESCE(mabstractor.AbstractorLastName,'')) as UserName FROM torderabstractor JOIN `mabstractor` ON `mabstractor`.`AbstractorUID` = `torderabstractor`.`AbstractorUID` WHERE `torderabstractor`.`OrderUID` = '".$OrderUID."' GROUP BY `torderabstractor`.`AbstractorUID` ");

    return $query->result();

  }elseif ($type == "Vendor") {

    $query = $this->db->query("SELECT UserName,AssignedToUserUID As UserUID FROM torderassignment JOIN musers on musers.UserUID = torderassignment.AssignedToUserUID WHERE `OrderUID` = '".$OrderUID."' AND torderassignment.VendorUID != '0' AND SendToVendor = '1' ");
    return $query->result();
   
  }elseif ($type == "Organization") {

    $query = $this->db->query("SELECT UserName, UserUID FROM musers JOIN mroles on mroles.RoleUID = musers.RoleUID WHERE RoleType IN (1,2,3,4,5,6,7) AND musers.Active = 1 ");
    return $query->result();

  }else {

    $query = $this->db->query("SELECT UserName, UserUID FROM musers WHERE musers.UserUID = '".$UserUID."' ");
    return $query->result();

  }

}

function SaveMiscellaneousNotes($OrderUID,$MiscellaneousNotes)
{
    $dataArray = array("MiscellaneousNotes"=>$MiscellaneousNotes);
    $this->db->where('OrderUID',$OrderUID);
    $this->db->update('torders',$dataArray);
    if($this->db->affected_rows() > 0)
    {
      return true;
    }else{
      return false;
    }
}

function GetMiscellaneousNotes($OrderUID)
{
      $this->db->select('MiscellaneousNotes');
      $this->db->from('torders');
      $this->db->where('OrderUID',$OrderUID);
      return $this->db->get()->row()->MiscellaneousNotes;
}

  
function InsertNewFollowupType($type){
  $this->db->select('name');
  $this->db->from('mFollowupTypes');
  $this->db->where('name',$type);
  $query = $this->db->get();
  if(empty($query->result_array())){
    // Insert Data
    $InsertData = [
      'Name'=>$type,
      'Description'=>$type.' Description'
    ];  
    $query = $this->db->insert('mFollowupTypes',$InsertData);
  }
}

/**
  * @description Get Notes Details
  * @param 
  * @throws no exception
  * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
  * @since  13/10/2020
  * @version  
  */

function GetNotesDetails($OrderUID){
  $this->db->select("*");
  $this->db->from('tordernotes');
  $this->db->join('musers','tordernotes.CreatedByUserUID=musers.UserUID','left');
  $this->db->join('mreportsections','tordernotes.SectionUID=mreportsections.SectionUID','left');
  $this->db->where(array("tordernotes.OrderUID"=>$OrderUID));
  $query = $this->db->get();
  return $query->result();
}
/**
  * @description Save Notes 
  * @param 
  * @throws no exception
  * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
  * @since  13/10/2020
  * @version  
  */

 function Save_Notes($PostArray)
  {

    $fieldArray = array(
      "OrderUID"=>$PostArray['OrderUID'],
      "Note"=>$PostArray['NoteCmt'],
      "SectionUID"=>$PostArray['NoteType'],
      "OrgUID"=>"1",
      "RoleType"=>"1,2,3,4,5,6,7,9,11,12",
      "CreatedByUserUID"=>$this->session->userdata('UserUID'),
      "CreatedOn"=>date('y-m-d H:i:s')
    );
    $res = $this->db->insert('tordernotes', $fieldArray);
    $data1['ModuleName']='notes_add';
    $data1['Content'] = 'note information added';
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['DateTime']=date('y-m-d H:i:s');
    $data1['TableName']='tordernotes';
    $data1['UserUID']=$this->session->userdata('UserUID');                
    $this->common_model->Audittrail_insert($data1);

    if($res){
      $data=array("msg"=>"Notes are Added Successfully","type"=>"color success");
    }
    else{
      $data=array("msg"=>"error","type"=>"error");
    }
    echo json_encode($data);
  }
/**
  * @description Save Notes Comment
  * @param 
  * @throws no exception
  * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
  * @since  13/10/2020
  * @version  
  */

function Save_Notes_Comment($PostArray)
  {

    $fieldArray = array(
      "OrderUID"=>$PostArray['OrderUID'],
      "NotesUID"=>$PostArray['NoteUID'],
      "Comment"=>$PostArray['Noteinfo'],
      "OrgUID"=>"1",
      "CreatedByUserUID"=>$this->session->userdata('UserUID'),
      "CreatedOn"=>date('y-m-d H:i:s')
    );
    $res = $this->db->insert('tOrderNotesComments', $fieldArray);
    $data1['ModuleName']='notes_comment';
    $data1['Content'] = 'note comments added';
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['DateTime']=date('y-m-d H:i:s');
    $data1['TableName']='tOrderNotesComments';
    $data1['UserUID']=$this->session->userdata('UserUID');                
    $this->common_model->Audittrail_insert($data1);

    if($res){
      $data=array("msg"=>"Comments are Added Successfully","type"=>"color success");
    }
    else{
      $data=array("msg"=>"error","type"=>"error");
    }
    echo json_encode($data);
  }


/**
  * @description Comment Fetch
  * @param 
  * @throws no exception
  * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
  * @since  13/10/2020
  * @version  
  */ 
public function CommentAdd($PostArray){

 $this->db->select("*");
  $this->db->from('tOrderNotesComments');
  $this->db->join('musers','tOrderNotesComments.CreatedByUserUID=musers.UserUID','left');
  $this->db->where(array("tOrderNotesComments.NotesUID"=>$PostArray['NoteAttr']));
  $query = $this->db->get();
  return $query->result();
}


}
?>
