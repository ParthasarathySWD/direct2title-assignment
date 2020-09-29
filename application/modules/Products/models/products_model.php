<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Products_model extends CI_Model {

	
  function __construct()
    { 
        parent::__construct();
    }


    function ProductCode()
  {

    $query = $this->db->query("SELECT MAX(`ProductUID`) AS `AUTO_INCREMENT` FROM `mproducts`");
    $res = $query->row();
    $id = sprintf("%03d",$res->AUTO_INCREMENT+1);
    $Product="P";
    $auto_number=$Product."".$id;

    return $auto_number;

  }


    
    function GetProductsDetails(){

        $this->db->select("*,mproducts.Active");
        $this->db->from('mproducts');
        $query = $this->db->get();
        return $query->result();
	}

  function GetProductsDetailsById($ProductUID)
  {
    $this->db->select("*,mproducts.Active");
    $this->db->from('mproducts');
    $this->db->where(array("mproducts.ProductUID"=>$ProductUID));
    $query = $this->db->get();
   
    return $query->row();
  }

   function saveProductsDetails($PostArray)
	{

    if($PostArray['ProductUID']==0)
    {
    $UserLoggin = $this->loggedid = $this->session->userdata('UserUID');

    $Active=1;
    $AgentPricing=isset($PostArray['AgentPricing'])? 1:0;
    $UnderWritingPricing=isset($PostArray['UnderWritingPricing'])? 1:0;
    $InsuranceType=isset($PostArray['InsuranceType'])? 1:0;
    $IsDynamicProduct=isset($PostArray['IsDynamicProduct'])? 1:0;
    $IsFloodProduct=isset($PostArray['IsFloodProduct'])? 1:0;
    $IsSelfAssign=isset($PostArray['IsSelfAssign'])? 1:0;
    $AdverseConditionEnable=isset($PostArray['AdverseConditionEnable'])? 1:0;

		$fieldArray = array(
					"ProductUID"=>$PostArray['ProductUID'],
          "ProductName"=>$PostArray['ProductName'],
          "ProductCode"=>$PostArray['ProductCode'],
          "AgentPricing"=>$AgentPricing,
          "UnderWritingPricing"=>$UnderWritingPricing,
          "InsuranceType"=>$InsuranceType,
          "IsDynamicProduct"=>$IsDynamicProduct,
          "IsFloodProduct"=>$IsFloodProduct,
          "IsSelfAssign"=>$IsSelfAssign,
          "AdverseConditionEnable"=>$AdverseConditionEnable,
          "CreatedByUserUID"=>$UserLoggin,
          "CreatedOn"=>date('Y-m-d H:i:s'),
          "ModifiedByUserUID"=>$UserLoggin,
          "ModifiedOn"=>date('Y-m-d H:i:s'),
					"Active"=>$Active
				);

       $res = $this->db->insert('mproducts', $fieldArray);
        $Product_id  = $this->db->insert_id();
        // INSERT AUDIT RAIL
        $InsetData = array(
        'UserUID' => $this->loggedid,
        'ModuleName' => 'ProductLog',
        'Feature' => $Product_id,
        'Content' => htmlentities('Product: <b>'.$PostArray['ProductName'].'</b> Created'),
        'DateTime' => date('Y-m-d H:i:s'));
        $this->common_model->InsertAuditTrail($InsetData);

       if($res)
          $data=array("product_id"=>$Product_id,"msg"=>"Products are Added Successfully","type"=>"color success");
       else
          $data=array("msg"=>"error","type"=>"error");

          echo json_encode($data);
	}

  else{

     $UserLoggin = $this->loggedid = $this->session->userdata('UserUID');

    $Active=isset($PostArray['Active'])? 1:0;
    $AgentPricing=isset($PostArray['AgentPricing'])? 1:0;
    $UnderWritingPricing=isset($PostArray['UnderWritingPricing'])? 1:0;
    $InsuranceType=isset($PostArray['InsuranceType'])? 1:0;
    $IsDynamicProduct=isset($PostArray['IsDynamicProduct'])? 1:0;
    $IsFloodProduct=isset($PostArray['IsFloodProduct'])? 1:0;
    $IsSelfAssign=isset($PostArray['IsSelfAssign'])? 1:0;
    $AdverseConditionEnable=isset($PostArray['AdverseConditionEnable'])? 1:0;

    $fieldArray = array(
          "ProductUID"=>$PostArray['ProductUID'],
          "ProductName"=>$PostArray['ProductName'],
          "ProductCode"=>$PostArray['ProductCode'],
          "AgentPricing"=>$AgentPricing,
          "UnderWritingPricing"=>$UnderWritingPricing,
          "InsuranceType"=>$InsuranceType,
          "IsDynamicProduct"=>$IsDynamicProduct,
          "IsFloodProduct"=>$IsFloodProduct,
          "IsSelfAssign"=>$IsSelfAssign,
          "AdverseConditionEnable"=>$AdverseConditionEnable,
          "CreatedByUserUID"=>$UserLoggin,
          "CreatedOn"=>date('Y-m-d H:i:s'),
          "ModifiedByUserUID"=>$UserLoggin,
          "ModifiedOn"=>date('Y-m-d H:i:s'),
          "Active"=>$Active
          
        );
        $InputsArr = array('ProductCode','ProductName');
        $CheckBoxes = array('IsDynamicProduct','IsFloodProduct','AdverseConditionEnable','Active','InsuranceType','AgentPricing','IsSelfAssign','UnderWritingPricing');
        foreach ($fieldArray as $keyAA => $valueAA) {
          $STRR = '';
          // CHACK WITH PREVIOUS VALUE FOR CHANGE
          $Changed = $this->common_model->CheckAudit(
            'ProductUID',
            $PostArray['ProductUID'],
            'mproducts',
            $keyAA,
            $valueAA
          );
          if($Changed != 'FALSE'){
            if(in_array($keyAA,$CheckBoxes) && (int)$Changed == 1 && $valueAA == 0){
              $STRR .= '<b>'.$keyAA.'</b> Changed from <b>Yes</b> to <b>No</b>';
            }else if(in_array($keyAA,$CheckBoxes) && (int)$Changed == 0 && $valueAA == 1){
                $STRR .= '<b>'.$keyAA.'</b> Changed from <b>No</b> to <b>Yes</b>';
            } 
          }
          if(in_array($keyAA,$InputsArr) && $Changed != 'FALSE'){
            $STRR .= '<b>'.$keyAA.'</b> Changed from <b>'. $Changed .'</b> to <b>'.$valueAA.'</b>';
          }
          if($STRR != ''){
            // INSERT AUDIT RAIL
            $InsetData = array(
            'UserUID' => $this->loggedid,
            'ModuleName' => 'ProductLog',
            'Feature' => $PostArray['ProductUID'],
            'Content' => htmlentities($STRR),
            'DateTime' => date('Y-m-d H:i:s'));
            $this->common_model->InsertAuditTrail($InsetData);
          }
        } 
               
    $this->db->where(array("ProductUID"=>$PostArray['ProductUID']));
       $res = $this->db->update('mproducts', $fieldArray);
  
       if($res)

            $data=array("msg"=>"Products are Updated Successfully","type"=>"color primary");
       else
            $data=array("msg"=>"error","type"=>"error");

          echo json_encode($data);

  }  
}

function saveProductEditDetails($PostArray)
  {
    $data1['ModuleName']='product-update';
    $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
    $data1['DateTime']=date('y-m-d H:i:s');
    $data1['TableName']='mproducts';
    $data1['UserUID']=$this->session->userdata('UserUID');                

    $this->db->select('*');
    $this->db->from('mproducts');
    $this->db->where('ProductUID',$PostArray['ProductUID']);
    $oldvalue=$this->db->get('')->row_array();
    $query = $this->db->query("UPDATE mproducts set " . $PostArray["column"] . " = '".$PostArray["editval"]."' WHERE  ProductUID=".$PostArray["ProductUID"]);
      if($query)
      {
        $res = array("validation_error" => 1,'message' => 'Updated Successfully');
      }
      else
      {
        $res = array("validation_error" => 0,'message' =>'Error');
      }    
      echo json_encode($res);
    $this->db->select('*');
    $this->db->from('mproducts');
    $this->db->where('ProductUID',$PostArray['ProductUID']);
    $newvalue = $this->db->get('')->row_array();
    $this->common_model->Audittrail_diff($newvalue,$oldvalue,$data1);
  }

  function delete_products($Id)
  {
    $query = $this->db->query("DELETE FROM mproducts WHERE ProductUID ='$Id' ");
    if($this->db->affected_rows() > 0)
    {
                  $data1['ModuleName']='products-delete';
                  $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
                  $data1['DateTime']=date('y-m-d H:i:s');
                  $data1['TableName']='mproducts';
                  $data1['UserUID']=$this->session->userdata('UserUID');                
                  $this->common_model->Audittrail_insert($data1);
      return true;
    }
    else
    {
      return false;
    }
  }



  function GetProductName($ProductCode = '')
  {
    $query = $this->db->query("SELECT * FROM `mproducts` 
      WHERE mproducts.ProductCode = '$ProductCode'");

    return $query->row();
  }


   /**
    * @description Get All Audits with referance to Product
    * @param ProductUID
    * @throws no exception
    * @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
    * @return AUditLogs for SubProduct 
    * @since  14/1/2020
    * @version  Task Management
    */ 
    function getproductLogs($ProductUID){
      $this->db->select('*');
      $this->db->from('taudittrail');
      $this->db->join('musers','musers.UserUID = taudittrail.UserUID','left');  
      $this->db->where('Feature',$ProductUID);
      $this->db->where('ModuleName','ProductLog');
      $this->db->order_by('taudittrail.AuditUID','DESC');
      return $this->db->get()->result();
    }


}
?>
