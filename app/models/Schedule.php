<?php

class Schedule extends \Eloquent {
	protected $fillable = [];

	protected $table = 'employee_schedule';

	    public function checkSchedule($employeeId, $dayDate) {

	        //$schedule = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($dayDate))->where('start_time', '<>', '00:00:00')->get();
			$schedule = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($dayDate))->where('start_time', '<>', '')->get();	        

	        if( !empty($schedule) ) {

	            return true;

	        } else {

	            return false;

	        } 
	   
	    }

	    public function getSchedule($employeeId, $dayDate) {

	        return DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($dayDate))->get();

	    }	

	    public function getScheduleByEmployeeId($employeeId) {
	    	
	    	return DB::table('employee_schedule')->where('employee_id', $employeeId)->get();

	    }

}

