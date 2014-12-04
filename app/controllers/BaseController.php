<?php

class BaseController extends Controller {

	/* get instance of veer app */
	protected $veer;
	
	/* global template var */
	protected $template;

	/* save view for caching */
	protected $view;	
	

	public function __construct()
	{
		$this->veer = app('veer');

		$this->veer->loadedComponents['template'] = $this->template = $this->veer->template =  
			array_get($this->veer->siteConfig, 'TEMPLATE', config('veer.template'));
				
		$data = $this->veer->registerComponents(Route::currentRouteName());

		if($this->veer->loadedComponents) {
			$this->veer->loadedComponents = array_merge($this->veer->loadedComponents, $data);
		} else {
			$this->veer->loadedComponents = $data;
		}

		$this->veer->statistics();	
	}

	public function __destruct()
	{
		/**
		 *  Caching Html Pages
		 *  tweak with Auth::getName() instead of Auth::check() @testing
		 */
		if (is_object($this->view) && config('veer.htmlcache_enable') == true && !auth_check_session()) { 

			$cache_url = cache_current_url_value();

			$expiresAt = now(24, 'hours'); 
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