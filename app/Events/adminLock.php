<?php namespace Veer\Events;

use Veer\Events\Event;

use Illuminate\Queue\SerializesModels;

class adminLock extends Event {

    protected $userId;
    protected $routeRoot;
    protected $routeEntity;
    protected $id;

    protected $cacheName;

    protected $locked = false;
    protected $lockedByWhom;
    protected $updated = null;

	use SerializesModels;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
        $this->getData();              
	}

    public function handle($event = array())
    {
        if(!empty($event)) $this->setData($event);

        $this->cacheName = 'lock-'.$this->routeRoot.'-'.$this->routeEntity.'-'.$this->id;

        if($this->checkLock()) {
            if(!empty($this->id) && !empty($this->userId) && !empty($this->routeRoot)) {

                \Cache::put($this->cacheName,
                        $this->userId, 2
                );

                $this->updated = time();
            }
        }

        app('veer')->loadedComponents['event']['lock-for-edit'] = $this->locked;
    }

    protected function getData()
    {
        $this->userId = \Auth::id();

        $this->routeRoot = head(explode(".", app('router')->currentRouteName()));

        $this->id = \Input::get('id') == 'new' ? null : \Input::get('id');

        $this->routeEntity = implode("&", app('router')->current()->parameters());     
    }

    protected function setData($event)
    {
        $this->userId = array_get($event, 0);

        $this->routeRoot = array_get($event, 1);

        $this->routeEntity = array_get($event, 2);

        $this->id = array_get($event, 3);
    }

    protected function checkLock()
    {
        $this->lockedByWhom = \Cache::get($this->cacheName);

        if(empty($this->lockedByWhom)) return true;

        if($this->lockedByWhom == $this->userId) return true;

        $this->locked = true;

        return false;
    }

}
