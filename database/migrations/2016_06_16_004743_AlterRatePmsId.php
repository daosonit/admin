<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRatePmsId extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rate_plans', function(Blueprint $table)
		{
    		$table->integer('rap_pms_id')->after('rap_id')->default(0)->comment('ID đơn giá pms');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('rate_plans', function(Blueprint $table)
		{
			$table->dropColumn('rap_pms_id');
		});
	}

}
