<?php
define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

if (file_exists($compiled = __DIR__.'/compiled.php'))
{
	require $compiled;
}

Patchwork\Utf8\Bootup::initMbstring();
Illuminate\Support\ClassLoader::register();

if (is_dir($workbench = __DIR__.'/workbench'))
{
	Illuminate\Workbench\Starter::start($workbench);
}

$app = require_once __DIR__.'/start.php';

$app->run();
