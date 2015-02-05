<?php namespace Veer\Services\Show;

class Site {
	
	protected $site = null;
		
	/**
	 * get Sites
	 */
	public function getSites() 
	{	
		return \Veer\Models\Site::orderBy('manual_sort','asc')->get();		
	}
	
	/*
	 * get Site Id
	 */
	public function getSiteId()
	{
		return !empty($this->site) ? $this->site : null;
	}
	
	/*
	 * set Site
	 */
	public function setSite($id = null)
	{
		$this->site = empty($id) ? app('veer')->siteId : $id;
	}
	
	protected function replaceSortingBy($orderBy)
	{
		if(\Input::get('sort', null)) $orderBy[0] = \Input::get('sort');
		
		if(\Input::get('direction', null)) $orderBy[1] = \Input::get('direction'); 
		
		return $orderBy;
	}
	
	/**
	 * 
	 */
	protected function getConfigurationOrComponent($type = 'configuration', $siteId = null, $orderBy = array('id', 'desc')) 
	{	
		$orderBy = $this->replaceSortingBy($orderBy);
		
		if(empty($siteId)) $items = \Veer\Models\Site::where('id', '>', 0);
		
		else $items = \Veer\Models\Site::where('id', '=', $siteId);
		
		$items = $items->with(array($type => function($query) use ($orderBy, $type) 
		{
			if($type == 'components') $query->orderBy('sites_id');
			$query->orderBy($orderBy[0], $orderBy[1]);
		}))->get();
			
		return $items;
	}	
	
	/**
	 * Show Configurations
	 */
	public function getConfiguration($siteId = null, $orderBy = array('id', 'desc')) 
	{	
		return $this->getConfigurationOrComponent('configuration', $siteId, $orderBy);
	}	
	
	/**
	 * Show Components
	 */
	public function getComponents($siteId = null, $orderBy = array('id', 'desc')) 
	{	
		return $this->getConfigurationOrComponent('components', $siteId, $orderBy);
	}	
	
	/**
	 * Show Secrets
	 */
	public function getSecrets() 
	{		
		return \Veer\Models\Secret::orderBy('created_at', 'desc')->get();
	}		
	
}
