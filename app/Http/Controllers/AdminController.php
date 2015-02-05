<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input as Input;

class AdminController extends Controller {

	public function __construct()
	{
		parent::__construct();

		$this->middleware('auth');
		
		$this->middleware('auth.admin');
				 
		app('veer')->loadedComponents['template'] = app('veer')->template = $this->template = config('veer.template-admin');			

		app('veer')->isBoundSite = false;
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
	 */
	public function show($t)
	{		
		$json = Input::get('json',false); // TODO: ?
		
		if(Input::has('SearchField')) 
		{ 
			$search = app('veeradmin')->search($t);
		
			if(is_object($search)) { return $search; }
		}
		
		$view = $t;
		
		switch ($t) {
			case "attributes":
				$items = ( new \Veer\Services\Show\Attribute )->getUngroupedAttributes();
				break;
			
			case "tags":
				$items = ( new \Veer\Services\Show\Tag )->getTagsWithoutSite();
				break;
			
			case "downloads":
				$items = ( new \Veer\Services\Show\Download )->getDownloads();
				break;
			
			case "images":
				$items = ( new \Veer\Services\Show\Image )->getImages(array(Input::get('filter') =>  Input::get('filter_id')));
				break;
			
			case "sites":
				$items = ( new \Veer\Services\Show\Site )->getSites();
				break;
			
			case "configuration":	
				$items = ( new \Veer\Services\Show\Site )->getConfiguration(Input::get('site'));
				break;	
			
			case "components":	
				$items = ( new \Veer\Services\Show\Site )->getComponents(Input::get('site'));
				break;				
			
			case "secrets":	
				$items = ( new \Veer\Services\Show\Site )->getSecrets();
				break;	
			
			case "categories":		
				$category = Input::get('category');
				$imageFilter = Input::get('image');
				
				if(empty($category)) {
					$items = ( new \Veer\Services\Show\Category )->getAllCategories($imageFilter);	
					$view = "categories";
				} else {
					$items = ( new \Veer\Services\Show\Category )->getCategoryAdvanced($category);	
					$view = "category"; 
				}		
				break;
				
			case "pages":		
				$page = Input::get('id');
				
				if(empty($page)) {
					$items = ( new \Veer\Services\Show\Page )->getAllPages(array(
						Input::get('filter') =>  Input::get('filter_id'),
					));
					$view = "pages";
				} else {
					$items =( new \Veer\Services\Show\Page )->getPageAdvanced($page);
					$view = "page";
				}

				if(is_object($items)) {
					$items->fromCategory = Input::get('category'); 
				}
				break;	

			case "products":		
				$product = Input::get('id');
				
				if(empty($product)) {
					$items = ( new \Veer\Services\Show\Product )->getAllProducts(array(
						Input::get('filter') =>  Input::get('filter_id'),
					));
					$view = "products";
				} else {
					$items =( new \Veer\Services\Show\Product )->getProductAdvanced($product);
					$view = "product";
				}
								
				if(is_object($items)) {
					$items->fromCategory = Input::get('category'); 
				}
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
				break;
			
			case "comments":
				$items = app('veeradmin')->showComments(array(
					Input::get('filter') =>  Input::get('filter_id'),
				));				
				app('veer')->setUnreadTimestamp('comments');				
				break;
			
			case "restore":
				app('veeradmin')->restore(Input::get('type'), Input::get('id'));
				return back();
			
			default:
				$items = app('veeradmin')->{'show' . strtoupper($t[0]) . substr($t, 1)}(array(
					Input::get('filter') =>  Input::get('filter_id'),
				));
				break;
		}

		if(isset($items) && isset($view)) {
			return view($this->template.'.'.$view, array(
				"items" => $items,
				"data" => app('veer')->loadedComponents,
				"template" => $this->template
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
