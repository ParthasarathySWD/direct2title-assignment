<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class City_model extends CI_Model {

	
  function __construct()
    { 
        parent::__construct();
    }
    
     /**
  * @description  Get City Details By Post Value 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
    function GetCityDetails($post)
    {
      $this->_getCitydetail_query($post);
      if ($post['length']!='') {
         $this->db->limit($post['length'], $post['start']);
      }
      $query = $this->db->get();
      return $query->result(); 
	}

/**
  * @description  City Query By Post Value 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
  function _getCitydetail_query($post)
  {
      $this->db->select("*, mCities.Active");
      $this->db->from('mCities');
      $this->db->join('mCounties','mCities.CountyUID=mCounties.CountyUID','left');
      $this->db->join('mStates','mCities.StateUID=mStates.StateUID','left'); 
      if(!empty($post['where'])){
        $this->db->where($post['where']);
      }
        
      // foreach ($post['where_in'] as $index => $value){
      //    $this->db->where_in($index, $value);
      // }
        
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
  * @description  City CoutAll By Post Value 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
  function count_all($post){
    $this->_count_all_city($post);
    $query = $this->db->count_all_results();
    return $query;
  }
  
   /**
  * @description  City Cout By Post Value 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
  public function _count_all_city($post){
    $this->db->from('mCities');
    $this->db->join('mCounties','mCities.CountyUID=mCounties.CountyUID','left');
    $this->db->join('mStates','mCities.StateUID=mStates.StateUID','left');
   /* if($post['where']!='')
    {
      $this->db->where($post['where']);
    }
    foreach ($post['where_in'] as $index => $value){
      $this->db->where_in($index, $value);
    }*/
  }
  
   /**
  * @description  City Filter By Post Value 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
  function count_filtered($post){
    $this->_getCitydetail_query($post);    
    $query = $this->db->get();
    return $query->num_rows();
  }

/**
  * @description Get County Details By CityUID 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
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

/**
  * @description Save City Details  
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */
  function saveCityDetails($PostArray)
	{

    if($PostArray['CityUID']==0)
    {
    $UserLoggin = $this->loggedid = $this->session->userdata('UserUID');

    $Active=1;

		$fieldArray = array(
          "CityUID"=>$PostArray['CityUID'],
          "CityName"=>$PostArray['CityName'],
          "CountyUID"=>$PostArray['CountyUID'],
          "StateUID"=>$PostArray['StateUID'],
          "ZipCode"=>$PostArray['ZipCode'],
					"Active"=>$Active
				);

       $res = $this->db->insert('mCities', $fieldArray);

       if($res)
          $data=array("msg"=>"City are Added Successfully","type"=>"color success");
       else
          $data=array("msg"=>"error","type"=>"error");

          echo json_encode($data);
	}

  else{

     $UserLoggin = $this->loggedid = $this->session->userdata('UserUID');

    $Active=isset($PostArray['Active'])? 1:0;

    $fieldArray = array(
          "CityUID"=>$PostArray['CityUID'],
          "CityName"=>$PostArray['CityName'],
          "CountyUID"=>$PostArray['CountyUID'],
          "StateUID"=>$PostArray['StateUID'],
          "ZipCode"=>$PostArray['ZipCode'],
          "Active"=>$Active
          
        );
    
    $this->db->where(array("CityUID"=>$PostArray['CityUID']));
       $res = $this->db->update('mCities', $fieldArray);
       
       if($res)

            $data=array("msg"=>"City are Updated Successfully","type"=>"color primary");
       else
            $data=array("msg"=>"error","type"=>"error");

          echo json_encode($data);

  }  
}

  /**
  * @description Update City Details  
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 
  function saveCityEditDetails($PostArray)
  {

    $query = $this->db->query("UPDATE mCities set Active = '".$PostArray["Active"]."' WHERE  CityUID=".$PostArray["CityUID"]);

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
  * @description City Details Delete 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 
  function delete_city($data)
  {
    if($this->db->delete('mCities',$data))
    {
      return 1;
    } else {
      return 0;
    }
  }

/**
  * @description City Details Import 
  * @throws no exception
  * @author Sathis Kannan<sathish.kannan@avanzegroup.com>
  * @since  Oct 12th 2020
  */ 
  function CityDetailsImport(){

    $this->db->select("*,mCities.Active");
    $this->db->from('mCities');
    $this->db->join('mCounties','mCities.CountyUID=mCounties.CountyUID','left');
    $this->db->join('mStates','mCities.StateUID=mStates.StateUID','left');
    $query = $this->db->get();
    return $query->result();
  
  }

}
?>
