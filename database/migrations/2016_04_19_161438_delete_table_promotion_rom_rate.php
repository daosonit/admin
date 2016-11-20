<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteTablePromotionRomRate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::drop('promotions_hms_rate');
		Schema::drop('promotions_new_rate');
		Schema::drop('promotion_room_rate');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::create('promotions_hms_rate', function(Blueprint $table)
		{
			$table->increments('phr_id')->index();
			$table->integer('phr_promotion_id')->unsigned()->comment('id promotion');
			$table->integer('phr_rate_plan_id')->unsigned()->comment('id don gia');
			$table->unique(['phr_promotion_id', 'phr_rate_plan_id'], 'phr_unique_index');
		});

		Schema::create('promotions_new_rate', function(Blueprint $table)
		{
			$table->increments('pra_id')->index();
			$table->integer('pra_promotion_id')->unsigned()->comment('id promotion');
			$table->integer('pra_rate_plan_id')->unsigned()->comment('id don gia');
			$table->unique(['pra_promotion_id', 'pra_rate_plan_id'], 'pra_unique_index');
		});

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

}
