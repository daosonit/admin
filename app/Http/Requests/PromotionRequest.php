<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class PromotionRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'proh_title'         => 'required',
			'room_id'            => 'required',
			'date_range_book'    => 'required',
			'date_range_checkin' => 'required',

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
			'proh_title.required'         => 'Bạn hãy nhập tiêu đề khuyến mãi!',
			'room_id.required'            => 'Bạn hãy chọn phòng được áp dụng khuyến mãi',
			'date_range_checkin.required' => 'Bạn hãy nhập thời gian nhận phòng!',
			'date_range_book.required'    => 'Bạn hãy nhập thời gian đặt phòng!'
	    ];
	}

}
