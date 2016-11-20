<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration {


	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('departments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')->comment('Tên phòng ban - bộ phận');
			$table->text('description')->comment('Mô tả');
			$table->timestamps();
		});

		$departments = [
			['name' => 'IT', 'description' => 'Description'],
			['name' => 'Accountant', 'description' => 'Description'],
			['name' => 'Marketing', 'description' => 'Description'],
			['name' => 'Customer Service', 'description' => 'Description'],
			['name' => 'Planning', 'description' => 'Description'],
			['name' => 'HR', 'description' => 'Description'],
			['name' => 'Sales', 'description' => 'Description'],
			['name' => 'COS', 'description' => 'Description'],
			['name' => 'Content', 'description' => 'Description'],
		];	

		DB::table('departments')->insert($departments);

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('departments');
	}

}
