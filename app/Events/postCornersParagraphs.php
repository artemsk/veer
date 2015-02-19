<?php namespace Veer\Events;

use Veer\Events\Event;

use Illuminate\Queue\SerializesModels;

class postCornersParagraphs extends Event {

	use SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}
	
	public function parseParagraphs($text = null)
	{
		
	}

	public function subscribe($events = null)
	{
		$events->listen('post.corners.parse.paragraphs', '\Veer\Events\postCornersParagraphs@parseParagraphs');
	}	
}
