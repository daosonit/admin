<?php 
namespace App\Mytour\Traits;
use App\Mytour\ModelQueryScopes\HotelActivedScope;

trait HotelActived {
	/**
	 * Boot the soft deleting trait for a model.
	 *
	 * @return void
	 */
	public static function bootHotelActived()
	{
		static::addGlobalScope(new HotelActivedScope);
	}
}