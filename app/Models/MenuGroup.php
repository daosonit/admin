<?php namespace App\Models;


class MenuGroup extends Model {

	protected $fillable = ['name', 'description', 'visible_on', 'icon', 'order', 'active'];


	public function menus()
	{
		return $this->hasMany('App\Models\Menu');
	}



	public function menuItems()
	{
		return $this->hasManyThrough('App\Models\MenuItem', 'App\Models\Menu');
	}




	public function scopeActived($query)
	{
		return $query->where('active', 1);
	}


	public function getIcon()
	{
		return ($this->icon ? $this->icon : 'fa-share');
	}



	public function getVisibleRoles()
	{
		return !empty($this->visible_on) ? explode('|', $this->visible_on) : [];
	}

}
