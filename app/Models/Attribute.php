<?php

namespace Veer\Models;

class Attribute extends \Eloquent {
    
    protected $table = "attributes";
	
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $dates = ['deleted_at'];

    protected $fillable = array("type", "name", "val", "descr");
    
    // Many Attributes Have Many (Pages & Products)
    
    public function pages() {
        return $this->morphedByMany('\Veer\Models\Page', 'elements', 'attributes_connect', 'attributes_id', 'elements_id');
    }

    public function products() {
        return $this->morphedByMany('\Veer\Models\Product', 'elements', 'attributes_connect', 'attributes_id', 'elements_id');
    }
    
}