<?php namespace Veer\Lib;

use Illuminate\Support\Facades\Input;

class VeerAdmin {

	
	public function __construct()
	{
	}
	
	
	/**
	 * Show Sites
	 */
	public function showSites() 
	{	
		return \Veer\Models\Site::orderBy('id','asc')->get()->load('subsites', 'categories', 'components', 'configuration', 
					'users', 'discounts', 'userlists', 'orders', 'delivery', 'payment', 'communications', 
					'roles', 'parentsite'); // elements separately		
	}
	

	/**
	 * Show Attributes
	 */
	public function showAttributes() 
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
	 * Show Tags
	 */
	public function showTags() 
	{	
		$items = \Veer\Models\Tag::orderBy('name', 'asc')->with('pages', 'products')->paginate(50);	
		
		$items['counted'] = \Veer\Models\Tag::count();

		return $items;
	}	
	
	
	/**
	 * Show Downloads
	 */
	public function showDownloads() 
	{	
		$items = \Veer\Models\Download::orderBy('fname','desc')->orderBy('id', 'desc')->with('elements')->paginate(50);	
		$items_temporary = \Veer\Models\Download::where('original','=',0)->count();
		$items_counted = \Veer\Models\Download::count(\Illuminate\Support\Facades\DB::raw('DISTINCT fname'));
		
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
	 */
	public function showComments() 
	{	
		$items = \Veer\Models\Comment::orderBy('id','desc')->with('elements')->paginate(50); // users -> only for user's page
		$items['counted'] = \Veer\Models\Comment::count();
		return $items;
	}	
	

	/**
	 * Show Images
	 */
	public function showImages() 
	{	
		$items = \Veer\Models\Image::orderBy('id', 'desc')->with('pages', 'products', 'categories')->paginate(25);	
		
		$items['counted'] = \Veer\Models\Image::count();

		return $items;
	}	
	
	
	/**
	 * Show Categories
	 */
	public function showCategories($category = null, $image = null) 
	{	
		if($category == null || $image != null) {
			$items = $this->showManyCategories($image);
			
		} else {			
			$items = $this->showOneCategory($category);
		}
		
		return $items;
	}		
	
	
	/**
	 * show One Category
	 */
	public function showOneCategory($category) 
	{
		$items = \Veer\Models\Category::where('id','=',$category)->with(array(
			'parentcategories' => function ($query) { $query->orderBy('manual_sort','asc'); },
			'subcategories' => function ($query) { $query->orderBy('manual_sort','asc')
				->with('pages', 'products', 'subcategories'); }
		))->first();

		if(is_object($items)) {
			$items->load('products', 'pages', 'images', 'communications');

			$items->pages->sortBy('manual_order');

			$site = \Veer\Models\Configuration::where('sites_id','=', $items->sites_id)
				->where('conf_key','=','SITE_TITLE')->pluck('conf_val');
			$items['site_title'] = $site;
		}	
		return $items;
	}
	
	
	/**
	 * show Many Categories
	 * @params filter
	 */
	public function showManyCategories($image)
	{
		if(!empty($image)) {
			$items = \Veer\Models\Site::with(array('categories' => function($query) use ($image) {
				$query->whereHas('images',function($q) use ($image) {
					$q->where('images_id','=',$image);					
				})->with('products', 'pages', 'subcategories');
			}))->orderBy('manual_sort','asc')->get();	

			$items['filtered'] = "images";
			$items['filtered_id'] = $image;

			return $items;
		} 

		return \Veer\Models\Site::with(array('categories' => function($query) {
				$query->has('parentcategories', '<', 1)->orderBy('manual_sort','asc')
				->with('pages', 'products', 'subcategories');
			}))->orderBy('manual_sort','asc')->get();
	}
	
	
	/**
	 * Show Products
	 */
	public function showProducts($image = null, $tag = null, $product = null) 
	{	
		if(!empty($image)) {
			$items = $this->showProductsFiltered('images', $image);			
			$items['filtered'] = "images";
			$items['filtered_id'] = $image;
			return $items;
		}
		
		if(!empty($tag)) {
			$items = $this->showProductsFiltered('tags', $tag);
			$items['filtered'] = "tags";
			$items['filtered_id'] = \Veer\Models\Tag::where('id','=',$tag)->pluck('name');
			return $items;
		}
		
		if(!empty($product)) { 			
			if($product == "new") { return new stdClass(); }
			return $this->showOneProduct($product);
		}
		
		$items = \Veer\Models\Product::orderBy('id','desc')->with('images', 'categories')->paginate(25); 
		$items['counted'] = \Veer\Models\Product::count();
		return $items;
	}	
	
	
	/**
	 * show products with filter
	 * @param type [image, tag]
	 */
	public function showProductsFiltered($type, $filter_id) 
	{		
		return \Veer\Models\Product::whereHas($type, function($query) use ($filter_id, $type) {
				$query->where( $type . '_id', '=', $filter_id);
			})->orderBy('id','desc')->with('images', 'categories')->paginate(25); 		
	}
	
	/**
	 * show One Product
	 * @param type $product
	 * @return type
	 */
	public function showOneProduct($product)
	{
		$items = \Veer\Models\Product::find($product);
			
		if(is_object($items)) {
			$items->load('subproducts', 'parentproducts', 'pages', 'categories', 'tags',
				'attributes', 'images', 'downloads');		

			$items['basket'] = $items->userlists()->where('name','=','[basket]')->get();
			$items['lists'] = $items->userlists()->where('name','!=','[basket]')->get();	
		}	
			
		return $items;
	}
	
	
	/**
	 * Show Pages
	 */
	public function showPages($image = null, $tag = null, $page = null) 
	{	
		if(!empty($image)) {
			$items = $this->showPagesFiltered('images', $image);			
			$items['filtered'] = "images";
			$items['filtered_id'] = $image;
			return $items;
		}
		
		if(!empty($tag)) {
			$items = $this->showPagesFiltered('tags', $tag);
			$items['filtered'] = "tags";
			$items['filtered_id'] = \Veer\Models\Tag::where('id','=',$tag)->pluck('name');
			return $items;
		}
		
		if(!empty($page)) {			
			if($page == "new") { return new stdClass(); }
			return $this->showOnePage($page);
		}
		
		$items = \Veer\Models\Page::orderBy('id','desc')->with('images', 'categories', 'user', 'subpages', 'comments')->paginate(25); 
		$items['counted'] = \Veer\Models\Page::count();
		return $items;
	}
	
	
	/**
	 * show pages with filter
	 * @param type $type
	 * @param type $filter_id
	 * @return type
	 */
	public function showPagesFiltered($type, $filter_id)
	{
		return \Veer\Models\Page::whereHas($type, function($query) use ($filter_id, $type) {
				$query->where( $type . '_id', '=', $filter_id );
			})->orderBy('id','desc')->with('images', 'categories', 'user', 'subpages', 'comments')->paginate(25);			
	}
	
	
	/**
	 * show one page
	 * @param type $page
	 * @return type
	 */
	public function showOnePage($page) 
	{
		$items = \Veer\Models\Page::find($page);
			
		if(is_object($items)) {
			$items->load('user', 'subpages', 'parentpages', 'products', 'categories', 'tags', 'attributes',
					'images', 'downloads');	
			$items['lists'] = $items->userlists()->count(\Illuminate\Support\Facades\DB::raw('DISTINCT name'));
		}	
			
		return $items;
	}
	
	
	/**
	 * Show Configurations
	 */
	public function showConfiguration($siteId = null, $orderBy = array('id', 'desc')) 
	{	
		if(Input::get('sort', null)) { $orderBy[0] = Input::get('sort'); }
		if(Input::get('direction', null)) { $orderBy[1] = Input::get('direction'); }
		
		if($siteId == null) {
			return \Veer\Models\Site::where('id','>',0)->with(array('configuration' => function($query) use ($orderBy) {
				$query->orderBy($orderBy[0], $orderBy[1]);
			}))->get();
		}
		
		$items[0] = \Veer\Models\Site::with(array('configuration' => function($query) use ($orderBy) {
				$query->orderBy($orderBy[0], $orderBy[1]);
			}))->find($siteId); 
		return $items;
	}
	
	
	/**
	 * Show Components
	 */
	public function showComponents($siteId = null, $orderBy = array('id', 'desc')) 
	{	
		if(Input::get('sort', null)) { $orderBy[0] = Input::get('sort'); }
		if(Input::get('direction', null)) { $orderBy[1] = Input::get('direction'); }
	
		if($siteId == null) {
			return \Veer\Models\Site::where('id','>',0)->with(array('components' => function($query) use ($orderBy) {
				$query->orderBy('sites_id')->orderBy($orderBy[0], $orderBy[1]);
			}))->get();
		}
		
		$items = \Veer\Models\Site::with(array('components' => function($query) use ($orderBy) {
				$query->orderBy('sites_id')->orderBy($orderBy[0], $orderBy[1]);
			}))->find($siteId); 
			
		return array($items);
	}	
	
	
	/**
	 * Show Secrets
	 */
	public function showSecrets() 
	{		
		$items = \Veer\Models\Secret::all();
		$items->sortByDesc('created_at');
			
		return $items;
	}	
	
	
	/**
	 * Show Jobs
	 */
	public function showJobs() 
	{		
		$items = \Artemsk\Queuedb\Job::all();
		$items->sortBy('scheduled_at');
		
		$items_failed = \Illuminate\Support\Facades\DB::table("failed_jobs")->get();
		
		$statuses = array(\Artemsk\Queuedb\Job::STATUS_OPEN => "Open",
						  \Artemsk\Queuedb\Job::STATUS_WAITING => "Waiting",
					\Artemsk\Queuedb\Job::STATUS_STARTED => "Started",
					\Artemsk\Queuedb\Job::STATUS_FINISHED => "Finished",
					\Artemsk\Queuedb\Job::STATUS_FAILED => "Failed");
			
		return array('jobs' => $items, 'failed' => $items_failed, 'statuses' => $statuses);
	}	
	
	
	/**
	 * Show Etc.
	 */
	public function showEtc() 
	{		
		$cache = \Illuminate\Support\Facades\DB::table("cache")->get();
		$migrations = \Illuminate\Support\Facades\DB::table("migrations")->get();
		$reminders = \Illuminate\Support\Facades\DB::table("password_reminders")->get();	

		if(config('database.default') == 'mysql') {
			$trashed = $this->trashedElements(); }
		
		return array('cache' => $cache, 'migrations' => $migrations, 
			'reminders' => $reminders, 'trashed' => empty($trashed)?null:$trashed);
	}	
	
	
	/**
	 * Show trashedElements (only 'mysql')
	 * @param type $action
	 * @return type
	 */
	protected function trashedElements($action = null)
	{
		$tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
		
		foreach($tables as $table) {
			if (\Illuminate\Support\Facades\Schema::hasColumn(reset($table), 'deleted_at'))
			{
				$check = \Illuminate\Support\Facades\DB::table(reset($table))
					->whereNotNull('deleted_at')->count();
				if($check > 0) {
				$items[reset($table)] = $check;				
					if($action == "delete") {
						\Illuminate\Support\Facades\DB::table(reset($table))
							->whereNotNull('deleted_at')->delete();
					}				
				}
			}
		}
		return $items;
	}	
	
}