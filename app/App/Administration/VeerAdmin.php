<?php namespace Veer\Administration;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

class VeerAdmin extends Show {

	protected $action_performed = array();
	
	public $skipShow = false;
	
	public $counted = null;
	
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
			'reminders' => $reminders, 'trashed' => empty($trashed)? null : $trashed);
	}	
	
	protected function checkLatestVersion()
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->get(\Veer\VeerApp::VEERCOREURL . "/releases", array('verify' => false));
		$res = json_decode($response->getBody());
				
		return head($res)->tag_name;
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
		$turnoff = Input::get('turnoff');
		$turnon = Input::get('turnon');
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
				$site->redirect_on = empty($values['redirect_on']) ? 0 : true;
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
		$confs = Input::get('configuration');		
		$new = Input::get('new');
		
		if(!empty($confs)) { $cardid = head(array_keys($confs)); }
		if(!empty($new)) { $cardid = $siteid; $confs = $new; }
		
		if(!empty($siteid)) { 
		
			$save = Input::get('save');
			$delete = Input::get('dele');

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
		$confs = Input::get('components');
		$new = Input::get('new');
		
		if(!empty($confs)) { $cardid = head(array_keys($confs)); }
		if(!empty($new)) { $cardid = $siteid; $confs = $new; }
		
		if(!empty($siteid)) { 
		
			$save = Input::get('save');
			$delete = Input::get('dele');

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
		
		$save = Input::get('save');
		$delete = Input::get('dele');		
		$cardid = Input::get('secrets');
		
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
		$run = Input::get('_run');
		$delete = Input::get('dele');
		$save = Input::get('save');
		$pause = Input::get('paus');
		
		if(!empty($delete)) {
			
			\Artemsk\Queuedb\Job::destroy(head(array_keys($delete)));
			Event::fire('veer.message.center', \Lang::get('veeradmin.jobs.delete'));
			$this->action_performed[] = "DELETE job";
		}

		if(!empty($run)) {
			
			$this->runJob( head(array_keys($run)) , Input::get('payload') );
			
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

		$classFullName = "\Veer\Queues\\" . $queue;

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
		$editOneCategory = Input::get('category');
		if(!empty($editOneCategory)) { 
			
			return $this->updateOneCategory($editOneCategory); 
		}
		
		$action = Input::get('action');
		$cid = Input::get('deletecategoryid');
		$new = Input::get('newcategory');
		
		if($action == "delete" && !empty($cid)) {
			$this->action_performed[] = "DELETE category";
			$this->deleteCategory($cid);
		}
		
		if($action == "add" && !empty($new)) {			
			$site_id = Input::get('siteid');				
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
			$c->views = array_get($options, 'views', 0);
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
		if(starts_with($ids, $start)) 
		{
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
		$editOneProduct = Input::get('id');
		if(!empty($editOneProduct)) { 
			
			return $this->updateOneProduct($editOneProduct); 
		}
				
		// quick actions: status etc.
		$this->quickProductsActions(Input::get('action'));

		$all = Input::all();
		
		$title = trim(array_get($all, 'fill.title'));
		$freeForm = trim(array_get($all, 'freeForm'));
		
		if(empty($freeForm) && !empty($title)) {
			
			$prices = explode(":", array_get($all, 'prices'));
			$options =  explode(":", array_get($all, 'options'));
			$categories =  explode(",", array_get($all, 'categories'));
			
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

				$image = array_get($items, 15);
				if(!empty($image)) {
					$new = new \Veer\Models\Image; 
					$new->img = $image;
					$new->save();
					$new->products()->attach($p->id);			
				}
				
				$file= array_get($items, 16);
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
		$action = array_get($all, 'action');
				
		array_set($all, 'fill.star', isset($all['fill']['star']) ? true : 0);
		array_set($all, 'fill.download', isset($all['fill']['download']) ? true : 0);
	
		$salesOn = parse_form_date(array_get($all, 'fill.price_sales_on', 0));
		
		$salesOff = parse_form_date(array_get($all, 'fill.price_sales_off', 0));
					
		$toShow = parse_form_date(array_get($all, 'fill.to_show', 0));
				
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
		$action = array_get($attributes, 'actionButton');
		
		// tags
		if(isset($attributes['tags'])) $this->attachTags(array_get($attributes, 'tags'), $object);
		
		// attributes
		if(isset($attributes['attributes'])) $this->attachAttributes(array_get($attributes, 'attributes'), $object);

		// images
		if(Input::hasFile(array_get($attributes, 'uploadImageId', 'uploadImage'))) {
			$this->upload('image', array_get($attributes, 'uploadImageId', 'uploadImage'), 
				$id, $type, array_get($options, 'prefix.image'), null);
		}			
		
		$this->attachElements(array_get($attributes, 'attachImages'), $object, 'images', null);
		
		$this->detachElements($action, 
			array_get($attributes, 'removeImageId', 'removeImage'), $object, 'images', 
			array_get($options, 'message.images'));
	
		//files
		if(Input::hasFile(array_get($attributes, 'uploadFilesId', 'uploadFiles'))) {
			$this->upload('file', array_get($attributes, 'uploadFilesId', 'uploadFiles'), 
				$id, $object, array_get($options, 'prefix.file'), null);
		}
		
		$this->copyFiles(array_get($attributes, 'attachFiles'), $object);		
		$this->removeFile($action);
		
		// categories: we cannot add not existing categories as we don't know site id
		if(isset($attributes['attachCategories'])) 
		{
			$this->attachElements(array_get($attributes, 'attachCategories'), $object, 'categories', null);	
		}
		$this->detachElements($action, array_get($attributes, 'removeCategoryId', 'removeCategory'), $object, 'categories', null);
				
		// pages
		$this->attachElements(array_get($attributes, 'attachPages'), $object, 'pages', null);	
		$this->detachElements($action, array_get($attributes, 'removePageId', 'removePage'), $object, 'pages', null);	
		
		// products
		$this->attachElements(array_get($attributes, 'attachProducts'), $object, 'products', null);	
		$this->detachElements($action, array_get($attributes, 'removeProductId', 'removeProduct'), $object, 'products', null);			
		
		// child products
		$this->attachElements(array_get($attributes, 'attachChildProducts'), $object, 'subproducts', null);	
		$this->detachElements($action, array_get($attributes, 'removeChildProductId', 'removeChildProduct'), $object, 'subproducts', null);
		
		// parent products
		$this->attachElements(array_get($attributes, 'attachParentProducts'), $object, 'parentproducts', null);	
		$this->detachElements($action, array_get($attributes, 'removeParentProductId', 'removeParentProduct'), $object, 'parentproducts', null);
		
		// child pages
		$this->attachElements(array_get($attributes, 'attachChildPages'), $object, 'subpages', null);	
		$this->detachElements($action, array_get($attributes, 'removeChildPageId', 'removeChildPage'), $object, 'subpages', null);
		
		// parent pages
		$this->attachElements(array_get($attributes, 'attachParentPages'), $object, 'parentpages', null);	
		$this->detachElements($action, array_get($attributes, 'removeParentPageId', 'removeParentPage'), $object, 'parentpages', null);		
	}
	
	
	/**
	 * update Pages
	 */
	public function updatePages()
	{
		// if we're working with one page then call another function
		//
		$editOnePage = Input::get('id');
		if(!empty($editOnePage)) { 
			
			return $this->updateOnePage($editOnePage); 
		}
		
		//quick actions
		$this->quickPagesActions(Input::get('action'));
	
		$all = Input::all();
		
		$title = trim(array_get($all, 'title'));

		if(!empty($title)) {
			
			$categories =  explode(",", array_get($all, 'categories'));
			
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
		$action = array_get($all, 'action');
				
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
			$this->deleteProduct($r[1]);
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
			
		$attachImages = array_get($all, 'attachImages');
		if(!empty($attachImages)) { 
			
			$result = preg_match("/\[(?s).*\]/", $attachImages, $small);
			$parseTypes = explode(":", substr(array_get($small, 0, ''),2,-1));
					
			if(starts_with($attachImages, 'NEW')) {
				$attach = empty($newId) ? null : $newId;
			} else {
				$parseAttach = explode("[", $attachImages);
				$attach = explode(",", array_get($parseAttach, 0));				
			}
			
			$this->attachFromForm($parseTypes, $attach, 'images');
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.image.attach'));
			$this->action_performed[] = "ATTACH image";				
		}
		
		if(starts_with(array_get($all, 'action'), 'deleteImage')) {
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
			$img->users()->detach();
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
		
		if(starts_with(Input::get('action'), "deleteTag")) {
			$r = explode(".", Input::get('action'));
			$this->deleteTag($r[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.tag.delete'));
			$this->action_performed[] = "DELETE tag";			
		} else {
		
			$existingTags = Input::get('renameTag');
			if(is_array($existingTags)) {
				foreach($existingTags as $key => $value) { $value = trim($value);
					$tagDb = \Veer\Models\Tag::where('name','=',$value)->first();
					if(!is_object($tagDb)) {
						\Veer\Models\Tag::where('id','=',$key)->update(array('name' => $value));
					}
				}
			}

			$new = $this->parseForm(Input::get('newTag'));

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
		$attach = explode(",", trim(array_get($parseAttach, 0)));	
		
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
				if($k == 3) { $object = \Veer\Models\User::find($id); }	
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
		$action = Input::get('action');
		
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
			$exdate = Input::get('expiration_day');
			
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
			$prdIds = explode(",", Input::get('prdId'));
			$pgIds = explode(",", Input::get('pgId'));
			$this->prepareCopying($r[1], $prdIds, $pgIds);
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.copy'));
			$this->action_performed[] = "COPY file";			
		}
		
		if(Input::hasFile(Input::get('uploadFiles'))) {				
			$newId[] = $this->upload('file', 'uploadFiles', null, null, '', null, true);
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.upload'));
			$this->action_performed[] = "UPLOAD file";					
		}
		
		$attachFiles = Input::get('attachFiles');
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
			
			$prdIds = explode(",", array_get($parseTypes, 'elements.0'));
			$pgIds = explode(",",  array_get($parseTypes, 'elements.1'));
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
		
		if(starts_with(Input::get('action'), "deleteAttrValue")) {
			list($act, $id) = explode(".", Input::get('action'));
			$this->deleteAttribute($id);
			Event::fire('veer.message.center', \Lang::get('veeradmin.attribute.delete'));
			$this->action_performed[] = "DELETE attribute";	
			
		} elseif(Input::get('action') == "newAttribute") {
		
			$manyValues = preg_split('/[\n\r]+/', trim(Input::get('newValue')) );
			foreach($manyValues as $value) {
				$this->attachToAttributes(Input::get('newName'), $value);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.attribute.new'));
			$this->action_performed[] = "NEW attribute";				
		} else {
			
			// rename attribute name
			$attrName = Input::get('renameAttrName');
			foreach($attrName as $k => $v) 
			{
				if($k != $v) {
					\Veer\Models\Attribute::where('name', '=', $k)
						->update(array('name' => $v));
				}
			}
			
			// update attribute value & descr
			$attrVal = Input::get('renameAttrValue');
			$attrDescr = Input::get('descrAttrValue');
			$attrType = Input::get('attrType');
			foreach($attrVal as $k => $v) 
			{
				if(array_get($attrType, $k, 0) == 1) { $type = "descr"; } else { $type = "choose"; }
				
				\Veer\Models\Attribute::where('id', '=', $k)
						->update(array('val' => $v, 
							'descr' => array_get($attrDescr, $k, ''),
							'type' => $type));
			}
			
			// add new values to existing name
			$newAttrValue = Input::get('newAttrValue');
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
	
	
	/**
	 * update Etc
	 */
	public function updateEtc()
	{
		Event::fire('router.filter: csrf');
		
		$all = Input::all();
		$action = Input::get('action');
		
		if($action == "runRawSql" && array_get($all, 'freeFormSql') != null)
		{
			// TODO: warning! very dangerous!
			\DB::statement( array_get($all, 'freeFormSql') );
			Event::fire('veer.message.center', \Lang::get('veeradmin.etc.sql'));
			$this->action_performed[] = "RUN sql";
		}
	
		if(Input::get('actionButton') == "checkLatestVersion")
		{
			$latest = $this->checkLatestVersion();
			
			// for ajax calls
			if(app('request')->ajax()) {
				return view(app('veer')->template.'.elements.version', array(
					"latest" => $latest,
					"current" => \Veer\VeerApp::VEERVERSION,
				));		
			}
		}
		

	}	
	
	
	/**
	 * update Roles
	 */
	public function updateRoles()
	{
		\Eloquent::unguard();
		
		$action = Input::get('action');
		
		if($action == "updateRoles")
		{
			foreach(Input::get('role', array()) as $roleId => $role)
			{
				if($roleId != "new") 
				{
					\Veer\Models\UserRole::where('id','=', $roleId)
						->update($role);					
				}
				
				elseif( $roleId == "new" && !empty($role['role']))
				{
					$r = \Veer\Models\UserRole::firstOrNew(array("role" => $role['role'], "sites_id" => Input::get('InSite')));
					$r->fill($role);
					$r->sites_id = Input::get('InSite');
					$r->save();
					$newId = $r->id;
				}
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.role.update'));
			$this->action_performed[] = "UPDATE roles";
		}
		
		if(!empty($action) && starts_with($action, "deleteRole"))
		{
			list($act, $id) = explode(".", $action);
			$this->deleteUserRole($id);
			Event::fire('veer.message.center', \Lang::get('veeradmin.role.delete'));
			$this->action_performed[] = "DELETE role";	
		}
		
		if(Input::has('InUsers'))
		{
			$users = Input::get('InUsers');
			
			$parseAttach = explode("[", $users);
			
			if(starts_with($users, "NEW")) { $rolesId = $newId; } 
			
			else {	$rolesId = trim(array_get($parseAttach, 0));	}
			
			$usersIds = $this->parseIds( substr( array_get($parseAttach, 1) ,0,-1) );
		
			$this->associate("users", $usersIds, $rolesId, "roles_id");								
		}
	}
	
	
	/**
	 * delete User Role
	 * @param type $id
	 */
	protected function deleteUserRole($id)
	{
		$u = \Veer\Models\UserRole::find($id);
		if(is_object($u)) {
			$u->users()->update(array('roles_id' => null));
			$u->delete();			
		}
	}
	
	
	/**
	 * Associate (belongTo, hasMany relationships)
	 * - updating parents (parent field) in childs tables
	 * 
	 * @param string $relation Child model, ex: page, user, product etc.
	 * @param array $childs Ids 
	 * @param string $childsField 
	 * @param int $parentId
	 * @param string $parentField
	 * @param string $raw Raw where Sql
	 * @return void
	 */
	protected function associate($relation, $childs, $parentId, $parentField, $childsField = "id", $raw = null)
	{
		$relation = "\\" . elements(str_singular($relation));
		$r = $relation::whereIn($childsField, $childs);
		if (!empty($raw)) { $r->whereRaw($raw); }
		$r->update(array($parentField => $parentId));
	}

	
	/**
	 * update Communications
	 */
	public function updateCommunications()
	{
		if(Input::get('action') == "addMessage")
		{
			return app('veer')->communicationsSend(Input::get('communication', array()));
			Event::fire('veer.message.center', \Lang::get('veeradmin.communication.new'));
			$this->action_performed[] = "NEW communication";
		}
		
		if(Input::has('hideMessage'))
		{
			\Veer\Models\Communication::where('id','=',head(Input::get('hideMessage')))
				->update(array('hidden' => true));
			Event::fire('veer.message.center', \Lang::get('veeradmin.communication.hide'));
			$this->action_performed[] = "HIDE communication";
		}
		
		if(Input::has('unhideMessage'))
		{
			\Veer\Models\Communication::where('id','=',head(Input::get('unhideMessage')))
				->update(array('hidden' => false));
			Event::fire('veer.message.center', \Lang::get('veeradmin.communication.unhide'));
			$this->action_performed[] = "UNHIDE communication";
		}
		
		if(Input::has('deleteMessage'))
		{
			\Veer\Models\Communication::where('id','=',head(Input::get('deleteMessage')))
				->delete();
			Event::fire('veer.message.center', \Lang::get('veeradmin.communication.delete'));
			$this->action_performed[] = "DELETE communication";
		}
	}
	
	
	/**
	 * update Comments
	 */
	public function updateComments()
	{
		if(Input::get('action') == "addComment")
		{
			return app('veer')->commentsSend();
			Event::fire('veer.message.center', \Lang::get('veeradmin.comment.new'));
			$this->action_performed[] = "NEW comment";
		}
		
		if(Input::has('hideComment'))
		{
			\Veer\Models\Comment::where('id','=',head(Input::get('hideComment')))
				->update(array('hidden' => true));
			Event::fire('veer.message.center', \Lang::get('veeradmin.comment.hide'));
			$this->action_performed[] = "HIDE comment";
		}
		
		if(Input::has('unhideComment'))
		{
			\Veer\Models\Comment::where('id','=',head(Input::get('unhideComment')))
				->update(array('hidden' => false));
			Event::fire('veer.message.center', \Lang::get('veeradmin.comment.unhide'));
			$this->action_performed[] = "UNHIDE comment";
		}
		
		if(Input::has('deleteComment'))
		{
			\Veer\Models\Comment::where('id','=',head(Input::get('deleteComment')))
				->delete();
			Event::fire('veer.message.center', \Lang::get('veeradmin.comment.delete'));
			$this->action_performed[] = "DELETE comment";
		}
	}
	
	
	/**
	 * update Searches
	 */
	public function updateSearches()
	{
		if(Input::has('deleteSearch'))
		{
			$this->deleteSearch(head(Input::get('deleteSearch')));
			Event::fire('veer.message.center', \Lang::get('veeradmin.search.delete'));
			$this->action_performed[] = "DELETE search";
			return null;
		}
		
		if(Input::get('action') == "addSearch" && Input::has('search'))
		{
			$q = trim( Input::get('search') );
			if(!empty($q))
			{
				$search = \Veer\Models\Search::firstOrCreate(array("q" => $q));
				$search->increment('times');                  
				$search->save();
				
				$users =  Input::get('users');
				
				if(starts_with($users, ':')) 
				{
					$users = substr($users, 1);
					
					if( !empty($users) )
					{
						$users = explode(",", trim($users) );

						if(count($users) > 0) $search->users()->attach($users);
					}	
				}
								
				Event::fire('veer.message.center', \Lang::get('veeradmin.search.new'));
				$this->action_performed[] = "NEW search";
			}
		}	
	}
	
	
	/**
	 * delete Search
	 * @param int $id
	 */
	protected function deleteSearch($id)
	{
		$s = \Veer\Models\Search::find($id);
		if(is_object($s)) {
			$s->users()->detach();
			$s->delete();			
		}
	}
	
	
	/**
	 * update Lists
	 */
	public function updateLists()
	{
		if(Input::has('deleteList'))
		{
			$this->deleteList(head(Input::get('deleteList')));
			Event::fire('veer.message.center', \Lang::get('veeradmin.list.delete'));
			$this->action_performed[] = "DELETE list";
			return null;
		}
		
		if(Input::get('action') == "addList" && ( Input::has('products') || Input::has('pages') ))
		{
			\Eloquent::unguard();
			
			$all = Input::all();
			
			if(array_get($all, 'fill.users_id') == null && array_get($all, 'fill.session_id') == null)
			{
				array_set($all, 'fill.users_id', \Auth::id());
				array_set($all, 'fill.session_id', \Session::getId());
			}
			
			if(array_get($all, 'fill.name') == null) array_set($all, 'fill.name', '[basket]');				
			if(array_get($all, 'checkboxes.basket') != null) array_set($all, 'fill.name', '[basket]');	
				
			$p = preg_split('/[\n\r]+/', trim( array_get($all, 'products') ));
			
			if(is_array($p)) { $this->saveAndAttachLists ($p, '\\'.elements('product'), array_get($all, 'fill')); }					
			
			$pg = preg_split('/[\n\r]+/', trim( array_get($all, 'pages') ));
			
			if(is_array($pg)) { $this->saveAndAttachLists ($pg, '\\'.elements('page'), array_get($all, 'fill')); }		
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.list.new'));
			$this->action_performed[] = "NEW list";
		}	
	}
	
	
	/**
	 * Save and Attach Lists
	 * @param type $p
	 * @param type $model
	 * @param type $fill
	 */
	protected function saveAndAttachLists($p, $model, $fill)
	{
		foreach($p as $element)
		{
			$parseElements = explode(":", $element);

			$id = array_get($parseElements, 0);
			$qty = array_get($parseElements, 1, 1);
			$attrStr = array_get($parseElements, 2);

			$attrs = explode(",", $attrStr);

			$item = $model::find( trim($id) );

			if(is_object($item))
			{
				$cart = new \Veer\Models\UserList;
				$cart->fill( $fill );
				$cart->quantity = !empty($qty) ? $qty : 1;

				if(is_array($attrs) && !empty($attrStr) ) 
				{
					$cart->attributes = json_encode($attrs); 
				}
				$cart->save();
				$item->userlists()->save($cart);
			}
		}
	}
	
	
	/**
	 * delete List
	 * @param int $id
	 */
	protected function deleteList($id)
	{
		\Veer\Models\UserList::where('id','=',$id)->delete();
	}
	
	
	/**
	 * update Books
	 */
	public function updateBooks()
	{
		if(Input::has('deleteUserbook'))
		{
			$this->deleteBook(head(Input::get('deleteUserbook')));
			Event::fire('veer.message.center', \Lang::get('veeradmin.book.delete'));
			$this->action_performed[] = "DELETE book";
			return null;
		}
		
		$all = Input::all();
		$action = array_get($all, 'action');
		
		if($action == "addUserbook" || $action == "updateUserbook" )
		{
			app('veershop')->updateOrNewBook( head(array_get($all, 'userbook', array())) );
			Event::fire('veer.message.center', \Lang::get('veeradmin.book.update'));
			$this->action_performed[] = "UPDATE books";
		}
	}
	
	
	/**
	 * delete Book
	 * @param int $id
	 */
	protected function deleteBook($id)
	{
		\Veer\Models\UserBook::where('id','=',$id)->delete();
	}
	
	
	/**
	 * update Users
	 */
	public function updateUsers()
	{
		Event::fire('router.filter: csrf');
			
		$restrictions = Input::get('changeRestrictUser');
		$ban = Input::get('changeStatusUser');
		$delete = Input::get('deleteUser');
		
		if(!empty($restrictions))
		{
			\Veer\Models\User::where('id','=', key($restrictions))
				->update(array('restrict_orders' => head($restrictions)));			
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.update'));
			$this->action_performed[] = "UPDATE user";
			return null;
		}
		
		if(!empty($ban) && key($ban) != \Auth::id())
		{
			\Veer\Models\User::where('id','=', key($ban))
				->update(array('banned' => head($ban)));
			
			if(head($ban) == true) {
				\Veer\Models\UserAdmin::where('users_id','=', key($ban))
				->update(array('banned' => head($ban)));
			}
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.ban'));
			$this->action_performed[] = "UPDATE user";
			return null;
		}
		
		if(!empty($delete) && key($delete) != \Auth::id())
		{
			$this->deleteUser(key($delete));
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.delete'));
			$this->action_performed[] = "DELETE user";
			return null;
		}
		
		// if we're working with one user then call another function
		//
		$editOneUser = Input::get('id');
		if(!empty($editOneUser)) 
		{ 	
			return $this->updateOneUser($editOneUser); 
		}
		
		if(Input::get('action') == "Add")
		{
			$freeForm = Input::get('freeForm');
			$parseForm = !empty($freeForm) ? preg_split('/[\n\r]+/', trim($freeForm)) : array() ;
			
			$freeFormKeys = array(
				'username', 'phone', 'firstname', 'lastname', 'birth', 'gender', 'roles_id',
				'newsletter', 'restrict_orders', 'banned'
			);
			
			$siteId = Input::get('siteId');						
			if(empty($siteId)) $siteId = app('veer')->siteId;
			
			$rules = array(
				'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL,sites_id,' . $siteId,
				'password' => 'required|min:6',
			);			

			$validator = \Validator::make(Input::all(), $rules);
			 
			if(!$validator->fails())
			{
				$user = new \Veer\Models\User;
				
				$user->email = Input::get('email');
				$user->password = Input::get('password');
				$user->sites_id = $siteId;
				
				foreach($parseForm as $key => $value)
				{
					if(!empty($value)) $user->{$freeFormKeys[$key]} = $value;
				}
				$user->save();		
				Event::fire('veer.message.center', \Lang::get('veeradmin.user.new'));
				$this->action_performed[] = "NEW user";			
			}

		}
	}
	
	
	/**
	 * delete User and update connections
	 * @param int $id
	 */
	protected function deleteUser($id)
	{
		$u = \Veer\Models\User::find($id);
		if(is_object($u)) 
		{
			$u->discounts()->update(array("status" => "canceled"));
			$u->userlists()->update(array("users_id" => false));
			$u->books()->update(array("users_id" => false));
			$u->images()->detach();
			$u->searches()->detach();
			$u->administrator()->delete();
			// don't update: orders, bills, pages, comments, communications
			// do not need: site, role	
			$u->delete();
		}
	}
	
	
	/**
	 * update One user
	 */
	public function updateOneUser($id)
	{	
		$action = Input::get('action');
		$fill = Input::get('fill');
		
		$siteId = Input::get('fill.sites_id');						
		if(empty($siteId)) $siteId = app('veer')->siteId;
		
		$fill['sites_id'] = $siteId;
		if(array_has($fill, 'password') && empty($fill['password'])) array_forget($fill, 'password');
		
		if($action == "add") 
		{	
			$rules = array(
				'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL,sites_id,' . $siteId,
				'password' => 'required|min:6',
			);			

			$validator = \Validator::make($fill, $rules);
			
			if($validator->fails()) { 
				Event::fire('veer.message.center', \Lang::get('veeradmin.user.new.error'));
				$this->action_performed[] = "ERROR add user";
				return false;	
			}
			
			$user = new \Veer\Models\User;
			$user->save();		
			$id = $user->id;
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.new'));
			$this->action_performed[] = "NEW user";
		} 
		
		else 
		{
			$user = \Veer\Models\User::find($id);
		}
		
		$fill['restrict_orders'] = isset($fill['restrict_orders']) ? true : false;
		$fill['newsletter'] = isset($fill['newsletter']) ? true : false;
		
		$fill['birth'] = parse_form_date(array_get($fill, 'birth'));
		
		\Eloquent::unguard();
		$user->fill($fill);
		$user->save();
		
		if(Input::has('addAsAdministrator'))
		{
			$admin = \Veer\Models\UserAdmin::withTrashed()->where('users_id','=',$id)->first();
			if(!is_object($admin))
			{
				\Veer\Models\UserAdmin::create(array('users_id' => $id));
				Event::fire('veer.message.center', \Lang::get('veeradmin.user.admin'));
				$this->action_performed[] = "CREATE admin";		
			}
			
			else { $admin->restore(); }
		}
		
		if(Input::has('administrator')) $this->updateOneAdministrator(Input::get('administrator'), $id);

		// images
		if(Input::hasFile('uploadImage')) 
		{
			$this->upload('image', 'uploadImage', $id, 'users', 'usr', null);
		}			
		
		$this->attachElements(Input::get('attachImages'), $user, 'images', null);
		
		$this->detachElements($action, 'removeImage', $user, 'images', null);
		
		// pages
		if(Input::has('attachPages'))
		{
			$pages = $this->parseIds(Input::get('attachPages'));
			$this->associate("pages", $pages, $id, "users_id");
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.page.attach'));
			$this->action_performed[] = "ATTACH page to user";			
		}
		
		if(starts_with($action, 'removePage'))
		{
			$p = explode(".", $action);
			$this->associate("pages", array($p[1]), 0, "users_id");
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.page.detach'));
			$this->action_performed[] = "DETACH page from user";
		}
		
		if(starts_with($action, "deletePage")) 
		{
			$p = explode(".", $action); 
			$this->deletePage($p[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.delete'));
			$this->action_performed[] = "DElETE page";
			return null;
		}
		
		// books
		if($action == "addUserbook" || $action == "updateUserbook" )
		{
			foreach(Input::get('userbook', array()) as $book)
			{
				app('veershop')->updateOrNewBook($book);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.book.update'));
			$this->action_performed[] = "UPDATE books";
		}		
		
		if(Input::has('deleteUserbook'))
		{
			$this->deleteBook(head(Input::get('deleteUserbook')));
			Event::fire('veer.message.center', \Lang::get('veeradmin.book.delete'));
			$this->action_performed[] = "DELETE book";
			return null;
		}
		
		// discounts
		if(Input::has('cancelDiscount'))
		{
			\Veer\Models\UserDiscount::where('id','=', head(Input::get('cancelDiscount')))
				->update(array('status' => 'canceled'));
			Event::fire('veer.message.center', \Lang::get('veeradmin.discount.cancel'));
			$this->action_performed[] = "CANCEL discount";
		}
		
		if(Input::has('attachDiscounts'))
		{
			$discounts = $this->parseIds(Input::get('attachDiscounts'));
			$this->associate("UserDiscount", $discounts, $id, "users_id", "id", "users_id = 0 and status = 'wait'");
			Event::fire('veer.message.center', \Lang::get('veeradmin.discount.attach'));
			$this->action_performed[] = "ATTACH discount";			
		}
		
		// orders & bills
		$this->shopActions();
		
		// communications
		if(Input::has('sendMessageToUser'))
		{
			app('veer')->communicationsSend(Input::get('communication', array()));
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.page.sendmessage'));
			$this->action_performed[] = "SEND message to user";
		}

		if($action == "add") {
			$this->skipShow = true;
			Input::replace(array('id' => $id));
			return \Redirect::route('admin.show', array('users', 'id' => $id));	
		}	
	}
	
	
	/*
	 * Shop Actions: 
	 * - Bills: update, delete, send|paid|cancel
	 * -
	 */
	protected function shopActions()
	{
		// orders
		// TODO: move to app(veershop)
		if(Input::has('pin'))
		{
			$pin = key(Input::get('pin'));
			\Veer\Models\Order::where('id','=',head(Input::get('pin')))
				->update(array('pin' => $pin == 1 ? 0 : 1));
		}
		
		if(Input::has('updateOrderStatus'))
		{
			$history = Input::get('history.'.Input::get('updateOrderStatus'));
			array_set($history, 'orders_id', Input::get('updateOrderStatus'));
			array_set($history, 'name', 
				\Veer\Models\OrderStatus::where('id','=', array_get($history, 'status_id', null))
					->pluck('name')
				);
			$sendEmail = array_pull($history, 'send_to_customer', false);
			
			$update = array('status_id' => array_get($history, 'status_id'));
		
			$progress = array_pull($history, 'progress');
			if(!empty($progress)) $update['progress'] = $progress;
			
			\Eloquent::unguard();
			\Veer\Models\OrderHistory::create($history);
			\Veer\Models\Order::where('id','=',Input::get('updateOrderStatus'))
				->update($update);
			
			// TODO: send to user: sendEmail
		}
		
		if(Input::has('updatePaymentHold'))
		{
			\Veer\Models\Order::where('id','=',head(Input::get('updatePaymentHold')))
				->update(array('payment_hold' => key(Input::get('updatePaymentHold'))));
		}
		
		if(Input::has('updatePaymentDone'))
		{
			\Veer\Models\Order::where('id','=',head(Input::get('updatePaymentDone')))
				->update(array('payment_done' => key(Input::get('updatePaymentDone'))));
		}
		
		if(Input::has('updateShippingHold'))
		{
			\Veer\Models\Order::where('id','=',head(Input::get('updateShippingHold')))
				->update(array('delivery_hold' => key(Input::get('updateShippingHold'))));
		}
		
		if(Input::has('updateOrderClose'))
		{
			\Eloquent::unguard();
			\Veer\Models\Order::where('id','=',head(Input::get('updateOrderClose')))
				->update(array('close' => key(Input::get('updateOrderClose')), "close_time" => now()));
		}
		
		if(Input::has('updateOrderHide'))
		{
			\Veer\Models\Order::where('id','=',head(Input::get('updateOrderHide')))
				->update(array('hidden' => key(Input::get('updateOrderHide'))));
		}
		
		if(Input::has('updateOrderArchive'))
		{
			\Veer\Models\Order::where('id','=',head(Input::get('updateOrderArchive')))
				->update(array('archive' => key(Input::get('updateOrderArchive'))));
		}
		
		// bills
		if(Input::has('updateBillStatus'))
		{
			$billUpdate = Input::get('billUpdate.'.Input::get('updateBillStatus'));
			
			\Veer\Models\OrderBill::where('id','=',Input::get('updateBillStatus'))
				->update(array('status_id' => array_get($billUpdate, 'status_id')));
			
			if(array_has($billUpdate, 'comments'))
			{
				array_set($billUpdate, 'name', 
				\Veer\Models\OrderStatus::where('id','=', array_get($billUpdate, 'status_id'))
					->pluck('name')
				);
				\Eloquent::unguard();
				\Veer\Models\OrderHistory::create($billUpdate);				
			}
			
			//\Veer\Models\Order::where('id','=',array_get($billUpdate, 'orders_id'))
			//	->update(array('status_id' => array_get($billUpdate, 'status_id')));
		}		
		
		if(Input::has('updateBillSend'))
		{
			\Veer\Models\OrderBill::where('id','=',head(Input::get('updateBillSend')))
				->update(array('sent' => true));
			// TODO: SendMail
		}
		
		if(Input::has('updateBillPaid'))
		{
			\Veer\Models\OrderBill::where('id','=',head(Input::get('updateBillPaid')))
				->update(array('paid' => key(Input::get('updateBillPaid'))));
		}	
		
		if(Input::has('updateBillCancel'))
		{
			\Veer\Models\OrderBill::where('id','=',head(Input::get('updateBillCancel')))
				->update(array('canceled' => key(Input::get('updateBillCancel'))));
		}
		
		if(Input::has('deleteBill'))
		{
			\Veer\Models\OrderBill::where('id','=',Input::get('deleteBill'))
				->delete();
		}
		
		if(Input::has('addNewBill') && Input::has('billCreate.fill.orders_id'))
		{
			$fill = Input::get('billCreate.fill');
			
			$order = \Veer\Models\Order::find(array_get($fill, 'orders_id'));
			$status = \Veer\Models\OrderStatus::find(array_get($fill, 'status_id'));
			
			$payment = $payment_method = array_get($fill, 'payment_method');
			
			if(empty($payment))	
			{
				$payment = \Veer\Models\OrderPayment::find(array_get($fill, 'payment_method_id'));
				$payment_method = isset($payment->name) ? $payment->name : $payment_method;
			}
			
			$content = '';
			
			if(Input::has('billCreate.template'))
			{
				$content = view("components.bills.".Input::get('billCreate.template'), array(
					"order" => $order,
					"status" => $status,
					"payment" => $payment,
					"price" => array_get($fill, 'price')
				))->render();
			}
			
			\Eloquent::unguard();
			
			$b = new \Veer\Models\OrderBill;
			
			$b->fill($fill);
			$b->users_id = isset($order->users_id) ? $order->users_id : 0;
			$b->payment_method = $payment_method;
			$b->content = $content;
					
			if(Input::has('billCreate.fill.sendTo'))
			{
				$b->sent = true;
				// TODO: SendMail
			}
			
			$b->save();
		}
	}
	
	
	/** 
	 * update Administrator
	 * TODO: use ranks to determine who can update whom
	 */
	protected function updateOneAdministrator($administrator, $id)
	{
		$administrator['banned'] = array_get($administrator, 'banned', false) ? true : false;
		
		if($id == \Auth::id()) array_forget($administrator, 'banned');
			
		\Veer\Models\UserAdmin::where('users_id','=',$id)
			->update($administrator);
	}
	
	
	
	/*
	 * update Statuses
	 */
	public function updateStatuses()
	{
		\Eloquent::unguard();

		if(Input::has('updateGlobalStatus'))
		{
			$status_id = Input::get('updateGlobalStatus');
			
			$s = \Veer\Models\OrderStatus::find($status_id);
			
			if(is_object($s))
			{
				$this->addOrUpdateGlobalStatus($s, $status_id);
				Event::fire('veer.message.center', \Lang::get('veeradmin.status.update'));
				$this->action_performed[] = "UPDATE status";
			}
		}
		
		if(Input::has('deleteStatus'))
		{
			$this->deleteStatus(Input::get('deleteStatus'));
			Event::fire('veer.message.center', \Lang::get('veeradmin.status.delete'));
			$this->action_performed[] = "DELETE status";
		}
		
		if(Input::has('addStatus'))
		{
			foreach(Input::get('InName') as $key => $value)
			{
				if(!empty($value))
				{
					$this->addOrUpdateGlobalStatus(new \Veer\Models\OrderStatus, $key);
				}
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.status.new'));
			$this->action_performed[] = "NEW status";			
		}
	}
	
	
	/**
	 * add or update global status (query)
	 * @param type $s
	 * @param type $status_id
	 */
	protected function addOrUpdateGlobalStatus($s, $status_id)
	{
		$s->name = Input::get('InName.'.$status_id);
		$s->manual_order = Input::get('InOrder.'.$status_id, $status_id);
		$s->color = Input::get('InColor.'.$status_id, '#000');
				
		$flag = Input::get('InFlag.'.$status_id);
				
		$flags = array('flag_first' => 0,'flag_unreg' => 0, 'flag_error' => 0,
				'flag_payment' => 0, 'flag_delivery' => 0, 'flag_close' => 0,
				'secret' => 0);
				
		if(!empty($flag)) $flags[$flag] = true;

		$s->fill($flags);
		$s->save();		
	}
	
	
	/**
	 * delete Status
	 */
	protected function deleteStatus($id)
	{
		\Veer\Models\Order::where('status_id','=',$id)
			->update(array('status_id' => 0));
		
		\Veer\Models\OrderBill::where('status_id','=',$id)
			->update(array('status_id' => 0));
		
		\Veer\Models\OrderHistory::where('status_id','=',$id)
			->update(array('status_id' => 0));
		
		\Veer\Models\OrderStatus::destroy($id);
	}
	
	
	/**
	 * update Payment Methods
	 */
	public function updatePayment()
	{
		if(Input::has('deletePaymentMethod'))
		{
			Event::fire('veer.message.center', \Lang::get('veeradmin.payment.delete'));
			$this->action_performed[] = "DELETE payment method";
			return $this->deletePaymentMethod(Input::get('deletePaymentMethod'));
		}
		
		if(Input::has('updatePaymentMethod'))
		{
			$p = \Veer\Models\OrderPayment::find(Input::get('updatePaymentMethod'));
			if(!is_object($p))
			{
				return Event::fire('veer.message.center', \Lang::get('veeradmin.payment.error'));
			}
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.payment.update'));
			$this->action_performed[] = "UPDATE payment";	
		}
		
		if(Input::has('addPaymentMethod'))
		{
			$p = new \Veer\Models\OrderPayment;
			Event::fire('veer.message.center', \Lang::get('veeradmin.payment.new'));
			$this->action_performed[] = "NEW payment";	
		}	
		
		$func_name = Input::get('payment.fill.func_name');
		
		if(!empty($func_name) && !class_exists('\\Veer\\Ecommerce\\' . $func_name)) 
		{
			return Event::fire('veer.message.center', \Lang::get('veeradmin.payment.error'));
		}
		
		$fill = Input::get('payment.fill');
		
		$fill['commission'] = strtr( array_get($fill, 'commission'), array("%" => ""));
		$fill['discount_price'] = strtr( array_get($fill, 'discount_price'), array("%" => ""));
		$fill['enable'] = isset($fill['enable']) ? true : false;
		$fill['discount_enable'] = isset($fill['discount_enable']) ? true : false;
		
		\Eloquent::unguard();
		
		$p->fill($fill);
		$p->save();
	}

	
	/**
	 * delete Payment Method
	 */
	protected function deletePaymentMethod($id)
	{
		\Veer\Models\Order::where('payment_method_id','=',$id)
			->update(array('payment_method_id' => 0));
		
		\Veer\Models\OrderBill::where('payment_method_id','=',$id)
			->update(array('payment_method_id' => 0));
				
		\Veer\Models\OrderPayment::destroy($id);
	}
	

	/**
	 * update Shipping Methods
	 */
	public function updateShipping()
	{
		if(Input::has('deleteShippingMethod'))
		{
			Event::fire('veer.message.center', \Lang::get('veeradmin.shipping.delete'));
			$this->action_performed[] = "DELETE shipping method";
			return $this->deleteShippingMethod(Input::get('deleteShippingMethod'));
		}
		
		if(Input::has('updateShippingMethod'))
		{
			$p = \Veer\Models\OrderShipping::find(Input::get('updateShippingMethod'));
			if(!is_object($p))
			{
				return Event::fire('veer.message.center', \Lang::get('veeradmin.shipping.error'));
			}
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.shipping.update'));
			$this->action_performed[] = "UPDATE shipping";	
		}
		
		if(Input::has('addShippingMethod'))
		{
			$p = new \Veer\Models\OrderShipping;
			Event::fire('veer.message.center', \Lang::get('veeradmin.shipping.new'));
			$this->action_performed[] = "NEW shipping";	
		}	
		
		$func_name = Input::get('shipping.fill.func_name');
		
		if(!empty($func_name) && !class_exists('\\Veer\\Ecommerce\\' . $func_name)) 
		{
			return Event::fire('veer.message.center', \Lang::get('veeradmin.shipping.error'));
		}
		
		$fill = Input::get('shipping.fill');
		
		$fill['discount_price'] = strtr( array_get($fill, 'discount_price'), array("%" => ""));
		$fill['enable'] = isset($fill['enable']) ? true : false;
		$fill['discount_enable'] = isset($fill['discount_enable']) ? true : false;
		
		if(array_has($fill, 'address'))
		{
			$addresses = preg_split('/[\n\r]+/', array_get($fill, 'address') );
			foreach($addresses as $k => $address)
			{
				$parts = explode("|", $address);
				$parts = array_filter($parts, function($value) { if(!empty($value)) return $value; });
				$addresses[$k] = $parts;
			}
			
			$fill['address'] = json_encode($addresses);	
		}

		\Eloquent::unguard();
		
		$p->fill($fill);
		$p->save();
	}

	
	/**
	 * delete Shipping Method
	 */
	protected function deleteShippingMethod($id)
	{
		\Veer\Models\Order::where('delivery_method_id','=',$id)
			->update(array('delivery_method_id' => 0));
				
		\Veer\Models\OrderShipping::destroy($id);
	}	
	
	
	/**
	 * update Discounts
	 */
	public function updateDiscounts()
	{
		if(Input::has('deleteDiscount'))
		{
			Event::fire('veer.message.center', \Lang::get('veeradmin.discount.delete'));
			$this->action_performed[] = "DELETE discount";
			return $this->deleteDiscount(Input::get('deleteDiscount'));
		}
		
		\Eloquent::unguard();
		
		if(Input::has('updateGlobalDiscounts'))
		{
			foreach(Input::get('discount', array()) as $key => $discount)
			{
				$fill = array_get($discount, 'fill');

				$fill['discount'] = strtr($fill['discount'], array("%" => ""));
				$fill['expires'] = isset($fill['expires']) ? true : false;

				if($key == "new") { //continue;
					if(array_get($fill, 'discount') > 0 && array_get($fill, 'sites_id') > 0) $d = new \Veer\Models\UserDiscount; 
				}
				
				else { $d = \Veer\Models\UserDiscount::find($key); }
				
				if(isset($d) && is_object($d))
				{
					$d->fill($fill);
					$d->save();
					unset($d);
				}
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.discount.update'));
			$this->action_performed[] = "UPDATE discount";			
		}
	}
	
	
	/**
	 * delete Discount
	 */
	protected function deleteDiscount($id)
	{
		return \Veer\Models\UserDiscount::destroy($id);
	}
	
	
	/**
	 * update Bills
	 */
	public function updateBills()
	{
		return $this->shopActions();	
	}
	
	
	/**
	 * update Orders
	 */
	public function updateOrders()
	{
		$this->shopActions();
		
		$editOneOrder = Input::get('id');
		
		if(!empty($editOneOrder)) 
		{ 	
			return $this->updateOneOrder($editOneOrder); 
		}		
	}
	
	
	/**
	 * update One Order
	 */
	public function updateOneOrder($id)
	{		
		echo "<pre>";
		print_r(Input::all());
		echo "</pre>";
		$action = Input::get('action');
		$fill = Input::get('fill');
		
		$siteId = Input::get('fill.sites_id');						
		if(empty($siteId)) $fill['sites_id'] = app('veer')->siteId;
		
		$usersId = Input::get('fill.users_id');
		if(empty($usersId) && $action != "add") $fill['users_id'] = \Auth::id();
		
		$order = \Veer\Models\Order::find($id);
		
		if(!is_object($order)) $order = new \Veer\Models\Order;
		
		if($action == "delete")
		{
			$this->deleteOrder($order);
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.order.delete'));
			$this->action_performed[] = "DELETE order";	
			$this->skipShow = true;
			return \Redirect::route('admin.show', array('orders'));
		}
		
		\Eloquent::unguard();
		
		$fill['free'] = isset($fill['free']) ? 1 : 0;
		$fill['close'] = isset($fill['close']) ? 1 : 0;
		$fill['hidden'] = isset($fill['hidden']) ? 1 : 0;
		$fill['archive'] = isset($fill['archive']) ? 1 : 0;
		$fill['delivery_free'] = isset($fill['delivery_free']) ? 1 : 0;
		$fill['delivery_hold'] = isset($fill['delivery_hold']) ? 1 : 0;
		$fill['payment_hold'] = isset($fill['payment_hold']) ? 1 : 0;
		$fill['payment_done'] = isset($fill['payment_done']) ? 1 : 0;
		
		if($fill['close'] == true) $fill['close_time'] = now();
		
		$fill['progress'] = isset($fill['progress']) ? strtr($fill['progress'], array("%" => "")) : 5;
		
		$deliveryPlan = array_get($fill, 'delivery_plan');
		$deliveryReal = array_get($fill, 'delivery_real');
		
		$fill['delivery_plan'] = !empty($deliveryPlan) ? parse_form_date($deliveryPlan) : NULL;
		$fill['delivery_real'] = !empty($deliveryReal) ? parse_form_date($deliveryReal) : NULL;
		
		if($order->cluster_oid != array_get($fill, 'cluster_oid') || $order->cluster != array_get($fill, 'cluster'))
		{
			$existingOrders = \Veer\Models\Order::where('sites_id','=',$fill['sites_id'])
				->where('cluster','=', array_get($fill, 'cluster'))
				->where('cluster_oid','=',array_get($fill, 'cluster_oid'))->first();
			
			// we cannot update cluster ids if they already exist
			if(isset($existingOrders) || array_get($fill, 'cluster_oid') == null) 
			{
				array_forget($fill, 'cluster_oid');
				array_forget($fill, 'cluster');
			}
		}
		
		if($order->status_id != array_get($fill, 'status_id', $order->status_id))
		{
			\Veer\Models\OrderHistory::create(array(
				"orders_id" => $order->id,
				"status_id" => array_get($fill, 'status_id'),
				"name" => \Veer\Models\OrderStatus::where('id','=',array_get($fill, 'status_id'))->pluck('name'),
				"comments" => "",
			));
		}
		
		if($order->delivery_method_id != array_get($fill, 'delivery_method_id', $order->delivery_method_id)	&& 
			array_get($fill, 'delivery_method') == null)
		{
			$fill['delivery_method'] = \Veer\Models\OrderShipping::where('id','=',array_get($fill, 'delivery_method_id'))->pluck('name');
		}
		
		if($order->payment_method_id != array_get($fill, 'payment_method_id', $order->payment_method_id)	&& 
			array_get($fill, 'payment_method') == null)
		{
			$fill['payment_method'] = \Veer\Models\OrderPayment::where('id','=',array_get($fill, 'payment_method_id'))->pluck('name');
		}
		
		$order->fill($fill);
		
		if($action == "add")
		{
			$order->pin = false;
			$order->close_time = null;
			$order->type = 'reg';
			
			if(empty($order->status_id))
			{
				$order->status_id = \Veer\Models\OrderStatus::firststatus()->pluck('id');
			}			
			
			$cluster = \Veer\Models\Configuration::where('sites_id','=',$order->sites_id)
				->where('conf_key','=','CLUSTER')->pluck('conf_val');
			if(empty($cluster)) $cluster = 0;
			
			$order->cluster = $cluster;
			$order->cluster_oid = \Veer\Models\Order::where('sites_id','=',$order->sites_id)
				->where('cluster','=',$cluster)->max('cluster_oid') + 1;
			
			if(empty($usersId) && !empty($order->email)) 
			{
				$findUser = \Veer\Models\User::where('sites_id','=',$order->sites_id)
					->where('email','=',$order->email)->first();
				
				if(is_object($findUser)) 
				{
					$order->users_id = $findUser->id;
				}

				else 
				{
					$newUser = new \Veer\Models\User;

					$newUser->sites_id = $order->sites_id;
					$newUser->email = $order->email;
					$newUser->phone = $order->phone;

					$password2Email = str_random(16);
					$newUser->password = $password2Email;
					$newUser->save();

					$order->users_id = $newUser->id;
					$order->type = 'unreg';
					
					$unregStatus = \Veer\Models\OrderStatus::unregstatus()->pluck('id');
					if(!empty($unregStatus)) $order->status_id = $unregStatus;					
				}	
			}
			
			$userRole = \Veer\Models\UserRole::whereHas('users', function($q) use ($order) {
				$q->where('users.id','=',$order->users_id);
			})->pluck('role');
			
			if(isset($userRole)) $order->user_type = $userRole;
			
			if(!empty($order->userdiscount_id))
			{
				$checkDiscount = \Veer\Models\UserDiscount::where('id','=',$order->userdiscount_id)
					->where('sites_id','=',$order->sites_id)
					->whereNested(function($q) use ($order) {
						$q->where('users_id','=',0)
							->orWhere('users_id','=',$order->users_id);
					})
					->whereNested(function($q) {
						$q->where('status','=','wait')
							->orWhere('status','=','active');
					})
					->first();
				
				if(is_object($checkDiscount))
				{
					$checkDiscount->users_id = $order->users_id;
					$checkDiscount->status = 'active';
					$checkDiscount->save();
				}
				
				else
				{
					$order->userdiscount_id = 0;
				}	
			}
			
			$book = Input::get('userbook.0', array());
			$book['fill']['users_id'] = $order->users_id;
			
			$newBook = app('veershop')->updateOrNewBook($book);
				
			if(isset($newBook) && is_object($newBook)) { 
				$order->userbook_id = $newBook->id;
				$order->country = $newBook->country;
				$order->city = $newBook->city;
				$order->address = trim( $newBook->postcode . " " . $newBook->address );
			}
	
			$order->hash = bcrypt($order->cluster.$order->cluster_oid.$order->users_id.$order->sites_id.str_random(16));
			$order->save();
		}
		
		// new book
		if($action == "addUserbook" || $action == "updateUserbook")
		{
			foreach(Input::get('userbook', array()) as $book)
			{
				$newBook = app('veershop')->updateOrNewBook($book);
				
				if(isset($newBook) && is_object($newBook)) { 
					$order->userbook_id = $newBook->id;
					$order->country = $newBook->country;
					$order->city = $newBook->city;
					$order->address = trim( $newBook->postcode . " " . $newBook->address );
				}
			}
		}
		
		// contents
		if(Input::has('editContent'))
		{
			$contentId = Input::get('editContent');
			
			$ordersProducts = Input::get('ordersProducts.' . $contentId . ".fill");
			
			$content = $this->editOrderContent( \Veer\Models\OrderProduct::find($contentId) , $ordersProducts, $order);
			
			$content->save();
		}
		
		if(Input::has('attachContent'))
		{
			$newContent = Input::get('attachContent');
			if(starts_with($newContent, ":"))
			{
				$parseContent = explode(":", substr($newContent,1) );
				foreach($parseContent as $product)
				{
					$p = explode(",", $product);
					if(array_get($p,0) != null)
					{
						$content = $this->editOrderContent( new \Veer\Models\OrderProduct, array(
							"product" => 1,
							"products_id" => array_get($p, 0),
							"quantity" => array_get($p, 1, 1),
							"attributes" => array_get($p, 2)
						), $order);
					
						$content->save();
					}
				}
			}
			
			else 
			{
				$parseContent = explode(":", $newContent);
				
				$content = new \Veer\Models\OrderProduct;			
				$content->orders_id = $order->id;
				$content->product = 0;
				$content->products_id = 0;
				$content->name = array_get($parseContent, 0, '[?]');
				$content->original_price = $content->price_per_one = array_get($parseContent, 1, 0);
				$content->quantity = array_get($parseContent, 2, 1);
				$content->price = $content->price_per_one * $content->quantity;
				$content->save();
			}
		}
		
		if(Input::has('deleteContent'))
		{
			\Veer\Models\OrderProduct::destroy(Input::get('deleteContent'));
		}
				
		// sums price & weight
		$order->content_price = $order->orderContent->sum('price'); 
		
		$order->used_discount = ($order->orderContent->sum('original_price')) - ($order->orderContent->sum('price_per_one'));
		
		if($order->used_discount < 0) { $order->used_discount = 0; }		
		else { $order->used_discount = round(($order->used_discount / $order->content_price) * 100, 2); }
		
		$order->weight = $order->orderContent->sum('weight');
		
		// recalculate delivery
		if($action == "recalculate" || $action == "add")
		{
			$order = $this->recalculateOrderDelivery($order);
			
			$order = $this->recalculateOrderPayment($order);
		}
		
		// total
		if($order->delivery_free == true) {	$order->price = $order->content_price; }
		else { $order->price = $order->content_price + $order->delivery_price; }
			
		// history
		if(Input::has('deleteHistory'))
		{
			\Veer\Models\OrderHistory::where('id','=',Input::get('deleteHistory'))->forceDelete();
			
			$previous = \Veer\Models\OrderHistory::where('orders_id','=',$order->id)->orderBy('id','desc')->first();
			if(is_object($previous))
			{
				$order->status_id = $previous->status_id;
			}
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.order.history.delete'));
			$this->action_performed[] = "DELETE history";
		}
				
		$order->save();

		// communications
		if(Input::has('sendMessageToUser'))
		{
			app('veer')->communicationsSend(Input::get('communication', array()));
			Event::fire('veer.message.center', \Lang::get('veeradmin.user.page.sendmessage'));
			$this->action_performed[] = "SEND message to user";
		}	
	}
	
	
	/**
	 * edit Content
	 */
	protected function editOrderContent($content, $ordersProducts, $order)
	{
		$productsId = array_get($ordersProducts, 'products_id');
		$attributes = array_pull($ordersProducts, 'attributes');
			
		$oldQuantity = isset($content->quantity) ? $content->quantity : 1;
		
		$content->orders_id = $order->id;
		
		$content->attributes = json_encode(explode(",", $attributes));
			
		$content->quantity = array_pull($ordersProducts, 'quantity', 1);
		if($content->quantity < 1) $content->quantity = 1;
		
		\Eloquent::unguard();

		if(empty($productsId)) 
		{
			$content->product = 0;
			$content->fill($ordersProducts);
			$content->price = $content->quantity * $content->price_per_one;
			return $content;
		}

		$content->product = 1;
		$content->name = array_get($ordersProducts, 'name');
		$content->original_price = array_get($ordersProducts, 'original_price');
		$content->price_per_one =  array_get($ordersProducts, 'price_per_one');
		
		if($content->quantity != $oldQuantity) 
		{
			$content->weight = (array_get($ordersProducts, 'weight') / $oldQuantity) * $content->quantity;
		} 
		
		else 
		{
			$content->weight = array_get($ordersProducts, 'weight');
		}
		
		if($content->products_id != array_get($ordersProducts, 'products_id') || !empty($content->attributes))
		{
			$product = \Veer\Models\Product::find(array_get($ordersProducts, 'products_id'));
		}
		
		// use attributes
		if(!empty($content->attributes) && is_object($product))
		{
			$attributesParsed = $this->parseAttributes($content->attributes, $content->id, $product);
			if(is_array($attributesParsed)) {
				foreach($attributesParsed as $attr) {
					$content->price_per_one = 
						$attr['pivot']['product_new_price'] > 0 ? $attr['pivot']['product_new_price'] : $content->price_per_one;
				}
			}
		}
		
		if($content->products_id != array_get($ordersProducts, 'products_id') && is_object($product))
		{
			$shopCurrency = \Veer\Models\Configuration::where('sites_id','=',$order->sites_id)
					->where('conf_key','=','SHOP_CURRENCY')->pluck('conf_val');
			$shopCurrency = !empty($shopCurrency) ? $shopCurrency : null;
			
			$content->products_id = $product->id;
			$content->original_price = empty($content->original_price) ? 
				app('veershop')->currency($product->price, $product->currency, array("forced_currency" => $shopCurrency)) : $content->original_price;
			$content->name = $product->title;
			$content->weight = $product->weight * $content->quantity;

			if(empty($content->price_per_one))
			{
				\Session::forget('discounts');
				\Session::forget('discounts_checked');
				\Session::forget('roles_id');
				\Session::forget('discounts_by_role_checked');
				\Session::forget('discounts_by_role');

				$pricePerOne = app('veershop')->calculator($product, false, array(
					"sites_id" => $order->sites_id,
					"users_id" => $order->users_id,
					"roles_id" => \Veer\Models\UserRole::where('role','=', $order->user_type)->pluck('id'),
					"discount_id" => $order->userdiscount_id,
					"forced_currency" => $shopCurrency 
				));

				$content->price_per_one = $pricePerOne;
			}
		}

		$content->price = $content->quantity * $content->price_per_one;

		$content->comments = array_get($ordersProducts, 'comments', '');
		
		return $content;
	}

	
	/**
	 * delete Order
	 */
	protected function deleteOrder($order)
	{
		if(is_object($order))
		{
			\Veer\Models\OrderHistory::where('orders_id','=',$order->id)->delete();
			$order->orderContent()->delete();			
			$order->bills()->delete();			
			$order->secrets()->delete();
			// communications skip
			
			$order->delete();		
		}
	}
	
	
	/**
	 * recalculate order delivery
	 */
	protected function recalculateOrderDelivery($order)
	{
		$delivery = \Veer\Models\OrderShipping::find($order->delivery_method_id);

		if(!is_object($delivery)) return $order;
		
		// change address if it's pickup
		if ($delivery->delivery_type == "pickup" && !empty($delivery->address)) 
		{
			// TODO: if we have several address how to choose the right one?
			// now it's just one address!
			$parseAddresses = json_decode($delivery->address);

			$order->country = array_get(head($parseAddresses), 0);
			$order->city = array_get(head($parseAddresses), 1);
			$order->address = array_get(head($parseAddresses), 2);
			$order->userbook_id = 0;
		}

		// 2
		switch ($delivery->payment_type) 
		{
			case "free":
				$order->delivery_price = 0;
				$order->delivery_free = true;
				$order->delivery_hold = false;
				break;
			
			case "fix":
				$order->delivery_price = $delivery->price;
				$order->delivery_free = false;
				$order->delivery_hold = false;
				break;
		}
		
		// 3 calculator
		if (!empty($delivery->func_name) && class_exists('\\Veer\\Ecommerce\\' . $delivery->func_name)) 
		{
			$class = '\\Veer\\Ecommerce\\' . $delivery->func_name;

			$deliveryFunc = new $class;

			$getData = $deliveryFunc->fire($order, $delivery);

			$order->delivery_price = isset($getData->delivery_price) ? $getData->delivery_price : $delivery->price;
			$order->delivery_free = isset($getData->delivery_free) ? $getData->delivery_free : false;
			$order->delivery_hold = isset($getData->delivery_hold) ? $getData->delivery_hold : true;

			$delivery->discount_enable = isset($getData->discount_enable) ? $getData->discount_enable : $delivery->discount_enable;
			$delivery->discount_price = isset($getData->discount_price) ? $getData->discount_price : $delivery->discount_price;
			$delivery->discount_conditions = isset($getData->discount_conditions) ? $getData->discount_conditions : $delivery->discount_conditions;
		}

		// 4
		if ($delivery->discount_enable == 1 && $delivery->discount_price > 0) 
		{
			$checkConditions = $this->checkDisountConditions($delivery->discount_conditions, $order);

			if (array_get($checkConditions, 'activate') == true || array_get($checkConditions, 'conditions') == false) 
			{
				if (array_get($checkConditions, 'price') == "total") 
				{
					$content = new \Veer\Models\OrderProduct;
					$content->orders_id = $order->id;
					$content->product = 0;
					$content->products_id = 0;
					$content->name = \Lang::get('veeradmin.order.content.delivery.discount') . " (-" . $delivery->discount_price . "%)";
					$content->original_price = 0 - ($order->content_price * ($delivery->discount_price / 100));
					$content->quantity = 1;
					$content->attributes = "";
					$content->comments = \Lang::get('veeradmin.order.content.discount');
					$content->price_per_one = $content->original_price;
					$content->price = $content->original_price;
					$content->save();

					$order->content_price = $order->content_price + $content->price;
				} 
				
				else 
				{
					$order->delivery_price = $order->delivery_price * ( 1 - ( $delivery->discount_price / 100));
				}
			}
		}

		if ($order->delivery_price <= 0 && $order->delivery_hold != true) 
		{
			$order->delivery_free = true;
		}

		return $order;
	}

	/**
	 * check Discount Conditions for Shipping|Payment
	 * @param type $custom_conditions
	 * @param type $order
	 * @return type
	 */
	protected function checkDisountConditions($custom_conditions, $order, $price_to_discount = "delivery")
	{
		$conditions_exist = false;
		$activate_discount = false;		
		
		$conditions = preg_split('/[\n\r]+/', $custom_conditions );
		
		if (count($conditions) > 0) 
		{
			foreach ($conditions as $c) 
			{
				if(empty($c)) continue;
				
				$parseCondition = explode(":", $c);
				
				$condition = array_get($parseCondition, 0);
				$value = array_get($parseCondition, 1);
				$value = trim($value);
				
				switch ($condition) 
				{
					case "$": // discount by price
						if ($order->content_price >= $value) $activate_discount = true;
						$conditions_exist = true;
						break;

					case "w": // discount by weight
						if ($order->weight >= $value) $activate_discount = true;
						$conditions_exist = true;
						break;
						
					case "l": // discount by location (country, city)
						if (str_contains($order->country, $value)) $activate_discount = true;
						if (str_contains($order->city, $value)) $activate_discount = true;
						$conditions_exist = true;
						break;
					
					case "la": // discount by location (address)
						if (str_contains($order->address, $value)) $activate_discount = true;
						$conditions_exist = true;
						break;
						
					case "pp": // discount by payment method (id)
						if ($order->payment_method_id == $value) $activate_discount = true;
						$conditions_exist = true;
						break;
						
					case "d": // price to change by discount ( content_price or only delivery price)
						$price_to_discount = $value;
						break;
					
					default:
						break;
				}
			}
		}

		return array(
			"conditions" => $conditions_exist, 
			"activate" => $activate_discount, 
			"price" => $price_to_discount);
	}
	
	
	/**
	 * recalculate Payment for Orders
	 * @param type $order
	 */
	protected function recalculateOrderPayment($order)
	{
        $payment = \Veer\Models\OrderPayment::find($order->payment_method_id);

		if(!is_object($payment)) return $order;
		
		// 1
		switch ($payment->paying_time) 
		{
			case "now":
				// TODO: redirect to payment system (but if admin then change to later)
				break;

			case "later":
				// TODO: create link to payment system and send it to user (save it somewhere)
				break;
		}
		
		// 2 calculator
		if (!empty($payment->func_name) && class_exists('\\Veer\\Ecommerce\\' . $payment->func_name)) 
		{
			$class = '\\Veer\\Ecommerce\\' . $payment->func_name;

			$paymentFunc = new $class;

			$getData = $paymentFunc->fire($order, $payment);

			$order->payment_done = isset($getData->payment_done) ? $getData->payment_done : false;
			$order->payment_hold = isset($getData->payment_hold) ? $getData->payment_hold : true;

			$payment->commission = isset($getData->commission) ? $getData->commission : $payment->commission;
			$payment->discount_enable = isset($getData->discount_enable) ? $getData->discount_enable : $payment->discount_enable;
			$payment->discount_price = isset($getData->discount_price) ? $getData->discount_price : $payment->discount_price;
			$payment->discount_conditions = isset($getData->discount_conditions) ? $getData->discount_conditions : $payment->discount_conditions;
		}
		
		// 3 
		if ($payment->commission > 0) 
		{
			$content = new \Veer\Models\OrderProduct;
			$content->orders_id = $order->id;
			$content->product = 0;
			$content->products_id = 0;
			$content->name = \Lang::get('veeradmin.order.content.payment.commission') . " (" . $payment->commission . "%)";
			$content->original_price = $order->content_price * ($payment->commission / 100);
			$content->quantity = 1;
			$content->attributes = "";
			$content->comments = \Lang::get('veeradmin.order.content.commission');
			$content->price_per_one = $content->original_price;
			$content->price = $content->original_price;
			$content->save();

			$order->content_price = $order->content_price + $content->price;
		}

		// 4
		if ($payment->discount_enable == 1 && $payment->discount_price > 0) 
		{
			$checkConditions = $this->checkDisountConditions($payment->discount_conditions, $order, "total");

			if (array_get($checkConditions, 'activate') == true || array_get($checkConditions, 'conditions') == false) 
			{
				if (array_get($checkConditions, 'price') == "total") 
				{
					$content = new \Veer\Models\OrderProduct;
					$content->orders_id = $order->id;
					$content->product = 0;
					$content->products_id = 0;
					$content->name = \Lang::get('veeradmin.order.content.payment.discount') ." (-" . $payment->discount_price . "%)";
					$content->original_price = 0 - ($order->content_price * ($payment->discount_price / 100));
					$content->quantity = 1;
					$content->attributes = "";
					$content->comments = \Lang::get('veeradmin.order.content.discount');
					$content->price_per_one = $content->original_price;
					$content->price = $content->original_price;
					$content->save();
					
					$order->content_price = $order->content_price + $content->price;
				}
				
				else
				{
					$order->delivery_price = $order->delivery_price * ( 1 - ( $payment->discount_price / 100));
				}
			}
		}
		
		return $order;
	}	
}
