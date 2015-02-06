<?php namespace Veer\Services\Show;

class User {
	
	use \Veer\Services\Traits\SortingTraits, 
		\Veer\Services\Traits\HelperTraits,
		\Veer\Services\Traits\CommonTraits;
	
	/**
	 * Query Builder: 
	 * 
	 * - who: 1 User
	 * - with: 
	 * - to whom: make() | user/{id}
	 * 
	 * @later: 'role', 'comments', 'books', 'discounts', 'userlists', 'orders', 'bills', 
	 * 'communications', 'administrator', 'searches', 'pages'*
	 */
	public function getUserWithSite($siteId, $id)
	{
		return \Veer\Models\User::where('id', '=', $id)->where('sites_id', '=', $siteId)->where('banned', '!=', '1')->first();
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Items Quantity
	 * - with: 
	 * - to whom: add2cart(), user.login | user/cart/add
	 */
	public function getUserLists($siteId, $userid, $sessionid = null, $name = "[basket]", $onlySum = true) 
	{
		$items = \Veer\Models\UserList::where('sites_id','=', $siteId)
			->where(function($query) use ($userid, $sessionid) {
				if($userid > 0) {
				$query->where('users_id','=', empty($userid) ? 0 : $userid)
					->orWhere('session_id','=', $sessionid);	
				} else {
				$query->where('users_id','=', empty($userid) ? 0 : $userid)
					->where('session_id','=', $sessionid);							
				}
			})->where('name','=', $name);
					
		if($name == "[basket]")	$items->where('elements_type','=','Veer\Models\Product');
		
		return ($onlySum == true) ? $items->sum('quantity') : $items;
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Cart Entities
	 * - with: 
	 * - to whom: user/cart/
	 */
	public function getUserCart($siteId, $userid, $sessionid)
	{
		return $this->getUserLists($siteId, $userid, $sessionid, "[basket]", false)->get();
	}
	
	/**
	 * show Users
	 */
	public function getAllUsers($filters = array(), $orderBy = array('created_at', 'desc'), $paginateItems = 25)
	{
		$orderBy = $this->replaceSortingBy($orderBy);
		
		$items = $this->isUsersFiltered($filters, $orderBy)->with(
			'role', 'comments', 'communications',
			'administrator', 'pages', 'images'
			)
			->with($this->loadSiteTitle())
			->paginate($paginateItems);
			
		app('veer')->loadedComponents['counted'] = \Veer\Models\User::count();
		
		return $items;		
	}
	
	/**
	 * show Users Filtered; TODO: connected with filterOrdersByProduct() method
	 */
	protected function showUsersFiltered($filter_id, $type)
	{
		return \Veer\Models\User::whereHas($type, function($query) use ($filter_id, $type) 
		{
			$query->where( str_plural($type).'_id', '=', $filter_id );
		});
	}
	
	/**
	* check if filter is active and get items 
	*/
	protected function isUsersFiltered($filters, $orderBy)
	{
		foreach($filters as $type => $filter_id)
		{
			if(!empty($filter_id)) $items = $this->showUsersFiltered($filter_id, $type);  
		}		
		
		if(!isset($items)) $items = \Veer\Models\User::select(); 
		
		return $items->orderBy($orderBy[0], $orderBy[1]);
	}
	
	/*
	 * get one user
	 */
	public function getUserAdvanced($user, $options = array())
	{
		if($user == "new") return new \stdClass(); 
			
		$items = \Veer\Models\User::find($user);
			
		if(is_object($items)) 
		{	
			$this->loadUserRelations($items);
			
			$this->loadSiteTitle($items);
					
			$this->loadImagesWithElements($items, array_get($options, 'skipWith', false));
			
			$items['files'] = $this->getOrderDownloads($items->orders);
					
			$items['basket'] = $items->userlists()->where('name','=','[basket]')->count();
			
			$items['lists'] = $items->userlists()->where('name','!=','[basket]')->count();	
			
			if(empty(app('veer')->loadedComponents['billsTypes'])) $this->getExistingBillTemplates();
		}	
		
		return $items;
	}

	/* user relations */
	protected function loadUserRelations($items)
	{
		$items->load('role', 'administrator', 'pages');
		
		$items->load(array(
		'books' => function($q) { $q->with('orders'); },

		'orders' => function($q) { $q->with('userbook', 'userdiscount', 'status', 'delivery', 'payment', 'downloads')
			->with($this->loadSiteTitle())
			->with(array('bills' => function($query) { $query->with('status'); }));
		},

		'discounts' => function($q) { $q->with('orders')->with($this->loadSiteTitle()); },

		'bills' => function($q) { $q->with('status', 'payment'); }));
	}
	
}
