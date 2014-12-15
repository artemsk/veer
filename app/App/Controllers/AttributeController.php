<?php

class AttributeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $attributes = app('veerdb')->route();   
		
		if(!is_object($attributes)) { return Redirect::route('index'); }
                
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.attributes', array(
			"attributes" => $attributes,
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

		$attribute = $vdb->route($id);   
		
		if(!is_object($attribute)) { return Redirect::route('index'); }
		
		$data = $this->veer->loadedComponents;     

		if($vdb->data['parent_flag'] != 1) {
			
			$paginator_and_sorting = get_paginator_and_sorting();

			$products = $vdb->attributeOnlyProductsQuery($this->veer->siteId, $id, $paginator_and_sorting);

			$pages = $vdb->attributeOnlyPagesQuery($this->veer->siteId, $id, $paginator_and_sorting);
			
			$siteId = $this->veer->siteId;
			
			$tags = \Veer\Models\Tag::whereHas('products', function($query) use($id, $siteId, $attribute) {
                    $query->siteValidation($siteId)->checked()->whereHas('attributes', function($q) use ($id, $attribute) {
							$q->where('name','=',$attribute['name'])->where('val','=',$attribute['val']);
					});
                })->orWhereHas('pages', function($query) use($id, $siteId, $attribute) {
                    $query->siteValidation($siteId)->excludeHidden()->whereHas('attributes', function($q) use ($id, $attribute) {
							$q->where('name','=',$attribute['name'])->where('val','=',$attribute['val']);
					});
                })->remember(5)->get();		
				
			$categories = \Veer\Models\Category::whereHas('products', function($query) use($id, $siteId, $attribute) {
                    $query->siteValidation($siteId)->checked()->whereHas('attributes', function($q) use ($id, $attribute) {
							$q->where('name','=',$attribute['name'])->where('val','=',$attribute['val']);
					});
                })->orWhereHas('pages', function($query) use($id, $siteId, $attribute) {
                    $query->siteValidation($siteId)->excludeHidden()->whereHas('attributes', function($q) use ($id, $attribute) {
							$q->where('name','=',$attribute['name'])->where('val','=',$attribute['val']);
					});
                })->remember(5)->get();	
				
			$view = view($this->template.'.attribute', array(
				"attribute" => $attribute,
				"products" => $products,
				"pages" => $pages,
				"tags" => $tags,
				"categories" => $categories,
				"data" => $data,
				"template" => $data['template']
			));
		
		} else {
			
			$view = view($this->template.'.attribute', array(
				"attribute" => $attribute,
				"data" => $data,
				"template" => $data['template']
			));
		}
				
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
