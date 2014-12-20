<?php

class CategoryController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$categories= app('veerdb')->route();   
				
		if(!is_object($categories)) { return Redirect::route('index'); }
		
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.categories', array(
			"categories" => $categories,
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

		$category = $vdb->route($id);        		

		if(!is_object($category)) { return Redirect::route('index'); }
		
		$paginator_and_sorting = get_paginator_and_sorting();
		
		$products = $vdb->categoryOnlyProductsQuery($id, $paginator_and_sorting);

		$pages = $vdb->categoryOnlyPagesQuery($id, $paginator_and_sorting);

		$category->increment('views');	

        $category->load('images');
        
        $tags = \Veer\Models\Tag::whereHas('products', function($query) use($id) {
                    $query->checked()->whereHas('categories', function($q) use ($id) {
							$q->where('categories_id','=',$id);
					});
                })->orWhereHas('pages', function($query) use($id) {
                    $query->excludeHidden()->whereHas('categories', function($q) use ($id) {
							$q->where('categories_id','=',$id);
					});
                })->remember(5)->get();
				 
        $attributes = \Veer\Models\Attribute::whereHas('pages', function($query) use($id) {
                    $query->excludeHidden()->whereHas('categories', function($q) use ($id) {
							$q->where('categories_id','=',$id);
					});
				})->orWhereHas('products', function($query) use($id) {
                    $query->checked()->whereHas('categories', function($q) use ($id) {
							$q->where('categories_id','=',$id);
					});
                })->get(); 		
				
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.category', array(
			"category" => $category,
			"products" => $products,
			"pages" => $pages,
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


// TODO: products & pages должны подгружать комментарии
// TODO: all, new, comments, ratings?
// TODO: сортировка товаров в зависимости от route