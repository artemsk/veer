<?php

namespace Veer\Models;

class Order extends \Eloquent {
    
    protected $table = "orders";
    protected $softDelete = true;
    
    // Many Orders <- One
    
    public function site() {
        return $this->belongsTo('\Veer\Models\Site','sites_id','id');
    }
        
    public function user() {
        return $this->belongsTo('\Veer\Models\User','users_id','id');
    }
    
    public function userbook() {
        return $this->belongsTo('\Veer\Models\UserBook','userbook_id','id');
    }
    
    public function userdiscount() {
        return $this->belongsTo('\Veer\Models\UserDiscount','userdiscount_id','id');
    }
    
    // latest status
    public function status() {
        return $this->belongsTo('\Veer\Models\OrderStatus','status_id','id');
    }
    
    public function delivery() {
        return $this->belongsTo('\Veer\Models\OrderShipping','delivery_method_id','id');
    }
    
    public function payment() {
        return $this->belongsTo('\Veer\Models\OrderPayment','payment_method_id','id');
    }
    
    // Many Orders <-> Many
    
    public function status_history() {
        return $this->belongsToMany('\Veer\Models\OrderStatus','orders_history', 'orders_id', 'status_id');        
    }
    
    public function products() {
        return $this->belongsToMany('\Veer\Models\Product','orders_products', 'orders_id', 'products_id');        
    }
    
    // One Order -> Many
    
    public function bills() {
       return $this->hasMany('\Veer\Models\OrderBill', 'orders_id', 'id'); 
    }
    
	public function secrets() {
        return $this->morphMany('\Veer\Models\Secret', 'elements');
    }   
	
}