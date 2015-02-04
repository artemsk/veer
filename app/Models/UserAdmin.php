<?php

namespace Veer\Models;

class UserAdmin extends \Eloquent {
    
    protected $table = "users_admin";
	
    use \Illuminate\Database\Eloquent\SoftDeletes; 	
	protected $dates = ['deleted_at'];
	
    protected $fillable = array("users_id", "sess_id");
    
    // One Admin <-> One
    
    public function user() {
        return $this->belongsTo('\Veer\Models\User','users_id','id');
    }
        
}
