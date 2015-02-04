<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Veer\Services\Show\Attribute as ShowAttribute;

class AttributeController extends Controller {

	protected $showAttribute;
	
	public function __construct(ShowAttribute $showAttribute)
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
		
		if(!is_object($attributes)) { return \Redirect::route('index'); }
                
		$view = view($this->template.'.attributes', array(
			"attributes" => $attributes,
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
	 */
	public function show($id, $childId = null)
	{		
		$attribute = $this->showAttribute->getAttribute($id, $childId);
		
		if(!is_object($attribute)) { return \Redirect::route('index'); }
		
		$data = array(
			"attribute" => $attribute,
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		);
		
		if(!empty($childId)) 
		{	
			$page_sort = get_paginator_and_sorting();

			array_set($data, 'products', $this->showAttribute->getProductsWithAttribute(app('veer')->siteId, $childId, $page_sort));

			array_set($data, 'pages', $this->showAttribute->getPagesWithAttribute(app('veer')->siteId, $childId, $page_sort));
			
			array_set($data, 'tags', $this->showAttribute->getTagsWithAttribute($attribute->name, $attribute->val, app('veer')->siteId));	

			array_set($data, 'categories', $this->showAttribute->getCategoriesWithAttribute($attribute->name, $attribute->val, app('veer')->siteId));
		} 
			
		$view = view($this->template.'.attribute', $data);

		$this->view = $view; 

		return $view;
	}

}
