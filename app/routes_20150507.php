<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
date_default_timezone_set('Asia/Manila');

Route::get('/', function()
{
	/*
	$uri = Request::path();
	if('/' === $uri) {
		return Redirect::to('login');
	}
	*/
	return Redirect::to('users/login');

});

// Show the login form
Route::get('/users/login', array('as' => 'usersLogin', 'uses' => 'UsersController@showLogin'));
// Process the login form
Route::post('/users/login', array('as' => 'processLogin', 'uses' => 'UsersController@doLogin'));
// Logging Out
Route::get('users/logout', array('uses' => 'UsersController@doLogout'));

Route::get('/employee/servertime', array('before' => 'auth', 'as' => 'updateServerTime', 'uses' => 'EmployeesController@updateServerTime') );
Route::get('/employee/serverdatetime', array('before' => 'auth', 'as' => 'getServerDateTime', 'uses' => 'EmployeesController@getServerDateTime') );
Route::get('/employee/clocking', array('before' => 'auth', 'as' => 'employeeTimesheet', 'uses' => 'EmployeesController@showEmployeeTimesheet'));

Route::post('/employee/clocking', array('as' => 'timeClocking', 'uses' => function(){

	$data = Input::all();

	//echo Session::put('timeclocking', $data['timeclocking']);

	$workShift = new Workshift;
	$getWorkShift = $workShift->getWorkShiftByEmployeeId(Auth::user()->employee_id);

	$todayDate = date('Y-m-d');

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($todayDate);
		
	//$employeeId = Auth::user()->employee_id;
	//$workShift = DB::table('work_shift')->where('employee_id', $employeeId)->get();
	
	$timesheet = new Timesheet;			
	$getTimesheetById = $timesheet->getTimesheetById(Auth::user()->employee_id, date('Y-m-d'));		

	$schedule = new Schedule;
	$hasSchedule = $schedule->checkSchedule(Auth::user()->employee_id, date('Y-m-d'));		
	$getSchedule = $schedule->getSchedule(Auth::user()->employee_id, date('Y-m-d'));

	//Deduction Model
	$deduction = new Deduction;

	$hasNightShiftStartTimeThreshold = true;
	$nightShiftStartTimeThreshold = 5; //It should be bigger;

	//get schedule
	//Check if there is assign schedule today
	//if ( $hasSchedule && strtotime($getSchedule[0]->start_time) !== strtotime('00:00:00')) {
	if ( $hasSchedule && strtotime($getSchedule[0]->start_time) !== '' ) {
		
		$hasTodaySchedule = true;

	} else { //check workShift table default schedule if no schedule in the schedule table

		$hasTodaySchedule = false;

	}

	//Computation Setting
	$hasBreak		= false; 
	$breakTime		= 1;
	$hasOvertime	= true;
	$overTime1       = '02:00:00';
	$overTime2       = '01:00:00';

	$nightDiff['startTime'] = '22:00:00'; //10PM or 22:00 hrs
	$nightDiff['endTime'] = '06:00:00'; //6AM or 6:00 hrs

	//Find the employee timesheet record for this day
	$employeeClocking = Timesheet::where('employee_id', '=', $data['employeeno'])->where('daydate', '=', date('Y-m-d'))->first();

	//Todo: Simplify and refactoring the code
	$otherDayDate = date( "Y-m-d", strtotime('-2 days') );
	$getOtherDayDate = DB::table('employee_timesheet')->where('employee_id', $data['employeeno'])->where('daydate', $otherDayDate)->get();										

	$yesterDayDate = date( "Y-m-d", strtotime('yesterday') );
	$getYesterDayDate = DB::table('employee_timesheet')->where('employee_id', $data['employeeno'])->where('daydate', $yesterDayDate)->get();													
	
	$employeeNightDiffClocking = Timesheet::where('employee_id', '=', $data['employeeno'])->where('daydate', '=', $yesterDayDate)->first();

	//Summary
	$employeeSummary = Summary::where('employee_id', '=', $data['employeeno'])->where('daydate', '=', date('Y-m-d'))->first();		
	
	$employeeSummaryNightDiffClocking = Summary::where('employee_id', '=', $data['employeeno'])->where('daydate', '=', $yesterDayDate)->first();		
	
	//$getworkShiftYesterDayDate = DB::table('work_shift')->where('employee_id', $employeeId)->get();	


	//Get Other day clocking
	$otherday['time_in_1']     =     $getOtherDayDate[0]->time_in_1;
	$otherday['time_in_2']     =     $getOtherDayDate[0]->time_in_2;
	$otherday['time_in_3']     =     $getOtherDayDate[0]->time_in_3;

	$otherday['time_out_1']    =     $getOtherDayDate[0]->time_out_1;
	$otherday['time_out_2']    =     $getOtherDayDate[0]->time_out_2;
	$otherday['time_out_3']    =     $getOtherDayDate[0]->time_out_3;

	//Get Yesterday clocking
	$yesterday['time_in_1']    =     $getYesterDayDate[0]->time_in_1;
	$yesterday['time_in_2']    =     $getYesterDayDate[0]->time_in_2;
	$yesterday['time_in_3']    =     $getYesterDayDate[0]->time_in_3;

	$yesterday['time_out_1']   =     $getYesterDayDate[0]->time_out_1;
	$yesterday['time_out_2']   =     $getYesterDayDate[0]->time_out_2;
	$yesterday['time_out_3']   =     $getYesterDayDate[0]->time_out_3;

	//Get today Clocking
	$today['time_in_1']     =     $employeeClocking->time_in_1;
	$today['time_in_2']     =     $employeeClocking->time_in_2;
	$today['time_in_3']     =     $employeeClocking->time_in_3;

	$today['time_out_1']    =     $employeeClocking->time_out_1;
	$today['time_out_2']    =     $employeeClocking->time_out_2;
	$today['time_out_3']    =     $employeeClocking->time_out_3;

	//Get today clocking status
	$today['clocking_status'] = $employeeClocking->clocking_status;

	$currentDateTime = date("Y-m-d H:i:s"); //0000-00-00 00:00:00

	if(Request::ajax()) {

		if ( $data['timeclocking'] == 'in' ) {	
						
			//$clockingOutDay = strtolower(date('D', strtotime($currentDateTime)));
			
			//$workShift['start_time'] =  date( 'Y-m-d', strtotime($currentDateTime) ).' '.$getWorkShift[0]->start_time;
			//$workShift['rest_day'] = explode(', ', $getWorkShift[0]->rest_day);												

			
			
			// Check yesterday clocking status
			
			if ( $getYesterDayDate[0]->clocking_status === 'open' ||
				 $getYesterDayDate[0]->clocking_status === 'close' ||
				 $getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
				 $getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
				 $getYesterDayDate[0]->clocking_status === 'clock_out_3' ) {				

				/*echo $today['clocking_status'];
				echo "\n";
				echo $currentDateTime;
				echo "\n";*/
				

				echo getClockingInDateTime($today['clocking_status'], $currentDateTime);

				// Regular clocking in
				/*if ( $employeeClocking->clocking_status === 'open' ) { //Today clocking status is "open"

					echo 'Time Clocking > 1';

				}

				// Regular clocking but has second shift
				if ( $employeeClocking->clocking_status === 'clock_out_1' ) {

				}

				if ( $employeeClocking->clocking_status === 'clock_out_2' ) {

				}				
				
				// Forget to Clock out yesterday
				if ( $employeeClocking->clocking_status === 'forgot_to_clock_out' ) {

				}*/

				/*if ( $employeeClocking->save() && $employeeSummary->save() ) {
					
					return Redirect::to('/redraw/timesheet');			

				}*/

			}

			// Yesterday clock out
			if ( $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' ) {

				echo 'debug.io';
				echo getClockingInDateTimeYesterdayClockOut($today['clocking_status'], $currentDateTime);

				/*if ( $employeeClocking->clocking_status === 'open' ) {
				
					echo '// Yesterday clock in';

				}*/

				/*if ( $employeeClocking->clocking_status === 'clock_out_3' ) {

				}*/				
				
			}
													
		}


		if ( $data['timeclocking'] == 'out' ) {						

			// Check yesterday clocking status
			if ( $getYesterDayDate[0]->clocking_status === 'open' ||
				$getYesterDayDate[0]->clocking_status === 'close' ||
				$getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
				$getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
				$getYesterDayDate[0]->clocking_status === 'clock_out_3' ) {
				
				echo getClockingOutDateTime($today['clocking_status'], $currentDateTime);

				// Regular clocking out
				/*if ( $employeeClocking->clocking_status === 'clock_in_1' ) {

					$employeeClocking->time_out_1       =   $currentDateTime;
					$employeeClocking->clocking_status =   'clock_out_1';					

					$clockingIn = date('H:i:s', strtotime($employeeClocking->time_in_1));
					$clockingOut = date('H:i:s', strtotime($currentDateTime));

					$clockingInDay = strtolower(date('D', strtotime($employeeClocking->time_in_1)));
					$clockingOutDay = strtolower(date('D', strtotime($currentDateTime)));

					$workShift['start_time'] = date( 'Y-m-d', strtotime($employeeClocking->time_in_1) ).' '.$getWorkShift[0]->start_time;
					$workShift['end_time'] =  date( 'Y-m-d', strtotime($currentDateTime) ).' '.$getWorkShift[0]->end_time;
					$workShift['rest_day'] = explode(', ', $getWorkShift[0]->rest_day);

					if ( $employeeClocking->save() ) {
						
						$employeeSummary->save();
						return Redirect::to('/redraw/timesheet');			

					}		

				}


				// Regular clocking but has second shift
				if ( $employeeClocking->clocking_status === 'clock_in_3' ) {

					//Add a rule to this
					$employeeClocking->time_out_3       =   $currentDateTime;
					$employeeClocking->clocking_status =   'clock_out_3';

					//testing
					$clockingIn = date('H:i:s', strtotime($employeeClocking->time_in_3));
					$clockingOut = date('H:i:s', strtotime($currentDateTime));

					$clockingInDay = strtolower(date('D', strtotime($employeeClocking->time_in_3)));
					$clockingOutDay = strtolower(date('D', strtotime($currentDateTime)));

					$workShift['start_time'] = date( 'Y-m-d', strtotime($employeeClocking->time_in_3) ).' '.$getWorkShift[0]->start_time;
					$workShift['end_time'] =  date( 'Y-m-d', strtotime($currentDateTime) ).' '.$getWorkShift[0]->end_time;
					$workShift['rest_day'] = explode(', ', $getWorkShift[0]->rest_day);

					//testing
					$employeeClocking->total_hours_3	=	getTotalHours($clockingIn, $clockingOut);
					$employeeClocking->work_hours_3		=	getWorkHours($clockingIn, $clockingOut, $hasBreak, $breakTime);					

					if ( $employeeClocking->save() ) {
						
						return Redirect::to('/redraw/timesheet');			

					}									

				}		

				// Forget to Clock out yesterday
				if ( $employeeClocking->clocking_status === 'clock_in_2' ) {

					if ( $employeeClocking->save() ) {
						
						return Redirect::to('/redraw/timesheet');			

					}													

				}*/	

			}


			// Initialize: Forget to Clock out yesterday or 
			if ( $getYesterDayDate[0]->clocking_status === 'clock_in_1' ) {
				
				echo getClockingOutDateTimeForgotToClockOut($today['clocking_status'], $currentDateTime);

				/*if ( $employeeClocking->clocking_status === 'open' ) {				
				
										
				}*/

				
			} elseif ( $getYesterDayDate[0]->clocking_status === 'clock_in_2' ) {										

				if ( $employeeClocking->clocking_status === 'open' ) {

					$employeeClocking->time_out_1				=   $currentDateTime;
					$employeeClocking->clocking_status			= 'forgot_to_clock_out';

					$employeeNightDiffClocking->clocking_status =   'clock_out_2';
					$employeeNightDiffClocking->save();

					if ( $employeeClocking->save() ) {
						
						return Redirect::to('/redraw/timesheet');

					}				
				}

			} elseif ( $getYesterDayDate[0]->clocking_status === 'clock_in_3' ) {														

				if ( $employeeClocking->clocking_status === 'open' ) {
				
					$employeeClocking->time_out_1				=   $currentDateTime;
					$employeeClocking->clocking_status			= 'forgot_to_clock_out';
					$employeeNightDiffClocking->clocking_status =   'clock_out_3';
					$employeeNightDiffClocking->save();

					if ( $employeeClocking->save() ) {
						
						return Redirect::to('/redraw/timesheet');

					}			

				}

			}

			// Yesterday clock out
			if ( $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' ) {

				echo '// Yesterday clock out';

				/*if ( $employeeClocking->clocking_status === 'clock_in_3' ) {						

					$employeeClocking->time_out_3				=   $currentDateTime;
					$employeeClocking->clocking_status			= 'clock_out_3';

					if ( $employeeClocking->save() ) {
						
						return Redirect::to('/redraw/timesheet');

					}					

				}*/

			}

		}

	}

}));

//Route::post('/employee/overtimestatus/{timesheetId}'
/*Route::post('/employee/overtimestatus/{timesheetId}', array('as' => 'redrawOvertimeStatus', 'uses' => function()
{
	$data = Input::all();
		
	if( Request::ajax() ){		
		
		$getOvertimeStatus = Timesheet::where('id', '=', $data['timesheetId'])->first();										
				
		//var_dump($getOvertimeStatus->clocking_status);

		if ( $getOvertimeStatus->clocking_status === 'clock_out_1' ) {

			$getOvertimeStatus->overtime_status_1 = $data['otStatus'];

		} 

		if ( $getOvertimeStatus->clocking_status === 'clock_out_2' ) {

			$getOvertimeStatus->overtime_status_2 = $data['otStatus'];
			
		} 

		if ( $getOvertimeStatus->clocking_status === 'clock_out_3' ) {

			$getOvertimeStatus->overtime_status_3 = $data['otStatus'];

		}		

		if( $getOvertimeStatus->save() ) {

			return Redirect::to('/redraw/timesheet');	

		}

	}

	//var_dump($getOvertimeStatus);

}));*/

Route::get( '/redraw/timesheet', array('as' => 'redrawTimesheet', 'uses' => 'EmployeesController@redrawEmployeeTimesheet') );

Route::get( '/redraw/summary', array('as' => 'redrawSummary', 'uses' => 'EmployeesController@redrawEmployeeSummary') );


