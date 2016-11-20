<?php namespace App\Models;


class ModuleGroup extends Model {

	protected $primaryKey = 'mog_id';

	protected $table = 'module_group';


	public function modules()
	{
		return $this->hasMany('App\Models\Module', 'mod_group_id', 'mog_id')->orderBy('mod_name');
	}


	public function icon()
	{
		return !empty($this->mog_icon) ? $this->mog_icon : 'fa fa-share';
	}

}
