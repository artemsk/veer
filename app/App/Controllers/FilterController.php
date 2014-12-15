<?php

class FilterController extends \BaseController {

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
		//
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
		
		$filtered = app('veerdb')->route($id);        		

		if(!is_array($filtered)) { return Redirect::route('index'); }
		
		$paginator_and_sorting = get_paginator_and_sorting(); //?
		     
		$items = array("products" => array(0 => 0), "pages" => array(0 => 0));
		
		if(count($filtered['products'])) {
			foreach($filtered['products'] as $p) {
				$items['products'][$p->id] = $p->id;
			}
		}

		if(count($filtered['pages'])) {
			foreach($filtered['pages'] as $p) {
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

		$view = view($this->template.'.filter', array(
			"categories" => $categories,
			"products" => @$filtered['products'],
			"pages" => @$filtered['pages'],
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
