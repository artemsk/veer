<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Veer\Services\Show\Tag as ShowTag;

class TagController extends Controller {

	protected $showTag;
	
	public function __construct(ShowTag $showTag)
	{
		parent::__construct();
		
		$this->showTag = $showTag;
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$tags = $this->showTag->getTagsWithSite(
			app('veer')->siteId
		);    

		return $this->viewIndex('tags', $tags);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$tag = $this->showTag->getTag($id);
		
		if(!is_object($tag)) { return Redirect::route('index'); }
		
		$paginator_and_sorting = get_paginator_and_sorting();
			
		$view = view($this->template.'.tag', array(
			"tag" => $tag,
			"products" => $this->showTag->withProducts(app('veer')->siteId, $id, $paginator_and_sorting),
			"pages" => $this->showTag->withPages(app('veer')->siteId, $id, $paginator_and_sorting),
			"attributes" => $this->showTag->withAttributes($id, app('veer')->siteId),
			"categories" => $this->showTag->withCategories($id, app('veer')->siteId),
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		));
			
		$this->view = $view; 
				
		return $view;
	}

}
