@extends('layouts.admin.default')

@section('content')

<?php 

$adminCutoff = new AdminCutoff;

$cutoff['id'] = $adminCutoff->getCutoffbyYearMonth()->id;
$cutoff['year'] = $adminCutoff->getCutoffbyYearMonth()->year;
$cutoff['month'] = $adminCutoff->getCutoffbyYearMonth()->month;
$cutoff['type'] = $adminCutoff->getCutoffbyYearMonth()->cutoff_type;
$cutoff['dateFrom'][1] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_1;
$cutoff['dateTo'][1] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_1;
$cutoff['dateFrom'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2;
$cutoff['dateTo'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2;	

echo $cutoff['month'];

$currentDate = date('Y-m-d');


if ( $cutoff['type'] === 'Monthly' ) {


} elseif ( $cutoff['type'] === 'Semi Monthly' ) {

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

		echo $currentCutoff = 1;
	
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

	//return $cutoffArr2;

	if( in_array($currentDate, $cutoffArr2) ) {

		echo $currentCutoff = 2;

	}	

}

if ( $currentCutoff === 1 ) { ////1st CutOff - e.g 11-25

	echo $cutOffDateFrom = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_1;
	echo '<br />';
	echo $cutOffDateTo = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_1;

	Session::put('cutOffDateFrom', $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_1);
	Session::put('cutOffDateTo', $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_1);

} elseif ( $currentCutoff === 2 ) { ////1st CutOff - e.g 26-10

	echo $cutOffDateFrom = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2;
	echo '<br />';	
	echo $cutOffDateTo = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2;		

	Session::put('cutOffDateFrom', $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2);
	Session::put('cutOffDateTo', $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2);	

}	


$getTeamMember = DB::table('employees')
	->where('company_id', $employeeInfo[0]->company_id) 
	->where('department_id', $employeeInfo[0]->department_id)
	->get();

foreach($getTeamMember as $teamMember) {
	
	$getPendingOvertime = DB::table('employee_timesheet')
        ->where('employee_id', '=', $teamMember->id)        
        ->orWhere(function($query)
        {
            $query->where('employee_timesheet.overtime_status_1', '=', -1)
                  ->where('employee_timesheet.overtime_status_1', '<>', 0)
          		  ->whereBetween('daydate', [Session::get('cutOffDateFrom'), Session::get('cutOffDateTo')]);
        })
		->orWhere(function($query)
        {
            $query->where('employee_timesheet.overtime_status_2', '=', -1)
                  ->where('employee_timesheet.overtime_status_2', '<>', 0)                  
          		  ->whereBetween('daydate', [Session::get('cutOffDateFrom'), Session::get('cutOffDateTo')]);

        })        
		->orWhere(function($query)
        {
            $query->where('employee_timesheet.overtime_status_3', '=', -1)
                  ->where('employee_timesheet.overtime_status_3', '<>', 0)
          		  ->whereBetween('daydate', [Session::get('cutOffDateFrom'), Session::get('cutOffDateTo')]);

        })                		
        ->get();

}
    //$employeeId = Session::get('userEmployeeId');

//$getPendingOvertime = array_merge($getPendingOvertime1, $getPendingOvertime2);

//dd($getPendingOvertime[1][1]->employee_id);
//break;
?>

<!--div class="container"-->
	<div class="row">

		<div class="col-md-3 " id="sidebar" role="navigation">			
			<ul class="list-group">
				<li><a href="{{ url('/admin/dashboard') }}" class="list-group-item">Dashboard</a></li>
				<!--li><a href="{{ url('/admin/scheduling') }}" class="list-group-item">Employee Scheduling</a></li-->
				<li><a href="{{ url('/admin/employee') }}" class="list-group-item">Employee</a></li>
				<li><a href="{{ url('/admin/timeclock') }}" class="list-group-item active">TimeClock &amp; Attendance</a></li>
				<li><a href="{{ url('/admin/hr') }}" class="list-group-item">Human Resources</a></li>
				<li><a href="{{ url('/admin/payroll') }}" class="list-group-item">Payroll</a></li>
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
							<th id="cb" class="manage-column column-cb check-column">{{ Form::checkbox('', '', '', array('id' => 'cb-select-all-1')) }}</th>
							<th>Timesheet ID</th>	           				           		
			           		<th>Employee #</th>		           				           		
			           		<th>Date</th>
			           		<th>Full Name</th>
			           		<th colspan="2">1st shift OT</th>			           		
							<th colspan="2">2nd shift OT</th>			           					           		                        
							
						</tr>
					</thead>
					<tbody>
						
	                <?php foreach($getPendingOvertime as $key => $val):

	                	$employee = DB::table('employees')->where('id', $getPendingOvertime[$key]->employee_id)->first();	
	                	$employee->firstname;

	                 ?>
		
						<tr>
							<!--input id="cb-select-8" type="checkbox" name="post[]" value="8"-->
							<td class="check-column">{{ Form::checkbox('check[]', $getPendingOvertime[$key]->id, array('id' => 'cb-select-'.$getPendingOvertime[$key]->id, 'class' => 'checkbox')) }}</td>
							<td><?php echo $getPendingOvertime[$key]->id; ?></td>		           				           	
			           		<td><?php echo $employee->employee_number; ?></td>		           				      
			           		<td><?php echo date('D, M d', strtotime($getPendingOvertime[$key]->daydate)); ?></td>		           				      
			           		<td><?php echo $employee->firstname.' '.$employee->middle_name.', '.$employee->lastname; ?></td>           
							
           					<?php if ( !empty($getPendingOvertime[$key]->overtime_status_1) ) { ?>
           					<td>{{ $getPendingOvertime[$key]->total_overtime_1 }}</td>
		           			<td>	
		           				{{-- Form::text('overtime_hours', $getPendingOvertime[$key]->total_overtime_1, array('readonly' => 'readonly')) --}}
		           				{{-- Form::select('shift_ot_1', array('' => '', 'denied' => 'Denied', 'approve' => 'Approve'), '') --}}
								{{ Form::radio('name[]', 'value', true) }}		           				
							</td>

							<?php } elseif ( !empty($getPendingOvertime[$key]->overtime_status_2) ) { ?>		   
							<td>{{ $getPendingOvertime[$key]->total_overtime_2 }}</td>							
							<td>           						
		           				{{-- Form::text('overtime_hours', $getPendingOvertime[$key]->total_overtime_1, array('readonly' => 'readonly')) --}}		           				
								{{-- Form::select('shift_ot_1', array('' => '', 'denied' => 'Denied', 'approve' => 'Approve'), '') --}}

							</td>								
							<?php } ?>
								
													
							
           					<?php if ( !empty($getPendingOvertime[$key]->overtime_status_3) ) { ?>
           					<td>{{ $getPendingOvertime[$key]->total_overtime_3 }}</td>
		           			<td>
		           				{{-- Form::text('overtime_hours', $getPendingOvertime[$key]->total_overtime_3, array('readonly' => 'readonly')) --}}
		           				{{-- Form::select('shift_ot_2', array('' => '', 'denied' => 'Denied', 'approve' => 'Approve'), '') --}}
							</td>							
							<?php } elseif ( empty($getPendingOvertime[$key]->overtime_status_3) ) { ?>		   							
           					<td>
           						&nbsp;
           						{{-- $getPendingOvertime[$key]->total_overtime_3 --}}           						
           					</td>
							<td> 
								&nbsp;          						
		           				{{-- Form::text('overtime_hours', $getPendingOvertime[$key]->total_overtime_3, array('readonly' => 'readonly')) --}}
								{{-- Form::select('shift_ot_2', array('' => '', 'denied' => 'Denied', 'approve' => 'Approve'), '', array('disabled' => 'disabled')) --}}
							</td>															
							<?php } ?>
								
							
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
							<th></th>
							<th>Timesheet ID</th>	           				           		
			           		<th>Employee #</th>		           				           		
			           		<th>Date</th>
			           		<th>Full Name</th>
			           		<th colspan="2">1st shift OT</th>			           		
							<th colspan="2">2nd shift OT</th>	           		
						</tr>
					</tfoot>
				</table>


            {{ Form::close() }}  				




              </div>
              
            </div>            
            
		</div>




	</div>	
<!--/div-->

@stop