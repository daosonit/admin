<?php namespace App\Models\Components;

use App\Models\Model;
use App\Models\GlobalScopes\RoomScope;
use DB;

class Rooms extends Model {



	protected $primaryKey = 'rom_id';

    protected $table = 'rooms';

    public $timestamps = false;

    //relationship many to many rateplans
    public function ratePlans()
    {
        return $this->belongsToMany('App\Models\Components\RatePlan', 'rooms_rate_plans', 'rrp_room_id', 'rrp_rate_plan_id');
    }

    //Relationship Vs Table rooms_rate_plans (One - Many)
    public function roomRates()
    {
        return $this->hasMany('App\Models\Components\RoomsRatePlans', 'rrp_room_id');
    }

    //relationship many to many rateplans
    public function promoRatePlanRoom()
    {
        return $this->hasMany('App\Models\Components\PromotionRoomRatePlan', 'proa_room_id', 'rom_id');
    }

    //relationship many to many promotions_new
    public function promotionsTa()
    {
        return $this->belongsToMany('App\Models\Components\Promotion', 'promotions_new_room', 'pror_room_id', 'pror_promotion_id');
    }

    //relationship many to many promotions_new
    public function promotionsOta()
    {
        return $this->belongsToMany('App\Models\Components\PromotionsOta', 'promotions_hms_room', 'pror_room_id', 'pror_promotion_id');
    }

    public function conveniences()
    {
        return $this->belongsToMany('App\Models\Components\Conveniences', 'rooms_conveniences', 'roc_room_id', 'roc_convenience_id');
    }

    public function hotel()
    {
        return $this->belongsTo('App\Models\Components\Hotel', 'rom_hotel');
    }

    /**
     * Lấy thông tin phòng theo ID khách sạn
     * @param  array $hotelId
     * @return
     */
    public function getRoomInfoByHotelId($hotelId, $params = [])
    {
        $field_select = array_get($params, 'field_select', '*');
        $active       = array_get($params, 'active', 0);


        $data_return = $this->select($field_select)
                            ->where('rom_hotel', '=', $hotelId)
                            ->where('rom_delete', '=', NO_ACTIVE);

        if ($active > 0) {
            $data_return->where('rom_active', '=', ACTIVE);
        }

        return $data_return->get();
    }

    /**
     * Lấy thông tin phòng theo danh sách ID phòng
     * @param  array $hotelId
     * @return
     */
    public function getRoomInfoByRoomId($params = [])
    {
        $field_select = array_get($params, 'field_select', '*');
        $roomIds      = array_get($params, 'room_ids', []);

        $data_return = $this->select($field_select)
                            ->whereIn('rom_id', $roomIds)
                            ->get();

        return $data_return;
    }

    /**
     * Lấy thông tin tất cả phòng theo ID khách sạn
     * @param  array $hotelId
     * @return
     */
    public function getAllRoomInfoByHotelId($params = [])
    {
        $field_select = array_get($params, 'field_select', '*');
        $hotelId      = array_get($params, 'rom_hotel', 0);

        $data_return = $this->select($field_select)
                            ->where('rom_hotel', '=', $hotelId)
                            ->where('rom_delete', '=', NO_ACTIVE)
                            ->get();

        return $data_return;
    }

    /**
     * getRoomInfoByListHotelId
     * Lấy thông tin phòng theo List ID khách sạn
     * @param  array $hotelId
     * @return
     */
    public function getRoomInfoByListHotelId(array $hotelId, $params = [])
    {
        $field_select = array_get($params, 'field_select', '*');

        $data_return = $this->select($field_select)
                            ->whereIn('rom_hotel', $hotelId)
                            ->where('rom_active', '=', ACTIVE)
                            ->get();

        return $data_return;
    }


    public function insertRoom($params)
    {
        $rom_hotel        = $params['rom_hotel'];
        $rom_name         = $params['rom_name'];
        $rom_stock        = $params['rom_stock'];
        $rom_area         = $params['rom_area'];
        $rom_person       = $params['rom_person'];
        $rom_children     = $params['rom_children'];
        $rom_smoke        = $params['rom_smoke'];
        $rom_info_bed     = $params['rom_info_bed'];
        $rom_exchange_bed = $params['rom_exchange_bed'];
        $rom_trend        = $params['rom_trend'];

        $this->rom_hotel        = $rom_hotel;
        $this->rom_name         = $rom_name;
        $this->rom_stock        = $rom_stock;
        $this->rom_area         = $rom_area;
        $this->rom_person       = $rom_person;
        $this->rom_children     = $rom_children;
        $this->rom_smoke        = $rom_smoke;
        $this->rom_info_bed     = $rom_info_bed;
        $this->rom_exchange_bed = $rom_exchange_bed;
        $this->rom_trend        = $rom_trend;

        DB::enableQueryLog();

        $this->save();

        return $this;
    }


     /**
     * Lấy thông tin tất cả phòng
     * @param  array $hotelId
     * @return
     */
    public function getInfoRooms($params)
    {
        $field_select = array_get($params, 'field_select', '*');
        $group_by     = array_get($params, 'group_by');
        $order_by     = array_get($params, 'order_by');
        $take         = array_get($params, 'take');
        $skip         = array_get($params, 'skip');

        $query = $this->select($field_select);
        $data = $query->active();
        if(!empty($order_by)) $data = $query->orderBy($order_by, 'asc');
        if(!empty($group_by)) $data = $query->groupBy($group_by);
        if(!empty($take))     $data = $query->take($take);
        if(!empty($skip))     $data = $query->skip($skip);

        $data = $query->with('ratePlans');

        $data = $query->get();

        return $data;
    }

    public function scopeActive($query)
    {
        return $query->where('rom_delete', '=', NO_ACTIVE)
                     ->where('rom_active', '=', ACTIVE);

    }

    public function newQuery()
    {
        return parent::newQuery()->where('rom_delete', '=', 0);
    }

}
