<?php

/**
 * Global Events
 *
 * @params
 * @return
 */

Event::listen('veer.message.center', function($message)
{
	app('veer')->loadedComponents['veer_message_center'] = 
		view('components.events-veer-message-center', array('message' => $message));
});