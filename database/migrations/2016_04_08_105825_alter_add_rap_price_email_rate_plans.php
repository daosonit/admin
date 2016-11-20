<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddRapPriceEmailRatePlans extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rate_plans', function(Blueprint $table)
		{
    		$table->boolean('rap_price_email')->after('rap_hidden_price')->default(0)->comment('Check bao gia qua email');
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
			$table->dropColumn('rap_price_email');
		});
	}

}
