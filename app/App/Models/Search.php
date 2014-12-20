<?php

namespace Veer\Models;

class Search extends \Eloquent {
    
    protected $table = "searches";
	
    use \Illuminate\Database\Eloquent\SoftDeletingTrait; 	
	protected $dates = ['deleted_at'];
	
    protected $fillable = array("q");
    
    // Many Searches <-> Many Users
    
    public function users() {
        return $this->belongsToMany('\Veer\Models\User','searches_connect', 'searches_id', 'users_id');  
    }
    
}