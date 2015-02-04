<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Veer\Services\Show\Category as ShowCategory;

class CategoryController extends Controller {

	protected $showCategory;
	
	public function __construct(ShowCategory $showCategory)
	{
		parent::__construct();
		
		$this->showCategory = $showCategory;
	}

	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$categories = $this->showCategory->getTopCategoriesWithSite(
			app('veer')->siteId
		);
				
		if(!is_object($categories)) { return Redirect::route('index'); }
		
		$view = view($this->template.'.categories', array(
			"categories" => $categories,
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		)); 

		$this->view = $view; 

		return $view;
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

		$category = $this->showCategory->getCategory(app('veer')->siteId, $id);

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
                })->get(); // TODO: remember 5
				 
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

}


// TODO: products & pages должны подгружать комментарии
// TODO: all, new, comments, ratings?
// TODO: сортировка товаров в зависимости от route