<?php

namespace Veer\Events;

/*
 * Parse page images. Use imagePostFirst & imagePostSecond attributes of page
 * to choose images with special effects
 */

class pageNeighbours
{
    protected $data;
    protected $page;

    /**
     * Handle user login events.
     */
    public function getNeighbours($event = null)
    {
        $this->pageId = $event;

        $this->data['exists'] = false;
        
        $this->data['previous'] = \Veer\Models\Page::where('id', '<', $this->pageId)->sitevalidation(app('veer')->siteId)->excludeHidden()->select('title', 'id', 'url')->orderBy('id', 'desc')->first();

        $this->data['next'] = \Veer\Models\Page::where('id', '>', $this->pageId)->sitevalidation(app('veer')->siteId)->excludeHidden()->select('title', 'id', 'url')->orderBy('id', 'asc')->first();

        if(count($this->data['previous']) > 0 || count($this->data['next']) > 0) {
            $this->data['exists'] = true;
        }

        app('veer')->loadedComponents['event']['neighbours'] = $this->data;
    }

  

    /**
     * Register the listeners for the subscriber.
     *
     */
    public function subscribe($events = null)
    {
        $events->listen('page.neighbours', '\Veer\Events\pageNeighbours@getNeighbours');
    }
}
