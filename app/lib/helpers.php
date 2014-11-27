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

    
