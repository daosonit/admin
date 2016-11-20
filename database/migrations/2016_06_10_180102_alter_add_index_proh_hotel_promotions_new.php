<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddIndexProhHotelPromotionsNew extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('promotions_new', function ($table) {
		    $table->index(['proh_hotel']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('promotions_new', function ($table) {
		    $table->dropIndex(['proh_hotel']); // Drops index 'geo_state_index'
		});
	}

}
