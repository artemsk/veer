<?php

class AdminController extends \BaseController {

	protected $adm;
	
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
		
	}
	

	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		
		
				echo "<pre>";
		print_r(app('veer')->administrator_credentials);
		echo "</pre>";
		
		echo "<pre>";
		print_r(DB::getQueryLog());
		echo "</pre>";
		
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
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		switch ($id) {
			case "sites":				
				$items = $this->showSites();					
				$view = "sites";
				break;
			
			case "attributes":
				$items = $this->showAttributes();				
				$view = "attributes";
				break;

			case "categories":		
				$category = Input::get('category', null);
				$items = $this->showCategories($category);
				$view = empty($category) ? "categories" : "category";
				break;

			case "tags":		
				$items = $this->showTags();
				$view = "tags";
				break;			
			
			case "images":		
				$items = $this->showImages();
				$view = "images";
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
	 * Show Sites
	 * @return type
	 */
	protected function showSites() 
	{	
		return \Veer\Models\Site::all()->load('subsites', 'categories', 'components', 'configuration', 
					'users', 'discounts', 'userlists', 'orders', 'delivery', 'payment', 'communications', 
					'roles', 'parentsite'); // elements separately		
	}
	
	/**
	 * Show Attributes
	 * @return type
	 */
	protected function showAttributes() 
	{	
		$items = \Veer\Models\Attribute::all()->sortBy('name')->load('pages', 'products');
				
		foreach($items as $key => $item) {
			$items_grouped[$item->name][$key] = $key;
			$items_counted[$item->name]['prd'] = (isset($items_counted[$item->name]['prd']) ? 
				$items_counted[$item->name]['prd'] : 0) + ($item->products->count()); 
			$items_counted[$item->name]['pg'] = (isset($items_counted[$item->name]['pg']) ? 
				$items_counted[$item->name]['pg'] : 0) + ($item->pages->count()); 
		}

		if($items_grouped) {
			
			$items->put('grouped', $items_grouped);
			$items->put('counted', $items_counted);				
		}
		
		return $items;
	}
	
	/**
	 * Show Categories
	 * @return type
	 */
	protected function showCategories($category = null) 
	{	
		if($category == null) {
			
			$items = \Veer\Models\Site::with(array('categories' => function($query) {
				$query->has('parentcategories', '<', 1)->orderBy('manual_sort','asc');
			}))->orderBy('manual_sort','asc')->get();
			
		} else {
		 
			$items = \Veer\Models\Category::where('id','=',$category)->with(array(
				'parentcategories' => function ($query) { $query->orderBy('manual_sort','asc'); },
				'subcategories' => function ($query) { $query->orderBy('manual_sort','asc'); }
			))->first();
			
			$items->load('products', 'pages', 'images', 'communications');
			
			$site = \Veer\Models\Configuration::where('sites_id','=', $items->sites_id)
				->where('conf_key','=','SITE_TITLE')->pluck('conf_val');
			$items['site_title'] = $site;
		}
		
		return $items;
	}	
	
	/**
	 * Show Tags
	 * @return type
	 */
	protected function showTags() 
	{	
		$items = \Veer\Models\Tag::orderBy('name', 'asc')->with('pages', 'products')->paginate(50);	
		
		$items['counted'] = \Veer\Models\Tag::count();

		return $items;
	}	
	
	/**
	 * Show Images
	 * @return type
	 */
	protected function showImages() 
	{	
		$items = \Veer\Models\Image::orderBy('id', 'desc')->with('pages', 'products', 'categories')->paginate(25);	
		
		$items['counted'] = \Veer\Models\Image::count();

		return $items;
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
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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
