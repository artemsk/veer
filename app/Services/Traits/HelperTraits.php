<?php namespace Veer\Services\Traits;

trait HelperTraits {

	/* contents of bills types */
	public $billsTypes = null;
	
	/**
	 * Site with Site title
	 */
	protected function loadSiteTitle($items = null)
	{
		$siteWithTitle = array('site' => function($q) 
			{ 
				$q->with(array('configuration' => function($query) 
				{
					$query->where('conf_key','=','SITE_TITLE'); 
				}));
			}); // TODO: remember 5
					
		if(!empty($items)) 
		{	
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
	public function getExistingBillTemplates()
	{
		$billsTypes = \File::allFiles(base_path()."/resources/views/components/bills");
		
		foreach(isset($billsTypes) ? $billsTypes : array() as $billFile)
		{
			app('veer')->loadedComponents['billsTypes'][ array_get(pathinfo($billFile), 'filename') ] = array_get(pathinfo($billFile), 'filename');
		}	
	}
	
}
