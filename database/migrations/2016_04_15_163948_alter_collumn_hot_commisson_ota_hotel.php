<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCollumnHotCommissonOtaHotel extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('hotels', function ($table) {
		    $table->renameColumn('hot_commisson_ota', 'hot_commission_ota');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('hotels', function ($table) {
		    $table->renameColumn('hot_commission_ota', 'hot_commisson_ota');
		});
	}

}
