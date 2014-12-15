<?php

namespace Veer\Models;

class Category extends \Eloquent {
    
    protected $table = "categories";
    protected $softDelete = true;
   
    public function scopeSiteValidation($query, $site_id) {
        return $query->whereHas('categories', function($q) use ($site_id) {
                $q->where('sites_id','=',$site_id)->remember(3);
            });
    }    
        
    // Many Categories Have One Site
    
    public function site() {
        return $this->belongsTo('\Veer\Models\Site','sites_id','id');
    }
    
    // Many Categories Have Many (Sub/Parent Categories, Pages, Products & Images)
    
    public function subcategories() {
        return $this->belongsToMany('\Veer\Models\Category','categories_pivot', 'parent_id','child_id');
    }
    
    public function parentcategories() {
        return $this->belongsToMany('\Veer\Models\Category','categories_pivot', 'child_id','parent_id');
    }
    
    public function pages() {
        return $this->morphedByMany('\Veer\Models\Page', 'elements', 'categories_connect', 'categories_id', 'elements_id');
    }

    public function products() {
        return $this->morphedByMany('\Veer\Models\Product', 'elements', 'categories_connect', 'categories_id', 'elements_id');
    }
    
    public function images() {
        return $this->morphToMany('\Veer\Models\Image', 'elements', 'images_connect', 'elements_id', 'images_id');
    }
    
    // One Category Has Many Messages
    
    public function communications() {
        return $this->morphMany('\Veer\Models\Communication', 'elements');
    }   
               
}