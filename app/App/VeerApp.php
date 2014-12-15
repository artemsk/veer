<?php namespace Veer\Lib;

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
	const VEERVERSION = '0.3.0-alpha';

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
					if(class_exists("\Veer\Lib\Events\\" . $component->components_src, false)) { 
						\Illuminate\Support\Facades\Event::subscribe("\Veer\Lib\Events\\" . $component->components_src); 
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
			$classFullName = empty($type) ? ("\Veer\Lib\Components\\" . $className) : ("\Veer\Lib\Events\\" . $className);
			if($type == "queue") { $classFullName = "\Veer\Lib\Queues\\" . $className; }

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
		$item = Job::where('status','<=','1')->orderBy('scheduled_at', 'asc')->remember(config('veer.repeatjob'))->first();
		if(is_object($item)) {		
		
			$job = new QdbJob(app(), $item);

			$job->fire();
		}
	}

}
