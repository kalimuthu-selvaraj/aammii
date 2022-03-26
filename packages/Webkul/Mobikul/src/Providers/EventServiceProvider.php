<?php

namespace Webkul\Mobikul\Providers;

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
        Event::listen('bagisto.admin.layout.head', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('mobikul::admin.layouts.style');
        });
        
        Event::listen('mobikul.banner-image.create.after', 'Webkul\Mobikul\Listeners\BannerTranslation@afterBannerImageCreatedUpdated');

        Event::listen('mobikul.banner-image.update.after', 'Webkul\Mobikul\Listeners\BannerTranslation@afterBannerImageCreatedUpdated');
        
        Event::listen('mobikul.notification.create.after', 'Webkul\Mobikul\Listeners\NotificationTranslation@afterNotificationCreatedUpdated');

        Event::listen('mobikul.notification.update.after', 'Webkul\Mobikul\Listeners\NotificationTranslation@afterNotificationCreatedUpdated');
        
        Event::listen('mobikul.carousel.create.after', 'Webkul\Mobikul\Listeners\CarouselTranslation@afterCarouselCreatedUpdated');

        Event::listen('mobikul.carousel.update.after', 'Webkul\Mobikul\Listeners\CarouselTranslation@afterCarouselCreatedUpdated');

        Event::listen('shop.search.after', 'Webkul\Mobikul\Listeners\Search@afterSearch');
    }
}
