<?php namespace Veer\Lib;

use Illuminate\Support\ServiceProvider;

class VeerServiceProvider extends ServiceProvider {

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
		$this->registerVeerApp();
		
		$this->registerVeerQueryBuilder();
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
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('veer', 'veerdb');
	}	
	
}
