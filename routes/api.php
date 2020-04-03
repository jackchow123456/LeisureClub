<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Router;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Auth::routes();
Route::post('auth/sendSms', 'Auth\loginController@sendSms');
Route::post('auth/checkSms', 'Auth\loginController@checkMobileSms');

Route::group(['prefix' => 'entry', 'as' => "七牛云上传", 'namespace' => "Entry" , 'middleware' => ['jwt.auth']], function () {
    Route::post('application/upload', 'EntryController@QiNiuUploadExample');
    Route::get('application/export', 'EntryController@exampleExport')->name("excel导出");
    Route::post('application/import', 'EntryController@exampleImport')->name("excel导入");
    Route::get('application/job', 'EntryController@exampleJobs')->name("队列任务");
    Route::post('application/vote', 'EntryController@vote');
    Route::get('application/vote', 'EntryController@getVoteResult');
});

Route::get('post', 'Post\PostController@index');

// Api 路由

Route::group([
    'prefix' => 'admin',
    'namespace' => '\App\Admin\Controllers\Api',
], function (Router $router) {
    Route::get('/post/getList', 'PostApiController@getList');
});

Route::group([
    'prefix' => 'admin/'.config('store.prefix', 'store'),
    'namespace' => '\App\Admin\Controllers\Api\Store',
], function (Router $router) {

    // 商品管理
    Route::get('/goods/getGoodsAttr', 'GoodsController@getGoodsAttr')->name('store.goods.getGoodsAttr');
    Route::post('/goods/createGoodsAttr', 'GoodsController@createGoodsAttr')->name('store.goods.createGoodsAttr');
});

// 地区
Route::get('/area', 'AreaController@getArea')->name('获取地区信息');

Route::group([
    'prefix' => config('store.prefix', 'store'),
    'namespace' => 'Store',
], function (Router $router) {
    // 首页轮播
    Route::get('/ad/getBanner', 'AdController@getBanner')->name('store.goods.getGoodsAttr');

    // 商品管理
    Route::get('/goods/getGoodsList', 'GoodsController@getGoodsList')->name('获取商品列表');
    Route::get('/goods/{goods_id}/detail', 'GoodsController@getGoodsDetail')->name('获取商品详情');

    // 购物车相关
    Route::prefix('cart')->middleware('jwt.auth')->group(function ($router) {
        $router->get('/', 'CartController@index')->name('获取购物车列表');
        $router->post('add', 'CartController@store')->name('添加商品到购物车');
        $router->post('/delete', 'CartController@destroy')->name('商品移出购物车');
        $router->put('{id}', 'CartController@update')->name('修改购物车中某个商品的数量')->where('id', '[0-9]+');;
        $router->post('checked', 'CartController@checked')->name('勾选商品');
        $router->get('getChecked', 'CartController@getCartChecked')->name('获取勾选商品列表');
    });

    // 订单相关
    Route::prefix('order')->middleware('jwt.auth')->group(function ($router) {
        $router->get('/', 'OrderController@index')->name('获取订单列表');
        $router->get('{id}', 'OrderController@show')->name('获取订单详情')->where('id', '[0-9]+');;
        $router->post('submit', 'OrderController@store')->name('提交订单');
        $router->put('cancel', 'OrderController@cancel')->name('取消订单');
        $router->post('return', 'OrderController@returnGoods')->name('申请退货');
        $router->post('pay', 'OrderController@pay')->name('支付订单');
    });

    // 地址管理
    Route::prefix('address')->middleware('jwt.auth')->group(function ($router) {
        $router->get('/', 'UserController@getAddressList')->name('获取地址列表');
//        $router->get('{id}', 'OrderController@show')->name('获取订单详情')->where('id', '[0-9]+');;
        $router->post('/', 'UserController@addAddress')->name('添加地址');
//        $router->put('cancel', 'OrderController@cancel')->name('取消订单');
//        $router->post('return', 'OrderController@returnGoods')->name('申请退货');
    });

    // 用户相关
    Route::prefix('user')->middleware('jwt.auth')->group(function ($router) {
        $router->get('/profile', 'UserController@profile')->name('获取用户个人信息');
    });
});
