<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCreatTablePromotionRoomRate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('promotion_room_rate', function(Blueprint $table)
		{
			$table->increments('proa_id')->index();
			$table->integer('proa_promotion_id')->unsigned()->comment('id promotion');
			$table->integer('proa_room_id')->unsigned()->comment('id don gia');
			$table->integer('proa_rate_plan_id')->unsigned()->comment('id don gia');
			$table->integer('proa_promo_type')->default(0)->comment('Kieu KM 0(TA), 1(OTA)');
			$table->unique(['proa_promotion_id', 'proa_room_id', 'proa_rate_plan_id'], 'proa_unique_index');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('promotion_room_rate');
	}

}
