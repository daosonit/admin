<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddRoaTotalAllotmentPms extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$year        = 2014;//Năm đã có
        $month       = 0;//Tháng đã có
        $year_finish = 2020;//12/2020

        //Số bảng cần
        $num_table = (($year_finish + 1) - $year) * 12 - $month;

        for ($i = 1; $i <= $num_table; $i++) {
            if ($month == 12) {
                $month = 1;
                $year++;
            } else {
                $month++;
            }
            if ($month < 10) $month = '0' . $month;
            $table_name = 'rooms_allotment_' . $year . $month;
            Schema::table($table_name, function(Blueprint $table) use ($table_name)
			{
                if(!Schema::hasColumn($table_name, 'roa_total_allotment_pms')){
    				$table->tinyInteger('roa_total_allotment_pms')->default(0)->comment('tong so allotment pms');
                }
			});
        }
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$year        = 2014;//Năm đã có
        $month       = 0;//Tháng đã có
        $year_finish = 2020;//12/2020

        //Số bảng cần
        $num_table = (($year_finish + 1) - $year) * 12 - $month;

        for ($i = 1; $i <= $num_table; $i++) {
            if ($month == 12) {
                $month = 1;
                $year++;
            } else {
                $month++;
            }
            if ($month < 10) $month = '0' . $month;
            $table_name = 'rooms_allotment_' . $year . $month;
            Schema::table($table_name, function(Blueprint $table) use ($table_name)
			{
                if(Schema::hasColumn($table_name, 'roa_total_allotment_pms')){
				    $table->dropColumn('roa_total_allotment_pms');
                }
			});
        }
	}

}
