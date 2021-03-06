<?php

namespace Webkul\Admin\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Webkul\Product\Models\ProductAttributeValue;
use Webkul\Product\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderDataGridExport implements FromView, ShouldAutoSize
{
    /**
     * DataGrid instance
     *
     * @var mixed
     */
    protected $gridData = [];

    /**
     * Create a new instance.
     *
     * @param mixed DataGrid
     * @return void
     */
    public function __construct($gridData)
    {
        $this->gridData = $gridData;
    }

     /**
     * function to create a blade view for export.
     *
     */
    public function view(): View
    {
        $columns = [];
        foreach($this->gridData as $key => $gridData) {
            $columns = array_keys((array) $gridData);
			
            break;
        }
		
 		$exportData=[];
		$OrderId=0;
		foreach($this->gridData as $key => $gridData) {
			if($OrderId != $gridData->ID){
				$exportData[$key]["ID"]= $gridData->ID;
				$exportData[$key]["DATE"]= $gridData->DATE;
				$exportData[$key]["CUSTOMER_NAME"]= $gridData->CUSTOMER_NAME;
				$exportData[$key]["PHONE_NO"]= $gridData->PHONE_NO;
 				$exportData[$key]["EMAIL"]= $gridData->EMAIL;
				$exportData[$key]["ADDRESS"]= $gridData->ADDRESS;
				$exportData[$key]["PINCODE"]= $gridData->PINCODE;
				$exportData[$key]["CITY"]= $gridData->CITY;
				$exportData[$key]["STATE"]= $gridData->STATE;
				$exportData[$key]["COUNTRY"]= $gridData->COUNTRY;
				$exportData[$key]["PAYMENT"]= $gridData->PAYMENT;
				$exportData[$key]["AMOUNT"]= $gridData->AMOUNT;
				$exportData[$key]["SHIPPING_CHARGES"]= $gridData->SHIPPING_CHARGES;
				if( $gridData->SHIPPING_METHOD == 'tablerate_courier-charge')
					$exportData[$key]["SHIPPING_METHOD"]= 'Courier';
				else
					$exportData[$key]["SHIPPING_METHOD"]= 'Transport';
				$exportData[$key]["CHANNEL_NAME"]= $gridData->CHANNEL_NAME;
				//$exportData[$key]["BILLING_TO"]= $gridData->BILLING_TO;
				//$exportData[$key]["SHIPPING_TO"]= $gridData->SHIPPING_TO;
			}else{
				$exportData[$key]["ID"]= " ";
				$exportData[$key]["DATE"]= " ";
				$exportData[$key]["CUSTOMER_NAME"]= " ";
				$exportData[$key]["PHONE_NO"]= " ";
				$exportData[$key]["EMAIL"]= " ";
				$exportData[$key]["ADDRESS"]= " ";
				$exportData[$key]["PINCODE"]= " ";
				$exportData[$key]["CITY"]= " ";
				$exportData[$key]["STATE"]= " ";
				$exportData[$key]["COUNTRY"]= " ";
				$exportData[$key]["PAYMENT"]= " ";
				$exportData[$key]["AMOUNT"]= " ";
				$exportData[$key]["SHIPPING_METHOD"]= " ";
				$exportData[$key]["SHIPPING_CHARGES"]= " ";
 				$exportData[$key]["CHANNEL_NAME"]= " ";
				//$exportData[$key]["BILLING_TO"]= " ";
				//$exportData[$key]["SHIPPING_TO"]= " ";
			}
			$Category=DB::table('product_categories')->leftJoin('category_translations', function ($leftJoin) {
                $leftJoin->on('category_translations.category_id', '=', 'product_categories.category_id');
            })->where('product_categories.product_id', $gridData->PRODUCT_ID)->first();
			$Hsncode=ProductAttributeValue::where("product_id",$gridData->PRODUCT_ID)->where("attribute_id",32)->first();
			if(isset($Hsncode->text_value))
				$exportData[$key]["HSNCODE"]= $Hsncode->text_value;
			else
				$exportData[$key]["HSNCODE"]= "";
			if(isset($Category->name))
				$exportData[$key]["CATEGORY"]= $Category->name;
			else
				$exportData[$key]["CATEGORY"]= "";
			$Barcode=ProductAttributeValue::where("product_id",$gridData->PRODUCT_ID)->where("attribute_id",31)->first();
			
 			if(isset($Barcode->text_value))
				$exportData[$key]["BARCODE"]= $Barcode->text_value;
			else
				$exportData[$key]["BARCODE"]= "";
			$exportData[$key]["PRODUCT_NAME"]= $gridData->PRODUCT_NAME;
            //$exportData[$key]["HSNCODE"]= $gridData->HSNCODE;
            $exportData[$key]["QTY"]= $gridData->QTY;
            $exportData[$key]["MRP"]= $gridData->MRP;
            $exportData[$key]["ORDER_AMOUNT"]= $gridData->ORDER_AMOUNT;
            $exportData[$key]["PRICE_WITH_OUT_TAX"]= $gridData->PRICE_WITH_OUT_TAX;
            $exportData[$key]["TAX_AMOUNT"]= $gridData->TAX_AMOUNT;
            $exportData[$key]["DISCOUNT"]= $gridData->DISCOUNT;
            $exportData[$key]["TOTAL"]= $gridData->TOTAL;
			if($OrderId != $gridData->ID){
				$exportData[$key]["STATUS"]= $gridData->STATUS;
				$exportData[$key]["CARRIER_TITLE"]=  $gridData->CARRIER_TITLE;
				$exportData[$key]["TRACKING_NUMBER"]=  $gridData->TRACKING_NUMBER;
			}
			else{
				$exportData[$key]["STATUS"]= " ";
				$exportData[$key]["CARRIER_TITLE"]= " ";
				$exportData[$key]["TRACKING_NUMBER"]= " ";
			}

			if($OrderId != $gridData->ID)
				$OrderId=$gridData->ID;
        }
        return view('admin::export.ordertemp', [
            'columns' => $columns,
            'records' => $exportData,
        ]);
    }
}