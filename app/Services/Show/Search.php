<?php namespace Veer\Services\Show;

class Search {

	use \Veer\Services\Traits\FilterTraits;
	
	/**
	 * Query Builder: 
	 * 
	 * - who: Search Results for Many Products & Pages
	 * - with: Images
	 * - to whom: make() | search/{id} or $_POST
	 */
	public function getSearchResultsWithSite($siteId, $q, $queryParams = array())
	{
		$p = array('products' => array(), 'pages' => array());
		
		$qq = explode(' ', $q);

		$p['products'] = $this->searchModel('\Veer\Models\Product', 
			array_get($queryParams, 'search_field_product', 'title'), $qq, $siteId, $queryParams);

		$p['pages'] = $this->searchModel('\Veer\Models\Page', 
			array_get($queryParams, 'search_field_page', 'title'), $qq, $siteId, $queryParams);
		
		return $p;
	}
	
	/* search model */
	protected function searchModel($model, $field, $q, $siteId = null, $queryParams = array())
	{
		$results = $model::whereNested(function($query) use ($q, $field) {
				foreach ($q as $word) {
					$query->where(function($queryNested) use ($word, $field) {
						$queryNested->where($field, '=', $word)
							->orWhere($field, 'like', $word . '%%%')
							->orWhere($field, 'like', '%%%' . $word)
							->orWhere($field, 'like', '%%%' . $word . '%%%');
					});
				}
			})->with(array('images' => function($query) {
			$query->orderBy('id', 'desc')->take(1);
		}));

		if(!empty($siteId) && $model == '\Veer\Models\Product') $results->checked()->siteValidation($siteId);
		
		if(!empty($siteId) && $model == '\Veer\Models\Page') $results->excludeHidden()->siteValidation($siteId);
		
		return $results->orderBy(array_get($queryParams, 'sort', 'created_at'), array_get($queryParams, 'direction', 'desc'))
			->take(array_get($queryParams, 'take', 25))
			->skip(array_get($queryParams, 'skip', 0))->get();
	}
}
