<?php

class BaseController extends Controller {

	/* get instance of veer app */
	protected $veer;

	/* save view for caching */
	protected $view;

	public function __construct()
	{
		$this->veer = app('veer');

		$data = $this->veer->registerComponents(Route::currentRouteName());

		$this->veer->loadedComponents = $data;

		$this->veer->loadedComponents['template'] = $this->veer->template = 
			array_get($this->veer->siteConfig, 'TEMPLATE', Config::get('veer.template'));

		$this->veer->statistics();		
	}

	public function __destruct()
	{
		/**
		 *  Caching Html Pages
		 *  tweak with Auth::getName() instead of Auth::check() @testing
		 */
		if (is_object($this->view) && Config::get('veer.htmlcache_enable') == true && !auth_check_session()) { 

			$cache_url = cache_current_url_value();

			$expiresAt = Carbon\Carbon::now()->addHours(24);
			Cache::has($cache_url) ?: Cache::add($cache_url, $this->view->__toString(), $expiresAt);
		}
	}

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}      

}