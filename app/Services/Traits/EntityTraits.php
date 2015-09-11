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
				$query->orderBy('pivot_id', 'asc');
			}
			))->orderBy('created_at', 'desc')->get();	
	}
			
	
	/* load comments for object */
	public function loadComments($object, $type = 'page')
	{
		if(db_parameter('COMMENTS_SYSTEM') == "disqus") 
		{ 
			app('veer')->loadedComponents['comments_disqus'] = viewx('components.disqus', array("identifier" => $type.$object->id));
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
	public function getAllEntities($model, $filters = array(), $paginateItems = 24, $sort = array('id' => 'desc'))
	{			
		$type = key($filters);
		
		$filter_id = head($filters);
				
		if(!empty($type) && !empty($filter_id)) $items = $this->filterEntities($model, $type, $filter_id);
		
		if($type == "unused") $items = $model::has('categories','<',1);
		
		if(!isset($items)) $items = $model::select();
		
		$items->with(array('images' => function($q) {
			$q->orderBy('pivot_id', 'asc');
		}, 'categories'));
		
		if($model == "\Veer\Models\Page") $items->with('user', 'subpages', 'comments');
		 
		if(!empty($type)) app('veer')->loadedComponents['filtered'] = $type; 
						
		if(!empty($filter_id)) app('veer')->loadedComponents['filtered_id'] = $this->replaceFilterId($type, $filter_id); 
		
		return $this->sortAllEntities($items, $sort)->paginate($paginateItems);
	}	

    protected function sortAllEntities($items, $sort)
    {
        if(empty(key($sort))) return $items->orderBy('id', 'desc');

        return $items->orderBy(key($sort), array_get($sort, key($sort), 'desc'));
    }

    /* filter pages */
	public function filterEntities($model, $type, $filter_id)
	{
		$type_field = $type;
  
		if($type == "site")
		{
			$type = "categories";
		}

		return $model::whereHas($type, function($query) use ($filter_id, $type_field) 
		{
			$query->where( str_plural($type_field) . '_id', '=', $filter_id );
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
