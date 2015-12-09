<?php namespace Veer\Services\Administration;

class Structure {
	
    protected $action = null;

    public function __construct($t)
    {
        $this->action = 'update' . ucfirst($t);
        app('veer')->skipShow = false;
    }
    
    public function handle()
    {
        return $this->{$this->action}();
    }
    
	/**
	 * Update Sites
	 * @return void 
	 */
	public function updateSites()
	{
        return (new Elements\Site)->run();
    }	
        
	/**
	 * Update Root Categories
	 */
	public function updateCategories()
	{
		return (new Elements\Category)->run();	
	}

	/**
	 * update Products
	 */
	public function updateProducts()
	{
		return (new Elements\Product)->run();	
	}
		
	/**
	 * update Pages
	 */
	public function updatePages()
	{
		return (new Elements\Page)->run();	
	}
	
	/**
	 * update images 
	 */
	public function updateImages()
	{
        return (new Elements\Image)->run();
    }
	
	/**
	 * update tags
	 */
	public function updateTags()
	{		
		return (new Elements\Tag)->run();
	}
	
	/**
	 * update downloads
	 */
	public function updateDownloads()
	{
		return (new Elements\Download)->run();		
	}	
	
	/**
	 * update attributes
	 */
	public function updateAttributes()
	{
        return (new Elements\Attribute)->run();	
	}
}
