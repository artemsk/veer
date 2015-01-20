<?php namespace Veer;

use Carbon\Carbon;

// TODO: empty current_user_role & current_user_discount* after logging out (flushRemembered)

class VeerShop {
	
	protected $currency_symbol;
	
	protected $current_user_role;
	
	protected $current_user_discount;
	
	protected $current_user_discount_by_role;
	
	protected $discount_checked = false;
	
	protected $discount_by_role_checked = false;
	
	protected $use_cluster;
	
	/**
	 * Price calculator:
	 * user_discounts > role > price_sales (promotions) > price
	 * 
	 * @params (array)$prices
	 * @return price
	 */	
	public function __construct() 
	{
		$this->currency_symbol = db_parameter('CURRENCY_SYMBOL', config('veer.currency_symbol'));
		
		$this->use_cluster = db_parameter('USE_CLUSTER', config('veer.use_cluster'));
	}

	
	
	
	public function getPrice($product, $bypassUser = false, $custom = null)
	{
		if(!empty($custom))
		{
			if(!administrator()) $custom = null;
		}
		
		$price = $this->calculator($product, $bypassUser, $custom);
		$regular_price = $this->currency($product['price'], $product['currency'], $custom);
		
		if($regular_price!= $price) {
			return app('view')->make(app('veer')->loadedComponents['template'] . ".elements.price-discount")
				->with('price', $this->priceFormat($price))
				->with('regular_price', $this->priceFormat($regular_price));
		} else {
			return app('view')->make(app('veer')->loadedComponents['template'] . ".elements.price-regular")->with('price', $this->priceFormat($price));
		}
	}
	
	
	
	
	public function priceFormat($price)
	{		
		if(round($price) == $price) {
			$price = number_format($price, 0);
		} else {
			$price = number_format($price, 2);
		}
		
		$price = strtr($this->currency_symbol, array("[price]" => $price));
				
		return $price;
	}
	
	
	
	
	public function priceCurrencyFormat($price, $itemCurrency, $custom = null)
	{
		return $this->priceFormat($this->currency($price, $itemCurrency, $custom));
	}
	
	
	
	
	public function calculator($product, $bypassUser = false, $custom = null)
	{
		// 1
		// First of all, we take regular price
		$price = $product['price'];
                        
		// 2
		// We check if sales price exists and it's active
		if(Carbon::now() >= $product['price_sales_on'] && Carbon::now() <= $product['price_sales_off']) 
		{ 
			$price = $product['price_sales']; 			
		}
            
		// 3 
		// We check if we have logged user & if he has active discount
		if(app('auth')->id() <= 0 || $bypassUser == true) { 
			return $this->currency($price, $product['currency'], $custom);
		}
		
		// 4
		// We check if existing user have discount
		$discounts = $this->discounts($price, $custom);
		
		if($discounts['discount'] == true) {
			return $this->currency($discounts['price'], $product['currency'], $custom);
		}
		
		// 5 
		// We check if existing user have discount by his role
		$discounts_by_role = $this->discounts_by_role($product, $price, $custom);
		
		if($discounts_by_role['discount'] == true) {
			return $this->currency($discounts_by_role['price'], $product['currency'], $custom);
		}		
		
		// if no discounts for user
		return $this->currency($price, $product['currency'], $custom);				
	}
	
	
	
	
	public function discounts($price, $custom = null) 
	{	
		$discount = false;
		
		$siteId = app('veer')->siteId;
		$userId = app('auth')->id();
		$whereraw = "id > 0";
		
		if(!empty($custom))
		{
			$siteId = array_get($custom, 'sites_id', $siteId);
			$userId = array_get($custom, 'users_id', $userId);
			if(array_get($custom, 'discount_id') > 0)
			{
				$whereraw = "id = ".array_get($custom, 'discount_id');
			}
		}
		
		if($this->discount_checked == false) {
			if(!app('session')->has('discounts_checked')) {
				$this->current_user_discount = \Veer\Models\UserDiscount::where('sites_id','=',$siteId)
				->where('users_id','=',$userId)
				->where('status','=','active')
				->whereRaw($whereraw)
				->whereNested(function($query) {
					$query->whereRaw(" ( expires = '1' and (expiration_day >= '" . date('Y-m-d H:i:00', time()) . 
						"' or expiration_times > '0') ) or ( expires = '0' ) ");
				})
				->orderBy('id')->select('discount')->remember(2)->first();	
				app('session')->put('discounts',$this->current_user_discount);
				app('session')->put('discounts_checked', true);				
			} else {
				$this->current_user_discount = app('session')->get('discounts');
			}
			$this->discount_checked = true;	
		} 
		
		if(count(@get_object_vars($this->current_user_discount)) > 0) {
			$price = $price * ( 1 - ( $this->current_user_discount->discount / 100));		
			$discount = true;
		}	
	 
		return array('discount' => $discount, 'price' => $price);		
	}
	
	
	
	
	public function discounts_by_role($product, $price, $custom = null)
	{
		$discount = false;

		$roleId = app('auth')->user()->roles_id;
		$siteId = app('veer')->siteId;
		
		if(!empty($custom))
		{
			$roleId = array_get($custom, 'roles_id', $roleId);
			$siteId = array_get($custom, 'sites_id', $siteId);
		}
		
		if(empty($this->current_user_role)) {
			if(!app('session')->has('roles_id')) {
				$this->current_user_role = $roleId;
				app('session')->put('roles_id', $this->current_user_role);
			} else {
				$this->current_user_role = app('session')->get('roles_id');
			}	
		}
		
		if($this->discount_by_role_checked == false) {
			if(!app('session')->has('discounts_by_role_checked')) {	
				$this->current_user_discount_by_role = \Veer\Models\UserRole::where('sites_id','=',$siteId)
				->where('id','=',$this->current_user_role)
				->whereNested(function($query) {
					$query->where('discount','>',0)
					->orWhere('price_field','!=','price');
				})
				->select('price_field','discount')->remember(1)->first();
				app('session')->put('discounts_by_role',$this->current_user_discount_by_role);
				app('session')->put('discounts_by_role_checked', true);
			} else {
				$this->current_user_discount_by_role = app('session')->get('discounts_by_role');
			} 	
			$this->discount_by_role_checked = true;	
		}
			
		if(count(@get_object_vars($this->current_user_discount_by_role)) > 0) {
			$price = $product[$this->current_user_discount_by_role->price_field];
			if($this->current_user_discount_by_role->discount > 0) { 
				$price = $price * ( 1 - ( $this->current_user_discount_by_role->discount / 100)); } 	 	
			$discount = true;
		}	
	 
		return array('discount' => $discount, 'price' => $price);			
	}
	
	
	
	
	/**
	 * Use shop or product currencies
	 * itemCurrency > shopCurrency > price
	 * 
	 * TODO: later get sites_id and get specific db parameter for specific site
	 * 
	 * @params $price, $itemCurrency
	 * @return $price
	 */
	public function currency($price, $itemCurrency, $custom = null)
	{		
		if($itemCurrency > 0 && $itemCurrency != 1) { return ($price * $itemCurrency);  }
		
		$shopCurrency = array_get($custom, 'forced_currency', db_parameter('SHOP_CURRENCY', 1));
		
		if($shopCurrency > 0 && $shopCurrency != 1) {
			return ($price * $shopCurrency);
		}
		
		return $price;		
	}
	
	
	
	
	/**
	 * get real order Id
	 * @param int $cluster
	 * @param int $cluster_oid
	 * @return string
	 */
	public function getOrderId($cluster, $cluster_oid)
	{
		if($this->use_cluster == true) 
		{
			return $cluster . "." . $cluster_oid;
		}
		
		return $cluster_oid;
	}
	
	
	
	
	/**
	 * update or create new user book (or office address)
	 * @param type $book
	 */
	public function updateOrNewBook($book)
	{
		\Event::fire('router.filter: csrf');
		
		\Eloquent::unguard();
		
		$bookId = array_get($book, 'bookId', null);

		if(isset(app('veer')->administrator_credentials))
		{
			$usersId = array_get($book, 'fill.users_id', \Auth::id());
			if(empty($usersId)) { $usersId = \Auth::id(); }
		}

		else { $usersId = \Auth::id(); }
		
		$book['fill']['users_id'] = $usersId;
		
		$b = empty($bookId) ? new \Veer\Models\UserBook : \Veer\Models\UserBook::firstOrNew(array("id" => $bookId));
		
		if(isset($book['fill']['address']) && empty($book['fill']['address'])) { return false; }
		
		if(isset($book['fill'])) { $b->fill($book['fill']); }
		$b->primary = array_get($book, 'checkboxes.primary', false) ? true : false;
		$b->office_address = array_get($book, 'checkboxes.office_address', false) ? true : false;
		$b->save();
		
		return $b;
	}
	
	
	
	
	/**
	 * add New Order
	 * 
	 * pin, close_time, type, status_id, cluster, cluster_oid,
	 * users_id, user_type, userdiscount_id, userbook_id, country, city, address, hash
	 * 
	 * @param $order
	 * @param $usersId
	 * @return array
	 */
	public function addNewOrder($order, $usersId, $book = array())
	{
		$order->pin = false;
		$order->close_time = null;
		$order->type = 'reg';

		$firststatus = \Veer\Models\OrderStatus::firststatus()->pluck('id');
		$order->status_id = !empty($firststatus) ? $firststatus : 0;

		$cluster = \Veer\Models\Configuration::where('sites_id', '=', $order->sites_id)
			->where('conf_key', '=', 'CLUSTER')->pluck('conf_val');
		if (empty($cluster)) $cluster = 0;

		$order->cluster = $cluster;
		$order->cluster_oid = \Veer\Models\Order::where('sites_id', '=', $order->sites_id)
				->where('cluster', '=', $cluster)->max('cluster_oid') + 1;

		if (empty($usersId) && !empty($order->email)) 
		{
			$findUser = \Veer\Models\User::where('sites_id', '=', $order->sites_id)
				->where('email', '=', $order->email)->first();

			if (is_object($findUser)) 
			{
				$order->users_id = $findUser->id;
				if(empty($order->phone)) $order->phone = $findUser->phone;
				if(empty($order->name)) $order->name = $findUser->firstname . " " . $findUser->lastname;
			} else {
				$newUser = new \Veer\Models\User;

				$newUser->sites_id = $order->sites_id;
				$newUser->email = $order->email;
				$newUser->phone = $order->phone;

				$password2Email = str_random(16);
				$newUser->password = $password2Email;
				$newUser->save();

				$order->users_id = $newUser->id;
				$order->type = 'unreg';

				$unregStatus = \Veer\Models\OrderStatus::unregstatus()->pluck('id');
				if (!empty($unregStatus)) $order->status_id = $unregStatus;
			}
		}

		if(!empty($usersId) && (empty($order->email) || empty($order->phone) || empty($order->name)))
		{
			$findUser = \Veer\Models\User::where('id','=', $usersId)->first();
			if(is_object($findUser))
			{
				if(empty($order->email)) $order->email = $findUser->email;
				if(empty($order->phone)) $order->phone = $findUser->phone;
				if(empty($order->name)) $order->name = $findUser->firstname . " " . $findUser->lastname;
			}
		}
		
		$userRole = \Veer\Models\UserRole::whereHas('users', function ($q) use ($order) {
			$q->where('users.id', '=', $order->users_id);
		})->pluck('role');

		if (isset($userRole)) $order->user_type = $userRole;

		if (!empty($order->userdiscount_id)) {
			$checkDiscount = \Veer\Models\UserDiscount::where('id', '=', $order->userdiscount_id)
				->where('sites_id', '=', $order->sites_id)
				->whereNested(function ($q) use ($order) {
					$q->where('users_id', '=', 0)
						->orWhere('users_id', '=', $order->users_id);
				})
				->whereNested(function ($q) {
					$q->where('status', '=', 'wait')
						->orWhere('status', '=', 'active');
				})
				->first();

			if (is_object($checkDiscount)) {
				$checkDiscount->users_id = $order->users_id;
				$checkDiscount->status = 'active';
				$checkDiscount->save();
			} else {
				$order->userdiscount_id = 0;
			}
		}

		$book['fill']['users_id'] = $order->users_id;

		$newBook = $this->updateOrNewBook($book);

		if (isset($newBook) && is_object($newBook)) {
			$order->userbook_id = $newBook->id;
			$order->country = $newBook->country;
			$order->city = $newBook->city;
			$order->address = trim($newBook->postcode . " " . $newBook->address);
		}

		$order->hash = bcrypt($order->cluster . $order->cluster_oid . $order->users_id . $order->sites_id . str_random(16));
		$order->save();
		
		$this->incrementOrdersCount($order->users_id);
		
		$secret = new \Veer\Models\Secret(array("secret" => str_random(64)));
		$order->secrets()->save($secret);
		
		return array($order, isset($checkDiscount) ? $checkDiscount : null);
	}
	
	
	
	
	/**
	 * Increment Orders Counts
	 */
	protected function incrementOrdersCount($users_id)
	{
		\Veer\Models\User::where('id','=',$users_id)
			->increment('orders_count');
	}
	
	
	
	
	/**
	 * attach Order Content (get data from cart or request)
	 * @param $order
	 */
	public function attachOrderContent($newContent, $order)
	{
		if (starts_with($newContent, ":")) 
		{
			$parseContent = explode(":", substr($newContent, 1));
			foreach ($parseContent as $product) 
			{
				$p = explode(",", $product);
				if (array_get($p, 0) != null) 
				{
					$content = $this->editOrderContent(new \Veer\Models\OrderProduct, array(
						"product" => 1,
						"products_id" => array_get($p, 0),
						"quantity" => array_get($p, 1, 1),
						"attributes" => array_get($p, 2)
					), $order);

					$content->save();
				}
			}
		} else {
			$parseContent = explode(":", $newContent);

			$content = new \Veer\Models\OrderProduct;
			$content->orders_id = $order->id;
			$content->product = 0;
			$content->products_id = 0;
			$content->name = array_get($parseContent, 0, '[?]');
			$content->original_price = $content->price_per_one = array_get($parseContent, 1, 0);
			$content->quantity = array_get($parseContent, 2, 1);
			$content->price = $content->price_per_one * $content->quantity;
			$content->save();
		}
	}
	
	
	
	
	/**
	 * edit Order Content
	 */
	public function editOrderContent($content, $ordersProducts, $order)
	{
		$productsId = array_get($ordersProducts, 'products_id');
		$attributes = array_pull($ordersProducts, 'attributes');
			
		$oldQuantity = isset($content->quantity) ? $content->quantity : 1;
		
		$content->orders_id = $order->id;
		
		$content->attributes = json_encode(explode(",", $attributes));
			
		$content->quantity = array_pull($ordersProducts, 'quantity', 1);
		if($content->quantity < 1) $content->quantity = 1;
		
		\Eloquent::unguard();

		if(empty($productsId)) 
		{
			$content->product = 0;
			$content->fill($ordersProducts);
			$content->price = $content->quantity * $content->price_per_one;
			return $content;
		}

		$content->product = 1;
		$content->name = array_get($ordersProducts, 'name');
		$content->original_price = array_get($ordersProducts, 'original_price');
		$content->price_per_one =  array_get($ordersProducts, 'price_per_one');
		
		if($content->quantity != $oldQuantity) 
		{
			$content->weight = (array_get($ordersProducts, 'weight') / $oldQuantity) * $content->quantity;
		} 
		
		else 
		{
			$content->weight = array_get($ordersProducts, 'weight');
		}
		
		if($content->products_id != array_get($ordersProducts, 'products_id') || !empty($content->attributes))
		{
			$product = \Veer\Models\Product::find(array_get($ordersProducts, 'products_id'));
		}
		
		// use attributes
		if(!empty($content->attributes) && is_object($product))
		{
			$attributesParsed = $this->parseAttributes($content->attributes, $content->id, $product);
			if(is_array($attributesParsed)) {
				foreach($attributesParsed as $attr) {
					$content->price_per_one = 
						$attr['pivot']['product_new_price'] > 0 ? $attr['pivot']['product_new_price'] : $content->price_per_one;
				}
			}
		}
		
		if($content->products_id != array_get($ordersProducts, 'products_id') && is_object($product))
		{
			$shopCurrency = \Veer\Models\Configuration::where('sites_id','=',$order->sites_id)
					->where('conf_key','=','SHOP_CURRENCY')->pluck('conf_val');
			$shopCurrency = !empty($shopCurrency) ? $shopCurrency : null;
			
			$content->products_id = $product->id;
			$content->original_price = empty($content->original_price) ? 
				$this->currency($product->price, $product->currency, array("forced_currency" => $shopCurrency)) : $content->original_price;
			$content->name = $product->title;
			$content->weight = $product->weight * $content->quantity;

			if(empty($content->price_per_one))
			{
				$this->flushRememberedDiscounts();

				$pricePerOne = $this->calculator($product, false, array(
					"sites_id" => $order->sites_id,
					"users_id" => $order->users_id,
					"roles_id" => \Veer\Models\UserRole::where('role','=', $order->user_type)->pluck('id'),
					"discount_id" => $order->userdiscount_id,
					"forced_currency" => $shopCurrency 
				));

				$content->price_per_one = $pricePerOne;
			}
		}

		$content->price = $content->quantity * $content->price_per_one;

		$content->comments = array_get($ordersProducts, 'comments', '');
		
		return $content;
	}
	
	
	
	
	/**
	 * parseAttributes
	 * @param orders_products table $attributes
	 * @param orders_products table $id
	 * @param object $product
	 */
	public function parseAttributes($attributes, $id, $product)
	{
		$a = json_decode($attributes);

		$attributesParsed = array();

		foreach( is_array($a) ? $a : array() as $value)
		{
			$attribute = $product->attributes->filter(function($attr) use ($value)
			{
				return $attr->id == $value ? $attr : null;
			});

			$attributesParsed = array_merge($attributesParsed, $attribute->toArray());
		}	

		return $attributesParsed;
	}	
	
	
	
	
	/**
	 * recalculate order delivery
	 */
	public function recalculateOrderDelivery($order)
	{
		$delivery = \Veer\Models\OrderShipping::find($order->delivery_method_id);

		if(!is_object($delivery)) return $order;
		
		// change address if it's pickup
		if ($delivery->delivery_type == "pickup" && !empty($delivery->address)) 
		{
			// TODO: if we have several address how to choose the right one?
			// now it's just one address!
			$parseAddresses = json_decode($delivery->address);

			$order->country = array_get(head($parseAddresses), 0);
			$order->city = array_get(head($parseAddresses), 1);
			$order->address = array_get(head($parseAddresses), 2);
			$order->userbook_id = 0;
		}

		// 2
		switch ($delivery->payment_type) 
		{
			case "free":
				$order->delivery_price = 0;
				$order->delivery_free = true;
				$order->delivery_hold = false;
				break;
			
			case "fix":
				$order->delivery_price = $delivery->price;
				$order->delivery_free = false;
				$order->delivery_hold = false;
				break;
		}
		
		// 3 calculator
		if (!empty($delivery->func_name) && class_exists('\\Veer\\Ecommerce\\' . $delivery->func_name)) 
		{
			$class = '\\Veer\\Ecommerce\\' . $delivery->func_name;

			$deliveryFunc = new $class;

			$getData = $deliveryFunc->fire($order, $delivery);

			$order->delivery_price = isset($getData->delivery_price) ? $getData->delivery_price : $delivery->price;
			$order->delivery_free = isset($getData->delivery_free) ? $getData->delivery_free : false;
			$order->delivery_hold = isset($getData->delivery_hold) ? $getData->delivery_hold : true; // TODO: do we need this?

			$delivery->discount_enable = isset($getData->discount_enable) ? $getData->discount_enable : $delivery->discount_enable;
			$delivery->discount_price = isset($getData->discount_price) ? $getData->discount_price : $delivery->discount_price;
			$delivery->discount_conditions = isset($getData->discount_conditions) ? $getData->discount_conditions : $delivery->discount_conditions;
		}

		// 4
		if ($delivery->discount_enable == 1 && $delivery->discount_price > 0) 
		{
			$checkConditions = $this->checkDisountConditions($delivery->discount_conditions, $order);

			if (array_get($checkConditions, 'activate') == true || array_get($checkConditions, 'conditions') == false) 
			{
				if (array_get($checkConditions, 'price') == "total") 
				{
					$content = new \Veer\Models\OrderProduct;
					$content->orders_id = $order->id;
					$content->product = 0;
					$content->products_id = 0;
					$content->name = \Lang::get('veeradmin.order.content.delivery.discount') . " (-" . $delivery->discount_price . "%)";
					$content->original_price = 0 - ($order->content_price * ($delivery->discount_price / 100));
					$content->quantity = 1;
					$content->attributes = "";
					$content->comments = \Lang::get('veeradmin.order.content.discount');
					$content->price_per_one = $content->original_price;
					$content->price = $content->original_price;
					$content->save();

					$order->content_price = $order->content_price + $content->price;
				} 
				
				else 
				{
					$order->delivery_price = $order->delivery_price * ( 1 - ( $delivery->discount_price / 100));
				}
			}
		}

		if ($order->delivery_price <= 0 && $order->delivery_hold != true) 
		{
			$order->delivery_free = true;
		}

		return $order;
	}

	
	
	
	/**
	 * check Discount Conditions for Shipping|Payment
	 * @param type $custom_conditions
	 * @param type $order
	 * @return type
	 */
	protected function checkDisountConditions($custom_conditions, $order, $price_to_discount = "delivery")
	{
		$conditions_exist = false;
		$activate_discount = false;		
		
		$conditions = preg_split('/[\n\r]+/', $custom_conditions );
		
		if (count($conditions) > 0) 
		{
			foreach ($conditions as $c) 
			{
				if(empty($c)) continue;
				
				$parseCondition = explode(":", $c);
				
				$condition = array_get($parseCondition, 0);
				$value = array_get($parseCondition, 1);
				$value = trim($value);
				
				switch ($condition) 
				{
					case "$": // discount by price
						if ($order->content_price >= $value) $activate_discount = true;
						$conditions_exist = true;
						break;

					case "w": // discount by weight
						if ($order->weight >= $value) $activate_discount = true;
						$conditions_exist = true;
						break;
						
					case "l": // discount by location (country, city)
						if (str_contains( mb_strtolower($order->country), mb_strtolower($value) )) $activate_discount = true;
						if (str_contains( mb_strtolower($order->city), mb_strtolower($value) )) $activate_discount = true;
						$conditions_exist = true;
						break;
					
					case "la": // discount by location (address)
						if (str_contains( mb_strtolower($order->address), mb_strtolower($value) )) $activate_discount = true;
						$conditions_exist = true;
						break;
						
					case "pp": // discount by payment method (id)
						if ($order->payment_method_id == $value) $activate_discount = true;
						$conditions_exist = true;
						break;
						
					case "d": // price to change by discount ( content_price or only delivery price)
						$price_to_discount = $value;
						break;
					
					default:
						break;
				}
			}
		}

		return array(
			"conditions" => $conditions_exist, 
			"activate" => $activate_discount, 
			"price" => $price_to_discount);
	}
	
	
	
	
	/**
	 * recalculate Payment for Orders
	 * @param type $order
	 */
	public function recalculateOrderPayment($order)
	{
        $payment = \Veer\Models\OrderPayment::find($order->payment_method_id);

		if(!is_object($payment)) return $order;
		
		// 1
		switch ($payment->paying_time) 
		{
			case "now":
				// TODO: redirect to payment system (but if admin then change to later)
				break;

			case "later":
				// TODO: create link to payment system and send it to user (save it somewhere)
				break;
		}
		
		// 2 calculator
		if (!empty($payment->func_name) && class_exists('\\Veer\\Ecommerce\\' . $payment->func_name)) 
		{
			$class = '\\Veer\\Ecommerce\\' . $payment->func_name;

			$paymentFunc = new $class;

			$getData = $paymentFunc->fire($order, $payment);

			$order->payment_done = isset($getData->payment_done) ? $getData->payment_done : false;
			$order->payment_hold = isset($getData->payment_hold) ? $getData->payment_hold : true; // TODO: do we need this?

			$payment->commission = isset($getData->commission) ? $getData->commission : $payment->commission;
			$payment->discount_enable = isset($getData->discount_enable) ? $getData->discount_enable : $payment->discount_enable;
			$payment->discount_price = isset($getData->discount_price) ? $getData->discount_price : $payment->discount_price;
			$payment->discount_conditions = isset($getData->discount_conditions) ? $getData->discount_conditions : $payment->discount_conditions;
		}
		
		// 3 
		if ($payment->commission > 0) 
		{
			$content = new \Veer\Models\OrderProduct;
			$content->orders_id = $order->id;
			$content->product = 0;
			$content->products_id = 0;
			$content->name = \Lang::get('veeradmin.order.content.payment.commission') . " (" . $payment->commission . "%)";
			$content->original_price = $order->content_price * ($payment->commission / 100);
			$content->quantity = 1;
			$content->attributes = "";
			$content->comments = \Lang::get('veeradmin.order.content.commission');
			$content->price_per_one = $content->original_price;
			$content->price = $content->original_price;
			$content->save();

			$order->content_price = $order->content_price + $content->price;
		}

		// 4
		if ($payment->discount_enable == 1 && $payment->discount_price > 0) 
		{
			$checkConditions = $this->checkDisountConditions($payment->discount_conditions, $order, "total");

			if (array_get($checkConditions, 'activate') == true || array_get($checkConditions, 'conditions') == false) 
			{
				if (array_get($checkConditions, 'price') == "total") 
				{
					$content = new \Veer\Models\OrderProduct;
					$content->orders_id = $order->id;
					$content->product = 0;
					$content->products_id = 0;
					$content->name = \Lang::get('veeradmin.order.content.payment.discount') ." (-" . $payment->discount_price . "%)";
					$content->original_price = 0 - ($order->content_price * ($payment->discount_price / 100));
					$content->quantity = 1;
					$content->attributes = "";
					$content->comments = \Lang::get('veeradmin.order.content.discount');
					$content->price_per_one = $content->original_price;
					$content->price = $content->original_price;
					$content->save();
					
					$order->content_price = $order->content_price + $content->price;
				}
				
				else
				{
					$order->delivery_price = $order->delivery_price * ( 1 - ( $payment->discount_price / 100));
				}
			}
		}
		
		return $order;
	}
	
	
	
	/**
	 * change User Discount Status - call when making of order is over
	 * @param type $discount
	 * @param type $status
	 * TODO: unused $status variable
	 */
	public function changeUserDiscountStatus($discount, $status = null)
	{
		$discount->expiration_times = $discount->expiration_times - 1;
		
		if($discount->expiration_times <= 0) $discount->expiration_times = -1;
		
		if($discount->expires == true && $discount->expiration_times <= 0) $discount->status = 'expired';
		
		if($discount->expires == true && \Carbon\Carbon::parse($discount->expiration_day)->timestamp > 0 
			&& \Carbon\Carbon::parse($discount->expiration_day)->timestamp < time())
		{
			$discount->status = 'expired';
		}
		
		$discount->save();
		
		$this->flushRememberedDiscounts();
	}	
		
	
	
	
	/**
	 * flush disounts & roles values in session
	 * 
	 */
	public function flushRememberedDiscounts()
	{
		\Session::forget('discounts');
		\Session::forget('discounts_checked');
		\Session::forget('roles_id');
		\Session::forget('discounts_by_role_checked');
		\Session::forget('discounts_by_role');
	}
}
