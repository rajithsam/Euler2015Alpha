<?php

class Overtime extends \Eloquent {
	protected $fillable = [];

    protected $table = 'overtime';	


	public function belongsToTimesheet() {

		return $this->belongsTo('Timesheet');

	}

}