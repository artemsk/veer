<?php

class AdminController extends \BaseController {

	
	public function __construct()
	{
		parent::__construct();
		
		$this->beforeFilter('auth');
		
		$this->beforeFilter(function()
        {
			if(administrator() == false) { return Redirect::route('user.index'); } 
			
			$a = app('veer')->administrator_credentials;			
			$a['sites_encoded'] = json_decode($a['sites_watch']);

			if(!in_array(app('veer')->siteId, (array)$a['sites_encoded']) && !empty($a['sites_watch'])) {
				return Redirect::route('user.index');
			}			
			app('veer')->administrator_credentials['sites_encoded'] = $a['sites_encoded'];
			app('veer')->administrator_credentials['access_encoded'] = json_decode(app('veer')->administrator_credentials['access_parameters']);
			
			app('veer')->loadedComponents['template'] = app('veer')->template = config('veer.template-admin');			
        });
		
		$this->template = config('veer.template-admin');
	}
	

	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view(app('veer')->template.'.dashboard', array(
			"data" => app('veer')->loadedComponents,
			"template" => app('veer')->template
		));	
		
	}

	
	

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	
	

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $t
	 * @return Response
	 */
	public function show($t)
	{
		$i = app('veeradmin');
		
		$json = Input::get('json',false);
		// TODO: ?
		
		switch ($t) {
			case "sites":				
				$items = app('veeradmin')->showSites();	$view = "sites";
				break;
			
			case "attributes":
				$items = app('veeradmin')->showAttributes(); $view = "attributes";
				break;
			
			case "tags":		
				$items = app('veeradmin')->showTags(); $view = "tags";
				break;
			
			case "downloads":		
				$items = app('veeradmin')->showDownloads(); $view = "downloads";
				break;			

			case "comments":		
				$items = app('veeradmin')->showComments(); $view = "comments";
				break;			
			
			case "images":		
				$items = app('veeradmin')->showImages(); $view = "images";
				break;			
			
			case "categories":		
				$category = Input::get('category', null);
				$image = Input::get('image', null);
				$items = app('veeradmin')->showCategories($category, $image);
				$view = empty($category) ? "categories" : "category";
				break;

			case "products":		
				$image = Input::get('image', null);
				$tag = Input::get('tag', null);
				$product = Input::get('id', null);
				
				$items = app('veeradmin')->showProducts($image, $tag, $product);
				
					if(is_object($items)) {
						$items->fromCategory = Input::get('category', null); 
					}
				
				$view = empty($product) ? "products" : "product";
				break;		

			case "pages":		
				$image = Input::get('image', null);
				$tag = Input::get('tag', null);
				$page = Input::get('id', null);
				
				$items = app('veeradmin')->showPages($image, $tag, $page);

					if(is_object($items)) {
						$items->fromCategory = Input::get('category', null); 
					}
								
				$view = empty($page) ? "pages" : "page";
				break;				
				
			case "configuration":	
				$items = app('veeradmin')->showConfiguration(Input::get('site', null));
				$view = "configuration";
				break;				
			
			case "components":	
				$items = app('veeradmin')->showComponents(Input::get('site', null));
				$view = "components";
				break;	
			
			case "secrets":	
				$items = app('veeradmin')->showSecrets();
				$view = "secrets";
				break;	
			
			case "jobs":	
				$items = app('veeradmin')->showJobs();
				$view = "jobs";
				break;	
			
			case "etc":	
				$items = app('veeradmin')->showEtc();
				$view = "etc";
				break;	
			
			default:
				break;
		}

		if(isset($items) && isset($view)) {
			return view(app('veer')->template.'.'.$view, array(
				"items" => $items,
				"data" => app('veer')->loadedComponents,
				"template" => app('veer')->template
			));
		}

	}


	
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	
	

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $t
	 * @return Response
	 */
	public function update($t)
	{
		$f = "update".strtoupper($t[0]).substr($t,1);
		
		$data = app('veeradmin')->{$f}();
		
		if(!app('request')->ajax() && !(app('veeradmin')->skipShow)) {
			
			return $this->show($t);
		} else {
			
			return $data;
		}
	}

	
	

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
