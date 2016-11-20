<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatePlansTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rate_plans', function(Blueprint $table)
		{
			$table->increments('rap_id')->index();
			$table->integer('rap_hotel_id')->comment('id khách sạn');
			$table->string('rap_title')->comment('Tên của đơn giá. ');
			$table->text('rap_room_apply_id')->comment('json id phòng áp dụng đơn giá');
			$table->tinyInteger('rap_type_price')->default(0)->index()->comment('Kiểu giá');
			$table->text('rap_surcharge_info')->default("")->comment('Thông tin phụ thu');
			$table->integer('rap_accompanied_service')->default(0)->comment('Dịch vụ đi kèm');
			$table->text('rap_cancel_policy_info')->default("")->comment('Thông tin chính sách hủy ');
			$table->boolean('rap_delete')->default(0)->comment('');
			$table->integer('rap_delete_user_id')->default(0)->comment('id user xóa đơn giá');
			$table->integer('rap_delete_time')->default(0)->comment('thời gian xóa đơn giá');
			$table->boolean('rap_hidden_price')->default(0)->comment('Trạng thái ẩn giá: 0: không được kích hoạt, 1: đã kích hoạt');
			$table->boolean('rap_active')->default(0)->comment('Trạng thái kích hoạt của đơn giá: 0: không được kích hoạt, 1: đã kích hoạt');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('rate_plans');
	}

}
