<?php namespace App\Http\Controllers\Profile;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\PasswordBroker;
use Validator, Auth, View, Redirect, Config, Input;
use App\Mytour\Classes\Images\UploadImage;

class ProfileController extends Controller
{

    public function __construct()
    {
    }


    public function showProfile(Authenticatable $user)
    {
        return View::make('profile')->with('user', $user);
    }


    public function updateProfile(Request $request, Authenticatable $user)
    {
        $this->validate($request, $this->rules(), $this->messages());

        $data = $request->only('name', 'avatar', 'phone', 'address');

        if ($request->hasFile('avatar')) {
            $image_upload = new UploadImage();

            $option = array('prefix_size' => Config::get('image_config.sizeUser'),
                            'path'        => Config::get('image_config.pathUser'));
            $image_upload->make($option)->save($request->file('avatar'));

            if (count($image_upload->error()) == 0) {

                //Xóa ảnh cũ
                $image_upload->delete($user->avatar);
                $user->avatar = $image_upload->fileName();
                
            } else {
                return Redirect::back()->withInput()->withErrors(['avatar' => $image_upload->error()]);
            }

        }

        $user->name    = $data['name'];
        $user->phone   = $data['phone'];
        $user->address = $data['address'];

        $user->save();

        return redirect(route('profile.index'))->with('status_info','Cập nhật thành công');
    }

    private function rules()
    {
        $rules = ['name'    => 'required|max:255',
                  'phone'   => 'required|digits_between:8,11',
                  'address' => 'required|max:255'];

        if (Input::has('avatar')) {
            $rules['avatar'] = 'required|image';
        }

        return $rules;
    }

    private function messages()
    {
        return ['name.required'        => 'Bạn chưa nhập tên!',
                'name.max'             => 'Tên quá dài!',
                'phone.required'       => 'Bạn chưa nhập số điện thoại',
                'phone.digits_between' => 'Số điện thoại trong khoảng 8-11 ký tự số',
                'address.required'     => 'Địa chỉ bắt buộc nhập',
                'address.max'          => 'Địa chỉ quá dài',];
    }

    public function updateAvatar(Request $request)
    {

    }

    public function changePassword(Request $request, Authenticatable $user)
    {
        $rules = array('password_old'          => 'required|',
                       'password'              => 'required|min:6|different:password_old',
                       'password_confirmation' => 'required|same:password');

        $messages = array('password_old.required'          => 'Mật khẩu cũ bắt buộc nhập',
                          'password.required'              => 'Mật khẩu mới bắt buộc nhập',
                          'password.different'             => 'Mật khẩu mới bắt buộc khác mật khẩu cũ',
                          'password.min'                   => 'Mật khẩu mới phải lớn hơn hoặc bằng 6 ký tự',
                          'password_confirmation.required' => 'Nhập lại mật khẩu mới',
                          'password_confirmation.same'     => 'Nhập lại không chính xác',);

        $validation = Validator::make($request->all(), $rules, $messages);

        if (!Auth::validate(array('email' => Auth::user()->email, 'password' => $request->password_old))) {
            $validation->getMessageBag()->add('password_old', 'Mật khẩu cũ không đúng');
            return Redirect::back()->withErrors($validation)->withInput()->with(array('tab' => 2));

        } else {

            if ($validation->fails() == false) {
                $user           = Auth::user();
                $user->password = bcrypt($request->password);
                if ($user->save()) {
                    Auth::login($user);
                    return redirect(route('profile.index'))->with(array('status' => 'Đổi mật khẩu thành công', 'tab' => 2));
                } else {
                    return redirect(route('profile.index'))->with(array('status' => 'Có lỗi xảy ra', 'tab' => 2));
                }
            }

            return Redirect::back()->withErrors($validation)->withInput()->with(array('tab' => 2));
        }
    }

    /**
     * @param Request $request
     */
    private function validation(Request $request, $rules, $messages)
    {
        $this->validate($request, $rules, $messages);
    }
}
