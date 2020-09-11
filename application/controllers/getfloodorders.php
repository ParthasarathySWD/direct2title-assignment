<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Getfloodorders extends CI_Controller {

	function __construct()
	{ 
		parent::__construct();
		$this->load->config('keywords');
		$this->load->model('Common_model');
	}
	
	function index()
	{
	    error_reporting(0);
	    $this->CreateLogsForCRON('CRON Started');  
	    //echo date("d/m/Y H:i:s"). " CRON Started".'<br>';

	    //Get lereta Flood type Order
	    $lereta_query = $this->db->query("  SELECT * FROM torders 
	    	INNER join mcustomers ON mcustomers.CustomerUID = torders.CustomerUID 
	    	INNER join msubproducts ON msubproducts.SubProductUID = torders.SubProductUID 
	    	INNER join mproducts ON mproducts.ProductUID = msubproducts.ProductUID 
	    	INNER join mCustomerApiInfo ON mCustomerApiInfo.CustomerUID=mcustomers.CustomerUID  
	    	AND mCustomerApiInfo.SubProductUID=torders.SubProductUID 
	    	Left join torderpropertyroles ON torderpropertyroles.OrderUID = torders.OrderUID 
	    	Left join morderpriority ON torders.PriorityUID=morderpriority.PriorityUID  
	    	WHERE mCustomerApiInfo.OrderSourceName = 'Lereta' AND torders.StatusUID='0' AND torders.OrderNumber!='' 
	    	GROUP BY torders.OrderUID");
	                 
	    $lereta_query_orders = $lereta_query->result();

	    $NoteType = $this->GetNoteTypeUID('API Note');
	    $SectionUID = $NoteType->SectionUID;

	    foreach ($lereta_query_orders as $key => $value) {
	    	$ClientKey = $value->ClientKey;
	    	$OrderUID = $value->OrderUID;
	    	$OrderSourceName = $value->OrderSourceName;

	    	$this->db->select("*");
	    	$this->db->from('mApiTitlePlatform');
	    	$this->db->where(array("mApiTitlePlatform.OrderSourceName"=>$OrderSourceName));
	    	$query = $this->db->get();
	    	$SourceName = $query->row();

	    	$OrderSourceUID = $SourceName->OrderSourceUID;
	    	$OrderSourceName = $SourceName->OrderSourceName;

	    	if(trim($ClientKey) == ''){
	    		$clientkeynotes = array(
	    			'Note' => 'ClientKey is Missing',
	    			'SectionUID' => $SectionUID,
	    			'OrderUID' => $OrderUID,
	    			'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
	    			'CreatedByAPI' => $OrderSourceUID,
	    			'CreatedOn' => date('Y-m-d H:i:s')
	    		);			  		
	    		$this->db->insert('tordernotes',$clientkeynotes); 
	    	}
	    }

	    //Get Wolter Flood type Order
	    $wolters_query = $this->db->query(" SELECT * FROM torders 
	    	INNER join mcustomers ON mcustomers.CustomerUID = torders.CustomerUID 
	    	INNER join msubproducts ON msubproducts.SubProductUID = torders.SubProductUID 
	    	INNER join mproducts ON mproducts.ProductUID = msubproducts.ProductUID 
	    	INNER join mCustomerApiInfo ON mCustomerApiInfo.CustomerUID=mcustomers.CustomerUID  
	    	AND mCustomerApiInfo.SubProductUID=torders.SubProductUID 
	    	Left join torderpropertyroles ON torderpropertyroles.OrderUID = torders.OrderUID 
	    	Left join morderpriority ON torders.PriorityUID=morderpriority.PriorityUID
	    	WHERE mCustomerApiInfo.OrderSourceName = 'WoltersKluwer' AND torders.StatusUID='0' AND torders.OrderNumber!=''
	    	GROUP BY torders.OrderUID ");
	                 
	    $wolters_query_orders = $wolters_query->result();

	    $url_send = $this->config->item("api_url");
	    
	    if(!empty($wolters_query_orders) && !empty($lereta_query_orders)){

	        // Wolter CURL Function
	        $wolters_query_orders['SourceName'] = 'WoltersKluwer';
	        $str_data = json_encode($wolters_query_orders);
	        $this->sendPostData($url_send, $str_data, 'isgn');

	        // Lereta CURL Function
	        $lereta_query_orders['SourceName'] = 'Lereta';
	        $str_data = json_encode($lereta_query_orders);
	        $this->sendPostData($url_send, $str_data, 'isgn');

	    } else if(!empty($wolters_query_orders) || !empty($lereta_query_orders)){

	      if(!empty($wolters_query_orders)){
	        $wolters_query_orders['SourceName'] = 'WoltersKluwer';
	        $str_data = json_encode($wolters_query_orders);
	        $this->sendPostData($url_send, $str_data, 'isgn');
	      } else {
	        //echo "No Records for WoltersKluwer".'<br>';
	      }

	      if(!empty($lereta_query_orders)){
	        $lereta_query_orders['SourceName'] = 'Lereta';
	        $str_data = json_encode($lereta_query_orders);
	        $this->sendPostData($url_send, $str_data, 'isgn');
	      } else {
	        //echo "No Records for Lereta".'<br>';
	      }
	    } else {
	      //echo "No Records for Lereta and WoltersKluwer".'<br>';
	      $this->CreateLogsForCRON('CRON Stopped');  
	      //echo date("d/m/Y H:i:s"). " CRON Stopped".'<br>';
	    }
	}

	// ( Curl Function ) Send Post Data to API Server Starts//

	function sendPostData($url, $post, $OrgCode){
		$APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
		$Orders = json_decode($post);
		foreach ($Orders as $key => $value) {
			if($key === 'SourceName'){
				$this->db->select("*");
			    $this->db->from('mApiTitlePlatform');
			    $this->db->where(array("mApiTitlePlatform.OrderSourceName"=>trim($value)));
			    $query = $this->db->get();
			    $SourceName = $query->row();

			    $OrderSourceUID = $SourceName->OrderSourceUID;
			    $OrderSourceName = $SourceName->OrderSourceName;
			}
		}

		log_message('error','Status : CRON Stopped', false);

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POST => true,
		  CURLOPT_POSTFIELDS => $post,
		  CURLOPT_HTTPHEADER => array(
		    "authorization: ".$APiAuthKeyDetails->APIAuthKey,
			"cache-control: no-cache",
			"content-type: application/json",
		    ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {

			$NoteType = $this->GetNoteTypeUID('API Note');
	    	$SectionUID = $NoteType->SectionUID;

			//echo $response;
			if($OrderSourceName === 'Lereta'){

				$LeretaResponse = json_decode($response);

				foreach ($LeretaResponse as $key => $value) {
					$OrderNumber = $value->OrderNumber;
					$ProviderOrderNumber = $value->ProviderOrderNumber;
					$Message = $value->Message;
					$Status = $value->StatusCode;
					$Ack = $value->Ack;

					$OrderDetails = $this->GetOrderUIDFromOrderNumber($OrderNumber);
					$OrderUID = $OrderDetails->OrderUID;

					if($Message === 'Send'){

						$insert_notes = array(
											'SectionUID' => $SectionUID,
											'OrderUID' => $OrderUID,
											'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
											'CreatedByAPI' => $OrderSourceUID,
											'CreatedOn' => date('Y-m-d H:i:s')
											);			  		

				    	if($Status === 'STATUS' || $Status === 'OrderPlaced'){

				    		$insert_notes['Note'] = $OrderNumber .' is Send to Flood API ( '.$OrderSourceName.' )';	

					    	$this->db->where('OrderNumber',$OrderNumber);
					  		$this->db->where('StatusUID',0);
					  		$this->db->update('torders',array('StatusUID'=>'11','IsFloodOrder'=>'1'));
					  		$this->insert_flood_order($OrderUID,$ProviderOrderNumber,'Lereta');	
						} else {

							$insert_notes['Note'] = $OrderNumber .' is Send to Flood API ( '.$OrderSourceName.' )' . 'But failed to respond';	
						}

						$result = $this->db->insert("tordernotes", $insert_notes);
					}
				}

			} else {

				$WKResponse = json_decode($response);

				foreach ($WKResponse as $key => $value) {
					$OrderNumber = $value->OrderNumber;
					$ProviderOrderNumber = $value->ProviderOrderNumber;
					$Message = $value->Message;
					$StatusCode = $value->StatusCode;
					$Ack = $value->Ack;

					$OrderDetails = $this->GetOrderUIDFromOrderNumber($OrderNumber);
					$OrderUID = $OrderDetails->OrderUID;

					if($Message === 'Send'){

						$this->db->where('OrderNumber',$OrderNumber);
						$this->db->where('StatusUID',0);
						$this->db->update('torders',array('StatusUID'=>'11','IsFloodOrder'=>'1'));

						$insert_notes = array(
							'Note' => $OrderNumber .' is Send to Flood API ( '.$OrderSourceName.' )',
							'SectionUID' => $SectionUID,
							'OrderUID' => $OrderUID,
							'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
							'CreatedByAPI' => $OrderSourceUID,
							'CreatedOn' => date('Y-m-d H:i:s')
						);			  		
						$result = $this->db->insert("tordernotes", $insert_notes);
						$this->insert_flood_order($OrderUID,$ProviderOrderNumber,'WoltersKluwer');	
					}
				}
			}
		}

		$this->CreateLogsForCRON('CRON Stopped');  
	}

	// ( Curl Function ) Send Post Data to API Server Ends//


	function insert_flood_order($OrderUID,$ProviderOrderNumber,$Integration){

      $OrderDetails = $this->common_model->get_orderdetails($OrderUID);

      $this->db->select("*");
      $this->db->from('mApiTitlePlatform');
      $this->db->where(array("mApiTitlePlatform.OrderSourceName"=>trim($Integration)));
      $query = $this->db->get();
      $SourceName = $query->row();

      $Api_Order = array(
        'OrderUID'=>$OrderUID,
        'OrderNumber'=>$OrderDetails->OrderNumber,
        'OrderSourceUID'=>$SourceName->OrderSourceUID,
        'ProviderOrderNumber'=>$ProviderOrderNumber,
        'Status'=>'Accepted',
        'CreatedDateTime'=>Date('Y-m-d H:i:s',strtotime("now")),
        'CreatedBy'=> $this->session->userdata('UserUID'),
        'AcceptedDateTime'=>Date('Y-m-d H:i:s',strtotime("now")),
        'AcceptedBy'=> $SourceName->OrderSourceUID,
      );

      $this->db->insert('tApiOutBoundOrders',$Api_Order); 
      $ApiOutBoundOrderUID = $this->db->insert_id();
      return $ApiOutBoundOrderUID;
    }


	function GetNoteTypeUID($SectionName)
	{
		$this->db->select("*");
		$this->db->from('mreportsections');
		$this->db->where(array("mreportsections.SectionName"=>$SectionName));
		$query = $this->db->get();
		return $query->row();
	}

	function GetOrderUIDFromOrderNumber($OrderNumber)
	{
		$this->db->select("*");
		$this->db->from('torders');
		$this->db->where(array("torders.OrderNumber"=>$OrderNumber));
		$query = $this->db->get();
		return $query->row();
	}

	function CreateLogsForCRON($Message){

	    $LOGFolder = $this->config->item("LOGFolder");

	    $FileName = $LOGFolder.'cron_logs-'.date('d-m-Y').'.log';

	    if(!file_exists($LOGFolder))
	    {
	      if(!mkdir($LOGFolder, 0777, true)){die('Unable to Create Folder');}
	    }

	    $Cron_Msg = 'Timestamp : ' .date('d-m-Y H:i:s') .' '.$Message. PHP_EOL;
	    file_put_contents($FileName, $Cron_Msg, FILE_APPEND);

	}

}
?>
