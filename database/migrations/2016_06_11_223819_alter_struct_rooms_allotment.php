<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStructRoomsAllotment extends Migration {

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
            Schema::table($table_name, function(Blueprint $table)
			{
				$table->dropColumn(['roa_info_allotment_ota', 'roa_info_allotment_ta']);
				$table->tinyInteger('roa_allotment_ota')->index()->default(0)->comment('so luong allotment ota');
				$table->tinyInteger('roa_allotment_ta')->index()->default(0)->comment('so luong allotment ta');
				$table->boolean('roa_check_allotment_pms')->default(0)->comment('check allotment pms');
				$table->integer('roa_time')->index()->default(0)->comment('thoi gian');
				$table->boolean('roa_status')->default(1)->comment('trạng thái đóng/mở phòng')->change();
				$table->unique(['roa_room_id', 'roa_time'], 'roa_unique_index');
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
            Schema::table($table_name, function(Blueprint $table)
			{
				$table->dropUnique('roa_unique_index');
				$table->dropColumn(['roa_allotment_ota', 'roa_allotment_ta', 'roa_check_allotment_pms', 'roa_time']);
				$table->text('roa_info_allotment_ota')->default("")->comment('thong tin allotment ota');
				$table->text('roa_info_allotment_ta')->default("")->comment('thong tin allotment ta');
				$table->text('roa_status')->default("")->comment('trạng thái đóng/mở phòng')->change();
			});
        }
	}

}
