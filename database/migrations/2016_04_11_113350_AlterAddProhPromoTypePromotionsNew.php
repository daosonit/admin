<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddProhPromoTypePromotionsNew extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('promotions_new', function(Blueprint $table)
		{
    		$table->boolean('proh_promo_type')->default(0)->comment('Kiểu KM OTA hay TA');
    		$table->integer('proh_promo_id_ota')->default(0)->comment('ID Promo OTA cu');
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
			$table->dropColumn('proh_promo_type');
			$table->dropColumn('proh_promo_id_ota');
		});
	}

}
