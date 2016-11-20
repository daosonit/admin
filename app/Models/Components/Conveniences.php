<?php namespace App\Models\Components;

use App\Models\Model;

class Conveniences extends Model {

	protected $primaryKey = 'con_id';

	protected $table = 'conveniences';

	public function getInfoConveniences($params)
	{
		$data_return  = [];
		$field_select = array_get($params, 'field_select', "*");
		$not_in_id	  = array_get($params, 'not_in_id',[]);
		$con_type	  = array_get($params, 'con_type',[]);

		$data_return = $this->select($field_select)
                            ->where('con_delete', '=', 0)
                            ->where('con_active', '=', 1)
							->whereNotIn('con_id', $not_in_id)
							->whereIn('con_type', $con_type)
        					->get();

        return $data_return;
	}

	public function roomConveniences()
    {
    	return $this->belongsToMany('App\Models\Components\Rooms', 'rooms_conveniences', 'roc_convenience_id', 'roc_room_id');
    }
}
