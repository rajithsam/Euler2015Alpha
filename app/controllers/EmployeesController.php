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

		if(Request::ajax()) {

			//in 1 and out 1
			if( (int) $data["column"] === 3 ) {

				$this->updateTimesheetTimeIn1( $data, $data["column"] );

			} elseif( (int) $data["column"] === 5 ) {

				$this->updateTimesheetTimeOut1( $data, $data["column"] );

			}

			//in 2 and out 2
			if( (int) $data["column"] === 6 ) {

				$this->updateTimesheetTimeIn2( $data, $data["column"] );

			} elseif( (int) $data["column"] === 8 ) {

				$this->updateTimesheetTimeOut2( $data, $data["column"] );

			}


			//in 3 and out 3
			if( (int) $data["column"] === 9 ) {

				$this->updateTimesheetTimeIn3( $data, $data["column"] );

			} elseif( (int) $data["column"] === 11 ) {

				$this->updateTimesheetTimeOut3( $data, $data["column"] );

			}			

			//return Redirect::to('/redraw/timesheet');

		} 		
					
	}


public function updateTimesheetTimeIn1( $data = '', $column = NULL ) {

	//Settings
	$nightDiff['from'] = strtotime('22:00:00');
	$nightDiff['to'] = strtotime('06:00:00');
	$breakTime = date('H:i:s', strtotime('01:00:00'));

	$shift = 1;	
	
	$hasNightDiff = false;

	$getTimesheet = Timesheet::where('id', (int) trim($data["row_id"]))->first();					

	$clockingIn = $getTimesheet->time_in_1;
	$clockingOut = $getTimesheet->time_out_1;

	$timeInHour = date('G', strtotime($clockingIn)); //24-hour format of an hour without leading zeros
	$timeOutHour = date('G', strtotime($clockingOut)); //24-hour format of an hour without leading zeros	

	$timesheetId = $getTimesheet->id;
	$employeeId = $getTimesheet->employee_id;
	$clockingStatus = $getTimesheet->clocking_status;
	//$shiftStart = $getTimesheet->schedule_in;
	//$shiftEnd = $getTimesheet->schedule_out;
	$dayDate = $getTimesheet->daydate;
	$tardiness = $getTimesheet->tardiness_1;

	$dayOfTheWeek = date('l', strtotime($dayDate));

	$getSummary = Summary::where('employee_id', $employeeId)->where('daydate', trim($dayDate))->first();

	/**
	* if !empty time in 1 and  empty time in 1
	* or 
	* if clocking status = clock_in_1
	*
	* check tardiness
	* compute tardiness update timesheet/summary tardiness/lates
	*/

	//if ( !empty($clockingIn) && empty($clockingOut) &&
	//	 $clockingStatus === 'clock_in_1' ) {

	/*	$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
		$getTimesheet->time_in_1 = $clockingDateTime;
		$getTimesheet->clocking_status = 'clock_in_1';

		$schedule = new Schedule;
		$getSchedule = DB::table('employee_schedule')
		->where('employee_id', $employeeId)
		->where('schedule_date', trim($dayDate))->first();

		$workShift = new Workshift;		
		$getWorkShiftByDayOfTheWeek = DB::table('work_shift')
		->where('employee_id', $employeeId)->where('name_of_day', date('l', strtotime($dayDate)))
		->where('shift', $shift)->first();

		$holiday = new Holiday;
		$getHolidayByDate = $holiday->getHolidayByDate($dayDate);		

		if( !empty($getSchedule) ) {

			$scheduled['start_time'] = $getSchedule->start_time;
			$scheduled['end_time'] = $getSchedule->end_time;			
			$scheduled['rest_day'] = $getSchedule->rest_day;	

			$startTime = date('H:i:s', strtotime($scheduled['start_time']));

		} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
			
			//$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek->start_time;
			//$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek->end_time;				

			// From 01:00:00 change to 2015-04-30 09:00:00
			$scheduled['start_time'] = date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek->start_time;

			// From 01:00:00 change to 2015-04-30 01:00:00
			$scheduled['end_time'] =  date( 'Y-m-d', strtotime($getTimesheet->time_out_1) ).' '.$getWorkShiftByDayOfTheWeek->end_time;

			$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek->rest_day;						

			$startTime = $scheduled['start_time'];

		}	

		if( strtotime(date('H:i', strtotime($clockingDateTime))) === 
			strtotime(date('H:i', strtotime($scheduled['start_time']))) ) { //WITHOUT TARDINESS

			$getTimesheet->tardiness_1 = '';							
			$getSummary->lates = '';	

			//return dd('//WITHOUT TARDINESS');

		} elseif( strtotime(date('H:i', strtotime($clockingDateTime))) > 
			strtotime(date('H:i', strtotime($scheduled['start_time']))) ) { //WITH TARDINESS

			$clockingIn = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
			$tardinessTime = getTardinessTime($clockingIn, $startTime, true);

			if( !empty($tardinessTime) ) {

				$getTimesheet->tardiness_1 = $tardinessTime;							
				$getSummary->lates = $tardinessTime;	

			}

		}

		if ( $getTimesheet->save() ) {

			$getSummary->save();
			
			return Redirect::to('/redraw/timesheet');			

		}*/			

	//} elseif ( !empty($clockingIn) && !empty($clockingOut) &&
	//	 $clockingStatus === 'clock_out_1' ) {

		//return dd($data);
		//die();

		//Todo Add a condition here
		if ( !empty($data['value']) && !empty($getTimesheet->time_out_1) ) {
			
			$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
			$getTimesheet->time_in_1 = $clockingDateTime;
			$getTimesheet->clocking_status = 'clock_out_1';	

		} elseif ( !empty($data['value']) && empty($getTimesheet->time_out_1) ) {

			$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
			$getTimesheet->time_in_1 = $clockingDateTime;
			$getTimesheet->clocking_status = 'clock_in_1';	

		} elseif ( empty($data['value']) && !empty($getTimesheet->time_out_1) ) {

			$clockingDateTime = '';
			$getTimesheet->time_in_1 = $clockingDateTime;
			$getTimesheet->clocking_status = 'clock_in_1';				

		} elseif ( empty($data['value']) && empty($getTimesheet->time_out_1) ) {

			$clockingDateTime = '';
			$getTimesheet->time_in_1 = '';
			$getTimesheet->clocking_status = 'close';

		}			

		$schedule = new Schedule;
		$getSchedule = DB::table('employee_schedule')
		->where('employee_id', $employeeId)
		->where('schedule_date', trim($dayDate))->first();

		$workShift = new Workshift;		
		$getWorkShiftByDayOfTheWeek = DB::table('work_shift')
		->where('employee_id', $employeeId)->where('name_of_day', date('l', strtotime($dayDate)))
		->where('shift', $shift)->first();

		$holiday = new Holiday;
		$getHolidayByDate = DB::table('holiday')->where('date', trim($dayDate))->first();		

		if( !empty($getSchedule) ) {

			$scheduled['start_time'] = $getSchedule->start_time;
			$scheduled['end_time'] = $getSchedule->end_time;			
			$scheduled['rest_day'] = $getSchedule->rest_day;	

			$startTime = date('H:i:s', strtotime($scheduled['start_time']));

		} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
			
			//$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek->start_time;
			//$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek->end_time;				

			// From 01:00:00 change to 2015-04-30 09:00:00
			$scheduled['start_time'] = date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek->start_time;

			// From 01:00:00 change to 2015-04-30 01:00:00
			$scheduled['end_time'] =  date( 'Y-m-d', strtotime($getTimesheet->time_out_1) ).' '.$getWorkShiftByDayOfTheWeek->end_time;

			$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek->rest_day;						

			$startTime = $scheduled['start_time'];

		}	

		$clockingIn = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));

		if( strtotime(date('H:i', strtotime($clockingDateTime))) === 
			strtotime(date('H:i', strtotime($scheduled['start_time']))) ) { //WITHOUT TARDINESS

			$getTimesheet->tardiness_1 = '';							
			$getSummary->lates = '';	

			//return dd('//WITHOUT TARDINESS');

		} elseif( strtotime(date('H:i', strtotime($clockingDateTime))) > 
			strtotime(date('H:i', strtotime($scheduled['start_time']))) ) { //WITH TARDINESS

			
			$tardinessTime = getTardinessTime($clockingIn, $startTime, true);

			if( !empty($tardinessTime) ) {

				$getTimesheet->tardiness_1 = $tardinessTime;							
				$getSummary->lates = $tardinessTime;	

			}

		}


		//SCHEDULED : TRUE
		/*if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {


			//REST DAY: FALSE
			if ( $scheduled['rest_day'] !== 1 ) {

				//LATE/TARDINESS: TRUE
				if ( !empty($getTimesheet->tardiness_1) ) {

					echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																																						

				
				//LATE/TARDINESS: FALSE
				} else {

					echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																					

				}

				
				
				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $clockingDateTime);
				$timeOutArr = explode(' ', $getTimesheet->time_out_1);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i', strtotime($clockingDateTime)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i', strtotime($getTimesheet->time_out_1)));

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			            $getTimesheet->night_differential_1 = 8.00;

			        } else {

			            $getTimesheet->night_differential_1 = $workHours;

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        } else {

						$getTimesheet->night_differential_1 = $workHours;

			        }
			    }

				//UNDERTIME: TRUE
				if ( strtotime($getTimesheet->time_out_1) < strtotime($scheduled['end_time']) ) {

					echo "UNDERTIME: TRUE \n";
					echo $getTimesheet->undertime_1 = getUnderTimeHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['start_time'], $scheduled['end_time']);																			
					$getSummary->undertime = getUnderTimeHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['start_time'], $scheduled['end_time']);																			

				} else {

					echo "UNDERTIME: FALSE \n";
					echo $getTimesheet->undertime_1 = '';																			
					$getSummary->undertime = '';																			

				}		    				


			//REST DAY: TRUE
			} elseif ( $scheduled['rest_day'] === 1 ) {


			}
		}*/


		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {


			//REST DAY: FALSE
			if ( $scheduled['rest_day'] !== 1 ) {

				//LATE/TARDINESS: TRUE
				if ( !empty($getTimesheet->tardiness_1) ) {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																																						

				
				//LATE/TARDINESS: FALSE
				} else {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																					

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $clockingDateTime);
				$timeOutArr = explode(' ', $getTimesheet->time_out_1);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i', strtotime($clockingDateTime)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i', strtotime($getTimesheet->time_out_1)));

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			            $getTimesheet->night_differential_1 = 8.00;

			        } else {

			            $getTimesheet->night_differential_1 = $workHours;

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        } else {

						$getTimesheet->night_differential_1 = $workHours;

			        }

			    } /*else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        }
		        
		    	}*/		    

				//UNDERTIME: TRUE
				if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					//echo "UNDERTIME: TRUE \n";
					$getTimesheet->undertime_1 = getUnderTimeHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['start_time'], $scheduled['end_time']);																			
					$getSummary->undertime = getUnderTimeHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['start_time'], $scheduled['end_time']);																			

				} else {

					//echo "UNDERTIME: FALSE \n";
					$getTimesheet->undertime_1 = '';																			
					$getSummary->undertime = '';																			

				}		    				

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($clockingDateTime)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($getTimesheet->time_out_1)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    ////echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$getTimesheet->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				} else {

					$isOvertime = false;
					$getTimesheet->total_overtime_1 = '';				
				}

				//HOLIDAY: TRUE
				if( hasHoliday($dayDate) ) {

					 ////echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

						//echo "Regular holiday \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->legal_holiday = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);															
							$getSummary->legal_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->legal_holiday = '';

						}						


					} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day
						

						//echo "Special non-working day \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->special_holiday = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																		
							$getSummary->special_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->special_holiday = '';

						}	

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					//echo "HOLIDAY: FALSE \n";

					//echo "Regular Day \n";		

					if (!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->regular = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																																	
						$getSummary->regular_overtime = '';

						$getSummary->legal_holiday = '';
						$getSummary->special_holiday = '';						
						
					} else { //ISOVERTIME: TRUE

						$getSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->regular = '';

						$getSummary->legal_holiday_overtime = '';	
						$getSummary->special_holiday_overtime = '';												

					}					

				}									

			//REST DAY: TRUE
			} elseif ( $scheduled['rest_day'] === 1 ) {


				//LATE/TARDINESS: TRUE
				if ( !empty($getTimesheet->tardiness_1) ) {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																																						

				
				//LATE/TARDINESS: FALSE
				} else {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																					

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $clockingDateTime);
				$timeOutArr = explode(' ', $getTimesheet->time_out_1);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i', strtotime($clockingDateTime)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i', strtotime($getTimesheet->time_out_1)));

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			            $getTimesheet->night_differential_1 = 8.00;

			        } else {

			            $getTimesheet->night_differential_1 = $workHours;

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        } else {

						$getTimesheet->night_differential_1 = $workHours;

			        }

			    } else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        }
		        
		    	}		    

				//UNDERTIME: TRUE
				if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					//echo "UNDERTIME: TRUE \n";
					$getTimesheet->undertime_1 = getUnderTimeHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['start_time'], $scheduled['end_time']);																			
					$getSummary->undertime = getUnderTimeHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['start_time'], $scheduled['end_time']);																								

				} else {

					//echo "UNDERTIME: FALSE \n";
					$getTimesheet->undertime_1 = '';																			
					$getSummary->undertime = '';																			

				}		    				

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($clockingDateTime)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($getTimesheet->time_out_1)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    //echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$getTimesheet->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				} else {

					$isOvertime = false;
					$getTimesheet->total_overtime_1 = '';				
				}

				//HOLIDAY: TRUE
				if( hasHoliday($dayDate) ) {

					 //echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

						//echo "Regular holiday \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->rest_day_legal_holiday = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);															
							$getSummary->rest_day_legal_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->rest_day_legal_holiday = '';

						}						


					} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day
						

						//echo "Special non-working day \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->rest_day_special_holiday = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																		
							$getSummary->rest_day_special_holiday_overtime = '';
							
						} else { //ISOVERTIME: TRUE

							$getSummary->rest_day_special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->rest_day_special_holiday = '';

						}	

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					//echo "HOLIDAY: FALSE \n";

					//echo "Regular Day \n";		

					if (!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->rest_day = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																																	
						$getSummary->rest_day_overtime = '';

						$getSummary->rest_day_legal_holiday = '';
						$getSummary->rest_day_special_holiday = '';						
						
					} else { //ISOVERTIME: TRUE

						$getSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->rest_day = '';

						$getSummary->rest_day_legal_holiday_overtime = '';
						$getSummary->rest_day_special_holiday_overtime = '';							

					}					

				}				


			}

		}		


		if ( $getTimesheet->save() ) {	

			$getSummary->save();
	
			return Redirect::to('/redraw/timesheet');			

		}		

	//}

}


public function updateTimesheetTimeOut1( $data = '', $column = NULL ) {

	//return dd($data);
	//die();

	//Settings
	$nightDiff['from'] = strtotime('22:00:00');
	$nightDiff['to'] = strtotime('06:00:00');
	$breakTime = date('H:i:s', strtotime('01:00:00'));	

	$shift = 1;	
	
	$hasNightDiff = false;

	$getTimesheet = Timesheet::where('id', (int) trim($data["row_id"]))->first();					

	$clockingIn = $getTimesheet->time_in_1;
	$clockingOut = $getTimesheet->time_out_1;

	$timeInHour = date('G', strtotime($clockingIn)); //24-hour format of an hour without leading zeros
	$timeOutHour = date('G', strtotime($clockingOut)); //24-hour format of an hour without leading zeros	

	$timesheetId = $getTimesheet->id;
	$employeeId = $getTimesheet->employee_id;
	$clockingStatus = $getTimesheet->clocking_status;
	//$shiftStart = $getTimesheet->schedule_in;
	//$shiftEnd = $getTimesheet->schedule_out;
	$dayDate = $getTimesheet->daydate;
	$tardiness = $getTimesheet->tardiness_1;

	$dayOfTheWeek = date('l', strtotime($dayDate));

	$getSummary = Summary::where('employee_id', $employeeId)->where('daydate', trim($dayDate))->first();

	/**
	* if !empty time in 1 and  empty time in 1
	* or 
	* if clocking status = clock_in_1
	*
	* check tardiness
	* compute tardiness update timesheet/summary tardiness/lates
	*/

	//if ( !empty($clockingIn) && !empty($clockingOut) &&
	//	 $clockingStatus === 'clock_out_1' ) {

		//return dd($data);
		//die();

		//Todo Add a condition here
		if ( !empty($data['value']) && !empty($getTimesheet->time_in_1) ) {

			$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
			$getTimesheet->time_out_1 = $clockingDateTime;
			$getTimesheet->clocking_status = 'clock_out_1';	

		} elseif ( !empty($data['value']) && empty($getTimesheet->time_in_1) ) {

			$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
			$getTimesheet->time_out_1 = $clockingDateTime;
			$getTimesheet->clocking_status = 'clock_out_1';	//close		

		} elseif ( empty($data['value']) && !empty($getTimesheet->time_in_1) ) {

			$clockingDateTime = '';
			$getTimesheet->clocking_status = 'clock_in_1';			

		} elseif ( empty($data['value']) && empty($getTimesheet->time_in_1) ) {

			$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
			$getTimesheet->time_out_1 = '';
			$getTimesheet->clocking_status = 'open';					

		}		

		$schedule = new Schedule;
		$getSchedule = DB::table('employee_schedule')
		->where('employee_id', $employeeId)
		->where('schedule_date', trim($dayDate))->first();

		$workShift = new Workshift;		
		$getWorkShiftByDayOfTheWeek = DB::table('work_shift')
		->where('employee_id', $employeeId)->where('name_of_day', date('l', strtotime($dayDate)))
		->where('shift', $shift)->first();

		$holiday = new Holiday;
		$getHolidayByDate = DB::table('holiday')->where('date', trim($dayDate))->first();

		if( !empty($getSchedule) ) {

			$scheduled['start_time'] = $getSchedule->start_time;
			$scheduled['end_time'] = $getSchedule->end_time;			
			$scheduled['rest_day'] = $getSchedule->rest_day;	

			$startTime = date('H:i:s', strtotime($scheduled['start_time']));

		} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
			
			//$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek->start_time;
			//$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek->end_time;				

			// From 01:00:00 change to 2015-04-30 09:00:00
			$scheduled['start_time'] = date( 'Y-m-d', strtotime($getTimesheet->time_out_1) ).' '.$getWorkShiftByDayOfTheWeek->start_time;

			// From 01:00:00 change to 2015-04-30 01:00:00
			$scheduled['end_time'] =  date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek->end_time;

			$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek->rest_day;						

			$startTime = $scheduled['start_time'];

		}	

		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {


			//REST DAY: FALSE
			if ( $scheduled['rest_day'] !== 1 ) {

				//LATE/TARDINESS: TRUE
				if ( !empty($getTimesheet->tardiness_1) ) {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																																						

				
				//LATE/TARDINESS: FALSE
				} else {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																					

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $getTimesheet->time_in_1);
				$timeOutArr = explode(' ', $clockingDateTime);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i', strtotime($getTimesheet->time_in_1)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i', strtotime($clockingDateTime)));

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			            $getTimesheet->night_differential_1 = 8.00;

			        } else {

			            $getTimesheet->night_differential_1 = $workHours;

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        } else {

						$getTimesheet->night_differential_1 = $workHours;

			        }

			    } /*else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        }
		        
		    	}*/		    

				//UNDERTIME: TRUE
				if ( strtotime($getTimesheet->time_out_1) < strtotime($scheduled['end_time']) ) {

					//echo "UNDERTIME: TRUE \n";
					$getTimesheet->undertime_1 = getUnderTimeHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$getSummary->undertime = getUnderTimeHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			

				} else {

					//echo "UNDERTIME: FALSE \n";
					$getTimesheet->undertime_1 = '';																			
					$getSummary->undertime = '';																			

				}		    				

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($getTimesheet->time_in_1)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    //echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$getTimesheet->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				} else {

					$isOvertime = false;
					$getTimesheet->total_overtime_1 = '';				
				}

				//HOLIDAY: TRUE
				if( hasHoliday($dayDate) ) {

					 //echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

						//echo "Regular holiday \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->legal_holiday = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);															
							$getSummary->legal_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->legal_holiday = '';

						}						


					} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day
						

						//echo "Special non-working day \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->special_holiday = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																		
							$getSummary->special_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->special_holiday = '';

						}	

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					//echo "HOLIDAY: FALSE \n";

					//echo "Regular Day \n";		

					if (!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->regular = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																																	
						$getSummary->regular_overtime = '';

						$getSummary->legal_holiday = '';
						$getSummary->special_holiday = '';						
						
					} else { //ISOVERTIME: TRUE

						$getSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->regular = '';

						$getSummary->legal_holiday_overtime = '';	
						$getSummary->special_holiday_overtime = '';												

					}					

				}									


			//REST DAY: TRUE
			} elseif ( $scheduled['rest_day'] === 1 ) {


				//LATE/TARDINESS: TRUE
				if ( !empty($getTimesheet->tardiness_1) ) {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																																						

				
				//LATE/TARDINESS: FALSE
				} else {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																					

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $getTimesheet->time_in_1);
				$timeOutArr = explode(' ', $clockingDateTime);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i', strtotime($getTimesheet->time_in_1)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i', strtotime($clockingDateTime)));

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			            $getTimesheet->night_differential_1 = 8.00;

			        } else {

			            $getTimesheet->night_differential_1 = $workHours;

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        } else {

						$getTimesheet->night_differential_1 = $workHours;

			        }

			    } /*else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        }
		        
		    	}*/		    

				//UNDERTIME: TRUE
				if ( strtotime($getTimesheet->time_out_1) < strtotime($scheduled['end_time']) ) {

					//echo "UNDERTIME: TRUE \n";
					$getTimesheet->undertime_1 = getUnderTimeHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$getSummary->undertime = getUnderTimeHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																								

				} else {

					//echo "UNDERTIME: FALSE \n";
					$getTimesheet->undertime_1 = '';																			
					$getSummary->undertime = '';																			

				}		    				

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($getTimesheet->time_in_1)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    //echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$getTimesheet->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				} else {

					$isOvertime = false;
					$getTimesheet->total_overtime_1 = '';				
				}

				//HOLIDAY: TRUE
				if( hasHoliday($dayDate) ) {

					 //echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

						//echo "Regular holiday \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->rest_day_legal_holiday = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);															
							$getSummary->rest_day_legal_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->rest_day_legal_holiday = '';

						}						


					} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day
						

						//echo "Special non-working day \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->rest_day_special_holiday = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																		
							$getSummary->rest_day_special_holiday_overtime = '';
							
						} else { //ISOVERTIME: TRUE

							$getSummary->rest_day_special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->rest_day_special_holiday = '';

						}	

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					//echo "HOLIDAY: FALSE \n";

					//echo "Regular Day \n";		

					if (!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->rest_day = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																																	
						$getSummary->rest_day_overtime = '';

						$getSummary->rest_day_legal_holiday = '';
						$getSummary->rest_day_special_holiday = '';						
						
					} else { //ISOVERTIME: TRUE

						$getSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->rest_day = '';

						$getSummary->rest_day_legal_holiday_overtime = '';
						$getSummary->rest_day_special_holiday_overtime = '';							

					}					

				}				


			}

		}


		if ( $getTimesheet->save() ) {	

			$getSummary->save();
	
			return Redirect::to('/redraw/timesheet');			

		}		

	//}

}

public function updateTimesheetTimeIn2( $data = '', $column = NULL ) {

	//return dd($data);

	//Settings
	$nightDiff['from'] = strtotime('22:00:00');
	$nightDiff['to'] = strtotime('06:00:00');
	$breakTime = date('H:i:s', strtotime('01:00:00'));

	$shift = 1;	
	
	$hasNightDiff = false;

	$getTimesheet = Timesheet::where('id', (int) trim($data["row_id"]))->first();					

	$clockingIn = $getTimesheet->time_in_1;
	$clockingOut = $getTimesheet->time_out_1;

	$timeInHour = date('G', strtotime($clockingIn)); //24-hour format of an hour without leading zeros
	$timeOutHour = date('G', strtotime($clockingOut)); //24-hour format of an hour without leading zeros	

	$timesheetId = $getTimesheet->id;
	$employeeId = $getTimesheet->employee_id;
	$clockingStatus = $getTimesheet->clocking_status;
	//$shiftStart = $getTimesheet->schedule_in;
	//$shiftEnd = $getTimesheet->schedule_out;
	$dayDate = $getTimesheet->daydate;
	$tardiness = $getTimesheet->tardiness_1;

	$dayOfTheWeek = date('l', strtotime($dayDate));

	$getSummary = Summary::where('employee_id', $employeeId)->where('daydate', trim($dayDate))->first();

	/**
	* if !empty time in 1 and  empty time in 1
	* or 
	* if clocking status = clock_in_1
	*
	* check tardiness
	* compute tardiness update timesheet/summary tardiness/lates
	*/

	/*if ( !empty($clockingIn) && empty($clockingOut) &&
		 $clockingStatus === 'clock_in_1' ) {

		$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
		$getTimesheet->time_in_1 = $clockingDateTime;
		$getTimesheet->clocking_status = 'clock_in_1';

		$schedule = new Schedule;
		$getSchedule = DB::table('employee_schedule')
		->where('employee_id', $employeeId)
		->where('schedule_date', trim($dayDate))->first();

		$workShift = new Workshift;		
		$getWorkShiftByDayOfTheWeek = DB::table('work_shift')
		->where('employee_id', $employeeId)->where('name_of_day', date('l', strtotime($dayDate)))
		->where('shift', $shift)->first();

		$holiday = new Holiday;
		$getHolidayByDate = $holiday->getHolidayByDate($dayDate);		

		if( !empty($getSchedule) ) {

			$scheduled['start_time'] = $getSchedule->start_time;
			$scheduled['end_time'] = $getSchedule->end_time;			
			$scheduled['rest_day'] = $getSchedule->rest_day;	

			$startTime = date('H:i:s', strtotime($scheduled['start_time']));

		} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
			
			//$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek->start_time;
			//$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek->end_time;				

			// From 01:00:00 change to 2015-04-30 09:00:00
			$scheduled['start_time'] = date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek->start_time;

			// From 01:00:00 change to 2015-04-30 01:00:00
			$scheduled['end_time'] =  date( 'Y-m-d', strtotime($getTimesheet->time_out_1) ).' '.$getWorkShiftByDayOfTheWeek->end_time;

			$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek->rest_day;						

			$startTime = $scheduled['start_time'];

		}	

		if( strtotime(date('H:i', strtotime($clockingDateTime))) === 
			strtotime(date('H:i', strtotime($scheduled['start_time']))) ) { //WITHOUT TARDINESS

			$getTimesheet->tardiness_1 = '';							
			$getSummary->lates = '';	

			//return dd('//WITHOUT TARDINESS');

		} elseif( strtotime(date('H:i', strtotime($clockingDateTime))) > 
			strtotime(date('H:i', strtotime($scheduled['start_time']))) ) { //WITH TARDINESS

			$clockingIn = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
			$tardinessTime = getTardinessTime($clockingIn, $startTime, true);

			if( !empty($tardinessTime) ) {

				$getTimesheet->tardiness_1 = $tardinessTime;							
				$getSummary->lates = $tardinessTime;	

			}

		}

		if ( $getTimesheet->save() ) {

			$getSummary->save();
			
			return Redirect::to('/redraw/timesheet');			

		}			

	}*/ 

	//elseif ( !empty($clockingIn) && !empty($clockingOut) &&
	//	 $clockingStatus === 'clock_out_1' ) {

		//return dd($data);
		//die();
		
		$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
		$getTimesheet->time_in_1 = $clockingDateTime;
		$getTimesheet->clocking_status = 'clock_out_1';		

		$getTimesheet->time_in_2 = '';

		$schedule = new Schedule;
		$getSchedule = DB::table('employee_schedule')
		->where('employee_id', $employeeId)
		->where('schedule_date', trim($dayDate))->first();

		$workShift = new Workshift;		
		$getWorkShiftByDayOfTheWeek = DB::table('work_shift')
		->where('employee_id', $employeeId)->where('name_of_day', date('l', strtotime($dayDate)))
		->where('shift', $shift)->first();

		$holiday = new Holiday;
		$getHolidayByDate = DB::table('holiday')->where('date', trim($dayDate))->first();		

		if( !empty($getSchedule) ) {

			$scheduled['start_time'] = $getSchedule->start_time;
			$scheduled['end_time'] = $getSchedule->end_time;			
			$scheduled['rest_day'] = $getSchedule->rest_day;	

			$startTime = date('H:i:s', strtotime($scheduled['start_time']));

		} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
			
			//$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek->start_time;
			//$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek->end_time;				

			// From 01:00:00 change to 2015-04-30 09:00:00
			$scheduled['start_time'] = date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek->start_time;

			// From 01:00:00 change to 2015-04-30 01:00:00
			$scheduled['end_time'] =  date( 'Y-m-d', strtotime($getTimesheet->time_out_1) ).' '.$getWorkShiftByDayOfTheWeek->end_time;

			$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek->rest_day;						

			$startTime = $scheduled['start_time'];

		}	

		$clockingIn = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));

		if( strtotime(date('H:i', strtotime($clockingDateTime))) === 
			strtotime(date('H:i', strtotime($scheduled['start_time']))) ) { //WITHOUT TARDINESS

			$getTimesheet->tardiness_1 = '';							
			$getSummary->lates = '';	

			//return dd('//WITHOUT TARDINESS');

		} elseif( strtotime(date('H:i', strtotime($clockingDateTime))) > 
			strtotime(date('H:i', strtotime($scheduled['start_time']))) ) { //WITH TARDINESS

			
			$tardinessTime = getTardinessTime($clockingIn, $startTime, true);

			if( !empty($tardinessTime) ) {

				$getTimesheet->tardiness_1 = $tardinessTime;							
				$getSummary->lates = $tardinessTime;	

			}

		}

		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {


			//REST DAY: FALSE
			if ( $scheduled['rest_day'] !== 1 ) {

				//LATE/TARDINESS: TRUE
				if ( !empty($getTimesheet->tardiness_1) ) {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																																						

				
				//LATE/TARDINESS: FALSE
				} else {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																					

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $clockingDateTime);
				$timeOutArr = explode(' ', $getTimesheet->time_out_1);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i', strtotime($clockingDateTime)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i', strtotime($getTimesheet->time_out_1)));

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			            $getTimesheet->night_differential_1 = 8.00;

			        } else {

			            $getTimesheet->night_differential_1 = $workHours;

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        } else {

						$getTimesheet->night_differential_1 = $workHours;

			        }

			    } /*else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        }
		        
		    	}*/		    

				//UNDERTIME: TRUE
				if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					//echo "UNDERTIME: TRUE \n";
					$getTimesheet->undertime_1 = getUnderTimeHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['start_time'], $scheduled['end_time']);																			
					$getSummary->undertime = getUnderTimeHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['start_time'], $scheduled['end_time']);																			

				} else {

					//echo "UNDERTIME: FALSE \n";
					$getTimesheet->undertime_1 = '';																			
					$getSummary->undertime = '';																			

				}		    				

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($clockingDateTime)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($getTimesheet->time_out_1)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    ////echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$getTimesheet->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				} else {

					$isOvertime = false;
					$getTimesheet->total_overtime_1 = '';				
				}

				//HOLIDAY: TRUE
				if( hasHoliday($dayDate) ) {

					 ////echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

						//echo "Regular holiday \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->legal_holiday = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);															
							$getSummary->legal_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->legal_holiday = '';

						}						


					} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day
						

						//echo "Special non-working day \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->special_holiday = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																		
							$getSummary->special_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->special_holiday = '';

						}	

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					//echo "HOLIDAY: FALSE \n";

					//echo "Regular Day \n";		

					if (!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->regular = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																																	
						$getSummary->regular_overtime = '';

						$getSummary->legal_holiday = '';
						$getSummary->special_holiday = '';						
						
					} else { //ISOVERTIME: TRUE

						$getSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->regular = '';

						$getSummary->legal_holiday_overtime = '';	
						$getSummary->special_holiday_overtime = '';												

					}					

				}									

			//REST DAY: TRUE
			} elseif ( $scheduled['rest_day'] === 1 ) {


				//LATE/TARDINESS: TRUE
				if ( !empty($getTimesheet->tardiness_1) ) {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																																						

				
				//LATE/TARDINESS: FALSE
				} else {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																					

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $clockingDateTime);
				$timeOutArr = explode(' ', $getTimesheet->time_out_1);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i', strtotime($clockingDateTime)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i', strtotime($getTimesheet->time_out_1)));

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			            $getTimesheet->night_differential_1 = 8.00;

			        } else {

			            $getTimesheet->night_differential_1 = $workHours;

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        } else {

						$getTimesheet->night_differential_1 = $workHours;

			        }

			    } else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        }
		        
		    	}		    

				//UNDERTIME: TRUE
				if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					//echo "UNDERTIME: TRUE \n";
					$getTimesheet->undertime_1 = getUnderTimeHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['start_time'], $scheduled['end_time']);																			
					$getSummary->undertime = getUnderTimeHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['start_time'], $scheduled['end_time']);																								

				} else {

					//echo "UNDERTIME: FALSE \n";
					$getTimesheet->undertime_1 = '';																			
					$getSummary->undertime = '';																			

				}		    				

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($clockingDateTime)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($getTimesheet->time_out_1)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    ////echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$getTimesheet->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				} else {

					$isOvertime = false;
					$getTimesheet->total_overtime_1 = '';				
				}

				//HOLIDAY: TRUE
				if( hasHoliday($dayDate) ) {

					////echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

						//echo "Regular holiday \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->rest_day_legal_holiday = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);															
							$getSummary->rest_day_legal_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->rest_day_legal_holiday = '';

						}						


					} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day
						

						//echo "Special non-working day \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->rest_day_special_holiday = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																		
							$getSummary->rest_day_special_holiday_overtime = '';
							
						} else { //ISOVERTIME: TRUE

							$getSummary->rest_day_special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->rest_day_special_holiday = '';

						}	

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					//echo "HOLIDAY: FALSE \n";

					//echo "Regular Day \n";		

					if (!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->rest_day = getTotalHours($clockingDateTime, $getTimesheet->time_out_1, $scheduled['end_time']);																																	
						$getSummary->rest_day_overtime = '';

						$getSummary->rest_day_legal_holiday = '';
						$getSummary->rest_day_special_holiday = '';						
						
					} else { //ISOVERTIME: TRUE

						$getSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->rest_day = '';

						$getSummary->rest_day_legal_holiday_overtime = '';
						$getSummary->rest_day_special_holiday_overtime = '';							

					}					

				}				


			}

		}		


		if ( $getTimesheet->save() ) {	

			$getSummary->save();
	
			return Redirect::to('/redraw/timesheet');			

		}		

	//}

}

public function updateTimesheetTimeOut2( $data = '', $column = NULL ) {

	//return dd($data);
	//die();

	//Settings
	$nightDiff['from'] = strtotime('22:00:00');
	$nightDiff['to'] = strtotime('06:00:00');
	$breakTime = date('H:i:s', strtotime('01:00:00'));	

	$shift = 1;	
	
	$hasNightDiff = false;

	$getTimesheet = Timesheet::where('id', (int) trim($data["row_id"]))->first();					

	$clockingIn = $getTimesheet->time_in_1;
	$clockingOut = $getTimesheet->time_out_1;

	$timeInHour = date('G', strtotime($clockingIn)); //24-hour format of an hour without leading zeros
	$timeOutHour = date('G', strtotime($clockingOut)); //24-hour format of an hour without leading zeros	

	$timesheetId = $getTimesheet->id;
	$employeeId = $getTimesheet->employee_id;
	$clockingStatus = $getTimesheet->clocking_status;
	//$shiftStart = $getTimesheet->schedule_in;
	//$shiftEnd = $getTimesheet->schedule_out;
	$dayDate = $getTimesheet->daydate;
	$tardiness = $getTimesheet->tardiness_1;

	$dayOfTheWeek = date('l', strtotime($dayDate));

	$getSummary = Summary::where('employee_id', $employeeId)->where('daydate', trim($dayDate))->first();

		$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
		$getTimesheet->time_out_1 = $clockingDateTime;
		$getTimesheet->clocking_status = 'clock_out_1';	

		$getTimesheet->time_out_2 = '';

		$schedule = new Schedule;
		$getSchedule = DB::table('employee_schedule')
		->where('employee_id', $employeeId)
		->where('schedule_date', trim($dayDate))->first();

		$workShift = new Workshift;		
		$getWorkShiftByDayOfTheWeek = DB::table('work_shift')
		->where('employee_id', $employeeId)->where('name_of_day', date('l', strtotime($dayDate)))
		->where('shift', $shift)->first();

		$holiday = new Holiday;
		$getHolidayByDate = DB::table('holiday')->where('date', trim($dayDate))->first();

		if( !empty($getSchedule) ) {

			$scheduled['start_time'] = $getSchedule->start_time;
			$scheduled['end_time'] = $getSchedule->end_time;			
			$scheduled['rest_day'] = $getSchedule->rest_day;	

			$startTime = date('H:i:s', strtotime($scheduled['start_time']));

		} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
			
			//$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek->start_time;
			//$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek->end_time;				

			// From 01:00:00 change to 2015-04-30 09:00:00
			$scheduled['start_time'] = date( 'Y-m-d', strtotime($getTimesheet->time_out_1) ).' '.$getWorkShiftByDayOfTheWeek->start_time;

			// From 01:00:00 change to 2015-04-30 01:00:00
			$scheduled['end_time'] =  date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek->end_time;

			$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek->rest_day;						

			$startTime = $scheduled['start_time'];

		}	

		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {


			//REST DAY: FALSE
			if ( $scheduled['rest_day'] !== 1 ) {

				//LATE/TARDINESS: TRUE
				if ( !empty($getTimesheet->tardiness_1) ) {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																																						

				
				//LATE/TARDINESS: FALSE
				} else {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																					

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $getTimesheet->time_in_1);
				$timeOutArr = explode(' ', $clockingDateTime);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i', strtotime($getTimesheet->time_in_1)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i', strtotime($clockingDateTime)));

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			            $getTimesheet->night_differential_1 = 8.00;

			        } else {

			            $getTimesheet->night_differential_1 = $workHours;

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        } else {

						$getTimesheet->night_differential_1 = $workHours;

			        }

			    } /*else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        }
		        
		    	}*/		    

				//UNDERTIME: TRUE
				if ( strtotime($getTimesheet->time_out_1) < strtotime($scheduled['end_time']) ) {

					//echo "UNDERTIME: TRUE \n";
					$getTimesheet->undertime_1 = getUnderTimeHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$getSummary->undertime = getUnderTimeHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			

				} else {

					//echo "UNDERTIME: FALSE \n";
					$getTimesheet->undertime_1 = '';																			
					$getSummary->undertime = '';																			

				}		    				

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($getTimesheet->time_in_1)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    ////echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$getTimesheet->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				} else {

					$isOvertime = false;
					$getTimesheet->total_overtime_1 = '';				
				}

				//HOLIDAY: TRUE
				if( hasHoliday($dayDate) ) {

					 ////echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

						//echo "Regular holiday \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->legal_holiday = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);															
							$getSummary->legal_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->legal_holiday = '';

						}						


					} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day
						

						//echo "Special non-working day \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->special_holiday = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																		
							$getSummary->special_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->special_holiday = '';

						}	

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					//echo "HOLIDAY: FALSE \n";

					//echo "Regular Day \n";		

					if (!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->regular = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																																	
						$getSummary->regular_overtime = '';

						$getSummary->legal_holiday = '';
						$getSummary->special_holiday = '';						
						
					} else { //ISOVERTIME: TRUE

						$getSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->regular = '';

						$getSummary->legal_holiday_overtime = '';	
						$getSummary->special_holiday_overtime = '';												

					}					

				}									


			//REST DAY: TRUE
			} elseif ( $scheduled['rest_day'] === 1 ) {


				//LATE/TARDINESS: TRUE
				if ( !empty($getTimesheet->tardiness_1) ) {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																																						

				
				//LATE/TARDINESS: FALSE
				} else {

					//echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			
					$workHours = getWorkHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$getTimesheet->work_hours_1 = 8.00;					

					} else {

						$getTimesheet->work_hours_1 = $workHours;					

					}				

					//TODO: Compute total hours with out overtime - getTotalHours
					$getTimesheet->total_hours_1 = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																					

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $getTimesheet->time_in_1);
				$timeOutArr = explode(' ', $clockingDateTime);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i', strtotime($getTimesheet->time_in_1)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i', strtotime($clockingDateTime)));

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			            $getTimesheet->night_differential_1 = 8.00;

			        } else {

			            $getTimesheet->night_differential_1 = $workHours;

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        } else {

						$getTimesheet->night_differential_1 = $workHours;

			        }

			    } /*else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
						$getTimesheet->night_differential_1 = 8.00;

			        }
		        
		    	}*/		    

				//UNDERTIME: TRUE
				if ( strtotime($getTimesheet->time_out_1) < strtotime($scheduled['end_time']) ) {

					//echo "UNDERTIME: TRUE \n";
					$getTimesheet->undertime_1 = getUnderTimeHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$getSummary->undertime = getUnderTimeHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																								

				} else {

					//echo "UNDERTIME: FALSE \n";
					$getTimesheet->undertime_1 = '';																			
					$getSummary->undertime = '';																			

				}		    				

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($getTimesheet->time_in_1)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    ////echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$getTimesheet->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				} else {

					$isOvertime = false;
					$getTimesheet->total_overtime_1 = '';				
				}

				//HOLIDAY: TRUE
				if( hasHoliday($dayDate) ) {

					 ////echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

						//echo "Regular holiday \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->rest_day_legal_holiday = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);															
							$getSummary->rest_day_legal_holiday_overtime = '';							
							
						} else { //ISOVERTIME: TRUE

							$getSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->rest_day_legal_holiday = '';

						}						


					} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day
						

						//echo "Special non-working day \n";

						if(!$isOvertime) { //ISOVERTIME: FALSE

							$getSummary->rest_day_special_holiday = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																		
							$getSummary->rest_day_special_holiday_overtime = '';
							
						} else { //ISOVERTIME: TRUE

							$getSummary->rest_day_special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							$getSummary->rest_day_special_holiday = '';

						}	

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					//echo "HOLIDAY: FALSE \n";

					//echo "Regular Day \n";		

					if (!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->rest_day = getTotalHours($getTimesheet->time_in_1, $clockingDateTime, $scheduled['end_time']);																																	
						$getSummary->rest_day_overtime = '';

						$getSummary->rest_day_legal_holiday = '';
						$getSummary->rest_day_special_holiday = '';						
						
					} else { //ISOVERTIME: TRUE

						$getSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->rest_day = '';

						$getSummary->rest_day_legal_holiday_overtime = '';
						$getSummary->rest_day_special_holiday_overtime = '';							

					}					

				}				


			}

		}

		if ( $getTimesheet->save() ) {	

			$getSummary->save();
	
			return Redirect::to('/redraw/timesheet');			

		}		

	//}

}

public function updateTimesheetTimeIn3( $data = '', $column = NULL ) {

	//Settings
	$nightDiff['from'] = strtotime('22:00:00');
	$nightDiff['to'] = strtotime('06:00:00');
	$breakTime = date('H:i:s', strtotime('01:00:00'));

	$shift = 2;	
	
	$hasNightDiff = false;

	$getTimesheet = Timesheet::where('id', (int) trim($data["row_id"]))->first();					

	$clockingIn = $getTimesheet->time_in_3;
	$clockingOut = $getTimesheet->time_out_3;

	$timeInHour = date('G', strtotime($clockingIn)); //24-hour format of an hour without leading zeros
	$timeOutHour = date('G', strtotime($clockingOut)); //24-hour format of an hour without leading zeros	

	$timesheetId = $getTimesheet->id;
	$employeeId = $getTimesheet->employee_id;
	$clockingStatus = $getTimesheet->clocking_status;
	//$shiftStart = $getTimesheet->schedule_in;
	//$shiftEnd = $getTimesheet->schedule_out;
	$dayDate = $getTimesheet->daydate;
	$tardiness = $getTimesheet->tardiness_3;

	$dayOfTheWeek = date('l', strtotime($dayDate));

	$getSummary = Summary::where('employee_id', $employeeId)->where('daydate', trim($dayDate))->first();

	//Todo Add a condition here
	if ( !empty($data['value']) && !empty($getTimesheet->time_out_3) ) {
		
		$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
		$getTimesheet->time_in_3 = $clockingDateTime;
		$getTimesheet->clocking_status = 'clock_out_3';

	} elseif ( !empty($data['value']) && empty($getTimesheet->time_out_3) ) {

		$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));
		$getTimesheet->time_in_3 = $clockingDateTime;
		$getTimesheet->clocking_status = 'clock_in_3';

	} elseif ( empty($data['value']) && !empty($getTimesheet->time_out_3) ) {

		$clockingDateTime = '';
		$getTimesheet->time_in_3 = $clockingDateTime;
		$getTimesheet->clocking_status = 'clock_in_3'; //close

	} elseif ( empty($data['value']) && empty($getTimesheet->time_out_3) ) {

		$clockingDateTime = '';
		$getTimesheet->time_in_3 = '';
		$getTimesheet->clocking_status = 'close';

	}


	$schedule = new Schedule;
	$getSchedule = DB::table('employee_schedule')
	->where('employee_id', $employeeId)
	->where('schedule_date', trim($dayDate))->first();

	$workShift = new Workshift;		
	$getWorkShiftByDayOfTheWeek = DB::table('work_shift')
	->where('employee_id', $employeeId)->where('name_of_day', date('l', strtotime($dayDate)))
	->where('shift', $shift)->first();

	$holiday = new Holiday;
	$getHolidayByDate = DB::table('holiday')->where('date', trim($dayDate))->first();		

	if( !empty($getSchedule) ) {

		$scheduled['start_time'] = $getSchedule->start_time;
		$scheduled['end_time'] = $getSchedule->end_time;			
		$scheduled['rest_day'] = $getSchedule->rest_day;	

		$startTime = date('H:i:s', strtotime($scheduled['start_time']));

	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek->start_time;
		//$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek->end_time;				

		// From 01:00:00 change to 2015-04-30 09:00:00
		$scheduled['start_time'] = date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek->start_time;

		// From 01:00:00 change to 2015-04-30 01:00:00
		$scheduled['end_time'] =  date( 'Y-m-d', strtotime($getTimesheet->time_out_3) ).' '.$getWorkShiftByDayOfTheWeek->end_time;

		$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek->rest_day;						

		$startTime = $scheduled['start_time'];

	}	

	$clockingIn = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));

	if ( $getTimesheet->save() ) {	

		$getSummary->save();

		return Redirect::to('/redraw/timesheet');			

	}		


}

public function updateTimesheetTimeOut3( $data = '', $column = NULL ) {

	//Settings
	$nightDiff['from'] = strtotime('22:00:00');
	$nightDiff['to'] = strtotime('06:00:00');
	$breakTime = date('H:i:s', strtotime('01:00:00'));	

	$shift = 2;	
	
	$hasNightDiff = false;

	$getTimesheet = Timesheet::where('id', (int) trim($data["row_id"]))->first();					

	$clockingIn = $getTimesheet->time_in_3;
	$clockingOut = $getTimesheet->time_out_3;

	$timeInHour = date('G', strtotime($clockingIn)); //24-hour format of an hour without leading zeros
	$timeOutHour = date('G', strtotime($clockingOut)); //24-hour format of an hour without leading zeros	

	$timesheetId = $getTimesheet->id;
	$employeeId = $getTimesheet->employee_id;
	$clockingStatus = $getTimesheet->clocking_status;
	//$shiftStart = $getTimesheet->schedule_in;
	//$shiftEnd = $getTimesheet->schedule_out;
	$dayDate = $getTimesheet->daydate;
	$tardiness = $getTimesheet->tardiness_3;

	$dayOfTheWeek = date('l', strtotime($dayDate));

	$getSummary = Summary::where('employee_id', $employeeId)->where('daydate', trim($dayDate))->first();

	
	$hourNeedle = date('G',strtotime(trim($data['value'])));

	$hourHaystack = array(0, 1, 2, 3, 4, 5, 6);

	if ( in_array($hourNeedle, $hourHaystack) ) {

		$nightdiff = true;

	} else {

		$nightdiff = false;
	}

	$dayDateModify = new DateTime($dayDate);

	//Todo Add a condition here
	if ( !empty($data['value']) && !empty($getTimesheet->time_in_3) ) {

		if(!$nightdiff) {

			$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));

		} else {

			//$dayDateModify = new DateTime($dayDate);
			$dayDateModify->modify('+1 day');
			//$dayDateModify->format('Y-m-d');			
			$clockingDateTime = date('Y-m-d', strtotime($dayDateModify->format('Y-m-d'))).' '.date('H:i:s', strtotime($data['value']));

		}

		$getTimesheet->time_out_3 = $clockingDateTime;
		$getTimesheet->clocking_status = 'clock_out_3';	

	} elseif ( !empty($data['value']) && empty($getTimesheet->time_in_3) ) {

		if(!$nightdiff) {

			$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));

		} else {

			//$dayDateModify = new DateTime($dayDate);
			$dayDateModify->modify('+1 day');
			//$dayDateModify->format('Y-m-d');			
			$clockingDateTime = date('Y-m-d', strtotime($dayDateModify->format('Y-m-d'))).' '.date('H:i:s', strtotime($data['value']));

		}

		$getTimesheet->time_out_3 = $clockingDateTime;
		$getTimesheet->clocking_status = 'clock_out_3';	//close		

	} elseif ( empty($data['value']) && !empty($getTimesheet->time_in_3) ) {

		$clockingDateTime = '';
		$getTimesheet->time_out_3 = $clockingDateTime;
		$getTimesheet->clocking_status = 'clock_in_3';			

	} elseif ( empty($data['value']) && empty($getTimesheet->time_in_3) ) {

		$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));

		if(!$nightdiff) {

			$clockingDateTime = date('Y-m-d', strtotime($dayDate)).' '.date('H:i:s', strtotime($data['value']));			

		} else {

			//$dayDateModify = new DateTime($dayDate);
			$dayDateModify->modify('+1 day');
			//$dayDateModify->format('Y-m-d');			
			$clockingDateTime = date('Y-m-d', strtotime($dayDateModify->format('Y-m-d'))).' '.date('H:i:s', strtotime($data['value']));

		}

		$getTimesheet->time_out_3 = '';
		$getTimesheet->clocking_status = 'close';					

	}		


	$schedule = new Schedule;
	$getSchedule = DB::table('employee_schedule')
	->where('employee_id', $employeeId)
	->where('schedule_date', trim($dayDate))->first();

	$workShift = new Workshift;		
	$getWorkShiftByDayOfTheWeek = DB::table('work_shift')
	->where('employee_id', $employeeId)->where('name_of_day', date('l', strtotime($dayDate)))
	->where('shift', $shift)->first();

	$holiday = new Holiday;
	$getHolidayByDate = DB::table('holiday')->where('date', trim($dayDate))->first();

	if( !empty($getSchedule) ) {

		$scheduled['start_time'] = $getSchedule->start_time;
		$scheduled['end_time'] = $getSchedule->end_time;			
		$scheduled['rest_day'] = $getSchedule->rest_day;	

		$startTime = date('H:i:s', strtotime($scheduled['start_time']));

	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek->start_time;
		//$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek->end_time;				

		// From 01:00:00 change to 2015-04-30 09:00:00
		$scheduled['start_time'] = date( 'Y-m-d', strtotime($getTimesheet->time_out_3) ).' '.$getWorkShiftByDayOfTheWeek->start_time;

		// From 01:00:00 change to 2015-04-30 01:00:00
		$scheduled['end_time'] =  date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek->end_time;

		$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek->rest_day;						

		$startTime = $scheduled['start_time'];

	}	

	//return dd($clockingDateTime);

	//SCHEDULED : TRUE
	if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
	     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

		//REST DAY: FALSE
		if ( $scheduled['rest_day'] !== 1 ) {

			$datetime1 = $getTimesheet->time_in_3;
			$datetime2 = $clockingDateTime;
			
			//$interval = getDateTimeDiffInterval($getTimesheet->time_in_3, $getTimesheet->time_out_3) { //Used

			$datetime1 = new DateTime($datetime1);
			$datetime2 = new DateTime($datetime2);
			$interval = $datetime1->diff($datetime2);					

			$hh = $interval->format('%H');
			$mm = $interval->format('%I');
			$ss = $interval->format('%S');	

			$overtime = getTimeToDecimalHours($hh, $mm, $ss); //number_format($hours, 2);*/
			
			//OVERTIME: TRUE
		    $isOvertime = true;					
			$getTimesheet->total_overtime_3 = $overtime;		
			$getTimesheet->total_hours_3 = $overtime;

			/*
			//echo "LATE/TARDINESS: TRUE \n";
			//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			

			$workHours = $getTimesheet->total_overtime_3;											

			if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

				$getTimesheet->work_hours_3 = 8.00;					

			} else {

				$getTimesheet->work_hours_3 = $workHours;

			}
			*/				

			//TODO: Compute total hours with out overtime - getTotalHours
			$getTimesheet->total_hours_3 = $getTimesheet->total_overtime_3;

			//GET NIGHTDIFF
			$timeInArr = array();
			$timeOutArr = array();

			$timeInArr = explode(' ', $getTimesheet->time_in_3);
			$timeOutArr = explode(' ', $clockingDateTime);

			$nightDiff['from'] = strtotime(date('Y-m-d H:i', strtotime($timeInArr[0].' '.'22:00:00')));
			$nightDiff['to'] = strtotime(date('Y-m-d H:i', strtotime($timeOutArr[0].' '.'06:00:00')));  				

		    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i', strtotime($getTimesheet->time_in_3)));			  
		    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i', strtotime($clockingDateTime)));

		    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
		    {
		        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

		            //$getTimesheet->night_differential_3 = 8.00;
		            $getTimesheet->night_differential_3 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

		        } else {

		            //$getTimesheet->night_differential_3 = $overtime;
		           $getTimesheet->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;

		        }

		    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
		        
		        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
		            
					//$getTimesheet->night_differential_3 = 8.00;			        	
					$getTimesheet->night_differential_3 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;

		        } else {

					//$getTimesheet->night_differential_3 = $overtime;
					$getTimesheet->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;

		        }

		    } /*else {

		        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
		            
					//$getTimesheet->night_differential_3 = 8.00;
					$getTimesheet->night_differential_3 = ($nightDiff['from'] - $nightDiff['to']) / 3600;

		        }
	        
	    	}*/

			//HOLIDAY: TRUE
			if( hasHoliday($dayDate) ) {

				 ////echo "HOLIDAY: TRUE \n";

				if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

					//echo "Regular holiday \n";

					if(!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->legal_holiday = getTotalHours($getTimesheet->time_in_3, $clockingDateTime, $scheduled['end_time']);															
						$getSummary->legal_holiday_overtime = '';							
						
					} else { //ISOVERTIME: TRUE

						$getSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->legal_holiday = '';

					}						


				} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day
					

					//echo "Special non-working day \n";

					if(!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->special_holiday = getTotalHours($getTimesheet->time_in_3, $clockingDateTime, $scheduled['end_time']);																		
						$getSummary->special_holiday_overtime = '';							
						
					} else { //ISOVERTIME: TRUE

						$getSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->special_holiday = '';

					}	

				}												

			//HOLIDAY: FALSE	
			} else { //Regular Day

				//echo "HOLIDAY: FALSE \n";

				//echo "Regular Day \n";		

				if (!$isOvertime) { //ISOVERTIME: FALSE

					$getSummary->regular = getTotalHours($getTimesheet->time_in_3, $clockingDateTime, $scheduled['end_time']);																																	
					$getSummary->regular_overtime = '';

					$getSummary->legal_holiday = '';
					$getSummary->special_holiday = '';						
					
				} else { //ISOVERTIME: TRUE

					$getSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
					$getSummary->regular = '';

					$getSummary->legal_holiday_overtime = '';	
					$getSummary->special_holiday_overtime = '';												

				}					

			}									

		//REST DAY: TRUE
		} elseif ( $scheduled['rest_day'] === 1 ) {


			$datetime1 = $getTimesheet->time_in_3;
			$datetime2 = $clockingDateTime;
			
			//$interval = getDateTimeDiffInterval($getTimesheet->time_in_3, $getTimesheet->time_out_3) { //Used

			$datetime1 = new DateTime($datetime1);
			$datetime2 = new DateTime($datetime2);
			$interval = $datetime1->diff($datetime2);					

			$hh = $interval->format('%H');
			$mm = $interval->format('%I');
			$ss = $interval->format('%S');	

			$overtime = getTimeToDecimalHours($hh, $mm, $ss); //number_format($hours, 2);*/
			
			//OVERTIME: TRUE
		    $isOvertime = true;					
			$getTimesheet->total_overtime_3 = $overtime;		
			$getTimesheet->total_hours_3 = $overtime;

			/*
			//echo "LATE/TARDINESS: TRUE \n";
			//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours			

			$workHours = $getTimesheet->total_overtime_3;											

			if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

				$getTimesheet->work_hours_3 = 8.00;					

			} else {

				$getTimesheet->work_hours_3 = $workHours;					

			}
			*/				

			//TODO: Compute total hours with out overtime - getTotalHours
			$getTimesheet->total_hours_3 = $getTimesheet->total_overtime_3;

			//GET NIGHTDIFF
			$timeInArr = array();
			$timeOutArr = array();

			$timeInArr = explode(' ', $getTimesheet->time_in_3);
			$timeOutArr = explode(' ', $clockingDateTime);

			$nightDiff['from'] = strtotime(date('Y-m-d H:i', strtotime($timeInArr[0].' '.'22:00:00')));
			$nightDiff['to'] = strtotime(date('Y-m-d H:i', strtotime($timeOutArr[0].' '.'06:00:00')));  				

		    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i', strtotime($getTimesheet->time_in_3)));			  
		    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i', strtotime($clockingDateTime)));

		    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
		    {
		        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

		            $getTimesheet->night_differential_3 = 8.00;

		        } else {

		            $getTimesheet->night_differential_3 = $overtime;

		        }

		    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
		        
		        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
		            
					$getTimesheet->night_differential_3 = 8.00;

		        } else {

					$getTimesheet->night_differential_3 = $overtime;

		        }

		    } /*else {

		        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
		            
					$getTimesheet->night_differential_3 = 8.00;

		        }
	        
	    	}*/

			//HOLIDAY: TRUE
			if( hasHoliday($dayDate) ) {

				 ////echo "HOLIDAY: TRUE \n";

				if ( 'Regular holiday' === $getHolidayByDate->holiday_type ) { //Regular holiday

					//echo "Regular holiday \n";

					if(!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->rest_day_legal_holiday = getTotalHours($getTimesheet->time_in_3, $clockingDateTime, $scheduled['end_time']);															
						$getSummary->rest_day_legal_holiday_overtime = '';							
						
					} else { //ISOVERTIME: TRUE

						$getSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->rest_day_legal_holiday = '';

					}						


				} elseif ( 'Special non-working day' === $getHolidayByDate->holiday_type ) { //Special non-working day
					

					//echo "Special non-working day \n";

					if(!$isOvertime) { //ISOVERTIME: FALSE

						$getSummary->rest_day_special_holiday = getTotalHours($getTimesheet->time_in_3, $clockingDateTime, $scheduled['end_time']);																		
						$getSummary->rest_day_special_holiday_overtime = '';
						
					} else { //ISOVERTIME: TRUE

						$getSummary->rest_day_special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						$getSummary->rest_day_special_holiday = '';

					}	

				}												

			//HOLIDAY: FALSE	
			} else { //Regular Day

				//echo "HOLIDAY: FALSE \n";

				//echo "Regular Day \n";		

				if (!$isOvertime) { //ISOVERTIME: FALSE

					$getSummary->rest_day = getTotalHours($getTimesheet->time_in_3, $clockingDateTime, $scheduled['end_time']);																																	
					$getSummary->rest_day_overtime = '';

					$getSummary->rest_day_legal_holiday = '';
					$getSummary->rest_day_special_holiday = '';						
					
				} else { //ISOVERTIME: TRUE

					$getSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
					$getSummary->rest_day = '';

					$getSummary->rest_day_legal_holiday_overtime = '';
					$getSummary->rest_day_special_holiday_overtime = '';							

				}					

			}				


		}

	}

	if ( $getTimesheet->save() ) {	

		$getSummary->save();

		return Redirect::to('/redraw/timesheet');			

	}		

	//}

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