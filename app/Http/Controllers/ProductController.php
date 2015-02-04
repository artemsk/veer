<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;

class ProductController extends Controller {

	public function __construct()
	{
		parent::__construct();

	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->show('new');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int|string  $id
	 * @return Response
	 */
	public function show($id)
	{
		// TODO: queryParams -> sort, filter
		
		$method = \Route::currentRouteName();

		if(in_array($id, array('new', 'ordered', 'viewed'))) {

			$method = "sortingProducts";                  
		} 

		$vdb = app('veerdb');

		$product = $vdb->make($method, $id);                 

		if(!is_object($product)) { return $this->show('new'); }
		
		if($method != "sortingProducts") {
			
			$paginator_and_sorting = get_paginator_and_sorting();

				$sub = $vdb->productOnlySubProductsQuery($this->veer->siteId, $id, $paginator_and_sorting);

				$parent = $vdb->productOnlyParentProductsQuery($this->veer->siteId, $id, $paginator_and_sorting);

				$categories = $vdb->productOnlyCategoriesQuery($this->veer->siteId, $id, $paginator_and_sorting);

				$pages = $vdb->productOnlyPagesQuery($this->veer->siteId, $id, $paginator_and_sorting);

			$product->increment('viewed');	

			$product->load('images', 'tags', 'attributes', 'downloads', 'userlists');

			// TODO: connected?=sub/parent new? ordered? viewed?

			// TODO: groups

			if(db_parameter('COMMENTS_SYSTEM') == "disqus") { 
				$this->veer->loadedComponents['comments_disqus'] = view('components.disqus', array("identifier" => "product".$product->id));
			} else {
				$product->load('comments');
				$this->veer->loadedComponents['comments_own'] = $product->comments->toArray();
			}

			$data = array(
				"product" => $product,
				"subproducts" => $sub,
				"parentproducts" => $parent,
				"pages" => $pages,
				"categories" => $categories,
				"data" => $this->veer->loadedComponents,
				"template" => $this->template
			); 
			
			$view = view($this->template.'.product', $data);

		} else {
			
			$data = array(
				"products" => $product,
				"data" => $this->veer->loadedComponents,
				"template" => $this->template
			); 
			
			$view = view($this->template.'.products', $data);
			
		}
		
		$this->view = $view; 

		return $view;	
	}

}
