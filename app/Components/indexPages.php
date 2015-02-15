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
    
	use \Veer\Services\Traits\EntityTraits;
	
    public $data;
    
    function __construct() {
        
        $this->data = $this->getHomeEntities('\Veer\Models\Page', app('veer')->siteId, db_parameter('CATEGORY_HOME'));                                    
    }  
                 
}
