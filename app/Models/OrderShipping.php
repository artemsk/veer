<?php

namespace Veer\Models;

class OrderShipping extends \Eloquent {
    
    protected $table = "orders_shipping";
	
    use \Illuminate\Database\Eloquent\SoftDeletes; 	
	protected $dates = ['deleted_at'];
    
    // Many Shipping Methods <- One
    
    public function site() {
        return $this->belongsTo('\Veer\Models\Site','sites_id','id');
    }
    
    // One Shipping Method -> Many
    
    public function orders() {
       return $this->hasMany('\Veer\Models\Order', 'delivery_method_id', 'id'); 
    }
    
    
}