<?php namespace Veer\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller extends BaseController {

	use DispatchesCommands, ValidatesRequests;

	/* get instance of veer app */
	protected $veer;
	
	/* global template var */
	protected $template;

	/* save view for caching */
	protected $view;	
	

	public function __construct()
	{
		$this->veer = app('veer');

		$this->veer->routePrepare(\Route::currentRouteName());
		
		$this->template = $this->veer->template;
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
			\Cache::has($cache_url) ?: \Cache::add($cache_url, $this->view->__toString(), $expiresAt);
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
			$this->layout = \View::make($this->layout);
		}
	}     
	
	/**
	 * Common Index page for entities
	 */
	protected function viewIndex($type, $object)
	{		
		if(!is_object($object)) { return \Redirect::route('index'); }
                
		$view = view($this->template . '.' . $type, array(
			$type => $object,
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		)); 

		$this->view = $view; 

		return $view;
	}
}
