<?php namespace App\Mytour\Validators;

class RoomValidator extends MytourValidator {
	

	/**
	 * array: các trường cần validate khi tạo mới
	 */
	protected $createFields = [
		'room_type'    => 'required',
		'room_total'   => 'required',
		'room_area'    => 'required',
		'adult'        => 'required',
		// 'rom_info_bed' => 'required',
		'convenience'  => 'required',

	];


	protected $createMsg = [
		'room_type.required'    => 'Loại phòng là thông tin bắt buộc.',
		'room_total.required'   => 'Bạn chưa nhập tổng số phòng hiện có.',
		'room_area.required'    => 'Bạn chưa nhập diện tích phòng tối thiểu.',
		'adult.required'        => 'Bạn chưa nhập số người lớn tối đa.',
		// 'rom_info_bed.required' => 'Bạn chưa chọn loại giường.',
		'convenience.required'  => 'Bạn chưa chọn tiện nghi.',
		
	];

	// protected $editFields = [
	// 	'name' => 'required',
	// 	'description' => 'required',
	// 	'timebook_start' => 'required',
	// 	'timebook_start' => 'required',
	// ];


	// protected $editMsg = [
	// 	'name.required' => 'Tên chương trình là thông tin bắt buộc',
	// 	'description.required' => 'Mô tả là thông tin bắt buộc',
	// 	'description.min' => 'Mô tả ít nhất 20 ký tự',
	// 	'timebook_start.required' => 'Thời gian đặt áp dụng là thông tin bắt buộc',
	// 	'timebook_start.required' => 'Thời gian đặt áp dụng là thông tin bắt buộc',
	// ];



}