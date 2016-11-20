<?php namespace App\Models;


class Member extends Model {

	protected $primaryKey = 'use_id';


	protected $table = 'users';


	protected $fieldPrefix = 'use_';

	public function adminUser()
	{
		return $this->hasOne('App\Models\AdminUser', 'adm_user_id_outside');
	}



	public function findByEmail($email)
	{
		return $this->where('use_email', $email)->get()->first();
	}

}
