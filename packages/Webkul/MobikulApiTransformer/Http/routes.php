<?php

Route::group(['prefix' => 'mobikulhttp/'], function ($router) {

    Route::group(['middleware' => ['locale', 'theme', 'currency']], function ($router) {

        Route::namespace('Webkul\MobikulApiTransformer\Http\Controllers\Shop')->group(function () {

            // Catalog APIs routes
            Route::prefix('catalog')->group(function () {
                // Homepagedata API
                Route::get('/homepagedata' , 'Catalog\HomePageController@index')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Catalog\Homepage'
                ]);

                // CategoryPageData API
                Route::get('/categoryPageData' , 'Catalog\CategoryPageController@index')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Catalog\CategoryPage'
                ]);

                // Productpagedata API
                Route::get('/productpagedata' , 'Catalog\ProductPageController@index')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Catalog\ProductPage'
                ]);

                // Productcollection API
                Route::get('/productCollection' , 'Catalog\ProductPageController@collection')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Catalog\ProductCollection'
                ]);

                // Addtowishlist API
                Route::post('/addtowishlist' , 'Catalog\WishListController@index')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Catalog\WishList'
                ]);

                // Comparelist API
                Route::get('/comparelist' , 'Catalog\CompareController@index')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Catalog\CompareList',
                ]);

                // AddtoCompare API
                Route::post('/addtocompare' , 'Catalog\CompareController@add')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Catalog\CompareList',
                ]);

                // RemoveFromCompare API
                Route::delete('/removefromcompare', 'Catalog\CompareController@remove')->defaults('_config', [
                    'repository' => 'Webkul\Velocity\Repositories\VelocityCustomerCompareProductRepository',
                    'authorization_required' => true
                ]);

                // Ratingformdata API
                Route::get('/ratingformdata', 'Catalog\RatingController@index');

                // Getcategorylist API
                Route::get('/getcategorylist', 'Catalog\CategoryPageController@categoryList');

                // Productshare API
                Route::post('/productshare', 'Catalog\ProductShareController@index')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Catalog\ProductShare'
                ]);
            });

            // Customer APIs routes
            Route::prefix('customer')->group(function () {

                // Register/Createaccount API
                Route::post('/createaccount' , 'Customer\AccountController@index')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\CreateAccount'
                ]);

                // AccountInfoData API
                Route::get('/accountinfodata' , 'Customer\AccountController@get')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\InfoAccount'
                ]);

                // Save Account Info API
                Route::post('/saveaccountinfo' , 'Customer\AccountController@save')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\SaveAccount'
                ]);

                // Forgot Password API
                Route::post('/forgotpassword', 'Customer\AccountController@forgot');

                // Check CustomerByEmail API
                Route::get('/checkcustomerbyemail' , 'Customer\AccountController@checkCustomerByEmail')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\CheckCustomerByEmail'
                ]);

                // Createaccountformdata API
                Route::get('/createaccountformdata' , 'Customer\AccountController@formData');

                // Login API
                Route::post('/login' , 'Customer\AccountController@login')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\Login'
                ]);

                // Customer's Address APIs
                // AddressFormData API
                Route::get('/addressformdata' , 'Customer\AddressController@formData')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\AddressFormData'
                ]);

                // AddressBookData API
                Route::get('/addressbookdata' , 'Customer\AddressController@bookData')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\AddressBookData'
                ]);

                // SaveAddress API
                Route::post('/saveaddress' , 'Customer\AddressController@save');

                // DeleteAddress API
                Route::delete('/deleteaddress' , 'Customer\AddressController@delete');

                // Customer Order APIs
                // OrderDetails API
                Route::get('/orderdetails' , 'Customer\OrderController@view')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\OrderDetails'
                ]);

                // OrderList API
                Route::get('/orderlist' , 'Customer\OrderController@list')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\OrderList'
                ]);

                // Reorder API
                Route::post('/reorder' , 'Customer\OrderController@reorder');

                // InvoiceView API
                Route::get('/invoiceview' , 'Customer\OrderController@invoice')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\Invoice'
                ]);

                // ShipmentView API
                Route::get('/shipmentview', 'Customer\OrderController@shipment')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\Shipment'
                ]);

                // SaveReview API
                Route::post('/savereview','Customer\ReviewController@save');

                // ReviewList API
                Route::get('/reviewlist','Customer\ReviewController@list');

                // ReviewsDetail API
                Route::get('/reviewdetails','Customer\ReviewController@view');

                // Wishlist API
                Route::get('/wishlist' , 'Customer\WishlistController@list')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\Wishlist'
                ]);

                // RemoveFromWishlist API
                Route::delete('/removefromwishlist' , 'Customer\WishlistController@remove');

                // WishlistToCart API
                Route::post('/wishlisttocart', 'Customer\WishlistController@moveToCart');

                // MyDownloadsList API
                Route::get('/mydownloadslist' , 'Customer\DownloadController@list')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\DownloadList'
                ]);
            });

            // Checkout API Routes
            Route::prefix('checkout')->group(function () {
                // Cart APIs
                // Addtocart API
                Route::post('/addtocart', 'Checkout\CartController@add');

                // CartDetail API
                Route::get('/cartdetails' , 'Checkout\CartController@view');

                // EmptyCart API
                Route::post('/emptycart', 'Checkout\CartController@empty');

                // UpdateCart API
                Route::post('/updatecart', 'Checkout\CartController@update');

                // RemoveCartItem API
                Route::post('/removecartitem', 'Checkout\CartController@remove');

                // Checkout APIs
                // CustomerRegister at checkout API
                Route::post('/accountcreate', 'Checkout\CheckoutController@register');

                // Customer's addresses at checkout API
                Route::get('/checkoutaddress', 'Checkout\CheckoutController@addresses')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Checkout\Addresses'
                ]);

                // Customer's address form data at checkout API
                Route::get('/checkoutaddressformdata', 'Checkout\CheckoutController@addressFormData')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Checkout\AddressFormData'
                ]);

                // ShippingMethods API
                Route::post('/shippingmethods', 'Checkout\CheckoutController@shippingMethods')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Checkout\ShippingMethod'
                ]);

                // ReviewAndPayment API
                Route::post('/reviewandpayment', 'Checkout\CheckoutController@reviewAndPayment')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Checkout\Payment'
                ]);

                // ApplyCoupon API
                Route::post('/applycoupon','Checkout\CheckoutController@applyCoupon');

                // RemoveCoupon API
                Route::post('/removecoupon','Checkout\CheckoutController@removeCoupon');

                // Razorpay Order API
                Route::post('/razorpayorder', 'Checkout\CheckoutController@razorpayOrder');

                // PlaceOrder API
                Route::post('/placeorder', 'Checkout\CheckoutController@placeOrder')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Checkout\PlaceOrder'
                ]);

                // ChangeOrderStatus API
                Route::post('/changeorderstatus', 'Checkout\OrderController@index');

                // WishlistFromCart API
                Route::post('/wishlistfromcart', 'Checkout\WishlistController@index')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Checkout\Wishlist'
                ]);
            });

            // Sales API Routes
            Route::prefix('sales')->group(function () {
                // AllToCart/MoveToCart API
                Route::post('/alltocart', 'Checkout\CartController@moveToCart');

                //guest view
                Route::post('/guestview', 'Checkout\OrderController@guestView');

                //sharewishlist
                Route::post('/sharewishlist', 'Checkout\WishlistController@share')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\WishlistShare'
                ]);
            });

            // Extra API Routes
            Route::prefix('extra')->group(function () {
                // CmsData API
                Route::get('/cmsdata', 'Extra\MobikulController@index');

                // RegisterDevice API
                Route::post('/registerdevice', 'Extra\MobikulController@registerDevice');

                // Logout API
                Route::post('/logout', 'Extra\MobikulController@logout');

                // CustomCollection API
                Route::post('/customcollection', 'Extra\MobikulController@collection');

                // GetNotification API
                Route::get('/notificationlist', 'Extra\MobikulController@notificationList');

                // SearchSuggestion API
                Route::get('/searchsuggestion', 'Extra\MobikulController@search')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Extra\SearchList'
                ]);

                // SearchTermList API
                Route::get('/searchtermlist', 'Extra\MobikulController@searchTerm')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Extra\SearchTermList'
                ]);
            });

            // Contact API Routes
            Route::prefix('contact')->group(function () {
                // Post API
                Route::post('/post' , 'Contact\PostController@index')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Contact\Post'
                ]);
            });

            // Download API Routes
            Route::prefix('download')->group(function () {
                // DownloadLinkSample API
                Route::post('/downloadlinksample', 'Catalog\ProductPageController@downloadLinkSample')->defaults('_config', [
                    'resource' => 'Webkul\MobikulApiTransformer\Http\Resources\Customer\DownloadLinkSample'
                ]);
            });

            // Index API Routes
            Route::prefix('index')->group(function () {
                // UploadProfilePic API
                Route::post('/uploadprofilepic', 'Customer\CustomerController@uploadProfilePic');

                //UploadBannerPic
                Route::post('/uploadbannerpic', 'Customer\CustomerController@uploadBannerPic');
            });

            // ProductAlert API Routes
            Route::prefix('productalert')->group(function () {
                //PriceDropAlert API
                Route::post('/price' , 'ProductAlert\PriceController@index');
            });

            // Profile Update Route
            Route::prefix('profile')->group(function () {
                // Edit API
                Route::post('/edit', 'Customer\CustomerController@update')->defaults('_config', [
                    'redirect' => 'customer.profile.index'
                ])->name('mobikul-api.customer.profile.edit');
            });

        });
    });
});

