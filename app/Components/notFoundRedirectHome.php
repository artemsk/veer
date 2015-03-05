<?php

namespace Veer\Components;

class notFoundRedirectHome
{

    public function __construct()
    {
        app('veer')->forceEarlyResponse = true;

        app('veer')->earlyResponseContainer = redirect()->route('index');
    }
}
