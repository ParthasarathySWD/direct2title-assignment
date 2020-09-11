<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*Calculation of Due Date*/

Class calculate_businessdays_vendors {

	const MONDAY    = 1;
	const TUESDAY   = 2;
	const WEDNESDAY = 3;
	const THURSDAY  = 4;
	const FRIDAY    = 5;
	const SATURDAY  = 6;
	const SUNDAY    = 7;

	/**
    * @param DateTime   $startDate       Date to start calculations from
    * @param DateTime[] $holidays        Array of holidays, holidays are no considered business days.
    * @param int[]      $nonBusinessDays Array of days of the week which are not business days.
    */

	public function __construct(DateTime $startDate, array $holidays, array $nonBusinessDays) {
		$this->date = $startDate;
		$this->holidays = $holidays;
		$this->nonBusinessDays = $nonBusinessDays;
		$this->excludedaycount = 0; 

	}


	public function getBusinessDay_vendors(){
      //echo "---------------loop start -----------<br>";
      //print_r($this->date);
		while (true) {
			if ($this->isBusinessDay_vendors($this->date)) {
          //echo "-----------loop end---------";
				return $this->excludedaycount;
			}else{
          //echo "neglected Date : ".print_r($this->date).'<br>';
				$this->date->modify("+1 day");
				$this->excludedaycount++;
			}
		}

	}

	public function addBusinessDay_vendors($howManyDays) {
		$i = 0;
		$holiday = 0;
		while ($i < $howManyDays) {
			$this->date->modify("+1 day");
			if ($this->isBusinessDay_vendors($this->date)) {
				$i++;
			}else{
				if($holiday == 0){
					$holiday = 1;
				}
			}
		}
		return $holiday;
	}

	public function getdate_vendors()
	{
		return $this->date;
	}




	public function isBusinessDay_vendors(DateTime $date) {
		if (in_array((int)$date->format('N'), $this->nonBusinessDays)) {
        //Date is a nonBusinessDay.
			return false;
		}
		foreach ($this->holidays as $day) {
			if ($date->format('Y-m-d') == $day->format('Y-m-d')) {
        //Date is a holiday.
				return false; 
			}
		}
    //Date is a business day.
		return true;
	}
}


function getholidays_vendors()
{
	$CI =& get_instance();


	$holidays = [];
	$holidayarray = $CI->common_model->get_holidays();
	foreach ($holidayarray as $key => $value){
		$holidays[$key] = new DateTime($value['HolidayDate']);
	}
	return $holidays;
}

function get_holidays_vendors()
{
	$CI =& get_instance();

	$holidays = [];
	$holidayarray = $CI->common_model->get_holidays();
	foreach ($holidayarray as $key => $value){
		$holidays[$key] = $value['HolidayDate'];
	}
	return $holidays;
}


function get_morderpriority_vendors($PriorityUID){
	$CI = &get_instance();


	$priority = [];
	$priorityarray = $CI->common_model->get_morder_priority($PriorityUID);
	return $priorityarray;
}

function get_priority_time_vendors($VendorUID,$SubProductUID,$PriorityUID){
	$CI = &get_instance();
	$priority = [];
	$priorityarray = $CI->common_model->get_vendorprioritytime_for_duedate($VendorUID,$SubProductUID,$PriorityUID);
	return $priorityarray;
}


function checkBusinessDay_vendors($Entrydate){

	$holidays =  get_holidays_vendors();

	$holidaycheck = date('Y-m-d', strtotime($Entrydate));
	if((date('l', strtotime($Entrydate)) == 'Saturday') || (date('l', strtotime($Entrydate)) == 'Sunday')) { 
		return 0;

	}elseif(in_array($holidaycheck, $holidays)){
		return 0;
	}else{
		return 1;
	}
}

function calculate_duedate_vendors($OrderEntryTime,$VendorUID,$SubProductUID,$PriorityUID){
	$CI =& get_instance();
	$Priorityrow = get_priority_time_vendors($VendorUID,$SubProductUID,$PriorityUID);


	if(count($Priorityrow) > 0){

		$duedate = calculate_duedate_1_vendors($OrderEntryTime,$Priorityrow->PriorityTime);

		return $duedate;

	}else{

		$priority = get_morderpriority_vendors($PriorityUID);
		$PriorityTime = $priority->TAT;

		$duedate = calculate_duedate_1_vendors($OrderEntryTime,$PriorityTime);


		return $duedate;
	}

}

function get_diff_hours_vendors($OrderEntryTime){

	$Entrydateobj = new DateTime($OrderEntryTime);
	$ModifyEntrydateobj = new DateTime($OrderEntryTime);
	$ModifyEntrydateobj->modify("1 days");
	$ModifyEntrydateobj->setTime(0, 0);
	$diff = $Entrydateobj->diff($ModifyEntrydateobj);

	return $diff;

}



function calculate_duedate_1_vendors($OrderEntryTime,$PriorityTime){
    //echo "PriorityTime : ".$PriorityTime;
    //echo "<br>";
    //echo "<br>-------standard SLA--------";

	$holidays = getholidays_vendors();
	$Entrydateobj = new DateTime($OrderEntryTime);
	$ModifyEntrydateobj = new DateTime($OrderEntryTime);
	$ModifyEntrydateobj->add(new DateInterval("PT{$PriorityTime}H"));
	$timeused = get_diff_hours_vendors($OrderEntryTime);
	$interval = $Entrydateobj->diff($ModifyEntrydateobj);
	if($interval->d == 0){

		$ModifyEntrydateobj->modify("-1 second");
		$TATAdded_Date = $ModifyEntrydateobj->format('Y-m-d H:i:s');

		$calculator = new calculate_businessdays_vendors(new DateTime($TATAdded_Date),$holidays,[calculate_businessdays_vendors::SUNDAY,calculate_businessdays_vendors::SATURDAY]);

		$DateTime = $calculator->getDate_vendors();

		if($calculator->isBusinessDay_vendors($DateTime)){
        // echo '<pre> Datetime: ';print_r($DateTime);

		}else{
			$calculator->getBusinessDay_vendors();
			$DateTime = $calculator->getDate_vendors();
			$DateTime->setTime(0, 0);
			$DateTime->add(new DateInterval("PT{$PriorityTime}H"));
			$DateTime->modify("-1 second");
		}


	}else{

		$Entrydate = new DateTime($OrderEntryTime);
		$Entrydate->modify("-1 second");
		$calculator = new calculate_businessdays_vendors($Entrydate,$holidays,[calculate_businessdays_vendors::SUNDAY,calculate_businessdays_vendors::SATURDAY]);
		$excludecount = $calculator->getBusinessDay_vendors();
		$Adddate = $calculator->getDate_vendors();
		$hourTime = $Adddate->format('H');
		$minuteTime = $Adddate->format('i');
		$secondsTime = $Adddate->format('s'); 
		$calculator->addBusinessDay_vendors($interval->d);
		$DateTime = $calculator->getDate_vendors();
		$DateTime->setTime(0, 0);
		$DateTime->add( new DateInterval( 'PT' . ( (integer) $hourTime ) . 'H'. ( (integer) $minuteTime ) . 'M' . ( (integer) $secondsTime ) . 'S' ) );

	}
	$duedate = $DateTime->format('Y-m-d H:i:s');


	return $duedate;
}

function calculate_duedate_2_vendors($OrderEntryTime,$PriorityTime){
    //echo "<br>";
    //echo "PriorityTime : ".$PriorityTime.'<br>';
    //echo "<br>-------Pre/Post SLA--------";
	$checktime =  date('H:i:s',strtotime($OrderEntryTime));

	if (strtotime($checktime) > strtotime('03:00:00')) {

		$holidays = getholidays_vendors();
		$Entrydateobj = new DateTime($OrderEntryTime);
		$ModifyEntrydateobj = new DateTime($OrderEntryTime);
		$ModifyEntrydateobj->add(new DateInterval("PT{$PriorityTime}H"));
		$timeused = get_diff_hours_vendors($OrderEntryTime);
		$interval = $Entrydateobj->diff($ModifyEntrydateobj);

		if($interval->d == 0){
			$ModifyEntrydateobj->modify("-1 second");
			$TATAdded_Date = $ModifyEntrydateobj->format('Y-m-d H:i:s');

			$calculator = new calculate_businessdays_vendors(new DateTime($TATAdded_Date),$holidays,[calculate_businessdays_vendors::SUNDAY,calculate_businessdays_vendors::SATURDAY]);
			$DateTime = $calculator->getDate_vendors();


			if($calculator->isBusinessDay_vendors($DateTime)){
				/*check is business day*/
			}else{
				$calculator->getBusinessDay_vendors();
				$DateTime = $calculator->getDate_vendors();
				$DateTime->setTime(0, 0);
				$DateTime->add(new DateInterval("PT{$PriorityTime}H"));
				$DateTime->modify("-1 second");
			}

		}else{
			$Entrydate = new DateTime($OrderEntryTime);
			$Entrydate->modify("-1 second");
			$calculator = new calculate_businessdays_vendors($Entrydate,$holidays,[calculate_businessdays_vendors::SUNDAY,calculate_businessdays_vendors::SATURDAY]);
			$excludecount = $calculator->getBusinessDay_vendors();
			$Adddate = $calculator->getDate_vendors();

			$hourTime = $Adddate->format('H');
			$minuteTime = $Adddate->format('i');
			$secondsTime = $Adddate->format('s');


			$calculator->addBusinessDay_vendors($interval->d);


			$DateTime = $calculator->getDate_vendors();
			$DateTime->setTime(0, 0);
			$DateTime->add( new DateInterval( 'PT' . ( (integer) $hourTime ) . 'H'. ( (integer) $minuteTime ) . 'M' . ( (integer) $secondsTime ) . 'S' ) );
			$DateTime = $calculator->getDate_vendors();

		}
		$DateTime->modify("+1 days");
		$DateTime->setTime(0, 0);
		$DateTime->modify("-1 second");
		$duedate = $DateTime->format('Y-m-d H:i:s');
		return $duedate;

	}else{

		$holidays = getholidays_vendors();
		$Entrydateobj = new DateTime($OrderEntryTime);
		$ModifyEntrydateobj = new DateTime($OrderEntryTime);
		$ModifyEntrydateobj->add(new DateInterval("PT{$PriorityTime}H"));
		$timeused = get_diff_hours_vendors($OrderEntryTime);
		$interval = $Entrydateobj->diff($ModifyEntrydateobj);

		if($interval->d == 0){
			$ModifyEntrydateobj->modify("-1 second");
			$TATAdded_Date = $ModifyEntrydateobj->format('Y-m-d H:i:s');

			$calculator = new calculate_businessdays_vendors(new DateTime($TATAdded_Date),$holidays,[calculate_businessdays_vendors::SUNDAY,calculate_businessdays_vendors::SATURDAY]);
			$DateTime = $calculator->getDate_vendors();


			if($calculator->isBusinessDay_vendors($DateTime)){
        // echo '<pre> Datetime: ';print_r($DateTime);

			}else{
				$calculator->getBusinessDay_vendors();
				$DateTime = $calculator->getDate_vendors();
				$DateTime->setTime(0, 0);
				$DateTime->add(new DateInterval("PT{$PriorityTime}H"));
				$DateTime->modify("-1 second");
			}

		}else{
			$Entrydate = new DateTime($OrderEntryTime);
			$Entrydate->modify("-1 second");
			$calculator = new calculate_businessdays_vendors($Entrydate,$holidays,[calculate_businessdays_vendors::SUNDAY,calculate_businessdays_vendors::SATURDAY]);
			$excludecount = $calculator->getBusinessDay_vendors();
			$Adddate = $calculator->getDate_vendors();

			$hourTime = $Adddate->format('H');
			$minuteTime = $Adddate->format('i');
			$secondsTime = $Adddate->format('s');


			$calculator->addBusinessDay_vendors($interval->d);


			$DateTime = $calculator->getDate_vendors();
			$DateTime->setTime(0, 0);
			$DateTime->add( new DateInterval( 'PT' . ( (integer) $hourTime ) . 'H'. ( (integer) $minuteTime ) . 'M' . ( (integer) $secondsTime ) . 'S' ) );

		}
		$duedate = $DateTime->format('Y-m-d H:i:s');

		return $duedate;

	}
}

function calculate_duedate_3_vendors($OrderEntryTime,$PriorityTime){
    //echo "<br>-------SkipOrderOpenDate--------";
	$holidays = getholidays_vendors();
	$Entrydateobj = new DateTime($OrderEntryTime);
	$Entrydateobj->modify("+1 days");
	$ModifyEntrydateobj = new DateTime($OrderEntryTime);
	$ModifyEntrydateobj->modify("+1 days");
	$ModifyEntrydateobj->add(new DateInterval("PT{$PriorityTime}H"));
	$timeused = get_diff_hours_vendors($OrderEntryTime);
	$interval = $Entrydateobj->diff($ModifyEntrydateobj);


	if($interval->d == 0){

		$Entry_dateobj = new DateTime($OrderEntryTime);
		$Entry_dateobj->setTime(0, 0);
		$Entry_dateobj->modify("+1 days");
		$Entry_dateobj->add(new DateInterval("PT{$PriorityTime}H"));;
		$Entry_dateobj->modify("-1 second");

		$TATAdded_Date = $Entry_dateobj->format('Y-m-d H:i:s');

		$calculator = new calculate_businessdays_vendors(new DateTime($TATAdded_Date),$holidays,[calculate_businessdays_vendors::SUNDAY,calculate_businessdays_vendors::SATURDAY]);
		$DateTime = $calculator->getDate_vendors();


		if($calculator->isBusinessDay_vendors($DateTime)){


		}else{
			$calculator->getBusinessDay_vendors();
			$DateTime = $calculator->getDate_vendors();
			$DateTime->setTime(0, 0);
			$DateTime->add(new DateInterval("PT{$PriorityTime}H"));
			$DateTime->modify("-1 second");
		}

	}else{
		$Entrydate = new DateTime($OrderEntryTime);
		$Entrydate->setTime(0, 0);
		$Entrydate->modify("+1 days");
		$Entrydate->modify("-1 second");
		$calculator = new calculate_businessdays_vendors($Entrydate,$holidays,[calculate_businessdays_vendors::SUNDAY,calculate_businessdays_vendors::SATURDAY]);
		$excludecount = $calculator->getBusinessDay_vendors();
		$Adddate = $calculator->getDate_vendors();

		$hourTime = $Adddate->format('H');
		$minuteTime = $Adddate->format('i');
		$secondsTime = $Adddate->format('s');


		$calculator->addBusinessDay_vendors($interval->d);


		$DateTime = $calculator->getDate_vendors();
		$DateTime->setTime(0, 0);
		$DateTime->add( new DateInterval( 'PT' . ( (integer) $hourTime ) . 'H'. ( (integer) $minuteTime ) . 'M' . ( (integer) $secondsTime ) . 'S' ) );

	}
	$duedate = $DateTime->format('Y-m-d H:i:s');
	return $duedate;
}

/*GET TAT ORDERS*/
function Get_TAT_OrderUIDs_vendors()
{ 
	$CI =& get_instance();
	$CI->load->model('dashboard/dashboard_model');
	$Orderwithpriority = $CI->dashboard_model->GetOrdersWithPriorityDetails();
	$holiday = $CI->common_model->GetHolidays_vendors(); 
	foreach($holiday as $holiDate)
	{
		$holidays[] = $holiDate->HolidayDate;
	} 
	foreach ($Orderwithpriority as $orders) 
	{
		if($orders->SkipOrderOpenDate==1) // SkipOpen date add to holiday array
		{
			$holidays[] = $orders->SkipOrderOpenDate;
		}
		$From = $orders->OrderEntryDatetime;
		$End = date('Y-m-d H:i:s');
		if($orders->PriorityTime=='')
		{
			$TAT_time = $orders->TAT.':00';
		} else {
			$TAT_time = $orders->PriorityTime.':00';      
		}
		$duration = working_hours_vendors($From,$End,$holidays);
		$order = $CI->dashboard_model->GetTotalTATOrders($TAT_time,$orders->OrderUID,$orders->PriorityUID,$duration);
		$TAT[]= $order->TATUID; 
	}
	$TATOrder_unique = array_filter($TAT);
	$TATOrdersUID = array_values(array_unique($TATOrder_unique));  
	return $TATOrdersUID;
}


function working_hours_vendors($start,$end,$holidays)
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

	$total_min_sec = date('i:s',$diff);
	return $count .":".$total_min_sec;
}?>