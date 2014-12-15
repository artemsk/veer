<?php

namespace Veer\Models;

class UserBook extends \Eloquent {
    
    protected $table = "users_book";
    protected $softDelete = true;
    
    // Many User Books <- One
    
    public function user() {
        return $this->belongsTo('\Veer\Models\User','users_id','id');
    }
    
    // One User Book -> Many
    
    public function orders() {
       return $this->hasMany('\Veer\Models\Order', 'userbook_id', 'id'); 
    }
    
}
