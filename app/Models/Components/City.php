<?php namespace App\Models\Components;

use App\Models\Model;

class City extends Model {

	//
	protected $table = 'countries';

	protected $primaryKey = 'cou_id';


	public function newQuery()
    {
        return parent::newQuery()->where('cou_parent_id', '>', 0);
    }



    public function scopeActive($query)
    {
    	$query->where('cou_active', 1);
    }

    public function scopeVietNam($query)
    {
    	$query->where('cou_parent_id', 1);	
    }

    public function scopeSearchByName($query, $name)
    {
    	$query->where('cou_name', 'LIKE', '%' . $name . '%');
    }





    public function scopeIdInList($query, $ids)
    {
        if(!empty($ids)){
            $query->whereIn('cou_id', $ids);
        }
    }
}
