<?php

namespace Veer\Models;

class Product extends \Eloquent {
    
    protected $table = "products";
    protected $softDelete = true;
    
    public function scopeSiteValidation($query, $site_id) 
    {
        return $query->whereHas('categories', function($q) use ($site_id) {
                $q->where('sites_id','=',$site_id)->remember(3);
            });
    }
    
    public function scopeExcludeHidden($query) {
        return $query->where('status','!=','hide');
    }
    
    public function scopeExcludeFuturProducts($query) {
        return $query->where('to_show','<', \Carbon\Carbon::now());
    }
    
    /* check hidden & future at once */
    public function scopeChecked($query) {
        return $query->where('status','!=','hide')->where('to_show','<', \Carbon\Carbon::now());
    }  
    
    // Many Products <-> Many
    
    public function subproducts() {
        return $this->belongsToMany('\Veer\Models\Product','products_pivot', 'parent_id', 'child_id');
    }
    
    public function parentproducts() {
        return $this->belongsToMany('\Veer\Models\Product','products_pivot', 'child_id', 'parent_id');
    }
    
    public function pages() {
        return $this->belongsToMany('\Veer\Models\Page','pages_products', 'products_id', 'pages_id');        
    }
    
    public function categories() {
        return $this->morphToMany('\Veer\Models\Category', 'elements', 'categories_connect', 'elements_id', 'categories_id');
    }
    
    public function tags() {
        return $this->morphToMany('\Veer\Models\Tag', 'elements', 'tags_connect', 'elements_id', 'tags_id');
    }    
 
    public function attributes() {
        return $this->morphToMany('\Veer\Models\Attribute', 'elements', 'attributes_connect', 'elements_id', 'attributes_id')
				->withPivot('product_new_price');
    } 
    
    public function images() {
        return $this->morphToMany('\Veer\Models\Image', 'elements', 'images_connect', 'elements_id', 'images_id');
    } 
    
    public function orders() {
        return $this->belongsToMany('\Veer\Models\Order','orders_products', 'products_id', 'orders_id');        
    }
    
    // One Product -> Many
    
    public function comments() {
        return $this->morphMany('\Veer\Models\Comment', 'elements');
    }   
    
    public function downloads() {
        return $this->morphMany('\Veer\Models\Download', 'elements');
    }
    
    public function userlists() {
        return $this->morphMany('\Veer\Models\UserList', 'elements');
    }

    public function communications() {
        return $this->morphMany('\Veer\Models\Communication', 'elements');
    }       
    
   // products on home
   public function scopeHomePages($query, $site_id, $home_id) 
   {
        return $query->whereHas('categories', function($q) use ($site_id, $home_id) {
                $q->where('sites_id','=',$site_id)->where('categories.id','=',$home_id);
            });
   }
    
}