<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Veer\Services\Show\Search as ShowSearch;

class SearchController extends Controller {

	protected $showSearch;
	
	public function __construct(ShowSearch $showSearch)
	{
		parent::__construct();		
		
		$this->showSearch = $showSearch;
	}
	
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		return Redirect::route('index'); // TODO: configuration - set template page or redirect ( & same for search)
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store()
	{            
		if(\Input::has('q')) 
		{                
			$q = trim(\Input::get('q'));  
			if( !empty($q) ) 
			{
				$search = \Veer\Models\Search::firstOrCreate(array("q" => $q));
				$search->increment('times');                  
				$search->save();

				if(auth_check_session()) { $search->users()->attach(\Auth::id()); } 

				return $this->results(
					$this->showSearch->getSearchResultsWithSite(app('veer')->siteId, $q, get_paginator_and_sorting())
				);
			}
		}
           
		return $this->index(); 			
	}

	/**
	 * Display the specified resource.
	 */
	public function show($id)
	{
		$search = \Veer\Models\Search::find($id);
		
		if(!$search) { return $this->index(); }
		
		$search->increment('times');

		return $this->results(
			$this->showSearch->getSearchResultsWithSite(app('veer')->siteId, $search->q, get_paginator_and_sorting())
		);		
	}
	
	/**
	 * Show results for search.show & search.store
	 */
	protected function results($searched) 
	{	
		$view = viewx($this->template.'.search', array(
			"products" => $searched['products'],
			"pages" => $searched['pages'],
			"categories" => $this->showSearch->withCategories($searched['products'], $searched['pages']),			
			"tags" => $this->showSearch->withTags($searched['products'], $searched['pages']),
			"attributes" => $this->showSearch->withAttributes($searched['products'], $searched['pages']),
			"template" => $this->template
		)); 
	
		$this->view = $view; 
			
		return $view;				
	}

}
