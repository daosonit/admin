<?php namespace App\Handlers\Events\System;

use App\Events\System\AdminAccountWasCreated;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Mail;

class EmailAdminAccountCreatedNotification {

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
	 * @param  AdminAccountWasCreated  $event
	 * @return void
	 */
	public function handle(AdminAccountWasCreated $event)
	{
		$newAccount = $event->newAdminAccount;
		Mail::send('emails.system.welcome', ['newAdminAccount' => $newAccount], function($message) use($newAccount)
		{
	    	$message->to($newAccount->email, 'Welcome ' . $newAccount->name . 'to Mytour Administrator')->subject('Welcome To Mytour Administrator!');
		});
	}

}
