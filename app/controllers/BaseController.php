<?php

class BaseController extends Controller {
    
        protected $veer;
        
        protected $view;
        
        public function __construct() 
        {
                $this->veer = App::make('veer');
                
                $data = $this->veer->registerComponents(Route::currentRouteName());     
                
                $this->veer->loadedComponents = $data;
                
                $this->veer->loadedComponents['template']= $this->veer->template = 
                        array_get($this->veer->siteConfig, 'TEMPLATE', Config::get('veer.template'));

                $this->veer->statistics();
        }

        
        public function __destruct() 
        {
            /**
             *  Caching Html Pages
             */
            if(is_object($this->view) && Config::get('veer.htmlcache_enable') == true) {
                
                $cache_url = cache_current_url_value();
    
                $expiresAt = Carbon\Carbon::now()->addHours(24);

                Cache::add($cache_url, $this->view->__toString(), $expiresAt);
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
        
        /**
	 * Setup common template variables
	 *
	 * @return void
	 */
	protected function setupVariables()
	{
		$this->veer->queries = count(DB::getQueryLog());
                
                $this->veer->loading = round(microtime(true)-LARAVEL_START,4);
                
                $this->veer->memory = number_format(memory_get_usage());
                
                $this->veer->version = \Veer\Lib\VeerApp::VEERVERSION;
	}        

}