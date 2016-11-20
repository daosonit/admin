<?php namespace App\Providers;

use View;
use Illuminate\Support\ServiceProvider;


class ComposerServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Using class based composers...
        View::composer(['app'], 'App\Http\ViewComposers\AppComposer');
        
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

}
