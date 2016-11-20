<?php namespace App\Models\Components;

use App\Models\Model;
use Event,DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Mytour\Traits\HotelActived;
use Config;

class Hotel extends Model {

    use SoftDeletes;
    // use HotelActived; Global scope Hotel Active 

	protected $primaryKey = 'hot_id';

	protected $table = 'hotels';

    public $timestamps = false;

    const TYPE_HOTEL_TA     = 0;
    const TYPE_HOTEL_OTA    = 1;
    const TYPE_HOTEL_HYBRID = 2;

    /**
     * Get the price room of rate plan
     */
    public function rooms()
    {
        return $this->hasMany('App\Models\Components\Rooms', 'rom_hotel', 'hot_id');
    }

    public function getInfoHotelById ($params)
    {
        $field_select = array_get($params, 'field_select', "*");
        $hotelID      = array_get($params, 'hotel_id', 0);

        return $this->select($field_select)
                    ->where('hot_id', '=', $hotelID)
                    ->where('hot_active', '=', ACTIVE)
                    ->first();

    }

    public function getInfoHotel ($params)
    {
        $field_select  = array_get($params, 'field_select', "*");
        $take          = array_get($params, 'take', '');
        $skip          = array_get($params, 'skip', '');
        $order_by_asc  = array_get($params, 'order_by_asc', '');

        $data_return = $this->select($field_select)
                            ->where('hot_active', '=', ACTIVE)
                            ->where('hot_country', '=', ACTIVE);

        if (!empty($order_by_asc)) $data_return->orderBy($order_by_asc, 'ASC');

        if (!empty($order_by_desc)) $data_return->orderBy($order_by_desc, 'DESC');

        if (!empty($take)) $data_return->take($take);

        if (!empty($skip)) $data_return->skip($skip);

        return $data_return->get();

    }

    public function updateInfoHotelById ($params)
    {
        $hotelID    = array_get($params, 'hotel_id', 0);
        $dataUpdate = array_get($params, 'data_update', "");

        DB::enableQueryLog();

        return $this->where('hot_id', '=', $hotelID)
                    ->update($dataUpdate);

    }



    public function scopeActive($query)
    {
        $query->where('hot_active', ACTIVE);
    }



    public function scopeSeachByName($query, $name)
    {
        $query->where('hot_name_temp', 'LIKE', '%' . $name . '%');
    }


    public function scopeIdInList($query, array $ids)
    {
        if(!empty($ids)){
            $query->whereIn('hot_id', $ids);
        }
    }



    public function getUrl()
    {
        return route('hotel-detail-site', ['hotelID' => $this->hot_id, 'str_slug_name' => str_slug($this->hot_name_temp)]);
    }

    public function getImageUrl()
    {
        
    }


    public function city()
    {
        return $this->belongsTo('App\Models\Components\City', 'hot_city', 'cou_id');
    }

}
