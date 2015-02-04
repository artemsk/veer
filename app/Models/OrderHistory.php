<?php

namespace Veer\Models;

class OrderHistory extends \Eloquent {
    
    protected $table = "orders_history";
	
    use \Illuminate\Database\Eloquent\SoftDeletes; 	
	protected $dates = ['deleted_at'];
    
}