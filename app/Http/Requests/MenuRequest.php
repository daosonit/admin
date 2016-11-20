<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class MenuRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return $this->user()->hasRole('	admin.su');
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name' => 'required',
			'visible_on' => 'required',
			'menu_group_id' => 'required|numeric'
		];
	}


	/**
	 * Get the validation messages that apply to the request.
	 *
	 * @return array
	 */
	public function messages()
	{
	    return [
			'name.required' => 'Name là thông tin bắt buộc!',
			'visible_on.required'	  => 'Visible là thông tin bắt buộc dùng để hiển hiện thị menu theo permission của User, nhập ngăn cách nhau bởi "|"',
			'menu_group_id.required' => 'Bạn chưa chọn Group!',
			'menu_group_id.numeric' => 'Thông tin Group không hợp lệ!',
	    ];
	}

}
