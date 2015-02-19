<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Veer\Services\Show\Product as ShowProduct;

class ProductController extends Controller {

	protected $showProduct;
	
	public function __construct(ShowProduct $showProduct)
	{
		parent::__construct();
		
		$this->showProduct = $showProduct;
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->showProductLists('new');
	}

	/* product lists: new, viewed, ordered */
	public function showProductLists($type)
	{
		$products = $this->showProduct->getProductLists($type, app('veer')->siteId);
			
		return $this->viewIndex('products', $products);
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int|string  $id
	 * @return Response
	 */
	public function show($id)
	{
		if(in_array($id, array('new', 'ordered', 'viewed'))) return $this->showProductLists($id);
		
		// TODO: queryParams -> sort, filter

		$product = $this->showProduct->getProduct($id, app('veer')->siteId);
		
		if(!is_object($product)) { return $this->showProductLists('new'); }
		
		$product->increment('viewed');	
		
		$product->load('images', 'tags', 'attributes', 'downloads', 'userlists');
		
		$this->showProduct->loadComments($product, 'product');
		
		$paginator_and_sorting = get_paginator_and_sorting();

		$data = array(
			"product" => $product,
			"subproducts" => $this->showProduct->withChildProducts(app('veer')->siteId, $product->id, $paginator_and_sorting),
			"parentproducts" => $this->showProduct->withParentProducts(app('veer')->siteId, $product->id, $paginator_and_sorting),
			"pages" => $this->showProduct->withPages(app('veer')->siteId, $product->id, $paginator_and_sorting),
			"categories" => $this->showProduct->withCategories(app('veer')->siteId, $product->id),
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		);
	
		$view = view($this->template.'.product', $data);

		$this->view = $view; 

		return $view;	
	}

}
