<?php


/**
 * 
 * Veer Helpers 
 * & Laravel 5 helpers
 * 
 */


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
	function sanitize_url($url = '')
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
	 */
	function db_parameter($param = null, $default = null, $getFromDbSiteId = null)
	{
                if(!empty($param)) 
                {
                    $v = app('veer')->siteConfig;    
					
					if(!empty($getFromDbSiteId)) { 
						
						$cacheName = 'dbparameter'.$getFromDbSiteId.'-'.$param;
						
						$v[$param] = \Cache::remember($cacheName, .5, function() use ($param, $getFromDbSiteId) 
						{
							return \Veer\Models\Configuration::where('sites_id','=',$getFromDbSiteId)
							->where('conf_key','=',$param)->pluck('conf_val'); 
						}); 
						
						if(empty($v[$param])) unset($v[$param]);
					}
					
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



if ( ! function_exists('stored'))
{
	/**
	 * Get shopping cart value from session
	 *
	 */
	function stored()
	{
		return Session::get('shopping_cart_items', 0);		
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




if (!function_exists('elements')) {

	/**
	 * Correct model name for element
	 * Model doesn't start with \
	 * 
	 * @param string $type
	 * @return string
	 */
	function elements($type = "page")
	{
		return "Veer\Models\\" . ucfirst( str_singular($type) );
	}

}




if (!function_exists('statuses')) {

	/**
	 * Get all veer shop statuses
	 * @return object
	 */
	function statuses($flag = null)
	{ 
		if(empty($flag)) 
		{ 
			return \Cache::remember('listOfStatuses', .5, function() { 
				return \Veer\Models\OrderStatus::orderBy('manual_order','asc')->get(); }); 
		}
		
		$statuses = ($flag == "secret") ? \Veer\Models\OrderStatus::where($flag, '=', true) :
			\Veer\Models\OrderStatus::where('flag_' . $flag, '=', true);
		
		return \Cache::remember('listofStatuses-'.$flag, .5, function() use ($statuses) { 
			return $statuses->orderBy('manual_order', 'asc')->get(); 
		}); 
	}

}




if (!function_exists('payments')) {

	/**
	 * Get all veer shop payments
	 * @return object
	 */
	function payments($siteId = null)
	{ 
		if(empty($siteId)) { $payments = \Veer\Models\OrderPayment::select(); } else {
			$payments = \Veer\Models\OrderPayment::where('sites_id', '=', $siteId);
		}
		
		return \Cache::remember('listofPaymentMethods-'.$siteId, .5, function() use ($payments) { 
			return $payments->where('enable', '=', true)->orderBy('manual_order', 'asc')->get(); 
		});
	}
}




if (!function_exists('shipping')) {

	/**
	 * Get all veer shop shipping
	 * @return object
	 */
	function shipping($siteId = null)
	{ 
		if(empty($siteId)) { $shipping = \Veer\Models\OrderShipping::select(); } else {
			$shipping = \Veer\Models\OrderShipping::where('sites_id', '=', $siteId);
		}
		
		return \Cache::remember('listofShippingMethods-'.$siteId, .5, function() use ($shipping) { 
			return $shipping->where('enable', '=', true)->orderBy('manual_order', 'asc')->get(); 
		});
	}
}





if (!function_exists('parse_form_date')) {

	/**
	 * Parse date from form
	 * @return object
	 */
	function parse_form_date($d = null)
	{ 
		if(empty($d)) return now();
		
		$parseDate = explode("/", $d);
		
		return ((int)array_get($parseDate, 2, 0) <= 0) ? now() : \Carbon\Carbon::parse($d);	
	}
}




if (!function_exists('unread')) {

	/**
	 * Show unread elements
	 * @return object
	 */
	function unread($model = 'comment')
	{ 
		return \Veer\Services\Show\UserProperties::showUnreadNumbers($model);
	}
}




if (!function_exists('array_set_empty')) {

	/**
	 */
	function array_set_empty(&$array, $key, $default = null)
	{ 
		$value = array_get($array, $key);
		
		return !empty($value) ?: array_set($array, $key, $default);
	}
}




if (!function_exists('array_set_if')) {

	/**
	 */
	function array_set_if($condition, &$array, $key, $default = null)
	{ 
		$value = array_get($array, $key);
		
		return $value != $condition ?: array_set($array, $key, $default);
	}
}




if (!function_exists('paragraphs')) {

	/**
	 */
	function paragraphs($text, $delimiter = null)
	{ 	
		if(!empty($delimiter)) return explode($delimiter, $text);
		
		else $paragraphs = preg_split('#<p([^>])*>#',strtr( $text, array(
			"</p>" => "")));

		return array_filter($paragraphs, 'strlen');
	}
}




if ( ! function_exists('viewx'))
{
	/**
	 * Get the evaluated view contents for the given view.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @param  array   $mergeData
	 * @return \Illuminate\View\View
	 */
	function viewx($view = null, $data = array(), $mergeData = array())
	{
		$factory = app('Illuminate\Contracts\View\Factory');

		if (func_num_args() === 0)
		{
			return $factory;
		}

		return $factory->exists($view) ? $factory->make($view, $data, $mergeData) : redirect()->route('404');
	}
}




if (!function_exists('veer_get')) {

	/**
	 */
	function veer_get($key, $default = null)
	{
		return data_get(app('veer')->loadedComponents, $key, $default);
	}
}