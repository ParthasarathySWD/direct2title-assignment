<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Real_ec_model extends CI_Model {
	
	function __construct()
	{ 
		parent::__construct();
		$this->load->config('keywords');
		$this->load->model('Common_model');
	}

	function GetIsgnUser(){
		$this->db->select('*')->from('musers');
		$this->db->where('LoginID', 'isgn');
		$query=$this->db->get();
		$UserUID=$query->row()->UserUID;
		return $UserUID;
	}

	function clean($string) {
		$find = array("&","<",">");
		$replace = array("&amp;","&lt;","&gt;");
		$res = htmlspecialchars($string);
		$str = html_entity_decode($res);
		$html_str = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $string);
		return $html_str;
	}

	// ( Curl Function ) Send Post Data to API Server Starts//

		function sendPostData($url, $post){
			$APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
			$Orders = json_decode($post,true);
			$Comment = '';
			foreach ($Orders as $key => $value) {
				if($key === 'InBoundUID'){
					$this->db->select("*");
					$this->db->from('tApiOrders');
					$this->db->join ( 'mApiTitlePlatform', 'tApiOrders.OrderSourceUID = mApiTitlePlatform.OrderSourceUID' , 'left' );
					$this->db->where(array("tApiOrders.InBoundUID"=>trim($value)));
					$query = $this->db->get();
					$SourceName = $query->row();

					$OrderUID = $SourceName->OrderUID;
					$OrderNumber = $SourceName->OrderNumber;
					$OrderSourceUID = $SourceName->OrderSourceUID;
					$OrderSourceName = $SourceName->OrderSourceName;
				}

				if($key === 'OrderUID'){
					$OrderUID = $value;
				}

				if($key === 'Comment'){
					$Comment = $value;
				}
			}
			$cmt ='';
			if(!empty($Comment)){
				$cmt = $Comment;
			}

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

			$res = '';
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);

			if ($err) {
			  //echo "cURL Error #:" . $err;
			} else { 

				$SuccessMsg = explode('$', $response);
				$API = $SuccessMsg[0];
				$Action = trim($SuccessMsg[1]);
				$Status = trim($SuccessMsg[2]);

				switch ($API) {
					case 'RealEC':

					switch ($Action) {
						case '100':
						$Notes = 'Action: 100<br>Order Request - '.$cmt;
						break;
						case '130':
						$Notes = 'Action: 130<br>Service Confirmed - '.$cmt;
						break;
						case '140':
						$Notes = 'Action: 140<br>Order Not Accepted - '.$cmt;
						break;
						case '150':
						$Notes = 'Action: 150<br>Product Delivered - '.$cmt;
						break;
						case '180':
						$Notes = 'Action: 180<br>Document Delivered - '.$cmt;
						break;
						case '220':
						$Notes = 'Action: 220<br>Comment - '.$cmt;
						break;
						case '222':
						$Notes = 'Action: 222<br>Comment Action Required - '.$cmt;
						break;
						case '230':
						$Notes = 'Action: 230<br>Service On Hold - '.$cmt;
						break;
						case '240':
						$Notes = 'Action: 240<br>Service Cancelled - '.$cmt;
						break;
						case '260':
						$Notes = 'Action: 260<br>Service Resumed - '.$cmt;
						break;
						case '270':
						$Notes = 'Action: 270<br>Service Completed - '.$cmt;
						break;
						case '385':
						$Notes = 'Action: 385<br>Standard Data File Delivered - '.$cmt;
						break;
						case '500':
						$Notes = 'Action: 500<br>Curative Cleared - '.$cmt;
						break;
						default:
						$Notes = $Action.' Action is sent to RealEC API - '.$cmt;
						break;
					}

					$res = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status);

					break;
                    /* Start - Desc: Westcor Response Handling @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
					case 'Westcor':
					$Notes = '';	
					    if($Action === 'AT05'){
							$Notes ='Action: AT05<br>sent Note to API';
						}
						else if($Action === 'AT06'){
							$Notes ='Action: AT06<br> Service Suspend';
						}
						else if($Action === 'AT08'){
							$Notes ='Action: AT08<br> Service Resumed';
						}
						else if($Action === 'AT07'){
							$Notes ='Action: AT07<br> Service Cancelled';
						}
						else if($Action === 'AT16'){
							$Notes ='Action: AT16<br> Service Completed';
						}
						if($Notes){
							$res = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status);
						}
					break;
                    /* End - Desc: Westcor Response Handling @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
					case 'Stewart':
					$Notes = '';
					if($Action === 'product_reply'){
						$Notes = 'Product Reply XML - ';
					} else if($Action === 'product_reply_report'){
						$Notes = 'Final Report (Product Reply PDF) - ';
					} else if($Action === 'product_dispute_reply'){
						$Notes = 'Product Dispute Reply XML - ';
					} else if($Action === 'product_reply_error'){
						$Notes = 'Product Reply XML is Not sent to Stewart API. Waiting for the Response from Stewart';
					} else if($Action === 'error'){
						$Notes = 'Product Reply XML is Not sent to Stewart API. XML Data InCorrect';
					} 

					$res = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status);

					break;
					case 'Lereta':
					break;
					case 'WoltersKluwer':
					break;
					case 'StateCapital':
					break;
					case 'LoansPQ':
					$Notes = '';
					if($Action === 'loanspq_pdf_reply'){
						//$Notes = 'Product Reply PDF - ';
					} else if($Action === 'mortgage_info'){
						//$Notes = 'Mortgage info, Legal and  Vesting Description - ';
					} else if($Action === 'internal_comments'){
						$Notes = 'Internal Comments - '.$cmt;
					} 
					$res = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status);
					break;

					case 'Keystone':
					/* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
					$Notes = '';
					if($Action === 'AddNote'){
						$Notes = 'Action : AddNote <br> Notes -  '.$cmt;
					} else if($Action === 'AddNoteAck'){
						$Notes = 'Action : AddNoteAck <br> Notes with Action - '.$cmt;
					} else if($Action === 'AddAttachment'){
						$Notes = 'Action : AddAttachment <br> Attachments - '.$cmt;
					} else if($Action === 'DeliverProduct'){
						$Notes = 'Action : DeliverProduct <br> Final Title Product - '.$cmt;
					} else if($Action === 'DeliverPolicy'){
						$Notes = 'Action : DeliverPolicy <br> Final Title Policy attachment - '.$cmt;
					} else if($Action === 'InternalSubordination-NotApprovedByTitleProvider'){
						$Notes = 'Action : InternalSubordination-NotApprovedByTitleProvider <br> To notify that Internal Subordination is not approved - '.$cmt;
					} else if($Action === 'ClearedTitle'){
						$Notes = 'Action : ClearedTitle <br> to notify that the curative process is complete and the title is clear - '.$cmt;
					} else if($Action === 'CompletedSigning'){
						$Notes = 'Action : CompletedSigning <br> to notify the Lender that the Closing Documents were executed by the applicable parties. - '.$cmt;
					} else if($Action === 'AttorneyDocsFinalized'){
						$Notes = 'Action : AttorneyDocsFinalized <br> to notify that the attorney package has been finalized and to send the final document to the Lender. - '.$cmt;
					} 

					$res = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status);
					break;

					/* @purpose D2TINT-162: For event AT16 - Order review
					   @author D.Samuel Prabhu
					   @since 24 June 2020
					*/

					case 'Pabs':
						if($Action === 'AT16')
						{
							$Notes = 'Action: AT16<br>Service Completed - '.$cmt;
						}

						$res = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status);
					break;
				}

				return $res;
			}
		}

		function UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status){
			/* @purpose D2TINT-104: Keystone DocType and Events Changes  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
			if($Status == 'Success'){			
				if ( $Action === '385' || $Action === 'product_reply' || $Action === 'product_dispute_reply' || $Action === 'mortgage_info') {
					$this->db->where('OrderUID',$OrderUID);
					$this->db->update('torders',array('IsAPIXMLSend'=>'1'));
				} else if ( $Action === '150' || $Action === 'product_reply_report' || $Action === 'loanspq_pdf_reply' || $Action === 'AttorneyDocsFinalized' || $Action === 'DeliverProduct') {

					$this->db->where('OrderUID',$OrderUID);
					if($Action === 'DeliverProduct'){
						$this->db->update('torders',array('IsAPIReportSend'=>'1','IsAPIXMLSend'=>'1'));
					} else {					
						$this->db->update('torders',array('IsAPIReportSend'=>'1'));
					}
				}
			} else if($Status == 'Fail') {
				if ( $Action === '385' || $Action === 'product_reply' || $Action === 'product_dispute_reply' || $Action === 'mortgage_info') {
					$this->db->where('OrderUID',$OrderUID);
					$this->db->update('torders',array('IsAPIXMLSend'=>'0'));
				} else if ( $Action === '150' || $Action === 'product_reply_report' || $Action === 'loanspq_pdf_reply' || $Action === 'AttorneyDocsFinalized' || $Action === 'DeliverProduct') {

					$this->db->where('OrderUID',$OrderUID);
					if($Action === 'DeliverProduct'){
						$this->db->update('torders',array('IsAPIReportSend'=>'0','IsAPIXMLSend'=>'0'));
					} else {					
						$this->db->update('torders',array('IsAPIReportSend'=>'0'));
					}
				}
			}

			/* @purpose: Queue Management for closing order @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: June 15th 2020 */
			$this->load->model('api/api_model');
			$fieldArray['StatusUID'] = $EventCode;
			$fieldArray['EventType'] = 'Outbound';
			$fieldArray['Comment'] = '';
			$this->api_model->ClosingQueueManagement($OrderUID,$fieldArray,$OrderSourceUID);

			/* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/

			if(!empty($Notes)){
				$Note = $Notes.'<br> Status: '.$Status;
			} else {
				$Note = 'Action: '.$Action.' is sent to '.$OrderSourceName;
			}

			$NoteType = $this->GetNoteTypeUID('API Note');
			$SectionUID = $NoteType->SectionUID;
			$insert_notes = array(
				'Note' => $Note,
				'EventCode' => $Action,
				'SectionUID' => $SectionUID,
				'OrderUID' => $OrderUID,
				'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
				'CreatedByAPI' => $OrderSourceUID,
				'CreatedOn' => date('Y-m-d H:i:s')
			);       
			$this->db->insert("tordernotes", $insert_notes);
			$data1['ModuleName']=$Note.'-insert';
			$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
			$data1['DateTime']=date('y-m-d H:i:s');
			$data1['TableName']='tordernotes';
			$data1['OrderUID']=$OrderUID;
			$data1['UserUID']=$this->session->userdata('UserUID');                        
			$this->common_model->Audittrail_insert($data1);
			return true;
		}

		function GetNoteTypeUID($SectionName)
		{
			$this->db->select("*");
			$this->db->from('mreportsections');
			$this->db->where(array("mreportsections.SectionName"=>$SectionName));
			$query = $this->db->get();
			return $query->row();
		}

		function GetNotes($OrderUID)
		{
			$this->db->select("*");
			$this->db->from('tordernotes');
			$this->db->where(array("tordernotes.OrderUID"=>$OrderUID));
			$query = $this->db->get();
			return $query->row();
		}

	// ( Curl Function ) Send Post Data to API Server Ends//

	// Event Code 220 -Comment Api Order Function Starts //

		function CommentApiOrder($OrderUID,$Comment){
			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

	    	$AppSourceName = $this->GetSourceName($OrderUID); 
            $SourceName = trim($AppSourceName->OrderSourceName);

            if($SourceName=="RealEC") {
            	$Action = 220;
            } else if($SourceName=="Stewart") { 
            	$Action = 'note';
            } else if($SourceName=="LoansPQ") { 
            	$Action = 'internal_comments';
		        //$Comment = $this->LoansPQComments($OrderUID);
            } else if($SourceName=="Keystone") { 
            	$Action = 'AddNote';
            } else if($SourceName=="Pabs") {
            	$Action = 'AT17';
            }  
            /* Desc: Westcor Response Handling @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
            else if($SourceName=="Westcor") { 
            	$Action = 'AT05';
            }

			if($Is_Api){

				$url_send = $this->config->item("api_url");

				$data = array(
					'EventCode' => $Action,
					'OrderUID' => $OrderUID,
					'InBoundUID' => $InBoundUID,
					'TransactionID' => $TransactionID,
					'Comment' => $this->clean($Comment),
					'OrderNumber' => $Is_Api->OrderNumber,
					'SourceName' => $SourceName,
					'ApiOrderRequestUID' => $ApiOrderRequestUID
				);

				//$str_data = json_encode($data);
				$str_data = json_encode($data);

				$result = $this->sendPostData($url_send, $str_data);
			}
		}

		function LoansPQComments($OrderUID){

			$NoteType = $this->GetNoteTypeUID('API Note');
        	$SectionUID = $NoteType->SectionUID;
        	
			$this->db->select("*");
			$this->db->from('tordernotes');
			$this->db->where(array("tordernotes.OrderUID"=>$OrderUID,"tordernotes.SectionUID"=>$SectionUID));
			$query = $this->db->get();
			$tordernotes = $query->result();

			$note = '';
			foreach ($tordernotes as $key => $value) {

				if($value->CreatedByUserUID == 0) {
					$CreatedByAPI = $this->GetOrderSourceName($value->CreatedByAPI);
					$CreatedBy = $CreatedByAPI->OrderSourceName;
				} else {
					$CreatedByUserUID = $this->GetUserName($value->CreatedByUserUID);
					$CreatedBy = $CreatedByUserUID->UserName;
				}

				$note.=date('m/d/Y H:i:s', strtotime($value->CreatedOn)).' '.$CreatedBy.' : '.$value->Note. PHP_EOL;
			}

			$notes = str_replace('<br>', '', $note);
			return $notes;
		}

		function GetOrderSourceName($OrderSourceUID){
			$this->db->select("*");
			$this->db->from('mApiTitlePlatform');
			$this->db->where(array("mApiTitlePlatform.OrderSourceUID"=>$OrderSourceUID));     
			$query = $this->db->get();
			return $query->row();
		}

		function GetUserName($UserUID){
			$this->db->select("*");
			$this->db->from('musers');
			$this->db->where(array("musers.UserUID"=>$UserUID));     
			$query = $this->db->get();
			return $query->row();
		}

		function FinalLoansPQComments($OrderUID,$Comment){
			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;
			$AppSourceName = $this->GetSourceName($OrderUID); 
			$SourceName = trim($AppSourceName->OrderSourceName);
			$Action = 'internal_comments';
			if($Is_Api){
				$url_send = $this->config->item("api_url");
				$data = array(
					'EventCode' => $Action,
					'OrderUID' => $OrderUID,
					'InBoundUID' => $InBoundUID,
					'TransactionID' => $TransactionID,
					'Comment' => $this->clean($Comment),
					'OrderNumber' => $Is_Api->OrderNumber,
					'SourceName' => $SourceName,
					'ApiOrderRequestUID' => $ApiOrderRequestUID
				);
				$str_data = json_encode($data);

				$APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $url_send,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => "",
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => "POST",
					CURLOPT_POST => true,
					CURLOPT_POSTFIELDS => $str_data,
					CURLOPT_HTTPHEADER => array(
						"authorization: ".$APiAuthKeyDetails->APIAuthKey,
						"cache-control: no-cache",
						"content-type: application/json",
					),
				));

				$res = '';
				$response = curl_exec($curl);
				$err = curl_error($curl);
				curl_close($curl);

				if ($err) {
					return false;
				} else { 
					$SuccessMsg = explode('$', $response);
					$API = $SuccessMsg[0];
					$Action = trim($SuccessMsg[1]);
					$Status = trim($SuccessMsg[2]);

					if($Status == 'Success'){			
						$this->db->where('OrderUID',$OrderUID);
						$this->db->update('torders',array('IsAPICommentSent'=>'1'));
					} else if($Status == 'Fail') {
						$this->db->where('OrderUID',$OrderUID);
						$this->db->update('torders',array('IsAPICommentSent'=>'0'));
					}
					return true;
				}
			}
		}

	// Event Code 220 -Comment Api Order Function Ends //

	// Event Code 222 - Comment Action Api Order Function Starts //

		function CommentActionApiOrder($OrderUID,$Comment){

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

			$ActionComment = str_replace("<b>ACTION</b> - ",'',$Comment);

			$AppSourceName = $this->GetSourceName($OrderUID); 
            $SourceName = trim($AppSourceName->OrderSourceName);

	    	if($SourceName=="RealEC") {
		      $Action = 222;
		    } else if($SourceName=="Stewart") { 
		      $Action = 'note';
		    } else if($SourceName=="Keystone") { 
		      $Action = 'AddNoteAck';
		    } 

			if($Is_Api){

				$url_send = $this->config->item("api_url");

				$data = array(
					'EventCode' => $Action,
					'OrderUID' => $OrderUID,
					'InBoundUID' => $InBoundUID,
					'TransactionID' => $TransactionID,
					'Comment' => $this->clean($ActionComment),
					'SourceName' => $SourceName,
					'ApiOrderRequestUID' => $ApiOrderRequestUID,
					'OrderNumber' => $Is_Api->OrderNumber,
				);

				//$str_data = json_encode($data);
				$str_data = json_encode($data);

				$result = $this->sendPostData($url_send, $str_data);
			}
		}


	// Event Code 222 - Comment Action Api Order Function Ends //

	// Event Code 230 - On-Hold Api Order Function Starts //

		function OnHoldApiOrder($OrderUID,$OnHoldComments,$ApiRequestCode){

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;
			$AppSourceName = $this->GetSourceName($OrderUID); 
            $SourceName = trim($AppSourceName->OrderSourceName);

            if($SourceName=="RealEC") {
            	$Action = 230;
            } else if($SourceName=="Stewart") { 
            	$Action = 'additional_info_request';
            } else if($SourceName=="Keystone") { 
            	$Action = 'SuspendOrder';
            } else if($SourceName=="Pabs") { 
            	$Action = 'AT18';
            } 
            /* Desc: Westcor Response Handling @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
            else if($SourceName=="Westcor") { 
            	$Action = 'AT06';
            }

			if($Is_Api){

				$url_send = $this->config->item("api_url");

				$data = array(
					'EventCode' => $Action,
					'OrderUID' => $OrderUID,
					'InBoundUID' => $InBoundUID,
					'TransactionID' => $TransactionID,
					'OnHoldComments' => $this->clean($OnHoldComments),
					'ApiRequestCode' => $ApiRequestCode,
					'SourceName' => $SourceName,
					'ApiOrderRequestUID' => $ApiOrderRequestUID,
					'OrderNumber' => $Is_Api->OrderNumber,
				);

				//$str_data = json_encode($data);
				$str_data = json_encode($data);

				$result = $this->sendPostData($url_send, $str_data);
			}
		}

	// Event Code 230 - On-Hold Api Order Function Ends //

	// Event Code 240 - Cancel Api Order Function Starts //

		function CancelApiOrder($OrderUID,$Remarks){

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
  			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

			$AppSourceName = $this->GetSourceName($OrderUID); 
            $SourceName = trim($AppSourceName->OrderSourceName);
            $CustomerAmount = $AppSourceName->CustomerAmount;

	    	if($SourceName=="RealEC") {
	    		$Action = 240;
	    		$Integration = "RealEC";
	    	} else if($SourceName=="Stewart") { 
	    		$Action = 'cancel_complete';
	    		$Integration = "Stewart";
	    	} else if($SourceName=="Keystone") { 
	    		$Action = 'CancelOrder';
	    		$Integration = "Keystone";
	    	}else if($SourceName=="Pabs") { 
	    		$Action = 'AT19';
	    		$Integration = "Pabs";
	    	}
	    	/* Desc: Westcor Response Handling @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
	    	else if($SourceName=="Westcor") {  
	    		$Action = 'AT07';
	    		$Integration = "Westcor";
	    	} else{
	    		$Integration = "";
	    	}

	    	if($Is_Api){

	    		$url_send = $this->config->item("api_url");

	    		$data = array(
	    			'EventCode' => $Action,
					'OrderUID' => $OrderUID,
	    			'InBoundUID' => $InBoundUID,
	    			'TransactionID' => $TransactionID,
	    			'SourceName' => $Integration,
	    			'CancelComments' => $this->clean($Remarks),
	    			'ApiOrderRequestUID' => $ApiOrderRequestUID,
	    			'OrderNumber' => $Is_Api->OrderNumber,
	    			'CustomerAmount' => $CustomerAmount
	    		);

				$str_data = json_encode($data);
				$result = $this->sendPostData($url_send, $str_data);
			}
		}

	// Event Code 240 - Cancel Api Order Function Ends //

	// Event Code 260 - Resume On-Hold  Api Order Function Starts //

		function ResumeOnHoldApiOrder($OrderUID,$StopRemarks){

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;
			$AppSourceName = $this->GetSourceName($OrderUID); 
            $SourceName = trim($AppSourceName->OrderSourceName);

            if($SourceName=="RealEC") {
            	$Action = 260;
            } else if($SourceName=="Keystone") { 
            	$Action = 'ResumeOrder';
            } else if($SourceName=="Pabs") { 
            	$Action = 'AT20';
            } 
            /* Desc: Westcor Response Handling @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
            else if($SourceName=="Westcor") { 
            	$Action = 'AT08';
            }

			if($Is_Api){

				$url_send = $this->config->item("api_url");

				$data = array(
					'EventCode' => $Action,
					'OrderUID' => $OrderUID,
					'InBoundUID' => $InBoundUID,
					'TransactionID' => $TransactionID,
					'ResumeOnHoldComments' => $this->clean($StopRemarks),
					'SourceName' => $SourceName,
					'ApiOrderRequestUID' => $ApiOrderRequestUID,
					'OrderNumber' => $Is_Api->OrderNumber,
				);

				//$str_data = json_encode($data);
				$str_data = json_encode($data);

				$result = $this->sendPostData($url_send, $str_data);

				
			}
		}

	// Event Code 260 - Resume On-Hold Api Order Function Ends //

	// Event Code 270 - Complete Api Order Function Starts //

		function CompleteApiOrder($OrderUID){

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$OrderNumber = $Details->OrderNumber;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;
			$Comment = '';

			$AppSourceName = $this->GetSourceName($OrderUID); 
            $SourceName = trim($AppSourceName->OrderSourceName);
            $torders = $this->common_model->get_orderdetails($OrderUID);

			if($Is_Api){

				if($SourceName=="RealEC") {
					if ($Is_Api->SubProductCode === 'TO'){
						$Action = 500;
					} else if ($torders->IsClosingProduct){
						$Action = '';
					} else{
						$Action = 270;
					}
				} else if($SourceName=="LoansPQ") { 
					$Action = 'status_complete';
				} else if($SourceName=="Keystone") { 
					$Action = 'CompleteOrder';
					$Comment = 'Service has been completed';
				} else if($SourceName=="Pabs") { 
					$Action = 'AT16';
					$Comment = 'Service has been completed';
				} 
				/* Desc: Westcor Response Handling @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
				else if($SourceName=="Westcor") { 
					$Action = 'AT16';
					$Comment = 'Service has been completed';
				}

				$url_send = $this->config->item("api_url");

				$data = array(
					'EventCode' => $Action,
					'OrderUID' => $OrderUID,
					'InBoundUID' => $InBoundUID,
					'TransactionID' => $TransactionID,
					'OrderNumber' => $OrderNumber,
					'SourceName' => $SourceName,
					'Comment' => $Comment,
					'ApiOrderRequestUID' => $ApiOrderRequestUID
				);

				//$str_data = json_encode($data);
				$str_data = json_encode($data);

				$result = $this->sendPostData($url_send, $str_data);
			}
		}

	// Event Code 270 - Complete Api Order Function Ends //

	// Checking whether the order is API Order or Not //
 
		function CheckApiOrders($OrderUID){
			$this->db->select("*"); 
			$this->db->from('torders');
			$this->db->join ( 'msubproducts', 'torders.SubProductUID = msubproducts.SubProductUID' , 'left' );
			$this->db->where(array("torders.APIOrder"=>1,"torders.OrderUID"=>$OrderUID));
			$query = $this->db->get();
			$Is_Api = $query->row();

			return $Is_Api;
		}

	// Checking Inbound Transaction Details //

		function GetInBoundTransactionDetails($OrderUID){
			$this->db->select("*"); 
			$this->db->from('tApiOrders');
			$this->db->where(array("tApiOrders.OrderUID"=>$OrderUID));
			$query = $this->db->get();
			$data = $query->row();

			return $data;
		}

		function GetSourceName($OrderUID)
		{
			$this->db->select("*");
			$this->db->from('torders');
			$this->db->join('mApiTitlePlatform','torders.OrderSourceUID=mApiTitlePlatform.OrderSourceUID','left');
			$this->db->where(array("torders.OrderUID"=>$OrderUID));       
			$query = $this->db->get();
			return $query->row();
		}	

		function AcceptCancelApiOrder($OrderUID, $Comments)
        {            
            $Is_Api = $this->CheckApiOrders($OrderUID);
            $Details = $this->GetInBoundTransactionDetails($OrderUID);
            $InBoundUID = $Details->InBoundUID;
            $TransactionID = $Details->TransactionID;

            $SourceName = $this->GetSourceName($OrderUID); 
            $OrderSourceName = $SourceName->OrderSourceName;
        	$CustomerAmount = $SourceName->CustomerAmount;

            $SourceName = trim($OrderSourceName);

            if($SourceName=="RealEC") {
              $Action = 240;
            } else if($SourceName=="Stewart") { 
              $Action = 'cancel_complete';
            }

            if($Is_Api){

                $url_send = $this->config->item("api_url");

                $data = array(
                    'EventCode' => $Action,
                    'OrderUID' => $OrderUID,
                    'InBoundUID' => $InBoundUID,
                    'TransactionID' => $TransactionID,
                    'CancelComments' => $this->clean($Comments),
                    'SourceName' => $SourceName,
                	'CustomerAmount' => $CustomerAmount
                );
                $str_data = json_encode($data);
                $result = $this->sendPostData($url_send, $str_data);
            }
        }

        function ApprovalFunctionForClosing($fieldArray,$Action,$CreatedByAPI){

        	$json_encode = json_encode($fieldArray);
        	$OrderUID = $fieldArray['OrderUID'];
        	$TransactionID = $fieldArray['TransactionID'];
        	$InBoundUID = $fieldArray['InBoundUID'];
        	$data_array['OrderUID']=$OrderUID;
        	$data_array['Remark']= $Action.' by API';
        	$data_array['ApprovalFunction']= $Action;
        	$data_array['ApprovalStatus']=0;
        	$data_array['IsReviewed']=1;
        	$data_array['RaisedDatetime']=date('Y-m-d H:i:s');
        	$data_array['RaisedByAPI']=$CreatedByAPI;
        	$data_array['APIContentData']=$json_encode;
        	$approval = $this->db->insert('torderapprovals',$data_array);
        	if($approval){
        		return $approval;
        	} else {
        		return false;
        	}
        }

        function toUpdateScheduleInfo($fieldArray,$CreatedByAPI){

        	$OrderUID = $fieldArray['OrderUID'];
        	$InBoundUID = $fieldArray['InBoundUID'];
        	$torders = $this->common_model->get_row('torders', ['OrderUID'=>$OrderUID]);
        	$ProductList = $fieldArray['ProductList'];
        	$ScheduledDate = $ProductList['ScheduledDate'];
        	$ScheduledTime = $ProductList['ScheduledTime'];
        	$SigningLocation = $ProductList['SigningLocationList']['SigningLocation'];
        	$Instructions = $ProductList['Instructions'];
        	if(empty($Instructions))
        	{
        		$Instructions = '';
        	}
        	$Comment = $ProductList['Event']['Comment'];
        	if(empty($Comment))
        	{
        		$Comment = '';
        	}
			$SigningDateTime = date('Y-m-d H:i:s', strtotime($ScheduledDate . ' ' .$ScheduledTime.$seconds));

			$this->db->trans_begin();

			$old_torders = $this->common_model->get_orderdetails($OrderUID);
        	$old_SpecialInstruction = $old_torders->SpecialInstruction;
        	$old_AddressNotes = $old_torders->AddressNotes;
        	if($Instructions){
    			$update_schdeule['SpecialInstruction'] = $Instructions;
        	}
        	if($Comment){
    			$update_schdeule['AddressNotes'] = $Comment;
        	}
        	if($update_schdeule){
        		$this->db->where(array("OrderUID"=>$OrderUID));        
        		$this->db->update('torders', $update_schdeule);
        	}

        	$old_tOrderClosingTemp = $this->common_model->get_row('tOrderClosingTemp', ['OrderUID'=>$OrderUID]);
        	$old_SigningDateTime = $old_torders->SigningDateTime;
        	
        	$SigningDateTime = date('Y-m-d H:i:s', strtotime($ScheduledDate . ' ' .$ScheduledTime));

        	$tOrderClosingTemp['SigningDateTime'] = $SigningDateTime;
        	$tOrderClosingTemp['SigningAddress1'] = ($SigningLocation['Addr1'] ?: '');
        	$tOrderClosingTemp['SigningAddress2'] = ($SigningLocation['Addr2'] ?: '');
        	$tOrderClosingTemp['SigningZipCode'] = ($SigningLocation['Zip'] ?: '');
        	$tOrderClosingTemp['SigningCityName'] = ($SigningLocation['City'] ?: '');
        	$tOrderClosingTemp['SigningStateCode'] = ($SigningLocation['State'] ?: '');

        	if ($old_torders->IsClosingProduct == 1) {
        		$old_tOrderClosingTemp = $this->common_model->get_row('tOrderClosingTemp', ['OrderUID'=>$OrderUID]);
        		if($old_tOrderClosingTemp){
        			$old_SigningDateTime = $old_tOrderClosingTemp->SigningDateTime;
        			$this->db->where(array("OrderUID"=>$OrderUID));        
        			$this->db->update('tOrderClosingTemp', $tOrderClosingTemp);
        		} else {
        			$tOrderClosingTemp = array(
        				'OrderUID' => $OrderUID,
        				'SigningDateTime' => $SigningDateTime,
        				'SpecialInstruction' => $Instructions,
        				'AddressNotes' => $Comment,
        				'SigningAddress1' => ($SigningLocation['Addr1'] ?: ''),
        				'SigningAddress2' => ($SigningLocation['Addr2'] ?: ''),
        				'SigningZipCode' => ($SigningLocation['Zip'] ?: ''),
        				'SigningCityName' => ($SigningLocation['City'] ?: ''),
        				'SigningStateCode' => ($SigningLocation['State'] ?: ''),
        				'IsSigningAddress' => 'Others',
        			);
        			$this->db->insert("tOrderClosingTemp", $tOrderClosingTemp);
        		}
			}

			if($old_SigningDateTime){
	        	$old_SigningDateTime = $old_SigningDateTime;
	        } else {
	        	$old_SigningDateTime = '-';
	        }
	        if($old_SpecialInstruction){
	        	$old_SpecialInstruction = $old_SpecialInstruction;
	        } else {
	        	$old_SpecialInstruction = '-';
	        }
	        if($old_AddressNotes){
	        	$old_AddressNotes = $old_AddressNotes;
	        } else {
	        	$old_AddressNotes = '-';
	        }

        	$this->db->where(array("OrderUID"=>$OrderUID));        
        	$this->db->update('tOrderClosingTemp', $tOrderClosingTemp);

			$ScheduleStatus = array('Assign', 'Reschedule');			
			$this->db->select ( '*' );
        	$this->db->from ('tOrderSchedule' );
        	$this->db->where('OrderUID',$OrderUID);
        	$this->db->where_in('ScheduleStatus',$ScheduleStatus);
        	$query = $this->db->get();
        	$tOrderSchedule = $query->result_array();

        	$Notes.= 'RealEC Request: 303<br>';
        	$Notes.= 'Scheduled Date: '.$ScheduledDate.'<br>';
        	$Notes.= 'Scheduled Time: '.$ScheduledTime.'<br>';
        	$Notes.= 'Signing Location: '.$SigningLocation['Addr1'].' '.$SigningLocation['Addr2'].','.$SigningLocation['City'].','.$SigningLocation['State'].','.$SigningLocation['Zip'].'.<br>';

        	$torders = $this->common_model->get_orderdetails($OrderUID);
        	$StatusUID = $torders->StatusUID;

        	if($StatusUID == $this->config->item('keywords')['Cancelled']){
        		$this->load->model('order_cancel/order_cancel_model');
        		$this->load->model('my_approvals/my_approval_model');
        		$result = $this->order_cancel_model->RevokeOrderStatus($OrderUID);
        		if($result)
        		{
        			$Notes.= '<b>Order is changed from Cancelled to Reopened</b><br>';
        			$this->order_cancel_model->DeleteRevokedOrder($OrderUID);
        			$this->my_approval_model->Audittrail_insert($Notes,$OrderUID);
        		}
        	} elseif($StatusUID == $this->config->item('keywords')['New Order']){
        		$Notes.= '<b>Order is in New Status</b><br>';
        	} else {
        		$Notes.= '<b>Schedule Update Request move it to approvals</b><br>';
        		if (count($tOrderSchedule) > 1 ) {
        			$this->ApprovalFunctionForClosing($fieldArray,'Schedule',$CreatedByAPI);
        		} else {
        			$this->ApprovalFunctionForClosing($fieldArray,'Schedule',$CreatedByAPI);
        			/* Direct Update Information - Single Borrower*/

	        		/*$ScheduleUID = $tOrderSchedule[0]['ScheduleUID'];
	        		$this->db->select ( '*' );
	        		$this->db->from ('tOrderScheduleBorrower' );
	        		$this->db->where('ScheduleUID',$ScheduleUID);
	        		$query = $this->db->get();
	        		$tOrderScheduleBorrower = $query->result_array();
	        		if (count($tOrderScheduleBorrower) > 1 ) {
	        			$this->ApprovalFunctionForClosing($fieldArray,'Schedule',$CreatedByAPI);
	        		} else {
	        			$Notes = 'Action: 303'.'<br>Scheduled Date: '.$ScheduledDate.'<br>Scheduled Time: '.$ScheduledTime.'<br>Signing Location: '.$SigningLocation['Addr1'].' '.$SigningLocation['Addr2'].','.$SigningLocation['City'].','.$SigningLocation['State'].','.$SigningLocation['Zip'].'.';
	        			$ScheduleUID = $tOrderScheduleBorrower[0]['ScheduleUID'];

	        			$tOrderSchedule = array(
	        				"SigningDateTime"=> $SigningDateTime,
	        			);
	        			$this->db->where('OrderUID',$OrderUID);
	        			$this->db->where('ScheduleUID',$ScheduleUID);
	        			$this->db->update('tOrderSchedule',$tOrderSchedule);

	        			$tOrderSigning = array(
	        				"ModifiedUserUID"=>$this->GetIsgnUser(),
	        				"SignedDateTime"=> $SigningDateTime,
	        			);
	        			$this->db->where('OrderUID',$OrderUID);
	        			$this->db->where('ScheduleUID',$ScheduleUID);
	        			$this->db->update('tOrderSign',$tOrderSigning);

	        			$ClosingData = array(
	        				'IsSigningAddress'=>'Others',
	        				'SigningAddress1'=>$SigningLocation['Addr1'],
	        				'SigningAddress2'=>$SigningLocation['Addr2'],
	        				'SigningZipCode'=>$SigningLocation['Zip'],
	        				'SigningCityName'=>$SigningLocation['City'],
	        				'SigningStateCode'=>$SigningLocation['State']
	        			);
	        			$this->db->where('OrderUID',$OrderUID);
	        			$this->db->where('ScheduleUID',$ScheduleUID);
	        			$this->db->update('tOrderClosing',$ClosingData);
	        		}*/
	        	}  
	        }      	

        	$NoteData = json_encode($ProductList);

        	$NoteType = $this->GetNoteTypeUID('API Note');
        	$SectionUID = $NoteType->SectionUID;

        	$Notes.= '<br><b>Signing Date & Time updated in summary</b><br>';
        	$Notes.= '<i>Existing:</i> '.$old_SigningDateTime.'<br>';
        	$Notes.= '<i>Now:</i> '.$SigningDateTime.'<br>';

        	$Notes.= '<br><b>Special Instruction updated in summary</b><br>';
        	$Notes.= '<i>Existing:</i> '.$old_SpecialInstruction.'<br>';
        	$Notes.= '<i>Now:</i> '.$Instructions.'<br>';

        	$Notes.= '<br><b>Comments updated in summary</b><br>';
        	$Notes.= '<i>Existing:</i> '.$old_AddressNotes.'<br>';
        	$Notes.= '<i>Now:</i> '.$Comment.'<br>';

        	$insert_notes = array(
        		'Note' => $Notes,
        		'NoteData' => $NoteData,
        		'EventCode' => '303',
        		'SectionUID' => $SectionUID,
        		'OrderUID' => $OrderUID,
        		'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
        		'CreatedByAPI' => $CreatedByAPI,
        		'CreatedOn' => date('Y-m-d H:i:s')
        	);

        	$result = $this->db->insert("tordernotes", $insert_notes);

        	if ($this->db->trans_status() === FALSE) {
        		$this->db->trans_rollback();
        		echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
        	}else{
        		$this->db->trans_commit();
        		echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'303 Event Inserted into Isgn Organization'));
        	}
        }

        function GetOrderSignedUID($OrderUID){
        	$this->db->select ( '*,MAX(SignUID) AS SignUID' );
        	$this->db->from ('tOrderSign' );
        	$this->db->where('OrderUID',$OrderUID);
        	$Status = array('Reschedule','InProgress');
        	$this->db->where_in('SigningStatus',$Status);
        	$query = $this->db->get();
        	return $query->row()->SignUID;
        }

        function EditSignDetails($SignedUID) {
        	$this->db->select ( '*' );
        	$this->db->select('DATE_FORMAT(SignedDateTime, "%m/%d/%Y") as SignedDateTime', FALSE);
        	$this->db->from ('tOrderSign' );
        	$this->db->where('SignUID',$SignedUID);
        	$query = $this->db->get();
        	return $query->row();
        }

        function toRescheduleclosing($fieldArray,$CreatedByAPI){
        	$OrderUID = $fieldArray['OrderUID'];
        	$InBoundUID = $fieldArray['InBoundUID'];
        	$ProductList = $fieldArray['ProductList'];
        	$ScheduledDate = $ProductList['ScheduledDate'];
        	$ScheduledTime = $ProductList['ScheduledTime'];
        	$Instructions = $ProductList['Instructions'];
        	if(empty($Instructions))
        	{
        		$Instructions = '';
        	}
        	$Comment = $ProductList['Event']['Comment'];
        	if(empty($Comment))
        	{
        		$Comment = '';
        	}
        	
			$this->db->trans_begin();

			$old_torders = $this->common_model->get_orderdetails($OrderUID);
        	$old_SpecialInstruction = $old_torders->SpecialInstruction;
        	$old_AddressNotes = $old_torders->AddressNotes;    

        	if($Instructions){
    			$update_schdeule['SpecialInstruction'] = $Instructions;
        	}
        	if($Comment){
    			$update_schdeule['AddressNotes'] = $Comment;
        	}
        	if($update_schdeule){
        		$this->db->where(array("OrderUID"=>$OrderUID));        
        		$this->db->update('torders', $update_schdeule);
        	}

        	$SigningDateTime = date('Y-m-d H:i:s', strtotime($ScheduledDate . ' ' .$ScheduledTime));
        	$tOrderClosingTemp['SigningDateTime'] = $SigningDateTime;

        	if ($old_torders->IsClosingProduct == 1) {
        		$old_tOrderClosingTemp = $this->common_model->get_row('tOrderClosingTemp', ['OrderUID'=>$OrderUID]);
        		if($old_tOrderClosingTemp){
        			$old_SigningDateTime = $old_tOrderClosingTemp->SigningDateTime;
        			$this->db->where(array("OrderUID"=>$OrderUID));        
        			$this->db->update('tOrderClosingTemp', $tOrderClosingTemp);
        		} else {
        			$tOrderClosingTemp = array(
        				'OrderUID' => $OrderUID,
        				'SigningDateTime' => $SigningDateTime,
        				'SpecialInstruction' => $Instructions,
        				'AddressNotes' => $Comment
        			);
        			$this->db->insert("tOrderClosingTemp", $tOrderClosingTemp);
        		}
			}

			if($old_SigningDateTime){
	        	$old_SigningDateTime = $old_SigningDateTime;
	        } else {
	        	$old_SigningDateTime = '-';
	        }
	        if($old_SpecialInstruction){
	        	$old_SpecialInstruction = $old_SpecialInstruction;
	        } else {
	        	$old_SpecialInstruction = '-';
	        }
	        if($old_AddressNotes){
	        	$old_AddressNotes = $old_AddressNotes;
	        } else {
	        	$old_AddressNotes = '-';
	        }

			$ScheduleStatus = array('Assign', 'Reschedule');			
			$this->db->select ( '*' );
        	$this->db->from ('tOrderSchedule' );
        	$this->db->where('OrderUID',$OrderUID);
        	$this->db->where_in('ScheduleStatus',$ScheduleStatus);
        	$query = $this->db->get();
        	$tOrderSchedule = $query->result_array();

        	$torders = $this->common_model->get_orderdetails($OrderUID);
        	$StatusUID = $torders->StatusUID;

        	$Notes.= 'RealEC Request: 310<br>';        	
        	$Notes.= 'Scheduled Date: '.$ScheduledDate.'<br>';
        	$Notes.= 'Scheduled Time: '.$ScheduledTime.'<br>';
        	
        	if($StatusUID == $this->config->item('keywords')['Cancelled']){
        		$this->load->model('order_cancel/order_cancel_model');
        		$this->load->model('my_approvals/my_approval_model');
        		$result = $this->order_cancel_model->RevokeOrderStatus($OrderUID);
        		if($result)
        		{
        			$Notes.= '<b>Order is changed from Cancelled to Reopened</b><br>';
        			$this->order_cancel_model->DeleteRevokedOrder($OrderUID);
        			$this->my_approval_model->Audittrail_insert($Notes,$OrderUID);
        		}
        	} elseif($StatusUID == $this->config->item('keywords')['New Order']){
        		$Notes.= '<b>Order is in New Status</b><br>';
        	} else {
        		$Notes.= '<b>Reschedule Request move it to approvals</b><br>';
          		if (count($tOrderSchedule) > 1 ) {
        			$this->ApprovalFunctionForClosing($fieldArray,'Reschedule',$CreatedByAPI);
    			} else {
        			$this->ApprovalFunctionForClosing($fieldArray,'Reschedule',$CreatedByAPI);
        			/* Direct Update Information - Single Borrower*/

	        		/*$ScheduleUID = $tOrderSchedule[0]['ScheduleUID'];
	        		$this->db->select ( '*' );
	        		$this->db->from ('tOrderScheduleBorrower' );
	        		$this->db->where('ScheduleUID',$ScheduleUID);
	        		$query = $this->db->get();
	        		$tOrderScheduleBorrower = $query->result_array();
	        		if (count($tOrderScheduleBorrower) > 1 ) {
	        			$this->ApprovalFunctionForClosing($fieldArray,'Reschedule',$CreatedByAPI);
	        		} else {
	        			$Notes = '';
	        			$Notes = 'Action: 310'.'<br>Scheduled Date: '.$ScheduledDate.'<br>Scheduled Time: '.$ScheduledTime;
	        			$ScheduleUID = $tOrderScheduleBorrower[0]['ScheduleUID'];
	        			$tOrderSigning = array(
	        				"ModifiedUserUID"=>$this->GetIsgnUser(),
	        				"SignedDateTime"=> $SigningDateTime,
	        			);
	        			$this->db->where('OrderUID',$OrderUID);
	        			$this->db->where('ScheduleUID',$ScheduleUID);
	        			$this->db->update('tOrderSign',$tOrderSigning);
	        		}*/
	        	}
	        }

        	$NoteData = json_encode($ProductList);
        	$NoteType = $this->GetNoteTypeUID('API Note');
        	$SectionUID = $NoteType->SectionUID;

        	$Notes.= '<br><b>Signing Date & Time updated in summary</b><br>';
        	$Notes.= '<i>Existing:</i> '.$old_SigningDateTime.'<br>';
        	$Notes.= '<i>Now:</i> '.$SigningDateTime.'<br>';

        	$Notes.= '<br><b>Special Instruction updated in summary</b><br>';
        	$Notes.= '<i>Existing:</i> '.$old_SpecialInstruction.'<br>';
        	$Notes.= '<i>Now:</i> '.$Instructions.'<br>';

        	$Notes.= '<br><b>Comments updated in summary</b><br>';
        	$Notes.= '<i>Existing:</i> '.$old_AddressNotes.'<br>';
        	$Notes.= '<i>Now:</i> '.$Comment.'<br>';

        	$insert_notes = array(
        		'Note' => $Notes,
        		'NoteData' => $NoteData,
        		'EventCode' => '310',
        		'SectionUID' => $SectionUID,
        		'OrderUID' => $OrderUID,
        		'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
        		'CreatedByAPI' => $CreatedByAPI,
        		'CreatedOn' => date('Y-m-d H:i:s')
        	);

    		$result = $this->db->insert("tordernotes", $insert_notes);

    		if ($this->db->trans_status() === FALSE) {
    			$this->db->trans_rollback();
    			echo json_encode( array('status' => 'failed','InBoundUID' =>$InBoundUID));
    		}else{
    			$this->db->trans_commit();
    			echo json_encode( array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'310 Event Inserted into Isgn Organization'));
    		}
        }

        function GetDocTypes460() {
        	$DocType460 = $this->config->item('DocType460');
        	return $DocType460;
        }
        
        function GetDocTypeValue($DocType){
        	$DocTypeValue = '';
        	if($DocType){        		
        		$DocType460 = $this->config->item('DocType460');
        		foreach ($DocType460 as $key => $value) {
        			if($key == $DocType){
        				$DocTypeValue =  $DocType;
        			}
        		}
        	}

        	if(empty($DocTypeValue)){        	
        		$DocTypeValue = $this->GetDocTypeValue('Others');
        	}

        	return $DocTypeValue;
        }

        function togetDocfrom_Lender($fieldArray,$CreatedByAPI){

        	$OrderUID = $fieldArray['OrderUID'];
        	$InBoundUID = $fieldArray['InBoundUID'];
        	$ProductList = $fieldArray['ProductList'];
        	$DocumentList = (array)$ProductList['DocumentList'];
			$DocumentCount = $DocumentList['Count'];
			$Comment = $ProductList['Event']['Comment'];

			$NoteData = json_encode($ProductList);

			if($DocumentCount == 1){
				$Document = $DocumentList['Document'];
				$Documents = [];
				$Documents[] = $Document;
			} else{
				$Documents = $DocumentList['Document'];
			}

			$torders = $this->common_model->get_orderdetails($OrderUID);
			$OrderNumber = $torders->OrderNumber;
			$DocTypes460 = $this->GetDocTypes460();

			$this->db->select('*')->from('musers');
			$this->db->where('LoginID', 'isgn');
			$query=$this->db->get();
			$UserName=$query->row()->UserName;
			$UserUID=$query->row()->UserUID;

        	$document_count = 0;
        	foreach ($Documents as $key => $value) {
        		$Document = $value['Content'];  
        		$FileName = $value['FileName'];  
        		$DocType = $value['DocType'];  
        		$Description = $value['Description'];  
        		$FinalPDF = base64_decode($Document);   
        		$Plus = $i+1;

        		if($torders->OrderDocsPath!= NULL)
        		{
        			$OrderDocs_Path = $torders->OrderDocsPath;
        		}else{

        			$date = date('Ymd');
        			$OrderDocsPath = 'uploads/searchdocs/'.$date.'/'.$OrderNumber.'/';
        			$query = $this->db->query("update torders SET OrderDocsPath =".$OrderDocsPath." Where OrderUID='".$OrderUID."' ");

        			$OrderDocs_Path = $OrderDocsPath;
        		}

        		$DocTypeValue = $this->GetDocTypeValue($DocType);
        		$TypeOfDocument = $this->common_model->GetDocumentTypeUIDByCode('Others');
        		$DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode($DocType);
        		if(!empty($DocumentTypeUID)){
        			$DocTypeValue = $DocumentTypeUID;
        		} else {
        			$DocTypeValue = $TypeOfDocument;        			
        		}

        		$this->db->like('DocumentFileName', $FileName);
        		$this->db->where(array('OrderUID' => $OrderUID));
        		$torderdocuments=$this->db->get('torderdocuments');
        		$searchdocumentcount=$torderdocuments->num_rows();
        		$searchdocumentcount+=1;

        		$ApiDocumentFileName = $this->GetAvailFileName($FileName, '.pdf', $searchdocumentcount, $OrderUID);

        		$file = FCPATH . $OrderDocs_Path . $ApiDocumentFileName;            

        		if (!is_dir($OrderDocs_Path)) {
        			mkdir($OrderDocs_Path, 0777, true);
        		}

        		file_put_contents($file, $FinalPDF);

        		$Insert = array(
        			'OrderUID'=>$OrderUID,
        			'DocumentTypeUID'=> $DocTypeValue,
        			'DocumentFileName'=>$ApiDocumentFileName,
        			'DisplayFileName'=>$ApiDocumentFileName,
        			'UploadedUserUID'=>$UserUID,
        			'UploadedDate'=>date('Y-m-d H:i:s'),
        			'TypeOfDocument'=> $DocTypeValue,
        			'DocumentCreatedDate'=>date('Y-m-d H:i:s'),
        			'IsDocumentReceived'=> 1,
        			'DocumentReceivedDateTime'=> date('Y-m-d H:i:s')
        		);

        		$res = $this->db->insert('torderdocuments',$Insert);

        		if($res){
        			$document_count++;

        			$Notes = $ApiDocumentFileName . ' is received from API';
        			$this->AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, '460');
        		}
        	}

        	if($document_count>0){
        		$Notes = 'PDF Delivered By API. <br>Action: 460<br>';
        		if(!empty($Comment)){
        			$Notes.= 'Comment: '.$Comment.'<br>';
        		}

        		if(!empty($Description)){
        			$Notes.= 'Description: '.$Description.'<br>';
        		}

        		if(!empty($DocType)){
        			$Notes.= 'DocType: '.$DocType.'<br>';
        		}
        		$this->AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, '460');

        		$res = array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'460 Event Inserted into Isgn Organization');
        	}else{
        		$res = array('status' => 'failed ','InBoundUID' =>$InBoundUID, 'message'=>'460 Event Inserted into Isgn Organization');
        	}

        	echo json_encode($res);

        }

        function AddNotes($Notes, $NoteData, $OrderUID, $CreatedByAPI, $EventCode, $InBoundUID=null){

        	$NoteType = $this->GetNoteTypeUID('API Note');
        	$SectionUID = $NoteType->SectionUID;

        	$insert_notes = array(
        		'Note' => $Notes,
        		'NoteData' => $NoteData,
        		'EventCode' => $EventCode,
        		'SectionUID' => $SectionUID,
        		'OrderUID' => $OrderUID,
        		//'InBoundUID' => $InBoundUID,
        		'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
        		'CreatedByAPI' => $CreatedByAPI,
        		'CreatedOn' => date('Y-m-d H:i:s')
        	);

        	$result = $this->db->insert("tordernotes", $insert_notes);
        }

        public function GetAvailFileName($FileName, $ext, $itr, $OrderUID)
        {
        	$DocumentFileName=$FileName.'_'.$itr.$ext;
        	$query=$this->db->get_where('torderdocuments', array('OrderUID'=>$OrderUID,
        		'DocumentFileName'=>$DocumentFileName));
        	$numrows=$query->num_rows();
        	if($numrows==0)
        	{ 
        		return $DocumentFileName;
        	}
        	$itr+=1;
        	return $this->GetAvailFileName($FileName, $ext, $itr);
        }

        function GetApiStatusCodeDetails($RequestCode){
        	$this->db->select ( '*' ); 
        	$this->db->from ( 'mApiRequest' );
        	$this->db->where ('mApiRequest.RequestCode',$RequestCode);
        	$query = $this->db->get();
        	$res = $query->row();
        	return $res;
        }

/* NF262 - RealEC dispute request */
        function RealECDisputeRequest($DataArray){ 


        	$TransactionID = $DataArray['TransactionID'];
        	$StatusUID = $DataArray['StatusUID'];
        	$OrderUID = $DataArray['OrderUID'];
        	$InBoundUID = $DataArray['InBoundUID']; 
        	$Comment = $DataArray['Comment'];

        	$OrderDetails = $this->common_model->get_orderdetails($OrderUID);
        	$OrderSourceUID = $OrderDetails->OrderSourceUID;

        	$this->db->select("*");
        	$this->db->from('mApiTitlePlatform');
        	$this->db->where(array("mApiTitlePlatform.OrderSourceUID"=>$OrderSourceUID));
        	$query = $this->db->get();
        	$SourceName = $query->row();
        	$CreatedByAPI = $SourceName->OrderSourceUID;
        	$OrderSourceName = $SourceName->OrderSourceName;

        	$Remarks = 'Exception Raised by RealEC: <br>'.$DataArray['Comment'];
        	$NoteType = $this->GetNoteTypeUID('API Note');
        	$SectionUID = $NoteType->SectionUID;

        	/*Exception raise */
        	$fieldArray = array(
        		"OrderUID"=>$OrderUID,
        		"ExceptionUID"=>$SectionUID,
        		"Remarks"=>$Remarks,
        		"RaisedByAPI"=>$CreatedByAPI,
        		"RaisedOn"=> Date('Y-m-d H:i:s',strtotime("now"))
        	);

        	/*Change Status*/
        	$torders = $this->db->get_where('torders', array('OrderUID'=>$OrderUID))->row();
        	$ExceptionRaised = $this->config->item('keywords')['Exception Raised']; 
        	if($torders->StatusUID == $ExceptionRaised ) {
        		$status = array("StatusUID"=>$ExceptionRaised,"IsPrint"=>NULL);
        	} else{
        		$status = array("StatusUID"=>$ExceptionRaised,"IsPrint"=>NULL,"IsExceptionOldStatusUID"=>$torders->StatusUID);        
        	}

        	$this->db->trans_begin();
        	$this->db->insert('texceptions', $fieldArray);
        	$this->db->where(array("torders.OrderUID"=>$OrderUID));
        	$this->db->update('torders',$status);

        	$insert_notes = array(
        		'Note' => $Remarks,
        		'SectionUID' => $SectionUID,
        		'OrderUID' => $OrderUID,
        		'EventCode' => '222', /* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
        		'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
        		'CreatedByAPI' => $CreatedByAPI,
        		'CreatedOn' => date('Y-m-d H:i:s')
        	);

        	if ($this->db->trans_status() === FALSE)
        	{
        		$this->db->trans_rollback();
        		return array('status' => 'failed','InBoundUID' =>$InBoundUID, 'message'=>'222 Event failed in Isgn Organization');
        	}
        	else
        	{
        		$this->db->trans_commit();
        		$this->db->insert("tordernotes", $insert_notes);
        		return array('status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'222 Event Inserted in Isgn Organization');
        	} 
        }       



        /* Key Stone Functions */

        function Fun_EscalationResponse($OrderUID,$Remarks){

        	$Is_Api = $this->CheckApiOrders($OrderUID);
        	$Details = $this->GetInBoundTransactionDetails($OrderUID);
        	$InBoundUID = $Details->InBoundUID;
        	$TransactionID = $Details->TransactionID;
        	$ApiOrderRequestUID = $Details->ApiOrderRequestUID;
        	$OrderNumber = $Details->OrderNumber;

        	$AppSourceName = $this->GetSourceName($OrderUID); 
        	$SourceName = trim($AppSourceName->OrderSourceName);
        	$CustomerAmount = $AppSourceName->CustomerAmount;

        	if($SourceName == "Keystone") {
        		$Action = "EscalationResponse";
        		$Integration = "Keystone";
        	} else{
        		$Integration = "";
        	}

        	if($Is_Api){

        		$url_send = $this->config->item("api_url");
        		$data = array(
        			'EventCode' => $Action,
        			'InBoundUID' => $InBoundUID,
        			'TransactionID' => $TransactionID,
        			'ApiOrderRequestUID' => $ApiOrderRequestUID,
        			'SourceName' => $Integration,
        			'Comment' => $this->clean($Remarks),
        			'OrderUID' => $OrderUID,
					'OrderNumber' => $OrderNumber,
        		);

        		$str_data = json_encode($data);
        		$result = $this->sendPostData($url_send, $str_data);
        	}
        }

        function OrderReopenRequestProcess($OrderUID,$Remarks) {
        	$Is_Api = $this->CheckApiOrders($OrderUID);
        	$Details = $this->GetInBoundTransactionDetails($OrderUID);
        	$InBoundUID = $Details->InBoundUID;
        	$TransactionID = $Details->TransactionID;
        	$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

        	$AppSourceName = $this->GetSourceName($OrderUID); 
        	$SourceName = trim($AppSourceName->OrderSourceName);
        	$CustomerAmount = $AppSourceName->CustomerAmount;

        	if($SourceName=="Keystone") { 
        		$Action = 'OrderReopenRequest';
        		$Integration = "Keystone";
        	} else{
        		$Integration = "";
        	}

        	if($Is_Api){

        		$url_send = $this->config->item("api_url");
        		$data = array(
        			'EventCode' => $Action,
        			'InBoundUID' => $InBoundUID,
        			'TransactionID' => $TransactionID,
        			'OrderNumber' => $Details->OrderNumber,
        			'SourceName' => $Integration,
        			'Comment' => $this->clean($Remarks),
        			'ApiOrderRequestUID' => $ApiOrderRequestUID,
        			'OrderUID' => $OrderUID,
        		);

        		$str_data = json_encode($data);

        		$result = $this->sendPostData($url_send, $str_data);
        	}
        } 

        function OrderReopenApprovedProcess($OrderUID,$Remarks) {
        	$Is_Api = $this->CheckApiOrders($OrderUID);
        	$Details = $this->GetInBoundTransactionDetails($OrderUID);
        	$InBoundUID = $Details->InBoundUID;
        	$TransactionID = $Details->TransactionID;
        	$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

        	$AppSourceName = $this->GetSourceName($OrderUID); 
        	$SourceName = trim($AppSourceName->OrderSourceName);
        	$CustomerAmount = $AppSourceName->CustomerAmount;

        	if($SourceName=="Keystone") { 
        		$Action = 'OrderReopenApproved';
        		$Integration = "Keystone";
        	} else{
        		$Integration = "";
        	}

        	if($Is_Api){

        		$url_send = $this->config->item("api_url");
        		$data = array(
        			'EventCode' => $Action,
        			'InBoundUID' => $InBoundUID,
        			'TransactionID' => $TransactionID,
        			'OrderNumber' => $Details->OrderNumber,
        			'SourceName' => $Integration,
        			'Comment' => $this->clean($Remarks),
        			'ApiOrderRequestUID' => $ApiOrderRequestUID,
        			'OrderUID' => $OrderUID,
        		);

        		$str_data = json_encode($data);

        		$result = $this->sendPostData($url_send, $str_data);
        	}
        } 

        function OrderRevisionRequest($OrderUID,$Remarks,$EventCode,$ReasonSection='') {
        	$Is_Api = $this->CheckApiOrders($OrderUID);
        	$Details = $this->GetInBoundTransactionDetails($OrderUID);
        	$InBoundUID = $Details->InBoundUID;
        	$TransactionID = $Details->TransactionID;
        	$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

        	$AppSourceName = $this->GetSourceName($OrderUID); 
        	$SourceName = trim($AppSourceName->OrderSourceName);
        	$CustomerAmount = $AppSourceName->CustomerAmount;

        	if($SourceName=="Keystone") { 
        		$Action = $EventCode;
        		$Integration = "Keystone";
        	} else{
        		$Integration = "";
        	}

        	if($Is_Api){

        		$url_send = $this->config->item("api_url");
        		$data = array(
        			'EventCode' => $Action,
        			'InBoundUID' => $InBoundUID,
        			'TransactionID' => $TransactionID,
        			'OrderNumber' => $Details->OrderNumber,
        			'SourceName' => $Integration,
        			'Comment' => $this->clean($Remarks),
        			'ApiOrderRequestUID' => $ApiOrderRequestUID,
        			'OrderUID' => $OrderUID,
        			'ReasonSection' => $ReasonSection,
        		);

        		$str_data = json_encode($data);

        		$result = $this->sendPostData($url_send, $str_data);
        	}
        } 

        function OrderRevisionRequestDoc($data) {
        	$OrderUID = $data['OrderUID'];
        	$RevisionDetails = $data['DisputeReplyDetails'];
        	$DocumentFileName = $data['documentfilenames'];
        	$EventCode = $data['EventCode'];
        	$Is_Api = $this->CheckApiOrders($OrderUID);
        	$Details = $this->GetInBoundTransactionDetails($OrderUID);
        	$InBoundUID = $Details->InBoundUID;
        	$TransactionID = $Details->TransactionID;
        	$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

        	$AppSourceName = $this->GetSourceName($OrderUID); 
        	$SourceName = trim($AppSourceName->OrderSourceName);

        	$Documents=[];
        	if($DocumentFileName){        		
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
        	}

        	if($SourceName=="Keystone") { 
        		$Action = $EventCode;
        		$Integration = "Keystone";
        	} else{
        		$Action = "";
        		$Integration = "";
        	}

        	if($Is_Api){
        		$url_send = $this->config->item("api_url");
        		$data = array(
        			'EventCode' => $Action,
        			'InBoundUID' => $InBoundUID,
        			'TransactionID' => $TransactionID,
        			'OrderNumber' => $Details->OrderNumber,
        			'OrderUID' => $OrderUID,
        			'SourceName' => $Integration,
        			'ApiOrderRequestUID' => $ApiOrderRequestUID,
        			'RevisionDetails' => $RevisionDetails,
        			'Documents' => $Documents,
        		);
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

		/**
		* @purpose: D2TINT-222 - 303 Event Ready to schedule
		*			To display schedule in API Schedule Panel list of schedule page ( same as keystone )
		* @author : D.Samuel Prabhu
		* @since  : 19 Aug 2020
		*
		* @param  : $fieldArray as array
		* @param  : $CreatedByAPI 
		**/

		function UpdateAPIScheduleInfo($fieldArray,$CreatedByAPI)
		{

			$OrderUID = $fieldArray['OrderUID'];
			$InBoundUID = $fieldArray['InBoundUID'];
			$ProductList = $fieldArray['ProductList'];
			$ScheduledDate = $ProductList['ScheduledDate'];
			$ScheduledTime = $ProductList['ScheduledTime'];
			$SigningLocation = $ProductList['SigningLocationList']['SigningLocation'];
			$Instructions = (!empty($ProductList['Instructions']) ?: '');
			$Comment =  (!empty($ProductList['Event']['Comment']) ?: '' );

        	$SigningDateTime = date('Y-m-d H:i:s', strtotime($ScheduledDate . ' ' .$ScheduledTime));

			$OrderDetail = $this->common_model->get_orderdetails($OrderUID);

            /* check closing product */
            if ($OrderDetail->IsClosingProduct == 1) 
            {

           		/* Get county name */
	           	if(!empty($SigningLocation['Zip']) && empty($SigningLocation['County']))
	           	{
	           		$this->load->model('order_complete/order_complete_model');
	           		$CountyName = $this->order_complete_model->getCountyDetail($SigningLocation['Zip']);
	           		$ClosingCountyName = $CountyName[0]->CountyName;
	           	}

	           	/* Begin Transaction */
	           	$this->db->trans_begin();

	           	/* Update if exists or insert into tOrderClosingTemp table */
	           	$closingTempDetail = $this->common_model->get_row('tOrderClosingTemp', ['OrderUID'=>$OrderUID]);

	           	$tOrderClosingTemp = array(
	           		'OrderUID' 			 => $OrderUID,
	           		'SigningDateTime' 	 => $SigningDateTime,
	           		'SpecialInstruction' => $Instructions,
	           		'AddressNotes' 		 => $Comment,
	           		'SigningAddress1' 	 => ($SigningLocation['Addr1'] ?: ''),
	           		'SigningAddress2' 	 => ($SigningLocation['Addr2'] ?: ''),
	           		'SigningZipCode' 	 => ($SigningLocation['Zip'] ?: ''),
	           		'SigningCityName' 	 => ($SigningLocation['City'] ?: ''),
	           		'SigningStateCode' 	 => ($SigningLocation['State'] ?: ''),
	           		'SigningCountyName'  => $ClosingCountyName,
	           		'IsSigningAddress' 	 => 'Others',
	           	);

	           	/* update closingtemp */
	           	if(!empty($closingTempDetail))
	           	{	        		
	           		unset($tOrderClosingTemp['OrderUID']);

	           		$this->db->where(["OrderUID"=>$OrderUID]);        
	           		$this->db->update('tOrderClosingTemp', $tOrderClosingTemp);
	           	}
	           	else /* insert closingtemp */
	           	{
	           		$this->db->insert("tOrderClosingTemp", $tOrderClosingTemp);				
	           	}


	           	/* Insert into tApiOrderSchedule */
	           	$apiScheduleInfo = array(
	           		'OrderUID' 			 		=> $OrderUID,
	           		'ClosingScheduledDatetime'	=> $SigningDateTime,
	           		'SpecialInstruction' 		=> $Instructions,
	           		'ClosingAddress1' 	 		=> ($SigningLocation['Addr1'] ?: ''),
	           		'ClosingAddress2' 	 		=> ($SigningLocation['Addr2'] ?: ''),
	           		'ClosingZipcode' 	 		=> ($SigningLocation['Zip'] ?: ''),
	           		'ClosingCityName'		    => ($SigningLocation['City'] ?: ''),
	           		'ClosingStateCode' 	 		=> ($SigningLocation['State'] ?: ''),
	           		'ClosingCountyName'	 		=> $ClosingCountyName,
	           		'IsMatching' 				=> 1,
	           		'CreatedBy' 		 		=> $CreatedByAPI,
	           		'CreatedDateTime' 	 	    => date('Y-m-d H:i:s')
	           	);

	           	$this->db->insert('tApiOrderSchedule',$apiScheduleInfo);

	           	/* Insert notes */     
	           	$NoteData = json_encode($ProductList);

	           	$Notes.= 'RealEC Request: 303<br>';
	            $Notes.= 'Scheduled Date: '. date("m/d/Y", strtotime($ScheduledDate)).'<br>';
	           	$Notes.= 'Scheduled Time: '.$ScheduledTime.'<br>';
	           	$Notes.= 'Signing Location: '.$SigningLocation['Addr1'].' '.$SigningLocation['Addr2'].','.$SigningLocation['City'].','.$SigningLocation['State'].','.$SigningLocation['Zip'].'.<br>';

	           	if(!empty($Instructions))
	           	{
	           		$Notes.= 'Special Instruction: '.$Instructions.'<br>';
	           	} 	

	           	if(!empty($Comment))
	           	{
	           		$Notes.= 'Comments: '.$Comment.'<br>';
	           	}

	           	$NoteType = $this->GetNoteTypeUID('API Note');
	           	$SectionUID = $NoteType->SectionUID;

	           	$tordernotes = array(
	           		'Note' => $Notes,
	           		'NoteData' => $NoteData,
	           		'EventCode' => '303',
	           		'SectionUID' => $SectionUID,
	           		'OrderUID' => $OrderUID,
	           		'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
	           		'CreatedByAPI' => $CreatedByAPI,
	           		'CreatedOn' => date('Y-m-d H:i:s')
	           	);

	           	$result = $this->db->insert("tordernotes", $tordernotes);

	           	/* Insert Audit trail */
	           	$UserUID = $this->db->select('*')->from('musers')->where('LoginID', 'isgn')->get()->row()->UserUID;
	           	$AuditLog = array(
	           		'UserUID'    => $UserUID,
	           		'ModuleName' => 'Schedule',
	           		'OrderUID'   => $OrderUID,
	           		'Feature'    => $OrderUID,
	           		'Content'    => $Notes,
	           		'DateTime'   => date('Y-m-d H:i:s')
	           	);
	          	$this->common_model->Audittrail_insert($AuditLog);	  	  	

	           	/* on Transacton Fails */
	           	if ($this->db->trans_status() === FALSE) 
	           	{
	           		$this->db->trans_rollback();
	           		echo json_encode(['status' => 'failed','InBoundUID' =>$InBoundUID, 'message'=> '303 Event failed.']);
	           	}
	           	else     /* on Transacton success */
	           	{
	           		$this->db->trans_commit();
	           		echo json_encode(['status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'303 Event Inserted into Isgn Organization']);
	           	}
            }
            else
            {
        	 	echo json_encode(['status' => 'failed','InBoundUID' =>$InBoundUID, 'message'=> '303 Event failed - Invalid order.']);
            }
	       
		}


		/**
		* @purpose: D2TINT-222 - 310 Event Re-Schedule
		* @author : D.Samuel Prabhu
		* @since  : 19 Aug 2020
		*
		* @param  : $fieldArray as array
		* @param  : $CreatedByAPI 
		**/
		function UpdateAPIRescheduleInfo($fieldArray,$CreatedByAPI)
		{
			$OrderUID 		= $fieldArray['OrderUID'];
        	$InBoundUID 	= $fieldArray['InBoundUID'];
        	$ProductList 	= $fieldArray['ProductList'];
        	$ScheduledDate 	= ($ProductList['ScheduledDate'] ?: '');
        	$ScheduledTime 	= ($ProductList['ScheduledTime'] ?: '');
        	$Instructions 	= ($ProductList['Instructions'] ?: '');
        	$Comment 		= ($ProductList['Event']['Comment'] ?: '');

        	$SigningDateTime = date('Y-m-d H:i:s', strtotime($ScheduledDate . ' ' .$ScheduledTime));

			$OrderDetail = $this->common_model->get_orderdetails($OrderUID);

		    /* check closing product */
           if($OrderDetail->IsClosingProduct == 1) 
           {
           	 	/* Begin Transaction */
	           	$this->db->trans_begin();

	           	$this->db->where('OrderUID',$OrderUID);
	           	$this->db->where('ScheduleStatus','Assign');
	           	$AssignedSchedules = $this->db->get('tOrderSchedule')->result();	           
               
                /* if it has one assigned schedule then update record */
	           	if(count($AssignedSchedules) == 1)
	           	{
	           		$this->load->model('api/api_model');

	           		/* Update tOrderSchedule */
	           	    $ScheduleUID = $AssignedSchedules[0]->ScheduleUID;
	           		$tOrderSchedule = array(
	           			"ModifiedByUserUID" => $this->api_model->GetIsgnUser(),
	           			'ModifiedDateTime'	=> date('Y-m-d H:i:s'),
	           			"SigningDateTime"	=> $SigningDateTime,
	           			"ScheduleStatus"	=> "Reschedule",
	           		);
	           		$this->db->where('OrderUID',$OrderUID);
	           		$this->db->where('ScheduleUID',$ScheduleUID);
	           		$this->db->update('tOrderSchedule',$tOrderSchedule);

	           		/* Update tOrderSign */	           		
	           		$tOrderSigning = array(
	           			"ModifiedUserUID"	=> $this->api_model->GetIsgnUser(),
	           			'ModifiedDateTime'	=> date('Y-m-d H:i:s'),
	           			"SignedDateTime"	=> $SigningDateTime,
	           			"SigningStatus"		=> "Reschedule",
	           		);

	           		$this->db->where('OrderUID',$OrderUID);
	           		$this->db->where('ScheduleUID',$ScheduleUID);
	           		$this->db->update('tOrderSign',$tOrderSigning); 

                    /* Update tOrderClosing */	   
	           	/*	$ClosingData = array(
	           			'IsSigningAddress'	=> 'Others',
	           			'SigningAddress1'	=>($SigningLocation['Addr1'] ?: ''),
	           			'SigningAddress2'	=> ($SigningLocation['Addr2'] ?: ''),
	           			'SigningZipCode'	=> ($SigningLocation['Zip'] ?: ''),
	           			'SigningCityName'	=> ($SigningLocation['City'] ?: ''),
	           			'SigningStateCode'	=> ($SigningLocation['State'] ?: ''),
	           			'SigningCountyName'	=> $($SigningLocation['Addr1'] ?: ''),
	           		);
	           		$this->db->where('OrderUID',$OrderUID);
	           		$this->db->where('ScheduleUID',$ScheduleUID);
	           		$this->db->update('tOrderClosing',$ClosingData);*/
	           	}
	           	else
	           	{

	           		/* Insert into tApiOrderSchedule */
	           		$apiScheduleInfo = array(
	           			'OrderUID' 			 		=> $OrderUID,
	           			'ClosingScheduledDatetime'	=> $SigningDateTime,
	           			'SpecialInstruction' 		=> $Instructions,
	           			'IsMatching' 				=> 1,
	           			'CreatedBy' 		 		=> $CreatedByAPI,
	           			'CreatedDateTime' 	 	    => date('Y-m-d H:i:s')
	           		);

	           		$this->db->insert('tApiOrderSchedule',$apiScheduleInfo);

	           	}

                /* get closingTemp Detail  */
	           	$closingTempDetail = $this->common_model->get_row('tOrderClosingTemp', ['OrderUID'=>$OrderUID]);

	           	$tOrderClosingTemp = array(
	           		'OrderUID' 			 => $OrderUID,
	           		'SigningDateTime' 	 => $SigningDateTime,
	           		'SpecialInstruction' => $Instructions,
	           		'AddressNotes' 		 => $Comment,
	           		'IsSigningAddress' 	 => 'Others',
	           	);

	           	/* update closingtemp */
	           	if(!empty($closingTempDetail))
	           	{	        		
	           		unset($tOrderClosingTemp['OrderUID']);

	           		$this->db->where(["OrderUID"=>$OrderUID]);        
	           		$this->db->update('tOrderClosingTemp', $tOrderClosingTemp);
	           	}
	           	else /* insert closingtemp */
	           	{
	           		$this->db->insert("tOrderClosingTemp", $tOrderClosingTemp);				
	           	}

	           	/* Insert log */
	           	$Notes.= 'RealEC Request: 310<br>';        	
	           	$Notes.= 'Scheduled Date: '. date("m/d/Y", strtotime($ScheduledDate)).'<br>';
	           	$Notes.= 'Scheduled Time: '.$ScheduledTime.'<br>';


	           	if(!empty($Instructions))
	           	{
	           		$Notes.= 'Special Instruction: '.$Instructions.'<br>';
	           	} 	

	           	if(!empty($Comment))
	           	{
	           		$Notes.= 'Comments: '.$Comment.'<br>';
	           	}

	           	$NoteType = $this->GetNoteTypeUID('API Note');
	           	$SectionUID = $NoteType->SectionUID;

	           	$tordernotes = array(
	           		'Note' 		   => $Notes,
	           		'EventCode'    => '310',
	           		'SectionUID'   => $SectionUID,
	           		'OrderUID' 	   => $OrderUID,
	           		'RoleType' 	   => '1,2,3,4,5,6,7,8,9,11,12',
	           		'CreatedByAPI' => $CreatedByAPI,
	           		'CreatedOn'    => date('Y-m-d H:i:s')
	           	);

	           	$result = $this->db->insert("tordernotes", $tordernotes);

	           	/* Insert Audit trail */
	            $UserUID = $this->db->select('*')->from('musers')->where('LoginID', 'isgn')->get()->row()->UserUID;
	           	$AuditLog = array(
	           		'UserUID'    => $UserUID,
	           		'ModuleName' => 'Reschedule',
	           		'OrderUID'   => $OrderUID,
	           		'Feature'    => $OrderUID,
	           		'Content'    => $Notes,
	           		'DateTime'   => date('Y-m-d H:i:s')
	           	); 
	           	$this->common_model->Audittrail_insert($AuditLog);	  	

	           	/* on Transacton Fails */
	           	if ($this->db->trans_status() === FALSE) 
	           	{
	           		$this->db->trans_rollback();
	           		echo json_encode(['status' => 'failed','InBoundUID' =>$InBoundUID, 'message'=> '310 Event failed.']);
	           	}
	           	else     /* on Transacton success */
	           	{
	           		$this->db->trans_commit();
	           		echo json_encode(['status' => 'success','InBoundUID' =>$InBoundUID, 'message'=>'310 Event Inserted into Isgn Organization']);
	           	}

           }
           else
           {
           	 echo json_encode(['status' => 'failed','InBoundUID' =>$InBoundUID, 'message' => '310 Event failed - Invalid order.']);
           }

		}
}?>
