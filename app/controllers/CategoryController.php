<?php

class CategoryController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
                $VeerDb = new VeerDb(Route::currentRouteName());   
                
                echo "<pre>";
                print_r(Illuminate\Support\Facades\DB::getQueryLog());
                echo "</pre>";
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
				$method = Route::currentRouteName();
				
				$vdb = app('veerdb');
				
                $category = $vdb->make($method, $id);        		

                $products = $vdb->categoryOnlyProductsQuery($id, get_paginator_and_sorting());
                
                $pages = $vdb->categoryOnlyPagesQuery($id, get_paginator_and_sorting());
                
				$category->increment('views');	
				
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
