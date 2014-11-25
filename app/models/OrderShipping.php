<?php

namespace Veer\Models;

class OrderShipping extends \Eloquent {
    
    protected $table = "orders_shipping";
    protected $softDelete = true;
    
    // Many Shipping Methods <- One
    
    public function site() {
        return $this->belongsTo('\Veer\Models\Site','sites_id','id');
    }
    
    // One Shipping Method -> Many
    
    public function orders() {
       return $this->hasMany('\Veer\Models\Order', 'delivery_method_id', 'id'); 
    }
    
    
}