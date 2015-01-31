<?php namespace Veer\Architecture;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of showAttribute
 *
 * @author Jerry
 */
class showAttribute {
	
	public function __construct()
	{
		//
	}
		
	/**
	 * handle
	 */
	public function handle($siteId = null, $paginateItems = 100)
	{
		return app('veer')->isSiteFiltered ? 
			  $this->getTopAttributesWithSite($siteId) 
			: $this->getUngroupedAttributes($paginateItems);
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
	protected function queryWithSite($siteId)
	{		
		return \Veer\Models\Attribute::withTrashed()->whereHas('products', function($q) use($siteId) {
					$q->sitevalidation($siteId);
				})
				->whereRaw('`attributes`.`deleted_at` is null')
				->orWhereHas('pages', function($q) use($siteId) {
					$q->sitevalidation($siteId);
				})
				->whereRaw('`attributes`.`deleted_at` is null')
				->select('name', 'id')
				->groupBy('name');
	}
	
	/**
	 * general query
	 */
	protected function query()
	{
		return \Veer\Models\Attribute::orderBy('name')->with('pages', 'products');
	}
	
	/**
	 * getter
	 */
	public function getTopAttributesWithSite($siteId)
	{
		return $this->queryWithSite($siteId)->remember(5)->get();
	}
	
	/**
	 * getter
	 */
	public function getUngroupedAttributes($paginateItems = 100)
	{
		$items = $this->query()->paginate($paginateItems);		
		
		$iteratedItems = $this->iterateAttributes($items);
		
		$items['grouped'] = array_get($iteratedItems, 'grouped');
			
		$items['counted'] = array_get($iteratedItems, 'counted');			
		
		return $items;
	}
	
	/**
	 * iterate & group & count attributes
	 */
	public function iterateAttributes($items)
	{
		$iterated = array();
		
		foreach($items as $key => $item) 
		{
			$iterated['grouped'][$item->name][$key] = $key;
			
			$iterated['counted'][$item->name]['prd'] = 
				( isset($iterated['counted'][$item->name]['prd']) ? 
					$iterated['counted'][$item->name]['prd'] : 0 ) + 
				($item->products->count());
			
			$iterated['counted'][$item->name]['pg'] = 
				( isset($iterated['counted'][$item->name]['pg']) ? 
				$iterated['counted'][$item->name]['pg'] : 0 ) + 
				($item->pages->count()); 
		}
		
		return $iterated;
	}
	
	
	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Attribute (Name or Value) 
	 * - with: 
	 * - to whom: make() | attribute/{id[0].id[1]}
	 */
	public function getParentOrChildAttribute($id, $childId = null)
	{
		if (empty($childId)) 
		{
			$p = null;
			
			$parent_attribute = \Veer\Models\Attribute::where('id', '=', $id)->select('name')->first();
			
			if(is_object($parent_attribute)) 
			{	
				$p = \Veer\Models\Attribute::where('name', 'like', $parent_attribute->name)->get();	
			}
		} 
		
		else 
		{
			$p = \Veer\Models\Attribute::where('id', '=', $childId)->first();
		}

		return $p;
	}
	
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages
	 * - with: Images
	 * - to whom: 1 Attribute | attribute/{id[0]/id[1]}
	 */
	public function getPagesWithAttribute($siteId, $attributeId, $queryParams)
	{
		return \Veer\Models\Page::whereHas('attributes', function($q) use($attributeId) 
			{
				$q->where('attributes_id', '=', $attributeId);
			})
			->with(array('images' => function($query) 
			{
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
	 * - who: Many Products
	 * - with: Images
	 * - to whom: 1 Attribute | attribute/{id[0]/id[1]}
	 */
	public function getProductsWithAttribute($siteId, $attributeId, $queryParams)
	{
		return \Veer\Models\Product::whereHas('attributes', function($q) use($attributeId) {
					$q->where('attributes_id', '=', $attributeId);
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
	
	public function getModelWithAttribute($model, $attributeName, $attributeVal, $siteId)
	{
		return $model::whereHas('products', function($query) use($attributeName, $attributeVal, $siteId) 
			{
				$query->siteValidation($siteId)
					->checked()
					->whereHas('attributes', function($q) use ($attributeName, $attributeVal) 
				{
					$q->where('name','=',$attributeName)->where('val','=',$attributeVal);
				});
			})->orWhereHas('pages', function($query) use($attributeName, $attributeVal, $siteId) 
			{
				$query->siteValidation($siteId)
					->excludeHidden()
					->whereHas('attributes', function($q) use ($attributeName, $attributeVal) 
				{
					$q->where('name','=',$attributeName)->where('val','=',$attributeVal);
				});
			})->remember(5)->get();		
	}
	
	public function getTagsWithAttribute($attributeName, $attributeVal, $siteId)
	{
		return $this->getModelWithAttribute("\Veer\Models\Tag", $attributeName, $attributeVal, $siteId);		
	}	
	
	public function getCategoriesWithAttribute($attributeName, $attributeVal, $siteId)
	{
		return $this->getModelWithAttribute("\Veer\Models\Category", $attributeName, $attributeVal, $siteId);	
	}
	
}
