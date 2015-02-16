<?php namespace Veer\Services\Traits;

trait CommonTraits {
	
	/*
	 * Load Images with Elements
	 */
	public function loadImagesWithElements($items, $skipWith = false)
	{
		return $skipWith === false ? $items->load(array('images' => function($q)
			{
				$q->with('pages', 'products', 'categories', 'users');
			})) 
				: $items->load('images');
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Products | Pages
	 * - with: Images
	 * - to whom: 1 Attribute, category
	 */
	public function getElementsWhereHasModel($type, $table, $id, $siteId = null, $queryParams = null)
	{
		if($type == "products") $model = '\Veer\Models\Product';
		else $model = '\Veer\Models\Page';
		
		$table_field = $table;
		
		if(is_array($table)) list($table, $table_field) = $table;
		
		$items = $model::whereHas($table, function($q) use($id, $table_field) {
					$q->where($table_field.'_id', '=', $id);
				});
				
		if($table != "images") {
			$items = $items->with(array('images' => function($query) {
					$query->orderBy('id', 'asc');
			}));
		}
		
		if(!empty($siteId)) $items = $items->sitevalidation($siteId);
		
		if($type == "products") { 
			$items = $items->checked()->orderBy( array_get($queryParams, 'sort', 'created_at'), 
				array_get($queryParams, 'direction', 'desc'))
				->take(array_get($queryParams, 'take', 25))->skip(array_get($queryParams, 'skip', 0));
		}
		
		if($type == "pages") { 
			$items = $items->excludeHidden()->orderBy( array_get($queryParams, 'sort_pages', 'created_at'), 
				array_get($queryParams, 'direction_pages', 'desc'))
				->take(array_get($queryParams, 'take_pages', 25))->skip(array_get($queryParams, 'skip_pages', 0));
		}
		
		return $items->get();		
	}		

	/* with models */
	public function withModels($model, $table, $id, $siteId = null)
	{
		app('veer')->cachingQueries->make(
			$model::whereHas('products', function($query) use($id, $table, $siteId) 
			{
				if(!empty($siteId)) $query->siteValidation($siteId);
				$query->checked()->whereHas($table, function($q) use ($id, $table) {
							$q->where($table.'_id','=',$id);
					});
			})->orWhereHas('pages', function($query) use($id, $table, $siteId) 
			{
				 if(!empty($siteId)) $query->siteValidation($siteId);
				 $query->excludeHidden()->whereHas($table, function($q) use ($id, $table) {
							$q->where($table.'_id','=',$id);
					});
			}));
			
		return app('veer')->cachingQueries->remember(5, 'get');
	}
	
}
