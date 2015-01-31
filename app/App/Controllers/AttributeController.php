<?php

class AttributeController extends \BaseController {

	protected $showAttribute;
	
	public function __construct(\Veer\Architecture\showAttribute $showAttribute)
	{
		parent::__construct();
		
		$this->showAttribute = $showAttribute;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $attributes = $this->showAttribute->getTopAttributesWithSite(
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
		$attribute = $this->showAttribute->getParentOrChildAttribute($id, $childId);
		
		if(!is_object($attribute)) { return Redirect::route('index'); }
		
		if(!empty($childId)) {
			
			$page_sort = get_paginator_and_sorting();

			$products = $this->showAttribute->getProductsWithAttribute(app('veer')->siteId, $childId, $page_sort);

			$pages = $this->showAttribute->getPagesWithAttribute(app('veer')->siteId, $childId, $page_sort);
			
			$tags = $this->showAttribute->getTagsWithAttribute($attribute->name, $attribute->val, app('veer')->siteId);	

			$categories = $this->showAttribute->getCategoriesWithAttribute($attribute->name, $attribute->val, app('veer')->siteId);	

			$view = view($this->template.'.attribute', array(
				"attribute" => $attribute,
				"products" => $products,
				"pages" => $pages,
				"tags" => $tags,
				"categories" => $categories,
				"data" => $this->veer->loadedComponents,
				"template" => $this->template
			));
		
		} else {
			
			$view = view($this->template.'.attribute', array(
				"attribute" => $attribute,
				"data" => $this->veer->loadedComponents,
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
