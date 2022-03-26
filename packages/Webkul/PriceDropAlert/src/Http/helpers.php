<?php
    use Webkul\PriceDropAlert\PriceDropAlert;

    if (! function_exists('priceDrop')) {
        function priceDrop()
        {
            return app()->make(PriceDropAlert::class);
        }
    }
?>