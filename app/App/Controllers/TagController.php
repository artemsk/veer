<?php

class TagController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$tags= app('veerdb')->route();   
				
		if(!is_object($tags)) { return Redirect::route('index'); }
		
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.tags', array(
			"tags" => $tags,
			"data" => $data,
			"template" => $data['template']
		)); 

		$this->view = $view; 

		return $view;
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
		$vdb = app('veerdb');

		$tag = $vdb->route($id);   
		
		if(!is_object($tag)) { return Redirect::route('index'); }
		
		$data = $this->veer->loadedComponents;     
			
		$paginator_and_sorting = get_paginator_and_sorting();

		$products = $vdb->tagOnlyProductsQuery($this->veer->siteId, $id, $paginator_and_sorting);

		$pages = $vdb->tagOnlyPagesQuery($this->veer->siteId, $id, $paginator_and_sorting);
				
		$siteId = $this->veer->siteId;
			
		$attributes = \Veer\Models\Attribute::whereHas('products', function($query) use($id, $siteId) {
				$query->siteValidation($siteId)->checked()->whereHas('tags', function($q) use ($id) {
						$q->where('tags_id','=',$id);
				});
			})->orWhereHas('pages', function($query) use($id, $siteId) {
				$query->siteValidation($siteId)->excludeHidden()->whereHas('tags', function($q) use ($id) {
						$q->where('tags_id','=',$id);
				});
			})->remember(5)->get();		
				
		$categories = \Veer\Models\Category::whereHas('products', function($query) use($id, $siteId) {
				$query->siteValidation($siteId)->checked()->whereHas('tags', function($q) use ($id) {
						$q->where('tags_id','=',$id);
				});
			})->orWhereHas('pages', function($query) use($id, $siteId) {
				$query->siteValidation($siteId)->excludeHidden()->whereHas('tags', function($q) use ($id) {
						$q->where('tags_id','=',$id);
				});
			})->remember(5)->get();	
				
		$view = view($this->template.'.tag', array(
			"tag" => $tag,
			"products" => $products,
			"pages" => $pages,
			"attributes" => $attributes,
			"categories" => $categories,
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
