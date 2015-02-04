<?php

namespace Veer\Models;

class Image extends \Eloquent {
    
    protected $table = "images";
	
    use \Illuminate\Database\Eloquent\SoftDeletes; 	
	protected $dates = ['deleted_at'];
    
    // Many Images <-> Many
    
    public function pages() {
        return $this->morphedByMany('\Veer\Models\Page', 'elements', 'images_connect', 'images_id', 'elements_id');
    }

    public function products() {
        return $this->morphedByMany('\Veer\Models\Product', 'elements', 'images_connect', 'images_id', 'elements_id');
    }
 
    public function categories() {
        return $this->morphedByMany('\Veer\Models\Category', 'elements', 'images_connect', 'images_id', 'elements_id');
    }
    
    public function users() {
        return $this->morphedByMany('\Veer\Models\User', 'elements', 'images_connect', 'images_id', 'elements_id');
    }
	
}