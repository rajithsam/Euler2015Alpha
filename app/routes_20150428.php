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

	echo Session::put('timeclocking', $data['timeclocking']);

	$workShift = new Workshift;
	$getWorkShift = $workShift->getWorkShiftByEmployeeId(Auth::user()->employee_id);

	$todayDate = date('Y-m-d');

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($todayDate);
	
	//var_dump($employeeClocking);


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


	//Todo: check this code the logic is correct
	$getClockingDateTime = date("Y-m-d H:i:s"); //0000-00-00 00:00:00

	if(Request::ajax()) {

		if ( $data['timeclocking'] == 'in' ) {	
						
			$clockingOutDay = strtolower(date('D', strtotime($getClockingDateTime)));
			
			$workShift['start_time'] =  date( 'Y-m-d', strtotime($getClockingDateTime) ).' '.$getWorkShift[0]->start_time;
			$workShift['rest_day'] = explode(', ', $getWorkShift[0]->rest_day);												
			
			// Check yesterday clocking status
			if ( $getYesterDayDate[0]->clocking_status === 'open' ||
				$getYesterDayDate[0]->clocking_status === 'close' ||
				$getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
				$getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
				$getYesterDayDate[0]->clocking_status === 'clock_out_3' ) {				

				// Regular clocking in
				if ( $employeeClocking->clocking_status === 'open' ) { // Today clocking status is "open"				

					clockingStatusIn($clocking = 'in', $hasTodaySchedule, $getClockingDateTime,
						$status = 'open', $scheduleStartTime = $workShift['start_time'], $getWorkShift,
						$employeeClocking, $employeeSummary, $deduction);		

				}


				// Regular clocking but has second shift
				if ( $employeeClocking->clocking_status === 'clock_out_1' ) {

					clockingStatusIn($clocking = 'in', $hasTodaySchedule, $getClockingDateTime,
						$status = 'clock_out_1', $scheduleStartTime = $workShift['start_time'], $getWorkShift,
						$employeeClocking, $employeeSummary, $deduction);																

				}

				if ( $employeeClocking->clocking_status === 'clock_out_2' ) {

					clockingStatusIn($clocking = 'in', $hasTodaySchedule, $getClockingDateTime,
						$status = 'clock_out_2', $scheduleStartTime = $workShift['start_time'], $getWorkShift,
						$employeeClocking, $employeeSummary, $deduction);									

				}				
				
				// Forget to Clock out yesterday
				if ( $employeeClocking->clocking_status === 'forgot_to_clock_out' ) {

					clockingStatusIn($clocking = 'in', $hasTodaySchedule, $getClockingDateTime,
						$status = 'forgot_to_clock_out', $scheduleStartTime = $workShift['start_time'], $getWorkShift,
						$employeeClocking, $employeeSummary, $deduction);										

				}	

				//Don't Delete for checking
				//if ( $employeeClocking->clocking_status === 'clock_out_3' ) {

					//$employeeClocking->time_in_1       =   $getClockingDateTime;
					//$employeeClocking->clocking_status =   'clock_in_1';				

				//}

				//if ( $employeeClocking->save() ) {
				/*if ( $employeeClocking->save() && $employeeSummary->save() ) {
					
					return Redirect::to('/redraw/timesheet');			

				}*/

			}

			// Yesterday clock out
			if ( $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' ) {

				if ( $employeeClocking->clocking_status === 'open' ) {

					clockingStatusInYesterday($clocking = 'in', $hasTodaySchedule, $getClockingDateTime,
						$status = 'open', $scheduleStartTime = $workShift['start_time'], $getWorkShift,
						$employeeClocking, $employeeNightDiffClocking, $employeeSummary, $deduction);					

				}

				if ( $employeeClocking->clocking_status === 'clock_out_3' ) {

					clockingStatusInYesterday($clocking = 'in', $hasTodaySchedule, $getClockingDateTime,
						$status = 'clock_out_3', $scheduleStartTime = $workShift['start_time'], $getWorkShift,
						$employeeClocking, $employeeNightDiffClocking, $employeeSummary, $deduction);					

				}				
				
			}			
													
		}


		if ( $data['timeclocking'] == 'out' ) {						

			echo 'Clocking Out';				

			// Check yesterday clocking status
			if ( $getYesterDayDate[0]->clocking_status === 'open' ||
				$getYesterDayDate[0]->clocking_status === 'close' ||
				$getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
				$getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
				$getYesterDayDate[0]->clocking_status === 'clock_out_3' ) {

				// Regular clocking out
				if ( $employeeClocking->clocking_status === 'clock_in_1' ) {

					$employeeClocking->time_out_1       =   $getClockingDateTime;
					$employeeClocking->clocking_status =   'clock_out_1';					

					$clockingIn = date('H:i:s', strtotime($employeeClocking->time_in_1));
					$clockingOut = date('H:i:s', strtotime($getClockingDateTime));

					$clockingInDay = strtolower(date('D', strtotime($employeeClocking->time_in_1)));
					$clockingOutDay = strtolower(date('D', strtotime($getClockingDateTime)));

					$workShift['start_time'] = date( 'Y-m-d', strtotime($employeeClocking->time_in_1) ).' '.$getWorkShift[0]->start_time;
					$workShift['end_time'] =  date( 'Y-m-d', strtotime($getClockingDateTime) ).' '.$getWorkShift[0]->end_time;
					$workShift['rest_day'] = explode(', ', $getWorkShift[0]->rest_day);

					if( !$hasTodaySchedule ) {

						//Compare the schedule to the clocking out time

						//NO OVERTIME: for checking
						if( strtotime(date('H:i', strtotime($clockingIn))) <= strtotime(date('H:i', strtotime($workShift['start_time']))) &&
							strtotime(date('H:i', strtotime($clockingOut))) === strtotime(date('H:i', strtotime($getWorkShift[0]->end_time))) ) {																									                         	                         	
                           	
                           	//NOT A REST DAY
							if ( !in_array($clockingInDay, $workShift['rest_day']) && !in_array($clockingOutDay, $workShift['rest_day']) ) {

								$employeeClocking->total_hours_1	=	getTotalHours($clockingIn, $clockingOut);
								$employeeClocking->work_hours_1		=	getWorkHours($clockingIn, $clockingOut, $hasBreak, $breakTime);
								$employeeClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																			
								$employeeClocking->undertime_1		=	getUnderTimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time, true, $breakTime);								

								if(!empty($getHolidayByDate[0]->holiday_type)) {

									if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

										//echo 'Regular holiday';
										echo $employeeSummary->legal_holiday	=	getTotalHours($clockingIn, $clockingOut);									
										
									} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day

										//echo 'Special non-working day';
										echo $employeeSummary->special_holiday =	getTotalHours($clockingIn, $clockingOut);																		
									
									}										

								} else { //Regular Days
								
									echo 'Regular Day';
									//$employeeSummary->regular =    getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																																		

								}

							// REST DAY: RD (First 8hrs)
							} else {

								$todayTotalWorkHours = strtotime(getTimeDiff($workShift['start_time'], $workShift['end_time']));
								decimalToTimeFormat($employeeClocking->total_hours_1);

								//RULES: First 8 Hours
								if (strtotime($employeeClocking->total_hours_1) <= $todayTotalWorkHours) {

									$employeeClocking->total_hours_1	=	getTotalHours($clockingIn, $clockingOut);
									$employeeClocking->work_hours_1		=	getWorkHours($clockingIn, $clockingOut, $hasBreak, $breakTime);
									$employeeClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																			
									$employeeClocking->undertime_1		=	getUnderTimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time, true, $breakTime);

									if(!empty($getHolidayByDate[0]->holiday_type)) {

										if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

											//echo 'Regular holiday';
											$employeeSummary->rest_day_legal_holiday	=	getTotalHours($clockingIn, $clockingOut);									
											
										} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day

											//echo 'Special non-working day';
											$employeeSummary->rest_day_special_holiday	=	getTotalHours($clockingIn, $clockingOut);																		

										}	

									} else { //Regular Days

										$employeeSummary->rest_day =    getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																																		

									}									

								}

							}

						//01:00   <=  02:00 1428944400 <= 1428948000
						//08:44   >   07:00 	

						//WITH OVERTIME
						} elseif ( strtotime(date('H:i', strtotime($clockingIn))) <= strtotime(date('H:i', strtotime($workShift['start_time']))) &&
							 strtotime(date('H:i', strtotime($clockingOut))) > strtotime(date('H:i', strtotime($workShift['end_time']))) ) {

							$clockingInDateTime = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_1));
							$clockingOutDateTime = date('Y-m-d H:i:s', strtotime($getClockingDateTime));

							//Overtime
							//Todo: add a reason form
							
							$overTime = getOvertimeHours($clockingInDateTime, $clockingOutDateTime, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);

							$employeeClocking->total_hours_1  	=	getTotalHours($clockingIn, $clockingOut, $hasOvertime, $overTime);
							$employeeClocking->work_hours_1		=	getWorkHours($clockingIn, $clockingOut, $hasBreak, $breakTime);													
							$employeeClocking->undertime_1	 	=	getUnderTimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time, true, $breakTime);							

							//$workShift['rest_day'] = explode(', ', $getWorkShift[0]->rest_day);
							
							//REGULAR DAY: Reg OT
							if ( !in_array($clockingInDay, $workShift['rest_day']) && !in_array($clockingOutDay, $workShift['rest_day']) ) {

								if(!empty($getHolidayByDate[0]->holiday_type)) {

									if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) {

										echo 'Regular holiday';

										$employeeClocking->total_overtime_1 =	getOvertimeHours($clockingInDateTime, $clockingOutDateTime, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
										$employeeSummary->legal_holiday_overtime	=	getOvertimeHours($clockingInDateTime, $clockingOutDateTime, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);;																		

									} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) {

										echo 'Special non-working day';

										$employeeClocking->total_overtime_1 =	getOvertimeHours($clockingInDateTime, $clockingOutDateTime, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
										$employeeSummary->special_holiday_overtime	=	getOvertimeHours($clockingInDateTime, $clockingOutDateTime, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
									}		

								} else {

									echo 'Regular Day';
									$employeeClocking->total_overtime_1 =	getOvertimeHours($clockingInDateTime, $clockingOutDateTime, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
									$employeeSummary->regular_overtime =    getOvertimeHours($clockingInDateTime, $clockingOutDateTime, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																																										

								}

							//REST DAY: RD OT	
							} else {
							
								$todayTotalWorkHours = strtotime(getTimeDiff($workShift['start_time'], $workShift['end_time']));
								decimalToTimeFormat($employeeClocking->total_hours_1);

								//LEGAL Holiday OT/RD SPL Holiday OT
								//Exceed the first 8 hours
								if ( strtotime($employeeClocking->total_hours_1) > $todayTotalWorkHours ) {

									if(!empty($getHolidayByDate[0]->holiday_type)) {

										if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) {

											//echo 'Regular holiday';

											$employeeClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
											$employeeSummary->rest_day_legal_holiday_overtime	=	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																		

										} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) {

											//echo 'Special non-working day';

											$employeeClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
											$employeeSummary->rest_day_special_holiday_overtime	=	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										

										}

									} else {

										echo 'Regular Day';
										$employeeClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
										$employeeSummary->rest_day_overtime =    getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																																		

									}									

								}									

							}	

						} else {

							//Clocking out early/undertime
							//Todo: add a reason form

							echo 'early/undertime';

						}

					} else {

						//Code for the employee with schedule assign

					}

					if ( $employeeClocking->save() ) {
						
						$employeeSummary->save();
						return Redirect::to('/redraw/timesheet');			

					}		

				}


				// Regular clocking but has second shift
				if ( $employeeClocking->clocking_status === 'clock_in_3' ) {

					//Add a rule to this
					$employeeClocking->time_out_3       =   $getClockingDateTime;
					$employeeClocking->clocking_status =   'clock_out_3';

					//testing
					$clockingIn = date('H:i:s', strtotime($employeeClocking->time_in_3));
					$clockingOut = date('H:i:s', strtotime($getClockingDateTime));

					$clockingInDay = strtolower(date('D', strtotime($employeeClocking->time_in_3)));
					$clockingOutDay = strtolower(date('D', strtotime($getClockingDateTime)));

					$workShift['start_time'] = date( 'Y-m-d', strtotime($employeeClocking->time_in_3) ).' '.$getWorkShift[0]->start_time;
					$workShift['end_time'] =  date( 'Y-m-d', strtotime($getClockingDateTime) ).' '.$getWorkShift[0]->end_time;
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

					if(!$hasTodaySchedule) {
					
						//Compare the schedule to the clocking in time
						if( date('H:i:s', strtotime($getClockingDateTime)) === $getWorkShift[0]->end_time ) {
							
							$employeeClocking->time_out_2       =   $getClockingDateTime;
							$employeeClocking->clocking_status =   'clock_out_2';				

						} else {

							//Clocking out early/undertime
							//Todo: add a reason form
							$employeeClocking->time_out_2       =   $getClockingDateTime;
							$employeeClocking->clocking_status =   'clock_out_2';							

						}

					} else {

						//Code for the employee with schedule assign

					}


					if ( $employeeClocking->save() ) {
						
						return Redirect::to('/redraw/timesheet');			

					}													

				}	


			}


			// Initialize: Forget to Clock out yesterday or 
			if ( $getYesterDayDate[0]->clocking_status === 'clock_in_1' ) {

				if ( $employeeClocking->clocking_status === 'open' ) {				
				
					$timeOutDateTime = date('G', strtotime($getClockingDateTime));

					if( $timeOutDateTime < 17 ) { //12
						
						//echo 'yesterday time out';

						$employeeNightDiffClocking->time_out_1		= $getClockingDateTime;		
						//$employeeNightDiffClocking->night_shift_time_out = 1;			
						$employeeNightDiffClocking->clocking_status = 'yesterday_clock_out'; //'clock_out_1';

						$clockingIn = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_1));
						$clockingOut = date('Y-m-d H:i:s', strtotime($getClockingDateTime));

						/*
						echo "\n";
						echo 'Clocking Date Time';							
						echo "\n";
						echo $clockingIn = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_1));
						echo "\n";
						echo $clockingOut = date('Y-m-d H:i:s', strtotime($getClockingDateTime));
						echo "\n";
						echo strtotime(date('Y-m-d H:i', strtotime($clockingIn)));
						echo "\n";
						echo strtotime(date('Y-m-d H:i', strtotime($clockingOut)));
						echo "\n";
						*/

						$clockingInDay = strtolower(date('D', strtotime($employeeNightDiffClocking->time_in_1)));
						$clockingOutDay = strtolower(date('D', strtotime($getClockingDateTime)));

						$workShift['start_time'] = date( 'Y-m-d', strtotime($employeeNightDiffClocking->time_in_1) ).' '.$getWorkShift[0]->start_time;
						//$workShift['end_time'] =  date( 'Y-m-d', strtotime($getClockingDateTime) ).' '.$getWorkShift[0]->end_time;
						$workShift['end_time'] =  date( 'Y-m-d', strtotime('-1 day') ).' '.$getWorkShift[0]->end_time;
						$workShift['rest_day'] = explode(', ', $getWorkShift[0]->rest_day);

						$clockingInDateTime = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_1));
						$clockingOutDateTime = date('Y-m-d H:i:s', strtotime($getClockingDateTime));

						/*
						echo "\n";
						echo 'Workshift';
						echo "\n";
						echo $workShift['start_time'];
						echo "\n";
						echo $workShift['end_time'];
						echo "\n";
						echo strtotime(date('Y-m-d H:i', strtotime($workShift['start_time'])));
						echo "\n";
						echo strtotime(date('Y-m-d H:i', strtotime($workShift['end_time'])));
						echo "\n";
						*/

						if(!$hasTodaySchedule) {

							//Compare the schedule to the clocking out time

							//echo strtotime(date('H:i', strtotime($clockingOut)));
							//echo strtotime(date('H:i', strtotime($getWorkShift[0]->end_time)));

							//NO OVERTIME: for checking
							if( strtotime(date('Y-m-d H:i', strtotime($clockingIn))) <= strtotime(date('Y-m-d H:i', strtotime($workShift['start_time']))) &&
								strtotime(date('Y-m-d H:i', strtotime($clockingOut))) === strtotime(date('Y-m-d H:i', strtotime($getWorkShift[0]->end_time))) ) {
								echo 'debug.io 1';
								/*$employeeNightDiffClocking->time_out_1		= $getClockingDateTime;		
								$employeeNightDiffClocking->night_shift_time_out = 1;			
								$employeeNightDiffClocking->clocking_status = 'yesterday_clock_out'; //'clock_out_1';								

								$clockingIn = date('H:i:s', strtotime($employeeNightDiffClocking->time_in_1));
								$clockingOut = date('H:i:s', strtotime($getClockingDateTime));*/

								//NOT A REST DAY
								if ( !in_array($clockingInDay, $workShift['rest_day']) && !in_array($clockingOutDay, $workShift['rest_day']) ) {

									$employeeNightDiffClocking->total_hours_1	=	getTotalHours($clockingIn, $clockingOut, $hasOvertime, $overTime1);
									$employeeNightDiffClocking->work_hours_1		=	getWorkHours($clockingIn, $clockingOut, $hasBreak, $breakTime);
									$employeeNightDiffClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																			
									$employeeNightDiffClocking->undertime_1		=	getUnderTimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time, true, $breakTime);

									if(!empty($getHolidayByDate[0]->holiday_type)) {

										if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

											//echo 'Regular holiday';
											echo $employeeSummaryNightDiffClocking->legal_holiday	=	getTotalHours($clockingIn, $clockingOut);									
											
										} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day

											//echo 'Special non-working day';
											echo $employeeSummaryNightDiffClocking->special_holiday =	getTotalHours($clockingIn, $clockingOut);																		

										} 

									} else { //Regular Days
										
											echo 'Regular Day';
											//$employeeSummary->regular =    getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																																		

									} 

								// REST DAY: RD (First 8hrs)
								} else {

									$todayTotalWorkHours = strtotime(getTimeDiff($workShift['start_time'], $workShift['end_time']));
									decimalToTimeFormat($$employeeSummaryNightDiffClocking->total_hours_1);

									//RULES: First 8 Hours
									if (strtotime($employeeClocking->total_hours_1) <= $todayTotalWorkHours) {

										$employeeNightDiffClocking->total_hours_1	=	getTotalHours($clockingIn, $clockingOut, $hasOvertime, $overTime1);
										$employeeNightDiffClocking->work_hours_1		=	getWorkHours($clockingIn, $clockingOut, $hasBreak, $breakTime);
										$employeeNightDiffClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																			
										$employeeNightDiffClocking->undertime_1		=	getUnderTimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time, true, $breakTime);

										if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

											//echo 'Regular holiday';
											$$employeeSummaryNightDiffClocking->rest_day_legal_holiday	=	getTotalHours($clockingIn, $clockingOut);									
											
										} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day

											//echo 'Special non-working day';
											$$employeeSummaryNightDiffClocking->rest_day_special_holiday	=	getTotalHours($clockingIn, $clockingOut);																		

										} else { //Regular Days

											$$employeeSummaryNightDiffClocking->rest_day =    getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																																		

										}									

									}

								}								

							
							//01:00   <=  02:00 1428944400 <= 1428948000
							//08:44   >   07:00 

							
							//1429102800 1429102800		
							//1429102800 1429016400

							//WITH OVERTIME Double check this
							} elseif ( strtotime(date('Y-m-d H:i', strtotime($clockingIn))) <= strtotime(date('Y-m-d H:i', strtotime($workShift['start_time']))) &&
							 		   strtotime(date('Y-m-d H:i', strtotime($clockingOut))) > strtotime(date('Y-m-d H:i', strtotime($workShift['end_time']))) ) {
									
								echo 'debug.io 2';
								//echo strtotime(date('Y-m-d H:i', strtotime($clockingIn)));
								//echo strtotime(date('Y-m-d H:i', strtotime($workShift['start_time'])));

								//Clocking out early/undertime
								//Todo: add a reason form

								/*$employeeNightDiffClocking->time_out_1		= $getClockingDateTime;		
								$employeeNightDiffClocking->night_shift_time_out = 1;			
								$employeeNightDiffClocking->clocking_status = 'yesterday_clock_out'; //'clock_out_1';						

								$clockingIn = date('H:i:s', strtotime($employeeNightDiffClocking->time_in_1));
								$clockingOut = date('H:i:s', strtotime($getClockingDateTime));*/
								
								/*$employeeNightDiffClocking->total_hours_1	 =	getTotalHours($clockingIn, $clockingOut, $hasOvertime, $overTime1);
								$employeeNightDiffClocking->work_hours_1	 =	getWorkHours($clockingIn, $clockingOut, $hasBreak, $breakTime);
								$employeeNightDiffClocking->total_overtime_1 =	getOvertimeHours($employeeNightDiffClocking->time_in_1, $getClockingDateTime, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																											
								$employeeNightDiffClocking->undertime_1	 	 =	getUnderTimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time, true, $breakTime);*/

								//Overtime
								//Todo: add a reason form
								
								$clockingInDateTime = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_1));
								$clockingOutDateTime = date('Y-m-d H:i:s', strtotime($getClockingDateTime));								
								/*
								/echo "\n";
								echo $clockingInDateTime = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_1));
								echo "\n";
								echo $clockingOutDateTime = date('Y-m-d H:i:s', strtotime($getClockingDateTime));								
								echo "\n";
								
								echo "\n";
								echo $clockingIn;
								echo "\n";
								echo $clockingOut;
								echo "\n";
								*/
					
								$overTime = getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);

								$employeeNightDiffClocking->total_hours_1  	=	getTotalHours($clockingIn, $clockingOut, $hasOvertime, $overTime);
								$employeeNightDiffClocking->work_hours_1		=	getWorkHours($clockingIn, $clockingOut, $hasBreak, $breakTime);													
								$employeeNightDiffClocking->undertime_1	 	=	getUnderTimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time, true, $breakTime);							

								//$workShift['rest_day'] = explode(', ', $getWorkShift[0]->rest_day);

								//REGULAR DAY: Reg OT
								if ( !in_array($clockingInDay, $workShift['rest_day']) && !in_array($clockingOutDay, $workShift['rest_day']) ) {

									
									if(!empty($getHolidayByDate[0]->holiday_type)) {

										if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) {

											echo 'Regular holiday';

											$employeeNightDiffClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
											$employeeSummaryNightDiffClocking->legal_holiday_overtime	=	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																		

										} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) {

											echo 'Special non-working day';

											$employeeNightDiffClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
											$employeeSummaryNightDiffClocking->special_holiday_overtime	=	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										

										}

									} else {

										$employeeNightDiffClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
										$employeeSummaryNightDiffClocking->regular_overtime =    getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																																										

									}	

								//REST DAY: RD OT	
								} else {

									$todayTotalWorkHours = strtotime(getTimeDiff($workShift['start_time'], $workShift['end_time']));
									//decimalToTimeFormat($employeeNightDiffClocking->total_hours_1);

									//LEGAL Holiday OT/RD SPL Holiday OT
									//Exceed the first 8 hours
									if ( strtotime($employeeNightDiffClocking->total_hours_1) > $todayTotalWorkHours ) {

										if(!empty($getHolidayByDate[0]->holiday_type)) {
											
											if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) {

												//echo 'Regular holiday';

												$employeeNightDiffClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
												$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime	=	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																		

											} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) {

												//echo 'Special non-working day';

												$employeeNightDiffClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
												$employeeSummaryNightDiffClocking->rest_day_special_holiday_overtime	=	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										

											}

										} else {

											$employeeNightDiffClocking->total_overtime_1 =	getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																										
											$employeeSummaryNightDiffClocking->rest_day_overtime =    getOvertimeHours($clockingIn, $clockingOut, $getWorkShift[0]->start_time, $getWorkShift[0]->end_time);																																		

										}									

									}									

								}								

							} else {

								//Clocking out early/undertime
								//Todo: add a reason form
							}

						} else {

							//Code for the employee with schedule assign

						}
             
							
						if ( $employeeNightDiffClocking->save() ) {

							$employeeSummaryNightDiffClocking->save();
							return Redirect::to('/redraw/timesheet');

						}							

					} else {

						echo 'forgot to timeout yesterday';

						$employeeClocking->time_out_1				= $getClockingDateTime;
						$employeeClocking->clocking_status			= 'forgot_to_clock_out';
						$employeeNightDiffClocking->clocking_status = 'clock_out_1';
						$employeeNightDiffClocking->save();		

						if ( $employeeClocking->save() ) {
							
							return Redirect::to('/redraw/timesheet');

						}								

					}					
				}

				
			} elseif ( $getYesterDayDate[0]->clocking_status === 'clock_in_2' ) {										

				if ( $employeeClocking->clocking_status === 'open' ) {

					$employeeClocking->time_out_1				=   $getClockingDateTime;
					$employeeClocking->clocking_status			= 'forgot_to_clock_out';

					$employeeNightDiffClocking->clocking_status =   'clock_out_2';
					$employeeNightDiffClocking->save();

					if ( $employeeClocking->save() ) {
						
						return Redirect::to('/redraw/timesheet');

					}				
				}

			} elseif ( $getYesterDayDate[0]->clocking_status === 'clock_in_3' ) {														

				if ( $employeeClocking->clocking_status === 'open' ) {
				
					$employeeClocking->time_out_1				=   $getClockingDateTime;
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

				if ( $employeeClocking->clocking_status === 'clock_in_3' ) {						

					$employeeClocking->time_out_3				=   $getClockingDateTime;
					$employeeClocking->clocking_status			= 'clock_out_3';

					if ( $employeeClocking->save() ) {
						
						return Redirect::to('/redraw/timesheet');

					}					

				}

			}

		}

	}

}));

//Route::post('/employee/overtimestatus/{timesheetId}'
Route::post('/employee/overtimestatus/{timesheetId}', array('as' => 'redrawOvertimeStatus', 'uses' => function()
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

}));

Route::get( '/redraw/timesheet', array('as' => 'redrawTimesheet', 'uses' => 'EmployeesController@redrawEmployeeTimesheet') );

Route::get( '/redraw/summary', array('as' => 'redrawSummary', 'uses' => 'EmployeesController@redrawEmployeeSummary') );


