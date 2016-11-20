<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionHmsRate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('promotions_hms_rate', function(Blueprint $table)
		{
			$table->increments('phr_id')->index();
			$table->integer('phr_promotion_id')->unsigned()->comment('id promotion');
			$table->integer('phr_rate_plan_id')->unsigned()->comment('id don gia');
			$table->unique(['phr_promotion_id', 'phr_rate_plan_id'], 'phr_unique_index');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('promotions_hms_rate');
	}

}
