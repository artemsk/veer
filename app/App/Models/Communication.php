<?php

namespace Veer\Models;

class Communication extends \Eloquent {
    
    protected $table = "communications";
	
    use \Illuminate\Database\Eloquent\SoftDeletingTrait; 	
	protected $dates = ['deleted_at'];
        
    // Many Messages <- One
    
    public function elements() {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo('\Veer\Models\User','users_id','id');
    }
    
    public function site() {
        return $this->belongsTo('\Veer\Models\Site','sites_id','id');
    }
    
}