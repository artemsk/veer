<?php namespace Veer\Lib;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

class VeerAdmin {

	
	protected $action_performed = array();
	
	public $skipShow = false;
	
	
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
		$items = \Veer\Models\Attribute::orderBy('name')->with('pages', 'products')->paginate(100);
				
		foreach($items as $key => $item) {
			$items_grouped[$item->name][$key] = $key;
			$items_counted[$item->name]['prd'] = (isset($items_counted[$item->name]['prd']) ? 
				$items_counted[$item->name]['prd'] : 0) + ($item->products->count()); 
			$items_counted[$item->name]['pg'] = (isset($items_counted[$item->name]['pg']) ? 
				$items_counted[$item->name]['pg'] : 0) + ($item->pages->count()); 
		}

		$items['grouped'] = array();
		if(isset($items_grouped)) {
			$items['grouped'] = $items_grouped;
			$items['counted'] = $items_counted;				
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
		
		if(isset($items_regrouped)) { $i = 0;
			foreach($items_regrouped as $key => $item) { $items_index[$key] = $i; $i++; }
		}
		
		$items['temporary'] = $items_temporary;
		$items['counted'] = $items_counted;
		$items['regrouped'] = isset($items_regrouped) ? $items_regrouped : array();
		$items['index'] = isset($items_index) ? $items_index : array();
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
		if(Input::get('filter', null) == "unused") {
			$items = \Veer\Models\Image::orderBy('id', 'desc')->has('pages','<',1)->has('products','<',1)->has('categories','<',1)->paginate(25);	
		} else {
			$items = \Veer\Models\Image::orderBy('id', 'desc')->with('pages', 'products', 'categories')->paginate(25);	
		}
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
	public function showProducts($image = null, $tag = null, $attribute = null, $product = null) 
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
		
		if(!empty($attribute)) {
			$items = $this->showProductsFiltered('attributes', $attribute);
			$items['filtered'] = "attributes";
			$a = \Veer\Models\Attribute::where('id','=',$attribute)->select('name', 'val')->first();
			$items['filtered_id'] = $a->name.":".$a->val;
			return $items;
		}
		
		if(!empty($product)) { 			
			if($product == "new") { return new \stdClass(); }
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
	public function showPages($image = null, $tag = null, $attribute = null, $page = null) 
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
		
		if(!empty($attribute)) {
			$items = $this->showPagesFiltered('attributes', $attribute);
			$items['filtered'] = "attributes";
			$a = \Veer\Models\Attribute::where('id','=',$attribute)->select('name', 'val')->first();
			$items['filtered_id'] = $a->name.":".$a->val;
			return $items;
		}
		
		if(!empty($page)) {			
			if($page == "new") { return new \stdClass(); }
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
		
		$items = array();
		
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
		if(!empty($title)) {
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
			
			$this->skipShow = true;
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
			
			$childs = $this->attachElements($all['child'], $category, 'subcategories', array(
				"action" => "NEW child categories",
				"language" => "veeradmin.category.child.attach"
			));
			
			if( !$childs ) {			
				$category->subcategories()->attach(
					$this->addCategory($all['child'], $category->site->id)
				);
				Event::fire('veer.message.center', \Lang::get('veeradmin.category.child.new'));
				$this->action_performed[] = "NEW child category";
			}
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
			
			$this->attachElements($all['attachImages'], $category, 'images', array(
				"action" => "ATTACH images",
				"language" => "veeradmin.category.images.attach"
			));
			
			if(Input::hasFile('uploadImage')) {

				$this->upload('image', 'uploadImage', $all['category'], 'categories', 'ct', array(
					"action" => "NEW images",
					"language" => \Lang::get('veeradmin.category.images.new')
				));
			}	
		}

		$this->detachElements($all['action'], 'removeImage', $category, 'images', array(
			"action" => "REMOVE images",
			"language" => "veeradmin.category.images.detach"
		));
	
		
		// add existings products
		if($all['action'] == "updateProducts") {
			
			$this->attachElements($all['attachProducts'], $category, 'products', array(
				"action" => "ATTACH products",
				"language" => "veeradmin.category.products.attach"
			));
		}
		
		$this->detachElements($all['action'], 'removeProduct', $category, 'products', array(
			"action" => "REMOVE products",
			"language" => "veeradmin.category.products.detach"
		));		
		
		$this->quickProductsActions($all['action']);
		
		// add existings pages
		if($all['action'] == "updatePages") {
			
			$this->attachElements($all['attachPages'], $category, 'pages', array(
				"action" => "ATTACH pages",
				"language" => "veeradmin.category.pages.attach"
			));
		}

		$this->detachElements($all['action'], 'removePage', $category, 'pages', array(
			"action" => "REMOVE pages",
			"language" => "veeradmin.category.pages.detach"
		));				
		
		$this->quickPagesActions($all['action']);
		
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
	 * attach Elements
	 * @param string|array $ids
	 * @param type $object
	 * @param type $relation
	 * @param type $message
	 * @param type $separator
	 * @param type $start
	 */
	public function attachElements($ids, $object, $relation, $message = array(), $separator = ",", $start = ":", $replace = false)
	{
		if(!is_array($ids)) {
			$elements = $this->parseIds($ids, $separator, $start);
		} else { $elements = $ids; }

		if(isset($elements)) {
			if($replace == true) {
				$object->{$relation}()->sync($elements); 
			} else {
				$object->{$relation}()->attach($elements); 
			}
		
			if(!empty($message)) {
				$this->action_performed[] = array_get($message, 'action', '');
				Event::fire('veer.message.center', \Lang::get(array_get($message, 'language', 'veeradmin.empty')));
			}
			return true;
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
				if(!empty($message)) {
					$this->action_performed[] = array_get($message, 'action', '');
					Event::fire('veer.message.center', \Lang::get(array_get($message, 'language', 'veeradmin.empty')));
				}
			}
		}
	}

	
	/**
	 * update Products
	 * @return type
	 */
	public function updateProducts()
	{
		// if we're working with one product then call another function
		//
		$editOneProduct = Input::get('id', null);
		if(!empty($editOneProduct)) { 
			
			return $this->updateOneProduct($editOneProduct); 
		}
				
		// quick actions: status etc.
		$this->quickProductsActions(Input::get('action', null));

		$all = Input::all();
		
		$title = trim(array_get($all, 'fill.title', null));
		$freeForm = trim(array_get($all, 'freeForm', null));
		
		if(empty($freeForm) && !empty($title)) {
			
			$prices = explode(":", array_get($all, 'prices', null));
			$options =  explode(":", array_get($all, 'options', null));
			$categories =  explode(",", array_get($all, 'categories', null));
			
			$p = new \Veer\Models\Product;
			$p->title = $title;
			$p->url = array_get($all, 'fill.url', '');
			$p->price = array_get($prices, 0, 0);
			$p->price_sales = array_get($prices, 1, 0);
			$p->price_opt = array_get($prices, 2, 0);
			$p->price_base = array_get($prices, 3, 0);
			$p->currency = array_get($prices, 4, 0);
			$p->qty = array_get($options, 0, 0);
			$p->weight = array_get($options, 1, 0);
			$p->score = array_get($options, 2, 0);
			$p->star = array_get($options, 3, 0);
			$p->production_code = array_get($options, 4, '');
			$p->status = "hide";
			$p->save();
			
			if(!empty($categories)) {
				$p->categories()->attach($categories);
			}
			
			// images
			if(Input::hasFile('uploadImage')) {
				$this->upload('image', 'uploadImage', $p->id, 'products', 'prd', null);
			}

			//files
			if(Input::hasFile('uploadFile')) {
				$this->upload('file', 'uploadFile', $p->id, $p, 'prd', null);
			}		
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.new'));
			$this->action_performed[] = "NEW product";			
		}
		
		if(!empty($freeForm)) {
			
			$parseff = preg_split('/[\n\r]+/', trim($freeForm) );
			foreach($parseff as $p) {
				$items = explode("|", $p);

				$p = new \Veer\Models\Product;
				$p->title = array_get($items, 0, '');
				$p->url = array_get($items, 1, '');
				$p->qty = array_get($items, 3, 0);
				$p->weight =  array_get($items, 4, 0);
				$p->currency =  array_get($items, 5, 0);				
				$p->price =  array_get($items, 6, 0);
				$p->price_sales =  array_get($items, 7, 0);
				$p->price_opt =  array_get($items, 8, 0);
				$p->price_base =  array_get($items, 9, 0);
				$p->price_sales_on = array_get($items, 10, 0);
				$p->price_sales_off = array_get($items, 11, 0);
				$p->to_show = array_get($items, 12, 0);				
				$p->score = array_get($items, 13, 0);	
				$p->star = array_get($items, 14, 0);	
				$p->production_code = array_get($items, 17, 0);	
				$p->status = array_get($items, 18, 'hide');					
				$p->descr = substr(array_get($items, 19, ''), 2, -2);
				$p->save();
			
				$categories =  explode(",", array_get($items, 2, ''));
				if(!empty($categories)) {
					$p->categories()->attach($categories);
				}

				$image = array_get($items, 15, null);
				if(!empty($image)) {
					$new = new \Veer\Models\Image; 
					$new->img = $image;
					$new->save();
					$new->products()->attach($p->id);			
				}
				
				$file= array_get($items, 16, null);
				if(!empty($file)) {
					$new = new \Veer\Models\Download; 
					$new->original = 1;
					$new->fname= $file;
					$new->expires = 0;
					$new->expiration_day = 0;
					$new->expiration_times = 0;
					$new->downloads = 0;
					$p->downloads()->save($new);		
				}				
			}	
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.new'));
			$this->action_performed[] = "NEW product";			
		}
	}
	
	
	/**
	 * update One product
	 * @param type $id
	 */
	public function updateOneProduct($id)
	{
		\Eloquent::unguard();
		
		$all = Input::all();
		$action = array_get($all, 'action', null);
				
		array_set($all, 'fill.star', isset($all['fill']['star']) ? true : 0);
		array_set($all, 'fill.download', isset($all['fill']['download']) ? true : 0);

		$salesOn = explode("/", array_get($all, 'fill.price_sales_on', 0));
	
		$temporaryY = (int)array_get($salesOn, 2, 0);
		if($temporaryY <= 1999) { $salesOn = \Carbon\Carbon::today(); } else {
			$salesOn = \Carbon\Carbon::parse(array_get($all, 'fill.price_sales_on', 0));
		}
	
		$salesOff = explode("/", array_get($all, 'fill.price_sales_off', 0));
		
		$temporaryY = (int)array_get($salesOff, 2, 0);
		if($temporaryY <= 1999) { $salesOff = \Carbon\Carbon::today(); } else {
			$salesOff = \Carbon\Carbon::parse(array_get($all, 'fill.price_sales_off', 0));
		}		
			
		$toShow = explode("/", array_get($all, 'fill.to_show', 0));
		
		$temporaryY = (int)array_get($toShow, 2, 0);
		if($temporaryY <= 1999) { $toShow = \Carbon\Carbon::today(); } else {
			$toShow = \Carbon\Carbon::parse(array_get($all, 'fill.to_show', 0));
		}	
		
		array_set($all, 'fill.price_sales_on', $salesOn);

		array_set($all, 'fill.price_sales_off', $salesOff);
		
		$toShow->hour((int)array_get($all, (int)'to_show_hour', 0));
		$toShow->minute((int)array_get($all, (int)'to_show_minute', 0));
		
		array_set($all, 'fill.to_show', $toShow);
			
		if($action == "add" || $action == "saveAs") {
			
			$product = new \Veer\Models\Product;
			$product->fill($all['fill']);
			$product->status = "hide";
			$product->save();
			
			$id = $product->id;
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.new'));
			$this->action_performed[] = "NEW product";
		} else {
			$product = \Veer\Models\Product::find($id);
		}
	
		if($action == "update") {
			$product->fill($all['fill']);
			$product->save();
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.update'));
			$this->action_performed[] = "UPDATE product";			
		}
		
		//status
		if($action == "updateStatus.".$id) 
		{
			$this->changeProductStatus($product);
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.status'));
			$this->action_performed[] = "UPDATE product status";
		}
		
		$this->connections($product, $id, 'products', array(
			"actionButton" => $action,
			"tags" => $all['tags'],
			"attributes" => $all['attribute'],
			"attachImages" => $all['attachImages'],
			"attachFiles" => $all['attachFiles'],
			"attachCategories" => $all['attachCategories'],
			"attachPages" => $all['attachPages'],
			"attachChildProducts" => $all['attachChildProducts'],
			"attachParentProducts" => $all['attachParentProducts']
		), array(
			"prefix" => array("image" => "prd", "file" => "prd")
		));		
			
		// freeform
		if(!empty($all['freeForm'])) {
			$ff = preg_split('/[\n\r]+/', trim($all['freeForm']) );
			foreach ($ff as $freeForm) {
				if(starts_with($freeForm, 'Tag:')) {
					$this->attachElements($freeForm, $product, 'tags', null, ",", "Tag:");
				} else {
					$this->attachElements($freeForm, $product, 'attributes', null, ",", "Attribute:");
				}
			}
		}
		
		if($action == "add" || $action == "saveAs") {
			$this->skipShow = true;
			Input::replace(array('id' => $id));
			return \Redirect::route('admin.show', array('products', 'id' => $id));
		}
	}
	
	
	/**
	 * attach Tags
	 */
	public function attachTags($tags, $object)
	{
		\Eloquent::unguard();
		
		$tagArr = array();
		
		$t = preg_split('/[\n\r]+/', trim($tags) );

		if(is_array($t)) {
			foreach($t as $tag)
			{
				if(empty($tag)) { continue; }

				$tagDb = \Veer\Models\Tag::firstOrNew(array('name' => $tag));
				if(!$tagDb->exists) {
					$tagDb->name = $tag;
					$tagDb->save();
				}
				$tagArr[] = $tagDb->id;
			}
			$this->attachElements($tagArr, $object, 'tags', null, ",", ":", true);
		}
	}
	
	
	/**
	 * attach Attributes
	 * @param type $attributes
	 * @param type $id
	 * @param type $object
	 */
	public function attachAttributes($attributes, $object)
	{
		if(is_array($attributes)) {
			
			\Eloquent::unguard();

			$attrArr = array();
			
			foreach($attributes as $a)
			{
				if(empty($a['name'])) { continue; }
				
				$attr = \Veer\Models\Attribute::firstOrNew(array(
					"name" => $a['name'], 
					"val" => $a['val'], 
					"type" => array_get($a, 'type', 'descr')));
				
				if(!$attr->exists) {
					$attr->type = array_get($a, 'type', 'descr');
					$attr->name = $a['name'];
					$attr->val = $a['val'];
					$attr->descr = $a['descr'];
					$attr->save();
				}
				$attrArr[$attr->id] = array("product_new_price" => array_get($a, 'price', ''));	
			}

			$this->attachElements($attrArr, $object, 'attributes', null, ",", ":", true);
		}
	}
	
	
	/**
	 * upload Image
	 * @param type $file
	 * @param type $id
	 * @param type $relation|$object
	 * @param type $prefix
	 * @param type $message
	 */
	public function upload($type, $file, $id, $relationOrObject, $prefix = null, $message = null, $skipRelation = false) 
	{
		$newId = null;
		$fname = $prefix. $id. "_" . date('YmdHis',time()) .
			str_random(10) . "." . Input::file($file)->getClientOriginalExtension();

		if($type == "image") {
			Input::file($file)->move( base_path() . "/" . config('veer.images_path'), $fname);
			$new = new \Veer\Models\Image; 
			$new->img = $fname;
			$new->save();
			if(!$skipRelation) { $new->{$relationOrObject}()->attach($id); }
			$newId = $new->id;
		} 
		
		if($type == "file") {
			Input::file($file)->move( base_path() . "/" . config('veer.downloads_path'), $fname);
			$new = new \Veer\Models\Download; 
			$new->original = 1;
			$new->fname= $fname;
			$new->expires = 0;
			$new->expiration_day = 0;
			$new->expiration_times = 0;
			$new->downloads = 0;
			if(!$skipRelation) { $relationOrObject->downloads()->save($new); } else {
				$new->save();
			}
			$newId = $new->id;
		}
				
		if(!empty($message)) {
			Event::fire('veer.message.center', $message['language']);
			$this->action_performed[] = $message['action'];
		}
		return $newId;
	}
	
	
	/**
	 * copy files to new object
	 * @param type $files
	 * @param type $object
	 */
	public function copyFiles($files, $object) 
	{
		$filesDb = $this->parseIds($files);
		
		if(is_array($filesDb)) {
			foreach($filesDb as $file) 
			{
				$fileModel = \Veer\Models\Download::find($file);
				if(is_object($fileModel)) {
					$newfile = $fileModel->replicate();
					$object->downloads()->save($newfile);
				}
			}
		}	
	}
	
	
	/**
	 * remove file
	 * @param type $removeFile
	 */
	public function removeFile($removeFile)
	{
		if(starts_with($removeFile, 'removeFile')) {
			$r = explode(".", $removeFile);
			if(!empty($r[1])) { 
				\Veer\Models\Download::where('id','=',$r[1])->update(array('elements_id' => null, 'elements_type' => ''));
			}
		}
	}
	
	
	/**
	 * change product status
	 */
	public function changeProductStatus($product)
	{
		if(is_object($product)) {
			switch ($product->status) {
				case "hide":
					$product->status = "buy";
					break;
				case "sold":
					$product->status = "hide";
					break;
				default:
					$product->status = "sold";
					break;
			}	
			$product->save();
		}
	}
	
	
	/**
	 * Connections
	 * @param type $object
	 * @param type $id
	 * @param type $type
	 * @param type $attributes
	 * @param type $options
	 */
	public function connections($object, $id, $type, $attributes = array(), $options = array())
	{
		$action = array_get($attributes, 'actionButton', null);
		
		// tags
		$this->attachTags(array_get($attributes, 'tags', null), $object);
		
		// attributes
		$this->attachAttributes(array_get($attributes, 'attributes', null), $object);

		// images
		if(Input::hasFile(array_get($attributes, 'uploadImageId', 'uploadImage'))) {
			$this->upload('image', array_get($attributes, 'uploadImageId', 'uploadImage'), 
				$id, $type, array_get($options, 'prefix.image', null), null);
		}			
		
		$this->attachElements(array_get($attributes, 'attachImages', null), $object, 'images', null);
		
		$this->detachElements($action, 
			array_get($attributes, 'removeImageId', 'removeImage'), $object, 'images', 
			array_get($options, 'message.images', null));
	
		//files
		if(Input::hasFile(array_get($attributes, 'uploadFilesId', 'uploadFiles'))) {
			$this->upload('file', array_get($attributes, 'uploadFilesId', 'uploadFiles'), 
				$id, $object, array_get($options, 'prefix.file', null), null);
		}
		
		$this->copyFiles(array_get($attributes, 'attachFiles', null), $object);		
		$this->removeFile($action);
		
		// categories: we cannot add not existing categories as we don't know site id
		$this->attachElements(array_get($attributes, 'attachCategories', null), $object, 'categories', null);			
		$this->detachElements($action, array_get($attributes, 'removeCategoryId', 'removeCategory'), $object, 'categories', null);
				
		// pages
		$this->attachElements(array_get($attributes, 'attachPages', null), $object, 'pages', null);	
		$this->detachElements($action, array_get($attributes, 'removePageId', 'removePage'), $object, 'pages', null);	
		
		// products
		$this->attachElements(array_get($attributes, 'attachProducts', null), $object, 'products', null);	
		$this->detachElements($action, array_get($attributes, 'removePageId', 'removeProduct'), $object, 'products', null);			
		
		// child products
		$this->attachElements(array_get($attributes, 'attachChildProducts', null), $object, 'subproducts', null);	
		$this->detachElements($action, array_get($attributes, 'removeChildProductId', 'removeChildProduct'), $object, 'subproducts', null);
		
		// parent products
		$this->attachElements(array_get($attributes, 'attachParentProducts', null), $object, 'parentproducts', null);	
		$this->detachElements($action, array_get($attributes, 'removeParentProductId', 'removeParentProduct'), $object, 'parentproducts', null);
		
		// child pages
		$this->attachElements(array_get($attributes, 'attachChildPages', null), $object, 'subpages', null);	
		$this->detachElements($action, array_get($attributes, 'removeChildProductId', 'removeChildPage'), $object, 'subpages', null);
		
		// parent pages
		$this->attachElements(array_get($attributes, 'attachParentPages', null), $object, 'parentpages', null);	
		$this->detachElements($action, array_get($attributes, 'removeParentProductId', 'removeParentPage'), $object, 'parentpages', null);		
	}
	
	
	/**
	 * update Pages
	 */
	public function updatePages()
	{
		// if we're working with one page then call another function
		//
		$editOnePage = Input::get('id', null);
		if(!empty($editOnePage)) { 
			
			return $this->updateOnePage($editOnePage); 
		}
		
		//quick actions
		$this->quickPagesActions(Input::get('action', null));
	
		$all = Input::all();
		
		$title = trim(array_get($all, 'title', null));

		if(!empty($title)) {
			
			$categories =  explode(",", array_get($all, 'categories', null));
			
			$p = new \Veer\Models\Page;
			$p->title = $title;
			$p->url = array_get($all, 'url', '');
			$p->hidden = 1;
			$p->manual_order = 999999;
			$p->users_id = \Auth::id();
			
			$txt= preg_replace("/{{(?s).*}}/", "", array_get($all, 'txt', ''), 1);
			$result = preg_match("/{{(?s).*}}/", array_get($all, 'txt', ''), $small);
			
			$p->small_txt = substr(trim( array_get($small, 0, '') ), 2, -2);
			$p->txt = trim( $txt );
			$p->save();
					
			if(!empty($categories)) {
				$p->categories()->attach($categories);
			}
			
			// images
			if(Input::hasFile('uploadImage')) {
				$this->upload('image', 'uploadImage', $p->id, 'pages', 'pg', null);
			}

			//files
			if(Input::hasFile('uploadFile')) {
				$this->upload('file', 'uploadFile', $p->id, $p, 'pg', null);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.new'));
			$this->action_performed[] = "NEW page";			
		}
	}

	
	/**
	 * update One Page
	 * @param type $id
	 */
	public function updateOnePage($id)
	{	
		\Eloquent::unguard();
		
		$all = Input::all();
		$action = array_get($all, 'action', null);
				
		array_set($all, 'fill.original', isset($all['fill']['original']) ? true : 0);
		array_set($all, 'fill.show_small', isset($all['fill']['show_small']) ? true : 0);
		array_set($all, 'fill.show_comments', isset($all['fill']['show_comments']) ? true : 0);
		array_set($all, 'fill.show_title', isset($all['fill']['show_title']) ? true : 0);		
		array_set($all, 'fill.show_date', isset($all['fill']['show_date']) ? true : 0);
		array_set($all, 'fill.in_list', isset($all['fill']['in_list']) ? true : 0);
		array_set($all, 'fill.users_id', empty($all['fill']['users_id']) ? \Auth::id() : $all['fill']['users_id']);

		if($action == "add" || $action == "saveAs") {
			
			$page = new \Veer\Models\Page;
			$page->fill($all['fill']);
			$page->hidden = true;
			$page->save();
			
			$id = $page->id;
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.new'));
			$this->action_performed[] = "NEW page";
		} else {
			$page = \Veer\Models\Page::find($id);
		}
	
		if($action == "update") {
			$page->fill($all['fill']);
			$page->save();
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.update'));
			$this->action_performed[] = "UPDATE page";			
		}
		
		//status
		if($action == "changeStatusPage.".$id) 
		{
			if($page->hidden == true) { $page->hidden = false; } else { $page->hidden = true; }
			$page->save();
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.status'));
			$this->action_performed[] = "UPDATE page status";
		}		
		
		$this->connections($page, $id, 'pages', array(
			"actionButton" => $action,
			"tags" => $all['tags'],
			"attributes" => $all['attribute'],
			"attachImages" => $all['attachImages'],
			"attachFiles" => $all['attachFiles'],
			"attachCategories" => $all['attachCategories'],
			"attachProducts" => $all['attachProducts'],
			"attachChildPages" => $all['attachChildPages'],
			"attachParentPages" => $all['attachParentPages']
		), array(
			"prefix" => array("image" => "pg", "file" => "pg")
		));		
			
		// freeform
		if(!empty($all['freeForm'])) {
			$ff = preg_split('/[\n\r]+/', trim($all['freeForm']) );
			foreach ($ff as $freeForm) {
				if(starts_with($freeForm, 'Tag:')) {
					$this->attachElements($freeForm, $page, 'tags', null, ",", "Tag:");
				} else {
					$this->attachElements($freeForm, $page, 'attributes', null, ",", "Attribute:");
				}
			}
		}
		
		if($action == "add" || $action == "saveAs") {
			$this->skipShow = true;
			Input::replace(array('id' => $id));
			return \Redirect::route('admin.show', array('pages', 'id' => $id));	
		}
	}
	
	
	/**
	 * Products actions
	 * @param type $action
	 */
	protected function quickProductsActions($action)
	{
		if(starts_with($action, "changeStatusProduct")) 
		{
			$r = explode(".", $action); 
			$this->changeProductStatus( \Veer\Models\Product::find($r[1]) );
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.status'));
			$this->action_performed[] = "UPDATE product status";
		}
		
		if(starts_with($action, "deleteProduct")) 
		{
			$r = explode(".", $action); 
			\Veer\Models\Product::find($r[1])->delete();
			// TODO: relationships
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.delete'));
			$this->action_performed[] = "DElETE product";
		}		
		
		if(starts_with($action, "showEarlyProduct")) 
		{
			\Eloquent::unguard();
			$r = explode(".", $action); 
			\Veer\Models\Product::where('id','=',$r[1])->update(array("to_show" => now()));
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.show'));
			$this->action_performed[] = "SHOW product";
		}
	}
	
	
	/**
	 * Pages actions
	 * @param type $action
	 */
	protected function quickPagesActions($action)
	{
		if(starts_with($action, "changeStatusPage")) 
		{
			$r = explode(".", $action); 
			$page = \Veer\Models\Page::find($r[1]);
			if($page->hidden == true) { $page->hidden = false; } else { $page->hidden = true; }
			$page->save();
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.status'));
			$this->action_performed[] = "UPDATE page status";			
		}
		
		if(starts_with($action, "deletePage")) 
		{
			$r = explode(".", $action); 
			$this->deletePage($r[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.delete'));
			$this->action_performed[] = "DElETE page";
		}	
	}	
	
	
	/**
	 * delete Page & relationships
	 */
	protected function deletePage($id)
	{
		$p = \Veer\Models\Page::find($id);
		if(is_object($p)) {
			$p->subpages()->detach();
			$p->parentpages()->detach();
			$p->products()->detach();
			$p->categories()->detach();
			$p->tags()->detach();
			$p->attributes()->detach();
			$p->images()->detach();
			$p->downloads()->update(array("elements_id" => 0));
			
			$p->userlists()->delete();
			$p->delete();
			// comments, communications skip
		}
	}
	

	/**
	 * delete Product & relationships
	 */
	protected function deleteProduct($id)
	{
		$p = \Veer\Models\Product::find($id);
		if(is_object($p)) {
			$p->subproducts()->detach();
			$p->parentproducts()->detach();
			$p->pages()->detach();
			$p->categories()->detach();
			$p->tags()->detach();
			$p->attributes()->detach();
			$p->images()->detach();
			$p->downloads()->update(array("elements_id" => 0));
			
			$p->userlists()->delete();
			$p->delete();
			// orders_products, comments, communications skip
		}
	}
	
	
	/**
	 * update images 
	 */
	public function updateImages()
	{
		$all = Input::all();
		foreach($all as $k => $v) { 
			if(Input::hasFile($k)) {				
					$newId[] = $this->upload('image', $k, null, null, '', null, true);
					Event::fire('veer.message.center', \Lang::get('veeradmin.image.upload'));
					$this->action_performed[] = "UPLOAD image";					
			}				
		}
			
		$attachImages = array_get($all, 'attachImages', null);
		if(!empty($attachImages)) { 
			
			$result = preg_match("/\[(?s).*\]/", $attachImages, $small);
			$parseTypes = explode(":", substr(array_get($small, 0, ''),2,-1));
						
			if(starts_with($attachImages, 'NEW')) {
				$attach = empty($newId) ? null : $newId;
			} else {
				$parseAttach = explode("[", $attachImages);
				$attach = explode(",", array_get($parseAttach, 0, null));				
			}
			
			$this->attachFromForm($parseTypes, $attach, 'images');
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.image.attach'));
			$this->action_performed[] = "ATTACH image";				
		}
		
		if(starts_with(array_get($all, 'action', null), 'deleteImage')) {
			$r = explode(".", $all['action']);
			$this->deleteImage($r[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.image.delete'));
			$this->action_performed[] = "DELETE image";		
		}
		
	}
	
	
	/**
	 *  delete Image function
	 * @param type $id
	 */
	protected function deleteImage($id)
	{
		$img = \Veer\Models\Image::find($id);
		if(is_object($img)) {
			$img->pages()->detach();
			$img->products()->detach();
			$img->categories()->detach();
			\File::delete(config("veer.images_path")."/".$img->img);
			$img->delete();			
		}
	}
	
	
	/**
	 * update tags
	 */
	public function updateTags()
	{		
		\Eloquent::unguard();
		
		if(starts_with(Input::get('action', null), "deleteTag")) {
			$r = explode(".", Input::get('action', null));
			$this->deleteTag($r[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.tag.delete'));
			$this->action_performed[] = "DELETE tag";			
		} else {
		
			$existingTags = Input::get('renameTag', null);
			if(is_array($existingTags)) {
				foreach($existingTags as $key => $value) { $value = trim($value);
					$tagDb = \Veer\Models\Tag::where('name','=',$value)->first();
					if(!is_object($tagDb)) {
						\Veer\Models\Tag::where('id','=',$key)->update(array('name' => $value));
					}
				}
			}

			$new = $this->parseForm(Input::get('newTag', null));

			if(is_array($new['target'])) {
				foreach($new['target'] as $tag) {
					$tag = trim($tag);
					if(empty($tag)) { continue; }
					$tagDb = \Veer\Models\Tag::firstOrNew(array('name' => $tag));
					$tagDb->save();
					$tags[] = $tagDb->id;
				}
				if(isset($tags)) {
					$this->attachFromForm($new['elements'], $tags, 'tags');
				}		
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.tag.update'));
			$this->action_performed[] = "UPDATE tags";					
		}
	}
	
	
	/**
	 * delete Tag
	 * @param type $id
	 */
	protected function deleteTag($id)
	{
		$t = \Veer\Models\Tag::find($id);
		if(is_object($t)) {
			$t->pages()->detach();
			$t->products()->detach();
			$t->delete();			
		}
	}
	
	
	/**
	 * parsing free form for tag|image connections
	 */
	public function parseForm($textarea)
	{
		$small = ''; 
		$result = preg_match("/\[(?s).*\]/", $textarea, $small);
		$parseTypes = explode(":", substr(array_get($small, 0, ''),2,-1));
		$parseAttach = explode("[", $textarea);
		$attach = explode(",", trim(array_get($parseAttach, 0, null)));	
		
		return array('target' => $attach, 'elements' => $parseTypes);		
	}
				
	
	/**
	 * attachFromForm
	 */
	public function attachFromForm($str, $attach, $type) 
	{
		foreach($str as $k => $v) {
		$p = explode(",", $v);
			foreach($p as $id) {
				if($k == 0) { $object = \Veer\Models\Product::find($id); }
				if($k == 1) { $object = \Veer\Models\Page::find($id); }
				if($k == 2) { $object = \Veer\Models\Category::find($id); }			
				if(is_object($object)) {
					$this->attachElements($attach, $object, $type, null);
				}
			}
		}
	}
	
	
	/**
	 * update downloads
	 */
	public function updateDownloads()
	{
		$action = Input::get('action', null);
		
		$this->removeFile($action);
		
		if(starts_with($action, 'deleteFile'))
		{
			$r = explode(".", $action);
			$this->deleteFile($r[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.delete'));
			$this->action_performed[] = "DELETE file";	
		}
		
		if(starts_with($action, 'makeRealLink')) 
		{
			$times = Input::get('times', 0);
			$exdate = Input::get('expiration_day', null);
			
			$r = explode(".", $action);
			$f = \Veer\Models\Download::find($r[1]);
			if(is_object($f)) {
				$newF = $f->replicate();
				$newF->secret = str_random(100).date("Ymd", time());
				if($times > 0 || !empty($exdate)) { 
					$newF->expires = 1;
					$newF->expiration_times = $times;
					if(!empty($exdate)) {
						$newF->expiration_day = \Carbon\Carbon::parse($exdate);				
					}
				}
				
				$newF->original = 0;
				$newF->save();
				Event::fire('veer.message.center', \Lang::get('veeradmin.file.download'));
				$this->action_performed[] = "CREATE download link";
			}
		}
		
		if(starts_with($action, 'copyFile')) 
		{
			$r = explode(".", $action);
			$prdIds = explode(",", Input::get('prdId', null));
			$pgIds = explode(",", Input::get('pgId', null));
			$this->prepareCopying($r[1], $prdIds, $pgIds);
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.copy'));
			$this->action_performed[] = "COPY file";			
		}
		
		if(Input::hasFile(Input::get('uploadFiles', null))) {				
			$newId[] = $this->upload('file', 'uploadFiles', null, null, '', null, true);
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.upload'));
			$this->action_performed[] = "UPLOAD file";					
		}
		
		$attachFiles = Input::get('attachFiles', null);
		if(!empty($attachFiles)) { 
			
			$parseTypes = $this->parseForm($attachFiles);
			
			$attach = array();
			
			if(is_array($parseTypes['target'])) {
				foreach($parseTypes['target'] as $t) {
					$t = trim($t);
					if(empty($t) || $t == "NEW") { 
						if(!empty($newId)) {
							$attach = array_merge($attach, $newId);
						}
						continue;
					}					
					$attach[] = $t;
				}	
			}
			
			$prdIds = explode(",", array_get($parseTypes, 'elements.0', null));
			$pgIds = explode(",",  array_get($parseTypes, 'elements.1', null));
			foreach($attach as $f) {
				$this->prepareCopying($f, $prdIds, $pgIds);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.attach'));
			$this->action_performed[] = "ATTACH file";				
		}		
	}
	
	
	/**
	 *  delete File function
	 * @param type $id
	 */
	protected function deleteFile($id)
	{
		$f = \Veer\Models\Download::find($id);
		if(is_object($f)) {			
			$allCopies = \Veer\Models\Download::where('fname','=',$f->fname)->get();
			
			if(count($allCopies) <= 1) {
				\File::delete(config("veer.downloads_path")."/".$f->fname);
			}
			$f->delete();			
		}
	}	
	
	
	/**
	 * prepare Copying Files
	 * @param type $fileId
	 */
	public function prepareCopying($fileId, $prds = array(), $pgs = array())
	{
		
		if(is_array($prds)) {
			foreach($prds as $id) {
				$object = \Veer\Models\Product::find(trim($id));
				if(is_object($object)) {
					$this->copyFiles(":".$fileId, $object);
				}
			}
		}

		if(is_array($pgs)) {
			foreach($pgs as $id) {
				$object = \Veer\Models\Page::find(trim($id));
				if(is_object($object)) {
					$this->copyFiles(":".$fileId, $object);
				}
			}
		}
	}
	
	
	/**
	 * update attributes
	 */
	public function updateAttributes()
	{
		\Eloquent::unguard();
		
		if(starts_with(Input::get('action', null), "deleteAttrValue")) {
			list($act, $id) = explode(".", Input::get('action', null));
			$this->deleteAttribute($id);
			Event::fire('veer.message.center', \Lang::get('veeradmin.attribute.delete'));
			$this->action_performed[] = "DELETE attribute";	
			
		} elseif(Input::get('action', null) == "newAttribute") {
		
			$manyValues = preg_split('/[\n\r]+/', trim(Input::get('newValue', null)) );
			foreach($manyValues as $value) {
				$this->attachToAttributes(Input::get('newName', null), $value);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.attribute.new'));
			$this->action_performed[] = "NEW attribute";				
		} else {
			
			// rename attribute name
			$attrName = Input::get('renameAttrName', null);
			foreach($attrName as $k => $v) 
			{
				if($k != $v) {
					\Veer\Models\Attribute::where('name', '=', $k)
						->update(array('name' => $v));
				}
			}
			
			// update attribute value & descr
			$attrVal = Input::get('renameAttrValue', null);
			$attrDescr = Input::get('descrAttrValue', null);
			$attrType = Input::get('attrType', null);
			foreach($attrVal as $k => $v) 
			{
				if(array_get($attrType, $k, 0) == 1) { $type = "descr"; } else { $type = "choose"; }
				
				\Veer\Models\Attribute::where('id', '=', $k)
						->update(array('val' => $v, 
							'descr' => array_get($attrDescr, $k, ''),
							'type' => $type));
			}
			
			// add new values to existing name
			$newAttrValue = Input::get('newAttrValue', null);
			foreach($newAttrValue as $k => $v) 
			{	
				$this->attachToAttributes($k, $v);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.attribute.update'));
			$this->action_performed[] = "UPDATE attributes";			
		}
	}
	
	
	/**
	 * delete Attribute
	 */
		/**
	 * delete Tag
	 * @param type $id
	 */
	protected function deleteAttribute($id)
	{
		$t = \Veer\Models\Attribute::find($id);
		if(is_object($t)) {
			$t->pages()->detach();
			$t->products()->detach();
			$t->delete();			
		}
	}
	
	
	/**
	 * update attributes Connections
	 */
	public function attachToAttributes($name, $form)
	{
		$new = $this->parseForm($form);
				
		if(is_array($new['target'])) {
			foreach($new['target'] as $a) 
			{
				$a = trim($a);
				if(empty($a)) { continue; }
				if(starts_with($a, ":")) { 
					$aDb = \Veer\Models\Attribute::find(substr($a,1));
					if(!is_object($aDb)) { continue; }
				} else {
				$aDb = \Veer\Models\Attribute::firstOrNew(
					array('name' => $name,
						'val' => $a,
						'type' => '?'
						));
				$aDb->save();
				}
				$attributes[] = $aDb->id;
			}
			if(isset($attributes)) {
				$this->attachFromForm($new['elements'], $attributes, 'attributes');
				unset($attributes);
			}		
		}
	}
	
}