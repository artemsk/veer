<?php
namespace Veer\Lib;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;
use Veer\Models\Component;

class VeerApp {
    
    const VEERVERSION = '0.0.2';
    
    public $siteId;
    
    public $siteUrl;
    
    public $siteConfig = array();    
    
    public $statistics;
    
    /**
     * Construct VeerApp.
     *
     * @return void
     */
    public function __construct()
    {        
        $this->siteUrl = $this->siteUrl();

        $siteDb = $this->isSiteAvailable($this->siteUrl);
        
        $this->saveConfiguration($siteDb); 
    }
           
    /**
     * Get Site Url and do some cleaning
     *
     * @return $url
     */   
    protected function siteUrl() 
    {        
        $url = "http://" . strtr(Request::server('HTTP_HOST') . Request::server('PHP_SELF'), array(
          "www." => "",
          "index.php" => "",
        ));
        
        if(ends_with($url, "/")) { $url = substr($url,0,-1); }
         
        return $url;
    }        
        
    /**
     * Check if this site's url exists in database & return its id
     *
     * @param $siteUrl
     * @return $siteDb
     */   
    protected function isSiteAvailable($siteUrl)
    {
        $siteDb = \Veer\Models\Site::where('url', '=', $siteUrl)
            ->where('on_off', '=', '1')->remember(10)->firstOrFail();

        if($siteDb->redirect_on == 1 && $siteDb->redirect_url != "") {
            $siteDb = \Veer\Models\Site::where('url', '=', $siteDb->redirect_url)
            ->where('on_off', '=', '1')->remember(10)->firstOrFail();  // TODO: test     
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
        Config::set('veer.mainurl',$siteDb->url);
        Config::set('veer.site_id',$siteDb->id);
        
        $this->siteId = $siteDb->id;
        
        //if(Cache::has('site_categories')) {} else {
        //Cache::add('site_categories', $siteDb->categories->lists('id','id'), 1); } 
        
        $siteConfig = \Veer\Models\Configuration::where('sites_id','=',$siteDb->id)->remember(1)->lists('conf_val', 'conf_key');

        $this->siteConfig = $siteConfig;
    }
    
    /**
     * Register site's components based on current route name
     *
     * @param $routeName
     * @return $data
     */     
    public function registerComponents($routeName) 
    {       
        $c = Component::validComponents($this->siteId, $routeName)->remember(1)->get();
        $data = array();
        
        foreach($c as $component) {    
            switch($component->components_type) {
                
                case "functions":
                        $data[$component->components_src] = $this->loadComponentClass($component->components_src); 
                        $data['output'] = object_get($data[$component->components_src],'data');
                    break;
                    
                case "pages";
                        $data['#page_'.$component->components_src] = 
                            \Veer\Models\Page::find($component->components_src); // do not perfom sitevalidation as a rule exception (?)
                    break;
                
                default:
                    break;           
            }            
        }  
        
        return $data;
    } 
 
    /**
     * Loading custom classes for components
     *
     * @param $className
     * @return object $className
     */ 
    protected function loadComponentClass($className) 
    {        
        $classFullName = "\Veer\Lib\Components\\" . $className;
        
        if(!class_exists($classFullName)) {  
            
            $pathComponent = app_path()."/lib/components/".$className.".php";
                           
            if(file_exists($pathComponent)) { require $pathComponent; }
        }  
        
        if(class_exists($classFullName)) { return new $classFullName; }        
    }
    
    /**
     * Collection statistics
     *
     * @return void
     */ 
    public function statistics()
	{
		$this->statistics['queries'] = count(\Illuminate\Support\Facades\DB::getQueryLog());
                
                $this->statistics['loading'] = round(microtime(true)-LARAVEL_START,4);
                
                $this->statistics['memory'] = number_format(memory_get_usage());
                
                $this->statistics['version'] = VeerApp::VEERVERSION;
                
                return $this->statistics;
	}    
        
    
}
