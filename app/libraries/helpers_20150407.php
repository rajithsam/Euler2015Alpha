<?php

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
	function decimalTimeToTimeFormat($decimalTime, $format = 'H:i:s') {

		$decimalTimeArr = explode('.', number_format($decimalTime, 2));	

		$hh = $decimalTimeArr[0];

		$hours = abs($decimalTime) - floor(abs($decimalTime));
		$mm = floor($hours * 60);
		$ss = abs($hours * 60) - floor($hours * 60);

		return $timeFormat = date('H:i:s', strtotime(sprintf('%s:%s:%s', $hh, $mm, $ss)));

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

	function timeToDecimal($time) { //Not use

	    $timeArr = explode(':', $time);
	    $decTime = ($timeArr[0] * 60) + ($timeArr[1]) + ($timeArr[2] / 60);
	 			   
	    return $decTime;

	}

	function decimalToTime($decimal) { //Not use

		$hours = floor((int) $decimal / 60);
	    $minutes = floor((int) $decimal % 60);
	    $seconds = $decimal - (int) $decimal; 
	    $seconds = round($seconds * 60);
	 
	    return str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" . str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT);

	}

	function timeToMinutes($time) { //Not use

		$timeArr = explode(':', $time);

		$hh = $timeArr[0] * (60 / 1);
		$mm = $timeArr[1] * (1 / 1);
		$ss = $timeArr[2] * (1 / 60);

		return $hh = $hh + $mm + $ss;
		
	}


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


		if ($timeArr1[0] !== '00' && $timeArr1[1] !== '00') {
			if (!$hasBreak) {

				$workHours = ($timeArr1[0]) + ($timeArr1[1] / 60) + ($timeArr1[2] / 3600);		
				$workHours = number_format($workHours, 2);					

				/*
				echo 'Decimal Time Format: '. number_format($workHours, 2);	
				echo '<br />';
				echo 'Decimal Minute Format: '. decimalMinutesFormat($workHours); //decimalMinutesFormat(decimalTimeToTimeFormat($workHours));
				echo '<br />';	
				echo 'Time Format: '. date('H:i', strtotime($workHours)); //date('H:i', strtotime(decimalTimeToTimeFormat($workHours)));
				*/

				switch ($output) {
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
				}			
				
			} else {

				$workHours = ($timeArr1[0] - $breakTimeArr[0]) + (($timeArr1[1] + $breakTimeArr[1]) / 60) + (($timeArr1[2] + $breakTimeArr[2]) / 3600);											
				$workHours = number_format($workHours, 2);

				/*echo 'Decimal Time Format: '. number_format($workHours, 2);
				echo '<br />';
				echo 'Decimal Minute Format: '. decimalMinutesFormat($workHours); //decimalMinutesFormat(decimalTimeToTimeFormat($workHours));		
				echo '<br />';				
				echo 'Time Format: '. date('H:i', strtotime($workHours)); //date('H:i', strtotime(decimalTimeToTimeFormat($workHours)));*/

				switch ($output) {
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
				}			
				
			}

		}

	}


	/*
	* Computation: Total hours
	*/

	function getTotalHours($clockingIn, $clockingOut, $hasOvertime = false, $overTime = '', $output = "") {

	$clocking['in'] = $clockingIn;
	$clocking['out'] = $clockingOut;

	//$hasOvertime	= true;
	//$overTime       = '02:00:00';
	$overTimeArr    = explode(':', $overTime);

	getTimeDiff($clocking['out'], $clocking['in']);
	$timeArr1 = explode( ':', getTimeDiff($clocking['out'], $clocking['in']) );

		if ($timeArr1[0] !== '00' && $timeArr1[1] !== '00') {
			
			if (!$hasOvertime) {
				
				$totalHours = ($timeArr1[0] + $overTime) + ($timeArr1[1] / 60) + ($timeArr1[2] / 3600);				
				$totalHours = number_format($totalHours, 2);	

				/*
				echo 'Decimal Time Format: '. number_format($totalHours, 2);	
				echo '<br />';
				echo 'Decimal Minute Format: '. decimalMinutesFormat(decimalTimeToTimeFormat($totalHours));		
				echo '<br />';	
				echo 'Time Format: '. date('H:i', strtotime(decimalTimeToTimeFormat($totalHours)));
				*/

				switch ($output) {
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
				}			


			} else {


				$totalHours = ($timeArr1[0] + $overTimeArr[0]) + (($timeArr1[1] + $overTimeArr[1]) / 60) + (($timeArr1[2] + $overTimeArr[2]) / 3600);	
				$totalHours = number_format($totalHours, 2);				
				
				/*
				echo 'Decimal Time: '. number_format($totalHours, 2);	
				echo '<br />';
				echo 'Decimal Minute Format: '. decimalMinutesFormat(decimalTimeToTimeFormat($totalHours));							
				echo '<br />';	
				echo 'Time Format: '. date('H:i', strtotime(decimalTimeToTimeFormat($totalHours)));	
				*/

				switch ($output) {
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
				}					
			}

		}
		
	}

	function getTotalHoursTotal($totalHours1 = 0.00, $totalHours2 = 0.00, $totalHours3 = 0.00) {

		/*$totalHours1 = (double) $totalHours1;
		$totalHours2 = (double) $totalHours2;
		$totalHours3 = (double) $totalHours3;

		if ( $totalHours1 !== 0.00 && $totalHours2 !== 0.00  && $totalHours3 !== 0.00 ) {
			
			return $totalHours = $totalHours1 + $totalHours2 + $totalHours3;

		} elseif ( $totalHours1 !== 0.00 && $totalHours2 !== 0.00  && $totalHours3 === 0.00 ) {

			return $totalHours = $totalHours1 + $totalHours2;
		
		} elseif ( $totalHours1 !== 0.00 && $totalHours2 === 0.00  && $totalHours3 === 0.00 ) {

			return $totalHours = $totalHours1;

		}*/		

		$totalHours = (double) $totalHours1 + (double) $totalHours2 + (double) $totalHours3;
		return $totalHours;		

	}

	function getWorkHoursTotal($workHours1 = 0.00, $workHours2 = 0.00, $workHours3 = 0.00) {

		/*$workHours1 = (double) $workHours1;
		$workHours2 = (double) $workHours2;
		$workHours3 = (double) $workHours3;

		if ( $workHours1 !== 0.00 && $workHours2 !== 0.00  && $workHours3 !== 0.00 ) {
			
			return $workHours = $workHours1 + $workHours2 + $workHours3;

		} elseif ( $workHours1 !== 0.00 && $workHours2 !== 0.00  && $workHours3 === 0.00 ) {

			return $workHours = $workHours1 + $workHours2;
		
		} elseif ( $workHours1 !== 0.00 && $workHours2 === 0.00  && $workHours3 === 0.00 ) {

			return $workHours = $workHours1;

		}*/

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

	function getDecimalTimeFormat($decimalTime) {

		if( $decimalTime != 0 || $decimalTime != 0.00 ) {
		
			//date( 'H:i:s', strtotime($decimalTime) );

			$hh = date( 'H', strtotime($decimalTime) );
			$mm = date( 'i', strtotime($decimalTime) );
			$ss = date( 's', strtotime($decimalTime) );
				
			//return $hh .' h'. $mm .' m';
		
			$format = '%s h %s m';
			return sprintf( $format, $hh, $mm );
				
		} else {

			return '0.00';

		}

	}

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
