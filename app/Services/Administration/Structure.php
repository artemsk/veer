<?php namespace Veer\Services\Administration;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

class Structure {
	
    use Elements\Helper, Elements\Attach;
    
    protected $action = null;

    public function __construct($t)
    {
        $this->action = 'update' . ucfirst($t);
        app('veeradmin')->skipShow = false;
    }
    
    public function handle()
    {
        return $this->{$this->action}();
    }
    
	/**
	 * Update Sites
	 * @return void 
	 */
	public function updateSites()
	{
        (new Elements\Site)->run();
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
		
			$this->deleteCategory($cid);
		}
		
		if($action == "add" && !empty($new)) {			
			$site_id = Input::get('siteid');				
			$this->addCategory($new, $site_id);			
			
			
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
				$oldsorting = (new \Veer\Services\Show\Category)->getAllCategories(Input::get('image',null));
				if(is_object($oldsorting)) {					
					foreach ($this->sortElements($oldsorting, $sorting) as $sort => $id) {
						\Veer\Models\Category::where('id', '=', $id)->update(array('manual_sort' => $sort));
					}	
				}			
			}	
			
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
	 * Update One Category
	 */	
	public function updateOneCategory($cid)
	{	
		$all = Input::all();

		// delete sub categories from db
		// deletecategoryid <- id of deleted category id
		if($all['action'] == "delete") { 			
			
			
			return $this->deleteCategory($all['deletecategoryid']); 
		}
		
		$category = \Veer\Models\Category::find($cid);
		
		
		// delete current category from db
		if($all['action'] == "deleteCurrent") {			
					
			$this->deleteCategory($cid);
			
			Input::replace(array('category' => null));
			
				
			Event::fire('veer.message.center', \Lang::get('veeradmin.category.delete') );	
			
			app('veeradmin')->skipShow = true;
			return \Redirect::route('admin.show', array('categories'));
		}
		
		
		// new & first parent of current category
		if($all['action'] == "saveParent" && isset($all['parentId']) && $all['parentId'] > 0) {
			
			$category->parentcategories()->attach($all['parentId']);
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.category.parent.new'));			
				
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
			
		}
		
		
		// updating info
		if($all['action'] == "updateCurrent") {
			$category->title = $all['title'];
			$category->remote_url = $all['remoteUrl'];
			$category->description = $all['description'];
			$category->save();
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.category.update'));
			
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
					
			   }
			}
		}
		
		
		// remove child from current
		if($all['action'] == "removeInChild") {
			$category->subcategories()->detach($all['currentChildId']);
			Event::fire('veer.message.center', \Lang::get('veeradmin.category.child.detach'));
			
		}
		
		
		// sort
		if($all['action'] == "sort") {
			$all['relationship'] = "subcategories";
			
			if(isset($all['parentid'])) {			
				$oldsorting[0] = (new \Veer\Services\Show\Category)->getCategoryAdvanced($all['parentid']);
				if(is_object($oldsorting[0])) {					
					foreach ($this->sortElements($oldsorting, $all) as $sort => $id) {
						\Veer\Models\Category::where('id', '=', $id)->update(array('manual_sort' => $sort));
					}					
				}				
			}
			
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

                $this->detachElements($all['action'], 'removeAllImages', $category, 'images', null, true);
		
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
			$p->url = trim(array_get($all, 'fill.url', ''));
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
		$all['fill']['url'] = trim($all['fill']['url']);

		if($action == "add" || $action == "saveAs") {
			
			$product = new \Veer\Models\Product;
			$product->fill($all['fill']);
			$product->status = "hide";
			$product->save();
			
			$id = $product->id;
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.new'));
			
		} else {
			$product = \Veer\Models\Product::find($id);
		}
	
		if($action == "update") {
			$product->fill($all['fill']);
			$product->save();
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.update'));
					
		}
		
		//status
		if($action == "updateStatus.".$id) 
		{
			$this->changeProductStatus($product);
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.status'));
			
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
			app('veeradmin')->skipShow = true;
			Input::replace(array('id' => $id));
			return \Redirect::route('admin.show', array('products', 'id' => $id));
		}
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
			$p->url = trim(array_get($all, 'url', ''));
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
                $all['fill']['url'] = trim($all['fill']['url']);

		if($action == "add" || $action == "saveAs") {
			
			$page = new \Veer\Models\Page;
			$page->fill($all['fill']);
			$page->hidden = true;
			$page->save();
			
			$id = $page->id;
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.new'));
			
		} else {
			$page = \Veer\Models\Page::find($id);
		}
	
		if($action == "update") {
			$page->fill($all['fill']);
			$page->save();
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.update'));
			
		}
		
		//status
		if($action == "changeStatusPage.".$id) 
		{
			if($page->hidden == true) { $page->hidden = false; } else { $page->hidden = true; }
			$page->save();
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.status'));
			
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
			app('veeradmin')->skipShow = true;
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
			
		}
		
		if(starts_with($action, "deleteProduct")) 
		{
			$r = explode(".", $action); 
			$this->deleteProduct($r[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.delete') . 
				" " . app('veeradmin')->restore_link('product', $r[1]));
			
		}		
		
		if(starts_with($action, "showEarlyProduct")) 
		{
			\Eloquent::unguard();
			$r = explode(".", $action); 
			\Veer\Models\Product::where('id','=',$r[1])->update(array("to_show" => now()));
			Event::fire('veer.message.center', \Lang::get('veeradmin.product.show'));
			
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
						
		}
		
		if(starts_with($action, "deletePage")) 
		{
			$r = explode(".", $action); 
			$this->deletePage($r[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.page.delete') . 
				" " . app('veeradmin')->restore_link('page', $r[1]));
			
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
						
		}
		
		if(starts_with(array_get($all, 'action'), 'deleteImage')) {
			$r = explode(".", $all['action']);
			$this->deleteImage($r[1]);
			Event::fire('veer.message.center', \Lang::get('veeradmin.image.delete'));
				
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
            $this->deletingLocalOrCloudFiles('images', $img->img, config("veer.images_path"));
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
			
		}
		
		if(starts_with($action, 'makeRealLink')) 
		{
			$times = Input::get('times', 0);
			$exdate = Input::get('expiration_day');
                        $linkname = Input::get('link_name');
			
			$r = explode(".", $action);
			$f = \Veer\Models\Download::find($r[1]);
			if(is_object($f)) {
				$newF = $f->replicate();
				$newF->secret = empty($linkname) ? str_random(100).date("Ymd", time()) : $linkname;
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
				
			}
		}
		
		if(starts_with($action, 'copyFile')) 
		{
			$r = explode(".", $action);
			$prdIds = explode(",", Input::get('prdId'));
			$pgIds = explode(",", Input::get('pgId'));
			$this->prepareCopying($r[1], $prdIds, $pgIds);
			
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.copy'));
					
		}
		
		if(Input::hasFile(Input::get('uploadFiles'))) {				
			$newId[] = $this->upload('file', 'uploadFiles', null, null, '', null, true);
			Event::fire('veer.message.center', \Lang::get('veeradmin.file.upload'));
						
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
				
			
		} elseif(Input::get('action') == "newAttribute") {
		
			$manyValues = preg_split('/[\n\r]+/', trim(Input::get('newValue')) );
			foreach($manyValues as $value) {
				$this->attachToAttributes(Input::get('newName'), $value);
			}
			Event::fire('veer.message.center', \Lang::get('veeradmin.attribute.new'));
					
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
	
	
	
	
}
