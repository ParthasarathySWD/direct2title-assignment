<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Api_common_model extends CI_Model {
	
	function __construct()
	{ 
		parent::__construct();
		$this->load->config('keywords');
		$this->load->model('Common_model');
	}
	

	// ( Curl Function ) Send Post Data to API Server Starts//

		function sendPostData($url, $post){
			$APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
			$Orders = json_decode($post);
			$OrderSourceName = $Orders->SourceName;
			$OrderSourceUID = $Orders->OrderSourceUID;
			$OrderUID = $Orders->OrderUID;

			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 3000,
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
				//echo "cURL Error #:" . $err;
			} else {

				$SuccessMsg = explode('$', $response);
				$API = $SuccessMsg[0];
				$Action = trim($SuccessMsg[1]);
				$InBoundUID = trim($SuccessMsg[2]);
				$TransactionNO = trim($SuccessMsg[3]);
				$ResponseResult = trim($SuccessMsg[4]);
				/* NF281 Avanze as Abstractor API Integration */
				if($API === 'Pabs' || $API === 'Avanze'){
					if($Action === 'AT04'){
						$Notes = 'AT04 (Documents)';
						$Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$ResponseResult);
					} else if($Action === 'AT05'){
						$Notes = 'AT05 (Comment)';
						$Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$ResponseResult);
					} else if($Action === 'AT06'){
						$Notes = 'AT06 (On-Hold)';
						$Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$ResponseResult);
					} else if($Action === 'AT07'){
						$Notes = 'AT07 (Cancel)';
						$Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$ResponseResult);
					} else if($Action === 'AT08'){
						$Notes = 'AT08 (Resume)';
						$Result = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$ResponseResult);
					} 
				}
			}
		}

	// ( Curl Function ) Send Post Data to API Server Ends//

	// Comment Api Order Function Starts //

		function CommentApiOrder($OrderUID,$Comment){

			$tApiOutBoundOrders = $this->GetApiOutBoundOrders($OrderUID,'AT05');
				
			if($tApiOutBoundOrders){

				$ApiOutBoundOrderUID = $tApiOutBoundOrders->ApiOutBoundOrderUID;
				$OrderSourceUID = $tApiOutBoundOrders->OrderSourceUID;

		    	$AppSourceName = $this->GetSourceName($OrderSourceUID); 
	            $SourceName = trim($AppSourceName->OrderSourceName);

				$OrderDetails = $this->GetOrderDetails($OrderUID);

		    	if($SourceName=="Pabs" || $SourceName=="Avanze") {
			      $Action = 'AT05';
			    } 

			    $url_send = $this->config->item("api_url");

			    $data = array(
			      'EventCode' => $Action,
			      'OrderUID' => $OrderUID,
			      'OrderDetails' => $OrderDetails,
			      'Comment' => $Comment,
			      'OrderSourceUID'=> $OrderSourceUID,
			      'SourceName'=> $SourceName
			    );

				//$str_data = json_encode($data);
			    $str_data = json_encode($data);

			    $result = $this->sendPostData($url_send, $str_data);
			}
		}

		function CancelApiOrder($OrderUID,$Comment){

			$tApiOutBoundOrders = $this->GetApiOutBoundOrders($OrderUID,'AT07');

			if($tApiOutBoundOrders){

				$ApiOutBoundOrderUID = $tApiOutBoundOrders->ApiOutBoundOrderUID;
				$OrderSourceUID = $tApiOutBoundOrders->OrderSourceUID;

		    	$AppSourceName = $this->GetSourceName($OrderSourceUID); 
	            $SourceName = trim($AppSourceName->OrderSourceName);

				$OrderDetails = $this->GetOrderDetails($OrderUID);

		    	if($SourceName=="Pabs" || $SourceName=="Avanze") {
			      $Action = 'AT07';
			    } 

			    $url_send = $this->config->item("api_url");

			    $data = array(
			      'EventCode' => $Action,
			      'OrderUID' => $OrderUID,
			      'OrderDetails' => $OrderDetails,
			      'Comment' => $Comment,
			      'OrderSourceUID'=> $OrderSourceUID,
			      'SourceName'=> $SourceName
			    );

				//$str_data = json_encode($data);
			    $str_data = json_encode($data);

			    $this->db->where(array('ApiOutBoundOrderUID' => $ApiOutBoundOrderUID ));
			    $this->db->update('tApiOutBoundOrders', array('Status'=>'Cancelled'));
			    
			    $result = $this->sendPostData($url_send, $str_data);
			}
		}

		function OnHoldApiOrder($OrderUID,$Comment){

			$tApiOutBoundOrders = $this->GetApiOutBoundOrders($OrderUID,'AT06');

			if($tApiOutBoundOrders){

				$ApiOutBoundOrderUID = $tApiOutBoundOrders->ApiOutBoundOrderUID;
				$OrderSourceUID = $tApiOutBoundOrders->OrderSourceUID;

		    	$AppSourceName = $this->GetSourceName($OrderSourceUID); 
	            $SourceName = trim($AppSourceName->OrderSourceName);

				$OrderDetails = $this->GetOrderDetails($OrderUID);

		    	if($SourceName=="Pabs" || $SourceName=="Avanze") {
			      $Action = 'AT06';
			    } 

			    $url_send = $this->config->item("api_url");

			    $data = array(
			      'EventCode' => $Action,
			      'OrderUID' => $OrderUID,
			      'OrderDetails' => $OrderDetails,
			      'Comment' => $Comment,
			      'OrderSourceUID'=> $OrderSourceUID,
			      'SourceName'=> $SourceName
			    );

				//$str_data = json_encode($data);
			    $str_data = json_encode($data);

			    $result = $this->sendPostData($url_send, $str_data);
			}
		}

		function ResumeOnHoldApiOrder($OrderUID){

			$tApiOutBoundOrders = $this->GetApiOutBoundOrders($OrderUID,'AT08');

			if($tApiOutBoundOrders){

				$ApiOutBoundOrderUID = $tApiOutBoundOrders->ApiOutBoundOrderUID;
				$OrderSourceUID = $tApiOutBoundOrders->OrderSourceUID;

		    	$AppSourceName = $this->GetSourceName($OrderSourceUID); 
	            $SourceName = trim($AppSourceName->OrderSourceName);

				$OrderDetails = $this->GetOrderDetails($OrderUID);

		    	if($SourceName=="Pabs" || $SourceName=="Avanze") {
			      $Action = 'AT08';
			    } 

			    $Comment = 'Service Resumed';

			    $url_send = $this->config->item("api_url");

			    $data = array(
			      'EventCode' => $Action,
			      'OrderUID' => $OrderUID,
			      'OrderDetails' => $OrderDetails,
			      'Comment' => $Comment,
			      'OrderSourceUID'=> $OrderSourceUID,
			      'SourceName'=> $SourceName
			    );

				//$str_data = json_encode($data);
			    $str_data = json_encode($data);

			    $result = $this->sendPostData($url_send, $str_data);
			}
		}

		function GetAttachmentToAPI($OrderUID,$DocFileName) {
			if($OrderUID){
				$this->db->select ( 'torderdocuments.*, torders.OrderDocsPath, mtemplates.TemplateName,torders.OrderNumber as OrderNumber' ); 
				$this->db->from ( 'torderdocuments' );
				$this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID',"left");
				$this->db->join ( 'mtemplates', 'torders.TemplateUID = mtemplates.TemplateUID',"left");
				$this->db->where ('torderdocuments.OrderUID',$OrderUID);
				$this->db->where ('torderdocuments.DocumentFileName',$DocFileName);
				$query = $this->db->get();
				$Result = $query->row();
				return $Result;
			}
		}

		function SendPackagestoAbstractor($post){

			$OrderUID = $post['OrderUID'];
			$DocumentFileName = $post['documentfilenames'];

			$tApiOutBoundOrders = $this->GetApiOutBoundOrders($OrderUID,'AT04');
				
			if($tApiOutBoundOrders){

				$Documents=[];
				foreach($DocumentFileName as $key=>$value) {
					$DocFileName = $value;
					$OrderDocDetails = $this->GetAttachmentToAPI($OrderUID,$DocFileName);
					$OrderNumber = $OrderDocDetails->OrderNumber;
					$OrderDocsPath = $OrderDocDetails->OrderDocsPath;
					$FileName = $OrderDocDetails->DocumentFileName;
					$TemplateName = $OrderDocDetails->TemplateName;
					$DocumentCreatedDate = $OrderDocDetails->DocumentCreatedDate;
					$text = file_get_contents(FCPATH.$OrderDocsPath.$FileName);
					$Content = base64_encode($text);
					$obj= [];
					$obj['OrderDocsPath']=$OrderDocsPath;
					$obj['DocumentFileName']=$FileName;
					$obj['TemplateName']=$TemplateName;
					$obj['DocumentCreatedDate']=$DocumentCreatedDate;
					$obj['Content']=$Content;
					array_push($Documents, $obj);
					unset($obj);
				}

				$ApiOutBoundOrderUID = $tApiOutBoundOrders->ApiOutBoundOrderUID;
				$OrderSourceUID = $tApiOutBoundOrders->OrderSourceUID;

		    	$AppSourceName = $this->GetSourceName($OrderSourceUID); 
	            $SourceName = trim($AppSourceName->OrderSourceName);

				$OrderDetails = $this->GetOrderDetails($OrderUID);

		    	if($SourceName=="Avanze") {
			      $Action = 'AT04';
			    } 

			    $url_send = $this->config->item("api_url");

			    $data = array(
			      'EventCode' => $Action,
			      'OrderUID' => $OrderUID,
			      'OrderDetails' => $OrderDetails,
			      'Comment' => 'Search Package',
			      'OrderSourceUID'=> $OrderSourceUID,
			      'Documents'=> $Documents,
			      'SourceName'=> $SourceName
			    );

			    $str_data = json_encode($data);
			    $result = $this->sendPostData($url_send, $str_data);
			}
		}

		function GetApiOutBoundOrders($OrderUID,$Action){

			$this->db->select("*");
			$this->db->from('torders');
			$this->db->where(array("torders.OrderUID"=>$OrderUID));
			$query = $this->db->get();
			$OrderDetails = $query->row();
			$AbstractorUID = $OrderDetails->AbstractorUID;

			$this->db->select("*");
			$this->db->from('mabstractor');
			$this->db->where(array("mabstractor.AbstractorUID"=>$AbstractorUID));
			$query = $this->db->get();
			$OrderDetails = $query->row();
			$OrderSourceUID = $OrderDetails->OrderSourceUID;

			$this->db->select_max('ApiOutBoundOrderUID');
			$this->db->from('tApiOutBoundOrders');
			$this->db->where(array("tApiOutBoundOrders.OrderUID"=>$OrderUID,"tApiOutBoundOrders.OrderSourceUID"=>$OrderSourceUID));
			$query = $this->db->get();
			$ApiOutBoundOrderUID = $query->row()->ApiOutBoundOrderUID;

			if($Action == 'AT05'){
				$Status = array('New', 'Accepted', 'Completed');				
			} else {
				$Status = array('New', 'Accepted');				
			}

			$this->db->select("*");
			$this->db->from('tApiOutBoundOrders');
			$this->db->where(array("tApiOutBoundOrders.ApiOutBoundOrderUID"=>$ApiOutBoundOrderUID));       
			$this->db->where_in('Status',$Status);       
			$query = $this->db->get();
			$tApiOutBoundOrders = $query->row();

			if($tApiOutBoundOrders){
				return $tApiOutBoundOrders;
			} else {
				return false;
			}
		}

		function GetCancelApiOutBoundOrders($OrderUID){
			$this->db->select_max('ApiOutBoundOrderUID');
			$this->db->from('tApiOutBoundOrders');
			$this->db->where(array("tApiOutBoundOrders.OrderUID"=>$OrderUID));
			$query = $this->db->get();
			$tApiOutBoundOrders = $query->row();

			$this->db->select("*");
			$this->db->from('tApiOutBoundOrders');
			$this->db->where(array("tApiOutBoundOrders.ApiOutBoundOrderUID"=>$tApiOutBoundOrders->ApiOutBoundOrderUID));       
			$query = $this->db->get();
			return $query->row();
		}

		function GetSourceName($OrderSourceUID)
		{
			$this->db->select("*");
			$this->db->from('mApiTitlePlatform');
			$this->db->where(array("mApiTitlePlatform.OrderSourceUID"=>$OrderSourceUID));       
			$query = $this->db->get();
			return $query->row();
		}	

		function UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$TransactionNO,$ResponseResult)
		{
			if($ResponseResult == 'Success'){
				$PabsNotes = $Notes.' is sent to API - '.$OrderSourceName;
			} else {
				$PabsNotes = $Notes.' is failed';
			}

			$NoteType = $this->GetNoteTypeUID('API Note');
			$SectionUID = $NoteType->SectionUID;

			$insert_notes = array(
				'Note' => $PabsNotes,
				'SectionUID' => $SectionUID,
				'OrderUID' => $OrderUID,
				'EventCode' => $Action,
				'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
				'CreatedByAPI' => $OrderSourceUID,
				'CreatedOn' => Date('Y-m-d H:i:s',strtotime("now"))
			);       

			$this->db->insert("tordernotes", $insert_notes);

			$this->db->select('*')->from('musers');
			$this->db->where('LoginID', 'isgn');
			$query=$this->db->get();
			$UserName=$query->row()->UserName;
			$UserID=$query->row()->UserUID;

			$data1['ModuleName']=$PabsNotes.'-insert';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']=Date('Y-m-d H:i:s',strtotime("now"));
			$data1['TableName']='tordernotes';
			$data1['OrderUID']=$OrderUID;
			$data1['UserUID']=$UserID;                        
			$this->common_model->Audittrail_insert($data1);
		}

		function GetNoteTypeUID($SectionName)
		{
			$this->db->select("*");
			$this->db->from('mreportsections');
			$this->db->where(array("mreportsections.SectionName"=>$SectionName));
			$query = $this->db->get();
			return $query->row();
		}

		function GetOrderDetails($OrderUID)
		{
			if($OrderUID){
				$this->db->select ( '*,torderpropertyinfo.APN as PINAPN' ); 
				$this->db->from ( 'torders' );
				$this->db->join ( 'torderlegaldescription', 'torderlegaldescription.OrderUID = torders.OrderUID' , 'left' );
				$this->db->join ( 'torderpropertyinfo', 'torderpropertyinfo.OrderUID = torders.OrderUID' , 'left' );
				$this->db->join ( 'torderdocuments', 'torderdocuments.OrderUID = torders.OrderUID' , 'left' );
				$this->db->join ( 'torderaddress', 'torderaddress.OrderUID = torders.OrderUID' , 'left' );
				$this->db->join ( 'tApiOutBoundOrders', 'tApiOutBoundOrders.OrderUID = torders.OrderUID' , 'left' );
				$this->db->where ('torders.OrderUID',$OrderUID);
				$this->db->where ('tApiOutBoundOrders.Status','Accepted');
				$this->db->group_by('torderdocuments.OrderUID'); 
				$query = $this->db->get();
				$OrderDetails = $query->row();

				$data = json_encode($OrderDetails);

				return $OrderDetails;
			}
		}

}?>
