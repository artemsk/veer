<?php

namespace Veer\Models;

class Tag extends \Eloquent {
    
    protected $table = "tags";
	
    use \Illuminate\Database\Eloquent\SoftDeletingTrait; 	
	protected $dates = ['deleted_at'];
	
    protected $fillable = array("name");
    
    // Many Tags <-> Many
    
    public function pages() {
        return $this->morphedByMany('\Veer\Models\Page', 'elements', 'tags_connect', 'tags_id', 'elements_id');
    }

    public function products() {
        return $this->morphedByMany('\Veer\Models\Product', 'elements', 'tags_connect', 'tags_id', 'elements_id');
    }
    
}