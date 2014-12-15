<?php

namespace Veer\Models;

class UserList extends \Eloquent {
    
    protected $table = "users_lists";
    protected $softDelete = false;
	protected $fillable = array('session_id');
    
    // Many Lists <- One
    
    public function site() {
        return $this->belongsTo('\Veer\Models\Site','sites_id','id');
    }
        
    public function user() {
        return $this->belongsTo('\Veer\Models\User','users_id','id');
    }
    
    public function elements() {
        return $this->morphTo();
    }
}

// TODO: взаимосвязь с order?