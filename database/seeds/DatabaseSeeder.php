<?php

use Illuminate\Database\Seeder;

use App\Models\MenuGroup;
use App\Models\Menu;
use App\Models\MenuItem; 

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{

		$menuGroups = [
			['id' =>  1, 'name' => 'Booking Management', 'description' => 'des', 'visible_on' => 'admin.su|mytour.staff', 'order' => '1', 'active' => '1', 'icon' => 'fa-bank' ],
			['id' =>  2, 'name' => 'Content', 'description' => 'des', 'visible_on' => 'admin.su|mytour.staff', 'order' => '2', 'active' => '1', 'icon' => 'fa-edit' ],
			['id' =>  3, 'name' => 'Statistics', 'description' => 'des', 'visible_on' => 'admin.su|mytour.staff', 'order' => '3', 'active' => '1', 'icon' => 'fa-area-chart' ],
			['id' =>  4, 'name' => 'Marketing', 'description' => 'des', 'visible_on' => 'mkt.manager', 'order' => '4', 'active' => '1', 'icon' => 'fa-th' ],
			['id' =>  5, 'name' => 'System', 'description' => 'des', 'visible_on' => 'admin.su', 'order' => '5', 'active' => '1', 'icon' => 'fa-cogs' ],
			['id' =>  6, 'name' => 'Developer', 'description' => 'des', 'visible_on' => 'dev.manager|dev.leader|dev.staff', 'order' => '5', 'active' => '1', 'icon' => 'fa-cogs' ],
		];


		$menus = [
			1 => ['name' => 'Menus', 'menu_group_id' => 5, 'order' => 1, 'active' => 1, 'visible_on' => 'admin.su'],
			2 => ['name' => 'Accounts', 'menu_group_id' => 5, 'order' => 1, 'active' => 1, 'visible_on' => 'admin.su'],
			3 => ['name' => 'Roles', 'menu_group_id' => 5, 'order' => 1, 'active' => 1, 'visible_on' => 'admin.su'],
			4 => ['name' => 'Permissions', 'menu_group_id' => 5, 'order' => 1, 'active' => 1, 'visible_on' => 'admin.su'],

			5 => ['name' => 'Hotels', 'menu_group_id' => 2, 'order' => 1, 'active' => 1, 'visible_on' => 'mytour.staff'],
			6 => ['name' => 'Tours', 'menu_group_id' => 2, 'order' => 1, 'active' => 1, 'visible_on' => 'mytour.staff'],
			7 => ['name' => 'Locations', 'menu_group_id' => 2, 'order' => 1, 'active' => 1, 'visible_on' => 'mytour.staff'],

			8 => ['name' => 'Hotel Bookings', 'menu_group_id' => 1, 'order' => 1, 'active' => 1, 'visible_on' => 'mytour.staff'],
			9 => ['name' => 'Deal Bookings', 'menu_group_id' => 1, 'order' => 1, 'active' => 1, 'visible_on' => 'mytour.staff'],
			10 => ['name' => 'Tour Bookings', 'menu_group_id' => 1, 'order' => 1, 'active' => 1, 'visible_on' => 'mytour.staff'],
			11 => ['name' => 'Ticket Bookings', 'menu_group_id' => 1, 'order' => 1, 'active' => 1, 'visible_on' => 'mytour.staff'],	

			12 => ['name' => 'Vouchers', 'menu_group_id' => 4, 'order' => 1, 'active' => 1, 'visible_on' => 'mytour.staff'],

			13 => ['name' => 'Mytour Logs', 'menu_group_id' => 6, 'order' => 1, 'active' => 1, 'visible_on' => 'dev.staff|dev.leader|dev.manager'],

			14 => ['name' => 'Exports', 'menu_group_id' => 3, 'order' => 1, 'active' => 1, 'visible_on' => 'mytour.staff'],
			15 => ['name' => 'Statistics', 'menu_group_id' => 3, 'order' => 1, 'active' => 1, 'visible_on' => 'mytour.staff'],
		];


		$menuItems = [
			['name' => 'Add a new menu item', 'menu_id' => 11, 'route' => 'system.menu-items.create', 'visible_on' => 'admin.execute', 'active' => 1],
			['name' => 'Menu item list', 'menu_id' => 11, 'route' => 'system.menu-items.index', 'visible_on' => 'admin.execute', 'active' => 1],
			['name' => 'Add a new menu', 'menu_id' => 11, 'route' => 'system.menus.create', 'visible_on' => 'admin.execute', 'active' => 1],
			['name' => 'Menu list', 'menu_id' => 11, 'route' => 'system.menus.index', 'visible_on' => 'admin.execute', 'active' => 1],
			['name' => 'Add a new menu group', 'menu_id' => 11, 'route' => 'system.menu-groups.create', 'visible_on' => 'admin.execute', 'active' => 1],
			['name' => 'Menu group list', 'menu_id' => 11, 'route' => 'system.menu-groups.index', 'visible_on' => 'admin.execute', 'active' => 1],
			['name' => 'Create a new account', 'menu_id' => 12, 'route' => 'account.confirm-outside-account', 'visible_on' => 'admin.execute', 'active' => 1],
			['name' => 'Account List', 'menu_id' => 12, 'route' => 'account-list', 'visible_on' => 'admin.execute', 'active' => 1],

			['name' => 'New role', 'menu_id' => 13, 'route' => 'role-create', 'visible_on' => 'admin.execute', 'active' => 1],
			['name' => 'Role list', 'menu_id' => 13, 'route' => 'role-list', 'visible_on' => 'admin.execute', 'active' => 1],
			['name' => 'New permission', 'menu_id' => 14, 'route' => 'permission-create', 'visible_on' => 'admin.execute', 'active' => 1],
			['name' => 'Permission list', 'menu_id' => 14, 'route' => 'permission-list', 'visible_on' => 'admin.execute', 'active' => 1],
		];



		if(MenuGroup::all()->count() == 0){
			foreach($menuGroups as $menuGroupRecord){
				$menuGroup = MenuGroup::create($menuGroupRecord);
				if($menuGroup){
					$menuInserts = [];
					foreach($menus as $menu){
						if($menu['menu_group_id'] == $menuGroup->id){
							$newMenu = Menu::create($menu);
							if($newMenu){
								$items = [];
								foreach($menuItems as $menuItem){
									if($menuItem['menu_id'] == $newMenu->id){
										$items[] = $menuItem;
									}
								}
								if(!empty($items)){
									MenuItem::insert($items);
								}
							}
						}
					}
				}

			}
		}


	}

}
