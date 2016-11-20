<?php namespace App\Commands;

use App\Commands\Command;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Mail;
class SendUserNewPassword extends Command implements SelfHandling, ShouldBeQueued {

	use InteractsWithQueue, SerializesModels;

	protected $adminUser;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct($adminUser)
	{
		$this->adminUser = $adminUser;
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$account 	 = $this->adminUser;
		$newPassword = $this->randomString();

		$this->adminUser->password = \Hash::make($newPassword);

		if($this->adminUser->save()){
			Mail::send('emails.system.reset-password', ['account' => $account, 'newPassword' => $newPassword], function($message) use($account)
			{
		    	$message->to($account->email, 'New Password - ' . $account->name . ' Mytour Administrator')->subject('Send new password!');
			});
		}
	}



	private function randomString($length = 8)
	{
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	    $pass = array();
	    $alphaLength = strlen($alphabet) - 1; 
	    for ($i = 0; $i < $length; $i++) {
	        $n = rand(0, $alphaLength);
	        $pass[] = $alphabet[$n];
	    }
	    return implode($pass); 
	}

}
