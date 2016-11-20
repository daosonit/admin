<?php namespace App\Handlers\Events;

use App\Events\HotelBookingWasSuccessed;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Mail;
class SendEmailHotelBookingSuccessToCustomer {

	/**
	 * Create the event handler.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param  HotelBookingWasSuccessed  $event
	 * @return void
	 */
	public function handle(HotelBookingWasSuccessed $event)
	{

		$hotelBooking = $event->hotelBooking;

		Mail::send('emails.hotel-booking.hotel-booking-success-to-customer', ['hotelBooking' => $hotelBooking], function($message) use($hotelBooking)
		{
	    	$message->to($hotelBooking->boo_customer_email, 'Chào ' . $hotelBooking->boo_customer_name . ' booking của bạn đã được xử lý thành công.')->subject('Mytour - Booking Success!');		
		});
	}

}
