<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRapPriceIdRatePlans extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rate_plans', function ($table) {
		    $table->renameColumn('rap_price_id', 'rap_parent_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('rate_plans', function ($table) {
		    $table->renameColumn('rap_parent_id', 'rap_price_id');
		});
	}

}
