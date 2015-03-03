<?php

namespace Veer\Events;

/*
 * Parse page images. Use imagePostFirst & imagePostSecond attributes of page
 * to choose images with special effects
 */

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

        $this->hiddenImages();
    }

    protected function hiddenImages()
    {
        if(!isset($this->attributes['imagePostHidden'])) return false;

        $hidden = json_decode('['.$this->attributes['imagePostHidden'].']');

        foreach(is_array($hidden) ? $hidden : array() as $hiddenId)
        {
            $this->images->forget($hiddenId);
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
        $events->listen('page.images', '\Veer\Events\pageImages@getImages');
    }
}
