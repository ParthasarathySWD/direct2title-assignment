<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Common_integration_model extends CI_Model {
	
	function __construct() {
		parent::__construct();
		$this->load->config('keywords');
		$this->load->model('common_model');
	}

	// ( Curl Function ) Send Post Data to API Server Starts//
	function sendPostData($url, $post) {
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
				"authorization: isgn",
				"cache-control: no-cache",
				"content-type: application/json",
			),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		if ($err) {
			return false;
		} else {

			/* @purpose: Queue Management for closing order @author: Yagavi.G <yagavi.g@avanzegroup.com> @since: June 15th 2020 */
			$list = json_decode($post,true);
			$OrderUID = $list['OrderUID'];
			$EventCode = $list['EventCode'];
			$OrderSourceUID = $list['OrderSourceUID'];
			$ScheduleUID = $list['ScheduleUID'];
			$this->load->model('api/api_model');
			$fieldArray['StatusUID'] = $EventCode;
			$fieldArray['ScheduleUID'] = $ScheduleUID;
			$fieldArray['EventType'] = 'Outbound';
			$fieldArray['Comment'] = '';
			$this->api_model->ClosingQueueManagement($OrderUID,$fieldArray,$OrderSourceUID);
			
			return true;
		}
	}

	function clean($string) {
		$find = array("&","<",">");
		$replace = array("&amp;","&lt;","&gt;");
		$res = htmlspecialchars($string);
		$str = html_entity_decode($res);
		$html_str = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $string);
		return $html_str;
	}

	function GetNoteTypeUID($SectionName)	{
		$this->db->select("*");
		$this->db->from('mreportsections');
		$this->db->where(array("mreportsections.SectionName"=>$SectionName));
		$query = $this->db->get();
		return $query->row();
	}

	function GetAdverseConditionDetails($OrderUID){
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

	function GetLegalDescription($OrderUID)	{
		$this->db->where('OrderUID',$OrderUID);
		$q = $this->db->get('torderlegaldescription')->row();
		return $q->LegalDescription;
	}

	function GetLiensDetails($OrderUID)	{
		$this->db->select("*");
		$this->db->from('torderleins');
		$this->db->join('mdocumenttypes','torderleins.DocumentTypeUID=mdocumenttypes.DocumentTypeUID','left');
		$this->db->join('mmortgagedbvtypes','torderleins.Lien_DBVTypeUID_1=mmortgagedbvtypes.DBVTypeUID','left');
		$this->db->where(array("torderleins.OrderUID"=>$OrderUID));    
		$query = $this->db->get();
		return $query->result_array();
	}

	function GetJudgementDetails($OrderUID)	{
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

	function GetPropertyDetails($OrderUID)	{
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

	function GetAssessmentDetails($OrderUID)	{
		$this->db->select("*");
		$this->db->from('torderassessment');
		$this->db->where(array("torderassessment.OrderUID"=>$OrderUID));
		$query = $this->db->get();
		return $query->row();
	}

	/*Get Order Details Starts */

	function GetOrderDetails($OrderUID)	{
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

	/*Get Order Details Ends */

	/* Get DeedList Details Starts */

	function GetDeedListDetails($OrderUID)	{
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

	function GetDeedDetails($OrderUID)	{
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

	function get_partytypes($OrderUID = '',$PartyTypeUID = '',$DeedSNo = '')	{
		$this->db->select("*"); 
		$this->db->from('torderdeedparties');
		$this->db->where(array("torderdeedparties.OrderUID"=>$OrderUID,"PartyTypeUID"=>$PartyTypeUID,"DeedSNo"=>$DeedSNo));
		$query = $this->db->get();
		return $query->result();	
	}

	/* Get DeedList Details Ends */

	/*Get MortgageList Details Starts*/

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

	function GetMortgageDetails($OrderUID)	{
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

	function GetPropertyRoles($OrderUID)	{
		$this->db->select("*");
		$this->db->from('torderpropertyroles');
		$this->db->where(array("torderpropertyroles.OrderUID"=>$OrderUID));       
		$query = $this->db->get();
		return $query->result();
	}	

	function get_partytypes_mort($OrderUID = '',$PartyTypeUID = '',$MortgageSNo = '')	{
		$this->db->select("*"); 
		$this->db->from('tordermortgageparties');
		$this->db->where(array("tordermortgageparties.OrderUID"=>$OrderUID,"PartyTypeUID"=>$PartyTypeUID,"MortgageSNo"=>$MortgageSNo));
		$query = $this->db->get();
		return $query->result();	
	}

	/*Get MortgageList Details Ends*/

	/*Get TaxList Details Starts*/

	function GetTaxListDetails($OrderUID)	{
		$Mortgages = $this->config->item('DocumentTypeUID')['Mortgages'];
		$this->db->select("*");
		$this->db->from('tordertaxcerts');
		$this->db->join('msubdocumentmortgages', 'tordertaxcerts.DocumentTypeUID=msubdocumentmortgages.DocumentTypeUID', 'left');
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

	/*Get TaxList Details Ends*/

	/*Get Judgement Details Starts*/

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

	function GetJudgmentDetails($OrderUID)	{
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

	function get_partytypes_judg($OrderUID = '',$PartyTypeUID = '',$JudgementSNo = '')	{
		$this->db->select("*"); 
		$this->db->from('torderjudgementparties');
		$this->db->where(array("torderjudgementparties.OrderUID"=>$OrderUID,"PartyTypeUID"=>$PartyTypeUID,"JudgementSNo"=>$JudgementSNo));
		$query = $this->db->get();
		return $query->result();	
	}

	/*Get Judgement Details Ends*/


	function GetAttachmentToAPI($OrderUID,$DocFileName)	{
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

	function GetFinalReportToAPI($OrderUID)	{
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
	
	/*Checking whether the order is API Order or Not */
 
	function CheckApiOrders($OrderUID){
		$this->db->select("*"); 
		$this->db->from('torders');
		$this->db->where(array("torders.APIOrder"=>1,"torders.OrderUID"=>$OrderUID));
		$query = $this->db->get();
		$Is_Api = $query->row();

		return $Is_Api;
	}

	function GetInBoundTransactionDetails($OrderUID){
		$this->db->select("*"); 
		$this->db->from('tApiOrders');
		$this->db->where(array("tApiOrders.OrderUID"=>$OrderUID));
		$query = $this->db->get();
		$data = $query->row();

		return $data;
	}

	function GetSourceName($OrderUID)	{
		$this->db->select("*");
		$this->db->from('torders');
		$this->db->join('mApiTitlePlatform','torders.OrderSourceUID=mApiTitlePlatform.OrderSourceUID','left');
		$this->db->where(array("torders.OrderUID"=>$OrderUID));       
		$query = $this->db->get();
		return $query->row();
	}	

	function Gettaxexemptionbyid($TaxCertSNo)	{
		$this->db->select("*");
	    $this->db->from('tordertaxexemptions');    
	    $this->db->join('mtaxexemptions','tordertaxexemptions.TaxExemptionUID=mtaxexemptions.TaxExemptionUID','left');
	    $this->db->where(array("tordertaxexemptions.TaxCertSNo"=> $TaxCertSNo));
	    $query = $this->db->get();
	    return $query->result_array();
	}	

	function GetOverAllOrderDetailsByOrderUID($post){
		$OrderUID = $post['OrderUID'];
		$EventCode = $post['EventCode'];
		$DocumentFileName = $post['documentfilenames'];
		$DeliveryNotes = $post['DeliveryNotes'];
		$TotalFiles = $post['TotalFiles'];
		if($OrderUID){
			$OrderDetails = $this->GetOrderDetails($OrderUID);
			$DeedListDetails = $this->GetDeedListDetails($OrderUID);   
			$TrustorDetails = $this->GetPropertyRoles($OrderUID);
			$AdverseConditions = $this->GetAdverseConditionDetails($OrderUID);

			$FinalMortgageListDetails = [];
			$MortgageListDetails = $this->GetMortgageListDetails($OrderUID); 

			foreach ($MortgageListDetails as $key => $values) { 

				$MortgageSNo = $values->MortgageSNo;

				$MortgageAssigneeDetails = $this->GetMortgageAssigneeDetails($OrderUID,$MortgageSNo); 
				$FinalMortgageAssigneeDetails = [];

				foreach ($MortgageAssigneeDetails as $ele => $row) {
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
			if($Legal!='') {
				$Legaldes['Description'] = $this->clean($Legal);
			}

			$Liens = $this->GetLiensDetails($OrderUID);  
			$PropertyInfo = $this->GetPropertyDetails($OrderUID);
			$Assessment = $this->GetAssessmentDetails($OrderUID);

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

			$OrderNumbers = $this->GetOrderDetails($OrderUID);
			$OrderNumber = $OrderNumbers->OrderNumber;

			$FinalProductDetails = $this->GetFinalReportToAPI($OrderUID);

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

				$AppSourceName = $this->GetSourceName($OrderUID); 
				$SourceName = trim($AppSourceName->OrderSourceName);

				$DocTypeCode = $OrderDocDetails->TypeOfDocument;
				$TypeOfDocument = $OrderDocDetails->TypeOfDocument;

				$DocumentTypeCode = $this->common_model->GetDocumentTypeCodeByUID($OrderDocDetails->TypeOfDocument);
				if(!empty($DocumentTypeCode)){
					$TypeOfDocument = $DocumentTypeCode;
				} else {
					$DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $CreatedByAPI);
					$TypeOfDocument = $this->common_model->GetDocumentTypeCodeByUID($DocumentTypeUID);
				}

				if($SourceName=='Keystone') {
					$KeystoneDocTypes = $this->config->item('KeystoneDocTypes');
					if(!empty($KeystoneDocTypes)){
						$TypeOfDocument = $KeystoneDocTypes[$TypeOfDocument];
					} else {
						$DocumentTypeUID = $this->common_model->GetDocumentTypeUIDByCode_Integration('Other', $CreatedByAPI);
						$TypeOfDocument = $this->common_model->GetDocumentTypeCodeByUID($DocumentTypeUID);
					}
				}

				$obj= [];
				$obj['OrderDocsPath']=$OrderDocsPath;
				$obj['DocumentFileName']=$FileName;
				$obj['TemplateName']=$TemplateName;
				$obj['DocumentCreatedDate']= Date('Y/m/d H:i:s',strtotime($DocumentCreatedDate));
				$obj['Content']=$Content;
				$obj['DocTypeCode']=$TypeOfDocument;
				$obj['DocTypeName']=$TypeOfDocument;

				array_push($Documents, $obj);
				unset($obj);
			}

			$orderdetails_array = array(
				'OrderDetails' => $OrderDetails,
				'DeedListDetails' => $DeedListDetails,
				'MortgageListDetails' => $FinalMortgageListDetails,
				'TaxListDetails' => $FinalTaxListDetails,
				'LegalDetails' => $Legaldes,
				'LiensDetails' => $Liens,
				'JudgementDetails' => $FinalJudgmentListDetails,
				'AssessmentDetails' => $Assessment,
				'TrustorDetails' => $TrustorDetails,
				'PropertyInfoDetails' => $PropertyInfo,
				'AdverseConditions' => $AdverseConditions,
				'Documents' => $Documents
			);
			return $orderdetails_array;
		} else {
			return false;
		}
	}
}
?>
