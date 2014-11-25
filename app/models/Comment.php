<?php

namespace Veer\Models;

class Comment extends \Eloquent {
    
    protected $table = "comments";
    protected $softDelete = true;
    
    public function scopeExcludeHidden($query) {
        return $query->where('hidden','!=',0);
    }
    
    // Many Comments <- One
    
    public function elements() {
        return $this->morphTo();
    }

    public function user() {
        return $this->belongsTo('\Veer\Models\User','users_id','id');
    }
    
}