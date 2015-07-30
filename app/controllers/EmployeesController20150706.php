<?php

class EmployeesController extends \BaseController {

	protected $table = 'employees';

	/**
	 * Display a listing of the resource.
	 * GET /employees
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /employees/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /employees
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /employees/{id}
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
	 * GET /employees/{id}/edit
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
	 * PUT /employees/{id}
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
	 * DELETE /employees/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	//Additional method
	
	public function showEmployee() {
	
		$employee = new Employee;
		$employee->getAllEmployee();
				
	}

	public function showEmployeeTimesheet() {

		Session::forget('searchEmployeeId');	

		//return Session::get('dayDateArr');
		$employeeId = Session::get('userEmployeeId');
		//$employeeId = Auth::user()->employee_id;

		$currentDate = date('Y-m-d');
		$dayOfTheWeek = date('l');
		$shift = 1;		

		$employee = new Employee;		
		$employeeInfo = $employee->getEmployeeInfoById($employeeId);
		//$workShift = new Workshift;
		//$employeeWorkShift = $workShift->getWorkShiftByEmployeeId($employeeId);	

		$timesheet = new Timesheet;		
		$employeeTimesheet = $timesheet->getAllRows($employeeId);						
		$getTimesheetById = $timesheet->getTimesheetById($employeeId, $currentDate);		
		$timesheetPerMonth = $timesheet->getTimesheetPerMonth($employeeId, Session::get('dayDateArr'));			

		//$schedule = new Schedule;		
		//$checkSchedule = $schedule->checkSchedule($employeeId, $currentDate);		
		//$getSchedule = $schedule->getSchedule($employeeId, $currentDate);

		//$getSchedule = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($currentDate))->get();
		//$getWorkShiftByDayOfTheWeek = DB::table('work_shift')->where('employee_id',  $employeeId)->where('name_of_day', $dayOfTheWeek)->where('shift', $shift)->get();
		//$getTimeSheet = DB::table('employee_timesheet')->where('employee_id', $employeeId)->where('daydate', trim($currentDate))->get();		

		//Employee view
		//return View::make('employees.clocking', ['employeeInfo' => $employeeInfo, 'employeeWorkShift' => $employeeWorkShift, 'employeeTimesheet' => $employeeTimesheet, 'getTimesheetById' => $getTimesheetById, 'timesheetPerMonth' => $timesheetPerMonth]);		
		return View::make('employees.clocking', ['employeeInfo' => $employeeInfo, 'employeeTimesheet' => $employeeTimesheet, 'getTimesheetById' => $getTimesheetById, 'timesheetPerMonth' => $timesheetPerMonth]);

	}

	public function showAdminEmployeeTimesheet() {
		
		$employeeId = Session::get('userEmployeeId');
		//$employeeId = Auth::user()->employee_id;

		$currentDate = date('Y-m-d');

		$employee = new Employee;		
		$employeeInfo = $employee->getEmployeeInfoById($employeeId);

		$workShift = new Workshift;
		$employeeWorkShift = $workShift->getWorkShiftByEmployeeId($employeeId);	

		$timesheet = new Timesheet;		
		$employeeTimesheet = $timesheet->getAllRows($employeeId);						
		$getTimesheetById = $timesheet->getTimesheetById($employeeId, $currentDate);		
		$timesheetPerMonth = $timesheet->getTimesheetPerMonth($employeeId, Session::get('dayDateArr'));			

		$schedule = new Schedule;		
		$checkSchedule = $schedule->checkSchedule($employeeId, $currentDate);		
		$getSchedule = $schedule->getSchedule($employeeId, $currentDate);	

		//Admin view
		return View::make('employees.admin.clocking', ['employeeInfo' => $employeeInfo, 'employeeWorkShift' => $employeeWorkShift, 'employeeTimesheet' => $employeeTimesheet, 'getTimesheetById' => $getTimesheetById, 'timesheetPerMonth' => $timesheetPerMonth]);

	}


	/*public function searchResultEmployeeTimesheet() {	

		$data = Input::get();

		//return Redirect::route('adminEmployeeTimesheet', array('searchemployeeid' => $data["searchemployeeid"]));

		//$employeeId = Session::get('userEmployeeId');
		//$employeeId = Auth::user()->employee_id;

		$employeeId = (int) $data["searchemployeeid"];

		$currentDate = date('Y-m-d');

		$employee = new Employee;		
		$employeeInfo = $employee->getEmployeeInfoById($employeeId);

		$workShift = new Workshift;
		$employeeWorkShift = $workShift->getWorkShiftByEmployeeId($employeeId);	

		$timesheet = new Timesheet;		
		$employeeTimesheet = $timesheet->getAllRows($employeeId);						
		$getTimesheetById = $timesheet->getTimesheetById($employeeId, $currentDate);		
		$timesheetPerMonth = $timesheet->getTimesheetPerMonth($employeeId, Session::get('dayDateArr'));			

		$schedule = new Schedule;		
		$checkSchedule = $schedule->checkSchedule($employeeId, $currentDate);		
		$getSchedule = $schedule->getSchedule($employeeId, $currentDate);	

		//Admin view
		return View::make('employees.admin.clockingsearch', ['searchemployeeid' => $data["searchemployeeid"], 'employeeInfo' => $employeeInfo, 'employeeWorkShift' => $employeeWorkShift, 'employeeTimesheet' => $employeeTimesheet, 'getTimesheetById' => $getTimesheetById, 'timesheetPerMonth' => $timesheetPerMonth]);		

	}*/

	public function redrawEmployeeTimesheet() {
		
		if( Request::ajax() ) {
			$employeeId = Session::get('userEmployeeId');
			//$employeeId = Auth::user()->employee_id;
			
			$timesheet = new Timesheet;
			$timesheetJsObjectPerMonth = $timesheet->getTimesheetJsObjectPerMonth($employeeId, Session::get('dayDateArr'));	
			
			//return '{"data":'.$timesheet->getTimesheetJsObject($employeeId).'}';
			//dd($timesheetJsObjectPerMonth);
			
			return $timesheetJsObjectPerMonth; //$timesheet->getTimesheetJsObjectPerMonth($employeeId, Session::get('dayDateArr'));

		}

	}

	public function loadEmployeeTimesheet() {
		
		//if(Request::ajax()){
			$employeeId = Session::get('userEmployeeId');
			//$employeeId = Auth::user()->employee_id;
			
			$timesheet = new Timesheet;
			$timesheetJsObjectPerMonth = $timesheet->getTimesheetJsObjectPerMonth($employeeId, Session::get('dayDateArr'));	
			
			//return '{"data":'.$timesheet->getTimesheetJsObject($employeeId).'}';
			//dd($timesheetJsObjectPerMonth);
			
			return $timesheetJsObjectPerMonth; //$timesheet->getTimesheetJsObjectPerMonth($employeeId, Session::get('dayDateArr'));

		//}

	}	

	public function redrawEmployeeSummary() {

		if(Request::ajax()){
			$employeeId = Session::get('userEmployeeId');
			//$employeeId = Auth::user()->employee_id;
			
			$summary = new Summary;
			$summaryPerMonth = $summary->getSummaryCutoff($employeeId, Session::get('dayDateArr'));						

			//var_dump($summaryPerMonth);
			return $summaryPerMonth;

		}		

	}	

	public function redrawSearchEmployeeTimesheet() {

		//$employeeId = (int) Session::get("searchEmployeeId");
		//$currentDate = date('Y-m-d');

		if ( Request::ajax() ) {
			
			$timesheet = new Timesheet;
			$timesheetJsObjectPerMonth = $timesheet->getSearchTimesheetJsObjectPerMonth(Session::get('searchEmployeeId'), Session::get('dayDateArr'));	
	
			return $timesheetJsObjectPerMonth;

		}

	}

	public function redrawSearchEmployeeSummary() {

		//$employeeId = (int) Session::get("searchEmployeeId");
		//$currentDate = date('Y-m-d');		

		if( Request::ajax() ) {
			//$employeeId = Session::get('userEmployeeId');
			//$employeeId = Auth::user()->employee_id;
			
			$summary = new Summary;
			$summaryPerMonth = $summary->getSearchSummaryCutoff(Session::get('searchEmployeeId'), Session::get('dayDateArr'));						

			//var_dump($summaryPerMonth);
			return $summaryPerMonth;

		}		

	}	

	public function redrawSearchEmployeeInfo() {	

		$employeeId = (int) Session::get("searchEmployeeId");

		//$employee = new Employee;		
		//$employeeInfo = $employee->getEmployeeInfoById($employeeId);	

		$employee = DB::table('employees')->where('id', $employeeId)->get(); 		
		
		$company = DB::table('employees')->where('id', $employee[0]->company_id)->get(); 
		$manager = DB::table('employees')->where('id', $employee[0]->manager_id)->get(); 
		$jobTitle = DB::table('job_title')->where('id', $employee[0]->position_id)->get(); 
		$department = DB::table('departments')->where('id', $employee[0]->department_id)->get(); 

		//$employeeId = Session::put('userEmployeeId', $employee[0]->id);
		
		if( Request::ajax() ) {

	        $output = '{';
	        $output .= '"data": [';
	        $output .= '{'; 

	        $output .= '"fullname": '. '"'.$employee[0]->firstname.', '.$employee[0]->lastname.'",';                
	        $output .= '"employeenumber": '. '"'.$employee[0]->employee_number.'",';
	       	$output .= '"designation": '. '"'.$jobTitle[0]->name.'",';
	       	$output .= '"team": '. '"'.$department[0]->name.'",';
	       	$output .= '"head": '. '"'.$manager[0]->firstname.', '.$manager[0]->lastname.'"';

	        $output .= '}'; 
	        $output .= ']';
	        $output .= '}';

	        return (string) $output; //json_encode($timeSheetObj);

        }   

	}


	public function adminTimesheetSave() {
		
		//value, id, row_id, column
		$data = Input::all();

		//	return dd($data);

		if(Request::ajax()) {

			//General Settings
			$nightDiff['from'] = strtotime('22:00');
			$nightDiff['to'] = strtotime('06:00');	
			
			$hasNightDiff = false;			

			//$dayOfTheWeek = date('l');

			$breakTime = date('H:i:s', strtotime('01:00:00'));
			
			$getTimesheet = Timesheet::where('id', (int) trim($data["row_id"]))->first();		

			//in-out 1			
			if((int) $data["column"] === 3) {
				
				//var_dump($data["value"]);

				if( !empty($data["value"]) ) {
					
					$dataValueArr = explode('-', $data["value"]);

					/*return dd(getTardinessTime($clockingIn, $shiftStart));
					exit;*/
					
					if(!empty($dataValueArr)) {

						//UPDARTED TIME IN AND TIME OUT
						$clockingIn = trim($dataValueArr[0]);
						$clockingOut = trim($dataValueArr[1]);
						
						$timesheetId = $getTimesheet->id;

						$employeeId = $getTimesheet->employee_id;
						$clockingStatus = $getTimesheet->clocking_status;

						$shiftStart = $getTimesheet->schedule_in;
						$shiftEnd = $getTimesheet->schedule_out;

						//$scheduleIn = $getTimesheet->schedule_in;
						//$scheduleOut = $getTimesheet->schedule_out;

						$dayDate = $getTimesheet->daydate;

						$tardiness = $getTimesheet->tardiness_1;
						
						//don't forget clocking status
						//check schedule


						//General Settings
						//$nightDiff['from'] = strtotime('22:00:00');
						//$nightDiff['to'] = strtotime('06:00:00');

						$timeInDateTime = date('G', strtotime($clockingIn));
						$timeOutDateTime = date('G', strtotime($clockingOut));

						//$clockingIn =  date( 'Y-m-d', strtotime($dayDate) ).' '.date( 'H:i:s', strtotime($clockingIn) );
						//$clockingOut =  date( 'Y-m-d', strtotime($dayDate) ).' '.date( 'H:i:s', strtotime($clockingOut) );						

						//e.g clockingIn result should be: 2015-05-15 15:30:13
						
						//$hour >= 21 && $hour <= 4
						//date('G', strtotime($nightDiff['from']))


						//$clockingIn =  date( 'Y-m-d', strtotime($dayDate) ).' '.date( 'H:i:s', strtotime($clockingIn) );
						//$clockingOut =  date( 'Y-m-d', strtotime($dayDate) ).' '.date( 'H:i:s', strtotime($clockingOut) );						

						//CHECK IF NIGHT DIFF TRUE
						if ( date('G', strtotime($shiftStart)) >= date('G', strtotime($nightDiff['from'])) &&
							 date('G', strtotime($shiftEnd)) <= date('G', strtotime($nightDiff['to'])) ) {

							//echo 'DEBUG.IO';
							
							if ( $timeInDateTime < 24 && $timeInDateTime < date('G', strtotime($nightDiff['from'])) ) {
								
								$clockingIn =  date( 'Y-m-d', strtotime($dayDate) ).' '.date( 'H:i:s', strtotime($clockingIn) );
								//$clockingIn =  date( 'Y-m-d', strtotime($dayDate) ).' '.date( 'H:i:s', strtotime($clockingIn) );

							} else {
															
								$clockingIn =  date( 'Y-m-d', strtotime($dayDate) ).' '.date( 'H:i:s', strtotime($clockingIn) );							

							}

							if ( $timeOutDateTime < 24 && $timeOutDateTime < date('G', strtotime($nightDiff['to'])) ) {
																	
								$clockingOut =  date( 'Y-m-d', strtotime($dayDate . '1 day') ).' '.date( 'H:i:s', strtotime($clockingOut) );
							
							} else {	

								$clockingOut =  date( 'Y-m-d', strtotime($dayDate) ).' '.date( 'H:i:s', strtotime($clockingOut) );					

							}

						} else {

							$clockingIn =  date( 'Y-m-d', strtotime($dayDate) ).' '.date( 'H:i:s', strtotime($clockingIn) );
							$clockingOut =  date( 'Y-m-d', strtotime($dayDate) ).' '.date( 'H:i:s', strtotime($clockingOut) );						

						}



						$dayOfTheWeek = date('l', strtotime($dayDate));
						$shift = 1;

						$schedule = new Schedule;
						//$getSchedule = $schedule->getSchedule($employeeId, $employeeClocking->daydate);
						$getSchedule = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($dayDate))->first();

						$workShift = new Workshift;
						//$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, date('l', strtotime($employeeClocking->daydate)), $overtime->shift);
						$getWorkShiftByDayOfTheWeek = DB::table('work_shift')->where('employee_id', $employeeId)->where('name_of_day', date('l', strtotime($dayDate)))->where('shift', $shift)->first();

						if( !empty($getSchedule) ) {

							//$scheduled['start_time'] = $getSchedule[0]->start_time;
							//$scheduled['end_time'] = $getSchedule[0]->end_time;
							$scheduled['rest_day'] = $getSchedule->rest_day;			

						} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {

							// From 01:00:00 change to 2015-04-30 09:00:00
							//$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeClocking[0]->time_in_1) ).' '.

							// From 01:00:00 change to 2015-04-30 01:00:00
							//$scheduled['end_time'] =  date( 'Y-m-d', strtotime($clockingDateTime) ).' '.

							$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek->rest_day;						

						}					

						$holiday = DB::table('holiday')->where('date', trim($dayDate))->first();
						//dd($holiday);

						$overtime = DB::table('overtime')
								->where('employee_id', $employeeId)
								->where('timesheet_id', $timesheetId)
								->where('seq_no', 1)
								->where('shift', 1)
								->where('overtime_status', 1)
								->get();

						//dd($overtime);	

						//CHECK IF HAS NIGHTDIFF		
						if ( strtotime($clockingIn) >= $nightDiff['from'] ||
							 strtotime($clockingOut) <= $nightDiff['to'] ) {

							$hasNightDiff = true;

						}							

						if ( strtotime(date('H:i', strtotime($clockingIn))) === strtotime(date('H:i', strtotime($shiftStart))) &&
							strtotime(date('H:i', strtotime($clockingOut))) === strtotime(date('H:i', strtotime($shiftEnd))) ) {

							DB::table('employee_timesheet')
								->where('id', $timesheetId)
								->update(array(							
										'time_in_1' => trim($clockingIn),
										'time_out_1' => trim($clockingOut),
										'tardiness_1' => '',
										'undertime_1' => '',
										'total_overtime_1' => '',
										'overtime_status_1'  => 'NULL'	//Apply OT																		
										));

							//Todo: update overtime table
							DB::table('overtime')
								->where('employee_id', $employeeId)
								->where('timesheet_id', $timesheetId)
								->where('seq_no', 1)
								->where('shift', 1)
								->update(array(																
										'overtime_status'  => 'NULL'	//Apply OT																		
										));		

						}

						//TARDINESS/LATES
						if( strtotime($clockingIn) > strtotime($shiftStart) ) {

							$tardinessTime = getTardinessTime($clockingIn, $shiftStart);

							DB::table('employee_timesheet')
								->where('id', $timesheetId)
								->update(array(					
										'time_in_1' => trim($clockingIn),
										'time_out_1' => trim($clockingOut),																			
										'tardiness_1' => $tardinessTime,
										'total_overtime_1' => '',
										'overtime_status_1'  => 'NULL'	//Apply OT										
										));

							//Todo: update overtime table
							DB::table('overtime')
								->where('employee_id', $employeeId)
								->where('timesheet_id', $timesheetId)
								->where('seq_no', 1)
								->where('shift', 1)
								->update(array(																
										'overtime_status'  => 'NULL'	//Apply OT																		
										));

							DB::table('employee_summary')
								->where('employee_id', $employeeId)
								->where('daydate', $dayDate)
								->update(array(												
										'lates' => $tardinessTime
										));												

											
							$getUpdatedTimesheet = Timesheet::where('id', (int) trim($timesheetId))->first();														

						} else {

							DB::table('employee_timesheet')
								->where('id', $timesheetId)
								->update(array(			
										'time_in_1' => trim($clockingIn),
										'time_out_1' => trim($clockingOut),																					
										'tardiness_1' => '',										
										));

							DB::table('employee_summary')
								->where('employee_id', $employeeId)
								->where('daydate', $dayDate)
								->update(array(												
										'lates' => ''
										));																					

						}				

						//dd($getUpdatedTimesheet);

						if( !empty($getUpdatedTimesheet) ) {

							//LATE/TARDINESS: TRUE
							if ( !empty($getUpdatedTimesheet->tardiness_1) ) {

								//echo "LATE/TARDINESS: TRUE \n";
								//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
								$workHours = getWorkHours($clockingIn, $clockingOut, $shiftEnd);												

								//TODO: Compute total hours with out overtime - getTotalHours
								$totalHours = getTotalHours($clockingIn, $clockingOut, $shiftEnd);												

								DB::table('employee_timesheet')
									->where('id', $timesheetId)
									->update(array(
											'time_in_1' => trim($clockingIn),
											'time_out_1' => trim($clockingOut),																																														
											'work_hours_1' => $workHours,
											'total_hours_1' => $totalHours
											));

								//Todo: update overtime table
								DB::table('overtime')
									->where('employee_id', $employeeId)
									->where('timesheet_id', $timesheetId)
									->where('seq_no', 1)
									->where('shift', 1)
									->update(array(																
											'overtime_status'  => 'NULL'	//Apply OT																		
											));																											

							} 

						} else {

							//echo "LATE/TARDINESS: FALSE \n";
							//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours						
							$workHours = getWorkHours($clockingIn, $clockingOut, $shiftEnd);						

							//TODO: Compute total hours with overtime - getTotalHours
							$totalHours = getTotalHours($clockingIn, $clockingOut, $shiftEnd);

							DB::table('employee_timesheet')
								->where('id', $timesheetId)
								->update(array(
										'time_in_1' => trim($clockingIn),
										'time_out_1' => trim($clockingOut),																																													
										'work_hours_1' => $workHours,
										'total_hours_1' => $totalHours
										));

						}

						//UNDERTIME: TRUE
						if( strtotime($clockingOut) < strtotime($shiftEnd) ) {

							//echo "UNDERTIME: TRUE \n";
							$undertime = getUnderTimeHours($clockingIn, $clockingOut, $shiftStart, $shiftEnd);

							DB::table('employee_timesheet')
								->where('id', $timesheetId)
								->update(array(
										'time_in_1' => trim($clockingIn),
										'time_out_1' => trim($clockingOut),																																												
										'undertime_1' => $undertime,
										'total_overtime_1' => '',
										'overtime_status_1'  => 'NULL'	//Apply OT																					
										));

							//Todo: update overtime table
							DB::table('overtime')
								->where('employee_id', $employeeId)
								->where('timesheet_id', $timesheetId)
								->where('seq_no', 1)
								->where('shift', 1)
								->update(array(																
										'overtime_status'  => 'NULL'	//Apply OT																		
										));


							DB::table('employee_summary')
								->where('employee_id', $employeeId)
								->where('daydate', $dayDate)
								->update(array(												
										'undertime' => $undertime
										));																																											

						} else {

							DB::table('employee_timesheet')
								->where('id', $timesheetId)
								->update(array(			
										'time_in_1' => trim($clockingIn),
										'time_out_1' => trim($clockingOut),																																									
										'undertime_1' => '',																			
										));

							DB::table('employee_summary')
								->where('employee_id', $employeeId)
								->where('daydate', $dayDate)
								->update(array(												
										'undertime' => ''
										));																					

						}					

						//OVERTIME: TRUE
						$isOvertime = false;

						/*if ( date('H:i', strtotime($clockingIn)) <= date('H:i', strtotime($shiftStart)) &&
							 date('H:i', strtotime($clockingOut)) > date('H:i', strtotime($shiftEnd)) ) {*/

						if ( strtotime($clockingIn) <= strtotime($shiftStart) &&
							 strtotime($clockingOut) > strtotime($shiftEnd) ) {						

						    //echo "OVERTIME: TRUE \n";

						    $isOvertime = true;

						    $totalOvertime = getOvertimeHours($clockingOut, $shiftEnd);

							DB::table('employee_timesheet')
								->where('id', $timesheetId)
								->update(array(			
										'total_overtime_1' => $totalOvertime,
										'time_in_1' => trim($clockingIn),
										'time_out_1' => trim($clockingOut),																																									
										'tardiness_1' => '',
										'undertime_1' => '',
										'overtime_status_1'  => 'NULL' //Apply OT
										));	

							//Todo: update overtime table
							DB::table('overtime')
								->where('employee_id', $employeeId)
								->where('timesheet_id', $timesheetId)
								->where('seq_no', 1)
								->where('shift', 1)
								->update(array(																
										'overtime_status'  => 'NULL'	//Apply OT																		
										));																									

						}




						//dd($holiday);

						//REST DAY: FALSE
						if ( $scheduled['rest_day'] !== 1 ) {					

							//HOLIDAY: TRUE
							if ( !empty($holiday) ) {

								if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

									//echo "Regular holiday \n";

									$totalHours = getTotalHours($clockingIn, $clockingOut, $shiftEnd);

									if(!$isOvertime) { //ISOVERTIME: FALSE
													
										DB::table('employee_summary')
											->where('employee_id', $employeeId)
											->where('daydate', $dayDate)
											->update(array(												
													'legal_holiday_night_diff' => $totalHours
													));								
										
									}			


								} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day
									

									//echo "Special non-working day \n";

									$totalHours = getTotalHours($clockingIn, $clockingOut, $shiftEnd);

									if(!$isOvertime) { //ISOVERTIME: FALSE

										DB::table('employee_summary')
											->where('employee_id', $employeeId)
											->where('daydate', $dayDate)
											->update(array(												
													'special_holiday_night_diff' => $totalHours
													));																
										
									}	

								}
							
							//HOLIDAY: FALSE
							} else { //Regular Day

								//echo "HOLIDAY: FALSE \n";

								//echo "Regular Day \n";		

								$totalHours = getTotalHours($clockingIn, $clockingOut, $shiftEnd);

								if (!$isOvertime) { //ISOVERTIME: FALSE
				
									DB::table('employee_summary')
										->where('employee_id', $employeeId)
										->where('daydate', $dayDate)
										->update(array(												
												'regular_night_differential' => $totalHours
												));								
							
								}

							}

						//REST DAY: TRUE
						} elseif ( $scheduled['rest_day'] === 1 ) {

							//HOLIDAY: TRUE
							if ( !empty($holiday) ) {

								if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday

									//echo "Regular holiday \n";

									$totalHours = getTotalHours($clockingIn, $clockingOut, $shiftEnd);

									if(!$isOvertime) { //ISOVERTIME: FALSE
													
										DB::table('employee_summary')
											->where('employee_id', $employeeId)
											->where('daydate', $dayDate)
											->update(array(												
													'rest_day_legal_holiday_night_diff' => $totalHours
													));								
										
									}			


								} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day
									

									//echo "Special non-working day \n";

									$totalHours = getTotalHours($clockingIn, $clockingOut, $shiftEnd);

									if(!$isOvertime) { //ISOVERTIME: FALSE

										DB::table('employee_summary')
											->where('employee_id', $employeeId)
											->where('daydate', $dayDate)
											->update(array(												
													'rest_day_special_holiday_night_diff' => $totalHours
													));																
										
									}	

								}
							
							//HOLIDAY: FALSE
							} else { //Regular Day

								//echo "HOLIDAY: FALSE \n";

								//echo "Regular Day \n";		

								$totalHours = getTotalHours($clockingIn, $clockingOut, $shiftEnd);

								if (!$isOvertime) { //ISOVERTIME: FALSE
				
									DB::table('employee_summary')
										->where('employee_id', $employeeId)
										->where('daydate', $dayDate)
										->update(array(												
												'rest_day_night_differential' => $totalHours
												));								
							
								}

							}

						}


						//OVERTIME TRUE
						if( !empty($overtime) ) {


							//REST DAY: FALSE
							if ( $scheduled['rest_day'] !== 1 ) {					

								//HOLIDAY: TRUE
								if ( !empty($holiday) ) {

									if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday


										if ( !$hasNightDiff ) {

											$update = array('legal_holiday_overtime' => $totalOvertime);

										} elseif ( $hasNightDiff ) {

											$update = array('legal_holiday_overtime_night_diff' => $totalOvertime);

										}

										DB::table('employee_summary')
											->where('employee_id', $employeeId)
											->where('daydate', $dayDate)
											->update($update);																				


									} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day
										

										if ( !$hasNightDiff ) {

											$update = array('legal_holiday_overtime' => $totalOvertime);

										} elseif ( $hasNightDiff ) {

											$update = array('legal_holiday_overtime_night_diff' => $totalOvertime);

										}

										DB::table('employee_summary')
											->where('employee_id', $employeeId)
											->where('daydate', $dayDate)
											->update($update);																				


									}
								
								//HOLIDAY: FALSE
								} else { //Regular Day

									//echo "HOLIDAY: FALSE \n";

									if ( !$hasNightDiff ) {

										$update = array('regular_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('regular_overtime_night_diff' => $totalOvertime);

									}

									DB::table('employee_summary')
										->where('employee_id', $employeeId)
										->where('daydate', $dayDate)
										->update($update);																	

								}

							//REST DAY: TRUE
							} elseif ( $scheduled['rest_day'] === 1 ) {

								//HOLIDAY: TRUE
								if ( !empty($holiday) ) {

									if ( 'Regular holiday' === $holiday->holiday_type ) { //Regular holiday


										if ( !$hasNightDiff ) {

											$update = array('rest_day_legal_holiday_overtime' => $totalOvertime);

										} elseif ( $hasNightDiff ) {

											$update = array('rest_day_legal_holiday_overtime_night_diff' => $totalOvertime);

										}

										DB::table('employee_summary')
											->where('employee_id', $employeeId)
											->where('daydate', $dayDate)
											->update($update);																				


									} elseif ( 'Special non-working day' === $holiday->holiday_type ) { //Special non-working day
										

										if ( !$hasNightDiff ) {

											$update = array('rest_day_legal_holiday_overtime' => $totalOvertime);

										} elseif ( $hasNightDiff ) {

											$update = array('rest_day_legal_holiday_overtime_night_diff' => $totalOvertime);

										}

										DB::table('employee_summary')
											->where('employee_id', $employeeId)
											->where('daydate', $dayDate)
											->update($update);																				


									}
								
								//HOLIDAY: FALSE
								} else { //Regular Day

									//echo "HOLIDAY: FALSE \n";

									if ( !$hasNightDiff ) {

										$update = array('rest_day_overtime' => $totalOvertime);

									} elseif ( $hasNightDiff ) {

										$update = array('rest_day_overtime_night_diff' => $totalOvertime);

									}

									DB::table('employee_summary')
										->where('employee_id', $employeeId)
										->where('daydate', $dayDate)
										->update($update);																	

								}

							}



						} else {



						}



					} else {
						
						return "Not Allowed";
											
					}
										
				} else {
				
					return "Not Allowed";
				}

			}
			
			//in-out 2		
			if((int) $data["column"] === 4) {
				
				if( !empty($data["value"]) ) {			

					$dataValueArr = explode('-', $data["value"]);
					return DB::table('employee_timesheet')
								->where('id', (int) $data["row_id"])
								->update(array(							
										'time_in_2' => trim($dataValueArr[0]),
										'time_out_2' => trim($dataValueArr[1])
										));
										
				}

			}
			
	    	//in-out 3
			if((int) $data["column"] === 5) {
				
				if( !empty($data["value"]) ) {			
				
					$dataValueArr = explode('-', $data["value"]);
					return DB::table('employee_timesheet')
								->where('id', (int) $data["row_id"])
								->update(array(							
										'time_in_3' => trim($dataValueArr[0]),
										'time_out_3' => trim($dataValueArr[1])
										));
				}

			}


		//return Redirect::to('/redraw/timesheet');

		} 		
					
	}



    public function updateServerTime() {

	    if(Request::ajax()){

	        //return date('H:i:s A');

	        //return date('H:i:s');
	        return date('H:').date('i').'<span class="" style="font-size:21px; margin-top:0px;">'.date('s').'</span>';

	    }
    }
    
    /*public function updateServerTime() {
        //echo getServerTime();
    }*/

    //http://www.pontikis.net/tip/?id=18
    public function getServerDateTime() {
    	
		if(Request::ajax()){
	
			$dateTime = new DateTime();
			//return $dateTime->format('Y-m-d H:i:s');

			return $dateTime->format('D, M d');
	
		}

	}


	public function postShift()
	{
			$path = public_path();
	        $allowedExts = array("xls","xlsx","csv");
			$temp = explode(".", $_FILES["file"]["name"]);
	        $extension = end($temp);
	        //$filename= $temp[0];
	        $filename= $temp[0] . '_' . date('YmdHis') . '_' . rand(111,999);
	        $destinationPath =  $path .'/uploads/'.$filename.'.'.$extension;

	        if(in_array($extension, $allowedExts)&&($_FILES["file"]["size"] < 20000000)) {
	              
	            if($_FILES["file"]["error"] > 0) {
	            
	                //echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
	            
	            } else {
	                 
	                  //if (file_exists($path."/uploads/" . $_FILES["file"]["name"]))
	              	 if (file_exists($path."/uploads/" . $filename)) {
	                 
	                    //echo $_FILES["file"]["name"] . " already exists. ";
	                 
	                 } else {
	                    $uploadSuccess = move_uploaded_file($_FILES["file"]["tmp_name"],$destinationPath);


	                        Excel::load($destinationPath, function($reader) {

						        //$results = Excel::load('public/products2.xls')->get();
						        $result = $reader->all();
						        $reader->toArray();

						       //$reader->dump();

						        // Loop through all the sheets
						        $reader->each(function($sheet) {

							        // Loop through all rows
						            $sheet->each(function($row) {

							            //$date = $row->schedule_date;
										//$scheduleDate = date('Y-m-d', strtotime($schedule_date));

						                $schedule = new Schedule;
						                $schedule->employee_id = $row->employee_id;
						                $schedule->year = $row->year;
						                $schedule->month = $row->month;
						                $schedule->day = $row->day;
						                $schedule->day = $row->shift;
						                $schedule->day = $row->rest_day;
						                //$schedule->day = $row->hours_per_day;
						                $schedule->start_time = $row->start_time;
						                $schedule->end_time = $row->end_time;
						                $schedule->schedule_date = date('Y-m-d', strtotime($row->schedule_date));						                
						                $schedule->save();

						                //Redirect::action('EmployeesController@index');

							        });

						        });

						    });

	                    /*if( $uploadSuccess )
	                    {
	                       $document_details=Response::json(Author::insert_document_details_Call($filename,$destinationPath));
	                       return $document_details; // or do a redirect with some message that file was uploaded
	                    }
	                    else
	                    {
	                       return Response::json('error', 400);
	                    }*/
	                    Session::flash('error', 'Uploading of employee is successful');
	                    //return Redirect::action('EmployeesController@indexshift');
	                    return Redirect::route('adminScheduling');
	                }
	               
	            }
	                
	        } else {
	            
	            return "Invalid file";
	        }
	}		

}