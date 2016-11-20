<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RoomsRatePlan extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rooms_rate_plans', function(Blueprint $table)
		{
			$table->increments('rrp_id')->index();
			$table->integer('rrp_room_id')->comment('id phòng');
			$table->integer('rrp_rate_plan_id')->unsigned()->comment('id đơn giá');
			$table->unique(array('rrp_room_id', 'rrp_rate_plan_id'));
			$table->foreign('rrp_rate_plan_id')
			      ->references('rap_id')->on('rate_plans')
				  ->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rooms_rate_plans');
	}

}
