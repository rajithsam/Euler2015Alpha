<?php

class Holiday extends \Eloquent {
	protected $fillable = [];

	protected $table = 'holiday';

    public function getHolidayByDate($todayDate) {

        $holiday = DB::table('holiday')->where('date', trim($todayDate))->get();

        return $holiday;
    
    } 	
}