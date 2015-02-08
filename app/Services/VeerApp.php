<?php namespace Veer\Services;
	
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Artemsk\Queuedb\Job;
use Artemsk\Queuedb\QdbJob;
use Veer\Models\Component;

class VeerApp {

	use TemporaryTrait;
	
	/**
	 *  Veer Layer.
	 * 
	 */
	const VEERVERSION = 'v1.1.4';

	/** 
	 * Veer Core Url
	 * 
	 */
	const VEERCOREURL = 'https://api.github.com/repos/artemsk/veer';
	
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
	 *  Template variable
	 */
	public $template;
	
	/**
	 *  Work only with site-specific entities
	 */
	public $isBoundSite = true;
		
	/**
	 *  Cached Queries
	 */
	public $cachingQueries;
	
	/**
	 * Construct the VeerApp.
	 *
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->cachingQueries = new CachingQueries;
	}

	/**
	 * is Site Filtered?
	 * @return bool
	 */
	public function isBoundSite() 
	{
		return $this->isBoundSite;
	}
	
	/**
	 * Boot the VeerApp.
	 *
	 * @return void
	 */
	public function run()
	{		
		\DB::enableQueryLog(); // TODO: remove
		
		$this->booted = true;

		$this->siteUrl = $this->siteUrl();
		
		$siteDb = $this->isSiteAvailable($this->siteUrl);

		$this->saveConfiguration($siteDb);	
	}
		
	
	/**
	 * Get Site Url with some cleaning. 
	 * Mirrors/sites should be on the same level as Veer directory.
	 *
	 * @return string $url
	 */
	protected function siteUrl()
	{ 		
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
	 * @param string $siteUrl
	 * @return \Veer\Models\Site $siteDb
	 */
	protected function isSiteAvailable($siteUrl)
	{
		$this->cachingQueries->make(\Veer\Models\Site::where('url', '=', $siteUrl)->where('on_off', '=', '1'));
		
		$siteDb = $this->cachingQueries->rememberForever('firstOrFail');

		if ($siteDb->redirect_on == 1 && $siteDb->redirect_url != "") {

			$siteDb = (new CachingQueries(\Veer\Models\Site::where('url', '=', $siteDb->redirect_url)
					->where('on_off', '=', '1')))->rememberForever('firstOrFail');
		}

		return $siteDb;
	}

	/**
	 * Loading site's configuration from database
	 *
	 * @return void
	 */
	protected function saveConfiguration($siteDb)
	{
		Config::set('veer.mainurl', $siteDb->url);
		Config::set('veer.site_id', $siteDb->id);

		$this->siteId = $siteDb->id;

		$this->cachingQueries->make(\Veer\Models\Configuration::where('sites_id', '=', $siteDb->id));
		
		$this->siteConfig = $this->cachingQueries->lists('conf_val', 'conf_key', 1440);
	}

	/**
	 * Load route components:
	 * methods (immediate actions), events, queues etc.
	 */
	public function routePrepare($routeName)
	{
		$this->loadedComponents['template'] = $this->template =  
			array_get($this->siteConfig, 'TEMPLATE', config('veer.template'));
				
		$this->registerComponents($routeName);

		$this->statistics();
	}
	
	/**
	 * Register components & events based on current route name & site. It allows
	 * us to have different components and actions for different routes [and events 
	 * on different sites].
	 */
	public function registerComponents($routeName, $params = null)
	{
		$this->cachingQueries->make(Component::validComponents($this->siteId, $routeName));
		
		$c = $this->cachingQueries->remember(1, 'get'); 
				
		foreach ($c as $component) 
		{	
			 $this->{camel_case('register '.$component->components_type)}($component->components_src, $params);
		}
	}

	protected function registerFunctions($src, $params)
	{
		$this->loadedComponents['function_' . $src] = $this->loadComponentClass($src, $params);
	}
	
	protected function registerEvents($src, $params)
	{
		$this->loadComponentClass($src, $params, 'events');
		
		if(class_exists("\Veer\Events\\" . $src, false)) { 
			\Illuminate\Support\Facades\Event::subscribe("\Veer\Events\\" . $src); 
			
			$this->loadedComponents['event_' . $src] = true;
			// now you can fire these events in templates etc.
		}	
	}
	
	protected function registerPages($src)
	{
		$this->loadedComponents['page_' . $src] = \Veer\Models\Page::find($src); 
	}
	
	/**
	 * Loading custom classes for components; event subscribers; queues
	 */
	public function loadComponentClass($className, $params = null, $type = "components")
	{ 
		/* Another vendor's component */
		if (starts_with($className, '\\')) return $this->instantiateClass($className, $params, $type);
		
		$classFullName = "\Veer\\" . ucfirst($type) . "\\" . $className;
		
		if (!class_exists($classFullName)) $this->loadClassFromPath($className, $type);
			
		return $this->instantiateClass($classFullName, $params, $type);
	}

	protected function loadClassFromPath($className, $type)
	{
		$pathComponent = base_path() . "/". config("veer." . $type . "_path") . "/" . $className . ".php";
	
		if (file_exists($pathComponent)) { require $pathComponent; } 
	}
	
	protected function instantiateClass($classFullName, $params = null, $type = null)
	{
		if (class_exists($classFullName, false) && $type == "components") 
		{
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
		!(config('veer.history_refs')) ? : $this->trackingReferrals();

		!(config('veer.history_urls')) ? : $this->trackingUrls();

		!(config('veer.history_ips')) ? : $this->trackingIps();		
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
			File::append(config('veer.history_path') . 
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
		File::append(config('veer.history_path') . '/urls.' . $f . '.txt', 
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
		File::append(config('veer.history_path') . '/ips.' . $f . '.txt', 
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
	
}
