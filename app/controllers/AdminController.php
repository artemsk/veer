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
		
		$this->template = config('veer.template-admin');
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
				$image = Input::get('image', null);
				$items = $this->showCategories($category, $image);
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

			case "downloads":		
				$items = $this->showDownloads();
				$view = "downloads";
				break;
			
			case "comments":		
				$items = $this->showComments();
				$view = "comments";
				break;		
			
			case "products":		
				$image = Input::get('image', null);
				$tag = Input::get('tag', null);
				$items = $this->showProducts($image, $tag);
				$view = "products";
				break;		

			case "pages":		
				$image = Input::get('image', null);
				$tag = Input::get('tag', null);
				$items = $this->showPages($image, $tag);
				$view = "pages";
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
	protected function showCategories($category = null, $image = null) 
	{	
		if($category == null || $image != null) {
			
			if(!empty($image)) {
				$items = \Veer\Models\Site::with(array('categories' => function($query) use ($image) {
					$query->whereHas('images',function($q) use ($image) {
						$q->where('images_id','=',$image);					
					});
				}))->orderBy('manual_sort','asc')->get();	
				
				$items['filtered'] = "images";
				$items['filtered_id'] = $image;
				
				return $items;
			} 
			
				$items = \Veer\Models\Site::with(array('categories' => function($query) {
					$query->has('parentcategories', '<', 1)->orderBy('manual_sort','asc');
				}))->orderBy('manual_sort','asc')->get();
			
			
		} else {			
				$items = \Veer\Models\Category::where('id','=',$category)->with(array(
					'parentcategories' => function ($query) { $query->orderBy('manual_sort','asc'); },
					'subcategories' => function ($query) { $query->orderBy('manual_sort','asc'); }
				))->first();
				
				if(is_object($items)) {
					$items->load('products', 'pages', 'images', 'communications');

					$site = \Veer\Models\Configuration::where('sites_id','=', $items->sites_id)
						->where('conf_key','=','SITE_TITLE')->pluck('conf_val');
					$items['site_title'] = $site;
				}			
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
	 * Show Downloads
	 * @return type
	 */
	protected function showDownloads() 
	{	
		$items = \Veer\Models\Download::orderBy('fname','desc')->orderBy('id', 'desc')->with('elements')->paginate(50);	
		$items_temporary = \Veer\Models\Download::where('original','=',0)->count();
		$items_counted = \Veer\Models\Download::count(DB::raw('DISTINCT fname'));
		
		foreach($items as $key => $item) {
			$items_regrouped[$item->fname][$item->original][$key]=$key;
		}
		
		$items['temporary'] = $items_temporary;
		$items['counted'] = $items_counted;
		$items['regrouped'] = $items_regrouped;
		return $items;
	}
	
	/**
	 * Show Comments
	 * @return type
	 */
	protected function showComments() 
	{	
		$items = \Veer\Models\Comment::orderBy('id','desc')->with('elements')->paginate(50); // users -> only for user's page
		$items['counted'] = \Veer\Models\Comment::count();
		return $items;
	}
	
	/**
	 * Show Products
	 * @return type
	 */
	protected function showProducts($image = null, $tag = null) 
	{	
		if(!empty($image)) {
			$items = \Veer\Models\Product::whereHas('images', function($query) use ($image) {
				$query->where('images_id','=',$image);
			})->orderBy('id','desc')->with('images', 'categories')->paginate(25); 
			
			$items['filtered'] = "images";
			$items['filtered_id'] = $image;
			return $items;
		}
		
		if(!empty($tag)) {
			$items = \Veer\Models\Product::whereHas('tags', function($query) use ($tag) {
				$query->where('tags_id','=',$tag);
			})->orderBy('id','desc')->with('images', 'categories')->paginate(25); 
			
			$items['filtered'] = "tags";
			$items['filtered_id'] = \Veer\Models\Tag::where('id','=',$tag)->pluck('name');
			return $items;
		}
		
		$items = \Veer\Models\Product::orderBy('id','desc')->with('images', 'categories')->paginate(25); 
		$items['counted'] = \Veer\Models\Product::count();
		return $items;
	}	
	
/**
	 * Show Products
	 * @return type
	 */
	protected function showPages($image = null, $tag = null) 
	{	
		if(!empty($image)) {
			$items = \Veer\Models\Product::whereHas('images', function($query) use ($image) {
				$query->where('images_id','=',$image);
			})->orderBy('id','desc')->with('images', 'categories')->paginate(25); 
			
			$items['filtered'] = "images";
			$items['filtered_id'] = $image;
			return $items;
		}
		
		if(!empty($tag)) {
			$items = \Veer\Models\Product::whereHas('tags', function($query) use ($tag) {
				$query->where('tags_id','=',$tag);
			})->orderBy('id','desc')->with('images', 'categories')->paginate(25); 
			
			$items['filtered'] = "tags";
			$items['filtered_id'] = \Veer\Models\Tag::where('id','=',$tag)->pluck('name');
			return $items;
		}
		
		$items = \Veer\Models\Product::orderBy('id','desc')->with('images', 'categories')->paginate(25); 
		$items['counted'] = \Veer\Models\Product::count();
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
