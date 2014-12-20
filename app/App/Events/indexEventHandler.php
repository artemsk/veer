<?php namespace Veer\Lib\Events;

class indexEventHandler {

	function __construct()
	{
//
	}

	/**
	 * Handle user login events.
	 */
	public function onShutdown($event = null)
	{
//
	}

	/**
	 * Register the listeners for the subscriber.
	 *
	 * @param Illuminate\Events\Dispatcher $events
	 * @return array
	 */
	public function subscribe($events = null)
	{
		$events->listen('index.footer', '\Veer\Lib\Events\indexEventHandler@onShutdown');
	}

}
