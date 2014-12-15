<?php

namespace Veer\Models;

class OrderProduct extends \Eloquent {
    
    protected $table = "orders_products";
    protected $softDelete = true;
    
}