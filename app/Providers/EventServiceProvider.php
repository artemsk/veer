<?php namespace Veer\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {

	/**
	 * The event handler mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
                'lock.for.edit' => [
                        'Veer\Events\adminLock'
                ],
	];

	/**
	 * Register any other events for your application.
	 *
	 * @param  \Illuminate\Contracts\Events\Dispatcher  $events
	 * @return void
	 */
	public function boot(DispatcherContract $events)
	{
		parent::boot($events);

		\Event::listen('veer.message.center', function($message)
		{
			app('veer')->loadedComponents['veer_message_center'][] = $message;
			
		});
		
		\Event::listen('veer.message.center.flush', function()
		{
			unset(app('veer')->loadedComponents['veer_message_center']);			
		});
	}

}
