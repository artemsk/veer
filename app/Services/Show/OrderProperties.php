<?php namespace Veer\Services\Show;

use Illuminate\Support\Facades\Input;

class OrderProperties {
	
	use \Veer\Services\Traits\HelperTraits;
	
	/**
	 * show Bills
	 */
	public function showBills($filters = array(), $orderBy = array('created_at', 'desc'))
	{
		if(Input::get('sort', null)) 
		{ 
			$orderBy[0] = Input::get('sort'); 
		}
		
		if(Input::get('direction', null)) 
		{ 
			$orderBy[1] = Input::get('direction'); 
		}
		
		$type = key($filters);
		
		if($type == 'order' || $type == 'user' || empty($type))
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderBill");
		} 
		
		elseif( $type == 'status')
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderBill", $pluralize = false);
		}
		
		elseif( $type == 'payment')
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderBill", $pluralize = false, "payment_method_id");
		}
		
		else 
		{
			$items = \Veer\Models\OrderBill::where($type, '=', array_get($filters, $type, 0));
		}
		
		if(empty(app('veer')->loadedComponents['billsTypes'])) $this->getExistingBillTemplates();

		return $items->orderBy($orderBy[0], $orderBy[1])
			->with(
				'order', 'user', 'status', 'payment'
			)->paginate(50);
	}
	
	
	/**
	 * show Discounts
	 */
	public function showDiscounts( $filters = array() )
	{
		$type = key($filters);
		
		if($type == "status")
		{
			$items = \Veer\Models\UserDiscount::where('status', '=', head($filters));
		}
		
		else
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserDiscount");
		}
		return $items->orderBy('created_at', 'desc')
			->with('user', 'orders')
			->with($this->loadSiteTitle())
			->paginate(50);
	}	
	
	
	/**
	 * show Payment Methods
	 */
	public function showPayment( $filters = array() )
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderPayment")->orderBy('sites_id', 'asc')
			->with('orders', 'bills')
			->with($this->loadSiteTitle())
			->paginate(50);
	}
	
	
	/**
	 * show Shipping Methods
	 */
	public function showShipping( $filters = array() )
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderShipping")->orderBy('sites_id', 'asc')
			->with('orders')
			->with($this->loadSiteTitle())->paginate(50);
	}
	
	
	/**
	 * show Statuses
	 */
	public function showStatuses( $filters = array() )
	{
		$items = \Veer\Models\OrderStatus::orderBy('manual_order', 'asc');
		
		$items->with(array('orders' => function($q) {
				
			}, 'bills' => function($q) {
				
			}, 'orders_with_history' => function($q) {
				
			}));
			
		return $items->paginate(50);
	}	
	
}
