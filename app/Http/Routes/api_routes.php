<?php 

Route::get('get-hotel-price', function(){
	$hotels = App\Models\Components\Hotel::get();
	dd($hotels->keyBy(function($hotel){
		return $hotel->hot_id;
	})->where('hot_id', 20));
});