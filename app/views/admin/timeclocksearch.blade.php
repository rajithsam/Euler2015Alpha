@extends('layouts.admin.default')

@section('content')

<?php 

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


	} elseif ( $cutoffConfig['cutoff_options'] === 2 ) { // 2nd cutoff overlap next month



		//http://stackoverflow.com/questions/10633879/current-date-minus-4-month
		//http://stackoverflow.com/questions/8912780/get-the-last-day-of-the-month3455634556
		//http://www.brightcherry.co.uk/scribbles/php-adding-and-subtracting-dates/
		//http://stevekostrey.com/php-dates-add-and-subtract-months-really/

		//$lastMonthDays = date('t', strtotime("-1 month"));					
		//$lastMonth = date('Y-m-d', strtotime("-". $lastMonthDays ."days"));

		//$currentDate = strtotime('-1 month' , strtotime($currentDate));
		//$currentDate = date('Y-m-d' , $$currentDate);
		
		$cutoff['dateFrom'][2] = strtotime('-1 month' , strtotime($cutoff['dateFrom'][2]));
		$cutoff['dateFrom'][2] = date('Y-m-d' , $cutoff['dateFrom'][2]);

		$cutoff['dateTo'][2] = strtotime('-1 month' , strtotime($cutoff['dateTo'][2]));
		$cutoff['dateTo'][2] = date('Y-m-d' , $cutoff['dateTo'][2]);


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

$getEmployeeIds = DB::table('employees')
	->where('manager_id', $employeeInfo[0]->id) 
	->where('company_id', $employeeInfo[0]->company_id) 
	->where('department_id', $employeeInfo[0]->department_id)
	->get();

foreach($getEmployeeIds as $employeeId) {

	$employeeIdArr[] = $employeeId->id;
	Session::put('employeeIdArr', $employeeIdArr);

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
						//->get();
						->paginate(15);

//dd($getPendingOvertime);							

/*foreach($getPendingOvertime as $ot) {
	echo $ot->employee_id;
	echo '<br>';
	echo $ot->overtime_status;
	echo '<br>';
}*/


?>
						
<!--div class="container"-->
	<div class="row">

		<div class="col-md-3 " id="sidebar" role="navigation">			
			<ul class="list-group">
				<a href="{{ url('/admin/dashboard') }}" class="list-group-item">Dashboard</a>
				<a href="{{ url('/admin/scheduling') }}" class="list-group-item">Employee Scheduling</a>
				<a href="{{ url('/admin/timeclock') }}" class="list-group-item active">TimeClock &amp; Attendance</a>
				<a href="{{ url('/admin/hr') }}" class="list-group-item">Human Resources</a>
				<a href="{{ url('/admin/payroll') }}" class="list-group-item">Payroll</a>
			</ul>

			<ul class="list-group">
				<a href="{{ url('/admin/dashboard') }}" class="list-group-item active">Settings</a>				
			</ul>			
		</div>

		<div class="col-md-9" id="content" role="main">

            <div class="panel panel-default">
              <!-- Default panel contents -->
              <div class="panel-heading" style="font-size:14px; font-weight:bold;">Overtime Application</div>
              <div class="panel-body hide hidden">
                <p>...</p>
              </div>        


            {{ Form::open(array('url' => '/admin/timeclock', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}                
            {{-- Form::hidden('employeeid', $employeeId); --}}


				<select name="action" id="bulk-action-selector-top">
				<option value="-1" selected="selected">Bulk Actions</option>
				<option value="0" class="hide-if-no-js">Denied</option>
				<option value="1">Approved</option>
				</select>
				<input type="submit" name="" id="doaction" class="button action" value="Apply">	

				<table class="table table-striped table-hover display list-table" cellspacing="0" width="100%">
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

			<?php echo $getPendingOvertime->links(); ?>            

              </div>
              
            </div>            
            
		</div>




	</div>	
<!--/div-->

@stop