<?php namespace App\Http\Controllers\HotelPromotion;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Components\Rooms;
use App\Models\Components\RoomPrice;
use App\Models\Components\RatePlan;
use App\Models\Components\Promotion;
use App\Models\Components\RoomsRatePlans;
use App\Models\Components\RoomRatePromotion;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Components\Hotel;
use Illuminate\Http\Request;
use App\Http\Requests\PromotionRequest;
use Input, View, Redirect, Validator, Auth, Session, Log, Response, DB;
use App\Mytour\Classes\AdminLog;

class HotelPromotionController extends Controller {

	private $prefixPromo = 'custom_';

	private $dataTypeDiscount = [
		Promotion::TYPE_DISCOUNT_PERCENT    => 'Giảm trừ theo %',
		Promotion::TYPE_DISCOUNT_MONEY      => 'Giảm trừ theo số tiền',
		Promotion::TYPE_DISCOUNT_FREE_NIGHT => 'Free night'
	];

	const ACTION_ADD  = 0;
	const ACTION_EDIT = 1;

	public function __construct(Rooms $rooms
        , RatePlan $ratePlan
        , Promotion $promotion
        , Hotel $hotel
        , RoomPrice $roomPrice
        , RoomsRatePlans $roomRatePlan
        , RoomRatePromotion $roomRatePromo
        , AdminLog $adminLog
        )
	{
		$this->rooms         = $rooms;
		$this->hotel         = $hotel;
		$this->ratePlan      = $ratePlan;
		$this->roomPrice     = $roomPrice;
		$this->promotion     = $promotion;
		$this->roomRatePlan  = $roomRatePlan;
		$this->roomRatePromo = $roomRatePromo;
		$this->adminLog 	 = $adminLog;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($hotelID)
	{
		$dataPromo = $this->promotion->where('proh_hotel', '=', $hotelID)
									 ->where('proh_delete', '=', NO_ACTIVE)
									 ->orderBy('proh_create_time', 'DESC')
									 ->paginate(NUM_PER_PAGE);

		$hotelPms = $this->checkTypeHotelPms($hotelID);

		return View('components.modules.hotel_promotion.index', compact('dataPromo', 'hotelID', 'hotelPms'));
	}

	public function create_step_1 ($hotelID)
	{
		return View('components.modules.hotel_promotion.create_step_1', compact('hotelID'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create_step_2($hotelID, $typePromo)
	{
		$params           = ['rom_id', 'rom_name', 'rap_id', 'rap_name', 'active' => ACTIVE];
		$dataRoomByHotel  = $this->rooms->getRoomInfoByHotelId($hotelID, $params);
		$dataTypeDiscount = $this->dataTypeDiscount;

		return View('components.modules.hotel_promotion.create_step_2', compact('dataRoomByHotel', 'typePromo', 'hotelID', 'dataTypeDiscount'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store_step_1()
	{
		$hotelID    = Input::get('hotel_id');
		$promo_type = Input::get('promo_type');

		return Redirect::route('hotel-promo-create-step-2', [$hotelID, $promo_type]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store_step_2(Request $request)
	{
		$proh_promo_type = $request->get('proh_promo_type');

		//Get thong tin Form Request
		$dataPromoForm = $this->getDataFormExecute($request, self::ACTION_ADD);

		//Check KS PMS thi ko cap nhat KM OTA
		if ($proh_promo_type == Promotion::TYPE_PROMO_OTA
			&& $this->checkTypeHotelPms($dataPromoForm['proh_hotel'])) {
			return Redirect::back();
		}

		$iDRoomApply  = $request->get($this->prefixPromo . 'room_id');
		$params       = ['room_ids' => $iDRoomApply, 'field_select' => ['rom_id', 'rom_hotel']];
		$dataRoomApply = $this->rooms->getRoomInfoByRoomId($params);

		$idRoomApplyOfRate = [];

		if (is_object($dataRoomApply) && !$dataRoomApply->isEmpty()) {
			$dataHotel = $this->hotel->getInfoHotelById(['hotel_id' => $dataPromoForm['proh_hotel'], 'field_select' => ['hot_mark_up']]);
			$markup_ta = (int) $dataHotel->hot_mark_up;

			foreach ($dataRoomApply as $infoRoom) {
				foreach ($infoRoom->ratePlans as $infoRate) {
					if ($proh_promo_type == Promotion::TYPE_PROMO_TA
						&& $infoRate->rap_type_price == RatePlan::TYPE_PRICE_TA
						&& $infoRate->rap_parent_id == NO_ACTIVE) {
						$info_price_contract_ta = $request->get($this->prefixPromo . 'price_contract_ta_promo_' . $infoRoom->rom_id . $infoRate->rap_id);
						if (!empty($info_price_contract_ta)) {
							foreach ($info_price_contract_ta as $dayWeek => $info) {
								$price_contract_ta_promo = convert_format_price($info);
								$day_discount[$infoRoom->rom_id][$infoRate->rap_id]['price_contract'][$dayWeek] = $price_contract_ta_promo;
								$day_discount[$infoRoom->rom_id][$infoRate->rap_id]['price_discount'][$dayWeek] = $price_contract_ta_promo * (($markup_ta / 100) + 1);
							}
						}
					}
					$rate_id_check = $request->get($this->prefixPromo . 'rate_plan_id' . $infoRoom->rom_id . $infoRate->rap_id);
					if ($rate_id_check > 0) {
						//Get Array ID Rate Plan Follow Room ID Apply OF Promo
						$idRoomApplyOfRate[$infoRate->rap_id][] = $infoRoom->rom_id;
					}
				}
			}
		}

		//Create Promotion New
		DB::enableQueryLog();

		//Create Promotion New
		$newPromotion = $this->promotion->create($dataPromoForm);

		//Insert du lieu vao bang room_rate_promo
		if (is_object($newPromotion) && $newPromotion->proh_id > 0) {
			foreach ($idRoomApplyOfRate as $rateID => $infoRoom) {
				//Get Rate plan Children
				$dataRateChild = $this->ratePlan->where('rap_parent_id', '=', $rateID)
												->first();

				foreach ($infoRoom as $roomID) {
					try {
						$discount = [];
						if ($proh_promo_type == Promotion::TYPE_PROMO_TA) {
							if (isset($day_discount[$roomID][$rateID])
								&& !empty($day_discount[$roomID][$rateID])) {
								$discount = $day_discount[$roomID][$rateID];
							}
						}

						$discount = json_encode($discount);
						$roomRatePlan = $this->roomRatePlan->where('rrp_room_id', '=', $roomID)
														   ->where('rrp_rate_plan_id', '=', $rateID)
														   ->firstOrFail();

					   	$dataRoomRatePromo = [
							'rapr_price_promo_info' => $discount
					   	];

						$newPromotion->roomRatePlans()->attach($roomRatePlan->rrp_id, $dataRoomRatePromo);

						if ($dataRateChild != null) {
							$roomRatePlanChild = $this->roomRatePlan->where('rrp_room_id', '=', $roomID)
															   ->where('rrp_rate_plan_id', '=', $dataRateChild->rap_id)
															   ->firstOrFail();

						   	$dataRoomRatePromoChild = [
								'rapr_price_promo_info' => $discount
						   	];

						   	$newPromotion->roomRatePlans()->attach($roomRatePlanChild->rrp_id, $dataRoomRatePromoChild);
						}

					} catch (ModelNotFoundException $e) {
						Log::error($e->getMessage());
					}
				}
			}
		}
		//save log create Promotion
		$uri                    = $request->path();
		$adm_id                 = $request->user()->id;
		$ip                     = $request->ip();
		$params_insert = array(
							   'id' 	   => $newPromotion->proh_id,
							   'ip'		   => $ip,
							   'type'	   => 1,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

		$this->saveLogPromo($params_insert);

        //Alert thong tin cap nhat thanh cong
        $_msg_alert = SUCCESS_ALERT;
        $dataMsg = View('layouts.includes.success-alert', compact('_msg_alert'))->render();

        Session::flash('_msg_alert', $dataMsg);

		return Redirect::route('hotel-promo-list', [$newPromotion->proh_hotel]);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function editPromotion($id)
	{
		try {
			$infoPromo = $this->promotion->findOrFail($id);

			$table_price = "room_price_" . date('Ym', $infoPromo->proh_time_start);
			$params      = ['rom_id', 'rom_name', 'rap_id', 'rap_name', 'active' => ACTIVE];

			$dataRoomByHotel = $this->rooms->getRoomInfoByHotelId($infoPromo->proh_hotel, $params)->load('ratePlans');

			//Get data Room Of Promo
			$listRoomID      = [];
			$listRateID      = [];
			$listRateParent  = [];
			$dataRoomOfPromo = $infoPromo->roomRatePlans;

			if (!$dataRoomOfPromo->isEmpty()) {
				foreach ($dataRoomOfPromo as $infoRoomRate) {
					$listRoomID[] = $infoRoomRate->rrp_room_id;
					$listRateID[] = $infoRoomRate->rrp_rate_plan_id;
					$listRoomRate[$infoRoomRate->rrp_room_id][] = $infoRoomRate->rrp_rate_plan_id;
				}
			}

			// dd($listRateID);
			if ($infoPromo->proh_promo_type == Promotion::TYPE_PROMO_TA) {
				$dataRoomOfPromo = $infoPromo->roomRates;
				if (is_object($dataRoomOfPromo) && !$dataRoomOfPromo->isEmpty()) {
					foreach ($dataRoomOfPromo as $infoPrice) {
						$price_promo_info = json_decode($infoPrice->rapr_price_promo_info, true);
						if (!empty($price_promo_info)) {
							$dataPriceTa[$infoPrice->roomRates->rrp_room_id][$infoPrice->roomRates->rrp_rate_plan_id] = isset($price_promo_info['price_contract']) ? $price_promo_info['price_contract'] : 0;
						}
					}
				}
			}

		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return Response::make('Promotion not found!', 404);
		}

		$dataTypeDiscount = $this->dataTypeDiscount;

		$prefix = "";
		switch ($infoPromo->proh_type) {
			case Promotion::TYPE_PROMO_EARLY:
				$prefix = "early";
				break;

			case Promotion::TYPE_PROMO_LASTMINUTE:
				$prefix = "last";
				break;

			case Promotion::TYPE_PROMO_CUSTOM:
				$prefix = "custom";
				break;
		}

		return View('components.modules.hotel_promotion.edit-promo', compact('infoPromo', 'dataRoomByHotel', 'dataPriceTa', 'listRoomID', 'listRateID', 'listRoomRate', 'dataTypeDiscount', 'prefix'));

	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function updatePromotion(Request $request, $promoID)
	{
		$dataPromo       = $this->promotion->find($promoID);
		$proh_promo_type = $dataPromo->proh_promo_type;
		$dataPromoUpdate = $this->getDataFormExecute($request, self::ACTION_EDIT);

		//Check KS PMS thi ko cap nhat KM OTA
		// if ($proh_promo_type == Promotion::TYPE_PROMO_OTA
		// 	&& $this->checkTypeHotelPms($dataPromo->proh_hotel)) {
		// 	return Redirect::back();
		// }

		//Insert đơn giá mới theo khoang thoi gian nhan
		$arr_room_id  = $request->get('room_id');
		$params       = ['room_ids' => $arr_room_id, 'field_select' => ['rom_id', 'rom_hotel']];
		$dataRoomInfo = $this->rooms->getRoomInfoByRoomId($params);

		//Get Array Rate ID Old Apply Of Promo
		$arr_rate_old_promo = [];
		if (is_object($dataPromo->roomRatePlans)
			&& !$dataPromo->roomRatePlans->isEmpty()) {
			foreach ($dataPromo->roomRatePlans as $infoRoomRate) {
				$arr_rate_old_promo[$infoRoomRate->rrp_room_id][] = $infoRoomRate->rrp_rate_plan_id;
			}
		}

		//Get Array Rate ID New Apply Of Promo
		$arr_rate_new_promo = [];
		if (is_object($dataRoomInfo) && !$dataRoomInfo->isEmpty()) {
			$dataHotel = $this->hotel->getInfoHotelById(['hotel_id' => $dataPromo->proh_hotel, 'field_select' => ['hot_mark_up']]);
			$markup_ta = (int) $dataHotel->hot_mark_up;

			foreach ($dataRoomInfo as $infoRoom) {
				foreach ($infoRoom->ratePlans as $infoRate) {
					if ($proh_promo_type == Promotion::TYPE_PROMO_TA
						&& $infoRate->rap_type_price == RatePlan::TYPE_PRICE_TA
						&& $infoRate->rap_parent_id == NO_ACTIVE) {
						$info_price_contract_ta = $request->get($this->prefixPromo . 'price_contract_ta_promo_' . $infoRoom->rom_id . $infoRate->rap_id);
						if (!empty($info_price_contract_ta)) {
							foreach ($info_price_contract_ta as $dayWeek => $info) {
								$price_contract_ta_promo = convert_format_price($info);
								$day_discount[$infoRoom->rom_id][$infoRate->rap_id]['price_contract'][$dayWeek] = $price_contract_ta_promo;
								$day_discount[$infoRoom->rom_id][$infoRate->rap_id]['price_discount'][$dayWeek] = $price_contract_ta_promo * (($markup_ta / 100) + 1);
							}
						}
					}
					$rate_id_check = $request->get($this->prefixPromo . 'rate_plan_id' . $infoRoom->rom_id . $infoRate->rap_id);
					if ($rate_id_check > 0) {
						$arr_rate_new_promo[$infoRoom->rom_id][] = $infoRate->rap_id;
					}
				}
			}
		}

		$arr_rate_del_promo    = [];
		$arr_rate_update_promo = [];
		if (!empty($arr_rate_old_promo)) {
			foreach ($arr_rate_old_promo as $roomID => $rateInfo) {
				foreach ($rateInfo as $rateID) {
					if (isset($arr_rate_new_promo[$roomID])) {
						if (in_array($rateID, $arr_rate_new_promo[$roomID])) {
							$arr_rate_new_promo[$roomID]      = array_diff($arr_rate_new_promo[$roomID], [$rateID]);
							$arr_rate_update_promo[$roomID][] = $rateID;
						} else {
							$arr_rate_del_promo[$roomID][] = $rateID;
						}
					} else {
						$arr_rate_del_promo[$roomID][] = $rateID;
					}
				}
			}
		}

		//Update Promotion
		$dataPromo->fill($dataPromoUpdate)->save();

		//save log create Promotion
		$uri                    = $request->path();
		$adm_id                 = $request->user()->id;
		$ip                     = $request->ip();
		$params_update = array(
							   'id' 	   => $promoID,
							   'ip'		   => $ip,
							   'type'	   => 2,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

		$this->saveLogPromo($params_update);

		if (is_object($dataPromo) && $dataPromo->proh_id > 0) {
			//delete rate plan of promo
			if (!empty($arr_rate_del_promo)) {
				foreach ($arr_rate_del_promo as $roomID => $infoRate) {
					foreach ($infoRate as $rateID) {
					//Get Rate plan Children
					$dataRateChild = $this->ratePlan->where('rap_parent_id', '=', $rateID)
													->first();
						try {
							$roomRatePlan = $this->roomRatePlan->where('rrp_room_id', '=', $roomID)
															   ->where('rrp_rate_plan_id', '=', $rateID)
															   ->firstOrFail();

							$dataPromo->roomRatePlans()->detach($roomRatePlan->rrp_id);

							if ($dataRateChild != null) {
								$roomRatePlanChild = $this->roomRatePlan->where('rrp_room_id', '=', $roomID)
															   ->where('rrp_rate_plan_id', '=', $dataRateChild->rap_id)
															   ->first();

								$dataPromo->roomRatePlans()->detach($roomRatePlanChild->rrp_id);
							}

						} catch (ModelNotFoundException $e) {
							Log::error($e->getMessage());
						}
					}
				}
			}

			//Insert du lieu vao bang room_rate_promo
			if (!empty($arr_rate_new_promo)) {
				foreach ($arr_rate_new_promo as $roomID => $infoRate) {
					foreach ($infoRate as $rateID) {
						//Get Rate plan Children
						$dataRateChild = $this->ratePlan->where('rap_parent_id', '=', $rateID)
														->first();
						try {
							$discount = [];
							if ($proh_promo_type == Promotion::TYPE_PROMO_TA) {
								if (isset($day_discount[$roomID][$rateID])
									&& !empty($day_discount[$roomID][$rateID])) {
									$discount = $day_discount[$roomID][$rateID];
								}
							}

							$discount = json_encode($discount);
							$roomRatePlan = $this->roomRatePlan->where('rrp_room_id', '=', $roomID)
															   ->where('rrp_rate_plan_id', '=', $rateID)
															   ->firstOrFail();

						   	$dataRoomRatePromo = [
								'rapr_price_promo_info' => $discount
						   	];

							$dataPromo->roomRatePlans()->attach($roomRatePlan->rrp_id, $dataRoomRatePromo);

							if ($dataRateChild != null) {
								$roomRatePlanChild = $this->roomRatePlan->where('rrp_room_id', '=', $roomID)
																   ->where('rrp_rate_plan_id', '=', $dataRateChild->rap_id)
																   ->firstOrFail();

							   	$dataRoomRatePromoChild = [
									'rapr_price_promo_info' => $discount
							   	];

								$dataPromo->roomRatePlans()->attach($roomRatePlanChild->rrp_id, $dataRoomRatePromoChild);
							}

						} catch (ModelNotFoundException $e) {
							Log::error($e->getMessage());
						}
					}
				}
			}

			//Update Price Of Promo
			if (!empty($arr_rate_update_promo)) {
				foreach ($arr_rate_update_promo as $roomID => $infoRate) {
					foreach ($infoRate as $rateID) {
						//Get Rate plan Children
						$dataRateChild = $this->ratePlan->where('rap_parent_id', '=', $rateID)
														->first();
						try {
							$discount = [];
							if ($proh_promo_type == Promotion::TYPE_PROMO_TA) {
								if (isset($day_discount[$roomID][$rateID])
									&& !empty($day_discount[$roomID][$rateID])) {
									$discount = $day_discount[$roomID][$rateID];
								}
							}

							$discount = json_encode($discount);
							$roomRatePlan = $this->roomRatePlan->where('rrp_room_id', '=', $roomID)
															   ->where('rrp_rate_plan_id', '=', $rateID)
															   ->firstOrFail();

						   	$dataRoomRatePromo = [
								'rapr_price_promo_info' => $discount
						   	];

							$this->roomRatePromo->where('rapr_promotion_id', '=', $dataPromo->proh_id)
												->where('rapr_room_rate_id', '=', $roomRatePlan->rrp_id)
												->update($dataRoomRatePromo);

							if ($dataRateChild != null) {
								$roomRatePlanChild = $this->roomRatePlan->where('rrp_room_id', '=', $roomID)
															   ->where('rrp_rate_plan_id', '=', $dataRateChild->rap_id)
															   ->first();

							   	$dataRoomRatePromoChild = [
									'rapr_price_promo_info' => $discount
							   	];

								$this->roomRatePromo->where('rapr_promotion_id', '=', $dataPromo->proh_id)
													->where('rapr_room_rate_id', '=', $roomRatePlanChild->rrp_id)
													->update($dataRoomRatePromoChild);
							}

						} catch (ModelNotFoundException $e) {
							Log::error($e->getMessage());
						}
					}
				}
			}
		}

        //Alert thong tin cap nhat thanh cong
		$_msg_alert = SUCCESS_ALERT;
		$dataMsg    = View('layouts.includes.success-alert', compact('_msg_alert'))->render();

        Session::flash('_msg_alert', $dataMsg);

		return Redirect::route('hotel-promo-list', [$dataPromo->proh_hotel]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request)
	{
		$dataPromo                   = $this->promotion->find($request->get('id'));
		$dataPromo->proh_delete      = ACTIVE;
		$dataPromo->proh_delete_user = Auth::id();
		$dataPromo->proh_delete_time = time();
		$dataPromo->save();

		return $dataPromo->proh_id;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function active(Request $request)
	{
		$value = abs($request->get('val') - 1);
		$dataPromo = $this->promotion->find($request->get('id'));
		//Check KS PMS thi ko cap nhat KM OTA
		if ($dataPromo->proh_promo_type == Promotion::TYPE_PROMO_OTA
			&& $this->checkTypeHotelPms($dataPromo->proh_hotel)) {
			return 0;
		}

		$dataPromo->proh_active = $value;
		$dataPromo->save();

		return $dataPromo->proh_id;
	}

	public function getInfoPriceRateNew ($hotelID, $params, $day_discount, $timeInt, $time_finish, $type_discount)
	{
		$dataPriceInsert = [];
		$dataPrice = $this->roomPrice->getPriceByRoomRateId($params);
		$dataHotel = $this->hotel->getInfoHotelById(['hotel_id' => $hotelID, 'field_select' => ['hot_commission_ota', 'hot_mark_up']]);

		$markup_ta      = (int) $dataHotel->hot_mark_up;
		$commission_ota = (int) $dataHotel->hot_commission_ota;

		if (is_object($dataPrice) && !$dataPrice->isEmpty()) {
			//Get du lieu bang time checkin
			$time_start_first = strtotime(date('Ym01', $timeInt));
			$time_start_last  = strtotime(date('Ymt', $timeInt));

			foreach ($dataPrice as $priceInfo) {
				$dataPriceInsert = [
					'rop_hotel_id'      => $hotelID,
					'rop_room_id'       => $priceInfo->rop_room_id,
					'rop_type_price'    => $priceInfo->rop_type_price,
					'rop_person_type'   => $priceInfo->rop_person_type,
				];

				//gan gia tri mac dinh
				for ($j = 1; $j <= 31; $j++) {
					$field_col = 'rop_col' . $j;
					$dataPriceInsert[$field_col] = 0;
				}

				for ($i = $time_start_first; $i <= $time_start_last; $i += 86400) {
					if ($i <= $time_finish) {
						$field_col = 'rop_col' . date('j', $i);
						$dayOfWeek = date('N', $i);

						if ($priceInfo->rop_type_price == RatePlan::TYPE_PRICE_OTA) {
							$price_ota = (double) $priceInfo->$field_col;

							switch ($type_discount) {
								case Promotion::TYPE_DISCOUNT_PERCENT:
									if ($day_discount[$dayOfWeek] > 0) {
										$price_ota = $price_ota - ($price_ota * $day_discount[$dayOfWeek] / 100);
									}
									break;

								case Promotion::TYPE_DISCOUNT_MONEY:
									if ($day_discount[$dayOfWeek] > 0) {
										$price_discount_ota = doubleval(str_replace(",", "", $day_discount[$dayOfWeek]));
										$price_ota = $price_ota - $price_discount_ota;
									}
									break;

								case Promotion::TYPE_DISCOUNT_FREE_NIGHT:
									$num_room_apply = $day_discount['proh_free_night_num'] - $day_discount['proh_free_night_discount'];
									$price_ota      = (double) ($price_ota * $num_room_apply) / $day_discount['proh_free_night_num'];
									break;
							}

							$dataPriceInsert['rop_info_price_contract'][$i] = $price_ota - ($price_ota * $commission_ota) / 100;
							$dataPriceInsert['rop_info_price_publish'][$i]  = $price_ota * RoomPrice::RATE_OTA_PRICE_PUBLISH;
							$dataPriceInsert[$field_col]                    = $price_ota;
						} elseif ($priceInfo->rop_type_price == RatePlan::TYPE_PRICE_TA) {
							$price_ta_in = 0;
							if (isset($day_discount[$params['room_id']][$params['rate_plan_id']][$dayOfWeek])) {
								$price_ta_in_discount = $day_discount[$params['room_id']][$params['rate_plan_id']][$dayOfWeek];
								$price_ta_in_discount = doubleval(str_replace(",", "", $price_ta_in_discount));
								if ($price_ta_in_discount >= 0) {
									$price_ta_in = $price_ta_in_discount;
								}
							}

							$price_ta = $price_ta_in * (($markup_ta / 100) + 1);
							$dataPriceInsert['rop_info_price_contract'][$i] = $price_ta_in;
							$dataPriceInsert['rop_info_price_publish'][$i]  = $price_ta * RoomPrice::RATE_TA_PRICE_PUBLISH;
							$dataPriceInsert[$field_col]                    = $price_ta;
						}
					}
	            }
			}
		}

		return $dataPriceInsert;
	}

	/**
	 * [createRateRoomPrice description]
	 * Create Rate Plan By Array Info
	 * @param  array  $dataInsert [description]
	 * @param  array  $params     [description]
	 * @return [type]             [description]
	 */
	private function createRateRoomPrice(array $dataInsert, array $params)
	{
		$data_return = [];
		if(!empty($dataInsert)) {
			foreach ($dataInsert as $rateID => $dataRate) {
				$idRateNew = $this->ratePlan->insertRatePlanReturnId($dataRate);
				if ($idRateNew > 0) {
					$data_return[$rateID] = $idRateNew;
					if (isset($params[$rateID]) && !empty($params[$rateID])) {
						foreach ($params[$rateID] as $roomID => $info) {
							$this->createRoomPriceTimeRange($idRateNew, $info);
						}
					}
				}
			}
		}

		return $data_return;
	}

	/**
	 * [createRoomPriceTimeRange description]
	 * Create price of Rate Plan ID by time range
	 * @param  [int] $idRateNew [description]
	 * @param  [array] $params    [description]
	 * @return [type]            [description]
	 */
	private function createRoomPriceTimeRange($idRateNew, array $params)
	{
		$time_start         = array_get($params, 'time_start', "");
		$time_finish        = array_get($params, 'time_finish', "");
		$hotelID            = array_get($params, 'hotel_id', "");
		$roomID             = array_get($params, 'room_id', "");
		$rateID             = array_get($params, 'rate_id', "");
		$day_discount       = array_get($params, 'discount', "");
		$proh_discount_type = array_get($params, 'proh_discount_type', "");

		if ($time_start > 0 && $time_finish > 0) {
			for ($i = strtime_firstday_month($time_start); $i <= $time_finish; $i = strtime_next_month($i)) {
				$table_price = 'room_price_' . date('Ym', $i);
				$params      = ['table' => $table_price, 'room_id' => $roomID, 'rate_plan_id' => $rateID, 'person_type' => RoomPrice::NUM_PERSON_MAX_TYPE];
				$dataPrice   = $this->getInfoPriceRateNew($hotelID, $params, $day_discount, $i, $time_finish, $proh_discount_type);

				if (!empty($dataPrice)) {
					$dataPrice['rop_rate_plan_id'] = $idRateNew;
					$dataPrice['rop_info_price_publish']  = json_encode($dataPrice['rop_info_price_publish']);
					$dataPrice['rop_info_price_contract'] = json_encode($dataPrice['rop_info_price_contract']);
					$params = ['table' => $table_price, 'data_insert' => $dataPrice];
					$this->roomPrice->insertPriceByData($params);
				}
			}
		}
	}

	/**
	 * [getDataFormExecute description]
	 * Get data Of Form Create Or Update
	 * @param  [type] $request [description]
	 * @return [type]          [description]
	 */
	private function getDataFormExecute($request, $action = 1)
	{
		$rules = [
			'proh_title'         => 'required',
			'room_id'            => 'required',
			'date_range_checkin' => 'required',
			'proh_min_night'     => 'numeric|min:1',
			'proh_max_night'     => 'numeric|min:1',
		];

		$msg_validate = [
			'proh_title.required'         => 'Bạn hãy nhập tiêu đề khuyến mãi!',
			'room_id.required'            => 'Bạn hãy chọn phòng được áp dụng khuyến mãi',
			'date_range_checkin.required' => 'Bạn hãy nhập thời gian nhận phòng!',
			'proh_min_night.min'          => 'Bạn hãy nhập số đêm ở tối thiểu, số đêm tối thiểu là 1!',
			'proh_max_night.min'          => 'Bạn hãy nhập số đêm ở tối đa, số đêm tối đa là 1!',
		];

		$proh_type = $request->get('proh_type');

		//Get prefix Promotion
		$this->getPrefixPromotion($proh_type);

		if ($proh_type == Promotion::TYPE_PROMO_CUSTOM) {
			$rules['date_range_book']                 = 'required';
			$msg_validate['date_range_book.required'] = 'Bạn hãy nhập thời gian đặt phòng!';
		}

		$request->offsetSet('proh_title', $request->get($this->prefixPromo . 'proh_title'));
		$request->offsetSet('room_id', $request->get($this->prefixPromo . 'room_id'));
		$request->offsetSet('date_range_checkin', $request->get($this->prefixPromo . 'date_range_checkin'));
		$request->offsetSet('proh_min_night', $request->get($this->prefixPromo . 'proh_min_night'));
		$request->offsetSet('proh_max_night', $request->get($this->prefixPromo . 'proh_max_night'));

		$this->validate($request, $rules, $msg_validate);

		$date_range_checkin = convert_str_time_range($request->get('date_range_checkin'));
		$proh_time_start    = $date_range_checkin['time_start'];
		$proh_time_finish   = $date_range_checkin['time_finish'];

		$proh_time_book_start  = 0;
		$proh_time_book_finish = 0;
		$proh_min_day_before   = 0;
		$proh_max_day_before   = 0;
		$proh_day_apply        = [];

		$hotelID         = $request->get('hotel_id');
		$proh_promo_type = $request->get('proh_promo_type');
		$proh_title      = $request->get('proh_title');

		switch ($proh_type) {
			case Promotion::TYPE_PROMO_CUSTOM :
				$date_range_book       = convert_str_time_range($request->get('date_range_book'));
				$proh_time_book_start  = $date_range_book['time_start'];
				$proh_time_book_finish = $date_range_book['time_finish'];

				if (!$request->get('day_apply_every')) {
					$proh_day_apply_input = $request->get('proh_day_apply');
					for ($i = 1; $i <= 7; $i++) {
						$proh_day_apply['day_apply'][$i] = 0;
						if (isset($proh_day_apply_input[$i])) {
							$proh_day_apply['day_apply'][$i] = (int)$proh_day_apply_input[$i];
						}
					}
					//Set time dat phong neu co y/c hour dat phong
					$time_start_apply = $request->get('time_start_apply');
					if (!empty($time_start_apply) && !$request->get('day_apply_every')) {
						$proh_day_apply['time_start'] = $time_start_apply;
					}
					$time_finish_apply = $request->get('time_finish_apply');
					if (!empty($time_finish_apply) && !$request->get('day_apply_every')) {
						$proh_day_apply['time_finish'] = $time_finish_apply;
					}

					//Gan gia tri mac dinh
					if (empty($proh_day_apply)) {
						$proh_day_apply = [];
					}
				}
				break;

			case Promotion::TYPE_PROMO_EARLY :
				$proh_min_day_before = $request->get('proh_min_day_before');
				break;

			case Promotion::TYPE_PROMO_LASTMINUTE :
				$proh_max_day_before = $request->get('proh_max_day_before');
				break;
		}

		$proh_day_deny = [];
		foreach ($request->get('proh_day_deny') as $day_deny) {
			if (!empty($day_deny)) {
				$proh_day_deny[] = str_int_time($day_deny);
			}
		}

		$proh_discount_type       = 0;
		$proh_promotion_info      = [];
		$proh_free_night_num      = 0;
		$proh_free_night_discount = 0;

		if ($proh_promo_type == RatePlan::TYPE_PRICE_OTA) {
			$proh_discount_type  = $request->get($this->prefixPromo . 'proh_discount_type');
			switch ($proh_discount_type) {
				case Promotion::TYPE_DISCOUNT_PERCENT :
					$proh_promotion_info = $request->get($this->prefixPromo . 'price_contract_ota_promo');
					break;

				case Promotion::TYPE_DISCOUNT_MONEY :
					$price_promo_info = $request->get($this->prefixPromo . 'price_contract_ota_promo');

					if (!empty($price_promo_info)) {
						foreach ($price_promo_info as $key => $price) {
							$proh_promotion_info[$key] = convert_format_price($price);
						}
					}
					break;

				case Promotion::TYPE_DISCOUNT_FREE_NIGHT :
					$proh_free_night_num      = $request->get($this->prefixPromo . 'proh_free_night_num');
					$proh_free_night_discount = $request->get($this->prefixPromo . 'proh_free_night_discount');
			}
		}

		$data_return = [
			'proh_title'                => $proh_title,
			'proh_hotel'                => $hotelID,
			'proh_time_start'           => $proh_time_start,
			'proh_time_finish'          => $proh_time_finish,
			'proh_time_book_start'      => $proh_time_book_start,
			'proh_time_book_finish'     => $proh_time_book_finish,
			'proh_min_night'            => $request->get('proh_min_night'),
			'proh_max_night'            => $request->get('proh_max_night'),
			'proh_type'                 => $request->get('proh_type'),
			'proh_day_deny'             => $proh_day_deny,
			'proh_day_apply'            => $proh_day_apply,
			'proh_cancel_policy'        => $request->get('proh_cancel_policy'),
			'proh_discount_type'        => $proh_discount_type,
			'proh_promo_type'           => $proh_promo_type,
			'proh_promotion_info'       => $proh_promotion_info,
			'proh_min_day_before'       => $proh_min_day_before,
			'proh_max_day_before'       => $proh_max_day_before,
			'proh_free_night_num'       => $proh_free_night_num,
			'proh_free_night_discount'  => $proh_free_night_discount,
	  	];

	  	if ($action == self::ACTION_ADD) {
			$data_return['proh_create_time']          = time();
			$data_return['proh_create_admin_user_id'] = Auth::id();
	  	}

	  	return $data_return;
	}

	private function saveLogPromo($params)
	{
		$id        = $params['id'];
		$ip        = $params['ip'];
		$type      = $params['type'];
		$adm_id    = $params['adm_id'];
		$uri       = $params['uri'];

		$query = showQueryExecute();

		if($query != '')
		{

			$data_promo = $this->promotion->find($id)->toArray();

			foreach ($data_promo as $field => $values) {
	            $data[$field] = base64_encode($values);
	        }

			$this->adminLog->saveLog($data, $ip, $adm_id, 11, 'promotions_new', $type , $query, $uri, 'proh_id', $id);
		}
	}

	/**
	 * [getPrefixPromotion description]
	 * get Prefix Of Promotion
	 * @param  [type] $typePromo [description]
	 * @return [type]            [description]
	 */
	private function getPrefixPromotion ($typePromo)
	{
		switch ($typePromo) {
			case Promotion::TYPE_PROMO_EARLY:
				$this->prefixPromo = 'early_';
				break;

			case Promotion::TYPE_PROMO_LASTMINUTE:
				$this->prefixPromo = 'last_';
				break;

			case Promotion::TYPE_PROMO_CUSTOM:
				$this->prefixPromo = 'custom_';
				break;
		}

		return $this->prefixPromo;
	}

	/**
	 * updatePriceRatePlan
	 * Update Price Of rate
	 * @param  array $listIdRatePlan [description]
	 * @param  array $params         [description]
	 * @return [type]                 [description]
	 */
	public function updatePriceRatePlan(array $listIdRatePlan, array $params)
	{
		$hotelID            = array_get($params, 'hotel_id', "");
		$discount           = array_get($params, 'discount', []);
		$time_start         = array_get($params, 'time_start', 0);
		$time_finish        = array_get($params, 'time_finish', 0);
		$proh_discount_type = array_get($params, 'proh_discount_type', "");

		if (!empty($discount) && $time_start > 0 && $time_finish > 0) {
			foreach ($listIdRatePlan as $roomID => $infoRate) {
				foreach ($infoRate as $rateID => $rateParentID) {
					for ($i = strtime_firstday_month($time_start); $i <= $time_finish; $i = strtime_next_month($i)) {
						$table_price = 'room_price_' . date('Ym', $i);
						$params      = ['table' => $table_price, 'room_id' => $roomID, 'rate_plan_id' => $rateParentID, 'person_type' => RoomPrice::NUM_PERSON_MAX_TYPE];
						$dataPrice   = $this->getInfoPriceRateNew($hotelID, $params, $discount, $i, $time_finish, $proh_discount_type);

						if (!empty($dataPrice)) {
							$dataPrice['rop_rate_plan_id'] = $rateID;
							$dataPrice['rop_info_price_publish']  = json_encode($dataPrice['rop_info_price_publish']);
							$dataPrice['rop_info_price_contract'] = json_encode($dataPrice['rop_info_price_contract']);
							$params = [
								'table'        => $table_price,
								'room_id'      => $roomID,
								'rate_plan_id' => $rateID,
								'person_type'  => RoomPrice::NUM_PERSON_MAX_TYPE,
								'data_update'  => $dataPrice
							];

							$this->roomPrice->updatePriceByRoomRateId($params);
						}
					}
				}
			}
		}
	}

	/**
	 * [checkTypeHotel description]
	 * check kieu KS PMS hay ko
	 * @param  [type] $hotelID [description]
	 * @return [type]          [description]
	 */
	private function checkTypeHotelPms($hotelID)
	{
		$dataHotel = $this->hotel->select('hot_pms_active')
								 ->where('hot_id', '=', $hotelID)
								 ->get()->first();

	 	if (!is_null($dataHotel) && $dataHotel->hot_pms_active) {
	 		return true;
	 	} else {
	 		return false;
	 	}

	}

}
