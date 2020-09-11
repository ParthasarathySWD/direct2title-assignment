<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Api_to_d2t extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->lang->load('keywords');
		$this->load->model('api/api_model');
		$this->load->model('orderentry/Orderentry_model');
		$this->load->helper('tatcalculate');
		$this->load->helper('customer_pricing');
	}

	function index(){

		// error_reporting(E_ALL);
		$output_xml = file_get_contents('php://input');

		$fieldArray = json_decode($output_xml, true);		

		$TransactionID = $fieldArray['TransactionID'];
		$StatusUID = $fieldArray['StatusUID'];
		$OrderUID = $fieldArray['OrderUID'];


		if($StatusUID == 230){

			$array = array('WorkflowStatus' => '4');
			$WorkflowModuleUID = array('1','2','3');

	        $this->db->where(array("OrderUID"=>$OrderUID));
	        $this->db->where_in("WorkflowModuleUID",$WorkflowModuleUID);
	        $result = $this->db->update('torderassignment',$array);


        	$insert_notes = array(
        						'Note' => 'Order Put OnHold by API',
        						'SectionUID' => 26,
        						'OrderUID' => $OrderUID,
        						'RoleType' => '1,2,3,4,5,6,7,9,11,12',
        						'CreatedOn' => date('Y-m-d H:i:s')
        					);

        	$result = $this->db->insert("tordernotes", $insert_notes);

		}
		else if($StatusUID == 260){

			$array = array('WorkflowStatus' => '3');
			$WorkflowModuleUID = array('1','2','3');

	        $this->db->where(array("OrderUID"=>$OrderUID));
	        $this->db->where_in("WorkflowModuleUID",$WorkflowModuleUID);
	        $result = $this->db->update('torderassignment',$array);

        	$insert_notes = array(
        						'Note' => 'Order Released by API',
        						'SectionUID' => 26,
        						'OrderUID' => $OrderUID,
        						'RoleType' => '1,2,3,4,5,6,7,9,11,12',
        						'CreatedOn' => date('Y-m-d H:i:s')
        					);

        	$result = $this->db->insert("tordernotes", $insert_notes);
		}
		else if($StatusUID == 222){

			$Comment = $fieldArray['Comment'];

        	$insert_notes = array(
        						'Note' => $Comment,
        						'SectionUID' => 26,
        						'OrderUID' => $OrderUID,
        						'RoleType' => '1,2,3,4,5,6,7,9,11,12',
        						'CreatedOn' => date('Y-m-d H:i:s')
        					);

        	$result = $this->db->insert("tordernotes", $insert_notes);

		}
		else if($StatusUID == 220){

			$Comment = $fieldArray['Comment'];

        	$insert_notes = array(
        						'Note' => $Comment,
        						'SectionUID' => 26,
        						'OrderUID' => $OrderUID,
        						'RoleType' => '1,2,3,4,5,6,7,9,11,12',
        						'CreatedOn' => date('Y-m-d H:i:s')
        					);

        	$result = $this->db->insert("tordernotes", $insert_notes);

		}
		else if($StatusUID == 240){

			$array = array('StatusUID' => 110 );
	        $this->db->where(array("torders.OrderUID"=>$OrderUID));
	        $result = $this->db->update('torders',$array);

	        $canceldata['OrderUID']=$OrderUID;
	        $canceldata['Remarks']='Order Cancelled by API';
	        $canceldata['RequestedBy']=0;
	        $canceldata['ApprovedBy']=0;
	        $canceldata['CancelStatus']=1;
	        $canceldata['CancellationRequestTime']=date('Y-m-d H:i:s');
	        $canceldata['CancellationApproveDeclineTime']=date('Y-m-d H:i:s');

	        $this->db->insert('tordercancel', $canceldata);

          	$notes_data = array('OrderUID'=>$OrderUID,'SectionUID'=>26,'Roletype'=>'1,2,3,4,5,6,7,9,11,12','CreatedOn'=> Date('Y-m-d H:i:s',strtotime("now")),'CreatedByUserUID'=>0,'Note'=>'Order Cancelled by API');
			$this->db->insert('tordernotes',$notes_data);


		}
		else if($StatusUID == 100){

		    $POSTArray=(array)$fieldArray;
		    unset($POSTArray['StatusUID']);
		    $ArrData['AutoAccept']=$POSTArray['AutoAccept'];

		    unset($POSTArray['AutoAccept']);

		    $result=$this->db->insert('tApiOrders', $POSTArray);
		    $OrderRequestUID=$this->db->insert_id();

		    if ($result) {
		      if ($ArrData['AutoAccept']==1) {
		        $response=$this->orderentry($OrderRequestUID);
		        if ($response['status']==1) {
		          $response['status']='success';
		          $response['SendtoOrganization']=1;
		          $Details['OrderNumber']=$response['OrderNumber'];
		          $Details['OrderUID']=$response['OrderUID'];
                  $this->api_model->UpdatetApiOrders($OrderRequestUID, $Details);


		          echo json_encode($response);
		        }
		        else {
		          $response['status']='failed';
		          $response['SendtoOrganization']=0;
		          $Details['TransactionID']=$response['TransactionID'];

		          echo json_encode($response);
		        }
		      }
		      else
		      {
		        echo json_encode( array('status' => 'success', 'SendtoOrganization'=>1));
		        
		      }
		    }
		    else{
		      echo json_encode( array('status' => 'failed', 'SendtoOrganization'=>0));
		    }

		}		
		else{
			echo json_encode( array('status' => 'failed', 'message'=>'Undefined EventCode'));
		}

	}

	public function orderentry($OrderRequestUID)
	{

		$tOrderRequest=$this->api_model->Get_tOrderRequestByOrderRequestUID($OrderRequestUID);
		$mcustomers=$this->api_model->getCustomerByCustomerName($tOrderRequest->CustomerName);
		$ProductUID = $tOrderRequest->ProductCode;

		if(!isset($mcustomers->CustomerUID))
		{
			return array('status'=>0, 'msg'=>'Invalid Customer'); exit;
		}

		if($ProductUID=='' )
		{
			return array('status'=>0, 'msg'=>'Invalid Product Code'); exit;
		}

		$CustomerUID=$mcustomers->CustomerUID;
		$error=0;

		$arrayCode = array();

		$SubProductUID = '';


		$row['SubProductName']='Property Report';
		$row['LoanNumber']=$tOrderRequest->LoanNumber;
		$row['LoanAmount']=$tOrderRequest->LoanAmount;
		$row['PropertyAddress1']=$tOrderRequest->PropertyAddress1;
		$row['PropertyCityName']=$tOrderRequest->PropertyCityName;
		$row['PropertyCountyName']=$tOrderRequest->PropertyCountyName;
		$row['PropertyStateCode']=$tOrderRequest->PropertyStateCode;
		$row['PropertyZipcode']=$tOrderRequest->PropertyZipcode;
		$row['APN']='';
		$row['AdditionalInfo']='';
		$row['Template']='';
		$row['EmailReportTo']=$tOrderRequest->EmailReportTo;
		$row['AttentionName']=$tOrderRequest->AttentionName;
		$row['BorrowerName1']=$tOrderRequest->BorrowerName1;
		$row['Email1']=$tOrderRequest->Email1;
		$row['HomeNumber1']=$tOrderRequest->HomeNumber1;
		$row['WorkNumber1']=$tOrderRequest->WorkNumber1;
		$row['CellNo1']='';
		$row['SocialNo1']='';
		$row['BorrowerName2']=$tOrderRequest->BorrowerName2;
		$row['Email2']=$tOrderRequest->Email2;
		$row['HomeNumber2']=$tOrderRequest->HomeNumber2;
		$row['WorkNumber2']=$tOrderRequest->WorkNumber2;
		$row['CellNo2']='';
		$row['SocialNo2']='';

		// if($CustomerUID =='' || $ProductUID ==''){
		//   echo json_encode(array('error'=>'1','message'=>'Select the Required Fields'));exit;
		// }



		foreach( $row as $field )
		{
			//to clean up $ sign
			$field = trim( $field, "$ ");
			$arrayCode[0][0] = '';
			$arrayCode[0][1] = '';
			$arrayCode[0][2] = '';
			$arrayCode[0][3] = '';
			$arrayCode[0][] = $field;
		}

	      
	      $SubProduct_check = [];

	      // print_r($arrayCode); exit;

	      foreach ($arrayCode as $i => $v) {

	        $SubProductUID = $this->input->post('SubProductUID');

	        $SubProduct_check[$i] = False;

	        $msubproducts = array();

	        $mcustomerproducts[$i] = array();

	        if($v[4] == '' && $SubProductUID == '')
	        {
	          $default_subproducts = $this->common_model->get_defaultsubproduct($CustomerUID);
	          $msubproductt = explode(",", $default_subproducts->DefaultProductSubValue);
	          if(count($msubproductt)== 1){
	            $msubproducts = $this->common_model->getsubproductbyUID($msubproductt[0]);
	            if(count($msubproducts) == 0){
	              $SubProduct_check[$i] = false;
	            }else{
	              $SubProductUID = $msubproducts->SubProductUID;
	            }
	          }else{
	            $SubProduct_check[$i] = false;
	          }

	        }elseif($v[4] !=''){

	          $msubproducts = $this->Orderentry_model->get_sub_product($v[4]);
	          if(!empty($msubproducts)){
	            $SubProductUID = $msubproducts->SubProductUID;
	            $SubProduct_check[$i] = true;
	          }else{
	            $SubProduct_check[$i] = false;
	          }
	          // print_r($msubproducts); exit;

	        }elseif ($SubProductUID !='') {
	          $msubproducts = $this->common_model->getsubproductbyUID($SubProductUID);

	          if(count($msubproducts) >0){
	            $SubProductUID = $msubproducts->SubProductUID;
	            $SubProduct_check[$i] = true;
	          }else{
	            $SubProduct_check[$i] = false;
	          }
	        }


	        if(!empty($msubproducts) ){

	          $mcustomerproducts[$i] = $this->Orderentry_model->get_all_in_customerproduct($CustomerUID,$ProductUID,$SubProductUID);

	          if(!empty($mcustomerproducts[$i]) ){

	            $SubProduct_check[$i] = true;
	            $arrayCode[$i][0] = $mcustomerproducts[$i]->OrderTypeName;
	            $arrayCode[$i][1] = $mcustomerproducts[$i]->PriorityName;
	            $arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
	            $arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
	            $arrayCode[$i][4] = $mcustomerproducts[$i]->SubProductName;
	          }else{
	            $SubProduct_check[$i] = false;
	            $mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
	            $arrayCode[$i][0] = '';
	            $arrayCode[$i][1] = '';
	            $arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
	            $arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
	            $arrayCode[$i][4] = $msubproducts->SubProductName;
	          }
	        }else{
	          $SubProduct_check[$i] = false;
	          $mcustomerproducts[$i] = $this->Orderentry_model->get_customer_product($CustomerUID,$ProductUID);
	          $arrayCode[$i][0] = '';
	          $arrayCode[$i][1] = '';
	          $arrayCode[$i][2] = $mcustomerproducts[$i]->CustomerName;
	          $arrayCode[$i][3] = $mcustomerproducts[$i]->ProductName;
	          $arrayCode[$i][4] = '';

	        } 
	      }

	      // print_r($arrayCode);exit;
	      foreach($arrayCode as $i => $a)
	      { 

	        $count = count($a);
	        $field_count = 17;


	              $CityName = $a[8];

	              $CountyName = $a[9];
	              $StateCode = $a[10];
	              $Zipcode = $a[11];

	              $TemplateName = $a[14];   



	              $template = $this->Orderentry_model->get_template($TemplateName);



	              if($TemplateName == ''){
	                $default_template = $this->common_model->get_defaulttemplate_bycustomerUID($mcustomerproducts[$i]->CustomerUID);
	                if(!empty($default_template) ){
	                  $template = $default_template;
	                }
	              }


	              /*$mstates = $this->Orderentry_model->get_state($StateName);
	              $mcounties = [];
	                          if($StateCode > 0){
	                              $mcounties = $this->Orderentry_model->get_county($StateCode,$CountyName);
	                          }

	              $mcities = $this->Orderentry_model->get_city($CityName,$a[11]);*/

	              if($SubProduct_check[$i] == False){ 
	                return array('status'=>0, 'msg'=>'SubProduct Cannot be Null ');exit;

	              }elseif ($a[17] == ''){

	                return array('status'=>0, 'msg'=>'Borrower Cannot be Null ');exit;

	              }elseif ($StateCode == '') {

	                return array('status'=>0, 'msg'=>'StateCode Cannot be Null ');exit;


	              }elseif ($CountyName == '') {

	                return array('status'=>0, 'msg'=>'CountyName Cannot be Null ');exit;

	              }elseif ($CityName == '') {

	                return array('status'=>0, 'msg'=>'CityName Cannot be Null ');exit;


	              }elseif ($Zipcode == '') {

	                return array('status'=>0, 'msg'=>'Zipcode Cannot be Null ');exit;


	              }else{

	                $data['OrderTypeUID'] = $mcustomerproducts[$i]->OrderTypeUID;
	                $data['PriorityUID'] = $mcustomerproducts[$i]->PriorityUID;
	                $data['CustomerUID'] = $mcustomerproducts[$i]->CustomerUID;
	                $data['SubProductUID'] = $mcustomerproducts[$i]->SubProductUID;
	                $data['OrderNumber'] = '';
	                $data['LoanNumber'] = $a[5];
	                $data['LoanAmount'] = $a[6];
	                $data['PropertyAddress1'] = $a[7];
	                $data['PropertyZipcode'] = $a[11];
	                $data['APN'] = $a[12];
	                $data['AdditionalInfo'] = $a[13];
	                $data['EmailReportTo'] = $a[15];
	                $data['PropertyStateCode'] = $StateCode;
	                $data['PropertyCityName'] = $CityName;
	                $data['PropertyCountyName'] = $CountyName;
	                $data['AttentionName'] = $a[16];
	                $data['APIOrder']=1;


	                if(count($template) > 0){

	                  $data['TemplateUID'] = $template->TemplateUID;
	                }else{
	                  $data['TemplateUID'] = null;
	                }

	                /*********
	                If EmailReportTo fields is empty then CustomerPContactEmailID will get updated
	                **********/

	                if($a[15] == '')
	                {
	                  $data['EmailReportTo'] = $mcustomerproducts[$i]->CustomerPContactEmailID;
	                }
	                else
	                {
	                  $data['EmailReportTo'] = $a[15];
	                } 

	                /**************/    

	                $result = $this->api_model->savebulkentry_order($data,$a);
	                if ($result) {
	                  $Status='Accepted';
	                  $this->api_model->changetOrderRequestStatus($OrderRequestUID, $Status);

	                  return array('status'=>1, 'msg'=>'Order '.$result['OrderNumber'].' Placed Successfully', 'OrderNumber'=>$result['OrderNumber'], 'OrderUID'=>$result['OrderUID']);

	                }
	                else
	                {
	                  return array('status'=>0, 'msg'=>'Unable to Process Order ');
	                }

	              }
	          
	      }

	}

}
?>
