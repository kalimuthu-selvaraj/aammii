<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Checkout;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Facades\Cart;
use Webkul\Tax\Helpers\Tax;

class CartDetail extends JsonResource
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
     * Contains guest's checkout value.
     *
     * @var boolean
     */
    protected $isAllowedGuestCheckout = true;

    /**
     * Contains status for cart error.
     *
     * @var boolean
     */
    protected $isCheckoutAllowed = true;

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
     * Contains cart's item array.
     *
     * @var array
     */
    protected $cartItems = [];

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
        $this->productRepository = app('Webkul\Product\Repositories\ProductRepository');

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

        Cart::collectTotals();

        $cart = Cart::getCart();
        
        if (! isset($this['customer']) && ! core()->getConfigData('catalog.products.guest-checkout.allow-guest-checkout')) {
            $this->isAllowedGuestCheckout = false;
        }

        if ( Cart::hasError() ) {
            $this->isCheckoutAllowed = false;
        }
        
        foreach ($cart->items as $item) {
            $this->discountAmount       += $item->discount_amount;
            $this->baseDiscountAmount   += $item->base_discount_amount;
            $this->subTotal     = (float) $this->subTotal + $item->total;
            $this->baseSubTotal = (float) $this->baseSubTotal + $item->base_total;

            $product = $item->product;
            $productBaseImage = productimage()->getProductBaseImage($product);

            $productPrice = mobikulApi()->getProductPrice($product);

            $totalQty = $product->getTypeInstance()->totalQuantity();

            $cart_item_data = [
                'image'                 => $productBaseImage['medium_image_url'],
                'dominantColor'         => mobikulApi()->getImageDominantColor($productBaseImage['medium_image_url']),
                'thresholdQty'          => "0", //need discussion
                'name'                  => $item->name,
                'canMoveToWishlist'     => $product->getTypeInstance()->canBeMovedFromWishlistToCart($item),
                'id'                    => $item->id,
                'sku'                   => $item->sku,
                'qty'                   => $item->quantity,
                'remainingQty'          => $totalQty,
                'typeId'                => $product->type,
                
                'price'                 => (float) $productPrice['price'],
                'formattedPrice'        => (String) core()->formatPrice($productPrice['price'], core()->getBaseCurrencyCode()),

                'finalPrice'            => (float) core()->convertPrice($productPrice['price'], $this->currencyCode),
                'formattedFinalPrice'   => (string) core()->currency($productPrice['price'], $this->currencyCode),

                'specialPrice'              => $product->getTypeInstance()->haveSpecialPrice() ? (float) $product->getTypeInstance()->getSpecialPrice() : 0,
                'formatedSpecialPrice'      => $product->getTypeInstance()->haveSpecialPrice() ? core()->formatPrice($product->getTypeInstance()->getSpecialPrice(), core()->getBaseCurrencyCode()) : '',
                
                'convertedSpecialPrice'     => $product->getTypeInstance()->haveSpecialPrice() ? core()->convertPrice($product->getTypeInstance()->getSpecialPrice(),$this->currencyCode) : 0,
                'formatedConvertedSpecialPrice'      => $product->getTypeInstance()->haveSpecialPrice() ? core()->formatPrice(core()->convertPrice($product->getTypeInstance()->getSpecialPrice(), $this->currencyCode), $this->currencyCode) : '',

                'baseSubTotal'          => (float) $item->base_total,
                'formattedBaseSubTotal' => (string) core()->formatPrice($item->base_total, core()->getBaseCurrencyCode()),

                'subTotal'              => (float) core()->convertPrice($item->base_total, $this->currencyCode),
                'formattedSubTotal'     => (string) core()->formatPrice(core()->convertPrice($item->base_total, $this->currencyCode), $this->currencyCode),

                'isInRange'             => false, //need discussion
                'groupedProductId'      => 0, //need discussion
                'productId'             => (int) $item->product_id,
            ];
            
            if ( isset($item->additional['attributes'])) {
                $cart_item_data['additional'] = $item->additional;
                
                if ( isset($item->additional['product_id']) ) {
                    $cart_item_data['additional']['product_id'] = (int) $item->additional['product_id'];
                }
            }

            $this->cartItems[] = $cart_item_data;
        }

        //$this->taxTotal = Tax::getTaxTotal($cart, false);
        //$this->baseTaxTotal = Tax::getTaxTotal($cart, true);
		
		$this->taxTotal = 0;
        $this->baseTaxTotal = 0;

        //$this->grandTotal = $this->subTotal + $this->taxTotal - $this->discountAmount;
        $this->grandTotal = $this->subTotal - $this->discountAmount;
        //$this->baseGrandTotal = $this->baseSubTotal + $this->baseTaxTotal - $this->baseDiscountAmount;
        $this->baseGrandTotal = $this->baseSubTotal - $this->baseDiscountAmount;

        if ($shipping = $cart->selected_shipping_rate) {
            $this->shippingTotal        = $shipping->price;
            $this->baseShippingTotal    = $shipping->base_price;
            
            $this->grandTotal           = (float) $this->grandTotal + $shipping->price - $shipping->discount_amount;
            $this->baseGrandTotal       = (float) $this->baseGrandTotal + $shipping->base_price - $shipping->base_discount_amount;

            $this->discountAmount      += $shipping->discount_amount;
            $this->baseDiscountAmount  += $shipping->base_discount_amount;
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
            'success'                   => true,
            'message'                   => '',
            'isAllowedGuestCheckout'    => $this->isAllowedGuestCheckout,
            'isCheckoutAllowed'         => $this->isCheckoutAllowed,
            'minimumAmount'             => 0, //need discussion
            'minimumFormattedAmount'    => '', //need discussion
            'showThreshold'             => false,
            'canGuestCheckoutDownloadable' => false, //need discussion
            'allowMultipleShipping'     => false, //need discussion
            'items'                     => $this->cartItems,
            'totalCount'                => count($cart->items),
            'crossSellList'             => $product->cross_sells()->get(),
            'cartCount'                 => count($cart->items),
            'cartTotal'                 => (string) core()->formatPrice(core()->convertPrice($cart->base_grand_total, $this->currencyCode),$this->currencyCode),
            'unformattedCartTotal'      => (float) core()->convertPrice($cart->base_grand_total, $this->currencyCode),
            'totalsData'                => $this->cartTotal,
            'couponCode'                => (string) $cart->coupon_code ? $cart->coupon_code : '',
        ];
    }
}
