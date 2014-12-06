<?php

class ProductController extends \BaseController {

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
		// TODO: queryParams -> sort, filter
		
		$method = Route::currentRouteName();

		if(in_array($id, array('new', 'ordered', 'viewed'))) {

			$method = "sortingProducts";                  
		} 

		$vdb = app('veerdb');

		$product = $vdb->make($method, $id);                 

		if(!is_object($product)) { return Redirect::route('product.show', array('new')); }
		
		$paginator_and_sorting = get_paginator_and_sorting();
		
			$sub = $vdb->productOnlySubProductsQuery($this->veer->siteId, $id, $paginator_and_sorting);

			$parent = $vdb->productOnlyParentProductsQuery($this->veer->siteId, $id, $paginator_and_sorting);

			$categories = $vdb->productOnlyCategoriesQuery($this->veer->siteId, $id, $paginator_and_sorting);

			$pages = $vdb->productOnlyPagesQuery($this->veer->siteId, $id, $paginator_and_sorting);
                
		$product->increment('viewed');	

		$product->load('images', 'tags', 'attributes', 'downloads', 'userlists');

		// TODO: comments system
		
		// TODO: connected?=sub/parent new? ordered? viewed?
		
		// TODO: groups
		
		/*if($page->show_comments == 1) { 
			if($comments_system == "disqus") { 

				$data_comments['disqus_shortname'] = array_get(Config::get('veer.site_config'),'COMMENTS_DISQUS_ID'); 
				$data_comments['disqus_identifier'] = '_page_' . $pid;
				$data_comments['disqus_title'] = $p->title;
				$data_comments['disqus_url'] = URL::full();                    
				$path_to_comments_template = 'disqus';
			} else {

				$p->load('comments');

				$data_comments = $p->comments->toArray();
			}
		}*/	
		
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
