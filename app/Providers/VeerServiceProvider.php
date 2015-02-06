<?php namespace Veer\Providers;

use Illuminate\Support\ServiceProvider;

class VeerServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
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
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerVeerApp();
		
		$this->registerVeerShop();
	}
			
	/**
	 * Register the Veer Initialization.
	 *
	 * @return void
	 */
	
	public function registerVeerApp()
	{
		$this->app->bindShared('veer', function() { return new \Veer\Services\VeerApp; });
	}
	
	/**
	 * Register the Veer Shop
	 *
	 * @return void
	 */	
	public function registerVeerShop()
	{
		$this->app->bindShared('veershop', function() { return new \Veer\Services\VeerShop; });
	}
			
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('veer', 'veershop'); //, 'command.veer.install');
	}	
	
}
