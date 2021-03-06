@extends('layouts.admin.default')

@section('content')

<?php  
$employeeId = Session::get('userEmployeeId');
$userId = Session::get('userId');
//$searchEmployeeId = Session::get('searchEmployeeId');

//http://stackoverflow.com/questions/1519818/how-do-check-if-a-php-session-is-empty
/*if ( isset($searchEmployeeId) && !empty($searchEmployeeId) ) {

	//echo 'Search:'. $searchEmployeeId;	            
	$employeeId = $employeeId;

} elseif ( isset($employeeId) && !empty($employeeId) ) {

	//echo $employeeId;
	$employeeId = $searchEmployeeId;

}*/

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


    //$cutoff['dateFrom'][2] = strtotime('-1 month' , strtotime($cutoff['dateFrom'][2]));
    //$cutoff['dateFrom'][2] = date('Y-m-d' , $cutoff['dateFrom'][2]);

    //$cutoff['dateTo'][2] = strtotime('1 month' , strtotime($cutoff['dateTo'][2]));
    //$cutoff['dateTo'][2] = date('Y-m-d' , $cutoff['dateTo'][2]);          

    //$cutoff['dateFrom'][2] = strtotime('-1 month' , strtotime($cutoff['dateFrom'][2]));
    //$cutoff['dateFrom'][2] = date('Y-m-d' , strtotime($cutoff['dateFrom'][2]));         


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

  $cutOffDateFrom = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2;
  $cutOffDateTo = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2;   

  Session::put('cutOffDateFrom', $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2);
  Session::put('cutOffDateTo', $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2); 

} 	

//$summaries = Summary::where('employee_id', $employeeId)->whereBetween('daydate', [$cutOffDateFrom, $cutOffDateTo])->get();

$summary = DB::table('employee_summary')
                     ->select(DB::raw('SUM(lates) as lates, SUM(undertime) as undertime, SUM(absent) as absent, SUM(paid_sick_leave) as paid_sick_leave, SUM(paid_vacation_leave) as paid_vacation_leave, SUM(leave_without_pay) as leave_without_pay, SUM(maternity_leave) as maternity_leave, SUM(paternity_leave) as paternity_leave,
                      SUM(regular) as regular, SUM(regular_overtime) as regular_overtime, SUM(regular_overtime_night_diff) as regular_overtime_night_diff, SUM(regular_night_differential) as regular_night_differential, SUM(rest_day) as rest_day, SUM(rest_day_overtime) as rest_day_overtime, SUM(rest_day_overtime_night_diff) as rest_day_overtime_night_diff, SUM(rest_day_night_differential) as rest_day_night_differential,
                       SUM(rest_day_special_holiday) as rest_day_special_holiday, SUM(rest_day_special_holiday_overtime) as rest_day_special_holiday_overtime, SUM(rest_day_special_holiday_overtime_night_diff) as rest_day_special_holiday_overtime_night_diff, SUM(rest_day_special_holiday_night_diff) as rest_day_special_holiday_night_diff, SUM(rest_day_legal_holiday) as rest_day_legal_holiday, SUM(rest_day_legal_holiday_overtime) as rest_day_legal_holiday_overtime,
                       SUM(rest_day_legal_holiday_overtime_night_diff) as rest_day_legal_holiday_overtime_night_diff, SUM(rest_day_legal_holiday_night_diff) as rest_day_legal_holiday_night_diff, SUM(special_holiday) as special_holiday, SUM(special_holiday_overtime) as special_holiday_overtime, SUM(special_holiday_overtime_night_diff) as special_holiday_overtime_night_diff, SUM(special_holiday_night_diff) as special_holiday_night_diff, SUM(legal_holiday) as legal_holiday,
                       SUM(legal_holiday_overtime) as legal_holiday_overtime, SUM(legal_holiday_overtime_night_diff) as legal_holiday_overtime_night_diff, SUM(legal_holiday_night_diff) as legal_holiday_night_diff'))
					 ->where('employee_id', $employeeId)
					 ->whereBetween('daydate', [$cutOffDateFrom, $cutOffDateTo])                     
                     ->first();

//dd($summary);

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

$manager = '';
$manager = Employee::where('id', '=', $employeeInfo[0]->manager_id)->first();

if ( !empty($manager) ) {

	$managerFullname = $manager->firstname.', '.$manager->lastname;

} else {

	$managerFullname = '';

}

$employees = DB::table('employees')
	->where('manager_id', $employeeInfo[0]->id)
  	->orWhere('supervisor_id', $employeeInfo[0]->id)
	//->where('company_id', $employeeInfo[0]->company_id) 
	//->where('department_id', $employeeInfo[0]->department_id)
	->get();

$employeeArr[0] = '';
foreach($employees as $employee) {

	$employeeArr[$employee->id] = $employee->firstname. ', ' .$employee->lastname;

}

//$getSchedule = DB::table('employee_schedule')->where('employee_id', $employee->id)->where('schedule_date', trim($currentDate))->get();
//$getWorkShiftByDayOfTheWeek = DB::table('work_shift')->where('employee_id',  $employee->id)->where('name_of_day', $dayOfTheWeek)->where('shift', $shift)->get();

?>
<div class="page-container" style="padding-bottom:20px;">

        <!--div class="row" style="padding-bottom:20px;"-->

          <div class="col-md-3 clearfix hide hidden">

            <aside class="sidebar">
              <nav class="sidebar-nav">
                <ul id="menu">
                  <li>
                    <a href="{{ url('/admin/dashboard') }}">
                      <span class="sidebar-nav-item-icon fa fa-tachometer fa-lg"></span>                      
                      <span class="sidebar-nav-item">Dashboard</span>                      
                    </a>
                    
                  </li>
                  <li>
                      <a href="{{ url('/admin/timeclock') }}">
                      <span class="sidebar-nav-item-icon fa fa-clock-o fa-lg"></span>
                      <span class="sidebar-nav-item">TimeClock & Attendance</span>
                      </a>
                      <!--ul class="submenu-1 collapse">
                          <li><a href="{{ url('/admin/timeclock') }}">Overtime</a></li>
                          <li><a href="{{ url('/admin/timeclock/report') }}">Overtime</a></li>
                      </ul-->
                  </li>
                  <li>
                      <a href="{{ url('/admin/scheduling') }}">
                      <span class="sidebar-nav-item-icon fa fa-calendar fa-lg"></span>
                      <span class="sidebar-nav-item">Employee Scheduling</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>
                  <li>
                      <a href="{{ url('/admin/hr') }}">
                      <span class="sidebar-nav-item-icon fa fa-users fa-lg"></span>
                      <span class="sidebar-nav-item">Human Resources</span>
                      </a>
                      <ul class="submenu-1 collapse">
                          <li><a href="{{ url('/admin/hr/employees') }}">Employees</a></li>
                      </ul>
                  </li>                  
                  <li>
                      <a href="{{ url('/admin/payroll') }}">
                      <span class="sidebar-nav-item-icon fa fa-calculator fa-lg"></span>
                      <span class="sidebar-nav-item">Payroll</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>
                  <li>
                      <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-cogs fa-lg"></span>
                      <span class="sidebar-nav-item">Admin</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>                                                      
                </ul>
               </nav>
            </aside>                      

          </div><!--//.col-md-2-->

          <!--div id="content" class="col-md-12" role="main"-->

            <ol class="breadcrumb hide hidden">
              <li><a href="#">Home</a></li>
              <li class="active">Page</li>
            </ol>

            <h1 class="page-header">Report</h1>

            

			<table  class="table" width="100%" border="0">
			  <tr>
			    <th style="width:10%; border:none;">Employee No.</th>
			    <td style="width:10%; border:none;">{{ $employeeInfo[0]->employee_number }}</td>
			    <th style="width:10%; border:none;">Employee Name:</th>
			    <td style="width:10%; border:none;">{{ $employeeInfo[0]->firstname }}, {{ $employeeInfo[0]->lastname }}</td>
			    <th style="width:15%; border:none;">Manager / Supervisor:</th>
			    <td style="border:none;">{{ $managerFullname }}</td>
			  </tr>
			  <tr>
			    <th style="width:10%; border:none;">Designation:</th>
			    <td style="width:10%; border:none;">{{ $jobTitle->name }}</td>
			    <th style="width:10%; border:none;">Department:</th>
			    <td style="width:10%; border:none;">{{ $department->name }}</td>
			    <td style="width:15%; border:none;">&nbsp;</td>
			    <td style="border:none;">&nbsp;</td>
			  </tr>
			</table>			            


			<!--div class="row"-->

				<!--div class="col-md-12"-->			
					<div class="panel panel-default">
					  <!-- Default panel contents -->
					  <div class="panel-heading" style="font-size:14px; font-weight:bold;">Summary</div>
					  <div class="panel-body">

					  </div><!--/.pane-body-->        


					    <div class="table-responsive">

							<table class="table table-striped table-hover table-list display" height="250">
							<thead>
								<tr>
									<th>Lates / UT</th>
									<th>Absences</th>	
									<th>Paid SL</th>
									<th>Paid VL</th>
									<th>Leave w/o Pay</th>
									<th>Maternity Leave</th>	
									<th>Paternity Leave</th>
									<th>Regular</th>
									<th>Reg OT</th>
									<th>Reg OT+ND</th>
									<th>Reg ND</th>	
									<th>RD (First 8hrs)</th>
									<th>RD OT</th>
									<th>RD OT+ND</th>
									<th>RD ND</th>	
									<th>SPL Holiday (First 8Hrs)</th>
									<th>SPL Holiday OT</th>
									<th>SPL Holiday OT+ND</th>
									<th>SPL Holiday ND</th>	
									<th>LEGAL Holiday</th>
									<th>LEGAL Holiday OT</th>
									<th>LEGAL Holiday OT+ND</th>	
									<th>LEGAL Holiday ND</th>
									<th>RD SPL Holiday (First 8Hrs)</th>
									<th>RD SPL Holiday OT</th>
									<th>RD SPL Holiday OT+ND</th>
									<th>RD SPL Holiday ND</th>
									<th>RD LEGAL Holiday</th>
									<th>RD LEGAL Holiday OT</th>
									<th>RD LEGAL Holiday OT+ND</th>
									<th>RD LEGAL Holiday ND</th>																
								</tr>
							</thead>
							<tbody>
								<?php 
								//http://stackoverflow.com/questions/4483540/php-show-a-number-to-2-decimal-places
								//foreach( $summaries as $summary ):
								?>

									<td>{{ number_format((float) $summary->lates, 2, '.', '') . ' / ' . number_format((float) $summary->undertime, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->absent, 2, '.', '') }}</td>	
									<td>{{ number_format((float) $summary->paid_sick_leave, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->paid_vacation_leave, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->leave_without_pay, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->maternity_leave, 2, '.', '') }}</td>	
									<td>{{ number_format((float) $summary->paternity_leave, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->regular, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->regular_overtime, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->regular_overtime_night_diff, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->regular_night_differential, 2, '.', '') }}</td>	
									<td>{{ number_format((float) $summary->rest_day, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->rest_day_overtime, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->rest_day_overtime_night_diff, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->rest_day_night_differential, 2, '.', '') }}</td>	
									<td>{{ number_format((float) $summary->special_holiday, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->special_holiday_overtime, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->special_holiday_overtime_night_diff, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->special_holiday_night_diff, 2, '.', '') }}</td>	
									<td>{{ number_format((float) $summary->legal_holiday, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->legal_holiday_overtime, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->legal_holiday_overtime_night_diff, 2, '.', '') }}</td>	
									<td>{{ number_format((float) $summary->legal_holiday_night_diff, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->rest_day_special_holiday, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->rest_day_special_holiday_overtime, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->rest_day_special_holiday_overtime_night_diff, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->rest_day_special_holiday_night_diff, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->rest_day_legal_holiday, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->rest_day_legal_holiday_overtime, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->rest_day_legal_holiday_overtime_night_diff, 2, '.', '') }}</td>
									<td>{{ number_format((float) $summary->rest_day_legal_holiday_night_diff, 2, '.', '') }}</td>						
								
								<?php //endforeach; ?>
							</tbody>
							</table>

						</div>	






					</div><!--/.panel-->
				<!--/div-->

			<!--/div--><!--/.row-->            

             

          <!--/div--><!--//#content .col-md-9-->          

        <!--/div--><!--//.row-->

      </div><!--//.page-container-->

@stop