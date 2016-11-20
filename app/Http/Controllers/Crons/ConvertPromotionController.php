<?php namespace App\Http\Controllers\Crons;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Components\Promotion;
use App\Models\Components\PromotionsOta;
use App\Models\Components\RoomsRatePlans;
use App\Models\Components\RoomRatePromotion;
use App\Models\Components\PromotionRooms;
use App\Models\Components\PromotionRoomsTa;

use Illuminate\Http\Request;

use Input, View, Session;

class ConvertPromotionController extends Controller {

    public function __construct(Promotion $promoTa
        , PromotionsOta $promoOta
        , RoomsRatePlans $roomRate
        , PromotionRooms $promotionRooms
        , PromotionRoomsTa $promotionRoomsTa
        , RoomRatePromotion $roomRatePromo)
    {
        $this->promoTa          = $promoTa;
        $this->promoOta         = $promoOta;
        $this->roomRate         = $roomRate;
        $this->promotionRooms   = $promotionRooms;
        $this->roomRatePromo    = $roomRatePromo;
        $this->promotionRoomsTa = $promotionRoomsTa;
    }

    /**
     * Convert Promotion OTA
     *
     * @return Response
     */
    public function convertPromotionOta()
    {
        die;
        $offset    = 100;
        $page      = (int)Input::get('page', 0);
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('convert-promotion-ota');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $dataPromoTa = $this->promoTa->select('proh_promo_id_ota')
                                     ->where('proh_promo_id_ota', '>', 0)
                                     ->get();

        $dataPromoOtaNew = $dataPromoTa->keyBy('proh_promo_id_ota')->keys()->toArray();

        $dataPromo   = $this->promoOta->select('*')
                                      ->take($offset)
                                      ->skip($skip)
                                      ->get();

        $listIdPromo = $dataPromo->keyBy('proh_id')->keys()->toArray();

        if (is_object($dataPromo) && !$dataPromo->isEmpty()) {
            $dataRoomRate = $this->getRoomRateOfPromoOne($listIdPromo, Promotion::TYPE_PROMO_OTA);

            foreach ($dataPromo as $promoInfo) {
                if (isset($dataRoomRate[$promoInfo->proh_id])) {
                    if (!in_array($promoInfo->proh_id, $dataPromoOtaNew)) {
                        if ($promoInfo->proh_discount_type != Promotion::TYPE_DISCOUNT_PERCENT
                            && $promoInfo->proh_discount_type != Promotion::TYPE_DISCOUNT_MONEY) {
                            continue;
                        }

                        if ($promoInfo->proh_discount_type == Promotion::TYPE_DISCOUNT_PERCENT) {
                            if ($promoInfo->proh_apply_night_type != 1 && $promoInfo->proh_apply_night_type != 3) {
                                continue;
                            }
                        }

                        $promotion_info_update = [];
                        if ($promoInfo->proh_apply_night_type == 1) {
                            $promotion_info = json_decode($promoInfo->proh_promotion_info, true);
                            if (!empty($promotion_info)) {
                                $discount = isset($promotion_info[0]) ? $promotion_info[0] : 0;
                                for ($i = 1; $i <= 7; $i++) {
                                    $promotion_info_update[$i] = $discount;
                                }
                            }
                            $promotion_info_update = $promotion_info_update;
                        } else {
                            $promotion_info_update = json_decode($promoInfo->proh_promotion_info);
                        }

                        $dataPromoInsert = [
                            'proh_hotel'                => $promoInfo->proh_hotel,
                            'proh_title'                => $promoInfo->proh_title,
                            'proh_time_start'           => $promoInfo->proh_time_start,
                            'proh_time_finish'          => $promoInfo->proh_time_finish,
                            'proh_time_book_start'      => $promoInfo->proh_time_book_start,
                            'proh_time_book_finish'     => $promoInfo->proh_time_book_finish,
                            'proh_min_day_before'       => $promoInfo->proh_min_day_before,
                            'proh_max_day_before'       => $promoInfo->proh_max_day_after,
                            'proh_type'                 => $promoInfo->proh_type,
                            'proh_discount_type'        => $promoInfo->proh_discount_type,
                            'proh_apply_night_type'     => $promoInfo->proh_apply_night_type,
                            'proh_free_night'           => $promoInfo->proh_free_night,
                            'proh_free_night_num'       => $promoInfo->proh_free_night_num,
                            'proh_free_night_discount'  => $promoInfo->proh_free_night_discount,
                            'proh_promotion_info'       => $promotion_info_update,
                            'proh_day_deny'             => json_decode($promoInfo->proh_day_deny),
                            'proh_day_apply'            => [],
                            'proh_currency'             => $promoInfo->proh_currency,
                            'proh_cancel_policy'        => $promoInfo->proh_cancel_policy,
                            'proh_create_time'          => $promoInfo->proh_create_time,
                            'proh_create_admin_user_id' => $promoInfo->proh_create_user_id,
                            'proh_min_night'            => $promoInfo->proh_min_night,
                            'proh_max_night'            => $promoInfo->proh_max_night,
                            'proh_delete'               => $promoInfo->proh_delete,
                            'proh_delete_user'          => $promoInfo->proh_delete_user,
                            'proh_delete_time'          => $promoInfo->proh_delete_time,
                            'proh_active'               => $promoInfo->proh_active,
                            'proh_promo_type'           => 1,
                            'proh_promo_id_ota'         => $promoInfo->proh_id
                        ];

                        $newPromotion = $this->promoTa->create($dataPromoInsert);
                        $listIdRoomRate = isset($dataRoomRate[$promoInfo->proh_id]) ? $dataRoomRate[$promoInfo->proh_id] : [];
                        if (!empty($listIdRoomRate)) {
                            $roomRateId = [];
                            foreach ($listIdRoomRate as $roomRate) {
                                $data = $this->roomRatePromo->where('rapr_room_rate_id', '=', $roomRate)
                                                            ->where('rapr_promotion_id', '=', $newPromotion->proh_id)
                                                            ->get()->first();

                                if (is_null($data)) {
                                    $roomRateId[$roomRate] = $roomRate;
                                }
                            }

                            if (count($roomRateId) > 0) {
                                $newPromotion->roomRatePlans()->attach($roomRateId);
                            }
                        }

                        echo "INSERT PROMOTION OTA " . $promoInfo->proh_id . " SUCCESS<br>";
                    } else {
                        $newPromotion = $this->promoTa->where('proh_promo_id_ota', '=', $promoInfo->proh_id)->first();
                        $listIdRoomRate = isset($dataRoomRate[$promoInfo->proh_id]) ? $dataRoomRate[$promoInfo->proh_id] : [];

                        if (!empty($listIdRoomRate)) {
                            $roomRateId = [];
                            foreach ($listIdRoomRate as $infoRoomRate) {
                                $data = $this->roomRatePromo->where('rapr_promotion_id', '=', $newPromotion->proh_id)
                                                            ->where('rapr_room_rate_id', '=', $infoRoomRate)
                                                            ->get()->first();

                                if (is_null($data)) {
                                    $roomRateId[$infoRoomRate] = $infoRoomRate;
                                }
                            }

                            if (count($roomRateId) > 0) {
                                $newPromotion->roomRatePlans()->attach($roomRateId);
                                echo "UPDATE PROMOTION OTA " . $promoInfo->proh_id . " SUCCESS<br>";
                            }
                        }
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON PROMOTION OTA SUCCESS';
        }
    }


    public function convertPromotionTa ()
    {
        die;
        $page      = (int)Input::get('page', 0);
        $offset    = 100;
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('convert-promotion-ta');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $dataPromo = $this->promoTa->select(['proh_id'])
                                   ->where('proh_promo_id_ota' , '=', 0)
                                   ->orderBy('proh_id', 'DESC')
                                   ->take($offset)
                                   ->skip($skip)
                                   ->get();

        $listIdPromo = $dataPromo->keyBy('proh_id')->keys()->toArray();

        if (is_object($dataPromo) && !$dataPromo->isEmpty()) {
            $dataRoomRate          = $this->getRoomRateOfPromoOne($listIdPromo, Promotion::TYPE_PROMO_TA);
            $dataRoomRateBreakfast = $this->getRoomRateOfPromo($listIdPromo, Promotion::TYPE_PROMO_TA);

            foreach ($dataPromo as $info) {
                if ($info->proh_promotion_info == "") {
                    $dataPromoUpdate                      = $this->promoTa->find($info->proh_id);
                    $dataPromoUpdate->proh_promotion_info = [];
                    $dataPromoUpdate->save();
                }

                $listIdRoomRate          = isset($dataRoomRate[$info->proh_id]) ? $dataRoomRate[$info->proh_id] : [];
                $listIdRoomRatebreakfast = isset($dataRoomRateBreakfast[$info->proh_id]) ? $dataRoomRateBreakfast[$info->proh_id] : [];

                if (!empty($listIdRoomRate)) {
                    $roomRateId = [];
                    foreach ($listIdRoomRate as $rID => $infoRoomRate) {
                        $data = $this->roomRatePromo->where('rapr_promotion_id', '=', $info->proh_id)
                                                    ->where('rapr_room_rate_id', '=', $rID)
                                                    ->get()->first();

                        if (is_null($data)) {
                            $roomRateId[] = [
                                'rapr_promotion_id'     => $info->proh_id,
                                'rapr_room_rate_id'     => $rID,
                                'rapr_price_promo_info' => $infoRoomRate
                            ];
                        }
                    }

                    if (count($roomRateId) > 0) {
                        $info->roomRatePlans()->attach($roomRateId);
                        echo "UPDATE PROMOTION TA " . $info->proh_id . " SUCCESS<br>";
                    }
                }

                if (!empty($listIdRoomRatebreakfast)) {
                    $roomRateId = [];
                    foreach ($listIdRoomRatebreakfast as $rID => $infoRoomRate) {
                        $data = $this->roomRatePromo->where('rapr_promotion_id', '=', $info->proh_id)
                                                    ->where('rapr_room_rate_id', '=', $rID)
                                                    ->get()->first();

                        if (is_null($data)) {
                            $roomRateId[] = [
                                'rapr_promotion_id'     => $info->proh_id,
                                'rapr_room_rate_id'     => $rID,
                                'rapr_price_promo_info' => $infoRoomRate
                            ];
                        }
                    }

                    if (count($roomRateId) > 0) {
                        $info->roomRatePlans()->attach($roomRateId);
                        echo "UPDATE PROMOTION TA BREAKFAST" . $info->proh_id . " SUCCESS<br>";
                    }
                }
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON PROMOTION TA SUCCESS';
        }
    }

    public function getRoomRateOfPromo (array $listPromoId, $promo_type)
    {
        if ($promo_type == Promotion::TYPE_PROMO_OTA) {
            $rate_title = 'OTA_Room_Breakfast';
            $dataRoom   = $this->promotionRooms->getRoomIdOfPromo($listPromoId);
            $listRoomId = $dataRoom->keyBy('pror_room_id')->keys()->toArray();

            if (is_object($dataRoom) && !$dataRoom->isEmpty()) {
                foreach ($dataRoom as $info) {
                    $dataPromo[$info->pror_room_id][] = $info->pror_promotion_id;
                }
            }
        } else {
            $rate_title = 'TA_Room_Breakfast';
            $dataRoom   = $this->promotionRoomsTa->getRoomIdOfPromo($listPromoId);
            $listRoomId = $dataRoom->keyBy('pror_room_id')->keys()->toArray();

            if (is_object($dataRoom) && !$dataRoom->isEmpty()) {
                foreach ($dataRoom as $info) {
                    $promotion_info = json_decode($info->pror_promotion_info, true);
                    $data_promo_price = [];
                    if (count($promotion_info) > 0) {
                        foreach ($promotion_info as $key => $infoPrice) {
                            if (isset($infoPrice['price_contract'])) {
                                $data_promo_price['price_contract'][$key] = $infoPrice['price_contract'];
                            }
                            if (isset($infoPrice['price_discount'])) {
                                $data_promo_price['price_discount'][$key] = $infoPrice['price_discount'];
                            }
                        }
                    }
                    $dataPromo[$info->pror_promotion_id][$info->pror_room_id][] = json_encode($data_promo_price);
                }
            }
        }

        $data_return = [];

        $dataRoomRate = $this->roomRate->select(['rrp_id', 'rrp_room_id'])
                                       ->join('rate_plans', 'rap_id', '=', 'rrp_rate_plan_id')
                                       ->whereIn('rrp_room_id', $listRoomId)
                                       ->where('rap_title', '=', $rate_title)
                                       ->get();

        if (is_object($dataRoomRate) && !$dataRoomRate->isEmpty()) {
            foreach ($dataRoomRate as $info) {
                foreach ($dataPromo as $prID => $infoPromo) {
                    if (isset($infoPromo[$info->rrp_room_id])) {
                        foreach ($infoPromo[$info->rrp_room_id] as $promo)  {
                            $data_return[$prID][$info->rrp_id] = $promo;
                        }
                    }
                }
            }
        }

        return $data_return;
    }

    public function getRoomRateOfPromoOne (array $listPromoId, $promo_type)
    {
        if ($promo_type == Promotion::TYPE_PROMO_OTA) {
            $rate_title = 'OTA_Room_Only';
            $dataRoom   = $this->promotionRooms->getRoomIdOfPromo($listPromoId);
            $listRoomId = $dataRoom->keyBy('pror_room_id')->keys()->toArray();

            if (is_object($dataRoom) && !$dataRoom->isEmpty()) {
                foreach ($dataRoom as $info) {
                    $dataPromo[$info->pror_room_id][] = $info->pror_promotion_id;
                }
            }
        } else {
            $rate_title = 'TA_Room_Only';
            $dataRoom   = $this->promotionRoomsTa->getRoomIdOfPromo($listPromoId);
            $listRoomId = $dataRoom->keyBy('pror_room_id')->keys()->toArray();

            if (is_object($dataRoom) && !$dataRoom->isEmpty()) {
                foreach ($dataRoom as $info) {
                    $promotion_info = json_decode($info->pror_promotion_info, true);
                    $data_promo_price = [];
                    if (count($promotion_info) > 0) {
                        foreach ($promotion_info as $key => $infoPrice) {
                            if (isset($infoPrice['price_contract'])) {
                                $data_promo_price['price_contract'][$key] = $infoPrice['price_contract'];
                            }
                            if (isset($infoPrice['price_discount'])) {
                                $data_promo_price['price_discount'][$key] = $infoPrice['price_discount'];
                            }
                        }
                    }
                    $dataPromo[$info->pror_promotion_id][$info->pror_room_id][] = json_encode($data_promo_price);
                }
            }
        }

        $data_return = [];

        $dataRoomRate = $this->roomRate->select(['rrp_id', 'rrp_room_id'])
                                       ->join('rate_plans', 'rap_id', '=', 'rrp_rate_plan_id')
                                       ->whereIn('rrp_room_id', $listRoomId)
                                       ->where('rap_title', '=', $rate_title)
                                       ->get();

        if (is_object($dataRoomRate) && !$dataRoomRate->isEmpty()) {
            foreach ($dataRoomRate as $info) {
                foreach ($dataPromo as $prID => $infoPromo) {
                    if (isset($infoPromo[$info->rrp_room_id])) {
                        foreach ($infoPromo[$info->rrp_room_id] as $promo)  {
                            $data_return[$prID][$info->rrp_id] = $promo;
                        }
                    }
                }
            }
        }

        return $data_return;
    }

    public function deletePromotionOta ()
    {
        die;
        return $this->promoTa->where('proh_promo_id_ota', '>', NO_ACTIVE)
                             ->where('proh_promo_type', '=', Promotion::TYPE_PROMO_OTA)
                             ->delete();
    }

    public function deleteRoomRatePromotion ()
    {
        die;
        return $this->roomRatePromo->where('rapr_id', '>', NO_ACTIVE)
                                   ->delete();
    }

    public function convertPromoLastMinutes ()
    {
        die;
        $page      = (int)Input::get('page', 0);
        $offset    = 100;
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('convert_promotion_last_minutes');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $dataPromo = $this->promoTa->select(['proh_id', 'proh_time_start', 'proh_time_finish', 'proh_time_book_start', 'proh_time_book_finish'])
                                   ->where('proh_promo_id_ota' , '>', 0)
                                   ->where('proh_type', '=', Promotion::TYPE_PROMO_LASTMINUTE)
                                   ->orderBy('proh_id', 'DESC')
                                   ->take($offset)
                                   ->skip($skip)
                                   ->get();

        $listIdPromo = $dataPromo->keyBy('proh_id')->keys()->toArray();

        if (is_object($dataPromo) && !$dataPromo->isEmpty()) {
            foreach ($dataPromo as $info) {
                $dataUpdate = [
                    'proh_time_start'       => $info->proh_time_book_start,
                    'proh_time_finish'      => $info->proh_time_book_finish,
                    'proh_time_book_start'  => $info->proh_time_start,
                    'proh_time_book_finish' => $info->proh_time_finish,
                ];

                $this->promoTa->where('proh_id', '=', $info->proh_id)
                              ->limit(1)
                              ->update($dataUpdate);

                echo "UPDATE PROMOTION LASTMINUTES SUCCESS " . $info->proh_id . '<br>';
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON PROMOTION TA SUCCESS';
        }
    }

    public function updatePromotionTa ()
    {
        die;
        $page      = (int)Input::get('page', 0);
        $offset    = 200;
        $skip      = $page * $offset;
        $time_life = 3;
        $url       = route('update-promotion-ta');
        $page_next = $page + 1;
        $message   = '<h1>Running...</h1>';

        $dataPromo = $this->promoTa->select(['proh_id', 'proh_active', 'proh_status'])
                                   ->where('proh_promo_id_ota' , '=', 0)
                                   ->orderBy('proh_id', 'DESC')
                                   ->take($offset)
                                   ->skip($skip)
                                   ->get();

        $listIdPromo = $dataPromo->keyBy('proh_id')->keys()->toArray();

        if (is_object($dataPromo) && !$dataPromo->isEmpty()) {

            foreach ($dataPromo as $info) {
                $dataPromo = $this->promoTa->find($info->proh_id);
                $active = 0;
                if ($info->proh_active && $info->proh_status) {
                    $active = 1;
                }
                $dataPromo->proh_active = $active;
                $dataPromo->save();
                echo "UPDATE PROMOTION TA SUCCESS " . $info->proh_id . '<br>';
            }

            return View('layouts.crons', compact('message', 'time_life', 'url', 'page_next'));
        } else {
            echo 'CRON PROMOTION TA SUCCESS';
        }

    }

}
