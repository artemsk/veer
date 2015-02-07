<?php namespace Veer\Services\Show;

class Order {

	use \Veer\Services\Traits\SortingTraits, \Veer\Services\Traits\HelperTraits;
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
	
	
	/**
	 * show Orders
	 */
	public function getAllOrders( $filters = array(), $orderBy = array('created_at', 'desc') )
	{		
		$orderBy = $this->replaceSortingBy($orderBy);
				
		$type = key($filters);
		
		$items = $this->filterOrders($type, $filters);
		
		if($type != "archive") $items = $items->where('archive', '!=', true);
		
		if(\Input::get('sort') == null) { $items = $items->orderBy('pin', 'desc'); }
		
		app('veer')->loadedComponents['counted']['active'] = \Veer\Models\Order::where('archive', '!=', true)->count();
		
		app('veer')->loadedComponents['counted']['archived'] = \Veer\Models\Order::where('archive', '=', true)->count();
		
		return $items->orderBy($orderBy[0], $orderBy[1])
			->with('user', 'userbook', 'userdiscount', 'status', 'delivery', 'payment')
			->with($this->loadSiteTitle())
			->with(array('bills' => function($q) { $q->with('status'); }))
			->paginate(50);	
	}
	
	protected function filterOrders($type, $filters)
	{
		$fields = array("userbook" => "userbook_id", 
			"userdiscount" => "userdiscount_id", 
			"status" => "status_id",
			"delivery" => "delivery_method_id",
			"payment" => "payment_method_id",
			"status_history" => "status_id",
			"site" => null,
			"user" => null);
		
		if(array_key_exists($type, $fields) || empty($type)) 
		{
			return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Order", 
				$this->getPluralizeValue($type), array_get($fields, $type));
		}
				
		if($type == "products") return $this->filterOrderByProducts(head($filters));
		
		return \Veer\Models\Order::where($type, '=', head($filters));
	}
	
	protected function filterOrderByProducts($filter_id)
	{
		return \Veer\Models\Order::whereHas('products', function($query) use ($filter_id) 
		{
			$query->where( 'products_id', '=', $filter_id );
		});
	}
	
	protected function getPluralizeValue($type = null)
	{
		if(in_array($type, array("site", "user")) || empty($type)) return true;
		
		return false;		
	}
	
	/**
	 * show One order
	 */
	public function getOrderAdvanced($order)
	{
		if($order == "new") { return new \stdClass(); }
		
		$items = \Veer\Models\Order::find($order);
			
		if(is_object($items)) 
		{
			$this->loadOrderRelations($items);
			
			$this->loadSiteTitle($items);
					
			if(is_object($items->products)) $items->products = $this->regroupOrderProducts($items->products);
			
			if(is_object($items->orderContent)) $items->orderContent = $this->orderContentParse($items->orderContent, $items->products);
		}	
		
		if(empty(app('veer')->loadedComponents['billsTypes'])) $this->getExistingBillTemplates();
		
		return $items;
	}
	
	/* user relations */
	protected function loadOrderRelations($items)
	{
		// do not load 'downloads' because we have products->with(downloads)
		$items->load('status', 'delivery', 'payment', 'secrets', 'orderContent');
		
		$items->load(array(
			'user' => function($q) { $q->with('role', 'administrator'); },

			'userbook' => function($q) { $q->with('orders'); }, 

			'userdiscount' => function($q) { $q->with('orders'); },

			'bills' => function($q) { $q->with('status', 'payment'); },

			'products' => function($q) { $q->with('images', 'categories', 'tags', 'attributes', 'downloads'); },

			'status_history' => function($q) { $q->withTrashed(); }));		
	}
	
	/**
	 * regrouped order products by pivot-id
	 */
	protected function regroupOrderProducts($products = array())
	{
		$regrouped = array();
		
		foreach($products as $p) { array_set($regrouped, $p->pivot->id, $p); }
		
		return $regrouped;
	}
	
	/** 
	 * parse order content's attributes and make elements summary (cloud)
	 */
	protected function orderContentParse($content, $products)
	{
		foreach($content as $key => $p)
		{
			if( array_get($products, $p->id) != null)
			{
				if(!empty($p->attributes)) $p->attributesParsed = app('veershop')->parseAttributes($p->attributes, $p->id, $products[$p->id]);

				foreach(!empty($products[$p->id]->downloads) ? $products[$p->id]->downloads : array() as $c)
				{
						$downloads[] = $c;
				}	

				foreach(array("categories", "tags", "attributes") as $cloudType)
				{
					foreach(!empty($products[$p->id]->{$cloudType}) ? $products[$p->id]->{$cloudType} : array() as $c)
					{
						$bigCloud[$cloudType]['t'][$c->id] = $c->title;
						//$bigCloud['q'][$c->id] = isset($categoriesCloud['q'][$c->id]) ? ($categoriesCloud['q'][$c->id] + 1) : 1; 
					}
				}				
			} 
		}
		
		return array('content' => $content, 'downloads' => isset($downloads) ? $downloads : array(), 
											'statistics' => isset($bigCloud) ? $bigCloud : array());
	}
	
}
