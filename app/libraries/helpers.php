<?php
	//http://bwc.dole.gov.ph/FAQ/viewdetails.aspx?id=1
	/*function nightDifferential($percentage = '', $hourOfwork) {}*/

	/**
	*
	* TIMESHEET: Clocking In Functions	
	*
	*/

function getClockingInDateTime($clockingStatus = '', $clockingDateTime = '') {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');

	$dayOfTheWeek = date('l');
	
	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));
	//return dd($getSchedule);
	
	$workShift = new Workshift;
	//$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek);
	//return dd($getWorkShiftByDayOfTheWeek);
	//break;
	
	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);
	//return dd($employeeSetting);

	if ( !empty($getSchedule) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;			
		
	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//$getWorkShiftByEmployeeId->name_of_day;

		$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek[0]->start_time;
		$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek[0]->end_time;		

	} else {

		return 'getClockingInDateTime Check default schedule and Scheduled table for this employee';

	}

	//if ( !empty($employeeSetting) ) {}

	// Regular clocking in
	if ( $clockingStatus === 'open' ) { // Today clocking status is "open"

		//Check first if has schedule assign
		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

			echo "SCHEDULED : TRUE \n";

			//Schedule code here

			//echo $scheduled['start_time'];				

			if( strtotime(date('H:i', strtotime($clockingDateTime))) === 
				strtotime(date('H:i', strtotime($scheduled['start_time']))) ) {						

				return clockingStatusOpenIn($clockingDateTime);

			} else {

				//with tardiness
				return clockingStatusOpenIn($clockingDateTime, true);

			}				


		}
		
	//SCHEDULED : FALSE
	//HAS NO SCHEDULE - EMERGENCY OR SECOND SHIFT		
	} else {

		echo "SCHEDULED : FALSE \n";	

	}
	
		
	// Regular clocking but has second shift
	if ($clockingStatus === 'clock_out_1') {

		echo 'clock_out_1';

		//return "Clocking Status: > ". $clockingStatus;

		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

			echo "SCHEDULED : TRUE \n";

			//Schedule code here

			//echo $scheduled['start_time'];				

			if( strtotime(date('H:i', strtotime($clockingDateTime))) >= 
				strtotime(date('H:i', strtotime($scheduled['end_time']))) ) {						
				
				echo "SCHEDULED : TRUE 1 \n";

				return clockingStatusClockOut1In($clockingDateTime);

			} else {

				echo "SCHEDULED : TRUE 2 \n";

				//with tardiness
				return clockingStatusClockOut1In($clockingDateTime, true);

			}

		} else {

			echo "SCHEDULED : FALSE \n";	

		}				

	} 

	
	if ($clockingStatus === 'clock_out_2') {

		echo 'clock_out_2';

		//return "Clocking Status: > ". $clockingStatus;

		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

			//Schedule code here

			//echo $scheduled['start_time'];				

			if( strtotime(date('H:i', strtotime($clockingDateTime))) >= 
				strtotime(date('H:i', strtotime($scheduled['end_time']))) ) {						

				echo 'debug.io';
				return clockingStatusClockOut2In($clockingDateTime);

			} else {
				echo 'debug.io';
				//with tardiness
				return clockingStatusClockOut2In($clockingDateTime, true);

			}
								
		}				

	}

	if ($clockingStatus === 'forgot_to_clock_out') { // Forget to Clock out yesterday

		echo 'forgot_to_clock_out';

		//return "Clocking Status: > ". $clockingStatus;

		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

			//Compare the schedule to the clocking in time
			if( strtotime(date('H:i', strtotime($clockingDateTime))) === 
				strtotime(date('H:i', strtotime($workShift['start_time']))) ) {						

				return clockingStatusForgotToClockOut($clockingDateTime);

			} else {
				
				//with tardiness
				return clockingStatusForgotToClockOut($clockingDateTime, true);

			}
								
		}				

	}

}

function getClockingInDateTimeYesterdayClockOut($clockingStatus = '', $clockingDateTime = '') {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');
	$dayOfTheWeek = date('l');
	
	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	$workShift = new Workshift;
	//$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek);
	
	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$yesterDayDate = date("Y-m-d", strtotime('yesterday'));
	$getYesterDayDate = $timesheet->getTimesheetYesterday($employeeId, $yesterDayDate);
	$employeeNightDiffClocking = $timesheet->getEmployeeNightDiffClocking($employeeId, $yesterDayDate);
	
	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));		

	$employeeSummaryNightDiffClocking = $summary->getEmployeeSummaryNightDiffClocking($employeeId, $yesterDayDate);

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);		

	//Todo: schedule Date Time

	if ( !empty($getSchedule) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;			

		//$scheduled['start_time'] = date('H:i:s', strtotime($getSchedule[0]->start_time));
		//$scheduled['end_time'] = date('H:i:s', strtotime($getSchedule[0]->end_time));			
		
	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//$getWorkShiftByEmployeeId->name_of_day;

		$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek[0]->start_time;
		$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek[0]->end_time;		

	} else {

		return 'getClockingInDateTimeYesterdayClockOut Check default schedule and Scheduled table for this employee';

	}	

	if ( $clockingStatus === 'open' ) {	

 		echo "OPEN \n";

		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
	     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

			echo "SCHEDULED : TRUE \n";

			if( strtotime(date('H:i', strtotime($clockingDateTime))) === 
				strtotime(date('H:i', strtotime($scheduled['start_time']))) ) {										

				return clockingStatusYesterdayClockOut($clockingDateTime);

			} else {	

				return clockingStatusYesterdayClockOut($clockingDateTime, true);					

			}

		//SCHEDULED : FALSE
		//HAS NO SCHEDULE - EMERGENCY OR SECOND SHIFT			
		} else {

			echo "SCHEDULED : FALSE \n";	

		}

	}

	if ( $clockingStatus === 'clock_out_3' ) { // Check the clocking status if can be change to "close"

		echo 'debug.io > clock_out_3';

		if( hasSchedule() ) {		

			$employeeClocking->time_in_1		= $clockingDateTime;					
			$employeeClocking->clocking_status = 'clock_in_1';

			$employeeNightDiffClocking->clocking_status = 'clock_out_1';
			
			$employeeNightDiffClocking->save();						

			if ( $employeeClocking->save() ) {
				
				return Redirect::to('/redraw/timesheet');

			}				

		} elseif( hasWorkShiftSchedule() ) {


			$employeeClocking->time_in_1		= $clockingDateTime;					
			$employeeClocking->clocking_status = 'clock_in_1';

			$employeeNightDiffClocking->clocking_status = 'clock_out_1';
			
			$employeeNightDiffClocking->save();						

			if ( $employeeClocking->save() ) {
				
				return Redirect::to('/redraw/timesheet');

			}		

		}				

	}				

}

function clockingStatusYesterdayClockOut($clockingDateTime = '', $tardiness = false) {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');

	$currentDate = date('Y-m-d');		
	$yesterDayDate = date("Y-m-d", strtotime('yesterday'));
	$dayOfTheWeek = date('l');

	$timeInDateTime = date('G', strtotime($clockingDateTime));

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));
	$getScheduleYesterday = $schedule->getSchedule($employeeId, $yesterDayDate);

	$workShift = new Workshift;
	//$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 1);

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);

	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$getYesterDayDate = $timesheet->getTimesheetYesterday($employeeId, $yesterDayDate);
	$employeeNightDiffClocking = $timesheet->getEmployeeNightDiffClocking($employeeId, $yesterDayDate);

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));		
	$employeeSummaryNightDiffClocking = $summary->getEmployeeSummaryNightDiffClocking($employeeId, $yesterDayDate);

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

	if( !empty($getSchedule) && !empty($getScheduleYesterday) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;	

		$scheduledYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
		$scheduledYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
		$scheduledYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;			

	} elseif( !empty($getSchedule) && empty($getScheduleYesterday) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;	

	} elseif( empty($getSchedule) && !empty($getScheduleYesterday) ) {

		$scheduledYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
		$scheduledYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
		$scheduledYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;						

	} elseif( empty($getSchedule) && empty($getScheduleYesterday) ) {

		if( !empty($getWorkShiftByDayOfTheWeek) ) {

			$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeNightDiffClocking->time_in_1) ).' '.$getWorkShiftByDayOfTheWeek[0]->start_time;						
			$scheduled['end_time'] =  date( 'Y-m-d', strtotime('-1 day') ).' '.$getWorkShiftByDayOfTheWeek[0]->end_time;
			$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek[0]->rest_day;

		}

	} else {

		return 'clockingStatusYesterdayClockOut Check default schedule and Scheduled table for this employee';

	}		

	
	if( $timeInDateTime < 6 ) { //Test: set to 6 to see forgot to clock out example and 12 to see yesterday clock out												

		echo 'clock_in_3';	
	
		$employeeClocking->time_in_3       = $clockingDateTime;					
		$employeeClocking->clocking_status = 'clock_in_3'; //'clock_out_1';

		//Check this out
		/*if( $employeeNightDiffClocking->clocking_status === 'yesterday_clock_out' ) {

			$employeeNightDiffClocking->clocking_status = 'close';
			$employeeNightDiffClocking->save();

		}*/			

		if($tardiness) {

			$clockingIn = $clockingDateTime; //$getClockingDateTime;
			$startTime = $getWorkShiftByDayOfTheWeek[0]->start_time; //$getWorkShift[0]->start_time;	

			$tardinessTime = getTardinessTime($clockingIn, $startTime);

			if( !empty($tardinessTime) ) {

				$employeeClocking->tardiness_1 = $tardinessTime;							
				$employeeSummary->lates = $tardinessTime;				
			}
		}		

	} elseif( $timeInDateTime > 6 ) {

		echo 'clock_in_1';

		$employeeClocking->time_in_1 = $clockingDateTime;
		$employeeClocking->clocking_status = 'clock_in_1';

		if( $employeeNightDiffClocking->clocking_status === 'yesterday_clock_out' ) {

			$employeeNightDiffClocking->clocking_status = 'close';
			$employeeNightDiffClocking->save();

		}

		if($tardiness) {

			$clockingIn = $clockingDateTime; //$getClockingDateTime;
			$startTime = $getWorkShiftByDayOfTheWeek[0]->start_time; //$getWorkShift[0]->start_time;	

			$tardinessTime = getTardinessTime($clockingIn, $startTime);

			if( !empty($tardinessTime) ) {

				$employeeClocking->tardiness_1 = $tardinessTime;							
				$employeeSummary->lates = $tardinessTime;				
			}
		}

	}

	if ( $employeeClocking->save() ) {

		if( !empty($tardinessTime) ) {

			$employeeSummary->save();

		}						
	
		return Redirect::to('/redraw/timesheet');

	}

}

//Initialize: Forget to Clock out yesterday or

function getClockingOutDateTimeForgotToClockOutClockIn1($clockingStatus = '', $clockingDateTime = '', $yesterdayClockingStatus = '') {

	//return 'getClockingOutDateTimeForgotToClockOutClockIn1';
	//return 'Initialize: Forget to Clock out yesterday or';

	//Settings
	$breakTime = date('H:i', strtotime('01:00:00'));

	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');

	$currentDate = date('Y-m-d');		
	$yesterDayDate = date("Y-m-d", strtotime('yesterday'));
	$dayOfTheWeek = date('l');

	$timeInDateTime = date('G', strtotime($clockingDateTime));

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));
	$getScheduleYesterday = $schedule->getSchedule($employeeId, $yesterDayDate);

	$workShift = new Workshift;
	//$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 1);

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);

	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$getYesterDayDate = $timesheet->getTimesheetYesterday($employeeId, $yesterDayDate);
	$employeeNightDiffClocking = $timesheet->getEmployeeNightDiffClocking($employeeId, $yesterDayDate);

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));		
	$employeeSummaryNightDiffClocking = $summary->getEmployeeSummaryNightDiffClocking($employeeId, $yesterDayDate);

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

	if( !empty($getSchedule) && !empty($getScheduleYesterday) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;	

		$scheduledYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
		$scheduledYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
		$scheduledYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;			

	} elseif( !empty($getSchedule) && empty($getScheduleYesterday) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;	

	} elseif( empty($getSchedule) && !empty($getScheduleYesterday) ) {

		$scheduledYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
		$scheduledYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
		$scheduledYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;						

	} elseif( empty($getSchedule) && empty($getScheduleYesterday) ) {

		if( !empty($getWorkShiftByDayOfTheWeek) ) {

			$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeNightDiffClocking->time_in_1) ).' '.$getWorkShiftByDayOfTheWeek[0]->start_time;						
			$scheduled['end_time'] =  date( 'Y-m-d', strtotime('-1 day') ).' '.$getWorkShiftByDayOfTheWeek[0]->end_time;
			$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek[0]->rest_day;

		}

	} else {

		return 'getClockingOutDateTimeForgotToClockOutClockIn1 Check default schedule and Scheduled table for this employee';

	}


	$employeeNightDiffClocking->time_out_1		= $clockingDateTime;										
	$employeeNightDiffClocking->clocking_status = 'yesterday_clock_out';			

	$clockingIn = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_1)); // From 2015-04-30 08:36:16 change to 08:36:16
	$clockingOut = date('Y-m-d H:i:s', strtotime($clockingDateTime)); // From 2015-04-30 08:36:16 change to 08:36:16		
	
	$clockingInDay = strtolower(date('D', strtotime($employeeNightDiffClocking->time_in_1))); //Check day name e.g thu
	$clockingOutDay = strtolower(date('D', strtotime($clockingDateTime))); //Check day name e.g thu		

	$clockingInDateTime = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_1));
	$clockingOutDateTime = date('Y-m-d H:i:s', strtotime($clockingDateTime));	


	//YESTERDAY CLOCKOUT
	if ( $timeInDateTime < 15 ) { //Test: set to 6 to see forgot to clock out example and 12 to see yesterday clock out
	
		echo 'yesterday_clock_out';

		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

			echo "SCHEDULED : TRUE \n";

			//REST DAY: FALSE
			if ( $scheduled['rest_day'] !== 1 ) {

				echo "REST DAY: FALSE \n";

				//LATE/TARDINESS: TRUE
				if ( !empty($employeeNightDiffClocking->tardiness_1) ) {					

					echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					// (in + out) - breaktime
					//$employeeNightDiffClocking->work_hours_1 = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_1 = 8.00;

					} else {

						$employeeNightDiffClocking->work_hours_1 = $workHours;

					}

					//TODO: Compute total hours with out overtime - getTotalHours
					// in + out + overtime
					$employeeNightDiffClocking->total_hours_1 = getTotalHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

				//LATE/TARDINESS: FALSE
				} else {

					echo "LATE/TARDINESS: FALSE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					// (in + out) - breaktime
					//$employeeNightDiffClocking->work_hours_1 = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_1 = 8.00;

					} else {

						$employeeNightDiffClocking->work_hours_1 = $workHours;

					}					

					//TODO: Compute total hours with overtime - getTotalHours
					// in + out + overtime
					$employeeNightDiffClocking->total_hours_1 = getTotalHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $employeeNightDiffClocking->time_in_1);
				$timeOutArr = explode(' ', $clockingDateTime);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeNightDiffClocking->time_in_1)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

			    $hasNightDiff = false;

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			        	$hasNightDiff = true;

			            //$employeeNightDiffClocking->night_differential_1 = 8.00;
			            $employeeNightDiffClocking->night_differential_1 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
			            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

			        } else {

			        	$hasNightDiff = true;		        	

			           //$employeeNightDiffClocking->night_differential_1 = $workHours;
			           //$employeeNightDiffClocking->night_differential_1 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
					   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;	

			            $employeeNightDiffClocking->night_differential_1 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
			            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;					  	            

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {

			        	$hasNightDiff = true;		        	
			            
						//$employeeNightDiffClocking->night_differential_1 = 8.00;
						$employeeNightDiffClocking->night_differential_1 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
						$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;										

			        } else {

			        	$hasNightDiff = true;		        	

						//$employeeNightDiffClocking->night_differential_1 = $workHours;
						$employeeNightDiffClocking->night_differential_1 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
						$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					

			        }
			    } else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
			           	$hasNightDiff = true;

						//$employeeNightDiffClocking->night_differential_1 = 8.00;
						$employeeNightDiffClocking->night_differential_1 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
						$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;

			        }
			        
			    }

				//UNDERTIME: TRUE
				if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					echo "UNDERTIME: TRUE \n";
					echo $employeeNightDiffClocking->undertime_1 = getUnderTimeHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$employeeSummaryNightDiffClocking->undertime = getUnderTimeHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);
				}

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($employeeNightDiffClocking->time_in_1)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$employeeNightDiffClocking->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}				

				//HOLIDAY: TRUE
				if( hasHoliday($currentDate) ) {

					 echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

						echo "Regular holiday \n";

						$employeeSummaryNightDiffClocking->legal_holiday = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);					

						if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

							$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

							$overtimeNightdiff = $overtimeHours + $nightDifferential;

							$employeeSummaryNightDiffClocking->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

						}

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							//$employeeSummaryNightDiffClocking->legal_holiday = getTotalHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);															
							$employeeSummaryNightDiffClocking->legal_holiday = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/	

						if($isOvertime) { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						

						}

						if($hasNightDiff) { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->legal_holiday_night_diff = $nightDifferential;

						}													


					} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day				

						echo "Special non-working day \n";

						$employeeSummaryNightDiffClocking->special_holiday = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);

						if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

							$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

							$overtimeNightdiff = $overtimeHours + $nightDifferential;

							$employeeSummaryNightDiffClocking->special_holiday_overtime_night_diff = $overtimeNightdiff;						

						}					

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							//$employeeSummaryNightDiffClocking->special_holiday = getTotalHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																		
							$employeeSummaryNightDiffClocking->special_holiday = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/

						if($isOvertime) { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							
						}

						if($hasNightDiff) { //HASNIGHTDIFF: TRUE

							$employeeSummaryNightDiffClocking->special_holiday_night_diff = $nightDifferential;

						}						

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					echo "HOLIDAY: FALSE \n";

					echo "Regular Day \n";	

					$employeeSummaryNightDiffClocking->regular = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);				

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummaryNightDiffClocking->regular_overtime_night_diff = $overtimeNightdiff;						

					}					

					/*if (!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummaryNightDiffClocking->regular = getTotalHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																																	
						$employeeSummaryNightDiffClocking->regular = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/

					if ($isOvertime) { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						
					}				

					if($hasNightDiff) { //HASNIGHTDIFF: TRUE

						$employeeSummaryNightDiffClocking->regular_night_differential = $nightDifferential;

					}									

				}

				if ( $employeeNightDiffClocking->save() ) {
							
					$employeeSummaryNightDiffClocking->save();
					return Redirect::to('/redraw/timesheet');			

				}							

			//REST DAY: TRUE
			} elseif ( $scheduled['rest_day'] === 1 ) {

				echo "REST DAY: TRUE \n";

				//TODO RULES: First 8 Hours get the hours_per_day in employee setting

				//LATE/TARDINESS: TRUE
				if ( !empty($employeeNightDiffClocking->tardiness_1) ) {

					echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					//$employeeNightDiffClocking->work_hours_1 = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_1 = 8.00;

					} else {

						$employeeNightDiffClocking->work_hours_1 = $workHours;

					}

					//TODO: Compute total hours with out overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_1 = getTotalHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

				//LATE/TARDINESS: FALSE
				} else {

					echo "LATE/TARDINESS: FALSE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					//$employeeNightDiffClocking->work_hours_1 = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																	

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_1 = 8.00;	

					} else {

						$employeeNightDiffClocking->work_hours_1 = $workHours;

					}					

					//TODO: Compute total hours with overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_1 = getTotalHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $employeeNightDiffClocking->time_in_1);
				$timeOutArr = explode(' ', $clockingDateTime);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeNightDiffClocking->time_in_1)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

			    $hasNightDiff = false;

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

						$hasNightDiff = true;
			     
			            //$employeeNightDiffClocking->night_differential_1 = 8.00;
			            $employeeNightDiffClocking->night_differential_1 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
			            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

			        } else {

					   $hasNightDiff = true;		        	

			           //$employeeNightDiffClocking->night_differential_1 = $workHours;
			           //$employeeNightDiffClocking->night_differential_1 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
					   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;

			            $employeeNightDiffClocking->night_differential_1 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
			            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;					   

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {

						$hasNightDiff = true;		        	
			            
						//$employeeNightDiffClocking->night_differential_1 = 8.00;
						$employeeNightDiffClocking->night_differential_1 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
						$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;

			        } else {

						$hasNightDiff = true;		        	

						//$employeeNightDiffClocking->night_differential_1 = $workHours;
						$employeeNightDiffClocking->night_differential_1 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
						$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;

			        }
			    } else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {

						$hasNightDiff = true;		        	
			            
						//$employeeNightDiffClocking->night_differential_1 = 8.00;
						$employeeNightDiffClocking->night_differential_1 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
						$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;					

			        }
			        
			    }				

				//UNDERTIME: TRUE
				if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					echo "UNDERTIME: TRUE \n";
					echo $employeeNightDiffClocking->undertime_1 = getUnderTimeHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$employeeSummaryNightDiffClocking->undertime = getUnderTimeHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			

				}

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($employeeNightDiffClocking->time_in_1)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$employeeNightDiffClocking->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}				

				//HOLIDAY: TRUE
				if( hasHoliday($currentDate) ) {

					 echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

						echo "Regular holiday \n";

						$employeeSummaryNightDiffClocking->rest_day_legal_holiday = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);

						if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

							$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

							$overtimeNightdiff = $overtimeHours + $nightDifferential;

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;						

						}					

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							//$employeeSummaryNightDiffClocking->rest_day_legal_holiday = getTotalHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);															
							$employeeSummaryNightDiffClocking->rest_day_legal_holiday = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/

						if($isOvertime) { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}				

						if($hasNightDiff) { //HASNIGHTDIFF: TRUE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_night_diff = $nightDifferential;

						}											


					} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
						
						echo "Special non-working day \n";

						$employeeSummaryNightDiffClocking->rest_day_special_holiday = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);					

						if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

							$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

							$overtimeNightdiff = $overtimeHours + $nightDifferential;

							$employeeSummaryNightDiffClocking->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;						

						}					

						if(!$isOvertime) { //ISOVERTIME: FALSE

							//$employeeSummaryNightDiffClocking->rest_day_legal_holiday = getTotalHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);															
							$employeeSummaryNightDiffClocking->rest_day_legal_holiday = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}					

						if($isOvertime) { //ISOVERTIME: FALSE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						
						}	

						if($hasNightDiff) { //HASNIGHTDIFF: TRUE

							$employeeSummaryNightDiffClocking->rest_day_special_holiday_night_diff = $nightDifferential;

						}					

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					echo "HOLIDAY: FALSE \n";

					echo "Regular Rest Day \n";		

					$employeeSummaryNightDiffClocking->rest_day = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);				

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummaryNightDiffClocking->rest_day_overtime_night_diff = $overtimeNightdiff;						

					}					

					/*if (!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummaryNightDiffClocking->rest_day = getTotalHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																																	
						$employeeSummaryNightDiffClocking->rest_day = getWorkHours($employeeNightDiffClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/	

					if ($isOvertime) { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}				

					if($hasNightDiff) { //HASNIGHTDIFF: TRUE

						$employeeSummaryNightDiffClocking->rest_day_night_differential = $nightDifferential;

					}	


				}

				if ( $employeeNightDiffClocking->save() ) {
							
					$employeeSummaryNightDiffClocking->save();
					return Redirect::to('/redraw/timesheet');			

				}			

			}

		//SCHEDULED : FALSE
		//HAS NO SCHEDULE - EMERGENCY OR SECOND SHIFT			
		} else {

			echo "SCHEDULED : FALSE \n";	

		}


	//FORGOT TO CLOCK OUT	
	} elseif ( $timeInDateTime > 12 ) { //Forgot to clock out don't have computation for work hours, tota hours, total overtime, undertime.

		echo 'forgot_to_clock_out';

		$employeeClocking->time_out_1				= $clockingDateTime;
		$employeeClocking->clocking_status			= 'forgot_to_clock_out';

		$employeeNightDiffClocking->clocking_status = 'clock_out_1';
		$employeeNightDiffClocking->forgot_to_clock_out = 1;
		$employeeNightDiffClocking->save();		

		if ( $employeeClocking->save() ) {

			$employeeNightDiffClocking->save();	
			
			return Redirect::to('/redraw/timesheet');					

		}		

	}

}


function getClockingOutDateTimeForgotToClockOutClockIn2($clockingStatus = '', $clockingDateTime = '', $yesterdayClockingStatus = '') {

	//return 'Initialize: Forget to Clock out yesterday or';

	//Settings
	$breakTime = date('H:i', strtotime('01:00:00'));

	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');

	$currentDate = date('Y-m-d');		
	$yesterDayDate = date("Y-m-d", strtotime('yesterday'));
	$dayOfTheWeek = date('l');

	$timeInDateTime = date('G', strtotime($clockingDateTime));

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));
	$getScheduleYesterday = $schedule->getSchedule($employeeId, $yesterDayDate);

	$workShift = new Workshift;
	//$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 1);

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);

	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$getYesterDayDate = $timesheet->getTimesheetYesterday($employeeId, $yesterDayDate);
	$employeeNightDiffClocking = $timesheet->getEmployeeNightDiffClocking($employeeId, $yesterDayDate);

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));		
	$employeeSummaryNightDiffClocking = $summary->getEmployeeSummaryNightDiffClocking($employeeId, $yesterDayDate);

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

	if( !empty($getSchedule) && !empty($getScheduleYesterday) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;	

		$scheduledYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
		$scheduledYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
		$scheduledYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;			

	} elseif( !empty($getSchedule) && empty($getScheduleYesterday) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;	

	} elseif( empty($getSchedule) && !empty($getScheduleYesterday) ) {

		$scheduledYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
		$scheduledYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
		$scheduledYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;						

	} elseif( empty($getSchedule) && empty($getScheduleYesterday) ) {

		if( !empty($getWorkShiftByDayOfTheWeek) ) {

			$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeNightDiffClocking->time_in_2) ).' '.$getWorkShiftByDayOfTheWeek[0]->start_time;						
			$scheduled['end_time'] =  date( 'Y-m-d', strtotime('-1 day') ).' '.$getWorkShiftByDayOfTheWeek[0]->end_time;
			$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek[0]->rest_day;

		}

	} else {

		return 'getClockingOutDateTimeForgotToClockOutClockIn2 Check default schedule and Scheduled table for this employee';

	}


	$employeeNightDiffClocking->time_out_2		= $clockingDateTime;										
	$employeeNightDiffClocking->clocking_status = 'yesterday_clock_out';			

	$clockingIn = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_2)); // From 2015-04-30 08:36:16 change to 08:36:16
	$clockingOut = date('Y-m-d H:i:s', strtotime($clockingDateTime)); // From 2015-04-30 08:36:16 change to 08:36:16		
	
	$clockingInDay = strtolower(date('D', strtotime($employeeNightDiffClocking->time_in_2))); //Check day name e.g thu
	$clockingOutDay = strtolower(date('D', strtotime($clockingDateTime))); //Check day name e.g thu		

	$clockingInDateTime = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_2));
	$clockingOutDateTime = date('Y-m-d H:i:s', strtotime($clockingDateTime));	


	//YESTERDAY CLOCKOUT
	if ( $timeInDateTime < 15 ) { //Test: set to 6 to see forgot to clock out example and 12 to see yesterday clock out
	
		echo 'yesterday_clock_out';

		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

			echo "SCHEDULED : TRUE \n";

			//REST DAY: FALSE
			if ( $scheduled['rest_day'] !== 1 ) {

				echo "REST DAY: FALSE \n";

				//LATE/TARDINESS: TRUE
				if ( !empty($employeeNightDiffClocking->tardiness_2) ) {

					echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					//$employeeNightDiffClocking->work_hours_2 = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_2 = 8.00;

					} else {

						$employeeNightDiffClocking->work_hours_2 = $workHours;						

					}					

					//TODO: Compute total hours with out overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_2 = getTotalHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);															

				//LATE/TARDINESS: FALSE
				} else {

					echo "LATE/TARDINESS: FALSE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					//$employeeNightDiffClocking->work_hours_2 = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_2 = 8.00;

					} else {

						$employeeNightDiffClocking->work_hours_2 = $workHours;						

					}		

					//TODO: Compute total hours with overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_2 = getTotalHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $employeeNightDiffClocking->time_in_2);
				$timeOutArr = explode(' ', $clockingDateTime);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeNightDiffClocking->time_in_2)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

			    $hasNightDiff = false;

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			        	$hasNightDiff = true;

			            //$employeeNightDiffClocking->night_differential_2 = 8.00;
			            $employeeNightDiffClocking->night_differential_2 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
			            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

			        } else {

			        	$hasNightDiff = true;		        	

			           //$employeeNightDiffClocking->night_differential_2 = $workHours;
			           //$employeeNightDiffClocking->night_differential_2 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
					   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            

			            $employeeNightDiffClocking->night_differential_2 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
			            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;			        	

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {

			        	$hasNightDiff = true;		        	
			            
						//$employeeNightDiffClocking->night_differential_2 = 8.00;
						$employeeNightDiffClocking->night_differential_2 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
						$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;										

			        } else {

			        	$hasNightDiff = true;		        	

						//$employeeNightDiffClocking->night_differential_2 = $workHours;
						$employeeNightDiffClocking->night_differential_2 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
						$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					

			        }
			    } else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
			           	$hasNightDiff = true;

						//$employeeNightDiffClocking->night_differential_2 = 8.00;
						$employeeNightDiffClocking->night_differential_2 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
						$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;

			        }
			        
			    }			

				//UNDERTIME: TRUE
				if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					echo "UNDERTIME: TRUE \n";
					echo $employeeNightDiffClocking->undertime_2 = getUnderTimeHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$employeeSummaryNightDiffClocking->undertime = getUnderTimeHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																								

				}

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($employeeNightDiffClocking->time_in_2)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$employeeNightDiffClocking->total_overtime_2 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}				

				//HOLIDAY: TRUE
				if( hasHoliday($currentDate) ) {

					 echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

						echo "Regular holiday \n";

						$employeeSummaryNightDiffClocking->legal_holiday = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);					

						if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

							$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

							$overtimeNightdiff = $overtimeHours + $nightDifferential;

							$employeeSummaryNightDiffClocking->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

						}

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							//$employeeSummaryNightDiffClocking->legal_holiday = getTotalHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);															
							$employeeSummaryNightDiffClocking->legal_holiday = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/	

						if($isOvertime) { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						

						}

						if($hasNightDiff) { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->legal_holiday_night_diff = $nightDifferential;

						}													


					} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day				

						echo "Special non-working day \n";

						$employeeSummaryNightDiffClocking->special_holiday = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);

						if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

							$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

							$overtimeNightdiff = $overtimeHours + $nightDifferential;

							$employeeSummaryNightDiffClocking->special_holiday_overtime_night_diff = $overtimeNightdiff;						

						}					

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							//$employeeSummaryNightDiffClocking->special_holiday = getTotalHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);																		
							$employeeSummaryNightDiffClocking->special_holiday = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/

						if($isOvertime) { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
							
						}

						if($hasNightDiff) { //HASNIGHTDIFF: TRUE

							$employeeSummaryNightDiffClocking->special_holiday_night_diff = $nightDifferential;

						}						

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					echo "HOLIDAY: FALSE \n";

					echo "Regular Day \n";	

					$employeeSummaryNightDiffClocking->regular = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);				

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummaryNightDiffClocking->regular_overtime_night_diff = $overtimeNightdiff;						

					}					

					/*if (!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummaryNightDiffClocking->regular = getTotalHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);																																	
						$employeeSummaryNightDiffClocking->regular = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/

					if ($isOvertime) { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						
					}				

					if($hasNightDiff) { //HASNIGHTDIFF: TRUE

						$employeeSummaryNightDiffClocking->regular_night_differential = $nightDifferential;

					}									

				}

				if ( $employeeNightDiffClocking->save() ) {
							
					$employeeSummaryNightDiffClocking->save();
					return Redirect::to('/redraw/timesheet');			

				}							

			//REST DAY: TRUE
			} elseif ( $scheduled['rest_day'] === 1 ) {

				echo "REST DAY: TRUE \n";

				//TODO RULES: First 8 Hours get the hours_per_day in employee setting

				//LATE/TARDINESS: TRUE
				if ( !empty($employeeNightDiffClocking->tardiness_2) ) {

					echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					//$employeeNightDiffClocking->work_hours_2 = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_2 = 8.00;

					} else {

						$employeeNightDiffClocking->work_hours_2 = $workHours;						

					}		

					//TODO: Compute total hours with out overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_2 = getTotalHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

				//LATE/TARDINESS: FALSE
				} else {

					echo "LATE/TARDINESS: FALSE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					//$employeeNightDiffClocking->work_hours_2 = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_2 = 8.00;

					} else {

						$employeeNightDiffClocking->work_hours_2 = $workHours;						

					}		

					//TODO: Compute total hours with overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_2 = getTotalHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

				}

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $employeeNightDiffClocking->time_in_2);
				$timeOutArr = explode(' ', $clockingDateTime);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeNightDiffClocking->time_in_2)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

			    $hasNightDiff = false;

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

						$hasNightDiff = true;
			     
			            //$employeeNightDiffClocking->night_differential_2 = 8.00;
			            $employeeNightDiffClocking->night_differential_2 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
			            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

			        } else {

					   $hasNightDiff = true;		        	

			           //$employeeNightDiffClocking->night_differential_2 = $workHours;
			           //$employeeNightDiffClocking->night_differential_2 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
					   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;

			            $employeeNightDiffClocking->night_differential_2 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
			            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;					   

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {

						$hasNightDiff = true;		        	
			            
						//$employeeNightDiffClocking->night_differential_2 = 8.00;
						$employeeNightDiffClocking->night_differential_2 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
						$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;

			        } else {

						$hasNightDiff = true;		        	

						//$employeeNightDiffClocking->night_differential_2 = $workHours;
						$employeeNightDiffClocking->night_differential_2 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
						$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;

			        }
			    } else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {

						$hasNightDiff = true;		        	
			            
						//$employeeNightDiffClocking->night_differential_2 = 8.00;
						$employeeNightDiffClocking->night_differential_2 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
						$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;					

			        }
			        
			    }				

				//UNDERTIME: TRUE
				if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					echo "UNDERTIME: TRUE \n";
					echo $employeeNightDiffClocking->undertime_2 = getUnderTimeHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$employeeSummaryNightDiffClocking->undertime = getUnderTimeHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																								

				}

				//OVERTIME: TRUE
				$isOvertime = false;

				if ( date('H:i', strtotime($employeeNightDiffClocking->time_in_2)) <= date('H:i', strtotime($scheduled['start_time'])) &&
					 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$employeeNightDiffClocking->total_overtime_2 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}				

				//HOLIDAY: TRUE
				if( hasHoliday($currentDate) ) {

					 echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

						echo "Regular holiday \n";

						$employeeSummaryNightDiffClocking->rest_day_legal_holiday = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);

						if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

							$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

							$overtimeNightdiff = $overtimeHours + $nightDifferential;

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;						

						}					

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							//$employeeSummaryNightDiffClocking->rest_day_legal_holiday = getTotalHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);															
							$employeeSummaryNightDiffClocking->rest_day_legal_holiday = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/

						if($isOvertime) { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}				

						if($hasNightDiff) { //HASNIGHTDIFF: TRUE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_night_diff = $nightDifferential;

						}											


					} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
						
						echo "Special non-working day \n";

						$employeeSummaryNightDiffClocking->rest_day_special_holiday = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);					

						if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

							$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

							$overtimeNightdiff = $overtimeHours + $nightDifferential;

							$employeeSummaryNightDiffClocking->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;						

						}					

						if(!$isOvertime) { //ISOVERTIME: FALSE

							//$employeeSummaryNightDiffClocking->rest_day_legal_holiday = getTotalHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);															
							$employeeSummaryNightDiffClocking->rest_day_legal_holiday = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}					

						if($isOvertime) { //ISOVERTIME: FALSE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						
						}	

						if($hasNightDiff) { //HASNIGHTDIFF: TRUE

							$employeeSummaryNightDiffClocking->rest_day_special_holiday_night_diff = $nightDifferential;

						}					

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					echo "HOLIDAY: FALSE \n";

					echo "Regular Rest Day \n";		

					$employeeSummaryNightDiffClocking->rest_day = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);				

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummaryNightDiffClocking->rest_day_overtime_night_diff = $overtimeNightdiff;						

					}					

					/*if (!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummaryNightDiffClocking->rest_day = getTotalHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);																																	
						$employeeSummaryNightDiffClocking->rest_day = getWorkHours($employeeNightDiffClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/	

					if ($isOvertime) { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}				

					if($hasNightDiff) { //HASNIGHTDIFF: TRUE

						$employeeSummaryNightDiffClocking->rest_day_night_differential = $nightDifferential;

					}	


				}

				if ( $employeeNightDiffClocking->save() ) {
							
					$employeeSummaryNightDiffClocking->save();
					return Redirect::to('/redraw/timesheet');			

				}			

			}

		//SCHEDULED : FALSE
		//HAS NO SCHEDULE - EMERGENCY OR SECOND SHIFT			
		} else {

			echo "SCHEDULED : FALSE \n";	

		}


	//FORGOT TO CLOCK OUT	
	} elseif ( $timeInDateTime > 12 ) { //Forgot to clock out don't have computation for work hours, tota hours, total overtime, undertime.

		echo 'forgot_to_clock_out';

		$employeeClocking->time_out_2				= $clockingDateTime;
		$employeeClocking->clocking_status			= 'forgot_to_clock_out';

		$employeeNightDiffClocking->clocking_status = 'clock_out_2';
		$employeeNightDiffClocking->forgot_to_clock_out = 1;
		$employeeNightDiffClocking->save();		

		if ( $employeeClocking->save() ) {

			$employeeNightDiffClocking->save();	
			
			return Redirect::to('/redraw/timesheet');					

		}		

	}

}

function getClockingOutDateTimeForgotToClockOutClockIn3($clockingStatus = '', $clockingDateTime = '', $yesterdayClockingStatus = '') {


	//return 'Initialize: Forget to Clock out yesterday or';

	//Settings
	$breakTime = date('H:i', strtotime('01:00:00'));

	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');

	$currentDate = date('Y-m-d');		
	$yesterDayDate = date("Y-m-d", strtotime('yesterday'));
	$dayOfTheWeek = date('l');

	$timeInDateTime = date('G', strtotime($clockingDateTime));

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));
	$getScheduleYesterday = $schedule->getSchedule($employeeId, $yesterDayDate);

	$workShift = new Workshift;
	//$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 1);

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);

	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$getYesterDayDate = $timesheet->getTimesheetYesterday($employeeId, $yesterDayDate);
	$employeeNightDiffClocking = $timesheet->getEmployeeNightDiffClocking($employeeId, $yesterDayDate);

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));		
	$employeeSummaryNightDiffClocking = $summary->getEmployeeSummaryNightDiffClocking($employeeId, $yesterDayDate);

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

	if( !empty($getSchedule) && !empty($getScheduleYesterday) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;	

		$scheduledYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
		$scheduledYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
		$scheduledYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;			

	} elseif( !empty($getSchedule) && empty($getScheduleYesterday) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;	

	} elseif( empty($getSchedule) && !empty($getScheduleYesterday) ) {

		$scheduledYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
		$scheduledYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
		$scheduledYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;						

	} elseif( empty($getSchedule) && empty($getScheduleYesterday) ) {

		if( !empty($getWorkShiftByDayOfTheWeek) ) {

			$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeNightDiffClocking->time_in_3) ).' '.$getWorkShiftByDayOfTheWeek[0]->start_time;						
			$scheduled['end_time'] =  date( 'Y-m-d', strtotime('-1 day') ).' '.$getWorkShiftByDayOfTheWeek[0]->end_time;
			$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek[0]->rest_day;

		}

	} else {

		return 'getClockingOutDateTimeForgotToClockOutClockIn3 Check default schedule and Scheduled table for this employee';

	}


	$employeeNightDiffClocking->time_out_3		= $clockingDateTime;										
	$employeeNightDiffClocking->clocking_status = 'yesterday_clock_out';			

	$clockingIn = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_3)); // From 2015-04-30 08:36:16 change to 08:36:16
	$clockingOut = date('Y-m-d H:i:s', strtotime($clockingDateTime)); // From 2015-04-30 08:36:16 change to 08:36:16		
	
	$clockingInDay = strtolower(date('D', strtotime($employeeNightDiffClocking->time_in_3))); //Check day name e.g thu
	$clockingOutDay = strtolower(date('D', strtotime($clockingDateTime))); //Check day name e.g thu		

	$clockingInDateTime = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_3));
	$clockingOutDateTime = date('Y-m-d H:i:s', strtotime($clockingDateTime));	


	//YESTERDAY CLOCKOUT
	if ( $timeInDateTime < 15 ) { //Test: set to 6 to see forgot to clock out example and 12 to see yesterday clock out
	
		echo 'yesterday_clock_out';

		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

			echo "SCHEDULED : TRUE \n";

			//REST DAY: FALSE
			if ( $scheduled['rest_day'] !== 1 ) {

				echo "REST DAY: FALSE \n";

				//LATE/TARDINESS: TRUE
				//if ( !empty($employeeNightDiffClocking->tardiness_3) ) {

					echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					//$employeeNightDiffClocking->work_hours_3 = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																	
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												
						
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_3 = 8.00;						

					} else {

						$employeeNightDiffClocking->work_hours_3 = $workHours;						

					}		
							
					//TODO: Compute total hours with out overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_3 = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				//LATE/TARDINESS: FALSE
				/*} else {

					echo "LATE/TARDINESS: FALSE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					$employeeNightDiffClocking->work_hours_3 = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

					//TODO: Compute total hours with overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_3 = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				}*/

			//GET NIGHTDIFF
			$timeInArr = array();
			$timeOutArr = array();

			$timeInArr = explode(' ', $employeeNightDiffClocking->time_in_3);
			$timeOutArr = explode(' ', $clockingDateTime);

			$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
			$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

		    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeNightDiffClocking->time_in_3)));			  
		    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

		    $hasNightDiff = false;

		    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
		    {
		        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

		        	$hasNightDiff = true;

		            //$employeeNightDiffClocking->night_differential_3 = 8.00;
		            $employeeNightDiffClocking->night_differential_3 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

		        } else {

		        	$hasNightDiff = true;		        	

		           //$employeeNightDiffClocking->night_differential_3 = $workHours;
		           //$employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
				   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;

		            $employeeNightDiffClocking->night_differential_3 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;				   		            

		        }

		    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
		        
		        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {

		        	$hasNightDiff = true;		        	
		            
					//$employeeNightDiffClocking->night_differential_3 = 8.00;
					$employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;										

		        } else {

		        	$hasNightDiff = true;		        	

					//$employeeNightDiffClocking->night_differential_3 = $workHours;
					$employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					

		        }
		    } else {

		        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
		            
		           	$hasNightDiff = true;

					//$employeeNightDiffClocking->night_differential_3 = 8.00;
					$employeeNightDiffClocking->night_differential_3 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
					$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;

		        }
		        
		    }				

				//UNDERTIME: TRUE
				/*if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					echo "UNDERTIME: TRUE \n";
					echo $employeeNightDiffClocking->undertime_3 = getUnderTimeHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$employeeSummaryNightDiffClocking->undertime = getUnderTimeHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			

				}*/

				//OVERTIME: TRUE
				$isOvertime = false;

				//if ( date('H:i', strtotime($employeeNightDiffClocking->time_in_3)) <= date('H:i', strtotime($scheduled['start_time'])) &&
				//	 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$employeeNightDiffClocking->total_overtime_3 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				//}				

				//HOLIDAY: TRUE
				if( hasHoliday($currentDate) ) {

					 echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

						echo "Regular holiday \n";

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							$employeeSummaryNightDiffClocking->legal_holiday_night_diff = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);															
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->legal_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/						

						//$employeeSummaryNightDiffClocking->legal_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						
						$employeeSummaryNightDiffClocking->legal_holiday_overtime_night_diff = ($employeeSummaryNightDiffClocking->legal_holiday_overtime_night_diff + getOvertimeHours($clockingDateTime, $scheduled['end_time']));						

						if($hasNightDiff) { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->legal_holiday_night_diff = $nightDifferential;

						}						

					} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
						
						echo "Special non-working day \n";

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							$employeeSummaryNightDiffClocking->special_holiday_night_diff = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																		
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->special_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/	

						//$employeeSummaryNightDiffClocking->special_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						
						$employeeSummaryNightDiffClocking->special_holiday_overtime_night_diff = ($employeeSummaryNightDiffClocking->special_holiday_overtime_night_diff + getOvertimeHours($clockingDateTime, $scheduled['end_time']));												

						if($hasNightDiff) { //ISOVERTIME: TRUE

							//$employeeSummaryNightDiffClocking->special_holiday_night_diff = $nightDifferential;
							$employeeSummaryNightDiffClocking->special_holiday_night_diff = ($employeeSummaryNightDiffClocking->special_holiday_night_diff + $nightDifferential);

						}							

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					echo "HOLIDAY: FALSE \n";

					echo "Regular Day \n";		

					/*if (!$isOvertime) { //ISOVERTIME: FALSE

						$employeeSummaryNightDiffClocking->regular_night_differential = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																																	
						
					} else { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->regular_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/					

					//$employeeSummaryNightDiffClocking->regular_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);					
					$employeeSummaryNightDiffClocking->regular_overtime_night_diff = ($employeeSummaryNightDiffClocking->regular_overtime_night_diff + getOvertimeHours($clockingDateTime, $scheduled['end_time']));										

					if($hasNightDiff) { //ISOVERTIME: TRUE

						//$employeeSummaryNightDiffClocking->regular_night_differential = $nightDifferential;
						$employeeSummaryNightDiffClocking->regular_night_differential = ($employeeSummaryNightDiffClocking->regular_night_differential + $nightDifferential);

					}					

				}

				if ( $employeeNightDiffClocking->save() ) {
							
					$employeeSummaryNightDiffClocking->save();
					return Redirect::to('/redraw/timesheet');			

				}							

			//REST DAY: TRUE
			} elseif ( $scheduled['rest_day'] === 1 ) {

				echo "REST DAY: TRUE \n";

				//TODO RULES: First 8 Hours get the hours_per_day in employee setting

				//LATE/TARDINESS: TRUE
				//if ( !empty($employeeNightDiffClocking->tardiness_3) ) {

					echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					//$employeeNightDiffClocking->work_hours_3 = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																	
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												
						
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_3 = 8.00;

					} else {

						$employeeNightDiffClocking->work_hours_3 = $workHours;						

					}

					//TODO: Compute total hours with out overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_3 = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				//LATE/TARDINESS: FALSE
				/*} else {

					echo "LATE/TARDINESS: FALSE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					$employeeNightDiffClocking->work_hours_3 = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

					//TODO: Compute total hours with overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_3 = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				}*/

				//GET NIGHTDIFF
				$timeInArr = array();
				$timeOutArr = array();

				$timeInArr = explode(' ', $employeeNightDiffClocking->time_in_3);
				$timeOutArr = explode(' ', $clockingDateTime);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
				$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeNightDiffClocking->time_in_3)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

			    $hasNightDiff = false;

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

						$hasNightDiff = true;
			     
			            //$employeeNightDiffClocking->night_differential_3 = 8.00;
			            $employeeNightDiffClocking->night_differential_3 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
			            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

			        } else {

					   $hasNightDiff = true;		        	

			           //$employeeNightDiffClocking->night_differential_3 = $workHours;
			           //$employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
					   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;

			            $employeeNightDiffClocking->night_differential_3 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
			            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;					   

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {

						$hasNightDiff = true;		        	
			            
						//$employeeNightDiffClocking->night_differential_3 = 8.00;
						$employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
						$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;

			        } else {

						$hasNightDiff = true;		        	

						//$employeeNightDiffClocking->night_differential_3 = $workHours;
						$employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
						$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;

			        }
			    } else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {

						$hasNightDiff = true;		        	
			            
						//$employeeNightDiffClocking->night_differential_3 = 8.00;
						$employeeNightDiffClocking->night_differential_3 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
						$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;					

			        }
			        
			    }			

				//UNDERTIME: TRUE
				/*if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					echo "UNDERTIME: TRUE \n";
					echo $employeeNightDiffClocking->undertime_3 = getUnderTimeHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$employeeSummaryNightDiffClocking->undertime = getUnderTimeHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																								

				}*/

				//OVERTIME: TRUE
				$isOvertime = false;

				//if ( date('H:i', strtotime($employeeNightDiffClocking->time_in_3)) <= date('H:i', strtotime($scheduled['start_time'])) &&
				//	 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$employeeNightDiffClocking->total_overtime_3 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				//}				

				//HOLIDAY: TRUE
				if( hasHoliday($currentDate) ) {

					 echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

						echo "Regular holiday \n";

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_night_diff = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);															
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/						

						$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime_night_diff = ($employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime_night_diff + getOvertimeHours($clockingDateTime, $scheduled['end_time']));						

						if($hasNightDiff) { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_night_diff = ($employeeSummaryNightDiffClocking->rest_day_legal_holiday_night_diff + $nightDifferential);

						}							

					} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
						

						echo "Special non-working day \n";

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							$employeeSummaryNightDiffClocking->rest_day_special_holiday_night_diff = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																		
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->rest_day_special_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/	

						//$employeeSummaryNightDiffClocking->rest_day_special_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						
						$employeeSummaryNightDiffClocking->rest_day_special_holiday_overtime_night_diff = ($employeeSummaryNightDiffClocking->rest_day_special_holiday_overtime_night_diff + getOvertimeHours($clockingDateTime, $scheduled['end_time']));						

						if($hasNightDiff) { //ISOVERTIME: TRUE

							//$employeeSummaryNightDiffClocking->rest_day_special_holiday_night_diff = $nightDifferential;
							$employeeSummaryNightDiffClocking->rest_day_special_holiday_night_diff = ($employeeSummaryNightDiffClocking->rest_day_special_holiday_night_diff + $nightDifferential);

						}							

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					echo "HOLIDAY: FALSE \n";

					echo "Regular Rest Day \n";		

					/*if (!$isOvertime) { //ISOVERTIME: FALSE

						$employeeSummaryNightDiffClocking->rest_day_night_differential = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																																	
						
					} else { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->rest_day_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/

					//$employeeSummaryNightDiffClocking->rest_day_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);									
					$employeeSummaryNightDiffClocking->rest_day_overtime_night_diff = ($employeeSummaryNightDiffClocking->rest_day_overtime_night_diff + getOvertimeHours($clockingDateTime, $scheduled['end_time']));														

					if($hasNightDiff) { //ISOVERTIME: TRUE

						//$employeeSummaryNightDiffClocking->rest_day_night_differential = $nightDifferential;
						$employeeSummaryNightDiffClocking->rest_day_night_differential = ($employeeSummaryNightDiffClocking->rest_day_night_differential + $nightDifferential);						

					}						

				}

				if ( $employeeNightDiffClocking->save() ) {
							
					$employeeSummaryNightDiffClocking->save();
					return Redirect::to('/redraw/timesheet');			

				}			

			}

		//SCHEDULED : FALSE
		//HAS NO SCHEDULE - EMERGENCY OR SECOND SHIFT
		} else {

			echo "SCHEDULED : FALSE \n";	

		}


	//FORGOT TO CLOCK OUT	
	} elseif ( $timeInDateTime > 12 ) { //Forgot to clock out don't have computation for work hours, tota hours, total overtime, undertime.

		echo 'forgot_to_clock_out';

		$employeeClocking->time_out_3				= $clockingDateTime;
		$employeeClocking->clocking_status			= 'forgot_to_clock_out';

		$employeeNightDiffClocking->clocking_status = 'clock_out_3';
		$employeeNightDiffClocking->forgot_to_clock_out = 1;
		$employeeNightDiffClocking->save();		

		if ( $employeeClocking->save() ) {

			$employeeNightDiffClocking->save();	
			
			return Redirect::to('/redraw/timesheet');					

		}		

	}

}

function getClockingOutDateTimeForgotToClockOutClockIn33($clockingStatus = '', $clockingDateTime = '', $yesterdayClockingStatus = '') {

	//return 'Initialize: Forget to Clock out yesterday or';

	//Settings
	$breakTime = date('H:i', strtotime('01:00:00'));

	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');

	$currentDate = date('Y-m-d');		
	$yesterDayDate = date("Y-m-d", strtotime('yesterday'));
	$dayOfTheWeek = date('l');

	$timeInDateTime = date('G', strtotime($clockingDateTime));

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));
	$getScheduleYesterday = $schedule->getSchedule($employeeId, $yesterDayDate);

	$workShift = new Workshift;
	//$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 1);

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);

	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$getYesterDayDate = $timesheet->getTimesheetYesterday($employeeId, $yesterDayDate);
	$employeeNightDiffClocking = $timesheet->getEmployeeNightDiffClocking($employeeId, $yesterDayDate);

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));		
	$employeeSummaryNightDiffClocking = $summary->getEmployeeSummaryNightDiffClocking($employeeId, $yesterDayDate);

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

	if( !empty($getSchedule) && !empty($getScheduleYesterday) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;	

		$scheduledYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
		$scheduledYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
		$scheduledYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;			

	} elseif( !empty($getSchedule) && empty($getScheduleYesterday) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;	

	} elseif( empty($getSchedule) && !empty($getScheduleYesterday) ) {

		$scheduledYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
		$scheduledYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
		$scheduledYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;						

	} elseif( empty($getSchedule) && empty($getScheduleYesterday) ) {

		if( !empty($getWorkShiftByDayOfTheWeek) ) {

			$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeNightDiffClocking->time_in_3) ).' '.$getWorkShiftByDayOfTheWeek[0]->start_time;						
			$scheduled['end_time'] =  date( 'Y-m-d', strtotime('-1 day') ).' '.$getWorkShiftByDayOfTheWeek[0]->end_time;
			$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek[0]->rest_day;

		}

	} else {

		return 'getClockingOutDateTimeForgotToClockOutClockIn3 Check default schedule and Scheduled table for this employee';

	}


	$employeeNightDiffClocking->time_out_3		= $clockingDateTime;										
	$employeeNightDiffClocking->clocking_status = 'yesterday_clock_out';			

	$clockingIn = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_3)); // From 2015-04-30 08:36:16 change to 08:36:16
	$clockingOut = date('Y-m-d H:i:s', strtotime($clockingDateTime)); // From 2015-04-30 08:36:16 change to 08:36:16		
	
	$clockingInDay = strtolower(date('D', strtotime($employeeNightDiffClocking->time_in_3))); //Check day name e.g thu
	$clockingOutDay = strtolower(date('D', strtotime($clockingDateTime))); //Check day name e.g thu		

	$clockingInDateTime = date('Y-m-d H:i:s', strtotime($employeeNightDiffClocking->time_in_3));
	$clockingOutDateTime = date('Y-m-d H:i:s', strtotime($clockingDateTime));	


	//YESTERDAY CLOCKOUT
	if ( $timeInDateTime < 15 ) { //Test: set to 6 to see forgot to clock out example and 12 to see yesterday clock out
	
		echo 'yesterday_clock_out';

		//SCHEDULED : TRUE
		if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
		     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

			echo "SCHEDULED : TRUE \n";

			//REST DAY: FALSE
			if ( $scheduled['rest_day'] !== 1 ) {

				echo "REST DAY: FALSE \n";

				//LATE/TARDINESS: TRUE
				//if ( !empty($employeeNightDiffClocking->tardiness_3) ) {

					echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					//$employeeNightDiffClocking->work_hours_3 = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																	
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												
						
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_3 = 8.00;						

					} else {

						$employeeNightDiffClocking->work_hours_3 = $workHours;						

					}		
							
					//TODO: Compute total hours with out overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_3 = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				//LATE/TARDINESS: FALSE
				/*} else {

					echo "LATE/TARDINESS: FALSE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					$employeeNightDiffClocking->work_hours_3 = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

					//TODO: Compute total hours with overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_3 = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				}*/

				//GET NIGHTDIFF
				$timeInArr = array();
  				$timeOutArr = array();

  				$timeInArr = explode(' ', $employeeNightDiffClocking->time_in_3);
  				$timeOutArr = explode(' ', $clockingDateTime);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
  				$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeNightDiffClocking->time_in_3)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			            //$employeeNightDiffClocking->night_differential_3 = 8.00;
			            $employeeNightDiffClocking->night_differential_3 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            

			        } else {

			           //$employeeNightDiffClocking->night_differential_3 = $workHours;
			           $employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
			            
						//$employeeNightDiffClocking->night_differential_3 = 8.00;
						$employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					

			        } else {

						//$employeeNightDiffClocking->night_differential_3 = $workHours;
						$employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					

			        }
			    } else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
						//$employeeNightDiffClocking->night_differential_3 = 8.00;
						$employeeNightDiffClocking->night_differential_3 = ($nightDiff['from'] - $nightDiff['to']) / 3600;

			        }
			        
			    }				

				//UNDERTIME: TRUE
				/*if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					echo "UNDERTIME: TRUE \n";
					echo $employeeNightDiffClocking->undertime_3 = getUnderTimeHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$employeeSummaryNightDiffClocking->undertime = getUnderTimeHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			

				}*/

				//OVERTIME: TRUE
				$isOvertime = false;

				//if ( date('H:i', strtotime($employeeNightDiffClocking->time_in_3)) <= date('H:i', strtotime($scheduled['start_time'])) &&
				//	 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$employeeNightDiffClocking->total_overtime_3 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				//}				

				//HOLIDAY: TRUE
				if( hasHoliday($currentDate) ) {

					 echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

						echo "Regular holiday \n";

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							$employeeSummaryNightDiffClocking->legal_holiday_night_diff = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);															
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->legal_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/						

						$employeeSummaryNightDiffClocking->legal_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						

					} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
						
						echo "Special non-working day \n";

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							$employeeSummaryNightDiffClocking->special_holiday_night_diff = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																		
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->special_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/	

						$employeeSummaryNightDiffClocking->special_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					echo "HOLIDAY: FALSE \n";

					echo "Regular Day \n";		

					/*if (!$isOvertime) { //ISOVERTIME: FALSE

						$employeeSummaryNightDiffClocking->regular_night_differential = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																																	
						
					} else { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->regular_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/					

					$employeeSummaryNightDiffClocking->regular_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);					

				}

				if ( $employeeNightDiffClocking->save() ) {
							
					$employeeSummaryNightDiffClocking->save();
					return Redirect::to('/redraw/timesheet');			

				}							

			//REST DAY: TRUE
			} elseif ( $scheduled['rest_day'] === 1 ) {

				echo "REST DAY: TRUE \n";

				//TODO RULES: First 8 Hours get the hours_per_day in employee setting

				//LATE/TARDINESS: TRUE
				//if ( !empty($employeeNightDiffClocking->tardiness_3) ) {

					echo "LATE/TARDINESS: TRUE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					//$employeeNightDiffClocking->work_hours_3 = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																	
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												
						
					$workHours = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

					if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

						$employeeNightDiffClocking->work_hours_3 = 8.00;

					} else {

						$employeeNightDiffClocking->work_hours_3 = $workHours;						

					}

					//TODO: Compute total hours with out overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_3 = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				//LATE/TARDINESS: FALSE
				/*} else {

					echo "LATE/TARDINESS: FALSE \n";
					//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
					$employeeNightDiffClocking->work_hours_3 = getWorkHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

					//TODO: Compute total hours with overtime - getTotalHours
					$employeeNightDiffClocking->total_hours_3 = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				}*/

				//GET NIGHTDIFF
				$timeInArr = array();
  				$timeOutArr = array();

  				$timeInArr = explode(' ', $employeeNightDiffClocking->time_in_3);
  				$timeOutArr = explode(' ', $clockingDateTime);

				$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
  				$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

			    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeNightDiffClocking->time_in_3)));			  
			    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

			    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
			    {
			        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

			            //$employeeNightDiffClocking->night_differential_3 = 8.00;
			            $employeeNightDiffClocking->night_differential_3 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            

			        } else {

			           //$employeeNightDiffClocking->night_differential_3 = $workHours;
			           $employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            

			        }

			    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
			        
			        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
			            
						//$employeeNightDiffClocking->night_differential_3 = 8.00;
						$employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					

			        } else {

						//$employeeNightDiffClocking->night_differential_3 = $workHours;
						$employeeNightDiffClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					

			        }
			    } else {

			        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
			            
						//$employeeNightDiffClocking->night_differential_3 = 8.00;
						$employeeNightDiffClocking->night_differential_3 = ($nightDiff['from'] - $nightDiff['to']) / 3600;

			        }
			        
			    }				

				//UNDERTIME: TRUE
				/*if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

					echo "UNDERTIME: TRUE \n";
					echo $employeeNightDiffClocking->undertime_3 = getUnderTimeHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
					$employeeSummaryNightDiffClocking->undertime = getUnderTimeHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																								

				}*/

				//OVERTIME: TRUE
				$isOvertime = false;

				//if ( date('H:i', strtotime($employeeNightDiffClocking->time_in_3)) <= date('H:i', strtotime($scheduled['start_time'])) &&
				//	 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

				    echo "OVERTIME: TRUE \n";

				    $isOvertime = true;					
					$employeeNightDiffClocking->total_overtime_3 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				//}				

				//HOLIDAY: TRUE
				if( hasHoliday($currentDate) ) {

					 echo "HOLIDAY: TRUE \n";

					if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

						echo "Regular holiday \n";

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_night_diff = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);															
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/						

						$employeeSummaryNightDiffClocking->rest_day_legal_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						

					} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
						

						echo "Special non-working day \n";

						/*if(!$isOvertime) { //ISOVERTIME: FALSE

							$employeeSummaryNightDiffClocking->rest_day_special_holiday_night_diff = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																		
							
						} else { //ISOVERTIME: TRUE

							$employeeSummaryNightDiffClocking->rest_day_special_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						}*/	

						$employeeSummaryNightDiffClocking->rest_day_special_holiday_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						

					}												

				//HOLIDAY: FALSE	
				} else { //Regular Day

					echo "HOLIDAY: FALSE \n";

					echo "Regular Rest Day \n";		

					/*if (!$isOvertime) { //ISOVERTIME: FALSE

						$employeeSummaryNightDiffClocking->rest_day_night_differential = getTotalHours($employeeNightDiffClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																																	
						
					} else { //ISOVERTIME: TRUE

						$employeeSummaryNightDiffClocking->rest_day_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/

					$employeeSummaryNightDiffClocking->rest_day_overtime_night_diff = getOvertimeHours($clockingDateTime, $scheduled['end_time']);									

				}

				if ( $employeeNightDiffClocking->save() ) {
							
					$employeeSummaryNightDiffClocking->save();
					return Redirect::to('/redraw/timesheet');			

				}			

			}

		//SCHEDULED : FALSE
		//HAS NO SCHEDULE - EMERGENCY OR SECOND SHIFT
		} else {

			echo "SCHEDULED : FALSE \n";	

		}


	//FORGOT TO CLOCK OUT	
	} elseif ( $timeInDateTime > 12 ) { //Forgot to clock out don't have computation for work hours, tota hours, total overtime, undertime.

		echo 'forgot_to_clock_out';

		$employeeClocking->time_out_3				= $clockingDateTime;
		$employeeClocking->clocking_status			= 'forgot_to_clock_out';

		$employeeNightDiffClocking->clocking_status = 'clock_out_3';
		$employeeNightDiffClocking->forgot_to_clock_out = 1;
		$employeeNightDiffClocking->save();		

		if ( $employeeClocking->save() ) {

			$employeeNightDiffClocking->save();	
			
			return Redirect::to('/redraw/timesheet');					

		}		

	}

}

function getClockingOutDateTimeYesterdayClockOut($clockingStatus = '', $clockingDateTime = '') {		

	$currentDate = date('Y-m-d');		
	$yesterDayDate = date("Y-m-d", strtotime('yesterday'));

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));
	$getScheduleYesterday = $schedule->getSchedule($employeeId, $yesterDayDate);

	$workShift = new Workshift;
	$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);				

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);		

	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));		

	$getYesterDayDate = $timesheet->getTimesheetYesterday($employeeId, $yesterDayDate);
	$employeeNightDiffClocking = $timesheet->getEmployeeNightDiffClocking($employeeId, $yesterDayDate);

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));		
	$employeeSummaryNightDiffClocking = $summary->getEmployeeSummaryNightDiffClocking($employeeId, $yesterDayDate);		

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);				


	if( !empty($getSchedule) && !empty($getScheduleYesterday) ) {

	$schedule['start_time'] = $getSchedule[0]->start_time;
	$schedule['end_time'] = $getSchedule[0]->end_time;
	$schedule['rest_day'] = $getSchedule[0]->rest_day;	

	$scheduleYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
	$scheduleYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
	$scheduleYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;			

	} elseif( !empty($getSchedule) && empty($getScheduleYesterday) ) {

	$schedule['start_time'] = $getSchedule[0]->start_time;
	$schedule['end_time'] = $getSchedule[0]->end_time;
	$schedule['rest_day'] = $getSchedule[0]->rest_day;	

	} elseif( empty($getSchedule) && !empty($getScheduleYesterday) ) {

	$scheduleYesterday['start_time'] = $getScheduleYesterday[0]->start_time;
	$scheduleYesterday['end_time'] = $getScheduleYesterday[0]->end_time;
	$scheduleYesterday['rest_day'] = $getScheduleYesterday[0]->rest_day;						

	} elseif( !empty($getWorkShiftByEmployeeId) ) {

	//$workShift['start_time'] = $getWorkShiftByEmployeeId[0]->start_time;
	//$workShift['end_time'] = $getWorkShiftByEmployeeId[0]->end_time;		
	//$workShift['rest_day'] = $getWorkShiftByEmployeeId[0]->rest_day;
	//$workShift['rest_day'] = explode(', ', $getWorkShiftByEmployeeId[0]->rest_day);

	$workShift['start_time'] = date( 'Y-m-d', strtotime($employeeNightDiffClocking->time_in_1) ).' '.$getWorkShiftByEmployeeId[0]->start_time;						
	$workShift['end_time'] =  date( 'Y-m-d', strtotime('-1 day') ).' '.$getWorkShiftByEmployeeId[0]->end_time;
	$workShift['rest_day'] = explode(', ', $getWorkShiftByEmployeeId[0]->rest_day);

	}		

	if ( $clockingStatus === 'clock_in_3' ) {						

		echo '// Yesterday clock out';

		$employeeClocking->time_out_3				=   $clockingDateTime;
		$employeeClocking->clocking_status			= 'clock_out_3';

		//Check this out
		/*if( $employeeNightDiffClocking->clocking_status === 'yesterday_clock_out' ) {

			$employeeNightDiffClocking->clocking_status = 'close';
			$employeeNightDiffClocking->save();

		}*/	

		if ( $employeeClocking->save() ) {
			
			return Redirect::to('/redraw/timesheet');

		}					

	}

}


function clockingStatusOpenForgotToClockOut($clockingDateTime = '', $tardiness = false) {}


function clockingStatusOpenIn($clockingDateTime = '', $tardiness = false) {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');
	
	$dayOfTheWeek = date('l');

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));
	
	$workShift = new Workshift;
	//$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek);				

	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));		

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));		

	//$setting = new Setting;
	//$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);				

	if ( !empty($getSchedule) ) {

		$schedule['start_time'] = $getSchedule[0]->start_time;
		$schedule['end_time'] = $getSchedule[0]->end_time;			
		
	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//$getWorkShiftByEmployeeId->name_of_day;

		$workShift['start_time'] = $getWorkShiftByDayOfTheWeek[0]->start_time;
		$workShift['end_time'] = $getWorkShiftByDayOfTheWeek[0]->end_time;		

	} else {

		return 'clockingStatusOpenIn Check default schedule and Scheduled table for this employee';		

	}	

	$employeeClocking->time_in_1 = $clockingDateTime;
	$employeeClocking->clocking_status = 'clock_in_1';

	if($tardiness) {

		$clockingIn = $clockingDateTime; //$getClockingDateTime;

		if( !empty($getSchedule) ) { 

			$startTime = $schedule['start_time'];

		} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
			
			$startTime = $workShift['start_time'];

		}

		$tardinessTime = getTardinessTime($clockingIn, $startTime);

		if( !empty($tardinessTime) ) {

			$employeeClocking->tardiness_1 = $tardinessTime;							
			$employeeSummary->lates = $tardinessTime;				
		}
	}

	if ( $employeeClocking->save() ) {

		if( !empty($tardinessTime) ) {

			$employeeSummary->save();

		}
		
		return Redirect::to('/redraw/timesheet');			

	}	

}

function clockingStatusClockOut1In($clockingDateTime = '', $tardiness = false) {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');	

	$dayOfTheWeek = date('l');	

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	$workShift = new Workshift;
	//$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 2);

	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));		

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));		

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);				

    if ( !empty($getSchedule) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;			
		$scheduled['shift'] = $getSchedule[0]->shift;
		
	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//$getWorkShiftByEmployeeId->name_of_day;

		$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek[0]->start_time;
		$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek[0]->end_time;		
		$scheduled['shift'] = $getWorkShiftByDayOfTheWeek[0]->shift;

	} else {

		$scheduled['shift'] = 0;
		//return 'clockingStatusClockOut1In Check default schedule and Scheduled table for this employee';

	}
    
    //2ND SHIFT: TRUE
	if ( $scheduled['shift'] === 2 ) {

		echo "2ND SHIFT: TRUE \n";

		$employeeClocking->time_in_3 = $clockingDateTime;
		$employeeClocking->clocking_status = 'clock_in_3';	

		if($tardiness) {

			$clockingIn = $clockingDateTime; //$getClockingDateTime;
			
			if( !empty($getSchedule) ) { 

				$startTime = $scheduled['start_time'];

			} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
				
				$startTime = $scheduled['start_time'];

			}

			$tardinessTime = getTardinessTime($clockingIn, $startTime);

			if( !empty($tardinessTime) ) {

				$employeeClocking->tardiness_3 = $tardinessTime;							
				$employeeSummary->lates = $tardinessTime;				
			}
		}


		//return 'clockingStatusClockOut1In';

		if ( $employeeClocking->save() ) {

			if( !empty($tardinessTime) ) {

				$employeeSummary->save();

			}
			
			return Redirect::to('/redraw/timesheet');			

		}	

	//2ND SHIFT: FALSE
	} /*elseif ( $scheduled['shift'] !== 2 ) {

		echo "2ND SHIFT: FALSE \n";
		return 'No schedule';

	}*/

	if ( $scheduled['shift'] === 0 ) {

		echo "2ND SHIFT: FALSE \n";

		$employeeClocking->time_in_3 = $clockingDateTime;
		$employeeClocking->clocking_status = 'clock_in_3';	

		/*if($tardiness) {

			$clockingIn = $clockingDateTime; //$getClockingDateTime;
			
			if( !empty($getSchedule) ) { 

				$startTime = $scheduled['start_time'];

			} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
				
				$startTime = $scheduled['start_time'];

			}

			$tardinessTime = getTardinessTime($clockingIn, $startTime);

			if( !empty($tardinessTime) ) {

				$employeeClocking->tardiness_3 = $tardinessTime;							
				$employeeSummary->lates = $tardinessTime;				
			}
		}*/


		//return 'clockingStatusClockOut1In';

		if ( $employeeClocking->save() ) {

			/*if( !empty($tardinessTime) ) {

				$employeeSummary->save();

			}*/
			
			return Redirect::to('/redraw/timesheet');			

		}

	}

}

function clockingStatusClockOut2In($clockingDateTime = '', $tardiness = false) {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');		
	$dayOfTheWeek = date('l');

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	$workShift = new Workshift;
	//$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);				
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 1);	

	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));		

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));		

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);	

	if ( !empty($getSchedule) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;			
		
	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//$getWorkShiftByEmployeeId->name_of_day;

		$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek[0]->start_time;
		$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek[0]->end_time;		

	} else {

		return 'clockingStatusClockOut2In Check default schedule and Scheduled table for this employee';

	}		

	$employeeClocking->time_in_3       =   $clockingDateTime;
	$employeeClocking->clocking_status =   'clock_in_3';	

	if($tardiness) {

		$clockingIn = $clockingDateTime; //$getClockingDateTime;

		if( !empty($getSchedule) ) { 

			$startTime = $schedule['start_time'];

		} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
			
			$startTime = $scheduled['start_time'];

		}		

		$tardinessTime = getTardinessTime($clockingIn, $startTime);

		if( !empty($tardinessTime) ) {

			$employeeClocking->tardiness_3 = $tardinessTime;							
			$employeeSummary->lates = $tardinessTime;				
		}
	}

	if ( $employeeClocking->save() ) {

		if( !empty($tardinessTime) ) {

			$employeeSummary->save();

		}
		
		return Redirect::to('/redraw/timesheet');			

	}					

}	


function clockingStatusForgotToClockOut($clockingDateTime = '', $tardiness = false) {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');		
	$dayOfTheWeek = date('l');

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));
	
	$workShift = new Workshift;
	//$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);				
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 1);

	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));		

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));		

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);				
	
	if ( !empty($getSchedule) ) {

		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;			
		
	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//$getWorkShiftByEmployeeId->name_of_day;

		$scheduled['start_time'] = $getWorkShiftByDayOfTheWeek[0]->start_time;
		$scheduled['end_time'] = $getWorkShiftByDayOfTheWeek[0]->end_time;		

	} else {

		return 'clockingStatusForgotToClockOut Check default schedule and Scheduled table for this employee';

	}

	$employeeClocking->time_in_2       =   $clockingDateTime;
	$employeeClocking->clocking_status =   'clock_in_2';	

	if($tardiness) {

		$clockingIn = $clockingDateTime; //$getClockingDateTime;
		
		if( !empty($getSchedule) ) { 

			$startTime = $schedule['start_time'];

		} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
			
			$startTime = $scheduled['start_time'];

		}

		$tardinessTime = getTardinessTime($clockingIn, $startTime);

		if( !empty($tardinessTime) ) {

			$employeeClocking->tardiness_2 = $tardinessTime;							
			$employeeSummary->lates = $tardinessTime;				
		}
	}

	if ( $employeeClocking->save() ) {

		if( !empty($tardinessTime) ) {

			$employeeSummary->save();

		}
		
		return Redirect::to('/redraw/timesheet');			

	}

}	

/**
*
* TIMESHEET: Clocking Out Functions	
*
*/

function getClockingOutDateTime($clockingStatus = '', $clockingDateTime = '') {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');		

	$dayOfTheWeek = date('l');	
	$currentDate = date('Y-m-d');

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	$workShift = new Workshift;
	//$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek);

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);
	
	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);				

	if( !empty($getSchedule) ) {

		//echo 'getSchedule';
		
		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;			
		
	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//echo 'getWorkShiftByDayOfTheWeek';

     	// From 01:00:00 change to 2015-04-30 09:00:00
		$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeClocking->time_in_1) ).' '.$getWorkShiftByDayOfTheWeek[0]->start_time;

		// From 01:00:00 change to 2015-04-30 01:00:00
		$scheduled['end_time'] =  date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek[0]->end_time;

		$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek[0]->rest_day;
	}


	// Regular clocking out
	if ( $clockingStatus === 'clock_in_1' ) { // Today clocking status is "open"

		echo "Regular clocking out \n";
		return clockingStatusClockIn1Out($clockingDateTime);
	
	// Regular clocking but has second shift	
	} elseif ( $clockingStatus === 'clock_in_3' ) { // Today clocking status is "open"
			
		//echo 'Regular clocking but has second shift';
		return clockingStatusClockIn3Out($clockingDateTime);	

	// Forget to Clock out yesterday
	} elseif ( $clockingStatus === 'clock_in_2' ) { // Today clocking status is "open"

		echo 'Forget to Clock out yesterday';
		return clockingStatusClockIn2Out($clockingDateTime);	

	}

}


function clockingStatusClockIn1Out($clockingDateTime) {

	echo clockingStatusClockIn1Out;

	//Settings
	$breakTime = date('H:i', strtotime('01:00:00'));

	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');		

	$dayOfTheWeek = date('l');	
	$currentDate = date('Y-m-d');

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	$workShift = new Workshift;
	//$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 1);

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);
	
	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);				

	if( !empty($getSchedule) ) {

		//echo 'getSchedule';
		
		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;			
		
	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//echo 'getWorkShiftByDayOfTheWeek';

     	// From 01:00:00 change to 2015-04-30 09:00:00
		$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeClocking->time_in_1) ).' '.$getWorkShiftByDayOfTheWeek[0]->start_time;

		// From 01:00:00 change to 2015-04-30 01:00:00
		$scheduled['end_time'] =  date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek[0]->end_time;

		$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek[0]->rest_day;
	}


	$employeeClocking->time_out_1 = $clockingDateTime;
	$employeeClocking->clocking_status = 'clock_out_1';	

	// From 2015-04-30 08:36:16 change to 08:36:16
	$clockingIn = date('H:i:s', strtotime($employeeClocking->time_in_1)); 
	// From 2015-04-30 08:36:16 change to 08:36:16	
	$clockingOut = date('H:i:s', strtotime($clockingDateTime)); 

	//SCHEDULED : TRUE
	if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
	     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

		echo "SCHEDULED : TRUE \n";

		//REST DAY: FALSE
		if ( $scheduled['rest_day'] !== 1 ) {

			echo "REST DAY: FALSE \n";

			//LATE/TARDINESS: TRUE
			if ( !empty($employeeClocking->tardiness_1) ) {

				echo "LATE/TARDINESS: TRUE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				//$employeeClocking->work_hours_1 = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_1 = 8.00;					

				} else {

					$employeeClocking->work_hours_1 = $workHours;					

				}				

				//TODO: Compute total hours with out overtime - getTotalHours
				$employeeClocking->total_hours_1 = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

			//LATE/TARDINESS: FALSE
			} else {

				echo "LATE/TARDINESS: FALSE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				//$employeeClocking->work_hours_1 = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_1 = 8.00;

				} else {

					$employeeClocking->work_hours_1 = $workHours;					

				}				

				//TODO: Compute total hours with overtime - getTotalHours
				$employeeClocking->total_hours_1 = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

			}

			//GET NIGHTDIFF
			$timeInArr = array();
			$timeOutArr = array();

			$timeInArr = explode(' ', $employeeClocking->time_in_1);
			$timeOutArr = explode(' ', $clockingDateTime);

			$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
			$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

		    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeClocking->time_in_1)));			  
		    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

		    $hasNightDiff = false;

		    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
		    {
		        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00
		        	echo 'ND: 1';
		        	$hasNightDiff = true;

		            //$employeeClocking->night_differential_1 = 8.00;
		            $employeeClocking->night_differential_1 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

		        } else {
					echo 'ND: 2';
		        	$hasNightDiff = true;		        	

		           //$employeeClocking->night_differential_1 = $workHours;
		           //$employeeClocking->night_differential_1 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
				   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
					
					if( (($nightDiff['to'] - $timesheet['timeIn']) / 3600) > 0  )
					{
						$employeeClocking->night_differential_1 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
			            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		        	
		            }
		            else //negative
		            {
		            	
		            	$employeeClocking->night_differential_1 = '';
		            	$nightDifferential = '';

		            }

		        }

		    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
		        
		        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
					echo 'ND: 3';
		        	$hasNightDiff = true;		        	
		            
					//$employeeClocking->night_differential_1 = 8.00;
					$employeeClocking->night_differential_1 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;										

		        } else { //negative
					echo 'ND: 4';
		        	$hasNightDiff = true;		

		        	if( (($timesheet['timeOut'] - $timesheet['timeIn']) / 3600) > 0 )
		        	{
						//$employeeClocking->night_differential_1 = $workHours;
						$employeeClocking->night_differential_1 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
						$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
					}						
					else 
		            {
		            	
		            	$employeeClocking->night_differential_1 = '';
		            	$nightDifferential = '';

		            }

		        }
		    } else {

		        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
					echo 'ND: 5';		            
		           	$hasNightDiff = true;

					//$employeeClocking->night_differential_1 = 8.00;
					$employeeClocking->night_differential_1 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
					$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;

		        }
		        
		    }		

			//UNDERTIME: TRUE
			if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

				echo "UNDERTIME: TRUE \n";
				echo $employeeClocking->undertime_1 = getUnderTimeHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
				$employeeSummary->undertime = getUnderTimeHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			

			}

			//OVERTIME: TRUE
			$isOvertime = false;

			if ( date('H:i', strtotime($employeeClocking->time_in_1)) <= date('H:i', strtotime($scheduled['start_time'])) &&
				 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

			    echo "OVERTIME: TRUE \n";

			    $isOvertime = true;					
				$employeeClocking->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

			}				

			//HOLIDAY: TRUE
			if( hasHoliday($currentDate) ) {

				 echo "HOLIDAY: TRUE \n";

				if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

					echo "Regular holiday \n";

					$employeeSummary->legal_holiday = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);					

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummary->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

					}

					/*if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->legal_holiday = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);															
						$employeeSummary->legal_holiday = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/	

					if($isOvertime) { //ISOVERTIME: TRUE

						$employeeSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						

					}

					if($hasNightDiff) { //ISOVERTIME: TRUE

						$employeeSummary->legal_holiday_night_diff = $nightDifferential;

					}													


				} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day				

					echo "Special non-working day \n";

					$employeeSummary->special_holiday = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummary->special_holiday_overtime_night_diff = $overtimeNightdiff;						

					}					

					/*if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->special_holiday = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																		
						$employeeSummary->special_holiday = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/

					if($isOvertime) { //ISOVERTIME: TRUE

						$employeeSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						
					}

					if($hasNightDiff) { //HASNIGHTDIFF: TRUE

						$employeeSummary->special_holiday_night_diff = $nightDifferential;

					}						

				}												

			//HOLIDAY: FALSE	
			} else { //Regular Day

				echo "HOLIDAY: FALSE \n";

				echo "Regular Day \n";	

				$employeeSummary->regular = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);				

				if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

					$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					$overtimeNightdiff = $overtimeHours + $nightDifferential;

					$employeeSummary->regular_overtime_night_diff = $overtimeNightdiff;						

				}					

				/*if (!$isOvertime) { //ISOVERTIME: FALSE

					//$employeeSummary->regular = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																																	
					$employeeSummary->regular = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
					
				} else { //ISOVERTIME: TRUE

					$employeeSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}*/

				if ($isOvertime) { //ISOVERTIME: TRUE

					$employeeSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
					
				}				

				if($hasNightDiff) { //HASNIGHTDIFF: TRUE

					$employeeSummary->regular_night_differential = $nightDifferential;

				}									

			}

			if ( $employeeClocking->save() ) {
						
				$employeeSummary->save();
				return Redirect::to('/redraw/timesheet');			

			}							

		//REST DAY: TRUE
		} elseif ( $scheduled['rest_day'] === 1 ) {

			echo "REST DAY: TRUE \n";

			//TODO RULES: First 8 Hours get the hours_per_day in employee setting

			//LATE/TARDINESS: TRUE
			if ( !empty($employeeClocking->tardiness_1) ) {

				echo "LATE/TARDINESS: TRUE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				//$employeeClocking->work_hours_1 = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_1 = 8.00;					

				} else {

					$employeeClocking->work_hours_1 = $workHours;					

				}				

				//TODO: Compute total hours with out overtime - getTotalHours
				$employeeClocking->total_hours_1 = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

			//LATE/TARDINESS: FALSE
			} else {

				echo "LATE/TARDINESS: FALSE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				//$employeeClocking->work_hours_1 = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_1 = 8.00;

				} else {

					$employeeClocking->work_hours_1 = $workHours;					

				}				

				//TODO: Compute total hours with overtime - getTotalHours
				$employeeClocking->total_hours_1 = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);												

			}

			//GET NIGHTDIFF
			$timeInArr = array();
			$timeOutArr = array();

			$timeInArr = explode(' ', $employeeClocking->time_in_1);
			$timeOutArr = explode(' ', $clockingDateTime);

			$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
			$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

		    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeClocking->time_in_1)));			  
		    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

		    $hasNightDiff = false;

		    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
		    {
		        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

					$hasNightDiff = true;
		     
		            //$employeeClocking->night_differential_1 = 8.00;
		            $employeeClocking->night_differential_1 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

		        } else {

				   $hasNightDiff = true;		        	

		           //$employeeClocking->night_differential_1 = $workHours;
		           //$employeeClocking->night_differential_1 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
				   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;
		   
					$employeeClocking->night_differential_1 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;	   

		        }

		    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
		        
		        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {

					$hasNightDiff = true;		        	
		            
					//$employeeClocking->night_differential_1 = 8.00;
					$employeeClocking->night_differential_1 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;

		        } else {

					$hasNightDiff = true;		        	

					//$employeeClocking->night_differential_1 = $workHours;
					$employeeClocking->night_differential_1 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;

		        }
		    } else {

		        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {

					$hasNightDiff = true;		        	
		            
					//$employeeClocking->night_differential_1 = 8.00;
					$employeeClocking->night_differential_1 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
					$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;					

		        }
		        
		    }	

			//UNDERTIME: TRUE
			if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

				echo "UNDERTIME: TRUE \n";
				echo $employeeClocking->undertime_1 = getUnderTimeHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
				$employeeSummary->undertime = getUnderTimeHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																							

			}

			//OVERTIME: TRUE
			$isOvertime = false;

			if ( date('H:i', strtotime($employeeClocking->time_in_1)) <= date('H:i', strtotime($scheduled['start_time'])) &&
				 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

			    echo "OVERTIME: TRUE \n";

			    $isOvertime = true;					
				$employeeClocking->total_overtime_1 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

			}				

			//HOLIDAY: TRUE
			if( hasHoliday($currentDate) ) {

				 echo "HOLIDAY: TRUE \n";

				if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

					echo "Regular holiday \n";

					$employeeSummary->rest_day_legal_holiday = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummary->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;						

					}					

					/*if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->rest_day_legal_holiday = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);															
						$employeeSummary->rest_day_legal_holiday = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/

					if($isOvertime) { //ISOVERTIME: TRUE

						$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}				

					if($hasNightDiff) { //HASNIGHTDIFF: TRUE

						$employeeSummary->rest_day_legal_holiday_night_diff = $nightDifferential;

					}											


				} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
					
					echo "Special non-working day \n";

					$employeeSummary->rest_day_special_holiday = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);					

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummary->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;						

					}					

					if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->rest_day_legal_holiday = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);															
						$employeeSummary->rest_day_legal_holiday = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}					

					if($isOvertime) { //ISOVERTIME: FALSE

						$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						
					}	

					if($hasNightDiff) { //HASNIGHTDIFF: TRUE

						$employeeSummary->rest_day_special_holiday_night_diff = $nightDifferential;

					}					

				}												

			//HOLIDAY: FALSE	
			} else { //Regular Day

				echo "HOLIDAY: FALSE \n";

				echo "Regular Rest Day \n";		

				$employeeSummary->rest_day = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);				

				if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

					$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					$overtimeNightdiff = $overtimeHours + $nightDifferential;

					$employeeSummary->rest_day_overtime_night_diff = $overtimeNightdiff;						

				}					

				/*if (!$isOvertime) { //ISOVERTIME: FALSE

					//$employeeSummary->rest_day = getTotalHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);																																	
					$employeeSummary->rest_day = getWorkHours($employeeClocking->time_in_1, $clockingDateTime, $scheduled['end_time']);
					
				} else { //ISOVERTIME: TRUE

					$employeeSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}*/	

				if ($isOvertime) { //ISOVERTIME: TRUE

					$employeeSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}				

				if($hasNightDiff) { //HASNIGHTDIFF: TRUE

					$employeeSummary->rest_day_night_differential = $nightDifferential;

				}	


			}

			if ( $employeeClocking->save() ) {
						
				$employeeSummary->save();
				return Redirect::to('/redraw/timesheet');			

			}			

		}

	//SCHEDULED : FALSE
	//HAS NO SCHEDULE - EMERGENCY OR SECOND SHIFT		
	} else {

		echo "SCHEDULED : FALSE \n";	

	}	

}


function clockingStatusClockIn2Out($clockingDateTime) {

	//Settings
	$breakTime = date('H:i', strtotime('01:00:00'));
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');		

	$dayOfTheWeek = date('l');	
	$currentDate = date('Y-m-d');

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	$workShift = new Workshift;
	//$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 1);

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);
	
	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);				

	if( !empty($getSchedule) ) {

		//echo 'getSchedule';
		
		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;			
		
	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//echo 'getWorkShiftByDayOfTheWeek';

     	// From 01:00:00 change to 2015-04-30 09:00:00
		$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeClocking->time_in_2) ).' '.$getWorkShiftByDayOfTheWeek[0]->start_time;

		// From 01:00:00 change to 2015-04-30 01:00:00
		$scheduled['end_time'] =  date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek[0]->end_time;

		$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek[0]->rest_day;
	}


	$employeeClocking->time_out_2 = $clockingDateTime;
	$employeeClocking->clocking_status = 'clock_out_2';	

	// From 2015-04-30 08:36:16 change to 08:36:16
	$clockingIn = date('H:i:s', strtotime($employeeClocking->time_in_2)); 
	// From 2015-04-30 08:36:16 change to 08:36:16	
	$clockingOut = date('H:i:s', strtotime($clockingDateTime)); 

	//SCHEDULED : TRUE
	if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
	     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

		echo "SCHEDULED : TRUE \n";

		//REST DAY: FALSE
		if ( $scheduled['rest_day'] !== 1 ) {

			echo "REST DAY: FALSE \n";

			//LATE/TARDINESS: TRUE
			if ( !empty($employeeClocking->tardiness_2) ) {

				echo "LATE/TARDINESS: TRUE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours				
				//$employeeClocking->work_hours_2 = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_2 = 8.00;

				} else {

					$employeeClocking->work_hours_2 = $workHours;					

				}								

				//TODO: Compute total hours with out overtime - getTotalHours
				$employeeClocking->total_hours_2 = getTotalHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

			//LATE/TARDINESS: FALSE
			} else {

				echo "LATE/TARDINESS: FALSE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				//$employeeClocking->work_hours_2 = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_2 = 8.00;

				} else {

					$employeeClocking->work_hours_2 = $workHours;					

				}								

				//TODO: Compute total hours with overtime - getTotalHours
				$employeeClocking->total_hours_2 = getTotalHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

			}

			//GET NIGHTDIFF
			$timeInArr = array();
			$timeOutArr = array();

			$timeInArr = explode(' ', $employeeClocking->time_in_2);
			$timeOutArr = explode(' ', $clockingDateTime);

			$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
			$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

		    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeClocking->time_in_2)));			  
		    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

		    $hasNightDiff = false;

		    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
		    {
		        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

		        	$hasNightDiff = true;

		            //$employeeClocking->night_differential_2 = 8.00;
		            $employeeClocking->night_differential_2 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

		        } else {

		        	$hasNightDiff = true;		        	

		           //$employeeClocking->night_differential_2 = $workHours;
		           //$employeeClocking->night_differential_2 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
				   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;	

		            $employeeClocking->night_differential_2 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;				   	            

		        }

		    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
		        
		        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {

		        	$hasNightDiff = true;		        	
		            
					//$employeeClocking->night_differential_2 = 8.00;
					$employeeClocking->night_differential_2 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;										

		        } else {

		        	$hasNightDiff = true;		        	

					//$employeeClocking->night_differential_2 = $workHours;
					$employeeClocking->night_differential_2 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					

		        }
		    } else {

		        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
		            
		           	$hasNightDiff = true;

					//$employeeClocking->night_differential_2 = 8.00;
					$employeeClocking->night_differential_2 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
					$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;

		        }
		        
		    }

			//UNDERTIME: TRUE
			if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

				echo "UNDERTIME: TRUE \n";
				echo $employeeClocking->undertime_2 = getUnderTimeHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
				$employeeSummary->undertime = getUnderTimeHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			

			}

			//OVERTIME: TRUE
			$isOvertime = false;

			if ( date('H:i', strtotime($employeeClocking->time_in_2)) <= date('H:i', strtotime($scheduled['start_time'])) &&
				 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

			    echo "OVERTIME: TRUE \n";

			    $isOvertime = true;					
				$employeeClocking->total_overtime_2 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

			}				

			//HOLIDAY: TRUE
			if( hasHoliday($currentDate) ) {

				 echo "HOLIDAY: TRUE \n";

				if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

					echo "Regular holiday \n";

					$employeeSummary->legal_holiday = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);					

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummary->legal_holiday_overtime_night_diff = $overtimeNightdiff;						

					}

					/*if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->legal_holiday = getTotalHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);															
						$employeeSummary->legal_holiday = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/	

					if($isOvertime) { //ISOVERTIME: TRUE

						$employeeSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						

					}

					if($hasNightDiff) { //ISOVERTIME: TRUE

						$employeeSummary->legal_holiday_night_diff = $nightDifferential;

					}													


				} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day				

					echo "Special non-working day \n";

					$employeeSummary->special_holiday = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummary->special_holiday_overtime_night_diff = $overtimeNightdiff;						

					}					

					/*if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->special_holiday = getTotalHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);																		
						$employeeSummary->special_holiday = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/

					if($isOvertime) { //ISOVERTIME: TRUE

						$employeeSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
						
					}

					if($hasNightDiff) { //HASNIGHTDIFF: TRUE

						$employeeSummary->special_holiday_night_diff = $nightDifferential;

					}						

				}												

			//HOLIDAY: FALSE	
			} else { //Regular Day

				echo "HOLIDAY: FALSE \n";

				echo "Regular Day \n";	

				$employeeSummary->regular = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);				

				if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

					$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					$overtimeNightdiff = $overtimeHours + $nightDifferential;

					$employeeSummary->regular_overtime_night_diff = $overtimeNightdiff;						

				}					

				/*if (!$isOvertime) { //ISOVERTIME: FALSE

					//$employeeSummary->regular = getTotalHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);																																	
					$employeeSummary->regular = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
					
				} else { //ISOVERTIME: TRUE

					$employeeSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}*/

				if ($isOvertime) { //ISOVERTIME: TRUE

					$employeeSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
					
				}				

				if($hasNightDiff) { //HASNIGHTDIFF: TRUE

					$employeeSummary->regular_night_differential = $nightDifferential;

				}									

			}

			if ( $employeeClocking->save() ) {
						
				$employeeSummary->save();
				return Redirect::to('/redraw/timesheet');			

			}							

		//REST DAY: TRUE
		} elseif ( $scheduled['rest_day'] === 1 ) {

			echo "REST DAY: TRUE \n";

			//TODO RULES: First 8 Hours get the hours_per_day in employee setting

			//LATE/TARDINESS: TRUE
			if ( !empty($employeeClocking->tardiness_2) ) {

				echo "LATE/TARDINESS: TRUE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				//$employeeClocking->work_hours_2 = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_2 = 8.00;

				} else {

					$employeeClocking->work_hours_2 = $workHours;					

				}								

				//TODO: Compute total hours with out overtime - getTotalHours
				$employeeClocking->total_hours_2 = getTotalHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

			//LATE/TARDINESS: FALSE
			} else {

				echo "LATE/TARDINESS: FALSE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				//$employeeClocking->work_hours_2 = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_2 = 8.00;

				} else {

					$employeeClocking->work_hours_2 = $workHours;

				}								

				//TODO: Compute total hours with overtime - getTotalHours
				$employeeClocking->total_hours_2 = getTotalHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												

			}

			//GET NIGHTDIFF
			$timeInArr = array();
			$timeOutArr = array();

			$timeInArr = explode(' ', $employeeClocking->time_in_2);
			$timeOutArr = explode(' ', $clockingDateTime);

			$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
			$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

		    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeClocking->time_in_2)));			  
		    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

		    $hasNightDiff = false;

		    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
		    {
		        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

		        	$hasNightDiff = true;

		            //$employeeClocking->night_differential_2 = 8.00;
		            $employeeClocking->night_differential_2 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

		        } else {

		        	$hasNightDiff = true;		        	

		           //$employeeClocking->night_differential_2 = $workHours;
		           //$employeeClocking->night_differential_2 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
				   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            

		            $employeeClocking->night_differential_2 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		        	

		        }

		    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
		        
		        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {

		        	$hasNightDiff = true;		        	
		            
					//$employeeClocking->night_differential_2 = 8.00;
					$employeeClocking->night_differential_2 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;										

		        } else {

		        	$hasNightDiff = true;		        	

					//$employeeClocking->night_differential_2 = $workHours;
					$employeeClocking->night_differential_2 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					

		        }
		    } else {

		        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
		            
		           	$hasNightDiff = true;

					//$employeeClocking->night_differential_2 = 8.00;
					$employeeClocking->night_differential_2 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
					$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;

		        }
		        
		    }			

			//UNDERTIME: TRUE
			if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

				echo "UNDERTIME: TRUE \n";
				echo $employeeClocking->undertime_2 = getUnderTimeHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
				$employeeSummary->undertime = getUnderTimeHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																							

			}

			//OVERTIME: TRUE
			$isOvertime = false;

			if ( date('H:i', strtotime($employeeClocking->time_in_2)) <= date('H:i', strtotime($scheduled['start_time'])) &&
				 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

			    echo "OVERTIME: TRUE \n";

			    $isOvertime = true;					
				$employeeClocking->total_overtime_2 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

			}				

			//HOLIDAY: TRUE
			if( hasHoliday($currentDate) ) {

				 echo "HOLIDAY: TRUE \n";

				if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

					echo "Regular holiday \n";

					$employeeSummary->rest_day_legal_holiday = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummary->rest_day_legal_holiday_overtime_night_diff = $overtimeNightdiff;						

					}					

					/*if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->rest_day_legal_holiday = getTotalHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);															
						$employeeSummary->rest_day_legal_holiday = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/

					if($isOvertime) { //ISOVERTIME: TRUE

						$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}				

					if($hasNightDiff) { //HASNIGHTDIFF: TRUE

						$employeeSummary->rest_day_legal_holiday_night_diff = $nightDifferential;

					}											


				} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
					
					echo "Special non-working day \n";

					$employeeSummary->rest_day_special_holiday = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);					

					if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

						$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

						$overtimeNightdiff = $overtimeHours + $nightDifferential;

						$employeeSummary->rest_day_special_holiday_overtime_night_diff = $overtimeNightdiff;						

					}					

					if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->rest_day_legal_holiday = getTotalHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);															
						$employeeSummary->rest_day_legal_holiday = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}					

					if($isOvertime) { //ISOVERTIME: FALSE

						$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);						
					}	

					if($hasNightDiff) { //HASNIGHTDIFF: TRUE

						$employeeSummary->rest_day_special_holiday_night_diff = $nightDifferential;

					}					

				}												

			//HOLIDAY: FALSE	
			} else { //Regular Day

				echo "HOLIDAY: FALSE \n";

				echo "Regular Rest Day \n";		

				$employeeSummary->rest_day = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);				

				if($isOvertime && $hasNightDiff) { //ISOVERTIME AND HASNIGHTDIFF: TRUE

					$overtimeHours = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					$overtimeNightdiff = $overtimeHours + $nightDifferential;

					$employeeSummary->rest_day_overtime_night_diff = $overtimeNightdiff;						

				}					

				/*if (!$isOvertime) { //ISOVERTIME: FALSE

					//$employeeSummary->rest_day = getTotalHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);																																	
					$employeeSummary->rest_day = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);
					
				} else { //ISOVERTIME: TRUE

					$employeeSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}*/	

				if ($isOvertime) { //ISOVERTIME: TRUE

					$employeeSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}				

				if($hasNightDiff) { //HASNIGHTDIFF: TRUE

					$employeeSummary->rest_day_night_differential = $nightDifferential;

				}	

			}

			if ( $employeeClocking->save() ) {
						
				$employeeSummary->save();
				return Redirect::to('/redraw/timesheet');			

			}			

		}

	//SCHEDULED : FALSE
	//HAS NO SCHEDULE - EMERGENCY OR SECOND SHIFT		
	} else {

		echo "SCHEDULED : FALSE \n";	

	}	

}

function clockingStatusClockIn3Out($clockingDateTime) {

	//Settings
	$breakTime = date('H:i', strtotime('01:00:00'));
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');		

	$dayOfTheWeek = date('l');	
	$currentDate = date('Y-m-d');

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	$workShift = new Workshift;
	//$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 2);

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);
	
	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);	

	$employeeClocking->time_out_3 = $clockingDateTime;
	$employeeClocking->clocking_status = 'clock_out_3';	

	if( !empty($getSchedule) ) {

		//echo 'getSchedule';
		
		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;			
		
	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//echo 'getWorkShiftByDayOfTheWeek';

     	// From 01:00:00 change to 2015-04-30 09:00:00
		$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeClocking->time_in_3) ).' '.$getWorkShiftByDayOfTheWeek[0]->start_time;

		// From 01:00:00 change to 2015-04-30 01:00:00
		$scheduled['end_time'] =  date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek[0]->end_time;

		$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek[0]->rest_day;
	}

	// From 2015-04-30 08:36:16 change to 08:36:16
	$clockingIn = date('H:i:s', strtotime($employeeClocking->time_in_3)); 
	// From 2015-04-30 08:36:16 change to 08:36:16	
	$clockingOut = date('H:i:s', strtotime($clockingDateTime)); 


	//SCHEDULED : TRUE
	if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
	     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

		echo "SCHEDULED : TRUE \n";

			//return dd('debug.io');	

		//REST DAY: FALSE
		if ( $scheduled['rest_day'] !== 1 ) {

			echo "REST DAY: FALSE \n";

			$datetime1 = $employeeClocking->time_in_3;
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
			$employeeClocking->total_overtime_3 = $overtime;		
			$employeeClocking->total_hours_3 = $overtime;

			//LATE/TARDINESS: TRUE
			/*if ( !empty($employeeClocking->tardiness_3) ) {

				echo "LATE/TARDINESS: TRUE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				//$employeeClocking->work_hours_3 = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_3 = 8.00;

				} else {

					$employeeClocking->work_hours_3 = $workHours;					

				}												

				//TODO: Compute total hours with out overtime - getTotalHours
				$employeeClocking->total_hours_3 = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

			//LATE/TARDINESS: FALSE
			} else {

				echo "LATE/TARDINESS: FALSE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				$employeeClocking->work_hours_3 = getWorkHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				//TODO: Compute total hours with overtime - getTotalHours
				$employeeClocking->total_hours_3 = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

			}*/

			//GET NIGHTDIFF
			$timeInArr = array();
			$timeOutArr = array();

			$timeInArr = explode(' ', $employeeClocking->time_in_3);
			$timeOutArr = explode(' ', $clockingDateTime);

			$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
			$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

		    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeClocking->time_in_3)));			  
		    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

		    $hasNightDiff = false;

		    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
		    {
		        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

		        	$hasNightDiff = true;

		            //$employeeClocking->night_differential_3 = 8.00;
		            $employeeClocking->night_differential_3 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

		        } else {

		        	$hasNightDiff = true;		        	

		           //$employeeClocking->night_differential_3 = $workHours;
		           //$employeeClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
				   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
		            
		            $employeeClocking->night_differential_3 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		        	

		        }

		    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
		        
		        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {

		        	$hasNightDiff = true;		        	
		            
					//$employeeClocking->night_differential_3 = 8.00;
					$employeeClocking->night_differential_3 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;										

		        } else {

		        	$hasNightDiff = true;		        	

					//$employeeClocking->night_differential_3 = $workHours;
					$employeeClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					

		        }
		    } else {

		        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
		            
		           	$hasNightDiff = true;

					//$employeeClocking->night_differential_3 = 8.00;
					$employeeClocking->night_differential_3 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
					$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;

		        }
		        
		    }			

			//UNDERTIME: TRUE
			/*if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

				echo "UNDERTIME: TRUE \n";
				echo $employeeClocking->undertime_3 = getUnderTimeHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
				$employeeSummary->undertime = getUnderTimeHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			

			}*/

			//OVERTIME: TRUE
			//$isOvertime = false;

			//if ( date('H:i', strtotime($employeeClocking->time_in_3)) <= date('H:i', strtotime($scheduled['start_time'])) &&
			//	 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

			    echo "OVERTIME: TRUE \n";

			    $isOvertime = true;					
				//$employeeClocking->total_overtime_3 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
				$employeeClocking->total_overtime_3 = $overtime;

			//}

			//HOLIDAY: TRUE
			if( hasHoliday($currentDate) ) {

				 echo "HOLIDAY: TRUE \n";

				if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

					echo "Regular holiday \n";

					//if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->legal_holiday = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);															
						
					/*} else { //ISOVERTIME: TRUE

						$employeeSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/

					//$employeeSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);					

					$employeeSummary->legal_holiday_overtime = ($employeeSummary->legal_holiday_overtime + $overtime);	

					if($hasNightDiff) { //ISOVERTIME: TRUE

						$employeeSummary->legal_holiday_night_diff = ($employeeSummary->legal_holiday_night_diff + $nightDifferential);

					}									

				} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
					

					echo "Special non-working day \n";

					//if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->special_holiday = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																		
						
					/*} else { //ISOVERTIME: TRUE

						$employeeSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/	

					//$employeeSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
					$employeeSummary->special_holiday_overtime = ($employeeSummary->special_holiday_overtime + $overtime);		

					if($hasNightDiff) { //ISOVERTIME: TRUE

						$employeeSummary->special_holiday_night_diff = ($employeeSummary->special_holiday_night_diff + $nightDifferential);

					}									

				}												

			//HOLIDAY: FALSE	
			} else { //Regular Day

				echo "HOLIDAY: FALSE \n";

				echo "Regular Day \n";		

				//if (!$isOvertime) { //ISOVERTIME: FALSE

					//$employeeSummary->regular = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																																	
					
				/*} else { //ISOVERTIME: TRUE

					$employeeSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}*/					

				//$employeeSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
				$employeeSummary->regular_overtime = $overtime;

				if($hasNightDiff) { //ISOVERTIME: TRUE

					$employeeSummary->regular_night_differential = ($employeeSummary->regular_night_differential + $nightDifferential);

				}									

			}

			if ( $employeeClocking->save() ) {
						
				$employeeSummary->save();
				return Redirect::to('/redraw/timesheet');			

			}							

		//REST DAY: TRUE
		} elseif ( $scheduled['rest_day'] === 1 ) {

			echo "REST DAY: TRUE \n";

			$datetime1 = $employeeClocking->time_in_3;
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
			$employeeClocking->total_overtime_3 = $overtime;		
			$employeeClocking->total_hours_3 = $overtime;			

			//TODO RULES: First 8 Hours get the hours_per_day in employee setting

			//LATE/TARDINESS: TRUE
			/*if ( !empty($employeeClocking->tardiness_3) ) {

				echo "LATE/TARDINESS: TRUE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				//$employeeClocking->work_hours_3 = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_3 = 8.00;

				} else {

					$employeeClocking->work_hours_3 = $workHours;

				}																

				//TODO: Compute total hours with out overtime - getTotalHours
				$employeeClocking->total_hours_3 = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

			//LATE/TARDINESS: FALSE
			} else {

				echo "LATE/TARDINESS: FALSE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				$employeeClocking->work_hours_3 = getWorkHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				//TODO: Compute total hours with overtime - getTotalHours
				$employeeClocking->total_hours_3 = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

			}*/

			//GET NIGHTDIFF
			$timeInArr = array();
			$timeOutArr = array();

			$timeInArr = explode(' ', $employeeClocking->time_in_3);
			$timeOutArr = explode(' ', $clockingDateTime);

			$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
			$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

		    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeClocking->time_in_3)));			  
		    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));  				

		    $hasNightDiff = false;

		    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
		    {
		        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

		        	$hasNightDiff = true;

		            //$employeeClocking->night_differential_3 = 8.00;
		            $employeeClocking->night_differential_3 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;

		        } else {

		        	$hasNightDiff = true;		        	

		           //$employeeClocking->night_differential_3 = $workHours;
		           //$employeeClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;		            
				   //$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;	

		            $employeeClocking->night_differential_3 = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;		            
		            $nightDifferential = ($nightDiff['to'] - $timesheet['timeIn']) / 3600;			   	            

		        }

		    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
		        
		        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {

		        	$hasNightDiff = true;		        	
		            
					//$employeeClocking->night_differential_3 = 8.00;
					$employeeClocking->night_differential_3 = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $nightDiff['from']) / 3600;										

		        } else {

		        	$hasNightDiff = true;		        	

					//$employeeClocking->night_differential_3 = $workHours;
					$employeeClocking->night_differential_3 = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					
					$nightDifferential = ($timesheet['timeOut'] - $timesheet['timeIn']) / 3600;					

		        }
		    } else {

		        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
		            
		           	$hasNightDiff = true;

					//$employeeClocking->night_differential_3 = 8.00;
					$employeeClocking->night_differential_3 = ($nightDiff['from'] - $nightDiff['to']) / 3600;
					$nightDifferential = ($nightDiff['from'] - $nightDiff['to']) / 3600;

		        }
		        
		    }			

			//UNDERTIME: TRUE
			/*if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

				echo "UNDERTIME: TRUE \n";
				echo $employeeClocking->undertime_3 = getUnderTimeHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
				$employeeSummary->undertime = getUnderTimeHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																							

			}*/

			//OVERTIME: TRUE
			$isOvertime = false;

			//if ( date('H:i', strtotime($employeeClocking->time_in_3)) <= date('H:i', strtotime($scheduled['start_time'])) &&
			//	 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

			    echo "OVERTIME: TRUE \n";

			    $isOvertime = true;					
				//$employeeClocking->total_overtime_3 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);
			    $employeeClocking->total_overtime_3 = $overtime;

			//}				

			//HOLIDAY: TRUE
			if( hasHoliday($currentDate) ) {

				 echo "HOLIDAY: TRUE \n";

				if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

					echo "Regular holiday \n";

					/*if(!$isOvertime) { //ISOVERTIME: FALSE

						$employeeSummary->rest_day_legal_holiday = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);															
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/						

					//$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);					
					//$employeeSummary->rest_day_legal_holiday_overtime = $overtime;		
					$employeeSummary->rest_day_legal_holiday_overtime = ($employeeSummary->rest_day_legal_holiday_overtime + $overtime);

					if($hasNightDiff) { //ISOVERTIME: TRUE

						//$employeeSummary->rest_day_legal_holiday_night_diff = $nightDifferential;
						$employeeSummary->rest_day_legal_holiday_night_diff = ($employeeSummary->rest_day_legal_holiday_night_diff + $nightDifferential);

					}								

				} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
					

					echo "Special non-working day \n";

					/*if(!$isOvertime) { //ISOVERTIME: FALSE

						$employeeSummary->rest_day_special_holiday = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																		
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->rest_day_special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/	

					//$employeeSummary->rest_day_special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);					
					//$employeeSummary->rest_day_special_holiday_overtime = $overtime;	
					$employeeSummary->rest_day_special_holiday_overtime = ($employeeSummary->rest_day_special_holiday_overtime + $overtime);	

					if($hasNightDiff) { //ISOVERTIME: TRUE

						//$employeeSummary->rest_day_special_holiday_night_diff = $nightDifferential;
						$employeeSummary->rest_day_special_holiday_night_diff = ($employeeSummary->rest_day_special_holiday_night_diff + $nightDifferential);

					}		 

				}												

			//HOLIDAY: FALSE	
			} else { //Regular Day

				echo "HOLIDAY: FALSE \n";

				echo "Regular Rest Day \n";		

				/*if (!$isOvertime) { //ISOVERTIME: FALSE

					$employeeSummary->rest_day = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																																	
					
				} else { //ISOVERTIME: TRUE

					$employeeSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}*/					

				//$employeeSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);				
				//$employeeSummary->rest_day_overtime = $overtime;
				$employeeSummary->rest_day_overtime = ($employeeSummary->rest_day_overtime + $overtime);

				if($hasNightDiff) { //ISOVERTIME: TRUE

					//$employeeSummary->rest_day_night_differential = $nightDifferential;
					$employeeSummary->rest_day_night_differential = ($employeeSummary->rest_day_night_differential + $nightDifferential);

				}					 


			}

			if ( $employeeClocking->save() ) {
						
				$employeeSummary->save();
				return Redirect::to('/redraw/timesheet');			

			}			

		}

	//SCHEDULED : FALSE
	//HAS NO SCHEDULE - EMERGENCY OR SECOND SHIFT
	} else {

	//	echo "SCHEDULED : FALSE \n";	

	}	

}

function clockingStatusClockIn33Out($clockingDateTime) {

	//Settings
	$breakTime = date('H:i', strtotime('01:00:00'));
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');		

	$dayOfTheWeek = date('l');	
	$currentDate = date('Y-m-d');

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	$workShift = new Workshift;
	//$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);	
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, 2);

	//return dd($getWorkShiftByDayOfTheWeek);
	//break;

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);
	
	$timesheet = new Timesheet;
	$employeeClocking = $timesheet->getEmployeeByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$summary = new Summary;
	$employeeSummary = $summary->getEmployeeSummaryByEmployeeIdandDate($employeeId, date('Y-m-d'));

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);				

	if( !empty($getSchedule) ) {

		//echo 'getSchedule';
		
		$scheduled['start_time'] = $getSchedule[0]->start_time;
		$scheduled['end_time'] = $getSchedule[0]->end_time;
		$scheduled['rest_day'] = $getSchedule[0]->rest_day;			
		
	} elseif( !empty($getWorkShiftByDayOfTheWeek) ) {
		
		//echo 'getWorkShiftByDayOfTheWeek';

     	// From 01:00:00 change to 2015-04-30 09:00:00
		$scheduled['start_time'] = date( 'Y-m-d', strtotime($employeeClocking->time_in_3) ).' '.$getWorkShiftByDayOfTheWeek[0]->start_time;

		// From 01:00:00 change to 2015-04-30 01:00:00
		$scheduled['end_time'] =  date( 'Y-m-d', strtotime($clockingDateTime) ).' '.$getWorkShiftByDayOfTheWeek[0]->end_time;

		$scheduled['rest_day'] = $getWorkShiftByDayOfTheWeek[0]->rest_day;
	}


	$employeeClocking->time_out_3 = $clockingDateTime;
	$employeeClocking->clocking_status = 'clock_out_3';	

	// From 2015-04-30 08:36:16 change to 08:36:16
	$clockingIn = date('H:i:s', strtotime($employeeClocking->time_in_3)); 
	// From 2015-04-30 08:36:16 change to 08:36:16	
	$clockingOut = date('H:i:s', strtotime($clockingDateTime)); 

	//SCHEDULED : TRUE
	if ( (!empty($scheduled['start_time']) || $scheduled['start_time'] !== '00:00:00' || $scheduled['start_time'] !== '') &&
	     (!empty($scheduled['end_time']) || $scheduled['end_time'] !== '00:00:00' || $scheduled['end_time'] !== '') ) {

		echo "SCHEDULED : TRUE \n";

		//REST DAY: FALSE
		if ( $scheduled['rest_day'] !== 1 ) {

			echo "REST DAY: FALSE \n";

			//LATE/TARDINESS: TRUE
			//if ( !empty($employeeClocking->tardiness_3) ) {

				echo "LATE/TARDINESS: TRUE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				//$employeeClocking->work_hours_3 = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_3 = 8.00;

				} else {

					$employeeClocking->work_hours_3 = $workHours;					

				}												

				//TODO: Compute total hours with out overtime - getTotalHours
				$employeeClocking->total_hours_3 = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

			//LATE/TARDINESS: FALSE
			/*} else {

				echo "LATE/TARDINESS: FALSE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				$employeeClocking->work_hours_3 = getWorkHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				//TODO: Compute total hours with overtime - getTotalHours
				$employeeClocking->total_hours_3 = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

			}*/

			//GET NIGHTDIFF
			$timeInArr = array();
			$timeOutArr = array();

			$timeInArr = explode(' ', $employeeClocking->time_in_3);
			$timeOutArr = explode(' ', $clockingDateTime);

			$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
			$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

		    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeClocking->time_in_3)));			  
		    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime)));   				

		    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
		    {
		        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

		            $employeeClocking->night_differential_3 = 8.00;

		        } else {

		            $employeeClocking->night_differential_3 = $workHours;

		        }

		    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
		        
		        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
		            
					$employeeClocking->night_differential_3 = 8.00;

		        } else {

					$employeeClocking->night_differential_3 = $workHours;

		        }
		    } else {

		        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
		            
					$employeeClocking->night_differential_3 = 8.00;

		        }
		        
		    }			

			//UNDERTIME: TRUE
			/*if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

				echo "UNDERTIME: TRUE \n";
				echo $employeeClocking->undertime_3 = getUnderTimeHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
				$employeeSummary->undertime = getUnderTimeHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			

			}*/

			//OVERTIME: TRUE
			//$isOvertime = false;

			//if ( date('H:i', strtotime($employeeClocking->time_in_3)) <= date('H:i', strtotime($scheduled['start_time'])) &&
			//	 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

			    echo "OVERTIME: TRUE \n";

			    $isOvertime = true;					
				$employeeClocking->total_overtime_3 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

			//}

			//HOLIDAY: TRUE
			if( hasHoliday($currentDate) ) {

				 echo "HOLIDAY: TRUE \n";

				if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

					echo "Regular holiday \n";

					//if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->legal_holiday = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);															
						
					/*} else { //ISOVERTIME: TRUE

						$employeeSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/

					$employeeSummary->legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);					


				} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
					

					echo "Special non-working day \n";

					//if(!$isOvertime) { //ISOVERTIME: FALSE

						//$employeeSummary->special_holiday = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																		
						
					/*} else { //ISOVERTIME: TRUE

						$employeeSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/	

					$employeeSummary->special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}												

			//HOLIDAY: FALSE	
			} else { //Regular Day

				echo "HOLIDAY: FALSE \n";

				echo "Regular Day \n";		

				//if (!$isOvertime) { //ISOVERTIME: FALSE

					//$employeeSummary->regular = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																																	
					
				/*} else { //ISOVERTIME: TRUE

					$employeeSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}*/					

				$employeeSummary->regular_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);				

			}

			if ( $employeeClocking->save() ) {
						
				$employeeSummary->save();
				return Redirect::to('/redraw/timesheet');			

			}							

		//REST DAY: TRUE
		} elseif ( $scheduled['rest_day'] === 1 ) {

			echo "REST DAY: TRUE \n";

			//TODO RULES: First 8 Hours get the hours_per_day in employee setting

			//LATE/TARDINESS: TRUE
			//if ( !empty($employeeClocking->tardiness_3) ) {

				echo "LATE/TARDINESS: TRUE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				//$employeeClocking->work_hours_3 = getWorkHours($employeeClocking->time_in_2, $clockingDateTime, $scheduled['end_time']);												
				$workHours = getWorkHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				if ( $workHours >= 8 || $workHours >= 8.00 || $workHours >= 8.0 ) {

					$employeeClocking->work_hours_3 = 8.00;

				} else {

					$employeeClocking->work_hours_3 = $workHours;

				}																

				//TODO: Compute total hours with out overtime - getTotalHours
				$employeeClocking->total_hours_3 = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

			//LATE/TARDINESS: FALSE
			/*} else {

				echo "LATE/TARDINESS: FALSE \n";
				//TODO: check employee setting if has_break is true and break time is set. - function getWorkHours
				$employeeClocking->work_hours_3 = getWorkHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

				//TODO: Compute total hours with overtime - getTotalHours
				$employeeClocking->total_hours_3 = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);												

			}*/

			//GET NIGHTDIFF
			$timeInArr = array();
			$timeOutArr = array();

			$timeInArr = explode(' ', $employeeClocking->time_in_3);
			$timeOutArr = explode(' ', $clockingDateTime);

			$nightDiff['from'] = strtotime(date('Y-m-d H:i a', strtotime($timeInArr[0].' '.'22:00:00')));
			$nightDiff['to'] = strtotime(date('Y-m-d H:i a', strtotime($timeOutArr[0].' '.'06:00:00')));  				

		    $timesheet['timeIn'] = strtotime(date('Y-m-d H:i a', strtotime($employeeClocking->time_in_3)));			  
		    $timesheet['timeOut'] = strtotime(date('Y-m-d H:i a', strtotime($clockingDateTime))); 			

		    if ( $timesheet['timeIn'] >= $nightDiff['from'] && $timesheet['timeIn'] <= $nightDiff['to'] )
		    {
		        if ( $timesheet['timeOut'] >= $nightDiff['to'] ) { // SET IT TO 8.00

		            $employeeClocking->night_differential_3 = 8.00;

		        } else {

		            $employeeClocking->night_differential_3 = $workHours;

		        }

		    } elseif ( $timesheet['timeOut'] >= $nightDiff['from'] && $timesheet['timeOut'] <= $nightDiff['to'] ) {
		        
		        if ( $timesheet['timeIn'] <= $nightDiff['from'] ) {
		            
					$employeeClocking->night_differential_3 = 8.00;

		        } else {

					$employeeClocking->night_differential_3 = $workHours;

		        }
		    } else {

		        if($timesheet['timeIn'] < $nightDiff['from'] && $timesheet['timeOut'] > $nightDiff['to']) {
		            
					$employeeClocking->night_differential_3 = 8.00;

		        }
		        
		    }			

			//UNDERTIME: TRUE
			/*if ( strtotime($clockingDateTime) < strtotime($scheduled['end_time']) ) {

				echo "UNDERTIME: TRUE \n";
				echo $employeeClocking->undertime_3 = getUnderTimeHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																			
				$employeeSummary->undertime = getUnderTimeHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['start_time'], $scheduled['end_time']);																							

			}*/

			//OVERTIME: TRUE
			$isOvertime = false;

			//if ( date('H:i', strtotime($employeeClocking->time_in_3)) <= date('H:i', strtotime($scheduled['start_time'])) &&
			//	 date('H:i', strtotime($clockingDateTime)) > date('H:i', strtotime($scheduled['end_time'])) ) {

			    echo "OVERTIME: TRUE \n";

			    $isOvertime = true;					
				$employeeClocking->total_overtime_3 = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

			//}				

			//HOLIDAY: TRUE
			if( hasHoliday($currentDate) ) {

				 echo "HOLIDAY: TRUE \n";

				if ( 'Regular holiday' === $getHolidayByDate[0]->holiday_type ) { //Regular holiday

					echo "Regular holiday \n";

					/*if(!$isOvertime) { //ISOVERTIME: FALSE

						$employeeSummary->rest_day_legal_holiday = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);															
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/						

					$employeeSummary->rest_day_legal_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);					

				} elseif ( 'Special non-working day' === $getHolidayByDate[0]->holiday_type ) { //Special non-working day
					

					echo "Special non-working day \n";

					/*if(!$isOvertime) { //ISOVERTIME: FALSE

						$employeeSummary->rest_day_special_holiday = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																		
						
					} else { //ISOVERTIME: TRUE

						$employeeSummary->rest_day_special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

					}*/	

					$employeeSummary->rest_day_special_holiday_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);					

				}												

			//HOLIDAY: FALSE	
			} else { //Regular Day

				echo "HOLIDAY: FALSE \n";

				echo "Regular Rest Day \n";		

				/*if (!$isOvertime) { //ISOVERTIME: FALSE

					$employeeSummary->rest_day = getTotalHours($employeeClocking->time_in_3, $clockingDateTime, $scheduled['end_time']);																																	
					
				} else { //ISOVERTIME: TRUE

					$employeeSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);

				}*/					

				$employeeSummary->rest_day_overtime = getOvertimeHours($clockingDateTime, $scheduled['end_time']);				

			}

			if ( $employeeClocking->save() ) {
						
				$employeeSummary->save();
				return Redirect::to('/redraw/timesheet');			

			}			

		}

	//SCHEDULED : FALSE
	//HAS NO SCHEDULE - EMERGENCY OR SECOND SHIFT
	} else {

		echo "SCHEDULED : FALSE \n";	

	}	

}	

//get schedule
//Check if there is assign schedule today
//if ( $hasSchedule && strtotime($getSchedule[0]->start_time) !== strtotime('00:00:00')) {
function hasSchedule() {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');		

	$schedule = new Schedule;
	$hasSchedule = $schedule->checkSchedule($employeeId, date('Y-m-d'));		
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	if ( $hasSchedule && strtotime($getSchedule[0]->start_time) !== '' ) {
		
		return true;

	} else { //check workShift table default schedule if no schedule in the schedule table

		return false;

	}

}

function getSchedule() {

	return 'Scheduled';

}


function hasWorkShiftSchedule() {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');		

	$dayOfTheWeek = date('l');

	$workShift = new Workshift;
	$hasWorkShiftSchedule = $workShift->checkWorkShiftSchedule($employeeId);			
	//$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);
	$getWorkShiftByDayOfTheWeek = $workShift->getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek);	

	if ( $hasWorkShiftSchedule && strtotime($getWorkShiftByDayOfTheWeek[0]->start_time) !== '' ) {
		
		return true;

	} else {

		return false;

	}

}

function getWorkShiftScheduleByEmployeeId() {

	$workShift = new Workshift;
	$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);

	return $getWorkShiftByEmployeeId[0]->start_time;

}

/**
*
* TARDINESS TIME
*
*/

function getTardinessTime($clockingIn, $shiftStart) {

	//$clockingIn = date('H:i:s', strtotime($clockingIn)); // removing the date

	//Compare Clocking in time from today schedule
	if( strtotime($clockingIn) > strtotime($shiftStart) ) {

		$interval = getDateTimeDiffInterval($clockingIn, $shiftStart);		

		$hh = $interval->format('%H');
		$mm = $interval->format('%I');
		$ss = $interval->format('%S');	

		//$tardiness = getTimeToDecimalHours($hh, $mm, $ss); //number_format($hours, 2);
		$tardiness = getTimeToDecimalMinutes($hh, $mm, $ss);

		return number_format($tardiness, 2);

	}

}

//function getTotalHours_1($clockingIn, $clockingOut, $hasOvertime = false, $overTime = '', $output = "")
//function getTotalHours($clockingIn, $clockingOut, $shiftEnd) {

function getTotalHours($clockingIn, $clockingOut, $shiftEnd) {

	//$employeeId = Session::get('userEmployeeId');
	//$userId = Session::get('userId');

	/*if ( !empty(Session::get('searchEmployeeId')) ) {	
	
		$employeeId = Session::get('searchEmployeeId');

	} else {

		$employeeId = Session::get('userEmployeeId');
	
	}*/

	$searchEmployeeId = Session::get('searchEmployeeId');

	if ( !empty($searchEmployeeId) ) {
	
		$employeeId = $searchEmployeeId;

	} else {

		$employeeId = Session::get('userEmployeeId');
	
	}	


	//return $employeeId;
	//die();

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));
	//$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	$workShift = new Workshift;
	$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);	

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

	//return dd($getSchedule);		
	

	if( !empty($getSchedule) ) {

		$schedule['start_time'] = $getSchedule[0]->start_time;
		$schedule['end_time'] = $getSchedule[0]->end_time;			
		$schedule['hours_per_day'] = $getSchedule[0]->hours_per_day;

		$halfOfhoursPerDay = ($schedule['hours_per_day'] / 2); // e.g 8.00 without break
		
	} elseif( !empty($getWorkShiftByEmployeeId) ) {

		$workShift['start_time'] = $getWorkShiftByEmployeeId[0]->start_time;
		$workShift['end_time'] = $getWorkShiftByEmployeeId[0]->end_time;		
		$workShift['hours_per_day'] = $getWorkShiftByEmployeeId[0]->hours_per_day;			

		$halfOfhoursPerDay = ($workShift['hours_per_day'] / 2); // e.g 8.00 without break

	}		

	if ( !empty($employeeSetting->hours_per_day) ) {

		$halfOfhoursPerDay = ($employeeSetting->hours_per_day / 2); // e.g 8.00 without break
		$setting['break_time'] = $employeeSetting->break_time;

	} elseif( empty($employeeSetting->hours_per_day) ) {

		if( !empty($getSchedule) ) {

			$halfOfhoursPerDay = ($schedule['hours_per_day'] / 2); // e.g 8.00 without break
		
		} elseif( !empty($getWorkShiftByEmployeeId) ) {
	
			$halfOfhoursPerDay = ($workShift['hours_per_day'] / 2); // e.g 8.00 without break

		}

	}

	$interval = getDateTimeDiffInterval($clockingIn, $clockingOut);

	$days = $interval->format('%a');
	$days = (int) $days;

	if ( $days !== 0 ) {
		
		$hhToDays = ($days * 24);
		$hh = (int) $hhToDays;				

	} else {

		$hh = (int) $interval->format('%h');

	}

	$mm = (int) $interval->format('%i');
	$ss = (int) $interval->format('%s');	

	$totalHours = getTimeToDecimalHours($hh, $mm, $ss); //number_format($hours, 2);
	$overtimeHours = getOvertimeHours($clockingOut, $shiftEnd);

	if ( $employeeSetting->has_break === 1 ) {			

		list($breakTimeHh, $breakTimeMm, $breakTimeSs) = explode(':', $setting['break_time']);
		$breakTime = getTimeToDecimalHours($breakTimeHh, $breakTimeMm, $breakTimeSs);

	}	

	//$clockingOut = date('Y-m-d', strtotime($shiftEnd)).' '.$clockingOut;
	
	if ( strtotime(date('H:i', strtotime($clockingOut))) >
		 strtotime(date('H:i', strtotime($shiftEnd))) ) {
			
		if ( $employeeSetting->has_break === 1 ) {

			if( $totalHours > $halfOfhoursPerDay ) {

				return ( number_format($totalHours, 2) + number_format($overtimeHours, 2) ) - $breakTime; 
				//return ( number_format($totalHours, 2) + number_format($overtimeHours, 2) ); 

			} else {

				return ( number_format($totalHours, 2) + number_format($overtimeHours, 2) );

			}
		
		} else {

			return ( number_format($totalHours, 2) + number_format($overtimeHours, 2) );
		
		}

	} else {

		if ( $employeeSetting->has_break === 1 ) {

			if( $totalHours > $halfOfhoursPerDay ) {

				return number_format($totalHours, 2) - $breakTime;					
				//return number_format($totalHours, 2);

			} else {

				return number_format($totalHours, 2);

			}
		
		} else {

			return number_format($totalHours, 2);
		
		}			

	}

}


function getTotalMinutes($clockingIn, $clockingOut, $shiftEnd) {

	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));
	//$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	$workShift = new Workshift;
	$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);	

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

	//return dd($getSchedule);		
	

	if( !empty($getSchedule) ) {

		$schedule['start_time'] = $getSchedule[0]->start_time;
		$schedule['end_time'] = $getSchedule[0]->end_time;			
		$schedule['hours_per_day'] = $getSchedule[0]->hours_per_day;

		$halfOfhoursPerDay = ($schedule['hours_per_day'] / 2); // e.g 8.00 without break
		
	} elseif( !empty($getWorkShiftByEmployeeId) ) {

		$workShift['start_time'] = $getWorkShiftByEmployeeId[0]->start_time;
		$workShift['end_time'] = $getWorkShiftByEmployeeId[0]->end_time;		
		$workShift['hours_per_day'] = $getWorkShiftByEmployeeId[0]->hours_per_day;			

		$halfOfhoursPerDay = ($workShift['hours_per_day'] / 2); // e.g 8.00 without break

	}		

	if ( !empty($employeeSetting->hours_per_day) ) {

		$halfOfhoursPerDay = ($employeeSetting->hours_per_day / 2); // e.g 8.00 without break
		$setting['break_time'] = $employeeSetting->break_time;

	} elseif( empty($employeeSetting->hours_per_day) ) {

		if( !empty($getSchedule) ) {

			$halfOfhoursPerDay = ($schedule['hours_per_day'] / 2); // e.g 8.00 without break
		
		} elseif( !empty($getWorkShiftByEmployeeId) ) {
	
			$halfOfhoursPerDay = ($workShift['hours_per_day'] / 2); // e.g 8.00 without break

		}

	}

	$interval = getDateTimeDiffInterval($clockingIn, $clockingOut);

	$days = $interval->format('%a');
	$days = (int) $days;

	if ( $days !== 0 ) {
		
		$hhToDays = ($days * 24);
		$hh = (int) $hhToDays;				

	} else {

		$hh = (int) $interval->format('%h');

	}

	$mm = (int) $interval->format('%i');
	$ss = (int) $interval->format('%s');	

	$totalHours = getTimeToDecimalMinutes($hh, $mm, $ss); //number_format($hours, 2);
	$overtimeHours = getOvertimeHours($clockingOut, $shiftEnd);

	if ( $employeeSetting->has_break === 1 ) {			

		list($breakTimeHh, $breakTimeMm, $breakTimeSs) = explode(':', $setting['break_time']);
		$breakTime = getTimeToDecimalMinutes($breakTimeHh, $breakTimeMm, $breakTimeSs);

	}		

	//$clockingOut = date('Y-m-d', strtotime($shiftEnd)).' '.$clockingOut;
	
	if ( strtotime(date('H:i', strtotime($clockingOut))) >
		 strtotime(date('H:i', strtotime($shiftEnd))) ) {
			
		if ( $employeeSetting->has_break === 1 ) {

			if( $totalHours > $halfOfhoursPerDay ) {

				return ( number_format($totalHours, 2) + number_format($overtimeHours, 2) ) - $breakTime; 

			} else {

				return ( number_format($totalHours, 2) + number_format($overtimeHours, 2) );

			}
		
		} else {

			return ( number_format($totalHours, 2) + number_format($overtimeHours, 2) );
		
		}

	} else {

		if ( $employeeSetting->has_break === 1 ) {

			if( $totalHours > $halfOfhoursPerDay ) {

				return number_format($totalHours, 2) - $breakTime;					

			} else {

				return number_format($totalHours, 2);

			}
		
		} else {

			return number_format($totalHours, 2);
		
		}			

	}

}

function getTotalHoursYesterday($clockingIn, $clockingOut, $shiftEnd) {

	$yesterDayDate = date("Y-m-d", strtotime('yesterday'));

	$schedule = new Schedule;
	$getSchedule = $schedule->getSchedule($employeeId, $yesterDayDate);
	//$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

	$workShift = new Workshift;
	$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);			

	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

	if( !empty($getSchedule) ) {

		$schedule['start_time'] = $getSchedule[0]->start_time;
		$schedule['end_time'] = $getSchedule[0]->end_time;			
		$schedule['hours_per_day'] = $getSchedule[0]->hours_per_day;

		$halfOfhoursPerDay = ($schedule['hours_per_day'] / 2); // e.g 8.00 without break
		
	} elseif( !empty($getWorkShiftByEmployeeId) ) {

		/*$workShift['start_time'] = $getWorkShiftByEmployeeId[0]->start_time;
		$workShift['end_time'] = $getWorkShiftByEmployeeId[0]->end_time;		
		$workShift['hours_per_day'] = $getWorkShiftByEmployeeId[0]->hours_per_day;*/

		$workShift['start_time'] = date( 'Y-m-d', strtotime($clockingIn) ).' '.$getWorkShiftByEmployeeId[0]->start_time;						
		$workShift['end_time'] =  date( 'Y-m-d', strtotime('-1 day') ).' '.$getWorkShiftByEmployeeId[0]->end_time;
		//$workShift['rest_day'] = explode(', ', $getWorkShiftByEmployeeId[0]->rest_day);			
		$workShift['hours_per_day'] = $getWorkShiftByEmployeeId[0]->hours_per_day;

		$halfOfhoursPerDay = ($workShift['hours_per_day'] / 2); // e.g 8.00 without break

	}		

	if ( !empty($employeeSetting->hours_per_day) ) {

		$halfOfhoursPerDay = ($employeeSetting->hours_per_day / 2); // e.g 8.00 without break
		$setting['break_time'] = $employeeSetting->break_time;

	}		

	$interval = getDateTimeDiffInterval($clockingIn, $clockingOut);

	$days = $interval->format('%a');
	$days = (int) $days;

	if ( $days !== 0 ) {
		
		$hhToDays = ($days * 24);
		$hh = (int) $hhToDays;				

	} else {

		$hh = (int) $interval->format('%h');

	}

	$mm = (int) $interval->format('%i');
	$ss = (int) $interval->format('%s');	

	$totalHours = getTimeToDecimalHours($hh, $mm, $ss); //number_format($hours, 2);
	$overtimeHours = getOvertimeHours($clockingOut, $shiftEnd);

	if ( $employeeSetting->has_break === 1 ) {			

		list($breakTimeHh, $breakTimeMm, $breakTimeSs) = explode(':', $setting['break_time']);
		$breakTime = getTimeToDecimalHours($breakTimeHh, $breakTimeMm, $breakTimeSs);

	}		

	//$clockingOut = date('Y-m-d', strtotime($shiftEnd)).' '.$clockingOut;
	
	if ( strtotime(date('H:i', strtotime($clockingOut))) >
		 strtotime(date('H:i', strtotime($shiftEnd))) ) {
			
		if ( $employeeSetting->has_break === 1 ) {

			if( $totalHours > $halfOfhoursPerDay ) {

				return ( number_format($totalHours, 2) + number_format($overtimeHours, 2) ) - $breakTime; 

			} else {

				return ( number_format($totalHours, 2) + number_format($overtimeHours, 2) );

			}
		
		} else {

			return ( number_format($totalHours, 2) + number_format($overtimeHours, 2) );
		
		}

	} else {

		if ( $employeeSetting->has_break === 1 ) {

			if( $totalHours > $halfOfhoursPerDay ) {

				return number_format($totalHours, 2) - $breakTime;					

			} else {

				return number_format($totalHours, 2);

			}
		
		} else {

			return number_format($totalHours, 2);
		
		}			

	}

}	

//Check this code
function getOvertimeHours($clockingOut, $shiftEnd) {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');

	/*$datetime1 = new DateTime($clockingOut);
	$datetime2 = new DateTime($shiftEnd);
	$interval = $datetime1->diff($datetime2);*/

	$interval = getDateTimeDiffInterval($clockingOut, $shiftEnd);

	$days = $interval->format('%a');
	$days = (int) $days;

	if ( $days !== 0 ) {
		
		$hhToDays = ($days * 24);
		$hh = (int) $hhToDays;				

	} else {

		$hh = (int) $interval->format('%h');

	}

	$mm = (int) $interval->format('%i');
	$ss = (int) $interval->format('%s');

	//return strtotime($clockingOut).' '.strtotime($shiftEnd);

	if( strtotime($clockingOut) > strtotime($shiftEnd) ) {

		$overtimeHours = getTimeToDecimalHours($hh, $mm, $ss); //number_format($hours, 2);

		return number_format($overtimeHours, 2);

	}

}		


//function hasOvertime($clockingDateTime, $shiftStart) {
function hasOvertime($clockingIn, $clockingDateTime, $shiftStart, $shiftEnd) {
	
	//$interval = getDateTimeDiffInterval($clockingDateTime, $shiftEnd);		

	/*echo "\n";
	echo $hh = (int) $interval->format('%h');
	echo "\n";
	echo $mm = (int) $interval->format('%i');
	echo "\n";
	echo "\n";*/

	/*
	//with 1 hr break
	//if ( ($hh >= 6) && ($mm > 0) ) { //WITH OVERTIME
	if ( ($hh >= 9) && ($mm > 0) ) { //WITH OVERTIME			
	
		return true;

	//} elseif ( ($hh === 6) && ($mm === 0) ) { //NO OVERTIME			
	} elseif ( ($hh === 9) && ($mm === 0) ) { //NO OVERTIME

		return false;

	}*/	

	/*
	echo $clockingIn.' <= '.$shiftStart;
	echo "\n";
	echo $clockingDateTime.' > '.$shiftEnd;	
	*/

	if ( (strtotime($clockingIn) <= strtotime($shiftStart)) &&
	 	 (strtotime($clockingDateTime) > strtotime($shiftEnd)) ) {

		return true;

	}  else {

		return false;

	}

}


	//Todo Add a the break 
	//function getWorkHours_11($clockingIn, $clockingOut,$hasBreak = false, $breakTime = '', $output = "") {
	function getWorkHours($clockingIn, $clockingOut, $shiftEnd) {

		//$employeeId = Session::get('userEmployeeId');
		//$userId = Session::get('userId');

		/*if ( !empty(Session::get('searchEmployeeId')) ) {	
		
			$employeeId = Session::get('searchEmployeeId');

		} else {

			$employeeId = Session::get('userEmployeeId');		

		}*/

		$searchEmployeeId = Session::get('searchEmployeeId');

		if ( !empty($searchEmployeeId) ) {
		
			$employeeId = $searchEmployeeId;

		} else {

			$employeeId = Session::get('userEmployeeId');
		
		}		

		$schedule = new Schedule;
		$getSchedule = $schedule->getSchedule($employeeId, date('Y-m-d'));

		$workShift = new Workshift;
		$getWorkShiftByEmployeeId = $workShift->getWorkShiftByEmployeeId($employeeId);	

		$setting = new Setting;
		$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

		if( !empty($getSchedule) ) {

			$schedule['start_time'] = $getSchedule[0]->start_time;
			$schedule['end_time'] = $getSchedule[0]->end_time;			
			$schedule['hours_per_day'] = $getSchedule[0]->hours_per_day;

			$halfOfhoursPerDay = ($schedule['hours_per_day'] / 2); // e.g 8.00 without break
			
		} elseif( !empty($getWorkShiftByEmployeeId) ) {

			$workShift['start_time'] = $getWorkShiftByEmployeeId[0]->start_time;
			$workShift['end_time'] = $getWorkShiftByEmployeeId[0]->end_time;		
			$workShift['hours_per_day'] = $getWorkShiftByEmployeeId[0]->hours_per_day;			

			$halfOfhoursPerDay = ($workShift['hours_per_day'] / 2); // e.g 8.00 without break

		}		

		if ( !empty($employeeSetting->hours_per_day) ) {

			$halfOfhoursPerDay = ($employeeSetting->hours_per_day / 2); // e.g 8.00 without break
			$setting['has_break'] = $employeeSetting->has_break;
			$setting['break_time'] = $employeeSetting->break_time;
			
		}


		//$interval = getDateTimeDiffInterval($clockingIn, $shiftEnd);
		$interval = getDateTimeDiffInterval($clockingIn, $clockingOut);		

		$days = $interval->format('%a');
		$days = (int) $days;

		if ( $days !== 0 ) {
			
			$hhToDays = ($days * 24);
			$hh = (int) $hhToDays;				

		} else {

			$hh = (int) $interval->format('%h');

		}

		$mm = (int) $interval->format('%i');
		$ss = (int) $interval->format('%s');	

		if ( $setting['has_break'] === 1 ) {

			
			list($breakTimeHh, $breakTimeMm, $breakTimeSs) = explode(':', $setting['break_time']);
			//$breakTimeArr = explode(':', $setting['break_time']);

			$breakTime = getTimeToDecimalHours($breakTimeHh, $breakTimeMm, $breakTimeSs);
			//$breakTime = getTimeToDecimalHours($breakTimeArr[0], $breakTimeArr[1], $breakTimeArr[2]);
			$workHours = getTimeToDecimalHours($hh, $mm, $ss); //number_format($hours, 2);

			if( $workHours > $halfOfhoursPerDay ) {
				
				//return $breakTime;
				$workHours = $workHours - $breakTime;
				return number_format($workHours, 2);

			} else {

				$workHours = getTimeToDecimalHours($hh, $mm, $ss);
				return number_format($workHours, 2);

			}

		} else {

			$workHours = getTimeToDecimalHours($hh, $mm, $ss); //number_format($hours, 2);
			return number_format($workHours, 2);

		}

		//return number_format($workHours, 2);

	}	


function hasUndertime($clockingIn, $clockingOut, $shiftStart, $shiftEnd) { //remove this later is has been change to isUndertime
	
	if( strtotime($clockingOut) < strtotime($shiftEnd) ) {

		return true;	

	}

}

function isUndertime($clockingIn, $clockingOut, $shiftStart, $shiftEnd) {
	
	if( strtotime($clockingOut) < strtotime($shiftEnd) ) {

		return true;	

	}

}


//function getUnderTimeHours($clockingIn, $clockingOut, $shiftStart, $shiftEnd, $hasBreak = false, $breakTime = '', $output = "") {
function getUnderTimeHours($clockingIn, $clockingOut, $shiftStart, $shiftEnd) {
	
	$employeeId = Session::get('userEmployeeId');
	$userId = Session::get('userId');
	
	$setting = new Setting;
	$employeeSetting = $setting->getEmployeeSettingByEmployeeId($employeeId);

	//$interval = getDateTimeDiffInterval($clockingOut, $shiftStart);
	//$interval = getDateTimeDiffInterval($clockingOut, $clockingIn);

	$interval = getDateTimeDiffInterval($clockingOut, $shiftEnd);

	$hh = (int) $interval->format('%h');
	$mm = (int) $interval->format('%i');
	$ss = (int) $interval->format('%s');	

	//$underTimeHours = getTimeToDecimalHours($hh, $mm, $ss); //number_format($hours, 2);
	$underTimeHours = getTimeToDecimalMinutes($hh, $mm, $ss);
	
	return number_format($underTimeHours, 2);		


}

function hasHoliday($currentDate) {

	$holiday = new Holiday;
	$getHolidayByDate = $holiday->getHolidayByDate($currentDate);

	if( !empty($getHolidayByDate[0]->holiday_type) ) {

		return true;

	} else {

		return false;

	}

}	

/**
*
*HELPER FUNCTIONS
*
*/

/*
|--------------------------------------------------------------------------
| DateTime Controller
| http://www.calculatorsoup.com/calculators/time/
|--------------------------------------------------------------------------	
*/

function getTimeToDecimalHours($hh = 0, $mm = 0, $ss = 0) { //Used

	//To convert time to just hours	
	$hours = $hh * (1 / 1); // or $hh	
	$minutes = $mm * (1 / 60); // or $mm / 60 hours	
	$seconds = $ss * (1 / 3600); // $ss / 3600 hours	

	return $hours + $minutes + $seconds;

}

function getTimeToDecimalMinutes($hh, $mm, $ss) { //Used

	//To convert time to just minutes	
	$hourstominutes = $hh * (60 / 1); // or $hh * 60 minutes	
	$minutes = $mm * (1 / 1); // or $mm * 1 minutes 	
	$secondstominutes = $ss * (1 / 60); // or $ss / 60 minutes	
	return $hourstominutes + $minutes + $secondstominutes;
	
}

function getTimeToDecimalSeconds($hh, $mm, $ss) { //Used

	//To convert time to just seconds
	$hourstoseconds = $hh * (3600 / 1); // or $hh * 3600 seconds
	$minutestoseconds = $mm * (60 / 1); // or $mm * 60 seconds
	$seconds = $ss * (1 / 1); // or $ss / 1 seconds		
	return $hourstoseconds + $minutestoseconds + $seconds;		

}

function getDateTimeDiffInterval($datetime1, $datetime2) { //Used

	$datetime1 = new DateTime($datetime1);
	$datetime2 = new DateTime($datetime2);
	$interval = $datetime1->diff($datetime2);	
	
	//return $interval->format($format);

	return $interval;

}

function getDateTimeDiffIntervalReverse($datetime1, $datetime2) { //Used

	$datetime1 = new DateTime($datetime2);
	$datetime2 = new DateTime($datetime1);
	$interval = $datetime1->diff($datetime2);	
	
	//return $interval->format($format);

	return $interval;

}

/*
*
* OLD Functions
*
*/

function getTimeDiff($datetime1, $datetime2, $format = '%H:%I:%S') { //old

	$datetime1 = new DateTime($datetime1);
	$datetime2 = new DateTime($datetime2);
	$interval = $datetime1->diff($datetime2);	
	
	return $interval->format($format);

}

function decimalMinutesFormat($decimalMinutes) {	

	$timeArr = explode(':', $decimalMinutes);

	if(empty($timeArr[0])) {
		$timeArr[0] = 0;
	}

	if(empty($timeArr[1])) {
		$timeArr[1] = 0;
	}

	if(empty($timeArr[2])) {
		$timeArr[2] = 0;
	}

	//Decimal minutes to time format
	return ($timeArr[0] * 60) + $timeArr[1] + ($timeArr[2] / 60);	

}


// * http://stackoverflow.com/questions/9102680/how-to-convert-a-decimal-into-time-eg-hhmmss
//http://www.hashbangcode.com/blog/converting-and-decimal-time-php
//http://forums.phpfreaks.com/topic/127520-solved-convert-decimal-to-time/

function decimalToTime($decimal)
{
	if( !empty($decimal) ) {

	    // start by converting to seconds
	    $seconds = (int)($decimal * 3600);
	    // we're given hours, so let's get those the easy way
	  	$hours = floor($decimal);
	    // since we've "calculated" hours, let's remove them from the seconds variable
	    $seconds -= $hours * 3600;
	    // calculate minutes left
	    $minutes = floor($seconds / 60);
	    // remove those from seconds as well
	    $seconds -= $minutes * 60;
	    // return the time formatted HH:MM:SS
	    //return lz($hours).":".lz($minutes).":".lz($seconds);
				    
		if( $hours === -1 || $hours === -1.0 || $hours === -1.00 ) {

			$hours = 0;

			$format = '%sm';
			return sprintf( $format, $minutes ); 				
			
		} else {

			$format = '%sh %sm';
			return sprintf( $format, $hours, $minutes ); 				

		}

   } else {

   		return '-';

   }

	
}

function decimalToTimeSummary($decimal)
{
	if( !empty($decimal) ) {

	    // start by converting to seconds
	    $seconds = (int)($decimal * 3600);
	    // we're given hours, so let's get those the easy way
	    $hours = floor($decimal);
	    // since we've "calculated" hours, let's remove them from the seconds variable
	    $seconds -= $hours * 3600;
	    // calculate minutes left
	    $minutes = floor($seconds / 60);
	    // remove those from seconds as well
	    $seconds -= $minutes * 60;
	    // return the time formatted HH:MM:SS
	    //return lz($hours).":".lz($minutes).":".lz($seconds);
		
		if( $hours === -1 || $hours === -1.0 || $hours === -1.00 ) {

			$hours = 0;

			$format = '%sm';
			return sprintf( $format, $minutes ); 				
			
		} else {

			$format = '%sh %sm';
			return sprintf( $format, $hours, $minutes ); 				

		}	

   } else {

   		return ' - ';

   }
	
}	

function decimalToTimeFormat($decimal, $format = '%s:%s:%s')
{
	if( !empty($decimal) ) {

	    // start by converting to seconds
	    $seconds = (int)($decimal * 3600);
	    // we're given hours, so let's get those the easy way
	    $hours = floor($decimal);
	    // since we've "calculated" hours, let's remove them from the seconds variable
	    $seconds -= $hours * 3600;
	    // calculate minutes left
	    $minutes = floor($seconds / 60);
	    // remove those from seconds as well
	    $seconds -= $minutes * 60;
	    // return the time formatted HH:MM:SS
	    //return lz($hours).":".lz($minutes).":".lz($seconds);
		
		//$format = '%sh %sm';
		//return sprintf( $format, $hours, $minutes ); 	

		if ($format === '%sh %sm') {

			return sprintf( $format, $hours, $minutes ); 	

		} elseif($format === '%sh %sm %ss') {

			return sprintf( $format, $hours, $minutes, $seconds ); 	

		} elseif($format === '%s:%s:%s') {

			return sprintf( $format, $hours, $minutes, $seconds ); 	
			
		}	


   } else {

   		return ' - ';

   }
	
}

function getTotal($decimaltime1 = 0.00, $decimaltime2 = 0.00, $decimaltime3 = 0.00) {

	$decimaltime = (double) $decimaltime1 + (double) $decimaltime2 + (double) $decimaltime3;
	
		return number_format($decimaltime, 2, '.', '');			

}


function getTotalHoursTotal($totalHours1 = 0.00, $totalHours2 = 0.00, $totalHours3 = 0.00) {

	$totalHours = (double) $totalHours1 + (double) $totalHours2 + (double) $totalHours3;
	return $totalHours;		

}

function getWorkHoursTotal($workHours1 = 0.00, $workHours2 = 0.00, $workHours3 = 0.00) {

	$workHours = (double) $workHours1 + (double) $workHours2 + (double) $workHours3;
	return $workHours;

}

function getOvertimeTotal($overtime1 = 0.00, $overtime2 = 0.00, $overtime3 = 0.00) {

	$totalOvertime = (double) $overtime1 + (double) $overtime2 + (double) $overtime3;
	return $totalOvertime;

}

function getTardinessTotal($tardiness1 = 0.00, $tardiness2 = 0.00, $tardiness3 = 0.00) {

	$totalTardiness = (double) $tardiness1 + (double) $tardiness2 + (double) $tardiness3;
	return $totalTardiness;

}	

function getUndertimeTotal($undertime1 = 0.00, $undertime2 = 0.00, $undertime3 = 0.00) {

	$totalUndertime = (double) $undertime1 + (double) $undertime2 + (double) $undertime3;
	return $totalUndertime;

}	

/*function getDecimalTimeFormat($decimalTime) {

	if( $decimalTime !== 0 || $decimalTime !== 0.00 ) {
	
		//date( 'H:i:s', strtotime($decimalTime) );

		$hh = date( 'H', strtotime($decimalTime) );
		$mm = date( 'i', strtotime($decimalTime) );
		$ss = date( 's', strtotime($decimalTime) );
			
		//return $hh .' h'. $mm .' m';
	
		$format = '%sh %sm';
		return sprintf( $format, $hh, $mm );
			
	} else {

		return '0.00';

	}       		
}*/


//http://en.wikipedia.org/wiki/Shift_plan
function getshiftPlanWorkSequence($hour) {

	$hour = date('H', time());

	if( $hour > 7 && $hour <= 15 || $hour > 6 && $hour <= 18 ) { //day shift, 1st shift - This shift often occurs from 07:00 to 15:00 for eight-hour shifts, and from 06:00 to 18:00 for twelve-hour shifts.
	
	  return "D";
	
	} elseif( $hour > 15 && $hour <= 23 ) { //swing shift, 2nd shift - This shift often occurs from 15:00 to 23:00 for eight-hour shifts, and is not used with twelve-hour shifts.
	
	  return "S";
	
	} elseif( $hour > 23 && $hour <= 7 || $hour > 18 && $hour <= 6 ) { //night shift, 3rd shift, graveyard shift - This shift often occurs from 23:00 to 07:00 for eight-hour shifts, and from 18:00 to 06:00 for twelve-hour shifts.
	
	  return "N";
	
	}

}

//http://www.learnersdictionary.com/qa/parts-of-the-day-early-morning-late-morning-etc
function getPartsOfDay($timeIn) {

	$hour = date('H', strtotime($timeIn));
	
	//Morning
	if ( $hour >= 5 && $hour <= 12 ) {

		return 'morning';

	//Afternoon
	} elseif ($hour >= 12 && $hour <= 17) {

		return 'afternoon';

	} elseif ($hour >= 17 && $hour <= 21) {

		return '';

	} elseif ($hour >= 21 && $hour <= 4) {

		return '';

	}

}



//Summary Computation
//function latesUndertimeCutoffTotal($decimalArr) {
function cutoffTotal($decimalArr) {

	$total = 0;
	foreach ( $decimalArr as $decimal ) {

		$total += $decimal;

	}  

	//return $total;
	return number_format((double) $total, 2, '.', '');

}	

