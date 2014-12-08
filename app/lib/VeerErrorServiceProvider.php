<?php namespace Veer\Lib;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;

class VeerErrorServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->registerCommonErrorHandler();
		$this->register404ErrorHandler();
		$this->registerDbErrorHandler();
		$this->registerModelErrorHandler();		
	}

	/**
	 * Common
	 * 
	 * @return void
	 */	
	protected function registerCommonErrorHandler()
	{
		$this->app->error(function(\Exception $exception, $code) {
			Log::error($exception);
		});
	}

	/**
	 * 404
	 *
	 * @return Redirect::route
	 */
	protected function register404ErrorHandler()
	{
		$this->app->error(function(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $exception) {
			
			Log::error("URL Not found ". app('url')->full());

			return Redirect::route('404');
		});
	}

	/**
	 * Database connection error
	 * 
	 * @return Response::make
	 */
	protected function registerDbErrorHandler()
	{
		$this->app->error(function(\PDOException $exception) {
			
			Log::error("Error connecting to database: " . $exception->getMessage());

			$cache_url = cache_current_url_value();

			if (Cache::has($cache_url)) {

				$cachedPage = Cache::get($cache_url);
				return view('dummy', array('cachedPage' => $cachedPage));
			} else {
				return response("Error connecting to database. Please come back later. ", 503);
			}
		});
	}

	/**
	 * Handler for findOrFail() method
	 * Used only for detecting site.
	 * 
	 * @return Response::make
	 */
	protected function registerModelErrorHandler()
	{
		$this->app->error(function(\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
			
			Log::error("Unable to find site: " . app('url')->full() . " " . $exception->getMessage());

			return response("Be right back!", 503);
		});
	}

}
