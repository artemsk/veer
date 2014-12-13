<?php namespace Veer\Lib;

use Illuminate\Support\ServiceProvider;

class VeerServiceProvider extends ServiceProvider {

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
		$this->registerVeerApp();
		
		$this->registerVeerQueryBuilder();
		
		$this->registerVeerShop();
		
		$this->registerVeerAdmin();
	}
	
	/**
	 * Boot the service provider.
	 * We don't need it now if we're gonna run some tasks in console.
	 *
	 * @return void
	 */
	public function boot()
	{
		if ( !($this->app->runningInConsole()) ) {
			$this->app['veer']->run();	
		}
	}
	
	/**
	 * Register the Veer Initialization.
	 *
	 * @return void
	 */
	
	public function registerVeerApp()
	{
		$this->app->bindShared('veer', function() { return new VeerApp; });
	}
	
	/**
	 * Register the Veer Query Builder
	 *
	 * @return void
	 */	
	public function registerVeerQueryBuilder()
	{
		$this->app->bindShared('veerdb', function() { return new VeerDb; });
	}

	/**
	 * Register the Veer Shop
	 *
	 * @return void
	 */	
	public function registerVeerShop()
	{
		$this->app->bindShared('veershop', function() { return new VeerShop; });
	}
		
	/**
	 * Register the Veer Admin
	 *
	 * @return void
	 */	
	public function registerVeerAdmin()
	{
		$this->app->bindShared('veeradmin', function() { return new VeerAdmin; });
	}
	
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('veer', 'veerdb', 'veershop', 'veeradmin');
	}	
	
}
