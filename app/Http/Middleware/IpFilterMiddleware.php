<?php namespace App\Http\Middleware;

use Closure;
use Config;
use App\Models\ResetIP;

class IpFilterMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		$allowIps = ResetIP::get()->valueBy(function($item){
			return $item->ip;
		})->toArray();

		$allowIps = mytour_collect($allowIps);
	
		$ipList = collect(Config::get('mytour.office_ip'));
		if($ipList->contains($this->getRequestIP($request)) || $allowIps->contains($this->getRequestIP($request))){
			return $next($request);	
		}
		return 'Access denied!';
	}





	private function getRequestIP($request)
	{
		$ip = $request->ip();
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $ip;
	}

}
