<?php namespace App\Http\Middleware\System;

use Closure;
use Session;
class SupperAdminMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$curAdmin = $request->user();
		// Allowed next request if current user is supper admin
		if($curAdmin->isSupperAdmin()){
			return $next($request);
		}
		return redirect('access-forbidden')->with('message', 'You are not supper admin!');
	}

}
