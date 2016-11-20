<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MytourInitializer extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mytour:init';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Initializer Project!';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

		if(!file_exists(storage_path('app/public/'))){
			if(!file_exists(storage_path('app/'))){
				mkdir(storage_path('app/'), 0777);	
			}
			mkdir(storage_path('app/public/'), 0777);
		}

		if(file_exists(public_path('assets')) && is_link(public_path('assets'))){
			unlink(public_path('assets'));
		}
		
		symlink(base_path('resources/assets/'), public_path('assets'));

		if(file_exists(public_path('public')) && is_link(public_path('public'))){
			unlink(public_path('public'));
		}
		symlink(storage_path('app/public/'), public_path('public'));

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			
		];
	}

}
