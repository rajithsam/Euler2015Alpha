@extends('layouts.admin.default')

@section('content')

<!--div class="container"-->
<div class="row">

	<div class="col-md-3 " id="sidebar" role="navigation">			
		<ul class="list-group">
			<a href="{{ url('/admin/dashboard') }}" class="list-group-item">Dashboard</a>
			<!--li><a href="{{ url('/admin/scheduling') }}" class="list-group-item">Employee Scheduling</a></li-->
			<a href="{{ url('/admin/employee') }}" class="list-group-item active">Employee</a>
			<a href="{{ url('/admin/timeclock') }}" class="list-group-item">TimeClock &amp; Attendance</a></li>
			<a href="{{ url('/admin/hr') }}" class="list-group-item">Human Resources</a>
			<a href="{{ url('/admin/payroll') }}" class="list-group-item">Payroll</a>
		</ul>
	</div>

	<div class="col-md-9" id="content" role="main">
			
	
		<div class="panel panel-default">
		  <!-- Default panel contents -->
		  <div class="panel-heading" style="font-size:14px; font-weight:bold;">Employee<div class="pull-right"><a href="{{ url('/admin/user/new') }}" class="">Add New</a></div></div>
		  <div class="panel-body hide hidden">
		    <p>...</p>
		  </div>

			<table class="table table-striped table-hover display list-table users" cellspacing="0" width="100%">
				<thead>
					<tr>
						<!--th>#</th-->		           				           		
		           		<th>Employee #</th>		           				           		
		           		<th>Full Name</th>                        
		           		<th>Nick Name</th>		           	
		           		<th>Group</th>
						<!--th>Date</th-->		           		                        							
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
                <?php foreach( $getUserEmployee as $userEmployee ): ?>
	
					<tr>
						<!--td><?php //echo $userEmployee->id; ?></td-->	
		           		<td><?php echo $userEmployee->employee_number; ?></td>		           				           		
		           		<td><?php echo $userEmployee->firstname.' '.$userEmployee->middle_name.', '.$userEmployee->lastname; ?></td>                        
		           		<td><?php echo $userEmployee->nick_name; ?></td>		           	
						<td><?php echo $userEmployee->name; ?></td>	
						<!--td--><?php //echo $userEmployee->created_at; ?><!--/td-->	

						<td><a href="{{ URL::to('/admin/user/' . $userEmployee->employee_id . '/edit/') }}">Edit</a> | <a href="{{ URL::to('/admin/user/' . $userEmployee->employee_id . '/delete/') }}">Delete</a></td>						
					</tr>
						
				<?php endforeach; ?>
                </tbody>
				<tfoot class="hide hidden">
					<tr>
						<!--th>#</th-->		           				           		
		           		<th>Employee #</th>		           				           		
		           		<th>Full Name</th>                        
		           		<th>Nick Name</th>		           	
		           		<th>Group</th>
						<!--th>Date</th-->	
						<th>Actions</th>							           		                        	
					</tr>
				</tfoot>
			</table>

		</div>			

	</div>

</div>	
<!--/div-->

@stop