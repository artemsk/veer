<?php namespace Veer\Components;

use Veer\Models\Product;
use Veer\VeerShop;
use Illuminate\Support\Facades\Config;

/**
 * 
 * Veer.Components @globalCallme
 * 
 * - Callme Form
 * 
 * @params 
 * @params 
 * 
 * @return $data
 */

class globalCallme {   
    
    public $data = array();
    
    function __construct($params = null) {
		
		$this->data['callme'] = view("components.call-me-modal-component");
		
	}
	
}
