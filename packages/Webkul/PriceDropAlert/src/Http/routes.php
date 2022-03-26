<?php

Route::group(['middleware' => ['web', 'locale', 'theme', 'currency']], function () {
    Route::namespace('Webkul\PriceDropAlert\Http\Controllers\Shop')->group(function () {
        
        Route::post('/subscription', 'SubscriptionController@subscribe')->defaults('_config', [
            'redirect' => 'shop.productOrCategory.index'
        ])->name('shop.price-drop-alert.product.subscription');

        Route::get('/unsubscribe/{token}', 'SubscriptionController@unsubscribe')->defaults('_config', [
            'redirect' => 'shop.productOrCategory.index'
        ])->name('shop.price-drop-alert.product.unsubscribe');
    });
});