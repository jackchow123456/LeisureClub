<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->get('/test', 'HomeController@test')->name('admin.test');
    $router->resources([
        'users' => UserController::class,
        'posts' => PostController::class,
    ]);

    $router->get('api/users', 'PostController@users');
    $router->post('upload_file', 'PostController@upload_file');

    //文件管理
    $router->get('fileManager', 'FileManagerController@index')->name('fileManager.index')->middleware('permission:fileManager');
    $router->get('fileManager/image', 'FileManagerController@image')->name('fileManager.image');
    $router->get('fileManager/folders', 'FileManagerController@folders')->name('fileManager.folders');
    $router->get('fileManager/multiUpload', 'FileManagerController@multiUpload')->name('fileManager.multiUpload');
    $router->post('fileManager/listFolders', 'FileManagerController@listFolders')->name('fileManager.listFolders');
    $router->post('fileManager/files', 'FileManagerController@files')->name('fileManager.files');
    $router->post('fileManager/create', 'FileManagerController@create')->name('fileManager.create');
    $router->post('fileManager/directory', 'FileManagerController@directory')->name('fileManager.directory');
    $router->post('fileManager/delete', 'FileManagerController@delete')->name('fileManager.delete');
    $router->post('fileManager/move', 'FileManagerController@move')->name('fileManager.move');
    $router->post('fileManager/copy', 'FileManagerController@copy')->name('fileManager.copy');
    $router->post('fileManager/rename', 'FileManagerController@rename')->name('fileManager.rename');
    $router->post('fileManager/upload', 'FileManagerController@upload')->name('fileManager.upload');

    // 商城管理
    Route::group([
        'prefix' => config('store.prefix', 'store'),
        'namespace' => 'Store',
    ], function (Router $router) {
        Route::get('/', 'StoreController@index')->name('store.index');

        // 商品管理
        $router->resources(['goods' => GoodsController::class]);
        $router->resources(['ad' => AdController::class]);
    });


});
