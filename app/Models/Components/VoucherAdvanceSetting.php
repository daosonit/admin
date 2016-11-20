<?php namespace App\Models\Components;

use App\Models\Model;

class VoucherAdvanceSetting extends Model {

	//
	protected $table = 'voucher_advance_settings';


	protected $fillable = ['booking_money_min', 'booking_money_max', 'hotel_accepted_apply', 'hotel_city_apply', 
						   'customer_first_booking', 'customer_old', 'customer_logged_in', 'customer_is_partner',
						   'hotel_accepted_apply', 'hotel_star_rate_apply', 'customer_logged_in'];

	protected $casts = [
		'hotel_city_apply' => 'collection',
      	'hotel_accepted_apply' => 'collection',
      	'hotel_star_rate_apply' => 'collection'
    ];



	public function voucher()
	{
		return $this->belongsTo('App\Models\Components\Voucher');
	}

}
