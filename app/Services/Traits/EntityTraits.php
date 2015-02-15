<?php namespace Veer\Services\Traits;

trait EntityTraits {
	
	
	/* get categories for products & pages */
	public function getCategoriesWhereHasElements($type, $id, $siteId)
	{
		return \Veer\Models\Category::whereHas($type, function($q) use($id) {
				$q->where('elements_id', '=', $id);
			})
			->where('sites_id', '=', $siteId)
			->with(array('images' => function($query) {
				$query->orderBy('id', 'asc')->take(1);
			}
			))->orderBy('created_at', 'desc')->get();	
	}
			
	
	/* load comments for object */
	public function loadComments($object, $type = 'page')
	{
		if(db_parameter('COMMENTS_SYSTEM') == "disqus") 
		{ 
			app('veer')->loadedComponents['comments_disqus'] = view('components.disqus', array("identifier" => $type.$object->id));
		} 
		
		else 
		{
			$object->load('comments');
			
			app('veer')->loadedComponents['comments_own'] = $object->comments->toArray();
		}
	}	
	
	
	/**
	 * get all pages|products
	 */
	public function getAllEntities($model, $filters = array(), $paginateItems = 25) 
	{			
		$type = key($filters);
		
		$filter_id = head($filters);
				
		if(!empty($type) && !empty($filter_id)) $items = $this->filterEntities($model, $type, $filter_id);
		
		if($type == "unused") $items = $model::has('categories','<',1);
		
		if(!isset($items)) $items = $model::select();
		
		if($model == "\Veer\Models\Page") {
			$items->with('images', 'categories', 'user', 'subpages', 'comments');
		} else {
			$items->with('images', 'categories');
		}
		
		if(!empty($type)) app('veer')->loadedComponents['filtered'] = $type; 
						
		if(!empty($filter_id)) app('veer')->loadedComponents['filtered_id'] = $this->replaceFilterId($type, $filter_id); 
		
		return $items->orderBy('id','desc')->paginate($paginateItems);	
	}	
	
	/* filter pages */
	public function filterEntities($model, $type, $filter_id)
	{
		$type_field = $type;
		
		if($type == "site")
		{
			$type = "categories";
			$type_field = "sites";
		}
		
		return $model::whereHas($type, function($query) use ($filter_id, $type_field) 
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
	
	/* get only home entities */
	public function getHomeEntities($model, $siteId, $homeId)
	{
		$items = $model::homepages($siteId, $homeId)->with(
			array( 'categories' => function($query) use ($siteId, $homeId) {
					$query->where('sites_id','=',$siteId)->where('categories.id','!=',$homeId);
				}))->with(array('images' => function($query) {
					$query->orderBy('id','asc')->take(1);
				}));
	
		if($model == "\Veer\Models\Page") {
			$items->excludeHidden()->with('user');
		} else {
			$items->checked();
		}
		
		return $items;   
	}
}