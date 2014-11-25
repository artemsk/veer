<?php

namespace Veer\Models;

class Site extends \Eloquent {
    
    protected $table = "sites";
    protected $softDelete = true;
    protected $fillable = array("url");
    
    // One Site -> Many
    
    public function subsites() {
        return $this->hasMany('\Veer\Models\Site','parent_id','id');
    }
    
    public function categories() {
        return $this->hasMany('\Veer\Models\Category', 'sites_id', 'id');
        //return $this->hasManyThrough('\Veer\Models\Category', '\Veer\Models\Site', 'parent_id', 'sites_id');
    }
    
    public function components() {
       return $this->hasMany('\Veer\Models\Component', 'sites_id', 'id'); 
    }
    
    public function configuration() {
       return $this->hasMany('\Veer\Models\Configuration', 'sites_id', 'id'); 
    }
    
    public function users() {
       return $this->hasMany('\Veer\Models\User', 'sites_id', 'id'); 
    }
    
    public function discounts() {
       return $this->hasMany('\Veer\Models\UserDiscount', 'sites_id', 'id'); 
    }
    
    public function userlists() {
       return $this->hasMany('\Veer\Models\UserList', 'sites_id', 'id'); 
    }
    
    public function orders() {
       return $this->hasMany('\Veer\Models\Order', 'sites_id', 'id'); 
    }
    
    public function delivery() {
       return $this->hasMany('\Veer\Models\OrderShipping', 'sites_id', 'id'); 
    }    
 
    public function payment() {
       return $this->hasMany('\Veer\Models\OrderPayment', 'sites_id', 'id'); 
    }  
    
    public function communications() {
       return $this->hasMany('\Veer\Models\Communication', 'sites_id');
    }
    
    public function roles() {
       return $this->hasMany('\Veer\Models\UserRole', 'sites_id', 'id'); 
    }
    
    public function elements() {
        return $this->hasManyThrough('\Veer\Models\CategoryConnect', '\Veer\Models\Category', 'sites_id', 'categories_id');
    }
    
    // Many Sites <- One
    
    public function parentsite() {
        return $this->belongsTo('\Veer\Models\Site','parent_id','id');        
    }
    
}