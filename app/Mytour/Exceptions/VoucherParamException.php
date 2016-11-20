<?php namespace App\Mytour\Exceptions;

class VoucherParamException extends \Exception
{
	protected $message = 'Param truyền vào Voucher phải là 1 mảng hoặc 1 thể hiện của HotelBooking model';

	protected $code = 1;
}