@extends('layouts.admin.default')

@section('content')

<div class="row">


<!--
Session::put('employeesInfo', $employeeInfo);
Session::put('employeeWorkShift', $employeeWorkShift);
Session::put('dayDateArr', $cutoffArr1);
-->

<?php
	
	//echo 'ADMIN';
	//echo Session::get('debug');
	
	/*
	$currentDate = date('Y-m-d');		
	$employeeId = $employeeId;

	$schedule = new Schedule;		

	$workShift = new Workshift;

	$employeeWorkShift = $workShift->getWorkShiftByEmployeeId($employeeId);	

	foreach(Session::get('dayDateArr') as $dayDate){

		$checkSchedule = $schedule->checkSchedule($employeeId, $dayDate);
		$getSchedule = $schedule->getSchedule($employeeId, $dayDate);		

		if($checkSchedule) {

			echo date('H:i:s', strtotime($getSchedule[0]->start_time)).' - '.date('H:i:s', strtotime($getSchedule[0]->end_time));				

		} elseif(!$checkSchedule) {

			if( !empty($employeeWorkShift) ) {
				echo $employeeWorkShift[0]->start_time.' - '.$employeeWorkShift[0]->end_time;				
			} else {
				echo 'No schedule or default shift assign';
			} 

		}

	}
	*/

?>


	<div id="dateTime">datetime</div>
	<?php
		
		$employeeId = Session::get('userEmployeeId');
		$userId = Session::get('userId');

		$schedule = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', date('Y-m-d'))->get();
    	$workShift = DB::table('work_shift')->where('employee_id', $employeeId)->get();

		$forgotYesterdayTimeOut = false;		

		$nightShiftdiff['start_time'] = 22; //10PM or 22:00 hrs	
		$nightShiftdiff['end_time']   = 6; //6AM or 6:00 hrs

	?>

	<div class="col-md-12 clearfix">	
		<div class="col-md-6" style="font-size:11px;">
			@if($employeeInfo)
				<p class="hide hidden"><strong>Name:</strong> {{ $employeeInfo[0]->firstname }}, {{ $employeeInfo[0]->lastname }}, {{ $employeeInfo[0]->middle_name }}<br /><strong>Employee no:</strong> {{ $employeeInfo[0]->employee_number }}<br /><strong>Position:</strong> Web Programmer</p>
			@endif

		</div>

		<div class="col-md-6">
			<ul class="pull-right" style="list-style:none; list-style-type:none; margin:0; padding:0; border:0; border:none;">
				<li style="font-size:24px; text-align:center;"><!--00:00-->
				<?php 
					//http://php.net/manual/en/function.date.php
					//echo date('H:i A');						
				?>
				<div id="clock"></div>
				</li>	
				<li style="font-size:10px; text-align:center;"><!--Thursday, 1 January 1970-->
				<?php
					//http://php.net/manual/en/function.date.php
					echo date('l, d F Y');
				?>		
				</li>

				<?php
				$data['employeeno'] = $employeeId;

				//Find the employee timesheet record for this day
				$employeeClocking = Timesheet::where('employee_id', '=', $data['employeeno'])->where('daydate', '=', date('Y-m-d'))->first();

				//Todo: Simplify and refactoring the code
				$otherDayDate = date( "Y-m-d", strtotime('-2 days') );
				$getOtherDayDate = DB::table('employee_timesheet')->where('employee_id', $data['employeeno'])->where('daydate', $otherDayDate)->get();										

				$yesterDayDate = date( "Y-m-d", strtotime('yesterday') );
				$getYesterDayDate = DB::table('employee_timesheet')->where('employee_id', $data['employeeno'])->where('daydate', $yesterDayDate)->get();													
				
				$employeeNightDiffClocking = Timesheet::where('employee_id', '=', $data['employeeno'])->where('daydate', '=', $yesterDayDate)->first();

				//	echo $getYesterDayDate[0]->clocking_status;				

				?>			

				<li style="text-align:center;">

						{{ Form::open(array('', 'id' => 'timeClockingForm')) }}				
							<!--button id="time-in" class="btn btn-primary btn-lg" role="button">Time In</button-->													
							{{ Form::hidden('employeenumber', $employeeId, array('id' => 'employee-number')) }}							
							
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
						@if ( $getYesterDayDate[0]->clocking_status === 'open' ||
							  $getYesterDayDate[0]->clocking_status === 'close' ||
							$getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
							$getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
							$getYesterDayDate[0]->clocking_status === 'clock_out_3' ||
							$getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' )

							@if ( $employeeClocking->clocking_status === 'open' ||
							 	  $employeeClocking->clocking_status === 'forgot_to_clock_out' )
						
								{{ Form::button('Time In', array('id' => 'time-clocking-btn', 'class' => 'time-in btn btn-primary btn-lg')) }}																																											
								{{ Form::button('Time Out', array('id' => 'time-clocking-btn', 'class' => 'hide time-out btn btn-primary btn-lg')) }}																																																		

							@elseif ( $employeeClocking->clocking_status === 'clock_in_1' || 
									  $employeeClocking->clocking_status === 'clock_in_2' ||
									  $employeeClocking->clocking_status === 'clock_in_3' )

								{{ Form::button('Time In', array('id' => 'time-clocking-btn', 'class' => 'hide time-in btn btn-primary btn-lg')) }}																																											
								{{ Form::button('Time Out', array('id' => 'time-clocking-btn', 'class' => 'time-out btn btn-primary btn-lg')) }}																																																																											

							@endif

							@if ( $employeeClocking->clocking_status === 'clock_out_1' || 
								  $employeeClocking->clocking_status === 'clock_out_2' ||
								  $employeeClocking->clocking_status === 'clock_out_3' )

									{{ Form::button('Time In', array('id' => 'time-clocking-btn', 'class' => 'time-in btn btn-primary btn-lg')) }}																																											
									{{ Form::button('Time Out', array('id' => 'time-clocking-btn', 'class' => 'hide time-out btn btn-primary btn-lg')) }}																																																		

							@endif
						
						@endif

						@if ( $getYesterDayDate[0]->clocking_status === 'clock_in_1' ||
							  $getYesterDayDate[0]->clocking_status === 'clock_in_2' ||
						      $getYesterDayDate[0]->clocking_status === 'clock_in_3' ||
						      $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' )

							@if ( $employeeClocking->clocking_status === 'open' ||
							 	  $employeeClocking->clocking_status === 'forgot_to_clock_out' )
						
								{{ Form::button('Time In', array('id' => 'time-clocking-btn', 'class' => 'hide time-in btn btn-primary btn-lg')) }}																																											
								{{ Form::button('Time Out', array('id' => 'time-clocking-btn', 'class' => 'time-out btn btn-primary btn-lg')) }}																																																		

							@endif

						@elseif ( $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' )

							@if ( $employeeClocking->clocking_status === 'clock_in_3' )
						
								{{ Form::button('Time In', array('id' => 'time-clocking-btn', 'class' => 'hide time-in btn btn-primary btn-lg')) }}																																											
								{{ Form::button('Time Out', array('id' => 'time-clocking-btn', 'class' => 'time-out btn btn-primary btn-lg')) }}																																																		

							@endif								
							
						@endif

				
						{{ Form::close() }}							

				</li>								
			</ul>
		</div>

	</div>		

	<div class="col-md-2">

		<div class="panel panel-default">
		  <div class="panel-heading">
		    <h3 class="panel-title">
				@if($employeeInfo)
					<p><strong>Name:</strong> {{ $employeeInfo[0]->firstname }}, {{ $employeeInfo[0]->lastname }}, {{ $employeeInfo[0]->middle_name }}
				@endif</h3>
		    </h3>
		  </div>
			<table class="table">
				<tbody>
				<tr>
					<td>Employee No.: <strong>{{ $employeeInfo[0]->employee_number }}</strong></td>					
				</tr>
				<tr>
					<td>Designation:<br /><strong>Programmer<strong></td>					
				</tr>
				<tr>
					<td>Team:<br /><strong>Admin</strong></td>					
				</tr>
				<tr>
					<td>Manager / Supervisor:<br /><strong>Richard Lim</strong></td>
				</tr>
				<tr>
					<td>Default Shift:<br /><strong>8:00 - 17:00</strong><br /><strong>Monday - Friday</strong></td>					
				</tr>		    			
				</tbody>
			</table>
		</div>

		
	



	</div>

    <div class="col-md-10">


	    <!--h3 style="font-size:14px; font-weight:bold;">My Timesheet</h3-->

			<table id="timesheet" class="table table-striped table-hover display" cellspacing="0" width="100%">
				<thead>
					<!--tr>
						<th colspan="2" style="text-align:center;">Office Work</th>	
		           		<th colspan="9" style="text-align:center;">Attendance</th>
		           		<th colspan="2" style="text-align:center;">Deductions</th>						
					</tr-->
					<tr>
		           		<th>ID</th>		           		
		           		<th>Date</th>		           		
		           		<th>Schedule</th>		           			
		           		<th>in-out</th>
		           		<th>in-out</th>
		           		<th>in-out</th>      
		           		<th>Total Hours</th>
		           		<th>Work Hours</th>		           		
		           		<th>Total Overtime</th>
		           		<th>Tardiness</th>		           	
		           		<th>Undertime</th>
		           		<th>OT Status</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th>ID</th>	
		           		<th>Date</th>
						<th>Schedule</th>			           			
		           		<th>in-out</th>
		           		<th>in-out</th>
		           		<th>in-out</th>       		
						<th>Total Hours</th>
		           		<th>Work Hours</th>		           		
		           		<th>Total Overtime</th>
		           		<th>Tardiness</th>		           	
		           		<th>Undertime</th>		           		
		           		<th>OT Status</th>
					</tr>
				</tfoot>
			</table>

	<!--nav class="pull-right">
      <ul class="pagination">
        <li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
        <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li><a href="#" aria-label="Next"><span aria-hidden="true">»</span></a></li>
     </ul>
   	</nav-->   	

    </div>
   
    <div class="col-md-10 pull-right">

		<div class="panel panel-default">
		  <div class="panel-heading">
		    <h3 class="panel-title">Summary</h3>
		  </div>
		  <div class="panel-body">
		    <div class="row">
		    	<div class="col-md-3">
		    		<table class="table">
		    			<tbody>
		    			<tr>
		    				<td>Lates / UT</td>
		    				<td id="lates-ut"></span></td>		    				
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
		    				<td id="marternity-leave"></td>
		    			</tr>
		    			<tr>
		    				<td>Paternity Leave</td>
		    				<td id="paternity-leave"></td>
		    			</tr>

		    			</tbody>
		    		</table>
		    	</div>
		    	<div class="col-md-3">
		    		<table class="table">
		    			<tbody>
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
					<table class="table">
		    			<tbody>
		    			<tr>
		    				<td>SPL Holiday (First 8Hrs)</td>
		    				<td id="spl-holiday"></td>
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
		    				<td id="spl-holiday"></td>
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
		    				<td id="legal-holiday-ot+nd"></td>
		    			</tr>
		    			<tr>
		    				<td>LEGAL Holiday ND</td>
		    				<td id="legal-hoiday-nd"></td>
		    			</tr>

		    			</tbody>
		    		</table>

		    	</div>		    	
				<div class="col-md-3">
					<table class="table">
		    			<tbody>
		    			<tr>
		    				<td>RD SPL Holiday (First 8Hrs)</td>
		    				<td id="rd-spl-holiday"></td>
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


		  </div>
		</div>      


    </div>

</div><!--// .class -->	



@stop