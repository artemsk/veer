<?php

class OrderController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Redirect::route('index'); 
		// TODO: страница где можно было бы ввести секретный код, чтобы гость увидел свой заказ.
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
                // TODO: для гостей и незарег. форма с кодом
                
		$administrator = false;
		
		if(Auth::check()) {
			$v = Auth::user()->load('administrator');
			if(isset($v->administrator->banned) && (bool)($v->administrator->banned) == false) { $administrator = true; }
		}
		
		if(Auth::check() || $administrator == true) {
			
			$orders = app('veerdb')->route($id, array('userId' => Auth::id(), 'administrator' => $administrator));
			
		} else {
			
			return Redirect::route('order.index'); 
		}
                
        if(!is_object($orders)) { return Redirect::route('index'); }
		 
		$orders->load('user', 'userbook', 'userdiscount', 'status', 'delivery', 'payment', 'status_history', 'products', 'bills');
		
		// TODO: разбить на отдельные страницы
		// TODO: вместе с products загружать images & downloads		
		
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.order', array(
			"order" => $orders,
			"data" => $data,
			"template" => $data['template']
		)); 

		$this->view = $view; 

		return $view;
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
