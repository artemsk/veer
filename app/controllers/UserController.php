<?php

use Veer\Models\UserList;

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//return Redirect::route('user.login'); 
                
                echo "<pre>";
                print_r(Auth::id());
                print_r(Auth::getName());
                echo "</pre>";
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
                $getData = new VeerDb(Route::currentRouteName(), $id);
                
                echo "<pre>";
                print_r(Illuminate\Support\Facades\DB::getQueryLog());
                echo "</pre>";
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
				
				$items = UserList::where('sites_id','=',app('veer')->siteId)
					->where(function($query) use ($userid) {
						if($userid > 0) {
						$query->where('users_id','=', empty($userid) ? 0 : $userid)
							->orWhere('session_id','=',Session::getId());	
						} else {
						$query->where('users_id','=', empty($userid) ? 0 : $userid)
							->where('session_id','=',Session::getId());							
						}
					})->where('name','=','[basket]')
					->where('elements_type','=','Veer\Models\Product')
					->sum('quantity');
				
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
                if(Auth::check()) { echo "Logged.<br>"; }
                
 echo "<pre>";
 print_r(Auth::getName());
 echo "<br>";
 print_r(Session::all());
 echo "</pre>";
		$data = $this->veer->loadedComponents;
                
                echo "<pre>";
                print_r(Illuminate\Support\Facades\DB::getQueryLog());
                echo "</pre>";
                        
                return \View::make($this->template.'.login', $data); 
                
                
	}


	/**
	 * Login Post
	 *
	 * @return Response
	 */
	public function loginPost()
	{
		$data = $this->veer->loadedComponents;
                if (Auth::check())
                {
                    return "Logged<br>";
                     
                } 
                    
                
                if (Auth::attempt(array('email' => \Input::get('email'), 'password' => \Input::get('password'), 'banned' => 0)))
                {
					//\Veer\Models\User::find(Auth::id())->increment('logons_count');
					
					Auth::user()->increment('logons_count');
					
					//echo "<pre>";
					//print_r(Auth::user());
					//echo "</pre>";

                    return Redirect::intended('user/login');
                }

                return \View::make($this->template.'.login', $data); 
	}        
        
        
}
