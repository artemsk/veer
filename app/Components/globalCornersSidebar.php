<?php namespace Veer\Components;

class globalCornersSidebar {   
    
	public $data;
	
	public function __construct()
	{
		$c = json_decode('['.db_parameter('SIDEBAR_CATEGORIES').']');

		app('veer')->cachingQueries->make(\Veer\Models\Category::where('sites_id','=',app('veer')->siteId)
			->where('id','!=',db_parameter('CATEGORY_HOME', 0))
			->whereIn('id', $c)->orderBy('manual_sort', 'asc'));
		
		$this->data['categories'] = app('veer')->cachingQueries->lists('title', 'id', 5, 'categoriesSidebar'.app('veer')->siteId);
	}
	
}