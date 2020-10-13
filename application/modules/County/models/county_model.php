<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class County_model extends CI_Model {

	
  function __construct()
    { 
        parent::__construct();
    }
    
    /**
  * @description  Get County Details By Post Value 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
  function GetCountyDetails($post)
  { 
    $this->_getCountydetail_query($post);
    if ($post['length']!='') {
       $this->db->limit($post['length'], $post['start']);
    }
    $query = $this->db->get();
    return $query->result();     
	}

/**
  * @description  County Query By Post Value 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
  function _getCountydetail_query($post)
  {
      $this->db->select("*,mCounties.Active");
      $this->db->from('mCounties');
      $this->db->join('mStates','mCounties.StateUID=mStates.StateUID','left');
      /*if(!empty($post['where'])){
        $this->db->where($post['where']);
      }
        
      foreach ($post['where_in'] as $index => $value){
         $this->db->where_in($index, $value);
      }*/
        
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
      { // here order processing 
        if($post['column_order'][$post['order'][0]['column']]!='')
          {
            $this->db->order_by($post['column_order'][$post['order'][0]['column']], $post['order'][0]['dir']);    
          }    
      } else if (isset($this->order)) {
        $order = $this->order;
        $this->db->order_by(key($order), $order[key($order)]);  
      }
  }

/**
  * @description  County CoutAll By Post Value 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
  function count_all($post){
    $this->_count_all_County($post);
    $query = $this->db->count_all_results();
    return $query;
  }
  
  /**
  * @description  County Cout By Post Value 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
  public function _count_all_County($post)
  {
    $this->db->select("*,mCounties.Active");
    $this->db->from('mCounties');
    $this->db->join('mStates','mCounties.StateUID=mStates.StateUID','left');
    /*if($post['where']!='')
    {
      $this->db->where($post['where']);
    }
    foreach ($post['where_in'] as $index => $value){
      $this->db->where_in($index, $value);
    }*/
  }
  /**
  * @description  County Filter By Post Value 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
  function count_filtered($post){
    $this->_getCountydetail_query($post);    
    $query = $this->db->get();
    return $query->num_rows();
  }
/**
  * @description Get County Details By CountyUID 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */

  function GetCountyDetailsById($CountyUID)
  {
    $this->db->select("*,mCounties.Active");
    $this->db->from('mCounties');
    $this->db->join('mStates','mCounties.StateUID=mStates.StateUID','left');
    $this->db->where(array("mCounties.CountyUID"=>$CountyUID));
    $query = $this->db->get();
    return $query->row();
  }

/**
  * @description Save County Details  
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
  function saveCountyDetails($PostArray)
	{

    if($PostArray['CountyUID']==0)
    {
    $UserLoggin = $this->loggedid = $this->session->userdata('UserUID');

    $Active=1;

		$fieldArray = array(
          "CountyUID"=>$PostArray['CountyUID'],
          "CountyName"=>$PostArray['CountyName'],
          "CountyCode"=>$PostArray['CountyCode'],
          "StateUID"=>$PostArray['StateUID'],
          "TimeZone"=>$PostArray['TimeZone'],
					"Active"=>$Active
				);

       $res = $this->db->insert('mCounties', $fieldArray);

       if($res)
          $data=array("msg"=>"County are Added Successfully","type"=>"color success");
       else
          $data=array("msg"=>"error","type"=>"error");

          echo json_encode($data);
	}

  else{

     $UserLoggin = $this->loggedid = $this->session->userdata('UserUID');

    $Active=isset($PostArray['Active'])? 1:0;

    $fieldArray = array(
          "CountyUID"=>$PostArray['CountyUID'],
          "CountyName"=>$PostArray['CountyName'],
          "CountyCode"=>$PostArray['CountyCode'],
          "StateUID"=>$PostArray['StateUID'],
          "TimeZone"=>$PostArray['TimeZone'],
          "Active"=>$Active
          
        );
    
    $this->db->where(array("CountyUID"=>$PostArray['CountyUID']));
       $res = $this->db->update('mCounties', $fieldArray);
       
       if($res)

            $data=array("msg"=>"County are Updated Successfully","type"=>"color primary");
       else
            $data=array("msg"=>"error","type"=>"error");

          echo json_encode($data);

  }  
}
/**
  * @description Update County Details  
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 
function saveCountyEditDetails($PostArray)
  {

    $query = $this->db->query("UPDATE mCounties set Active ='".$PostArray["Active"]."' WHERE  CountyUID=".$PostArray["CountyUID"]);
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
  * @description County Details Delete 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 
  function delete_county($data)
  {
    if($this->db->delete('mCounties',$data))
    {
      return 1;
    } else {
      return 0;
    }
  }
  /**
  * @description County Details Import 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 
  function CountyDetailsImport(){
    $this->db->select("*,mCounties.Active");
    $this->db->from('mCounties');
    $this->db->join('mStates','mCounties.StateUID=mStates.StateUID','left');
    $query = $this->db->get();
    return $query->result();
  }

}
?>
