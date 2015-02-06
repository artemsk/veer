<?php namespace Veer\Services\Show;

class Order {

	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Order
	 * - with: 
	 * - to whom: make() | order/{id}
	 * 
	 * @later: 'userbook','userdiscount','status','delivery','payment','status_history','bills'
	 */
	public function getOrderWithSite($siteId, $id, $userId = null, $bypassUser = false)
	{
		$items = \Veer\Models\Order::where('id','=',$id)
			->where('hidden', '!=', 1)
			->where('sites_id', '=', $siteId);
		
		if(!$bypassUser) $items->where('users_id','=',$userId);
		
		return $items->first();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Bill
	 * - with: 
	 * - to whom: make() | order/bills/{id}
	 * 
	 * @later: 'order', 'user', 'status', 'payment'
	 */
	public function getBillWithSite($siteId, $id, $lnk, $userId = null, $bypassUser = false)
	{
		$items = \Veer\Models\OrderBill::where('link', '=', $lnk)
			->where('id', '=', $id);
		
		if(!$bypassUser) $items->where('users_id','=',$userId)->where('sites_id', '=', $siteId);
		
		return $items->first();
	}
	
}
