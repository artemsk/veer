<?php namespace Veer\Components;

use Veer\Models\Product;

/**
 * 
 * Veer.Components @indexProducts
 * 
 * - collect product for specific category; 
 *   should be used for index page.
 * 
 * db: CATEGORY_HOME
 * $siteId
 * 
 */

class indexProducts {   
    
	use \Veer\Services\Traits\EntityTraits;
	
    public $data;
    
    function __construct() {
        
        $this->data = $this->getHomeEntities('\Veer\Models\Product', app('veer')->siteId, db_parameter('CATEGORY_HOME'));                                    
    }  
          
}
