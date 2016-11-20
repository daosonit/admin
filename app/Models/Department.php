<?php namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model {

	use SoftDeletes;

	protected $fillable = ['name', 'description'];

	public function adminUsers()
	{
		return $this->hasMany('App\Models\AdminUser', 'department_id');
	}







}