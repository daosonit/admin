<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddProhDayApplyPromotion extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('promotions_new', function(Blueprint $table)
		{
			$table->text('proh_day_apply')->after('proh_day_deny')->comment('Mang ngay cu the trong tuan ap dung KM');
		});

		Schema::table('promotions_hms', function(Blueprint $table)
		{
			$table->text('proh_day_apply')->after('proh_day_deny')->comment('Mang ngay cu the trong tuan ap dung KM');
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
			$table->dropColumn('proh_day_apply');
		});

		Schema::table('promotions_hms', function(Blueprint $table)
		{
			$table->dropColumn('proh_day_apply');
		});
	}

}
