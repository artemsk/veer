<?php namespace Veer\Services\Show;

use Illuminate\Support\Facades\Input;

class OrderProperties {
	
	use \Veer\Services\Traits\HelperTraits, \Veer\Services\Traits\SortingTraits;
	
	/**
	 * show Bills
	 */
	public function getBills($filters = array(), $orderBy = array('created_at', 'desc'), $paginateItems = 50)
	{
		$orderBy = $this->replaceSortingBy($orderBy);
		
		$items = $this->filterBills(key($filters), $filters);
		
		if(empty(app('veer')->loadedComponents['billsTypes'])) $this->getExistingBillTemplates();

		return $items->orderBy($orderBy[0], $orderBy[1])
			->with(
				'order', 'user', 'status', 'payment'
			)->paginate($paginateItems);
	}
	
	/* filter bills */
	protected function filterBills($type, $filters)
	{
		if(in_array($type, array('order', 'user', 'status', 'payment')) || empty($type))
		{
			return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderBill",
				in_array($type, array('payment', 'status')) ? false : true,
				$type == 'payment' ? 'payment_method_id' : null
			);
		}
		
		return \Veer\Models\OrderBill::where($type, '=', array_get($filters, $type, 0));
	}
	
	/**
	 * show Discounts
	 */
	public function getDiscounts( $filters = array(), $paginateItems = 50 )
	{
		if(key($filters) == "status") $items = \Veer\Models\UserDiscount::where('status', '=', head($filters));

		else $items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserDiscount");
			
		return $items->orderBy('created_at', 'desc')
			->with('user', 'orders')
			->with($this->loadSiteTitle())
			->paginate($paginateItems);
	}	
	
	/**
	 * show Payment Methods
	 */
	public function getPayment( $filters = array(), $paginateItems = 50 )
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderPayment")
			->orderBy('sites_id', 'asc')
			->with('orders', 'bills')
			->with($this->loadSiteTitle())
			->paginate($paginateItems);
	}
	
	/**
	 * show Shipping Methods
	 */
	public function getShipping( $filters = array(), $paginateItems = 50 )
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderShipping")
			->orderBy('sites_id', 'asc')
			->with('orders')
			->with($this->loadSiteTitle())
			->paginate($paginateItems);
	}
	
	/**
	 * show Statuses
	 */
	public function getStatuses($paginateItems = 50)
	{
		return \Veer\Models\OrderStatus::orderBy('manual_order', 'asc')
			->with('orders', 'bills', 'orders_with_history')
			->paginate($paginateItems);
	}	
	
}
