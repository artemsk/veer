<?php namespace Veer\Services\Traits;

trait CommentTraits {
	
	
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