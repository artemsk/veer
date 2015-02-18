<?php
namespace Veer\Components;

class indexCornersDigest {  
	
	use \Veer\Services\Traits\CommonTraits;
	
	public $data;
	
	public $number_of_items = 8;
	
	public function __construct()
	{	
		$tagId = db_parameter('CORNERS_TAG_DIGEST');
		
		$this->data['items'] = $this->getElementsWhereHasModel('pages', 'tags', $tagId, app('veer')->siteId, array(
			"take" => $this->number_of_items
		), true)->select('id', 'title', 'small_txt', 'views', 'created_at', 'users_id')->get();
		
		$this->data['tagName'] = \Cache::remember('tagNameId'. $tagId, 2, function() use($tagId) {
			return \Veer\Models\Tag::where('id', '=', $tagId)->pluck('name');
		});
	}
	
}