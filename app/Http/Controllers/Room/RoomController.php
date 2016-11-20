<?php namespace App\Http\Controllers\Room;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Components\Rooms;
use App\Models\Components\Conveniences;
use App\Mytour\Validators\RoomValidator;
use App\Mytour\Classes\AdminLog;

use Illuminate\Http\Request;
use View,Input,Redirect,Auth,DB;

class RoomController extends Controller {

	public function __construct(
		Rooms $rooms,
		Conveniences $conveniences,
		RoomValidator $roomValidator,
		AdminLog $adminLog
	)
	{
		$this->rooms         = $rooms;
		$this->conveniences  = $conveniences;
		$this->roomValidator = $roomValidator;
		$this->adminLog      = $adminLog;
	}

	public function getCreate()
	{
		$param_conv = ['field_select' => array('con_id', 'con_type', 'con_name'),
						'not_in_id'	  => array(70,83),
						'con_type' 	  => array(5)];
		$data_conveniences = $this->conveniences->getInfoConveniences($param_conv);
		// dd($data_conveniences);
		return View::make('components.modules.room.create', compact('data_conveniences'));
	}

	public function postCreate(Request $request, $id) {
		$data_create = $request->all();

		if($this->checkAddBed($request))
		{
			$this->roomValidator->pushCreateField(['bed_required' => 'required'])
						      	->pushCreateMsg(['bed_required.required' => 'Bạn chưa chọn số lượng giường.']);
		}
		
		$this->roomValidator->validateDataCreate($request);

		$array_info_bed = [];
		$array_info_bed['single-bed']   = $data_create['bed']['single-bed'];
		$array_info_bed['double-bed']   = $data_create['bed']['double-bed'];
		$array_info_bed['queen-bed']    = $data_create['bed']['queen-bed'];
		$array_info_bed['king-bed']     = $data_create['bed']['king-bed'];
		$array_info_bed['division-bed'] = $data_create['bed']['division-bed'];
		$array_info_bed['sofa-bed']     = $data_create['bed']['sofa-bed'];
		$room_info_bed = json_encode($array_info_bed);

		$array_exchange_bed = [];
		$array_exchange_bed['ex-single-bed']   = $data_create['ex-single-bed'];
		$array_exchange_bed['ex-double-bed']   = $data_create['ex-double-bed'];
		$array_exchange_bed['ex-queen-bed']    = $data_create['ex-queen-bed'];
		$array_exchange_bed['ex-king-bed']     = $data_create['ex-king-bed'];
		$array_exchange_bed['ex-division-bed'] = $data_create['ex-division-bed'];
		$array_exchange_bed['ex-sofa-bed']     = $data_create['ex-sofa-bed'];
		$room_exchange_bed = json_encode($array_exchange_bed);

		$param_insert = array('rom_hotel' 			=> $id,
							  'rom_name'	 		=> $data_create['room_type'],
							  'rom_stock' 			=> $data_create['room_total'],
							  'rom_area' 			=> $data_create['room_area'],
							  'rom_person' 			=> $data_create['adult'],
							  'rom_children' 		=> $data_create['child'],
							  'rom_smoke' 			=> $data_create['room_smoke'],
							  'rom_info_bed' 		=> $room_info_bed,
							  'rom_exchange_bed' 	=> $room_exchange_bed,
							  'rom_trend' 			=> $data_create['trend']
							  );
		$status = $this->rooms->insertRoom($param_insert);

		$uri    = $request->path();
		$adm_id = $request->user()->id;
		$ip     = $request->ip();

		//save log insert room
		$params_insert_room = array(
							   		'id' 	   	=> $status->rom_id,
								   	'ip'		=> $ip,
								   	'type'	   	=> 1,
								   	'adm_id'	=> $adm_id,
								   	'uri'	   	=> $uri
								   	);

		$this->saveLogRoom($params_insert_room);

		//insert tiện nghi vào bảng convenience
		foreach ($data_create['convenience'] as $key => $con_id) {
			$dataInsert[] = $con_id;
		}

		$this->rooms->conveniences()->attach($dataInsert);

		//update lại tiện nghi phòng vào bảng rooms
		$data_conveniences = $this->rooms->conveniences->toArray();
		$new_room_id = $this->rooms->rom_id;
		$array_convenience = [];
		foreach ($data_conveniences as $key => $value) {
			$array_convenience[$key] = $value;
			$array_convenience[$key]['roc_room_id'] = $new_room_id;
			unset($array_convenience[$key]['con_hot'],$array_convenience[$key]['con_active'],
				  $array_convenience[$key]['con_delete'],$array_convenience[$key]['con_order'],
				  $array_convenience[$key]['pivot']);
		}
		$info_convenience = json_encode($array_convenience);
		$this->rooms->rom_conveniences = $info_convenience;

		$this->rooms->save();
		
		//save log update convenience
		$params_update = array(
							   'id' 	   => $status->rom_id,
							   'ip'		   => $ip,
							   'type'	   => 2,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

		$this->saveLogRoom($params_update);
		
		if($status){
			return Redirect::to(route('room-list', array('id' => $id)));
		}
	}

	private function checkAddBed(Request $request)
	{
		$bed = $request->get('bed');

		return (array_sum($bed) < 1);
	}

	public function getData($id)
	{
		$data = [];
		$params = ['rom_hotel' => $id];
		$data_room = $this->rooms->getAllRoomInfoByHotelId($params);
		$params_bed = ['Giường đơn','Giường đôi','Giường đôi lớn','Giường đôi rất lớn','Giường tầng','Giường Sofa'];
		$data_room->load('hotel');
		foreach ($data_room as $key => $info) {
			$data_hotel = $info->hotel;
			if ($data_hotel->hot_pms_room_info != "") {
		      	$pms_room_info = json_decode($data_hotel->hot_pms_room_info, true);
		      	$data[$key]['pms_room_info'] = $pms_room_info;
		   		$data[$key]['pms_room_ids'] = array_keys($pms_room_info);
		   	}
		   	
			$data[$key]['hot_pms_link'] = $data_hotel->hot_pms_link;
			$data[$key]['hot_pms_active'] = $data_hotel->hot_pms_active;
			$data[$key]['rom_pms_room_id'] = $info->rom_pms_room_id;
			$data[$key]['room_id'] = $info->rom_id;
			$data[$key]['pms_active'] = $info->rom_id;
			$data[$key]['rom_active'] = $info->rom_active;
			$data[$key]['name'] = $info->rom_name;
			$data[$key]['picture'] = $info->rom_picture;
			$data[$key]['number_room'] = $info->rom_stock;
			$data[$key]['person']['adult'] = $info->rom_person;
			$data[$key]['person']['child'] = $info->rom_children;
			if($info->rom_info_bed != ''){
				$rom_info_bed = array_values(json_decode($info->rom_info_bed,true));
				foreach ($rom_info_bed as $key_bed => $bed) {
					if($bed > 0) $data[$key]['bed']['rom_info_bed'][] = $bed . ' ' . $params_bed[$key_bed];
				}
			}
			if($info->rom_exchange_bed != ''){
				$rom_exchange_bed = array_values(json_decode($info->rom_exchange_bed,true));
				
				if(array_sum($rom_exchange_bed) > 0){
					foreach ($rom_exchange_bed as $key_ex_bed => $ex_bed) {
						if($ex_bed > 0) $data[$key]['bed']['rom_exchange_bed'][] = $ex_bed . ' ' . $params_bed[$key_ex_bed];
					}
				}
			}
		}

		return View::make('components.modules.room.index', compact('data','id'));
	}

	public function getEdit($id)
	{
		$rooms = $this->rooms->find($id);
		
		$data_show = [];
		$data_show['room_name'] = $rooms->rom_name;
		$data_show['room_total'] = $rooms->rom_stock;
		$data_show['room_area'] = $rooms->rom_area;
		$data_show['room_adult'] = $rooms->rom_person;
		$data_show['room_child'] = $rooms->rom_children;
		$data_show['room_smoke'] = $rooms->rom_smoke;
		$room_bed_info = $rooms->rom_info_bed;
		if($room_bed_info == ''){
			$data_show['bed']['single-bed']   = 0;
			$data_show['bed']['double-bed']   = 0;
			$data_show['bed']['queen-bed']    = 0;
			$data_show['bed']['king-bed']     = 0;
			$data_show['bed']['division-bed'] = 0;
			$data_show['bed']['sofa-bed']     = 0;
		} else {
			$data_show['bed'] = json_decode($room_bed_info,true);
		}

		$room_bed_info = $rooms->rom_exchange_bed;
		if($room_bed_info == ''){
			$data_show['ex_bed']['ex-single-bed']   = 0;
			$data_show['ex_bed']['ex-double-bed']   = 0;
			$data_show['ex_bed']['ex-queen-bed']    = 0;
			$data_show['ex_bed']['ex-king-bed']     = 0;
			$data_show['ex_bed']['ex-division-bed'] = 0;
			$data_show['ex_bed']['ex-sofa-bed']     = 0;
		} else {
			$data_show['ex_bed'] = json_decode($room_bed_info,true);
		}

		$data_show['convenience'] = [];
		if($rooms->rom_conveniences != ''){
			$list_conavenience = json_decode($rooms->rom_conveniences,true);
			foreach ($list_conavenience as $conv) {
				$data_show['convenience'][] = $conv['con_id'];
			}	
		}

		$trend = $rooms->rom_trend;
		for ($i=0; $i < 7; $i++) { 
			if($trend == $i){
				$data_show['trend'][$i] = 'checked';
			}else {
				$data_show['trend'][$i] = '';
			}
		}

		$param_conv = ['field_select' => array('con_id', 'con_type', 'con_name'),
						'not_in_id'	  => array(70,83),
						'con_type' 	  => array(5)];
		$data_conveniences = $this->conveniences->getInfoConveniences($param_conv);
		// dd($data_conveniences);
		return View::make('components.modules.room.edit', compact('data_show', 'data_conveniences'));
	}

	public function postEdit(Request $request, $id) {
		$data_create = $request->all();
		$rooms = $this->rooms->find($id);

		$data_conveniences_old = [];
		if($rooms->rom_conveniences != ''){
			$data_conveniences_old = json_decode($rooms->rom_conveniences,true);
		}

		if($this->checkAddBed($request))
		{
			$this->roomValidator->pushCreateField(['bed_required' => 'required'])
						      	->pushCreateMsg(['bed_required.required' => 'Bạn chưa chọn số lượng giường.']);
		}
		
		$this->roomValidator->validateDataCreate($request);

		$array_info_bed = [];
		$array_info_bed['single-bed']   = $data_create['bed']['single-bed'];
		$array_info_bed['double-bed']   = $data_create['bed']['double-bed'];
		$array_info_bed['queen-bed']    = $data_create['bed']['queen-bed'];
		$array_info_bed['king-bed']     = $data_create['bed']['king-bed'];
		$array_info_bed['division-bed'] = $data_create['bed']['division-bed'];
		$array_info_bed['sofa-bed']     = $data_create['bed']['sofa-bed'];
		$room_info_bed = json_encode($array_info_bed);

		$array_exchange_bed = [];
		$array_exchange_bed['ex-single-bed']   = $data_create['ex-single-bed'];
		$array_exchange_bed['ex-double-bed']   = $data_create['ex-double-bed'];
		$array_exchange_bed['ex-queen-bed']    = $data_create['ex-queen-bed'];
		$array_exchange_bed['ex-king-bed']     = $data_create['ex-king-bed'];
		$array_exchange_bed['ex-division-bed'] = $data_create['ex-division-bed'];
		$array_exchange_bed['ex-sofa-bed']     = $data_create['ex-sofa-bed'];
		$room_exchange_bed = json_encode($array_exchange_bed);

		$rooms->rom_name         = $data_create['room_type'];
		$rooms->rom_stock        = $data_create['room_total'];
		$rooms->rom_area         = $data_create['room_area'];
		$rooms->rom_person       = $data_create['adult'];
		$rooms->rom_children     = $data_create['child'];
		$rooms->rom_smoke        = $data_create['room_smoke'];
		$rooms->rom_info_bed     = $room_info_bed;
		$rooms->rom_exchange_bed = $room_exchange_bed;
		$rooms->rom_trend        = $data_create['trend'];

		DB::enableQueryLog();

		$status = $rooms->save();

		$uri    = $request->path();
		$adm_id = $request->user()->id;
		$ip     = $request->ip();

		//save log edit room
		$params_update = array(
							   'id' 	   => $id,
							   'ip'		   => $ip,
							   'type'	   => 2,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

		$this->saveLogRoom($params_update);

		if(!empty($data_conveniences_old)){
			foreach ($data_conveniences_old as $con_id_old) {
				$dataDelete[] = $con_id_old['con_id'];
			}

			foreach ($data_create['convenience'] as $key => $con_id) {
				$dataInsert[] = intval($con_id);
			}

			$filter = array_diff($dataInsert, $dataDelete);
			$filter_inverse = array_diff($dataDelete, $dataInsert);
			$check_unique = count($filter) + count($filter_inverse);
			
			if($check_unique > 0)
			{
				$rooms->conveniences()->detach($dataDelete);

				$rooms->conveniences()->attach($dataInsert);
				$data_conveniences = $rooms->conveniences->toArray();
				$new_room_id = $rooms->rom_id;
				$array_convenience = [];
				foreach ($data_conveniences as $key => $value) {
					$array_convenience[$key] = $value;
					$array_convenience[$key]['roc_room_id'] = $new_room_id;
					unset($array_convenience[$key]['con_hot'],$array_convenience[$key]['con_active'],
						  $array_convenience[$key]['con_delete'],$array_convenience[$key]['con_order'],
						  $array_convenience[$key]['pivot']);
				}
				$info_convenience = json_encode($array_convenience);
				$rooms->rom_conveniences = $info_convenience;
				$rooms->save();
				
				//save log edit convenience
				$params_conv = array(
								     'id' 	   	 => $id,
								     'ip'		 => $ip,
								     'type'	     => 2,
								     'adm_id'	 => $adm_id,
								     'uri'	   	 => $uri
								     );

				$this->saveLogRoom($params_conv);
			}
			
		}

		if($status){
			return Redirect::to(route('room-list', array('id' => $rooms->rom_hotel)));
		}
	}

	public function deleteRoom(Request $request)
	{
		$roomID = Input::get('id_room');

		$rooms = $this->rooms->find($roomID);

		$rooms->rom_delete = 1;
		$rooms->rom_delete_time = time();
		$rooms->rom_delete_user_id = Auth::id();

		DB::enableQueryLog();

		$rooms->save();

		$uri    = $request->path();
		$adm_id = $request->user()->id;
		$ip     = $request->ip();

		$params_update = array(
							   'id' 	   => $roomID,
							   'ip'		   => $ip,
							   'type'	   => 2,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

		$this->saveLogRoom($params_update);
		
		// return redirect()->back();
	}

	public function activeRoom(Request $request)
	{
		$roomID = Input::get('id_room');

		$rooms = $this->rooms->find($roomID);

		$rooms->rom_active = abs($rooms->rom_active - 1);

		DB::enableQueryLog();
		
		$rooms->save();

		$uri    = $request->path();
		$adm_id = $request->user()->id;
		$ip     = $request->ip();

		$params_update = array(
							   'id' 	   => $roomID,
							   'ip'		   => $ip,
							   'type'	   => 2,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

		$this->saveLogRoom($params_update);
	}

	public function saveRoomPms(Request $request)
	{
		$roomID = Input::get('id_room');
		$roomPmsID = Input::get('id_room_pms');

		$rooms = $this->rooms->find($roomID);

		$rooms->rom_pms_room_id = $roomPmsID;

		DB::enableQueryLog();

		$rooms->save();

		$uri    = $request->path();
		$adm_id = $request->user()->id;
		$ip     = $request->ip();

		$params_update = array(
							   'id' 	   => $roomID,
							   'ip'		   => $ip,
							   'type'	   => 2,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

		$this->saveLogRoom($params_update);
	}

	public function deleteRoomPms(Request $request)
	{
		$roomID = Input::get('id_room');

		$rooms = $this->rooms->find($roomID);

		$rooms->rom_pms_room_id = 0;

		DB::enableQueryLog();

		$rooms->save();

		$uri    = $request->path();
		$adm_id = $request->user()->id;
		$ip     = $request->ip();

		$params_update = array(
							   'id' 	   => $roomID,
							   'ip'		   => $ip,
							   'type'	   => 2,
							   'adm_id'	   => $adm_id,
							   'uri'	   => $uri
							   );

		$this->saveLogRoom($params_update);
	}

	private function saveLogRoom($params)
	{
		$id        = $params['id'];
		$ip        = $params['ip'];
		$type      = $params['type'];
		$adm_id    = $params['adm_id'];
		$uri       = $params['uri'];

		$query = showQueryExecute();

		if($query != '')
		{

			$data_room = DB::table('rooms')->where('rom_id', $id)->get();

			foreach ($data_room[0] as $field => $values) {
	            $data[$field] = base64_encode($values);
	        }

			$this->adminLog->saveLog($data, $ip, $adm_id, 14, 'rooms', $type , $query, $uri, 'rom_id', $id);
		}
	}
}
