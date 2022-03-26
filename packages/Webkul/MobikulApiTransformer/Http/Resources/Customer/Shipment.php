<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class Shipment extends JsonResource
{
    /**
     * Contains shipment items.
     *
     * @var array
     */
    protected $shipmentItems = [];

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $shipment = $this['shipment'];
        foreach ($shipment->items as $item) {
            $options = [];
            if ( isset($item->additional['attributes']) ) {
                $options = $item->additional['attributes'];
            }

            $this->shipmentItems[] = [
                'id'        => $item->id,
                'name'      => $item->name,
                'productId' => $item->product_id,
                'sku'       => $item->sku,
                'option'    => $options,
                'qty'       => $item->qty,
            ];
        }

        return [
            'success'       => true,
            'message'       => "",
            'orderId'       => $shipment->order_id,
            'itemList'      => $this->shipmentItems,
            'trackingData'  => [
                'id'            => $shipment->id,
                'number'        => $shipment->track_number,
                'title'         => $shipment->carrier_title,
                'carrier'       => $shipment->carrier_title
            ],
            'eTag'          => "00b9d5113997543dc143c02a4c240efd", //pending
        ];
    }
}