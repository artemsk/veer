<?php namespace Veer;

use Carbon\Carbon;

// TODO: test if everything would be slow?
// TODO: empty current_user_role & current_user_discount* after logging out

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
		$regular_price = $this->currency($product['price'], $product['currency']);
		
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
	
	
	
	
	public function priceCurrencyFormat($price, $itemCurrency)
	{
		return $this->priceFormat($this->currency($price, $itemCurrency));
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
			return $this->currency($price, $product['currency']);
		}
		
		// 4
		// We check if existing user have discount
		$discounts = $this->discounts($price, $custom);
		
		if($discounts['discount'] == true) {
			return $this->currency($discounts['price'], $product['currency']);
		}
		
		// 5 
		// We check if existing user have discount by his role
		$discounts_by_role = $this->discounts_by_role($product, $price, $custom);
		
		if($discounts_by_role['discount'] == true) {
			return $this->currency($discounts_by_role['price'], $product['currency']);
		}		
		
		// if no discounts for user
		return $this->currency($price, $product['currency']);				
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
	 * @params $price, $itemCurrency
	 * @return $price
	 */
	public function currency($price, $itemCurrency)
	{		
		if($itemCurrency > 0 && $itemCurrency != 1) { return ($price * $itemCurrency);  }
		
		$shopCurrency = db_parameter('SHOP_CURRENCY', 1);
		
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
	
	
}