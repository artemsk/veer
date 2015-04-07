<?php

namespace Veer\Components;

class commonOutputJson
{
    protected $urlTrigger = "json";

    protected $dbTokensName = "JSON_TOKENS";

    protected $dbRenderFlag = "RENDER_JSON";

    public function __construct()
    {
        if(\Input::has($this->urlTrigger)) {

            $jsonToken = json_decode("[".db_parameter($this->dbTokensName)."]");
            if(in_array(\Input::get($this->urlTrigger), $jsonToken)) app('veer')->siteConfig[$this->dbRenderFlag] = true;
        }
    }
}
