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
                $this->jsonResponseCheck();

		$this->veer = app('veer');

		$this->veer->routePrepare(\Route::currentRouteName());
		
		$this->template = $this->veer->template;

		$this->middleware('early.view');
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
	 * common index view generator
	 */
	protected function viewIndex($type, $object, $check = true)
	{		
		if(!is_object($object) && $check) { return \Redirect::route('index'); }
                
		$view = viewx($this->template . '.' . $type, array(
			$type => $object,
			"template" => $this->template
		)); 

		$this->view = $view; 

		return $view;
	}

        // check for json tokens and json request. TODO: test for speed
        protected function jsonResponseCheck()
        {
            if(\Input::has('json')) {
                
                $jsonToken = json_decode("[".db_parameter('JSON_TOKENS')."]");
                if(in_array(\Input::get('json'), $jsonToken)) app('veer')->siteConfig['RENDER_JSON'] = true;
            } 
        }

}
