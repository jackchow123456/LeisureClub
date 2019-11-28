<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resources([
        'users' => UserController::class,
        'posts' => PostController::class,
    ]);

    $router->get('api/users', 'PostController@users');
    $router->post('upload_file', 'PostController@upload_file');

    // 商城管理
    Route::group([
        'prefix' => config('store.prefix', 'store'),
        'namespace' => 'Store',
    ], function (Router $router) {
        Route::get('/', 'StoreController@index')->name('store.index');

        // 商品管理
        $router->resources(['goods' => GoodsController::class]);
    });


});
