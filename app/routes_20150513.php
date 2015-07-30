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
	$currentDateTime = date("Y-m-d H:i:s"); //0000-00-00 00:00:00	
	$employeeId = Session::get('userEmployeeId');
		
	$workShift = new Workshift;
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
 	$employeeClocking = Timesheet::where('employee_id', '=', $employeeId)->where('daydate', '=', date('Y-m-d'))->first();

	$otherDayDate = date( "Y-m-d", strtotime('-2 days') );
	$getOtherDayDate = DB::table('employee_timesheet')->where('employee_id', $employeeId)->where('daydate', $otherDayDate)->get();	

	$yesterDayDate = date( "Y-m-d", strtotime('yesterday') );
	$getYesterDayDate = DB::table('employee_timesheet')->where('employee_id', $employeeId)->where('daydate', $yesterDayDate)->get();	
	
	$employeeNightDiffClocking = Timesheet::where('employee_id', '=', $employeeId)->where('daydate', '=', $yesterDayDate)->first();
		
	$employeeSummary = Summary::where('employee_id', '=', $employeeId)->where('daydate', '=', date('Y-m-d'))->first();
	
	$employeeSummaryNightDiffClocking = Summary::where('employee_id', '=', $employeeId)->where('daydate', '=', $yesterDayDate)->first();	


	//Get Other day clocking
	if ( !empty($getOtherDayDate[0]) ) {

		$otherday['time_in_1']     =     $getOtherDayDate[0]->time_in_1;
		$otherday['time_in_2']     =     $getOtherDayDate[0]->time_in_2;
		$otherday['time_in_3']     =     $getOtherDayDate[0]->time_in_3;

		$otherday['time_out_1']    =     $getOtherDayDate[0]->time_out_1;
		$otherday['time_out_2']    =     $getOtherDayDate[0]->time_out_2;
		$otherday['time_out_3']    =     $getOtherDayDate[0]->time_out_3;

	}

	if ( !empty($getYesterDayDate[0]) ) {
	
		//Get Yesterday clocking
		$yesterday['time_in_1']    =     $getYesterDayDate[0]->time_in_1;
		$yesterday['time_in_2']    =     $getYesterDayDate[0]->time_in_2;
		$yesterday['time_in_3']    =     $getYesterDayDate[0]->time_in_3;

		$yesterday['time_out_1']   =     $getYesterDayDate[0]->time_out_1;
		$yesterday['time_out_2']   =     $getYesterDayDate[0]->time_out_2;
		$yesterday['time_out_3']   =     $getYesterDayDate[0]->time_out_3;

	}

	//Get today Clocking
	$today['time_in_1']     =     $employeeClocking->time_in_1;
	$today['time_in_2']     =     $employeeClocking->time_in_2;
	$today['time_in_3']     =     $employeeClocking->time_in_3;

	$today['time_out_1']    =     $employeeClocking->time_out_1;
	$today['time_out_2']    =     $employeeClocking->time_out_2;
	$today['time_out_3']    =     $employeeClocking->time_out_3;

	//Get today clocking status
	$today['clocking_status'] = $employeeClocking->clocking_status;

	if(Request::ajax()) {

		if ( $data['timeclocking'] == 'in' ) {	

			// Check yesterday clocking status			
			if( !empty($getYesterDayDate[0]) ) {

				if ( $getYesterDayDate[0]->clocking_status === 'open' ||
					 $getYesterDayDate[0]->clocking_status === 'close' ||
					 $getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
					 $getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
					 $getYesterDayDate[0]->clocking_status === 'clock_out_3' ) {				

					echo getClockingInDateTime($today['clocking_status'], $currentDateTime);


				}

				// Yesterday clock out
				if ( $getYesterDayDate[0]->clocking_status === 'yesterday_clock_out' ) {

					echo getClockingInDateTimeYesterdayClockOut($today['clocking_status'], $currentDateTime);
					
				}

			} elseif ( empty($getYesterDayDate[0]) ) {
				
				if ( $today['clocking_status'] === 'open' ||
					$today['clocking_status'] === 'close' ||
					$today['clocking_status'] === 'clock_out_1' ||
					$today['clocking_status'] === 'clock_out_2' ||
					$today['clocking_status'] === 'clock_out_3' ) {					

					echo 'clocking status > open';
					getClockingInDateTime($today['clocking_status'], $currentDateTime);

				}

			}
													
		}


		if ( $data['timeclocking'] == 'out' ) {								

			if( !empty($getYesterDayDate[0]) ) {

				// Check yesterday clocking status
				if ( $getYesterDayDate[0]->clocking_status === 'open' ||
					$getYesterDayDate[0]->clocking_status === 'close' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_1' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_2' ||
					$getYesterDayDate[0]->clocking_status === 'clock_out_3' ) {
						
					echo getClockingOutDateTime($today['clocking_status'], $currentDateTime);


				}

				// Initialize: Forget to Clock out yesterday or 
				if ( $getYesterDayDate[0]->clocking_status === 'clock_in_1' ) {
					
					echo getClockingOutDateTimeForgotToClockOutClockIn1($today['clocking_status'], $currentDateTime, 'clock_in_1');
					
				} elseif ( $getYesterDayDate[0]->clocking_status === 'clock_in_2' ) {

					echo getClockingOutDateTimeForgotToClockOutClockIn2($today['clocking_status'], $currentDateTime, 'clock_in_2');


				} elseif ( $getYesterDayDate[0]->clocking_status === 'clock_in_3' ) {			

					getClockingOutDateTimeForgotToClockOutClockIn3($today['clocking_status'], $currentDateTime, 'clock_in_3');											

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

	//$employeeId = Auth::user()->employee_id;
	$employeeId = Session::get('userEmployeeId');	
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);
	//$getAllEmployee = $employee->getAllEmployee();		

	//$getAllEmployeeUser = DB::table('users')->get(); 	
	//return dd($user);

	$getUserEmployee = DB::table('users')            
        ->join('employees', 'users.employee_id', '=', 'employees.id')
        ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
        ->join('groups', 'users_groups.group_id', '=', 'groups.id')
        ->get();	

     //return dd($getUserEmployee);

	//Admin view
	//return View::make('admin.dashboard', ['employeeInfo' => $employeeInfo, 'getAllEmployee' => $getAllEmployee, 'getAllEmployeeUser' => $getAllEmployeeUser]);
	return View::make('admin.dashboard', ['employeeInfo' => $employeeInfo, 'getUserEmployee' => $getUserEmployee]);        

}));

Route::get('/admin/user/new', array('uses' => function()
{	
	
	//$employeeId = Auth::user()->employee_id;
	$employeeId = Session::get('userEmployeeId');
	
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	return View::make('admin.usernew',['employeeInfo' => $employeeInfo]);

}));

//https://scotch.io/tutorials/simple-laravel-crud-with-resource-controllers

//Route::post('/admin/user/createuser', array('uses' => function()
Route::post('/admin/user/new', array('uses' => function()
{	
	
	$data = Input::all();

	//return dd($data);
	//return $data["group"];

	/*$user = Sentry::register(array(
	    'email'    => $input['email'],
	    'password' => $input['password'],
	    'first_name' => $input['first_name'],
	    'last_name' => $input['last_name'],
	    'empnumber' => $input['empnumber'],
	    'group_name' => $input['group_name'],
	    'is_superuser' => $input['is_superuser'],
	    'activated' => $is_activated,
	    //'activated' => $input['activated'],
	),true);*/

	/*$SentryUser = Sentry::register(array(
	    'email'    => $input['email'],
	    'password' => $input['password'],
	    'first_name' => $input['first_name'],
	    'last_name' => $input['last_name'],
	    'empnumber' => $input['empnumber'],
	    'group_name' => $input['group_name'],
	    'is_superuser' => $input['is_superuser'],
	    'activated' => $is_activated,
	    //'activated' => $input['activated'],
	),true);*/

	
	//$employee = new Employee;
	//$employee->employee_number = $data["employee_number"];
	//$employee->firstname = $data["firstname"];
	//$employee->lastname = $data["lastname"];	
	//$employee->middle_name = $data["middlename"];
	//$employee->nick_name = $data["nick_name"];	

	//$user = new User;
	//$user->employee_id = $employee->id;
	//$user->employee_number = $data["employee_number"];
	//$user->first_name = $data["firstname"];
	//$user->last_name = $data["lastname"];
	//$user->email = $data["email"];
	//$user->password = Hash::make($data["password"]);
	
	//$user->remember_token
	//$user->permissions
	//$user->activated
	//$user->activation_code
	//$user->activate_at
	//$user->last_login
	//$user->persist_code
	//$user->reset_password_code	

	//if( $employee->save() ) {
		/*$SentryUser = Sentry::register(array(
		    'email'    => $data['email'],
		    'employee_id' => $employee->id,
		    'employee_number' => $data["employee_number"],
		    'password' => $data['password'],
		    'first_name' => $data['firstname'],
		    'last_name' => $data['lastname'],		  
		    //'activated' => $is_activated,
		    //'activated' => $data['activated'],
		), true);*/
	//}		

	$employee = new Employee;
	$employee->employee_number = $data["employee_number"];
	$employee->firstname = $data["firstname"];
	$employee->lastname = $data["lastname"];	
	$employee->middle_name = $data["middlename"];
	$employee->nick_name = $data["nick_name"];						

	if ( $employee->save() ) {
		try
		{
			// Create the user
			$SentryUser = Sentry::createUser(array(
			    'email'    => $data['email'],
			    'employee_id' => $employee->id,
			    'employee_number' => $data["employee_number"],
			    'password' => $data['password'],
			    'first_name' => $data['firstname'],
			    'last_name' => $data['lastname'],
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
							'hours_per_day' => 8.00
							
						));	

				if( isset($userId) ) {
					
					DB::table('users_groups')
							->insert(array(
								'user_id' => $userId, 
								'group_id' => $data["group"]
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

	$rules = array(
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

	}

}));


Route::get('/admin/user/{employeeId}/edit', array('as' => 'adminUserDelete', 'uses' => function($employeeId)
{	
	//return $employeeId;
	
	/*
	//$employeeId = Auth::user()->employee_id;
	//$employeeId = Session::get('userEmployeeId');	
	//$userId = Session::get('userId');
	*/
	$employee = new Employee;		
	$employeeInfo = $employee->getEmployeeInfoById($employeeId);

	$user = DB::table('users')->where('employee_id', $employeeId)->first(); 
	//return dd($user);

	$userGroup = DB::table('users_groups')->where('user_id', $user->id)->first(); 
	//return dd($userGroup);	

	$group = DB::table('groups')->where('id', (int) $userGroup->group_id)->first();  	
	
	return View::make('admin.useredit', ['employeeInfo' => $employeeInfo, 'user' => $user, 'group' => $group]);


}));


Route::post('/admin/user/edit', array('as' => 'adminUserDelete', 'uses' => function()
{
	
	$data = Input::all();

	//return dd($data);

	$employee = new Employee;
	$employeeUpdate = Employee::where('id', (int) $data["employee_id"])->first();

	$employeeUpdate->employee_number = $data["employee_number"];
	$employeeUpdate->firstname = $data["firstname"];
	$employeeUpdate->lastname = $data["lastname"];	
	$employeeUpdate->middle_name = $data["middlename"];
	$employeeUpdate->nick_name = $data["nick_name"];

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
		    
			$userUpdate = User::where('employee_id', $employeeId)->first();
			$userId = $userUpdate->id;

		    // Find the user using the user id
			$userUpdate = Sentry::findUserById($userId);

		    // Update the user details
			
			$userUpdate->employee_id = $employeeId;
			$userUpdate->employee_number = $data["employee_number"];
			$userUpdate->first_name = $data["firstname"];
			$userUpdate->last_name = $data["lastname"];
			$userUpdate->email = $data["email"];
			//$userUpdate->password = Hash::make($data["password"]);	

		    // Update the user
		    if ( $userUpdate->save() ) {
		     
		     	//return dd($userUpdate);
		     	//break;
		        // User information was updated
				//$userId = $userUpdate->id;	


				if( !empty($data["password"]) ) {

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
						'group_id' => $data["group"]
					));		

				}

				return Redirect::route('adminDashboard');		        

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

}));



Route::get('/admin/employee/timesheet', array('before' => 'auth', 'as' => 'adminEmployeeTimesheet', 'uses' => 'EmployeesController@showAdminEmployeeTimesheet'));

Route::get( '/redraw/admin/timesheet', array('as' => 'redrawAdminTimesheet', 'uses' => 'EmployeesController@redrawAdminEmployeeTimesheet') );

Route::post( '/admin/timesheet/save', array('as' => 'adminTimesheetSave', 'uses' => 'EmployeesController@adminTimesheetSave') );


