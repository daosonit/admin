<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * Image path
	 */
	const IMAGE_PATH = '/upload/admin/avatar_images/';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'admins';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];

	/**
	 * getAvatar
	 *
	 * @return string url image
	 */
	public function getAvatar()
	{
		return static::IMAGE_PATH . (!empty($this->adm_picture) ? $this->adm_picture : 'no_avatar.png');
	}

	/**
	 * userOutside
	 *
	 * @return instance of Member model 
	 */
	public function userOutside()
	{
		return $this->hasOne('App\Models\Member', 'use_id', 'adm_user_id_outside');
	}

	/**
	 * isSupperAdmin
	 *
	 * @return boolean 
	 */
	public function isSupperAdmin()
	{
		return ($this->adm_email === 'get.right.fnt@gmail.com');
        
	}

}
