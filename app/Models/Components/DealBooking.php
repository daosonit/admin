<?php namespace App\Models\Components;

use App\Models\Model;
use App\Mytour\Traits\Booking as BookingMethods;

class DealBooking extends MytourModel {

	use BookingMethods ;

	protected $primaryKey = 'bod_id';

	protected $table = 'booking_deal';

	public $fieldPrefix = 'bod_';





	public function hotel()
	{
		return $this->belongsTo('App\Models\Components\Hotel', 'bod_hotel_id');
	}

	
	


}
