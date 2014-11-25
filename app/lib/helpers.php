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
                return Config::get('veer.htmlcache') . strtr( URL::full(), 
                array( "http://" => "http_",
                       "/" => "_",
                       "." => "_"
                    ));
	}
}
