<?php namespace App\Http\Controllers\Hotel;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;
use App\Models\Components\Hotel;
use MytourHelper;

class HotelController extends Controller {



	public function __construct(Hotel $hotel)
	{
		$this->hotel = $hotel;

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$this->userCan('hotelbooking.showlist');

		$selectFields = ['hot_name_temp', 'hot_address_temp', 'hot_id', 'hot_phone', 'hot_email'];
		$hotelQuery = $this->hotel->select($selectFields);

		// Start Queries  ======================================================
		if ($id = $request->get('id', '')) $hotelQuery->where('hot_id', $id);

		if ($name = $request->get('name', '')) $hotelQuery->where('hot_name_temp','LIKE', '%'. $name . '%');

		if ($email = $request->get('email', '')) $hotelQuery->where('hot_email', $email);

		if ($phone = $request->get('phone', '')) $hotelQuery->where('hot_phone', $phone);

		if ($star = $request->get('star', '')) $hotelQuery->where('hot_category', $star);

		//End Queries=====================================================

		
		$starRates = MytourHelper::getHotelStarCollection();
		$starRates->prepend('--Chọn Hạng Sao--', 0);

		$hotels = $hotelQuery->paginate(NUM_PER_PAGE);
		$stt = ($hotels->currentPage() - 1)*NUM_PER_PAGE;
		$hotels->each(function($item) use(&$stt){
			$item->stt = ++$stt;
		});
		return view('components.modules.hotel.index')->with(['hotels' => $hotels, 'starRates' => $starRates]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
			$hotel = $this->hotel->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('Hotel not found!', 404);
		}
		$hotel->delete();
		return redirect()->route('modules.hotels.index');
	}




	public function getTrashed(Request $request)
	{
		$selectFields 	= ['hot_name_temp', 'hot_address_temp', 'hot_id', 'hot_phone', 'hot_email'];
		$hotelQuery 	= $this->hotel->onlyTrashed()->select($selectFields);

		// Start Queries  ======================================================
		if ($id = $request->get('id', '')) $hotelQuery->where('hot_id', $id);

		if ($name = $request->get('name', '')) $hotelQuery->where('hot_name_temp','LIKE', '%'. $name . '%');

		if ($email = $request->get('email', '')) $hotelQuery->where('hot_email', $email);

		if ($phone = $request->get('phone', '')) $hotelQuery->where('hot_phone', $phone);

		if ($star = $request->get('star', '')) $hotelQuery->where('hot_category', $star);


		$starRates = MytourHelper::getHotelStarCollection();
		$starRates->prepend('--Chọn Hạng Sao--', 0);

		$hotels 		= $hotelQuery->paginate(NUM_PER_PAGE);

		$stt = ($hotels->currentPage() - 1) * NUM_PER_PAGE;
		$hotels->each(function($item) use(&$stt){
			$item->stt = ++$stt;
		});
		return view('components.modules.hotel.trashed')->with(['hotels' => $hotels, 'starRates' => $starRates]);
	}




	/**
	 * Restore the specified resource from trash.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function restore($id)
	{
		try {
			$hotel = $this->hotel->onlyTrashed()
								 ->where('hot_id', $id)
								 ->get()->first();
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('Hotel not found!', 404);
		}
		$hotel->restore();
		return redirect()->route('modules.hotels.trashed');
	}

}
