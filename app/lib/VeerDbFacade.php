<?php namespace Veer\Lib;

use Illuminate\Support\Facades\Facade;

class VeerQ extends Facade {

    protected static function getFacadeAccessor() { return 'veerdb'; }

}