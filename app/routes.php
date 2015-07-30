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

	//CHECK Users table
	if ( Schema::hasTable('users') ) {

		$userCount = User::count();

	    //CHECK User empty
		//if( !isset($users) && empty($users) ) {
		if ( $userCount === 0 ) {
								
			return Redirect::to('admin/install');

		} elseif ( $userCount >= 1 ) {

			return Redirect::to('users/login');

		}

	}

	//return Redirect::to('users/login');

});

Route::get('/admin/install', function() {

	return View::make('admin.install');

});

Route::post('/admin/install', function() {

	$data = Input::all();

	$rules = array(
				'employenumber' => 'required|alpha_num',
				'firstname' => 'required',
				'lastname' => 'required',
				'middlename' => 'required',
				'nickname' => 'required',
				'email' => 'required|unique:users|email',
				'password' => 'required|confirmed'
			);

	$validator = Validator::make($data, $rules);

	if ( $validator->fails() ) {

		$messages = $validator->messages();
	    return Redirect::to('/admin/install')->withErrors($validator)->withInput(Input::except('password'))->withInput(Input::except('password_confirmation'));

	} else {


		//TODO: check employment date
		/*$userCount = User::count();
			
		if ( $userCount >= 0 ) {		

			$number = $userCount += 1;
			$totalDigits = strlen($number);

			$currentDate = date('Y');

			if ( $totalDigits === 1 ) {

				//$digit = 'Ones';
				$zeros = '0000';
				$employeeNumber = $currentDate.$zeros.$number;	
				
			} elseif ( $totalDigits === 2 ) {

				//$digit = 'Tens';	
				$zeros = '000';
				$employeeNumber = $currentDate.$zeros.$number;		
				
			} elseif ( $totalDigits  === 3 ) {

				//$digit = 'Hundreds';
				$zeros = '00';
				$employeeNumber = $currentDate.$zeros.$number;			

			} elseif ( $totalDigits  === 4 || $totalDigits  === 5 || $totalDigits  === 6) {

				//$digit = 'thousands';
				$zeros = '0';
				$employeeNumber = $currentDate.$zeros.$number;				

			} elseif ( $totalDigits  === 7 || $totalDigits  === 8 || $totalDigits  === 9) {

				$digit = 'millions';
				$zeros = '';
				$employeeNumber = $currentDate.$zeros.$number;					

			}

		}*/		

		$employee = new Employee;
		$employee->employee_number = trim($data["employenumber"]);						
		//$employee->employee_number = trim($employeeNumber);	
		$employee->firstname = trim(ucwords($data["firstname"]));
		$employee->lastname = trim(ucwords($data["lastname"]));							
		$employee->middle_name = trim(ucwords($data["middlename"]));
		$employee->nick_name = trim(ucwords($data["nickname"]));	

		if ( $employee->save() ) {				

	 	try {

				// Create the user
				$SentryUser = Sentry::createUser(array(
				    'email'    => trim($data['email']),
				    'employee_id' => $employee->id,
				    //'employee_number' => trim($employeeNumber),
				    'employee_number' => trim($data["employenumber"]),
				    'password' => trim($data['password']),
				    'first_name' => trim(ucwords($data['firstname'])),
				    'last_name' => trim(ucwords($data['lastname'])),
				    'activated'   => true,
				));

				if($SentryUser) {

					$userId = $SentryUser->id;

					DB::table('employee_setting')
							->insert(array(
								'employee_id' => $employee->id,
								'has_overtime' => 1,
								'has_break' => 1,
								'break_time' => '01:00:00',
								'hours_per_day' => number_format(8, 2)
								
							));						

					$administrator = DB::table('groups')->where('name', 'Administrator')->first();

					if( isset($userId) && !empty($userId) ) {
						
						DB::table('users_groups')
								->insert(array(
									'user_id' => $userId, 
									'group_id' => $administrator->id //$data["role_id"]
								));	

						$Workshift = new Workshift;

						//Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday

						$nameOfDay = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');

						for ( $i = 0; $i <= 6; $i++ ) {
							
							if ( in_array('saturday', $nameOfDay) || 
								 in_array('sunday', $nameOfDay) ) {

								$restDay = 0;
								$startTime = date('H:i:s', strtotime('08:00:00'));
								$endTime = date('H:i:s', strtotime('17:00:00'));							

							} else {

								$restDay = 1;
								$startTime = '';
								$endTime = '';

							}

							DB::table('work_shift')
								->insert(array(
									'employee_id' => $employee->id,
									'name_of_day' => ucwords($nameOfDay[$i]),
									'rest_day' => $restDay,
									'start_time' => $startTime,
									'end_time' => $endTime									
							));

						}								

					}				

					//send an email or go to another page to view users crendetial
					return Redirect::to('users/login');					

				}
			
			}

			catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
			{

				$getMessages = 'Login field is required.';
				return Redirect::to('admin/install')->withErrors(array('login' => $getMessages));				

			}
			catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
			{
				$getMessages = 'Password field is required.';
				return Redirect::to('admin/install')->withErrors(array('login' => $getMessages));								
			}
			catch (Cartalyst\Sentry\Users\UserExistsException $e)
			{
				$getMessages = 'User with this login already exists.';
				return Redirect::to('admin/install')->withErrors(array('login' => $getMessages));								
			}

		}

	}

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

Route::post('/employee/clocking', array('as' => 'timeClocking', 'uses' => function() {

	$data = Input::all();
	//return dd($data);

	$currentDateTime = date("Y-m-d H:i:s"); //0000-00-00 00:00:00	
	$employeeId = Session::get('userEmployeeId');
		
	$Workshift = new Workshift;
	$timesheet = new Timesheet;			
	$emplooyeeSetting = new Setting;		
	
	//Computation Setting
	/*$hasBreak		= false; 
	$breakTime		= 1;
	$hasOvertime	= true;
	$overTime1       = '02:00:00';
	$overTime2       = '01:00:00';*/

	/*$emplooyeeSetting = new Setting;	
	$getEmployeeSettingByEmployeeId = $emplooyeeSetting->getEmployeeSettingByEmployeeId();

	return dd($getEmployeeSettingByEmployeeId); //If no setting found the result will be NULL
	break;*/	

	//Todo: Refactoring the code

	//Find the employee timesheet record for this day
 	$employeeClocking = Timesheet::where('employee_id', '=', $employeeId)
 									->where('daydate', '=', date('Y-m-d'))
 									->first();

	$otherDayDate = date( "Y-m-d", strtotime('-2 days') );
	$getOtherDayDate = DB::table('employee_timesheet')
								->where('employee_id', $employeeId)
								->where('daydate', $otherDayDate)
								->get();	

	$yesterDayDate = date( "Y-m-d", strtotime('yesterday') );
	$getYesterDayDate = DB::table('employee_timesheet')
								->where('employee_id', $employeeId)
								->where('daydate', $yesterDayDate)
								->get();	
	
	$employeeNightDiffClocking = Timesheet::where('employee_id', '=', $employeeId)
											->where('daydate', '=', $yesterDayDate)
											->first();
		
	$employeeSummary = Summary::where('employee_id', '=', $employeeId)
								->where('daydate', '=', date('Y-m-d'))
								->first();
	
	$employeeSummaryNightDiffClocking = Summary::where('employee_id', '=', $employeeId)
												->where('daydate', '=', $yesterDayDate)
												->first();	

	//Get Other day clocking
	if ( !empty($getOtherDayDate[0]) ) {

		$otherday['time_in_1'] = $getOtherDayDate[0]->time_in_1;
		$otherday['time_in_2'] = $getOtherDayDate[0]->time_in_2;
		$otherday['time_in_3'] = $getOtherDayDate[0]->time_in_3;

		$otherday['time_out_1'] = $getOtherDayDate[0]->time_out_1;
		$otherday['time_out_2'] = $getOtherDayDate[0]->time_out_2;
		$otherday['time_out_3'] = $getOtherDayDate[0]->time_out_3;

	}

	if ( !empty($getYesterDayDate[0]) ) {
	
		//Get Yesterday clocking
		$yesterday['time_in_1'] = $getYesterDayDate[0]->time_in_1;
		$yesterday['time_in_2'] = $getYesterDayDate[0]->time_in_2;
		$yesterday['time_in_3'] = $getYesterDayDate[0]->time_in_3;

		$yesterday['time_out_1'] = $getYesterDayDate[0]->time_out_1;
		$yesterday['time_out_2'] = $getYesterDayDate[0]->time_out_2;
		$yesterday['time_out_3'] = $getYesterDayDate[0]->time_out_3;

	}

	if ( !empty($employeeClocking) ) {	

		//Get today Clocking
		$today['time_in_1'] = $employeeClocking->time_in_1;
		$today['time_in_2'] = $employeeClocking->time_in_2;
		$today['time_in_3'] = $employeeClocking->time_in_3;

		$today['time_out_1'] = $employeeClocking->time_out_1;
		$today['time_out_2'] = $employeeClocking->time_out_2;
		$today['time_out_3'] = $employeeClocking->time_out_3;

		//Get today clocking status
		$today['clocking_status'] = $employeeClocking->clocking_status;

	}

	/**
	*
	* CLOCKING IN
	*
	*/

	if(Request::ajax()) {

		if ( $data['timeclocking'] == 'in' ) {	

			// Check yesterday clocking status			
			if( !empty($getYesterDayDate[0]) ) {

				if ( $getYesterDayDate[0]->clocking_status === 'open' ||
					 $getYesterDayDate[0]->clocking_status === 'close' ||
					 $getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
					 $getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
					 $getYesterDayDate[0]->clocking_status === 'clock_out_3' ) {				

					echo 'clock_out_1';

					echo getClockingInDateTime($today['clocking_status'], $currentDateTime);

					/*if( empty($employeeClocking->overtime_status_1) ) {

						//echo 'Do you want to apply for OT';

						//echo $data["resultOvertimeStatus1"];

					}*/
				
				}

				// Yesterday clock out
				if ( $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' ) {

					echo 'Yesterday clock out';
					echo getClockingInDateTimeYesterdayClockOut($today['clocking_status'], $currentDateTime);
					
				}

			} elseif ( empty($getYesterDayDate[0]) ) {

				if ( $today['clocking_status'] === 'open' ||
					$today['clocking_status'] === 'close' ||
					$today['clocking_status'] === 'clock_out_1' ||
					$today['clocking_status'] === 'clock_out_2' ||
					$today['clocking_status'] === 'clock_out_3' ) {					

					getClockingInDateTime($today['clocking_status'], $currentDateTime);

				}

			}
													
		}


		/**
		*
		* CLOCKING OUT
		*
		*/

		if ( $data['timeclocking'] == 'out' ) {								

			if( !empty($getYesterDayDate[0]) ) {

				// Check yesterday clocking status
				if ( $getYesterDayDate[0]->clocking_status === 'open' ||
					$getYesterDayDate[0]->clocking_status === 'close' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_3' ) {
						
					echo getClockingOutDateTime($today['clocking_status'], $currentDateTime);

					//echo $getYesterDayDate[0]->overtime_status;

				}

				// Initialize: Forget to Clock out yesterday or 
				if ( $getYesterDayDate[0]->clocking_status === 'clock_in_1' ) {

					if ( $today['clocking_status'] === 'open') {

						echo getClockingOutDateTimeForgotToClockOutClockIn1($today['clocking_status'], $currentDateTime, 'clock_in_1');

					}
					
				} elseif ( $getYesterDayDate[0]->clocking_status === 'clock_in_2' ) {

					echo getClockingOutDateTimeForgotToClockOutClockIn2($today['clocking_status'], $currentDateTime, 'clock_in_2');


				} elseif ( $getYesterDayDate[0]->clocking_status === 'clock_in_3' ) {			

					echo getClockingOutDateTimeForgotToClockOutClockIn3($today['clocking_status'], $currentDateTime, 'clock_in_3');											

				}

				// Yesterday clock out
				if ( $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' ) {

					getClockingOutDateTimeYesterdayClockOut($today['clocking_status'], $currentDateTime);

				}

			} elseif ( empty($getYesterDayDate[0]) ) {


				if ( $today['clocking_status'] === 'open' ||
					$today['clocking_status'] === 'close' ||
					$today['clocking_status'] === 'clock_out_1' ||
					$today['clocking_status'] === 'clock_out_2' ||
					$today['clocking_status'] === 'clock_out_3' ) {

					getClockingInDateTime($today['clocking_status'], $currentDateTime);

				}

			}

		}

		
		/**
		*
		* SEARCH
		*
		*/	

		/*if ( $data['search'] == 'search' ) {

			return 'yo';

			return $employeeId = $data['employee'];
			
			$timesheet = new Timesheet;
			$timesheetJsObjectPerMonth = $timesheet->getSearchTimesheetJsObjectPerMonth($employeeId, Session::get('dayDateArr'));	
		
			return $timesheetJsObjectPerMonth;

		}*/

		

	}

}));

Route::post('/employee/clocking', array('as' => 'timesheetAjaxTable', 'uses' => function() {

	$data = Input::all();
	//return dd($data);

	$currentDateTime = date("Y-m-d H:i:s"); //0000-00-00 00:00:00	
	$employeeId = Session::get('userEmployeeId');
		
	$Workshift = new Workshift;
	$timesheet = new Timesheet;			
	$emplooyeeSetting = new Setting;		
	
	//Computation Setting
	/*$hasBreak		= false; 
	$breakTime		= 1;
	$hasOvertime	= true;
	$overTime1       = '02:00:00';
	$overTime2       = '01:00:00';*/

	/*$emplooyeeSetting = new Setting;	
	$getEmployeeSettingByEmployeeId = $emplooyeeSetting->getEmployeeSettingByEmployeeId();

	return dd($getEmployeeSettingByEmployeeId); //If no setting found the result will be NULL
	break;*/	

	//Todo: Refactoring the code

	//Find the employee timesheet record for this day
 	$employeeClocking = Timesheet::where('employee_id', '=', $employeeId)
 									->where('daydate', '=', date('Y-m-d'))
 									->first();

	$otherDayDate = date( "Y-m-d", strtotime('-2 days') );
	$getOtherDayDate = DB::table('employee_timesheet')
								->where('employee_id', $employeeId)
								->where('daydate', $otherDayDate)
								->get();	

	$yesterDayDate = date( "Y-m-d", strtotime('yesterday') );
	$getYesterDayDate = DB::table('employee_timesheet')
								->where('employee_id', $employeeId)
								->where('daydate', $yesterDayDate)
								->get();	
	
	$employeeNightDiffClocking = Timesheet::where('employee_id', '=', $employeeId)
											->where('daydate', '=', $yesterDayDate)
											->first();
		
	$employeeSummary = Summary::where('employee_id', '=', $employeeId)
								->where('daydate', '=', date('Y-m-d'))
								->first();
	
	$employeeSummaryNightDiffClocking = Summary::where('employee_id', '=', $employeeId)
												->where('daydate', '=', $yesterDayDate)
												->first();	

	//Get Other day clocking
	if ( !empty($getOtherDayDate[0]) ) {

		$otherday['time_in_1'] = $getOtherDayDate[0]->time_in_1;
		$otherday['time_in_2'] = $getOtherDayDate[0]->time_in_2;
		$otherday['time_in_3'] = $getOtherDayDate[0]->time_in_3;

		$otherday['time_out_1'] = $getOtherDayDate[0]->time_out_1;
		$otherday['time_out_2'] = $getOtherDayDate[0]->time_out_2;
		$otherday['time_out_3'] = $getOtherDayDate[0]->time_out_3;

	}

	if ( !empty($getYesterDayDate[0]) ) {
	
		//Get Yesterday clocking
		$yesterday['time_in_1'] = $getYesterDayDate[0]->time_in_1;
		$yesterday['time_in_2'] = $getYesterDayDate[0]->time_in_2;
		$yesterday['time_in_3'] = $getYesterDayDate[0]->time_in_3;

		$yesterday['time_out_1'] = $getYesterDayDate[0]->time_out_1;
		$yesterday['time_out_2'] = $getYesterDayDate[0]->time_out_2;
		$yesterday['time_out_3'] = $getYesterDayDate[0]->time_out_3;

	}

	if ( !empty($employeeClocking) ) {	

		//Get today Clocking
		$today['time_in_1'] = $employeeClocking->time_in_1;
		$today['time_in_2'] = $employeeClocking->time_in_2;
		$today['time_in_3'] = $employeeClocking->time_in_3;

		$today['time_out_1'] = $employeeClocking->time_out_1;
		$today['time_out_2'] = $employeeClocking->time_out_2;
		$today['time_out_3'] = $employeeClocking->time_out_3;

		//Get today clocking status
		$today['clocking_status'] = $employeeClocking->clocking_status;

	}

	/**
	*
	* CLOCKING IN
	*
	*/

	if(Request::ajax()) {

		if ( $data['timeclocking'] == 'in' ) {	

			// Check yesterday clocking status			
			if( !empty($getYesterDayDate[0]) ) {

				if ( $getYesterDayDate[0]->clocking_status === 'open' ||
					 $getYesterDayDate[0]->clocking_status === 'close' ||
					 $getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
					 $getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
					 $getYesterDayDate[0]->clocking_status === 'clock_out_3' ) {				

					echo 'clock_out_1';

					echo getClockingInDateTime($today['clocking_status'], $currentDateTime);

					/*if( empty($employeeClocking->overtime_status_1) ) {

						//echo 'Do you want to apply for OT';

						//echo $data["resultOvertimeStatus1"];

					}*/

				}

				// Yesterday clock out
				if ( $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' ) {

					echo 'Yesterday clock out';
					echo getClockingInDateTimeYesterdayClockOut($today['clocking_status'], $currentDateTime);
					
				}

			} elseif ( empty($getYesterDayDate[0]) ) {

				if ( $today['clocking_status'] === 'open' ||
					$today['clocking_status'] === 'close' ||
					$today['clocking_status'] === 'clock_out_1' ||
					$today['clocking_status'] === 'clock_out_2' ||
					$today['clocking_status'] === 'clock_out_3' ) {					

					getClockingInDateTime($today['clocking_status'], $currentDateTime);

				}

			}
													
		}


		/**
		*
		* CLOCKING OUT
		*
		*/

		if ( $data['timeclocking'] == 'out' ) {								

			if( !empty($getYesterDayDate[0]) ) {

				// Check yesterday clocking status
				if ( $getYesterDayDate[0]->clocking_status === 'open' ||
					$getYesterDayDate[0]->clocking_status === 'close' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_3' ) {
						
					echo getClockingOutDateTime($today['clocking_status'], $currentDateTime);

					//echo $getYesterDayDate[0]->overtime_status;

				}

				// Initialize: Forget to Clock out yesterday or 
				if ( $getYesterDayDate[0]->clocking_status === 'clock_in_1' ) {

					if ( $today['clocking_status'] === 'open') {

						echo getClockingOutDateTimeForgotToClockOutClockIn1($today['clocking_status'], $currentDateTime, 'clock_in_1');

					}
					
				} elseif ( $getYesterDayDate[0]->clocking_status === 'clock_in_2' ) {

					echo getClockingOutDateTimeForgotToClockOutClockIn2($today['clocking_status'], $currentDateTime, 'clock_in_2');


				} elseif ( $getYesterDayDate[0]->clocking_status === 'clock_in_3' ) {			

					echo getClockingOutDateTimeForgotToClockOutClockIn3($today['clocking_status'], $currentDateTime, 'clock_in_3');											

				}

				// Yesterday clock out
				if ( $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' ) {

					getClockingOutDateTimeYesterdayClockOut($today['clocking_status'], $currentDateTime);

				}

			} elseif ( empty($getYesterDayDate[0]) ) {


				if ( $today['clocking_status'] === 'open' ||
					$today['clocking_status'] === 'close' ||
					$today['clocking_status'] === 'clock_out_1' ||
					$today['clocking_status'] === 'clock_out_2' ||
					$today['clocking_status'] === 'clock_out_3' ) {

					getClockingInDateTime($today['clocking_status'], $currentDateTime);

				}

			}

		}

		
		/**
		*
		* SEARCH
		*
		*/	

		/*if ( $data['search'] == 'search' ) {

			return 'yo';

			return $employeeId = $data['employee'];
			
			$timesheet = new Timesheet;
			$timesheetJsObjectPerMonth = $timesheet->getSearchTimesheetJsObjectPerMonth($employeeId, Session::get('dayDateArr'));	
		
			return $timesheetJsObjectPerMonth;

		}*/

		

	}

}));


//Route::post('/employee/overtimestatus/{timesheetId}'
Route::post('/employee/overtimestatus/', array('as' => 'redrawOvertimeStatus', 'uses' => function()
{
	$data = Input::all();

	/*return dd($data);
	exit;*/

	$getTimesheetById = Timesheet::where('id', '=', $data['timesheetId'])->first();															

	if ( Request::ajax() ){		
		
		if ( $getTimesheetById->clocking_status === 'clock_out_1' ) {

			$getTimesheetById->overtime_status_1 = $data['otStatus'];

			if( $getTimesheetById->save() ) {

				DB::table('overtime')
							->where('timesheet_id', $data['timesheetId'])
							->where('seq_no', 1)
							->update(['overtime_status' => $data['otStatus']]);

				return Redirect::to('/redraw/timesheet');	

			}

		} elseif ( $getTimesheetById->clocking_status === 'clock_out_2' ) {
			
			$getTimesheetById->overtime_status_2 = $data['otStatus'];			

			if( $getTimesheetById->save() ) {

				DB::table('overtime')
							->where('timesheet_id', $data['timesheetId'])
							->where('seq_no', 2)
							->update(['overtime_status' => $data['otStatus']]);

				return Redirect::to('/redraw/timesheet');	

			}		

		} elseif ( $getTimesheetById->clocking_status === 'clock_out_3' ) {
			
			$getTimesheetById->overtime_status_3 = $data['otStatus'];						

			if( $getTimesheetById->save() ) {

				DB::table('overtime')
							->where('timesheet_id', $data['timesheetId'])
							->where('seq_no', 3)
							->update(['overtime_status' => $data['otStatus']]);

				return Redirect::to('/redraw/timesheet');	

			}		

		}

	}

}));

Route::get( '/redraw/timesheet', array('as' => 'redrawTimesheet', 'uses' => 'EmployeesController@redrawEmployeeTimesheet') );

Route::get( '/load/timesheet', array('as' => 'loadTimesheet', 'uses' => 'EmployeesController@loadEmployeeTimesheet') );

Route::get( '/redraw/summary', array('as' => 'redrawSummary', 'uses' => 'EmployeesController@redrawEmployeeSummary') );

/**
*
* Administration
*
*/

/**
*
* Administration: Dashboard
*
*/

//https://scotch.io/tutorials/simple-laravel-layouts-using-blade
//Route::get('/admin/dashboard', array('before' => 'auth', 'as' => 'adminDashboard', 'uses' => function()
Route::get('/admin/dashboard', array('as' => 'adminDashboard', 'uses' => function()
{	

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();	
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();	

	//Admin view
	//return View::make('admin.dashboard', ['employeeInfo' => $employeeInfo, 'getAllEmployee' => $getAllEmployee, 'getAllEmployeeUser' => $getAllEmployeeUser]);	    

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.dashboard', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        

		} else {
			//http://jsfiddle.net/umz8t/458/
			//http://stackoverflow.com/questions/16344354/how-to-make-blinking-flashing-text-with-css3
			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';
		}	

	}

}));

/**
*
* Administration: Employee Scheduling
*
*/

//Route::get('/admin/employee', array('as' => '', 'uses' => function() {
Route::get('/admin/scheduling', array('as' => 'adminScheduling', 'uses' => function() {


	//return 'Administration: Employee Scheduling';
	
	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();	
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();	
	        
	//Admin view
	//return View::make('admin.scheduling', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.scheduling', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

		} else {

			//http://jsfiddle.net/umz8t/458/
			//http://stackoverflow.com/questions/16344354/how-to-make-blinking-flashing-text-with-css3
			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';
		}	

	}	

}));


Route::post('/admin/scheduling/search/default/schedule/', array('as' => '', 'uses' => function() {

	//$data = Input::all();

	$data['employee_number'] = Input::get('employee_number');

	$employeeNumber = Employee::where('employee_number', '=', trim($data['employee_number']))->first();

	$defaultSchedules = Workshift::where('employee_id', '=', trim($employeeNumber->id))->get();	

	$employeeId = Session::get('userEmployeeId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();




	return View::make('admin.scheduling', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee, 'defaultSchedules' => $defaultSchedules]);
	//return View::make('admin.scheduling', ['employeeInfo' => $employeeInfo, 'defaultSchedules' => $defaultSchedules]);

}));

Route::post( '/admin/scheduling/upload/new/schedule', array('as' => 'adminUploadNewSchedule', 'uses' => 'EmployeesController@postShift') );	

Route::post('/admin/scheduling/search/uploaded/schedule', array('as' => '', 'uses' => function() {

	$data = Input::all();

	//$data['employee_number'] = Input::get('employee_number');

	$employeeNumber = Employee::where('employee_number', '=', trim($data['employee_number']))->first();

	$uploadedSchedules = Schedule::where('employee_id', '=', trim($employeeNumber->id))
									->whereBetween('schedule_date', array($data["schedule_date_from"], $data["schedule_date_to"]))
									->get();	

	$employeeId = Session::get('userEmployeeId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	
	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();



	return View::make('admin.scheduling', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee, 'uploadedSchedules' => $uploadedSchedules]);
	//return View::make('admin.scheduling', ['employeeInfo' => $employeeInfo, 'defaultSchedules' => $defaultSchedules]);

}));

/**
*
* Administration: TimeClock & Attendace
*
*/

Route::get('/admin/timeclock', array('as' => 'adminTimeClock', 'uses' => function() {

	//return 'Administration: TimeClock & Attendace';

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();	

	//Admin view	
	//return View::make('admin.timeclock', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.timeclock', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);

		} else {

			//http://jsfiddle.net/umz8t/458/
			//http://stackoverflow.com/questions/16344354/how-to-make-blinking-flashing-text-with-css3
			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}	
	}    

}));

Route::post('/admin/timeclock', array('as' => '', 'uses' => function() {

	$data = Input::get();

	//General Settings
	$nightDiff['from'] = strtotime('22:00:00');
	$nightDiff['to'] = strtotime('06:00:00');

	//return dd($data);

	//$dayOfTheWeek = date('l');	
	//$currentDate = date('Y-m-d');

	if ( -1 !== (int) $data["action"] ) {

		if ( !empty($data["check"]) ) {

			if ( is_array($data["check"]) ) {

	        	if ( sizeof($data["check"]) > 1 ) {

	        		$overtimeById = Overtime::whereIn('id', $data["check"])->get();

	        		$totalOvertime = array();

	        		foreach($overtimeById as $overtime) {

		                $employeeId = $overtime->employee_id;

		                if ( $overtime->seq_no === 1 || 
		                	 $overtime->seq_no === 2 ) {

		                		$shift = 1;

		                } elseif ( $overtime->seq_no === 3 ) {

		                		$shift = 2;
		                }

						$timesheet = new Timesheet;
						$employeeClocking = Timesheet::where('id', $overtime->timesheet_id)->first();

						//return dd($employeeClocking);

						$schedule = new Schedule;
						//$getSchedule = $schedule->getSchedule($employeeId, $employeeClocking->daydate);
						$getSchedule = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($employeeClocking->daydate))->first();

						$Workshift = new Workshift;
						//$getWorkshiftByDayOfTheWeek = $Workshift->getWorkshiftByDayOfTheWeek($employeeId, date('l', strtotime($employeeClocking->daydate)), $overtime->shift);
						$getWorkshiftByDayOfTheWeek = DB::table('work_shift')->where('employee_id', $employeeId)->where('name_of_day', date('l', strtotime($employeeClocking->daydate)))->where('shift', $shift)->first();						

						//return dd($getWorkshiftByDayOfTheWeek);

						$holiday = new Holiday;
						//$getHolidayByDate = $holiday->getHolidayByDate($employeeClocking[0]->daydate);
						$getHolidayByDate = DB::table('holiday')->where('date', trim($employeeClocking->daydate))->first();
						
						//return dd($getHolidayByDate);

						//$timesheet = new Timesheet;
						//$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, $employeeClocking->daydate);						
						//$employeeClocking = DB::table('employee_timesheet')->where('employee_id', '=', $employeeId)->where('daydate', '=', $employeeClocking->daydate)->first();    						

						$summary = new Summary;
						$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, $employeeClocking->daydate);

						//return dd($employeeSummary);						

						//$setting = new Setting;
						//$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

						if( !empty($getSchedule) ) {
							
							//$scheduled['start_time'] = $getSchedule[0]->start_time;
							//$scheduled['end_time'] = $getSchedule[0]->end_time;
							$scheduled['rest_day'] = $getSchedule->rest_day;			
							
						} elseif( !empty($getWorkshiftByDayOfTheWeek) ) {
							
					     	// From 01:00:00 change to 2015-04-30 09:00:00
							//$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeClocking[0]->time_in_1) ).' '.$getWorkshiftByDayOfTheWeek[0]->start_time;

							// From 01:00:00 change to 2015-04-30 01:00:00
							//$scheduled['end_time'] =  date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkshiftByDayOfTheWeek[0]->end_time;

							$scheduled['rest_day'] = $getWorkshiftByDayOfTheWeek->rest_day;							
				
						}	


						/*
						$totalOvertime = '';					
						$totalOvertime1 = '';	
						$totalOvertime2 = '';	
						$totalOvertime3 = '';
						*/

						if ( !empty($employeeClocking->total_overtime_1) && 
							 !empty($employeeClocking->total_overtime_3) ) {

							$totalOvertime = $employeeClocking->total_overtime_1 + $employeeClocking->total_overtime_3;

						} elseif ( !empty($employeeClocking->total_overtime_2) && 
							 !empty($employeeClocking->total_overtime_3) ) {

							$totalOvertime = $employeeClocking->total_overtime_2 + $employeeClocking->total_overtime_3;

						} elseif ( !empty($employeeClocking->total_overtime_1) && 
							 empty($employeeClocking->total_overtime_3) ) {

							$totalOvertime = $employeeClocking->total_overtime_1;

						} elseif ( !empty($employeeClocking->total_overtime_2) && 
							 empty($employeeClocking->total_overtime_3) ) {

							$totalOvertime = $employeeClocking->total_overtime_2;

						} elseif ( (empty($employeeClocking->total_overtime_1) && 
							 !empty($employeeClocking->total_overtime_3)) ||
							 (empty($employeeClocking->total_overtime_2) && 
							 !empty($employeeClocking->total_overtime_3)) ) {

							$totalOvertime = $employeeClocking->total_overtime_3;

						} else {

							$totalOvertime = '';

						}


						$hasNightDiff = false;

						if ( $overtime->seq_no === 1 ) {

							//$getTimesheetById = Timesheet::where('id', '=', $overtime->timesheet_id)->first();

							DB::table('employee_timesheet')
								->where('id', $overtime->timesheet_id)								
								->update(array('overtime_status_1' => $data["action"]));

							DB::table('overtime')
								->where('id', $overtime->id)
								->where('seq_no', 1)
								->update(array('overtime_status' => $data["action"]));
							
							//$totalOvertime1 = $employeeClocking->total_overtime_1;

							if ( strtotime($employeeClocking->total_time_in_1) >= $nightDiff['from'] ||
								 strtotime($employeeClocking->total_time_out_1) <= $nightDiff['to'] ) {

								$hasNightDiff = true;

							}
							


						} elseif ( $overtime->seq_no === 2 ) {

							//$getTimesheetById = Timesheet::where('id', '=', $overtime->timesheet_id)->first();

							DB::table('employee_timesheet')
								->where('id', $overtime->timesheet_id)								
								->update(array('overtime_status_2' => $data["action"]));

							DB::table('overtime')
								->where('id', $overtime->id)
								->where('seq_no', 2)
								->update(array('overtime_status' => $data["action"]));

							//$totalOvertime2 = $employeeClocking->total_overtime_2;	

							if ( strtotime($employeeClocking->total_time_in_2) >= $nightDiff['from'] ||
								 strtotime($employeeClocking->total_time_out_2) <= $nightDiff['to'] ) {

								$hasNightDiff = true;

							}														


						} elseif ( $overtime->seq_no === 3 ) {

							//$getTimesheetById = Timesheet::where('id', '=', $overtime->timesheet_id)->first();

							DB::table('employee_timesheet')
								->where('id', $overtime->timesheet_id)								
								->update(array('overtime_status_3' => $data["action"]));

							DB::table('overtime')
								->where('id', $overtime->id)
								->where('seq_no', 3)
								->update(array('overtime_status' => $data["action"]));

							//$totalOvertime3 = $employeeClocking->total_overtime_3;	


							if ( strtotime($employeeClocking->total_time_in_3) >= $nightDiff['from'] ||
								 strtotime($employeeClocking->total_time_out_3) <= $nightDiff['to'] ) {

								$hasNightDiff = true;

							}															

						}


						//REST DAY: FALSE
						if ( $scheduled['rest_day'] !== 1 ) {
	
							//HOLIDAY: TRUE
							if( hasHoliday($employeeClocking->daydate) ) {

							 echo "HOLIDAY: TRUE \n";

							if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

								echo "Regular holiday \n";

								//TODO: check if has night diff								

								//$employeeSummary->legal_holiday_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);															

								if ( 1 === (int) $data["action"] ) {

									if ( !$hasNightDiff ) {

										$update = array('legal_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('legal_holiday_overtime_night_diff' => $totalOvertime);

									}


									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('legal_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('legal_holiday_overtime_night_diff' => $totalOvertime);

									}									

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}

					
							} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day

								echo "Special non-working day \n";

								//TODO: check if has night diff								

								//$employeeSummary->special_holiday_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																		

								if ( 1 === (int) $data["action"] ) {

									if ( !$hasNightDiff ) {

										$update = array('special_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('special_holiday_overtime_night_diff' => $totalOvertime);

									}									
	
									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('special_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('special_holiday_overtime_night_diff' => $totalOvertime);

									}									

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}															
									

							}

							//HOLIDAY: FALSE	
							} else { //Regular Day			

								//TODO: check if has night diff																

								//$employeeSummary->regular_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																		
								

								if ( 1 === (int) $data["action"] ) {

									if ( !$hasNightDiff ) {

										$update = array('regular_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('regular_overtime_night_diff' => $totalOvertime);

									}									
		
									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('regular_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('regular_overtime_night_diff' => $totalOvertime);

									}									

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}

							}

						//REST DAY: TRUE
						} elseif ( $scheduled['rest_day'] === 1 ) {

							//HOLIDAY: TRUE
							if( hasHoliday($employeeClocking->daydate) ) {

							 echo "HOLIDAY: TRUE \n";

							if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

								echo "Regular holiday \n";

								//TODO: check if has night diff								

								//$employeeSummary->rest_day_legal_holiday_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);															

								if ( 1 === (int) $data["action"] ) {

									if ( !$hasNightDiff ) {

										$update = array('rest_day_legal_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_legal_holiday_overtime_night_diff' => $totalOvertime);

									}									
	
									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('rest_day_legal_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_legal_holiday_overtime_night_diff' => $totalOvertime);

									}									

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}																																	
					
							} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day

								echo "Special non-working day \n";

								//TODO: check if has night diff								

								//$employeeSummary->rest_day_special_holiday_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																		

								if ( 1 === (int) $data["action"] ) {

									if ( !$hasNightDiff ) {

										$update = array('rest_day_special_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_special_holiday_overtime_night_diff' => $totalOvertime);

									}									
	
									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('rest_day_special_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_special_holiday_overtime_night_diff' => $totalOvertime);

									}									


									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}																																																																								
									

							}

							//HOLIDAY: FALSE	
							} else { //Rest Day												

								//TODO: check if has night diff																

								//$employeeSummary->rest_day_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																		

								if ( 1 === (int) $data["action"] ) {


									if ( !$hasNightDiff ) {

										$update = array('rest_day_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_overtime_night_diff' => $totalOvertime);

									}	

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('rest_day_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_overtime_night_diff' => $totalOvertime);

									}										

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}																																															

							}						

						}

	        		}

	        		return Redirect::route('adminTimeClock');

	        	} else {


	        		foreach($data["check"] as $check) {
					
						$overtimeById = Overtime::whereIn('id', $data["check"])->first();

		                $employeeId = $overtimeById->employee_id;

		                if ( $overtimeById->seq_no === 1 || 
		                	 $overtimeById->seq_no === 2 ) {

		                		$shift = 1;

		                } elseif ( $overtimeById->seq_no === 3 ) {

		                		$shift = 2;
		                }


						$timesheet = new Timesheet;
						$employeeClocking = Timesheet::where('id', $overtimeById->timesheet_id)->first();

						$schedule = new Schedule;
						//$getSchedule = $schedule->getSchedule($employeeId, $employeeClocking->daydate);
						$getSchedule = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($employeeClocking->daydate))->first();

						$Workshift = new Workshift;
						//$getWorkshiftByDayOfTheWeek = $Workshift->getWorkshiftByDayOfTheWeek($employeeId, date('l', strtotime($employeeClocking->daydate)), $overtime->shift);
						$getWorkshiftByDayOfTheWeek = DB::table('work_shift')->where('employee_id', $employeeId)->where('name_of_day', date('l', strtotime($employeeClocking->daydate)))->where('shift', $shift)->first();						

						//return dd($getWorkshiftByDayOfTheWeek);

						$holiday = new Holiday;
						//$getHolidayByDate = $holiday->getHolidayByDate($employeeClocking[0]->daydate);
						$getHolidayByDate = DB::table('holiday')->where('date', trim($employeeClocking->daydate))->first();
						
						//return dd($getHolidayByDate);

						//$timesheet = new Timesheet;
						//$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, $employeeClocking->daydate);						
						//$employeeClocking = DB::table('employee_timesheet')->where('employee_id', '=', $employeeId)->where('daydate', '=', $employeeClocking->daydate)->first();    						

						$summary = new Summary;
						$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, $employeeClocking->daydate);

						//return dd($employeeSummary);						

						//$setting = new Setting;
						//$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

						if( !empty($getSchedule) ) {
							
							//$scheduled['start_time'] = $getSchedule[0]->start_time;
							//$scheduled['end_time'] = $getSchedule[0]->end_time;
							$scheduled['rest_day'] = $getSchedule->rest_day;			
							
						} elseif( !empty($getWorkshiftByDayOfTheWeek) ) {
							
					     	// From 01:00:00 change to 2015-04-30 09:00:00
							//$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeClocking[0]->time_in_1) ).' '.$getWorkshiftByDayOfTheWeek[0]->start_time;

							// From 01:00:00 change to 2015-04-30 01:00:00
							//$scheduled['end_time'] =  date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkshiftByDayOfTheWeek[0]->end_time;

							$scheduled['rest_day'] = $getWorkshiftByDayOfTheWeek->rest_day;
						}	

						//$totalOvertime = '';

						if ( !empty($employeeClocking->total_overtime_1) && 
							 !empty($employeeClocking->total_overtime_3) ) {

							$totalOvertime = $employeeClocking->total_overtime_1 + $employeeClocking->total_overtime_3;

						} elseif ( !empty($employeeClocking->total_overtime_2) && 
							 !empty($employeeClocking->total_overtime_3) ) {

							$totalOvertime = $employeeClocking->total_overtime_2 + $employeeClocking->total_overtime_3;

						} elseif ( !empty($employeeClocking->total_overtime_1) && 
							 empty($employeeClocking->total_overtime_3) ) {

							$totalOvertime = $employeeClocking->total_overtime_1;

						} elseif ( !empty($employeeClocking->total_overtime_2) && 
							 empty($employeeClocking->total_overtime_3) ) {

							$totalOvertime = $employeeClocking->total_overtime_2;

						} elseif ( (empty($employeeClocking->total_overtime_1) && 
							 !empty($employeeClocking->total_overtime_3)) ||
							 (empty($employeeClocking->total_overtime_2) && 
							 !empty($employeeClocking->total_overtime_3)) ) {

							$totalOvertime = $employeeClocking->total_overtime_3;

						} else {

							$totalOvertime = '';

						}

						$hasNightDiff = false;

						if ( $overtimeById->seq_no === 1 ) {

							$getTimesheetById = Timesheet::where('id', '=', $overtimeById->timesheet_id)->first();

							DB::table('employee_timesheet')
								->where('id', $overtimeById->timesheet_id)								
								->update(array('overtime_status_1' => $data["action"]));

							DB::table('overtime')
								->where('id', $overtimeById->id)
								->where('seq_no', 1)
								->update(array('overtime_status' => $data["action"]));
							
							//$totalOvertime = $employeeClocking->total_overtime_1;

							if ( strtotime($employeeClocking->total_time_in_1) >= $nightDiff['from'] ||
								 strtotime($employeeClocking->total_time_out_1) <= $nightDiff['to'] ) {

								$hasNightDiff = true;

							}															


						} elseif ( $overtimeById->seq_no === 2 ) {

							//$getTimesheetById = Timesheet::where('id', '=', $overtimeById->timesheet_id)->first();

							DB::table('employee_timesheet')
								->where('id', $overtimeById->timesheet_id)								
								->update(array('overtime_status_2' => $data["action"]));

							DB::table('overtime')
								->where('id', $overtimeById->id)
								->where('seq_no', 2)
								->update(array('overtime_status' => $data["action"]));

							//$totalOvertime = $employeeClocking->total_overtime_1;

							if ( strtotime($employeeClocking->total_time_in_2) >= $nightDiff['from'] ||
								 strtotime($employeeClocking->total_time_out_2) <= $nightDiff['to'] ) {

								$hasNightDiff = true;

							}														


						} elseif ( $overtimeById->seq_no === 3 ) {

							//$getTimesheetById = Timesheet::where('id', '=', $overtimeById->timesheet_id)->first();

							DB::table('employee_timesheet')
								->where('id', $overtimeById->timesheet_id)								
								->update(array('overtime_status_3' => $data["action"]));

							DB::table('overtime')
								->where('id', $overtimeById->id)
								->where('seq_no', 3)
								->update(array('overtime_status' => $data["action"]));

							//$totalOvertime = $employeeClocking->total_overtime_1;	

							if ( strtotime($employeeClocking->total_time_in_3) >= $nightDiff['from'] ||
								 strtotime($employeeClocking->total_time_out_3) <= $nightDiff['to'] ) {

								$hasNightDiff = true;

							}														

						}

						//REST DAY: FALSE
						if ( $scheduled['rest_day'] !== 1 ) {
	
							//HOLIDAY: TRUE
							if( hasHoliday($employeeClocking->daydate) ) {

							 echo "HOLIDAY: TRUE \n";

							if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

								echo "Regular holiday \n";

								//TODO: check if has night diff								

								//$employeeSummary->legal_holiday_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);															

								if ( 1 === (int) $data["action"] ) {

									if ( !$hasNightDiff ) {

										$update = array('legal_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('legal_holiday_overtime_night_diff' => $totalOvertime);

									}


									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('legal_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('legal_holiday_overtime_night_diff' => $totalOvertime);

									}									

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}

					
							} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day

								echo "Special non-working day \n";

								//TODO: check if has night diff								

								//$employeeSummary->special_holiday_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																		

								if ( 1 === (int) $data["action"] ) {

									if ( !$hasNightDiff ) {

										$update = array('special_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('special_holiday_overtime_night_diff' => $totalOvertime);

									}									
	
									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('special_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('special_holiday_overtime_night_diff' => $totalOvertime);

									}									

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}															
									

							}

							//HOLIDAY: FALSE	
							} else { //Regular Day			

								//TODO: check if has night diff																

								//$employeeSummary->regular_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																		
								

								if ( 1 === (int) $data["action"] ) {

									if ( !$hasNightDiff ) {

										$update = array('regular_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('regular_overtime_night_diff' => $totalOvertime);

									}									
		
									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('regular_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('regular_overtime_night_diff' => $totalOvertime);

									}									

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}

							}

						//REST DAY: TRUE
						} elseif ( $scheduled['rest_day'] === 1 ) {

							//HOLIDAY: TRUE
							if( hasHoliday($employeeClocking->daydate) ) {

							 echo "HOLIDAY: TRUE \n";

							if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

								echo "Regular holiday \n";

								//TODO: check if has night diff								

								//$employeeSummary->rest_day_legal_holiday_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);															

								if ( 1 === (int) $data["action"] ) {

									if ( !$hasNightDiff ) {

										$update = array('rest_day_legal_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_legal_holiday_overtime_night_diff' => $totalOvertime);

									}									
	
									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('rest_day_legal_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_legal_holiday_overtime_night_diff' => $totalOvertime);

									}									

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}																																	
					
							} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day

								echo "Special non-working day \n";

								//TODO: check if has night diff								

								//$employeeSummary->rest_day_special_holiday_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																		

								if ( 1 === (int) $data["action"] ) {

									if ( !$hasNightDiff ) {

										$update = array('rest_day_special_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_special_holiday_overtime_night_diff' => $totalOvertime);

									}									
	
									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('rest_day_special_holiday_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_special_holiday_overtime_night_diff' => $totalOvertime);

									}									


									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}																																																																								
									

							}

							//HOLIDAY: FALSE	
							} else { //Rest Day												

								//TODO: check if has night diff																

								//$employeeSummary->rest_day_overtime = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																		

								if ( 1 === (int) $data["action"] ) {


									if ( !$hasNightDiff ) {

										$update = array('rest_day_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_overtime_night_diff' => $totalOvertime);

									}	

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								} elseif ( 0 === (int) $data["action"] ) {

									$totalOvertime = '';

									if ( !$hasNightDiff ) {

										$update = array('rest_day_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_overtime_night_diff' => $totalOvertime);

									}										

									DB::table('employee_summary')
										->where('employee_id', $employeeId)								
										->where('daydate', $employeeClocking->daydate)								
										->update($update);									

								}																																															

							}						

						}


	        		}

	        		return Redirect::route('adminTimeClock');

	        	}
			
			}

		} else {

			return Redirect::route('adminTimeClock');

		}


	} else {

		return Redirect::route('adminTimeClock');

	}



}));


/**
*
* Administration: Human Resources
*
*/

Route::get('/admin/hr', array('as' => 'adminHumanResource', 'uses' => function() {

	//return 'Administration: Human Resources';

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();	
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();	

	//Admin view	
	//return View::make('admin.hr', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.hr', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

		} else {

			//http://jsfiddle.net/umz8t/458/
			//http://stackoverflow.com/questions/16344354/how-to-make-blinking-flashing-text-with-css3
			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	

}));

Route::post('/admin/hr', array('as' => '', 'uses' => function() {

	$data = Input::get();

	//General Settings
	//$nightDiff['from'] = strtotime('22:00:00');
	//$nightDiff['to'] = strtotime('06:00:00');

	//return dd($data);

	//$dayOfTheWeek = date('l');	
	//$currentDate = date('Y-m-d');

	if ( -1 !== (int) $data["action"] ) {

		if ( !empty($data["check"]) ) {

			if ( is_array($data["check"]) ) {



	        	if ( sizeof($data["check"]) > 1 ) { //Mulitple check

	        		$leaves = Leave::whereIn('id', $data["check"])->get();

	        		//$totalLeave = array();

	        		foreach($leaves as $leave) {						

	        			$employeeId = $leave->employee_id;

	        			$leaveSetting = DB::table('leave_setting')->where('employee_id', $employeeId)->get();

	        			$data["action"] = (int) $data["action"];

						// Start date
						$fromDate = $leave->from_date;
						// End date
						$toDate = $leave->to_date;	        			

						$leaveDateArr = array($fromDate, $toDate);


	        			if ( ($data["action"] === 1) && 
	        				 (-1 === (int) $leave->status) ||
	        				 ($data["action"] === 1) && 
	        				 (0 === (int) $leave->status) ) { //Aprroved

	        				if ( 0 !== (int) $leaveSetting[0]->leave_credits ) {

		        				$leaveBalance = $leaveSetting[0]->leave_balance -= 1;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 1));


								//Paid Sick Leave
								if ( 'sick leave' === strtolower($leave->nature_of_leave) ) {

									$update = array('paid_sick_leave' => number_format(8, 2));

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);											
	
		   	        			//Paid Vacation Leave
		   	        			} elseif ( 'vacation leave' === strtolower($leave->nature_of_leave) ) {

		   	        				$update = array('paid_vacation_leave' => number_format(8, 2));

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);				   	        				

		   	        			//Marternity Leave
								} elseif ( 'maternity leave' === strtolower($leave->nature_of_leave) ) {				   	        				

									$update = array('maternity_leave' => number_format(8, 2));

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);											

								//Paternity Leave
								} elseif ( 'paternity leave' === strtolower($leave->nature_of_leave) ) {											

									$update = array('paternity_leave' => number_format(8, 2));

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);

								//Leave Without Pay
		   	        			} elseif ( 'leave without Pay' === strtolower($leave->nature_of_leave) ) {											

		   	        				$update = array('leave_without_pay' => number_format(8, 2));

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);				   	        				

		   	        			}

		   	        		}

   	        			} 
  	        			
  	        			if( ($data["action"] === 0) && 
	        				      (-1 === (int) $leave->status) ) { //Denied	 


	        				if ( $leaveSetting[0]->leave_balance <= (int) $leaveSetting[0]->leave_credits ) {        				

		        				/*$leaveBalance = $leaveSetting[0]->leave_balance;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));*/

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 0));

		   	        		}


	        			} elseif( ($data["action"] === 0) && 
   	        					  (1 === (int) $leave->status) ) { //Denied

	        				if ( $leaveSetting[0]->leave_balance <= (int) $leaveSetting[0]->leave_credits ) {        				

		        				$leaveBalance = $leaveSetting[0]->leave_balance += 1;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 0));

								//Paid Sick Leave
								if ( 'sick leave' === strtolower($leave->nature_of_leave) ) {

									$update = array('paid_sick_leave' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);											

		   	        			//Paid Vacation Leave
		   	        			} elseif ( 'vacation leave' === strtolower($leave->nature_of_leave) ) {

		   	        				$update = array('paid_vacation_leave' => '0');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);				   	        				

		   	        			//Marternity Leave
								} elseif ( 'maternity leave' === strtolower($leave->nature_of_leave) ) {				   	        				

									$update = array('maternity_leave' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);											

								//Paternity Leave
								} elseif ( 'paternity leave' === strtolower($leave->nature_of_leave) ) {											

									$update = array('paternity_leave' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);

								//Leave Without Pay
		   	        			} elseif ( 'leave without Pay' === strtolower($leave->nature_of_leave) ) {											

		   	        				$update = array('leave_without_pay' => '');

			   	        			DB::table('employee_summary')
			   	        				->where('employee_id', $employeeId)
			   	        				->whereBetween('daydate', $leaveDateArr)
			   	        				->update($update);				   	        				

		   	        			}								   	        				

		   	        		}

   	        			} 

	        		}	        		


	        		return Redirect::route('adminHumanResource');

	        	} else { //Mulitple check


	        		//Code here

	        		foreach($data["check"] as $check) {
					
						$leave = Leave::whereIn('id', $data["check"])->first();

						$employeeId = $leave->employee_id;

	        			$leaveSetting = DB::table('leave_setting')->where('employee_id', $employeeId)->get();						

	        			$data["action"] = (int) $data["action"];

						if ( ($data["action"] === 1) && 
	        				 (-1 === (int) $leave->status) ||
							 ($data["action"] === 1) && 
	        				 (0 === (int) $leave->status) ) { //Aprroved

	        				if ( 0 !== (int) $leaveSetting[0]->leave_credits ) {

		        				$leaveBalance = $leaveSetting[0]->leave_balance -= 1;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 1));

		   	        		}	   	        			

   	        			} 

   	        			if( ($data["action"] === 0) && 
	        				      (-1 === (int) $leave->status) ) { //Denied	 


	        				if ( $leaveSetting[0]->leave_balance <= (int) $leaveSetting[0]->leave_credits ) {        				

		        				/*$leaveBalance = $leaveSetting[0]->leave_balance;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));*/

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 0));

		   	        		}


	        			} elseif( ($data["action"] === 0) && 
   	        					  (1 === (int) $leave->status) ) { //Denied

	        				if ( $leaveSetting[0]->leave_balance <= (int) $leaveSetting[0]->leave_credits ) {        				

		        				$leaveBalance = $leaveSetting[0]->leave_balance += 1;

								DB::table('leave_setting')
									->where('employee_id', $employeeId)
									->update(array('leave_balance' => $leaveBalance));

		   	        			DB::table('leave')
		   	        				->where('id', $leave->id)
		   	        				->update(array('status' => 0));

		   	        		}

   	        			}      			

	        		}

	        		return Redirect::route('adminHumanResource');

	        	}
			
			}

		} else {

			return Redirect::route('adminHumanResource');

		}


	} else {

		return Redirect::route('adminHumanResource');

	}

}));


Route::get('/admin/hr/employees', array('as' => 'adminHumanResourceEmployees', 'uses' => function() {

	//return 'Administration: Human Resources';

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();	
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();	

	//Admin view	
	//return View::make('admin.hr', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.employees', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

		} else {

			//http://jsfiddle.net/umz8t/458/
			//http://stackoverflow.com/questions/16344354/how-to-make-blinking-flashing-text-with-css3
			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	

}));




/**
*
* Administration: Payroll
*
*/

Route::get('/admin/payroll', array('as' => '', 'uses' => function() {

	//return 'Administration: Payroll';

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}	
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();	

	//Admin view
	//return View::make('admin.payroll', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.payroll', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);

		} else {

			//http://jsfiddle.net/umz8t/458/
			//http://stackoverflow.com/questions/16344354/how-to-make-blinking-flashing-text-with-css3
			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	

}));


//CREATE: NEW USER/EMPLOYEE
Route::get('/admin/user/new', array('uses' => function()
{	
	
	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	//return View::make('admin.usernew',['employeeInfo' => $employeeInfo]);

	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			return View::make('admin.usernew',['employeeInfo' => $employeeInfo]);

		} else {

			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	

}));

//https://scotch.io/tutorials/simple-laravel-crud-with-resource-controllers

//Route::post('/admin/user/createuser', array('uses' => function()
Route::post('/admin/user/new', array('uses' => function()
{	

	$data = Input::all();
	
	$employeeId = Session::get('userEmployeeId');
	$companies = Company::all();
	$departments = Department::all();
	$jobTitles = JobTitle::all();
	$managers = Employee::where('id', '<>', $employeeId)->get();
	$supervisors = Employee::where('id', '<>', $employeeId)->get();

	$roles = DB::table('groups')->get();

	//$companyArr[0] = '';
	foreach ($companies as $company) {

	    $companyArr[$company->id] = $company->id;

	}

	//$departmentArr[0] = '';
	foreach ($departments as $department) {

	    $departmentArr[$department->id] = $department->id;

	}

	//$jobTitleArr[0] = '';
	foreach ($jobTitles as $jobTitle) {

	    $jobTitleArr[$jobTitle->id] = $jobTitle->id;

	}

	//$managerArr[0] = '';
	foreach ($managers as $manager) {

	   //$fullname = $manager->firstname.', '.$manager->lastname;

	    $managerArr[$manager->id] = $manager->id;

	}

	//$supervisorArr[0] = '';
	foreach ($supervisors as $supervisor) {

	   //$fullname = $supervisor->firstname.', '.$supervisor->lastname;

	    $supervisorArr[$supervisor->id] = $supervisor->id;

	}

	//$roleArr[0] = '';
	foreach($roles as $role) {

	    //echo $role->name;
	    $roleArr[$role->id] = $role->id;

	}	
	
	//http://stackoverflow.com/questions/17235444/how-to-validate-select-box-input-in-laravel4

	//$data['designation'] = (int) $data['designation'];
	$isEmployeeType = (isset($data['is_employee_type'])) ? $data['is_employee_type'] : 'is_employee';
	
	//return dd($data['is_employee_type']);

	if ( !empty($isEmployeeType) ) {

		if( $isEmployeeType === 'is_manager' ) { //Manager:1

			//$employeeType = 1;

			$rules = array(
						'employee_number' => 'required|unique:users',
						//'designation' => array('required', 'in:1,2,3'),
						'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),
						'firstname' => 'required',
						'lastname' => 'required',
						'middlename' => 'required',
						'nick_name' => 'required',
						'company_id' => array('required', 'in:'.implode(',', $companyArr)),
						//'department_head' => array('required', 'in:'.implode(',', $departmentArr)), //in:foo,bar,...
						//'supervisor_id' => array('required', 'in:'.implode(',', $supervisorArr)), //in:foo,bar,...
						'position_id' => array('required', 'in:'.implode(',', $jobTitleArr)),
						'role_id' => array('required', 'in:'.implode(',', $roleArr)),
						//'email' => 'required|unique:users|email',
						'email' => 'email',
						'password' => 'required|min:5|confirmed'
					 );

		} elseif ( $isEmployeeType === 'is_supervisor' ) { //Supervisor:2

			//$employeeType = 2;

			$rules = array(
						'employee_number' => 'required|unique:users',				
						//'designation' => array('required', 'in:1,2,3'),				
						'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),			
						'firstname' => 'required',
						'lastname' => 'required',
						'middlename' => 'required',
						'nick_name' => 'required',
						'company_id' => array('required', 'in:'.implode(',', $companyArr)),
						'department_id' => array('required', 'in:'.implode(',', $departmentArr)), //in:foo,bar,...
						'position_id' => array('required', 'in:'.implode(',', $jobTitleArr)),
						'department_head' => array('required', 'in:'.implode(',', $managerArr)),										
						'role_id' => array('required', 'in:'.implode(',', $roleArr)),
						//'email' => 'required|unique:users|email',
						'email' => 'email',
						'password' => 'required|min:5|confirmed'
					 );

		} elseif ( $isEmployeeType === 'is_employee' ) { //Employee:0

			//$employeeType = 0;

			$rules = array(
						'employee_number' => 'required|unique:users',				
						//'designation' => array('required', 'in:1,2,3'),				
						'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),			
						'firstname' => 'required',
						'lastname' => 'required',
						'middlename' => 'required',
						'nick_name' => 'required',
						'company_id' => array('required', 'in:'.implode(',', $companyArr)),
						'department_id' => array('required', 'in:'.implode(',', $departmentArr)), //in:foo,bar,...
						'position_id' => array('required', 'in:'.implode(',', $jobTitleArr)),
						'department_head' => array('required', 'in:'.implode(',', $managerArr)),				
						'supervisor_id' => array('required', 'in:'.implode(',', $supervisorArr)),
						'role_id' => array('required', 'in:'.implode(',', $roleArr)),
						//'email' => 'required|unique:users|email',
						'email' => 'email',
						'password' => 'required|min:5|confirmed'
					 );

		}

	} elseif( empty($isEmployeeType) ) {

			//$employeeType = 0;

			$rules = array(
						'employee_number' => 'required|unique:users',				
						//'designation'  => array('required', 'in:1,2,3'),		
						'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),							
						'firstname' => 'required',
						'lastname' => 'required',
						'middlename' => 'required',
						'nick_name' => 'required',
						'company_id' => array('required', 'in:'.implode(',', $companyArr)),
						'department_id' => array('required', 'in:'.implode(',', $departmentArr)), //in:foo,bar,...
						'position_id' => array('required', 'in:'.implode(',', $jobTitleArr)),
						'department_head' => array('required', 'in:'.implode(',', $managerArr)),				
						'supervisor_id' => array('required', 'in:'.implode(',', $supervisorArr)),						
						'role_id' => array('required', 'in:'.implode(',', $roleArr)),
						//'email' => 'required|unique:users|email',
						'email' => 'email',
						'password' => 'required|min:5|confirmed'
					 );

	}

	$validator = Validator::make($data, $rules);

	if ( $validator->fails() ) {

		$messages = $validator->messages();

		//return Redirect::to('/admin/user/new')->withErrors($validator)->withInput(Input::except('password'))->withInput(Input::except('designation'));		
	    return Redirect::to('/admin/user/new')->withErrors($validator)->withInput(Input::except('password'));


	} else {

		//TODO: check employment date
		$userCount = User::count();
			
		/*if ( $userCount >= 0 ) {		

			$number = $userCount += 1;
			$totalDigits = strlen($number);

			$currentDate = date('Y');

			if ( $totalDigits === 1 ) {

				//$digit = 'Ones';
				$zeros = '0000';
				$employeeNumber = $currentDate.$zeros.$number;	
				
			} elseif ( $totalDigits === 2 ) {

				//$digit = 'Tens';	
				$zeros = '000';
				$employeeNumber = $currentDate.$zeros.$number;		
				
			} elseif ( $totalDigits  === 3 ) {

				//$digit = 'Hundreds';
				$zeros = '00';
				$employeeNumber = $currentDate.$zeros.$number;			

			} elseif ( $totalDigits  === 4 || $totalDigits  === 5 || $totalDigits  === 6) {

				//$digit = 'thousands';
				$zeros = '0';
				$employeeNumber = $currentDate.$zeros.$number;				

			} elseif ( $totalDigits  === 7 || $totalDigits  === 8 || $totalDigits  === 9) {

				$digit = 'millions';
				$zeros = '';
				$employeeNumber = $currentDate.$zeros.$number;					

			}

		}*/		

		$employee = new Employee;
		$employee->employee_number = trim(ucwords($data["employee_number"]));
		$employee->firstname = trim(ucwords($data["firstname"]));
		$employee->lastname = trim(ucwords($data["lastname"]));	
		$employee->middle_name = trim(ucwords($data["middlename"]));
		$employee->nick_name = trim(ucwords($data["nick_name"]));								
		
		if( $isEmployeeType === 'is_manager' ) { //Manager:1
			
			$employee->employee_type = 1;
			$employee->manager_id = 0;
			$employee->supervisor_id = 0;	

		} elseif ( $isEmployeeType === 'is_supervisor' ) { //Supervisor:2

			$employee->employee_type = 2;
			$employee->manager_id = $data["department_head"];
			$employee->supervisor_id = 0;				

		} elseif ( $isEmployeeType === 'is_employee' ) { //Employee:0		

			$employee->employee_type = 0;
			$employee->manager_id = $data["department_head"];
			$employee->supervisor_id = $data["supervisor_id"];

		}			
		
		$employee->company_id = $data["company_id"];
		$employee->department_id = $data["department_id"];
		$employee->position_id = $data["position_id"];

		$email = (!empty($data['email'])) ? $data['email'] : strtolower($employeeUpdate->firstname).'.'.strtolower($employeeUpdate->lastname).'@backofficeph.com';		

		if ( $employee->save() ) {
			try
			{

				// Create the user
				$SentryUser = Sentry::createUser(array(
				    'email'    => trim($email),
				    'employee_id' => $employee->id,
				    'employee_number' => trim(ucwords($data["employee_number"])),
				    'password' => $data['password'],
				    'first_name' => trim(ucwords($data['firstname'])),
				    'last_name' => trim(ucwords($data['lastname'])),
				    'activated'   => true,
				));

				if($SentryUser) {

					$userId = $SentryUser->id;		

					DB::table('employee_setting')
							->insert(array(
								'employee_id' => $employee->id,
								'has_overtime' => 1,
								'has_break' => 1,
								'break_time' => '01:00:00',
								'hours_per_day' => number_format(8, 2)
								
							));	

					if( isset($userId) ) {
						
						DB::table('users_groups')
								->insert(array(
									'user_id' => $userId, 
									'group_id' => $data["role_id"]
								));	

					}				

					
					Session::put('newEmployeeId', $employee->id);	
					//return Redirect::route('adminDashboard');	
					return Redirect::route('adminUserNewSchedule', array('newEmployeeId' => $employee->id));

				}
			
			}
			catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
			{
				echo 'Login field is required.';
			}
			catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
			{
				echo 'Password field is required.';
			}
			catch (Cartalyst\Sentry\Users\UserExistsException $e)
			{
				echo 'User with this login already exists.';
			}

		}		


	}


		

	//Session::get('newEmployeeId', 32);		
	//return Redirect::route('adminUserNewSchedule', array('user' => 1));

}));


///admin/user/new/{employeeId}/schedule/
Route::get('/admin/user/new/{newEmployeeId}/schedule/', array('as' => 'adminUserNewSchedule', 'uses' => function($newEmployeeId)
{

	//echo $newEmployeeId;


	$employeeId = Session::get('userEmployeeId');		
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	/*$getUserEmployee = DB::table('users')            
        ->join('employees', 'users.employee_id', '=', 'employees.id')
        ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
        ->join('groups', 'users_groups.group_id', '=', 'groups.id')
        ->get();	

     //return dd($getUserEmployee);*/

	
	//return View::make('admin.newschedule', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	
	return View::make('admin.newschedule', ['employeeInfo' => $employeeInfo, 'newEmployeeId' => $newEmployeeId]); 


}));

Route::post('/admin/user/new/schedule/', array('as' => 'adminProcessUserNewSchedule', 'uses' => function()
{		
    
	$data = Input::all();

	$Workshift = new Workshift;

	for ( $i = 0; $i <= sizeof($data["schedule"]) - 1; $i++ ) {
		
		DB::table('work_shift')
			->insert(array(
				'employee_id' => (int) $data['new_employee_id'],
				'name_of_day' => ucwords($data["schedule"][$i]["nameofday"]),
				'rest_day' => $data["schedule"][$i]["restday"],
				'start_time' => date('H:i:s', strtotime($data["schedule"][$i]["starttimehh"] . ':' . $data["schedule"][$i]["starttimemm"])),
				'end_time' => date('H:i:s', strtotime($data["schedule"][$i]["endtimehh"] . ':' . $data["schedule"][$i]["endtimemm"]))
				
		));

	}

	return Redirect::route('adminDashboard');

	/*$rules = array(
		'start_time' => 'required',
		'end_time' => 'required',
		'rest_day' => 'required'
	);

	$validator = Validator::make($data, $rules);

	if ( $validator->fails() ) {

		$messages = $validator->messages();

        return Redirect::to('/admin/user/new/'.$data['new_employee_id'].'/schedule/')->withErrors($validator);		
        //return  Redirect::route('adminProcessUserNewSchedule')->withErrors($validator); 

	} else {

		echo $startTime = date('H:i:s', strtotime( Input::get('start_time').' '.Input::get('start_ampm') ));
		echo $endTime = date('H:i:s', strtotime( Input::get('end_time').' '.Input::get('end_ampm') ));		

		$interval = getDateTimeDiffInterval($startTime, $endTime);

		$hh = (int) $interval->format('%h');
		$mm = (int) $interval->format('%i');
		$ss = (int) $interval->format('%s');			

		echo $hoursPerDay = (double) getTimeToDecimalHours($hh, $mm, $ss);

		DB::table('work_shift')
			->insert(array(
				'employee_id' => (int) $data['new_employee_id'],
				'rest_day' => strtolower($data["rest_day"]),
				'hours_per_day' => $hoursPerDay,
				'start_time' => $startTime,
				'end_time' => $endTime
				
			));	

		//return Redirect::to('/admin/user/new/schedule/');
		return Redirect::route('adminDashboard');

	}*/

}));


//'/admin/user/schedule/{employeeId}/edit'
Route::post('/admin/user/edit/default/schedule', array('as' => 'editDefaultSchedule', 'uses' => function()
{	

	$data = Input::all();
	
	//Todo: Validation

	$Workshift = new Workshift;

	for ( $i = 0; $i <= sizeof($data["schedule"]) - 1; $i++ ) {

		DB::table('work_shift')
			->where('id', (int) $data["schedule"][$i]["defaultScheduleId"])
			->update(array(				
				'name_of_day' => ucwords($data["schedule"][$i]["nameofday"]),
				'rest_day' => $data["schedule"][$i]["restday"],
				'start_time' => date('H:i:s', strtotime($data["schedule"][$i]["starttimehh"] . ':' . $data["schedule"][$i]["starttimemm"])),
				'end_time' => date('H:i:s', strtotime($data["schedule"][$i]["endtimehh"] . ':' . $data["schedule"][$i]["endtimemm"]))
				
		));

	}

	return Redirect::route('adminScheduling');	


}));

Route::post('/admin/user/edit/uploaded/schedule', array('as' => 'editUploadedSchedule', 'uses' => function()
{	

	$data = Input::all();

	//Todo: Validation


	$schedule = new Schedule;

	for ( $i = 0; $i <= sizeof($data["schedule"]) - 1; $i++ ) {

		$startTime = date('H:i:s', strtotime($data["schedule"][$i]["starttimehh"] . ':' . $data["schedule"][$i]["starttimemm"]));
		$endTime = date('H:i:s', strtotime($data["schedule"][$i]["endtimehh"] . ':' . $data["schedule"][$i]["endtimemm"]));

		$startDate = date('Y-m-d', strtotime($data["schedule"][$i]["startdate"]));
		$endDate = date('Y-m-d', strtotime($data["schedule"][$i]["enddate"]));

		$startDateTime = date('Y-m-d H:i:s', strtotime($startTime.' '.$startDate));
		$endDateTime =  date('Y-m-d H:i:s', strtotime($endTime.' '.$endDate));

		$scheduleDate = $startDate;

		DB::table('employee_schedule')
			->where('id', (int) $data["schedule"][$i]["uploadedScheduleId"])
			->update(array(				
				'year' => date('Y', strtotime($startDate)), 
				'month' => date('M', strtotime($startDate)),
				'day' => date('j', strtotime($startDate)),
				'shift' => (int) $data["schedule"][$i]["shift"],
				'rest_day' => $data["schedule"][$i]["restday"],
				'start_time' => $startDateTime,
				'end_time' => $endDateTime,
				'schedule_date' => $scheduleDate
				
		));

	}	

	return Redirect::route('adminScheduling');

}));

Route::get('/admin/user/{employeeEditId}/edit', array('as' => 'adminUserEdit', 'uses' => function($employeeEditId)
{	

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');
	$employeeEditId = (int) $employeeEditId;

	$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
	//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

	if( !empty($userGroups) ) {

	  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

	}

	$currentUser = Sentry::getUser();
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);
	$employeeEditInfo = $employee->getEmployeeInfoById($employeeEditId);
	
	if( !empty($groups) ) {

		if ( strcmp(strtolower($groups->name), strtolower('Employee')) !== 0 ) {

			//return View::make('admin.useredit',['employeeInfo' => $employeeInfo, 'employeeEditId' => $employeeId, 'user' => $user]);
			return View::make('admin.useredit', ['employeeInfo' => $employeeInfo, 'employeeEditInfo' => $employeeEditInfo]);				

		} else {

			echo '<body style="background-color:#000000;"><p style="text-align:center; font-size:75px; color:#00ff00;">_We are watching you (>_<)</p></body>';

		}

	}	


}));

//Route::post('/admin/user/{employeeEditId}/update', array('as' => 'adminUserProcessUpdate', 'uses' => function($employeeEditId)
//Route::post('/admin/user/update', array('as' => 'adminUserProcessUpdate', 'uses' => function($employeeEditId)	
Route::post('/admin/user/{employeeEditId}/edit', array('as' => 'adminUserProcessEdit', 'uses' => function($employeeEditId)
{
	$data = Input::all();
	$employeeEditId = (int) $employeeEditId;

	//$employeeId = Session::get('userEmployeeId');
	//$employeeEditId = (int) $data['employee_id'];

	//return dd($employeeEditId);
	//exit;


	$companies = Company::all();
	$departments = Department::all();
	$jobTitles = JobTitle::all();
	$managers = Employee::where('id', '<>', $employeeEditId)->get();
	$supervisors = Employee::where('id', '<>', $employeeEditId)->get();

	$roles = DB::table('groups')->get();

	//$companyArr[0] = '';
	foreach ($companies as $company) {

	    $companyArr[$company->id] = $company->id;

	}

	//$departmentArr[0] = '';
	foreach ($departments as $department) {

	    $departmentArr[$department->id] = $department->id;

	}

	//$jobTitleArr[0] = '';
	foreach ($jobTitles as $jobTitle) {

	    $jobTitleArr[$jobTitle->id] = $jobTitle->id;

	}

	//$managerArr[0] = '';
	foreach ($managers as $manager) {

	   //$fullname = $manager->firstname.', '.$manager->lastname;

	    $managerArr[$manager->id] = $manager->id;

	}

	//$supervisorArr[0] = '';
	foreach ($supervisors as $supervisor) {

	   //$fullname = $supervisor->firstname.', '.$supervisor->lastname;

	    $supervisorArr[$supervisor->id] = $supervisor->id;

	}

	//$roleArr[0] = '';
	foreach($roles as $role) {

	    //echo $role->name;
	    $roleArr[$role->id] = $role->id;

	}

	//http://stackoverflow.com/questions/17235444/how-to-validate-select-box-input-in-laravel4

	//$data['designation'] = (int) $data['designation'];
	$isEmployeeType = (isset($data['is_employee_type'])) ? $data['is_employee_type'] : 'is_employee';
		
	if ( !empty($isEmployeeType) ) {

		if( $isEmployeeType === 'is_manager' ) { //Manager:1

			$employeeType = 1;
			$managerId = 0;
			$supervisorId = 0;

			$rules = array(
						//'designation' => array('required', 'in:1,2,3'),
						'employee_number' => 'required',				
						'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),
						'firstname' => 'required',
						'lastname' => 'required',
						'middlename' => 'required',
						'nick_name' => 'required',
						'company_id' => array('required', 'in:'.implode(',', $companyArr)),
						//'department_id' => array('required', 'in:'.implode(',', $departmentArr)), //in:foo,bar,...
						//'supervisor_id' => array('required', 'in:'.implode(',', $supervisorArr)), //in:foo,bar,...
						'position_id' => array('required', 'in:'.implode(',', $jobTitleArr)),
						'role_id' => array('required', 'in:'.implode(',', $roleArr)),
						//'email' => 'required|unique:users|email'
						'email' => 'email',
						'password' => 'min:5'						
					 );

		} elseif ( $isEmployeeType === 'is_supervisor' ) { //Supervisor:2

			$employeeType = 2;
			$managerId = $data["department_head"];
			$supervisorId = $data["supervisor_id"];			

			$rules = array(
						//'designation' => array('required', 'in:1,2,3'),
						'employee_number' => 'required',										
						'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),			
						'firstname' => 'required',
						'lastname' => 'required',
						'middlename' => 'required',
						'nick_name' => 'required',
						'company_id' => array('required', 'in:'.implode(',', $companyArr)),
						'department_id' => array('required', 'in:'.implode(',', $departmentArr)), //in:foo,bar,...
						'position_id' => array('required', 'in:'.implode(',', $jobTitleArr)),
						'department_head' => array('required', 'in:'.implode(',', $managerArr)),										
						'role_id' => array('required', 'in:'.implode(',', $roleArr)),
						//'email' => 'required|unique:users|email'
						'email' => 'email',
						'password' => 'min:5'						
					 );

		} elseif ( $isEmployeeType === 'is_employee' ) { //Employee:0

			$employeeType = 0;
			$managerId = $data["department_head"];
			$supervisorId = $data["supervisor_id"];			

			$rules = array(
						//'designation' => array('required', 'in:1,2,3'),
						'employee_number' => 'required',										
						'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),			
						'firstname' => 'required',
						'lastname' => 'required',
						'middlename' => 'required',
						'nick_name' => 'required',
						'company_id' => array('required', 'in:'.implode(',', $companyArr)),
						'department_id' => array('required', 'in:'.implode(',', $departmentArr)), //in:foo,bar,...
						'position_id' => array('required', 'in:'.implode(',', $jobTitleArr)),
						'department_head' => array('required', 'in:'.implode(',', $managerArr)),				
						'supervisor_id' => array('required', 'in:'.implode(',', $supervisorArr)),						
						'role_id' => array('required', 'in:'.implode(',', $roleArr)),
						//'email' => 'required|unique:users|email'
						'email' => 'email',
						'password' => 'min:5'						
					 );

		}

	} elseif( empty($isEmployeeType) ) {

			$employeeType = 0;
			$managerId = $data["department_head"];
			$supervisorId = $data["supervisor_id"];

			$rules = array(
						//'designation'  => array('required', 'in:1,2,3'),
						'employee_number' => 'required',								
						'is_employee_type' => array('required', 'in:is_manager,is_supervisor,is_employee'),							
						'firstname' => 'required',
						'lastname' => 'required',
						'middlename' => 'required',
						'nick_name' => 'required',
						'company_id' => array('required', 'in:'.implode(',', $companyArr)),
						'department_id' => array('required', 'in:'.implode(',', $departmentArr)), //in:foo,bar,...
						'position_id' => array('required', 'in:'.implode(',', $jobTitleArr)),
						'department_head' => array('required', 'in:'.implode(',', $managerArr)),				
						'supervisor_id' => array('required', 'in:'.implode(',', $supervisorArr)),						
						'role_id' => array('required', 'in:'.implode(',', $roleArr)),
						//'email' => 'required|unique:users|email'
						'email' => 'email',
						'password' => 'min:5'						
					 );

	}

	if( !empty($data["password"]) || $data["password"] !== '' ) {
		
		$rules['password'] = 'confirmed';
		//return dd($rules);
		//$validator = Validator::make($data, $rules);

	}/* else {

		$validator = Validator::make($data, $rules);

	}*/

	//return dd($rules);
	//die();

	$validator = Validator::make($data, $rules);

	if ( $validator->fails() ) {

		$messages = $validator->messages();
	    return Redirect::to('/admin/user/'.$employeeEditId.'/edit')->withErrors($validator);	    


	} else {		

		$employee = new Employee;
		$employeeUpdate = Employee::where('id', $employeeEditId)->first();

		$employeeUpdate->employee_number = trim(ucwords($data["employee_number"]));
		$employeeUpdate->firstname = trim(ucwords($data["firstname"]));
		$employeeUpdate->lastname = trim(ucwords($data["lastname"]));	
		$employeeUpdate->middle_name = trim(ucwords($data["middlename"]));
		$employeeUpdate->nick_name = trim(ucwords($data["nick_name"]));						

		//$employeeUpdate->employee_type = $employeeType;		
		//$employeeUpdate->manager_id = $managerId;
		//$employeeUpdate->supervisor_id = $supervisorId;	

		if( $isEmployeeType === 'is_manager' ) { //Manager:1
			
			$employeeUpdate->employee_type = 1;
			$employeeUpdate->manager_id = 0;
			$employeeUpdate->supervisor_id = 0;	

		} elseif ( $isEmployeeType === 'is_supervisor' ) { //Supervisor:2

			$employeeUpdate->employee_type = 2;
			$employeeUpdate->manager_id = $data["department_head"];
			$employeeUpdate->supervisor_id = 0;				

		} elseif ( $isEmployeeType === 'is_employee' ) { //Employee:0		

			$employeeUpdate->employee_type = 0;
			$employeeUpdate->manager_id = $data["department_head"];
			$employeeUpdate->supervisor_id = $data["supervisor_id"];

		}		

		$employeeUpdate->company_id = $data["company_id"];
		$employeeUpdate->department_id = $data["department_id"];
		$employeeUpdate->position_id = $data["position_id"];

		if( $employeeUpdate->save() ) {


			//$employeeId = Session::get('userEmployeeId');
			//$userId = Session::get('userId');
			//$userEmail = Session::get('email');  		

			$employeeId = $employeeUpdate->id;
					
			/*$userUpdate = User::where('employee_id', $employeeId)->first();
			$userUpdate->employee_id = $employeeId;
			$userUpdate->employee_number = $data["employee_number"];
			$userUpdate->first_name = $data["firstname"];
			$userUpdate->last_name = $data["lastname"];
			$userUpdate->email = $data["email"];
			$userUpdate->password = Hash::make($data["password"]);*/
			
			//$userUpdate->remember_token
			//$userUpdate->permissions
			//$userUpdate->activated
			//$userUpdate->activation_code
			//$userUpdate->activate_at
			//$userUpdate->last_login
			//$userUpdate->persist_code
			//$userUpdate->reset_password_code	

			try
			{
			    
				$userUpdate = User::where('employee_id', $employeeEditId)->first();
				$userId = $userUpdate->id;

			    // Find the user using the user id
				$userUpdate = Sentry::findUserById($userId);

			    // Update the user details

				//$email = (!empty($data['email'])) ? $data['email'] : 'dummy@backofficeph.com';
				$email = (!empty($data['email'])) ? $data['email'] : strtolower($employeeUpdate->firstname).'.'.strtolower($employeeUpdate->lastname).'@backofficeph.com';
				
				$userUpdate->employee_id = $employeeEditId;
				$userUpdate->employee_number = trim(ucwords($data["employee_number"]));
				$userUpdate->first_name = $data["firstname"];
				$userUpdate->last_name = $data["lastname"];
				$userUpdate->email = trim($email); //$data["email"];
				//$userUpdate->password = Hash::make($data["password"]);	

			    // Update the user table
			    if ( $userUpdate->save() ) {
			     
			     	//return dd($userUpdate);
			     	//break;
			        // User information was updated
					//$userId = $userUpdate->id;	


					if( !empty($data["password"]) ) {
					//if( !empty($data["password"]) || $data["password"] !== '' ) {			    	

					try
					{
					    // Find the user using the user email address
					    //$user = Sentry::findUserByLogin($data["email"]);

					    $user = Sentry::findUserById($userId);

					    // Get the password reset code
					    $resetCode = $user->getResetPasswordCode();

					    // Now you can send this code to your user via email for example.

					    // OR 

						// Check if the reset password code is valid
					    if ( $user->checkResetPasswordCode($resetCode) )
					    {
					        // Attempt to reset the user password
					        if ( $user->attemptResetPassword($resetCode, $data["password"]) )
					        {
					            // Password reset passed
					            //echo 'Password reset passed';
					            
					            $user->password = $data["password"];
					            $user->save();
					        }
					        else
					        {
					            // Password reset failed
					            echo 'Password reset failed';
					        }
					    }
					    else
					    {
					        // The provided password reset code is Invalid
					        echo 'The provided password reset code is Invalid';					      
					    }


					}
					catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
					{
					    echo 'User was not found.';
					}

					}				

					if( isset($userId) ) {

					DB::table('users_groups')
						->where('user_id', $userId)
						->update(array(				
							'group_id' => $data["role_id"]
						));		

					}


					$message = 'Updated Successfully.';
					//return Redirect::route('adminDashboard');		        
					//return Redirect::route('adminHumanResourceEmployees')->with('message', $message);
	    			return Redirect::to('/admin/user/'.$employeeEditId.'/edit')->with('message', $message);	
	    			//return Redirect::route('adminUserEdit');		        
	    												

			    } else {

			    	// User information was not updated
			        echo 'User information was not updated';
			    }

			}
			catch (Cartalyst\Sentry\Users\UserExistsException $e)
			{
			    echo 'User with this login already exists.';
			}
			catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
			{
			    echo 'User was not found.';
			}			

		}

	}		

}));


Route::get('/admin/user/{employeeId}/delete', array('as' => 'adminUserDelete', 'uses' => function($employeeId)
{	
	//return $employeeId;
	
	/*
	//$employeeId = Auth::user()->employee_id;
	//$employeeId = Session::get('userEmployeeId');	
	//$userId = Session::get('userId');
	*/
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$user = '';
	$userGroup = '';
	$group = '';
	$user = DB::table('users')->where('employee_id', $employeeId)->first(); 
	//return dd($user);


	if ( !empty($user) ) {

		$userGroup = DB::table('users_groups')->where('user_id', $user->id)->first(); 
		//return dd($userGroup);	

	}


	if ( !empty($userGroup) ) {
		
		$group = DB::table('groups')->where('id', (int) $userGroup->group_id)->first();  	

	} 
	
	return View::make('admin.useredit', ['employeeInfo' => $employeeInfo, 'user' => $user, 'group' => $group]);


}));

///admin/user/new/{newEmployeeId}/schedule/
Route::get('/admin/user/leave/', array('uses' => function()
{	
	
	//$employeeId = Auth::user()->employee_id;
	$employeeId = Session::get('userEmployeeId');
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	return View::make('admin.leaveform',['employeeInfo' => $employeeInfo]);

}));

Route::post('/admin/user/leave/', array('uses' => function()
{	

	$data = Input::all();

	$leave = new Leave;

	//$employeeId = Auth::user()->employee_id;
	$employeeId = Session::get('userEmployeeId');
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$rules = array(
		'company_id' => 'required',
		'position_id' => 'required',
		'department_id' => 'required',
		'department_head' => 'required',		
		'nature_of_leave' => 'required',		
		//'other_nature_of_leave' => 'required',			
		'with_pay' => 'required',			
		'number_of_days' => 'required',					
		'from' => 'required',
		'to' => 'required',
		'reasons' => 'required'						

	);

	$validator = Validator::make($data, $rules);

	if ( $validator->fails() ) {

		$messages = $validator->messages();

        return Redirect::to('/admin/user/leave/')->withErrors($validator);		


	} else {

		/*$leave->employee_id = $data["employeeid"];

		$leave->company_id = $data["company_id"];
		$leave->position_id = $data["position_id"];
		$leave->department_id = $data["department_id"];
		$leave->department_head = ucwords($data["department_head"]);

		$leave->nature_of_leave = $data["nature_of_leave"];
		$leave->other_nature_of_leave = $data["other_nature_of_leave"];

		$leave->with_pay = $data["with_pay"];

		$leave->number_of_days = $data["number_of_days"];
		$leave->from_date = $data["from"];
		$leave->to_date = $data["to"];
		$leave->reason = $data["reasons"];*/


		/*if( $leave->save() ) {

			return View::make('admin.leaveform',['employeeInfo' => $employeeInfo]);

		}*/		


		DB::table('leave')
			->insert(array(
				'employee_id' => (int) $data["employeeid"],
				'company_id' => strtolower($data["company_id"]),
				'position_id' => $data["position_id"],
				'department_id' => $data["department_id"],
				'department_head' => ucwords($data["department_head"]),

				'nature_of_leave' => $data["nature_of_leave"],			
				'other_nature_of_leave' => $data["other_nature_of_leave"],

				'with_pay' => $data["with_pay"],				

				'number_of_days' => $data["number_of_days"],			
				'from_date' => $data["from"],		
				'to_date' => $data["to"],		
				'reason' => $data["reasons"],
				'status' => -1																								
			));	

		return View::make('admin.leaveform',['employeeInfo' => $employeeInfo]);			

	}

}));



Route::get('/admin/employee/timesheet', array('before' => 'auth', 'as' => 'adminEmployeeTimesheet', 'uses' => 'EmployeesController@showAdminEmployeeTimesheet'));

////Route::post('/admin/employee/timesheet', array('before' => 'auth', 'as' => 'adminEmployeeTimesheet', 'uses' => 'EmployeesController@searchResultEmployeeTimesheet'));

//Route::post('/admin/employee/timesheet', array('before' => 'auth', 'as' => 'redrawAdminTimesheet', 'uses' => 'EmployeesController@redrawSearchEmployeeTimesheet'));

//Route::get( '/redraw/admin/search/timesheet', array('as' => 'redrawAdminTimesheet', 'uses' => 'EmployeesController@redrawSearchEmployeeTimesheet') );

Route::post( '/admin/timesheet/save', array('as' => 'adminTimesheetSave', 'uses' => 'EmployeesController@adminTimesheetSave') );


Route::get('/admin/search/timesheet/', array('as' => 'searchTimesheet', 'uses' => function()
{

	$data = Input::all();

	$searchEmployeeId = (int) $data['employeeid'];

	if($searchEmployeeId !== 0)
	{

		$employeeId = Session::get('userEmployeeId');

		Session::put('searchEmployeeId', $searchEmployeeId);

		$employee = new Employee;		
		$employeeInfo = $employee->getEmployeeInfoById($employeeId);
		$employeeSearchInfo = $employee->getEmployeeInfoById($searchEmployeeId);

		$getUserEmployee = DB::table('users')            
		    ->join('employees', 'users.employee_id', '=', 'employees.id')
		    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
		    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
		    ->get();

		return View::make('employees.admin.clockingsearch', ['employeeInfo' => $employeeInfo, 'employeeSearchInfo' => $employeeSearchInfo, 'searchEmployeeId' => $searchEmployeeId]);	
	
	}
	else 
	{

		return Redirect::to('/employee/clocking');

	}



}));

Route::get( '/redraw/search/timesheet', array('as' => 'redrawAdminSearchTimesheet', 'uses' => 'EmployeesController@redrawSearchEmployeeTimesheet') );
Route::get( '/redraw/search/summary', array('as' => 'redrawSearchEmployeeSummary', 'uses' => 'EmployeesController@redrawSearchEmployeeSummary') );

Route::get( '/redraw/search/summary2', array('as' => 'redrawAdminSearchSummary', 'uses' => 'EmployeesController@redrawSearchEmployeeSummary') );

Route::get( '/redraw/search/employeeInfo', array('as' => 'redrawAdminEmployeeInfo', 'uses' => 'EmployeesController@redrawSearchEmployeeInfo') );

Route::get('/employee/report/summary', array('as' => 'reportSummary', 'uses' => function()
{

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$searchEmployeeId = Session::get('searchEmployeeId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$getUserEmployee = DB::table('users')            
	    ->join('employees', 'users.employee_id', '=', 'employees.id')
	    ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
	    ->join('groups', 'users_groups.group_id', '=', 'groups.id')
	    ->get();

	//return View::make('admin.dashboard', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        
	return View::make('admin.reportsummary', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        	

}));


/*Route::post('/admin/get/supervisors/', array('as' => 'getSupervisors', 'uses' => function()
{
    
	$data = Input::all();
	return dd($data);

    $employeeId = Session::get('userEmployeeId');
	
	if(Request::ajax()) {

		$supervisors = Employee::where('id', '<>', $employeeId)->get();

		$supervisorArr[0] = '';
		foreach ($supervisors as $supervisor) {

		   $fullname = $supervisor->firstname.', '.$supervisor->lastname;

		    $supervisorArr[$supervisor->id] = $fullname;

		    return $supervisorArr;

		}	

		

	}

}));*/


//CREATE: NEW COMPANY
Route::get('/admin/company/new', array('as' => 'adminNewCompany', 'uses' => function()
{

	//return 'Add Company';

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	return View::make('admin.companynew', ['employeeInfo' => $employeeInfo]);

}));

//CREATE: NEW COMPANY
Route::post('/admin/company/new', array('as' => 'adminProcessNewCompany', 'uses' => function()
{

	//return 'Save New Company';
	
	$data = Input::all();

	//return dd($data);

	$rules = array(
			 	'company_name' => 'required'
			 );

	$validator = Validator::make($data, $rules);

	if ( $validator->fails() ) {

		$messages = $validator->messages();
	    return Redirect::to('/admin/company/new')->withErrors($validator);

	} else {	

		$company = new Company;
		$company->name = trim(ucwords($data['company_name']));
		//$company->save();

		if ( $company->save() ) {

			$message = 'Created Successfully.';
			return Redirect::to('/admin/company/new')->with('message', $message);

		}
	}

}));

//UPDATE: EXISTING COMPANY
Route::get('/admin/company/edit/{id}', array('as' => 'adminEditCompany', 'uses' => function($id)
{

	$id = (int) $id;
	//return 'Update Company';

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);	

	return View::make('admin.companyedit', array('id' => $id, 'employeeInfo' => $employeeInfo));
	//return Redirect::route('updateCompany', array('id' => $id));

}));

//UPDATE: EXISTING COMPANY
Route::post('/admin/company/edit/{id}', array('as' => 'adminProcessEditCompany', 'uses' => function($id)
{
	$data = Input::all();
	$id = (int) $id;

	$rules = array(
			 	'company_name' => 'required'
			 );

	$validator = Validator::make($data, $rules);

	if ( $validator->fails() ) {

		$messages = $validator->messages();
	    return Redirect::to('/admin/company/edit/'.$id)->withErrors($validator);

	} else {	



		$company = Company::find($id);
		$company->name = trim(ucwords($data['company_name']));

		if ( $company->save() ) {

			$message = 'Updated Successfully.';
			//return Redirect::to('/admin/company/new/'.$id)->with('message', $message);
			return Redirect::to('/admin/company/new/')->with('message', $message);

		}

	}

}));

//DELETE: EXISTING COMPANY
Route::get('/admin/company/delete/{id}', array('as' => 'adminDeleteCompany', 'uses' => function($id)
{
	$id = (int) $id;

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);	

	//return 'Update Company';
	return View::make('admin.companydelete', array('id' => $id, 'employeeInfo' => $employeeInfo));
	//return Redirect::route('updateCompany', array('id' => $id));

}));

//DELETE: EXISTING COMPANY
Route::post('/admin/company/delete/{id}', array('as' => 'adminProcessDeleteCompany', 'uses' => function($id)
{
	$data = Input::all();
	$id = (int) $id;

	$company = Company::find($id);
	
	if ( $company->delete() ) {
		
		$message = 'Deleted Successfully.';		
		return Redirect::route('adminNewCompany')->with('message', $message);

	}

}));




//CREATE: NEW DEPARTMENT
Route::get('/admin/department/new', array('as' => 'adminNewDepartment', 'uses' => function()
{

	//return 'Add Department';

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	return View::make('admin.departmentnew', ['employeeInfo' => $employeeInfo]);

}));

//CREATE: NEW DEPARTMENT
Route::post('/admin/department/new', array('as' => 'adminProcessNewDepartment', 'uses' => function()
{

	//return 'Save New Department';
	
	$data = Input::all();	

	$rules = array(
			 	'department_name' => 'required'
			 );

	$validator = Validator::make($data, $rules);

	if ( $validator->fails() ) {

		$messages = $validator->messages();
	    return Redirect::to('/admin/department/new')->withErrors($validator);

	} else {	

		$department = new Department;
		$department->name = trim(ucwords($data['department_name']));

		if ( $department->save() ) {

			$message = 'Created Successfully.';
			return Redirect::to('/admin/department/new')->with('message', $message);

		}
	}

}));

//UPDATE: EXISTING DEPARTMENT
Route::get('/admin/department/edit/{id}', array('as' => 'adminEditDepartment', 'uses' => function($id)
{

	$id = (int) $id;

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	//return 'Update Department';
	return View::make('admin.departmentedit', array('id' => $id, 'employeeInfo' => $employeeInfo));

}));

//UPDATE: EXISTING COMPANY
Route::post('/admin/department/edit/{id}', array('as' => 'adminProcessEditDepartment', 'uses' => function($id)
{
	$data = Input::all();
	$id = (int) $id;

	$rules = array(
			 	'department_name' => 'required'
			 );

	$validator = Validator::make($data, $rules);

	if ( $validator->fails() ) {

		$messages = $validator->messages();
	    return Redirect::to('/admin/department/edit/'.$id)->withErrors($validator);

	} else {	



		$department = Department::find($id);
		$department->name = trim(ucwords($data['department_name']));

		if ( $department->save() ) {

			$message = 'Updated Successfully.';
			//return Redirect::to('/admin/company/new/'.$id)->with('message', $message);
			return Redirect::to('/admin/department/new/')->with('message', $message);

		}

	}

}));

//DELETE: EXISTING COMPANY
Route::get('/admin/department/delete/{id}', array('as' => 'adminDeleteDepartment', 'uses' => function($id)
{
	$id = (int) $id;

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	//return 'Update Company';
	return View::make('admin.departmentdelete', array('id' => $id, 'employeeInfo' => $employeeInfo));
	//return Redirect::route('updateCompany', array('id' => $id));

}));

//DELETE: EXISTING COMPANY
Route::post('/admin/department/delete/{id}', array('as' => 'adminProcessDeleteDepartment', 'uses' => function($id)
{
	$data = Input::all();
	$id = (int) $id;

	$department = Department::find($id);
	
	if ( $department->delete() ) {

		$message = 'Deleted Successfully.';		
		return Redirect::route('adminNewDepartment')->with('message', $message);

	}

}));





//CREATE: new JobTitle
Route::get('/admin/jobtitle/new', array('as' => 'adminNewJobTitle', 'uses' => function()
{

	//return 'Add Job Title';

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);	

	return View::make('admin.jobtitlenew', ['employeeInfo' => $employeeInfo]);

}));

//CREATE: new JobTitle
Route::post('/admin/jobtitle/new', array('as' => 'adminProcessNewJobTitle', 'uses' => function()
{

	//return 'Save New Job Title';
	
	$data = Input::all();	

	$rules = array(
			 	'job_title_name' => 'required'
			 );

	$validator = Validator::make($data, $rules);

	if ( $validator->fails() ) {

		$messages = $validator->messages();
	    return Redirect::to('/admin/jobtitle/new')->withErrors($validator);

	} else {	

		$JobTitle = new JobTitle;
		$JobTitle->name = trim(ucwords($data['job_title_name']));

		if ( $JobTitle->save() ) {

			$message = 'Created Successfully.';
			return Redirect::to('/admin/jobtitle/new')->with('message', $message);

		}
	}

}));

//UPDATE: EXISTING DEPARTMENT
Route::get('/admin/jobtitle/edit/{id}', array('as' => 'adminEditJobTitle', 'uses' => function($id)
{

	$id = (int) $id;

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	//return 'Update Department';
	return View::make('admin.jobtitleedit', array('id' => $id, 'employeeInfo' => $employeeInfo));

}));

//UPDATE: EXISTING COMPANY
Route::post('/admin/jobtitle/edit/{id}', array('as' => 'adminProcessEditJobTitle', 'uses' => function($id)
{
	$data = Input::all();
	$id = (int) $id;

	$rules = array(
			 	'job_title_name' => 'required'
			 );

	$validator = Validator::make($data, $rules);

	if ( $validator->fails() ) {

		$messages = $validator->messages();
	    return Redirect::to('/admin/jobtitle/edit/'.$id)->withErrors($validator);

	} else {	



		$JobTitle = JobTitle::find($id);
		$JobTitle->name = trim(ucwords($data['job_title_name']));

		if ( $JobTitle->save() ) {

			$message = 'Updated Successfully.';
			//return Redirect::to('/admin/company/new/'.$id)->with('message', $message);
			return Redirect::to('/admin/jobtitle/new/')->with('message', $message);

		}

	}

}));

//DELETE: EXISTING COMPANY
Route::get('/admin/jobtitle/delete/{id}', array('as' => 'adminDeleteJobTitle', 'uses' => function($id)
{
	$id = (int) $id;

	$employeeId = Session::get('userEmployeeId');	
	$userId = Session::get('userId');

	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);	

	//return 'Update Company';
	return View::make('admin.jobtitledelete', array('id' => $id, 'employeeInfo' => $employeeInfo));

}));

//DELETE: EXISTING COMPANY
Route::post('/admin/jobtitle/delete/{id}', array('as' => 'adminProcessDeleteJobTitle', 'uses' => function($id)
{
	$data = Input::all();
	$id = (int) $id;

	$JobTitle = JobTitle::find($id);
	
	if ( $JobTitle->delete() ) {

		$message = 'Deleted Successfully.';		
		return Redirect::route('adminNewJobTitle')->with('message', $message);

	}

}));


//----------------------- Ibyang's Routes ------------------------ //

//routes for Group Module
//route to edit group details page
Route::get('/editgroup/{id}', 'GroupController@editgroup');

//route for delete groups page
Route::delete('/destroygroup/{id}', 'GroupController@destroygroup');

Route::get('/getGroups','GroupController@index');

Route::post('/createGroup','GroupController@handleCreate');

Route::get('/getGroup','GroupController@createGroup');

//routes for Permissions Module
//route for delete groups page
Route::delete('/destroyperm/{id}', 'PermissionController@destroyperm');

//route to view list of permission
Route::get('/getPermissionList','PermissionController@index');

//route to create new permission
Route::post('/createPermission','PermissionController@createPermission');

Route::get('/createPermission','PermissionController@create');

//route to edit permission
Route::get('/editpermission/{id}','PermissionController@editpermission');

Route::post('/editpermission','PermissionController@handleEditpermission');

//route to view permission page per user
Route::get('/userpermission/{id}', 'UsersController@userpermission');

//route to edit user permission
Route::post('/userpermission', 'UsersController@handleEditPermission');

//route to edit group permission
Route::post('/groupedit', 'GroupController@handleEditGroup');