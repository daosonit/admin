<?php namespace App\Mytour\Traits;

trait Booking {


	/**
	 * Lấy ra tổng tiền phải thanh toán thực tế của booking
	 */
	public function getTotalMoney()
	{
		return doubleval($this->{$this->fieldPrefix . 'total_money'});
	}


	/**
	 * Lấy ra tổng tiền chưa giảm trừ chưa có phụ thu của booking
	 */
	public function getBookingMoney()
	{
		return doubleval($this->{$this->fieldPrefix . 'total_money'} + $this->{$this->fieldPrefix . 'money_discount'} + $this->{$this->fieldPrefix . 'money_discount_vpoint'});
	}



}