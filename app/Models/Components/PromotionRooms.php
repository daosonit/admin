<?php namespace App\Models\Components;

use App\Models\Model;

class PromotionRooms extends Model {

    protected $primaryKey = 'pror_id';

    protected $table = 'promotions_hms_room';

    protected $guarded = [];

    public $timestamps = false;

    public function getRoomIdOfPromo (array $listIdPromo)
    {
        return $this->select('*')
                    ->whereIn('pror_promotion_id', $listIdPromo)
                    ->get();

    }

}
