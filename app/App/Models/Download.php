<?php

namespace Veer\Models;

class Download extends \Eloquent {
    
    protected $table = "downloads";
    protected $softDelete = true;

    // Many Downloads <- One
    
    public function elements() {
        return $this->morphTo();
    }
    
    
}

// TODO: несколько original файлы могут быть у товаров, страниц; если один и тот же файл у нескольких товаров или страниц, то запись в бд дублируется
// TODO: после оформления заказа создается копия в бд для пользователя - у которой может быть экспирейшн и тп.