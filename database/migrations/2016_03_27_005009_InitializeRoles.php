<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitializeRoles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		$rolesData = [
			['name' => 'admin.su', 'display_name' => 'Super Admin', 'description'  => 'Special Role - Full roles and permissions'],
			['name' => 'special.role', 'display_name' => 'Special Role', 'description'  => 'Vai trò đặc biệt.'],

			['name' => 'cs.mamager', 'display_name' => 'Customer Service Manager', 'description'  => 'Trường bộ phận chăm sóc khách hàng.'],
			['name' => 'cs.leader', 'display_name' => 'Customer Service Leader', 'description'  => 'Trường nhóm của bộ phận chăm sóc khách hàng.'],
			['name' => 'cs.staff', 'display_name' => 'Customer Service Staff', 'description'  => 'Nhân viên chăm sóc khách hàng.'],
			['name' => 'cs.intern', 'display_name' => 'Customer Serivce Intern Staff', 'description'  => 'Nhân viên thực tập, cộng tác viên chăm sóc khách hàng'],

			['name' => 'mkt.manager', 'display_name' => 'Marketing Manager', 'description'  => 'Trưởng bộ phận marketing'],
			['name' => 'mkt.leader', 'display_name' => 'Marketing Leader', 'description'  => 'Trưởng nhóm bộ phận marketing'],
			['name' => 'mkt.staff', 'display_name' => 'Marketing Staff', 'description'  => 'Nhân viên marketing.'],
			['name' => 'mkt.intern', 'display_name' => 'Marketing Intern', 'description'  => 'Thực tập, cộng tác viên marketing.'],

			['name' => 'dev.manager', 'display_name' => 'Developer (IT) Manager', 'description'  => 'Trưởng phòng bộ phận IT '],
			['name' => 'dev.leader', 'display_name' => 'Developer (IT) Leader', 'description'  => 'Trưởng nhóm bộ phận IT'],
			['name' => 'dev.staff', 'display_name' => 'Developer (IT) Staff' , 'description'  => 'Nhân viên IT'],
			['name' => 'dev.intern', 'display_name' => 'Developer (IT) Intern Staff', 'description'  => 'Nhân viên, thực tập sinh IT'],

			['name' => 'sales.manager', 'display_name' => 'Sales Manager', 'description'  => 'Trưởng bộ phận bán hàng.'],
			['name' => 'sales.leader', 'display_name' => 'Sales Leader', 'description'  => 'Trưởng nhóm bộ phận bán hàng.'],
			['name' => 'sales.staff', 'display_name' => 'Sales Staff', 'description'  => 'Nhân viên bộ phận bán hàng.'],
			['name' => 'sales.intern', 'display_name' => 'Sales Intern Staff', 'description'  => 'Nhân viên bộ phận bán hàng.'],

			['name' => 'act.manager', 'display_name' => 'Accountant Manager', 'description'  => 'Trưởng bộ phận kế toán.'],
			['name' => 'act.leader', 'display_name' => 'Accountant Leader', 'description'  => 'Trưởng nhóm bộ phận kế toán.'],
			['name' => 'act.staff', 'display_name' => 'Accountant Staff', 'description'  => 'Nhân viên bộ phận  kế toán.'],
			['name' => 'act.intern', 'display_name' => 'Accountant Intern Staff', 'description'  => 'Nhân viên bộ phận  kế toán.'],

			['name' => 'cos.manager', 'display_name' => 'COS Manager', 'description'  => 'Trưởng bộ phận COS.'],
			['name' => 'cos.leader', 'display_name' => 'COS Leader', 'description'  => 'Trưởng nhóm bộ phận COS.'],
			['name' => 'cos.staff', 'display_name' => 'COS Staff ', 'description'  => 'Nhân viên bộ phận COS.'],
			['name' => 'cos.intern', 'display_name' => 'COS Intern Staff', 'description'  => 'Nhân viên bộ phận  COS.'],

			['name' => 'pln.manager', 'display_name' => 'Planning Manager', 'description'  => 'Trưởng bộ phận kế hoạch phát triển.'],
			['name' => 'pln.leader', 'display_name' => 'Planning Leader', 'description'  => 'Trưởng nhóm bộ phận kế hoạch phát triển.'],
			['name' => 'pln.staff', 'display_name' => 'Planning Staff', 'description'  => 'Nhân viên bộ phận  kế hoạch phát triển.'],
			['name' => 'pln.intern', 'display_name' => 'Planning Intern Staff', 'description'  => 'Nhân viên bộ phận  kế hoạch phát triển.'],

			['name' => 'hr.manager', 'display_name' => 'Human Resource Manager', 'description'  => 'Trưởng bộ phận hành chính nhân sự.'],
			['name' => 'hr.leader', 'display_name' => 'Human Resource Leader', 'description'  => 'Trưởng nhóm bộ phận hành chính nhân sự.'],
			['name' => 'hr.staff', 'display_name' => 'Human Resource Staff', 'description'  => 'Nhân viên bộ phận  hành chính nhân sự.'],
			['name' => 'hr.intern', 'display_name' => 'Human Resource Intern Staff', 'description'  => 'Nhân viên bộ phận  hành chính nhân sự.'],

			['name' => 'content.manager', 'display_name' => 'Content Manager', 'description'  => 'Trưởng bộ phận content.'],
			['name' => 'content.leader', 'display_name' => 'Content Leader', 'description'  => 'Trưởng nhóm bộ phân content'],
			['name' => 'content.staff', 'display_name' => 'Content Staff', 'description'  => 'Nhân viên bộ phận content.'],
			['name' => 'content.intern', 'display_name' => 'Content Intern Staff', 'description'  => 'Thực tập, cộng tác viên content'],

			['name' => 'mytour.staff', 'display_name' => 'Mytour Staff', 'description'  => 'Là nhân viên của Mytour.'],
		];


		DB::table(Config::get('entrust.roles_table'))->insert($rolesData);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// DB::table(Config::get('entrust.roles_table'))->truncate();
	}

}
