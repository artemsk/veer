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



if ( ! function_exists('include_content_to_variable'))
{
	/**
	 * Include content to variable .
	 *
	 * @return string
	 */
	function include_content_to_variable($filename)
	{
                if (is_file($filename)) {
                    ob_start();
                    include $filename;
                    $contents = ob_get_contents();
                    ob_end_clean();
                    return $contents;
                }
	}
}



    
