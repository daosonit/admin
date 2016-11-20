<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddProhMaxNightPromotionsHms extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('promotions_hms', function(Blueprint $table)
		{
    		$table->tinyInteger('proh_max_night')->after('proh_min_night')->default(0)->comment('So dem toi da ap dung KM');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('promotions_hms', function(Blueprint $table)
		{
			$table->dropColumn('proh_max_night');
		});
	}

}
