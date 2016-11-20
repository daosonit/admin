<?php namespace App\Http\Controllers\Statistics;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use DB, Excel, Carbon\Carbon;
use MytourStatic;
class MoneyStatisticController extends Controller {

	public function __construct()
	{
		//check Role of User
		$this->userHasRole('mytour.staff');

		$this->bookingModules = MytourStatic::bookingModules();
	}




	public function getIndex()
	{
		
	}



	public function exportMoney(Request $request)
	{

		$module = $request->get('module', HOTEL_MODULE);

		dd($module);

		$fileName = 'MytourMoneyExport' . Carbon::now()->toDateTimeString();

		Excel::create($fileName, function($excel){

			$hotelModuleInfo = $this->bookingModules->get(HOTEL_MODULE);

			$table = array_get($hotelModuleInfo, 'table', 'booking_hotel');
			$selectFields = ['boo_id', 'boo_code', 'boo_customer_name', 'boo_customer_email',
							 'boo_total_money', 'boo_supplier_money' ];

			$moneyData = collect(DB::table($table)->select($selectFields)
										  ->get());
			$moneyData = $moneyData->map(function($moneyReceive){

				return $moneyReceive = (array)$moneyReceive;

			})->keyBy(function($moneyReceive){

				return $moneyReceive['boo_id'];

			});

			

			$excel->sheet($table, function($sheet) use($moneyData){

				$sheet->fromArray($moneyData);
				$columnTitles = ['A1' => 'ID', 'B1' => 'CODE', 'C1' => 'CUSTOMER NAME', 
								 'D1' => 'CUSTOMER EMAIL', 
				 				 'E1' => 'TOTAL MONEY', 'F1' => 'SUPPLIER MONEY'
				];
				$sheet->row(1, $columnTitles);

			})->download('xlsx');
		});

	}



	public function getExportMoney()
	{
		dd(MytourStatic::bookingModules()->map(function($module, $key){
			return $key;
		}, false));

		return view('components.statistics.money-statistic.export-money');
	}


}
