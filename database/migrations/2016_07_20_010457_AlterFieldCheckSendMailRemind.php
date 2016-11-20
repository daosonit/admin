<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFieldCheckSendMailRemind extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('hotels', function ($table) {
			if(!Schema::hasColumn('hotels', 'hot_count_remind') && !Schema::hasColumn('hotels', 'hot_time_remind')){
			    $table->tinyInteger('hot_count_remind')->default(0)->comment('Số lần gửi mail nhắc khách sạn');
			    $table->Integer('hot_time_remind')->default(0)->comment('Thời gian gửi mail nhắc khách sạn');
			}
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
			$table->dropColumn('hot_count_remind');
			$table->dropColumn('hot_time_remind');
		});
	}

}
