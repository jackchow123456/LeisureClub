<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

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
            $baseNmae = basename($v->getPath());
            $fileNmae = rtrim($v->getFilename(), '.php');
            $this->app->singleton("{$baseNmae}\\{$fileNmae}", "App\\Repository\\$baseNmae\\$fileNmae");
        }
    }
}
