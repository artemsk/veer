<?php namespace Veer\Administration;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;
use Veer\Services\VeerApp;

trait Configuration {
		
	protected $action_performed = array();	
	
	protected function checkLatestVersion()
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->get(VeerApp::VEERCOREURL . "/releases", array('verify' => false));
		$res = json_decode($response->getBody());
				
		return head($res)->tag_name;
	}
	
		
	/**
	 * update Etc
	 */
	public function updateEtc()
	{
		Event::fire('router.filter: csrf');
		
		$all = Input::all();
		$action = Input::get('action');
	
		if($action == "runRawSql" && array_get($all, 'freeFormSql') != null)
		{
			// TODO: warning! very dangerous!
			\DB::statement( array_get($all, 'freeFormSql') );
			Event::fire('veer.message.center', \Lang::get('veeradmin.etc.sql'));
			$this->action_performed[] = "RUN sql";
		}
	
		if(Input::get('actionButton') == "checkLatestVersion")
		{
			$latest = $this->checkLatestVersion();
			
			// for ajax calls
			if(app('request')->ajax()) {
				return view(app('veer')->template.'.elements.version', array(
					"latest" => $latest,
					"current" => VeerApp::VEERVERSION,
				));		
			}
		}
		
		if(Input::get('actionButton') == "sendPingEmail" && config('mail.from.address') != null)
		{
			\Mail::send('emails.ping', array(), function($message)
			{
				$message->to(config('mail.from.address'));
			});
		}
		
		if(Input::get('actionButton') == "clearTrashed" && Input::get('button') != null)
		{
			\Illuminate\Support\Facades\DB::table(Input::get('button'))
				->whereNotNull('deleted_at')->delete();
		}
		
		if(Input::get('actionButton') == "clearCache")
		{
			\Cache::flush();
		}
		
	}	
	
}
