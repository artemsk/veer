<?php namespace Veer\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class AuthenticateAdministrator {

	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}
	
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(!administrator()) 
		{ 
			return \Redirect::route('user.index'); 
		} 
		
		$a = app('veer')->administrator_credentials;	
		
		$a['sites_encoded'] = json_decode($a['sites_watch']);

		if(!in_array(app('veer')->siteId, (array)$a['sites_encoded']) && !empty($a['sites_watch'])) 
		{
			return \Redirect::route('user.index');
		}			
		
		app('veer')->administrator_credentials['sites_encoded'] = $a['sites_encoded'];
		
		app('veer')->administrator_credentials['access_encoded'] = json_decode(app('veer')->administrator_credentials['access_parameters']);
		
		return $next($request);
	}

}
