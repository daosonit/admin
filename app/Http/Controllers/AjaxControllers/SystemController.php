<?php namespace App\Http\Controllers\AjaxControllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\AdminUser;
use App\Models\Role;
use App\Models\Permission;
use View, Input, Auth;

class SystemController extends Controller {

	public function __construct(AdminUser $adminUser, Role $role, Permission $permission)
	{
		$this->middleware('admin.su');
		$this->adminUser  = $adminUser;
		$this->curUser    =  Auth::user();
		$this->role 	  = $role;
		$this->permission = $permission;
	}


	public function attachPermissionToRole($roleID, Request $request)
	{
		$resCheck = false;
		$isChecked = false;
		$role = $this->role->find($roleID);
		$permission = $role->perms();

		$id = $request->input('id');
		$permissionAttach = $this->permission->find($id);
		
		$permissions = $permission->get();
		if($key = $permissions->contains($permissionAttach)){
			foreach($permissions as $key => $perm){
				if($permissionAttach->id == $perm->id){
					$permissions->forget($key);
				}
			}
		} else {
			$permissions->push($permissionAttach);
			$isChecked = true;
		}
		$permIDlist = [];

		foreach($permissions as $perm){
			$permIDlist[] = $perm->id;
		}
		$attached = $role->perms()->sync($permIDlist);

		
		foreach($attached as $type){
			if(!empty($type)){
				$resCheck = true;
				break;
			}
		}
		
		return response()->json(['res' => $resCheck, 'checked' => $isChecked]);
	}




	public function attachRoleToAdminUser($adminID, Request $request)
	{
		$resCheck = false;
		$isChecked = false;
		$adminUser = $this->adminUser->find($adminID);
		$role = $adminUser->roles();

		$id = $request->input('id');
		$roleAttach = $this->role->find($id);
		
		$roles = $role->get();
		if($key = $roles->contains($roleAttach)){
			foreach($roles as $key => $role){
				if($roleAttach->id == $role->id){
					$roles->forget($key);
				}
			}
		} else {
			$roles->push($roleAttach);
			$isChecked = true;
		}
		$roleIDlist = [];

		foreach($roles as $role){
			$roleIDlist[] = $role->id;
		}
		$attached = $adminUser->roles()->sync($roleIDlist);

		
		foreach($attached as $type){
			if(!empty($type)){
				$resCheck = true;
				break;
			}
		}
		
		return response()->json(['res' => $resCheck, 'checked' => $isChecked]);
	}

}
