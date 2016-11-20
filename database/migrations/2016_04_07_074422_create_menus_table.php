<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('menu_group_id')->unsigned();
			$table->string('name');
			$table->string('visible_on');
			$table->tinyInteger('order')->nullable();
			$table->boolean('active')->default('0');
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('menu_group_id')
				  ->references('id')
				  ->on('menu_groups');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('menus');
	}

}
