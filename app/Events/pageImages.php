<?php

namespace Veer\Events;

class pageImages
{
    protected $data;
    protected $attributes;
    protected $images_double;
    protected $pullLater;
    public $images;

    /**
     * Handle user login events.
     */
    public function getImages($event = array())
    {
        if (count($event[0]) <= 0) return false;

        $this->attributes = $event[1];

        $this->images = $event[0];

        $this->checkAttributes();

        if (count(array_get($this->data, 'featured')) > 0) {

            $head = count($this->images) > 0 && count(array_get($this->data,
                        'featured')) > 1 ? $this->images->shift() : null;

            $this->images = collect($this->data['featured'])->merge($this->images);

            if (!empty($head))
                    $this->images = $this->images->merge(array($head));
        }

        app('veer')->loadedComponents['event']['images'] = $this->images;
    }

    protected function checkAttributes()
    {
        foreach (array('imagePostFirst', 'imagePostSecond') as $type) {
            if (isset($this->attributes[$type]))
                    $this->setImage($this->attributes[$type]);
        }
    }

    protected function setImage($key)
    {
        if (isset($this->images[$key])) {
            $this->data['featured'][] = $this->images[$key];

            $this->images->forget($key);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     */
    public function subscribe($events = null)
    {
        $events->listen('page.images',
            '\Veer\Events\pageCornersImages@getImages');
    }
}
