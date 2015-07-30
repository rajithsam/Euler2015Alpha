<?php

class Employee extends \Eloquent {
	protected $fillable = [];
	protected $table = 'employees';


    public function getEmployeeInfoById($id) {

		return DB::table('employees')->where('id', $id)->get();					  					  		

    }	
	
    public function getAllEmployee() {

		return Employee::all();					  					  		

    }	
	
    public function getAllEmployeeByDepartment($departmentId) {
		
	}
	
}