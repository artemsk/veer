<?php namespace Veer\Lib;

use Veer\Models\Category;
use Veer\Models\Tag;
use Veer\Models\Attribute;
use Veer\Models\Image;  
use Veer\Models\Product; //a
use Veer\Models\Page;    //a
use Veer\Models\Order;   //b
use Veer\Models\User;    //b

/**
 * 
 * Veer @VeerDb !essential
 * 
 * - collect product & pages for category/tag/attribute/search etc. 
 *   is used everywhere: 
 * 
 *   pages & products
 *   no-conditions [ tags, attributes, comments*, downloads, searches 
 *                   [[ users_**, communications* ]], [[ orders_** ]] ]  
 *  
 *   conditions [ products|sub|parent ~ categories|sites|hide|to_show ] {pages_products} [1]
 *              [ pages|sub|parent ~ categories|sites|hidden ] {pages_products} [2]
 *              [ categories|sub|parent ~ sites ] [3]
 *             *[ orders ~ users|sites|hidden ]
 *         auth*[ users ~ sites|banned ] [ users_admin ~ users|banned ]
 *             *[ users_discounts ~ users|sites|status ]
 *              [ sites ~ ]
 * 
 *   no-conditions special [ images ]
 * 
 *   methods+: search, filter, [sort]new, [sort]ordered, [sort]viewed, [array]products
 * 
 *   not-used-here [ cache, components, configuration, failed_jobs, migrations, password_reminders ] 
 * 
 * @return object
 */
class VeerDb {

	public $data;

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

		return $this->$functionName($siteId, $id, $queryParams);
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
	 * - who: 1 Category 
	 * - with: Parent & Sub Categories
	 * - to whom: make() | category/{id}
	 */
	public function categoryShowQuery($siteId, $id, $queryParams = array())
	{
		return Category::where('sites_id', '=', $siteId)->where('id', '=', $id)->
				with(array(
					'subcategories' => function($query) use ($siteId) {

					$query->where('sites_id', '=', $siteId);
				},
					'parentcategories' => function($query) use ($siteId) {

					$query->where('sites_id', '=', $siteId);
				}
				))->first();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Products
	 * - with: Images
	 * - to whom: 1 Category | category/{id}
	 */
	public function categoryOnlyProductsQuery($id, $queryParams)
	{
		return Product::whereHas('categories', function($q) use($id) {

					$q->where('categories_id', '=', $id);
				})->with(array('images' => function($query) {

					$query->orderBy('id', 'asc')->take(1);
				}))->checked()
				->orderBy($queryParams['sort'], $queryParams['direction'])
				->take($queryParams['take'])
				->skip($queryParams['skip'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages 
	 * - with: Images
	 * - to whom: 1 Category | category/{id}
	 */
	public function categoryOnlyPagesQuery($id, $queryParams)
	{
		return Page::whereHas('categories', function($q) use($id) {

					$q->where('categories_id', '=', $id);
				})->with(array('images' => function($query) {

					$query->orderBy('id', 'asc')->take(1);
				}))->excludeHidden()
				->orderBy('created_at', 'desc')
				->take($queryParams['take_pages'])
				->skip($queryParams['skip_pages'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Top Level Categories 
	 * - with: Images
	 * - to whom: make() | category/{blank}
	 */
	public function categoryIndexQuery($siteId, $id = null, $queryParams = array())
	{
		return Category::where('sites_id', '=', $siteId)->has('parentcategories', '<', 1)
				->with(array('images' => function($query) {
					$query->orderBy('id', 'desc')->take(1);
				}))->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Tag
	 * - with: 
	 * - to whom: make() | tag/{id}
	 */
	public function tagShowQuery($siteId, $id, $queryParams)
	{
		return Tag::where('id', '=', $id)->first();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Products 
	 * - with: Images
	 * - to whom: 1 Tag | tag/{id}
	 */
	public function tagOnlyProductsQuery($siteId, $id, $queryParams)
	{
		return Product::whereHas('tags', function($q) use($id) {
					$q->where('tags_id', '=', $id);
				})->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->sitevalidation($siteId)
				->checked()
				->orderBy($queryParams['sort'], $queryParams['direction'])
				->take($queryParams['take'])
				->skip($queryParams['skip'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages 
	 * - with: Images
	 * - to whom: 1 Tag | tag/{id}
	 */
	public function tagOnlyPagesQuery($siteId, $id, $queryParams)
	{
		return Page::whereHas('tags', function($q) use($id) {
					$q->where('tags_id', '=', $id);
				})
				->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->sitevalidation($siteId)
				->excludeHidden()
				->orderBy('created_at', 'desc')
				->take($queryParams['take_pages'])
				->skip($queryParams['skip_pages'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Tags 
	 * - with: 
	 * - to whom: make() | tag/{blank}
	 *
	 * We should include the same statement twice as "OR" operator always 
	 * has priority over "AND". Methods checked() & excludehidden() ignored 
	 * for some perfomance issues
	 */
	public function tagIndexQuery($siteId, $id = null, $queryParams = array())
	{
		return Tag::withTrashed()->whereHas('products', function($q) use($siteId) {
					$q->sitevalidation($siteId);
				})
				->whereRaw('`tags`.`deleted_at` is null')
				->orWhereHas('pages', function($q) use($siteId) {
					$q->sitevalidation($siteId);
				}
				)->whereRaw('`tags`.`deleted_at` is null')->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Attribute (Name or Value) 
	 * - with: 
	 * - to whom: make() | attribute/{id[0].id[1]}
	 */
	public function attributeShowQuery($siteId, $id, $queryParams)
	{
		$p = null;

		$id = explode('.', $id);

		if (!isset($id[1]) || @$id[1] == "") {

			$parent_attribute = Attribute::where('id', '=', $id[0])->select('name')->first();
			
			if(is_object($parent_attribute)) {
				$p = Attribute::where('name', 'like', $parent_attribute->name)->get();
			}
			
			$this->data['parent_flag'] = 1;
		} else {

			$p = Attribute::where('id', '=', $id[1])->first();
			$this->data['parent_flag'] = 0;
		}

		return $p;
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Products
	 * - with: Images
	 * - to whom: 1 Attribute | attribute/{id[0].id[1]}
	 */
	public function attributeOnlyProductsQuery($siteId, $id, $queryParams)
	{
		$id = explode('.', $id);

		$id = $id[1];

		return Product::whereHas('attributes', function($q) use($id) {
					$q->where('attributes_id', '=', $id);
				})
				->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->sitevalidation($siteId)
				->checked()
				->orderBy($queryParams['sort'], $queryParams['direction'])
				->take($queryParams['take'])
				->skip($queryParams['skip'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages
	 * - with: Images
	 * - to whom: 1 Attribute | attribute/{id[0].id[1]}
	 */
	public function attributeOnlyPagesQuery($siteId, $id, $queryParams)
	{
		$id = explode('.', $id);

		$id = $id[1];

		return Page::whereHas('attributes', function($q) use($id) {
					$q->where('attributes_id', '=', $id);
				})
				->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->sitevalidation($siteId)
				->excludeHidden()
				->orderBy('created_at', 'desc')
				->take($queryParams['take_pages'])
				->skip($queryParams['skip_pages'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Attributes
	 * - with: 
	 * - to whom: make() | attribute/{blank}
	 *
	 * We should include the same statement twice as "OR" operator always has 
	 * priority over "AND". Methods checked() & excludehidden() ignored. Cached for 5 minutes.
	 */
	public function attributeIndexQuery($siteId, $id = null, $queryParams = array())
	{		
		return Attribute::withTrashed()->whereHas('products', function($q) use($siteId) {
					$q->sitevalidation($siteId);
				})
				->whereRaw('`attributes`.`deleted_at` is null')
				->orWhereHas('pages', function($q) use($siteId) {
					$q->sitevalidation($siteId);
				})
				->whereRaw('`attributes`.`deleted_at` is null')
				->select('name', 'id')
				->groupBy('name')
				->remember(5)
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Image
	 * - with: 
	 * - to whom: make() | image/{id}
	 *
	 * We should include the same statement three times as "OR" operator always has 
	 * priority over "AND". Methods checked() & excludehidden() ignored. Cached for 5 minutes.
	 */
	public function imageShowQuery($siteId, $id, $queryParams = null)
	{
		return Image::where('id', '=', $id)->withTrashed()->whereHas('products', function($q) use($siteId) {
					$q->sitevalidation($siteId);
				})
				->whereRaw('`images`.`deleted_at` is null')
				->orWhereHas('pages', function($q) use($siteId) {
					$q->sitevalidation($siteId);
				})
				->whereRaw('`images`.`deleted_at` is null')
				->where('id', '=', $id)
				->orWhereHas('categories', function($q) use($siteId) {
					$q->where('sites_id', '=', $siteId);
				})
				->whereRaw('`images`.`deleted_at` is null')
				->where('id', '=', $id)
				->first();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Products
	 * - with: 
	 * - to whom: 1 Image | image/{id}
	 */
	public function imageOnlyProductsQuery($siteId, $id, $queryParams)
	{
		return Product::whereHas('images', function($q) use($id) {
					$q->where('images_id', '=', $id);
				}
				)->sitevalidation($siteId)
				->checked()
				->orderBy($queryParams['sort'], $queryParams['direction'])
				->take($queryParams['take'])
				->skip($queryParams['skip'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages
	 * - with: 
	 * - to whom: 1 Image | image/{id}
	 */
	public function imageOnlyPagesQuery($siteId, $id, $queryParams)
	{
		return Page::whereHas('images', function($q) use($id) {
					$q->where('images_id', '=', $id);
				})
				->sitevalidation($siteId)
				->excludeHidden()
				->orderBy('created_at', 'desc')
				->take($queryParams['take_pages'])
				->skip($queryParams['skip_pages'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Categories
	 * - with: 
	 * - to whom: 1 Image | image/{id}
	 */
	public function imageOnlyCategoriesQuery($siteId, $id, $queryParams = null)
	{
		return Category::whereHas('images', function($q) use($id) {
					$q->where('images_id', '=', $id);
				})
				->where('sites_id', '=', $siteId)
				->orderBy('created_at', 'desc')
				->get();
	}

	/**
	 * Query Builder: Index/Home Page
	 */
	public function indexQuery($siteId, $id, $queryParams)
	{
		// 
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Product
	 * - with: Categories (?)
	 * - to whom: make() | product/{id}
	 */
	public function productShowQuery($siteId, $id, $queryParams = null)
	{
		if (is_numeric($id)) {
			$field = "id";
		} else {
			$field = "url";
		}

		return Product::sitevalidation($siteId)
				->where($field, '=', $id)
				->with(array('categories' => function($query) use ($siteId) {
					$query->where('sites_id', '=', $siteId);
				}
				))->checked()->first(); // @testing оставить как есть?
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages
	 * - with: Images
	 * - to whom: 1 Product | product/{id}
	 */
	public function productOnlyPagesQuery($siteId, $id, $queryParams)
	{
		return Page::whereHas('products', function($q) use($id) {
					$q->where('products_id', '=', $id);
				})
				->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->sitevalidation($siteId)
				->excludeHidden()
				->orderBy('created_at', 'desc')
				->take($queryParams['take_pages'])
				->skip($queryParams['skip_pages'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Sub Products
	 * - with: Images
	 * - to whom: 1 Product | product/{id}
	 */
	public function productOnlySubProductsQuery($siteId, $id, $queryParams = null)
	{
		return Product::whereHas('parentproducts', function($q) use($id) {
					$q->where('parent_id', '=', $id);
				}
				)->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->sitevalidation($siteId)
				->checked()
				->orderBy($queryParams['sort'], $queryParams['direction'])
				->take($queryParams['take'])
				->skip($queryParams['skip'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Parent Products
	 * - with: Images
	 * - to whom: 1 Product | product/{id}
	 */
	public function productOnlyParentProductsQuery($siteId, $id, $queryParams = null)
	{
		return Product::whereHas('subproducts', function($q) use($id) {
					$q->where('child_id', '=', $id);
				}
				)->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->sitevalidation($siteId)
				->checked()
				->orderBy($queryParams['sort'], $queryParams['direction'])
				->take($queryParams['take'])
				->skip($queryParams['skip'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Categories
	 * - with: Images
	 * - to whom: 1 Product | product/{id}
	 */
	public function productOnlyCategoriesQuery($siteId, $id, $queryParams = null)
	{
		return Category::whereHas('products', function($q) use($id) {
					$q->where('elements_id', '=', $id);
				})
				->where('sites_id', '=', $siteId)->with(array('images' => function($query) {
				$query->orderBy('id', 'asc')->take(1);
			}
			))->orderBy('created_at', 'desc')->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Page
	 * - with: 
	 * - to whom: make() | page/{id}
	 */
	public function pageShowQuery($siteId, $id, $queryParams = null)
	{
		if (is_numeric($id)) {
			$field = "id";
		} else {
			$field = "url";
		}

		return Page::sitevalidation($siteId)
				->where($field, '=', $id)
				->excludeHidden()
				->with(array(//'categories' => function($query) use ($siteId) 
					//{
					//      $query->where('sites_id','=',$siteId);
					//},
				))->first(); // @testing we have function for that (?)
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Products
	 * - with: Images
	 * - to whom: 1 Page | page/{id}
	 */
	public function pageOnlyProductsQuery($siteId, $id, $queryParams)
	{
		return Product::whereHas('pages', function($q) use($id) {
					$q->where('pages_id', '=', $id);
				})
				->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->sitevalidation($siteId)
				->checked()
				->orderBy($queryParams['sort'], $queryParams['direction'])
				->take($queryParams['take'])
				->skip($queryParams['skip'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Sub Pages
	 * - with: Images
	 * - to whom: 1 Page | page/{id}
	 */
	public function pageOnlySubPagesQuery($siteId, $id, $queryParams = null)
	{
		return Page::whereHas('parentpages', function($q) use($id) {
					$q->where('parent_id', '=', $id);
				})
				->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->sitevalidation($siteId)
				->excludeHidden()
				->orderBy('created_at', 'descr')
				->take($queryParams['take_pages'])
				->skip($queryParams['skip_pages'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Parent Pages
	 * - with: Images
	 * - to whom: 1 Page | page/{id}
	 */
	public function pageOnlyParentPagesQuery($siteId, $id, $queryParams = null)
	{
		return Page::whereHas('subpages', function($q) use($id) {
					$q->where('child_id', '=', $id);
				})->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->sitevalidation($siteId)
				->excludeHidden()
				->orderBy('created_at', 'descr')
				->take($queryParams['take_pages'])
				->skip($queryParams['skip_pages'])
				->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Categories
	 * - with: Images
	 * - to whom: 1 Page | page/{id}
	 */
	public function pageOnlyCategoriesQuery($siteId, $id, $queryParams = null)
	{
		return Category::whereHas('pages', function($q) use($id) {
					$q->where('elements_id', '=', $id);
				})
				->where('sites_id', '=', $siteId)
				->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->orderBy('created_at', 'desc')->get();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages
	 * - with: Images
	 * - to whom: make() | page/{blank}
	 */
	public function pageIndexQuery($siteId, $id = null, $queryParams = array())
	{
		return Page::whereHas('categories', function($q) use($siteId) {
					$q->where('sites_id', '=', $siteId);
				})->excludeHidden()
				->orderBy($queryParams['sort'], $queryParams['direction'])
				->take($queryParams['take_pages'])
				->skip($queryParams['skip_pages'])
				->with(array('images' => function($query) {
					$query->orderBy('id', 'desc')->take(1);
				}
				), 'categories')->get();
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
	 * - to whom: add2cart(), user.login | user/basket/add
	 */
	public function userShoppingCart($siteId, $userid) {
		return \Veer\Models\UserList::where('sites_id','=', $siteId)
					->where(function($query) use ($userid) {
						if($userid > 0) {
						$query->where('users_id','=', empty($userid) ? 0 : $userid)
							->orWhere('session_id','=', app('session')->getId());	
						} else {
						$query->where('users_id','=', empty($userid) ? 0 : $userid)
							->where('session_id','=', app('session')->getId());							
						}
					})->where('name','=','[basket]')
					->where('elements_type','=','Veer\Models\Product')
					->sum('quantity');
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

	/**
	 * Query Builder: 
	 * 
	 * - who: Sorted Products: new, popular by orders or views
	 * - with: Images
	 * - to whom: ? | ?
	 */
	public function sortingProductsQuery($siteId, $id, $queryParams)
	{
		$p = null;

		if ($id == 'new') {
			$p = Product::sitevalidation($siteId)
				->checked()
				->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}))
				->orderBy('created_at', 'desc')
				->take($queryParams['take'])
				->skip($queryParams['skip'])
				->get();
		}

		if ($id == 'ordered') {
			$p = Product::sitevalidation($siteId)
				->checked()
				->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}))
				->orderBy('ordered', 'desc')
				->take($queryParams['take'])
				->skip($queryParams['skip'])
				->get();
		}

		if ($id == 'viewed') {
			$p = Product::sitevalidation($siteId)
				->checked()
				->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}))
				->orderBy('viewed', 'desc')
				->take($queryParams['take'])
				->skip($queryParams['skip'])
				->get();
		}

		return $p;
	}

	/**
	 * @parseFilterStr - parse configuration[FILTER_ATTRS]
	 * 
	 * @params $parseStr  key|value\n
	 * @return $preloaded
	 */
	public function parseFilterStr($parseStr)
	{
		if ($parseStr != "") {
			$preloaded = explode('\n', $parseStr);
			foreach ($preloaded as $v) {
				$filterPair = explode('|', $v);
				$filterPair[2] = Attribute::where('name', '=', trim($filterPair[0]))->select('id')->first();
			}
			return $filterPair;
		}
	}

}
