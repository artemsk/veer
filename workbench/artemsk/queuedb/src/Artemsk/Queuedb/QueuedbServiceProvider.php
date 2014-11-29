<?php namespace Artemsk\Queuedb;

use Artemsk\Queuedb\QdbConnector;
use Artemsk\Queuedb\QdbCommand;
use Illuminate\Support\ServiceProvider;

class QueuedbServiceProvider extends ServiceProvider {

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
		$this->registerQdbCommand($this->app);
	}
	
	/**
	 * 
	 *
	 * @return void
	 */
    public function boot()
    {
        $this->registerQdbConnector($this->app['queue']);

        $this->commands('command.queue.qdb');
    }
	
	/**
	 * 
	 *
	 * @return void
	 */	
    protected function registerQdbCommand($app)
    {
        $app['command.queue.qdb'] = $app->share(function ($app) {
             return new QdbCommand();
        });
    }
	
	/**
	 * 
	 *
	 * @return void
	 */	
    protected function registerQdbConnector($manager)
    {
        $manager->addConnector('qdb', function () {
            return new QdbConnector();
        });
    }
	
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('command.queue.qdb');
	}

}
