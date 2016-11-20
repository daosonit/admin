<?php namespace App\Mytour\Validators\System;

use App\Mytour\Validators\MytourValidator;

class AccountValidator extends MytourValidator {
	

	/**
	 * array: các trường cần validate khi tạo mới
	 */
	protected $createFields = [
		'name' => 'required|max:255|min:3',
		'email' => 'required|email|max:255|unique:admins',
		'password' => 'required|confirmed|min:8',
		'phone' => 'required',
		'identity_card' => 'required|numeric',
		'gender'		=> 'required'
	];


	protected $createMsg = [
		'name.required' => 'Name là thông tin bắt buộc!',
		'email.required' => 'Email là thông tin bắt buộc!',
		'email.email' => 'Email không đúng định dạng!',
		'email.unique' => 'Email này đã tồn tại!',
		'password.required' => 'Password là thông tin bắt buộc!',
		'password.confirmed' => 'Password và Confirm Password không khớp!',
		'phone.required' => 'Số điện thoại là thông tin bắt buộc!',
		'identity_card.required' => 'Số chứng minh thư là thông tin bắt buộc!',
		'identity_card.numeric' => 'Số chứng minh thư phải là chuỗi số!'
	];

	protected $editFields = [
		'name' => 'required|max:255|min:3',
		'phone' => 'required',
		'identity_card' => 'required|numeric',
		'gender'
	];


	protected $editMsg = [
		'name.required' => 'Name là thông tin bắt buộc!',
		'phone.required' => 'Số điện thoại là thông tin bắt buộc!',
		'identity_card.required' => 'Số chứng minh thư là thông tin bắt buộc!',
		'identity_card.numeric' => 'Số chứng minh thư phải là chuỗi số!'
	];



}