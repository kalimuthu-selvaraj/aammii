<?php

Route::group([
        'prefix'        => 'admin/orderreport',
        'middleware'    => ['web', 'admin']
    ], function () {

        Route::get('', 'Mesk\OrderReport\Http\Controllers\Admin\OrderReportController@index')->defaults('_config', [
            'view' => 'orderreport::admin.index',
        ])->name('admin.orderreport.index');

        Route::post('download-samples', 'Mesk\OrderReport\Http\Controllers\Admin\OrderReportController@downloadFile')->defaults('_config', [
            'view' => 'orderreport::admin.index',
        ])->name('download-order-update-sample-files');
});