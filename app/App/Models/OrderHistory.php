<?php

namespace Veer\Models;

class OrderHistory extends \Eloquent {
    
    protected $table = "orders_history";
	
    use \Illuminate\Database\Eloquent\SoftDeletingTrait; 	
	protected $dates = ['deleted_at'];
    
}