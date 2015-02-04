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
		
		//$this->commands('command.veer.install');
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//$this->registerCommands($this->app);
		
		$this->registerVeerApp();
		
		$this->registerVeerQueryBuilder();
		
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
	 * Register the Veer Query Builder
	 *
	 * @return void
	 */	
	public function registerVeerQueryBuilder()
	{
		$this->app->bindShared('veerdb', function() { return new \Veer\Services\VeerDb; });
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
		return array('veer', 'veerdb', 'veershop'); //, 'command.veer.install');
	}	
	
	/**
	 * 
	 *
	 * @return void
	 */	
    protected function registerCommands($app)
    {
        $app['command.veer.install'] = $app->share(function ($app) {
             return new \Veer\Console\Commands\FirstThingCommand();
        });	
    }	
	

}
