<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoucherDiscountInfosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('voucher_discount_infos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('voucher_id')->unsigned()->unique()->comment('Khóa phụ tham chiếu đến id bảng vouchers');
			$table->tinyInteger('money_type')->nullable()->unsigned()->comment('Kiểu tính discount tiền theo % hoặc số tiền trực tiếp: 0: sô tiền, 1: % giá trị đơn phòng');
			$table->tinyInteger('vpoint_type')->nullable()->unsigned()->comment('Kiểu tính discount vpoint theo % hoặc số vpoint trực tiếp: 0: sô vpoint, 1: % giá trị đơn phòng');
			$table->integer('money')->unsigned()->nullable()->comment('Số tiền nếu là giảm trừ bằng tiền');
			$table->integer('money_max')->unsigned()->nullable()->comment('Số tiền giảm trừ tối đa');
			$table->integer('vpoint')->unsigned()->nullable()->comment('Lượng discount nếu là tặng vpoint');
			$table->integer('vpoint_max')->unsigned()->nullable()->comment('Số điểm vpoint giảm trừ tối đa');
			$table->integer('vpoint_expire')->unsigned()->nullable()->comment('Hạn sử dụng của vpoint tính theo đơn vị ngày');
			$table->text('gift_info')->nullable()->comment('Thông tin về voucher nếu là quà tặng');

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
		Schema::drop('voucher_discount_infos');
	}

}
