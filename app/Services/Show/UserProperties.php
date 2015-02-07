<?php namespace Veer\Services\Show;

class UserProperties {
	
	use \Veer\Services\Traits\HelperTraits;
	
	/**
	 * show Unread Numbers
	 */
	static public function showUnreadNumbers($model, $raw = null, $period = 5)
	{
		$modelFull = "\\" . elements( str_singular($model) );
			
		$numbers = $modelFull::where('created_at', '>=', self::getUnreadTimestamp( str_plural($model) ));
		
		if (!empty($raw)) { $numbers->whereRaw($raw); }
		
		$numbers = app('veer')->cachingQueries->makeAndRemember($numbers, 'count', $period, null, 'unreadNumbers'.$model);
		
		return $numbers > 0 ? $numbers : null;		
	}
	
	/*
	 * Get unread timestamps
	 * for user & elements
	 * 
	 */
	static protected function getUnreadTimestamp($type)
	{
		$cacheName = "unread." . $type . "." . \Auth::id();
		
		return \Cache::get($cacheName, date('Y-m-d 0:0:00', time()));
	}
	
	/**
	 * show Users Books
	 */
	public function getBooks($filters = array(), $paginateItems = 25)
	{
		$type = key($filters);
		
		if($type == "orders") $items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserBook", $pluralize = false, "userbook_id");
				
		else $items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserBook");
		
		return $items->orderBy('created_at','desc')
			->with('user', 'orders')
			->paginate($paginateItems);
	}	
	
	/**
	 * show Users Lists
	 */
	public function getLists($filters = array(), $paginateItems = 50)
	{
		$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserList")
			->orderBy('name','asc')
			->orderBy('created_at','desc')
			->with('user', 'elements')
			->with($this->loadSiteTitle())
			->paginate($paginateItems);
		
		list($itemsRegroup, $itemsUsers) = $this->iterateLists($items);
		
		$items['regrouped'] = isset($itemsRegroup) ? $itemsRegroup : array();
		
		$items['users'] = isset($itemsUsers) ? $itemsUsers : array();
		
		$items['basket'] = \Veer\Models\UserList::where('name','=','[basket]')->count();
		
		$items['lists'] = \Veer\Models\UserList::where('name','!=','[basket]')->count();

		return $items;
	}
	
	/* regroup lists & collect users */
	protected function iterateLists($items)
	{
		$itemsRegroup = [];
		
		$itemsUsers = [];
		
		foreach($items as $key => $item)
		{
			$itemsRegroup[$item->users_id][$item->id] = $key;
			
			if($item->users_id > 0 && !isset($itemsUsers[$item->users_id])) 
			{
				$itemsUsers[$item->users_id] = $item->user;
			}
		}
		
		return array($itemsRegroup, $itemsUsers);
	}
	
	/**
	 * show Searches
	 */
	public function getSearches($filters = array(), $paginateItems = 50)
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Search")
			->orderBy('times', 'desc')
			->orderBy('created_at', 'desc')
			->with('users')
			->paginate($paginateItems);
	}	
	
	/*
	 * Set unread timestamps
	 * 
	 */
	protected function setUnreadTimestamp($type)
	{
		$cacheName = "unread." . $type . "." . \Auth::id();

		\Cache::forever($cacheName, now());
	}
	
	/**
	 * Show Comments
	 */
	public function getComments($filters = array(), $paginateItems = 50) 
	{		
		$this->setUnreadTimestamp('comments');
		
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Comment")->orderBy('id','desc')
			->with('elements')
			->paginate($paginateItems); 
			// users -> only for user's page
	}	
	
	/**
	 * show Communications
	 */
	public function getCommunications($filters = array(), $paginateItems = 25)
	{
		$this->setUnreadTimestamp('communications');
		
		$items = $this->filterCommunications(key($filters), $filters);		
		
		$items = $items->orderBy('created_at', 'desc')
			->with('user', 'elements')
			->with($this->loadSiteTitle())
			->paginate($paginateItems);
			
		foreach($items as $key => $item) $itemsUsers[$key] = $this->parseCommunicationRecipients($item->recipients);
		
		app('veer')->loadedComponents['recipients'] = isset($itemsUsers) ? $itemsUsers : array();
		
		return $items;		
	}	
	
	protected function filterCommunications($type, $filters)
	{			
		if(!in_array($type, array("type", "url", "order")))
		{
			return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Communication");
		} 
		
		if($type == "order")
		{
			return \Veer\Models\Communication::where('elements_type', '=', elements($type))
				->where('elements_id','=', head($filters));
		}
		
		return \Veer\Models\Communication::where($type, '=', array_get($filters, $type, 0));
	}
		
	/**
	 * parse communications
	 */
	protected function parseCommunicationRecipients($recipients)
	{
		if(empty($recipients)) { return null; }
		
		$u = json_decode($recipients);
		
		if(!is_array($u) || is_array($u) && count($u) < 1) { return null; } 
		
		$getUsers = \Veer\Models\User::whereIn('id', $u)->get();
	
		$itemsUsers = array();
		
		foreach($getUsers as $user) $itemsUsers[$user->id] = $user;
		
		return $itemsUsers;
	}
	
	/**
	 * show Roles
	 */
	public function getRoles( $filters = array(), $paginateItems = 50 )
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserRole")
			->orderBy('sites_id', 'asc')
			->with('users')
			->with($this->loadSiteTitle())
			->paginate($paginateItems);
	}
	
}
