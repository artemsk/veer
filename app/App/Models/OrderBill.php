<?php

namespace Veer\Models;

class OrderBill extends \Eloquent {
    
    protected $table = "orders_bills";
    protected $softDelete = true;    
    
    // Many Bills <- One
    
    public function order() {
        return $this->belongsTo('\Veer\Models\Order','orders_id','id');
    }

    public function user() {
        return $this->belongsTo('\Veer\Models\User','users_id','id');
    }
    
    public function status() {
        return $this->belongsTo('\Veer\Models\OrderStatus','status_id','id');
    }
    
    public function payment() {
        return $this->belongsTo('\Veer\Models\OrderPayment','payment_method_id','id');
    }
    
}