<?php namespace App\Models\Components;

use App\Models\Model;
use Carbon\Carbon;

class Voucher extends Model {

	protected $table = 'vouchers';

	protected $fillable = [ 'name', 'description', 'category_id', 'discount_type', 'time_checkin_apply',
							'type', 'timebook_start', 'timebook_finish', 'checkin_start', 'checkin_finish', 
							'advance_setting', 'cancellation_policy', 'expired_at', 'acitve'
						  ];

	protected $dates = ['timebook_start', 'timebook_finish', 'checkin_start', 'checkin_finish', 'expired_at', 'created_at', 'updated_at'];




	public function voucherCodes()
	{
		return $this->hasMany('App\Models\Components\VoucherCode');
	}




	public function voucherAdvanceSetting()
	{
		return $this->hasOne('App\Models\Components\VoucherAdvanceSetting');
	}



	public function voucherDiscountInfo()
	{
		return $this->hasOne('App\Models\Components\VoucherDiscountInfo');
	}



	public function setTimebookStartAttribute($date)
	{
		$this->attributes['timebook_start'] = Carbon::parse($date);
	}

	public function setTimebookFinishAttribute($date)
	{
		$this->attributes['timebook_finish'] = Carbon::parse($date);
	}

	public function setCheckinStartAttribute($date)
	{
		$this->attributes['checkin_start'] = Carbon::parse($date);
	}

	public function setCheckinFinishAttribute($date)
	{
		$this->attributes['checkin_finish'] = Carbon::parse($date);
	}

	public function setExpiredAtAttribute($date)
	{
		$this->attributes['expired_at'] = Carbon::parse($date);
	}




	public function scopeSearchByName($query, $name)
	{
		$query->where('name', 'LIKE', '%' . $name . '%');
	}

}



