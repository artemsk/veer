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