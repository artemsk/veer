<?php

/* "/code/ext/",
 * + sort, sort_directons, + more_pages
 * + downloads
 */

get('/404',array('uses' => 'IndexController@show404', 'as' => '404'));
Route::resource('/', 'IndexController', array('only' => array('index')));
Route::resource('category', 'CategoryController');
Route::resource('tag', 'TagController');
Route::resource('attribute', 'AttributeController');
Route::resource('image', 'ImageController');
Route::resource('product', 'ProductController');
Route::resource('page', 'PageController');
Route::resource('search', 'SearchController');
Route::resource('filter', 'FilterController');

get('user/login', array('uses' => 'UserController@login', 'as' => 'user.login'));
post('user/login', array('uses' => 'UserController@loginPost', 'as' => 'user.login.post'));
Route::resource('user', 'UserController');
Route::resource('order', 'OrderController');

Route::resource('admin', 'AdminController');