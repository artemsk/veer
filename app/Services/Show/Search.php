<?php namespace Veer\Services\Show;

class Search {

	use \Veer\Services\Traits\FilterTraits;
	
	protected $targetModel;
	
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
			$query->orderBy('id', 'desc');
		}));

		if(!empty($siteId) && $model == '\Veer\Models\Product') $results->checked()->siteValidation($siteId);
		
		if(!empty($siteId) && $model == '\Veer\Models\Page') $results->excludeHidden()->siteValidation($siteId);
		
		return $results->orderBy(array_get($queryParams, 'sort', 'created_at'), array_get($queryParams, 'direction', 'desc'))
			->take(array_get($queryParams, 'take', 25))
			->skip(array_get($queryParams, 'skip', 0))->get();
	}
	
	protected function parseQ($q)
	{		
		if(starts_with($q, '!')) return array_add( explode(":", substr(mb_strtolower($q),1)), 1, null);
				
		return array(null, null);
	}
	
	protected function getModelName($model, $t)
	{
		if(empty($model)) return $this->findModelNameByUrl($t);
		
		if(in_array($model, array('product', 'page', 'category', 'user', 'order'))) { 
			
			$this->targetModel = str_plural($model);
			
			return elements($this->targetModel); 
		}
	}
	
	protected function findModelNameByUrl($t)
	{
		$models = array(
			"books" => "UserBook",
			"lists" => "UserList",
			"roles" => "UserRole",
			"statuses" => "OrderStatus",
			"payment" => "OrderPyment",
			"shipping" => "OrderShipping",
			"discounts" => "UserDiscount",
			"bills" => "OrderBill",
			"jobs" => null,
			"etc" => null
		);
		
		if(!array_key_exists($t, $models)) return elements($t);
		
		if(isset($models[$t])) return elements($models[$t]);
	}
	
	/**
	 * 
	 * @todo leftovers: categories, attributes,
	 */
	protected function getSearchFields($t)
	{
		$fields = array(
			"users" => array("email", "username", "firstname", "lastname", "phone"),
			"books" => array("name", "country", "region", "city", "postcode", "address", "nearby_station", "b_bank", "b_bik", "b_others"),
			"searches" => array("q"),
			"comments" => array("author", "txt", "rate"),
			"pages" => array("title", "small_txt", "txt"),
			"products" => array("title", "descr", "production_code"),
			"tags" => array("name"),
			"orders" => array("id", "cluster_oid", "email", "phone"),
			"bills" => array("id", "orders_id")	
		);
		
		return isset($fields[$t]) ? $fields[$t] : null;
	}
	
	/** 
	 * Search
	 */
	public function searchAdmin($t, $paginateItems = 25)
	{
		$this->targetModel = $t;
		
		$q = \Input::get('SearchField');
		
		list($model, $id) = $this->parseQ($q);
		
		$field = $model == 'category' ? 'category' : 'id';
		
		$model = $this->getModelName($model, $this->targetModel);
				
		if(!empty($id)) return \Redirect::route('admin.show', array($this->targetModel, $field => $id));
		
		$view = $this->targetModel;
		
		$searchFields = $this->getSearchFields($this->targetModel);
		
		if(!empty($searchFields))
		{
			$items = $model::whereNested(function($query) use($q, $searchFields) {
				foreach($searchFields as $s) { $query->orWhere($s, 'like', '%'.$q.'%'); }
			})->paginate($paginateItems);	
		}
		
		if(isset($items) && is_object($items))
		{
			return view(app('veer')->template.'.'.$view, array(
				"items" => $items,
				"data" => app('veer')->loadedComponents,
				"template" => app('veer')->template
			));
		}
			
		return false;
	}	
}
