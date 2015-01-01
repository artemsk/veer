<?php namespace Veer;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Artemsk\Queuedb\Job;
use Artemsk\Queuedb\QdbJob;
use Veer\Models\Component;

class VeerApp {

	/**
	 *  Veer Layer.
	 * 
	 */
	const VEERVERSION = '0.6.0-alpha';

	/**
	 *  Booted?
	 * 
	 */	
	public $booted = false;
	
	/**
	 *  Current url 
	 * 
	 */	
	public $siteId;	

	/**
	 *  Site Id associated with current url.
	 * 
	 */	
	public $siteUrl;	

	/**
	 *  Database dynamic configuration
	 * 
	 */	
	public $siteConfig = array();	

	/**
	 *  Statistics
	 * 
	 */		
	public $statistics;
	
	/**
	 *  Loaded components for current route
	 * 
	 */		
	public $loadedComponents;	

	/**
	 * Construct the VeerApp.
	 *
	 * 
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Boot the VeerApp.
	 *
	 * @return void
	 */
	public function run()
	{		
		$this->booted = true;

		$this->siteUrl = $this->siteUrl();
		
		$siteDb = $this->isSiteAvailable($this->siteUrl);

		$this->saveConfiguration($siteDb);	
	}
		
	
	/**
	 * Get Site Url with some cleaning. 
	 * Mirrors/sites should be on the same level as Veer directory.
	 *
	 * @return $url
	 */
	protected function siteUrl()
	{ 
		/* Preserve old method for history: 
		[1] "http://" . strtr(Request::header('host') . Request::server('PHP_SELF')
		
		[2] $segments = explode('/', Request::server('REQUEST_URI'));		
		$segments = array_values(array_filter($segments, function($v) { return $v != ''; }));		
		$url = Request::getSchemeAndHttpHost() . 
			(empty($segments[0]) ? null : "/" . $segments[0]). 
			(empty($segments[1]) ? null : "/" . $segments[1]);
		*/
		
		$url = strtr(url(), array(
				"www." => "",
				"index.php/" => "",
				"index.php" => "",
		));

		if (ends_with($url, "/")) {
			$url = substr($url, 0, -1);
		}
		
		return $url;
	}

	/**
	 * Check if this site's url exists in database & return its id
	 * Because of caching turning on/off and other changes
	 * come to effect after cleaning cache only.
	 *
	 * @param $siteUrl
	 * @return $siteDb
	 */
	protected function isSiteAvailable($siteUrl)
	{
		$siteDb = \Veer\Models\Site::where('url', '=', $siteUrl)
				->where('on_off', '=', '1')->rememberForever()->firstOrFail();

		if ($siteDb->redirect_on == 1 && $siteDb->redirect_url != "") {
			$siteDb = \Veer\Models\Site::where('url', '=', $siteDb->redirect_url)
					->where('on_off', '=', '1')->rememberForever()->firstOrFail();  // TODO: test     
		}

		return $siteDb;
	}

	/**
	 * Loading site's configuration from database
	 *
	 * @param $siteDb
	 * @return void
	 */
	protected function saveConfiguration($siteDb)
	{
		Config::set('veer.mainurl', $siteDb->url);
		Config::set('veer.site_id', $siteDb->id);

		$this->siteId = $siteDb->id;

		$siteConfig = \Veer\Models\Configuration::where('sites_id', '=', $siteDb->id)->remember(1440)->lists('conf_val', 'conf_key');

		$this->siteConfig = $siteConfig;
	}

	/**
	 * Register components & events based on current route name & site. It allows
	 * us to have different components and actions for different routes [and events 
	 * on different sites].
	 *
	 * @param $routeName
	 * @return $data
	 */
	public function registerComponents($routeName, $params = null)
	{
		$c = Component::validComponents($this->siteId, $routeName)->remember(1)->get();
		$data = array(); 
		$data['output'] = array();

		foreach ($c as $component) {
			switch ($component->components_type) {

				case "functions":
					$data['function_'.$component->components_src] = $this->loadComponentClass($component->components_src, $params);
					if(is_object($data['function_'.$component->components_src])) { 
						$data['output'] = array_merge($data['output'], (array)object_get($data['function_'.$component->components_src], 'data'));
						// now you have outputed data for templates.
					}
					break;

				case "events":
					$data['event_'.$component->components_src] = $this->loadComponentClass($component->components_src, $params, 'event');
					if(class_exists("\Veer\Events\\" . $component->components_src, false)) { 
						\Illuminate\Support\Facades\Event::subscribe("\Veer\Events\\" . $component->components_src); 
						// now you can fire these events in templates etc.
					}						
					break;
				
				case "pages";
					$data['page_' . $component->components_src] = \Veer\Models\Page::find($component->components_src); 
					// do not perfom sitevalidation as a rule exception (?)
					break;

				default:
					break;
			}
		}

		return $data;
	}

	/**
	 * Loading custom classes for components; event subscribers; queues
	 *
	 * @param $className
	 * @param $params - if needed
	 * @return object $className
	 */
	public function loadComponentClass($className, $params = null, $type = null)
	{ 
		/* Another vendor's component */
		if (starts_with($className, '\\')) {

			$classFullName = $className;
		} else {
			// detect: component or event 
			$classFullName = empty($type) ? ("\Veer\Components\\" . $className) : ("\Veer\Events\\" . $className);
			if($type == "queue") { $classFullName = "\Veer\Queues\\" . $className; }

			if (!class_exists($classFullName)) { 

				$pathComponent = ( empty($type) ? config("veer.components_path") : config("veer.events_path")) . "/" . $className . ".php";
				if($type == "queue") { $pathComponent = config("veer.queues_path") . $className . ".php"; }
				
				if (file_exists($pathComponent)) {
					require $pathComponent;
				} else { 
					return 'Class Not Found.';  }
			}
		}

		if (class_exists($classFullName, false) && empty($type)) {
			return new $classFullName($params);
		}
	}

	/**
	 * Collecting statistics
	 *
	 * @return void
	 */
	public function statistics()
	{ 
		$this->statistics['queries'] = count(\Illuminate\Support\Facades\DB::getQueryLog());

		$this->statistics['loading'] = round(microtime(true) - LARAVEL_START, 4);

		$this->statistics['memory'] = number_format(memory_get_usage());
		
		$this->statistics['mem.veer'] = number_format(memory_get_usage()-7125344); // ~initial memory used 

		$this->statistics['version'] = self::VEERVERSION;

		return $this->statistics;		
	}

	/**
	 * Tracking user's behavior
	 *
	 * @return void
	 */
	public function tracking()
	{ 
		!(config('veer.history.refs')) ? : $this->trackingReferrals();

		!(config('veer.history.urls')) ? : $this->trackingUrls();

		!(config('veer.history.ips')) ? : $this->trackingIps();		
	}
	
	/**
	 * Tracking Referals
	 *
	 * @params
	 * @return void
	 */
	protected function trackingReferrals()
	{
		$past = \Illuminate\Support\Facades\URL::previous();
		
		if(!str_contains($past, url())) { 			
			$f = date('Y.W', time());
			File::append(config('veer.history.path') . 
				'/referrals.' . $f . '.txt', $past . "\r\n" );
		}
	}
	
	/**
	 * Tracking Urls for Auth.User
	 *
	 * @params
	 * @return void
	 */
	protected function trackingUrls()
	{
		if(!auth_check_session()) { return; }
		
		$f = date('Y.W', time());
		File::append(config('veer.history.path') . '/urls.' . $f . '.txt', 
			\Illuminate\Support\Facades\Auth::id(). '|' . app('url')->current() . '|' .
			\Illuminate\Support\Facades\Route::currentRouteName() . "\r\n" );
	}
	
	/**
	 * Tracking Ips - use for Debugging.
	 *
	 * @params
	 * @return void
	 */
	protected function trackingIps()
	{
		$f = date('Y.W', time());
		File::append(config('veer.history.path') . '/ips.' . $f . '.txt', 
		\Illuminate\Support\Facades\Request::getClientIp(). '|' . url() . '|' .
			\Illuminate\Support\Facades\Route::currentRouteName() . "\r\n" );
	}	

	/**
	 * Running Queues: one job per request per minute (value from configuration).
	 * Only for 'qdb' driver as default
	 *
	 * @params
	 * @return void
	 */
	public function queues() 	
	{
		if(config('queue.default') == "qdb" && !\Cache::has('queue_checked')) { 
		
		$item = Job::where('status','<=','1')
			->where('scheduled_at','<=',date('Y-m-d H:i:00', time()))
			->orderBy('scheduled_at', 'asc')
			->first();
		
		if(is_object($item)) {		
		
			$job = new QdbJob(app(), $item);

				$job->fire(); 
		}
		\Cache::put('queue_checked', true, config('veer.repeatjob'));	
	    }		
	}

	/**
	 * Communications Send
	 * 
	 * @return bool
	 */
	public function communicationsSend( $options = array() )
	{
		\Event::fire('router.filter: csrf');
		
		$all = \Input::all();
		
		if(array_get($all, 'message', null) == null) return false;
		
		\Eloquent::unguard();
		
		if(array_get($all, 'fill.users_id', null) == null) array_set($all, 'fill.users_id', \Auth::id());		
		if(array_get($all, 'fill.sites_id', null) == null) array_set($all, 'fill.sites_id', app('veer')->siteId);		
		if(array_get($all, 'fill.url', null) == null) array_set($all, 'fill.url', app('url')->current());
		
		if(array_get($all, 'fill.users_id', null) != null)
		{
			if(array_get($all, 'fill.sender', null) == null) array_set($all, 'fill.sender', \Auth::user()->username);			
			if(array_get($all, 'fill.sender_phone', null) == null) array_set($all, 'fill.sender_phone', \Auth::user()->phone);			
			if(array_get($all, 'fill.sender_email', null) == null) array_set($all, 'fill.sender_email', \Auth::user()->email);
		}
		
		$message = new \Veer\Models\Communication;
		
		$message->fill( array_get($all, 'fill', null) );
		
		$message->public = array_get($all, 'checkboxes.public', 
			array_get($options, 'checkboxes.public', false)) ? true : false;
		
		$message->email_notify = array_get($all, 'checkboxes.email_notify', 
			array_get($options, 'checkboxes.email_notify', false)) ? true : false;
		
		$message->hidden = array_get($all, 'checkboxes.hidden', 
			array_get($options, 'checkboxes.hidden', false)) ? true : false;
		
		$message->intranet = array_get($all, 'checkboxes.intranet', 
			array_get($options, 'checkboxes.intranet', false)) ? true : false;
		
		$connected = array_get($all, 'connected', null) ;
		
		if(!empty($connected))
		{
			list($model, $id) = explode(":", $connected);
			
			$message->elements_type = elements($model);
			
			$message->elements_id = $id;
		}
		
		list($text, $emails, $recipients) = $this->parseMessage( array_get($all, 'message', null) );
		
		$message->message = $text;
		$message->recipients = json_encode($recipients);
		
		$message->save();
		
		if($message->email_notify == true || !empty($emails))
		{
			$this->message2mail($message->id);
		}
		
		return true;
	}
	
	/**
	 * Parse message
	 * 
	 * @param type $m
	 * @return string
	 */
	protected function parseMessage($m)
	{
		$emailsCollection = $usernamesCollection = array();
		
		$usernames = "/(@[^\\s]+)\\b/i"; 		
		$emails = "/(\\[[^\\s]+\\])/i"; 
		
		preg_match_all($emails, $m, $matches);
		
		$m = preg_replace($emails, "", $m);
		
		foreach($matches[0] as $match)
		{
			$emailsCollection[] = substr($match, 1, -1);
		}
		
		preg_match_all($usernames, $m, $matches);
		
		$m = preg_replace($usernames, "", $m);
		
		foreach($matches[0] as $match)
		{ 
			if(starts_with($match, "@:")) { $userId = substr($match, 2); }
			
			else
			{
				$userId = \Veer\Models\User::where('username','=', substr($match, 1))->pluck('id');
			}
			
			if(!empty($userId)) $usernamesCollection[] = $userId;
		}
		
		$m = preg_replace("/(\\s+)/i", " ", $m);
		
		return array( trim($m), $emailsCollection, $usernamesCollection );
	}
	
	/*
	 * Get unread timestamps
	 * for user & elements
	 * 
	 */
	public function getUnreadTimestamp($type)
	{
		$cacheName = "unread." . $type . "." . \Auth::id();
		
		return \Cache::get($cacheName, now());
	}
	
	/*
	 * Set unread timestamps
	 * 
	 */
	public function setUnreadTimestamp($type)
	{
		$cacheName = "unread." . $type . "." . \Auth::id();
		
		\Cache::forever($cacheName, now());
	}
	
	/**
	 * Sending mails queue
	 * 
	 */
	protected function message2mail($messageId)
	{
		//
	}
	
}
