<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');


Artisan::command('build', function () {

    //COMPOSER 包加载
    system("composer install");

    //优化COMPOSER自动加载
    system("composer dump-autoload --optimize");

    //更新路由缓存
    $this->call('route:clear', []);

    //更新配置缓存
    $this->call('config:clear', []);

})->describe('项目构建');