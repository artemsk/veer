<?php

namespace Veer\Models;

class UserDiscount extends \Eloquent {
    
    protected $table = "users_discounts";
    protected $softDelete = true;
    protected $fillable = array('status');
    
    // Many Discounts <- One
    
    public function site() {
        return $this->belongsTo('\Veer\Models\Site','sites_id','id');
    }
        
    public function user() {
        return $this->belongsTo('\Veer\Models\User','users_id','id');
    }
    
    // One Discount -> Many
    
    public function orders() {
       return $this->hasMany('\Veer\Models\Order', 'userdiscount_id', 'id'); 
    }
    
}
