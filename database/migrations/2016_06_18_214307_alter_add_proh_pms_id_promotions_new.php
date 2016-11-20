<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddProhPmsIdPromotionsNew extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('promotions_new', function(Blueprint $table)
		{
    		$table->integer('proh_pms_id')->after('proh_hotel')->default(0)->comment('ID KM pms');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('promotions_new', function(Blueprint $table)
		{
			$table->dropColumn('proh_pms_id');
		});
	}

}
