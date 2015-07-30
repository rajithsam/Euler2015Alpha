<?php

class Setting extends \Eloquent {
	protected $fillable = [];
	protected $table = 'employee_setting';

	public function getEmployeeSettingByEmployeeId($employeeId = '') {
       
		if( empty($employeeId) ) {

			$employeeId = Session::get('userEmployeeId');

		} else {

			$employeeId = (int) $employeeId;

		}

		return Setting::where('employee_id', '=', $employeeId)->first();       

    }   
}