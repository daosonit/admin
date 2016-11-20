<?php namespace App\Http\Controllers\AjaxControllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Components\City;


class CitySuggestionController extends Controller {

	//


	public function __construct(City $city)
	{
		$this->city = $city;
	}

	public function getCitySuggestion(Request $request)
	{
 		$cities = collect([]);
 		if($keyword = $request->get('q', '')){
 			$cities = $this->city->select(['cou_id as id', 'cou_name as name'])
								->vietNam()->active()
								->searchByName($keyword)
								->get();
 		}
 		return $cities->toJson();	
 	}

}
