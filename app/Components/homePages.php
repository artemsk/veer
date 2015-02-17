<?php namespace Veer\Components;

/**
 * 
 * Veer.Components @homePages
 * 
 * - collect pgaes for specific category; 
 *   should be used for index page.
 * 
 * db: CATEGORY_HOME
 * $siteId
 * 
 */

class homePages {   
    
	use \Veer\Services\Traits\HomeTraits;
	
    public $data;
    
    public function __construct() {
        
        $this->data = $this->getHomeEntities('\Veer\Models\Page', app('veer')->siteId, db_parameter('CATEGORY_HOME'))->get();                                    
    }  
                 
}
