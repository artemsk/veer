<?php

$app = new Illuminate\Foundation\Application;

$env = $app->detectEnvironment(array(

	'local' => array('Jerry13'),

));

$app->bindInstallPaths(array(
        'app' => __DIR__ . '/app',
        'public' => __DIR__ . '/assets',
        'base' => __DIR__ . '',
        'storage' => __DIR__ . '/app/storage',
));

$framework = $app['path.base'].'/vendor/laravel/framework/src';

require $framework.'/Illuminate/Foundation/start.php';

return $app;

