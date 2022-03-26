<?php

namespace Webkul\PriceDropAlert\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Webkul\PriceDropAlert\PriceDropAlert;
use Webkul\PriceDropAlert\Facades\PriceDropAlert as PriceDropAlertFacade;

class PriceDropAlertServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/../Http/helpers.php';
        
        include __DIR__ . '/../Http/admin-routes.php';

        include __DIR__ . '/../Http/routes.php';

        $this->app->register(EventServiceProvider::class);

        $this->app->register(ModuleServiceProvider::class);

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'price_drop');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'price_drop');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFacades();
        
        $this->registerConfig();
    }

    /**
     * Register Bouncer as a singleton.
     *
     * @return void
     */
    protected function registerFacades()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('priceDrop', PriceDropAlertFacade::class);

        $this->app->singleton('priceDrop', function () {
            return app()->make(PriceDropAlert::class);
        });
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php', 'menu.admin'
        );

        // $this->mergeConfigFrom(
        //     dirname(__DIR__) . '/Config/acl.php', 'acl'
        // );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );
    }
}
