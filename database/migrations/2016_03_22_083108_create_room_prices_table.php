<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomPricesTable extends Migration {

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
            Schema::create($table_name, function(Blueprint $table)
			{
				$table->increments('rop_id')->index();
				$table->integer('rop_hotel_id')->comment('id khách sạn');
				$table->integer('rop_room_id')->index()->comment('id phòng');
                $table->integer('rop_rate_plan_id')->index()->unsigned()->comment('id đơn giá');
				$table->tinyInteger('rop_type_price')->index()->default(-1)->comment('Kieu gia OTA = 1 hay TA = 0');
				$table->text('rop_info_price_publish')->default("")->comment('mảng thông tin giá công bố');
				$table->text('rop_info_price_contract')->default("")->comment('mảng thông tin giá trả khách sạn');
				$table->text('rop_info_price_season_contract')->default("")->comment('');
				for ($i = 1; $i <= 31; $i++) {
					$col = 'rop_col' . $i;
					$table->double($col)->comment('thông tin gia theo ngay');
				}
                $table->foreign('rop_rate_plan_id')
                      ->references('rap_id')->on('rate_plans')
                      ->onDelete('cascade');
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
            Schema::drop($table_name);
        }
	}

}
