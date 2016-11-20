<?php namespace App\Models;


class MenuItem extends Model {

	protected $fillable = ['name', 'description', 'visible_on', 'route', 'menu_id', 'active'];


	public function menu()
	{
		return $this->belongsTo('App\Models\Menu');
	}

}
