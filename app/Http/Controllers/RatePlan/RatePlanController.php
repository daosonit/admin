<?php namespace App\Http\Controllers\RatePlan;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use View, Input, Redirect,Response, Auth, DB;
use App\Models\Components\Rooms;
use App\Models\Components\RatePlan;
use App\Models\Components\RoomsRatePlans;
use App\Models\Components\Hotel;
use App\Mytour\Validators\RatePlanValidator;
use App\Mytour\Classes\AdminLog;

use Illuminate\Http\Request;

class RatePlanController extends Controller {

	public function __construct(
		Rooms $rooms,
		RatePlan $ratePlan,
		RatePlanValidator $ratePlanValidator,
		RoomsRatePlans $roomsRatePlans,
		Hotel $hotel,
		AdminLog $adminLog
	)
	{
		$this->rooms             = $rooms;
		$this->ratePlan          = $ratePlan;
		$this->ratePlanValidator = $ratePlanValidator;
		$this->roomsRatePlans 	 = $roomsRatePlans;
		$this->hotel             = $hotel;
		$this->adminLog 		 = $adminLog;
	}



	public function getCreate($id)
	{
		$data = [];
		$info_room = $this->rooms->getRoomInfoByHotelId($id, ['active' => ACTIVE])->toArray();
		foreach ($info_room as $key => $value) {
			$data[$value['rom_id']]['room_name'] = $value['rom_name'];
			$data[$value['rom_id']]['room_id'] = $value['rom_id'];
		}

		$params = [
					'field_select' => ['hot_ota_only'],
					'hotel_id'	   => $id
					];
		$hotel_info = $this->hotel->getInfoHotelById($params);
		$hotel_type['ota'] = $hotel_info->hot_ota_only;
		$hotel_type['hybrid'] = $hotel_info->hot_ota_hybrid;

		$option = [];
		for ($i = 1;$i <= 10;$i++) {
			$option[$i] = $i * 10 . '% giá trị đơn phòng';
		}
		$option[0] = "Miễn phí";
		$option[11] = "1 đêm đầu tiên";
		$option[12] = "2 đêm đầu tiên";
		return View::make('components.modules.rate_plan.create', compact('data','option','hotel_type'));
	}

	public function postCreate(Request $request, $id) {
		$rap_cancel_policy_info = '';
		$arr_cancel_policy_info = [];
		$rap_hotel_id           = $id;

		if($this->checkUniqueRoom($request, $id))
		{
			$this->ratePlanValidator->pushCreateField(['room_unique' => 'required'])
						      		->pushCreateMsg(['room_unique.required' => 'Tên đơn giá đã tồn tại.']);
		}

		if($this->checkExtraBox($request, 1))
		{
			$this->ratePlanValidator->pushCreateField(['bed_required' => 'required'])
						      		->pushCreateMsg(['bed_required.required' => 'Bạn chưa nhập thông tin thêm giường.']);
		}

		if($this->checkExtraBox($request, 2))
		{
			$this->ratePlanValidator->pushCreateField(['adult_required' => 'required'])
						      		->pushCreateMsg(['adult_required.required' => 'Bạn chưa nhập đủ thông tin thêm người lớn.']);
		}

		if($this->checkExtraBox($request, 3))
		{
			$this->ratePlanValidator->pushCreateField(['child_required' => 'required'])
						      		->pushCreateMsg(['child_required.required' => 'Bạn chưa nhập đủ thông tin thêm trẻ em.']);
		}


		$this->ratePlanValidator->validateDataCreate($request);

		$data_create = $request->all();

		$rap_room_apply_id = json_encode($data_create['rap_room_apply_id']);

		if($data_create['type_policy'] == 2)
		{
			$cancel_policy_info = [];
			foreach ($data_create['rap_policy_day'] as $key => $value) {
				$cancel_policy_info[$key]['day'] = $value;
				$cancel_policy_info[$key]['fee'] = $data_create['rap_policy_fee'][$key];
			}

			$arr_cancel_policy_info['cancel_policy_info'] = $cancel_policy_info;


			if($data_create['policy_group_room'] != '')
			{
				$group_cancel_policy_info = [];
				$group_cancel_policy_info['num_rooms'] = $data_create['policy_group_room'];
				foreach ($data_create['policy_group_day'] as $key => $value) {
					$group_cancel_policy_info[$key]['day'] = $value;
					$group_cancel_policy_info[$key]['fee'] = $data_create['policy_group_fee'][$key];
				}
				$arr_cancel_policy_info['group_cancel_policy_info'] = $group_cancel_policy_info;
			}

			if(isset($data_create['top_period_time']))
			{
				$peak_period = [];
				$group_period = [];
				foreach ($data_create['top_period_time'] as $key => $value) {
					$check_period = 'check_period_' . ($key + 1);
					$time_range  = explode(' - ', $value);

		            $time_start          = $time_range[0];
		            $time_start          = str_replace('/', '-', $time_start);
		            $time_checkin 		 = strtotime($time_start);

		            $time_finish         = $time_range[1];
		            $time_finish         = str_replace('/', '-', $time_finish);
		            $time_checkout 		 = strtotime($time_finish);

		            $peak_period[$key]['date'] = [$time_checkin, $time_checkout];
					if(isset($data_create[$check_period])) {
						$peak_period[$key]['check_period'] = 1;
						$peak_period[$key][0]['day'] = 1;
						$peak_period[$key][0]['fee'] = 1;
						$peak_period[$key][1]['day'] = 1;
						$peak_period[$key][1]['fee'] = 1;
						if($data_create['rap_type_price'] == RatePlan::TYPE_PRICE_TA) {
			            	$group_period[$key]['num_rooms'] = '';
			            	$group_period[$key][0]['day'] = 1;
			            	$group_period[$key][0]['fee'] = 1;
			            	$group_period[$key][1]['day'] = 1;
			            	$group_period[$key][1]['fee'] = 1;
				        }
					} else {
			            $period_day = 'top_period_day_' . ($key+1);
			            $period_fee = 'top_period_fee_' . ($key+1);

			            foreach ($data_create[$period_day] as $keys => $period) {
			            	$peak_period[$key][$keys]['day'] = $period;
			            	$peak_period[$key][$keys]['fee'] = $data_create[$period_fee][$keys];
			            }

			            if($data_create['rap_type_price'] == RatePlan::TYPE_PRICE_TA) {
				            $min_room = 'group_period_room_' . ($key + 1);
			            	$group_period[$key]['num_rooms'] = $data_create[$min_room];
			            	$group_period_day = 'group_period_day_' . ($key+1);
			            	$group_period_fee = 'group_period_fee_' . ($key+1);

			            	foreach ($data_create[$group_period_day] as $group => $gr_period) {
				            	$group_period[$key][$group]['day'] = $gr_period;
				            	$group_period[$key][$group]['fee'] = $data_create[$group_period_fee][$group];
				            }
				        }
				    }

				}
				$arr_cancel_policy_info['peak_period']  = $peak_period;
				if($data_create['rap_type_price'] == RatePlan::TYPE_PRICE_TA) {
					$arr_cancel_policy_info['group_period']  = $group_period;
				}
			}

			$rap_cancel_policy_info = json_encode($arr_cancel_policy_info);
		}

		$surcharge_info = [];
		$room_apply_bed = [];
		if(isset($data_create['add_extra_bed']) && $data_create['add_extra_bed'] == 1){
			$surcharge_info['add_extra_bed']   = $data_create['add_extra_bed'];
			$surcharge_info['bed_extra_price'] = format_currency($data_create['bed_extra_price']);

			if(isset($data_create['rap_room_apply_bed'])) {
				$room_apply_bed = $data_create['rap_room_apply_bed'];
			}
		}
		$rap_room_apply_bed = json_encode($room_apply_bed);

		if(isset($data_create['add_extra_adult']) && $data_create['add_extra_adult'] == 1){
			$surcharge_info['add_extra_adult'] = $data_create['add_extra_adult'];
			$surcharge_info['number_adult']    = $data_create['number_adult'];
			$surcharge_info['price_adult']     = format_currency($data_create['price_adult']);
		}

		if(isset($data_create['add_extra_child']) && $data_create['add_extra_child'] == 1){
			$surcharge_info['add_extra_child'] = $data_create['add_extra_child'];
			$surcharge_info['number_child']    = $data_create['number_child'];
			$surcharge_info['min_child']       = $data_create['min_child'];
			$surcharge_info['max_child']       = $data_create['max_child'];
			$array_currency = [];
			foreach ($data_create['extra_child'] as $key_curr => $currency) {
				$array_currency[$key_curr] = format_currency($currency);
			}
			$surcharge_info['extra_child']     = $array_currency;
			$surcharge_info['child_adult']     = $data_create['child_adult'];
		}

		$rap_surcharge_info = json_encode($surcharge_info);

		$accompanied_service = [];
		if(isset($data_create['conv'])){
			foreach ($data_create['conv'] as $service) {
				if($service == 1) $accompanied_service[] = 'Bữa sáng';
				if($service == 2) $accompanied_service[] = 'Bữa trưa';
				if($service == 3) $accompanied_service[] = 'Bữa tối';
			}
		}

		$rap_accompanied_service = json_encode($accompanied_service);

		$hidden_price = 0;
		if($data_create['rap_type_price'] == 0) {
			$hidden_price = 1;
		}

		$param_insert = array('rap_hotel_id' 			=> $rap_hotel_id,
							  'rap_title'	 			=> $data_create['rap_title'],
							  'rap_room_apply_id' 		=> $rap_room_apply_id,
							  'rap_type_price' 			=> $data_create['rap_type_price'],
							  'rap_surcharge_info' 		=> $rap_surcharge_info,
							  'rap_accompanied_service' => $rap_accompanied_service,
							  'rap_cancel_policy_info' 	=> $rap_cancel_policy_info,
							  'rap_room_apply_bed' 		=> $rap_room_apply_bed,
							  ''
							  );

		$status = $this->ratePlan->insertRatePlan($param_insert);


		foreach ($data_create['rap_room_apply_id'] as $key => $id_room) {
			$dataInsert[] = $id_room;
		}
		$this->ratePlan->rooms()->attach($dataInsert, ['rrp_hidden_price' => 1]);

		if($status){
			$uri                    = $request->path();
			$adm_id                 = $request->user()->id;
			$ip                     = $request->ip();
			$params_insert = array(
							   'id' 	   => $status->rap_id,
							   'ip'		   => $ip,
							   'type'	   => 1,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

			$this->saveLogRate($params_insert);

			return Redirect::to(route('rate-plan-list', array('id' => $id)));
		}
	}

	public function getData($id) {
		$data = [];
		$hotel_id = $id;
		$info_hotel = $this->hotel->find($id);
		$hotel_type = $info_hotel->hot_pms_active;

		$params_select = ['hotel_id'=> $id];
		if($hotel_type) {
			$data_rate_plan = $this->ratePlan->getRatePlanPmsListingByHotelId($params_select);
		} else {
			$data_rate_plan = $this->ratePlan->getRatePlanListingByHotelId($params_select);
		}

		foreach ($data_rate_plan as $key => $info_rate) {
			$data[$key]['rate_id'] = $info_rate->rap_id;
			$data[$key]['rate_active'] = $info_rate->rap_active;
			$data[$key]['rate_name'] = $info_rate->rap_title;
			if($info_rate->rap_type_price == RatePlan::TYPE_PRICE_OTA){
				$data[$key]['rate_type'] = 'Khách lẻ (OTA)';
			} elseif($info_rate->rap_type_price == RatePlan::TYPE_PRICE_TA) {
				$data[$key]['rate_type'] = 'Đại lý (TA)';
			}

			$params_select_room = ['field_select' => 'rom_name',
									   'room_ids' 	  => json_decode($info_rate->rap_room_apply_id, true)];

			$room_names = $this->rooms->getRoomInfoByRoomId($params_select_room)->toArray();

			$data[$key]['rate_room'] = $room_names;

			if($info_rate->rap_cancel_policy_info == ''){
				$data[$key]['rate_policy'] = 'Không hoàn hủy';
			} else {
				$data[$key]['rate_policy'] = 'Tùy chỉnh';
			}

			$surcharge_info = json_decode($info_rate->rap_surcharge_info,true);

			$data[$key]['rate_surcharge'] = [];
			if(!empty($surcharge_info)){
				if(isset($surcharge_info['add_extra_bed']) && $surcharge_info['add_extra_bed'] == 1)
				{
					$data[$key]['rate_surcharge']['extra_bed']['name']  = 'Giường phụ';
					$data[$key]['rate_surcharge']['extra_bed']['price'] = $surcharge_info['bed_extra_price'];
				}

				if(isset($surcharge_info['add_extra_adult']) && $surcharge_info['add_extra_adult'] == 1)
				{
					$data[$key]['rate_surcharge']['extra_adult']['name']   = 'Thêm người lớn';
					$data[$key]['rate_surcharge']['extra_adult']['number'] = $surcharge_info['number_adult'];
					$data[$key]['rate_surcharge']['extra_adult']['price']  = $surcharge_info['price_adult'];
				}

				if(isset($surcharge_info['add_extra_child']) && $surcharge_info['add_extra_child'] == 1)
				{
					$data[$key]['rate_surcharge']['extra_child']['name'] = 'Trẻ em';
					$data[$key]['rate_surcharge']['extra_child']['number'] = $surcharge_info['number_child'];
					if(!empty($surcharge_info['min_child'])) {
						foreach ($surcharge_info['min_child'] as $keys => $info_child) {
							$data[$key]['rate_surcharge']['extra_child']['limit'][$keys]['from'] = $info_child;
							$data[$key]['rate_surcharge']['extra_child']['limit'][$keys]['to'] = $surcharge_info['max_child'][$keys];
							$data[$key]['rate_surcharge']['extra_child']['limit'][$keys]['price'] = $surcharge_info['extra_child'][$keys];
						}
					} else {
						$data[$key]['rate_surcharge']['extra_child']['limit'][0]['from'] = 0;
						$data[$key]['rate_surcharge']['extra_child']['limit'][0]['to'] = 0;
						$data[$key]['rate_surcharge']['extra_child']['limit'][0]['price'] = 0;
					}
					$data[$key]['rate_surcharge']['extra_child']['child_adult'] = $surcharge_info['child_adult'];
					
				}

			}

			$accompanied_service = json_decode($info_rate->rap_accompanied_service,true);
			$data[$key]['rate_service'] = [];
			if(!empty($accompanied_service)){
				$data[$key]['rate_service'] = $accompanied_service;
			}

		}

		return View::make('components.modules.rate_plan.index', compact('data','hotel_id','hotel_type'));
	}

	public function getEdit($id)
	{
		$data = [];
		$rate_plan_info = $this->ratePlan->find($id);
		$can = json_decode($rate_plan_info->rap_cancel_policy_info,true);
		// dd($can);
		$info_room = $this->rooms->getRoomInfoByHotelId($rate_plan_info->rap_hotel_id, ['active' => ACTIVE])->toArray();

		foreach ($info_room as $key => $value) {
			$data[$value['rom_id']]['room_name'] = $value['rom_name'];
			$data[$value['rom_id']]['room_id'] = $value['rom_id'];
		}

		$params = [
					'field_select' => ['hot_ota_only'],
					'hotel_id'	   => $rate_plan_info->rap_hotel_id
					];
		$hotel_info = $this->hotel->getInfoHotelById($params);
		$hotel_type['ota'] = $hotel_info->hot_ota_only;
		$hotel_type['hybrid'] = $hotel_info->hot_ota_hybrid;

		$option = [];
		for ($i = 1;$i <= 10;$i++) {
			$option[$i] = $i * 10 . '% giá trị đơn phòng';
		}
		$option[0] = "Miễn phí";
		$option[11] = "1 đêm đầu tiên";
		$option[12] = "2 đêm đầu tiên";

		$data_rate_plan['name'] = $rate_plan_info->rap_title;
		$data_rate_plan['type-price'] = $rate_plan_info->rap_type_price;
		$data_rate_plan['room'] = json_decode($rate_plan_info->rap_room_apply_id,true);
		$data_rate_plan['policy'] = $rate_plan_info->rap_cancel_policy_info;
		if($data_rate_plan['policy'] != ''){
			$policy_info = json_decode($data_rate_plan['policy'],true);

			if(isset($policy_info['cancel_policy_info'])){
				foreach ($policy_info['cancel_policy_info'] as $key_policy => $info) {
					if($key_policy == 0 || $key_policy == 1){
						$data_rate_plan['cancel_policy_info'][$key_policy]['day'] = $info['day'];
						$data_rate_plan['cancel_policy_info'][$key_policy]['fee'] = $info['fee'];
					} else {
						$data_rate_plan['ex_cancel_policy_info'][$key_policy]['day'] = $info['day'];
						$data_rate_plan['ex_cancel_policy_info'][$key_policy]['fee'] = $info['fee'];
					}

				}
			}

			if(isset($policy_info['group_cancel_policy_info'])){
				$data_rate_plan['group_cancel_policy_info']['num_rooms'] = $policy_info['group_cancel_policy_info']['num_rooms'];
				unset($policy_info['group_cancel_policy_info']['num_rooms']);

				foreach ($policy_info['group_cancel_policy_info'] as $key_group => $info_gr) {
					if($key_group == 0 || $key_group == 1){
						$data_rate_plan['group_cancel_policy_info'][$key_group]['day'] = $info_gr['day'];
						$data_rate_plan['group_cancel_policy_info'][$key_group]['fee'] = $info_gr['fee'];
					} else {
						$data_rate_plan['ex_group_cancel_policy_info'][$key_group]['day'] = $info_gr['day'];
						$data_rate_plan['ex_group_cancel_policy_info'][$key_group]['fee'] = $info_gr['fee'];
					}

				}
			}

			if(isset($policy_info['peak_period'])){
				foreach ($policy_info['peak_period'] as $key_period => $info_period) {
					if(isset($info_period['check_period'])) $data_rate_plan['peak_period'][$key_period]['check_period'] = $info_period['check_period'];
					$date_start  = date('d/m/Y', $info_period['date'][0]);
					$date_finish = date('d/m/Y', $info_period['date'][1]);
					$data_rate_plan['peak_period'][$key_period]['date'] = $date_start . ' - ' . $date_finish;
					unset($info_period['date']);

					foreach ($info_period as $key_period_ex => $info_period_ex) {
						if($key_period_ex == 0 || $key_period_ex == 1){
							$data_rate_plan['peak_period'][$key_period]['period'][$key_period_ex]['day'] = $info_period_ex['day'];
							$data_rate_plan['peak_period'][$key_period]['period'][$key_period_ex]['fee'] = $info_period_ex['fee'];
						} else {
							$data_rate_plan['peak_period'][$key_period]['ex_period'][$key_period_ex]['day'] = $info_period_ex['day'];
							$data_rate_plan['peak_period'][$key_period]['ex_period'][$key_period_ex]['fee'] = $info_period_ex['fee'];
						}
					}
				}

				if(isset($policy_info['group_period'])){
					foreach ($policy_info['group_period'] as $group_period => $info_group_period) {
						$data_rate_plan['group_period'][$group_period]['num_rooms'] = $info_group_period['num_rooms'];
						unset($info_group_period['num_rooms']);
						foreach ($info_group_period as $group_period_ex => $info_group_period_ex) {
							$data_rate_plan['group_period'][$group_period]['group'][$group_period_ex]['day'] = $info_group_period_ex['day'];
							$data_rate_plan['group_period'][$group_period]['group'][$group_period_ex]['fee'] = $info_group_period_ex['fee'];
						}
					}
				}
			}

		}

		$surcharge_info = json_decode($rate_plan_info->rap_surcharge_info,true);
		if(empty($surcharge_info)){
			$data_rate_plan['surcharge_info'] = ['add_extra_bed' 	=> '',
												 'bed_extra_price' 	=> '',
												 'add_extra_adult' 	=> '',
												 'number_adult' 	=> '',
												 'price_adult'		=> '',
												 'add_extra_child' 	=> '',
												 'number_child' 	=> '',
												 'min_child' 		=> [0 => ''],
												 'max_child' 		=> [0 => ''],
												 'extra_child' 		=> [0 => ''],
												 'child_adult' 		=> ''];
		} else {
			$data_rate_plan['surcharge_info'] = ['add_extra_bed' 	=> (isset($surcharge_info['add_extra_bed']) ? 'checked' : ''),
												 'bed_extra_price' 	=> (isset($surcharge_info['bed_extra_price']) ? $surcharge_info['bed_extra_price'] : ''),
												 'add_extra_adult' 	=> (isset($surcharge_info['add_extra_adult']) ? 'checked' : ''),
												 'number_adult' 	=> (isset($surcharge_info['number_adult']) ? $surcharge_info['number_adult'] : ''),
												 'price_adult' 		=> (isset($surcharge_info['price_adult']) ? $surcharge_info['price_adult'] : ''),
												 'add_extra_child' 	=> (isset($surcharge_info['add_extra_child']) ? 'checked' : ''),
												 'number_child' 	=> (isset($surcharge_info['number_child']) ? $surcharge_info['number_child'] : ''),
												 'min_child' 		=> (isset($surcharge_info['min_child']) ? $surcharge_info['min_child'] : [0 => '']),
												 'max_child' 		=> (isset($surcharge_info['max_child']) ? $surcharge_info['max_child'] : [0 => '']),
												 'extra_child' 		=> (isset($surcharge_info['extra_child']) ? $surcharge_info['extra_child'] : [0 => '']),
												 'child_adult' 		=> (isset($surcharge_info['child_adult']) ? $surcharge_info['child_adult'] : '')];
		}

		$data_rate_plan['accompanied_service'] = ['','','',''];
		$accompanied_service = json_decode($rate_plan_info->rap_accompanied_service,true);
		if(in_array('Bữa sáng', $accompanied_service)) $data_rate_plan['accompanied_service'][0] = 'checked';
		if(in_array('Bữa trưa', $accompanied_service)) $data_rate_plan['accompanied_service'][1] = 'checked';
		if(in_array('Bữa tối', $accompanied_service))  $data_rate_plan['accompanied_service'][2] = 'checked';
		if(count($accompanied_service) == 3) $data_rate_plan['accompanied_service'][3] = 'checked';

		// Thông tin phòng áp dụng phụ thu giường phụ
		$data_rate_plan['room_apply_bed'] = array();
		$room_apply_bed = json_decode($rate_plan_info->rap_room_apply_bed,true);
		if(count($room_apply_bed) > 0) {
			$data_rate_plan['room_apply_bed'] = $room_apply_bed;
		}

		return View::make('components.modules.rate_plan.edit', compact('data','option','data_rate_plan', 'hotel_type'));
	}

	public function postEdit(Request $request, $id) {
		$data_create = $request->all();
		$rate_plan = $this->ratePlan->find($id);

		$rap_cancel_policy_info = '';
		$arr_cancel_policy_info = [];

		if($rate_plan->rap_title != $data_create['rap_title']){
			if($this->checkUniqueRoom($request, $rate_plan->rap_hotel_id))
			{
				$this->ratePlanValidator->pushEditField(['room_unique' => 'required'])
							      		->pushEditMsg(['room_unique.required' => 'Tên đơn giá đã tồn tại.']);
			}
			$this->ratePlanValidator->validateDataEdit($request);
		}

		$rap_room_apply_id = json_encode($data_create['rap_room_apply_id']);
		$room_apply_id = json_decode($rate_plan->rap_room_apply_id,true);

		if($data_create['type_policy'] == 2)
		{
			$cancel_policy_info = [];
			foreach ($data_create['rap_policy_day'] as $key => $value) {
				$cancel_policy_info[$key]['day'] = $value;
				$cancel_policy_info[$key]['fee'] = $data_create['rap_policy_fee'][$key];
			}

			$arr_cancel_policy_info['cancel_policy_info'] = $cancel_policy_info;


			if($data_create['policy_group_room'] != '')
			{
				$group_cancel_policy_info = [];
				$group_cancel_policy_info['num_rooms'] = $data_create['policy_group_room'];
				foreach ($data_create['policy_group_day'] as $key => $value) {
					$group_cancel_policy_info[$key]['day'] = $value;
					$group_cancel_policy_info[$key]['fee'] = $data_create['policy_group_fee'][$key];
				}
				$arr_cancel_policy_info['group_cancel_policy_info'] = $group_cancel_policy_info;
			}

			if(isset($data_create['top_period_time']))
			{
				$peak_period = [];
				foreach ($data_create['top_period_time'] as $key => $value) {
					$time_range  = explode(' - ', $value);

		            $time_start          = $time_range[0];
		            $time_start          = str_replace('/', '-', $time_start);
		            $time_checkin 		 = strtotime($time_start);

		            $time_finish         = $time_range[1];
		            $time_finish         = str_replace('/', '-', $time_finish);
		            $time_checkout 		 = strtotime($time_finish);

		            $peak_period[$key]['date'] = [$time_checkin, $time_checkout];
					$check_period = 'check_period_' . ($key + 1);

					if(isset($data_create[$check_period])) {
						$peak_period[$key]['check_period'] = 1;
						$peak_period[$key][0]['day'] = 1;
		            	$peak_period[$key][0]['fee'] = 1;
		            	$peak_period[$key][1]['day'] = 1;
		            	$peak_period[$key][1]['fee'] = 1;
		            	if($data_create['rap_type_price'] == RatePlan::TYPE_PRICE_TA) {
			            	$group_period[$key]['num_rooms'] = '';
			            	$group_period[$key][0]['day'] = 1;
			            	$group_period[$key][0]['fee'] = 1;
			            	$group_period[$key][1]['day'] = 1;
			            	$group_period[$key][1]['fee'] = 1;
				        }
					} else {
					
			            $period_day = 'top_period_day_' . ($key+1);
			            $period_fee = 'top_period_fee_' . ($key+1);

			            foreach ($data_create[$period_day] as $keys => $period) {
			            	$peak_period[$key][$keys]['day'] = $period;
			            	$peak_period[$key][$keys]['fee'] = $data_create[$period_fee][$keys];
			            }

			            if($data_create['rap_type_price'] == RatePlan::TYPE_PRICE_TA) {
				            $min_room = 'group_period_room_' . ($key + 1);
			            	$group_period[$key]['num_rooms'] = $data_create[$min_room];
			            	$group_period_day = 'group_period_day_' . ($key+1);
			            	$group_period_fee = 'group_period_fee_' . ($key+1);

			            	foreach ($data_create[$group_period_day] as $group => $gr_period) {
				            	$group_period[$key][$group]['day'] = $gr_period;
				            	$group_period[$key][$group]['fee'] = $data_create[$group_period_fee][$group];
				            }
				        }
				    }
		            
				}
				$arr_cancel_policy_info['peak_period']  = $peak_period;
				if($data_create['rap_type_price'] == RatePlan::TYPE_PRICE_TA) {
					$arr_cancel_policy_info['group_period']  = $group_period;
				}
			}

			$rap_cancel_policy_info = json_encode($arr_cancel_policy_info);
		}

		$surcharge_info = [];
		$room_apply_bed = [];
		if(isset($data_create['add_extra_bed']) && $data_create['add_extra_bed'] == 1){
			$surcharge_info['add_extra_bed']   = $data_create['add_extra_bed'];
			$surcharge_info['bed_extra_price'] = format_currency($data_create['bed_extra_price']);
		
			if(isset($data_create['rap_room_apply_bed'])) {
				$room_apply_bed = $data_create['rap_room_apply_bed'];
			}	
		}
		$rap_room_apply_bed = json_encode($room_apply_bed);

		if(isset($data_create['add_extra_adult']) && $data_create['add_extra_adult'] == 1){
			$surcharge_info['add_extra_adult'] = $data_create['add_extra_adult'];
			$surcharge_info['number_adult']    = $data_create['number_adult'];
			$surcharge_info['price_adult']     = format_currency($data_create['price_adult']);
		}

		if(isset($data_create['add_extra_child']) && $data_create['add_extra_child'] == 1){
			$surcharge_info['add_extra_child'] = $data_create['add_extra_child'];
			$surcharge_info['number_child']    = $data_create['number_child'];
			$surcharge_info['min_child']       = $data_create['min_child'];
			$surcharge_info['max_child']       = $data_create['max_child'];
			$array_currency = [];
			foreach ($data_create['extra_child'] as $key_curr => $currency) {
				$array_currency[$key_curr] = format_currency($currency);
			}
			$surcharge_info['extra_child']     = $array_currency;
			$surcharge_info['child_adult']     = $data_create['child_adult'];
		}

		$rap_surcharge_info = json_encode($surcharge_info);

		$accompanied_service = [];
		if(isset($data_create['conv'])){
			foreach ($data_create['conv'] as $service) {
				if($service == 1) $accompanied_service[] = 'Bữa sáng';
				if($service == 2) $accompanied_service[] = 'Bữa trưa';
				if($service == 3) $accompanied_service[] = 'Bữa tối';
			}
		}

		$rap_accompanied_service = json_encode($accompanied_service);

		$rate_plan->rap_title               = $data_create['rap_title'];
		$rate_plan->rap_room_apply_id       = $rap_room_apply_id;
		$rate_plan->rap_type_price          = $data_create['rap_type_price'];
		$rate_plan->rap_surcharge_info      = $rap_surcharge_info;
		$rate_plan->rap_accompanied_service = $rap_accompanied_service;
		$rate_plan->rap_cancel_policy_info  = $rap_cancel_policy_info;
		$rate_plan->rap_room_apply_bed 		= $rap_room_apply_bed;

		DB::enableQueryLog();

		$status = $rate_plan->save();

		$rate_plan_child = $this->ratePlan->where('rap_parent_id', $id)->get()->first();

		$remove_room_apply = array_diff($room_apply_id, $data_create['rap_room_apply_id']);
		if(count($remove_room_apply) > 0){
			$rate_plan->rooms()->detach($remove_room_apply);

			if($rate_plan_child != NULL) {
				$rate_plan_child->rooms()->detach($remove_room_apply);
			}
		}

		$add_room_apply = array_diff($data_create['rap_room_apply_id'], $room_apply_id);
		if(count($add_room_apply) > 0) {
			$rate_plan->rooms()->attach($add_room_apply);
			if($rate_plan_child != NULL) {
				$rate_plan_child->rooms()->attach($add_room_apply, ['rrp_hidden_price' => 1]);
				$rate_plan_child->rap_room_apply_id = $rap_room_apply_id;
				$rate_plan_child->save();
			}
		}

		if($status){
			$uri                    = $request->path();
			$adm_id                 = $request->user()->id;
			$ip                     = $request->ip();
			$params_update = array(
							   'id' 	   => $id,
							   'ip'		   => $ip,
							   'type'	   => 2,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

			$this->saveLogRate($params_update);
			return Redirect::to(route('rate-plan-list', array('id' => $rate_plan->rap_hotel_id)));
		}
	}

	public function deleteRatePlan(Request $request)
	{
		$uri                    = $request->path();
		$adm_id                 = $request->user()->id;
		$ip                     = $request->ip();

		$rateID = Input::get('id_rate');

		$rate_plan = $this->ratePlan->find($rateID);

		$rate_plan->rap_delete = 1;
		$rate_plan->rap_delete_time = time();
		$rate_plan->rap_delete_user_id = Auth::id();

		DB::enableQueryLog();

		$rate_plan->save();

		$rate_plan_child = $this->ratePlan->where('rap_parent_id', '=', $rateID)->get()->first();
		$rate_plan_child->rap_delete = 1;
		$rate_plan_child->rap_delete_time = time();
		$rate_plan_child->rap_delete_user_id = Auth::id();

		$rate_plan_child->save();

		$params_update = array(
							   'id' 	   => $rateID,
							   'ip'		   => $ip,
							   'type'	   => 2,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

		$this->saveLogRate($params_update);
		// return redirect()->back();
	}

	public function activeRatePlan(Request $request)
	{
		$uri                    = $request->path();
		$adm_id                 = $request->user()->id;
		$ip                     = $request->ip();

		$rateID = Input::get('id_rate');

		$rate_plan = $this->ratePlan->find($rateID);

		$rate_plan->rap_active = abs($rate_plan->rap_active - 1);

		DB::enableQueryLog();

		$rate_plan->save();

		$rate_plan_child = $this->ratePlan->where('rap_parent_id', '=', $rateID)->get()->first();
		$rate_plan_child->rap_active = abs($rate_plan_child->rap_active - 1);

		$rate_plan_child->save();

		$params_update = array(
							   'id' 	   => $rateID,
							   'ip'		   => $ip,
							   'type'	   => 2,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

		$this->saveLogRate($params_update);
	}

	private function checkUniqueRoom(Request $request, $idHotel)
	{
		$rate_plan_name = $request->get('rap_title');

		$params_room = ['field_select'	=> ['rap_title'],
						'hotel_id'	=> $idHotel];
		$data_rate_plan = $this->ratePlan->getRatePlanListingByHotelId($params_room);
		$array_rate_plan = [];
		foreach ($data_rate_plan as $key => $rate_plan) {
			$array_rate_plan[] = $rate_plan->rap_title;
		}

		return (in_array($rate_plan_name,$array_rate_plan));
	}

	private function checkExtraBox(Request $request, $box)
	{
		$data_create = $request->all();

		switch ($box) {
			case '1':
				if(isset($data_create['add_extra_bed']) && $data_create['bed_extra_price'] == '')
				{
					return true;
				}
				break;

			case '2':
				if(isset($data_create['add_extra_adult']) && ($data_create['number_adult'] == '' || $data_create['price_adult'] == ''))
				{
					return true;
				}
				break;

			case '3':
				if(isset($data_create['add_extra_child']) && ($data_create['number_child'] == '' || in_array('',$data_create['min_child']) ||
													in_array('',$data_create['max_child']) || in_array('',$data_create['extra_child']) ||
													$data_create['child_adult'] == ''))
				{
					return true;
				}
				break;
		}

	}

	private function saveLogRate($params)
	{
		$id        = $params['id'];
		$ip        = $params['ip'];
		$type      = $params['type'];
		$adm_id    = $params['adm_id'];
		$uri       = $params['uri'];

		$query = showQueryExecute();

		if($query != '')
		{

			$data_room = $this->ratePlan->find($id)->toArray();

			foreach ($data_room as $field => $values) {
	            $data[$field] = base64_encode($values);
	        }

			$this->adminLog->saveLog($data, $ip, $adm_id, 11, 'rate_plans', $type , $query, $uri, 'rap_id', $id);
		}
	}

}

