<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Tax\Helpers\Tax;

class OrderDetails extends JsonResource
{
    /**
     * Contains current channel
     *
     * @var string
     */
    protected $channel;

    /**
     * Contains current currency
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * Contains current locale
     *
     * @var string
     */
    protected $localeCode;

    /**
     * Contains order's invoices list.
     *
     * @var array
     */
    protected $invoiceList = [];

    /**
     * Contains order's shipment list.
     *
     * @var array
     */
    protected $shipmentList = [];

    /**
     * Contains order's product discount.
     *
     * @var float
     */
    protected $discountAmount = 0;

    /**
     * Contains order's product base discount.
     *
     * @var float
     */
    protected $baseDiscountAmount = 0;

    /**
     * Contains order's sub total.
     *
     * @var float
     */
    protected $subTotal = 0;

    /**
     * Contains order's base sub total.
     *
     * @var float
     */
    protected $baseSubTotal = 0;

    /**
     * Contains order's shipping total.
     *
     * @var float
     */
    protected $shippingAmount = 0;

    /**
     * Contains order's base shipping total.
     *
     * @var float
     */
    protected $baseShippingAmount = 0;

    /**
     * Contains order's tax total.
     *
     * @var float
     */
    protected $taxTotal = 0;

    /**
     * Contains order's base tax total.
     *
     * @var float
     */
    protected $baseTaxTotal = 0;

    /**
     * Contains order's grand total.
     *
     * @var float
     */
    protected $grandTotal = 0;

    /**
     * Contains order's base grand total.
     *
     * @var float
     */
    protected $baseGrandTotal = 0;

    /**
     * Contains order's shipping address.
     *
     * @var string
     */
    protected $shippingAddressString = '';

    /**
     * Contains order's billing address.
     *
     * @var string
     */
    protected $billingAddressString = '';

    /**
     * Contains reorder value.
     *
     * @var boolean
     */
    protected $canReorder = true;

    /**
     * Contains order product's ids.
     *
     * @var array
     */
    protected $productIds = [];

    /**
     * Contains order data.
     *
     * @var array
     */
    protected $orderData = [];

    /**
     * Contains order item list.
     *
     * @var array
     */
    protected $orderItems = [];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->productInventoryRepository = app('Webkul\Product\Repositories\ProductInventoryRepository');

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
        
        if ( $order->invoices->count() ) {
            foreach($order->invoices as $key => $invoice) {
                $this->invoiceList[$key] = $invoice->toArray();
                $this->invoiceList[$key]['invoiceItems'] = $invoice->items->toArray();
            }
        }
        
        if ( $order->shipments->count() ) {
            foreach($order->shipments as $key => $shipment) {
                $this->shipmentList[$key] = $shipment->toArray();
                $this->shipmentList[$key]['shipmentItems'] = $shipment->items->toArray();
            }
        }
        
        foreach ($order->items as $item) {
            $this->discountAmount       += $item->discount_amount;
            $this->baseDiscountAmount   += $item->base_discount_amount;
            $this->subTotal     = (float) $this->subTotal + $item->total;
            $this->baseSubTotal = (float) $this->baseSubTotal + $item->base_total;
            
            $options = [];
            if ( isset($item->additional['attributes']) ) {
                $options = $item->additional['attributes'];
            }
            $this->productIds[] = $item->product_id;

            $productBaseImage = productimage()->getProductBaseImage($item->product);

            //check qty of product
            $qtyArray = [
                'Ordered'   => $item->qty_ordered,
                'Invoiced'  => $item->qty_invoiced,
                'Shipped'   => $item->qty_shipped,
                'Canceled'  => $item->qty_canceled,
                'Refunded'  => $item->qty_refunded,
            ];

            $this->orderItems[] = [
                'name'          => $item->name,
                'productId'     => $item->id,
                'option'        => $options,
                'sku'           => $item->sku,
                'price'         => $item->price,
                'formattedPrice'=> (string) core()->formatPrice($item->price, $order->order_currency_code),
                'qty'           => $qtyArray,
                'subTotal'      => core()->formatPrice($item->total, $order->order_currency_code),
                'image'         => $productBaseImage['medium_image_url']
            ];
        }
        $this->orderData['itemList'] = $this->orderItems;

        $this->taxTotal             = $order->tax_amount;
        $this->baseTaxTotal         = $order->base_tax_amount;
        $this->grandTotal           = $this->subTotal + $this->taxTotal - $this->discountAmount;
        $this->baseGrandTotal       = $this->baseSubTotal + $this->baseTaxTotal - $this->baseDiscountAmount;
        
        $this->shippingAmount       = $order->shipping_amount;
        $this->baseShippingAmount   = $order->base_shipping_amount;
        $this->discountAmount       = $order->discount_amount;
        $this->baseDiscountAmount   = $order->base_discount_amount;
        
        $this->grandTotal       = (float) $this->grandTotal + $this->shippingAmount - $this->discountAmount;
        $this->baseGrandTotal   = (float) $this->baseGrandTotal + $this->baseShippingAmount - $this->baseDiscountAmount;
        
        $this->orderData['totals'] = [
            [
                'title'             => 'Subtotal',
                'value'             => $this->subTotal,
                'formattedValue'    => core()->formatPrice($this->subTotal, $order->order_currency_code),
                'unformattedValue'  => $this->baseSubTotal
            ],  [
                'title'             => 'Shipping & Handling',
                'value'             => $this->shippingAmount,
                'formattedValue'    => core()->formatPrice($this->shippingAmount, $order->order_currency_code),
                'unformattedValue'  => $this->baseShippingAmount
            ],  [
                'title'             => 'Tax',
                'value'             => $this->taxTotal,
                'formattedValue'    => core()->formatPrice($this->taxTotal, $order->order_currency_code),
                'unformattedValue'  => $this->baseTaxTotal
            ],  [
                'title'             => 'Discount',
                'value'             => $this->discountAmount,
                'formattedValue'    => core()->formatPrice($this->discountAmount, $order->order_currency_code),
                'unformattedValue'  => $this->baseDiscountAmount
            ],  [
                'title'             => 'Grand Total',
                'value'             => $this->grandTotal,
                'formattedValue'    => core()->formatPrice($this->grandTotal, $order->order_currency_code),
                'unformattedValue'  => $this->baseGrandTotal
            ],
        ];
        
        if ( $order->shipping_address ) {
            $this->shippingAddressString = "{$order->shipping_address->first_name} {$order->shipping_address->last_name}
            {$order->shipping_address->address1} {$order->shipping_address->address2} {$order->shipping_address->state}, {$order->shipping_address->city}, {$order->shipping_address->postcode} {$order->shipping_address->country} T: {$order->shipping_address->phone}";
        }
        
        if ( $order->billing_address ) {
            $this->billingAddressString = "{$order->billing_address->first_name} {$order->billing_address->last_name}
            {$order->billing_address->address1} {$order->billing_address->address2}{$order->billing_address->state}, {$order->billing_address->city}, {$order->billing_address->postcode}
            {$order->billing_address->country} T: {$order->billing_address->phone}";
        }

        $productInventory = $this->productInventoryRepository->findWhereIn('product_id', array_unique($this->productIds))->toArray();
        
        foreach ($productInventory as $inventory) {
            if ($inventory['qty'] == 0) {
                $this->canReorder = false;
                break;
            }
        }
        
        // PaymentMethod
        $paymentMethod = $order->with('payment')->where('cart_id', $order->cart_id)->first()->payment->method;
        
        return [
            'success'               => true,
            'message'               => "",
            'shippingMethod'        => $order->shipping_title,
            'hasShipments'          => $order->canShip() ? true : false,
            'hasInvoices'           => $order->canInvoice() ? true : false,
            'customerName'          => $order->customer_first_name . ' ' . $order->customer_last_name,
            'customerEmail'         => $order->customer_email,
            'state'                 => $order->status,
            'orderDate'             => $order->created_at,
            'incrementId'           => $order->id,
            'statusLabel'           => $order->status,
            'statusColorCode'       => "#d5d5d5", //pending
            'canReorder'            => $this->canReorder,
            'orderTotal'            => core()->formatPrice($this->grandTotal, $order->order_currency_code),
            'orderData'             => $this->orderData,
            'invoiceList'           => $this->invoiceList,
            'shipmentList'          => $this->shipmentList,
            'creditmemoList'        => [], //pending
            'shippingAddress'       => $this->shippingAddressString,
            'billing'               => $this->billingAddressString,
            'paymentMethod'         => $paymentMethod,
            'eTag'                  => '02002b84642b2671e91c9b9481b626d0', //pending
        ];
    }
}