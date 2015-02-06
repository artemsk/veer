<?php namespace Veer\Services\Show;

class Filter {

	/**
	 * Query Builder: 
	 * 
	 * - who: Filtered Results for Many Products & Pages
	 * - with: Images
	 * - to whom: make() | filter/{id[0].id[1].id[2].id[3]}
	 * 
	 * TODO: FILTER_ATTRS key|value ?
	 */
	public function getFilter($siteId, $id, $queryParams = array())
	{
		$p = array('products' => array(), 'pages' => array());
		
		$id = explode('-', $id);

		$category_id = $id[0] ? $id[0] : 0;

		// If the value of first key of array is "null", we will change
		// condition operator from "=" to ">" (0) to include all categories in our
		// filter which exist for our site.               
		$c = "=";  if ($category_id <= 0) $c = ">";

		// Next we will walk through $id array to collect all filtered
		// model attributes; we will skip blank and 0 values, then we'll 
		// add collected data to our new array variable which we'll use twice - 
		// for products and for pages filtering.
		$a = $this->collectFilteredAttributes($id);

		// Queries for products model. First, we check if category is filtered,
		// then we're going through attributes filters. Then we collect
		// images, checking hidden and future products, and limits.
		$p['products'] = $this->collectEntities('\Veer\Models\Product', $category_id, $siteId, $c, $a, $queryParams);

		// Queries for pages model. First, we check if category is filtered,
		// then we're going through attributes filters. Then we collect
		// images, checking hidden, and limits.               
		$p['pages'] = $this->collectEntities('\Veer\Models\Page', $category_id, $siteId, $c, $a, $queryParams);

		//
		return $p;
	}
	
	/* collect attributes */
	protected function collectFilteredAttributes($id)
	{
		$a = array();
		
		if (count($id) > 1) 
		{
			foreach ($id as $k => $filter) 
			{
				if ($k <= 0 || $filter <= 0) continue; // skip category
					
				$a[$k] = Attribute::find($filter)->toArray();
			}
		}
		
		return $a;
	}
	
	protected function collectEntities($model, $category_id, $siteId, $condition, $attributes, $queryParams = array())
	{
		$entity = $model::whereHas('categories', function($q) use ($category_id, $siteId, $condition) {
				$q->where('categories_id', $condition, $category_id)->where('sites_id', '=', $siteId);
			});
	
		$this->collectWithAttributes($entity, $attributes);
	
		$entity->with(array('images' => function($query) { $query->orderBy('id', 'desc')->take(1); }));
	
		if($model == '\Veer\Models\Product') $entity->checked();
		
		else $entity->excludeHidden();
		
		return $entity->orderBy(array_get($queryParams, 'sort', 'created_at'), array_get($queryParams, 'direction', 'desc'))
			->take(array_get($queryParams, 'take', 25))
			->skip(array_get($queryParams, 'skip', 0))->get();
	}
	
	protected function collectWithAttributes($entity, $attributes)
	{
		return $entity->where(function($q) use ($attributes) {
				if (count($attributes) > 0) {
					foreach ($attributes as $filter) {
						$q->whereHas('attributes', function($query) use ($filter) {
							$query->where('name', '=', $filter['name'])->where('val', '=', $filter['val']);
						});
					}
				}
		});
	}
	
	protected function withModels($model, $products, $pages)
	{
		$items = $model::select();
		
		if(count($products) > 0) {
			$items->whereHas('products', function($query) use($products) {
				$query->checked()->whereIn('products.id', $products->modelKeys());
			});
		}
		
		if(count($pages) > 0) {
			$items->orWhereHas('pages', function($query) use($pages) {
				$query->excludeHidden()->whereIn('pages.id', $pages->modelKeys());
			});
		}
			
		return app('veer')->cachingQueries->makeAndRemember($items, 'get', 5, null, 
			md5($this->getFilterCacheName($model, $products, $pages)));
	}
	
	public function withTags($products, $pages)
	{
		return $this->withModels('\Veer\Models\Tag', $products, $pages);
	}
	
	public function withAttributes($products, $pages)
	{
		return $this->withModels('\Veer\Models\Attribute', $products, $pages);
	}
	
	public function withCategories($products, $pages)
	{
		return $this->withModels('\Veer\Models\Category', $products, $pages);
	}	
	
	protected function getFilterCacheName($model, $products, $pages)
	{
		return ($model."|".implode("|", $products->modelKeys())."|".implode("|", $pages->modelKeys()));
	}
	
}
