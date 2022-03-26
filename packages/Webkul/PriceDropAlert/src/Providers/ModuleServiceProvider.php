<?php

namespace Webkul\PriceDropAlert\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\PriceDropAlert\Models\EmailTemplate::class,
        \Webkul\PriceDropAlert\Models\EmailTemplateTranslation::class,
        \Webkul\PriceDropAlert\Models\PriceDropSubscriber::class,        
    ];
}