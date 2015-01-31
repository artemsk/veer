<?php

/* "/code/ext/",
 * + sort, sort_directons, + more_pages
 */

get('/404', array('uses' => 'IndexController@show404', 'as' => '404'));
Route::resource('/', 'IndexController', array('only' => array('index')));
Route::resource('category', 'CategoryController', array('only' => array('index', 'show')));

get('/attribute', array('uses' => 'AttributeController@index', 'as' => 'attribute.index'));
get('/attribute/{parentId?}/{childId?}', array('uses' => 'AttributeController@show', 'as' => 'attribute.show'));


Route::resource('filter', 'FilterController', array('only' => array('index', 'show')));
Route::resource('image', 'ImageController', array('only' => array('show')));

get('order/bills/{id?}/{lnk?}', array('uses' => 'OrderController@bills', 'as' => 'order.bills'));
get('order/success', array('uses' => 'OrderController@success', 'as' => 'order.success'));
Route::resource('order', 'OrderController', array('only' => array('index', 'show', 'store')));
//
Route::resource('page', 'PageController', array('only' => array('index', 'show')));
//
Route::resource('product', 'ProductController', array('only' => array('index', 'show')));
Route::resource('search', 'SearchController', array('only' => array('index', 'show', 'store')));
Route::resource('tag', 'TagController', array('only' => array('index', 'show')));

//
get('user/register', array('uses' => 'UserController@register', 'as' => 'user.register'));
post('user/register', array('uses' => 'UserController@registerPost', 'as' => 'user.register.post'));
get('user/login', array('uses' => 'UserController@login', 'as' => 'user.login'));
post('user/login', array('uses' => 'UserController@loginPost', 'as' => 'user.login.post'));
get('user/logout', array('uses' => 'UserController@logout', 'as' => 'user.logout'));

get('user/cart', array('uses' => 'UserController@showCart', 'as' => 'user.cart.show'));
post('user/cart', array('uses' => 'UserController@updateCart', 'as' => 'user.cart.update'));
get('user/cart/add/{id?}', array('uses' => 'UserController@addToCart', 'as' => 'user.cart.add'));
get('user/cart/remove/{cartId?}', array('uses' => 'UserController@removeFromCart', 'as' => 'user.cart.remove'));
get('user/list/add/{type?}/{id?}', array('uses' => 'UserController@addToList', 'as' => 'user.list.add'));
get('user/list/remove/{listId?}', array('uses' => 'UserController@removeFromList', 'as' => 'user.list.remove'));

post('user/comment/add', array('uses' => 'UserController@addComment', 'as' => 'user.comment.add'));
post('user/communication/add', array('uses' => 'UserController@addCommunication', 'as' => 'user.communication.add'));

get('user/password/remind', 'RemindersController@getRemind');
post('user/password/remind', 'RemindersController@postRemind');
get('user/password/reset/{token?}', 'RemindersController@getReset');
post('user/password/reset', 'RemindersController@postReset');

Route::resource('user', 'UserController', array('only' => array('index', 'show')));

get('download/{lnk?}', array('uses' => 'DownloadController@download', 'as' => 'download.link'));

Route::resource('admin', 'AdminController', array('only' => array('index', 'show', 'update')));

get('/information', function() {
	
	echo count(\Illuminate\Support\Facades\DB::getQueryLog()). "<br>";;
    echo round(microtime(true) - LARAVEL_START, 4)."<br>";
	echo number_format(memory_get_usage())."<br><br>";
	
	echo app_path()."<br>";
	echo base_path()."<br>";
	echo public_path()."<br>";
	echo storage_path()."<br>";
});