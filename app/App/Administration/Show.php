<?php namespace Veer\Administration;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

class Show {
	
	/**
	 * Show Sites
	 */
	public function showSites( $filters = array() ) 
	{	
		return \Veer\Models\Site::orderBy('manual_sort','asc')
			->get()->load(
				
				'subsites', 'categories', 'components', 'configuration', 
				'users', 'discounts', 'userlists', 'orders', 'delivery', 
				'payment', 'communications', 'roles', 'parentsite'
				
				); // elements separately		
	}
	
	/**
	 * Show Attributes
	 */
	public function showAttributes( $filters = array() ) 
	{	
		$items = \Veer\Models\Attribute::orderBy('name')
			->with('pages', 'products')->paginate(100);
				
		foreach($items as $key => $item) 
		{
			$items_grouped[$item->name][$key] = $key;
			
			$items_counted[$item->name]['prd'] = 
				( isset($items_counted[$item->name]['prd']) ? 
					$items_counted[$item->name]['prd'] : 0 ) + 
				($item->products->count());
			
			$items_counted[$item->name]['pg'] = 
				( isset($items_counted[$item->name]['pg']) ? 
				$items_counted[$item->name]['pg'] : 0 ) + 
				($item->pages->count()); 
		}

		$items['grouped'] = array();
		
		if(isset($items_grouped)) 
		{
			$items['grouped'] = $items_grouped;
			
			$items['counted'] = $items_counted;				
		}
		
		return $items;
	}	
	
	/**
	 * Show Tags
	 */
	public function showTags( $filters = array() ) 
	{	
		$items = \Veer\Models\Tag::orderBy('name', 'asc')
			->with('pages', 'products')->paginate(50);	
		
		$items['counted'] = \Veer\Models\Tag::count();

		return $items;
	}	
	
	/**
	 * Show Downloads
	 */
	public function showDownloads( $filters = array() ) 
	{	
		$items = \Veer\Models\Download::orderBy('fname','desc')
			->orderBy('id', 'desc')
			->with('elements')
			->paginate(50);	
		
		$items_temporary = 
			\Veer\Models\Download::where('original','=',0)->count();
		
		$items_counted = 
			\Veer\Models\Download::count(\Illuminate\Support\Facades\DB::raw(
				'DISTINCT fname'
			));
		
		foreach($items as $key => $item) 
		{
			$items_regrouped[$item->fname][$item->original][$key]=$key;
		}
		
		if(isset($items_regrouped)) 
		{ 
			$i = 0;
			
			foreach($items_regrouped as $key => $item) 
			{ 
				$items_index[$key] = $i; 
				
				$i++; 
			}
		}
		
		$items['temporary'] = $items_temporary;
		
		$items['counted'] = $items_counted;
		
		$items['regrouped'] = isset($items_regrouped) ? $items_regrouped : array();
		
		$items['index'] = isset($items_index) ? $items_index : array();
		
		return $items;
	}	
	
	/*
	 * build filter query on models with elements
	 */
	protected function buildFilterWithElementsQuery($filters, $model, $pluralize = true, $field = null)
	{
		$type = key($filters); 
			
		$filter_id = head($filters);
			
		if($filter_id != null && $type != "pages" && $type != "products" && $type != "categories" )
		{
			$items = $model::whereHas($type, function($query) use ($filter_id, $type, $pluralize, $field) 
			{
				if(!empty($field))
				{
					$query->where( $field, '=', $filter_id );
				}
				
				else 
				{
					$query->where( ($pluralize) ? str_plural($type).'_id' : $type.'_id', '=', $filter_id );
				}
			});
		}
		
		elseif($filter_id != null)
		{
			$items = $model::where('elements_type', '=', elements($type))
				->where('elements_id','=', $filter_id);
		}
		
		else 
		{
			$items = $model::select();
		}
		
		return $items;
	}
	
	/**
	 * Show Comments
	 */
	public function showComments($filters = array()) 
	{		
		$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Comment")->orderBy('id','desc')
			->with('elements')
			->paginate(50); 
			// users -> only for user's page
				
		$items['counted'] = \Veer\Models\Comment::count();
		
		return $items;
	}	
	
	/**
	 * Show Images
	 */
	public function showImages( $filters = array() ) 
	{	
		if(key($filters) == "unused") 
		{
			$items = \Veer\Models\Image::orderBy('id', 'desc')
				->has('pages','<',1)
				->has('products','<',1)
				->has('categories','<',1)
				->has('users','<',1)
				->paginate(25);	
		} 
		
		else 
		{
			$items = \Veer\Models\Image::orderBy('id', 'desc')
				->with(
					'pages', 'products', 'categories', 'users'
					)
				->paginate(25);	
		}
		
		$items['counted'] = \Veer\Models\Image::count();

		return $items;
	}	
	
	/**
	 * Show Categories
	 */
	public function showCategories($category = null, $image = null) 
	{	
		if($category == null || $image != null) 
		{
			return $this->showManyCategories($image);	
		} 		
		
		return $this->showOneCategory($category);
	}		
	
	/**
	 * show One Category
	 */
	public function showOneCategory($category) 
	{
		$items = \Veer\Models\Category::where('id','=',$category)
			->with(array(
			'parentcategories' => function ($query) 
			{ 
				$query->orderBy('manual_sort','asc'); 
			},
			'subcategories' => function ($query) 
			{ 
				$query->orderBy('manual_sort','asc')
					->with('pages', 'products', 'subcategories'); 
			}))
				->first();

		if(is_object($items)) 
		{
			$items->load('products', 'pages', 'images', 'communications');

			$items->pages->sortBy('manual_order');

			$items['site_title'] = 
				\Veer\Models\Configuration::where('sites_id','=',$items->sites_id)
				->where('conf_key','=','SITE_TITLE')
				->pluck('conf_val');
		}	
		
		return $items;
	}
	
	/**
	 * show Many Categories
	 * @params filter
	 */
	public function showManyCategories($image)
	{
		if(!empty($image)) 
		{
			$items = \Veer\Models\Site::with(
				array('categories' => function($query) use ($image) 
				{
					$query->whereHas('images',function($q) use ($image) 
					{
						$q->where('images_id','=',$image);					
					})
					->with('products', 'pages', 'subcategories');
				}))
					->orderBy('manual_sort','asc')
					->get();	

			$items['filtered'] = "images";
			
			$items['filtered_id'] = $image;

			return $items;
		} 

		return \Veer\Models\Site::with(
			array('categories' => function($query) 
			{
				$query->has('parentcategories', '<', 1)
					->orderBy('manual_sort','asc')
					->with('pages', 'products', 'subcategories');
			}))
				->orderBy('manual_sort','asc')
				->get();
	}
	
	/**
	 * Show Products
	 */
	public function showProducts($product = null, $filters = array()) 
	{	
		if(!empty($product)) 
		{ 			
			if($product == "new") 
			{ 
				return new \stdClass(); 
			}
			
			return $this->showOneProduct($product);
		}		
		
		$type = key($filters);
		
		$filter_id = head($filters);
				
		if(!empty($type) && !empty($filter_id) && $type != "site") 
		{
			$items = $this->showProductsFiltered($type, $filter_id);
			
			$items['filtered'] = $type;
			
			if($type == "attributes")
			{				
				$a = \Veer\Models\Attribute::where('id','=',$filter_id)
					->select('name', 'val')->first();

				if(is_object($a)) 
				{
					$items['filtered_id'] = $a->name.":".$a->val;
				}
			}
			
			elseif($type == "tags") 
			{
				$items['filtered_id'] = 
				\Veer\Models\Tag::where('id','=',$filter_id)->pluck('name');
			}
			
			else 
			{
				$items['filtered_id'] = $filter_id;
			}
			
			return $items;
		}
			
		if($type == "unused") 
		{
			$items = \Veer\Models\Product::has('categories','<',1);
		}
		
		elseif($type == "site" && !empty($filter_id))
		{
			$items = \Veer\Models\Product::whereHas('categories', function($query) use ($filter_id) {
				$query->where('sites_id', '=', $filter_id);
			});
		}
		
		else
		{
			$items = \Veer\Models\Product::select();
		}
		
		$items = $items->orderBy('id','desc')
			->with('images', 'categories')
			->paginate(25); 
		
		if(!empty($type)) 
		{ 
			$items['filtered'] = $type; 
		} 
		
		else 
		{ 
			$items['counted'] = \Veer\Models\Product::count();
		}
		
		if(!empty($filter_id)) { $items['filtered_id'] = $filter_id; }		
		
		return $items;
	}	
	
	/**
	 * show products with filter
	 * @param type [image, tag]
	 */
	public function showProductsFiltered($type, $filter_id) 
	{		
		return \Veer\Models\Product::whereHas($type, function($query) use ($filter_id, $type) 
		{
			$query->where( $type . '_id', '=', $filter_id);
		})
			->orderBy('id','desc')
			->with('images', 'categories')
			->paginate(25); 		
	}
	
	/**
	 * show One Product
	 * @param type $product
	 * @return type
	 */
	public function showOneProduct($product)
	{
		$items = \Veer\Models\Product::find($product);
			
		if(is_object($items)) 
		{
			$items->load(
				'subproducts', 'parentproducts', 'pages', 'categories', 
				'tags', 'attributes', 'images', 'downloads' );		

			$items['basket'] = $items->userlists()->where('name','=','[basket]')->count();
			
			$items['lists'] = $items->userlists()->where('name','!=','[basket]')->count();	
		}	
		
		return $items;
	}
	
	/**
	 * Show Pages
	 */
	public function showPages($page = null, $filters = array()) 
	{	
		if(!empty($page)) 
		{			
			if($page == "new") 
			{ 
				return new \stdClass(); 
			}
			
			return $this->showOnePage($page);
		}		
				
		$type = key($filters);
		
		$filter_id = head($filters);
				
		if(!empty($type) && !empty($filter_id) && $type != "site") 
		{
			$items = $this->showPagesFiltered($type, $filter_id);
			
			$items['filtered'] = $type;
			
			if($type == "attributes")
			{				
				$a = \Veer\Models\Attribute::where('id','=',$filter_id)
					->select('name', 'val')->first();
				
				if(is_object($a)) 
				{
					$items['filtered_id'] = $a->name.":".$a->val;
				}
			}
			
			elseif($type == "tags") 
			{
				$items['filtered_id'] = 
				\Veer\Models\Tag::where('id','=',$filter_id)->pluck('name');
			}
			
			else 
			{
				$items['filtered_id'] = $filter_id;
			}
			
			return $items;
		}
				
		if($type == "unused") 
		{
			$items = \Veer\Models\Page::has('categories','<',1);
		}
		
		elseif($type == "site" && !empty($filter_id))
		{
			$items = \Veer\Models\Page::whereHas('categories', function($query) use ($filter_id) {
				$query->where('sites_id', '=', $filter_id);
			});
		}
		
		else
		{
			$items = \Veer\Models\Page::select();
		}
		
		$items = $items->orderBy('id','desc')
			->with(
				'images', 'categories', 'user', 'subpages', 'comments'
				)->paginate(25); 

		if(!empty($type)) 
		{ 
			$items['filtered'] = $type; 
		} 
		
		else 
		{ 
			$items['counted'] = \Veer\Models\Page::count(); 
		}
		
		if(!empty($filter_id)) { $items['filtered_id'] = $filter_id; }
		
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
		return \Veer\Models\Page::whereHas($type, function($query) use ($filter_id, $type) 
		{
			$query->where( $type . '_id', '=', $filter_id );
		})
			->orderBy('id','desc')
			->with(
				'images', 'categories', 'user', 'subpages', 'comments'
			)->paginate(25);			
	}
	
	/**
	 * show one page
	 * @param type $page
	 * @return type
	 */
	public function showOnePage($page) 
	{
		$items = \Veer\Models\Page::find($page);
			
		if(is_object($items)) 
		{
			$items->load(
				'user', 'subpages', 'parentpages', 'products', 'categories', 
				'tags', 'attributes', 'images', 'downloads');
			
			$items['lists'] = 
				$items->userlists()->count(\Illuminate\Support\Facades\DB::raw(
					'DISTINCT name'
				));
			
			$items->markdownSmall = \Parsedown::instance()
				->setBreaksEnabled(true)
				->text($items->small_txt);
			$items->markdownTxt = \Parsedown::instance()
				->setBreaksEnabled(true)
				->text($items->txt);
			// TODO: test because of fatal errors
		}	
			
		return $items;
	}
	
	/**
	 * Show Configurations
	 */
	public function showConfiguration($siteId = null, $orderBy = array('id', 'desc')) 
	{	
		if(Input::get('sort', null)) 
		{ 
			$orderBy[0] = Input::get('sort');
		}
		
		if(Input::get('direction', null)) 
		{ 
			$orderBy[1] = Input::get('direction'); 
		}
		
		if($siteId == null) 
		{
			return \Veer\Models\Site::where('id','>',0)
				->with(
					array('configuration' => function($query) use ($orderBy) 
					{
						$query->orderBy($orderBy[0], $orderBy[1]);
					}))
				->get();
		}
		
		$items[0] = \Veer\Models\Site::with(
			array('configuration' => function($query) use ($orderBy) 
			{
				$query->orderBy($orderBy[0], $orderBy[1]);
			}))
				->find($siteId); 
			
		return $items;
	}
	
	/**
	 * Show Components
	 */
	public function showComponents($siteId = null, $orderBy = array('id', 'desc')) 
	{	
		if(Input::get('sort', null)) 
		{ 
			$orderBy[0] = Input::get('sort');
		}
		
		if(Input::get('direction', null)) 
		{ 
			$orderBy[1] = Input::get('direction');
		}
	
		if($siteId == null) {
			return \Veer\Models\Site::where('id','>',0)
				->with(
					array('components' => function($query) use ($orderBy) 
					{
						$query->orderBy('sites_id')
							->orderBy($orderBy[0], $orderBy[1]);
					}))
				->get();
		}
		
		$items = \Veer\Models\Site::with(
			array('components' => function($query) use ($orderBy) 
			{
				$query->orderBy('sites_id')->orderBy($orderBy[0], $orderBy[1]);
			}))
				->find($siteId); 
			
		return array($items);
	}	
	
	/**
	 * Show Secrets
	 */
	public function showSecrets( $filters = array() ) 
	{		
		$items = \Veer\Models\Secret::all();
		
		$items->sortByDesc('created_at');
			
		return $items;
	}	
	
	/**
	 * Show Jobs
	 */
	public function showJobs( $filters = array() ) 
	{		
		$items = \Artemsk\Queuedb\Job::all();
		
		$items->sortBy('scheduled_at');
		
		$items_failed = 
			\Illuminate\Support\Facades\DB::table("failed_jobs")->get();
		
		$statuses = array(
			\Artemsk\Queuedb\Job::STATUS_OPEN => "Open",
			\Artemsk\Queuedb\Job::STATUS_WAITING => "Waiting",
			\Artemsk\Queuedb\Job::STATUS_STARTED => "Started",
			\Artemsk\Queuedb\Job::STATUS_FINISHED => "Finished",
			\Artemsk\Queuedb\Job::STATUS_FAILED => "Failed"
		);
			
		return array(
			'jobs' => $items, 
			'failed' => $items_failed, 
			'statuses' => $statuses
		);
	}	
	
	/**
	 * show Users Filtered
	 */
	protected function showUsersFiltered($filter_id, $type)
	{
		return \Veer\Models\User::whereHas($type, function($query) use ($filter_id, $type) 
		{
			$query->where( str_plural($type).'_id', '=', $filter_id );
		});
	}
	
	/**
	* check if filter is active and get items 
	*/
	protected function isUsersFiltered($filters, $orderBy)
	{
		foreach($filters as $type => $filter_id)
		{
			if(!empty($filter_id)) 
			{ 
				$items = $this->showUsersFiltered($filter_id, $type);  
			}	
		}		
		
		if(!isset($items)) 
		{ 
			$items = \Veer\Models\User::select(); 
		} 
		
		return $items->orderBy($orderBy[0], $orderBy[1]);
	}
	
	/**
	 * show Users
	 */
	public function showUsers($userId = null, $filters = array(), $orderBy = array('created_at', 'desc'))
	{
		if(!empty($userId)) 
		{			
			if($userId == "new") { return new \stdClass(); }
			
			return $this->showOneUser($userId);
		}
		
		if(Input::get('sort', null)) 
		{ 
			$orderBy[0] = Input::get('sort'); 
		}
		
		if(Input::get('direction', null)) 
		{ 
			$orderBy[1] = Input::get('direction'); 
		}
		
		$items = $this->isUsersFiltered($filters, $orderBy)->with(
			'role', 'comments', 'communications',
			'administrator', 'pages', 'images'
			)
			->with(array('site' => function($q) 
			{
				$q->with(array('configuration' => function($query) 
				{
					$query->where('conf_key','=','SITE_TITLE');
				}));
			}))
			->paginate(25);
			
		$items['counted'] = \Veer\Models\User::count();
		
		return $items;		
	}
	
	/**
	 * show One User
	 * @param type $user
	 */
	public function showOneUser($user)
	{
		$items = \Veer\Models\User::find($user);
			
		if(is_object($items)) 
		{
			$items->load(
				'role', 'books', 'discounts',
				'orders', 'bills', 'administrator',
				'pages', 'images'
			);
			
			$items->load(array('site' => function($q) 
			{
				$q->with(array('configuration' => function($query) 
				{
					$query->where('conf_key','=','SITE_TITLE');
				}));
			}));
						
			$items['basket'] = $items->userlists()->where('name','=','[basket]')->count();
			
			$items['lists'] = $items->userlists()->where('name','!=','[basket]')->count();			
		}	
		
		return $items;
	}

	/**
	 * show Users Books
	 */
	public function showBooks($filters = array())
	{
		$type = key($filters);
		
		if($type == "orders") 
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserBook", $pluralize = false, "userbook_id");
		}
		
		else
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserBook");
		}
		
		$items = $items->orderBy('created_at','desc')
			->with('user', 'orders')
			->paginate(25);
		
		$items['counted'] =
			\Veer\Models\UserBook::count();
		
		return $items;
	}	
	
	/**
	 * show Users Lists
	 */
	public function showLists($filters = array())
	{
		$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserList")
			->orderBy('name','asc')
			->orderBy('created_at','desc')
			->with('user', 'elements')
			->with(array('site' => function($q) 
			{
				$q->with(array('configuration' => function($query) 
				{
					$query->where('conf_key','=','SITE_TITLE');
				}));
			}))
			->paginate(50);
		
		foreach($items as $key => $item)
		{
			$itemsRegroup[$item->users_id][$item->id] = $key;
			
			if($item->users_id > 0 && !isset($itemsUsers[$item->users_id])) 
			{
				$itemsUsers[$item->users_id] = $item->user;
			}
		}
		
		$items['regrouped'] = isset($itemsRegroup) ? $itemsRegroup : array();
		
		$items['users'] = isset($itemsUsers) ? $itemsUsers : array();
		
		$items['basket'] = 
			\Veer\Models\UserList::where('name','=','[basket]')->count();
		
		$items['lists'] = 
			\Veer\Models\UserList::where('name','!=','[basket]')->count();

		return $items;
	}
	
	/**
	 * show Searches
	 */
	public function showSearches($filters = array())
	{
		$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Search")
			->orderBy('times', 'desc')
			->orderBy('created_at', 'desc')
			->with('users')
			->paginate(50);
		
		$items['counted'] = 
			\Veer\Models\Search::count();
		
		return $items;
	}		
	
	/**
	 * show Communications
	 */
	public function showCommunications($filters = array())
	{
		$type = key($filters);
		
		if($type != "type" && $type != "url")
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Communication");
		} 
		
		else
		{
			$items = \Veer\Models\Communication::where($type, '=', array_get($filters, $type, 0));
		}
		
		$items = $items->orderBy('created_at', 'desc')
			->with('user', 'elements')
			->with(array('site' => function($q) 
			{
				$q->with(array('configuration' => function($query) 
				{
					$query->where('conf_key','=','SITE_TITLE');
				}));
			}))
			->paginate(25);
			
		foreach($items as $key => $item)
		{
			$itemsUsers[$key] = $this->parseCommunicationRecipients($item->recipients);
		}
		
		$items['recipients'] = isset($itemsUsers) ? $itemsUsers : array();
		
		$items['counted'] = 
			\Veer\Models\Communication::count();
		
		return $items;		
	}		
	
	/**
	 * parse communications
	 * @param string $recipients
	 */
	protected function parseCommunicationRecipients($recipients)
	{
		if(empty($recipients)) { return null; }
		
		$u = json_decode($recipients);
				
		if(!is_array($u)) { return null; } 
		
		foreach($u as $userId) 
		{
			$getUser = \Veer\Models\User::find($userId);
			
			$itemsUsers[$userId] = isset($getUser) ? $getUser : $userId;  
		}
		
		return $itemsUsers;
	}
	
	/**
	 * show Roles
	 */
	public function showRoles( $filters = array() )
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserRole")->orderBy('sites_id', 'asc')
			->with('users')
			->with(array('site' => function($q) 
			{
				$q->with(array('configuration' => function($query) 
				{
					$query->where('conf_key','=','SITE_TITLE');
				}));
			}))
			->paginate(50);
	}	
	
	/**
	 * show Orders
	 */
	public function showOrders( $order = null, $filters = array(), $orderBy = array('created_at', 'desc') )
	{
		$pinSkip = false;
		
		if(Input::get('sort', null)) 
		{ 
			$orderBy[0] = Input::get('sort');
			
			$pinSkip = true;
		}
		
		if(Input::get('direction', null)) 
		{ 
			$orderBy[1] = Input::get('direction'); 
		}
		
		$type = key($filters);
		
		if($type == "userbook" || $type == "userdiscount" || $type == "status") 
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Order", $pluralize = false, $type . "_id");
		}
		
		elseif( $type == "delivery" || $type == "payment")
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Order", $pluralize = false, $type . "_method_id");
		}
		
		elseif( $type == "status_history")
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Order", $pluralize = false, "status_id");
		}
		
		elseif( $type == "products")
		{
			$filter_id = head($filters);
			
			$items = \Veer\Models\Order::whereHas($type, function($query) use ($filter_id) 
			{
				$query->where( 'products_id', '=', $filter_id );
			});
		}
		
		elseif( $type == "site" || $type == "user" || empty($type))
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Order");
		}
		
		else
		{
			$items = \Veer\Models\Order::where($type, '=', head($filters));
		}
		
		if($type != "archive")
		{
			$items = $items->where('archive', '!=', true);
		}
		
		if($pinSkip === false) { $items = $items->orderBy('pin', 'desc'); }
		
		$this->counted['active'] = \Veer\Models\Order::where('archive', '!=', true)->count();
		
		$this->counted['archived'] = \Veer\Models\Order::where('archive', '=', true)->count();
		
		return $items->orderBy($orderBy[0], $orderBy[1])
			->with(
				'user', 'userbook', 'userdiscount', 'status', 'delivery', 'payment', 'bills')
			->with(array('site' => function($q) 
			{
				$q->with(array('configuration' => function($query) 
				{
					$query->where('conf_key','=','SITE_TITLE');
				}));
			}))
			->paginate(50);	
	}
	
	/**
	 * show Statuses
	 */
	public function showStatuses( $filters = array() )
	{
		return \Veer\Models\OrderStatus::orderBy('manual_order', 'asc')
			->with('orders', 'bills', 'orders_with_history')
			->paginate(50);
	}	
	
	/**
	 * show Shipping Methods
	 */
	public function showShipping( $filters = array() )
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderShipping")->orderBy('sites_id', 'asc')
			->with('orders')
			->with(array('site' => function($q) 
			{
				$q->with(array('configuration' => function($query) 
				{
					$query->where('conf_key','=','SITE_TITLE');
				}));
			}))->paginate(50);
	}		
	
	/**
	 * show Payment Methods
	 */
	public function showPayment( $filters = array() )
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderPayment")->orderBy('sites_id', 'asc')
			->with('orders', 'bills')
			->with(array('site' => function($q) 
			{
				$q->with(array('configuration' => function($query) 
				{
					$query->where('conf_key','=','SITE_TITLE');
				}));
			}))
			->paginate(50);
	}		
	
	/**
	 * show Discounts
	 */
	public function showDiscounts( $filters = array() )
	{
		$type = key($filters);
		
		if($type == "status")
		{
			$items = \Veer\Models\UserDiscount::where('status', '=', head($filters));
		}
		
		else
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\UserDiscount");
		}
		return $items->orderBy('created_at', 'desc')
			->with('user', 'orders')
			->with(array('site' => function($q) 
			{
				$q->with(array('configuration' => function($query) 
				{
					$query->where('conf_key','=','SITE_TITLE');
				}));
			}))
			->paginate(50);
	}	
	
	/**
	 * show Bills
	 */
	public function showBills($filters = array(), $orderBy = array('created_at', 'desc'))
	{
		if(Input::get('sort', null)) 
		{ 
			$orderBy[0] = Input::get('sort'); 
		}
		
		if(Input::get('direction', null)) 
		{ 
			$orderBy[1] = Input::get('direction'); 
		}
		
		$type = key($filters);
		
		if($type == 'order' || $type == 'user' || empty($type))
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderBill");
		} 
		
		elseif( $type == 'status')
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderBill", $pluralize = false);
		}
		
		elseif( $type == 'payment')
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderBill", $pluralize = false, "payment_method_id");
		}
		
		else 
		{
			$items = \Veer\Models\OrderBill::where($type, '=', array_get($filters, $type, 0));
		}
		
		return $items->orderBy($orderBy[0], $orderBy[1])
			->with(
				'order', 'user', 'status', 'payment'
			)->paginate(50);
	}	
			
}
