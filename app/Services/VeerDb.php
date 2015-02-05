<?php namespace Veer\Services;

use Veer\Models\Category;
use Veer\Models\Tag;
use Veer\Models\Attribute;
use Veer\Models\Image;  
use Veer\Models\Product; //a
use Veer\Models\Page;    //a
use Veer\Models\Order;   //b
use Veer\Models\User;    //b

class VeerDb {

	public $data;
	
	public $show;

	function __construct($method = null, $id = null, $params = null)
	{	
		if ($method != null) {

			$this->data = $this->make($method, $id, $params);
			
			return $this->data;
		}
	}

	/**
	 * make
	 * 
	 */
	public function make($method, $id = null, $params = null)
	{
		$siteId = app('veer')->siteId;

		$queryParams = $this->paramsDefault($params);

		$functionName = camel_case(strtr($method, array("." => "_"))) . "Query";

		return $this->{$functionName}($siteId, $id, $queryParams);
	}

	/**
	 * route
	 *
	 * @params $id, $params
	 * @return 
	 */
	public function route($id = null, $params = null)
	{
		return $this->make(\Illuminate\Support\Facades\Route::currentRouteName(), $id, $params);
	}
	
	/**
	 * Default Params
	 * 
	 */
	protected function paramsDefault($params = array())
	{
		$defaultParams = array(
			"sort" => "updated_at",
			"direction" => "desc",
			"skip" => 0,
			"take" => 999,
			"skip_pages" => 0,
			"take_pages" => 15,
			"search_field_product" => "title",
			"search_field_page" => "title"
		);

		foreach ($defaultParams as $k => $v) {
			if (!isset($params[$k])) {
				$params[$k] = $v;
			}
		}

		return $params;
	}



	

	

	/**
	 * Query Builder: 
	 * 
	 * - who: Search Results for Many Products & Pages
	 * - with: Images
	 * - to whom: make() | search/{id} or $_POST
	 */
	public function searchStoreQuery($siteId, $id, $queryParams)
	{
		$p = null;

		$q = explode(' ', $queryParams['q']);

		$field = $queryParams['search_field_product'];

		$p['products'] = Product::siteValidation($siteId)
			->whereNested(function($query) use ($q, $field) {
				foreach ($q as $word) {
					$query->where(function($queryNested) use ($word, $field) {
						$queryNested->where($field, '=', $word)
						->orWhere($field, 'like', $word . '%%%')
						->orWhere($field, 'like', '%%%' . $word)
						->orWhere($field, 'like', '%%%' . $word . '%%%');
					});
				}
			})->with(array('images' => function($query) {
				$query->orderBy('id', 'desc')->take(1);
			}
			))->checked()
			->orderBy($queryParams['sort'], $queryParams['direction'])
			->take($queryParams['take'])
			->skip($queryParams['skip'])
			->get();

		$field = $queryParams['search_field_page'];

		$p['pages'] = Page::siteValidation($siteId)
			->whereNested(function($query) use ($q, $field) {
				foreach ($q as $word) {
					$query->where(function($queryNested) use ($word, $field) {
						$queryNested->where($field, '=', $word)
						->orWhere($field, 'like', $word . '%%%')
						->orWhere($field, 'like', '%%%' . $word)
						->orWhere($field, 'like', '%%%' . $word . '%%%');
					});
				}
			})->with(array('images' => function($query) {
				$query->orderBy('id', 'desc')->take(1);
			}
			))->excludeHidden()
			->orderBy('created_at', 'desc')
			->take($queryParams['take_pages'])
			->skip($queryParams['skip_pages'])
			->get();
		return $p;
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Filtered Results for Many Products & Pages
	 * - with: Images
	 * - to whom: make() | filter/{id[0].id[1].id[2].id[3]}
	 * 
	 * @params: FILTER_ATTRS key|value 
	 */
	public function filterShowQuery($siteId, $id, $queryParams)
	{
		$p = null;

		$id = explode('.', $id);

		$category_id = $id[0] ? $id[0] : 0;

		// If the value of first key of array is "null", we will change
		// condition operator from "=" to ">" (0) to include all categories in our
		// filter which exist for our site.               
		$c = "=";
		if ($category_id <= 0) {
			$c = ">";
		}

		$a = array();

		// Next we will walk through $id array to collect all filtered
		// model attributes; we will skip blank and 0 values, then we'll 
		// add collected data to our new array variable which we'll use twice - 
		// for products and for pages filtering.
		if (count($id) > 1) {
			foreach ($id as $k => $filter) {
				if ($k <= 0 || $filter <= 0) {
					continue;
				} // skip category

				$a[$k] = Attribute::find($filter)->toArray();
			}
		}

		// Queries for products model. First, we check if category is filtered,
		// then we're going through attributes filters. Then we collect
		// images, checking hidden and future products, and limits.
		$p['products'] = Product::whereHas('categories', function($q) use ($category_id, $siteId, $c) {
				$q->where('categories_id', $c, $category_id)->where('sites_id', '=', $siteId);
			})->where(function($q) use ($a) {
				if (count($a) > 0) {
					foreach ($a as $filter) {
						$q->whereHas('attributes', function($query) use ($filter) {
							$query->where('name', '=', $filter['name'])->where('val', '=', $filter['val']);
						});
					}
				}
			})->with(array('images' => function($query) {
				$query->orderBy('id', 'desc')->take(1);
			}))->checked()
			->orderBy($queryParams['sort'], $queryParams['direction'])
			->take($queryParams['take'])
			->skip($queryParams['skip'])
			->get();

		// Queries for pages model. First, we check if category is filtered,
		// then we're going through attributes filters. Then we collect
		// images, checking hidden, and limits.               
		$p['pages'] = Page::whereHas('categories', function($q) use ($category_id, $siteId, $c) {
				$q->where('categories_id', $c, $category_id)->where('sites_id', '=', $siteId);
			})->where(function($q) use ($a) {
				if (count($a) > 0) {
					foreach ($a as $filter) {
						$q->whereHas('attributes', function($query) use ($filter) {
							$query->where('name', '=', $filter['name'])->where('val', '=', $filter['val']);
						});
					}
				}
			})->with(array('images' => function($query) {
				$query->orderBy('id', 'desc')->take(1);
			}
			))->excludeHidden()
			->orderBy('created_at', 'desc')
			->take($queryParams['take_pages'])
			->skip($queryParams['skip_pages'])
			->get();

		//
		return $p;
	}

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
	
	/**
	 * Query Builder: 
	 * 
	 * - who: 1 User
	 * - with: 
	 * - to whom: make() | user/{id}
	 * 
	 * @later: 'role', 'comments', 'books', 'discounts', 'userlists', 'orders', 'bills', 
	 * 'communications', 'administrator', 'searches', 'pages'*
	 */
	protected function userIndexQuery($siteId, $id, $queryParams)
	{
		return User::where('id', '=', $id)->where('sites_id', '=', $siteId)
				->where('banned', '!=', '1')->first();
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Items Quantity
	 * - with: 
	 * - to whom: add2cart(), user.login | user/cart/add
	 */
	public function userLists($siteId, $userid, $name = "[basket]", $onlySum = true) 
	{
		$items = \Veer\Models\UserList::where('sites_id','=', $siteId)
					->where(function($query) use ($userid) {
						if($userid > 0) {
						$query->where('users_id','=', empty($userid) ? 0 : $userid)
							->orWhere('session_id','=', app('session')->getId());	
						} else {
						$query->where('users_id','=', empty($userid) ? 0 : $userid)
							->where('session_id','=', app('session')->getId());							
						}
					})->where('name','=', $name);
					
		if($name == "[basket]")	$items->where('elements_type','=','Veer\Models\Product');
		
		return ($onlySum == true) ? $items->sum('quantity') : $items;
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Cart Entities
	 * - with: 
	 * - to whom: user/cart/
	 */
	public function userCartShowQuery($siteId, $userid)
	{
		return $this->userLists($siteId, $userid, "[basket]", false)->get();
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: List of Products in/across Sites 
	 * - with: Images
	 * - to whom: ? | ?
	 */
	public function connectedQuery($siteId, $id, $queryParams)
	{
		$p = null;

		if ($queryParams['connectedQuery'] == 'connected') {
			$p = Product::sitevalidation($siteId)
					->whereIn('id', $id)
					->with(array('images' => function($query) {
						$query->orderBy('id', 'asc')->take(1);
					}))
					->checked()->get();
		}

		if ($queryParams['connectedQuery'] == 'connectedEverywhere') {
			$p = Product::whereIn('id', $id)
					->with(array('images' => function($query) {
						$query->orderBy('id', 'asc')->take(1);
					}))
					->checked()->get();
		}

		return $p;
	}

}
