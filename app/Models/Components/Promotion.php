<?php namespace App\Models\Components;

use App\Models\Model;

class Promotion extends Model {

    //Kieu KM
    const TYPE_PROMO_TA  = 0;
    const TYPE_PROMO_OTA = 1;

    //Loai KM
    const TYPE_PROMO_EARLY      = 1;
    const TYPE_PROMO_LASTMINUTE = 2;
    const TYPE_PROMO_CUSTOM     = 3;

    //Kieu discount trong KM OTA
    const TYPE_DISCOUNT_PERCENT    = 1;
    const TYPE_DISCOUNT_MONEY      = 2;
    const TYPE_DISCOUNT_FREE_NIGHT = 4;

    protected $primaryKey = 'proh_id';

    protected $table = 'promotions_new';

    protected $guarded = [];

    protected $casts = [
        'proh_promotion_info' => 'collection',
        'proh_day_deny'       => 'collection',
        'proh_day_apply'      => 'collection',
    ];

    public $timestamps = false;

    public function roomRates ()
    {
        return $this->hasMany('App\Models\Components\RoomRatePromotion', 'rapr_promotion_id');
    }

    //Relationship vs Table rooms_rate_plans (Many - Many)
    public function roomRatePlans ()
    {
        return $this->belongsToMany('App\Models\Components\RoomsRatePlans', 'room_rate_promotion', 'rapr_promotion_id', 'rapr_room_rate_id');
    }

    public function getPromoByHotelId ($hotelID)
    {
        $data_return =  $this->select('*')
                             ->where('proh_hotel', '=', $hotelID);
        $data_return->active();
        $data_return->del();

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
