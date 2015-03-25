<?php

namespace Veer\Components;

class categoryCornersPages extends indexCornersPages
{
    use \Veer\Services\Traits\HomeTraits;

    public function __construct()
    {
        parent::__construct();

        $this->category = app('router')->current()->category;

        $this->createListOfPages();

        $category = (new \Veer\Services\Show\Category)->getCategory($this->category,
            app('veer')->siteId);

        if (is_object($category)) {
            $category->increment('views');

            $category->load(array('images' => function($q) {
                return $q->orderBy('pivot_id', 'asc');
            }));

            app('veer')->loadedComponents['function']['indexCornersPages'] = $this;

            if (app('veer')->forceEarlyResponse === false)
                    $this->categoryEarlyResponse($category);
        }
    }

    protected function categoryEarlyResponse($category)
    {
        app('veer')->forceEarlyResponse = true;

        app('veer')->earlyResponseContainer = viewx(app('veer')->template.'.category',
            array(
            "category" => $category,
            "template" => app('veer')->template
        ));
    }
}
