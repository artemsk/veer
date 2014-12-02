<?php namespace Veer\Lib;

use Carbon\Carbon;
use Veer\Models\UserDiscount;
use Veer\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

// TODO: test if everything would be slow?

class VeerShop {
	
	/**
	 * Price calculator:
	 * user_discounts > role > price_sales (promotions) > price
	 * 
	 * @params (array)$prices
	 * @return price
	 */
	
	public function __construct() {
		//
	}

	public static function getPrice($product)
	{
		$price = self::calculator($product);
		
		if($product['price'] != $price) {
			return View::make(app('veer')->loadedComponents['template'] . ".elements.price-discount")
				->with('price', self::priceFormat($price))
				->with('regular_price', self::priceFormat($product['price']));
		} else {
			return View::make(app('veer')->loadedComponents['template'] . ".elements.price-regular")->with('price', self::priceFormat($price));
		}
	}
	
	
	
	
	public static function priceFormat($price)
	{		
		if(round($price) == $price) {
			$price = number_format($price, 0);
		} else {
			$price = number_format($price, 2);
		}
		
		$currency_symbol = db_parameter('CURRENCY_SYMBOL', Config::get('veer.currency_symbol'));
		
		if(Config::get('veer.currency_symbol_place') == "before") {
			$price = $currency_symbol.$price;
		} else {
			$price = $price.$currency_symbol;
		}
		
		return $price;
	}
	
	
	
	
	public static function calculator($product)
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
			return self::currency($price, $product['currency']);
		}
		
		// 4
		// We check if existing user have discount
		$discounts = self::discounts($price);
		
		if($discounts['discount'] == true) {
			return self::currency($discounts['price'], $product['currency']);
		}
		
		// 5 
		// We check if existing user have discount by his role
		$discounts_by_role = self::discounts_by_role($product, $price);
		
		if($discounts_by_role['discount'] == true) {
			return self::currency($discounts_by_role['price'], $product['currency']);
		}		
		
		// if no discounts for user
		return self::currency($price, $product['currency']);				
	}
	
	
	
	
	public static function discounts($price) 
	{	
		$discount = false;
		
		$d = UserDiscount::where('sites_id','=',app('veer')->siteId)
			->where('users_id','=',Auth::id())
			->where('status','=','active')
			->whereNested(function($query) {
				$query->whereRaw(" ( expires = '1' and (expiration_day >= '" . date('Y-m-d H:i:00', strtotime(Carbon::now())) . 
					"' or expiration_times > '0') ) or ( expires = '2' ) ");
			})
			->orderBy('id')->select('id','discount')->remember(1)->first();
			
		if(count(@get_object_vars($d)) > 0) {
			$price = $price * ( 1 - ( $d->discount / 100));		
			$discount = true;
		}	
	 
		return array('discount' => $discount, 'price' => $price);		
	}
	
	
	
	
	public static function discounts_by_role($product, $price)
	{
		$discount = false;

		$d = UserRole::where('sites_id','=',app('veer')->siteId)
			->where('id','=',Auth::user()->roles_id)
			->whereNested(function($query) {
				$query->where('discount','>',0)
				->orWhere('price_field','!=','price');
			})
			->select('price_field','discount')->remember(1)->first();
			
		if(count(@get_object_vars($d)) > 0) {
			$price = $product[$d->price_field];
			if($d->discount > 0) { $price = $price * ( 1 - ( $d->discount / 100)); } 	 	
			$discount = true;
		}	
	 
		return array('discount' => $discount, 'price' => $price);			
	}
	
	
	
	
	/**
	 * Use shop or product currencies
	 * itemCurrency > shopCurrency > price
	 * @params
	 * @return void
	 */
	public static function currency($price, $itemCurrency)
	{		
		if($itemCurrency > 0 && $itemCurrency != 1) { return ($price * $itemCurrency);  }
		
		$shopCurrency = db_parameter('SHOP_CURRENCY', 1);
		
		if($shopCurrency > 0 && $shopCurrency != 1) {
			return ($price * $shopCurrency);
		}
		
		return $price;		
	}
	
}