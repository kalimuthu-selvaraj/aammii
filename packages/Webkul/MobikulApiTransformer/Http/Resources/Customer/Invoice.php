<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class Invoice extends JsonResource
{
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
     * Contains order's invoice item list.
     *
     * @var array
     */
    protected $invoiceItems = [];

    /**
     * Contains order's invoice totals.
     *
     * @var array
     */
    protected $invoiceTotals = [];
    
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
        $invoice = $this['invoice'];
        
        foreach($invoice->items as $item) {
            $this->discountAmount       += $item->discount_amount;
            $this->baseDiscountAmount   += $item->base_discount_amount;
            $this->taxTotal             += $item->tax_amount;
            $this->baseTaxTotal         += $item->base_tax_amount;
            $this->shippingAmount       += $item->shipping_amount;
            $this->baseShippingAmount   += $item->base_shipping_amount;
            $this->subTotal     = (float) $this->subTotal + $item->total;
            $this->baseSubTotal = (float) $this->baseSubTotal + $item->base_total;

            $options = [];
            if ( isset($item->additional['attributes']) ) {
                $options = $item->additional['attributes'];
            }

            $productBaseImage = productimage()->getProductBaseImage($item->product);

            $this->invoiceItems[] = [
                'id'                => $item->id,
                'name'              => $item->name,
                'productId'         => $item->product_id,
                'option'            => $options,
                'sku'               => $item->sku,
                'qty'               => $item->qty,
                'image'             => $productBaseImage['medium_image_url'],
                'taxAmount'         => $item->tax_amount,
                'discountAmount'    => $item->discount_amount,
                'price'             => core()->currency($item->price, $item->order_currency_code),
                'subTotal'          => core()->currency($item->total, $item->order_currency_code),
                'rowTotal'          => '', //pending about rowTotal amount
            ];
        }
        $this->taxTotal = 0;
        $this->baseTaxTotal = 0;
        //$this->grandTotal           = (float) ($this->subTotal + $this->taxTotal + $this->shippingAmount - $this->discountAmount);
        $this->grandTotal           = (float) ($this->subTotal+ $this->shippingAmount - $this->discountAmount);
        $this->baseGrandTotal       = (float) ($this->baseSubTotal + $this->baseShippingAmount - $this->baseDiscountAmount);
        
        $this->invoiceTotals = [
            [
                'title'             => 'Subtotal',
                'value'             => $this->subTotal,
                'formattedValue'    => core()->formatPrice($this->subTotal, $invoice->order_currency_code),
                'unformattedValue'  => $this->baseSubTotal
            ],  [
                'title'             => 'Shipping & Handling',
                'value'             => $this->shippingAmount,
                'formattedValue'    => core()->formatPrice($this->shippingAmount, $invoice->order_currency_code),
                'unformattedValue'  => $this->baseShippingAmount
            ],  [
                'title'             => 'Tax',
                'value'             => $this->taxTotal,
                'formattedValue'    => core()->formatPrice($this->taxTotal, $invoice->order_currency_code),
                'unformattedValue'  => $this->baseTaxTotal
            ],  [
                'title'             => 'Discount',
                'value'             => $this->discountAmount,
                'formattedValue'    => core()->formatPrice($this->discountAmount, $invoice->order_currency_code),
                'unformattedValue'  => $this->baseDiscountAmount
            ],  [
                'title'             => 'Grand Total',
                'value'             => $this->grandTotal,
                'formattedValue'    => core()->formatPrice($this->grandTotal, $invoice->order_currency_code),
                'unformattedValue'  => $this->baseGrandTotal
            ],
        ];

        return [
            'success'   => true,
            'message'   => '',
            'orderId'   => $invoice->order_id,
            'itemList'  => $this->invoiceItems,
            'total'     => $this->invoiceTotals
        ];
    }
}