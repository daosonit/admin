<?php 

namespace App\Mytour\MytourExtends;

use Illuminate\Database\Eloquent\Collection;
use Closure;
/**
* @author NHH
*/


class MytourModelCollection extends Collection
{


	/**
	 * Sử dụng primary key của model làm key cho collection
	 *
	 * @return App\Mytour\MytourExtends\MytourModelCollection
	 */
	public function keyByPrimaryKey()
	{
		return $this->keyBy(function($item){
			return $item->getKey();
		});
	}



	/**
	 * Sử dụng Closure trả về giá trị của từng item 
	 *
	 * @param Closure $callback
	 * @return App\Mytour\MytourExtends\MytourModelCollection
	 */
	public function valueBy(Closure $callback)
	{
		foreach($this->items as $key => $item){
			$this->items[$key] = $callback($item);
		}
		return $this;
	}


	



}