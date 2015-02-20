<?php
namespace Veer\Components;

class indexCornersPages {   
    
	use \Veer\Services\Traits\HomeTraits;
	
	public $data;

	public $default_type = 9;
	
	public $itemsPerPage = 25;
	
	public $autoSort = true;

	/* */
	protected $category;
	
	protected $sumRow = 0;
	
	protected $currentRow = 0;
	
	protected $working_data;
	
    public function __construct() {
        
		$this->category = db_parameter('CATEGORY_HOME');
		
		if(starts_with("index", \Route::currentRouteName())) $this->createListOfPages();
    }    
    
	public function setCategory($category)
	{
		$this->category = $category;
	}
	
	/* create list of pages */
	public function createListOfPages()
	{
		$e = $this->getHomeEntities('\Veer\Models\Page', app('veer')->siteId, $this->category)
			->with('attributes', 'user')
			->select('id', 'url', 'title', 'small_txt', 'views', 'created_at', 'users_id')
			->orderBy('manual_order', 'asc')->simplePaginate($this->itemsPerPage);

		if(count($e) <= 0) return null;
		
		$this->data['items'] = $this->getAttributes($e);
		
		if($this->autoSort) $this->makeGrid();
		
		$this->data['gridSort'] = array_get($this->working_data, 'makeRow');
		
		if(app('request')->ajax()) $this->earlyResponse();
	}
	
	/* get grid attributes of items */
	protected function getAttributes($e)
	{
		foreach($e as $key => $item)
		{
			$itemParams = $item->attributes->lists('val', 'name');
			
			$this->working_data['full'][$key] = array_get($itemParams, 'gridSize', 6);
			
			if($this->working_data['full'][$key] == 6) $this->working_data['only6'][$key] = $key;
			
			$item->designType = array_get($itemParams, 'designType', $this->default_type);
			
			$item->bgColor = array_get($itemParams, 'bgColor');
		}
		
		return $e;
	}
	
	/* create 24-grid of 6 & 12 elements */
	protected function makeGrid()
	{ 
		a:
		reset($this->working_data['full']);	
		$takeOne = head($this->working_data['full']);
		$keyOne = key($this->working_data['full']);

		if(empty($takeOne)) return true;
		
		$future = $takeOne + $this->sumRow;
		
		if($future <= 24) 
		{ 	
			$this->makeRow($future, $keyOne);
			goto a;
		}
		
		$this->swap();
		goto a;
	}
    
	/* make row */
	protected function makeRow($future, $keyOne)
	{
		$this->working_data['makeRow'][$this->currentRow][] = $keyOne;
			
		if($future == 24) { $this->nextRow(); } 
		
		else { $this->sumRow = $future; }
			
		array_pull($this->working_data['full'], $keyOne);
		array_pull($this->working_data['only6'], $keyOne);
	}
	
	/* swap elements to fill row */
	protected function swap()
	{
		$take6 = head($this->working_data['only6']);

		if(!empty($take6))
		{
			$this->working_data['makeRow'][$this->currentRow][] = $take6;
			array_pull($this->working_data['only6'], $take6);
			array_pull($this->working_data['full'], $take6);
		}

		$this->nextRow();
	}
	
	/* shift to next row */
	protected function nextRow()
	{
		$this->sumRow = 0; 
		
		$this->currentRow = $this->currentRow + 1;
	}
	
	/* send response for ajax requests */
	protected function earlyResponse()
	{
		app('veer')->forceEarlyResponse = true;
		
		$data2view['data']['function']['indexCornersPages']['data'] = $this->data;
		$data2view['template'] = app('veer')->template;
		$data2view['loadScripts'] = true;
		
		app('veer')->earlyResponseContainer = view(app('veer')->template . ".layout.pages-list", $data2view);
	}
	
}
