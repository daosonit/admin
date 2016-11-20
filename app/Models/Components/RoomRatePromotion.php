<?php namespace App\Models\Components;

use App\Models\Model;

class RoomRatePromotion extends Model {

	protected $primaryKey = 'rapr_id';

    protected $table = 'room_rate_promotion';

    public $timestamps = false;

    public function promotion ()
    {
        return $this->belongsTo('App\Models\Components\Promotion', 'rapr_promotion_id');
    }

    public function roomRates ()
    {
        return $this->belongsTo('App\Models\Components\RoomsRatePlans', 'rapr_room_rate_id');
    }

}
