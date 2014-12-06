<?php

class ImageController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
            return Redirect::route('index');
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
		
		$image = $vdb->route($id); 
			
		if(!is_object($image)) { return Redirect::route('index'); }
		
		$paginator_and_sorting = get_paginator_and_sorting();

		$products = $vdb->imageOnlyProductsQuery($this->veer->siteId, $id, $paginator_and_sorting);

		$pages = $vdb->imageOnlyPagesQuery($this->veer->siteId, $id, $paginator_and_sorting);

		$categories = $vdb->imageOnlyCategoriesQuery($this->veer->siteId, $id);
		
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.category', array(
			"image" => $image,
			"products" => $products,
			"pages" => $pages,
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
