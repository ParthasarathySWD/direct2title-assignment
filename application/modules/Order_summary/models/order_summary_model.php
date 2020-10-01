<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Order_Summary_Model extends CI_Model {

	
	function __construct()
	{ 
		parent::__construct();
		$this->config->load('keywords');
	}

	/*function CopytorderDocuments($fieldArray){

		$keys = array();
		$values = array();

		$src = './'.$fieldArray['DocumentFilepath'];
		$dst = './'.$fieldArray['CopyDocumentPath'];
		$FileName = $fieldArray['DocumentFileName'];
		$ToOrderUID = $fieldArray['ToOrderUID'];

		$src_file = './'.$fieldArray['DocumentFilepath'].$FileName;
		$dst_file = './'.$fieldArray['CopyDocumentPath'].$FileName;

		mkdir(dirname($dst_file), 0777, true);
		copy($src_file, $dst_file);

		$arr = $this->GettorderDocument($fieldArray);

		foreach($arr as $key => $value)
		{
			if( $key == "OrderUID")
			{
				$key = $key;	
				$value = $ToOrderUID;	
			}
			else
			{
				$key = $key;
				$value = $value;
			}
			
			array_push($keys, $key);
			array_push($values, $value);
		}

		$column_name = $keys;
		$column_value = $values;
		$PostArray = array_combine($column_name, $column_value);

		$query = $this->db->insert("torderdocuments", $PostArray);
	}*/

	function GetDuplicateDetails($OrderDetails)
	{
		$PropertyAddress1 = $OrderDetails->PropertyAddress1;
		$PropertyAddress2 = $OrderDetails->PropertyAddress2;
		$PropertyCityName = addslashes($OrderDetails->PropertyCityName);
		$PropertyStateCode = addslashes($OrderDetails->PropertyStateCode);
		$PropertyZipcode = $OrderDetails->PropertyZipcode;
		$PropertyCountyName = addslashes($OrderDetails->PropertyCountyName);
		$OrderUID = $OrderDetails->OrderUID;
		$LoanNumber = $OrderDetails->LoanNumber;
		$SubProductUID = $OrderDetails->SubProductUID;
		$CustomerUID = $OrderDetails->CustomerUID;
		$ProductUID = $OrderDetails->ProductUID;

		$Addr = strtoupper(trim(trim($PropertyAddress1).' '.trim($PropertyAddress2)));

		$Address = addslashes($Addr);

		$OrderProperertyRoles = $this->common_model->Gettorderpropertyroles($OrderUID);

		$is_duplicate = $this->Orderentry_model->findduplicate($PropertyAddress1,$PropertyAddress2,$PropertyZipcode,$PropertyCityName,$PropertyCountyName,$PropertyStateCode,$LoanNumber, [$SubProductUID], $CustomerUID, $OrderProperertyRoles, [$ProductUID], $OrderUID);

		/*$query = $this->db->query(" SELECT *,TRIM(CONCAT_WS(' ',TRIM(PropertyAddress1),TRIM(PropertyAddress2))) AS whole_name FROM torders
									WHERE torders.OrderUID <> '$OrderUID' 
									AND (torders.PropertyZipcode = '$PropertyZipcode'
									AND torders.PropertyCityName = '$PropertyCityName'
									AND torders.PropertyStateCode = '$PropertyStateCode'
									AND torders.PropertyCountyName = '$PropertyCountyName')
									AND (torders.LoanNumber LIKE '$LoanNumber' AND torders.LoanNumber !='' AND torders.OrderUID <> '$OrderUID')
									AND (torders.SubProductUID = '$SubProductUID')
									GROUP BY torders.OrderUID 
									HAVING whole_name LIKE '$Address' AND (LoanNumber LIKE '$LoanNumber' AND LoanNumber !='') AND (torders.SubProductUID = '$SubProductUID')");
									$res = $query->result_array();*/

									return $is_duplicate;
								}

								function CopytorderDocuments($fieldArray){

									$keys = array();
									$values = array();

									$keys1 = array();
									$values1 = array();

									$src = './'.$fieldArray['DocumentFilepath'];
									$dst = './'.$fieldArray['CopyDocumentPath'];
									$FileName = $fieldArray['DocumentFileName'];
									$ToOrderUID = $fieldArray['ToOrderUID'];

									$arr = $this->GettorderDocument($fieldArray);

									foreach($arr as $key => $value)
									{
										if( $key == "OrderUID")
										{
											$key = $key;    
											$value = $ToOrderUID;    
										}
										else
										{
											$key = $key;
											$value = $value;
										}

										array_push($keys, $key);
										array_push($values, $value);
									}

									$column_name = $keys;
									$column_value = $values;
									$PostArray = array_combine($column_name, $column_value);

									$OrderUID = $fieldArray['Doc_FromOrderUID'];
									$DocumentFileName = $PostArray['DocumentFileName'];

									$result = explode('.',$DocumentFileName);
									$DocumentFileName_1 =  $result[0];

									foreach ($PostArray as $key => $value) {

										$DocumentFileName = $this->CheckDuplicationFileName($OrderUID,$DocumentFileName_1);

										$src_file = './'.$fieldArray['DocumentFilepath'].$FileName;
										$dst_file = './'.$fieldArray['CopyDocumentPath'].$DocumentFileName;

										mkdir(dirname($dst_file), 0777, true);
										copy($src_file, $dst_file);


										if( $key == "DocumentFileName")
										{
											$key = $key;    
											$value = $DocumentFileName; 
										}
										else
										{
											$key = $key;
											$value = $value;
										}

										array_push($keys1, $key);
										array_push($values1, $value);
									}

									$column_name_final = $keys1;
									$column_value_final = $values1;
									$PostArray_final = array_combine($column_name_final, $column_value_final);

									$query = $this->db->insert("torderdocuments", $PostArray_final);

								}

								function CheckDuplicationFileName($OrderUID,$DocumentFileName){

									$query = $this->db->query("SELECT * FROM torderdocuments WHERE DocumentFileName LIKE '$DocumentFileName%' ");
									$reportfilenamecount=$query->num_rows();

									if($reportfilenamecount>0)
									{
										$DocumentFileName = $this->GetAvailFileName($DocumentFileName,'.pdf',$reportfilenamecount);
									}
									else{
										$DocumentFileName = $DocumentFileName.'.pdf';
									}

									return $DocumentFileName;
								}

								function GetAvailFileName($FileName, $ext, $itr)
								{
									$DocumentFileName=$FileName.'_'.$itr.$ext;
									$query=$this->db->get_where('torderdocuments', array('DocumentFileName'=>$DocumentFileName));
									$numrows=$query->num_rows();
									if($numrows==0)
									{    
										return $DocumentFileName;
									}
									else{
										$itr+=1;
										$this->GetAvailFileName($FileName, $ext, $itr);
									}

								}

								function GettorderDocument($fieldArray){

									$this->db->select("*");
									$this->db->from('torderdocuments');
									$this->db->where(array("OrderUID" =>$fieldArray['Doc_FromOrderUID'],"DocumentFileName"=>$fieldArray['DocumentFileName']));
									$query = $this->db->get();
									$res = $query->row();

									return $res;
								}



								function GetCompleteDetails($OrderUID)
								{
									$query = $this->db->query("SELECT * FROM torderassignment 
										LEFT JOIN mworkflowmodules ON mworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID
										LEFT JOIN `msubmenuworkflowmodules` ON msubmenuworkflowmodules.WorkflowModuleUID = torderassignment.WorkflowModuleUID 
										WHERE torderassignment.WorkflowModuleUID 
										IN (2,3) AND torderassignment.OrderUID = '$OrderUID'
										ORDER BY `MenuPosition`;");

									return $query->result();
								}

								function GetAttachmentDetailsForCopy($OrderUID){

									$this->db->select ( '*,torders.OrderDocsPath' );
									$this->db->from ( 'torderdocuments' );
									$this->db->join ( 'mdocumenttypes', 'mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID' , 'left' );
									$this->db->join ( 'msearchmodes', 'msearchmodes.SearchModeUID = torderdocuments.SearchModeUID' , 'left' );
									$this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID' , 'left' );
									$this->db->where(array("torderdocuments.OrderUID"=>$OrderUID));
									$this->db->order_by("torderdocuments.Position asc, torderdocuments.SearchModeUID asc");
									$query = $this->db->get();
									return $query->result();
								}

								function GetAttachmentDetails($OrderUID){

									$this->db->select ( '*,torders.OrderDocsPath' );
									$this->db->from ( 'torderdocuments' );
									$this->db->join ( 'mdocumenttypes', 'mdocumenttypes.DocumentTypeUID = torderdocuments.DocumentTypeUID' , 'left' );
									$this->db->join ( 'msearchmodes', 'msearchmodes.SearchModeUID = torderdocuments.SearchModeUID' , 'left' );
									$this->db->join ( 'torders', 'torders.OrderUID = torderdocuments.OrderUID' , 'left' );
									$this->db->where(array("torderdocuments.OrderUID"=>$OrderUID, "torderdocuments.IsReport"=>0));
									$this->db->order_by("torderdocuments.Position asc, torderdocuments.SearchModeUID asc");
									$query = $this->db->get();
									return $query->result();
								}


								function GetUSPSAddress($OrderUID)
								{
									$query = $this->db->query("SELECT EXISTS(SELECT * FROM torderaddress WHERE OrderUID = '$OrderUID') as CheckUSPSAddress;
										");
									return $query->row();
								}

								function GetStateDetails($StateUID){

									$this->db->select("StateCode");
									$this->db->from('mstates');
									$this->db->where(array("Active"=>1,"mstates.StateUID"=>$StateUID));     
									$query = $this->db->get();
									return $query->row();
								}

								function GetCityDetails($CityUID){

									$this->db->select("CityName");
									$this->db->from('mcities');
									$this->db->where(array("Active"=>1,"mcities.CityUID"=>$CityUID));     
									$query = $this->db->get();
									return $query->row();

								}

								function GetCountyDetails($CountyUID){

									$this->db->select("CountyName");
									$this->db->from('mcounties');
									$this->db->where(array("Active"=>1,"mcounties.CountyUID"=>$CountyUID));     
									$query = $this->db->get();
									return $query->row();

								}

								function GetNotessection($id=NULL)
								{
									if($id==NULL)
									{
										$q = $this->db->get('mreportsections');
										return $q->result();
									} else {
										$this->db->where('SectionUID',$id);
										$q = $this->db->get('mreportsections');
										return $q->row();
									}
								} 

								function GetProductBySubproducts($SubProductUID)
								{
									$this->db->select("*");
									$this->db->from('msubproducts');
									$this->db->where(array("SubProductUID"=>$SubProductUID));
									$query = $this->db->get();
									return $query->row();
								}


								function get_orderdetails($OrderUID)
								{
									if($OrderUID){
										$this->db->select ( '*' ); 
										$this->db->from ( 'torders' );
										$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID' , 'left' );
										$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
										$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );
										$this->db->where ('torders.OrderUID',$OrderUID);
										$query = $this->db->get();
										return $query->row();
									}

								}


	/*function GetOrderDetailsForCopy($OrderDetail){

		$PropertyAddress1 =  $OrderDetail->PropertyAddress1;
		$PropertyAddress2 = $OrderDetail->PropertyAddress2;
		$PropertyCity = $OrderDetail->PropertyCity;
		$PropertyStateUID = $OrderDetail->PropertyStateUID;
		$PropertyZipcode = $OrderDetail->PropertyZipcode;
		$OrderUID = $OrderDetail->OrderUID;


		$this->db->select ( '*' ); 
		$this->db->from ( 'torders' );
		$this->db->join ( 'mstates', 'mstates.StateUID = torders.PropertyStateUID' , 'left' );
		$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID' , 'left' );
		$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
		$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );
		$this->db->join ( 'mcities', 'mcities.CityUID = torders.PropertyCity' , 'left' );
		$this->db->join ( 'mcounties', 'mcounties.CountyUID = torders.PropertyCountyUID' , 'left' );
		$this->db->join ( 'torderassignment', 'torderassignment.OrderUID = torders.OrderUID' , 'right' );
		$this->db->where(array("torders.PropertyAddress1"=>$PropertyAddress1, "torders.PropertyAddress2"=>$PropertyAddress2, "torders.PropertyCity"=>$PropertyCity,"torders.PropertyStateUID"=>$PropertyStateUID,"torders.PropertyZipcode"=>$PropertyZipcode )); 
		$this->db->where("torders.OrderUID <>",$OrderUID);
		$this->db->group_by('torders.OrderUID');
		$query = $this->db->get();
		return $query->result();		
		
	}*/

	function get_propdetails($OrderUID = '')
	{
		if($OrderUID){
			$this->db->select ( '*' ); 
			$this->db->from ( 'torderpropertyroles' );
			$this->db->join ( 'torders', 'torderpropertyroles.OrderUID = torders.OrderUID' , 'left');
			$this->db->join ( 'mpropertyroles', 'mpropertyroles.PropertyRoleUID = torderpropertyroles.PropertyRoleUID' , 'inner' );
			$this->db->where ('torders.OrderUID',$OrderUID);
			$query = $this->db->get();
			return $query->result();
		}
		
	}


	/*function insert_order($data,$LoanAmount,$status){

		$this->AltORderNumber = $data['AltORderNumber'];
		$this->LoanNumber = $data['LoanNumber'];
		$this->LoanAmount = $LoanAmount;
		$this->CustomerRefNum = $data['CustomerRefNum'];
		$this->CustomerUID = $data['customer'];
		$this->SubProductUID = $data['SubProductUID'];
		$this->PriorityUID = $data['PriorityUID'];
		$this->OrderTypeUID = $data['OrderTypeUID'];
		$this->PropertyAddress1 = $data['PropertyAddress1'];
		$this->PropertyAddress2 = $data['PropertyAddress2'];
		$this->PropertyCity = $data['PropertyCity'];
		$this->PropertyStateUID = $data['PropertyStateUID'];
		$this->PropertyCountyUID = $data['PropertyCountyUID'];
		$this->PropertyZipcode = $data['PropertyZipcode'];
		$this->TemplateUID = $data['TemplateUID'];
		$this->EmailReportTo = $data['EmailReportTo'];
		$this->APN = $data['APN'];
		$this->StatusUID = $status;
		$this->OrderEntryDatetime = Date('Y-m-d H:i:s',strtotime("now"));
		
		$this->db->where(array("OrderUID"=>$data['OrderUID']));        
	    $result = $this->db->update('torders', $this);

	    $OrderUID = $data['OrderUID'];

		$property_role = $this->saveProperty_Details($data,$OrderUID);
		return $OrderUID;

	}*/

	function insert_order($data,$LoanAmount,$UspsAddress,$USPS,$status){

		$UserUID = $this->session->userdata('UserUID');
		$RoleUID = $this->session->userdata('RoleUID');
		$UserRole = $this->common_model->GetUserRoleTypeDetails($UserUID);
		$UserRoleType = $UserRole->RoleType;

		$InsuranceType=$data['InsuranceType'];

		// Get ISGN Custoomer
		$Logged_customer = $this->db->query("Select GroupUID from mgroupusers where GroupUserUID = ".$UserUID)->row();

		if($RoleUID < 7){

			$Logged_vendor_id = 1;
		}

		if(empty($Logged_customer)){

			$Logged_vendor_id = 1;

		}else{

			$Logged_group_id = $Logged_customer->GroupUID;
			$Logged_vendor= $this->db->query("Select GroupVendorUID from mgroupvendors where GroupUID = ".$Logged_group_id)->row();

			if(empty($Logged_vendor)){

				$Logged_vendor_id = 1;

			}else{

				if($RoleUID<7){

					$Logged_vendor_id = 1;
				}else{

					$Logged_vendor_id = 0;
				}


			}

		}

		//$this->IsMERS=isset($data['IsMERS'])? 1:0;


		if($Logged_vendor_id > 0) {

			$this->AltORderNumber = $data['AltORderNumber'];
			$this->LoanNumber = $data['LoanNumber'];
			$this->LoanAmount = $LoanAmount;
			$this->CashOutAmount = $data['CashOutAmount'];
			$this->CustomerRefNum = $data['CustomerRefNum'];
			$this->CustomerUID = $data['customer'];
			$this->SubProductUID = $data['SubProductUID'];
			$this->PriorityUID = $data['PriorityUID'];
			$this->ProjectUID = $data['ProjectUID'];
			$this->OrderTypeUID = $data['OrderTypeUID'];
			$this->PropertyAddress1 = $data['PropertyAddress1'];
			$this->PropertyAddress2 = $data['PropertyAddress2'];
			$this->PropertyCityName = $data['PropertyCityName'];
			$this->PropertyStateCode = $data['PropertyStateCode'];
			$this->PropertyCountyName = $data['PropertyCountyName'];
			$this->PropertyZipcode = $data['PropertyZipcode'];
			$this->TemplateUID = $data['TemplateUID'];
			$this->EmailReportTo = $data['EmailReportTo'];
			$this->AttentionName = $data['AttentionName'];
			$this->SpecialInstruction = $data['SpecialInstruction'];
			$this->AddressNotes = $data['AddressNotes'];
			$this->APN = $data['APN'];
			$this->OwnerOccupancy = $data['OwnerOccupancy'];
			$this->StatusUID = $status;		
			$this->InsuranceType = $InsuranceType;		
			$this->PartnerUID = $data['PartnerUID'];		
			$this->BranchUID = $data['CustomerBranch'];		
			$this->LastTouchDateTime = Date('Y-m-d H:i:s',strtotime("now"));

		}

		else{

			$this->LoanNumber = $data['LoanNumber'];
			$this->CashOutAmount = $data['CashOutAmount'];
			$this->SubProductUID = $data['SubProductUID'];
			$this->OrderTypeUID = $data['OrderTypeUID'];
			$this->PropertyAddress1 = $data['PropertyAddress1'];
			$this->PropertyAddress2 = $data['PropertyAddress2'];
			$this->PropertyCityName = $data['PropertyCityName'];
			$this->PropertyStateCode = $data['PropertyStateCode'];
			$this->PropertyCountyName = $data['PropertyCountyName'];
			$this->PropertyZipcode = $data['PropertyZipcode'];
			$this->ProjectUID = $data['ProjectUID'];
			$this->StatusUID = $status;
			$this->InsuranceType = $InsuranceType;	
			$this->BranchUID = $data['CustomerBranch'];
			$this->PartnerUID = $data['PartnerUID'];		
			$this->LastTouchDateTime = Date('Y-m-d H:i:s',strtotime("now"));
			$this->SpecialInstruction = $data['SpecialInstruction'];

		}
		$OrderUID = $data['OrderUID'];
		$X1result =$this->GetX1Order($OrderUID);
		if(!empty($X1result))
		{
			$this->BorrowerType = $data['BorrowerType'];
			$this->PropertyType = $data['PropertyType'];
			$this->TransactionType = $data['TransactionType'];
		}

		$msubproducts = $this->common_model->get_row('msubproducts', ['SubProductUID'=>$this->SubProductUID]);
		$IsClosingProduct = $this->Orderentry_model->IsClosingProduct($msubproducts->ProductUID);


		if (!empty($IsClosingProduct)) {

			if (!empty($data['SigningDate']) && !empty($data['SigningTime'])) {

				$SigningDateTime = DateTime::createFromFormat("m/d/Y" , $data['SigningDate']);
				$SigningDate = $SigningDateTime->format('Y-m-d');
				$seconds =':00';
				$SigningTime = date("H:i", strtotime($data['SigningTime']));
				$SigningDateTime = date('Y-m-d H:i:s', strtotime($SigningDate . ' ' .$SigningTime.$seconds));
			}
			else{
				$SigningDateTime = "";
			}
			$tOrderClosingTemp['OrderUID'] = $data['OrderUID'];
			$tOrderClosingTemp['SigningDateTime'] = $SigningDateTime;

			$this->SigningLocalDate = $SigningDateTime;


			if (!empty($this->common_model->get_row('tOrderClosingTemp', array("OrderUID"=>$data['OrderUID'])))) {
				$this->common_model->save('tOrderClosingTemp', $tOrderClosingTemp, array("OrderUID"=>$data['OrderUID']));					
			}
			else{
				$this->common_model->save('tOrderClosingTemp', $tOrderClosingTemp);					
			}

		}


		         /* $data1['ModuleName']='Ordersummary-update';
                  $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
                  $data1['DateTime']=date('y-m-d H:i:s');
                  $data1['TableName']='torders';
                  $data1['OrderUID']=$data['OrderUID'];
                  $data1['UserUID']=$this->session->userdata('UserUID');  */              

                  // $this->db->select('*');
                  // $this->db->from('torders');
                  // $this->db->where('OrderUID',$data['OrderUID']);
                  // $oldvalue=$this->db->get('')->row_array();

				  /*$data1['ModuleName']='Ordersummary-update';
                  $data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
                  $data1['DateTime']=date('y-m-d H:i:s');
                  $data1['TableName']='torders';
                  $data1['OrderUID']=$data['OrderUID'];
                  $data1['UserUID']=$this->session->userdata('UserUID');    */

                 /* $this->db->select('*');
                  $this->db->from('torders');
                  $this->db->where('OrderUID',$data['OrderUID']);                 
                  $oldvalue=$this->db->get('')->row_array();  */
                  $this->BorrowerType = $data['BorrowerType'];
                  $this->TransactionType=$data['TransactionType'];
                  $this->PropertyType=$data['PropertyType'];

                  // audit start
                  $str = '';
                  $VAlidationTriggerFields = [];
                  foreach ($this as $keyAA => $valueAA) {
                  	/* CHECK WITH PREVIOUS VALUE FOR CHANGE*/
                  	$Changed = $this->common_model->CheckAudit(
                  		'OrderUID',
                  		$data['OrderUID'],
                  		'torders',
                  		$keyAA,
                  		$valueAA
                  	);
          // LastTouchDateTime 
                  	if($keyAA != 'LastTouchDateTime')
                  	{
                  		$str = '<b>'.$keyAA.'</b> Changed from <b>'.$Changed.'</b> to <b>'.$valueAA.'</b>';
                  	}




                  	if($Changed != 'FALSE' && $Changed != ""){
                  		$this->common_model->updateOrderDocTypesFieldchange($keyAA,$OrderUID);
                  		$VAlidationTriggerFields[$keyAA] = $valueAA;
			// Validation Trigger for Document Creation
			// if($keyAA == 'TransactionType' || $keyAA == 'PropertyType'){
			// 	$this->load->model('attachments_new/Attachments_new_model');
			// 	$this->Attachments_new_model->GetOrderValidationTriggers($OrderUID,$keyAA,$valueAA);
			// }
                  		$InsetData = array(
                  			'UserUID' => $this->loggedid,
                  			'ModuleName' => 'Order Summary',
                  			'OrderUID' => $data['OrderUID'],
                  			'OldValue' => $Changed,
                  			'NewValue' => $valueAA,
                  			'Content' => htmlentities($str),
                  			'IpAddreess' => $_SERVER['REMOTE_ADDR'],
                  			'DateTime' => date('Y-m-d H:i:s'));
                  		$this->common_model->InsertAuditTrail($InsetData);
                  	}
                  }
        //audit end
   //      	$this->load->model('attachments_new/Attachments_new_model');
			// $this->Attachments_new_model->GetOrderValidationTriggers($OrderUID,$VAlidationTriggerFields);

                  $this->IsTaxcert=isset($data['IsTaxcert'])? 1:0;

                  $this->db->where(array("OrderUID"=>$data['OrderUID']));        
                  $result = $this->db->update('torders', $this);

	              /*$this->db->select('*');
                  $this->db->from('torders');
                  $this->db->where('OrderUID',$data['OrderUID']);   
                  $newvalue = $this->db->get('')->row_array();
                  $this->common_model->Audittrail_diff($newvalue,$oldvalue,$data1);*/
	              // $this->db->select('*');
               //    $this->db->from('torders');
               //    $this->db->where('OrderUID',$data['OrderUID']);
               //    $newvalue=$this->db->get('')->row_array();
               //    $this->common_model->Audittrail_diff($newvalue,$oldvalue,$data1);

                  $property_role = $this->saveProperty_Details($data,$OrderUID,$UspsAddress,$USPS);

		// D-2-T3 insert  if  not exists
                  /*Insert Order Import*/
                  $this->Insert_tOrderImport($data,$OrderUID);

                  /* @purpose: To Update Additional Information  @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: May 27th 2020 */
                  $this->InsertAdditionalInformation($data,$OrderUID);

                  return $OrderUID;

              }

              function saveProperty_Details($data,$OrderUID ='',$UspsAddress,$USPS)
              {

              	$UserUID = $this->session->userdata('UserUID');
              	$RoleUID = $this->session->userdata('RoleUID');
              	$UserRole = $this->common_model->GetUserRoleTypeDetails($UserUID);
              	$UserRoleType = $UserRole->RoleType;
              	$insertdet = $updatedet = [];

		// Get ISGN Custoomer
              	$Logged_customer = $this->db->query("Select GroupUID from mgroupusers where GroupUserUID = ".$UserUID)->row();

              	if($RoleUID < 7){

              		$Logged_vendor_id = 1;
              	}

              	if(empty($Logged_customer)){

              		$Logged_vendor_id = 1;

              	}else{

              		$Logged_group_id = $Logged_customer->GroupUID;
              		$Logged_vendor= $this->db->query("Select GroupVendorUID from mgroupvendors where GroupUID = ".$Logged_group_id)->row();

              		if(empty($Logged_vendor)){

              			$Logged_vendor_id = 1;

              		}else{

              			if($RoleUID<7){

              				$Logged_vendor_id = 1;
              			}else{

              				$Logged_vendor_id = 0;
              			}


              		}

              	}


              	$Address = $UspsAddress;
              	$array = (array)$Address;

              	$main_array = $array['Address'];
              	$sub_array = (array)$main_array;


		// $this->db->where(array("OrderUID"=>$OrderUID ));
	 //    $res = $this->db->delete('torderpropertyroles');

              	if(empty($data['PropertyRoleUID'][0])){
              		return true;
              	}
              	else{

              		if($Logged_vendor_id > 0) {

              			$entry_array = array();
              			$count = count($data['PropertyRoleUID']);
              			$PropertyRoleUID = $data['PropertyRoleUID']; 
              			$PRTitle = $data['PRTitle']; 
              			$PRName = $data['PRName'];
              			$PREmailID = $data['PREmailID']; 
              			$PRHomeNumber = $data['PRHomeNumber']; 
              			$PRWorkNumber = $data['PRWorkNumber']; 
              			$PRCellNumber = $data['PRCellNumber'];

              			$CheckPRBirthDate = isset($data['CheckPRBirthDate']) ? $data['CheckPRBirthDate'] : ''; 				
              			$PRMaritalStatus = isset($data['PRMaritalStatus']) ? $data['PRMaritalStatus'] : NULL; 
              			$CheckPRFirstHomeBuyerIndicator = isset($data['CheckPRFirstHomeBuyerIndicator']) ? 1 : 0; 

              			$PRSocialNumber = $data['PRSocialNumber'];
              			$MailingAddress1 = isset($data['MailingAddress1']) ? $data['MailingAddress1'] : NULL; 
              			$MailingAddress2 = isset($data['MailingAddress2']) ? $data['MailingAddress2'] : NULL; 
              			$MailingZipCode = isset($data['MailingZipCode']) ? $data['MailingZipCode'] : NULL; 
              			$MailingCityName = isset($data['MailingCityName']) ? $data['MailingCityName'] : NULL; 
              			$MailingStateCode = isset($data['MailingStateCode']) ? $data['MailingStateCode'] : NULL;
              			$MailingCountyName = isset($data['MailingCountyName']) ? $data['MailingCountyName'] : NULL; 
              			$MailingAddressNotes = isset($data['MailingAddressNotes']) ? $data['MailingAddressNotes'] : NULL;

              			$SigningAddress1 = isset($data['SigningAddress1']) ? $data['SigningAddress1'] : NULL; 
              			$SigningAddress2 = isset($data['SigningAddress2']) ? $data['SigningAddress2'] : NULL; 
              			$SigningZipCode = isset($data['SigningZipCode']) ? $data['SigningZipCode'] : NULL; 
              			$SigningCityName = isset($data['SigningCityName']) ? $data['SigningCityName'] : NULL; 
              			$SigningStateCode = isset($data['SigningStateCode']) ? $data['SigningStateCode'] : NULL;
              			$SigningCountyName = isset($data['SigningCountyName']) ? $data['SigningCountyName'] : NULL; 
              			$SigningAddressNotes = isset($data['SigningAddressNotes']) ? $data['SigningAddressNotes'] : NULL;
              			$SigningSpecialInstruction = isset($data['SigningSpecialInstruction']) ? $data['SigningSpecialInstruction'] : NULL;
              			$chk_mailing = isset($data['chk_mailing']) ? $data['chk_mailing'] : NULL; 
              			$chk_Signing = isset($data['chk_Signing']) ? $data['chk_Signing'] : NULL; 


              			for($i=0; $i<$count; $i++)  
              			{
              				$entry_array = array();
              				$secret = $this->config->item('encryption_key');
              				$encrypt = new AesCtr();
					//@author Naveenkumar @purpose Encrypt social number if user social number true. otherwise will updated null.
              				$EncrptSocialNumber = $PRSocialNumber[$i] ? $encrypt->encrypt($PRSocialNumber[$i], $secret, 256) : NULL;

              				$PRBirthDate = ($data['CheckPRBirthDate'][$i] == NULL) ? $data['CheckPRBirthDate'][$i] : Date('Y-m-d',strtotime($data['CheckPRBirthDate'][$i]));

              				$entry_array = array(
              					"OrderUID"=>$OrderUID,
              					'PropertyRoleUID' => $PropertyRoleUID[$i],
              					'PRName' => $PRName[$i],
              					'PRTitle' => $PRTitle[$i],
              					'PREmailID' => str_replace(['[', ']', '"', '\''], "",  $PREmailID[$i]),
              					'PRHomeNumber' => $PRHomeNumber[$i],
              					'PRWorkNumber' => $PRWorkNumber[$i],
              					'PRCellNumber' => $PRCellNumber[$i],

              					'PRBirthDate' => $PRBirthDate,
              					'PRMaritalStatus' => $PRMaritalStatus[$i],
              					'PRFirstHomeBuyerIndicator' => isset($data['CheckPRFirstHomeBuyerIndicator'][$i]) ? 1 : 0,

              					'PRSocialNumber' => $EncrptSocialNumber,
              					'MailingAddressNotes' => $MailingAddressNotes[$i],
              					'IsMailingAddress' => $chk_mailing[$i+1],
              					'IsSigningAddress' => $chk_Signing[$i+1],
              				);
					// echo '<pre>';print_r($entry_array);exit;
              				if ($chk_mailing[$i+1] == 'property') {
              					$PropertyAddress = $this->db->select('PropertyAddress1,PropertyAddress2,PropertyStateCode,PropertyCountyName,PropertyZipcode,PropertyCityName')->from('torders')->where('OrderUID',$OrderUID)->get()->row();

              					$entry_array['MailingAddress1'] = NULL;
              					$entry_array['MailingAddress2'] = NULL;
              					$entry_array['MailingZipCode'] = NULL;
              					$entry_array['MailingCityName'] = NULL;
              					$entry_array['MailingStateCode'] = NULL;
              					$entry_array['MailingCountyName'] = NULL;
              					$entry_array['MailingAddressNotes'] = NULL;
              				}
              				else{

              					$entry_array['MailingAddress1'] = $MailingAddress1[$i];
              					$entry_array['MailingAddress2'] = $MailingAddress2[$i];
              					$entry_array['MailingZipCode'] = $MailingZipCode[$i];
              					$entry_array['MailingCityName'] = $MailingCityName[$i];
              					$entry_array['MailingStateCode'] = $MailingStateCode[$i];
              					$entry_array['MailingCountyName'] = $MailingCountyName[$i];
              					$entry_array['MailingAddressNotes'] = $MailingAddressNotes[$i];
              				} 

              				/*--- Siging Address Updation ---*/
              				if (in_array($chk_Signing[$i+1], ['property', 'mailing'])) {

              					$entry_array['SigningAddress1'] = NULL;
              					$entry_array['SigningAddress2'] = NULL;
              					$entry_array['SigningZipCode'] = NULL;
              					$entry_array['SigningCityName'] = NULL;
              					$entry_array['SigningStateCode'] = NULL;
              					$entry_array['SigningCountyName'] = NULL;
              					$entry_array['SigningAddressNotes'] = NULL;
              					$entry_array['SigningSpecialInstruction'] = NULL;

              				}
              				else{

              					$entry_array['SigningAddress1'] = $SigningAddress1[$i];
              					$entry_array['SigningAddress2'] = $SigningAddress2[$i];
              					$entry_array['SigningZipCode'] = $SigningZipCode[$i];
              					$entry_array['SigningCityName'] = $SigningCityName[$i];
              					$entry_array['SigningStateCode'] = $SigningStateCode[$i];
              					$entry_array['SigningCountyName'] = $SigningCountyName[$i];
              					$entry_array['SigningAddressNotes'] = $SigningAddressNotes[$i];
              					$entry_array['SigningSpecialInstruction'] = $SigningSpecialInstruction[$i];
              				} 

					/*$data1['ModuleName']=$PRName[$i].' '.'PropertyRoles-update';
					$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
					$data1['DateTime']=date('y-m-d H:i:s');
					$data1['TableName']='torderpropertyroles';  
					$data1['OrderUID'] = $OrderUID;             
					$data1['UserUID']=$this->session->userdata('UserUID');                
					$this->common_model->Audittrail_insert($data1);*/

// audit start
					$str = '';
					foreach ($entry_array as $keyAA => $valueAA) {
						/* CHECK WITH PREVIOUS VALUE FOR CHANGE*/

						$Changed = $this->common_model->CheckAudit(
							'Id',
							$data['OrderPropertyRoleUID'][$i],
							'torderpropertyroles',
							$keyAA,
							$valueAA
						);
          // // PRSocialNumber, LastTouchDateTime
          // if($keyAA != 'PRSocialNumber')
          // {
          // 	if(!empty($valueAA)){
						$str = '<b>'.$PRName[$i].': '.$keyAA.'</b> Changed from <b>'.$Changed.'</b> to <b>'.$valueAA.'</b>';
          // }
          // }

						if($Changed != 'FALSE' && $Changed != ""){
							$InsetData = array(
								'UserUID' => $this->loggedid,
								'ModuleName' => 'Property Roles',
								'OrderUID' => $data['OrderUID'],
								'OldValue' => $Changed,
								'NewValue' => $valueAA,
								'Content' => htmlentities($str),
								'IpAddreess' => $_SERVER['REMOTE_ADDR'],
								'DateTime' => date('Y-m-d H:i:s'));

							$this->common_model->InsertAuditTrail($InsetData);
						}
					}
        //audit end

					if (!empty($data['OrderPropertyRoleUID'][$i])) {
						$entry_array['Id'] = $data['OrderPropertyRoleUID'][$i];
						array_push($updatedet, $entry_array);
					}
					else{
						array_push($insertdet, $entry_array);

					}
				}


			}

			else{

				$entry_array = array();
				$count = count($data['PropertyRoleUID']);
				$PropertyRoleUID = $data['PropertyRoleUID']; 
				$PRName = $data['PRName']; 
				$PREmailID = $data['PREmailID']; 
				$PRHomeNumber = $data['PRHomeNumber']; 
				$PRWorkNumber = $data['PRWorkNumber']; 
				$PRCellNumber = $data['PRCellNumber'];

				$CheckPRBirthDate = isset($data['CheckPRBirthDate']) ? $data['CheckPRBirthDate'] : ''; 
				$PRMaritalStatus = isset($data['PRMaritalStatus']) ? $data['PRMaritalStatus'] : NULL; 
				$CheckPRFirstHomeBuyerIndicator = isset($data['CheckPRFirstHomeBuyerIndicator']) ? 1 : 0;

				$MailingAddress1 = isset($data['MailingAddress1']) ? $data['MailingAddress1'] : NULL ; 
				$MailingAddress2 = isset($data['MailingAddress2']) ? $data['MailingAddress2'] : NULL ; 
				$MailingZipCode = isset($data['MailingZipCode']) ? $data['MailingZipCode'] : NULL ; 
				$MailingCityName = isset($data['MailingCityName']) ? $data['MailingCityName'] : NULL ; 
				$MailingStateCode = isset($data['MailingStateCode']) ? $data['MailingStateCode'] : NULL ;
				$MailingCountyName = isset($data['MailingCountyName']) ? $data['MailingCountyName'] : NULL ; 
				$MailingAddressNotes = isset($data['MailingAddressNotes']) ? $data['MailingAddressNotes'] : NULL ;

				$SigningAddress1 = isset($data['SigningAddress1']) ? $data['SigningAddress1'] : NULL; 
				$SigningAddress2 = isset($data['SigningAddress2']) ? $data['SigningAddress2'] : NULL; 
				$SigningZipCode = isset($data['SigningZipCode']) ? $data['SigningZipCode'] : NULL; 
				$SigningCityName = isset($data['SigningCityName']) ? $data['SigningCityName'] : NULL; 
				$SigningStateCode = isset($data['SigningStateCode']) ? $data['SigningStateCode'] : NULL;
				$SigningCountyName = isset($data['SigningCountyName']) ? $data['SigningCountyName'] : NULL; 
				$SigningAddressNotes = isset($data['SigningAddressNotes']) ? $data['SigningAddressNotes'] : NULL;
				$SigningSpecialInstruction = isset($data['SigningSpecialInstruction']) ? $data['SigningSpecialInstruction'] : NULL;
				$chk_mailing = isset($data['chk_mailing']) ? $data['chk_mailing'] : NULL; 
				$chk_Signing = isset($data['chk_Signing']) ? $data['chk_Signing'] : NULL; 


				$chk_mailing = $data['chk_mailing']; 

				for($i=0; $i<$count; $i++)  
				{
					$entry_array = array();
					$secret = $this->config->item('encryption_key');
					$encrypt = new AesCtr();
					$EncrptSocialNumber = $encrypt->encrypt($PRSocialNumber[$i], $secret, 256);

					$PRBirthDate = ($data['CheckPRBirthDate'][$i] == NULL) ? $data['CheckPRBirthDate'][$i] : Date('Y-m-d',strtotime($data['CheckPRBirthDate'][$i]));

					$entry_array = array(
						"OrderUID"=>$OrderUID,
						'PropertyRoleUID' => $PropertyRoleUID[$i],
						'PRName' => $PRName[$i],
						'PRTitle' => $PRTitle[$i],
						'PREmailID' => str_replace(['[', ']', '"', '\''], "",  $PREmailID[$i]),
						'PRHomeNumber' => $PRHomeNumber[$i],
						'PRWorkNumber' => $PRWorkNumber[$i],
						'PRCellNumber' => $PRCellNumber[$i],

						'PRBirthDate' => $PRBirthDate,
						'PRMaritalStatus' => $PRMaritalStatus[$i],
						'PRFirstHomeBuyerIndicator' => isset($data['CheckPRFirstHomeBuyerIndicator'][$i]) ? 1 : 0,

						'PRSocialNumber' => $EncrptSocialNumber,
						'MailingAddressNotes' => $MailingAddressNotes[$i],
						'IsMailingAddress' => $chk_mailing[$i+1],
						'IsSigningAddress' => $chk_Signing[$i+1],
					);

					if ($chk_mailing[$i+1] == 'property') {
						$PropertyAddress = $this->db->select('PropertyAddress1,PropertyAddress2,PropertyStateCode,PropertyCountyName,PropertyZipcode,PropertyCityName')->from('torders')->where('OrderUID',$OrderUID)->get()->row();

						$entry_array['MailingAddress1'] = NULL;
						$entry_array['MailingAddress2'] = NULL;
						$entry_array['MailingZipCode'] = NULL;
						$entry_array['MailingCityName'] = NULL;
						$entry_array['MailingStateCode'] = NULL;
						$entry_array['MailingCountyName'] = NULL;
						$entry_array['MailingAddressNotes'] = NULL;

					}
					else{
						$entry_array['MailingAddress1'] = $MailingAddress1[$i];
						$entry_array['MailingAddress2'] = $MailingAddress2[$i];
						$entry_array['MailingZipCode'] = $MailingZipCode[$i];
						$entry_array['MailingCityName'] = $MailingCityName[$i];
						$entry_array['MailingStateCode'] = $MailingStateCode[$i];
						$entry_array['MailingCountyName'] = $MailingCountyName[$i];
					} 

					/*--- Siging Address Updation ---*/
					if (in_array($chk_Signing[$i+1], ['property', 'mailing'])) {

						$entry_array['SigningAddress1'] = NULL;
						$entry_array['SigningAddress2'] = NULL;
						$entry_array['SigningZipCode'] = NULL;
						$entry_array['SigningCityName'] = NULL;
						$entry_array['SigningStateCode'] = NULL;
						$entry_array['SigningCountyName'] = NULL;
						$entry_array['SigningAddressNotes'] = $SigningAddressNotes[$i];
						$entry_array['SigningSpecialInstruction'] = $SigningSpecialInstruction[$i];

					}
					else{

						$entry_array['SigningAddress1'] = $SigningAddress1[$i];
						$entry_array['SigningAddress2'] = $SigningAddress2[$i];
						$entry_array['SigningZipCode'] = $SigningZipCode[$i];
						$entry_array['SigningCityName'] = $SigningCityName[$i];
						$entry_array['SigningStateCode'] = $SigningStateCode[$i];
						$entry_array['SigningCountyName'] = $SigningCountyName[$i];
						$entry_array['SigningAddressNotes'] = $SigningAddressNotes[$i];
						$entry_array['SigningSpecialInstruction'] = $SigningSpecialInstruction[$i];
					} 



					/*$data1['ModuleName']=$PRName[$i].' '.'PropertyRoles-insert';
					$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
					$data1['DateTime']=date('y-m-d H:i:s');
					$data1['TableName']='torderpropertyroles';  
					$data1['OrderUID'] = $OrderUID;             
					$data1['UserUID']=$this->session->userdata('UserUID');                
					$this->common_model->Audittrail_insert($data1);*/
// Audit log
       // $assess_id = $this->db->insert_id();
					$STRR = 'Property Roles: <b>'.$PRName[$i].'</b> is Added';
					$InsetData = array(
						'UserUID' => $this->loggedid,
						'ModuleName' => 'Property Roles',
						'OrderUID' => $PostArray['OrderUID'],
						'Feature' => $assess_id,
						'TableName' => 'torderpropertyroles',
						'Content' => htmlentities($STRR),
						'IpAddreess' => $_SERVER['REMOTE_ADDR'],
						'DateTime' => date('Y-m-d H:i:s'));
					$this->common_model->InsertAuditTrail($InsetData);

					if (!empty($data['OrderPropertyRoleUID'][$i])) {
						$entry_array['Id'] = $data['OrderPropertyRoleUID'][$i];
						array_push($updatedet, $entry_array);
					}
					else{
						array_push($insertdet, $entry_array);

					}
				}

			}
					// echo '<pre>';print_r($insertdet);exit;
			if (!empty($updatedet)) {
				$this->db->update_batch('torderpropertyroles', $updatedet, 'Id');
			}
			if (!empty($insertdet)) {
				$this->db->insert_batch('torderpropertyroles', $insertdet);
			}
		}

		if($USPS == NULL){
			$CheckValue = $this->GetUSPSAddress($data['OrderUID']);

			$CheckUSPSAddress = $CheckValue->CheckUSPSAddress;

			if($CheckUSPSAddress == 1){

					/*$this->db->where(array("OrderUID"=>$OrderUID ));
			    	$res = $this->db->delete('torderaddress');
			    	return true;*/
			    	$fieldArray = array(
			    		"USPSAddress1"=>'',
			    		"USPSAddress2"=>'',
			    		"USPSZipcode"=>'',
			    		"USPSState"=>'',
			    		"USPSCity"=>'',
			    		"USPSCounty"=>'',
			    	); 
			    	$this->db->where(array("OrderUID"=>$data['OrderUID']));   
			    	$res = $this->db->update('torderaddress', $fieldArray);
			    	return true;

			    }
			    else{
			    	return true;
			    }
			}
			else{

				$CheckValue = $this->GetUSPSAddress($data['OrderUID']);

				$CheckUSPSAddress = $CheckValue->CheckUSPSAddress;

			/*echo "<pre>";
			print_r($CheckValue);  */

			if($CheckUSPSAddress == 1){

				$fieldArray = array(
					"OrderUID"=>$data['OrderUID'],
					"USPSAddress1"=>$sub_array['Address2'],
					"USPSAddress2"=>'',
					"USPSZipcode"=>$USPS->PropertyZipcode,
					"USPSStateCode"=>$USPS->PropertyStateUID,
					"USPSCityName"=>$USPS->PropertyCity,
					"USPSCountyName"=>$USPS->PropertyCountyUID
				);
				$this->db->where(array("OrderUID"=>$data['OrderUID']));   
				$res = $this->db->update('torderaddress', $fieldArray);
			}
			else{

				$fieldArray = array(
					"OrderUID"=>$data['OrderUID'],
					"USPSAddress1"=>$sub_array['Address2'],
					"USPSAddress2"=>'',
					"USPSZipcode"=>$USPS->PropertyZipcode,
					"USPSStateCode"=>$USPS->PropertyStateUID,
					"USPSCityName"=>$USPS->PropertyCity,
					"USPSCountyName"=>$USPS->PropertyCountyUID
				);

				$query = $this->db->insert('torderaddress', $fieldArray);
			}
		}
		
	}



	/*function saveProperty_Details($data,$OrderUID ='')
	{*/

		/*$Address = $UspsAddress;
		$array = (array)$Address;

		$main_array = $array['Address'];
		$sub_array = (array)$main_array;*/


		/*$this->db->where(array("OrderUID"=>$OrderUID ));
	    $res = $this->db->delete('torderpropertyroles');


		if(empty($data['PropertyRoleUID'][0])){
			return true;
		}else{
			$entry_array = array();
			$count = count($data['PropertyRoleUID']);
			$PropertyRoleUID = $data['PropertyRoleUID']; 
			$PRName = $data['PRName']; 
			$PREmailID = $data['PREmailID']; 
			$PRHomeNumber = $data['PRHomeNumber']; 
			$PRWorkNumber = $data['PRWorkNumber']; 
			$PRCellNumber = $data['PRCellNumber']; 
			$PRSocialNumber = $data['PRSocialNumber']; 
			for($i=0; $i<$count; $i++)  
			{
				$entry_array[] = array(
					"OrderUID"=>$OrderUID,
					'PropertyRoleUID' => $PropertyRoleUID[$i],
					'PRName' => $PRName[$i],
					'PREmailID' => $PREmailID[$i],
					'PRHomeNumber' => $PRHomeNumber[$i],
					'PRWorkNumber' => $PRWorkNumber[$i],
					'PRCellNumber' => $PRCellNumber[$i],
					'PRSocialNumber' => $PRSocialNumber[$i],
					);
			}
			$this->db->insert_batch('torderpropertyroles', $entry_array);
		}*/

		/*if($USPS == NULL){

			$this->db->where(array("OrderUID"=>$OrderUID ));
	    	$res = $this->db->delete('torderaddress');
			return true;
		}
		else{

			$CheckValue = $this->GetUSPSAddress($data['OrderUID']);

					$CheckUSPSAddress = $CheckValue->CheckUSPSAddress;

					if($CheckUSPSAddress == 1){

	                    $fieldArray = array(
							"OrderUID"=>$data['OrderUID'],
							"USPSAddress1"=>$sub_array['Address2'],
							"USPSAddress2"=>'',
							"USPSZipcode"=>$USPS->PropertyZipcode,
							"USPSState"=>$USPS->PropertyStateUID,
							"USPSCity"=>$USPS->PropertyCity,
							"USPSCounty"=>$USPS->PropertyCountyUID
						);
	                    $this->db->where(array("OrderUID"=>$data['OrderUID']));   
	                    $res = $this->db->update('torderaddress', $fieldArray);
	                }
	                else{

	                    $fieldArray = array(
							"OrderUID"=>$data['OrderUID'],
							"USPSAddress1"=>$sub_array['Address2'],
							"USPSAddress2"=>'',
							"USPSZipcode"=>$USPS->PropertyZipcode,
							"USPSState"=>$USPS->PropertyStateUID,
							"USPSCity"=>$USPS->PropertyCity,
							"USPSCounty"=>$USPS->PropertyCountyUID
						);

		       			$query = $this->db->insert('torderaddress', $fieldArray);
	                }
	            }*/

	            /*}*/


	            function saveRoleswithoutuspsDetails($PostArray,$LoanAmount)
	            {

	            	$fieldArray = array(
	            		"AltORderNumber"=>$PostArray['AltORderNumber'],
	            		"LoanNumber"=>$PostArray['LoanNumber'],
	            		"LoanAmount"=>$LoanAmount,
	            		"CashOutAmount"=>$PostArray['CashOutAmount'],
	            		"CustomerRefNum"=>$PostArray['CustomerRefNum'],
	            		"PriorityUID"=>$PostArray['PriorityUID'],
	            		"ProjectUID"=>$PostArray['ProjectUID'],
	            		"OrderTypeUID"=>$PostArray['OrderTypeUID'],
	            		"PropertyAddress1"=>$PostArray['PropertyAddress1'],
	            		"PropertyAddress2"=>$PostArray['PropertyAddress2'],
	            		"PropertyCity"=>$PostArray['PropertyCity'],
	            		"PropertyStateUID"=>$PostArray['PropertyStateUID'],
	            		"PropertyCountyUID"=>$PostArray['PropertyCountyUID'],
	            		"PropertyZipcode"=>$PostArray['PropertyZipcode'],
	            		"TemplateUID"=>$PostArray['TemplateUID'],
	            		"EmailReportTo"=>$PostArray['EmailReportTo'],
	            		"APN"=>$PostArray['APN']
	            	);

	            	$this->db->trans_begin();

	            	$this->db->where(array("OrderUID"=>$PostArray['OrderUID']));        
	            	$result = $this->db->update('torders', $fieldArray);

	            	if($result)
	            	{

	            		$this->db->where(array("OrderUID"=>$PostArray['OrderUID'] ));
	            		$res = $this->db->delete('torderpropertyroles');

	            		for($i=0;$i<count($this->input->post('PropertyRoleUID'));$i++)
	            		{

	            			$fieldArray = array(
	            				"OrderUID"=>$PostArray ['OrderUID'],
	            				"PropertyRoleUID"=>$PostArray ['PropertyRoleUID'][$i],
	            				"PRName"=>$PostArray ['PRName'][$i],
	            				"PREmailID"=>$PostArray ['PREmailID'][$i],
	            				"PRHomeNumber"=>$PostArray ['PRHomeNumber'][$i],
	            				"PRWorkNumber"=>$PostArray ['PRWorkNumber'][$i],
	            				"PRCellNumber"=>$PostArray ['PRCellNumber'][$i],
	            				"PRSocialNumber"=>$PostArray ['PRSocialNumber'][$i],
	            			);
	            			$res = $this->db->insert('torderpropertyroles', $fieldArray);
	            		}


	            		$this->db->where(array("OrderUID"=>$PostArray['OrderUID'] ));
	            		$res = $this->db->delete('torderaddress');


	            		if ($this->db->trans_status() === FALSE)
	            		{
	            			$this->db->trans_rollback();
	            			$data=array("msg"=>"error","type"=>"color danger");
	            		}
	            		else
	            		{
	            			$this->db->trans_commit();
	            			$data=array("msg"=>"Updated Successfully","type"=>"color success");
	            		}

	            		echo json_encode($data);
	            	}

	            }


  //       function getzipcontents($zipcode = '')
		// {
		//  $query = $this->db->query("SELECT * FROM `mcities` 
  //                 LEFT JOIN mstates ON mcities.StateUID = mstates.StateUID 
  //                 LEFT JOIN mcounties ON mcities.StateUID = mcounties.StateUID 
  //                 AND mcities.CountyUID = mcounties.CountyUID
  //                 WHERE mcities.ZipCode = '$zipcode'");

  //     return $query->row();
		// }

	            function getzipcontents($zipcode = '')
	            {
	            	$zipcode=str_replace('-', '', $zipcode);
	            	$query = $this->db->query("SELECT * FROM `mcities`
	            		LEFT JOIN mstates ON mcities.StateUID = mstates.StateUID
	            		LEFT JOIN mcounties ON mcities.StateUID = mcounties.StateUID
	            		AND mcities.CountyUID = mcounties.CountyUID
	            		WHERE mcities.ZipCode = '$zipcode'");
	            	$result=$query->row();	     

	            	if(empty($result)){
	            		$zipcode_new=substr("$zipcode", 0, 4);			           
	            		$query = $this->db->query("SELECT * FROM `mcities`
	            			LEFT JOIN mstates ON mcities.StateUID = mstates.StateUID
	            			LEFT JOIN mcounties ON mcities.StateUID = mcounties.StateUID
	            			AND mcities.CountyUID = mcounties.CountyUID
	            			WHERE mcities.ZipCode  LIKE '$zipcode_new%'");
	            		return $query->row();
	            	}else{

	            		return $result;
	            	}
	            }

	            function update_document_type($TemplateUID,$OrderUID)
	            {
	            	$this->db->select('*');
	            	$this->db->from('torders');
	            	$this->db->where('OrderUID',$OrderUID);   
	            	$oldvalue = $this->db->get('')->row_array();
	            	$query = $this->db->query("UPDATE torders SET TemplateUID = '".$TemplateUID."' WHERE OrderUID = '".$OrderUID."' ");
	            	if($this->db->affected_rows()>0)
	            	{
	            		$data1['ModuleName']='Template-update';
	            		$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
	            		$data1['DateTime']=date('y-m-d H:i:s');
	            		$data1['TableName']='torders';  
	            		$data1['OrderUID'] = $OrderUID;             
	            		$data1['UserUID']=$this->session->userdata('UserUID');

	            		$this->db->select('*');
	            		$this->db->from('torders');
	            		$this->db->where('OrderUID',$OrderUID);   
	            		$newvalue = $this->db->get('')->row_array();                

	            		$this->common_model->Audittrail_diff($newvalue,$oldvalue,$data1);
	            		return true;
	            	}
	            	else
	            	{
	            		return false;
	            	}
	            }

	            function insert_start_delay_time($OrderUID,$StartTime,$StartRemarks,$StartNoteType,$ApiRequestUID)
	            {
	            	$cus['OrderUID'] = $OrderUID;
	            	$cus['CustomerDelayStartTime'] = $StartTime;
	            	$cus['CustomerDelayStartRemarks'] = $StartRemarks;
	            	$cus['CustomerDelayStartNoteType'] = $StartNoteType;
	            	$cus['StartedByUserUID'] = $this->session->userdata('UserUID');
	            	$query = $this->db->insert('mcustomerdelay',$cus);

	            	$order_details = $this->common_model->get_orderdetails($OrderUID);
	            	$OrderSourceUID = $order_details->OrderSourceUID;

	            	if($this->db->affected_rows()>0)
	            	{
	            		$note['OrderUID'] = $cus['OrderUID'];
	            		$note['SectionUID'] = $cus['CustomerDelayStartNoteType'];
	            		$note['RoleType'] = '1,6,7';
	            		$note['SystemNotesUID'] = '5';
	            		$note['Note'] = $cus['CustomerDelayStartRemarks'].' - Customer Delay Start';
	            		$note['CreatedByUserUID'] = $this->session->userdata('UserUID');
	            		$note['CreatedOn'] = date('Y-m-d H:i:s',strtotime('now'));
	            		$res = $this->db->insert('tordernotes',$note);

	            		$ApiRequestCode ='';

	            		if($ApiRequestUID != 0){

	            			$ApiStatusCodeDetails = $this->GetApiStatusCodeDetails($ApiRequestUID); 
	            			$ApiRequestCode = $ApiStatusCodeDetails->RequestCode;
	            			$RequestDesc = $ApiStatusCodeDetails->RequestDesc;

	            			$api_request['OrderUID'] = $cus['OrderUID'];
	            			$api_request['ApiRequestUID'] = $ApiRequestUID;
	            			$api_request['ApiRequestComment'] = $cus['CustomerDelayStartRemarks'];
	            			$api_request['CreatedBy'] = $this->session->userdata('UserUID');
	            			$api_request['CreatedDate'] = date('Y-m-d H:i:s',strtotime('now'));
	            			$result = $this->db->insert('tApiRequestStatus',$api_request);

	            			$note_api['OrderUID'] = $cus['OrderUID'];
	            			$note_api['SectionUID'] = $cus['CustomerDelayStartNoteType'];
	            			$note_api['Note'] = 'Additional Request to Stewart <br> Code: '.$ApiRequestCode.'<br> Text: '.$RequestDesc.'<br> Comments: '.$cus['CustomerDelayStartRemarks'];
	            			$note_api['CreatedByUserUID'] = $this->session->userdata('UserUID');
	            			$note_api['CreatedOn'] = date('Y-m-d H:i:s',strtotime('now'));
	            			$res = $this->db->insert('tordernotes',$note_api);
	            		}				

	            		if($res){
	            			$this->real_ec_model->OnHoldApiOrder($OrderUID,$StartRemarks,$ApiRequestCode);
	            		}

				// Change Order Workflow Status Hold
	            		$status['WorkflowStatus'] = 4;
	            		$this->db->where('WorkflowStatus <>',5);
	            		$this->db->where('OrderUID',$cus['OrderUID']);
	            		$this->db->update('torderassignment',$status);



	            		/* @author Parthasarathy <parthasarathy.m@avanzegroup.com> purpose insert notifications for all customer users */


	            		$CustomerUsers = $this->common_model->get('musers', ['CustomerUID'=>$order_details->CustomerUID, 'Active'=>1]);
	            		foreach ($CustomerUsers as $key => $user) 
	            		{
	            			$Message = '<span class="user-name"> Client Delay Started - #'.$order_details->OrderNumber.'</span> ' . $StartRemarks;
	            			$this->common_model->create_common_notification($user->UserUID,$Message,'order_summary?OrderUID='.$order_details->OrderUID);
	            		}
	            		return true;
	            	}
	            	else
	            	{
	            		return false;
	            	}
	            } 

	            function GetApiStatusCodeDetails($ApiRequestUID){
	            	$this->db->select ( '*' ); 
	            	$this->db->from ( 'mApiRequest' );
	            	$this->db->where ('mApiRequest.ApiRequestUID',$ApiRequestUID);
	            	$query = $this->db->get();
	            	$res = $query->row();
	            	return $res;
	            }

	            function update_stop_delay_time($OrderUID,$StopTime,$StopRemarks,$StopNoteType,$CustomerDelayUID)
	            {

	            	$cus['OrderUID'] = $OrderUID;
	            	$cus['CustomerDelayStopTime'] = $StopTime;
	            	$cus['CustomerDelayStopRemarks'] = $StopRemarks;
	            	$cus['CustomerDelayStopNoteType'] = $StopNoteType;
	            	$cus['StoppedByUserUID'] = $this->session->userdata('UserUID');
	            	$this->db->where('CustomerDelayUID',$CustomerDelayUID);
	            	$query = $this->db->update('mcustomerdelay',$cus);
	            	if($this->db->affected_rows()>0)
	            	{
   	    	  // Change Order Workflow Status Hold
	            		$status['WorkflowStatus'] = 3;
	            		$this->db->where('WorkflowStatus <>',5);
	            		$this->db->where('OrderUID',$cus['OrderUID']);
	            		$this->db->update('torderassignment',$status);

	            		$note['OrderUID'] = $cus['OrderUID'];
	            		$note['SectionUID'] = $cus['CustomerDelayStopNoteType'];
	            		$note['RoleType'] = '1,6,7';
	            		$note['Note'] = $cus['CustomerDelayStopRemarks'].' - Customer Delay Stop';
	            		$note['CreatedByUserUID'] = $this->session->userdata('UserUID');
	            		$note['CreatedOn'] = date('Y-m-d H:i:s',strtotime('now'));
	            		$res = $this->db->insert('tordernotes',$note);

	            		if($res){
	            			$this->real_ec_model->ResumeOnHoldApiOrder($OrderUID,$StopRemarks);
	            		}



	            		/* @author Parthasarathy <parthasarathy.m@avanzegroup.com> purpose insert notifications for all customer users */


	            		$torders = $this->db->get_where('torders', array('OrderUID'=>$OrderUID))->row();
	            		$CustomerUsers = $this->common_model->get('musers', ['CustomerUID'=>$order_details->CustomerUID, 'Active'=>1]);
	            		foreach ($CustomerUsers as $key => $user) 
	            		{
	            			$Message = '<span class="user-name"> Client Delay Started - #'.$order_details->OrderNumber.'</span> ' . $StartRemarks;
	            			$this->common_model->create_common_notification($user->UserUID,$Message,'order_summary?OrderUID='.$order_details->OrderUID);
	            		}

	            		return true;
	            	}
	            	else
	            	{
	            		return false;
	            	}
	            }


	            function gettime($OrderUID)
	            {
	            	$query = $this->db->query("SELECT MAX(CustomerDelayStartTime) as CustomerDelayStartTime FROM mcustomerdelay WHERE OrderUID ='$OrderUID' and CustomerDelayStopTime IS NULL Order By CustomerDelayUID DESC");
	            	$res = $query->row();
	            	if($res)
	            	{
	            		return $res->CustomerDelayStartTime;
	            	}

	            }

		  // function getdelayhistory($OrderUID)
		  // {
		  // 	$query = $this->db->query("SELECT *, DATE_FORMAT(CustomerDelayStartTime, '%m-%d-%Y %h:%i %p') AS StartTime, DATE_FORMAT(CustomerDelayStopTime, '%m-%d-%Y %h:%i %p') AS StopTime FROM mcustomerdelay WHERE OrderUID ='$OrderUID' ");
		  // 	return $query->result();
		  // }

	            function getdelayhistory($OrderUID,$loggedid)
	            {

	            	if($this->common_model->GetCustomerDelayQueue() == 2)
	            	{
	            		$query = $this->db->query("SELECT *, DATE_FORMAT(CustomerDelayStartTime, '%m-%d-%Y %h:%i %p') AS StartTime, DATE_FORMAT(CustomerDelayStopTime, '%m-%d-%Y %h:%i %p') AS StopTime FROM mcustomerdelay
	            			WHERE OrderUID ='$OrderUID' ");
	            		return $query->result();
	            	}
	            	else if($this->common_model->GetCustomerDelayQueue() == 1)
	            	{
	            		$query = $this->db->query("SELECT *, DATE_FORMAT(CustomerDelayStartTime, '%m-%d-%Y %h:%i %p') AS StartTime, DATE_FORMAT(CustomerDelayStopTime, '%m-%d-%Y %h:%i %p') AS StopTime FROM mcustomerdelay
	            			LEFT JOIN torderassignment ON torderassignment.OrderUID = mcustomerdelay.OrderUID WHERE mcustomerdelay.OrderUID ='$OrderUID' and torderassignment.AssignedToUserUID = '$loggedid' GROUP BY mcustomerdelay.CustomerDelayUID");
	            		return $query->result();
	            	}


	            }

	            function getdelayhistory_NoteType($OrderUID)
	            {
	            	$query = $this->db->query("SELECT CustomerDelayStartNoteType,CustomerDelayUID FROM mcustomerdelay WHERE OrderUID ='$OrderUID' AND CustomerDelayStopTime = '0000-00-00 00:00:00'");
	            	return $query->row();
	            }


	            function ChangeOrderStatus($OrderUID)
	            {
	            	$this->db->select ( 'StatusUID' ); 
	            	$this->db->from ( 'torders' );
	            	$this->db->where ('torders.OrderUID',$OrderUID);
	            	$query = $this->db->get();
	            	$Status = $query->row();	
	            	$StatusUID = $Status->StatusUID;

	            	if($StatusUID == $this->config->item('keywords')['Order Assigned'])
	            	{
	            		$OrderProgress = $this->config->item('keywords')['Order Work In Progress'];
	            		$status = array("StatusUID"=>$OrderProgress);
	            		$this->db->where(array("torders.OrderUID"=>$OrderUID));
	            		$this->db->update('torders',$status);
	            	}

	            }


	            function ChangeOrderStatusCheck($OrderUID)
	            {
	            	$this->db->select ( 'StatusUID' ); 
	            	$this->db->from ( 'torders' );
	            	$this->db->where ('torders.OrderUID',$OrderUID);
	            	$query = $this->db->get();
	            	$Status = $query->row();	
	            	$StatusUID = $Status->StatusUID;

	            	return $StatusUID;
	            }



	            function GetOrderDetails($OrderUID)
	            {
	            	$this->db->select ( '*' ); 
	            	$this->db->from ( 'torders' );
	            	$this->db->join ( 'mstates', 'PropertyStateUID = mstates.StateUID' , 'inner' );
	            	$this->db->join ( 'mcustomers', 'torders.CustomerUID = mcustomers.CustomerUID' , 'left' );
	            	$this->db->join ( 'mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID' , 'left' );
	            	$this->db->join ( 'morderpriority', 'morderpriority.PriorityUID = torders.PriorityUID' , 'left' );
	            	$this->db->join ( 'mcities', 'mcities.CityUID = torders.PropertyCity' , 'left' );
	            	$this->db->join ( 'mcounties', 'mcounties.CountyUID = torders.PropertyCountyUID' , 'left' );
	            	$this->db->join ( 'mtemplates', 'mtemplates.TemplateUID = torders.TemplateUID' , 'left' );
	            	$this->db->join ( 'msubproducts', 'msubproducts.SubProductUID = torders.SubProductUID' , 'left' );
	            	$this->db->where ('torders.OrderUID',$OrderUID);
	            	$query = $this->db->get();
	            	return $query->result();

	            }

	            function GetOrderLineDetails($OrderUID)
	            {
	            	$this->db->select ( '*' ); 
	            	$this->db->from ( 'torderpropertyroles' );
	            	$this->db->join ( 'torders', 'torderpropertyroles.OrderUID = torders.OrderUID' , 'left');
	            	$this->db->join ( 'mpropertyroles', 'mpropertyroles.PropertyRoleUID = torderpropertyroles.PropertyRoleUID' , 'inner' );
	            	$this->db->where ('torders.OrderUID',$OrderUID);
	            	$query = $this->db->get();
	            	return $query->result();

	            }

	            function GetAssessmentDetails($OrderUID)
	            {
	            	$this->db->select("*");
	            	$this->db->from('torderassessment');
	            	$this->db->where(array("torderassessment.OrderUID"=>$OrderUID));
	            	$query = $this->db->get();
	            	return $query->result();
	            }

	            function GetDeedDetails($OrderUID)
	            {
	            	$Deed = $this->config->item('DocumentTypeUID')['Deeds'];

	            	$this->db->select("*");
	            	$this->db->from('torderdeeds');
	            	$this->db->join('mdocumenttypes','torderdeeds.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
	            	$this->db->join('mestateinterests','torderdeeds.EstateInterestUID=mestateinterests.EstateInterestUID','left');
	            	$this->db->where(array("torderdeeds.OrderUID"=>$OrderUID));  
	            	$query = $this->db->get();
	            	return $query->result();

	            }


	            function GetDeedGrantorDetails($OrderUID)
	            {
	            	$Grantor = $this->config->item('PartyTypeUID')['Grantor'];

	            	$this->db->select("*");
	            	$this->db->from('torderdeedparties');
	            	$this->db->where(array("torderdeedparties.OrderUID"=>$OrderUID, "torderdeedparties.PartyTypeUID" =>$Grantor));
	            	$query = $this->db->get();
	            	return $query->result();
	            }

	            function GetDeedGranteeDetails($OrderUID)
	            {
	            	$Grantee = $this->config->item('PartyTypeUID')['Grantee'];

	            	$this->db->select("*"); 
	            	$this->db->from('torderdeedparties');
	            	$this->db->where(array("torderdeedparties.OrderUID"=>$OrderUID, "torderdeedparties.PartyTypeUID" =>$Grantee));
	            	$query = $this->db->get();
	            	return $query->result();
	            }

	            function GetMortgageDetails($OrderUID)
	            {
	            	$Mortgages = $this->config->item('DocumentTypeUID')['Mortgages'];

	            	$this->db->select("*");
	            	$this->db->from('tordermortgages');
	            	$this->db->join('mdocumenttypes','tordermortgages.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
	            	$this->db->join('mlientypes','tordermortgages.LienTypeUID=mlientypes.LienTypeUID','left');
	            	$this->db->join('mmortgagedbvtypes','tordermortgages.Mortgage_DBVTypeUID_1=mmortgagedbvtypes.DBVTypeUID','left');
	            	$this->db->where(array("tordermortgages.OrderUID"=>$OrderUID));       
	            	$query = $this->db->get();
	            	return $query->result();

	            }

	            function GetMortgagorsDetails($OrderUID)
	            {
	            	$Mortgagor = $this->config->item('PartyTypeUID')['Mortgagor'];

	            	$this->db->select("*");
	            	$this->db->from('tordermortgageparties');
	            	$this->db->where(array("tordermortgageparties.OrderUID"=>$OrderUID, "tordermortgageparties.PartyTypeUID" =>$Mortgagor));
	            	$query = $this->db->get();
	            	return $query->result();
	            }

	            function GetMortgageesDetails($OrderUID)
	            {

	            	$Mortgagee = $this->config->item('PartyTypeUID')['Mortgagee'];

	            	$this->db->select("*");
	            	$this->db->from('tordermortgageparties');
	            	$this->db->where(array("tordermortgageparties.OrderUID"=>$OrderUID, "tordermortgageparties.PartyTypeUID" =>$Mortgagee));
	            	$query = $this->db->get();
	            	return $query->result();
	            }

	            function GetPropertyDetails($OrderUID)
	            {
	            	$this->db->select("*");
	            	$this->db->from('torderpropertyinfo');
	            	$this->db->where(array("torderpropertyinfo.OrderUID"=>$OrderUID));     
	            	$query = $this->db->get();
	            	return $query->result();
	            }

	            function GetJudgmentDetails($OrderUID)
	            {
	            	$this->db->select("*");
	            	$this->db->from('torderjudgements');
	            	$this->db->join('mdocumenttypes','torderjudgements.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
	            	$this->db->where(array("torderjudgements.OrderUID"=>$OrderUID)); 
	            	$query = $this->db->get();
	            	return $query->result();
	            }

	            function GetJudgmentLineDetails($OrderUID)
	            {

	            	$this->db->select("*");
	            	$this->db->from('torderjudgementparties');
	            	$this->db->join('mpropertyroles','torderjudgementparties.PropertyRoleUID=mpropertyroles.PropertyRoleUID','left');
	            	$this->db->join('morderpartytypes','torderjudgementparties.PartyTypeUID=morderpartytypes.PartyTypeUID','left');
	            	$this->db->where(array("torderjudgementparties.OrderUID" =>$OrderUID));
	            	$query = $this->db->get();
	            	return $query->result();
	            }

	            function GetLeinDetails($OrderUID)
	            {
	            	$Liens = $this->config->item('DocumentTypeUID')['Liens'];

	            	$this->db->select("*");
	            	$this->db->from('torderleins');
	            	$this->db->join('mdocumenttypes','torderleins.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
	            	$this->db->join('mmortgagedbvtypes','torderleins.Lien_DBVTypeUID_1=mmortgagedbvtypes.DBVTypeUID','left');
	            	$this->db->where(array("torderleins.OrderUID"=>$OrderUID));    
	            	$query = $this->db->get();
	            	return $query->result();
	            }

	            function GetTaxDetails($OrderUID)
	            {
	            	$Taxes = $this->config->item('DocumentTypeUID')['Taxes'];

	            	$this->db->select("*");
	            	$this->db->from('tordertaxcerts');
	            	$this->db->join('mdocumenttypes','tordertaxcerts.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
	            	$this->db->join('mtaxcertbasis','tordertaxcerts.TaxBasisUID=mtaxcertbasis.TaxBasisUID','left');
	            	$this->db->join('mtaxstatus','tordertaxcerts.TaxStatusUID=mtaxstatus.TaxStatusUID','left');
	            	$this->db->join('mtaxauthority','tordertaxcerts.TaxAuthorityUID=mtaxauthority.TaxAuthorityUID','left');
	            	/*$this->db->join('mtaxexemptions','tordertaxcerts.TaxExemptionUID=mtaxexemptions.TaxExemptionUID','left');*/
	            	$this->db->where(array("mdocumenttypes.DocumentCategoryUID"=> $Taxes,"tordertaxcerts.OrderUID"=>$OrderUID));    
	            	$query = $this->db->get();
	            	return $query->result();
	            }

	            function get_deedno($OrderUID = '')
	            {
	            	$this->db->select("*"); 
	            	$this->db->from('torderdeedparties');
	            	$this->db->where(array("torderdeedparties.OrderUID"=>$OrderUID));
	            	$this->db->group_by('torderdeedparties.DeedSNo');
	            	$query = $this->db->get();
	            	return $query->result();	
	            }

	            function get_torderdeedby_deeds($deednos = '',$OrderUID = '')
	            {
	            	$this->db->select("*"); 
	            	$this->db->from('torderdeedparties');
	            	$this->db->where(array("torderdeedparties.OrderUID"=>$OrderUID));
	            	$this->db->where_in($deednos);
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

	            function get_partytypes_mort($OrderUID = '',$PartyTypeUID = '',$MortgageSNo = '')
	            {
	            	$this->db->select("*"); 
	            	$this->db->from('tordermortgageparties');
	            	$this->db->where(array("tordermortgageparties.OrderUID"=>$OrderUID,"PartyTypeUID"=>$PartyTypeUID,"MortgageSNo"=>$MortgageSNo));
	            	$query = $this->db->get();
	            	return $query->result();	
	            } 

	            function get_partytypes_judg($OrderUID = '',$JudgementSNo = '')
	            {
	            	$this->db->select("*"); 
	            	$this->db->from('torderjudgementparties');
	            	$this->db->join('morderpartytypes','torderjudgementparties.PartyTypeUID=morderpartytypes.PartyTypeUID','left');    
	            	$this->db->join('mpropertyroles','torderjudgementparties.PropertyRoleUID=mpropertyroles.PropertyRoleUID','left');	
	            	$this->db->where(array("torderjudgementparties.OrderUID"=>$OrderUID,"JudgementSNo"=>$JudgementSNo));
	            	$query = $this->db->get();
	            	return $query->result();	
	            } 

	            function get_deedno_format($deednos){

	            	$deed_id = [];
	            	if(count($deednos) > 0 ){
	            		foreach ($deednos as $key => $deedno) {
	            			$deed_id[] = $deedno->DeedSNo;
	            		}
	            	}
	            	return $deed_id;
	            }

	            function GetJudgmentParties($JudgementSNo)
	            {

	            	$this->db->select("*");
	            	$this->db->from('torderjudgementparties');
	            	$this->db->join('mpropertyroles','torderjudgementparties.PropertyRoleUID=mpropertyroles.PropertyRoleUID','left');
	            	$this->db->join('morderpartytypes','torderjudgementparties.PartyTypeUID=morderpartytypes.PartyTypeUID','left');
	            	$this->db->where(array("torderjudgementparties.JudgementSNo" =>$JudgementSNo));
	            	$query = $this->db->get();
	            	return $query->result();
	            }

	            function GetCopyOrderdetailsBytableUsed($PostArray){
	            	$TableUsed = $PostArray['TableUsed'];
	            	$AdditionalTableUsed = $PostArray['AdditionalTableUsed'];
	            	$SubMenuWorkflowModuleUID = $PostArray['SubMenuWorkflowModuleUID'];
	            	$AutoIncrementSNo = $PostArray['AutoIncrementSNo'];
	            	$IsSingleTableUsed = $PostArray['IsSingleTableUsed'];
	            	$FromOrderUID = $PostArray['FromOrderUID'];
	            	$ToOrderUID = $PostArray['ToOrderUID'];
	            	$searchString = ',';

	            	if($AdditionalTableUsed != ''){
	            		$this->db->select("*");
	            		$this->db->from($TableUsed);
	            		$this->db->where(array("OrderUID" =>$FromOrderUID));
	            		$query = $this->db->get();
	            		$res = $query->result();

	            		$result = [];
	            		if(strpos($AdditionalTableUsed, $searchString) !== false )
	            		{
	            			$AdditionalTableUsed = explode(",",$AdditionalTableUsed);
	            			for ($i=0; $i <count($AdditionalTableUsed) ; $i++) {
	            				$this->db->select("*");
	            				$this->db->from($AdditionalTableUsed[$i]);
	            				$this->db->where(array("OrderUID" =>$FromOrderUID));
	            				$query = $this->db->get();
	            				$result[] = $query->result();
					//echo $AdditionalTableUsed[$i].'<br>';
	            			}
	            		}
	            		else
	            		{
	            			$AdditionalTableUsed = array('0'=>$AdditionalTableUsed);
 					//echo "<pre>";print_r($AdditionalTableUsed);exit();
	            			$this->db->select("*");
	            			$this->db->from($AdditionalTableUsed[0]);
	            			$this->db->where(array("OrderUID" =>$FromOrderUID));
	            			$query = $this->db->get();
	            			$result[] = $query->result();
					//echo $AdditionalTableUsed;
	            		}	

				//exit();
	            		return array('MainTable' => $res, 'SubTable' => $result, 'TableUsed' => $TableUsed, 'AdditionalTableUsed' => $AdditionalTableUsed, 'AutoIncrementSNo' => $AutoIncrementSNo, 'IsSingleTableUsed' => $IsSingleTableUsed);
	            	}
	            	if($AdditionalTableUsed =='' )
	            	{
			//echo "AdditionalTableUsed";exit();
	            		$TableUsed = $PostArray['TableUsed'];
	            		$this->db->select("*");
	            		$this->db->from($TableUsed);
	            		$this->db->where(array("OrderUID" =>$FromOrderUID));
	            		$query = $this->db->get();
	            		$res = $query->result();
	            		return array('MainTable' => $res, 'SubTable' => '', 'TableUsed' => $TableUsed, 'AdditionalTableUsed' => '', 'AutoIncrementSNo' => $AutoIncrementSNo, 'IsSingleTableUsed' => $IsSingleTableUsed);
	            	}
	            }
	            function subTableResults($AdditionalTableUsed,$FromOrderUID,$FromAutoIncrementSNo,$AutoIncrementSNo){

	            	$this->db->select("*");
	            	$this->db->from($AdditionalTableUsed);
	            	$this->db->where(array("OrderUID" =>$FromOrderUID, $AutoIncrementSNo =>$FromAutoIncrementSNo));
	            	$query = $this->db->get();
	            	$result = $query->result();

	            	return $result;
	            }

	            function GetOrderUIDByEntryDataDetails($OrderUID,$TableUsed){

	            	$query = $this->db->query("SELECT EXISTS(SELECT * FROM $TableUsed WHERE OrderUID = $OrderUID) as CheckOrderUID;
	            		");
	            	return $query->row();
	            }

	            function PutPasteOrderDetailsByCopyOrder($CopyOrder,$ToOrderUID)
	            {

	            	if($CopyOrder['AdditionalTableUsed'] != NULL)
	            	{
	            		foreach ($CopyOrder['MainTable'] as $fieldArray) 
	            		{
	            			$keys = array();
	            			$values = array();
	            			$Subkeys = array();
	            			$Subvalues = array();

	            			$arr = (array) $fieldArray;

	            			$FromAutoIncrementSNo = '';
	            			$AutoIncrementSNo = $CopyOrder['AutoIncrementSNo'];


	            			foreach($arr as $k => $v)
	            			{

	            				$FromOrderUID = $arr['OrderUID'];				

	            				$SubPostArray = [];					

	            				if ($k === $CopyOrder['AutoIncrementSNo']) {

	            					unset($arr[$k]);

	            					foreach($arr as $key => $value)
	            					{
	            						if( $key == "OrderUID")
	            						{
	            							$key = $key;	
	            							$value = $ToOrderUID;	
	            						}
	            						else
	            						{
	            							$key = $key;
	            							$value = $value;
	            						}

	            						array_push($keys, $key);
	            						array_push($values, $value);
	            					}

	            					$column_name = $keys;
	            					$column_value = $values;
	            					$PostArray = array_combine($column_name, $column_value);						

	            					$query = $this->db->insert($CopyOrder['TableUsed'], $PostArray);

	            					$ToAutoIncrementSNo=$this->db->insert_id();

	            					if($query){

	            						if( $k === $AutoIncrementSNo)
	            						{
	            							$FromAutoIncrementSNo = $v;

								//Get Sub Table Values
								//echo "<pre>";print_r($CopyOrder['AdditionalTableUsed']);exit();
	            							for ($i=0; $i < count($CopyOrder['AdditionalTableUsed']) ; $i++) {
	            								$SubTableResults ='';
	            								$SubTableResults = $this->subTableResults($CopyOrder['AdditionalTableUsed'][$i],$FromOrderUID,$FromAutoIncrementSNo,$AutoIncrementSNo);
								//echo "<pre>";print_r($CopyOrder['AdditionalTableUsed'][$i]);exit();
	            								foreach($SubTableResults as $el => $row)
	            								{
	            									$subtable = (array) $row;
	            									foreach($subtable as $col_name => $col_val)
	            									{

	            										if ($col_name === $CopyOrder['AutoIncrementSNo'] ) {

	            											unset($subtable[$col_name]);


	            											if($CopyOrder['AdditionalTableUsed'][$i] == 'tordermortgageassignment' || $CopyOrder['AdditionalTableUsed'][$i] == 'tordertaxinstallment')
	            											{

	            												foreach($subtable as $key => $value)
	            												{

	            													if( $key == "OrderUID")
	            													{
	            														$key = $key;	
	            														$value = $ToOrderUID;	
	            													}
	            													else
	            													{
	            														$key = $key;
	            														$value = $value;
	            													}

	            													array_push($Subkeys, $key);
	            													array_push($Subvalues, $value);

	            												}

	            												array_push($Subkeys, $AutoIncrementSNo);
	            												array_push($Subvalues, $ToAutoIncrementSNo);

	            												$col_name = $Subkeys;
	            												$column_value = $Subvalues;
	            												$SubPostArray = array_combine($col_name, $column_value);
	            												unset($SubPostArray['PartyTypeUID']);
	            												unset($SubPostArray['PartyName']);
	            												unset($SubPostArray['TaxExemptionUID']);
	            												unset($SubPostArray['TaxAmount']);

	            											}
	            											else
	            											{
	            												foreach($subtable as $key => $value)
	            												{

	            													if( $key == "OrderUID")
	            													{
	            														$key = $key;	
	            														$value = $ToOrderUID;	
	            													}
	            													else
	            													{
	            														$key = $key;
	            														$value = $value;
	            													}

	            													array_push($Subkeys, $key);
	            													array_push($Subvalues, $value);

	            												}

	            												array_push($Subkeys, $AutoIncrementSNo);
	            												array_push($Subvalues, $ToAutoIncrementSNo);

	            												$col_name = $Subkeys;
	            												$column_value = $Subvalues;
	            												$SubPostArray = array_combine($col_name, $column_value);
	            											}
												//echo '<pre>';print_r($SubPostArray);
	            											$query = $this->db->insert($CopyOrder['AdditionalTableUsed'][$i], $SubPostArray);
	            											$SubPostArray = '';


	            										}
	            									}


	            								}
	            							}

	            						}


	            					}
	            				}

	            			}
	            		}
	            	}

	            	if($CopyOrder['AdditionalTableUsed'] == NULL && $CopyOrder['AutoIncrementSNo'] != NULL)
	            	{
	            		foreach ($CopyOrder['MainTable'] as $fieldArray) 
	            		{
	            			$keys = array();
	            			$values = array();
	            			$Subkeys = array();
	            			$Subvalues = array();

	            			$arr = (array) $fieldArray;

	            			$FromAutoIncrementSNo = '';
	            			$AutoIncrementSNo = $CopyOrder['AutoIncrementSNo'];

	            			foreach($arr as $k => $v)
	            			{
	            				$FromOrderUID = $arr['OrderUID'];				
	            				$SubPostArray = [];					

	            				if ($k === $CopyOrder['AutoIncrementSNo']) 
	            				{
	            					unset($arr[$k]);

	            					foreach($arr as $key => $value)
	            					{
	            						if( $key == "OrderUID")
	            						{
	            							$key = $key;	
	            							$value = $ToOrderUID;	
	            						}
	            						else
	            						{
	            							$key = $key;
	            							$value = $value;
	            						}

	            						array_push($keys, $key);
	            						array_push($values, $value);
	            					}

	            					$column_name = $keys;
	            					$column_value = $values;
	            					$PostArray = array_combine($column_name, $column_value);						

	            					$query = $this->db->insert($CopyOrder['TableUsed'], $PostArray);
	            				}
	            			}
	            		}
	            	}

	            	if($CopyOrder['AdditionalTableUsed'] == NULL && $CopyOrder['AutoIncrementSNo'] == NULL && $CopyOrder['IsSingleTableUsed'] == 1)
	            	{
	            		$keys = array();
	            		$values = array();
	            		$Subkeys = array();
	            		$Subvalues = array();

	            		foreach ($CopyOrder['MainTable'] as $fieldArray) 
	            		{			
	            			$arr = (array) $fieldArray;

	            			unset($arr['OrderUID']);

	            			$FromAutoIncrementSNo = '';
	            			$AutoIncrementSNo = $CopyOrder['AutoIncrementSNo'];

	            			array_push($keys, 'OrderUID');
	            			array_push($values, $ToOrderUID);	

	            			$column_name = $keys;
	            			$column_value = $values;
	            			$PostArray = array_combine($column_name, $column_value);

	            			$EntryData = array_merge($PostArray,$arr);

	            			$OrderUID = $EntryData['OrderUID'];

	            			$CheckOrderUID = $this->GetOrderUIDByEntryDataDetails($OrderUID,$CopyOrder['TableUsed']);

	            			$CheckOrderUID = $CheckOrderUID->CheckOrderUID;

	            			if($CheckOrderUID == 1){
	            				$this->db->where(array("OrderUID"=>$OrderUID));        
	            				$query = $this->db->update($CopyOrder['TableUsed'], $EntryData);
	            			}

	            			if($CheckOrderUID == 0){
	            				$query = $this->db->insert($CopyOrder['TableUsed'], $EntryData);
	            			}		
	            		}				

	            	}

	            	if($CopyOrder['AdditionalTableUsed'] == NULL && $CopyOrder['AutoIncrementSNo'] == NULL && $CopyOrder['IsSingleTableUsed'] == 0)
	            	{
	            		$keys = array();
	            		$values = array();
	            		$Subkeys = array();
	            		$Subvalues = array();

	            		foreach ($CopyOrder['MainTable'] as $fieldArray) 
	            		{			
	            			$arr = (array) $fieldArray;

	            			$FromAutoIncrementSNo = '';
	            			$AutoIncrementSNo = $CopyOrder['AutoIncrementSNo'];

	            			foreach($arr as $k => $v)
	            			{
	            				$FromOrderUID = $arr['OrderUID'];				
	            				$SubPostArray = [];					

	            				if( $k == "OrderUID")
	            				{
	            					$k = $k;	
	            					$v = $ToOrderUID;	
	            				}
	            				else
	            				{
	            					$k = $k;
	            					$v = $v;
	            				}

	            				array_push($keys, $k);
	            				array_push($values, $v);											
	            			}

	            			$column_name = $keys;
	            			$column_value = $values;
	            			$PostArray = array_combine($column_name, $column_value);	

	            			$query = $this->db->insert($CopyOrder['TableUsed'], $PostArray);
	            		}				

	            	}			
	            }


	            function GetOrderDetailsForCopy($OrderDetail){

	            	$PropertyAddress1 = $OrderDetail->PropertyAddress1;
	            	$PropertyAddress2 = $OrderDetail->PropertyAddress2;
	            	$PropertyCity = $OrderDetail->PropertyCity;
	            	$PropertyStateUID = $OrderDetail->PropertyStateUID;
	            	$PropertyZipcode = $OrderDetail->PropertyZipcode;
	            	$PropertyCountyUID = $OrderDetail->PropertyCountyUID;
	            	$OrderUID = $OrderDetail->OrderUID;

	            	$this->db->select ( '*' ); 
	            	$this->db->from ( 'torders' );
	            	$this->db->where(array("torders.PropertyCity"=>$PropertyCity,"torders.PropertyCountyUID"=>$PropertyCountyUID,"torders.PropertyStateUID"=>$PropertyStateUID,"torders.PropertyZipcode"=>$PropertyZipcode )); 
	            	$this->db->where("torders.OrderUID <>",$OrderUID);
	            	$this->db->group_by('torders.OrderUID');
	            	$query = $this->db->get();
	            	return $query->result();		

	            }

	            function GetDuplicateOrder($DatabaseAddress,$OrderUID)
	            {

	            	$query = $this->db->query(" SELECT *,TRIM(CONCAT_WS(' ',TRIM(PropertyAddress1),TRIM(PropertyAddress2))) AS whole_name FROM torders
	            		LEFT JOIN mcities ON mcities.CityUID = torders.PropertyCity
	            		LEFT JOIN mstates ON mstates.StateUID = torders.PropertyStateUID
	            		LEFT JOIN mcounties ON mcounties.CountyUID = torders.PropertyCountyUID
	            		INNER JOIN `torderassignment` ON torderassignment.OrderUID = torders.OrderUID
	            		WHERE torders.OrderUID <> '$OrderUID'
	            		GROUP BY torders.OrderUID 
	            		HAVING whole_name LIKE '$DatabaseAddress'");
	            	return $query->result_array();
	            }



	            function GetCustomerDetails($CustomerUID)
	            {
	            	$this->db->select('*')->from('mcustomers');
	            	$this->db->join('mstates', 'mstates.StateUID=mcustomers.CustomerStateUID', 'left');
	            	$this->db->join('mcounties', 'mcounties.CountyUID=mcustomers.CustomerCountyUID', 'left');
	            	$this->db->join('mcities', 'mcities.CityUID=mcustomers.CustomerCityUID', 'left');
	            	$this->db->join('mtemplates', 'mtemplates.TemplateUID=mcustomers.DefaultTemplateUID', 'left');
	            	$this->db->join('morderpriority', 'morderpriority.PriorityUID=mcustomers.PriorityUID', 'left');
	            	$this->db->join('mpricing', 'mpricing.PricingUID=mcustomers.PricingUID', 'left');
	            	$this->db->where(array('mcustomers.CustomerUID' => $CustomerUID ));
	            	return $this->db->get()->result();

	            }


	            function GetCustomerProducts($CustomerUID)
	            {
	            	$this->db->select('*')->from('mcustomerproducts');
	            	$this->db->join('mproducts', 'mproducts.ProductUID=mcustomerproducts.ProductUID', 'left');
	            	$this->db->join('msubproducts', 'msubproducts.SubProductUID=mcustomerproducts.SubProductUID', 'left');
	            	$this->db->where(array('mcustomerproducts.CustomerUID' => $CustomerUID ));
	            	$this->db->group_by('mcustomerproducts.ProductUID');
	            	return $this->db->get()->result();

	            }


	            function GetCustomerSubProductByID($CustomerUID, $SubProductUID, $ProductUID)
	            {
	            	$this->db->select('*')->from('mcustomerproducts');
	            	$this->db->join('mproducts', 'mproducts.ProductUID=mcustomerproducts.ProductUID', 'left');
	            	$this->db->join('msubproducts', 'msubproducts.SubProductUID=mcustomerproducts.SubProductUID', 'left');
	            	$this->db->where(array('mcustomerproducts.CustomerUID' => $CustomerUID, 'mcustomerproducts.ProductUID'=> $ProductUID, 'mcustomerproducts.SubProductUID'=>$SubProductUID ));
	            	return $this->db->get()->row();

	            }

	            function GetCustomerTemplate($CustomerUID, $SubProductUID, $ProductUID)
	            {
	            	$this->db->select('*')->from('mcustomertemplates');
	            	$this->db->join('mtemplates', 'mtemplates.TemplateUID=mcustomertemplates.TemplateUID', 'left');
	            	$this->db->where(array('mcustomertemplates.ProductUID'=> $ProductUID, 'mcustomertemplates.SubProductUID'=>$SubProductUID ));
	            	return $this->db->get()->row();

	            }


	            function GetCustomerWorkflows($CustomerUID, $SubProductUID, $ProductUID)
	            {
	            	$this->db->select('*')->from('mcustomerworkflowmodules');
	            	$this->db->join('mworkflowmodules', 'mworkflowmodules.WorkflowModuleUID=mcustomerworkflowmodules.WorkflowModuleUID', 'left');
	            	$this->db->where(array('mcustomerworkflowmodules.ProductUID'=> $ProductUID, 'mcustomerworkflowmodules.SubProductUID'=>$SubProductUID, 'mcustomerworkflowmodules.CustomerUID'=> $CustomerUID ));
	            	return $this->db->get()->result();

	            }


	            function GetCustomerPriorities($CustomerUID, $SubProductUID, $ProductUID, $PriorityUID)
	            {
	            	$this->db->select('*')->from('mcustomerproducttat');
	            	$this->db->join('morderpriority', 'morderpriority.PriorityUID=mcustomerproducttat.PriorityUID', 'left');
	            	$this->db->where(array('mcustomerproducttat.ProductUID'=> $ProductUID, 'mcustomerproducttat.SubProductUID'=>$SubProductUID, 'mcustomerproducttat.CustomerUID'=> $CustomerUID, 'mcustomerproducttat.PriorityUID'=>$PriorityUID ));
	            	return $this->db->get()->result();

	            }


	            function GetCustomerProductPricing($CustomerUID, $SubProductUID, $ProductUID, $StateUID, $CountyUID)
	            {
	            	$this->db->select('mpricingproducts.*')->from('mpricingproducts');
	            	$this->db->join('mcustomers', 'mcustomers.PricingUID=mpricingproducts.PricingUID', 'left');
	            	$this->db->where(array('mpricingproducts.SubProductUID'=>$SubProductUID, 'mpricingproducts.StateUID'=> $StateUID, 'mpricingproducts.CountyUID'=>$CountyUID));
	            	return $this->db->get()->row();

	            }


	            function GetCustomerOrderTypeDoc($CustomerUID, $OrderTypeUID)
	            {
	            	$this->db->select('*')->from('mcustomerordertypedoc');
	            	$this->db->where(array('mcustomerordertypedoc.CustomerUID'=>$CustomerUID));
	            	$CustomerOrderTypeDoc=$this->db->get()->result();

	            	$this->db->select('*')->from('mordertypes');
	            	$this->db->where('OrderTypeUID', $OrderTypeUID);
	            	$result=$this->db->get()->row();

	            	$OrderType = '';

	            	$mcustomerordertypedoc['DocumentName']="";
	            	$mcustomerordertypedoc['OrderType']=$result->OrderTypeName;
	            	foreach ($CustomerOrderTypeDoc as $key => $row)
	            	{
	            		$OrderType .= $row->OrderTypeUID;
	            		$OrderTypeUIDs=explode(',', $OrderType);

	            		foreach ($OrderTypeUIDs as $key => $value) {
	            			if($value==$OrderTypeUID)
	            			{
	            				$mcustomerordertypedoc['DocumentName']=$row->DocumentName;
	            			}
	            			else
	            			{
	            				$mcustomerordertypedoc['DocumentName']=$row->DocumentName;
	            			}
	            		}

	            	}
	            	return (object)$mcustomerordertypedoc;


	            }

	            function GetCustomerDefaultSubProducts($CustomerUID)
	            {
	            	$this->db->select('*')->from('mcustomerdefaultproduct');
	            	$this->db->where(array('mcustomerdefaultproduct.CustomerUID'=>$CustomerUID));
	            	$CustomerDefaultProduct=$this->db->get()->row();

	            	$OrderType = '';
	            	$OrderTypeUIDs=array(); $samplearray=[];

		// $mcustomerdefaultproduct['ProductType']=array();
	            	$mcustomerdefaultproduct['DefaultProduct']=$CustomerDefaultProduct->DefaultProductSubCode;
	            	if($CustomerDefaultProduct->DefaultProductSubCode==1)
	            	{
	            		$OrderType = $CustomerDefaultProduct->DefaultProductSubValue;
	            		$OrderTypeUIDs=explode(',', $OrderType);
	            		$mcustomerdefaultproduct['DefaultProduct']='Default Product';
			// $mcustomerdefaultproduct['ProductType']=$OrderTypeUIDs;
	            	}
	            	elseif($CustomerDefaultProduct->DefaultProductSubCode==2)
	            	{
	            		$OrderType = $CustomerDefaultProduct->DefaultProductSubValue;
	            		$OrderTypeUIDs=explode(',', $OrderType);
	            		$mcustomerdefaultproduct['DefaultProduct']='Most Processed SubProduct of Month';

	            	}
	            	else
	            	{
	            		$OrderType = $CustomerDefaultProduct->DefaultProductSubValue;
	            		$OrderTypeUIDs=explode(',', $OrderType);
	            		$mcustomerdefaultproduct['DefaultProduct']='Most Processed SubProduct so far';
	            	}
	            	foreach ($OrderTypeUIDs as $key => $value) {
	            		$this->db->select('*')->from('msubproducts');
	            		$this->db->where('SubProductUID', $value);
	            		$result=$this->db->get()->row();
	            		array_push($samplearray, $result->SubProductName);

	            	}
	            	$mcustomerdefaultproduct['ProductType']=$samplearray;


	            	return $mcustomerdefaultproduct;


	            }


	            function GetCustomerSubProducts($ProductUID, $CustomerUID)
	            {
	            	$this->db->select('*')->from('mcustomerproducts');
	            	$this->db->join('mproducts', 'mproducts.ProductUID=mcustomerproducts.ProductUID', 'left');
	            	$this->db->join('msubproducts', 'msubproducts.SubProductUID=mcustomerproducts.SubProductUID', 'left');
	            	$this->db->where(array('mcustomerproducts.CustomerUID' => $CustomerUID, 'mcustomerproducts.ProductUID'=> $ProductUID ));
	            	$this->db->order_by('mcustomerproducts.SubProductUID');
	            	return $this->db->get()->result();

	            }

	            function get_customer_vendorpricing($OrderUID){
	            	$this->db->select ( '*' ); 
	            	$this->db->from ( 'torders' );
	            	$this->db->where ('torders.OrderUID',$OrderUID);
	            	$this->db->join('mcustomers', 'mcustomers.CustomerUID = torders.CustomerUID');
	            	$this->db->join('mabstractor', 'mabstractor.AbstractorUID = torders.AbstractorUID','left');
	            	$query = $this->db->get();
	            	return $query->row();
	            }

	            function CheckSearchExistingData($OrderUID)
	            {
	            	$tables = array('torderabstractor','torderdocuments');
	            	$res = '';
	            	foreach ($tables as $value) {

	            		$this->db->select ( '*' ); 
	            		$this->db->from ( $value );
	            		$this->db->where('OrderUID', $OrderUID);
	            		$query = $this->db->get();
	            		if($query->num_rows() > 0)
	            		{
	            			$res .= '1,';
	            		}
	            		else{
	            			$res .= '0,';
	            		}
	            	}
	            	return $res;
	            }

	            function CheckTypingExistingData($OrderUID)
	            {
	            	$tables = array('torderdeeds','torderdeedparties','tordermortgages','tordermortgageparties','torderpropertyinfo','torderjudgements','torderjudgementparties','torderleins','torderaddress','torderlegaldescription');
	            	$res = '';
	            	foreach ($tables as $value) {

	            		$this->db->select ( '*' ); 
	            		$this->db->from ( $value );
	            		$this->db->where('OrderUID', $OrderUID);
	            		$query = $this->db->get();
	            		if($query->num_rows() > 0)
	            		{
	            			$res .= '1,';
	            		}
	            		else{
	            			$res .= '0,';
	            		}
	            	}
	            	return $res;
	            }

	            function CheckTaxExistingData($OrderUID)
	            {

	            	$tables = array('tordertaxcerts','torderassessment','tordertaxexemptions','tordertaxinstallment');
	            	$res = '';
	            	foreach ($tables as $value) {

	            		$this->db->select ( '*' ); 
	            		$this->db->from ( $value );
	            		$this->db->where('OrderUID', $OrderUID);
	            		$query = $this->db->get();
	            		if($query->num_rows() > 0)
	            		{
	            			$res .= '1,';
	            		}
	            		else{
	            			$res .= '0,';
	            		}
	            	}

	            	return $res;
	            }

	            function GetCurrentSubProduct($OrderUID)
	            {
	            	$this->db->select ( 'SubProductUID' ); 
	            	$this->db->from ( 'torders' );
	            	$this->db->where ('torders.OrderUID',$OrderUID);
	            	$query = $this->db->get()->row();
	            	return $query->SubProductUID;
	            }

	// function GetCurrentProduct($GetCurrentSubProduct)
	// {
	// 	$this->db->select ( 'ProductName' ); 
	// 	$this->db->from ( 'mproducts' );
	// 	$this->db->join('msubproducts', 'msubproducts.ProductUID = mproducts.ProductUID', 'left');
	// 	$this->db->where ('msubproducts.SubProductUID',$GetCurrentSubProduct);
	// 	$query = $this->db->get()->row();
	// 	return $query->ProductName;
	// }

	            function GetSubProduct($GetCurrentSubProduct)
	            {
	            	$this->db->select ( 'SubProductName' ); 
	            	$this->db->from ( 'msubproducts' );
	            	$this->db->where ('msubproducts.SubProductUID',$GetCurrentSubProduct);
	            	$query = $this->db->get()->row();
	            	return $query->SubProductName;
	            }

	            function GetWorkflowModule($CustomerUID,$SubProductUID)
	            {
	            	$this->db->select ( 'WorkflowModuleUID' ); 
	            	$this->db->from ( 'mcustomerworkflowmodules' );
	            	$this->db->where (array('SubProductUID' => $SubProductUID, 'CustomerUID'=> $CustomerUID ));
	            	$query = $this->db->get()->result_array();
	            	return $query;
	            }

	            function GetWorkflowName($WorkflowModuleUID)
	            {
	            	$this->db->select('WorkflowModuleName'); 
	            	$this->db->from ( 'mworkflowmodules' );
	            	$this->db->where (array('WorkflowModuleUID' => $WorkflowModuleUID));
	            	$query = $this->db->get()->row();
	            	return $query->WorkflowModuleName;
	            }

	            function ResetOrders($OrderUID,$Workflows){


	            	$this->db->trans_start();

	            	if($Workflows == 1){

			// $tables = array('torderabstractor','torderdocuments');
			// $this->db->where('OrderUID', $OrderUID);
			// $this->db->delete($tables);
	            	}
	            	elseif($Workflows == 2){

	            		$tables = array('torderdeeds','torderdeedparties','tordermortgages','tordermortgageparties','torderpropertyinfo','torderjudgements','torderjudgementparties','torderleins','torderaddress','torderlegaldescription');
	            		$this->db->where('OrderUID', $OrderUID);
	            		$this->db->delete($tables);

	            	}
	            	elseif($Workflows == 3){

	            		$tables = array('tordertaxcerts','torderassessment','tordertaxexemptions','tordertaxinstallment');
	            		$this->db->where('OrderUID', $OrderUID);
	            		$this->db->delete($tables);

	            	}
	            	$this->db->trans_complete();

	            	if ($this->db->trans_status() === FALSE) {

	            		$this->db->trans_rollback();
	            		return FALSE;
	            	} 
	            	else {

	            		$this->db->trans_commit();
	            		return TRUE;
	            	}
	            }

	            function GetOrdersOrderTypes($OrderUID)
	            {
	            	$this->db->where('torders.OrderUID',$OrderUID);	
	            	$this->db->select('torders.SubProductUID, mordertypes.OrderTypeName, mordertypes.OrderTypeUID')->from('torders');
	            	$this->db->join('mordertypes', 'mordertypes.OrderTypeUID = torders.OrderTypeUID','LEFT');
	            	return $this->db->get()->result();
	            }

	            function GetSubProductOrderType($SubProductUID)
	            {
	            	$this->db->where('msubproducts.SubProductUID',$SubProductUID);	
	            	$this->db->select('msubproducts.OrderTypeUID, mordertypes.OrderTypeName')->from('msubproducts');
	            	$this->db->join('mordertypes','mordertypes.OrderTypeUID = msubproducts.OrderTypeUID','LEFT');
	            	return $this->db->get()->row();
	            }

	            function UpdateSubProduct($OrderUID,$SubProductUID)
	            {
		/*$data1['ModuleName']='Subproduct-update';
		$data1['IpAddreess']=$_SERVER['REMOTE_ADDR']; 
		$data1['DateTime']=date('y-m-d H:i:s');
		$data1['TableName']='torders';
		$data1['OrderUID']=$OrderUID;
		$data1['UserUID']=$this->session->userdata('UserUID');  */  

		$this->db->trans_begin();

		$this->db->select('*');
		$this->db->from('torders');
		$this->db->where('OrderUID',$OrderUID);                 
		$oldvalue=$this->db->get('')->row_array();
		$OldSubProductUID = $oldvalue['SubProductUID'];
		$Old_OrderDueDateTime = $oldvalue['OrderDueDatetime'];
		$PriorityUID = $oldvalue['PriorityUID'];
		$old_msubproducts = $this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$OldSubProductUID.'')->row();

		$this->db->set('SubProductUID', $SubProductUID);
		$this->db->where('OrderUID', $OrderUID);
		$this->db->update('torders');

		/*Function to change OrderNumber*/
		$this->Change_OrderNumber($OrderUID,$SubProductUID);

		if($oldvalue['IsBilled'] == '0') {
			$this->calculate_customerpricing($OrderUID,$SubProductUID,$OldSubProductUID);
		}
		
		$this->db->select('*');
		$this->db->from('torders');
		$this->db->join('msubproducts','msubproducts.SubProductUID = torders.SubProductUID','LEFT');
		$this->db->where('OrderUID',$OrderUID); 
		$newvalue = $this->db->get('')->row_array();

		$NewSubProductUID = $newvalue['SubProductUID'];
		$new_msubproducts = $this->db->query('SELECT * FROM msubproducts WHERE SubProductUID='.$NewSubProductUID.'')->row();

		// $this->common_model->Audittrail_diff($newvalue,$oldvalue,$data1);

		/* Start - OrderDueDateTime Changes for Product & SubProduct*/
		$OrderDueDateTime = calculate_duedate($newvalue['OrderEntryDatetime'],$newvalue['CustomerUID'],$NewSubProductUID,$PriorityUID);
		$this->db->set('OrderDueDatetime', $OrderDueDateTime);
		$this->db->where('OrderUID', $OrderUID);
		$this->db->update('torders');
		/* End - OrderDueDateTime Changes for Product & SubProduct*/

		$tApiOrders = $this->db->query('SELECT * FROM tApiOrders WHERE OrderUID='.$OrderUID.'')->row();
		if($tApiOrders){
			$this->db->set('SubProductUID', $NewSubProductUID);
			$this->db->where('OrderUID', $OrderUID);
			$this->db->update('tApiOrders');
		}

		$NoteType = $this->GetNoteTypeUID('System Note');
		$SectionUID = $NoteType->SectionUID;
		$Notes.= 'Sub Product is changed from '.$old_msubproducts->SubProductName. ' to '.$new_msubproducts->SubProductName;
		$Notes.= '<br>OrderDueDate is changed from '.$Old_OrderDueDateTime.' to '.$OrderDueDateTime;
		$stre2 = 'Sub Product is changed from <b>'.$old_msubproducts->SubProductName.'</b> to <b>'.$new_msubproducts->SubProductName.'</b><br>OrderDueDate is changed from <b>'.$Old_OrderDueDateTime.'</b> to <b>'.$OrderDueDateTime.'</b>';
		$InsetData2 = array(
			'UserUID' => $this->loggedid,
			'OrderUID' => $OrderUID,
			'ModuleName' => 'Summary - Sub Product',
			'Content' => htmlentities($stre2),
			'DateTime' => date('Y-m-d H:i:s'));
		$this->common_model->InsertAuditTrail($InsetData2);

		$insert_notes = array(
			'Note' => $Notes,
			'SectionUID' => $SectionUID,
			'OrderUID' => $OrderUID,
			'RoleType' => '1,2,3,4,5,6,7,9,11,12',
			'CreatedByUserUID' => $this->session->userdata('UserUID'),
			'CreatedOn' => date('Y-m-d H:i:s')
		);

		$result = $this->db->insert("tordernotes", $insert_notes);

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			return false;
		}else{
			$this->db->trans_commit();
			return true;
		}
	}

	function GetNoteTypeUID($SectionName)
	{
		$this->db->select("*");
		$this->db->from('mreportsections');
		$this->db->where(array("mreportsections.SectionName"=>$SectionName));
		$query = $this->db->get();
		return $query->row();
	}


	function GetSub_productDetails($ProductUID, $CustomerUID)
	{
		$data['mcustomerproducts.ProductUID'] = $ProductUID;
		$data['mcustomerproducts.CustomerUID'] = $CustomerUID;
		$this->db->select('msubproducts.SubProductUID, msubproducts.SubProductName')->from('mcustomerproducts');
		$this->db->join('msubproducts','msubproducts.SubProductUID = mcustomerproducts.SubProductUID','LEFT');
		$this->db->where($data);
		$q = $this->db->get();
		return $q->result(); 
	}

	function GetCustomerProductUID($CustomerUID)
	{
		$this->db->select('ProductUID')->from('mcustomerproducts');
		$this->db->where('mcustomerproducts.CustomerUID',$CustomerUID);
		return $this->db->get()->row();	
	}

	function get_county_bystate($StateUID){
		$query = $this->db->query("SELECT * FROM mcounties WHERE StateUID = '".$StateUID."' AND Active = 1 ");
		return $query->result();
	}

	function UpdateOrderDueDateCustomerDelay($OrderUID,$OrderDueDatetime)
	{
		$data['OrderDueDatetime'] = $OrderDueDatetime;	
		$this->db->where('OrderUID',$OrderUID);
		$this->db->update('torders',$data);	
	}

	function GetWorkflowPricing($OrderUID)
	{
		$this->db->select('*')->from('torderassignment');
		$this->db->where('OrderUID',$OrderUID);
		return $this->db->get()->result();
	}

	/*******Function to change OrderNumber When Changed *******/
	function Change_OrderNumber($OrderUID,$SubProductUID){

		$Order = $this->db->select('OrderNumber, APIOrder')-> where('OrderUID', $OrderUID)->limit(1)->get('torders')->row();
		if(!empty($Order)){
			$Old_OrderNumber = $Order->OrderNumber;

			$SubProduct_details = $this->get_productcodeby_subproduct($SubProductUID);
			if(!empty($SubProduct_details)){
				
				$New_SubProductCode = $SubProduct_details->ProductCode;

				//remove tab and spaces in starting
				$str = preg_replace('/s+/S', " ", $Old_OrderNumber);

				/*Replace First Character from the string*/
				$NewOrderNumber =  substr_replace($str,$New_SubProductCode,0,1);
				$data = array(
					'OrderNumber' => $NewOrderNumber,
					'LastModifiedByUserUID' => $this->session->userdata('UserUID'),
					'LastTouchDateTime' => Date('Y-m-d H:i:s',strtotime('now'))
				);

				$this->db->where('OrderUID', $OrderUID);
				$this->db->update('torders', $data);


                /**
                * Function changed to update the order number in api
                * @author:  D.Samuel Prabhu
                * @since: 08 Feb 2020
                */

                if($Order->APIOrder==1)
                {
                	$this->common_model->UpdateApiOrderNumber($OrderUID,$NewOrderNumber);
                }

                if($this->db->affected_rows() > 0)
                {
                	return true;
                }
                else
                {
                	return false;
                }
            }	
            return false;
        }else{
        	return false;
        }
        return false;
    }

    function get_productcodeby_subproduct($SubProductUID){
    	$SubProduct_details = $this->db->query("SELECT ProductCode FROM msubproducts JOIN mproducts ON mproducts.ProductUID = msubproducts.ProductUID WHERE SubProductUID = '".$SubProductUID."' ");
    	return $SubProduct_details->row();
    }

    function SearchCheckWorkflowStatus($OrderUID){
    	$this->db->select('*');
    	$this->db->from('torderassignment');
    	$this->db->where('OrderUID',$OrderUID);
    	$this->db->where('WorkflowModuleUID',1);
    	return $this->db->get()->row();
    }

    function TypingCheckWorkflowStatus($OrderUID){

    	$this->db->select('*');
    	$this->db->from('torderassignment');
    	$this->db->where('OrderUID',$OrderUID);
    	$this->db->where('WorkflowModuleUID',2);
    	return $this->db->get()->row();
    }

    function TaxingCheckWorkflowStatus($OrderUID){

    	$this->db->select('*');
    	$this->db->from('torderassignment');
    	$this->db->where('OrderUID',$OrderUID);
    	$this->db->where('WorkflowModuleUID',3);
    	return $this->db->get()->row();
    }
    function SearchStatus($OrderUID){
    	$this->db->select('*');
    	$this->db->from('torders');
    	$this->db->where('OrderUID',$OrderUID);
    	return $this->db->get()->row();

    }

    function calculate_customerpricing($OrderUID,$SubProductUID,$OldSubProductUID=''){
    	$this->load->helper('customer_pricing');
    	$this->load->helper('customer_closing_pricing');
    	$closing_pricing = new Customer_closing_pricing();

    	$Order = $this->common_model->GetOrderDetailsbyUID($OrderUID);
    	$Orderdetails = $Order[0];

    	if($Orderdetails->IsBilled == 0) {


    		$pricing = new Customer_pricing();
    		$newproductdetails = $this->common_model->getProductDetailsBySubProductUID($SubProductUID);
    		$oldproductdetails = $this->common_model->getProductDetailsBySubProductUID($OldSubProductUID);

			//update torderpayment cycle with customer actual pricing
			//changing data after updated -- tOrderPayments table
    		$paymentsmanualrows = $this->db->select('*')->from('tOrderPayments')->where(array('OrderUID'=>$OrderUID,'BeneficiaryType'=>'Customer','IsBilled'=>0))->get()->result();
    		$previousValue = null;
    		foreach ($paymentsmanualrows as $paymentsmanualrowkey => $paymentsmanualrow) {

    			$previoustotalamount = 0;
    			$loopcustomeramount = 0;
    			$CustomerAmount = 0;

    			if(!empty($paymentsmanualrow->PaymentsUID)) {
    				if($previousValue) {
    					$previoustotalamount = $previousValue->CustomerAmount;
    				}

    				if(!empty($newproductdetails) && $newproductdetails->IsClosingProduct == 1) {

    					if(!empty($paymentsmanualrow->ScheduleUID)) {

    						$schedulerow = $this->db->select('*')->from('tOrderSchedule')->join('mabstractor','mabstractor.AbstractorUID = 	tOrderSchedule.AbstractorUID')->where('ScheduleUID',$paymentsmanualrow->ScheduleUID)->get()->row();
    						if($schedulerow->IsAbstractor == 1) {
    							$Orderdetails->VendorTypeUID = 1;
    						} elseif ($schedulerow->IsAttorney == 1) {
    							$Orderdetails->VendorTypeUID = 3;
    						} elseif ($schedulerow->IsNotary == 1) {
    							$Orderdetails->VendorTypeUID = 2;
    						}
    						$customer_pricing = $closing_pricing->get_Closing_Pricings($Orderdetails);

    						if($paymentsmanualrow->ApprovalFunction == 'CustomerActualPricing') {


    							$CustomerAmount = $customer_pricing->Pricing;
    							$data = array(
    								'CustomerActualAmount' => $CustomerAmount,
    								'CustomerAmount' => $CustomerAmount,
    								'OperatorType' => NULL,
    								'CustomerAdditionalAmount' => 0.00,
    								'LastModifiedByUserUID' => $this->session->userdata('UserUID'),
    								'LastTouchDateTime' => Date('Y-m-d H:i:s',strtotime('now'))
    							);
    							$this->db->where('OrderUID', $OrderUID);
    							$this->db->update('torders', $data);
    						} else {
    							$paymentsmanualrow->CustomerAdditionalAmount = $customer_pricing->DualClosingFee;
    						}

    					} elseif (empty($paymentsmanualrow->ScheduleUID) && $paymentsmanualrow->ApprovalFunction == 'CustomerActualPricing') {
							//echo '<pre>';print_r($paymentsmanualrow);
    						$data = array(
    							'CustomerActualAmount' => 0,
    							'CustomerAmount' => 0,
    							'OperatorType' => NULL,
    							'CustomerAdditionalAmount' => 0.00,
    							'LastModifiedByUserUID' => $this->session->userdata('UserUID'),
    							'LastTouchDateTime' => Date('Y-m-d H:i:s',strtotime('now'))
    						);
    						$this->db->where('OrderUID', $OrderUID);
    						$this->db->update('torders', $data);
    					}


    				} elseif (!empty($newproductdetails) && $paymentsmanualrow->ApprovalFunction == 'CustomerActualPricing') {


    					/*INSERT CUSTOMER ACTUAL PRICING*/

    					/*---------  --------- */
						//project is RMS update reverse pricing
						//RMS changes w.r.t.SubProduct
    					if($Orderdetails->RMS == 1){
    						$CustomerAmount = $pricing->get_Customer_ReverseMortgagePricing($Orderdetails->CustomerUID,$Orderdetails->SubProductUID);
    					} else {
    						$Orderss = new stdclass();
    						$Orderss->CustomerUID = $Orderdetails->CustomerUID;
    						$Orderss->SubProductUID = $SubProductUID;
    						$Orderss->PropertyCountyName = $Orderdetails->PropertyCountyName;
    						$Orderss->PropertyStateCode = $Orderdetails->PropertyStateCode;
    						/*@Desc Customer Pricing Update D2T-540 @Author Jainulabdeen @Updated May 21 2020*/
    						$CustomerAmountQuote = $pricing->get_Customer_Pricings_Quote($Orderss);	
    						$CustomerAmount = $CustomerAmountQuote->Pricing;
    						$CustomerQuote = $CustomerAmountQuote->IsQuote;
    					}

    					$data = array(
    						'CustomerActualAmount' => $CustomerAmount,
    						'CustomerAmount' => $CustomerAmount,
    						'IsQuote' => $CustomerQuote,
    						'OperatorType' => NULL,
    						'CustomerAdditionalAmount' => 0.00,
    						'LastModifiedByUserUID' => $this->session->userdata('UserUID'),
    						'LastTouchDateTime' => Date('Y-m-d H:i:s',strtotime('now'))
    					);

    					$this->db->where('OrderUID', $OrderUID);
    					$this->db->update('torders', $data);


    				}	

    				if($paymentsmanualrow->OperatorType == '-') {
    					$loopcustomeramount = $previoustotalamount-$paymentsmanualrow->CustomerAdditionalAmount;
    				}else{
    					$loopcustomeramount = $previoustotalamount+$paymentsmanualrow->CustomerAdditionalAmount;
    				}

    				$this->db->where('PaymentsUID',$paymentsmanualrow->PaymentsUID);
    				$this->db->update('tOrderPayments',array('CustomerAmount'=>$loopcustomeramount+$CustomerAmount,'CustomerAdditionalAmount'=>$paymentsmanualrow->CustomerAdditionalAmount));
    			}

    			$previousValue =  $this->db->select('*')->from('tOrderPayments')->where('PaymentsUID',$paymentsmanualrow->PaymentsUID)->get()->row();

    		}

    		$this->common_model->updatecustomerfee_payments($OrderUID);


			//decline all previous approvals
    		$this->common_model->decline_customerapprovals($OrderUID);

    	}
    	if($this->db->affected_rows() > 0)
    	{
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }

    function BilledStatus($OrderUID)
    {
    	$this->db->select('*');
    	$this->db->from('torders');
    	$this->db->where(array("OrderUID" =>$OrderUID,"IsBilled"=>1));
    	$query = $this->db->get();
    	if($query->num_rows() > 0)
    	{
    		return true;
    	}
    	else{
    		return false;
    	}
    }

    function addoptionalworkflowfororder($WorkflowModules, $OrderUID, $torders)
    {
    	$data['OrderUID']=$OrderUID;
    	$data['SubProductUID']=$torders->SubProductUID;
    	$data['CreatedBy']=$this->loggedid;
    	$data['CreatedOn']=date('Y-m-d H:i:s');

    	foreach ($WorkflowModules as $key => $value) {
    		$data['WorkflowModuleUID']=$value;
    		$this->db->insert('torderoptionalworkflows', $data);
    	}
    	return true;
    }

    function GetStewartApiRequest(){

    	$this->db->select('*');
    	$this->db->from('mApiRequest');
    	$this->db->where('RequestType','Additional Information');
    	return $this->db->get()->result();
    }


    function get_customerpricingdatas_frompayments($OrderUID){
    	$query = $this->db->query("SELECT SUM(CONCAT(`tOrderPayments`.`OperatorType`, '', `tOrderPayments`.`CustomerAdditionalAmount`)) AS CustomerAdditionalAmount, torders.CustomerAmount,torders.AgentPricing AS AgentPricing, torders.UnderWritingPricing AS UnderWritingPricing,torders.CustomerActualAmount  FROM torders LEFT JOIN tOrderPayments ON torders.OrderUID  = tOrderPayments.OrderUID WHERE torders.OrderUID = '".$OrderUID."' ApprovalFunction IN (".$this->config->item('pricing_customer_Approvalfunctionall').")");
    	return $query->row();
    }

    function get_abstractorpricingdatas_frompayments($OrderUID){
    	$query = $this->db->query("SELECT SUM(CONCAT(`tOrderPayments`.`OperatorType`, '', `tOrderPayments`.`AbstractorAdditionalFee`)) AS AbstractorAdditionalFee, torders.AbstractorFee,'' AS AgentPricing, '' AS UnderWritingPricing,torders.AbstractorCopyCost AS AbstractorCopyCost,torders.AbstractorActualFee FROM torders LEFT JOIN tOrderPayments ON torders.OrderUID  = tOrderPayments.OrderUID JOIN torderabstractor ON torderabstractor.AbstractorOrderUID = tOrderPayments.AbstractorOrderUID WHERE torders.OrderUID = '".$OrderUID."' AND ApprovalFunction IN ('AbstractorActualPricing','AbstractorPricingAdjustments','AbstractorPricingOverride') ");
    	return $query->row();
    }

    function get_parentpartnerdetails()
    {
    	$this->db->select('*');	
    	$this->db->from('mPartner');
    	$this->db->where(array('IsParentCompany'=>1,'Active'=>1));   
    	return $this->db->get()->result();
    }
    function get_torderimportdata($OrderUID)
    {
    	$this->db->select('*')->from('tOrderImport');
    	$this->db->where('OrderUID',$OrderUID);
    	return $this->db->get()->row();
    }

    public function get_allpartners()
    {
    	$this->db->select('mPartner.PartnerUID,mPartner.PartnerCompanyName');
    	$this->db->from('mPartner');
    	$this->db->group_by('mPartner.PartnerUID'); 
    	$query = $this->db->get();
    	return $query->result();
    }

    public function Update_OrderHeaderdetails($OrderUID,$PriorityID,$OrderTypeID)
    {
    	if ($PriorityID) {
    		$PriorityDet = array('PriorityUID' => $PriorityID);
    		$this->db->where('OrderUID',$OrderUID);
    		$this->db->update('torders',$PriorityDet);
    	}
    	else{
    		$OrderTypeDet = array('OrderTypeUID' => $OrderTypeID);
    		$this->db->where('OrderUID',$OrderUID);
    		$this->db->update('torders',$OrderTypeDet);
    	}
    	if ($this->db->affected_rows() > 0 ) {
    		return 1;
    	}
    	else {
    		return 0;
    	}
    }

    /* D-2-T23 Parner or Inhouse autopopulate feature */
    function Insert_tOrderImport($data,$OrderUID ='') {
    	if(!empty($OrderUID)) {
			//timport data
    		$updatetimport = new Stdclass();
    		$updatetimport->OrderUID = $OrderUID;
    		$updatetimport->Investor = isset($data['Investor']) ? $data['Investor'] : null;
    		$updatetimport->MCAPercentage = isset($data['MCAPercentage']) ? $data['MCAPercentage'] : null;
    		$updatetimport->MCAAmount = isset($data['MCAAmount']) ? (float) str_replace(',', '', $data['MCAAmount']) : 0.00;
    		$updatetimport->MCABuckets = 	isset($data['MCABuckets']) ? $data['MCABuckets'] : null;
    		$updatetimport->Agent = isset($data['Agent']) ? $data['Agent'] : null;
    		$updatetimport->TitleCompany = isset($data['TitleCompany']) ? $data['TitleCompany'] : null;
    		$updatetimport->TitleUnderwriter = isset($data['TitleUnderwriter']) ? $data['TitleUnderwriter'] : null;
    		$updatetimport->PolicyNumber = isset($data['PolicyNumber']) ? $data['PolicyNumber'] : null;
    		$updatetimport->Issuer = isset($data['Issuer']) ? $data['Issuer'] : null;
    		$updatetimport->ClientKickBackComments = isset($data['ClientKickBackComments']) ? $data['ClientKickBackComments'] : null;
    		$updatetimport->SearchOrderedDate = isset($data['SearchOrderedDate']) && !empty($data['SearchOrderedDate']) ? Date('Y-m-d',strtotime($data['SearchOrderedDate'])) : null;
    		$updatetimport->PolicyApproveDate = isset($data['PolicyApproveDate']) && !empty($data['PolicyApproveDate'])  ? Date('Y-m-d',strtotime($data['PolicyApproveDate'])) : null;
    		$updatetimport->ClientKickBackDate = isset($data['ClientKickBackDate']) && !empty($data['ClientKickBackDate']) ? Date('Y-m-d',strtotime($data['ClientKickBackDate'])) : null;
    		$updatetimport->FHA = isset($data['FHA']) && !empty($data['FHA']) ? $data['FHA'] : null;
    		$this->db->where('OrderUID',$OrderUID);
    		$this->db->where('OrderUID',$OrderUID);    
    		$timportquery = $this->db->get('tOrderImport');
    		if ( $timportquery->num_rows() > 0 )
    		{
    			$this->db->where('OrderUID',$OrderUID);    
    			$this->db->update('tOrderImport',$updatetimport);
    		} else {
    			$this->db->insert('tOrderImport',$updatetimport);
    		}
    	}
    }

    /* @purpose: To Update Additional Info @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: May 27th 2020 */
    function InsertAdditionalInformation($data,$OrderUID ='') {
    	if(!empty($OrderUID)) {			
    		$tApiOrders = $this->db->query('SELECT * FROM tApiOrders WHERE OrderUID='.$OrderUID.'')->row();
    		$OrderRequestUID = $tApiOrders->OrderRequestUID;

    		$updateAdditional = new Stdclass();			
    		$updateAdditional->EstateInterestUID = isset($data['EstateInterestUID']) ? $data['EstateInterestUID'] : null;
    		$updateAdditional->PropertyEstimatedValue = isset($data['PropertyEstimatedValue']) ? $data['PropertyEstimatedValue'] : null;
    		$updateAdditional->CountyUID = isset($data['CountyUID']) ? $data['CountyUID'] : null;
    		$updateAdditional->UnitCount = isset($data['UnitCount']) ? $data['UnitCount'] : null;
    		$updateAdditional->GovernmentLoanType = isset($data['GovernmentLoanType']) ? $data['GovernmentLoanType'] : null;
    		$updateAdditional->BorrowerCount = 	isset($data['BorrowerCount']) ? $data['BorrowerCount'] : null;
    		$updateAdditional->LoanIdentifierType = 	isset($data['LoanIdentifierType']) ? $data['LoanIdentifierType'] : null;
    		$updateAdditional->LoanMaturityPeriod = 	isset($data['LoanMaturityPeriod']) ? $data['LoanMaturityPeriod'] : null;
    		$updateAdditional->LoanPurpose = 	isset($data['LoanPurpose']) ? $data['LoanPurpose'] : null;
    		$updateAdditional->MortgageType = 	isset($data['MortgageType']) ? $data['MortgageType'] : null;
    		$updateAdditional->MortgageTypeOther = 	isset($data['MortgageTypeOther']) ? $data['MortgageTypeOther'] : null;

    		/* Tinyint Fields */
    		$updateAdditional->PUDIndicator = isset($data['PUDIndicator']) ? 1 : 0;
    		$updateAdditional->EmployeeLoanIndicator = isset($data['EmployeeLoanIndicator']) ? 1 : 0;
    		$updateAdditional->PiggyBackLoanIndicator = isset($data['PiggyBackLoanIndicator']) ? 1 : 0;

    		/* Decimal Fields */
    		$updateAdditional->APRPercentage = isset($data['APRPercentage']) ? (float) str_replace(',', '', $data['APRPercentage']) : 0.00;
    		$updateAdditional->InvestorLoanAmount = isset($data['InvestorLoanAmount']) ? (float) str_replace(',', '', $data['InvestorLoanAmount']) : 0.00;
    		$updateAdditional->LTVRatio = isset($data['LTVRatio']) ? (float) str_replace(',', '', $data['LTVRatio']) : 0.00;
    		$updateAdditional->InterestAndMIMonthlyPaymentAmt = isset($data['InterestAndMIMonthlyPaymentAmt']) ? (float) str_replace(',', '', $data['InterestAndMIMonthlyPaymentAmt']) : 0.00;
    		$updateAdditional->BaseLoanAmount = isset($data['BaseLoanAmount']) ? (float) str_replace(',', '', $data['BaseLoanAmount']) : 0.00;

    		/* Date Fields */
    		$updateAdditional->ApplicationDate = isset($data['ApplicationDate']) && !empty($data['ApplicationDate']) ? Date('Y-m-d',strtotime($data['ApplicationDate'])) : null;

    		$this->db->where('OrderUID',$OrderUID);
    		$tOrderAdditionalInfo = $this->db->get('tOrderAdditionalInfo');
    		if ( $tOrderAdditionalInfo->num_rows() > 0 )
    		{
    			$this->db->where('OrderUID',$OrderUID);    
    			$this->db->update('tOrderAdditionalInfo',$updateAdditional);
    		} else {
    			$updateAdditional->OrderUID = $OrderUID;
    			$updateAdditional->OrderRequestUID = $OrderRequestUID;
    			$this->db->insert('tOrderAdditionalInfo',$updateAdditional);
    		}
    	}
    }

    function getCustomerSchedule($torders)
    {
    	$this->db->select('mCustomerBranches.BranchName,mCustomerBranches.BranchUID,mstates.StateCode');
    	$this->db->from('mCustomerBranches');
    	$this->db->join('mcities','mcities.CityUID = mCustomerBranches.CityUID','left');
    	$this->db->join('mstates','mstates.StateUID = mCustomerBranches.StateUID','left');
    	$this->db->join('mcounties','mcounties.CountyUID = mCustomerBranches.CountyUID','left');
    	$this->db->where(array('mCustomerBranches.ZipCode'=>$torders->PropertyZipcode,'mcities.CityName'=>$torders->PropertyCityName,'mstates.StateCode'=>$torders->PropertyStateCode,'mcounties.CountyName'=>$torders->PropertyCountyName));
    	$this->db->where('mCustomerBranches.CustomerUID',$torders->CustomerUID);
    	$this->db->where('mCustomerBranches.Active',1);
    	return $this->db->get()->result();
    }

	/* 
	 * Function    : Get Request
     * Description : To get transactionID and InBoundUID if it is API Order
     * Date        : 21/12/2019
     * Author      : D.Samuel Prabhu
     * @Parameter  : OrderUID Int 
     *
	*/


	function getApiOrderDetail($OrderUID)
	{
		$this->db->select("torders.OrderUID, torders.OrderNumber, tApiOrders.InBoundUID, tApiOrders.TransactionID");
		$this->db->from('torders');
		$this->db->join ( 'tApiOrders', 'tApiOrders.OrderUID = torders.OrderUID' , 'left' );
		$this->db->join ( 'tApiOutBoundOrders', 'tApiOutBoundOrders.OrderUID = torders.OrderUID' , 'left' );
		$this->db->where(array("torders.OrderUID"=>$OrderUID));  
		$query = $this->db->get();

		return $query->row();

	}

	function GetX1Order($OrderUID) 
	{
		$torders=$this->db->get_where('torders', array('OrderUID'=>$OrderUID))->row();

		$mstates=$this->db->get_where('mstates', array('StateCode' => $torders->PropertyStateCode))->row();

		$mcounties=$this->db->get_where('mcounties', array('StateUID'=>$mstates->StateUID, 'CountyName'=>$torders->PropertyCountyName))->row();

		if(!empty($mcounties))
		{
			$query = $this->db->query("SELECT *, CASE WHEN msearchmodes.SearchModeUID = '6' THEN 
				mcountysearchmodes.WebsiteURL ELSE msearchmodes.SearchSiteURL END AS SiteURL 
				FROM mcountysearchmodes
				LEFT JOIN msearchmodes ON mcountysearchmodes.SearchModeUID = msearchmodes.SearchModeUID 
				WHERE mcountysearchmodes.CountyUID = '". $mcounties->CountyUID ."'and msearchmodes.SearchSiteURL = 'X1' AND msearchmodes.SearchModeUID <> 5
				Order By FIELD(SearchModeName, 'Free', 'Paid', 'Others', 'Abstractor')");

			$data = $query->row();
		}
		return $data;
	}

	function CheckX1OrderInMyapproval($OrderUID)
	{
		$ApiOutboundStatus = array('50','100');
		$this->db->select('*');
		$this->db->from('tOrderEventLog');
		$this->db->join('tApiOutBoundOrders','tApiOutBoundOrders.ApiOutBoundOrderUID = tApiOutBoundOrders.ApiOutBoundOrderUID');
		$this->db->join('torderapprovals','torderapprovals.ApiOutBoundOrderUID = tOrderEventLog.ApiOutBoundOrderUID','left');
		$this->db->join('torders','torders.OrderUID = tApiOutBoundOrders.OrderUID','left');
		$this->db->where('tOrderEventLog.OrderUID',$OrderUID);
		$this->db->where('tApiOutBoundOrders.Status', 'Accepted');
		$this->db->where_not_in('tApiOutBoundOrders.ApiOutboundStatus', $ApiOutboundStatus);
		$this->db->where('torderapprovals.ApprovalStatus', 0);
		$this->db->where('torders.OrderUID',$OrderUID);
		$this->db->group_by('tApiOutBoundOrders.ApiOutBoundOrderUID');
		$tOrderEventLog = $this->db->get()->RESULT();
		return $tOrderEventLog;
	}

	/**
	* @purpose : To get api title order details
	* @param $OrderUID
	* 
	* @throws no exception
	* @author D.Samuel Prabhu <samuel.prabhu@avanzegroup.com>
	* @return order details as object or empty 
	* @since 21 July 2020 
	* 
	**/

	function getTitleOrderDetail($OrderUID)
	{
		$this->db->select('torders.OrderUID, torders.OrderNumber, torders.AltORderNumber, torders.CustomerRefNum, torders.APIOrder');
		$this->db->select(' mproducts.ProductCode,  mproducts.ProductName,  mproducts.IsTitleProduct,  mproducts.IsClosingProduct, msubproducts.SubProductName');
		$this->db->from('torders');
		$this->db->join('mordertypes','mordertypes.OrderTypeUID=torders.OrderTypeUID', 'left');
		$this->db->join('msubproducts','msubproducts.SubProductUID=torders.SubProductUID', 'left');
		$this->db->join ( 'mproducts', 'msubproducts.ProductUID = mproducts.ProductUID' , 'left' );

		$whereCondition = array('torders.OrderUID' => $OrderUID, 'torders.APIOrder' => 1); 

		$this->db->where($whereCondition); 
		$query = $this->db->get();
		return $query->row();
	}	

	/**
	* @purpose : To get api closing order details by customer reference number
	* @param $CustomerRefNum as string
	* 
	* @throws no exception
	* @author D.Samuel Prabhu <samuel.prabhu@avanzegroup.com>
	* @return order details as object or empty 
	* @since 21 July 2020 
	* 
	**/
	function getOrdersByCustomerRefNum($CustomerRefNum)
	{
		$this->db->select('torders.OrderUID, torders.OrderNumber, torders.AltORderNumber, torders.CustomerRefNum, torders.APIOrder');
		$this->db->select(' mproducts.ProductCode,  mproducts.ProductName,  mproducts.IsTitleProduct,  mproducts.IsClosingProduct, msubproducts.SubProductName');
		$this->db->from('torders');
		$this->db->join('mordertypes','mordertypes.OrderTypeUID=torders.OrderTypeUID', 'left');
		$this->db->join('msubproducts','msubproducts.SubProductUID=torders.SubProductUID', 'left');
		$this->db->join ( 'mproducts', 'msubproducts.ProductUID = mproducts.ProductUID' , 'left' );

		$whereCondition = array('torders.CustomerRefNum' => $CustomerRefNum, 'torders.APIOrder' => 1);

		$this->db->where($whereCondition); 
		
		$query = $this->db->get();
		return $query->result();
	}


	/**
	* @purpose : To update alternate number for order by OrderUID
	* @param $OrderUID as string
	* 
	* @throws no exception
	* @author D.Samuel Prabhu <samuel.prabhu@avanzegroup.com>
	* @return true/false as bool
	* @since 21 July 2020 
	* 
	**/

	function updateAltOrderNumber($OrderUID,$AltOrderNumber)
	{
		$this->db->trans_begin();//begins your transaction
		$this->db->set('AltORderNumber', $AltOrderNumber);
		$this->db->where('OrderUID', $OrderUID);//where condition
		$this->db->update('torders');//update query

        //checks transaction status
		if ($this->db->trans_status() === FALSE)
		{ 
		    $this->db->trans_rollback();//if update fails rollback and  return false
		    return FALSE;

		}
		else
		{
	        $this->db->trans_commit();//if success commit transaction and returns true
	        return TRUE;
	    }
	}

    /**
	* @purpose : To update Order Policy for order by OrderUID
	* @param $OrderUID as string
	* @param $Data as array
	* 
	* @throws no exception
	* @author D.Samuel Prabhu <samuel.prabhu@avanzegroup.com>
	* @return true/false as bool
	* @since 21 July 2020 
	* 
	**/

	function UpdateOrderDetail($OrderUID,$Data)
	{
		$this->db->trans_begin();//begins your transaction

		$this->db->where('OrderUID', $OrderUID);//where condition
		$this->db->update('torders', $Data);//update query

        //checks transaction status
		if ($this->db->trans_status() === FALSE)
		{ 
		    $this->db->trans_rollback();//if update fails rollback and  return false
		    return FALSE;

		}
		else
		{
	        $this->db->trans_commit();//if success commit transaction and returns true
	        return TRUE;
	    }
	}

}?>
