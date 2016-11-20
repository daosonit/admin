<?php namespace App\Http\Controllers\Crons;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Components\Hotel;
use App\Models\Components\RatePlan;
use App\Models\Components\Rooms;
use App\Models\Components\RoomPrice;
use App\Models\Components\RoomsRatePlans;
use App\Models\Components\Promotion;
use App\Models\Components\RoomsAllotment;

use Illuminate\Http\Request;

use Input, View, Session;

class CronsApiPmsController extends Controller {

    private $data_price_publish = [];
    private $data_price_ota_in  = [];
    private $data_price_ota_out = [];

	public function __construct(Hotel $hotel,
        RatePlan $ratePlan,
        RoomsRatePlans $roomRatePlan,
        RoomPrice $roomPrice,
        Rooms $room,
        Promotion $promotion,
        RoomsAllotment $roomAllotment)
    {
        $this->hotel         = $hotel;
        $this->room          = $room;
        $this->ratePlan      = $ratePlan;
        $this->roomPrice     = $roomPrice;
        $this->roomRatePlan  = $roomRatePlan;
        $this->promotion     = $promotion;
        $this->roomAllotment = $roomAllotment;

        $this->time_start   = strtotime('06/01/2016');
        $this->time_finish  = strtotime('12/01/2020');
    }

    public function CronRatePlanPms ()
    {
        $page      = (int) Input::get('page', 0);
        $offset    = 30;
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('cron_rate_plan_pms');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $dataHotel = $this->hotel->select(['hot_pms_link', 'hot_id'])
                                 ->where('hot_pms_active', '=', ACTIVE)
                                 ->take($offset)
                                 ->skip($skip)
                                 ->get();

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            foreach ($dataHotel as $info) {
                $data = array(
                    'api_key' => env('API_PMS_KEY'),
                    'ac'      => 'priceType',
                );

                $content         = getcontent($info->hot_pms_link . '/Api/Get', $data);
                $array_rate_pms = json_decode($content, true);

                if(count($array_rate_pms) > 0) {
                    $array_rate_pms = pushValueToKey($array_rate_pms, 'ID');
                    $rate_plan_hotel = $array_rate_pms;

                    foreach ($rate_plan_hotel as $rate_id => $info_rate) {
                        $rate_pms_id = $rate_id;
                        $rate_check  = $this->ratePlan->select('rap_id')->where('rap_pms_id', '=', $rate_pms_id)->where('rap_hotel_id', '=', $info->hot_id)->get()->toArray();

                        if(count($rate_check) == 0){

                            $rate_title  = $info_rate['price_type'];
                            $room_pms_id = [];

                            foreach ($info_rate['room_types'] as $key => $info_room_pms) {
                                $room_pms_id[] = $info_room_pms['ID'];
                            }

                            $info_id   = $this->room->select(['rom_id', 'rom_pms_room_id'])
                                                    ->where('rom_active', '=', 1)
                                                    ->where('rom_delete', '=', 0)
                                                    ->where('rom_hotel', '=', $info->hot_id)
                                                    ->whereIn('rom_pms_room_id', $room_pms_id)
                                                    ->get()->toArray();


                            $room_apply_id = [];
                            foreach ($info_id as $key => $info_room) {
                                $room_apply_id[] = $info_room['rom_id'];
                            }

                            $rate_surchage_info = [];
                            if($info_rate['surcharges'] != '') {
                                $rate_surchage_info = explode(',', $info_rate['surcharges']);
                            }
                            $surcharge_info = [];
                            if(!empty($rate_surchage_info)) {

                                if(in_array('BED', $rate_surchage_info)) {
                                    $price_bed = explode('.', $info_rate['price_bed']);
                                    $surcharge_info['add_extra_bed']    = 1;
                                    $surcharge_info['bed_extra_price']  = $price_bed[0];
                                }

                                if(in_array('ADULT', $rate_surchage_info)) {
                                    $price_person = explode('.', $info_rate['price_person']);
                                    $surcharge_info['add_extra_adult']  = 1;
                                    $surcharge_info['number_adult']     = $info_rate['max_person'];
                                    $surcharge_info['price_adult']      = $price_person[0];
                                }

                                if(in_array('CHILD', $rate_surchage_info)) {
                                    $surcharge_info['add_extra_child']  = 1;
                                    $surcharge_info['number_child']     = $info_rate['max_child'];
                                    $surcharge_info['child_adult']      = $info_rate['age_child'];
                                    $surcharge_info['min_child']        = [];
                                    $surcharge_info['max_child']        = [];
                                    $surcharge_info['extra_child']      = [];
                                    foreach ($info_rate['surcharge'] as $key => $item) {
                                        $price_age = explode('.', $item['price_age']);
                                        $surcharge_info['min_child'][]      = $item['age_from'];
                                        $surcharge_info['max_child'][]      = $item['age_to'];
                                        $surcharge_info['extra_child'][]    = $price_age[0];
                                    }
                                }
                            }

                            $cancel_policy_info = [];
                            if($info_rate['cancel_policy'] == 'CUSTOM') {
                                $cancel_policy_info['cancel_policy_info'] = [];
                                $cancel_policy_info['cancel_policy_info'][0]['day'] = $info_rate['is_time'];
                                if($info_rate['value_is_time'] == 'ONE') {
                                    $cancel_policy_info['cancel_policy_info'][0]['fee'] = 11;
                                } elseif($info_rate['value_is_time'] == 'TWO') {
                                    $cancel_policy_info['cancel_policy_info'][0]['fee'] = 12;
                                } elseif($info_rate['value_is_time'] == 'FREE') {
                                    $cancel_policy_info['cancel_policy_info'][0]['fee'] = 0;
                                } else {
                                    $cancel_policy_info['cancel_policy_info'][0]['fee'] = $info_rate['value_is_time'] / 10;
                                }

                                foreach ($info_rate['cancel_normal'] as $key => $extra_policy) {
                                    $cancel_policy_info['cancel_policy_info'][$key + 1]['day'] = $extra_policy['out_time'];

                                    if($extra_policy['value_out_time'] == 'ONE') {
                                        $cancel_policy_info['cancel_policy_info'][$key + 1]['fee'] = 11;
                                    } elseif($extra_policy['value_out_time'] == 'TWO') {
                                        $cancel_policy_info['cancel_policy_info'][$key + 1]['fee'] = 12;
                                    } elseif($extra_policy['value_out_time'] == 'FREE') {
                                        $cancel_policy_info['cancel_policy_info'][$key + 1]['fee'] = 0;
                                    } else {
                                        $cancel_policy_info['cancel_policy_info'][$key + 1]['fee'] = $extra_policy['value_out_time'] / 10;
                                    }
                                }

                                if(count($info_rate['cancel_special']) > 0) {
                                    $cancel_policy_info['peak_period'] = [];
                                    foreach ($info_rate['cancel_special'] as $key => $special) {
                                        $date = [];
                                        $date[0] = strtotime($special['date_from']);
                                        $date[1] = strtotime($special['date_to']);
                                        $cancel_policy_info['peak_period'][$key]['date'] = $date;
                                        $cancel_policy_info['peak_period'][$key][0]['day'] = $special['is_time_spec'];
                                        if($special['value_is_time_spec'] == 'ONE') {
                                            $cancel_policy_info['peak_period'][$key][0]['fee'] = 11;
                                        } elseif($special['value_is_time_spec'] == 'TWO') {
                                            $cancel_policy_info['peak_period'][$key][0]['fee'] = 12;
                                        } elseif($special['value_is_time_spec'] == 'FREE') {
                                            $cancel_policy_info['peak_period'][$key][0]['fee'] = 0;
                                        } else {
                                            $cancel_policy_info['peak_period'][$key][0]['fee'] = $special['value_is_time_spec'] / 10;
                                        }

                                        if(count($special['items']) > 0) {
                                            foreach ($special['items'][0] as $key_extra => $extra_special) {
                                                $cancel_policy_info['peak_period'][$key][$key_extra + 1]['day'] = $extra_special['out_time_spec'];

                                                if($extra_special['value_out_time_spec'] == 'ONE') {
                                                    $cancel_policy_info['peak_period'][$key][$key_extra + 1]['fee'] = 11;
                                                } elseif($extra_special['value_out_time_spec'] == 'TWO') {
                                                    $cancel_policy_info['peak_period'][$key][$key_extra + 1]['fee'] = 12;
                                                } elseif($extra_special['value_out_time_spec'] == 'FREE') {
                                                    $cancel_policy_info['peak_period'][$key][$key_extra + 1]['fee'] = 0;
                                                } else {
                                                    $cancel_policy_info['peak_period'][$key][$key_extra + 1]['fee'] = $extra_special['value_out_time_spec'] / 10;
                                                }
                                            }
                                        }
                                    }
                                }

                            }

                            $service        = [];
                            foreach ($info_rate['room_services'] as $info_service)
                            {
                                if($info_service['title'] == "Ăn sáng") $service[] = "Bữa sáng";
                                if($info_service['title'] == "Ăn trưa") $service[] = "Bữa trưa";
                                if($info_service['title'] == "Ăn tối")  $service[] = "Bữa tối";
                            }
                            $room_apply_bed = [];

                            $params = [
                                    'rap_hotel_id'              => $info->hot_id,
                                    'rap_pms_id'                => $rate_pms_id,
                                    'rap_title'                 => $rate_title,
                                    'rap_type_price'            => 1,
                                    'rap_room_apply_id'         => json_encode($room_apply_id),
                                    'rap_surcharge_info'        => json_encode($surcharge_info),
                                    'rap_cancel_policy_info'    => json_encode($cancel_policy_info),
                                    'rap_accompanied_service'   => json_encode($service),
                                    'rap_room_apply_bed'        => json_encode($room_apply_bed),
                                    'rap_active'                => $info_rate['status']
                                    ];

                            $rate_new = $this->ratePlan->create($params);

                            $rate_new->rooms()->attach($room_apply_id);
                        }
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON RATE PLAN PMS SUCCESS';die;
        }
    }

    public function CronRoomRatePricePms ()
    {
        // die;
        $page      = (int) Input::get('page', 0);
        $offset    = 5;
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('cron_room_price_pms');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $dataHotel = $this->hotel->select(['hot_id', 'hot_pms_link'])
                                 ->where('hot_pms_active', '=', ACTIVE)
                                 ->take($offset)
                                 ->skip($skip)
                                 ->get();

        $listHotelID = $dataHotel->keyBy('hot_id')->keys()->toArray();
        $dataRoom = $this->room->select(['rom_id', 'rom_pms_room_id', 'rom_hotel'])
                               ->whereIn('rom_hotel', $listHotelID)
                               ->where('rom_pms_room_id', '>' , 0)
                               ->get();

        if (is_object($dataRoom) && !$dataRoom->isEmpty()) {
            foreach ($dataRoom as $infoRoom) {
                $dataRoomPms[$infoRoom->rom_hotel][$infoRoom->rom_pms_room_id] = $infoRoom->rom_id;
            }
        }

        $dataRatePlan = $this->ratePlan->select(['rap_id', 'rap_pms_id', 'rap_hotel_id'])
                                       ->whereIn('rap_hotel_id', $listHotelID)
                                       ->where('rap_pms_id', '>' , 0)
                                       ->get();

        if (is_object($dataRatePlan) && !$dataRatePlan->isEmpty()) {
            foreach ($dataRatePlan as $infoRatePlan) {
                $dataRatePlanPms[$infoRatePlan->rap_hotel_id][$infoRatePlan->rap_pms_id] = $infoRatePlan->rap_id;
            }
        }

        for ($i = 1; $i <= 31; $i++) {
            $field_col = "rop_col" . $i;
            $dataPriceDefault[$field_col] = 0;
        }

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {

            $dataPricePms = $this->getPricePms();
            $this->setValueDefault();

            foreach ($dataHotel as $infoHotel) {
                $commission = $this->getCommissionHotelPms($infoHotel->hot_pms_link);

                $data = array(
                    'api_key' => env('API_PMS_KEY'),
                    'ac'      => 'room',
                    'df'      => '01/06/2016',
                    'dt'      => '30/06/2017'
                );

                $content      = getcontent($infoHotel->hot_pms_link . '/Api/Get', $data);
                $dataPriceApi = json_decode($content, true);

                $arr_month  = [];
                $dataPrice = [];

                if (count($dataPriceApi) > 0 && !isset($dataPriceApi['code'])) {
                    foreach ($dataPriceApi as $day => $info) {
                        $time = strtotime($day);

                        foreach ($info as $roomID => $infoRate) {
                            if (!isset($dataRoomPms[$infoHotel->hot_id][$roomID])) {
                                continue;
                            }

                            foreach ($infoRate as $rateID => $infoPrice) {
                                if (!isset($dataRatePlanPms[$infoHotel->hot_id][$rateID])) {
                                    continue;
                                }

                                if (isset($dataPricePms[$dataRoomPms[$infoHotel->hot_id][$roomID]][$dataRatePlanPms[$infoHotel->hot_id][$rateID]][date('Ym', $time)])) {
                                    continue;
                                }
                                if (!isset($dataPrice[date('Ym', $time)][$roomID][$rateID])) {
                                    $dataPrice[date('Ym', $time)][$roomID][$rateID] = $dataPriceDefault;
                                }

                                $price_ota     = (double) $infoPrice['price_ota'];
                                $price_ota_in  = $price_ota - ($price_ota * $commission) / 100;
                                $price_publish = $price_ota * 1.15;
                                $price_ota_in  = $price_ota - ($price_ota * $commission) / 100;

                                if (!isset($dataPrice[date('Ym', $time)][$roomID][$rateID]['rop_info_price_publish'])) {
                                    $dataPrice[date('Ym', $time)][$roomID][$rateID]['rop_info_price_publish']  = isset($this->data_price_publish[date('Ym', $time)]) ? $this->data_price_publish[date('Ym', $time)] : [];
                                    $dataPrice[date('Ym', $time)][$roomID][$rateID]['rop_info_price_contract'] = isset($this->data_price_ota_in[date('Ym', $time)]) ? $this->data_price_ota_in[date('Ym', $time)] : [];
                                }

                                $dataPrice[date('Ym', $time)][$roomID][$rateID]['rop_col' . date('j', $time)]     = $price_ota;
                                $dataPrice[date('Ym', $time)][$roomID][$rateID]['rop_info_price_publish'][$time]  = $price_publish;
                                $dataPrice[date('Ym', $time)][$roomID][$rateID]['rop_info_price_contract'][$time] = $price_ota_in;
                            }
                        }
                    }
                }

                if (count($dataPrice) > 0) {
                    foreach ($dataPrice as $prev_tbl => $infoRoom) {
                        $dataInsert = [];
                        foreach ($infoRoom as $roomID => $infoRate) {
                            foreach ($infoRate as $rateID => $infoPrice) {
                                $infoPrice['rop_hotel_id']     = $infoHotel->hot_id;
                                $infoPrice['rop_room_id']      = $dataRoomPms[$infoHotel->hot_id][$roomID];
                                $infoPrice['rop_rate_plan_id'] = $dataRatePlanPms[$infoHotel->hot_id][$rateID];
                                $infoPrice['rop_person_type']  = 2;
                                $infoPrice['rop_type_price']   = 1;
                                $infoPrice['rop_price_pms']    = 1;
                                $infoPrice['rop_info_price_publish']  = json_encode($infoPrice['rop_info_price_publish']);
                                $infoPrice['rop_info_price_contract'] = json_encode($infoPrice['rop_info_price_contract']);

                                $dataInsert[] = $infoPrice;
                            }
                        }

                        //Insert Price
                        $tbl_price = "room_price_" . $prev_tbl;
                        $this->roomPrice->setTable($tbl_price);
                        $this->roomPrice->insert($dataInsert);

                        echo "INSERT " . $tbl_price . " PRICE PMS HOTEL " . $infoHotel->hot_id . 'SUCCESS<br>';
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON ROOM PRICE PMS SUCCESS';die;
        }
    }

    public function CronRoomAllotmentPms ()
    {
        // die;
        $page      = (int) Input::get('page', 0);
        $offset    = 5;
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('cron_room_allotment_pms');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $dataHotel = $this->hotel->select(['hot_id', 'hot_pms_link'])
                                 ->where('hot_pms_active', '=', ACTIVE)
                                 ->take($offset)
                                 ->skip($skip)
                                 ->get();

        $listHotelID = $dataHotel->keyBy('hot_id')->keys()->toArray();
        $dataRoom = $this->room->select(['rom_id', 'rom_pms_room_id', 'rom_hotel'])
                               ->whereIn('rom_hotel', $listHotelID)
                               ->where('rom_pms_room_id', '>' , 0)
                               ->get();

        if (is_object($dataRoom) && !$dataRoom->isEmpty()) {
            foreach ($dataRoom as $infoRoom) {
                $dataRoomPms[$infoRoom->rom_hotel][$infoRoom->rom_pms_room_id] = $infoRoom->rom_id;
            }
        }

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            $dataAllotmentPms = $this->getAllotmentPms($listHotelID);

            foreach ($dataHotel as $infoHotel) {
                // Lấy thông tin inventory
                $data = [
                    'api_key'    => env('API_PMS_KEY'),
                    'type'       => 'inventorydetail',
                    'date_start' => '01/07/2016',
                    'date_end'   => '01/01/2018'
                ];

                $content       = getcontent($infoHotel->hot_pms_link . '/Api/Index', $data);
                $dataAllotment = json_decode($content, true);

                if (isset($dataAllotment['data']['rooms']) > 0
                    && !isset($dataAllotment['code'])) {
                    $dataInsert = [];
                    foreach ($dataAllotment['data']['rooms'] as $day => $infoAllotment) {
                        $dayInt = strtotime($day);
                        foreach ($infoAllotment as $rIDPms => $info) {
                            if (!isset($dataRoomPms[$infoHotel->hot_id][$rIDPms])) {
                                continue;
                            }

                            if (isset($dataAllotmentPms[date('Ym', $dayInt)][$dataRoomPms[$infoHotel->hot_id][$rIDPms]][$dayInt])) {
                                continue;
                            }

                            $rID = $dataRoomPms[$infoHotel->hot_id][$rIDPms];
                            $dataInsert[$rID][date('Ym', $dayInt)][] = [
                                'roa_hotel_id'            => $infoHotel->hot_id,
                                'roa_room_id'             => $rID,
                                'roa_status'              => 1,
                                'roa_allotment_ota'       => $info['inventory'],
                                'roa_allotment_ta'        => 0,
                                'roa_check_allotment_pms' => 1,
                                'roa_time'                => $dayInt
                            ];
                        }
                    }

                    if (count($dataInsert) > 0) {
                        foreach ($dataInsert as $rID => $infoAllot) {
                            foreach ($infoAllot as $prev_tbl => $info) {
                                $tbl_allotment = 'rooms_allotment_' . $prev_tbl;
                                $this->roomAllotment->setTable($tbl_allotment);
                                $this->roomAllotment->insert($info);
                            }
                        }
                    }

                    echo "CRON ALLOTMENT HOTEL PMS SUCCESS" . $infoHotel->hot_id . '<br>';
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON ROOM ALLOTMENT PMS SUCCESS';die;
        }
    }

    private function getCommissionHotelPms ($link_pms)
    {
        $discount = 0;
        $data = array(
                'api_key' => env('API_PMS_KEY'),
                'ac'      => 'partner'
            );

        $content   = getcontent($link_pms . '/Api/Get', $data);
        $array_pms = json_decode($content, true);

        if (isset($array_pms['discount'])) {
            if ($array_pms['currency'] == '%') {
                $discount_pms = explode('.', $array_pms['discount']);
                $discount     = $discount_pms[0];
            }
        }

        return $discount;
    }

    private function setValueDefault ()
    {
        for ($i = strtotime('06/01/2016'); $i <= strtotime('06/30/2017'); $i += 86400 * 31) {
            $day_first_month = strtotime(date('Ym01', $i));
            $day_last_month  = strtotime(date('Ymt', $i));

            for ($d = $day_first_month; $d <= $day_last_month; $d += 86400) {
                $this->data_price_publish[date('Ym', $i)][$d] = 0;
                $this->data_price_ota_in[date('Ym', $i)][$d]  = 0;
            }
        }
    }

    public function cronPromotionPms ()
    {
        // die;
        $page      = (int) Input::get('page', 0);
        $offset    = 10;
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('cron_promotion_pms');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $dataHotel = $this->hotel->select(['hot_id', 'hot_pms_link'])
                                 ->where('hot_pms_active', '=', ACTIVE)
                                 ->take($offset)
                                 ->skip($skip)
                                 ->get();

        $listHotelID = $dataHotel->keyBy('hot_id')->keys()->toArray();
        $dataRoom = $this->room->select(['rom_id', 'rom_pms_room_id', 'rom_hotel'])
                               ->whereIn('rom_hotel', $listHotelID)
                               ->where('rom_pms_room_id', '>' , 0)
                               ->get();

        if (is_object($dataRoom) && !$dataRoom->isEmpty()) {
            foreach ($dataRoom as $infoRoom) {
                $dataRoomPms[$infoRoom->rom_hotel][$infoRoom->rom_pms_room_id] = $infoRoom->rom_id;
            }
        }

        $dataRatePlan = $this->ratePlan->select(['rap_id', 'rap_pms_id', 'rap_hotel_id'])
                                       ->whereIn('rap_hotel_id', $listHotelID)
                                       ->where('rap_pms_id', '>' , 0)
                                       ->get();

        if (is_object($dataRatePlan) && !$dataRatePlan->isEmpty()) {
            foreach ($dataRatePlan as $infoRatePlan) {
                $dataRatePlanPms[$infoRatePlan->rap_hotel_id][$infoRatePlan->rap_pms_id] = $infoRatePlan->rap_id;
            }
        }

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            foreach ($dataHotel as $infoHotel) {
                // Lấy thông tin iventory
                $data = [
                    'api_key' => env('API_PMS_KEY'),
                    'ac'      => 'promotion'
                ];

                $content       = getcontent($infoHotel->hot_pms_link . '/Api/Get', $data);
                $dataPromotion = json_decode($content, true);

                if (count($dataPromotion) > 0) {
                    foreach ($dataPromotion as $infoPromo) {
                        $dataPromoMytour = $this->promotion->where('proh_pms_id', '=', $infoPromo['ID'])
                                                           ->where('proh_hotel', '=', $infoHotel->hot_id)
                                                           ->get()->first();

                        if (!is_null($dataPromoMytour)) {
                            continue;
                        }

                        $listRoomApply    = [];
                        $listRoomApplyPms = array_keys($infoPromo['room_types']);
                        foreach ($listRoomApplyPms as $rIDPms) {
                            if (isset($dataRoomPms[$infoHotel->hot_id][$rIDPms])) {
                                $listRoomApply[] = $dataRoomPms[$infoHotel->hot_id][$rIDPms];
                            }
                        }

                        $listRateApply    = [];
                        $listRateApplyPms = array_keys($infoPromo['price_types']);
                        foreach ($listRateApplyPms as $rateIDPms) {
                            if (isset($dataRatePlanPms[$infoHotel->hot_id][$rateIDPms])) {
                                $listRateApply[] = $dataRatePlanPms[$infoHotel->hot_id][$rateIDPms];
                            }
                        }

                        $dataIdRomRate = [];
                        if (count($listRoomApply) > 0 && count($listRateApply) > 0) {
                            $dataRomRate = $this->roomRatePlan->whereIn('rrp_room_id', $listRoomApply)
                                                              ->whereIn('rrp_rate_plan_id', $listRateApply)
                                                              ->get();

                            if (is_object($dataRomRate) && !$dataRomRate->isEmpty()) {
                                foreach ($dataRomRate as $infoRomRate) {
                                    $dataIdRomRate[] = $infoRomRate->rrp_id;
                                }
                            }
                        }

                        $dataInsert = $this->ConvertPromotionPMSToMytour($infoHotel->hot_id, $infoPromo);
                        $newPromotion = $this->promotion->create($dataInsert);
                        if (count($dataIdRomRate) > 0) {
                            $newPromotion->roomRatePlans()->attach($dataIdRomRate);
                        }

                        echo 'CRON PROMOTION SUCCESS ' . $infoPromo['ID'] . '<br>';
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON PROMOTION PMS SUCCESS';die;
        }

    }

    /**
     * [ConvertPromotionPMSToMytour description]
     * Thay doi kieu KM tu PMS sang Mytour
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    private function ConvertPromotionPMSToMytour ($hotelID, $promo_pmsInfo)
    {
        $dataPromo                        = [];
        $promo_pmsInfo['hotel_id']        = $hotelID;
        $promo_pmsInfo['ID']              = $promo_pmsInfo['ID'];
        $promo_pmsInfo['date_start']      = strtotime($promo_pmsInfo['date_start']);
        $promo_pmsInfo['date_end']        = strtotime($promo_pmsInfo['date_end']);
        $promo_pmsInfo['date_start_book'] = isset($promo_pmsInfo['date_start_book']) ? strtotime($promo_pmsInfo['date_start_book']) : "";
        $promo_pmsInfo['date_end_book']   = isset($promo_pmsInfo['date_end_book']) ? strtotime($promo_pmsInfo['date_end_book']) : "";

        $type           = $promo_pmsInfo['type'];
        $proh_min_night = isset($promo_pmsInfo['min_night']) ? $promo_pmsInfo['min_night'] : 0;
        $proh_max_night = isset($promo_pmsInfo['max_night']) ? $promo_pmsInfo['max_night'] : 0;

        $day_deny = [];
        $day_deny_pms = $promo_pmsInfo['date_no_add'];
        if (count($day_deny_pms) > 0) {
            foreach ($day_deny_pms as $day) {
                $day_deny[] = strtotime($day);
            }
        }

        $currency = 0;
        if (isset($promo_pmsInfo['currency'])) {
            $currency = $this->changeCurrencyPMS($promo_pmsInfo['currency']);
            $proh_discount_type = 2;
            if ($currency == 0) {
                $proh_discount_type = 1;
            }
        }

        $promotion_discount     = [];
        $promotion_discount_pms = [];
        if (isset($promo_pmsInfo['days'])) {
            $promotion_discount_pms = json_decode($promo_pmsInfo['days'], true);
        }
        if (count($promotion_discount_pms) > 0) {
            foreach ($promotion_discount_pms as $dayWeek => $discount) {
                $discount = (double) $discount;
                if ($proh_discount_type == 1) {
                    $discount = (int) $discount;
                }
                $promotion_discount[$dayWeek] = $discount;
            }
        }

        $cancel_policy_pms = 0;
        if($promo_pmsInfo['cancel_policy'] == "Giữ nguyên chính sách hủy của đơn giá áp dụng") {
            $cancel_policy_pms = 1;
        }

        switch ($type) {
            case 'FREE':
                $dataPromo = [
                    'proh_pms_id'              => $promo_pmsInfo['ID'],
                    'proh_title'               => $promo_pmsInfo['title'],
                    'proh_hotel'               => $promo_pmsInfo['hotel_id'],
                    'proh_time_start'          => $promo_pmsInfo['date_start'],
                    'proh_time_finish'         => $promo_pmsInfo['date_end'],
                    'proh_time_book_start'     => $promo_pmsInfo['date_start_book'],
                    'proh_time_book_finish'    => $promo_pmsInfo['date_end_book'],
                    'proh_min_night'           => $proh_min_night,
                    'proh_max_night'           => $proh_max_night,
                    'proh_free_night'          => 0,
                    'proh_type'                => 3,
                    'proh_discount_type'       => 4,
                    'proh_free_night_num'      => $promo_pmsInfo['stay_night'],
                    'proh_free_night_discount' => $promo_pmsInfo['free_night'],
                    'proh_day_deny'            => $day_deny,
                    'proh_day_apply'           => [],
                    'proh_promotion_info'      => [],
                    'proh_min_day_before'      => 0,
                    'proh_max_day_before'      => 0,
                    'proh_cancel_policy'       => $cancel_policy_pms,
                    'proh_promo_type'          => 1,
                    'proh_active'              => $promo_pmsInfo['status'],
                ];
                break;

            case 'LAST':
                $dataPromo = [
                    'proh_pms_id'           => $promo_pmsInfo['ID'],
                    'proh_title'            => $promo_pmsInfo['title'],
                    'proh_hotel'            => $promo_pmsInfo['hotel_id'],
                    'proh_time_start'       => $promo_pmsInfo['date_start'],
                    'proh_time_finish'      => $promo_pmsInfo['date_end'],
                    'proh_time_book_start'  => 0,
                    'proh_time_book_finish' => 0,
                    'proh_max_day_before'    => $promo_pmsInfo['intval_day'],
                    'proh_min_night'        => $promo_pmsInfo['min_night'],
                    'proh_max_night'        => $promo_pmsInfo['max_night'],
                    'proh_min_day_before'   => 0,
                    'proh_type'             => 2,
                    'proh_free_night'       => 0,
                    'proh_discount_type'    => $proh_discount_type,
                    'proh_promotion_info'   => $promotion_discount,
                    'proh_currency'         => $currency,
                    'proh_day_deny'         => $day_deny,
                    'proh_day_apply'        => [],
                    'proh_cancel_policy'    => $cancel_policy_pms,
                    'proh_promo_type'       => 1,
                    'proh_active'           => $promo_pmsInfo['status'],
                ];
                break;

            case 'EARLY':
                $dataPromo = [
                    'proh_pms_id'           => $promo_pmsInfo['ID'],
                    'proh_title'            => $promo_pmsInfo['title'],
                    'proh_hotel'            => $promo_pmsInfo['hotel_id'],
                    'proh_time_start'       => $promo_pmsInfo['date_start'],
                    'proh_time_finish'      => $promo_pmsInfo['date_end'],
                    'proh_time_book_start'  => 0,
                    'proh_time_book_finish' => 0,
                    'proh_min_day_before'   => $promo_pmsInfo['intval_day'],
                    'proh_min_night'        => $promo_pmsInfo['min_night'],
                    'proh_max_night'        => $promo_pmsInfo['max_night'],
                    'proh_type'             => 1,
                    'proh_free_night'       => 0,
                    'proh_max_day_before'   => 0,
                    'proh_discount_type'    => $proh_discount_type,
                    'proh_promotion_info'   => $promotion_discount,
                    'proh_cancel_policy'    => $cancel_policy_pms,
                    'proh_currency'         => $currency,
                    'proh_day_deny'         => $day_deny,
                    'proh_day_apply'        => [],
                    'proh_promo_type'       => 1,
                    'proh_active'           => $promo_pmsInfo['status'],
                ];
                break;

            case 'NORMAL':
                $dataPromo = [
                    'proh_pms_id'           => $promo_pmsInfo['ID'],
                    'proh_title'            => $promo_pmsInfo['title'],
                    'proh_hotel'            => $promo_pmsInfo['hotel_id'],
                    'proh_time_start'       => $promo_pmsInfo['date_start'],
                    'proh_time_finish'      => $promo_pmsInfo['date_end'],
                    'proh_time_book_start'  => $promo_pmsInfo['date_start_book'],
                    'proh_time_book_finish' => $promo_pmsInfo['date_end_book'],
                    'proh_min_night'        => $promo_pmsInfo['min_night'],
                    'proh_max_night'        => $promo_pmsInfo['max_night'],
                    'proh_min_day_before'   => 0,
                    'proh_free_night'       => 0,
                    'proh_max_day_before'   => 0,
                    'proh_type'             => 3,
                    'proh_discount_type'    => $proh_discount_type,
                    'proh_promotion_info'   => $promotion_discount,
                    'proh_currency'         => $currency,
                    'proh_day_deny'         => $day_deny,
                    'proh_day_apply'        => [],
                    'proh_cancel_policy'    => $cancel_policy_pms,
                    'proh_promo_type'       => 1,
                    'proh_active'           => $promo_pmsInfo['status'],
                ];
                break;

        }

        return $dataPromo;
    }

    /**
     * [changeCurrencyPMS description]
     * Chuyen doi currency PMS sang ID Mytour
     * @param  [type] $currency [description]
     * @return [type]           [description]
     */
    private function changeCurrencyPMS ($currency)
    {
        switch ($currency) {
            case '%':
                $id_currency = 0;
                break;

            case 'VND':
                $id_currency = 1;
                break;

            case 'USD':
                $id_currency = 2;
                break;

            case 'JPY':
                $id_currency = 3;
                break;

            case 'EUR':
                $id_currency = 5;
                break;

            default:
                $id_currency = 1;
        }

        return $id_currency;
    }

    private function getAllotmentPms ($listHotelID)
    {
        $dataAllotmentPms = [];
        for ($y = 2016; $y <= 2018; $y++) {
            for ($m = 1; $m <= 12; $m++) {
                if ($m < 10) $m = "0" . $m;
                $tbl_allotment = 'rooms_allotment_' . $y.$m;
                $this->roomAllotment->setTable($tbl_allotment);
                $dataAllotmentMytour = $this->roomAllotment->join('rooms', 'roa_room_id', '=', 'rom_id')
                                                           ->where('rom_pms_room_id', '>', 0)
                                                           ->whereIn('rom_hotel', $listHotelID)
                                                           ->where('roa_check_allotment_pms', '=', ACTIVE)
                                                           ->get();

                if (is_object($dataAllotmentMytour) && !$dataAllotmentMytour->isEmpty()) {
                    foreach ($dataAllotmentMytour as $infoAllot) {
                        $dataAllotmentPms[$y.$m][$infoAllot->roa_room_id][$infoAllot->roa_time] = 1;
                    }
                }
            }
        }

        return $dataAllotmentPms;
    }

    private function getPricePms ()
    {
        $dataPricePms = [];
        for ($y = 2016; $y <= 2017; $y++) {
            for ($m = 1; $m <= 12; $m++) {
                if ($m < 10) $m = "0" . $m;
                $tbl_price = 'room_price_' . $y.$m;
                $this->roomPrice->setTable($tbl_price);
                $dataPriceMytour = $this->roomPrice->where('rop_price_pms', '>', 0)
                                                   ->get();

                if (is_object($dataPriceMytour) && !$dataPriceMytour->isEmpty()) {
                    foreach ($dataPriceMytour as $infoPrice) {
                        $dataPricePms[$infoPrice->rop_room_id][$infoPrice->rop_rate_plan_id][$y.$m] = 1;
                    }
                }
            }
        }

        return $dataPricePms;
    }

    public function updatePromotionLastPms ()
    {
        $page      = (int) Input::get('page', 0);
        $offset    = 10;
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('update_promotion_last_pms');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $dataHotel = $this->hotel->select(['hot_id', 'hot_pms_link'])
                                 ->where('hot_pms_active', '=', ACTIVE)
                                 ->take($offset)
                                 ->skip($skip)
                                 ->get();

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            foreach ($dataHotel as $infoHotel) {
                // Lấy thông tin iventory
                $data = [
                    'api_key' => env('API_PMS_KEY'),
                    'ac'      => 'promotion'
                ];

                $content       = getcontent($infoHotel->hot_pms_link . '/Api/Get', $data);
                $dataPromotion = json_decode($content, true);

                if (count($dataPromotion) > 0) {

                    foreach ($dataPromotion as $infoPromo) {
                        if ($infoPromo['ID'] != 22) continue;
                        $day_deny = [];
                        $day_deny_pms = $infoPromo['date_no_add'];
                        if (count($day_deny_pms) > 0) {
                            foreach ($day_deny_pms as $day) {
                                if (!empty($day)) {
                                    $day_deny[] = strtotime($day);
                                }
                            }
                        }
                        $day_deny = json_encode($day_deny);

                        if ($infoPromo['type'] == 'LAST') {
                            $proh_max_day_before = isset($infoPromo['intval_day']) ? $infoPromo['intval_day'] : 0;
                            $proh_time_start     = isset($infoPromo['date_start']) ? strtotime($infoPromo['date_start']) : "";
                            $proh_time_finish    = isset($infoPromo['date_end']) ? strtotime($infoPromo['date_end']) : "";

                            $dataUpdate = [
                                'proh_max_day_before'   => $proh_max_day_before,
                                'proh_time_start'       => $proh_time_start,
                                'proh_time_finish'      => $proh_time_finish,
                                'proh_time_book_start'  => "",
                                'proh_time_book_finish' => "",
                                'proh_day_deny'         => $day_deny
                            ];

                            $this->promotion->where('proh_pms_id', '=', $infoPromo['ID'])
                                            ->where('proh_hotel', '=', $infoHotel->hot_id)
                                            ->limit(1)
                                            ->update($dataUpdate);

                        } else {
                            $dataUpdate = ['proh_day_deny' => $day_deny];
                            $this->promotion->where('proh_pms_id', '=', $infoPromo['ID'])
                                                ->where('proh_hotel', '=', $infoHotel->hot_id)
                                                ->limit(1)
                                                ->update($dataUpdate);
                        }

                        echo 'UPDATE PROMOTION LAST PMS SUCCESS ' . $infoPromo['ID'] . '<br>';
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'UPDATE PROMOTION LAST PMS SUCCESS';die;
        }
    }

    public function updateAllotmentPms ()
    {
        $page      = (int) Input::get('page', 0);
        $offset    = 5;
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('update_room_allotment_pms');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $dataHotel = $this->hotel->select(['hot_id', 'hot_pms_link'])
                                 ->where('hot_pms_active', '=', ACTIVE)
                                 ->take($offset)
                                 ->skip($skip)
                                 ->get();

        $listHotelID = $dataHotel->keyBy('hot_id')->keys()->toArray();
        $dataRoom = $this->room->select(['rom_id', 'rom_pms_room_id', 'rom_hotel'])
                               ->whereIn('rom_hotel', $listHotelID)
                               ->where('rom_pms_room_id', '>' , 0)
                               ->get();

        if (is_object($dataRoom) && !$dataRoom->isEmpty()) {
            foreach ($dataRoom as $infoRoom) {
                $dataRoomPms[$infoRoom->rom_hotel][$infoRoom->rom_pms_room_id] = $infoRoom->rom_id;
            }
        }

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            $dataAllotmentPms = $this->getAllotmentPms($listHotelID);

            foreach ($dataHotel as $infoHotel) {
                // Lấy thông tin inventory
                $data = [
                    'api_key'    => env('API_PMS_KEY'),
                    'type'       => 'inventorydetail',
                    'date_start' => '01/06/2016',
                    'date_end'   => '01/01/2018'
                ];

                $content       = getcontent($infoHotel->hot_pms_link . '/Api/Index', $data);
                $dataAllotment = json_decode($content, true);

                if (isset($dataAllotment['data']['rooms']) > 0
                    && !isset($dataAllotment['code'])) {
                    $dataInsert = [];
                    $dataUpdate = [];

                    foreach ($dataAllotment['data']['rooms'] as $day => $infoAllotment) {
                        $dayInt = strtotime($day);
                        foreach ($infoAllotment as $rIDPms => $info) {
                            if (!isset($dataRoomPms[$infoHotel->hot_id][$rIDPms])) {
                                continue;
                            }
                            $rID = $dataRoomPms[$infoHotel->hot_id][$rIDPms];
                            if (!isset($dataAllotmentPms[date('Ym', $dayInt)][$rID][$dayInt])) {
                                $dataInsert[$rID][date('Ym', $dayInt)][] = [
                                    'roa_hotel_id'            => $infoHotel->hot_id,
                                    'roa_room_id'             => $rID,
                                    'roa_status'              => 1,
                                    'roa_allotment_ota'       => $info['inventory'],
                                    'roa_allotment_ta'        => 0,
                                    'roa_check_allotment_pms' => 1,
                                    'roa_time'                => $dayInt,
                                    'roa_total_allotment_pms' => $info['total']
                                ];
                            } else {
                                $dataUpdate[$rID][date('Ym', $dayInt)][] = [
                                    'roa_hotel_id'            => $infoHotel->hot_id,
                                    'roa_room_id'             => $rID,
                                    'roa_allotment_ota'       => $info['inventory'],
                                    'roa_time'                => $dayInt,
                                    'roa_total_allotment_pms' => $info['total']
                                ];
                            }
                        }
                    }

                    if (count($dataUpdate) > 0) {
                        foreach ($dataUpdate as $rID => $infoAllot) {
                            foreach ($infoAllot as $prev_tbl => $info) {
                                $tbl_allotment = "rooms_allotment_" . $prev_tbl;
                                $sql_update = "UPDATE {$tbl_allotment} SET roa_allotment_ota = CASE ";
                                $check_update    = 0;
                                $total_allotment = 0;
                                foreach ($info as $infoUpdate) {
                                    $sql_update .= " WHEN roa_time = " . $infoUpdate['roa_time'] . " THEN " . $infoUpdate['roa_allotment_ota'];
                                    $total_allotment = $infoUpdate['roa_total_allotment_pms'];
                                }
                                $sql_update .= " ELSE roa_allotment_ota END ";
                                $sql_update .= ", roa_total_allotment_pms = {$total_allotment} ";

                                $sql_update .= " WHERE roa_room_id = {$rID} AND roa_hotel_id = {$infoHotel->hot_id} AND roa_check_allotment_pms = 1";
                                $params = [
                                    'table'        => $tbl_allotment,
                                    'query_update' => $sql_update
                                ];
                                $this->roomAllotment->updateAllotmentRawQuery($params);
                            }
                            echo "UPDATE HOTEL PMS SUCCESS " . $infoHotel->hot_id . '<br>';
                        }

                        if (count($dataInsert) > 0) {
                            foreach ($dataInsert as $rID => $infoAllot) {
                                foreach ($infoAllot as $prev_tbl => $info) {
                                    $tbl_allotment = 'rooms_allotment_' . $prev_tbl;
                                    $this->roomAllotment->setTable($tbl_allotment);
                                    $this->roomAllotment->insert($info);
                                }

                                echo "INSERT HOTEL PMS SUCCESS " . $infoHotel->hot_id . '<br>';
                            }
                        }
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'UPDATE ALLOTMENT PMS SUCCESS';die;
        }
    }


    public function insertAllotmentFail ($hotel_id, $time, $pms)
    {
        $dayInt = strtotime($time);
        $table = "rooms_allotment_" . date('Ym', $dayInt);
        $this->roomAllotment->setTable($table);
        $dataAllotment = $this->roomAllotment->where('roa_hotel_id', '=', $hotel_id)
                                             ->where('roa_check_allotment_pms', '=', $pms)
                                             ->get();

        if (is_object($dataAllotment) && !$dataAllotment->isEmpty()) {
            $time_start  = strtotime(date('01-m-Y', $dayInt));
            $time_finish = strtotime(date('t-m-Y', $dayInt));
            $arr_time    = [];
            $dataInsert  = [];
            $dataDefault = [];

            $dataRoom = $this->room->select('rom_id')
                                   ->where('rom_hotel', '=', $hotel_id)
                                   ->where('rom_active', '=', 1)
                                   ->where('rom_delete', '=', 0)
                                   ->get();

           for ($d = $time_start; $d <= $time_finish; $d += 86400) {
                $arr_time[] = $d;
                foreach ($dataRoom as $infoRoom) {
                    $dataDefault[$infoRoom->rom_id][$d] = [
                        'roa_hotel_id'            => $hotel_id,
                        'roa_room_id'             => $infoRoom->rom_id,
                        'roa_status'              => 1,
                        'roa_allotment_ota'       => 0,
                        'roa_allotment_ta'        => 0,
                        'roa_check_allotment_pms' => $pms,
                        'roa_time'                => $d
                    ];
                }
            }

            foreach ($dataAllotment as $infoAllotment) {
                if (in_array($infoAllotment->roa_time, $arr_time)) {
                    unset($dataDefault[$infoAllotment->roa_room_id][$infoAllotment->roa_time]);
                }
            }

            if (count($dataDefault) > 0) {
                foreach ($dataDefault as $rID => $infoInsert) {
                    if (count($infoInsert)) {
                        $this->roomAllotment->setTable($table);
                        $this->roomAllotment->insert($infoInsert);
                        echo 'INSERT ALLOTMENT SUCCESS';
                    }
                }
            }
        }
    }
}
