@extends('layouts.admin.default-search')

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
//$employeeSearchInfo[0]->id

$company = Company::find($employeeSearchInfo[0]->company_id);
$department = Department::find($employeeSearchInfo[0]->department_id);
$jobTitle = JobTitle::find($employeeSearchInfo[0]->position_id);

$manager = Employee::where('id', '=', $employeeSearchInfo[0]->manager_id)->first();

if( !empty($manager) ) {

  $managerFullname = $manager->firstname.', '.$manager->lastname;

} else {

  $managerFullname = '';

}



$employees = DB::table('employees')
	->where('manager_id', $employeeSearchInfo[0]->id)
  ->orWhere('supervisor_id', $employeeSearchInfo[0]->id)
	//->where('company_id', $employeeSearchInfo[0]->company_id) 
	//->where('department_id', $employeeSearchInfo[0]->department_id)
	->get();

$employeeArr[0] = '';
if( !empty($employees) ) {

    foreach($employees as $employee) {
  	$employeeArr[$employee->id] = $employee->firstname. ', ' .$employee->lastname;

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
 
//$getTimesheetperCutoff = Timesheet::where('employee_id', $searchEmployeeId)->whereBetween('daydate', array($cutOffDateFrom, $cutOffDateTo))->paginate(5);

$getTimesheetperCutoff = Timesheet::where('employee_id', $searchEmployeeId)->paginate(15);

?>

<div class="page-container">
<?php 
  //echo $cutOffDateFrom;
  //echo $cutOffDateTo;

  /*foreach($getTimesheetperCutoff as $timesheet) {

      echo $timesheet->daydate;

  }*/


?>

        <div class="row" style="padding-bottom:20px;">

          <div class="col-md-2">
            <p style="margin:0 auto; text-align:center;"><img src="" data-src="holder.js/150x150?bg=959595&fg=dcdcdc" alt="" class="img-circle"></p>
          </div><!--//.col-md-2-->

          <div class="col-md-3">
            <p id="clock" style="margin:0 0 0 0; padding:0 0 0 0; text-align:center; line-height:1; font-size:65px; font-weight:normal;">09:00</p>
            <p style="margin:0 0 0 0; padding:0 0 0 0; text-align:center; line-height:1; font-size:16px; font-weight:normal;">Wednesday, February 24th 2015</p>
          </div><!--//.col-md-3-->

          <div class="col-md-2">                      
            <!--p style="padding-top:25px; text-align:center;"><input type="button" value="Time In" class="btn btn-custom-default" style="font-size:36px; font-weight:normal;" /></p-->

          </div>          
          <div class="col-md-5">
            <div class="pull-right">
            <!--p>Cut Off Period: Feb 11, 2015 to Feb 25, 2015</p-->
            <!--form class="form-inline search hide hidden">
              <div class="form-group">
                <label for="inputSearch" class="sr-only">Name</label>
                <input type="text" class="form-control" id="input-search" placeholder="Search by Cut off date">
              </div>
              <button type="submit" class="btn btn-custom-default"><i class="fa fa-search"></i></button>
            </form-->

                       

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
                  <?php if ( !empty($employeeSearchInfo) ) { ?>
                    {{ $employeeSearchInfo[0]->firstname }}, {{ $employeeSearchInfo[0]->lastname }}
                  <?php } ?>
                  </h3>
                  
                </div>
                <div class="panel-body hide hidden"></div>
                  <section id="designation" style="background-color:#1a1a19;">
                    <div class="row">                              
                      <div class="col-md-12">

                        <table class="table table-inline">
                          <tbody>
                          <tr>
                            <td class="first-tr-td">Employee No. <span>
                              <?php if ( !empty($employeeSearchInfo) ) { ?>
                                {{ $employeeSearchInfo[0]->employee_number }}
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
                              <span>Creatives</span></td>
                          </tr>
                          <tr>
                            <td>Manager / Supervisor:<br />
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

                          <table class="table table-inline">
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

                          <table class="table table-inline">
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

                          <table class="table table-inline">
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


                          <table class="table table-inline">
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

                          <table class="table table-inline">
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

                          <table class="table table-inline">
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

                <div class="table-body-container">
                  <div class="table-head-container"></div><!--/.table-head-container-->


            	    <!--h3 style="font-size:14px; font-weight:bold;">My Timesheet</h3-->

                  <table id="timesheet1" class="table table-striped table-hover display hide hidden" cellspacing="0" width="100%">
            				<thead>					
            					<tr style="background-color: #b32728; 	color:#dcdcdc; text-transform: uppercase;">
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

            				<tfoot class="hide hidden">
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

                  <table id="timesheet-ajax" class="table table-striped table-hover display" cellspacing="0" width="100%">
                  <thead>         
                    <tr style="background-color: #b32728;   color:#dcdcdc; text-transform: uppercase;">
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
                  <?php $page = (int) Input::get('page'); ?>
                  <?php foreach($getTimesheetperCutoff as $timesheet) : ?>        
                    <tr id="<?php echo $timesheet->id; ?>">
                      <td class="timesheet-id-<?php echo $timesheet->id; ?>"><?php echo $timesheet->id; ?></td>                 
                      <td class="timesheet-daydate-<?php echo $timesheet->id; ?>"><?php echo $timesheet->daydate; ?></td>                 
                      <td class="timesheet-schedule-<?php echo $timesheet->id; ?>"><?php echo $timesheet->schedule_in . ' - ' . $timesheet->schedule_out; ?></td>                   

                      <?php if ( ( !isset($page) && empty($page) ) || ($page === 1 || $page === 0) ) : ?>
                      <td class="edit-cell timesheet-inout1-<?php echo $timesheet->id; ?>"><?php
                      if ( !empty($timesheet->time_in_1) &&
                           !empty($timesheet->time_out_1) ) {
                            echo date('H:i', strtotime($timesheet->time_in_1)) . ' - ' . date('H:i', strtotime($timesheet->time_out_1));
                      } elseif ( !empty($timesheet->time_in_1) &&
                           empty($timesheet->time_out_1) ) {
                           echo date('H:i', strtotime($timesheet->time_in_1));
                      } elseif ( empty($timesheet->time_in_1) &&
                           !empty($timesheet->time_out_1) ) {
                           echo date('H:i', strtotime($timesheet->time_out_1));
                      } else {
                        echo ' - ';
                      }
                      ?></td> 
                      <?php else: ?>
                      <td class="timesheet-inout1-<?php echo $timesheet->id; ?>"><?php
                      if ( !empty($timesheet->time_in_1) &&
                           !empty($timesheet->time_out_1) ) {
                            echo date('H:i', strtotime($timesheet->time_in_1)) . ' - ' . date('H:i', strtotime($timesheet->time_out_1));
                      } elseif ( !empty($timesheet->time_in_1) &&
                           empty($timesheet->time_out_1) ) {
                           echo date('H:i', strtotime($timesheet->time_in_1));
                      } elseif ( empty($timesheet->time_in_1) &&
                           !empty($timesheet->time_out_1) ) {
                           echo date('H:i', strtotime($timesheet->time_out_1));
                      } else {
                        echo ' - ';
                      }
                      ?></td>                      
                      <?php endif; ?>

                      <?php if ( ( !isset($page) && empty($page) ) || ($page === 1 || $page === 0) ) : ?>
                      <td class="edit-cell timesheet-inout2-<?php echo $timesheet->id; ?>"><?php
                      if ( !empty($timesheet->time_in_2) &&
                           !empty($timesheet->time_out_2) ) {
                            echo date('H:i', strtotime($timesheet->time_in_2)) . ' - ' . date('H:i', strtotime($timesheet->time_out_2));
                      } elseif ( !empty($timesheet->time_in_2) &&
                           empty($timesheet->time_out_2) ) {
                           echo date('H:i', strtotime($timesheet->time_in_2));
                      } elseif ( empty($timesheet->time_in_2) &&
                           !empty($timesheet->time_out_2) ) {
                           echo date('H:i', strtotime($timesheet->time_out_2));
                      } else {
                        echo ' - ';
                      }
                      ?></td>
                      <?php else: ?>                      
                      <td class="timesheet-inout2-<?php echo $timesheet->id; ?>"><?php
                      if ( !empty($timesheet->time_in_2) &&
                           !empty($timesheet->time_out_2) ) {
                            echo date('H:i', strtotime($timesheet->time_in_2)) . ' - ' . date('H:i', strtotime($timesheet->time_out_2));
                      } elseif ( !empty($timesheet->time_in_2) &&
                           empty($timesheet->time_out_2) ) {
                           echo date('H:i', strtotime($timesheet->time_in_2));
                      } elseif ( empty($timesheet->time_in_2) &&
                           !empty($timesheet->time_out_2) ) {
                           echo date('H:i', strtotime($timesheet->time_out_2));
                      } else {
                        echo ' - ';
                      }
                      ?></td>                      
                      <?php endif; ?>

                      <?php if ( ( !isset($page) && empty($page) ) || ($page === 1 || $page === 0) ) : ?>
                      <td class="edit-cell timesheet-inout3-<?php echo $timesheet->id; ?>"><?php
                      if ( !empty($timesheet->time_in_3) &&
                           !empty($timesheet->time_out_3) ) {
                            echo date('H:i', strtotime($timesheet->time_in_3)) . ' - ' . date('H:i', strtotime($timesheet->time_out_3));
                      } elseif ( !empty($timesheet->time_in_3) &&
                           empty($timesheet->time_out_3) ) {
                           echo date('H:i', strtotime($timesheet->time_in_3));
                      } elseif ( empty($timesheet->time_in_3) &&
                           !empty($timesheet->time_out_3) ) {
                           echo date('H:i', strtotime($timesheet->time_out_3));
                      } else {
                        echo ' - ';
                      }
                      ?></td>
                      <?php else: ?>
                      <td class="timesheet-inout3-<?php echo $timesheet->id; ?>"><?php
                      if ( !empty($timesheet->time_in_3) &&
                           !empty($timesheet->time_out_3) ) {
                            echo date('H:i', strtotime($timesheet->time_in_3)) . ' - ' . date('H:i', strtotime($timesheet->time_out_3));
                      } elseif ( !empty($timesheet->time_in_3) &&
                           empty($timesheet->time_out_3) ) {
                           echo date('H:i', strtotime($timesheet->time_in_3));
                      } elseif ( empty($timesheet->time_in_3) &&
                           !empty($timesheet->time_out_3) ) {
                           echo date('H:i', strtotime($timesheet->time_out_3));
                      } else {
                        echo ' - ';
                      }
                      ?></td>
                      <?php endif; ?>                                                  
                      <td class="timesheet-totalhours-<?php echo $timesheet->id; ?>"><?php echo $timesheet->total_hours; ?></td>
                      <td class="timesheet-workhours-<?php echo $timesheet->id; ?>"><?php echo $timesheet->work_hours; ?></td>                 
                      <td class="timesheet-totalovertime-<?php echo $timesheet->id; ?>"><?php echo $timesheet->total_overtime; ?></td>
                      <td class="timesheet-tardiness-<?php echo $timesheet->id; ?>"><?php echo $timesheet->tardiness; ?></td>                
                      <td class="timesheet-undertime-<?php echo $timesheet->id; ?>"><?php echo $timesheet->undertime; ?></td>
                      <td class="timesheet-otstatus-<?php echo $timesheet->id; ?>"><?php echo $timesheet->overtime_status; ?></td>
                    </tr>

                  <?php endforeach; ?>

                  <tfoot class="hide hidden">
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

                    <div class="table-foot-container"></div><!--/.table-footer-container-->               

                <nav class="pull-right">{{ $getTimesheetperCutoff->appends(array('employeeid' => Input::get('employeeid')))->links() }}</nav>
                
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
                          <table class="table table-inline summary">
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
                          <table class="table table-inline summary">
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
                        <table class="table table-inline summary">
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
                        <table class="table table-inline summary">
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