<?php

class OrderController extends \BaseController {

	protected $administrator = false;
	
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

		$view = view($this->template.'.secret-order', array(
			"data" => $data,
			"template" => $data['template']
		)); 

		$this->view = $view; // to cache

		return $view;		
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {}


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
		$this->administrator = administrator();
		
		if(Auth::check() || $this->administrator == true) {
			
			$orders = app('veerdb')->route($id, array('userId' => Auth::id(), 'administrator' => $this->administrator));
			
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
	public function edit($id) {}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id) {}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id) {}


	/**
	 * check Bills
	 * @param type $lnk
	 * @return type
	 */
	public function bills($id = null, $lnk = null) 
	{
		$this->administrator = administrator();
		
		if(Auth::check() || $this->administrator == true) 
		{	
			return $this->showBill( 
				app('veerdb')->route(array($id, $lnk), array('userId' => Auth::id(), 'administrator' => $this->administrator))
			);
		}		
		
		return Redirect::route('index'); 
	}
	
	/**
	 * show Blls
	 * @param type $bills
	 */
	protected function showBill($bills)
	{
		if(!is_object($bills)) { return Redirect::route('index'); }
		
		// TODO: only html output? can it be redirect to external payment?
		// TODO: loadedComponents here?
		
		if(!$this->administrator) 
		{
			$bills->viewed = true;
			$bills->save();
		}
		
		return Response::make($bills->content);
	}
	
	
	/**
	 * Order was successful made
	 */
	public function success()
	{
		//
	}
	
}
