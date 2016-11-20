<?php namespace App\Models;


class Menu extends Model {

	//

	protected $fillable = ['name', 'menu_group_id', 'visible_on', 'order', 'active'];



	public function menuGroup()
	{
		return $this->belongsTo('App\Models\MenuGroup');
	}



	public function menuItems()
	{
		return $this->hasMany('App\Models\MenuItem');
	}



	 /**
     * Scope a query to only include menu of a given group.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfGroup($query, $group)
    {
        return $query->where('menu_group_id', $group);
    }




    public function getVisibleRoles()
    {
    	return ($this->visible_on ? explode('|', $this->visible_on) : []);
    }
}
