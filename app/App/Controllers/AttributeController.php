<?php

class AttributeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $attributes = ( new \Veer\Architecture\showAttribute )->getTopAttributesWithSite(
			app('veer')->siteId
		); 
		
		if(!is_object($attributes)) { return Redirect::route('index'); }
                
		$view = view($this->template.'.attributes', array(
			"attributes" => $attributes,
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		)); 

		$this->view = $view; 

		return $view;
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store() {}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, $childId = null)
	{
		$showAttribute = new \Veer\Architecture\showAttribute;
		
		$attribute = $showAttribute->getParentOrChildAttribute($id, $childId);
		
		if(!is_object($attribute)) { return Redirect::route('index'); }
		
		$data = $this->veer->loadedComponents;     

		if(!empty($childId)) {
			
			$page_sort = get_paginator_and_sorting();

			$products = $showAttribute->getProductsWithAttribute(app('veer')->siteId, $childId, $page_sort);

			$pages = $showAttribute->getPagesWithAttribute(app('veer')->siteId, $childId, $page_sort);
			
			$tags = $showAttribute->getTagsWithAttribute($attribute->name, $attribute->val, app('veer')->siteId);	

			$categories = $showAttribute->getCategoriesWithAttribute($attribute->name, $attribute->val, app('veer')->siteId);	
						
			$view = view($this->template.'.attribute', array(
				"attribute" => $attribute,
				"products" => $products,
				"pages" => $pages,
				"tags" => $tags,
				"categories" => $categories,
				"data" => $data,
				"template" => $this->template
			));
		
		} else {
			
			$view = view($this->template.'.attribute', array(
				"attribute" => $attribute,
				"data" => $data,
				"template" => $this->template
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
	public function edit($id) {}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id) {}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id) {}

}
