<?php

namespace Webkul\PriceDropAlert\Facades;

use Illuminate\Support\Facades\Facade;

class PriceDropAlert extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'priceDrop';
    }
}