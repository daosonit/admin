<?php namespace App\Http\Controllers\Crons;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Components\HotelBooking;

use Illuminate\Http\Request;

use Input, View;

class ConvertPolicyBookingController extends Controller
{
	public function __construct(
		HotelBooking $hotelBooking
	)
    {
        $this->hotelBooking      = $hotelBooking;

        $this->time_start        = strtotime('18/06/2016');
        $this->time_finish       = strtotime('03:00:00 21-06-2016');
    }

    public function convertPolicyBookingOld ()
    {
    	$offset    = 50;
        $page      = (int)Input::get('page', 0);
        $time_life = 3;
        $url       = route('update-policy-booking');
        $page_next = $page + 1;
        $message   = 'Processing...';

        $data_next = 0;

        $params = array(
            'field_select' => ['boo_id', 'boo_book_info', 'boo_check_voucher_policy'],
            'time_start'   => $this->time_start,
            'time_finish'  => $this->time_finish,
            'take'         => $offset,
            'skip'         => $page * $offset
        );

        $dataBooking = $this->hotelBooking->getInfoBookingCron($params);

        if(count($dataBooking) == 0){
        	echo 'CONVERT POLICY BOOKING HOTEL SUCCESS';
        	die;
        }

        foreach ($dataBooking as $key => $booking) {
        	$booking_info = json_decode($booking->boo_book_info, true);
            if(!empty($booking_info)) {
                foreach ($booking_info as $roomId => $room) {
                    $update = 0;
                    if(isset($room['rate_info'])){
                        $update = 1;
                        foreach ($room['rate_info'] as $rateId => $rate) {
                            $policy = [];

                            if($booking->boo_check_voucher_policy == 1) {
                                $policy['type_policy'] = 1;
                            } else {                   
                                $policy['type_policy'] = 2;
                                $policy['content_policy'] = [
                                                                    0 => ['day' => 3,  'fee' => 10],
                                                                    1 => ['day' => 3,  'fee' => 5],
                                                                    2 => ['day' => 7,  'fee' => 0]
                                                                ];
                                if($rate['numroom'] >= 5) {
                                    $policy['type_policy_group'] = 2;
                                    $policy['content_policy_group'] = [  'numroom' => 5,
                                                                           0 => ['day' => 7,  'fee' => 10],
                                                                           1 => ['day' => 7,  'fee' => 5],
                                                                           2 => ['day' => 15, 'fee' => 0]
                                                                        ];
                                }
                            }

                            $booking_info[$roomId]['rate_info'][$rateId]['cancel_policy_info_new'] = $policy;
                        }
                    }
                }

                if($update == 1){
                    $booking->boo_book_info = json_encode($booking_info);
                    $booking->save();
                }
            }
        }

        return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next', 'data_next'));
    }
}