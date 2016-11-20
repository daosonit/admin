<?php namespace App\Models\Components;

use App\Models\Model;

class PromotionRoomsTa extends Model {

    protected $primaryKey = 'pror_id';

    protected $table = 'promotions_new_room';

    protected $guarded = [];

    public $timestamps = false;

    public function getRoomIdOfPromo (array $listIdPromo)
    {
        return $this->select('*')
                    ->whereIn('pror_promotion_id', $listIdPromo)
                    ->get();

    }

}
