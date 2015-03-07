<?php

namespace Veer\Components;

class indexCornersExcerpts extends indexCornersDigest
{

    public function __construct()
    {
        $this->number_of_items = 25;

        $this->tagId = db_parameter('CORNERS_TAG_EXCERPTS');

        $this->createListOfPages();

        $this->data['items'] = $this->data['items']->getDictionary();
    }
}
