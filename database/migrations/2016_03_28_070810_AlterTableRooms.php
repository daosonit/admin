<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRooms extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rooms', function(Blueprint $table)
		{
			$table->tinyInteger('rom_smoke')->default(0)->comment('phòng có/không hút thuốc');
			$table->tinyInteger('rom_trend')->default(0)->comment('hướng phòng');
			$table->text('rom_info_bed')->default("")->comment('mảng thông tin giường');
			$table->text('rom_exchange_bed')->default("")->comment('mảng thông tin giường thay thế');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('rooms', function(Blueprint $table)
		{
			$table->dropColumn('rom_smoke');
			$table->dropColumn('rom_trend');
			$table->dropColumn('rom_info_bed');
			$table->dropColumn('rom_exchange_bed');
		});
	}

}
