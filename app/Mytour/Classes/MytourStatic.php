<?php 
namespace App\Mytour\Classes;

/**
* 
*/
class MytourStatic 
{
	public function bookingModules()
	{
		return mytour_collect([
			HOTEL_MODULE => ['name' => 'Hotel', 'id' => HOTEL_MODULE, 'table' => 'booking_hotel', 'field_prefix' => 'boo_'],
			DEAL_MODULE => ['name' => 'Deal', 'id' => DEAL_MODULE, 'table' => 'booking_deal', 'field_prefix' => 'bod_'],
			TOUR_MODULE => ['name' => 'Tour', 'id' => TOUR_MODULE, 'table' => 'tour_booking', 'field_prefix' => 'tbo_'],
			TICKET_MODULE => ['name' => 'Ticket', 'id' => TICKET_MODULE, 'table' => 'booking_ticket', 'field_prefix' => 'boot_']
		]);
	}


	public function bookingStates()
	{
		return mytour_collect([
			BOOKING_NEW => 'Đăt mới',
			BOOKING_PROCESSING => 'Đang xử lý',
			BOOKING_FAIL => 'Thất bại',
			BOOKING_SUCCESS => 'Thành công',
			BOOKING_WAITTING => 'Chờ duyệt',
			BOOKING_CANCEL => 'Hủy'
		]);
	}
}
