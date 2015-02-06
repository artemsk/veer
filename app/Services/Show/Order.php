<?php namespace Veer\Services\Show;

class Order {
	//put your code here
	
	
	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Order
	 * - with: 
	 * - to whom: make() | order/{id}
	 * 
	 * @later: 'userbook','userdiscount','status','delivery','payment','status_history','bills'
	 */
	protected function orderShowQuery($siteId, $id, $queryParams)
	{
		$userId = $queryParams['userId']; // @testing security
		
		if((bool)$queryParams['administrator'] == true) {
			return Order::where('id', '=', $id)
				->where('hidden', '!=', 1)
				->where('sites_id', '=', $siteId)
				->first();			
		} else {		
			return Order::where('id', '=', $id)
				->where('users_id', '=', $userId)
				->where('hidden', '!=', 1)
				->where('sites_id', '=', $siteId)
				->first();
		}
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
	protected function orderBillsQuery($siteId, $id, $queryParams)
	{
		$userId = $queryParams['userId']; // @testing security
		
		if((bool)$queryParams['administrator'] == true) {
			return \Veer\Models\OrderBill::where('link', '=', array_get($id, 1, null))
				->where('id', '=', array_get($id, 0, null))
				->first();			
		} else {		
			return \Veer\Models\OrderBill::where('link', '=', array_get($id, 1, null))
				->where('id', '=', array_get($id, 0, null))
				->where('users_id', '=', $userId)
				->where('sites_id', '=', $siteId)
				->first();
		}
	}
	
}
