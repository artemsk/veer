<?php

namespace Veer\Components;

use Veer\Services\Show\Page as ShowPage;

class indexCornersHero {
    
    public $data;

    public function __construct()
    {
        $id = db_parameter('HERO_ON_HOME');

        if (!empty($id)) {
            $this->data = (new ShowPage)->getPage($id, app('veer')->siteId);

            $this->data->load(array('images' => function($q) {
                return $q->orderBy('pivot_id', 'asc');
            }));
        }
    }
}
