<?php namespace App\Http\Controllers\Crons;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Components\RoomServicesStatus;
use App\Models\Components\RoomPrice;
use App\Models\Components\RoomsRatePlans;
use App\Models\Components\RoomsAllotment;
use App\Models\Components\Hotel;
use App\Models\Components\HotelBooking;
use App\Models\Components\Rooms;
use App\Models\Components\RatePlan;
use App\Models\Components\RoomConveniences;

use Illuminate\Http\Request;

use Input, View, Session, DB, Log;

class ConvertRatePlanController extends Controller
{

    private $listRateOfRoom      = [];
    private $listRateOfRoomBreakfast      = [];
    private $listPriceOfRoom     = [];
    private $listAllotmentOfRoom = [];
    private $listConvenience     = [];

    public function __construct(Hotel $hotelRepo
        , Rooms $roomRepo
        , RatePlan $rateRepo
        , RoomServicesStatus $roomServices
        , RoomsAllotment $roomAllotmentRepo
        , RoomPrice $roomPrice
        , RoomsRatePlans $roomRateRepo
        , HotelBooking $hotelBooking
        , RoomConveniences $roomConveniences)
    {
        $this->hotelRepo         = $hotelRepo;
        $this->roomRepo          = $roomRepo;
        $this->rateRepo          = $rateRepo;
        $this->roomPrice         = $roomPrice;
        $this->hotelBooking      = $hotelBooking;
        $this->roomRateRepo      = $roomRateRepo;
        $this->roomServices      = $roomServices;
        $this->roomAllotmentRepo = $roomAllotmentRepo;
        $this->roomConveniences  = $roomConveniences;

        $this->time_start        = strtotime('01/01/2014');
        $this->time_finish       = strtotime('12/31/2017');
    }

    public function createRatePlan ()
    {
        die;
        $offset    = 50;
        $page      = (int)Input::get('page', 0);
        $time_life = 3;
        $url       = route('create_rate_plan');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $params = array(
            'field_select' => ['hot_id', 'hot_required_hidden_price', 'hot_ota_hotel', 'hot_ota_hybrid', 'hot_ota_only'],
            'take'         => $offset,
            'skip'         => $page * $offset,
            'order_by_asc' => 'hot_id'
        );

        $dataHotel   = $this->hotelRepo->getInfoHotel($params);
        $listHotelID = $dataHotel->keyBy('hot_id')->keys()->toArray();

        $dataRoom = $this->roomRepo->select(['rom_id', 'rom_hotel'])
                                   ->whereIn('rom_hotel', $listHotelID)
                                   ->get();

        $listRoomID = $dataRoom->keyBy('rom_id')->keys()->toArray();
        $this->getInfoRoomConvenience($listRoomID);
        $this->getRateByRoomId($listRoomID);

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            //Get Room Of Hotel
            $dataRoomOfHotel = [];
            if (!$dataRoom->isEmpty()) {
                foreach ($dataRoom as $infoRoom) {
                    $dataRoomOfHotel[$infoRoom->rom_hotel][] = $infoRoom->rom_id;
                }
            }

            if (!empty ($dataRoomOfHotel)) {
                //get hidden price of room
                $data_hidden_price = $this->getHiddenPriceOfRoom($listRoomID);
                $rap_title_ta            = 'TA_Room_Only';
                $rap_title_ta_breakfast  = 'TA_Room_Breakfast';
                $rap_title_ota           = 'OTA_Room_Only';
                $rap_title_ota_breakfast = 'OTA_Room_Breakfast';

                foreach ($dataHotel as $infoHotel) {
                    $hID = $infoHotel->hot_id;
                    $dataInsertRoomRatePlanTa           = [];
                    $dataInsertRoomRatePlanOta          = [];
                    $dataInsertRoomRatePlanTaBreakfast  = [];
                    $dataInsertRoomRatePlanOtaBreakfast = [];

                    if (isset($dataRoomOfHotel[$hID]) && !empty($dataRoomOfHotel[$hID])) {
                        $check_exist_rate_ta  = 0;
                        $check_exist_rate_ota = 0;
                        foreach ($dataRoomOfHotel[$hID] as $rID) {
                            $price_email  = 0;
                            $hidden_price = 0;
                            if ($infoHotel->hot_required_hidden_price) {
                                $price_email = 1;
                            } else {
                                $hidden_price = isset($data_hidden_price[$rID]) ? $data_hidden_price[$rID] : 0;
                            }
                            if (isset($this->listRateOfRoom[$rID][RatePlan::TYPE_PRICE_TA])) {
                                $check_exist_rate_ta = 1;
                            }
                            if (isset($this->listRateOfRoom[$rID][RatePlan::TYPE_PRICE_OTA])) {
                                $check_exist_rate_ota = 1;
                            }
                            if (!$check_exist_rate_ta) {
                                $hidden_price = ACTIVE;
                            }

                            if (isset($this->listConvenience[$hID]) && in_array($rID, $this->listConvenience[$hID])) {
                                $dataInsertRoomRatePlanTaBreakfast[] = [
                                    'rrp_room_id'      => $rID,
                                    'rrp_hidden_price' => ACTIVE,
                                    'rrp_price_email'  => $price_email
                                ];

                                $dataInsertRoomRatePlanOtaBreakfast[] = [
                                    'rrp_room_id'      => $rID,
                                    'rrp_hidden_price' => 0,
                                    'rrp_price_email'  => 0
                                ];
                            } else {
                                $dataInsertRoomRatePlanTa[] = [
                                    'rrp_room_id'      => $rID,
                                    'rrp_hidden_price' => ACTIVE,
                                    'rrp_price_email'  => $price_email
                                ];

                                $dataInsertRoomRatePlanOta[] = [
                                    'rrp_room_id'      => $rID,
                                    'rrp_hidden_price' => 0,
                                    'rrp_price_email'  => 0
                                ];
                            }
                        }

                        $rate_ta_delete  = 0;
                        $rate_ota_delete = 0;

                        if ($infoHotel->hot_ota_hotel == 0
                            && $infoHotel->hot_ota_hybrid == 0
                            && $infoHotel->hot_ota_only == 0) {
                            $rate_ota_delete = 1;
                        }

                        if (($infoHotel->hot_ota_hotel == 1
                            && $infoHotel->hot_ota_hybrid == 0)
                            || $infoHotel->hot_ota_only == 1) {
                            $rate_ta_delete = 1;
                        }

                        $check_create_rate_breakfast = 1;
                        if (isset($this->listConvenience[$hID]) && count($this->listConvenience[$hID]) > 0) {
                            $rap_room_apply_id = json_encode($this->listConvenience[$hID]);
                            if (!$check_exist_rate_ta) {
                                //Tao rate plan TA
                                $params = [
                                    'rap_hotel_id'            => $hID,
                                    'rap_title'               => $rap_title_ta_breakfast,
                                    'rap_room_apply_id'       => $rap_room_apply_id,
                                    'rap_type_price'          => RatePlan::TYPE_PRICE_TA,
                                    'rap_surcharge_info'      => json_encode([]),
                                    'rap_accompanied_service' => json_encode([0 => 'Bữa sáng']),
                                    'rap_cancel_policy_info'  => '{"cancel_policy_info":[{"day":"3","fee":"10"},{"day":"3","fee":"5"},{"day":"7","fee":"0"}],"group_cancel_policy_info":{"num_rooms":"5","0":{"day":"7","fee":"10"},"1":{"day":"7","fee":"5"},"2":{"day":"15","fee":"0"}}}',
                                    'rap_active'              => ACTIVE,
                                    'rap_delete'              => $rate_ta_delete
                                ];

                                $data_return_ta = [];
                                $data_return_ta = $this->rateRepo->create($params);

                                if ($data_return_ta->rap_id > 0) {
                                    $data_return_ta->rooms()->attach($dataInsertRoomRatePlanTaBreakfast);
                                    echo 'Create Rate Plan TA Success For Room ID ' . $hID . '<br>';
                                }
                            }

                            if (!$check_exist_rate_ota) {
                                //Tao rate plan OTA
                                $params = [
                                    'rap_hotel_id'            => $hID,
                                    'rap_title'               => $rap_title_ota_breakfast,
                                    'rap_room_apply_id'       => $rap_room_apply_id,
                                    'rap_type_price'          => RatePlan::TYPE_PRICE_OTA,
                                    'rap_surcharge_info'      => json_encode([]),
                                    'rap_accompanied_service' => json_encode([0 => 'Bữa sáng']),
                                    'rap_cancel_policy_info'  => '{"cancel_policy_info":[{"day":"3","fee":"10"},{"day":"3","fee":"5"},{"day":"7","fee":"0"}]}',
                                    'rap_active'              => ACTIVE,
                                    'rap_delete'              => $rate_ota_delete
                                ];

                                $data_return_ota = [];
                                $data_return_ota = $this->rateRepo->create($params);

                                if ($data_return_ota->rap_id > 0) {
                                    $data_return_ota->rooms()->attach($dataInsertRoomRatePlanOtaBreakfast);
                                    echo 'Create Rate Plan OTA Success For Room ID ' . $hID . '<br>';
                                }
                            }
                            if (count($dataRoomOfHotel[$hID]) == count($this->listConvenience[$hID])) {
                                $check_create_rate_breakfast = 0;
                            }
                        }

                        //Check Tao don gia ko co an sang
                        if ($check_create_rate_breakfast) {
                            $rom_breakfast     = isset($this->listConvenience[$hID]) ? $this->listConvenience[$hID] : [];
                            $rap_room_apply_id = array_diff($dataRoomOfHotel[$hID], $rom_breakfast);
                            $rap_room_apply_id = json_encode(array_values($rap_room_apply_id));

                            if (!$check_exist_rate_ta) {
                                //Tao rate plan TA
                                $params = [
                                    'rap_hotel_id'            => $hID,
                                    'rap_title'               => $rap_title_ta,
                                    'rap_room_apply_id'       => $rap_room_apply_id,
                                    'rap_type_price'          => RatePlan::TYPE_PRICE_TA,
                                    'rap_surcharge_info'      => json_encode([]),
                                    'rap_accompanied_service' => json_encode([]),
                                    'rap_cancel_policy_info'  => '{"cancel_policy_info":[{"day":"3","fee":"10"},{"day":"3","fee":"5"},{"day":"7","fee":"0"}],"group_cancel_policy_info":{"num_rooms":"5","0":{"day":"7","fee":"10"},"1":{"day":"7","fee":"5"},"2":{"day":"15","fee":"0"}}}',
                                    'rap_active'              => ACTIVE,
                                    'rap_delete'              => $rate_ta_delete
                                ];

                                $data_return_ta = [];
                                $data_return_ta = $this->rateRepo->create($params);

                                if ($data_return_ta->rap_id > 0) {
                                    $data_return_ta->rooms()->attach($dataInsertRoomRatePlanTa);
                                    echo 'Create Rate Plan TA Success For Room ID ' . $hID . '<br>';
                                }
                            }

                            if (!$check_exist_rate_ota) {
                                //Tao rate plan OTA
                                $params = [
                                    'rap_hotel_id'            => $hID,
                                    'rap_title'               => $rap_title_ota,
                                    'rap_room_apply_id'       => $rap_room_apply_id,
                                    'rap_type_price'          => RatePlan::TYPE_PRICE_OTA,
                                    'rap_surcharge_info'      => json_encode([]),
                                    'rap_accompanied_service' => json_encode([]),
                                    'rap_cancel_policy_info'  => '{"cancel_policy_info":[{"day":"3","fee":"10"},{"day":"3","fee":"5"},{"day":"7","fee":"0"}]}',
                                    'rap_active'              => ACTIVE,
                                    'rap_delete'              => $rate_ota_delete
                                ];

                                $data_return_ota = [];
                                $data_return_ota = $this->rateRepo->create($params);

                                if ($data_return_ota->rap_id > 0) {
                                    $data_return_ota->rooms()->attach($dataInsertRoomRatePlanOta);
                                    echo 'Create Rate Plan OTA Success For Room ID ' . $hID . '<br>';
                                }
                            }
                        }
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON RATE PLAN SUCCESS';die;
        }

    }

    public function convertPrice ($time_start, $time_finish)
    {
        die;
        $this->time_start  = strtotime($time_start);
        $this->time_finish = strtotime($time_finish);
        $offset    = 10;
        $page      = (int)Input::get('page', 0);
        $time_life = 3;
        $url       = route('convert_price_rate_plan', [$time_start, $time_finish]);
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $params = array(
            'field_select' => 'hot_id',
            'take'         => $offset,
            'skip'         => $page * $offset,
            'order_by_asc' => 'hot_id'
        );

        $dataHotel        = $this->hotelRepo->getInfoHotel($params);
        $listHotelID      = $dataHotel->keyBy('hot_id')->keys()->toArray();
        $dataRoom         = $this->roomRepo->getRoomInfoByListHotelId($listHotelID, ['field_select' => ['rom_id', 'rom_hotel']]);
        $listRoomID       = $dataRoom->keyBy('rom_id')->keys()->toArray();
        $dataRatePlanRoom = $this->roomRateRepo->whereIn('rrp_room_id', $listRoomID)
                                               ->with('ratePlans')
                                               ->get();

        $listRateOfRoom = [];
        if (is_object($dataRatePlanRoom) && !$dataRatePlanRoom->isEmpty()) {
            foreach ($dataRatePlanRoom as $infoRate) {
                $listRateOfRoom[$infoRate->rrp_room_id][] = $infoRate->ratePlans;
            }
        }

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            if (!empty ($listRateOfRoom)) {
                for ($i = $this->time_start; $i <= $this->time_finish; $i += 86400 * 31) {
                    $table_room_price     = "room_price_" . date('Ym', $i);
                    $table_room_allotment = "rooms_allotment_" . date('Ym', $i);
                    $table_room_service   = "room_services_status_" . date('Ym', $i);

                    $this->getPriceRoomId($listRoomID, $table_room_price);
                    $this->getAllotmentRoomId($listRoomID, $table_room_allotment);

                    $dataInsert           = [];
                    $dataPriceConvert     = [];
                    $dataAllotmentInsert  = [];
                    $dataAllotmentConvert = [];

                    $params = [
                        'table'        => $table_room_service,
                        'list_room'    => $listRoomID,
                        'order_by_asc' => 'romst_room_id'
                    ];

                    $dataPrice = $this->roomServices->getInfoRoomServices($params);

                    if (is_object($dataPrice) && !$dataPrice->isEmpty()) {
                        foreach ($dataPrice as $infoPrice) {
                            $hID  = $infoPrice->romst_hotel_id;
                            $rID  = $infoPrice->romst_room_id;
                            $time = $infoPrice->romst_time;

                            if (isset($listRateOfRoom[$rID])) {
                                foreach ($listRateOfRoom[$rID] as $infoRatePlan) {
                                    if (!isset($dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id])) {
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id] = $this->getDefaultDataPrice($i);
                                    }

                                    $price_publish = $infoPrice->romst_price_publish;
                                    if ($infoRatePlan->rap_type_price == RatePlan::TYPE_PRICE_TA) {
                                        $price          = $infoPrice->romst_price_ta;
                                        $price_contract = $infoPrice->romst_price_ta_contact;
                                        if ($price_publish <= 0) {
                                            $price_publish = $price * RoomPrice::RATE_TA_PRICE_PUBLISH;
                                        }
                                    } elseif ($infoRatePlan->rap_type_price == RatePlan::TYPE_PRICE_OTA) {
                                        $price          = $infoPrice->romst_price_ota;
                                        $price_contract = $infoPrice->romst_price_ota_contact;
                                        if ($price_publish <= 0) {
                                            $price_publish = $price * RoomPrice::RATE_OTA_PRICE_PUBLISH;
                                        }
                                    }

                                    $romst_price_season_contract = $infoPrice->romst_price_season_contact;
                                    $field_col = 'rop_col' . date('j', $time);

                                    if (!isset($this->listPriceOfRoom[$rID][$infoRatePlan->rap_id][$infoRatePlan->rap_type_price])) {
                                        if ($dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_hotel_id'] == 0) {
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_hotel_id']     = $hID;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_room_id']      = $rID;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_rate_plan_id'] = $infoRatePlan->rap_id;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_type_price']   = $infoRatePlan->rap_type_price;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_person_type']  = RoomPrice::NUM_PERSON_MAX_TYPE;
                                        }
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_info_price_publish'][$time]         = $price_publish;
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_info_price_contract'][$time]        = $price_contract;
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_info_price_season_contract'][$time] = $romst_price_season_contract;
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id][$field_col]                              = $price;
                                    } else {
                                        $dataPriceConvert = [];
                                    }
                                }
                            }

                            if (!isset($this->listAllotmentOfRoom[$rID])) {
                                $allotment_ota    = !empty($infoPrice->romst_allotment) ? $infoPrice->romst_allotment : 0;
                                $allotment_ta     = !empty($infoPrice->romst_allotment_special) ? $infoPrice->romst_allotment_special : 0;
                                $allotment_status = !empty($infoPrice->romst_status) ? $infoPrice->romst_status : 0;
                                $dataAllotmentConvert[$rID][$time] = [
                                    'roa_hotel_id'            => $hID,
                                    'roa_room_id'             => $rID,
                                    'roa_allotment_ota'       => $allotment_ota,
                                    'roa_allotment_ta'        => $allotment_ta,
                                    'roa_time'                => $time,
                                    'roa_check_allotment_pms' => NO_ACTIVE,
                                    'roa_status'              => $allotment_status
                                ];
                            }

                            if (isset($dataPriceConvert[$hID][$rID])) {
                                foreach ($dataPriceConvert[$hID][$rID] as $rateID => $info) {
                                    //Gan gia tri default
                                    if (!is_array($info['rop_info_price_contract'])) {
                                        $info['rop_info_price_contract'] = [];
                                    }
                                    if (!is_array($info['rop_info_price_publish'])) {
                                        $info['rop_info_price_publish'] = [];
                                    }
                                    if (!is_array($info['rop_info_price_season_contract'])) {
                                        $info['rop_info_price_season_contract'] = [];
                                    }

                                    $dataInsert[$info['rop_room_id']][$rateID] = $info;
                                    $dataInsert[$info['rop_room_id']][$rateID]['rop_info_price_publish']         = json_encode($info['rop_info_price_publish']);
                                    $dataInsert[$info['rop_room_id']][$rateID]['rop_info_price_contract']        = json_encode($info['rop_info_price_contract']);
                                    $dataInsert[$info['rop_room_id']][$rateID]['rop_info_price_season_contract'] = json_encode($info['rop_info_price_season_contract']);
                                }
                            }
                        }
                    }

                    //Neu co du lieu convert thi insert vao bang room price
                    if (!empty($dataInsert)) {
                        $dataInsertFinal = [];
                        foreach ($dataInsert as $roomInfo) {
                            foreach ($roomInfo as $rateInfo) {
                                $dataInsertFinal[] = $rateInfo;
                            }
                        }

                        $params_insert = ['table' => $table_room_price, 'data_insert' => $dataInsertFinal];
                        // echo $table_room_price . '==============================';
                        // echo '<pre>';
                        // print_r($dataInsert);
                        // echo '</pre>';die;
                        $this->roomPrice->insertPriceByData($params_insert);
                    }

                    //Insert allotmen vao bang rooms_allotment
                    if (isset($dataAllotmentConvert) && !empty($dataAllotmentConvert)) {
                        foreach ($dataAllotmentConvert as $roomID => $roomInfo) {
                            $params_insert = ['table' => $table_room_allotment, 'data_insert' => $roomInfo];
                            $this->roomAllotmentRepo->insertAllotmentByData($params_insert);
                        }
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON PRICE SUCCESS';die;
        }

    }

    public function getDefaultDataPrice ($timeInt)
    {
        $data_return = [
            'rop_hotel_id'     => 0,
            'rop_room_id'      => 0,
            'rop_rate_plan_id' => 0,
            'rop_type_price'   => 0,
        ];

        $time_start  = strtotime(date('Ym01', $timeInt));
        $time_finish = strtotime(date('Ymt', $timeInt));

        for ($i = $time_start; $i <= $time_finish; $i += 86400) {
            $data_return['rop_info_price_publish'][$i]         = 0;
            $data_return['rop_info_price_contract'][$i]        = 0;
            $data_return['rop_info_price_season_contract'][$i] = 0;
        }

        for ($i = 1; $i <= 31; $i++) {
            $data_return['rop_col' . $i] = 0;
        }

        return $data_return;
    }

    public function getDefaultDataAllotment ($timeInt)
    {
        $data_return = [
                'roa_hotel_id' => 0,
                'roa_room_id'  => 0,
            ];
        $time_start  = strtotime(date('Ym01', $timeInt));
        $time_finish = strtotime(date('Ymt', $timeInt));

        for ($i = $time_start; $i <= $time_finish; $i += 86400) {
            $data_return['roa_status'][$i]             = 0;
            $data_return['roa_info_allotment_ta'][$i]  = 0;
            $data_return['roa_info_allotment_ota'][$i] = 0;

        }

        return $data_return;
    }

    private function getHiddenPriceOfRoom($listRoomID)
    {
        $data_return = [];

        for ($i = strtime_firstday_month($this->time_start); $i <= $this->time_finish; $i = strtime_next_month($i)) {
            $table_price = "room_services_status_" . date('Ym', $i);
            $params = ['table' => $table_price, 'field_select' => ['romst_room_id', 'romst_status', 'romst_price'], 'list_room' => $listRoomID];
            $dataPrice = $this->roomServices->getInfoRoomServices($params);
            if (!$dataPrice->isEmpty()) {
                foreach ($dataPrice as $priceInfo) {
                    if (isset($data_return[$priceInfo['romst_room_id']])) {
                        if ($data_return[$priceInfo['romst_room_id']] == 1) {
                            continue;
                        } else {
                            $data_return[$priceInfo['romst_room_id']] = $priceInfo['romst_status'] == 2 ? 1: 0;
                        }
                    } else {
                        $data_return[$priceInfo['romst_room_id']] = $priceInfo['romst_status'] == 2 ? 1: 0;
                    }
                }
            }
        }

        return $data_return;
    }


    /**
     * [getRateByRoomId description]
     * @param  array $listRoomID [description]
     * @return [type]             [description]
     */
    public function getRateByRoomId (array $listRoomID)
    {
        $dataRatePlan = $this->roomRateRepo->whereIn('rrp_room_id', $listRoomID)
                                           ->with('ratePlans')
                                           ->get();

        $listRateOfRoom = [];
        if (is_object($dataRatePlan) && !$dataRatePlan->isEmpty()) {
            foreach ($dataRatePlan as $info) {
                if ($info->ratePlans->rap_parent_id == 0) {
                    $listRateOfRoom[$info->rrp_room_id][$info->ratePlans->rap_type_price] = $info->rrp_rate_plan_id;
                }
            }
        }

        $this->listRateOfRoom = $listRateOfRoom;

    }

    /**
     * [getRateByRoomId description]
     * @param  array $listRoomID [description]
     * @return [type]             [description]
     */
    public function getRateByRoomIdUpdate (array $listRoomID)
    {
        $dataRatePlan = $this->roomRateRepo->whereIn('rrp_room_id', $listRoomID)
                                           ->with('ratePlans')
                                           ->get();

        $listRateOfRoom = [];
        $listRateOfRoomBreakfast = [];

        if (is_object($dataRatePlan) && !$dataRatePlan->isEmpty()) {
            foreach ($dataRatePlan as $info) {
                if ($info->ratePlans->rap_parent_id == 0) {
                    if ($info->ratePlans->rap_title == 'TA_Room_Only'
                        || $info->ratePlans->rap_title == 'OTA_Room_Only') {
                        $listRateOfRoom[$info->rrp_room_id][$info->ratePlans->rap_type_price] = $info->rrp_rate_plan_id;
                    }

                    if ($info->ratePlans->rap_title == 'TA_Room_Breakfast'
                        || $info->ratePlans->rap_title == 'OTA_Room_Breakfast') {
                        $listRateOfRoomBreakfast[$info->rrp_room_id][$info->ratePlans->rap_type_price] = $info->rrp_rate_plan_id;
                    }
                }
            }
        }

        $this->listRateOfRoom = $listRateOfRoom;
        $this->listRateOfRoomBreakfast = $listRateOfRoomBreakfast;

    }

    /**
     * [getPriceRoomId description]
     * @param  array  $listRoomID [description]
     * @return [type]             [description]
     */
    public function getPriceRoomId(array $listRoomID, $table_price)
    {
        //Set table price
        $this->roomPrice->setTable($table_price);

        $dataPrice = $this->roomPrice->select('rop_id', 'rop_room_id', 'rop_rate_plan_id', 'rop_type_price')
                                     ->whereIn('rop_room_id', $listRoomID)
                                     ->get();

        $listPriceOfRoom = [];
        if (is_object($dataPrice) && !$dataPrice->isEmpty()) {
            foreach ($dataPrice as $info) {
                $listPriceOfRoom[$info->rop_room_id][$info->rop_rate_plan_id][$info->rop_type_price] = 1;
            }
        }

        $this->listPriceOfRoom = $listPriceOfRoom;
    }

    /**
     * [getPriceRoomId description]
     * @param  array  $listRoomID [description]
     * @return [type]             [description]
     */
    public function getAllotmentRoomId(array $listRoomID, $table_allotment)
    {
        //Set table allotment
        $this->roomAllotmentRepo->setTable($table_allotment);

        $dataAllotment = $this->roomAllotmentRepo->select('roa_room_id')
                                                 ->whereIn('roa_room_id', $listRoomID)
                                                 ->get();

        $listAllotmentOfRoom = [];
        if (is_object($dataAllotment) && !$dataAllotment->isEmpty()) {
            foreach ($dataAllotment as $info) {
                $listAllotmentOfRoom[$info->roa_room_id] = 1;
            }
        }

        $this->listAllotmentOfRoom = $listAllotmentOfRoom;
    }

    /**
     * [convertBookingHotel description]
     * Convert thong tin boo_book_info theo logic rate plan
     * @return [type] [description]
     */
    public function ConvertBookingHotel ()
    {
        $page      = (int)Input::get('page', 0);
        $offset    = 100;
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('convert-booking-hotel');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $time_end = strtotime('06/20/2016');

        $dataBooking = $this->hotelBooking->select(['boo_id', 'boo_hotel', 'boo_book_info', 'boo_book_info_booked', 'boo_book_info_modified', 'boo_pms_inventory_info', 'boo_time_book'])
                                          ->where('boo_pms_inventory_info', '<>', 'null')
                                          ->where('boo_pms_inventory_info', '<>', '')
                                          ->orderBy('boo_id', 'DESC')
                                          ->take($offset)
                                          ->skip($skip)
                                          ->get();

        if (is_object($dataBooking) && !$dataBooking->isEmpty()) {
            //Get thong tin rate of room
            $listHotelID = $dataBooking->keyBy('boo_hotel')->keys()->toArray();
            $dataRoom    = $this->roomRepo->getRoomInfoByListHotelId($listHotelID, ['field_select' => ['rom_id', 'rom_hotel']]);
            $listRoomID  = $dataRoom->keyBy('rom_id')->keys()->toArray();

            $boo_book_info_update          = [];
            $boo_book_info_booked_update   = [];
            $boo_book_info_modified_update = [];
            //Gan Key Rate Plan vao boo_book_info
            foreach ($dataBooking as $infoBook) {
                $is_ota_booking         = 1;
                $check_booking_update   = 1;
                $boo_book_info          = json_decode($infoBook->boo_book_info, true);
                $boo_book_info_booked   = json_decode($infoBook->boo_book_info_booked, true);
                $boo_book_info_modified = json_decode($infoBook->boo_book_info_modified, true);
                $rate_pms_booking = [];

                if (count($boo_book_info) > 0) {
                    $boo_time_book = strtotime($infoBook->boo_time_book->format('m/d/Y'));

                    foreach ($boo_book_info as $rID => $info) {
                        if (isset($info['rate_info']) && count($info['rate_info']) > 0) {
                            foreach ($info['rate_info'] as $rateID => $info_rate) {
                                $params_rp = ['field_select' => ['rap_id'],
                                              'pmsId'        => $rateID,
                                              'hotelId'      => $infoBook->boo_hotel];
                                $rate_id_mt = $this->rateRepo->getInfoRatePlanByIdRatePms($params_rp)->first();

                                if ($rate_id_mt != NULL) {
                                    $boo_book_info_update[$infoBook->boo_id][$rID] = $info;
                                    unset($boo_book_info_update[$infoBook->boo_id][$rID]['rate_info'][$rateID]);
                                    $boo_book_info_update[$infoBook->boo_id][$rID]['rate_info'][$rate_id_mt->rap_id] = $info_rate;
                                    $rate_pms_booking[$infoBook->boo_id][$rID] = $rate_id_mt->rap_id;
                                }
                            }
                        } else {
                            $check_booking_update = 0;
                            break;
                        }
                    }
                    
                }

                if (count($boo_book_info_booked) > 0
                    && $check_booking_update) {
                    foreach ($boo_book_info_booked as $rID => $info) {
                        if (isset($rate_pms_booking[$infoBook->boo_id][$rID])
                            && $rate_pms_booking[$infoBook->boo_id][$rID] > 0) {
                            foreach ($info as $rateID => $info_rate) {
                                $boo_book_info_booked_update[$infoBook->boo_id][$rID][$rate_pms_booking[$infoBook->boo_id][$rID]] = $info_rate;
                            }
                        }
                    }
                }

                if (count($boo_book_info_modified) > 0
                    && $check_booking_update) {
                    foreach ($boo_book_info_modified as $rID => $info) {
                        if (isset($rate_pms_booking[$infoBook->boo_id][$rID])
                            && $rate_pms_booking[$infoBook->boo_id][$rID] > 0) {
                            foreach ($info as $rateID => $info_rate) {
                                $boo_book_info_modified_update[$infoBook->boo_id][$rID][$rate_pms_booking[$infoBook->boo_id][$rID]] = $info_rate;
                            }
                        }
                    }
                }
            }

            //Update lai thong tin boo_book_info
            if (count($boo_book_info_update) > 0) {
                foreach ($boo_book_info_update as $bookID => $infoBook) {
                    $dataUpdate = ['boo_book_info' => json_encode($infoBook)];

                    if (isset($boo_book_info_booked_update[$bookID])
                        && count($boo_book_info_booked_update[$bookID]) > 0) {
                        $dataUpdate['boo_book_info_booked'] = json_encode($boo_book_info_booked_update[$bookID]);
                    }
                    if (isset($boo_book_info_modified_update[$bookID])
                        && count($boo_book_info_modified_update[$bookID]) > 0) {
                        $dataUpdate['boo_book_info_modified'] = json_encode($boo_book_info_modified_update[$bookID]);
                    }

                    $this->hotelBooking->where('boo_id', '=', $bookID)
                                       ->update($dataUpdate);

                    echo "CONVERT BOOKING PMS SUCCESS " . $bookID . '<br>';
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON BOOKING HOTEL SUCCESS';die;
        }
    }

    private function getInfoRoomConvenience($listRoomID)
    {
        $dataConveniences = $this->roomConveniences->select(['rom_id', 'rom_hotel'])
                                                   ->Join('rooms', 'roc_room_id', '=', 'rom_id')
                                                   ->whereIn('roc_room_id', $listRoomID)
                                                   ->where('roc_convenience_id', '=', 70)
                                                   ->get();

        if (is_object($dataConveniences) && !$dataConveniences->isEmpty()) {
            foreach ($dataConveniences as $info) {
                $this->listConvenience[$info->rom_hotel][] = $info->rom_id;
            }
        }
    }

    public function deleteRatePlan ()
    {
        die;
        return $this->rateRepo->where('rap_active', '=', 1)
                              ->delete();
    }

    public function deleteRoomAllotment ()
    {
        die;
        for ($year = 2014; $year <= 2017; $year++) {
            for ($m = 1; $m <= 12; $m++) {
                $month = $m;
                if ($m < 10) $month = "0" . $m;
                $table_allot = "rooms_allotment_" . $year . $month;
                $this->roomAllotmentRepo->setTable($table_allot);
                $data = $this->roomAllotmentRepo->where('roa_hotel_id', '>', 0)
                                                ->delete();
                echo "DELETE ALLOTMENT SUCCESS TABLE " . $table_allot . '<br>';
            }
        }
    }

    public function cronUpdateRatePlan ()
    {
        die;
        $offset    = 50;
        $page      = (int)Input::get('page', 0);
        $time_life = 3;
        $url       = route('create_rate_plan');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $params = array(
            'field_select' => ['hot_id', 'hot_required_hidden_price', 'hot_ota_hotel', 'hot_ota_hybrid', 'hot_ota_only'],
            'take'         => $offset,
            'skip'         => $page * $offset,
            'order_by_asc' => 'hot_id'
        );

        $dataHotel   = $this->hotelRepo->getInfoHotel($params);
        $listHotelID = $dataHotel->keyBy('hot_id')->keys()->toArray();

        $dataRoom = $this->roomRepo->select(['rom_id', 'rom_hotel'])
                                   ->whereIn('rom_hotel', $listHotelID)
                                   ->get();

        $listRoomID = $dataRoom->keyBy('rom_id')->keys()->toArray();
        $this->getInfoRoomConvenience($listRoomID);
        $this->getRateByRoomId($listRoomID);

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            //Get Room Of Hotel
            $dataRoomOfHotel = [];
            if (!$dataRoom->isEmpty()) {
                foreach ($dataRoom as $infoRoom) {
                    $dataRoomOfHotel[$infoRoom->rom_hotel][] = $infoRoom->rom_id;
                }
            }

            if (!empty ($dataRoomOfHotel)) {
                //get hidden price of room
                $rap_title_ta_breakfast  = 'TA_Room_Breakfast';
                $rap_title_ota_breakfast = 'OTA_Room_Breakfast';

                foreach ($dataHotel as $infoHotel) {
                    $hID = $infoHotel->hot_id;
                    $dataInsertRoomRatePlanTaBreakfast  = [];
                    $dataInsertRoomRatePlanOtaBreakfast = [];

                    if (isset($dataRoomOfHotel[$hID]) && !empty($dataRoomOfHotel[$hID])) {
                        foreach ($dataRoomOfHotel[$hID] as $rID) {
                            if (isset($this->listConvenience[$hID]) && in_array($rID, $this->listConvenience[$hID])) {
                                $dataInsertRoomRatePlanTaBreakfast[] = [
                                    'rrp_room_id'      => $rID,
                                    'rrp_hidden_price' => ACTIVE,
                                    'rrp_price_email'  => $price_email
                                ];

                                $dataInsertRoomRatePlanOtaBreakfast[] = [
                                    'rrp_room_id'      => $rID,
                                    'rrp_hidden_price' => 0,
                                    'rrp_price_email'  => 0
                                ];
                            }
                        }

                        $rate_ta_delete  = 0;
                        $rate_ota_delete = 0;

                        if ($infoHotel->hot_ota_hotel == 0
                            && $infoHotel->hot_ota_hybrid == 0
                            && $infoHotel->hot_ota_only == 0) {
                            $rate_ota_delete = 1;
                        }

                        if (($infoHotel->hot_ota_hotel == 1
                            && $infoHotel->hot_ota_hybrid == 0)
                            || $infoHotel->hot_ota_only == 1) {
                            $rate_ta_delete = 1;
                        }

                        $check_create_rate_breakfast = 1;
                        if (isset($this->listConvenience[$hID]) && count($this->listConvenience[$hID]) > 0) {
                            $rap_room_apply_id = json_encode($this->listConvenience[$hID]);
                            if (!$check_exist_rate_ta) {
                                //Tao rate plan TA
                                $params = [
                                    'rap_hotel_id'            => $hID,
                                    'rap_title'               => $rap_title_ta_breakfast,
                                    'rap_room_apply_id'       => $rap_room_apply_id,
                                    'rap_type_price'          => RatePlan::TYPE_PRICE_TA,
                                    'rap_surcharge_info'      => json_encode([]),
                                    'rap_accompanied_service' => json_encode([0 => 'Bữa sáng']),
                                    'rap_cancel_policy_info'  => '{"cancel_policy_info":[{"day":"3","fee":"10"},{"day":"3","fee":"5"},{"day":"7","fee":"0"}],"group_cancel_policy_info":{"num_rooms":"5","0":{"day":"7","fee":"10"},"1":{"day":"7","fee":"5"},"2":{"day":"15","fee":"0"}}}',
                                    'rap_active'              => ACTIVE,
                                    'rap_delete'              => $rate_ta_delete
                                ];

                                $data_return_ta = [];
                                $data_return_ta = $this->rateRepo->create($params);
                                Log::info('LINE 187=============HOTEL ID ' . $hID . ' params ->' . json_encode($params));

                                if ($data_return_ta->rap_id > 0) {
                                    $data_return_ta->rooms()->attach($dataInsertRoomRatePlanTaBreakfast);
                                    echo 'Create Rate Plan TA Success For Room ID ' . $hID . '<br>';
                                }
                            }

                            if (!$check_exist_rate_ota) {
                                //Tao rate plan OTA
                                $params = [
                                    'rap_hotel_id'            => $hID,
                                    'rap_title'               => $rap_title_ota_breakfast,
                                    'rap_room_apply_id'       => $rap_room_apply_id,
                                    'rap_type_price'          => RatePlan::TYPE_PRICE_OTA,
                                    'rap_surcharge_info'      => json_encode([]),
                                    'rap_accompanied_service' => json_encode([0 => 'Bữa sáng']),
                                    'rap_cancel_policy_info'  => '{"cancel_policy_info":[{"day":"3","fee":"10"},{"day":"3","fee":"5"},{"day":"7","fee":"0"}]}',
                                    'rap_active'              => ACTIVE,
                                    'rap_delete'              => $rate_ota_delete
                                ];

                                $data_return_ota = [];
                                $data_return_ota = $this->rateRepo->create($params);
                                Log::info('LINE 211=============HOTEL ID ' . $hID . ' params ->' . json_encode($params));

                                if ($data_return_ota->rap_id > 0) {
                                    $data_return_ota->rooms()->attach($dataInsertRoomRatePlanOtaBreakfast);
                                    echo 'Create Rate Plan OTA Success For Room ID ' . $hID . '<br>';
                                }
                            }
                        }
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON RATE PLAN SUCCESS';die;
        }

    }

    private function getInfoRoomConvenienceUpdate($listRoomID)
    {
        $dataConveniences = $this->roomConveniences->select(['rom_id', 'rom_hotel', 'roc_id'])
                                                   ->leftJoin('rooms', 'roc_room_id', '=', 'rom_id')
                                                   ->whereIn('roc_room_id', $listRoomID)
                                                   ->where('roc_convenience_id', '=', 70)
                                                   ->get();

        if (is_object($dataConveniences) && !$dataConveniences->isEmpty()) {
            foreach ($dataConveniences as $info) {
                if (is_null($info->roc_id)) {
                    $this->listConvenience[$info->rom_hotel][] = $info->rom_id;
                }
            }
        }
    }

    public function ConvertRateHotelTa ()
    {
        die;
        $page      = (int)Input::get('page', 0);
        $offset    = 50;
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('hidden_rate_hotel_ta');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $dataHotel = $this->hotelRepo->select(['hot_id'])
                                     ->where('hot_ota_hybrid', '=', 0)
                                     ->where('hot_ota_only', '=', 0)
                                     ->take($offset)
                                     ->skip($skip)
                                     ->get();

        $listHotelID = $dataHotel->keyBy('hot_id')->keys()->toArray();

        $dataRoom = $this->roomRepo->select(['rom_id', 'rom_hotel'])
                                   ->whereIn('rom_hotel', $listHotelID)
                                   ->get();

        $listRoomID = $dataRoom->keyBy('rom_id')->keys()->toArray();
        $this->getInfoRoomConvenience($listRoomID);
        $this->getRateByRoomId($listRoomID);
        // dd($this->listRateOfRoom);
        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            if (count($this->listRateOfRoom) > 0) {
                foreach ($this->listRateOfRoom as $rID => $info) {
                    foreach ($info as $rateID) {
                        $data_update = ['rrp_hidden_price' => 0];
                        $this->roomRateRepo->where('rrp_room_id', '=', $rID)
                                           ->where('rrp_rate_plan_id', '=', $rateID)
                                           ->update($data_update);

                       echo 'UPDATE RATE PLAN HIDDEN SUCCESS ' . $rID . '---' . $rateID .'<br>';
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON RATE PLAN HIDDEN HOTEL TA SUCCESS';die;
        }


    }


    public function updateRatePlan ()
    {
        die;
        $offset    = 50;
        $page      = (int)Input::get('page', 0);
        $time_life = 3;
        $url       = route('update_rate_plan');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $params = array(
            'field_select' => ['hot_id', 'hot_required_hidden_price', 'hot_ota_hotel', 'hot_ota_hybrid', 'hot_ota_only'],
            'take'         => $offset,
            'skip'         => $page * $offset,
            'order_by_asc' => 'hot_id'
        );

        $dataHotel   = $this->hotelRepo->getInfoHotel($params);
        $listHotelID = $dataHotel->keyBy('hot_id')->keys()->toArray();

        $dataRoom = $this->roomRepo->select(['rom_id', 'rom_hotel'])
                                   ->whereIn('rom_hotel', $listHotelID)
                                   ->get();

        $listRoomID = $dataRoom->keyBy('rom_id')->keys()->toArray();
        $this->getInfoRoomConvenience($listRoomID);
        $this->getRateByRoomIdUpdate($listRoomID);

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            //Get Room Of Hotel
            $dataRoomOfHotel = [];
            if (!$dataRoom->isEmpty()) {
                foreach ($dataRoom as $infoRoom) {
                    $dataRoomOfHotel[$infoRoom->rom_hotel][] = $infoRoom->rom_id;
                }
            }

            if (!empty ($dataRoomOfHotel)) {
                //get hidden price of room
                $data_hidden_price = $this->getHiddenPriceOfRoom($listRoomID);
                $rap_title_ta            = 'TA_Room_Only';
                $rap_title_ta_breakfast  = 'TA_Room_Breakfast';
                $rap_title_ota           = 'OTA_Room_Only';
                $rap_title_ota_breakfast = 'OTA_Room_Breakfast';

                foreach ($dataHotel as $infoHotel) {
                    $hID = $infoHotel->hot_id;
                    $dataInsertRoomRatePlanTa           = [];
                    $dataInsertRoomRatePlanOta          = [];
                    $dataInsertRoomRatePlanTaBreakfast  = [];
                    $dataInsertRoomRatePlanOtaBreakfast = [];

                    if (isset($dataRoomOfHotel[$hID]) && !empty($dataRoomOfHotel[$hID])) {
                        $check_exist_rate_ta  = 0;
                        $check_exist_rate_ota = 0;
                        $check_exist_rate_ta_breakfast = 0;
                        $check_exist_rate_ota_breakfast = 0;

                        foreach ($dataRoomOfHotel[$hID] as $rID) {
                            $price_email  = 0;
                            $hidden_price = 0;
                            if ($infoHotel->hot_required_hidden_price) {
                                $price_email = 1;
                            } else {
                                $hidden_price = isset($data_hidden_price[$rID]) ? $data_hidden_price[$rID] : 0;
                            }
                            if (isset($this->listRateOfRoom[$rID][RatePlan::TYPE_PRICE_TA])) {
                                $check_exist_rate_ta = 1;
                            }
                            if (isset($this->listRateOfRoom[$rID][RatePlan::TYPE_PRICE_OTA])) {
                                $check_exist_rate_ota = 1;
                            }
                            if (isset($this->listRateOfRoomBreakfast[$rID][RatePlan::TYPE_PRICE_TA])) {
                                $check_exist_rate_ta_breakfast = 1;
                            }
                            if (isset($this->listRateOfRoomBreakfast[$rID][RatePlan::TYPE_PRICE_OTA])) {
                                $check_exist_rate_ota_break_fast = 1;
                            }

                            if (!$check_exist_rate_ta || !$check_exist_rate_ta_breakfast) {
                                $hidden_price = ACTIVE;
                            }

                            if (isset($this->listConvenience[$hID]) && in_array($rID, $this->listConvenience[$hID])) {
                                $dataInsertRoomRatePlanTaBreakfast[] = [
                                    'rrp_room_id'      => $rID,
                                    'rrp_hidden_price' => ACTIVE,
                                    'rrp_price_email'  => $price_email
                                ];

                                $dataInsertRoomRatePlanOtaBreakfast[] = [
                                    'rrp_room_id'      => $rID,
                                    'rrp_hidden_price' => 0,
                                    'rrp_price_email'  => 0
                                ];
                            } else {
                                $dataInsertRoomRatePlanTa[] = [
                                    'rrp_room_id'      => $rID,
                                    'rrp_hidden_price' => ACTIVE,
                                    'rrp_price_email'  => $price_email
                                ];

                                $dataInsertRoomRatePlanOta[] = [
                                    'rrp_room_id'      => $rID,
                                    'rrp_hidden_price' => 0,
                                    'rrp_price_email'  => 0
                                ];
                            }
                        }

                        $rate_ta_delete  = 0;
                        $rate_ota_delete = 0;

                        if ($infoHotel->hot_ota_hotel == 0
                            && $infoHotel->hot_ota_hybrid == 0
                            && $infoHotel->hot_ota_only == 0) {
                            $rate_ota_delete = 1;
                        }

                        if (($infoHotel->hot_ota_hotel == 1
                            && $infoHotel->hot_ota_hybrid == 0)
                            || $infoHotel->hot_ota_only == 1) {
                            $rate_ta_delete = 1;
                        }

                        $check_create_rate_breakfast = 1;
                        if (isset($this->listConvenience[$hID]) && count($this->listConvenience[$hID]) > 0) {
                            $rap_room_apply_id = json_encode($this->listConvenience[$hID]);
                            if (!$check_exist_rate_ta_breakfast) {
                                //Tao rate plan TA
                                $params = [
                                    'rap_hotel_id'            => $hID,
                                    'rap_title'               => $rap_title_ta_breakfast,
                                    'rap_room_apply_id'       => $rap_room_apply_id,
                                    'rap_type_price'          => RatePlan::TYPE_PRICE_TA,
                                    'rap_surcharge_info'      => json_encode([]),
                                    'rap_accompanied_service' => json_encode([0 => 'Bữa sáng']),
                                    'rap_cancel_policy_info'  => '{"cancel_policy_info":[{"day":"3","fee":"10"},{"day":"3","fee":"5"},{"day":"7","fee":"0"}],"group_cancel_policy_info":{"num_rooms":"5","0":{"day":"7","fee":"10"},"1":{"day":"7","fee":"5"},"2":{"day":"15","fee":"0"}}}',
                                    'rap_active'              => ACTIVE,
                                    'rap_delete'              => $rate_ta_delete
                                ];

                                $data_return_ta = [];
                                $data_return_ta = $this->rateRepo->create($params);

                                if ($data_return_ta->rap_id > 0) {
                                    $data_return_ta->rooms()->attach($dataInsertRoomRatePlanTaBreakfast);
                                    echo 'Create Rate Plan TA Success For Room ID ' . $hID . '<br>';
                                }
                            } else {
                                $dataRatePlan = $this->rateRepo->where('rap_title', '=', $rap_title_ta_breakfast)
                                                               ->where('rap_hotel_id', '=', $hID)
                                                               ->get()->first();

                                if (!is_null($dataRatePlan)) {
                                    $dataRatePlan->rap_room_apply_id = $rap_room_apply_id;
                                    $dataRatePlan->save();

                                    $dataRatePlan->rooms()->sync($this->listConvenience[$hID]);
                                    echo 'UPDATE Rate Plan TA BREAKFAST Success For Room ID ' . $hID . '<br>';
                                }
                            }

                            if (!$check_exist_rate_ota_breakfast) {
                                //Tao rate plan OTA
                                $params = [
                                    'rap_hotel_id'            => $hID,
                                    'rap_title'               => $rap_title_ota_breakfast,
                                    'rap_room_apply_id'       => $rap_room_apply_id,
                                    'rap_type_price'          => RatePlan::TYPE_PRICE_OTA,
                                    'rap_surcharge_info'      => json_encode([]),
                                    'rap_accompanied_service' => json_encode([0 => 'Bữa sáng']),
                                    'rap_cancel_policy_info'  => '{"cancel_policy_info":[{"day":"3","fee":"10"},{"day":"3","fee":"5"},{"day":"7","fee":"0"}]}',
                                    'rap_active'              => ACTIVE,
                                    'rap_delete'              => $rate_ota_delete
                                ];

                                $data_return_ota = [];
                                $data_return_ota = $this->rateRepo->create($params);

                                if ($data_return_ota->rap_id > 0) {
                                    $data_return_ota->rooms()->attach($dataInsertRoomRatePlanOtaBreakfast);
                                    echo 'Create Rate Plan OTA Success For Room ID ' . $hID . '<br>';
                                }
                            } else {
                                $dataRatePlan = $this->rateRepo->where('rap_title', '=', $rap_title_ota_breakfast)
                                                               ->where('rap_hotel_id', '=', $hID)
                                                               ->get()->first();

                                if (!is_null($dataRatePlan)) {
                                    $dataRatePlan->rap_room_apply_id = $rap_room_apply_id;
                                    $dataRatePlan->save();

                                    $dataRatePlan->rooms()->sync($this->listConvenience[$hID]);
                                    echo 'UPDATE Rate Plan OTA BREAKFAST Success For Room ID ' . $hID . '<br>';
                                }
                            }


                            if (count($dataRoomOfHotel[$hID]) == count($this->listConvenience[$hID])) {
                                $check_create_rate_breakfast = 0;
                            }
                        }

                        //Check Tao don gia ko co an sang
                        if ($check_create_rate_breakfast) {
                            $rom_breakfast     = isset($this->listConvenience[$hID]) ? $this->listConvenience[$hID] : [];
                            $rap_room_apply_id = array_diff($dataRoomOfHotel[$hID], $rom_breakfast);
                            $rap_room_apply_id = json_encode(array_values($rap_room_apply_id));

                            if (!$check_exist_rate_ta) {
                                //Tao rate plan TA
                                $params = [
                                    'rap_hotel_id'            => $hID,
                                    'rap_title'               => $rap_title_ta,
                                    'rap_room_apply_id'       => $rap_room_apply_id,
                                    'rap_type_price'          => RatePlan::TYPE_PRICE_TA,
                                    'rap_surcharge_info'      => json_encode([]),
                                    'rap_accompanied_service' => json_encode([]),
                                    'rap_cancel_policy_info'  => '{"cancel_policy_info":[{"day":"3","fee":"10"},{"day":"3","fee":"5"},{"day":"7","fee":"0"}],"group_cancel_policy_info":{"num_rooms":"5","0":{"day":"7","fee":"10"},"1":{"day":"7","fee":"5"},"2":{"day":"15","fee":"0"}}}',
                                    'rap_active'              => ACTIVE,
                                    'rap_delete'              => $rate_ta_delete
                                ];

                                $data_return_ta = [];
                                $data_return_ta = $this->rateRepo->create($params);

                                if ($data_return_ta->rap_id > 0) {
                                    $data_return_ta->rooms()->attach($dataInsertRoomRatePlanTa);
                                    echo 'Create Rate Plan TA Success For Room ID ' . $hID . '<br>';
                                }
                            } else {
                                $dataRatePlan = $this->rateRepo->where('rap_title', '=', $rap_title_ta)
                                                               ->where('rap_hotel_id', '=', $hID)
                                                               ->get()->first();

                                if (!is_null($dataRatePlan)) {
                                    $dataRatePlan->rap_room_apply_id = $rap_room_apply_id;
                                    $dataRatePlan->save();

                                    $arr_room_id = json_decode($rap_room_apply_id, true);
                                    $dataRatePlan->rooms()->sync($arr_room_id);
                                    echo 'UPDATE Rate Plan TA Success For Room ID ' . $hID . '<br>';
                                }
                            }

                            if (!$check_exist_rate_ota) {
                                //Tao rate plan OTA
                                $params = [
                                    'rap_hotel_id'            => $hID,
                                    'rap_title'               => $rap_title_ota,
                                    'rap_room_apply_id'       => $rap_room_apply_id,
                                    'rap_type_price'          => RatePlan::TYPE_PRICE_OTA,
                                    'rap_surcharge_info'      => json_encode([]),
                                    'rap_accompanied_service' => json_encode([]),
                                    'rap_cancel_policy_info'  => '{"cancel_policy_info":[{"day":"3","fee":"10"},{"day":"3","fee":"5"},{"day":"7","fee":"0"}]}',
                                    'rap_active'              => ACTIVE,
                                    'rap_delete'              => $rate_ota_delete
                                ];

                                $data_return_ota = [];
                                $data_return_ota = $this->rateRepo->create($params);

                                if ($data_return_ota->rap_id > 0) {
                                    $data_return_ota->rooms()->attach($dataInsertRoomRatePlanOta);
                                    echo 'Create Rate Plan OTA Success For Room ID ' . $hID . '<br>';
                                }
                            } else {
                                $dataRatePlan = $this->rateRepo->where('rap_title', '=', $rap_title_ota)
                                                               ->where('rap_hotel_id', '=', $hID)
                                                               ->get()->first();

                                if (!is_null($dataRatePlan)) {
                                    $dataRatePlan->rap_room_apply_id = $rap_room_apply_id;
                                    $dataRatePlan->save();

                                    $arr_room_id = json_decode($rap_room_apply_id, true);
                                    $dataRatePlan->rooms()->sync($arr_room_id);
                                    echo 'UPDATE Rate Plan OTA Success For Room ID ' . $hID . '<br>';
                                }
                            }
                        }
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON RATE PLAN SUCCESS';die;
        }

    }

    public function updatePrice ($time_start, $time_finish)
    {
        die;
        $this->time_start  = strtotime($time_start);
        $this->time_finish = strtotime($time_finish);
        $offset    = 10;
        $page      = (int)Input::get('page', 0);
        $time_life = 3;
        $url       = route('update_price_rate_plan', [$time_start, $time_finish]);
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $params = array(
            'field_select' => 'hot_id',
            'take'         => $offset,
            'skip'         => $page * $offset,
            'order_by_asc' => 'hot_id'
        );

        $dataHotel        = $this->hotelRepo->getInfoHotel($params);
        $listHotelID      = $dataHotel->keyBy('hot_id')->keys()->toArray();
        $dataRoom = $this->roomRepo->select(['rom_id', 'rom_hotel'])
                                   ->whereIn('rom_hotel', $listHotelID)
                                   ->where('rom_delete', '=', ACTIVE)
                                   ->get();

        $listRoomID       = $dataRoom->keyBy('rom_id')->keys()->toArray();
        $dataRatePlanRoom = $this->roomRateRepo->whereIn('rrp_room_id', $listRoomID)
                                               ->with('ratePlans')
                                               ->get();

        $listRateOfRoom = [];
        if (is_object($dataRatePlanRoom) && !$dataRatePlanRoom->isEmpty()) {
            foreach ($dataRatePlanRoom as $infoRate) {
                $listRateOfRoom[$infoRate->rrp_room_id][] = $infoRate->ratePlans;
            }
        }

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            if (!empty ($listRateOfRoom)) {
                for ($i = $this->time_start; $i <= $this->time_finish; $i += 86400 * 31) {
                    $table_room_price     = "room_price_" . date('Ym', $i);
                    $table_room_allotment = "rooms_allotment_" . date('Ym', $i);
                    $table_room_service   = "room_services_status_" . date('Ym', $i);

                    $this->getPriceRoomId($listRoomID, $table_room_price);
                    $this->getAllotmentRoomId($listRoomID, $table_room_allotment);

                    $dataInsert           = [];
                    $dataPriceConvert     = [];
                    $dataAllotmentInsert  = [];
                    $dataAllotmentConvert = [];

                    $params = [
                        'table'        => $table_room_service,
                        'list_room'    => $listRoomID,
                        'order_by_asc' => 'romst_room_id'
                    ];

                    $dataPrice = $this->roomServices->getInfoRoomServices($params);

                    if (is_object($dataPrice) && !$dataPrice->isEmpty()) {
                        foreach ($dataPrice as $infoPrice) {
                            $hID  = $infoPrice->romst_hotel_id;
                            $rID  = $infoPrice->romst_room_id;
                            $time = $infoPrice->romst_time;

                            if (isset($listRateOfRoom[$rID])) {
                                foreach ($listRateOfRoom[$rID] as $infoRatePlan) {
                                    if (!isset($dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id])) {
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id] = $this->getDefaultDataPrice($i);
                                    }

                                    $price_publish = $infoPrice->romst_price_publish;
                                    if ($infoRatePlan->rap_type_price == RatePlan::TYPE_PRICE_TA) {
                                        $price          = $infoPrice->romst_price_ta;
                                        $price_contract = $infoPrice->romst_price_ta_contact;
                                        if ($price_publish <= 0) {
                                            $price_publish = $price * RoomPrice::RATE_TA_PRICE_PUBLISH;
                                        }
                                    } elseif ($infoRatePlan->rap_type_price == RatePlan::TYPE_PRICE_OTA) {
                                        $price          = $infoPrice->romst_price_ota;
                                        $price_contract = $infoPrice->romst_price_ota_contact;
                                        if ($price_publish <= 0) {
                                            $price_publish = $price * RoomPrice::RATE_OTA_PRICE_PUBLISH;
                                        }
                                    }

                                    $romst_price_season_contract = $infoPrice->romst_price_season_contact;
                                    $field_col = 'rop_col' . date('j', $time);

                                    if (!isset($this->listPriceOfRoom[$rID][$infoRatePlan->rap_id][$infoRatePlan->rap_type_price])) {
                                        if ($dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_hotel_id'] == 0) {
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_hotel_id']     = $hID;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_room_id']      = $rID;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_rate_plan_id'] = $infoRatePlan->rap_id;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_type_price']   = $infoRatePlan->rap_type_price;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_person_type']  = RoomPrice::NUM_PERSON_MAX_TYPE;
                                        }
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_info_price_publish'][$time]         = $price_publish;
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_info_price_contract'][$time]        = $price_contract;
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_info_price_season_contract'][$time] = $romst_price_season_contract;
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id][$field_col]                              = $price;
                                    } else {
                                        $dataPriceConvert = [];
                                    }
                                }
                            }

                            if (!isset($this->listAllotmentOfRoom[$rID])) {
                                $allotment_ota    = !empty($infoPrice->romst_allotment) ? $infoPrice->romst_allotment : 0;
                                $allotment_ta     = !empty($infoPrice->romst_allotment_special) ? $infoPrice->romst_allotment_special : 0;
                                $allotment_status = !empty($infoPrice->romst_status) ? $infoPrice->romst_status : 0;
                                $dataAllotmentConvert[$rID][$time] = [
                                    'roa_hotel_id'            => $hID,
                                    'roa_room_id'             => $rID,
                                    'roa_allotment_ota'       => $allotment_ota,
                                    'roa_allotment_ta'        => $allotment_ta,
                                    'roa_time'                => $time,
                                    'roa_check_allotment_pms' => NO_ACTIVE,
                                    'roa_status'              => $allotment_status
                                ];
                            }

                            if (isset($dataPriceConvert[$hID][$rID])) {
                                foreach ($dataPriceConvert[$hID][$rID] as $rateID => $info) {
                                    //Gan gia tri default
                                    if (!is_array($info['rop_info_price_contract'])) {
                                        $info['rop_info_price_contract'] = [];
                                    }
                                    if (!is_array($info['rop_info_price_publish'])) {
                                        $info['rop_info_price_publish'] = [];
                                    }
                                    if (!is_array($info['rop_info_price_season_contract'])) {
                                        $info['rop_info_price_season_contract'] = [];
                                    }

                                    $dataInsert[$info['rop_room_id']][$rateID] = $info;
                                    $dataInsert[$info['rop_room_id']][$rateID]['rop_info_price_publish']         = json_encode($info['rop_info_price_publish']);
                                    $dataInsert[$info['rop_room_id']][$rateID]['rop_info_price_contract']        = json_encode($info['rop_info_price_contract']);
                                    $dataInsert[$info['rop_room_id']][$rateID]['rop_info_price_season_contract'] = json_encode($info['rop_info_price_season_contract']);
                                }
                            }
                        }
                    }

                    //Neu co du lieu convert thi insert vao bang room price
                    if (!empty($dataInsert)) {
                        $dataInsertFinal = [];
                        foreach ($dataInsert as $roomInfo) {
                            foreach ($roomInfo as $rateInfo) {
                                $dataInsertFinal[] = $rateInfo;
                            }
                        }

                        $params_insert = ['table' => $table_room_price, 'data_insert' => $dataInsertFinal];
                        dnd($params_insert);die;
                        // echo $table_room_price . '==============================';
                        // echo '<pre>';
                        // print_r($dataInsert);
                        // echo '</pre>';die;
                        $this->roomPrice->insertPriceByData($params_insert);
                    }

                    //Insert allotmen vao bang rooms_allotment
                    if (isset($dataAllotmentConvert) && !empty($dataAllotmentConvert)) {
                        foreach ($dataAllotmentConvert as $roomID => $roomInfo) {
                            $params_insert = ['table' => $table_room_allotment, 'data_insert' => $roomInfo];
                            dnd($params_insert);die;
                            $this->roomAllotmentRepo->insertAllotmentByData($params_insert);
                        }
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON PRICE SUCCESS';die;
        }

    }

    public function convertPriceByHotelId ($hotelId, $time_start, $time_finish)
    {
        die;
        $this->time_start  = strtotime($time_start);
        $this->time_finish = strtotime($time_finish);
        $offset    = 10;
        $page      = (int)Input::get('page', 0);
        $time_life = 300;
        $url       = route('convert_price_by_hotel_id', [$hotelId, $time_start, $time_finish]);
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $params = array(
            'field_select' => 'hot_id',
            'take'         => $offset,
            'skip'         => $page * $offset,
            'order_by_asc' => 'hot_id'
        );

        $dataHotel = $this->hotelRepo->where('hot_id' , '=', $hotelId)
                                     ->get();

        $listHotelID      = [$hotelId];
        $dataRoom         = $this->roomRepo->getRoomInfoByListHotelId($listHotelID, ['field_select' => ['rom_id', 'rom_hotel']]);
        $listRoomID       = $dataRoom->keyBy('rom_id')->keys()->toArray();
        $dataRatePlanRoom = $this->roomRateRepo->whereIn('rrp_room_id', $listRoomID)
                                               ->with('ratePlans')
                                               ->get();

        $listRateOfRoom = [];
        if (is_object($dataRatePlanRoom) && !$dataRatePlanRoom->isEmpty()) {
            foreach ($dataRatePlanRoom as $infoRate) {
                $listRateOfRoom[$infoRate->rrp_room_id][] = $infoRate->ratePlans;
            }
        }

        if (is_object($dataHotel) && !$dataHotel->isEmpty()) {
            if (!empty ($listRateOfRoom)) {
                for ($i = $this->time_start; $i <= $this->time_finish; $i += 86400 * 31) {
                    $table_room_price     = "room_price_" . date('Ym', $i);
                    $table_room_allotment = "rooms_allotment_" . date('Ym', $i);
                    $table_room_service   = "room_services_status_" . date('Ym', $i);

                    $this->getPriceRoomId($listRoomID, $table_room_price);
                    $this->getAllotmentRoomId($listRoomID, $table_room_allotment);

                    $dataInsert           = [];
                    $dataPriceConvert     = [];
                    $dataAllotmentInsert  = [];
                    $dataAllotmentConvert = [];

                    $params = [
                        'table'        => $table_room_service,
                        'list_room'    => $listRoomID,
                        'order_by_asc' => 'romst_room_id'
                    ];

                    $dataPrice = $this->roomServices->getInfoRoomServices($params);
                    echo $table_room_service;
                    dnd($dataPrice);
                    if (is_object($dataPrice) && !$dataPrice->isEmpty()) {
                        foreach ($dataPrice as $infoPrice) {
                            $hID  = $infoPrice->romst_hotel_id;
                            $rID  = $infoPrice->romst_room_id;
                            $time = $infoPrice->romst_time;

                            if (isset($listRateOfRoom[$rID])) {
                                foreach ($listRateOfRoom[$rID] as $infoRatePlan) {
                                    if (!isset($dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id])) {
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id] = $this->getDefaultDataPrice($i);
                                    }

                                    $price_publish = $infoPrice->romst_price_publish;
                                    if ($infoRatePlan->rap_type_price == RatePlan::TYPE_PRICE_TA) {
                                        $price          = $infoPrice->romst_price_ta;
                                        $price_contract = $infoPrice->romst_price_ta_contact;
                                        if ($price_publish <= 0) {
                                            $price_publish = $price * RoomPrice::RATE_TA_PRICE_PUBLISH;
                                        }
                                    } elseif ($infoRatePlan->rap_type_price == RatePlan::TYPE_PRICE_OTA) {
                                        $price          = $infoPrice->romst_price_ota;
                                        $price_contract = $infoPrice->romst_price_ota_contact;
                                        if ($price_publish <= 0) {
                                            $price_publish = $price * RoomPrice::RATE_OTA_PRICE_PUBLISH;
                                        }
                                    }

                                    $romst_price_season_contract = $infoPrice->romst_price_season_contact;
                                    $field_col = 'rop_col' . date('j', $time);

                                    if (!isset($this->listPriceOfRoom[$rID][$infoRatePlan->rap_id][$infoRatePlan->rap_type_price])) {
                                        if ($dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_hotel_id'] == 0) {
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_hotel_id']     = $hID;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_room_id']      = $rID;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_rate_plan_id'] = $infoRatePlan->rap_id;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_type_price']   = $infoRatePlan->rap_type_price;
                                            $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_person_type']  = RoomPrice::NUM_PERSON_MAX_TYPE;
                                        }
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_info_price_publish'][$time]         = $price_publish;
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_info_price_contract'][$time]        = $price_contract;
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id]['rop_info_price_season_contract'][$time] = $romst_price_season_contract;
                                        $dataPriceConvert[$hID][$rID][$infoRatePlan->rap_id][$field_col]                              = $price;
                                    } else {
                                        $dataPriceConvert = [];
                                    }
                                }
                            }

                            if (!isset($this->listAllotmentOfRoom[$rID])) {
                                $allotment_ota    = !empty($infoPrice->romst_allotment) ? $infoPrice->romst_allotment : 0;
                                $allotment_ta     = !empty($infoPrice->romst_allotment_special) ? $infoPrice->romst_allotment_special : 0;
                                $allotment_status = !empty($infoPrice->romst_status) ? $infoPrice->romst_status : 0;
                                $dataAllotmentConvert[$rID][$time] = [
                                    'roa_hotel_id'            => $hID,
                                    'roa_room_id'             => $rID,
                                    'roa_allotment_ota'       => $allotment_ota,
                                    'roa_allotment_ta'        => $allotment_ta,
                                    'roa_time'                => $time,
                                    'roa_check_allotment_pms' => NO_ACTIVE,
                                    'roa_status'              => $allotment_status
                                ];
                            }

                            if (isset($dataPriceConvert[$hID][$rID])) {
                                foreach ($dataPriceConvert[$hID][$rID] as $rateID => $info) {
                                    //Gan gia tri default
                                    if (!is_array($info['rop_info_price_contract'])) {
                                        $info['rop_info_price_contract'] = [];
                                    }
                                    if (!is_array($info['rop_info_price_publish'])) {
                                        $info['rop_info_price_publish'] = [];
                                    }
                                    if (!is_array($info['rop_info_price_season_contract'])) {
                                        $info['rop_info_price_season_contract'] = [];
                                    }

                                    $dataInsert[$info['rop_room_id']][$rateID] = $info;
                                    $dataInsert[$info['rop_room_id']][$rateID]['rop_info_price_publish']         = json_encode($info['rop_info_price_publish']);
                                    $dataInsert[$info['rop_room_id']][$rateID]['rop_info_price_contract']        = json_encode($info['rop_info_price_contract']);
                                    $dataInsert[$info['rop_room_id']][$rateID]['rop_info_price_season_contract'] = json_encode($info['rop_info_price_season_contract']);
                                }
                            }
                        }
                    }

                    //Neu co du lieu convert thi insert vao bang room price
                    if (!empty($dataInsert)) {
                        $dataInsertFinal = [];
                        foreach ($dataInsert as $roomInfo) {
                            foreach ($roomInfo as $rateInfo) {
                                $dataInsertFinal[] = $rateInfo;
                            }
                        }

                        $params_insert = ['table' => $table_room_price, 'data_insert' => $dataInsertFinal];
                        // echo $table_room_price . '==============================';
                        // echo '<pre>';
                        // print_r($dataInsert);
                        // echo '</pre>';
                        $this->roomPrice->insertPriceByData($params_insert);
                        echo 'CRON PRICE SUCCESS';die;
                    }

                    //Insert allotmen vao bang rooms_allotment
                    if (isset($dataAllotmentConvert) && !empty($dataAllotmentConvert)) {
                        foreach ($dataAllotmentConvert as $roomID => $roomInfo) {
                            $params_insert = ['table' => $table_room_allotment, 'data_insert' => $roomInfo];
                            $this->roomAllotmentRepo->insertAllotmentByData($params_insert);
                        }
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON PRICE SUCCESS';die;
        }

    }

   /**
     * [convertBookingHotel description]
     * Convert thong tin boo_book_info theo logic rate plan
     * @return [type] [description]
     */
    public function ConvertBookingHotelById ($booking_id, $is_ota_booking)
    {
        die;
        $dataBooking = $this->hotelBooking->select(['boo_id', 'boo_hotel', 'boo_book_info', 'boo_book_info_booked', 'boo_book_info_modified'])
                                          ->where('boo_id', '=', $booking_id)
                                          ->get();

        if (is_object($dataBooking) && !$dataBooking->isEmpty()) {
            //Get thong tin rate of room
            $listHotelID = $dataBooking->keyBy('boo_hotel')->keys()->toArray();
            $dataRoom    = $this->roomRepo->getRoomInfoByListHotelId($listHotelID, ['field_select' => ['rom_id', 'rom_hotel']]);
            $listRoomID  = $dataRoom->keyBy('rom_id')->keys()->toArray();
            $this->getRateByRoomId($listRoomID);

            $boo_book_info_update          = [];
            $boo_book_info_booked_update   = [];
            $boo_book_info_modified_update = [];
            //Gan Key Rate Plan vao boo_book_info
            foreach ($dataBooking as $infoBook) {
                $boo_book_info          = json_decode($infoBook->boo_book_info, true);
                $boo_book_info_booked   = json_decode($infoBook->boo_book_info_booked, true);
                $boo_book_info_modified = json_decode($infoBook->boo_book_info_modified, true);

                if (count($boo_book_info) > 0) {
                    foreach ($boo_book_info as $rID => $info) {
                        if (isset($info['numroom'])) {
                            //Booking lay gia OTA
                            if (isset($this->listRateOfRoom[$rID])) {
                                if ($is_ota_booking) {
                                    $rateID = $this->listRateOfRoom[$rID][RatePlan::TYPE_PRICE_OTA];
                                } else {
                                    $rateID = $this->listRateOfRoom[$rID][RatePlan::TYPE_PRICE_TA];
                                }

                                if (isset($info['rate_id']) && $info['rate_id'] > 0) {
                                    $rateID = $info['rate_id'];
                                }
                                $boo_book_info_update[$infoBook->boo_id][$rID]['list_day_allotment']         = isset($info['list_day_allotment']) ? $info['list_day_allotment'] : "";
                                $boo_book_info_update[$infoBook->boo_id][$rID]['list_day_allotment_special'] = isset($info['list_day_allotment_special']) ? $info['list_day_allotment_special'] : "";
                                $boo_book_info_update[$infoBook->boo_id][$rID]['num_allotment']              = isset($info['num_allotment']) ? $info['num_allotment'] : "";
                                $boo_book_info_update[$infoBook->boo_id][$rID]['num_allotment_special']      = isset($info['num_allotment_special']) ? $info['num_allotment_special'] : "";

                                if (isset($info['list_day_allotment'])) {
                                    unset($info['list_day_allotment']);
                                }
                                if (isset($info['list_day_allotment_special'])) {
                                    unset($info['list_day_allotment_special']);
                                }
                                if (isset($info['num_allotment'])) {
                                    unset($info['num_allotment']);
                                }
                                if (isset($info['num_allotment_special'])) {
                                    unset($info['num_allotment_special']);
                                }
                                $boo_book_info_update[$infoBook->boo_id][$rID]['rate_info'][$rateID] = $info;
                            }
                        } else {
                            $check_booking_update = 0;
                            break;
                        }
                    }
                }

                if (count($boo_book_info_booked) > 0
                    && $check_booking_update) {
                    foreach ($boo_book_info_booked as $rID => $info) {
                        //Booking lay gia OTA
                        if (isset($this->listRateOfRoom[$rID])) {
                            if ($is_ota_booking) {
                                $rateID = $this->listRateOfRoom[$rID][RatePlan::TYPE_PRICE_OTA];
                            } else {
                                $rateID = $this->listRateOfRoom[$rID][RatePlan::TYPE_PRICE_TA];
                            }
                            if (isset($info['rate_id']) && $info['rate_id'] > 0) {
                                $rateID = $info['rate_id'];
                            }
                            $boo_book_info_booked_update[$infoBook->boo_id][$rID][$rateID] = $info;
                        }
                    }
                }

                if (count($boo_book_info_modified) > 0
                    && $check_booking_update) {
                    foreach ($boo_book_info_modified as $rID => $info) {
                        //Booking lay gia OTA
                        if (isset($this->listRateOfRoom[$rID])) {
                            if ($is_ota_booking) {
                                $rateID = $this->listRateOfRoom[$rID][RatePlan::TYPE_PRICE_OTA];
                            } else {
                                $rateID = $this->listRateOfRoom[$rID][RatePlan::TYPE_PRICE_TA];
                            }
                            if (isset($info['rate_id']) && $info['rate_id'] > 0) {
                                $rateID = $info['rate_id'];
                            }
                            $boo_book_info_modified_update[$infoBook->boo_id][$rID][$rateID] = $info;
                        }
                    }
                }
            }

            //Update lai thong tin boo_book_info
            if (count($boo_book_info_update) > 0) {
                foreach ($boo_book_info_update as $bookID => $infoBook) {
                    $dataUpdate = ['boo_book_info' => json_encode($infoBook)];

                    if (isset($boo_book_info_booked_update[$bookID])
                        && count($boo_book_info_booked_update[$bookID]) > 0) {
                        $dataUpdate['boo_book_info_booked'] = json_encode($boo_book_info_booked_update[$bookID]);
                    }
                    if (isset($boo_book_info_modified_update[$bookID])
                        && count($boo_book_info_modified_update[$bookID]) > 0) {
                        $dataUpdate['boo_book_info_modified'] = json_encode($boo_book_info_modified_update[$bookID]);
                    }
                    dnd($dataUpdate);die;
                    echo "UPDATE BOOKING HOTEL SUCCESS";
                    // $this->hotelBooking->where('boo_id', '=', $bookID)
                                       // ->update($dataUpdate);
                }
            }
        }
    }

    public function deletePriceRatePerson ()
    {
        $listHotelID = [12006, 154, 10978, 11593, 9763];
        $dataRatePlan = $this->rateRepo->select(['rap_id', 'rap_hotel_id', 'rap_parent_id'])
                                        ->whereIn('rap_hotel_id', $listHotelID)
                                        ->where('rap_parent_id', '=', 0)
                                        ->get();
        $dataReturn = [];

        if (is_object($dataRatePlan) && !$dataRatePlan->isEmpty()) {
            foreach ($dataRatePlan as $infoRate) {
                $this->roomPrice->setTable('room_price_201607');
                $dataPrice = $this->roomPrice->select(['rop_id', 'rop_hotel_id'])
                                             ->where('rop_rate_plan_id', '=', $infoRate->rap_id)
                                             ->where('rop_hotel_id', '=', $infoRate->rap_hotel_id)
                                             ->where('rop_person_type', '=', 1)
                                             ->get();

                if (is_object($dataPrice) && !$dataPrice->isEmpty()) {
                    $dataReturn = [];
                    foreach ($dataPrice as $infoPrice) {
                        $dataReturn[] = $infoPrice->rop_id;
                    }

                    if (count ($dataReturn) > 0) {
                        $this->roomPrice->setTable('room_price_201607');
                        $this->roomPrice->whereIn('rop_id', $dataReturn)
                                        ->limit(30)->delete();

                        echo "DELETE SUCCESS " . implode(', ', $dataReturn);
                    }
                }
            }
        }
    }

}