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
                
		$view = view($this->template.'.login', $data); 

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
        
}

// TODO: Validator: показывать ошибки
// TODO: регистрация пользователя по секретному коду без какой-либо формы (быстрая регистрация)