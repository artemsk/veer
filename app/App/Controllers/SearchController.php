<?php

class SearchController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Redirect::route('index'); // TODO: configuration - set template page or redirect ( & same for search)
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{            
		if(Input::has('q')) 
		{                
			$q = trim(Input::get('q'));  
			if( $q != '' ) 
			{
				$search = \Veer\Models\Search::firstOrCreate(array("q" => $q));
				$search->increment('times');                  
				$search->save();

				if(auth_check_session()) { $search->users()->attach(Auth::id()); } 

				$searched = app('veerdb')->make(Route::currentRouteName(), $search->id,  array( 'q' => $q ));
				
				if(!is_array($searched)) { return $this->index(); }

				return $this->results($searched);
			}
		}
           
		return $this->index(); 			
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// TODO: queryParams -> sort, filter
		
		$search = \Veer\Models\Search::find($id);
		
		if(!$search) { return $this->index(); }
		
		$searched = app('veerdb')->make('search.store', $id,  array('q' => $search->q ));
		
		if(!is_array($searched)) { return $this->index(); }
		
		$search->increment('times');
		
		$paginator_and_sorting = get_paginator_and_sorting(); //?
		     
		return $this->results($searched);		
	}

	
	/**
	 * Show results for search.show & search.store
	 * @param type $searched
	 * @return type
	 */
	protected function results($searched) 
	{	
		$items = array("products" => array(0 => 0), "pages" => array(0 => 0));
		
		if(count($searched['products'])) {
			foreach($searched['products'] as $p) {
				$items['products'][$p->id] = $p->id;
			}
		}

		if(count($searched['pages'])) {
			foreach($searched['pages'] as $p) {
				$items['pages'][$p->id] = $p->id;
			}
		}
		
        $tags = \Veer\Models\Tag::whereHas('products', function($query) use($items) {
                    $query->checked()->whereIn('products.id', $items['products']);
                })->orWhereHas('pages', function($query) use($items) {
                    $query->excludeHidden()->whereIn('pages.id', $items['pages']);
                })->remember(5)->get();
				 
        $attributes = \Veer\Models\Attribute::whereHas('products', function($query) use($items) {
                    $query->checked()->whereIn('products.id', $items['products']);
                })->orWhereHas('pages', function($query) use($items) {
                    $query->excludeHidden()->whereIn('pages.id', $items['pages']);
                })->remember(5)->get();
				
		$categories = \Veer\Models\Category::whereHas('products', function($query) use($items) {
                    $query->checked()->whereIn('products.id', $items['products']);
                })->orWhereHas('pages', function($query) use($items) {
                    $query->excludeHidden()->whereIn('pages.id', $items['pages']);
                })->remember(5)->get();		
					
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.search', array(
			"categories" => $categories,
			"products" => @$searched['products'],
			"pages" => @$searched['pages'],
			"tags" => $tags,
			"attributes" => $attributes,
			"data" => $data,
			"template" => $data['template']
		)); 

		$this->view = $view; 
			
		return $view;			
	}

	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
