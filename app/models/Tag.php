<?php

namespace Veer\Models;

class Tag extends \Eloquent {
    
    protected $table = "tags";
    protected $softDelete = true;
    protected $fillable = array("name");
    
    // Many Tags <-> Many
    
    public function pages() {
        return $this->morphedByMany('\Veer\Models\Page', 'elements', 'tags_connect', 'tags_id', 'elements_id');
    }

    public function products() {
        return $this->morphedByMany('\Veer\Models\Product', 'elements', 'tags_connect', 'tags_id', 'elements_id');
    }
    
}