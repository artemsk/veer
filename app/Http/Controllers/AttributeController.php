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
		
		return $this->viewIndex('attributes', $attributes);
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
			"template" => $this->template
		);
		
		if(!empty($childId)) 
		{	
			$page_sort = get_paginator_and_sorting();

			array_set($data, 'products', $this->showAttribute->withProducts(app('veer')->siteId, $childId, $page_sort));

			array_set($data, 'pages', $this->showAttribute->withPages(app('veer')->siteId, $childId, $page_sort));
			
			array_set($data, 'tags', $this->showAttribute->withTags($attribute->name, $attribute->val, app('veer')->siteId));	

			array_set($data, 'categories', $this->showAttribute->withCategories($attribute->name, $attribute->val, app('veer')->siteId));
		} 
			
		$view = viewx($this->template.'.attribute', $data);

		$this->view = $view; 

		return $view;
	}

}
