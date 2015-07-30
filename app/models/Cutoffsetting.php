<?php

class Cutoffsetting extends \Eloquent {
	protected $fillable = [];
	protected $table = 'cutoff_setting';


	public function getAllCutoffSetting() {

		return Cutoffsetting::all();

		//return DB::table('cutoff_setting')->get();

	}
}