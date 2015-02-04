<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class TagController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$tags= app('veerdb')->route();   
				
		if(!is_object($tags)) { return Redirect::route('index'); }
		
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.tags', array(
			"tags" => $tags,
			"data" => $data,
			"template" => $data['template']
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

		$tag = $vdb->route($id);   
		
		if(!is_object($tag)) { return Redirect::route('index'); }
		
		$data = $this->veer->loadedComponents;     
			
		$paginator_and_sorting = get_paginator_and_sorting();

		$products = $vdb->tagOnlyProductsQuery($this->veer->siteId, $id, $paginator_and_sorting);

		$pages = $vdb->tagOnlyPagesQuery($this->veer->siteId, $id, $paginator_and_sorting);
				
		$siteId = $this->veer->siteId;
			
		$attributes = \Veer\Models\Attribute::whereHas('products', function($query) use($id, $siteId) {
				$query->siteValidation($siteId)->checked()->whereHas('tags', function($q) use ($id) {
						$q->where('tags_id','=',$id);
				});
			})->orWhereHas('pages', function($query) use($id, $siteId) {
				$query->siteValidation($siteId)->excludeHidden()->whereHas('tags', function($q) use ($id) {
						$q->where('tags_id','=',$id);
				});
			})->get(); // TODO: remember 5		 
				
		$categories = \Veer\Models\Category::whereHas('products', function($query) use($id, $siteId) {
				$query->siteValidation($siteId)->checked()->whereHas('tags', function($q) use ($id) {
						$q->where('tags_id','=',$id);
				});
			})->orWhereHas('pages', function($query) use($id, $siteId) {
				$query->siteValidation($siteId)->excludeHidden()->whereHas('tags', function($q) use ($id) {
						$q->where('tags_id','=',$id);
				});
			})->get();	 // TODO: remember 5
				
		$view = view($this->template.'.tag', array(
			"tag" => $tag,
			"products" => $products,
			"pages" => $pages,
			"attributes" => $attributes,
			"categories" => $categories,
			"data" => $data,
			"template" => $data['template']
		));
			
		$this->view = $view; 
				
		return $view;
	}

}
