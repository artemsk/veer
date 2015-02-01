<?php

class AdminController extends \BaseController {

	public function __construct()
	{
		parent::__construct();
		
		$this->beforeFilter('auth.basic');
		
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
		
		// inject? 
		$this->template = config('veer.template-admin');
		
		app('veer')->isSiteFiltered = false;
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
	 * @param  int  $t
	 * @return Response
	 */
	public function show($t)
	{
		$json = Input::get('json',false); // TODO: ?
		
		if(Input::has('SearchField')) 
		{ 
			$search = app('veeradmin')->search($t);
		
			if(is_object($search)) { return $search; }
		}
		
		switch ($t) {
			case "attributes":
				$items = ( new \Veer\Architecture\showAttribute )->getUngroupedAttributes();
				$view = $t;
				break;
			
			case "categories":		
				$category = Input::get('category');
				$image = Input::get('image');
				$items = app('veeradmin')->showCategories($category, $image);
				$view = empty($category) ? "categories" : "category";
				break;

			case "products":		
				$product = Input::get('id');
				
				$items = app('veeradmin')->showProducts($product, array(
					Input::get('filter') =>  Input::get('filter_id'),
				));
				
				if(is_object($items)) {
					$items->fromCategory = Input::get('category'); 
				}
				
				$view = empty($product) ? "products" : "product";
				break;		

			case "pages":		
				$page = Input::get('id');
				
				$items = app('veeradmin')->showPages($page, array(
					Input::get('filter') =>  Input::get('filter_id'),
				));

				if(is_object($items)) {
					$items->fromCategory = Input::get('category'); 
				}
								
				$view = empty($page) ? "pages" : "page";
				break;				
				
			case "configuration":	
				$items = app('veeradmin')->showConfiguration(Input::get('site'));
				$view = "configuration";
				break;				
			
			case "components":	
				$items = app('veeradmin')->showComponents(Input::get('site'));
				$view = "components";
				break;	
			
			case "lists":
				$items = app('veeradmin')->showLists(array(
					Input::get('filter') =>  Input::get('filter_id'),
				));
				$view = "userlists";
				break;		
			
			case "users":
				$user = Input::get('id');
				$items = app('veeradmin')->showUsers($user, array(
					Input::get('filter') =>  Input::get('filter_id'),
				));
				$view = empty($user) ? "users" : "user";
				break;	
			
			case "orders":
				$order = Input::get('id');
				$items = app('veeradmin')->showOrders($order, array(
					Input::get('filter') =>  Input::get('filter_id'),
				));
				$view = empty($order) ? "orders" : "order";
				break;	
			
			case "communications":
				$items = app('veeradmin')->showCommunications(array(
					Input::get('filter') =>  Input::get('filter_id'),
				));				
				app('veer')->setUnreadTimestamp('communications');				
				$view = "communications";
				break;
			
			case "comments":
				$items = app('veeradmin')->showComments(array(
					Input::get('filter') =>  Input::get('filter_id'),
				));				
				app('veer')->setUnreadTimestamp('comments');				
				$view = "comments";
				break;
			
			case "restore":
				app('veeradmin')->restore(Input::get('type'), Input::get('id'));
				return back();
				break;
			
			default:
				$items = app('veeradmin')->{'show' . strtoupper($t[0]) . substr($t, 1)}(array(
					Input::get('filter') =>  Input::get('filter_id'),
				));
				$view = $t;
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
	public function edit($id) {}

	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $t
	 * @return Response
	 */
	public function update($t)
	{
		if(Input::has('SearchField')) return $this->show($t);

		$f = "update".strtoupper($t[0]).substr($t,1);
		
		$data = app('veeradmin')->{$f}();
		
		if(!app('request')->ajax() && !(app('veeradmin')->skipShow)) 
		{	
			return $this->show($t);
		} 
			
		return $data;
	}

	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id) {}


}
