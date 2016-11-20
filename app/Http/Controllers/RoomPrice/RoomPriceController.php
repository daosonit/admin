<?php namespace App\Http\Controllers\RoomPrice;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Components\Hotel;
use App\Models\Components\Rooms;
use App\Models\Components\RoomPrice;
use App\Models\Components\RoomsAllotment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Components\RatePlan;
use App\Models\Components\RoomsRatePlans;
use App\Models\Components\RoomRatePromotion;
use App\Models\Components\Promotion;
use View, Input, Event, Session, Redirect, App, DB, Log, Response;
use App\Mytour\Classes\AdminLog;

class RoomPriceController extends Controller {

    private $hotelID;

    private $time_checkin;

    private $time_checkout;

    private $dataPriceInsert = [];

    private $dataPriceUpdate = [];

    private $dataAllotmentInsert = [];

    private $dataAllotmentUpdate = [];

    private $defaultPrice = [];

    private $defaultAllotment = [];

    private $dataPromotion = [];

    private $defaultRatePromo = [];

    /**
     * % Commission OTA
     * @var [type]
     */
    private $commission_ota;

    /**
     * % Markup TA
     * @var [type]
     */
    private $mark_up;

    /**
     * Kiểu tính giá: Có thuế, phí hay không
     * @var [boolean]
     */
    private $tax_fee;

    /**
     * Loai KS
     * @var integer
     */
    private $typeHotel = -1;

    /**
     * Kieu KS HMS Or PMS
     */
    private $hotelPms = 0;

    public function __construct(RoomPrice $roomPrice
        , Rooms $rooms, RoomsAllotment $roomsAllotment
        , Hotel $hotel
        , RatePlan $ratePlan
        , RoomsRatePlans $roomsRatePlan
        , AdminLog $adminLog
        , Promotion $promotion
        , RoomRatePromotion $roomRatePromo)
    {
        $this->userHasRole('sales.staff');
        $this->hotel          = $hotel;
        $this->rooms          = $rooms;
        $this->ratePlan       = $ratePlan;
        $this->roomsRatePlan  = $roomsRatePlan;
        $this->roomPrice      = $roomPrice;
        $this->roomsAllotment = $roomsAllotment;
        $this->adminLog       = $adminLog;
        $this->promotion      = $promotion;
        $this->roomRatePromo  = $roomRatePromo;

        $this->time_checkin  = strtotime(date('m/d/Y'));
        $this->time_checkout = strtotime(date('m/d/Y')) + 6 * 86400;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function show($hotelID)
    {
        $this->hotelID = $hotelID;
        $time_checkin  = $this->time_checkin;
        $time_checkout = $this->time_checkout;

        $params = [
            'time_checkin'  => $time_checkin,
            'time_checkout' => $time_checkout
        ];

        $data           = $this->getPriceAjaxByDate($params);
        $hotelID        = $this->hotelID;
        $mark_up        = $this->mark_up;
        $commission_ota = $this->commission_ota;
        $tax_fee        = $this->tax_fee;
        $typeHotel      = $this->typeHotel;
        $hotelPms       = $this->hotelPms;
        $dataRoomInfo   = $data['dataRoomInfo'];
        $ratePlanInfo   = $data['ratePlanInfo'];
        $priceData      = $data['priceData'];
        $allotmentData  = $data['allotmentData'];
        $ratePricePromo = $data['ratePricePromo'];

        // dump($priceData);
        return View('components.modules.room_price.show', compact('dataRoomInfo', 'ratePlanInfo', 'priceData', 'allotmentData', 'time_checkin', 'time_checkout', 'hotelID', 'mark_up', 'commission_ota', 'ratePricePromo', 'typeHotel', 'hotelPms', 'tax_fee'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function excutePrice(Request $request)
    {
        $hotelID             = Input::get('hotel_id');
        $this->hotelID       = $hotelID;
        $markup_ta           = Input::get('markup_ta');
        $commission_ota      = Input::get('commission_ota');

        $room_rate_info      = json_decode(Input::get('room_rate_info'), true);
        $this->time_checkin  = Input::get('time_checkin');
        $this->time_checkout = Input::get('time_checkout');

        //Update thong tin markup TA commission OTA of Hotel
        $info_hotel = $this->hotel->find($hotelID);

        if (($info_hotel->hot_mark_up != $markup_ta) || ($info_hotel->hot_commission_ota != $commission_ota))
        {
            $params = ['hotel_id' => $hotelID, 'data_update' => ['hot_mark_up' => $markup_ta, 'hot_commission_ota' => $commission_ota]];
            $this->hotel->updateInfoHotelById($params);

            $uri    = $request->path();
            $adm_id = $request->user()->id;
            $ip     = $request->ip();

            $params_update = array(
                                   'id'        => $this->hotelID,
                                   'ip'        => $ip,
                                   'type'      => 2,
                                   'adm_id'    => $adm_id,
                                   'uri'       => $uri,
                                   'table'     => 'hotels'
                                   );

            $this->saveLogRoomPrice($params_update);
        }

        $dataRoomOfHotel = $this->rooms->getRoomInfoByHotelId($hotelID, ['field_select' => 'rom_id', 'active' => ACTIVE]);

        if (is_object($dataRoomOfHotel) && !$dataRoomOfHotel->isEmpty()) {
            foreach ($dataRoomOfHotel as $roomInfo) {
                $roomID = $roomInfo->rom_id;
                if (isset($room_rate_info[$roomID])) {
                    foreach ($room_rate_info[$roomID] as $rateID => $rateInfo) {
                        //Check neu la KS PMS thi ko cap nhat Gia OTA
                        if ($info_hotel->hot_pms_active && $rateInfo['type_price']) {
                            continue;
                        }

                        for ($personType = 1; $personType <= 2; $personType++) {
                            //Tao don gia 1 nguoi
                            if ($personType == 1) {
                                $rate_id_person = $this->createRatePlanPerson($rateID);
                                $rate_id_new = $rate_id_person;
                            } else {
                                $rate_id_new = $rateID;
                            }

                            for ($i = $this->time_checkin; $i <= $this->time_checkout; $i += 86400) {
                                $price_ta_in   = Input::get('price_ta_in_' . $roomID . $rateID . $personType . $i);
                                $price_ota_out = Input::get('price_ota_out_' . $roomID . $rateID . $personType . $i);

                                $dataPriceInput['ta_in'][$i]   = ($price_ta_in != "") ? (str_replace(",", "", $price_ta_in)) : "";
                                $dataPriceInput['ota_out'][$i] = ($price_ota_out != "") ? (str_replace(",", "", $price_ota_out)) : "";
                            }

                            $dataPrice = [
                                'hotel_id'       => $hotelID,
                                'room_id'        => $roomID,
                                'rate_plan_id'   => $rate_id_new,
                                'person_type'   => $personType,
                                'commission_ota' => $commission_ota,
                                'markup_ta'      => $markup_ta,
                                'price_input'    => $dataPriceInput,
                                'price_type'     => $rateInfo['type_price']
                            ];

                            $this->excutePriceByData($dataPrice);
                        }
                    }
                }

                for ($i = $this->time_checkin; $i <= $this->time_checkout; $i += 86400) {
                    $allotment_ota          = Input::get('allotment_ota_' . $roomID . $i);
                    $dataAllotmentInput[$i] = !empty($allotment_ota) ? intval($allotment_ota) : "";
                }

                //Check neu la KS PMS thi ko cap nhat Allotment OTA
                if ($info_hotel->hot_pms_active) {
                    continue;
                }

                $dataAllotment = [
                                'room_id'         => $roomID,
                                'allotment_input' => $dataAllotmentInput
                             ];

                $this->excuteAllotmentByData($dataAllotment);
            }
        }
        //Cap nhat thong tin Price
        $check_insert_price = $this->insertPrice($request);
        $check_update_price = $this->updatePrice($request);

        //Cap nhat thong tin Allotment
        $check_insert_allotment = $this->insertAllotment($request);
        $check_update_allotment = $this->updateAllotment($request);

        if ($check_insert_price || $check_update_price
            || $check_insert_allotment || $check_update_allotment) {
            //Alert thong tin cap nhat thanh cong
            $_msg_alert = SUCCESS_ALERT;
            $dataMsg = View('layouts.includes.success-alert', compact('_msg_alert'))->render();
        } else {
             //Alert thong tin cap nhat that bai
            $_msg_alert = ERROR_ALERT;
            $dataMsg = View('layouts.includes.error-alert', compact('_msg_alert'))->render();
        }

        Session::flash('_msg_alert', $dataMsg);

        return Redirect::route('room-price-show', [$hotelID]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function getCommissionHotel()
    {
        //Get markup TA, commisson OTA Of Hotel
        try {
            $dataHotel            = $this->hotel->findOrFail($this->hotelID);
            $this->mark_up        = $dataHotel->hot_mark_up;
            $this->commission_ota = $dataHotel->hot_commission_ota;
            $this->tax_fee        = $dataHotel->hot_tax_fee;
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return Response::make('Hotel not found!', 404);
        }

    }

    /**
     * [getPriceAjaxByDate description]
     * Get Price by date
     * @return json
     */
    public function getPriceAjaxByDate ($params)
    {
        $this->time_checkin  = array_get($params, 'time_checkin', 0);
        $this->time_checkout = array_get($params, 'time_checkout', 0);
        $this->getTypeHotel();

        //Get markup TA, commisson OTA Of Hotel
        $this->getCommissionHotel();

        $ratePlanInfo   = [];
        //getInfo room of Hotel
        $dataRoom = $this->rooms->getRoomInfoByHotelId($this->hotelID, ['field_select' => ['rom_id', 'rom_name'], 'active' => ACTIVE])->load('roomRates');

        $dataRoomInfo = [];
        foreach ($dataRoom as $infoRoom) {
            $dataRoomInfo[$infoRoom->rom_id] = $infoRoom->rom_name;

            $dataRateInfo = $infoRoom->roomRates->load('ratePlans');

            if (is_object($dataRateInfo) && !$dataRateInfo->isEmpty()) {
                foreach ($dataRateInfo as $infoRate) {
                    if ($infoRate->ratePlans->rap_active == ACTIVE
                        && $infoRate->ratePlans->rap_delete == NO_ACTIVE
                        && $infoRate->ratePlans->rap_parent_id == 0) {
                        //Check KS PMS thi chi lay allotment PMS
                        if ($this->hotelPms
                            && !$infoRate->ratePlans->rap_pms_id
                            && $infoRate->ratePlans->rap_type_price) {
                            continue;
                        }

                        //Check KS OTA, TA thi chi ap dung rate plan tuong ung
                        if ($this->typeHotel == Hotel::TYPE_HOTEL_TA
                            && $infoRate->ratePlans->rap_type_price != RatePlan::TYPE_PRICE_TA) {
                            continue;
                        }

                        if ($this->typeHotel == Hotel::TYPE_HOTEL_OTA
                            && $infoRate->ratePlans->rap_type_price != RatePlan::TYPE_PRICE_OTA) {
                            continue;
                        }

                        $ratePlanInfo[$infoRoom->rom_id][$infoRate->ratePlans->rap_id] = [
                            'title'        => $infoRate->ratePlans->rap_title,
                            'type_price'   => $infoRate->ratePlans->rap_type_price,
                            'hidden_price' => $infoRate->rrp_hidden_price,
                            'price_email'  => $infoRate->rrp_price_email,
                            'rrp_id'       => $infoRate->rrp_id
                        ];
                    }
                }
            }
        }

        // Fill a default value to all rooms
        $priceData = $this->getDefaultPrice();

        $allotmentData = $this->getDefaultAllotment();

        $ratePromoData = $this->getDefaultRatePromo();

        // get price
        if ($this->time_checkin > 0 && $this->time_checkout > 0) {
            for ($i = strtime_firstday_month($this->time_checkin); $i <= $this->time_checkout; $i = strtime_next_month($i)) {
                $price_table     = 'room_price_' . date('Ym', $i);
                $allotment_table = 'rooms_allotment_' . date('Ym', $i);
                $this->fillPriceData($price_table, $allotment_table, $priceData, $allotmentData, $ratePromoData);
            }
        }

        $dataReturn = [
            'hotelID'        => $this->hotelID,
            'dataRoomInfo'   => $dataRoomInfo,
            'ratePlanInfo'   => $ratePlanInfo,
            'priceData'      => $priceData,
            'allotmentData'  => $allotmentData,
            'ratePricePromo' => $ratePromoData,
        ];

        return $dataReturn;
    }

    public function showRoomInfoAjax ()
    {
        $hotelID        = Input::get('hot_id');
        $this->hotelID = $hotelID;
        $time_checkin   = Input::get('time_start');
        $time_checkout  = Input::get('time_finish');

        $params = [
            'time_checkin'  => $time_checkin,
            'time_checkout' => $time_checkout
        ];

        $data           = $this->getPriceAjaxByDate($params);
        $dataRoomInfo   = $data['dataRoomInfo'];
        $ratePlanInfo   = $data['ratePlanInfo'];
        $mark_up        = $this->mark_up;
        $commission_ota = $this->commission_ota;
        $typeHotel      = $this->typeHotel;
        $hotelPms       = $this->hotelPms;
        $priceData      = $data['priceData'];
        $allotmentData  = $data['allotmentData'];
        $ratePricePromo = $data['ratePricePromo'];
        $time_checkin   = $this->time_checkin;
        $time_checkout  = $this->time_checkout;

        return View('components.modules.room_price.get-price-ajax', compact('dataRoomInfo', 'ratePlanInfo', 'priceData', 'allotmentData', 'time_checkin', 'time_checkout', 'hotelID', 'mark_up', 'commission_ota', 'ratePricePromo', 'typeHotel', 'hotelPms'));
    }

    /**
     * Điền giá trị từ DB vào mảng thông tin mặc định
     * @param  string $priceTable       Tên bảng lưu dữ liệu phòng
     * @param  string $allotmentTable       Hotel's ID list
     * @param  array &$priceDefault     Tham trị của mảng thông tin phòng mặc định (lấy từ method getDefaultPrice ở trên)
     * @param  array &$allotmentDefault     Tham trị của mảng thông tin phòng mặc định (lấy từ method getDefaultAllotment ở trên)
     * @return array
     */
    private function fillPriceData($priceTable, $allotmentTable, &$priceDefault, &$allotmentDefault, &$ratePromoDefault)
    {
        $params['table'] = $priceTable;
        $paramsAllotment = [
            'table'         => $allotmentTable,
            'time_checkin'  => $this->time_checkin,
            'time_checkout' => $this->time_checkout,
        ];
        //Get data price
        $dataRoomOfHotel = $this->rooms->getRoomInfoByHotelId($this->hotelID, ['active' => ACTIVE]);
        $listRoom = $dataRoomOfHotel->keyBy('rom_id')->keys()->toArray();

        $dataRateOfHotel = $this->ratePlan->getRatePlanByHotelId(['hotel_id' => $this->hotelID, 'active' => ACTIVE]);
        $listRate        = $dataRateOfHotel->keyBy('rap_id')->keys()->toArray();

        $dataPrice     = $this->roomPrice->getDataByListRoomRate($listRoom, $listRate, $params)->load('roomRatePlans');
        $dataAllotment = $this->roomsAllotment->getDataByListRoom($listRoom, $paramsAllotment);

        //get info allotment of hotel
        if (is_object($dataAllotment) && !$dataAllotment->isEmpty()) {
            foreach ($dataAllotment as $infoAllot) {
                //Check KS PMS thi chi lay allotment PMS
                if ($this->hotelPms && !$infoAllot->roa_check_allotment_pms) {
                    continue;
                }

                $hotelID = $infoAllot->roa_hotel_id;
                $roomID  = $infoAllot->roa_room_id;
                $timeInt = $infoAllot->roa_time;
                $locked = $infoAllot->roa_status ? 0 : 1;

                $allotmentDefault[$hotelID][$roomID][$timeInt] = [
                    'num_allot' => $infoAllot->roa_allotment_ota,
                    'locked'    => $locked
                ];
            }
        }

        //Get Price Of Room Rate
        if (is_object($dataPrice) && !$dataPrice->isEmpty()) {
            foreach ($dataPrice as $infoPrice) {
                $hid         = (int) $infoPrice->rop_hotel_id;
                $roomID      = (int) $infoPrice->rop_room_id;
                $rateID      = (int) $infoPrice->rop_rate_plan_id;
                $person_type = (int) $infoPrice->rop_person_type;

                $rop_info_price_publish   = $infoPrice->rop_info_price_publish;
                $room_info_price_contract = $infoPrice->rop_info_price_contract;

                // Room's status
                $hiddenPrice = $infoPrice->roomRatePlans->rrp_hidden_price;
                $priceEmail  = $infoPrice->roomRatePlans->rrp_price_email;
                $show_price_person = 0;
                $rate_parent_id = 0;

                //Thong tin Discount KM OTA, TA
                $info_price_promo_ta  = [];
                $info_price_promo_ota = [];

                $check_is_price_person = 0;

                foreach ($room_info_price_contract as $timeInt => $infoPriceContract) {
                    $locked    = isset($allotmentDefault[$this->hotelID][$roomID][$timeInt]['locked']) ? $allotmentDefault[$this->hotelID][$roomID][$timeInt]['locked'] : 0;
                    $field_col = 'rop_col' . date('j', $timeInt);

                    $price_ta           = 0;
                    $price_contract_ta  = 0;
                    $price_ota          = 0;
                    $price_contract_ota = 0;

                    $rate_parent_id = 0;
                    if ($person_type == RoomPrice::NUM_PERSON_MIN_TYPE) {
                        if ($infoPrice[$field_col] > 0
                            && $timeInt >= $this->time_checkin && $timeInt <= $this->time_checkout) {
                            $show_price_person = 1;
                        }
                        $rate_parent_id = $infoPrice->ratePlans->rap_parent_id;
                        //Check KS PMS thi chi lay allotment PMS
                        if ($this->hotelPms
                            && !$infoPrice->ratePlans->rap_pms_id
                            && $infoPrice->ratePlans->rap_type_price) {
                            continue;
                        }
                    }

                    if ($infoPrice->rop_type_price) {
                        $price_ta           = 0;
                        $price_contract_ta  = 0;
                        $price_ota          = $infoPrice[$field_col];
                        $price_contract_ota = $infoPriceContract;
                    } else {
                        $price_ota          = 0;
                        $price_contract_ota = 0;
                        $price_ta           = $infoPrice[$field_col];
                        $price_contract_ta  = $infoPriceContract;
                    }

                    $dataPromotion = [];
                    if (isset($this->dataPromotion[$roomID][$rateID])) {
                        $dataPromotion = $this->dataPromotion[$roomID][$rateID];
                    } elseif ($rate_parent_id > 0 && isset($this->dataPromotion[$roomID][$rate_parent_id])) {
                        $dataPromotion = $this->dataPromotion[$roomID][$rate_parent_id];
                    }

                    if (!empty($dataPromotion)) {
                        foreach ($dataPromotion as $promotion) {
                            $dayWeek = date('N', $timeInt);
                            $ta_in   = $price_contract_ta;
                            $ta_out  = $price_ta;
                            $ota_in  = $price_contract_ota;
                            $ota_out = $price_ota;
                            $proh_day_deny = $promotion['proh_day_deny'];

                            if (!in_array($timeInt, $proh_day_deny)) {
                                if ($promotion['proh_promo_type'] == Promotion::TYPE_PROMO_OTA) {
                                    $proh_promotion_info = $promotion['proh_promotion_info'];

                                    switch ($promotion['proh_discount_type']) {
                                        case Promotion::TYPE_DISCOUNT_PERCENT:
                                            if ($proh_promotion_info[$dayWeek] > 0) {
                                                $ota_in = $price_contract_ota - ($price_contract_ota * $proh_promotion_info[$dayWeek] / 100);
                                                $ota_out = $price_ota - ($price_ota * $proh_promotion_info[$dayWeek] / 100);
                                            }
                                            break;

                                        case Promotion::TYPE_DISCOUNT_MONEY:
                                            if ($proh_promotion_info[$dayWeek] > 0) {
                                                $price_discount_ota = doubleval(str_replace(",", "", $proh_promotion_info[$dayWeek]));
                                                $ota_in = $price_contract_ota - $price_discount_ota;
                                                $ota_out = $price_ota - $price_discount_ota;
                                            }
                                            break;

                                        case Promotion::TYPE_DISCOUNT_FREE_NIGHT:
                                            $num_room_apply = $promotion['proh_free_night_num'] - $promotion['proh_free_night_discount'];
                                            $ota_in  = (double) ($price_contract_ota * $num_room_apply) / $promotion['proh_free_night_num'];
                                            $ota_out = (double) ($price_ota * $num_room_apply) / $promotion['proh_free_night_num'];
                                            break;
                                    }
                                } else {
                                    $info_price_ta = json_decode($promotion['info_price_promo_ta'], true);
                                    if (isset($info_price_ta['price_contract'][$dayWeek])
                                        && $info_price_ta['price_contract'][$dayWeek] > 0) {
                                        $ta_in = $info_price_ta['price_contract'][$dayWeek];
                                    }
                                    if (isset($info_price_ta['price_discount'][$dayWeek])
                                        && $info_price_ta['price_discount'][$dayWeek] > 0) {
                                        $ta_out = $info_price_ta['price_discount'][$dayWeek];
                                    }
                                }
                            }

                            if (isset($ratePromoDefault[$roomID][$rateID][$promotion['proh_id']]['promo_price'][$person_type][$timeInt])) {
                                $ratePromoDefault[$roomID][$rateID][$promotion['proh_id']]['promo_price'][$person_type][$timeInt] = [
                                    'ta_in'   => $ta_in,
                                    'ta_out'  => $ta_out,
                                    'ota_in'  => $ota_in,
                                    'ota_out' => $ota_out,
                                    'locked'  => $locked
                                ];
                            } elseif ($person_type == RoomPrice::NUM_PERSON_MIN_TYPE && $show_price_person
                                && $timeInt >= $this->time_checkin && $timeInt <= $this->time_checkout
                                && $rate_parent_id > 0) {
                                $ratePromoDefault[$roomID][$rate_parent_id][$promotion['proh_id']]['promo_price'][$person_type][$timeInt] = [
                                    'ta_in'   => $ta_in,
                                    'ta_out'  => $ta_out,
                                    'ota_in'  => $ota_in,
                                    'ota_out' => $ota_out,
                                    'locked'  => $locked
                                ];
                            }

                            //sap xep mang gia KM theo person type
                            if (isset($ratePromoDefault[$roomID][$rateID][$promotion['proh_id']]['promo_price'])
                                && count($ratePromoDefault[$roomID][$rateID][$promotion['proh_id']]['promo_price']) > 1) {
                                ksort($ratePromoDefault[$roomID][$rateID][$promotion['proh_id']]['promo_price']);
                            } elseif($rate_parent_id > 0
                                && isset($ratePromoDefault[$roomID][$rate_parent_id][$promotion['proh_id']]['promo_price'])
                                && count($ratePromoDefault[$roomID][$rate_parent_id][$promotion['proh_id']]['promo_price']) > 1) {
                                ksort($ratePromoDefault[$roomID][$rate_parent_id][$promotion['proh_id']]['promo_price']);
                            }
                        }
                    }

                    $price = [
                        'hotel_id'       => $hid,
                        'room_id'        => $roomID,
                        'person_type'    => $person_type,
                        'rate_plan_id'   => $infoPrice->rop_rate_plan_id,
                        'rate_parent_id' => $rate_parent_id,
                        'person_type'    => $person_type,
                        'price_type'     => $infoPrice->rop_type_price,
                        'time'           => $timeInt,
                        'public'         => $rop_info_price_publish[$timeInt],
                        'ta_in'          => $price_contract_ta,
                        'ta_out'         => $price_ta,
                        'ota_in'         => $price_contract_ota,
                        'ota_out'        => $price_ota,
                        'hidden'         => $hiddenPrice,
                        'price_email'    => $priceEmail,
                        'locked'         => $locked,
                    ];

                    if (isset($priceDefault[$hid][$roomID][$rateID][$person_type][$timeInt])) {
                        $priceDefault[$hid][$roomID][$rateID][$person_type][$timeInt] = $price;
                    } elseif($person_type == RoomPrice::NUM_PERSON_MIN_TYPE && $show_price_person
                        && $timeInt >= $this->time_checkin && $timeInt <= $this->time_checkout
                        && $rate_parent_id > 0) {
                        //Set Value Default Price Person
                        if (!$check_is_price_person) {
                            for ($d = $this->time_checkin; $d <= $this->time_checkout; $d += 86400) {
                                $priceDefault[$hid][$roomID][$rate_parent_id][$person_type][$d] = 0;
                            }
                        }
                        $check_is_price_person = 1;
                        $priceDefault[$hid][$roomID][$rate_parent_id][$person_type][$timeInt] = $price;
                    }
                }

                //sap xep mang gia theo person type
                if (isset($priceDefault[$hid][$roomID][$rateID])
                    && count($priceDefault[$hid][$roomID][$rateID]) > 1) {
                    ksort($priceDefault[$hid][$roomID][$rateID]);
                } elseif($rate_parent_id > 0
                    && isset($priceDefault[$hid][$roomID][$rate_parent_id])
                    && count($priceDefault[$hid][$roomID][$rate_parent_id]) > 1) {
                    ksort($priceDefault[$hid][$roomID][$rate_parent_id]);
                }
            }
        }

    }

    /**
     * Lấy toàn bộ thông tin phòng khách sạn và điền vào giá trị mặc định cho các loại giá phòng
     * @param  string $strHotelID Chuỗi ID khách sạn
     * @return array
     */
    private function setDefaultPriceAllotment()
    {
        $roomID    = [];

        // Get regular room & fill default value
        $listRoom = $this->rooms->getRoomInfoByHotelId($this->hotelID, ['active' => ACTIVE]);

        if (is_object($listRoom) && !$listRoom->isEmpty()) {
            foreach ($listRoom as $row) {
                $hid    = (int) $row['rom_hotel'];
                $roomID = (int) $row['rom_id'];
                $params = ['field_select' => 'rrp_rate_plan_id', 'room_id' => $roomID];
                $dataRoomIdByRateId = $this->roomsRatePlan->getRateIdIdByRoomId($params)->load('ratePlans');

                // Thông tin được fill theo từng ngày trong khoảng thời gian mà user chọn (checkin/checkout)

                for ($daytime = $this->time_checkin; $daytime <= $this->time_checkout; $daytime += 86400) {
                    if (is_object($dataRoomIdByRateId) && !$dataRoomIdByRateId->isEmpty()) {
                        foreach ($dataRoomIdByRateId as $rateInfo) {
                            if ($rateInfo->ratePlans->rap_delete == NO_ACTIVE
                                && $rateInfo->ratePlans->rap_parent_id == 0) {
                                //Check KS PMS thi chi lay Gia PMS
                                if ($this->hotelPms
                                    && !$rateInfo->ratePlans->rap_pms_id
                                    && $rateInfo->ratePlans->rap_type_price) {
                                    continue;
                                }

                                $this->defaultPrice[$hid][$roomID][$rateInfo->rrp_rate_plan_id][RoomPrice::NUM_PERSON_MAX_TYPE][$daytime] = [
                                    'hotel_id'       => $hid,
                                    'room_id'        => $roomID,
                                    'rate_plan_id'   => $rateInfo->rrp_rate_plan_id,
                                    'rate_parent_id' => 0,
                                    'price_type'     => 0,
                                    'person_type'    => 0,
                                    'time'           => $daytime,
                                    'public'         => 0,
                                    'ta_in'          => 0,
                                    'ta_out'         => 0,
                                    'ota_in'         => 0,
                                    'ota_out'        => 0,
                                    'hidden'         => 0,
                                    'price_email'    => 0,
                                    'locked'         => 0,
                                ];
                            }
                        }
                    }

                    //Set Default Allotment Of Hotel
                    $this->defaultAllotment[$hid][$roomID][$daytime] = [
                        'locked'    => 0,
                        'num_allot' => 0
                    ];
                }
            }
        }
    }

    /**
     * [getDefaultPrice description]
     * Get Gia phong mac dinh
     * @return [type] [description]
     */
    private function getDefaultPrice ()
    {
        if (empty($this->defaultPrice)) {
            $this->setDefaultPriceAllotment();
        }

        return $this->defaultPrice;
    }

    /**
     * [getDefaultAllotment description]
     * get Allotment mac dinh
     * @return [type] [description]
     */
    private function getDefaultAllotment ()
    {
        if (empty($this->defaultAllotment)) {
            $this->setDefaultPriceAllotment();
        }

        return $this->defaultAllotment;
    }

    /**
     * [getDefaultPromo description]
     * get KM cho Don gia
     * @return [type] [description]
     */
    private function getDefaultRatePromo ()
    {
        if (empty($this->defaultRatePromo)) {
            $this->setDefaultPromo();
        }

        return $this->defaultRatePromo;
    }

    private function setDefaultPromo()
    {
        $dataPromo = $this->promotion->getPromoByHotelId($this->hotelID)->load('roomRatePlans');
        if (is_object($dataPromo) && !$dataPromo->isEmpty()) {
            foreach ($dataPromo as $info) {
                if (!empty($info->proh_time_book_finish)) {
                    if ($info->proh_time_book_finish < time()) {
                        continue;
                    }
                } elseif($info->proh_time_finish < time()) {
                    continue;
                }
                $dataRoomRate = $info->roomRatePlans->load('ratePlans');
                if (is_object($dataRoomRate) && !$dataRoomRate->isEmpty()) {
                    foreach ($dataRoomRate as $roomRateInfo) {
                        $roomID = $roomRateInfo->rrp_room_id;
                        $rateID = $roomRateInfo->rrp_rate_plan_id;
                        $info_price_promo_ta = [];

                        if ($info->proh_promo_type == Promotion::TYPE_PROMO_TA) {
                            $dataRoomPromoTa = $info->roomRates()
                                                         ->where('rapr_room_rate_id', '=', $roomRateInfo->rrp_id)
                                                         ->first();

                            $info_price_promo_ta = $dataRoomPromoTa->rapr_price_promo_info;
                        }
                        $this->dataPromotion[$roomID][$rateID][$info->proh_id] = json_decode($info, true);
                        $this->dataPromotion[$roomID][$rateID][$info->proh_id]['info_price_promo_ta'] = $info_price_promo_ta;
                        $this->defaultRatePromo[$roomID][$rateID][$info->proh_id]['promo_info'] = [
                            'title'      => $info->proh_title . "_" . $roomRateInfo->ratePlans->rap_title,
                            'type_promo' => $info->proh_promo_type
                        ];

                        for ($daytime = $this->time_checkin; $daytime <= $this->time_checkout; $daytime += 86400) {
                            $this->defaultRatePromo[$roomID][$rateID][$info->proh_id]['promo_price'][RoomPrice::NUM_PERSON_MAX_TYPE][$daytime] = [
                                'ta_in'   => 0,
                                'ta_out'  => 0,
                                'ota_in'  => 0,
                                'ota_out' => 0,
                                'locked'  => 0
                            ];
                        }
                    }
                }
            }
        }
    }

    public function updateAllotmentOfDay(Request $request)
    {
        $hotelID  = Input::get('hID');
        $hotelPms = Input::get('hPms');
        $roomID   = Input::get('rID');
        $time     = Input::get('time');
        $value    = Input::get('value');

        $table  = 'rooms_allotment_' . date('Ym', $time);

        $this->roomsAllotment->setTable($table);
        $dataAllotment = $this->roomsAllotment->where('roa_room_id', '=', $roomID)
                                              ->where('roa_time', '=', $time);

        if ($hotelPms) {
            $dataAllotment->where('roa_check_allotment_pms', '=', ACTIVE);
            $check_allotment_pms = ACTIVE;
        } else {
            $dataAllotment->where('roa_check_allotment_pms', '=', NO_ACTIVE);
            $check_allotment_pms = NO_ACTIVE;
        }
        $dataAllotment = $dataAllotment->first();

        DB::enableQueryLog();
        if (is_null($dataAllotment)) {
            $time_first = strtotime(date('Ym01', $time));
            $time_last  = strtotime(date('Ymt', $time));

            for ($d = $time_first; $d <= $time_last; $d += 86400) {
                $status = $value;
                if ($d == $time) {
                    $status = abs($value - 1);
                }
                $dataAllotmentInsert[] = [
                    'roa_hotel_id'            => $hotelID,
                    'roa_room_id'             => $roomID,
                    'roa_allotment_ota'       => 0,
                    'roa_allotment_ta'        => 0,
                    'roa_check_allotment_pms' => $check_allotment_pms,
                    'roa_time'                => $d,
                    'roa_status'              => $status
                ];
            }
            $this->roomsAllotment->insert($dataAllotmentInsert);
            $type   = 1;
            $roa_id = 0;
        } else {
            $dataUpdate = ['roa_status' => abs($value - 1)];
            $this->roomsAllotment->where('roa_id', '=', $dataAllotment->roa_id)
                                 ->update($dataUpdate);

            $roa_id = $dataAllotment->roa_id;
            $type   = 2;
        }

        $value_return = (int) $value + 1;

        //Save Log
        $uri    = $request->path();
        $adm_id = $request->user()->id;
        $ip     = $request->ip();

        $params_update = array(
                               'id'     => $roa_id,
                               'ip'     => $ip,
                               'type'   => $type,
                               'adm_id' => $adm_id,
                               'uri'    => $uri,
                               'table'  => 'rooms_allotment'
                               );

        $this->saveLogRoomPrice($params_update);

        return $value_return;
    }

    public function updateAllotmentTimeRange(Request $request)
    {
        $hotelID       = Input::get('hotel_id');
        $this->hotelID = $hotelID;
        $roomID        = Input::get('room_id');
        $num_allot     = Input::get('num_allot_modal');
        $time_range    = Input::get('time_range_allotment_modal');
        $day_apply     = Input::get('day_apply_allotment_modal');
        $time_range    = explode('-', $time_range);

        //check chon khoang tgian, nhap gia
        if (!isset($time_range[0]) || empty($time_range[0])) {
            $_msg_alert = 'Bạn hãy chọn khoảng thời gian cập nhật';
            $dataMsg    = View('layouts.includes.error-alert', compact('_msg_alert'))->render();
            return json_encode(['load_page' => 0, 'msg' => $dataMsg]);
        }

        if ($num_allot == "") {
            $_msg_alert = 'Bạn hãy nhập số allotment.';
            $dataMsg    = View('layouts.includes.error-alert', compact('_msg_alert'))->render();
            return json_encode(['load_page' => 0, 'msg' => $dataMsg]);
        }

        $time_start         = trim($time_range[0]);
        $time_start         = str_replace('/', '-', $time_start);
        $this->time_checkin = strtotime($time_start);

        $time_finish         = trim($time_range[1]);
        $time_finish         = str_replace('/', '-', $time_finish);
        $this->time_checkout = strtotime($time_finish);

        $dataAllotmentInput = [];

        for ($i = $this->time_checkin; $i <= $this->time_checkout; $i += 86400) {
            if(count($day_apply) > 0) {
                if (in_array(date('N', $i), $day_apply)) {
                    $dataAllotmentInput[$i] = $num_allot;
                }
            }
        }

        if (count($dataAllotmentInput) <= 0) {
            $_msg_alert = 'Bạn hãy chọn ngày áp dụng allotment.';
            $dataMsg    = View('layouts.includes.error-alert', compact('_msg_alert'))->render();
            return json_encode(['load_page' => 0, 'msg' => $dataMsg]);
        }

        $dataAllotment = [
            'room_id'         => $roomID,
            'allotment_input' => $dataAllotmentInput
        ];

        $this->excuteAllotmentByData($dataAllotment);

        $check_insert_allotment = $this->insertAllotment($request);
        $check_update_allotment = $this->updateAllotment($request);
        if ($check_insert_allotment || $check_update_allotment) {
            $_msg_alert = SUCCESS_ALERT;
            $dataMsg    = View('layouts.includes.success-alert', compact('_msg_alert'))->render();
        } else {
            $_msg_alert = ERROR_ALERT;
            $dataMsg    = View('layouts.includes.error-alert', compact('_msg_alert'))->render();
        }

        return json_encode(['load_page' => 1, 'msg' => $dataMsg]);
    }

    private function excutePriceByData ($dataPrice)
    {
        $roomID      = array_get($dataPrice, 'room_id', 0);
        $rateID      = array_get($dataPrice, 'rate_plan_id', 0);
        $person_type = array_get($dataPrice, 'person_type', 0);

        if ($this->time_checkin > 0 && $this->time_checkout > 0) {
            for ($i = strtime_firstday_month($this->time_checkin); $i <= $this->time_checkout; $i = strtime_next_month($i)) {
                $params = [
                    'room_id'      => $roomID,
                    'rate_plan_id' => $rateID,
                    'table'        => 'room_price_' . date('Ym', $i)
                ];

                $dataPriceCheckin = $this->roomPrice->getPriceByRoomRateId($params);
                $this->getDataExcutePrice($dataPriceCheckin, $dataPrice, $i);
            }
        }

    }

    public function getDataExcutePrice ($dataPrice, $params, $timeInt)
    {
        $hotelID        = array_get($params, 'hotel_id', 0);
        $roomID         = array_get($params, 'room_id', 0);
        $rateID         = array_get($params, 'rate_plan_id', 0);
        $markup_ta      = array_get($params, 'markup_ta', 0);
        $commission_ota = array_get($params, 'commission_ota', 0);
        $hiddenPrice    = array_get($params, 'hidden_price', 1);
        $dataPriceInput = array_get($params, 'price_input', 0);
        $price_type     = array_get($params, 'price_type', 0);
        $person_type    = array_get($params, 'person_type', 0);

        $prepend_tbl = date('Ym', $timeInt);
        $tbl_name    = 'room_price_' . $prepend_tbl;

        if (is_object($dataPrice) && !$dataPrice->isEmpty()) {
            foreach ($dataPrice as $info) {
                //Get du lieu bang time checkin
                $time_start_first = $timeInt;
                $time_start_last = strtotime(date('Ymt', $timeInt));
                if ($time_start_last >= $this->time_checkout) {
                    $time_start_last  = $this->time_checkout;
                    $time_start_first = strtotime(date('Ym01', $timeInt));
                }

                $priceContract = $info->rop_info_price_contract;
                $pricePublish = $info->rop_info_price_publish;

                $this->dataPriceUpdate[$prepend_tbl]['table'] = $tbl_name;

                $this->dataPriceUpdate[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_type']            = $price_type;
                $this->dataPriceUpdate[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_contract']        = $priceContract;
                $this->dataPriceUpdate[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_publish']         = $pricePublish;
                $this->dataPriceUpdate[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_season_contract'] = $info->rop_info_price_season_contract;

                for ($i = $time_start_first; $i <= $time_start_last; $i += 86400) {
                    $field_col = 'rop_col' . date('j', $i);
                    //Kieu gia OTA
                    if ($price_type) {
                        $price_ota_out = (double) (isset($dataPriceInput['ota_out'][$i]) && $dataPriceInput['ota_out'][$i] != "") ? $dataPriceInput['ota_out'][$i] : $info->$field_col;
                        $this->dataPriceUpdate[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price'][$field_col]  = $price_ota_out;
                        $this->dataPriceUpdate[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_contract'][$i] = $price_ota_out - ($price_ota_out * $commission_ota) / 100;
                        $this->dataPriceUpdate[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_publish'][$i]  = $price_ota_out * RoomPrice::RATE_OTA_PRICE_PUBLISH;
                    } else {
                        $prc_ta_in_old = isset($priceContract[$i]) ? $priceContract[$i] : 0;
                        $price_ta_in   = (double) (isset($dataPriceInput['ta_in'][$i]) && $dataPriceInput['ta_in'][$i] != "") ? $dataPriceInput['ta_in'][$i] : $prc_ta_in_old;
                        $price_ta      = $price_ta_in * (($markup_ta / 100) + 1);
                        $price_ta      = generatePrice($price_ta, 'down', 0);
                        $this->dataPriceUpdate[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_contract'][$i] = $price_ta_in;
                        $this->dataPriceUpdate[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price'][$field_col]  = $price_ta;
                        $this->dataPriceUpdate[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_publish'][$i]  = $price_ta * RoomPrice::RATE_TA_PRICE_PUBLISH;
                    }
                }
            }
        } else {
            //Get du lieu bang time checkin
            $time_start_first      = strtotime(date('Ym01', $timeInt));
            $time_start_last       = strtotime(date('Ymt', $timeInt));
            $this->dataPriceInsert[$prepend_tbl]['table'] = $tbl_name;
            $this->dataPriceInsert[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_type']   = $price_type;

            for ($i = 1; $i <= 31; $i++) {
                $field_col = 'rop_col' . $i;
                $this->dataPriceInsert[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price'][$field_col] = 0;
            }

            for ($i = $time_start_first; $i <= $time_start_last; $i += 86400) {
                $field_col = 'rop_col' . date('j', $i);

                if ($price_type) {
                    if (!isset($dataPriceInput['ota_out'][$i])) {
                        $dataPriceInput['ota_out'][$i] = 0;
                    }

                    $price_ota = (double) $dataPriceInput['ota_out'][$i];
                    $this->dataPriceInsert[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price'][$field_col]  = $price_ota;
                    $this->dataPriceInsert[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_contract'][$i] = $price_ota - ($price_ota * $commission_ota) / 100;
                    $this->dataPriceInsert[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_publish'][$i]  = $price_ota * RoomPrice::RATE_OTA_PRICE_PUBLISH;
                } else {
                    if (!isset($dataPriceInput['ta_in'][$i])) {
                        $dataPriceInput['ta_in'][$i] = 0;
                    }

                    $price_ta_in = (double) $dataPriceInput['ta_in'][$i];
                    $price_ta = $price_ta_in * (($markup_ta / 100) + 1);
                    $price_ta = generatePrice($price_ta, 'down', 0);
                    $this->dataPriceInsert[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_contract'][$i] = $price_ta_in;
                    $this->dataPriceInsert[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price'][$field_col]  = $price_ta;
                    $this->dataPriceInsert[$prepend_tbl]['info'][$roomID][$rateID][$person_type]['price_publish'][$i]  = $price_ta * RoomPrice::RATE_TA_PRICE_PUBLISH;
                }
            }
        }
    }

    public function insertPrice ($request)
    {
        // dd($this->dataPriceInsert);
        // echo '<pre>';
        // print_r($this->dataPriceInsert);
        // echo '</pre>';
        $value_return    = 0;
        $dataPriceInsert = [];
        foreach ($this->dataPriceInsert as $roomID => $infoRoomPrice) {
            if (!empty ($infoRoomPrice['table']) && !empty ($infoRoomPrice['info'])) {
                foreach ($infoRoomPrice['info'] as $roomID => $infoRate) {
                    foreach ($infoRate as $rateID => $infoPersonType) {
                        foreach ($infoPersonType as $personType => $infoPrice) {
                            $dataPrice = [
                                    'rop_hotel_id'                   => $this->hotelID,
                                    'rop_room_id'                    => $roomID,
                                    'rop_rate_plan_id'               => $rateID,
                                    'rop_type_price'                 => $infoPrice['price_type'],
                                    'rop_person_type'                => $personType,
                                    'rop_info_price_publish'         => json_encode($infoPrice['price_publish']),
                                    'rop_info_price_contract'        => json_encode($infoPrice['price_contract']),
                                    'rop_info_price_season_contract' => json_encode([])
                                 ];

                            $dataPriceInsert[] = array_merge($dataPrice, $infoPrice['price']);
                        }
                    }
                }

                if (!empty ($dataPriceInsert)) {
                    $paramsInsert = ['table' => $infoRoomPrice['table'], 'data_insert' => $dataPriceInsert];
                    if ($this->roomPrice->insertPriceByData($paramsInsert)) {
                        $uri    = $request->path();
                        $adm_id = $request->user()->id;
                        $ip     = $request->ip();

                        $params_insert = array(
                                               'id'        => ['room_id' => $roomID, 'rate_plan_id' => $rateID, 'person_type' => $personType],
                                               'ip'        => $ip,
                                               'type'      => 1,
                                               'adm_id'    => $adm_id,
                                               'uri'       => $uri,
                                               'table'     => 'room_price'
                                               );

                        $this->saveLogRoomPrice($params_insert);

                        $value_return = 1;
                    }
                    // reset lai mang du lieu gia insert
                    $dataPriceInsert = [];
                }
            }
        }
        // reset lai mang du lieu gia insert
        $this->dataPriceInsert = [];

        return $value_return;
    }

    public function updatePrice ($request)
    {
        // dd($this->dataPriceUpdate);
        // echo '<pre>';
        // print_r($this->dataPriceUpdate);
        // echo '</pre>';
        $value_return    = 0;
        $dataPriceUpdate = [];
        foreach ($this->dataPriceUpdate as $infoRoomPrice) {
            if (!empty ($infoRoomPrice['table']) && !empty ($infoRoomPrice['info'])) {
                foreach ($infoRoomPrice['info'] as $roomID => $infoRate) {
                    foreach ($infoRate as $rateID => $infoPersonType) {
                        foreach ($infoPersonType as $personType => $infoPrice) {
                            $dataPrice = [
                                            'rop_type_price'                 => $infoPrice['price_type'],
                                            'rop_info_price_publish'         => json_encode($infoPrice['price_publish']),
                                            'rop_info_price_contract'        => json_encode($infoPrice['price_contract']),
                                            'rop_info_price_season_contract' => json_encode($infoPrice['price_season_contract'])
                                         ];

                            $dataPriceUpdate = array_merge($dataPrice, $infoPrice['price']);
                            // dd($dataPriceUpdate);
                            $paramsUpdate = [
                                                'table'         => $infoRoomPrice['table'],
                                                'room_id'       => $roomID,
                                                'rate_plan_id'  => $rateID,
                                                'data_update'   => $dataPriceUpdate
                                            ];
                            if ($this->roomPrice->updatePriceByRoomRateId($paramsUpdate)) {
                                $uri    = $request->path();
                                $adm_id = $request->user()->id;
                                $ip     = $request->ip();

                                $params_update = array(
                                                       'id'        => ['room_id' => $roomID, 'rate_plan_id' => $rateID, 'person_type' => $personType],
                                                       'ip'        => $ip,
                                                       'type'      => 2,
                                                       'adm_id'    => $adm_id,
                                                       'uri'       => $uri,
                                                       'table'     => 'room_price'
                                                       );

                                $this->saveLogRoomPrice($params_update);

                                $value_return = 1;
                            }
                        }
                    }
                }
            }
        }

        // reset lai mang du lieu gia update
        $this->dataPriceUpdate = [];

        return $value_return;
    }

    private function excuteAllotmentByData ($dataAllotment)
    {
        $roomID = array_get($dataAllotment, 'room_id', 0);

        if ($this->time_checkin > 0 && $this->time_checkout > 0) {
            for ($i = strtime_firstday_month($this->time_checkin); $i <= $this->time_checkout; $i = strtime_next_month($i)) {
                $table_allotment = 'rooms_allotment_' . date('Ym', $i);
                $this->roomsAllotment->setTable($table_allotment);
                $check_allotment_exist = $this->roomsAllotment->where('roa_room_id', '=', $roomID)
                                                              ->where('roa_check_allotment_pms', '=', 0)
                                                              ->count();

                $this->getDataExcuteAllotment($check_allotment_exist, $dataAllotment, $i);
            }
        }
    }

    private function getDataExcuteAllotment($check_allotment_exist, $params, $timeInt)
    {
        $roomID             = array_get($params, 'room_id', 0);
        $dataAllotmentInput = array_get($params, 'allotment_input', 0);

        $prepend_tbl = date('Ym', $timeInt);
        $tbl_name    = 'rooms_allotment_' . $prepend_tbl;

        if ($check_allotment_exist) {
            //Get du lieu bang time checkin
            $time_start_first = $timeInt;
            $time_start_last  = strtotime(date('Ymt', $timeInt));
            if ($time_start_last >= $this->time_checkout) {
                $time_start_last  = $this->time_checkout;
                $time_start_first = strtotime(date('Ym01', $timeInt));
            }

            $sql_update = "UPDATE " . $tbl_name . " SET roa_allotment_ota = CASE";
            for ($i = $time_start_first; $i <= $time_start_last; $i += 86400) {
                if (isset($dataAllotmentInput[$i])) {
                    $num_allot = $dataAllotmentInput[$i] == "" ? 0 : $dataAllotmentInput[$i];
                    $sql_update .= " WHEN roa_time = " . $i . " THEN " . $num_allot;
                }
            }

            $sql_update .= " ELSE roa_allotment_ota END WHERE roa_room_id = " . $roomID . " AND roa_check_allotment_pms = 0";
            $this->dataAllotmentUpdate[$prepend_tbl][$roomID] = $sql_update;
        } else {
            //Get du lieu bang time checkin
            $time_start_first = strtotime(date('Ym01', $timeInt));
            $time_start_last  = strtotime(date('Ymt', $timeInt));

            for ($i = $time_start_first; $i <= $time_start_last; $i += 86400) {
                $num_allot = isset($dataAllotmentInput[$i]) ? $dataAllotmentInput[$i] : 0;
                $this->dataAllotmentInsert[$prepend_tbl][$roomID][$i] = $num_allot;
            }
        }
    }

    private function insertAllotment($request)
    {
        // dd($this->dataAllotmentInsert);
        // echo '<pre>';
        // print_r($this->dataAllotmentInsert);
        // echo '</pre>';die;
        $value_return        = 0;
        $dataAllotmentInsert = [];

        foreach ($this->dataAllotmentInsert as $prev_table => $infoRoomAllotment) {
            if (count($infoRoomAllotment) > 0) {
                foreach ($infoRoomAllotment as $roomID => $infoAllotment) {
                    foreach ($infoAllotment as $time => $num_allot) {
                        $dataAllotmentInsert[] = [
                            'roa_hotel_id'            => $this->hotelID,
                            'roa_room_id'             => $roomID,
                            'roa_allotment_ota'       => $num_allot,
                            'roa_allotment_ta'        => 0,
                            'roa_check_allotment_pms' => NO_ACTIVE,
                            'roa_time'                => $time,
                            'roa_status'              => ACTIVE
                        ];
                    }
                }

                if (!empty ($dataAllotmentInsert)) {
                    $table = "rooms_allotment_" . $prev_table;
                    $paramsInsert = ['table' => $table, 'data_insert' => $dataAllotmentInsert];

                    if ($this->roomsAllotment->insertAllotmentByData($paramsInsert)) {
                        $uri    = $request->path();
                        $adm_id = $request->user()->id;
                        $ip     = $request->ip();

                        $params_insert = array(
                                               'id'        => $roomID,
                                               'ip'        => $ip,
                                               'type'      => 1,
                                               'adm_id'    => $adm_id,
                                               'uri'       => $uri,
                                               'table'     => 'rooms_allotment'
                                               );

                        $this->saveLogRoomPrice($params_insert);

                        $value_return = 1;
                    }
                    // reset lai mang du lieu insert
                    $dataAllotmentInsert = [];
                }
            }
        }
        // reset lai mang du lieu insert
        $this->dataAllotmentInsert = [];

        return $value_return;
    }

    private function updateAllotment($request)
    {
        // dd($this->dataAllotmentUpdate);
        // echo '<pre>';
        // print_r($this->dataAllotmentUpdate);
        // echo '</pre>';die;
        $value_return        = 0;
        $dataAllotmentUpdate = [];

        foreach ($this->dataAllotmentUpdate as $prev_tbl => $infoRoomAllotment) {
            if (count($infoRoomAllotment) > 0) {
                foreach ($infoRoomAllotment as $roomID => $query_update) {
                    $table = 'rooms_allotment_' . $prev_tbl;
                    $paramsUpdate = [
                        'table'        => $table,
                        'query_update' => $query_update
                    ];

                    if ($this->roomsAllotment->updateAllotmentRawQuery($paramsUpdate)) {

                        $uri    = $request->path();
                        $adm_id = $request->user()->id;
                        $ip     = $request->ip();

                        $params_update = array(
                                               'id'        => $roomID,
                                               'ip'        => $ip,
                                               'type'      => 2,
                                               'adm_id'    => $adm_id,
                                               'uri'       => $uri,
                                               'table'     => 'rooms_allotment'
                                               );

                        $this->saveLogRoomPrice($params_update);

                        $value_return = 1;
                    }
                }
            }
        }
        //reset lai mang du lieu update
        $this->dataAllotmentUpdate = [];

        return $value_return;
    }

    public function updatePriceContractTaTimeRange(Request $request)
    {
        $hotelID               = Input::get('hotel_id');
        $this->hotelID         = $hotelID;
        $roomID                = Input::get('room_id');
        $rateID                = Input::get('rate_id');
        $price_contract        = Input::get('price_contract_room');
        $price_contract_person = Input::get('price_contract_person');
        $markup_ta             = Input::get('markup_ta');
        $commission_ota        = Input::get('commission_ota');
        $day_apply             = Input::get('day_apply_price_contract');
        $time_range            = Input::get('time_range_price_contract');
        $time_range            = explode('-', $time_range);

        //check chon khoang tgian, nhap gia
        if (!isset($time_range[0])|| empty($time_range[0])) {
            $_msg_alert = 'Bạn hãy chọn khoảng thời gian cập nhật';
            $dataMsg    = View('layouts.includes.error-alert', compact('_msg_alert'))->render();
            return json_encode(['load_page' => 0, 'msg' => $dataMsg]);
        }
        if ($price_contract == "") {
            $_msg_alert = 'Bạn hãy nhập giá vào của phòng';
            $dataMsg    = View('layouts.includes.error-alert', compact('_msg_alert'))->render();
            return json_encode(['load_page' => 0, 'msg' => $dataMsg]);
        }

        $time_start         = trim($time_range[0]);
        $time_start         = str_replace('/', '-', $time_start);
        $this->time_checkin = strtotime($time_start);

        $time_finish         = trim($time_range[1]);
        $time_finish         = str_replace('/', '-', $time_finish);
        $this->time_checkout = strtotime($time_finish);

        for ($personType = 1; $personType <= 2; $personType++) {
            //Tao don gia 1 nguoi
            if ($personType == 1) {
                $rate_id_person = $this->createRatePlanPerson($rateID);
                $rate_id_new = $rate_id_person;
            } else {
                $rate_id_new = $rateID;
            }

            $dataPriceInput = [];

            for ($i = $this->time_checkin; $i <= $this->time_checkout; $i += 86400) {
                if (count($day_apply) > 0) {
                    if (in_array(date('N', $i), $day_apply)) {
                        $price_ta_in = ($personType == RoomPrice::NUM_PERSON_MIN_TYPE) ? $price_contract_person : $price_contract;
                        $dataPriceInput['ta_in'][$i]   = ($price_ta_in != "") ? (str_replace(",", "", $price_ta_in)) : "";
                    }
                }
            }

            if (!isset($dataPriceInput['ta_in'])) {
                $_msg_alert = 'Bạn hãy chọn ngày áp dụng giá.';
                $dataMsg    = View('layouts.includes.error-alert', compact('_msg_alert'))->render();
                return json_encode(['load_page' => 0, 'msg' => $dataMsg]);
            }

            $dataPrice = [
                'hotel_id'       => $hotelID,
                'room_id'        => $roomID,
                'rate_plan_id'   => $rate_id_new,
                'person_type'   => $personType,
                'commission_ota' => $commission_ota,
                'markup_ta'      => $markup_ta,
                'price_input'    => $dataPriceInput,
                'price_type'     => RatePlan::TYPE_PRICE_TA
            ];

            $this->excutePriceByData($dataPrice);
        }

        $check_insert_price = $this->insertPrice($request);
        $check_update_price = $this->updatePrice($request);
        if ($check_insert_price || $check_update_price) {
            $_msg_alert = SUCCESS_ALERT;
            $dataMsg    = View('layouts.includes.success-alert', compact('_msg_alert'))->render();
        } else {
            $_msg_alert = ERROR_ALERT;
            $dataMsg    = View('layouts.includes.success-alert', compact('_msg_alert'))->render();
        }

        return json_encode(['load_page' => 1, 'msg' => $dataMsg]);
    }

    public function updatePriceOtaTimeRange(Request $request)
    {
        $hotelID          = Input::get('hotel_id');
        $this->hotelID    = $hotelID;
        $roomID           = Input::get('room_id');
        $rateID           = Input::get('rate_id');
        $price_ota        = Input::get('price_room');
        $price_ota_person = Input::get('price_person');
        $markup_ta        = Input::get('markup_ta');
        $commission_ota   = Input::get('commission_ota');
        $day_apply        = Input::get('day_apply_price');
        $time_range       = Input::get('time_range_price');
        $time_range       = explode('-', $time_range);

        //check chon khoang tgian, nhap gia
        if (!isset($time_range[0]) || empty($time_range[0])) {
            $_msg_alert = 'Bạn hãy chọn khoảng thời gian cập nhật';
            $dataMsg    = View('layouts.includes.error-alert', compact('_msg_alert'))->render();
            return json_encode(['load_page' => 0, 'msg' => $dataMsg]);
        }
        if ($price_ota == "") {
            $_msg_alert = 'Bạn hãy nhập giá vào của phòng';
            $dataMsg    = View('layouts.includes.error-alert', compact('_msg_alert'))->render();
            return json_encode(['load_page' => 0, 'msg' => $dataMsg]);
        }

        $time_start         = trim($time_range[0]);
        $time_start         = str_replace('/', '-', $time_start);
        $this->time_checkin = strtotime($time_start);

        $time_finish         = trim($time_range[1]);
        $time_finish         = str_replace('/', '-', $time_finish);
        $this->time_checkout = strtotime($time_finish);

        $dataPriceInput = [];
        for ($personType = 1; $personType <= 2; $personType++) {
            //Tao don gia 1 nguoi
            if ($personType == 1) {
                $rate_id_person = $this->createRatePlanPerson($rateID);
                $rate_id_new = $rate_id_person;
            } else {
                $rate_id_new = $rateID;
            }

            for ($i = $this->time_checkin; $i <= $this->time_checkout; $i += 86400) {
                if (in_array(date('N', $i), $day_apply)) {
                    $price_ota_out = ($personType == 1) ? $price_ota_person : $price_ota;
                    $dataPriceInput['ota_out'][$i]   = ($price_ota_out != "") ? (str_replace(",", "", $price_ota_out)) : "";
                }
            }

            if (!isset($dataPriceInput['ota_out'])) {
                $_msg_alert = 'Bạn hãy chọn ngày áp dụng giá.';
                $dataMsg    = View('layouts.includes.error-alert', compact('_msg_alert'))->render();
                return json_encode(['load_page' => 0, 'msg' => $dataMsg]);
            }

            $dataPrice = [
                'hotel_id'       => $hotelID,
                'room_id'        => $roomID,
                'rate_plan_id'   => $rate_id_new,
                'person_type'    => $personType,
                'commission_ota' => $commission_ota,
                'markup_ta'      => $markup_ta,
                'price_input'    => $dataPriceInput,
                'price_type'     => RatePlan::TYPE_PRICE_OTA
            ];

            $this->excutePriceByData($dataPrice);
        }

        $check_insert_price = $this->insertPrice($request);
        $check_update_price = $this->updatePrice($request);
        if ($check_insert_price || $check_update_price) {
            $_msg_alert = SUCCESS_ALERT;
            $dataMsg    = View('layouts.includes.success-alert', compact('_msg_alert'))->render();
        } else {
            $_msg_alert = ERROR_ALERT;
            $dataMsg    = View('layouts.includes.success-alert', compact('_msg_alert'))->render();
        }

        return json_encode(['load_page' => 1, 'msg' => $dataMsg]);
    }

    public function hiddenPriceAjax (Request $request)
    {
        $rrpID = Input::get('rrp_id');
        $value_hidden_price = 0;
        $value_price_email  = 0;
        if (Input::get('type') == 'hidden-price') {
            $value_hidden_price = abs(Input::get('value') - 1);
            if ($value_hidden_price) {
                $value_price_email  = Input::get('value');
            }
        } else {
            $value_price_email  = abs(Input::get('value') - 1);
            if ($value_hidden_price) {
                $value_hidden_price = Input::get('value');
            }
        }

        if  ($rrpID > 0) {
            $dataRoomRate = $this->roomsRatePlan->find($rrpID);
            $dataRoomRate->rrp_hidden_price = $value_hidden_price;
            $dataRoomRate->rrp_price_email  = $value_price_email;
            $dataRoomRate->save();

            //Cap nhat Rate Children
            $dataRateChildren = $this->ratePlan->select('rap_id')
                                               ->where('rap_parent_id', '=', $dataRoomRate->rrp_rate_plan_id)
                                               ->get();

            if (is_object($dataRateChildren) && !$dataRateChildren->isEmpty()) {
                foreach ($dataRateChildren as $infoRate) {
                    $dataRoomRateChild = $this->roomsRatePlan->where('rrp_room_id', '=', $dataRoomRate->rrp_room_id)
                                                        ->where('rrp_rate_plan_id', '=', $infoRate->rap_id)
                                                        ->first();

                    $dataRoomRateChild->rrp_hidden_price = $value_hidden_price;
                    $dataRoomRateChild->rrp_price_email  = $value_price_email;
                    $dataRoomRateChild->save();
                }
            }

            //Save Log
            $uri    = $request->path();
            $adm_id = $request->user()->id;
            $ip     = $request->ip();

            $params_update = array(
                                   'id'        => $rrpID,
                                   'ip'        => $ip,
                                   'type'      => 2,
                                   'adm_id'    => $adm_id,
                                   'uri'       => $uri,
                                   'table'     => 'rooms_rate_plans'
                                   );

            $this->saveLogRoomPrice($params_update);

            return 1;
        }

        return 0;
    }

    public function checkTaxFeeAjax()
    {
        $hotelID = Input::get('hotId');
        $valueChange = abs(Input::get('valueCheck') - 1);

        if  ($hotelID > 0) {
            $dataHotel = $this->hotel->find($hotelID);
            $dataHotel->hot_tax_fee = $valueChange;
            $dataHotel->save();

            return 1;
        }

        return 0;
    }

    private function saveLogRoomPrice($params)
    {
        $id        = $params['id'];
        $ip        = $params['ip'];
        $type      = $params['type'];
        $adm_id    = $params['adm_id'];
        $uri       = $params['uri'];
        $table     = $params['table'];

        $query = showQueryExecute();

        if($query != '')
        {
            switch ($table) {
                case 'room_price':
                    $prepend_checkin_tbl = date('Ym', $this->time_checkin);
                    $price_table         = 'room_price_' . $prepend_checkin_tbl;
                    $params_price        = [
                                            'table' => $price_table,
                                            'room_id' => $id['room_id'],
                                            'rate_plan_id' => $id['rate_plan_id'],
                                            'person_type' => $id['person_type']
                                            ];

                    $data_room_price     = $this->roomPrice->getPriceByRoomRateId($params_price)->toArray();

                    foreach ($data_room_price[0] as $field => $values) {
                        if ($field == 'rop_info_price_contract'
                            || $field == 'rop_info_price_publish'
                            || $field == 'rop_info_price_season_contract') {
                            $values = json_encode($values);
                        }

                        $data[$field] = base64_encode($values);
                    }

                    $this->adminLog->saveLog($data, $ip, $adm_id, 11, $price_table, $type , $query, $uri, 'rop_id', $data_room_price[0]['rop_id']);
                    break;

                case 'hotels':
                    $params_hotel = ['hotel_id' => $id];
                    $data_hotel = $this->hotel->getInfoHotelById($params_hotel)->toArray();

                    foreach ($data_hotel as $field => $values) {
                        $data[$field] = base64_encode($values);
                    }

                    $this->adminLog->saveLog($data, $ip, $adm_id, 11, 'hotels', $type , $query, $uri, 'hot_id', $id);
                    break;

                case 'rooms_allotment':
                    $prepend_checkin_tbl = date('Ym', $this->time_checkin);
                    $price_table         = 'rooms_allotment_' . $prepend_checkin_tbl;
                    $data_room_allotment = DB::table($price_table )->where('roa_id', '=', $id)->get();

                    if (isset($data_room_allotment[0])) {
                        foreach ($data_room_allotment[0] as $field => $values) {
                            $data[$field] = base64_encode($values);
                        }
                        $this->adminLog->saveLog($data, $ip, $adm_id, 11, $price_table, $type , $query, $uri, 'roa_id', $id);
                    }

                    break;

                case 'rate_plan':
                    $data_rate = $this->ratePlan->find($id)->toArray();

                    foreach ($data_rate as $field => $values) {
                        $data[$field] = base64_encode($values);
                    }

                    $this->adminLog->saveLog($data, $ip, $adm_id, 11, 'rate_plans', $type , $query, $uri, 'rap_id', $id);
                    break;
            }


        }
    }

    private function setTypeHotel ()
    {
        try {
            $dataHotel = $this->hotel->select(['hot_id', 'hot_ota_hybrid', 'hot_ota_only', 'hot_ota_hotel', 'hot_pms_active'])
                                     ->findOrFail($this->hotelID);

            if ($dataHotel->hot_ota_hybrid) {
                $this->typeHotel = Hotel::TYPE_HOTEL_HYBRID;
            } elseif ($dataHotel->hot_ota_only) {
                $this->typeHotel = Hotel::TYPE_HOTEL_OTA;
            } elseif ($dataHotel->hot_ota_hotel) {
                $this->typeHotel = Hotel::TYPE_HOTEL_OTA;
            } else {
                $this->typeHotel = Hotel::TYPE_HOTEL_TA;
            }

            $this->hotelPms = $dataHotel->hot_pms_active;
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());
            return Response::make('Hotel not found!', 404);
        }
    }

    /**
     * [getTypeHotel description]
     * Get kieu KS OTA, Hybrid, TA
     * @return [type] [description]
     */
    private function getTypeHotel ()
    {
        if ($this->typeHotel == -1) {
            $this->setTypeHotel();
        }

        return $this->typeHotel;
    }

    /**
     * [createRatePlanPerson description]
     * Tao don gia voi gia 1 nguoi
     * @param  [type] $rateID [description]
     * @return [type]         [description]
     */
    private function createRatePlanPerson ($rateID)
    {
        $dataRate = $this->ratePlan->where('rap_parent_id', '=', $rateID)->get()->first();
        if ($dataRate == null) {
            $dataRateParent = $this->ratePlan->find($rateID);
            $dataRateChildren = [
                'rap_title'               => 'Rate_Person_' . $dataRateParent->rap_title,
                'rap_hotel_id'            => $dataRateParent->rap_hotel_id,
                'rap_room_apply_id'       => $dataRateParent->rap_room_apply_id,
                'rap_parent_id'           => $dataRateParent->rap_id,
                'rap_type_price'          => $dataRateParent->rap_type_price,
                'rap_surcharge_info'      => $dataRateParent->rap_surcharge_info,
                'rap_accompanied_service' => $dataRateParent->rap_accompanied_service,
                'rap_cancel_policy_info'  => $dataRateParent->rap_cancel_policy_info,
                'rap_active'              => $dataRateParent->rap_active,
            ];

            //Create Rate Plan New
            $dataRatePlanNew   = $this->ratePlan->create($dataRateChildren);
            $data_return       = $dataRatePlanNew->rap_id;
            $arr_room_id_apply = json_decode($dataRateParent->rap_room_apply_id, true);
            $arr_room_of_promo = [];
            $arr_promo_id      = [];
            $price_promo_info  = [];

            if (!empty($arr_room_id_apply)) {
                $dataRoomRate = $this->roomsRatePlan->whereIn('rrp_room_id', $arr_room_id_apply)
                                                    ->where('rrp_rate_plan_id', '=', $dataRateParent->rap_id)
                                                    ->get();

                if (is_object($dataRoomRate) && !$dataRoomRate->isEmpty()) {
                    foreach ($dataRoomRate as $info) {
                        $arr_rrp_hidden_price[$info->rrp_room_id] = $info->rrp_hidden_price;
                        $arr_rrp_price_email[$info->rrp_room_id]   = $info->rrp_price_email;
                        if (!$info->roomRatePromo->isEmpty()) {
                            foreach ($info->roomRatePromo as $infoRoomPromo) {
                                $arr_promo_id[] = $infoRoomPromo->rapr_promotion_id;
                                $arr_room_of_promo[$infoRoomPromo->rapr_promotion_id][] = $info->rrp_room_id;
                                $price_promo_info[$infoRoomPromo->rapr_promotion_id][$info->rrp_room_id] = $infoRoomPromo->rapr_price_promo_info;
                            }
                        }
                    }

                    if (!empty ($arr_room_id_apply)) {
                        foreach ($arr_room_id_apply as $romID) {
                            $rrp_hidden_price = isset($arr_rrp_hidden_price[$romID]) ? $arr_rrp_hidden_price[$romID] : 1;
                            $rrp_price_email  = isset($arr_rrp_price_email[$romID]) ? $arr_rrp_price_email[$romID] : 1;
                            $dataRoomRateInsert = [
                                'rrp_hidden_price' => $rrp_hidden_price,
                                'rrp_price_email'  => $rrp_price_email
                            ];
                            $dataRatePlanNew->rooms()->attach($romID, $dataRoomRateInsert);
                        }
                    }

                    //Get All Promotion Apply Of Rate Plan
                    if (!empty($arr_promo_id)) {
                        $dataPromo = $this->promotion->select('proh_id')
                                                     ->whereIn('proh_id', $arr_promo_id)
                                                     ->get();

                        if (is_object($dataPromo) && !$dataPromo->isEmpty()) {
                            foreach ($dataPromo as $infoPromo) {
                                if (isset($arr_room_of_promo[$infoPromo->proh_id])) {
                                    foreach ($arr_room_of_promo[$infoPromo->proh_id] as $romID) {
                                        $roomRatePlan = $this->roomsRatePlan->where('rrp_room_id', '=', $romID)
                                                                           ->where('rrp_rate_plan_id', '=', $dataRatePlanNew->rap_id)
                                                                           ->first();

                                            $discount = isset($price_promo_info[$infoPromo->proh_id][$romID]) ? $price_promo_info[$infoPromo->proh_id][$romID] : "";
                                            $dataRoomRatePromo = [
                                                'rapr_price_promo_info' => $discount
                                            ];

                                        $infoPromo->roomRatePlans()->attach($roomRatePlan->rrp_id, $dataRoomRatePromo);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $data_return = $dataRate->rap_id;
        }

        return $data_return;
    }
}
