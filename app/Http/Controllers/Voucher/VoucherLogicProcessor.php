<?php namespace App\Http\Controllers\Voucher;

use App\Mytour\Classes\VoucherSystem;
use Illuminate\Http\Request;
use Excel;

trait VoucherLogicProcessor {



	private function isSingleCode(Request $request)
	{
		return ($request->get('type') == VoucherSystem::SINGLE_CODE);
	}



	private function isDiscountMoney(Request $request){
		return ($request->get('discount_type') == VoucherSystem::DISCOUNT_MONEY_TYPE);
	}


	private function isDiscountGift(Request $request){
		return ($request->get('discount_type') == VoucherSystem::DISCOUNT_GIFT_TYPE);
	}


	private function isDiscountVpoint(Request $request){
		return ($request->get('discount_type') == VoucherSystem::DISCOUNT_VPOINT_TYPE);
	}



	private function isMultiCode(Request $request)
	{
		return ($request->get('type') == VoucherSystem::MULTI_CODE);		
	}




	private function getListCodeByExcelFile($file)
	{
		$arrReturn = [];
		$sheets = Excel::load($file)->get();
		if($sheets->count()){
			$firstSheet = $sheets->first();
			// dd($firstSheet);
			if($firstSheet->count()){
				foreach($firstSheet as $row){
					$arrReturn[] = $row->first();	
				}
			}
		}
		return $arrReturn;
	}


	private function removeDuplicateCode($codeList)
	{
		
		$duplicateCodes = $this->voucherCode->select('code')->whereIn('code', $codeList->toArray())->get();
		if(!$duplicateCodes->isEmpty()){
			$duplicateCodeArr = [];
			foreach($duplicateCodes as $code){
				$duplicateCodeArr[] = $code->code;
			}
			$duplicateCodes = collect($duplicateCodeArr);
			// dd($duplicateCodes);
			$codeList = $codeList->reject(function ($value) use ($duplicateCodes) {
				return $duplicateCodes->contains($value);
			});
		}

		return $codeList;
	}



}