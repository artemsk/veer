<?php

namespace Veer\Models;

class OrderProduct extends \Eloquent {
    
    protected $table = "orders_products";
	
    use \Illuminate\Database\Eloquent\SoftDeletes; 	
	protected $dates = ['deleted_at'];
    
}