<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRoomApplyBed extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rate_plans', function ($table) {
		    $table->text('rap_room_apply_bed')->comment('json id phòng áp dụng phụ thu giường phụ');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('rate_plans', function ($table) {
			$table->dropColumn('rap_room_apply_bed');
		});
	}

}
