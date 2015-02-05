<?php namespace Veer\Services\Show;

class Product {

	use \Veer\Services\Traits\CommonTraits, \Veer\Services\Traits\CommentTraits;
	
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
		
		if(!empty($siteId)) $product = $product->checked()->sitevalidation($siteId);
		
		return $product->with(array('categories' => function($query) use ($siteId) {
			$query->where('sites_id', '=', $siteId);
		}))->first();
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
	public function withCategories($siteId, $id, $queryParams = null)
	{
		return \Veer\Models\Category::whereHas('products', function($q) use($id) {
				$q->where('elements_id', '=', $id);
			})
			->where('sites_id', '=', $siteId)
			->with(array('images' => function($query) {
				$query->orderBy('id', 'asc')->take(1);
			}
			))->orderBy('created_at', 'desc')->get();			
	}
	
	
	
}
