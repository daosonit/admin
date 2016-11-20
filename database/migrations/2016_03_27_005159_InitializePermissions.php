<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitializePermissions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$permissionData = [
			['name' => 'hotelbooking.showlist', 'display_name' => 'Hotel Booking', 'description'  => 'Can  Hotel Booking.'],
			['name' => 'hotelbooking.viewdetail', 'display_name' => 'Hotel Booking', 'description'  => 'Can  Hotel Booking.'],
			['name' => 'hotelbooking.processing', 'display_name' => 'Hotel Booking', 'description'  => 'Can  Hotel Booking.'],
			['name' => 'hotelbooking.send-email-to-customer', 'display_name' => 'Hotel Booking', 'description'  => 'Can  Hotel Booking.'],
			['name' => 'hotelbooking.change-status', 'display_name' => 'Hotel Booking', 'description'  => 'Can  Hotel Booking.'],
			['name' => 'hotelbooking.receive-money', 'display_name' => 'Hotel Booking', 'description'  => 'Can  Hotel Booking.'],



			['name' => 'role.showlist', 'display_name' => 'Role Show List', 'description'  => 'description'],
			['name' => 'role.create', 'display_name' => 'Role Create', 'description'  => 'description'],
			['name' => 'role.update', 'display_name' => 'Role Update', 'description'  => 'description'],
			['name' => 'role.delete', 'display_name' => 'Role Delete', 'description'  => 'description'],

			['name' => 'permission.showlist', 'display_name' => 'Permission Show List ', 'description'  => 'description'],
			['name' => 'permission.create', 'display_name' => 'Permission Create', 'description'  => 'description'],
			['name' => 'permission.update', 'display_name' => 'Permission Update', 'description'  => 'description'],
			['name' => 'permission.delete', 'display_name' => 'Permission Update', 'description'  => 'description'],

			['name' => 'voucher.showlist', 'display_name' => 'Voucher Show List', 'description'  => 'description'],
			['name' => 'voucher.create', 'display_name' => 'Voucher Create', 'description'  => 'description'],
			['name' => 'voucher.update', 'display_name' => 'Voucher Update', 'description'  => 'description'],
			['name' => 'voucher.delete', 'display_name' => 'Voucher Delete', 'description'  => 'description'],

			['name' => 'account.showlist', 'display_name' => 'Account Show List', 'description'  => 'description'],
			['name' => 'account.create', 'display_name' => 'Account Create', 'description'  => 'description'],
			['name' => 'account.update', 'display_name' => 'Account Update', 'description'  => 'description'],
			['name' => 'account.delete', 'display_name' => 'Account Delete', 'description'  => 'description'],

			['name' => 'admin.execute', 'display_name' => 'Admin Execute', 'description'  => 'description'],

		];

		DB::table(Config::get('entrust.permissions_table'))->insert($permissionData);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// DB::table(Config::get('entrust.permissions_table'))->truncate();
	}

}
