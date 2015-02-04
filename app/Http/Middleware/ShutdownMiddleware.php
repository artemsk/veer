<?php namespace Veer\Http\Middleware;

use Closure;

use Illuminate\Contracts\Routing\TerminableMiddleware;

class ShutdownMiddleware implements TerminableMiddleware {

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
		if (!\App::runningInConsole() && \App::bound('veer')) {

			$timeToLoad = empty(app('veer')->statistics['loading']) ? 0 : app('veer')->statistics['loading'];
			
			if($timeToLoad > config('veer.loadingtime')) {

				$recollect = app('veer')->statistics();
				\Log::alert('Slowness detected: ' . $timeToLoad . ': ', $recollect);
				info('Queries: ', \DB::getQueryLog());
			}

			app('veer')->tracking();

			if(config('queue.default') == 'qdb') { app('veer')->queues(); }		
		} 
	}
	
}
