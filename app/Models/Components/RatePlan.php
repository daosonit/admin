<?php namespace App\Models\Components;

use App\Models\Model;
use Event, DB;

class RatePlan extends Model {

    const TYPE_PRICE_TA  = 0;
    const TYPE_PRICE_OTA = 1;

    protected $primaryKey = 'rap_id';

    protected $table = 'rate_plans';

    protected $guarded = [];

    public $timestamps = false;

    //Relationship Vs Table Rooms (Many - Many)
    public function rooms() {
        return $this->belongsToMany('App\Models\Components\Rooms', 'rooms_rate_plans', 'rrp_rate_plan_id', 'rrp_room_id');
    }

    //Relationship Vs Table rooms_rate_plans (One - Many)
    public function roomRates()
    {
        return $this->hasMany('App\Models\Components\RoomsRatePlans', 'rrp_rate_plan_id');
    }

    //Relationship Vs Table room_price (One - Many)
    public function roomPrice()
    {
        return $this->hasMany('App\Models\Components\RoomPrice');
    }

    public function getRatePlanByHotelId ($params)
    {
        $data_return = [];

        $field_select = array_get($params, 'field_select', "*");
        $hotelID      = array_get($params, 'hotel_id', 0);
        $active       = array_get($params, 'active', 0);

            $data_return = $this->select($field_select)
                                ->where('rap_hotel_id', '=', $hotelID)
                                ->where('rap_delete', '=', NO_ACTIVE);

            if ($active > 0) {
                $data_return->where('rap_active', '=', ACTIVE);
            }

        return $data_return->get();
    }

    public function getRatePlanListingByHotelId ($params) {
        $data_return = [];

        $field_select = array_get($params, 'field_select', "*");
        $hotelID      = array_get($params, 'hotel_id', 0);

        if ($hotelID  > 0) {
            $data_return = $this->select($field_select)
                                ->where('rap_hotel_id', '=', $hotelID)
                                ->where('rap_parent_id', '=', NO_ACTIVE)
                                ->where('rap_delete', '=', NO_ACTIVE)
                                ->get();
            }

        return $data_return;
    }

    public function getRatePlanPmsListingByHotelId ($params) {
        $data_return = [];

        $field_select = array_get($params, 'field_select', "*");
        $hotelID      = array_get($params, 'hotel_id', 0);

        if ($hotelID  > 0) {
            $data_return = $this->select($field_select)
                                ->where('rap_hotel_id', '=', $hotelID)
                                ->where('rap_parent_id', '=', NO_ACTIVE)
                                ->where('rap_delete', '=', NO_ACTIVE)
                                ->where(function($query)
                                {
                                    $query->where('rap_type_price', '=', 0)
                                          ->orWhere('rap_pms_id', '>', 0);
                                })
                                ->get();
            }

        return $data_return;
    }

    public function insertRatePlan($params)
    {
        $rap_hotel_id            = $params['rap_hotel_id'];
        $rap_title               = $params['rap_title'];
        $rap_room_apply_id       = $params['rap_room_apply_id'];
        $rap_type_price          = $params['rap_type_price'];
        $rap_surcharge_info      = $params['rap_surcharge_info'];
        $rap_accompanied_service = $params['rap_accompanied_service'];
        $rap_cancel_policy_info  = $params['rap_cancel_policy_info'];
        $rap_room_apply_bed      = $params['rap_room_apply_bed'];

        $this->rap_hotel_id            = $rap_hotel_id;
        $this->rap_title               = $rap_title;
        $this->rap_room_apply_id       = $rap_room_apply_id;
        $this->rap_type_price          = $rap_type_price;
        $this->rap_surcharge_info      = $rap_surcharge_info;
        $this->rap_accompanied_service = $rap_accompanied_service;
        $this->rap_cancel_policy_info  = $rap_cancel_policy_info;
        $this->rap_room_apply_bed      = $rap_room_apply_bed;

        DB::enableQueryLog();

        $this->save();

        return $this;
    }

    public function updateRatePlanById($params)
    {
        $rateID      = array_get($params, 'rate_id', 0);
        $data_update = array_get($params, 'data_update', "");

        if (!empty($data_update)) {

            DB::enableQueryLog();

            return $this->where('rap_id', '=', $rateID)
                        ->update($data_update);
        }

        return false;
    }

    /**
     * [insertRatePlanReturnId description]
     * insert status allotment by id room
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function insertRatePlanReturnId ($data_insert)
    {
        if (!empty($data_insert)) {
            return $this->insertGetId($data_insert);
        }

        return false;
    }

    public function getInfoRatePlanByIdRatePms($params)
    {
        $data_return = [];

        $field_select = array_get($params, 'field_select', "*");
        $hotelId      = array_get($params, 'hotelId', 0);
        $pmsId        = array_get($params, 'pmsId', 0);

        if($pmsId > 0 && $hotelId > 0) {
            $data_return = $this->select($field_select)
                                ->where('rap_hotel_id', '=', $hotelId)
                                ->where('rap_pms_id', '=', $pmsId)
                                ->get();
        }

        return $data_return;
    }
}
