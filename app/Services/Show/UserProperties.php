<?php namespace Veer\Services\Show;

class UserProperties {
	
	use \Veer\Services\Traits\HelperTraits;
	
	/**
	 * show Unread Numbers
	 */
	static public function showUnreadNumbers($model, $raw = null, $period = 5)
	{
		$modelFull = "\\" . elements( str_singular($model) );
			
		$numbers = $modelFull::where('created_at', '>=', app('veer')->getUnreadTimestamp( str_plural($model) ));
		
		if (!empty($raw)) { $numbers->whereRaw($raw); }
		
		$numbers = app('veer')->cachingQueries->makeAndRemember($numbers, 'count', $period, null, 'unreadNumbers'.$model);
		
		return $numbers > 0 ? $numbers : null;		
	}
	
	/**
	 * show Users Books
	 */
	public function showBooks($filters = array())
	{
		$type = key($filters);
		
		if($type == "orders") 
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserBook", $pluralize = false, "userbook_id");
		}
		
		else
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserBook");
		}
		
		$items = $items->orderBy('created_at','desc')
			->with('user', 'orders')
			->paginate(25);
		
		$items['counted'] =
			\Veer\Models\UserBook::count();
		
		return $items;
	}	
	
	/**
	 * show Users Lists
	 */
	public function showLists($filters = array())
	{
		$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserList")
			->orderBy('name','asc')
			->orderBy('created_at','desc')
			->with('user', 'elements')
			->with($this->loadSiteTitle())
			->paginate(50);
		
		foreach($items as $key => $item)
		{
			$itemsRegroup[$item->users_id][$item->id] = $key;
			
			if($item->users_id > 0 && !isset($itemsUsers[$item->users_id])) 
			{
				$itemsUsers[$item->users_id] = $item->user;
			}
		}
		
		$items['regrouped'] = isset($itemsRegroup) ? $itemsRegroup : array();
		
		$items['users'] = isset($itemsUsers) ? $itemsUsers : array();
		
		$items['basket'] = 
			\Veer\Models\UserList::where('name','=','[basket]')->count();
		
		$items['lists'] = 
			\Veer\Models\UserList::where('name','!=','[basket]')->count();

		return $items;
	}
	
	
	/**
	 * show Searches
	 */
	public function showSearches($filters = array())
	{
		$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Search")
			->orderBy('times', 'desc')
			->orderBy('created_at', 'desc')
			->with('users')
			->paginate(50);
		
		$items['counted'] = 
			\Veer\Models\Search::count();
		
		return $items;
	}	
	
	
	/**
	 * Show Comments
	 */
	public function showComments($filters = array()) 
	{		
		$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Comment")->orderBy('id','desc')
			->with('elements')
			->paginate(50); 
			// users -> only for user's page
				
		$items['counted'] = \Veer\Models\Comment::count();
		
		$items['counted_unread'] = self::showUnreadNumbers("comment");
		
		return $items;
	}	
	
	
	/**
	 * show Communications
	 */
	public function showCommunications($filters = array())
	{
		$type = key($filters);
		
		if($type != "type" && $type != "url" && $type != "order")
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Communication");
		} 
		
		elseif($type == "order")
		{
			$items = \Veer\Models\Communication::where('elements_type', '=', elements($type))
				->where('elements_id','=', head($filters));
		}
		
		else
		{
			$items = \Veer\Models\Communication::where($type, '=', array_get($filters, $type, 0));
		}
		
		$items = $items->orderBy('created_at', 'desc')
			->with('user', 'elements')
			->with($this->loadSiteTitle())
			->paginate(25);
			
		foreach($items as $key => $item)
		{
			$itemsUsers[$key] = $this->parseCommunicationRecipients($item->recipients);
		}
		
		$items['recipients'] = isset($itemsUsers) ? $itemsUsers : array();
		
		$items['counted'] = 
			\Veer\Models\Communication::count();
		
		$items['counted_unread'] = self::showUnreadNumbers("communication");
		
		return $items;		
	}	
	
	/**
	 * parse communications
	 * @param string $recipients
	 */
	protected function parseCommunicationRecipients($recipients)
	{
		if(empty($recipients)) { return null; }
		
		$u = json_decode($recipients);
		
		if(!is_array($u) || is_array($u) && count($u) < 1) { return null; } 
		
		$getUsers = \Veer\Models\User::whereIn('id', $u)->get();
	
		$itemsUsers = array();
		
		foreach($getUsers as $user) 
		{
			$itemsUsers[$user->id] = $user;
		}
		
		return $itemsUsers;
	}
	
	/**
	 * show Roles
	 */
	public function showRoles( $filters = array() )
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserRole")->orderBy('sites_id', 'asc')
			->with('users')
			->with($this->loadSiteTitle())
			->paginate(50);
	}
	
	
}
