<?php namespace Veer\Services\Show;

class Page {
	
	use \Veer\Services\Traits\CommonTraits, \Veer\Services\Traits\CommentTraits;
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Pages
	 * - with: Images
	 * - to whom: make() | page/{blank}
	 */
	public function getPagesWithSite($siteId, $queryParams = array())
	{
		return \Veer\Models\Page::whereHas('categories', function($q) use($siteId) {
				$q->where('sites_id', '=', $siteId);
			})->excludeHidden()
			->orderBy(array_get($queryParams, 'sort', 'created_at'), array_get($queryParams, 'direction', 'desc'))
			->take(array_get($queryParams, 'take_pages', 15))
			->skip(array_get($queryParams, 'skip_pages', 0))
			->with(array('images' => function($query) {
				$query->orderBy('id', 'desc')->take(1);
			}
			), 'categories')->get();
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: 1 Page
	 * - with: 
	 * - to whom: make() | page/{id}
	 */
	public function getPage($id, $siteId = null)
	{
		$field = !is_numeric($id) ? "url" : "id";
		
		$page = \Veer\Models\Page::where($field, '=', $id)->excludeHidden();
		
		if(!empty($siteId)) $page = $page->sitevalidation($siteId);
		
		return $page->first(); 
	}

	/*
	 * check contents inside public/html folder
	 */
	public function isHtmlFileExist($siteId, $id)
	{
        $p_html = config('veer.htmlpages_path') . '/' . $siteId . '/' . $id . '.html';
		
        if (\File::exists( $p_html )) return $p_html;
	}
	
	/*
	 * get content from public/html folder
	 */
	public function getContentFromHtmlFile($htmlLink = null)
	{  
		return !empty($htmlLink) ? \File::get( $htmlLink ) : null;
	}
	
	/* 
	 * check file and get at once
	 */
	public function checkAndGetContentFromHtmlFile($siteId, $id)
	{
		return $this->getContentFromHtmlFile( $this->isHtmlFileExist($siteId, $id) );
	}
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Many Products
	 * - with: Images
	 * - to whom: 1 Page | page/{id}
	 */
	public function withProducts($siteId, $id, $queryParams)
	{
		return $this->getElementsWhereHasModel('products', 'pages', $id, $siteId, $queryParams);
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Sub Pages
	 * - with: Images
	 * - to whom: 1 Page | page/{id}
	 */
	public function withChildPages($siteId, $id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('pages', array('parentpages', 'parent'), $id, $siteId, $queryParams);
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Parent Pages
	 * - with: Images
	 * - to whom: 1 Page | page/{id}
	 */
	public function withParentPages($siteId, $id, $queryParams = null)
	{
		return $this->getElementsWhereHasModel('pages', array('subpages', 'child'), $id, $siteId, $queryParams);
	}

	/**
	 * Query Builder: 
	 * 
	 * - who: Many Categories
	 * - with: Images
	 * - to whom: 1 Page | page/{id}
	 */
	public function withCategories($siteId, $id)
	{
		return \Veer\Models\Category::whereHas('pages', function($q) use($id) {
					$q->where('elements_id', '=', $id);
				})
				->where('sites_id', '=', $siteId)
				->with(array('images' => function($query) {
					$query->orderBy('id', 'asc')->take(1);
				}
				))->orderBy('created_at', 'desc')->get();
	}


	
}
