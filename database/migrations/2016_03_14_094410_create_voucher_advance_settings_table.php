<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherAdvanceSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('voucher_advance_settings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('voucher_id')->unsigned()->unique()->comment('');
			$table->text('hotel_star_rate_apply')->nullable()->comment('Chuỗi json mảng của các hạng sao đc áp dụng! ');
			$table->text('hotel_city_apply')->nullable()->comment('Chuỗi json lưu mảng của các id tỉnh thành mà khách sạn đó thuộc được áp dụng');
			$table->integer('booking_money_min')->unsigned()->nullable()->comment('Giá trị tối thiểu của booking');
			$table->integer('booking_money_max')->unsigned()->nullable()->comment('Giá trị tối đa của booking');

			$table->text('hotel_accepted_apply')->nullable()->comment('Lưu json mảng id các khách sạn được áp dụng voucher này');

			$table->tinyInteger('customer_first_booking')->nullable()->comment('Chỉ Áp dụng cho khách hàng đặt phòng lần đầu!');

			$table->tinyInteger('customer_old')->nullable()->comment('Chỉ Áp dụng cho khách hàng cũ!');

			$table->tinyInteger('customer_logged_in')->nullable()->comment('Chỉ Áp dụng cho khách hàng đăng nhập!');

			$table->tinyInteger('customer_is_partner')->nullable()->comment('Áp dụng cho đối tác');
			
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
		Schema::drop('voucher_advance_settings');
	}

}
