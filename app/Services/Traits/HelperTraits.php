<?php namespace Veer\Services\Traits;

trait HelperTraits {

	/**
	 * Site with Site title
	 */
	protected function loadSiteTitle($items = null)
	{
		$siteWithTitle = array('site' => function($q) { 
            $q->with(array('configuration' => function($query) 
            {
                $query->where('conf_key','=','SITE_TITLE'); 
            }));
        }); // TODO: remember 5
					
		if(!empty($items)) {	
			return \Cache::remember(app('veer')->cachingQueries->generateCacheKeyExternal($items), 1, 
				function() use($items, $siteWithTitle) {
					return $items->load($siteWithTitle);
				});
		}
		
		return $siteWithTitle;
	}

	/**
	 * Get Existing Bill Templates
	 */
	protected function getExistingBillTemplates()
	{
		$billsTypes = \File::allFiles(base_path()."/resources/views/components/bills");
		
		foreach(isset($billsTypes) ? $billsTypes : array() as $billFile) {
			app('veer')->loadedComponents['billsTypes'][ array_get(pathinfo($billFile), 'filename') ] = array_get(pathinfo($billFile), 'filename');
		}	
	}
	
	/**
	 * only downloads for order
	 * (will take only products)
	 */
	protected function getOrderDownloads($orders = array())
	{
		$files = array();
		
		foreach($orders as $o) {
			foreach($o->downloads as $file)
			{
				$file->elements_type == elements('product') 
					? array_push($files, $file) : null;
			}
		}
		
		return $files;
	}

	
	/*
	 * build filter query on models with elements
	 */
	protected function buildFilterWithElementsQuery($filters, $model, $pluralize = true, $field = null)
	{
		if(head($filters) != null) return $this->createFilterQuery($filters, $model, $pluralize, $field);
		
		return $model::select();
	}
	
	protected function createFilterQuery($filters, $model, $pluralize, $field)
	{
		$type = key($filters);  
		
		$filter_id = head($filters);
		
		$special_types = array("pages", "products", "categories");
		
		if(in_array($type, $special_types)) {
			return $model::where('elements_type', '=', elements($type))->where('elements_id','=', $filter_id);
		}

		return $model::whereHas($type, function($query) use ($filter_id, $type, $pluralize, $field) 
			{			
				if (!empty($field)) $query->where($field, '=', $filter_id); 
				
				else $query->where(($pluralize) ? str_plural($type) . '_id' : $type . '_id', '=', $filter_id); 
			});
	}
}
