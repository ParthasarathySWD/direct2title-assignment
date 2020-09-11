<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class D2t_to_api extends CI_Controller {

	function __construct()
	{ 
		parent::__construct();
		$this->load->config('keywords');
		$this->load->model('Common_model');
	}

	// ( Curl Function ) Send Post Data to API Server Starts//
	function sendPostData($url, $post){
		$Orders = json_decode($post,true);
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
		}
		$APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
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
					$Notes = 'Action: 100<br>Order Request - ';
					break;
					case '130':
					$Notes = 'Action: 130<br>Service Confirmed - ';
					break;
					case '140':
					$Notes = 'Action: 140<br>Order Not Accepted - ';
					break;
					case '150':
					$Notes = 'Action: 150<br>Product Delivered - ';
					break;
					case '180':
					$Notes = 'Action: 180<br>Document Delivered - ';
					break;
					case '220':
					//$Notes = 'Action: 220<br>Comment - ';
					break;
					case '222':
					//$Notes = 'Action: 222<br>Comment Action Required - ';
					break;
					case '230':
					$Notes = 'Action: 230<br>Service On Hold - ';
					break;
					case '240':
					$Notes = 'Action: 240<br>Service Cancelled - ';
					break;
					case '260':
					$Notes = 'Action: 260<br>Service Resumed - ';
					break;
					case '270':
					$Notes = 'Action: 270<br>Service Completed - ';
					break;
					case '385':
					$Notes = 'Action: 385<br>Standard Data File Delivered - ';
					break;
					case '500':
					$Notes = 'Action: 500<br>Curative Cleared - ';
					break;
					default:
					$Notes = $Action.' Action is sent to RealEC API - ';
					break;
				}

				$res = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status);

				break;

				/* Start - Desc: PabsOrder Response Handling @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
				case 'Pabs':
				$Notes = '';	
				if($Action === 'AT12'){
					$Notes = 'Action: AT12<br>Order Accepted';
				}
				else if($Action === 'AT13'){
					$Notes ='Action: AT13<br>Order Rejected';
				}
				else if($Action === 'AT14'){
					$Notes = 'Action: AT14<br>Documents Sent to Pabs';
				}
				else if($Action === 'AT15'){
					$Notes ='Action: AT15<br>Final Report sent';
				} 
				if($Notes){
					$res = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status);
				}
				break;
				/* End - Desc: PabsOrder Response Handling @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */

				/* Start - Desc: Westcor Response Handling @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
				case 'Westcor':
				$Notes = '';	
				if($Action === 'AT12'){
					$Notes = 'Action: AT12<br>Order Accepted';
				}
				else if($Action === 'AT13'){
					$Notes ='Action: AT13<br>Order Rejected';
				}
				else if($Action === 'AT14'){
					$Notes = 'Action: AT14<br>Documents Sent to Westcor';
				}
				else if($Action === 'AT15'){
					$Notes ='Action: AT15<br>Final Report sent';
				} 
				else if($Action === 'AT05'){
					$Notes ='Action: AT05<br>Sent Note to API';
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

					if($Notes){
						$res = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status);
					}

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
						$Notes = 'Internal Comments - ';
					} 
					$res = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status);
					break;
				case 'Keystone':
					$Notes = '';
					if($Action === 'AddNote'){
						$Notes = 'Action : AddNote <br> Notes -  ';
					} else if($Action === 'AddNoteAck'){
						$Notes = 'Action : AddNoteAck <br> Notes with Action - ';
					} else if($Action === 'AddAttachment'){
						$Notes = 'Action : AddAttachment <br> Attachments - ';
					} else if($Action === 'DeliverProduct'){
						$Notes = 'Action : DeliverProduct <br> Final Title Product - ';
					} else if($Action === 'DeliverPolicy'){
						$Notes = 'Action : DeliverPolicy <br> Final Title Policy attachment - ';
					} else if($Action === 'InternalSubordination-NotApprovedByTitleProvider'){
						$Notes = 'Action : InternalSubordination-NotApprovedByTitleProvider <br> To notify that Internal Subordination is not approved - ';
					} else if($Action === 'ClearedTitle'){
						$Notes = 'Action : ClearedTitle <br> to notify that the curative process is complete and the title is clear - ';
					} else if($Action === 'CompletedSigning'){
						$Notes = 'Action : CompletedSigning <br> to notify the Lender that the Closing Documents were executed by the applicable parties. - ';
					} else if($Action === 'AttorneyDocsFinalized'){
						$Notes = 'Action : AttorneyDocsFinalized <br> to notify that the attorney package has been finalized and to send the final document to the Lender. - ';
					} else if($Action === 'DeliverFinalSS'){
						$Notes = 'Action : DeliverFinalSS <br> to notify that the can exchange this event having Final data and/or Attachment. - ';
					} 
					/* @purpose D2TINT-97: Show all the API events Under API Events Tab  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/
					$res = $this->UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status);
					break;
			}

			return $res;
		}
	}

	// ( Curl Function ) Send Post Data to API Server Ends//

	function UpdateReportSend($OrderUID,$OrderSourceUID,$OrderSourceName,$Notes,$Action,$Status){
		/* @purpose D2TINT-104: Keystone DocType and Events Changes  @author Yagavi G <yagavi.g@avanzegroup.com> @since 29th April 2020*/ 
		if($Status == 'Success'){			
			if ( $Action === '385' ||  $Action === 'AT15' || $Action === 'product_reply' || $Action === 'product_dispute_reply' || $Action === 'mortgage_info') {
				$this->db->where('OrderUID',$OrderUID);
				$this->db->update('torders',array('IsAPIXMLSend'=>'1'));
			} else if ( $Action === '150' || $Action === 'AT14' || $Action === 'product_reply_report' || $Action === 'loanspq_pdf_reply' || $Action === 'AttorneyDocsFinalized' || $Action === 'DeliverProduct' || $Action === 'DeliverFinalSS') {

				$this->db->where('OrderUID',$OrderUID);
				if($Action === 'DeliverProduct' ){
					$this->db->update('torders',array('IsAPIReportSend'=>'1','IsAPIXMLSend'=>'1'));
				} else {					
					$this->db->update('torders',array('IsAPIReportSend'=>'1'));
				}
			}
		} else if($Status == 'Fail') {
			if ( $Action === '385' || $Action === 'AT15' || $Action === 'product_reply' || $Action === 'product_dispute_reply' || $Action === 'mortgage_info') {
				$this->db->where('OrderUID',$OrderUID);
				$this->db->update('torders',array('IsAPIXMLSend'=>'0'));
			} else if ( $Action === '150' || $Action === 'AT14' || $Action === 'product_reply_report' || $Action === 'loanspq_pdf_reply' || $Action === 'AttorneyDocsFinalized' || $Action === 'DeliverProduct' || $Action === 'DeliverFinalSS') {

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
			$Note = $Notes.$Status;
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
		$this->Common_model->Audittrail_insert($data1);

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

	public function GetAdverseConditionDetails($OrderUID){

		$this->db->select("*");
		$this->db->from('tOrderAdverse');
		$this->db->join ( 'mAdverseConditions', 'mAdverseConditions.AdverseConditionsUID = tOrderAdverse.AdverseConditionsUID' , 'left' );
		$this->db->where(array("OrderUID"=>$OrderUID));
		$query = $this->db->get();
		return $query->result_array();
	}

	function GetMortgageAssigneeDetails($OrderUID,$MortgageSNo){
		$this->db->select("*");
		$this->db->from('tordermortgageassignment');
		$this->db->join('msubdocumentmortgages','tordermortgageassignment.DocumentTypeUID=msubdocumentmortgages.DocumentTypeUID','left');
		$this->db->join ( 'tordermortgages', 'tordermortgages.MortgageSNo = tordermortgageassignment.MortgageSNo' , 'left' );
		$this->db->where(array("tordermortgageassignment.OrderUID"=>$OrderUID,"tordermortgageassignment.MortgageSNo"=>$MortgageSNo));
		$this->db->order_by('tordermortgageassignment.SubMortgagePosition','ASC');
		$query = $this->db->get();
		return $query->result();
	}

	function GetCountyStateUID($CountyName,$StateCode){
		$this->db->select ( '*' ); 
		$this->db->from ( 'mcounties' );
		$this->db->join('mstates','mstates.StateUID=mcounties.StateUID');
		$this->db->where(array('mcounties.CountyName'=>$CountyName, 'mstates.StateCode'=>$StateCode));
		$query = $this->db->get();
		return $query->row();
	}

	// Event Code 385 Function Starts //
		function SendStdDataDeliveredByProviderScript()
		{
			$OrderUID = $this->input->post('OrderUID');
			$order_details = $this->common_model->get_orderdetails($OrderUID);
			$Orderdetailsss = $order_details;

			$OrderDetails = $this->GetOrderDetails($OrderUID);
			$OrderNumber = $OrderDetails->OrderNumber;
	    	$DeedListDetails = $this->GetDeedListDetails($OrderUID);   
	    	$TrustorDetails = $this->GetPropertyRoles($OrderUID);
	    	$AdverseConditions = $this->GetAdverseConditionDetails($OrderUID);

	    	$CountyName=$OrderDetails->PropertyCountyName;
	    	$StateCode=$OrderDetails->PropertyStateCode;
	    	$CountyStateDetails = $this->GetCountyStateUID($CountyName,$StateCode);

	    	$CountyFIPS = $CountyStateDetails->CountyFIPSCode;
	    	$CountyFIPSCode = substr($CountyFIPS, 2);
	    	$StateFIPSCode = $CountyStateDetails->FIPSCode;

	    	$OrderDetails->CountyFIPSCode = $CountyFIPSCode;
    		$OrderDetails->StateFIPSCode = $StateFIPSCode;

    		$OrderDetails->EstimatedPropertyValue = '777000';
    		$OrderDetails->ValueSource = 'AVM';
    		$OrderDetails->DateOfValueSource = '2018-08-27';
    		$OrderDetails->CensusTract = '0698.02';
    		$OrderDetails->MSANumber = '37340';
    		$OrderDetails->VestingDescription = '';

	    	$AppSourceName = $this->GetSourceName($OrderUID); 
            $SourceName = trim($AppSourceName->OrderSourceName);

	    	$FinalMortgageListDetails = [];
	    	$MortgageListDetails = $this->GetMortgageListDetails($OrderUID); 

	    	foreach ($MortgageListDetails as $key => $values) {

	    		$MortgageSNo = $values->MortgageSNo;

	    		$MortgageAssigneeDetails = $this->GetMortgageAssigneeDetails($OrderUID,$MortgageSNo); 
	    		$FinalMortgageAssigneeDetails = [];

	    		foreach ($MortgageAssigneeDetails as $ele => $row) {

	    			// SubDocument Assignee 

					$sub_Book = '';
	                $sub_Page = '';
	                $sub_DocumentNo = '';

	                if($row->Subdocument_DBVTypeUID_1=='2'){
						$sub_BookPage = $row->Subdocument_DBVTypeValue_1;
						$sub_Res = explode('/',$sub_BookPage);
						$sub_Book = $sub_Res[0];
						$sub_Page = $sub_Res[1];					
					}elseif($row->Subdocument_DBVTypeUID_2=='2'){
						$sub_BookPage = $row->Subdocument_DBVTypeValue_2;
						$sub_Res = explode('/',$sub_BookPage);
						$sub_Book = $sub_Res[0];
						$sub_Page = $sub_Res[1];					
					}

					if($row->Subdocument_DBVTypeUID_1=='1'){
						$sub_Book = $row->Subdocument_DBVTypeValue_1;
					}elseif($row->Subdocument_DBVTypeUID_2=='1'){
						$sub_Book = $row->Subdocument_DBVTypeValue_2;
					}

					if($row->Subdocument_DBVTypeUID_1=='14'){
						$sub_Page = $row->Subdocument_DBVTypeValue_1;
					}elseif($row->Subdocument_DBVTypeUID_2=='14'){
						$sub_Page = $row->Subdocument_DBVTypeValue_2;
					}

					if($row->Subdocument_DBVTypeUID_1=='6'){
						$sub_DocumentNo = $row->Subdocument_DBVTypeValue_1;
					}elseif($row->Subdocument_DBVTypeUID_2=='6'){
						$sub_DocumentNo = $row->Subdocument_DBVTypeValue_2;
					}


					$Assignee= [];
					$Assignee['DocumentTypeName']=$row->DocumentTypeName;
					$Assignee['SubDocumentNo']=$sub_DocumentNo;
					$Assignee['SubBook']=$sub_Book;
					$Assignee['SubPage']=$sub_Page;
					$Assignee['Dated']=$row->Dated;
					$Assignee['Recorded']=$row->Recorded;
					$Assignee['OtherAssignmentType']=$row->MortgageAssignmentType;
					$Assignee['SubAmount']= '0.00';
					$Assignee['SubComments']=$row->Comments;

					array_push($FinalMortgageAssigneeDetails, $Assignee);

	    		}

                if($values->IsOpenEnded == 1){
                    $IsOpenEnded = 'Yes';
                }else{
                    $IsOpenEnded = 'No';
                }
                if($values->Mortgage_DBVTypeUID_1=='7'){
                    $Instrument = $values->Mortgage_DBVTypeValue_1;
                }elseif($values->Mortgage_DBVTypeUID_2=='7'){
                    $Instrument = $values->Mortgage_DBVTypeValue_2;
                }
                $Position = $key + 1;
                switch($Position)
                {
                    case "1":
                    $os = 'st';
                    break;
                    case "2":
                    $os = 'nd';
                    break;
                    case "3":
                    $os = 'rd';
                    break;
                    default:
                    $os = 'th';
                }
                if($values->Trustee1 != ''){
                    $Trustee = $this->clean($values->Trustee1);
                }else{
                    $Trustee = 'N/A';
                }

                $Book = '';
                $Page = '';
                $DocumentNo = '';

                if($values->Mortgage_DBVTypeUID_1=='2'){
					$BookPage = $values->Mortgage_DBVTypeValue_1;
					$Res = explode('/',$BookPage);
					$Book = $Res[0];
					$Page = $Res[1];					
				}elseif($values->Mortgage_DBVTypeUID_2=='2'){
					$BookPage = $values->Mortgage_DBVTypeValue_2;
					$Res = explode('/',$BookPage);
					$Book = $Res[0];
					$Page = $Res[1];					
				}

				if($values->Mortgage_DBVTypeUID_1=='1'){
					$Book = $values->Mortgage_DBVTypeValue_1;
				}elseif($values->Mortgage_DBVTypeUID_2=='1'){
					$Book = $values->Mortgage_DBVTypeValue_2;
				}

				if($values->Mortgage_DBVTypeUID_1=='14'){
					$Page = $values->Mortgage_DBVTypeValue_1;
				}elseif($values->Mortgage_DBVTypeUID_2=='14'){
					$Page = $values->Mortgage_DBVTypeValue_2;
				}

				if($values->Mortgage_DBVTypeUID_1=='6'){
					$DocumentNo = $values->Mortgage_DBVTypeValue_1;
				}elseif($values->Mortgage_DBVTypeUID_2=='6'){
					$DocumentNo = $values->Mortgage_DBVTypeValue_2;
				}	

                // $CustomerName = $value['CustomerName'];
                $MortgageAmount = $value['MortgageAmount'];
                $MortgageRecorded = $value['MortgageRecorded'];
                $MortgageDated = $value['MortgageDated'];
                $MortgageComments = $this->clean($value['MortgageComments']);

				$obj= [];
				$obj['Page']=$Page;
				$obj['Book']=$Book;
				$obj['DocumentNo']=$DocumentNo;
				$obj['Date']=$values->MortgageDated;
				$obj['RecordingDate']=$values->MortgageRecorded;
				$obj['MortgageMaturityDate']=$values->MortgageMaturityDate;
				$obj['Name']=$values->CustomerName;
				$obj['Trustee']=$Trustee;
				$obj['Amount']=$values->MortgageAmount;
				$obj['OpenEnded']=$IsOpenEnded;
				$obj['Position']=$Position.$os;
				$obj['Instrument']=$Instrument;
				$obj['Comment']=$values->MortgageComments;
				$obj['DocumentTypeName']=$values->DocumentTypeName;
				$obj['Mortgagee']=$values->Mortgagee;
				$obj['Mortgagor']=$values->Mortgagor;
				$obj['AdditionalInfo']=$values->AdditionalInfo;
				$obj['AssigneeDetails']=$FinalMortgageAssigneeDetails;

				array_push($FinalMortgageListDetails, $obj);
				unset($obj);
			}			

	    	$TaxListDetails = $this->GetTaxListDetails($OrderUID); 
			$FinalTaxListDetails = [];
	    	foreach ($TaxListDetails as $key => $value) 
	    	{
				$TaxCertSNo = $value->TaxCertSNo;
				$OrderUID = $value->OrderUID;
				$type = $value->TaxType;
				$damt = $value->AmountDelinquent; 
				$ddate = $value->GoodThroughDate;
				$dtype = $value->DocumentTypeName;
				$basic = $value->TaxBasisName;	
				$TaxID = $value->ParcelNumber;
				$PropertyClassName = $value->StewartCode;
				$TaxComments = $value->TaxComments;
				
				$TaxInstallment = $this->GetTaxInstallmentDetails($OrderUID, $TaxCertSNo); 

				$AmountPaid ='';
				$BaseAmount ='';
				$DatePaid =''; 
				$NextDueDate = '';
				foreach ($TaxInstallment as $key => $value) {
					$AmountPaid = $AmountPaid+$value->AmountPaid; 
					$BaseAmount = $BaseAmount+$value->GrossAmount;					
					$DatePaid = $value->DatePaid;
					$NextDueDate = $value->NextDueDate;
					$satus = $value->TaxStatusName;
					$TaxYear = $value->TaxYear;
				}
				
				$TaxExcem = $this->Gettaxexemptionbyid($TaxCertSNo); 
				foreach ($TaxExcem as $key => $exvalue) {
				  $Excemption = $exvalue['TaxExemptionName'];
				  $ExcemptionAmt = $exvalue['TaxAmount'];
				}

				if(empty($Excemption)){
					$Excemption = 'Empty';
				}

				$UniqueID = $key; 
				$Date = $DatePaid;
				$TotalTax = $BaseAmount;
				$TaxYear = $TaxYear;
				$TotalTaxPaid = $AmountPaid;
				$Comment = $TaxComments;
				$NextDueDate = $NextDueDate;

				if($Date == ''){
					$Date = '0000-00-00';
				}
				if($NextDueDate == ''){
					$NextDueDate = '0000-00-00';
				}

				if(empty($TotalTax)){
					//$TotalTax = '';
				}if(empty($TaxYear)){
					//$TaxYear = '';
				}if(empty($TotalTaxPaid)){
					$TotalTaxPaid = 'Empty';
				}if(empty($satus)){
					$satus = 'Empty';
				}

				if($ExcemptionAmt == '' || $ExcemptionAmt == ' '){
					$ExcemptionAmt = '0.00';
				}
				
				$obj= [];
				$obj['PropertyClassName']=$PropertyClassName;
				$obj['UniqueID']=$UniqueID;
				$obj['TaxID']=$TaxID;
				$obj['Taxtype']=$type;
				$obj['DocumentTypeName']=$dtype;
				$obj['Date']=$Date;
				$obj['NextDueDate'] = $NextDueDate;
				$obj['Paidthro'] = $ddate;
				$obj['TotalTax']=$TotalTax;
				$obj['TaxYear']=$TaxYear;
				$obj['TotalTaxPaid']=$TotalTaxPaid;
				$obj['AmountDelinquent']=$damt;
				$obj['DelinquentYear']=substr($ddate,0,4);
				$obj['Excemption']=$Excemption;
				$obj['ExcemptionAmount']=$ExcemptionAmt;
				$obj['TaxStatus']=$satus;
				$obj['TaxBasic']=$basic;
				$obj['Comment']=$Comment;
				
				array_push($FinalTaxListDetails, $obj);		
				unset($obj);		
			}
			
			$Legal = $this->GetLegalDescription($OrderUID);

			$last_char = substr($Legal, -1); 

			if($last_char !== "."){
				$Legal = $Legal.".";
			}

			$Legaldes = [];
			if($Legal!='')
			{
		      $Legaldes['Description'] = $this->clean($Legal);
			}

			$Liens = $this->GetLiensDetails($OrderUID);  
			//$Judgement = $this->GetJudgementDetails($OrderUID); 
			$PropertyInfo = $this->GetPropertyDetails($OrderUID);
			$Assessment = $this->GetAssessmentDetails($OrderUID);

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

			$FinalJudgmentListDetails = [];
			$JudgmentListDetails = $this->GetJudgmentListDetails($OrderUID);

			foreach ($JudgmentListDetails as $key => $values) {

				$JudgementSNo = $values->JudgementSNo;
				$JudgementCaseNo = $values->JudgementCaseNo;

				$Judg_Book = '';
                $Judg_Page = '';
                $Judg_DocumentNo = '';
                $Judg_CaseNo = '';

                if($values->JudgementCaseNo){
					$Judg_CaseNo = $values->JudgementCaseNo;
				}elseif($values->Judgement_DBVTypeUID_1=='3'){
                    $Judg_CaseNo = $values->Judgement_DBVTypeValue_1;
                }elseif($values->Judgement_DBVTypeUID_2=='3'){
                    $Judg_CaseNo = $values->Judgement_DBVTypeValue_2;
                }


                if($values->Judgement_DBVTypeUID_1=='7'){
                    $Judg_Instrument = $values->Judgement_DBVTypeValue_1;
                }elseif($values->Judgement_DBVTypeUID_2=='7'){
                    $Judg_Instrument = $values->Judgement_DBVTypeValue_2;
                }

                if($values->Judgement_DBVTypeUID_1=='2'){
					$Judg_BookPage = $values->Judgement_DBVTypeValue_1;
					$Res = explode('/',$Judg_BookPage);
					$Judg_Book = $Res[0];
					$Judg_Page = $Res[1];					
				}elseif($values->Judgement_DBVTypeUID_2=='2'){
					$Judg_BookPage = $values->Judgement_DBVTypeValue_2;
					$Res = explode('/',$Judg_BookPage);
					$Judg_Book = $Res[0];
					$Judg_Page = $Res[1];					
				}

				if($values->Judgement_DBVTypeUID_1=='1'){
					$Judg_Book = $values->Judgement_DBVTypeValue_1;
				}elseif($values->Judgement_DBVTypeUID_2=='1'){
					$Judg_Book = $values->Judgement_DBVTypeValue_2;
				}

				if($values->Judgement_DBVTypeUID_1=='14'){
					$Judg_Page = $values->Judgement_DBVTypeValue_1;
				}elseif($values->Judgement_DBVTypeUID_2=='14'){
					$Judg_Page = $values->Judgement_DBVTypeValue_2;
				}

				if($values->Judgement_DBVTypeUID_1=='6'){
					$Judg_DocumentNo = $values->Judgement_DBVTypeValue_1;
				}elseif($values->Judgement_DBVTypeUID_2=='6'){
					$Judg_DocumentNo = $values->Judgement_DBVTypeValue_2;
				}

				$obj= [];
				$obj['Page']=$Judg_Page;
				$obj['Book']=$Judg_Book;
				$obj['Instrument']=$Judg_Instrument;
				$obj['JudgementCaseNo']=$Judg_CaseNo;
				$obj['DocumentNo']=$Judg_DocumentNo;
				$obj['Amount']=$values->JudgementAmount;
				$obj['Date']=$values->JudgementDated;
				$obj['RecordingDate']=$values->JudgementRecorded;
				$obj['JudgementFiled']=$values->JudgementFiled;
				$obj['JudgementExceptionOnPolicy']=$values->JudgementExceptionOnPolicy;
				$obj['Comment']=$values->JudgementComments;
				$obj['DocumentTypeName']=$values->DocumentTypeName;
				$obj['Plaintiff']=$values->Plaintiff;
				$obj['PlaintiffAttorney']=$values->PlaintiffAttorney;
				$obj['Defendent']=$values->Defendent;
				$obj['DefendentAttorney']=$values->DefendentAttorney;

				array_push($FinalJudgmentListDetails, $obj);
				unset($obj);
			}

			$col_name = array();
	    	$col_val = array();

			if($Is_Api)
			{
				$url_send = $this->config->item("api_url");

				$FinalProductDetails = $this->GetFinalReportToAPI($OrderUID);

				if($SourceName=='RealEC')
				{
 			      array_push($col_name, 'EventCode');
		    	  array_push($col_val, 385);					
				} else if($SourceName=='Stewart') {
 			    	if($this->input->post('ExceptionClear'))
					{
						array_push($col_name, 'EventCode');
						array_push($col_val, 'product_dispute_reply');  

						$DisputeReplyDetails = $this->input->post('DisputeReplyDetails'); 

						array_push($col_name, 'DisputeReplyDetails');
						array_push($col_val, $DisputeReplyDetails); 
						      
					} else {
						array_push($col_name, 'EventCode');
						array_push($col_val, 'product_reply');
					}	
				} else if($SourceName=='LoansPQ') {
					array_push($col_name, 'EventCode');
					array_push($col_val, 'mortgage_info');	
				} else if($SourceName=='Keystone') {
					/*if ($Orderdetailsss->IsClosingProduct) {
						array_push($col_name, 'EventCode');
						array_push($col_val, 'CompletedSigning');						
					} else {
						array_push($col_name, 'EventCode');
						array_push($col_val, 'ClearedTitle');
					}*/
				} else if($SourceName=='Pabs') {

					$pabs_data = ['InBoundUID' => $InBoundUID, 'TransactionID' => $TransactionID, 'EventCode' =>'AT15', 'ApiOrderRequestUID' => $ApiOrderRequestUID, 'OrderUID' => $OrderUID, 'OrderNumber' => $OrderNumber, 'SourceName' => $SourceName];
					
					$this->load->model('api_abstractor/api_abstractor_model');
					$pabs_data['Final'] = $this->api_abstractor_model->getPabsFinalReportData($OrderUID); 

					$str_data = json_encode($pabs_data);
				    $result = $this->sendPostData($url_send, $str_data); 
				    exit;	
				} else if($SourceName=='Westcor') {

					$pabs_data = ['InBoundUID' => $InBoundUID, 'TransactionID' => $TransactionID, 'EventCode' =>'AT15', 'ApiOrderRequestUID' => $ApiOrderRequestUID, 'OrderUID' => $OrderUID, 'OrderNumber' => $OrderNumber, 'SourceName' => $SourceName];
					
					$this->load->model('api_abstractor/api_abstractor_model');
					$pabs_data['Final'] = $this->api_abstractor_model->getPabsFinalReportData($OrderUID); 

					$str_data = json_encode($pabs_data);
				    $result = $this->sendPostData($url_send, $str_data); 
				    exit;	
				}

	    		array_push($col_name, 'InBoundUID');
	    		array_push($col_val, $InBoundUID);

	    		array_push($col_name, 'TransactionID');
	    		array_push($col_val, $TransactionID);

				array_push($col_name, 'OrderDetails');
	    		array_push($col_val, $OrderDetails);

	    		array_push($col_name, 'DeedListDetails');
	    		array_push($col_val, $DeedListDetails);

	    		array_push($col_name, 'MortgageListDetails');
	    		array_push($col_val, $FinalMortgageListDetails);

	    		array_push($col_name, 'TaxListDetails');
	    		array_push($col_val, $FinalTaxListDetails);

	    		array_push($col_name, 'LegalDetails');
	    		array_push($col_val, $Legaldes);

	    		array_push($col_name, 'LiensDetails');
	    		array_push($col_val, $Liens);

	    		array_push($col_name, 'JudgementDetails');
	    		array_push($col_val, $FinalJudgmentListDetails);
	    		
	    		array_push($col_name, 'AssessmentDetails');
	    		array_push($col_val, $Assessment);

	    		array_push($col_name, 'TrustorDetails');
	    		array_push($col_val, $TrustorDetails);

	    		array_push($col_name, 'PropertyInfoDetails');
	    		array_push($col_val, $PropertyInfo);

	    		array_push($col_name, 'AdverseConditions');
	    		array_push($col_val, $AdverseConditions);

	    		array_push($col_name, 'SourceName');
                array_push($col_val, $SourceName);

                array_push($col_name, 'OrderNumber');
                array_push($col_val, $OrderNumber);

                array_push($col_name, 'OrderUID');
                array_push($col_val, $OrderUID);

                array_push($col_name, 'ApiOrderRequestUID');
                array_push($col_val, $ApiOrderRequestUID);

	    		$column_name = $col_name;
			    $column_value = $col_val;   
			    $data = array_combine($column_name, $column_value);
  			    //$str_data = json_encode($data);
  			    $str_data = json_encode($data);
				$result = $this->sendPostData($url_send, $str_data); 			
			}	
		}

		function GetLegalDescription($OrderUID)
	    {
	    	$this->db->where('OrderUID',$OrderUID);
	    	$q = $this->db->get('torderlegaldescription')->row();
	    	return $q->LegalDescription;
	    }

	    function GetLiensDetails($OrderUID)
	    {
	        $this->db->select("*");
			$this->db->from('torderleins');
			$this->db->join('mdocumenttypes','torderleins.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
			$this->db->join('mmortgagedbvtypes','torderleins.Lien_DBVTypeUID_1=mmortgagedbvtypes.DBVTypeUID','left');
			$this->db->where(array("torderleins.OrderUID"=>$OrderUID));    
			$query = $this->db->get();
			return $query->result_array();
	    }

	    function GetJudgementDetails($OrderUID)
	    {
	       $this->db->select("*");
		   $this->db->from('torderjudgements'); 
			$this->db->join('torderjudgementparties','torderjudgementparties.JudgementSNo=torderjudgements.JudgementSNo','left');
			$this->db->join('morderpartytypes','torderjudgementparties.PartyTypeUID=morderpartytypes.PartyTypeUID','left');
			$this->db->join('mpropertyroles','torderjudgementparties.PropertyRoleUID=mpropertyroles.PropertyRoleUID','left');
		   $this->db->join('mdocumenttypes','torderjudgements.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
		   $this->db->where(array("torderjudgements.OrderUID"=>$OrderUID)); 
		   $this->db->group_by('torderjudgements.JudgementSNo');
		   $query = $this->db->get();
		   return $query->result_array();
	    }

	    function GetPropertyDetails($OrderUID)
		{
			$this->db->select("*");
			$this->db->from('torderpropertyinfo');
			$this->db->join('mcounties','torderpropertyinfo.IndependentCountyUID=mcounties.CountyUID','left');
			$this->db->join('mcities','torderpropertyinfo.City=mcities.CityUID','left');
			$this->db->join('mpropertyclass','torderpropertyinfo.PropertyClassUID=mpropertyclass.PropertyClassUID','left');
			$this->db->join('mpropertyuse','torderpropertyinfo.PropertyUseUID=mpropertyuse.PropertyUseUID','left');
			$this->db->join('mmaritalstatus','torderpropertyinfo.MaritalStatusUID=mmaritalstatus.MaritalStatusUID','left');
			$this->db->where(array("torderpropertyinfo.OrderUID"=>$OrderUID));    
			$this->db->group_by('torderpropertyinfo.OrderUID'); 
			$query = $this->db->get();
			return $query->result_array();
		}

		function GetAssessmentDetails($OrderUID)
		{
			$this->db->select("*");
			$this->db->from('torderassessment');
			$this->db->where(array("torderassessment.OrderUID"=>$OrderUID));
			$query = $this->db->get();
			return $query->row();
		}

		// Get Order Details Starts //

			function GetOrderDetails($OrderUID)
			{
				if($OrderUID){
					$this->db->select ( '*,torderpropertyinfo.APN as PINAPN,torders.OrderNumber as OrderNumber,msubproducts.SubProductName, mproducts.ProductName,torders.OrderUID AS OrderUID' ); 
					$this->db->from ( 'torders' );
					$this->db->join ( 'msubproducts', 'torders.SubProductUID = msubproducts.SubProductUID' , 'left' );
					$this->db->join ( 'mproducts', 'msubproducts.ProductUID = mproducts.ProductUID' , 'left' );
					$this->db->join ( 'torderlegaldescription', 'torderlegaldescription.OrderUID = torders.OrderUID' , 'left' );
					$this->db->join ( 'torderpropertyinfo', 'torderpropertyinfo.OrderUID = torders.OrderUID' , 'left' );
					$this->db->join ( 'torderdocuments', 'torderdocuments.OrderUID = torders.OrderUID' , 'left' );
					$this->db->join ( 'torderaddress', 'torderaddress.OrderUID = torders.OrderUID' , 'left' );
					$this->db->join ( 'tApiOutBoundOrders', 'tApiOutBoundOrders.OrderUID = torders.OrderUID' , 'left' );
					$this->db->where ('torders.OrderUID',$OrderUID);
					$this->db->group_by('torderdocuments.OrderUID'); 
					$query = $this->db->get();
					$OrderDetails = $query->row();

					$data = json_encode($OrderDetails);

					return $OrderDetails;
				}
			}

		// Get Order Details Ends //

		// Get DeedList Details Starts //

			function GetDeedListDetails($OrderUID)
			{
				$DeedDetails= $this->GetDeedDetails($OrderUID);

				$Grantorkey = $this->config->item('PartyTypeUID')['Grantor'];
				$Granteekey = $this->config->item('PartyTypeUID')['Grantee'];
				$Grantor = NULL;
				$Grantee = NULL;
				$all = [];

				foreach ($DeedDetails as $key => $value) {
					
					$all[$key]['Grantor'] = $this->get_partytypes($value->OrderUID,$Grantorkey,$value->DeedSNo);
					$all[$key]['Grantee'] = $this->get_partytypes($value->OrderUID,$Granteekey,$value->DeedSNo);
				}

				foreach ($DeedDetails as $key => $value) {

					foreach ($all[$key]['Grantor'] as $keys => $values) {

						if($values->DeedSNo == $values->DeedSNo){
							$Grantor[] = $this->clean($values->PartyName);
						}
					}

					foreach ($all[$key]['Grantee'] as $key1 => $value1) {
						if($value1->DeedSNo == $value->DeedSNo){
							$Grantee[] = $this->clean($value1->PartyName);
						}
					}

					$Grantor1 = implode(',', $Grantor); 
					$Grantee1 = implode(',', $Grantee);
					$DeedDetails[$key]->Grantor = $Grantor1; 
					$DeedDetails[$key]->Grantee = $Grantee1;
					$Grantor = [];
					$Grantee =[];
				}

				$data = json_encode($DeedDetails);

				return $DeedDetails;
			}

			function GetDeedDetails($OrderUID)
			{
				$Deed = $this->config->item('DocumentTypeUID')['Deeds'];

				$this->db->select("*");
				$this->db->from('torderdeeds');
				$this->db->join('mdocumenttypes','torderdeeds.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
				$this->db->join('mestateinterests','torderdeeds.EstateInterestUID=mestateinterests.EstateInterestUID','left');
				$this->db->join('mtenancytype','torderdeeds.TenancyUID=mtenancytype.TenancyUID','left');
				$this->db->where(array("torderdeeds.OrderUID"=>$OrderUID));  
				$this->db->order_by('torderdeeds.DeedPosition',"ASC");
				$query = $this->db->get();
				return $query->result();
			}

			function get_partytypes($OrderUID = '',$PartyTypeUID = '',$DeedSNo = '')
			{
				$this->db->select("*"); 
				$this->db->from('torderdeedparties');
				$this->db->where(array("torderdeedparties.OrderUID"=>$OrderUID,"PartyTypeUID"=>$PartyTypeUID,"DeedSNo"=>$DeedSNo));
				$query = $this->db->get();
				return $query->result();	
			}

		// Get DeedList Details Ends //

		// Get MortgageList Details Starts //

			function GetMortgageListDetails($OrderUID){

				$MortgageDetails= $this->GetMortgageDetails($OrderUID);

				$Mortgagorkey = $this->config->item('PartyTypeUID')['Mortgagor'];
				$Mortgageekey = $this->config->item('PartyTypeUID')['Mortgagee'];
				$Mortgagor = NULL;
				$Mortgagee = NULL;
				$mort = [];
				foreach ($MortgageDetails as $key => $value) {
					
					$mort[$key]['Mortgagor'] = $this->get_partytypes_mort($value->OrderUID,$Mortgagorkey,$value->MortgageSNo);
					$mort[$key]['Mortgagee'] = $this->get_partytypes_mort($value->OrderUID,$Mortgageekey,$value->MortgageSNo);
				}

				foreach ($MortgageDetails as $key => $value) {

					foreach ($mort[$key]['Mortgagor'] as $keys => $values) {

						if($values->MortgageSNo == $values->MortgageSNo){
							$Mortgagor[] = $this->clean($values->PartyName);
						}
					}

					foreach ($mort[$key]['Mortgagee'] as $key1 => $value1) {
						if($value1->MortgageSNo == $value->MortgageSNo){
							$Mortgagee[] = $this->clean($value1->PartyName);
						}
					}

					$Mortgagor1 = implode(',', $Mortgagor); 
					$Mortgagee1 = implode(',', $Mortgagee);
					$MortgageDetails[$key]->Mortgagor = $Mortgagor1; 
					$MortgageDetails[$key]->Mortgagee = $Mortgagee1;
					$Mortgagor = [];
					$Mortgagee = [];

				}

				$data = json_encode($MortgageDetails);

				return $MortgageDetails;
			}

			function GetMortgageDetails($OrderUID)
			{
				$Mortgages = $this->config->item('DocumentTypeUID')['Mortgages'];

				$this->db->select("tordermortgages.*,mmortgagedbvtypes.*,mlientypes.*,mcustomers.CustomerName");
				$this->db->from('tordermortgages');
				$this->db->join('mdocumenttypes','tordermortgages.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
				$this->db->join('mlientypes','tordermortgages.LienTypeUID=mlientypes.LienTypeUID','left');
				$this->db->join('mmortgagedbvtypes','tordermortgages.Mortgage_DBVTypeUID_1=mmortgagedbvtypes.DBVTypeUID','left');
				$this->db->join('torders','tordermortgages.OrderUID=torders.OrderUID','left');
				$this->db->join('mcustomers','mcustomers.CustomerUID=torders.CustomerUID','left');
				// $this->db->join('torderpropertyroles','torderpropertyroles.OrderUID=torders.OrderUID','left');
				$this->db->where(array("tordermortgages.OrderUID"=>$OrderUID));
				$this->db->group_by("tordermortgages.MortgageSNo");        
				$query = $this->db->get();
				return $query->result();
			}

			function GetPropertyRoles($OrderUID)
			{
				$this->db->select("*");
				$this->db->from('torderpropertyroles');
				$this->db->where(array("torderpropertyroles.OrderUID"=>$OrderUID));       
				$query = $this->db->get();
				return $query->result();
			}	

			function get_partytypes_mort($OrderUID = '',$PartyTypeUID = '',$MortgageSNo = '')
			{
				$this->db->select("*"); 
				$this->db->from('tordermortgageparties');
				$this->db->where(array("tordermortgageparties.OrderUID"=>$OrderUID,"PartyTypeUID"=>$PartyTypeUID,"MortgageSNo"=>$MortgageSNo));
				$query = $this->db->get();
				return $query->result();	
			}

		// Get MortgageList Details Ends //

		// Get TaxList Details Starts //

			function GetTaxListDetails($OrderUID)
			{
				$Mortgages = $this->config->item('DocumentTypeUID')['Mortgages'];
				$this->db->select("*");
				$this->db->from('tordertaxcerts');
				$this->db->join('msubdocumentmortgages', 'tordertaxcerts.DocumentTypeUID=msubdocumentmortgages.DocumentCategoryUID', 'left');
				$this->db->join('mtaxcertbasis', 'tordertaxcerts.TaxBasisUID=mtaxcertbasis.TaxBasisUID', 'left');
				$this->db->join('mpropertyclass', 'tordertaxcerts.PropertyClassUID=mpropertyclass.PropertyClassUID', 'left');
				// $this->db->join('mpropertyuse', 'tordertaxcerts.PropertyUseUID=mpropertyuse.PropertyUseUID', 'left');
				$this->db->where(array("tordertaxcerts.OrderUID"=>$OrderUID));       
				$query = $this->db->get();
				$Result = $query->result();
				return $Result;
			}

			function GetTaxInstallmentDetails($OrderUID, $TaxCertSNo){
				$this->db->select("tordertaxinstallment.*,mtaxstatus.*");
				$this->db->from('tordertaxinstallment');
				$this->db->join('mtaxstatus', 'tordertaxinstallment.TaxStatusUID=mtaxstatus.TaxStatusUID', 'left');
				$this->db->join('tordertaxcerts','tordertaxinstallment.OrderUID=tordertaxcerts.OrderUID','left');
				$this->db->where(array("tordertaxinstallment.OrderUID"=>$OrderUID,"tordertaxinstallment.TaxCertSNo"=>$TaxCertSNo)); 
				//$this->db->group_by('OrderUID');
				$query = $this->db->get();
				return $query->result();		
			}



		// Get TaxList Details Ends //

			function GetJudgmentListDetails($OrderUID){

				$JudgmentDetails= $this->GetJudgmentDetails($OrderUID);

				$Plaintiffkey = $this->config->item('PartyTypeUID')['Plaintiff'];
				$Defendentkey = $this->config->item('PartyTypeUID')['Defendent'];
				$Plaintiff = NULL;
				$PlaintiffAttorney = NULL;
				$Defendent = NULL;
				$DefendentAttorney = NULL;
				$judg = [];
				foreach ($JudgmentDetails as $key => $value) {
					
					$judg[$key]['Plaintiff'] = $this->get_partytypes_judg($value->OrderUID,$Plaintiffkey,$value->JudgementSNo);
					$judg[$key]['Defendent'] = $this->get_partytypes_judg($value->OrderUID,$Defendentkey,$value->JudgementSNo);
				}

				foreach ($JudgmentDetails as $key => $value) {

					foreach ($judg[$key]['Plaintiff'] as $keys => $values) {

						if($values->JudgementSNo == $values->JudgementSNo){
							$Plaintiff[] = $this->clean($values->PartyName);
							$PlaintiffAttorney[] = $this->clean($values->Attorney);
						}
					}

					foreach ($judg[$key]['Defendent'] as $key1 => $value1) {
						if($value1->JudgementSNo == $value->JudgementSNo){
							$Defendent[] = $this->clean($value1->PartyName);
							$DefendentAttorney[] = $this->clean($value1->Attorney);
						}
					}

					$Plaintiff1 = implode(',', $Plaintiff); 
					$PlaintiffAttorney1 = implode(',', $PlaintiffAttorney); 
					$Defendent1 = implode(',', $Defendent);
					$DefendentAttorney1 = implode(',', $DefendentAttorney); 
					$JudgmentDetails[$key]->Plaintiff = $Plaintiff1; 
					$JudgmentDetails[$key]->PlaintiffAttorney = $PlaintiffAttorney1;
					$JudgmentDetails[$key]->Defendent = $Defendent1;
					$JudgmentDetails[$key]->DefendentAttorney = $DefendentAttorney1;
					$Plaintiff = [];
					$PlaintiffAttorney = [];
					$Defendent = [];
					$DefendentAttorney = [];

				}

				$data = json_encode($JudgmentDetails);

				return $JudgmentDetails;
			}

			function GetJudgmentDetails($OrderUID)
			{
				$this->db->select("torderjudgements.*,mmortgagedbvtypes.*,mdocumenttypes.*");
				$this->db->from('torderjudgements');
				$this->db->join('mdocumenttypes','torderjudgements.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
				$this->db->join('mmortgagedbvtypes','torderjudgements.Judgement_DBVTypeUID_1=mmortgagedbvtypes.DBVTypeUID','left');
				$this->db->join('torders','torderjudgements.OrderUID=torders.OrderUID','left');
				$this->db->where(array("torderjudgements.OrderUID"=>$OrderUID));
				$this->db->group_by("torderjudgements.JudgementSNo");        
				$query = $this->db->get();
				return $query->result();
			}

			function get_partytypes_judg($OrderUID = '',$PartyTypeUID = '',$JudgementSNo = '')
			{
				$this->db->select("*"); 
				$this->db->from('torderjudgementparties');
				$this->db->where(array("torderjudgementparties.OrderUID"=>$OrderUID,"PartyTypeUID"=>$PartyTypeUID,"JudgementSNo"=>$JudgementSNo));
				$query = $this->db->get();
				return $query->result();	
			}


	// Event Code 385 Function Ends //


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
                	'CustomerAmount' => $CustomerAmount
				);

				//$str_data = json_encode($data);
				$str_data = json_encode($data);

				$result = $this->sendPostData($url_send, $str_data);
			}
		}

	// Event Code 240 - Cancel Api Order Function Ends //

	// Event Code 230 - On-Hold Api Order Function Starts //

		function OnHoldApiOrder($OrderUID,$OnHoldComments){

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

			$SourceName = $this->GetSourceName($OrderUID); 
	    	$OrderSourceName = $SourceName->OrderSourceName;

	    	$SourceName = trim($OrderSourceName);

	    	if($SourceName=="RealEC") {
		      $Action = 230;
		    } else if($SourceName=="Stewart") { 
		      $Action = 'additional_info_request';
		    } 

			if($Is_Api){

				$url_send = $this->config->item("api_url");

				$data = array(
					'EventCode' => $Action,
					'OrderUID' => $OrderUID,
					'InBoundUID' => $InBoundUID,
					'TransactionID' => $TransactionID,
					'OnHoldComments' => $this->clean($OnHoldComments),
					'SourceName' => $SourceName
				);

				//$str_data = json_encode($data);
				$str_data = json_encode($data);

				$result = $this->sendPostData($url_send, $str_data);
			}
		}

	// Event Code 230 - On-Hold Api Order Function Ends //

	// Event Code 260 - Resume On-Hold  Api Order Function Starts //

		function ResumeOnHoldApiOrder($OrderUID,$StopRemarks){

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

			$SourceName = $this->GetSourceName($OrderUID); 
	    	$OrderSourceName = $SourceName->OrderSourceName;

	    	$SourceName = trim($OrderSourceName);

			if($Is_Api){

				$url_send = $this->config->item("api_url");

				$data = array(
					'EventCode' => 260,
					'InBoundUID' => $InBoundUID,
					'OrderUID' => $OrderUID,
					'TransactionID' => $TransactionID,
					'ResumeOnHoldComments' => $this->clean($StopRemarks),
					'SourceName' => $SourceName
				);

				$str_data = json_encode($data);

				$result = $this->sendPostData($url_send, $str_data);

				
			}
		}


	// Event Code 260 - Resume On-Hold Api Order Function Ends //

	// Event Code 220 -Comment Api Order Function Starts //

		function CommentApiOrder($OrderUID,$Comment){

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

			$SourceName = $this->GetSourceName($OrderUID); 
	    	$OrderSourceName = $SourceName->OrderSourceName;

	    	$SourceName = trim($OrderSourceName);

	    	if($SourceName=="RealEC") {
		      $Action = 220;
		    } else if($SourceName=="Stewart") { 
		      $Action = 'note';
		    } else if($SourceName=="LoansPQ") { 
		      $Action = 'internal_comments';
		    } 
		    /* Desc: Westcor Comment sent to API @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
		    else if($SourceName=="Westcor") 
		    { 
		      $Action = 'AT05';
		    }

			if($Is_Api){

				$url_send = $this->config->item("api_url");

				$data = array(
					'EventCode' => $Action,
					'InBoundUID' => $InBoundUID,
					'OrderUID' => $OrderUID,
					'TransactionID' => $TransactionID,
					'Comment' => $this->clean($Comment),
					'SourceName' => $SourceName
				);

				//$str_data = json_encode($data);
				$str_data = json_encode($data);

				$result = $this->sendPostData($url_send, $str_data);
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

			$SourceName = $this->GetSourceName($OrderUID); 
	    	$OrderSourceName = $SourceName->OrderSourceName;

	    	$SourceName = trim($OrderSourceName);

			$ActionComment = str_replace("<b>ACTION</b> - ",'',$Comment);

	    	if($SourceName=="RealEC") {
		      $Action = 222;
		    } else if($SourceName=="Stewart") { 
		      $Action = 'note';
		    } 

			if($Is_Api){

				$url_send = $this->config->item("api_url");

				$data = array(
					'EventCode' => $Action,
					'OrderUID' => $OrderUID,
					'InBoundUID' => $InBoundUID,
					'TransactionID' => $TransactionID,
					'Comment' => $this->clean($ActionComment),
					'SourceName' => $SourceName
				);

				//$str_data = json_encode($data);
				$str_data = json_encode($data);

				$result = $this->sendPostData($url_send, $str_data);
			}
		}
	// Event Code 222 - Comment Action Api Order Function Ends //

	// Event Code 270 - Complete Api Order Function Starts //

		function CompleteApiOrder($OrderUID){

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

			$SourceName = $this->GetSourceName($OrderUID); 
	    	$OrderSourceName = $SourceName->OrderSourceName;

	    	$SourceName = trim($OrderSourceName);

	    	if($SourceName=="RealEC") {
		      $Action = 270;
		    } else if($SourceName=="LoansPQ") { 
		      $Action = 'status_complete';
		    } 

			if($Is_Api){

				$url_send = $this->config->item("api_url");

				$data = array(
					'EventCode' => $Action,
					'OrderUID' => $OrderUID,
					'InBoundUID' => $InBoundUID,
					'TransactionID' => $TransactionID,
					'SourceName' => $SourceName
				);

				//$str_data = json_encode($data);
				$str_data = json_encode($data);

				$result = $this->sendPostData($url_send, $str_data);				
			}
		}

	// Event Code 270 - Complete Api Order Function Ends //

	// Event Code 180 - Attachments Api Order Function Starts //

		function SendAttachmentsToApi(){

			error_reporting(1);
			$OrderUID = $this->input->post('OrderUID');

			$OrderNumbers = $this->GetOrderDetails($OrderUID);
			$OrderNumber = $OrderNumbers->OrderNumber;

			$TotalFiles = $this->input->post('TotalFiles');
			$DocumentFileName = $this->input->post('documentfilenames');

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

			$SourceName = $this->GetSourceName($OrderUID);  
	    	$OrderSourceName = $SourceName->OrderSourceName;
	    	$OrderSourceUID = $SourceName->OrderSourceUID;

	    	$SourceName = trim($OrderSourceName);

			$col_name = array();
	    	$col_val = array();

			if($Is_Api){

				$url_send = $this->config->item("api_url");			

				$Documents=[];

				$attachment_doc = array(); //@Desc Attachment Doc set array
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

					$attachment_doc[$OrderDocsPath . $FileName] =  $FileName; //@Desc Storing Attachments
					/**
					* @purpose To get the doctype and typeofdocument
					*
					* @param  OrderUID
					* 
					* @author Yagavi G <yagavi.g@avanzegroup.com>
					* @return TypeOfDocument, DocTypeCode
					* @since April 1 2020
					*
					*/

					$DocTypeCode = $OrderDocDetails->TypeOfDocument;
					$TypeOfDocument = $OrderDocDetails->TypeOfDocument;
					
					$DocumentTypeCode = $this->common_model->GetDocumentTypeCodeByUID($OrderDocDetails->TypeOfDocument);
					if(!empty($DocumentTypeCode)){
						$TypeOfDocument = $DocumentTypeCode;
					} else {
						$DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $OrderSourceUID);
						$TypeOfDocument = $this->common_model->GetDocumentTypeCodeByUID($DocumentTypeUID);
					}

					if($SourceName=='Keystone') {
						$KeystoneDocTypes = $this->config->item('KeystoneDocTypes');
						if(!empty($KeystoneDocTypes)){
							$TypeOfDocument = $KeystoneDocTypes[$TypeOfDocument];
						} else {
							$DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $OrderSourceUID);
							$TypeOfDocument = $this->common_model->GetDocumentTypeCodeByUID($DocumentTypeUID);
						}
					}

					$obj= [];
					$obj['OrderDocsPath']=$OrderDocsPath;
					$obj['DocumentFileName']=$FileName;
					$obj['TemplateName']=$TemplateName;
					$obj['DocumentCreatedDate']=$DocumentCreatedDate;
					$obj['Content']=$Content;
					$obj['DocTypeCode']=$TypeOfDocument;
					$obj['DocTypeName']=$TypeOfDocument;

					/* Desc: PabsOrder Comment sent to API @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
					if($SourceName=='Pabs') {
						$file_parts = pathinfo(FCPATH.$OrderDocsPath.$FileName);
						$obj['FileType'] = strtoupper($file_parts['extension']);			
					}
                    /* Desc: Westcor Comment sent to API @author Shivaraj N <shivaraja.n@avanzegroup.com> @since Sept 11th 2020 */
					if($SourceName=='Westcor') {
						$file_parts = pathinfo(FCPATH.$OrderDocsPath.$FileName);
						$obj['FileType'] = strtoupper($file_parts['extension']);			
					}

					array_push($Documents, $obj);
					unset($obj);

				}

				if($SourceName=='RealEC') {
					array_push($col_name, 'EventCode');
					array_push($col_val, 180);					
				} else if($SourceName=='Keystone') {
					array_push($col_name, 'EventCode');
					array_push($col_val, 'AddAttachment');	
				} else if($SourceName=='Pabs') {
					array_push($col_name, 'EventCode');
					array_push($col_val, 'AT14');	
				} else if($SourceName=='Westcor') {
					array_push($col_name, 'EventCode');
					array_push($col_val, 'AT14');	
				}

	    		array_push($col_name, 'InBoundUID');
	    		array_push($col_val, $InBoundUID);

	    		array_push($col_name, 'TransactionID');
	    		array_push($col_val, $TransactionID);

				array_push($col_name, 'Documents');
	    		array_push($col_val, $Documents);

	    		array_push($col_name, 'OrderNumber');
	    		array_push($col_val, $OrderNumber);

	    		array_push($col_name, 'OrderUID');
	    		array_push($col_val, $OrderUID);

	    		array_push($col_name, 'SourceName');
	    		array_push($col_val, $SourceName);

	    		array_push($col_name, 'ApiOrderRequestUID');
	    		array_push($col_val, $ApiOrderRequestUID);

	    		$column_name = $col_name;
			    $column_value = $col_val;   

			    $data = array_combine($column_name, $column_value);

			    $this->load->model('api_abstractor/api_abstractor_model');
			    $data['Final'] = $this->api_abstractor_model->getPabsFinalReportData($OrderUID); 

				$str_data = json_encode($data);

				/*@Desc Audit Log @Author Jainulabdeen @Added On Apr 15 2020*/
				$L_name = md5(Date('Y-m-d H:i:s'));
				$desFolder=FCPATH . 'uploads/DeliveryLogs/';
				if (!is_dir($desFolder)) {
				mkdir($desFolder, 0777, true);
				chmod($desFolder, 0777);
				}
				$LPath = 'uploads/DeliveryLogs/'.$L_name.'.eml';
				$new_str_data = preg_replace('/,/', ', ', $str_data);
				$handle =fopen($LPath,'w');
				fwrite($handle, $new_str_data);
				fclose($handle);

				$Content = json_encode([
				'senttomails'=>$SourceName,
				'documents'=>$attachment_doc,
				'eml_file'=>$LPath
				]);

				$InsetData = array(
				'UserUID' => $this->session->userdata('UserUID'),
				'ModuleName' => 'OrderDeliveryLog',
				'Feature' => $OrderUID,
				'NewValue'=> $new_str_data,
				'Content' => $Content,
				'DateTime' => date('Y-m-d H:i:s'));
				$this->common_model->InsertAuditTrail($InsetData);
				/*Audit Log End*/
				/*@Desc documents send out of D2T @Author Jainulabdeen @On 20-4-2020*/
					$datetime = date('Y-m-d h:i:s');
					foreach ($Documents as $key => $value) {
						$query = $this->db->query("UPDATE torderdocuments set IsAPISend = '1', IsAPISendDateTime='".$datetime."' WHERE  DocumentFileName='".$value['DocumentFileName']."' and OrderUID = '".$OrderUID."' ");
					}
					/*End*/

				$result = $this->sendPostData($url_send, $str_data);
			}
		}

		function GetAttachmentToAPI($OrderUID,$DocFileName)
		{
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

	// Event Code 180 - Attachments Api Order Function Ends //

	// Event Code 150 - Attachments Api Order Function Starts //

		function SendReportsToApi(){

			$OrderUID = $this->input->post('OrderUID');
			$order_details = $this->common_model->get_orderdetails($OrderUID);
			$Orderdetailsss = $order_details;

			$OrderDetails = $this->GetOrderDetails($OrderUID);
      		$OrderNumber = $OrderDetails->OrderNumber;

      		$TotalFiles = $this->input->post('TotalFiles');
      		$DocumentFileName = $this->input->post('documentfilenames');

			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

			$AppSourceName = $this->GetSourceName($OrderUID); 
            $SourceName = trim($AppSourceName->OrderSourceName);
            $OrderSourceUID = trim($AppSourceName->OrderSourceUID);

			$col_name = array();
	    	$col_val = array();

			if($Is_Api){

				$url_send = $this->config->item("api_url");

				$FinalProductDetails = $this->GetFinalReportToAPI($OrderUID);

				$attachment_doc = array(); //@Desc Attachment Doc set array
				foreach($DocumentFileName as $key=>$value) {
					$Documents = [];

					$DocFileName = $value;
					$OrderDocDetails = $this->GetAttachmentToAPI($OrderUID,$DocFileName);

					$OrderNumber = $OrderDocDetails->OrderNumber;
					$OrderDocsPath = $OrderDocDetails->OrderDocsPath;
					$FileName = $OrderDocDetails->DocumentFileName;
					$TemplateName = $OrderDocDetails->TemplateName;
					$DocumentCreatedDate = $OrderDocDetails->DocumentCreatedDate;
					$text = file_get_contents(FCPATH.$OrderDocsPath.$FileName);
					$Content = base64_encode($text);

					$attachment_doc[$OrderDocsPath . $FileName] =  $FileName; //@Desc Storing Attachments
					/**
					* @purpose To get the doctype and typeofdocument
					*
					* @param  OrderUID
					* 
					* @author Yagavi G <yagavi.g@avanzegroup.com>
					* @return TypeOfDocument, DocTypeCode
					* @since April 1 2020
					*
					*/

					$DocTypeCode = $OrderDocDetails->TypeOfDocument;
					$TypeOfDocument = $OrderDocDetails->TypeOfDocument;

					$DocumentTypeCode = $this->common_model->GetDocumentTypeCodeByUID($OrderDocDetails->TypeOfDocument);
					if(!empty($DocumentTypeCode)){
						$TypeOfDocument = $DocumentTypeCode;
					} else {
						$DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $OrderSourceUID);
						$TypeOfDocument = $this->common_model->GetDocumentTypeCodeByUID($DocumentTypeUID);
					}

					if($SourceName=='Keystone') {
						$KeystoneDocTypes = $this->config->item('KeystoneDocTypes');
						if(!empty($KeystoneDocTypes)){
							$TypeOfDocument = $KeystoneDocTypes[$TypeOfDocument];
						} else {
							$DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $OrderSourceUID);
							$TypeOfDocument = $this->common_model->GetDocumentTypeCodeByUID($DocumentTypeUID);
						}
					}

					$obj= [];
					$obj['DocumentFileName']=$FileName;
					$obj['TemplateName']=$TemplateName;
					$obj['DocumentCreatedDate']=$DocumentCreatedDate;
					$obj['Content']=$Content;
					$obj['DocTypeCode']=$TypeOfDocument;
					$obj['DocTypeName']=$TypeOfDocument;

					array_push($Documents, $obj);
					unset($obj);

					if($SourceName=='RealEC') {
						array_push($col_name, 'EventCode');
						array_push($col_val, 150);					
					} else if($SourceName=='Stewart') {
						array_push($col_name, 'EventCode');
						array_push($col_val, 'product_reply_report');	
					} else if($SourceName=='LoansPQ') {
						array_push($col_name, 'EventCode');
						array_push($col_val, 'loanspq_pdf_reply');	
					} else if($SourceName=='Keystone') {
						if ($Orderdetailsss->IsClosingProduct) {
							/* Closing Order */
							array_push($col_name, 'EventCode');
							array_push($col_val, 'DeliverFinalSS');
							//array_push($col_val, 'AttorneyDocsFinalized');
						} else {
							/* Title Order */
							array_push($col_name, 'EventCode');
							array_push($col_val, 'DeliverProduct');
						}
					} else if($SourceName=='Pabs') {
						array_push($col_name, 'EventCode');
						array_push($col_val, 'AT14');	
					} else if($SourceName=='Westcor') {
						array_push($col_name, 'EventCode');
						array_push($col_val, 'AT14');	
					}

		    		array_push($col_name, 'InBoundUID');
		    		array_push($col_val, $InBoundUID);

		    		array_push($col_name, 'TransactionID');
		    		array_push($col_val, $TransactionID);

					array_push($col_name, 'Documents');
		    		array_push($col_val, $Documents);

		    		array_push($col_name, 'OrderNumber');
	    			array_push($col_val, $OrderNumber);

	    			array_push($col_name, 'OrderUID');
	    			array_push($col_val, $OrderUID);

		    		array_push($col_name, 'SourceName');
               	    array_push($col_val, $SourceName);

               	    array_push($col_name, 'OrderDetails');
               	    array_push($col_val, $OrderDetails);

               	    array_push($col_name, 'ApiOrderRequestUID');
               	    array_push($col_val, $ApiOrderRequestUID);

               	    array_push($col_name, 'OrderUID');
               	    array_push($col_val, $OrderUID);

		    		$column_name = $col_name;
				    $column_value = $col_val;   

				    $data = array_combine($column_name, $column_value);

				    $str_data = json_encode($data);

					/*@Desc Audit Log @Author Jainulabdeen @Added On Apr 15 2020*/
					$L_name = md5(Date('Y-m-d H:i:s'));
					$desFolder=FCPATH . 'uploads/DeliveryLogs/';
					if (!is_dir($desFolder)) {
					    mkdir($desFolder, 0777, true);
					    chmod($desFolder, 0777);
				    }
					$LPath = 'uploads/DeliveryLogs/'.$L_name.'.eml';
					$new_str_data = preg_replace('/,/', ', ', $str_data);
					$handle =fopen($LPath,'w');
					fwrite($handle, $new_str_data);
					fclose($handle);

					$Content = json_encode([
					'senttomails'=>$SourceName,
					'documents'=>$attachment_doc,
					'eml_file'=>$LPath
					]);

					$InsetData = array(
					'UserUID' => $this->session->userdata('UserUID'),
					'ModuleName' => 'OrderDeliveryLog',
					'Feature' => $OrderUID,
					'NewValue'=> $new_str_data,
					'Content' => $Content,
					'DateTime' => date('Y-m-d H:i:s'));
					$this->common_model->InsertAuditTrail($InsetData);
					/*Audit Log End*/
					/*@Desc documents send out of D2T @Author Jainulabdeen @On 20-4-2020*/
					$datetime = date('Y-m-d h:i:s');
					foreach ($Documents as $key => $value) {
						$query = $this->db->query("UPDATE torderdocuments set IsAPISend = '1', IsAPISendDateTime='".$datetime."' WHERE  DocumentFileName='".$value['DocumentFileName']."' and OrderUID = '".$OrderUID."' ");
					}
					/*End*/
					$result = $this->sendPostData($url_send, $str_data);

					$fieldArray = array(
				         "IsMailSend"=> 1,
				         "MailSendBy"=>$this->session->userdata('UserUID'),
				         "MailSendDateTime"=>date('Y-m-d H:i:s'),
				      );
				       $this->db->where('OrderUID',$OrderUID);
				       $query = $this->db->update('torders',$fieldArray);

		        }				
			}	
		}

		function GetFinalReportToAPI($OrderUID)
		{
			if($OrderUID){
				$this->db->select ( 'torders.OrderUID,torders.OrderNumber, mtemplates.TemplateName, mtemplates.TemplateUID' ); 
				$this->db->from ( 'torders' );
				$this->db->join ( 'mtemplates', 'torders.TemplateUID = mtemplates.TemplateUID',"left");
				$this->db->where ('torders.OrderUID',$OrderUID);
				$query = $this->db->get();
				$Result = $query->row();
				return $Result;
			}
		}

	// Event Code 150 - Attachments Api Order Function Ends //

	
	// Checking whether the order is API Order or Not //
 
	function CheckApiOrders($OrderUID){
		$this->db->select("*"); 
		$this->db->from('torders');
		$this->db->where(array("torders.APIOrder"=>1,"torders.OrderUID"=>$OrderUID));
		$query = $this->db->get();
		$Is_Api = $query->row();

		return $Is_Api;
	}


	function ApiOrderAccept(){

		$Is_Api = $this->CheckApiOrders($OrderUID);
		$Details = $this->GetInBoundTransactionDetails($OrderUID);
		$InBoundUID = $Details->InBoundUID;
		$TransactionID = $Details->TransactionID;
		$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

		$OrderDetails = $this->GetOrderDetails($OrderUID);
		$OrderNumber = $OrderDetails->OrderNumber;

		$SourceName = $this->GetSourceName($OrderUID); 
    	$OrderSourceName = $SourceName->OrderSourceName;

    	$SourceName = trim($OrderSourceName);

		if($Is_Api){

			$url_send = $this->config->item("api_url");

			$data = array(
				'EventCode' => 130,
				'InBoundUID' => $InBoundUID,
				'TransactionID' => $TransactionID,
				'OrderNumber' => $OrderNumber,
				'OrderUID' => $OrderUID,
				'SourceName' => $SourceName
			);

			//$str_data = json_encode($data);
			$str_data = json_encode($data);

			$result = $this->sendPostData($url_send, $str_data);

			
		}

	}

	function ApiOrderReject(){

		$Is_Api = $this->CheckApiOrders($OrderUID);
		$Details = $this->GetInBoundTransactionDetails($OrderUID);
		$InBoundUID = $Details->InBoundUID;
		$TransactionID = $Details->TransactionID;
		$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

		$SourceName = $this->GetSourceName($OrderUID); 
    	$OrderSourceName = $SourceName->OrderSourceName;

    	$SourceName = trim($OrderSourceName);

		if($Is_Api){

			$url_send = $this->config->item("api_url");

			$data = array(
				'EventCode' => 140,
				'OrderUID' => $OrderUID,
				'InBoundUID' => $InBoundUID,
				'TransactionID' => $TransactionID,
				'SourceName' => $SourceName
			);

			//$str_data = json_encode($data);
			$str_data = json_encode($data);

			$result = $this->sendPostData($url_send, $str_data);
		}
	}


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

	function Gettaxexemptionbyid($TaxCertSNo)
	{
		$this->db->select("*");
	    $this->db->from('tordertaxexemptions');    
	    $this->db->join('mtaxexemptions','tordertaxexemptions.TaxExemptionUID=mtaxexemptions.TaxExemptionUID','left');
	    $this->db->where(array("tordertaxexemptions.TaxCertSNo"=> $TaxCertSNo));
	    $query = $this->db->get();
	    return $query->result_array();
	}


	function AcceptCancelApiOrder()
    {
        $OrderUID = $this->input->post('OrderUID');
        $Comments = $this->input->post('Comments');
        
        $Is_Api = $this->CheckApiOrders($OrderUID);
        $Details = $this->GetInBoundTransactionDetails($OrderUID);
        $InBoundUID = $Details->InBoundUID;
        $TransactionID = $Details->TransactionID;
        $ApiOrderRequestUID = $Details->ApiOrderRequestUID;

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

	function clean($string)
	{
		// $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

		//return preg_replace('/[^A-Za-z0-9 ]/', '', $string); // Removes special chars.
		//return preg_replace('/[^-A-Za-z0-9,. ]/', '', $string);
		//return htmlspecialchars($string);

		$res = htmlspecialchars($string);
		$str = html_entity_decode($res);
		return $str;
	}

	function CheckCleen($string='')
	{
		$string .= '      *sddsf<?>/\\-';
		echo $this->clean($string);
	}


	function SendTitleReportsToApi()
	{
		$OrderUID = $this->input->post('OrderUID');
		$OrderDetails = $this->GetOrderDetails($OrderUID);
    	$DeedListDetails = $this->GetDeedListDetails($OrderUID);   
    	$TrustorDetails = $this->GetPropertyRoles($OrderUID);
    	$AdverseConditions = $this->GetAdverseConditionDetails($OrderUID);

    	$AppSourceName = $this->GetSourceName($OrderUID); 
        $SourceName = trim($AppSourceName->OrderSourceName);

    	$FinalMortgageListDetails = [];
    	$MortgageListDetails = $this->GetMortgageListDetails($OrderUID); 

    	foreach ($MortgageListDetails as $key => $values) { 

    		$MortgageSNo = $values->MortgageSNo;

    		$MortgageAssigneeDetails = $this->GetMortgageAssigneeDetails($OrderUID,$MortgageSNo); 
    		$FinalMortgageAssigneeDetails = [];

    		foreach ($MortgageAssigneeDetails as $ele => $row) {

    			// SubDocument Assignee 

				$sub_Book = '';
                $sub_Page = '';
                $sub_DocumentNo = '';

                if($row->Subdocument_DBVTypeUID_1=='2'){
					$sub_BookPage = $row->Subdocument_DBVTypeValue_1;
					$sub_Res = explode('/',$sub_BookPage);
					$sub_Book = $sub_Res[0];
					$sub_Page = $sub_Res[1];					
				}elseif($row->Subdocument_DBVTypeUID_2=='2'){
					$sub_BookPage = $row->Subdocument_DBVTypeValue_2;
					$sub_Res = explode('/',$sub_BookPage);
					$sub_Book = $sub_Res[0];
					$sub_Page = $sub_Res[1];					
				}

				if($row->Subdocument_DBVTypeUID_1=='1'){
					$sub_Book = $row->Subdocument_DBVTypeValue_1;
				}elseif($row->Subdocument_DBVTypeUID_2=='1'){
					$sub_Book = $row->Subdocument_DBVTypeValue_2;
				}

				if($row->Subdocument_DBVTypeUID_1=='14'){
					$sub_Page = $row->Subdocument_DBVTypeValue_1;
				}elseif($row->Subdocument_DBVTypeUID_2=='14'){
					$sub_Page = $row->Subdocument_DBVTypeValue_2;
				}

				if($row->Subdocument_DBVTypeUID_1=='6'){
					$sub_DocumentNo = $row->Subdocument_DBVTypeValue_1;
				}elseif($row->Subdocument_DBVTypeUID_2=='6'){
					$sub_DocumentNo = $row->Subdocument_DBVTypeValue_2;
				}


				$Assignee= [];
				$Assignee['DocumentTypeName']=$row->DocumentTypeName;
				$Assignee['SubDocumentNo']=$sub_DocumentNo;
				$Assignee['SubBook']=$sub_Book;
				$Assignee['SubPage']=$sub_Page;
				$Assignee['Dated']=$row->Dated;
				$Assignee['Recorded']=$row->Recorded;
				$Assignee['OtherAssignmentType']=$row->MortgageAssignmentType;
				$Assignee['SubAmount']= '0.00';
				$Assignee['SubComments']=$row->Comments;

				array_push($FinalMortgageAssigneeDetails, $Assignee);

    		}

            if($values->IsOpenEnded == 1){
                $IsOpenEnded = 'Yes';
            }else{
                $IsOpenEnded = 'No';
            }
            if($values->Mortgage_DBVTypeUID_1=='7'){
                $Instrument = $values->Mortgage_DBVTypeValue_1;
            }elseif($values->Mortgage_DBVTypeUID_2=='7'){
                $Instrument = $values->Mortgage_DBVTypeValue_2;
            }
            $Position = $key + 1;
            switch($Position)
            {
                case "1":
                $os = 'st';
                break;
                case "2":
                $os = 'nd';
                break;
                case "3":
                $os = 'rd';
                break;
                default:
                $os = 'th';
            }
            if($values->Trustee1 != ''){
                $Trustee = $this->clean($values->Trustee1);
            }else{
                $Trustee = 'N/A';
            }

            $Book = '';
            $Page = '';
            $DocumentNo = '';

            if($values->Mortgage_DBVTypeUID_1=='2'){
				$BookPage = $values->Mortgage_DBVTypeValue_1;
				$Res = explode('/',$BookPage);
				$Book = $Res[0];
				$Page = $Res[1];					
			}elseif($values->Mortgage_DBVTypeUID_2=='2'){
				$BookPage = $values->Mortgage_DBVTypeValue_2;
				$Res = explode('/',$BookPage);
				$Book = $Res[0];
				$Page = $Res[1];					
			}

			if($values->Mortgage_DBVTypeUID_1=='1'){
				$Book = $values->Mortgage_DBVTypeValue_1;
			}elseif($values->Mortgage_DBVTypeUID_2=='1'){
				$Book = $values->Mortgage_DBVTypeValue_2;
			}

			if($values->Mortgage_DBVTypeUID_1=='14'){
				$Page = $values->Mortgage_DBVTypeValue_1;
			}elseif($values->Mortgage_DBVTypeUID_2=='14'){
				$Page = $values->Mortgage_DBVTypeValue_2;
			}

			if($values->Mortgage_DBVTypeUID_1=='6'){
				$DocumentNo = $values->Mortgage_DBVTypeValue_1;
			}elseif($values->Mortgage_DBVTypeUID_2=='6'){
				$DocumentNo = $values->Mortgage_DBVTypeValue_2;
			}	

            // $CustomerName = $value['CustomerName'];
            $MortgageAmount = $value['MortgageAmount'];
            $MortgageRecorded = $value['MortgageRecorded'];
            $MortgageDated = $value['MortgageDated'];
            $MortgageComments = $this->clean($value['MortgageComments']);

			$obj= [];
			$obj['Page']=$Page;
			$obj['Book']=$Book;
			$obj['DocumentNo']=$DocumentNo;
			$obj['Date']=$values->MortgageDated;
			$obj['RecordingDate']=$values->MortgageRecorded;
			$obj['MortgageMaturityDate']=$values->MortgageMaturityDate;
			$obj['Name']=$values->CustomerName;
			$obj['Trustee']=$Trustee;
			$obj['Amount']=$values->MortgageAmount;
			$obj['OpenEnded']=$IsOpenEnded;
			$obj['Position']=$Position.$os;
			$obj['Instrument']=$Instrument;
			$obj['Comment']=$values->MortgageComments;
			$obj['DocumentTypeName']=$values->DocumentTypeName;
			$obj['Mortgagee']=$values->Mortgagee;
			$obj['Mortgagor']=$values->Mortgagor;
			$obj['AdditionalInfo']=$values->AdditionalInfo;
			$obj['AssigneeDetails']=$FinalMortgageAssigneeDetails;

			array_push($FinalMortgageListDetails, $obj);
			unset($obj);
		}			

    	$TaxListDetails = $this->GetTaxListDetails($OrderUID); 
		$FinalTaxListDetails = [];
    	foreach ($TaxListDetails as $key => $value) 
    	{
			$TaxCertSNo = $value->TaxCertSNo;
			$OrderUID = $value->OrderUID;
			$type = $value->TaxType;
			$damt = $value->AmountDelinquent; 
			$ddate = $value->GoodThroughDate;
			$dtype = $value->DocumentTypeName;
			$basic = $value->TaxBasisName;	
			$TaxID = $value->ParcelNumber;
			$PropertyClassName = $value->StewartCode;
			$TaxComments = $value->TaxComments;
			
			$TaxInstallment = $this->GetTaxInstallmentDetails($OrderUID, $TaxCertSNo); 

			$AmountPaid ='';
			$BaseAmount ='';
			$DatePaid =''; 
			$NextDueDate = '';
			foreach ($TaxInstallment as $key => $value) {
				$AmountPaid = $AmountPaid+$value->AmountPaid; 
				$BaseAmount = $BaseAmount+$value->GrossAmount;					
				$DatePaid = $value->DatePaid;
				$NextDueDate = $value->NextDueDate;
				$satus = $value->TaxStatusName;
				$TaxYear = $value->TaxYear;
			}
			
			$TaxExcem = $this->Gettaxexemptionbyid($TaxCertSNo); 
			foreach ($TaxExcem as $key => $exvalue) {
			  $Excemption = $exvalue['TaxExemptionName'];
			  $ExcemptionAmt = $exvalue['TaxAmount'];
			}

			if(empty($Excemption)){
				$Excemption = 'Empty';
			}

			$UniqueID = $key; 
			$Date = $DatePaid;
			$TotalTax = $BaseAmount;
			$TaxYear = $TaxYear;
			$TotalTaxPaid = $AmountPaid;
			$Comment = $TaxComments;
			$NextDueDate = $NextDueDate;

			if($Date == ''){
				$Date = '0000-00-00';
			}
			if($NextDueDate == ''){
				$NextDueDate = '0000-00-00';
			}

			if(empty($TotalTax)){
				$TotalTax = '';
			}if(empty($TaxYear)){
				$TaxYear = '';
			}if(empty($TotalTaxPaid)){
				$TotalTaxPaid = 'Empty';
			}if(empty($satus)){
				$satus = 'Empty';
			}

			if($ExcemptionAmt == '' || $ExcemptionAmt == ' '){
				$ExcemptionAmt = '0.00';
			}
			
			$obj= [];
			$obj['PropertyClassName']=$PropertyClassName;
			$obj['UniqueID']=$UniqueID;
			$obj['TaxID']=$TaxID;
			$obj['Taxtype']=$type;
			$obj['DocumentTypeName']=$dtype;
			$obj['Date']=$Date;
			$obj['NextDueDate'] = $NextDueDate;
			$obj['Paidthro'] = $ddate;
			$obj['TotalTax']=$TotalTax;
			$obj['TaxYear']=$TaxYear;
			$obj['TotalTaxPaid']=$TotalTaxPaid;
			$obj['AmountDelinquent']=$damt;
			$obj['DelinquentYear']=substr($ddate,0,4);
			$obj['Excemption']=$Excemption;
			$obj['ExcemptionAmount']=$ExcemptionAmt;
			$obj['TaxStatus']=$satus;
			$obj['TaxBasic']=$basic;
			$obj['Comment']=$this->clean($Comment);
			
			array_push($FinalTaxListDetails, $obj);		
			unset($obj);		
		}
		
		$Legal = $this->GetLegalDescription($OrderUID);

		$last_char = substr($Legal, -1); 

		if($last_char !== "."){
			$Legal = $Legal.".";
		}

		$Legaldes = [];
		if($Legal!='')
		{
	      $Legaldes['Description'] = $this->clean($Legal);
		}

		$Liens = $this->GetLiensDetails($OrderUID);  
		//$Judgement = $this->GetJudgementDetails($OrderUID); 
		$PropertyInfo = $this->GetPropertyDetails($OrderUID);
		$Assessment = $this->GetAssessmentDetails($OrderUID);

		$Is_Api = $this->CheckApiOrders($OrderUID);
		$Details = $this->GetInBoundTransactionDetails($OrderUID);
		$InBoundUID = $Details->InBoundUID;
		$TransactionID = $Details->TransactionID;
		$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

		$FinalJudgmentListDetails = [];
		$JudgmentListDetails = $this->GetJudgmentListDetails($OrderUID);

		foreach ($JudgmentListDetails as $key => $values) {

			$JudgementSNo = $values->JudgementSNo;
			$JudgementCaseNo = $values->JudgementCaseNo;

			$Judg_Book = '';
            $Judg_Page = '';
            $Judg_DocumentNo = '';
            $Judg_CaseNo = '';

            if($values->JudgementCaseNo){
				$Judg_CaseNo = $values->JudgementCaseNo;
			}elseif($values->Judgement_DBVTypeUID_1=='3'){
                $Judg_CaseNo = $values->Judgement_DBVTypeValue_1;
            }elseif($values->Judgement_DBVTypeUID_2=='3'){
                $Judg_CaseNo = $values->Judgement_DBVTypeValue_2;
            }


            if($values->Judgement_DBVTypeUID_1=='7'){
                $Judg_Instrument = $values->Judgement_DBVTypeValue_1;
            }elseif($values->Judgement_DBVTypeUID_2=='7'){
                $Judg_Instrument = $values->Judgement_DBVTypeValue_2;
            }

            if($values->Judgement_DBVTypeUID_1=='2'){
				$Judg_BookPage = $values->Judgement_DBVTypeValue_1;
				$Res = explode('/',$Judg_BookPage);
				$Judg_Book = $Res[0];
				$Judg_Page = $Res[1];					
			}elseif($values->Judgement_DBVTypeUID_2=='2'){
				$Judg_BookPage = $values->Judgement_DBVTypeValue_2;
				$Res = explode('/',$Judg_BookPage);
				$Judg_Book = $Res[0];
				$Judg_Page = $Res[1];					
			}

			if($values->Judgement_DBVTypeUID_1=='1'){
				$Judg_Book = $values->Judgement_DBVTypeValue_1;
			}elseif($values->Judgement_DBVTypeUID_2=='1'){
				$Judg_Book = $values->Judgement_DBVTypeValue_2;
			}

			if($values->Judgement_DBVTypeUID_1=='14'){
				$Judg_Page = $values->Judgement_DBVTypeValue_1;
			}elseif($values->Judgement_DBVTypeUID_2=='14'){
				$Judg_Page = $values->Judgement_DBVTypeValue_2;
			}

			if($values->Judgement_DBVTypeUID_1=='6'){
				$Judg_DocumentNo = $values->Judgement_DBVTypeValue_1;
			}elseif($values->Judgement_DBVTypeUID_2=='6'){
				$Judg_DocumentNo = $values->Judgement_DBVTypeValue_2;
			}

			$obj= [];
			$obj['Page']=$Judg_Page;
			$obj['Book']=$Judg_Book;
			$obj['Instrument']=$Judg_Instrument;
			$obj['JudgementCaseNo']=$Judg_CaseNo;
			$obj['DocumentNo']=$Judg_DocumentNo;
			$obj['Amount']=$values->JudgementAmount;
			$obj['Date']=$values->JudgementDated;
			$obj['RecordingDate']=$values->JudgementRecorded;
			$obj['JudgementFiled']=$values->JudgementFiled;
			$obj['JudgementExceptionOnPolicy']=$values->JudgementExceptionOnPolicy;
			$obj['Comment']=$values->JudgementComments;
			$obj['DocumentTypeName']=$values->DocumentTypeName;
			$obj['Plaintiff']=$values->Plaintiff;
			$obj['PlaintiffAttorney']=$values->PlaintiffAttorney;
			$obj['Defendent']=$values->Defendent;
			$obj['DefendentAttorney']=$values->DefendentAttorney;

			array_push($FinalJudgmentListDetails, $obj);
			unset($obj);
		}

		$EventCode = $this->input->post('EventCode');
		$OrderNumbers = $this->GetOrderDetails($OrderUID);
		$OrderNumber = $OrderNumbers->OrderNumber;

		$TotalFiles = $this->input->post('TotalFiles');
		$DocumentFileName = $this->input->post('documentfilenames');

		$col_name = array();
    	$col_val = array();

    	$OrderSourceName = $this->common_model->GetApiSourceName($OrderUID); 
    	$OrderSourceUID = $OrderSourceName->OrderSourceUID;

		if($Is_Api)
		{
			$url_send = $this->config->item("api_url");

			$FinalProductDetails = $this->GetFinalReportToAPI($OrderUID);

			foreach($DocumentFileName as $key=>$value) {
				$Documents = [];

				$DocFileName = $value;
				$OrderDocDetails = $this->GetAttachmentToAPI($OrderUID,$DocFileName);

				$OrderNumber = $OrderDocDetails->OrderNumber;
				$OrderDocsPath = $OrderDocDetails->OrderDocsPath;
				$FileName = $OrderDocDetails->DocumentFileName;
				$TemplateName = $OrderDocDetails->TemplateName;
				$DocumentCreatedDate = $OrderDocDetails->DocumentCreatedDate;
				$text = file_get_contents(FCPATH.$OrderDocsPath.$FileName);
				$Content = base64_encode($text);

				/**
				* @purpose To get the doctype and typeofdocument
				*
				* @param  OrderUID
				* 
				* @author Yagavi G <yagavi.g@avanzegroup.com>
				* @return TypeOfDocument, DocTypeCode
				* @since April 1 2020
				*
				*/
				$DocTypeCode = $OrderDocDetails->TypeOfDocument;
				$TypeOfDocument = $OrderDocDetails->TypeOfDocument;

				$DocumentTypeCode = $this->common_model->GetDocumentTypeCodeByUID($OrderDocDetails->TypeOfDocument);
				if(!empty($DocumentTypeCode)){
					$TypeOfDocument = $DocumentTypeCode;
				} else {
					$DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $OrderSourceUID);
					$TypeOfDocument = $this->common_model->GetDocumentTypeCodeByUID($DocumentTypeUID);
				}

				if($SourceName=='Keystone') {
					$KeystoneDocTypes = $this->config->item('KeystoneDocTypes');
					if(!empty($KeystoneDocTypes)){
						$TypeOfDocument = $KeystoneDocTypes[$TypeOfDocument];
					} else {
						$DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $OrderSourceUID);
						$TypeOfDocument = $this->common_model->GetDocumentTypeCodeByUID($DocumentTypeUID);
					}
				}			

				$obj= [];
				$obj['DocumentFileName']=$FileName;
				$obj['TemplateName']=$TemplateName;
				$obj['DocumentCreatedDate']= Date('Y/m/d H:i:s',strtotime($DocumentCreatedDate));
				$obj['Content']=$Content;
				$obj['DocTypeCode']=$TypeOfDocument;
				$obj['DocTypeName']=$TypeOfDocument;

				array_push($Documents, $obj);
				unset($obj);


				if($SourceName=='RealEC'){
					array_push($col_name, 'EventCode');
					array_push($col_val, $EventCode);					
				} 

				array_push($col_name, 'InBoundUID');
				array_push($col_val, $InBoundUID);

				array_push($col_name, 'TransactionID');
				array_push($col_val, $TransactionID);

				array_push($col_name, 'OrderDetails');
				array_push($col_val, $OrderDetails);

				array_push($col_name, 'DeedListDetails');
				array_push($col_val, $DeedListDetails);

				array_push($col_name, 'MortgageListDetails');
				array_push($col_val, $FinalMortgageListDetails);

				array_push($col_name, 'TaxListDetails');
				array_push($col_val, $FinalTaxListDetails);

				array_push($col_name, 'LegalDetails');
				array_push($col_val, $Legaldes);

				array_push($col_name, 'LiensDetails');
				array_push($col_val, $Liens);

				array_push($col_name, 'JudgementDetails');
				array_push($col_val, $FinalJudgmentListDetails);

				array_push($col_name, 'AssessmentDetails');
				array_push($col_val, $Assessment);

				array_push($col_name, 'TrustorDetails');
				array_push($col_val, $TrustorDetails);

				array_push($col_name, 'PropertyInfoDetails');
				array_push($col_val, $PropertyInfo);

				array_push($col_name, 'AdverseConditions');
				array_push($col_val, $AdverseConditions);

				array_push($col_name, 'SourceName');
				array_push($col_val, $SourceName);

				array_push($col_name, 'Documents');
				array_push($col_val, $Documents);

				array_push($col_name, 'OrderNumber');
	    		array_push($col_val, $OrderNumber);

	    		array_push($col_name, 'OrderUID');
	    		array_push($col_val, $OrderUID);

				$column_name = $col_name;
				$column_value = $col_val;   
				$data = array_combine($column_name, $column_value);

				$str_data = json_encode($data);
				$result = $this->sendPostData($url_send, $str_data);

				if($result){
					echo json_encode( array('success' => 1, 'message'=>'Final Report is Delivered'));exit;
				} else {
					echo json_encode( array('success' => 0, 'message'=>'Final Report is not Delivered'));exit;
				}
			} 			
		}	
	}

	function SendAttachmentsToAttroney(){
			$this->load->model('pto_model');
			error_reporting(0);
			$OrderUID = $this->input->post('OrderUID');

			$OrderNumbers = $this->GetOrderDetails($OrderUID);
			$OrderNumber = $OrderNumbers->OrderNumber;
			$TotalFiles = $this->input->post('TotalFiles');
			$DocumentFileName = $this->input->post('documentfilenames');
			$ProviderOrderNumber = $this->input->post('ProviderOrderNumber');


			$Is_Api = $this->pto_model->GetAbstractorUserUIDByOrderUID($OrderUID);
			if($ProviderOrderNumber)
			{
				$pto_id=$ProviderOrderNumber;
			}
			else
			{
				$pto_id=$Is_Api['ProviderOrderNumber'];
			}

			$col_name = array();
	    	$col_val = array();

			if($Is_Api){

				$url_send = $this->config->item("api_url");			

				$Documents=[];
				foreach($DocumentFileName as $key=>$value) {

				    $DocFileName = $value;
				    $OrderDocDetails = $this->GetAttachmentToAPI($OrderUID,$DocFileName);
				    $PtoOrderId = $OrderDocDetails->PtoOrderId;
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


				array_push($col_name, 'EventCode');
	    		array_push($col_val, 'd2t-searchDoc');

				array_push($col_name, 'Documents');
	    		array_push($col_val, $Documents);

	    		array_push($col_name, 'OrderNumber');
	    		array_push($col_val, $OrderNumber);

	    		array_push($col_name, 'OrderUID');
	    		array_push($col_val, $OrderUID);

	    		array_push($col_name, 'PtoOrderId');
	    		array_push($col_val, $pto_id);

	    		array_push($col_name, 'SourceName');
	    		array_push($col_val, 'PTO');

	    		$column_name = $col_name;
			    $column_value = $col_val;   

			    $data = array_combine($column_name, $column_value);

				$str_data = json_encode($data);


				$result = $this->sendDataToPto($url_send, $str_data,$OrderUID);
			echo '<pre>';print_r($result);exit;
			}
		}

		function sendDataToPto($url, $post,$OrderUID)
		{
		 $APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
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
		  //echo "cURL Error #:" . $err;
		} else 
		{ 
		  $SuccessMsg = explode('$', $response);
          $API = $SuccessMsg[0];
          $Action = trim($SuccessMsg[1]);
          $InBoundUID = trim($SuccessMsg[2]);
          $TransactionNO = trim($SuccessMsg[3]);
          $Status = trim($SuccessMsg[4]);
          $PtoOrderId=trim($SuccessMsg[5]);
         if($Status == "success")
          {
		$this->db->select("*");
		$this->db->from('mApiTitlePlatform');
		$this->db->where(array("mApiTitlePlatform.OrderSourceName"=>"PTO"));
		$query = $this->db->get();
		$SourceName = $query->row();
		$OrderSourceUID = $SourceName->OrderSourceUID;
		$post_decode=json_decode($post);
		foreach ($post_decode->Documents as $key => $value) 
		 {
		 $file_name .= $value->DocumentFileName.',';	
		 }
		$Notes = $file_name.'Package Sended To PTO';
		$res=$this->UpdateReportSend($OrderUID,$OrderSourceUID,$SourceName,$Notes,'d2t-searchDoc','');
		return true;
		}
		else
		{
		$this->db->select("*");
		$this->db->from('mApiTitlePlatform');
		$this->db->where(array("mApiTitlePlatform.OrderSourceName"=>"PTO"));
		$query = $this->db->get();
		$SourceName = $query->row();
		$OrderSourceUID = $SourceName->OrderSourceUID;
		$Notes = 'Package Not Sended To PTO';	
		$res=$this->UpdateReportSend($OrderUID,$OrderSourceUID,$SourceName,$Notes,'d2t-searchDoc','');
		}
		}

		}

		function SendPackagestoAbstractor(){
			$post = $this->input->post();
			$OrderUID = $this->input->post('OrderUID');
			$DocumentFileName = $this->input->post('documentfilenames');
			$this->load->model('Api_common_model');
			$res = $this->Api_common_model->SendPackagestoAbstractor($post);
		}

		/* Closing Events 761/765/766 to RealEC*/

		function DocumentPackageReceived(){
			$this->load->model('title_closing/Closing_model');
			$OrderUID = $this->input->post('OrderUID');
			$EventCode = $this->input->post('EventCode');
			$IsAPI = $this->Closing_model->CheckApiOrders($OrderUID);

			$AppSourceName = $this->GetSourceName($OrderUID); 
            $SourceName = trim($AppSourceName->OrderSourceName);

            if($IsAPI){

            	if($SourceName=='RealEC')
            	{
            		switch ($EventCode) {
            			case '761':
            			$Comment_761['Comment'] = 'Document Package Received';
            			$Comments = '761 Event Send Successfully';
            			$Event_761 = $this->Closing_model->SendEventstoAPI($OrderUID,'761', $Comment_761);
            			break;

            			case '765':
            			$Comment_765['Comment'] = 'Closing Documents Signing Executed';
            			$Comments = '765 Event Send Successfully';
            			$Event_765 = $this->Closing_model->SendEventstoAPI($OrderUID,'765', $Comment_765);
            			break;

            			case '766':
            			$Comment_766['Comment'] = 'Closing Documents Signing Not Executed';
            			$Comments = '766 Event Send Successfully';
            			$Event_766 = $this->Closing_model->SendEventstoAPI($OrderUID,'766', $Comment_765);
            			break;
            		}
				} else if($SourceName=='Keystone'){
            		$closing_keystone_data['OrderUID'] = $OrderUID;
            		$closing_data['OrderUID'] = $OrderUID;
					switch ($EventCode) {
            			case '761':
            			$closing_data['EventCode'] = 'ReceiveClosingPackage';
            			$closing_data['EventComments'] = 'Closing Package Received';
            			$Comments = 'ReceiveClosingPackage Event Send Successfully';
            			$this->Closing_model->SendClosingEventstoAPI($closing_data);
            			
            			/*$closing_keystone_data['EventCode'] = 'ClosingPackageSentToNotary';
            			$closing_keystone_data['Comment'] = 'Closing Package Received';
            			$Comments = 'ClosingPackageSentToNotary Event Send Successfully';
            			$this->Closing_model->SendClosingEventstoAPI($closing_keystone_data);*/
            			break;

            			case '765':
            			$closing_data['EventCode'] = 'CompletedSigning';
            			$closing_data['EventComments'] = 'Closing Documents Signing Executed';
            			$Comments = 'CompletedSigning Event Send Successfully';
            			$this->Closing_model->SendClosingEventstoAPI($closing_data);

            			/*$closing_keystone_data['EventCode'] = 'ClosingPackageApprovedByVendor';
            			$closing_keystone_data['Comment'] = 'Closing Documents Signing Executed';
            			$Comments = 'ClosingPackageApprovedByVendor Event Send Successfully';
            			$this->Closing_model->SendClosingEventstoAPI($closing_keystone_data);*/
            			break;

            			case '766':
            			$closing_data['EventCode'] = 'FailedSigning';
            			$closing_data['EventComments'] = 'Closing Documents Signing not Executed';
            			$Comments = 'FailedSigning Event Send Successfully';
            			$this->Closing_model->SendClosingEventstoAPI($closing_data);

            			/*$closing_keystone_data['EventCode'] = 'ClosingPackageRejectedByVendor';
            			$closing_keystone_data['Comment'] = 'Closing Documents Signing not Executed';
            			$Comments = 'ClosingPackageRejectedByVendor Event Send Successfully';
            			$this->Closing_model->SendClosingEventstoAPI($closing_keystone_data);*/            			
            			break;
            		}
				}
			} else {
				$Comments = 'Not an API order to send this event';
			}
			$result = array('OrderUID' => $OrderUID, 'Comment' => $Comments);			
			echo json_encode($result);
		}

		/* NotaryGo - Send Attachments Starts */
		function SendAttachmentsToNotary()
		{
			$this->load->model('Notary_model');
			$OrderUID = $this->input->post('OrderUID');
			$OrderNumbers = $this->GetOrderDetails($OrderUID);
			$OrderNumber = $OrderNumbers->OrderNumber;
			$TotalFiles = $this->input->post('TotalFiles');
			$DocumentFileName = $this->input->post('documentfilenames');
			$ScheduleUID = $this->input->post('ScheduleUID');
			$APIOrder = $this->OutboundOrderDetails($OrderUID,$ScheduleUID);
			$ProviderOrderNumber = $APIOrder->ProviderOrderNumber;

			$url_send = $this->config->item("api_url");     
			if($ProviderOrderNumber)
			{
				$Documents=[];
				$SendDocumentFileName=[];
				foreach($DocumentFileName as $key=>$value) 
				{
					$DocFileName = $value;
					$OrderDocDetails = $this->GetAttachmentToAPI($OrderUID,$DocFileName);
					$PtoOrderId = $OrderDocDetails->PtoOrderId;
					$OrderDocsPath = $OrderDocDetails->OrderDocsPath;
					$FileName = $OrderDocDetails->DocumentFileName;
					$DocumentCreatedDate = $OrderDocDetails->DocumentCreatedDate;
					$text = file_get_contents(FCPATH.$OrderDocsPath.$FileName);
					$Content = base64_encode($text);
					$Documents[] = array('FileName'=>$FileName,'Content'=>$Content,'CreatedDate'=>$DocumentCreatedDate);
					$SendDocumentFileName[] = $FileName;
				}
				$DocumentFileSend = implode(', ', $SendDocumentFileName);
				$data['Documents'] = $Documents;
				$data['EventCode'] = 'Document';
				$data['OrderDetails']['OrderNumber'] = $OrderNumber; 
				$data['ProviderOrderNumber'] = $ProviderOrderNumber;  
				$data['SpecialNotes'] = $this->input->post('Notes');  
				$data['SourceName'] = 'Notary';  
				$data['OrderUID'] = $OrderUID;
				$data['DocumentFileSend'] = $DocumentFileSend;
				$str_data = json_encode($data);
				$result = $this->Notary_model->sendPostData($url_send,$str_data,'isgn',$OrderUID);    
				$res = array('success' => 1 );
			} else {
				$res = array('success' => 0 );
			}

			echo json_encode($res);
		}

		function OutboundOrderDetails($OrderUID,$ScheduleUID)
		{
			$this->db->select ( '*' );
			$this->db->from ( 'tOrderSchedule' );
			$this->db->where(array("tOrderSchedule.ScheduleUID"=>$ScheduleUID,"tOrderSchedule.OrderUID"=>$OrderUID));
			$query = $this->db->get();
			$tOrderSchedule =  $query->row();

			if($tOrderSchedule){
				$AbstractorUID = $tOrderSchedule->AbstractorUID;
				$AbstractorOrderUID = $tOrderSchedule->AbstractorOrderUID;
				$this->db->select ( '*' );
				$this->db->from ( 'tApiOutBoundOrders' );
				$this->db->join ( 'torders', 'torders.OrderUID = tApiOutBoundOrders.OrderUID' );
				$this->db->join ( 'mApiTitlePlatform', 'mApiTitlePlatform.OrderSourceUID = tApiOutBoundOrders.OrderSourceUID' , 'left' );
				$this->db->where(array("tApiOutBoundOrders.OrderUID"=>$OrderUID,"tApiOutBoundOrders.AbstractorOrderUID"=> $AbstractorOrderUID,"mApiTitlePlatform.OrderSourceName"=> "Notary"));
				$query = $this->db->get();
				$tApiOutBoundOrders =  $query->row();
				return $tApiOutBoundOrders;
			}
		}	

		function Func_SendVendorNotes() {
			$this->load->model('Notary_model');
			$OrderUID = $this->input->post('OrderUID');
			$SectionUID = $this->input->post('SectionUID');
			$ScheduleUID = $this->input->post('NotaryScheduleUID');
			$OrderNumbers = $this->GetOrderDetails($OrderUID);
			$OrderNumber = $OrderNumbers->OrderNumber;
			$APIOrder = $this->OutboundOrderDetails($OrderUID,$ScheduleUID);
			$ProviderOrderNumber = $APIOrder->ProviderOrderNumber;
			$url_send = $this->config->item("api_url");

			if($ProviderOrderNumber) {
				$data['Documents'] = '';
				$data['EventCode'] = 'Document';
				$data['OrderDetails']['OrderNumber'] = $OrderNumber; 
				$data['ProviderOrderNumber'] = $ProviderOrderNumber;  
				$data['SpecialNotes'] = $this->input->post('VendorNotes');  
				$data['SourceName'] = 'Notary';  
				$data['OrderUID'] = $OrderUID;
				$data['DocumentFileSend'] = $DocumentFileSend;
				$str_data = json_encode($data);
				$result = $this->Notary_model->sendPostData($url_send,$str_data,'isgn',$OrderUID);  

				$insert_notes = array(
					'Note' => $this->input->post('VendorNotes'),
					'EventCode' => 'Document',
					'SectionUID' => $SectionUID,
					'OrderUID' => $OrderUID,
					'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
					'CreatedByUserUID' => $this->session->userdata('UserUID'),
					'CreatedOn' => date('Y-m-d H:i:s')
				);   
				$result = $this->db->insert("tordernotes", $insert_notes);  
			}
        	
        	$res = array('success' => 1 );
			echo json_encode($res);
		}

		/* NotaryGo - Send Attachments Ends */

		/* Start NotaryGo - CRON File Order Status */
		function Cron_NotaryOrderStatus(){
			$date = new DateTime("now");
			$curr_date = $date->format('Y-m-d ');
			$status = array('Complete', 'Cancel');
			$api_status = array('SIGNED', 'NO-SIGN', 'NO-SHOW');
			$where_au = "(tOrderSchedule.APIStatus = 'NEW' OR DATE(tOrderSchedule.SigningDateTime) = '$curr_date')";
			$this->db->select ( '*' );
			$this->db->from ( 'tApiOutBoundOrders' );
			$this->db->join ( 'mApiTitlePlatform', 'mApiTitlePlatform.OrderSourceUID = tApiOutBoundOrders.OrderSourceUID' , 'left' );
			$this->db->join ( 'torders', 'torders.OrderUID = tApiOutBoundOrders.OrderUID' );
			$this->db->join ( 'tOrderSchedule', 'tOrderSchedule.AbstractorOrderUID = tApiOutBoundOrders.AbstractorOrderUID' );
			$this->db->join ( 'tOrderSign', 'tOrderSign.ScheduleUID = tOrderSchedule.ScheduleUID' );
			$this->db->where( 'tApiOutBoundOrders.ProviderOrderNumber <>', '' );
			$this->db->where( 'mApiTitlePlatform.OrderSourceName', 'Notary' );
			$this->db->where( 'tApiOutBoundOrders.Status', 'Accepted' );
			$this->db->where( 'tOrderSign.IsSignConfirmDone', '0' );
			$this->db->where_not_in( 'tOrderSchedule.ScheduleStatus', $status );
			$this->db->where( $where_au );
			$this->db->where_not_in( 'tOrderSchedule.APIStatus', $api_status );
			$this->db->group_by("tApiOutBoundOrders.ApiOutBoundOrderUID");
			$query = $this->db->get();
			$notary =  $query->result();
			if($notary){
				$this->db->select("*");
				$this->db->from('mApiTitlePlatform');
				$this->db->where(array("mApiTitlePlatform.OrderSourceName"=> 'Notary'));
				$query = $this->db->get();
				$mApiTitlePlatform = $query->row();

				$notary_orders['NotaryOrders'] = $notary;
				$notary_orders['SourceName'] = 'Notary';
				$notary_orders['EventCode'] = 'OrderStatus';
				$notary_orders['mApiTitlePlatform'] = $mApiTitlePlatform;
				$url_send = $this->config->item("api_url");
				$this->NotaryCronPostData($url_send, $notary_orders, 'isgn');
			}
		}

		function NotaryCronPostData($url, $notary_orders, $OrgCode){
			$APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
			$post = json_encode($notary_orders);
			$OrderSourceUID = $notary_orders['mApiTitlePlatform']->OrderSourceUID;
			$OrderSourceName = $notary_orders['mApiTitlePlatform']->OrderSourceName;
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
				$NoteType = $this->GetNoteTypeUID('API Note');
				$SectionUID = $NoteType->SectionUID;
				$notary_orders_response = json_decode($response,true);

				foreach ($notary_orders_response as $key => $value) {
					$Notes = '';
					$SigningAgent = '';
					$EstimateFee = '';
					$Notary_ACk = '';
					$ACK = '';
					$Message = '';
					$Documents = '';
					$ResponseStatus = '';
					$ApiOutBoundOrderUID = $value['ApiOutBoundOrderUID'];
					$OrderUID = $value['OrderUID'];
					$OrderNumber = $value['OrderNumber'];
					$ProviderOrderNumber = $value['ProviderOrderNumber'];
					$ScheduleUID = $value['ScheduleUID'];
					$orderstatus_response = $value['response'];
					$Notary_ACk = simplexml_load_string($orderstatus_response);
					$ACK = $Notary_ACk->Success;
					$Message = $Notary_ACk->Message;
					//$Documents = $Notary_ACk->Documents;
					$res = json_encode($Notary_ACk);
					$Receive_JsonArray = json_decode($res, true);
					$Documents = $Receive_JsonArray['Documents']['ReturnDocument'];

					if(!empty($Message))
					{	
						$Message = explode(',', $Message);
						if($Message[1])
						{
							$SigningAgentString = explode(':', $Message[1]);
							$SigningAgent = $SigningAgentString[1];
						}
						if($Message[2])
						{
							$EstimateFeeString = explode(':', $Message[2]);
							$EstimateFee = $EstimateFeeString[1];
							$EstimateFee = str_replace('$', '', $EstimateFee);
						}
					}
					if(!empty($Documents))
					{
						$fieldArray['OrderUID'] = $OrderUID;
						$fieldArray['Attachments'] = $Documents;
						$fieldArray['ProviderOrderNumber'] = $ProviderOrderNumber;
						$result  = $this->NotaryGoAttachment($fieldArray,$OrderSourceUID);
					}					

					$NotaryStatus = array('NEW', 'BOOKED', 'SIGNED', 'NO-SIGN', 'NO-SHOW');
					$ResponseStatus = strtoupper($Notary_ACk->Status);
					$NotaryNotes = '';

					if(in_array($ResponseStatus, $NotaryStatus) == TRUE){
						$Status = array(
							'APIStatus'=>$ResponseStatus,
							'SigningAgent'=>$SigningAgent,
							'EstimateFee'=>$EstimateFee
						);
						$this->db->where('ScheduleUID', $ScheduleUID);
						$this->db->update('tOrderSchedule',$Status);
						$data1['ApiOutBoundOrderUID']=$ApiOutBoundOrderUID;
						
						/* @purpose: To add notes for exceeded fee and prohibited agent  @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: 14th May 2020*/
						$NotaryNotes = $this->UpdateNotaryGoDetails($OrderUID,$ScheduleUID,$SigningAgent,$EstimateFee);
					}

					$Notes = 'Notary Order Status: <br> Success: '.$ACK.'<br> ProviderOrderNumber: '.$ProviderOrderNumber.'<br> Status: '.$Notary_ACk->Status.'<br> SigningAgent: '.$SigningAgent.'<br> EstimateFee: $'.number_format($EstimateFee,2).$NotaryNotes;					
					$NoteType = $this->GetNoteTypeUID('API Note');
					$SectionUID = $NoteType->SectionUID;
					$NoteData = json_encode($data);
					$insert_notes = array(
						'Note' => $Notes,
						'EventCode' => 'OrderStatus',
						'SectionUID' => $SectionUID,
						'OrderUID' => $OrderUID,
						'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
						'CreatedByAPI' => $OrderSourceUID,
						'CreatedOn' => date('Y-m-d H:i:s')
					);       

					$this->db->insert("tordernotes", $insert_notes);

					$data1['ModuleName']=$Notes.'-insert';
					$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
					$data1['DateTime']=date('y-m-d H:i:s');
					$data1['TableName']='tordernotes';
					$data1['OrderUID']=$OrderUID;
					$data1['OrderSourceUID']=$OrderSourceUID;
					$data1['OrderStatus']="'".$ResponseStatus."'";
					$data1['UserUID']=$this->session->userdata('UserUID');                        
					$this->Common_model->Audittrail_insert($data1);
				}
			}
		}

		/* @purpose: To add notes for exceeded fee and prohibited agent  @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: 14th May 2020*/
		function UpdateNotaryGoDetails($OrderUID,$ScheduleUID,$SigningAgent,$EstimateFee){
			$this->db->select ( '*' );
			$this->db->from ( 'tOrderSchedule' );
			$this->db->where( 'tOrderSchedule.ScheduleUID', $ScheduleUID );
			$this->db->where( 'tOrderSchedule.OrderUID', $OrderUID );
			$query = $this->db->get();
			$tOrderSchedule = $query->row();
			$AbstractorUID = $tOrderSchedule->AbstractorUID;
			$AbstractorOrderUID = $tOrderSchedule->AbstractorOrderUID;
			$this->load->model('Notary_model');
			$NotaryStatus = $this->Notary_model->CheckAgentEstimatedFeeStatus($AbstractorOrderUID);
			$NotaryNotes = '<br>';
			if($NotaryStatus['IsFeeExceed'] == 1){
				$NotaryNotes.= '<span style="color:red">Estimated fee is exceeded when compare with vendor fee</span><br>';
			}
			if($NotaryStatus['IsAgentProhibited'] == 1){
				$NotaryNotes.= '<span style="color:red">'.$NotaryStatus['SigningAgent'].' is Prohibited by Sourcepoint</span><br>';
			}
			return $NotaryNotes;
		}	

		/* End NotaryGo - CRON File Order Status */

		/* Start UPS - CRON File Tracking */
		function Cron_UPSOrderTracking(){
			$date = new DateTime("now");
			$curr_date = $date->format('Y-m-d ');
			$status = array('Complete', 'Cancel');
			//$where_au = "(DATE(tOrderSchedule.SigningDateTime) = '$curr_date')";
			$this->db->select ( '*' );
			$this->db->from ( 'tApiOutBoundOrders' );
			$this->db->join ( 'mApiTitlePlatform', 'mApiTitlePlatform.OrderSourceUID = tApiOutBoundOrders.OrderSourceUID' , 'left' );
			$this->db->join ( 'torders', 'torders.OrderUID = tApiOutBoundOrders.OrderUID' );
			$this->db->join ( 'tOrderShipping', 'tOrderShipping.ShipmentIdentificationNumber = tApiOutBoundOrders.ProviderOrderNumber' );
			/*$this->db->join ( 'tOrderSchedule', 'tOrderSchedule.ScheduleUID = tOrderShipping.ScheduleUID' );
			$this->db->join ( 'tOrderSign', 'tOrderSign.ScheduleUID = tOrderSchedule.ScheduleUID' );*/
			$this->db->where( 'tApiOutBoundOrders.ProviderOrderNumber <>', '' );
			$this->db->where( 'tOrderShipping.TrackingNumber <>', '' );
			$this->db->where( 'mApiTitlePlatform.OrderSourceName', 'UPS' );
			$this->db->where( 'tApiOutBoundOrders.Status', 'Accepted' );
			$this->db->where( 'tOrderShipping.IsManualCancel', '0' );
			$this->db->where( 'tOrderShipping.ShippingStatus <>', 'Exception' );
			//$this->db->where( 'tOrderSign.IsSignConfirmDone', '0' );
			//$this->db->where_not_in( 'tOrderSchedule.ScheduleStatus', $status );
			//$this->db->where( $where_au );
			$this->db->group_by("tOrderShipping.ShippingUID");
			$query = $this->db->get();
			$ups =  $query->result();

			if($ups){
				$this->db->select("*");
				$this->db->from('mApiTitlePlatform');
				$this->db->where(array("mApiTitlePlatform.OrderSourceName"=> 'UPS'));
				$query = $this->db->get();
				$mApiTitlePlatform = $query->row();

				$ups_orders['UPSOrders'] = $ups;
				$ups_orders['SourceName'] = 'UPS';
				$ups_orders['EventCode'] = 'Track';
				$ups_orders['APIService'] = 'Track';
				$ups_orders['mApiTitlePlatform'] = $mApiTitlePlatform;
				$url_send = $this->config->item("api_url");
				$this->UPSCronPostData($url_send, $ups_orders, 'isgn', 0);
			} else{
				echo json_encode( array('status' => 'failed', 'message'=> 'No Records Found'));exit;
			}
		}

		function UPSOrderTrackingByShipmentID(){
			$post = $this->input->post();
			$ShippingUID = $this->input->post('ShippingUID');
			$OrderUID = $this->input->post('OrderUID');
			$date = new DateTime("now");
			$curr_date = $date->format('Y-m-d ');
			$status = array('Complete', 'Cancel');
			//$where_au = "(DATE(tOrderSchedule.SigningDateTime) = '$curr_date')";
			$this->db->select ( '*' );
			$this->db->from ( 'tApiOutBoundOrders' );
			$this->db->join ( 'mApiTitlePlatform', 'mApiTitlePlatform.OrderSourceUID = tApiOutBoundOrders.OrderSourceUID' , 'left' );
			$this->db->join ( 'torders', 'torders.OrderUID = tApiOutBoundOrders.OrderUID' );
			$this->db->join ( 'tOrderShipping', 'tOrderShipping.ShipmentIdentificationNumber = tApiOutBoundOrders.ProviderOrderNumber' );
			/*$this->db->join ( 'tOrderSchedule', 'tOrderSchedule.ScheduleUID = tOrderShipping.ScheduleUID' );*/
			//$this->db->join ( 'tOrderSign', 'tOrderSign.ScheduleUID = tOrderSchedule.ScheduleUID' );
			$this->db->where( 'tApiOutBoundOrders.ProviderOrderNumber <>', '' );
			$this->db->where( 'tOrderShipping.TrackingNumber <>', '' );
			$this->db->where( 'mApiTitlePlatform.OrderSourceName', 'UPS' );
			//$this->db->where( 'tApiOutBoundOrders.Status', 'Accepted' );
			//$this->db->where( 'tOrderShipping.IsManualCancel', '0' );
			//$this->db->where( 'tOrderSign.IsSignConfirmDone', '0' );
			//$this->db->where_not_in( 'tOrderSchedule.ScheduleStatus', $status );
			//$this->db->where( $where_au );
			$this->db->where( 'tOrderShipping.ShippingUID', $ShippingUID );
			$this->db->where( 'tOrderShipping.OrderUID', $OrderUID );
			$this->db->group_by("tOrderShipping.ShippingUID");
			$query = $this->db->get();
			$ups =  $query->result();

			if($ups){
				$this->db->select("*");
				$this->db->from('mApiTitlePlatform');
				$this->db->where(array("mApiTitlePlatform.OrderSourceName"=> 'UPS'));
				$query = $this->db->get();
				$mApiTitlePlatform = $query->row();

				$ups_orders['UPSOrders'] = $ups;
				$ups_orders['SourceName'] = 'UPS';
				$ups_orders['EventCode'] = 'Track';
				$ups_orders['APIService'] = 'Track';
				$ups_orders['mApiTitlePlatform'] = $mApiTitlePlatform;
				$url_send = $this->config->item("api_url");
				$this->UPSCronPostData($url_send, $ups_orders, 'isgn', 1);
			} else{
				echo json_encode( array('status' => 'failed', 'message'=> 'No Records Found'));exit;
			}
		}

		function UPSCronPostData($url, $ups_orders, $OrgCode, $Action){
			$APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
			$post = json_encode($ups_orders);
			$OrderSourceUID = $ups_orders['mApiTitlePlatform']->OrderSourceUID;
			$OrderSourceName = $ups_orders['mApiTitlePlatform']->OrderSourceName;

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
				$NoteType = $this->GetNoteTypeUID('API Note');
				$SectionUID = $NoteType->SectionUID;
				$ups_orders_response = json_decode($response,true);

				foreach ($ups_orders_response as $key => $value) {
					$Notes = '';
					$Error_Message = '';
					$Notes.= '<b>Tracking Status:</b><br>';
					$orderstatus_response = $value['response'];
					$ShippingDetails = $value['ShippingDetails'];
					$ApiOutBoundOrderUID = $ShippingDetails['ApiOutBoundOrderUID'];
					$OrderUID = $ShippingDetails['OrderUID'];
					$OrderNumber = $ShippingDetails['OrderNumber'];
					$ProviderOrderNumber = $ShippingDetails['ProviderOrderNumber'];
					$ShippingUID = $ShippingDetails['ShippingUID'];
					$ShippingDetails['OrderSourceUID'] = $OrderSourceUID;

					$fieldArray = json_decode($orderstatus_response, true);
					$xml_array_json = $fieldArray['response'] ;
					$ResponseStatusCode = $xml_array_json['Response']['ResponseStatusCode'];

					if($ResponseStatusCode ==  1){
						$ReadTrackResponse = $this->ReadTrackResponse($xml_array_json,$ShippingDetails);
						if($ReadTrackResponse){
							//$OrderTrackers = $ReadTrackResponse['OrderTrackers'];							
							$Notes.= $ReadTrackResponse['TrackEventNotes'];
						} else {
							$Notes.= 'Error Occurs';
						}
					} else {
						$Error_Message.= '<b>Response</b> <br>';
						$Error_Message.= 'ErrorSeverity: '.$xml_array_json['Response']['Error']['ErrorSeverity'].'<br>';
						$Error_Message.= 'ErrorCode: '.$xml_array_json['Response']['Error']['ErrorCode'].'<br>';
						$Error_Message.= 'ErrorDescription: '.$xml_array_json['Response']['Error']['ErrorDescription'].'<br>';
						$Notes.='ProviderOrderNumber: '.$ProviderOrderNumber.'<br> Message: '.$Error_Message;
					}

					$NoteType = $this->GetNoteTypeUID('API Note');
					$SectionUID = $NoteType->SectionUID;
					$NoteData = json_encode($data);
					$insert_notes = array(
						'Note' => $Notes,
						'EventCode' => 'OrderStatus',
						'SectionUID' => $SectionUID,
						'OrderUID' => $OrderUID,
						'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
						'CreatedByAPI' => $OrderSourceUID,
						'CreatedOn' => date('Y-m-d H:i:s')
					);       

					$this->db->insert("tordernotes", $insert_notes);

					$data1['ModuleName']=$Notes.'-insert';
					$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
					$data1['DateTime']=date('y-m-d H:i:s');
					$data1['TableName']='tordernotes';
					$data1['OrderUID']=$OrderUID;
					$data1['OrderSourceUID']=$OrderSourceUID;
					$data1['ApiOutBoundOrderUID']=$ApiOutBoundOrderUID;
					$data1['UserUID']=$this->session->userdata('UserUID');                        
					$this->Common_model->Audittrail_insert($data1);

					if($Action == 1){
						$this->load->model('shipping/shipping_model');
						$res = $this->shipping_model->GetShipTrackingDetailsByCron($ShippingDetails);
						if($res){
							echo json_encode( array('status' => 'success', 'message'=> 'Track Event Sended', 'htmlresponse'=> $res));exit;
						}else{
							echo json_encode( array('status' => 'failed', 'message'=> 'No Records Found'));exit;
						}
					}
				}
			}
		}

		/**
		* Function to track bin status
		*
		* @param int binUID
		* 
		* @throws no exception
		* @author D.Samuel Prabhu
		* @return nothing 
		* @since 24 JAN 2020 
		*
		*/ 


		function UPSOrderTrackingByBinUID()
		{
	      $post = $this->input->post();
	      $BinUID = $this->input->post('BinUID');
	      $date = new DateTime("now");
	      $curr_date = $date->format('Y-m-d ');
	      $status = array('Complete', 'Cancel');
	      //$where_au = "(DATE(tOrderSchedule.SigningDateTime) = '$curr_date')";
	      $this->db->select ( '*' );
	      $this->db->from ( 'tApiOutBoundOrders' );
	      $this->db->join ( 'mApiTitlePlatform', 'mApiTitlePlatform.OrderSourceUID = tApiOutBoundOrders.OrderSourceUID' , 'left' );
	      $this->db->join ( 'tBin', 'tBin.BinUID = tApiOutBoundOrders.BinUID' );
	      $this->db->where( 'tApiOutBoundOrders.ProviderOrderNumber <>', '' );
	      $this->db->where( 'tBin.TrackingNumber <>', '' );
	      $this->db->where( 'mApiTitlePlatform.OrderSourceName', 'UPS' );
	      $this->db->where( 'tApiOutBoundOrders.BinUID', $BinUID );
	      $query = $this->db->get();
	      $ups =  $query->result();

	      if($ups){
	        $this->db->select("*");
	        $this->db->from('mApiTitlePlatform');
	        $this->db->where(array("mApiTitlePlatform.OrderSourceName"=> 'UPS'));
	        $query = $this->db->get();
	        $mApiTitlePlatform = $query->row();

	        $ups_orders['UPSOrders'] = $ups;
	        $ups_orders['SourceName'] = 'UPS';
	        $ups_orders['EventCode'] = 'Track';
	        $ups_orders['APIService'] = 'Track';
	        $ups_orders['isBulk'] = true;
	        $ups_orders['mApiTitlePlatform'] = $mApiTitlePlatform;
	        $url_send = $this->config->item("api_url");
	        $this->UPSCronBinPostData($url_send, $ups_orders, 'isgn', 1);
	      } else{
	        echo json_encode( array('status' => 'failed', 'message'=> 'No Records Found'));exit;
	      }
	    }

		/**
		* Function to post Bin Cron Data
		*
		* @param string url to api url
		* @param array ups_orders detail
		* 
		* @throws no exception
		* @author D.Samuel Prabhu
		* @return nothing 
		* @since 24 JAN 2020 
		*
		*/ 

	    function UPSCronBinPostData($url, $ups_orders, $OrgCode, $Action)
	    {
			$APiAuthKeyDetails = $this->Common_model->getAPiAuthKey();
			$post = json_encode($ups_orders);
			$OrderSourceUID = $ups_orders['mApiTitlePlatform']->OrderSourceUID;
			$OrderSourceName = $ups_orders['mApiTitlePlatform']->OrderSourceName;

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
				$NoteType = $this->GetNoteTypeUID('API Note');
				$SectionUID = $NoteType->SectionUID;
				$ups_orders_response = json_decode($response,true);

				foreach ($ups_orders_response as $key => $value) {
					$Notes = '';
					$Error_Message = '';
					$Notes.= '<b>Tracking Status:</b><br>';
					$orderstatus_response = $value['response'];
					$ShippingDetails = $value['ShippingDetails'];
					$ApiOutBoundOrderUID = $ShippingDetails['ApiOutBoundOrderUID'];
					$BinUID = $ShippingDetails['BinUID'];
					$OrderUID = $ShippingDetails['OrderUID'];
					$OrderNumber = $ShippingDetails['OrderNumber'];
					$ProviderOrderNumber = $ShippingDetails['ProviderOrderNumber'];
					$BinUID = $ShippingDetails['BinUID'];
					$ShippingDetails['OrderSourceUID'] = $OrderSourceUID;

					$fieldArray = json_decode($orderstatus_response, true);
					$xml_array_json = $fieldArray['response'] ;
					$ResponseStatusCode = $xml_array_json['Response']['ResponseStatusCode'];

					if($ResponseStatusCode ==  1){
						$ReadTrackResponse = $this->ReadBinTrackResponse($xml_array_json,$ShippingDetails);

						if($ReadTrackResponse){
							//$OrderTrackers = $ReadTrackResponse['OrderTrackers'];							
							$Notes.= $ReadTrackResponse['TrackEventNotes'];
						} else {
							$Notes.= 'Error Occurs';
						}
					} else {
						$Error_Message.= '<b>Response</b> <br>';
						$Error_Message.= 'ErrorSeverity: '.$xml_array_json['Response']['Error']['ErrorSeverity'].'<br>';
						$Error_Message.= 'ErrorCode: '.$xml_array_json['Response']['Error']['ErrorCode'].'<br>';
						$Error_Message.= 'ErrorDescription: '.$xml_array_json['Response']['Error']['ErrorDescription'].'<br>';
						$Notes.='ProviderOrderNumber: '.$ProviderOrderNumber.'<br> Message: '.$Error_Message;
					}

					$NoteType = $this->GetNoteTypeUID('API Note');
					$SectionUID = $NoteType->SectionUID;
					$NoteData = json_encode($data);
					$insert_notes = array(
						'Note' => $Notes,
						'EventCode' => 'OrderStatus',
						'SectionUID' => $SectionUID,
						'OrderUID' => $OrderUID,
						'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
						'CreatedByAPI' => $BinUID,
						'CreatedOn' => date('Y-m-d H:i:s')
					);       

					$this->db->insert("tordernotes", $insert_notes);

					$data1['ModuleName']='Bulk Shipping';
					$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
					$data1['DateTime']=date('y-m-d H:i:s');
					$data1['TableName']='tordernotes';					
					$data1['Feature']=$ShippingDetails['BinUID'];
					$data1['content']= htmlentities($Notes);
					$data1['OrderSourceUID']=$OrderSourceUID;
					$data1['ApiOutBoundOrderUID']=$ApiOutBoundOrderUID;
					$data1['UserUID']=$this->session->userdata('UserUID');  

					$this->Common_model->Audittrail_insert($data1);

					if($Action == 1){
						$this->load->model('shipping/shipping_model');
						$res = $this->GetBinTrackingDetailsByCron($ShippingDetails);
						if($res){
							echo json_encode( array('status' => 'success', 'message'=> 'Track Event Sended', 'htmlresponse'=> $res));exit;
						}else{
							echo json_encode( array('status' => 'failed', 'message'=> 'No Records Found'));exit;
						}
					}
				}
			}
		}


	/**
	* Function to format bin tracking response 
	* @param array
	* 
	* @throws no exception
	* @author D.Samuel Prabhu
	* @return nothing 
	* @since 24 JAN 2020 
	*
	*/ 
	function GetBinTrackingDetailsByCron($post)
	{
		$str = '';
		
			$this->db->select('*');
			$this->db->from('tOrderShippingTracker');
			$this->db->where('BinUID',$post['BinUID']);
			$this->db->group_by('tOrderShippingTracker.ActivityDate');
			$this->db->order_by('tOrderShippingTracker.ActivityDate', "DESC");
			$tOrderShippingDateTracker = $this->db->get()->result();		

			if($tOrderShippingDateTracker){			
				$str.='<button type="button" class="close order_track_close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>';
				$str.='<h6 class="deliver_name">Delivered by UPS</h6>';
				$str.='<h6 class="tract_id">Tracking ID : '.$post['TrackingNumber'].'</h6>';
				foreach ($tOrderShippingDateTracker as $key => $value) {

					$this->db->select('*');
					$this->db->from('tOrderShippingTracker');
					$this->db->where('BinUID',$post['BinUID']);
					$this->db->where('ActivityDate',date('Y-m-d', strtotime($value->ActivityDate)));
					$this->db->order_by('tOrderShippingTracker.ActivityTime', "DESC");
					$tOrderShippingTimeTracker = $this->db->get()->result();

					$str.='<div class="row">
					<div class="date_desc">
					<div class="col-md-12"><h6 style="margin-top:8px;">'.date("l j F Y", strtotime($value->ActivityDate)).'</h6>
					</div>
					</div>';	

					foreach ($tOrderShippingTimeTracker as $row => $ele) {
						$location = [];
						$location[] = $ele->ActivityLocationCity;
						$location[] = $ele->ActivityLocationStateProvinceCode;
						$location[] = $ele->ActivityLocationPostalCode;
						$l = array_filter($location);
						$addr = implode(', ', $l);
						if($addr){
							$delivery_location = $addr.', '.$ele->ActivityLocationCountryCode.'.';
						} else{
							$delivery_location = $ele->ActivityLocationCountryCode.'.';
						}

						$str.='<div class="time_desc">
						<div class="col-md-2">'.date('H:i a', strtotime($ele->ActivityTime)).'</div>
						<div class="col-md-10">'.$ele->ActivityStatusTypeDescription.'<span class="order_crnt_loc">'.$delivery_location.'</span>
						</div>
						</div>';
					}

					$str.='</div>';
				}
			}
	
		return $str;
	}


	function ReadBinTrackResponse($xml_array_json,$ShippingDetails='') 
	{

			$this->db->trans_begin();
			$Activity = $xml_array_json['Shipment']['Package']['Activity'];
			$OrderTrackers = [];

			$BinUID = $ShippingDetails['BinUID'];
			$TrackEventNotes='';

			$this->db->where(['BinUID'=>$BinUID]);
			$this->db->delete('tOrderShippingTracker');

			foreach ($Activity as $key => $value) {

				if($key == 0){
					$TrackEventNotes.= 'Tracking Number: '. $xml_array_json['Shipment']['Package']['TrackingNumber'].'<br>';
					$TrackEventNotes.= 'SignedBy: '. $value['ActivityLocation']['SignedForByName'].'<br>';
					$TrackEventNotes.= 'Status: '. $value['Status']['StatusType']['Code'].'-'.$value['Status']['StatusType']['Description'].'<br>';
					$TrackEventNotes.= 'Date: '. date('m/d/Y', strtotime($value['Date'])).'<br>';
					$TrackEventNotes.= 'Time: '. date('H:i:s', strtotime($value['Time'])).'<br>';

					$UPSTrackingStatus = $this->config->item('UPSTrackingStatus');
					$FinalStatusCode = $value['Status']['StatusType']['Code'];
					$FinalStatus = $UPSTrackingStatus[$FinalStatusCode];
				}

				$TrackResposeDetails['BinUID'] = $ShippingDetails['BinUID'];
				$TrackResposeDetails['OrderSourceUID'] = $ShippingDetails['OrderSourceUID'];
				$TrackResposeDetails['CreatedByAPI'] = $ShippingDetails['OrderSourceUID'];

				$TrackResposeDetails['ShipmentServiceCode'] = $xml_array_json['Shipment']['Service']['Code'];
				$TrackResposeDetails['ShipmentServiceDescription'] = $xml_array_json['Shipment']['Service']['Description'];
				$TrackResposeDetails['ShipmentIdentificationNumber'] = $xml_array_json['Shipment']['ShipmentIdentificationNumber'];
				$TrackResposeDetails['TrackingNumber'] = $xml_array_json['Shipment']['Package']['TrackingNumber'];
				$TrackResposeDetails['PickupDate'] = date('Y-m-d', strtotime($xml_array_json['Shipment']['PickupDate']));
				$TrackResposeDetails['WeightCode'] = $xml_array_json['Shipment']['Package']['PackageWeight']['UnitOfMeasurement']['Code'];
				$TrackResposeDetails['Weight'] = $xml_array_json['Shipment']['Package']['PackageWeight']['Weight'];

				$TrackResposeDetails['ActivityLocationCity'] = $value['ActivityLocation']['Address']['City'];
				$TrackResposeDetails['ActivityLocationStateProvinceCode'] = $value['ActivityLocation']['Address']['StateProvinceCode'];
				$TrackResposeDetails['ActivityLocationPostalCode'] = $value['ActivityLocation']['Address']['PostalCode'];
				$TrackResposeDetails['ActivityLocationCountryCode'] = $value['ActivityLocation']['Address']['CountryCode'];
				$TrackResposeDetails['ActivityCode'] = $value['ActivityLocation']['Code'];
				$TrackResposeDetails['ActivityDescription'] = $value['ActivityLocation']['Description'];
				$TrackResposeDetails['ActivitySignedForByName'] = $value['ActivityLocation']['SignedForByName'];
				$TrackResposeDetails['ActivityStatusTypeCode'] = $value['Status']['StatusType']['Code'];
				$TrackResposeDetails['ActivityStatusTypeDescription'] = $value['Status']['StatusType']['Description'];
				$TrackResposeDetails['ActivityStatusCode'] = $value['Status']['StatusCode']['Code'];
				$TrackResposeDetails['ActivityDate'] = date('Y-m-d', strtotime($value['Date']));
				$TrackResposeDetails['ActivityTime'] = date('H:i:s', strtotime($value['Time']));

				$OrderTrackers[] = $TrackResposeDetails;

				$this->db->insert('tOrderShippingTracker',$TrackResposeDetails);
			}

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return false;
			}else{
				$this->db->trans_commit();

                
				$this->db->select('BinUID, OrderUID, ShippingUID');
				$this->db->from('tBinOrders');
				$this->db->where('BinUID',$ShippingDetails['BinUID']);
				$ShippedDetails = $this->db->get()->result_array();

                $ShippingUIDs = array_column($ShippedDetails,'ShippingUID');
                 
                if(!empty($ShippingUIDs))
                {
                	$Status['ShippingStatus'] = $FinalStatus;
					$this->db->where_in('ShippingUID', $ShippingUIDs);
					$this->db->update('tOrderShipping',$Status);
                }  
	  
				
				$arrayResult = array('TrackEventNotes' => $TrackEventNotes, 'OrderTrackers' => $OrderTrackers, 'ShippedDetails' => $ShippedDetails);
				
				return $arrayResult;
			}
		}



		function ReadTrackResponse($xml_array_json,$ShippingDetails='') {

			$this->db->trans_begin();
			$Activity = $xml_array_json['Shipment']['Package']['Activity'];
			$OrderTrackers = [];

			$ShippingUID = $ShippingDetails['ShippingUID'];
			$OrderUID = $ShippingDetails['OrderUID'];

			$this->db->where(['ShippingUID'=>$ShippingUID,'OrderUID'=>$OrderUID]);
			$this->db->delete('tOrderShippingTracker');

			foreach ($Activity as $key => $value) {

				if($key == 0){
					$TrackEventNotes.= 'Tracking Number: '. $xml_array_json['Shipment']['Package']['TrackingNumber'].'<br>';
					$TrackEventNotes.= 'SignedBy: '. $value['ActivityLocation']['SignedForByName'].'<br>';
					$TrackEventNotes.= 'Status: '. $value['Status']['StatusType']['Code'].'-'.$value['Status']['StatusType']['Description'].'<br>';
					$TrackEventNotes.= 'Date: '. date('m/d/Y', strtotime($value['Date'])).'<br>';
					$TrackEventNotes.= 'Time: '. date('H:i:s', strtotime($value['Time'])).'<br>';

					$UPSTrackingStatus = $this->config->item('UPSTrackingStatus');
					$FinalStatusCode = $value['Status']['StatusType']['Code'];
					$FinalStatus = $UPSTrackingStatus[$FinalStatusCode];
				}

				$TrackResposeDetails['OrderUID'] = $ShippingDetails['OrderUID'];
				$TrackResposeDetails['ShippingUID'] = $ShippingDetails['ShippingUID'];
				$TrackResposeDetails['OrderSourceUID'] = $ShippingDetails['OrderSourceUID'];
				$TrackResposeDetails['CreatedByAPI'] = $ShippingDetails['OrderSourceUID'];

				$TrackResposeDetails['ShipmentServiceCode'] = $xml_array_json['Shipment']['Service']['Code'];
				$TrackResposeDetails['ShipmentServiceDescription'] = $xml_array_json['Shipment']['Service']['Description'];
				$TrackResposeDetails['ShipmentIdentificationNumber'] = $xml_array_json['Shipment']['ShipmentIdentificationNumber'];
				$TrackResposeDetails['TrackingNumber'] = $xml_array_json['Shipment']['Package']['TrackingNumber'];
				$TrackResposeDetails['PickupDate'] = date('Y-m-d', strtotime($xml_array_json['Shipment']['PickupDate']));
				$TrackResposeDetails['WeightCode'] = $xml_array_json['Shipment']['Package']['PackageWeight']['UnitOfMeasurement']['Code'];
				$TrackResposeDetails['Weight'] = $xml_array_json['Shipment']['Package']['PackageWeight']['Weight'];

				$TrackResposeDetails['ActivityLocationCity'] = $value['ActivityLocation']['Address']['City'];
				$TrackResposeDetails['ActivityLocationStateProvinceCode'] = $value['ActivityLocation']['Address']['StateProvinceCode'];
				$TrackResposeDetails['ActivityLocationPostalCode'] = $value['ActivityLocation']['Address']['PostalCode'];
				$TrackResposeDetails['ActivityLocationCountryCode'] = $value['ActivityLocation']['Address']['CountryCode'];
				$TrackResposeDetails['ActivityCode'] = $value['ActivityLocation']['Code'];
				$TrackResposeDetails['ActivityDescription'] = $value['ActivityLocation']['Description'];
				$TrackResposeDetails['ActivitySignedForByName'] = $value['ActivityLocation']['SignedForByName'];
				$TrackResposeDetails['ActivityStatusTypeCode'] = $value['Status']['StatusType']['Code'];
				$TrackResposeDetails['ActivityStatusTypeDescription'] = $value['Status']['StatusType']['Description'];
				$TrackResposeDetails['ActivityStatusCode'] = $value['Status']['StatusCode']['Code'];
				$TrackResposeDetails['ActivityDate'] = date('Y-m-d', strtotime($value['Date']));
				$TrackResposeDetails['ActivityTime'] = date('H:i:s', strtotime($value['Time']));

				$OrderTrackers[] = $TrackResposeDetails;

				$this->db->insert('tOrderShippingTracker',$TrackResposeDetails);
			}

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return false;
			}else{
				$this->db->trans_commit();
				$Status['ShippingStatus'] = $FinalStatus;
				$this->db->where('ShippingUID', $ShippingDetails['ShippingUID']);
				$this->db->update('tOrderShipping',$Status);
				$arrayResult = array('TrackEventNotes' => $TrackEventNotes, 'OrderTrackers' => $OrderTrackers);
				return $arrayResult;
			}
		}

		function TrackResponseHtml($post){

			$this->db->select('*');
			$this->db->from('tOrderShipping');
			$this->db->where('ShippingUID',$post['ShippingUID']);
			$this->db->where('OrderUID',$post['OrderUID']);
			$tOrderShipping = $this->db->get()->row();
			$TrackingNumber = $tOrderShipping->TrackingNumber;
			$str = '';
			if($tOrderShipping){			
				$this->db->select('*');
				$this->db->from('tOrderShippingTracker');
				$this->db->where('ShippingUID',$post['ShippingUID']);
				$this->db->where('OrderUID',$post['OrderUID']);
				$this->db->group_by('tOrderShippingTracker.ActivityDate');
				$this->db->order_by('tOrderShippingTracker.ActivityDate', "DESC");
				$tOrderShippingDateTracker = $this->db->get()->result();		

				if($tOrderShippingDateTracker){			
					$str.='<button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="top: -31px;color: #fff;"><i class="material-icons">clear</i></button>';
					$str.='<h6 class="deliver_name">Delivered by UPS</h6>';
					$str.='<h6 class="tract_id">Tracking ID : '.$TrackingNumber.'</h6>';
					foreach ($tOrderShippingDateTracker as $key => $value) {

						$this->db->select('*');
						$this->db->from('tOrderShippingTracker');
						$this->db->where('ShippingUID',$post['ShippingUID']);
						$this->db->where('OrderUID',$post['OrderUID']);
						$this->db->where('ActivityDate',date('Y-m-d', strtotime($value->ActivityDate)));
						$this->db->order_by('tOrderShippingTracker.ActivityTime', "DESC");
						$tOrderShippingTimeTracker = $this->db->get()->result();

						$str.='<div class="row">
						<div class="date_desc">
						<div class="col-md-12"><h6 style="margin-top:8px;">'.date("l j F Y", strtotime($value->ActivityDate)).'</h6>
						</div>
						</div>';	

						foreach ($tOrderShippingTimeTracker as $row => $ele) {
							$str.='<div class="time_desc">
							<div class="col-md-2">'.date('H:i a', strtotime($ele->ActivityTime)).'</div>
							<div class="col-md-10">'.$ele->ActivityStatusTypeDescription.'<span class="order_crnt_loc">'.$ele->ActivityLocationCity.', '.$ele->ActivityLocationCountryCode.'</span>
							</div>
							</div>';
						}

						$str.='</div>';
					}
				}
			}
			return $str;
		}

		public function xml() {
			header('Content-Type: application/xml');
			libxml_use_internal_errors(true);
			$response = file_get_contents("php://input");
			$headers = apache_request_headers();
			$Valid_Xml = simplexml_load_string($response);
			$xml_json = json_encode($Valid_Xml);
			$xml_array_json = json_decode($xml_json,true);

			$ReadTrackResponse = $this->ReadTrackResponse($xml_array_json);

			if($ReadTrackResponse){
				$OrderTrackers = $ReadTrackResponse['OrderTrackers'];
				$html = $this->TrackResponseHtml($OrderTrackers);
				$Notes.= $ReadTrackResponse['TrackEventNotes'];
			} else {
				$Notes.= 'Error Occurs';
			}
		}
		/* End UPS - CRON File Tracking */


		function NotaryGoAttachment($fieldArray,$CreatedByAPI){
			$document_count = 0;
			$currentdate=date('Ymd');
			$OrderUID= $fieldArray['OrderUID'];
			$this->db->select('*');
			$this->db->from('torders');
			$this->db->where('OrderUID',$OrderUID);
			$torders=$this->db->get()->row();
			$OrderNumber = $torders->OrderNumber;
			$Attachments = [];
			$Attachments = $fieldArray['Attachments'];
			$ProviderOrderNumber = $fieldArray['ProviderOrderNumber'];
			$i = 1;
			
			foreach ($Attachments as $key => $value) 
			{				
				$Document = $value['Document'];  
				$FinalPDF = base64_decode($Document);   
				$current_date=date("Y-m-d-h-i-s");
				$randomnumber=str_replace("-","",$current_date);
				$Responsedocname = 'NotaryGo_'.$torders->OrderNumber.'_'.$i.$randomnumber.'.pdf';
				if($torders->OrderDocsPath!= NULL)
				{
					$OrderDocs_Path = $torders->OrderDocsPath;
				}
				else
				{
					$query = $this->db->query("update torders SET OrderDocsPath ='uploads/searchdocs/".$currentdate."/".$torders->OrderNumber."/"."' Where OrderNumber='".$torders->OrderNumber."' ");

					$OrderDocs_Path = 'uploads/searchdocs/'.$currentdate.'/'.$torders->OrderNumber."/";
				}

				$file = FCPATH . $OrderDocs_Path . $Responsedocname;            

				if (!is_dir($OrderDocs_Path)) {
					mkdir($OrderDocs_Path, 0777, true);
				} 

				file_put_contents($file, $FinalPDF);

				$this->db->select('*')->from('musers');
				$this->db->where('LoginID', 'isgn');
				$query=$this->db->get();
				$UserName=$query->row()->UserName;
				$UserID=$query->row()->UserUID;
				$DocumentTypeUID = $this->common_model->getDocumentTypeUIDByDocTYpe('Signed Package');
				$arrData['OrderUID']=$torders->OrderUID;
				$arrData['DocumentFileName']=$Responsedocname;
				$arrData['DisplayFileName']=$Responsedocname;
				$arrData['DocumentTypeUID']= $DocumentTypeUID;
				$arrData['UploadedDate']=date('Y-m-d H:i:s');
				$arrData['DocumentCreatedDate']=date('Y-m-d H:i:s');
				$arrData['UploadedUserUID']= $UserID;
				$arrData['TypeOfDocument']= $DocumentTypeUID;
				$arrData['SearchModeUID']= '6';
				$arrData['ProviderOrderNumber']= $ProviderOrderNumber;
				$Attachment=$this->Updateattachmentdetails($arrData,$CreatedByAPI);
				unset($arrData);
			}
			unset($Attachments);
		}

		public function Updateattachmentdetails($data,$CreatedByAPI){
			$attach_data = $data;
			unset($data['ProviderOrderNumber']);
			$this->db->insert('torderdocuments',$data);

			if($this->db->affected_rows()>0)
			{    
				$Notes = $this->AttachNotes($attach_data,$CreatedByAPI);
			}else{
				return false;
			}    
		}

		function AttachNotes($data,$CreatedByAPI){
			$ProviderOrderNumber = $data['ProviderOrderNumber'];
			$OrderUID = $data['OrderUID'];
			$InBoundUID = (isset($data['InBoundUID']) ? $data['InBoundUID'] : null);
			$DocumentFileName = $data['DocumentFileName'];
			$NoteType = $this->GetNoteTypeUID('API Note');
			$SectionUID = $NoteType->SectionUID;

			$this->db->select("*");
			$this->db->from('mApiTitlePlatform');
			$this->db->where(array("mApiTitlePlatform.OrderSourceUID"=>$CreatedByAPI));
			$query = $this->db->get();
			$SourceName = $query->row();
			$OrderSourceName=$SourceName->OrderSourceName;

			$insert_notes = array(
				'Note' => 'Provider CertID :'.$ProviderOrderNumber.'<br>'.$DocumentFileName .' is attached by API ( '.$OrderSourceName.' )',
				'SectionUID' => $SectionUID,
				'OrderUID' => $OrderUID,
				'EventCode' => 'Attachment',
				'RoleType' => '1,2,3,4,5,6,7,8,9,11,12',
				'CreatedByAPI' => $CreatedByAPI,
				'CreatedOn' => date('Y-m-d H:i:s')
			);
			$result = $this->db->insert("tordernotes", $insert_notes);

		}

		function OrderRevisionRequestResponseDocument(){
			$post = $this->input->post();	
			$OrderUID = $this->input->post('OrderUID');		
			$this->load->model('Real_ec_model');
			$EventCode = 'RevisedDeliverProduct';
			$post['EventCode'] = $EventCode;
			$res = $this->Real_ec_model->OrderRevisionRequestDoc($post);
		}

		function OrderRevisionRequestResponse(){
			$post = $this->input->post();
			$OrderUID = $this->input->post('OrderUID');
			$this->load->model('Real_ec_model');
			$DisputeReplyDetails = $this->input->post('DisputeReplyDetails');
			$EventCode ='QCCorrectionsCompleted'; 
			$Remarks = [];
			foreach($DisputeReplyDetails as $Dispute) {
				$Remarks[] = $this->clean($Dispute['Comments']);
		 	}
		 	$Comments = implode(', ', $Remarks);
			//$res = $this->Real_ec_model->OrderRevisionRequest($post);
			$res = $this->Real_ec_model->OrderRevisionRequest($OrderUID,$Comments,$EventCode);
		}

		function OrderRevisionRequestAccepted(){
			$post = $this->input->post();
			$OrderUID = $this->input->post('OrderUID');
			$this->load->model('Real_ec_model');
			$DisputeReplyDetails = $this->input->post('DisputeReplyDetails');
			$EventCode ='RevisionRequestApproved'; 
			$Remarks = [];
			foreach($DisputeReplyDetails as $Dispute) {
				$Remarks[] = $this->clean($Dispute['Comments']);
		 	}
		 	$Comments = implode(', ', $Remarks);
			//$res = $this->Real_ec_model->OrderRevisionRequest($post);
			$res = $this->Real_ec_model->OrderRevisionRequest($OrderUID,$Comments,$EventCode);
		}

		/**
		* @description D2TINT-44 - Display selected document details and doctypes
		*
		* @param  OrderUID, DocumentName, Document Types
		* 
		* @author Yagavi G <yagavi.g@avanzegroup.com>
		* @return JSON value
		* @since 2nd April 2020
		*
		*/ 				

		function GetDocumentTypesForFinalReports(){
			$post = $this->input->post();
			$DocumentFileName=[];
			$OrderUID = $post['OrderUID'];
			$DocumentFileName = $post['documentfilenames'];
			$EventCode = $post['EventCode'];

			$OrderSourceName = $this->common_model->GetApiSourceName($OrderUID); 
			$SourceName = $OrderSourceName->OrderSourceName;
			$OrderSourceUID = $OrderSourceName->OrderSourceUID;

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

				$DocumentTypeCode = $this->common_model->GetDocumentTypeCodeByUID($OrderDocDetails->TypeOfDocument);
				if(!empty($DocumentTypeCode)){
					$TypeOfDocument = $DocumentTypeCode;
				} else {
					$DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $OrderSourceUID);
					$TypeOfDocument = $this->common_model->GetDocumentTypeCodeByUID($DocumentTypeUID);
				}

				if($SourceName=='Keystone') {
					$KeystoneDocTypes = $this->config->item('KeystoneDocTypes');
					if(!empty($KeystoneDocTypes)){
						$TypeOfDocument = $KeystoneDocTypes[$TypeOfDocument];
					} else {
						$DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $OrderSourceUID);
						$TypeOfDocument = $this->common_model->GetDocumentTypeCodeByUID($DocumentTypeUID);
					}
				}

				$FileSize = $this->common_model->filesize_formatted($OrderDocsPath.$FileName);

				$obj= [];
				$obj['OrderDocsPath']=$OrderDocsPath;
				$obj['FileSize']=$FileSize;
				$obj['DocumentFileName']=$FileName;
				$obj['TemplateName']=$TemplateName;
				$obj['DocumentCreatedDate']=$DocumentCreatedDate;
				$obj['Content']=$Content;
				$obj['DocTypeCode']=$TypeOfDocument;
				$obj['DocTypeName']=$TypeOfDocument;
				array_push($Documents, $obj);
				unset($obj);
			}	

			$DefaultDoctypes = $this->config->item('DefaultDoctypes');
			$KeystoneDocTypes = $this->config->item('KeystoneDocTypes');

			$order_details = $this->common_model->get_orderdetails($OrderUID);
			$Orderdetailsss = $order_details;
			if ($Orderdetailsss->IsClosingProduct) {
				$KeystoneFinalDocTypes = $this->config->item('KeystoneClosingFinalDocTypes');
			} else {
				$KeystoneFinalDocTypes = $this->config->item('KeystoneTitleFinalDocTypes');
			}

			$KeystoneDocumentDocTypes = $this->config->item('KeystoneDocumentDocTypes');
			
			if(trim($SourceName) == 'Keystone'){
				/*@author Yagavi G <yagavi.g@avanzegroup.com @purpose To change the Doctype for the Attachment @since 20th April 2020>*/
				if($EventCode == 'FinalDoc'){
					$TypeOfDocument = $KeystoneFinalDocTypes;
				} else if($EventCode == 'Doc'){
					$TypeOfDocument = $KeystoneDocumentDocTypes;
				}
			} else {
				$TypeOfDocument = $DefaultDocTypes;
			}

			$html = '';
			$html.='<table id="FinalReportTable" width="100%" style="font-size: 11px;" class="table table-striped table-inverse ">
            <thead class="thead-inverse">
              <tr>
                <th width="5%">Action</th>
                <th width="15%">Attachments</th>
                <th width="15%">File Size</th>
                <th width="20%">Document Type <span style="color:red">*</span></th>
              </tr>
            </thead>
            <tbody>';

			foreach ($Documents as $key => $value) {
				$i = $key+1;
				$html.='<tr data-file-path="'.$value['OrderDocsPath'].'" data-documentfilename="'.$value['DocumentFileName'].'">';
				$html.='<td><div class="be-checkbox be-checkbox-color inline">
				<input type="checkbox"  class="final_doc" checked id="finaldoc'.$i.'">
				<label for="finaldoc'.$i.'"></label></div></td>';

                $html.='<td style="text-align: left;">'.$value['DocumentFileName'].'</td>';
                $html.='<td style="text-align: left;">'.$value['FileSize'].'</td>';
                $html.='<td style="text-align: left;">';
                $html.='<select class="mdl-select2 select2 mdl-textfield__input input-xs APITypeOfDocument required" name="APITypeOfDocument" id="APITypeOfDocument" style="font-size:11px;">';
                $html.='<option value=""></option>';                               
                foreach ($TypeOfDocument as $type => $ele) {
                	if($value['DocTypeCode']==$ele){
                		$html.='<option value="'.$type.'" selected >'.$ele.'</option>';
                	} else {
                		$html.='<option value="'.$type.'">'.$ele.'</option>';
                	}
                }
                $html.='</select>';
                $html.='</td>';
                $html.='</tr>';
			}

			$html.='</tbody>';
			$html.='</table>';

			$result = array('html' => $html, 'msg' => 'Successful');
			echo json_encode($result);
		}

		/**
		* @purpose D2TINT-44 - Send Final Reports to API with selected doctypes
		*
		* @param  OrderUID, DocumentName, Document Types
		* 
		* @author Yagavi G <yagavi.g@avanzegroup.com>
		* @return true
		* @since 2nd April 2020
		*
		*/ 		

		function SendFinalReports(){
			$post = $this->input->post();
			$OrderUID = $this->input->post('OrderUID');
			$order_details = $this->common_model->get_orderdetails($OrderUID);
			$Orderdetailsss = $order_details;
			$OrderDetails = $this->GetOrderDetails($OrderUID);
      		$OrderNumber = $OrderDetails->OrderNumber;
      		$TotalFiles = $this->input->post('TotalFiles');
      		$DocumentFileName = $this->input->post('documentfilenames');
      		$DocumentTypes = $this->input->post('DocumentTypes');
      		$DocEventCode = $this->input->post('EventCode');
			$Is_Api = $this->CheckApiOrders($OrderUID);
			$Details = $this->GetInBoundTransactionDetails($OrderUID);
			$InBoundUID = $Details->InBoundUID;
			$TransactionID = $Details->TransactionID;
			$ApiOrderRequestUID = $Details->ApiOrderRequestUID;

			$AppSourceName = $this->GetSourceName($OrderUID); 
            $SourceName = trim($AppSourceName->OrderSourceName);
            $OrderSourceUID = $AppSourceName->OrderSourceUID;

			$col_name = array();
	    	$col_val = array();

			if($Is_Api){
				$url_send = $this->config->item("api_url");
				$Documents = [];
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
					$TypeOfDocument = $DocumentTypes[$key];
					$DocTypeCode = $TypeOfDocument;

					if($SourceName=='Keystone') {
						/*@author Yagavi G <yagavi.g@avanzegroup.com @purpose To change the Doctype for the Attachment @since 20th April 2020>*/
						if($DocEventCode == 'FinalDoc'){
							if ($Orderdetailsss->IsClosingProduct) {
								$KeystoneDocTypes = $this->config->item('KeystoneClosingFinalDocTypes');
							} else {
								$KeystoneDocTypes = $this->config->item('KeystoneTitleFinalDocTypes');
							}
						} else if($DocEventCode == 'Doc'){
							$KeystoneDocTypes = $this->config->item('KeystoneDocumentDocTypes');							
						}

						if(!empty($KeystoneDocTypes)){
							$TypeOfDocument = $KeystoneDocTypes[$TypeOfDocument];
						}
					}

                     //Start//@author:Shivaraj N <shivaraja.n@avanzegroup.com>
					if($SourceName=='Pabs') {
						$PabsDocTypes = $this->config->item('PabsFinalDocTypes');
						if(!empty($PabsDocTypes)){
							$TypeOfDocument = $PabsDocTypes[$TypeOfDocument];
						}
					}
                      //end//@author:Shivaraj N <shivaraja.n@avanzegroup.com>
					$obj= [];
					$obj['DocumentFileName']=$FileName;
					$obj['TemplateName']=$TemplateName;
					$obj['DocumentCreatedDate']=$DocumentCreatedDate;
					$obj['Content']=$Content;
					$obj['DocTypeCode']=$TypeOfDocument;
					$obj['DocTypeName']=$TypeOfDocument;

					array_push($Documents, $obj);
					unset($obj);
				}

				if($SourceName=='Keystone') {
					/*@author Yagavi G <yagavi.g@avanzegroup.com @purpose To change the Doctype for the Attachment @since 20th April 2020>*/
					if($DocEventCode == 'FinalDoc'){
						if ($Orderdetailsss->IsClosingProduct) {
							/* Closing Order */
							array_push($col_name, 'EventCode');
							array_push($col_val, 'DeliverFinalSS');
							//array_push($col_val, 'AttorneyDocsFinalized');
						} else {
							/* Title Order */
							array_push($col_name, 'EventCode');
							array_push($col_val, 'DeliverProduct');
						}
					} else if($DocEventCode == 'Doc'){
						array_push($col_name, 'EventCode');
						array_push($col_val, 'AddAttachment');							
					}
				}

				/*@author:Shivaraj N <shivaraja.n@avanzegroup.com>*/
				if($SourceName=='Pabs') {
				    /* Final Report */
						array_push($col_name, 'EventCode');
						array_push($col_val, 'AT15');				
				}
                /*@author:Shivaraj N <shivaraja.n@avanzegroup.com>*/
				array_push($col_name, 'InBoundUID');
				array_push($col_val, $InBoundUID);

				array_push($col_name, 'TransactionID');
				array_push($col_val, $TransactionID);

				array_push($col_name, 'Documents');
				array_push($col_val, $Documents);

				array_push($col_name, 'OrderNumber');
				array_push($col_val, $OrderNumber);

				array_push($col_name, 'OrderUID');
				array_push($col_val, $OrderUID);

				array_push($col_name, 'SourceName');
				array_push($col_val, $SourceName);

				array_push($col_name, 'OrderDetails');
				array_push($col_val, $OrderDetails);

				array_push($col_name, 'ApiOrderRequestUID');
				array_push($col_val, $ApiOrderRequestUID);

				array_push($col_name, 'OrderUID');
				array_push($col_val, $OrderUID);

				$column_name = $col_name;
				$column_value = $col_val;   

				$data = array_combine($column_name, $column_value);
				$str_data = json_encode($data);
				$result = $this->sendPostData($url_send, $str_data);
				/*@author Yagavi G <yagavi.g@avanzegroup.com @purpose To change the Doctype for the Attachment @since 20th April 2020>*/
				if($DocEventCode == 'FinalDoc'){
					$fieldArray = array(
						"IsMailSend"=> 1,
						"MailSendBy"=>$this->session->userdata('UserUID'),
						"MailSendDateTime"=>date('Y-m-d H:i:s'),
					);
					$this->db->where('OrderUID',$OrderUID);
					$query = $this->db->update('torders',$fieldArray);
				}

			}	
		}
}
?>
