<?php

Route::group([
    //  'prefix'     => 'razorpay',
       'middleware' => ['web', 'theme', 'locale', 'currency']
   ], function () {

       Route::get('razorpay-redirect','Mesk\razorpay\Http\Controllers\RazorpayController@redirect')->name('razorpay.process');
       Route::post('razorpaycheck','Mesk\razorpay\Http\Controllers\RazorpayController@verify')->name('razorpay.callback'); 
});