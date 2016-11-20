<?php namespace App\Models\Components;

use App\Models\Model;

class VoucherDiscountInfo extends Model {

	//
	protected $table = 'voucher_discount_infos';

	protected $fillable = ['money_type', 'vpoint_type', 'money', 'money_max', 'vpoint', 'vpoint_max', 'vpoint_expire', 'gift_info'];

	public function voucher()
	{
		return $this->belongsTo('App\Models\Components\Voucher');
	}

}
