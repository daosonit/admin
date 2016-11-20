<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddHotCommissionMarkup extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('hotels', function ($table) {
			$table->tinyInteger('hot_commisson_ota')->default(0)->comment('Phan tram commit cua phong KS, ap dung tinh gia cho KS OTA');
			$table->tinyInteger('hot_mark_up')->default(0)->comment('% Mark-up để tính giá ban ra TA');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('hotels', function ($table) {
		    $table->dropColumn(['hot_commisson_ota', 'hot_mark_up']);
		});
	}

}
