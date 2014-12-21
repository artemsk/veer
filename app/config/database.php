<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| PDO Fetch Style
	|--------------------------------------------------------------------------
	|
	| By default, database results will be returned as instances of the PHP
	| stdClass object; however, you may desire to retrieve records in an
	| array format for simplicity. Here you can tweak the fetch style.
	|
	*/

	'fetch' => PDO::FETCH_CLASS,

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work. Of course
	| you may use many connections at once using the Database library.
	|
	*/

	'default' => !isset($_ENV['DBDRVR']) ? 'sqlite' : $_ENV['DBDRVR'],

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => array(

		'sqlite' => array(
			'driver'   => 'sqlite',
			'database' => __DIR__.'/../database/' . ((!isset($_ENV['DBNAME'])) ? 'empty.sqlite' : $_ENV['DBNAME']), //
			'prefix'   => '',
		),

		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => !isset($_ENV['DBHOST']) ? '' : $_ENV['DBHOST'],
			'database'  => !isset($_ENV['DBNAME']) ? '' : $_ENV['DBNAME'],
			'username'  => !isset($_ENV['DBUSER']) ? '' : $_ENV['DBUSER'],
			'password'  => !isset($_ENV['DBPSSW']) ? '' : $_ENV['DBPSSW'],
			'charset'   => !isset($_ENV['DBCHAR']) ? '' : $_ENV['DBCHAR'],
			'collation' => !isset($_ENV['DBCOLL']) ? '' : $_ENV['DBCOLL'],
			'prefix'    => !isset($_ENV['DBPRFX']) ? '' : $_ENV['DBPRFX'],
		),

		'pgsql' => array(
			'driver'   => 'pgsql',
			'host'     => !isset($_ENV['DBHOST']) ? '' : $_ENV['DBHOST'],
			'database' => !isset($_ENV['DBNAME']) ? '' : $_ENV['DBNAME'],
			'username' => !isset($_ENV['DBUSER']) ? '' : $_ENV['DBUSER'],
			'password' => !isset($_ENV['DBPSSW']) ? '' : $_ENV['DBPSSW'],
			'charset'  => !isset($_ENV['DBCHAR']) ? '' : $_ENV['DBCHAR'],
			'prefix'   => !isset($_ENV['DBPRFX']) ? '' : $_ENV['DBPRFX'],
			'schema'   => 'public',
		),

		'sqlsrv' => array(
			'driver'   => 'sqlsrv',
			'host'     => !isset($_ENV['DBHOST']) ? '' : $_ENV['DBHOST'],
			'database' => !isset($_ENV['DBNAME']) ? '' : $_ENV['DBNAME'],
			'username' => !isset($_ENV['DBUSER']) ? '' : $_ENV['DBUSER'],
			'password' => !isset($_ENV['DBPSSW']) ? '' : $_ENV['DBPSSW'],
			'prefix'   => !isset($_ENV['DBPRFX']) ? '' : $_ENV['DBPRFX'],
		),

	),

	/*
	|--------------------------------------------------------------------------
	| Migration Repository Table
	|--------------------------------------------------------------------------
	|
	| This table keeps track of all the migrations that have already run for
	| your application. Using this information, we can determine which of
	| the migrations on disk haven't actually been run in the database.
	|
	*/

	'migrations' => 'migrations',

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/

	'redis' => array(

		'cluster' => false,

		'default' => array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		),

	),

);
