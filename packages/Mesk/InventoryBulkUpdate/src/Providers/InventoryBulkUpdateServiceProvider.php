<?php

namespace Mesk\InventoryBulkUpdate\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class InventoryBulkUpdateServiceProvider extends ServiceProvider
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


        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'inventorybulkupdate');

        $this->publishes([
            __DIR__ . '/../../publishable/inventory-update' => public_path('storage/inventory-update'),
        ], 'public');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'inventorybulkupdate');

        
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