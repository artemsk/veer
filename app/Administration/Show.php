<?php namespace Veer\Administration;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Event;

class Show {
	
	use \Veer\Services\Traits\CommonTraits, \Veer\Services\Traits\HelperTraits;
	
	/* request from user */
	public $userRequest = false;
		
	/* */
	public $counted = null;
	
		
	
	
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
			->with($this->loadSiteTitle())
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
		
		if($type != "type" && $type != "url" && $type != "order")
		{
			$items = $this->buildFilterWithElementsQuery($filters, "\Veer\Models\Communication");
		} 
		
		elseif($type == "order")
		{
			$items = \Veer\Models\Communication::where('elements_type', '=', elements($type))
				->where('elements_id','=', head($filters));
		}
		
		else
		{
			$items = \Veer\Models\Communication::where($type, '=', array_get($filters, $type, 0));
		}
		
		$items = $items->orderBy('created_at', 'desc')
			->with('user', 'elements')
			->with($this->loadSiteTitle())
			->paginate(25);
			
		foreach($items as $key => $item)
		{
			$itemsUsers[$key] = $this->parseCommunicationRecipients($item->recipients);
		}
		
		$items['recipients'] = isset($itemsUsers) ? $itemsUsers : array();
		
		$items['counted'] = 
			\Veer\Models\Communication::count();
		
		$items['counted_unread'] = $this->showUnreadNumbers("communication");
		
		return $items;		
	}		
	
	/**
	 * show Unread Numbers
	 */
	public function showUnreadNumbers($model, $raw = null, $period = 5)
	{
		$modelFull = "\\" . elements( str_singular($model) );
			
		$numbers = $modelFull::where('created_at', '>=', app('veer')->getUnreadTimestamp( str_plural($model) ));
		
		if (!empty($raw)) { $numbers->whereRaw($raw); }
		
		$numbers = app('veer')->cachingQueries->makeAndRemember($numbers, 'count', $period, null, 'unreadNumbers'.$model);
		
		return $numbers > 0 ? $numbers : null;		
	}
	
	/**
	 * parse communications
	 * @param string $recipients
	 */
	protected function parseCommunicationRecipients($recipients)
	{
		if(empty($recipients)) { return null; }
		
		$u = json_decode($recipients);
		
		if(!is_array($u) || is_array($u) && count($u) < 1) { return null; } 
		
		$getUsers = \Veer\Models\User::whereIn('id', $u)->get();
	
		$itemsUsers = array();
		
		foreach($getUsers as $user) 
		{
			$itemsUsers[$user->id] = $user;
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
			->with($this->loadSiteTitle())
			->paginate(50);
	}	
	
	
	/**
	 * show Statuses
	 */
	public function showStatuses( $filters = array() )
	{
		$items = \Veer\Models\OrderStatus::orderBy('manual_order', 'asc');
		
		$items->with(array('orders' => function($q) {
				
			}, 'bills' => function($q) {
				
			}, 'orders_with_history' => function($q) {
				
			}));
			
		return $items->paginate(50);
	}	
	
	/**
	 * show Shipping Methods
	 */
	public function showShipping( $filters = array() )
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderShipping")->orderBy('sites_id', 'asc')
			->with('orders')
			->with($this->loadSiteTitle())->paginate(50);
	}		
	
	/**
	 * show Payment Methods
	 */
	public function showPayment( $filters = array() )
	{
		return $this->buildFilterWithElementsQuery($filters, "\Veer\Models\OrderPayment")->orderBy('sites_id', 'asc')
			->with('orders', 'bills')
			->with($this->loadSiteTitle())
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
			->with($this->loadSiteTitle())
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
		
		if(empty($this->billsTypes)) $this->getExistingBillTemplates();

		return $items->orderBy($orderBy[0], $orderBy[1])
			->with(
				'order', 'user', 'status', 'payment'
			)->paginate(50);
	}	
			
	
	/** 
	 * Search
	 * @param type $t
	 * @return boolean|object
	 */
	public function search($t)
	{
		$q = Input::get('SearchField');
		
		$model = null;
		$id = null;
		
		if(starts_with($q, '!'))
		{
			$parseSearch = explode(":", substr($q,1));
			
			$model = strtolower(array_get($parseSearch, 0));
			$id = array_get($parseSearch, 1);
		}
		
		$field = "id";
		
		if(!empty($model) && !empty($id))
		{
			switch ($model) {
				case "product": $t = "products"; break;
				case "page": $t = "pages"; break;
				case "category": $t = "categories"; $field = "category"; break;					
				case "user": $t = "users"; break;
				case "order": $t  = "orders"; break;
				default: return false; break;
			}
		}
		
		if(empty($model)) { 
			switch ($t) {
				case "books": $model = "UserBook"; break;
				case "lists": $model = "UserList"; break;
				case "roles": $model = "UserRole"; break;					
				case "statuses": $model = "OrderStatus"; break;
				case "payment": $model = "OrderPayment"; break;
				case "shipping": $model = "OrderShipping"; break;
				case "discounts": $model = "UserDiscount"; break;
				case "bills": $model = "OrderBill"; break;

				case "jobs": return false;
				case "etc": return false;

				default: $model = $t; break;
			}
		}
			
		$model = elements($model);
		
		if(!empty($id))
		{
			return \Redirect::route('admin.show',
				array($t, $field => $id));
		}
		
		$view = $t;
		
		switch ($t) {
				case "users": 
					$searchfields = array("email", "username", "firstname", "lastname", "phone"); break;
				case "books": 
					$searchfields = array("name", "country", "region", "city", 
						"postcode", "address", "nearby_station", "b_bank", "b_bik", "b_others"); break; 
				case "searches":
					$searchfields = array("q"); break;
				case "comments":
					$searchfields = array("author", "txt", "rate"); break;
				case "pages":
					$searchfields = array("title", "small_txt", "txt"); break;
				case "products":
					$searchfields = array("title", "descr", "production_code"); break;
				case "tags":
					$searchfields = array("name"); break;
				case "orders":
					$searchfields = array("id", "cluster_oid", "email", "phone"); break;
				case "bills":
					$searchfields = array("id", "orders_id"); break;
				
				// TODO: leftovers: categories, attributes,
				//case "attributes":
				//	$searchfields = array("name", "val", "descr"); break;
				//case "communications":
					//$searchfields = array("sender", "sender_phone", "sender_email",
					//	"message", "recipients", "theme"); break;
					//	TODO: recipients
				//case "lists":
				//	$searchfields = array("name");
				//	$view = "userlists"; break;
				// TODO: regroup
				default: 
					return false;
					//$searchfields = array("id"); break;
			}
		
		if(isset($searchfields))
		{
			$items = $model::whereNested(function($query) use($q, $searchfields) {
							foreach($searchfields as $s)
							{
								$query->orWhere($s, 'like', '%'.$q.'%');
							}
						})->paginate(25);	
		}
		
		if(isset($items) && is_object($items))
		{
			return view(app('veer')->template.'.'.$view, array(
				"items" => $items,
				"data" => app('veer')->loadedComponents,
				"template" => app('veer')->template
			));
		}
			
		return false;
	}
}
