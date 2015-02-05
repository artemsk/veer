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
	
}