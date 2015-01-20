<?php

/* "/code/ext/",
 * + sort, sort_directons, + more_pages
 */

get('/404',array('uses' => 'IndexController@show404', 'as' => '404'));
Route::resource('/', 'IndexController', array('only' => array('index')));
Route::resource('category', 'CategoryController', array('only' => array('index', 'show')));
Route::resource('attribute', 'AttributeController', array('only' => array('index', 'show')));
Route::resource('filter', 'FilterController', array('only' => array('index', 'show')));
Route::resource('image', 'ImageController', array('only' => array('show')));

get('order/bills/{id?}/{lnk?}', array('uses' => 'OrderController@bills', 'as' => 'order.bills'));
Route::resource('order', 'OrderController', array('only' => array('index', 'show', 'store')));
//
Route::resource('page', 'PageController', array('only' => array('index', 'show')));
//
Route::resource('product', 'ProductController', array('only' => array('index', 'show')));
Route::resource('search', 'SearchController', array('only' => array('index', 'show', 'store')));
Route::resource('tag', 'TagController', array('only' => array('index', 'show')));
//
get('user/login', array('uses' => 'UserController@login', 'as' => 'user.login'));
post('user/login', array('uses' => 'UserController@loginPost', 'as' => 'user.login.post'));
get('user/cart/add/{id?}', array('uses' => 'UserController@addToCart', 'as' => 'user.cart.add'));
get('user/list/add/{listName}/{type?}/{id?}', array('uses' => 'UserController@addToList', 'as' => 'user.list.add'));
get('user/cart/remove/{cartId?}', array('uses' => 'UserController@removeFromCart', 'as' => 'user.cart.remove'));
get('user/list/remove/{listId?}', array('uses' => 'UserController@removeFromList', 'as' => 'user.list.remove'));

get('user/password/remind', 'RemindersController@getRemind');
post('user/password/remind', 'RemindersController@postRemind');
get('user/password/reset/{token?}', 'RemindersController@getReset');
post('user/password/reset', 'RemindersController@postReset');

Route::resource('user', 'UserController', array('only' => array('index', 'show')));

get('download/{lnk?}', array('uses' => 'DownloadController@download', 'as' => 'download.link'));

Route::resource('admin', 'AdminController', array('only' => array('index', 'show', 'update')));