<?php namespace Veer\Services\Show;

class Download {
	
	/**
	 * handle
	 */
	public function handle($paginateItems = 50)
	{
		return $this->getDownloads($paginateItems);
	}
	
	protected function query() 
	{
		return \Veer\Models\Download::orderBy('fname','desc')
			->orderBy('id', 'desc')
			->with('elements');	
	}
	
	/**
	 * Show Downloads
	 */
	public function getDownloads( $paginateItems = 50 ) 
	{	
		$items = $this->query()->paginate($paginateItems);	
		
		$items['regrouped'] = $this->regroupByName($items);
		
		$items['index'] = array_flip( array_keys($items['regrouped']) );
		
		app('veer')->loadedComponents['temporary'] = \Veer\Models\Download::where('original','=',0)->count();
		
		app('veer')->loadedComponents['counted'] = \Veer\Models\Download::count(\Illuminate\Support\Facades\DB::raw(
				'DISTINCT fname'
			));
		
		return $items;
	}	
	
	/** regroup files by original name */
	protected function regroupByName($items)
	{
		foreach($items as $key => $item) 
		{
			$items_regrouped[$item->fname][$item->original][$key]=$key;
		}
		
		return isset($items_regrouped) ? $items_regrouped : array();
	}
}
