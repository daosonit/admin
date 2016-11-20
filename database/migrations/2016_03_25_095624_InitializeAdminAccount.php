<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\AdminUser;

class InitializeAdminAccount extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$adminUser = new AdminUser;
		
		$adminUser->email 	 = 'quanghieu2104@gmail.com';
		$adminUser->password = bcrypt('mytour.vn');
		$adminUser->phone    = '0985214214';
		$adminUser->name     = 'Nguyễn Quang Hiếu';
		$adminUser->address    = 'Phòng IT, 51 Lê Đại Hành, Hà Nội';
		$adminUser->branch    = 1;
		$adminUser->active    = 1;
		$adminUser->save();

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
