<?php namespace App\Http\Controllers\Voucher;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Excel, View, Validator, Response, Log;
use Carbon\Carbon;

use App\Models\Components\Voucher;
use App\Models\Components\VoucherCode;
use App\Models\Components\VoucherAdvanceSetting;
use App\Models\Components\VoucherDiscountInfo;
use App\Models\Components\Hotel;
use App\Models\Components\City;
use App\Mytour\Validators\VoucherValidator;
use App\Mytour\Excels\Imports\VoucherCodeImport;

use App\Mytour\Repositories\Voucher\FindVoucherByName;
use App\Http\Controllers\Voucher\VoucherLogicProcessor;

class VoucherController extends Controller {

	use VoucherLogicProcessor;
	//

	public function __construct(Request $request, Voucher $voucher, VoucherCode $voucherCode, 
								VoucherAdvanceSetting $voucherAdvanceSetting, 
								VoucherDiscountInfo $voucherDiscountInfo,
								VoucherValidator $voucherValidator)
	{
		
		$this->voucher 	   			 = $voucher;
		$this->voucherCode 			 = $voucherCode;
		$this->voucherAdvanceSetting = $voucherAdvanceSetting;
		$this->voucherDiscountInfo 	 = $voucherDiscountInfo;
		$this->voucherVali 			 = $voucherValidator;
	}




	public function showList(Request $request)
	{

		
		//Check passed if user can voucher.showlist do
		$this->userCan('voucher.showlist');
		$voucherQuery = $this->voucher->select('*');

		if($name = $request->get('name', '')) 
			$voucherQuery->searchByName($name);

		if($id = $request->get('id', '')) 
			$voucherQuery->where('id', $id);

		if($code = $request->get('code', '')){
			$voucherCode = $this->voucherCode->findByCode($code);
			if($voucherCode){
				$voucherQuery->where('id', $voucherCode->voucher->id); 
			} 
		}

		$voucherData = $voucherQuery->paginate(20);

		//Eager Load Releated Model
		$voucherData->load('voucherCodes');
		
		return View::make('components.modules.voucher.index', compact('voucherData'));
	}


	/**
	 * Lấy form tạo mới 1 voucher
	 * @param Illuminate\Http\Request $request
	 */
	public function getCreate(Request $request)
	{
		return View::make('components.modules.voucher.create');
	}



	/**
	 * Lấy danh sách code của voucher theo voucher id
	 * @param Illuminate\Http\Request $request
	 * @param int $id : id cua voucher
	 */
	public function getCodes(Request $request, $id)
	{


		$voucher = $this->voucher->findOrFail($id);
		$voucherCodeQuery = $voucher->voucherCodes();
		if($code = $request->get('code', '')){
			$voucherCodeQuery->searchByCode($code);
		}
		$voucherCodes = $voucherCodeQuery->paginate(NUM_PER_PAGE);
		$stt = 0;
		$voucherCodes->each(function($item) use(&$stt){
			$item->stt = ++$stt;
		});

		return view('components.modules.voucher.show-codes')->with('voucherCodes', $voucherCodes);
	}



	/**
	 * Xóa voucher code 
	 * @param int $id : id cua voucher
	 */
	public function deleteCode($id)
	{
		$this->userCan('voucher.delete');
		try {
			$voucherCode = $this->voucherCode->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return Response::make('VoucherCode not found!', 404);
		}
		$voucherCode->delete();
		return redirect()->back();

	}



	/**
	 * Post form tạo mới 1 voucher 
	 * @param Illuminate\Http\Request $request
	 */
	public function postCreate(Request $request)
	{
		//Check passed if user can voucher.showlist do
		$this->userCan('voucher.create');

		$this->customValidate($request);

		$insertData = $this->getDataPost($request);
		$voucher 	= $this->voucher->create($insertData);

		$voucher->save();

		//Insert Voucher Code
		if ($this->isSingleCode($request)) {
			$voucherCode = $this->voucherCode;

			$voucherCode->code = $request->get('code');

			$voucher->voucherCodes()->save($voucherCode);
		}

		if ($this->isMultiCode($request)) {
			// Read excel file and insert into voucher code
			if ($request->hasFile('code_list')) {
				$codeListFile = $request->file('code_list');

				//lấy ra mảng code từ file excel được user upload
				$excel = $this->getListCodeByExcelFile($codeListFile);
				$codeList = collect($excel);

				// Loại bỏ những mã bị trùng
				$codeList->unique();

				if(!$codeList->isEmpty()){

					$codes = [];

					//Remove existed codes in database form $CodeList
					$codeList = $this->removeDuplicateCode($codeList);

					//End remove duplicate code
					foreach($codeList as $code){
						$codes[] = new VoucherCode($code);
					}
					//Insert Danh sách code 
					$voucher->voucherCodes()->saveMany($codes);
				}
			}
		}

		// Insert Discount Info
		$voucherDiscountInfo = $this->voucherDiscountInfo;

		$voucherDiscountInfo->fill($request->all());

		$voucher->voucherDiscountInfo()->save($voucherDiscountInfo);

		if ($voucher->advance_setting) {
			// Insert advance setting
			$adv_setting = $this->voucherAdvanceSetting;

			$adv_setting->fill($insertData);
			$voucher->voucherAdvanceSetting()->save($adv_setting);
			
		}
		
		return redirect()->route('voucher-list');
	}



	private function getDataPost(Request $request)
	{
		$data 				 		   = $request->all();
		$data['hotel_accepted_apply']  = collect(explode(',', $request->get('hotel_allow', '')));
		$data['hotel_city_apply'] 	   = collect(explode(',', $request->get('hotel_city_allow', '')));
		$data['hotel_star_rate_apply'] = collect($request->get('hotel_star_rate_apply', []));
		$data['customer_logged_in']    = $request->get('customer_logged_in', 0);
		$data['advance_setting']       = $request->get('advance_setting', 0);
		$data['time_checkin_apply']    = $request->get('time_checkin_apply', 0);
		return $data;
	}





	/**
	 * Lấy ra form sửa của voucher
	 * @param Illuminate\Http\Request $request
	 * @param App\Models\Components\Hotel $hotel
	 * @param App\Models\Components\City $city
	 * @param int $id : id cua voucher
	 */
	public function getEdit(Request $request, Hotel $hotel, City $city, $id)
	{
		
		try {
			$voucher = $this->voucher->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return Response::make('Voucher not found!', 404);
		}

		$hotels = json_encode([]);
		$cities = json_encode([]);

		$hotelIDs = $voucher->voucherAdvanceSetting ? $voucher->voucherAdvanceSetting->hotel_accepted_apply->toArray() : [];
		$cityIDs = $voucher->voucherAdvanceSetting ? $voucher->voucherAdvanceSetting->hotel_city_apply->toArray() : [];

		if(!empty($hotelIDs)){
			$hotels = $hotel->select(['hot_id as id', 'hot_name_temp as name'])
					   ->active()->idInList($hotelIDs)
					   ->get()->toJson();
		}
		
		if(!empty($cityIDs)){
			$cities = $city->select(['cou_id as id', 'cou_name as name'])
						   ->active()->idInList($cityIDs)
						   ->get()->toJson();
		}

		return View::make('components.modules.voucher.edit', compact('voucher', 'cities', 'hotels'));
	}



	public function postEdit(Request $request, $id)
	{
		//Check passed if user can voucher.showlist do
		$this->userCan('voucher.update');

		try {
			$voucher = $this->voucher->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return Response::make('Voucher not found!', 404);
		}
		
		$this->customValidate($request);

		$insertData = $this->getDataPost($request);
		$voucher->fill($insertData);

		$voucher->save();

		//Insert Voucher Code
		if ($this->isSingleCode($request)) {
			$voucherCode = $this->voucherCode;

			$voucherCode->code = $request->get('code');

			$voucher->voucherCodes()->delete();
			$voucher->voucherCodes()->save($voucherCode);
		}

		if ($this->isMultiCode($request)) {
			// Read excel file and insert into voucher code
			if ($request->hasFile('code_list')) {
				$codeListFile = $request->file('code_list');

				//lấy ra mảng code từ file excel được user upload
				$excel = $this->getListCodeByExcelFile($codeListFile);
				$codeList = collect($excel);

				// Loại bỏ những mã bị trùng
				$codeList->unique();

				if(!$codeList->isEmpty()){

					$codes = [];

					//Remove existed codes in database form $CodeList
					$codeList = $this->removeDuplicateCode($codeList);

					//End remove duplicate code
					foreach($codeList as $code){
						$codes[] = new VoucherCode($code);
					}

					if(!empty($codes)){
						// Xóa hết các code cũ
						$voucher->voucherCodes()->delete();

						//Insert Danh sách code 
						$voucher->voucherCodes()->saveMany($codes);
					}
				}
			}
		}
		// Insert Discount Info

		$voucher->voucherDiscountInfo->fill($request->all());

		$voucher->voucherDiscountInfo->save();

		if ($voucher->advance_setting) {
			// Insert advance setting
			if($voucher->voucherAdvanceSetting){
				$voucher->voucherAdvanceSetting->fill($insertData);
				$voucher->voucherAdvanceSetting->save();
			} else {
				$advSetting = $this->voucherAdvanceSetting;
				$advSetting->fill($insertData);
				$voucher->voucherAdvanceSetting()->save($advSetting);
			}
		}


		return redirect()->route('voucher-list');
	}


	/**
	 * Xóa voucher
	 * @param Illuminate\Http\Request $request
	 * @param integer $id : id voucher
	 */
	public function delete(Request $request, $id)
	{
		//Check passed if user can voucher.showlist do
		$this->userCan('voucher.delete');

		try{
			$voucher = $this->voucher->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return Response::make('Voucher not found!', 404);
		}
		$voucher->delete();
		return redirect()->back();
	}


	private function customValidate(Request $request)
	{
		if ($this->isSingleCode($request)) {
			$this->voucherVali->pushCreateField(['code' => 'required|unique:voucher_codes'])
						      ->pushCreateMsg(['code.required' => 'Bạn chưa nhập mã CODE', 
												'code.unique' => 'CODE bạn vừa nhập đã tồn tại!']);
		}
		if ($this->isMultiCode($request)) {
			$this->voucherVali->pushCreateField(['code_list' => 'required'])
							  ->pushCreateMsg(['code_list.required' => 'Bạn chưa nhập file danh sách CODE']);
		}

		if ($this->isDiscountMoney($request)) {
			$this->voucherVali->pushCreateField(['money' => 'required|min:1'])
							  ->pushCreateMsg(['money.required' => 'Bạn chưa nhập giá trị tiền giảm trừ', 
												'money.min' => 'Giá trị giảm trừ không hợp lệ!']);
		} elseif ($this->isDiscountGift($request)) {
			$this->voucherVali->pushCreateField(['gift_info' => 'required'])
							  ->pushCreateMsg(['gift_info.required' => 'Bạn chưa nhập thông tin quà tặng.']);
		} elseif ($this->isDiscountVpoint($request)) {
			$this->voucherVali->pushCreateField(['vpoint' => 'required'])
							  ->pushCreateMsg(['vpoint.required' => 'Bạn chưa nhập giá trị vpoint.']);
		}
		if ($request->route()->getName() == 'voucher-create'){
			$this->voucherVali->validateDataCreate($request);	
		}
		if ($request->route()->getName() == 'voucher-edit'){
			$this->voucherVali->validateDataEdit($request);	
		}
	}

}
