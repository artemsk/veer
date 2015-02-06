<?php namespace Veer\Services\Traits;

trait FilterTraits {
	
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
	
	protected function getFilterCacheName($model, $products, $pages)
	{
		return ($model."|".implode("|", $products->modelKeys())."|".implode("|", $pages->modelKeys()));
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
	
}