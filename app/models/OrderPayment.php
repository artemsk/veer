<?php

namespace Veer\Models;

class OrderPayment extends \Eloquent {
    
    protected $table = "orders_payment";
    protected $softDelete = true;
    
    // Many Payments Methods <- One
    
    public function site() {
        return $this->belongsTo('\Veer\Models\Site','sites_id','id');
    }
    
    // One Payment Method -> Many
    
    public function orders() {
       return $this->hasMany('\Veer\Models\Order', 'payment_method_id', 'id'); 
    }
    
    public function bills() {
       return $this->hasMany('\Veer\Models\OrderBill', 'payment_method_id', 'id'); 
    }
    
}