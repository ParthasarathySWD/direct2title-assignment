<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class State_model extends CI_Model {

	
  function __construct()
    { 
        parent::__construct();
    }
    
    /**
  * @description Get State Details
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 10th 2020
  */ 
    function GetStateDetails(){

        $this->db->select("*,mStates.Active");
        $this->db->from('mStates');
        $this->db->Order_by('mStates.StateName ASC');
        $query = $this->db->get();
        return $query->result();
	}

  /**
  * @description Get State Details By StateUID
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 
  function GetStateDetailsById($StateUID)
  {
    $this->db->select("*,mStates.Active");
    $this->db->from('mStates');
    $this->db->where(array("mStates.StateUID"=>$StateUID));
    $query = $this->db->get();
    return $query->row();
  }


  /**
  * @description Save State Details 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 

function saveOrderTypeEditDetails($PostArray)
  {
    $query = $this->db->query("UPDATE mStates set Active = '".$PostArray["editval"]."' WHERE  StateUID=".$PostArray["StateUID"]);
    if($query)
    {
      $res = array("validation_error" => 1,'message' => 'Updated Successfully');
    }
    else
    {
      $res = array("validation_error" => 0,'message' =>'Error');
    }
      echo json_encode($res);        
  }

   /**
  * @description Save State Details 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 

  function SaveStateInfo($PostArray)
  {
    $Active=1;
    $fieldArray = array(
      "StateName"=>$PostArray['StateName'],
      "StateCode"=>$PostArray['StateCode'],
      "FIPSCode"=>$PostArray['FIPSCode'],
      "Active"=>$Active
    );
    $res = $this->db->insert('mStates', $fieldArray);
    $data1['ModuleName']='state_add';
    $data1['Content'] = 'state information added';
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['DateTime']=date('y-m-d H:i:s');
    $data1['TableName']='mStates';
    $data1['UserUID']=$this->session->userdata('UserUID');                
    $this->common_model->Audittrail_insert($data1);

    if($res){
      $data=array("msg"=>"State are Added Successfully","type"=>"color success");
    }
    else{
      $data=array("msg"=>"error","type"=>"error");
    }
    echo json_encode($data);
  }

 /**
  * @description Update State Details 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 

  function UpdateStateInfo($PostArray)
  {
    $Active=isset($PostArray['Active'])? 1:0;

    $fieldArray = array(
      "StateUID"=>$PostArray['StateUID'],
      "StateName"=>$PostArray['StateName'],
      "StateCode"=>$PostArray['StateCode'],
      "FIPSCode"=>$PostArray['FIPSCode'],
      "Active"=>$Active
    );

    $data1['ModuleName']='state-update';
    $data1['Content'] = 'state information Updated';
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['DateTime']=date('y-m-d H:i:s');
    $data1['TableName']='mStates';
    $data1['UserUID']=$this->session->userdata('UserUID');                

    $this->db->select('*');
    $this->db->from('mStates');
    $this->db->where('StateUID',$PostArray['StateUID']);
    $oldvalue=$this->db->get('')->row_array();

    $this->db->where(array("StateUID"=>$PostArray['StateUID']));
    $res = $this->db->update('mStates', $fieldArray);

    $this->db->select('*');
    $this->db->from('mStates');
    $this->db->where('StateUID',$PostArray['StateUID']);
    $newvalue = $this->db->get('')->row_array();
    $this->common_model->Audittrail_diff($newvalue,$oldvalue,$data1);

    if($res)

      $data=array("msg"=>"State Updated Successfully","type"=>"color primary");

    else
      $data=array("msg"=>"error","type"=>"error");

    echo json_encode($data);
  }

   /**
  * @description AuditLog 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 

  public function StateAuditlog()
  {
    $this->db->where('ModuleName',"state-update");
    // $this->db->where('ModuleName',"state-add");
    $query = $this->db->get('tAuditTrail');
    return $query->result();
  }

   /**
  * @description Delete State Details 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 
  function delete_state($data)
  {
    if($this->db->delete('mStates',$data))
    {
                  $data1['ModuleName']='state-delete';
                  $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
                  $data1['DateTime']=date('y-m-d H:i:s');
                  $data1['TableName']='mStates';
                  $data1['UserUID']=$this->session->userdata('UserUID');                
                  $this->common_model->Audittrail_insert($data1);
      return 1;
    } else {
      return 0;
    }
  } 

}


?>
