<?php namespace Veer\Commands;

use Veer\Commands\Command;

use Illuminate\Contracts\Bus\SelfHandling;

class TrackingUserCommand extends Command implements SelfHandling {

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Execute the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		if(config('veer.history_refs')) $this->trackingReferrals();

		if(config('veer.history_urls')) $this->trackingUrls();

		if(config('veer.history_ips')) $this->trackingIps();
	}
	
	/**
	 * Tracking Referals.
	 *
	 * 
	 */
	protected function trackingReferrals()
	{
		$past = \URL::previous();
		
		if(!str_contains($past, url())) $this->trackingToFile('referrals', array($past));
	}
	
	/**
	 * Tracking Urls for Auth.User.
	 * 
	 * 
	 */
	protected function trackingUrls()
	{
		if(!auth_check_session()) { return; }
		
		$this->trackingToFile('urls', array(
			\Auth::id(), app('url')->current(), \Route::currentRouteName()
		));
	}
	
	/**
	 * Tracking Ips - use for Debugging.
	 * 
	 * 
	 */
	protected function trackingIps()
	{
		$this->trackingToFile('ips', array(
			\Request::getClientIp(), url(), \Route::currentRouteName()
		));
	}	

	/**
	 * Appending statistics to file.
	 * 
	 *
	 */
	protected function trackingToFile($type, $data)
	{
		\File::append(config('veer.history_path') . '/' . $type . '.' . date('Y.W', time()) . '.txt',
			implode('|', $data) . "\r\n"
		);
	}	

}
