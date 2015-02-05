<?php namespace Veer\Services\Show;

class Image {

	use \Veer\Services\Traits\CommonTraits;
	
	/**
	 * handle
	 */
	public function handle($paginateItems = 25)
	{
		return $this->getImages(array(), $paginateItems);
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Image
	 * - with: 
	 * - to whom: make() | image/{id}
	 *
	 * We should include the same statement three times as "OR" operator always has 
	 * priority over "AND". Methods checked() & excludehidden() ignored. Cached for 5 minutes.
	 */
	public function getImageWithSite($siteId, $id)
	{
		return \Veer\Models\Image::where('id', '=', $id)->withTrashed()->whereHas('products', function($q) use($siteId) {
				$q->sitevalidation($siteId);
			})
			->whereRaw('`images`.`deleted_at` is null')
			->orWhereHas('pages', function($q) use($siteId) {
				$q->sitevalidation($siteId);
			})
			->whereRaw('`images`.`deleted_at` is null')
			->where('id', '=', $id)
			->orWhereHas('categories', function($q) use($siteId) {
				$q->where('sites_id', '=', $siteId);
			})
			->whereRaw('`images`.`deleted_at` is null')
			->where('id', '=', $id)
			->orWhereHas('users', function($q) use($siteId) {
				$q->where('sites_id', '=', $siteId);
			})
			->whereRaw('`images`.`deleted_at` is null')
			->where('id', '=', $id)
			->first();
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Products
	 * - with: 
	 * - to whom: 1 Image | image/{id}
	 */
	public function withProducts($siteId, $id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('products', 'images', $id, $siteId, $queryParams);
	}	
			
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages
	 * - with: 
	 * - to whom: 1 Image | image/{id}
	 */
	public function withPages($siteId, $id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('pages', 'images', $id, $siteId, $queryParams);
	}	
	
	/** custom method for categories & users */
	protected function whereHasImageCustom($model, $id, $siteId)
	{
		return $model::whereHas('images', function($q) use($id) {
				$q->where('images_id', '=', $id);
			})
			->where('sites_id', '=', $siteId)
			->orderBy('created_at', 'desc')
			->get();
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Categories
	 * - with: 
	 * - to whom: 1 Image | image/{id}
	 */
	public function withCategories($siteId, $id)
	{
		return $this->whereHasImageCustom('\Veer\Models\Category', $id, $siteId);
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Users
	 * - with: 
	 * - to whom: 1 Image | image/{id}
	 */
	public function withUsers($siteId, $id)
	{
		return $this->whereHasImageCustom('\Veer\Models\User', $id, $siteId);
	}	
	
	/**
	 * Show Images
	 */
	public function getImages( $filters = array(), $paginateItems = 25 ) 
	{	
		app('veer')->loadedComponents['counted'] = \Veer\Models\Image::count();
		
		$items = key($filters) == "unused" ? $this->getUnusedImages() : $this->getAllImages();

		return $items->paginate($paginateItems);
	}
	
	
	/* unused images */
	public function getUnusedImages()
	{
		return  \Veer\Models\Image::orderBy('id', 'desc')
				->has('pages','<',1)
				->has('products','<',1)
				->has('categories','<',1)
				->has('users','<',1);
	}
	
	/* all images with elements */
	public function getAllImages()
	{
		return \Veer\Models\Image::orderBy('id', 'desc')
				->with(
					'pages', 
					'products', 
					'categories', 
					'users');
	}
	
}
