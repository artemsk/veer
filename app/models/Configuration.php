<?php
namespace Veer\Models;

class Configuration extends \Eloquent {
    
    protected $table = "configuration";
    protected $softDelete = true;
    
    // Many Configuration Values <- One
    
    public function site() {
        return $this->belongsTo('\Veer\Models\Site','sites_id','id');
    }
    
}
