<?php

return array(

    /*
	|--------------------------------------------------------------------------
	| Template by default. Best practice is to use prefix "template-"
	|--------------------------------------------------------------------------
	*/
    
        'template' => "template-default",   
    
    	/*
	|--------------------------------------------------------------------------
	| Cache Enable & Prefixes for Html Pages
	|--------------------------------------------------------------------------
	*/
    
        'htmlcache_enable' => true,   
    
        'htmlcache' => '_htmlcache_',    
    
    	/*
	|--------------------------------------------------------------------------
	| Paths to public html pages (_path_/*.html), images, 
	| downloads, templates - starting from root (even if in public/)
	| 
	|--------------------------------------------------------------------------
	*/
    
        'htmlpages_path' => "assets/pages",  
    
        'images_path' => "assets/images", 
    
        'downloads_path' => "assets/downloads",  
    
        /*
	|--------------------------------------------------------------------------
	| Default settings - loading time
	|--------------------------------------------------------------------------
	*/    
    
        'loadingtime' => '1',
           
    /*
	|--------------------------------------------------------------------------
	| User history: referrals, urls, ips - to 
	|--------------------------------------------------------------------------
	*/    
    
        'history.refs' => true,
        
        'history.urls' => true,
        
        'history.ips' => false,   
	
		'history.path' => storage_path() . "/history",
        
        
);
