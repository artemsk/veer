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
                return config('veer.htmlcache') . sanitize_url(URL::full());
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




if ( ! function_exists('administrator'))
{
	/**
	 * Check if user is administrator
	 *
	 * @return true of false
	 */
	function administrator()
	{
        $administrator = false;
		
		if(Auth::check()) {
			$v = Auth::user()->load('administrator');
			if(isset($v->administrator->banned) && (bool)($v->administrator->banned) == false) { 
				$administrator = true; 
				app('veer')->administrator_credentials = $v->administrator;
				$v->administrator()->touch();
			}
		}
		
		return $administrator;
	}
}




if ( ! function_exists('now'))
{
	/**
	 * now with add
	 *
	 * @return now
	 */
	function now($add = null, $markHours = false)
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




if ( ! function_exists('abort'))
{
	/**
	 * Throw an HttpException with the given data.
	 *
	 * @param  int     $code
	 * @param  string  $message
	 * @param  array   $headers
	 * @return void
	 *
	 * @throws \Symfony\Component\HttpKernel\Exception\HttpException
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	function abort($code, $message = '', array $headers = array())
	{
		return app()->abort($code, $message, $headers);
	}
}




if ( ! function_exists('bcrypt'))
{
	/**
	 * Hash the given value.
	 *
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	function bcrypt($value, $options = array())
	{
		return app('hash')->make($value, $options);
	}
}




if ( ! function_exists('cookie'))
{
	/**
	 * Create a new cookie instance.
	 *
	 * @param  string  $name
	 * @param  string  $value
	 * @param  int     $minutes
	 * @param  string  $path
	 * @param  string  $domain
	 * @param  bool    $secure
	 * @param  bool    $httpOnly
	 * @return \Symfony\Component\HttpFoundation\Cookie
	 */
	function cookie($name = null, $value = null, $minutes = 0, $path = null, $domain = null, $secure = false, $httpOnly = true)
	{
		$cookie = app('cookie');

		if (is_null($name))
		{
			return $cookie;
		}

		return $cookie->make($name, $value, $minutes, $path, $domain, $secure, $httpOnly);
	}
}




if ( ! function_exists('delete'))
{
	/**
	 * Register a new DELETE route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	function delete($uri, $action)
	{
		return app('router')->delete($uri, $action);
	}
}




if ( ! function_exists('logger'))
{
	/**
	 * Log a debug message to the logs.
	 *
	 * @param  string  $message
	 * @param  array  $context
	 * @return void
	 */
	function logger($message, array $context = array())
	{
		return app('log')->debug($message, $context);
	}
}




if ( ! function_exists('old'))
{
	/**
	 * Retrieve an old input item.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	function old($key = null, $default = null)
	{
		return app('request')->old($key, $default);
	}
}




if ( ! function_exists('patch'))
{
	/**
	 * Register a new PATCH route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	function patch($uri, $action)
	{
		return app('router')->patch($uri, $action);
	}
}




if ( ! function_exists('put'))
{
	/**
	 * Register a new PUT route with the router.
	 *
	 * @param  string  $uri
	 * @param  \Closure|array|string  $action
	 * @return \Illuminate\Routing\Route
	 */
	function put($uri, $action)
	{
		return app('router')->put($uri, $action);
	}
}




if ( ! function_exists('redirect'))
{
	/**
	 * Get an instance of the redirector.
	 *
	 * @param  string|null  $to
	 * @param  int     $status
	 * @param  array   $headers
	 * @param  bool    $secure
	 * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
	 */
	function redirect($to = null, $status = 302, $headers = array(), $secure = null)
	{
		if ( ! is_null($to))
		{
			return app('redirect')->to($to, $status, $headers, $secure);
		}
		else
		{
			return app('redirect');
		}
	}
}




if ( ! function_exists('odd'))
{
	/**
	 * Check if num is odd or not
	 *
	 * @param  int|float $num
	 * @return bool
	 */
	function odd($num)
	{
		 return ($num % 2) ? TRUE : FALSE;
	}
}