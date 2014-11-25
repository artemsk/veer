<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
        //
});


App::after(function($request, $response)
{
        //
});


App::shutdown(function($request) 
{
        // TODO: temporary logs
        $queries = DB::getQueryLog();
        echo "<br/>".number_format(memory_get_usage())."<br>";
        echo "<pre>";
        print_r($queries);
        echo "</pre>";
        $veerTimer = round(microtime(true)-LARAVEL_START,4);
        echo $veerTimer."<br>";
        if($veerTimer > Config::get('veer.loadingtime')) { // max loading time notify
            echo "?<br>";
        }
        // TODO: notify on slowness
        // TODO: save cache html
        // TODO: clear unused old cache (queue?) - thumbs, stats, htmls, ips
        // TODO: save referals

});
    

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});