<?php namespace App\Models\Components;

use App\Models\Model;
use DB;

class RoomPrice extends Model {

    //ti le tinh gia publish
    const RATE_TA_PRICE_PUBLISH  = 1.3;
    const RATE_OTA_PRICE_PUBLISH = 1.15;

    //Value Kieu giá phong theo so phong
    const NUM_PERSON_MIN_TYPE = 1;
    const NUM_PERSON_MAX_TYPE = 2;

	protected $primaryKey = 'rop_id';

    protected $table;

    // protected $casts = [
    //     'rop_info_price_contract'        => 'collection',
    //     'rop_info_price_publish'         => 'collection',
    //     'rop_info_price_season_contract' => 'collection'
    // ];

    public $timestamps = false;

    public function setTable($tableName)
    {
        $this->table = $tableName;
    }

    public function __construct()
    {
        parent::__construct();
        $current_date = date('Ym', time());
        $tableName    = 'room_price_' . $current_date;
        $this->setTable($tableName);
    }

    //Relationship vs Table rate_plans (One - Many)
    public function ratePlans ()
    {
        return $this->belongsTo('App\Models\Components\RatePlan', 'rop_rate_plan_id');
    }

    //Relationship vs Table rooms_rate_plans (One - Many)
    public function roomRatePlans ()
    {
        return $this->belongsTo('App\Models\Components\RatePlan', 'rop_rate_plan_id');
    }

    /**
     * get data price by hotel id
     * @param  array $listHotel
     * @param  array $params
     * @return
     */
    public function getDataByHotelId($hotelID, array $params)
    {
        $data_return = [];

        $table        = array_get($params, 'table', "");
        $field_select = array_get($params, 'field_select', "*");

        if ($table != "") {
            $this->setTable($table);

            $data_return = $this->select($field_select)
                                ->where('rop_hotel_id', '=', $hotelID)
                                ->get();
        }

        return $data_return;
    }

    /**
     * get data price by list room ID
     * @param  array $list_room
     * @param  array $params
     * @return
     */
    public function getDataByListRoomRate(array $listRoomID, array $listRateID, array $params)
    {
        $data_return = [];

        $table        = array_get($params, 'table', "");
        $field_select = array_get($params, 'field_select', "*");
        $hotel_pms    = array_get($params, 'hotel_pms', 0);

        if ($table != "") {
            $this->setTable($table);

            $data_return = $this->select($field_select)
                                ->whereIn('rop_room_id', $listRoomID)
                                ->whereIn('rop_rate_plan_id', $listRateID);

            if ($hotel_pms > 0) {
                $data_return->where('rop_price_pms', '=', ACTIVE);
            }
            return $data_return->get();
        }

        return $data_return;
    }

    /**
     * get data price by list Rate ID
     * @param  array $list_room
     * @param  array $params
     * @return
     */
    public function getDataByListRateId(array $listRateID, array $params)
    {
        $data_return = [];

        $table        = array_get($params, 'table', "");
        $field_select = array_get($params, 'field_select', "*");

        if ($table != "") {
            $this->setTable($table);

            $data_return = $this->select($field_select)
                                ->whereIn('rop_rate_plan_id', $listRateID)
                                ->get();
        }

        return $data_return;
    }

    /**
     * [getPriceByRoomRateId description]
     * get Allotment by Room ID
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function getPriceByRoomRateId ($params)
    {
        $table        = array_get($params, 'table', "");
        $field_select = array_get($params, 'field_select', '*');
        $room_id      = array_get($params, 'room_id', 0);
        $rate_plan_id = array_get($params, 'rate_plan_id', 0);
        $person_type  = array_get($params, 'person_type', 0);
        $price_type   = array_get($params, 'price_type', "");

        if ($table != "" && $room_id > 0 && $rate_plan_id > 0) {
            $this->setTable($table);

            $data_return = $this->select($field_select);

            if ($room_id > 0) {
                 $data_return->where('rop_room_id', '=', $room_id);
            }

            if ($rate_plan_id > 0) {
                 $data_return->where('rop_rate_plan_id', '=', $rate_plan_id);
            }

            if ($person_type > 0) {
                 $data_return->where('rop_person_type', '=', $person_type);
            }

            if ($price_type >= 0 && !empty($price_type)) {
                 $data_return->where('rop_type_price', '=', $price_type);
            }

            return $data_return->get();
        }

        return false;
    }

    /**
     * [insertPriceByData description]
     * insert status allotment by id room
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function insertPriceByData ($params)
    {
        $table       = array_get($params, 'table', "");
        $data_insert = array_get($params, 'data_insert', "");

        if ($table != "" && !empty($data_insert)) {
            $this->setTable($table);

            DB::enableQueryLog();

            return $this->insert($data_insert);
        }

        return false;
    }

    /**
     * [updatePriceByRoomRateId description]
     * update status allotment by id room
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function updatePriceByRoomRateId ($params)
    {
        $table        = array_get($params, 'table', "");
        $room_id      = array_get($params, 'room_id', 0);
        $rate_plan_id = array_get($params, 'rate_plan_id', 0);
        $person_type  = array_get($params, 'person_type', 0);
        $dataUpdate   = array_get($params, 'data_update', []);

        if ($table != "" && !empty($dataUpdate)) {
            $this->setTable($table);

            DB::enableQueryLog();

            return $this->where('rop_room_id', '=', $room_id)
                        ->where('rop_rate_plan_id', '=', $rate_plan_id)
                        ->where('rop_price_pms', '=', 0)
                        ->update($dataUpdate);
        }

        return false;
    }

    public function setRopInfoPriceContractAttribute ($value)
    {
        $this->attributes['rop_info_price_contract'] = json_encode(stripslashes($value), true);
    }

    public function getRopInfoPriceContractAttribute($value)
    {
        return $this->newCollection(json_decode(stripslashes($value), true));
    }

    public function setRopInfoPricePublishAttribute ($value)
    {
        $this->attributes['rop_info_price_publish'] = json_encode(stripslashes($value), true);
    }

    public function getRopInfoPricePublishAttribute($value)
    {
        return $this->newCollection(json_decode(stripslashes($value), true));
    }

    public function setRopInfoPriceSeasonContractAttribute ($value)
    {
        $this->attributes['rop_info_price_season_contract'] = json_encode(stripslashes($value), true);
    }

    public function getRopInfoPriceSeasonContractAttribute($value)
    {
        return $this->newCollection(json_decode(stripslashes($value), true));
    }

}
