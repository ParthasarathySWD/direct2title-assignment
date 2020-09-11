<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportDeliverySchedule extends MX_Controller {

	function __construct()
	{
 	  parent::__construct();
	  $this->load->model('report_delivery_schedule/report_schedule_model');
	  $this->load->model('status_report/Status_Report_model'); 
	  $this->days = array("zeroth","first","second","third","fourth","fifth","sixth","seventh","eighth","ninth");
	  $this->month = array_flip(array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'));
	}

	public function index()
	{ 	 
	  	$Schedule = $this->common_model->getReportSchedules();	 

	  if(empty($Schedule)){ echo 'No Active Schedules'; exit; } 
	  $Today = date('Y-m-d H:i');
	  echo '<pre>Current Time: '.$Today;
	  foreach($Schedule as $row) 
	  {
	  	$Cron = date('Y-m-d '.$row->DeliveryTime);
	  	$Filter = json_decode($row->ReportFilter,TRUE);
		  $function = $row->DeliveryReport;
		  echo '<pre>Loop Time: '.$Cron;
	  	switch ($row->DeliveryType) 
	  	{
	  	 	case 'Daily':
	  	 	  if($Cron == $Today)
	  	 	//   if(1 == 1)
	  	 	  {
				// $Report = $this->$function($Filter);
				$Report = $this->getReportWithRoute($row->DeliveryReport,$Filter);
	  	 	    $repsonse = $this->sendEmail($row,$Report);
	  	 	    echo 'Daily Report Send : <b>'.$Report.'</b> => '.$repsonse;
	  	 	  }
	  	 	break;

	  	 	case 'Weekly':
	  	 	  if(strtoupper(date('D')) == strtoupper($row->WeekDay) && date('H:i') == $row->DeliveryTime)
	  	 	  {
	  	 	  	$Report = $this->getReportWithRoute($row->DeliveryReport,$Filter);
	  	 	    $repsonse = $this->sendEmail($row,$Report);
	  	 	    echo 'Weekly Report Send : <b>'.$Report.'</b> => '.$repsonse.'<br>';
	  	 	  }	 
	  	 	break;

	  	 	case 'Monthly':
	  	 	  $Report = $this->MonthlySchedule($row,$Filter,$function);	
	  	 	  if(!empty($Report))
	  	 	  {
	  	 	  	$repsonse = $this->sendEmail($row,$Report);
	  	 	  	echo 'Monthly Report Send : <b>'.$Report.'</b> => '.$repsonse.'<br>';	
	  	 	  }
	  	 	break;

	  	 	case 'Quaterly':
	  	 	 $Report = $this->QuaterlySchedule($row,$Filter,$function);
	  	 	 if(!empty($Report))
	  	 	 {
	  	 	   $repsonse = $this->sendEmail($row,$Report);
	  	 	   echo 'Quaterly Report Send : <b>'.$Report.'</b> => '.$repsonse.'<br>';	
	  	 	 }
	  	 	break;

	  	 	case 'Half Yearly':
	  	 	  $Report = $this->HalfYearlySchedule($row,$Filter,$function);
	  	 	  if(!empty($Report))
	  	 	  {
	  	 	  	$repsonse = $this->sendEmail($row,$Report);
	  	 	  	echo 'Half Yearly Report Send : <b>'.$Report.'</b> => '.$repsonse.'<br>';	
	  	 	  }
	  	 	break;

	  	 	case 'Yearly':
	  	 	  $Report = $this->YearlySchedule($row,$Filter,$function);
	  	 	  if(!empty($Report))
	  	 	  {
	  	 	  	$repsonse = $this->sendEmail($row,$Report);
	  	 	  	echo 'Yearly Report Send : <b>'.$Report.'</b> => '.$repsonse.'<br>';	
	  	 	  }
	  	 	break;
	  	} 	
	  	sleep(5);
	  }
	}   

	public function MonthlySchedule($data,$filter,$function)
	{
		$copy = 'None';
		$days = $this->days;
		if($data->Every<>0 && !empty($data->WeekDay))
		{
		   $Schedule = (new DateTime($days[$data->Every].' '.$data->WeekDay.' of this month'))->format('Y-m-d');	
		   if(date('Y-m-d') == $Schedule && date('H:i') == $data->DeliveryTime)
		   {
			 $Report = $this->getReportWithRoute($function,$filter);		     
		   }
		} else {
		  if($data->Date == date('d') && date('H:i') == $data->DeliveryTime)
		  {
		    $Report = $this->getReportWithRoute($function,$filter);	
		  }
		}
	    return $Report;
	}

	public function YearlySchedule($data,$filter,$function)
	{
	  $copy = 'None';	
	  $days = $this->days;
	  $month = date('F', mktime(0, 0, 0, $data->Every, 10));
	  if($data->Every<>0 && !empty($data->WeekDay) && $data->Date<>0)
	  {
	  	$Schedule = (new DateTime($days[$data->Date].' '.$data->WeekDay.' of '.$month.' '.date('Y')))->format('Y-m-d');	
	  	if(date('Y-m-d') == $Schedule && date('H:i') == $data->DeliveryTime)
		{
		  return $this->getReportWithRoute($function,$filter);	
		}
	  } else { 
	  	$recdate = date('Y-m-d',strtotime(date('Y').'-'.$data->Every.'-'.$data->Date));
		if($recdate == date('Y-m-d') && date('H:i') == $data->DeliveryTime)
		{
		  return $this->getReportWithRoute($function,$filter);	
		}
	  }
	}

	public function HalfYearlySchedule($data,$filter,$function)
	{
		$copy = 'None';
		$days = $this->days;
		$month = date('F', mktime(0, 0, 0, $data->Every, 10));
		if($data->Every<>0 && !empty($data->WeekDay) && $data->Date<>0)
		{
		    $Schedule = (new DateTime($days[$data->Date].' '.$data->WeekDay.' of '.$month.' '.date('Y')))->format('Y-m-d');	
			if(date('Y-m-d') == $Schedule && date('H:i') == $data->DeliveryTime) 
			{
			  return $this->getReportWithRoute($function,$filter);	
			}
		} else {
			if(date('m')<=6) {
			  $curHalf = $this->getHalfYearlyMonths(1);	
			} else if(date('m')>6 && date('m')<=12) {
			  $curHalf = $this->getHalfYearlyMonths(2);		
			} 
			$recdate = date('Y').'-'.$curHalf[$data->Every].'-'.$data->Date;  
 			if($recdate == date('Y-m-d') && date('H:i') == $data->DeliveryTime)
			{
			  return $this->getReportWithRoute($function,$filter);	
			}
		}
	}

	public function QuaterlySchedule($data,$filter,$function)
	{
		$curMonth = date("m", time());
		$curQuarter = ceil($curMonth/3);
		$curQuatorMonth = $this->getQuoterMonths($curQuarter);

		$copy = 'None';
		$days = $this->days;
		if($data->Every<>0 && !empty($data->WeekDay) && $data->Date<>0)
		{
			$Schedule = (new DateTime($days[$data->Date].' '.$data->WeekDay.' of '.$curQuatorMonth[$data->Every].' '.date('Y')))->format('Y-m-d');
			if(date('Y-m-d') == $Schedule && date('H:i') == $data->DeliveryTime)
			{
			   return $this->getReportWithRoute($function,$filter);	 	
			}
		} else {
			$recquater = date('Y-m-d',strtotime(date('Y').'-'.$this->month[$curQuatorMonth[$data->Every]].'-'.$data->Date));
			if(date('Y-m-d') == $recquater && date('H:i') == $data->DeliveryTime)
			{
			   return $this->getReportWithRoute($function,$filter);	 
			}
		} 
	}

	function getQuoterMonths($quarter)
	{
		switch($quarter) 
		{
		  case 1: return array(1=>'January',2=>'February',3=>'March');
		  case 2: return array(1=>'April',2=>'May',3=>'June');
		  case 3: return array(1=>'July',2=>'August',3=>'September');
		  case 4: return array(1=>'October',2=>'November',3=>'December');
		}
	}

	function getHalfYearlyMonths($month)
	{
		switch($month) 
		{
		  case 1: return array(1=>'01',2=>'02',3=>'03',4=>'04',5=>'05',6=>'06');
		  case 2: return array(1=>'07',2=>'08',3=>'09',4=>'10',5=>'11',6=>'12');
		}
	}
 	
 	function sendEmail($data,$File='')
 	{
 	   $this->load->library('email');		
 	   $this->config->load('email');

 	   $this->email->from('notifications@direct2title.com');
 	   foreach(explode(';', $data->EmailTo) as $to) 
 	   {
 	   	 if(!empty($to))
 	   	 {
 	      $this->email->to($to);
 	   	 }
 	   }

 	   if(!empty($data->EmailCC))
 	   {
 	   	 $this->email->cc($data->EmailCC);
		}
		// @Desc Group User Mail D2T-631 @Uba @On Jun 27 2020
		$GroupMails = $this->common_model->GetGroupUserEmails($data->EmailToGroups);
		if(!empty($GroupMails)){
			foreach(array_filter($GroupMails) as $GroupMails){
				if(!empty($GroupMails))
				{
					$this->email->cc($GroupMails);
				}
			}
		}

 	   if(!empty($data->EmailBCC))
 	   {
 	   	 $this->email->bcc($data->EmailBCC);
 	   }

 	   if(!empty($File))
 	   {
 	   	 $this->email->attach($File);
 	   }
		$this->email->subject($data->EmailSubject);      
		// Schedulename replaced with message @Uba @On 10 Sep 2020
		$Message = 'Please find scheduled delivery of <b>'.$data->DeliveryScheduleName.'</b> attached. <br><br>';     
		$Message .= $data->EmailTemplate;
 	   $this->email->message($Message);
 	   if($this->email->send())
 	   {
			echo 'Mail Sent-------------------------';
 	   	 unlink($File);
 	   	 return 'Report Send Successfully';
 	   } else {
 	   	 return 'Oops!... Mail Error';
 	   } 	
 	}	

	function ExportPropertyBilled($Filter)
	{
		set_include_path( get_include_path().PATH_SEPARATOR."..");

		require_once APPPATH."third_party/xlsxwriter.class.php";

		$ColumnArray1 = array('SUB NO'=>'GENERAL','COST CENTER'=>'GENERAL','COMP NAME'=>'GENERAL','CONTACT NAME'=>'GENERAL','PROP NO'=>'GENERAL', 'ALT ORDER NO'=>'GENERAL', 'CUSTOMER REF NO'=>'GENERAL', 'API REF NO'=>'GENERAL', 'PROPERTY ADDRESS'=>'GENERAL','PROPERTY CITY'=>'GENERAL','PROPERTY STATE'=>'GENERAL','PROPERTY COUNTY'=>'GENERAL','PROPERTY ZIP CODE'=>'GENERAL','LOAN NUMBER'=>'GENERAL','ORDER DATE TIME'=>'MM/DD/YYYY HH:MM:SS','ASSIGNED DATE TIME'=>'MM/DD/YYYY HH:MM:SS','ABSTRACTOR RECEIVED DATE TIME'=>'MM/DD/YYYY HH:MM:SS','BILLED DATE TIME'=>'MM/DD/YYYY HH:MM:SS','SUB ACTUAL COST'=>'dollar','CUSTOMER DELAY'=>'GENERAL','ASSIGNMENT TIME'=>'GENERAL','ABS TIME'=>'GENERAL','RET TO BILLED'=>'GENERAL','NUM HOURS'=>'GENERAL','ASSIGNED USER'=>'GENERAL','AUTO ASSIGNED'=>'GENERAL','CURRENT QUEUE'=>'GENERAL','INTERNAL/EXTERNAL'=>'GENERAL');
		$AbstractorMultiple = $this->Status_Report_model->checkMultipleAbstractor();
	  if($AbstractorMultiple>3) // limit only 3
	  {
	  	$AbstractorMultiple = 3;
	  }
	  for ($i=1; $i <= $AbstractorMultiple; $i++) 
	  { 
	  	$abst = array('ABSTRACTOR NUMBER-'.$i=>'GENERAL', 'ABSTRACTOR NAME-'.$i=>'GENERAL','ABSTRACTOR FEE-'.$i=>'dollar','COPY COST-'.$i=>'dollar','ADDITIONAL FEE-'.$i=>'GENERAL','TOTAL ABSTRACTOR FEE-'.$i=>'dollar');
	  	$ColumnArray1 = array_merge($ColumnArray1,$abst);
	  }
	  $ColumnArray2 = array('CODE'=>'GENERAL','ABSTRACTOR RECEIVED VIA'=>'GENERAL','EDI TYPE'=>'GENERAL','AUTO BILLED'=>'GENERAL','BILLED USER'=>'GENERAL','BORROWER NAME'=>'GENERAL','EDI DATE'=>'GENERAL','EDI NOTE'=>'GENERAL','EDI USER'=>'GENERAL','DATE TIME SENT'=>'GENERAL','EMAIL ADDRESS'=>'GENERAL','DESCRIPTION'=>'GENERAL','TAT'=>'GENERAL','TAT TYPE'=>'GENERAL','ORDER BILLED USER'=>'GENERAL','ORDER BILLED DATE TIME'=>'MM/DD/YYYY HH:MM:SS');
	  $ExcelHeader = array_merge($ColumnArray1,$ColumnArray2);

	  $writer = new XLSXWriter();
	  $header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#FFFFFF', 'fill'=>'#003366');

	  $writer->writeSheetHeader('Sheet1', $ExcelHeader, $header_style);

	  // Genereate Cell data
	  $row = 2;
	  $ProductUID =  isset($Filter['ProductUID']) ? $Filter['ProductUID'] : '';  
	  $SubProductUID =  isset($Filter['SubProductUID']) ? $Filter['SubProductUID'] : '';
	  $PropertyStateUID = isset($Filter['PropertyStateUID']) ? $Filter['PropertyStateUID'] : ''; 
	  $LoanNumber =  isset($Filter['LoanNumber']) ? $Filter['LoanNumber'] : ''; 
	  $CustomerUID =   isset($Filter['CustomerUID']) ? $Filter['CustomerUID'] : ''; 
	  $PRName =  isset($Filter['PRName']) ? $Filter['PRName'] : '';
	  $FilterType =  isset($Filter['FilterType']) ? $Filter['FilterType'] : '';
	  $VendorUID =  isset($Filter['VendorUID']) ? $Filter['VendorUID'] : '';
	  $FromDate =  isset($Filter['FromDate']) ? $Filter['FromDate'] : '';
	  $ToDate =  isset($Filter['ToDate']) ? $Filter['ToDate'] : '';
  	  $formData =array_filter(array('ProductUID'=>$ProductUID,'SubProductUID'=>$SubProductUID,'PropertyStateCode'=>$PropertyStateUID,'LoanNumber'=>$LoanNumber,'CustomerUID'=>$CustomerUID,'PRName'=>$PRName,'FilterType'=>$FilterType,'VendorUID'=>$VendorUID,'FromDate'=>$FromDate,'ToDate'=>$ToDate));
  	  $post['formData'] = $formData;
  	  $post['AjaxRecord'] = 'PropertyBilled';

  	  $data = $this->Status_Report_model->GetOrderDetails($post);
  	  foreach ($data as $key => $value)  
  	  {
	    //Additional Info
  	  	$OrderUID = $value->OrderUID;
  	  	$currentQueue='';
  	  	$currentQueue=$this->common_model->GetCurrentQueueStatus($OrderUID);

  	  	$OrderCompleteDateTime_Raw = $value->OrderCompleteDateTime;

  	  	$OrderBillingDateTime_Raw = $value->BillingDateTime;

  	  	$OrderDueDatetime = $value->OrderDueDatetime;

  	  	if($OrderDueDatetime > $OrderCompleteDateTime_Raw)
  	  	{
  	  		$TATHrs = 'TAT Met'; 
  	  		$OrderDueAge = '';  
  	  	} else {
  	  		$OrderDueAge = $this->time_ago($OrderDueDatetime,$OrderCompleteDateTime_Raw); 
  	  		$TATHrs = 'TAT Miss';
  	  	}

  	  	$query = $this->db->query("SELECT GROUP_CONCAT(UserName) AS AssignedUserName, MIN(AssignedDatetime) as AssignedDatetime, COALESCE(HOUR(TIMEDIFF(CompleteDateTime, AssignedDatetime)),0) as AssignTime from torderassignment left JOIN musers on torderassignment.AssignedToUserUID = musers.UserUID WHERE OrderUID = '$OrderUID' Order by WorkflowModuleUID ASC");
  	  	$AssignedUserName = $query->row();
  	  	$AssignedUserName_Raw = $AssignedUserName->AssignedUserName;
  	  	$AssignedDatetime_Raw = $AssignedUserName->AssignedDatetime;

  	  	$sql = "SELECT AbstractorNo,AddressLine1, AbstractorCompanyName,AbstractorFirstName,AbstractorMiddleName,AbstractorLastName, MIN(AbstractorSendDateTime) AS AbstractorSendDateTime, MAX(CompletedDateTime) AS AbstractorReceivedDateTime, COALESCE(HOUR(TIMEDIFF(AbstractorReceivedDateTime, AbstractorSendDateTime)),0) as AbstraTime FROM `torderabstractor` left join mabstractor ON torderabstractor.AbstractorUID=mabstractor.AbstractorUID WHERE OrderUID='$OrderUID' ";
  	  	$absquery = $this->db->query($sql);

  	  	$abs_doc_propinfo_details = $absquery->row();

  	  	$AbstractorNo_Raw = $abs_doc_propinfo_details->AbstractorNo;
  	  	$AbstractorCompanyName_Raw = $abs_doc_propinfo_details->AbstractorCompanyName;
  	  	$AbstractorFirstName_Raw = $abs_doc_propinfo_details->AbstractorFirstName;
  	  	$AbstractorMiddleName_Raw = $abs_doc_propinfo_details->AbstractorMiddleName;
  	  	$AbstractorLastName_Raw = $abs_doc_propinfo_details->AbstractorLastName;
  	  	$AbstractorSendDateTime = $abs_doc_propinfo_details->AbstractorSendDateTime;
  	  	$AbstractorReceivedDateTime = $abs_doc_propinfo_details->AbstractorReceivedDateTime;
  	  	$abst1 = strtotime($AbstractorReceivedDateTime);
  	  	$abst2 = strtotime($AbstractorSendDateTime);
  	  	$absdiff = $abst1 - $abst2;
  	  	$ABSTime = $absdiff / ( 60 * 60 );

  	  	$SearchAsOfDate = $abs_doc_propinfo_details->SearchAsOfDate;

  	  	if($SearchAsOfDate === '0000-00-00 00:00:00' || $SearchAsOfDate == ''){
  	  		$SearchAsOfDate_Raw = '';
  	  	}
  	  	else{
  	  		$SearchAsOfDate_Raw = date('m/d/Y',strtotime($SearchAsOfDate));
  	  	}


  	  	$StatusUID_Raw = $value->StatusUID;

  	  	if($StatusUID_Raw == 100){
  	  		$OpenStatus = 'C';
  	  	}
  	  	else{
  	  		$OpenStatus = 'O';
  	  	}

  	  	if($value->CancelStatus == 1)
  	  	{
  	  		$CancelDateTime = $value->CancellationApproveDeclineTime;
  	  	}
  	  	else
  	  	{
  	  		$CancelDateTime = NULL;
  	  	}

  	  	$APN_Raw = $abs_doc_propinfo_details->APN;

  	  	$CustomerDelay = $this->Status_Report_model->GetCustomerDelay($value->OrderUID); 
  	  	$CustomerDelay = $CustomerDelay->CustomerDelay;
  	  	if($CustomerDelay == 0){
  	  		$CustDelay = 'N';
  	  	}
  	  	else{
  	  		$CustDelay = 'Y';
  	  	}

  	  	$AssignTime = $AssignedUserName->AssignTime.' hours';
  	  	$AbstraTime =  round($ABSTime).' hours';
  	  	$RetTime = $value->RetTime.' hours';
  	  	$NumofHours = $AssignTime + $AbstraTime + $RetTime.' hours';


  	  	$this->db->select('SUM(HOUR(TIMEDIFF(AssignedDateTime, CompletedDateTime))) AS ABS_TAT', false);
  	  	$this->db->from('torderabstractor');
  	  	$this->db->where('OrderUID', $value->OrderUID);
  	  	$this->db->where('OrderStatus', 5);
  	  	$AbstraTime = $this->db->get()->row()->ABS_TAT;
  	  	$AbstraTime =  round($AbstraTime).' hours';


  	  	$this->db->select('(CASE WHEN AssignedDateTime IS NOT NULL AND AssignedDateTime != "0000-00-00 00:00:00" THEN DATE_FORMAT(AssignedDateTime, "%m/%d/%Y %H:%i:%s") ELSE "" END) AS AssignedDateTime', false);
  	  	$this->db->from('torderabstractor');
  	  	$this->db->where('OrderUID', $StatusReport->OrderUID);
  	  	$this->db->order_by('AssignedDateTime', 'ASC');
  	  	$AssignedDateTime_New = $this->db->get()->row()->AssignedDateTime;

  	  	$this->db->select('(CASE WHEN CompletedDateTime IS NOT NULL AND CompletedDateTime != "0000-00-00 00:00:00" THEN DATE_FORMAT(CompletedDateTime, "%m%d/%Y %H:%i:%s") ELSE "" END) AS CompletedDateTime', false);
  	  	$this->db->from('torderabstractor');
  	  	$this->db->where('OrderUID', $StatusReport->OrderUID);
  	  	$this->db->order_by('CompletedDateTime', 'ASC');
  	  	$CompletedDateTime_New = $this->db->get()->row()->CompletedDateTime;


  	  	if ($AssignedDatetime_Raw=='0000-00-00 00:00:00' || $AssignedDatetime_Raw == '') {
  	  		$AssignedDatetime_Raw= NULL;
  	  	}
  	  	else{
  	  		$AssignedDatetime_Raw= $AssignedDatetime_Raw;
  	  	}
		// AbstractorReceivedDateTime
  	  	if ($AbstractorReceivedDateTime=='0000-00-00 00:00:00' || $AbstractorReceivedDateTime == '') {
  	  		$AbstractorReceivedDateTime=NULL;
  	  	}
  	  	else{
  	  		$AbstractorReceivedDateTime= $AbstractorReceivedDateTime;
  	  	}
		// AbstractorSendDateTime
  	  	if ($AbstractorSendDateTime=='0000-00-00 00:00:00' || $AbstractorSendDateTime == '') {
  	  		$AbstractorSendDateTime=NULL;
  	  	}
  	  	else{
  	  		$AbstractorSendDateTime= $AbstractorSendDateTime;
  	  	}

		// OrderBillingDateTime
  	  	if ($OrderBillingDateTime_Raw=='0000-00-00 00:00:00' || $OrderBillingDateTime_Raw == '') {
  	  		$OrderBillingDateTime_Raw=NULL;
  	  	}
  	  	else{
  	  		$OrderBillingDateTime_Raw=$OrderBillingDateTime_Raw;
  	  	}

		//Date Format Validation Ends

   		// Multiple Abstractor Details sperate column
  	  	$abstquery = $this->db->query("SELECT AbstractorNo, AbstractorCompanyName,AbstractorFee, AbstractorAdditionalFee,AbstractorCopyCost,OperatorType,AbstractorActualFee FROM `torderabstractor` LEFT JOIN mabstractor ON torderabstractor.AbstractorUID=mabstractor.AbstractorUID WHERE OrderUID=".$OrderUID." LIMIT 3");
  	  	$Abstractors = $abstquery->result();

  	  	$cost_center = $value->CostCenterCode.'-'.$value->CostCenterName;

  	  	$IsInhouseExternal = $value->IsInhouseExternal == 1 ? "External" : "Internal";

  	  	$dataset1 = array($value->CustomerNumber, $cost_center, $value->CustomerName,$value->CustomerPContactName,$value->OrderNumber,$value->AltORderNumber,$value->CustomerRefNum,$value->TransactionID,$value->PropertyAddress1.','.$value->PropertyAddress2,$value->PropertyCityName,$value->PropertyStateCode,$value->PropertyCountyName,$value->PropertyZipcode,$value->LoanNumber,$value->OrderEntryDatetime,$AssignedDateTime_New,$CompletedDateTime_New,$OrderBillingDateTime_Raw,$value->CustomerAmount,$CustDelay,$AssignTime,$AbstraTime,$RetTime,$NumofHours,$AssignedUserName_Raw,'N',$currentQueue, $IsInhouseExternal);

  	  	if(count($Abstractors)>0)
  	  	{
  	  		$abstrct_empty = 0;
  	  		foreach ($Abstractors as $abstrow) 
  	  		{ 
  	  			$operator = '';
  	  			if($abstrow->OperatorType=='-')
  	  			{
  	  				$operator = '( '.$abstrow->OperatorType.' )';
  	  			}
  	  			$AbstractorActualFee = number_format($abstrow->AbstractorActualFee, 2, '.', '');    
  	  			$AbstractorCopyCost = number_format($abstrow->AbstractorCopyCost, 2, '.', '');    
  	  			$AbstractorAdditionalFee = $operator.' $'.number_format($abstrow->AbstractorAdditionalFee, 2, '.', '');    
  	  			$AbstractorFee = number_format($abstrow->AbstractorFee, 2, '.', '');    
  	  			$multabst = array($abstrow->AbstractorNo,$abstrow->AbstractorCompanyName,$AbstractorActualFee,$AbstractorCopyCost,$AbstractorAdditionalFee,$AbstractorFee);
  	  			$dataset1 = array_merge($dataset1,$multabst);
  	  			$abstrct_empty++;
  	  		} 
  	  		if($abstrct_empty!=$AbstractorMultiple)
  	  		{
  	  			for($i=0; $i < ($AbstractorMultiple-$abstrct_empty); $i++) 
  	  			{
  	  				$abst = array(' ',' ',' ',' ',' ',' '); 
  	  				$dataset1 = array_merge($dataset1,$abst);
  	  			} 
  	  		}
  	  	} else {
  	  		for($i=1; $i <= 3; $i++) 
  	  		{
  	  			$abst = array(' ',' ',' ',' ',' ',' '); 
  	  			$dataset1 = array_merge($dataset1,$abst);
  	  		} 

  	  	} 

  	  	$dataset2 = array($value->SubProductCode.' - '.$value->SubProductName,'APP',' ','N', $value->MailSendBy, $value->PRName, ' ',' ',' ',
  	  		$AbstractorSendDateTime,$value->CustomerPContactEmailID,' ',$OrderDueAge,$TATHrs,$value->OrderBilledUserName,$OrderBillingDateTime_Raw);
  	  	$Exceldataset = array_merge($dataset1,$dataset2);
  	  	$writer->writeSheetRow('Sheet1', array_values($Exceldataset));
  	  	$row++;
  	  }

  	$file = "PropertyBilled.xlsx";
  	$writer->writeToFile($file);
  	return $file;
}

function ExportPropertyInflow($Filter)
{
	set_include_path( get_include_path().PATH_SEPARATOR."..");
	require_once APPPATH."third_party/xlsxwriter.class.php";
	$writer = new XLSXWriter();
	$header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#FFFFFF', 'fill'=>'#003366');
	$ExcelHeader = array('PROP NO'=>'GENERAL','COST CENTER'=>'GENERAL','ALT ORDER NO'=>'GENERAL','CUSTOMER REF NO'=>'GENERAL','API REF NO'=>'GENERAL','SUB NO'=>'GENERAL','COMP NAME'=>'GENERAL','ORDER TIME'=>'MM/DD/YYYY HH:MM:SS','BILLED TIME'=>'MM/DD/YYYY HH:MM:SS','CANCEL TIME'=>'MM/DD/YYYY HH:MM:SS','CODE'=>'GENERAL','CURRENT STATUS'=>'GENERAL','CURRENT QUEUE'=>'GENERAL','INTERNAL/EXTERNAL'=>'GENERAL','LOAN NO'=>'GENERAL','BORROWER NAME'=>'GENERAL','PROPERTY ADDRESS '=>'GENERAL','PROPERTY CITY'=>'GENERAL','PROPERTY COUNTY '=>'GENERAL','PROPERTY STATE '=>'GENERAL','PROPERTY ZIP CODE'=>'GENERAL','SUB ACTL COST'=>'dollar','ASSIGNED DATE TIME'=>'MM/DD/YYYY HH:MM:SS','ABSTRACTOR RECEIVED DATE TIME'=>'MM/DD/YYYY HH:MM:SS','ABSTRACTOR NUMBER'=>'GENERAL','ABSTRACTOR_COMP NAME'=>'GENERAL','ABSTRACTOR SEARCH TYPE'=>'GENERAL','ABSTRACTOR PROJ COST'=>'dollar','ABSTRACTOR COPY COST'=>'dollar','ABSTRACTOR ACTL COST'=>'dollar','CUSTOMER DELAY'=>'GENERAL','AUTO ASSIGNED'=>'GENERAL','DATE TIME SENT'=>'MM/DD/YYYY HH:MM:SS','EMAIL ADDRESS'=>'GENERAL','CONTACT NAME'=>'GENERAL','ASSIGNED USER'=>'GENERAL','BILLED USER'=>'GENERAL','APN'=>'GENERAL','TOWNSHIP'=>'GENERAL','TAX_MAP_NO'=>'GENERAL'); 
	$writer->writeSheetHeader('Sheet1', $ExcelHeader, $header_style);

	$n=2;
	$ProductUID =  isset($Filter['ProductUID']) ? $Filter['ProductUID'] : '';  
	$SubProductUID =  isset($Filter['SubProductUID']) ? $Filter['SubProductUID'] : '';
	$PropertyStateUID = isset($Filter['PropertyStateUID']) ? $Filter['PropertyStateUID'] : ''; 
	$LoanNumber =  isset($Filter['LoanNumber']) ? $Filter['LoanNumber'] : ''; 
	$CustomerUID =   isset($Filter['CustomerUID']) ? $Filter['CustomerUID'] : ''; 
	$PRName =  isset($Filter['PRName']) ? $Filter['PRName'] : '';
	$FilterType =  isset($Filter['FilterType']) ? $Filter['FilterType'] : '';
	$FromDate =  isset($Filter['FromDate']) ? $Filter['FromDate'] : '';
	$ToDate =  isset($Filter['ToDate']) ? $Filter['ToDate'] : '';
	$StatusUID = implode(',',$Filter['StatusUID']); 
	$Workflow = implode(',', $Filter['Workflow']);
	$InternalExternal =  $this->input->post('InternalExternal'); 
	$formData =array_filter(array('ProductUID'=>$ProductUID,'SubProductUID'=>$SubProductUID,'PropertyStateCode'=>$PropertyStateUID,'LoanNumber'=>$LoanNumber,'StatusUID'=>$StatusUID,'Workflow'=>$Workflow,'CustomerUID'=>$CustomerUID,'PRName'=>$PRName,'FromDate'=>$FromDate,'ToDate'=>$ToDate,'InternalExternal'=>$InternalExternal));
	$post['formData'] = $formData;  
	$post['AjaxRecord'] = 'PropertyInflow';
	$data = $this->Status_Report_model->GetOrderDetails($post);
  // file_put_contents('sql_property.txt', serialize($data)); exit;
	foreach ($data as $key => $value) 
	{
	//Additional Info
		$OrderUID = $value->OrderUID;
		$currentQueue='';
		$currentQueue=$this->common_model->GetCurrentQueueStatus($OrderUID);

		$OrderCompleteDateTime_Raw = $value->OrderCompleteDateTime;

		$OrderBillingDateTime_Raw = $value->BillingDateTime;

		$OrderDueDatetime = $value->OrderDueDatetime;

		$OrderDueAge = $this->Status_Report_model->time_ago($OrderDueDatetime);
		$AbstractorActualCost = number_format($value->AbstractorFee, 2, '.', '');

		$query = $this->db->query("SELECT GROUP_CONCAT(UserName) AS AssignedUserName,MIN(AssignedDatetime) as AssignedDatetime from torderassignment left JOIN musers on torderassignment.AssignedToUserUID = musers.UserUID WHERE OrderUID = '$OrderUID' Order by WorkflowModuleUID ASC");
		$AssignedUserName = $query->row();
		$AssignedUserName_Raw = $AssignedUserName->AssignedUserName;
		$AssignedDatetime_Raw = $AssignedUserName->AssignedDatetime;

		$query = $this->db->query("SELECT AbstractorNo, AbstractorCompanyName,AbstractorFirstName,AbstractorMiddleName,AbstractorLastName,AbstractorReceivedDateTime FROM `torderabstractor` left join mabstractor ON torderabstractor.AbstractorUID=mabstractor.AbstractorUID WHERE OrderUID='$OrderUID'");
		$AbstractorNo = $query->row();
		if(is_object($AbstractorNo))
		{
			$AbstractorNo_Raw = $AbstractorNo->AbstractorNo;
			$AbstractorCompanyName_Raw = $AbstractorNo->AbstractorCompanyName;
			$AbstractorFirstName_Raw = $AbstractorNo->AbstractorFirstName;
			$AbstractorMiddleName_Raw = $AbstractorNo->AbstractorMiddleName;
			$AbstractorLastName_Raw = $AbstractorNo->AbstractorLastName;
			$AbstractorReceivedDateTime = $AbstractorNo->AbstractorReceivedDateTime;
		} else {
			$AbstractorNo_Raw = '';
			$AbstractorCompanyName_Raw = '';
			$AbstractorFirstName_Raw = '';
			$AbstractorMiddleName_Raw = '';
			$AbstractorLastName_Raw = '';
			$AbstractorReceivedDateTime = NULL;
		}

		$query = $this->db->query("SELECT SearchAsOfDate FROM `torderdocuments` where OrderUID='$OrderUID' AND SearchAsOfDate IS NOT NULL");
		$SearchAsOfDate = $query->row();
		$SearchAsOfDate = $SearchAsOfDate->SearchAsOfDate;

		$SearchAsOfDate_Raw = $SearchAsOfDate;

		$StatusUID_Raw = $value->StatusUID;

		if($StatusUID_Raw == 100){
			$OpenStatus = 'C';
		}
		else{
			$OpenStatus = 'O';
		}

		if($value->CancelStatus == 1)
		{
			$CancelDateTime = $value->CancellationApproveDeclineTime;
		}
		else
		{
			$CancelDateTime = NULL;
		}

		if(($value->StatusUID<>100) && ($value->IsBilled==1))
		{
			$StatusName = 'Billed';
		} else {
			$StatusName = $value->StatusName;
		}

 //Custdelay
		$CustomerDelay = $this->Status_Report_model->GetCustomerDelay($value->OrderUID); 
		$CustomerDelay = $CustomerDelay->CustomerDelay;
		if($CustomerDelay == 0){
			$CustDelay = 'N';
		}
		else{
			$CustDelay = 'Y';
		}
  //Custdelay
  //APN
		$query = $this->db->query("SELECT APN,City from `torderpropertyinfo` where OrderUID='$OrderUID'");
		$APN = $query->row();
		$Township = $APN->City;
		$APN_Raw = $APN->APN;
  //APN
  //Parcel Number
		$query = $this->db->query("SELECT DISTINCT ParcelNumber from `tordertaxcerts` where OrderUID='$OrderUID'");
		$Parcel = $query->row();
		if(is_object($Parcel))
		{
			$ParcelNumber = $Parcel->ParcelNumber;
		} else {
			$ParcelNumber = '';
		}
  //Parcel Number
  //Abstractor assigned user name
		$query = $this->db->query("SELECT AbstractorFirstName from `mabstractor` LEFT JOIN torderabstractor ON torderabstractor.AbstractorUID = mabstractor.AbstractorUID where OrderUID='$OrderUID'");
		$AbstractorAssignedUserName = $query->result();
		$absname = '';
		foreach ($AbstractorAssignedUserName as $key => $data) {
			$absname .= $data->AbstractorFirstName.',';
		}

		$abstrassignedname = rtrim($absname,',');
		if($abstrassignedname)
		{
			$absassign =  $abstrassignedname;
		}else{
			$absassign =  '';
		}
  //Abstractor assigned user name
  //Report delivered date time
		$query = $this->db->query("SELECT MailSendDateTime,EmailReportTo FROM `torders` where OrderUID ='$OrderUID' and StatusUID = 100 and IsBilled = 1 ");
		$ReportDeliveredDateTime = $query->row();
		if(is_object($ReportDeliveredDateTime))
		{
			$EmailReportTo = $ReportDeliveredDateTime->EmailReportTo;
			$ReportDeliveredDateTime_Raw = $ReportDeliveredDateTime->MailSendDateTime;
		} else {
			$EmailReportTo = '';
			$ReportDeliveredDateTime_Raw = '';
		}
  //report delivered date time
  //Assigned date time
		$query = $this->db->query("SELECT OrderUID, MIN(AssignedDatetime) as AssignedDatetime from `torderassignment` where OrderUID='$OrderUID'");
		$AssignedDatetime = $query->row();
		$AssignedDatetime_Raw = $AssignedDatetime->AssignedDatetime;
  //Assigned date time
  //Auto assigned
		$query = $this->db->query("SELECT   SelfManualAssign from `torderassignment` WHERE OrderUID='$OrderUID'");
		$autoassignresults = $query->result();
		$autoassign = '';
		foreach ($autoassignresults as $key => $res) 
		{
			if($res->SelfManualAssign == 'MANUAL' || $res->SelfManualAssign == 'EXTERNAL')
			{
				$autoassign .= 'NO'.'/';
			}else{
				$autoassign .= 'YES'.'/';
			}
		}
		$autoassignorder = rtrim($autoassign,'/');
  		//Auto assigned
		$cost_center = $value->CostCenterCode.'-'.$value->CostCenterName;

		$IsInhouseExternal = $value->IsInhouseExternal == 1 ? "External" : "Internal";

		$Exceldataset = array($value->OrderNumber,$cost_center,$value->AltORderNumber,$value->CustomerRefNum,$value->TransactionID,$value->CustomerNumber,$value->CustomerName,$value->OrderEntryDatetime,$OrderBillingDateTime_Raw,$CancelDateTime,$value->SubProductCode.' - '.$value->SubProductName,$StatusName,$currentQueue,$IsInhouseExternal,$value->LoanNumber,$value->PRName,$value->PropertyAddress1.' '.$value->PropertyAddress2,$value->PropertyCityName,$value->PropertyCountyName,$value->PropertyStateCode,$value->PropertyZipcode,$value->CustomerAmount,$AssignedDatetime_Raw,$AbstractorReceivedDateTime,$AbstractorNo_Raw,$AbstractorCompanyName_Raw,$value->OrderTypeName,'', number_format($value->AbstractorCopyCost, 2, '.', ''), $AbstractorActualCost, $CustDelay, $autoassignorder, $ReportDeliveredDateTime_Raw, $EmailReportTo, $value->CustomerPContactName, $absassign, $value->OrderBilledUserName, $APN_Raw, $Township, $ParcelNumber); 
		$writer->writeSheetRow('Sheet1', array_values($Exceldataset));
		$n++;
	}

	$file = "PropertyInflow.xlsx";
	$writer->writeToFile($file);
	return $file;
 } 

	 function ExportPropertyUnBilled($Filter)
	 {
	 	set_include_path( get_include_path().PATH_SEPARATOR."..");
	 	require_once APPPATH."third_party/xlsxwriter.class.php";

	 	$ColumnArray1 = array('SUB NO'=>'GENERAL','CODE CENTER'=>'GENERAL','COMP NAME'=>'GENERAL','CONTACT NAME'=>'GENERAL','PROP NO'=>'GENERAL','ALT ORDER NO'=>'GENERAL','CUSTOMER REF NO'=>'GENERAL','API REF NO'=>'GENERAL','PROPERTY ADDRESS'=>'GENERAL','PROPERTY CITY'=>'GENERAL','PROPERTY STATE'=>'GENERAL','PROPERTY COUNTY'=>'GENERAL','PROPERTY ZIP CODE'=>'GENERAL','LOAN NUMBER'=>'GENERAL','ORDER DATE TIME'=>'MM/DD/YYYY HH:MM:SS','ASSIGNED DATE TIME'=>'MM/DD/YYYY HH:MM:SS','ABSTRACTOR RECEIVED DATE TIME'=>'MM/DD/YYYY HH:MM:SS','BILLED DATE TIME'=>'MM/DD/YYYY HH:MM:SS','SUB ACTUAL COST'=>'dollar','CUSTOMER DELAY'=>'GENERAL','ASSIGNMENT TIME'=>'GENERAL','ABS TIME'=>'GENERAL','RET TO BILLED'=>'GENERAL','NUM HOURS'=>'GENERAL','ASSIGNED USER'=>'GENERAL','AUTO ASSIGNED'=>'GENERAL');
	 	$AbstractorMultiple = $this->Status_Report_model->checkMultipleAbstractor();
		  if($AbstractorMultiple>3) // limit only 3
		  {
		  	$AbstractorMultiple = 3;
		  }
		  for ($i=1; $i <= $AbstractorMultiple; $i++) 
		  { 
		  	$abst = array('ABSTRACTOR NUMBER-'.$i=>'GENERAL','COMPANY NAME-'.$i=>'GENERAL','ABSTRACTOR FEE-'.$i=>'dollar','COPY COST-'.$i=>'dollar','ADDITIONAL FEE-'.$i=>'GENERAL','TOTAL ABSTRACTOR FEE-'.$i=>'dollar');
		  	$ColumnArray1 = array_merge($ColumnArray1,$abst);
		  }
		  $ColumnArray2 = array('CODE'=>'GENERAL','ABSTRACTOR RECEIVED VIA'=>'GENERAL','EDI TYPE'=>'GENERAL','AUTO BILLED'=>'GENERAL','BILLED USER'=>'GENERAL','BORROWER NAME'=>'GENERAL','EDI DATE'=>'GENERAL','EDI NOTE'=>'GENERAL','EDI USER'=>'GENERAL','DATE TIME SENT'=>'MM/DD/YYYY HH:MM:SS','EMAIL ADDRESS'=>'GENERAL','DESCRIPTION'=>'GENERAL','TAT'=>'GENERAL','TAT TYPE'=>'GENERAL');
		  $ExcelHeader = array_merge($ColumnArray1,$ColumnArray2);
		  $writer = new XLSXWriter();
		  $header_style = array('font'=>'Calibri','font-size'=>11,'font-style'=>'bold','valign'=>'top', 'color'=>'#FFFFFF', 'fill'=>'#003366');
		  $writer->writeSheetHeader('Sheet1', $ExcelHeader, $header_style);

		// Genereate Cell data
		  $row = 2;
		  $ProductUID =  isset($Filter['ProductUID']) ? $Filter['ProductUID'] : '';  
		  $SubProductUID =  isset($Filter['SubProductUID']) ? $Filter['SubProductUID'] : '';
		  $PropertyStateUID = isset($Filter['PropertyStateUID']) ? $Filter['PropertyStateUID'] : ''; 
		  $LoanNumber =  isset($Filter['LoanNumber']) ? $Filter['LoanNumber'] : ''; 
		  $CustomerUID =   isset($Filter['CustomerUID']) ? $Filter['CustomerUID'] : ''; 
		  $PRName =  isset($Filter['PRName']) ? $Filter['PRName'] : '';
		  $FilterType =  isset($Filter['FilterType']) ? $Filter['FilterType'] : '';
		  $FromDate =  isset($Filter['FromDate']) ? $Filter['FromDate'] : '';
		  $ToDate =  isset($Filter['ToDate']) ? $Filter['ToDate'] : '';
		  $StatusUID = implode(',',$Filter['StatusUID']); 
		  $Workflow = implode(',', $Filter['Workflow']);
		  $formData =array_filter(array('ProductUID'=>$ProductUID,'SubProductUID'=>$SubProductUID,'PropertyStateCode'=>$PropertyStateUID,'LoanNumber'=>$LoanNumber,'CustomerUID'=>$CustomerUID,'PRName'=>$PRName,'FromDate'=>$FromDate,'ToDate'=>$ToDate,'StatusUID'=>$StatusUID,'Workflow'=>$Workflow));
		  $post['formData'] = $formData;
		  $post['AjaxRecord'] = 'PropertyUnBilled';
		  $data = $this->Status_Report_model->GetOrderDetails($post);
		  foreach ($data as $key => $value)  
		  {
		    //Additional Info
		  	$OrderUID = $value->OrderUID;

		  	$OrderCompleteDateTime_Raw = $value->OrderCompleteDateTime;

		  	$OrderBillingDateTime_Raw = $value->BillingDateTime;

		  	$OrderDueDatetime = $value->OrderDueDatetime;

		  	if($OrderDueDatetime > $OrderCompleteDateTime_Raw)
		  	{
		  		$TATHrs = 'TAT Met'; 
		  		$OrderDueAge = '';  
		  	} else {
		  		$OrderDueAge = $this->time_ago($OrderDueDatetime,$OrderCompleteDateTime_Raw); 
		  		$TATHrs = 'TAT Miss';
		  	}

		  	$query = $this->db->query("SELECT GROUP_CONCAT(UserName) AS AssignedUserName,COALESCE(HOUR(TIMEDIFF(CompleteDateTime, AssignedDatetime)),0) as AssignTime,MIN(AssignedDatetime) as AssignedDatetime from torderassignment left JOIN musers on torderassignment.AssignedToUserUID = musers.UserUID WHERE OrderUID = '$OrderUID' Order by WorkflowModuleUID ASC");
		  	$AssignedUserName = $query->row();
		  	$AssignedUserName_Raw = $AssignedUserName->AssignedUserName;
		  	$AssignedDatetime_Raw = $AssignedUserName->AssignedDatetime;

		  	$query = $this->db->query("SELECT AbstractorNo, AbstractorCompanyName,AbstractorFirstName,AbstractorMiddleName,AbstractorLastName, MIN(AbstractorSendDateTime) AS AbstractorSendDateTime, MAX(CompletedDateTime) AS AbstractorReceivedDateTime, COALESCE(HOUR(TIMEDIFF(AbstractorReceivedDateTime, AbstractorSendDateTime)),0) as AbstraTime FROM `torderabstractor` left join mabstractor ON torderabstractor.AbstractorUID=mabstractor.AbstractorUID WHERE OrderUID='$OrderUID' ");
		  	$AbstractorNo = $query->row();
		  	$AbstractorNo_Raw = $AbstractorNo->AbstractorNo;
		  	$AbstractorCompanyName_Raw = $AbstractorNo->AbstractorCompanyName;
		  	$AbstractorFirstName_Raw = $AbstractorNo->AbstractorFirstName;
		  	$AbstractorMiddleName_Raw = $AbstractorNo->AbstractorMiddleName;
		  	$AbstractorLastName_Raw = $AbstractorNo->AbstractorLastName;
		  	$AbstractorReceivedDateTime = $AbstractorNo->AbstractorReceivedDateTime;
		  	$AbstractorSendDateTime = $AbstractorNo->AbstractorSendDateTime;

		    // AbstractorSendDateTime
		  	if ($AbstractorSendDateTime=='0000-00-00 00:00:00' || $AbstractorSendDateTime == '') {
		  		$AbstractorSendDateTime=NULL;
		  	}
		  	else{
		  		$AbstractorSendDateTime = $AbstractorSendDateTime;
		  	}

		    // AbstractorReceivedDateTime
		  	if ($AbstractorReceivedDateTime=='0000-00-00 00:00:00' || $AbstractorReceivedDateTime == '') {
		  		$AbstractorReceivedDateTime=NULL;
		  	}
		  	else{
		  		$AbstractorReceivedDateTime = $AbstractorReceivedDateTime;
		  	}
		    // OrderAssignedDateTime
		  	if ($AssignedDatetime_Raw=='0000-00-00 00:00:00' || $AssignedDatetime_Raw == '') {
		  		$AssignedDatetime_Raw=NULL;
		  	}
		  	else{
		  		$AssignedDatetime_Raw = $AssignedDatetime_Raw;
		  	}



		  	$query = $this->db->query("SELECT SearchAsOfDate FROM `torderdocuments` where OrderUID='$OrderUID' AND SearchAsOfDate IS NOT NULL");
		  	$SearchAsOfDate = $query->row();
		  	$SearchAsOfDate = $SearchAsOfDate->SearchAsOfDate;

		  	if($SearchAsOfDate === '0000-00-00 00:00:00' || $SearchAsOfDate == ''){
		  		$SearchAsOfDate_Raw = NULL;
		  	}
		  	else{
		  		$SearchAsOfDate_Raw = $SearchAsOfDate;
		  	}

		  	$StatusUID_Raw = $value->StatusUID;

		  	if($StatusUID_Raw == 100){
		  		$OpenStatus = 'C';
		  	}
		  	else{
		  		$OpenStatus = 'O';
		  	}

		  	if($value->CancelStatus == 1)
		  	{
		  		$CancelDateTime = $value->CancellationApproveDeclineTime;
		  	}
		  	else
		  	{
		  		$CancelDateTime = NULL;
		  	}


		  // OrderBillingDateTime
		  	if ($OrderBillingDateTime_Raw=='0000-00-00 00:00:00' || $OrderBillingDateTime_Raw == '') {
		  		$OrderBillingDateTime_Raw=NULL;
		  	}
		  	else{
		  		$OrderBillingDateTime_Raw = $OrderBillingDateTime_Raw;
		  	}

		  	$query = $this->db->query("SELECT APN from `torderpropertyinfo` where OrderUID='$OrderUID'");
		  	$APN = $query->row();
		  	$APN_Raw = $APN->APN;

		  	$CustomerDelay = $this->Status_Report_model->GetCustomerDelay($value->OrderUID); 
		  	$CustomerDelay = $CustomerDelay->CustomerDelay;
		  	if($CustomerDelay == 0){
		  		$CustDelay = 'N';
		  	}
		  	else{
		  		$CustDelay = 'Y';
		  	}

		  	$AssignTime = $AssignedUserName->AssignTime.' hours';
		  	$AbstraTime =  round($AbstractorNo->AbstraTime).' hours';
		  	$RetTime = $value->RetTime.' hours';
		  	$NumofHours = $AssignTime + $AbstraTime + $RetTime.' hours';

		  	$cost_center = $value->CostCenterCode.'-'.$value->CostCenterName;

		  	$dataset1 = array($value->CustomerNumber,$cost_center,$value->CustomerName,$value->CustomerPContactName,$value->OrderNumber,$value->AltORderNumber,$value->CustomerRefNum,$value->TransactionID,$value->PropertyAddress1.','.$value->PropertyAddress2,$value->PropertyCityName,$value->PropertyStateCode,$value->PropertyCountyName,$value->PropertyZipcode,$value->LoanNumber,$value->OrderEntryDatetime,$AssignedDatetime_Raw,$AbstractorReceivedDateTime,$OrderBillingDateTime_Raw,$value->CustomerAmount,$CustDelay,$AssignTime,$AbstraTime,$RetTime,$NumofHours,$AssignedUserName_Raw,'N');

		 // Multiple Abstractor Details sperate column
		  	$abstquery = $this->db->query("SELECT AbstractorNo, AbstractorCompanyName,AbstractorFee, AbstractorAdditionalFee,AbstractorCopyCost,OperatorType,AbstractorActualFee FROM `torderabstractor` LEFT JOIN mabstractor ON torderabstractor.AbstractorUID=mabstractor.AbstractorUID WHERE OrderUID=".$OrderUID." LIMIT 3");
		  	$Abstractors = $abstquery->result(); 
		  	$AbstractorMultiple = $this->Status_Report_model->checkMultipleAbstractor();
		     if($AbstractorMultiple>3) // limit only 3
		     {
		     	$AbstractorMultiple = 3;
		     }

		     if(count($Abstractors)>0)
		     {
		     	$abstrct_empty = 0;
		     	foreach ($Abstractors as $abstrow) 
		     	{ 
		     		$operator = '';
		     		if($abstrow->OperatorType=='-')
		     		{
		     			$operator = '( '.$abstrow->OperatorType.' )';
		     		}
		     		$AbstractorActualFee = number_format($abstrow->AbstractorActualFee, 2, '.', '');    
		     		$AbstractorCopyCost = number_format($abstrow->AbstractorCopyCost, 2, '.', '');    
		     		$AbstractorAdditionalFee = '$'.number_format($abstrow->AbstractorAdditionalFee, 2, '.', '').' '.$operator;    
		     		$AbstractorFee = number_format($abstrow->AbstractorFee, 2, '.', '');    
		     		$multabst = array($abstrow->AbstractorNo,$abstrow->AbstractorCompanyName,$AbstractorActualFee,$AbstractorCopyCost,$AbstractorAdditionalFee,$AbstractorFee);
		     		$dataset1 = array_merge($dataset1,$multabst);
		     		$abstrct_empty++;
		     	} 
		     	if($abstrct_empty!=$AbstractorMultiple)
		     	{
		     		for($i=0; $i < ($AbstractorMultiple-$abstrct_empty); $i++) 
		     		{
		     			$abst = array(' ',' ',' ',' ',' ',' '); 
		     			$dataset1 = array_merge($dataset1,$abst);
		     		} 
		     	}
		     } else {
		     	for($i=1; $i <= $AbstractorMultiple; $i++) 
		     	{
		     		if($i==1)
		     		{
		     			$operator = '';
		     			if($abstrow->OperatorType=='-')
		     			{
		     				$operator = '( '.$abstrow->OperatorType.' )';
		     			}
		     			$AbstractorActualFee = number_format($abstrow->AbstractorActualFee, 2, '.', '');    
		     			$AbstractorCopyCost = number_format($abstrow->AbstractorCopyCost, 2, '.', '');    
		     			$AbstractorAdditionalFee = $operator.' $'.number_format($abstrow->AbstractorAdditionalFee, 2, '.', '').' '.$operator;    
		     			$AbstractorFee = number_format($abstrow->AbstractorFee, 2, '.', '');    
		     			$abst = array($AbstractorNo_Raw,$AbstractorCompanyName_Raw,$AbstractorActualFee,$AbstractorCopyCost,$AbstractorAdditionalFee,$AbstractorFee);
		     			$dataset1 = array_merge($dataset1,$abst);
		     		} else {
		     			$abst = array(' ',' ',' ',' ',' ',' '); 
		     			$dataset1 = array_merge($dataset1,$abst);
		     		}
		     	} 
		     }

		     $dataset2 = array($value->SubProductCode.' - '.$value->SubProductName,'APP',' ','N',$value->MailSendBy,$value->PRName,' ',' ',' ',$value->AbstractorSendDateTime,$value->CustomerPContactEmailID,' ',$OrderDueAge,$TATHrs);
		     $Exceldataset = array_merge($dataset1,$dataset2);
		     $writer->writeSheetRow('Sheet1', array_values($Exceldataset));
		     $row++;
		 }

		 $file = "uploads/temp_reportdelivery/PropertyUnBilled.xlsx";
		 $writer->writeToFile($file);
		 return $file;
	}

	function time_ago($time,$end_time) 
	{
	  // List of Holidays
		$holiday = $this->common_model->GetHolidays(); 
		foreach($holiday as $holiDate)
		{
			$holidays[] = $holiDate->HolidayDate;
		}
		$Hrs = $this->working_hours($time,$end_time,$holidays);
		$time_difference = $this->HourstoSeconds($Hrs);

		$seconds = $time_difference;  
	  $minutes      = round($seconds / 60 );           // value 60 is seconds  
	  $hours           = round($seconds / 3600);           //value 3600 is 60 minutes * 60 sec  
	  $days          = round($seconds / 86400);          //86400 = 24 * 60 * 60;  
	  $weeks          = round($seconds / 604800);          // 7*24*60*60;  
	  $months          = round($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60  
	  $years          = round($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60  
	  if($seconds <= 60)  
	  { 
	  	if($seconds<0){
	  		return '';
	  	}else{
	  		return "$seconds sec";  
	  	} 
	  }  
	  else if($minutes <=60)  
	  {  
	  	if($minutes==1)  
	  	{  
	  		return "$minutes min";  
	  	}  
	  	else  
	  	{
	  		if($minutes<1){
	  			return "$seconds seconds";  
	  		} else {
	  			return "$minutes Min ";  
	  		}  
	  	}  
	  }  
	  else if($hours <=24)  
	  {  
	  	if($hours==1)  
	  	{  
	  		return "1 Hrs ";  
	  	}  
	  	else  
	  	{  
	  		return "$hours hours";  
	  	}  
	  }  
	  else if($days <= 7)  
	  {  
	  	if($days==1)  
	  	{  
	  		return "$days days";  
	  	}  
	  	else  
	  	{
	  		if($days<1){
	  			return "$hours hours"; 
	  		} 
	  		else{
	  			return "$days Days ";  
	  		} 
	  	}  
	  }  
	  else if($weeks <= 4.3) //4.3 == 52/12  
	  {   
	  	return "$days days";  
	  }  
	  else if($months <=12)  
	  {  
	  	return "$days days ";  

	  } 
	  else  
	  {  
	  	if($years==1)  
	  	{  
	  		return "1 Year";  
	  	}  
	  	else  
	  	{ 
	  		return "$years Years";	    
	  	}   
	  }  
	}  

	function HourstoSeconds($time) 
	{
		$sec = 0;
		foreach (array_reverse(explode(':', $time)) as $k => $v) $sec += pow(60, $k) * $v;
		return $sec;
	}

	function working_hours($start,$end,$holidays)
	{
		$startDate = new DateTime($start);
		$endDate = new DateTime($end);
		$periodInterval = new DateInterval("PT1H");
		$period = new DatePeriod($startDate, $periodInterval, $endDate);
		$count = 0;

		foreach($period as $date)
		{
			$startofday = clone $date;
			$startofday->setTime(00,01);
			$endofday = clone $date;
			$endofday->setTime(24,00);

			if($date > $startofday && $date <= $endofday && !in_array($date->format('l'), array('Sunday','Saturday'))
				&& !in_array($date->format('Y-m-d'),$holidays))
			{
				$count++;
			}    
		}

	  //Get seconds of Start time
		$start_d = date("Y-m-d H:00:00", strtotime($start));
		$start_d_seconds = strtotime($start_d);
		$start_t_seconds = strtotime($start);
		$start_seconds = $start_t_seconds - $start_d_seconds;

	  //Get seconds of End time
		$end_d = date("Y-m-d H:00:00", strtotime($end));
		$end_d_seconds = strtotime($end_d);
		$end_t_seconds = strtotime($end);
		$end_seconds = $end_t_seconds - $end_d_seconds;
		$diff = $end_seconds-$start_seconds;
		if($diff!=0):
			$count--;
		endif;

		if($count>0)
		{
			$count = $count;
		} else {
			$count = '00';
		}

		$total_min_sec = date('i:s',$diff);
		return $count .":".$total_min_sec;
	} 

	/**
		* @description Report CURL Function
		* @param 
		* @throws no exception
		* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
		* @return File 
		* @since  Jul 2 2020
		* @version  
		*/ 
	function getReportWithRoute($url,$post){

		$URIs = $this->config->item('AutoDeliveryReportRoutes');
		// echo '<pre>';print_r($URIs[$url]);exit;
		$post['IsReportDelivery'] = TRUE;
		
		if($url == 'PropertyBilled'){
			$post['AjaxRecord'] = 'PropertyBilled';
		}
		if($url == 'PropertyUnBilled'){
			$post['AjaxRecord'] = 'PropertyUnBilled';
		}
		if($url == 'Pipeline'){
			$post['AjaxRecord'] = 'Pipeline';
		}
		if($url == 'PropertyInflow'){
			$post['AjaxRecord'] = 'PropertyInflow';
		}
		if($url == 'AbstractorAssignment'){
			$post['AjaxRecord'] = 'AbstractorAssignment';
		}
		if($url == 'EmailImportExcel_Success'){
			$post['IsReceived'] = 'Success';
		}
		if($url == 'EmailImportExcel_Failure'){
			$post['IsReceived'] = 'Failure';
		}
		if($url == 'EmailImportExcel_Invalid'){
			$post['IsReceived'] = 'Invalid';
		}
		if($url == 'rms_report_excel_inprogress'){
			$post['AjaxRecord'] = 'excel_inprogress';
		}		
		if($url == 'rms_report_excel_submission'){
			$post['AjaxRecord'] = 'excel_submission';
		}		
		if($url == 'rms_report_excel_allinflow'){
			$post['AjaxRecord'] = 'excel_allinflow';
		}
		if($url == 'rms_report_customer_inprogress'){
			$post['AjaxRecord'] = 'excel_inprogress';
		}		
		if($url == 'rms_report_customer_excel_submission'){
			$post['AjaxRecord'] = 'excel_submission';
		}		
		if($url == 'rms_report_customer_excel_allinflow'){
			$post['AjaxRecord'] = 'excel_allinflow';
		}
		if($url == 'CustomerUserReport'){
			$post['usertype'] = 'CustUser';
		}
		if($url == 'OrgUserReport'){
			$post['usertype'] = 'OrgUser';
		}
		if($url == 'tat_assign_report'){
			$post['AjaxRecord'] = 'TATAssignReport';
		}

		
		$url = base_url().$URIs[$url];
		echo 'Url: '.$url;
		// echo '<pre>';print_r($post);exit;
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => json_encode($post),
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json",
			),
		));	
		$response = curl_exec($curl);
		$response = json_decode($response,TRUE);

		return $response['file'];
	}

}
