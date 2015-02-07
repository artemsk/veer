<?php namespace Veer\Services\Show;

class Attribute {
	
	use \Veer\Services\Traits\CommonTraits;
		
	/**
	 * Handle.
	 *
	 * @return Collection|array
	 */
	public function handle($siteId = null, $paginateItems = 100)
	{
		return !empty($siteId) ? 
			  $this->getTopAttributesWithSite($siteId) 
			: $this->getUngroupedAttributes($paginateItems);
	}
		
	/**
	 * Make query within site.
	 * 
	 * 
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
	 * Make general query.
	 * 
	 * 
	 */
	protected function query()
	{
		return \Veer\Models\Attribute::orderBy('name')->with('pages', 'products');
	}
	
	/**
	 * Get top attributes within site.
	 * 
	 * 
	 */
	public function getTopAttributesWithSite($siteId)
	{
		app('veer')->cachingQueries->make($this->queryWithSite($siteId));
		
		return app('veer')->cachingQueries->remember(5, 'get');
	}
	
	/**
	 * Get all attributes.
	 * 
	 * 
	 */
	public function getUngroupedAttributes($paginateItems = 100)
	{
		$items = $this->query();
			
		$items = $paginateItems < 0 ? $items->get() : $items->paginate($paginateItems);		
		
		$iteratedItems = $this->iterateAttributes($items);
		
		$items['grouped'] = array_get($iteratedItems, 'grouped');
			
		$items['counted'] = array_get($iteratedItems, 'counted');			
		
		return $items;
	}
	
	/**
	 * Iterate attributes.
	 * 
	 * 
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
	 * Get attribute.
	 * 
	 * 
	 * @param integer $id
	 */
	public function getAttribute($id, $childId = null)
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
	 * Get pages associated with attribute.
	 * 
	 * 
	 * @param string $queryParams
	 */
	public function withPages($siteId, $attributeId, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('pages', 'attributes', $attributeId, $siteId, $queryParams);
	}	
	
	/**
	 * Get products associated with attribute.
	 * 
	 * 
	 * @param string $queryParams
	 */
	public function withProducts($siteId, $attributeId, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('products', 'attributes', $attributeId, $siteId, $queryParams);
	}	
	
	/**
	 * Get other models associated with attribute
	 * 
	 * 
	 * @param string $model
	 */
	protected function getModelWithAttribute($model, $attributeName, $attributeVal, $siteId)
	{
		app('veer')->cachingQueries->make(
			$model::whereHas('products', function($query) use($attributeName, $attributeVal, $siteId) 
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
			}));
			
		return app('veer')->cachingQueries->remember(5, 'get'); 	
	}
	
	/**
	 * Get tags associated with attribute.
	 * 
	 * 
	 */
	public function withTags($attributeName, $attributeVal, $siteId)
	{
		return $this->getModelWithAttribute("\Veer\Models\Tag", $attributeName, $attributeVal, $siteId);		
	}	
	
	/**
	 * Get categories associated with attribute
	 * 
	 * 
	 */	
	public function withCategories($attributeName, $attributeVal, $siteId)
	{
		return $this->getModelWithAttribute("\Veer\Models\Category", $attributeName, $attributeVal, $siteId);	
	}
	
}
