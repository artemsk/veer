<?php namespace Veer\Services\Show;

class Page {
	
	use \Veer\Services\Traits\CommonTraits;
	
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
		
		$page = \Veer\Models\Page::where($field, '=', $id);
	
		if(!empty($siteId)) $page = $page->excludeHidden()->sitevalidation($siteId);
		
		return $page->first(); 
	}

	/*
	 * check contents inside public/html folder
	 */
	public function isHtmlFileExist($siteId, $id)
	{
        $p_html = config('veer.htmlpages_path') . '/' . $id . '.html'; // TODO: $siteId . '/' . ?
		
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
		return $this->getCategoriesWhereHasElements('pages', $id, $siteId);
	}

	public function getPageAdvanced($page, $options = array())
	{
		if($page == "new") return new \stdClass(); 
			
		$items = $this->getPage($page);
			
		if(is_object($items)) 
		{
			$items->load(
				'user', 'subpages', 'parentpages', 'products', 'categories', 
				'tags', 'attributes', 'downloads');
			
			$this->loadImagesWithElements($items, array_get($options, 'skipWith', false));
			
			$items['lists'] = 
				$items->userlists()->count(\Illuminate\Support\Facades\DB::raw(
					'DISTINCT name'
				));
			
			$items->markdownSmall = \Parsedown::instance()
				->setBreaksEnabled(true)
				->text($items->small_txt);
			$items->markdownTxt = \Parsedown::instance()
				->setBreaksEnabled(true)
				->text($items->txt);
			// TODO: test because of fatal errors
		}	
			
		return $items;
	}
	
	/**
	 * Show Pages
	 */
	public function getAllPages($filters = array(), $paginateItems = 25) 
	{			
		$type = key($filters);
		
		$filter_id = head($filters);
				
		if(!empty($type) && !empty($filter_id)) $items = $this->filterPages($type, $filter_id);
		
		if($type == "unused") $items = \Veer\Models\Page::has('categories','<',1);
		
		if(!isset($items)) $items = \Veer\Models\Page::select();
		
		$items = $items->orderBy('id','desc')->with(
				'images', 'categories', 'user', 'subpages', 'comments' )->paginate($paginateItems); 

		if(!empty($type)) app('veer')->loadedComponents['filtered'] = $type; 
		
		else app('veer')->loadedComponents['counted'] = \Veer\Models\Page::count(); 
				
		if(!empty($filter_id)) app('veer')->loadedComponents['filtered_id'] = $this->replaceFilterId($type, $filter_id); 
		
		return $items;
	}	
	
	/* filter pages */
	public function filterPages($type, $filter_id)
	{
		$type_field = $type;
		
		if($type == "site")
		{
			$type = "categories";
			$type_field = "sites";
		}
		
		return \Veer\Models\Page::whereHas($type, function($query) use ($filter_id, $type_field) 
		{
			$query->where( $type_field . '_id', '=', $filter_id );
		});
	}
	
	/* replace id with name if it's possible */
	protected function replaceFilterId($type, $filter_id)
	{
		if($type == "attributes")
		{				
			$a = \Veer\Models\Attribute::where('id','=',$filter_id)
				->select('name', 'val')->first();

			if(is_object($a)) 
			{
				$filter_id = $a->name.":".$a->val;
			}
		}

		if($type == "tags") $filter_id = \Veer\Models\Tag::where('id','=',$filter_id)->pluck('name');	
		
		return $filter_id;
	}
	
}
