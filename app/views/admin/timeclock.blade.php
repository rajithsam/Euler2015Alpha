@extends('layouts.admin.default')

@section('content')
<?php 

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

$getEmployeeIds = DB::table('employees')
	->where('manager_id', $employeeInfo[0]->id) 
	->where('company_id', $employeeInfo[0]->company_id) 
	->where('department_id', $employeeInfo[0]->department_id)
	->get();

	$employeeIdArr = array();

	if( !empty($getEmployeeIds)	) {

		foreach($getEmployeeIds as $employeeId) {

		$employeeIdArr[] = $employeeId->id;
		Session::put('employeeIdArr', $employeeIdArr);
		
	}

}

//$getPendingOvertime = Overtime::where('overtime_status', '=', -1)->whereIn('employee_id', $employeeIdArr)->get();

/*$getPendingOvertime = DB::table('employee_timesheet')
						->join('overtime', 'employee_timesheet.id', '=', 'overtime.timesheet_id')
						->where('overtime_status', '=', -1)
						->whereIn('employee_timesheet.employee_id', $employeeIdArr)
						->whereBetween('daydate', [$cutOffDateFrom, $cutOffDateTo])
						//->whereBetween('daydate', [Session::get('cutOffDateFrom'), Session::get('cutOffDateTo')])
						->get();*/


$getPendingOvertime = DB::table('employees')
						->join('employee_timesheet', 'employees.id', '=', 'employee_timesheet.employee_id')
						->join('overtime', 'employee_timesheet.id', '=', 'overtime.timesheet_id')
						->where('overtime_status', '=', -1)
						->orWhere('overtime_status', '=', 1)
						->orWhere('overtime_status', '=', 0)
						->whereIn('employee_timesheet.employee_id', $employeeIdArr)
						->whereBetween('daydate', [$cutOffDateFrom, $cutOffDateTo])
						->get();
						//->paginate(15);

//dd($getPendingOvertime);							

/*foreach($getPendingOvertime as $ot) {
	echo $ot->employee_id;
	echo '<br>';
	echo $ot->overtime_status;
	echo '<br>';
}*/



//Schedule

/*$employeeId = Session::get('userEmployeeId');
$dayOfTheWeek = date('l');
$currentDate = date('Y-m-d');
$shift = 1;

$employee = DB::table('employees')->where('id', $employeeId)->get();

$company = DB::table('employees')->where('id', $employee[0]->company_id)->get(); 
$manager = DB::table('employees')->where('id', $employee[0]->manager_id)->get(); 
$jobTitle = DB::table('job_title')->where('id', $employee[0]->position_id)->get(); 
$department = DB::table('departments')->where('id', $employee[0]->department_id)->get(); 

$employeesByManager = DB::table('employees')->where('manager_id', $employeeId)->get();

foreach($employeesByManager as $employee) {

echo $employee->id;

}*/


?>
<div class="page-container">

        <div class="row" style="padding-bottom:20px;">

          <div class="col-md-2 clearfix">

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
                    <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-users fa-lg"></span>                      
                      <span class="sidebar-nav-item">Employees</span>                      
                    </a>

                  <ul class="submenu-1 collapse">
                      <li><a href="{{ url('/admin/hr/employees') }}">All Employees</a></li>
                      <li><a href="{{ url('/admin/user/new') }}">Add New</a></li>
                  </ul>                    
                    
                  </li>                  
                  <li>
                      <a href="{{ url('/admin/scheduling') }}">
                      <span class="sidebar-nav-item-icon fa fa-calendar fa-lg"></span>
                      <span class="sidebar-nav-item">Schedule</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>                  
                  <li>
                      <a href="{{ url('/admin/timeclock') }}">
                      <span class="sidebar-nav-item-icon fa fa-clock-o fa-lg"></span>
                      <span class="sidebar-nav-item">TimeClock</span>
                      </a>
                      <!--ul class="submenu-1 collapse">
                          <li><a href="{{ url('/admin/timeclock') }}">Overtime</a></li>
                          <li><a href="{{ url('/admin/timeclock/report') }}">Overtime</a></li>
                      </ul-->
                  </li>
                  <li>
                      <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-folder-o fa-lg"></span>
                      <span class="sidebar-nav-item">Requests</span>
                      </a>
                      <!--ul class="submenu-1 collapse">
                          <li><a href="{{ url('/admin/timeclock') }}">Overtime</a></li>
                          <li><a href="{{ url('/admin/timeclock/report') }}">Overtime</a></li>
                      </ul-->
                  </li>
                  <li>
                      <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-bar-chart fa-lg"></span>
                      <span class="sidebar-nav-item">Reports</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>                 
                  <li>
                      <a href="">
                      <span class="sidebar-nav-item-icon fa fa-building fa-lg"></span>
                      <span class="sidebar-nav-item">Company</span>
                      </a>
                  </li>                  

                  <li class="hide hidden">
                      <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-cogs fa-lg"></span>
                      <span class="sidebar-nav-item">Admin</span>
                      </a>
                  </li>                                                                        
                  <li>
                      <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-cogs fa-lg"></span>
                      <span class="sidebar-nav-item">Settings</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>                                                                                          
                </ul>

        <!--a href="{{ action('PermissionController@index') }}" class="list-group-item">Permissions</a>
        <a href="{{ action('GroupController@index') }}" class="list-group-item">Group</a> 
        <a href="{{ url('/admin/payroll') }}" class="list-group-item"></a>
        <a href="{{ url('/admin/dashboard') }}" class="list-group-item active">Settings</a-->       


               </nav>

            </aside>                    

          </div><!--//.col-md-2-->

          <div id="content" class="col-md-10" role="main">

            <ol class="breadcrumb hide hidden">
              <li><a href="#">Home</a></li>
              <li class="active">Page</li>
            </ol>

            <h1 class="page-header">Time Clock & Attendance</h1>

 			  <!-- Default panel contents -->
              <div class="panel-heading" style="font-size:14px; font-weight:bold;">Overtime Application</div>
              <div class="panel-body hide hidden">
                <p>...</p>
              </div>        


	            {{ Form::open(array('url' => '/admin/timeclock', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}                
	            {{-- Form::hidden('employeeid', $employeeId); --}}

	              <div class="tablenav top">
	                <div class="actions bulk-actions">
	                  
	                  <div class="form-group">
	                    <label for="bulk-action-selector-top" class="screen-reader-text"></label>
	                    
	                    <div class="col-sm-3">
	                      <select name="action" id="bulk-action-selector-top" class="form-control">
	                        <option value="-1" selected="selected">Bulk Actions</option>
	                        <option value="0" class="hide-if-no-js">Denied</option>
	                        <option value="1">Approved</option>
	                      </select>                      
	                    </div>
	                    <input type="submit" name="" id="doaction" class="btn btn-custom-default action" value="Apply" class="pull-right"> 
	                  </div>

	                </div>            
	              </div>


				<table class="table table-striped table-hover display table-list" cellspacing="0" width="100%">
					<thead>
						<tr>
							<!--input id="cb-select-all-1" type="checkbox"-->
							<th id="cb" class="manage-column column-cb check-column">							
								{{ Form::checkbox('', '', '', array('id' => 'cb-select-all-1')) }}
							</th>
							<th>Timesheet ID</th>	           				           		
	           				           		
			           		<th>Date</th>
			           		<th>Full Name</th>
			           		<th>1st shift OT</th>			           		
							<th>2nd shift OT</th>			           					           		                        
							<th>Status</th>
							
						</tr>
					</thead>
					<tbody>
						
	                <?php foreach($getPendingOvertime as $pendingOvertime):

	                	//$employee = DB::table('employees')->where('id', $pendingOvertime->employee_id)->first();	
						//$timesheet = DB::table('employee_timesheet')->where('id', $pendingOvertime->timesheet_id)->first();		                	

	                 ?>
		
						<tr>
							<!--input id="cb-select-8" type="checkbox" name="post[]" value="8"-->							
							<td class="check-column">
								{{ Form::checkbox('check[]', $pendingOvertime->id, false, array('id' => 'cb-select-'.$pendingOvertime->id, 'class' => 'checkbox')) }}
							</td>
							<td><?php echo $pendingOvertime->timesheet_id; ?></td>		           				           	
	           				      
			           		<td><?php echo date('D, M d', strtotime($pendingOvertime->daydate)); ?></td>		           				      
			           		<td><?php echo $pendingOvertime->firstname.' '.$pendingOvertime->middle_name.', '.$pendingOvertime->lastname; ?></td>           
							
           					<td>

           					<?php if ( ( ($pendingOvertime->seq_no === 1 && $pendingOvertime->overtime_status === -1) ||
           							   ($pendingOvertime->seq_no === 1 && $pendingOvertime->overtime_status === 1) ||
           							   ($pendingOvertime->seq_no === 1 && $pendingOvertime->overtime_status === 0) ) ||
           							 ( ($pendingOvertime->seq_no === 2 && $pendingOvertime->overtime_status === -1) &&
           							   ($pendingOvertime->seq_no === 2 && $pendingOvertime->overtime_status === 1) ||
           							   ($pendingOvertime->seq_no === 2 && $pendingOvertime->overtime_status === 0) ) ) : ?>

           						<?php echo $pendingOvertime->total_overtime_1; ?>
           						<?php echo $pendingOvertime->total_overtime_2; ?>

           					<?php endif; ?>
           					</td>

							<td>

							<?php if ( ( ($pendingOvertime->seq_no === 3 && $pendingOvertime->overtime_status === -1) ||
           							   ($pendingOvertime->seq_no === 3 && $pendingOvertime->overtime_status === 1) ||
           							   ($pendingOvertime->seq_no === 3 && $pendingOvertime->overtime_status === 0) ) ) : ?>								
								
								<?php echo $pendingOvertime->total_overtime_3; ?>
							
							<?php endif; ?>								
							</td>

							<td>
								<?php
								if ( $pendingOvertime->overtime_status === -1 ):
									echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Pending</span>';
								elseif ( $pendingOvertime->overtime_status === 0 ):
									echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Denied</span>';
								elseif ( $pendingOvertime->overtime_status === 1 ):
									echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Approved</span>';
								endif;
								?>							
							</td>
								
							
						</tr>
	
					<?php endforeach; ?>
				
	                </tbody>
					<tfoot class="hide hidden">
						<!--tr>
							<div class="clearfix pull-right">                        
		                	{{ Form::submit('Approve', array('class' => '', 'class' => 'btn btn-primary')) }}
		                	</div>                                        
						</tr-->	
					
						<tr>
							<!--input id="cb-select-all-1" type="checkbox"-->
							<th id="cb" class="manage-column column-cb check-column">{{ Form::checkbox('', '', '', array('id' => 'cb-select-all-2')) }}</th>
							<th>Timesheet ID</th>	           				           		
	           				           		
			           		<th>Date</th>
			           		<th>Full Name</th>
			           		<th>1st shift OT</th>			           		
							<th>2nd shift OT</th>
							<th>Status</th>
						</tr>
					</tfoot>
				</table>

            	{{ Form::close() }}  				

              </div>
              
            </div>

          </div><!--//#content .col-md-9-->           

        </div><!--//.row-->

      </div><!--//.page-container-->

@stop