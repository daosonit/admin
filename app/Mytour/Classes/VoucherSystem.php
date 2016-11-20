<?php namespace App\Mytour\Classes;

use App\Mytour\Repositories\Voucher\VoucherRepository;

use App\Mytour\Repositories\HotelBooking\HotelBookingRepository;

use App\models\Components\HotelBooking;

use App\models\Components\DealBooking;



class VoucherSystem {

	const SINGLE_CODE = 'single';

	const MULTI_CODE = 'multi';

	const DISCOUNT_GIFT_TYPE = 'gift';
	
	const DISCOUNT_MONEY_TYPE = 'money';

	const DISCOUNT_VPOINT_TYPE = 'vpoint';

	const DEFAULT_CANC_PLC 		= 'default';

	const HOTEL_CANC_PLC 		= 'by_hotel';

	const VOUCHER_CANC_PLC 		= 'by_voucher';



	private $error = ['code' => 0, 'message' => ''];

	public function __construct(VoucherRepository $voucherRepo)
	{
		$this->voucherRepo = $voucherRepo;
	}


	public function getDefaultInfo()
	{
		return [
			'apply' => false,
			'code' => '',
			'type' => static::SINGLE_CODE,
			'discount_type' => static::DISCOUNT_MONEY_TYPE,
			'discount' => 0,
			'error_code' => $this->error['code'],
			// 'error_msg' => $this->error['message'],
			// '000' => ,
			// '000' => ,
			// '000' => ,
			// '000' => ,
			// '000' => ,
			// '000' => ,
			// '000' => ,
			// '000' => ,
		];
	}




	public function applyVoucher($booking)
	{
		$result 	 = $this->getDefaultInfo();
		$bookingInfo = $this->getBookingInfo($booking);

		dd($bookingInfo);

	}


	protected function getBookingInfo($booking)
	{
	// dd($booking);
		$info = [];
		if ($booking instanceof HotelBooking || $booking instanceof DealBooking) {

			$info['hotel_id'] = intval($booking->{$booking->fieldPrefix . 'hotel'});

			if ($booking instanceof DealBooking) {
				$info['hotel_id'] = intval($booking->{$booking->fieldPrefix . 'hotel_id'});
			} 
			
			$info['voucher_code'] 	= strval($booking->{$booking->fieldPrefix . 'voucher_code'});
			$info['time_checkin'] 	= intval($booking->{$booking->fieldPrefix . 'time_start'});
			$info['time_checkout'] 	= intval($booking->{$booking->fieldPrefix . 'time_finish'});
			$info['time_book'] 		= intval($booking->{$booking->fieldPrefix . 'time_book'});
			$info['total_money']	= doubleval($booking->getBookingMoney());
			$info['customer_email'] = strval($booking->{$booking->fieldPrefix . 'customer_email'});
			$info['customer_phone'] = strval($booking->{$booking->fieldPrefix . 'customer_phone'});
			$info['user_id'] 		= intval($booking->{$booking->fieldPrefix . 'user_id'});

		} else {
			throw new \App\Mytour\Exceptions\VoucherParamException;
		}
		return $info;
	}


	protected function getVoucherByCode($code)
	{
		return $this->voucherRepo->findBy('code', $code);
	}




	protected function validateVoucher()
	{

	}




	/**
	 * 
	 */

	public static function getCancellationPolicys()
	{
		return [

		];
	}






}