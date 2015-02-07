<?php namespace Veer\Services\Show;

class Tag {

	use \Veer\Services\Traits\CommonTraits;
		
	/**
	 * handle
	 */
	public function handle($siteId = null, $paginateItems = 50)
	{
		return !empty($siteId) ? 
			  $this->getTagsWithSite($siteId) 
			: $this->getTagsWithoutSite($paginateItems);
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Tags 
	 * - with: 
	 * - to whom: make() | tag/{blank}
	 *
	 * We should include the same statement twice as "OR" operator always 
	 * has priority over "AND". Methods checked() & excludehidden() ignored 
	 * for some perfomance issues
	 */
	public function getTagsWithSite($siteId)
	{
		return \Veer\Models\Tag::withTrashed()->whereHas('products', function($q) use($siteId) {
				$q->sitevalidation($siteId);
			})
			->whereRaw('`tags`.`deleted_at` is null')
			->orWhereHas('pages', function($q) use($siteId) {
				$q->sitevalidation($siteId);
			}
			)->whereRaw('`tags`.`deleted_at` is null')->get();
	}
	
	/**
	 * Show Tags
	 */
	public function getTagsWithoutSite($paginateItems = 50) 
	{	
		return \Veer\Models\Tag::orderBy('name', 'asc')
			->with('pages', 'products')->paginate($paginateItems);	
	}	
	
	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Tag
	 * - with: 
	 * - to whom: make() | tag/{id}
	 */
	public function getTag($id)
	{
		return \Veer\Models\Tag::where('id', '=', $id)->first();
	}	
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages 
	 * - with: Images
	 * - to whom: 1 Tag | tag/{id}
	 */
	public function withPages($siteId, $id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('pages', 'tags', $id, $siteId, $queryParams);
	}		
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Products 
	 * - with: Images
	 * - to whom: 1 Tag | tag/{id}
	 */
	public function withProducts($siteId, $id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('products', 'tags', $id, $siteId, $queryParams);
	}			
	
	public function withAttributes($id, $siteId)
	{
		return $this->withModels('\Veer\Models\Attribute', 'tags', $id, $siteId);			
	}		
	
	public function withCategories($id, $siteId)
	{
		return $this->withModels('\Veer\Models\Category', 'tags', $id, $siteId);			
	}	
	
}
