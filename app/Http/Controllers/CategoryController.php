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
				
		return $this->viewIndex('categories', $categories);
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{	
		$category = $this->showCategory->getCategory($id, app('veer')->siteId);

		if(!is_object($category)) { return Redirect::route('index'); }
		
		$category->increment('views');	

        $category->load(array('images' => function($q) {
			return $q->orderBy('pivot_id', 'asc');
		}));
		
		$paginator_and_sorting = get_paginator_and_sorting();
		
		$view = view($this->template.'.category', array(
			"category" => $category,
			"products" => $this->showCategory->withProducts($id, $paginator_and_sorting),
			"pages" => $this->showCategory->withPages($id, $paginator_and_sorting),
			"tags" => $this->showCategory->withTags($id),
			"attributes" => $this->showCategory->withAttributes($id),
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		)); 

		$this->view = $view; 

		return $view;
	}

}


// TODO: products & pages должны подгружать комментарии
// TODO: all, new, comments, ratings?
// TODO: сортировка товаров в зависимости от route