<?php namespace Veer\Services\Show;

class Category {
	
	use \Veer\Services\Traits\CommonTraits;
	
	/**
	 * Handle.
	 * 
	 * 
	 */
	public function handle($siteId = null, $paginateItems = 100)
	{
		return !empty($siteId) ? 
			  $this->getTopCategoriesWithSite($siteId) 
			: $this->getCategoriesWithoutSite($paginateItems);
	}
	
	/**
	 * Get top categories within site.
	 * 
	 * 
	 */
	public function getTopCategoriesWithSite($siteId)
	{
		return \Veer\Models\Category::where('sites_id', '=', $siteId)->has('parentcategories', '<', 1)
				->with(array('images' => function($query) {
					$query->orderBy('pivot_id', 'asc');
				}))->get();
	}
	
	/**
	 * Get category.
	 * 
	 * 
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
	 * Get products associated with category.
	 * 
	 * 
	 */
	public function withProducts($id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('products', 'categories', $id, null, $queryParams);
	}

	/**
	 * Get pases associated with category.
	 * 
	 * 
	 */
	public function withPages($id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('pages', 'categories', $id, null, $queryParams);
	}	
	
	/**
	 * Get tags associated with category.
	 * 
	 * 
	 */	
	public function withTags($id)
	{
		return $this->withModels('\Veer\Models\Tag', 'categories', $id);
	}	
	
	/**
	 * Get attributes associated with category.
	 * 
	 * 
	 */	
	public function withAttributes($id)
	{
		return $this->withModels('\Veer\Models\Attribute', 'categories', $id);	
	}
	
	/**
	 * Get general categories.
	 * 
	 * 
	 */
	public function getCategoriesWithoutSite($category = null, $image = null) 
	{	
		if($category == null || $image != null) 
		{
			return $this->getAllCategories($image);	
		} 		
		
		return $this->getCategoryAdvanced($category);
	}	
	
	/**
	 * Get all categories.
	 * 
	 * 
	 */
	public function getAllCategories($imageFilter = null)
	{
		if(!empty($imageFilter)) 
		{
			$items = $this->filterCategoryByImage($imageFilter);

			app('veer')->loadedComponents['filtered'] = "images";
			
			app('veer')->loadedComponents['filtered_id']  = $imageFilter;
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
	
	/**
	 * Filter category by image.
	 * 
	 * 
	 */
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
	
	/**
	 * Get category.
	 * 
	 * 
	 */
	public function getCategoryAdvanced($category, $options = array()) 
	{
		$items = $this->getCategory($category, null);

		if(is_object($items)) 
		{
			$items->load('communications');

			$this->loadImagesWithElements($items, array_get($options, 'skipWith', false));
			
			$items->load(array('pages' => function($q) {
				$q->with('user', 'subpages', 'categories', 'comments', 'images')->orderBy('manual_order', 'asc');
			},
                'products' => function($q) {
                    $q->with('categories', 'images');
            }));

			$items->site_title = db_parameter('SITE_TITLE', null, $items->sites_id);
		}	
		
		return $items;
	}
}
