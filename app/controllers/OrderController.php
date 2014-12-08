<?php

class OrderController extends \BaseController {

	
	public function __construct()
	{
		parent::__construct();		
		//$this->beforeFilter('auth', array('only' => array('index', 'show')));
	}
	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$data = $this->veer->loadedComponents;            

		$view = view($this->template.'.secret-order', $data); 

		$this->view = $view; // to cache

		return $view;		
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
	 * Check secret code for order and get object if it exists.
	 *
	 * @return Response
	 */
	public function store()
	{
		if(Input::has('password')) 
		{                
			$p = trim(Input::get('password'));  
			if( $p != '' ) 
			{
				$check = \Veer\Models\Order::whereHas('secrets', function($query) use ($p) {
					$query->where('secret','=',$p);
				})->pluck('id');
				
				if($check) {
					$orders = app('veerdb')->make('order.show', $check, array('userId' => 0, 'administrator' => true));
				
					return $this->showOrder($orders);
				}
			}
		}           
		return $this->index(); 	
	}

	
	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, $force = false)
	{
		$administrator = administrator();
		
		if(Auth::check() || $administrator == true) {
			
			$orders = app('veerdb')->route($id, array('userId' => Auth::id(), 'administrator' => $administrator));
			
		} else {			
			return $this->index(); 
		}
                
		return $this->showOrder($orders);        
	}

	
	/**
	 * Display result.
	 *
	 * @param  int  $orders
	 * @return Response
	 */
	protected function showOrder($orders) 
	{		
		if(!is_object($orders)) { return Redirect::route('index'); }
		 
		$orders->load('user', 'userbook', 'userdiscount', 'status', 'delivery', 'payment', 'status_history', 'products', 'bills', 'secrets');
				
		// TODO: разбить на отдельные страницы
		// TODO: вместе с products загружать images & downloads	[доступно после оплаты]	
		// TODO: если просмотр по коду, то ничего делать нельзя без логина
		
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
