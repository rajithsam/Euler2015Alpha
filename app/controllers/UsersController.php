<?php

class UsersController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * GET /users
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /users/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /users
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /users/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /users/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /users/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /users/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


	//Additional method

	public function showLogin()
	{
	
		return View::make('users.index'); //Return back to login page		

	}

	public function doLogin()
	{

		/**
		* @Todo: Server side validation
		*/

		$employeeno = Input::get('employeeno');	
		$password = Input::get('password');

		/*$data['employeeno'] = $employeeno;
		$data['password'] = $password;

		$rules = array(
			'employee_number' => 'required',
			'password' => 'required'			
		);		

		$validator = Validator::make($data, $rules);		

	if ( $validator->fails() ) {

		$messages = $validator->messages();

	    return Redirect::to('users/login')->withErrors($validator);		

	} else {*/

	    /*$userdata = array(
	        'employee_number'     	=> $employeeno,
	        'password'  			=> $password
	    );*/
		
			// Login credentials
			$credentials = array(			
				'employee_number' => $employeeno,
				//'email'     => 'j.a@gmail.com',				
				'password' => $password,
			);
		
			try
			{
					
				// Authenticate the user
				$user = Sentry::authenticate($credentials, false);
				
				/*if($user){
					
					return Redirect::to('/employee/clocking');
					
				} else {
					
					return View::make('users.index'); //Return back to login page 	
				
				}*/	
			


		//if ( Auth::attempt($userdata) ) {					
		if($user){	

	        // validation successful!
	        // redirect them to the secure section or whatever
	        // return Redirect::to('secure');
	        // for now we'll just echo success (even though echoing in a controller is bad)
	        //echo 'SUCCESS!';
			
			//return $user->employee_id;
			
			Session::put('userEmployeeId', $user->employee_id);
			Session::put('userId', $user->id);			
			Session::put('email', $user->email);
			
			$user['employeeId'] = $user->employee_id;		

			/*$emplooyeeSetting = new Setting;	
			$getEmployeeSettingByEmployeeId = $emplooyeeSetting->getEmployeeSettingByEmployeeId();

			return dd($getEmployeeSettingByEmployeeId); //If no setting found the result will be NULL
			break;*/			

			$employee = new Employee;		
			$employeeInfo = $employee->getEmployeeInfoById($user['employeeId']);

			$workShift = new Workshift;
			//$employeeWorkShift = $workShift->getWorkShiftByEmployeeId($user['employeeId']); 

			//$dayOfTheWeek = date('l', strtotime($dayDate));
			//$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeInfo[0]->id, $dayOfTheWeek);	

						
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
			$currentMonth = date('M');
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
	

					if( in_array($currentDate, $cutoffArr1) ) {

						$currentCutoff = 1;
					
					}


					// /return $currentMonth.' - '.$cutoff['month'];					
					//$currentMonth
					//$cutoff['month'] = $adminCutoff->getCutoffbyYearMonth()->month;
					//$cutoff['type'] = $adminCutoff->getCutoffbyYearMonth()->cutoff_type;
					//$cutoff['dateFrom'][1] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_1;
					//$cutoff['dateTo'][1] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_1;		

					//$cutoff['dateFrom'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2;
					//$cutoff['dateTo'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2;				

					if ( strtotime($currentDate) !== strtotime($cutoff['dateFrom'][1]) ||
						 strtotime($currentDate) >= strtotime($cutoff['dateFrom'][1]) ) {

						//$lastMonth = date('M', strtotime('-1 month'));
						//$cutoff['month'] = $lastMonth;

						/*$cutoff['dateFrom'][2] = strtotime('-1 month' , strtotime($cutoff['dateFrom'][2]));
						$cutoff['dateFrom'][2] = date('Y-m-d' , $cutoff['dateFrom'][2]);

						$cutoff['dateTo'][2] = strtotime('-1 month' , strtotime($cutoff['dateTo'][2]));
						$cutoff['dateTo'][2] = date('Y-m-d' , $cutoff['dateTo'][2]);*/

						$cutoff['dateFrom'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_from_2;
						$cutoff['dateTo'][2] = $adminCutoff->getCutoffbyYearMonth()->cutoff_date_to_2;

						//return 'true';


					}
					//return strtotime($currentDate). ' - ' .strtotime($cutoff['dateFrom'][1]);
					//die();

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

					if( in_array($currentDate, $cutoffArr2) ) {

						$currentCutoff = 2;

					}

				}

			}	
			
			/*return $currentCutoff;
			die();*/

			//return dd( 'Current Cutoff: '. $currentCutoff.' From '.$cutoff['dateFrom'][2] .' - To:'. $cutoff['dateTo'][2] );
			//die();				

		
			$schedule = new Schedule;		
			$workShift = new Workshift;

			if ( $currentCutoff === 1 ) { ////1st CutOff - e.g 11-25

									
				//Check employee timesheet table if has the current date.
				$getDayDateResult = DB::table('employee_timesheet')->where('employee_id', $employeeInfo[0]->id)->where('daydate', $currentDate)->get();			

				if ( empty($getDayDateResult) ) {

					foreach ( $cutoffArr1 as $dayDate ) {
	
						//return dd(date('l', strtotime($dayDate)));						
						
						$dayOfTheWeek = date('l', strtotime($dayDate));
						
						$checkSchedule = $schedule->checkSchedule($employeeInfo[0]->id, $dayDate);
						$getSchedule = $schedule->getSchedule($employeeInfo[0]->id, $dayDate);		
						//$employeeWorkShift = $workShift->getWorkShiftByEmployeeId($employeeInfo[0]->id);	
						$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeInfo[0]->id, $dayOfTheWeek, 1);	

						if($checkSchedule) {

							$schedule['start_time'] = date('H:i:s', strtotime($getSchedule[0]->start_time));
							$schedule['end_time'] = date('H:i:s', strtotime($getSchedule[0]->end_time));							

						} elseif(!$checkSchedule) {

							if( !empty($getWorkShiftByDayOfTheWeek) ) {

								$schedule['start_time'] = date('H:i:s', strtotime($getWorkShiftByDayOfTheWeek[0]->start_time));
								$schedule['end_time'] = date('H:i:s', strtotime($getWorkShiftByDayOfTheWeek[0]->end_time));				

							} else {

								$schedule['start_time'] = '';
								$schedule['end_time'] = '';

							} 

						}

	        			$timesheetId = DB::table('employee_timesheet')
						->insertGetId(
							array(
							'employee_id' => $employeeInfo[0]->id,
							'daydate' => $dayDate,
							'schedule_in' => $schedule['start_time'],
							'schedule_out' => $schedule['end_time'],
							'night_shift_time_out' => 0,
							'clocking_status' => 'open'						
						));


						for ($i = 1; $i <= 3; $i++) {
							DB::table('overtime')
							->insert(
								array(
								'employee_id' => $employeeInfo[0]->id,
								'timesheet_id' => $timesheetId,
								'seq_no' => $i,
								'shift' => $i
							));							

						}					


						DB::table('employee_summary')
						->insert(
							array(
							'employee_id' => $employeeInfo[0]->id,
							'daydate' => $dayDate
						));


					}

				} else {					

					foreach ( $cutoffArr1 as $dayDate ) {

						//return 'debug.io';
						
						//return dd(date('l', strtotime($dayDate)));						
						
						$dayOfTheWeek = date('l', strtotime($dayDate));
						
						$checkSchedule = $schedule->checkSchedule($employeeInfo[0]->id, $dayDate);
						$getSchedule = $schedule->getSchedule($employeeInfo[0]->id, $dayDate);		
						//$employeeWorkShift = $workShift->getWorkShiftByEmployeeId($employeeInfo[0]->id);	
						$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeInfo[0]->id, $dayOfTheWeek, 1);	

						if($checkSchedule) {

							$schedule['start_time'] = date('H:i:s', strtotime($getSchedule[0]->start_time));
							$schedule['end_time'] = date('H:i:s', strtotime($getSchedule[0]->end_time));							

						} elseif(!$checkSchedule) {

							if( !empty($getWorkShiftByDayOfTheWeek) ) {

								$schedule['start_time'] = date('H:i:s', strtotime($getWorkShiftByDayOfTheWeek[0]->start_time));
								$schedule['end_time'] = date('H:i:s', strtotime($getWorkShiftByDayOfTheWeek[0]->end_time));				

							} else {

								$schedule['start_time'] = '';
								$schedule['end_time'] = '';

							} 

						}

						
						DB::table('employee_timesheet')
							->where('employee_id', $employeeInfo[0]->id)
							->where('daydate', $dayDate)
							->update(array('schedule_in' => $schedule['start_time'], 'schedule_out' => $schedule['end_time']));							
						
					}	

				}
				Session::put('debug', 'debug');			
				//echo Session::get('isLoginCheck');
				Session::put('employeesInfo', $employeeInfo);

				/*if( !empty($employeeWorkShift) ) {
					Session::put('employeeWorkShift', $employeeWorkShift); //check this out
				}*/

				if( !empty($getWorkShiftByDayOfTheWeek) ) {
					Session::put('getWorkShiftByDayOfTheWeek', $getWorkShiftByDayOfTheWeek); //check this out
				}						

				Session::put('dayDateArr', $cutoffArr1);
				
				return Redirect::route( 'employeeTimesheet');					

			} elseif ( $currentCutoff === 2 ) { ////1st CutOff - e.g 26-10

				//Check employee timesheet table if has the current date.
				$getDayDateResult = DB::table('employee_timesheet')->where('employee_id', $employeeInfo[0]->id)->where('daydate', $currentDate)->get();

				//return dd($getDayDateResult);

				if ( empty($getDayDateResult) ) {

					foreach ( $cutoffArr2 as $dayDate ) {

						//return dd(date('l', strtotime($dayDate)));						
						
						$dayOfTheWeek = date('l', strtotime($dayDate));
						
						$checkSchedule = $schedule->checkSchedule($employeeInfo[0]->id, $dayDate);
						$getSchedule = $schedule->getSchedule($employeeInfo[0]->id, $dayDate);		
						//$employeeWorkShift = $workShift->getWorkShiftByEmployeeId($employeeInfo[0]->id);	
						$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeInfo[0]->id, $dayOfTheWeek, 1);	

						if($checkSchedule) {

							$schedule['start_time'] = date('H:i:s', strtotime($getSchedule[0]->start_time));
							$schedule['end_time'] = date('H:i:s', strtotime($getSchedule[0]->end_time));							

						} elseif(!$checkSchedule) {

							if( !empty($getWorkShiftByDayOfTheWeek) ) {

								$schedule['start_time'] = date('H:i:s', strtotime($getWorkShiftByDayOfTheWeek[0]->start_time));
								$schedule['end_time'] = date('H:i:s', strtotime($getWorkShiftByDayOfTheWeek[0]->end_time));				

							} else {

								$schedule['start_time'] = '';
								$schedule['end_time'] = '';

							} 

						}

	        			$timesheetId = DB::table('employee_timesheet')
						->insertGetId(
							array(
							'employee_id' => $employeeInfo[0]->id,
							'daydate' => $dayDate,
							'schedule_in' => $schedule['start_time'],
							'schedule_out' => $schedule['end_time'],
							'night_shift_time_out' => 0,
							'clocking_status' => 'open'						
						));


						for ($i = 1; $i <= 3; $i++) {
							DB::table('overtime')
							->insert(
								array(
								'employee_id' => $employeeInfo[0]->id,
								'timesheet_id' => $timesheetId,
								'seq_no' => $i,
								'shift' => $i
							));							

						}						


						DB::table('employee_summary')
						->insert(
							array(
							'employee_id' => $employeeInfo[0]->id,
							'daydate' => $dayDate
						));

					}

				} else {					
					
					foreach ( $cutoffArr2 as $dayDate ) {

						//$getDayDateResult = DB::table('employee_timesheet')->where('employee_id', $employeeInfo[0]->id)->where('daydate', $currentDate)->get();

						//return dd($getDayDateResult);
					
						
						$dayOfTheWeek = date('l', strtotime($dayDate));
						
						$checkSchedule = $schedule->checkSchedule($employeeInfo[0]->id, $dayDate);
						$getSchedule = $schedule->getSchedule($employeeInfo[0]->id, $dayDate);		
						//$employeeWorkShift = $workShift->getWorkShiftByEmployeeId($employeeInfo[0]->id);	
						$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeInfo[0]->id, $dayOfTheWeek, 1);	

						if($checkSchedule) {

							$schedule['start_time'] = date('H:i:s', strtotime($getSchedule[0]->start_time));
							$schedule['end_time'] = date('H:i:s', strtotime($getSchedule[0]->end_time));							

						} elseif(!$checkSchedule) {

							if( !empty($getWorkShiftByDayOfTheWeek) ) {

								$schedule['start_time'] = date('H:i:s', strtotime($getWorkShiftByDayOfTheWeek[0]->start_time));
								$schedule['end_time'] = date('H:i:s', strtotime($getWorkShiftByDayOfTheWeek[0]->end_time));				

							} else {

								$schedule['start_time'] = '';
								$schedule['end_time'] = '';

							} 

						}

						DB::table('employee_timesheet')
							->where('employee_id', $employeeInfo[0]->id)
							->where('daydate', $dayDate)
							->update(array('schedule_in' => $schedule['start_time'], 'schedule_out' => $schedule['end_time']));							
						
					}					

				}
				
				Session::put('debug', 'debug');			
				//echo Session::get('isLoginCheck');
				Session::put('employeesInfo', $employeeInfo);

				/*if( !empty($employeeWorkShift) ) {
					Session::put('employeeWorkShift', $employeeWorkShift); //check this out
				}*/

				if( !empty($getWorkShiftByDayOfTheWeek) ) {
					Session::put('getWorkShiftByDayOfTheWeek', $getWorkShiftByDayOfTheWeek); //check this out
				}				

	

				Session::put('dayDateArr', $cutoffArr2);
				
				return Redirect::route( 'employeeTimesheet');					

			}
			
			//return Redirect::route( 'employeeTimesheet');			

 		} /*else {        

	        //validation not successful, send back to form 
	    	return Redirect::to('users.index'); //Return back to login page

    	}*/
		
		}
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
		{
			$getMessages = 'Login field is required.';
			return Redirect::to('users/login')->withErrors(array('login' => $getMessages));				
		}
		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
		{
			$getMessages = 'Password field is required.';
			return Redirect::to('users/login')->withErrors(array('login' => $getMessages));								
		}
		catch (Cartalyst\Sentry\Users\WrongPasswordException $e)
		{
			$getMessages = 'Wrong password, try again.';
			return Redirect::to('users/login')->withErrors($getMessages);								
		}
		catch (Cartalyst\Sentry\Users\UserNotFoundException $e)
		{
			$getMessages = 'User was not found.';
			return Redirect::to('users/login')->withErrors(array('login' => $getMessages));								
		}
		catch (Cartalyst\Sentry\Users\UserNotActivatedException $e)
		{
			$getMessages = 'User is not activated.';
			return Redirect::to('users/login')->withErrors(array('login' => $getMessages));								
		}		

    }
//} // validator

	public function doLogout()
	{				
		Sentry::logout();		
		Session::flush();
		return Redirect::to('users/login');
	}    		        	    		

}