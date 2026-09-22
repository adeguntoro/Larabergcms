<?php

namespace LarabergCms\LarabergCms;

use Illuminate\Support\ServiceProvider;

class LarabergCmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/larabergcms.php', 'larabergcms');
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'larabergcms');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/larabergcms.php' => config_path('larabergcms.php'),
            ], 'larabergcms-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/larabergcms'),
            ], 'larabergcms-views');

            $this->publishes([
                __DIR__ . '/../resources/vendor/larabergcms' => resource_path('vendor/larabergcms'),
            ], 'larabergcms-assets');
        }
    }
}