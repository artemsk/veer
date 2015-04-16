<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

get('/404', array('uses' => 'IndexController@show404', 'as' => '404'));
get("/", array('uses' => 'IndexController@index', 'as' => 'index'));
post("/", array('uses' => 'IndexController@index', 'as' => 'index.post'));

get('user/register', array('uses' => 'UserController@register', 'as' => 'user.register'));
post('user/register', array('uses' => 'UserController@registerPost', 'as' => 'user.register.post'));
get('user/login', array('uses' => 'UserController@login', 'as' => 'user.login'));
post('user/login', array('uses' => 'UserController@loginPost', 'as' => 'user.login.post'));
get('user/logout', array('uses' => 'UserController@logout', 'as' => 'user.logout'));

get('user', array('uses' => 'UserController@index', 'as' => 'user.index'));
get('user/{id?}', array('uses' => 'UserController@show', 'as' => 'user.show'));

get('user/cart', array('uses' => 'UserController@showCart', 'as' => 'user.cart.show'));
post('user/cart', array('uses' => 'UserController@updateCart', 'as' => 'user.cart.update'));
get('user/cart/add/{id?}', array('uses' => 'UserController@addToCart', 'as' => 'user.cart.add'));
get('user/cart/remove/{cartId?}', array('uses' => 'UserController@removeFromCart', 'as' => 'user.cart.remove'));
get('user/list/add/{type?}/{id?}', array('uses' => 'UserController@addToList', 'as' => 'user.list.add'));
get('user/list/remove/{listId?}', array('uses' => 'UserController@removeFromList', 'as' => 'user.list.remove'));
post('user/comment/add', array('uses' => 'UserController@addComment', 'as' => 'user.comment.add'));
post('user/communication/add', array('uses' => 'UserController@addCommunication', 'as' => 'user.communication.add'));

Route::resource('filter', 'FilterController', array('only' => array('index', 'show')));

get('attribute', array('uses' => 'AttributeController@index', 'as' => 'attribute.index'));
get('attribute/{parentId?}/{childId?}', array('uses' => 'AttributeController@show', 'as' => 'attribute.show'));

Route::resource('category', 'CategoryController', array('only' => array('index', 'show')));
Route::resource('tag', 'TagController', array('only' => array('index', 'show')));
Route::resource('search', 'SearchController', array('only' => array('index', 'show', 'store')));
Route::resource('product', 'ProductController', array('only' => array('index', 'show')));

get(env('PAGE_ROUTE','page'), array('uses' => 'PageController@index', 'as' => 'page.index'));
get(env('PAGE_ROUTE','page') .'/{id?}', array('uses' => 'PageController@show', 'as' => 'page.show'));

get('download/{lnk?}', array('uses' => 'DownloadController@download', 'as' => 'download.link'));
get('image/{template}/{filename}', array('uses' => 'ImageController@show', 'as' => 'image.show'));

get('order/bills/{id?}/{lnk?}', array('uses' => 'OrderController@bills', 'as' => 'order.bills'));
get('order/success', array('uses' => 'OrderController@success', 'as' => 'order.success'));
Route::resource('order', 'OrderController', array('only' => array('index', 'show', 'store')));

post('api/lists/{model?}', array('uses' => 'ApiController@lists', 'as' => 'api.lists'));

get('admin/worker/{commands?}', array('uses' => 'AdminController@worker', 'as' => 'admin.worker'));
Route::resource('admin', 'AdminController', array('only' => array('index', 'show', 'update')));

// TODO: rewrite:
// check: AuthenticatesAndRegistersUsers, PasswordBroker, ResetsPasswords
get('user/password/remind', 'RemindersController@getRemind');
post('user/password/remind', 'RemindersController@postRemind');
get('user/password/reset/{token?}', 'RemindersController@getReset');
post('user/password/reset', 'RemindersController@postReset');


