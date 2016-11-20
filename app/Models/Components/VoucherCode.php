<?php namespace App\Models\Components;

use App\Models\Model;

class VoucherCode extends Model {

	protected $table = 'voucher_codes';


	public function __construct($code = null)
	{
		$this->code = $code;
	}


	public function voucher()
	{
		return $this->belongsTo('App\Models\Components\Voucher');
	}




	public function scopeSearchByCode($query, $code)
	{
		$query->where('code', $code);
	}



	public function findByCode($code)
	{
		return $this->where('code', $code)->get()->first();
	}

}
