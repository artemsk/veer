<?php

namespace Veer\Models;

class OrderProduct extends \Eloquent {
    
    protected $table = "orders_products";
	
    use \Illuminate\Database\Eloquent\SoftDeletingTrait; 	
	protected $dates = ['deleted_at'];
    
}