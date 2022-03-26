<?php

    Route::group(['middleware' => ['web']], function () {
        
        Route::prefix('admin')->group(function () {
            
            // Admin Routes
            Route::group(['namespace' => 'Webkul\Mobikul\Http\Controllers', 'middleware' => ['admin']], function () {

                // Featuredcategories routes
                Route::prefix('featuredcategories')->group(function () {
                    // index
                    Route::get('/', 'FeaturedCategoryController@index')->defaults('_config', [
                        'view' => 'mobikul::admin.featured-category.index'
                    ])->name('mobikul.featured-category.index');

                    // create
                    Route::get('/create', 'FeaturedCategoryController@create')->defaults('_config', [
                        'view' => 'mobikul::admin.featured-category.create'
                    ])->name('mobikul.featured-category.create');

                    //store the featuredcategories
                    Route::post('/store', 'FeaturedCategoryController@store')->defaults('_config', [
                        'redirect' => 'mobikul.featured-category.index'
                    ])->name('mobikul.featured-category.store');

                    //add featuredcategories pages
                    Route::get('/edit/{id}', 'FeaturedCategoryController@edit')->defaults('_config', [
                        'view' => 'mobikul::admin.featured-category.edit'
                    ])->name('mobikul.featured-category.edit');

                    //update the featuredcategories
                    Route::post('/update', 'FeaturedCategoryController@update')->defaults('_config', [
                        'redirect' => 'mobikul.featured-category.index'
                    ])->name('mobikul.featured-category.update');

                    //delete the featuredcategories
                    Route::post('/delete/{id}', 'FeaturedCategoryController@delete')->defaults('_config', [
                        'redirect' => 'mobikul.featured-category.index'
                    ])->name('mobikul.featured-category.delete');

                    //mass-delete the featuredcategories
                    Route::post('/massdelete', 'FeaturedCategoryController@massDestroy')->defaults('_config', [
                        'redirect' => 'mobikul.featured-category.index'
                    ])->name('mobikul.featured-category.mass-delete');

                    //mass-update the featuredcategories
                    Route::post('/massupdate', 'FeaturedCategoryController@massUpdate')->defaults('_config', [
                        'redirect' => 'mobikul.featured-category.index'
                    ])->name('mobikul.featured-category.mass-update');
                });

                //Banner images routes
                Route::prefix('bannerimage')->group(function () {
                    // banner images index
                    Route::get('/', 'BannerImagesController@index')->defaults('_config', [
                        'view' => 'mobikul::admin.banner-image.index'
                    ])->name('mobikul.banner-image.index');

                    // banner images create
                    Route::get('/create', 'BannerImagesController@create')->defaults('_config', [
                        'view' => 'mobikul::admin.banner-image.create'
                    ])->name('mobikul.banner-image.create');

                    //store the bannerimage
                    Route::post('/store', 'BannerImagesController@store')->defaults('_config', [
                        'redirect' => 'mobikul.banner-image.index'
                    ])->name('mobikul.banner-image.store');

                    //add bannerimage pages
                    Route::get('/edit/{id}', 'BannerImagesController@edit')->defaults('_config', [
                        'view' => 'mobikul::admin.banner-image.edit'
                    ])->name('mobikul.banner-image.edit');

                    //update the bannerimage
                    Route::put('/edit/{id}', 'BannerImagesController@update')->defaults('_config', [
                        'redirect' => 'mobikul.banner-image.index'
                    ])->name('mobikul.banner-image.update');

                    //delete the bannerimage
                    Route::post('/delete/{id}', 'BannerImagesController@delete')->defaults('_config', [
                        'redirect' => 'mobikul.banner-image.index'
                    ])->name('mobikul.banner-image.delete');

                    //mass-delete the bannerimage
                    Route::post('/massdelete', 'BannerImagesController@massDestroy')->defaults('_config', [
                        'redirect' => 'mobikul.banner-image.index'
                    ])->name('mobikul.banner-image.mass-delete');

                    //mass-update the bannerimage
                    Route::post('/massupdate', 'BannerImagesController@massUpdate')->defaults('_config', [
                        'redirect' => 'mobikul.banner-image.index'
                    ])->name('mobikul.banner-image.mass-update');
                });

                // Notificationc routes
                Route::prefix('notification')->group(function () {
                    // notification index
                    Route::get('/', 'NotificationController@index')->defaults('_config', [
                        'view' => 'mobikul::admin.notification.index'
                    ])->name('mobikul.notification.index');

                    //add notification pages
                    Route::get('/add', 'NotificationController@create')->defaults('_config', [
                        'view' => 'mobikul::admin.notification.create'
                    ])->name('mobikul.notification.create');

                    //store the notification
                    Route::post('/store', 'NotificationController@store')->defaults('_config', [
                        'redirect' => 'mobikul.notification.index'
                    ])->name('mobikul.notification.store');

                    //show edit notification pages
                    Route::get('/edit/{id}', 'NotificationController@edit')->defaults('_config', [
                        'view' => 'mobikul::admin.notification.edit'
                    ])->name('mobikul.notification.edit');

                    //update the notification
                    Route::put('/edit/{id}', 'NotificationController@update')->defaults('_config', [
                        'redirect' => 'mobikul.notification.index'
                    ])->name('mobikul.notification.update');

                    //delete the notification
                    Route::post('/delete/{id}', 'NotificationController@delete')->defaults('_config', [
                        'redirect' => 'mobikul.notification.index'
                    ])->name('mobikul.notification.delete');

                    //mass-delete the notification
                    Route::post('/massdelete', 'NotificationController@massDestroy')->defaults('_config', [
                        'redirect' => 'mobikul.notification.index'
                    ])->name('mobikul.notification.mass-delete');

                    //mass-update the notification
                    Route::post('/massupdate', 'NotificationController@massUpdate')->defaults('_config', [
                        'redirect' => 'mobikul.notification.index'
                    ])->name('mobikul.notification.mass-update');

                    //send the notification
                    Route::get('/sendnotification/{notification_id}', 'NotificationController@sendNotification')->name('mobikul.notification.send-notification');

                    //mass-update the notification
                    Route::post('/exist', 'NotificationController@exist')->name('mobikul.notification.cat-product-id');
                });

                // Carousel routes
                Route::prefix('carousel')->group(function () {
                    //index page
                    Route::get('/', 'CarouselController@index')->defaults('_config', [
                        'view' => 'mobikul::admin.carousel.index'
                    ])->name('mobikul.carousel.index');

                    //add carousel pages
                    Route::get('/add', 'CarouselController@create')->defaults('_config', [
                    'view' => 'mobikul::admin.carousel.create'
                    ])->name('mobikul.carousel.create');

                    //store the carousel
                    Route::post('/store', 'CarouselController@store')->defaults('_config', [
                        'redirect' => 'mobikul.carousel.index'
                    ])->name('mobikul.carousel.store');

                    //show carousel pages
                    Route::get('/edit/{carousel_id}', 'CarouselController@show')->defaults('_config', [
                    'view' => 'mobikul::admin.carousel.edit'
                    ])->name('mobikul.carousel.edit');

                    //update the carousel
                    Route::put('/update', 'CarouselController@update')->defaults('_config', [
                        'redirect' => 'mobikul.carousel.index'
                    ])->name('mobikul.carousel.update');

                    //mass-delete the carousel
                    Route::post('/massdelete', 'CarouselController@massDestroy')->defaults('_config', [
                    'redirect' => 'mobikul.carousel.index'
                    ])->name('mobikul.carousel.mass-delete');

                    //mass-update the carousel
                    Route::post('/massupdate', 'CarouselController@massUpdate')->defaults('_config', [
                    'redirect' => 'mobikul.carousel.index'
                    ])->name('mobikul.carousel.mass-update');

                    //delete the carousel
                    Route::post('/delete/{id}', 'CarouselController@delete')->defaults('_config', [
                        'redirect' => 'mobikul.carousel.index'
                    ])->name('mobikul.carousel.delete');

                    //show carousel pages
                    Route::get('/assign/{carousel_id}', 'CarouselController@assign')->defaults('_config', [
                        'view' => 'mobikul::admin.carousel.assign'
                    ])->name('mobikul.carousel.assign');
                });

                // Carousel Image routes
                Route::prefix('image-carousel')->group(function () {
                    //index page
                    Route::get('/', 'CarouselImageController@index')->defaults('_config', [
                        'view' => 'mobikul::admin.carousel-image.index'
                    ])->name('mobikul.carousel.image.index');

                    //add carousel Image pages
                    Route::get('/add', 'CarouselImageController@create')->defaults('_config', [
                    'view' => 'mobikul::admin.carousel-image.create'
                    ])->name('mobikul.carousel.image.create');

                    //store the image carousel
                    Route::post('/store', 'CarouselImageController@store')->defaults('_config', [
                        'redirect' => 'mobikul.carousel.image.index'
                    ])->name('mobikul.carousel.image.store');

                    //add carousel Image pages
                    Route::get('/edit/{image_carousel_id}', 'CarouselImageController@show')->defaults('_config', [
                        'view' => 'mobikul::admin.carousel-image.edit'
                    ])->name('mobikul.carousel.image.edit');

                    //update the carousel Image
                    Route::post('/update', 'CarouselImageController@update')->defaults('_config', [
                        'redirect' => 'mobikul.carousel-image.index'
                    ])->name('mobikul.carousel.image.update');

                    //delete the carousel Image
                    Route::post('/delete/{id}', 'CarouselImageController@delete')->defaults('_config', [
                        'redirect' => 'mobikul.carousel-image.index'
                    ])->name('mobikul.carousel.image.delete');

                    //mass-delete the carousel Image
                    Route::post('/massdelete', 'CarouselImageController@massDestroy')->defaults('_config', [
                        'redirect' => 'mobikul.carousel-image.index'
                    ])->name('mobikul.carousel.image.mass-delete');

                    //mass-update the carousel image
                    Route::post('/massupdate', 'CarouselImageController@massUpdate')->defaults('_config', [
                        'redirect' => 'mobikul.carousel-image.index'
                    ])->name('mobikul.carousel.image.mass-update');

                    //assign the carousel image to the carousel
                    Route::post('/assigncarousel/images', 'CarouselController@assignCarouselImages')->defaults('_config', [
                        'redirect' => 'mobikul.carousel-image.index'
                    ])->name('mobikul.carousel.image.assigncarousel.image');

                    //assign the product to the carousel
                    Route::post('/assignproduct/product', 'CarouselController@assignCarouselProducts')->defaults('_config', [
                        'redirect' => 'mobikul.carousel-image.index'
                    ])->name('mobikul.carousel.image.assigncarousel.product');
                });

                // Custom-Collection Routes
                Route::prefix('custom-collection')->group(function () {
                    //index page
                    Route::get('/', 'CustomCollectionController@index')->defaults('_config', [
                        'view' => 'mobikul::admin.custom-collection.index'
                    ])->name('mobikul.custom-collection.index');

                    // create custom-collection page
                    Route::get('/create', 'CustomCollectionController@create')->defaults('_config', [
                        'view' => 'mobikul::admin.custom-collection.create'
                    ])->name('mobikul.custom-collection.create');

                    //store the custom-collection
                    Route::post('/create', 'CustomCollectionController@store')->defaults('_config', [
                        'redirect' => 'mobikul.custom-collection.index'
                    ])->name('mobikul.custom-collection.store');

                    //mass-delete the custom-collection
                    Route::post('/massdelete',      'CustomCollectionController@massDestroy')->defaults('_config', [
                    'redirect' => 'mobikul.custom-collection.index'
                    ])->name('mobikul.custom-collection.mass-delete');

                    //mass-update the custom-collection
                    Route::post('/massupdate', 'CustomCollectionController@massUpdate')->defaults('_config', [
                    'redirect' => 'mobikul.custom-collection.index'
                    ])->name('mobikul.custom-collection.mass-update');

                    //delete the custom-collection
                    Route::post('/delete/{id}', 'CustomCollectionController@destroy')->defaults('_config', [
                        'redirect' => 'mobikul.custom-collection.index'
                    ])->name('mobikul.custom-collection.delete');
                    
                    //product search for linked products
                    Route::get('/search', 'CustomCollectionController@collectionSearch')->name('mobikul.custom-collection.search');
                });

                // Mobikul Order history routes
                Route::prefix('order-history')->group(function () {
                    // Order history dataGrid
                    Route::get('/', 'OrderController@index')->defaults('_config', [
                        'view' => 'mobikul::admin.orders.index'
                    ])->name('mobikul.order.index');
                });
                
                // Product search for linked products
                Route::get('attributes/search', 'CustomCollectionController@brandSearch')->defaults('_config', [
                    'view' => 'mobikul::admin.custom-collection.create'
                ])->name('mobikul.custom-collection.attributes.brandsearch');
            });
        });
    });

    Route::group(['middleware' => ['web', 'locale', 'theme', 'currency']], function () {
        Route::namespace('Webkul\Mobikul\Http\Controllers')->group(function () {
            Route::get('/categorysearch', 'ShopController@search')
                    ->name('velocity.search.index')
                    ->defaults('_config', [
                        'view' => 'shop::search.search'
            ]);
        });
    });
