<?php

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
