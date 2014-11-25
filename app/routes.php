<?php

//$app['router']->get('/', function() {
    // Because 'Hello, World!' is too mainstream
//    return 'Are you looking for me ?';
//});
/*
Illuminate\Support\Facades\Route::get('/', function() {
    // Because 'Hello, World!' is too mainstream
    return 'Are you looking for me ?';
});

$app['router']->get('/catalog/1', function() {
    // Because 'Hello, World!' is too mainstream
    return 'Are you looking for me 2 ?';
});
 * "/page/","/catalog/","/client/","/order/","/attr/","/keyword/","/search/","/product/","/user/","/code/ext/","/adm/","/filter/"
 * + sort, sort_directons, + more_pages
 */

Route::any('{all}', function($uri)
{
  return 'Are you looking for me ?';
})->where('all', '.*');