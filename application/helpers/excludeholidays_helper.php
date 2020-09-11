
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 
   

function get_working_hours($from,$to)
{
    // timestamps
    $from_timestamp = strtotime($from);
    $to_timestamp = strtotime($to);

    // work day seconds
    $workday_start_hour = 0;
    $workday_end_hour = 24;
    $workday_seconds = ($workday_end_hour - $workday_start_hour)*3600;

    // work days beetwen dates, minus 1 day
    $from_date = date('Y-m-d',$from_timestamp);
    $to_date = date('Y-m-d',$to_timestamp);


    $workdays_number = count(get_workdays($from_date,$to_date))-1;
    $workdays_number = $workdays_number<0 ? 0 : $workdays_number;

    // start and end time
    $start_time_in_seconds = date("H",$from_timestamp)*3600+date("i",$from_timestamp)*60;
    $end_time_in_seconds = date("H",$to_timestamp)*3600+date("i",$to_timestamp)*60;

    // final calculations
    $working_hours = ($workdays_number * $workday_seconds + $end_time_in_seconds - $start_time_in_seconds) / 86400 * 24;

    return $working_hours;
}


function get_workdays($from,$to) 
{
    // arrays
    $days_array = array();
    $skipdays = array("Saturday", "Sunday");
    $skipdates = get_USholidays();

    // other variables
    $i = 0;
    $current = $from;

    if($current == $to) // same dates
    {
        $timestamp = strtotime($from);
        if (!in_array(date("l", $timestamp), $skipdays)&&!in_array(date("Y-m-d", $timestamp), $skipdates)) {
            $days_array[] = date("Y-m-d",$timestamp);
        }
    }
    elseif($current < $to) // different dates
    {
        while ($current < $to) {
            $timestamp = strtotime($from." +".$i." day");
            if (!in_array(date("l", $timestamp), $skipdays)&&!in_array(date("Y-m-d", $timestamp), $skipdates)) {
                $days_array[] = date("Y-m-d",$timestamp);
            }
            $current = date("Y-m-d",$timestamp);
            $i++;
        }
    }

    return $days_array;
}


function get_USholidays() 
{
    // arrays
    $CI = get_instance();
	 
	$holiday = $CI->common_model->GetHolidays(); 
	foreach($holiday as $holiDate)
	{
	    $holidays[] = $holiDate->HolidayDate;
	}  
    // You have to put there your source of holidays and make them as array...
    // For example, database in Codeigniter:
    // $days_array = $this->my_model->get_USholidays_array();

    return $holidays;
}

	function time_ago($hours) 
	{  
	  $time_difference = HourstoSeconds($hours); 
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
	      return "$minutes min ";  
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
	  return "$days day";  
	}  
	else  
	{
	  if($days<1){
	   return "$hours hours"; 
	 } 
	 else{
	  return "$days days ";  
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
	     	return '-';
	    
	  }   
	 }  
	}  

	function HourstoSeconds($time) 
	{
	  return $time * 60 * 60;
	}

?>