<?php namespace Veer\Lib;

use Carbon\Carbon;
use Veer\Models\UserDiscount;
use Veer\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

// TODO: test if everything would be slow?
// TODO: empty current_user_role & current_user_discount* after logging out

class VeerShop {
	
	protected $currency_symbol;
	
	protected $current_user_role;
	
	protected $current_user_discount;
	
	protected $current_user_discount_by_role;
	
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
	}

	
	
	
	public function getPrice($product)
	{
		$price = $this->calculator($product);
		
		if($product['price'] != $price) {
			return View::make(app('veer')->loadedComponents['template'] . ".elements.price-discount")
				->with('price', $this->priceFormat($price))
				->with('regular_price', $this->priceFormat($product['price']));
		} else {
			return View::make(app('veer')->loadedComponents['template'] . ".elements.price-regular")->with('price', $this->priceFormat($price));
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
	
	
	
	
	public function calculator($product)
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
		if(Auth::id() <= 0) { 
			return $this->currency($price, $product['currency']);
		}
		
		// 4
		// We check if existing user have discount
		$discounts = $this->discounts($price);
		
		if($discounts['discount'] == true) {
			return $this->currency($discounts['price'], $product['currency']);
		}
		
		// 5 
		// We check if existing user have discount by his role
		$discounts_by_role = $this->discounts_by_role($product, $price);
		
		if($discounts_by_role['discount'] == true) {
			return $this->currency($discounts_by_role['price'], $product['currency']);
		}		
		
		// if no discounts for user
		return $this->currency($price, $product['currency']);				
	}
	
	
	
	
	public function discounts($price) 
	{	
		$discount = false;
		
		if(empty($this->current_user_discount)) {		
			$this->current_user_discount = UserDiscount::where('sites_id','=',app('veer')->siteId)
			->where('users_id','=',Auth::id())
			->where('status','=','active')
			->whereNested(function($query) {
				$query->whereRaw(" ( expires = '1' and (expiration_day >= '" . date('Y-m-d H:i:00', strtotime(Carbon::now())) . 
					"' or expiration_times > '0') ) or ( expires = '0' ) ");
			})
			->orderBy('id')->select('discount')->remember(2)->first();			
		} 
		
		if(count(@get_object_vars($this->current_user_discount)) > 0) {
			$price = $price * ( 1 - ( $this->current_user_discount->discount / 100));		
			$discount = true;
		}	
	 
		return array('discount' => $discount, 'price' => $price);		
	}
	
	
	
	
	public function discounts_by_role($product, $price)
	{
		$discount = false;

		$this->current_user_role = empty($this->current_user_role) ? Auth::user()->roles_id : $this->current_user_role;
		
		if(empty($this->current_user_discount_by_role)) {	
		$this->current_user_discount_by_role = UserRole::where('sites_id','=',app('veer')->siteId)
			->where('id','=',$this->current_user_role)
			->whereNested(function($query) {
				$query->where('discount','>',0)
				->orWhere('price_field','!=','price');
			})
			->select('price_field','discount')->remember(1)->first();
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
	
}