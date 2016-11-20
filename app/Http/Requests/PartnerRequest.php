<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class PartnerRequest extends Request
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasRole('	admin.su');
        //return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array(
            'pn_name' => 'required',
            'pn_link' => 'required',
            'pn_info' => 'required',
            'pn_type' => 'integer',
            'pn_logo' => 'image|max:1024*500'
        );
    }

    /**
     * Messages validation
     */
    public function messages()
    {
        return array('pn_name.required' => 'Tên đối tác bắt buộc  nhập',
                     'pn_link.required' => 'URL bắt buộc  nhập',
                     'pn_info.required' => 'Thông tin đối tác bắt buộc  nhập',
                     'pn_type.integer'  => 'Thông tin đối tác không hợp lệ',
                     'pn_logo.image'    => 'Ảnh sai định dạng',
                     'pn_logo.max'      => 'Dung lượng ảnh quá lớn');
    }
}
