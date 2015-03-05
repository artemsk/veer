<?php namespace Veer\Http\Controllers;

use Veer\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Veer\Services\Show\Order as ShowOrder;

class OrderController extends Controller {

	protected $administrator = false;
	
	protected $showOrder;
	
	public function __construct(ShowOrder $showOrder)
	{
		parent::__construct();		
		
		//$this->beforeFilter('auth', array('only' => array('index', 'show')));
		
		$this->showOrder = $showOrder;
	}
	
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		/* do not cache: $this->view = $view; */		
		return viewx($this->template.'.secret-order', array(
			"template" => $this->template
		));
	}
	
	/**
	 * Check secret code for order and get object if it exists.
	 */
	public function store()
	{
		if(\Input::has('password')) 
		{                
			$p = trim(\Input::get('password'));  
			if( !empty($p) ) 
			{
				$check = \Veer\Models\Order::whereHas('secrets', function($query) use ($p) {
					$query->where('secret','=',$p);
				})->pluck('id');
				
				if(!empty($check)) 
				{
					return $this->showingOrder( 
						$this->showOrder->getOrderWithSite(app('veer')->siteId, $check, null, true)
					);
				}
			}
		}           
		return $this->index(); 	
	}

	/**
	 * Display the specified resource.
	 */
	public function show($id)
	{
		$this->administrator = administrator();
		
		if(\Auth::check() || $this->administrator == true) 
		{	
			return $this->showingOrder(
				$this->showOrder->getOrderWithSite(app('veer')->siteId, $id, \Auth::id(), $this->administrator)
			);	
		}		
			
		return $this->index();       
	}
	
	/**
	 * Display result.
	 */
	protected function showingOrder($orders) 
	{		
		if(!is_object($orders)) { return Redirect::route('index'); }
		 
		$orders->load(
			'user', 'userbook', 'userdiscount', 'status', 'delivery', 
			'payment', 'status_history', 'products', 'bills', 'secrets',
			'orderContent');
				
		return $this->viewIndex('order', $orders, false);
		
		// TODO: разбить на отдельные страницы
		// TODO: вместе с products загружать images & downloads	[доступно после оплаты]	
		// TODO: если просмотр по коду, то ничего делать нельзя без логина
	}
	
	/**
	 * check Bills
	 */
	public function bills($id = null, $lnk = null) 
	{
		$this->administrator = administrator();
		
		if(\Auth::check() || $this->administrator == true) 
		{	
			return $this->showingBill( 
				$this->showOrder->getBillWithSite(app('veer')->siteId, $id, $lnk, \Auth::id(), $this->administrator)
			);
		}		
		
		return Redirect::route('index'); 
	}
	
	/**
	 * show Bills
	 */
	protected function showingBill($bills)
	{
		if(!is_object($bills)) { return Redirect::route('index'); }
		
		// TODO: only html output? can it be redirect to external payment?
		
		if(!$this->administrator) 
		{
			$bills->viewed = true;
			$bills->save();
		}
		
		return \Response::make($bills->content);
	}
	
	/**
	 * Order was successful made
	 */
	public function success()
	{
		if(\Session::has("successfulOrder"))
		{		
			$orders = $this->showOrder->getOrderWithSite(
				app('veer')->siteId, \Session::get("successfulOrder"), \Auth::id(), administrator());

			if(is_object($orders))
			{
				$orders->load('user', 'userbook', 'userdiscount', 'status', 'delivery', 
					'payment', 'status_history', 'products', 'bills', 'secrets', 'orderContent');
				
				// TODO: do we need to load all information?
				// TODO: downloads for digital products

				/* do not cache */
				return viewx($this->template.'.success-order', array(
					"order" => $orders,
					"template" => $this->template
				));
			}
		}
		
		return Redirect::route('index'); 
	}
	
}
