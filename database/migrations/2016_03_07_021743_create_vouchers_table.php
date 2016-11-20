<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVouchersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vouchers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('category_id')->comment('Danh mục của voucher');
			$table->string('name')->comment('Tên của khuyến mãi. ');

			$table->enum('type', ['single', 'multi'])->comment('Kiểu voucher: single: kiểu code nhập tên trực tiếp sử dụng nhiều lần. multi: bao gồm nhiều mã code sử dụng một lần ');

			$table->integer('quantity')->insigned()->comment('');

			$table->enum('discount_type', ['gift','money','vpoint'])->comment('Hình thức khuyến mãi: gift: voucher là quà tặng (ví dụ như tặng vé đi xem phim khi áp dụng mã voucher để đặt phòng), vpoint: giảm trừ bằng cách cộng vpoint vào tài khoản của khách hàng , money: giảm trừ trực tiếp bằng tiền');

			$table->text('description')->comment('Mô tả chương trình.');

			$table->timestamp('timebook_start')->comment('Thời gian đặt áp dụng - bắt đầu ');
			$table->timestamp('timebook_finish')->comment('Thời gian đặt áp dụng - kết thúc');

			$table->boolean('time_checkin_apply')->default(false)->comment('Cho phép sử dụng Check thời gian ở');
			$table->timestamp('checkin_start')->nullable()->comment('Thời gian ở bắt đầu ');
			$table->timestamp('checkin_finish')->nullable()->comment('Thời gian ở kết thúc');

			$table->boolean('advance_setting')->default(false)->comment('Cho phép sử dụng tùy chọn nâng cao');

			$table->enum('cancellation_policy', ['default', 'by_hotel', 'by_voucher'])->comment('Chính sách hủy : by_hotel: Theo chính sách hủy của khách sạn, by_voucher: không hoàn hủy');
			$table->timestamp('expired_at')->nullable()->comment('Hạn dùng của voucher - Lưu thời điểm mà voucher ko còn hiệu lực');
			$table->boolean('acitve')->default(false)->comment('Trạng thái kích hoạt của voucher: 0: không được kích hoạt, 1: đã kích hoạt');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vouchers');
	}

}
