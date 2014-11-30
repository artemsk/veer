<?php

class AttributeController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
                $VeerDb = VeerQ::route();   
                
				Queue::later(5, function($job) 
				{
					
					File::append( storage_path() . "/queue.txt", \Carbon\Carbon::now(). "\r\n");

					$job->delete();
				});

                foreach($VeerDb as $d) {
                    echo $d->id." ".$d->name."<br>";
                }

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
                $VeerDb = new VeerDb(Route::currentRouteName(), $id);                 
                
                if($VeerDb->data['parent_flag'] != 1) {
                    
                $products = $VeerDb->attributeOnlyProductsQuery($this->veer->siteId, $id, get_paginator_and_sorting());
                
                $pages = $VeerDb->attributeOnlyPagesQuery($this->veer->siteId, $id, get_paginator_and_sorting());
                
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
