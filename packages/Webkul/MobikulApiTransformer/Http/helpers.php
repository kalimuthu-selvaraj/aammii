<?php
    use Webkul\MobikulApiTransformer\MobikulApi;

    if (! function_exists('mobikulApi')) {
        function mobikulApi()
        {
            return app()->make(MobikulApi::class);
        }
    }
?>