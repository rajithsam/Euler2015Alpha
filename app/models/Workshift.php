<?php
class Workshift extends \Eloquent {
	protected $fillable = [];
    protected $table = 'work_shift';	

    public function checkWorkShiftSchedule($employeeId) {

        $workShift = DB::table('work_shift')->where('employee_id', $employeeId)->where('start_time', '<>', '')->get();

        if( !empty($workShift) ) {

            return true;

        } else {

            return false;

        } 
   
    }    

    public function getWorkShiftByEmployeeId($employeeId) {
    	
    	return DB::table('work_shift')->where('employee_id', $employeeId)->get();

    }
	
    public function getWorkShiftByDayOfTheWeek($employeeId, $dayOfTheWeek, $shift = 1) {
    	
    	return DB::table('work_shift')
                    ->where('employee_id', $employeeId)
                    ->where('name_of_day', $dayOfTheWeek)
                    ->where('shift', $shift)
                    ->get();

    }	
   
}