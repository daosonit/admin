<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnAcompaniedService extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rate_plans', function(Blueprint $table)
		{			
    		$table->text('rap_accompanied_service')->change();
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
			$table->integer('rap_accompanied_service')->default(0)->change();
		});
	}

}
