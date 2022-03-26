<?php

Route::group([
        'prefix'        => 'admin/orderbulkupdate',
        'middleware'    => ['web', 'admin']
    ], function () {

        Route::get('', 'Mesk\OrderBulkUpdate\Http\Controllers\Admin\OrderBulkUpdateController@index')->defaults('_config', [
            'view' => 'orderbulkupdate::admin.index',
        ])->name('admin.orderbulkupdate.index');

        Route::post('download-samples', 'Mesk\OrderBulkUpdate\Http\Controllers\Admin\OrderBulkUpdateController@downloadFile')->defaults('_config', [
            'view' => 'orderbulkupdate::admin.index',
        ])->name('download-order-update-sample-files');

        
        Route::post('/importnew', 'Mesk\OrderBulkUpdate\Http\Controllers\Admin\OrderBulkUpdateController@importOrders')->defaults('_config',[
            'view' => 'orderbulkupdate::admin.importResult' 
        ])->name('import-update-order-form-submit');
        


});