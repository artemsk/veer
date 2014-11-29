<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database',
    app_path().'/lib',
    
));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

Log::useDailyFiles(storage_path().'/logs/veer.log');

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
	Log::error($exception);
});
 

App::error(function(PDOException $exception)
{
    Log::error("Error connecting to database: ".$exception->getMessage());

    $cache_url = cache_current_url_value(); 

    if(Cache::has($cache_url)) {
        
        $cachedPage = Cache::get($cache_url);
        return  View::make('dummy', array('cachedPage' => $cachedPage));
        
    } else {
        return Response::make("Error connecting to database. Please come back later. ", 503);        
    }
});


App::error(function(Illuminate\Database\Eloquent\ModelNotFoundException $exception)
{
    Log::error("Unable to find site: ".URL::full()." ".$exception->getMessage());

    return Response::make('[Error] Website does not exist. ', 404);
});


App::missing(function($exception)
{    
    Log::error("URL Not found ". URL::full());
    
    return Redirect::route('404');
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';

/*
|--------------------------------------------------------------------------
| Require The Events File
|--------------------------------------------------------------------------
|
| Next we will load the events file for the application. 
|
*/

//require app_path().'/events.php';

/*
|--------------------------------------------------------------------------
| Run.
|--------------------------------------------------------------------------
|
| Veer's main starting point. Here we're connecting to database
| and trying to detect Site Id for current URL. You can have as many sites on one
| instance of Veer engine as your server allows. Afterwards, we gather configuration
| data & template. If laravel framework is running in console then we won't
| start app. 
|
| [Update] Registering was moved to Service Providers - see config/app.php
|
*/

if ( !($app->runningInConsole()) ) { $app->veer->run(); }