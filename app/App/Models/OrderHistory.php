<?php

namespace Veer\Models;

class OrderHistory extends \Eloquent {
    
    protected $table = "orders_history";
    protected $softDelete = true;
    
}