<?php

class AdminCutoff extends \Eloquent {	
	protected $fillable = [];
	protected $table = 'cutoffs';

	public function getCutoffbyYearMonth() {

		//date_default_timezone_set('Asia/Manila');
		
		$getMonth  = date("M");	//01 through 12
		$getYear    = date("Y"); //01 to 31	

		return $this->where('year', $getYear)->where('month', ucfirst($getMonth))->first();

	}
}