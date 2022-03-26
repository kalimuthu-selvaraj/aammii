<?php

namespace Webkul\Mobikul\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    protected $models = [
        \Webkul\Mobikul\Models\FeaturedCategory::class,
        \Webkul\Mobikul\Models\FeaturedCategoryChannel::class,
        \Webkul\Mobikul\Models\BannerImage::class,
        \Webkul\Mobikul\Models\BannerImageTranslation::class,
        \Webkul\Mobikul\Models\Notification::class,
        \Webkul\Mobikul\Models\NotificationTranslation::class,
        \Webkul\Mobikul\Models\Carousel::class,
        \Webkul\Mobikul\Models\CarouselTranslation::class,
        \Webkul\Mobikul\Models\CarouselImages::class,
        \Webkul\Mobikul\Models\ImageProductCarousel::class,
        \Webkul\Mobikul\Models\CustomCollection::class,
        \Webkul\Mobikul\Models\RegisterDevice::class,
        \Webkul\Mobikul\Models\Contact::class,
        \Webkul\Mobikul\Models\SearchTerm::class,
        \Webkul\Mobikul\Models\CompareProduct::class,
        \Webkul\Mobikul\Models\Customer::class,
    ];
}