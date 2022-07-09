<?php

namespace Mesk\OrderReport\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class OrderReportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Http/admin-routes.php');


        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'orderreport');

        $this->publishes([
            __DIR__ . '/../../publishable/order-update' => public_path('storage/order-update'),
        ], 'public');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'orderreport');

        
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/admin-menu.php', 'menu.admin'
        );

        
    }
}