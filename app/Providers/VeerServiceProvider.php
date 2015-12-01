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

        $this->registerVeerAdmin();
	}
			
	/**
	 * Register the Veer Initialization.
	 *
	 * @return void
	 */
	
	public function registerVeerApp()
	{
		$this->app->singleton('veer', function() { return new \Veer\Services\VeerApp; });
	}
	
	/**
	 * Register the Veer Shop
	 *
	 * @return void
	 */	
	public function registerVeerShop()
	{
		$this->app->singleton('veershop', function() { return new \Veer\Services\VeerShop; });
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
		return ['veer', 'veershop', 'veeradmin']; //, 'command.veer.install');
	}	
	
}
