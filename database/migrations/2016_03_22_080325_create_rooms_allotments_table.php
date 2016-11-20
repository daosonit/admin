<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsAllotmentsTable extends Migration {

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

        //Số bảng cần tạo
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
            Schema::create($table_name, function(Blueprint $table)
			{
                $table->engine = 'MyISAM';
				$table->increments('roa_id')->index();
				$table->integer('roa_hotel_id')->index()->comment('id khách sạn');
				$table->integer('roa_room_id')->index()->comment('id phòng');
				$table->text('roa_info_allotment_ota')->default("")->comment('thông tin allotment ota');
				$table->text('roa_info_allotment_ta')->default("")->comment('thông tin allotment ta');
				$table->text('roa_status')->default("")->comment('trạng thái đóng/mở phòng');
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

        //Số bảng cần tạo
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
            Schema::drop($table_name);
        }
	}

}
