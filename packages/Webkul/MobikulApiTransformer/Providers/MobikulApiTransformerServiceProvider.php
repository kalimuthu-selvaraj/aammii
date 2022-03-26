<?php

namespace Webkul\MobikulApiTransformer\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Foundation\AliasLoader;
use Webkul\MobikulApiTransformer\Http\Middleware\ValidateAPIHeader;
use Webkul\Shipping\Facades\Shipping;
use Webkul\MobikulApiTransformer\MobikulApi;
use Webkul\MobikulApiTransformer\Facades\MobikulApi as MobikulApiFacade;

class MobikulApiTransformerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        include __DIR__ . '/../Http/helpers.php';

        $this->loadRoutesFrom(__DIR__.'/../Http/routes.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'mobikul-api');

        // $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'mobikul-api');

        $router->aliasMiddleware('validateAPIHeader', ValidateAPIHeader::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFacades();
    }

    /**
     * Register Bouncer as a singleton.
     *
     * @return void
     */
    protected function registerFacades()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('mobikulApi', MobikulApiFacade::class);

        $this->app->singleton('mobikulApi', function () {
            return app()->make(MobikulApi::class);
        });

        $loader->alias('shipping', Shipping::class);

        $this->app->singleton('shipping', function () {
            return new shipping();
        });

        $this->app->bind('shipping', 'Webkul\MobikulApiTransformer\Shipping');
    }
}
