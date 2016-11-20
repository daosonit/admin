<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class DepartmentRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return $this->user()->hasRole('admin.su');
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name' => 'required'
		];
	}


	public function messages()
	{
		return [
			'name.required' => 'Tên là thông tin bắt buộc!'
		];
	}

}
