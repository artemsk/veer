<?php namespace Veer\Commands;

use Veer\Commands\Command;

use Illuminate\Contracts\Bus\SelfHandling;

class SendEmailCommand extends Command implements SelfHandling {

	protected $view;
	
	protected $data;
	
	protected $from;
	
	protected $to;
	
	protected $subject;
	
	//public $queued = true; // TODO: queued and not queued sending?
	
	/**
	 * Create a new command instance.
	 *
	 */
	public function __construct($view, $data, $subject = null, $to = null, $from = null, $siteId = null )
	{	
		$this->view = $view;
		
		$this->data = $data;
		
		$this->from = !empty($from) ? $from : $this->getEmailFrom($siteId);
		
		$this->to = $to;
		
		$this->subject = $subject;
	}

	/**
	 * Execute the command.
	 *
	 */
	public function handle()
	{
		if(empty($this->to)) return false;
		
		if(!is_array($this->to)) return $this->sendEmail($this->to);
		
		foreach($this->to as $to) $this->sendEmail($to);
	}

	/**
	 * Get 'From' field values for specific site
	 */
	protected function getEmailFrom($siteId = null)
	{
		return array(
			"address" => db_parameter("EMAIL_ADDRESS", config("mail.from.address"), $siteId),
			"name" => db_parameter("EMAIL_NAME", config("mail.from.name"), $siteId)
		);
	}
	
	/**
	 * Basic Email Send Queue
	 */
	public function sendEmail($to)
	{
		return \Mail::queue($this->view, $this->data, function($message) use ($to) {
			
			if(!empty($this->from)) $message->from($this->from['address'], $this->from['name']);
			
			$message->to($to);
			
			if(!empty($this->subject)) $message->subject($this->subject);
			
		});
	}
	
}
