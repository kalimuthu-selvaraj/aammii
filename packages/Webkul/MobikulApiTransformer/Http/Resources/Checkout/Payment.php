<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Checkout;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Facades\Cart;
use Webkul\Tax\Helpers\Tax;
use Webkul\Payment\Facades\Payment as DefaultPayment;

class Payment extends JsonResource
{
    /**
     * Contains current currency
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * Contains order's billing address.
     *
     * @var string
     */
    protected $billingAddress = '';

    /**
     * Contains order's shipping address.
     *
     * @var string
     */
    protected $shippingAddress = '';

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
     * Contains cart's total array.
     *
     * @var array
     */
    protected $cartTotal = [];

    /**
     * Contains payment methods.
     *
     * @var array
     */
    protected $paymentMethods = [];

    /**
     * Contains razorpay details for SDK.
     *
     * @var array
     */
    protected $razorpayDetails = [];

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

        if ( core()->getConfigData('mobikul.mobikul.razorpay_mobile.status') ) {

            $billingAddress = $cart->billing_address;

            $shipping_rate = $cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0;
            $discount_amount = $cart->discount_amount;

            $total_amount =  ($cart->sub_total + $cart->tax_total + $shipping_rate) - $discount_amount;

            $this->razorpayDetails = [
                'merchant_id'     => core()->getConfigData('mobikul.mobikul.razorpay_mobile.merchant_id'),
                'merchant_secret' => core()->getConfigData('mobikul.mobikul.razorpay_mobile.merchant_secret'),
                'status'          => core()->getConfigData('mobikul.mobikul.razorpay_mobile.status'),
                "amount"          => $total_amount* 100, //razorpay accept payment in INR*100
                "name"            => $billingAddress->name,
                'receipt'         => $cart->id,
                "description"     => "RazorPay payment collection for the order - " . $cart->id,
                "image"           => core()->getConfigData('general.design.admin_logo.logo_image') ?\Illuminate\Support\Facades\Storage::url(core()->getConfigData('general.design.admin_logo.logo_image')) : '',
                "prefill"         => [
                    "name"    => $billingAddress->name,
                    "email"   => $billingAddress->email,
                    "contact" => $billingAddress->phone,
                ],
                "notes"           => [
                    "address"           => $billingAddress->address,
                    "merchant_order_id" => $cart->id,
                ],
                "theme"             => [

                ],
            ];
        }

        $shippingMethod = $this['shipping_method'];

        if (Cart::hasError() || !$shippingMethod || !Cart::saveShippingMethod($shippingMethod)) {
            return [
                'status'    => false,
                'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-save-shipping'),
                'redirect'  => true
            ];
        }

        Cart::collectTotals();

        $cart = Cart::getCart();

        if ( $cart->billing_address != null) {
            $this->billingAddress = "{$cart->billing_address->first_name} {$cart->billing_address->last_name}
                {$cart->billing_address->address1} {$cart->billing_address->address2},
                {$cart->billing_address->city}, {$cart->billing_address->state}, {$cart->billing_address->pincode}
                {$cart->billing_address->country}
                T: {$cart->billing_address->phone}";
        }

        if ( $cart->shipping_address != null) {
            $this->shippingAddress = "{$cart->shipping_address->first_name} {$cart->shipping_address->last_name}
                {$cart->shipping_address->address1} {$cart->shipping_address->address2},
                {$cart->shipping_address->city}, {$cart->shipping_address->state}, {$cart->shipping_address->pincode}
                {$cart->shipping_address->country}
                T: {$cart->shipping_address->phone}";
        }

        $selectedShipping = $cart->shipping_rates->where('method', $shippingMethod)->first();

        $defaultPaymentMethods = DefaultPayment::getSupportedPaymentMethods();

        foreach ($defaultPaymentMethods['paymentMethods'] as $payment) {

            $this->paymentMethods[] = [
                'code'              => $payment['method'],
                'title'             => $payment['method_title'],
                'extraInformation'  => $payment['description'],
                'sort'              => $payment['sort']
            ];

        }

        if ( core()->getConfigData('mobikul.mobikul.razorpay_mobile.status') ) {
            $this->paymentMethods[] = [
                'code'              => 'razorpay_mobile',
                'title'             => core()->getConfigData('mobikul.mobikul.razorpay_mobile.title'),
                'extraInformation'  => core()->getConfigData('mobikul.mobikul.razorpay_mobile.description'),
                'sort'              => 4
            ];
        }

        foreach ($cart->items as $item) {
            $this->discountAmount       += $item->discount_amount;
            $this->baseDiscountAmount   += $item->base_discount_amount;
            $this->subTotal = (float) $this->subTotal + $item->total;
            $this->baseSubTotal = (float) $this->baseSubTotal + $item->base_total;

            $product = $item->product;
            $productBaseImage = productimage()->getProductBaseImage($product);
            $productPrice = mobikulApi()->getProductPrice($product);

            $children = [];
            if ( isset($item->children) && $item->children ) {
                foreach ($item->children as $child) {
                    $childImageGallery =  productimage()->getProductBaseImage($child->product);

                    $child['quantity'] = $child['quantity'] ? $child['quantity'] * $item['quantity'] : $child['quantity'];

                    $children[] = [
                        'productName'       => $child->name,
                        'qty'               => $child->quantity,
                        'price'             => core()->convertPrice($child->price, $this->currencyCode),
                        'subTotal'          => core()->convertPrice($child->total, $this->currencyCode),
                        'formattedPrice'    => core()->formatPrice(core()->convertPrice($child->price, $this->currencyCode), $this->currencyCode),
                        'formattedSubTotal' => core()->formatPrice(core()->convertPrice($child->total, $this->currencyCode), $this->currencyCode),
                        'thumbNail'         => $childImageGallery['small_image_url'],
                        'dominantColor'     => mobikulApi()->getImageDominantColor($childImageGallery['small_image_url']),
                        'unformattedPrice'  => $child->price
                    ];
                }
            }

            $cart_item_data = [
                'productName'       => $item->name,
                'qty'               => $item->quantity,

                'price'             => (float) $productPrice['price'],
                'formattedPrice'    => (String) core()->formatPrice($productPrice['price'], core()->getBaseCurrencyCode()),

                'finalPrice'        => (float) core()->convertPrice($productPrice['price'], $this->currencyCode),
                'formattedFinalPrice'   => (string) core()->currency($productPrice['price'], $this->currencyCode),

                'specialPrice'              => $product->getTypeInstance()->haveSpecialPrice() ? (float) $product->getTypeInstance()->getSpecialPrice() : 0,
                'formatedSpecialPrice'      => $product->getTypeInstance()->haveSpecialPrice() ? core()->formatPrice($product->getTypeInstance()->getSpecialPrice(), core()->getBaseCurrencyCode()) : '',

                'convertedSpecialPrice'     => $product->getTypeInstance()->haveSpecialPrice() ? core()->convertPrice($product->getTypeInstance()->getSpecialPrice(),$this->currencyCode) : 0,
                'formatedConvertedSpecialPrice'      => $product->getTypeInstance()->haveSpecialPrice() ? core()->formatPrice(core()->convertPrice($product->getTypeInstance()->getSpecialPrice(), $this->currencyCode), $this->currencyCode) : '',

                'baseSubTotal'      => (float) $item->base_total,
                'formattedBaseSubTotal' => (string) core()->formatPrice($item->base_total, core()->getBaseCurrencyCode()),

                'subTotal'          => (float) core()->convertPrice($item->base_total, $this->currencyCode),
                'formattedSubTotal' => (string) core()->formatPrice(core()->convertPrice($item->base_total, $this->currencyCode), $this->currencyCode),

                'thumbNail'         => $productBaseImage['small_image_url'],
                'dominantColor'     => mobikulApi()->getImageDominantColor($productBaseImage['small_image_url']),
                'unformattedPrice'  => $item->price,
            ];

            if (! empty($children) ) {
                $cart_item_data['children'] = $children;
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
            'success'           => true,
            'message'           => trans('mobikul-api::app.api.checkout.review-and-payment.success-payment'),
            'billingAddress'    => $this->billingAddress,
            'shippingAddress'   => $this->shippingAddress,
            'shippingMethod'    => $selectedShipping->carrier_title,
            'cartCount'         => count($cart->items),
            'cartTotal'         => (string) core()->formatPrice(core()->convertPrice($cart->base_grand_total, $this->currencyCode), $this->currencyCode),
            'unformattedCartTotal'  => (float) core()->convertPrice($cart->base_grand_total, $this->currencyCode),
            'paymentMethods'    => $this->paymentMethods,
            'currencyCode'      => $this->currencyCode,
            'couponCode'        => (string) $cart->coupon_code ?  $cart->coupon_code : '',
            'orderReviewData'   => [
                'items'             => $this->cartItems,
                'cartTotal'         => (string) core()->formatPrice(core()->convertPrice($cart->base_grand_total, $this->currencyCode), $this->currencyCode),
                'totalsData'        => $this->cartTotal
            ],
            'razorpay_details' => $this->razorpayDetails
        ];
    }
}
