<?php
namespace Veer\Models;

class Configuration extends \Eloquent {
    
    protected $table = "configuration";
	
    use \Illuminate\Database\Eloquent\SoftDeletingTrait; 	
	protected $dates = ['deleted_at'];
    protected $fillable = array("sites_id", "conf_key");

    // Many Configuration Values <- One
    
    public function site() {
        return $this->belongsTo('\Veer\Models\Site','sites_id','id');
    }
    
}
