<?php

return array(

    /*
	|--------------------------------------------------------------------------
	| Template by default. Best practice is to use prefix "template-"
	|--------------------------------------------------------------------------
	*/
    
        'template' => "template-blank",   
	
		'template-admin' => "template-admin",
    
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
		'assets_path' => "assets",
    
        'htmlpages_path' => "assets/pages",  
    
        'images_path' => "assets/images", 
    
        'downloads_path' => "assets/downloads",  
	
    /*
	|--------------------------------------------------------------------------
	| USER ! Paths to components, events, queues, e-commerce
	|--------------------------------------------------------------------------
	*/
    
        'components_path' => "app/App/Components",  
    
        'events_path' => "app/App/Events", 
    
        'queues_path' => "app/App/Queues",  	
	
		'ecommerce_path' => "app/App/Components/Ecommerce",  
    
    /*
	|--------------------------------------------------------------------------
	| Default settings - loading time alert limit (seconds)
	|--------------------------------------------------------------------------
	*/    
    
        'loadingtime' => '2',
	
    /*
	|--------------------------------------------------------------------------
	| Queue "qdb" repeat time (minutes)
	|--------------------------------------------------------------------------
	*/    
    
        'repeatjob' => '5',	
           
    /*
	|--------------------------------------------------------------------------
	| User history: referrals, urls, ips - to 
	|--------------------------------------------------------------------------
	*/    
    
        'history.refs' => true,
        
        'history.urls' => true,
        
        'history.ips' => false,   
	
		'history.path' => storage_path() . "/history",
	
	
    /*
	|--------------------------------------------------------------------------
	| E-commerce settings
	|--------------------------------------------------------------------------
	*/            

		'currency_symbol' => '$[price]',
        
		/* showing orders clusters */
		'use_cluster' => false,
	
		'restrict_orders' => false,
	
    /*
	|--------------------------------------------------------------------------
	| Markdown Editor 
	|--------------------------------------------------------------------------
	*/ 
	
		//'markdown_editor' => true,

);
