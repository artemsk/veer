<?php

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
