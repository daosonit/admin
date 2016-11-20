<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use App\Events\HotelBookingWasSuccessed;

class SendEmailHotelBookingSuccess extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	protected $hotelBooking;

	public function __construct($hotelBooking)
	{
		$this->hotelBooking = $hotelBooking;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		event(new HotelBookingWasSuccessed($this->hotelBooking));
	}

}
