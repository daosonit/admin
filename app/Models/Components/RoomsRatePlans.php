<?php namespace App\Models\Components;

use App\Models\Model;

class RoomsRatePlans extends Model {

	protected $primaryKey = 'rrp_id';

    protected $table = 'rooms_rate_plans';

    public $timestamps = false;

    //Relationship vs Table room_rate_promotion (One - Many)
    public function roomRatePromo ()
    {
        return $this->hasMany('App\Models\Components\RoomRatePromotion', 'rapr_room_rate_id');
    }

    //Relationship vs Table rate_plans (One - Many)
    public function ratePlans ()
    {
        return $this->belongsTo('App\Models\Components\RatePlan', 'rrp_rate_plan_id');
    }

    //Relationship vs Table rooms (One - Many)
    public function rooms ()
    {
        return $this->belongsTo('App\Models\Components\Rooms', 'rrp_room_id');
    }

    //Relationship vs Table room_price (One - Many)
    public function roomPrice ()
    {
        return $this->hasMany('App\Models\Components\RoomPrice');
    }

    //Relationship vs Table promotions_new (Many - Many)
    public function promotions ()
    {
        return $this->belongsToMany('App\Models\Components\Promotion', 'room_rate_promotion', 'rapr_room_rate_id', 'rapr_promotion_id');
    }

    public function insertRoomsRatePlans($params)
    {
		return $this->insert($params);
    }

    public function getRateIdIdByRoomId($params)
    {
        $field_select = array_get($params, 'field_select', '*');
        $room_id = array_get($params, 'room_id', 0);

        return $this->select($field_select)
                    ->where('rrp_room_id', '=', $room_id)
                    ->get();

    }

}
