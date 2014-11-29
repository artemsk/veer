<?php

class ProductController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Redirect::route('product.show', array('new'));
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
	 * @param  int|string  $id
	 * @return Response
	 */
	public function show($id)
	{
                $method = Route::currentRouteName();
                
                if(in_array($id, array('new', 'ordered', 'viewed'))) {
                    
                    $method = "sortingProducts";                  
                } 
                
                $VeerDb = new VeerDb($method, $id);                 
                
                $subproducts = $VeerDb->productOnlySubProductsQuery($this->veer->siteId, $id, get_paginator_and_sorting());
                
                $parentproducts = $VeerDb->productOnlyParentProductsQuery($this->veer->siteId, $id, get_paginator_and_sorting());
                
                $categories = $VeerDb->productOnlyCategoriesQuery($this->veer->siteId, $id, get_paginator_and_sorting());
                
                $pages = $VeerDb->productOnlyPagesQuery($this->veer->siteId, $id, get_paginator_and_sorting());
                
                echo "<pre>";
                print_r(Illuminate\Support\Facades\DB::getQueryLog());
                echo "</pre>";
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
