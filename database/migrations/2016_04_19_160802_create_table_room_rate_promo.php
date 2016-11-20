<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRoomRatePromo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('room_rate_promotion', function(Blueprint $table)
		{
			$table->increments('rapr_id')->index();
			$table->integer('rapr_promotion_id')->comment('id khuyen mai')->unsigned();
			$table->integer('rapr_room_rate_id')->comment('id room rate');
			$table->text('rapr_price_promo_info')->comment('Thong tin gia KM TA');
			$table->unique(array('rapr_promotion_id', 'rapr_room_rate_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('room_rate_promotion');
	}

}
