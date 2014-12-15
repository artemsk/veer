Veer
====

1) run: composer create-project artemsk/veer --prefer-dist

2) create ".env.[name].php" with data:

	return array(
		'DBPSSW' => '',
		'DBUSER' => '',
		'DBNAME' => '',
		'DBHOST' => '',
		'DBCHAR' => '',
		'DBCOLL' => '',
		'DBPRFX' => '', 
	);
	
	- or edit database configuration in app/config/database.php
	
3) run: php artisan veer:install [url] --migrate
   - use your installation url
   
4) go to [url]/admin/sites
   
Update
------

1) run: composer update

2) run: php artisan veer:publish
