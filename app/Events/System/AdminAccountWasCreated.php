<?php namespace App\Events\System;

use App\Events\Event;

use Illuminate\Queue\SerializesModels;

class AdminAccountWasCreated extends Event {

	use SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($adminUser)
	{
		$this->newAdminAccount = $adminUser;
	}

}
