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
	public function addToCart($id)
	{
		if(isset($id)) {
			
			$product = \Veer\Models\Product::where('id','=',$id)->checked()->first();
			if(is_object($product)) {
				
				$userid = \Illuminate\Support\Facades\Auth::id();
					
				$cart = new UserList;
				$cart->sites_id = app('veer')->siteId;
				$cart->users_id = empty($userid) ? 0 : $userid;
				$cart->session_id = Session::getId();
				$cart->name = "[basket]";                           
				$cart->quantity = 1;
				if(count(Input::all())>0) {
					$cart->attributes = json_encode(Input::all());
				}				
				$cart->save();
				$product->userlists()->save($cart);  
				
				$items = app('veerdb')->userShoppingCart(app('veer')->siteId, $userid);
				
				Session::put('shopping_cart_items',$items);
				
			}
		}
		
		return stored();
	}	
	
	
	/**
	 * Login Form
	 *
	 * @return Response
	 */
	public function login()
	{
		$data = $this->veer->loadedComponents;
                
		$view = view($this->template.'.login', $data); 

		//$this->view = $view; // do not cache

		return $view;  
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
				app('veerdb')->userShoppingCart(app('veer')->siteId, Auth::id()));
					
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
        
        
}

// TODO: Validator: показывать ошибки
// TODO: регистрация пользователя по секретному коду без какой-либо формы (быстрая регистрация)