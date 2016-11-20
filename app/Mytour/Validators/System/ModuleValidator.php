<?php namespace App\Mytour\Validators\System;

use App\Mytour\Validators\MytourValidator;

class ModuleValidator extends MytourValidator {
	

	/**
	 * array: các trường cần validate khi tạo mới
	 */
	protected $createFields = [
		'mod_name' => 'required',
		'mod_group_id' => 'required',
		'mod_listroute' => 'required',
	];


	protected $createMsg = [
		'mod_name.required' => 'Tên module là thông tin bắt buộc',
		'mod_group_id.required' => 'Group là thông tin bắt buộc',
		'mod_listroute.required' => 'List route là thông tin bắt buộc',
		
	];

	protected $editFields = [
		'mod_name' => 'required',
		'mod_group_id' => 'required',
		'mod_listroute' => 'required',
	];


	protected $editMsg = [
		'mod_name.required' => 'Tên module là thông tin bắt buộc',
		'mod_group_id.required' => 'Group là thông tin bắt buộc',
		'mod_listroute.required' => 'List route là thông tin bắt buộc',
	];



}