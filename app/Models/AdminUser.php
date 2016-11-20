<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class AdminUser extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	// add this trait to your user model
	use EntrustUserTrait {

		can 	as entrustCan;
        hasRole as entrustHasRole;
        ability as entrustAbility;
        
        
    }
	use SoftDeletes;


	const GENDER_MALE = 0;

	const GENDER_FEMALE = 1;

	const ACTIVE = 1;

	const DEACTIVE = 0;


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
	protected $fillable = ['name', 'email', 'department_id', 'address', 'user_id', 'password', 'phone', 'active', 'identity_card', 'gender'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];


	/**
	 * The dates
	 *
	 * @var array
	 */
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];



    /**
     * getAvatar
     *
     * @return string url image
     */
    public function getAvatar()
    {
        $image_user_s3 = url_s3() . Config::get('image_config.pathUser') . 'user_' . $this->avatar;
        return (!empty($this->avatar) ? $image_user_s3 : $this->getNoAvatarImage());
    }

    private function getNoAvatarImage()
    {
        if ($this->gender == static::GENDER_MALE) {
            return asset('assets/mytour/images/noavatar.jpg');
        }
        return asset('assets/mytour/images/noavatar_female.png');
    }


	/**
	 * userOutside
	 *
	 * @return instance of Member model 
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\Member', 'adm_user_id_outside');
	}

	/**
	 * isSupperAdmin
	 *
	 * @return boolean 
	 */
	public function isSupperAdmin()
	{
		return (in_array($this->attributes['email'], explode('|', Config::get('mytour.super_user'))));
	}



	public function scopeWomen($query)
	{
		return $query->where('gender', static::GENDER_FEMALE);
	}



	public function scopeActive($query)
	{
		return $query->where('active', ACTIVE);
	}


	public function scopeMen($query)
	{
		return $query->where('gender', static::GENDER_MALE);
	}


	public function can($permission, $requireAll = false)
	{
		if($this->isSupperAdmin()) return true;
		return $this->entrustCan($permission, $requireAll = false);
	}



	public function hasRole($name, $requireAll = false)
	{
		if($this->isSupperAdmin()) return true;
		return $this->entrustHasRole($name, $requireAll = false);
	}



	public function ability($roles, $permissions, $options = [])
	{
		if($this->isSupperAdmin()) return true;
		return $this->entrustAbility($roles, $permissions, $options = []);
	}



	public function department()
	{
		return $this->belongsTo('App\Models\Department');
	}


}
