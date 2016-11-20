<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Components\HotelBooking;
use App\Events\HotelBookingWasSuccessed;
class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\Inspire',
		'App\Console\Commands\MytourInitializer'
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('Inspire')
				 ->hourly();
				 
		// $schedule->call(function(){
		// 	$hotelBooking = HotelBooking::find(7799);
		// 	event(new HotelBookingWasSuccessed($hotelBooking));
		// })->everyFiveMinutes();
	}

}
