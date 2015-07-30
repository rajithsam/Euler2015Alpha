<?php
class Group extends Eloquent
{
	protected $table = 'groups';

    public function users()
    {
        return $this->hasMany('Users', 'group_name', 'id');
    }

    /*public function permissions()
    {
        //return $this->belongsTo('Permissions');
        return $this->hasMany('Permissions', '')
    }*/
}
?>


