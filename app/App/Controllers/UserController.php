<?php

use Veer\Models\UserList;

class UserController extends \BaseController {

	public function __construct()
	{
		parent::__construct();
		
		$this->beforeFilter('auth', array('only' => array('index', 'show')));
	}
	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = app('veerdb')->route(Auth::id());
		
		if(!is_object($user)) { return Redirect::route('index'); }
		
		return $this->showUser($user);
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
		$administrator = administrator();
		
		if($administrator == false) { return $this->index(); }
		
		$user = app('veerdb')->make('user.index', $id);
		
		if(!is_object($user)) { return Redirect::route('index'); }
		
		return $this->showUser($user);	
	}

	
	/**
	 * Display User page
	 *
	 * @param  int  $iuser
	 * @return Response
	 */
	protected function showUser($user) {
		
		$user->load("role", "comments", "books", "discounts", "userlists", "orders", 
			"bills", "communications", "administrator", "searches", "pages");

		// TODO: разбить на отдельные страницы
		
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.user', array(
			"user" => $user,
			"data" => $data,
			"template" => $data['template']
		)); 

		$this->view = $view; 

		return $view;		
	}	

	// TODO: делать для пользователя статистику на основе заказов/комментариев/лайков/посещений? - свойства, слова, товары, разделы и тд
	
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


	/**
	 * Add product to shopping cart
	 *
	 * @return Response
	 */
	public function addToCart($id = null)
	{
		if(isset($id)) {
			
			$product = \Veer\Models\Product::sitevalidation(app('veer')->siteId)
				->where('id','=',$id)->checked()->first();
			if(is_object($product)) 
			{	
				$userid = \Illuminate\Support\Facades\Auth::id();
					
				$this->savingEntity($product, $userid);
				
				$items = app('veerdb')->userLists(app('veer')->siteId, $userid);
				
				Session::put('shopping_cart_items',$items);
			}
		}
		
		return stored();
	}	
	
	
	/**
	 * Add page or product to user list
	 *
	 * @return Response
	 */
	public function addToList($type = null, $id = null)
	{
		$userid = \Illuminate\Support\Facades\Auth::id();
		
		if(isset($id))
		{
			switch ($type) {
				case "product": $e = \Veer\Models\Product::sitevalidation(app('veer')->siteId)
					->where('id','=',$id)->checked()->first(); break;
				case "page": $e = \Veer\Models\Page::sitevalidation(app('veer')->siteId)
					->where('id','=',$id)->excludeHidden()->first(); break;
			}
			
			if(is_object($e)) $this->savingEntity($e, $userid, Input::get('name','[basket]'));
		}
		
		return  app('veerdb')->userLists(app('veer')->siteId, $userid, Input::get('name','[basket]'));
	}	
	
	
	/**
	 * saving Entity to db
	 * @param type $e
	 */
	protected function savingEntity($e, $userid, $name = "[basket]")
	{		
		$cart = new UserList;
		$cart->sites_id = app('veer')->siteId;
		$cart->users_id = empty($userid) ? 0 : $userid;
		$cart->session_id = Session::getId();
		$cart->name = $name;                           
		$cart->quantity = 1;
		if(Input::has('attributes') && is_array(Input::get('attributes'))) {
			$cart->attributes = json_encode(Input::get('attributes'));
		}				
		$cart->save();
		
		$e->userlists()->save($cart);  // another query
	}
	
	
	/**
	 * remove Entity from Cart
	 */
	public function removeFromCart($cartId)
	{
		$this->removeFromList($cartId);
		
		$items = app('veerdb')->userLists(app('veer')->siteId, \Auth::id());
				
		Session::put('shopping_cart_items',$items);
	}
	
	
	/**
	 * remove Entity from List
	 */
	public function removeFromList($listId)
	{
		$userid = \Illuminate\Support\Facades\Auth::id();
		
		\Veer\Models\UserList::where('sites_id','=', app('veer')->siteId)
			->where(function($query) use ($userid) {
				if($userid > 0) {
				$query->where('users_id','=', empty($userid) ? 0 : $userid)
					->orWhere('session_id','=', app('session')->getId());	
				} else {
				$query->where('users_id','=', empty($userid) ? 0 : $userid)
					->where('session_id','=', app('session')->getId());							
				}
		})->where('id','=', $listId)->delete();	
	}
	
	
	/**
	 * Login Form
	 *
	 * @return Response
	 */
	public function login()
	{
		$data = $this->veer->loadedComponents;
                
		$view = view($this->template.'.login', array(
			"data" => $data,
			"template" => $data['template']
		)); 

		//$this->view = $view; // do not cache

		return $view;  
	}

	
	/**
	 * Logout
	 */
	public function logout()
	{
		Session::flush();
		
		Auth::logout();
		
		if(!app('request')->ajax()) return Redirect::route('index'); 
	}
	

	/**
	 * Login Post
	 *
	 * @return Response
	 */
	public function loginPost()
	{
		$save_old_session_id = Session::getId();
			
        if (Auth::attempt(array('email' => \Input::get('email'), 'password' => \Input::get('password'), 
			'banned' => 0, 'sites_id' => app('veer')->siteId)))
        {
			Auth::user()->increment('logons_count');
			
			Session::put('roles_id', Auth::user()->roles_id);
			
			UserList::where('session_id','=',$save_old_session_id)->update(array('users_id' => Auth::id()));
			
			Session::put('shopping_cart_items', 
				app('veerdb')->userLists(app('veer')->siteId, Auth::id()));
					
			if(administrator() == true) {
				\Veer\Models\UserAdmin::where('id','=',app('veer')->administrator_credentials['id'])->
					update(array(
						"sess_id" => Session::getId(),
						"last_logon" => now(),
						"ips" => \Illuminate\Support\Facades\Request::getClientIp(),
						"logons_count" => app('veer')->administrator_credentials['logons_count'] + 1
					));
			}
			
			return Redirect::intended();
        } 
		
		return $this->login();
	}        
     
	
	/**
	 * add Comment
	 */
	public function addComment()
	{
		$added = false;
		
		$anonymAllow = db_parameter("NEW_COMMENT_ANONYM", true);
		
		if( ($anonymAllow == false && Auth::id() > 0) || $anonymAllow == true )
		{
			$data = Input::all();

			array_set($data, 'fill.users_id', Auth::id());			

			array_set($options, 'checkboxes.hidden', db_parameter('NEW_COMMENT_HIDDEN', false));

			$added = app('veer')->commentsSend($data, $options);
		}

		return (int)$added;
	}
        
	
	/**
	 * add Communication
	 */
	public function addCommunication()
	{
		$added = false;
		
		$data = Input::all();

		array_set($data, 'communication.fill.users_id', Auth::id());			
		array_set($data, 'communication.fill.sites_id', app('veer')->siteId);	
		
		$validator = \Validator::make( array_get($data, 'communication.fill'), array(
				'users_id' => 'required_without_all:sender_email,sender_phone',
				'sender_phone' => 'required_without_all:users_id,sender_email',
				'sender_email' => 'required_without_all:users_id,sender_phone'
			));
		
		if(!$validator->fails())
		{
			array_set($data, 'communication.checkboxes.public', db_parameter('NEW_COMMUNICATION_PUBLIC', true));
			array_set($data, 'communication.checkboxes.email_notify', db_parameter('NEW_COMMUNICATION_EMAIL', true));
			array_set($data, 'communication.checkboxes.hidden', db_parameter('NEW_COMMUNICATION_HIDDEN', false));
			array_set($data, 'communication.checkboxes.intranet', db_parameter('NEW_COMMUNICATION_INTRANET', false));

			$added = app('veer')->communicationsSend( array_get($data, 'communication') );
		}
		
		return (int)$added;
	}
	
	
	/**
	 * register
	 */
	public function register()
	{
		$data = $this->veer->loadedComponents;
                
		$view = view($this->template.'.register', array(
			"data" => $data,
			"template" => $data['template']
		)); 

		return $view;  
	}
	
	
	/**
	 * register Post
	 */
	public function registerPost()
	{
		\Event::fire('router.filter: csrf');
		
		$fill = Input::get('fill');
				
		$fill['sites_id'] = app('veer')->siteId;
		
		if(array_has($fill, 'password') && empty($fill['password'])) array_forget($fill, 'password');
		
		$rules = array(
			'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL,sites_id,' . app('veer')->siteId,
			'password' => 'required|min:6',
		);			

		$validator = \Validator::make($fill, $rules);
			
		if($validator->fails()) 
		{ 
			return Redirect::route('user.register')->withErrors($validator);		
		}
		
		\Eloquent::unguard();
		
		$user = new \Veer\Models\User;
		
		$fill['restrict_orders'] = db_parameter('ECOMMERCE_RESTRICT_ORDERS', config('veer.restrict_orders', false));
		
		$fill['newsletter'] = isset($fill['newsletter']) ? true : false;
		$fill['birth'] = parse_form_date(array_get($fill, 'birth'));
		
		$user->fill($fill);
		
		$user->save();		
		
		\Auth::login($user);
		
		return Redirect::intended();	
	}
	
	/**
	 * show cart
	 * @return type
	 */
	public function showCart()
	{
		$data = $this->veer->loadedComponents;
        
		// prepare products		
		$cart = app('veerdb')->route(\Auth::id());   
		
		$grouped = array();
		
		foreach($cart as $entity)
		{
			$attributes_flag = empty($entity->attributes) ? 0 : 1;
			$group_id = empty($entity->attributes) ? $entity->elements_id : $entity->id;
			
			if(isset($grouped[$attributes_flag . "." . $group_id])) 
			{
				$grouped[$attributes_flag . "." . $group_id]->quantity = 
					$grouped[$attributes_flag . "." . $group_id]->quantity + $entity->quantity;
				
				$attributes = json_decode($grouped[$attributes_flag . "." . $group_id]->attributes);
				$newAttributes = json_decode($entity->attributes);
				$mergedAttributes = array_merge((array)$attributes, (array)$newAttributes);
				
				if(is_array($mergedAttributes) && count($mergedAttributes) > 0) { 
					$grouped[$attributes_flag . "." . $group_id]->attributes = json_encode($mergedAttributes); }
			}
			
			else 
			{
				$grouped[$attributes_flag . "." . $group_id] = $entity;
			}
		}
		
		// show user books
		if(\Auth::id() > 0) $userbooks = \Auth::user()->books;
		
		// pre- order calculations
		
		$order = new \Veer\Models\Order;
		
		$order->sites_id = app('veer')->siteId;
		$order->users_id = \Auth::id();
		
		app('veershop')->basicOrderParameters($order);
		
		list($order, $checkDiscount) = app('veershop')->addNewOrder($order, \Auth::id(), array(), true);
		
		foreach($grouped as $entity)
		{
			$order->orderContent->push(app('veershop')->editOrderContent(new \Veer\Models\OrderProduct, array(
				"product" => 1,
				"products_id" => $entity->elements_id,
				"quantity" => $entity->quantity,
				"attributes" => $entity->attributes
			), $order, true));
		}
		
		app('veershop')->sumOrderPricesAndWeight($order);
		
		$calculations = array();
		
		$saveContentPrice = $order->content_price;
		$saveDeliveryPirce = $order->delivery_price;
		
		foreach(shipping(app('veer')->siteId) as $method)
		{
			$order->delivery_method_id = $method->id;
			
			app('veershop')->recalculateOrderDelivery($order, $method, true);	
			
			$calculations['shipping'][$method->id] = array(
				"method" => $method->toArray(),
				"delivery_price" => $order->delivery_price,
				"delivery_hold" => $order->delivery_hold,
				"delivery_free" => $order->delivery_free,
				"content_price_change" => ($order->content_price-$saveContentPrice)
			);
			
			$order->content_price = $saveContentPrice;
			$order->delivery_price = $saveDeliveryPirce;
		}
		
		foreach(payments(app('veer')->siteId) as $method)
		{
			$order->payment_method_id = $method->id;
			
			app('veershop')->recalculateOrderPayment($order, $method, true);
			
			$calculations['payment'][$method->id] = array(
				"method" => $method->toArray(),
				"payment_hold" => $order->payment_hold,
				"payment_free" => $order->payment_free,
				"delivery_price_change" => ($order->delivery_price-$saveDeliveryPirce),
				"content_price_change" => ($order->content_price-$saveContentPrice)
			);
			
			$order->content_price = $saveContentPrice;
			$order->delivery_price = $saveDeliveryPirce;
		}		
			
		// total
		if($order->delivery_free == true) {	$order->price = $order->content_price; }
		else { $order->price = $order->content_price + $order->delivery_price; }
			
		$view = view($this->template.'.cart', array(
			"cart" => $grouped,
			"books" => isset($userbooks) ? $userbooks : null,
			"methods" => $calculations,
			"order" => $order,
			"data" => $data,
			"template" => $data['template']
		)); 

		return $view;  
	}
	
}

// TODO: Validator: показывать ошибки
// TODO: регистрация пользователя по секретному коду без какой-либо формы (быстрая регистрация)
// TODO: send email to user after successful registration

// TODO: update cart
// TODO: remove cart
// TODO: recalculate cart
// TODO: make order?