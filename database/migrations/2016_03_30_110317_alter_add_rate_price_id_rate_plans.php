<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddRatePriceIdRatePlans extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rate_plans', function(Blueprint $table)
		{
			$table->integer('rap_price_id')->after('rap_room_apply_id')->comment('id don gia lay gia lam moc tinh gia cho don gia');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('rate_plans', function(Blueprint $table)
		{
			$table->dropColumn('rap_price_id');
		});
	}

}
