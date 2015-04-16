<?php

namespace Veer\Models;

class Component extends \Eloquent {
    
    protected $table = "components";
	
    use \Illuminate\Database\Eloquent\SoftDeletes; 	
	protected $dates = ['deleted_at'];
    
    public function scopeSiteValidation($q, $site_id) {
        return $q->where('sites_id','=',$site_id); // TODO: remember 1          
    }
    
    public function scopeValidComponents($q, $site_id, $route_name) {
        return $q->where('sites_id','=',$site_id)->where(function($query) use ($route_name) {
            $query->where('route_name','=',$route_name)
                  ->orWhere('route_name','=','GLOBAL');
        });                        
    }
    
    // Many Components <- One
    
    public function site() {
        return $this->belongsTo('\Veer\Models\Site','sites_id','id');
    }
    
}
