<?php

class SearchController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Redirect::route('index');
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
            if(Input::has('q')) 
            {                
                $q = trim(Input::get('q'));  
                if( $q != '') 
                {
                    $search = \Veer\Models\Search::firstOrCreate(array("q" => $q));
                    $search->increment('times');                  
                    $search->save();
                    
                    if(Auth::check()) { $search->users()->attach(Auth::id()); } 
                    
                    $getData = new veerDb(Route::currentRouteName(), $search->id, array( 'q' => $q ));
                    
                }
            }
           
            echo "<pre>";
            print_r(Illuminate\Support\Facades\DB::getQueryLog());
            echo "</pre>";
            // return default
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
                $search = \Veer\Models\Search::find($id);
                
                if($search) {
                    
                    $getData = new veerDb('search.store', $id, array( 'q' => $search->q ));
                    
                }
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


}
