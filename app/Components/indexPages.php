<?php namespace Veer\Components;

use Veer\Models\Page;

/**
 * 
 * Veer.Components @indexPages
 * 
 * - collect pgaes for specific category; 
 *   should be used for index page.
 * 
 * db: CATEGORY_HOME
 * $siteId
 * 
 */

class indexPages {   
    
    public $data = array();
    
    function __construct() {
        
        $v = app('veer');
        
        $siteId = $v->siteId;
        $homeId = db_parameter('CATEGORY_HOME');     
        
        $this->data = Page::homepages($siteId, $homeId)->checked()->with(
			array( 'categories' => function($query) use ($siteId, $homeId) {
					$query->where('sites_id','=',$siteId)->where('categories.id','!=',$homeId);
				}))->with(array('images' => function($query) {
					$query->orderBy('id','asc')->take(1);
				}))->with('user')->get();                                         
    }  
          
}
