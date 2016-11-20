<?php




/*
|--------------------------------------------------------------------------
| Module Routes
|--------------------------------------------------------------------------
|
| Nhung route thuoc ve tat cac cac module duoc dinh nghia o day
|
|
|
*/
Route::group(['prefix' => 'modules'], function(){

	Route::group(['prefix' => 'hotel-bookings', 'namespace' => 'HotelBooking'], function(){
		Route::get('/', ['as' => 'hotel-bookings', 'uses' => 'HotelBookingController@showList']);
	});

	// Route::group(['prefix' => 'deal-bookings', 'namespace' => 'DealBooking'], function(){
	// 	Route::get('bookings', ['as' => 'bookings', 'uses' => 'DealBookingController@showList']);
	// });



	Route::group(['prefix' => 'voucher', 'namespace' => 'Voucher'], function(){
		Route::get('list', ['as' => 'voucher-list', 'uses' => 'VoucherController@showList']);

		Entrust::routeNeedsRole('voucher-create', 'admin.su');
		Route::get('create', ['as' => 'voucher-create', 'uses' => 'VoucherController@getCreate']);
		Route::post('create', ['as' => 'voucher-create', 'uses' => 'VoucherController@postCreate']);

		Route::get('edit/{id}', ['as' => 'voucher-edit', 'uses' => 'VoucherController@getEdit']);
		Route::post('edit/{id}', ['as' => 'voucher-edit', 'uses' => 'VoucherController@postEdit']);

		Route::get('delete/{id}', ['as' => 'voucher-delete', 'uses' => 'VoucherController@delete']);

		Route::get('codes/{id?}', ['as' => 'voucher-codes', 'uses' => 'VoucherController@getCodes']);

		Route::get('codes/{id}/delete', ['as' => 'voucher-codes-delete', 'uses' => 'VoucherController@deleteCode']);
	});




	Route::group(['prefix' => 'member', 'namespace' => 'Member'], function(){
		Route::get('list', 'MemberController@showList');
	});





	//=================Router Module Room Price==================
	Route::group(['prefix' => 'room-price', 'namespace' => 'RoomPrice'], function(){
		Route::get('list', ['as' => 'room-price-list', 'uses' => 'RoomPriceController@index']);

		Route::get('show/{id}', ['as' => 'room-price-show', 'uses' => 'RoomPriceController@show']);

		Route::post('update-price-form', ['as' => 'update-price-form', 'uses' => 'RoomPriceController@excutePrice']);

		Route::post('get-price-ajax', ['as' => 'get-price-ajax', 'uses' => 'RoomPriceController@showRoomInfoAjax']);

		Route::post('update-allotment-of-day', ['as' => 'update-allotment-of-day', 'uses' => 'RoomPriceController@updateAllotmentOfDay']);

		Route::post('update-allotment-range-time', ['as' => 'update-allotment-range-time', 'uses' => 'RoomPriceController@updateAllotmentTimeRange']);

		Route::post('update-price-contract-ta-range-time', ['as' => 'update-price-contract-ta-range-time', 'uses' => 'RoomPriceController@updatePriceContractTaTimeRange']);

		Route::post('update-price-ota-range-time', ['as' => 'update-price-ota-range-time', 'uses' => 'RoomPriceController@updatePriceOtaTimeRange']);

		Route::post('hidden-price-ajax', ['as' => 'hidden-price-ajax', 'uses' => 'RoomPriceController@hiddenPriceAjax']);

		Route::post('check-tax-fee-ajax', ['as' => 'check-tax-fee-ajax', 'uses' => 'RoomPriceController@checkTaxFeeAjax']);

		Route::post('active-price-email-ajax', ['as' => 'active-price-email-ajax', 'uses' => 'RoomPriceController@hiddenPriceAjax']);

	});

	//=================Router Module Room==================
	Route::group(['prefix' => 'room', 'namespace' => 'Room'], function(){
		Route::get('create/{id}', ['as' => 'room-create', 'uses' => 'RoomController@getCreate']);
		Route::post('create/{id}', ['uses' => 'RoomController@postCreate']);

		Route::get('edit/{id}', ['as' => 'room-edit', 'uses' => 'RoomController@getEdit']);
		Route::post('edit/{id}', ['uses' => 'RoomController@postEdit']);

		Route::get('list/{id}', ['as' => 'room-list', 'uses' => 'RoomController@getData']);

		Route::post('delete-room', ['as' => 'delete-room', 'uses' => 'RoomController@deleteRoom']);
		Route::post('active-room', ['as' => 'active-room', 'uses' => 'RoomController@activeRoom']);
		Route::post('save-room-pms', ['as' => 'save-room-pms', 'uses' => 'RoomController@saveRoomPms']);
		Route::post('delete-room-pms', ['as' => 'delete-room-pms', 'uses' => 'RoomController@deleteRoomPms']);
	});

	//=================Router Module Rate Plan==================
	Route::group(['prefix' => 'rate-plan', 'namespace' => 'RatePlan'], function(){
		Route::get('create/{id}', ['as' => 'rate-plan-create', 'uses' => 'RatePlanController@getCreate']);
		Route::post('create/{id}', ['uses' => 'RatePlanController@postCreate']);

		Route::get('edit/{id}', ['as' => 'rate-plan-edit', 'uses' => 'RatePlanController@getEdit']);
		Route::post('edit/{id}', ['uses' => 'RatePlanController@postEdit']);

		Route::get('list/{id}', ['as' => 'rate-plan-list', 'uses' => 'RatePlanController@getData']);
		Route::post('delete-rate-plan', ['as' => 'delete-rate-plan', 'uses' => 'RatePlanController@deleteRatePlan']);
		Route::post('check-active', ['as' => 'check-active', 'uses' => 'RatePlanController@activeRatePlan']);
	});

	//=================Router Module Hotel Promotion==================
	Route::group(['prefix' => 'hotel-promotion', 'namespace' => 'HotelPromotion'], function(){

		Route::get('listing/{id}', ['as' => 'hotel-promo-list', 'uses' => 'HotelPromotionController@index']);

		Route::get('create_step_1/{id}', ['as' => 'hotel-promo-create-step-1', 'uses' => 'HotelPromotionController@create_step_1']);
		Route::get('create_step_2/{id}/{type}', ['as' => 'hotel-promo-create-step-2', 'uses' => 'HotelPromotionController@create_step_2']);
		Route::post('store_step_1', ['as' => 'hotel-promo-create-post-step-1', 'uses' => 'HotelPromotionController@store_step_1']);
		Route::post('store_step_2', ['as' => 'hotel-promo-create-post-step-2', 'uses' => 'HotelPromotionController@store_step_2']);


		Route::get('edit_promotion/{id}', ['as' => 'edit-hotel-promo', 'uses' => 'HotelPromotionController@editPromotion']);
		Route::patch('edit_promotion/{id}', ['as' => 'update-hotel-promo', 'uses' => 'HotelPromotionController@updatePromotion']);
		Route::get('delete_promotion', ['as' => 'delete-hotel-promo', 'uses' => 'HotelPromotionController@destroy']);

		Route::get('edit_promo_ta_custom/{id}', ['as' => 'edit-promo-ta-custom', 'uses' => 'HotelPromotionController@editPromoTa']);
		Route::get('edit_promo_ota_custom/{id}', ['as' => 'edit-promo-ota-custom', 'uses' => 'HotelPromotionController@editPromoOta']);


		Route::get('ajax-get-name-hotel', ['as' => 'ajax-get-name-hotel', 'uses' => 'HotelPromotionController@getNameHotelAjax']);
		Route::get('active_hotel_promo', ['as' => 'active-hotel-promo', 'uses' => 'HotelPromotionController@active']);

	});



	Route::group(['namespace' => 'Hotel'], function(){


		Route::get('hotels/trashed', ['as' => 'modules.hotels.trashed', 'uses' => 'HotelController@getTrashed']);

		Route::get('hotels/{id}/restore', ['as' => 'modules.hotels.restore', 'uses' => 'HotelController@restore']);

		Route::resource('hotels', 'HotelController');


	});

    Route::group(['namespace' => 'Contents'], function() {
        Route::resource('partners', 'PartnerController');

        //Route active
        Route::get('/partners/active/{id}/{field}', array('as'   => 'modules.partners.active',
                                                          'uses' => 'PartnerController@active'));
    });

	Route::group(['namespace' => 'Marketing'], function () {
		Route::resource('manage-email', 'ManageEmailController');
	});
});

