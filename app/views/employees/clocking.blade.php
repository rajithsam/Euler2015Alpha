@extends('layouts.default')

@section('content')

<?php  
$employeeId = Session::get('userEmployeeId');
$userId = Session::get('userId');

$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

if( !empty($userGroups) ) {

  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

}

$currentUser = Sentry::getUser();

//$employeeId = Session::get('userEmployeeId');
//$employeeInfo[0]->id

$company = Company::find($employeeInfo[0]->company_id);
$department = Department::find($employeeInfo[0]->department_id);
$jobTitle = JobTitle::find($employeeInfo[0]->position_id);

$manager = Employee::where('id', '=', $employeeInfo[0]->manager_id)->first();

if( !empty($manager) ) {

  $managerFullname = $manager->firstname.', '.$manager->lastname;

} else {

  $managerFullname = '';

}

//ADMINISTRATOR
if( !empty($groups) ) :
  if( strcmp(strtolower($groups->name), strtolower('Administrator')) === 0 ) :              

    $employees = DB::table('employees')
      ->where('id', '<>', $employeeInfo[0]->id)
      ->get();  
    
  elseif( strcmp(strtolower($groups->name), strtolower('Manager')) === 0 ) :                  

    $employees = DB::table('employees')
      ->where('id', '<>', $employeeInfo[0]->id)  
      //->where('manager_id', $employeeInfo[0]->id)
      //->orWhere('supervisor_id', $employeeInfo[0]->id)
      ->where('manager_id', $employeeInfo[0]->id)
      ->get();  

  elseif( strcmp(strtolower($groups->name), strtolower('Supervisor')) === 0 ) :                        

    $employees = DB::table('employees')
      ->where('id', '<>', $employeeInfo[0]->id)  
      //->where('manager_id', $employeeInfo[0]->id)
      //->orWhere('supervisor_id', $employeeInfo[0]->id)
      ->where('supervisor_id', $employeeInfo[0]->id)
      ->get();      

  endif;
endif;

$employeeArr[0] = '';
$employeeIdArr = array();
if( !empty($employees) ) {

    foreach($employees as $employee) {
      $employeeArr[$employee->id] = $employee->firstname. ', ' .$employee->lastname;
      $employeeIdArr[] = $employee->id; //use in checking absences

  }
  
}

$forgotYesterdayTimeOut = false;

//$employeeId = Session::get('userEmployeeId');
$data['employeeno'] = Session::get('userEmployeeId');
//$data['employeeno'] = Auth::user()->employee_id;

//Find the employee timesheet record for this day
$employeeClocking = '';
$employeeClocking = Timesheet::where('employee_id', '=', $data['employeeno'])->where('daydate', '=', date('Y-m-d'))->first();

//Todo: Simplify and refactoring the code
$otherDayDate = date( "Y-m-d", strtotime('-2 days') );
$getOtherDayDate = '';
$getOtherDayDate = DB::table('employee_timesheet')->where('employee_id', $data['employeeno'])->where('daydate', $otherDayDate)->get();										

$yesterDayDate = date( "Y-m-d", strtotime('yesterday') );

$getYesterDayDate = '';
//$getYesterDayDate = DB::table('employee_timesheet')->where('employee_id', $data['employeeno'])->where('daydate', $yesterDayDate)->get();													

$getYesterDayDate = Timesheet::where('employee_id', $data['employeeno'])->where('daydate', $yesterDayDate)->get();																	

$employeeNightDiffClocking = '';
$employeeNightDiffClocking = Timesheet::where('employee_id', '=', $data['employeeno'])->where('daydate', '=', $yesterDayDate)->first();

//	echo $getYesterDayDate[0]->clocking_status;


//DETECT: CUTOFF

$adminCutoff = new AdminCutoff;
$adminCutoffConfig = new Cutoffsetting;

$getAllCutoffSetting = $adminCutoffConfig->getAllCutoffSetting();

$cutoff['id'] = $adminCutoff->getCutoffbyYearMonth()->id;
$cutoff['year'] = $adminCutoff->getCutoffbyYearMonth()->year;
$cutoff['month'] = $adminCutoff->getCutoffbyYearMonth()->month;
$cutoff['type'] = $adminCutoff->getCutoffbyYearMonth()->cutoff_type;
$cutoff['dateFrom'][1] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_1;
$cutoff['dateTo'][1] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_1;    

$cutoff['dateFrom'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2;
$cutoff['dateTo'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2;        

$cutoffConfig['cutoff_type'] = $getAllCutoffSetting[0]->cutoff_type;
$cutoffConfig['cutoff_options'] = $getAllCutoffSetting[0]->cutoff_options;

$currentDate = date('Y-m-d');     
$currentMonth = date('M');
$currentCutoff = '';
$getSchedule = '';


if ( $cutoff['type'] === 'Monthly' ) {

  // Monthly

} elseif ( $cutoff['type'] === 'Semi Monthly' ) {

  if ( $cutoffConfig['cutoff_options'] === 1 ) { // 1st and 2nd cutoff same within the month

    // 1st and 2nd cutoff same within the month

    //return 'debug.io';
    //exit;

    $currentDate = date('Y-m-d');

    //1st CutOff - e.g 11-25
    $startTime1 = strtotime($cutoff['dateFrom'][1]); 
    $endTime1 = strtotime($cutoff['dateTo'][1]); 

    // Loop between timestamps, 1 day at a time 
    //$cutoffArr1 = array();
    $cutoffArr1[] = date('Y-m-d', $startTime1); 
    do {
       
       $startTime1 = strtotime('+1 day', $startTime1); 
       $cutoffArr1[] = date('Y-m-d', $startTime1);
       
    } while ($startTime1 < $endTime1);

    //return $cutoffArr1;     

    if( in_array($currentDate, $cutoffArr1) ) {

      $currentCutoff = 1;
    
    }


    //2nd CutOff - e.g 26-10
    $startTime2 = strtotime($cutoff['dateFrom'][2]);        
    $endTime2 = strtotime($cutoff['dateTo'][2]); 

    // Loop between timestamps, 1 day at a time 
    //$cutoffArr2 = array();
    $cutoffArr2[] = date('Y-m-d', $startTime2);         
    do {

       $startTime2 = strtotime('+1 day', $startTime2); 
       $cutoffArr2[] = date('Y-m-d', $startTime2);
       
    } while ($startTime2 < $endTime2);        

    //return dd($cutoffArr2);

    if( in_array($currentDate, $cutoffArr2) ) {

      $currentCutoff = 2;

    }         


  } elseif ( $cutoffConfig['cutoff_options'] === 2 ) { // 2nd cutoff overlap next month
  
    //http://stackoverflow.com/questions/10633879/current-date-minus-4-month
    //http://stackoverflow.com/questions/8912780/get-the-last-day-of-the-month3455634556
    //http://www.brightcherry.co.uk/scribbles/php-adding-and-subtracting-dates/
    //http://stevekostrey.com/php-dates-add-and-subtract-months-really/


    //$lastMonthDays = date('t', strtotime("-1 month"));          
    //$lastMonth = date('Y-m-d', strtotime("-". $lastMonthDays ."days"));

    //$currentDate = strtotime('-1 month' , strtotime($currentDate));
    //$currentDate = date('Y-m-d' , $$currentDate);

    //1st CutOff - e.g 11-25
    $startTime1 = strtotime($cutoff['dateFrom'][1]); 
    $endTime1 = strtotime($cutoff['dateTo'][1]); 

    // Loop between timestamps, 1 day at a time 
    //$cutoffArr1 = array();
    $cutoffArr1[] = date('Y-m-d', $startTime1); 
    do {
       
       $startTime1 = strtotime('+1 day', $startTime1); 
       $cutoffArr1[] = date('Y-m-d', $startTime1);
       
    } while ($startTime1 < $endTime1);

    //return $cutoffArr1;     

    if( in_array($currentDate, $cutoffArr1) ) {

      $currentCutoff = 1;
    
    }


    // /return $currentMonth.' - '.$cutoff['month'];          
    //$currentMonth
    //$cutoff['month'] = $adminCutoff->getCutoffbyYearMonth()->month;
    //$cutoff['type'] = $adminCutoff->getCutoffbyYearMonth()->cutoff_type;
    //$cutoff['dateFrom'][1] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_1;
    //$cutoff['dateTo'][1] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_1;    

    //$cutoff['dateFrom'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2;
    //$cutoff['dateTo'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2;        

    if ( strtotime($currentDate) !== strtotime($cutoff['dateFrom'][1]) ||
       strtotime($currentDate) >= strtotime($cutoff['dateFrom'][1]) ) {

      //$lastMonth = date('M', strtotime('-1 month'));
      //$cutoff['month'] = $lastMonth;

      /*$cutoff['dateFrom'][2] = strtotime('-1 month' , strtotime($cutoff['dateFrom'][2]));
      $cutoff['dateFrom'][2] = date('Y-m-d' , $cutoff['dateFrom'][2]);

      $cutoff['dateTo'][2] = strtotime('-1 month' , strtotime($cutoff['dateTo'][2]));
      $cutoff['dateTo'][2] = date('Y-m-d' , $cutoff['dateTo'][2]);*/

      $cutoff['dateFrom'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2;
      $cutoff['dateTo'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2;      

      //return 'true';


    }
    //return strtotime($currentDate). ' - ' .strtotime($cutoff['dateFrom'][1]);
    //die();

    //2nd CutOff - e.g 26-10
    $startTime2 = strtotime($cutoff['dateFrom'][2]);        
    $endTime2 = strtotime($cutoff['dateTo'][2]); 

    // Loop between timestamps, 1 day at a time 
    //$cutoffArr2 = array();
    $cutoffArr2[] = date('Y-m-d', $startTime2);         
    do {

       $startTime2 = strtotime('+1 day', $startTime2); 
       $cutoffArr2[] = date('Y-m-d', $startTime2);
       
    } while ($startTime2 < $endTime2);        

    //return dd($cutoffArr2);

    if( in_array($currentDate, $cutoffArr2) ) {

      $currentCutoff = 2;

    }

  }

}

if ( $currentCutoff === 1 ) { ////1st CutOff - e.g 11-25

  $cutOffDateFrom = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_1;
  $cutOffDateTo = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_1;

  Session::put('cutOffDateFrom', $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_1);
  Session::put('cutOffDateTo', $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_1);

} elseif ( $currentCutoff === 2 ) { ////1st CutOff - e.g 26-10


  //$cutoff['dateFrom'][2] = strtotime('-1 month' , strtotime($cutoff['dateFrom'][2]));
  //$cutoff['dateFrom'][2] = date('Y-m-d' , $cutoff['dateFrom'][2]);

  //$cutoff['dateTo'][2] = strtotime('-1 month' , strtotime($cutoff['dateTo'][2]));
  //$cutoff['dateTo'][2] = date('Y-m-d' , $cutoff['dateTo'][2]); 

  $cutOffDateFrom = $cutoff['dateFrom'][2];
  $cutOffDateTo = $cutoff['dateTo'][2];   

  Session::put('cutOffDateFrom', $cutOffDateFrom);
  Session::put('cutOffDateTo', $cutOffDateTo); 
  
  
  /*$cutOffDateFrom = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2;
  $cutOffDateTo = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2;   

  Session::put('cutOffDateFrom', $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2);
  Session::put('cutOffDateTo', $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2); */

} 
 
//$getTimesheetPerCutoff = Timesheet::where('employee_id', $employeeId)->whereBetween('daydate', array($cutOffDateFrom, $cutOffDateTo))->paginate(15);
$getTimesheetPerCutoff = Timesheet::where('employee_id', $employeeId)->whereBetween('daydate', array($cutOffDateFrom, $cutOffDateTo))->get();

//$getTimesheetPerCutoff = Timesheet::where('employee_id', $employeeId)->orderBy('daydate', 'desc')->paginate(15);


//ABSENCES
//CHECK LEAVE
//$checkLeavePerCutoff = leave::where('employee_id', $employeeId)->whereBetween('from_date', array($cutOffDateFrom, $cutOffDateTo))->get();
/*$checkLeavePerCutoff = leave::whereIn('employee_id', $employeeIdArr)
                        ->whereBetween('from_date', array($cutOffDateFrom, $cutOffDateTo))
                        ->whereBetween('To_date', array($cutOffDateFrom, $cutOffDateTo))
                        ->get();*/


?>

<div class="page-container">

<?php 
//dd($checkLeavePerCutoff);
//$yesterDayDate = date( "Y-m-d", strtotime('yesterday') );
//$getYesterDayDate = '';
//dd($employeeIdArr);

//ADMINISTRATOR

/*$cutoffArr = array();

if ( !empty($groups) ) :
  if ( strcmp(strtolower($groups->name), strtolower('Administrator')) === 0 ) :              

    if ( sizeof($employeeIdArr) >= 1 ) {

        //echo '//GROUP: ADMINISTRATOR';

        //echo 'No leave';
        //$checkTimesheetPerCutoff = Timesheet::whereIn('employee_id', $employeeIdArr)->whereBetween('daydate', array($cutOffDateFrom, $cutOffDateTo))->get();
        //$employeeTimesheetYesterDayDate = DB::table('employee_timesheet')->where('employee_id', $data['employeeno'])->where('daydate', $yesterDayDate)->get();    
        
        $employeeIdArr[] = $employeeId; //Add the Administrator ID;
        $checkTimesheetPerCutoff = Timesheet::whereIn('employee_id', $employeeIdArr)->where('daydate', $yesterDayDate)->get();

        if( !empty($checkTimesheetPerCutoff) ) {
          
          foreach ( $checkTimesheetPerCutoff as $timesheetPerCutoff) {

            $empId = (int) $timesheetPerCutoff->employee_id;
            $dayDate = $timesheetPerCutoff->daydate;
            $scheduleIn = $timesheetPerCutoff->schedule_in;
            $scheduleOut = $timesheetPerCutoff->schedule_out;
            $clockingStatus = $timesheetPerCutoff->clocking_status;        
            $timeIn1 = $timesheetPerCutoff->time_in_1;
            $timeOut1 = $timesheetPerCutoff->time_out_1;    

            //Todo Check Leave
            //$checkLeavePerCutoff = Leave::where('employee_id', $empId)->whereBetween('from_date', array($cutOffDateFrom, $cutOffDateTo))->get();
            $checkLeavePerCutoff = Leave::where('employee_id', $empId)->whereBetween('from_date', array($cutOffDateFrom, $cutOffDateTo))->first();            

            if ( !empty($checkLeavePerCutoff) ) {
              //$dayDate
              //dd($checkLeavePerCutoff->from_date);

              $startTime = strtotime($checkLeavePerCutoff->from_date);        
              $endTime = strtotime($checkLeavePerCutoff->to_date); 

              // Loop between timestamps, 1 day at a time 
              //$cutoffArr = array();
              $cutoffArr[] = date('Y-m-d', $startTime);         
              do {

                 $startTime = strtotime('+1 day', $startTime); 
                 $cutoffArr[] = date('Y-m-d', $startTime);
                 
              } while ($startTime < $endTime);

            }

            //CHECK IF HAS LEAVE
            if ( !in_array($dayDate, $cutoffArr) ) { //If has no leave found in the leave table            

              if ( ($clockingStatus === 'open')) {
                if ( empty($timeIn1) && empty($timeOut1) ) {
                  if ( !empty($scheduleIn) && !empty($scheduleOut) ||
                      $scheduleIn !== '00:00:00' && $scheduleOut !== '00:00:00' ) {

                    //echo 'absent';

                    $employeeSetting = DB::table('employee_setting')->where('employee_id', $empId)->first();

                    //echo $employeeSetting->hours_per_day;
                    //echo $timesheetPerCutoff->employee_id;

                    $summaryUpdate = Summary::where('employee_id', $empId)->where('daydate', $timesheetPerCutoff->daydate)->first();

                    $summaryUpdate->absent = $employeeSetting->hours_per_day;
                    $summaryUpdate->save();

                  }
                }
              }

            }

          } 

        }

    }

  endif;


  if( strcmp(strtolower($groups->name), strtolower('Employee')) === 0 ) :              

    if ( sizeof($employeeIdArr) === 1 ) {
    
        //echo '//GROUP: EMPLOYEE';

        //echo 'No leave';
        //$checkTimesheetPerCutoff = Timesheet::whereIn('employee_id', $employeeIdArr)->whereBetween('daydate', array($cutOffDateFrom, $cutOffDateTo))->get();
        //$employeeTimesheetYesterDayDate = DB::table('employee_timesheet')->where('employee_id', $data['employeeno'])->where('daydate', $yesterDayDate)->get();    
        
        $timesheetPerCutoff = Timesheet::where('employee_id', $employeeId)->where('daydate', $yesterDayDate)->first();


        if( !empty($timesheetPerCutoff) ) {
          //dd($timesheetPerCutoff->employee_id);
          //echo $employeeId;
          
          $empId = (int) $timesheetPerCutoff->employee_id;
          $dayDate = $timesheetPerCutoff->daydate;
          $scheduleIn = $timesheetPerCutoff->schedule_in;
          $scheduleOut = $timesheetPerCutoff->schedule_out;
          $clockingStatus = $timesheetPerCutoff->clocking_status;        
          $timeIn1 = $timesheetPerCutoff->time_in_1;
          $timeOut1 = $timesheetPerCutoff->time_out_1;       

          //dd($cutoffArr);

          //CHECK IF HAS HOLIDAY

          //$checkHoliday = holiday::where('date', $dayDate)->first();
          //if ( !empty($checkHoliday) ) {

            //Todo Check Leave
            $checkLeavePerCutoff = Leave::where('employee_id', $empId)->whereBetween('from_date', array($cutOffDateFrom, $cutOffDateTo))->first();

            if ( !empty($checkLeavePerCutoff) ) {
              //$dayDate
              //dd($checkLeavePerCutoff->from_date);

              $startTime = strtotime($checkLeavePerCutoff->from_date);        
              $endTime = strtotime($checkLeavePerCutoff->to_date); 

              // Loop between timestamps, 1 day at a time 
              //$cutoffArr = array();
              $cutoffArr[] = date('Y-m-d', $startTime);         
              do {

                 $startTime = strtotime('+1 day', $startTime); 
                 $cutoffArr[] = date('Y-m-d', $startTime);
                 
              } while ($startTime < $endTime);

            }

            //CHECK IF HAS LEAVE
            if ( !in_array($dayDate, $cutoffArr) ) { //If has no leave found in the leave table

              if ( ($clockingStatus === 'open')) {
                if ( empty($timeIn1) && empty($timeOut1) ) {
                  if ( !empty($scheduleIn) && !empty($scheduleOut) ||
                      $scheduleIn !== '00:00:00' && $scheduleOut !== '00:00:00' ) {

                    echo 'absent';

                    $employeeSetting = DB::table('employee_setting')->where('employee_id', $empId)->first();

                    //echo $employeeSetting->hours_per_day;
                    //echo $timesheetPerCutoff->employee_id;

                    $summaryUpdate = Summary::where('employee_id', $empId)->where('daydate', $timesheetPerCutoff->daydate)->first();

                    $summaryUpdate->absent = $employeeSetting->hours_per_day;
                   //$summaryUpdate->save();

                  }
                }
              }

            } else {

              //dd($cutoffArr);
              echo $dayDate;

            }

          //}


        }

    }


  endif;

endif;
*/


$t = time();
$hourNeedle = date('G',strtotime(date('H:i:s', $t)));

$hourHaystack = array(0, 1, 2, 3, 4, 5, 6);

if ( in_array($hourNeedle, $hourHaystack) ) {

  $nightdiff = 'true';

}
else
  
{

 $nightdiff = 'false';

}


/*$dayDateModify = new DateTime($dayDate);

//Todo Add a condition here
if ( !empty($data['value']) && !empty($getTimesheet->time_in_3) ) {

  if(!$nightdiff) {

    $clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));

  } else {

    //$dayDateModify = new DateTime($dayDate);
    $dayDateModify->modify('+1 day');
    //$dayDateModify->format('Y-m-d');      
    $clockingDateTime = date('Y-m-d', strtotime($dayDateModify->format('Y-m-d'))).' '.date('H:i:s', strtotime($data['value']));

  }

  $getTimesheet->time_out_3 = $clockingDateTime;
  $getTimesheet->clocking_status = 'clock_out_3'; 

} elseif ( !empty($data['value']) && empty($getTimesheet->time_in_3) ) {

  if(!$nightdiff) {

    $clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));

  } else {

    //$dayDateModify = new DateTime($dayDate);
    $dayDateModify->modify('+1 day');
    //$dayDateModify->format('Y-m-d');      
    $clockingDateTime = date('Y-m-d', strtotime($dayDateModify->format('Y-m-d'))).' '.date('H:i:s', strtotime($data['value']));

  }

  $getTimesheet->time_out_3 = $clockingDateTime;
  $getTimesheet->clocking_status = 'clock_out_3'; //close   

} elseif ( empty($data['value']) && !empty($getTimesheet->time_in_3) ) {

  $clockingDateTime = '';
  $getTimesheet->time_out_3 = $clockingDateTime;
  $getTimesheet->clocking_status = 'clock_in_3';      

} elseif ( empty($data['value']) && empty($getTimesheet->time_in_3) ) {

  $clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));

  if(!$nightdiff) {

    $clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));      

  } else {

    //$dayDateModify = new DateTime($dayDate);
    $dayDateModify->modify('+1 day');
    //$dayDateModify->format('Y-m-d');      
    $clockingDateTime = date('Y-m-d', strtotime($dayDateModify->format('Y-m-d'))).' '.date('H:i:s', strtotime($data['value']));

  }

  $getTimesheet->time_out_3 = '';
  $getTimesheet->clocking_status = 'close';         

} */


?>

        <div class="row" style="padding-bottom:20px;">

          <div class="col-md-2">
            <p style="margin:0 auto; text-align:center;"><img src="" data-src="holder.js/150x150?bg=959595&fg=dcdcdc" alt="" class="img-circle"></p>
          </div><!--//.col-md-2-->

          <div class="col-md-3">
            <p id="clock" style="margin:0 0 0 0; padding:0 0 0 0; text-align:center; line-height:1; font-size:65px; font-weight:normal;">09:00</p>
            <p style="margin:0 0 0 0; padding:0 0 0 0; text-align:center; line-height:1; font-size:16px; font-weight:normal;"><?php echo date('l, F d\t\h Y'); ?></p>
          </div><!--//.col-md-3-->

          <div class="col-md-2">                      
            <!--p style="padding-top:25px; text-align:center;"><input type="button" value="Time In" class="btn btn-custom-default" style="font-size:36px; font-weight:normal;" /></p-->


			{{ Form::open(array('', 'id' => 'timeClockingForm')) }}				
				<!--button id="time-in" class="btn btn-primary btn-lg" role="button">Time In</button-->	
				{{-- Form::hidden('employeenumber', Auth::user()->employee_id, array('id' => 'employee-number')) --}}							                            												
				{{ Form::hidden('employeenumber', $data['employeeno'], array('id' => 'employee-number')) }}							
				
				{{ Form::hidden('overtime_status_1', $employeeClocking->overtime_status_1, array('id' => 'overtime-status-1')) }}
				{{ Form::hidden('overtime_status_2', $employeeClocking->overtime_status_2, array('id' => 'overtime-status-2')) }}
				{{ Form::hidden('overtime_status_3', $employeeClocking->overtime_status_3, array('id' => 'overtime-status-3')) }}

				{{ Form::hidden('daydate', date('Y-m-d'), array('id' => 'day-date')) }}							
				{{ Form::hidden('timenow', strtotime(date('H:i:s')), array('id' => 'time-now')) }}							

				@if( !empty($employeeWorkShift) )
					{{ Form::hidden('schedin', strtotime($employeeWorkShift[0]->start_time), array('id' => 'sched-in')) }}
					{{ Form::hidden('schedout', strtotime($employeeWorkShift[0]->end_time), array('id' => 'sched-out')) }}							
				@endif

				{{ Form::hidden('timein1', date('H:i:s'), array('id' => 'time-in-1')) }}
				{{ Form::hidden('timeout1', date('H:i:s'), array('id' => 'time-out-1')) }}

				{{ Form::hidden('timein2', date('H:i:s'), array('id' => 'time-in-2')) }}
				{{ Form::hidden('timeout2', date('H:i:s'), array('id' => 'time-out-2')) }}														

				{{ Form::hidden('timein3', date('H:i:s'), array('id' => 'time-in-3')) }}
				{{ Form::hidden('timeout3', date('H:i:s'), array('id' => 'time-out-3')) }}	
											
				{{-- Form::submit('Time In', array('id' => 'time-clocking-btn', 'class' => 'time-in btn btn-primary btn-lg')) --}}							
																
				{{ Form::hidden('forgotyesterdaytimeout', $forgotYesterdayTimeOut, array('id' => 'forgot-yesterday-timeout')) }}																																																																										


			{{-- // Check yesterday clocking status --}}

			@if ( !empty($getYesterDayDate[0]) )

				@if ( $getYesterDayDate[0]->clocking_status === 'open' ||
					  $getYesterDayDate[0]->clocking_status === 'close' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_3' ||
					$getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' )

					@if ( $employeeClocking->clocking_status === 'open' ||
					 	  $employeeClocking->clocking_status === 'forgot_to_clock_out' )
				
						{{ Form::button('Time In', array('id' => 'time-clocking-btn', 'class' => 'time-in btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal')) }}																																											
						{{ Form::button('Time Out', array('id' => 'time-clocking-btn', 'class' => 'hide time-out btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal')) }}																																																		

					@elseif ( $employeeClocking->clocking_status === 'clock_in_1' || 
							  $employeeClocking->clocking_status === 'clock_in_2' ||
							  $employeeClocking->clocking_status === 'clock_in_3' )

						{{ Form::button('Time In', array('id' => 'time-clocking-btn', 'class' => 'hide time-in btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal')) }}																																											
						{{ Form::button('Time Out', array('id' => 'time-clocking-btn', 'class' => 'time-out btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal')) }}																																																																											

					@endif

					@if ( $employeeClocking->clocking_status === 'clock_out_1' || 
						  $employeeClocking->clocking_status === 'clock_out_2' ||
						  $employeeClocking->clocking_status === 'clock_out_3' )

							{{ Form::button('Time In', array('id' => 'time-clocking-btn', 'class' => 'time-in btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal')) }}																																											
							{{ Form::button('Time Out', array('id' => 'time-clocking-btn', 'class' => 'hide time-out btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal')) }}																																																		

					@endif
				
				@endif

				{{-- @if ( $getYesterDayDate[0]->clocking_status === 'clock_in_1' ||
					  $getYesterDayDate[0]->clocking_status === 'clock_in_2' ||
				      $getYesterDayDate[0]->clocking_status === 'clock_in_3' ||
				      $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' ) --}}

        @if ( $getYesterDayDate[0]->clocking_status === 'clock_in_1' ||
            $getYesterDayDate[0]->clocking_status === 'clock_in_2' ||
              $getYesterDayDate[0]->clocking_status === 'clock_in_3' )              

					@if ( $employeeClocking->clocking_status === 'open' ||
					 	  $employeeClocking->clocking_status === 'forgot_to_clock_out' )
				
						{{ Form::button('Time In', array('id' => 'time-clocking-btn', 'class' => 'hide time-in btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal')) }}																																											
						{{ Form::button('Time Out', array('id' => 'time-clocking-btn', 'class' => 'time-out btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal')) }}																																																		

					@endif

				@elseif ( $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' )

					@if ( $employeeClocking->clocking_status === 'clock_in_3' )
				
						{{ Form::button('Time In', array('id' => 'time-clocking-btn', 'class' => 'hide time-in btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal')) }}																																											
						{{ Form::button('Time Out', array('id' => 'time-clocking-btn', 'class' => 'time-out btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal')) }}																																																		

					@endif								
					
				@endif

			@elseif ( empty($getYesterDayDate[0]) )

				{{ Form::button('Time In', array('id' => 'time-clocking-btn', 'class' => 'time-in btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal', 'style' => 'font-size:36px; font-weight:normal')) }}																																											
				{{ Form::button('Time Out', array('id' => 'time-clocking-btn', 'class' => 'hide time-out btn btn-custom-default', 'style' => 'font-size:36px; font-weight:normal')) }}																																																		
				

			@endif						

	
			{{ Form::close() }}

          <div id="wait"></div>
     
          </div>          
          <div class="col-md-5 hide hidden">
            <div class="pull-right">
           
            <?php
            /*
            Employee
            Supervisor
            Manager
            Payroll
            Administrator
            */

            /*if( !empty($groups) ) :

              if ( strcmp(strtolower($groups->name), strtolower('Administrator')) === 0 ||
                  strcmp(strtolower($groups->name), strtolower('Manager')) === 0 ||
                  strcmp(strtolower($groups->name), strtolower('Supervisor')) === 0 ||
                  strcmp(strtolower($groups->name), strtolower('Payroll')) === 0 ) : ?>

      			{{ Form::open(array('route' => 'searchTimesheet', 'method' => 'get', 'id' => 'searchTimesheetForm', 'class' => 'form-inline search')) }}	

      				{{-- Form::hidden('search', 'search')--}}
      				{{ Form::label('Employees', 'Employees', array('class' => 'sr-only')) }}
      				{{ Form::select('employeeid', $employeeArr, '', array('id' => 'employee-id', 'class' => 'form-control')) }}

      				{{-- Form::button('<i class="fa fa-search"></i>', array('id' => 'search-timesheet-btn', 'class' => 'btn btn-custom-default')) --}}
              {{ Form::submit('Edit', array('id' => 'search-timesheet-btn', 'class' => 'btn btn-custom-default')) }}

      			{{ Form::close() }}

            <?php
              endif;

            endif;*/
            ?>                        

            <form class="form-inline search hide hidden">
              <div class="form-group">
                <label for="inputSearch" class="sr-only">Emloyee Name</label>
                <select class="form-control">
                  <option value="">Catherine Lor</option>
                  <option value="">Jessie Dayrit</option>
                  <option value="">Justino Arciga</option>
                  <option value="">Ivy lane Opon</option>
                </select>
              </div>
              <button type="submit" class="btn btn-custom-default"><i class="fa fa-search"></i></button>
            </form>

            <!-- Split button -->
            <div class="btn-group hide hidden">
              <button type="button" class="btn btn-default">Search Department Employee Name</button>
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
              </button>
              <ul class="dropdown-menu" role="menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Catherine Lor</a></li>
                <li><a href="#">Jessie Dayrit</a></li>
                <li><a href="#">Justino Arciga</a></li>
                <li><a href="#">Ivy lane Opon</a></li>
              </ul>
            </div>            

          </div>

          </div><!--//.col-md-6-->        

        </div><!--//.row-->


        <div class="row">

          <div id="sidebar-container" class="col-md-2">

            <div class="sidebar">

              <div class="panel panel-custom panel-custom-default">
                <div class="panel-heading">
                  
                  <h3 class="panel-title" style="font-size:11px;">
                  <?php if ( !empty($employeeInfo) ) { ?>
                    {{ $employeeInfo[0]->firstname }}, {{ $employeeInfo[0]->lastname }}
                  <?php } ?>
                  </h3>
                  
                </div>
                <div class="panel-body hide hidden"></div>
                  <section id="designation" style="background-color:#1a1a19;">
                    <div class="row">                              
                      <div class="col-md-12">

                        <table class="table table-inline table-condensed">
                          <tbody>
                          <tr>
                            <td class="first-tr-td">Employee No. <span>
                              <?php if ( !empty($employeeInfo) ) { ?>
                                {{ $employeeInfo[0]->employee_number }}
                              <?php } ?>
                            </span></td>                            
                          </tr>
                          <tr>
                            <td>Designation:<br />
                              <span>
                                <?php if ( !empty($jobTitle) ) { ?>
                                  {{ $jobTitle->name }}
                                <?php } ?>

                              </span></td>
                          </tr>
                          <tr>
                            <td>Team:<br />
                              <span>{{ $department->name; }}</span></td>
                          </tr>
                          <tr
>                            <td>Manager / Supervisor:<br />
                              <span>{{ $managerFullname }}</span></td>
                          </tr>
                          <tr>
                            <td>Default Shift:<br />
                                <span class="hide hidden">8:00 am 5:00 pm</span><br />
                                <span class="hide hidden">Monday - Friday</span>
                            </td>
                          </tr>                                       
                          </tbody>
                        </table>

                      </div>          
                    <div>
                  </section><!--//#designation-->                 

              </div><!--//.panel-default-->                 


              <div class="panel-group panel-custom-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-custom panel-custom-default">
                  <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading1">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse1" aria-expanded="true" aria-controls="collapse1">
                        Employee Info                        
                      <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span>                        
                      </a>                      
                    </h4>
                  </div>
                  <div id="collapse1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading1">
                    <div class="panel-body hide hidden"></div>

                    <section style="background-color:#1a1a19;">
                      <div class="row">                              
                        <div class="col-md-12">

                          <table class="table table-inline table-condensed">
                            <tbody>
                            <tr>
                              <td class="first-tr-td">Employee Info <span>Content</span></td>                            
                            </tr>                                       
                            </tbody>
                          </table>

                        </div>          
                      <div>
                    </section><!--//#section-->

                  </div>
                </div>
                <div class="panel panel-custom panel-custom-default">
                  <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading2">
                    <h4 class="panel-title">
                      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse2" aria-expanded="false" aria-controls="collapse2">
                        Compensation                        
                      <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span>                        
                      </a>
                    </h4>
                  </div>
                  <div id="collapse2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading2">
                    
                    <div class="panel-body hide hidden"></div>

                    <section style="background-color:#1a1a19;">
                      <div class="row">                              
                        <div class="col-md-12">

                          <table class="table table-inline table-condensed">
                            <tbody>
                            <tr>
                              <td class="first-tr-td">Compensation <span>Content</span></td>                            
                            </tr>                                                                  
                            </tbody>
                          </table>

                        </div>          
                      <div>
                    </section><!--//#section-->

                  </div>
                </div>
                <div class="panel panel-custom panel-custom-default">
                  <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading3">
                    <h4 class="panel-title">
                      <a class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse3" aria-expanded="false" aria-controls="collapse3">
                        Tax Exemption
                      <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span>                        
                      </a>

                    </h4>
                  </div>
                  <div id="collapse3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading3">
                    
                    <div class="panel-body hide hidden"></div>

                    <section style="background-color:#1a1a19;">
                      <div class="row">                              
                        <div class="col-md-12">

                          <table class="table table-inline table-condensed">
                            <tbody>
                            <tr>
                              <td class="first-tr-td">Tax Exemption <span>Content</span></td>                            
                            </tr>                                                                  
                            </tbody>
                          </table>

                        </div>          
                      <div>
                    </section><!--//#section-->

                  </div>
                </div>
                <div class="panel panel-custom panel-custom-default">
                  <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading4">
                    <h4 class="panel-title">
                      <a class="collapsed hide hidden" data-toggle="collapse" data-parent="#accordion" href="#collapse4" aria-expanded="false" aria-controls="collapse4">
                        Leave Credits                        
                        <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span> 
                      </a>
                        Leave Credits
                    </h4>
                  </div>
                  <div id="collapse4" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading4">
                    
                    <div class="panel-body hide hidden"></div>

                    <section style="background-color:#1a1a19;">
                      <div class="row">                              
                        <div class="col-md-12">


                          <table class="table table-inline table-condensed">
                            <tbody>
                            <tr>
                              <td class="first-tr-td">Sick Leave: <span>5.5</span></td>                            
                            </tr>                                      
                            <tr>
                              <td class="first-tr-td">Vacation Leave: <span>7</span></td>                            
                            </tr>                                      
                            <tr>
                              <td class="first-tr-td">
                                <a href="{{ url('/admin/user/leave/') }}" class="btn btn-custom-default center-block" style="font-size:11px;">Leave Application</a>
                              </td>                            
                            </tr>                                                                                              
                            </tbody>
                          </table>

                        </div>          
                      <div>
                    </section><!--//#section-->

                  </div>
                </div>

                <div class="panel panel-custom panel-custom-default">
                  <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading5">
                    <h4 class="panel-title">
                      <a class="collapsed hide hidden" data-toggle="collapse" data-parent="#accordion" href="#collapse5" aria-expanded="false" aria-controls="collapse5">
                        Change Schedule
                        <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span>                        
                      </a>
                        Change Schedule                      
                    </h4>
                  </div>
                  <div id="collapse5" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading5">
                    
                    <div class="panel-body hide hidden"></div>

                    <section style="background-color:#1a1a19;">
                      <div class="row">                              
                        <div class="col-md-12">

                          <table class="table table-inline table-condensed">
                            <tbody>
                            <tr>
                              <td class="first-tr-td">
                                <a href="#" class="btn btn-custom-default center-block" style="font-size:11px;">Change Schedule</a>
                              </td>                            
                            </tr>                                    
                            </tbody>
                          </table>

                        </div>          
                      <div>
                    </section><!--//#section-->


                  </div>
                </div>

                <div class="panel panel-custom panel-custom-default">
                  <div class="panel-heading panel-heading-trl-radius-reset" role="tab" id="heading6">
                    <h4 class="panel-title">
                      <a class="collapsed hide hidden" data-toggle="collapse" data-parent="#accordion" href="#collapse6" aria-expanded="false" aria-controls="collapse5">
                        Other Request
                        <span class="custom-plus-icon pull-right"><i class="fa fa-plus"></i></span>                        
                      </a>
                      Other Request
                    </h4>
                  </div>
                  <div id="collapse6" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading6">
                    
                    <div class="panel-body hide hidden"></div>

                    <section style="background-color:#1a1a19;">
                      <div class="row">                              
                        <div class="col-md-12">

                          <table class="table table-inline table-condensed">
                            <tbody>
                            <tr>
                              <td class="first-tr-td">For Other Request, Please ask Human Resource Personel</td>                            
                            </tr>                                      
                            </tbody>
                          </table>

                        </div>          
                      <div>
                    </section><!--//#section-->


                  </div>
                </div>                

              </div>             


            </div><!--//.sidebar-->              


          </div>          

          <div class="col-md-10">
            <div class="row">
              
              <div class="col-md-12">
                  {{-- Session::get('searchEmployeeId') --}}
                  {{-- $cutOffDateFrom.' - '.$cutOffDateTo --}}
                
                <div class="table-body-container table-responsive">
                  <div class="table-head-container"></div><!--/.table-head-container-->

            	    <!--h3 style="font-size:14px; font-weight:bold;">My Timesheet</h3-->

                  <table id="timesheet-ajax" class="timesheet table table-striped table-hover table-condensed display" cellspacing="0" width="100%">
                  <thead>                             
                    <tr style="background-color: #b32728; color:#dcdcdc; text-transform: uppercase;">
                      <th class="hide hidden">ID</th>                 
                      <th>Date</th>                 
                      <th>Schedule</th>                   
                      <th style="text-align:right;">Time<br />In&nbsp;1</th>
                      <th>-</th>
                      <th style="text-align:left;">Time<br />Out&nbsp;1</th>
                      <th style="text-align:right;">Time<br />In&nbsp;2</th>
                      <th>-</th>
                      <th style="text-align:left;">Time<br />Out&nbsp;2</th>
                      <th style="text-align:right;">Time<br />In&nbsp;3</th>
                      <th>-</th>
                      <th style="text-align:left;">Time<br />Out&nbsp;3</th>                            
                      <th>Total<br />Hours</th>
                      <th>Work<br />Hours</th>    
                      <th>Night<br />Diff</th>             
                      <th>Total<br />Overtime</th>                      
                      <th>Tardiness</th>                
                      <th>Undertime</th>
                      <th>OT&nbsp;Status</th>
                    </tr>
                  </thead>

                  <tfoot class="hide hidden">
                    <tr style="background-color: #b32728; color:#dcdcdc; text-transform: uppercase;">
                      <th class="hide hidden">ID</th>                 
                      <th>Date</th>                 
                      <th>Schedule</th>                   
                      <th style="text-align:right;">Time<br />In&nbsp;1</th>
                      <th>-</th>
                      <th style="text-align:left;">Time<br />Out&nbsp;1</th>
                      <th style="text-align:right;">Time<br />In&nbsp;2</th>
                      <th>-</th>
                      <th style="text-align:left;">Time<br />Out&nbsp;2</th>
                      <th style="text-align:right;">Time<br />In&nbsp;3</th>
                      <th>-</th>
                      <th style="text-align:left;">Time<br />Out&nbsp;3</th>                            
                      <th>Total<br />Hours</th>
                      <th>Work<br />Hours</th>    
                      <th>Night<br />Diff</th>             
                      <th>Total<br />Overtime</th>                      
                      <th>Tardiness</th>                
                      <th>Undertime</th>
                      <th>OT&nbsp;Status</th>
                    </tr>
                  </tfoot>
                  <tbody>                  
                  <?php foreach($getTimesheetPerCutoff as $timesheet) : ?>        
                    <tr id="<?php echo $timesheet->id; ?>">
                      <td class="timesheet-id-<?php echo $timesheet->id; ?> hide hidden"><?php echo $timesheet->id; ?></td>                 
                      <td class="timesheet-daydate-<?php echo $timesheet->id; ?>"><?php echo $timesheet->daydate; ?></td>                 
                      <td class="timesheet-schedule-<?php echo $timesheet->id; ?>"><?php echo $timesheet->schedule_in . ' - ' . $timesheet->schedule_out; ?></td>                   
                      <td class="edit-cell timesheet-in1-<?php echo $timesheet->id; ?>" style="text-align:right;"><?php
                      if ( !empty($timesheet->time_in_1) ) {
                           echo date('H:i', strtotime($timesheet->time_in_1));
                      }
                      ?></td>
                      <td style="text-align:center;">-</td>
                      <td class="edit-cell timesheet-out1-<?php echo $timesheet->id; ?>" style="text-align:left;"><?php
                      if ( !empty($timesheet->time_out_1) ) {
                           echo date('H:i', strtotime($timesheet->time_out_1));
                      }
                      ?></td>                      

                      <td class="edit-cell timesheet-in2-<?php echo $timesheet->id; ?>" style="text-align:right;"><?php
                      if ( !empty($timesheet->time_in_2) ) {
                           echo date('H:i', strtotime($timesheet->time_in_2));
                      }
                      ?></td>
                      <td style="text-align:center;">-</td>
                      <td class="edit-cell timesheet-out2-<?php echo $timesheet->id; ?>" style="text-align:left;"><?php
                     if ( !empty($timesheet->time_out_2) ) {
                           echo date('H:i', strtotime($timesheet->time_out_2));
                      }
                      ?></td>
                          <td class="edit-cell timesheet-in3-<?php echo $timesheet->id; ?>" style="text-align:right;"><?php
                      if ( !empty($timesheet->time_in_3) ) {
                           echo date('H:i', strtotime($timesheet->time_in_3));
                      }
                      ?></td>
                      <td style="text-align:center;">-</td>
                      <td class="edit-cell timesheet-out3-<?php echo $timesheet->id; ?>" style="text-align:left;"><?php
                     if ( !empty($timesheet->time_out_3) ) {
                           echo date('H:i', strtotime($timesheet->time_out_3));
                      }
                      ?></td>
                      <td class="timesheet-totalhours-<?php echo $timesheet->id; ?>"><?php echo $timesheet->total_hours; ?></td>
                      <td class="timesheet-workhours-<?php echo $timesheet->id; ?>"><?php echo $timesheet->work_hours; ?></td>                 
                      <td class="timesheet-nightdifferential-<?php echo $timesheet->id; ?>"><?php echo $timesheet->night_differential; ?></td-->                      
                      <td class="timesheet-totalovertime-<?php echo $timesheet->id; ?>"><?php echo $timesheet->total_overtime; ?></td>
                      <td class="timesheet-tardiness-<?php echo $timesheet->id; ?>"><?php echo $timesheet->tardiness; ?></td>                
                      <td class="timesheet-undertime-<?php echo $timesheet->id; ?>"><?php echo $timesheet->undertime; ?></td>
                      <td class="ot-status-btn timesheet-otstatus-<?php echo $timesheet->id; ?>" style="text-align:center;"><?php echo $timesheet->overtime_status; ?></td>
                    </tr>

                  <?php endforeach; ?>
                  </tbody>
                  </table>   

                    <div class="table-foot-container"></div><!--/.table-footer-container-->               

                <nav class="hide hidden pull-right">{{-- $getTimesheetPerCutoff->links() --}}</nav>
                
                </div><!--/.table-body-container-->               

              </div>


              <div class="col-md-12">

                  <div class="panel panel-custom panel-custom-default">
                    <div class="panel-heading">
                      <h3 class="panel-title">Summary <a href="{{ url('/employee/report/summary') }}" class="pull-right">View Summary Report</a></h3>                    
                    </div>
                    <div class="panel-body hide hidden"></div>
                    <section id="summary" style="background-color:#1a1a19;">
                      <div class="row">
                        <div class="col-md-3">
                          <table class="table table-inline table-condensed summary">
                            <tbody>
                            <tr>
                              <td class="first-tr-td">Lates / UT</td>
                              <td id="lates-ut" class="first-tr-td"></span></td>                
                            </tr>
                            <tr>
                              <td>Absences</td>
                              <td id="absences"></td>
                            </tr>
                            <tr>
                              <td>Paid SL</td>
                              <td id="paid-sl"></td>
                            </tr>
                            <tr>
                              <td>Paid VL</td>
                              <td id="paid-vl"></td>
                            </tr>
                            <tr>
                              <td>Leave w/o Pay</td>
                              <td id="leave-without-pay"></td>
                            </tr>
                            <tr>
                              <td>Maternity Leave</td>
                              <td id="maternity-leave"></td>
                            </tr>
                            <tr>
                              <td>Paternity Leave</td>
                              <td id="paternity-leave"></td>
                            </tr>

                            </tbody>
                          </table>
                        </div>
                        <div class="col-md-3">
                          <table class="table table-inline table-condensed summary">
                            <tbody>
                            <tr>
                              <td class="first-tr-td">Regular</td>
                              <td id="regular" class="first-tr-td"></td>
                            </tr>                              
                            <tr>
                              <td>Reg OT</td>
                              <td id="reg-ot"></td>
                            </tr>
                            <tr>
                              <td>Reg OT+ND</td>
                              <td id="reg-ot-nd"></td>
                            </tr>
                            <tr>
                              <td>Reg ND</td>
                              <td id="reg-nd"></td>
                            </tr>
                            <tr>
                              <td>RD (First 8hrs)</td>
                              <td id="rd"></td>
                            </tr>
                            <tr>
                              <td>RD OT</td>
                              <td id="rd-ot"></td>
                            </tr>
                            <tr>
                              <td>RD OT+ND</td>
                              <td id="rd-ot-nd"></td>
                            </tr>
                            <tr>
                              <td>RD ND</td>
                              <td id="rd-nd"></td>
                            </tr>

                            </tbody>
                          </table>
                        </div>
                      <div class="col-md-3">            
                        <table class="table table-inline table-condensed summary">
                            <tbody>
                            <tr>
                              <td class="first-tr-td">SPL Holiday (First 8Hrs)</td>
                              <td id="spl-holiday" class="first-tr-td"></td>
                            </tr>
                            <tr>
                              <td>SPL Holiday OT</td>
                              <td id="spl-holiday-ot"></td>
                            </tr>
                            <tr>
                              <td>SPL Holiday OT+ND</td>
                              <td id="spl-holiday-ot-nd"></td>
                            </tr>
                            <tr>
                              <td>SPL Holiday ND</td>
                              <td id="spl-holiday-nd"></td>
                            </tr>
                          <tr>
                              <td>LEGAL Holiday</td>
                              <td id="legal-holiday"></td>
                            </tr>             
                            <tr>
                              <td>LEGAL Holiday OT</td>
                              <td id="legal-holiday-ot"></td>
                            </tr>
                            <tr>
                              <td>LEGAL Holiday OT+ND</td>
                              <td id="legal-holiday-ot-nd"></td>
                            </tr>
                            <tr>
                              <td>LEGAL Holiday ND</td>
                              <td id="legal-hoiday-nd"></td>
                            </tr>

                            </tbody>
                          </table>

                        </div>          
                      <div class="col-md-3">
                        <table class="table table-inline table-condensed summary">
                            <tbody>
                            <tr>
                              <td class="first-tr-td">RD SPL Holiday (First 8Hrs)</td>
                              <td id="rd-spl-holiday" class="first-tr-td"></td>
                            </tr>
                            <tr>
                              <td>RD SPL Holiday OT</td>
                              <td id="rd-spl-holiday-ot"></td>
                            </tr>
                            <tr>
                              <td>RD SPL Holiday OT+ND</td>
                              <td id="rd-spl-holiday-ot-nd"></td>
                            </tr>
                            <tr>
                              <td>RD SPL Holiday ND</td>
                              <td id="rd-spl-holiday-nd"></td>
                            </tr>
                            <tr>
                              <td>RD LEGAL Holiday</td>
                              <td id="rd-legal-holiday"></td>
                            </tr>             
                            <tr>
                              <td>RD LEGAL Holiday OT</td>
                              <td id="rd-legal-holiday-ot"></td>
                            </tr>
                            <tr>
                              <td>RD LEGAL Holiday OT+ND</td>
                              <td id="rd-legal-holiday-ot-nd"></td>
                            </tr>
                            <tr>
                              <td>RD LEGAL Holiday ND</td>
                              <td id="rd-legal-holiday-nd"></td>
                            </tr>

                            </tbody>
                          </table>            
                        </div>          
                      <div>
                    </section><!--//#summary-->                 

                  </div><!--//.panel-default-->  


              </div>
            
            </div>
          </div>

        </div><!--//.row-->

      </div><!--//.page-container-->

@stop