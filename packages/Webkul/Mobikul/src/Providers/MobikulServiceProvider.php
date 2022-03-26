<?php

namespace Webkul\Mobikul\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Webkul\Mobikul\Console\Commands\Install;

class MobikulServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'mobikul');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'mobikul');

        $this->overrideModels();

        $this->publishes([
            __DIR__ . '/../../publishable/assets' => public_path('vendor/webkul/mobikul/assets'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/customers/account/profile/edit.blade.php' => resource_path('themes/velocity/views/customers/account/profile/edit.blade.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../Resources/views/shop/customers/account/partials/sidemenu.blade.php' => resource_path('themes/velocity/views/customers/account/partials/sidemenu.blade.php'),
        ]);

        $this->app->register(ModuleServiceProvider::class);

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * override model
     */
    protected function overrideModels()
    {
        //override the customer model
        $this->app->concord->registerModel(
            \Webkul\Customer\Contracts\Customer::class, \Webkul\Mobikul\Models\Customer::class
        );

        //override the compare product model
        $this->app->concord->registerModel(
            \Webkul\Velocity\Contracts\VelocityCustomerCompareProduct::class, \Webkul\Mobikul\Models\CompareProduct::class
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
        
        $this->registerConfig();
    }

    /**
     * Register the console commands of this package
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class
            ]);
        }
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php', 'paymentmethods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php', 'menu.admin'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );
    }
}
