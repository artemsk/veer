<?php

/* "/code/ext/",
 * + sort, sort_directons, + more_pages
 * + downloads
 */

get('/404',array('uses' => 'IndexController@show404', 'as' => '404'));
Route::resource('/', 'IndexController', array('only' => array('index')));
Route::resource('category', 'CategoryController', array('only' => array('index', 'show')));
Route::resource('attribute', 'AttributeController', array('only' => array('index', 'show')));
Route::resource('filter', 'FilterController', array('only' => array('index', 'show')));
Route::resource('image', 'ImageController', array('only' => array('show')));
//
Route::resource('order', 'OrderController', array('only' => array('index', 'show')));
//
Route::resource('page', 'PageController', array('only' => array('index', 'show')));
//
Route::resource('product', 'ProductController', array('only' => array('index', 'show')));

Route::resource('tag', 'TagController', array('only' => array('index', 'show')));




Route::resource('search', 'SearchController');


get('user/login', array('uses' => 'UserController@login', 'as' => 'user.login'));
post('user/login', array('uses' => 'UserController@loginPost', 'as' => 'user.login.post'));
get('user/basket/add/{id?}', array('uses' => 'UserController@addToCart', 'as' => 'user.basket.add'));
Route::resource('user', 'UserController');


//Route::resource('admin', 'AdminController');