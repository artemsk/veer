<?php  namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Veer\Models\UserList;

class UserController extends Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->middleware('guest', array('only' => array('login', 'register')));
		
		$this->middleware('auth', array('only' => array('index', 'show', 'logout')));	
	}
	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = app('veerdb')->route(\Auth::id());

		if(!is_object($user)) { return \Redirect::route('index'); }
		
		return $this->showUser($user);
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
		
		if(!is_object($user)) { return \Redirect::route('index'); }
		
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
				
				\Session::put('shopping_cart_items',$items);
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
			
			if(is_object($e)) $this->savingEntity($e, $userid, \Input::get('name','[basket]'));
		}
		
		return  app('veerdb')->userLists(app('veer')->siteId, $userid, \Input::get('name','[basket]'));
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
		$cart->session_id = \Session::getId();
		$cart->name = $name;                           
		$cart->quantity = 1;
		if(\Input::has('attributes') && is_array(\Input::get('attributes'))) {
			$cart->attributes = json_encode(\Input::get('attributes'));
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
				
		\Session::put('shopping_cart_items',$items);
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
        
		$viewLink = $this->template.'.login';
		
		if(!\View::exists($viewLink)) $viewLink = config('veer.template').'.login';
		
		$view = view($viewLink, array(
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
		\Session::flush();
		
		\Auth::logout();
		
		if(!app('request')->ajax()) return \Redirect::route('index'); 
	}
	

	/**
	 * Login Post
	 *
	 * @return Response
	 */
	public function loginPost()
	{
		$save_old_session_id = \Session::getId();
			
        if (\Auth::attempt(array('email' => \Input::get('email'), 'password' => \Input::get('password'), 
			'banned' => 0, 'sites_id' => app('veer')->siteId)))
        {
			\Auth::user()->increment('logons_count');
			
			\Session::put('roles_id', \Auth::user()->roles_id);
			
			\Veer\Models\UserList::where('session_id','=',$save_old_session_id)->update(array('users_id' => \Auth::id()));
			
			\Session::put('shopping_cart_items', 
				app('veerdb')->userLists(app('veer')->siteId, \Auth::id()));
					
			if(administrator() == true) {
				\Veer\Models\UserAdmin::where('id','=',app('veer')->administrator_credentials['id'])->
					update(array(
						"sess_id" => \Session::getId(),
						"last_logon" => now(),
						"ips" => \Illuminate\Support\Facades\Request::getClientIp(),
						"logons_count" => app('veer')->administrator_credentials['logons_count'] + 1
					));
			}
			
			return \Redirect::intended();
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
		
		if( ($anonymAllow == false && \Auth::id() > 0) || $anonymAllow == true )
		{
			$data = \Input::all();

			array_set($data, 'fill.users_id', \Auth::id());			

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
		
		$data = \Input::all();

		array_set($data, 'communication.fill.users_id', \Auth::id());			
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
		
		$fill = \Input::get('fill');
				
		$fill['sites_id'] = app('veer')->siteId;
		
		if(array_has($fill, 'password') && empty($fill['password'])) array_forget($fill, 'password');
		
		$rules = array(
			'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL,sites_id,' . app('veer')->siteId,
			'password' => 'required|min:6',
		);			

		$validator = \Validator::make($fill, $rules);
			
		if($validator->fails()) 
		{ 
			return \Redirect::route('user.register')->withErrors($validator);		
		}
		
		\Eloquent::unguard();
		
		$user = new \Veer\Models\User;
		
		$fill['restrict_orders'] = db_parameter('ECOMMERCE_RESTRICT_ORDERS', config('veer.restrict_orders', false));
		
		$fill['newsletter'] = isset($fill['newsletter']) ? true : false;
		$fill['birth'] = parse_form_date(array_get($fill, 'birth'));
		
		$user->fill($fill);
		
		$user->save();		
		
		\Auth::login($user);
		
		return \Redirect::intended();	
	}
	
	/**
	 * show cart
	 * @return type
	 */
	public function showCart()
	{
		$data = $this->veer->loadedComponents;
        
		// prepare content	
		$cart = app('veerdb')->make('user.cart.show', \Auth::id());   
		
		$grouped = app('veershop')->regroupShoppingCart($cart);
		
		// show user books
		if(\Auth::id() > 0) $userbooks = \Auth::user()->books;
		
		list($order, $checkDiscount, $calculations) = app('veershop')->prepareOrder($grouped);
		
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
	
	/**
	 * update Cart
	 * @return type
	 */
	public function updateCart()
	{
		\Event::fire('router.filter: csrf');
		
		if(\Input::get('action') == "order") return $this->makeOrder();
	}
	
	/**
	 * make Order
	 */
	protected function makeOrder()
	{
		$data = $this->veer->loadedComponents;
        
		$cart = app('veerdb')->make('user.cart.show', \Auth::id());   
		
		// rules
		if((\Auth::id() <= 0 && \Input::get('email') == null) || $cart->count() <= 0) 
		{ 
			\Session::flash('errorMessage', \Lang::get('veershop.order.error'));
			return \Redirect::route('user.cart.show');
		}		
		
		$grouped = app('veershop')->regroupShoppingCart($cart);
		
		$book = null;
					
		if(\Input::get('userbook_id') != null) $book = \Veer\Models\UserBook::find(\Input::get('userbook_id'));
		
		if(\Input::get('book.address') != null) $book = app('veershop')->updateOrNewBook(\Input::get('book'));
		
		list($order, $checkDiscount, $calculations) = app('veershop')->prepareOrder(
			$grouped, $book, \Input::get('shipping_id'), \Input::get('payment_id'), false);
		
		$statusName = \Veer\Models\OrderStatus::where('id','=',$order->status_id)->pluck('name');
		
		\Veer\Models\OrderHistory::create(array(
			"orders_id" => $order->id,
			"status_id" => $order->status_id,
			"name" => !empty($statusName) ? $statusName : '',
			"comments" => "",
		));
			
		$order->save();
		
		if(isset($checkDiscount) && is_object($checkDiscount)) app('veershop')->changeUserDiscountStatus($checkDiscount);
		
		//app('veershop')->sendEmailOrderNew($order);
		
		// clear cart
		app('veerdb')->userLists(app('veer')->siteId, \Auth::id(), '[basket]', false)->delete();
		
		\Session::put('successfulOrder', $order->id);
		
		return \Redirect::route('order.success');
	}
	
}

// TODO: Validator: показывать ошибки
// TODO: регистрация пользователя по секретному коду без какой-либо формы (быстрая регистрация)
// TODO: send email to user after successful registration

// TODO: update cart
// TODO: remove cart
// TODO: recalculate cart