<?php
namespace App\Mytour\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Exception\HttpResponseException;

trait RolePermissionChecker
{

	public function userHasRole($role, $requireAll = false)
	{
		$request = $this->getCurrentRequest();
		if(!$request->user()->hasRole($role, $requireAll)){
			$message = $this->getRoleErrorMsg($role, $requireAll);
			throw new  HttpResponseException($this->makeRedirectErrorAccess($message));
		}
	}



	public function userCan($permission, $requireAll = false)
	{
		$request = $this->getCurrentRequest();
		if(!$request->user()->can($permission, $requireAll)){
			$message = $this->getPermissionErrorMsg($permission, $requireAll);
			throw new  HttpResponseException($this->makeRedirectErrorAccess($message));
		}
	}



	public function makeRedirectErrorAccess($message)
	{
		return redirect('access-forbidden')->with('message', $message);
	}


	private function getCurrentRequest()
	{
		return app()->make('Illuminate\Http\Request');
	}



    private function getPermissionErrorMsg($permission, $requireAll)
    {
    	
    	if(is_array($permission)){
    		if($requireAll){
    			$strRole =  "Required all of " . implode(', ', $permission);
    		} else {
    			$strRole =  "You can't any of " . implode(', ', $permission);
    		}
    	} else {
    		$strRole = "You can't " . $permission;	
    	}
    	
    	return $strRole;
    }


    private function getRoleErrorMsg($role, $requireAll)
    {
    	if(is_array($role)){
    		if($requireAll){
    			$strRole =  'Required all of ' . implode(', ', $role);
    		} else {
    			$strRole =  'You are not in any of ' . implode(', ', $role);
    		}
    	} else {
    		$strRole = 'You are not: ' . $role;	
    	}
    	return $strRole;
    }

}