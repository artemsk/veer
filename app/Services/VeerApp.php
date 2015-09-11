<?php

namespace Veer\Services;

use Illuminate\Support\Facades\Config;
use Veer\Models\Component;

class VeerApp
{
    /**
     *  Veer Layer.
     *
     */
    const VEERVERSION = 'v1.1.12';

    /**
     * Veer Core Url.
     * 
     */
    const VEERCOREURL = 'https://api.github.com/repos/artemsk/veer';

    /**
     *  Booted?
     *
     */
    public $booted = false;

    /**
     *  Current url. 
     * 
     */
    public $siteId;

    /**
     *  Site Id associated with current url.
     *
     */
    public $siteUrl;

    /**
     *  Database dynamic configuration.
     * 
     */
    public $siteConfig = array();

    /**
     *  Statistics.
     *
     */
    public $statistics;

    /**
     *  Loaded components for current route.
     * 
     */
    public $loadedComponents;

    /**
     *  Template variable.
     */
    public $template;

    /**
     *  Work only with site-specific entities.
     */
    public $isBoundSite = true;

    /**
     * Ajax return boolean
     */
    public $forceEarlyResponse = false;

    /**
     * Early View container
     */
    public $earlyResponseContainer;

    /**
     *  Cached Queries.
     */
    public $cachingQueries;

    /**
     * Construct the VeerApp.
     *
     * 
     * 
     */
    public function __construct()
    {
        $this->cachingQueries = new CachingQueries;
    }

    /**
     * is Site Filtered?
     *
     * 
     * 
     */
    public function isBoundSite()
    {
        return $this->isBoundSite;
    }

    /**
     * Boot the VeerApp.
     *
     *
     * 
     */
    public function run()
    {
        \DB::enableQueryLog(); // TODO: remove

        $this->siteUrl = $this->siteUrl();

        $siteDb = $this->isSiteAvailable($this->siteUrl);

        $this->saveConfiguration($siteDb);
        
        $this->booted = true;
    }

    /**
     * Get Site Url with some cleaning. 
     * Mirrors/sites should be on the same level as Veer directory.
     *
     * 
     * @return string $url
     */
    protected function siteUrl()
    {
        $url = strtr(url(),
            array(
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
     * Check if this site's url exists in database & return its id.
     * Because of caching turning on/off and other changes
     * come to effect after cleaning cache only.
     *
     * @param string $siteUrl
     * @return \Veer\Models\Site $siteDb
     */
    protected function isSiteAvailable($siteUrl)
    {
        $this->cachingQueries->make(\Veer\Models\Site::where('url', '=',
                $siteUrl)->where('on_off', '=', '1'));

        $siteDb = $this->cachingQueries->rememberForever('firstOrFail');

        if ($siteDb->redirect_on == 1 && $siteDb->redirect_url != "") {
            $siteDb = (new CachingQueries(\Veer\Models\Site::where('url', '=',
                        $siteDb->redirect_url)
                    ->where('on_off', '=', '1')))->rememberForever('firstOrFail');
        }

        return $siteDb;
    }

    /**
     * Loading site's configuration from database.
     *
     *
     *
     */
    protected function saveConfiguration($siteDb)
    {
        Config::set('veer.mainurl', $siteDb->url);
        Config::set('veer.site_id', $siteDb->id);

        $this->siteId = $siteDb->id;

        $this->cachingQueries->make(\Veer\Models\Configuration::where('sites_id',
                '=', $siteDb->id));

        $this->siteConfig = $this->cachingQueries->lists('conf_val', 'conf_key',
            1440);
    }

    /**
     * Load route components:
     * methods (immediate actions), events, queues etc.
     *
     *
     */
    public function routePrepare($routeName)
    {
        $this->loadedComponents['template'] = $this->template = array_get($this->siteConfig,
            'TEMPLATE', config('veer.template'));

        $this->registerComponents($routeName);

        $this->statistics();
    }

    /**
     * Register components & events based on current route name & site. It allows
     * us to have different components and actions for different routes [and events 
     * on different sites].
     * 
     * 
     */
    public function registerComponents($routeName, $params = null)
    {
        $this->cachingQueries->make(Component::validComponents($this->siteId,
                $routeName));

        $c = $this->cachingQueries->remember(1, 'get');

        foreach ($c as $component) {
            $this->{camel_case('register '.$component->components_type)}($component->components_src,
                $params);
        }
    }

    /**
     * Register Functions.
     *
     * @param string $src
     * @param mixed $params
     */
    protected function registerFunctions($src, $params)
    {
        $this->loadedComponents['function'][$src] = $this->loadComponentClass($src,
            $params);
    }

    /**
     * Register Events.
     * 
     * @param string $src
     * @param mixed $params
     */
    protected function registerEvents($src, $params)
    {
        $classFullName = $this->loadComponentClass($src, $params, 'events');

        if (!empty($classFullName)) {

            \Illuminate\Support\Facades\Event::subscribe($classFullName);

            $this->loadedComponents['event'][$src] = true;
            // now you can fire these events in templates etc.
        }
    }

    /**
     * Register Pages
     * 
     * 
     * @param string $src
     */
    protected function registerPages($src)
    {
        $this->loadedComponents['page'][$src] = \Veer\Models\Page::find($src);
    }

    /**
     * Loading custom classes for components, event subscribers.
     * 
     * 
     */
    protected function loadComponentClass($className, $params = null, $type = "components")
    {
        /* Another vendor's component */
        if (starts_with($className, '\\')) {
            $classFullName = $className;
        }

        else {
            $classFullName = "\Veer\\".ucfirst($type)."\\".$className;

            /* if (!class_exists($classFullName)) $this->loadClassFromPath($className, $type);
             *
             * we do not need this for now because we use psr-4 so all classes inside App folder
             * autoloaded by composer
             */
        }
        
        return $this->instantiateClass($classFullName, $params, $type);
    }

    /**
     * Get path for components and require them.
     *
     * @deprecated
     */
    protected function loadClassFromPath($className, $type)
    {
        $pathComponent = base_path()."/".config("veer.".$type."_path")."/".$className.".php";

        if (file_exists($pathComponent)) {
            require $pathComponent;
        }
    }

    /**
     * Instantiate class.
     * 
     * we instantiate components at once
     * for events type classes we only check if file was loaded then
     * wait till its called
     */
    protected function instantiateClass($classFullName, $params = null, $type = null)
    {
        if(class_exists($classFullName))
        {
            if($type == "components") return new $classFullName($params);

            return $classFullName;
        }
    }

    /**
     * Collecting statistics.
     *
     * 
     */
    public function statistics()
    {
        $this->statistics['queries'] = count(\Illuminate\Support\Facades\DB::getQueryLog());

        $this->statistics['loading'] = round(microtime(true) - LARAVEL_START, 4);

        $this->statistics['memory'] = number_format(memory_get_usage());

        $this->statistics['version'] = self::VEERVERSION;

        return $this->statistics;
    }
}
