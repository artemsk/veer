<?php
return array(
    
      /*
       * Template by default. Best practice is to use prefix "template-"
       * 
       */

        'template' => "template-blank",
        'template-admin' => "template-admin",

      /* 
       * Cache Enable & Prefixes for Html Pages
       * 
       */

        'htmlcache_enable' => env('VEER_HTMLCACHE', true),
        'htmlcache' => '_htmlcache_',

      /*
       * Paths to public html pages (_path_/*.html), images,
       * templates - starting from public; downloads -> starting from root
       * or storage_path()
       * 
       */

        'assets_path' => env('VEER_PATHS_ASSETS', 'assets/themes'),
        'htmlpages_path' => env('VEER_PATHS_HTMLPAGES', "assets/pages"),
        'images_path' => env('VEER_PATHS_IMAGES', "assets/images"),
        'downloads_path' => env('VEER_PATHS_DOWNLOADS', "downloads"),
    
        'cloudstorage_path' => env('VEER_PATHS_CLOUDSTORAGE', ""),
        'use_cloud_images' => env('VEER_USE_CLOUD_IMAGES', false),
        'use_cloud_files' => env('VEER_USE_CLOUD_FILES', false),

      /*
       * USER ! Paths to components, events, queues, e-commerce
       * @deprecated
       * 
       */

        'components_path' => "app/Components",
        'events_path' => "app/Events",
        'queues_path' => "app/Queues",
        'ecommerce_path' => "app/Components/Ecommerce",

      /*
       * Default settings - loading time alert limit (seconds)
       * 
       */

        'loadingtime' => env('VEER_LOADING', 2),

      /*
       * Queue "qdb" repeat time (minutes)
       * 
       */

        'repeatjob' => env('VEER_JOBCHECK', 5),

      /*
       * User history: referrals, urls, ips - to
       * 
       */

        'history_refs' => env('VEER_SAVE_REFERRALS', true),
        'history_urls' => env('VEER_SAVE_URLS', true),
        'history_ips' => env('VEER_SAVE_IPS', false),
        'history_path' => storage_path()."/logs/history",

      /*
       * E-commerce settings
       * 
       */

        'currency_symbol' => env('VEER_CURRENCY_SYMBOL', '$[price]'),
        /* showing orders clusters */
        'use_cluster' => env('VEER_CLUSTER', false),
        'restrict_orders' => env('VEER_RESTRICT_ORDERS', false),

      /*
       * Markdown Editor
       * 
       */

        //'markdown_editor' => true,

      /*
       * wkhtmltoimage path (win or linux). null == disabled
       * 
       */
    
        'wkhtmltoimage' => env('VEER_WKHTMLTOIMAGE_PATH', null),

      /*
       * Image thumbnails & cache
       * 
       */

        'image_templates' => [
            'small' => ['fit', 120, 90],
            'medium' => ['fit', 240, 180],
            'large' => ['resize', 500, 500],
            '284x211' => ['fit', 284, 211]
        ],

        'image_lifetime' => 43200,
);
