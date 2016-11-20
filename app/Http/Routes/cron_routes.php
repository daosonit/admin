<?php

Route::group(['prefix' => 'modules'], function(){
    Route::group(['prefix' => 'crons', 'namespace' => 'Crons'], function(){
        //Route tao don gia va cap nhat gia theo don phong
        Route::get('create_rate_plan', ['as' => 'create_rate_plan', 'uses' => 'ConvertRatePlanController@createRatePlan']);
        Route::get('update_rate_plan', ['as' => 'update_rate_plan', 'uses' => 'ConvertRatePlanController@updateRatePlan']);

        //Route Convert Gia
        Route::get('convert_price_rate_plan/{time_start}/{time_finish}', ['as' => 'convert_price_rate_plan', 'uses' => 'ConvertRatePlanController@convertPrice']);
        Route::get('convert_price_by_hotel_id/{hotel_id}/{time_start}/{time_finish}', ['as' => 'convert_price_by_hotel_id', 'uses' => 'ConvertRatePlanController@convertPriceByHotelId']);
        Route::get('update_price_rate_plan/{time_start}/{time_finish}', ['as' => 'update_price_rate_plan', 'uses' => 'ConvertRatePlanController@updatePrice']);

        //Route Convert Booking Hotel
        Route::get('convert_booking_hotel', ['as' => 'convert-booking-hotel', 'uses' => 'ConvertRatePlanController@ConvertBookingHotel']);
        Route::get('convert_booking_hotel_by_id/{boo_id}/{is_ota}', ['as' => 'convert-booking-hotel-by-id', 'uses' => 'ConvertRatePlanController@ConvertBookingHotelById']);

        //Route convert Commisision Room -> Hotel
        Route::get('convert-commission', ['as' => 'convert-commission', 'uses' => 'ConvertCommissionController@convertCommissionMarkup']);

        //Route convert promotion OTA
        Route::get('convert_promotion_ota', ['as' => 'convert-promotion-ota', 'uses' => 'ConvertPromotionController@convertPromotionOta']);
        Route::get('convert_promotion_last_minutes', ['as' => 'convert_promotion_last_minutes', 'uses' => 'ConvertPromotionController@convertPromoLastMinutes']);

        //Route delete promotion OTA
        Route::get('delete_promo_ota', ['as' => 'delete-promotion-ota', 'uses' => 'ConvertPromotionController@deletePromotionOta']);
        Route::get('delete_room_rate_promo', ['as' => 'delete-room-rate-promo', 'uses' => 'ConvertPromotionController@deleteRoomRatePromotion']);

        //Route convert promotion TA
        Route::get('convert_promotion_ta', ['as' => 'convert-promotion-ta', 'uses' => 'ConvertPromotionController@convertPromotionTa']);
        Route::get('update_promotion_ta', ['as' => 'update-promotion-ta', 'uses' => 'ConvertPromotionController@updatePromotionTa']);

        //Delete All Rate plan
        Route::get('delete_rate_plan', ['as' => 'delete-rate-plan', 'uses' => 'ConvertRatePlanController@deleteRatePlan']);

        Route::get('delete_price_rate_person', ['as' => 'delete_price_rate_person', 'uses' => 'ConvertRatePlanController@deletePriceRatePerson']);

        //Delete All Rate plan
        Route::get('delete_room_allotment', ['as' => 'delete-room-allotment', 'uses' => 'ConvertRatePlanController@deleteRoomAllotment']);

        //Cron hidden Rate Of Hotel TA
        Route::get('hidden_rate_hotel_ta', ['as' => 'hidden_rate_hotel_ta', 'uses' => 'ConvertRatePlanController@ConvertRateHotelTa']);

        //Cron update policy booking
        Route::get('update-policy-booking', ['as' => 'update-policy-booking', 'uses' => 'ConvertPolicyBookingController@convertPolicyBookingOld']);

        // CRON DATA PMS
        Route::get('cron_rate_plan_pms', ['as' => 'cron_rate_plan_pms', 'uses' => 'CronsApiPmsController@CronRatePlanPms']);
        Route::get('cron_room_price_pms', ['as' => 'cron_room_price_pms', 'uses' => 'CronsApiPmsController@CronRoomRatePricePms']);
        Route::get('cron_room_allotment_pms', ['as' => 'cron_room_allotment_pms', 'uses' => 'CronsApiPmsController@CronRoomAllotmentPms']);
        Route::get('cron_promotion_pms', ['as' => 'cron_promotion_pms', 'uses' => 'CronsApiPmsController@cronPromotionPms']);
        Route::get('update_promotion_last_pms', ['as' => 'update_promotion_last_pms', 'uses' => 'CronsApiPmsController@updatePromotionLastPms']);
        Route::get('update_room_allotment_pms', ['as' => 'update_room_allotment_pms', 'uses' => 'CronsApiPmsController@updateAllotmentPms']);
        Route::get('insert_room_allotment_fail/{hotel_id}/{time}/{pms}', ['as' => 'insert_room_allotment_fail', 'uses' => 'CronsApiPmsController@insertAllotmentFail']);



    });
});
