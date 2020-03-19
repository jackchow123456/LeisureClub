<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;
use Illuminate\Http\Resources\Json\Resource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 批量注册 Facades
        foreach (Finder::create()->files()->name('*.php')->in(app_path('Facades')) as $k => $v) {
            $baseName = basename($v->getPath());
            $fileName = rtrim($v->getFilename(), '.php');
            $this->app->singleton("{$baseName}\\{$fileName}", "App\\Repository\\$baseName\\$fileName");
        }
        Resource::withoutWrapping();
    }
}
