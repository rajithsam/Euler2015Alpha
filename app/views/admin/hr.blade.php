@extends('layouts.admin.default')

@section('content')
<?php 

	$employeeId = Session::get('userEmployeeId');
	
	//$leaves = Leave::where('status', '=', -1)->paginate(15);
	//Admininstrator Level
	//$leaves = Leave::paginate(15);

	
	//Manager and Supervisor Level
	$employee = DB::table('employees')->where('id', $employeeId)->get();

	$company = DB::table('employees')->where('id', $employee[0]->company_id)->get(); 
	$manager = DB::table('employees')->where('id', $employee[0]->manager_id)->get(); 
	$jobTitle = DB::table('job_title')->where('id', $employee[0]->position_id)->get(); 
	$department = DB::table('departments')->where('id', $employee[0]->department_id)->get(); 

	$employeesByManager = DB::table('employees')->where('manager_id', $employeeId)->get();
?>
<div class="page-container">

        <div class="row" style="padding-bottom:20px;">

          <div class="col-md-3 clearfix">

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
                      <a href="{{ url('/admin/hr') }}" class="active">
                      <span class="sidebar-nav-item-icon fa fa-users fa-lg"></span>
                      <span class="sidebar-nav-item">Human Resources</span>
                      </a>
                      <ul class="submenu-1 collapse in">
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

          <div id="content" class="col-md-9" role="main">

            <ol class="breadcrumb hide hidden">
              <li><a href="#">Home</a></li>
              <li class="active">Page</li>
            </ol>

            <h1 class="page-header">Human Resources</h1>

            <div class="panel panel-default">
              <!-- Default panel contents -->
              <div class="panel-heading" style="font-size:14px; font-weight:bold;">Leave Credits</div>
              <div class="panel-body">                
                <p class="hide hidden">...</p>
              </div>        

						<table class="table table-striped table-hover table-list display">
						<thead>
							<tr>
								<th>#</th>
								<th>Name</th>
								<th>Leave Credits</th>								
								<!--th>Leave Applied</th-->				
								<th>Leave Balance</th>												
							</tr>
						</thead>
						<tbody>
							<?php
							$leaveEntitlement = 1;
							
							foreach($employeesByManager as $employee) :

								$leaveSetting = DB::table('leave_setting')->where('employee_id', $employee->id)->where('leave_entitlement', $leaveEntitlement)->get();
								
							?>

								<?php
								if ( !empty($leaveSetting) ) : 

									$leaveCredits = (int) $leaveSetting[0]->leave_credits;
									
									$leaveApplied = DB::table('leave')->where('employee_id', $leaveSetting[0]->employee_id)->count(); 									

								?>

									<?php if ( !empty($leave) ) : ?>

									<?php //$leaveBalance = $leaveCredits - (int) $leaveApplied; ?>
									
									<?php endif; ?>																	
								
									<tr>
										<td><?php echo $leaveSetting[0]->employee_id ?></td>
										<td><?php echo $employee->firstname.', '.$employee->lastname; ?></td>
										<td><?php echo $leaveSetting[0]->leave_credits; ?></td>
										<!--td><?php //echo $leaveApplied; ?></td-->
										<td><?php echo $leaveSetting[0]->leave_balance; ?></td>
										
									</tr>
								
								<?php endif; ?>
							
							<?php endforeach; ?>

		                </tbody>
					</table>

	            {{ Form::close() }}  				

				<?php //echo $leaves->links(); ?>               
                
            </div>			



            <div class="panel panel-default">
              <!-- Default panel contents -->
              <div class="panel-heading" style="font-size:14px; font-weight:bold;">Applied Leaves</div>
              <div class="panel-body">                
                <p class="hide hidden">...</p>
              </div>        


	            {{ Form::open(array('url' => '/admin/hr', 'id' => 'humanResourceForm', 'class' => 'form-horizontal')) }}                
	            {{-- Form::hidden('employeeid', $employeeId); --}}


					<select name="action" id="bulk-action-selector-top">
					<option value="-1" selected="selected">Bulk Actions</option>
					<option value="0">Denied</option>
					<option value="1">Approved</option>
					</select>
					<input type="submit" name="" id="doaction" class="button action" value="Apply">	

					<table class="table table-striped table-hover display table-list" cellspacing="0" width="100%">
						<thead>
							<tr>
								<!--input id="cb-select-all-1" type="checkbox"-->
								<th id="cb" class="manage-column column-cb check-column">							
									{{ Form::checkbox('', '', '', array('id' => 'cb-select-all-1')) }}
								</th>
								<th>Leave ID</th>	      
								<th>Nature of Leave</th>	           				           		
								<th>Reason</th>
				           		<th>With Pay</th>
				           		<th>Date</th>								
								<th>Status</th>
								
							</tr>
						</thead>
						<tbody>
							
		                <?php foreach($employeesByManager as $employee) :
						
							$leaves = DB::table('leave')->where('employee_id', $employee->id)->get();

							if ( !empty($leaves) ) {
								foreach ( $leaves as $leave ) {

									//var_dump($leave->id);	
							?>		
								<tr>
									<!--input id="cb-select-8" type="checkbox" name="post[]" value="8"-->							
									<td class="check-column">
										{{ Form::checkbox('check[]', $leave->id, false, array('id' => 'cb-select-'.$leave->id, 'class' => 'checkbox')) }}
									</td>
									<td><?php echo $leave->id; ?></td>		           				           	
			           				      
					           		<td>
					           		<?php 

					           		if ( !empty($leave->nature_of_leave) ) {

					           			echo $leave->nature_of_leave;

					           		} else {

										echo $leave->other_nature_of_leave;				           			

					           		}

					           		?>
					           		</td>           

					           		<td><?php echo $leave->reason; ?></td>
									
		           					<td><?php echo $leave->with_pay; ?></td>

		           					<td><?php echo date('D, M d', strtotime($leave->from_date)) .' - '. date('D, M d', strtotime($leave->to_date)); ?></td>


									<td>
										<?php
										if ( $leave->status === -1 ):
											echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Pending</span>';
										elseif ( $leave->status === 0 ):
											echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Denied</span>';
										elseif ( $leave->status === 1 ):
											echo '<span class="label label-success" style="padding: 2px 4px; font-size: 11px;">Approved</span>';
										endif;
										?>							
									</td>
										
									
								</tr>									

							<?php	
								}
							}
							?>
		
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

				<?php //echo $leaves->links(); ?>               

            </div>  

		</div><!--//#content .col-md-9-->          

        </div><!--//.row-->

      </div><!--//.page-container-->

@stop