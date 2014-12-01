<?php
if ( ! function_exists('cache_current_url_value'))
{
	/**
	 * Generate correct URL for caching .
	 *
	 * @return string
	 */
	function cache_current_url_value()
	{
                return Config::get('veer.htmlcache') . sanitize_url(URL::full());
	}
}



if ( ! function_exists('sanitize_url'))
{
	/**
	 * Generate correct URL for caching .
	 *
	 * @return string
	 */
	function sanitize_url($url = '', $more = false)
	{
                return strtr( $url, 
                array( "http://" => "",
					   ":" => "_",
                       "/" => "_",
                       "." => "_"
                    ));
	}
}




if ( ! function_exists('get_paginator_and_sorting'))
{
	/**
	 * Get paginator & sorting.
	 *
	 * @return string
	 */
	function get_paginator_and_sorting()
	{
                $defaultParams = array(
                "sort" => "created_at",
                "direction" => "desc",
                "skip" => 0,
                "take" => 25,
                "skip_pages" => 0,
                "take_pages" => 25,
                "search_field_product" => "title",
                "search_field_page" => "title"
                );
                
                $g = Input::all();

                foreach($defaultParams as $k => $v) {
                    if( !isset($g[$k]) ) { $g[$k] = $v; }
                }
                
                return $g;
	}
}




if ( ! function_exists('db_parameter'))
{
	/**
	 * Trying to get paramter from Veer instance
	 *
         * @param parameter name
	 * @return result
	 */
	function db_parameter($param = null, $default = null)
	{
                if(!empty($param)) 
                {
                    $v = app('veer')->siteConfig;                   
                    return (isset($v[$param])) ? $v[$param] : db_parameter_not_found($param, $default);
                }
	}
}




if ( ! function_exists('db_parameter_not_found'))
{
	/**
	 * Log::error if db parameter not found
	 *
         * @param parameter name
	 * @return null
	 */
	function db_parameter_not_found($param = null, $default = null)
	{
                Log::error('Veer Component Error: Necessary parameter not found' . ((empty($param)) ? 0 : ': ' . $param));
                return $default;
	}
}
    



if ( ! function_exists('auth_check_session'))
{
	/**
	 * Auth::check without connecting to database @testing
	 *
	 * @return true of false
	 */
	function auth_check_session()
	{
        return Session::has(Auth::getName());		
	}
}




if ( ! function_exists('now'))
{
	/**
	 * now with add
	 *
	 * @return now
	 */
	function now($add = null, $markHours = null)
	{
		if( empty($add) ) { return \Carbon\Carbon::now(); }
		
		return !empty($markHours) ? \Carbon\Carbon::now()->addHours($add) : \Carbon\Carbon::now()->addMinutes($add);
	}
}




if ( ! function_exists('config'))
{
	/**
	 * Get the specified configuration value.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	function config($key, $default = null)
	{
		return app('config')->get($key, $default);
	}
}




if ( ! function_exists('get'))
{
	/**
	 * Register a new GET route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	function get($uri, $action)
	{
		return app('router')->get($uri, $action);
	}
}




if ( ! function_exists('post'))
{
	/**
	 * Register a new POST route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	function post($uri, $action)
	{
		return app('router')->post($uri, $action);
	}
}




if ( ! function_exists('info'))
{
	/**
	 * Write some information to the log.
	 *
	 * @param  string  $message
	 * @param  array   $context
	 * @return void
	 */
	function info($message, $context = array())
	{
		return app('log')->info($message, $context);
	}
}




if ( ! function_exists('response'))
{
	/**
	 * Return a new response from the application.
	 *
	 * @param  string  $content
	 * @param  int     $status
	 * @param  array   $headers
	 * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
	 */
	function response($content = '', $status = 200, array $headers = array())
	{	
		return \Illuminate\Support\Facades\Response::make($content, $status, $headers);
	}
}




if (!function_exists('view')) 
{
	/**
	 * Get the evaluated view contents for the given view.
	 *
	 * @param string $view
	 * @param array $data
	 * @param array $mergeData
	 * @return \Illuminate\View\View
	 */
	function view($view = null, $data = array(), $mergeData = array())
	{
		$factory = app('view');
		
		if (func_num_args() === 0) {
			return $factory;
		}
		return $factory->make($view, $data, $mergeData);
	}
}




if ( ! function_exists('stored'))
{
	/**
	 * Get shopping cart value from session
	 *
	 * @return Session: shopping_cart_items
	 */
	function stored()
	{
		return Session::get('shopping_cart_items', 0);		
	}
}