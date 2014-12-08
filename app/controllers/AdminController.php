<?php

class AdminController extends \BaseController {

	protected $adm;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->beforeFilter('auth');
		
		$this->beforeFilter(function()
        {
			if(administrator() == false) { return Redirect::route('user.index'); } 
			
			$a = app('veer')->administrator_credentials;			
			$a['sites_encoded'] = json_decode($a['sites_watch']);

			if(!in_array(app('veer')->siteId, (array)$a['sites_encoded']) && !empty($a['sites_watch'])) {
				return Redirect::route('user.index');
			}			
			app('veer')->administrator_credentials['sites_encoded'] = $a['sites_encoded'];
			app('veer')->administrator_credentials['access_encoded'] = json_decode(app('veer')->administrator_credentials['access_parameters']);
			
			app('veer')->loadedComponents['template'] = app('veer')->template = config('veer.template-admin');
        });
		
	}
	

	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		
		
				echo "<pre>";
		print_r(app('veer')->administrator_credentials);
		echo "</pre>";
		
		echo "<pre>";
		print_r(DB::getQueryLog());
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
	// TODO: too heavy?
	public function show($id)
	{
		switch ($id) {
			case "sites":
				
				$items = Veer\Models\Site::all()->load('subsites', 'categories', 'components', 'configuration', 
					'users', 'discounts', 'userlists', 'orders', 'delivery', 'payment', 'communications', 
					'roles', 'parentsite'); // elements separately				
				
				$view = "sites";
				break;
			
			case "attributes":
				$items = Veer\Models\Attribute::all()->sortBy('name')->load('pages', 'products');
				
				foreach($items as $key => $item) {
					$items_grouped[$item->name][$key] = $key;
					$items_counted[$item->name]['prd'] = (isset($items_counted[$item->name]['prd']) ? 
						$items_counted[$item->name]['prd'] : 0) + ($item->products->count()); 
					$items_counted[$item->name]['pg'] = (isset($items_counted[$item->name]['pg']) ? 
						$items_counted[$item->name]['pg'] : 0) + ($item->pages->count()); 
				}
				
				if($items_grouped) {
					$items->put('grouped', $items_grouped);
					$items->put('counted', $items_counted);				
				}
				
				$view = "attributes";
				break;

			default:
				break;
		}

		if(isset($items) && isset($view)) {
		return view(app('veer')->template.'.'.$view, array(
			"items" => $items,
			"data" => app('veer')->loadedComponents,
			"template" => app('veer')->template
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
