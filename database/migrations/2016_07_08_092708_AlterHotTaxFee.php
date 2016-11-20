<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterHotTaxFee extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('hotels', function ($table) {
			if(!Schema::hasColumn('hotels', 'hot_tax_fee')){
		    	$table->boolean('hot_tax_fee')->default(1)->comment('Giá đã bao gồm thuế và dịch vụ');
			}
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
			$table->dropColumn('hot_tax_fee');
		});
	}

}
