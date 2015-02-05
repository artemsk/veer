<?php namespace Veer\Services\Show;

class Category {
	
	use CommonTraits;
	
	/**
	 * handle
	 */
	public function handle($siteId = null, $paginateItems = 100)
	{
		return !empty($siteId) ? 
			  $this->getTopCategoriesWithSite($siteId) 
			: $this->getCategoriesWithoutSite($paginateItems);
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Top Level Categories 
	 * - with: Images
	 * - to whom: make() | category/{blank}
	 */
	public function getTopCategoriesWithSite($siteId)
	{
		return \Veer\Models\Category::where('sites_id', '=', $siteId)->has('parentcategories', '<', 1)
				->with(array('images' => function($query) {
					$query->orderBy('id', 'desc')->take(1);
				}))->get();
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Category 
	 * - with: Parent & Sub Categories
	 * - to whom: make() | category/{id}
	 */
	public function getCategory($id, $siteId = null)
	{
		if(!empty($siteId))
		{
			return \Veer\Models\Category::where('sites_id', '=', $siteId)->where('id', '=', $id)->
				with(array(
					'subcategories' => function($query) use ($siteId) {

					$query->where('sites_id', '=', $siteId);
				},
					'parentcategories' => function($query) use ($siteId) 
				{
					$query->where('sites_id', '=', $siteId);
				}
				))->first(); 
		}
		
		return \Veer\Models\Category::where('id','=',$id)->with(array(
			'parentcategories' => function ($query) 
			{ 
				$query->orderBy('manual_sort','asc'); 
			},
			'subcategories' => function ($query) 
			{ 
				$query->orderBy('manual_sort','asc')
					->with('pages', 'products', 'subcategories'); 
			}))
				->first();
	}
	
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Products
	 * - with: Images
	 * - to whom: 1 Category | category/{id}
	 */
	public function withProducts($id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('products', 'categories', $id, null, $queryParams);
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages 
	 * - with: Images
	 * - to whom: 1 Category | category/{id}
	 */
	public function withPages($id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('pages', 'categories', $id, null, $queryParams);
	}
	
	public function getModelWithCategory($model, $id)
	{
		app('veer')->cachingQueries->make(
			$model::whereHas('products', function($query) use($id) 
			{
				$query->checked()->whereHas('categories', function($q) use ($id) {
							$q->where('categories_id','=',$id);
					});
			})->orWhereHas('pages', function($query) use($id) 
			{
				 $query->excludeHidden()->whereHas('categories', function($q) use ($id) {
							$q->where('categories_id','=',$id);
					});
			}));
			
		return app('veer')->cachingQueries->remember(5, 'get');
	}
	
	
	public function withTags($id)
	{
		return $this->getModelWithCategory("\Veer\Models\Tag", $id);		
	}	
	
	
	public function withAttributes($id)
	{
		return $this->getModelWithCategory("\Veer\Models\Attribute", $id);	
	}
	

	/* all or filtered categories or one category */
	public function getCategoriesWithoutSite($category = null, $image = null) 
	{	
		if($category == null || $image != null) 
		{
			return $this->getAllCategories($image);	
		} 		
		
		return $this->getCategoryAdvanced($category);
	}	
	
	
	/**
	 * show Many Categories
	 * @params filter
	 */
	public function getAllCategories($imageFilter = null)
	{
		if(!empty($imageFilter)) 
		{
			$items = $this->filterCategoryByImage($imageFilter);

			app('veeradmin')->filtered = "images";
			
			app('veeradmin')->filtered_id  = $imageFilter;
		} 

		else 
		{
			$items = \Veer\Models\Site::with(array('categories' => function($query) 
				{
					$query->has('parentcategories', '<', 1)
						->orderBy('manual_sort','asc')
						->with('pages', 'products', 'subcategories');
				}));
		}
				
		return $items->orderBy('manual_sort','asc')->get();
	}	
	
	
	protected function filterCategoryByImage($imageFilter = null)
	{
		return \Veer\Models\Site::with(array('categories' => function($query) use ($imageFilter) 
		{
			$query->whereHas('images',function($q) use ($imageFilter) 
			{
				$q->where('images_id','=',$imageFilter);					
			})
			->with('products', 'pages', 'subcategories');
		}));	
	}
	
	
	/* get Category Advanced */
	public function getCategoryAdvanced($category, $options = array()) 
	{
		$items = $this->getCategory($category, null);

		if(is_object($items)) 
		{
			$items->load('products', 'communications');

			$this->loadImagesWithElements($items, array_get($options, 'skipWith', false));
			
			$items->load(array('pages' => function($q) {
				$q->orderBy('manual_order', 'asc');
			}));

			$items->site_title = db_parameter('SITE_TITLE', null, $items->sites_id);
		}	
		
		return $items;
	}
}
