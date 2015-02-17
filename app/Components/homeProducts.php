<?php namespace Veer\Components;

/**
 * 
 * Veer.Components @homeProducts
 * 
 * - collect product for specific category; 
 *   should be used for index page.
 * 
 * db: CATEGORY_HOME
 * $siteId
 * 
 */

class homeProducts {   
    
	use \Veer\Services\Traits\HomeTraits;
	
    public $data;
    
    public function __construct() {
        
        $this->data = $this->getHomeEntities('\Veer\Models\Product', app('veer')->siteId, db_parameter('CATEGORY_HOME'))->get();                                    
    }  
          
}
