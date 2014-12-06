<?php namespace Veer\Models;

class Secret extends \Eloquent {
    
    protected $table = "secrets";
    protected $softDelete = true;
    protected $fillable = array("secret");
	
    public function elements() {
        return $this->morphTo();
    }
	
}