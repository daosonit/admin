<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionNewRate extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('promotions_new_rate', function(Blueprint $table)
		{
			$table->increments('pra_id')->index();
			$table->integer('pra_promotion_id')->unsigned()->comment('id promotion');
			$table->integer('pra_rate_plan_id')->unsigned()->comment('id don gia');
			$table->unique(['pra_promotion_id', 'pra_rate_plan_id'], 'pra_unique_index');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('promotions_new_rate');
	}

}
