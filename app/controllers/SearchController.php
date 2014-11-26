<?php

class SearchController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
                    
                    // $search->users()->attach(1); // TODO: if user exists attach him to search result
                    
                    $getData = new Veer\Lib\Components\globalGetModelsData(array(
                    'method' => Route::currentRouteName(),
                    'id' => $search->id,
                    'params' => array(
                                    'q' => $q
                                )
                    ));
                    
                }
            }
           
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
                    
                    $getData = new Veer\Lib\Components\globalGetModelsData(array(
                        'method' => 'search.store',
                        'id' => $id,
                        'params' => array(
                            'q' => $search->q
                        )
                    ));
                }
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
