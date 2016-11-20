<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use View, Input, Validator, Auth, Event, Response, Config, Session;
use Queue, Carbon\Carbon;
use App\Commands\SendUserNewPassword;

use App\Models\AdminUser;
use App\Models\Member;
use App\Models\Module;
use App\Models\ModuleGroup;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Department;

use App\Events\System\AdminAccountWasCreated;
use App\Mytour\Validators\System\AccountValidator;
use App\Mytour\Validators\System\ModuleValidator;
use App\Mytour\Validators\System\PermissionValidator;



class SystemController extends Controller {

	
	public function __construct(AdminUser $adminUser , Member $member, Role $role, Permission $permission, Module $module, ModuleGroup $moduleGroup, Department $department)
	{
		$this->middleware('admin.su');
		$this->department 	= $department;
		$this->adminUser  	= $adminUser;
		$this->member   	= $member;
		$this->permission 	= $permission;
		$this->role 	  	= $role;
		$this->module 	  	= $module;
		$this->moduleGroup 	= $moduleGroup;
	}

	public function showAdminList(Request $request)
	{
		$adminQuery = $this->adminUser->query()->with('department');
		$keyword = $request->get('table_search');
		if ($request->get('table_search')) {
			$adminQuery->where('email', '=', $keyword)->orWhere(function($query) use($keyword){
				$query->where('name', 'LIKE', '%'.$keyword.'%');
			})->orWhere(function($query) use($keyword){
				$query->where('phone', 'LIKE', '%'.$keyword.'%');
			});
		}

		if ($department_id = $request->get('department_id', 0)) {
			$adminQuery->where('department_id', $department_id);
		}
		
		$adminUsers = $adminQuery->paginate(20);	
		$departments = $this->department->get()->keyBy(function($item){
			return $item->id;
		})->valueBy(function($item){
			return $item->name;
		});
		return View::make('components.system.account.index', compact('adminUsers', 'departments'));
	}


	public function showAdminTrashed(Request $request)
	{
		$adminQuery = $this->adminUser->onlyTrashed()->with('department');
		$keyword = $request->get('table_search');
		if ($request->get('table_search')) {
			$adminQuery->where('email', '=', $keyword)->orWhere(function($query) use($keyword){
				$query->where('name', 'LIKE', '%'.$keyword.'%');
			})->orWhere(function($query) use($keyword){
				$query->where('phone', 'LIKE', '%'.$keyword.'%');
			});
		}
		
		$adminUsers = $adminQuery->paginate(20);

		return View::make('components.system.account.trashed', compact('adminUsers'));	
	}


	public function getAdminEdit($adminID)
	{
		$admin = $this->adminUser->find($adminID);
		$departments = $this->getDepartments();
		return View::make('components.system.account.edit', compact('admin', 'departments'));

	}


	public function postAdminEdit($adminID, Request $request, AccountValidator $validator)
	{	
		//get admin model instance
		$admin = $this->adminUser->find($adminID);

		if (!$admin) {
			return Response::make('AdminUser not found!', 404);
		}

		$validator->validateDataEdit($request);

		// fill data to model 
		$admin->fill($request->all());
		$admin->active = $request->get('active', 0);
		
		//save to database 
		$admin->save();
		return redirect()->route('account-list');
	}

	



	public function getCreateRole()
	{
		return View::make('components.system.role.create');
	}



	public function postCreateRole(Request $request)
	{
		$inputs = $request->input();
		$vali = $this->validateRole($request);
	    $request->flash();
		if ($vali->fails()) return redirect()->back()->withErrors($vali->errors());

		$this->role->name = $inputs['name'];
		$this->role->display_name = $inputs['display_name'];
		$this->role->description = $inputs['description'];
		$this->role->save();
		return redirect()->route('role-list');
	}


	private function validateRole(Request $request)
	{
		return Validator::make($request->all(), [
		        'name' => 'required',
		        'display_name' => 'required'
		    ],
		    [
			    'name.required'    		=> 'Name là thông tin bắt buộc!',
			    'display_name.required' => 'Display name là thông tin bắt buộc!',
			]
	    );
	}

	public function getEditRole($roleID)
	{
		$role = $this->role->find($roleID);
		return View::make('components.system.role.edit',compact('role'));
	}

	public function postEditRole($roleID, Request $request)
	{
		$inputs = $request->input();
		$role = $this->role->find($roleID);
		$vali = $this->validateRole($request);
		$request->flash();
		
		if ($vali->fails()) return redirect()->back()->withErrors($vali->errors());

		$role->name = $inputs['name'];
		$role->display_name = $inputs['display_name'];
		$role->description = $inputs['description'];
		$role->save();
		return redirect()->route('role-list');

	}

	public function deleteRole($roleID)
	{
		$role = $this->role->find($roleID);
		
		$role->delete();
		
		return redirect()->route('role-list');
	}


	public function showRoleList(Request $request)
	{

		$inputs = $request->input();
		$query = $this->role->query();
		if ($request->has('table_search')) {
			$query->where('name', array_get($inputs, 'table_search'))
				  ->orWhere('display_name', 'LIKE', '%' .array_get($inputs, 'table_search'). '%' )
				  ->orWhere('id', '=', array_get($inputs, 'table_search'));
		}
		$roles = $query->paginate(20);
		return View::make('components.system.role.index', compact('roles'));

		
	}



	public function showListPermission(Request $request)
	{
		$inputs = $request->input();
		$query = $this->permission->query();
		if ($request->has('table_search')) {
			$query->where('name', array_get($inputs, 'table_search'))
				  ->orWhere('display_name', 'LIKE', '%' .array_get($inputs, 'table_search'). '%' );
		}
		$permissions = $query->paginate(20);
		return View::make('components.system.permission.index', compact('permissions'));
	}



	public function getCreatePermission()
	{
		return View::make('components.system.permission.create');
	}

	public function getEditPermission($permissionID)
	{
		$permission = $this->permission->find($permissionID);
		// dd($permission);

		return View::make('components.system.permission.edit',compact('permission'));
	}

	public function postEditPermission($permissionID, Request $request, PermissionValidator $validator)
	{
		
		$permission = $this->permission->find($permissionID);

		if(!$permission){
			return Response::make('Permission not found!', 404);
		}

		$validator->validateDataEdit($request);

		$permission->display_name = $request->get('display_name');
		$permission->description = $request->get('description');
		$permission->save();
		return redirect()->route('permission-list');

	}



	public function postCreatePermission(Request $request, PermissionValidator $validator)
	{
		$inputs = $request->input();

		$validator->validateDataCreate($request);

		$this->permission->name = $inputs['name'];
		$this->permission->display_name = $inputs['display_name'];
		$this->permission->description = $inputs['description'];
		$this->permission->save();
		return redirect()->route('permission-list');
	}



	public function getAttachPermission($roleID, Request $request)
	{
		$inputs = $request->input();
		$permQuery = $this->permission->query();
		if ($request->has('search')) {
			$keyword = array_get($inputs, 'search');
			$permQuery->where('name', 'LIKE', '%' . $keyword . '%')
					  ->orWhere('display_name',  'LIKE', '%' . $keyword . '%');
		}

		$permissions = $permQuery->orderBy('name')->get();
		$role 		 = $this->role->find($roleID);
		return View::make('components.system.role.attach-permission', compact('permissions', 'role') );
	}


	public function postAttachPermission($roleID, Request $request)
	{
		$inputs = $request->input();
		$role = $this->role->find($roleID);

		$permission_ids = array_get($inputs, 'permission_id');

		$role->perms()->sync($permission_ids);

		return redirect()->route('role-list');
	}



	/**
	 *  get list role form
	 * @param int $adminID
	 * @param Illuminate\Http\Request $request
	 * @return Illuminate\Http\Response
	 */
	public function getAttachRole($adminID, Request $request)
	{
		$inputs = $request->input();
		$roleQuery = $this->role->query();
		if ($request->has('search')) {
			$keyword = array_get($inputs, 'search');
			$roleQuery->where('name', 'LIKE', '%' . $keyword . '%')
					  ->orWhere('display_name',  'LIKE', '%' . $keyword . '%');
		}

		$roles 		= $roleQuery->orderBy('name')->get();
		$adminUser 	= $this->adminUser->find($adminID);
		return View::make('components.system.account.attach-role', compact('roles', 'adminUser') );
	}


	/**
	 * get create Admin Account form
	 * @return Illuminate\Http\Response
	 */
	public function getCreateAccount()
	{

		if(!Session::has('member')){
			return redirect()->route('account.confirm-outside-account');	
		}
		$departments = $this->getDepartments();
		return View::make('components.system.account.create', compact('departments'));
	}




	public function getOutsideAccount()
	{
		return view('components.system.account.confirm-outside-account');
	}


	public function postOutsideAccount(Request $request)
	{
		$email = $request->get('outside_email');
		$member = $this->member->findByEmail($email);
		if(!$member || !$email){
			return redirect()->back()->with('message', 'Email không hợp lệ!');
		}
		return redirect()->route('account-create')->with('member', $member);
	}


	/**
	 *  post create account form
	 * @param Illuminate\Http\Request $request
	 * @param App\Mytour\Validators\AccountValidator $validator
	 * @return \Illuminate\Http\Response
	 */
	public function postCreateAccount(Request $request, AccountValidator $validator)
	{
		$data 		  = $request->all();

		$outsideEmail = $request->get('outside_email');

		$member  = $this->member->findByEmail($outsideEmail);

		if($member){
			Session::flash('member', $member);
			$data['user_id'] = $member->use_id;
		}
		//validate data when creating new account
		$validator->validateDataCreate($request);

		$newAdminUser = $this->createAccount($data);
		$newAdminUser->string_password = $data['password'];

		if ($newAdminUser) {
			//fire event when new admin account was created
			// Event::fire(new AdminAccountWasCreated($newAdminUser));
			// $date = Carbon::now()->addMinutes(1);
			Session::put('new_account', $newAdminUser);
			return redirect()->route('account.role-init');
		} 
		
	}


	public function getFinishCreateAccount()
	{
		if(Session::has('new_account')){
			$newAccount = Session::pull('new_account');
			return view('components.system.account.finish-create')->with('newAccount', $newAccount);
		}
		return redirect()->route('account.confirm-outside-account');
	}



	public function getRoleInitialization()
	{
		if(Session::has('new_account')){
			$newAccount = Session::get('new_account');
			$rolesList = $this->role->get()->groupBy(function($item){
				$pos = strpos($item->name,'.');
				return substr($item->name, 0, $pos);
			})->sortByDesc(function($item){
				return count($item);
			});

			return view('components.system.account.initialize-role')->with('rolesList', $rolesList);		
		} 
		return redirect()->route('account.confirm-outside-account');
		
	}


	public function postRoleInitialization(Request $request)
	{
		if(Session::has('new_account')){
			$roleIds = $request->get('role');
			$newAccount = Session::get('new_account');
			$newAccount->attachRoles($roleIds);
			Queue::push(new \App\Commands\SendMailNewAccount($newAccount));
			return redirect()->route('account.finish-create-account');
		} 
		return redirect()->route('account.confirm-outside-account');

	}


	public function softDeleteAccount($adminID)
	{
		$adminUser = $this->adminUser->find($adminID);
		if ($adminUser) {
			if ($adminUser->email === 'quanghieu2104@gmail.com') {
				return Response::make('Access forbidden!');
			}
			$adminUser->delete();
		} else {
			return Response::make('AdminUser Not Found', 404);
		}
		return redirect()->back();
	}


	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return new AdminUser
	 */
	private function createAccount(array $data)
	{
		return AdminUser::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => bcrypt($data['password']),
			'phone' => $data['phone'],
			'address' => $data['address'],
			'identity_card' => $data['identity_card'],
			'user_id' 		=> $data['user_id'],
			'gender'		=> $data['gender'],
			'active'		=> $data['active'],
			'department_id' => $data['department_id']
		]);
	}


	/**
	 * get create module form
	 * @param Illuminate\Http\Request $request
	 */
	public function showModuleList(Request $request)
	{
		$moduleQuery = $this->module->query();
		$request->get('table_search');
		if ($request->get('table_search')) {
			$moduleQuery->where('mod_name', 'LIKE', '%' . $request->get('table_search') . '%');
		}

		$modules = $moduleQuery->paginate(20);
		return view('components.system.module.index', compact('modules'));
	}


	/**
	 * get create module form
	 * @param Illuminate\Http\Request $request
	 */
	public function getCreateModule(Request $request)
	{
		$groups = $this->moduleGroup->all();
		$listGroup = [];
		foreach($groups as $group){
			$listGroup[$group->mog_id] = $group->mog_name;
		}
		return View::make('components.system.module.create', compact('listGroup'));
	}



	/**
	 * get create module form
	 * @param Illuminate\Http\Request $request
	 */
	public function postCreateModule(Request $request, ModuleValidator $validator)
	{
		$validator->validateDataCreate($request);
		$this->module->fill($request->all());
		$this->module->save();
		return redirect()->route('module-list');
	}



	/**
	 * get create module form
	 * @param Illuminate\Http\Request $request
	 */
	public function getEditModule($moduleID, Request $request)
	{
		$module = $this->module->find($moduleID);
		if (!$module) {
			return Response::make('Module not found!', 404);
		}


		$groups = $this->moduleGroup->all();
		$listGroup = [];
		foreach($groups as $group){
			$listGroup[$group->mog_id] = $group->mog_name;
		}
		return View::make('components.system.module.edit', compact('module', 'listGroup'));
	}



	/**
	 * get create module form
	 * @param Illuminate\Http\Request $request
	 */
	public function postEditModule($moduleID, Request $request, ModuleValidator $validator)
	{
		$module = $this->module->find($moduleID);
		if (!$module) {
			return Response::make('Module not found!', 404);
		}
		$validator->validateDataEdit($request);
		$module->fill($request->all());
		$module->save();
		return redirect()->route('module-list');
	}


	/**
	 * get create module form
	 * @param Illuminate\Http\Request $request
	 */
	public function deleteModule($moduleID, Request $request)
	{
		$module = $this->module->find($moduleID);
		
		if (!$module) {
			return Response::make('Module not found!', 404);
		}
		$module->delete();
		return redirect()->route('module-list');
	}


	private function getDepartments()
	{
		$departments = [];
		$this->department->all()->map(function($item) use(&$departments) {
		    $departments[$item->id] = $item->name;
		});
		return $departments;
	}


	public function restoreAdminUser($id)
	{
		$user = $this->adminUser
					 ->onlyTrashed()
					 ->where('id', $id)
					 ->get()->first();
		if($user){
			$user->restore();
		}
		return redirect()->route('account-trashed');
	}




	public function loginByUser($userID, Request $request)
	{
		$user = $this->adminUser->find($userID);
		if($request->user()->isSupperAdmin() ){
			Auth::logout();
			Auth::login($user);
			return '<script type="text/javascript">
						window.parent.location.href="/";
					</script>';
		} else {
			return '<script type="text/javascript">
						window.document.write("Access Denied!");
						setTimeout(function(){
							window.parent.location.href="/";	
						}, 1000);
					</script>';
		}
	}

	public function sendUserPassword($userID)
	{
		$user = $this->adminUser->find($userID);
		Queue::push(new SendUserNewPassword($user));
		return redirect()->back();
	}



	public function getAttachDepartmentRole(Request $request, $depID)
	{
		$curUser = $request->user();
		$department = $this->department->find($depID);
		$roleGroups 	=  $this->role->get()->groupBy(function($item, $key){
			$dotPos = strpos($item->name, '.'); 
			return substr($item->name, 0, $dotPos);
		});

		return view('components.system.department.attach-role')->with(['department' => $department, 'roleGroups' => $roleGroups]);
	}


	public function postAttachDepartmentRole(Request $request, $depID)
	{
		$roleData = mytour_collect((array)$request->get('role'));
		foreach($roleData as $userID => $roles){
			$user = $this->adminUser->find($userID);
			$user->roles()->sync($roles);
		}
		return redirect()->back();
	}


}
