<?php namespace Veer\Components;

class commonOutputJson {
    
    protected $urlTrigger = "_json";
    protected $dbTokensName = "JSON_TOKENS";
    protected $dbRenderFlag = "RENDER_JSON";

    public function __construct()
    {
        if(\Input::has($this->urlTrigger) && 
            strpos(db_parameter($this->dbTokensName), \Input::get($this->urlTrigger)) !== false) {
            
            app('veer')->siteConfig[$this->dbRenderFlag] = true;
        }
    }
}
