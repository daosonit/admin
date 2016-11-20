<?php namespace App\Mytour\Validators;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

abstract class MytourValidator 
{
	use ValidatesRequests;

	protected $createFields = [];

	protected $editFields = [];

	protected $createMsg = [];

	protected $editMsg = [];

	public function validateDataCreate(Request $request)
	{
		$createFields = $this->createFields;
		$createMsg    = $this->createMsg;
		return $this->validate($request, $createFields, $createMsg);
	}

	public function validateDataEdit(Request $request)
	{
		$editFields = $this->editFields;
		$editMsg    = $this->editMsg;
		return $this->validate($request, $editFields, $editMsg);
	}


	



	public function pushCreateField($fields = [])
	{
		foreach($fields as $field => $role){
			$this->createFields[$field] = $role;
		}
		return $this;
	}


	public function pushEditField($fields = [])
	{
		foreach($fields as $field => $role){
			$this->editFields[$field] = $role;
		}
		return $this;
	}

 
	public function pushCreateMsg($fields = [])
	{
		foreach($fields as $field => $msg){
			$this->createMsg[$field] = $msg;
		}
		return $this;
	}


	public function pushEditMsg($fields = [])
	{
		foreach($fields as $field => $msg){
			$this->editMsg[$field] = $msg;
		}
		return $this;
	}



}