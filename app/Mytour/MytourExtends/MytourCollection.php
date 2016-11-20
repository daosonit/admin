<?php 
namespace App\Mytour\MytourExtends;

use Illuminate\Support\Collection;

class MytourCollection extends Collection
{
	


	/**
	 * Modify prependItem method 
	 * @param mixed $value 
	 * @param mixed $key 
	 * @return App\Mytour\MytourExtends\MytourCollection
	 */
	public function prependItem($value, $key)
	{
		$tmpItems = [];
		if(!array_key_exists($key, $this->items)){
			$tmpItems[$key] = $value;
			foreach($this->items as $offset => $itemValue){
				$tmpItems[$offset] = $itemValue;
			}
			$this->items = $tmpItems;
		}
		return $this;
	}




}