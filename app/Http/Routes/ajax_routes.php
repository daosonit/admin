<?php


Route::group(['prefix' => 'ajax', 'namespace' => 'AjaxControllers'], function(){

	Route::group(['prefix' => 'role'], function(){
		Route::post('attach-one/{roleID}', ['as' => 'attach-one-permission', 'uses' => 'SystemController@attachPermissionToRole']);
	});

	Route::group(['prefix' => 'account'], function(){
		Route::post('attach-one/{adminID}', ['as' => 'attach-one-role', 'uses' => 'SystemController@attachRoleToAdminUser']);
	});

	Route::group(['prefix' => 'hotel'], function(){
		Route::get('hotel-suggest', ['as' => 'hotel-suggest', 'uses' => 'HotelSuggestionController@getHotelSuggestion']);
	});


	Route::group(['prefix' => 'city'], function(){
		Route::get('city-suggest', ['as' => 'city-suggest', 'uses' => 'CitySuggestionController@getCitySuggestion']);
	});
	
});