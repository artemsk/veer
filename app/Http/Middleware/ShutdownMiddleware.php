<?php namespace Veer\Http\Middleware;

use Closure;

class ShutdownMiddleware {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		return $next($request);		
	}

	public function terminate($request, $response)
	{
		if (!\App::runningInConsole() && \App::bound('veer') && app('veer')->booted) {

			$timeToLoad = empty(app('veer')->statistics['loading']) ? 0 : app('veer')->statistics['loading'];
			
			if($timeToLoad > config('veer.loadingtime')) {

				\Log::alert('Slowness detected: ' . $timeToLoad . ': ', app('veer')->statistics());
				info('Queries: ', \DB::getQueryLog());
			}

			(new \Veer\Commands\TrackingUserCommand())->handle();

			(new \Veer\Commands\HttpQueueWorkerCommand(config('queue.default')))->handle(); 		
		} 
	}
	
}
