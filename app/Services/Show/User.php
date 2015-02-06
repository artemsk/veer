<?php namespace Veer\Services\Show;

class User {
	
	
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
	
}
