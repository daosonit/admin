<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepartmentIdOnAdminsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('admins', function($table){
			$table->integer('department_id')->unsigned()->default('0');
		});
		DB::table('admins')->where('id', 1)->update(['department_id' => 1]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('admins', function($table){
			$table->dropColumn('department_id');
		});
	}

}
