<?php namespace App\Http\Controllers\Crons;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Components\RoomServicesStatus;
use App\Models\Components\RoomPrice;
use App\Models\Components\RoomsRatePlans;
use App\Models\Components\RoomsAllotment;
use App\Models\Components\Hotel;
use App\Models\Components\Rooms;
use App\Models\Components\RatePlan;

use Illuminate\Http\Request;

use Input, View;

class ConvertCommissionController extends Controller
{
	public function __construct(
		Hotel $hotelRepo,
        Rooms $roomRepo
	)
    {
        $this->hotelRepo         = $hotelRepo;
        $this->roomRepo          = $roomRepo;

        $this->time_start        = strtotime('03/01/2016');
        $this->time_finish       = strtotime('12/01/2020');
    }

    public function convertCommissionMarkup ()
    {
    	$offset    = 100;
        $page      = (int)Input::get('page', 0);
        $time_life = 3;
        $url       = route('convert-commission');
        $page_next = $page + 1;
        $message   = 'Processing...';

        $data_next = 0;

        $params = array(
            'field_select' => ['rom_hotel', 'rom_commission_percent', 'rom_mark_up'],
            'take'         => $offset,
            'skip'         => $page * $offset,
            'group_by'	   => 'rom_hotel',
            'order_by' 	   => 'rom_hotel'
        );

        $dataRoom = $this->roomRepo->getInfoRooms($params);

        if(count($dataRoom) == 0){
        	echo 'CONVERT COMMISSISON HOTEL SUCCESS';
        	die;
        }

        foreach ($dataRoom as $key => $value) {
        	$hotel = $this->hotelRepo->find($value->rom_hotel);
        	if(!empty($hotel)){
	        	$hotel->hot_commission_ota = $value->rom_commission_percent;
	        	$hotel->hot_mark_up = $value->rom_mark_up;

	        	$hotel->save();
	        }
        }

        return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next', 'data_next'));
    }
}