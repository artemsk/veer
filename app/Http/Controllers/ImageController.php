<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Veer\Services\Show\Image as ShowImage;

class ImageController extends Controller {

	protected $showImage;
	
	public function __construct(ShowImage $showImage)
	{
		parent::__construct();
		
		$this->showImage = $showImage;
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$image = $this->showImage->getImageWithSite(app('veer')->siteId, $id);
			
		if(!is_object($image)) { return Redirect::route('index'); }
		
		$paginator_and_sorting = get_paginator_and_sorting();
		
		$view = view($this->template.'.category', array(
			"image" => $image,
			"products" => $this->showImage->withProducts(app('veer')->siteId, $id, $paginator_and_sorting),
			"pages" => $this->showImage->withPages(app('veer')->siteId, $id, $paginator_and_sorting),
			"categories" => $this->showImage->withCategories(app('veer')->siteId, $id),
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		)); 

		$this->view = $view; 

		return $view;
	}

}
