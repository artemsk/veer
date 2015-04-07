<?php

namespace Veer\Components;

class commonChangeLocale
{
    protected $allowedLocales = ['en', 'fr', 'ru'];

    protected $urlTrigger = "locale";

    protected $sessionVarName = "rememberLocale";

    public function __construct()
    {
        if(\Input::has($this->urlTrigger) && in_array(\Input::get($this->urlTrigger), $this->allowedLocales)) {
            app()->setLocale(\Input::get($this->urlTrigger));
            \Session::put($this->sessionVarName, \Input::get($this->urlTrigger));
        } elseif(\Session::has($this->sessionVarName)) {
            app()->setLocale(\Session::get($this->sessionVarName));
        }
    }
}
