<?php

namespace Veer\Models;

class Search extends \Eloquent {
    
    protected $table = "searches";
    protected $softDelete = true;
    protected $fillable = array("q");
    
    // Many Searches <-> Many Users
    
    public function users() {
        return $this->belongsToMany('\Veer\Models\User','searches_connect', 'searches_id', 'users_id');  
    }
    
}