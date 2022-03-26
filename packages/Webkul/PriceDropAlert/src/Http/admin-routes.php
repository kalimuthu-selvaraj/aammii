<?php

Route::group(['middleware' => ['web']], function () {
    Route::prefix('admin')->group(function () {
        
        // Admin Routes
        Route::group(['middleware' => ['admin']], function () {

            // Price Drop Alert Routes
            Route::get('/price_alert_log', 'Webkul\PriceDropAlert\Http\Controllers\Admin\PriceDropController@index')->defaults('_config', [
                'view' => 'price_drop::admin.settings.price-drop-alert.index'
            ])->name('admin.price-alert.log.index');
            
            // Email Template Routes
            Route::get('/email_template', 'Webkul\PriceDropAlert\Http\Controllers\Admin\EmailTemplateController@index')->defaults('_config', [
                'view' => 'price_drop::admin.settings.email-template.index'
            ])->name('admin.price-alert.email-template.index');

            Route::get('/email_template/create', 'Webkul\PriceDropAlert\Http\Controllers\Admin\EmailTemplateController@create')->defaults('_config', [
                'view' => 'price_drop::admin.settings.email-template.create'
            ])->name('admin.price-alert.email-template.create');

            Route::post('/email_template/create', 'Webkul\PriceDropAlert\Http\Controllers\Admin\EmailTemplateController@store')->defaults('_config', [
                'redirect' => 'admin.price-alert.email-template.index'
            ])->name('admin.price-alert.email-template.store');

            Route::get('/email_template/edit/{id}', 'Webkul\PriceDropAlert\Http\Controllers\Admin\EmailTemplateController@edit')->defaults('_config', [
                'view' => 'price_drop::admin.settings.email-template.edit'
            ])->name('admin.price-alert.email-template.edit');

            Route::put('/email_template/edit/{id}', 'Webkul\PriceDropAlert\Http\Controllers\Admin\EmailTemplateController@update')->defaults('_config', [
                'redirect' => 'admin.price-alert.email-template.index'
            ])->name('admin.price-alert.email-template.update');

            Route::post('/email_template/delete/{id}', 'Webkul\PriceDropAlert\Http\Controllers\Admin\EmailTemplateController@destroy')->name('admin.price-alert.email-template.delete');
        });
    });
});