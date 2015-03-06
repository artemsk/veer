<?php

namespace Veer\Components;

class indexCornersDigest
{

    use \Veer\Services\Traits\CommonTraits;
    public $data;
    public $number_of_items = 8;
    public $tagId;

    public function __construct()
    {
        $this->tagId = db_parameter('CORNERS_TAG_DIGEST');

        $this->createListOfPages();
    }

    public function createListOfPages()
    {
        $this->data['items'] = $this->getElementsWhereHasModel('pages', 'tags',
                $this->tagId, app('veer')->siteId,
                array(
                "take" => $this->number_of_items
                ), true)->select('id', 'url', 'title', 'small_txt', 'views',
                'created_at', 'users_id')->orderBy('manual_order', 'desc')->get();

        $this->data['tagName'] = \Cache::remember('tagNameId'.$this->tagId, 2,
                function() {
                return \Veer\Models\Tag::where('id', '=', $this->tagId)->pluck('name');
            });
    }
}
