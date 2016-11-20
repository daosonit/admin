<?php namespace App\Models;


class Module extends Model {

	protected $primaryKey = 'mod_id';


	protected $table = 'modules';


	protected $menu = [];


	protected $fillable = ['mod_name', 'mod_group_id', 'mod_listfile', 'mod_listroute', 'mod_listname', 'mod_order'];

	public $timestamps = false;

	public function getMenu()
	{
		
		$nameList = explode('|', $this->mod_listname);
		$routeList = explode('|', $this->mod_listroute);

		foreach($nameList as $key => $name)
		{
			if(isset($routeList[$key]) && !empty($routeList[$key])){
				$menu['route'] = $routeList[$key];
			} else {
				$menu['route'] = 'dashboard';
			}
			$menu['name'] = $name;
			$this->menu[] = $menu;
		}
		return $this->menu;
	}



	public function moduleGroup()
	{
		return $this->belongsTo('App\Models\ModuleGroup', 'mod_group_id', 'mog_id');
	}

}
