<?php 


Route::group(['prefix' => 'system'], function(){

	Route::group(['prefix' => 'role'], function(){
		Route::get('create', ['as' => 'role-create', 'uses' => 'SystemController@getCreateRole']);
		Route::post('create', 'SystemController@postCreateRole');
		Route::get('list', ['as' => 'role-list', 'uses' => 'SystemController@showRoleList']);	
		Route::get('edit/{roleID}', ['as' => 'role-edit', 'uses' => 'SystemController@getEditRole']);	
		Route::post('edit/{roleID}', ['uses' => 'SystemController@postEditRole']);	
		Route::get('delete/{roleID}', ['as' => 'role-delete', 'uses' => 'SystemController@deleteRole']);

		Route::get('attach/{roleID}', ['as' => 'attach-permission', 'uses' => 'SystemController@getAttachPermission']);
		Route::post('attach/{roleID}', 'SystemController@postAttachPermission');
		
	});


	Route::group(['prefix' => 'permission'], function(){
		Route::get('create', ['as' => 'permission-create', 'uses' => 'SystemController@getCreatePermission']);
		Route::post('create', 'SystemController@postCreatePermission');
		Route::get('list', ['as' => 'permission-list', 'uses' => 'SystemController@showListPermission']);
		Route::get('edit/{permissionID}', ['as' => 'permission-edit', 'uses' => 'SystemController@getEditPermission']);	
		Route::post('edit/{permissionID}', ['uses' => 'SystemController@postEditPermission']);	
	});


	Route::group(['prefix' => 'account'], function(){

		Route::get('list', ['as' => 'account-list', 'uses' => 'SystemController@showAdminList']);
		Route::get('trashed', ['as' => 'account-trashed', 'uses' => 'SystemController@showAdminTrashed']);
		Route::get('create', ['as' => 'account-create', 'uses' => 'SystemController@getCreateAccount']);
		Route::post('create', ['uses' => 'SystemController@postCreateAccount']);

		Route::get('edit/{adminID}', ['as' => 'admin-edit', 'uses' => 'SystemController@getAdminEdit']);
		Route::post('edit/{adminID}', ['uses' => 'SystemController@postAdminEdit']);

		Route::get('attach/{adminID}', ['as' => 'attach-role', 'uses' => 'SystemController@getAttachRole']);
		Route::post('attach/{adminID}', 'SystemController@postAttachRole');


		Route::get('delete/{adminID}', ['as' => 'admin-delete', 'uses' => 'SystemController@softDeleteAccount']);
		Route::get('restore/{adminID}', ['as' => 'admin-restore', 'uses' => 'SystemController@restoreAdminUser']);

		Route::get('confirm-outside-account', ['as' => 'account.confirm-outside-account', 'uses' => 'SystemController@getOutsideAccount']);
		Route::post('confirm-outside-account', ['uses' => 'SystemController@postOutsideAccount']);

		Route::get('role-init', ['as' => 'account.role-init', 'uses' => 'SystemController@getRoleInitialization']);

		Route::post('role-init', ['as' => 'account.role-init', 'uses' => 'SystemController@postRoleInitialization']);

		Route::get('finish-create-account', ['as' => 'account.finish-create-account', 'uses' => 'SystemController@getFinishCreateAccount']);

		Route::get('login-user/{userID}', ['as' => 'account.fake-login', 'uses' => 'SystemController@loginByUser']);

		Route::get('send-new-password/{userID}', ['as' => 'account.send-password', 'uses' => 'SystemController@sendUserPassword']);
		
	});


	Route::group(['prefix' => 'module'], function(){

		Route::get('list', ['as' => 'module-list', 'uses' => 'SystemController@showModuleList']);
		Route::get('create', ['as' => 'module-create', 'uses' => 'SystemController@getCreateModule']);
		Route::post('create', ['uses' => 'SystemController@postCreateModule']);

		Route::get('edit/{moduleID}', ['as' => 'module-edit', 'uses' => 'SystemController@getEditModule']);	
		Route::post('edit/{moduleID}', ['uses' => 'SystemController@postEditModule']);	

		Route::get('delete/{moduleID}', ['as' => 'module-delete', 'uses' => 'SystemController@deleteModule']);
	});



	Route::resource('menu-groups', 'MenuGroupController');
	Route::resource('menus', 'MenuController');
	Route::resource('menu-items', 'MenuItemController');


	Route::get('departments/{depID}/attach-role', ['as' => 'dep-attach-role', 'uses' => 'SystemController@getAttachDepartmentRole']);
	Route::post('departments/{depID}/attach-role', ['uses' => 'SystemController@postAttachDepartmentRole']);
	Route::resource('departments', 'System\DepartmentController');
	
});



















