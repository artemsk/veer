<?php namespace Veer\Services\Show;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of showCategory
 *
 * @author Jerry
 */
class Category {
	
	/**
	 * handle
	 */
	public function handle($siteId = null, $paginateItems = 100)
	{
		return app('veer')->isSiteFiltered ? 
			  $this->getTopCategoriesWithSite($siteId) 
			: $this->getUngroupedAttributes($paginateItems);
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
	public function getCategory($siteId, $id)
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
	
}
