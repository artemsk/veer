<?php

namespace Veer\Models;

class OrderStatus extends \Eloquent {
    
    protected $table = "orders_status";
    protected $softDelete = true;    
    
    // One Order Status -> Many
    
    public function orders() {
       return $this->hasMany('\Veer\Models\Order', 'status_id', 'id'); 
    }

    public function bills() {
       return $this->hasMany('\Veer\Models\OrderBill', 'status_id', 'id'); 
    }
    
    // Many Order Statuses <-> Many
    
    public function orders_with_history() {
        return $this->belongsToMany('\Veer\Models\Order','orders_history', 'status_id', 'orders_id');        
    }
    
    // CORE    
    public function scopeFirstStatus($query) {
        return $query->where('flag_first','=','1')->remember(10);                    
    }
    
    public function scopeUnregStatus($query) {
        return $query->where('flag_unreg','=','1')->remember(10);                    
    }
    
    public function scopeErrorStatus($query) {
        return $query->where('flag_error','=','1')->remember(10);                    
    }
    
    public function scopeCloseStatus($query) {
        return $query->where('flag_close','=','1')->remember(10);                    
    }
    
    public function scopeDeliveryStatus($query) {
        return $query->where('flag_delivery','=','1')->remember(10);                    
    }
    
    public function scopePaymentStatus($query) {
        return $query->where('flag_payment','=','1')->remember(10);                    
    }
    
    
}