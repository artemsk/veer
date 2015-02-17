<?php namespace Veer\Http\Middleware;

use Closure;

class EarlyResponseMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(app('veer')->forceEarlyResponse === true) {
		
			return app('veer')->earlyResponseContainer;
		}
		
		return $next($request);
	}

}
