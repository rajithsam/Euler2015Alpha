<?php

	/**
	*
	*HELPER FUNCTIONALITY
	*
	*/

	/*
	|--------------------------------------------------------------------------
	| DateTime Controller
	| http://www.calculatorsoup.com/calculators/time/
	|--------------------------------------------------------------------------	
	*/

	function getTimeDiff($timeIn, $timeOut, $format = '%H:%I:%S') {

		$datetime1 = new DateTime($timeOut);
		$datetime2 = new DateTime($timeIn);
		$interval = $datetime1->diff($datetime2);	
		
		return $interval->format($format);

	}

	//Convert: decimal time to time format
	/*function decimalTimeToTimeFormat($decimalTime, $format = 'H:i:s') {

		$decimalTimeArr = explode('.', number_format($decimalTime, 2));	

		$hh = $decimalTimeArr[0];

		$hours = abs($decimalTime) - floor(abs($decimalTime));
		$mm = floor($hours * 60);
		$ss = abs($hours * 60) - floor($hours * 60);

		return $timeFormat = date('H:i:s', strtotime(sprintf('%s:%s:%s', $hh, $mm, $ss)));

	}*/

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

	/*function timeToDecimal($time) { //Not use

	    $timeArr = explode(':', $time);
	    $decTime = ($timeArr[3] * 60) + ($timeArr[4]) + ($timeArr[5] / 60);
	 			   
	    return $timeArr;    

	}*/

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
			
			$format = '%sh %sm';
			return sprintf( $format, $hours, $minutes ); 	

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
			
			$format = '%sh %sm';
			return sprintf( $format, $hours, $minutes ); 	

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

	// lz = leading zero
	/*function lz($num)
	{
	    return (strlen($num) < 2) ? "0{$num}" : $num;
	}*/	
	
	/*function timeToMinutes($time) { //Not use

		$timeArr = explode(':', $time);

		$hh = $timeArr[0] * (60 / 1);
		$mm = $timeArr[1] * (1 / 1);
		$ss = $timeArr[2] * (1 / 60);

		return $hh = $hh + $mm + $ss;
		
	}*/


	/*
	* Computation: Work hours
	*/
	function getWorkHours($clockingIn, $clockingOut, $hasBreak = false, $breakTime = '', $output = "") {
		
		$clocking['in'] = $clockingIn;
		$clocking['out'] = $clockingOut;
		
		//$hasBreak = false; 
		//$breakTime = '01:00:00';
		$breakTimeArr = explode(':', $breakTime);

		getTimeDiff($clocking['out'], $clocking['in']);
		$timeArr1 = explode( ':', getTimeDiff($clocking['out'], $clocking['in']) );


		if ($timeArr1[0] !== '' && $timeArr1[1] !== '') {

			if (!$hasBreak) {

				$workHours = ($timeArr1[0]) + ($timeArr1[1] / 60) + ($timeArr1[2] / 3600);		

			} else {

				$workHours = ($timeArr1[0] - $breakTimeArr[0]) + (($timeArr1[1] + $breakTimeArr[1]) / 60) + (($timeArr1[2] + $breakTimeArr[2]) / 3600);											
				
			}

			$workHours = number_format($workHours, 2);

			/*switch ($output) {
				case 'decimal time':
					//Decimal Time Format
					return number_format($workHours, 2);
					break;
				
				case 'decimal minutes':
					//Decimal Minute Format
					return decimalMinutesFormat($workHours);
					break;			

				default:
					//Time Format
					return date('H:i', strtotime($workHours));
					break;		
			}*/

			/*$hh = date('H', strtotime($workHours));	
			$mm = date('i', strtotime($workHours));	
			$ss = date('s', strtotime($workHours));	
			
			$format = '%s:%s:%s';
			return sprintf( $format, $hh, $mm, $ss );*/

			return $workHours;

			//return date('H:i:s', strtotime($workHours));


		}

	}


	/*
	* Computation: Total hours
	*/

	function getTotalHours($clockingIn, $clockingOut, $hasOvertime = false, $overTime = '', $output = "") {

		$clocking['in'] = $clockingIn;
		$clocking['out'] = $clockingOut;

		if( !empty($overTime) ) {
	
		    // start by converting to seconds
		    $seconds = (int)($overTime * 3600);
		    // we're given hours, so let's get those the easy way
		    $hours = floor($overTime);
		    // since we've "calculated" hours, let's remove them from the seconds variable
		    $seconds -= $hours * 3600;
		    // calculate minutes left
		    $minutes = floor($seconds / 60);
		    // remove those from seconds as well
		    $seconds -= $minutes * 60;
		    // return the time formatted HH:MM:SS
		    //return lz($hours).":".lz($minutes).":".lz($seconds);
			
			$format = '%s:%s:%s';
			$overTime = sprintf( $format, $hours, $minutes, $seconds); 	

	   }

		//$hasOvertime	= true;
		//$overTime       = '02:00:00';
		$overTimeArr    = explode(':', $overTime);

		//getTimeDiff($clocking['out'], $clocking['in']);
		$timeArr1 = explode( ':', getTimeDiff($clocking['out'], $clocking['in']) );

		if ($timeArr1[0] !== '' && $timeArr1[1] !== '') {
			
			if (!$hasOvertime) {
				
				$totalHours = ($timeArr1[0] + $overTime) + ($timeArr1[1] / 60) + ($timeArr1[2] / 3600);				

			} else {

				$totalHours = ($timeArr1[0] + $overTimeArr[0]) + (($timeArr1[1] + $overTimeArr[1]) / 60) + (($timeArr1[2] + $overTimeArr[2]) / 3600);	
			}

			$totalHours = number_format($totalHours, 2);				

			/*switch ($output) {
				case 'decimal time':
					//Decimal Time Format
					return number_format($totalHours, 2);
					break;
				
				case 'decimal minutes':
					//Decimal Minute Format
					return decimalMinutesFormat($totalHours);
					break;			

				default:
					//Time Format
					return date('H:i', strtotime($totalHours));
					break;		
			}*/		

			/*$hh = date('H', strtotime($totalHours));	
			$mm = date('i', strtotime($totalHours));	
			$ss = date('s', strtotime($totalHours));	
			
			$format = '%s:%s:%s';
			return sprintf( $format, $hh, $mm, $ss );

			//return $totalHours;
			*/
			return $totalHours;

		}
		
	}

	function getOvertimeHours($clockingIn, $clockingOut, $shiftStart, $shiftEnd) {

	$datetime1 = new DateTime($clockingOut);
	$datetime2 = new DateTime($shiftEnd);
	$interval = $datetime1->diff($datetime2);

	//$time = $interval->format('%H:%I:%S');// (1000 * 60 * 60 * 24);
	//$time = $interval->format('%R%a days %H:%I:%S');

	$days = $interval->format('%a');
	$days = (int) $days;

	if ( $days !== 0 ) {
		
		$hhdays = ($days * 24);
		$hh = $hhdays;				

	} else {

		$hh = $interval->format('%H');				
	}

	//$hh = $interval->format('%H');
	$mm = $interval->format('%I');
	$ss = $interval->format('%S');

	//To convert time to just hours:
	$hours = $hh + ($mm / 60) + ($ss / 3600); //Used in Accouting: http://en.wikipedia.org/wiki/Decimal_time#Accounting

	//To convert time to just minutes:
	$minutes = ($hh * 60) + ($mm * 1) + ($ss / 60);
					 
	return $overtimeHours = number_format($hours, 2);

}

	//http://support.payrollhero.com/knowledge_base/topics/what-are-undertime-rules
	// Todo: Undertime Rules
	function getUnderTimeHours($clockingIn, $clockingOut, $shiftStart, $shiftEnd, $hasBreak = false, $breakTime = '', $output = "") {
		
		//Check if the employee completes his/her shift for the day		
		//if( ($clockingIn >= $shiftStart || $clockingIn <= $shiftStart) && ($clockingOut < $shiftEnd) ) {
		if( ($clockingOut < $shiftEnd) ) {

			$datetime1 = new DateTime($clockingOut);
			$datetime2 = new DateTime($shiftStart);
			$interval = $datetime1->diff($datetime2);			

			$time = $interval->format('%H:%I:%S');

			$hh = $interval->format('%H');
			$mm = $interval->format('%I');
			$ss = $interval->format('%S');

			//To convert time to just hours:
			$hours = $hh + ($mm / 60) + ($ss / 3600); //Used in Accouting: http://en.wikipedia.org/wiki/Decimal_time#Accounting

			//To convert time to just minutes:
			$minutes = ($hh * 60) + ($mm * 1) + ($ss / 60);
							 
			return $underTimeHours = number_format($hours, 2);

		}

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

		return $total;

	}

	//http://bwc.dole.gov.ph/FAQ/viewdetails.aspx?id=1
	/*function nightDifferential($percentage = '', $hourOfwork) {}*/


	/**
	*
	* TIMESHEET FUNCTIONALITY
	*
	*/

	/**
	*
	* CLOCKING: IN
	*
	*/
	function clockingStatusIn($clocking = '', $hasTodaySchedule = '', $getClockingDateTime = '',
		$status = '', $scheduleStartTime = '', $getWorkShift = '', 
		$employeeClocking = '', $employeeSummary = '', $deduction
	) {

		if ($status === 'open') {

			if(!$hasTodaySchedule) {

				if( strtotime(date('H:i', strtotime($getClockingDateTime))) === strtotime(date('H:i', strtotime($scheduleStartTime))) ) {

					$employeeClocking->time_in_1       = $getClockingDateTime;
					$employeeClocking->clocking_status = 'clock_in_1';	

				} else {

					//Clocking in early/tardiness
					//Todo: add a reason form
					$employeeClocking->time_in_1       = $getClockingDateTime;
					$employeeClocking->clocking_status = 'clock_in_1';

					$clockingIn = $getClockingDateTime;
					$startTime = $getWorkShift[0]->start_time; 				
					$getTardiness = $deduction->getTardiness($clockingIn, $startTime, '');									
					
					if( !empty($getTardiness) ) {
					
						$employeeClocking->tardiness_1 = $getTardiness;				
						$employeeSummary->lates = $getTardiness;				
					
					}			

				}

				echo 'open.io';		

				if ( $employeeClocking->save() && $employeeSummary->save() ) {
					
					return Redirect::to('/redraw/timesheet');			

				}				
				

			} else {

				//Code for the employee with schedule assign

			}
		}

		if ($status === 'clock_out_1') {

			if(!$hasTodaySchedule) {

				//Compare the schedule to the clocking in time
				//if( $getClockingDateTime >= $getWorkShift[0]->end_time ) {
				if( strtotime(date('H:i', strtotime($getClockingDateTime))) >= strtotime(date('H:i', strtotime($scheduleStartTime))) ) {						
				
					//Todo: add a reason form
					$employeeClocking->time_in_3       =   $getClockingDateTime;
					$employeeClocking->clocking_status =   'clock_in_3';				

				} else {
					
					//Todo: add a reason form
					//Add an alert box
					// delete this code:
					$employeeClocking->time_in_3       =   $getClockingDateTime;
					$employeeClocking->clocking_status =   'clock_in_3';							

				}

				if ( $employeeClocking->save() && $employeeSummary->save() ) {
					
					return Redirect::to('/redraw/timesheet');			

				}				

			} else {

				//Code for the employee with schedule assign

			}

			echo 'clock_out_1.io';					

		}


		if ($status === 'clock_out_2') {

			if(!$hasTodaySchedule) {
			
				//Compare the schedule to the clocking in time
				//if( $getClockingDateTime >= $getWorkShift[0]->end_time ) {
				if( strtotime(date('H:i', strtotime($getClockingDateTime))) >= strtotime(date('H:i', strtotime($scheduleStartTime))) ) {						
											
					//Todo: add a reason form
					$employeeClocking->time_in_3       =   $getClockingDateTime;
					$employeeClocking->clocking_status =   'clock_in_3';				

				} else {
					
					//Todo: add a reason form
					//Add an alert box
					// delete this code:
					$employeeClocking->time_in_3       =   $getClockingDateTime;
					$employeeClocking->clocking_status =   'clock_in_3';							

				}

			} else {

				//Code for the employee with schedule assign

			}

			echo 'clock_out_2.io';

			/*if ( $employeeClocking->save() && $employeeSummary->save() ) {
				
				return Redirect::to('/redraw/timesheet');			

			}*/			

		}


		if ($status === 'forgot_to_clock_out') {

			//$employeeClocking->time_in_2       =   $getClockingDateTime;
			//$employeeClocking->clocking_status =   'clock_in_2';					

			if(!$hasTodaySchedule) {
			
				//Compare the schedule to the clocking in time
				//if( date('H:i:s', strtotime($getClockingDateTime)) === $getWorkShift[0]->start_time ) {
				if( strtotime(date('H:i', strtotime($getClockingDateTime))) === strtotime(date('H:i', strtotime($scheduleStartTime))) ) {						
					
					$employeeClocking->time_in_2       =   $getClockingDateTime;
					$employeeClocking->clocking_status =   'clock_in_2';					

				} else {

					//Clocking in early/tardiness
					//Todo: add a reason form
					$employeeClocking->time_in_2       =   $getClockingDateTime;
					$employeeClocking->clocking_status =   'clock_in_2';								

				}

			} else {

				//Code for the employee with schedule assign

			}

			echo 'forgot_to_clock_out.io';

			/*if ( $employeeClocking->save() && $employeeSummary->save() ) {
				
				return Redirect::to('/redraw/timesheet');			

			}*/			

		}		

	}


	function clockingStatusInYesterday($clocking = 'in', $hasTodaySchedule = '', $getClockingDateTime = '',
		$status = '', $scheduleStartTime = '', $getWorkShift = '', 
		$employeeClocking = '', $employeeNightDiffClocking = '', $employeeSummary = '',
		$deduction
		
	) {

		if ($status === 'open') {

			$timeInDateTime = date('G', strtotime($getClockingDateTime));

			if(!$hasTodaySchedule) {

				if( strtotime(date('H:i', strtotime($getClockingDateTime))) === strtotime(date('H:i', strtotime($scheduleStartTime))) ) {						

					if( $timeInDateTime < 6 ) { //12								
						
						$employeeClocking->time_in_3       = $getClockingDateTime;					
						$employeeClocking->clocking_status = 'clock_in_3'; //'clock_out_1';

					} else {

						$employeeClocking->time_in_1	   = $getClockingDateTime;
						$employeeClocking->clocking_status = 'clock_in_1';

						if( $employeeNightDiffClocking->clocking_status === 'yesterday_clock_out' ) {
			
							$employeeNightDiffClocking->clocking_status = 'close';
							//$employeeNightDiffClocking->save();

						}

						$clockingIn = $getClockingDateTime;
						$startTime = $getWorkShift[0]->start_time; 

						$getTardiness = $deduction->getTardiness($clockingIn, $startTime, '');									
						
						if( !empty($getTardiness) ) {
						
							$employeeClocking->tardiness_1 = $getTardiness;				
							$employeeSummary->lates = $getTardiness;
							//$employeeSummary->save();				
						
						}								

					}

					if ( $employeeClocking->save() ) {
					
						return Redirect::to('/redraw/timesheet');

					}												

				} else {								

					//Clocking in early/tardiness
					//Todo: add a reason form

					if( $timeInDateTime < 6 ) { //12								
						
						$employeeClocking->time_in_3       = $getClockingDateTime;					
						$employeeClocking->clocking_status = 'clock_in_3'; //'clock_out_1';

					} else {
						echo 'debug.io';
						$employeeClocking->time_in_1	   = $getClockingDateTime;
						$employeeClocking->clocking_status = 'clock_in_1';

						if( $employeeNightDiffClocking->clocking_status === 'yesterday_clock_out' ) {
			
							$employeeNightDiffClocking->clocking_status = 'close';
							//$employeeNightDiffClocking->save();

						}

						$clockingIn = $getClockingDateTime;
						$startTime = $getWorkShift[0]->start_time; 

						$getTardiness = $deduction->getTardiness($clockingIn, $startTime, '');									
						
						if( !empty($getTardiness) ) {
						
							$employeeClocking->tardiness_1 = $getTardiness;													
							$employeeSummary->lates = $getTardiness;
							//$employeeSummary->save();				
						
						}																

					}

					/*if ( $employeeClocking->save() ) {
					
						return Redirect::to('/redraw/timesheet');

					}*/																					

				}		

			} else {

				//Code for the employee with schedule assign

			}


			echo 'yesterday_clock_out.io';

		}

		if ($status === 'clock_out_3') {

			$employeeClocking->time_in_1		= $getClockingDateTime;					
			$employeeClocking->clocking_status = 'clock_in_1';

			$employeeNightDiffClocking->clocking_status = 'clock_out_1';
			
			$employeeNightDiffClocking->save();						

			echo 'yesterday_clock_out > clock_out_3.io';		

			/*if ( $employeeClocking->save() ) {
				
				return Redirect::to('/redraw/timesheet');

			}*/			

		}		


	}


	/**
	*
	* CLOCKING: OUT
	*
	*/

	function clockingStatusOut($clocking = '', $hasTodaySchedule = '', $getClockingDateTime = '',
		$status = '', $scheduleStartTime = '', $getWorkShift = '', 
		$employeeClocking = '', $employeeSummary = '', $deduction
	) {

	}





