<?php

class AdminController extends \BaseController {

	protected $adm;
	
	protected $action_performed = null;
	
	
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
		
		$data = $this->$f();
		
		if(!app('request')->ajax()) {
			
			return $this->show($t);
		} else {
			
			return $data;
		}
	}

	
	
	
	/**
	 * Update Sites
	 */
	protected function updateSites() {
		
		$data = Input::get('site');
		$turnoff = Input::get('turnoff', null);
		$turnon = Input::get('turnon', null);
		$message = "Sites were updated.";
		
		foreach($data as $key => $values) {
			
			$values['url'] = trim($values['url']);
			
			if(empty($values['url'])) { continue; }
			
			$site = \Veer\Models\Site::firstOrNew(array("id" => trim($key)));
        
			if(app('veer')->siteId != $key) { $site->url = $values['url']; }
			$site->parent_id = empty($values['parent_id']) ? 0 : $values['parent_id'];
			$site->manual_sort = empty($values['manual_sort']) ? 0 : $values['manual_sort'];
			if(app('veer')->siteId != $key) { 
				$site->redirect_on = empty($values['redirect_on']) ? false : true;
				$site->redirect_url = empty($values['redirect_url']) ? '' : $values['redirect_url'];
			}
			
			$site->on_off = isset($site->on_off) ? $site->on_off : false;
			if($key == $turnoff && app('veer')->siteId != $key) { $site->on_off = false; $message.= " Site #".$site->id." is down."; }
			if($key == $turnon) { $site->on_off = true; $message.= " Site #".$site->id." is up."; }
			
			if(!isset($site->id)) { $message.=" Adding a new site."; }
			
			$site->save();
			$this->action_performed = "update";
		}	
		
		if(app('veer')->siteId == $turnoff) { $message.=" You cannot turn off current site!"; }
		
		Artisan::call('cache:clear');
		
		Event::fire('veer.message.center', $message);
	}
	
	
	
	
	/**
	 * Update Configuration
	 */
	protected function updateConfiguration() 
	{
		Eloquent::unguard();
		
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
				$this->action_performed = "update";
			}

			if(!empty($delete)) {
				\Veer\Models\Configuration::destroy($cardid);
				$this->action_performed = "delete";
			}

			Artisan::call('cache:clear');

			// for ajax calls
			if(app('request')->ajax()) {
				$items = $this->showConfiguration($siteid, array('id','desc'));

				return view(app('veer')->template.'.lists.configuration-cards', array(
					"configuration" => $items[0]->configuration,
					"siteid" => $siteid,
				));		
			}
				 // for error
		} else { Event::fire('veer.message.center', 'Error. Reload page.'); }
	}	
	
	
	
	
	/**
	 * Update Components
	 */
	protected function updateComponents() 
	{
		Eloquent::unguard();		
		$siteid = Input::get('siteid');
		$confs = Input::get('components', null);
		$new = Input::get('new', null);
		
		if(!empty($confs)) { $cardid = head(array_keys($confs)); }
		if(!empty($new)) { $cardid = $siteid; $confs = $new; }
		
		if(!empty($siteid)) { 
		
			$save = Input::get('save', null);
			$copy = Input::get('copy', null);
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
				$this->action_performed = "update";
			}

			if(!empty($delete)) {
				\Veer\Models\Component::destroy($cardid);
				$this->action_performed = "delete";
			}

			// for ajax calls
			if(app('request')->ajax()) {
				$items = $this->showComponents($siteid, array('id','desc'));
				
				return view(app('veer')->template.'.lists.components-cards', array(
					"components" => $items[0]->components,
					"siteid" => $siteid,
				));		
			}
				 // for erro
		} else { Event::fire('veer.message.center', 'Error. Reload page.'); }
	}	
	
	
	
	
	/**
	 * Update Jobs
	 */	
	protected function updateJobs()
	{
		$run = Input::get('_run', null);
		$delete = Input::get('dele', null);
		$save = Input::get('save', null);
		
		if(!empty($delete)) {
			\Artemsk\Queuedb\Job::destroy(head(array_keys($delete)));
			Event::fire('veer.message.center', 'Job deleted.');
			$this->action_performed = "delete";
		}

		if(!empty($run)) {
			$jobid = head(array_keys($run));
			$payload = Input::get('payload', null);

			$item = \Artemsk\Queuedb\Job::where('id','=',$jobid)->first();			
			if(is_object($item)) {		
				
				$item->payload = $payload;
				$item->status = \Artemsk\Queuedb\Job::STATUS_OPEN;
				$item->scheduled_at = now();
				$item->save();

				$job = new Artemsk\Queuedb\QdbJob(app(), $item);
				$job->fire();
				Event::fire('veer.message.center', 'Job done.');
				$this->action_performed = "update";
			}
		}
		
		if(!empty($save)) {
	
			$q = Input::all();

			$startc = \Carbon\Carbon::parse(array_get($q, 'jobs.new.start'));
			$repeat = array_get($q, 'jobs.new.repeat');
			$data =  json_decode(array_get($q, 'jobs.new.data'), true);
			$queue = array_get($q, 'jobs.new.classname');
			
			if($repeat > 0) {
				$data['repeatJob'] = $repeat;
			}
			
			//$checkQueueClass = app('veer')->loadComponentClass($queue, null, 'queue');
			$classFullName = "\Veer\Lib\Queues\\" . $queue;
			
			if (!class_exists($classFullName)) {  Event::fire('veer.message.center', "Class not found. Run 'composer dump-autoload'."); } 
			else {			
				if(now() >= $startc) {
					Queue::push( $classFullName , $data);
				} else {
					$wait = \Carbon\Carbon::now()->diffInMinutes($startc);
					Queue::later($wait, $classFullName , $data);
				}
				Event::fire('veer.message.center', "Job added.");
			}	
		}
		
		
	}

	
	
	
	/**
	 * Update Secrets
	 */	
	protected function updateSecrets() 
	{
		Eloquent::unguard();	
		
		$save = Input::get('save', null);
		$delete = Input::get('dele', null);		
		$cardid = Input::get('secrets', null);
		
		if(!empty($delete)) {
			\Veer\Models\Secret::destroy(head(array_keys($delete)));
			Event::fire('veer.message.center', 'Secret deleted.');
			$this->action_performed = "delete";
		}		

		if(!empty($save)) {			
			$id = head(array_keys($save));
			
			foreach($cardid as $key => $value) 
			{
				if($value['elements_id'] <= 0) { Event::fire('veer.message.center', 'Elements ID should not be empty.'); continue; }
				
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
				Event::fire('veer.message.center', 'Secrets updated.');
				$this->action_performed = "update";
			}	
			
		}
	}
	
	
	
	
	/**
	 * Update Root Categories
	 */
	protected function updateCategories()
	{
		// if we're working with one category then call another function
		//
		$editOneCategory = Input::get('category', null);
		if(!empty($editOneCategory)) { return $this->updateOneCategory($editOneCategory); }
		
		$action = Input::get('action', null);
		$cid = Input::get('deletecategoryid', null);
		$new = Input::get('newcategory', null);
		
		if($action == "delete" && !empty($cid)) {
			$this->action_performed = "delete";	
			return $this->deleteCategory($cid);
		}
		
		if($action == "add" && !empty($new)) {
			$c = new \Veer\Models\Category;
			$c->title = $new;
			$c->description = '';
			$c->remote_url = '';
			$c->manual_sort = 999999;
			$c->views = 0;
			$c->sites_id = Input::get('siteid');
			$c->save();
			
			$items = \Veer\Models\Site::with(array('categories' => function($query) {
					$query->has('parentcategories', '<', 1)->orderBy('manual_sort','asc');
				}))->orderBy('manual_sort','asc')->where('id','=',Input::get('siteid'))->get();
				
			$this->action_performed = "add";	
			return app('view')->make(app('veer')->template.'.lists.categories-category', array(
				"categories" => $items[0]->categories,
				"siteid" => Input::get('siteid'),
				"child" => view(app('veer')->template.'.elements.asset-delete-categories-script')
			));	
			
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
		}
		
	}
	
	
	
	
	/**
	 * Sorting Elements
	 * @param type $elements
	 * @param type $sortingParams
	 * @return type array
	 */
	protected function sortElements($elements, $sortingParams)
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
	 * delete Category
	 * @param type $cid
	 * @return string
	 */
	protected function deleteCategory($cid)
	{
		\Veer\Models\Category::destroy($cid);
		\Veer\Models\CategoryConnect::where('categories_id','=',$cid)->forceDelete();
		\Veer\Models\CategoryPivot::where('parent_id','=',$cid)->orWhere('child_id','=',$cid)->forceDelete();
		Veer\Models\ImageConnect::where('elements_id','=',$cid)
		->where('elements_type','=','Veer\Models\Category')->forceDelete();
		//\Veer\Models\Communication::where('elements_id','=',$cid)
		//->where('elements_type','=','Veer\Models\Category')->delete();	
		$this->action_performed = "delete";
		return 'Deleted with all connections: images, parent & child categories, products, pages';
	}
	
	
	
	
	/**
	 * Update One Category
	 */	
	protected function updateOneCategory($cid)
	{	
		$all = Input::all();
		
		// deletecategoryid <- id of deleted category id
		if($all['action'] == "delete") { 
			$this->action_performed = "delete";	
			return $this->deleteCategory($all['deletecategoryid']); 
		}
		
		$category = \Veer\Models\Category::find($cid);
		
		// first parent of category
		if(isset($all['parentId']) && $all['action'] == "saveParent" && $all['parentId'] > 0) {
			$category->parentcategories()->attach($all['parentId']);
			Event::fire('veer.message.center', 'Attach new parent category.');
			$this->action_performed = "add";	
		}
		
		// updating parents
		if(isset($all['parentId']) && $all['action'] == "updateParent" && $all['parentId'] > 0) {			
			if($all['lastCategoryId'] != $all['parentId']) {
				
			   $check = \Veer\Models\CategoryPivot::where('child_id','=',$cid)
				->where('parent_id','=',$all['parentId'])->first();
	
			   if(!$check) {
					$category->parentcategories()->attach($all['parentId']);
					Event::fire('veer.message.center', 'Attach new parent category.');
			   }
			}
			$this->action_performed = "update";
		}
		
		// removing parents
		if(isset($all['parentId']) && $all['action'] == "removeParent") {
			
			$category->parentcategories()->detach($all['parentId']);
			
			Event::fire('veer.message.center', 'Detach parent category.');
			
			$this->action_performed = "delete";
		}
		
		// updating info
		if($all['action'] == "updateCurrent") {
			$category->title = $all['title'];
			$category->remote_url = $all['remoteUrl'];
			$category->description = $all['description'];
			$category->save();
			Event::fire('veer.message.center', 'Update category.');
			$this->action_performed = "update";
		}
		
		// delete current category
		if($all['action'] == "deleteCurrent") {
			
			$this->deleteCategory($cid);
			
			Input::replace(array('category' => null));
			
			return $this->show('categories');
		}
		
		// adding childs (new or existing)
		if($all['action'] == "addChild" && isset($all['child'])) {
			
			if(starts_with($all['child'], ":")) {
				$arr = explode(",", substr($all['child'],1));
				foreach($arr as $child) {
					$category->subcategories()->attach($child);
				}
				Event::fire('veer.message.center', 'Attach existing categories as childs.');
			} else {				
				$c = new \Veer\Models\Category;
				$c->title = $all['child'];
				$c->description = '';
				$c->remote_url = '';
				$c->manual_sort = 999999;
				$c->views = 0;
				$c->sites_id = $category->site->id;
				$c->save();
			
				$category->subcategories()->attach($c->id);
				Event::fire('veer.message.center', 'Attach new child category.');
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
					Event::fire('veer.message.center', 'Change parent category for child category.');
			   }
			}
		}
		
		// remove child from current
		if($all['action'] == "removeInChild") {
			$category->subcategories()->detach($all['currentChildId']);
			Event::fire('veer.message.center', 'Detach parent category for child category.');
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
		}		
			
		// update|add images
		if($all['action'] == "updateImages") {
			if (starts_with($all['attachImages'], ":")) {
				$arr = explode(",", substr($all['attachImages'], 1));
				foreach ($arr as $image) {
					$category->images()->attach($image);
				}
				Event::fire('veer.message.center', 'Attach existing images to category.');
			}

			if(Input::hasFile('uploadImage')) {

				$fname = "ct".@$all['category']."_".date('YmdHis',time()).
					str_random(10).".".Input::file('uploadImage')->getClientOriginalExtension();
				Input::file('uploadImage')->move(base_path()."/".Config::get('veer.images_path'), $fname);
				$newimage = new \Veer\Models\Image; 
				$newimage->img = $fname;
				$newimage->save();
				$newimage->categories()->attach($all['category']);
				Event::fire('veer.message.center', 'Upload new image & attach it to category.');
			}							
		}

		if(starts_with($all['action'], 'removeImage')) {
			$r = explode(".", $all['action']);
			if(!empty($r[1])) { $category->images()->detach($r[1]);	}
		}
		
		// add existings products
		if($all['action'] == "updateProducts") {
			if (starts_with($all['attachProducts'], ":")) {
				$arr = explode(",", substr($all['attachProducts'], 1));
				foreach ($arr as $prd) {
					$category->products()->attach($prd);
				}
				Event::fire('veer.message.center', 'Attach existing products to category.');
			}
		}

		if(starts_with($all['action'], 'removeProduct')) {
			$r = explode(".", $all['action']);
			if(!empty($r[1])) { $category->products()->detach($r[1]);	}
		}	
		
		// add existings pages
		if($all['action'] == "updatePages") {
			if (starts_with($all['attachPages'], ":")) {
				$arr = explode(",", substr($all['attachPages'], 1));
				foreach ($arr as $pg) {
					$category->pages()->attach($pg);
				}
				Event::fire('veer.message.center', 'Attach existing pages to category.');
			}
		}

		if(starts_with($all['action'], 'removePage')) {
			$r = explode(".", $all['action']);
			if(!empty($r[1])) { $category->pages()->detach($r[1]);	}
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
