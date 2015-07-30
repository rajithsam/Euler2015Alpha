@extends('layouts.admin.default')

@section('content')
<?php 

$employeeId = Session::get('userEmployeeId');
$dayOfTheWeek = date('l');
$currentDate = date('Y-m-d');
$shift = 1;

$employee = DB::table('employees')->where('id', $employeeId)->get();

$company = DB::table('employees')->where('id', $employee[0]->company_id)->get(); 
$manager = DB::table('employees')->where('id', $employee[0]->manager_id)->get(); 
$jobTitle = DB::table('job_title')->where('id', $employee[0]->position_id)->get(); 
$department = DB::table('departments')->where('id', $employee[0]->department_id)->get(); 

$employeesByManager = DB::table('employees')->where('manager_id', $employeeId)->get();

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

            <h1 class="page-header">Employee Scheduling</h1>

       		<div class="panel panel-default">
			  <!-- Default panel contents -->
			  <div class="panel-heading" style="font-size:14px; font-weight:bold;">Employee Scheduling <div class="pull-right"><a href="{{ url('/admin/user/new') }}" class="">Add New</a></div></div>
			  <div class="panel-body">

				<div role="tabpanel">

				<!-- Nav tabs -->
				<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#updateDefaultSchedule" aria-controls="updateDefaultSchedule" role="tab" data-toggle="tab">Update Default Schedule</a></li>
				<li role="presentation"><a href="#uploadNewScheduleUpdateUploadedSchedule" aria-controls="uploadNewScheduleUpdateUploadedSchedule" role="tab" data-toggle="tab">Upload New Schedule / Update Uploaded Schedule</a></li>
				</ul>

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="updateDefaultSchedule">

							<div class="tabpanel-container" style="padding:10px;">

								{{ Form::open(array('url' => '/admin/scheduling/search/default/schedule/')) }}

									{{ Form::label('Employee Number', 'Employee Number'); }}
									{{ Form::text('employee_number', ''); }}
									{{ Form::submit('Search', array('class' => '', 'class' => 'btn btn-primary')) }}

								{{ Form::close() }}


								<?php if( !empty($defaultSchedules) ) { ?>

								{{ Form::open(array('route' => 'editDefaultSchedule', 'id' => '', 'class' => 'form-horizontal')) }}            							
								            
								<table class="table table-striped table-hover table-list display" cellspacing="0" width="100%">

								        <thead>
								            <tr>
								                <th>Day</th>
								                <!--th>Shift</th-->                
								                <th>Rest day</th>
								                <!--th>Hours per day</th-->                
								                <th>Start time</th>
								                <th>End time</th>                                                
								            </tr>
								        </thead>

								        <tbody>            
								            	
									            <?php

									            foreach($defaultSchedules as $key => $defaultSchedule) {

									            	list($starttimehh, $starttimemm) = explode(':', $defaultSchedule->start_time);
									               	list($endtimehh, $endtimemm) = explode(':', $defaultSchedule->end_time);

									            ?>

									            <tr>
									                <td>
									                	<?php echo Form::hidden('schedule['.$key.'][defaultScheduleId]',  $defaultSchedule->id); ?>
									                	<?php echo Form::hidden('schedule['.$key.'][employeeId]',  $defaultSchedule->employee_id); ?>
									                	<?php echo Form::text('schedule['.$key.'][nameofday]', $defaultSchedule->name_of_day, array()); ?>
									                </td>
									                <!--td>Shift</td-->                
									                <td><?php echo Form::select('schedule['.$key.'][restday]', array(0 => 'No', 1 => 'Yes'), $defaultSchedule->rest_day, array()); ?></td>                
									                <!--td>Hours per day</td-->                
									                <td>
									                <?php
									                    echo Form::select(
									                             'schedule['.$key.'][starttimehh]',
									                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10', 11 => '11', 12 => '12',
									                                   13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24'
									                                  ),
									                             $starttimehh,
									                             array()
									                         );
									                ?>
									                
									                <?php
									                    echo Form::select(
									                             'schedule['.$key.'][starttimemm]',
									                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
									                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
									                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
									                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
									                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
									                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
									                                   60 => '60'),
									                             $starttimemm,
									                             array()
									                         );
									                ?>
									                </td>
									                <td>
									                <?php
									                    echo Form::select(
									                             'schedule['.$key.'][endtimehh]',
									                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10', 11 => '11', 12 => '12',
									                                   13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24'
									                                  ),
									                             $endtimehh,
									                             array()
									                         );
									                ?>
									                
									                <?php
									                    echo Form::select(
									                             'schedule['.$key.'][endtimemm]',
									                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
									                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
									                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
									                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
									                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
									                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
									                                   60 => '60'),
									                             $endtimemm,
									                             array()
									                         );
									                ?>
									                </td>                                                
									            </tr>

									            <?php } ?>
											
								        </tbody>

								    </table>

								        <div class="form-group pull-right">
								            <div class="col-md-12">
								            {{ Form::submit('Update', array('class' => '', 'class' => 'btn btn-primary')) }}          
								            </div>
								        </div> 

								    {{ Form::close() }}

								<?php } ?>							

								

							</div>

						</div><!--//#updateDefaultSchedule-->

					<div role="tabpanel" class="tab-pane" id="uploadNewScheduleUpdateUploadedSchedule">
						


							<div class="tabpanel-container" style="padding:10px;">

								<h4 style="font-family: "Times New Roman", Times, serif;">Upload New Schedule</h4>
								{{ Form::open(array('url'=>'/admin/scheduling/upload/new/schedule','files'=>true, 'method'=>'POST')) }}

								    <div class="form-group">							    	
									{{-- Form::label('file','Upload New Schedule',array('id'=> '', 'class' => 'col-sm-3 control-label')) --}}	
									{{ Form::file('file','',array('id' => '', 'class' => 'form-control')) }}    
								    </div>

								    <div class="form-group">
									{{ Form::submit('Save', array('class' => '', 'class' => 'btn btn-primary')) }}
									{{ Form::reset('Reset', array('class' => '', 'class' => 'btn btn-primary')) }}
									</div>

								{{ Form::close() }}


								<h4 style="font-family: "Times New Roman", Times, serif;">Update Uploaded Schedule</h4>

								{{ Form::open(array('url' => '/admin/scheduling/search/uploaded/schedule')) }}

									{{ Form::label('Employee Number', 'Employee Number'); }}
									{{ Form::text('employee_number', ''); }}

									{{ Form::label('Date From', 'Date From') }}
									{{ Form::text('schedule_date_from', '', array('class' => 'datepicker')) }}
									{{ Form::label('Date To', 'Date to') }}
									{{ Form::text('schedule_date_to', '', array('class' => 'datepicker')) }}

									{{ Form::submit('Search', array('class' => '', 'class' => 'btn btn-primary')) }}								

								{{ Form::close() }}


								<?php if( !empty($uploadedSchedules) ) { ?>

									
								{{ Form::open(array('route' => 'editUploadedSchedule', 'id' => '', 'class' => 'form-horizontal')) }}            							
								            
								<table class="table table-striped table-hover table-list display" cellspacing="0" width="100%">

								        <thead>
								            <tr>
								                <th>Schedule Date</th>
								                <th>Shift</th>                
								                <th>Rest day</th>
								                <!--th>Hours per day</th-->                
								                <th>Start time</th>
								                <th>Start Date</th>
								                <th>End time</th>  
								                <th>End Date</th>                                              
								            </tr>
								        </thead>

								        <tbody>            
								            	
											<?php

								            foreach($uploadedSchedules as $key => $uploadedSchedule) {

												list($startDate, $startTime) = explode(' ', $uploadedSchedule->start_time);
												list($endDate, $endTime) = explode(' ', $uploadedSchedule->end_time);

								            	list($starttimehh, $starttimemm) = explode(':', trim($startTime));
								               	list($endtimehh, $endtimemm) = explode(':', trim($endTime));							            	

								            ?>

								            <tr>
								                <td>
								                	<?php echo $uploadedSchedule->schedule_date; ?>
								                	<?php echo Form::hidden('schedule['.$key.'][uploadedScheduleId]',  $uploadedSchedule->id); ?>
								                	<?php echo Form::hidden('schedule['.$key.'][employeeId]',  $uploadedSchedule->employee_id); ?>
								                	<?php echo Form::text('schedule['.$key.'][scheduledate]', $uploadedSchedule->schedule_date, array('readonly' => 'readonly')); ?>
								                </td>
								                <!--td>Shift</td-->                
												<td><?php echo Form::select('schedule['.$key.'][shift]', array(1 => 1, 2 => 2), $uploadedSchedule->shift, array()); ?></td>                							                
								                <td><?php echo Form::select('schedule['.$key.'][restday]', array(0 => 'No', 1 => 'Yes'), $uploadedSchedule->rest_day, array()); ?></td>                
								                <!--td>Hours per day</td-->                
								                <td>
								                <?php
								                    echo Form::select(
								                             'schedule['.$key.'][starttimehh]',
								                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10', 11 => '11', 12 => '12',
								                                   13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24'
								                                  ),
								                             $starttimehh,
								                             array()
								                         );
								                ?>
								                
								                <?php
								                    echo Form::select(
								                             'schedule['.$key.'][starttimemm]',
								                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
								                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
								                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
								                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
								                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
								                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
								                                   60 => '60'),
								                             $starttimemm,
								                             array()
								                         );
								                ?>
								                </td>
												<td>							                	
								                	<?php echo Form::text('schedule['.$key.'][startdate]', $startDate, array('class' => 'datepicker')); ?>
								                </td>							                
								                <td>
								                <?php
								                    echo Form::select(
								                             'schedule['.$key.'][endtimehh]',
								                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10', 11 => '11', 12 => '12',
								                                   13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19', 20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24'
								                                  ),
								                             $endtimehh,
								                             array()
								                         );
								                ?>
								                
								                <?php
								                    echo Form::select(
								                             'schedule['.$key.'][endtimemm]',
								                             array('00' => '00', '01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10',
								                                   11 => '11', 12 => '12', 13 => '13', 14 => '14', 15 => '15', 16 => '16', 17 => '17', 18 => '18', 19 => '19',
								                                   20 => '20', 21 => '21', 22 => '22', 23 => '23', 24 => '24', 25 => '25', 26 => '26', 27 => '27', 28 => '28', 29 => '29',
								                                   30 => '30', 31 => '31', 32 => '32', 33 => '33', 34 => '34', 35 => '35', 36 => '36', 37 => '37', 38 => '38', 39 => '39',
								                                   40 => '40', 41 => '41', 42 => '42', 43 => '43', 44 => '44', 45 => '45', 46 => '46', 47 => '47', 48 => '48', 49 => '49',
								                                   50 => '50', 51 => '51', 52 => '52', 53 => '53', 54 => '54', 55 => '55', 56 => '56', 57 => '57', 58 => '58', 59 => '59',
								                                   60 => '60'),
								                             $endtimemm,
								                             array()
								                         );
								                ?>
								                </td>                                                
												<td>							                	
								                	<?php echo Form::text('schedule['.$key.'][enddate]', $endDate, array('class' => 'datepicker')); ?>
								                </td>							                
								            </tr>

								            <?php } ?>								            
											
								        </tbody>

								    </table>

								        <div class="form-group pull-right">
								            <div class="col-md-12">
								            {{ Form::submit('Update', array('class' => '', 'class' => 'btn btn-primary')) }}          
								            </div>
								        </div> 

								    {{ Form::close() }}
								            

								<?php } ?>							

								

							</div>


					</div><!--//#uploadNewScheduleUpdateUploadedSchedule-->

				</div>

			  </div>

			</div>  

          </div><!--//#content .col-md-9-->           

        </div><!--//.row-->

      </div><!--//.page-container-->

@stop