<?php
namespace Veer\Lib;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;

class VeerApp {
    
    const VEERVERSION = '0.0.2';
    
    public $siteId;
    
    public $siteUrl;
    
    public $siteConfig = array();
    
    
    public function __construct()
    {
        $this->siteUrl = $this->siteUrl();

        $siteDb = $this->isSiteAvailable($this->siteUrl);
        
        $this->saveConfiguration($siteDb);   
    }
    
        
    protected function siteUrl() 
    {        
        $url = "http://" . strtr(Request::server('HTTP_HOST') . Request::server('PHP_SELF'), array(
          "www." => "",
          "index.php" => "",
        ));
        
        if(ends_with($url, "/")) { $url = substr($url,0,-1); }
         
        return $url;
    }        
        
 
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
    
    
    protected function saveConfiguration($siteDb)
    {
        Config::set('veer.mainurl',$siteDb->url);
        Config::set('veer.site_id',$siteDb->id);
        
        $this->siteId = $siteDb->id;
        
        //if(Cache::has('site_categories')) {} else {
        //Cache::add('site_categories', $siteDb->categories->lists('id','id'), 1); } 
        
        $siteConfig = \Veer\Models\Configuration::where('sites_id','=',$siteDb->id)->remember(1)->lists('conf_val', 'conf_key');
        
        if(!isset($siteConfig['TEMPLATE'])) { $siteConfig['TEMPLATE'] = Config::get('veer.template'); }
        
        Config::set('veer.site_config',$siteConfig);   

        $this->siteConfig = $siteConfig;
    }
    
    
}
