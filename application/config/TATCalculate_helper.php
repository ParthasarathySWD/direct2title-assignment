<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 
   

function GetTotalTATOrders()
{
  $CI = get_instance();

  $Orderwithpriority = GetOrdersWithPriorityDetails(); 
  $holiday = $CI->common_model->GetHolidays(); 
  foreach($holiday as $holiDate)
  {
    $holidays[] = $holiDate->HolidayDate;
  }  
  foreach ($Orderwithpriority as $orders) 
  {
    $DueDatetime = $orders->OrderDueDatetime;  
    $From = $orders->OrderEntryDatetime;
    $End = date('Y-m-d H:i:s');

    $cust_delaytime = Get_CustomerDelayTime_ByOrder($orders->OrderUID); 
    if($cust_delaytime!=0)
    {
      $cdelay_time = array(); // reinstall array every order
      foreach ($cust_delaytime as $value) 
      {
        $start = $value->CustomerDelayStartTime;
        $delay_end = $value->CustomerDelayStopTime;
        if($value->CustomerDelayStopTime=='0000-00-00 00:00:00')
          {
            $delay_end = $End;
          } 
          $cdelay_time[] = working_hours($start,$delay_end,$holidays);   
        } 

        $total_cust_delaytime =sum_total_custdelaytime($cdelay_time);   

        $delay_time = explode(':',$total_cust_delaytime);  
        $TAT_with_delay = date('Y-m-d H:i:s',
          strtotime('+'.$delay_time[0].' hours +'.$delay_time[1].' minute +'.$delay_time[2].' second',
            strtotime($DueDatetime))
        );  

        $TAT_DueDatetime = due_exclude_days($DueDatetime,$TAT_with_delay,$holidays);
      
    } else {
      $TAT_DueDatetime = $DueDatetime;
    }

    $order = GetTotalTATOrderUID($TAT_DueDatetime,$orders->OrderUID,$orders->PriorityUID);
    $TAT[]= $order->TATUID; 
  }

  $TATOrder_unique = array_filter($TAT);
  $TATOrdersUID = array_values(array_unique($TATOrder_unique));  
  return $TATOrdersUID;
}


function due_exclude_days($start,$TAT_with_delay,$holidays)
{ 
  $time = date('H:i:s',strtotime($TAT_with_delay)); 
  $excluded = 0;

  if(!in_array(date('l',strtotime($TAT_with_delay)), array('Sunday','Saturday')) && !in_array($TAT_with_delay,$holidays))
  {
    $TAT_with_delay = $TAT_with_delay;
  } else {
    $TAT_with_delay = add_Day_excludeweekend($TAT_with_delay,$holidays,1).' '.$time;
    // $excluded ++;
  }
  
  $begin = new DateTime($start);
  $end = new DateTime($TAT_with_delay);
  $interval = new DateInterval('P1D');
  $begin->setTime(0, 0);
  $end->setTime(0, 0)->add($interval);
  $daterange = new DatePeriod($begin, $interval, $end); 
  
  
  foreach($daterange as $date)
  {
    $Curr_day = $date->format('Y-m-d');
    if(!in_array($date->format('l'), array('Sunday','Saturday')) && !in_array($Curr_day,$holidays))
    {
      $exclude_date = $Curr_day;
    } else {
      $excluded ++;
    }
  }
  
  if($excluded>0)
  {
    $exclude_date = add_Day_excludeweekend($TAT_with_delay,$holidays,$excluded); 
  }
  return $exclude_date.' '.date('H:i:s', strtotime('tomorrow') - 1);
} 

function add_Day_excludeweekend($date,$holidays,$excluded)
{ 
  $count1WD = 0;
  $temp = strtotime($date); 
  while($count1WD<$excluded)
  {
    $next1WD = strtotime('+1 weekday', $temp);
    $next1WDDate = date('Y-m-d', $next1WD);
    // echo $count1WD.') '.$next1WDDate.'<br>';
    if(!in_array($next1WDDate, $holidays))
    {
      $count1WD++;
    }
    $temp = $next1WD;
  }
  $next5WD = date("Y-m-d", $temp);
  return $next5WD;
}

function sum_total_custdelaytime($times) 
{
  $times = $times;
  $seconds = 0;
  foreach ($times as $time)
  {
    list($hour,$minute,$second) = explode(':', $time);
    $seconds += $hour*3600;
    $seconds += $minute*60;
    $seconds += $second;
  }
  $hours = floor($seconds/3600);
  $seconds -= $hours*3600;
  $minutes  = floor($seconds/60);
  $seconds -= $minutes*60;
  return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds); // Thanks to Patrick
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


function GetOrdersWithPriorityDetails()
{

  $CI =& get_instance();
  $CI->load->database();

  $CI->db->select('OrderUID,TAT,PriorityTime,torders.PriorityUID,torders.SubProductUID,torders.CustomerUID,SkipOrderOpenDate,OrderDueDatetime,OrderEntryDatetime');
  $CI->db->from('torders');
  $CI->db->where('torders.StatusUID <>',100);
  $CI->db->join('morderpriority','morderpriority.PriorityUID = torders.PriorityUID','LEFT');
  $CI->db->join('mcustomerpriorities','torders.SubProductUID = mcustomerpriorities.SubProductUID AND mcustomerpriorities.PriorityUID = torders.PriorityUID AND torders.CustomerUID = mcustomerpriorities.CustomerUID','LEFT');
  $CI->db->group_by('torders.OrderUID');
  return $CI->db->get()->result();
}

function Get_CustomerDelayTime_ByOrder($OrderUID)
{
  $CI =& get_instance();
  $CI->load->database();

  $query = $CI->db->query("SELECT OrderUID,CustomerDelayStartTime, CustomerDelayStopTime FROM mcustomerdelay WHERE OrderUID ='$OrderUID'");
  if($query->num_rows()>0)
  {
    return $query->result();
  } else {
    return 0;
  }
}

function GetTotalTATOrderUID($TAT,$OrderUID,$PriorityUID)
{

  $CI =& get_instance();
  $CI->load->database();

  $sql = "select (CASE WHEN (('$TAT') < (NOW()) AND StatusUID<>100 AND StatusUID<>110) THEN OrderUID END) AS TATUID from torders WHERE PriorityUID = $PriorityUID AND OrderUID = $OrderUID"; 
  return $CI->db->query($sql)->row();
}