<?php

namespace Veer\Models;

class UserAdmin extends \Eloquent {
    
    protected $table = "users_admin";
    protected $softDelete = true;
    protected $fillable = array("users_id", "sess_id");
    
    // One Admin <-> One
    
    public function user() {
        return $this->belongsTo('\Veer\Models\User','users_id','id');
    }
        
}
