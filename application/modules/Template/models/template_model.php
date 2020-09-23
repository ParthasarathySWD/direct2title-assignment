<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Template_model extends CI_Model {

	
  function __construct()
  { 
    parent::__construct();
  }

  function GetProductDetails(){

        $this->db->select("*");
        $this->db->from('mproducts');
        $query = $this->db->get();
        return $query->result();
  }

  function GetTemplateFileName($TemplateUID){

        $this->db->select("*");
        $this->db->from('mtemplates');
        $this->db->where(array("mtemplates.TemplateUID"=>$TemplateUID));
        $query = $this->db->get();
        return $query->row();
  }

  function GetTemplateDetails()
  {
      /**
       * @description Order by TemplateUID for showing recent add record on top.
       * @author karthick Kandasamy <karthick.kandasamy@avanzegroup.com>
       * @since  11 Aug 2020
       */
      $this->db->select("*");
      $this->db->from('mtemplates');
      $this->db->order_by("TemplateUID", "desc");
      $query = $this->db->get();
      return $query->result();
     }

  function GetTemplateByID($TemplateUID)
  { 
    $q = $this->db->get_where("mtemplates","TemplateUID = $TemplateUID");
    return $q->result();
  }

 function Duplicate_Entry($Template)
 {
   $this->db->where('TemplateCode',$Template);
   return $this->db->get('mtemplates')->num_rows();
 }
  
  function SaveTemplate($data,$ProjectUID)
  {
    if($this->db->insert('mtemplates',$data)==true)
    {
      $TemplateUID=$this->db->insert_id();

        /**
         * @description Version Based Add Templates
         * @author karthick Kandasamy <karthick.kandasamy@avanzegroup.com>
         * @since  11 Aug 2020
         */

        if($TemplateUID){

            $VersionCount = $this->db->query("SELECT * FROM mTemplateVersion WHERE TemplateUID = '$TemplateUID'")->num_rows();
            if(isset($VersionCount) && !empty($VersionCount)){
                $VersionName = $VersionCount;
            } else{
                $VersionName = 0;
            }


           $temp_version = array(
                    'TemplateUID' => $TemplateUID,
                    'VersionName' => $VersionName+1,
                    'DateTime' => date('Y-m-d H:i:s'),
                    'UserUID' => $this->session->userdata('UserUID'),
                );
            $this->db->insert('mTemplateVersion', $temp_version);
            $TemplateVersionID=$this->db->insert_id();

            $data['TemplateVersionID'] = $TemplateVersionID;
            array_push($data);
           $this->db->insert('mTemplateVersionDetails',$data);

        }

        if($ProjectUID){
        foreach ($ProjectUID as $key => $value) {
          $temp_proj = array(
            'TemplateUID' => $TemplateUID,
            'ProjectUID' => $value,
          );
          $this->db->insert('mTemplateProject', $temp_proj);
        }
      }



      $data1['ModuleName']='template_add';
      $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
      $data1['DateTime']=date('y-m-d H:i:s');
      $data1['TableName']='mtemplates';
      $data1['UserUID']=$this->session->userdata('UserUID');                
      $this->common_model->Audittrail_insert($data1);
      return $TemplateUID;
    } else {
      return 0;
    }
  }
 
  function UpdateTemplate($TemplateUID,$data,$ProjectUID)
  {

    $query = $this->db->query("DELETE FROM mTemplateProject WHERE TemplateUID ='$TemplateUID' ");

    if($ProjectUID){
      foreach ($ProjectUID as $key => $value) {
        $temp_proj = array(
          'TemplateUID' => $TemplateUID,
          'ProjectUID' => $value,
        );
        $this->db->insert('mTemplateProject', $temp_proj);
      }
    }

      if($TemplateUID){

          $VersionCount = $this->db->query("SELECT * FROM mTemplateVersion WHERE TemplateUID = '$TemplateUID'")->num_rows();
           if(isset($VersionCount) && !empty($VersionCount)){
               $VersionName = $VersionCount;
           } else{
               $VersionName = 0;
           }

          $temp_version = array(
              'TemplateUID' => $TemplateUID,
              'VersionName' => $VersionName+1,
              'DateTime' => date('Y-m-d H:i:s'),
              'UserUID' => $this->session->userdata('UserUID'),
          );
          $this->db->insert('mTemplateVersion', $temp_version);
          $TemplateVersionID=$this->db->insert_id();

          $data['TemplateVersionID'] = $TemplateVersionID;
          array_push($data);
          $this->db->insert('mTemplateVersionDetails',$data);

          unset($data['TemplateVersionID']);

      }

    $data1['ModuleName']='template-update';
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['DateTime']=date('y-m-d H:i:s');
    $data1['TableName']='mtemplates';
    $data1['UserUID']=$this->session->userdata('UserUID');                

    $this->db->select('*');
    //$this->db->from('mtemplates');
    $this->db->where('TemplateUID',$TemplateUID);
    $PreviousVal = $this->db->get('mtemplates')->row();

        $this->db->where('TemplateUID',$TemplateUID);
        $val=  $this->db->update('mtemplates',$data);
      $this->db->select('*');
      $this->db->from('mtemplates');
      $this->db->where('TemplateUID',$TemplateUID);
      $NewVal = $this->db->get('')->row();

      /**
       * @description Audit Logs
       * @throws no exception
       * @author karthick Kandasamy <karthick.kandasamy@avanzegroup.com>
       * @since  10 Aug 2020
       */

      if(
          $PreviousVal->TemplateCode != $NewVal->TemplateCode ||
          $PreviousVal->TemplateName != $NewVal->TemplateName ||
          $PreviousVal->ProductUID != $NewVal->ProductUID ||
          $PreviousVal->IsDynamicTemplate != $NewVal->IsDynamicTemplate ||
          $PreviousVal->TemplatePath != $NewVal->TemplatePath ||
          $PreviousVal->TemplateFileName != $NewVal->TemplateFileName ||
          $PreviousVal->TemplateTypeFile != $NewVal->TemplateTypeFile ||
          $PreviousVal->PageSize != $NewVal->PageSize ||
          $PreviousVal->MarginTop != $NewVal->MarginTop ||
          $PreviousVal->MarginBottom != $NewVal->MarginBottom ||
          $PreviousVal->MarginLeft != $NewVal->MarginLeft ||
          $PreviousVal->MarginRight != $NewVal->MarginRight ||
          $PreviousVal->FirstMarginTop != $NewVal->FirstMarginTop ||
          $PreviousVal->FirstMarginBottom != $NewVal->FirstMarginBottom ||
          $PreviousVal->FirstMarginRight != $NewVal->FirstMarginRight ||
          $PreviousVal->FirstMarginLeft != $NewVal->FirstMarginLeft ||
          $PreviousVal->Active != $NewVal->Active
      )
      {
          $msg = '';
          $change = false;



          if($PreviousVal->TemplateCode != $NewVal->TemplateCode){
              $msg .= '<b>TemplateCode</b> is Changed from '.$PreviousVal->TemplateCode.' to '.$NewVal->TemplateCode.' | ';
              $change = true;
          }
          if($PreviousVal->TemplateName != $NewVal->TemplateName){
              $msg .= '<b>TemplateName</b> is Changed from '.$PreviousVal->TemplateName.' to '.$NewVal->TemplateName.' | ';
              $change = true;
          }

          if($PreviousVal->ProductUID != $NewVal->ProductUID){
              $Product_name_new = $this->GetProductName($NewVal->ProductUID);
              $Product_new = implode(',',array_column($Product_name_new, 'ProductName'));
              $Product_name_old = $this->GetProductName($PreviousVal->ProductUID);
              $Product_old = implode(',',array_column($Product_name_old, 'ProductName'));

              $msg .= '<b>Product</b> is Changed from '.$Product_old.' to '.$Product_new;
              $change = true;
          }
          if($PreviousVal->IsDynamicTemplate == 1)
          {
              $status = 'Active';
              $new_status = 'In-Active';
          }else{
              $status = 'In-Active';
              $new_status = 'Active';
          }

          if($PreviousVal->IsDynamicTemplate != $NewVal->IsDynamicTemplate){
              $msg .= '<b>IsDynamicTemplate</b> is Changed from '.$status.' <b>to</b> '.$new_status.'  ';
              $change = true;
          }
          if($PreviousVal->TemplatePath != $NewVal->TemplatePath){
              $msg .= '<b>TemplatePath</b> is Changed from '.$PreviousVal->TemplatePath.' <b>to</b> '.$NewVal->TemplatePath.'  ';
              $change = true;
          }
          if($PreviousVal->TemplateFileName != $NewVal->TemplateFileName){
              $msg .= '<b>TemplateFileName</b> is Changed from '.$PreviousVal->TemplateFileName.' <b>to</b> '.$NewVal->TemplateFileName.'  ';
              $change = true;
          }
          if($PreviousVal->TemplateTypeFile != $NewVal->TemplateTypeFile){
              $msg .= '<b>TemplateTypeFile</b> is Changed from '.$PreviousVal->TemplateTypeFile.' <b>to</b> '.$NewVal->TemplateTypeFile.'  ';
              $change = true;
          }
          if($PreviousVal->PageSize != $NewVal->PageSize){
              $msg .= '<b>PageSize</b> is Changed from '.$PreviousVal->PageSize.' <b>to</b> '.$NewVal->PageSize.'  ';
              $change = true;
          }
          if($PreviousVal->MarginTop != $NewVal->MarginTop){
              $msg .= '<b>MarginTop</b> is Changed from '.$PreviousVal->MarginTop.' <b>to</b> '.$NewVal->MarginTop.'  ';
              $change = true;
          }
          if($PreviousVal->MarginBottom != $NewVal->MarginBottom){
              $msg .= '<b>MarginBottom</b> is Changed from '.$PreviousVal->MarginBottom.' <b>to</b> '.$NewVal->MarginBottom.'  ';
              $change = true;
          }
          if($PreviousVal->MarginLeft != $NewVal->MarginLeft){
              $msg .= '<b>MarginLeft</b> is Changed from '.$PreviousVal->MarginLeft.' <b>to</b> '.$NewVal->MarginLeft.'  ';
              $change = true;
          }
          if($PreviousVal->MarginRight != $NewVal->MarginRight){
              $msg .= '<b>MarginRight</b> is Changed from '.$PreviousVal->MarginRight.' <b>to</b> '.$NewVal->MarginRight.'  ';
              $change = true;
          }
          if($PreviousVal->FirstMarginTop != $NewVal->FirstMarginTop){
              $msg .= '<b>FirstMarginTop</b> is Changed from '.$PreviousVal->FirstMarginTop.' <b>to</b> '.$NewVal->FirstMarginTop.'  ';
              $change = true;
          }
          if($PreviousVal->FirstMarginBottom != $NewVal->FirstMarginBottom){
              $msg .= '<b>FirstMarginBottom</b> is Changed from '.$PreviousVal->FirstMarginBottom.' <b>to</b> '.$NewVal->FirstMarginBottom.'  ';
              $change = true;
          }
          if($PreviousVal->FirstMarginRight != $NewVal->FirstMarginRight){
              $msg .= '<b>FirstMarginRight</b> is Changed from '.$PreviousVal->FirstMarginRight.' <b>to</b> '.$NewVal->FirstMarginRight.'  ';
              $change = true;
          }
          if($PreviousVal->FirstMarginLeft != $NewVal->FirstMarginLeft){
              $msg .= '<b>FirstMarginLeft</b> is Changed from '.$PreviousVal->FirstMarginLeft.' <b>to</b> '.$NewVal->FirstMarginLeft.'  ';
              $change = true;
          }
          if($PreviousVal->Active == 1)
          {
            $status = 'Active';
            $new_status = 'In-Active';
          }else{
              $status = 'In-Active';
              $new_status = 'Active';
          }

          if($PreviousVal->Active != $NewVal->Active){
              $msg .= '<b>Active</b> is Changed from '.$status.' <b>to</b> '.$new_status.'  ';
              $change = true;
          }

        if($change == true){
              AuditHelper('template-setup',$TemplateUID,$msg);
          }
      }

    if($val > 0)
      {
          return 1;
      }
      else{
          return 0;
      }

  }

 function DeleteTemplate($Id)
 {
  $query = $this->db->query("DELETE FROM mtemplates WHERE TemplateUID ='$Id' ");
  if($this->db->affected_rows() > 0)
  {
                  $data1['ModuleName']='template-delete';
                  $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
                  $data1['DateTime']=date('y-m-d H:i:s');
                  $data1['TableName']='mtemplates';
                  $data1['UserUID']=$this->session->userdata('UserUID');                
                  $this->common_model->Audittrail_insert($data1);
    return true;
  }
  else
  {
    return false;
  }
}

  /* *** Field Update section starts *** */

  /**
    *@description Function to saveFieldSectionDetails
    *
    * @param FieldSectionDetails
    * 
    * @throws no exception
    * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
    * @return bool 
    * @since 12-8-2020 
    * @version Title Commitment 
    *
  */ 
  function saveFieldSectionDetails($FieldSectionDetails, $TemplateUID)
  {
    /*Code*/

    if (empty($TemplateUID)) {
      return false;
    }

    $DocumentLanguageUIDs = $FieldSectionDetails['DocumentLanguage'];
    $DocumentCategoryUIDs = $FieldSectionDetails['DocumentCategory'];
    $LanguageGroupUIDs = $FieldSectionDetails['LanguageGroup'];
    $FieldUIDs = $FieldSectionDetails['Field'];
    $TemplateFieldSectionUIDs = $FieldSectionDetails['TemplateFieldSectionUID'];


    $SaveDocumentLanguageFieldSection = $UpdateDocumentLanguageFieldSection = [];
    foreach ($DocumentLanguageUIDs as $key => $uid) 
    {
      $insert = $update = [];
      if (!empty($uid) && !empty($TemplateUID)) 
      {

        if (isset($TemplateFieldSectionUIDs[$key]) && !empty($TemplateFieldSectionUIDs[$key])) {
         $update['DocumentLanguageUID'] = $uid;
         $update['TemplateUID'] = $TemplateUID;
         $update['TemplateFieldSectionUID'] = $TemplateFieldSectionUIDs[$key];
         $update['FieldUID'] = NULL;
         $update['DocumentCategoryUID'] = NULL;
         $update['LanguageGroupUID'] = NULL;

         $UpdateDocumentLanguageFieldSection[] = $update;
       } 
       else
       {
         $insert['DocumentLanguageUID'] = $uid;
         $insert['TemplateUID'] = $TemplateUID;
         $insert['FieldUID'] = NULL;
         $insert['DocumentCategoryUID'] = NULL;
         $insert['LanguageGroupUID'] = NULL;

         $SaveDocumentLanguageFieldSection[] = $insert;

       }
     }
   }

    $SaveDocumentCategoryFieldSection = $UpdateDocumentCategoryFieldSection = [];
    foreach ($DocumentCategoryUIDs as $key => $uid) 
    {
      $insert = $update = [];
      if ( !empty($uid) ) 
      {

        if (isset($TemplateFieldSectionUIDs[$key]) && !empty($TemplateFieldSectionUIDs[$key])) {
         $update['DocumentCategoryUID'] = $uid;
         $update['TemplateUID'] = $TemplateUID;
         $update['TemplateFieldSectionUID'] = $TemplateFieldSectionUIDs[$key];
         $update['FieldUID'] = NULL;
         $update['DocumentLanguageUID'] = NULL;
         $update['LanguageGroupUID'] = NULL;

         $UpdateDocumentCategoryFieldSection[] = $update;
       } 
       else
       {
         $insert['DocumentCategoryUID'] = $uid;
         $insert['TemplateUID'] = $TemplateUID;
         $insert['FieldUID'] = NULL;
         $insert['DocumentLanguageUID'] = NULL;
         $insert['LanguageGroupUID'] = NULL;

         $SaveDocumentCategoryFieldSection[] = $insert;

       }
     }
   }

    $SaveLanguageFieldSection = $UpdateLanguageFieldSection = [];
    foreach ($LanguageGroupUIDs as $key => $uid) 
    {
      $insert = $update = [];
      if ( !empty($uid) ) 
      {
        
        if (isset($TemplateFieldSectionUIDs[$key]) && !empty($TemplateFieldSectionUIDs[$key])) {
         $update['DocumentCategoryUID'] = NULL;
         $update['TemplateUID'] = $TemplateUID;
         $update['TemplateFieldSectionUID'] = $TemplateFieldSectionUIDs[$key];
         $update['FieldUID'] = NULL;
         $update['DocumentCategoryUID'] = NULL;
         $update['LanguageGroupUID'] = $uid;

         $UpdateLanguageFieldSection[] = $update;
       } 
       else
       {
         $insert['DocumentCategoryUID'] = NULL;
         $insert['TemplateUID'] = $TemplateUID;
         $insert['FieldUID'] = NULL;
         $insert['DocumentCategoryUID'] = NULL;
         $insert['LanguageGroupUID'] = $uid;

         $SaveLanguageFieldSection[] = $insert;

       }
     }
   }

    $SaveFieldFieldSection = $UpdateFieldFieldSection = [];
    foreach ($FieldUIDs as $key => $uid) 
    {
      $insert = $update = [];
      if ( !empty($uid) )
      {
        
        if (isset($TemplateFieldSectionUIDs[$key]) && !empty($TemplateFieldSectionUIDs[$key])) {
         $update['DocumentCategoryUID'] = NULL;
         $update['TemplateUID'] = $TemplateUID;
         $update['TemplateFieldSectionUID'] = $TemplateFieldSectionUIDs[$key];
         $update['FieldUID'] = $uid;
         $update['DocumentCategoryUID'] = NULL;
         $update['LanguageGroupUID'] = NULL;

         $UpdateFieldFieldSection[] = $update;
       } 
       else
       {
         $insert['DocumentCategoryUID'] = NULL;
         $insert['TemplateUID'] = $TemplateUID;
         $insert['FieldUID'] = $uid;
         $insert['DocumentCategoryUID'] = NULL;
         $insert['LanguageGroupUID'] = NULL;

         $SaveFieldFieldSection[] = $insert;

       }
     }
   }

    if( !empty( $SaveDocumentLanguageFieldSection ) )
    {
      $this->db->insert_batch('mTemplateFieldSection', $SaveDocumentLanguageFieldSection);
    }
    if( !empty( $UpdateDocumentLanguageFieldSection ) )
    {
      $this->db->update_batch('mTemplateFieldSection', $UpdateDocumentLanguageFieldSection, 'TemplateFieldSectionUID');
    }
    if( !empty( $SaveDocumentCategoryFieldSection ) )
    {
      $this->db->insert_batch('mTemplateFieldSection', $SaveDocumentCategoryFieldSection);
    }
    if( !empty( $UpdateDocumentCategoryFieldSection ) )
    {
      $this->db->update_batch('mTemplateFieldSection', $UpdateDocumentCategoryFieldSection, 'TemplateFieldSectionUID');
    }
    if( !empty( $SaveLanguageFieldSection ) )
    {
      $this->db->insert_batch('mTemplateFieldSection', $SaveLanguageFieldSection);
    }
    if( !empty( $UpdateLanguageFieldSection ) )
    {
      $this->db->update_batch('mTemplateFieldSection', $UpdateLanguageFieldSection, 'TemplateFieldSectionUID');
    }
    if( !empty( $SaveFieldFieldSection ) )
    {
      $this->db->insert_batch('mTemplateFieldSection', $SaveFieldFieldSection);
    }
    if( !empty( $UpdateFieldFieldSection ) )
    {
      $this->db->update_batch('mTemplateFieldSection', $UpdateFieldFieldSection, 'TemplateFieldSectionUID');
    }

      //$insert_TemplateFieldSectionUID = $this->db->insert_id();

      $DocumentLanguageUIDs = $FieldSectionDetails['DocumentLanguage'];
      $DocumentCategoryUIDs = $FieldSectionDetails['DocumentCategory'];
      $LanguageGroupUIDs = $FieldSectionDetails['LanguageGroup'];
      $FieldUIDs = $FieldSectionDetails['Field'];


     if(
            !empty($DocumentLanguageUIDs) ||!empty($DocumentCategoryUIDs) ||
            !empty($LanguageGroupUIDs) ||!empty($FieldUIDs)

      ) {
         $msg = '';
         $change = false;
         $last_row=$this->db->select('TemplateFieldSectionUID')->order_by('TemplateFieldSectionUID',"desc")->limit(1)->get('mTemplateFieldSection')->row();

         $FieldMapping =  $this->getTemplateFieldSectionNames($last_row->TemplateFieldSectionUID);

         if(isset($FieldMapping) && $FieldMapping->FieldName != ''){
             $name = 'Field Mapping <b>Field List</b>';
             $value = $FieldMapping->FieldName;
         }else if(isset($FieldMapping) && $FieldMapping->DocumentCategoryName != ''){
             $name = 'Field Mapping <b>Document Category</b>';
             $value = $FieldMapping->DocumentCategoryName;
         }else if(isset($FieldMapping) && $FieldMapping->LanguageName != ''){
             $name = 'Field Mapping <b>Document Language</b>';
             $value = $FieldMapping->LanguageName;
         }else if(isset($FieldMapping) && $FieldMapping->LanguageGroupName != ''){
             $name = 'Field Mapping <b>Group</b>';
             $value = $FieldMapping->LanguageGroupName;
         }else{
             $name = '';
             $value = '';
         }

        if(isset($value) && $value != ''){
                $msg = ''.$name.'Added <b>'.$value.'</b>';
             AuditHelper('template-setup',$TemplateUID,$msg);
             }
        }
    }
  /* *** Field Update section Ends *** */


  /* *** Field Section Details *** */

  /**
    *@description Function to getTemplateFieldSectionDetails
    *
    * @param $TemplateUID
    * 
    * @throws no exception
    * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
    * @return Array 
    * @since 12-8-2020 
    * @version Title Commitment 
    *
  */ 
  function getTemplateFieldSectionDetails($TemplateUID)
  {
    
    $this->db->select('mTemplateFieldSection.*, mfields.FieldName, mfields.FieldKeyword, mDocumentLanguages.LanguageName, mDocumentCategory.DocumentCategoryName, mLanguageGroups.LanguageGroupName');
    $this->db->from('mTemplateFieldSection'); 
    $this->db->join('mfields', 'mfields.FieldUID = mTemplateFieldSection.FieldUID', 'left');
    $this->db->join('mDocumentLanguages', 'mDocumentLanguages.DocumentLanguageUID = mTemplateFieldSection.DocumentLanguageUID', 'left');
    $this->db->join('mDocumentCategory', 'mDocumentCategory.DocumentCategoryUID = mTemplateFieldSection.DocumentCategoryUID', 'left');
    $this->db->join('mLanguageGroups', 'mLanguageGroups.LanguageGroupUID = mTemplateFieldSection.LanguageGroupUID', 'left');
    $this->db->where('mTemplateFieldSection.TemplateUID', $TemplateUID);
    return $this->db->get()->result();
  }

    /**
     * @description Getting Audit data
     * @author karthick Kandasamy <karthick.kandasamy@avanzegroup.com>
     * @since  10 Aug 2020
     */
    function getTemplateAudits($post,$count = false){

        $this->db->select('taudittrail.*,musers.UserName');
        $this->db->from('taudittrail');
        $this->db->join('musers','musers.UserUID = taudittrail.UserUID','left');
        $this->db->where('ModuleName','template-setup');
        $this->db->where('Feature',$post['formData']['TemplateUID']);
        if($count == true){
            return $this->db->get()->num_rows();
        }
        if ($post['length'] != '' && $post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $this->db->order_by('AuditUID','DESC');
        return $this->db->get()->result();
    }

    /**
     * @description Getting Audit data
     * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
     * @since  22/09/2020
     */

     function getTemplateAuditsDetails($post){

        $this->db->select('taudittrail.*,musers.UserName');
        $this->db->from('taudittrail');
        $this->db->join('musers','musers.UserUID = taudittrail.UserUID','left');
        $this->db->where('ModuleName','template-setup');
        $this->db->where('Feature',$post);
        $this->db->order_by('AuditUID','DESC');
        return $this->db->get()->result();
    }


    /**
     * @description Getting Poduct Names
     * @author karthick Kandasamy <karthick.kandasamy@avanzegroup.com>
     * @since  10 Aug 2020
     */
    function GetProductName($productUID){

        $this->db->select("ProductName");
        $this->db->from('mproducts');
        $this->db->where_in('ProductUID',explode(',',$productUID));
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * @description Getting Project Names
     * @author karthick Kandasamy <karthick.kandasamy@avanzegroup.com>
     * @since  10 Aug 2020
     */
    function GetProjectName($ProductUID)
    {
        $this->db->select('*');
        $this->db->from ( 'mProjects' );
        $this->db->where_in('mProjects.ProductUID',$ProductUID);
        $query = $this->db->get();
        $Projects =  $query->result();
    }

    /**
     * @description Getting TemplateVersion Names
     * @author karthick Kandasamy <karthick.kandasamy@avanzegroup.com>
     * @since  11 Aug 2020
     */
    function getTemplateVersions($post,$count = false){

        $this->db->select('mTemplateVersion.*,musers.UserName');
        $this->db->from('mTemplateVersion');
        $this->db->join('musers','musers.UserUID = mTemplateVersion.UserUID','left');
        $this->db->where('TemplateUID',$post['formData']['TemplateUID']);
        if($count == true){
            return $this->db->get()->num_rows();
        }
        if ($post['length'] != '' && $post['length'] != -1) {
            $this->db->limit($post['length'], $post['start']);
        }
        $this->db->order_by('TemplateVersionID','DESC');
        return $this->db->get()->result();
    }

    /**
     * @description Getting TemplateVersionDetails
     * @author karthick Kandasamy <karthick.kandasamy@avanzegroup.com>
     * @since  11 Aug 2020
     */
    function GetTemplateVersionDetailsByID($TemplateVersionID)
    {
        $q = $this->db->get_where("mTemplateVersionDetails","TemplateVersionID = $TemplateVersionID");
        return $q->result();
    }

    /**
     *@description Function to getTemplateFieldSectionDetails
     *
     * @param $TemplateUID
     *
     * @throws no exception
     * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
     * @return Array
     * @since 12-8-2020
     * @version Title Commitment
     *
     */
    function getDocumentLanguageName($DocumentLanguageUID)
    {
        $q = $this->db->get_where("mDocumentLanguages","DocumentLanguageUID =".implode(array_values($DocumentLanguageUID)));
        return $q->row();
     }

    /**
     *@description Function to Delete TemplateFieldSection
     * @throws no exception
     * @author Karthick Kandasamy <karthick.kandasamy@avanzegroup.com>
     * @since 13-8-2020
     * @version Title Commitment
     *
     */

    function DeleteTemplateFieldSection($Id)
    {
        $getdata = $this->db->get_where("mTemplateFieldSection","TemplateFieldSectionUID =".$Id);
        $TemplateFieldSection =  $getdata->row();

        $FieldMapping =  $this->getTemplateFieldSectionNames($Id);
        if(isset($FieldMapping) && $FieldMapping->FieldName != ''){
            $name = 'Field Mapping <b>Field List</b>';
            $value = $FieldMapping->FieldName;
        }else if(isset($FieldMapping) && $FieldMapping->DocumentCategoryName != ''){
            $name = 'Field Mapping <b>Document Category</b>';
            $value = $FieldMapping->DocumentCategoryName;
        }else if(isset($FieldMapping) && $FieldMapping->LanguageName != ''){
            $name = 'Field Mapping <b>Document Language</b>';
            $value = $FieldMapping->LanguageName;
        }else if(isset($FieldMapping) && $FieldMapping->LanguageGroupName != ''){
            $name = 'Field Mapping <b>Group</b>';
            $value = $FieldMapping->LanguageGroupName;
        }else{
            $name = '';
            $value = '';
        }

        $this->db->query("DELETE FROM mTemplateFieldSection WHERE TemplateFieldSectionUID ='$Id' ");
        if($this->db->affected_rows() > 0)
        {
             $msg = ''.$name.'Deleted <b>'.$value.'</b>';

             AuditHelper('template-setup',$TemplateFieldSection->TemplateUID,$msg);

             return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *@description Function to getTemplateFieldSectionDetails
     * @throws no exception
     * @author Karthick Kandasamy <karthick.kandasamy@avanzegroup.com>
     * @return Array
     * @since 12-8-2020
     * @version Title Commitment
     *
     */
    function getTemplateFieldSectionNames($TemplateFieldSectionUID)
    {
        $this->db->select('mTemplateFieldSection.*, mfields.FieldName, mfields.FieldKeyword, mDocumentLanguages.LanguageName, mDocumentCategory.DocumentCategoryName, mLanguageGroups.LanguageGroupName');
        $this->db->from('mTemplateFieldSection');
        $this->db->join('mfields', 'mfields.FieldUID = mTemplateFieldSection.FieldUID', 'left');
        $this->db->join('mDocumentLanguages', 'mDocumentLanguages.DocumentLanguageUID = mTemplateFieldSection.DocumentLanguageUID', 'left');
        $this->db->join('mDocumentCategory', 'mDocumentCategory.DocumentCategoryUID = mTemplateFieldSection.DocumentCategoryUID', 'left');
        $this->db->join('mLanguageGroups', 'mLanguageGroups.LanguageGroupUID = mTemplateFieldSection.LanguageGroupUID', 'left');
        $this->db->where('mTemplateFieldSection.TemplateFieldSectionUID', $TemplateFieldSectionUID);
        return $this->db->get()->row();
    }

    /**
     * Function - getFieldList base on document type
     * @author Karthick kandasamy <karthick.kandasamy@avanzegroup.com>
     * @return Array Result of status
     * @since 14 Aug 2020
     */

    function getFieldList($DocumentTypeUID)
    {
        if(isset($DocumentTypeUID) && $DocumentTypeUID != '')
        {
            $this->db->select('*');
            $this->db->from('mfields');
            $this->db->where('DocumentTypeUID',$DocumentTypeUID);
            $FieldList = $this->db->get()->result();
        }else{
            $this->db->select('*');
            $this->db->from('mfields');
            $this->db->where('IsDocumentTypeField',1);
            $FieldList = $this->db->get()->result();

        }
            return $FieldList ;

    }
    /**
     * Function - saveTemplateClientProduct
     * @author Karthick kandasamy <karthick.kandasamy@avanzegroup.com>
     * @return Array Result of status
     * @since 19 Aug 2020
     */
    function saveTemplateClientProduct($data)
    {
        $this->db->insert('mTemplateProducts', $data);
        $insertID = $this->db->insert_id();
        if ($insertID)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }
    /**
     * Function - getFieldList base on document type
     * @author Karthick kandasamy <karthick.kandasamy@avanzegroup.com>
     * @return Array Result of status
     * @since 14 Aug 2020
     */
    function updateTemplateClientProduct($data)
    {
        $this->db->where('TemplateProductUID', $data['TemplateProductUID']);
        $updateQuery = $this->db->update('mTemplateProducts', $data);

        if ($updateQuery)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }
    /**
     * @description Get Document Language Client Details
     * @author Karthick kandasamy <karthick.kandasamy@avanzegroup.com>
     * @param TemplateUID
     * @throws no exception
     * @return array
     * @since 17-08-2020
     * @version Title Commitment
     */
    function _getTemplateClients($TemplateUID)
    {
        $TemplateClientProducts = $this->_getTemplateClientDetails($TemplateUID);
        return $TemplateClientProducts;
    }
    function _getTemplateClientDetails($TemplateUID)
    {
        $this->db->select('mcustomers.CustomerUID, mcustomers.CustomerName, mproducts.ProductUID, mproducts.ProductName, msubproducts.SubProductUID, msubproducts.SubProductName, mTemplateProducts.TemplateProductUID');

        $this->db->from('mTemplateProducts');

        $this->db->join('mcustomers', 'mcustomers.CustomerUID = mTemplateProducts.CustomerUID', 'left');

        $this->db->join('mproducts', 'mproducts.ProductUID = mTemplateProducts.ProductUID', 'left');

        $this->db->join('msubproducts', 'msubproducts.SubProductUID = mTemplateProducts.SubProductUID', 'left');

        $this->db->where('mTemplateProducts.TemplateUID', $TemplateUID);

        $result = $this->db->get()->result();
        return $result;
    }

   function GetZipcodeByStateCountyUID($StateUID,$CountyUID)
    {
        if(is_array($StateUID))
        {
            $this->db->where_in('StateUID',$StateUID);
        } else {
            $this->db->where('StateUID',$StateUID);
        }
        if(is_array($CountyUID))
        {
            $this->db->where_in('CountyUID',$CountyUID);
        } else {
            $this->db->where('CountyUID',$CountyUID);
        }
        return $this->db->get('mcities')->result();
    }

    function saveTemplateGeographic($data)
    {
        $this->db->insert('mTemplateGeographic', $data);
        $insertID = $this->db->insert_id();
        if ($insertID)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    function _getTemplateProductDetails($TemplateProductUID)
    {
        $this->db->select('mcustomers.CustomerUID, mcustomers.CustomerName, mproducts.ProductUID, mproducts.ProductName, msubproducts.SubProductUID, msubproducts.SubProductName, mTemplateProducts.TemplateProductUID');

        $this->db->from('mTemplateProducts');

        $this->db->join('mcustomers', 'mcustomers.CustomerUID = mTemplateProducts.CustomerUID', 'left');

        $this->db->join('mproducts', 'mproducts.ProductUID = mTemplateProducts.ProductUID', 'left');

        $this->db->join('msubproducts', 'msubproducts.SubProductUID = mTemplateProducts.SubProductUID', 'left');

        $this->db->where('mTemplateProducts.TemplateProductUID', $TemplateProductUID);

        $result = $this->db->get()->result();
        return $result;
    }

    public function get_productsbasedoncustomer($CustomerUID)
    {
        $this->db->select('mcustomerproducts.ProductUID, mproducts.ProductName');
        $this->db->from('mcustomerproducts');
        $this->db->join('mproducts', 'mproducts.ProductUID = mcustomerproducts.ProductUID');
        $this->db->where('mcustomerproducts.CustomerUID', $CustomerUID);
        $this->db->where('mproducts.Active', 1);
        $this->db->group_by('mproducts.ProductUID');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     *Function fetch subproduct by product customer
     * @author Karthick kandasamy <karthick.kandasamy@avanzegroup.com>
     *@since 19 Aug 2020
     */
    public function get_subproductsbasedoncustomer($CustomerUID, $ProductUID)
    {
        $this->db->select('msubproducts.SubProductUID, msubproducts.SubProductName,mproducts.ProductCode,mproducts.ProductName');
        $this->db->from('mcustomerproducts');
        $this->db->join('msubproducts', 'msubproducts.ProductUID = mcustomerproducts.ProductUID');
        $this->db->join('mproducts', 'mproducts.ProductUID = msubproducts.ProductUID');

        if ($CustomerUID != '') {
            $this->db->where('mcustomerproducts.CustomerUID', $CustomerUID);
        }
        /*End*/
        $this->db->where('mcustomerproducts.ProductUID', $ProductUID);
        $this->db->where('msubproducts.Active', 1);
        $this->db->group_by('msubproducts.SubProductUID');
        $query = $this->db->get();
        return $query->result();
    }
    /**
     *Function delete  delete Template Client Product
     * @author Karthick kandasamy <karthick.kandasamy@avanzegroup.com>
     *@since 19 Aug 2020
     */

    function deleteTemplateClientProduct($data)
    {
        $this->db->where('TemplateProductUID', $data['TemplateProductUID']);
        $updateQuery = $this->db->delete('mTemplateProducts', $data);

        if ($updateQuery)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    function _getTemplateGeographic($TemplateUID)
    {
        $templateGeographic = $this->_getTemplateGeographicDetails($TemplateUID);
        return $templateGeographic;

    }

    function _getTemplateGeographicDetails($TemplateUID)
    {
        $this->db->select('mstates.StateUID, mstates.StateName, mcounties.CountyUID, mcounties.CountyName,  mcities.CityUID, mcities.CityName, mTemplateGeographic.ZipCode, mTemplateGeographic.TemplateGeographicUID');

        $this->db->from('mTemplateGeographic');

        $this->db->join('mstates', 'mstates.StateUID = mTemplateGeographic.StateUID', 'left');

        $this->db->join('mcounties', 'mcounties.CountyUID = mTemplateGeographic.CountyUID', 'left');

        $this->db->join('mcities', 'mcities.CityUID = mTemplateGeographic.CityUID', 'left');

        $this->db->where('mTemplateGeographic.TemplateUID', $TemplateUID);

        $result = $this->db->get()->result();
        return $result;
    }

    function _getTemplateGeographicStateDetails($StateUID)
    {
        $this->db->select('mstates.StateUID, mstates.StateName, mcounties.CountyUID, mcounties.CountyName,  mcities.CityUID, mcities.CityName, mTemplateGeographic.ZipCode, mTemplateGeographic.TemplateGeographicUID');

        $this->db->from('mTemplateGeographic');

        $this->db->join('mstates', 'mstates.StateUID = mTemplateGeographic.StateUID', 'left');

        $this->db->join('mcounties', 'mcounties.CountyUID = mTemplateGeographic.CountyUID', 'left');

        $this->db->join('mcities', 'mcities.CityUID = mTemplateGeographic.CityUID', 'left');

        $this->db->where('mTemplateGeographic.StateUID', $StateUID);

        $result = $this->db->get()->result();
        return $result;
    }

    function updateTemplateGeographic($data)
    {
        $this->db->where('TemplateGeographicUID', $data['TemplateGeographicUID']);
        $updateQuery = $this->db->update('mTemplateGeographic', $data);

        if ($updateQuery)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    function deleteTemplateGeographic($data)
    {
        $this->db->where('TemplateGeographicUID', $data['TemplateGeographicUID']);
        $updateQuery = $this->db->delete('mTemplateGeographic', $data);

        if ($updateQuery)
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    function _getDocumentLanguageGeographicStateDetails($TemplateGeographicUID)
    {
        $this->db->select('mstates.StateUID, mstates.StateName, mcounties.CountyUID, mcounties.CountyName,  mcities.CityUID, mcities.CityName, mTemplateGeographic.ZipCode, mTemplateGeographic.TemplateGeographicUID');

        $this->db->from('mTemplateGeographic');

        $this->db->join('mstates', 'mstates.StateUID = mTemplateGeographic.StateUID', 'left');

        $this->db->join('mcounties', 'mcounties.CountyUID = mTemplateGeographic.CountyUID', 'left');

        $this->db->join('mcities', 'mcities.CityUID = mTemplateGeographic.CityUID', 'left');

        $this->db->where('mTemplateGeographic.TemplateGeographicUID', $TemplateGeographicUID);

        $result = $this->db->get()->result();
        return $result;
    }




    /**
      *@description Function to getLanguageDetails
      *
      * @param $DocumentLanguageUIDs
      *
      * @throws no exception
      * @author Sathis Kannan P <sathish.kannan@avanzegroup.com>
      * @return Array
      * @since 22-09-2020
      * @version D2T New Assignment
      *
    */

    public function getLanguageDetails($DocumentLanguageUIDs)
    {
        if (empty($DocumentLanguageUIDs)) {
            return [];
        }

        $this->db->select('mDocumentLanguages.*, mDocumentCategory.DocumentCategoryName, mDocumentCategory.IsMultiType');
        $this->db->from('mDocumentLanguages');
        $this->db->join('mDocumentCategory', 'mDocumentLanguages.DocumentCategoryUID = mDocumentCategory.DocumentCategoryUID');
        $this->db->where_in('mDocumentLanguages.DocumentLanguageUID', $DocumentLanguageUIDs);
        return $this->db->get()->result();
    }
 /**
      *@description Function to getFullDocumentLanguage_Recursive
      *
      * @param $DocumentLanguageUIDs
      *
      * @throws no exception
      * @author Sathis Kannan.P <sathish.kannan@avanzegroup.com>
      * @return String
      * @since 22-09-2020
      * @version D2T New Assignment
      *
    */
    public function getFullDocumentLanguage_Recursive($DocumentLanguageUID)
    {
        if (empty($DocumentLanguageUID)) {
            return "";
        }
        /*echo '<pre>';print_r($DocumentLanguageUID);*/

        $mDocumentLanguages = $this->common_model->get_row('mDocumentLanguages', ['DocumentLanguageUID'=>$DocumentLanguageUID]);
        $MainLanguage = $AdditionalLanguage = "";

        if ( !empty($mDocumentLanguages) ) 
        {   
            $MainLanguage = $mDocumentLanguages->Language;
            $mAdditionalDocumentLanguage = $this->common_model->get('mAdditionalDocumentLanguage', ['DocumentLanguageUID'=>$mDocumentLanguages->DocumentLanguageUID]);

            $this->count++;
            /*echo '<pre>';print_r($mDocumentLanguages);
            echo '<pre>';print_r($mAdditionalDocumentLanguage);
            if ($this->count > 20) {
                exit();
            }*/
            foreach ($mAdditionalDocumentLanguage as $key => $lang) {
                $AdditionalLanguage .= $this->Template_model->getFullDocumentLanguage_Recursive($lang->AdditionalDocumentLanguageUID);
            }
        }

        return $AdditionalLanguage . $MainLanguage;
    }

/**
      *@description Function to getdocumentcategories
      *
      * @param $DocumentLanguageUIDs
      *
      * @throws no exception
      * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
      * @return Array
      * @since 22-09-2020
      * @version D2T New Assignment
      *
    */
    public function getdocumentcategories($DocumentCategoryUIDs)
    {
        if (empty($DocumentCategoryUIDs)) {
            return [];
        }

        $this->db->select('mDocumentCategory.*');
        $this->db->from('mDocumentCategory');
        $this->db->where_in('mDocumentCategory.DocumentCategoryUID', $DocumentCategoryUIDs);
        return $this->db->get()->result();
    }

 /**
      *@description Function to getCategoryLanguageDetails
      *
      * @param $DocumentLanguageUIDs
      *
      * @throws no exception
      * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
      * @return Array
      * @since 22-09-2020
      * @version D2T New Assignment
      *
    */
    public function getCategoryLanguageDetails($DocumentCategoryUIDs)
    {
        if (empty($DocumentCategoryUIDs)) {
            return [];
        }

        $this->db->select('mDocumentLanguages.*, mDocumentCategory.DocumentCategoryName, mDocumentCategory.IsMultiType');
        $this->db->from('mDocumentCategory');
        $this->db->join('mDocumentLanguages', 'mDocumentLanguages.DocumentCategoryUID = mDocumentCategory.DocumentCategoryUID');
        $this->db->where_in('mDocumentLanguages.DocumentCategoryUID', $DocumentCategoryUIDs);
        $this->db->where('mDocumentLanguages.Active', 1);

        return $this->db->get()->result();
    }

 /**
      *@description Function to getLanguageGroups
      *
      * @param $DocumentLanguageUIDs
      *
      * @throws no exception
      * @author Sathis Kannan <sathish.kannan@avanzegroup.com>
      * @return Array
      * @since 22-09-2020
      * @version D2T New Assignment
      *
    */
    public function getLanguageGroups($LanguageGroupUIDs)
    {
        if (empty($LanguageGroupUIDs)) {
            return [];
        }

        $this->db->select('mLanguageGroups.*');
        $this->db->from('mLanguageGroups');
        $this->db->where_in('LanguageGroupUID', $LanguageGroupUIDs);
        return $this->db->get()->result();
    }

    /**
      *@description Function to getGroupLanguageDetails
      *
      * @param $DocumentLanguageUIDs
      *
      * @throws no exception
      * @author Sathis Kannan <sathish.kannan.m@avanzegroup.com>
      * @return Array
      * @since 22-09-2020
      * @version D2T New Assignment
      *
    */
    public function getLanguageGroupDetails($GroupLanguageUIDs)
    {
        if (empty($GroupLanguageUIDs)) {
            return [];
        }

        $this->db->select('mDocumentLanguages.*, mDocumentCategory.DocumentCategoryName, mDocumentCategory.IsMultiType');
        $this->db->from('mDocumentCategory');
        $this->db->join('mDocumentLanguages', 'mDocumentLanguages.DocumentCategoryUID = mDocumentCategory.DocumentCategoryUID');
        $this->db->where_in('LanguageGroupUID', $GroupLanguageUIDs);
        $this->db->where('mDocumentLanguages.Active', 1);

        return $this->db->get()->result();
    }


}
?>
