<?php namespace App\Mytour\Validators\System;

use App\Mytour\Validators\MytourValidator;

class PermissionValidator extends MytourValidator {
	

	/**
	 * array: các trường cần validate khi tạo mới
	 */
	protected $createFields = [
		'name' => 'required|unique:entrust_permissions',
		'display_name' => 'required',
	];


	protected $createMsg = [
		'name.required'    => 'Name là thông tin bắt buộc!',
		'name.unique'    => 'Permission này đã tồn tại! Vui lòng kiểm tra lại tên!',
		'display_name.required'    => 'Display name là thông tin bắt buộc',
	];

	protected $editFields = [
		'display_name' => 'required',
	];


	protected $editMsg = [
		'display_name.required'    => 'Display name là thông tin bắt buộc',
	];



}