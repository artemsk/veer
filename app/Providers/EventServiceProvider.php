<?php

namespace Veer\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
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

		\Event::listen('veer.message.center', function($message) {
            
            app('veer')->loadedComponents['veer_message_center'][] = $message;
            \Session::flash('veer_message_center', app('veer')->loadedComponents['veer_message_center']);
        });
    }
}
