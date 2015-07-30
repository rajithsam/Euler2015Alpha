<?php

class Deduction extends \Eloquent {
	protected $fillable = [];

	protected $table = '';

	//http://php.net/manual/en/dateinterval.format.php
	//http://php.net/manual/en/datetime.diff.php
	//http://www.calculatorsoup.com/calculators/time/time-to-decimal-calculator.php
	//Used in Accouting: http://en.wikipedia.org/wiki/Decimal_time#Accounting

	public function getTardiness($clockingIn, $shiftStart, $output = "") {

		//$output: "time", "decimal time", "decimal minutes"
		//Compare Clocking in time from today schedule		

		$clockingIn = date('H:i:s', strtotime($clockingIn));

		if( strtotime($clockingIn) > strtotime($shiftStart) ) {
	
			$datetime1 = new DateTime($clockingIn);
			$datetime2 = new DateTime($shiftStart);
			$interval = $datetime1->diff($datetime2);		

			$time = $interval->format('%H:%I:%S');

			$hh = $interval->format('%H');
			$mm = $interval->format('%I');
			$ss = $interval->format('%S');

			//Hours  conversion
			$hours = $hh + ($mm / 60) + ($ss / 3600); 

			//Minutes conversion
			$minutes = ($hh * 60) + ($mm * 1) + ($ss / 60);
							 
			$tardiness = number_format($hours, 2);

			return $tardiness;
	
		}

	}
	
}