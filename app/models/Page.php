<?php

namespace Veer\Models;

class Page extends \Eloquent {
    
    protected $table = "pages";
    protected $softDelete = true;
    
    public function scopeSiteValidation($query, $site_id) {
        return $query->whereHas('categories', function($q) use ($site_id) {
                $q->where('sites_id','=',$site_id)->remember(3);
            });
    }
    
    public function scopeExcludeHidden($query) {
        return $query->where('hidden','!=',1);
    }
    
    // Many Pages <- One
     
    public function user() {
        return $this->belongsTo('\Veer\Models\User','users_id','id');
    }
    
    // Many Pages <-> Many
    
    public function subpages() {
        return $this->belongsToMany('\Veer\Models\Page','pages_pivot', 'parent_id', 'child_id');
    }
    
    public function parentpages() {
        return $this->belongsToMany('\Veer\Models\Page','pages_pivot', 'child_id', 'parent_id');
    }
    
    public function products() {
        return $this->belongsToMany('\Veer\Models\Product','pages_products', 'pages_id', 'products_id');        
    }
    
    public function categories() {
        return $this->morphToMany('\Veer\Models\Category', 'elements', 'categories_connect', 'elements_id', 'categories_id');
    }
    
    public function tags() {
        return $this->morphToMany('\Veer\Models\Tag', 'elements', 'tags_connect', 'elements_id', 'tags_id');
    } 
    
    public function attributes() {
        return $this->morphToMany('\Veer\Models\Attribute', 'elements', 'attributes_connect', 'elements_id', 'attributes_id');
    } 
    
    public function images() {
        return $this->morphToMany('\Veer\Models\Image', 'elements', 'images_connect', 'elements_id', 'images_id');
    } 
    
    // One Page -> Many
    
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
    
    // news
    public function scopeNewsPages($query) {
        return $query->where('in_news','=',1)
                    ->where('hidden','!=',1)
                    ->orderBy('created_at','desc')
                    ->orderBy('manual_order','asc')
                    ->select('id','url','title','small_txt','created_at','show_comments');
    }
    
    // all pages
    public function scopeAllPages($query) {
        return $query->where('in_list','=',1)
                    ->where('hidden','!=',1)
                    ->orderBy('created_at','desc')
                    ->orderBy('manual_order','asc')
                    ->select('id','url','title','small_txt','created_at','show_title','show_date','show_comments');
    }
        
    // pages/articles on home
   public function scopeHomePages($query, $site_id, $home_id) {
        return $query->whereHas('categories', function($q) use ($site_id, $home_id) {
                $q->where('sites_id','=',$site_id)->where('categories.id','=',$home_id);
            });
    }
    
}