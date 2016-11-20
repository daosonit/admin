<?php 

namespace App\Http\Controllers\HotelBooking;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Components\HotelBooking;

use Illuminate\Http\Request;



class HotelBookingController extends Controller {



	public function __construct(HotelBooking $hotelBooking)
	{
		$this->hotelBooking = $hotelBooking;
	}





	public function showList(Request $request)
	{
		$query = $this->hotelBooking->with('city', 'hotel', 'hotel.city');

		// Start Queries  ======================================================
		if ($id = $request->get('id', '')) $query->where('boo_id', $id);

		if ($code = $request->get('code', '')) $query->where('boo_code', $code);

		if ($email = $request->get('email', '')) $query->where('boo_customer_email', $email);

		if ($phone = $request->get('phone', '')) $query->where('boo_customer_phone', $phone);

		if (($state = $request->get('state', -1)) >= 0) $query->where('boo_view', $state);

		//End Queries=====================================================

		$bookings = $query->orderBy($this->hotelBooking->getKeyName(), 'DESC')->paginate(NUM_PER_PAGE);
		
		return view('components.modules.hotel-booking.index')->with('bookings', $bookings);
	}

}
