<?php namespace App\Http\Controllers\AjaxControllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Components\Hotel;

class HotelSuggestionController extends Controller {

	//



	public function __construct(Hotel $hotel)
	{
		$this->hotel = $hotel;
	}


	public function getHotelSuggestion(Request $request)
	{
		$hotels =  collect([]);
		if ($keyword = $request->get('q', ''))
		{
			$hotels = $this->hotel->select(['hot_id as id', 'hot_name_temp as name'])
								  ->active()
								  ->seachByName($keyword)
								  ->get();
		}
		return $hotels->toJson();
	}




}
