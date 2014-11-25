<?php

class IndexController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
            return "1";
                /*$site_id = Config::get('veer.site_id');
        
                $template = array_get(Config::get('veer.site_config'),'TEMPLATE');

                $data['template']=$template;

                $c = new VeerComponents();
                $c_result = $c->_detect($site_id, Route::currentRouteName()); 

                $data['products'] = $c_result['home_products'];

                //$lists = \Veer\Models\UserList::find(1);
                //$lists->load('site','user','elements');

                //\Auth::loginUsingId(1);

                //echo "<pre>";
                //print_r($lists);
                //echo "</pre>";

                return \View::make('template.'.$template.'.home', $data); */
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
		//
	}

        /**
	 * Display the 404 page
	 *
	 * @param  int  $id
	 * @return Response
	 */
        public function show404()
	{
		//
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
