<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Veer\Services\Show\Filter as ShowFilter;

class FilterController extends Controller {

	protected $showFilter;
	
	public function __construct(ShowFilter $showFilter)
	{
		parent::__construct();		
		
		$this->showFilter = $showFilter;
	}
	
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		return Redirect::route('index'); // TODO: configuration - set template page or redirect ( & same for search)
	}

	/**
	 * Display the specified resource.
	 */
	public function show($id)
	{
		// TODO: queryParams -> sort, filter
		// TODO: if id=0 then it will try to show everything
		
		$filtered = $this->showFilter->getFilter(app('veer')->siteId, $id);       		
		
		$view = viewx($this->template.'.filter', array(
			"products" => $filtered['products'],
			"pages" => $filtered['pages'],
			"categories" => $this->showFilter->withCategories($filtered['products'], $filtered['pages']),			
			"tags" => $this->showFilter->withTags($filtered['products'], $filtered['pages']),
			"attributes" => $this->showFilter->withAttributes($filtered['products'], $filtered['pages']),
			"data" => $this->veer->loadedComponents,
			"template" => $this->template
		)); 

		$this->view = $view; 
			
		return $view;
	}

}
