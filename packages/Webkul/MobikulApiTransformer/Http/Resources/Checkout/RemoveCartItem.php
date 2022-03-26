<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Checkout;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Facades\Cart;
use Webkul\Tax\Helpers\Tax;

class RemoveCartItem extends JsonResource
{
    /**
     * Contains current currency
     *
     * @var string
     */
    protected $currencyCode;

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
    protected $shippingTotal = 0;

    /**
     * Contains order's base shipping total.
     *
     * @var float
     */
    protected $baseShippingTotal = 0;

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
     * Contains cart's total.
     *
     * @var array
     */
    protected $cartTotal = [];
    
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
        $this->currencyCode = request()->input('currency');

        Cart::removeItem($this['itemId']);

        $cart = Cart::getCart();
        
        if ( $cart ) {
            foreach ($cart->items as $item) {
                $this->discountAmount       += $item->discount_amount;
                $this->baseDiscountAmount   += $item->base_discount_amount;
                $this->subTotal     = (float) $this->subTotal + $item->total;
                $this->baseSubTotal = (float) $this->baseSubTotal + $item->base_total;
            }
    
            $this->taxTotal = Tax::getTaxTotal($cart, false);
            $this->baseTaxTotal = Tax::getTaxTotal($cart, true);
    
            $this->grandTotal = $this->subTotal + $this->taxTotal - $this->discountAmount;
            $this->baseGrandTotal = $this->baseSubTotal + $this->baseTaxTotal - $this->baseDiscountAmount;
    
            if ( $shipping = $cart->selected_shipping_rate ) {
                $this->shippingTotal        = $shipping->price;
                $this->baseShippingTotal    = $shipping->base_price;
                
                $this->grandTotal       = (float) $this->grandTotal + $shipping->price - $shipping->discount_amount;
                $this->baseGrandTotal   = (float) $this->baseGrandTotal + $shipping->base_price - $shipping->base_discount_amount;
    
                $this->discountAmount       += $shipping->discount_amount;
                $this->baseDiscountAmount   += $shipping->base_discount_amount;
            }
        }
        
        $this->cartTotal = [
            [
                'title'             => 'Subtotal',
                'value'             => (float) core()->convertPrice($this->baseSubTotal, $this->currencyCode),
                'formattedValue'    => (string) core()->currency($this->baseSubTotal, $this->currencyCode),
                'unformattedValue'  => (float) $this->baseSubTotal
            ],  [
                'title'             => 'Shipping & Handling',
                'value'             => (float) core()->convertPrice($this->baseShippingTotal, $this->currencyCode),
                'formattedValue'    => (string) core()->currency($this->baseShippingTotal, $this->currencyCode),
                'unformattedValue'  => (float) $this->baseShippingTotal
            ],  [
                'title'             => 'Tax',
                'value'             => (float) core()->convertPrice($this->baseTaxTotal, $this->currencyCode),
                'formattedValue'    => (string) core()->currency($this->baseTaxTotal, $this->currencyCode),
                'unformattedValue'  => (float) $this->baseTaxTotal
            ],  [
                'title'             => 'Discount',
                'value'             => (float) core()->convertPrice($this->baseDiscountAmount, $this->currencyCode),
                'formattedValue'    => (string) core()->currency($this->baseDiscountAmount, $this->currencyCode),
                'unformattedValue'  => (float) $this->baseDiscountAmount
            ],  [
                'title'             => 'Grand Total',
                'value'             => (float) core()->convertPrice($this->baseGrandTotal, $this->currencyCode),
                'formattedValue'    => (string) core()->currency($this->baseGrandTotal, $this->currencyCode),
                'unformattedValue'  => (float) $this->baseGrandTotal
            ],
        ];

        return [
            'success'       => true,
            'message'       => !$cart ? trans('mobikul-api::app.api.checkout.remove-cart-item.success-cart-empty') : trans('mobikul-api::app.api.checkout.remove-cart-item.success-remove-cart-item'),
            'cartCount'     => $cart ? $cart->items->count() : 0,
            'totalsData'    => $this->cartTotal
        ];
    }
}
