<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => 'entry', 'as' => "七牛云上传", 'namespace' => "Entry"], function () {
    Route::post('application/upload', 'EntryController@QiNiuUploadExample');
    Route::get('application/export', 'EntryController@exampleExport')->name("excel导出");
    Route::post('application/import', 'EntryController@exampleImport')->name("excel导入");
    Route::get('application/job', 'EntryController@exampleJobs')->name("队列任务");
    Route::post('application/vote', 'EntryController@vote');
});