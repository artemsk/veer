<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;

class FilterController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Redirect::route('index'); // TODO: configuration - set template page or redirect ( & same for search)
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// TODO: queryParams -> sort, filter
		
		$filtered = app('veerdb')->route($id);        		

		if(!is_array($filtered)) { return Redirect::route('index'); }
		
		$paginator_and_sorting = get_paginator_and_sorting(); //?
		     
		$items = array("products" => array(0 => 0), "pages" => array(0 => 0));
		
		if(count($filtered['products'])) {
			foreach($filtered['products'] as $p) {
				$items['products'][$p->id] = $p->id;
			}
		}

		if(count($filtered['pages'])) {
			foreach($filtered['pages'] as $p) {
				$items['pages'][$p->id] = $p->id;
			}
		}
		
        $tags = \Veer\Models\Tag::whereHas('products', function($query) use($items) {
                    $query->checked()->whereIn('products.id', $items['products']);
                })->orWhereHas('pages', function($query) use($items) {
                    $query->excludeHidden()->whereIn('pages.id', $items['pages']);
                })->get(); // TODO: remember 5
				 
        $attributes = \Veer\Models\Attribute::whereHas('products', function($query) use($items) {
                    $query->checked()->whereIn('products.id', $items['products']);
                })->orWhereHas('pages', function($query) use($items) {
                    $query->excludeHidden()->whereIn('pages.id', $items['pages']);
                })->get(); // TODO: remember 5
				
		$categories = \Veer\Models\Category::whereHas('products', function($query) use($items) {
                    $query->checked()->whereIn('products.id', $items['products']);
                })->orWhereHas('pages', function($query) use($items) {
                    $query->excludeHidden()->whereIn('pages.id', $items['pages']);
                })->get();	// TODO: remember 5	
					
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.filter', array(
			"categories" => $categories,
			"products" => @$filtered['products'],
			"pages" => @$filtered['pages'],
			"tags" => $tags,
			"attributes" => $attributes,
			"data" => $data,
			"template" => $data['template']
		)); 

		$this->view = $view; 
			
		return $view;
	}

}
