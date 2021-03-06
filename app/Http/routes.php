<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Sample.
|
| Admin, user login etc. routes are loaded in package routes files. It is possible
| to ovewrite those routes here. Please, check routes.php file in artemsk/veer-core
| package.
|
*/

/* common */

Route::group(['namespace' => 'Veer\Http\Controllers'], function() {

    get("/", array('uses' => 'IndexController@index', 'as' => 'index'));
    post("/", array('uses' => 'IndexController@index', 'as' => 'index.post'));

    /* auth & key actions - necessary */

    get('user/logout', array('uses' => 'UserController@logout', 'as' => 'user.logout'));
    get('user/register', array('uses' => 'UserController@register', 'as' => 'user.register'));
    post('user/register', array('uses' => 'UserController@registerPost', 'as' => 'user.register.post'));

    get('user/{id?}', array('uses' => 'UserController@show', 'as' => 'user.show'));

    get('user/cart', array('uses' => 'UserController@showCart', 'as' => 'user.cart.show'));
    post('user/cart', array('uses' => 'UserController@updateCart', 'as' => 'user.cart.update'));
    get('user/cart/add/{id?}', array('uses' => 'UserController@addToCart', 'as' => 'user.cart.add'));
    get('user/cart/remove/{cartId?}', array('uses' => 'UserController@removeFromCart', 'as' => 'user.cart.remove'));
    get('user/list/add/{type?}/{id?}', array('uses' => 'UserController@addToList', 'as' => 'user.list.add'));
    get('user/list/remove/{listId?}', array('uses' => 'UserController@removeFromList', 'as' => 'user.list.remove'));
    post('user/comment/add', array('uses' => 'UserController@addComment', 'as' => 'user.comment.add'));
    post('user/communication/add', array('uses' => 'UserController@addCommunication', 'as' => 'user.communication.add'));

    /* filter & search */

    Route::resource('filter', 'FilterController', array('only' => array('index', 'show')));
    Route::resource('search', 'SearchController', array('only' => array('index', 'show', 'store')));

    /* main entities - page|articles & product */

    Route::resource('product', 'ProductController', array('only' => array('index', 'show')));
    get(config('veer.page_route', 'page'), array('uses' => 'PageController@index', 'as' => 'page.index'));
    get(config('veer.page_route', 'page') .'/{id}', array('uses' => 'PageController@show', 'as' => 'page.show'));

    /* elements */

    get('attribute', array('uses' => 'AttributeController@index', 'as' => 'attribute.index'));
    get('attribute/{parentId?}/{childId?}', array('uses' => 'AttributeController@show', 'as' => 'attribute.show'));

    Route::resource('category', 'CategoryController', array('only' => array('index', 'show')));
    Route::resource('tag', 'TagController', array('only' => array('index', 'show')));

    get('image/{template}/{filename}', array('uses' => 'ImageController@show', 'as' => 'image.show'));

    /* e-commerce */

    get('order/success', array('uses' => 'OrderController@success', 'as' => 'order.success'));
    Route::resource('order', 'OrderController', array('only' => array('index', 'show', 'store')));

    /* custom */
    Route::any('custom/{params?}', ['uses' => 'IndexController@custom', 'as' => 'custom.index']);

    get('user/password/remind', 'RemindersController@getRemind');
    post('user/password/remind', 'RemindersController@postRemind');
    get('user/password/reset/{token?}', 'RemindersController@getReset');
    post('user/password/reset', 'RemindersController@postReset');

});
