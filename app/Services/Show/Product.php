<?php namespace Veer\Services\Show;

class Product {

	use \Veer\Services\Traits\CommonTraits, \Veer\Services\Traits\EntityTraits;
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Sorted Products: new, popular by orders or views
	 * - with: Images
	 * - to whom: ? | ?
	 */
	public function getProductLists($type, $siteId = null, $queryParams = array())
	{
		$items = \Veer\Models\Product::select();
		
		if(!empty($siteId)) $items->sitevalidation($siteId)->checked();
		
		$typeSorting = array(
			"new" => "created_at",
			"ordered" => "ordered",
			"viewed" => "viewed"
		);
		
		$items->with(array('images' => function($query) {
			$query->orderBy('id', 'asc')->take(1);
		}))->orderBy( array_get($typeSorting, $type, 'created_at'), 'desc');
		
		return $items->take(array_get($queryParams, 'take', 15))
			->skip(array_get($queryParams, 'skip', 0))
			->get();
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Product
	 * - with: Categories (?)
	 * - to whom: make() | product/{id}
	 */
	public function getProduct($id, $siteId = null)
	{
		$field = !is_numeric($id) ? "url" : "id";

		$product = \Veer\Models\Product::where($field, '=', $id);
		
		if(!empty($siteId)) { 
			$product = $product->checked()->sitevalidation($siteId)
			->with(array('categories' => function($query) use ($siteId) {
				$query->where('sites_id', '=', $siteId);
			})); 
		}
		
		return $product->first();
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages
	 * - with: Images
	 * - to whom: 1 Product | product/{id}
	 */
	public function withPages($siteId, $id, $queryParams)
	{
		return $this->getElementsWhereHasModel('pages', 'products', $id, $siteId, $queryParams);
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Sub Products
	 * - with: Images
	 * - to whom: 1 Product | product/{id}
	 */
	public function withChildProducts($siteId, $id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('products', array('parentproducts', 'parent'), $id, $siteId, $queryParams);
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Parent Products
	 * - with: Images
	 * - to whom: 1 Product | product/{id}
	 */
	public function withParentProducts($siteId, $id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('products', array('subproducts', 'child'), $id, $siteId, $queryParams);
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Categories
	 * - with: Images
	 * - to whom: 1 Product | product/{id}
	 */
	public function withCategories($siteId, $id)
	{
		return $this->getCategoriesWhereHasElements('products', $id, $siteId);		
	}
	
	
	/* get product advanced */
	public function getProductAdvanced($product, $options = array())
	{
		if($product == "new") return new \stdClass(); 

		$items = $this->getProduct($product);
			
		if(is_object($items)) 
		{
			$items->load(
				'subproducts', 'parentproducts', 'pages', 'categories', 
				'tags', 'attributes', 'downloads' );		

			$this->loadImagesWithElements($items, array_get($options, 'skipWith', false));
			
			$items['basket'] = $items->userlists()->where('name','=','[basket]')->count();
			
			$items['lists'] = $items->userlists()->where('name','!=','[basket]')->count();	
		}	
		
		return $items;
	}	
	
	/**
	 * Show Products
	 */
	public function getAllProducts($filters = array(), $paginateItems = 25) 
	{			
		$type = key($filters);
		
		$filter_id = head($filters);
				
		if(!empty($type) && !empty($filter_id)) $items = $this->filterProducts($type, $filter_id);
		
		if($type == "unused") $items = \Veer\Models\Product::has('categories','<',1);
		
		if(!isset($items)) $items = \Veer\Models\Product::select();
		
		$items = $items->orderBy('id','desc')->with('images', 'categories')->paginate($paginateItems); 
		
		if(!empty($type)) app('veer')->loadedComponents['filtered'] = $type; 
		
		else app('veer')->loadedComponents['counted'] = \Veer\Models\Product::count(); 
				
		if(!empty($filter_id)) app('veer')->loadedComponents['filtered_id'] = $this->replaceFilterId($type, $filter_id); 
		
		return $items;
	}	
	
	/* filter pages */
	public function filterProducts($type, $filter_id)
	{
		$type_field = $type;
		
		if($type == "site")
		{
			$type = "categories";
			$type_field = "sites";
		}
		
		return \Veer\Models\Product::whereHas($type, function($query) use ($filter_id, $type_field) 
		{
			$query->where( $type_field . '_id', '=', $filter_id );
		});
	}
			
	/* replace id with name if it's possible */
	protected function replaceFilterId($type, $filter_id)
	{
		if($type == "attributes")
		{				
			$a = \Veer\Models\Attribute::where('id','=',$filter_id)
				->select('name', 'val')->first();

			if(is_object($a)) 
			{
				$filter_id = $a->name.":".$a->val;
			}
		}

		if($type == "tags") $filter_id = \Veer\Models\Tag::where('id','=',$filter_id)->pluck('name');	
		
		return $filter_id;
	}
}
