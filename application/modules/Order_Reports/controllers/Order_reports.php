<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
class Order_reports extends MY_Controller {
function __construct()
    {
        parent::__construct();
		error_reporting(0);
		$this->load->library('USPS');
		$this->load->model('Common_model');
        $this->load->model('Order_reports_model');
        // $this->load->model('Address/Address_model');
        $this->load->model('Check_address_variance_model');
        $this->load->model('Stewart_model');
		// $this->load->model('Order_entry/Order_entry_model');
		// $this->load->model('Order_entry/Orderentry_model');
		// $this->lang->load('keywords');
		// $this->load->model('Common_model'); 
		// date_default_timezone_set('US/Eastern');
		// $this->load->model('real_ec_model');

		// $this->load->model('Login/Login_model');
		// $this->load->model('Order_reports_model');

		// ob_clean();
  //       ob_start(); 
  //       $this->isnotattachment=true;

  //       $this->load->model('stewart_model');
  //       $this->load->library('upload');
  //       $this->load->library("PDFMerger1");
  //       $this->load->helper('download');
  //       $this->load->helper('url');
  //       $this->load->config('keywords');
  //       $this->load->library('session');
  //       $this->load->library(array('form_validation'));
  //       $this->load->helper(array('form', 'url'));
    }  

	
	public function index()
	{
		// $data['content'] = 'index';
		// $this->load->view('page', $data);	
		if(isset($_GET['OrderUID']))
        {
            $OrderUID = $_GET['OrderUID'];

            $OrderID = $this->Common_model->GetOrderByID($OrderUID);

            $OrderNumber = $OrderID->OrderUID;

            if($OrderNumber == 0)
            {
                redirect(base_url().'my_orders');
            }

            $UserUID = $this->session->userdata('UserUID');

            $OrderrUID = $_GET['OrderUID'];

            $OrderUID = str_replace('/', '', $OrderrUID);

            $data['OrderUID'] = $OrderUID;

            $UserUID = $this->session->userdata('UserUID');


            $data['content'] = 'index';
            $data['Action']="ADD";
            $data['CaseSen'] = $this->Common_model->get_CaseSen($OrderUID)->CaseSensitivity;
            $data['data']=array('menu'=>'Report','title'=>'Report','link'=>array('Report'));
            // $data['output'] = $this->report_view();
            // print_r($data['output']);exit;

            $data['TopBarDetails']= $this->Common_model->GetTopBarDetailsByID($OrderUID,$UserUID);
            $data['TopBarDetails1']= $this->Common_model->GetTopBarDetails($OrderUID);
            $data['CompleteDetails1']= $this->Common_model->GetCompleteDetails($OrderUID);
            $data['CompleteDetails']= $this->Common_model->GetCompleteDetailsByID($OrderUID,$UserUID);

            $data['order_details'] = $this->Common_model->get_orderdetails($OrderUID);

            $data['Documents']=$this->Order_reports_model->GetDocuments($OrderUID);



            $data['Name'] = $this->UserName;
            $this->load->view('page', $data);
        }
        else
        {
            redirect(base_url().'my_orders');   
        }
	}
  function Preview()
    {
        error_reporting(0);
        $OrderUID = $this->input->get('OrderUID');

        $order_details = $this->Order_reports_model->get_torders($OrderUID);
        $SavePreviewOrderNumber = $order_details->OrderNumber;
        $order_details->TemplateUID;
        if($order_details->TemplateUID == 1 || $order_details->TemplateUID == 2 )
        {
            $doc = $this->isgn_property_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 2)
        {
            $doc = $this->limited_title_search_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 3)
        {
            $doc = $this->ez_close_deed_report($OrderUID);

        }
        elseif($order_details->TemplateUID == 4)
        {
            $doc = $this->ez_close_property_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 5)
        {
            $doc = $this->real_estate_property_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 20)
        {
            $doc = $this->isgn_property_report_stewart($OrderUID);
        }
        else
        {
            //$doc = $this->sbi_report($OrderUID);
            $doc = $this->isgn_property_report($OrderUID);
        }

        $this->load->library('pdf');
        $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        unset($pdf);
        $pdf = $this->pdf->load($param);       
        $pdf->SetProtection(array('copy','print','modify','annot-forms','fill-forms','extract','assemble','print-highres'));
        $pdf->packTableData = true;
        $pdf->shrink_tables_to_fit = 0; 
        // $pdf->list_indent_first_level = 0; 
        $html = mb_convert_encoding($doc, 'UTF-8', 'UTF-8');
        $pdf->WriteHTML($html); // write the HTML into the PDF
        $page_count = $pdf->page;
        $doc_save = $SavePreviewOrderNumber.'.pdf';
        $pdf->Output($doc_save, '');
        $dir = FCPATH.'Templates/Pdf/'.$SavePreviewOrderNumber.'.pdf';
        file_put_contents($dir,file_get_contents($doc_save));
        unlink($doc_save);
    }

	

   
    function report_view($OrderUID)
    {
        error_reporting(0);
        // $OrderUID = $_POST['OrderUID'];
        $order_details = $this->Order_reports_model->get_torders($OrderUID);
        $LoanNumber = $order_details->LoanNumber;
        $BorrowerName = $order_details->ReportBorrowerName;
        $ReportName = $LoanNumber.'-'.$BorrowerName;
        if($order_details->TemplateUID == 1 || $order_details->TemplateUID == 2)
        {
            $doc = $this->isgn_property_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 2)
        {
            $doc = $this->limited_title_search_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 3)
        {
            $doc = $this->ez_close_deed_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 4)
        {
            $doc = $this->ez_close_property_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 5)
        {
            $doc = $this->real_estate_property_report($OrderUID);
        }
        else
        {
            //$doc = $this->sbi_report($OrderUID);
            $doc = $this->isgn_property_report($OrderUID);
        }

        $this->load->library('pdf');
        //$data = $this->input->post('tinyymce');
        //echo "<script>console.log(".$data.")</script>";
        //exit;
        $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        // ob_start();
        //$data = file_get_contents( FCPATH.'/uploads/Templates/isgn_report.php' ) ;
        // ob_get_clean();
        // $data = $this->load->view('isgn', $data, true); // render the view into HTML
        // $this->load->library('pdf');
        $pdf->packTableData = true;
        $pdf->shrink_tables_to_fit = 1;
        $pdf = $this->pdf->load($param);
        $pdf->SetProtection(array('copy','print','modify','annot-forms','fill-forms','extract','assemble','print-highres'));
        //$pdf->SetDisplayMode('fullpage');
        // $pdf->SetDisplayMode('fullpage');
        // $pdf->list_indent_first_level = 0;  

        //echo $doc;exit;
        $html = mb_convert_encoding($doc, 'UTF-8', 'UTF-8');
        // echo $html;exit;
        // $pdf->SetFooter($_SERVER['HTTP_HOST'].'|{PAGENO}|'.date(DATE_RFC822)); // Add a footer for good measure ;)
        $pdf->WriteHTML($html); // write the HTML into the PDF
        $doc_save = $ReportName.'.pdf';
        ob_end_clean();
        $pdf->Output($doc_save, 'D');
    }

    function save_view($OrderUID)
    {
        try {
            
        error_reporting(E_ALL);
        $OrderUID = $OrderUID;
        $order_details = $this->Order_reports_model->get_torders($OrderUID);
        $SavePreviewOrderNumber = $order_details->OrderNumber;
        $order_details->TemplateUID;
        if($order_details->TemplateUID == 1)
        {
            $doc = $this->SeperateReport($OrderUID);
        }
        // echo $doc;
        $this->load->library('pdf');
        $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        unset($pdf);
        $pdf = $this->pdf->load($param);       
        $pdf->SetProtection(array('copy','print','modify','annot-forms','fill-forms','extract','assemble','print-highres'));
        $pdf->packTableData = true;
        $pdf->shrink_tables_to_fit = 0; 
        // $pdf->list_indent_first_level = 0; 
        $html = mb_convert_encoding($doc, 'UTF-8', 'UTF-8');
        $pdf->WriteHTML($html); // write the HTML into the PDF
        $page_count = $pdf->page;
        $doc_save = $SavePreviewOrderNumber.'.pdf';
        $pdf->Output($doc_save, '');
        $filepath = FCPATH.'Templates/Pdf/FinalTemplates/'.$SavePreviewOrderNumber.'.pdf';
        
        if(!file_exists(FCPATH.'Templates/Pdf/FinalTemplates/'))
        {
            if(!mkdir(FCPATH.'Templates/Pdf/FinalTemplates/')) die('Unable to Create Folder');
        }
        if(file_put_contents($filepath,file_get_contents($doc_save)))
        {
            // echo 'Saved';
        }
        } catch (Exception $e) {
            var_dump($e);
        }
        unlink($doc_save);
    }

    function SeperateReport($OrderUID)
    {
        error_reporting(0);
        $this->load->library('Dom/Simple_html_dom');
        $doc = new simple_html_dom();
        $filename = FCPATH.'Templates/isgnpropertyreport.php';
        $OrderUID = $OrderUID;
        $fp = fopen ( $filename, 'r' );
        //read our template into a variable
        $output = fread( $fp, filesize($filename));
        //Orders
        $torders_array = array();
        $keys = array();
        $values = array();
        $order_details = $this->Order_reports_model->get_torders($OrderUID);
        $ReportHeading = $order_details->ReportHeading;
        if($ReportHeading)
        {
            array_push($keys, '%%ReportHeading%%');
            array_push($values, $ReportHeading);
        }
        else{
            array_push($keys, '%%ReportHeading%%');
            array_push($values, 'Property Report');
        }
        $date = $order_details->OrderEntryDatetime;
        $OrderDate =  date('m/d/Y', strtotime($date));
        $dates = array('OrderDate'=>$OrderDate);
        foreach ($dates as $key => $value) 
        {

            array_push($keys, '%%'.$key.'%%');
            array_push($values, $value);
        }
        $CurrentDate = array('CurrentDate'=>date("m/d/Y"));
        foreach ($CurrentDate as $key => $value) 
        {

            array_push($keys, '%%'.$key.'%%');
            array_push($values, $value);
        }

        //Tax Certificate
        $TaxCertificateRequired = $order_details->TaxCertificateRequired;
        if($TaxCertificateRequired == 'Seperate Report' || $TaxCertificateRequired == 'No Tax Certificate')
        {
            array_push($keys, '%%TaxCert%%');
            array_push($values, ' ');
            array_push($keys, '%%TaxSection%%');
            array_push($values, ' ');
        }
        //Tax Certificate
        $base_url = FCPATH;
        $Attention =  $order_details->AttentionName;
        if($Attention)
        {
            $AttentionName = $Attention;
        }
        else{

            $AttentionName = '-';
        }
        $CustomerPContactMobileNo = $order_details->CustomerPContactMobileNo;
        $CustomerAddress1 = $order_details->CustomerAddress1;

        if($CustomerAddress1)
        {
            $CustomerAddress1 =  $order_details->CustomerAddress1;
        }
        else{
            $CustomerAddress1 = ' ';
        }
        $CustomerAddress2 = $order_details->CustomerAddress2;
        if($CustomerAddress2)
        {
            $CustomerAddress2 =  $order_details->CustomerAddress2.',';
        }
        else{
            $CustomerAddress2 = ' ';
        }
        $CustomerNumber = $order_details->CustomerNumber;
        if($CustomerNumber)
        {
            $CustomerNumber = $order_details->CustomerNumber.'/';
        }
        else{
            $CustomerNumber = '';
        }
        $CustomerName = $order_details->CustomerName;
        if($CustomerName)
        {
            $CustomerName = $order_details->CustomerName;
        }
        else{
            $CustomerName = ' ';
        }
        $CustomerPContactName =  $order_details->CustomerPContactName;
        if($CustomerPContactName)
        {
            $CustomerPContactName =  $order_details->CustomerPContactName;
        }
        else{
            $CustomerPContactName = ' ';
        }
        $CustomerStateCode =  $order_details->CustomerStateCode;
        if($CustomerStateCode)
        {
           $CustomerStateCode =  $order_details->CustomerStateCode.'-'; 
           $Customer_State_Code =  $order_details->CustomerStateCode; 
        }
        else
        {
            $CustomerStateCode =  ' '; 
        }

        $MiscellaneousNotes = $order_details->MiscellaneousNotes;
        if($MiscellaneousNotes)
        {
            $Miscellaneous = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Miscellaneous Information</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;'.$MiscellaneousNotes.' </td></tr></table>';
        }else{
            $Miscellaneous = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Miscellaneous Information</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;None </td></tr></table>';
        }
        
        $CustomerCountyName =  $order_details->CustomerCountyName;
        if($CustomerCountyName)
        {
           $CustomerCountyName =  $order_details->CustomerCountyName.',';
            $Customer_County_Name =  $order_details->CustomerCountyName;
        }
        else{
            $CustomerCountyName = '-';
        }
        $CustomerZipCode = $order_details->CustomerZipCode;
        if($CustomerZipCode)
        {
            $CustomerZipCode = $order_details->CustomerZipCode;
        }
        else{
            $CustomerZipCode = ' ';
        }
        $CustomerCityName = $order_details->CustomerCityName;
        if($CustomerCityName)
        {
             $CustomerCityName = $order_details->CustomerCityName.',';
        }
        else{
            $CustomerCityName = ' ';
        }
        $Mortgagee = $order_details->Mortgagee;
        $OrderNumber = $order_details->OrderNumber;
        $LoanNumber = $order_details->LoanNumber;
        if($LoanNumber)
        {
            $LoanNumber = $order_details->LoanNumber;
        }
        else{
            $LoanNumber = '-';
        }
        $OwnerName = $order_details->OwnerName;
        $CountyName =  $order_details->CountyName;
        $PropertyAddress1 = $order_details->PropertyAddress1;
        $PropertyAddress2 = $order_details->PropertyAddress2.',';
        $CityName = $order_details->CityName;
        $StateName = $order_details->StateName;
        $StateCode = $order_details->StateCode;
        if($StateCode)
        {
            $StateCode = $StateCode;
        }
        else{
            $StateCode = '-';
        }
        $DisclaimerResult = $this->Order_reports_model->get_DisclaimerNote($order_details->StateCode);
        if($DisclaimerResult->DisclaimerNote){
            $DisclaimerNote = $DisclaimerResult->DisclaimerNote;
        }
        else{
             $DisclaimerNote = '-';
        }
        if($DisclaimerResult->StateEmail){
            $DisclaimerStateEmail = $DisclaimerResult->StateEmail;
        }
        else{
            $DisclaimerStateEmail = '-';
        }
        if($DisclaimerResult->StateWebsite){
            $DisclaimerStateWebsite = $DisclaimerResult->StateWebsite;
        }
        else{
            $DisclaimerStateWebsite = '-';
        }
        if($DisclaimerResult->StatePhoneNumber){
            $DisclaimerStatePhoneNumber = $DisclaimerResult->StatePhoneNumber;
        }
        else{
            $DisclaimerStatePhoneNumber = '-';
        }
        if($DisclaimerResult->LegalDisclaimerNote == 1 && $DisclaimerResult->TaxDisclaimerNote == 1)
        {
            $taxpagebreak = '<div style="page-break-after: always;"></div>';
            $pagebreak =   '<div style="page-break-after: auto;"></div>';
            $LegalDisclaimerNote = 'displayfooter';
            $TaxDisclaimerNote = 'displayfooter';
        }
        elseif($DisclaimerResult->LegalDisclaimerNote == 0 && $DisclaimerResult->TaxDisclaimerNote == 1){

 
                $LegalDisclaimerNote = 'noheaderfooter';
                $TaxDisclaimerNote = ' ';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = ' ';
            
                             
        }
        elseif($DisclaimerResult->LegalDisclaimerNote == 1 && $DisclaimerResult->TaxDisclaimerNote == 0)
        {
                $LegalDisclaimerNote = 'displayfooter';
                $TaxDisclaimerNote = 'noheaderfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = '<div style="page-break-after: auto;"></div>';
        }
        else{
            $LegalDisclaimerNote = 'noheaderfooter';
                $TaxDisclaimerNote = 'noheaderfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = ' ';
        }
        $SearchDate = $order_details->SearchThroDate;
        if($SearchDate == '0000-00-00')
        {
            $SearchThroDate =  '-';
        }
        else{
            $SearchThroDate =  date('m/d/Y', strtotime($SearchDate));
        }
        $SearchFrom = $order_details->SearchFromDate;
        if($SearchFrom == '0000-00-00 00:00:00' || $SearchFrom == '')
            {
                $SearchFromDate = '-';
            }
            else
            {
                $SearchFromDate = date('m/d/Y', strtotime($SearchFrom));
            }
            $SearchAsOf = $order_details->SearchAsOfDate;
            if($SearchAsOf == '0000-00-00 00:00:00' || $SearchAsOf == '')
                {
                    $SearchAsOfDate = '-';
                }
                else
                {
                    $SearchAsOfDate = date('m/d/Y', strtotime($SearchAsOf));
                }
                $ZipCode = $order_details->PropertyZipcode;
                $address = $this->Order_reports_model->get_Address($OrderUID);
                foreach ($address as $key => $data) {

                    $AssessedCountyName = $data->AssessedCountyName;
                    $USPSCountyName = $data->USPSCountyName;
                }
                $ImageUrl = base_url().'assets/img/sourcepoint.png';
                $GoogleMapAddress = $PropertyAddress1.' '.$PropertyAddress2.' '.$CityName.' '.$StateName.' '.$ZipCode;
                $Mort = array('CustomerName'=>$CustomerNumber.$CustomerName,'AttentionName'=>$AttentionName,'CustomerAddress1'=>$CustomerAddress1,'CustomerAddress2'=>$CustomerAddress2,'CustomerCityName'=>$CustomerCityName,'CustomerCountyName'=>$CustomerCountyName,'CustomerStateCode'=>$CustomerStateCode,'CustomerPContactName'=>$CustomerPContactName,'OrderMortgagee'=>$Mortgagee,'Ordernumber'=>$OrderNumber,'Loannumber'=>$LoanNumber,'Ownername'=>$OwnerName,'Countyname'=>$CountyName,'County_name'=>$CountyName.',','Propertyaddress1'=>$PropertyAddress1,'Propertyaddress2'=>$PropertyAddress2,'Cityname'=>$CityName,'Statecode'=>$StateCode.'-','Zip'=>$ZipCode,'SearchThroDate'=>$SearchThroDate,'GoogleMapAddress'=>$GoogleMapAddress,'SearchFromDate'=>$SearchFromDate,'SearchAsOfDate'=>$SearchAsOfDate,'DisclaimerNote'=>$DisclaimerNote,'Url'=>$base_url,'CustomerZipCode'=>$CustomerZipCode,'OrderDate'=>$OrderDate,'DisclaimerStateEmail'=>$DisclaimerStateEmail,'DisclaimerStateWebsite'=>$DisclaimerStateWebsite,'DisclaimerStatePhoneNumber'=>$DisclaimerStatePhoneNumber,'Customer_State_Code'=>$Customer_State_Code,'Customer_County_Name'=>$Customer_County_Name,'TaxDisclaimerNote'=>$TaxDisclaimerNote,'taxpagebreak'=>$taxpagebreak,'pagebreak'=>$pagebreak,'LegalDisclaimerNote'=>$LegalDisclaimerNote,'State_code'=>$StateCode,'ImageUrl'=>$ImageUrl,'Miscellaneous'=>$Miscellaneous);


                foreach ($Mort as $key => $value) 
                {

                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
                //Orders
                //Heading
                //Chain of Title
                if($this->Order_reports_model->get_torderdeeds($OrderUID))
                {
                    $DeedHeading = '<p class="blur text-center" style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;">Chain of Title</p>';
                    array_push($keys, '%%DeedHeading%%');
                    array_push($values, $DeedHeading);
                }
                else
                {

                    array_push($keys, '%%DeedHeading%%');
                    array_push($values, ' ');
                }
                //Chain of Title
                //Tax Heading
                if($this->Order_reports_model->get_tordertaxcerts($OrderUID))
                {
                    $TaxHeading = '<tr><td class="blur text-center" colspan="6"><p style="font-size: 10pt;width:100%;font-weight: bold;margin-top: 5pt;text-align: center;">Tax Information</p></td></tr>';
                    array_push($keys, '%%TaxHeading%%');
                    array_push($values, $TaxHeading);
                }
                else
                {
                    array_push($keys, '%%TaxHeading%%');
                    array_push($values, ' ');
                }
                //Tax Heading
                //Heading
                //Address
                 include APPPATH .'modules/Order_reports/views/addressvariance.php';
                //Address
                //Legal Description
                $legal_information = $this->Order_reports_model->get_LegalDescription($OrderUID);
                if($legal_information)
                {

                    foreach ($legal_information as $dm) 
                    {

                        $LegalDescr = str_replace('  ', ' &nbsp;', nl2br(strtoupper($dm->LegalDescription)));
                        if($LegalDescr)
                        {
                            array_push($keys, '%%LegalDescr%%');
                            array_push($values, $LegalDescr);
                        }
                        else{
                            array_push($keys, '%%LegalDescr%%');
                            array_push($values, ' ');
                        }

                        foreach($dm as $cm => $vm)
                        {
                            array_push($keys, '%%'.$cm.'%%');
                            array_push($values, $vm);
                        }

                    } 
                }
                else{

                    array_push($keys, '%%LegalDescr%%');
                    array_push($values, ' ');
                }
                //Legal Description
                //Order Assessment
                $order_assessment = $this->Order_reports_model->get_OrderAssessment($OrderUID);
                if($order_assessment)
                {

                    foreach ($order_assessment as $data_orderass_info) 
                    {
                        $AssessedYear = $data_orderass_info->AssessedYear;

                        if($AssessedYear)
                        {
                            array_push($keys, '%%AssessedYear%%');
                            array_push($values, $AssessedYear);
                        }
                        else{
                            array_push($keys, '%%AssessedYear%%');
                            array_push($values, '-');

                        }
                        $Agricultural = $data_orderass_info->Agriculture;

                        if($Agricultural)
                            
                        {   
                        $Agricultural = '$'.$Agricultural;
                        array_push($keys, '%%Agricultural%%');
                        array_push($values, $Agricultural);
                        }
                        else
                        {
                        array_push($keys, '%%Agricultural%%');
                        array_push($values, '-');

                    }
                    $TotalValue = $data_orderass_info->TotalValue;
                    if($TotalValue)
                    {
                        $TotalVal = '$'.$TotalValue;
                        array_push($keys, '%%TotalValue%%');
                        array_push($values, $TotalVal);
                    }
                    else{
                        array_push($keys, '%%TotalValue%%');
                        array_push($values, '-');

                    }
                    $Landstr = $data_orderass_info->Land;
                    if($Landstr)
                    {
                        $Landltrim = ltrim($Landstr, '$');
                        $LandRepl = str_replace(",","",$Landltrim);
                        $Lan = substr($LandRepl, 0,-3);
                        $Land = '$'.number_format($Lan,2);
                    }
                    else{
                        $Land = '-';
                    }
                    $Buildingsstr = $data_orderass_info->Buildings;
                    if($Buildingsstr)
                    {

                        $Buildingsltrim = ltrim($Buildingsstr, '$');
                        $BuildingsRepl = str_replace(",","",$Buildingsltrim);
                        $Build = substr($BuildingsRepl, 0,-3);
                        $Buildings = '$'.number_format($Build,2);
                    }
                    else{
                        $Buildings = '-';
                    }
                    $AssessmentValue = $data_orderass_info->AssessmentValue;
                    if($AssessmentValue)
                    {
                        $AssessmentValue = $AssessmentValue;
                        array_push($keys, '%%AssessmentValue%%');
                        array_push($values, $AssessmentValue);
                        array_push($keys, '%%alignment%%');
                        array_push($values, 'text-left');
                    }
                    else{
                        array_push($keys, '%%AssessmentValue%%');
                        array_push($values, '-');
                        array_push($keys, '%%alignment%%');
                        array_push($values, 'text-center');

                    }
                    $Value = array('TotalValue'=>$TotalValue,'Buildings'=>$Buildings,'Land'=>$Land,'Agricultural'=>$Agricultural,'AssessmentValue'=>$AssessmentValue);
                    foreach ($Value as $key => $value) 
                    {
                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                    }
                } 
            }
            else
            {
                $TaxArray = array('AssessedYear','Land','Buildings','Agricultural','TotalValue');
                foreach($TaxArray as $col)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, '-');
                }
                 array_push($keys, '%%AssessmentValue%%');
                 array_push($values, '-');
                 array_push($keys, '%%alignment%%');
                 array_push($values, 'text-center');
            }

            //Get Exemption
            $exemptions = $this->Order_reports_model->get_taxExemption($OrderUID);
            if($exemptions)
            {
                $Exempt = array('Exempt'=>'Yes');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            else
            {
                $Exempt = array('Exempt'=>'No');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            //Get Exemption
            //Get Borrowers
            $borrowers = $this->Order_reports_model->getBorrowers($OrderUID);
            $Count =  count($borrowers);
            if($Count > 1){

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    if($value->PRName !=''){

                    $borrower .=  trim($value->PRName.',');
                    }
                }
                $borrowers =  rtrim($borrower,",");
                $Array = explode(",", $borrowers);
                $LastArrayValue =  end($Array);
                
                if(count($Array) > 1 ){
                    array_pop($Array);
                    $BorrowerName = implode(", &nbsp;", $Array).' and '.$LastArrayValue;

                }else{
                    
                    $BorrowerName = implode(", &nbsp;", $Array);
                }
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
            else{

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    $borrower .=  $value->PRName.',';

                }
                $BorrowerName =  rtrim($borrower,",");
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
        //Get Borrowers

            $output = str_replace($keys,$values, $output);

            $doc->load($output);
        //Property Starts
            preg_match_all('/<div class=\"torderproperty\">(.*?)<\/div>/s',$output,$torderproperty);
        // $getCounts = $this->Order_reports_model->getCounts($OrderUID);
        // $MortgageCount= $this->Order_reports_model->getMortgageCount($OrderUID);
            $MortgageCount =  $this->Order_reports_model->getMortgageCount($OrderUID);
if($MortgageCount == 0){ $MortgageCount = "NA"; }
            array_push($keys, '%%MortgageCount%%');
            array_push($values, $MortgageCount);
            $LienCount =  $this->Order_reports_model->getLienCount($OrderUID);
if($LienCount == 0){ $LienCount = "NA"; }
            array_push($keys, '%%LienCount%%');
            array_push($values, $LienCount);
            $JudgementCount =  $this->Order_reports_model->getJudgementCount($OrderUID);
if($JudgementCount == 0){ $JudgementCount = "NA"; }
            array_push($keys, '%%JudgementCount%%');
            array_push($values, $JudgementCount);
            $GranteeGrantor =  $this->Order_reports_model->getGranteeGrantor($OrderUID);
            if($GranteeGrantor)
            {
                foreach ($GranteeGrantor as $col => $value) 
                {
                    $Grantee = $value->Grantee;
                    if($Grantee)
                    {
                        array_push($keys, '%%Grantee%%');
                        array_push($values, $Grantee);
                    }
                    else{
                        array_push($keys, '%%Grantee%%');
                        array_push($values, '-');
                    }
                    $Grantor = $value->Grantor;
                    if($Grantee){
                        array_push($keys, '%%Grantor%%');
                        array_push($values, $Grantor);
                    }
                    else{
                        array_push($keys, '%%Grantor%%');
                        array_push($values, '-');
                    }
                    $EstateInterestName = $value->EstateInterestName;
                    if($EstateInterestName)
                    {
                        array_push($keys, '%%EstateInterestName%%');
                        array_push($values, $EstateInterestName);
                    }
                    else{
                        array_push($keys, '%%EstateInterestName%%');
                        array_push($values, '-');
                    }
                    $TenancyName = $value->TenancyName;
                    if($TenancyName)
                    {
                        array_push($keys, '%%TenancyTypeName%%');
                        array_push($values, $TenancyName);
                    }
                    else{
                        array_push($keys, '%%TenancyTypeName%%');
                        array_push($values, '-');
                    }


                } 
            }
            else{
                array_push($keys, '%%Grantee%%');
                array_push($values, '-');
                array_push($keys, '%%Grantor%%');
                array_push($values, '-');
                array_push($keys, '%%EstateInterestName%%');
                array_push($values, '-');
                array_push($keys, '%%TenancyTypeName%%');
                array_push($values, '-');
            }

            $exemptions = $this->Order_reports_model->get_taxExemption($OrderUID);
            if($exemptions)
            {
                $Exempt = array('Exempt'=>'Yes');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            else
            {
                $Exempt = array('Exempt'=>'No');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }



            $tax_information = $this->Order_reports_model->get_tax_latest($OrderUID);
            if($tax_information)
            {
            //Property tax in ISGN Report
                foreach ($tax_information as $data) 
                {
                //DeliquentTax
                    if($data->TaxStatusName)
                    {
                        if($data->TaxStatusName == 'Delinquent')
                        {
                            $DeliquentTax = array('DeliquentTax'=>'Yes');
                            foreach($DeliquentTax as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        else
                        {
                            $DeliquentTax = array('DeliquentTax'=>'No');
                            foreach($DeliquentTax as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                    }
                    else{
                        array_push($keys, '%%DeliquentTax%%');
                        array_push($values, '-');
                    }

                    $LatestTaxYear = $data->LatestTaxYear;
                    if($LatestTaxYear)
                    {

                        array_push($keys, '%%LatestTaxYear%%');
                        array_push($values, $LatestTaxYear);
                        array_push($keys, '%%ReferTaxSection%%');
                        array_push($values, 'REFER TAX SECTION');
                    }
                    else
                    {
                        array_push($keys, '%%LatestTaxYear%%');
                        array_push($values, '-');
                        array_push($keys, '%%ReferTaxSection%%');
                        array_push($values, '-');
                    }
                }
            }
            else
            {
                $TaxArray = array('LatestTaxYear','DeliquentTax','ReferTaxSection');
                foreach($TaxArray as $col)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, '-');
                }
            }



            $property_information = $this->Order_reports_model->getPropertyInformation($OrderUID);
            if($property_information)
            {
                foreach ($property_information as $key => $data) 
                {
                    $MaritalStatusName = $data->MaritalStatusName;
                    if($MaritalStatusName)
                    {
                        array_push($keys, '%%MaritalStatusName%%');
                        array_push($values, $MaritalStatusName);
                    }
                    else{
                        array_push($keys, '%%MaritalStatusName%%');
                        array_push($values, ' ');
                    }
                    $SubDivisionName = $data->SubDivisionName;
                    if($SubDivisionName)
                    {
                        array_push($keys, '%%SubDivisionName%%');
                        array_push($values, $SubDivisionName);
                    }
                    else{
                        array_push($keys, '%%SubDivisionName%%');
                        array_push($values, '-');
                    }

                    $sdMapNo = $data->sdMapNo;
                    if($sdMapNo)
                    {
                        array_push($keys, '%%sdMapNo%%');
                        array_push($values, $sdMapNo);
                    }
                    else{
                        array_push($keys, '%%sdMapNo%%');
                        array_push($values, '-');
                    }
                    $dSection = $data->dSection;
                    if($dSection)
                    {
                        array_push($keys, '%%dSection%%');
                        array_push($values, $dSection);
                    }
                    else{
                        array_push($keys, '%%dSection%%');
                        array_push($values, '-');
                    }
                    $Township = $data->Township;
                    if($Township)
                    {
                        array_push($keys, '%%Township%%');
                        array_push($values, $Township);
                    }
                    else{
                        array_push($keys, '%%Township%%');
                        array_push($values, '-');
                    }
                    $APN = $data->APN;
                    if($APN)
                    {
                        array_push($keys, '%%APN%%');
                        array_push($values, $APN);
                    }
                    else{
                        array_push($keys, '%%APN%%');
                        array_push($values, '-');
                    }
                    $Lot = $data->Lot;
                    $Block = $data->Block;
                    if($Lot && $Block)
                    {
                      array_push($keys, '%%Lot%%');
                      array_push($values, $Block.'/'.$Lot);
                    }
                    
                    else if($Lot)
                    {
                        array_push($keys, '%%Lot%%');
                        array_push($values, $Lot);
                    }
                    
                    else if($Block)
                    {
                        array_push($keys, '%%Lot%%');
                        array_push($values, $Block);
                    }
                    else{
                        array_push($keys, '%%Lot%%');
                        array_push($values, '-');
                    }
                }
            }
            else
            {
                $TaxArray = array('Exempt','APN','Lot','Block','SubDivisionName','sdMapNo','dSection','Township');
                foreach($TaxArray as $col)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, '-');
                }
                        array_push($keys, '%%MaritalStatusName%%');
                        array_push($values, ' ');

            }

        //Property Tax in ISGN Report




            $torderproperty_table .= str_replace($keys, $values, $torderproperty[0][0]);
            foreach ($doc->find(".torderproperty") as $node ) 
            {
                $node->innertext = $torderproperty_table;
            }
        //Property Ends
        //Deed Starts
            preg_match_all('/<div class=\"torderdeeds\">(.*?)<\/div>/s',$output,$torderdeeds);
            $torderdeeds_table = '';
            $torderdeeds_array = array();
            $torderdeedsparties_array = array();
            $torder_deeds = $this->Order_reports_model->get_torderdeeds($OrderUID);
            $torderdeeds_array = $torder_deeds;
            $torderdeeds_array_count = count($torderdeeds_array);
            for ($i=0; $i < $torderdeeds_array_count; $i++) 
            { 
                $torderdeeds_array[$i]->deed_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($torderdeeds_array[$i] as $key => $value)
                {
                    $DocumentTypeName = $torderdeeds_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $DeedType = $torderdeeds_array[$i]->DeedType;
                            if($DeedType)
                            {
                                array_push($keys, '%%DeedDocumentTypeName%%');
                                array_push($values, $DeedType);
                            }
                            else{
                                array_push($keys, '%%DeedDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%DeedDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%DeedDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%DeedDocumentTypeName%%');
                        array_push($values, '-');
                    }
                    //Deed Date Format Change
                    $DDated = $torderdeeds_array[$i]->DeedDated;
                    if($DDated == '0000-00-00')
                    {
                        array_push($keys, '%%DeedDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $DeedDated =  date('m/d/Y', strtotime($DDated));
                        array_push($keys, '%%DeedDate%%');
                        array_push($values, $DeedDated);
                    }
                    //Deed Date Format Change
                    //Recorded date Format Change
                    $RDated = $torderdeeds_array[$i]->DeedRecorded;
                    if($RDated == '0000-00-00')
                    {
                        array_push($keys, '%%RecordedDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $RecordedDated =  date('m/d/Y', strtotime($RDated));
                        array_push($keys, '%%RecordedDate%%');
                        array_push($values, $RecordedDated);
                    }

                //Recorded date Format Change
                //ConsiderationAmount
                    $ConsiderationAmount = $torderdeeds_array[$i]->ConsiderationAmount;
                    if($ConsiderationAmount)
                    {
                        $ConsiderAmount = '$'.number_format($ConsiderationAmount,2);
                        array_push($keys, '%%ConsiderAmount%%');
                        array_push($values, $ConsiderAmount);
                    }
                    else{
                        array_push($keys, '%%ConsiderAmount%%');
                        array_push($values, '-');
                    }
                //ConsiderationAmount
                //Book/Page
                    $Deed_DBVTypeUID_1 = $torderdeeds_array[$i]->Deed_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_1);
                    $Deed_DBVTypeValue_1 = $torderdeeds_array[$i]->Deed_DBVTypeValue_1;
                    $Deed_DBVTypeUID_2 = $torderdeeds_array[$i]->Deed_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_2);
                    $Deed_DBVTypeValue_2 = $torderdeeds_array[$i]->Deed_DBVTypeValue_2;
                    if($DBVTypeName_1)
                    {
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, $DBVTypeName_1);
                        if($Deed_DBVTypeValue_1)
                        {
                            array_push($keys, '%%Deed_DBVTypeValue_1%%');
                            array_push($values, $Deed_DBVTypeValue_1);
                        }
                        else{
                            array_push($keys, '%%Deed_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }
                    }
                    else{
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Deed_DBVTypeValue_1%%');
                        array_push($values, '-');
                    }
                    if($DBVTypeName_2)
                    {
                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, $DBVTypeName_2);
                        if($Deed_DBVTypeValue_2)
                        {
                            array_push($keys, '%%Deed_DBVTypeValue_2%%');
                            array_push($values, $Deed_DBVTypeValue_2);
                        }
                        else{
                            array_push($keys, '%%Deed_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }
                    }
                    else{

                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Deed_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                //Deed Document
                    $DocumentNo = $torderdeeds_array[$i]->DocumentNo;
                    if($DocumentNo)
                    {
                        array_push($keys, '%%DocumentNo%%');
                        array_push($values, $DocumentNo);
                    }
                    else{
                        array_push($keys, '%%DocumentNo%%');
                        array_push($values, '-');
                    }
                //Deed Document
                //Certificate Number
                    $CertificateNo = $torderdeeds_array[$i]->CertificateNo;
                    if($CertificateNo)
                    {
                        array_push($keys, '%%CertificateNo%%');
                        array_push($values, $CertificateNo);
                    }
                    else{
                        array_push($keys, '%%CertificateNo%%');
                        array_push($values, '-');
                    }
                //Certificate Number
                //Instrument Number
                    $InstrumentNo = $torderdeeds_array[$i]->InstrumentNo;
                    if($InstrumentNo)
                    {
                        array_push($keys, '%%InstrumentNo%%');
                        array_push($values, $InstrumentNo);
                    }
                    else{
                        array_push($keys, '%%InstrumentNo%%');
                        array_push($values, '-');
                    }
                //Instrument Number
                //Deed Comments
                    $DeedComments = $torderdeeds_array[$i]->DeedComments;
                    if($DeedComments)
                    {
                        array_push($keys, '%%DeedComments%%');
                        array_push($values, $DeedComments);
                        array_push($keys, '%%deedalignment%%');
                        array_push($values, 'text-left');
                    }
                    else{
                        array_push($keys, '%%DeedComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%deedalignment%%');
                        array_push($values, 'text-center');
                    }
                //Deed Comments
                //Main Loop
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                //Main Loop
                }
                $torderdeeds_table .= str_replace($keys, $values, $torderdeeds[0][0]);
            }
            foreach ( $doc->find(".torderdeeds") as $node ) 
            {
                $node->innertext = $torderdeeds_table;
            }
        //Deed Ends
        //Mortgages Starts
            preg_match_all('/<div class=\"tordermortgages\">(.*?)<\/div>/s',$output,$tordermortgages);
            $tordermortgages_array = array();
            $tordermortgagesparties_array = array();
            $tordermortgages_array = $this->Order_reports_model->get_tordermortgageparties($OrderUID);
            $tordermortgages_array_count = count($tordermortgages_array);
            $tordermortgages_table2="";
            for ($i=0; $i < $tordermortgages_array_count; $i++) 
            { 

                $tordermortgages_table = '';
                $tordermortgages_array[$i]->mortgage_increment = $i+1;
                $keys = array();
                $values = array();
                        //Leins & Encumbrances
                if($i==0)
                {
                    $MortgageHeading = '<tr><th class="blur text-center" colspan="4"><p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Liens & Encumbrances</p></th></tr>';
                    array_push($keys, '%%MortgageHeading%%');
                    array_push($values, $MortgageHeading);

                }
                else
                {
                    array_push($keys, '%%MortgageHeading%%');
                    array_push($values, ' ');
                }
                foreach ($tordermortgages_array[$i] as $key => $value) 
                {

                    $DocumentTypeName = $tordermortgages_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $MortgageType = $tordermortgages_array[$i]->MortgageType;
                            if($MortgageType)
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, $MortgageType);
                            }
                            else
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%MortgageDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //Mortgage Date Format Change
                    $MortgageDated = $tordermortgages_array[$i]->MortgageDated;
                    if( $MortgageDated == '0000-00-00')
                    {
                        array_push($keys, '%%MortgageDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $MortgageDate =  date('m/d/Y', strtotime($MortgageDated));
                        array_push($keys, '%%MortgageDate%%');
                        array_push($values, $MortgageDate);
                    }
                //Mortgage Date Format Change
                //Mortgage Date Format Change
                    $MortgageRecorded = $tordermortgages_array[$i]->MortgageRecorded;
                    if($MortgageRecorded == '0000-00-00')
                    {
                        array_push($keys, '%%MortgageRecordedDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $MortgageRecordedDate =  date('m/d/Y', strtotime($MortgageRecorded));
                        array_push($keys, '%%MortgageRecordedDate%%');
                        array_push($values, $MortgageRecordedDate);
                    }
                //Mortgage Date Format Change
                //Mortgage Maturity Date Format
                    $MortgageMaturityDate = $tordermortgages_array[$i]->MortgageMaturityDate;
                    if( $MortgageMaturityDate == '0000-00-00')
                    {
                        array_push($keys, '%%MaturityDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $MaturityDate =  date('m/d/Y', strtotime($MortgageMaturityDate));
                        array_push($keys, '%%MaturityDate%%');
                        array_push($values, $MaturityDate);
                    }
                //Mortgage Maturity Date Format
                //LoanAmount
                    $MortgageAmount = $tordermortgages_array[$i]->MortgageAmount;
                    if($MortgageAmount)
                    {
                        $LoanAmt = '$'.number_format($MortgageAmount,2);
                        array_push($keys, '%%LoanAmt%%');
                        array_push($values, $LoanAmt);
                    }
                    else{
                        array_push($keys, '%%LoanAmt%%');
                        array_push($values, '-');
                    }
                //LoanAmount
                //Mortgagee
                    $Mortgagee = $tordermortgages_array[$i]->Mortgagee;
                    if($Mortgagee)
                    {
                        array_push($keys, '%%Mortgagee%%');
                        array_push($values, $Mortgagee);
                    }
                    else{
                        array_push($keys, '%%Mortgagee%%');
                        array_push($values, '-');
                    }
                //Mortgagee
                //Trustee
                    $Trustee1 = $tordermortgages_array[$i]->Trustee1;
                    $Trustee2 = $tordermortgages_array[$i]->Trustee2;
                    if($Trustee1 != '' && $Trustee2 != '')
                    {
                        $Trustee = $Trustee1.','.$Trustee2;
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);
                    }
                    elseif($Trustee1 != '')
                    {
                        $Trustee = $Trustee1;
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);
                    }
                    elseif($Trustee2 != ''){

                        $Trustee = $Trustee2;
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);

                    }
                    else{

                        $Trustee = '-';
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);
                    }
                //Trustee
                //Closed/Open Ended
                    if($tordermortgages_array[$i]->LienTypeName =='Closed Ended')
                    {
                        $MTG = array('MTG'=>'Closed Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    if($tordermortgages_array[$i]->LienTypeName =='Open Ended')
                    {
                        $MTG = array('MTG'=>'Open Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    else
                    {

                            array_push($keys, '%%MTG%%');
                            array_push($values,'-');
                        
                    }
                //Closed/Open Ended
                //Book/Page
                    $Mortgage_DBVTypeUID_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_1);
                    $Mortgage_DBVTypeValue_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_1;
                    $Mortgage_DBVTypeUID_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_2);
                    $Mortgage_DBVTypeValue_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_2;

                    if($DBVTypeName_1)
                    {
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, $DBVTypeName_1);
                        if($Mortgage_DBVTypeValue_1)
                        {
                            array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                            array_push($values, $Mortgage_DBVTypeValue_1);
                        }
                        else
                        {
                            array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }
                    }
                    else{
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                        array_push($values, '-');
                    }

                    if($DBVTypeName_2)
                    {
                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, $DBVTypeName_2);
                        if($Mortgage_DBVTypeValue_2)
                        {
                            array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                            array_push($values, $Mortgage_DBVTypeValue_2);
                        }
                        else{
                            array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }
                    }
                    else{

                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                    if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                    {
                        $AppendInstrument = $Mortgage_DBVTypeValue_1.','.$Mortgage_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_1 == 'Instrument'){
                        $AppendInstrument = $Mortgage_DBVTypeValue_1;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_2 == 'Instrument'){
                        $AppendInstrument = $Mortgage_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                    else{
                        $AppendInstrument = '-';
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                //Book/Page
                //Additional Info
                $AdditionalInfo = $tordermortgages_array[$i]->AdditionalInfo;
                if($AdditionalInfo)
                {
                    array_push($keys, '%%AdditionalInfo%%');
                    array_push($values, $AdditionalInfo);
                }
                else{
                    array_push($keys, '%%AdditionalInfo%%');
                    array_push($values, '-');
                }
                //Additional Info
                                //Closed/Open Ended
                    $OpenEnded = $tordermortgages_array[$i]->IsOpenEnded;
                    if($OpenEnded == '1')
                    {

                            array_push($keys, '%%OpenEnded%%');
                            array_push($values, 'Yes');
                        
                    }
                    else
                    {

                            array_push($keys, '%%OpenEnded%%');
                            array_push($values,'-');
                        
                    }
                //Closed/Open Ended

                    
                //Mortgage Comments
                    $MortgageComments = $tordermortgages_array[$i]->MortgageComments;
                    if($MortgageComments)
                    {
                        array_push($keys, '%%MortgageComments%%');
                        array_push($values, $MortgageComments);
                        array_push($keys, '%%mortgagealignment%%');
                        array_push($values, 'text-left');
                    }
                    else{
                        array_push($keys, '%%MortgageComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%mortgagealignment%%');
                        array_push($values, 'text-center');
                    }
                //Mortgage Comments
                //Main Loop
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                //Main Loop
                }
                $tordermortgages_table .= str_replace($keys, $values, $tordermortgages[0][0]);

                $submortgage_html='<table style="width: 100%;margin-top: 5pt;page-break-inside: avoid;table-layout: fixed;" cellspacing="0"><tr>
                <td class="td-bd text-center" colspan="3">
                <p style="font-size: 8pt;text-transform: uppercase;" class="bold text-center">%%submortgageDocumentTypeName%%</p>
                </td>
                <td class="td-bd text-center" colspan="1">
                <p style="font-size: 8pt;" class="bold text-center">Document Dated</p>
                </td>
                <td class="td-bd text-center" colspan="2">
                <p class="text-center" style="padding:0pt 20pt;font-size: 8pt;">%%Dated%%</p>
                </td>
                </tr>
                <tr>
                <td width="15.30%" class="td-bd text-center">
                <p style="font-size: 8pt;" class="bold">Recorded Date</p>
                </td>
                <td width="18.30%" class="td-bd text-center">
                <p style="font-size: 8pt;" class="text-center">%%Recorded%%</p>
                </td>
                <td width="15.30%" class="td-bd text-center">
                <p class="bold text-center" style="font-size: 8pt;">%%SubdocumentDBVTypeName_1%%</p>
                </td>
                <td width="18.30%" class="td-bd text-center">
                <p style="font-size: 8pt;" class="text-center">%%subdocument_DBVTypeValue_1%%</p>
                </td>
                <td width="15.30%" class="td-bd text-center">
                <p style="font-size: 8pt;" class="bold">%%SubdocumentDBVTypeName_2%%</p>
                </td>
                <td width="18.30%" class="td-bd text-center">
                <p style="font-size: 8pt;" class="text-center">%%subdocument_DBVTypeValue_2%%</p>
                </td>
                </tr>
                <tr>
                <td class="td-bd text-center" colspan="1">
                <p style="font-size: 8pt;" class="bold">Comments</p>
                </td>
                <td class="td-bd %%alignment%%" colspan="5">
                <p class="" style="text-align: justify;font-size: 7pt;">%%SubMortgageComments%%</p>
                </td>
                </tr></table>';

                $submortgage="";
                //Sub Mortgage
                $submortgage_table = '';
                $submortgage_array = array();
                $Mortgage = $tordermortgages_array[$i]->MortgageSNo;
                $submortgage_array = $this->Order_reports_model->getsubmortgage($OrderUID, $Mortgage);
                $submortgage_array_count = count($submortgage_array);
                for ($j=0; $j < $submortgage_array_count; $j++) 
                { 
                    $keys = array();
                    $values = array();
                    foreach ($submortgage_array[$j] as $key => $value) 
                    {

                    $DocumentTypeName = $submortgage_array[$j]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $MortgageAssignmentType = $submortgage_array[$j]->MortgageAssignmentType;
                            if($MortgageAssignmentType)
                            {
                                array_push($keys, '%%submortgageDocumentTypeName%%');
                                array_push($values, $MortgageAssignmentType);
                            }
                            else
                            {
                                array_push($keys, '%%submortgageDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%submortgageDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%submortgageDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%submortgageDocumentTypeName%%');
                        array_push($values, '-');
                    }

                        $Dated = $submortgage_array[$j]->Dated;
                        if($Dated == '0000-00-00')
                        {
                            array_push($keys, '%%Dated%%');
                            array_push($values, '-');

                        }
                        else{

                            $submortgagedated =  date('m/d/Y', strtotime($Dated));
                            array_push($keys, '%%Dated%%');
                            array_push($values, $submortgagedated);

                        }
                        $Recorded = $submortgage_array[$j]->Recorded;
                        if($Recorded == '0000-00-00')
                        {
                            array_push($keys, '%%Recorded%%');
                            array_push($values, '-');

                        }
                        else{

                            $submortgagerecorded =  date('m/d/Y', strtotime($Recorded));
                            array_push($keys, '%%Recorded%%');
                            array_push($values, $submortgagerecorded);

                        }
                        $SubMortgageComments = $submortgage_array[$j]->Comments;
                        if($SubMortgageComments)
                        {
                            array_push($keys, '%%SubMortgageComments%%');
                            array_push($values, $SubMortgageComments);
                            array_push($keys, '%%mortgagealignment%%');
                            array_push($values, 'text-left');
                        }
                        else{
                            array_push($keys, '%%SubMortgageComments%%');
                            array_push($values, '-');
                            array_push($keys, '%%mortgagealignment%%');
                            array_push($values, 'text-center');
                        }

                        $Subdocument_DBVTypeUID_1 = $submortgage_array[$j]->Subdocument_DBVTypeUID_1;
                        $SubdocumentDBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Subdocument_DBVTypeUID_1);
                        $Subdocument_DBVTypeValue_1 = $submortgage_array[$j]->Subdocument_DBVTypeValue_1;
                        $Subdocument_DBVTypeUID_2 = $submortgage_array[$j]->Subdocument_DBVTypeUID_2;
                        $SubdocumentDBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Subdocument_DBVTypeUID_2);
                        $Subdocument_DBVTypeValue_2 = $submortgage_array[$j]->Subdocument_DBVTypeValue_2;

                        if($SubdocumentDBVTypeName_1)
                        {
                            array_push($keys, '%%SubdocumentDBVTypeName_1%%');
                            array_push($values, $SubdocumentDBVTypeName_1);
                            if($Subdocument_DBVTypeValue_1)
                            {
                                array_push($keys, '%%subdocument_DBVTypeValue_1%%');
                                array_push($values, $Subdocument_DBVTypeValue_1);
                            }
                            else{

                                array_push($keys, '%%subdocument_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else{
                            array_push($keys, '%%SubdocumentDBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%subdocument_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }

                        if($SubdocumentDBVTypeName_2)
                        {
                            array_push($keys, '%%SubdocumentDBVTypeName_2%%');
                            array_push($values, $SubdocumentDBVTypeName_2);
                            if($Subdocument_DBVTypeValue_2)
                            {
                                array_push($keys, '%%subdocument_DBVTypeValue_2%%');
                                array_push($values, $Subdocument_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%subdocument_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else{

                            array_push($keys, '%%SubdocumentDBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%subdocument_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }

                    }

                    $submortgage .= str_replace($keys, $values, $submortgage_html);

                }
                $tordermortgages_table = str_replace('%%submortgage%%', $submortgage, $tordermortgages_table);

                $tordermortgages_table2 .= $tordermortgages_table;

            }
            foreach ( $doc->find(".tordermortgages") as $node ) 
            {
                $node->innertext = $tordermortgages_table2;
            }
        //Mortgages Ends
        //Judgement Starts
            preg_match_all('/<div class=\"torderjudgments\">(.*?)<\/div>/s',$output,$torderjudgments);
            $torderjudgments_table = '';
            $torderjudgments_array = array();
            $torderjudgments_array = $this->Order_reports_model->get_torderjudgements($OrderUID);
            $torderjudgments_array_count = count($torderjudgments_array);
            for ($i=0; $i < $torderjudgments_array_count; $i++)
            { 
                $torderjudgments_array[$i]->judgement_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($torderjudgments_array[$i] as $key => $value)
                {
                    $DocumentTypeName = $torderjudgments_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $JudgementType = $torderjudgments_array[$i]->JudgementType;
                            if($JudgementType)
                            {
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, $JudgementType);
                            }
                            else{
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%JudgementDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //Plaintiff
                    $Plaintiff = $torderjudgments_array[$i]->Plaintiff;
                    if($Plaintiff)
                    {
                        array_push($keys, '%%Plaintiff%%');
                        array_push($values, $Plaintiff);
                    }
                    else{
                        array_push($keys, '%%Plaintiff%%');
                        array_push($values, '-');
                    }
                //Plaintiff
                //Defendant
                    $Defendent = $torderjudgments_array[$i]->Defendent;
                    if($Defendent)
                    {
                        array_push($keys, '%%Defendent%%');
                        array_push($values, $Defendent);
                    }
                    else{
                        array_push($keys, '%%Defendent%%');
                        array_push($values, '-');
                    }
                //Defendant
                //Judgement Date Format Change
                    $JDated = $torderjudgments_array[$i]->JudgementDated;
                    if($JDated == '0000-00-00')
                    {
                        array_push($keys, '%%JudgeDated%%');
                        array_push($values, '-');  
                    }
                    else{
                        $Dated =  date('m/d/Y', strtotime($JDated));
                        array_push($keys, '%%JudgeDated%%');
                        array_push($values, $Dated);  
                    }
                //Judgement Date Format Change
                //Judgement Recorded date Format Change
                    $RDated = $torderjudgments_array[$i]->JudgementRecorded;
                    if($RDated == '0000-00-00')
                    {
                        array_push($keys, '%%JudgeRecorded%%');
                        array_push($values, '-');
                    }
                    else{
                        $RecordedDated =  date('m/d/Y', strtotime($RDated));
                        array_push($keys, '%%JudgeRecorded%%');
                        array_push($values, $RecordedDated);
                    }
                // JudgementRecorded date Format Change
                //Judgement Amount
                    $JudgementAmount = $torderjudgments_array[$i]->JudgementAmount;
                    if($JudgementAmount)
                    {
                        $JudAmt = '$'.number_format($JudgementAmount,2);
                        array_push($keys, '%%JudgementAmount%%');
                        array_push($values, $JudAmt);
                    }
                    else{
                        array_push($keys, '%%JudgementAmount%%');
                        array_push($values, '-');
                    }
                //Judgement Amount
                //Judgements Comments
                    $JudgementComments = $torderjudgments_array[$i]->JudgementComments;
                    if($JudgementComments)
                    {
                        array_push($keys, '%%JudgementComments%%');
                        array_push($values, $JudgementComments);
                        array_push($keys, '%%judalignment%%');
                        array_push($values, 'text-left');
                    }
                    else{
                        array_push($keys, '%%JudgementComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%judalignment%%');
                        array_push($values, 'text-center');
                    }
                //Judgements Comments
                    $Judgement_DBVTypeUID_1 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_1);
                    $Judgement_DBVTypeValue_1 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_1;
                    $Judgement_DBVTypeUID_2 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_2);
                    $Judgement_DBVTypeValue_2 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_2;
                    if($DBVTypeName_1)
                    {
                        if($DBVTypeName_1 !== 'Case number')
                        {
                            array_push($keys, '%%JudgementDBVTypeName_1%%');
                            array_push($values, $DBVTypeName_1);
                            if($Judgement_DBVTypeValue_1)
                            {
                                array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                array_push($values, $Judgement_DBVTypeValue_1);
                            }
                            else{
                                array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                            array_push($values, '-'); 
                        }
                    }
                    else
                    {
                        array_push($keys, '%%JudgementDBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                        array_push($values, '-'); 
                    }
                    if($DBVTypeName_2)
                    {
                        if($DBVTypeName_2 !== 'Case number')
                        {
                            
                            array_push($keys, '%%JudgementDBVTypeName_2%%');
                            array_push($values, $DBVTypeName_2);
                            if($Judgement_DBVTypeValue_2)
                            {
                                array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                array_push($values, $Judgement_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                            array_push($values, '-'); 
                        }
                    }
                    else{

                        array_push($keys, '%%JudgementDBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                //CaseNumber
                    $JudgementCaseNo = $torderjudgments_array[$i]->JudgementCaseNo;
                    if($JudgementCaseNo)
                    {
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $JudgementCaseNo);
                    }
                    else{

                        if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                        {
                            $AppendCasenumber = $Judgement_DBVTypeValue_1.','.$Judgement_DBVTypeValue_2;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);

                        }
                        if($DBVTypeName_1 == 'Case number'){
                            $AppendCasenumber = $Judgement_DBVTypeValue_1;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);

                        }
                        if($DBVTypeName_2 == 'Case number'){
                            $AppendCasenumber = $Judgement_DBVTypeValue_2;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);
                        }
                        else{
                            $AppendCasenumber = '-';
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);
                        }
                    }
                //CaseNumber
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
                $torderjudgments_table .= str_replace($keys, $values, $torderjudgments[0][0]);
            }
            foreach ( $doc->find(".torderjudgments") as $node ) 
            {
                $node->innertext = $torderjudgments_table;
            }
        //Judgments Ends
        //Liens Starts
            preg_match_all('/<div class=\"torderliens\">(.*?)<\/div>/s',$output,$torderliens);
            $torderliens_table = '';
            $torderliens_array = array();
            $torderliens_array = $this->Order_reports_model->get_torderliens($OrderUID);
            $torderliens_array_count = count($torderliens_array);
            for ($i=0; $i < $torderliens_array_count; $i++) 
            { 

                $torderliens_array[$i]->lien_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($torderliens_array[$i] as $key => $value) 
                {
                //DocumentTypeName
                    $DocumentTypeName = $torderliens_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $LeinType = $torderliens_array[$i]->LeinType;
                            if($LeinType)
                            {
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, $LeinType);
                            }
                            else{
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%LeinDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //DocumentTypeName
                //Lein Date Format Change
                    $LDated = $torderliens_array[$i]->LeinDated;
                    if($LDated == '0000-00-00')
                    {
                        array_push($keys, '%%LeinDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $Dated =  date('m/d/Y', strtotime($LDated));
                        array_push($keys, '%%LeinDate%%');
                        array_push($values, $Dated);
                    }
                //Lein Date Format Change
                //Lein Recorded date Format Change
                    $RDated = $torderliens_array[$i]->LeinRecorded;
                    if($RDated == '0000-00-00')
                    {
                        array_push($keys, '%%leinRecord%%');
                        array_push($values, '-');
                    }
                    else{
                        $RecordedDated =  date('m/d/Y', strtotime($RDated));
                        array_push($keys, '%%leinRecord%%');
                        array_push($values, $RecordedDated);
                    }
                // Lein date Format Change
                    if($torderliens_array[$i]->LienTypeName =='Closed Ended')
                    {
                        $MTG = array('MTG'=>'Closed Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    if($torderliens_array[$i]->LienTypeName =='Open Ended')
                    {
                        $MTG = array('MTG'=>'Open Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    else
                    {

                            array_push($keys, '%%MTG%%');
                            array_push($values, '-');
                        
                    }

                    $Lien_DBVTypeUID_1 = $torderliens_array[$i]->Lien_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_1);
                    $Lien_DBVTypeValue_1 = $torderliens_array[$i]->Lien_DBVTypeValue_1;
                    $Lien_DBVTypeUID_2 = $torderliens_array[$i]->Lien_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_2);
                    $Lien_DBVTypeValue_2 = $torderliens_array[$i]->Lien_DBVTypeValue_2;
                    if($DBVTypeName_1)
                    {
                        if($DBVTypeName_1 !== 'Case number')
                        {
                            array_push($keys, '%%DBVTypeName_1%%');
                            array_push($values, $DBVTypeName_1);
                            if($Lien_DBVTypeValue_1)
                            {
                                array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                array_push($values, $Lien_DBVTypeValue_1);
                            }
                            else{
                                array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%DBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%Lien_DBVTypeValue_1%%');
                            array_push($values, '-'); 
                        }
                    }
                    else
                    {
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Lien_DBVTypeValue_1%%');
                        array_push($values, '-'); 
                    }
                    if($DBVTypeName_2)
                    {
                        if($DBVTypeName_2 !== 'Case number')
                        {
                            
                            array_push($keys, '%%DBVTypeName_2%%');
                            array_push($values, $DBVTypeName_2);
                            if($Lien_DBVTypeValue_2)
                            {
                                array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                array_push($values, $Lien_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%DBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%Lien_DBVTypeValue_2%%');
                            array_push($values, '-'); 
                        }

                    }
                    else{

                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Lien_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                    //CaseNumber
                    if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                    {
                        $AppendCasenumber = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);

                    }
                    if($DBVTypeName_1 == 'Case number'){
                        $AppendCasenumber = $Lien_DBVTypeValue_1;
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);

                    }
                    if($DBVTypeName_2 == 'Case number'){
                        $AppendCasenumber = $Lien_DBVTypeValue_2;
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);
                    }
                    else{
                        $AppendCasenumber = '-';
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);
                    }
                    //CaseNumber
                    //Instrument
                    if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                    {
                        $AppendInstrument = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_1 == 'Instrument'){
                        $AppendInstrument = $Lien_DBVTypeValue_1;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_2 == 'Instrument'){
                        $AppendInstrument = $Lien_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                    else{
                        $AppendInstrument = '-';
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                //Instrument
                //Lein Amount
                    $LeinAmount = $torderliens_array[$i]->LeinAmount;
                    if($LeinAmount)
                    {
                        $MortAmt = '$'.number_format($LeinAmount,2);
                        array_push($keys, '%%LeinAmt%%');
                        array_push($values, $MortAmt);
                    }
                    else{
                        array_push($keys, '%%LeinAmt%%');
                        array_push($values, '-');
                    }
                //Lein Amount
                //Lein Comments
                    $LeinComments = $torderliens_array[$i]->LeinComments;
                    if($LeinComments)
                    {
                        array_push($keys, '%%LeinComments%%');
                        array_push($values, $LeinComments);
                        array_push($keys, '%%leinalignment%%');
                        array_push($values, 'text-left');
                    }
                    else{
                        array_push($keys, '%%LeinComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%leinalignment%%');
                        array_push($values, 'text-center');
                    }
                //Lein Comments

                    $Holder = $torderliens_array[$i]->Holder;
                    array_push($keys, '%%LeinHolder%%');
                    array_push($values, $Holder);

                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
                $output = str_replace($keys, $values, $output);

                $torderliens_table .= str_replace($keys, $values, $torderliens[0][0]);

            }
            foreach ( $doc->find(".torderliens") as $node ) 
            {
                $node->innertext = $torderliens_table;
            }
        //Leins Ends
            return $doc;
    }

    function isgn_property_report($OrderUID)
    {
        error_reporting(0);
        $this->load->library('Dom/Simple_html_dom');
        $doc = new simple_html_dom();
        $order_details = $this->Order_reports_model->get_torders($OrderUID);
        $StateCode = $order_details->StateCode;
        if($order_details->TemplateUID == 2) {
            $filename = FCPATH.'Templates/isgnpropertyreport-without-LD.php';
        }
        else {
            $filename = FCPATH.'Templates/isgnpropertyreport.php';
        }
        $OrderUID = $OrderUID;
        $fp = fopen ( $filename, 'r' );
        //read our template into a variable
        $output = fread( $fp, filesize($filename));
        //Orders
        $torders_array = array();
        $keys = array();
        $values = array();
        $order_details = $this->Order_reports_model->get_torders($OrderUID);
       
        $date = $order_details->OrderEntryDatetime;
        $OrderDate =  date('m/d/Y', strtotime($date));
        $dates = array('OrderDate'=>$OrderDate);
        foreach ($dates as $key => $value) 
        {

            array_push($keys, '%%'.$key.'%%');
            array_push($values, $value);
        }
        $CurrentDate = array('CurrentDate'=>date("m/d/Y"));
        foreach ($CurrentDate as $key => $value) 
        {

            array_push($keys, '%%'.$key.'%%');
            array_push($values, $value);
        }

        //Tax Certificate
        $TaxCertificateRequired = $order_details->TaxCertificateRequired;
        $WorkflowModuleUID = $this->Order_reports_model->GetWorkflowModuleUID($OrderUID);
        $workflowModuleArray = array();
        foreach ($WorkflowModuleUID as $key => $value){
            $workflowModuleArray[$key] = $value['WorkflowModuleUID'];
        }

        $tordertaxcerts_array = $this->Order_reports_model->get_tordertaxcerts($OrderUID);
        $tordertaxcerts_array_count = count($tordertaxcerts_array);

        $taxcert_html = ' ';
        $NumTotalWOExempt = '';
        $sep_taxcert_html = ' ';
        $sep_legaldesc = ' ';
        if($order_details->PropertyStateCode == 'TX'){

            $TotalTaxBaseAmt = [];
            $TotalTaxDueAmt = [];
            $TotalWOExempt = [];

            $taxcert_html.='<div class="">
            <div style="page-break-inside: avoid;">
            <table style="width: 100%;margin-top: 10pt;" cellspacing="0">
            <thead>
            <tr>
            <th class="blur text-center" colspan="5">
            <p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Summary of All Account(s)</p>
            </th>
            </tr>
            </thead>
            <tbody>
            <tr class="br-black br-b-trans">
            <td style="width: 30%;">
            <p></p>
            </td>
            <td colspan="2" style="width: 25%;" class="td-ta-r">
            <p class="bold">SUMMARY OF CURRENT YEAR</p>
            </td>
            <td colspan="2" style="width: 25%;" class="td-ta-r">
            <p class="bold ">SUMMARY OF ALL TAXES DUE</p>
            </td>
            </tr>

            <tr class="br-black br-t-trans">
            <td>
            <p class="bold td-ta-r"></p>
            </td>
            <td class="td-ta-r">
            <p class="bold td-ta-r">TAX YEAR</p>
            </td>
            <td class="td-ta-r">
            <p class="bold td-ta-r">BASE TAX</p>
            </td>
            <td class="td-ta-r">
            <p class="bold td-ta-r">DUE  %%CurrentMonth%%</p>
            </td>
            <td class="td-ta-r">
            <p class="bold td-ta-r">DUE  %%NextMonth%%</p>
            </td>
            </tr>';

            for ($i = 0; $i < $tordertaxcerts_array_count; $i++) {

                $DocumentTypeName = $tordertaxcerts_array[$i]->DocumentTypeName;
                if ($DocumentTypeName) {
                    if ($DocumentTypeName == 'Others') {
                        $TaxType = $tordertaxcerts_array[$i]->TaxType;
                        if ($TaxType) {
                            $TaxDocumentTypeName = $TaxType;
                        } else {
                            $TaxDocumentTypeName = '-';
                        }
                    } else if ($DocumentTypeName !== 'Others') {
                        $TaxDocumentTypeName = $DocumentTypeName;
                    } else {
                        $TaxDocumentTypeName = '-';
                    }
                } else {
                    $TaxDocumentTypeName = '-';
                }

                $WOExempt = $tordertaxcerts_array[$i]->WOExempt;
                if ($WOExempt) {
                    $TotalWOExempt[] = $WOExempt;
                } else {
                    $TotalWOExempt[] = 0;
                }

                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $taxinstallment_array = $this->Order_reports_model->GetTaxInstallmentGroupByYear($OrderUID, $TaxCert);
                $taxinstallment_array_count = count($taxinstallment_array);
                $TotalDueAmount = array();
                $TotalBaseAmount = array();

                for ($k = 0; $k < $taxinstallment_array_count; $k++) {
                    $keys = array();
                    $values = array();
                    foreach($taxinstallment_array[$k] as $key => $value) {
                        $TotalBaseAmt = $taxinstallment_array[$k]->TotalBaseAmt;
                        $TotalPaidAmt = $taxinstallment_array[$k]->TotalPaidAmt;
                        $TotalTaxStatus = $taxinstallment_array[$k]->TotalTaxStatus;

                        $DueAmount = [];
                        $GrossAmount = [];
                        $TaxYear = $taxinstallment_array[$k]->TaxYear;
                        $taxdue = $this->Order_reports_model->GetTaxDueDate($OrderUID, $TaxCert, $TaxYear);

                        foreach($taxdue as $key => $value) {
                            $DueAmount[] = $value->TotalPaidAmt;
                            $GrossAmount[] = $value->TotalGrossAmt;
                        }

                        $TotalAmountPaid = array_sum($DueAmount);
                        $TotalGrossAmount = array_sum($GrossAmount);
                        $TotalBaseAmt = $taxinstallment_array[$k]->TotalBaseAmt;

                       /* if ($TotalAmountPaid == 0) {
                            $TaxDueAmount = 0;
                        } else {*/
                            $TaxDueAmount = $TotalGrossAmount - $TotalAmountPaid;
                        /*}*/

                        $TaxYear = $taxinstallment_array[0]->TaxYear;
                        $BaseAmt = $taxinstallment_array[0]->TotalBaseAmt;
                        $NumBaseAmt = number_format($BaseAmt, 2);
                    }

                    $TotalDueAmount[] = $TaxDueAmount;
                    $TotalDue = array_sum($TotalDueAmount);
                    $NumTotalDue = number_format($TotalDue, 2);
                }

                $TotalTaxBaseAmt[] = $BaseAmt;
                $TotalTaxDueAmt[] = $TotalDue;

                $taxcert_html.= '<tr class="br-black br-b-trans br-t-trans"> <td >
                <p
                class = "td-ta-l" > '.$TaxDocumentTypeName.' </p> </td> <td class = "td-ta-r" >
                <p
                class = "bold td-ta-r" > '.$TaxYear.' </p> </td> <td class = "td-ta-r" >
                <p
                class = "td-ta-r" > '.$NumBaseAmt.' </p> </td> <td class = "td-ta-r" >
                <p
                class = "td-ta-r" > '.$NumTotalDue.' </p> </td> <td class = "td-ta-r" >
                <p
                class = "td-ta-r" > '.$NumTotalDue.' </p> </td> </tr>';

            }

            $TotalWOExempt = array_sum($TotalWOExempt);
            $NumTotalWOExempt = number_format($TotalWOExempt, 2);

            $TotalTaxBaseAmt = array_sum($TotalTaxBaseAmt);
            $NumTotalTaxBaseAmt = number_format($TotalTaxBaseAmt, 2);
            $TotalTaxDueAmt = array_sum($TotalTaxDueAmt);
            $NumTotalTaxDueAmt = number_format($TotalTaxDueAmt, 2);

            $taxcert_html.= '<tr class="br-black br-t-trans"> <td >
            <p
            class = "td-ta-l" > TOTAL TAX </p> </td> <td class = "td-ta-r" >
            <p
            class = "bold td-ta-r" > </p> </td> <td class = "td-ta-r" >
            <p
            class = "bold td-ta-r" > '.$NumTotalTaxBaseAmt.' </p> </td> <td class = "td-ta-r" >
            <p
            class = "bold td-ta-r" > '.$NumTotalTaxDueAmt.' </p> </td> <td class = "td-ta-r" >
            <p
            class = "bold td-ta-r" > '.$NumTotalTaxDueAmt.' </p> </td> </tr> </tbody>
            </table>
            </div>
            </div>
            </div>
            </div>';

        }

        $LegalDesc = $this->Order_reports_model->GetPropertyLegalDesc($OrderUID);

        $LegalDescription = $LegalDesc->LegalDescription;
        //$LegalDescription = nl2br($LegalDesc->LegalDescription);
        /*if($LegalDescription)
        {
            $LegalDescription = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Legal Description</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;'.nl2br($LegalDescription).' </td></tr></table>';
        }else{
            $LegalDescription = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Legal Description</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;None </td></tr></table>';
        }    */    

        $Taxcertificate ='';
        $TaxSection ='';
        
        if($TaxCertificateRequired == 'Seperate Report' && in_array('3', $workflowModuleArray))
        {
            $Taxcertificate .= '<div class="wrapper"> <div style="width: 80%; padding: 0pt 50pt;"> <h2 style="text-align: center;font-size: 14pt;">Tax Certificate</h2> </div><p class="blur text-center" style="font-size: 10pt;font-weight: bold;margin-top: 10px;">Order Information</p><div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tbody> <tr style="height: 23px;"> <td class="td-bd" style="width: 32%; height: 23px;" rowspan="4">%%CustomerName%% <br>%%CustomerAddress1%% %%CustomerAddress2%% %%CustomerCountyName%% %%CustomerCityName%% %%CustomerStateCode%% %%CustomerZipCode%%</td><td class="td-bd text-center bold" style="width: 15%; height: 23px;">Ordered Date</td><td class="td-bd text-center" style="width: 20%; height: 23px;">%%OrderDate%%</td><td class="td-bd text-center bold" style="width: 13%; height: 30px;" rowspan="2">Order #</td><td class="td-bd text-center" style="width: 13%; height: 23px;" rowspan="2">%%Ordernumber%%</td></tr><tr style="height: 23px;"> <td class="td-bd text-center bold" style="width: 15%; height: 23px;">County</td><td class="td-bd text-center" style="width: 20%; height: 23px;">%%Countyname%% </td></tr><tr style="height: 23px;"> <td class="td-bd text-center bold" style="width: 15%; height: 23px;">State</td><td class="td-bd text-center" style="width: 20%; height: 23px;">%%State_code%%</td><td class="td-bd text-center bold" style="width: 13%; height: 23px; " rowspan="2">Loan #</td><td class="td-bd text-center" style="width: 13%; height: 23px;" rowspan="2">%%Loannumber%%</td></tr></tbody> </table>';

            if($order_details->PropertyStateCode == 'TX'){
                $Taxcertificate .= '<p class="blur text-center" style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;">Property Information</p><div style="padding-top: 10pt;"><table style="width: 100%;" cellspacing="0"><tr><td class="td-bd text-center bold" style="width:16.5%;">Block/Lot</td><td class="td-bd text-center" style="width:17.8%;">%%Lot%%</td><td class="td-bd text-center bold" style="width:16.5%;">Subdivision</td><td class="td-bd text-center" colspan="3">%%SubDivisionName%%</td></tr>   <tr><td class="td-bd text-center bold" style="width:16.5%;">Property Tax</td><td class="td-bd text-center" style="width:17.8%;">%%ReferTaxSection%%</td><td class="td-bd text-center bold" style="width:16.5%;">Tax Year</td><td class="td-bd text-center" style="width:17.8%;">%%LatestTaxYear%%</td><td class="td-bd text-center bold" style="width:16.5%;">Delinquent Tax</td><td class="td-bd text-center" style="width:17.8%;">%%DeliquentTax%%</td></tr><tr><td class="td-bd text-center bold" style="width: 14.11%;">Borrower</td><td class="td-bd text-center" style="width:27.6%;">%%Borrowers%%</td><td class="td-bd text-center bold" colspan="4">Assessment Information</td></tr><tr><td class="td-bd text-center bold" style="width:16.5%;">Attention</td><td class="td-bd text-center bold" style="width:16.5%;">%%AttentionName%%</td><td class="td-bd text-center bold" style="width:16.5%;">Land Value</td><td class="td-bd text-center" style="width:17.8%;">%%Land%%</td><td class="td-bd text-center bold" style="width:11.6%;">Year</td><td class="td-bd text-center" style="width:11.6%;">%%AssessedYear%%</td></tr><tr><td class="td-bd text-center bold" style="width: 14.11%;" rowspan="3">Property Address</td><td class="td-bd text-center" style="width: 27.6%;" rowspan="3">%%Propertyaddress1%% %%Propertyaddress2%% %%Cityname%% %%Statecode%% %%Zip%%</td><td class="td-bd text-center bold" style="width:16.5%;">Improvement Value</td><td class="td-bd text-center" style="width:11.6%;">%%Buildings%%</td><td class="td-bd text-center bold" style="width:23.2%;" colspan="2">Total Assessment</td></tr><tr><td class="td-bd text-center bold" style="width:16.5%;">Agricultural Value</td><td class="td-bd text-center" style="width:17.8%;">%%Agricultural%%</td><td class="td-bd text-center" style="width:23.2%;vertical-align: middle;" colspan="2" rowspan="2">%%TotalValue%%</td></tr><tr><td class="td-bd text-center bold" style="width:16.5%;">Exemptions</td><td class="td-bd text-center" style="width:17.8%;">%%Exempt%%</td></tr><tr><td class="td-bd text-center bold" style="width:16.5%;">Comments</td><td class="td-bd %%alignment%%" colspan="5" style="">%%AssessmentValue%%</td></tr></table></div>';

                //$Taxcertificate.=$LegalDescription;
                $Taxcertificate.=$taxcert_html;

            } else {
                $Taxcertificate .= '<p class="blur text-center" style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;">Property Information</p><div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="td-bd text-center bold" style="width: 14.11%;;" rowspan="2">Borrower</td><td class="td-bd text-center" style="width:27.6%;;" rowspan="2">%%Borrowers%%</td><td class="td-bd text-center bold" colspan="4">Assessment Information</td></tr><tr> <td class="td-bd text-center bold" style="width:16.5%;">Land Value</td><td class="td-bd text-center" style="width:17.8%;">%%Land%%</td><td class="td-bd text-center bold" style="width:11.6%;">Year</td><td class="td-bd text-center" style="width:11.6%;">%%AssessedYear%%</td></tr><tr> <td class="td-bd text-center bold" style="width: 14.11%;" rowspan="3">Property Address</td><td class="td-bd text-center" style="width: 27.6%;" rowspan="3">%%Propertyaddress1%% %%Propertyaddress2%% %%Cityname%% %%Statecode%% %%Zip%%</td><td class="td-bd text-center bold" style="width:16.5%;">Improvement Value</td><td class="td-bd text-center" style="width:11.6%;">%%Buildings%%</td><td class="td-bd text-center bold" style="width:23.2%;" colspan="2">Total Assessment</td></tr><tr> <td class="td-bd text-center bold" style="width:16.5%;">Agricultural Value</td><td class="td-bd text-center" style="width:17.8%;">%%Agricultural%%</td><td class="td-bd text-center" style="width:23.2%;vertical-align: middle;" colspan="2" rowspan="2">%%TotalValue%%</td></tr><tr> <td class="td-bd text-center bold" style="width:16.5%;">Exemptions</td><td class="td-bd text-center" style="width:17.8%;">%%Exempt%%</td></tr><tr> <td class="td-bd text-center bold" style="width:16.5%;">Comments</td><td class="td-bd %%alignment%%" colspan="5" style="">%%AssessmentValue%%</td></tr></table> </div>';
            }

            $Taxcertificate .= '<div style="padding-top: 8pt;"></div><div class="tordertaxcerts"> <table style="width: 100%;margin-top: 10pt;border:0px;" cellspacing="0"> <thead> <tr> <th class="blur text-center" colspan="4"> <p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Tax Information</p></th> </tr></thead> <tbody> <tr style=""> <td width="14.7%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="bold">Taxing Entity</p></td><td width="27.6%" class="td-bd text-center" style=""> <p style="font-size: 8pt;text-transform: uppercase;" class="text-center">%%TaxDocumentTypeName%%</p></td><td width="14.7%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="bold">&nbsp;Parcel ID&nbsp;</p></td><td width="43.5%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="text-center">%%ParcelNumber%%</p></td></tr><tr style=""> <td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Tax Installment</p></td><td width="27.6%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center">%%TaxBasisName%%</p></td><td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Property Type</p></td><td width="43.5%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center">%%PropertyClassName%%</p></td></tr></tbody> </table> <table style="width: 100%;border: 0.01em solid grey;" cellspacing="0"> <tr style=""> <td width="10.4%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Tax Year</p></td><td width="18.6%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center bold">Tax Installments</p></td><td width="13.5%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Base Amount</p></td><td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center bold">Tax Status</p></td><td width="21.8%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Paid Amount</p></td><td width="21.8%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Due/Paid Date</p></td></tr>%%taxinstallment%% </table> <table style="width: 100%; margin-top: 10pt;" cellspacing="0">%%TaxExmp%%</table> <table style="width: 100%; margin-top: 10pt;" cellspacing="0"> <tbody> <tr style=""> <td class="td-bd bold text-center" style="width: 25%;font-size: 8pt;">Total Delinquent Payoff</td><td class="td-bd text-center" style="width: 25%;font-size: 8pt;">%%AmountDelinquent%%</td><td class="td-bd bold text-center" style="width: 25%;font-size: 8pt;">Good Through Date</td><td class="td-bd text-center" style="width: 25%;font-size: 8pt;">%%GoodThroughDate%%</td></tr><tr style=""> </tr><tr style=""> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 7pt;" colspan="1">Comments</td><td class="td-bd %%taxalignment%%" style="font-size: 7pt;" colspan="3">%%TaxComments%%</td></tr></tbody> </table> <div style="page-break-inside: avoid;"> <table style="width: 100%;margin-top: 10pt;" cellspacing="0"> <thead> <tr> <th class="blur text-center" colspan="14"> <p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Tax Collector Information</p></th> </tr></thead> <tbody> <tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;">Payable to</td><td class="td-bd text-center" colspan="13" style="width: 72.3774%;font-size: 8pt;">%%TaxPayable%%</td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;">Address</td><td class="td-bd text-center" colspan="13" style="width: 72.3774%;font-size: 8pt;">%%PaymentAddrLine1%% %%PaymentAddrLine2%% %%PaymentCity%% %%PaymentState%% %%PaymentZipCode%%</td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;">Phone #</td><td class="td-bd text-center" colspan="4" style=""> <p style="font-size: 8pt;" class="text-center">%%CollectorPhoneNumber%%</p></td><td class="td-bd text-center" colspan="2" style=""> <p style="font-size: 8pt;" class="bold">Web Address</p></td><td class="td-bd text-center" colspan="7" style=""> <p style="font-size: 8pt;color: #0066ff" class="text-center">%%WebsiteAddr%%</p></td></tr></tbody> </table> </div></div></div></div>';
            array_push($keys, '%%TaxCert%%');
            array_push($values, $Taxcertificate);
            array_push($keys, '%%TaxSection%%');
            array_push($values, ' ');
        }
        else if($TaxCertificateRequired == 'No Tax Certificate')
        {
            $this->save_view($OrderUID);
            array_push($keys, '%%TaxSection%%');
            array_push($values, ' ');
            array_push($keys, '%%TaxCert%%');
            array_push($values, ' ');
            array_push($keys,'%%taxpagebreak%%');
            array_push($values, ' ');
            array_push($keys,'%%TaxDisclaimerNote%%');
            array_push($values, 'noheaderfooter');

        }
        else{

            if($order_details->PropertyStateCode == 'TX'){
                //$TaxSection.=$LegalDescription;
                $TaxSection.=$taxcert_html;
            }

            $TaxSection.=' <div class="tordertaxcerts"> <table style="width: 100%;margin-top: 10pt;border:0px;" cellspacing="0"> <thead> <tr><th class="blur text-center" colspan="4"><p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Tax Information</p></th></tr></thead> <tbody> <tr style=""> <td width="14.7%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="bold">Taxing Entity</p></td><td width="27.6%" class="td-bd text-center" style=""> <p style="font-size: 8pt;text-transform: uppercase;" class="text-center">%%TaxDocumentTypeName%%</p></td><td width="14.7%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="bold">&nbsp;Parcel ID&nbsp;</p></td><td width="43.5%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="text-center">%%ParcelNumber%%</p></td></tr><tr style=""> <td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Tax Installment</p></td><td width="27.6%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center">%%TaxBasisName%%</p></td><td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Property Type</p></td><td width="43.5%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center">%%PropertyClassName%%</p></td></tr></tbody> </table> <table style="width: 100%;border: 0.01em solid grey;" cellspacing="0"> <tr style=""> <td width="10.4%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Tax Year</p></td><td width="18.6%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center bold">Tax Installments</p></td><td width="13.5%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Base Amount</p></td><td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center bold">Tax Status</p></td><td width="21.8%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Paid Amount</p></td><td width="21.8%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Due/Paid Date</p></td><!-- <td class="td-bd text-center" colspan="3" style=""> <p style="font-size: 8pt;" class=" text-center bold">Good Through Date</p></td>--> </tr>%%taxinstallment%% </table> <table style="width: 100%; margin-top: 10pt;" cellspacing="0">%%TaxExmp%%</table> <table style="width: 100%; margin-top: 10pt;" cellspacing="0"> <tbody> <tr style=""> <td class="td-bd bold text-center" style="width: 25%;font-size: 8pt;" >Total Delinquent Payoff</td><td class="td-bd text-center" style="width: 25%;font-size: 8pt;" >%%AmountDelinquent%%</td><td class="td-bd bold text-center" style="width: 25%;font-size: 8pt;" >Good Through Date</td><td class="td-bd text-center" style="width: 25%;font-size: 8pt;" >%%GoodThroughDate%%</td></tr><tr style=""> </tr><tr style=""> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;" colspan="1">Comments</td><td class="td-bd %%taxalignment%%" style="font-size: 8pt;" colspan="3" >%%TaxComments%%</td></tr></tbody> </table> <div style="page-break-inside: avoid;"> <table style="width: 100%;margin-top: 10pt;" cellspacing="0"> <thead> <tr><th class="blur text-center" colspan="14"><p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Tax Collector Information</p></th></tr></thead> <tbody> <tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;" >Payable to</td><td class="td-bd text-center" colspan="13" style="width: 72.3774%;font-size: 8pt;" >%%TaxPayable%%</td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;" >Address</td><td class="td-bd text-center" colspan="13" style="width: 72.3774%;font-size: 8pt;" >%%PaymentAddrLine1%% %%PaymentAddrLine2%% %%PaymentCity%% %%PaymentState%% %%PaymentZipCode%%</td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;" >Phone #</td><td class="td-bd text-center" colspan="4" style=""> <p style="font-size: 8pt;" class="text-center">%%CollectorPhoneNumber%%</p></td><td class="td-bd text-center" colspan="2" style=""> <p style="font-size: 8pt;" class="bold">Web Address</p></td><td class="td-bd text-center" colspan="7" style=""> <p style="font-size: 8pt;color:#0066ff" class="text-center">%%WebsiteAddr%%</p></td></tr></tbody> </table> </div></div>';
            array_push($keys, '%%TaxSection%%');
            array_push($values, $TaxSection);
            array_push($keys, '%%TaxCert%%');
            array_push($values, ' ');
            array_push($keys,'%%taxpagebreak%%');
            array_push($values, ' ');
            array_push($keys,'%%TaxDisclaimerNote%%');
            array_push($values, 'noheaderfooter');
        }
        //Tax Certificate

        $ReportHeading = $order_details->ReportHeading;
        if($ReportHeading)
        {
            array_push($keys, '%%ReportHeading%%');
            array_push($values, $ReportHeading);
        }
        else{
            array_push($keys, '%%ReportHeading%%');
            array_push($values, 'Property Report');
        }

        $base_url = FCPATH;
        $Attention =  $order_details->AttentionName;
        if($Attention)
        {
            $AttentionName = $Attention;
        }
        else{

            $AttentionName = '-';
        }
        $CustomerPContactMobileNo = $order_details->CustomerPContactMobileNo;
        $CustomerAddress1 = $order_details->CustomerAddress1;

        if($CustomerAddress1)
        {
            $CustomerAddress1 =  $order_details->CustomerAddress1;
        }
        else{
            $CustomerAddress1 = ' ';
        }
        $CustomerAddress2 = $order_details->CustomerAddress2;
        if($CustomerAddress2)
        {
            $CustomerAddress2 =  $order_details->CustomerAddress2.',';
        }
        else{
            $CustomerAddress2 = ' ';
        }
        $CustomerNumber = $order_details->CustomerNumber;
        if($CustomerNumber)
        {
            $CustomerNumber = $order_details->CustomerNumber.'/';
        }
        else{
            $CustomerNumber = '';
        }
        $CustomerName = $order_details->CustomerName;
        if($CustomerName)
        {
            $CustomerName = $order_details->CustomerName;
        }
        else{
            $CustomerName = ' ';
        }
        $CustomerPContactName =  $order_details->CustomerPContactName;
        if($CustomerPContactName)
        {
            $CustomerPContactName =  $order_details->CustomerPContactName;
        }
        else{
            $CustomerPContactName = ' ';
        }
        $CustomerStateCode =  $order_details->CustomerStateCode;
        if($CustomerStateCode)
        {
           $CustomerStateCode =  $order_details->CustomerStateCode.'-'; 
            $Customer_State_Code =  $order_details->CustomerStateCode; 
        }
        else
        {
            $CustomerStateCode =  ' '; 
        }
        $MiscellaneousNotes = $order_details->MiscellaneousNotes;
        if($MiscellaneousNotes)
        {
            $Miscellaneous = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Miscellaneous Information</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;'.$MiscellaneousNotes.' </td></tr></table>';
        }else{
            $Miscellaneous = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Miscellaneous Information</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;None </td></tr></table>';
        }
        $CustomerCountyName =  $order_details->CustomerCountyName;
        if($CustomerCountyName)
        {
            $CustomerCountyName =  $order_details->CustomerCountyName.',';
            $Customer_County_Name =  $order_details->CustomerCountyName;
        }
        else{
            $CustomerCountyName = '-';
        }
        $CustomerZipCode = $order_details->CustomerZipCode;
        if($CustomerZipCode)
        {
            $CustomerZipCode = $order_details->CustomerZipCode;
        }
        else{
            $CustomerZipCode = ' ';
        }
        $CustomerCityName = $order_details->CustomerCityName;
        if($CustomerCityName)
        {
            $CustomerCityName = $order_details->CustomerCityName.',';
        }
        else{
             $CustomerCityName = ' ';
        }
        $Mortgagee = $order_details->Mortgagee;
        $OrderNumber = $order_details->OrderNumber;
        $LoanNumber = $order_details->LoanNumber;
        if($LoanNumber)
        {
            $LoanNumber = $order_details->LoanNumber;
        }
        else{
            $LoanNumber = '-';
        }
        $OwnerName = $order_details->OwnerName;
        $CountyName =  $order_details->CountyName;
        $PropertyAddress1 = $order_details->PropertyAddress1;
        $PropertyAddress2 = $order_details->PropertyAddress2.',';
        $CityName = $order_details->CityName.',';
        $StateName = $order_details->StateName;
        $StateCode = $order_details->StateCode;
        if($StateCode)
        {
            $StateCode =  $order_details->StateCode;
        }
        else{
            $StateCode = '-';
        }
        $DisclaimerResult = $this->Order_reports_model->get_DisclaimerNote($order_details->StateCode);
        if($DisclaimerResult->DisclaimerNote){
            $DisclaimerNote = $DisclaimerResult->DisclaimerNote;
        }
        else{
             $DisclaimerNote = '-';
        }
        if($DisclaimerResult->StateEmail){
            $DisclaimerStateEmail = $DisclaimerResult->StateEmail;
        }
        else{
            $DisclaimerStateEmail = '-';
        }
        if($DisclaimerResult->StateWebsite){
            $DisclaimerStateWebsite = $DisclaimerResult->StateWebsite;
        }
        else{
            $DisclaimerStateWebsite = '-';
        }
        if($DisclaimerResult->StatePhoneNumber){
            $DisclaimerStatePhoneNumber = $DisclaimerResult->StatePhoneNumber;
        }
        else{
            $DisclaimerStatePhoneNumber = '-';
        }
        if($DisclaimerResult->LegalDisclaimerNote == 1 && $DisclaimerResult->TaxDisclaimerNote == 1)
        {
            $taxpagebreak = '<div style="page-break-after: always;"></div>';
            $pagebreak =   '<div style="page-break-after: auto;"></div>';
            $LegalDisclaimerNote = 'displayfooter';
            $TaxDisclaimerNote = 'displayfooter';
        }
        elseif($DisclaimerResult->LegalDisclaimerNote == 0 && $DisclaimerResult->TaxDisclaimerNote == 1){

 
                $LegalDisclaimerNote = 'noheaderfooter';
                $TaxDisclaimerNote = 'displayfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = '<div style="page-break-after: auto;"></div>';
            
                             
        }
        elseif($DisclaimerResult->LegalDisclaimerNote == 1 && $DisclaimerResult->TaxDisclaimerNote == 0)
        {
                $LegalDisclaimerNote = 'displayfooter';
                $TaxDisclaimerNote = 'noheaderfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = '<div style="page-break-after: auto;"></div>';
        }
        else{
            $LegalDisclaimerNote = 'noheaderfooter';
                $TaxDisclaimerNote = 'noheaderfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = '<div style="page-break-after: always;"></div>';
        }
        $SearchDate = $order_details->SearchThroDate;
        if($SearchDate == '0000-00-00')
        {
            $SearchThroDate =  '-';
        }
        else{
            $SearchThroDate =  date('m/d/Y', strtotime($SearchDate));
        }
        $SearchFrom = $order_details->SearchFromDate;
        if($SearchFrom == '0000-00-00 00:00:00' || $SearchFrom == '')
            {
                $SearchFromDate = '-';
            }
            else
            {
                $SearchFromDate = date('m/d/Y', strtotime($SearchFrom));
            }
            $SearchAsOf = $order_details->SearchAsOfDate;
            if($SearchAsOf == '0000-00-00 00:00:00' || $SearchAsOf == '')
                {
                    $SearchAsOfDate = '-';
                }
                else
                {
                    $SearchAsOfDate = date('m/d/Y', strtotime($SearchAsOf));
                }
                $ZipCode = $order_details->PropertyZipcode;
                $address = $this->Order_reports_model->get_Address($OrderUID);
                foreach ($address as $key => $data) {

                    $AssessedCountyName = $data->AssessedCountyName;
                    $USPSCountyName = $data->USPSCountyName;
                }
                $ImageUrl = base_url().'assets/img/sourcepoint.png';
                $GoogleMapAddress = $PropertyAddress1.' '.$PropertyAddress2.' '.$CityName.' '.$StateName.' '.$ZipCode;
                $Mort = array('CustomerName'=>$CustomerNumber.$CustomerName,'AttentionName'=>$AttentionName,'CustomerAddress1'=>$CustomerAddress1,'CustomerAddress2'=>$CustomerAddress2,'CustomerCityName'=>$CustomerCityName,'CustomerCountyName'=>$CustomerCountyName,'CustomerStateCode'=>$CustomerStateCode,'CustomerPContactName'=>$CustomerPContactName,'OrderMortgagee'=>$Mortgagee,'Ordernumber'=>$OrderNumber,'Loannumber'=>$LoanNumber,'Ownername'=>$OwnerName,'Countyname'=>$CountyName,'County_name'=>$CountyName.',','Propertyaddress1'=>$PropertyAddress1,'Propertyaddress2'=>$PropertyAddress2,'Cityname'=>$CityName,'Statecode'=>$StateCode.'-','Zip'=>$ZipCode,'SearchThroDate'=>$SearchThroDate,'GoogleMapAddress'=>$GoogleMapAddress,'SearchFromDate'=>$SearchFromDate,'SearchAsOfDate'=>$SearchAsOfDate,'DisclaimerNote'=>$DisclaimerNote,'Url'=>$base_url,'CustomerZipCode'=>$CustomerZipCode,'OrderDate'=>$OrderDate,'DisclaimerStateEmail'=>$DisclaimerStateEmail,'DisclaimerStateWebsite'=>$DisclaimerStateWebsite,'DisclaimerStatePhoneNumber'=>$DisclaimerStatePhoneNumber,'LegalDisclaimerNote'=>$LegalDisclaimerNote,'TaxDisclaimerNote'=>$TaxDisclaimerNote,'taxpagebreak'=>$taxpagebreak,'pagebreak'=>$pagebreak,'Customer_State_Code'=>$Customer_State_Code,'Customer_County_Name'=>$Customer_County_Name,'State_code'=>$StateCode,'ImageUrl'=>$ImageUrl,'Miscellaneous'=>$Miscellaneous, 'taxcert_html'=>$taxcert_html,'sep_legaldesc'=>$sep_legaldesc, 'sep_taxcert_html'=>$sep_taxcert_html,'NumTotalWOExempt'=>$NumTotalWOExempt,'CurrentDateTime'=>date('m/d/Y',strtotime("now")),'CurrentMonth'=>date('m/Y', strtotime('now')),'NextMonth'=>date('m/Y', strtotime('+1 month')));


                foreach ($Mort as $key => $value) 
                {

                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
        //Orders

        //Heading
        //Chain of Title
                if($this->Order_reports_model->get_torderdeeds($OrderUID))
                {
                    $DeedHeading = '<p class="blur text-center" style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;">Chain of Title</p>';
                    array_push($keys, '%%DeedHeading%%');
                    array_push($values, $DeedHeading);
                }
                else
                {

                    array_push($keys, '%%DeedHeading%%');
                    array_push($values, ' ');
                }
        //Chain of Title
        //Tax Heading
                if($this->Order_reports_model->get_tordertaxcerts($OrderUID))
                {
                    $TaxHeading = '<tr><td class="blur text-center" colspan="6"><p style="font-size: 10pt;width:100%;font-weight: bold;margin-top: 5pt;text-align: center;">Tax Information</p></td></tr>';
                    array_push($keys, '%%TaxHeading%%');
                    array_push($values, $TaxHeading);
                }
                else
                {
                    array_push($keys, '%%TaxHeading%%');
                    array_push($values, ' ');
                }
        //Tax Heading

        //Heading
        //Address
         include APPPATH .'modules/Order_reports/views/addressvariance.php';
        //Address
        //Legal Description
                $legal_information = $this->Order_reports_model->get_LegalDescription($OrderUID);
                if($legal_information)
                {

                    foreach ($legal_information as $dm) 
                    {

                        $LegalDescr = str_replace('  ', ' &nbsp;', nl2br(strtoupper($dm->LegalDescription)));
                        if($LegalDescr)
                        {
                            array_push($keys, '%%LegalDescr%%');
                            array_push($values, $LegalDescr);
                        }
                        else{
                            array_push($keys, '%%LegalDescr%%');
                            array_push($values, ' ');
                        }

                        foreach($dm as $cm => $vm)
                        {
                            array_push($keys, '%%'.$cm.'%%');
                            array_push($values, $vm);
                        }

                    } 
                }
                else{

                    array_push($keys, '%%LegalDescr%%');
                    array_push($values, ' ');
                }

        //Legal Description
        //Order Assessment
                $order_assessment = $this->Order_reports_model->get_OrderAssessment($OrderUID);
                if($order_assessment)
                {

                    foreach ($order_assessment as $data_orderass_info) 
                    {
                        $AssessedYear = $data_orderass_info->AssessedYear;

                        if($AssessedYear)
                        {
                            array_push($keys, '%%AssessedYear%%');
                            array_push($values, $AssessedYear);
                        }
                        else{
                            array_push($keys, '%%AssessedYear%%');
                            array_push($values, '-');

                        }
                        $Agricultural = $data_orderass_info->Agriculture;

                        if($Agricultural)
                            
                        {   
                        $Agricultural = '$'.$Agricultural;
                        array_push($keys, '%%Agricultural%%');
                        array_push($values, $Agricultural);
                        }
                        else
                        {
                        array_push($keys, '%%Agricultural%%');
                        array_push($values, '-');

                    }
                    $TotalValue = $data_orderass_info->TotalValue;

                    if($TotalValue)
                    {
                        $TotalVal = '$'.$TotalValue;
                        array_push($keys, '%%TotalValue%%');
                        array_push($values, $TotalVal);
                    }
                    else{
                        array_push($keys, '%%TotalValue%%');
                        array_push($values, '-');

                    }
                    $Landstr = $data_orderass_info->Land;
                    if($Landstr)
                    {
                        $Landltrim = ltrim($Landstr, '$');
                        $LandRepl = str_replace(",","",$Landltrim);
                        $Lan = substr($LandRepl, 0,-3);
                        $Land = '$'.number_format($Lan,2);
                    }
                    else{
                        $Land = '-';
                    }
                    $Buildingsstr = $data_orderass_info->Buildings;
                    if($Buildingsstr)
                    {

                        $Buildingsltrim = ltrim($Buildingsstr, '$');
                        $BuildingsRepl = str_replace(",","",$Buildingsltrim);
                        $Build = substr($BuildingsRepl, 0,-3);
                        $Buildings = '$'.number_format($Build,2);
                    }
                    else{
                        $Buildings = '-';
                    }
                    $AssessmentValue = $data_orderass_info->AssessmentValue;
                    if($AssessmentValue)
                    {
                        $AssessmentValue = $AssessmentValue;
                        array_push($keys, '%%AssessmentValue%%');
                        array_push($values, $AssessmentValue);
                        array_push($keys, '%%alignment%%');
                        array_push($values, 'text-left');
                    }
                    else{
                        array_push($keys, '%%AssessmentValue%%');
                        array_push($values, '-');
                        array_push($keys, '%%alignment%%');
                        array_push($values, 'text-center');

                    }
                    $Value = array('TotalValue'=>$TotalValue,'Buildings'=>$Buildings,'Land'=>$Land,'Agricultural'=>$Agricultural,'AssessmentValue'=>$AssessmentValue);
                    foreach ($Value as $key => $value) 
                    {
                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                    }
                } 
            }
            else{


                $TaxArray = array('AssessedYear','Land','Buildings','Agricultural','TotalValue');
                foreach($TaxArray as $col)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, '-');
                }
                 array_push($keys, '%%AssessmentValue%%');
                 array_push($values, '-');
                 array_push($keys, '%%alignment%%');
                 array_push($values, 'text-center');

            }

            /* Latest Tax Starts */

            $tax_information = $this->Order_reports_model->get_tax_latest($OrderUID);
            if($tax_information)
            {
                        //Property tax in ISGN Report
                foreach ($tax_information as $data) 
                {
                            //DeliquentTax
                    if($data->TaxStatusName)
                    {
                        if($data->TaxStatusName == 'Delinquent')
                        {
                            $DeliquentTax = array('DeliquentTax'=>'Yes');
                            foreach($DeliquentTax as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        else
                        {
                            $DeliquentTax = array('DeliquentTax'=>'No');
                            foreach($DeliquentTax as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                    }
                    else{
                        array_push($keys, '%%DeliquentTax%%');
                        array_push($values, '-');
                    }

                    $LatestTaxYear = $data->LatestTaxYear;
                    if($LatestTaxYear)
                    {

                        array_push($keys, '%%LatestTaxYear%%');
                        array_push($values, $LatestTaxYear);
                        array_push($keys, '%%ReferTaxSection%%');
                        array_push($values, 'REFER TAX SECTION');
                    }
                    else
                    {
                        array_push($keys, '%%LatestTaxYear%%');
                        array_push($values, '-');
                        array_push($keys, '%%ReferTaxSection%%');
                        array_push($values, '-');
                    }
                }
            }
            else
            {
                $TaxArray = array('LatestTaxYear','DeliquentTax','ReferTaxSection');
                foreach($TaxArray as $col)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, '-');
                }
            }

            /* Latest Tax Ends */

            /* Property Information Starts */

            $property_information = $this->Order_reports_model->getPropertyInformation($OrderUID);
            if($property_information)
            {
                foreach ($property_information as $key => $data) 
                {
                    $MaritalStatusName = $data->MaritalStatusName;
                    if($MaritalStatusName)
                    {
                        array_push($keys, '%%MaritalStatusName%%');
                        array_push($values, $MaritalStatusName);
                    }
                    else{
                        array_push($keys, '%%MaritalStatusName%%');
                        array_push($values, ' ');
                    }
                    $OwnerName = $data->OwnerName;
                    if($OwnerName)
                    {
                        array_push($keys, '%%OwnerName%%');
                        array_push($values, $OwnerName);
                    }
                    else{
                        array_push($keys, '%%OwnerName%%');
                        array_push($values, '-');
                    }
                    $SubDivisionName = $data->SubDivisionName;
                    if($SubDivisionName)
                    {
                        array_push($keys, '%%SubDivisionName%%');
                        array_push($values, $SubDivisionName);
                    }
                    else{
                        array_push($keys, '%%SubDivisionName%%');
                        array_push($values, '-');
                    }

                    $sdMapNo = $data->sdMapNo;
                    if($sdMapNo)
                    {
                        array_push($keys, '%%sdMapNo%%');
                        array_push($values, $sdMapNo);
                    }
                    else{
                        array_push($keys, '%%sdMapNo%%');
                        array_push($values, '-');
                    }
                    $dSection = $data->dSection;
                    if($dSection)
                    {
                        array_push($keys, '%%dSection%%');
                        array_push($values, $dSection);
                    }
                    else{
                        array_push($keys, '%%dSection%%');
                        array_push($values, '-');
                    }
                    $Township = $data->Township;
                    if($Township)
                    {
                        array_push($keys, '%%Township%%');
                        array_push($values, $Township);
                    }
                    else{
                        array_push($keys, '%%Township%%');
                        array_push($values, '-');
                    }
                    $APN = $data->APN;
                    if($APN)
                    {
                        array_push($keys, '%%APN%%');
                        array_push($values, $APN);
                    }
                    else{
                        array_push($keys, '%%APN%%');
                        array_push($values, '-');
                    }
                    $Lot = $data->Lot;
                    $Block = $data->Block;
                    if($Lot && $Block)
                    {
                      array_push($keys, '%%Lot%%');
                      array_push($values, $Block.'/'.$Lot);
                  }
                  
                  else if($Lot)
                  {
                    array_push($keys, '%%Lot%%');
                    array_push($values, $Lot);
                }
                
                else if($Block)
                {
                    array_push($keys, '%%Lot%%');
                    array_push($values, $Block);
                }
                else{
                    array_push($keys, '%%Lot%%');
                    array_push($values, '-');
                }
            }
            }
            else
            {
                $TaxArray = array('Exempt','APN','Lot','Block','SubDivisionName','sdMapNo','dSection','Township','OwnerName');
                foreach($TaxArray as $col)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, '-');
                }
                array_push($keys, '%%MaritalStatusName%%');
                array_push($values, ' ');
            }

            /* Property Information Ends */

        //Get Exemption
            $exemptions = $this->Order_reports_model->get_taxExemption($OrderUID);
            if($exemptions)
            {
                $Exempt = array('Exempt'=>'Yes');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            else
            {
                $Exempt = array('Exempt'=>'No');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
        //Get Exemption

        //Get Borrowers
            $borrowers = $this->Order_reports_model->getBorrowers($OrderUID);
            $Count =  count($borrowers);
            if($Count > 1){

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    if($value->PRName !=''){

                    $borrower .=  trim($value->PRName.',');
                    }
                }
                $borrowers =  rtrim($borrower,",");
                $Array = explode(",", $borrowers);
                $LastArrayValue =  end($Array);
                
                if(count($Array) > 1 ){
                    array_pop($Array);
                    $BorrowerName = implode(", &nbsp;", $Array).' and '.$LastArrayValue;

                }else{
                    
                    $BorrowerName = implode(", &nbsp;", $Array);
                }
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
            else{

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    $borrower .=  $value->PRName.',';

                }
                $BorrowerName =  rtrim($borrower,",");
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
        //Get Borrowers

            $output = str_replace($keys,$values, $output);

            $doc->load($output);
        //Property Starts
            preg_match_all('/<div class=\"torderproperty\">(.*?)<\/div>/s',$output,$torderproperty);
        // $getCounts = $this->Order_reports_model->getCounts($OrderUID);
        // $MortgageCount= $this->Order_reports_model->getMortgageCount($OrderUID);
            $MortgageCount =  $this->Order_reports_model->getMortgageCount($OrderUID);
if($MortgageCount == 0){ $MortgageCount = "NA"; }
            array_push($keys, '%%MortgageCount%%');
            array_push($values, $MortgageCount);
            $LienCount =  $this->Order_reports_model->getLienCount($OrderUID);
if($LienCount == 0){ $LienCount = "NA"; }
            array_push($keys, '%%LienCount%%');
            array_push($values, $LienCount);
            $JudgementCount =  $this->Order_reports_model->getJudgementCount($OrderUID);
if($JudgementCount == 0){ $JudgementCount = "NA"; }
            array_push($keys, '%%JudgementCount%%');
            array_push($values, $JudgementCount);
            $GranteeGrantor =  $this->Order_reports_model->getGranteeGrantor($OrderUID);
            if($GranteeGrantor)
            {
                foreach ($GranteeGrantor as $col => $value) 
                {
                    $Grantee = $value->Grantee;
                    if($Grantee)
                    {
                        array_push($keys, '%%Grantee%%');
                        array_push($values, $Grantee);
                    }
                    else{
                        array_push($keys, '%%Grantee%%');
                        array_push($values, '-');
                    }
                    $Grantor = $value->Grantor;
                    if($Grantee){
                        array_push($keys, '%%Grantor%%');
                        array_push($values, $Grantor);
                    }
                    else{
                        array_push($keys, '%%Grantor%%');
                        array_push($values, '-');
                    }
                    $EstateInterestName = $value->EstateInterestName;
                    if($EstateInterestName)
                    {
                        array_push($keys, '%%EstateInterestName%%');
                        array_push($values, $EstateInterestName);
                    }
                    else{
                        array_push($keys, '%%EstateInterestName%%');
                        array_push($values, '-');
                    }
                    $TenancyName = $value->TenancyName;
                    if($TenancyName)
                    {
                        array_push($keys, '%%TenancyTypeName%%');
                        array_push($values, $TenancyName);
                    }
                    else{
                        array_push($keys, '%%TenancyTypeName%%');
                        array_push($values, '-');
                    }


                } 
            }
            else{
                array_push($keys, '%%Grantee%%');
                array_push($values, '-');
                array_push($keys, '%%Grantor%%');
                array_push($values, '-');
                array_push($keys, '%%EstateInterestName%%');
                array_push($values, '-');
                array_push($keys, '%%TenancyTypeName%%');
                array_push($values, '-');
            }

            $exemptions = $this->Order_reports_model->get_taxExemption($OrderUID);
            if($exemptions)
            {
                $Exempt = array('Exempt'=>'Yes');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            else
            {
                $Exempt = array('Exempt'=>'No');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }

            
            $torderproperty_table .= str_replace($keys, $values, $torderproperty[0][0]);
            foreach ($doc->find(".torderproperty") as $node ) 
            {
                $node->innertext = $torderproperty_table;
            }
        //Property Ends
        //Deed Starts
            preg_match_all('/<div class=\"torderdeeds\">(.*?)<\/div>/s',$output,$torderdeeds);
            $torderdeeds_table = '';
            $torderdeeds_array = array();
            $torderdeedsparties_array = array();
            $torder_deeds = $this->Order_reports_model->get_torderdeeds($OrderUID);
            $torderdeeds_array = $torder_deeds;
            $torderdeeds_array_count = count($torderdeeds_array);
            for ($i=0; $i < $torderdeeds_array_count; $i++) 
            { 
                $torderdeeds_array[$i]->deed_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($torderdeeds_array[$i] as $key => $value)
                {
                    $DocumentTypeName = $torderdeeds_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $DeedType = $torderdeeds_array[$i]->DeedType;
                            if($DeedType)
                            {
                                array_push($keys, '%%DeedDocumentTypeName%%');
                                array_push($values, $DeedType);
                            }
                            else{
                                array_push($keys, '%%DeedDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%DeedDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%DeedDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%DeedDocumentTypeName%%');
                        array_push($values, '-');
                    }
                    //Deed Date Format Change
                    $DDated = $torderdeeds_array[$i]->DeedDated;
                    if($DDated == '0000-00-00')
                    {
                        array_push($keys, '%%DeedDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $DeedDated =  date('m/d/Y', strtotime($DDated));
                        array_push($keys, '%%DeedDate%%');
                        array_push($values, $DeedDated);
                    }
                    //Deed Date Format Change
                    //Recorded date Format Change
                    $RDated = $torderdeeds_array[$i]->DeedRecorded;
                    if($RDated == '0000-00-00')
                    {
                        array_push($keys, '%%RecordedDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $RecordedDated =  date('m/d/Y', strtotime($RDated));
                        array_push($keys, '%%RecordedDate%%');
                        array_push($values, $RecordedDated);
                    }

                //Recorded date Format Change
                //ConsiderationAmount
                    $ConsiderationAmount = $torderdeeds_array[$i]->ConsiderationAmount;
                    if($ConsiderationAmount)
                    {
                        $ConsiderAmount = '$'.number_format($ConsiderationAmount,2);
                        array_push($keys, '%%ConsiderAmount%%');
                        array_push($values, $ConsiderAmount);
                    }
                    else{
                        array_push($keys, '%%ConsiderAmount%%');
                        array_push($values, '-');
                    }
                //ConsiderationAmount
                //Book/Page
                    $Deed_DBVTypeUID_1 = $torderdeeds_array[$i]->Deed_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_1);
                    $Deed_DBVTypeValue_1 = $torderdeeds_array[$i]->Deed_DBVTypeValue_1;
                    $Deed_DBVTypeUID_2 = $torderdeeds_array[$i]->Deed_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_2);
                    $Deed_DBVTypeValue_2 = $torderdeeds_array[$i]->Deed_DBVTypeValue_2;
                    if($DBVTypeName_1)
                    {
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, $DBVTypeName_1);
                        if($Deed_DBVTypeValue_1)
                        {
                            array_push($keys, '%%Deed_DBVTypeValue_1%%');
                            array_push($values, $Deed_DBVTypeValue_1);
                        }
                        else{
                            array_push($keys, '%%Deed_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }
                    }
                    else{
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Deed_DBVTypeValue_1%%');
                        array_push($values, '-');
                    }
                    if($DBVTypeName_2)
                    {
                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, $DBVTypeName_2);
                        if($Deed_DBVTypeValue_2)
                        {
                            array_push($keys, '%%Deed_DBVTypeValue_2%%');
                            array_push($values, $Deed_DBVTypeValue_2);
                        }
                        else{
                            array_push($keys, '%%Deed_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }
                    }
                    else{

                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Deed_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                //Deed Document
                    $DocumentNo = $torderdeeds_array[$i]->DocumentNo;
                    if($DocumentNo)
                    {
                        array_push($keys, '%%DocumentNo%%');
                        array_push($values, $DocumentNo);
                    }
                    else{
                        array_push($keys, '%%DocumentNo%%');
                        array_push($values, '-');
                    }
                //Deed Document
                //Certificate Number
                    $CertificateNo = $torderdeeds_array[$i]->CertificateNo;
                    if($CertificateNo)
                    {
                        array_push($keys, '%%CertificateNo%%');
                        array_push($values, $CertificateNo);
                    }
                    else{
                        array_push($keys, '%%CertificateNo%%');
                        array_push($values, '-');
                    }
                //Certificate Number
                //Instrument Number
                    $InstrumentNo = $torderdeeds_array[$i]->InstrumentNo;
                    if($InstrumentNo)
                    {
                        array_push($keys, '%%InstrumentNo%%');
                        array_push($values, $InstrumentNo);
                    }
                    else{
                        array_push($keys, '%%InstrumentNo%%');
                        array_push($values, '-');
                    }
                //Instrument Number
                //Deed Comments
                    $DeedComments = $torderdeeds_array[$i]->DeedComments;
                    if($DeedComments)
                    {
                        array_push($keys, '%%DeedComments%%');
                        array_push($values, $DeedComments);
                        array_push($keys, '%%deedalignment%%');
                        array_push($values, 'text-left');

                    }
                    else{
                        array_push($keys, '%%DeedComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%deedalignment%%');
                        array_push($values, 'text-center');
                    }
                //Deed Comments
                //Main Loop
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                //Main Loop
                }
                $torderdeeds_table .= str_replace($keys, $values, $torderdeeds[0][0]);
            }
            foreach ( $doc->find(".torderdeeds") as $node ) 
            {
                $node->innertext = $torderdeeds_table;
            }
        //Deed Ends
        //Mortgages Starts
            preg_match_all('/<div class=\"tordermortgages\">(.*?)<\/div>/s',$output,$tordermortgages);
            $tordermortgages_array = array();
            $tordermortgagesparties_array = array();
            $tordermortgages_array = $this->Order_reports_model->get_tordermortgageparties($OrderUID);
            $tordermortgages_array_count = count($tordermortgages_array);
            $tordermortgages_table2="";
            for ($i=0; $i < $tordermortgages_array_count; $i++) 
            { 

                $tordermortgages_table = '';
                $tordermortgages_array[$i]->mortgage_increment = $i+1;
                $keys = array();
                $values = array();
                        //Leins & Encumbrances
                if($i==0)
                {
                    $MortgageHeading = '<tr><th class="blur text-center" colspan="4"><p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Liens & Encumbrances</p></th></tr>';
                    array_push($keys, '%%MortgageHeading%%');
                    array_push($values, $MortgageHeading);

                }
                else
                {
                    array_push($keys, '%%MortgageHeading%%');
                    array_push($values, ' ');
                }
                foreach ($tordermortgages_array[$i] as $key => $value) 
                {

                    $DocumentTypeName = $tordermortgages_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $MortgageType = $tordermortgages_array[$i]->MortgageType;
                            if($MortgageType)
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, $MortgageType);
                            }
                            else
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%MortgageDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //Mortgage Date Format Change
                    $MortgageDated = $tordermortgages_array[$i]->MortgageDated;
                    if( $MortgageDated == '0000-00-00')
                    {
                        array_push($keys, '%%MortgageDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $MortgageDate =  date('m/d/Y', strtotime($MortgageDated));
                        array_push($keys, '%%MortgageDate%%');
                        array_push($values, $MortgageDate);
                    }
                //Mortgage Date Format Change
                //Mortgage Date Format Change
                    $MortgageRecorded = $tordermortgages_array[$i]->MortgageRecorded;
                    if($MortgageRecorded == '0000-00-00')
                    {
                        array_push($keys, '%%MortgageRecordedDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $MortgageRecordedDate =  date('m/d/Y', strtotime($MortgageRecorded));
                        array_push($keys, '%%MortgageRecordedDate%%');
                        array_push($values, $MortgageRecordedDate);
                    }
                //Mortgage Date Format Change
                //Mortgage Maturity Date Format
                    $MortgageMaturityDate = $tordermortgages_array[$i]->MortgageMaturityDate;
                    if( $MortgageMaturityDate == '0000-00-00')
                    {
                        array_push($keys, '%%MaturityDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $MaturityDate =  date('m/d/Y', strtotime($MortgageMaturityDate));
                        array_push($keys, '%%MaturityDate%%');
                        array_push($values, $MaturityDate);
                    }
                //Mortgage Maturity Date Format
                //LoanAmount
                    $MortgageAmount = $tordermortgages_array[$i]->MortgageAmount;
                    if($MortgageAmount)
                    {
                        $LoanAmt = '$'.number_format($MortgageAmount,2);
                        array_push($keys, '%%LoanAmt%%');
                        array_push($values, $LoanAmt);
                    }
                    else{
                        array_push($keys, '%%LoanAmt%%');
                        array_push($values, '-');
                    }
                //LoanAmount
                //Mortgagee
                    $Mortgagee = $tordermortgages_array[$i]->Mortgagee;
                    if($Mortgagee)
                    {
                        array_push($keys, '%%Mortgagee%%');
                        array_push($values, $Mortgagee);
                    }
                    else{
                        array_push($keys, '%%Mortgagee%%');
                        array_push($values, '-');
                    }
                //Mortgagee
                //Trustee
                    $Trustee1 = $tordermortgages_array[$i]->Trustee1;
                    $Trustee2 = $tordermortgages_array[$i]->Trustee2;
                    if($Trustee1 != '' && $Trustee2 != '')
                    {
                        $Trustee = $Trustee1.','.$Trustee2;
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);
                    }
                    elseif($Trustee1 != '')
                    {
                        $Trustee = $Trustee1;
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);
                    }
                    elseif($Trustee2 != ''){

                        $Trustee = $Trustee2;
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);

                    }
                    else{

                        $Trustee = '-';
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);
                    }
                //Trustee
                //Closed/Open Ended
                    if($tordermortgages_array[$i]->LienTypeName =='Closed Ended')
                    {
                        $MTG = array('MTG'=>'Closed Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    if($tordermortgages_array[$i]->LienTypeName =='Open Ended')
                    {
                        $MTG = array('MTG'=>'Open Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    else
                    {

                            array_push($keys, '%%MTG%%');
                            array_push($values,'-');
                        
                    }
                //Closed/Open Ended
                //Book/Page
                    $Mortgage_DBVTypeUID_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_1);
                    $Mortgage_DBVTypeValue_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_1;
                    $Mortgage_DBVTypeUID_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_2);
                    $Mortgage_DBVTypeValue_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_2;

                    if($DBVTypeName_1)
                    {
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, $DBVTypeName_1);
                        if($Mortgage_DBVTypeValue_1)
                        {
                            array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                            array_push($values, $Mortgage_DBVTypeValue_1);
                        }
                        else
                        {
                            array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }
                    }
                    else{
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                        array_push($values, '-');
                    }

                    if($DBVTypeName_2)
                    {
                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, $DBVTypeName_2);
                        if($Mortgage_DBVTypeValue_2)
                        {
                            array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                            array_push($values, $Mortgage_DBVTypeValue_2);
                        }
                        else{
                            array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }
                    }
                    else{

                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                    if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                    {
                        $AppendInstrument = $Mortgage_DBVTypeValue_1.','.$Mortgage_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_1 == 'Instrument'){
                        $AppendInstrument = $Mortgage_DBVTypeValue_1;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_2 == 'Instrument'){
                        $AppendInstrument = $Mortgage_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                    else{
                        $AppendInstrument = '-';
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                //Book/Page
                //Additional Info
                $AdditionalInfo = $tordermortgages_array[$i]->AdditionalInfo;
                if($AdditionalInfo)
                {
                    array_push($keys, '%%AdditionalInfo%%');
                    array_push($values, $AdditionalInfo);
                }
                else{
                    array_push($keys, '%%AdditionalInfo%%');
                    array_push($values, '-');
                }
                //Additional Info
                //Closed/Open Ended
                $OpenEnded = $tordermortgages_array[$i]->IsOpenEnded;
                if($OpenEnded == '1')
                {

                    array_push($keys, '%%OpenEnded%%');
                    array_push($values, 'Yes');
                    
                }
                else
                {

                    array_push($keys, '%%OpenEnded%%');
                    array_push($values,'-');
                    
                }
                //Closed/Open Ended
                //Mortgage Comments
                    $MortgageComments = $tordermortgages_array[$i]->MortgageComments;
                    if($MortgageComments)
                    {
                        array_push($keys, '%%MortgageComments%%');
                        array_push($values, $MortgageComments);
                        array_push($keys, '%%mortgagealignment%%');
                        array_push($values, 'text-left');
                    }
                    else{
                        array_push($keys, '%%MortgageComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%mortgagealignment%%');
                        array_push($values, 'text-center');
                    }
                //Mortgage Comments
                //Main Loop
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                //Main Loop
                }
                $tordermortgages_table .= str_replace($keys, $values, $tordermortgages[0][0]);

                $submortgage_html='<table style="width: 100%;margin-top: 5pt;page-break-inside: avoid;table-layout: fixed;" cellspacing="0"><tr>
                <td class="td-bd text-center" colspan="3">
                <p style="font-size: 7pt;text-transform: uppercase;" class="bold text-center">%%submortgageDocumentTypeName%%</p>
                </td>
                <td class="td-bd text-center" colspan="1">
                <p style="font-size: 7pt;" class="bold text-center">Document Dated</p>
                </td>
                <td class="td-bd text-center" colspan="2">
                <p class="text-center" style="padding:0pt 20pt;font-size: 7pt;">%%Dated%%</p>
                </td>
                </tr>
                <tr>
                <td width="15.30%" class="td-bd text-center">
                <p style="font-size: 7pt;" class="bold">Recorded Date</p>
                </td>
                <td width="18.30%" class="td-bd text-center">
                <p style="font-size: 7pt;" class="text-center">%%Recorded%%</p>
                </td>
                <td width="15.30%" class="td-bd text-center">
                <p class="bold text-center" style="font-size: 7pt;">%%SubdocumentDBVTypeName_1%%</p>
                </td>
                <td width="18.30%" class="td-bd text-center">
                <p style="font-size: 7pt;" class="text-center">%%subdocument_DBVTypeValue_1%%</p>
                </td>
                <td width="15.30%" class="td-bd text-center">
                <p style="font-size: 7pt;" class="bold">%%SubdocumentDBVTypeName_2%%</p>
                </td>
                <td width="18.30%" class="td-bd text-center">
                <p style="font-size: 7pt;" class="text-center">%%subdocument_DBVTypeValue_2%%</p>
                </td>
                </tr>
                <tr>
                <td class="td-bd text-center" colspan="1">
                <p style="font-size: 7pt;" class="bold">Comments</p>
                </td>
                <td class="td-bd %%submortgagealignment%%" colspan="5">
                <p class="" style="text-align: justify;font-size: 7pt;">%%SubMortgageComments%%</p>
                </td>
                </tr></table>';

                $submortgage="";
                //Sub Mortgage
                $submortgage_table = '';
                $submortgage_array = array();
                $Mortgage = $tordermortgages_array[$i]->MortgageSNo;
                $submortgage_array = $this->Order_reports_model->getsubmortgage($OrderUID, $Mortgage);
                $submortgage_array_count = count($submortgage_array);
                for ($j=0; $j < $submortgage_array_count; $j++) 
                { 
                    $keys = array();
                    $values = array();
                    foreach ($submortgage_array[$j] as $key => $value) 
                    {

                    $DocumentTypeName = $submortgage_array[$j]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $MortgageAssignmentType = $submortgage_array[$j]->MortgageAssignmentType;
                            if($MortgageAssignmentType)
                            {
                                array_push($keys, '%%submortgageDocumentTypeName%%');
                                array_push($values, $MortgageAssignmentType);
                            }
                            else
                            {
                                array_push($keys, '%%submortgageDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%submortgageDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%submortgageDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%submortgageDocumentTypeName%%');
                        array_push($values, '-');
                    }

                        $Dated = $submortgage_array[$j]->Dated;
                        if($Dated == '0000-00-00')
                        {
                            array_push($keys, '%%Dated%%');
                            array_push($values, '-');

                        }
                        else{

                            $submortgagedated =  date('m/d/Y', strtotime($Dated));
                            array_push($keys, '%%Dated%%');
                            array_push($values, $submortgagedated);

                        }
                        $Recorded = $submortgage_array[$j]->Recorded;
                        if($Recorded == '0000-00-00')
                        {
                            array_push($keys, '%%Recorded%%');
                            array_push($values, '-');

                        }
                        else{

                            $submortgagerecorded =  date('m/d/Y', strtotime($Recorded));
                            array_push($keys, '%%Recorded%%');
                            array_push($values, $submortgagerecorded);

                        }
                        $SubMortgageComments = $submortgage_array[$j]->Comments;
                        if($SubMortgageComments)
                        {
                            array_push($keys, '%%SubMortgageComments%%');
                            array_push($values, $SubMortgageComments);
                            array_push($keys, '%%submortgagealignment%%');
                            array_push($values, 'text-left');
                        }
                        else{
                            array_push($keys, '%%SubMortgageComments%%');
                            array_push($values, '-');
                            array_push($keys, '%%submortgagealignment%%');
                            array_push($values, 'text-center');
                        }

                        $Subdocument_DBVTypeUID_1 = $submortgage_array[$j]->Subdocument_DBVTypeUID_1;
                        $SubdocumentDBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Subdocument_DBVTypeUID_1);
                        $Subdocument_DBVTypeValue_1 = $submortgage_array[$j]->Subdocument_DBVTypeValue_1;
                        $Subdocument_DBVTypeUID_2 = $submortgage_array[$j]->Subdocument_DBVTypeUID_2;
                        $SubdocumentDBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Subdocument_DBVTypeUID_2);
                        $Subdocument_DBVTypeValue_2 = $submortgage_array[$j]->Subdocument_DBVTypeValue_2;

                        if($SubdocumentDBVTypeName_1)
                        {
                            array_push($keys, '%%SubdocumentDBVTypeName_1%%');
                            array_push($values, $SubdocumentDBVTypeName_1);
                            if($Subdocument_DBVTypeValue_1)
                            {
                                array_push($keys, '%%subdocument_DBVTypeValue_1%%');
                                array_push($values, $Subdocument_DBVTypeValue_1);
                            }
                            else{

                                array_push($keys, '%%subdocument_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else{
                            array_push($keys, '%%SubdocumentDBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%subdocument_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }

                        if($SubdocumentDBVTypeName_2)
                        {
                            array_push($keys, '%%SubdocumentDBVTypeName_2%%');
                            array_push($values, $SubdocumentDBVTypeName_2);
                            if($Subdocument_DBVTypeValue_2)
                            {
                                array_push($keys, '%%subdocument_DBVTypeValue_2%%');
                                array_push($values, $Subdocument_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%subdocument_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else{

                            array_push($keys, '%%SubdocumentDBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%subdocument_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }

                    }

                    $submortgage .= str_replace($keys, $values, $submortgage_html);

                }
                $tordermortgages_table = str_replace('%%submortgage%%', $submortgage, $tordermortgages_table);

                $tordermortgages_table2 .= $tordermortgages_table;

            }
            foreach ( $doc->find(".tordermortgages") as $node ) 
            {
                $node->innertext = $tordermortgages_table2;
            }
        //Mortgages Ends

        //Tax Starts
            preg_match_all('/<div class=\"tordertaxcerts\">(.*?)<\/div>/s',$output,$tordertaxcerts);

            $tordertaxcerts_array = array();
            $tordertax_array = array();
            $tordertaxcerts_array = $this->Order_reports_model->get_tordertaxcerts($OrderUID);
            $tordertaxcerts_array_count = count($tordertaxcerts_array);
            $tordertaxcerts_table2="";
            $tordertaxcerts_table3="";
            for ($i=0; $i < $tordertaxcerts_array_count; $i++) 
            { 
                $tordertaxcerts_table = '';
                $tordertaxcerts_array[$i]->tax_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($tordertaxcerts_array[$i] as $key => $value)
                {
                //subdocumenttype
                $DocumentTypeName = $tordertaxcerts_array[$i]->DocumentTypeName;
                if($DocumentTypeName)
                {
                    if($DocumentTypeName == 'Others')
                    {
                        $TaxType = $tordertaxcerts_array[$i]->TaxType;
                        if($TaxType)
                        {
                            array_push($keys, '%%TaxDocumentTypeName%%');
                            array_push($values, $TaxType);
                        }
                        else
                        {
                            array_push($keys, '%%TaxDocumentTypeName%%');
                            array_push($values, '-');
                        }
                    }
                    else if($DocumentTypeName !== 'Others')
                    {
                        array_push($keys, '%%TaxDocumentTypeName%%');
                        array_push($values, $DocumentTypeName);
                    }
                    else
                    {
                        array_push($keys, '%%TaxDocumentTypeName%%');
                        array_push($values, '-');
                    }

                }
                else{
                    array_push($keys, '%%TaxDocumentTypeName%%');
                    array_push($values, '-');
                }
                //subdocumenttype
                //Amount Paid
                    $AmountPaid = $tordertaxcerts_array[$i]->AmountPaid;
                    $AmtPaid = '$'.number_format($AmountPaid,2);
                    if($AmtPaid)
                    {
                        array_push($keys, '%%AmountPaid%%');
                        array_push($values, $AmtPaid);
                    }
                    else{
                        array_push($keys, '%%AmountPaid%%');
                        array_push($values, '-');
                    }
                //Amount Paid
                //Amount Due
                    $AmountDue = $tordertaxcerts_array[$i]->AmountDue;
                    $AmtDue = '$'.number_format($AmountDue,2);
                    if($AmtDue)
                    {
                        array_push($keys, '%%AmountDue%%');
                        array_push($values, $AmtDue);
                    }
                    else{
                        array_push($keys, '%%AmountDue%%');
                        array_push($values, '-');
                    }
                //Amount Due
                //Gross Amount
                    $GrossAmount = $tordertaxcerts_array[$i]->GrossAmount;
                    $GrossAmt = '$'.number_format($GrossAmount,2);
                    if($GrossAmt)
                    {
                        array_push($keys, '%%GrossAmount%%');
                        array_push($values, $GrossAmt);
                    }
                    else{
                        array_push($keys, '%%GrossAmount%%');
                        array_push($values, '-');
                    }
                //Gross Amount



                  //ApprovedUnapprovedTaxAuthorityDetails
                    $UnapprovedTaxAuthorityDetails = $this->Order_reports_model->GetUnapprovedTaxAuthorityDetails($OrderUID,$tordertaxcerts_array[$i]->TaxAuthorityUID);
                    if($UnapprovedTaxAuthorityDetails)
                    {
                        //PaymentAddrLine1
                            $PaymentAddrLine1 = $UnapprovedTaxAuthorityDetails->PaymentAddrLine1;
                            if($PaymentAddrLine1)
                            {
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, $PaymentAddrLine1.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, ' ');
                            }
                        //PaymentAddrLine1
                        //PaymentAddrLine2
                            $PaymentAddrLine2 = $UnapprovedTaxAuthorityDetails->PaymentAddrLine2;
                            if($PaymentAddrLine2)
                            {
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, $PaymentAddrLine2.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, ' ');
                            }
                        //PaymentAddrLine2
                        //PaymentCity
                            $PaymentCity = $UnapprovedTaxAuthorityDetails->PaymentCity;
                            if($PaymentCity)
                            {
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, $PaymentCity.',');
                            }
                            else{
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, ' ');
                            }
                        //PaymentCity
                        //PaymentState
                            $PaymentState = $UnapprovedTaxAuthorityDetails->PaymentState;
                            if($PaymentState)
                            {
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, $PaymentState.'-');
                            }
                            else{
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, ' ');
                            }
                        //PaymentState
                        //PaymentZipCode
                            $PaymentZipCode = $UnapprovedTaxAuthorityDetails->PaymentZipCode;
                            if($PaymentZipCode)
                            {
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, $PaymentZipCode);
                            }
                            else{
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, ' ');
                            }
                        //PaymentZipCode


                        //Tax Collector Name
                            $TaxCollector = $UnapprovedTaxAuthorityDetails->TaxCollector;
                            if($TaxCollector)
                            {
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, $TaxCollector);
                            }
                            else{
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, '-');
                            }
                        //Tax Authority Name
                        //Tax Payable
                            $TaxPayable = $UnapprovedTaxAuthorityDetails->TaxPayable;
                            if($TaxPayable)
                            {
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, $TaxPayable);
                            }
                            else{
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, '-');
                            }
                        //Tax Payable
                        //Collector Phone
                        $CollectorPhone = $UnapprovedTaxAuthorityDetails->CollectorPhone;
                        if($CollectorPhone)
                        {
                            $numbers_only = preg_replace("/[^\d]/", "", $CollectorPhone);
                            $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "($1)&nbsp;$2-$3", $numbers_only);
                            array_push($keys, '%%CollectorPhoneNumber%%');
                            array_push($values, $number);
                        }
                        else{
                            array_push($keys, '%%CollectorPhoneNumber%%');
                            array_push($values, '-');
                        }
                        //Collector Phone
                        //Website Address
                        $WebsiteAddress = $UnapprovedTaxAuthorityDetails->WebsiteAddress;
                        if($WebsiteAddress)
                        {
                            array_push($keys, '%%WebsiteAddr%%');
                            array_push($values, '<u>'.$WebsiteAddress.'</u>');
                        }
                        else{
                            array_push($keys, '%%WebsiteAddr%%');
                            array_push($values, '-');
                        }
                        //Website Address
                        
                    }else{
                        $ApprovedTaxAuthorityDetails = $this->Order_reports_model->GetApprovedTaxAuthorityDetails($tordertaxcerts_array[$i]->TaxAuthorityUID);
                        if($ApprovedTaxAuthorityDetails)
                        {
                            //PaymentAddrLine1
                            $PaymentAddrLine1 =$ApprovedTaxAuthorityDetails->PaymentAddrLine1;
                            if($PaymentAddrLine1)
                            {
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, $PaymentAddrLine1.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, ' ');
                            }
                            //PaymentAddrLine1
                            //PaymentAddrLine2
                            $PaymentAddrLine2 =$ApprovedTaxAuthorityDetails->PaymentAddrLine2;
                            if($PaymentAddrLine2)
                            {
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, $PaymentAddrLine2.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, ' ');
                            }
                            //PaymentAddrLine2
                            //PaymentCity
                            $PaymentCity =$ApprovedTaxAuthorityDetails->PaymentCity;
                            if($PaymentCity)
                            {
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, $PaymentCity.',');
                            }
                            else{
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, ' ');
                            }
                            //PaymentCity
                            //PaymentState
                            $PaymentState =$ApprovedTaxAuthorityDetails->PaymentState;
                            if($PaymentState)
                            {
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, $PaymentState.'-');
                            }
                            else{
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, ' ');
                            }
                            //PaymentState
                            //PaymentZipCode
                            $PaymentZipCode =$ApprovedTaxAuthorityDetails->PaymentZipCode;
                            if($PaymentZipCode)
                            {
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, $PaymentZipCode);
                            }
                            else{
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, ' ');
                            }
                            //PaymentZipCode
                            //Tax Collector Name
                            $TaxCollector =$ApprovedTaxAuthorityDetails->TaxCollector;
                            if($TaxCollector)
                            {
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, $TaxCollector);
                            }
                            else{
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, '-');
                            }
                            //Tax Authority Name
                            //Tax Payable
                            $TaxPayable =$ApprovedTaxAuthorityDetails->TaxPayable;
                            if($TaxPayable)
                            {
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, $TaxPayable);
                            }
                            else{
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, '-');
                            }
                            //Tax Payable
                            //Collector Phone
                            $CollectorPhone =$ApprovedTaxAuthorityDetails->CollectorPhone;
                            if($CollectorPhone)
                            {
                                $numbers_only = preg_replace("/[^\d]/", "", $CollectorPhone);
                                $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "($1)&nbsp;$2-$3", $numbers_only);
                                array_push($keys, '%%CollectorPhoneNumber%%');
                                array_push($values, $number);
                            }
                            else{
                                array_push($keys, '%%CollectorPhoneNumber%%');
                                array_push($values, '-');
                            }
                            //Collector Phone
                            //Website Address
                            $WebsiteAddress = $ApprovedTaxAuthorityDetails->WebsiteAddress;
                            if($WebsiteAddress)
                            {
                                array_push($keys, '%%WebsiteAddr%%');
                                array_push($values, '<u>'.$WebsiteAddress.'</u>');
                            }
                            else{
                                array_push($keys, '%%WebsiteAddr%%');
                                array_push($values, '-');
                            }
                            //Website Address
                        }
                        else{
                            $data = array('PaymentAddrLine1','PaymentAddrLine2','PaymentCity','PaymentState','PaymentZipCode','TaxCollector');
                            foreach ($data as $value) {
                                array_push($keys, '%%'.$value.'%%');
                                array_push($values, ' ');
    
                            }
                            $datas = array('TaxPayable','CollectorPhoneNumber','WebsiteAddr');
                            foreach ($datas as $value) {
                                array_push($keys, '%%'.$value.'%%');
                                array_push($values, '-');
                            }
                        }
        
                    }
                    //ApprovedUnapprovedTaxAuthorityDetails

                //Amount Deliquent
                    $AmountDelinquent = $tordertaxcerts_array[$i]->AmountDelinquent;
                    $AmtDelinquent = '$'.number_format($AmountDelinquent,2);
                    if($AmtDelinquent)
                    {
                        array_push($keys, '%%AmountDelinquent%%');
                        array_push($values, $AmtDelinquent);
                    }
                    else{
                        array_push($keys, '%%AmountDelinquent%%');
                        array_push($values, '-');
                    }
                //Amount Deliquent
                //Account Number
                    $ParcelNumber = $tordertaxcerts_array[$i]->ParcelNumber;
                    if($ParcelNumber)
                    {
                        array_push($keys, '%%ParcelNumber%%');
                        array_push($values, $ParcelNumber);
                    }
                    else{
                        array_push($keys, '%%ParcelNumber%%');
                        array_push($values, '-');
                    }
                    //Account Number
                    //Estimated Tax
                    $EstimatedTax = $tordertaxcerts_array[$i]->EstimatedTax;
                    $EstimatedTax = '$'.number_format($EstimatedTax,2);
                    if($EstimatedTax)
                    {
                        array_push($keys, '%%EstimatedTax%%');
                        array_push($values, $EstimatedTax);
                    }
                    else{
                        array_push($keys, '%%EstimatedTax%%');
                        array_push($values, '-');
                    }
                    //Estimated Tax
                    //Tax Basis Name
                    $TaxBasisName = $tordertaxcerts_array[$i]->TaxBasisName;
                    if($TaxBasisName)
                    {
                        array_push($keys, '%%TaxBasisName%%');
                        array_push($values, $TaxBasisName);
                    }
                    else{
                        array_push($keys, '%%TaxBasisName%%');
                        array_push($values, '-');
                    }
                    //Tax Basis Name
                    //Property Class Name
                    $PropertyClassName = $tordertaxcerts_array[$i]->PropertyClassName;
                    if($PropertyClassName)
                    {
                        array_push($keys, '%%PropertyClassName%%');
                        array_push($values, $PropertyClassName);
                    }
                    else{
                        array_push($keys, '%%PropertyClassName%%');
                        array_push($values, '-');
                    }
                   //Property Class Name
                    //Tax Comments
                    $TaxComments = $tordertaxcerts_array[$i]->TaxComments;
                    if($TaxComments)
                    {
                        array_push($keys, '%%TaxComments%%');
                        array_push($values, $TaxComments);
                        array_push($keys, '%%taxalignment%%');
                        array_push($values, 'text-left');
                    }
                    else{
                        array_push($keys, '%%TaxComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%taxalignment%%');
                        array_push($values, 'text-center');
                    }
                    //Tax Comments
                    $TaxYears = $tordertaxcerts_array[$i]->TaxYear;
                    if($TaxYears)
                    {
                        array_push($keys, '%%TaxYears%%');
                        array_push($values, $TaxYears);
                    }
                    else{
                        array_push($keys, '%%TaxYears%%');
                        array_push($values, '-');
                    }
                    //Tax Date Format Change
                    $NextTaxDueDate = $tordertaxcerts_array[$i]->NextTaxDueDate;
                    if($NextTaxDueDate == '0000-00-00')
                    {
                        array_push($keys, '%%NextTaxDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $NextTaxDate =  date('m/d/Y', strtotime($NextTaxDueDate));
                        array_push($keys, '%%NextTaxDate%%');
                        array_push($values, $NextTaxDate);
                    }
                    //Tax Date Format Change
                    //Tax Date Format Change
                    $GoodThroughDate = $tordertaxcerts_array[$i]->GoodThroughDate;
                    if($GoodThroughDate == '0000-00-00' || $GoodThroughDate == ' ' )
                    {
                        array_push($keys, '%%GoodThroughDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $GoodThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                        array_push($keys, '%%GoodThroughDate%%');
                        array_push($values, $GoodThroughDate);
                    }
                    //Tax Date Format Change
                    //Tax Date Paid
                    $DatePaid = $tordertaxcerts_array[$i]->DatePaid;
                    if($DatePaid == '0000-00-00')
                    {
                        array_push($keys, '%%PaidDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $PaidDate =  date('m/d/Y', strtotime($DatePaid));
                        array_push($keys, '%%PaidDate%%');
                        array_push($values, $PaidDate);
                    }
                    //Tax Date Paid
                    //Main Loop
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);    
                //Main Loop
                }
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $TaxExemptionNames = $this->Order_reports_model->getExemptionname($OrderUID,$TaxCert);
                if($TaxExemptionNames)
                {
                    array_push($keys, '%%TaxExemptionNames%%');
                    array_push($values, $TaxExemptionNames);
                }
                else{
                    array_push($keys, '%%TaxExemptionNames%%');
                    array_push($values, '-');
                }

                $tordertaxcerts_table .= str_replace($keys, $values, $tordertaxcerts[0][0]);
                $taxinstallment_html='<tr style="border: 0.01em solid grey;">
                <td  width="10.4%" class="td-bd text-center" style="">
                <p style="font-size: 8pt;" class="">%%Tax_Year%%</p>
                </td>
                <td width="17.8%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="text-center">%%Tax_InstallmentName%%</p>
                </td>
                <td  width="13.5" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="">%%Gross_Amount%%</p>
                </td>
                <td width="14.7%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="text-center">%%Tax_StatusName%%</p>
                </td>
                <td width="21.8%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="">%%Amount_Paid%%</p>
                </td>
                <td width="21.8%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class=" text-center">%%Paid_Date%%</p>
                </td>
                </tr> ';
                $taxinstallment="";
                $taxinstallment_table = '';
                $taxinstallment_array = array();
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $taxinstallment_array = $this->Order_reports_model->gettaxinstallment($OrderUID, $TaxCert);
                $taxinstallment_array_count = count($taxinstallment_array);
                for ($k=0; $k < $taxinstallment_array_count; $k++) 
                { 

                    $keys = array();
                    $values = array();
                    foreach ($taxinstallment_array[$k] as $key => $value) 
                    {
                        $TaxYear = $taxinstallment_array[$k]->TaxYear;
                        if($TaxYear)
                        {
                            array_push($keys, '%%Tax_Year%%');
                            array_push($values, $TaxYear);

                        }
                        else{
                            array_push($keys, '%%Tax_Year%%');
                            array_push($values, '-');

                        }
                        $TaxInstallmentName = $taxinstallment_array[$k]->TaxInstallmentName;
                        if($TaxInstallmentName)
                        {
                            array_push($keys, '%%Tax_InstallmentName%%');
                            array_push($values, $TaxInstallmentName);

                        }
                        else{
                            array_push($keys, '%%Tax_InstallmentName%%');
                            array_push($values, '-');

                        }
                        $GrossAmount = $taxinstallment_array[$k]->GrossAmount;
                        if($GrossAmount)
                        {
                            $GrossAmount = '$'.number_format($GrossAmount,2);
                            array_push($keys, '%%Gross_Amount%%');
                            array_push($values, $GrossAmount);

                        }
                        else{
                            array_push($keys, '%%Gross_Amount%%');
                            array_push($values, '-');

                        }
                        $TaxStatusName = $taxinstallment_array[$k]->TaxStatusName;
                        if($TaxStatusName)
                        {
                            array_push($keys, '%%Tax_StatusName%%');
                            array_push($values, $TaxStatusName);

                        }
                        else{
                            array_push($keys, '%%Tax_StatusName%%');
                            array_push($values, '-');

                        }
                        $AmountPaid = $taxinstallment_array[$k]->AmountPaid;
                        if($AmountPaid)
                        {
                            $AmountPaid = '$'.number_format($AmountPaid,2);
                            array_push($keys, '%%Amount_Paid%%');
                            array_push($values, $AmountPaid);

                        }
                        else{
                            array_push($keys, '%%Amount_Paid%%');
                            array_push($values, '-');

                        }
                        $DatePaid = $taxinstallment_array[$k]->DatePaid;
                        if($DatePaid  == '0000-00-00')
                        {
                            array_push($keys, '%%Paid_Date%%');
                            array_push($values, '-');

                        }
                        else{
                            $DatePaid =  date('m/d/Y', strtotime($DatePaid));
                            array_push($keys, '%%Paid_Date%%');
                            array_push($values, $DatePaid);

                        }

                        $GoodThroughDate = $taxinstallment_array[$k]->GoodThroughDate;
                        if($GoodThroughDate  == '0000-00-00')
                        {
                            array_push($keys, '%%Good_Through_Date%%');
                            array_push($values, '-');

                        }
                        else{
                            $GoodThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                            array_push($keys, '%%Good_Through_Date%%');
                            array_push($values, $GoodThroughDate);

                        }
                    }

                    $taxinstallment .= str_replace($keys, $values, $taxinstallment_html);

                }
                $tordertaxcerts_table = str_replace('%%taxinstallment%%', $taxinstallment, $tordertaxcerts_table);


                $taxexemption="";
                $tordertaxemption_table = '';
                $tordertaxemption_array = array();
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $tordertaxemption_array = $this->Order_reports_model->gettaxExemption($OrderUID, $TaxCert);
                $tordertaxemption_array_count = count($tordertaxemption_array);
                $taxexemption_html='<tr class="tordertaxemption" style="border: 0.01em solid grey;">
                <td  rowspan="'. $tordertaxemption_array_count .'" width="20%" class="td-bd text-center" colspan="2" style="" >
                <p style="font-size: 8pt;" class="bold">Tax Exemption</p>
                </td>
                <td  width="30%" class="td-bd text-center" colspan="5" style="">
                <p style="font-size: 8pt;" class="text-center">%%TaxExemptionName%%</p>
                </td>
                <td  width="30%" class="td-bd text-center" colspan="5" style="">
                <p style="font-size: 8pt;" class="text-center">%%TaxAmount%%</p>
                </td>
                </tr>';
                for ($j=0; $j < $tordertaxemption_array_count; $j++) 
                {
                    if($j>0)
                    {
                      $taxexemption_html='<tr class="tordertaxemption" style="border: 0.01em solid grey;">
                        <td  width="30%" class="td-bd text-center" colspan="5" style="">
                        <p style="font-size: 8pt;" class="text-center">%%TaxExemptionName%%</p>
                        </td>
                        <td  width="30%" class="td-bd text-center" colspan="5" style="">
                        <p style="font-size: 8pt;" class="text-center">%%TaxAmount%%</p>
                        </td>
                        </tr>';

                    }
                    $keys = array();
                    $values = array();
                    foreach ($tordertaxemption_array[$j] as $key => $value) 
                    {
                        $TaxExemptionName = $tordertaxemption_array[$j]->TaxExemptionName;
                        if($TaxExemptionName)
                        {
                            array_push($keys, '%%TaxExemptionName%%');
                            array_push($values, $TaxExemptionName);

                        }
                        else{
                            array_push($keys, '%%TaxExemptionName%%');
                            array_push($values, '-');

                        }
                        $TaxAmount = $tordertaxemption_array[$j]->TaxAmount;
                        if($TaxAmount)
                        {
                            $TaxAmount = '$'.$TaxAmount;
                            array_push($keys, '%%TaxAmount%%');
                            array_push($values, $TaxAmount);

                        }
                        else{
                            array_push($keys, '%%TaxAmount%%');
                            array_push($values, '$0.00');

                        }

                    }

                    $taxexemption .= str_replace($keys, $values, $taxexemption_html);

                }
                $tordertaxcerts_table = str_replace('%%TaxExmp%%', $taxexemption, $tordertaxcerts_table);


 //Tax Exemption------------------------------------------------------------------------------------------
                $tordertaxcerts_table2 .= $tordertaxcerts_table;
            }

            foreach ( $doc->find(".tordertaxcerts") as $node ) 
            {
                $node->innertext = $tordertaxcerts_table2;
            }
        //Tax Ends




        //Judgement Starts
            preg_match_all('/<div class=\"torderjudgments\">(.*?)<\/div>/s',$output,$torderjudgments);
            $torderjudgments_table = '';
            $torderjudgments_array = array();
            $torderjudgments_array = $this->Order_reports_model->get_torderjudgements($OrderUID);
            $torderjudgments_array_count = count($torderjudgments_array);
            for ($i=0; $i < $torderjudgments_array_count; $i++)
            { 
                $torderjudgments_array[$i]->judgement_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($torderjudgments_array[$i] as $key => $value)
                {
                    $DocumentTypeName = $torderjudgments_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $JudgementType = $torderjudgments_array[$i]->JudgementType;
                            if($JudgementType)
                            {
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, $JudgementType);
                            }
                            else{
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%JudgementDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //Plaintiff
                    $Plaintiff = $torderjudgments_array[$i]->Plaintiff;
                    if($Plaintiff)
                    {
                        array_push($keys, '%%Plaintiff%%');
                        array_push($values, $Plaintiff);
                    }
                    else{
                        array_push($keys, '%%Plaintiff%%');
                        array_push($values, '-');
                    }
                //Plaintiff
                //Defendant
                    $Defendent = $torderjudgments_array[$i]->Defendent;
                    if($Defendent)
                    {
                        array_push($keys, '%%Defendent%%');
                        array_push($values, $Defendent);
                    }
                    else{
                        array_push($keys, '%%Defendent%%');
                        array_push($values, '-');
                    }
                //Defendant
                //Judgement Date Format Change
                    $JDated = $torderjudgments_array[$i]->JudgementDated;
                    if($JDated == '0000-00-00')
                    {
                        array_push($keys, '%%JudgeDated%%');
                        array_push($values, '-');  
                    }
                    else{
                        $Dated =  date('m/d/Y', strtotime($JDated));
                        array_push($keys, '%%JudgeDated%%');
                        array_push($values, $Dated);  
                    }
                //Judgement Date Format Change
                //Judgement Recorded date Format Change
                    $RDated = $torderjudgments_array[$i]->JudgementRecorded;
                    if($RDated == '0000-00-00')
                    {
                        array_push($keys, '%%JudgeRecorded%%');
                        array_push($values, '-');
                    }
                    else{
                        $RecordedDated =  date('m/d/Y', strtotime($RDated));
                        array_push($keys, '%%JudgeRecorded%%');
                        array_push($values, $RecordedDated);
                    }
                // JudgementRecorded date Format Change
                //Judgement Amount
                    $JudgementAmount = $torderjudgments_array[$i]->JudgementAmount;
                    if($JudgementAmount)
                    {
                        $JudAmt = '$'.number_format($JudgementAmount,2);
                        array_push($keys, '%%JudgementAmount%%');
                        array_push($values, $JudAmt);
                    }
                    else{
                        array_push($keys, '%%JudgementAmount%%');
                        array_push($values, '-');
                    }
                //Judgement Amount
                //Judgements Comments
                    $JudgementComments = $torderjudgments_array[$i]->JudgementComments;
                    if($JudgementComments)
                    {
                        array_push($keys, '%%JudgementComments%%');
                        array_push($values, $JudgementComments);
                        array_push($keys, '%%judalignment%%');
                        array_push($values, 'text-left');

                    }
                    else{
                        array_push($keys, '%%JudgementComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%judalignment%%');
                        array_push($values, 'text-center');
                    }
                //Judgements Comments
                    $Judgement_DBVTypeUID_1 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_1);
                    $Judgement_DBVTypeValue_1 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_1;
                    $Judgement_DBVTypeUID_2 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_2);
                    $Judgement_DBVTypeValue_2 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_2;
                    if($DBVTypeName_1)
                    {
                        if($DBVTypeName_1 !== 'Case number')
                        {
                            array_push($keys, '%%JudgementDBVTypeName_1%%');
                            array_push($values, $DBVTypeName_1);
                            if($Judgement_DBVTypeValue_1)
                            {
                                array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                array_push($values, $Judgement_DBVTypeValue_1);
                            }
                            else{
                                array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                            array_push($values, '-'); 
                        }
                    }
                    else
                    {
                        array_push($keys, '%%JudgementDBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                        array_push($values, '-'); 
                    }
                    if($DBVTypeName_2)
                    {
                        if($DBVTypeName_2 !== 'Case number')
                        {
                            
                            array_push($keys, '%%JudgementDBVTypeName_2%%');
                            array_push($values, $DBVTypeName_2);
                            if($Judgement_DBVTypeValue_2)
                            {
                                array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                array_push($values, $Judgement_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                            array_push($values, '-'); 
                        }
                    }
                    else{

                        array_push($keys, '%%JudgementDBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                //CaseNumber
                    $JudgementCaseNo = $torderjudgments_array[$i]->JudgementCaseNo;
                    if($JudgementCaseNo)
                    {
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $JudgementCaseNo);
                    }
                    else{

                        if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                        {
                            $AppendCasenumber = $Judgement_DBVTypeValue_1.','.$Judgement_DBVTypeValue_2;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);

                        }
                        if($DBVTypeName_1 == 'Case number'){
                            $AppendCasenumber = $Judgement_DBVTypeValue_1;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);

                        }
                        if($DBVTypeName_2 == 'Case number'){
                            $AppendCasenumber = $Judgement_DBVTypeValue_2;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);
                        }
                        else{
                            $AppendCasenumber = '-';
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);
                        }
                    }
                //CaseNumber
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
                $torderjudgments_table .= str_replace($keys, $values, $torderjudgments[0][0]);
            }
            foreach ( $doc->find(".torderjudgments") as $node ) 
            {
                $node->innertext = $torderjudgments_table;
            }
        //Judgments Ends
        //Liens Starts
            preg_match_all('/<div class=\"torderliens\">(.*?)<\/div>/s',$output,$torderliens);
            $torderliens_table = '';
            $torderliens_array = array();
            $torderliens_array = $this->Order_reports_model->get_torderliens($OrderUID);
            $torderliens_array_count = count($torderliens_array);
            for ($i=0; $i < $torderliens_array_count; $i++) 
            { 

                $torderliens_array[$i]->lien_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($torderliens_array[$i] as $key => $value) 
                {
                //DocumentTypeName
                    $DocumentTypeName = $torderliens_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $LeinType = $torderliens_array[$i]->LeinType;
                            if($LeinType)
                            {
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, $LeinType);
                            }
                            else{
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%LeinDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //DocumentTypeName
                //Lein Date Format Change
                    $LDated = $torderliens_array[$i]->LeinDated;
                    if($LDated == '0000-00-00')
                    {
                        array_push($keys, '%%LeinDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $Dated =  date('m/d/Y', strtotime($LDated));
                        array_push($keys, '%%LeinDate%%');
                        array_push($values, $Dated);
                    }
                //Lein Date Format Change
                //Lein Recorded date Format Change
                    $RDated = $torderliens_array[$i]->LeinRecorded;
                    if($RDated == '0000-00-00')
                    {
                        array_push($keys, '%%leinRecord%%');
                        array_push($values, '-');
                    }
                    else{
                        $RecordedDated =  date('m/d/Y', strtotime($RDated));
                        array_push($keys, '%%leinRecord%%');
                        array_push($values, $RecordedDated);
                    }
                // Lein date Format Change
                    if($torderliens_array[$i]->LienTypeName =='Closed Ended')
                    {
                        $MTG = array('MTG'=>'Closed Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    if($torderliens_array[$i]->LienTypeName =='Open Ended')
                    {
                        $MTG = array('MTG'=>'Open Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    else
                    {

                            array_push($keys, '%%MTG%%');
                            array_push($values, '-');
                        
                    }

                    $Lien_DBVTypeUID_1 = $torderliens_array[$i]->Lien_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_1);
                    $Lien_DBVTypeValue_1 = $torderliens_array[$i]->Lien_DBVTypeValue_1;
                    $Lien_DBVTypeUID_2 = $torderliens_array[$i]->Lien_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_2);
                    $Lien_DBVTypeValue_2 = $torderliens_array[$i]->Lien_DBVTypeValue_2;
                    if($DBVTypeName_1)
                    {
                        if($DBVTypeName_1 !== 'Case number')
                        {
                            array_push($keys, '%%DBVTypeName_1%%');
                            array_push($values, $DBVTypeName_1);
                            if($Lien_DBVTypeValue_1)
                            {
                                array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                array_push($values, $Lien_DBVTypeValue_1);
                            }
                            else{
                                array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%DBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%Lien_DBVTypeValue_1%%');
                            array_push($values, '-'); 
                        }
                    }
                    else
                    {
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Lien_DBVTypeValue_1%%');
                        array_push($values, '-'); 
                    }
                    if($DBVTypeName_2)
                    {
                        if($DBVTypeName_2 !== 'Case number')
                        {
                            
                            array_push($keys, '%%DBVTypeName_2%%');
                            array_push($values, $DBVTypeName_2);
                            if($Lien_DBVTypeValue_2)
                            {
                                array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                array_push($values, $Lien_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%DBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%Lien_DBVTypeValue_2%%');
                            array_push($values, '-'); 
                        }

                    }
                    else{

                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Lien_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                    //CaseNumber
                    if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                    {
                        $AppendCasenumber = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);

                    }
                    if($DBVTypeName_1 == 'Case number'){
                        $AppendCasenumber = $Lien_DBVTypeValue_1;
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);

                    }
                    if($DBVTypeName_2 == 'Case number'){
                        $AppendCasenumber = $Lien_DBVTypeValue_2;
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);
                    }
                    else{
                        $AppendCasenumber = '-';
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);
                    }
                    //CaseNumber
                    //Instrument
                    if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                    {
                        $AppendInstrument = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_1 == 'Instrument'){
                        $AppendInstrument = $Lien_DBVTypeValue_1;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_2 == 'Instrument'){
                        $AppendInstrument = $Lien_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                    else{
                        $AppendInstrument = '-';
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                //Instrument
                //Lein Amount
                    $LeinAmount = $torderliens_array[$i]->LeinAmount;
                    if($LeinAmount)
                    {
                        $MortAmt = '$'.number_format($LeinAmount,2);
                        array_push($keys, '%%LeinAmt%%');
                        array_push($values, $MortAmt);
                    }
                    else{
                        array_push($keys, '%%LeinAmt%%');
                        array_push($values, '-');
                    }
                //Lein Amount
                //Lein Comments
                    $LeinComments = $torderliens_array[$i]->LeinComments;
                    if($LeinComments)
                    {
                        array_push($keys, '%%LeinComments%%');
                        array_push($values, $LeinComments);
                        array_push($keys, '%%leinalignment%%');
                        array_push($values, 'text-left');

                    }
                    else{
                        array_push($keys, '%%LeinComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%leinalignment%%');
                        array_push($values, 'text-center');
                    }
                //Lein Comments

                    $Holder = $torderliens_array[$i]->Holder;
                    array_push($keys, '%%LeinHolder%%');
                    array_push($values, $Holder);

                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
                $output = str_replace($keys, $values, $output);

                $torderliens_table .= str_replace($keys, $values, $torderliens[0][0]);

            }
            foreach ( $doc->find(".torderliens") as $node ) 
            {
                $node->innertext = $torderliens_table;
            }
        //Leins Ends
            return $doc;
        }

        function ez_close_property_report($OrderUID)
        {
            error_reporting(0);
            $this->load->library('Dom/Simple_html_dom');
            $doc = new simple_html_dom();
            $OrderUID =$OrderUID;
            $TemplateName = strtolower(str_replace(' ', '', $order_details->TemplateName));
            $filename = FCPATH.'Templates/ezclosepropertyreport.php';
            $fp = fopen ( $filename, 'r' );
        //read our template into a variable
            $output = fread( $fp, filesize($filename));
        //Orders
            $torders_array = array();
            $keys = array();
            $values = array();
            $date = array('CurrentDate'=>date("m/d/Y"));
            foreach ($date as $key => $value) 
            {

                array_push($keys, '%%'.$key.'%%');
                array_push($values, $value);
            }
            //Address
             include APPPATH .'modules/Order_reports/views/addressvariance.php';
            //Address
            $order_details = $this->Order_reports_model->get_torders($OrderUID);
            $date = $order_details->OrderEntryDatetime;
            $OrderDate =  date('m/d/Y', strtotime($date));
            $dates = array('OrderDate'=>$OrderDate);
            foreach ($dates as $key => $value) 
            {

                array_push($keys, '%%'.$key.'%%');
                array_push($values, $value);
            }
            $Attention =  $order_details->AttentionName;
            if($Attention)
            {
                $AttentionName = $Attention;
            }
            else{

                $AttentionName = '-';
            }
            $base_url = PARENTSITEURL;
            $CustomerPContactMobileNo = $order_details->CustomerPContactMobileNo;
            $CustomerAddress1 = $order_details->CustomerAddress1;
            $CustomerAddress2 = $order_details->CustomerAddress2;
            $Borrowers = $order_details->Borrower;
            $CustomerPContactName =  $order_details->CustomerPContactName;
            $Grantee = $order_details->Grantee;
            $SubProductName = $order_details->SubProductName;
            $APN = $order_details->APN;
            $CustomerName = $order_details->CustomerName;
            $CustomerNumber = $order_details->CustomerNumber;
            if($CustomerNumber)
            {
                $CustomerNumber = $order_details->CustomerNumber.'/';
                $CustomerNo = $order_details->CustomerNumber;
            }
            else{
                $CustomerNumber = '';
            }
            $CustomerStateCode =  $order_details->CustomerStateCode;
            $CustomerCountyName = $order_details->CustomerCountyName;
            $CustomerCityName = $order_details->CustomerCityName;
            $CustomerZipCode = $order_details->CustomerZipCode;
            $OrderNumber = $order_details->OrderNumber;
            $LoanNumber = $order_details->LoanNumber;
            $OwnerName = $order_details->OwnerName;
            $CountyName =  $order_details->CountyName;
            $PropertyAddress1 = $order_details->PropertyAddress1;
            $PropertyAddress2 = $order_details->PropertyAddress2;
            $CityName = $order_details->CityName;
            $StateName = $order_details->StateName;
            $StateCode = $order_details->StateCode;
            $SearchFrom = $order_details->SearchFromDate;
            if($SearchFrom == '0000-00-00 00:00:00' || $SearchFrom == '')
                {
                    $SearchFromDate = '-';
                }
                else
                {
                    $SearchFromDate = date('m/d/Y', strtotime($SearchFrom));
                }
                $SearchAsOf = $order_details->SearchAsOfDate;
                if($SearchAsOf == '0000-00-00 00:00:00' || $SearchAsOf == '')
                    {
                        $SearchAsOfDate = '-';
                    }
                    else
                    {
                        $SearchAsOfDate = date('m/d/Y', strtotime($SearchAsOf));
                    }
                    $DisclaimerNote = $this->Order_reports_model->get_DisclaimerNote($StateName);
                    $ZipCode = $order_details->PropertyZipcode;
                    $LastDeedRecordedDate = $order_details->LastDeedRecorded;
                    $LastDeedRecorded =  date('m/d/Y', strtotime($LastDeedRecordedDate));
                    $ImageUrl = base_url().'assets/img/sourcepoint.png';
                    $Mort = array('SubProductName'=>$SubProductName,'CustomerPContactMobileNo'=>$CustomerPContactMobileNo,'CustomerName'=>$CustomerNumber.$CustomerName,'CustomerNumber'=>$CustomerNo,'Ordernumber'=>$OrderNumber,'Loannumber'=>$LoanNumber,'CustomerStateCode'=>$CustomerStateCode,'CustomerCountyName'=>$CustomerCountyName,'CustomerCityName'=>$CustomerCityName,'Ownername'=>$OwnerName,'Countyname'=>$CountyName,'Propertyaddress1'=>$PropertyAddress1,'Propertyaddress2'=>$PropertyAddress2,'Cityname'=>$CityName,'Statecode'=>$StateCode,'Zip'=>$ZipCode,'APN'=>$APN,'CustomerAddress1'=>$CustomerAddress1,'CustomerAddress2'=>$CustomerAddress2,'CustomerPContactName'=>$CustomerPContactName,'Grantee'=>$Grantee,'LastDeedRecorded'=>$LastDeedRecorded,'DisclaimerNote'=>$DisclaimerNote,'SearchFromDate'=>$SearchFromDate,'SearchAsOfDate'=>$SearchAsOfDate,'Borrower'=>$Borrowers,'Url'=>$base_url,'CustomerZipCode'=>$CustomerZipCode,'AttentionName'=>$AttentionName,'ImageUrl'=>$ImageUrl);
        //Heading
        //Chain of Title
                    if($this->Order_reports_model->get_torderdeeds($OrderUID))
                    {
                        $DeedHeading = ' <tr>
                        <td colspan="3" style="border:0.01em solid grey; border-left:0px; border-right:0px;">
                        <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>TITLE INFORMATION</b></p>
                        </td>
                        </tr>';
                        array_push($keys, '%%DeedHeading%%');
                        array_push($values, $DeedHeading);
                    }
                    else
                    {

                        array_push($keys, '%%DeedHeading%%');
                        array_push($values, ' ');
                    }
        //Chain of Title
        //Leins & Encumbrances
                    if($this->Order_reports_model->get_tordermortgageparties($OrderUID))
                    {
                        $MortgageHeading = '<tr><td colspan="3" style="border-bottom:0.01em solid grey;border-top:0.01px solid grey;">
                        <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>OPEN LIENS / ENCUMBRANCES / OTHER</b></p>
                        </td>
                        </tr>';
                        array_push($keys, '%%MortgageHeading%%');
                        array_push($values, $MortgageHeading);

                    }
                    else
                    {
                        array_push($keys, '%%MortgageHeading%%');
                        array_push($values, ' ');
                    }
        //Leins & Encumbrances 
        //Tax Heading
                    if($this->Order_reports_model->get_tordertaxcerts($OrderUID))
                    {
                        $TaxHeading = '<tr>
                        <td colspan="3" style="border-bottom:0.01em solid grey;">
                        <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>REAL ESTATE TAX</b></p>
                        </td>
                        </tr>';
                        array_push($keys, '%%TaxHeading%%');
                        array_push($values, $TaxHeading);
                    }
                    else
                    {
                        array_push($keys, '%%TaxHeading%%');
                        array_push($values, ' ');
                    }
        //Tax Heading

        //Judgement Heading
                    if($this->Order_reports_model->get_torderjudgements($OrderUID))
                    {
                        $JudgementHeading = '<tr>
                        <td colspan="3" style="border-bottom:0.01px solid grey;">
                        <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>OPEN LIENS / ENCUMBRANCES / OTHER</b></p>
                        </td>
                        </tr>';
                        array_push($keys, '%%JudgementHeading%%');
                        array_push($values, $JudgementHeading);
                    }
                    else
                    {
                        array_push($keys, '%%JudgementHeading%%');
                        array_push($values, ' ');
                    }
        //Judgement heading
        //Liens Heading
                    if($this->Order_reports_model->get_torderliens($OrderUID))
                    {
                        $LienHeading = '<tr>
                        <td colspan="4" style="border:0.01em solid grey;">
                        <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>OPEN LIENS / ENCUMBRANCES / OTHER</b></p>
                        </td>
                        </tr>';
                        array_push($keys, '%%LienHeading%%');
                        array_push($values, $LienHeading);
                    }
                    else{
                        array_push($keys, '%%LienHeading%%');
                        array_push($values, ' ');
                    }
        //liens Heading
        //Heading

                    foreach ($Mort as $key => $value) 
                    {


                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                    }
                    //Orders
                    //Order Assessment
                    $order_assessment = $this->Order_reports_model->get_OrderAssessment($OrderUID);
                    if($order_assessment)
                    {

                        foreach ($order_assessment as $data_orderass_info) 
                        {
                            $AssessedYear = $data_orderass_info->AssessedYear;

                            if($AssessedYear)
                            {
                                array_push($keys, '%%AssessedYear%%');
                                array_push($values, $AssessedYear);
                            }
                            else{
                                array_push($keys, '%%AssessedYear%%');
                                array_push($values, '-');

                            }
                            $Landstr = $data_orderass_info->Land;
                            if($Landstr)
                            {
                                $Landltrim = ltrim($Landstr, '$');
                                $LandRepl = str_replace(",","",$Landltrim);
                                $Lan = substr($LandRepl, 0,-3);
                                $Land = '$'.number_format($Lan,2);
                            }
                            else{
                                $Land = '-';
                            }
                            $Buildingsstr = $data_orderass_info->Buildings;
                            if($Buildingsstr)
                            {

                                $Buildingsltrim = ltrim($Buildingsstr, '$');
                                $BuildingsRepl = str_replace(",","",$Buildingsltrim);
                                $Build = substr($BuildingsRepl, 0,-3);
                                $Buildings = '$'.number_format($Build,2);
                            }
                            else{
                                $Buildings = '-';
                            }

                            $Value = array('TotalValue'=>$TotalValue,'Buildings'=>$Buildings,'Land'=>$Land);
                            foreach ($Value as $key => $value) 
                            {
                                array_push($keys, '%%'.$key.'%%');
                                array_push($values, $value);
                            }
                        } 
                    }
                    else{


                        $TaxArray = array('AssessedYear','Land','Buildings','TotalValue');
                        foreach($TaxArray as $col)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, '-');
                        }

                    }
                    $output = str_replace($keys,$values, $output);
        //Order Assessment
        //Legal Description
                    $legal_information = $this->Order_reports_model->get_LegalDescription($OrderUID);
                    if($legal_information)
                    {
                        foreach ($legal_information as $dm) 
                        {
                            $LegalDescr = str_replace('  ', ' &nbsp;', nl2br(strtoupper($dm->LegalDescription)));

                            if($LegalDescr)
                            {
                                array_push($keys, '%%LegalDescr%%');
                                array_push($values, $LegalDescr);
                            }
                            else{
                                array_push($keys, '%%LegalDescr%%');
                                array_push($values, ' ');
                            }
                            foreach($dm as $cm => $vm)
                            {
                                array_push($keys, '%%'.$cm.'%%');
                                array_push($values, $vm);
                            }
                        }
                    }
                    else{
                        array_push($keys, '%%LegalDescr%%');
                        array_push($values, ' ');
                    }

        //Legal Description
        //Get Borrowers
            $borrowers = $this->Order_reports_model->getBorrowers($OrderUID);
            $Count =  count($borrowers);
            if($Count > 1){

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    if($value->PRName !=''){

                    $borrower .=  trim($value->PRName.',');
                    }
                }
                $borrowers =  rtrim($borrower,",");
                $Array = explode(",", $borrowers);
                $LastArrayValue =  end($Array);
                
                if(count($Array) > 1 ){
                    array_pop($Array);
                    $BorrowerName = implode(", &nbsp;", $Array).' and '.$LastArrayValue;

                }else{
                    
                    $BorrowerName = implode(", &nbsp;", $Array);
                }
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
            else{

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    $borrower .=  $value->PRName.',';

                }
                $BorrowerName =  rtrim($borrower,",");
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
        //Get Borrowers
                    //Property Starts


                    preg_match_all('/<div class=\"torderproperty\">(.*?)<\/div>/s',$output,$torderproperty);
        // $getCounts = $this->Order_reports_model->getCounts($OrderUID);
        // $MortgageCount= $this->Order_reports_model->getMortgageCount($OrderUID);
                    $MortgageCount =  $this->Order_reports_model->getMortgageCount($OrderUID);
if($MortgageCount == 0){ $MortgageCount = "NA"; }
                    array_push($keys, '%%MortgageCount%%');
                    array_push($values, $MortgageCount);
                    $LienCount =  $this->Order_reports_model->getLienCount($OrderUID);
if($LienCount == 0){ $LienCount = "NA"; }
                    array_push($keys, '%%LienCount%%');
                    array_push($values, $LienCount);
                    $JudgementCount =  $this->Order_reports_model->getJudgementCount($OrderUID);
if($JudgementCount == 0){ $JudgementCount = "NA"; }
                    array_push($keys, '%%JudgementCount%%');
                    array_push($values, $JudgementCount);
                    $GranteeGrantor =  $this->Order_reports_model->getGranteeGrantor($OrderUID);
                    if($GranteeGrantor)
                    {
                        foreach ($GranteeGrantor as $col => $value) 
                        {
                            $Grantee = $value->Grantee;
                            if($Grantee)
                            {
                                array_push($keys, '%%Grantee%%');
                                array_push($values, $Grantee);
                            }
                            else{
                                array_push($keys, '%%Grantee%%');
                                array_push($values, '-');
                            }
                            $Grantor = $value->Grantor;
                            if($Grantee){
                                array_push($keys, '%%Grantor%%');
                                array_push($values, $Grantor);
                            }
                            else{
                                array_push($keys, '%%Grantor%%');
                                array_push($values, '-');
                            }
                            $EstateInterestName = $value->EstateInterestName;
                            if($EstateInterestName)
                            {
                                array_push($keys, '%%EstateInterestName%%');
                                array_push($values, $EstateInterestName);
                            }
                            else{
                                array_push($keys, '%%EstateInterestName%%');
                                array_push($values, '-');
                            }

                        } 
                    }
                    else{
                        array_push($keys, '%%Grantee%%');
                        array_push($values, '-');
                        array_push($keys, '%%Grantor%%');
                        array_push($values, '-');
                        array_push($keys, '%%EstateInterestName%%');
                        array_push($values, '-');
                    }



                    $tax_information = $this->Order_reports_model->get_tax($OrderUID);
                    if($tax_information)
                    {
            //Property tax in ISGN Report
                        foreach ($tax_information as $data) 
                        {
                //DeliquentTax
                            if($data->TaxStatusName)
                            {
                                if($data->TaxStatusName == 'Delinquent')
                                {
                                    $DeliquentTax = array('DeliquentTax'=>'Yes');
                                    foreach($DeliquentTax as $col => $val)
                                    {
                                        array_push($keys, '%%'.$col.'%%');
                                        array_push($values, $val);
                                    }
                                }
                                else
                                {
                                    $DeliquentTax = array('DeliquentTax'=>'No');
                                    foreach($DeliquentTax as $col => $val)
                                    {
                                        array_push($keys, '%%'.$col.'%%');
                                        array_push($values, $val);
                                    }
                                }
                            }
                            else{
                                array_push($keys, '%%DeliquentTax%%');
                                array_push($values, '-');
                            }

                            $TaxYear = $data->TaxYear;
                            if($TaxYear)
                            {

                                array_push($keys, '%%TaxYear%%');
                                array_push($values, $TaxYear);
                            }
                            else
                            {
                                array_push($keys, '%%TaxYear%%');
                                array_push($values, '-');
                            }
                            $GrossAmount = $data->GrossAmount;
                            if($GrossAmount)
                            {
                                $TaxYear = $data->TaxYear;
                                if($TaxYear == date("Y"))
                                {
                                    $PropertyTax = '$'.$GrossAmount;
                                    array_push($keys, '%%PropertyTax%%');
                                    array_push($values, $PropertyTax);
                                }
                                else
                                {
                                    $PropertyTax = "$0.00";
                                    array_push($keys, '%%PropertyTax%%');
                                    array_push($values, $PropertyTax);
                                }   
                            }
                            else
                            {
                                array_push($keys, '%%PropertyTax%%');
                                array_push($values, '-');
                            }

                        }
                    }
                    else
                    {
                        $TaxArray = array('TaxYear','PropertyTax','DeliquentTax');
                        foreach($TaxArray as $col)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, '-');
                        }
                    }

                    $property_information = $this->Order_reports_model->getPropertyInformation($OrderUID);
                    if($property_information)
                    {
                        foreach ($property_information as $key => $data) 
                        {
                            $MaritalStatusName = $data->MaritalStatusName;
                            if($MaritalStatusName)
                            {
                                array_push($keys, '%%MaritalStatusName%%');
                                array_push($values, $MaritalStatusName);
                            }
                            else{
                                array_push($keys, '%%MaritalStatusName%%');
                                array_push($values, ' ');
                            }
                            $SubDivisionName = $data->SubDivisionName;
                            if($SubDivisionName)
                            {
                                array_push($keys, '%%SubDivisionName%%');
                                array_push($values, $SubDivisionName);
                            }
                            else{
                                array_push($keys, '%%SubDivisionName%%');
                                array_push($values, '-');
                            }
                            if($data->PropertyUseName =='Exempt' || $data->PropertyUseName == 'Tax Exempt' )
                            {
                                $Exempt = array('Exempt'=>'Yes');
                                foreach($Exempt as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            else
                            {
                                $Exempt = array('Exempt'=>'No');
                                foreach($Exempt as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            $sdMapNo = $data->sdMapNo;
                            if($sdMapNo)
                            {
                                array_push($keys, '%%sdMapNo%%');
                                array_push($values, $sdMapNo);
                            }
                            else{
                                array_push($keys, '%%sdMapNo%%');
                                array_push($values, '-');
                            }
                            $dSection = $data->dSection;
                            if($dSection)
                            {
                                array_push($keys, '%%dSection%%');
                                array_push($values, $dSection);
                            }
                            else{
                                array_push($keys, '%%dSection%%');
                                array_push($values, '-');
                            }
                            $Township = $data->Township;
                            if($Township)
                            {
                                array_push($keys, '%%Township%%');
                                array_push($values, $Township);
                            }
                            else{
                                array_push($keys, '%%Township%%');
                                array_push($values, '-');
                            }
                            $APN = $data->APN;
                            if($APN)
                            {
                                array_push($keys, '%%APN%%');
                                array_push($values, $APN);
                            }
                            else{
                                array_push($keys, '%%APN%%');
                                array_push($values, '-');
                            }
                            $Lot = $data->Lot;
                            if($Lot)
                            {
                                array_push($keys, '%%Lot%%');
                                array_push($values, $Lot);
                            }
                            else{
                                array_push($keys, '%%Lot%%');
                                array_push($values, '-');
                            }

                            $Block = $data->Block;
                            if($Block)
                            {
                                array_push($keys, '%%Block%%');
                                array_push($values, $Block);
                            }
                            else{
                                array_push($keys, '%%Block%%');
                                array_push($values, '-');
                            }
                        }
                    }
                    else
                    {
                        $TaxArray = array('Exempt','APN','Lot','Block','SubDivisionName','MaritalStatusName','sdMapNo','dSection','Township');
                        foreach($TaxArray as $col)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, '-');
                        }
                    }
                //Property Ends
                    $output = str_replace($keys, $values, $output);
                    $doc->load($output);
               //Deed Starts
                    preg_match_all('/<div class=\"torderdeeds\">(.*?)<\/div>/s',$output,$torderdeeds);
                    $torderdeeds_table = '';
                    $torderdeeds_array = array();
                    $torderdeedsparties_array = array();
                    $torderdeeds_array = $this->Order_reports_model->get_torderdeeds($OrderUID);
                    $torderdeeds_array_count = count($torderdeeds_array);

                    for ($i=0; $i < $torderdeeds_array_count; $i++) 
                    { 
                        $torderdeeds_array[$i]->deed_increment = $i+1;
                        $keys = array();
                        $values = array();
                        foreach ($torderdeeds_array[$i] as $key => $value) 
                        {

                            //DocumentTypeName
                            $DocumentTypeName = $torderdeeds_array[$i]->DocumentTypeName;
                            if($DocumentTypeName)
                            {
                                if($DocumentTypeName == 'Others')
                                {
                                    $DeedType = $torderdeeds_array[$i]->DeedType;
                                    if($DeedType)
                                    {
                                        array_push($keys, '%%DeedDocumentTypeName%%');
                                        array_push($values, $DeedType);
                                    }
                                    else{
                                        array_push($keys, '%%DeedDocumentTypeName%%');
                                        array_push($values, '-');
                                    }
                                }
                                else if($DocumentTypeName !== 'Others')
                                {
                                    array_push($keys, '%%DeedDocumentTypeName%%');
                                    array_push($values, $DocumentTypeName);
                                }
                                else
                                {
                                    array_push($keys, '%%DeedDocumentTypeName%%');
                                    array_push($values, '-');
                                }

                            }
                            else{
                                array_push($keys, '%%DeedDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        //DocumentTypeName

                //Grantee
                            $Grantee = $torderdeeds_array[$i]->Grantee;
                            if($Grantee)
                            {
                                array_push($keys,'%%DeedGrantee%%');
                                array_push($values, $Grantee);
                            }
                            else{
                                array_push($keys,'%%DeedGrantee%%');
                                array_push($values, '-');
                            }
                //Grantor
                //Grantee
                            $Grantor = $torderdeeds_array[$i]->Grantor;
                            if($Grantor)
                            {
                                array_push($keys,'%%DeedGrantor%%');
                                array_push($values, $Grantor);
                            }
                            else{
                                array_push($keys,'%%DeedGrantor%%');
                                array_push($values, '-');
                            }
                //Grantee
                //Deed Date Format Change
                            $DDated = $torderdeeds_array[$i]->DeedDated;
                            if($DDated == '0000-00-00')
                            {
                                array_push($keys, '%%DeedDate%%');
                                array_push($values, '-');
                            }
                            else{
                                $DeedDated =  date('m/d/Y', strtotime($DDated));
                                array_push($keys, '%%DeedDate%%');
                                array_push($values, $DeedDated);
                            }
                //Deed Date Format Change
                //Recorded date Format Change
                            $RDated = $torderdeeds_array[$i]->DeedRecorded;
                            if($RDated == '0000-00-00')
                            {
                                array_push($keys, '%%RecordedDate%%');
                                array_push($values, '-');
                            }
                            else{

                                $RecordedDated =  date('m/d/Y', strtotime($RDated));
                                array_push($keys, '%%RecordedDate%%');
                                array_push($values, $RecordedDated);

                            }
                //Recorded date Format Change
                //ConsiderationAmount
                            $ConsiderationAmount = $torderdeeds_array[$i]->ConsiderationAmount;
                            if($ConsiderationAmount)
                            {
                                $ConsiderAmount = '$'.number_format($ConsiderationAmount,2);
                                array_push($keys, '%%ConsiderAmount%%');
                                array_push($values, $ConsiderAmount);
                            }
                            else{
                                array_push($keys, '%%ConsiderAmount%%');
                                array_push($values, '-');
                            }
                //ConsiderationAmount
                //Book/Page
                            $Deed_DBVTypeUID_1 = $torderdeeds_array[$i]->Deed_DBVTypeUID_1;
                            $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_1);
                            $Deed_DBVTypeValue_1 = $torderdeeds_array[$i]->Deed_DBVTypeValue_1;
                            $Deed_DBVTypeUID_2 = $torderdeeds_array[$i]->Deed_DBVTypeUID_2;
                            $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_2);
                            $Deed_DBVTypeValue_2 = $torderdeeds_array[$i]->Deed_DBVTypeValue_2;
                            if($DBVTypeName_1)
                            {
                                array_push($keys, '%%DBVTypeName_1%%');
                                array_push($values, $DBVTypeName_1);
                                if($Deed_DBVTypeValue_1)
                                {
                                    array_push($keys, '%%Deed_DBVTypeValue_1%%');
                                    array_push($values, $Deed_DBVTypeValue_1);
                                }
                                else{
                                    array_push($keys, '%%Deed_DBVTypeValue_1%%');
                                    array_push($values, '-');
                                }
                            }
                            else{
                                array_push($keys, '%%DBVTypeName_1%%');
                                array_push($values, 'Book/Page');
                                array_push($keys, '%%Deed_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }

                            if($DBVTypeName_2)
                            {
                                array_push($keys, '%%DBVTypeName_2%%');
                                array_push($values, $DBVTypeName_2);
                                if($Deed_DBVTypeValue_2)
                                {
                                    array_push($keys, '%%Deed_DBVTypeValue_2%%');
                                    array_push($values, $Deed_DBVTypeValue_2);
                                }
                                else{
                                    array_push($keys, '%%Deed_DBVTypeValue_2%%');
                                    array_push($values, '-');
                                }
                            }
                            else{

                                array_push($keys, '%%DBVTypeName_2%%');
                                array_push($values, 'Instrument');
                                array_push($keys, '%%Deed_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                //Deed Document
                            $EstateInterestName = $torderdeeds_array[$i]->EstateInterestName;
                            if($EstateInterestName)
                            {
                                array_push($keys, '%%DeedEstateInterestName%%');
                                array_push($values, $EstateInterestName);
                            }
                            else{
                                array_push($keys, '%%DeedEstateInterestName%%');
                                array_push($values, '-');
                            }
                //Deed Document
                //Deed Document
                            $DocumentNo = $torderdeeds_array[$i]->DocumentNo;
                            if($DocumentNo)
                            {
                                array_push($keys, '%%DocumentNo%%');
                                array_push($values, $DocumentNo);
                            }
                            else{
                                array_push($keys, '%%DocumentNo%%');
                                array_push($values, '-');
                            }
                //Deed Document
                //Certificate Number
                            $CertificateNo = $torderdeeds_array[$i]->CertificateNo;
                            if($CertificateNo)
                            {
                                array_push($keys, '%%CertificateNo%%');
                                array_push($values, $CertificateNo);
                            }
                            else{
                                array_push($keys, '%%CertificateNo%%');
                                array_push($values, '-');
                            }
                //Certificate Number
                //Instrument Number
                            $InstrumentNo = $torderdeeds_array[$i]->InstrumentNo;
                            if($InstrumentNo)
                            {
                                array_push($keys, '%%InstrumentNo%%');
                                array_push($values, $InstrumentNo);
                            }
                            else{
                                array_push($keys, '%%InstrumentNo%%');
                                array_push($values, '-');
                            }
                //Instrument Number
                //Deed Comments
                            $DeedComments = $torderdeeds_array[$i]->DeedComments;
                            if($DeedComments)
                            {
                                array_push($keys, '%%DeedComments%%');
                                array_push($values, $DeedComments);
                            }
                            else{
                                array_push($keys, '%%DeedComments%%');
                                array_push($values, '-');
                            }
                //Deed Comments
                //Main Loop
                            array_push($keys, '%%'.$key.'%%');
                            array_push($values, $value);
                //Main Loop

            //Latest Deed Grantee
                            $GranteeGrantor =  $this->Order_reports_model->getGranteeGrantor($OrderUID);
                            if($GranteeGrantor)
                            {
                                foreach ($GranteeGrantor as $col => $value) 
                                {
                                    $Grantee = $value->Grantee;
                                    if($Grantee)
                                    {

                                        array_push($keys, '%%LatestDeedGrantee%%');
                                        array_push($values, $Grantee);
                                    }
                                    else{

                                        array_push($keys, '%%LatestDeedGrantee%%');
                                        array_push($values, '-');
                                    }
                                } 
                            }
                            else{
                                array_push($keys, '%%LatestDeedGrantee%%');
                                array_push($values, '-');
                            }
            //Latest Deed Grantee
                        }
                        $output = str_replace($keys, $values, $output);
                        $torderdeeds_table .= str_replace($keys, $values, $torderdeeds[0][0]);

                    }
                    foreach ( $doc->find(".torderdeeds") as $node ) 
                    {
                        $node->innertext = $torderdeeds_table;
                    }
        //Deed Ends
        //Mortgages Starts
                    preg_match_all('/<div class=\"tordermortgages\">(.*?)<\/div>/s',$output,$tordermortgages);
                    $tordermortgages_table = '';
                    $tordermortgages_array = array();
                    $tordermortgagesparties_array = array();
                    $tordermortgages_array = $this->Order_reports_model->get_tordermortgageparties($OrderUID);

                    $tordermortgages_array_count = count($tordermortgages_array);

                    for ($i=0; $i < $tordermortgages_array_count; $i++) 
                    { 
                        $tordermortgages_array[$i]->mortgage_increment = $i+1;
                        $keys = array();
                        $values = array();
                        foreach ($tordermortgages_array[$i] as $key => $value) 
                        {
                            //DocumentTypeName
                            $DocumentTypeName = $tordermortgages_array[$i]->DocumentTypeName;
                            if($DocumentTypeName)
                            {
                                if($DocumentTypeName == 'Others')
                                {
                                    $MortgageType = $tordermortgages_array[$i]->MortgageType;
                                    if($MortgageType)
                                    {
                                        array_push($keys, '%%MortgageDocumentTypeName%%');
                                        array_push($values, $MortgageType);
                                    }
                                    else
                                    {
                                        array_push($keys, '%%MortgageDocumentTypeName%%');
                                        array_push($values, '-');
                                    }
                                }
                                else if($DocumentTypeName !== 'Others')
                                {
                                    array_push($keys, '%%MortgageDocumentTypeName%%');
                                    array_push($values, $DocumentTypeName);
                                }
                                else
                                {
                                    array_push($keys, '%%MortgageDocumentTypeName%%');
                                    array_push($values, '-');
                                }

                            }
                            else{
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, '-');
                            }
                            //DocumentTypeName
                            //Mortgage Date Format Change
                            $MortgageDated = $tordermortgages_array[$i]->MortgageDated;
                            if($MortgageDated == '0000-00-00'){


                                array_push($keys, '%%MortgageDate%%');
                                array_push($values,'-');
                            }
                            else{
                                $MortgageDate =  date('m/d/Y', strtotime($MortgageDated));
                                array_push($keys, '%%MortgageDate%%');
                                array_push($values, $MortgageDate);
                            }
                //Mortgage Date Format Change
                //Mortgage Date Format Change
                            $MortgageRecorded = $tordermortgages_array[$i]->MortgageRecorded;
                            if($MortgageRecorded == '0000-00-00')
                            {
                                array_push($keys, '%%MortgageRecordedDate%%');
                                array_push($values, '-');
                            }
                            else{
                                $MortgageRecordedDate =  date('m/d/Y', strtotime($MortgageRecorded));
                                array_push($keys, '%%MortgageRecordedDate%%');
                                array_push($values, $MortgageRecordedDate);
                            }
                //Mortgage Date Format Change
                //Mortgage Maturity Date Format
                            $MortgageMaturityDate = $tordermortgages_array[$i]->MortgageMaturityDate;
                            if($MortgageMaturityDate == '0000-00-00')
                            {
                                array_push($keys, '%%MaturityDate%%');
                                array_push($values, '-');
                            }
                            else{
                                $MaturityDate =  date('m/d/Y', strtotime($MortgageMaturityDate));
                                array_push($keys, '%%MaturityDate%%');
                                array_push($values, $MaturityDate);
                            }
                //Mortgage Maturity Date Format
                //MortAmount
                            $MortgageAmount = $tordermortgages_array[$i]->MortgageAmount;
                            if($MortgageAmount)
                            {
                                $MortAmt = '$'.number_format($MortgageAmount,2);
                                array_push($keys, '%%MortAmt%%');
                                array_push($values, $MortAmt);
                            }
                            else{
                                array_push($keys, '%%MortAmt%%');
                                array_push($values, '-');
                            }
                //MortAmount
                //Mortgagee
                            $Mortgagee = $tordermortgages_array[$i]->Mortgagee;
                            if($Mortgagee)
                            {
                                array_push($keys, '%%Mortgagee%%');
                                array_push($values, $Mortgagee);
                            }
                            else{
                                array_push($keys, '%%Mortgagee%%');
                                array_push($values, '-');
                            }
                //Mortgagee
                //Trustee
                            $Trustee1 = $tordermortgages_array[$i]->Trustee1;
                            $Trustee2 = $tordermortgages_array[$i]->Trustee2;
                            if($Trustee1 != '' && $Trustee2 != '')
                            {
                                $Trustee = $Trustee1.','.$Trustee2;
                                array_push($keys, '%%Trustee%%');
                                array_push($values, $Trustee);
                            }
                            if($Trustee1 != '')
                            {
                                $Trustee = $Trustee1;
                                array_push($keys, '%%Trustee%%');
                                array_push($values, $Trustee);
                            }
                            if($Trustee2 != ''){

                                $Trustee = $Trustee2;
                                array_push($keys, '%%Trustee%%');
                                array_push($values, $Trustee);

                            }
                            else{

                                array_push($keys, '%%Trustee%%');
                                array_push($values, '-');
                            }
                //Trustee
                            // if($tordermortgages_array[$i]->LienTypeName =='Open Ended')
                            // {
                            //     $MTG = array('MTG'=>'Yes');
                            //     foreach($MTG as $col => $val)
                            //     {
                            //         array_push($keys, '%%'.$col.'%%');
                            //         array_push($values, $val);
                            //     }
                            // }
                            // else
                            // {
                            //     $MTG = array('MTG'=>'No');
                            //     foreach($MTG as $col => $val)
                            //     {
                            //         array_push($keys, '%%'.$col.'%%');
                            //         array_push($values, $val);
                            //     }
                            // }
                                //Closed/Open Ended
                                $OpenEnded = $tordermortgages_array[$i]->IsOpenEnded;
                                if($OpenEnded == '1')
                                {

                                        array_push($keys, '%%OpenEnded%%');
                                        array_push($values, 'Yes');
                                    
                                }
                                else
                                {

                                        array_push($keys, '%%OpenEnded%%');
                                        array_push($values,'-');
                                    
                                }
                //Book/Page
                            $Mortgage_DBVTypeUID_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_1;
                            $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_1);
                            $Mortgage_DBVTypeValue_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_1;
                            $Mortgage_DBVTypeUID_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_2;
                            $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_2);
                            $Mortgage_DBVTypeValue_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_2;

                            if($DBVTypeName_1)
                            {
                                array_push($keys, '%%MortgageDBVTypeName_1%%');
                                array_push($values, $DBVTypeName_1);
                                if($Mortgage_DBVTypeValue_1)
                                {
                                    array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                                    array_push($values, $Mortgage_DBVTypeValue_1);
                                }
                                else{
                                    array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                                    array_push($values, '-');
                                }
                            }
                            else{
                                array_push($keys, '%%MortgageDBVTypeName_1%%');
                                array_push($values, 'Book/Page');
                                array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }

                            if($DBVTypeName_2)
                            {
                                array_push($keys, '%%MortgageDBVTypeName_2%%');
                                array_push($values, $DBVTypeName_2);
                                if($Mortgage_DBVTypeValue_2)
                                {
                                    array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                                    array_push($values, $Mortgage_DBVTypeValue_2);
                                }
                                else{

                                    array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                                    array_push($values, '-');
                                }
                            }
                            else{

                                array_push($keys, '%%MortgageDBVTypeName_2%%');
                                array_push($values, 'Instrument');
                                array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                //Instrument
                            if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                            {
                                $AppendInstrument = $Mortgage_DBVTypeValue_1.','.$Mortgage_DBVTypeValue_2;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);

                            }
                            if($DBVTypeName_1 == 'Instrument'){
                                $AppendInstrument = $Mortgage_DBVTypeValue_1;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);

                            }
                            if($DBVTypeName_2 == 'Instrument'){
                                $AppendInstrument = $Mortgage_DBVTypeValue_2;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);
                            }
                            else{
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, '-');
                            }
                //Instrument
                //Document
                            if($DBVTypeName_1 == 'Document' && $DBVTypeName_2 == 'Document')
                            {
                                $AppendDocument = $Mortgage_DBVTypeValue_1.','.$Mortgage_DBVTypeValue_2;
                                array_push($keys, '%%Document%%');
                                array_push($values, $AppendDocument);

                            }
                            if($DBVTypeName_1 == 'Document'){
                                $AppendDocument = $Mortgage_DBVTypeValue_1;
                                array_push($keys, '%%Document%%');
                                array_push($values, $AppendDocument);

                            }
                            if($DBVTypeName_2 == 'Document'){
                                $AppendDocument = $Mortgage_DBVTypeValue_2;
                                array_push($keys, '%%Document%%');
                                array_push($values, $AppendDocument);
                            }
                            else{
                                array_push($keys, '%%Document%%');
                                array_push($values, '-');
                            }
                //Document
                //Mortgage Comments
                            $MortgageComments = $tordermortgages_array[$i]->MortgageComments;
                            if($MortgageComments)
                            {
                                array_push($keys, '%%MortgageComments%%');
                                array_push($values, $MortgageComments);
                            }
                            else{
                                array_push($keys, '%%MortgageComments%%');
                                array_push($values, '-');
                            }
                //Mortgage Comments
                //Main Loop
                            array_push($keys, '%%'.$key.'%%');
                            array_push($values, $value);
                //Main Loop

                        }

                        $tordermortgages_table.= str_replace($keys, $values, $tordermortgages[0][0]);
                    }

                    foreach ( $doc->find(".tordermortgages") as $node ) 
                    {
                        $node->innertext = $tordermortgages_table;
                    }
        //Mortgages Ends
        //Tax Starts
                    preg_match_all('/<div class=\"tordertaxcerts\">(.*?)<\/div>/s',$output,$tordertaxcerts);
                    $tordertaxcerts_table = '';
                    $tordertaxcerts_array = array();
                    $tordertaxcerts_array = $this->Order_reports_model->get_tordertaxcerts($OrderUID);
                    $tordertaxcerts_array_count = count($tordertaxcerts_array);

                    for ($i=0; $i < $tordertaxcerts_array_count; $i++) 
                    { 
                        $tordertaxcerts_array[$i]->tax_increment = $i+1;
                        $keys = array();
                        $values = array();
                        foreach ($tordertaxcerts_array[$i] as $key => $value) 
                        {
                            //Total Value
                            $TotalValue = $tordertaxcerts_array[$i]->TotalValue;
                            if($TotalValue)
                            {
                                $TotalValue = '$'.$TotalValue;
                                array_push($keys, '%%TotalValue%%');
                                array_push($values, $TotalValue);
                            }
                            else{
                                array_push($keys, '%%TotalValue%%');
                                array_push($values, '-');
                            }
                            //Total Value
                            //Amount Due
                            $AmountDue = $tordertaxcerts_array[$i]->AmountDue;
                            $AmtDue = '$'.number_format($AmountDue,2);
                            if($AmtDue)
                            {
                                array_push($keys, '%%AmountDue%%');
                                array_push($values, $AmtDue);
                            }
                            else{
                                array_push($keys, '%%AmountDue%%');
                                array_push($values, '-');
                            }
                            //Amount Due
                            //Agriculture
                            $Agriculture = $tordertaxcerts_array[$i]->Agriculture;
                            if($Agriculture)
                            {
                                $Agriculture = '$'.$Agriculture;
                                array_push($keys, '%%Agriculture%%');
                                array_push($values, $Agriculture);
                            }
                            else{
                                array_push($keys, '%%Agriculture%%');
                                array_push($values, '-');
                            }
                            //Agriculture
                            //Amount Deliquent
                            $AmountDelinquent = $tordertaxcerts_array[$i]->AmountDelinquent;
                            $AmtDelinquent = '$'.number_format($AmountDelinquent,2);
                            if($AmtDelinquent)
                            {
                                array_push($keys, '%%AmountDelinquent%%');
                                array_push($values, $AmtDelinquent);
                            }
                            else{
                                array_push($keys, '%%AmountDelinquent%%');
                                array_push($values, '-');
                            }
                            //Amount Deliquent
                            //Tax Date Format Change
                            $GoodThroughDate = $tordertaxcerts_array[$i]->GoodThroughDate;
                            if($GoodThroughDate == '0000-00-00')
                            {
                                array_push($keys, '%%ThroughDate%%');
                                array_push($values, '-');
                            }
                            else{
                                $ThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                                array_push($keys, '%%ThroughDate%%');
                                array_push($values, $ThroughDate);
                            }
                            //Tax Date Format Change
                            //Tax Date Paid
                            $DatePaid = $tordertaxcerts_array[$i]->DatePaid;
                            if( $DatePaid  == '0000-00-00')
                            {
                                array_push($keys, '%%PaidDate%%');
                                array_push($values, '-');
                            }
                            else{

                                $PaidDate =  date('m/d/Y', strtotime($DatePaid));
                                array_push($keys, '%%PaidDate%%');
                                array_push($values, $PaidDate);
                            }
                            //Tax Date Paid
                            //Tax Comments
                                $TaxComments = $tordertaxcerts_array[$i]->TaxComments;
                                if($TaxComments)
                                {
                                    array_push($keys, '%%TaxComments%%');
                                    array_push($values, $TaxComments);
                                    array_push($keys, '%%taxalignment%%');
                                    array_push($values, 'text-left');
                                }
                                else{
                                    array_push($keys, '%%TaxComments%%');
                                    array_push($values, '-');
                                    array_push($keys, '%%taxalignment%%');
                                    array_push($values, 'text-center');
                                }
                            //Tax Comments
                            if($tordertaxcerts_array[$i]->Certified =='0')
                            {
                                $CertifiedStatus = array('CertifiedStatus'=>'unchecked.png');
                                foreach($CertifiedStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            else
                            {
                                $CertifiedStatus = array('CertifiedStatus'=>'checked.png');
                                foreach($CertifiedStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            //Total Tax Annual Due in Property Report

                            $tax_information = $this->Order_reports_model->get_tax($OrderUID);
                            if($tax_information)
                            {
                            //Property tax in ISGN Report
                                foreach ($tax_information as $data) 
                                {
                                //DeliquentTax
                                    if($data->TaxStatusName)
                                    {
                                        if($data->TaxStatusName == 'Delinquent')
                                        {
                                            $DeliquentTax = array('DeliquentTax'=>'Yes');
                                            foreach($DeliquentTax as $col => $val)
                                            {
                                                array_push($keys, '%%'.$col.'%%');
                                                array_push($values, $val);
                                            }
                                        }
                                        else
                                        {
                                            $DeliquentTax = array('DeliquentTax'=>'No');
                                            foreach($DeliquentTax as $col => $val)
                                            {
                                                array_push($keys, '%%'.$col.'%%');
                                                array_push($values, $val);
                                            }
                                        }
                                    }
                                    else{
                                        array_push($keys, '%%DeliquentTax%%');
                                        array_push($values, '-');
                                    }

                                    $TaxYear = $data->TaxYear;
                                    if($TaxYear)
                                    {
                                        array_push($keys, '%%TaxYear%%');
                                        array_push($values, $TaxYear);
                                    }
                                    else
                                    {
                                        array_push($keys, '%%TaxYear%%');
                                        array_push($values, '-');
                                    }
                                    $GrossAmount = $data->GrossAmount;
                                    if($GrossAmount)
                                    {
                                        $TaxYear = $data->TaxYear;
                                        if($TaxYear == date("Y"))
                                        {
                                            $PropertyTax = '$'.$GrossAmount;
                                            array_push($keys, '%%PropertyTax%%');
                                            array_push($values, $PropertyTax);
                                        }
                                        else
                                        {
                                            $PropertyTax = "$0.00";
                                            array_push($keys, '%%PropertyTax%%');
                                            array_push($values, $PropertyTax);
                                        }   
                                    }
                                    else
                                    {
                                        array_push($keys, '%%PropertyTax%%');
                                        array_push($values, '-');
                                    }

                                }
                            }
                            else
                            {
                                $TaxArray = array('TaxYear','PropertyTax','DeliquentTax');
                                foreach($TaxArray as $col)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, '-');
                                }
                            }
                            //Total Tax Annual Due in Property Report


                            array_push($keys, '%%'.$key.'%%');
                            array_push($values, $value);
                        }
                        $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                        $TaxCertSNo = $this->Order_reports_model->getExemptionname($OrderUID,$TaxCert);
                        $Tax = explode(",", $TaxCertSNo);
                        if(in_array('Homestead', $Tax))
                        {

                            $ExmpStatus = array('ExmpStatus'=>'checked.png');
                            foreach($ExmpStatus as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        else
                        {
                            $ExmpStatus = array('ExmpStatus'=>'unchecked.png');
                            foreach($ExmpStatus as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        if(in_array('Agricultural', $Tax))
                        {
                            $ExmpStatus = array('AgriExmpStatus'=>'checked.png');
                            foreach($ExmpStatus as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        else
                        {
                            $ExmpStatus = array('AgriExmpStatus'=>'unchecked.png');
                            foreach($ExmpStatus as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        if(in_array('Rural', $Tax))
                        {

                            $ExmpStatus = array('RuralExmpStatus'=>'checked.png');
                            foreach($ExmpStatus as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        else
                        {
                            $ExmpStatus = array('RuralExmpStatus'=>'unchecked.png');
                            foreach($ExmpStatus as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        if(in_array('Urban', $Tax))
                        {
                            $ExmpStatus = array('UrbanExmpStatus'=>'checked.png');
                            foreach($ExmpStatus as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        else
                        {
                            $ExmpStatus = array('UrbanExmpStatus'=>'unchecked.png');
                            foreach($ExmpStatus as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }

                  $getlatesttaxinstallment = $this->Order_reports_model->getlatesttaxinstallment($OrderUID,$TaxCert);
                        if($getlatesttaxinstallment)
                        {
                                foreach ($getlatesttaxinstallment as $key => $value) 
                                {
                                        $TaxYears = $value->TaxYear;
                                        if($TaxYears)
                                        {
                                            array_push($keys, '%%TaxYears%%');
                                            array_push($values, $TaxYears);
                                        }
                                        else{
                                            array_push($keys, '%%TaxYears%%');
                                            array_push($values, '-');
                                        }
                                        //Tax Date Format Change
                                        $NextTaxDueDate = $value->DatePaid;
                                        if($NextTaxDueDate=='0000-00-00' || $NextTaxDueDate == '')
                                        {
                                            array_push($keys, '%%NextTaxDate%%');
                                            array_push($values, '-');
                                        }
                                        else{
                                            $NextTaxDate =  date('m/d/Y', strtotime($NextTaxDueDate));
                                            array_push($keys, '%%NextTaxDate%%');
                                            array_push($values, $NextTaxDate);
                                        }
                                        //Gross Amount
                                        $GrossAmount = $value->LatestGrossAmount;
                                        $GrossAmt = '$'.number_format($GrossAmount,2);
                                        if($GrossAmt)
                                        {
                                            array_push($keys, '%%LatestGrossAmount%%');
                                            array_push($values, $GrossAmt);
                                        }
                                        else{
                                            array_push($keys, '%%LatestGrossAmount%%');
                                            array_push($values, '-');
                                        }
                                        //Gross Amount
                                        //Amount Paid
                                        $AmountPaid = $value->AmountPaid;
                                        $AmtPaid = '$'.number_format($AmountPaid,2);
                                        if($AmtPaid)
                                        {
                                            array_push($keys, '%%AmtPaid%%');
                                            array_push($values, $AmtPaid);
                                        }
                                        else{
                                            array_push($keys, '%%AmtPaid%%');
                                            array_push($values, '-');
                                        }
                                        //Amount Paid
                                        //Tax Status Name
                                        $TaxStatusName = $value->TaxStatusName;
                                        if($TaxStatusName)
                                        {
                                            array_push($keys, '%%StatusName%%');
                                            array_push($values, $TaxStatusName);
                                        }
                                        else{
                                            array_push($keys, '%%StatusName%%');
                                            array_push($values, '-');
                                        }
                                        //Tax Status Name
                                        //Tax Date Format Change
                                }
                        }
                        else{

                            $data = array('TaxYears','NextTaxDate','LatestGrossAmount','AmtPaid','StatusName');
                            foreach ($data as $value) {
                                array_push($keys, '%%'.$value.'%%');
                                array_push($values, '-');
                            }
                        }



                        $output = str_replace($keys, $values, $output);
                        $tordertaxcerts_table .= str_replace($keys, $values, $tordertaxcerts[0][0]);

                    }
                    foreach ( $doc->find(".tordertaxcerts") as $node ) 
                    {
                        $node->innertext = $tordertaxcerts_table;
                    }
        //Tax Ends
        //Liens Starts
                    preg_match_all('/<div class=\"torderliens\">(.*?)<\/div>/s',$output,$torderliens);
                    $torderliens_table = '';
                    $torderliens_array = array();
                    $torderliens_array = $this->Order_reports_model->get_torderliens($OrderUID);
                    $torderliens_array_count = count($torderliens_array);
                    for ($i=0; $i < $torderliens_array_count; $i++) 
                    { 

                        $torderliens_array[$i]->lien_increment = $i+1;

                        $keys = array();
                        $values = array();
                        foreach ($torderliens_array[$i] as $key => $value) 
                        {
                //DocumentTypeName
                    $DocumentTypeName = $torderliens_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $LeinType = $torderliens_array[$i]->LeinType;
                            if($LeinType)
                            {
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, $LeinType);
                            }
                            else{
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%LeinDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //DocumentTypeName
                //Lein Date Format Change
                            $LDated = $torderliens_array[$i]->LeinDated;
                            if($LDated =='0000-00-00')
                            {
                                array_push($keys, '%%LeinDate%%');
                                array_push($values, '-');
                            }
                            else{
                                $Dated =  date('m/d/Y', strtotime($LDated));
                                array_push($keys, '%%LeinDate%%');
                                array_push($values, $Dated);
                            }
                //Lein Date Format Change
                //Lein Amount
                            $LeinAmount = $torderliens_array[$i]->LeinAmount;
                            if($LeinAmount)
                            {
                                $MortAmt = '$'.number_format($LeinAmount,2);
                                array_push($keys, '%%LeinAmt%%');
                                array_push($values, $MortAmt);
                            }
                            else{
                                array_push($keys, '%%LeinAmt%%');
                                array_push($values, '-');
                            }
                //Lein Amount
                //Lein Comments
                            $LeinComments = $torderliens_array[$i]->LeinComments;
                            if($LeinComments)
                            {
                                array_push($keys, '%%LeinComments%%');
                                array_push($values, $LeinComments);
                            }
                            else{
                                array_push($keys, '%%LeinComments%%');
                                array_push($values, '-');
                            }
                //Lein Comments
                //Lein Recorded date Format Change
                            $RDated = $torderliens_array[$i]->LeinRecorded;
                            if($RDated == '0000-00-00')
                            {
                                array_push($keys, '%%LeinRecord%%');
                                array_push($values, '-');
                            }
                            else{
                                $RecordedDated =  date('m/d/Y', strtotime($RDated));
                                array_push($keys, '%%LeinRecord%%');
                                array_push($values, $RecordedDated);
                            }
                // Lein date Format Change
                            if($torderliens_array[$i]->LienTypeName =='Closed Ended')
                            {
                                $MTG = array('MTG'=>'N/A');
                                foreach($MTG as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            if($torderliens_array[$i]->LienTypeName =='Open Ended')
                            {
                                $MTG = array('MTG'=>'Yes');
                                foreach($MTG as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            else
                            {
                                $MTG = array('MTG'=>'No');
                                foreach($MTG as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }

                            $Lien_DBVTypeUID_1 = $torderliens_array[$i]->Lien_DBVTypeUID_1;
                            $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_1);
                            $Lien_DBVTypeValue_1 = $torderliens_array[$i]->Lien_DBVTypeValue_1;
                            $Lien_DBVTypeUID_2 = $torderliens_array[$i]->Lien_DBVTypeUID_2;
                            $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_2);
                            $Lien_DBVTypeValue_2 = $torderliens_array[$i]->Lien_DBVTypeValue_2;
                            if($DBVTypeName_1)
                            {
                                array_push($keys, '%%LienDBVTypeName_1%%');
                                array_push($values, $DBVTypeName_1);
                                if($Lien_DBVTypeValue_1)
                                {
                                    array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                    array_push($values, $Lien_DBVTypeValue_1);
                                }
                                else{
                                    array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                    array_push($values, '-');
                                }
                            }
                            else{
                                array_push($keys, '%%LienDBVTypeName_1%%');
                                array_push($values, 'Book/Page');
                                array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }

                            if($DBVTypeName_2)
                            {
                                array_push($keys, '%%LienDBVTypeName_2%%');
                                array_push($values, $DBVTypeName_2);
                                if($Lien_DBVTypeValue_2)
                                {
                                    array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                    array_push($values, $Lien_DBVTypeValue_2);
                                }
                                else{
                                    array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                    array_push($values, '-');
                                }
                            }
                            else{

                                array_push($keys, '%%LienDBVTypeName_2%%');
                                array_push($values, 'Instrument');
                                array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                //CaseNumber
                            if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                            {
                                $AppendCasenumber = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                                array_push($keys, '%%CaseNumber%%');
                                array_push($values, $AppendCasenumber);

                            }
                            if($DBVTypeName_1 == 'Case number'){
                                $AppendCasenumber = $Lien_DBVTypeValue_1;
                                array_push($keys, '%%CaseNumber%%');
                                array_push($values, $AppendCasenumber);

                            }
                            if($DBVTypeName_2 == 'Case number'){
                                $AppendCasenumber = $Lien_DBVTypeValue_2;
                                array_push($keys, '%%CaseNumber%%');
                                array_push($values, $AppendCasenumber);
                            }
                            else{
                                array_push($keys, '%%CaseNumber%%');
                                array_push($values, '-');
                            }
                //CaseNumber
                //Instrument
                            if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                            {
                                $AppendInstrument = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);

                            }
                            if($DBVTypeName_1 == 'Instrument'){
                                $AppendInstrument = $Lien_DBVTypeValue_1;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);

                            }
                            if($DBVTypeName_2 == 'Instrument'){
                                $AppendInstrument = $Lien_DBVTypeValue_2;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);
                            }
                            else{

                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, '-');
                            }
                //Instrument

                            $Holder = $torderliens_array[$i]->Holder;
                            if($Holder)
                            {
                                array_push($keys, '%%LeinHolder%%');
                                array_push($values, $Holder);
                            }
                            else{
                                array_push($keys, '%%LeinHolder%%');
                                array_push($values, '-');
                            }

                            array_push($keys, '%%'.$key.'%%');
                            array_push($values, $value);
                        }
                        $output = str_replace($keys, $values, $output);

                        $torderliens_table .= str_replace($keys, $values, $torderliens[0][0]);

                    }
                    foreach ( $doc->find(".torderliens") as $node ) 
                    {
                        $node->innertext = $torderliens_table;
                    }
        //Leins Ends
        //Judgement Starts
                    preg_match_all('/<div class=\"torderjudgments\">(.*?)<\/div>/s',$output,$torderjudgments);
                    $torderjudgments_table = '';
                    $torderjudgments_array = array();
                    $torderjudgments_array = $this->Order_reports_model->get_torderjudgements($OrderUID);
                    $torderjudgments_array_count = count($torderjudgments_array);
                    for ($i=0; $i < $torderjudgments_array_count; $i++)
                    { 
                        $torderjudgments_array[$i]->judgement_increment = $i+1;
                        $keys = array();
                        $values = array();
                        foreach ($torderjudgments_array[$i] as $key => $value)
                        {

                            //DocumentTypeName
                                $DocumentTypeName = $torderjudgments_array[$i]->DocumentTypeName;
                                if($DocumentTypeName)
                                {
                                    if($DocumentTypeName == 'Others')
                                    {
                                        $JudgementType = $torderjudgments_array[$i]->JudgementType;
                                        if($JudgementType)
                                        {
                                            array_push($keys, '%%JudgementDocumentTypeName%%');
                                            array_push($values, $JudgementType);
                                        }
                                        else{
                                            array_push($keys, '%%JudgementDocumentTypeName%%');
                                            array_push($values, '-');
                                        }
                                    }
                                    else if($DocumentTypeName !== 'Others')
                                    {
                                        array_push($keys, '%%JudgementDocumentTypeName%%');
                                        array_push($values, $DocumentTypeName);
                                    }
                                    else
                                    {
                                        array_push($keys, '%%JudgementDocumentTypeName%%');
                                        array_push($values, '-');
                                    }

                                }
                                else{
                                    array_push($keys, '%%JudgementDocumentTypeName%%');
                                    array_push($values, '-');
                                }
                            //DocumentTypeName

                            //Plaintiff
                            $Plaintiff = $torderjudgments_array[$i]->Plaintiff;
                            if($Plaintiff)
                            {
                                array_push($keys, '%%Plaintiff%%');
                                array_push($values, $Plaintiff);
                            }
                            else{
                                array_push($keys, '%%Plaintiff%%');
                                array_push($values, '-');
                            }
                //Plaintiff
                //Defendant
                            $Defendent = $torderjudgments_array[$i]->Defendent;
                            if($Defendent)
                            {
                                array_push($keys, '%%Defendent%%');
                                array_push($values, $Defendent);
                            }
                            else{
                                array_push($keys, '%%Defendent%%');
                                array_push($values, '-');
                            }
                //Defendant
                //DocumentTypeName
                            $JudDocumentTypeName = $torderjudgments_array[$i]->DocumentTypeName;
                            if($JudDocumentTypeName)
                            {
                                array_push($keys, '%%JudDocumentTypeName%%');
                                array_push($values, $JudDocumentTypeName);
                            }
                            else{
                                array_push($keys, '%%JudDocumentTypeName%%');
                                array_push($values, '-');
                            }
                //DocumentTypeName
                //Judgement Amount
                            $JudgementAmount = $torderjudgments_array[$i]->JudgementAmount;
                            if($JudgementAmount)
                            {
                                $JudAmt = '$'.number_format($JudgementAmount,2);
                                array_push($keys, '%%JudgementAmount%%');
                                array_push($values, $JudAmt);
                            }
                            else{
                                array_push($keys, '%%JudgementAmount%%');
                                array_push($values, '-');
                            }
                //Judgement Amount
                //Judgement Date Format Change
                            $JDated = $torderjudgments_array[$i]->JudgementDated;
                            if($JDated == '0000-00-00')
                            {
                                array_push($keys, '%%JudgeDated%%');
                                array_push($values, '-');
                            }
                            else{
                                $Dated =  date('m/d/Y', strtotime($JDated));
                                array_push($keys, '%%JudgeDated%%');
                                array_push($values, $Dated);
                            }
                //Judgement Date Format Change
                //Judgement Recorded date Format Change
                            $RDated = $torderjudgments_array[$i]->JudgementRecorded;
                            if($RDated == '0000-00-00')
                            {   
                                array_push($keys, '%%JudgeRecorded%%');
                                array_push($values, '-');
                            }
                            else{
                                $RecordedDated =  date('m/d/Y', strtotime($RDated));
                                array_push($keys, '%%JudgeRecorded%%');
                                array_push($values, $RecordedDated);
                            }
                // JudgementRecorded date Format Change
                // Judgement Comments
                            $JudgementComments = $torderjudgments_array[$i]->JudgementComments;
                            if($JudgementComments)
                            {
                                array_push($keys, '%%JudgementComments%%');
                                array_push($values, $JudgementComments);
                            }
                            else{
                                array_push($keys, '%%JudgementComments%%');
                                array_push($values, '-');
                            }
                // Judgement Comments
                            $Judgement_DBVTypeUID_1 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_1;
                            $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_1);
                            $Judgement_DBVTypeValue_1 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_1;
                            $Judgement_DBVTypeUID_2 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_2;
                            $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_2);
                            $Judgement_DBVTypeValue_2 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_2;
                            if($DBVTypeName_1)
                            {
                                array_push($keys, '%%JudgementDBVTypeName_1%%');
                                array_push($values, $DBVTypeName_1);
                                if($Judgement_DBVTypeValue_1)
                                {
                                    array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                    array_push($values, $Judgement_DBVTypeValue_1);
                                }
                                else{
                                    array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                    array_push($values, '-');
                                }
                            }
                            else{
                                array_push($keys, '%%JudgementDBVTypeName_1%%');
                                array_push($values, 'Book/Page');
                                array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                            if($DBVTypeName_2)
                            {
                                array_push($keys, '%%JudgementDBVTypeName_2%%');
                                array_push($values, $DBVTypeName_2);
                                if($Judgement_DBVTypeValue_2)
                                {
                                    array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                    array_push($values, $Judgement_DBVTypeValue_2);
                                }
                                else{
                                    array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                    array_push($values, '-');
                                }
                            }
                            else{

                                array_push($keys, '%%JudgementDBVTypeName_2%%');
                                array_push($values, 'Instrument');
                                array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                //Instrument
                            if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                            {
                                $AppendInstrument = $Judgement_DBVTypeValue_1.','.$Judgement_DBVTypeValue_2;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);

                            }
                            if($DBVTypeName_1 == 'Instrument'){
                                $AppendInstrument = $Judgement_DBVTypeValue_1;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);

                            }
                            if($DBVTypeName_2 == 'Instrument'){
                                $AppendInstrument = $Judgement_DBVTypeValue_2;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);
                            }
                            else{
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, '-');
                            }
                //Instrument

                //CaseNumber
                            $JudgementCaseNo = $torderjudgments_array[$i]->JudgementCaseNo;
                            if($JudgementCaseNo)
                            {
                                array_push($keys, '%%CaseNumber%%');
                                array_push($values, $JudgementCaseNo);
                            }
                            else{
                                if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                                {
                                    $AppendCasenumber = $Judgement_DBVTypeValue_1.','.$Judgement_DBVTypeValue_2;
                                    array_push($keys, '%%CaseNumber%%');
                                    array_push($values, $AppendCasenumber);

                                }
                                if($DBVTypeName_1 == 'Case number'){
                                    $AppendCasenumber = $Judgement_DBVTypeValue_1;
                                    array_push($keys, '%%CaseNumber%%');
                                    array_push($values, $AppendCasenumber);

                                }
                                if($DBVTypeName_2 == 'Case number'){
                                    $AppendCasenumber = $Judgement_DBVTypeValue_2;
                                    array_push($keys, '%%CaseNumber%%');
                                    array_push($values, $AppendCasenumber);
                                }
                                else{
                                    array_push($keys, '%%CaseNumber%%');
                                    array_push($values, '-');
                                }
                            }
                //CaseNumber
                            array_push($keys, '%%'.$key.'%%');
                            array_push($values, $value);
                        }
                        $torderjudgments_table .= str_replace($keys, $values, $torderjudgments[0][0]);
                    }
                    foreach ( $doc->find(".torderjudgments") as $node ) 
                    {
                        $node->innertext = $torderjudgments_table;
                    }
        //Judgments Ends
                    return $doc;
                }

                function ez_close_deed_report($OrderUID)
                {
                    $this->load->library('Dom/Simple_html_dom');
                    $doc = new simple_html_dom();       
                    $OrderUID = $OrderUID;
        // $TemplateName = strtolower(str_replace(' ', '', $order_details->TemplateName));
                    $filename = FCPATH.'Templates/ezclosedeedreport.php';
                    $fp = fopen ( $filename, 'r' );
                    $output = fread( $fp, filesize($filename));
                    $torders_array = array(); 
                    $keys = array();
                    $values = array();
                    $date = array('CurrentDate'=>date("m/d/Y"));
                    foreach ($date as $key => $value) 
                    {

                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                    }
                    $order_details = $this->Order_reports_model->get_torders($OrderUID);
                    $date = $order_details->OrderEntryDatetime;
                    $OrderDate =  date('m/d/Y', strtotime($date));
                    $dates = array('OrderDate'=>$OrderDate);
                    foreach ($dates as $key => $value) 
                    {

                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                    }
                    $Attention =  $order_details->AttentionName;
                    if($Attention)
                    {
                        $AttentionName = $Attention;
                    }
                    else{

                        $AttentionName = '-';
                    }
        //Orders
                    $base_url = FCPATH;
                    $Borrowers = $order_details->Borrower;
                    $CustomerPContactMobileNo = $order_details->CustomerPContactMobileNo;
                    $CustomerAddress1 = $order_details->CustomerAddress1;
                    $CustomerAddress2 = $order_details->CustomerAddress2;
                    $CustomerStateCode =  $order_details->CustomerStateCode;
                    $CustomerCountyName = $order_details->CustomerCountyName;
                    $CustomerCityName = $order_details->CustomerCityName;
                    $CustomerZipCode = $order_details->CustomerZipCode;
                    $CustomerPContactName =  $order_details->CustomerPContactName;
                    $Grantee = $order_details->Grantee;
                    $APN = $order_details->APN;
                    $CustomerName = $order_details->CustomerName;
                    $CustomerNumber = $order_details->CustomerNumber;
                    if($CustomerNumber)
                    {
                        $CustomerNumber = $order_details->CustomerNumber.'/';
                        $CustomerNo = $order_details->CustomerNumber;
                    }
                    else{
                        $CustomerNumber = '';
                    }
                    $OrderNumber = $order_details->OrderNumber;
                    $LoanNumber = $order_details->LoanNumber;
                    $OwnerName = $order_details->OwnerName;
                    $CountyName =  $order_details->CountyName;
                    $PropertyAddress1 = $order_details->PropertyAddress1;
                    $PropertyAddress2 = $order_details->PropertyAddress2;
                    $CityName = $order_details->CityName;
                    $StateName = $order_details->StateName;
                    $StateCode = $order_details->StateCode;
                    $DisclaimerNote = $this->Order_reports_model->get_DisclaimerNote($StateName);
                    $ZipCode = $order_details->PropertyZipcode;
                    $LastDeedRecordedDate = $order_details->LastDeedRecorded;
                    $LastDeedRecorded =  date('m/d/Y', strtotime($LastDeedRecordedDate));
                    $ImageUrl = base_url().'assets/img/sourcepoint.png';
                    $Mort = array('CustomerPContactMobileNo'=>$CustomerPContactMobileNo,'CustomerName'=>$CustomerNumber.$CustomerName,'CustomerNumber'=>$CustomerNo,'Ordernumber'=>$OrderNumber,'Loannumber'=>$LoanNumber,'Ownername'=>$OwnerName,'Countyname'=>$CountyName,'Propertyaddress1'=>$PropertyAddress1,'Propertyaddress2'=>$PropertyAddress2,'Cityname'=>$CityName,'Statecode'=>$StateCode,'Zip'=>$ZipCode,'APN'=>$APN,'CustomerAddress1'=>$CustomerAddress1,'CustomerAddress2'=>$CustomerAddress2,'CustomerPContactName'=>$CustomerPContactName,'Grantee'=>$Grantee,'LastDeedRecorded'=>$LastDeedRecorded,'DisclaimerNote'=>$DisclaimerNote,'Url'=>$base_url,'Borrower'=>$Borrowers,'CustomerStateCode'=>$CustomerStateCode,'CustomerCountyName'=>$CustomerCountyName,'CustomerCityName'=>$CustomerCityName,'CustomerZipCode'=>$CustomerZipCode,'AttentionName'=>$AttentionName,'ImageUrl'=>$ImageUrl);

        //Heading
        //Chain of Title
                    if($this->Order_reports_model->get_torderdeeds($OrderUID))
                    {
                        $DeedHeading = ' <tr>
                        <td colspan="3" style="border:0.01em solid grey; border-left:0px; border-right:0px;">
                        <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>TITLE INFORMATION</b></p>
                        </td>
                        </tr>';
                        array_push($keys, '%%DeedHeading%%');
                        array_push($values, $DeedHeading);
                    }
                    else
                    {

                        array_push($keys, '%%DeedHeading%%');
                        array_push($values, ' ');
                    }
        //Chain of Title
        //Leins & Encumbrances
                    if($this->Order_reports_model->get_tordermortgageparties($OrderUID))
                    {
                        $MortgageHeading = '<tr><td colspan="3" style="border-bottom:0.01em solid grey;">
                        <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>MORTGAGE</b></p>
                        </td>
                        </tr>';
                        array_push($keys, '%%MortgageHeading%%');
                        array_push($values, $MortgageHeading);

                    }
                    else
                    {
                        array_push($keys, '%%MortgageHeading%%');
                        array_push($values, ' ');
                    }
        //Leins & Encumbrances 
        //Tax Heading
                    if($this->Order_reports_model->get_tordertaxcerts($OrderUID))
                    {
                        $TaxHeading = '<tr>
                        <td colspan="3" style="border-bottom:0.01px solid grey;border-top:none">
                        <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>REAL ESTATE TAX</b></p>
                        </td>
                        </tr>';
                        array_push($keys, '%%TaxHeading%%');
                        array_push($values, $TaxHeading);
                    }
                    else
                    {
                        array_push($keys, '%%TaxHeading%%');
                        array_push($values, ' ');
                    }
        //Tax Heading

        //Judgement Heading
                    if($this->Order_reports_model->get_torderjudgements($OrderUID))
                    {
                        $JudgementHeading = '<tr>
                        <td colspan="3" style="border-bottom:0.01px solid grey;">
                        <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>JUDGEMENT</b></p>
                        </td>
                        </tr>';
                        array_push($keys, '%%JudgementHeading%%');
                        array_push($values, $JudgementHeading);
                    }
                    else
                    {
                        array_push($keys, '%%JudgementHeading%%');
                        array_push($values, ' ');
                    }
        //Judgement heading
        //Liens Heading
                    if($this->Order_reports_model->get_torderliens($OrderUID))
                    {
                        $LienHeading = '<tr>
                        <td colspan="4" style="border-bottom:0.01px solid grey;">
                        <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>LIENS</b></p>
                        </td>
                        </tr>';
                        array_push($keys, '%%LienHeading%%');
                        array_push($values, $LienHeading);
                    }
                    else{
                        array_push($keys, '%%LienHeading%%');
                        array_push($values, ' ');
                    }
        //liens Heading
        //Heading
                    foreach ($Mort as $key => $value) 
                    {
                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                    }
        //Orders
        //Order Assessment
                    $order_assessment = $this->Order_reports_model->get_OrderAssessment($OrderUID);
                    if($order_assessment)
                    {

                        foreach ($order_assessment as $data_orderass_info) 
                        {
                            $AssessedYear = $data_orderass_info->AssessedYear;

                            if($AssessedYear)
                            {
                                array_push($keys, '%%AssessedYear%%');
                                array_push($values, $AssessedYear);
                            }
                            else{
                                array_push($keys, '%%AssessedYear%%');
                                array_push($values, '-');

                            }
                            $Landstr = $data_orderass_info->Land;
                            if($Landstr)
                            {
                                $Landltrim = ltrim($Landstr, '$');
                                $LandRepl = str_replace(",","",$Landltrim);
                                $Lan = substr($LandRepl, 0,-3);
                                $Land = '$'.number_format($Lan,2);
                            }
                            else{
                                $Land = '-';
                            }
                            $Buildingsstr = $data_orderass_info->Buildings;
                            if($Buildingsstr)
                            {

                                $Buildingsltrim = ltrim($Buildingsstr, '$');
                                $BuildingsRepl = str_replace(",","",$Buildingsltrim);
                                $Build = substr($BuildingsRepl, 0,-3);
                                $Buildings = '$'.number_format($Build,2);
                            }
                            else{
                                $Buildings = '-';
                            }


                            $Value = array('TotalValue'=>$TotalValue,'Buildings'=>$Buildings,'Land'=>$Land);
                            foreach ($Value as $key => $value) 
                            {
                                array_push($keys, '%%'.$key.'%%');
                                array_push($values, $value);
                            }
                        } 
                    }
                    else{


                        $TaxArray = array('AssessedYear','Land','Buildings','TotalValue');
                        foreach($TaxArray as $col)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, '-');
                        }

                    }
                    $output = str_replace($keys,$values, $output);
        //Get Borrowers
            $borrowers = $this->Order_reports_model->getBorrowers($OrderUID);
            $Count =  count($borrowers);
            if($Count > 1){

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    if($value->PRName !=''){

                    $borrower .=  trim($value->PRName.',');
                    }
                }
                $borrowers =  rtrim($borrower,",");
                $Array = explode(",", $borrowers);
                $LastArrayValue =  end($Array);
                
                if(count($Array) > 1 ){
                    array_pop($Array);
                    $BorrowerName = implode(", &nbsp;", $Array).' and '.$LastArrayValue;

                }else{
                    
                    $BorrowerName = implode(", &nbsp;", $Array);
                }
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
            else{

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    $borrower .=  $value->PRName.',';

                }
                $BorrowerName =  rtrim($borrower,",");
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
        //Get Borrowers
        //Order Assessment
        //Legal Description
                    $legal_information = $this->Order_reports_model->get_LegalDescription($OrderUID);
                    if($legal_information) 
                    {
                        foreach($legal_information as $data) 
                        {
                            $LegalDescr = str_replace('  ', ' &nbsp;', nl2br(strtoupper($data->LegalDescription)));
                            if($LegalDescr) 
                            {
                                array_push($keys, '%%LegalDescr%%');
                                array_push($values, $LegalDescr);
                            }
                            else{
                                array_push($keys, '%%LegalDescr%%');
                                array_push($values, ' ');
                            }
                            foreach($data as $col_legal => $val_legal)
                            {
                                array_push($keys, '%%'.$col_legal.'%%');
                                array_push($values, $val_legal);
                            }
                        }
                    }
                    else{
                        array_push($keys, '%%LegalDescr%%');
                        array_push($values, ' ');
                    }

        //Legal Description 
        //Property Information
                    preg_match_all('/<div class=\"torderproperty\">(.*?)<\/div>/s',$output,$torderproperty);
                    $MortgageCount =  $this->Order_reports_model->getMortgageCount($OrderUID);
if($MortgageCount == 0){ $MortgageCount = "NA"; }
                    array_push($keys, '%%MortgageCount%%');
                    array_push($values, $MortgageCount);
                    $LienCount =  $this->Order_reports_model->getLienCount($OrderUID);
if($LienCount == 0){ $LienCount = "NA"; }
                    array_push($keys, '%%LienCount%%');
                    array_push($values, $LienCount);
                    $JudgementCount =  $this->Order_reports_model->getJudgementCount($OrderUID);
if($JudgementCount == 0){ $JudgementCount = "NA"; }
                    array_push($keys, '%%JudgementCount%%');
                    array_push($values, $JudgementCount);
                    $GranteeGrantor =  $this->Order_reports_model->getGranteeGrantor($OrderUID);
                    if($GranteeGrantor)
                    {
                        foreach ($GranteeGrantor as $col => $value) 
                        {
                            $Grantee = $value->Grantee;
                            if($Grantee)
                            {
                                array_push($keys, '%%Grantee%%');
                                array_push($values, $Grantee);
                            }
                            else{
                                array_push($keys, '%%Grantee%%');
                                array_push($values, '-');
                            }
                            $Grantor = $value->Grantor;
                            if($Grantee){
                                array_push($keys, '%%Grantor%%');
                                array_push($values, $Grantor);
                            }
                            else{
                                array_push($keys, '%%Grantor%%');
                                array_push($values, '-');
                            }
                            $EstateInterestName = $value->EstateInterestName;
                            if($EstateInterestName)
                            {
                                array_push($keys, '%%EstateInterestName%%');
                                array_push($values, $EstateInterestName);
                            }
                            else{
                                array_push($keys, '%%EstateInterestName%%');
                                array_push($values, '-');
                            }

                        } 
                    }
                    else{
                        array_push($keys, '%%Grantee%%');
                        array_push($values, '-');
                        array_push($keys, '%%Grantor%%');
                        array_push($values, '-');
                        array_push($keys, '%%EstateInterestName%%');
                        array_push($values, '-');
                    }
                    $tax_information = $this->Order_reports_model->get_tax($OrderUID);
                    if($tax_information)
                    {
            //Property tax in ISGN Report
                        foreach ($tax_information as $data) 
                        {
                //DeliquentTax
                            if($data->TaxStatusName)
                            {
                                if($data->TaxStatusName == 'Delinquent')
                                {
                                    $DeliquentTax = array('DeliquentTax'=>'Yes');
                                    foreach($DeliquentTax as $col => $val)
                                    {
                                        array_push($keys, '%%'.$col.'%%');
                                        array_push($values, $val);
                                    }
                                }
                                else
                                {
                                    $DeliquentTax = array('DeliquentTax'=>'No');
                                    foreach($DeliquentTax as $col => $val)
                                    {
                                        array_push($keys, '%%'.$col.'%%');
                                        array_push($values, $val);
                                    }
                                }
                            }
                            else{
                                array_push($keys, '%%DeliquentTax%%');
                                array_push($values, '-');
                            }

                            $TaxYear = $data->TaxYear;
                            if($TaxYear)
                            {

                                array_push($keys, '%%TaxYear%%');
                                array_push($values, $TaxYear);
                            }
                            else
                            {
                                array_push($keys, '%%TaxYear%%');
                                array_push($values, '-');
                            }
                            $GrossAmount = $data->GrossAmount;
                            if($GrossAmount)
                            {
                                $TaxYear = $data->TaxYear;
                                if($TaxYear == date("Y"))
                                {
                                    $PropertyTax = '$'.$GrossAmount;
                                    array_push($keys, '%%PropertyTax%%');
                                    array_push($values, $PropertyTax);
                                }
                                else
                                {
                                    $PropertyTax = "$0.00";
                                    array_push($keys, '%%PropertyTax%%');
                                    array_push($values, $PropertyTax);
                                }   
                            }
                            else
                            {
                                array_push($keys, '%%PropertyTax%%');
                                array_push($values, '-');
                            }

                        }
                    }
                    else
                    {
                        $TaxArray = array('TaxYear','PropertyTax','DeliquentTax');
                        foreach($TaxArray as $col)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, '-');
                        }
                    }

                    $property_information = $this->Order_reports_model->getPropertyInformation($OrderUID);
                    if($property_information)
                    {
                        foreach ($property_information as $key => $data) 
                        {
                            $MaritalStatusName = $data->MaritalStatusName;
                            if($MaritalStatusName)
                            {
                                array_push($keys, '%%MaritalStatusName%%');
                                array_push($values, $MaritalStatusName);
                            }
                            else{
                                array_push($keys, '%%MaritalStatusName%%');
                                array_push($values, '-');
                            }
                            $SubDivisionName = $data->SubDivisionName;
                            if($SubDivisionName)
                            {
                                array_push($keys, '%%SubDivisionName%%');
                                array_push($values, $SubDivisionName);
                            }
                            else{
                                array_push($keys, '%%SubDivisionName%%');
                                array_push($values, '-');
                            }
                            $sdMapNo = $data->sdMapNo;
                            if($sdMapNo)
                            {
                                array_push($keys, '%%sdMapNo%%');
                                array_push($values, $sdMapNo);
                            }
                            else{
                                array_push($keys, '%%sdMapNo%%');
                                array_push($values, '-');
                            }
                            $dSection = $data->dSection;
                            if($dSection)
                            {
                                array_push($keys, '%%dSection%%');
                                array_push($values, $dSection);
                            }
                            else{
                                array_push($keys, '%%dSection%%');
                                array_push($values, '-');
                            }
                            $Township = $data->Township;
                            if($Township)
                            {
                                array_push($keys, '%%Township%%');
                                array_push($values, $Township);
                            }
                            else{
                                array_push($keys, '%%Township%%');
                                array_push($values, '-');
                            }

                            $APN = $data->APN;
                            if($APN)
                            {
                                array_push($keys, '%%APN%%');
                                array_push($values, $APN);
                            }
                            else{
                                array_push($keys, '%%APN%%');
                                array_push($values, '-');
                            }
                            $Lot = $data->Lot;
                            if($Lot)
                            {
                                array_push($keys, '%%Lot%%');
                                array_push($values, $Lot);
                            }
                            else{
                                array_push($keys, '%%Lot%%');
                                array_push($values, '-');
                            }

                            $Block = $data->Block;
                            if($Block)
                            {
                                array_push($keys, '%%Block%%');
                                array_push($values, $Block);
                            }
                            else{
                                array_push($keys, '%%Block%%');
                                array_push($values, '-');
                            }
                        }
                    }
                    else
                    {
                        $TaxArray = array('Exempt','APN','Lot','Block','SubDivisionName','MaritalStatusName','sdMapNo','dSection','Township');
                        foreach($TaxArray as $col)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, '-');
                        }
                    }
                    $output = str_replace($keys, $values, $output);
                    $doc->load($output);
        //Deed Starts
                    preg_match_all('/<div class=\"torderdeeds\">(.*?)<\/div>/s',$output,$torderdeeds);
                    $torderdeeds_table = '';
                    $torderdeeds_array = array();
                    $torderdeedsparties_array = array();
                    $torder_deeds = $this->Order_reports_model->get_torderdeeds($OrderUID);
                    $torderdeeds_array = $torder_deeds;
                    $torderdeeds_array_count = count($torderdeeds_array);
                    for ($i=0; $i < $torderdeeds_array_count; $i++) 
                    { 
                        $torderdeeds_array[$i]->deed_increment = $i+1;
                        $keys = array();
                        $values = array();
                        foreach ($torderdeeds_array[$i] as $key => $value)
                        {
                     //subdocumenttype
                            $DocumentTypeName = $torderdeeds_array[$i]->DocumentTypeName;
                            if($DocumentTypeName)
                            {
                                if($DocumentTypeName == 'Others')
                                {
                                    $DeedType = $torderdeeds_array[$i]->DeedType;
                                    if($DeedType)
                                    {
                                        array_push($keys, '%%DeedDocumentTypeName%%');
                                        array_push($values, $DeedType);
                                    }
                                    else{
                                        array_push($keys, '%%DeedDocumentTypeName%%');
                                        array_push($values, '-');
                                    }
                                }
                                else if($DocumentTypeName !== 'Others')
                                {
                                    array_push($keys, '%%DeedDocumentTypeName%%');
                                    array_push($values, $DocumentTypeName);
                                }
                                else
                                {
                                    array_push($keys, '%%DeedDocumentTypeName%%');
                                    array_push($values, '-');
                                }

                            }
                            else{
                                array_push($keys, '%%DeedDocumentTypeName%%');
                                array_push($values, '-');
                            }
                    //subdocumenttype

                            $Grantee = $torderdeeds_array[$i]->Grantee;
                            if($Grantee)
                            {
                                array_push($keys,'%%DeedGrantee%%');
                                array_push($values, $Grantee);
                            }
                            else{
                                array_push($keys,'%%DeedGrantee%%');
                                array_push($values, '-');
                            }
                            $Grantor = $torderdeeds_array[$i]->Grantor;
                            if($Grantor)
                            {
                                array_push($keys,'%%DeedGrantor%%');
                                array_push($values, $Grantor);
                            }
                            else{
                                array_push($keys,'%%DeedGrantor%%');
                                array_push($values, '-');
                            }
                //Deed Date Format Change
                            $DDated = $torderdeeds_array[$i]->DeedDated;
                            if($DDated == '0000-00-00')
                            {
                                array_push($keys, '%%DeedDate%%');
                                array_push($values, '-');
                            }
                            else{
                                $DeedDated =  date('m/d/Y', strtotime($DDated));
                                array_push($keys, '%%DeedDate%%');
                                array_push($values, $DeedDated);
                            }
                //Deed Date Format Change
                //Recorded date Format Change
                            $RDated = $torderdeeds_array[$i]->DeedRecorded;
                            if($RDated == '0000-00-00')
                            {
                                array_push($keys, '%%RecordedDate%%');
                                array_push($values, '-');
                            }
                            else{
                                $RecordedDated =  date('m/d/Y', strtotime($RDated));
                                array_push($keys, '%%RecordedDate%%');
                                array_push($values, $RecordedDated);
                            }
                            $Deed_DBVTypeUID_1 = $torderdeeds_array[$i]->Deed_DBVTypeUID_1;
                            $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_1);
                            $Deed_DBVTypeValue_1 = $torderdeeds_array[$i]->Deed_DBVTypeValue_1;
                            $Deed_DBVTypeUID_2 = $torderdeeds_array[$i]->Deed_DBVTypeUID_2;
                            $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_2);
                            $Deed_DBVTypeValue_2 = $torderdeeds_array[$i]->Deed_DBVTypeValue_2;
                            if($DBVTypeName_1)
                            {
                                array_push($keys, '%%DBVTypeName_1%%');
                                array_push($values, $DBVTypeName_1);
                                if($Deed_DBVTypeValue_1)
                                {
                                    array_push($keys, '%%Deed_DBVTypeValue_1%%');
                                    array_push($values, $Deed_DBVTypeValue_1);
                                }
                                else{
                                    array_push($keys, '%%Deed_DBVTypeValue_1%%');
                                    array_push($values, '-');
                                }
                            }
                            else{
                                array_push($keys, '%%DBVTypeName_1%%');
                                array_push($values, 'Book/Page');
                                array_push($keys, '%%Deed_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }

                            if($DBVTypeName_2)
                            {
                                array_push($keys, '%%DBVTypeName_2%%');
                                array_push($values, $DBVTypeName_2);
                                if($Deed_DBVTypeValue_2)
                                {
                                    array_push($keys, '%%Deed_DBVTypeValue_2%%');
                                    array_push($values, $Deed_DBVTypeValue_2);
                                }
                                else{
                                    array_push($keys, '%%Deed_DBVTypeValue_2%%');
                                    array_push($values, '-');
                                }
                            }
                            else{

                                array_push($keys, '%%DBVTypeName_2%%');
                                array_push($values, 'Instrument');
                                array_push($keys, '%%Deed_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                //Deed Document
                            $DocumentNo = $torderdeeds_array[$i]->DocumentNo;
                            if($DocumentNo)
                            {   
                                array_push($keys, '%%DeedDocumentNo%%');
                                array_push($values, $DocumentNo);
                            }
                            else{

                                array_push($keys, '%%DeedDocumentNo%%');
                                array_push($values, '-');
                            }
                //Deed Document
                //Certificate Number
                            $CertificateNo = $torderdeeds_array[$i]->CertificateNo;
                            if($CertificateNo)
                            {
                                array_push($keys, '%%DeedCertificateNo%%');
                                array_push($values, $CertificateNo);
                            }
                            else{
                                array_push($keys, '%%DeedCertificateNo%%');
                                array_push($values, '-');
                            }
                //Certificate Number
                //Instrument Number
                            $InstrumentNo = $torderdeeds_array[$i]->InstrumentNo;
                            if($InstrumentNo)
                            {
                                array_push($keys, '%%DeedInstrumentNo%%');
                                array_push($values, $InstrumentNo);
                            }
                            else{
                                array_push($keys, '%%DeedInstrumentNo%%');
                                array_push($values, '-');
                            }
                //Instrument Number
                //ConsiderationAmount
                            $ConsiderationAmount = $torderdeeds_array[$i]->ConsiderationAmount;
                            if($ConsiderationAmount)
                            {
                                $ConsiderAmount = '$'.number_format($ConsiderationAmount,2);
                                array_push($keys, '%%ConsiderAmount%%');
                                array_push($values, $ConsiderAmount);
                            }
                            else{
                                array_push($keys, '%%ConsiderAmount%%');
                                array_push($values, '-');
                            }
                //ConsiderationAmount
                //Deed Comments
                            $DeedComments = $torderdeeds_array[$i]->DeedComments;
                            if($DeedComments)
                            {
                                array_push($keys, '%%DeedComments%%');
                                array_push($values, $DeedComments);
                            }
                            else{
                                array_push($keys, '%%DeedComments%%');
                                array_push($values, '-');
                            }
                //Deed Comments
                //Main Loop
                            array_push($keys, '%%'.$key.'%%');
                            array_push($values, $value);
                //Main Loop
                //Latest Deed Grantee
                            $GranteeGrantor =  $this->Order_reports_model->getGranteeGrantor($OrderUID);
                            if($GranteeGrantor)
                            {
                                foreach ($GranteeGrantor as $col => $value) 
                                {
                                    $Grantee = $value->Grantee;
                                    if($Grantee)
                                    {

                                        array_push($keys, '%%LatestDeedGrantee%%');
                                        array_push($values, $Grantee);
                                    }
                                    else{

                                        array_push($keys, '%%LatestDeedGrantee%%');
                                        array_push($values, '-');
                                    }
                                } 
                            }
                            else{
                                array_push($keys, '%%LatestDeedGrantee%%');
                                array_push($values, '-');
                            }
                //Latest Deed Grantee
                        }
                        $torderdeeds_table .= str_replace($keys, $values, $torderdeeds[0][0]);
                    }
                    foreach ( $doc->find(".torderdeeds") as $node ) 
                    {
                        $node->innertext = $torderdeeds_table;
                    }
        //Deed Ends 
        //Mortgages Starts
                    preg_match_all('/<div class=\"tordermortgages\">(.*?)<\/div>/s',$output,$tordermortgages);
                    $tordermortgages_table = '';
                    $tordermortgages_array = array();
                    $tordermortgagesparties_array = array();
                    $tordermortgages_array = $this->Order_reports_model->get_tordermortgageparties($OrderUID);

                    $tordermortgages_array_count = count($tordermortgages_array);

                    for ($i=0; $i < $tordermortgages_array_count; $i++) 
                    { 
                        $tordermortgages_array[$i]->mortgage_increment = $i+1;
                        $keys = array();
                        $values = array();
                        foreach ($tordermortgages_array[$i] as $key => $value) 
                        {

                    $DocumentTypeName = $tordermortgages_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $MortgageType = $tordermortgages_array[$i]->MortgageType;
                            if($MortgageType)
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, $MortgageType);
                            }
                            else
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%MortgageDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //Mortgage Date Format Change
                            $MortgageDated = $tordermortgages_array[$i]->MortgageDated;
                            if($MortgageDated == '0000-00-00')
                            {
                                array_push($keys, '%%MortgageDate%%');
                                array_push($values, '-');
                            }
                            else{
                                $MortgageDate =  date('m/d/Y', strtotime($MortgageDated));
                                array_push($keys, '%%MortgageDate%%');
                                array_push($values, $MortgageDate);
                            }
                //Mortgage Date Format Change
                //Mortgage Date Format Change
                            $MortgageRecorded = $tordermortgages_array[$i]->MortgageRecorded;
                            if($MortgageRecorded == '0000-00-00')
                            {
                                array_push($keys, '%%MortgageRecordedDate%%');
                                array_push($values, '&nbsp;&nbsp;&nbsp;&nbsp;-');
                            }
                            else{
                                $MortgageRecordedDate =  date('m/d/Y', strtotime($MortgageRecorded));
                                array_push($keys, '%%MortgageRecordedDate%%');
                                array_push($values, $MortgageRecordedDate);
                            }
                //Mortgage Date Format Change
                //Mortgage Maturity Date Format
                            $MortgageMaturityDate = $tordermortgages_array[$i]->MortgageMaturityDate;
                            if($MortgageMaturityDate == '0000-00-00')
                            {
                                array_push($keys, '%%MaturityDate%%');
                                array_push($values, '-');
                            }
                            else{
                                $MaturityDate =  date('m/d/Y', strtotime($MortgageMaturityDate));
                                array_push($keys, '%%MaturityDate%%');
                                array_push($values, $MaturityDate);
                            }
                //Mortgage Maturity Date Format
                //LoanAmount
                            $MortgageAmount = $tordermortgages_array[$i]->MortgageAmount;
                            if($MortgageAmount)
                            {
                                $MortAmt = '$'.number_format($MortgageAmount,2);
                                array_push($keys, '%%MortAmt%%');
                                array_push($values, $MortAmt);
                            }
                            else{
                                array_push($keys, '%%MortAmt%%');
                                array_push($values, '-');
                            }
                //LoanAmount
                //Mortgagee
                            $Mortgagee = $tordermortgages_array[$i]->Mortgagee;
                            if($Mortgagee)
                            {
                                array_push($keys, '%%Mortgagee%%');
                                array_push($values, $Mortgagee);
                            }
                            else{
                                array_push($keys, '%%Mortgagee%%');
                                array_push($values, '-');
                            }
                //Mortgagee
                //Trustee
                            $Trustee1 = $tordermortgages_array[$i]->Trustee1;
                // $Trustee2 = $tordermortgages_array[$i]->Trustee2;
                // if($Trustee1 != '' && $Trustee2 != '')
                // {
                //     $Trustee = $Trustee1.','.$Trustee2;
                //     array_push($keys, '%%Trustee%%');
                //     array_push($values, $Trustee);
                // }
                            if($Trustee1 != '')
                            {
                                $Trustee = $Trustee1;
                                array_push($keys, '%%Trustee%%');
                                array_push($values, $Trustee);
                            }
                // if($Trustee2 != ''){

                //     $Trustee = $Trustee2;
                //     array_push($keys, '%%Trustee%%');
                //     array_push($values, $Trustee);

                // }
                            else{

                                $Trustee = '-';
                                array_push($keys, '%%Trustee%%');
                                array_push($values, $Trustee);
                            }
                //Trustee
                            // if($tordermortgages_array[$i]->LienTypeName =='Closed Ended')
                            // {
                            //     $MTG = array('MTG'=>'Closed');
                            //     foreach($MTG as $col => $val)
                            //     {
                            //         array_push($keys, '%%'.$col.'%%');
                            //         array_push($values, $val);
                            //     }
                            // }
                            // if($tordermortgages_array[$i]->LienTypeName =='Open Ended')
                            // {
                            //     $MTG = array('MTG'=>'Open');
                            //     foreach($MTG as $col => $val)
                            //     {
                            //         array_push($keys, '%%'.$col.'%%');
                            //         array_push($values, $val);
                            //     }
                            // }
                            // else
                            // {
                            //     $MTG = array('MTG'=>'No');
                            //     foreach($MTG as $col => $val)
                            //     {
                            //         array_push($keys, '%%'.$col.'%%');
                            //         array_push($values, $val);
                            //     }
                            // }
                                //Closed/Open Ended
                                $OpenEnded = $tordermortgages_array[$i]->IsOpenEnded;
                                if($OpenEnded == '1')
                                {

                                        array_push($keys, '%%OpenEnded%%');
                                        array_push($values, 'Yes');
                                    
                                }
                                else
                                {

                                        array_push($keys, '%%OpenEnded%%');
                                        array_push($values,'-');
                                    
                                }
                //Book/Page
                            $Mortgage_DBVTypeUID_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_1;
                            $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_1);
                            $Mortgage_DBVTypeValue_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_1;
                            $Mortgage_DBVTypeUID_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_2;
                            $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_2);
                            $Mortgage_DBVTypeValue_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_2;
                            if($DBVTypeName_1)
                            {
                                array_push($keys, '%%DBVTypeName_1%%');
                                array_push($values, $DBVTypeName_1);
                                if($Mortgage_DBVTypeValue_1)
                                {
                                    array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                                    array_push($values, $Mortgage_DBVTypeValue_1);
                                }
                                else{

                                    array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                                    array_push($values, '-');
                                }
                            }
                            else{
                                array_push($keys, '%%DBVTypeName_1%%');
                                array_push($values, 'Book/Page');
                                array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }

                            if($DBVTypeName_2)
                            {
                                array_push($keys, '%%DBVTypeName_2%%');
                                array_push($values, $DBVTypeName_2);
                                if($Mortgage_DBVTypeValue_2)
                                {
                                    array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                                    array_push($values, $Mortgage_DBVTypeValue_2);
                                }
                                else{

                                    array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                                    array_push($values, '-');
                                }
                            }
                            else{

                                array_push($keys, '%%DBVTypeName_2%%');
                                array_push($values, 'Instrument');
                                array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                
                //Instrument
                            if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                            {
                                $AppendInstrument = $Mortgage_DBVTypeValue_1.','.$Mortgage_DBVTypeValue_2;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);

                            }
                            if($DBVTypeName_1 == 'Instrument'){
                                $AppendInstrument = $Mortgage_DBVTypeValue_1;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);

                            }
                            if($DBVTypeName_2 == 'Instrument'){
                                $AppendInstrument = $Mortgage_DBVTypeValue_2;
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);
                            }
                            else{
                                $AppendInstrument = ' ';
                                array_push($keys, '%%InstrumentNo_1%%');
                                array_push($values, $AppendInstrument);
                            }
                //Instrument
                //Document
                            if($DBVTypeName_1 == 'Document' && $DBVTypeName_2 == 'Document')
                            {
                                $AppendDocument = $Mortgage_DBVTypeValue_1.','.$Mortgage_DBVTypeValue_2;
                                array_push($keys, '%%Document%%');
                                array_push($values, $AppendDocument);

                            }
                            if($DBVTypeName_1 == 'Document'){
                                $AppendDocument = $Mortgage_DBVTypeValue_1;
                                array_push($keys, '%%Document%%');
                                array_push($values, $AppendDocument);

                            }
                            if($DBVTypeName_2 == 'Document'){
                                $AppendDocument = $Mortgage_DBVTypeValue_2;
                                array_push($keys, '%%Document%%');
                                array_push($values, $AppendDocument);
                            }
                            else{
                                array_push($keys, '%%Document%%');
                                array_push($values, '-');
                            }
                //Document
                //Mortgage Comments
                            $MortgageComments = $tordermortgages_array[$i]->MortgageComments;
                            if($MortgageComments)
                            {
                                array_push($keys, '%%MortgageComments%%');
                                array_push($values, $MortgageComments);
                            }
                            else{
                                array_push($keys, '%%MortgageComments%%');
                                array_push($values, '-');
                            }
                //Mortgage Comments
                //Main Loop
                            array_push($keys, '%%'.$key.'%%');
                            array_push($values, $value);
                //Main Loop

                        }

                        $tordermortgages_table.= str_replace($keys, $values, $tordermortgages[0][0]);
                    }

                    foreach ( $doc->find(".tordermortgages") as $node ) 
                    {
                        $node->innertext = $tordermortgages_table;
                    }
        //Mortgages Ends
        //Property Starts
                    preg_match_all('/<div class=\"torderproperty\">(.*?)<\/div>/s',$output,$torderproperty);
                    $property_information = $this->Order_reports_model->getPropertyInformation($OrderUID);
                    foreach ($property_information as $data) 
                    {
                        foreach($data as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    $torderproperty_table .= str_replace($keys, $values, $torderproperty[0][0]);
                    foreach ($doc->find(".torderproperty") as $node ) 
                    {
                        $node->innertext = $torderproperty_table;
                    }
        //Property Ends
        //Tax Starts
                    preg_match_all('/<div class=\"tordertaxcerts\">(.*?)<\/div>/s',$output,$tordertaxcerts);
                    $tordertaxcerts_table = '';
                    $tordertaxcerts_array = array();
                    $tordertaxcerts_array = $this->Order_reports_model->get_tordertaxcerts($OrderUID);
                    $tordertaxcerts_array_count = count($tordertaxcerts_array);
                    for ($i=0; $i < $tordertaxcerts_array_count; $i++) 
                    { 

                        $tordertaxcerts_array[$i]->tax_increment = $i+1;
                        $keys = array();
                        $values = array();
                        foreach ($tordertaxcerts_array[$i] as $key => $value) 
                        {
                            //Total Value
                            $TotalValue = $tordertaxcerts_array[$i]->TotalValue;
                            if($TotalValue)
                            {
                                $TotalValue = '$'.$TotalValue;
                                array_push($keys, '%%TotalValue%%');
                                array_push($values, $TotalValue);
                            }
                            else{
                                array_push($keys, '%%TotalValue%%');
                                array_push($values, '-');
                            }
                            //Total Value
                            //Agriculture
                            $Agriculture = $tordertaxcerts_array[$i]->Agriculture;
                            if($Agriculture)
                            {
                                $Agriculture = '$'.$Agriculture;
                                array_push($keys, '%%Agriculture%%');
                                array_push($values, $Agriculture);
                            }
                            else{
                                array_push($keys, '%%Agriculture%%');
                                array_push($values, '-');
                            }
                            //Agriculture
                            //Acreage
                            $Acreage = $tordertaxcerts_array[$i]->Acreage;
                            if($Acreage)
                            {
                                array_push($keys, '%%Acreage%%');
                                array_push($values, $Acreage);
                            }
                            else{
                                array_push($keys, '%%Acreage%%');
                                array_push($values, '-');
                            }
                            //Acreage
                            //Amount Deliquent
                            $AmountDelinquent = $tordertaxcerts_array[$i]->AmountDelinquent;
                            $AmtDelinquent = '$'.number_format($AmountDelinquent,2);
                            if($AmtDelinquent)
                            {
                                array_push($keys, '%%AmountDelinquent%%');
                                array_push($values, $AmtDelinquent);
                            }
                            else{
                                array_push($keys, '%%AmountDelinquent%%');
                                array_push($values, '-');
                            }
                            //Amount Deliquent
                            //Tax Date Format Change
                            $GoodThroughDate = $tordertaxcerts_array[$i]->GoodThroughDate;
                            if($GoodThroughDate == '0000-00-00')
                            {
                                array_push($keys, '%%ThroughDate%%');
                                array_push($values, '-');
                            }
                            else{
                                $ThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                                array_push($keys, '%%ThroughDate%%');
                                array_push($values, $ThroughDate);
                            }
                            //Tax Date Format Change
                            //Tax Date Paid
                            $DatePaid = $tordertaxcerts_array[$i]->DatePaid;
                            if($DatePaid == '0000-00-00')
                            {
                                array_push($keys, '%%PaidDate%%');
                                array_push($values, '-');
                            }
                            else{
                                $PaidDate =  date('m/d/Y', strtotime($DatePaid));
                                array_push($keys, '%%PaidDate%%');
                                array_push($values, $PaidDate);

                            }
                            //Tax Date Paid
                            //Tax Comments
                                $TaxComments = $tordertaxcerts_array[$i]->TaxComments;
                                if($TaxComments)
                                {
                                    array_push($keys, '%%TaxComments%%');
                                    array_push($values, $TaxComments);
                                    array_push($keys, '%%taxalignment%%');
                                    array_push($values, 'text-left');
                                }
                                else{
                                    array_push($keys, '%%TaxComments%%');
                                    array_push($values, '-');
                                    array_push($keys, '%%taxalignment%%');
                                    array_push($values, 'text-center');
                                }
                            //Tax Comments
                            if($tordertaxcerts_array[$i]->Certified =='0')
                            {
                                $CertifiedStatus = array('CertifiedStatus'=>'unchecked.png');
                                foreach($CertifiedStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            else
                            {
                                $CertifiedStatus = array('CertifiedStatus'=>'checked.png');
                                foreach($CertifiedStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            $tax_information = $this->Order_reports_model->get_tax($OrderUID);
                            if($tax_information)
                            {
                    //Property tax in ISGN Report
                                foreach ($tax_information as $data) 
                                {
                        //DeliquentTax
                                    if($data->TaxStatusName)
                                    {
                                        if($data->TaxStatusName == 'Delinquent')
                                        {
                                            $DeliquentTax = array('DeliquentTax'=>'Yes');
                                            foreach($DeliquentTax as $col => $val)
                                            {
                                                array_push($keys, '%%'.$col.'%%');
                                                array_push($values, $val);
                                            }
                                        }
                                        else
                                        {
                                            $DeliquentTax = array('DeliquentTax'=>'No');
                                            foreach($DeliquentTax as $col => $val)
                                            {
                                                array_push($keys, '%%'.$col.'%%');
                                                array_push($values, $val);
                                            }
                                        }
                                    }
                                    else{
                                        array_push($keys, '%%DeliquentTax%%');
                                        array_push($values, '-');
                                    }

                                    $TaxYear = $data->TaxYear;
                                    if($TaxYear)
                                    {
                                        array_push($keys, '%%TaxYear%%');
                                        array_push($values, $TaxYear);
                                    }
                                    else
                                    {
                                        array_push($keys, '%%TaxYear%%');
                                        array_push($values, '-');
                                    }
                                    $GrossAmount = $data->GrossAmount;
                                    if($GrossAmount)
                                    {
                                        $TaxYear = $data->TaxYear;
                                        if($TaxYear == date("Y"))
                                        {
                                            $PropertyTax = '$'.$GrossAmount;
                                            array_push($keys, '%%PropertyTax%%');
                                            array_push($values, $PropertyTax);
                                        }
                                        else
                                        {
                                            $PropertyTax = "$0.00";
                                            array_push($keys, '%%PropertyTax%%');
                                            array_push($values, $PropertyTax);
                                        }   
                                    }
                                    else
                                    {
                                        array_push($keys, '%%PropertyTax%%');
                                        array_push($values, '-');
                                    }
                                }
                            }
                            else
                            {
                                $TaxArray = array('TaxYear','PropertyTax','DeliquentTax');
                                foreach($TaxArray as $col)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, '-');
                                }
                            }
                            array_push($keys, '%%'.$key.'%%');
                            array_push($values, $value);
            }//foreach ends
            $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
            $TaxCertSNo = $this->Order_reports_model->getExemptionname($OrderUID,$TaxCert);
            $Tax = explode(",", $TaxCertSNo);
            if(in_array('Homestead', $Tax))
            {

                $ExmpStatus = array('ExmpStatus'=>'checked.png');
                foreach($ExmpStatus as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            else
            {
                $ExmpStatus = array('ExmpStatus'=>'unchecked.png');
                foreach($ExmpStatus as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            if(in_array('Agricultural', $Tax))
            {
                $ExmpStatus = array('AgriExmpStatus'=>'checked.png');
                foreach($ExmpStatus as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            else
            {
                $ExmpStatus = array('AgriExmpStatus'=>'unchecked.png');
                foreach($ExmpStatus as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            if(in_array('Rural', $Tax))
            {

                $ExmpStatus = array('RuralExmpStatus'=>'checked.png');
                foreach($ExmpStatus as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            else
            {
                $ExmpStatus = array('RuralExmpStatus'=>'unchecked.png');
                foreach($ExmpStatus as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            if(in_array('Urban', $Tax))
            {
                $ExmpStatus = array('UrbanExmpStatus'=>'checked.png');
                foreach($ExmpStatus as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            else
            {
                $ExmpStatus = array('UrbanExmpStatus'=>'unchecked.png');
                foreach($ExmpStatus as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }

                  $getlatesttaxinstallment = $this->Order_reports_model->getlatesttaxinstallment($OrderUID,$TaxCert);
                        if($getlatesttaxinstallment)
                        {
                                foreach ($getlatesttaxinstallment as $key => $value) 
                                {
                                        $TaxYears = $value->TaxYear;
                                        if($TaxYears)
                                        {
                                            array_push($keys, '%%TaxYears%%');
                                            array_push($values, $TaxYears);
                                        }
                                        else{
                                            array_push($keys, '%%TaxYears%%');
                                            array_push($values, '-');
                                        }
                                        //Tax Date Format Change
                                        $NextTaxDueDate = $value->DatePaid;
                                        if($NextTaxDueDate=='0000-00-00' || $NextTaxDueDate == '')
                                        {
                                            array_push($keys, '%%NextTaxDate%%');
                                            array_push($values, '-');
                                        }
                                        else{
                                            $NextTaxDate =  date('m/d/Y', strtotime($NextTaxDueDate));
                                            array_push($keys, '%%NextTaxDate%%');
                                            array_push($values, $NextTaxDate);
                                        }
                                        //Gross Amount
                                        $GrossAmount = $value->LatestGrossAmount;
                                        $GrossAmt = '$'.number_format($GrossAmount,2);
                                        if($GrossAmt)
                                        {
                                            array_push($keys, '%%LatestGrossAmount%%');
                                            array_push($values, $GrossAmt);
                                        }
                                        else{
                                            array_push($keys, '%%LatestGrossAmount%%');
                                            array_push($values, '-');
                                        }
                                        //Gross Amount
                                        //Amount Paid
                                        $AmountPaid = $value->AmountPaid;
                                        $AmtPaid = '$'.number_format($AmountPaid,2);
                                        if($AmtPaid)
                                        {
                                            array_push($keys, '%%AmtPaid%%');
                                            array_push($values, $AmtPaid);
                                        }
                                        else{
                                            array_push($keys, '%%AmtPaid%%');
                                            array_push($values, '-');
                                        }
                                        //Amount Paid
                                        //Tax Status Name
                                        $TaxStatusName = $value->TaxStatusName;
                                        if($TaxStatusName)
                                        {
                                            array_push($keys, '%%StatusName%%');
                                            array_push($values, $TaxStatusName);
                                        }
                                        else{
                                            array_push($keys, '%%StatusName%%');
                                            array_push($values, '-');
                                        }
                                        //Tax Status Name
                                        //Tax Date Format Change
                                }
                        }
                        else{

                            $data = array('TaxYears','NextTaxDate','LatestGrossAmount','AmtPaid','StatusName');
                            foreach ($data as $value) {
                                array_push($keys, '%%'.$value.'%%');
                                array_push($values, '-');
                            }
                        }

            $output = str_replace($keys, $values, $output);
            $tordertaxcerts_table .= str_replace($keys, $values, $tordertaxcerts[0][0]);
        }
        foreach ( $doc->find(".tordertaxcerts") as $node ) 
        {
            $node->innertext = $tordertaxcerts_table;
        }
        //Tax Ends
        //Liens Starts
        preg_match_all('/<div class=\"torderliens\">(.*?)<\/div>/s',$output,$torderliens);
        $torderliens_table = '';
        $torderliens_array = array();
        $torderliens_array = $this->Order_reports_model->get_torderliens($OrderUID);
        $torderliens_array_count = count($torderliens_array);
        for ($i=0; $i < $torderliens_array_count; $i++) 
        { 

            $torderliens_array[$i]->lien_increment = $i+1;

            $keys = array();
            $values = array();
            foreach ($torderliens_array[$i] as $key => $value) 
            {
                //DocumentTypeName
                  $DocumentTypeName = $torderliens_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $LeinType = $torderliens_array[$i]->LeinType;
                            if($LeinType)
                            {
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, $LeinType);
                            }
                            else{
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%LeinDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //DocumentTypeName
                //Lein Date Format Change
                $LDated = $torderliens_array[$i]->LeinDated;
                if($LDated == '0000-00-00')
                {
                    array_push($keys, '%%LeinDate%%');
                    array_push($values, '-');
                }
                else{
                    $Dated =  date('m/d/Y', strtotime($JDated));
                    array_push($keys, '%%LeinDate%%');
                    array_push($values, $Dated);
                }
                //Lein Date Format Change
                //Lein Recorded date Format Change
                $RDated = $torderliens_array[$i]->LeinRecorded;
                if($RDated == '0000-00-00')
                {
                    array_push($keys, '%%LeinRecord%%');
                    array_push($values, '-');
                }
                else{
                    $RecordedDated =  date('m/d/Y', strtotime($RDated));
                    array_push($keys, '%%LeinRecord%%');
                    array_push($values, $RecordedDated);
                }
                //Lein date Format Change
                //Lein Amount
                $LeinAmount = $torderliens_array[$i]->LeinAmount;
                if($LeinAmount)
                {
                    $MortAmt = '$'.number_format($LeinAmount,2);
                    array_push($keys, '%%LeinAmt%%');
                    array_push($values, $MortAmt);
                }
                else{
                    array_push($keys, '%%LeinAmt%%');
                    array_push($values, '-');
                }
                //Lein Amount
                //Lein Comments
                $LeinComments = $torderliens_array[$i]->LeinComments;
                if($LeinComments)
                {
                    array_push($keys, '%%LeinComments%%');
                    array_push($values, $LeinComments);
                }
                else{
                    array_push($keys, '%%LeinComments%%');
                    array_push($values, '-');
                }
                //Lein Comments
                if($torderliens_array[$i]->LienTypeName =='Closed Ended')
                {
                    $MTG = array('MTG'=>'Closed');
                    foreach($MTG as $col => $val)
                    {
                        array_push($keys, '%%'.$col.'%%');
                        array_push($values, $val);
                    }
                }
                if($torderliens_array[$i]->LienTypeName =='Open Ended')
                {
                    $MTG = array('MTG'=>'Open');
                    foreach($MTG as $col => $val)
                    {
                        array_push($keys, '%%'.$col.'%%');
                        array_push($values, $val);
                    }
                }
                else
                {
                    $MTG = array('MTG'=>'No');
                    foreach($MTG as $col => $val)
                    {
                        array_push($keys, '%%'.$col.'%%');
                        array_push($values, $val);
                    }
                }

                $Lien_DBVTypeUID_1 = $torderliens_array[$i]->Lien_DBVTypeUID_1;
                $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_1);
                $Lien_DBVTypeValue_1 = $torderliens_array[$i]->Lien_DBVTypeValue_1;
                $Lien_DBVTypeUID_2 = $torderliens_array[$i]->Lien_DBVTypeUID_2;
                $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_2);
                $Lien_DBVTypeValue_2 = $torderliens_array[$i]->Lien_DBVTypeValue_2;
                if($DBVTypeName_1)
                {
                    array_push($keys, '%%DBVTypeName_1%%');
                    array_push($values, $DBVTypeName_1);
                    if($Lien_DBVTypeValue_1)
                    {
                        array_push($keys, '%%Lien_DBVTypeValue_1%%');
                        array_push($values, $Lien_DBVTypeValue_1);
                    }
                    else{
                        array_push($keys, '%%Lien_DBVTypeValue_1%%');
                        array_push($values, '-');
                    }
                }

                if($DBVTypeName_2)
                {
                    array_push($keys, '%%DBVTypeName_2%%');
                    array_push($values, $DBVTypeName_2);
                    if($Lien_DBVTypeValue_2)
                    {
                        array_push($keys, '%%Lien_DBVTypeValue_2%%');
                        array_push($values, $Lien_DBVTypeValue_2);
                    }
                    else{
                        array_push($keys, '%%Lien_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                }
                else{

                    array_push($keys, '%%DBVTypeName_2%%');
                    array_push($values, 'Instrument');
                    array_push($keys, '%%Lien_DBVTypeValue_2%%');
                    array_push($values, '-');
                }
                //CaseNumber
                if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                {
                    $AppendCasenumber = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                    array_push($keys, '%%CaseNumber%%');
                    array_push($values, $AppendCasenumber);

                }
                if($DBVTypeName_1 == 'Case number'){
                    $AppendCasenumber = $Lien_DBVTypeValue_1;
                    array_push($keys, '%%CaseNumber%%');
                    array_push($values, $AppendCasenumber);

                }
                if($DBVTypeName_2 == 'Case number'){
                    $AppendCasenumber = $Lien_DBVTypeValue_2;
                    array_push($keys, '%%CaseNumber%%');
                    array_push($values, $AppendCasenumber);
                }
                else{
                    $AppendCasenumber = '-';
                    array_push($keys, '%%CaseNumber%%');
                    array_push($values, $AppendCasenumber);
                }
                //CaseNumber
                //Instrument
                if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                {
                    $AppendInstrument = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                    array_push($keys, '%%InstrumentNo_1%%');
                    array_push($values, $AppendInstrument);

                }
                if($DBVTypeName_1 == 'Instrument'){
                    $AppendInstrument = $Lien_DBVTypeValue_1;
                    array_push($keys, '%%InstrumentNo_1%%');
                    array_push($values, $AppendInstrument);

                }
                if($DBVTypeName_2 == 'Instrument'){
                    $AppendInstrument = $Lien_DBVTypeValue_2;
                    array_push($keys, '%%InstrumentNo_1%%');
                    array_push($values, $AppendInstrument);
                }
                else{

                    array_push($keys, '%%InstrumentNo_1%%');
                    array_push($values, '-');
                }
                //Instrument

                $Holder = $torderliens_array[$i]->Holder;
                if($Holder)
                {
                    array_push($keys, '%%LeinHolder%%');
                    array_push($values, $Holder);
                }
                else{
                    array_push($keys, '%%LeinHolder%%');
                    array_push($values, '-');
                }

                array_push($keys, '%%'.$key.'%%');
                array_push($values, $value);
            }
            $output = str_replace($keys, $values, $output);

            $torderliens_table .= str_replace($keys, $values, $torderliens[0][0]);

        }
        foreach ( $doc->find(".torderliens") as $node ) 
        {
            $node->innertext = $torderliens_table;
        }
        //Leins Ends
        //Judgement Starts
        preg_match_all('/<div class=\"torderjudgments\">(.*?)<\/div>/s',$output,$torderjudgments);
        $torderjudgments_table = '';
        $torderjudgments_array = array();
        $torderjudgments_array = $this->Order_reports_model->get_torderjudgements($OrderUID);
        print_r( $torderjudgments_array);
        $torderjudgments_array_count = count($torderjudgments_array);
        for ($i=0; $i < $torderjudgments_array_count; $i++)
        { 
            $torderjudgments_array[$i]->judgement_increment = $i+1;
            $keys = array();
            $values = array();
            foreach ($torderjudgments_array[$i] as $key => $value)
            {
                //Plaintiff
                $Plaintiff = $torderjudgments_array[$i]->Plaintiff;
                if($Plaintiff)
                {
                    array_push($keys, '%%Plaintiff%%');
                    array_push($values, $Plaintiff);
                }
                else{
                    array_push($keys, '%%Plaintiff%%');
                    array_push($values, '-');
                }
                //Plaintiff
                //Defendant
                $Defendent = $torderjudgments_array[$i]->Defendent;
                if($Defendent)
                {
                    array_push($keys, '%%Defendent%%');
                    array_push($values, $Defendent);
                }
                else{
                    array_push($keys, '%%Defendent%%');
                    array_push($values, '-');
                }
                //Defendant
                //DocumentTypeName
                    $DocumentTypeName = $torderjudgments_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $JudgementType = $torderjudgments_array[$i]->JudgementType;
                            if($JudgementType)
                            {
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, $JudgementType);
                            }
                            else{
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%JudgementDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //DocumentTypeName
                //Judgement Amount
                $JudgementAmount = $torderjudgments_array[$i]->JudgementAmount;
                $JudAmt = '$'.number_format($JudgementAmount,2);
                array_push($keys, '%%JudgementAmount%%');
                array_push($values, $JudAmt);
                //Judgement Amount
                //Judgement Date Format Change
                $JDated = $torderjudgments_array[$i]->JudgementDated;
                if($JDated == '0000-00-00')
                { 
                    array_push($keys, '%%JudgeDated%%');
                    array_push($values, '-');
                }
                else{
                    $Dated =  date('m/d/Y', strtotime($JDated));
                    array_push($keys, '%%JudgeDated%%');
                    array_push($values, $Dated);
                }
                //Judgement Date Format Change
                //Judgement Recorded date Format Change
                $RDated = $torderjudgments_array[$i]->JudgementRecorded;
                if($RDated == '0000-00-00')
                {
                    array_push($keys, '%%JudgeRecorded%%');
                    array_push($values, '-');
                }
                else{
                    $RecordedDated =  date('m/d/Y', strtotime($RDated));
                    array_push($keys, '%%JudgeRecorded%%');
                    array_push($values, $RecordedDated);
                }
                // JudgementRecorded date Format Change
                $Judgement_DBVTypeUID_1 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_1;
                $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_1);
                $Judgement_DBVTypeValue_1 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_1;
                $Judgement_DBVTypeUID_2 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_2;
                $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_2);
                $Judgement_DBVTypeValue_2 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_2;
                if($DBVTypeName_1)
                {
                    array_push($keys, '%%DBVTypeName_1%%');
                    array_push($values, $DBVTypeName_1);
                    if($Judgement_DBVTypeValue_1)
                    {
                        array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                        array_push($values, $Judgement_DBVTypeValue_1);
                    }
                    else{
                        array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                        array_push($values, '-');
                    }
                }
                else{
                    array_push($keys, '%%DBVTypeName_1%%');
                    array_push($values, 'Book/Page');
                    array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                    array_push($values, '-');
                }

                if($DBVTypeName_2)
                {
                    array_push($keys, '%%DBVTypeName_2%%');
                    array_push($values, $DBVTypeName_2);
                    if($Judgement_DBVTypeValue_2)
                    {
                        array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                        array_push($values, $Judgement_DBVTypeValue_2);
                    }
                    else{
                        array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                }
                else{

                    array_push($keys, '%%DBVTypeName_2%%');
                    array_push($values, 'Instrument');
                    array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                    array_push($values, '-');
                }
                //Instrument
                if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                {
                    $AppendInstrument = $Judgement_DBVTypeValue_1.','.$Judgement_DBVTypeValue_2;
                    array_push($keys, '%%JudInstrument%%');
                    array_push($values, $AppendInstrument);

                }
                if($DBVTypeName_1 == 'Instrument'){
                    $AppendInstrument = $Judgement_DBVTypeValue_1;
                    array_push($keys, '%%JudInstrument%%');
                    array_push($values, $AppendInstrument);

                }
                if($DBVTypeName_2 == 'Instrument'){
                    $AppendInstrument = $Judgement_DBVTypeValue_2;
                    array_push($keys, '%%JudInstrument%%');
                    array_push($values, $AppendInstrument);
                }
                else{
                    $AppendInstrument = '-';
                    array_push($keys, '%%JudInstrument%%');
                    array_push($values, $AppendInstrument);
                }
                //Instrument
                //CaseNumber
                $JudgementCaseNo = $torderjudgments_array[$i]->JudgementCaseNo;
                if($JudgementCaseNo)
                {
                    array_push($keys, '%%JudCaseNumber%%');
                    array_push($values, $JudgementCaseNo);
                }
                else{
                    if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                    {
                        $AppendCasenumber = $Judgement_DBVTypeValue_1.','.$Judgement_DBVTypeValue_2;
                        array_push($keys, '%%JudCaseNumber%%');
                        array_push($values, $AppendCasenumber);

                    }
                    if($DBVTypeName_1 == 'Case number'){
                        $AppendCasenumber = $Judgement_DBVTypeValue_1;
                        array_push($keys, '%%JudCaseNumber%%');
                        array_push($values, $AppendCasenumber);

                    }
                    if($DBVTypeName_2 == 'Case number'){
                        $AppendCasenumber = $Judgement_DBVTypeValue_2;
                        array_push($keys, '%%JudCaseNumber%%');
                        array_push($values, $AppendCasenumber);
                    }
                    else{
                        $AppendCasenumber = '-';
                        array_push($keys, '%%JudCaseNumber%%');
                        array_push($values, $AppendCasenumber);
                    }
                }
                //CaseNumber
                //Judgements Comments
                $JudgementComments = $torderjudgments_array[$i]->JudgementComments;
                if($JudgementComments)
                {
                    array_push($keys, '%%JudgementComments%%');
                    array_push($values, $JudgementComments);
                }
                else{
                    array_push($keys, '%%JudgementComments%%');
                    array_push($values, '-');
                }
                //Judgements Comments

                array_push($keys, '%%'.$key.'%%');
                array_push($values, $value);
            }
            $torderjudgments_table .= str_replace($keys, $values, $torderjudgments[0][0]);
        }
        foreach ( $doc->find(".torderjudgments") as $node ) 
        {
            $node->innertext = $torderjudgments_table;
        }
        //Judgments Ends
        return $doc;
    }

    function real_estate_property_report($OrderUID)
    {
        error_reporting(0);
        $this->load->library('Dom/Simple_html_dom');
        $doc = new simple_html_dom();
        $filename = FCPATH.'Templates/realestatepropertyreport.php';
        $OrderUID = $OrderUID;
        $fp = fopen ( $filename, 'r' );
        $output = fread( $fp, filesize($filename));


        //Order
        $torders_array = array();
        $order_details = $this->Order_reports_model->get_torders($OrderUID);
        $keys = array();
        $values = array();
        $date = array('CurrentDate'=>date("m/d/Y"));
        foreach ($date as $key => $value) 
        {

            array_push($keys, '%%'.$key.'%%');
            array_push($values, $value);
        }
        //Address
         include APPPATH .'modules/Order_reports/views/addressvariance.php';
        //Address

        $base_url = FCPATH;
        $UserName = $this->Order_reports_model->GetUserName($this->loggedid);
        $APN = $order_details->APN;
        $CustomerName = $order_details->CustomerName;
        $CustomerNumber = $order_details->CustomerNumber;
        if($CustomerNumber)
        {
            $CustomerNumber = $order_details->CustomerNumber.'/';
            $CustomerNo = $order_details->CustomerNumber;
        }
        else{
            $CustomerNumber = '';
        }
        $OrderNumber = $order_details->OrderNumber;
        $LoanNumber = $order_details->LoanNumber;
        $OwnerName = $order_details->OwnerName;
        $CountyName =  $order_details->CountyName;
        $PropertyAddress1 = $order_details->PropertyAddress1;
        $PropertyAddress2 = $order_details->PropertyAddress2;
        $CustomerAddress1 = $order_details->CustomerAddress1;
        $CustomerAddress2 = $order_details->CustomerAddress2;
        $CustomerStateCode =  $order_details->CustomerStateCode;
        $CustomerCountyName = $order_details->CustomerCountyName;
        $CustomerCityName = $order_details->CustomerCityName;
        $CustomerZipCode = $order_details->CustomerZipCode;
        $CustomerPContactMobileNo = $order_details->CustomerPContactMobileNo;
        $CustomerFaxNo = $order_details->CustomerFaxNo;
        $CityName = $order_details->CityName;
        $StateName = $order_details->StateName;
        $StateCode = $order_details->StateCode;
        $date = $order_details->OrderEntryDatetime;
        $OrderDate =  date('m/d/Y', strtotime($date));
        $SearchFrom = $order_details->SearchFromDate;
        if($SearchFrom == '0000-00-00 00:00:00' || $SearchFrom == '')
            {
                $SearchFromDate = '-';
            }
            else
            {
                $SearchFromDate = date('m/d/Y', strtotime($SearchFrom));
            }
            $SearchAsOf = $order_details->SearchAsOfDate;
            if($SearchAsOf == '0000-00-00 00:00:00' || $SearchAsOf == '')
                {
                    $SearchAsOfDate = '-';
                }
                else
                {
                    $SearchAsOfDate = date('m/d/Y', strtotime($SearchAsOf));
                }       
                $DisclaimerNote = $this->Order_reports_model->get_DisclaimerNote($StateName);
                $ZipCode = $order_details->PropertyZipcode;
                $LastDeedRecordedDate = $order_details->LastDeedRecorded;
                $LastDeedRecorded =  date('m/d/Y', strtotime($LastDeedRecordedDate));
                $ReportGeneratedDateTime = date("Y-m-d h:i:sa");
                $ImageUrl = base_url().'assets/img/sourcepoint.png';
                $Mort = array('OrderDate'=>$OrderDate,'CustomerName'=>$CustomerNumber.$CustomerName,'CustomerNumber'=>$CustomerNo,'CustomerAddress1'=>$CustomerAddress1,'CustomerAddress2'=>$CustomerAddress2,'CustomerStateCode'=>$CustomerStateCode,'CustomerCountyName'=>$CustomerCountyName,'CustomerCityName'=>$CustomerCityName,'CustomerPContactMobileNo'=>$CustomerPContactMobileNo,'CustomerFaxNo'=>$CustomerFaxNo,'Ordernumber'=>$OrderNumber,'Loannumber'=>$LoanNumber,'Ownername'=>$OwnerName,'Countyname'=>$CountyName,'Propertyaddress1'=>$PropertyAddress1,'Propertyaddress2'=>$PropertyAddress2,'Cityname'=>$CityName,'Statecode'=>$StateCode,'Zip'=>$ZipCode,'APN'=>$APN,'LastDeedRecorded'=>$LastDeedRecorded,'DisclaimerNote'=>$DisclaimerNote,'ReportGeneratedDateTime'=>$ReportGeneratedDateTime,'SearchFromDate'=>$SearchFromDate,'SearchAsOfDate'=>$SearchAsOfDate,'CustomerZipCode'=>$CustomerZipCode,'UserID'=>$UserName,'Url'=>$base_url,'ImageUrl'=>$ImageUrl);
                foreach ($Mort as $key => $value) 
                {
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
        //Order

        //Order Assessment
                $order_assessment = $this->Order_reports_model->get_OrderAssessment($OrderUID);
                if($order_assessment)
                {

                    foreach ($order_assessment as $data_orderass_info) 
                    {
                        $AssessedYear = $data_orderass_info->AssessedYear;

                        if($AssessedYear)
                        {
                            array_push($keys, '%%AssessedYear%%');
                            array_push($values, $AssessedYear);
                        }
                        else{
                            array_push($keys, '%%AssessedYear%%');
                            array_push($values, '-');

                        }
                        $Landstr = $data_orderass_info->Land;
                        if($Landstr)
                        {
                            $Landltrim = ltrim($Landstr, '$');
                            $LandRepl = str_replace(",","",$Landltrim);
                            $Lan = substr($LandRepl, 0,-3);
                            $Land = '$'.number_format($Lan,2);
                        }
                        else{
                            $Land = '-';
                        }
                        $Buildingsstr = $data_orderass_info->Buildings;
                        if($Buildingsstr)
                        {

                            $Buildingsltrim = ltrim($Buildingsstr, '$');
                            $BuildingsRepl = str_replace(",","",$Buildingsltrim);
                            $Build = substr($BuildingsRepl, 0,-3);
                            $Buildings = '$'.number_format($Build,2);
                        }
                        else{
                            $Buildings = '-';
                        }
                        $TotalValue = $data_orderass_info->TotalValue;
                        if($TotalValue)
                        {
                            $TotalValue = '$'.$TotalValue;
                            array_push($keys, '%%TotalValue%%');
                            array_push($values, $TotalValue);
                        }
                        else{
                            array_push($keys, '%%TotalValue%%');
                            array_push($values, '-');

                        }
                        $Value = array('TotalValue'=>$TotalValue,'Buildings'=>$Buildings,'Land'=>$Land);
                        foreach ($Value as $key => $value) 
                        {
                            array_push($keys, '%%'.$key.'%%');
                            array_push($values, $value);
                        }
                    } 
                }
                else{


                    $TaxArray = array('AssessedYear','Land','Buildings','TotalValue');
                    foreach($TaxArray as $col)
                    {
                        array_push($keys, '%%'.$col.'%%');
                        array_push($values, '-');
                    }

                }
                $output = str_replace($keys,$values, $output);
        //Order Assessment

        //Get Borrowers
            $borrowers = $this->Order_reports_model->getBorrowers($OrderUID);
            $Count =  count($borrowers);
            if($Count > 1){

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    if($value->PRName !=''){

                    $borrower .=  trim($value->PRName.',');
                    }
                }
                $borrowers =  rtrim($borrower,",");
                $Array = explode(",", $borrowers);
                $LastArrayValue =  end($Array);
                
                if(count($Array) > 1 ){
                    array_pop($Array);
                    $BorrowerName = implode(", &nbsp;", $Array).' and '.$LastArrayValue;

                }else{
                    
                    $BorrowerName = implode(", &nbsp;", $Array);
                }
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
            else{

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    $borrower .=  $value->PRName.',';

                }
                $BorrowerName =  rtrim($borrower,",");
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
        //Get Borrowers
        //Legal Description
                $legal_information = $this->Order_reports_model->get_LegalDescription($OrderUID); 
                if($legal_information)
                {
                    foreach($legal_information as $data) 
                    {
                        $LegalDescr = str_replace('  ', ' &nbsp;', nl2br(strtoupper($data->LegalDescription)));

                        if($LegalDescr){

                            array_push($keys, '%%LegalDescr%%');
                            array_push($values, $LegalDescr);
                        }
                        else{

                            array_push($keys, '%%LegalDescr%%');
                            array_push($values, ' ');
                        }
                        foreach($data as $col_legal => $val_legal)
                        {
                            array_push($keys, '%%'.$col_legal.'%%');
                            array_push($values, $val_legal);
                        }

                    }
                }
                else{
                    array_push($keys, '%%LegalDescr%%');
                    array_push($values, ' ');
                }
        //Legal Description
                $output = str_replace($keys,$values, $output);
                $doc->load($output);
        //Deed Starts
                preg_match_all('/<div class=\"torderdeeds\">(.*?)<\/div>/s',$output,$torderdeeds);
                $torderdeeds_table = '';
                $torderdeeds_array = array();
                $torderdeedsparties_array = array();
                $torder_deeds = $this->Order_reports_model->get_torderdeeds($OrderUID);
                $torderdeeds_array = $torder_deeds;
                $torderdeeds_array_count = count($torderdeeds_array);
                for ($i=0; $i < $torderdeeds_array_count; $i++) 
                { 
                    $torderdeeds_array[$i]->deed_increment = $i+1;
                    $keys = array();
                    $values = array();
                    foreach ($torderdeeds_array[$i] as $key => $value) 
                    {

                        $Deed_DBVTypeUID_1 = $torderdeeds_array[$i]->Deed_DBVTypeUID_1;
                        $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_1);
                        $Deed_DBVTypeValue_1 = $torderdeeds_array[$i]->Deed_DBVTypeValue_1;
                        $Deed_DBVTypeUID_2 = $torderdeeds_array[$i]->Deed_DBVTypeUID_2;
                        $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_2);
                        $Deed_DBVTypeValue_2 = $torderdeeds_array[$i]->Deed_DBVTypeValue_2;
                        if($DBVTypeName_1)
                        {
                            array_push($keys, '%%DeedDBVTypeName_1%%');
                            array_push($values, $DBVTypeName_1);
                            if($Deed_DBVTypeValue_1)
                            {
                                array_push($keys, '%%Deed_DBVTypeValue_1%%');
                                array_push($values, $Deed_DBVTypeValue_1);
                            }
                            else{
                                array_push($keys, '%%Deed_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else{
                            array_push($keys, '%%DeedDBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%Deed_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }
                        if($DBVTypeName_2)
                        {
                            array_push($keys, '%%DeedDBVTypeName_2%%');
                            array_push($values, $DBVTypeName_2);
                            if($Deed_DBVTypeValue_2)
                            {
                                array_push($keys, '%%Deed_DBVTypeValue_2%%');
                                array_push($values, $Deed_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%Deed_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else{

                            array_push($keys, '%%DeedDBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%Deed_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }

                //Deed Date Format Change
                        $DDated = $torderdeeds_array[$i]->DeedDated;
                        if($DDated == '0000-00-00')
                        {
                            array_push($keys, '%%DeedDate%%');
                            array_push($values, '-');
                        }
                        else{
                            $DeedDated =  date('m/d/Y', strtotime($DDated));
                            array_push($keys, '%%DeedDate%%');
                            array_push($values, $DeedDated);
                        }
                //Deed Date Format Change
                //Recorded date Format Change
                        $RDated = $torderdeeds_array[$i]->DeedRecorded;
                        if($RDated == '0000-00-00')
                        {
                            array_push($keys, '%%RecordedDate%%');
                            array_push($values, '-');
                        }
                        else{
                            $RecordedDated =  date('m/d/Y', strtotime($RDated));
                            array_push($keys, '%%RecordedDate%%');
                            array_push($values, $RecordedDated);
                        }
                //Recorded date Format Change
                //ConsiderationAmount
                        $ConsiderationAmount = $torderdeeds_array[$i]->ConsiderationAmount;
                        $ConsiderAmount = '$'.number_format($ConsiderationAmount,2);
                        array_push($keys, '%%ConsiderAmount%%');
                        array_push($values, $ConsiderAmount);
                //ConsiderationAmount
                //Main Loop
                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                //Main Loop
                    }
                    $torderdeeds_table .= str_replace($keys, $values, $torderdeeds[0][0]);
                }

                foreach ( $doc->find(".torderdeeds") as $node ) 
                {
                    $node->innertext = $torderdeeds_table;
                }
        //Deed Ends
        //Mortgages Starts
                preg_match_all('/<div class=\"tordermortgages\">(.*?)<\/div>/s',$output,$tordermortgages);
                $tordermortgages_table = '';
                $tordermortgages_array = array();
                $tordermortgagesparties_array = array();
                $tordermortgages_array = $this->Order_reports_model->get_tordermortgageparties($OrderUID);
                $tordermortgages_array_count = count($tordermortgages_array);
                for ($i=0; $i < $tordermortgages_array_count; $i++) 
                { 
                    $tordermortgages_array[$i]->mortgage_increment = $i+1;
                    $keys = array();
                    $values = array();
                    $NumberToWords = $this->Order_reports_model->ToOrdinal($i+1);
                    array_push($keys, '%%NumberToWords%%');
                    array_push($values, strtoupper($NumberToWords));
                    foreach ($tordermortgages_array[$i] as $key => $value) 
                    {

                    $DocumentTypeName = $tordermortgages_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $MortgageType = $tordermortgages_array[$i]->MortgageType;
                            if($MortgageType)
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, $MortgageType);
                            }
                            else
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%MortgageDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //Mortgage Date Format Change
                        $MortgageDated = $tordermortgages_array[$i]->MortgageDated;
                        if($MortgageDated == '0000-00-00')
                        {
                            array_push($keys, '%%MortgageDate%%');
                            array_push($values, '-');
                        }
                        else{
                            $MortgageDate =  date('m/d/Y', strtotime($MortgageDated));
                            array_push($keys, '%%MortgageDate%%');
                            array_push($values, $MortgageDate);
                        }
                //Mortgage Date Format Change
                //Mortgage Date Format Change
                        $MortgageRecorded = $tordermortgages_array[$i]->MortgageRecorded;
                        if($MortgageRecorded == '0000-00-00')
                        {
                            array_push($keys, '%%MortgageRecordedDate%%');
                            array_push($values, '-');
                        }
                        else{
                            $MortgageRecordedDate =  date('m/d/Y', strtotime($MortgageRecorded));
                            array_push($keys, '%%MortgageRecordedDate%%');
                            array_push($values, $MortgageRecordedDate);
                        }
                //Mortgage Date Format Change
                //Mortgage Maturity Date Format
                        $MortgageMaturityDate = $tordermortgages_array[$i]->MortgageMaturityDate;
                        if($MortgageMaturityDate == '0000-00-00')
                        {
                            array_push($keys, '%%MaturityDate%%');
                            array_push($values, '-');   
                        }
                        else{
                            $MaturityDate =  date('m/d/Y', strtotime($MortgageMaturityDate));
                            array_push($keys, '%%MaturityDate%%');
                            array_push($values, $MaturityDate);
                        }
                //Mortgage Maturity Date Format
                //MortgageAmount
                        $MortgageAmount = $tordermortgages_array[$i]->MortgageAmount;
                        if($MortgageAmount)
                        {
                            $MortAmt = '$'.number_format($MortgageAmount,2);
                            array_push($keys, '%%MortgageAmount%%');
                            array_push($values, $MortAmt);
                        }
                        else{
                            array_push($keys, '%%MortgageAmount%%');
                            array_push($values, '-');
                        }
                //MortgageAmount
                //Mortgagee
                        $Mortgagee = $tordermortgages_array[$i]->Mortgagee;
                        if($Mortgagee)
                        {
                            array_push($keys, '%%Mortgagee%%');
                            array_push($values, $Mortgagee);
                        }
                        else{
                            array_push($keys, '%%Mortgagee%%');
                            array_push($values, '-');
                        }
                //Mortgagee
                        // if($tordermortgages_array[$i]->LienTypeName =='Closed Ended')
                        // {
                        //     $MTG = array('MTG'=>'unchecked.png');
                        //     foreach($MTG as $col => $val)
                        //     {
                        //         array_push($keys, '%%'.$col.'%%');
                        //         array_push($values, $val);
                        //     }
                        // }
                        // if($tordermortgages_array[$i]->LienTypeName =='Open Ended')
                        // {
                        //     $MTG = array('MTG'=>'checked.png');
                        //     foreach($MTG as $col => $val)
                        //     {
                        //         array_push($keys, '%%'.$col.'%%');
                        //         array_push($values, $val);
                        //     }
                        // }
                        // else
                        // {
                        //     $MTG = array('MTG'=>'empty.png');
                        //     foreach($MTG as $col => $val)
                        //     {
                        //         array_push($keys, '%%'.$col.'%%');
                        //         array_push($values, $val);
                        //     }
                        // }
                            //Closed/Open Ended
                            $OpenEnded = $tordermortgages_array[$i]->IsOpenEnded;
                            if($OpenEnded == '1')
                            {

                                    array_push($keys, '%%OpenEnded%%');
                                    array_push($values, 'Yes');
                                
                            }
                            else
                            {

                                    array_push($keys, '%%OpenEnded%%');
                                    array_push($values,'-');
                                
                            }
                //Book/Page
                        $Mortgage_DBVTypeUID_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_1;
                        $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_1);
                        $Mortgage_DBVTypeValue_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_1;
                        $Mortgage_DBVTypeUID_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_2;
                        $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_2);
                        $Mortgage_DBVTypeValue_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_2;
                        if($DBVTypeName_1)
                        {
                            array_push($keys, '%%MortgageDBVTypeName_1%%');
                            array_push($values, $DBVTypeName_1);
                            if($Mortgage_DBVTypeValue_1)
                            {
                                array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                                array_push($values, $Mortgage_DBVTypeValue_1);
                            }
                            else{
                                array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else{
                            array_push($keys, '%%MortgageDBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }
                        if($DBVTypeName_2)
                        {
                            array_push($keys, '%%MortgageDBVTypeName_2%%');
                            array_push($values, $DBVTypeName_2);
                            if($Mortgage_DBVTypeValue_2)
                            {
                                array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                                array_push($values, $Mortgage_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else{

                            array_push($keys, '%%MortgageDBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }
                //Instrument
                        if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                        {
                            $AppendInstrument = $Mortgage_DBVTypeValue_1.','.$Mortgage_DBVTypeValue_2;
                            array_push($keys, '%%InstrumentNo_1%%');
                            array_push($values, $AppendInstrument);

                        }
                        if($DBVTypeName_1 == 'Instrument'){
                            $AppendInstrument = $Mortgage_DBVTypeValue_1;
                            array_push($keys, '%%InstrumentNo_1%%');
                            array_push($values, $AppendInstrument);

                        }
                        if($DBVTypeName_2 == 'Instrument'){
                            $AppendInstrument = $Mortgage_DBVTypeValue_2;
                            array_push($keys, '%%InstrumentNo_1%%');
                            array_push($values, $AppendInstrument);
                        }
                        else{
                            $AppendInstrument = '-';
                            array_push($keys, '%%InstrumentNo_1%%');
                            array_push($values, $AppendInstrument);
                        }
                //Instrument
                //Main Loop
                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                //Main Loop
                    }
                    $tordermortgages_table .= str_replace($keys, $values, $tordermortgages[0][0]);

                }
                foreach ( $doc->find(".tordermortgages") as $node ) 
                {
                    $node->innertext = $tordermortgages_table;
                }
        //Mortgages Ends
        //Tax Starts
                preg_match_all('/<div class=\"tordertaxcerts\">(.*?)<\/div>/s',$output,$tordertaxcerts);
                $tordertaxcerts_array = array();
                $tordertaxcerts_array = $this->Order_reports_model->get_tordertaxcerts($OrderUID);
                $tordertaxcerts_table2="";
                $tordertaxcerts_array_count = count($tordertaxcerts_array);
                for ($i=0; $i < $tordertaxcerts_array_count; $i++) 
                { 
                $tordertaxcerts_table = '';
                    $tordertaxcerts_array[$i]->mortgage_increment = $i+1;
                    $keys = array();
                    $values = array();
                    foreach ($tordertaxcerts_array[$i] as $key => $value) 
                    {


                //Tax Date Format Change
                        $GoodThroughDate = $tordertaxcerts_array[$i]->GoodThroughDate;
                        if($GoodThroughDate !== '0000-00-00')
                        {   
                            $ThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                            array_push($keys, '%%ThroughDate%%');
                            array_push($values, $ThroughDate);
                        }
                        else{
                            array_push($keys, '%%ThroughDate%%');
                            array_push($values, '-');
                        }
                //Tax Date Format Change
                //Tax Date Paid
                        $ParcelNumber = $tordertaxcerts_array[$i]->ParcelNumber;
                        if($ParcelNumber)
                        {
                            array_push($keys, '%%ParcelNumber%%');
                            array_push($values, $ParcelNumber);
                        }
                        else{
                            
                            array_push($keys, '%%ParcelNumber%%');
                            array_push($values, '-');
                        }
                //Tax Date Paid
                //Tax Comments
                        $TaxComments = $tordertaxcerts_array[$i]->TaxComments;
                        if($TaxComments)
                        {
                            array_push($keys, '%%TaxComments%%');
                            array_push($values, $TaxComments);
                            array_push($keys, '%%taxalignment%%');
                            array_push($values, 'text-left');
                        }
                        else{
                            array_push($keys, '%%TaxComments%%');
                            array_push($values, '-');
                            array_push($keys, '%%taxalignment%%');
                            array_push($values, 'text-center');
                        }
                //Tax Comments
                //Main Loop

                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);

                //Main Loop
                    }
                $tordertaxcerts_table .= str_replace($keys, $values, $tordertaxcerts[0][0]);
                // Tax Installemnt
                $taxinstallment_html='<tr> <td> <p>TAX YEAR&nbsp;:&nbsp;%%Tax_Year%%</p></td></tr><tr><td style="white-space: nowrap;"> <p>%%TaxInstallmentName%%&nbsp;:&nbsp;%%Gross_Amount%%&nbsp;%%Tax_StatusName%%,PAID DATE &nbsp;%%Paid_Date%%</p></td></tr>';
                $taxinstallment="";
                $taxinstallment_table = '';
                $taxinstallment_array = array();
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $taxinstallment_array = $this->Order_reports_model->gettaxinstallment($OrderUID, $TaxCert);
                $taxinstallment_array_count = count($taxinstallment_array);
                for ($k=0; $k < $taxinstallment_array_count; $k++) 
                { 

                    $keys = array();
                    $values = array();
                    foreach ($taxinstallment_array[$k] as $key => $value) 
                    {
                        $TaxYear = $taxinstallment_array[$k]->TaxYear;
                        if($TaxYear)
                        {
                            array_push($keys, '%%Tax_Year%%');
                            array_push($values, $TaxYear);

                        }
                        else{
                            array_push($keys, '%%Tax_Year%%');
                            array_push($values, '-');

                        }

                        $GrossAmount = $taxinstallment_array[$k]->GrossAmount;
                        if($GrossAmount)
                        {
                            $GrossAmount = '$'.number_format($GrossAmount,2);
                            array_push($keys, '%%Gross_Amount%%');
                            array_push($values, $GrossAmount);

                        }
                        else{
                            array_push($keys, '%%Gross_Amount%%');
                            array_push($values, '-');

                        }
                        $TaxStatusName = $taxinstallment_array[$k]->TaxStatusName;
                        if($TaxStatusName)
                        {
                            array_push($keys, '%%Tax_StatusName%%');
                            array_push($values, $TaxStatusName);

                        }
                        else{
                            array_push($keys, '%%Tax_StatusName%%');
                            array_push($values, '-');

                        }
                        $TaxInstallmentName = $taxinstallment_array[$k]->TaxInstallmentName;
                        if($TaxInstallmentName)
                        {
                            array_push($keys, '%%TaxInstallmentName%%');
                            array_push($values, $TaxInstallmentName);

                        }
                        else{
                            array_push($keys, '%%TaxInstallmentName%%');
                            array_push($values, '-');

                        }
                        $DatePaid = $taxinstallment_array[$k]->DatePaid;
                        if($DatePaid  == '0000-00-00')
                        {
                            array_push($keys, '%%Paid_Date%%');
                            array_push($values, '-');

                        }
                        else{
                            $DatePaid =  date('m/d/Y', strtotime($DatePaid));
                            array_push($keys, '%%Paid_Date%%');
                            array_push($values, $DatePaid);

                        }

                        $GoodThroughDate = $taxinstallment_array[$k]->GoodThroughDate;
                        if($GoodThroughDate  == '0000-00-00')
                        {
                            array_push($keys, '%%Good_Through_Date%%');
                            array_push($values, '-');

                        }
                        else{
                            $GoodThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                            array_push($keys, '%%Good_Through_Date%%');
                            array_push($values, $GoodThroughDate);

                        }
                    }

                    $taxinstallment .= str_replace($keys, $values, $taxinstallment_html);

                }
                $tordertaxcerts_table = str_replace('%%taxinstallment%%', $taxinstallment, $tordertaxcerts_table);
                $tordertaxcerts_table2 .= $tordertaxcerts_table;
                //Tax Installment
                }
                foreach($doc->find(".tordertaxcerts")as$node) 
                {
                    $node->innertext = $tordertaxcerts_table2;
                }
        //Tax Ends
        //Judgement Starts
                preg_match_all('/<div class=\"torderjudgments\">(.*?)<\/div>/s',$output,$torderjudgments);
                $torderjudgments_table = '';
                $torderjudgments_array = array();
                $torderjudgments_array = $this->Order_reports_model->get_torderjudgements($OrderUID);

                $torderjudgments_array_count = count($torderjudgments_array);
                for ($i=0; $i < $torderjudgments_array_count; $i++)
                { 
                    $torderjudgments_array[$i]->judgement_increment = $i+1;
                    $keys = array();
                    $values = array();
                    foreach ($torderjudgments_array[$i] as $key => $value)
                    {

                    $DocumentTypeName = $torderjudgments_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $JudgementType = $torderjudgments_array[$i]->JudgementType;
                            if($JudgementType)
                            {
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, $JudgementType);
                            }
                            else{
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%JudgementDocumentTypeName%%');
                        array_push($values, '-');
                    }

                //Plaintiff
                        $Plaintiff = $torderjudgments_array[$i]->Plaintiff;
                        if($Plaintiff)
                        {
                            array_push($keys, '%%Plaintiff%%');
                            array_push($values, $Plaintiff);
                        }
                        else{
                            array_push($keys, '%%Plaintiff%%');
                            array_push($values, '-');
                        }
                //Plaintiff
                //Defendant
                        $Defendent = $torderjudgments_array[$i]->Defendent;
                        if($Defendent)
                        {
                            array_push($keys, '%%Defendent%%');
                            array_push($values, $Defendent);
                        }
                        else{
                            array_push($keys, '%%Defendent%%');
                            array_push($values, '-');
                        }
                //Defendant
                //Judgement Amount
                        $JudgementAmount = $torderjudgments_array[$i]->JudgementAmount;
                        if($JudgementAmount)
                        {
                            $JudAmt = '$'.number_format($JudgementAmount,2);
                            array_push($keys, '%%JudgementAmount%%');
                            array_push($values, $JudAmt);
                        }
                        else{
                            array_push($keys, '%%JudgementAmount%%');
                            array_push($values, '-');
                        }
                //Judgement Amount
                //Judgement Date Format Change
                        $JDated = $torderjudgments_array[$i]->JudgementDated;
                        if($JDated == '0000-00-00')
                        {
                            array_push($keys, '%%JudgeDated%%');
                            array_push($values, '-');
                        }
                        else{
                            $Dated =  date('m/d/Y', strtotime($JDated));
                            array_push($keys, '%%JudgeDated%%');
                            array_push($values, $Dated);
                        }
                //Judgement Date Format Change
                //Judgement Recorded date Format Change
                        $RDated = $torderjudgments_array[$i]->JudgementRecorded;
                        if($RDated == '0000-00-00')
                        {
                            array_push($keys, '%%JudgeRecorded%%');
                            array_push($values, '-');
                        }
                        else{
                            $RecordedDated =  date('m/d/Y', strtotime($RDated));
                            array_push($keys, '%%JudgeRecorded%%');
                            array_push($values, $RecordedDated);
                        }
                        $FDated = $torderjudgments_array[$i]->JudgementFiled;
                        if($FDated == '0000-00-00')
                        {
                            array_push($keys, '%%JudgeFiled%%');
                            array_push($values, '-');
                        }
                        else{
                            $FiledDated =  date('m/d/Y', strtotime($FDated));
                            array_push($keys, '%%JudgeFiled%%');
                            array_push($values, $FiledDated);
                        }
                // JudgementRecorded date Format Change
                        $Judgement_DBVTypeUID_1 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_1;
                        $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_1);
                        $Judgement_DBVTypeValue_1 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_1;
                        $Judgement_DBVTypeUID_2 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_2;
                        $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_2);
                        $Judgement_DBVTypeValue_2 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_2;
                        if($DBVTypeName_1)
                        {
                            array_push($keys, '%%JudgementDBVTypeName_1%%');
                            array_push($values, $DBVTypeName_1);
                            if($Judgement_DBVTypeValue_1)
                            {
                                array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                array_push($values, $Judgement_DBVTypeValue_1);
                            }
                            else{
                                array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else{
                            array_push($keys, '%%JudgementDBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }
                        if($DBVTypeName_2)
                        {
                            array_push($keys, '%%JudgementDBVTypeName_2%%');
                            array_push($values, $DBVTypeName_2);
                            if($Judgement_DBVTypeValue_2)
                            {
                                array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                array_push($values, $Judgement_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else{

                            array_push($keys, '%%JudgementDBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }
                //Instrument
                        if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                        {
                            $AppendInstrument = $Judgement_DBVTypeValue_1.','.$Judgement_DBVTypeValue_2;
                            array_push($keys, '%%InstrumentNo_1%%');
                            array_push($values, $AppendInstrument);

                        }
                        if($DBVTypeName_1 == 'Instrument'){
                            $AppendInstrument = $Judgement_DBVTypeValue_1;
                            array_push($keys, '%%InstrumentNo_1%%');
                            array_push($values, $AppendInstrument);

                        }
                        if($DBVTypeName_2 == 'Instrument'){
                            $AppendInstrument = $Judgement_DBVTypeValue_2;
                            array_push($keys, '%%InstrumentNo_1%%');
                            array_push($values, $AppendInstrument);
                        }
                        else{
                            $AppendInstrument = '-';
                            array_push($keys, '%%InstrumentNo_1%%');
                            array_push($values, $AppendInstrument);
                        }
                //Instrument
                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                    }
                    $torderjudgments_table .= str_replace($keys, $values, $torderjudgments[0][0]);
                }
                foreach ( $doc->find(".torderjudgments") as $node ) 
                {
                    $node->innertext = $torderjudgments_table;
                }
        //Judgments Ends
        //Liens Starts
                preg_match_all('/<div class=\"torderliens\">(.*?)<\/div>/s',$output,$torderliens);
                $torderliens_table = '';
                $torderliens_array = array();
                $torderliens_array = $this->Order_reports_model->get_torderliens($OrderUID);
                $torderliens_array_count = count($torderliens_array);
                for ($i=0; $i < $torderliens_array_count; $i++) 
                { 

                    $torderliens_array[$i]->lien_increment = $i+1;

                    $keys = array();
                    $values = array();
            //Lein Date Format Change
                    $LDated = $torderliens_array[$i]->LeinDated;
                    if($LDated == '0000-00-00')
                    {
                        array_push($keys, '%%LeinDate%%');
                        array_push($values, '-');
                    }
                    else{

                        $Dated =  date('m/d/Y', strtotime($LDated));
                        array_push($keys, '%%LeinDate%%');
                        array_push($values, $Dated);
                    }
            //Lein Date Format Change
            //Lein Recorded date Format Change
                    $RDated = $torderliens_array[$i]->LeinRecorded;
                    if($RDated == '0000-00-00')
                    {   
                        array_push($keys, '%%LeinRecord%%');
                        array_push($values, '-');
                    }
                    else{
                        $RecordedDated =  date('m/d/Y', strtotime($RDated));
                        array_push($keys, '%%LeinRecord%%');
                        array_push($values, $RecordedDated);
                    }
            // Lein date Format Change
            // Lein Filed
                    $LeinFiled = $torderliens_array[$i]->LeinFiled;
                    if($LeinFiled == '0000-00-00')
                    {   
                        array_push($keys, '%%LeinFiled%%');
                        array_push($values, '-');
                    }
                    else{
                        $LeinFiled =  date('m/d/Y', strtotime($LeinFiled));
                        array_push($keys, '%%LeinFiled%%');
                        array_push($values, $LeinFiled);
                    }
            // Lein Filed
                    foreach ($torderliens_array[$i] as $key => $value) 
                    {

                    $DocumentTypeName = $torderliens_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $LeinType = $torderliens_array[$i]->LeinType;
                            if($LeinType)
                            {
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, $LeinType);
                            }
                            else{
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%LeinDocumentTypeName%%');
                        array_push($values, '-');
                    }

                //Lein Amount
                        $LeinAmount = $torderliens_array[$i]->LeinAmount;
                        if($LeinAmount)
                        {
                            $LeinAmt = '$'.number_format($LeinAmount,2);
                            array_push($keys, '%%LeinAmt%%');
                            array_push($values, $LeinAmt);
                        }
                        else{
                            array_push($keys, '%%LeinAmt%%');
                            array_push($values, '-');
                        }
                //Lein Amount
                        if($torderliens_array[$i]->LienTypeName =='Closed Ended')
                        {
                            $MTG = array('MTG'=>'Closed');
                            foreach($MTG as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        if($torderliens_array[$i]->LienTypeName =='Open Ended')
                        {
                            $MTG = array('MTG'=>'Open');
                            foreach($MTG as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        else
                        {
                            $MTG = array('MTG'=>'No');
                            foreach($MTG as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }

                        $Lien_DBVTypeUID_1 = $torderliens_array[$i]->Lien_DBVTypeUID_1;
                        $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_1);
                        $Lien_DBVTypeValue_1 = $torderliens_array[$i]->Lien_DBVTypeValue_1;
                        $Lien_DBVTypeUID_2 = $torderliens_array[$i]->Lien_DBVTypeUID_2;
                        $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_2);
                        $Lien_DBVTypeValue_2 = $torderliens_array[$i]->Lien_DBVTypeValue_2;
                        if($DBVTypeName_1)
                        {
                            array_push($keys, '%%LienDBVTypeName_1%%');
                            array_push($values, $DBVTypeName_1);
                            if($Lien_DBVTypeValue_1)
                            {
                                array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                array_push($values, $Lien_DBVTypeValue_1);
                            }
                            else{
                                array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else{
                            array_push($keys, '%%LienDBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%Lien_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }
                        if($DBVTypeName_2)
                        {
                            array_push($keys, '%%LienDBVTypeName_2%%');
                            array_push($values, $DBVTypeName_2);
                            if($Lien_DBVTypeValue_2)
                            {
                                array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                array_push($values, $Lien_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else{

                            array_push($keys, '%%LienDBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%Lien_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }
                //CaseNumber
                        if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                        {
                            $AppendCasenumber = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);

                        }
                        if($DBVTypeName_1 == 'Case number'){
                            $AppendCasenumber = $Lien_DBVTypeValue_1;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);

                        }
                        if($DBVTypeName_2 == 'Case number'){
                            $AppendCasenumber = $Lien_DBVTypeValue_2;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);
                        }
                        else{
                            $AppendCasenumber = ' ';
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);
                        }
                //CaseNumber

                        $Holder = $torderliens_array[$i]->Holder;
                        array_push($keys, '%%LeinHolder%%');
                        array_push($values, $Holder);

                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                    }
                    $output = str_replace($keys, $values, $output);

                    $torderliens_table .= str_replace($keys, $values, $torderliens[0][0]);

                }
                foreach ( $doc->find(".torderliens") as $node ) 
                {
                    $node->innertext = $torderliens_table;
                }
        //Leins Ends

        //Misc Exception Starts
                preg_match_all('/<div class=\"miscexception\">(.*?)<\/div>/s',$output,$tordermisc);
                $tordermisc_table = '';
                $tordermisc_array = array();
                $tordermisc_array = $this->Order_reports_model->get_torderliens($OrderUID);
                $tordermisc_array_count = count($tordermisc_array);
                for ($i=0; $i < $tordermisc_array_count; $i++) 
                { 

                    $tordermisc_array[$i]->lien_increment = $i+1;

                    $keys = array();
                    $values = array();
            //Lein Date Format Change
                    $LDated = $tordermisc_array[$i]->LeinDated;
                    if($LDated == '0000-00-00')
                    {
                        array_push($keys, '%%LeinDate%%');
                        array_push($values, ' ');
                    }
                    else{

                        $Dated =  date('m/d/Y', strtotime($LDated));
                        array_push($keys, '%%LeinDate%%');
                        array_push($values, $Dated);
                    }
            //Lein Date Format Change
            //Lein Recorded date Format Change
                    $RDated = $tordermisc_array[$i]->LeinRecorded;
                    if($RDated == '0000-00-00')
                    {   
                        array_push($keys, '%%LeinRecord%%');
                        array_push($values, ' ');
                    }
                    else{
                        $RecordedDated =  date('m/d/Y', strtotime($RDated));
                        array_push($keys, '%%LeinRecord%%');
                        array_push($values, $RecordedDated);
                    }
            // Lein date Format Change
                    foreach ($tordermisc_array[$i] as $key => $value) 
                    {
                //DocumentTypeName
                        $LeinDocumentTypeName = $tordermisc_array[$i]->DocumentTypeName;
                        array_push($keys, '%%LeinDocumentTypeName%%');
                        array_push($values, $LeinDocumentTypeName);
                //DocumentTypeName
                        if($tordermisc_array[$i]->LienTypeName =='Closed Ended')
                        {
                            $MTG = array('MTG'=>'Closed');
                            foreach($MTG as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        if($tordermisc_array[$i]->LienTypeName =='Open Ended')
                        {
                            $MTG = array('MTG'=>'Open');
                            foreach($MTG as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        else
                        {
                            $MTG = array('MTG'=>'No');
                            foreach($MTG as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }

                        $Lien_DBVTypeUID_1 = $tordermisc_array[$i]->Lien_DBVTypeUID_1;
                        $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_1);
                        $Lien_DBVTypeValue_1 = $tordermisc_array[$i]->Lien_DBVTypeValue_1;
                        $Lien_DBVTypeUID_2 = $tordermisc_array[$i]->Lien_DBVTypeUID_2;
                        $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_2);
                        $Lien_DBVTypeValue_2 = $tordermisc_array[$i]->Lien_DBVTypeValue_2;
                        if($DBVTypeName_1)
                        {
                            array_push($keys, '%%LienDBVTypeName_1%%');
                            array_push($values, $DBVTypeName_1);
                            if($Lien_DBVTypeValue_1)
                            {
                                array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                array_push($values, $Lien_DBVTypeValue_1);
                            }
                            else{
                                array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else{
                            array_push($keys, '%%LienDBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%Lien_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }
                        if($DBVTypeName_2)
                        {
                            array_push($keys, '%%LienDBVTypeName_2%%');
                            array_push($values, $DBVTypeName_2);
                            if($Lien_DBVTypeValue_2)
                            {
                                array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                array_push($values, $Lien_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else{

                            array_push($keys, '%%LienDBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%Lien_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }
                        //CaseNumber
                        if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                        {
                            $AppendCasenumber = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);

                        }
                        if($DBVTypeName_1 == 'Case number'){
                            $AppendCasenumber = $Lien_DBVTypeValue_1;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);

                        }
                        if($DBVTypeName_2 == 'Case number'){
                            $AppendCasenumber = $Lien_DBVTypeValue_2;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);
                        }
                        else{
                            $AppendCasenumber = ' ';
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);
                        }
                        //CaseNumber
                        $Holder = $torderliens_array[$i]->Holder;
                        array_push($keys, '%%LeinHolder%%');
                        array_push($values, $Holder);

                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                    }
                    $output = str_replace($keys, $values, $output);

                    $tordermisc_table .= str_replace($keys, $values, $tordermisc[0][0]);

                }
                foreach ( $doc->find(".miscexception") as $node ) 
                {
                    $node->innertext = $tordermisc_table;
                }
            //Misc Exception Ends 

                return $doc;
            }

            function sbi_report($OrderUID)
            {
                $OrderUID;
                error_reporting(0);
                $this->load->library('Dom/Simple_html_dom');
                $doc = new simple_html_dom();
                $OrderUID =$OrderUID;
                $TemplateName = strtolower(str_replace(' ', '', $order_details->TemplateName));
                $filename = FCPATH.'Templates/sbireport.php';
                $fp = fopen ( $filename, 'r' );
        //read our template into a variable
                $output = fread( $fp, filesize($filename));
        //Orders
                $torders_array = array();
                $keys = array();
                $values = array();
                $date = array('CurrentDate'=>date("m/d/Y"));
                foreach ($date as $key => $value) 
                {

                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
                //Address
                 include APPPATH .'modules/Order_reports/views/addressvariance.php';
                //Address
                $order_details = $this->Order_reports_model->get_torders($OrderUID);
                $date = $order_details->OrderEntryDatetime;
                $OrderDate =  date('m/d/Y', strtotime($date));
                $dates = array('OrderDate'=>$OrderDate);
                foreach ($dates as $key => $value) 
                {

                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
                $Attention =  $order_details->AttentionName;
                if($Attention)
                {
                    $AttentionName = $Attention;
                }
                else{

                    $AttentionName = '-';
                }
                $base_url = FCPATH;
                $CustomerPContactMobileNo = $order_details->CustomerPContactMobileNo;
                $CustomerAddress1 = $order_details->CustomerAddress1;
                $CustomerAddress2 = $order_details->CustomerAddress2;
                $Borrowers = $order_details->Borrower;
                $CustomerPContactName =  $order_details->CustomerPContactName;
                $Grantee = $order_details->Grantee;
                $ProductName = $order_details->ProductName;
                $SubProductName = $order_details->SubProductName;
                $APN = $order_details->APN;
                $CustomerName = $order_details->CustomerName;
                $CustomerNumber = $order_details->CustomerNumber;
                if($CustomerNumber)
                {
                    $CustomerNumber = $order_details->CustomerNumber.'/';
                    $CustomerNo = $order_details->CustomerNumber;
                }
                else{
                    $CustomerNumber = '';
                }
                $CustomerStateCode =  $order_details->CustomerStateCode;
                $CustomerCountyName = $order_details->CustomerCountyName;
                $CustomerCityName = $order_details->CustomerCityName;
                $CustomerZipCode = $order_details->CustomerZipCode;
                $OrderNumber = $order_details->OrderNumber;
                $LoanNumber = $order_details->LoanNumber;
                $OwnerName = $order_details->OwnerName;
                $CountyName =  $order_details->CountyName;
                $PropertyAddress1 = $order_details->PropertyAddress1;
                $PropertyAddress2 = $order_details->PropertyAddress2;
                $CityName = $order_details->CityName;
                $StateName = $order_details->StateName;
                $StateCode = $order_details->StateCode;
                $SearchFrom = $order_details->SearchFromDate;
                if($SearchFrom == '0000-00-00 00:00:00' || $SearchFrom == '')
                    {
                        $SearchFrmDate = '-';
                    }
                    else
                    {
                        $SearchFrmDate = date('m/d/Y', strtotime($SearchFrom));
                    }
                    $SearchAsOf = $order_details->SearchAsOfDate;
                    if($SearchAsOf == '0000-00-00 00:00:00' || $SearchAsOf == '')
                        {
                            $SearchAsDate = '-';
                        }
                        else
                        {
                            $SearchAsDate = date('m/d/Y', strtotime($SearchAsOf));
                        }
                        $DisclaimerNote = $this->Order_reports_model->get_DisclaimerNote($StateName);
                        $ZipCode = $order_details->PropertyZipcode;
                        $LastDeedRecordedDate = $order_details->LastDeedRecorded;
                        $LastDeedRecorded =  date('m/d/Y', strtotime($LastDeedRecordedDate));
                        $ImageUrl = base_url().'assets/img/sourcepoint.png';
                        $Mort = array('ProductName'=>$ProductName,'SubProductName'=>$SubProductName,'CustomerPContactMobileNo'=>$CustomerPContactMobileNo,'CustomerName'=>$CustomerNumber.$CustomerName,'CustomerNumber'=>$CustomerNo,'Ordernumber'=>$OrderNumber,'Loannumber'=>$LoanNumber,'CustomerStateCode'=>$CustomerStateCode,'CustomerCountyName'=>$CustomerCountyName,'CustomerCityName'=>$CustomerCityName,'Ownername'=>$OwnerName,'Countyname'=>$CountyName,'Propertyaddress1'=>$PropertyAddress1,'Propertyaddress2'=>$PropertyAddress2,'Cityname'=>$CityName,'Statecode'=>$StateCode,'Zip'=>$ZipCode,'APN'=>$APN,'CustomerAddress1'=>$CustomerAddress1,'CustomerAddress2'=>$CustomerAddress2,'CustomerPContactName'=>$CustomerPContactName,'Grantee'=>$Grantee,'LastDeedRecorded'=>$LastDeedRecorded,'DisclaimerNote'=>$DisclaimerNote,'SearchFromDate'=>$SearchFrmDate,'SearchAsOfDate'=>$SearchAsDate,'Borrower'=>$Borrowers,'Url'=>$base_url,'CustomerZipCode'=>$CustomerZipCode,'AttentionName'=>$AttentionName,'ImageUrl'=>$ImageUrl);
        //Heading
        //Chain of Title
                        if($this->Order_reports_model->get_torderdeeds($OrderUID))
                        {
                            $DeedHeading = ' <tr>
                            <td colspan="3" style="border:0.01em solid grey; border-left:0px; border-right:0px;">
                            <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>TITLE INFORMATION</b></p>
                            </td>
                            </tr>';
                            array_push($keys, '%%DeedHeading%%');
                            array_push($values, $DeedHeading);
                        }
                        else
                        {

                            array_push($keys, '%%DeedHeading%%');
                            array_push($values, ' ');
                        }
        //Chain of Title
        //Leins & Encumbrances
                        if($this->Order_reports_model->get_tordermortgageparties($OrderUID))
                        {
                            $MortgageHeading = '<tr><td colspan="4" style="border-bottom:0.01em solid grey;border-top:0.01em solid grey;">
                            <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>OPEN LIENS / ENCUMBRANCES / OTHER</b></p>
                            </td>
                            </tr>';
                            array_push($keys, '%%MortgageHeading%%');
                            array_push($values, $MortgageHeading);

                        }
                        else
                        {
                            array_push($keys, '%%MortgageHeading%%');
                            array_push($values, ' ');
                        }
        //Leins & Encumbrances 
        //Tax Heading
                        if($this->Order_reports_model->get_tordertaxcerts($OrderUID))
                        {
                            $TaxHeading = '<tr>
                            <td colspan="3" style="border-bottom:0.01px solid grey;">
                            <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>REAL ESTATE TAX</b></p>
                            </td>
                            </tr>';
                            array_push($keys, '%%TaxHeading%%');
                            array_push($values, $TaxHeading);
                        }
                        else
                        {
                            array_push($keys, '%%TaxHeading%%');
                            array_push($values, ' ');
                        }
        //Tax Heading

        //Judgement Heading
                        if($this->Order_reports_model->get_torderjudgements($OrderUID))
                        {
                            $JudgementHeading = '<tr>
                            <td colspan="3" style="border-bottom:0.01px solid grey;">
                            <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>OPEN LIENS / ENCUMBRANCES / OTHER</b></p>
                            </td>
                            </tr>';
                            array_push($keys, '%%JudgementHeading%%');
                            array_push($values, $JudgementHeading);
                        }
                        else
                        {
                            array_push($keys, '%%JudgementHeading%%');
                            array_push($values, ' ');
                        }
        //Judgement heading
        //Liens Heading
                        if($this->Order_reports_model->get_torderliens($OrderUID))
                        {
                            $LienHeading = '<tr>
                            <td colspan="4" style="border:0.01em solid grey;">
                            <p align="left" style="font-family:Arial; font-size: 8pt; padding:3pt;"><b>OPEN LIENS / ENCUMBRANCES / OTHER</b></p>
                            </td>
                            </tr>';
                            array_push($keys, '%%LienHeading%%');
                            array_push($values, $LienHeading);
                        }
                        else{
                            array_push($keys, '%%LienHeading%%');
                            array_push($values, ' ');
                        }
        //liens Heading
        //Heading

                        foreach ($Mort as $key => $value) 
                        {


                            array_push($keys, '%%'.$key.'%%');
                            array_push($values, $value);
                        }
        //Orders
        //Order Assessment
                        $order_assessment = $this->Order_reports_model->get_OrderAssessment($OrderUID);
                        if($order_assessment)
                        {

                            foreach ($order_assessment as $data_orderass_info) 
                            {
                                $AssessedYear = $data_orderass_info->AssessedYear;

                                if($AssessedYear)
                                {
                                    array_push($keys, '%%AssessedYear%%');
                                    array_push($values, $AssessedYear);
                                }
                                else{
                                    array_push($keys, '%%AssessedYear%%');
                                    array_push($values, '-');

                                }
                                $Landstr = $data_orderass_info->Land;
                                if($Landstr)
                                {
                                    $Landltrim = ltrim($Landstr, '$');
                                    $LandRepl = str_replace(",","",$Landltrim);
                                    $Lan = substr($LandRepl, 0,-3);
                                    $Land = '$'.number_format($Lan,2);
                                }
                                else{
                                    $Land = '-';
                                }
                                $Buildingsstr = $data_orderass_info->Buildings;
                                if($Buildingsstr)
                                {

                                    $Buildingsltrim = ltrim($Buildingsstr, '$');
                                    $BuildingsRepl = str_replace(",","",$Buildingsltrim);
                                    $Build = substr($BuildingsRepl, 0,-3);
                                    $Buildings = '$'.number_format($Build,2);
                                }
                                else{
                                    $Buildings = '-';
                                }


                                $Value = array('TotalValue'=>$TotalValue,'Buildings'=>$Buildings,'Land'=>$Land);
                                foreach ($Value as $key => $value) 
                                {
                                    array_push($keys, '%%'.$key.'%%');
                                    array_push($values, $value);
                                }
                            } 
                        }
                        else{


                            $TaxArray = array('AssessedYear','Land','Buildings','TotalValue');
                            foreach($TaxArray as $col)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, '-');
                            }

                        }
                        $output = str_replace($keys,$values, $output);
        //Order Assessment
        //Legal Description
                        $legal_information = $this->Order_reports_model->get_LegalDescription($OrderUID);
                        if($legal_information)
                        {
                            foreach ($legal_information as $dm) 
                            {
                                $LegalDescr = str_replace('  ', '&nbsp;', nl2br(strtoupper($dm->LegalDescription)));

                                if($LegalDescr)
                                {
                                    array_push($keys, '%%LegalDescr%%');
                                    array_push($values, $LegalDescr);
                                }
                                else{
                                    array_push($keys, '%%LegalDescr%%');
                                    array_push($values, ' ');
                                }
                                foreach($dm as $cm => $vm)
                                {
                                    array_push($keys, '%%'.$cm.'%%');
                                    array_push($values, $vm);
                                }
                            }
                        }
                        else{
                            array_push($keys, '%%LegalDescr%%');
                            array_push($values, ' ');
                        }
        //Legal Description
        //Get Borrowers
            $borrowers = $this->Order_reports_model->getBorrowers($OrderUID);
            $Count =  count($borrowers);
            if($Count > 1){

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    if($value->PRName !=''){

                    $borrower .=  trim($value->PRName.',');
                    }
                }
                $borrowers =  rtrim($borrower,",");
                $Array = explode(",", $borrowers);
                $LastArrayValue =  end($Array);
                
                if(count($Array) > 1 ){
                    array_pop($Array);
                    $BorrowerName = implode(", &nbsp;", $Array).' and '.$LastArrayValue;

                }else{
                    
                    $BorrowerName = implode(", &nbsp;", $Array);
                }
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
            else{

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    $borrower .=  $value->PRName.',';

                }
                $BorrowerName =  rtrim($borrower,",");
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
        //Get Borrowers
        //Property Starts


                        preg_match_all('/<div class=\"torderproperty\">(.*?)<\/div>/s',$output,$torderproperty);
        // $getCounts = $this->Order_reports_model->getCounts($OrderUID);
        // $MortgageCount= $this->Order_reports_model->getMortgageCount($OrderUID);
                        $MortgageCount =  $this->Order_reports_model->getMortgageCount($OrderUID);
if($MortgageCount == 0){ $MortgageCount = "NA"; }
                        array_push($keys, '%%MortgageCount%%');
                        array_push($values, $MortgageCount);
                        $LienCount =  $this->Order_reports_model->getLienCount($OrderUID);
if($LienCount == 0){ $LienCount = "NA"; }
                        array_push($keys, '%%LienCount%%');
                        array_push($values, $LienCount);
                        $JudgementCount =  $this->Order_reports_model->getJudgementCount($OrderUID);
if($JudgementCount == 0){ $JudgementCount = "NA"; }
                        array_push($keys, '%%JudgementCount%%');
                        array_push($values, $JudgementCount);
                        $GranteeGrantor =  $this->Order_reports_model->getGranteeGrantor($OrderUID);
                        if($GranteeGrantor)
                        {
                            foreach ($GranteeGrantor as $col => $value) 
                            {
                                $Grantee = $value->Grantee;
                                if($Grantee)
                                {
                                    array_push($keys, '%%Grantee%%');
                                    array_push($values, $Grantee);
                                }
                                else{
                                    array_push($keys, '%%Grantee%%');
                                    array_push($values, '-');
                                }
                                $Grantor = $value->Grantor;
                                if($Grantee){
                                    array_push($keys, '%%Grantor%%');
                                    array_push($values, $Grantor);
                                }
                                else{
                                    array_push($keys, '%%Grantor%%');
                                    array_push($values, '-');
                                }
                                $EstateInterestName = $value->EstateInterestName;
                                if($EstateInterestName)
                                {
                                    array_push($keys, '%%EstateInterestName%%');
                                    array_push($values, $EstateInterestName);
                                }
                                else{
                                    array_push($keys, '%%EstateInterestName%%');
                                    array_push($values, '-');
                                }

                            } 
                        }
                        else{
                            array_push($keys, '%%Grantee%%');
                            array_push($values, '-');
                            array_push($keys, '%%Grantor%%');
                            array_push($values, '-');
                            array_push($keys, '%%EstateInterestName%%');
                            array_push($values, '-');
                        }



                        $tax_information = $this->Order_reports_model->get_tax($OrderUID);
                        if($tax_information)
                        {
            //Property tax in ISGN Report
                            foreach ($tax_information as $data) 
                            {
                //DeliquentTax
                                if($data->TaxStatusName)
                                {
                                    if($data->TaxStatusName == 'Delinquent')
                                    {
                                        $DeliquentTax = array('DeliquentTax'=>'Yes');
                                        foreach($DeliquentTax as $col => $val)
                                        {
                                            array_push($keys, '%%'.$col.'%%');
                                            array_push($values, $val);
                                        }
                                    }
                                    else
                                    {
                                        $DeliquentTax = array('DeliquentTax'=>'No');
                                        foreach($DeliquentTax as $col => $val)
                                        {
                                            array_push($keys, '%%'.$col.'%%');
                                            array_push($values, $val);
                                        }
                                    }
                                }
                                else{
                                    array_push($keys, '%%DeliquentTax%%');
                                    array_push($values, '-');
                                }

                                $TaxYear = $data->TaxYear;
                                if($TaxYear)
                                {

                                    array_push($keys, '%%TaxYear%%');
                                    array_push($values, $TaxYear);
                                }
                                else
                                {
                                    array_push($keys, '%%TaxYear%%');
                                    array_push($values, '-');
                                }
                                $GrossAmount = $data->GrossAmount;
                                if($GrossAmount)
                                {
                                    $TaxYear = $data->TaxYear;
                                    if($TaxYear == date("Y"))
                                    {
                                        $PropertyTax = '$'.$GrossAmount;
                                        array_push($keys, '%%PropertyTax%%');
                                        array_push($values, $PropertyTax);
                                    }
                                    else
                                    {
                                        $PropertyTax = "$0.00";
                                        array_push($keys, '%%PropertyTax%%');
                                        array_push($values, $PropertyTax);
                                    }   
                                }
                                else
                                {
                                    array_push($keys, '%%PropertyTax%%');
                                    array_push($values, '-');
                                }

                            }
                        }
                        else
                        {
                            $TaxArray = array('TaxYear','PropertyTax','DeliquentTax');
                            foreach($TaxArray as $col)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, '-');
                            }
                        }

                        $property_information = $this->Order_reports_model->getPropertyInformation($OrderUID);
                        if($property_information)
                        {
                            foreach ($property_information as $key => $data) 
                            {
                                $MaritalStatusName = $data->MaritalStatusName;
                                if($MaritalStatusName)
                                {
                                    array_push($keys, '%%MaritalStatusName%%');
                                    array_push($values, $MaritalStatusName);
                                }
                                else{
                                    array_push($keys, '%%MaritalStatusName%%');
                                    array_push($values, '-');
                                }
                                $SubDivisionName = $data->SubDivisionName;
                                if($SubDivisionName)
                                {
                                    array_push($keys, '%%SubDivisionName%%');
                                    array_push($values, $SubDivisionName);
                                }
                                else{
                                    array_push($keys, '%%SubDivisionName%%');
                                    array_push($values, '-');
                                }
                                if($data->PropertyUseName =='Exempt' || $data->PropertyUseName == 'Tax Exempt' )
                                {
                                    $Exempt = array('Exempt'=>'Yes');
                                    foreach($Exempt as $col => $val)
                                    {
                                        array_push($keys, '%%'.$col.'%%');
                                        array_push($values, $val);
                                    }
                                }
                                else
                                {
                                    $Exempt = array('Exempt'=>'No');
                                    foreach($Exempt as $col => $val)
                                    {
                                        array_push($keys, '%%'.$col.'%%');
                                        array_push($values, $val);
                                    }
                                }
                                $sdMapNo = $data->sdMapNo;
                                if($sdMapNo)
                                {
                                    array_push($keys, '%%sdMapNo%%');
                                    array_push($values, $sdMapNo);
                                }
                                else{
                                    array_push($keys, '%%sdMapNo%%');
                                    array_push($values, '-');
                                }
                                $dSection = $data->dSection;
                                if($dSection)
                                {
                                    array_push($keys, '%%dSection%%');
                                    array_push($values, $dSection);
                                }
                                else{
                                    array_push($keys, '%%dSection%%');
                                    array_push($values, '-');
                                }
                                $Township = $data->Township;
                                if($Township)
                                {
                                    array_push($keys, '%%Township%%');
                                    array_push($values, $Township);
                                }
                                else{
                                    array_push($keys, '%%Township%%');
                                    array_push($values, '-');
                                }
                                $APN = $data->APN;
                                if($APN)
                                {
                                    array_push($keys, '%%APN%%');
                                    array_push($values, $APN);
                                }
                                else{
                                    array_push($keys, '%%APN%%');
                                    array_push($values, '-');
                                }
                                $Lot = $data->Lot;
                                if($Lot)
                                {
                                    array_push($keys, '%%Lot%%');
                                    array_push($values, $Lot);
                                }
                                else{
                                    array_push($keys, '%%Lot%%');
                                    array_push($values, '-');
                                }

                                $Block = $data->Block;
                                if($Block)
                                {
                                    array_push($keys, '%%Block%%');
                                    array_push($values, $Block);
                                }
                                else{
                                    array_push($keys, '%%Block%%');
                                    array_push($values, '-');
                                }
                            }
                        }
                        else
                        {
                            $TaxArray = array('Exempt','APN','Lot','Block','SubDivisionName','MaritalStatusName','sdMapNo','dSection','Township');
                            foreach($TaxArray as $col)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, ' ');
                            }
                        }
        //Property Ends
                        $output = str_replace($keys, $values, $output);
                        $doc->load($output);


        //Deed Starts
                        preg_match_all('/<div class=\"torderdeeds\">(.*?)<\/div>/s',$output,$torderdeeds);
                        $torderdeeds_table = '';
                        $torderdeeds_array = array();
                        $torderdeedsparties_array = array();
                        $torderdeeds_array = $this->Order_reports_model->get_torderdeeds($OrderUID);

                        $torderdeeds_array_count = count($torderdeeds_array);

                        for ($i=0; $i < $torderdeeds_array_count; $i++) 
                        { 

                            $torderdeeds_array[$i]->deed_increment = $i+1;
                            $keys = array();
                            $values = array();
                            foreach ($torderdeeds_array[$i] as $key => $value) 
                            {
                                //DocumentTypeName
                                $DocumentTypeName = $torderdeeds_array[$i]->DocumentTypeName;
                                if($DocumentTypeName)
                                {
                                    if($DocumentTypeName == 'Others')
                                    {
                                        $DeedType = $torderdeeds_array[$i]->DeedType;
                                        if($DeedType)
                                        {
                                            array_push($keys, '%%DeedDocumentTypeName%%');
                                            array_push($values, $DeedType);
                                        }
                                        else{
                                            array_push($keys, '%%DeedDocumentTypeName%%');
                                            array_push($values, '-');
                                        }
                                    }
                                    else if($DocumentTypeName !== 'Others')
                                    {
                                        array_push($keys, '%%DeedDocumentTypeName%%');
                                        array_push($values, $DocumentTypeName);
                                    }
                                    else
                                    {
                                        array_push($keys, '%%DeedDocumentTypeName%%');
                                        array_push($values, '-');
                                    }

                                }
                                else{
                                    array_push($keys, '%%DeedDocumentTypeName%%');
                                    array_push($values, '-');
                                }
                                //DocumentTypeName
                                // Grantee
                                $Grantee = $torderdeeds_array[$i]->Grantee;
                                if($Grantee)
                                {
                                    array_push($keys,'%%DeedGrantee%%');
                                    array_push($values, $Grantee);
                                }
                                else{
                                    array_push($keys,'%%DeedGrantee%%');
                                    array_push($values, '-');
                                }
                                // Grantee
                                // Grantor
                                $Grantor = $torderdeeds_array[$i]->Grantor;
                                if($Grantor)
                                {
                                    array_push($keys,'%%DeedGrantor%%');
                                    array_push($values, $Grantor);
                                }
                                else{
                                    array_push($keys,'%%DeedGrantor%%');
                                    array_push($values, '-');
                                }
                                // Grantor
                                //Deed Date Format Change
                                $DDated = $torderdeeds_array[$i]->DeedDated;
                                if($DDated == '0000-00-00')
                                {
                                    array_push($keys, '%%DeedDate%%');
                                    array_push($values, '-');
                                }
                                else{
                                    $DeedDated =  date('m/d/Y', strtotime($DDated));
                                    array_push($keys, '%%DeedDate%%');
                                    array_push($values, $DeedDated);
                                }
                                //Deed Date Format Change
                                //Recorded date Format Change
                                $RDated = $torderdeeds_array[$i]->DeedRecorded;
                                if($RDated == '0000-00-00')
                                {
                                    array_push($keys, '%%RecordedDate%%');
                                    array_push($values, '-');
                                }
                                else{

                                    $RecordedDated =  date('m/d/Y', strtotime($RDated));
                                    array_push($keys, '%%RecordedDate%%');
                                    array_push($values, $RecordedDated);

                                }
                                //Recorded date Format Change
                                //ConsiderationAmount
                                $ConsiderationAmount = $torderdeeds_array[$i]->ConsiderationAmount;
                                if($ConsiderationAmount)
                                {
                                    $ConsiderAmount = '$'.number_format($ConsiderationAmount,2);
                                    array_push($keys, '%%ConsiderAmount%%');
                                    array_push($values, $ConsiderAmount);
                                }
                                else{
                                    array_push($keys, '%%ConsiderAmount%%');
                                    array_push($values, '-');
                                }
                                //ConsiderationAmount

                                $Deed_DBVTypeUID_1 = $torderdeeds_array[$i]->Deed_DBVTypeUID_1;
                                $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_1);
                                $Deed_DBVTypeValue_1 = $torderdeeds_array[$i]->Deed_DBVTypeValue_1;
                                $Deed_DBVTypeUID_2 = $torderdeeds_array[$i]->Deed_DBVTypeUID_2;
                                $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_2);
                                $Deed_DBVTypeValue_2 = $torderdeeds_array[$i]->Deed_DBVTypeValue_2;
                                if($DBVTypeName_1)
                                {
                                    array_push($keys, '%%DBVTypeName_1%%');
                                    array_push($values, $DBVTypeName_1);
                                    if($Deed_DBVTypeValue_1)
                                    {
                                        array_push($keys, '%%Deed_DBVTypeValue_1%%');
                                        array_push($values, $Deed_DBVTypeValue_1);
                                    }
                                    else{
                                        array_push($keys, '%%Deed_DBVTypeValue_1%%');
                                        array_push($values, '-');
                                    }
                                }
                                else{
                                    array_push($keys, '%%DBVTypeName_1%%');
                                    array_push($values, 'Book/Page');
                                    array_push($keys, '%%Deed_DBVTypeValue_1%%');
                                    array_push($values, '-');
                                }
                                if($DBVTypeName_2)
                                {
                                    array_push($keys, '%%DBVTypeName_2%%');
                                    array_push($values, $DBVTypeName_2);
                                    if($Deed_DBVTypeValue_2)
                                    {
                                        array_push($keys, '%%Deed_DBVTypeValue_2%%');
                                        array_push($values, $Deed_DBVTypeValue_2);
                                    }
                                    else{
                                        array_push($keys, '%%Deed_DBVTypeValue_2%%');
                                        array_push($values, '-');
                                    }
                                }
                                else{

                                    array_push($keys, '%%DBVTypeName_2%%');
                                    array_push($values, 'Instrument');
                                    array_push($keys, '%%Deed_DBVTypeValue_2%%');
                                    array_push($values, '-');
                                }
                                //Deed Document
                                $DocumentNo = $torderdeeds_array[$i]->DocumentNo;
                                if($DocumentNo)
                                {
                                    array_push($keys, '%%DocumentNo%%');
                                    array_push($values, $DocumentNo);
                                }
                                else{
                                    array_push($keys, '%%DocumentNo%%');
                                    array_push($values, '-');
                                }
                                //Deed Document
                                //Certificate Number
                                $CertificateNo = $torderdeeds_array[$i]->CertificateNo;
                                if($CertificateNo)
                                {
                                    array_push($keys, '%%CertificateNo%%');
                                    array_push($values, $CertificateNo);
                                }
                                else{
                                    array_push($keys, '%%CertificateNo%%');
                                    array_push($values, '-');
                                }
                                //Certificate Number
                                //Instrument Number
                                $InstrumentNo = $torderdeeds_array[$i]->InstrumentNo;
                                if($InstrumentNo)
                                {
                                    array_push($keys, '%%InstrumentNo%%');
                                    array_push($values, $InstrumentNo);
                                }
                                else{
                                    array_push($keys, '%%InstrumentNo%%');
                                    array_push($values, '-');
                                }
                                //Instrument Number
                                //Deed Comments
                                $DeedComments = $torderdeeds_array[$i]->DeedComments;
                                if($DeedComments)
                                {
                                    array_push($keys, '%%DeedComments%%');
                                    array_push($values, $DeedComments);
                                }
                                else{
                                    array_push($keys, '%%DeedComments%%');
                                    array_push($values, '-');
                                }
                                //Deed Comments
                                //Main Loop
                                array_push($keys, '%%'.$key.'%%');
                                array_push($values, $value);
                                //Main Loop
                                //Latest Deed Grantee
                                $GranteeGrantor =  $this->Order_reports_model->getGranteeGrantor($OrderUID);
                                if($GranteeGrantor)
                                {
                                    foreach ($GranteeGrantor as $col => $value) 
                                    {
                                        $Grantee = $value->Grantee;
                                        if($Grantee)
                                        {

                                            array_push($keys, '%%LatestDeedGrantee%%');
                                            array_push($values, $Grantee);
                                        }
                                        else{

                                            array_push($keys, '%%LatestDeedGrantee%%');
                                            array_push($values, '-');
                                        }
                                    } 
                                }
                                else{
                                    array_push($keys, '%%LatestDeedGrantee%%');
                                    array_push($values, '-');
                                }
                            //Latest Deed Grantee
                            }

                            $output = str_replace($keys, $values, $output);
                            $torderdeeds_table .= str_replace($keys, $values, $torderdeeds[0][0]);

                        }
                        foreach ( $doc->find(".torderdeeds") as $node ) 
                        {
                            $node->innertext = $torderdeeds_table;
                        }
                        //Deed Ends
                        //Mortgages Starts
                        preg_match_all('/<div class=\"tordermortgages\">(.*?)<\/div>/s',$output,$tordermortgages);
                        $tordermortgages_table = '';
                        $tordermortgages_array = array();
                        $tordermortgagesparties_array = array();
                        $tordermortgages_array = $this->Order_reports_model->get_tordermortgageparties($OrderUID);

                        $tordermortgages_array_count = count($tordermortgages_array);

                        for ($i=0; $i < $tordermortgages_array_count; $i++) 
                        { 
                            $tordermortgages_array[$i]->mortgage_increment = $i+1;
                            $keys = array();
                            $values = array();
                            foreach ($tordermortgages_array[$i] as $key => $value) 
                            {
                                                    $DocumentTypeName = $tordermortgages_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $MortgageType = $tordermortgages_array[$i]->MortgageType;
                            if($MortgageType)
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, $MortgageType);
                            }
                            else
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%MortgageDocumentTypeName%%');
                        array_push($values, '-');
                    }
                    //DocumentTypeName
                                //Mortgage Date Format Change
                                $MortgageDated = $tordermortgages_array[$i]->MortgageDated;
                                if($MortgageDated == '0000-00-00'){


                                    array_push($keys, '%%MortgageDate%%');
                                    array_push($values,'-');
                                }
                                else{
                                    $MortgageDate =  date('m/d/Y', strtotime($MortgageDated));
                                    array_push($keys, '%%MortgageDate%%');
                                    array_push($values, $MortgageDate);
                                }
                //Mortgage Date Format Change
                //Mortgage Date Format Change
                                $MortgageRecorded = $tordermortgages_array[$i]->MortgageRecorded;
                                if($MortgageRecorded == '0000-00-00')
                                {
                                    array_push($keys, '%%MortgageRecordedDate%%');
                                    array_push($values, '-');
                                }
                                else{
                                    $MortgageRecordedDate =  date('m/d/Y', strtotime($MortgageRecorded));
                                    array_push($keys, '%%MortgageRecordedDate%%');
                                    array_push($values, $MortgageRecordedDate);
                                }
                //Mortgage Date Format Change
                //Mortgage Maturity Date Format
                                $MortgageMaturityDate = $tordermortgages_array[$i]->MortgageMaturityDate;
                                if($MortgageMaturityDate == '0000-00-00')
                                {
                                    array_push($keys, '%%MaturityDate%%');
                                    array_push($values, '-');
                                }
                                else{
                                    $MaturityDate =  date('m/d/Y', strtotime($MortgageMaturityDate));
                                    array_push($keys, '%%MaturityDate%%');
                                    array_push($values, $MaturityDate);
                                }
                //Mortgage Maturity Date Format
                //MortgageAmount
                                $MortgageAmount = $tordermortgages_array[$i]->MortgageAmount;
                                if($MortgageAmount)
                                {
                                    $MortAmt = '$'.number_format($MortgageAmount,2);
                                    array_push($keys, '%%MortgageAmount%%');
                                    array_push($values, $MortAmt);
                                }
                                else{
                                    array_push($keys, '%%MortgageAmount%%');
                                    array_push($values, '-');
                                }
                //MortgageAmount
                //Mortgagee
                                $Mortgagee = $tordermortgages_array[$i]->Mortgagee;
                                if($Mortgagee)
                                {
                                    array_push($keys, '%%Mortgagee%%');
                                    array_push($values, $Mortgagee);
                                }
                                else{
                                    array_push($keys, '%%Mortgagee%%');
                                    array_push($values, '-');
                                }
                //Mortgagee
                //Trustee
                                $Trustee1 = $tordermortgages_array[$i]->Trustee1;
                                $Trustee2 = $tordermortgages_array[$i]->Trustee2;
                                if($Trustee1 != '' && $Trustee2 != '')
                                {
                                    $Trustee = $Trustee1.','.$Trustee2;
                                    array_push($keys, '%%Trustee%%');
                                    array_push($values, $Trustee);
                                }
                                if($Trustee1 != '')
                                {
                                    $Trustee = $Trustee1;
                                    array_push($keys, '%%Trustee%%');
                                    array_push($values, $Trustee);
                                }
                                if($Trustee2 != ''){

                                    $Trustee = $Trustee2;
                                    array_push($keys, '%%Trustee%%');
                                    array_push($values, $Trustee);

                                }
                                else{

                                    $Trustee = '-';
                                    array_push($keys, '%%Trustee%%');
                                    array_push($values, $Trustee);
                                }
                //Trustee
                                // if($tordermortgages_array[$i]->LienTypeName =='Open Ended')
                                // {
                                //     $MTG = array('MTG'=>'Yes');
                                //     foreach($MTG as $col => $val)
                                //     {
                                //         array_push($keys, '%%'.$col.'%%');
                                //         array_push($values, $val);
                                //     }
                                // }
                                // else
                                // {
                                //     $MTG = array('MTG'=>'No');
                                //     foreach($MTG as $col => $val)
                                //     {
                                //         array_push($keys, '%%'.$col.'%%');
                                //         array_push($values, $val);
                                //     }
                                // }

                                //Closed/Open Ended
                                $OpenEnded = $tordermortgages_array[$i]->IsOpenEnded;
                                if($OpenEnded == '1')
                                {

                                        array_push($keys, '%%OpenEnded%%');
                                        array_push($values, 'Yes');
                                    
                                }
                                else
                                {

                                        array_push($keys, '%%OpenEnded%%');
                                        array_push($values,'-');
                                    
                                }
                //Closed/Open Ended
                //Book/Page
                                $Mortgage_DBVTypeUID_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_1;
                                $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_1);
                                $Mortgage_DBVTypeValue_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_1;
                                $Mortgage_DBVTypeUID_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_2;
                                $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_2);
                                $Mortgage_DBVTypeValue_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_2;
                                if($DBVTypeName_1)
                                {
                                    array_push($keys, '%%MortgageDBVTypeName_1%%');
                                    array_push($values, $DBVTypeName_1);
                                    if($Mortgage_DBVTypeValue_1)
                                    {
                                        array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                                        array_push($values, $Mortgage_DBVTypeValue_1);
                                    }
                                    else{
                                        array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                                        array_push($values, '-');
                                    }
                                }
                                else{
                                    array_push($keys, '%%MortgageDBVTypeName_1%%');
                                    array_push($values, 'Book/Page');
                                    array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                                    array_push($values, '-');
                                }
                                if($DBVTypeName_2)
                                {
                                    array_push($keys, '%%MortgageDBVTypeName_2%%');
                                    array_push($values, $DBVTypeName_2);
                                    if($Mortgage_DBVTypeValue_2)
                                    {
                                        array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                                        array_push($values, $Mortgage_DBVTypeValue_2);
                                    }
                                    else{
                                        array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                                        array_push($values, '-');
                                    }
                                }
                                else{

                                    array_push($keys, '%%MortgageDBVTypeName_2%%');
                                    array_push($values, 'Instrument');
                                    array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                                    array_push($values, '-');
                                }
                //Instrument
                                if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                                {
                                    $AppendInstrument = $Mortgage_DBVTypeValue_1.','.$Mortgage_DBVTypeValue_2;
                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, $AppendInstrument);

                                }
                                if($DBVTypeName_1 == 'Instrument'){
                                    $AppendInstrument = $Mortgage_DBVTypeValue_1;
                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, $AppendInstrument);

                                }
                                if($DBVTypeName_2 == 'Instrument'){
                                    $AppendInstrument = $Mortgage_DBVTypeValue_2;
                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, $AppendInstrument);
                                }
                                else{
                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, '-');
                                }
                //Instrument
                //Document
                                if($DBVTypeName_1 == 'Document' && $DBVTypeName_2 == 'Document')
                                {
                                    $AppendDocument = $Mortgage_DBVTypeValue_1.','.$Mortgage_DBVTypeValue_2;
                                    array_push($keys, '%%Document%%');
                                    array_push($values, $AppendDocument);

                                }
                                if($DBVTypeName_1 == 'Document'){
                                    $AppendDocument = $Mortgage_DBVTypeValue_1;
                                    array_push($keys, '%%Document%%');
                                    array_push($values, $AppendDocument);

                                }
                                if($DBVTypeName_2 == 'Document'){
                                    $AppendDocument = $Mortgage_DBVTypeValue_2;
                                    array_push($keys, '%%Document%%');
                                    array_push($values, $AppendDocument);
                                }
                                else{
                                    array_push($keys, '%%Document%%');
                                    array_push($values, '-');
                                }
                //Document
                //Mortgage Comments
                                $MortgageComments = $tordermortgages_array[$i]->MortgageComments;
                                if($MortgageComments)
                                {
                                    array_push($keys, '%%MortgageComments%%');
                                    array_push($values, $MortgageComments);
                                }
                                else{
                                    array_push($keys, '%%MortgageComments%%');
                                    array_push($values, '-');
                                }
                //Mortgage Comments
                //Main Loop
                                array_push($keys, '%%'.$key.'%%');
                                array_push($values, $value);
                //Main Loop

                            }

                            $tordermortgages_table.= str_replace($keys, $values, $tordermortgages[0][0]);
                        }

                        foreach ( $doc->find(".tordermortgages") as $node ) 
                        {
                            $node->innertext = $tordermortgages_table;
                        }
        //Mortgages Ends
        //Tax Starts

                        preg_match_all('/<div class=\"tordertaxcerts\">(.*?)<\/div>/s',$output,$tordertaxcerts);
                        $tordertaxcerts_table = '';
                        $tordertaxcerts_array = array();
                        $tordertaxcerts_array = $this->Order_reports_model->get_tordertaxcerts($OrderUID);
        // echo'<pre>';print_r($tordertaxcerts_array );
                        $tordertaxcerts_array_count = count($tordertaxcerts_array);
                        for ($i=0; $i < $tordertaxcerts_array_count; $i++) 
                        { 
                            $tordertaxcerts_array[$i]->tax_increment = $i+1;
                            $keys = array();
                            $values = array();
                            foreach ($tordertaxcerts_array[$i] as $key => $value) 
                            {

                //Total Value
                                $TotalValue = $tordertaxcerts_array[$i]->TotalValue;
                                if($TotalValue)
                                {
                                    $TotalValue = '$'.$TotalValue;
                                    array_push($keys, '%%TotalValue%%');
                                    array_push($values, $TotalValue);
                                }
                                else{
                                    array_push($keys, '%%TotalValue%%');
                                    array_push($values, '-');
                                }
                //Total Value

                //Agriculture
                                $Agriculture = $tordertaxcerts_array[$i]->Agriculture;
                                if($Agriculture)
                                {
                                    $Agriculture = '$'.$Agriculture;
                                    array_push($keys, '%%Agriculture%%');
                                    array_push($values, $Agriculture);
                                }
                                else{
                                    array_push($keys, '%%Agriculture%%');
                                    array_push($values, '-');
                                }
                //Agriculture
                //Amount Deliquent
                                $AmountDelinquent = $tordertaxcerts_array[$i]->AmountDelinquent;
                                $AmtDelinquent = '$'.number_format($AmountDelinquent,2);
                                if($AmtDelinquent)
                                {
                                    array_push($keys, '%%AmountDelinquent%%');
                                    array_push($values, $AmtDelinquent);
                                }
                                else{
                                    array_push($keys, '%%AmountDelinquent%%');
                                    array_push($values, '-');
                                }
                //Amount Deliquent
                //Tax Date Format Change
                                $GoodThroughDate = $tordertaxcerts_array[$i]->GoodThroughDate;
                                if($GoodThroughDate == '0000-00-00')
                                {
                                    array_push($keys, '%%ThroughDate%%');
                                    array_push($values, '-');
                                }
                                else{
                                    $ThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                                    array_push($keys, '%%ThroughDate%%');
                                    array_push($values, $ThroughDate);
                                }
                //Tax Date Format Change

                                //Tax Comments
                                $TaxComments = $tordertaxcerts_array[$i]->TaxComments;
                                if($TaxComments)
                                {
                                    array_push($keys, '%%TaxComments%%');
                                    array_push($values, $TaxComments);
                                    array_push($keys, '%%taxalignment%%');
                                    array_push($values, 'text-left');
                                }
                                else{
                                    array_push($keys, '%%TaxComments%%');
                                    array_push($values, '-');
                                    array_push($keys, '%%taxalignment%%');
                                    array_push($values, 'text-center');
                                }
                                //Tax Comments
                                if($tordertaxcerts_array[$i]->Certified =='0')
                                {
                                    $CertifiedStatus = array('CertifiedStatus'=>'unchecked.png');
                                    foreach($CertifiedStatus as $col => $val)
                                    {
                                        array_push($keys, '%%'.$col.'%%');
                                        array_push($values, $val);
                                    }
                                }
                                else
                                {
                                    $CertifiedStatus = array('CertifiedStatus'=>'checked.png');
                                    foreach($CertifiedStatus as $col => $val)
                                    {
                                        array_push($keys, '%%'.$col.'%%');
                                        array_push($values, $val);
                                    }
                                }

                                array_push($keys, '%%'.$key.'%%');
                                array_push($values, $value);
                            }

                            $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                            $TaxCertSNo = $this->Order_reports_model->getExemptionname($OrderUID,$TaxCert);
                            $Tax = explode(",", $TaxCertSNo);
                            if(in_array('Homestead', $Tax))
                            {

                                $ExmpStatus = array('ExmpStatus'=>'checked.png');
                                foreach($ExmpStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            else
                            {
                                $ExmpStatus = array('ExmpStatus'=>'unchecked.png');
                                foreach($ExmpStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            if(in_array('Agricultural', $Tax))
                            {
                                $ExmpStatus = array('AgriExmpStatus'=>'checked.png');
                                foreach($ExmpStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            else
                            {
                                $ExmpStatus = array('AgriExmpStatus'=>'unchecked.png');
                                foreach($ExmpStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            if(in_array('Rural', $Tax))
                            {

                                $ExmpStatus = array('RuralExmpStatus'=>'checked.png');
                                foreach($ExmpStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            else
                            {
                                $ExmpStatus = array('RuralExmpStatus'=>'unchecked.png');
                                foreach($ExmpStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            if(in_array('Urban', $Tax))
                            {
                                $ExmpStatus = array('UrbanExmpStatus'=>'checked.png');
                                foreach($ExmpStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                            else
                            {
                                $ExmpStatus = array('UrbanExmpStatus'=>'unchecked.png');
                                foreach($ExmpStatus as $col => $val)
                                {
                                    array_push($keys, '%%'.$col.'%%');
                                    array_push($values, $val);
                                }
                            }
                  $getlatesttaxinstallment = $this->Order_reports_model->getlatesttaxinstallment($OrderUID,$TaxCert);
                        if($getlatesttaxinstallment)
                        {
                                foreach ($getlatesttaxinstallment as $key => $value) 
                                {
                                        $TaxYears = $value->TaxYear;
                                        if($TaxYears)
                                        {
                                            array_push($keys, '%%TaxYears%%');
                                            array_push($values, $TaxYears);
                                        }
                                        else{
                                            array_push($keys, '%%TaxYears%%');
                                            array_push($values, '-');
                                        }
                                        //Tax Date Format Change
                                        $NextTaxDueDate = $value->DatePaid;
                                        if($NextTaxDueDate=='0000-00-00' || $NextTaxDueDate == '')
                                        {
                                            array_push($keys, '%%NextTaxDate%%');
                                            array_push($values, '-');
                                        }
                                        else{
                                            $NextTaxDate =  date('m/d/Y', strtotime($NextTaxDueDate));
                                            array_push($keys, '%%NextTaxDate%%');
                                            array_push($values, $NextTaxDate);
                                        }
                                        //Gross Amount
                                        $GrossAmount = $value->LatestGrossAmount;
                                        $GrossAmt = '$'.number_format($GrossAmount,2);
                                        if($GrossAmt)
                                        {
                                            array_push($keys, '%%LatestGrossAmount%%');
                                            array_push($values, $GrossAmt);
                                        }
                                        else{
                                            array_push($keys, '%%LatestGrossAmount%%');
                                            array_push($values, '-');
                                        }
                                        //Gross Amount
                                        //Amount Paid
                                        $AmountPaid = $value->AmountPaid;
                                        $AmtPaid = '$'.number_format($AmountPaid,2);
                                        if($AmtPaid)
                                        {
                                            array_push($keys, '%%AmtPaid%%');
                                            array_push($values, $AmtPaid);
                                        }
                                        else{
                                            array_push($keys, '%%AmtPaid%%');
                                            array_push($values, '-');
                                        }
                                        //Amount Paid
                                        //Tax Status Name
                                        $TaxStatusName = $value->TaxStatusName;
                                        if($TaxStatusName)
                                        {
                                            array_push($keys, '%%StatusName%%');
                                            array_push($values, $TaxStatusName);
                                        }
                                        else{
                                            array_push($keys, '%%StatusName%%');
                                            array_push($values, '-');
                                        }
                                        //Tax Status Name
                                        //Tax Date Format Change
                                }
                        }
                        else{

                            $data = array('TaxYears','NextTaxDate','LatestGrossAmount','AmtPaid','StatusName');
                            foreach ($data as $value) {
                                array_push($keys, '%%'.$value.'%%');
                                array_push($values, '-');
                            }
                        }

                            $output = str_replace($keys, $values, $output);
                            $tordertaxcerts_table .= str_replace($keys, $values, $tordertaxcerts[0][0]);

                        }
                        foreach ( $doc->find(".tordertaxcerts") as $node ) 
                        {
                            $node->innertext = $tordertaxcerts_table;
                        }
        //Tax Ends
        //Liens Starts
                        preg_match_all('/<div class=\"torderliens\">(.*?)<\/div>/s',$output,$torderliens);
                        $torderliens_table = '';
                        $torderliens_array = array();
                        $torderliens_array = $this->Order_reports_model->get_torderliens($OrderUID);
                        $torderliens_array_count = count($torderliens_array);
                        for ($i=0; $i < $torderliens_array_count; $i++) 
                        { 

                            $torderliens_array[$i]->lien_increment = $i+1;

                            $keys = array();
                            $values = array();
                            foreach ($torderliens_array[$i] as $key => $value) 
                            {
                //DocumentTypeName
                    $DocumentTypeName = $torderliens_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $LeinType = $torderliens_array[$i]->LeinType;
                            if($LeinType)
                            {
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, $LeinType);
                            }
                            else{
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%LeinDocumentTypeName%%');
                        array_push($values, '-');
                    }

                //DocumentTypeName
                //Lein Date Format Change
                                $LDated = $torderliens_array[$i]->LeinDated;
                                if($LDated =='0000-00-00')
                                {
                                    array_push($keys, '%%LeinDate%%');
                                    array_push($values, '-');
                                }
                                else{
                                    $Dated =  date('m/d/Y', strtotime($LDated));
                                    array_push($keys, '%%LeinDate%%');
                                    array_push($values, $Dated);
                                }
                //Lein Date Format Change
                //Lein Recorded date Format Change
                                $RDated = $torderliens_array[$i]->LeinRecorded;
                                if($RDated == '0000-00-00')
                                {
                                    array_push($keys, '%%LeinRecord%%');
                                    array_push($values, '-');
                                }
                                else{
                                    $RecordedDated =  date('m/d/Y', strtotime($RDated));
                                    array_push($keys, '%%LeinRecord%%');
                                    array_push($values, $RecordedDated);
                                }
                //Lein date Format Change
                //Lein Amount
                                $LeinAmount = $torderliens_array[$i]->LeinAmount;
                                if($LeinAmount)
                                {
                                    $MortAmt = '$'.number_format($LeinAmount,2);
                                    array_push($keys, '%%LeinAmt%%');
                                    array_push($values, $MortAmt);
                                }
                                else{
                                    array_push($keys, '%%LeinAmt%%');
                                    array_push($values, '-');
                                }
                //Lein Amount
                                if($torderliens_array[$i]->LienTypeName =='Closed Ended')
                                {
                                    $MTG = array('MTG'=>'N/A');
                                    foreach($MTG as $col => $val)
                                    {
                                        array_push($keys, '%%'.$col.'%%');
                                        array_push($values, $val);
                                    }
                                }
                                if($torderliens_array[$i]->LienTypeName =='Open Ended')
                                {
                                    $MTG = array('MTG'=>'Yes');
                                    foreach($MTG as $col => $val)
                                    {
                                        array_push($keys, '%%'.$col.'%%');
                                        array_push($values, $val);
                                    }
                                }
                                else
                                {
                                    $MTG = array('MTG'=>'No');
                                    foreach($MTG as $col => $val)
                                    {
                                        array_push($keys, '%%'.$col.'%%');
                                        array_push($values, $val);
                                    }
                                }
                                $Lien_DBVTypeUID_1 = $torderliens_array[$i]->Lien_DBVTypeUID_1;
                                $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_1);
                                $Lien_DBVTypeValue_1 = $torderliens_array[$i]->Lien_DBVTypeValue_1;
                                $Lien_DBVTypeUID_2 = $torderliens_array[$i]->Lien_DBVTypeUID_2;
                                $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_2);
                                $Lien_DBVTypeValue_2 = $torderliens_array[$i]->Lien_DBVTypeValue_2;
                                if($DBVTypeName_1)
                                {
                                    array_push($keys, '%%LienDBVTypeName_1%%');
                                    array_push($values, $DBVTypeName_1);
                                    if($Lien_DBVTypeValue_1)
                                    {
                                        array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                        array_push($values, $Lien_DBVTypeValue_1);
                                    }
                                    else{
                                        array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                        array_push($values, '-');
                                    }
                                }
                                else{
                                    array_push($keys, '%%LienDBVTypeName_1%%');
                                    array_push($values, 'Book/Page');
                                    array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                    array_push($values, '-');
                                }
                                if($DBVTypeName_2)
                                {
                                    array_push($keys, '%%LienDBVTypeName_2%%');
                                    array_push($values, $DBVTypeName_2);
                                    if($Lien_DBVTypeValue_2)
                                    {
                                        array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                        array_push($values, $Lien_DBVTypeValue_2);
                                    }
                                    else{
                                        array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                        array_push($values, '-');
                                    }
                                }
                                else{

                                    array_push($keys, '%%LienDBVTypeName_2%%');
                                    array_push($values, 'Instrument');
                                    array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                    array_push($values, '-');
                                }
                //CaseNumber
                                if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                                {
                                    $AppendCasenumber = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                                    array_push($keys, '%%CaseNumber%%');
                                    array_push($values, $AppendCasenumber);

                                }
                                if($DBVTypeName_1 == 'Case number'){
                                    $AppendCasenumber = $Lien_DBVTypeValue_1;
                                    array_push($keys, '%%CaseNumber%%');
                                    array_push($values, $AppendCasenumber);

                                }
                                if($DBVTypeName_2 == 'Case number'){
                                    $AppendCasenumber = $Lien_DBVTypeValue_2;
                                    array_push($keys, '%%CaseNumber%%');
                                    array_push($values, $AppendCasenumber);
                                }
                                else{
                                    array_push($keys, '%%CaseNumber%%');
                                    array_push($values, '-');
                                }
                //CaseNumber
                //Instrument
                                if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                                {
                                    $AppendInstrument = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, $AppendInstrument);

                                }
                                if($DBVTypeName_1 == 'Instrument'){
                                    $AppendInstrument = $Lien_DBVTypeValue_1;
                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, $AppendInstrument);

                                }
                                if($DBVTypeName_2 == 'Instrument'){
                                    $AppendInstrument = $Lien_DBVTypeValue_2;
                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, $AppendInstrument);
                                }
                                else{

                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, '-');
                                }
                //Instrument
                //Lein Comments
                                $LeinComments = $torderliens_array[$i]->LeinComments;
                                if($LeinComments)
                                {
                                    array_push($keys, '%%LeinComments%%');
                                    array_push($values, $LeinComments);
                                }
                                else{
                                    array_push($keys, '%%LeinComments%%');
                                    array_push($values, '-');
                                }
                //Lein Comments

                                $Holder = $torderliens_array[$i]->Holder;
                                if($Holder)
                                {
                                    array_push($keys, '%%LeinHolder%%');
                                    array_push($values, $Holder);
                                }
                                else{
                                    array_push($keys, '%%LeinHolder%%');
                                    array_push($values, '-');
                                }

                                array_push($keys, '%%'.$key.'%%');
                                array_push($values, $value);
                            }
                            $output = str_replace($keys, $values, $output);

                            $torderliens_table .= str_replace($keys, $values, $torderliens[0][0]);

                        }
                        foreach ( $doc->find(".torderliens") as $node ) 
                        {
                            $node->innertext = $torderliens_table;
                        }
        //Leins Ends
        //Judgement Starts
                        preg_match_all('/<div class=\"torderjudgments\">(.*?)<\/div>/s',$output,$torderjudgments);
                        $torderjudgments_table = '';
                        $torderjudgments_array = array();
                        $torderjudgments_array = $this->Order_reports_model->get_torderjudgements($OrderUID);
                        $torderjudgments_array_count = count($torderjudgments_array);
                        for ($i=0; $i < $torderjudgments_array_count; $i++)
                        { 
                            $torderjudgments_array[$i]->judgement_increment = $i+1;
                            $keys = array();
                            $values = array();
                            foreach ($torderjudgments_array[$i] as $key => $value)
                            {
                //Plaintiff
                                $Plaintiff = $torderjudgments_array[$i]->Plaintiff;
                                if($Plaintiff)
                                {
                                    array_push($keys, '%%Plaintiff%%');
                                    array_push($values, $Plaintiff);
                                }
                                else{
                                    array_push($keys, '%%Plaintiff%%');
                                    array_push($values, '-');
                                }
                //Plaintiff
                //Defendant
                                $Defendent = $torderjudgments_array[$i]->Defendent;
                                if($Defendent)
                                {
                                    array_push($keys, '%%Defendent%%');
                                    array_push($values, $Defendent);
                                }
                                else{
                                    array_push($keys, '%%Defendent%%');
                                    array_push($values, '-');
                                }
                //Defendant
                //DocumentTypeName
                    $DocumentTypeName = $torderjudgments_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $JudgementType = $torderjudgments_array[$i]->JudgementType;
                            if($JudgementType)
                            {
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, $JudgementType);
                            }
                            else{
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%JudgementDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //DocumentTypeName
                //Judgement Amount
                                $JudgementAmount = $torderjudgments_array[$i]->JudgementAmount;
                                $JudAmt = '$'.number_format($JudgementAmount,2);
                                array_push($keys, '%%JudgementAmount%%');
                                array_push($values, $JudAmt);
                //Judgement Amount
                //Judgement Date Format Change
                                $JDated = $torderjudgments_array[$i]->JudgementDated;
                                if($JDated == '0000-00-00')
                                {
                                    array_push($keys, '%%JudgeDated%%');
                                    array_push($values, '-');
                                }
                                else{
                                    $Dated =  date('m/d/Y', strtotime($JDated));
                                    array_push($keys, '%%JudgeDated%%');
                                    array_push($values, $Dated);
                                }
                //Judgement Date Format Change
                //Judgement Recorded date Format Change
                                $RDated = $torderjudgments_array[$i]->JudgementRecorded;
                                if($RDated == '0000-00-00')
                                {   
                                    array_push($keys, '%%JudgeRecorded%%');
                                    array_push($values, '-');
                                }
                                else{
                                    $RecordedDated =  date('m/d/Y', strtotime($RDated));
                                    array_push($keys, '%%JudgeRecorded%%');
                                    array_push($values, $RecordedDated);
                                }
                // JudgementRecorded date Format Change
                                $Judgement_DBVTypeUID_1 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_1;
                                $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_1);
                                $Judgement_DBVTypeValue_1 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_1;
                                $Judgement_DBVTypeUID_2 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_2;
                                $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_2);
                                $Judgement_DBVTypeValue_2 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_2;
                                if($DBVTypeName_1)
                                {
                                    array_push($keys, '%%JudgementDBVTypeName_1%%');
                                    array_push($values, $DBVTypeName_1);
                                    if($Judgement_DBVTypeValue_1)
                                    {
                                        array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                        array_push($values, $Judgement_DBVTypeValue_1);
                                    }
                                    else{
                                        array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                        array_push($values, '-');
                                    }
                                }
                                else{
                                    array_push($keys, '%%JudgementDBVTypeName_1%%');
                                    array_push($values, 'Book/Page');
                                    array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                    array_push($values, '-');
                                }
                                if($DBVTypeName_2)
                                {
                                    array_push($keys, '%%JudgementDBVTypeName_2%%');
                                    array_push($values, $DBVTypeName_2);
                                    if($Judgement_DBVTypeValue_2)
                                    {
                                        array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                        array_push($values, $Judgement_DBVTypeValue_2);
                                    }
                                    else{
                                        array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                        array_push($values, '-');
                                    }
                                }
                                else{

                                    array_push($keys, '%%JudgementDBVTypeName_2%%');
                                    array_push($values, 'Instrument');
                                    array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                    array_push($values, '-');
                                }
                //Instrument
                                if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                                {
                                    $AppendInstrument = $Judgement_DBVTypeValue_1.','.$Judgement_DBVTypeValue_2;
                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, $AppendInstrument);

                                }
                                if($DBVTypeName_1 == 'Instrument'){
                                    $AppendInstrument = $Judgement_DBVTypeValue_1;
                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, $AppendInstrument);

                                }
                                if($DBVTypeName_2 == 'Instrument'){
                                    $AppendInstrument = $Judgement_DBVTypeValue_2;
                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, $AppendInstrument);
                                }
                                else{
                                    array_push($keys, '%%InstrumentNo_1%%');
                                    array_push($values, '-');
                                }
                //Instrument

                //CaseNumber
                                $JudgementCaseNo = $torderjudgments_array[$i]->JudgementCaseNo;
                                if($JudgementCaseNo)
                                {
                                    array_push($keys, '%%Casenumber%%');
                                    array_push($values, $JudgementCaseNo);
                                }
                                else{
                                    if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                                    {
                                        $AppendCasenumber = $Judgement_DBVTypeValue_1.','.$Judgement_DBVTypeValue_2;
                                        array_push($keys, '%%Casenumber%%');
                                        array_push($values, $AppendCasenumber);

                                    }
                                    if($DBVTypeName_1 == 'Case number'){
                                        $AppendCasenumber = $Judgement_DBVTypeValue_1;
                                        array_push($keys, '%%Casenumber%%');
                                        array_push($values, $AppendCasenumber);

                                    }
                                    if($DBVTypeName_2 == 'Case number'){
                                        $AppendCasenumber = $Judgement_DBVTypeValue_2;
                                        array_push($keys, '%%Casenumber%%');
                                        array_push($values, $AppendCasenumber);
                                    }
                                    else{
                                        array_push($keys, '%%Casenumber%%');
                                        array_push($values, '-');
                                    }
                                }
                //CaseNumber
                //Judgements Comments
                                $JudgementComments = $torderjudgments_array[$i]->JudgementComments;
                                if($JudgementComments)
                                {
                                    array_push($keys, '%%JudgementComments%%');
                                    array_push($values, $JudgementComments);
                                }
                                else{
                                    array_push($keys, '%%JudgementComments%%');
                                    array_push($values, '-');
                                }
                //Judgements Comments
                                array_push($keys, '%%'.$key.'%%');
                                array_push($values, $value);
                            }
                            $torderjudgments_table .= str_replace($keys, $values, $torderjudgments[0][0]);
                        }
                        foreach ( $doc->find(".torderjudgments") as $node ) 
                        {
                            $node->innertext = $torderjudgments_table;
                        }
        //Judgments Ends
                        return $doc;
                    }

                    function export_repdort()
                    {
        // reference the Dompdf namespace
        //$data = $this->input->post('tinyymce');

                        $this->load->library('pdf');
                        $data = file_get_contents( FCPATH.'/uploads/Templates/isgn_report.html' ) ;
        // $this->pdf->load_html_file($data);
                        $this->pdf->load_html($data);

        //$customPaper = array(0,0,1000,5000);
                        $this->pdf->set_paper($customPaper);
        // $this->pdf->set_paper(DEFAULT_PDF_PAPER_SIZE, 'portrait');
        // $this->pdf->set_Paper(array(0,0,612.00, 1008.22));
                        $this->pdf->render();
                        $doc_save = 'report.pdf';
                        file_put_contents($doc_save, $this->pdf->output()); 
                        ob_get_clean();
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/force-download');
                        header("Content-Disposition: attachment; filename=" . basename($doc_save));
                        header('Content-Transfer-Encoding: binary');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                        header('Pragma: public');
                        header("Content-Length: " . filesize($doc_save));
                        flush();
                        readfile($doc_save);
                        @unlink($doc_save);
                        exit;
                    }

                    function export_report()
                    {


                        $this->load->library('pdf');
        //$data = $this->input->post('tinyymce');
        //echo "<script>console.log(".$data.")</script>";
        //exit;
                        $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        // ob_start();
        //$data = file_get_contents( FCPATH.'/uploads/Templates/isgn_report.php' ) ;
        // ob_get_clean();
        // $data = $this->load->view('isgn', $data, true); // render the view into HTML
        // $this->load->library('pdf');
                        $pdf = $this->pdf->load($param);
                        $pdf->SetProtection(array('copy','print','modify','annot-forms','fill-forms','extract','assemble','print-highres'));
        //$pdf->SetDisplayMode('fullpage');

        // $pdf->SetDisplayMode('fullpage');
                        $pdf->list_indent_first_level = 0;  

        // $pdf->SetFooter($_SERVER['HTTP_HOST'].'|{PAGENO}|'.date(DATE_RFC822)); // Add a footer for good measure ;)
        //$this->isgn_property_report();
                        $data = file_get_contents('uploads/Templates/isgn_report.html');
        $pdf->WriteHTML($data); // write the HTML into the PDF


        $doc_save = 'report.pdf';

        return $pdf->Output($doc_save, 'D'); // save to file because we can
    }

// Uploading Files in torderdocuments tables




    function Document_upload()
    { 
        try
        {

            $OrderUID = $this->input->post('OrderUID');
            $data['OrderUID'] = $this->input->post('OrderUID');
            $data['DocumentTypeUID'] = $this->input->post('documenttype'); 
            $date = date('Ymd');

            $torders = $this->Order_reports_model->GetOrderUID($OrderUID);

            $OrderDocs_Path = $torders->OrderDocsPath;

    //Check Order Docuemnt Path exist.
            if(empty($OrderDocs_Path))
            {

                $query = $this->db->query("update torders SET OrderDocsPath ='uploads/searchdocs/".$date."/".$data['OrderUID']."/"."' Where OrderUID=".$data['OrderUID']);

                $OrderDocs_Path = base_url() . 'uploads/searchdocs/'.$date.'/'.$data['OrderUID']."/";
            }

            $count_image = count($_FILES);

            $count = $count_image - 1;

            for($i=0; $i < $count; $i++)
            { 
                $image = $_FILES['image'.$i];

                echo '<pre>';print_r($image);

      //echo '<pre>';print_r($image);exit;

                $data['DocumentFileName'] = $image['name'];
                $image_tmp_name = $image['tmp_name'];

                if (!is_dir($OrderDocs_Path)) 
                {
                    mkdir($OrderDocs_Path, 0777, true);
                }  
                if(move_uploaded_file($image_tmp_name, $OrderDocs_Path.$data['DocumentFileName']))
                {
        //print_r($data);
                    $data['TypeOfDocument'] = $data['DocumentTypeUID']; 
                    $data['DocumentCreatedDate'] = date('Y-m-d'); 
                    $retdata = $this->Order_reports_model->StoreDocuments($data);
                    if(isset($retdata['errormsg']))
                    {
                        echo json_encode($retdata['errormsg']);
                    }

                }
            }
        }
        catch(Exception $e)
        {
            echo json_encode($e->getMessage());
        }
    }

    function ordersearch_upload_update()
    {

        $data['OrderUID'] = $this->input->post('OrderUID');
        $data['SearchModeUID'] = $this->input->post('SiteUID');
        $data['DocumentTypeUID'] = $this->input->post('DocumentTypeUID');  
        $data['DocumentFileName'] = $this->input->post('Filename');
        $data['Position'] = $this->input->post('Position');

        $passingData = array();

        $passingData["OrderUID"] = $data['OrderUID'];
        $passingData["SearchModeUID"] = $data['SearchModeUID'];

        for($i=0; $i<=count($data['DocumentFileName'])-1; $i++)
        {
            $passingData["DocumentFileName"] = $data["DocumentFileName"][$i];
            $passingData["Position"] = $data["Position"][$i];
            $passingData["DocumentTypeUID"] = $data["DocumentTypeUID"][$i];
            $response = $this->Order_reports_model->UpdatePosition($passingData);

            if($response==false)
            {
                return false;
            }
        }
    }

    function checkFileExistForOrderUID()
    { 
        $data['OrderUID'] = $this->input->post('OrderUID');
        $data['DocumentFileName'] = $this->input->post('DocumentFileName');

        $retvalue = $this->Order_reports_model->gettordersdocumentsRowCount($data);
        echo json_encode($retvalue);

    }


    function megre_PDF($OrderUID)
    {

        $data['OrderUID'] = $OrderUID;
        $pdf = new PDFMerger;
        $getPDF = $this->Order_reports_model->GetPDFdocuments($data);
        $torders = $this->Order_reports_model->Gettorders($data);


        if(count($getPDF>0))
        {
            $docs_files=array();
            foreach ($getPDF as $file)
            {
                $pdf->addPDF(FCPATH. $torders['OrderDocsPath']. $file->DocumentFileName, 'all');
            }

      /*  if($pdf->merge('file', FCPATH. $torders['OrderDocsPath'] .$torders['OrderNumber'].'_report.pdf')){

            echo '<pre>';print_r('completre');exit;
        }*/

        $pdf->merge('file', FCPATH. $torders['OrderDocsPath'] .$torders['OrderNumber'].'_Search.pdf');

        redirect(base_url('reports'));

        /*if(file_exists(FCPATH. $torders['OrderDocsPath'] .$torders['OrderNumber'].'_Search.pdf'))
        {
            $files = FCPATH. $torders['OrderDocsPath'] .$torders['OrderNumber'].'_Search.pdf';
            $zipname = $torders['OrderNumber'].'_Search.zip';
            $zip = new ZipArchive;
            $zip->open($zipname, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE);
            $new_filename = substr($files,strrpos($files,'/') + 1);
            $zip->addFile($files,$new_filename);

                  //$zip->addFile($file);
            $zip->close();


            ob_get_clean();
            // header('Content-Description: File Transfer');
            // header('Content-Type: application/force-download');
            // header("Content-Disposition: attachment; filename=" . basename($zipname));
            // header('Content-Transfer-Encoding: binary');
            // header('Expires: 0');
            // header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            // header('Pragma: public');
            // header("Content-Length: " . filesize($zipname));
            // flush();
            // readfile($zipname);
            // @unlink($zipname);

            //Change STatus
            $OrderUID =$this->input->post('OrderUID');
            $ExceptionRaised = $this->config->item('keywords')['Exception Raised'];
            $Complete = $this->config->item('keywords')['Complete'];
            $OrderProgress = $this->config->item('keywords')['Order Work In Progress'];
            $WorkflowModule = $this->config->item('WorkflowModule')['OrderSearch'];


            $Workflowstatus = $this->Common_model->GetWorkflowstatusByID($OrderUID);
            $Workflowstatus = $Workflowstatus->Workflowstatus;

            $TotalWorkflow = $this->Common_model->GetTotalWorkflowByID($OrderUID);
            $TotalWorkflow = $TotalWorkflow->TotalWorkflow;

            if($TotalWorkflow == $Workflowstatus )
            {
             $data = array("StatusUID"=>$Complete);
           }

           else
           {
            $data = array("StatusUID"=>$OrderProgress);
          }

          $workstatus = array("WorkflowStatus"=>1);


          $this->db->where(array("torderassignment.OrderUID"=>$OrderUID,"torderassignment.WorkflowModuleUID"=>$WorkflowModule));
          $this->db->update('torderassignment',$workstatus);

          // $this->db->where(array("torders.OrderUID"=>$OrderUID));
          // $this->db->update('torders',$data);


          redirect(base_url('reports'));

      }*/
  }

}

function AddAttachment()
{
    $OrderUID = $_POST['OrderUID'];
    $DocumentFileName = $this->input->post('documentfilename');

    //Trans Begin
    $this->db->trans_begin();

    $this->db->order_by('Id', 'ASC');
    $query=$this->db->get_where('torderpropertyroles', array('PropertyRoleUID'=>5,'OrderUID'=>$OrderUID));
    $troderpropertyroles=$query->row();

    $torders = $this->Order_reports_model->Gettorders(array('OrderUID'=>$OrderUID));


    if($troderpropertyroles->PRName!='')
    {
        $DocumentFileName = $torders['LoanNumber'].'-'.$troderpropertyroles->PRName;        
    }
    else
    {
        $DocumentFileName = $torders['LoanNumber'];
    }

    $DocumentFileName = str_replace(array('[',']','/', '\\',':','*','?','"','<','>','|',' '), '-',$DocumentFileName);
    $DocumentTypeUID = $this->common_model->getDocumentTypeUIDByDocTYpe('Final Reports');

    $data['OrderUID'] = $OrderUID;
    $data['DocumentFileName'] = $DocumentFileName . '.pdf';

    $this->db->where('OrderUID', $data['OrderUID']);
    $this->db->like('DocumentFileName', $DocumentFileName);
    $query=$this->db->get('torderdocuments');
    $reportfilenamecount=$query->num_rows();


    if($reportfilenamecount>0)
    {
        $data['DocumentFileName'] = $this->common_model->GetAvailFileName($DocumentFileName,'.pdf',$reportfilenamecount, $OrderUID);
    }

    $this->db->where('DocumentTypeName','Final Reports');
    $DocunmentType = $this->db->get('mdocumenttypes')->row();
    if(!empty($DocunmentType)){
        $data['DocumentTypeUID'] = $DocunmentType->DocumentTypeUID;
    }else{
        $data['DocumentTypeUID'] = 1; // any
    }
    $data['IsReport'] = 1;
    $data['TypeOfDocument'] = $DocumentTypeUID;
    $data['DocumentTypeUID'] = $DocumentTypeUID;
    $data['DocumentCreatedDate'] = date('Y-m-d H:i:s');
    $data['UploadedUserUID'] =  $this->loggedid;
    $data['DisplayFileName'] =  $data['DocumentFileName'];

    $this->load->model('order_search/ordersearch_model');
    //Default Current DateTime
    $checkdates= $this->ordersearch_model->CheckSearchDateExists($data['OrderUID']);
    $checkdates = $checkdates->CheckDates;

    if ($checkdates == 0) {
        $data['SearchAsOfDate'] = "0000-00-00 00:00:00";
        $data['SearchFromDate'] = "0000-00-00 00:00:00";
    }
    else
    {
        $this->db->select('MAX(SearchAsOfDate) AS SearchAsOfDate, MAX(SearchFromDate) AS SearchFromDate', false)->from('torderdocuments');
        $this->db->where('OrderUID',$data['OrderUID']);
        $result=$this->db->get();
        $torderdocuments=$result->row();

        $data['SearchAsOfDate']=$torderdocuments->SearchAsOfDate;
        $data['SearchFromDate']=$torderdocuments->SearchFromDate;
    }


    $result = $this->db->insert('torderdocuments', $data);

    $date = date('Y-m-d');

    $order_details = $this->Order_reports_model->get_torders($OrderUID);
    $TaxCertificateRequired = $order_details->TaxCertificateRequired;


    $torders = $this->Order_reports_model->Gettorders($data);
    $OrderDocs_Path = $torders['OrderDocsPath']; 
    // $sourcefile = FCPATH . 'Templates/Pdf/'.$torders['OrderNumber'].'.pdf';
    
    /* @author Parthasarathy M Changed for Release 3 Unable to insert report into attachment issue*/
    if($TaxCertificateRequired =='No Tax Certificate')
    {
        $sourcefile = FCPATH . 'Templates/Pdf/FinalTemplates/'.$torders['OrderNumber'].'.pdf';
        
    }
    else{
        $sourcefile = FCPATH . 'Templates/Pdf/'.$torders['OrderNumber'].'.pdf';
        
    }

    if (!file_exists($sourcefile)) {
        $this->db->trans_rollback();
        $response['status'] = "Failed";
        $response['message'] = "Unable to Insert in to attachment";
        $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response))
                ->_display();
                 exit;

    }

    //Check Order Docuemnt Path exist.
    if(empty($OrderDocs_Path))
    {

        $query = $this->db->query("update torders SET OrderDocsPath ='uploads/searchdocs/".$date."/".$torders['OrderNumber']."/"."' Where OrderUID=".$data['OrderUID']);

        $OrderDocs_Path = 'uploads/searchdocs/'.$date.'/'.$torders['OrderNumber']."/";
    }
    $destinationfile = FCPATH . $OrderDocs_Path . $data['DocumentFileName'];

    if (!is_dir(FCPATH . $OrderDocs_Path)) {
        mkdir(FCPATH .$OrderDocs_Path, 0777, true);
    }  
    $source = file_get_contents($sourcefile);
    if(file_put_contents($destinationfile ,$source) && $this->db->trans_status()===TRUE)
    {
        $response['status'] = "Ok";
        $response['message'] = "Report Inserted into attachment";
        $this->db->trans_commit();
    }
    else
    {
        $response['status'] = "Failed";
        $response['message'] = "Unable to Insert in to attachment";
        $this->db->trans_rollback();
    }
    $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));

}

function TaxCert_to_Attachment()
{
    error_reporting(0);
    $this->load->library('Dom/Simple_html_dom');
    $doc = new simple_html_dom();
    $OrderUID = $this->input->post('OrderUID');
    $order_details = $this->Order_reports_model->get_torders($OrderUID);
    $StateCode = $order_details->StateCode;

    if($StateCode == 'TX') {
        $filename = FCPATH.'Templates/taxcert_tx.php';
    } else {
        $filename = FCPATH.'Templates/taxcert.php';
    }

    $fp = fopen ( $filename, 'r' );
        //read our template into a variable
    $output = fread( $fp, filesize($filename));
        //Orders
    $torders_array = array();
    $keys = array();
    $values = array();
    $order_details = $this->Order_reports_model->get_torders($OrderUID);
    $SavePreviewOrderNumber = $order_details->OrderNumber;

    $date = $order_details->OrderEntryDatetime;
    $OrderDate =  date('m/d/Y', strtotime($date));
    $dates = array('OrderDate'=>$OrderDate);
    foreach ($dates as $key => $value) 
    {

        array_push($keys, '%%'.$key.'%%');
        array_push($values, $value);
    }
    $CurrentDate = array('CurrentDate'=>date("m/d/Y"));
    foreach ($CurrentDate as $key => $value) 
    {

        array_push($keys, '%%'.$key.'%%');
        array_push($values, $value);
    }

    $base_url = FCPATH;
    $Attention =  $order_details->AttentionName;
    if($Attention)
    {
        $AttentionName = $Attention;
    }
    else{

        $AttentionName = '-';
    }
    $CustomerPContactMobileNo = $order_details->CustomerPContactMobileNo;
    $CustomerAddress1 = $order_details->CustomerAddress1;
    if($CustomerAddress1)
    {
        $CustomerAddress1 =  $order_details->CustomerAddress1;
    }
    else{
        $CustomerAddress1 = ' ';
    }
    $CustomerZipCode = $order_details->CustomerZipCode;
    if($CustomerZipCode)
    {
        $CustomerZipCode = $order_details->CustomerZipCode;
    }
    else{
        $CustomerZipCode = ' ';
    }
    $CustomerAddress2 = $order_details->CustomerAddress2;
    if($CustomerAddress2)
    {
        $CustomerAddress2 =  $order_details->CustomerAddress2;
    }
    else{
        $CustomerAddress2 = '-';
    }
    $CustomerNumber = $order_details->CustomerNumber;
    if($CustomerNumber)
    {
        $CustomerNumber = $order_details->CustomerNumber.'/';
    }
    else{
        $CustomerNumber = '';
    }
    $CustomerName = $order_details->CustomerName;
    if($CustomerName)
    {
        $CustomerName = $order_details->CustomerName;
    }
    else{
        $CustomerName = '-';
    }
    $CustomerPContactName =  $order_details->CustomerPContactName;
    if($CustomerPContactName)
    {
        $CustomerPContactName =  $order_details->CustomerPContactName;
    }
    else{
        $CustomerPContactName = '-';
    }
    $CustomerStateCode =  $order_details->CustomerStateCode;
    if($CustomerStateCode)
    {
       $CustomerStateCode =  $order_details->CustomerStateCode.'-'; 
    }
    else
    {
        $CustomerStateCode =  ' '; 
    }
    $CustomerCountyName =  $order_details->CustomerCountyName;
    if($CustomerCountyName)
    {
        $CustomerCountyName =  $order_details->CustomerCountyName;
    }
    else{
        $CustomerCountyName = '-';
    }
    $CustomerCityName = $order_details->CustomerCityName;
    if($CustomerCityName)
    {
         $CustomerCityName = $order_details->CustomerCityName.',';
    }
    else{
        $CustomerCityName = ' ';
    }
    $Mortgagee = $order_details->Mortgagee;
    $OrderNumber = $order_details->OrderNumber;
    $LoanNumber = $order_details->LoanNumber;
    if($LoanNumber)
    {
        $LoanNumber = $order_details->LoanNumber;
    }
    else{
        $LoanNumber = '-';
    }
    $OwnerName = $order_details->OwnerName;
    $CountyName =  $order_details->CountyName;
    $PropertyAddress1 = $order_details->PropertyAddress1;
    $PropertyAddress2 = $order_details->PropertyAddress2.',';
    $CityName = $order_details->CityName.',';
    $StateName = $order_details->StateName;
    $StateCode = $order_details->StateCode;
    if($StateCode)
    {
        $StateCode = $order_details->StateCode;
    }
    else{
        $StateCode = '-';
    }
        $DisclaimerResult = $this->Order_reports_model->get_DisclaimerNote($order_details->StateCode);
        if($DisclaimerResult->DisclaimerNote){
            $DisclaimerNote = $DisclaimerResult->DisclaimerNote;
        }
        else{
             $DisclaimerNote = '-';
        }
        if($DisclaimerResult->StateEmail){
            $DisclaimerStateEmail = $DisclaimerResult->StateEmail;
        }
        else{
            $DisclaimerStateEmail = '-';
        }
        if($DisclaimerResult->StateWebsite){
            $DisclaimerStateWebsite = $DisclaimerResult->StateWebsite;
        }
        else{
            $DisclaimerStateWebsite = '-';
        }
        if($DisclaimerResult->StatePhoneNumber){
            $DisclaimerStatePhoneNumber = $DisclaimerResult->StatePhoneNumber;
        }
        else{
            $DisclaimerStatePhoneNumber = '-';
        }
        if($DisclaimerResult->LegalDisclaimerNote == 1 && $DisclaimerResult->TaxDisclaimerNote == 1)
        {
            $taxpagebreak = '<div style="page-break-after: always;"></div>';
            $pagebreak =   '<div style="page-break-after: auto;"></div>';
            $LegalDisclaimerNote = 'displayfooter';
            $TaxDisclaimerNote = 'displayfooter';
        }
        elseif($DisclaimerResult->LegalDisclaimerNote == 0 && $DisclaimerResult->TaxDisclaimerNote == 1){

 
                $LegalDisclaimerNote = 'noheaderfooter';
                $TaxDisclaimerNote = ' ';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = ' ';
            
                             
        }
        elseif($DisclaimerResult->LegalDisclaimerNote == 1 && $DisclaimerResult->TaxDisclaimerNote == 0)
        {
                $LegalDisclaimerNote = 'displayfooter';
                $TaxDisclaimerNote = 'noheaderfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = '<div style="page-break-after: auto;"></div>';
        }
        else{
            $LegalDisclaimerNote = 'noheaderfooter';
                $TaxDisclaimerNote = 'noheaderfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = '<div style="page-break-after: always;"></div>';
        }
    $SearchDate = $order_details->SearchThroDate;
    if($SearchDate == '0000-00-00')
    {
        $SearchThroDate =  '-';
    }
    else{
        $SearchThroDate =  date('m/d/Y', strtotime($SearchDate));
    }
    $SearchFrom = $order_details->SearchFromDate;
    if($SearchFrom == '0000-00-00 00:00:00' || $SearchFrom == '')
        {
            $SearchFromDate = '-';
        }
        else
        {
            $SearchFromDate = date('m/d/Y', strtotime($SearchFrom));
        }
        $SearchAsOf = $order_details->SearchAsOfDate;
        if($SearchAsOf == '0000-00-00 00:00:00' || $SearchAsOf == '')
            {
                $SearchAsOfDate = '-';
            }
            else
            {
                $SearchAsOfDate = date('m/d/Y', strtotime($SearchAsOf));
            }
            $ZipCode = $order_details->PropertyZipcode;
            $address = $this->Order_reports_model->get_Address($OrderUID);
            foreach ($address as $key => $data) {

                $AssessedCountyName = $data->AssessedCountyName;
                $USPSCountyName = $data->USPSCountyName;
            }
            $ImageUrl = base_url().'assets/img/sourcepoint.png';
            $GoogleMapAddress = $PropertyAddress1.' '.$PropertyAddress2.' '.$CityName.' '.$StateName.' '.$ZipCode;

            $tordertaxcerts_array = $this->Order_reports_model->get_tordertaxcerts($OrderUID);
            $tordertaxcerts_array_count = count($tordertaxcerts_array);

            $taxcert_html = ' ';
            $NumTotalWOExempt = '';
            $sep_taxcert_html = ' ';
            if($order_details->PropertyStateCode == 'TX'){

                $TotalTaxBaseAmt = [];
                $TotalTaxDueAmt = [];
                $TotalWOExempt = [];

                $taxcert_html.='<div class="">
                <div style="page-break-inside: avoid;">
                <table style="width: 100%;margin-top: 10pt;" cellspacing="0">
                <thead>
                <tr>
                <th class="blur text-center" colspan="5">
                <p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Summary of All Account(s)</p>
                </th>
                </tr>
                </thead>
                <tbody>
                <tr class="br-black br-b-trans">
                <td style="width: 30%;">
                <p style="font-size: 7pt;"></p>
                </td>
                <td colspan="2" style="width: 25%;" class="td-ta-r">
                <p style="font-size: 7pt;" class="bold">SUMMARY OF CURRENT YEAR</p>
                </td>
                <td colspan="2" style="width: 25%;" class="td-ta-r">
                <p style="font-size: 7pt;" class="bold ">SUMMARY OF ALL TAXES DUE</p>
                </td>
                </tr>

                <tr class="br-black br-t-trans">
                <td>
                <p style="font-size: 7pt;" class="bold td-ta-r"></p>
                </td>
                <td class="td-ta-r">
                <p style="font-size: 7pt;" class="bold td-ta-r">TAX YEAR</p>
                </td>
                <td class="td-ta-r">
                <p style="font-size: 7pt;" class="bold td-ta-r">BASE TAX</p>
                </td>
                <td class="td-ta-r">
                <p style="font-size: 7pt;" class="bold td-ta-r">DUE  %%CurrentMonth%%</p>
                </td>
                <td class="td-ta-r">
                <p style="font-size: 7pt;" class="bold td-ta-r">DUE  %%NextMonth%%</p>
                </td>
                </tr>';

                for ($i = 0; $i < $tordertaxcerts_array_count; $i++) {

                    $DocumentTypeName = $tordertaxcerts_array[$i]->DocumentTypeName;
                    if ($DocumentTypeName) {
                        if ($DocumentTypeName == 'Others') {
                            $TaxType = $tordertaxcerts_array[$i]->TaxType;
                            if ($TaxType) {
                                $TaxDocumentTypeName = $TaxType;
                            } else {
                                $TaxDocumentTypeName = '-';
                            }
                        } else if ($DocumentTypeName !== 'Others') {
                            $TaxDocumentTypeName = $DocumentTypeName;
                        } else {
                            $TaxDocumentTypeName = '-';
                        }
                    } else {
                        $TaxDocumentTypeName = '-';
                    }

                    $WOExempt = $tordertaxcerts_array[$i]->WOExempt;
                    if ($WOExempt) {
                        $TotalWOExempt[] = $WOExempt;
                    } else {
                        $TotalWOExempt[] = 0;
                    }

                    $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                    $taxinstallment_array = $this->Order_reports_model->GetTaxInstallmentGroupByYear($OrderUID, $TaxCert);
                    $taxinstallment_array_count = count($taxinstallment_array);
                    $TotalDueAmount = array();
                    $TotalBaseAmount = array();

                    for ($k = 0; $k < $taxinstallment_array_count; $k++) {
                        $keys = array();
                        $values = array();
                        foreach($taxinstallment_array[$k] as $key => $value) {
                            $TotalBaseAmt = $taxinstallment_array[$k]->TotalBaseAmt;
                            $TotalPaidAmt = $taxinstallment_array[$k]->TotalPaidAmt;
                            $TotalTaxStatus = $taxinstallment_array[$k]->TotalTaxStatus;

                            $DueAmount = [];
                            $GrossAmount = [];
                            $TaxYear = $taxinstallment_array[$k]->TaxYear;
                            $taxdue = $this->Order_reports_model->GetTaxDueDate($OrderUID, $TaxCert, $TaxYear);

                            foreach($taxdue as $key => $value) {
                                $DueAmount[] = $value->TotalPaidAmt;
                                $GrossAmount[] = $value->TotalGrossAmt;
                            }

                            $TotalAmountPaid = array_sum($DueAmount);
                            $TotalGrossAmount = array_sum($GrossAmount);
                            $TotalBaseAmt = $taxinstallment_array[$k]->TotalBaseAmt;

                           /* if ($TotalAmountPaid == 0) {
                                $TaxDueAmount = 0;
                            } else {*/
                                $TaxDueAmount = $TotalGrossAmount - $TotalAmountPaid;
                            /*}*/

                            $TaxYear = $taxinstallment_array[0]->TaxYear;
                            $BaseAmt = $taxinstallment_array[0]->TotalBaseAmt;
                            $NumBaseAmt = number_format($BaseAmt, 2);
                        }

                        $TotalDueAmount[] = $TaxDueAmount;
                        $TotalDue = array_sum($TotalDueAmount);
                        $NumTotalDue = number_format($TotalDue, 2);
                    }

                    $TotalTaxBaseAmt[] = $BaseAmt;
                    $TotalTaxDueAmt[] = $TotalDue;

                    $taxcert_html.= '<tr class="br-black br-b-trans br-t-trans"> <td >
                    <p style = "font-size: 7pt;"
                    class = "td-ta-l" > '.$TaxDocumentTypeName.' </p> </td> <td class = "td-ta-r" >
                    <p style = "font-size: 7pt;"
                    class = "bold td-ta-r" > '.$TaxYear.' </p> </td> <td class = "td-ta-r" >
                    <p style = "font-size: 7pt;"
                    class = "td-ta-r" > '.$NumBaseAmt.' </p> </td> <td class = "td-ta-r" >
                    <p style = "font-size: 7pt;"
                    class = "td-ta-r" > '.$NumTotalDue.' </p> </td> <td class = "td-ta-r" >
                    <p style = "font-size: 7pt;"
                    class = "td-ta-r" > '.$NumTotalDue.' </p> </td> </tr>';

                }

                $TotalWOExempt = array_sum($TotalWOExempt);
                $NumTotalWOExempt = number_format($TotalWOExempt, 2);

                $TotalTaxBaseAmt = array_sum($TotalTaxBaseAmt);
                $NumTotalTaxBaseAmt = number_format($TotalTaxBaseAmt, 2);
                $TotalTaxDueAmt = array_sum($TotalTaxDueAmt);
                $NumTotalTaxDueAmt = number_format($TotalTaxDueAmt, 2);

                $taxcert_html.= '<tr class="br-black br-t-trans"> <td >
                <p style = "font-size: 7pt;"
                class = "td-ta-l" > TOTAL TAX </p> </td> <td class = "td-ta-r" >
                <p style = "font-size: 7pt;"
                class = "bold td-ta-r" > </p> </td> <td class = "td-ta-r" >
                <p style = "font-size: 7pt;"
                class = "bold td-ta-r" > '.$NumTotalTaxBaseAmt.' </p> </td> <td class = "td-ta-r" >
                <p style = "font-size: 7pt;"
                class = "bold td-ta-r" > '.$NumTotalTaxDueAmt.' </p> </td> <td class = "td-ta-r" >
                <p style = "font-size: 7pt;"
                class = "bold td-ta-r" > '.$NumTotalTaxDueAmt.' </p> </td> </tr> </tbody>
                </table>
                </div>
                </div>
                </div>
                </div>';

            }

            $LegalDesc = $this->Order_reports_model->GetPropertyLegalDesc($OrderUID);

            $LegalDescription = $LegalDesc->PropertyDescription;
            //$LegalDescription = nl2br($LegalDesc->LegalDescription);
            /*if($LegalDescription)
            {
                $LegalDescription = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Legal Description</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;<pre>'.nl2br($LegalDescription).'</pre> </td></tr></table>';
            }else{
                $LegalDescription = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Legal Description</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;None </td></tr></table>';
            }*/

            $Mort = array('CustomerName'=>$CustomerNumber.$CustomerName,'AttentionName'=>$AttentionName,'CustomerAddress1'=>$CustomerAddress1,'CustomerAddress2'=>$CustomerAddress2,'CustomerCountyName'=>$CustomerCountyName,'CustomerCityName'=>$CustomerCityName,'CustomerStateCode'=>$CustomerStateCode,'CustomerPContactName'=>$CustomerPContactName,'OrderMortgagee'=>$Mortgagee,'Ordernumber'=>$OrderNumber,'Loannumber'=>$LoanNumber,'Ownername'=>$OwnerName,'Countyname'=>$CountyName,'County_name'=>$CountyName.',','Propertyaddress1'=>$PropertyAddress1,'Propertyaddress2'=>$PropertyAddress2,'Cityname'=>$CityName,'Statecode'=>$StateCode.'-','Zip'=>$ZipCode,'SearchThroDate'=>$SearchThroDate,'GoogleMapAddress'=>$GoogleMapAddress,'SearchFromDate'=>$SearchFromDate,'SearchAsOfDate'=>$SearchAsOfDate,'DisclaimerNote'=>$DisclaimerNote,'Url'=>$base_url,'OrderDate'=>$OrderDate,'CustomerZipCode'=>$CustomerZipCode,'DisclaimerStateEmail'=>$DisclaimerStateEmail,'DisclaimerStateWebsite'=>$DisclaimerStateWebsite,'DisclaimerStatePhoneNumber'=>$DisclaimerStatePhoneNumber,'Customer_State_Code'=>$Customer_State_Code,'Customer_County_Name'=>$Customer_County_Name,'TaxDisclaimerNote'=>$TaxDisclaimerNote,'taxpagebreak'=>$taxpagebreak,'pagebreak'=>$pagebreak,'LegalDisclaimerNote'=>$LegalDisclaimerNote,'State_code'=>$StateCode,'ImageUrl'=>$ImageUrl,'LegalDescription'=>$LegalDescription, 'taxcert_html'=>$taxcert_html, 'sep_taxcert_html'=>$sep_taxcert_html,'NumTotalWOExempt'=>$NumTotalWOExempt,'CurrentDateTime'=>date('m/d/Y',strtotime("now")),'CurrentMonth'=>date('m/Y', strtotime('now')),'NextMonth'=>date('m/Y', strtotime('+1 month')));


            foreach ($Mort as $key => $value) 
            {

                array_push($keys, '%%'.$key.'%%');
                array_push($values, $value);
            }
        //Orders

        //Order Assessment
            $order_assessment = $this->Order_reports_model->get_OrderAssessment($OrderUID);
            if($order_assessment)
            {

                foreach ($order_assessment as $data_orderass_info) 
                {
                    $AssessedYear = $data_orderass_info->AssessedYear;

                    if($AssessedYear)
                    {
                        array_push($keys, '%%AssessedYear%%');
                        array_push($values, $AssessedYear);
                    }
                    else{
                        array_push($keys, '%%AssessedYear%%');
                        array_push($values, '-');

                    }
                    $Agricultural = $data_orderass_info->Agriculture;

                    if($Agricultural)
                        {   $Agricultural = '$'.number_format($Agricultural,2);
                    array_push($keys, '%%Agricultural%%');
                    array_push($values, $Agricultural);
                }
                else{
                    array_push($keys, '%%Agricultural%%');
                    array_push($values, '-');

                }
                $TotalValue = $data_orderass_info->TotalValue;
                if($TotalValue)
                {
                    array_push($keys, '%%TotalValue%%');
                    array_push($values, $TotalValue);
                }
                else{
                    array_push($keys, '%%TotalValue%%');
                    array_push($values, '-');

                }
                $Landstr = $data_orderass_info->Land;
                if($Landstr)
                {
                    $Landltrim = ltrim($Landstr, '$');
                    $LandRepl = str_replace(",","",$Landltrim);
                    $Lan = substr($LandRepl, 0,-3);
                    $Land = '$'.number_format($Lan,2);
                }
                else{
                    $Land = '-';
                }
                $Buildingsstr = $data_orderass_info->Buildings;
                if($Buildingsstr)
                {

                    $Buildingsltrim = ltrim($Buildingsstr, '$');
                    $BuildingsRepl = str_replace(",","",$Buildingsltrim);
                    $Build = substr($BuildingsRepl, 0,-3);
                    $Buildings = '$'.number_format($Build,2);
                }
                else{
                    $Buildings = '-';
                }
                $AssessmentValue = $data_orderass_info->AssessmentValue;
                if($AssessmentValue)
                {
                    $AssessmentValue = $AssessmentValue;
                    array_push($keys, '%%AssessmentValue%%');
                    array_push($values, $AssessmentValue);
                    array_push($keys, '%%alignment%%');
                    array_push($values, 'text-left');
                }
                else{
                    array_push($keys, '%%AssessmentValue%%');
                    array_push($values, '-');
                    array_push($keys, '%%alignment%%');
                    array_push($values, 'text-center');

                }
                $Value = array('TotalValue'=>$TotalValue,'Buildings'=>$Buildings,'Land'=>$Land,'Agricultural'=>$Agricultural,'AssessmentValue'=>$AssessmentValue);
                foreach ($Value as $key => $value) 
                {
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
            } 
        }
        else{


            $TaxArray = array('AssessedYear','Land','Buildings','TotalValue','Agricultural');
            foreach($TaxArray as $col)
            {
                array_push($keys, '%%'.$col.'%%');
                array_push($values, '-');
            }
             array_push($keys, '%%AssessmentValue%%');
             array_push($values, '-');
             array_push($keys, '%%alignment%%');
             array_push($values, 'text-center');

        }
        //Assessment

        /* Latest Tax Starts */

        $tax_information = $this->Order_reports_model->get_tax_latest($OrderUID);
        if($tax_information)
        {
            //Property tax in ISGN Report
            foreach ($tax_information as $data) 
            {
                //DeliquentTax
                if($data->TaxStatusName)
                {
                    if($data->TaxStatusName == 'Delinquent')
                    {
                        $DeliquentTax = array('DeliquentTax'=>'Yes');
                        foreach($DeliquentTax as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    else
                    {
                        $DeliquentTax = array('DeliquentTax'=>'No');
                        foreach($DeliquentTax as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                }
                else{
                    array_push($keys, '%%DeliquentTax%%');
                    array_push($values, '-');
                }

                $LatestTaxYear = $data->LatestTaxYear;
                if($LatestTaxYear)
                {

                    array_push($keys, '%%LatestTaxYear%%');
                    array_push($values, $LatestTaxYear);
                    array_push($keys, '%%ReferTaxSection%%');
                    array_push($values, 'REFER TAX SECTION');
                }
                else
                {
                    array_push($keys, '%%LatestTaxYear%%');
                    array_push($values, '-');
                    array_push($keys, '%%ReferTaxSection%%');
                    array_push($values, '-');
                }
            }
        }
        else
        {
            $TaxArray = array('LatestTaxYear','DeliquentTax','ReferTaxSection');
            foreach($TaxArray as $col)
            {
                array_push($keys, '%%'.$col.'%%');
                array_push($values, '-');
            }
        }

        /* Latest Tax Ends */

        /* Property Information Starts */

        $property_information = $this->Order_reports_model->getPropertyInformation($OrderUID);
        if($property_information)
        {
            foreach ($property_information as $key => $data) 
            {
                $MaritalStatusName = $data->MaritalStatusName;
                if($MaritalStatusName)
                {
                    array_push($keys, '%%MaritalStatusName%%');
                    array_push($values, $MaritalStatusName);
                }
                else{
                    array_push($keys, '%%MaritalStatusName%%');
                    array_push($values, ' ');
                }
                $OwnerName = $data->OwnerName;
                if($OwnerName)
                {
                    array_push($keys, '%%OwnerName%%');
                    array_push($values, $OwnerName);
                }
                else{
                    array_push($keys, '%%OwnerName%%');
                    array_push($values, '-');
                }
                $SubDivisionName = $data->SubDivisionName;
                if($SubDivisionName)
                {
                    array_push($keys, '%%SubDivisionName%%');
                    array_push($values, $SubDivisionName);
                }
                else{
                    array_push($keys, '%%SubDivisionName%%');
                    array_push($values, '-');
                }

                $sdMapNo = $data->sdMapNo;
                if($sdMapNo)
                {
                    array_push($keys, '%%sdMapNo%%');
                    array_push($values, $sdMapNo);
                }
                else{
                    array_push($keys, '%%sdMapNo%%');
                    array_push($values, '-');
                }
                $dSection = $data->dSection;
                if($dSection)
                {
                    array_push($keys, '%%dSection%%');
                    array_push($values, $dSection);
                }
                else{
                    array_push($keys, '%%dSection%%');
                    array_push($values, '-');
                }
                $Township = $data->Township;
                if($Township)
                {
                    array_push($keys, '%%Township%%');
                    array_push($values, $Township);
                }
                else{
                    array_push($keys, '%%Township%%');
                    array_push($values, '-');
                }
                $APN = $data->APN;
                if($APN)
                {
                    array_push($keys, '%%APN%%');
                    array_push($values, $APN);
                }
                else{
                    array_push($keys, '%%APN%%');
                    array_push($values, '-');
                }
                $Lot = $data->Lot;
                $Block = $data->Block;
                if($Lot && $Block)
                {
                  array_push($keys, '%%Lot%%');
                  array_push($values, $Block.'/'.$Lot);
                }
                
                else if($Lot)
                {
                    array_push($keys, '%%Lot%%');
                    array_push($values, $Lot);
                }
                
                else if($Block)
                {
                    array_push($keys, '%%Lot%%');
                    array_push($values, $Block);
                }
                else{
                    array_push($keys, '%%Lot%%');
                    array_push($values, '-');
                }
            }
        }
        else
        {
            $TaxArray = array('Exempt','APN','Lot','Block','SubDivisionName','sdMapNo','dSection','Township','OwnerName');
            foreach($TaxArray as $col)
            {
                array_push($keys, '%%'.$col.'%%');
                array_push($values, '-');
            }
                    array_push($keys, '%%MaritalStatusName%%');
                    array_push($values, ' ');
        }

        /* Property Information Ends */

        //Get Exemption
        $exemptions = $this->Order_reports_model->get_taxExemption($OrderUID);
        if($exemptions)
        {
            $Exempt = array('Exempt'=>'Yes');
            foreach($Exempt as $col => $val)
            {
                array_push($keys, '%%'.$col.'%%');
                array_push($values, $val);
            }
        }
        else
        {
            $Exempt = array('Exempt'=>'No');
            foreach($Exempt as $col => $val)
            {
                array_push($keys, '%%'.$col.'%%');
                array_push($values, $val);
            }
        }
        //Get Exemption
        //Get Borrowers
            $borrowers = $this->Order_reports_model->getBorrowers($OrderUID);
            $Count =  count($borrowers);
            if($Count > 1){

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    if($value->PRName !=''){

                    $borrower .=  trim($value->PRName.',');
                    }
                }
                $borrowers =  rtrim($borrower,",");
                $Array = explode(",", $borrowers);
                $LastArrayValue =  end($Array);
                
                if(count($Array) > 1 ){
                    array_pop($Array);
                    $BorrowerName = implode(", &nbsp;", $Array).' and '.$LastArrayValue;

                }else{
                    
                    $BorrowerName = implode(", &nbsp;", $Array);
                }
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
            else{

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    $borrower .=  $value->PRName.',';

                }
                $BorrowerName =  rtrim($borrower,",");
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
        //Get Borrowers



        $output = str_replace($keys,$values, $output);
        $doc->load($output);

               //Tax Starts
            preg_match_all('/<div class=\"tordertaxcerts\">(.*?)<\/div>/s',$output,$tordertaxcerts);

            $tordertaxcerts_array = array();
            $tordertax_array = array();
            $tordertaxcerts_array = $this->Order_reports_model->get_tordertaxcerts($OrderUID);
            $tordertaxcerts_array_count = count($tordertaxcerts_array);
            $tordertaxcerts_table2="";
            $tordertaxcerts_table3="";
            for ($i=0; $i < $tordertaxcerts_array_count; $i++) 
            { 
                $tordertaxcerts_table = '';
                $tordertaxcerts_array[$i]->tax_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($tordertaxcerts_array[$i] as $key => $value)
                {
                //subdocumenttype
                $DocumentTypeName = $tordertaxcerts_array[$i]->DocumentTypeName;
                if($DocumentTypeName)
                {
                    if($DocumentTypeName == 'Others')
                    {
                        $TaxType = $tordertaxcerts_array[$i]->TaxType;
                        if($TaxType)
                        {
                            array_push($keys, '%%TaxDocumentTypeName%%');
                            array_push($values, $TaxType);
                        }
                        else
                        {
                            array_push($keys, '%%TaxDocumentTypeName%%');
                            array_push($values, '-');
                        }
                    }
                    else if($DocumentTypeName !== 'Others')
                    {
                        array_push($keys, '%%TaxDocumentTypeName%%');
                        array_push($values, $DocumentTypeName);
                    }
                    else
                    {
                        array_push($keys, '%%TaxDocumentTypeName%%');
                        array_push($values, '-');
                    }

                }
                else{
                    array_push($keys, '%%TaxDocumentTypeName%%');
                    array_push($values, '-');
                }
                //subdocumenttype
                //Amount Paid
                    $AmountPaid = $tordertaxcerts_array[$i]->AmountPaid;
                    $AmtPaid = '$'.number_format($AmountPaid,2);
                    if($AmtPaid)
                    {
                        array_push($keys, '%%AmountPaid%%');
                        array_push($values, $AmtPaid);
                    }
                    else{
                        array_push($keys, '%%AmountPaid%%');
                        array_push($values, '-');
                    }
                //Amount Paid
                //Amount Due
                    $AmountDue = $tordertaxcerts_array[$i]->AmountDue;
                    $AmtDue = '$'.number_format($AmountDue,2);
                    if($AmtDue)
                    {
                        array_push($keys, '%%AmountDue%%');
                        array_push($values, $AmtDue);
                    }
                    else{
                        array_push($keys, '%%AmountDue%%');
                        array_push($values, '-');
                    }
                //Amount Due
                //Gross Amount
                    $GrossAmount = $tordertaxcerts_array[$i]->GrossAmount;
                    $GrossAmt = '$'.number_format($GrossAmount,2);
                    if($GrossAmt)
                    {
                        array_push($keys, '%%GrossAmount%%');
                        array_push($values, $GrossAmt);
                    }
                    else{
                        array_push($keys, '%%GrossAmount%%');
                        array_push($values, '-');
                    }
                //Gross Amount


                  //ApprovedUnapprovedTaxAuthorityDetails
                    $UnapprovedTaxAuthorityDetails = $this->Order_reports_model->GetUnapprovedTaxAuthorityDetails($OrderUID,$tordertaxcerts_array[$i]->TaxAuthorityUID);
                    if($UnapprovedTaxAuthorityDetails)
                    {
                        //PaymentAddrLine1
                            $PaymentAddrLine1 = $UnapprovedTaxAuthorityDetails->PaymentAddrLine1;
                            if($PaymentAddrLine1)
                            {
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, $PaymentAddrLine1.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, ' ');
                            }
                        //PaymentAddrLine1
                        //PaymentAddrLine2
                            $PaymentAddrLine2 = $UnapprovedTaxAuthorityDetails->PaymentAddrLine2;
                            if($PaymentAddrLine2)
                            {
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, $PaymentAddrLine2.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, ' ');
                            }
                        //PaymentAddrLine2
                        //PaymentCity
                            $PaymentCity = $UnapprovedTaxAuthorityDetails->PaymentCity;
                            if($PaymentCity)
                            {
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, $PaymentCity.',');
                            }
                            else{
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, ' ');
                            }
                        //PaymentCity
                        //PaymentState
                            $PaymentState = $UnapprovedTaxAuthorityDetails->PaymentState;
                            if($PaymentState)
                            {
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, $PaymentState.'-');
                            }
                            else{
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, ' ');
                            }
                        //PaymentState
                        //PaymentZipCode
                            $PaymentZipCode = $UnapprovedTaxAuthorityDetails->PaymentZipCode;
                            if($PaymentZipCode)
                            {
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, $PaymentZipCode);
                            }
                            else{
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, ' ');
                            }
                        //PaymentZipCode


                        //Tax Collector Name
                            $TaxCollector = $UnapprovedTaxAuthorityDetails->TaxCollector;
                            if($TaxCollector)
                            {
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, $TaxCollector);
                            }
                            else{
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, '-');
                            }
                        //Tax Authority Name
                        //Tax Payable
                            $TaxPayable = $UnapprovedTaxAuthorityDetails->TaxPayable;
                            if($TaxPayable)
                            {
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, $TaxPayable);
                            }
                            else{
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, '-');
                            }
                        //Tax Payable
                        //Collector Phone
                        $CollectorPhone = $UnapprovedTaxAuthorityDetails->CollectorPhone;
                        if($CollectorPhone)
                        {
                            $numbers_only = preg_replace("/[^\d]/", "", $CollectorPhone);
                            $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "($1)&nbsp;$2-$3", $numbers_only);
                            array_push($keys, '%%CollectorPhoneNumber%%');
                            array_push($values, $number);
                        }
                        else{
                            array_push($keys, '%%CollectorPhoneNumber%%');
                            array_push($values, '-');
                        }
                        //Collector Phone
                        //Website Address
                        $WebsiteAddress = $UnapprovedTaxAuthorityDetails->WebsiteAddress;
                        if($WebsiteAddress)
                        {
                            array_push($keys, '%%WebsiteAddr%%');
                            array_push($values, '<u>'.$WebsiteAddress.'</u>');
                        }
                        else{
                            array_push($keys, '%%WebsiteAddr%%');
                            array_push($values, '-');
                        }
                        //Website Address
                        
                    }else{
                        $ApprovedTaxAuthorityDetails = $this->Order_reports_model->GetApprovedTaxAuthorityDetails($tordertaxcerts_array[$i]->TaxAuthorityUID);
                        if($ApprovedTaxAuthorityDetails)
                        {
                            //PaymentAddrLine1
                            $PaymentAddrLine1 =$ApprovedTaxAuthorityDetails->PaymentAddrLine1;
                            if($PaymentAddrLine1)
                            {
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, $PaymentAddrLine1.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, ' ');
                            }
                            //PaymentAddrLine1
                            //PaymentAddrLine2
                            $PaymentAddrLine2 =$ApprovedTaxAuthorityDetails->PaymentAddrLine2;
                            if($PaymentAddrLine2)
                            {
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, $PaymentAddrLine2.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, ' ');
                            }
                            //PaymentAddrLine2
                            //PaymentCity
                            $PaymentCity =$ApprovedTaxAuthorityDetails->PaymentCity;
                            if($PaymentCity)
                            {
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, $PaymentCity.',');
                            }
                            else{
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, ' ');
                            }
                            //PaymentCity
                            //PaymentState
                            $PaymentState =$ApprovedTaxAuthorityDetails->PaymentState;
                            if($PaymentState)
                            {
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, $PaymentState.'-');
                            }
                            else{
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, ' ');
                            }
                            //PaymentState
                            //PaymentZipCode
                            $PaymentZipCode =$ApprovedTaxAuthorityDetails->PaymentZipCode;
                            if($PaymentZipCode)
                            {
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, $PaymentZipCode);
                            }
                            else{
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, ' ');
                            }
                            //PaymentZipCode
                            //Tax Collector Name
                            $TaxCollector =$ApprovedTaxAuthorityDetails->TaxCollector;
                            if($TaxCollector)
                            {
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, $TaxCollector);
                            }
                            else{
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, '-');
                            }
                            //Tax Authority Name
                            //Tax Payable
                            $TaxPayable =$ApprovedTaxAuthorityDetails->TaxPayable;
                            if($TaxPayable)
                            {
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, $TaxPayable);
                            }
                            else{
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, '-');
                            }
                            //Tax Payable
                            //Collector Phone
                            $CollectorPhone =$ApprovedTaxAuthorityDetails->CollectorPhone;
                            if($CollectorPhone)
                            {
                                $numbers_only = preg_replace("/[^\d]/", "", $CollectorPhone);
                                $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "($1)&nbsp;$2-$3", $numbers_only);
                                array_push($keys, '%%CollectorPhoneNumber%%');
                                array_push($values, $number);
                            }
                            else{
                                array_push($keys, '%%CollectorPhoneNumber%%');
                                array_push($values, '-');
                            }
                            //Collector Phone
                            //Website Address
                            $WebsiteAddress = $ApprovedTaxAuthorityDetails->WebsiteAddress;
                            if($WebsiteAddress)
                            {
                                array_push($keys, '%%WebsiteAddr%%');
                                array_push($values, '<u>'.$WebsiteAddress.'</u>');
                            }
                            else{
                                array_push($keys, '%%WebsiteAddr%%');
                                array_push($values, '-');
                            }
                            //Website Address
                        }
                        else{
                            $data = array('PaymentAddrLine1','PaymentAddrLine2','PaymentCity','PaymentState','PaymentZipCode','TaxCollector');
                            foreach ($data as $value) {
                                array_push($keys, '%%'.$value.'%%');
                                array_push($values, ' ');
    
                            }
                            $datas = array('TaxPayable','CollectorPhoneNumber','WebsiteAddr');
                            foreach ($datas as $value) {
                                array_push($keys, '%%'.$value.'%%');
                                array_push($values, '-');
                            }
                        }
        
                    }
                    //ApprovedUnapprovedTaxAuthorityDetails
                //Amount Deliquent
                    $AmountDelinquent = $tordertaxcerts_array[$i]->AmountDelinquent;
                    $AmtDelinquent = '$'.number_format($AmountDelinquent,2);
                    if($AmtDelinquent)
                    {
                        array_push($keys, '%%AmountDelinquent%%');
                        array_push($values, $AmtDelinquent);
                    }
                    else{
                        array_push($keys, '%%AmountDelinquent%%');
                        array_push($values, '-');
                    }
                //Amount Deliquent
                //Account Number
                    $ParcelNumber = $tordertaxcerts_array[$i]->ParcelNumber;
                    if($ParcelNumber)
                    {
                        array_push($keys, '%%ParcelNumber%%');
                        array_push($values, $ParcelNumber);
                    }
                    else{
                        array_push($keys, '%%ParcelNumber%%');
                        array_push($values, '-');
                    }
                //Account Number
                //Estimated Tax
                    $EstimatedTax = $tordertaxcerts_array[$i]->EstimatedTax;
                    $EstimatedTax = '$'.number_format($EstimatedTax,2);
                    if($EstimatedTax)
                    {
                        array_push($keys, '%%EstimatedTax%%');
                        array_push($values, $EstimatedTax);
                    }
                    else{
                        array_push($keys, '%%EstimatedTax%%');
                        array_push($values, '-');
                    }
                //Estimated Tax
                //Tax Basis Name
                    $TaxBasisName = $tordertaxcerts_array[$i]->TaxBasisName;
                    if($TaxBasisName)
                    {
                        array_push($keys, '%%TaxBasisName%%');
                        array_push($values, $TaxBasisName);
                    }
                    else{
                        array_push($keys, '%%TaxBasisName%%');
                        array_push($values, '-');
                    }
                    //Tax Basis Name
                    //Property Class Name
                    $PropertyClassName = $tordertaxcerts_array[$i]->PropertyClassName;
                    if($PropertyClassName)
                    {
                        array_push($keys, '%%PropertyClassName%%');
                        array_push($values, $PropertyClassName);
                    }
                    else{
                        array_push($keys, '%%PropertyClassName%%');
                        array_push($values, '-');
                    }
                   //Property Class Name
                    //Tax Comments
                        $TaxComments = $tordertaxcerts_array[$i]->TaxComments;
                        if($TaxComments)
                        {
                            array_push($keys, '%%TaxComments%%');
                            array_push($values, $TaxComments);
                            array_push($keys, '%%taxalignment%%');
                            array_push($values, 'text-left');
                        }
                        else{
                            array_push($keys, '%%TaxComments%%');
                            array_push($values, '-');
                            array_push($keys, '%%taxalignment%%');
                            array_push($values, 'text-center');
                        }
                    //Tax Comments
                    $TaxYears = $tordertaxcerts_array[$i]->TaxYear;
                    if($TaxYears)
                    {
                        array_push($keys, '%%TaxYears%%');
                        array_push($values, $TaxYears);
                    }
                    else{
                        array_push($keys, '%%TaxYears%%');
                        array_push($values, '-');
                    }
                //Tax Date Format Change
                    $NextTaxDueDate = $tordertaxcerts_array[$i]->NextTaxDueDate;
                    if($NextTaxDueDate == '0000-00-00')
                    {
                        array_push($keys, '%%NextTaxDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $NextTaxDate =  date('m/d/Y', strtotime($NextTaxDueDate));
                        array_push($keys, '%%NextTaxDate%%');
                        array_push($values, $NextTaxDate);
                    }
                //Tax Date Format Change
                //Tax Date Format Change
                    $GoodThroughDate = $tordertaxcerts_array[$i]->GoodThroughDate;
                    if($GoodThroughDate == '0000-00-00' || $GoodThroughDate == ' ' )
                    {
                        array_push($keys, '%%GoodThroughDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $GoodThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                        array_push($keys, '%%GoodThroughDate%%');
                        array_push($values, $GoodThroughDate);
                    }
                //Tax Date Format Change
                //Tax Date Paid
                    $DatePaid = $tordertaxcerts_array[$i]->DatePaid;
                    if($DatePaid == '0000-00-00')
                    {
                        array_push($keys, '%%PaidDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $PaidDate =  date('m/d/Y', strtotime($DatePaid));
                        array_push($keys, '%%PaidDate%%');
                        array_push($values, $PaidDate);
                    }
                //Tax Date Paid
                //Main Loop
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);    
                //Main Loop
                }
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $TaxExemptionNames = $this->Order_reports_model->getExemptionname($OrderUID,$TaxCert);
                if($TaxExemptionNames)
                {
                    array_push($keys, '%%TaxExemptionNames%%');
                    array_push($values, $TaxExemptionNames);
                }
                else{
                    array_push($keys, '%%TaxExemptionNames%%');
                    array_push($values, '-');
                }

                $tordertaxcerts_table .= str_replace($keys, $values, $tordertaxcerts[0][0]);
                $taxinstallment_html='<tr style="border: 0.01em solid grey;">
                <td  width="10.4%" class="td-bd text-center" style="">
                <p style="font-size: 8pt;" class="">%%Tax_Year%%</p>
                </td>
                <td width="17.8%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="text-center">%%Tax_InstallmentName%%</p>
                </td>
                <td  width="13.5" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="">%%Gross_Amount%%</p>
                </td>
                <td width="14.7%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="text-center">%%Tax_StatusName%%</p>
                </td>
                <td width="21.8%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="">%%Amount_Paid%%</p>
                </td>
                <td width="21.8%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class=" text-center">%%Paid_Date%%</p>
                </td>
                </tr> ';
                $taxinstallment="";
                $taxinstallment_table = '';
                $taxinstallment_array = array();
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $taxinstallment_array = $this->Order_reports_model->gettaxinstallment($OrderUID, $TaxCert);
                $taxinstallment_array_count = count($taxinstallment_array);
                for ($k=0; $k < $taxinstallment_array_count; $k++) 
                { 

                    $keys = array();
                    $values = array();
                    foreach ($taxinstallment_array[$k] as $key => $value) 
                    {
                        $TaxYear = $taxinstallment_array[$k]->TaxYear;
                        if($TaxYear)
                        {
                            array_push($keys, '%%Tax_Year%%');
                            array_push($values, $TaxYear);

                        }
                        else{
                            array_push($keys, '%%Tax_Year%%');
                            array_push($values, '-');

                        }
                        $TaxInstallmentName = $taxinstallment_array[$k]->TaxInstallmentName;
                        if($TaxInstallmentName)
                        {
                            array_push($keys, '%%Tax_InstallmentName%%');
                            array_push($values, $TaxInstallmentName);

                        }
                        else{
                            array_push($keys, '%%Tax_InstallmentName%%');
                            array_push($values, '-');

                        }
                        $GrossAmount = $taxinstallment_array[$k]->GrossAmount;
                        if($GrossAmount)
                        {
                            $GrossAmount = '$'.number_format($GrossAmount,2);
                            array_push($keys, '%%Gross_Amount%%');
                            array_push($values, $GrossAmount);

                        }
                        else{
                            array_push($keys, '%%Gross_Amount%%');
                            array_push($values, '-');

                        }
                        $TaxStatusName = $taxinstallment_array[$k]->TaxStatusName;
                        if($TaxStatusName)
                        {
                            array_push($keys, '%%Tax_StatusName%%');
                            array_push($values, $TaxStatusName);

                        }
                        else{
                            array_push($keys, '%%Tax_StatusName%%');
                            array_push($values, '-');

                        }
                        $AmountPaid = $taxinstallment_array[$k]->AmountPaid;
                        if($AmountPaid)
                        {
                            $AmountPaid = '$'.number_format($AmountPaid,2);
                            array_push($keys, '%%Amount_Paid%%');
                            array_push($values, $AmountPaid);

                        }
                        else{
                            array_push($keys, '%%Amount_Paid%%');
                            array_push($values, '-');

                        }
                        $DatePaid = $taxinstallment_array[$k]->DatePaid;
                        if($DatePaid  == '0000-00-00')
                        {
                            array_push($keys, '%%Paid_Date%%');
                            array_push($values, '-');

                        }
                        else{
                            $DatePaid =  date('m/d/Y', strtotime($DatePaid));
                            array_push($keys, '%%Paid_Date%%');
                            array_push($values, $DatePaid);

                        }

                        $GoodThroughDate = $taxinstallment_array[$k]->GoodThroughDate;
                        if($GoodThroughDate  == '0000-00-00')
                        {
                            array_push($keys, '%%Good_Through_Date%%');
                            array_push($values, '-');

                        }
                        else{
                            $GoodThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                            array_push($keys, '%%Good_Through_Date%%');
                            array_push($values, $GoodThroughDate);

                        }
                    }

                    $taxinstallment .= str_replace($keys, $values, $taxinstallment_html);

                }
                $tordertaxcerts_table = str_replace('%%taxinstallment%%', $taxinstallment, $tordertaxcerts_table);


                $taxexemption="";
                $tordertaxemption_table = '';
                $tordertaxemption_array = array();
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $tordertaxemption_array = $this->Order_reports_model->gettaxExemption($OrderUID, $TaxCert);
                $tordertaxemption_array_count = count($tordertaxemption_array);
                $taxexemption_html='<tr class="tordertaxemption" style="border: 0.01em solid grey;">
                <td  rowspan="'. $tordertaxemption_array_count .'" width="20%" class="td-bd text-center" colspan="2" style="" >
                <p style="font-size: 8pt;" class="bold">Tax Exemption</p>
                </td>
                <td  width="30%" class="td-bd text-center" colspan="5" style="">
                <p style="font-size: 8pt;" class="text-center">%%TaxExemptionName%%</p>
                </td>
                <td  width="30%" class="td-bd text-center" colspan="5" style="">
                <p style="font-size: 8pt;" class="text-center">%%TaxAmount%%</p>
                </td>
                </tr>';
                for ($j=0; $j < $tordertaxemption_array_count; $j++) 
                {
                    if($j>0)
                    {
                      $taxexemption_html='<tr class="tordertaxemption" style="border: 0.01em solid grey;">
                        <td  width="30%" class="td-bd text-center" colspan="5" style="">
                        <p style="font-size: 8pt;" class="text-center">%%TaxExemptionName%%</p>
                        </td>
                        <td  width="30%" class="td-bd text-center" colspan="5" style="">
                        <p style="font-size: 8pt;" class="text-center">%%TaxAmount%%</p>
                        </td>
                        </tr>';

                    }
                    $keys = array();
                    $values = array();
                    foreach ($tordertaxemption_array[$j] as $key => $value) 
                    {
                        $TaxExemptionName = $tordertaxemption_array[$j]->TaxExemptionName;
                        if($TaxExemptionName)
                        {
                            array_push($keys, '%%TaxExemptionName%%');
                            array_push($values, $TaxExemptionName);

                        }
                        else{
                            array_push($keys, '%%TaxExemptionName%%');
                            array_push($values, '-');

                        }
                        $TaxAmount = $tordertaxemption_array[$j]->TaxAmount;
                        if($TaxAmount)
                        {
                            $TaxAmount = '$'.$TaxAmount;
                            array_push($keys, '%%TaxAmount%%');
                            array_push($values, $TaxAmount);

                        }
                        else{
                            array_push($keys, '%%TaxAmount%%');
                            array_push($values, '$0.00');

                        }

                    }

                    $taxexemption .= str_replace($keys, $values, $taxexemption_html);

                }
                $tordertaxcerts_table = str_replace('%%TaxExmp%%', $taxexemption, $tordertaxcerts_table);


 //Tax Exemption------------------------------------------------------------------------------------------
                $tordertaxcerts_table2 .= $tordertaxcerts_table;
            }

            foreach ( $doc->find(".tordertaxcerts") as $node ) 
            {
                $node->innertext = $tordertaxcerts_table2;
            }
        //Tax Ends

        $this->load->library('pdf');
        $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        $pdf = $this->pdf->load($param);
        $pdf->SetProtection(array('copy','print','modify','annot-forms','fill-forms','extract','assemble','print-highres'));
        $pdf->packTableData = true;
        $pdf->shrink_tables_to_fit = 1;
        // $pdf->list_indent_first_level = 0; 
        $html = mb_convert_encoding($doc, 'UTF-8', 'UTF-8');
        $pdf->WriteHTML($html); // write the HTML into the PDF
        $page_count = $pdf->page;
        $doc_save = $SavePreviewOrderNumber.'_TaxCert.pdf';
        $pdf->Output($doc_save, '');

        $this->db->order_by('Id', 'ASC');
        $query=$this->db->get_where('torderpropertyroles', array('PropertyRoleUID'=>5,'OrderUID'=>$OrderUID));
        $troderpropertyroles=$query->row();

        $torderdocuments_data['OrderUID'] = $OrderUID;
        $torders = $this->Order_reports_model->Gettorders($torderdocuments_data);


        if($troderpropertyroles->PRName!='')
        {
            $DocumentFileName = $torders['LoanNumber'].'-'.$troderpropertyroles->PRName.'_TaxCert';        
        }
        elseif($torder['LoanNumber']!='')
        {
            $DocumentFileName = $torders['LoanNumber'].'_TaxCert';
        }
        else
        {
            $DocumentFileName='TaxCert';
        }

        $DocumentFileName = str_replace(array('[',']','/', '\\',':','*','?','"','<','>','|'), '-',$DocumentFileName);


        $torderdocuments_data['DocumentFileName'] = $DocumentFileName . '.pdf';

        $this->db->where('OrderUID', $torderdocuments_data['OrderUID']);
        $this->db->like('DocumentFileName', $DocumentFileName);
        $query=$this->db->get('torderdocuments');
        $reportfilenamecount=$query->num_rows();

        if($reportfilenamecount>0)
        {
           $torderdocuments_data['DocumentFileName']=$this->common_model->GetAvailFileName($DocumentFileName,'.pdf',$reportfilenamecount, $OrderUID);
        }

        $OrderDocs_Path = $torders['OrderDocsPath']; 


        $date = date('Y-m-d');
        //Check Order Docuemnt Path exist.
        if(empty($OrderDocs_Path))
        {

            $query = $this->db->query("update torders SET OrderDocsPath ='uploads/searchdocs/".$date."/".$torders['OrderNumber']."/"."' Where OrderUID=".$torderdocuments_data['OrderUID']);

            $OrderDocs_Path = 'uploads/searchdocs/'.$date.'/'.$torders['OrderNumber']."/";
        }
        $destinationfile = FCPATH . $OrderDocs_Path . $torderdocuments_data['DocumentFileName'];

        if (!file_exists(FCPATH . $OrderDocs_Path)) {
            if(!mkdir(FCPATH .$OrderDocs_Path, 0777, true))
            {
                die('Unable to create Folder');
            }
        }  

        date_default_timezone_set('US/Eastern');
        $DocumentTypeUID = $this->common_model->getDocumentTypeUIDByDocTYpe('Reports');
        if(file_put_contents($destinationfile ,file_get_contents($doc_save)))
        {
            $torderdocuments_data['IsReport'] = 1;
            $torderdocuments_data['TypeOfDocument'] = $DocumentTypeUID;
            $torderdocuments_data['DocumentTypeUID'] = $DocumentTypeUID;
            $torderdocuments_data['DocumentCreatedDate'] = date('Y-m-d H:i:s');
            $torderdocuments_data['UploadedUserUID'] =  $this->loggedid;
            $torderdocuments_data['DisplayFileName'] =  $torderdocuments_data['DocumentFileName'];
            $this->load->model('order_search/ordersearch_model');
            //Default Current DateTime
            $checkdates= $this->ordersearch_model->CheckSearchDateExists($OrderUID);
            $checkdates = $checkdates->CheckDates;

            if ($checkdates == 0) {
                $torderdocuments_data['SearchAsOfDate'] = "0000-00-00 00:00:00";
                $torderdocuments_data['SearchFromDate'] = "0000-00-00 00:00:00";
            }
            else
            {
                $this->db->select('MAX(SearchAsOfDate) AS SearchAsOfDate, MAX(SearchFromDate) AS SearchFromDate', false)->from('torderdocuments');
                $this->db->where('OrderUID',$OrderUID);
                $result=$this->db->get();
                $torderdocuments=$result->row();
                
                $torderdocuments_data['SearchAsOfDate']=$torderdocuments->SearchAsOfDate;
                $torderdocuments_data['SearchFromDate']=$torderdocuments->SearchFromDate;
            }

            $result = $this->db->insert('torderdocuments', $torderdocuments_data);
            chmod($destinationfile, 0777);
            if($result)
            {
                $response['status'] = "Ok";
                $response['message'] = "Tax Cert Inserted into attachment";
            }
            else
            {
                $response['status'] = "Failed";
                $response['message'] = "Unable to Insert into attachment1";

            }
        }
        else
        {
            $response['status'] = "Faild";
            $response['message'] = "Unable to Insert into attachment2";

        }

        echo json_encode($response);

    }

function AddTaxCert()
{
    error_reporting(0);
    $this->load->library('Dom/Simple_html_dom');
    $doc = new simple_html_dom();
    $filename = FCPATH.'Templates/taxcert.php';
    $OrderUID = $this->input->post('OrderUID');
    $fp = fopen ( $filename, 'r' );
        //read our template into a variable
    $output = fread( $fp, filesize($filename));
        //Orders
    $torders_array = array();
    $keys = array();
    $values = array();
    $order_details = $this->Order_reports_model->get_torders($OrderUID);
    $SavePreviewOrderNumber = $order_details->OrderNumber;

    $date = $order_details->OrderEntryDatetime;
    $OrderDate =  date('m/d/Y', strtotime($date));
    $dates = array('OrderDate'=>$OrderDate);
    foreach ($dates as $key => $value) 
    {

        array_push($keys, '%%'.$key.'%%');
        array_push($values, $value);
    }
    $CurrentDate = array('CurrentDate'=>date("m/d/Y"));
    foreach ($CurrentDate as $key => $value) 
    {

        array_push($keys, '%%'.$key.'%%');
        array_push($values, $value);
    }

    $base_url = FCPATH;
    $Attention =  $order_details->AttentionName;
    if($Attention)
    {
        $AttentionName = $Attention;
    }
    else{

        $AttentionName = '-';
    }
    $CustomerPContactMobileNo = $order_details->CustomerPContactMobileNo;
    $CustomerAddress1 = $order_details->CustomerAddress1;
    if($CustomerAddress1)
    {
        $CustomerAddress1 =  $order_details->CustomerAddress1;
    }
    else{
        $CustomerAddress1 = ' ';
    }
    $CustomerZipCode = $order_details->CustomerZipCode;
    if($CustomerZipCode)
    {
        $CustomerZipCode = $order_details->CustomerZipCode;
    }
    else{
        $CustomerZipCode = ' ';
    }
    $CustomerAddress2 = $order_details->CustomerAddress2;
    if($CustomerAddress2)
    {
        $CustomerAddress2 =  $order_details->CustomerAddress2;
    }
    else{
        $CustomerAddress2 = '-';
    }
    $CustomerNumber = $order_details->CustomerNumber;
    if($CustomerNumber)
    {
        $CustomerNumber = $order_details->CustomerNumber.'/';
    }
    else{
        $CustomerNumber = '';
    }
    $CustomerName = $order_details->CustomerName;
    if($CustomerName)
    {
        $CustomerName = $order_details->CustomerName;
    }
    else{
        $CustomerName = '-';
    }
    $CustomerPContactName =  $order_details->CustomerPContactName;
    if($CustomerPContactName)
    {
        $CustomerPContactName =  $order_details->CustomerPContactName;
    }
    else{
        $CustomerPContactName = '-';
    }
    $CustomerStateCode =  $order_details->CustomerStateCode;
    if($CustomerStateCode)
    {
       $CustomerStateCode =  $order_details->CustomerStateCode.'-'; 
    }
    else
    {
        $CustomerStateCode =  ' '; 
    }
    $CustomerCountyName =  $order_details->CustomerCountyName;
    if($CustomerCountyName)
    {
        $CustomerCountyName =  $order_details->CustomerCountyName;
    }
    else{
        $CustomerCountyName = '-';
    }
    $CustomerCityName = $order_details->CustomerCityName;
    if($CustomerCityName)
    {
         $CustomerCityName = $order_details->CustomerCityName.',';
    }
    else{
        $CustomerCityName = ' ';
    }
    $Mortgagee = $order_details->Mortgagee;
    $OrderNumber = $order_details->OrderNumber;
    $LoanNumber = $order_details->LoanNumber;
    if($LoanNumber)
    {
        $LoanNumber = $order_details->LoanNumber;
    }
    else{
        $LoanNumber = '-';
    }
    $OwnerName = $order_details->OwnerName;
    $CountyName =  $order_details->CountyName;
    $PropertyAddress1 = $order_details->PropertyAddress1;
    $PropertyAddress2 = $order_details->PropertyAddress2.',';
    $CityName = $order_details->CityName.',';
    $StateName = $order_details->StateName;
    $StateCode = $order_details->StateCode;
    if($StateCode)
    {
        $StateCode = $order_details->StateCode;
    }
    else{
        $StateCode = '-';
    }
        $DisclaimerResult = $this->Order_reports_model->get_DisclaimerNote($order_details->StateCode);
        if($DisclaimerResult->DisclaimerNote){
            $DisclaimerNote = $DisclaimerResult->DisclaimerNote;
        }
        else{
             $DisclaimerNote = '-';
        }
        if($DisclaimerResult->StateEmail){
            $DisclaimerStateEmail = $DisclaimerResult->StateEmail;
        }
        else{
            $DisclaimerStateEmail = '-';
        }
        if($DisclaimerResult->StateWebsite){
            $DisclaimerStateWebsite = $DisclaimerResult->StateWebsite;
        }
        else{
            $DisclaimerStateWebsite = '-';
        }
        if($DisclaimerResult->StatePhoneNumber){
            $DisclaimerStatePhoneNumber = $DisclaimerResult->StatePhoneNumber;
        }
        else{
            $DisclaimerStatePhoneNumber = '-';
        }
        if($DisclaimerResult->LegalDisclaimerNote == 1 && $DisclaimerResult->TaxDisclaimerNote == 1)
        {
            $taxpagebreak = '<div style="page-break-after: always;"></div>';
            $pagebreak =   '<div style="page-break-after: auto;"></div>';
            $LegalDisclaimerNote = 'displayfooter';
            $TaxDisclaimerNote = 'displayfooter';
        }
        elseif($DisclaimerResult->LegalDisclaimerNote == 0 && $DisclaimerResult->TaxDisclaimerNote == 1){

 
                $LegalDisclaimerNote = 'noheaderfooter';
                $TaxDisclaimerNote = ' ';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = ' ';
            
                             
        }
        elseif($DisclaimerResult->LegalDisclaimerNote == 1 && $DisclaimerResult->TaxDisclaimerNote == 0)
        {
                $LegalDisclaimerNote = 'displayfooter';
                $TaxDisclaimerNote = 'noheaderfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = '<div style="page-break-after: auto;"></div>';
        }
        else{
            $LegalDisclaimerNote = 'noheaderfooter';
                $TaxDisclaimerNote = 'noheaderfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = '<div style="page-break-after: always;"></div>';
        }
    $SearchDate = $order_details->SearchThroDate;
    if($SearchDate == '0000-00-00')
    {
        $SearchThroDate =  '-';
    }
    else{
        $SearchThroDate =  date('m/d/Y', strtotime($SearchDate));
    }
    $SearchFrom = $order_details->SearchFromDate;
    if($SearchFrom == '0000-00-00 00:00:00' || $SearchFrom == '')
        {
            $SearchFromDate = '-';
        }
        else
        {
            $SearchFromDate = date('m/d/Y', strtotime($SearchFrom));
        }
        $SearchAsOf = $order_details->SearchAsOfDate;
        if($SearchAsOf == '0000-00-00 00:00:00' || $SearchAsOf == '')
            {
                $SearchAsOfDate = '-';
            }
            else
            {
                $SearchAsOfDate = date('m/d/Y', strtotime($SearchAsOf));
            }
            $ZipCode = $order_details->PropertyZipcode;
            $address = $this->Order_reports_model->get_Address($OrderUID);
            foreach ($address as $key => $data) {

                $AssessedCountyName = $data->AssessedCountyName;
                $USPSCountyName = $data->USPSCountyName;
            }
            $ImageUrl = base_url().'assets/img/sourcepoint.png';
            $GoogleMapAddress = $PropertyAddress1.' '.$PropertyAddress2.' '.$CityName.' '.$StateName.' '.$ZipCode;
            $Mort = array('CustomerName'=>$CustomerNumber.$CustomerName,'AttentionName'=>$AttentionName,'CustomerAddress1'=>$CustomerAddress1,'CustomerAddress2'=>$CustomerAddress2,'CustomerCountyName'=>$CustomerCountyName,'CustomerCityName'=>$CustomerCityName,'CustomerStateCode'=>$CustomerStateCode,'CustomerPContactName'=>$CustomerPContactName,'OrderMortgagee'=>$Mortgagee,'Ordernumber'=>$OrderNumber,'Loannumber'=>$LoanNumber,'Ownername'=>$OwnerName,'Countyname'=>$CountyName,'County_name'=>$CountyName.',','Propertyaddress1'=>$PropertyAddress1,'Propertyaddress2'=>$PropertyAddress2,'Cityname'=>$CityName,'Statecode'=>$StateCode.'-','Zip'=>$ZipCode,'SearchThroDate'=>$SearchThroDate,'GoogleMapAddress'=>$GoogleMapAddress,'SearchFromDate'=>$SearchFromDate,'SearchAsOfDate'=>$SearchAsOfDate,'DisclaimerNote'=>$DisclaimerNote,'Url'=>$base_url,'OrderDate'=>$OrderDate,'CustomerZipCode'=>$CustomerZipCode,'DisclaimerStateWebsite'=>$DisclaimerStateWebsite,'DisclaimerStateEmail'=>$DisclaimerStateEmail,'DisclaimerStatePhoneNumber'=>$DisclaimerStatePhoneNumber,'pagebreak'=>$pagebreak,'taxpagebreak'=>$taxpagebreak,'State_code'=>$StateCode,'ImageUrl'=>$ImageUrl);


            foreach ($Mort as $key => $value) 
            {

                array_push($keys, '%%'.$key.'%%');
                array_push($values, $value);
            }
        //Orders
        //Order Assessment
            $order_assessment = $this->Order_reports_model->get_OrderAssessment($OrderUID);
            if($order_assessment)
            {

                foreach ($order_assessment as $data_orderass_info) 
                {
                    $AssessedYear = $data_orderass_info->AssessedYear;

                    if($AssessedYear)
                    {
                        array_push($keys, '%%AssessedYear%%');
                        array_push($values, $AssessedYear);
                    }
                    else{
                        array_push($keys, '%%AssessedYear%%');
                        array_push($values, '-');

                    }
                    $Agricultural = $data_orderass_info->Agriculture;

                    if($Agricultural)
                        {   $Agricultural = '$'.number_format($Agricultural,2);
                    array_push($keys, '%%Agricultural%%');
                    array_push($values, $Agricultural);
                }
                else{
                    array_push($keys, '%%Agricultural%%');
                    array_push($values, '-');

                }
                $TotalValue = $data_orderass_info->TotalValue;

                if($TotalValue)
                {
                    array_push($keys, '%%TotalValue%%');
                    array_push($values, $TotalValue);
                }
                else{
                    array_push($keys, '%%TotalValue%%');
                    array_push($values, '-');

                }
                $Landstr = $data_orderass_info->Land;
                if($Landstr)
                {
                    $Landltrim = ltrim($Landstr, '$');
                    $LandRepl = str_replace(",","",$Landltrim);
                    $Lan = substr($LandRepl, 0,-3);
                    $Land = '$'.number_format($Lan,2);
                }
                else{
                    $Land = '-';
                }
                $Buildingsstr = $data_orderass_info->Buildings;
                if($Buildingsstr)
                {

                    $Buildingsltrim = ltrim($Buildingsstr, '$');
                    $BuildingsRepl = str_replace(",","",$Buildingsltrim);
                    $Build = substr($BuildingsRepl, 0,-3);
                    $Buildings = '$'.number_format($Build,2);
                }
                else{
                    $Buildings = '-';
                }
                $AssessmentValue = $data_orderass_info->AssessmentValue;
                if($AssessmentValue)
                {
                    $AssessmentValue = $AssessmentValue;
                    array_push($keys, '%%AssessmentValue%%');
                    array_push($values, $AssessmentValue);
                    array_push($keys, '%%alignment%%');
                    array_push($values, 'text-left');
                }
                else{
                    array_push($keys, '%%AssessmentValue%%');
                    array_push($values, '-');
                    array_push($keys, '%%alignment%%');
                    array_push($values, 'text-center');

                }

                $Value = array('TotalValue'=>$TotalValue,'Buildings'=>$Buildings,'Land'=>$Land,'Agricultural'=>$Agricultural,'AssessmentValue'=>$AssessmentValue);
                foreach ($Value as $key => $value) 
                {
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
            } 
        }
        else{


            $TaxArray = array('AssessedYear','Land','Buildings','TotalValue','Agricultural');
            foreach($TaxArray as $col)
            {
                array_push($keys, '%%'.$col.'%%');
                array_push($values, '-');
            }
             array_push($keys, '%%AssessmentValue%%');
             array_push($values, '-');
             array_push($keys, '%%alignment%%');
             array_push($values, 'text-center');
        }
        //Assessment
        //Get Exemption
        $exemptions = $this->Order_reports_model->get_taxExemption($OrderUID);
        if($exemptions)
        {
            $Exempt = array('Exempt'=>'Yes');
            foreach($Exempt as $col => $val)
            {
                array_push($keys, '%%'.$col.'%%');
                array_push($values, $val);
            }
        }
        else
        {
            $Exempt = array('Exempt'=>'No');
            foreach($Exempt as $col => $val)
            {
                array_push($keys, '%%'.$col.'%%');
                array_push($values, $val);
            }
        }
        //Get Exemption
        //Get Borrowers
            $borrowers = $this->Order_reports_model->getBorrowers($OrderUID);
            $Count =  count($borrowers);
            if($Count > 1){

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    if($value->PRName !=''){

                    $borrower .=  trim($value->PRName.',');
                    }
                }
                $borrowers =  rtrim($borrower,",");
                $Array = explode(",", $borrowers);
                $LastArrayValue =  end($Array);
                
                if(count($Array) > 1 ){
                    array_pop($Array);
                    $BorrowerName = implode(", &nbsp;", $Array).' and '.$LastArrayValue;

                }else{
                    
                    $BorrowerName = implode(", &nbsp;", $Array);
                }
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
            else{

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    $borrower .=  $value->PRName.',';

                }
                $BorrowerName =  rtrim($borrower,",");
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
            //Get Borrowers
            $output = str_replace($keys,$values, $output);
            $doc->load($output);
            //Tax Starts
            preg_match_all('/<div class=\"tordertaxcerts\">(.*?)<\/div>/s',$output,$tordertaxcerts);

            $tordertaxcerts_array = array();
            $tordertax_array = array();
            $tordertaxcerts_array = $this->Order_reports_model->get_tordertaxcerts($OrderUID);
            $tordertaxcerts_array_count = count($tordertaxcerts_array);
            $tordertaxcerts_table2="";
            $tordertaxcerts_table3="";
            for ($i=0; $i < $tordertaxcerts_array_count; $i++) 
            { 
                $tordertaxcerts_table = '';
                $tordertaxcerts_array[$i]->tax_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($tordertaxcerts_array[$i] as $key => $value)
                {
                         //Subdocument Type Name
                        $DocumentTypeName = $tordertaxcerts_array[$i]->DocumentTypeName;
                        if($DocumentTypeName)
                        {
                            if($DocumentTypeName == 'Others')
                            {
                                $TaxType = $tordertaxcerts_array[$i]->TaxType;
                                if($TaxType)
                                {
                                    array_push($keys, '%%TaxDocumentTypeName%%');
                                    array_push($values, $TaxType);
                                }
                                else
                                {
                                    array_push($keys, '%%TaxDocumentTypeName%%');
                                    array_push($values, '-');
                                }
                            }
                            else if($DocumentTypeName !== 'Others')
                            {
                                array_push($keys, '%%TaxDocumentTypeName%%');
                                array_push($values, $DocumentTypeName);
                            }
                            else
                            {
                                array_push($keys, '%%TaxDocumentTypeName%%');
                                array_push($values, '-');
                            }

                        }
                        else{
                            array_push($keys, '%%TaxDocumentTypeName%%');
                            array_push($values, '-');
                        }
                    //Subdocument Type Name
                    //Amount Paid
                    $AmountPaid = $tordertaxcerts_array[$i]->AmountPaid;
                    $AmtPaid = '$'.number_format($AmountPaid,2);
                    if($AmtPaid)
                    {
                        array_push($keys, '%%AmountPaid%%');
                        array_push($values, $AmtPaid);
                    }
                    else{
                        array_push($keys, '%%AmountPaid%%');
                        array_push($values, '-');
                    }
                    //Amount Paid
                    //Amount Due
                    $AmountDue = $tordertaxcerts_array[$i]->AmountDue;
                    $AmtDue = '$'.number_format($AmountDue,2);
                    if($AmtDue)
                    {
                        array_push($keys, '%%AmountDue%%');
                        array_push($values, $AmtDue);
                    }
                    else{
                        array_push($keys, '%%AmountDue%%');
                        array_push($values, '-');
                    }
                    //Amount Due
                    //Gross Amount
                    $GrossAmount = $tordertaxcerts_array[$i]->GrossAmount;
                    $GrossAmt = '$'.number_format($GrossAmount,2);
                    if($GrossAmt)
                    {
                        array_push($keys, '%%GrossAmount%%');
                        array_push($values, $GrossAmt);
                    }
                    else{
                        array_push($keys, '%%GrossAmount%%');
                        array_push($values, '-');
                    }
                    //Gross Amount
                    //ApprovedUnapprovedTaxAuthorityDetails
                    $UnapprovedTaxAuthorityDetails = $this->Order_reports_model->GetUnapprovedTaxAuthorityDetails($OrderUID,$tordertaxcerts_array[$i]->TaxAuthorityUID);
                    if($UnapprovedTaxAuthorityDetails)
                    {
                        //PaymentAddrLine1
                            $PaymentAddrLine1 = $UnapprovedTaxAuthorityDetails->PaymentAddrLine1;
                            if($PaymentAddrLine1)
                            {
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, $PaymentAddrLine1.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, ' ');
                            }
                        //PaymentAddrLine1
                        //PaymentAddrLine2
                            $PaymentAddrLine2 = $UnapprovedTaxAuthorityDetails->PaymentAddrLine2;
                            if($PaymentAddrLine2)
                            {
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, $PaymentAddrLine2.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, ' ');
                            }
                        //PaymentAddrLine2
                        //PaymentCity
                            $PaymentCity = $UnapprovedTaxAuthorityDetails->PaymentCity;
                            if($PaymentCity)
                            {
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, $PaymentCity.',');
                            }
                            else{
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, ' ');
                            }
                        //PaymentCity
                        //PaymentState
                            $PaymentState = $UnapprovedTaxAuthorityDetails->PaymentState;
                            if($PaymentState)
                            {
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, $PaymentState.'-');
                            }
                            else{
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, ' ');
                            }
                        //PaymentState
                        //PaymentZipCode
                            $PaymentZipCode = $UnapprovedTaxAuthorityDetails->PaymentZipCode;
                            if($PaymentZipCode)
                            {
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, $PaymentZipCode);
                            }
                            else{
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, ' ');
                            }
                        //PaymentZipCode


                        //Tax Collector Name
                            $TaxCollector = $UnapprovedTaxAuthorityDetails->TaxCollector;
                            if($TaxCollector)
                            {
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, $TaxCollector);
                            }
                            else{
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, '-');
                            }
                        //Tax Authority Name
                        //Tax Payable
                            $TaxPayable = $UnapprovedTaxAuthorityDetails->TaxPayable;
                            if($TaxPayable)
                            {
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, $TaxPayable);
                            }
                            else{
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, '-');
                            }
                        //Tax Payable
                        //Collector Phone
                        $CollectorPhone = $UnapprovedTaxAuthorityDetails->CollectorPhone;
                        if($CollectorPhone)
                        {
                            $numbers_only = preg_replace("/[^\d]/", "", $CollectorPhone);
                            $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "($1)&nbsp;$2-$3", $numbers_only);
                            array_push($keys, '%%CollectorPhoneNumber%%');
                            array_push($values, $number);
                        }
                        else{
                            array_push($keys, '%%CollectorPhoneNumber%%');
                            array_push($values, '-');
                        }
                        //Collector Phone
                        //Website Address
                        $WebsiteAddress = $UnapprovedTaxAuthorityDetails->WebsiteAddress;
                        if($WebsiteAddress)
                        {
                            array_push($keys, '%%WebsiteAddr%%');
                            array_push($values, '<u>'.$WebsiteAddress.'</u>');
                        }
                        else{
                            array_push($keys, '%%WebsiteAddr%%');
                            array_push($values, '-');
                        }
                        //Website Address
                        
                    }else{
                        $ApprovedTaxAuthorityDetails = $this->Order_reports_model->GetApprovedTaxAuthorityDetails($tordertaxcerts_array[$i]->TaxAuthorityUID);
                        if($ApprovedTaxAuthorityDetails)
                        {
                            //PaymentAddrLine1
                            $PaymentAddrLine1 =$ApprovedTaxAuthorityDetails->PaymentAddrLine1;
                            if($PaymentAddrLine1)
                            {
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, $PaymentAddrLine1.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, ' ');
                            }
                            //PaymentAddrLine1
                            //PaymentAddrLine2
                            $PaymentAddrLine2 =$ApprovedTaxAuthorityDetails->PaymentAddrLine2;
                            if($PaymentAddrLine2)
                            {
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, $PaymentAddrLine2.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, ' ');
                            }
                            //PaymentAddrLine2
                            //PaymentCity
                            $PaymentCity =$ApprovedTaxAuthorityDetails->PaymentCity;
                            if($PaymentCity)
                            {
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, $PaymentCity.',');
                            }
                            else{
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, ' ');
                            }
                            //PaymentCity
                            //PaymentState
                            $PaymentState =$ApprovedTaxAuthorityDetails->PaymentState;
                            if($PaymentState)
                            {
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, $PaymentState.'-');
                            }
                            else{
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, ' ');
                            }
                            //PaymentState
                            //PaymentZipCode
                            $PaymentZipCode =$ApprovedTaxAuthorityDetails->PaymentZipCode;
                            if($PaymentZipCode)
                            {
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, $PaymentZipCode);
                            }
                            else{
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, ' ');
                            }
                            //PaymentZipCode
                            //Tax Collector Name
                            $TaxCollector =$ApprovedTaxAuthorityDetails->TaxCollector;
                            if($TaxCollector)
                            {
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, $TaxCollector);
                            }
                            else{
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, '-');
                            }
                            //Tax Authority Name
                            //Tax Payable
                            $TaxPayable =$ApprovedTaxAuthorityDetails->TaxPayable;
                            if($TaxPayable)
                            {
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, $TaxPayable);
                            }
                            else{
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, '-');
                            }
                            //Tax Payable
                            //Collector Phone
                            $CollectorPhone =$ApprovedTaxAuthorityDetails->CollectorPhone;
                            if($CollectorPhone)
                            {
                                $numbers_only = preg_replace("/[^\d]/", "", $CollectorPhone);
                                $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "($1)&nbsp;$2-$3", $numbers_only);
                                array_push($keys, '%%CollectorPhoneNumber%%');
                                array_push($values, $number);
                            }
                            else{
                                array_push($keys, '%%CollectorPhoneNumber%%');
                                array_push($values, '-');
                            }
                            //Collector Phone
                            //Website Address
                            $WebsiteAddress = $ApprovedTaxAuthorityDetails->WebsiteAddress;
                            if($WebsiteAddress)
                            {
                                array_push($keys, '%%WebsiteAddr%%');
                                array_push($values, '<u>'.$WebsiteAddress.'</u>');
                            }
                            else{
                                array_push($keys, '%%WebsiteAddr%%');
                                array_push($values, '-');
                            }
                            //Website Address
                        }
                        else{
                            $data = array('PaymentAddrLine1','PaymentAddrLine2','PaymentCity','PaymentState','PaymentZipCode','TaxCollector');
                            foreach ($data as $value) {
                                array_push($keys, '%%'.$value.'%%');
                                array_push($values, ' ');
    
                            }
                            $datas = array('TaxPayable','CollectorPhoneNumber','WebsiteAddr');
                            foreach ($datas as $value) {
                                array_push($keys, '%%'.$value.'%%');
                                array_push($values, '-');
                            }
                        }
        
                    }
                    //ApprovedUnapprovedTaxAuthorityDetails

                //Amount Deliquent
                    $AmountDelinquent = $tordertaxcerts_array[$i]->AmountDelinquent;
                    $AmtDelinquent = '$'.number_format($AmountDelinquent,2);
                    if($AmtDelinquent)
                    {
                        array_push($keys, '%%AmountDelinquent%%');
                        array_push($values, $AmtDelinquent);
                    }
                    else{
                        array_push($keys, '%%AmountDelinquent%%');
                        array_push($values, '-');
                    }
                //Amount Deliquent
                //Account Number
                    $ParcelNumber = $tordertaxcerts_array[$i]->ParcelNumber;
                    if($ParcelNumber)
                    {
                        array_push($keys, '%%ParcelNumber%%');
                        array_push($values, $ParcelNumber);
                    }
                    else{
                        array_push($keys, '%%ParcelNumber%%');
                        array_push($values, '-');
                    }
                //Account Number
                //Estimated Tax
                    $EstimatedTax = $tordertaxcerts_array[$i]->EstimatedTax;
                    $EstimatedTax = '$'.number_format($EstimatedTax,2);
                    if($EstimatedTax)
                    {
                        array_push($keys, '%%EstimatedTax%%');
                        array_push($values, $EstimatedTax);
                    }
                    else{
                        array_push($keys, '%%EstimatedTax%%');
                        array_push($values, '-');
                    }
                //Estimated Tax
                    //Tax Basis Name
                    $TaxBasisName = $tordertaxcerts_array[$i]->TaxBasisName;
                    if($TaxBasisName)
                    {
                        array_push($keys, '%%TaxBasisName%%');
                        array_push($values, $TaxBasisName);
                    }
                    else{
                        array_push($keys, '%%TaxBasisName%%');
                        array_push($values, '-');
                    }
                    //Tax Basis Name
                    //Property Class Name
                    $PropertyClassName = $tordertaxcerts_array[$i]->PropertyClassName;
                    if($PropertyClassName)
                    {
                        array_push($keys, '%%PropertyClassName%%');
                        array_push($values, $PropertyClassName);
                    }
                    else{
                        array_push($keys, '%%PropertyClassName%%');
                        array_push($values, '-');
                    }
                   //Property Class Name
                    //Tax Comments
                        $TaxComments = $tordertaxcerts_array[$i]->TaxComments;
                        if($TaxComments)
                        {
                            array_push($keys, '%%TaxComments%%');
                            array_push($values, $TaxComments);
                            array_push($keys, '%%taxalignment%%');
                            array_push($values, 'text-left');
                        }
                        else{
                            array_push($keys, '%%TaxComments%%');
                            array_push($values, '-');
                            array_push($keys, '%%taxalignment%%');
                            array_push($values, 'text-center');
                        }
                    //Tax Comments
                    $TaxYears = $tordertaxcerts_array[$i]->TaxYear;
                    if($TaxYears)
                    {
                        array_push($keys, '%%TaxYears%%');
                        array_push($values, $TaxYears);
                    }
                    else{
                        array_push($keys, '%%TaxYears%%');
                        array_push($values, '-');
                    }
                //Tax Date Format Change
                    $NextTaxDueDate = $tordertaxcerts_array[$i]->NextTaxDueDate;
                    if($NextTaxDueDate == '0000-00-00')
                    {
                        array_push($keys, '%%NextTaxDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $NextTaxDate =  date('m/d/Y', strtotime($NextTaxDueDate));
                        array_push($keys, '%%NextTaxDate%%');
                        array_push($values, $NextTaxDate);
                    }
                //Tax Date Format Change
                //Tax Date Format Change
                    $GoodThroughDate = $tordertaxcerts_array[$i]->GoodThroughDate;
                    if($GoodThroughDate == '0000-00-00' || $GoodThroughDate == ' ' )
                    {
                        array_push($keys, '%%GoodThroughDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $GoodThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                        array_push($keys, '%%GoodThroughDate%%');
                        array_push($values, $GoodThroughDate);
                    }
                //Tax Date Format Change
                //Tax Date Paid
                    $DatePaid = $tordertaxcerts_array[$i]->DatePaid;
                    if($DatePaid == '0000-00-00')
                    {
                        array_push($keys, '%%PaidDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $PaidDate =  date('m/d/Y', strtotime($DatePaid));
                        array_push($keys, '%%PaidDate%%');
                        array_push($values, $PaidDate);
                    }
                //Tax Date Paid
                //Main Loop
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);    
                //Main Loop
                }
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $TaxExemptionNames = $this->Order_reports_model->getExemptionname($OrderUID,$TaxCert);
                if($TaxExemptionNames)
                {
                    array_push($keys, '%%TaxExemptionNames%%');
                    array_push($values, $TaxExemptionNames);
                }
                else{
                    array_push($keys, '%%TaxExemptionNames%%');
                    array_push($values, '-');
                }

                $tordertaxcerts_table .= str_replace($keys, $values, $tordertaxcerts[0][0]);
                $taxinstallment_html='<tr style="border: 0.01em solid grey;">
                <td  width="10.4%" class="td-bd text-center" style="">
                <p style="font-size: 8pt;" class="">%%Tax_Year%%</p>
                </td>
                <td width="17.8%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="text-center">%%Tax_InstallmentName%%</p>
                </td>
                <td  width="13.5" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="">%%Gross_Amount%%</p>
                </td>
                <td width="14.7%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="text-center">%%Tax_StatusName%%</p>
                </td>
                <td width="21.8%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class="">%%Amount_Paid%%</p>
                </td>
                <td width="21.8%" class="td-bd text-center"  style="">
                <p style="font-size: 8pt;" class=" text-center">%%Paid_Date%%</p>
                </td>
                </tr> ';
                $taxinstallment="";
                $taxinstallment_table = '';
                $taxinstallment_array = array();
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $taxinstallment_array = $this->Order_reports_model->gettaxinstallment($OrderUID, $TaxCert);
                $taxinstallment_array_count = count($taxinstallment_array);
                for ($k=0; $k < $taxinstallment_array_count; $k++) 
                { 

                    $keys = array();
                    $values = array();
                    foreach ($taxinstallment_array[$k] as $key => $value) 
                    {
                        $TaxYear = $taxinstallment_array[$k]->TaxYear;
                        if($TaxYear)
                        {
                            array_push($keys, '%%Tax_Year%%');
                            array_push($values, $TaxYear);

                        }
                        else{
                            array_push($keys, '%%Tax_Year%%');
                            array_push($values, '-');

                        }
                        $TaxInstallmentName = $taxinstallment_array[$k]->TaxInstallmentName;
                        if($TaxInstallmentName)
                        {
                            array_push($keys, '%%Tax_InstallmentName%%');
                            array_push($values, $TaxInstallmentName);

                        }
                        else{
                            array_push($keys, '%%Tax_InstallmentName%%');
                            array_push($values, '-');

                        }
                        $GrossAmount = $taxinstallment_array[$k]->GrossAmount;
                        if($GrossAmount)
                        {
                            $GrossAmount = '$'.number_format($GrossAmount,2);
                            array_push($keys, '%%Gross_Amount%%');
                            array_push($values, $GrossAmount);

                        }
                        else{
                            array_push($keys, '%%Gross_Amount%%');
                            array_push($values, '-');

                        }
                        $TaxStatusName = $taxinstallment_array[$k]->TaxStatusName;
                        if($TaxStatusName)
                        {
                            array_push($keys, '%%Tax_StatusName%%');
                            array_push($values, $TaxStatusName);

                        }
                        else{
                            array_push($keys, '%%Tax_StatusName%%');
                            array_push($values, '-');

                        }
                        $AmountPaid = $taxinstallment_array[$k]->AmountPaid;
                        if($AmountPaid)
                        {
                            $AmountPaid = '$'.number_format($AmountPaid,2);
                            array_push($keys, '%%Amount_Paid%%');
                            array_push($values, $AmountPaid);

                        }
                        else{
                            array_push($keys, '%%Amount_Paid%%');
                            array_push($values, '-');

                        }
                        $DatePaid = $taxinstallment_array[$k]->DatePaid;
                        if($DatePaid  == '0000-00-00')
                        {
                            array_push($keys, '%%Paid_Date%%');
                            array_push($values, '-');

                        }
                        else{
                            $DatePaid =  date('m/d/Y', strtotime($DatePaid));
                            array_push($keys, '%%Paid_Date%%');
                            array_push($values, $DatePaid);

                        }

                        $GoodThroughDate = $taxinstallment_array[$k]->GoodThroughDate;
                        if($GoodThroughDate  == '0000-00-00')
                        {
                            array_push($keys, '%%Good_Through_Date%%');
                            array_push($values, '-');

                        }
                        else{
                            $GoodThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                            array_push($keys, '%%Good_Through_Date%%');
                            array_push($values, $GoodThroughDate);

                        }
                    }

                    $taxinstallment .= str_replace($keys, $values, $taxinstallment_html);

                }
                $tordertaxcerts_table = str_replace('%%taxinstallment%%', $taxinstallment, $tordertaxcerts_table);


                $taxexemption="";
                $tordertaxemption_table = '';
                $tordertaxemption_array = array();
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $tordertaxemption_array = $this->Order_reports_model->gettaxExemption($OrderUID, $TaxCert);
                $tordertaxemption_array_count = count($tordertaxemption_array);
                $taxexemption_html='<tr class="tordertaxemption" style="border: 0.01em solid grey;">
                <td  rowspan="'. $tordertaxemption_array_count .'" width="20%" class="td-bd text-center" colspan="2" style="" >
                <p style="font-size: 8pt;" class="bold">Tax Exemption</p>
                </td>
                <td  width="30%" class="td-bd text-center" colspan="5" style="">
                <p style="font-size: 8pt;" class="text-center">%%TaxExemptionName%%</p>
                </td>
                <td  width="30%" class="td-bd text-center" colspan="5" style="">
                <p style="font-size: 8pt;" class="text-center">%%TaxAmount%%</p>
                </td>
                </tr>';
                for ($j=0; $j < $tordertaxemption_array_count; $j++) 
                {
                    if($j>0)
                    {
                      $taxexemption_html='<tr class="tordertaxemption" style="border: 0.01em solid grey;">
                        <td  width="30%" class="td-bd text-center" colspan="5" style="">
                        <p style="font-size: 8pt;" class="text-center">%%TaxExemptionName%%</p>
                        </td>
                        <td  width="30%" class="td-bd text-center" colspan="5" style="">
                        <p style="font-size: 8pt;" class="text-center">%%TaxAmount%%</p>
                        </td>
                        </tr>';

                    }
                    $keys = array();
                    $values = array();
                    foreach ($tordertaxemption_array[$j] as $key => $value) 
                    {
                        $TaxExemptionName = $tordertaxemption_array[$j]->TaxExemptionName;
                        if($TaxExemptionName)
                        {
                            array_push($keys, '%%TaxExemptionName%%');
                            array_push($values, $TaxExemptionName);

                        }
                        else{
                            array_push($keys, '%%TaxExemptionName%%');
                            array_push($values, '-');

                        }
                        $TaxAmount = $tordertaxemption_array[$j]->TaxAmount;
                        if($TaxAmount)
                        {
                            $TaxAmount = '$'.$TaxAmount;
                            array_push($keys, '%%TaxAmount%%');
                            array_push($values, $TaxAmount);

                        }
                        else{
                            array_push($keys, '%%TaxAmount%%');
                            array_push($values, '$0.00');

                        }

                    }

                    $taxexemption .= str_replace($keys, $values, $taxexemption_html);

                }
                $tordertaxcerts_table = str_replace('%%TaxExmp%%', $taxexemption, $tordertaxcerts_table);


 //Tax Exemption------------------------------------------------------------------------------------------
                $tordertaxcerts_table2 .= $tordertaxcerts_table;
            }

            foreach ( $doc->find(".tordertaxcerts") as $node ) 
            {
                $node->innertext = $tordertaxcerts_table2;
            }
        //Tax Ends

        $this->load->library('pdf');
        $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        $pdf = $this->pdf->load($param);
        $pdf->SetProtection(array('copy','print','modify','annot-forms','fill-forms','extract','assemble','print-highres'));
        $pdf->packTableData = true;
        $pdf->shrink_tables_to_fit = 1;
        // $pdf->list_indent_first_level = 0; 
        $html = mb_convert_encoding($doc, 'UTF-8', 'UTF-8');
        $pdf->WriteHTML($html); // write the HTML into the PDF
        $page_count = $pdf->page;
        $doc_save = $SavePreviewOrderNumber.'_TaxCert.pdf';
        $pdf->Output($doc_save, '');

        $this->db->order_by('Id', 'ASC');
        $query=$this->db->get_where('torderpropertyroles', array('PropertyRoleUID'=>5,'OrderUID'=>$OrderUID));
        $troderpropertyroles=$query->row();

        $torderdocuments_data['OrderUID'] = $OrderUID;
        $torders = $this->Order_reports_model->Gettorders($torderdocuments_data);


        if($troderpropertyroles->PRName!='')
        {
            $DocumentFileName = $torders['LoanNumber'].'-'.$troderpropertyroles->PRName.'_TaxCert';        
        }
        elseif($torder['LoanNumber']!='')
        {
            $DocumentFileName = $torders['LoanNumber'].'_TaxCert';
        }
        else
        {
            $DocumentFileName='TaxCert';
        }

        $DocumentFileName = str_replace(array('[',']','/', '\\',':','*','?','"','<','>','|'), '-',$DocumentFileName);


        $torderdocuments_data['DocumentFileName'] = $DocumentFileName . '.pdf';

        $this->db->where('OrderUID', $torderdocuments_data['OrderUID']);
        $this->db->like('DocumentFileName', $DocumentFileName);
        $query=$this->db->get('torderdocuments');
        $reportfilenamecount=$query->num_rows();

        if($reportfilenamecount>0)
        {
           $torderdocuments_data['DocumentFileName']=$this->common_model->GetAvailFileName($DocumentFileName,'.pdf',$reportfilenamecount, $OrderUID);
        }

        $torderdocuments_data['DisplayFileName']=$torderdocuments_data['DocumentFileName'];
        $OrderDocs_Path = $torders['OrderDocsPath']; 


        $date = date('Y-m-d');
        //Check Order Docuemnt Path exist.
        if(empty($OrderDocs_Path))
        {

            $query = $this->db->query("update torders SET OrderDocsPath ='uploads/searchdocs/".$date."/".$torders['OrderNumber']."/"."' Where OrderUID=".$torderdocuments_data['OrderUID']);

            $OrderDocs_Path = 'uploads/searchdocs/'.$date.'/'.$torders['OrderNumber']."/";
        }
        $destinationfile = FCPATH . $OrderDocs_Path . $torderdocuments_data['DocumentFileName'];

        if (!file_exists(FCPATH . $OrderDocs_Path)) {
            if(!mkdir(FCPATH .$OrderDocs_Path, 0777, true))
            {
                die('Unable to create Folder');
            }
        }  

        date_default_timezone_set('US/Eastern');
        $DocumentTypeUID = $this->common_model->getDocumentTypeUIDByDocTYpe('Reports');
        if(file_put_contents($destinationfile ,file_get_contents($doc_save)))
        {
            $torderdocuments_data['IsReport'] = 1;
            $torderdocuments_data['TypeOfDocument'] = $DocumentTypeUID;
            $torderdocuments_data['DocumentTypeUID'] = $DocumentTypeUID;
            $torderdocuments_data['DocumentCreatedDate'] = date('Y-m-d H:i:s');
            $torderdocuments_data['UploadedUserUID'] =  $this->loggedid;


            $result = $this->db->insert('torderdocuments', $torderdocuments_data);
            chmod($destinationfile, 0777);
            if($result)
            {
                $response['status'] = "Ok";
                $response['message'] = "Tax Cert Inserted into attachment";
            }
            else
            {
                $response['status'] = "Failed";
                $response['message'] = "Unable to Insert into attachment1";

            }
        }
        else
        {
            $response['status'] = "Faild";
            $response['message'] = "Unable to Insert into attachment2";

        }

        echo json_encode($response);

    }


    function Preview_info()
   {
        error_reporting(0);
        $OrderUID = $this->input->post('OrderUID');

        $order_details = $this->Order_reports_model->get_torders($OrderUID);
        $SavePreviewOrderNumber = $order_details->OrderNumber;


        $GetTemplate = $this->Order_reports_model->GetTemplateByOrderinFo($OrderUID);
        $doc = FCPATH.$GetTemplate->FilePathName;

        $GetTemplateUID = $this->Order_reports_model->GetTemplateUID($OrderUID);
        $PageSize = $GetTemplateUID->PageSize;
        $MarginTop = $GetTemplateUID->MarginTop;
        $MarginBottom = $GetTemplateUID->MarginBottom;
        $MarginLeft = $GetTemplateUID->MarginLeft;
        $MarginRight = $GetTemplateUID->MarginRight;

        $FirstMarginTop = $GetTemplateUID->FirstMarginTop;
        $FirstMarginBottom = $GetTemplateUID->FirstMarginBottom;
        $FirstMarginRight = $GetTemplateUID->FirstMarginRight;
        $FirstMarginLeft = $GetTemplateUID->FirstMarginLeft;

        if($FirstMarginTop){
            $FirstTop = $FirstMarginTop;
        } else {
            $FirstTop = $MarginTop;
        }

        if($FirstMarginBottom){
            $FirstBottom = $FirstMarginBottom;
        } else {
            $FirstBottom = $MarginBottom;
        }

        if($FirstMarginRight){
            $FirstRight = $FirstMarginRight;
        } else {
            $FirstRight = $MarginRight;
        }

        if($FirstMarginLeft){
            $FirstLeft = $FirstMarginLeft;
        } else {
            $FirstLeft = $MarginLeft;
        }

        $filecontents = file_get_contents($doc);  

        $filecontents = str_replace( 'style="color: blue;"'  , 'style="color: black;"' , $filecontents );  

        $filecontents.= "  <style> @page :first {margin-top:".$FirstTop."mm;margin-bottom:".$FirstBottom."mm;margin-left:".$FirstLeft."mm;margin-right:".$FirstRight."mm; }  @page {sheet-size:".$PageSize.";header: html_MyCustomHeader;footer: html_MyCustomFooter;margin-top:".$MarginTop."mm;margin-bottom:".$MarginBottom."mm;margin-left:".$MarginLeft."mm;margin-right:".$MarginRight."mm; }</style> ";

        $this->load->library('pdf');
        $param = '"en-GB-x","'.$PageSize.'","","","'.$MarginLeft.'","'.$MarginRight.'","'.$MarginTop.'","'.$MarginBottom.'",6,3';
        unset($pdf);
        $pdf = $this->pdf->load($param);      
        $pdf->SetProtection(array('copy','print','modify','annot-forms','fill-forms','extract','assemble','print-highres'));
        $pdf->packTableData = true;
        $pdf->shrink_tables_to_fit = 0;
        // $pdf->list_indent_first_level = 0;
        $html = mb_convert_encoding($filecontents, 'UTF-8', 'UTF-8');
        $pdf->WriteHTML($html); // write the HTML into the PDF
        $page_count = $pdf->page;
        $doc_save = $SavePreviewOrderNumber.'.pdf';
        $pdf->Output($doc_save, '');
        $dir = FCPATH.'Templates/Pdf/'.$SavePreviewOrderNumber.'.pdf';
        file_put_contents($dir,file_get_contents($doc_save));

   }

    function isgn_property_report_stewart($OrderUID)
    {
        error_reporting(0);
        $this->load->library('Ddom/Simple_html_dom');
        $doc = new simple_html_dom();
        $order_details = $this->Order_reports_model->get_torders($OrderUID);
        $StateCode = $order_details->StateCode;
        $filename = FCPATH.'Templates/isgnpropertyreportstewart.php';
        $OrderUID = $OrderUID;
        $fp = fopen ( $filename, 'r' );
        //read our template into a variable
        $output = fread( $fp, filesize($filename));
        //Orders
        $torders_array = array();
        $keys = array();
        $values = array();
        $order_details = $this->Order_reports_model->get_torders($OrderUID);
        
        $date = $order_details->OrderEntryDatetime;
        $OrderDate =  date('m/d/Y', strtotime($date));
        $dates = array('OrderDate'=>$OrderDate);
        foreach ($dates as $key => $value) 
        {
            array_push($keys, '%%'.$key.'%%');
            array_push($values, $value);
        }
        $CurrentDate = array('CurrentDate'=>date("m/d/Y"));
        foreach ($CurrentDate as $key => $value) 
        {

            array_push($keys, '%%'.$key.'%%');
            array_push($values, $value);
        }

        //Tax Certificate
        $TaxCertificateRequired = $order_details->TaxCertificateRequired;
        $WorkflowModuleUID = $this->Order_reports_model->GetWorkflowModuleUID($OrderUID);
        $workflowModuleArray = array();
        foreach ($WorkflowModuleUID as $key => $value){
            $workflowModuleArray[$key] = $value['WorkflowModuleUID'];
        }

        $tordertaxcerts_array = $this->Order_reports_model->get_tordertaxcerts($OrderUID);
        $tordertaxcerts_array_count = count($tordertaxcerts_array);

        $taxcert_html = ' ';
        $NumTotalWOExempt = '';
        $sep_taxcert_html = ' ';
        if($order_details->PropertyStateCode == 'TX'){

            $TotalTaxBaseAmt = [];
            $TotalTaxDueAmt = [];
            $TotalWOExempt = [];

            $taxcert_html.='<div class="">
            <div style="page-break-inside: avoid;">
            <table style="width: 100%;margin-top: 10pt;" cellspacing="0">
            <thead>
            <tr>
            <th class="blur text-center" colspan="5">
            <p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Summary of All Account(s)</p>
            </th>
            </tr>
            </thead>
            <tbody>
            <tr class="br-black br-b-trans">
            <td style="width: 30%;">
            <p style="font-size: 7pt;"></p>
            </td>
            <td colspan="2" style="width: 25%;" class="td-ta-r">
            <p style="font-size: 7pt;" class="bold">SUMMARY OF CURRENT YEAR</p>
            </td>
            <td colspan="2" style="width: 25%;" class="td-ta-r">
            <p style="font-size: 7pt;" class="bold ">SUMMARY OF ALL TAXES DUE</p>
            </td>
            </tr>

            <tr class="br-black br-t-trans">
            <td>
            <p style="font-size: 7pt;" class="bold td-ta-r"></p>
            </td>
            <td class="td-ta-r">
            <p style="font-size: 7pt;" class="bold td-ta-r">TAX YEAR</p>
            </td>
            <td class="td-ta-r">
            <p style="font-size: 7pt;" class="bold td-ta-r">BASE TAX</p>
            </td>
            <td class="td-ta-r">
            <p style="font-size: 7pt;" class="bold td-ta-r">DUE  %%CurrentMonth%%</p>
            </td>
            <td class="td-ta-r">
            <p style="font-size: 7pt;" class="bold td-ta-r">DUE  %%NextMonth%%</p>
            </td>
            </tr>';

            for ($i = 0; $i < $tordertaxcerts_array_count; $i++) {

                $DocumentTypeName = $tordertaxcerts_array[$i]->DocumentTypeName;
                if ($DocumentTypeName) {
                    if ($DocumentTypeName == 'Others') {
                        $TaxType = $tordertaxcerts_array[$i]->TaxType;
                        if ($TaxType) {
                            $TaxDocumentTypeName = $TaxType;
                        } else {
                            $TaxDocumentTypeName = '-';
                        }
                    } else if ($DocumentTypeName !== 'Others') {
                        $TaxDocumentTypeName = $DocumentTypeName;
                    } else {
                        $TaxDocumentTypeName = '-';
                    }
                } else {
                    $TaxDocumentTypeName = '-';
                }

                $WOExempt = $tordertaxcerts_array[$i]->WOExempt;
                if ($WOExempt) {
                    $TotalWOExempt[] = $WOExempt;
                } else {
                    $TotalWOExempt[] = 0;
                }

                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $taxinstallment_array = $this->Order_reports_model->GetTaxInstallmentGroupByYear($OrderUID, $TaxCert);
                $taxinstallment_array_count = count($taxinstallment_array);
                $TotalDueAmount = array();
                $TotalBaseAmount = array();

                for ($k = 0; $k < $taxinstallment_array_count; $k++) {
                    $keys = array();
                    $values = array();
                    foreach($taxinstallment_array[$k] as $key => $value) {
                        $TotalBaseAmt = $taxinstallment_array[$k]->TotalBaseAmt;
                        $TotalPaidAmt = $taxinstallment_array[$k]->TotalPaidAmt;
                        $TotalTaxStatus = $taxinstallment_array[$k]->TotalTaxStatus;

                        $DueAmount = [];
                        $GrossAmount = [];
                        $TaxYear = $taxinstallment_array[$k]->TaxYear;
                        $taxdue = $this->Order_reports_model->GetTaxDueDate($OrderUID, $TaxCert, $TaxYear);

                        foreach($taxdue as $key => $value) {
                            $DueAmount[] = $value->TotalPaidAmt;
                            $GrossAmount[] = $value->TotalGrossAmt;
                        }

                        $TotalAmountPaid = array_sum($DueAmount);
                        $TotalGrossAmount = array_sum($GrossAmount);
                        $TotalBaseAmt = $taxinstallment_array[$k]->TotalBaseAmt;

                       /* if ($TotalAmountPaid == 0) {
                            $TaxDueAmount = 0;
                        } else {*/
                            $TaxDueAmount = $TotalGrossAmount - $TotalAmountPaid;
                        /*}*/

                        $TaxYear = $taxinstallment_array[0]->TaxYear;
                        $BaseAmt = $taxinstallment_array[0]->TotalBaseAmt;
                        $NumBaseAmt = number_format($BaseAmt, 2);
                    }

                    $TotalDueAmount[] = $TaxDueAmount;
                    $TotalDue = array_sum($TotalDueAmount);
                    $NumTotalDue = number_format($TotalDue, 2);
                }

                $TotalTaxBaseAmt[] = $BaseAmt;
                $TotalTaxDueAmt[] = $TotalDue;

                $taxcert_html.= '<tr class="br-black br-b-trans br-t-trans"> <td >
                <p style = "font-size: 7pt;"
                class = "td-ta-l" > '.$TaxDocumentTypeName.' </p> </td> <td class = "td-ta-r" >
                <p style = "font-size: 7pt;"
                class = "bold td-ta-r" > '.$TaxYear.' </p> </td> <td class = "td-ta-r" >
                <p style = "font-size: 7pt;"
                class = "td-ta-r" > '.$NumBaseAmt.' </p> </td> <td class = "td-ta-r" >
                <p style = "font-size: 7pt;"
                class = "td-ta-r" > '.$NumTotalDue.' </p> </td> <td class = "td-ta-r" >
                <p style = "font-size: 7pt;"
                class = "td-ta-r" > '.$NumTotalDue.' </p> </td> </tr>';

            }

            $TotalWOExempt = array_sum($TotalWOExempt);
            $NumTotalWOExempt = number_format($TotalWOExempt, 2);

            $TotalTaxBaseAmt = array_sum($TotalTaxBaseAmt);
            $NumTotalTaxBaseAmt = number_format($TotalTaxBaseAmt, 2);
            $TotalTaxDueAmt = array_sum($TotalTaxDueAmt);
            $NumTotalTaxDueAmt = number_format($TotalTaxDueAmt, 2);

            $taxcert_html.= '<tr class="br-black br-t-trans"> <td >
            <p style = "font-size: 7pt;"
            class = "td-ta-l" > TOTAL TAX </p> </td> <td class = "td-ta-r" >
            <p style = "font-size: 7pt;"
            class = "bold td-ta-r" > </p> </td> <td class = "td-ta-r" >
            <p style = "font-size: 7pt;"
            class = "bold td-ta-r" > '.$NumTotalTaxBaseAmt.' </p> </td> <td class = "td-ta-r" >
            <p style = "font-size: 7pt;"
            class = "bold td-ta-r" > '.$NumTotalTaxDueAmt.' </p> </td> <td class = "td-ta-r" >
            <p style = "font-size: 7pt;"
            class = "bold td-ta-r" > '.$NumTotalTaxDueAmt.' </p> </td> </tr> </tbody>
            </table>
            </div>
            </div>
            </div>
            </div>';

        }

        $LegalDesc = $this->Order_reports_model->GetPropertyLegalDesc($OrderUID);

        $LegalDescription = $LegalDesc->LegalDescription;
        //$LegalDescription = nl2br($LegalDesc->LegalDescription);
        /*if($LegalDescription)
        {
            $LegalDescription = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Legal Description</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;'.nl2br($LegalDescription).' </td></tr></table>';
        }else{
            $LegalDescription = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Legal Description</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;None </td></tr></table>';
        }   */

        $Taxcertificate ='';
        $TaxSection ='';

        if($TaxCertificateRequired == 'Seperate Report' && in_array('3', $workflowModuleArray))
        {
            $Taxcertificate.= '<div class="wrapper"> <div style="width: 80%; padding: 0pt 50pt;"> <h2 style="text-align: center;font-size: 14pt;">Tax Certificate</h2> </div><p class="blur text-center" style="font-size: 10pt;font-weight: bold;margin-top: 10px;">Order Information</p><div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tbody> <tr style="height: 23px;"> <td class="td-bd" style="width: 32%; height: 23px;" rowspan="4">%%CustomerName%% <br>%%CustomerAddress1%% %%CustomerAddress2%% %%CustomerCountyName%% %%CustomerCityName%% %%CustomerStateCode%% %%CustomerZipCode%%</td><td class="td-bd text-center bold" style="width: 15%; height: 23px;">Ordered Date</td><td class="td-bd text-center" style="width: 20%; height: 23px;">%%OrderDate%%</td><td class="td-bd text-center bold" style="width: 13%; height: 30px;" rowspan="2">Order #</td><td class="td-bd text-center" style="width: 13%; height: 23px;" rowspan="2">%%Ordernumber%%</td></tr><tr style="height: 23px;"> <td class="td-bd text-center bold" style="width: 15%; height: 23px;">County</td><td class="td-bd text-center" style="width: 20%; height: 23px;">%%Countyname%% </td></tr><tr style="height: 23px;"> <td class="td-bd text-center bold" style="width: 15%; height: 23px;">State</td><td class="td-bd text-center" style="width: 20%; height: 23px;">%%State_code%%</td><td class="td-bd text-center bold" style="width: 13%; height: 23px; " rowspan="2">Loan #</td><td class="td-bd text-center" style="width: 13%; height: 23px;" rowspan="2">%%Loannumber%%</td></tr></tbody> </table>';

            $Taxcertificate .= '<p class="blur text-center" style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;">Property Information</p><div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="td-bd text-center bold" style="width: 14.11%;;" rowspan="2">Borrower</td><td class="td-bd text-center" style="width:27.6%;;" rowspan="2">%%Borrowers%%</td><td class="td-bd text-center bold" colspan="4">Assessment Information</td></tr><tr> <td class="td-bd text-center bold" style="width:16.5%;">Land Value</td><td class="td-bd text-center" style="width:17.8%;">%%Land%%</td><td class="td-bd text-center bold" style="width:11.6%;">Year</td><td class="td-bd text-center" style="width:11.6%;">%%AssessedYear%%</td></tr><tr> <td class="td-bd text-center bold" style="width: 14.11%;" rowspan="3">Property Address</td><td class="td-bd text-center" style="width: 27.6%;" rowspan="3">%%Propertyaddress1%% %%Propertyaddress2%% %%Cityname%% %%Statecode%% %%Zip%%</td><td class="td-bd text-center bold" style="width:16.5%;">Improvement Value</td><td class="td-bd text-center" style="width:11.6%;">%%Buildings%%</td><td class="td-bd text-center bold" style="width:23.2%;" colspan="2">Total Assessment</td></tr><tr> <td class="td-bd text-center bold" style="width:16.5%;">Agricultural Value</td><td class="td-bd text-center" style="width:17.8%;">%%Agricultural%%</td><td class="td-bd text-center" style="width:23.2%;vertical-align: middle;" colspan="2" rowspan="2">%%TotalValue%%</td></tr><tr> <td class="td-bd text-center bold" style="width:16.5%;">Exemptions</td><td class="td-bd text-center" style="width:17.8%;">%%Exempt%%</td></tr><tr> <td class="td-bd text-center bold" style="width:16.5%;">Comments</td><td class="td-bd %%alignment%%" colspan="5" style="">%%AssessmentValue%%</td></tr></table> </div>';

            $Taxcertificate .= '<div style="padding-top: 10pt;"></div><div class="tordertaxcerts"> <table style="width: 100%;margin-top: 10pt;border:0px;" cellspacing="0"> <thead> <tr> <th class="blur text-center" colspan="4"> <p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Tax Information</p></th> </tr></thead> <tbody> <tr style=""> <td width="14.7%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="bold">Taxing Entity</p></td><td width="27.6%" class="td-bd text-center" style=""> <p style="font-size: 8pt;text-transform: uppercase;" class="text-center">%%TaxDocumentTypeName%%</p></td><td width="14.7%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="bold">&nbsp;Parcel ID&nbsp;</p></td><td width="43.5%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="text-center">%%ParcelNumber%%</p></td></tr><tr style=""> <td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Tax Installment</p></td><td width="27.6%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center">%%TaxBasisName%%</p></td><td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Property Type</p></td><td width="43.5%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center">%%PropertyClassName%%</p></td></tr></tbody> </table> <table style="width: 100%;border: 0.01em solid grey;" cellspacing="0"> <tr style=""> <td width="10.4%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Tax Year</p></td><td width="15.6%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center bold">Tax Installments</p></td><td width="13.5%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Base Amount</p></td><td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center bold">Tax Status</p></td><td width="15.4%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Paid Amount</p></td><td width="15.2%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Paid Date</p></td><td width="15.2%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Next Due Date</p></td></tr>%%taxinstallment%% </table> <table style="width: 100%; margin-top: 10pt;" cellspacing="0">%%TaxExmp%%</table> <table style="width: 100%; margin-top: 10pt;" cellspacing="0"> <tbody> <tr style=""> <td class="td-bd bold text-center" style="width: 25%;font-size: 8pt;">Total Delinquent Payoff</td><td class="td-bd text-center" style="width: 25%;font-size: 8pt;">%%AmountDelinquent%%</td><td class="td-bd bold text-center" style="width: 25%;font-size: 8pt;">Good Through Date</td><td class="td-bd text-center" style="width: 25%;font-size: 8pt;">%%GoodThroughDate%%</td></tr><tr style=""> </tr><tr style=""> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 7pt;" colspan="1">Comments</td><td class="td-bd %%taxalignment%%" style="font-size: 7pt;" colspan="3">%%TaxComments%%</td></tr></tbody> </table> <div style="page-break-inside: avoid;"> <table style="width: 100%;margin-top: 10pt;" cellspacing="0"> <thead> <tr> <th class="blur text-center" colspan="14"> <p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Tax Collector Information</p></th> </tr></thead> <tbody> <tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;">Payable to</td><td class="td-bd text-center" colspan="13" style="width: 72.3774%;font-size: 8pt;">%%TaxPayable%%</td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;">Address</td><td class="td-bd text-center" colspan="13" style="width: 72.3774%;font-size: 8pt;">%%PaymentAddrLine1%% %%PaymentAddrLine2%% %%PaymentCity%% %%PaymentState%% %%PaymentZipCode%%</td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;">Phone #</td><td class="td-bd text-center" colspan="4" style=""> <p style="font-size: 8pt;" class="text-center">%%CollectorPhoneNumber%%</p></td><td class="td-bd text-center" colspan="2" style=""> <p style="font-size: 8pt;" class="bold">Web Address</p></td><td class="td-bd text-center" colspan="7" style=""> <p style="font-size: 8pt;color: #0066ff" class="text-center">%%WebsiteAddr%%</p></td></tr></tbody> </table> </div></div></div></div>';
            array_push($keys, '%%TaxCert%%');
            array_push($values, $Taxcertificate);
            array_push($keys, '%%TaxSection%%');
            array_push($values, ' ');
        }
        else if($TaxCertificateRequired == 'No Tax Certificate')
        {
            $this->save_view($OrderUID);
            array_push($keys, '%%TaxSection%%');
            array_push($values, ' ');
            array_push($keys, '%%TaxCert%%');
            array_push($values, ' ');
            array_push($keys,'%%taxpagebreak%%');
            array_push($values, ' ');
            array_push($keys,'%%TaxDisclaimerNote%%');
            array_push($values, 'noheaderfooter');

        }
        else{

            $TaxSection .=' <div class="tordertaxcerts"> <table style="width: 100%;margin-top: 10pt;border:0px;" cellspacing="0"> <thead> <tr> <th class="blur text-center" colspan="4"> <p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Tax Information</p></th> </tr></thead> <tbody> <tr style=""> <td width="14.7%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="bold">Taxing Entity</p></td><td width="27.6%" class="td-bd text-center" style=""> <p style="font-size: 8pt;text-transform: uppercase;" class="text-center">%%TaxDocumentTypeName%%</p></td><td width="14.7%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="bold">&nbsp;Parcel ID&nbsp;</p></td><td width="43.5%" class="td-bd text-center" style=""> <p style="font-size: 8pt;" class="text-center">%%ParcelNumber%%</p></td></tr><tr style=""> <td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Tax Installment</p></td><td width="27.6%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center">%%TaxBasisName%%</p></td><td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Property Type</p></td><td width="43.5%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center">%%PropertyClassName%%</p></td></tr></tbody> </table> <table style="width: 100%;border: 0.01em solid grey;" cellspacing="0"> <tr style=""> <td width="10.4%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Tax Year</p></td><td width="15.6%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center bold">Tax Installments</p></td><td width="13.5%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Base Amount</p></td><td width="14.7%" class="td-bd text-center"> <p style="font-size: 8pt;" class="text-center bold">Tax Status</p></td><td width="15.4%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Paid Amount</p></td><td width="15.4%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Paid Date</p></td><td width="15.2%" class="td-bd text-center"> <p style="font-size: 8pt;" class="bold">Next Due Date</p></td></tr>%%taxinstallment%% </table> <table style="width: 100%; margin-top: 10pt;" cellspacing="0">%%TaxExmp%%</table> <table style="width: 100%; margin-top: 10pt;" cellspacing="0"> <tbody> <tr style=""> <td class="td-bd bold text-center" style="width: 25%;font-size: 8pt;">Total Delinquent Payoff</td><td class="td-bd text-center" style="width: 25%;font-size: 8pt;">%%AmountDelinquent%%</td><td class="td-bd bold text-center" style="width: 25%;font-size: 8pt;">Good Through Date</td><td class="td-bd text-center" style="width: 25%;font-size: 8pt;">%%GoodThroughDate%%</td></tr><tr style=""> </tr><tr style=""> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;" colspan="1">Comments</td><td class="td-bd %%taxalignment%%" style="font-size: 8pt;" colspan="3">%%TaxComments%%</td></tr></tbody> </table> <div style="page-break-inside: avoid;"> <table style="width: 100%;margin-top: 10pt;" cellspacing="0"> <thead> <tr> <th class="blur text-center" colspan="14"> <p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Tax Collector Information</p></th> </tr></thead> <tbody> <tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;">Payable to</td><td class="td-bd text-center" colspan="13" style="width: 72.3774%;font-size: 8pt;">%%TaxPayable%%</td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;">Address</td><td class="td-bd text-center" colspan="13" style="width: 72.3774%;font-size: 8pt;">%%PaymentAddrLine1%% %%PaymentAddrLine2%% %%PaymentCity%% %%PaymentState%% %%PaymentZipCode%%</td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd bold text-center" style="width: 26.6226%;font-size: 8pt;">Phone #</td><td class="td-bd text-center" colspan="4" style=""> <p style="font-size: 8pt;" class="text-center">%%CollectorPhoneNumber%%</p></td><td class="td-bd text-center" colspan="2" style=""> <p style="font-size: 8pt;" class="bold">Web Address</p></td><td class="td-bd text-center" colspan="7" style=""> <p style="font-size: 8pt;color:#0066ff" class="text-center">%%WebsiteAddr%%</p></td></tr></tbody> </table> </div></div>';
            array_push($keys, '%%TaxSection%%');
            array_push($values, $TaxSection);
            array_push($keys, '%%TaxCert%%');
            array_push($values, ' ');
            array_push($keys,'%%taxpagebreak%%');
            array_push($values, ' ');
            array_push($keys,'%%TaxDisclaimerNote%%');
            array_push($values, 'noheaderfooter');
        }
        //Tax Certificate

        $ReportHeading = $order_details->ReportHeading;
        if($ReportHeading)
        {
            array_push($keys, '%%ReportHeading%%');
            array_push($values, $ReportHeading);
        }
        else{
            array_push($keys, '%%ReportHeading%%');
            array_push($values, 'Property Report');
        }
        
        $base_url = FCPATH;
        $Attention =  $order_details->AttentionName;
        if($Attention)
        {
            $AttentionName = $Attention;
        }
        else{

            $AttentionName = '-';
        }
        $CustomerPContactMobileNo = $order_details->CustomerPContactMobileNo;
        $CustomerAddress1 = $order_details->CustomerAddress1;

        if($CustomerAddress1)
        {
            $CustomerAddress1 =  $order_details->CustomerAddress1;
        }
        else{
            $CustomerAddress1 = ' ';
        }
        $CustomerAddress2 = $order_details->CustomerAddress2;
        if($CustomerAddress2)
        {
            $CustomerAddress2 =  $order_details->CustomerAddress2.',';
        }
        else{
            $CustomerAddress2 = ' ';
        }
        $CustomerNumber = $order_details->CustomerNumber;
        if($CustomerNumber)
        {
            $CustomerNumber = $order_details->CustomerNumber.'/';
        }
        else{
            $CustomerNumber = '';
        }
        $CustomerName = $order_details->CustomerName;
        if($CustomerName)
        {
            $CustomerName = $order_details->CustomerName;
        }
        else{
            $CustomerName = ' ';
        }
        $CustomerPContactName =  $order_details->CustomerPContactName;
        if($CustomerPContactName)
        {
            $CustomerPContactName =  $order_details->CustomerPContactName;
        }
        else{
            $CustomerPContactName = ' ';
        }
        $CustomerStateCode =  $order_details->CustomerStateCode;
        if($CustomerStateCode)
        {
           $CustomerStateCode =  $order_details->CustomerStateCode.'-'; 
            $Customer_State_Code =  $order_details->CustomerStateCode; 
        }
        else
        {
            $CustomerStateCode =  ' '; 
        }
        $MiscellaneousNotes = $order_details->MiscellaneousNotes;
        if($MiscellaneousNotes)
        {
            $Miscellaneous = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Miscellaneous Information</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;'.$MiscellaneousNotes.' </td></tr></table>';
        }else{
            $Miscellaneous = '<div style="padding-top: 10pt;"> <table style="width: 100%;" cellspacing="0"> <tr> <td class="blur text-center"> <p style="font-size: 10pt;width:100%; font-weight: bold;text-align: center;">Miscellaneous Information</p></td></tr><tr style="border: 0.01em solid grey;"> <td class="td-bd text-left" style="height: 25pt;"> &nbsp;&nbsp;&nbsp;None </td></tr></table>';
        }
        $CustomerCountyName =  $order_details->CustomerCountyName;
        if($CustomerCountyName)
        {
            $CustomerCountyName =  $order_details->CustomerCountyName.',';
            $Customer_County_Name =  $order_details->CustomerCountyName;
        }
        else{
            $CustomerCountyName = '-';
        }
        $CustomerZipCode = $order_details->CustomerZipCode;
        if($CustomerZipCode)
        {
            $CustomerZipCode = $order_details->CustomerZipCode;
        }
        else{
            $CustomerZipCode = ' ';
        }
        $CustomerCityName = $order_details->CustomerCityName;
        if($CustomerCityName)
        {
            $CustomerCityName = $order_details->CustomerCityName.',';
        }
        else{
             $CustomerCityName = ' ';
        }
        $Mortgagee = $order_details->Mortgagee;
        $OrderNumber = $order_details->OrderNumber;
        $LoanNumber = $order_details->LoanNumber;
        if($LoanNumber)
        {
            $LoanNumber = $order_details->LoanNumber;
        }
        else{
            $LoanNumber = '-';
        }
        $OwnerName = $order_details->OwnerName;
        $CountyName =  $order_details->CountyName;
        $PropertyAddress1 = $order_details->PropertyAddress1;
        $PropertyAddress2 = $order_details->PropertyAddress2.',';
        $CityName = $order_details->CityName.',';
        $StateName = $order_details->StateName;
        $StateCode = $order_details->StateCode;
        if($StateCode)
        {
            $StateCode =  $order_details->StateCode;
        }
        else{
            $StateCode = '-';
        }
        $DisclaimerResult = $this->Order_reports_model->get_DisclaimerNote($order_details->StateCode);
        if($DisclaimerResult->DisclaimerNote){
            $DisclaimerNote = $DisclaimerResult->DisclaimerNote;
        }
        else{
             $DisclaimerNote = '-';
        }
        if($DisclaimerResult->StateEmail){
            $DisclaimerStateEmail = $DisclaimerResult->StateEmail;
        }
        else{
            $DisclaimerStateEmail = '-';
        }
        if($DisclaimerResult->StateWebsite){
            $DisclaimerStateWebsite = $DisclaimerResult->StateWebsite;
        }
        else{
            $DisclaimerStateWebsite = '-';
        }
        if($DisclaimerResult->StatePhoneNumber){
            $DisclaimerStatePhoneNumber = $DisclaimerResult->StatePhoneNumber;
        }
        else{
            $DisclaimerStatePhoneNumber = '-';
        }
        if($DisclaimerResult->LegalDisclaimerNote == 1 && $DisclaimerResult->TaxDisclaimerNote == 1)
        {
            $taxpagebreak = '<div style="page-break-after: always;"></div>';
            $pagebreak =   '<div style="page-break-after: auto;"></div>';
            $LegalDisclaimerNote = 'displayfooter';
            $TaxDisclaimerNote = 'displayfooter';
        }
        elseif($DisclaimerResult->LegalDisclaimerNote == 0 && $DisclaimerResult->TaxDisclaimerNote == 1){

 
                $LegalDisclaimerNote = 'noheaderfooter';
                $TaxDisclaimerNote = 'displayfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = '<div style="page-break-after: auto;"></div>';
            
                             
        }
        elseif($DisclaimerResult->LegalDisclaimerNote == 1 && $DisclaimerResult->TaxDisclaimerNote == 0)
        {
                $LegalDisclaimerNote = 'displayfooter';
                $TaxDisclaimerNote = 'noheaderfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = '<div style="page-break-after: auto;"></div>';
        }
        else{
            $LegalDisclaimerNote = 'noheaderfooter';
                $TaxDisclaimerNote = 'noheaderfooter';
                $pagebreak =   '<div style="page-break-after: auto;"></div>';
                $taxpagebreak = '<div style="page-break-after: always;"></div>';
        }
        $SearchDate = $order_details->SearchThroDate;
        if($SearchDate == '0000-00-00')
        {
            $SearchThroDate =  '-';
        }
        else{
            $SearchThroDate =  date('m/d/Y', strtotime($SearchDate));
        }
        $SearchFrom = $order_details->SearchFromDate;
        if($SearchFrom == '0000-00-00 00:00:00' || $SearchFrom == '')
            {
                $SearchFromDate = '-';
            }
            else
            {
                $SearchFromDate = date('m/d/Y', strtotime($SearchFrom));
            }
            $SearchAsOf = $order_details->SearchAsOfDate;
            if($SearchAsOf == '0000-00-00 00:00:00' || $SearchAsOf == '')
                {
                    $SearchAsOfDate = '-';
                }
                else
                {
                    $SearchAsOfDate = date('m/d/Y', strtotime($SearchAsOf));
                }
                $ZipCode = $order_details->PropertyZipcode;
                $address = $this->Order_reports_model->get_Address($OrderUID);
                foreach ($address as $key => $data) {

                    $AssessedCountyName = $data->AssessedCountyName;
                    $USPSCountyName = $data->USPSCountyName;
                }
                $ImageUrl = base_url().'assets/img/sourcepoint.png';
                $GoogleMapAddress = $PropertyAddress1.' '.$PropertyAddress2.' '.$CityName.' '.$StateName.' '.$ZipCode;
                $Mort = array('CustomerName'=>$CustomerNumber.$CustomerName,'AttentionName'=>$AttentionName,'CustomerAddress1'=>$CustomerAddress1,'CustomerAddress2'=>$CustomerAddress2,'CustomerCityName'=>$CustomerCityName,'CustomerCountyName'=>$CustomerCountyName,'CustomerStateCode'=>$CustomerStateCode,'CustomerPContactName'=>$CustomerPContactName,'OrderMortgagee'=>$Mortgagee,'Ordernumber'=>$OrderNumber,'Loannumber'=>$LoanNumber,'Ownername'=>$OwnerName,'Countyname'=>$CountyName,'County_name'=>$CountyName.',','Propertyaddress1'=>$PropertyAddress1,'Propertyaddress2'=>$PropertyAddress2,'Cityname'=>$CityName,'Statecode'=>$StateCode.'-','Zip'=>$ZipCode,'SearchThroDate'=>$SearchThroDate,'GoogleMapAddress'=>$GoogleMapAddress,'SearchFromDate'=>$SearchFromDate,'SearchAsOfDate'=>$SearchAsOfDate,'DisclaimerNote'=>$DisclaimerNote,'Url'=>$base_url,'CustomerZipCode'=>$CustomerZipCode,'OrderDate'=>$OrderDate,'DisclaimerStateEmail'=>$DisclaimerStateEmail,'DisclaimerStateWebsite'=>$DisclaimerStateWebsite,'DisclaimerStatePhoneNumber'=>$DisclaimerStatePhoneNumber,'LegalDisclaimerNote'=>$LegalDisclaimerNote,'TaxDisclaimerNote'=>$TaxDisclaimerNote,'taxpagebreak'=>$taxpagebreak,'pagebreak'=>$pagebreak,'Customer_State_Code'=>$Customer_State_Code,'Customer_County_Name'=>$Customer_County_Name,'State_code'=>$StateCode,'ImageUrl'=>$ImageUrl,'Miscellaneous'=>$Miscellaneous, 'taxcert_html'=>$taxcert_html, 'sep_taxcert_html'=>$sep_taxcert_html,'NumTotalWOExempt'=>$NumTotalWOExempt,'CurrentDateTime'=>date('m/d/Y',strtotime("now")),'CurrentMonth'=>date('m/Y', strtotime('now')),'NextMonth'=>date('m/Y', strtotime('+1 month')));


                foreach ($Mort as $key => $value) 
                {

                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
        //Orders

        //Heading
        //Chain of Title
                if($this->Order_reports_model->get_torderdeeds($OrderUID))
                {
                    $DeedHeading = '<p class="blur text-center" style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;">Chain of Title</p>';
                    array_push($keys, '%%DeedHeading%%');
                    array_push($values, $DeedHeading);
                }
                else
                {

                    array_push($keys, '%%DeedHeading%%');
                    array_push($values, ' ');
                }
        //Chain of Title
        //Tax Heading
                if($this->Order_reports_model->get_tordertaxcerts($OrderUID))
                {
                    $TaxHeading = '<tr><td class="blur text-center" colspan="6"><p style="font-size: 10pt;width:100%;font-weight: bold;margin-top: 5pt;text-align: center;">Tax Information</p></td></tr>';
                    array_push($keys, '%%TaxHeading%%');
                    array_push($values, $TaxHeading);
                }
                else
                {
                    array_push($keys, '%%TaxHeading%%');
                    array_push($values, ' ');
                }
        //Tax Heading

        //Heading
        //Address
         include APPPATH .'modules/Order_reports/views/addressvariance.php';
        //Address
        //Legal Description
                $legal_information = $this->Order_reports_model->get_LegalDescription($OrderUID);
                if($legal_information)
                {

                    foreach ($legal_information as $dm) 
                    {

                        $LegalDescr = str_replace('  ', ' &nbsp;', nl2br(strtoupper($dm->LegalDescription)));
                        if($LegalDescr)
                        {
                            array_push($keys, '%%LegalDescr%%');
                            array_push($values, $LegalDescr);
                        }
                        else{
                            array_push($keys, '%%LegalDescr%%');
                            array_push($values, ' ');
                        }

                        foreach($dm as $cm => $vm)
                        {
                            array_push($keys, '%%'.$cm.'%%');
                            array_push($values, $vm);
                        }

                    } 
                }
                else{

                    array_push($keys, '%%LegalDescr%%');
                    array_push($values, ' ');
                }

        //Legal Description
        //Order Assessment
                $order_assessment = $this->Order_reports_model->get_OrderAssessment($OrderUID);
                if($order_assessment)
                {

                    foreach ($order_assessment as $data_orderass_info) 
                    {
                        $AssessedYear = $data_orderass_info->AssessedYear;

                        if($AssessedYear)
                        {
                            array_push($keys, '%%AssessedYear%%');
                            array_push($values, $AssessedYear);
                        }
                        else{
                            array_push($keys, '%%AssessedYear%%');
                            array_push($values, '-');

                        }
                        $Agricultural = $data_orderass_info->Agriculture;

                        if($Agricultural)
                            
                        {   
                        $Agricultural = '$'.$Agricultural;
                        array_push($keys, '%%Agricultural%%');
                        array_push($values, $Agricultural);
                        }
                        else
                        {
                        array_push($keys, '%%Agricultural%%');
                        array_push($values, '-');

                    }
                    $TotalValue = $data_orderass_info->TotalValue;

                    if($TotalValue)
                    {
                        $TotalVal = '$'.$TotalValue;
                        array_push($keys, '%%TotalValue%%');
                        array_push($values, $TotalVal);
                    }
                    else{
                        array_push($keys, '%%TotalValue%%');
                        array_push($values, '-');

                    }
                    $Landstr = $data_orderass_info->Land;
                    if($Landstr)
                    {
                        $Landltrim = ltrim($Landstr, '$');
                        $LandRepl = str_replace(",","",$Landltrim);
                        $Lan = substr($LandRepl, 0,-3);
                        $Land = '$'.number_format($Lan,2);
                    }
                    else{
                        $Land = '-';
                    }
                    $Buildingsstr = $data_orderass_info->Buildings;
                    if($Buildingsstr)
                    {

                        $Buildingsltrim = ltrim($Buildingsstr, '$');
                        $BuildingsRepl = str_replace(",","",$Buildingsltrim);
                        $Build = substr($BuildingsRepl, 0,-3);
                        $Buildings = '$'.number_format($Build,2);
                    }
                    else{
                        $Buildings = '-';
                    }
                    $AssessmentValue = $data_orderass_info->AssessmentValue;
                    if($AssessmentValue)
                    {
                        $AssessmentValue = $AssessmentValue;
                        array_push($keys, '%%AssessmentValue%%');
                        array_push($values, $AssessmentValue);
                        array_push($keys, '%%alignment%%');
                        array_push($values, 'text-left');
                    }
                    else{
                        array_push($keys, '%%AssessmentValue%%');
                        array_push($values, '-');
                        array_push($keys, '%%alignment%%');
                        array_push($values, 'text-center');

                    }
                    $Value = array('TotalValue'=>$TotalValue,'Buildings'=>$Buildings,'Land'=>$Land,'Agricultural'=>$Agricultural,'AssessmentValue'=>$AssessmentValue);
                    foreach ($Value as $key => $value) 
                    {
                        array_push($keys, '%%'.$key.'%%');
                        array_push($values, $value);
                    }
                } 
            }
            else{


                $TaxArray = array('AssessedYear','Land','Buildings','Agricultural','TotalValue');
                foreach($TaxArray as $col)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, '-');
                }
                 array_push($keys, '%%AssessmentValue%%');
                 array_push($values, '-');
                 array_push($keys, '%%alignment%%');
                 array_push($values, 'text-center');

            }

            /* Latest Tax Starts */

            $tax_information = $this->Order_reports_model->get_tax_latest($OrderUID);
            if($tax_information)
            {
                        //Property tax in ISGN Report
                foreach ($tax_information as $data) 
                {
                            //DeliquentTax
                    if($data->TaxStatusName)
                    {
                        if($data->TaxStatusName == 'Delinquent')
                        {
                            $DeliquentTax = array('DeliquentTax'=>'Yes');
                            foreach($DeliquentTax as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                        else
                        {
                            $DeliquentTax = array('DeliquentTax'=>'No');
                            foreach($DeliquentTax as $col => $val)
                            {
                                array_push($keys, '%%'.$col.'%%');
                                array_push($values, $val);
                            }
                        }
                    }
                    else{
                        array_push($keys, '%%DeliquentTax%%');
                        array_push($values, '-');
                    }

                    $LatestTaxYear = $data->LatestTaxYear;
                    if($LatestTaxYear)
                    {

                        array_push($keys, '%%LatestTaxYear%%');
                        array_push($values, $LatestTaxYear);
                        array_push($keys, '%%ReferTaxSection%%');
                        array_push($values, 'REFER TAX SECTION');
                    }
                    else
                    {
                        array_push($keys, '%%LatestTaxYear%%');
                        array_push($values, '-');
                        array_push($keys, '%%ReferTaxSection%%');
                        array_push($values, '-');
                    }
                }
            }
            else
            {
                $TaxArray = array('LatestTaxYear','DeliquentTax','ReferTaxSection');
                foreach($TaxArray as $col)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, '-');
                }
            }

            /* Latest Tax Ends */

            /* Property Information Starts */

            $property_information = $this->Order_reports_model->getPropertyInformation($OrderUID);
            if($property_information)
            {
                foreach ($property_information as $key => $data) 
                {
                    $MaritalStatusName = $data->MaritalStatusName;
                    if($MaritalStatusName)
                    {
                        array_push($keys, '%%MaritalStatusName%%');
                        array_push($values, $MaritalStatusName);
                    }
                    else{
                        array_push($keys, '%%MaritalStatusName%%');
                        array_push($values, ' ');
                    }
                    $OwnerName = $data->OwnerName;
                    if($OwnerName)
                    {
                        array_push($keys, '%%OwnerName%%');
                        array_push($values, $OwnerName);
                    }
                    else{
                        array_push($keys, '%%OwnerName%%');
                        array_push($values, '-');
                    }
                    $SubDivisionName = $data->SubDivisionName;
                    if($SubDivisionName)
                    {
                        array_push($keys, '%%SubDivisionName%%');
                        array_push($values, $SubDivisionName);
                    }
                    else{
                        array_push($keys, '%%SubDivisionName%%');
                        array_push($values, '-');
                    }

                    $sdMapNo = $data->sdMapNo;
                    if($sdMapNo)
                    {
                        array_push($keys, '%%sdMapNo%%');
                        array_push($values, $sdMapNo);
                    }
                    else{
                        array_push($keys, '%%sdMapNo%%');
                        array_push($values, '-');
                    }
                    $dSection = $data->dSection;
                    if($dSection)
                    {
                        array_push($keys, '%%dSection%%');
                        array_push($values, $dSection);
                    }
                    else{
                        array_push($keys, '%%dSection%%');
                        array_push($values, '-');
                    }
                    $Township = $data->Township;
                    if($Township)
                    {
                        array_push($keys, '%%Township%%');
                        array_push($values, $Township);
                    }
                    else{
                        array_push($keys, '%%Township%%');
                        array_push($values, '-');
                    }
                    $APN = $data->APN;
                    if($APN)
                    {
                        array_push($keys, '%%APN%%');
                        array_push($values, $APN);
                    }
                    else{
                        array_push($keys, '%%APN%%');
                        array_push($values, '-');
                    }
                    $Lot = $data->Lot;
                    $Block = $data->Block;
                    if($Lot && $Block)
                    {
                      array_push($keys, '%%Lot%%');
                      array_push($values, $Block.'/'.$Lot);
                  }
                  
                  else if($Lot)
                  {
                    array_push($keys, '%%Lot%%');
                    array_push($values, $Lot);
                }
                
                else if($Block)
                {
                    array_push($keys, '%%Lot%%');
                    array_push($values, $Block);
                }
                else{
                    array_push($keys, '%%Lot%%');
                    array_push($values, '-');
                }
            }
            }
            else
            {
                $TaxArray = array('Exempt','APN','Lot','Block','SubDivisionName','sdMapNo','dSection','Township','OwnerName');
                foreach($TaxArray as $col)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, '-');
                }
                array_push($keys, '%%MaritalStatusName%%');
                array_push($values, ' ');
            }

            /* Property Information Ends */


        //Get Exemption
            $exemptions = $this->Order_reports_model->get_taxExemption($OrderUID);
            if($exemptions)
            {
                $Exempt = array('Exempt'=>'Yes');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            else
            {
                $Exempt = array('Exempt'=>'No');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
        //Get Exemption

        //Get Borrowers
            $borrowers = $this->Order_reports_model->getBorrowers($OrderUID);
            $Count =  count($borrowers);
            if($Count > 1){

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    if($value->PRName !=''){

                    $borrower .=  trim($value->PRName.',');
                    }
                }
                $borrowers =  rtrim($borrower,",");
                $Array = explode(",", $borrowers);
                $LastArrayValue =  end($Array);
                
                if(count($Array) > 1 ){
                    array_pop($Array);
                    $BorrowerName = implode(", &nbsp;", $Array).' and '.$LastArrayValue;

                }else{
                    
                    $BorrowerName = implode(", &nbsp;", $Array);
                }
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
            else{

                $borrower ='';
                foreach($borrowers as $value) 
                {
                    $borrower .=  $value->PRName.',';

                }
                $BorrowerName =  rtrim($borrower,",");
                array_push($keys, '%%Borrowers%%');
                array_push($values, $BorrowerName ); 
            }
        //Get Borrowers

            $output = str_replace($keys,$values, $output);

            $doc->load($output);
        //Property Starts
            preg_match_all('/<div class=\"torderproperty\">(.*?)<\/div>/s',$output,$torderproperty);
        // $getCounts = $this->Order_reports_model->getCounts($OrderUID);
        // $MortgageCount= $this->Order_reports_model->getMortgageCount($OrderUID);
            $MortgageCount =  $this->Order_reports_model->getMortgageCount($OrderUID);
if($MortgageCount == 0){ $MortgageCount = "NA"; }
            array_push($keys, '%%MortgageCount%%');
            array_push($values, $MortgageCount);
            $LienCount =  $this->Order_reports_model->getLienCount($OrderUID);
if($LienCount == 0){ $LienCount = "NA"; }
            array_push($keys, '%%LienCount%%');
            array_push($values, $LienCount);
            $JudgementCount =  $this->Order_reports_model->getJudgementCount($OrderUID);
if($JudgementCount == 0){ $JudgementCount = "NA"; }
            array_push($keys, '%%JudgementCount%%');
            array_push($values, $JudgementCount);
            $GranteeGrantor =  $this->Order_reports_model->getGranteeGrantor($OrderUID);
            if($GranteeGrantor)
            {
                foreach ($GranteeGrantor as $col => $value) 
                {
                    $Grantee = $value->Grantee;
                    if($Grantee)
                    {
                        array_push($keys, '%%Grantee%%');
                        array_push($values, $Grantee);
                    }
                    else{
                        array_push($keys, '%%Grantee%%');
                        array_push($values, '-');
                    }
                    $Grantor = $value->Grantor;
                    if($Grantee){
                        array_push($keys, '%%Grantor%%');
                        array_push($values, $Grantor);
                    }
                    else{
                        array_push($keys, '%%Grantor%%');
                        array_push($values, '-');
                    }
                    $EstateInterestName = $value->EstateInterestName;
                    if($EstateInterestName)
                    {
                        array_push($keys, '%%EstateInterestName%%');
                        array_push($values, $EstateInterestName);
                    }
                    else{
                        array_push($keys, '%%EstateInterestName%%');
                        array_push($values, '-');
                    }
                    $MannerofTitle = $value->MannerofTitle;
                    if($MannerofTitle)
                    {
                        array_push($keys, '%%MannerofTitle%%');
                        array_push($values, $MannerofTitle);
                    }
                    else{
                        array_push($keys, '%%MannerofTitle%%');
                        array_push($values, '-');
                    }
                    $TenancyName = $value->TenancyName;
                    if($TenancyName)
                    {
                        array_push($keys, '%%TenancyTypeName%%');
                        array_push($values, $TenancyName);
                    }
                    else{
                        array_push($keys, '%%TenancyTypeName%%');
                        array_push($values, '-');
                    }


                } 
            }
            else{
                array_push($keys, '%%Grantee%%');
                array_push($values, '-');
                array_push($keys, '%%Grantor%%');
                array_push($values, '-');
                array_push($keys, '%%EstateInterestName%%');
                array_push($values, '-');
                array_push($keys, '%%TenancyTypeName%%');
                array_push($values, '-');
                array_push($keys, '%%MannerofTitle%%');
                array_push($values, '-');
            }

            $exemptions = $this->Order_reports_model->get_taxExemption($OrderUID);
            if($exemptions)
            {
                $Exempt = array('Exempt'=>'Yes');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }
            else
            {
                $Exempt = array('Exempt'=>'No');
                foreach($Exempt as $col => $val)
                {
                    array_push($keys, '%%'.$col.'%%');
                    array_push($values, $val);
                }
            }








            $torderproperty_table .= str_replace($keys, $values, $torderproperty[0][0]);
            foreach ($doc->find(".torderproperty") as $node ) 
            {
                $node->innertext = $torderproperty_table;
            }
        //Property Ends
        //Deed Starts
            preg_match_all('/<div class=\"torderdeeds\">(.*?)<\/div>/s',$output,$torderdeeds);
            $torderdeeds_table = '';
            $torderdeeds_array = array();
            $torderdeedsparties_array = array();
            $torder_deeds = $this->Order_reports_model->get_torderdeeds($OrderUID);
            // echo'<pre>';print_r($torder_deeds);exit;
            $torderdeeds_array = $torder_deeds;
            $torderdeeds_array_count = count($torderdeeds_array);
            for ($i=0; $i < $torderdeeds_array_count; $i++) 
            { 
                $torderdeeds_array[$i]->deed_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($torderdeeds_array[$i] as $key => $value)
                {
                    $DocumentTypeName = $torderdeeds_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $DeedType = $torderdeeds_array[$i]->DeedType;
                            if($DeedType)
                            {
                                array_push($keys, '%%DeedDocumentTypeName%%');
                                array_push($values, $DeedType);
                            }
                            else{
                                array_push($keys, '%%DeedDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%DeedDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%DeedDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%DeedDocumentTypeName%%');
                        array_push($values, '-');
                    }
                    //Deed Date Format Change
                    $DDated = $torderdeeds_array[$i]->DeedDated;
                    if($DDated == '0000-00-00')
                    {
                        array_push($keys, '%%DeedDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $DeedDated =  date('m/d/Y', strtotime($DDated));
                        array_push($keys, '%%DeedDate%%');
                        array_push($values, $DeedDated);
                    }
                    //Deed Date Format Change
                    //Recorded date Format Change
                    $RDated = $torderdeeds_array[$i]->DeedRecorded;
                    if($RDated == '0000-00-00')
                    {
                        array_push($keys, '%%RecordedDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $RecordedDated =  date('m/d/Y', strtotime($RDated));
                        array_push($keys, '%%RecordedDate%%');
                        array_push($values, $RecordedDated);
                    }

                //Recorded date Format Change
                //ConsiderationAmount
                    $ConsiderationAmount = $torderdeeds_array[$i]->ConsiderationAmount;
                    if($ConsiderationAmount)
                    {
                        $ConsiderAmount = '$'.number_format($ConsiderationAmount,2);
                        array_push($keys, '%%ConsiderAmount%%');
                        array_push($values, $ConsiderAmount);
                    }
                    else{
                        array_push($keys, '%%ConsiderAmount%%');
                        array_push($values, '-');
                    }
                //ConsiderationAmount
                //Book/Page
                    $Deed_DBVTypeUID_1 = $torderdeeds_array[$i]->Deed_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_1);
                    $Deed_DBVTypeValue_1 = $torderdeeds_array[$i]->Deed_DBVTypeValue_1;
                    $Deed_DBVTypeUID_2 = $torderdeeds_array[$i]->Deed_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Deed_DBVTypeUID_2);
                    $Deed_DBVTypeValue_2 = $torderdeeds_array[$i]->Deed_DBVTypeValue_2;
                    if($DBVTypeName_1)
                    {
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, $DBVTypeName_1);
                        if($Deed_DBVTypeValue_1)
                        {
                            array_push($keys, '%%Deed_DBVTypeValue_1%%');
                            array_push($values, $Deed_DBVTypeValue_1);
                        }
                        else{
                            array_push($keys, '%%Deed_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }
                    }
                    else{
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Deed_DBVTypeValue_1%%');
                        array_push($values, '-');
                    }
                    if($DBVTypeName_2)
                    {
                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, $DBVTypeName_2);
                        if($Deed_DBVTypeValue_2)
                        {
                            array_push($keys, '%%Deed_DBVTypeValue_2%%');
                            array_push($values, $Deed_DBVTypeValue_2);
                        }
                        else{
                            array_push($keys, '%%Deed_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }
                    }
                    else{

                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Deed_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                //Deed Document
                    $DocumentNo = $torderdeeds_array[$i]->DocumentNo;
                    if($DocumentNo)
                    {
                        array_push($keys, '%%DocumentNo%%');
                        array_push($values, $DocumentNo);
                    }
                    else{
                        array_push($keys, '%%DocumentNo%%');
                        array_push($values, '-');
                    }
                //Deed Document
                //Certificate Number
                    $CertificateNo = $torderdeeds_array[$i]->CertificateNo;
                    if($CertificateNo)
                    {
                        array_push($keys, '%%CertificateNo%%');
                        array_push($values, $CertificateNo);
                    }
                    else{
                        array_push($keys, '%%CertificateNo%%');
                        array_push($values, '-');
                    }
                //Certificate Number
                //Instrument Number
                    $InstrumentNo = $torderdeeds_array[$i]->InstrumentNo;
                    if($InstrumentNo)
                    {
                        array_push($keys, '%%InstrumentNo%%');
                        array_push($values, $InstrumentNo);
                    }
                    else{
                        array_push($keys, '%%InstrumentNo%%');
                        array_push($values, '-');
                    }
                //Instrument Number
                //Township
                    $DeedTownship = $torderdeeds_array[$i]->NewTownShip;
                    if($DeedTownship)
                    {
                        array_push($keys, '%%DeedTownship%%');
                        array_push($values, $DeedTownship);
                    }
                    else{
                        array_push($keys, '%%DeedTownship%%');
                        array_push($values, '-');
                    }
                //Deed Comments
                    $DeedComments = $torderdeeds_array[$i]->DeedComments;
                    if($DeedComments)
                    {
                        array_push($keys, '%%DeedComments%%');
                        array_push($values, $DeedComments);
                        array_push($keys, '%%deedalignment%%');
                        array_push($values, 'text-left');

                    }
                    else{
                        array_push($keys, '%%DeedComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%deedalignment%%');
                        array_push($values, 'text-center');
                    }
                //Deed Comments
                //Main Loop
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                //Main Loop
                }
                $torderdeeds_table .= str_replace($keys, $values, $torderdeeds[0][0]);
            }
            foreach ( $doc->find(".torderdeeds") as $node ) 
            {
                $node->innertext = $torderdeeds_table;
            }
        //Deed Ends
        //Mortgages Starts
            preg_match_all('/<div class=\"tordermortgages\">(.*?)<\/div>/s',$output,$tordermortgages);
            $tordermortgages_array = array();
            $tordermortgagesparties_array = array();
            $tordermortgages_array = $this->Order_reports_model->get_tordermortgageparties($OrderUID);
            $tordermortgages_array_count = count($tordermortgages_array);
            $tordermortgages_table2="";
            for ($i=0; $i < $tordermortgages_array_count; $i++) 
            { 

                $tordermortgages_table = '';
                $tordermortgages_array[$i]->mortgage_increment = $i+1;
                $keys = array();
                $values = array();
                        //Leins & Encumbrances
                if($i==0)
                {
                    $MortgageHeading = '<tr><th class="blur text-center" colspan="4"><p style="font-size: 10pt;width:100%; font-weight: bold;margin-top: 5pt;text-align: center;">Liens & Encumbrances</p></th></tr>';
                    array_push($keys, '%%MortgageHeading%%');
                    array_push($values, $MortgageHeading);

                }
                else
                {
                    array_push($keys, '%%MortgageHeading%%');
                    array_push($values, ' ');
                }
                foreach ($tordermortgages_array[$i] as $key => $value) 
                {

                    $DocumentTypeName = $tordermortgages_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $MortgageType = $tordermortgages_array[$i]->MortgageType;
                            if($MortgageType)
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, $MortgageType);
                            }
                            else
                            {
                                array_push($keys, '%%MortgageDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%MortgageDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%MortgageDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //Mortgage Date Format Change
                    $MortgageDated = $tordermortgages_array[$i]->MortgageDated;
                    if( $MortgageDated == '0000-00-00')
                    {
                        array_push($keys, '%%MortgageDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $MortgageDate =  date('m/d/Y', strtotime($MortgageDated));
                        array_push($keys, '%%MortgageDate%%');
                        array_push($values, $MortgageDate);
                    }
                //Mortgage Date Format Change
                //Mortgage Date Format Change
                    $MortgageRecorded = $tordermortgages_array[$i]->MortgageRecorded;
                    if($MortgageRecorded == '0000-00-00')
                    {
                        array_push($keys, '%%MortgageRecordedDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $MortgageRecordedDate =  date('m/d/Y', strtotime($MortgageRecorded));
                        array_push($keys, '%%MortgageRecordedDate%%');
                        array_push($values, $MortgageRecordedDate);
                    }
                //Mortgage Date Format Change
                //Mortgage Maturity Date Format
                    $MortgageMaturityDate = $tordermortgages_array[$i]->MortgageMaturityDate;
                    if( $MortgageMaturityDate == '0000-00-00')
                    {
                        array_push($keys, '%%MaturityDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $MaturityDate =  date('m/d/Y', strtotime($MortgageMaturityDate));
                        array_push($keys, '%%MaturityDate%%');
                        array_push($values, $MaturityDate);
                    }
                //Mortgage Maturity Date Format
                //LoanAmount
                    $MortgageAmount = $tordermortgages_array[$i]->MortgageAmount;
                    if($MortgageAmount)
                    {
                        $LoanAmt = '$'.number_format($MortgageAmount,2);
                        array_push($keys, '%%LoanAmt%%');
                        array_push($values, $LoanAmt);
                    }
                    else{
                        array_push($keys, '%%LoanAmt%%');
                        array_push($values, '-');
                    }
                //LoanAmount
                //Mortgagee
                    $Mortgagee = $tordermortgages_array[$i]->Mortgagee;
                    if($Mortgagee)
                    {
                        array_push($keys, '%%Mortgagee%%');
                        array_push($values, $Mortgagee);
                    }
                    else{
                        array_push($keys, '%%Mortgagee%%');
                        array_push($values, '-');
                    }
                //Mortgagee
                //Trustee
                    $Trustee1 = $tordermortgages_array[$i]->Trustee1;
                    $Trustee2 = $tordermortgages_array[$i]->Trustee2;
                    if($Trustee1 != '' && $Trustee2 != '')
                    {
                        $Trustee = $Trustee1.','.$Trustee2;
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);
                    }
                    elseif($Trustee1 != '')
                    {
                        $Trustee = $Trustee1;
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);
                    }
                    elseif($Trustee2 != ''){

                        $Trustee = $Trustee2;
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);

                    }
                    else{

                        $Trustee = '-';
                        array_push($keys, '%%Trustee%%');
                        array_push($values, $Trustee);
                    }
                //Trustee
                //Closed/Open Ended
                    if($tordermortgages_array[$i]->LienTypeName =='Closed Ended')
                    {
                        $MTG = array('MTG'=>'Closed Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    if($tordermortgages_array[$i]->LienTypeName =='Open Ended')
                    {
                        $MTG = array('MTG'=>'Open Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    else
                    {

                            array_push($keys, '%%MTG%%');
                            array_push($values,'-');
                        
                    }
                //Closed/Open Ended
                //Book/Page
                    $Mortgage_DBVTypeUID_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_1);
                    $Mortgage_DBVTypeValue_1 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_1;
                    $Mortgage_DBVTypeUID_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Mortgage_DBVTypeUID_2);
                    $Mortgage_DBVTypeValue_2 = $tordermortgages_array[$i]->Mortgage_DBVTypeValue_2;

                    if($DBVTypeName_1)
                    {
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, $DBVTypeName_1);
                        if($Mortgage_DBVTypeValue_1)
                        {
                            array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                            array_push($values, $Mortgage_DBVTypeValue_1);
                        }
                        else
                        {
                            array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }
                    }
                    else{
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Mortgage_DBVTypeValue_1%%');
                        array_push($values, '-');
                    }

                    if($DBVTypeName_2)
                    {
                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, $DBVTypeName_2);
                        if($Mortgage_DBVTypeValue_2)
                        {
                            array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                            array_push($values, $Mortgage_DBVTypeValue_2);
                        }
                        else{
                            array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }
                    }
                    else{

                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Mortgage_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                    if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                    {
                        $AppendInstrument = $Mortgage_DBVTypeValue_1.','.$Mortgage_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_1 == 'Instrument'){
                        $AppendInstrument = $Mortgage_DBVTypeValue_1;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_2 == 'Instrument'){
                        $AppendInstrument = $Mortgage_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                    else{
                        $AppendInstrument = '-';
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                //Book/Page
                //Additional Info
                $AdditionalInfo = $tordermortgages_array[$i]->AdditionalInfo;
                if($AdditionalInfo)
                {
                    array_push($keys, '%%AdditionalInfo%%');
                    array_push($values, $AdditionalInfo);
                }
                else{
                    array_push($keys, '%%AdditionalInfo%%');
                    array_push($values, '-');
                }
                //Additional Info
                //Closed/Open Ended
                $OpenEnded = $tordermortgages_array[$i]->IsOpenEnded;
                if($OpenEnded == '1')
                {

                    array_push($keys, '%%OpenEnded%%');
                    array_push($values, 'Yes');
                    
                }
                else
                {

                    array_push($keys, '%%OpenEnded%%');
                    array_push($values,'-');
                    
                }
                //Closed/Open Ended
                //Mortgage Comments
                    $MortgageComments = $tordermortgages_array[$i]->MortgageComments;
                    if($MortgageComments)
                    {
                        array_push($keys, '%%MortgageComments%%');
                        array_push($values, $MortgageComments);
                        array_push($keys, '%%mortgagealignment%%');
                        array_push($values, 'text-left');
                    }
                    else{
                        array_push($keys, '%%MortgageComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%mortgagealignment%%');
                        array_push($values, 'text-center');
                    }
                //Mortgage Comments
                //Main Loop
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                //Main Loop
                }
                $tordermortgages_table .= str_replace($keys, $values, $tordermortgages[0][0]);

                $submortgage_html='<table style="width: 100%;margin-top: 5pt;page-break-inside: avoid;table-layout: fixed;" cellspacing="0"><tr>
                <td class="td-bd text-center" colspan="3">
                <p style="font-size: 7pt;text-transform: uppercase;" class="bold text-center">%%submortgageDocumentTypeName%%</p>
                </td>
                <td class="td-bd text-center" colspan="1">
                <p style="font-size: 7pt;" class="bold text-center">Document Dated</p>
                </td>
                <td class="td-bd text-center" colspan="2">
                <p class="text-center" style="padding:0pt 20pt;font-size: 7pt;">%%Dated%%</p>
                </td>
                </tr>
                <tr>
                <td width="15.30%" class="td-bd text-center">
                <p style="font-size: 7pt;" class="bold">Recorded Date</p>
                </td>
                <td width="18.30%" class="td-bd text-center">
                <p style="font-size: 7pt;" class="text-center">%%Recorded%%</p>
                </td>
                <td width="15.30%" class="td-bd text-center">
                <p class="bold text-center" style="font-size: 7pt;">%%SubdocumentDBVTypeName_1%%</p>
                </td>
                <td width="18.30%" class="td-bd text-center">
                <p style="font-size: 7pt;" class="text-center">%%subdocument_DBVTypeValue_1%%</p>
                </td>
                <td width="15.30%" class="td-bd text-center">
                <p style="font-size: 7pt;" class="bold">%%SubdocumentDBVTypeName_2%%</p>
                </td>
                <td width="18.30%" class="td-bd text-center">
                <p style="font-size: 7pt;" class="text-center">%%subdocument_DBVTypeValue_2%%</p>
                </td>
                </tr>
                <tr>
                <td class="td-bd text-center" colspan="1">
                <p style="font-size: 7pt;" class="bold">Comments</p>
                </td>
                <td class="td-bd %%submortgagealignment%%" colspan="5">
                <p class="" style="text-align: justify;font-size: 7pt;">%%SubMortgageComments%%</p>
                </td>
                </tr></table>';

                $submortgage="";
                //Sub Mortgage
                $submortgage_table = '';
                $submortgage_array = array();
                $Mortgage = $tordermortgages_array[$i]->MortgageSNo;
                $submortgage_array = $this->Order_reports_model->getsubmortgage($OrderUID, $Mortgage);
                $submortgage_array_count = count($submortgage_array);
                for ($j=0; $j < $submortgage_array_count; $j++) 
                { 
                    $keys = array();
                    $values = array();
                    foreach ($submortgage_array[$j] as $key => $value) 
                    {

                    $DocumentTypeName = $submortgage_array[$j]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $MortgageAssignmentType = $submortgage_array[$j]->MortgageAssignmentType;
                            if($MortgageAssignmentType)
                            {
                                array_push($keys, '%%submortgageDocumentTypeName%%');
                                array_push($values, $MortgageAssignmentType);
                            }
                            else
                            {
                                array_push($keys, '%%submortgageDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%submortgageDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%submortgageDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%submortgageDocumentTypeName%%');
                        array_push($values, '-');
                    }

                        $Dated = $submortgage_array[$j]->Dated;
                        if($Dated == '0000-00-00')
                        {
                            array_push($keys, '%%Dated%%');
                            array_push($values, '-');

                        }
                        else{

                            $submortgagedated =  date('m/d/Y', strtotime($Dated));
                            array_push($keys, '%%Dated%%');
                            array_push($values, $submortgagedated);

                        }
                        $Recorded = $submortgage_array[$j]->Recorded;
                        if($Recorded == '0000-00-00')
                        {
                            array_push($keys, '%%Recorded%%');
                            array_push($values, '-');

                        }
                        else{

                            $submortgagerecorded =  date('m/d/Y', strtotime($Recorded));
                            array_push($keys, '%%Recorded%%');
                            array_push($values, $submortgagerecorded);

                        }
                        $SubMortgageComments = $submortgage_array[$j]->Comments;
                        if($SubMortgageComments)
                        {
                            array_push($keys, '%%SubMortgageComments%%');
                            array_push($values, $SubMortgageComments);
                            array_push($keys, '%%submortgagealignment%%');
                            array_push($values, 'text-left');
                        }
                        else{
                            array_push($keys, '%%SubMortgageComments%%');
                            array_push($values, '-');
                            array_push($keys, '%%submortgagealignment%%');
                            array_push($values, 'text-center');
                        }

                        $Subdocument_DBVTypeUID_1 = $submortgage_array[$j]->Subdocument_DBVTypeUID_1;
                        $SubdocumentDBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Subdocument_DBVTypeUID_1);
                        $Subdocument_DBVTypeValue_1 = $submortgage_array[$j]->Subdocument_DBVTypeValue_1;
                        $Subdocument_DBVTypeUID_2 = $submortgage_array[$j]->Subdocument_DBVTypeUID_2;
                        $SubdocumentDBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Subdocument_DBVTypeUID_2);
                        $Subdocument_DBVTypeValue_2 = $submortgage_array[$j]->Subdocument_DBVTypeValue_2;

                        if($SubdocumentDBVTypeName_1)
                        {
                            array_push($keys, '%%SubdocumentDBVTypeName_1%%');
                            array_push($values, $SubdocumentDBVTypeName_1);
                            if($Subdocument_DBVTypeValue_1)
                            {
                                array_push($keys, '%%subdocument_DBVTypeValue_1%%');
                                array_push($values, $Subdocument_DBVTypeValue_1);
                            }
                            else{

                                array_push($keys, '%%subdocument_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else{
                            array_push($keys, '%%SubdocumentDBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%subdocument_DBVTypeValue_1%%');
                            array_push($values, '-');
                        }

                        if($SubdocumentDBVTypeName_2)
                        {
                            array_push($keys, '%%SubdocumentDBVTypeName_2%%');
                            array_push($values, $SubdocumentDBVTypeName_2);
                            if($Subdocument_DBVTypeValue_2)
                            {
                                array_push($keys, '%%subdocument_DBVTypeValue_2%%');
                                array_push($values, $Subdocument_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%subdocument_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else{

                            array_push($keys, '%%SubdocumentDBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%subdocument_DBVTypeValue_2%%');
                            array_push($values, '-');
                        }

                    }

                    $submortgage .= str_replace($keys, $values, $submortgage_html);

                }
                $tordermortgages_table = str_replace('%%submortgage%%', $submortgage, $tordermortgages_table);

                $tordermortgages_table2 .= $tordermortgages_table;

            }
            foreach ( $doc->find(".tordermortgages") as $node ) 
            {
                $node->innertext = $tordermortgages_table2;
            }
        //Mortgages Ends

        //Tax Starts
            preg_match_all('/<div class=\"tordertaxcerts\">(.*?)<\/div>/s',$output,$tordertaxcerts);

            $tordertaxcerts_array = array();
            $tordertax_array = array();
            $tordertaxcerts_array = $this->Order_reports_model->get_tordertaxcerts($OrderUID);
            $tordertaxcerts_array_count = count($tordertaxcerts_array);
            $tordertaxcerts_table2="";
            $tordertaxcerts_table3="";
            for ($i=0; $i < $tordertaxcerts_array_count; $i++) 
            { 
                $tordertaxcerts_table = '';
                $tordertaxcerts_array[$i]->tax_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($tordertaxcerts_array[$i] as $key => $value)
                {
                //subdocumenttype
                $DocumentTypeName = $tordertaxcerts_array[$i]->DocumentTypeName;
                if($DocumentTypeName)
                {
                    if($DocumentTypeName == 'Others')
                    {
                        $TaxType = $tordertaxcerts_array[$i]->TaxType;
                        if($TaxType)
                        {
                            array_push($keys, '%%TaxDocumentTypeName%%');
                            array_push($values, $TaxType);
                        }
                        else
                        {
                            array_push($keys, '%%TaxDocumentTypeName%%');
                            array_push($values, '-');
                        }
                    }
                    else if($DocumentTypeName !== 'Others')
                    {
                        array_push($keys, '%%TaxDocumentTypeName%%');
                        array_push($values, $DocumentTypeName);
                    }
                    else
                    {
                        array_push($keys, '%%TaxDocumentTypeName%%');
                        array_push($values, '-');
                    }

                }
                else{
                    array_push($keys, '%%TaxDocumentTypeName%%');
                    array_push($values, '-');
                }
                //subdocumenttype
                //Amount Paid
                    $AmountPaid = $tordertaxcerts_array[$i]->AmountPaid;
                    $AmtPaid = '$'.number_format($AmountPaid,2);
                    if($AmtPaid)
                    {
                        array_push($keys, '%%AmountPaid%%');
                        array_push($values, $AmtPaid);
                    }
                    else{
                        array_push($keys, '%%AmountPaid%%');
                        array_push($values, '-');
                    }
                //Amount Paid
                //Amount Due
                    $AmountDue = $tordertaxcerts_array[$i]->AmountDue;
                    $AmtDue = '$'.number_format($AmountDue,2);
                    if($AmtDue)
                    {
                        array_push($keys, '%%AmountDue%%');
                        array_push($values, $AmtDue);
                    }
                    else{
                        array_push($keys, '%%AmountDue%%');
                        array_push($values, '-');
                    }
                //Amount Due
                //Gross Amount
                    $GrossAmount = $tordertaxcerts_array[$i]->GrossAmount;
                    $GrossAmt = '$'.number_format($GrossAmount,2);
                    if($GrossAmt)
                    {
                        array_push($keys, '%%GrossAmount%%');
                        array_push($values, $GrossAmt);
                    }
                    else{
                        array_push($keys, '%%GrossAmount%%');
                        array_push($values, '-');
                    }
                //Gross Amount



                  //ApprovedUnapprovedTaxAuthorityDetails
                    $UnapprovedTaxAuthorityDetails = $this->Order_reports_model->GetUnapprovedTaxAuthorityDetails($OrderUID,$tordertaxcerts_array[$i]->TaxAuthorityUID);
                    if($UnapprovedTaxAuthorityDetails)
                    {
                        //PaymentAddrLine1
                            $PaymentAddrLine1 = $UnapprovedTaxAuthorityDetails->PaymentAddrLine1;
                            if($PaymentAddrLine1)
                            {
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, $PaymentAddrLine1.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, ' ');
                            }
                        //PaymentAddrLine1
                        //PaymentAddrLine2
                            $PaymentAddrLine2 = $UnapprovedTaxAuthorityDetails->PaymentAddrLine2;
                            if($PaymentAddrLine2)
                            {
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, $PaymentAddrLine2.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, ' ');
                            }
                        //PaymentAddrLine2
                        //PaymentCity
                            $PaymentCity = $UnapprovedTaxAuthorityDetails->PaymentCity;
                            if($PaymentCity)
                            {
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, $PaymentCity.',');
                            }
                            else{
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, ' ');
                            }
                        //PaymentCity
                        //PaymentState
                            $PaymentState = $UnapprovedTaxAuthorityDetails->PaymentState;
                            if($PaymentState)
                            {
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, $PaymentState.'-');
                            }
                            else{
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, ' ');
                            }
                        //PaymentState
                        //PaymentZipCode
                            $PaymentZipCode = $UnapprovedTaxAuthorityDetails->PaymentZipCode;
                            if($PaymentZipCode)
                            {
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, $PaymentZipCode);
                            }
                            else{
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, ' ');
                            }
                        //PaymentZipCode


                        //Tax Collector Name
                            $TaxCollector = $UnapprovedTaxAuthorityDetails->TaxCollector;
                            if($TaxCollector)
                            {
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, $TaxCollector);
                            }
                            else{
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, '-');
                            }
                        //Tax Authority Name
                        //Tax Payable
                            $TaxPayable = $UnapprovedTaxAuthorityDetails->TaxPayable;
                            if($TaxPayable)
                            {
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, $TaxPayable);
                            }
                            else{
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, '-');
                            }
                        //Tax Payable
                        //Collector Phone
                        $CollectorPhone = $UnapprovedTaxAuthorityDetails->CollectorPhone;
                        if($CollectorPhone)
                        {
                            $numbers_only = preg_replace("/[^\d]/", "", $CollectorPhone);
                            $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "($1)&nbsp;$2-$3", $numbers_only);
                            array_push($keys, '%%CollectorPhoneNumber%%');
                            array_push($values, $number);
                        }
                        else{
                            array_push($keys, '%%CollectorPhoneNumber%%');
                            array_push($values, '-');
                        }
                        //Collector Phone
                        //Website Address
                        $WebsiteAddress = $UnapprovedTaxAuthorityDetails->WebsiteAddress;
                        if($WebsiteAddress)
                        {
                            array_push($keys, '%%WebsiteAddr%%');
                            array_push($values, '<u>'.$WebsiteAddress.'</u>');
                        }
                        else{
                            array_push($keys, '%%WebsiteAddr%%');
                            array_push($values, '-');
                        }
                        //Website Address
                        
                    }else{
                        $ApprovedTaxAuthorityDetails = $this->Order_reports_model->GetApprovedTaxAuthorityDetails($tordertaxcerts_array[$i]->TaxAuthorityUID);
                        if($ApprovedTaxAuthorityDetails)
                        {
                            //PaymentAddrLine1
                            $PaymentAddrLine1 =$ApprovedTaxAuthorityDetails->PaymentAddrLine1;
                            if($PaymentAddrLine1)
                            {
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, $PaymentAddrLine1.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine1%%');
                                array_push($values, ' ');
                            }
                            //PaymentAddrLine1
                            //PaymentAddrLine2
                            $PaymentAddrLine2 =$ApprovedTaxAuthorityDetails->PaymentAddrLine2;
                            if($PaymentAddrLine2)
                            {
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, $PaymentAddrLine2.',');
                            }
                            else{
                                array_push($keys, '%%PaymentAddrLine2%%');
                                array_push($values, ' ');
                            }
                            //PaymentAddrLine2
                            //PaymentCity
                            $PaymentCity =$ApprovedTaxAuthorityDetails->PaymentCity;
                            if($PaymentCity)
                            {
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, $PaymentCity.',');
                            }
                            else{
                                array_push($keys, '%%PaymentCity%%');
                                array_push($values, ' ');
                            }
                            //PaymentCity
                            //PaymentState
                            $PaymentState =$ApprovedTaxAuthorityDetails->PaymentState;
                            if($PaymentState)
                            {
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, $PaymentState.'-');
                            }
                            else{
                                array_push($keys, '%%PaymentState%%');
                                array_push($values, ' ');
                            }
                            //PaymentState
                            //PaymentZipCode
                            $PaymentZipCode =$ApprovedTaxAuthorityDetails->PaymentZipCode;
                            if($PaymentZipCode)
                            {
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, $PaymentZipCode);
                            }
                            else{
                                array_push($keys, '%%PaymentZipCode%%');
                                array_push($values, ' ');
                            }
                            //PaymentZipCode
                            //Tax Collector Name
                            $TaxCollector =$ApprovedTaxAuthorityDetails->TaxCollector;
                            if($TaxCollector)
                            {
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, $TaxCollector);
                            }
                            else{
                                array_push($keys, '%%TaxCollector%%');
                                array_push($values, '-');
                            }
                            //Tax Authority Name
                            //Tax Payable
                            $TaxPayable =$ApprovedTaxAuthorityDetails->TaxPayable;
                            if($TaxPayable)
                            {
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, $TaxPayable);
                            }
                            else{
                                array_push($keys, '%%TaxPayable%%');
                                array_push($values, '-');
                            }
                            //Tax Payable
                            //Collector Phone
                            $CollectorPhone =$ApprovedTaxAuthorityDetails->CollectorPhone;
                            if($CollectorPhone)
                            {
                                $numbers_only = preg_replace("/[^\d]/", "", $CollectorPhone);
                                $number = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", "($1)&nbsp;$2-$3", $numbers_only);
                                array_push($keys, '%%CollectorPhoneNumber%%');
                                array_push($values, $number);
                            }
                            else{
                                array_push($keys, '%%CollectorPhoneNumber%%');
                                array_push($values, '-');
                            }
                            //Collector Phone
                            //Website Address
                            $WebsiteAddress = $ApprovedTaxAuthorityDetails->WebsiteAddress;
                            if($WebsiteAddress)
                            {
                                array_push($keys, '%%WebsiteAddr%%');
                                array_push($values, '<u>'.$WebsiteAddress.'</u>');
                            }
                            else{
                                array_push($keys, '%%WebsiteAddr%%');
                                array_push($values, '-');
                            }
                            //Website Address
                        }
                        else{
                            $data = array('PaymentAddrLine1','PaymentAddrLine2','PaymentCity','PaymentState','PaymentZipCode','TaxCollector');
                            foreach ($data as $value) {
                                array_push($keys, '%%'.$value.'%%');
                                array_push($values, ' ');
    
                            }
                            $datas = array('TaxPayable','CollectorPhoneNumber','WebsiteAddr');
                            foreach ($datas as $value) {
                                array_push($keys, '%%'.$value.'%%');
                                array_push($values, '-');
                            }
                        }
        
                    }
                    //ApprovedUnapprovedTaxAuthorityDetails

                //Amount Deliquent
                    $AmountDelinquent = $tordertaxcerts_array[$i]->AmountDelinquent;
                    $AmtDelinquent = '$'.number_format($AmountDelinquent,2);
                    if($AmtDelinquent)
                    {
                        array_push($keys, '%%AmountDelinquent%%');
                        array_push($values, $AmtDelinquent);
                    }
                    else{
                        array_push($keys, '%%AmountDelinquent%%');
                        array_push($values, '-');
                    }
                //Amount Deliquent
                //Account Number
                    $ParcelNumber = $tordertaxcerts_array[$i]->ParcelNumber;
                    if($ParcelNumber)
                    {
                        array_push($keys, '%%ParcelNumber%%');
                        array_push($values, $ParcelNumber);
                    }
                    else{
                        array_push($keys, '%%ParcelNumber%%');
                        array_push($values, '-');
                    }
                    //Account Number
                    //Estimated Tax
                    $EstimatedTax = $tordertaxcerts_array[$i]->EstimatedTax;
                    $EstimatedTax = '$'.number_format($EstimatedTax,2);
                    if($EstimatedTax)
                    {
                        array_push($keys, '%%EstimatedTax%%');
                        array_push($values, $EstimatedTax);
                    }
                    else{
                        array_push($keys, '%%EstimatedTax%%');
                        array_push($values, '-');
                    }
                    //Estimated Tax
                    //Tax Basis Name
                    $TaxBasisName = $tordertaxcerts_array[$i]->TaxBasisName;
                    if($TaxBasisName)
                    {
                        array_push($keys, '%%TaxBasisName%%');
                        array_push($values, $TaxBasisName);
                    }
                    else{
                        array_push($keys, '%%TaxBasisName%%');
                        array_push($values, '-');
                    }
                    //Tax Basis Name
                    //Property Class Name
                    $PropertyClassName = $tordertaxcerts_array[$i]->PropertyClassName;
                    if($PropertyClassName)
                    {
                        array_push($keys, '%%PropertyClassName%%');
                        array_push($values, $PropertyClassName);
                    }
                    else{
                        array_push($keys, '%%PropertyClassName%%');
                        array_push($values, '-');
                    }
                   //Property Class Name
                    //Tax Comments
                    $TaxComments = $tordertaxcerts_array[$i]->TaxComments;
                    if($TaxComments)
                    {
                        array_push($keys, '%%TaxComments%%');
                        array_push($values, $TaxComments);
                        array_push($keys, '%%taxalignment%%');
                        array_push($values, 'text-left');
                    }
                    else{
                        array_push($keys, '%%TaxComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%taxalignment%%');
                        array_push($values, 'text-center');
                    }
                    //Tax Comments
                    $TaxYears = $tordertaxcerts_array[$i]->TaxYear;
                    if($TaxYears)
                    {
                        array_push($keys, '%%TaxYears%%');
                        array_push($values, $TaxYears);
                    }
                    else{
                        array_push($keys, '%%TaxYears%%');
                        array_push($values, '-');
                    }
                    //Tax Date Format Change
                    $NextTaxDueDate = $tordertaxcerts_array[$i]->NextTaxDueDate;
                    if($NextTaxDueDate == '0000-00-00')
                    {
                        array_push($keys, '%%NextTaxDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $NextTaxDate =  date('m/d/Y', strtotime($NextTaxDueDate));
                        array_push($keys, '%%NextTaxDate%%');
                        array_push($values, $NextTaxDate);
                    }
                    $NextDueDate = $taxinstallment_array[$k]->NextDueDate;
                    if($NextDueDate  == '0000-00-00' || $NextDueDate == '')
                    {
                        array_push($keys, '%%NextDueDate%%');
                        array_push($values, '-');

                    }
                    else{
                        $NextDueDate =  date('m/d/Y', strtotime($NextDueDate));
                        array_push($keys, '%%NextDueDate%%');
                        array_push($values, $NextDueDate);

                    }
                    //Tax Date Format Change
                    //Tax Date Format Change
                    $GoodThroughDate = $tordertaxcerts_array[$i]->GoodThroughDate;
                    if($GoodThroughDate == '0000-00-00' || $GoodThroughDate == ' ' )
                    {
                        array_push($keys, '%%GoodThroughDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $GoodThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                        array_push($keys, '%%GoodThroughDate%%');
                        array_push($values, $GoodThroughDate);
                    }
                    //Tax Date Format Change
                    //Tax Date Paid
                    $DatePaid = $tordertaxcerts_array[$i]->DatePaid;
                    if($DatePaid == '0000-00-00')
                    {
                        array_push($keys, '%%PaidDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $PaidDate =  date('m/d/Y', strtotime($DatePaid));
                        array_push($keys, '%%PaidDate%%');
                        array_push($values, $PaidDate);
                    }
                    //Tax Date Paid
                    //Main Loop
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);    
                //Main Loop
                }
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $TaxExemptionNames = $this->Order_reports_model->getExemptionname($OrderUID,$TaxCert);
                if($TaxExemptionNames)
                {
                    array_push($keys, '%%TaxExemptionNames%%');
                    array_push($values, $TaxExemptionNames);
                }
                else{
                    array_push($keys, '%%TaxExemptionNames%%');
                    array_push($values, '-');
                }

                $tordertaxcerts_table .= str_replace($keys, $values, $tordertaxcerts[0][0]);
                $taxinstallment_html='<tr style="border: 0.01em solid grey;">
                <td width="10.4%" class="td-bd text-center" style="">
                    <p style="font-size: 8pt;" class="">%%Tax_Year%%</p>
                </td>
                <td width="15.6%" class="td-bd text-center" style="">
                    <p style="font-size: 8pt;" class="text-center">%%Tax_InstallmentName%%</p>
                </td>
                <td width="13.5" class="td-bd text-center" style="">
                    <p style="font-size: 8pt;" class="">%%Gross_Amount%%</p>
                </td>
                <td width="14.7%" class="td-bd text-center" style="">
                    <p style="font-size: 8pt;" class="text-center">%%Tax_StatusName%%</p>
                </td>
                <td width="15.4%" class="td-bd text-center" style="">
                    <p style="font-size: 8pt;" class="">%%Amount_Paid%%</p>
                </td>
                <td width="15.2%" class="td-bd text-center" style="">
                    <p style="font-size: 8pt;" class=" text-center">%%Paid_Date%%</p>
                </td>
                <td width="15.2%" class="td-bd text-center" style="">
                    <p style="font-size: 8pt;" class=" text-center">%%NextDueDate%%</p>
                </td>
            </tr>';
                $taxinstallment="";
                $taxinstallment_table = '';
                $taxinstallment_array = array();
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $taxinstallment_array = $this->Order_reports_model->gettaxinstallment($OrderUID, $TaxCert);
                $taxinstallment_array_count = count($taxinstallment_array);
                for ($k=0; $k < $taxinstallment_array_count; $k++) 
                { 

                    $keys = array();
                    $values = array();
                    foreach ($taxinstallment_array[$k] as $key => $value) 
                    {
                        $TaxYear = $taxinstallment_array[$k]->TaxYear;
                        if($TaxYear)
                        {
                            array_push($keys, '%%Tax_Year%%');
                            array_push($values, $TaxYear);

                        }
                        else{
                            array_push($keys, '%%Tax_Year%%');
                            array_push($values, '-');

                        }
                        $TaxInstallmentName = $taxinstallment_array[$k]->TaxInstallmentName;
                        if($TaxInstallmentName)
                        {
                            array_push($keys, '%%Tax_InstallmentName%%');
                            array_push($values, $TaxInstallmentName);

                        }
                        else{
                            array_push($keys, '%%Tax_InstallmentName%%');
                            array_push($values, '-');

                        }
                        $GrossAmount = $taxinstallment_array[$k]->GrossAmount;
                        if($GrossAmount)
                        {
                            $GrossAmount = '$'.number_format($GrossAmount,2);
                            array_push($keys, '%%Gross_Amount%%');
                            array_push($values, $GrossAmount);

                        }
                        else{
                            array_push($keys, '%%Gross_Amount%%');
                            array_push($values, '-');

                        }
                        $TaxStatusName = $taxinstallment_array[$k]->TaxStatusName;
                        if($TaxStatusName)
                        {
                            array_push($keys, '%%Tax_StatusName%%');
                            array_push($values, $TaxStatusName);

                        }
                        else{
                            array_push($keys, '%%Tax_StatusName%%');
                            array_push($values, '-');

                        }
                        $AmountPaid = $taxinstallment_array[$k]->AmountPaid;
                        if($AmountPaid)
                        {
                            $AmountPaid = '$'.number_format($AmountPaid,2);
                            array_push($keys, '%%Amount_Paid%%');
                            array_push($values, $AmountPaid);

                        }
                        else{
                            array_push($keys, '%%Amount_Paid%%');
                            array_push($values, '-');

                        }
                        $DatePaid = $taxinstallment_array[$k]->DatePaid;
                        if($DatePaid  == '0000-00-00')
                        {
                            array_push($keys, '%%Paid_Date%%');
                            array_push($values, '-');

                        }
                        else{
                            $DatePaid =  date('m/d/Y', strtotime($DatePaid));
                            array_push($keys, '%%Paid_Date%%');
                            array_push($values, $DatePaid);

                        }
                        $NextDueDate = $taxinstallment_array[$k]->NextDueDate;
                        if($NextDueDate  == '0000-00-00' || $NextDueDate == '')
                        {
                            array_push($keys, '%%NextDueDate%%');
                            array_push($values, '-');

                        }
                        else{
                            $NextDueDate =  date('m/d/Y', strtotime($NextDueDate));
                            array_push($keys, '%%NextDueDate%%');
                            array_push($values, $NextDueDate);

                        }

                        $GoodThroughDate = $taxinstallment_array[$k]->GoodThroughDate;
                        if($GoodThroughDate  == '0000-00-00')
                        {
                            array_push($keys, '%%Good_Through_Date%%');
                            array_push($values, '-');

                        }
                        else{
                            $GoodThroughDate =  date('m/d/Y', strtotime($GoodThroughDate));
                            array_push($keys, '%%Good_Through_Date%%');
                            array_push($values, $GoodThroughDate);

                        }
                    }

                    $taxinstallment .= str_replace($keys, $values, $taxinstallment_html);

                }
                $tordertaxcerts_table = str_replace('%%taxinstallment%%', $taxinstallment, $tordertaxcerts_table);


                $taxexemption="";
                $tordertaxemption_table = '';
                $tordertaxemption_array = array();
                $TaxCert = $tordertaxcerts_array[$i]->TaxCertSNo;
                $tordertaxemption_array = $this->Order_reports_model->gettaxExemption($OrderUID, $TaxCert);
                $tordertaxemption_array_count = count($tordertaxemption_array);
                $taxexemption_html='<tr class="tordertaxemption" style="border: 0.01em solid grey;">
                <td rowspan="'. $tordertaxemption_array_count .'" width="20%" class="td-bd text-center" colspan="2" style="">
                    <p style="font-size: 8pt;" class="bold">Tax Exemption</p>
                </td>
                <td width="20%" class="td-bd text-center" colspan="6" style="">
                    <p style="font-size: 8pt;" class="text-center">%%TaxExemptionName%%</p>
                </td>
                <td width="20%" rowspan="'. $tordertaxemption_array_count .'" class="td-bd text-center" colspan="2" style="">
                    <p style="font-size: 8pt;" class="text-center bold">Exemption Amount</p>
                </td>
                <td width="20%" class="td-bd text-center" colspan="2" style="">
                    <p style="font-size: 8pt;" class="text-center">%%TaxAmount%%</p>
                </td>
                </tr>';
                for ($j=0; $j < $tordertaxemption_array_count; $j++) 
                {
                    if($j>0)
                    {
                      $taxexemption_html='<tr class="tordertaxemption" style="border: 0.01em solid grey;">
                        <td  width="20%" class="td-bd text-center" colspan="6" style="">
                            <p style="font-size: 8pt;" class="text-center">%%TaxExemptionName%%</p>
                        </td>
                        <td  width="20%" class="td-bd text-center" colspan="2" style="">
                        <p style="font-size: 8pt;" class="text-center">%%TaxAmount%%</p>
                        </td>
                        </tr>';

                    }
                    $keys = array();
                    $values = array();
                    foreach ($tordertaxemption_array[$j] as $key => $value) 
                    {
                        $TaxExemptionName = $tordertaxemption_array[$j]->TaxExemptionName;
                        if($TaxExemptionName)
                        {
                            array_push($keys, '%%TaxExemptionName%%');
                            array_push($values, $TaxExemptionName);

                        }
                        else{
                            array_push($keys, '%%TaxExemptionName%%');
                            array_push($values, '-');

                        }
                        $TaxAmount = $tordertaxemption_array[$j]->TaxAmount;
                        if($TaxAmount)
                        {
                            $TaxAmount = '$'.$TaxAmount;
                            array_push($keys, '%%TaxAmount%%');
                            array_push($values, $TaxAmount);

                        }
                        else{
                            array_push($keys, '%%TaxAmount%%');
                            array_push($values, '$0.00');

                        }

                    }

                    $taxexemption .= str_replace($keys, $values, $taxexemption_html);

                }
                $tordertaxcerts_table = str_replace('%%TaxExmp%%', $taxexemption, $tordertaxcerts_table);


 //Tax Exemption------------------------------------------------------------------------------------------
                $tordertaxcerts_table2 .= $tordertaxcerts_table;
            }

            foreach ( $doc->find(".tordertaxcerts") as $node ) 
            {
                $node->innertext = $tordertaxcerts_table2;
            }
        //Tax Ends




        //Judgement Starts
            preg_match_all('/<div class=\"torderjudgments\">(.*?)<\/div>/s',$output,$torderjudgments);
            $torderjudgments_table = '';
            $torderjudgments_array = array();
            $torderjudgments_array = $this->Order_reports_model->get_torderjudgements($OrderUID);
            $torderjudgments_array_count = count($torderjudgments_array);
            for ($i=0; $i < $torderjudgments_array_count; $i++)
            { 
                $torderjudgments_array[$i]->judgement_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($torderjudgments_array[$i] as $key => $value)
                {
                    $DocumentTypeName = $torderjudgments_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $JudgementType = $torderjudgments_array[$i]->JudgementType;
                            if($JudgementType)
                            {
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, $JudgementType);
                            }
                            else{
                                array_push($keys, '%%JudgementDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%JudgementDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //Plaintiff
                    $Plaintiff = $torderjudgments_array[$i]->Plaintiff;
                    if($Plaintiff)
                    {
                        array_push($keys, '%%Plaintiff%%');
                        array_push($values, $Plaintiff);
                    }
                    else{
                        array_push($keys, '%%Plaintiff%%');
                        array_push($values, '-');
                    }
                //Plaintiff
                //Defendant
                    $Defendent = $torderjudgments_array[$i]->Defendent;
                    if($Defendent)
                    {
                        array_push($keys, '%%Defendent%%');
                        array_push($values, $Defendent);
                    }
                    else{
                        array_push($keys, '%%Defendent%%');
                        array_push($values, '-');
                    }
                //Defendant
                //Judgement Date Format Change
                    $JDated = $torderjudgments_array[$i]->JudgementDated;
                    if($JDated == '0000-00-00')
                    {
                        array_push($keys, '%%JudgeDated%%');
                        array_push($values, '-');  
                    }
                    else{
                        $Dated =  date('m/d/Y', strtotime($JDated));
                        array_push($keys, '%%JudgeDated%%');
                        array_push($values, $Dated);  
                    }
                //Judgement Date Format Change
                //Judgement Recorded date Format Change
                    $RDated = $torderjudgments_array[$i]->JudgementRecorded;
                    if($RDated == '0000-00-00')
                    {
                        array_push($keys, '%%JudgeRecorded%%');
                        array_push($values, '-');
                    }
                    else{
                        $RecordedDated =  date('m/d/Y', strtotime($RDated));
                        array_push($keys, '%%JudgeRecorded%%');
                        array_push($values, $RecordedDated);
                    }
                // JudgementRecorded date Format Change
                //Judgement Amount
                    $JudgementAmount = $torderjudgments_array[$i]->JudgementAmount;
                    if($JudgementAmount)
                    {
                        $JudAmt = '$'.number_format($JudgementAmount,2);
                        array_push($keys, '%%JudgementAmount%%');
                        array_push($values, $JudAmt);
                    }
                    else{
                        array_push($keys, '%%JudgementAmount%%');
                        array_push($values, '-');
                    }
                //Judgement Amount
                //Judgements Comments
                    $JudgementComments = $torderjudgments_array[$i]->JudgementComments;
                    if($JudgementComments)
                    {
                        array_push($keys, '%%JudgementComments%%');
                        array_push($values, $JudgementComments);
                        array_push($keys, '%%judalignment%%');
                        array_push($values, 'text-left');

                    }
                    else{
                        array_push($keys, '%%JudgementComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%judalignment%%');
                        array_push($values, 'text-center');
                    }
                //Judgements Comments
                    $Judgement_DBVTypeUID_1 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_1);
                    $Judgement_DBVTypeValue_1 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_1;
                    $Judgement_DBVTypeUID_2 = $torderjudgments_array[$i]->Judgement_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Judgement_DBVTypeUID_2);
                    $Judgement_DBVTypeValue_2 = $torderjudgments_array[$i]->Judgement_DBVTypeValue_2;
                    if($DBVTypeName_1)
                    {
                        if($DBVTypeName_1 !== 'Case number')
                        {
                            array_push($keys, '%%JudgementDBVTypeName_1%%');
                            array_push($values, $DBVTypeName_1);
                            if($Judgement_DBVTypeValue_1)
                            {
                                array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                array_push($values, $Judgement_DBVTypeValue_1);
                            }
                            else{
                                array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                            array_push($values, '-'); 
                        }
                    }
                    else
                    {
                        array_push($keys, '%%JudgementDBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Judgement_DBVTypeValue_1%%');
                        array_push($values, '-'); 
                    }
                    if($DBVTypeName_2)
                    {
                        if($DBVTypeName_2 !== 'Case number')
                        {
                            
                            array_push($keys, '%%JudgementDBVTypeName_2%%');
                            array_push($values, $DBVTypeName_2);
                            if($Judgement_DBVTypeValue_2)
                            {
                                array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                array_push($values, $Judgement_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%JudgementDBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                            array_push($values, '-'); 
                        }
                    }
                    else{

                        array_push($keys, '%%JudgementDBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Judgement_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                //CaseNumber
                    $JudgementCaseNo = $torderjudgments_array[$i]->JudgementCaseNo;
                    if($JudgementCaseNo)
                    {
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $JudgementCaseNo);
                    }
                    else{

                        if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                        {
                            $AppendCasenumber = $Judgement_DBVTypeValue_1.','.$Judgement_DBVTypeValue_2;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);

                        }
                        if($DBVTypeName_1 == 'Case number'){
                            $AppendCasenumber = $Judgement_DBVTypeValue_1;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);

                        }
                        if($DBVTypeName_2 == 'Case number'){
                            $AppendCasenumber = $Judgement_DBVTypeValue_2;
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);
                        }
                        else{
                            $AppendCasenumber = '-';
                            array_push($keys, '%%CaseNumber%%');
                            array_push($values, $AppendCasenumber);
                        }
                    }
                //CaseNumber
                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
                $torderjudgments_table .= str_replace($keys, $values, $torderjudgments[0][0]);
            }
            foreach ( $doc->find(".torderjudgments") as $node ) 
            {
                $node->innertext = $torderjudgments_table;
            }
        //Judgments Ends
        //Liens Starts
            preg_match_all('/<div class=\"torderliens\">(.*?)<\/div>/s',$output,$torderliens);
            $torderliens_table = '';
            $torderliens_array = array();
            $torderliens_array = $this->Order_reports_model->get_torderliens($OrderUID);
            $torderliens_array_count = count($torderliens_array);
            for ($i=0; $i < $torderliens_array_count; $i++) 
            { 

                $torderliens_array[$i]->lien_increment = $i+1;
                $keys = array();
                $values = array();
                foreach ($torderliens_array[$i] as $key => $value) 
                {
                //DocumentTypeName
                    $DocumentTypeName = $torderliens_array[$i]->DocumentTypeName;
                    if($DocumentTypeName)
                    {
                        if($DocumentTypeName == 'Others')
                        {
                            $LeinType = $torderliens_array[$i]->LeinType;
                            if($LeinType)
                            {
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, $LeinType);
                            }
                            else{
                                array_push($keys, '%%LeinDocumentTypeName%%');
                                array_push($values, '-');
                            }
                        }
                        else if($DocumentTypeName !== 'Others')
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, $DocumentTypeName);
                        }
                        else
                        {
                            array_push($keys, '%%LeinDocumentTypeName%%');
                            array_push($values, '-');
                        }

                    }
                    else{
                        array_push($keys, '%%LeinDocumentTypeName%%');
                        array_push($values, '-');
                    }
                //DocumentTypeName
                //Lein Date Format Change
                    $LDated = $torderliens_array[$i]->LeinDated;
                    if($LDated == '0000-00-00')
                    {
                        array_push($keys, '%%LeinDate%%');
                        array_push($values, '-');
                    }
                    else{
                        $Dated =  date('m/d/Y', strtotime($LDated));
                        array_push($keys, '%%LeinDate%%');
                        array_push($values, $Dated);
                    }
                //Lein Date Format Change
                //Lein Recorded date Format Change
                    $RDated = $torderliens_array[$i]->LeinRecorded;
                    if($RDated == '0000-00-00')
                    {
                        array_push($keys, '%%leinRecord%%');
                        array_push($values, '-');
                    }
                    else{
                        $RecordedDated =  date('m/d/Y', strtotime($RDated));
                        array_push($keys, '%%leinRecord%%');
                        array_push($values, $RecordedDated);
                    }
                // Lein date Format Change
                    if($torderliens_array[$i]->LienTypeName =='Closed Ended')
                    {
                        $MTG = array('MTG'=>'Closed Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    if($torderliens_array[$i]->LienTypeName =='Open Ended')
                    {
                        $MTG = array('MTG'=>'Open Ended');
                        foreach($MTG as $col => $val)
                        {
                            array_push($keys, '%%'.$col.'%%');
                            array_push($values, $val);
                        }
                    }
                    else
                    {

                            array_push($keys, '%%MTG%%');
                            array_push($values, '-');
                        
                    }

                    $Lien_DBVTypeUID_1 = $torderliens_array[$i]->Lien_DBVTypeUID_1;
                    $DBVTypeName_1 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_1);
                    $Lien_DBVTypeValue_1 = $torderliens_array[$i]->Lien_DBVTypeValue_1;
                    $Lien_DBVTypeUID_2 = $torderliens_array[$i]->Lien_DBVTypeUID_2;
                    $DBVTypeName_2 = $this->Order_reports_model->GetDBVTypes($Lien_DBVTypeUID_2);
                    $Lien_DBVTypeValue_2 = $torderliens_array[$i]->Lien_DBVTypeValue_2;
                    if($DBVTypeName_1)
                    {
                        if($DBVTypeName_1 !== 'Case number')
                        {
                            array_push($keys, '%%DBVTypeName_1%%');
                            array_push($values, $DBVTypeName_1);
                            if($Lien_DBVTypeValue_1)
                            {
                                array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                array_push($values, $Lien_DBVTypeValue_1);
                            }
                            else{
                                array_push($keys, '%%Lien_DBVTypeValue_1%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%DBVTypeName_1%%');
                            array_push($values, 'Book/Page');
                            array_push($keys, '%%Lien_DBVTypeValue_1%%');
                            array_push($values, '-'); 
                        }
                    }
                    else
                    {
                        array_push($keys, '%%DBVTypeName_1%%');
                        array_push($values, 'Book/Page');
                        array_push($keys, '%%Lien_DBVTypeValue_1%%');
                        array_push($values, '-'); 
                    }
                    if($DBVTypeName_2)
                    {
                        if($DBVTypeName_2 !== 'Case number')
                        {
                            
                            array_push($keys, '%%DBVTypeName_2%%');
                            array_push($values, $DBVTypeName_2);
                            if($Lien_DBVTypeValue_2)
                            {
                                array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                array_push($values, $Lien_DBVTypeValue_2);
                            }
                            else{
                                array_push($keys, '%%Lien_DBVTypeValue_2%%');
                                array_push($values, '-');
                            }
                        }
                        else
                        {
                            array_push($keys, '%%DBVTypeName_2%%');
                            array_push($values, 'Instrument');
                            array_push($keys, '%%Lien_DBVTypeValue_2%%');
                            array_push($values, '-'); 
                        }

                    }
                    else{

                        array_push($keys, '%%DBVTypeName_2%%');
                        array_push($values, 'Instrument');
                        array_push($keys, '%%Lien_DBVTypeValue_2%%');
                        array_push($values, '-');
                    }
                    //CaseNumber
                    if($DBVTypeName_1 == 'Case number' && $DBVTypeName_2 == 'Case number')
                    {
                        $AppendCasenumber = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);

                    }
                    if($DBVTypeName_1 == 'Case number'){
                        $AppendCasenumber = $Lien_DBVTypeValue_1;
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);

                    }
                    if($DBVTypeName_2 == 'Case number'){
                        $AppendCasenumber = $Lien_DBVTypeValue_2;
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);
                    }
                    else{
                        $AppendCasenumber = '-';
                        array_push($keys, '%%CaseNumber%%');
                        array_push($values, $AppendCasenumber);
                    }
                    //CaseNumber
                    //Instrument
                    if($DBVTypeName_1 == 'Instrument' && $DBVTypeName_2 == 'Instrument')
                    {
                        $AppendInstrument = $Lien_DBVTypeValue_1.','.$Lien_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_1 == 'Instrument'){
                        $AppendInstrument = $Lien_DBVTypeValue_1;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);

                    }
                    if($DBVTypeName_2 == 'Instrument'){
                        $AppendInstrument = $Lien_DBVTypeValue_2;
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                    else{
                        $AppendInstrument = '-';
                        array_push($keys, '%%InstrumentNo_1%%');
                        array_push($values, $AppendInstrument);
                    }
                //Instrument
                //Lein Amount
                    $LeinAmount = $torderliens_array[$i]->LeinAmount;
                    if($LeinAmount)
                    {
                        $MortAmt = '$'.number_format($LeinAmount,2);
                        array_push($keys, '%%LeinAmt%%');
                        array_push($values, $MortAmt);
                    }
                    else{
                        array_push($keys, '%%LeinAmt%%');
                        array_push($values, '-');
                    }
                //Lein Amount
                //Lein Comments
                    $LeinComments = $torderliens_array[$i]->LeinComments;
                    if($LeinComments)
                    {
                        array_push($keys, '%%LeinComments%%');
                        array_push($values, $LeinComments);
                        array_push($keys, '%%leinalignment%%');
                        array_push($values, 'text-left');

                    }
                    else{
                        array_push($keys, '%%LeinComments%%');
                        array_push($values, '-');
                        array_push($keys, '%%leinalignment%%');
                        array_push($values, 'text-center');
                    }
                //Lein Comments

                    $Holder = $torderliens_array[$i]->Holder;
                    array_push($keys, '%%LeinHolder%%');
                    array_push($values, $Holder);

                    array_push($keys, '%%'.$key.'%%');
                    array_push($values, $value);
                }
                $output = str_replace($keys, $values, $output);

                $torderliens_table .= str_replace($keys, $values, $torderliens[0][0]);

            }
            foreach ( $doc->find(".torderliens") as $node ) 
            {
                $node->innertext = $torderliens_table;
            }
        //Leins Ends
            return $doc;
        }



            // Dispute PDF Reply

    function new_report_preview()
    {
        error_reporting(0);
        $OrderUID = $this->input->post('OrderUID');
        $order_details = $this->Order_reports_model->get_torders($OrderUID);
        $SavePreviewOrderNumber = $order_details->OrderNumber;
        $order_details->TemplateUID;
        if($order_details->TemplateUID == 1 || $order_details->TemplateUID == 2)
        {
            $doc = $this->isgn_property_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 2)
        {
            $doc = $this->limited_title_search_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 3)
        {
            $doc = $this->ez_close_deed_report($OrderUID);

        }
        elseif($order_details->TemplateUID == 4)
        {
            $doc = $this->ez_close_property_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 5)
        {
            $doc = $this->real_estate_property_report($OrderUID);
        }
        elseif($order_details->TemplateUID == 20)
        {
            $doc = $this->isgn_property_report_stewart($OrderUID);
        }
        else
        {
            //$doc = $this->sbi_report($OrderUID);
            $doc = $this->isgn_property_report($OrderUID);
        }

        $this->load->library('pdf');
        $param = '"en-GB-x","A4","","",10,10,10,10,6,3';
        unset($pdf);
        $pdf = $this->pdf->load($param);       
        $pdf->SetProtection(array('copy','print','modify','annot-forms','fill-forms','extract','assemble','print-highres'));
        $pdf->packTableData = true;
        $pdf->shrink_tables_to_fit = 0; 
        // $pdf->list_indent_first_level = 0; 
        $html = mb_convert_encoding($doc, 'UTF-8', 'UTF-8');
        $pdf->WriteHTML($html); // write the HTML into the PDF
        $page_count = $pdf->page;
        $doc_save = $SavePreviewOrderNumber.'.pdf';
        $pdf->Output($doc_save, '');
        $dir = FCPATH.'Templates/Pdf/'.$SavePreviewOrderNumber.'.pdf';
        $res = file_put_contents($dir,file_get_contents($doc_save));

        $this->db->select('*');
        $this->db->from('torders');
        $this->db->where('OrderUID',$OrderUID);
        $torders=$this->db->get()->row();

        $currentdate=date('Ymd');
        $current_date=date("Y-m-d-h-i-s");
        $randomnumber=str_replace("-","",$current_date);
        $Responsedocname = $torders->OrderNumber.'-'.$randomnumber.'.pdf';

        if($torders->OrderDocsPath!= NULL)
        {
          $OrderDocs_Path=$torders->OrderDocsPath;
        }else{
          $query = $this->db->query("update torders SET OrderDocsPath ='uploads/searchdocs/".$currentdate."/".$torders->OrderNumber."/"."' Where OrderNumber='".$torders->OrderNumber."' ");
          $OrderDocs_Path = 'uploads/searchdocs/'.$currentdate.'/'.$torders->OrderNumber."/";
        }

        $file = FCPATH . $OrderDocs_Path . $Responsedocname;            

        if (!is_dir($OrderDocs_Path)) {
           mkdir($OrderDocs_Path, 0777, true);
        } 

        file_put_contents($file,file_get_contents($doc_save));

        unlink($doc_save);

        $UserUID = $this->session->userdata('UserUID');
        $UserName = $this->session->userdata('UserName');
        $DocumentTypeUID = $this->common_model->getDocumentTypeUIDByDocTYpe('Final Reports');
        
        $arrData['OrderUID']=$OrderUID;
        $arrData['DocumentFileName']= $Responsedocname;
        $arrData['DisplayFileName']= $Responsedocname;
        $arrData['DocumentTypeUID']=$DocumentTypeUID;
        $arrData['UploadedDate']=date('Y-m-d H:i:s');
        $arrData['DocumentCreatedDate']=date('Y-m-d H:i:s');
        $arrData['UploadedUserUID']= $UserUID;
        $arrData['TypeOfDocument']= $DocumentTypeUID;
        $arrData['SearchModeUID']= '6';
        $Attachments=$this->Updateattachmentdetails($arrData);

        $DocumentFileName = (array)$Responsedocname;
        $TotalFiles = 1;

        //echo json_encode(array('success'=>1,'message'=>'Exception Cleared','data'=>''));exit;

        $this->stewart_model->SendDisputeReplyPDF($OrderUID,$DocumentFileName,$TotalFiles);
    }

    public function Updateattachmentdetails($data){
        $this->db->insert('torderdocuments',$data);
        if($this->db->affected_rows()>0){    
          return true;
        }else{
          return false;
        }    
    }

    function AddAttachments(){
        $OrderUID = $this->input->post('OrderUID');
        $order_details = $this->Order_reports_model->get_torders($OrderUID);
        $SavePreviewOrderNumber = $order_details->OrderNumber;
        $dir = FCPATH.'Templates/Pdf/'.$SavePreviewOrderNumber.'.pdf';

        $torderdocuments_data['OrderUID'] = $OrderUID;
        $torders = $this->Order_reports_model->Gettorders($torderdocuments_data);

        $DocumentFileName = $SavePreviewOrderNumber.'.pdf';

        $this->db->where('OrderUID', $torderdocuments_data['OrderUID']);
        $this->db->where('DocumentFileName', $DocumentFileName);
        $query=$this->db->get('torderdocuments');
        $reportfilenamecount=$query->row();

        date_default_timezone_set('US/Eastern');

        $torderdocuments_data['DocumentFileName']=$DocumentFileName;
        $torderdocuments_data['DisplayFileName']=$torderdocuments_data['DocumentFileName'];
        $OrderDocs_Path = $torders['OrderDocsPath']; 
        $date = date('Y-m-d');
        if(empty($OrderDocs_Path))
        {
            $query = $this->db->query("update torders SET OrderDocsPath ='uploads/searchdocs/".$date."/".$torders['OrderNumber']."/"."' Where OrderUID=".$torderdocuments_data['OrderUID']);
            $OrderDocs_Path = 'uploads/searchdocs/'.$date.'/'.$torders['OrderNumber']."/";
        }

        $destinationfile = FCPATH . $OrderDocs_Path . $torderdocuments_data['DocumentFileName'];

        if (!file_exists(FCPATH . $OrderDocs_Path)) {
            if(!mkdir(FCPATH .$OrderDocs_Path, 0777, true))
            {
                die('Unable to create Folder');
            }
        }

        if(file_put_contents($destinationfile ,file_get_contents($dir)))
        {
            $DocumentTypeUID = $this->common_model->getDocumentTypeUIDByDocTYpe('Final Reports');
            $torderdocuments_data['IsReport'] = 1;
            $torderdocuments_data['DocumentTypeUID'] = $DocumentTypeUID;
            $torderdocuments_data['TypeOfDocument'] = $DocumentTypeUID;
            $torderdocuments_data['DocumentCreatedDate'] = date('Y-m-d H:i:s');
            $torderdocuments_data['UploadedUserUID'] =  $this->loggedid;

            if(!$reportfilenamecount)
            {
                $result = $this->db->insert('torderdocuments', $torderdocuments_data);
            }

            chmod($destinationfile, 0777);
        }
    }









}
