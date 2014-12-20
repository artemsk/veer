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
		$this->registerCommands($this->app);
		
		$this->registerVeerApp();
		
		$this->registerVeerQueryBuilder();
		
		$this->registerVeerShop();
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
		
		$this->commands('command.veer.install');
		
		$this->commands('command.veer.publish');
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
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('veer', 'veerdb', 'veershop', 'command.veer.install', 'command.veer.publish');
	}	
	
	/**
	 * 
	 *
	 * @return void
	 */	
    protected function registerCommands($app)
    {
        $app['command.veer.install'] = $app->share(function ($app) {
             return new \FirstThingCommand();
        });
        $app['command.veer.publish'] = $app->share(function ($app) {
             return new \PublishVeerCommand();
        });		
    }	
	
}
