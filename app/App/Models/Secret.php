<?php namespace Veer\Models;

class Secret extends \Eloquent {
    
    protected $table = "secrets";
	
    use \Illuminate\Database\Eloquent\SoftDeletingTrait; 	
	protected $dates = ['deleted_at'];
	
    protected $fillable = array("secret", "elements_id", "elements_type");
	
    public function elements() {
        return $this->morphTo();
    }
	
}