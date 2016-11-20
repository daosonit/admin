<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('voucher_codes', function(Blueprint $table)
		{
			$table->increments('id')->comment('Khóa chính');
			$table->integer('voucher_id')->unsigned()->comment('Foreign key tham chiếu đến trường id của bảng vouchers');
			$table->string('code')->unique()->comment('lưu mã voucher - unique!');
			$table->timestamp('used_at')->comment('Thời gian mà mã voucher đc sử dụng!');
			// Add constraint
			$table->timestamps();
			$table->foreign('voucher_id')
				  ->references('id')
				  ->on('vouchers')
				  ->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('voucher_codes');
	}

}
