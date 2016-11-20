<?php

namespace App\Models\Components;

use App\Mytour\Traits\Booking as BookingMethods;
use App\Models\Model;
use App\Mytour\Contracts\MytourBooking;
use App\Mytour\Contracts\MytourHotelBooking;
use Carbon\Carbon;

class HotelBooking extends Model implements MytourBooking, MytourHotelBooking {

	use BookingMethods;

	protected $primaryKey = 'boo_id';

	protected $table = 'booking_hotel';

	public $fieldPrefix = 'boo_';

	public $timestamps = false;


	private function getAppTimeZone()
	{
		return config('app.timezone');
	}


	public function hotel()
	{
		return $this->belongsTo('App\Models\Components\Hotel', 'boo_hotel', 'hot_id');
	}


	/**
	 * trả về tổng số tiền của booking
	 *
	 * @return integer
	 */
	public function getBookingMoney()
	{
		return intval(
			$this->boo_total_money +
			$this->boo_money_discount +
			$this->boo_money_discount_vpoint +
			$this->boo_partner_money_discount +
		  	$this->boo_extra_money
		);
	}

	/**
	 * trả về số tiền thực tế phải thanh toán của khách hàng cho booking này
	 *
	 * @return integer
	 */
	public function getTotalMoney()
	{
		return intval(
			$this->boo_total_money
		);
	}

	/**
	 * trả về số tiền phải thanh toán cho KS
	 *
	 * @return integer
	 */
	public function getSupplierMoney()
	{
		return intval(
			$this->boo_supplier_money
		);
	}

	/**
	 * trả về trạng thái hiện tại của booking
	 *
	 * @return integer
	 */
	public function getBookingStatus()
	{
		return intval(
			$this->boo_view
		);
	}

	/**
	 * trả về thời gian checkin của booking
	 *
	 * @return Carbon/Carbon
	 */
	public function getDateTimeCheckin()
	{
		return  $this->boo_time_start;
	}

	/**
	 * trả về thời gian checkout của booking
	 *
	 * @return Carbon/Carbon
	 */
	public function getDateTimeCheckout()
	{
		return  $this->boo_time_finish;
	}

	/**
	 * trả về thời gian đặt của booking
	 *
	 * @return Carbon/Carbon
	 */
	public function getDateTimeBook()
	{
		return  $this->boo_time_book;
	}

	/**
	 * trả về thông tin khách hàng
	 *
	 * @return array
	 */
	public function getCustomerInfo()
	{
		return [
			'name' => $this->boo_customer_name,
			'email' => $this->boo_customer_email,
			'phone' => $this->boo_customer_phone,
			'city' => $this->boo_customer_city,
			'address' => $this->boo_customer_address
		];
	}

	/**
	 * trả tổng số tiền khuyến mãi của booking
	 *
	 * @return integer
	 */
	public function getDiscountMoney()
	{
		return intval(
			$this->boo_money_discount +
			$this->boo_money_discount_vpoint +
			$this->boo_money_discount_bank +
			$this->boo_partner_money_discount
		);
	}

	/**
	 * lấy ra tiền phụ thu của booking
	 *
	 * @return integer
	 */
	public function getExtraMoney()
	{
		return intval(
			$this->boo_extra_money
		);
	}

	public function setStatus($status)
	{

	}

	/**
	 * định nghĩa relation giữa booking model và city model
	 *
	 * @return Illuminate\Database\Eloquent\Relations\BelongTo
	 */
	public function city()
	{
		return $this->belongsTo('App\Models\Components\City', 'boo_customer_city', 'cou_id');
	}

	/**
	 * Mutator set value of boo_time_start
	 *
	 * @param Carbon/Carbon $value
	 */
	public function setBooTimeBookAttribute($value)
	{
		$this->attributes['boo_time_book'] = $value->timestamps;
	}

	/**
	 * Accessor get value of boo_time_book
	 *
	 * @param Carbon/Carbon $value
	 */
	public function getBooTimeBookAttribute()
	{
		return Carbon::createFromTimestamp($this->attributes['boo_time_book'], $this->getAppTimeZone());
	}

	/**
	 * Mutator set value of boo_time_start
	 *
	 * @param Carbon/Carbon $value
	 */
	public function setBooTimeStartAttribute($value)
	{
		$this->attributes['boo_time_start'] = $value->timestamps;
	}

	/**
	 * Accessor get value of boo_time_start
	 *
	 * @param Carbon/Carbon $value
	 */
	public function getBooTimeStartAttribute()
	{
		return Carbon::createFromTimestamp($this->attributes['boo_time_start'], $this->getAppTimeZone());
	}

	/**
	 * Mutator set value of boo_time_finish
	 *
	 * @param Carbon/Carbon $value
	 */
	public function setBooTimeFinishAttribute($value)
	{
		$this->attributes['boo_time_finish'] = $value->timestamps;
	}

	/**
	 * Accessor get value of boo_time_finish
	 *
	 * @param Carbon/Carbon $value
	 */
	public function getBooTimeFinishAttribute()
	{
		return Carbon::createFromTimestamp($this->attributes['boo_time_finish'], $this->getAppTimeZone());
	}

	/**
	 * Echo ra booking code nếu echo booking instance
	 *
	 * @return String
	 */
	public function __toString()
	{
		return $this->boo_code;
	}

	public function getInfoBookingCron($params)
	{
		$data_return = [];

        $field_select 	= array_get($params, 'field_select', "*");
        $time_start     = array_get($params, 'time_start', 0);
        $time_finish    = array_get($params, 'time_finish', 0);
        $take 			= array_get($params, 'take', 0);
        $skip 			= array_get($params, 'skip', 0);

        $data_return = $this->select($field_select)
                            ->where('boo_time_book', '>', time() - 86400)
                            ->where('boo_time_book', '<', time())
                            ->where('boo_view', '<>', 3)
                            ->Where('boo_view', '<>', 2)
                            ->orderBy('boo_id', 'ASC')
                            ->take($take)
        					->skip($skip)
                            ->get();

        return $data_return;
	}

}
