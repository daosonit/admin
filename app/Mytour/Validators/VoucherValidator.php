<?php namespace App\Mytour\Validators;

class VoucherValidator extends MytourValidator {
	

	/**
	 * array: các trường cần validate khi tạo mới
	 */
	protected $createFields = [
		'name' => 'required',
		'description' => 'required',
		'timebook_start' => 'required',
		'timebook_start' => 'required',
	];


	protected $createMsg = [
		'name.required' => 'Tên chương trình là thông tin bắt buộc',
		'description.required' => 'Mô tả là thông tin bắt buộc',
		'description.min' => 'Mô tả ít nhất 20 ký tự',
		'timebook_start.required' => 'Thời gian đặt áp dụng là thông tin bắt buộc',
		'timebook_start.required' => 'Thời gian đặt áp dụng là thông tin bắt buộc',
	];

	protected $editFields = [
		'name' => 'required',
		'description' => 'required',
		'timebook_start' => 'required',
		'timebook_start' => 'required',
	];


	protected $editMsg = [
		'name.required' => 'Tên chương trình là thông tin bắt buộc',
		'description.required' => 'Mô tả là thông tin bắt buộc',
		'description.min' => 'Mô tả ít nhất 20 ký tự',
		'timebook_start.required' => 'Thời gian đặt áp dụng là thông tin bắt buộc',
		'timebook_start.required' => 'Thời gian đặt áp dụng là thông tin bắt buộc',
	];



}