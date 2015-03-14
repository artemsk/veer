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
    
        'htmlcache_enable' => env('VEER_HTMLCACHE', true),   
    
        'htmlcache' => '_htmlcache_',    
    
    /*
	|--------------------------------------------------------------------------
	| Paths to public html pages (_path_/*.html), images, 
	| downloads, templates - starting from public
	| 
	|--------------------------------------------------------------------------
	*/
		'assets_path' => env('VEER_PATHS_ASSETS', 'assets/themes'),
    
        'htmlpages_path' => env('VEER_PATHS_HTMLPAGES', "assets/pages"),  
    
        'images_path' => env('VEER_PATHS_IMAGES', "assets/images"), 
    
        'downloads_path' => env('VEER_PATHS_DOWNLOADS', "assets/downloads"),  
	
    /*
	|--------------------------------------------------------------------------
	| USER ! Paths to components, events, queues, e-commerce
	|--------------------------------------------------------------------------
	*/
    
        'components_path' => "app/Components",  
    
        'events_path' => "app/Events", 
    
        'queues_path' => "app/Queues",  	
	
		'ecommerce_path' => "app/Components/Ecommerce",  
    
    /*
	|--------------------------------------------------------------------------
	| Default settings - loading time alert limit (seconds)
	|--------------------------------------------------------------------------
	*/    
    
        'loadingtime' => env('VEER_LOADING', 2),
	
    /*
	|--------------------------------------------------------------------------
	| Queue "qdb" repeat time (minutes)
	|--------------------------------------------------------------------------
	*/    
    
        'repeatjob' => env('VEER_JOBCHECK', 5),	
           
    /*
	|--------------------------------------------------------------------------
	| User history: referrals, urls, ips - to 
	|--------------------------------------------------------------------------
	*/    
    
        'history_refs' => env('VEER_SAVE_REFERRALS', true),
        
        'history_urls' => env('VEER_SAVE_URLS', true),
        
        'history_ips' => env('VEER_SAVE_IPS', false),   
	
	'history_path' => storage_path() . "/logs/history",
	
	
    /*
	|--------------------------------------------------------------------------
	| E-commerce settings
	|--------------------------------------------------------------------------
	*/            

		'currency_symbol' => env('VEER_CURRENCY_SYMBOL', '$[price]'),
        
		/* showing orders clusters */
		'use_cluster' => env('VEER_CLUSTER', false),
	
		'restrict_orders' => env('VEER_RESTRICT_ORDERS', false),
	
    /*
	|--------------------------------------------------------------------------
	| Markdown Editor 
	|--------------------------------------------------------------------------
	*/ 
	
		//'markdown_editor' => true,

);
