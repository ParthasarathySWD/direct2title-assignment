<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*Calculation of Due Date*/

Class calculate_businessdays {

	const MONDAY    = 1;
	const TUESDAY   = 2;
	const WEDNESDAY = 3;
	const THURSDAY  = 4;
	const FRIDAY    = 5;
	const SATURDAY  = 6;
	const SUNDAY    = 7;
  const DayHours  = 24;
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
      date_default_timezone_set('US/Eastern');

    }


    public function getBusinessDay(){
      //echo "---------------loop start -----------<br>";
      //print_r($this->date);
    	while (true) {
    		if ($this->isBusinessDay($this->date)) {
          //echo "-----------loop end---------";
    			return $this->excludedaycount;
    		}else{
          //echo "neglected Date : ".print_r($this->date).'<br>';
    			$this->date->modify("+1 day");
    			$this->excludedaycount++;
    		}
    	}

    }

    public function addBusinessDay($howManyDays) {
    	$i = 0;
    	$holiday = 0;
    	while ($i < $howManyDays) {
    		$this->date->modify("+1 day");
    		if ($this->isBusinessDay($this->date)) {
    			$i++;
    		}else{
    			if($holiday == 0){
    				$holiday = 1;
    			}
    		}
    	}
    	return $holiday;
    }

    public function addBusinessHours($howManyHours) {
      $i = 0;
      $holiday = 0;
      while ($i < $howManyHours) {
        $this->date->modify("+1 hour");
        if ($this->isBusinessDay($this->date)) {
          $i++;
        }else if($holiday == 0){
          $holiday = 1;
          $this->date->modify("-1 hour");
          $this->date->modify("+1 day");
          $this->addBusinessHours($howManyHours - $i);
        }
      }
      return $holiday;
    }

    public function addBusinessMin($howManyMin) {
      $i = 0;
      $holiday = 0;
      while ($i < $howManyMin) {
        $this->date->modify("+1 minute");
        if ($this->isBusinessDay($this->date)) {
          $i++;
        }else if($holiday == 0){
          $holiday = 1;
          $this->date->modify("-1 minute");
          $this->date->modify("+1 day");
          addBusinessHours($howManyMin - $i);
        }
      }
      return $holiday;
    }

    public function addBusinessSec($howManySec) {
      $i = 0;
      $holiday = 0;
      while ($i < $howManySec) {
        $this->date->modify("+1 second");
        if ($this->isBusinessDay($this->date)) {
          $i++;
        }else if($holiday == 0){
          $holiday = 1;
          $this->date->modify("-1 second");
          $this->date->modify("+1 day");
          addBusinessHours($howManySec - $i);
        }
      }
      return $holiday;
    }

    public function getdate()
    {
    	return $this->date;
    }




    public function isBusinessDay(DateTime $date) {
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


  function getholidays()
  {
  	$CI =& get_instance();


  	$holidays = [];
  	$holidayarray = $CI->common_model->get_holidays();
  	foreach ($holidayarray as $key => $value){
  		$holidays[$key] = new DateTime($value['HolidayDate']);
  	}
  	return $holidays;
  }

  function get_holidays()
  {
  	$CI =& get_instance();

  	$holidays = [];
  	$holidayarray = $CI->common_model->get_holidays();
  	foreach ($holidayarray as $key => $value){
  		$holidays[$key] = $value['HolidayDate'];
  	}
  	return $holidays;
  }


  function get_morderpriority($PriorityUID){
  	$CI =& get_instance();


  	$priority = [];
  	$priorityarray = $CI->common_model->get_morder_priority($PriorityUID);
  	return $priorityarray;
  }

  function get_priority_time($CustomerUID,$SubProductUID,$PriorityUID){
  	$CI =& get_instance();

  	$priority = [];
  	$priorityarray = $CI->common_model->get_prioritytime_for_duedate($CustomerUID,$SubProductUID,$PriorityUID);
  	return $priorityarray;
  }


  function checkBusinessDay($Entrydate){

  	$holidays =  get_holidays();

  	$holidaycheck = date('Y-m-d', strtotime($Entrydate));
  	if((date('l', strtotime($Entrydate)) == 'Saturday') || (date('l', strtotime($Entrydate)) == 'Sunday')) { 
  		return 0;

  	}elseif(in_array($holidaycheck, $holidays)){
  		return 0;
  	}else{
  		return 1;
  	}
  }

  function calculate_duedate($OrderEntryTime,$CustomerUID,$SubProductUID,$PriorityUID){
  	$CI =& get_instance();


  	$Priorityrow = get_priority_time($CustomerUID,$SubProductUID,$PriorityUID);


  	if(count($Priorityrow) > 0){

  		if($Priorityrow->SkipOrderOpenDate == '1'){

  			$duedate = calculate_duedate_1($OrderEntryTime,$Priorityrow->PriorityTime);

  		}elseif ($Priorityrow->SkipOrderOpenDate == '2') {

  			$duedate = calculate_duedate_2($OrderEntryTime,$Priorityrow->PriorityTime);

  		}elseif ($Priorityrow->SkipOrderOpenDate == '3') {

  			$duedate = calculate_duedate_3($OrderEntryTime,$Priorityrow->PriorityTime);

  		}else { 

  			$duedate = calculate_duedate_1($OrderEntryTime,$Priorityrow->PriorityTime);
  		}

  		return $duedate;

  	}else{

  		$priority = get_morderpriority($PriorityUID);
  		$PriorityTime = $priority->TAT;

  		$duedate = calculate_duedate_1($OrderEntryTime,$PriorityTime);


  		return $duedate;
  	}

  }

  function get_diff_hours($OrderEntryTime){

  	$Entrydateobj = new DateTime($OrderEntryTime);
  	$ModifyEntrydateobj = new DateTime($OrderEntryTime);
  	$ModifyEntrydateobj->modify("1 days");
  	$ModifyEntrydateobj->setTime(0, 0);
  	$diff = $Entrydateobj->diff($ModifyEntrydateobj);

  	return $diff;

  }



  function calculate_duedate_1($OrderEntryTime,$PriorityTime){
    //echo "PriorityTime : ".$PriorityTime;
    //echo "<br>";
    //echo "<br>-------standard SLA--------";

  	$holidays = getholidays();
  	$Entrydateobj = new DateTime($OrderEntryTime);
  	$ModifyEntrydateobj = new DateTime($OrderEntryTime);
  	$ModifyEntrydateobj->add(new DateInterval("PT{$PriorityTime}H"));
  	$timeused = get_diff_hours($OrderEntryTime);
  	$interval = $Entrydateobj->diff($ModifyEntrydateobj);
  	if($interval->d == 0){

  		$ModifyEntrydateobj->modify("-1 second");
  		$TATAdded_Date = $ModifyEntrydateobj->format('Y-m-d H:i:s');

  		$calculator = new calculate_businessdays(new DateTime($TATAdded_Date),$holidays,[calculate_businessdays::SUNDAY,calculate_businessdays::SATURDAY]);

  		$DateTime = $calculator->getDate();

  		if($calculator->isBusinessDay($DateTime)){
        // echo '<pre> Datetime: ';print_r($DateTime);

  		}else{
  			$calculator->getBusinessDay();
  			$DateTime = $calculator->getDate();
  			$DateTime->setTime(0, 0);
  			$DateTime->add(new DateInterval("PT{$PriorityTime}H"));
  			$DateTime->modify("-1 second");
  		}


  	}else{

  		$Entrydate = new DateTime($OrderEntryTime);
  		$Entrydate->modify("-1 second");
  		$calculator = new calculate_businessdays($Entrydate,$holidays,[calculate_businessdays::SUNDAY,calculate_businessdays::SATURDAY]);
  		$excludecount = $calculator->getBusinessDay();
  		$Adddate = $calculator->getDate();
  		$hourTime = $Adddate->format('H');
  		$minuteTime = $Adddate->format('i');
  		$secondsTime = $Adddate->format('s'); 
  		$calculator->addBusinessDay($interval->d);
  		$DateTime = $calculator->getDate();
  		$DateTime->setTime(0, 0);
  		$DateTime->add( new DateInterval( 'PT' . ( (integer) $hourTime ) . 'H'. ( (integer) $minuteTime ) . 'M' . ( (integer) $secondsTime ) . 'S' ) );

  	}
  	$duedate = $DateTime->format('Y-m-d H:i:s');


  	return $duedate;
  }

  function calculate_duedate_2($OrderEntryTime,$PriorityTime){
    //echo "<br>";
    //echo "PriorityTime : ".$PriorityTime.'<br>';
    //echo "<br>-------Pre/Post SLA--------";
  	$checktime =  date('H:i:s',strtotime($OrderEntryTime));

  	if (strtotime($checktime) > strtotime('03:00:00')) {

  		$holidays = getholidays();
  		$Entrydateobj = new DateTime($OrderEntryTime);
  		$ModifyEntrydateobj = new DateTime($OrderEntryTime);
  		$ModifyEntrydateobj->add(new DateInterval("PT{$PriorityTime}H"));
  		$timeused = get_diff_hours($OrderEntryTime);
  		$interval = $Entrydateobj->diff($ModifyEntrydateobj);

  		if($interval->d == 0){
  			$ModifyEntrydateobj->modify("-1 second");
  			$TATAdded_Date = $ModifyEntrydateobj->format('Y-m-d H:i:s');

  			$calculator = new calculate_businessdays(new DateTime($TATAdded_Date),$holidays,[calculate_businessdays::SUNDAY,calculate_businessdays::SATURDAY]);
  			$DateTime = $calculator->getDate();


  			if($calculator->isBusinessDay($DateTime)){
  				/*check is business day*/
  			}else{
  				$calculator->getBusinessDay();
  				$DateTime = $calculator->getDate();
  				$DateTime->setTime(0, 0);
  				$DateTime->add(new DateInterval("PT{$PriorityTime}H"));
  				$DateTime->modify("-1 second");
  			}

  		}else{
  			$Entrydate = new DateTime($OrderEntryTime);
  			$Entrydate->modify("-1 second");
  			$calculator = new calculate_businessdays($Entrydate,$holidays,[calculate_businessdays::SUNDAY,calculate_businessdays::SATURDAY]);
  			$excludecount = $calculator->getBusinessDay();
  			$Adddate = $calculator->getDate();

  			$hourTime = $Adddate->format('H');
  			$minuteTime = $Adddate->format('i');
  			$secondsTime = $Adddate->format('s');


  			$calculator->addBusinessDay($interval->d);


  			$DateTime = $calculator->getDate();
  			$DateTime->setTime(0, 0);
  			$DateTime->add( new DateInterval( 'PT' . ( (integer) $hourTime ) . 'H'. ( (integer) $minuteTime ) . 'M' . ( (integer) $secondsTime ) . 'S' ) );
  			$DateTime = $calculator->getDate();

  		}
  		$DateTime->modify("+1 days");
  		$DateTime->setTime(0, 0);
  		$DateTime->modify("-1 second");
  		$duedate = $DateTime->format('Y-m-d H:i:s');
  		return $duedate;

  	}else{

  		$holidays = getholidays();
  		$Entrydateobj = new DateTime($OrderEntryTime);
  		$ModifyEntrydateobj = new DateTime($OrderEntryTime);
  		$ModifyEntrydateobj->add(new DateInterval("PT{$PriorityTime}H"));
  		$timeused = get_diff_hours($OrderEntryTime);
  		$interval = $Entrydateobj->diff($ModifyEntrydateobj);

  		if($interval->d == 0){
  			$ModifyEntrydateobj->modify("-1 second");
  			$TATAdded_Date = $ModifyEntrydateobj->format('Y-m-d H:i:s');

  			$calculator = new calculate_businessdays(new DateTime($TATAdded_Date),$holidays,[calculate_businessdays::SUNDAY,calculate_businessdays::SATURDAY]);
  			$DateTime = $calculator->getDate();


  			if($calculator->isBusinessDay($DateTime)){
        // echo '<pre> Datetime: ';print_r($DateTime);

  			}else{
  				$calculator->getBusinessDay();
  				$DateTime = $calculator->getDate();
  				$DateTime->setTime(0, 0);
  				$DateTime->add(new DateInterval("PT{$PriorityTime}H"));
  				$DateTime->modify("-1 second");
  			}

  		}else{
  			$Entrydate = new DateTime($OrderEntryTime);
  			$Entrydate->modify("-1 second");
  			$calculator = new calculate_businessdays($Entrydate,$holidays,[calculate_businessdays::SUNDAY,calculate_businessdays::SATURDAY]);
  			$excludecount = $calculator->getBusinessDay();
  			$Adddate = $calculator->getDate();

  			$hourTime = $Adddate->format('H');
  			$minuteTime = $Adddate->format('i');
  			$secondsTime = $Adddate->format('s');


  			$calculator->addBusinessDay($interval->d);


  			$DateTime = $calculator->getDate();
  			$DateTime->setTime(0, 0);
  			$DateTime->add( new DateInterval( 'PT' . ( (integer) $hourTime ) . 'H'. ( (integer) $minuteTime ) . 'M' . ( (integer) $secondsTime ) . 'S' ) );

  		}
  		$duedate = $DateTime->format('Y-m-d H:i:s');

  		return $duedate;

  	}
  }

  function calculate_duedate_3($OrderEntryTime,$PriorityTime){
    //echo "<br>-------SkipOrderOpenDate--------";
  	$holidays = getholidays();
  	$Entrydateobj = new DateTime($OrderEntryTime);
  	$Entrydateobj->modify("+1 days");
  	$ModifyEntrydateobj = new DateTime($OrderEntryTime);
  	$ModifyEntrydateobj->modify("+1 days");
  	$ModifyEntrydateobj->add(new DateInterval("PT{$PriorityTime}H"));
  	$timeused = get_diff_hours($OrderEntryTime);
  	$interval = $Entrydateobj->diff($ModifyEntrydateobj);


  	if($interval->d == 0){
  		
  		$Entry_dateobj = new DateTime($OrderEntryTime);
  		$Entry_dateobj->setTime(0, 0);
  		$Entry_dateobj->modify("+1 days");
  		$Entry_dateobj->add(new DateInterval("PT{$PriorityTime}H"));;
  		$Entry_dateobj->modify("-1 second");

  		$TATAdded_Date = $Entry_dateobj->format('Y-m-d H:i:s');

  		$calculator = new calculate_businessdays(new DateTime($TATAdded_Date),$holidays,[calculate_businessdays::SUNDAY,calculate_businessdays::SATURDAY]);
  		$DateTime = $calculator->getDate();


  		if($calculator->isBusinessDay($DateTime)){


  		}else{
  			$calculator->getBusinessDay();
  			$DateTime = $calculator->getDate();
  			$DateTime->setTime(0, 0);
  			$DateTime->add(new DateInterval("PT{$PriorityTime}H"));
  			$DateTime->modify("-1 second");
  		}

  	}else{
  		$Entrydate = new DateTime($OrderEntryTime);
  		$Entrydate->setTime(0, 0);
  		$Entrydate->modify("+1 days");
  		$Entrydate->modify("-1 second");
  		$calculator = new calculate_businessdays($Entrydate,$holidays,[calculate_businessdays::SUNDAY,calculate_businessdays::SATURDAY]);
  		$excludecount = $calculator->getBusinessDay();
  		$Adddate = $calculator->getDate();

  		$hourTime = $Adddate->format('H');
  		$minuteTime = $Adddate->format('i');
  		$secondsTime = $Adddate->format('s');


  		$calculator->addBusinessDay($interval->d);


  		$DateTime = $calculator->getDate();
  		$DateTime->setTime(0, 0);
  		$DateTime->add( new DateInterval( 'PT' . ( (integer) $hourTime ) . 'H'. ( (integer) $minuteTime ) . 'M' . ( (integer) $secondsTime ) . 'S' ) );

  	}
  	$duedate = $DateTime->format('Y-m-d H:i:s');
  	return $duedate;
  }

  /*GET TAT ORDERS*/
  function Get_TAT_OrderUIDs()
  { 
  	$CI =& get_instance();
  	$CI->load->model('dashboard/dashboard_model');
  	$Orderwithpriority = $CI->dashboard_model->GetOrdersWithPriorityDetails();
  	$holiday = $CI->common_model->GetHolidays(); 
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
    $duration = working_hours($From,$End,$holidays);
    $order = $CI->dashboard_model->GetTotalTATOrders($TAT_time,$orders->OrderUID,$orders->PriorityUID,$duration);
    $TAT[]= $order->TATUID; 
  }
  $TATOrder_unique = array_filter($TAT);
  $TATOrdersUID = array_values(array_unique($TATOrder_unique));  
  return $TATOrdersUID;
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

	$total_min_sec = date('i:s',$diff);
	return $count .":".$total_min_sec;
}

  function Calculate_RM_DueDate($StartDateTime, $datevalues = [])
  {
    
    $Entrydateobj = new DateTime($StartDateTime);

    if(count($datevalues) == 4 && $datevalues[1]/24 > 1){

      $datevalues[0] = floor($datevalues[1]/24); 
      $datevalues[1] = $datevalues[1]%24;  
    }

    if (count($datevalues) == 4) {
      $AddDate = 'P' . $datevalues[0] . 'D' . 'T' . $datevalues[1] . 'H' . $datevalues[2] . 'M' . $datevalues[3] . 'S';
      $ModifyEntrydateobj = new DateTime($StartDateTime);
      $ModifyEntrydateobj->add(new DateInterval($AddDate));
      $ModifyEntrydateobj->modify("-1 second");
      $duedate = $ModifyEntrydateobj->format('Y-m-d H:i:s');
      return $duedate;
    }
    else{
      $duedate = $Entrydateobj->format('Y-m-d H:i:s');
      return $duedate;
    }
    $holidays = getholidays();

    if($interval->d == 0){

      // $ModifyEntrydateobj->modify("-1 second");
      $TATAdded_Date = $ModifyEntrydateobj->format('Y-m-d H:i:s');

      $calculator = new calculate_businessdays(new DateTime($TATAdded_Date),[],[]);

      $DateTime = $calculator->getDate();

      if($calculator->isBusinessDay($DateTime)){
        // echo '<pre> Datetime: ';print_r($DateTime);

      }else{
        $calculator->getBusinessDay();
        $DateTime = $calculator->getDate();
        $DateTime->setTime(0, 0);
        $DateTime->add(new DateInterval($AddDate));
        // $DateTime->modify("-1 second");
      }


    }else{

      $Entrydate = new DateTime($StartDateTime);
      $Entrydate->modify("-1 second");
      $calculator = new calculate_businessdays($Entrydate,[],[]);
      $excludecount = $calculator->getBusinessDay();

      $calculator->addBusinessDay($interval->d);
      $calculator->addBusinessHours($interval->h);
      $calculator->addBusinessMin($interval->i);
      $calculator->addBusinessSec($interval->s);
      $Adddate = $calculator->getDate();
      $hourTime = $Adddate->format('H');
      $minuteTime = $Adddate->format('i');
      $secondsTime = $Adddate->format('s'); 

      // echo "Time: " . $hourTime . ' : ' . $minuteTime . ':' . $secondsTime;exit;
      $DateTime = $calculator->getDate();
      // $DateTime->setTime(0, 0);
      $DateTime->add( new DateInterval($AddDate ) );
      // $DateTime->add( new DateInterval())

    }
    $duedate = $DateTime->format('Y-m-d H:i:s');


    return $duedate;


  }

  
  function d2t_format_date($formatdate)
  {
    $CI =& get_instance();
    return !empty($formatdate) && $formatdate != '0000-00-00 00:00:00' && $formatdate != '0000-00-00' ? date($CI->config->item('date_format'),strtotime($formatdate)) : '';
  }

  //datetime format
  function d2t_datetimeformat($formatdate)
  {
    $CI =& get_instance();
    return !empty($formatdate) && $formatdate != '0000-00-00 00:00:00' && $formatdate != '0000-00-00' ? date($CI->config->item('date_format'),strtotime($formatdate)) : '';
  }

  //date only
  function d2t_dateformat($formatdate)
  {
    $CI =& get_instance();
    return !empty($formatdate) && $formatdate != '0000-00-00 00:00:00' && $formatdate != '0000-00-00' ? date($CI->config->item('dateonly_format'),strtotime($formatdate)) : '';
  }

  function db_datevalidation($date)
  {
    return preg_match("/^\d\d\d\d-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/", $date);
  }

  // Validate state is valid or invalid
  function validateState($zipcode,$county,$state)
  {
    return $state;	
    $CI =& get_instance();
    $CI->load->database();
    
    // 5 digits less then zipcode add 0 prefix for missing count
    $ZipCode = strtok($zipcode,'-');
    if(strlen($ZipCode)<5)
    {
      $missing = 5 - strlen($ZipCode);  
      $ZipCode = str_pad($ZipCode, $missing + strlen($ZipCode), '0', STR_PAD_LEFT);
    } else {
      $ZipCode = substr($ZipCode, 0,5);
    }

    $CI->db->select('mstates.StateCode');
    $CI->db->from('mcities');
    $CI->db->join('mcounties','mcounties.CountyUID = mcities.CountyUID','INNER');
    $CI->db->join('mstates','mstates.StateUID = mcounties.StateUID','INNER');
    $CI->db->where('mstates.StateCode', $state);
    $CI->db->where('mcounties.CountyName', $county);
    $CI->db->where('mcities.ZipCode', $zipCode);
    $valid = $CI->db->get()->num_rows();
    if(empty($valid))
    {
      $CI->db->select('mstates.StateCode');
      $CI->db->from('mcities');
      $CI->db->join('mcounties','mcounties.CountyUID = mcities.CountyUID','INNER');
      $CI->db->join('mstates','mstates.StateUID = mcounties.StateUID','INNER');
      $CI->db->where('mstates.StateCode', $state);
      $CI->db->where('mcounties.CountyName', $county);
      $CI->db->like('mcities.ZipCode', $zipCode,FALSE);
      $valid = $CI->db->get()->num_rows();
    }
    return $valid;
  }

    /**
   * Money Symbol Formatter
   *
   * @param Number $amount
   * @return Number
  /**********@author Parthasarathy -Friday 1 JULY 2020**********/

  function moneyFormatter($amount){
    if ($amount != "" || $amount != NULL) {
      return "$".number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $amount)),2);
    }
    return '$0.0';
  }

   /**
   * Money Symbol Formatter
   *
   * @param Number $amount
   * @return Number or empty
  /**********@author alwin -9/10/2020**********/

  function moneyFormatterNotZero($amount){
    if ($amount != "" || $amount != NULL || $amount != 0) {
      return "$".number_format(sprintf('%0.2f', preg_replace("/[^0-9.]/", "", $amount)),2);
    }
    return '';
  }

  /**
   * Multi-array search
   *
   * @param array $array
   * @param array $search
   * @return array
  /**********@author praveen kumar -Friday 31 January 2020**********/

  function multi_array_search_keyvalues($array, $search,$limit=1)
  {

  	// Create the result array
  	$result = array();

  	// Iterate over each array element
  	foreach ($array as $key => $value)
  	{

  		// Iterate over each search condition
  		foreach ($search as $k => $v)
  		{

  			// If the array element does not meet the search condition then continue to the next element
  			if (!isset($value[$k]) || $value[$k] != $v)
  			{
  				continue 2;
  			}

  		}

  		$result[] = $value;

  		// Add the array element's key to the result array
  		if(!empty($result) && count($result) <= $limit) {

  			return $result;
  		}


  	}

  	// Return the result array
  	return $result;

  }

  //check function is defined
  if(!function_exists('calculate_sladuedate')) {
  	//trigger for all orders
  	function calculate_sladuedate($SLA_list,$OrderEntryTime) {
  		if($SLA_list->TurnTimeCalculation == 'Effective Next Day Turn Time') {
  			return get_nextturntat_duedate($SLA_list,$OrderEntryTime);
  		} else {
  			return get_regulartat_duedate($SLA_list,$OrderEntryTime);
  		}
  	}
  }

   //check function is defined
  if(!function_exists('get_regulartat_duedate')) {
  	function get_regulartat_duedate($SLA_list,$OrderEntryTime)
  	{
		//echo "<br>-------standard SLA DUE DATE--------";
  		$PriorityTime = $SLA_list->PriorityTime;
  		$holidays = $weekend = [];
  		$ExcludeSuspensionTime = explode(',', $SLA_list->ExcludeSuspensionTime);
  		if(in_array('Organization Holidays', $ExcludeSuspensionTime)) {
  			$holidays = getholidays();
  		}

  		if(in_array('Weekend', $ExcludeSuspensionTime)) {
  			$weekend = [calculate_businessdays::SUNDAY,calculate_businessdays::SATURDAY];
  		}

  		$Entrydateobj = new DateTime($OrderEntryTime);
  		$Entrydateobj->modify("+1 days");
  		$Entrydateobj->setTime(0,0);
  		$ModifyEntrydateobj = new DateTime($OrderEntryTime);
  		$ModifyEntrydateobj->modify("+1 days");
  		$ModifyEntrydateobj->setTime(0,0);

  		$ModifyEntrydateobj->add(new DateInterval("PT{$PriorityTime}H"));
  		$timeused = get_diff_hours($OrderEntryTime);
  		$interval = $Entrydateobj->diff($ModifyEntrydateobj);
  		if($interval->d == 0){

  			$ModifyEntrydateobj->modify("-1 second");
  			$TATAdded_Date = $ModifyEntrydateobj->format('Y-m-d H:i:s');

  			$calculator = new calculate_businessdays(new DateTime($TATAdded_Date),$holidays,$weekend);

  			$DateTime = $calculator->getDate();

  			if($calculator->isBusinessDay($DateTime)){
  				// echo '<pre> Datetime: ';print_r($DateTime);

  			}else{
  				$calculator->getBusinessDay();
  				$DateTime = $calculator->getDate();
  				$DateTime->setTime(0, 0);
  				$DateTime->add(new DateInterval("PT{$PriorityTime}H"));
  				$DateTime->modify("-1 second");
  			}


  		}else{

  			$Entrydate = new DateTime($OrderEntryTime);
  			$Entrydate->modify("-1 second");
  			$calculator = new calculate_businessdays($Entrydate,$holidays,$weekend);
  			$excludecount = $calculator->getBusinessDay();
  			$Adddate = $calculator->getDate();
  			$hourTime = $Adddate->format('H');
  			$minuteTime = $Adddate->format('i');
  			$secondsTime = $Adddate->format('s'); 
  			$calculator->addBusinessDay($interval->d);
  			$DateTime = $calculator->getDate();
  			$DateTime->setTime(0, 0);
  			$DateTime->add( new DateInterval( 'PT' . ( (integer) $hourTime ) . 'H'. ( (integer) $minuteTime ) . 'M' . ( (integer) $secondsTime ) . 'S' ) );

  		}
  		$duedate = $DateTime->format('Y-m-d H:i:s');
  		return $duedate;
  	}
  }

 //check function is defined
  if(!function_exists('get_nextturntat_duedate')) {

  	function get_nextturntat_duedate($SLA_list,$OrderEntryTime){
    //echo "<br>-------SkipOrderOpenDate--------";
  		$PriorityTime = $SLA_list->PriorityTime;
  		$holidays = $weekend = [];
  		$ExcludeSuspensionTime = explode(',', $SLA_list->ExcludeSuspensionTime);
  		if(in_array('Organization Holidays', $ExcludeSuspensionTime)) {
  			$holidays = getholidays();
  		}

  		if(in_array('Weekend', $ExcludeSuspensionTime)) {
  			$weekend = [calculate_businessdays::SUNDAY,calculate_businessdays::SATURDAY];
  		}

  		$Entrydateobj = new DateTime($OrderEntryTime);
  		$Entrydateobj->modify("+1 days");
  		$ModifyEntrydateobj = new DateTime($OrderEntryTime);
  		$ModifyEntrydateobj->modify("+1 days");
  		$ModifyEntrydateobj->add(new DateInterval("PT{$PriorityTime}H"));
  		$timeused = get_diff_hours($OrderEntryTime);
  		$interval = $Entrydateobj->diff($ModifyEntrydateobj);


  		if($interval->d == 0){
  			
  			$Entry_dateobj = new DateTime($OrderEntryTime);
  			$Entry_dateobj->setTime(0, 0);
  			$Entry_dateobj->modify("+1 days");
  			$Entry_dateobj->add(new DateInterval("PT{$PriorityTime}H"));;
  			$Entry_dateobj->modify("-1 second");

  			$TATAdded_Date = $Entry_dateobj->format('Y-m-d H:i:s');

  			$calculator = new calculate_businessdays(new DateTime($TATAdded_Date),$holidays,$weekend);
  			$DateTime = $calculator->getDate();


  			if($calculator->isBusinessDay($DateTime)){


  			}else{
  				$calculator->getBusinessDay();
  				$DateTime = $calculator->getDate();
  				$DateTime->setTime(0, 0);
  				$DateTime->add(new DateInterval("PT{$PriorityTime}H"));
  				$DateTime->modify("-1 second");
  			}

  		}else{
  			$Entrydate = new DateTime($OrderEntryTime);
  			$Entrydate->setTime(0, 0);
  			$Entrydate->modify("+1 days");
  			$Entrydate->modify("-1 second");
  			$calculator = new calculate_businessdays($Entrydate,$holidays,$weekend);
  			$excludecount = $calculator->getBusinessDay();
  			$Adddate = $calculator->getDate();

  			$hourTime = $Adddate->format('H');
  			$minuteTime = $Adddate->format('i');
  			$secondsTime = $Adddate->format('s');


  			$calculator->addBusinessDay($interval->d);


  			$DateTime = $calculator->getDate();
  			$DateTime->setTime(0, 0);
  			$DateTime->add( new DateInterval( 'PT' . ( (integer) $hourTime ) . 'H'. ( (integer) $minuteTime ) . 'M' . ( (integer) $secondsTime ) . 'S' ) );

  		}
  		$duedate = $DateTime->format('Y-m-d H:i:s');
  		return $duedate;
  	}
  }


  /**
    *@description Function to convert seconds to human readable format
    *
    * @param $ss (Seconds)
    * 
    * @throws no exception
    * @author Parthasarathy <parthasarathy.m@avanzegroup.com>
    * @return String 
    * @since 27.2.2020 
    * @version EditProcess 
    *
  */ 
    function seconds2human($ss) {
      $time = [];
      $s = $ss%60;
      $m = floor(($ss%3600)/60);
      $h = floor(($ss%86400)/3600);
      $d = floor(($ss%2592000)/86400);
      $M = floor($ss/2592000);

      if (!empty($M)) {
        $time[] = "$M months";
      }

      if (!empty($d)) {
        $time[] = "$d days";
      }
      if (!empty($h)) {
        $time[] = "$h hours";
      }
      if (!empty($m)) {
        $time[] = "$m minutes";
      }
      if (!empty($s)) {
        $time[] = "$s seconds";
      }

      return !empty($time) ? implode(", ", $time) : "";
    }

	function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}

 function convertColumnName($FieldName)
 { 
    $fldName = preg_replace('/[^A-Za-z0-9\-]/', '',$FieldName);  
    return str_replace(' ', '', $fldName);
 }

  function formatzipcode($zipcode)
  {
    if (strlen($zipcode) == 9) {
      return substr($zipcode, 0, 5) . "-" . substr($zipcode, 5, 4);
    }
    return $zipcode;
  } 


  /**
  *Function Calculate DueDate Based on working hours
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Monday 13 April 2020
  */

   //check function is defined
  if(!function_exists('get_businesshourtat_duedate')) {
  	function get_businesshourtat_duedate($SLA_list,$Order) {
  		$CI =& get_instance();
  		$CI->load->database();

  		$hoursToAdd = $SLA_list->PriorityTime;
  		$holidays = [];
  		$SkipWeekends = false;
  		$ExcludeSuspensionTime = explode(',', $SLA_list->ExcludeSuspensionTime);
  		$dayStart =  '00:00:00';
  		$dayEnd = '24:00:00';

  		if(in_array('Organization Holidays', $ExcludeSuspensionTime)) {
  			$holidayarray = $CI->common_model->get_holidays();
  			if(!empty($holidayarray)) {
  				$holidays = array_column($holidayarray, 'HolidayDate');
  			}
  		}
  		if(in_array('Weekend', $ExcludeSuspensionTime)) {
  			$SkipWeekends = true;
  		}


  		if($SLA_list->TurnTimeCalculation == 'Business Hours Turn Time') {

  			$dayStart = !empty($SLA_list->BusinessHourStartTime) ? $SLA_list->BusinessHourStartTime : '00:00:00';
  			$dayEnd = !empty($SLA_list->BusinessHourEndTime) ? $SLA_list->BusinessHourEndTime : '24:00:00';

  		} else if($SLA_list->TurnTimeCalculation == 'Effective Next Day Turn Time') {
  			//add 1 day skip order open date = 24 hours
  			$Order->FromActionDateTime = date('Y-m-d 00:00:00', strtotime('+1 day', strtotime($Order->FromActionDateTime)));
  		}


  		if(in_array('ClientDelay', $ExcludeSuspensionTime)) {

  			$CI->db->select("OrderUID,CustomerDelayStartTime,CustomerDelayStopTime");
  			$CI->db->from("mcustomerdelay");
  			$CI->db->where("OrderUID",$Order->OrderUID);
  			$CI->db->where("(CustomerDelayStartTime >= '".$Order->FromActionDateTime."' OR  CustomerDelayStopTime <= '".$Order->ToActionDateTime."')",NULL,FALSE);
  			$query = $CI->db->get();
  			$ClientDelays = $query->result();

  			foreach ($ClientDelays as $ClientDelay) {
  				if($Order->FromActionDateTime <= $ClientDelay->CustomerDelayStartTime || $Order->ToActionDateTime >= $ClientDelay->CustomerDelayStopTime) {
  					$hoursToAdd = $hoursToAdd+(int)get_slaworking_hours($ClientDelay->CustomerDelayStartTime,$ClientDelay->CustomerDelayStopTime,$dayStart,$dayEnd,$holidays);
  				}
  			}
  		}

  		if(in_array('OnHold', $ExcludeSuspensionTime)) {
  			$CI->db->select("OrderUID,OnholdDateTime,ReleaseDateTime");
  			$CI->db->from("torderonholdhistory");
  			$CI->db->where("OrderUID",$Order->OrderUID);
  			$CI->db->where("(torderonholdhistory.OnholdDateTime >= '".$Order->FromActionDateTime."' OR  torderonholdhistory.ReleaseDateTime <= '".$Order->ToActionDateTime."')",NULL,FALSE);
  			$query = $CI->db->get();
  			$OnHolds = $query->result();
  			foreach ($OnHolds as $OnHold) {
  				if($Order->FromActionDateTime >= $OnHold->OnholdDateTime || $Order->ToActionDateTime <= $OnHold->ReleaseDateTime) {
  					$hoursToAdd = $hoursToAdd+(int)get_slaworking_hours($OnHold->OnholdDateTime,$OnHold->ReleaseDateTime,$dayStart,$dayEnd,$holidays);
  				}
  			}
  		}	

  		$DateTimeobj = calculate_WorkingHours($Order->FromActionDateTime, (int)$hoursToAdd, $dayStart, $dayEnd,$holidays,$SkipWeekends);
  		return $DateTimeobj->format('Y-m-d H:i:s');
  	}
  }


  /**
  *Function Calculate DueDate Based on working hours
  *@author Praveen Kumar <praveen.kumar@avanzegroup.com>
  *@since Monday 13 April 2020
  */

  //check function is defined
  if(!function_exists('calculate_WorkingHours')) {
  	function calculate_WorkingHours($givenDate, $addtime, $dayStart, $dayEnd,$holidays, $SkipWeekends) {
    	//Break the working day start and end times into hours, minuets
  		$dayStart = explode(':', $dayStart);
  		$dayEnd = explode(':', $dayEnd);

  		//fetch customer delay



    	//Create required datetime objects and hours interval
  		$datetime = new DateTime($givenDate);
			$datetime->modify("-1 second");
  		$startofday = clone $datetime;
  		$startofday->setTime($dayStart[0], $dayStart[1]); //set start of working day time
  		$endofday = clone $datetime;
  		$endofday->setTime($dayEnd[0], $dayEnd[1]); //set end of working day time


  		$interval = 'PT'.$addtime.'H';

 			//if initial date is before the start of working day
  		if($datetime < $startofday) {
  			//reset to start of working hours
  			$datetime = $startofday;
  		}  		

  		//Add hours onto initial given date
  		$datetime->add(new DateInterval($interval));


 			//if initial date + hours is after the end of working day
  		if($datetime > $endofday)
  		{	
        //get the difference between the initial date + interval and the end of working day in seconds
  			$seconds = $datetime->getTimestamp()- $endofday->getTimestamp();
        //Loop to next day
  			while(true)
  			{

  				if(in_array($endofday->format('Y-m-d'), $holidays))
  				{
  					$endofday->add(new DateInterval('PT24H'));//Loop to next day by adding 24hrs
  					continue;
  				}

  				if(in_array($endofday->format('l'), array('Sunday','Saturday')) && $SkipWeekends)
  				{
  					$endofday->add(new DateInterval('PT24H'));//Loop to next day by adding 24hrs
  					continue;
  				}

  				$endofday->add(new DateInterval('PT24H'));//Loop to next day by adding 24hrs
  				$nextDay = $endofday->setTime($dayStart[0], $dayStart[1]);//Set day to working day start time
            //If the next day is on a weekend and the week day only param is true continue to add days
  				if(in_array($nextDay->format('Y-m-d'), $holidays))
  				{
  					continue;
  				}
  				else if(in_array($nextDay->format('l'), array('Sunday','Saturday')) && $SkipWeekends)
  				{
  					continue;
  				}
  				else //If not a weekend
  				{
  					$tmpDate = clone $nextDay;
  					$tmpDate->setTime($dayEnd[0], $dayEnd[1]);//clone the next day and set time to working day end time
  					$nextDay->add(new DateInterval('PT'.$seconds.'S')); //add the seconds onto the next day
            
            //if the next day time is later than the end of the working day continue loop
  					if($nextDay > $tmpDate)
  					{
  						$seconds = $nextDay->getTimestamp()-$tmpDate->getTimestamp();
  						$endofday = clone $tmpDate;
  						$endofday->setTime($dayStart[0], $dayStart[1]);

  					}
  					else //else return the new date.
  					{
  						return $endofday;


  					}
  				}
  			}
  		}

  		return $datetime;
  	}
  }

  /**
	* Function - working hours
	*
	* @param start date , enddate and holidays
	* 
	* @throws no exception 
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return hours
	* @since date Monday 11 May 2020
	*
	*/ 
  function get_slaworking_hours($startdate,$enddate,$dayStart,$dayEnd,$holidays){
  	//tim e config
  	//Break the working day start and end times into hours, minuets
  	$dayStart = explode(':', $dayStart);
  	$dayEnd = explode(':', $dayEnd);

  	//date objects
  	$ini = date_create($startdate);
  	$ini_wk = date_time_set(date_create($startdate),$dayStart[0],$dayStart[1]);
  	$end = date_create($enddate);
  	$end_wk = date_time_set(date_create($enddate),$dayEnd[0],$dayEnd[1]);
  	//days
  	$workdays_arr = get_slaworkdays($ini,$end,$holidays);
  	$workdays_count = count($workdays_arr);
  	$workday_seconds = (($dayEnd[0] * 60 + $dayEnd[1]) - ($dayStart[0] * 60 + $dayStart[1])) * 60;
  	//get time difference
  	$ini_seconds = 0;
  	$end_seconds = 0;
  	if(in_array($ini->format('Y-m-d'),$workdays_arr)) $ini_seconds = $ini->format('U') - $ini_wk->format('U');
  	if(in_array($end->format('Y-m-d'),$workdays_arr)) $end_seconds = $end_wk->format('U') - $end->format('U');
  	$seconds_dif = $ini_seconds > 0 ? $ini_seconds : 0;
  	if($end_seconds > 0) $seconds_dif += $end_seconds;
  	//final calculations
  	$working_seconds = ($workdays_count * $workday_seconds) - $seconds_dif;
  	$hours = round($working_seconds / 3600);
  	return (int)$hours; //return hrs
  }

 	/**
	* Function - working days
	*
	* @param ini time as starttime end as end time and holidays
	* 
	* @throws no exception 
	* @author Praveen Kumar <praveen.kumar@avanzegroup.com>
	* @return days
	* @since date Monday 11 May 2020
	*
	*/ 
  function get_slaworkdays($ini,$end,$holidays){
  	//config
  	$skipdays = [6,0]; //saturday:6; sunday:0
  	//vars
  	$current = clone $ini;
  	$current_disp = $current->format('Y-m-d');
  	$end_disp = $end->format('Y-m-d');
  	$days_arr = [];
  	//days range
  	while($current_disp <= $end_disp){
  		if(!in_array($current->format('w'),$skipdays) && !in_array($current_disp,$holidays)){
  			$days_arr[] = $current_disp;
  		}
  		$current->add(new DateInterval('P1D')); //adds one day
  		$current_disp = $current->format('Y-m-d');
  	}
  	return $days_arr;
  }

  /**
   *@description Function to Complete WorkflowQueues
   *
   * @param $OrderUID, $CustomerUID, $SubProductUID
   * 
   * @throws no exception
   * @author Periyasamy <Periyasamy.s@avanzegroup.com>
   * @return Completed Queue 
   * @since 16/6/2020 
   * @version Closing Dynamic Queues 
   *
  */ 
  function completePrevQueue($QueueUID,$ScheduleUID,$CustomerUID,$SubProductUID,$OrderUID,$cmp_queue,$is_reverse=false)
  {
    $CI =& get_instance();
    $CI->load->database();

    $PreQueue = $CI->common_model->getCompleteQueue($QueueUID,$CustomerUID,$SubProductUID);    
    $CompleteQueues = array_filter(explode(',',$PreQueue->CompleteQueueUIDs));
    $TriggerQueue = $PreQueue->TriggerQueueUIDs;  

    if(empty($ScheduleUID)) {
     $ScheduleUID = 0;
    }

    if(!empty($CompleteQueues))
    {
      if($is_reverse){
        $CI->db->where(array('OrderUID'=>$OrderUID));
        $CI->db->where_in('QueueUID',$CompleteQueues,FALSE);
        $CI->db->delete('tOrderQueues');
        return 1;   
      } else {
        $CI->db->where('OrderUID',$OrderUID);
        $CI->db->where('( ScheduleUID = '.$ScheduleUID.' OR ScheduleUID = 0)',NULL,FALSE);
        $CI->db->where_in('QueueUID',$CompleteQueues,FALSE);
        if($CI->db->update('tOrderQueues',$cmp_queue)) 
        {
          // logQueueAudit($QueueUID,'Completed',$OrderUID);
          if(in_array(9, $CompleteQueues)) {
            changeOrderSignStatus($QueueUID,$OrderUID);
          }

          if(!empty($TriggerQueue) && !($is_reverse))
          {
            insertTriggerQueues($ScheduleUID,$CustomerUID,$SubProductUID,$OrderUID,$TriggerQueue);
          }
          return 1;
        } else {
          return 0;
        }
      }
    } else {
      return 0;
    }
  }

  /**
   * @description Function to Awaiting Sign Complete Order Signing
   * @param $QueueUID, $OrderUID
   * @throws no exception
   * @author Periyasamy <Periyasamy.s@avanzegroup.com>
   * @return Completed Order Signing 
   * @since 19/6/2020 
   * @version Queues 
   *
  */
  function changeOrderSignStatus($QueueUID, $OrderUID)
  {
    $CI =& get_instance();
    $CI->load->database();
    
    $CI->db->trans_start();

    // Update Schedule Status
    $CI->db->where('OrderUID', $OrderUID);
    $CI->db->where('ScheduleStatus', 'Pending');
    $CI->db->update('tOrderSchedule',['ScheduleStatus'=>'Complete','ModifiedDateTime'=>date('Y-m-d H:i:s'),'ModifiedByUserUID'=>$CI->loggedid]);

    // Update tOrderSign
    $CI->db->where('OrderUID', $OrderUID);
    $CI->db->where_in('SigningStatus',['InProgress','Reschedule']);
    $CI->db->update('tOrderSign',['SigningStatus'=>'Sign','ModifiedDateTime'=>date('Y-m-d H:i:s'),'ModifiedUserUID'=>$CI->loggedid]);

    // Update tOrderAbstractor
    $CI->db->query('UPDATE torderabstractor SET AbstractorStatusCode = "COMPLETED", OrderStatus = 5 WHERE OrderUID IN (SELECT GROUP_CONCAT(DISTINCT AbstractorOrderUID) FROM tOrderSchedule WHERE ScheduleStatus = "Pending" AND OrderUID = '.$OrderUID.')');

    $CI->db->trans_complete();
    if($CI->db->trans_status() === FALSE) 
    {
      $CI->db->trans_rollback();
      return FALSE;
    } else {
      return TRUE;
    }
    
  }

  /**
   *@description Function to Trigger WorkflowQueues
   *
   * @param $OrderUID, $CustomerUID, $SubProductUID
   * 
   * @throws no exception
   * @author Periyasamy <Periyasamy.s@avanzegroup.com>
   * @return Completed Queue 
   * @since 16/6/2020 
   * @version Closing Dynamic Queues 
   *
  */ 
  function insertTriggerQueues($ScheduleUID,$CustomerUID,$SubProductUID,$OrderUID,$TriggerQueueUIDs, $OrderQueueUID=0)
  {
    $CI =& get_instance();
    $CI->load->database();
    $CI->load->model('common_model');
    $CI->load->config('keywords');

    if(!empty($TriggerQueueUIDs)) // Trigger Insert New Queue
    { 
      $TriggerQueues = explode(',', $TriggerQueueUIDs);  
      foreach ($TriggerQueues as $key => $QueueUID) 
      {
        /* **** Check for Duplicate key check **** */
        $isqueueavailable = $CI->common_model->get_row('tOrderQueues', ['OrderUID'=>$OrderUID, 'QueueUID'=>$QueueUID]);

        $RaisedDateTime_value = NULL;
        if ($QueueUID == $CI->config->item('Queues')['Awaiting Vendor']) 
        {
          $CI->db->where('OrderUID', $OrderUID);
          $CI->db->where('QueueUID', $CI->config->item('Queues')['Ready to Schedule/Reschedule']);
          $tOrderQueues = $CI->db->get('tOrderQueues')->row();
  
          if (!empty($tOrderQueues->RaisedDateTime) && $tOrderQueues->RaisedDateTime != "0000-00-00 00:00:00")
          {          
            $RaisedDateTime_value = date("Y-m-d H:i:s", strtotime($tOrderQueues->RaisedDateTime));
          }
          else
          {
           $RaisedDateTime_value = date("Y-m-d H:i:s"); 
          }

        }

        $RaisedDateTimeUpdate = date("Y-m-d H:i:s"); 

        if (empty($isqueueavailable)) 
        {

        $CI->db->query("INSERT INTO tOrderQueues (OrderUID,QueueUID,RaisedRemarks,RaisedReasonUID,RaisedByUserUID,ScheduleUID, RaisedDateTime) VALUES ('".$OrderUID."','".$QueueUID."','','','".$CI->loggedid."','".$ScheduleUID."', '" . $RaisedDateTime_value . "') ON DUPLICATE KEY UPDATE QueueStatus = 'Pending',CompletedDateTime = '', CompletedByUserUID = '', CompletedRemarks = '',RaisedDateTime= '".$RaisedDateTimeUpdate."'");
        }
        else
        {
          if (!empty($OrderQueueUID)) 
          {          
            $data = ['QueueStatus'=>'Pending', 'RaisedByUserUID'=>$CI->loggedid, 'RaisedDateTime'=>$RaisedDateTimeUpdate];
            $CI->db->where('QueueUID', $QueueUID);
            $CI->db->where('OrderUID', $OrderUID);
            $CI->db->update('tOrderQueues', $data);
          }
        }
        // logQueueAudit($QueueUID,'Pending',$OrderUID);
      }
    }
  }

  /**
   *@description Function to Audit Log WorkflowQueues
   *
   * @param $QueueUID, $QueueStatus, $OrderUID, $PrevQueue
   * 
   * @throws no exception
   * @author Periyasamy <Periyasamy.s@avanzegroup.com>
   * @return Completed Queue 
   * @since 22/6/2020  
   *
  */
  function logQueueAudit($QueueUID,$QueueStatus,$OrderUID,$PrevQueueUID='')
  {
    $CI =& get_instance();
    $CI->load->database();
     // echo '<pre>';print_r(func_get_args());exit; 
    $CurrentQueue = $CI->common_model->getTableColumnValue('mQueues','QueueUID',$QueueUID,'QueueName'); 
    if(!empty($PrevQueueUID))
    {
      $PreQueue = $CI->common_model->getTableColumnValue('mQueues','QueueUID',$PrevQueueUID,'QueueName'); 
    }

    if($QueueStatus == 'Pending')
    {
      if($PrevQueueUID != $QueueUID && !empty($PrevQueueUID)) {
        $content = 'Order Queue has been Moved from <b>'.$PreQueue.'</b> to <b>'.$CurrentQueue.'</b>';
      } else if(!empty($PreQueue) && $PrevQueueUID == $QueueUID) {
        $content = 'Order Queue <b>'.$CurrentQueue.'</b> Status changed from <b>Completed</b> to <b>Pending</b>';
      } else {
        $content = 'Order Queue has been Moved to <b>'.$CurrentQueue.'</b>';
      }
    } else if($QueueStatus == 'Completed') {
      $content = 'Order Queue <b>'.$CurrentQueue.'</b> has been Completed';
    } else if($QueueStatus == 'ReverseQueue') {
      $content = 'Order Queue <b>'.$CurrentQueue.'</b> has been Reversed';
    }

    $AuditData = array(
     'UserUID' => $CI->loggedid,
     'ModuleName' => 'OrderQueue',
     'OrderUID' => $OrderUID,
     'Feature' => $OrderUID,
     'Content' => $content,
     'DateTime' => date('Y-m-d H:i:s'));
    $CI->common_model->InsertAuditTrail($AuditData);

  }
/**
* @description Insert Audit
* @param ModuleName, Feature = UniqueUID, Message, OrderUID
* @throws no exception
* @author Ubakarasamy <ubakarasamy.sm@avanzegroup.com>
* @return Boolean 
* @since  15 Jul 2020
* @version  Issue Management Auditing
*/
  	function AuditHelper($Module,$FeatureUID,$Message,$OrderUID = NULL){
		$CI =& get_instance();
		$CI->load->database();
		if(!empty($Module) && (!empty($FeatureUID) || !empty($OrderUID))){
			$InsertData = array(
				'UserUID' => $CI->loggedid,
				'ModuleName' => $Module,
				'OrderUID' => $OrderUID,
				'Feature' => $FeatureUID,
				'Content' => htmlentities($Message),
				'DateTime' => date('Y-m-d H:i:s'));
			return $CI->common_model->InsertAuditTrail($InsertData);
		}else{
			return false;
		}
		
	}


   /**
  * @purpose : To insert into workflow queue 
  *
  * @param QueueUID as string
  * @param OrderUID as string
  * 
  * @throws no exception
  * @author D.Samuel Prabhu <samuel.prabhu@avanzegroup.com>
  * @return true/false 
  * @since 27 July 2020 
  *
  */ 

  function insertApiWorkflowQueue($QueueUID,$OrderUID, $Comment)
  {
    $CI =& get_instance();
    $CI->load->database();
  
    $UserUID =  $CI->db->select('*')->from('musers')->where('LoginID', 'isgn')->get()->row()->UserUID;
    $RaisedDateTime = date('Y-m-d H:i:s',strtotime('now'));
    $mQueues = $CI->db->select("*")->from('mQueues')->where('mQueues.QueueUID', $QueueUID)->get()->row();
  
    /* Transaction begins */
    $CI->db->trans_start();
    
    $CI->db->query("INSERT INTO tOrderQueues (OrderUID,QueueUID,RaisedByUserUID,EventComments, RaisedDateTime) VALUES ('".$OrderUID."','".$QueueUID."','".$UserUID."','".$Comment."','".$RaisedDateTime."') ON DUPLICATE KEY UPDATE QueueStatus = 'Pending',CompletedDateTime = '', CompletedByUserUID = '', CompletedRemarks = '', RaisedDateTime= '".$RaisedDateTime."', EventComments= '".$Comment."'");

    /* Transaction complete */
    $CI->db->trans_complete();

    /* If fails roll back */
    if($CI->db->trans_status() === FALSE) 
    {
      $CI->db->trans_rollback();
      return FALSE;
    } else {
      /* On success insert audit trail */
      $Comment = 'Order Queue has been moved to '.$mQueues->QueueName;
      $auditMsg = $Comment.'<br>';

      $AuditData = array(
        'UserUID' => $UserUID,
        'ModuleName' => 'OrderQueue',
        'OrderUID' => $OrderUID,
        'Feature' => $OrderUID,
        'Content' => $auditMsg,
        'DateTime' => date('Y-m-d H:i:s')
      );

      $CI->common_model->Audittrail_insert($AuditData);
        
      return TRUE;
    }
  }

  /**
  * @purpose : To insert into issue policy queue 
  *
  * @param OrderUID as string
  * 
  * @throws no exception
  * @author Yagavi G <yagavi.g@avanzegroup.com>
  * @since Aug 7th 2020 
  *
  */ 

  function InsertIssuePolicyQueue($OrderUID, $FormType, $Comment)
  {
    $CI =& get_instance();
    $CI->load->database();
    $UserUID = $CI->session->userdata('UserUID');
    $DateTime = date('Y-m-d H:i:s',strtotime('now'));
    $tOrderQueues = $CI->common_model->GetSigningCompletedDateTimeForClosingOrder($OrderUID);
    $SigningCompletedDate = $tOrderQueues->RaisedDateTime;
    $TitleOrder = $CI->common_model->GetTitleOrdersByOrderUID($OrderUID);

    if($TitleOrder){
      $TitleOrderUID = $TitleOrder->OrderUID;

      $CI->db->select("*")->from('tIssuePolicyOrders')->where(array("tIssuePolicyOrders.OrderUID"=>$TitleOrderUID));     
      $query = $CI->db->get();
      $issuepolicy = $query->row();
    
      /* Transaction begins */
      $CI->db->trans_start();

      $tIssuePolicyOrders = array(
        'OrderUID' => $TitleOrderUID,
        'Remarks' => $Comment,
        'FormType' => $FormType,
        'CreatedBy' => $UserUID,
        'CreatedOn' => $DateTime,
        'SigningCompletedDateTime' => $SigningCompletedDate,
      );

      if($FormType === 'SHORT')
      {
        $tIssuePolicyOrders['DisbursmentDate'] = date('Y-m-d H:i:s',strtotime('now'));
      }
      else if($FormType === 'LONG')
      {
        $tIssuePolicyOrders['RecordedDate'] = date('Y-m-d H:i:s',strtotime('now'));
      }

      if(empty($issuepolicy)){
        $CI->db->insert('tIssuePolicyOrders',$tIssuePolicyOrders);
      } else {
        $CI->db->where('OrderUID', $OrderUID);
        $CI->db->update('tOrderSign',$tIssuePolicyOrders);
      }

      /* Transaction complete */
      $CI->db->trans_complete();

      /* If fails roll back */
      if($CI->db->trans_status() === FALSE) 
      {
        $CI->db->trans_rollback();
        return FALSE;
      }
      else 
      {
        return TRUE;
      }
    }

  }

?>
