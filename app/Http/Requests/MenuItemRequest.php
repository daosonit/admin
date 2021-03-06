<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class MenuItemRequest extends Request {

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
			'menu_id' => 'required',
			'visible_on' => 'required',
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
			'menu_id.required' => 'Thông tin tên Menu là bắt buộc',
			'visible_on.required'	  => 'Visible là thông tin bắt buộc dùng để hiển hiện thị menu theo permission của User, nhập ngăn cách nhau bởi "|"',
	    ];
	}

}
