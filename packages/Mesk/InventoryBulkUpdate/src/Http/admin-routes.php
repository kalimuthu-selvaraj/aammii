<?php

Route::group([
        'prefix'        => 'admin/inventorybulkupdate',
        'middleware'    => ['web', 'admin']
    ], function () {

        Route::get('', 'Mesk\InventoryBulkUpdate\Http\Controllers\Admin\InventoryBulkUpdateController@index')->defaults('_config', [
            'view' => 'inventorybulkupdate::admin.index',
        ])->name('admin.inventorybulkupdate.index');

        Route::get('download-samples', 'Mesk\InventoryBulkUpdate\Http\Controllers\Admin\InventoryBulkUpdateController@downloadFile')->defaults('_config', [
            'view' => 'inventorybulkupdate::admin.index',
        ])->name('download-inventory-update-sample-files');

        
        Route::post('/importnew', 'Mesk\InventoryBulkUpdate\Http\Controllers\Admin\InventoryBulkUpdateController@importProductsInventory')->defaults('_config',[
            'view' => 'inventorybulkupdate::admin.importResult' 
        ])->name('import-update-inventory-form-submit');
        


});