<?php namespace App\Models\Components;

use App\Models\Model;

class PromotionsOta extends Model {

    //Loai KM
    const TYPE_PROMO_EARLY      = 1;
    const TYPE_PROMO_LASTMINUTE = 2;
    const TYPE_PROMO_CUSTOM     = 4;

    //Kieu discount trong KM OTA
    const TYPE_DISCOUNT_PERCENT    = 1;
    const TYPE_DISCOUNT_MONEY      = 2;
    const TYPE_DISCOUNT_FREE_NIGHT = 4;

    protected $primaryKey = 'proh_id';

    protected $table = 'promotions_hms';

    protected $guarded = [];

    public $timestamps = false;

    public function getPromoByHotelId ($hotelID)
    {
        $data_return =  $this->select('*')
                             ->where('proh_hotel', '=', $hotelID);
        $data_return->active();
        $data_return->del();

        return $data_return->get();
    }

    public function getInfoPromo (array $params)
    {
        $field_select  = array_get($params, 'field_select', "*");
        $take          = array_get($params, 'take', '');
        $skip          = array_get($params, 'skip', '');
        $order_by_asc  = array_get($params, 'order_by_asc', '');

        $data_return = $this->select($field_select);

        if (!empty($take)) $data_return->take($take);

        if (!empty($skip)) $data_return->skip($skip);

        return $data_return->get();
    }

    public function scopeActive($query)
    {
        return $query->where('proh_active', '=', ACTIVE);
    }

    public function scopeDel($query)
    {
        return $query->where('proh_delete', '=', NO_ACTIVE);

    }

}
