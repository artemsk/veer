<?php
namespace Veer\Components;

class indexCornersPages {   
    
	use \Veer\Services\Traits\HomeTraits;
	
	public $data;

	public $default_type = 9;
	
	public $itemsPerPage = 25;
	
	public $autoSort = true;

	/* */
	protected $sumRow = 0;
	
	protected $currentRow = 0;
	
	protected $working_data;
	
    function __construct($params = null) {
        
		$e = $this->getHomeEntities('\Veer\Models\Page', app('veer')->siteId, db_parameter('CATEGORY_HOME'))
			->with('attributes', 'user')
			->select('id', 'title', 'small_txt', 'views', 'created_at', 'users_id')
			->orderBy('manual_order', 'asc')->paginate($this->itemsPerPage);

		if(count($e) <= 0) return null;
		
		$this->data['items'] = $this->getAttributes($e);
		
		if($this->autoSort) $this->makeGrid();
		
		$this->data['gridSort'] = array_get($this->working_data, 'makeRow');
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
		}
		
		return $e;
	}
	
	/* create 24-grid of 6 & 12 elements */
	public function makeGrid()
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
		}

		$this->nextRow();
	}
	
	/* shift to next row */
	protected function nextRow()
	{
		$this->sumRow = 0; 
		
		$this->currentRow = $this->currentRow + 1;
	}
	
}

/*
 * 	seed temporarely 
 * 
	for($j = 0; $j <= 55; $j++)
	{
		$this->data['full'][] = rand(0, 1) == 1 ? 6 : 12;
	}
	
	foreach($this->data['full'] as $key => $value)
	{
		if($value == 6) $this->data['only6'][$key] = $key;
	}
 * 
 */
