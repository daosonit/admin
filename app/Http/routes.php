<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['domain' => config('mytour.domain.admin') . '.'.config('mytour.domain.site').'.' . config('mytour.tld')], function(){

	require __DIR__ . '/Routes/auth_routes.php';
	require __DIR__ . '/Routes/cron_routes.php';
	

	Route::group(['middleware' => 'auth'], function(){
		require __DIR__ . '/Routes/module_routes.php';
		require __DIR__ . '/Routes/other_routes.php';
		require __DIR__ . '/Routes/ajax_routes.php';
	    require __DIR__ . '/Routes/system_routes.php';
	    require __DIR__ . '/Routes/log_routes.php';
	});

	if(is_dev_env() || is_local_env()){
		require __DIR__ . '/Routes/test_routes.php';
	}

});



Route::group(['domain' => config('mytour.domain.api') . '.'.config('mytour.domain.site').'.' . config('mytour.tld')], function($router){
	require __DIR__ . '/Routes/api_routes.php';
});


Route::group(['domain' => config('mytour.domain.site') . '.' . config('mytour.tld')], function(){
	require __DIR__ . '/Routes/site_routes.php';
});


Route::get('test-model', function(){



	return ucfirst($name);

	// return App\Models\AdminUser::class;
	// $users = $user->get();
	// dd($users);

});
