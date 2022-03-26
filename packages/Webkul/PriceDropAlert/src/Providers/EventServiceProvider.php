<?php

namespace Webkul\PriceDropAlert\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Event::listen('bagisto.shop.products.price.after', function($viewRenderEventManager) {
        //     $viewRenderEventManager->addTemplate('price_drop::shop.products.view.price-drop-alert');
        // });

        Event::listen('admin.pricedrop.create.after', 'Webkul\PriceDropAlert\Listeners\TemplateTranslation@afterTemplateCreatedUpdated');

        Event::listen('admin.pricedrop.update.after', 'Webkul\PriceDropAlert\Listeners\TemplateTranslation@afterTemplateCreatedUpdated');

        Event::listen('catalog.product.update.after', 'Webkul\PriceDropAlert\Listeners\PriceDropNotification@afterProductUpdate');
    }
}
