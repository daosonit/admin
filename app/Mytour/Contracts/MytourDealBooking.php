<?php 

namespace App\Mytour\Contracts;


interface MytourDealBooking {
	
	/**
	 * Trả về thời gian checkin của Booking
	 */
	public function getDateTimeCheckin();

	/**
	 * Trả về thời gian checkout của Booking
	 */
	public function getDateTimeCheckout();

}