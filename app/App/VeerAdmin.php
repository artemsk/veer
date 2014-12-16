<?php namespace Veer\Lib;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

class VeerAdmin {

	
	protected $action_performed = array();
	
	
	public function __construct()
	{
	}
	
	
	/**
	 * Show Sites
	 */
	public function showSites() 
	{	
		return \Veer\Models\Site::orderBy('manual_sort','asc')->get()->load('subsites', 'categories', 'components', 'configuration', 
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
						$this->action_performed[] = "DELETE trashed";
					}				
				}
			}
		}
		return $items;
	}	
	
	
	/**
	 * Update Sites
	 * @return void
	 */
	public function updateSites()
	{
		$data = Input::get('site');
		$turnoff = Input::get('turnoff', null);
		$turnon = Input::get('turnon', null);
		$message = \Lang::get('veeradmin.sites.update');

		foreach ($data as $key => $values) {

			$values['url'] = trim($values['url']);

			if (empty($values['url'])) {
				continue;
			}

			$site = \Veer\Models\Site::firstOrNew(array("id" => trim($key)));

			if (app('veer')->siteId != $key) {
				$site->url = $values['url'];
			}
			
			$site->parent_id = empty($values['parent_id']) ? 0 : $values['parent_id'];
			$site->manual_sort = empty($values['manual_sort']) ? 0 : $values['manual_sort'];
			
			if (app('veer')->siteId != $key) {
				$site->redirect_on = empty($values['redirect_on']) ? null : true;
				$site->redirect_url = empty($values['redirect_url']) ? '' : $values['redirect_url'];
			}

			$site->on_off = isset($site->on_off) ? $site->on_off : false;
			
			if ($key == $turnoff && app('veer')->siteId != $key) {
				$site->on_off = false;
				$message.= \Lang::get('veeradmin.sites.down', array('site_id' => $site->id));
			}
			
			if ($key == $turnon) {
				$site->on_off = true;
				$message.= \Lang::get('veeradmin.sites.up', array('site_id' => $site->id));
			}

			if (!isset($site->id)) {
				$message.= \Lang::get('veeradmin.sites.new');
				$this->action_performed[] = "NEW site";
			}

			$site->save();
			$this->action_performed[] = "UPDATE site";
		}

		if (app('veer')->siteId == $turnoff) {
			$message.= \Lang::get('veeradmin.sites.error');
		}

		\Illuminate\Support\Facades\Artisan::call('cache:clear');
		$this->action_performed[] = "CLEAR cache";
		
		Event::fire('veer.message.center', $message);
	}

	
	/**
	 * Update Configuration
	 * @return void | (ajax?)view
	 */
	public function updateConfiguration() 
	{
		\Eloquent::unguard();
		
		$siteid = Input::get('siteid');
		$confs = Input::get('configuration', null);		
		$new = Input::get('new', null);
		
		if(!empty($confs)) { $cardid = head(array_keys($confs)); }
		if(!empty($new)) { $cardid = $siteid; $confs = $new; }
		
		if(!empty($siteid)) { 
		
			$save = Input::get('save', null);
			$delete = Input::get('dele', null);

			if(!empty($save) && !empty($confs[$cardid]['key'])) {
				$newc = \Veer\Models\Configuration::firstOrNew(array("conf_key" => $confs[$cardid]['key'], "sites_id" => $siteid));
				$newc->sites_id = $siteid;
				$newc->conf_key = $confs[$cardid]['key'];
				$newc->conf_val = $confs[$cardid]['value'];
				$newc->save();

				$cardid = $newc->id;
				$this->action_performed[] = "UPDATE configuration";
			}

			if(!empty($delete)) {
				\Veer\Models\Configuration::destroy($cardid);
				$this->action_performed[] = "DELETE configuration";
			}

			\Illuminate\Support\Facades\Artisan::call('cache:clear');
			$this->action_performed[] = "CLEAR cache";

			// for ajax calls
			if(app('request')->ajax()) {
				$items = $this->showConfiguration($siteid, array('id','desc'));

				return view(app('veer')->template.'.lists.configuration-cards', array(
					"configuration" => $items[0]->configuration,
					"siteid" => $siteid,
				));		
			}
				 
		} else { 
			Event::fire('veer.message.center', \Lang::get('veeradmin.error.reload')); 
		}
	}		
	
	
	/**
	 * Update Components
	 * @return void | (ajax?)view
	 */
	public function updateComponents() 
	{
		\Eloquent::unguard();		
		
		$siteid = Input::get('siteid');
		$confs = Input::get('components', null);
		$new = Input::get('new', null);
		
		if(!empty($confs)) { $cardid = head(array_keys($confs)); }
		if(!empty($new)) { $cardid = $siteid; $confs = $new; }
		
		if(!empty($siteid)) { 
		
			$save = Input::get('save', null);
			$delete = Input::get('dele', null);

			// We create new insert id every time
			if(!empty($save) && !empty($confs[$cardid]['name'])) {
				$newc = \Veer\Models\Component::firstOrNew(array("route_name" => $confs[$cardid]['name'], 
					"components_type" => $confs[$cardid]['type'], "components_src" => $confs[$cardid]['src'], "sites_id" => $siteid));
				$newc->route_name = $confs[$cardid]['name'];
				$newc->components_type = $confs[$cardid]['type'];
				$newc->components_src = $confs[$cardid]['src'];
				$newc->sites_id = $siteid;
				$newc->save();

				$cardid = $newc->id;
				$this->action_performed[] = "UPDATE component";
			}

			if(!empty($delete)) {
				\Veer\Models\Component::destroy($cardid);
				$this->action_performed[] = "DELETE component";
			}

			// for ajax calls
			if(app('request')->ajax()) {
				$items = $this->showComponents($siteid, array('id','desc'));
				
				return view(app('veer')->template.'.lists.components-cards', array(
					"components" => $items[0]->components,
					"siteid" => $siteid,
				));		
			}
				 // for error
		} else { 
			Event::fire('veer.message.center', \Lang::get('veeradmin.error.reload')); }
	}		
	

	/**
	 * Update Secrets
	 * @return void
	 */	
	public function updateSecrets() 
	{
		\Eloquent::unguard();	
		
		$save = Input::get('save', null);
		$delete = Input::get('dele', null);		
		$cardid = Input::get('secrets', null);
		
		if(!empty($delete)) {
			\Veer\Models\Secret::destroy(head(array_keys($delete)));
			Event::fire('veer.message.center', \Lang::get('veeradmin.secrets.delete'));
			$this->action_performed[] = "DELETE secret";
		}		

		if(!empty($save)) {			
			$id = head(array_keys($save));
			
			foreach($cardid as $key => $value) 
			{
				if($value['elements_id'] <= 0) { 
					Event::fire('veer.message.center', \Lang::get('veeradmin.secrets.error')); continue; }
				
				if($key != "new") {
					$newc = \Veer\Models\Secret::firstOrNew(array("id" => $id));
				} else {	
					$newc = new \Veer\Models\Secret;
				}
				$newc->secret = $value['pss'];
				$newc->elements_id = $value['elements_id'];
				$newc->elements_type = $value['elements_type'];
				$newc->save();
				$cardid = $newc->id;
				Event::fire('veer.message.center', \Lang::get('veeradmin.secrets.update'));
				$this->action_performed[] = "UPDATE secret";
			}	
		}
	}	
	
	
	/**
	 * Update Jobs
	 */	
	public function updateJobs()
	{
		$run = Input::get('_run', null);
		$delete = Input::get('dele', null);
		$save = Input::get('save', null);
		$pause = Input::get('paus', null);
		
		if(!empty($delete)) {
			
			\Artemsk\Queuedb\Job::destroy(head(array_keys($delete)));
			Event::fire('veer.message.center', \Lang::get('veeradmin.jobs.delete'));
			$this->action_performed[] = "DELETE job";
		}

		if(!empty($run)) {
			
			$this->runJob( head(array_keys($run)) , Input::get('payload', null) );
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.jobs.done'));
			$this->action_performed[] = "RUN job";			
		}
		
		if(!empty($save)) {		
			
			if($this->saveJob(Input::all())) {	
				Event::fire('veer.message.center', \Lang::get('veeradmin.jobs.new'));
				$this->action_performed[] = "NEW job";
			} else {		
				Event::fire('veer.message.center', \Lang::get('veeradmin.jobs.error')); 
		}	}
		
		if(!empty($pause)) {
			
			\Artemsk\Queuedb\Job::where('id','=', head(array_keys($pause)) )
				->update(array('status' => \Artemsk\Queuedb\Job::STATUS_FINISHED));	
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.jobs.pause'));
			$this->action_performed[] = "PAUSE job";
		}		
	}
	
	
	/**
	 * save Job
	 * @param array $q
	 * @return boolean
	 */
	protected function saveJob($q)
	{
		$startc = \Carbon\Carbon::parse(array_get($q, 'jobs.new.start'));
		$repeat = array_get($q, 'jobs.new.repeat');
		$data =  (array)json_decode(array_get($q, 'jobs.new.data'), true);
		$queue = array_get($q, 'jobs.new.classname');

		if($repeat > 0) {
			$data['repeatJob'] = $repeat;
		}

		$classFullName = "\Veer\Lib\Queues\\" . $queue;

		if (!class_exists($classFullName)) { 
			//
		} else {			
			if(now() >= $startc) {
				\Queue::push( $classFullName , $data);
			} else {
				$wait = \Carbon\Carbon::now()->diffInMinutes($startc);
				\Queue::later($wait, $classFullName , $data);
			}
			return true;
		}
	}
	
	
	/**
	 * Run Job
	 * @param type $jobid
	 * @param type $payload
	 * @return void
	 */
	protected function runJob($jobid, $payload)
	{
		$item = \Artemsk\Queuedb\Job::where('id','=',$jobid)->first();	

		if(is_object($item)) {						
			$item->payload = $payload;
			$item->status = \Artemsk\Queuedb\Job::STATUS_OPEN;
			$item->scheduled_at = now();
			$item->save();

			$job = new \Artemsk\Queuedb\QdbJob(app(), $item);
			$job->fire();
		}			
	}
	
	
	/**
	 * Update Root Categories
	 */
	public function updateCategories()
	{
		// if we're working with one category then call another function
		//
		$editOneCategory = Input::get('category', null);
		if(!empty($editOneCategory)) { 
			
			return $this->updateOneCategory($editOneCategory); 
		}
		
		$action = Input::get('action', null);
		$cid = Input::get('deletecategoryid', null);
		$new = Input::get('newcategory', null);
		
		if($action == "delete" && !empty($cid)) {
			$this->action_performed[] = "DELETE category";
			$this->deleteCategory($cid);
		}
		
		if($action == "add" && !empty($new)) {			
			$site_id = Input::get('siteid', null);				
			$this->addCategory($new, $site_id);			
			$this->action_performed[] = "NEW category";
			
			if(app('request')->ajax()) {
				$items = \Veer\Models\Site::with(array('categories' => function($query) {
						$query->has('parentcategories', '<', 1)->orderBy('manual_sort', 'asc');
					}))->orderBy('manual_sort', 'asc')->where('id', '=', $site_id)->get();

				return app('view')->make(app('veer')->template.'.lists.categories-category', array(
					"categories" => $items[0]->categories,
					"siteid" => $site_id,
					"child" => view(app('veer')->template.'.elements.asset-delete-categories-script')
				));
			}	
		}
		
		if($action == "sort") {
			$sorting = Input::all();
			$sorting['relationship'] = "categories";
			
			if(isset($sorting['parentid'])) {			
				$oldsorting = $this->showCategories(null, Input::get('image',null));
				if(is_object($oldsorting)) {					
					foreach ($this->sortElements($oldsorting, $sorting) as $sort => $id) {
						\Veer\Models\Category::where('id', '=', $id)->update(array('manual_sort' => $sort));
					}	
				}			
			}	
			$this->action_performed[] = "SORT categories";
		}
	}	

	
	/**
	 * add Category
	 * @param type $title
	 * @param type $site_id
	 * @param type $options
	 * @return $->id
	 */
	public function addCategory($title, $site_id, $options = array())
	{
		$c = new \Veer\Models\Category;
		$c->title = $title;
		$c->description = array_get($options, 'description', '');
		$c->remote_url = array_get($options, 'remote_url', '');
		$c->manual_sort = array_get($options, 'sort', 999999);
		$c->views = array_get($options, 'views', 0);;
		$c->sites_id = $site_id;
		$c->save();
		return $c->id;
	}
	
	
	/**
	 * delete Category: Category & connections
	 * @param type $cid
	 * @return string
	 */
	protected function deleteCategory($cid)
	{
		\Veer\Models\Category::destroy($cid);
		\Veer\Models\CategoryConnect::where('categories_id','=',$cid)->forceDelete();
		\Veer\Models\CategoryPivot::where('parent_id','=',$cid)->orWhere('child_id','=',$cid)->forceDelete();
		\Veer\Models\ImageConnect::where('elements_id','=',$cid)
		->where('elements_type','=','Veer\Models\Category')->forceDelete();
		// We do not delete communications for deleted items
	}	
	
	
	/**
	 * Sorting Elements
	 * @param object $elements
	 * @param array $sortingParams
	 * @return type array
	 */
	public function sortElements($elements, $sortingParams)
	{
		$newsort = array();
		foreach ($elements as $s) {
			if ($s->id == $sortingParams['parentid']) {
				foreach ($s->{$sortingParams['relationship']} as $k => $c) {
					if ($sortingParams['newindex'] == $k) {
						$newsort[] = $s->{$sortingParams['relationship']}[$sortingParams['oldindex']]->id;
					}
					if ($c->id != $s->{$sortingParams['relationship']}[$sortingParams['oldindex']]->id) {
						$newsort[] = $c->id;
					}
				}
			}
		}
		return $newsort;
	}	
	
	
	/**
	 * Update One Category
	 */	
	public function updateOneCategory($cid)
	{	
		$all = Input::all();
		
		// delete sub categories from db
		// deletecategoryid <- id of deleted category id
		if($all['action'] == "delete") { 			
			$this->action_performed[] = "DELETE child category";	
			
			return $this->deleteCategory($all['deletecategoryid']); 
		}
		
		$category = \Veer\Models\Category::find($cid);
		
		
		// delete current category from db
		if($all['action'] == "deleteCurrent") {			
					
			$this->deleteCategory($cid);
			
			Input::replace(array('category' => null));
			
			$this->action_performed[] = "DELETE category";	
			Event::fire('veer.message.center', \Lang::get('veeradmin.category.delete') );	
			
			return \Redirect::route('admin.show', array('categories'));
		}
		
		
		// new & first parent of current category
		if($all['action'] == "saveParent" && isset($all['parentId']) && $all['parentId'] > 0) {
			
			$category->parentcategories()->attach($all['parentId']);
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.category.parent.new'));			
			$this->action_performed[] = "NEW parent";	
		}
		
		
		// updating parents
		if(isset($all['parentId']) && $all['action'] == "updateParent" && $all['parentId'] > 0) {
			if($all['lastCategoryId'] != $all['parentId']) {
				
				$this->attachParentCategory($cid, $all['parentId'], $category);
			}
		}
		
		
		// removing parents
		if(isset($all['parentId']) && $all['action'] == "removeParent") {
			
			$category->parentcategories()->detach($all['parentId']);
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.category.parent.detach'));			
			$this->action_performed[] = "REMOVE parents";
		}
		
		
		// updating info
		if($all['action'] == "updateCurrent") {
			$category->title = $all['title'];
			$category->remote_url = $all['remoteUrl'];
			$category->description = $all['description'];
			$category->save();
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.category.update'));
			$this->action_performed[] = "UPDATE category";
		}
		

		// adding childs (new or existing)
		if($all['action'] == "addChild" && isset($all['child'])) {
			
			$childs = $this->parseIds($all['child']);
			
			if( $childs ) {
				foreach($childs as $child) { $category->subcategories()->attach($child); }
				Event::fire('veer.message.center', \Lang::get('veeradmin.category.child.attach'));
			} else {				
				$category->subcategories()->attach(
					$this->addCategory($all['child'], $category->site->id)
				);
				Event::fire('veer.message.center', \Lang::get('veeradmin.category.child.new'));
			}
			$this->action_performed[] = "NEW child category";
		}
		
		
		// quick move child category to another parent
		if($all['action'] == "updateInChild" && isset($all['parentId']) && $all['parentId'] > 0) {
			if($all['lastCategoryId'] != $all['parentId']) {	
				
			   $check = \Veer\Models\CategoryPivot::where('child_id','=',$all['currentChildId'])
				->where('parent_id','=',$all['parentId'])->first();	
			   
			   if(!$check) {
					$category = \Veer\Models\Category::find($all['currentChildId']);
					$category->parentcategories()->detach($all['lastCategoryId']);
					$category->parentcategories()->attach($all['parentId']);
					Event::fire('veer.message.center', \Lang::get('veeradmin.category.child.parent'));
					$this->action_performed[] = "UPDATE childs";
			   }
			}
		}
		
		
		// remove child from current
		if($all['action'] == "removeInChild") {
			$category->subcategories()->detach($all['currentChildId']);
			Event::fire('veer.message.center', \Lang::get('veeradmin.category.child.detach'));
			$this->action_performed[] = "REMOVE childs";
		}
		
		
		// sort
		if($all['action'] == "sort") {
			$all['relationship'] = "subcategories";
			
			if(isset($all['parentid'])) {			
				$oldsorting[0] = $this->showCategories($all['parentid']);
				if(is_object($oldsorting[0])) {					
					foreach ($this->sortElements($oldsorting, $all) as $sort => $id) {
						\Veer\Models\Category::where('id', '=', $id)->update(array('manual_sort' => $sort));
					}					
				}				
			}
			$this->action_performed[] = "SORT childs";
		}		
			
		
		// update|add images
		if($all['action'] == "updateImages") {
			
			$images = $this->parseIds($all['attachImages']);
			
			if ($images) {
				foreach ($images as $image) {
					$category->images()->attach($image);
				}
				Event::fire('veer.message.center', \Lang::get('veeradmin.category.images.attach'));
			}

			if(Input::hasFile('uploadImage')) {

				$fname = "ct". $all['category'] . "_" . date('YmdHis',time()) .
					str_random(10) . "." . Input::file('uploadImage')->getClientOriginalExtension();
				
				Input::file('uploadImage')->move( base_path() . "/" . config('veer.images_path'), $fname);
				
				$newimage = new \Veer\Models\Image; 
				$newimage->img = $fname;
				$newimage->save();
				$newimage->categories()->attach($all['category']);
				Event::fire('veer.message.center', \Lang::get('veeradmin.category.images.new'));
			}	
			$this->action_performed[] = "NEW images";
		}

		
		$this->detachElements($all['action'], 'removeImage', $category, 'images', array(
			"action" => "REMOVE images",
			"language" => "veeradmin.category.images.detach"
		));
		
		
		// add existings products
		if($all['action'] == "updateProducts") {
			
			$products = $this->parseIds($all['attachProducts']);
			
			if ($products) {
				foreach ($products as $prd) {
					$category->products()->attach($prd);
				}
				Event::fire('veer.message.center', \Lang::get('veeradmin.category.products.attach'));
				$this->action_performed[] = "ATTACH products";
			}
		}

		
		$this->detachElements($all['action'], 'removeProduct', $category, 'products', array(
			"action" => "REMOVE products",
			"language" => "veeradmin.category.products.detach"
		));
		
		
		// add existings pages
		if($all['action'] == "updatePages") {
			
			$pages = $this->parseIds($all['attachPages']);
			
			if ($pages) {
				foreach ($pages as $pg) {
					$category->pages()->attach($pg);
				}
				Event::fire('veer.message.center', \Lang::get('veeradmin.category.pages.attach'));
				$this->action_performed[] = "ATTACH pages";
			}
		}

		$this->detachElements($all['action'], 'removePage', $category, 'pages', array(
			"action" => "REMOVE pages",
			"language" => "veeradmin.category.pages.detach"
		));				
		
		// And that's it!
	}	
	
	
	/**
	 * attach Parents
	 * @param type $cid
	 * @param type $parent_id
	 * @param type $category
	 */
	protected function attachParentCategory($cid, $parent_id, $category)
	{
		$check = \Veer\Models\CategoryPivot::where('child_id','=',$cid)
			->where('parent_id','=',$parent_id)->first();

		if(!$check) {
			$category->parentcategories()->attach($parent_id);

			Event::fire('veer.message.center', \Lang::get('veeradmin.category.parent.new'));
			$this->action_performed[] = "UPDATE parents";
		}
	}
	
	/**
	 * parse Ids
	 * @param type $ids
	 * @param type $separator
	 * @param type $start
	 * @return type
	 */
	public function parseIds($ids, $separator = ",", $start = ":")
	{		
		if(starts_with($ids, $start)) {
			return explode( $separator, substr($ids, strlen($start)) );	
		} 
	}
	
	/**
	 * detach Elements
	 * @param type $detachString
	 * @param type $type
	 * @param type $object
	 * @param type $relation
	 * @param type $message
	 */
	public function detachElements($detachString, $type, $object, $relation, $message = array()) 
	{
		if(starts_with($detachString, $type)) {
			
			$r = explode(".", $detachString);
			
			if(!empty($r[1])) { 
				
				$object->{$relation}()->detach($r[1]);	
				$this->action_performed[] = array_get($message, 'action', '');
				Event::fire('veer.message.center', \Lang::get(array_get($message, 'language', 'veeradmin.empty')));
			}
		}
	}

	
}