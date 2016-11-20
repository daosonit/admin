<?php namespace App\Mytour\Validators;

class RatePlanValidator extends MytourValidator {
	

	/**
	 * array: các trường cần validate khi tạo mới
	 */
	protected $createFields = [
		'rap_title' => 'required',
		'rap_room_apply_id' => 'required',
	];


	protected $createMsg = [
		'rap_title.required' => 'Tên hệ thống giá là thông tin bắt buộc.',
		'rap_room_apply_id.required' => 'Bạn chưa chọn phòng khách sạn.',
		
	];


	protected $editFields = [
		'rap_title' => 'required',
		'rap_room_apply_id' => 'required',
	];


	protected $editMsg = [
		'rap_title.required' => 'Tên hệ thống giá là thông tin bắt buộc.',
		'rap_room_apply_id.required' => 'Bạn chưa chọn phòng khách sạn.',
		
	];



}