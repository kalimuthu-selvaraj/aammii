<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Checkout;

use Illuminate\Http\Resources\Json\JsonResource;

class ChangeOrderStatus extends JsonResource
{
    /**
     * Contains allowed order's status
     *
     * @var array
     */
    protected $allow_status = [
        'pending',
        'pending_payment',
        'processing',
        'completed',
        'canceled',
        'closed',
        'fraud',
    ];

    /**
     * Contains invoice detail.
     *
     * @var array
     */
    protected $invoice = [
        'order_id'  => null,
        'invoice'   => [
            'items'     => []
        ]
    ];

    /**
     * Contains shipment detail.
     *
     * @var array
     */
    protected $shipment = [
        'order_id'  => null,
        'shipment'  => [
            'carrier_title' => 'BlueDart Express',
            'track_number'  => 0,
            'source'        => null,
            'items'         => [],
        ]
    ];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->orderRepository = app('Webkul\Sales\Repositories\OrderRepository');
        
        $this->invoiceRepository  = app('Webkul\Sales\Repositories\InvoiceRepository');
        
        $this->shipmentRepository  = app('Webkul\Sales\Repositories\ShipmentRepository');

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
        $order = $this['order'];
        $order_status = $this->allow_status[$request->status];

        switch ($order_status) {
            case 'processing':
                // create Order Invoice
                if ( $order->canInvoice() ) {
                    return $this->createInvoice($order, $request);
                } else {
                    return [
                        'success' => false,
                        'message' => trans('mobikul-api::app.api.checkout.change-order-status.error-already-invoiced', [
                            'increment_id'  => $request->incrementId,
                            'order_status'  => $order_status,
                        ]),
                    ];
                }
                break;
            
            case 'completed':
                    $result = [];
                    // create Order Invoice
                    if ( $order->canInvoice() ) {
                        $result = $this->createInvoice($order, $request);
                    }
                    
                    // create Order Invoice
                    if ( $order->canShip() ) {
                        $result = $this->createShipment($order, $request);
                    }

                    return $result;
                break;
        
            case 'canceled':
                    $result = $this->orderRepository->cancel($order->id);
                    if ( $result ) {
                        return [
                            'success'   => true,
                            'message'   => trans('mobikul-api::app.api.checkout.change-order-status.success-status-changed', [
                                'order_status'  => $order_status,
                                'increment_id'  => $request->incrementId,
                            ]),
                        ];
                    } else {
                        return [
                            'success'   => false,
                            'message'   => trans('mobikul-api::app.api.checkout.change-order-status.error-order-status', [
                                'order_status'  => $order_status,
                            ]),
                        ];
                    }
                break;
    
            case 'closed':
                if ( $this->orderRepository->isInClosedState($order) ) {
                    $order->status = 'closed';
                    $order->save();

                    return [
                        'success'   => true,
                        'message'   => trans('mobikul-api::app.api.checkout.change-order-status.success-status-changed', [
                            'order_status'  => $order_status,
                            'increment_id'  => $request->incrementId,
                        ]),
                    ];
                } else {
                    return [
                        'success'   => false,
                        'message'   => trans('mobikul-api::app.api.checkout.change-order-status.error-order-status', [
                            'order_status'  => $order_status,
                        ]),
                    ];
                }
                
                break;
            
            default:
                if ( isset($order_status) ) {
                    $order->status = $order_status;
                    $order->save();
                    
                    return [
                        'success'   => true,
                        'message'   => trans('mobikul-api::app.api.checkout.change-order-status.success-status-changed', [
                            'order_status'  => $order_status,
                            'increment_id'  => $request->incrementId,
                        ]),
                    ];
                } else {
                    return [
                        'success'   => false,
                        'message'   => trans('mobikul-api::app.api.checkout.change-order-status.error-invalid-status'),
                    ];
                }

                break;
        }
    }

    /**
     * To create the order's invoice.
     *
     * @param  \Webkul\Sales\Models\Order
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function createInvoice($order, $request)
    {
        $this->invoice['order_id'] = $order->id;
        
        foreach ($order->items as $item) {
            if ( $item->qty_to_invoice > 0 ) {
                $this->invoice['invoice']['items'][$item->id] = $item->qty_to_invoice;
            }
        }

        $invoice = $this->invoiceRepository->create($this->invoice);
        if ( $invoice ) {
            return [
                'success'   => true,
                'message'   => trans('mobikul-api::app.api.checkout.change-order-status.success-status-changed', [
                    'order_status'  => $this->allow_status[$request->status],
                    'increment_id'  => $order->id,
                ]),
            ];
        } else {
            return [
                'success'   => false,
                'message'   => $invoice,
            ];
        }
    }

    /**
     * To create the order's shipment.
     *
     * @param  \Webkul\Sales\Models\Order
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function createShipment($order, $request)
    {
        $inventory_source   = $order->channel->inventory_sources->where('code', 'default')->first();

        $this->shipment['order_id'] = $order->id;
        $this->shipment['shipment']['track_number'] = rand(1000, 99999);
        $this->shipment['shipment']['source'] = isset($inventory_source->id) ? $inventory_source->id : null;
        
        foreach ($order->items as $item) {
            if ( $item->qty_to_ship > 0 && $item->product ) {
                $this->shipment['shipment']['items'][$item->id][$inventory_source->id] = $item->qty_invoiced;
            }
        }

        $shipment = $this->shipmentRepository->create($this->shipment);
        if ( $shipment ) {
            return [
                'success'   => true,
                'message'   => trans('mobikul-api::app.api.checkout.change-order-status.success-status-changed', [
                    'order_status'  => $this->allow_status[$request->status],
                    'increment_id'  => $request->incrementId,
                ]),
            ];
        } else {
            return [
                'success'   => false,
                'message'   => $shipment,
            ];
        }
    }
}
