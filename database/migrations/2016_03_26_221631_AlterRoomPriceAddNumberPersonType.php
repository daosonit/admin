<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRoomPriceAddNumberPersonType extends Migration {

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
            $table_name = 'room_price_' . $year . $month;
            Schema::table($table_name, function(Blueprint $table)
			{
				$table->tinyInteger('rop_person_type')->after('rop_rate_plan_id')->default(0)->comment('kieu gia so nguoi of phong (min or max)');
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
            $table_name = 'room_price_' . $year . $month;
            Schema::table($table_name, function(Blueprint $table)
			{
				$table->dropColumn('rop_person_type');
			});
        }
	}

}
