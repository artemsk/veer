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
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Order", 
				$this->getPluralizeValue($type), array_get($fields, $type));
		}
				
		elseif($type == "products") $items = $this->filterOrderByProducts(head($filters));
		
		else $items = \Veer\Models\Order::where($type, '=', head($filters));
		
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
	 * @param type $order
	 */
	public function showOneOrder($order)
	{
		if($order == "new") { return new \stdClass(); }
		
		$items = \Veer\Models\Order::find($order);
			
		if(is_object($items)) 
		{
			$items->load(
				'status', 'delivery', 'payment', 'secrets', 'orderContent'
			);
			// do not load 'downloads' because we have products->with(downloads)
			
			$this->loadSiteTitle($items);
					
			$items->load(array(
				'user' => function($q)
					{
						$q->with('role', 'administrator');
					},
				'userbook' => function($q)
					{
						$q->with('orders');
					}, 
				'userdiscount' => function($q)
					{
						$q->with('orders');
					},
				'bills' => function($q)
					{
						$q->with('status', 'payment');
					},
				'products' => function($q)
					{
						$q->with('images', 'categories', 'tags', 'attributes', 'downloads');
					},
				'status_history' => function($q)
					{
						$q->withTrashed();
					}));
		}	
		
		$regroupedProducts = array();
		
		if(is_object($items->products))
		{
			$regroupedProducts = $items->products = $this->regroupOrderProducts($items->products);
		}
		
		if(is_object($items->orderContent))
		{
			$items->orderContent = $this->orderContentParse($items->orderContent, $regroupedProducts);
		}
		
		if(empty($this->billsTypes)) $this->getExistingBillTemplates();
		
		return $items;
	}
	
	/**
	 * regrouped order products by pivot-id
	 */
	protected function regroupOrderProducts($products = array())
	{
		$regrouped = array();
		
		foreach($products as $p)
		{
			array_set($regrouped, $p->pivot->id, $p);
		}
		
		return $regrouped;
	}
	
	/** 
	 * parse order content's attributes and make elements summary (cloud)
	 * @param type $content
	 * @param type $products
	 */
	protected function orderContentParse($content, $products = null, $skipStats = false)
	{
		$downloads =
		$categoriesCloud = 
		$tagsCloud = 
		$attributesCloud = array();
		
		foreach($content as $key => $p)
		{
			if( array_get($products, $p->id, null) != null)
			{
				if(!empty($p->attributes)) 
				{
					$p->attributesParsed = app('veershop')->parseAttributes($p->attributes, $p->id, $products[$p->id]);
				}
				
				foreach(!empty($products[$p->id]->downloads) ? $products[$p->id]->downloads : array() as $c)
				{
						$downloads[] = $c;
				}
					
				if($skipStats === false) 
				{	
					foreach(!empty($products[$p->id]->categories) ? $products[$p->id]->categories : array() as $c)
					{
						$categoriesCloud['t'][$c->id] = $c->title;
						//$categoriesCloud['q'][$c->id] = isset($categoriesCloud['q'][$c->id]) ? ($categoriesCloud['q'][$c->id] + 1) : 1; 
					}

					foreach(!empty($products[$p->id]->tags) ? $products[$p->id]->tags : array() as $c)
					{
						$tagsCloud['t'][$c->id] = $c->name;
						//$tagsCloud['q'][$c->id] = isset($tagsCloud['q'][$c->id]) ? ($tagsCloud['q'][$c->id] + 1) : 1;
					}	

					foreach(!empty($products[$p->id]->attributes) ? $products[$p->id]->attributes : array() as $c)
					{
						$attributesCloud['t'][$c->id] = $c->name.":".$c->val;
						//$attributesCloud['q'][$c->id] = isset($attributesCloud['q'][$c->id]) ? ($attributesCloud['q'][$c->id] + 1) : 1;
						// TODO: how to count values properly
					}
				}
			} 
		}
		
		return array('content' => $content, 'downloads' => $downloads, 'statistics' => array(
			"categories" => $categoriesCloud,
			"tags" => $tagsCloud,
			"attributes" => $attributesCloud
		));
	}
}
