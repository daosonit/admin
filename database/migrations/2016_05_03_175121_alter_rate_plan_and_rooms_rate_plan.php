<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRatePlanAndRoomsRatePlan extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rate_plans', function ($table) {
		    $table->dropColumn('rap_hidden_price');
		    $table->dropColumn('rap_price_email');
		});
		Schema::table('rooms_rate_plans', function ($table) {
			$table->tinyInteger('rrp_hidden_price')->default(0)->comment('an gia bat buoc, 1 la an gia');
			$table->tinyInteger('rrp_price_email')->default(0)->comment('bao gia qua email');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('rooms_rate_plans', function ($table) {
		    $table->dropColumn('rrp_hidden_price');
		    $table->dropColumn('rrp_price_email');
		});
		Schema::table('rate_plans', function ($table) {
			$table->tinyInteger('rap_hidden_price')->default(0)->comment('an gia bat buoc, 1 la an gia');
			$table->tinyInteger('rap_price_email')->default(0)->comment('bao gia qua email');
		});
	}

}
