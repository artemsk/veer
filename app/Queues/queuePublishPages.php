<?php

namespace Veer\Queues;

class queuePublishPages
{

    use \Veer\Services\Traits\CommonTraits;

    /* name */
    public $queueName = "Publish queued pages";

    /* description */
    public $queueDescription = "Publish queued pages. Set category to check & queue category.";
    protected $number_of_items = 1;

    public function fire($job, $data)
    {
        $category = array_get($data, 'category', null);

        $queueCategory = array_get($data, 'queue', null);

        if (empty($queueCategory) || empty($category)) return $job->fail();

        $pages = $this->getPages($category, $queueCategory);

        if ($pages->count() > 0) {
            foreach ($pages as $page) {
                $page->hidden     = 0;
                $page->created_at = now();
                $page->categories()->detach($queueCategory);
                $page->save();
            }
        }

        // leave it here
        if (isset($data['repeatJob']) && $data['repeatJob'] > 0) {
            $job->release($data['repeatJob'], 'minutes');
        }
    }

    protected function getPages($category, $queueCategory)
    {
        return \Veer\Models\Page::whereHas('categories',
                    function($q) use($category) {
                    $q->where(function($query) use ($category) {
                        $query->where('categories_id', '=', $category);
                    });
                })->whereHas('categories',
                    function($q) use($queueCategory) {
                    $q->where(function($query) use ($queueCategory) {
                        $query->where('categories_id', '=', $queueCategory);
                    });
                })->with(array('images' => function($query) {
                    $query->orderBy('pivot_id', 'asc');
                }))->sitevalidation(app('veer')->siteId)->where('hidden', '=', 1)->orderBy('manual_order',
                    'asc')->orderBy('created_at', 'asc')
                ->take($this->number_of_items)->select('id', 'url', 'title',
                'small_txt', 'views', 'created_at', 'users_id', 'hidden')->get();
    }
}
