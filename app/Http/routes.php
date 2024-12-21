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

    Route::get("/", ['uses' => 'IndexController@index', 'as' => 'index']);
    Route::post("/", ['uses' => 'IndexController@index', 'as' => 'index.post']);

    /* auth & key actions - necessary */

    Route::get('user/logout', ['uses' => 'UserController@logout', 'as' => 'user.logout']);
    Route::get('user/register', ['uses' => 'UserController@register', 'as' => 'user.register']);
    Route::post('user/register', ['uses' => 'UserController@registerPost', 'as' => 'user.register.post']);

    Route::get('user/{id?}', ['uses' => 'UserController@show', 'as' => 'user.show']);

    Route::get('user/cart', ['uses' => 'UserController@showCart', 'as' => 'user.cart.show']);
    Route::post('user/cart', ['uses' => 'UserController@updateCart', 'as' => 'user.cart.update']);
    Route::get('user/cart/add/{id?}', ['uses' => 'UserController@addToCart', 'as' => 'user.cart.add']);
    Route::get('user/cart/remove/{cartId?}', ['uses' => 'UserController@removeFromCart', 'as' => 'user.cart.remove']);
    Route::get('user/list/add/{type?}/{id?}', ['uses' => 'UserController@addToList', 'as' => 'user.list.add']);
    Route::get('user/list/remove/{listId?}', ['uses' => 'UserController@removeFromList', 'as' => 'user.list.remove']);
    Route::post('user/comment/add', ['uses' => 'UserController@addComment', 'as' => 'user.comment.add']);
    Route::post('user/communication/add', ['uses' => 'UserController@addCommunication', 'as' => 'user.communication.add']);

    /* filter & search */

    Route::resource('filter', 'FilterController', ['only' => ['index', 'show']]);
    Route::resource('search', 'SearchController', ['only' => ['index', 'show', 'store']]);

    /* main entities - page|articles & product */

    Route::resource('product', 'ProductController', ['only' => ['index', 'show']]);
    Route::get(config('veer.page_route', 'page'), ['uses' => 'PageController@index', 'as' => 'page.index']);
    Route::get(config('veer.page_route', 'page') .'/{id}', ['uses' => 'PageController@show', 'as' => 'page.show']);

    /* elements */

    Route::get('attribute', ['uses' => 'AttributeController@index', 'as' => 'attribute.index']);
    Route::get('attribute/{parentId?}/{childId?}', ['uses' => 'AttributeController@show', 'as' => 'attribute.show']);

    Route::resource('category', 'CategoryController', ['only' => ['index', 'show']]);
    Route::resource('tag', 'TagController', ['only' => ['index', 'show']]);

    Route::get('image/{template}/{filename}', ['uses' => 'ImageController@show', 'as' => 'image.show']);

    /* e-commerce */

    Route::get('order/success', ['uses' => 'OrderController@success', 'as' => 'order.success']);
    Route::resource('order', 'OrderController', ['only' => ['index', 'show', 'store']]);

    /* custom */
    Route::any('custom/{params?}', ['uses' => 'IndexController@custom', 'as' => 'custom.index']);

    Route::get('user/password/remind', 'RemindersController@getRemind');
    Route::post('user/password/remind', 'RemindersController@postRemind');
    Route::get('user/password/reset/{token?}', 'RemindersController@getReset');
    Route::post('user/password/reset', 'RemindersController@postReset');

});
