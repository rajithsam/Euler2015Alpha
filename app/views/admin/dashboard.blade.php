@extends('layouts.admin.default')

@section('content')
<?php 

$employeeId = Session::get('userEmployeeId');	
$userId = Session::get('userId');

$dayOfTheWeek = date('l');
$currentDate = date('Y-m-d');
$shift = 1;

//CHECK USER GROUP
$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

if( !empty($userGroups) ) {

  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

}


if( !empty($groups) ) {

	if ( strcmp(strtolower($groups->name), strtolower('Administrator')) !== 0 ||
		 strcmp(strtolower($groups->name), strtolower('Human Resources')) !== 0 ) {

		//return View::make('admin.hr', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);
		$group['dashboard']['view'][0] = true;

	}if ( strcmp(strtolower($groups->name), strtolower('Manager')) !== 0 ||
		 strcmp(strtolower($groups->name), strtolower('Supervisor')) !== 0 ) {

		$group['dashboard']['view'][1] = true;

	} /*else {

		echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

	}*/

}	

$currentUser = Sentry::getUser();	

$employee = DB::table('employees')->where('id', $employeeId)->get();

$company = DB::table('employees')->where('id', $employee[0]->company_id)->get(); 
$manager = DB::table('employees')->where('id', $employee[0]->manager_id)->get(); 
$jobTitle = DB::table('job_title')->where('id', $employee[0]->position_id)->get(); 
$department = DB::table('departments')->where('id', $employee[0]->department_id)->get(); 

$employeesByManager = DB::table('employees')->where('manager_id', $employeeId)  ->orWhere('supervisor_id', $employeeId)->get();

//$getSchedule = DB::table('employee_schedule')->where('employee_id', $employee->id)->where('schedule_date', trim($currentDate))->get();
//$getWorkShiftByDayOfTheWeek = DB::table('work_shift')->where('employee_id',  $employee->id)->where('name_of_day', $dayOfTheWeek)->where('shift', $shift)->get();

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

            <h1 class="page-header">Dashboard</h1>

         
			
				<?php if ( $group['dashboard']['view'][0] ) { ?>
				<div class="well">
					<a href="{{ url('/admin/user/new') }}"  class="btn btn-custom-default">Add New Employees</a>
				</div>
				
				<div class="well">
			

	            	<div class="row" style="margin-bottom:20px;">

						<div class="col-md-3"><a href="{{ url('/admin/company/new') }}" class="btn btn-custom-default btn-block">Add Companies</a></div>
						<div class="col-md-3"><a href="{{ url('/admin/department/new') }}" class="btn btn-custom-default btn-block">Add Departments</a></div>
						<div class="col-md-3"><a href="{{ url('/admin/jobtitle/new') }}" class="btn btn-custom-default btn-block">Add Job Titles</a></div>
						<div class="col-md-3"><a href="{{ action('GroupController@index') }}" class="btn btn-custom-default btn-block">Add Groups</a></div>					

	 				</div><!--/.row-->

	            	<div class="row" style="margin-bottom:20px;">

						<div class="col-md-3"><a href="#" class="btn btn-custom-default btn-block">Create Holiday</a></div>
						<div class="col-md-3"><a href="#" class="btn btn-custom-default btn-block">Configure Cutoffs</a></div>

	 				</div><!--/.row-->
       
				</div><!--/.well-->   				 				
					

				<?php } elseif ( $group['dashboard']['view'][1] ) { ?>

				<div class="well">
					<a href="{{ url('/admin/user/new') }}"  class="btn btn-custom-default btn-block">Add New</a>
				</div>

	            <div class="row">
		        
		            <div class="col-md-12" style="margin-bottom:20px;">	
						<a href="{{ url('/admin/user/new') }}" class="btn btn-custom-default">Add New Employees</a>
					</div>
	 			
	 			</div><!--/.row-->				

				<div class="row">

					<div class="col-md-6">			
						<div class="panel panel-default">
						  <!-- Default panel contents -->
						  <div class="panel-heading" style="font-size:14px; font-weight:bold;">Today Schedule</div>
						  <div class="panel-body">
						    
							<table class="table table-striped table-hover table-list display">
							<thead>
								<tr>
									<th>Name</th>
									<th>Schedule</th>
									<th>Status</th>
									<th>Action</th>								
								</tr>
							</thead>
							<tbody>
								
							<?php
							$clockingStatus = '';
							foreach($employeesByManager as $employee): 

								$getSchedule = DB::table('employee_schedule')->where('employee_id', $employee->id)->where('schedule_date', trim($currentDate))->get();
								$getWorkShiftByDayOfTheWeek = DB::table('work_shift')->where('employee_id',  $employee->id)->where('name_of_day', $dayOfTheWeek)->where('shift', $shift)->get();

								$getTimeSheet = DB::table('employee_timesheet')->where('employee_id', $employee->id)->where('daydate', trim($currentDate))->get();
							
								/*echo $getTimeSheet[0]->id;
								echo '<br />';
								echo $getTimeSheet[0]->daydate;*/

								if( !empty($getTimeSheet) ) {

									//var_dump($getTimeSheet[0]->clocking_status);

									if( in_array($getTimeSheet[0]->clocking_status, array('clock_in_1', 'clock_in_2')) ) {
										
										$clockingStatus = '<span class="label label-success" style="padding: 2px 13px; font-size: 11px;">in</span>';

									} elseif( $getTimeSheet[0]->clocking_status === 'clock_in_3' ) {

										$clockingStatus = '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">in</span>';

									} else {

										$clockingStatus = '<span class="label label-default" style="padding: 2px 4px; font-size: 11px;">open</span>';

									}

								}
							?>										
								<?php if ( !empty($getSchedule) ) { ?>
									
									<?php
									$scheduled['start_time'] = $getSchedule[0]->start_time;
									$scheduled['end_time'] = $getSchedule[0]->end_time;			
									?>
									<tr>
									<td><?php  echo $employee->firstname.', '.$employee->lastname; ?></td>
									<td><?php  echo $scheduled['start_time'].' - '.$scheduled['end_time']; ?></td>
									<td><?php  echo $clockingStatus; ?></td>
									<td><a href="{{ URL::to('/admin/user/' . $employee->id . '/edit/') }}">Edit</a> | <a href="{{ URL::to('/admin/user/' . $employee->id . '/delete/') }}">Delete</a>		</td>								
									</tr>
								
								<?php }elseif( !empty($getWorkShiftByDayOfTheWeek) ) { ?>

									<?php
									$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek[0]->start_time;
									$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek[0]->end_time;		
									?>
									<tr>
									<td>
										<?php  echo $employee->firstname.', '.$employee->lastname; ?>
									</td>
									<td><?php  echo $scheduled['start_time'].' - '.$scheduled['end_time']; ?></td>
									<td><?php  echo $clockingStatus; ?></td>
									<td><a href="{{ URL::to('/admin/user/' . $employee->id . '/edit/') }}">Edit</a> | <a href="{{ URL::to('/admin/user/' . $employee->id . '/delete/') }}">Delete</a>		</td>
									</tr>

								<?php } else { ?>
									<tr>
									<td><?php echo $employee->firstname.', '.$employee->lastname; ?></td>
									<td>&nbsp;</td>
									<td><?php echo $clockingStatus; ?></td>
									<td><a href="{{ URL::to('/admin/user/' . $employee->id . '/edit/') }}">Edit</a> | <a href="{{ URL::to('/admin/user/' . $employee->id . '/delete/') }}">Delete</a>		</td>
									</tr>

								<?php } ?>
								

							<?php endforeach; ?>
								
							</tbody>
							</table>					    


						  </div><!--/.pane-body-->        
						</div><!--/.panel-->
					</div>

					<div class="col-md-6">			
						<div class="panel panel-default">
					  <!-- Default panel contents -->
					  <div class="panel-heading" style="font-size:14px; font-weight:bold;">Today Leave</div>
					  <div class="panel-body">
					    
						<table class="table table-striped table-hover table-list display">
						<thead>
							<tr>
								<th>Name</th>
								<th>Schedule</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							
						<?php
						$breakTime = 1;
						foreach($employeesByManager as $employee): 

							//Todo:employee setting for break

							$getSchedule = DB::table('employee_schedule')->where('employee_id', $employee->id)->where('schedule_date', trim($currentDate))->get();
							$getWorkShiftByDayOfTheWeek = DB::table('work_shift')->where('employee_id',  $employee->id)->where('name_of_day', $dayOfTheWeek)->where('shift', $shift)->get();

							$getTimeSheet = DB::table('employee_timesheet')->where('employee_id', $employee->id)->where('daydate', trim($currentDate))->get();

							/*echo $getTimeSheet[0]->id;
							echo '<br />';
							echo $getTimeSheet[0]->daydate;*/

							if( !empty($getTimeSheet) ) {

								//var_dump($getTimeSheet[0]->clocking_status);

								if( in_array($getTimeSheet[0]->clocking_status, array('clock_in_1', 'clock_in_2')) ) {
									
									$clockingStatus = '<span class="label label-success" style="padding: 2px 13px; font-size: 11px;">in</span>';

								} elseif( $getTimeSheet[0]->clocking_status === 'clock_in_3' ) {

									$clockingStatus = '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">in</span>';

								} else {

									$clockingStatus = '<span class="label label-default" style="padding: 2px 4px; font-size: 11px;">open</span>';

								}

							}
						?>		
										
							<?php if ( !empty($getSchedule) ) { ?>
								
								<?php
								$scheduled['start_time'] = $getSchedule[0]->start_time;
								$scheduled['end_time'] = $getSchedule[0]->end_time;			

								$interval = getDateTimeDiffInterval($scheduled['start_time'], $scheduled['end_time']);

								$days = $interval->format('%a');
								$days = (int) $days;

								if ( $days !== 0 ) {
									
									$hhToDays = ($days * 24);
									$hh = (int) $hhToDays;				

								} else {

									$hh = (int) $interval->format('%h');

								}

								$mm = (int) $interval->format('%i');
								$ss = (int) $interval->format('%s');	

								$totalHours = number_format(getTimeToDecimalHours($hh, $mm, $ss) - $breakTime, 2); //number_format($hours, 2);							
								?>

								<tr>
								<td><?php  echo $employee->firstname.', '.$employee->lastname; ?></td>
								<td><?php  echo $scheduled['start_time'].' - '.$scheduled['end_time']; ?></td>
								<td><?php  echo $clockingStatus; ?></td>
								</tr>
							
							<?php }elseif( !empty($getWorkShiftByDayOfTheWeek) ) { ?>

								<?php
								$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek[0]->start_time;
								$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek[0]->end_time;

								$interval = getDateTimeDiffInterval($scheduled['start_time'], $scheduled['end_time']);

								$days = $interval->format('%a');
								$days = (int) $days;

								if ( $days !== 0 ) {
									
									$hhToDays = ($days * 24);
									$hh = (int) $hhToDays;				

								} else {

									$hh = (int) $interval->format('%h');

								}

								$mm = (int) $interval->format('%i');
								$ss = (int) $interval->format('%s');	

								$totalHours = number_format(getTimeToDecimalHours($hh, $mm, $ss) - $breakTime, 2); //number_format($hours, 2);							
								?>

								<tr>
								<td><?php  echo $employee->firstname.', '.$employee->lastname; ?></td>
								<td><?php  echo $scheduled['start_time'].' - '.$scheduled['end_time']; ?></td>
								<td><?php  echo $clockingStatus; ?></td>
								</tr>

							<?php } else { ?>
								<tr>
								<td><?php echo $employee->firstname.', '.$employee->lastname; ?></td>
								<td>&nbsp;</td>
								<td><?php echo $clockingStatus; ?></td>
								</tr>

							<?php } ?>
							

						<?php endforeach; ?>
							
						</tbody>
						</table>					    


					  </div><!--/.pane-body-->        
						</div><!--/.panel-->
					</div>

				</div><!--/.row--> 


				<?php } ?>

           

             

          </div><!--//#content .col-md-9-->          

        </div><!--//.row-->

      </div><!--//.page-container-->

@stop