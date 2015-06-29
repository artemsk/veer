<?php namespace Veer\Providers;

use Illuminate\Support\ServiceProvider;

class VeerAdminServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;
	
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
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
	}
	
	/**
	 * Register the Veer Admin
	 *
	 * @return void
	 */	
	public function registerVeerAdmin()
	{
		$this->app->singleton('veeradmin', function() { return new \Veer\Administration\VeerAdmin; });
	}
			
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('veeradmin');
	}		
	
}
