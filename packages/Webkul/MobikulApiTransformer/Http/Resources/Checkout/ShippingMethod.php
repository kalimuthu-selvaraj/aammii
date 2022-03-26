<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Checkout;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Facades\Cart;
use Webkul\Shipping\Facades\Shipping;

class ShippingMethod extends JsonResource
{
    /**
     * Contains current currency
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * Contains customer's cart item count.
     *
     * @var int
     */
    protected $cartItemCount = 0;

    /**
     * Contains virtual item count.
     *
     * @var int
     */
    protected $virtualItemCount = 0;

    /**
     * Contains status for virtual cart.
     *
     * @var boolean
     */
    protected $isVirtualCart = false;

    /**
     * Contains shipping methods.
     *
     * @var array
     */
    protected $shippingMethods = [];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->cartAddressRepository = app('Webkul\Checkout\Repositories\CartAddressRepository');

        $this->customerAddressRepository = app('Webkul\Customer\Repositories\CustomerAddressRepository');
        
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
        
        $cart = Cart::getCart();
        
        if ( $cart ) {
            if ( isset($cart->billing_address->id) ) {
                $this->cartAddressRepository->delete($cart->billing_address->id);

                if ( isset($cart->shipping_address->id) ) {
                    $this->cartAddressRepository->delete($cart->shipping_address->id);
                }
            }
            
            foreach ($cart->items as $item) {
                if (! $item->product->getTypeInstance()->isStockable() ) {
                    $this->virtualItemCount += 1;
                }
            }

            $this->cartItemCount = $cart->items->count();
            
            if ( $this->virtualItemCount == $this->cartItemCount ) {
                $this->isVirtualCart = true;
            }
        }

        if ( getType($this['shippingData']) == 'string') {
            $addressData = json_decode($this['shippingData'], true);
        } else {
            $addressData = $this['shippingData'];
        }

        $data = [
            'billing'   => [
                'address1'          => '',
                'first_name'        => $cart->customer_first_name,
                'last_name'         => $cart->customer_last_name,
                'email'             => $cart->customer_email,
                'address_id'        => isset($addressData['addressId']) ? $addressData['addressId'] : 0,
            ],
            'shipping'  => [
                'address1'      => '',
                'first_name'    => $cart->customer_first_name,
                'last_name'     => $cart->customer_last_name,
                'email'         => $cart->customer_email,
                'address_id'    => isset($addressData['addressId']) ? $addressData['addressId'] : 0,
            ],
        ];
        
        if ( isset($addressData['addressId']) && $addressData['addressId']) {
            $address = $this->customerAddressRepository->findOneWhere([
                'id'            => $addressData['addressId'],
                'address_type'  => 'customer',
            ]);
            
            if ( $address ) {
                $data['billing']['address1'] = $address['address1'];
                $data['shipping']['address1'] = $address['address1'];
            } else {
                return [
                    'success'   => true,
                    'message'   => trans('mobikul-api::app.api.checkout.shipping-methods.no-shipping-address', ['address_id' => $addressData['addressId']]),
                    'redirect'  => true
                ];
            }
        } else {
            if ( isset($addressData['newAddress']['address1']) ) {
                $data['billing']    = $addressData['newAddress'];
                
                if ( isset($addressData['newAddress']['sameAsShipping']) && ($addressData['newAddress']['sameAsShipping'] == 0 || $addressData['newAddress']['sameAsShipping'] == false) ) {
                    $data['shipping']   = $addressData['newAddress'];
                }
                
                if ( isset($addressData['newAddress']['save_as_address']) && ($addressData['newAddress']['save_as_address'] == 1 || $addressData['newAddress']['save_as_address'] == true)) {

                    $data['billing']['save_as_address'] = true;
                    unset($data['shipping']['address_id']);
                } else {
                    if ( isset($data['billing']['save_as_address']) ) {
                        unset($data['billing']['save_as_address']);
                    }
                }
            }
        }
        
        $data['billing']['use_for_shipping']  =  false;
        if ( isset($addressData['sameAsShipping']) && $addressData['sameAsShipping'] == 1) {
            $data['billing']['use_for_shipping']  =  true;    
        }

        if ( $this->isVirtualCart == true || (isset($addressData['sameAsShipping']) && $addressData['sameAsShipping'] == 1)) {
            $data['shipping']['address1'] = '';
            if ( isset($data['shipping']['address_id'])) {
                unset($data['shipping']['address_id']);
            }
        }
        
        try {
            if (! is_null($cart)) {
                if (Cart::hasError() || ! Cart::saveCustomerAddress($data)) {
                    return [
                        'success'   => false,
                        'message'   => trans('mobikul-api::app.api.checkout.shipping-methods.error-shipping-address'),
                        'redirect'  => true
                    ];
                } else {
                    $cart = Cart::getCart();
        
                    Cart::collectTotals();
                    
                    if ($cart->haveStockableItems()) {
                        if (! $rates = Shipping::collectRates()) {
                            return [
                                'success'   => false,
                                'message'   => trans('mobikul-api::app.api.checkout.shipping-methods.error-shipping-rates'),
                                'redirect'  => true
                            ];
                        } else {
                            foreach ($rates['shippingMethods'] as $shippingMethod) {
                                $methods = [];
                                foreach ($shippingMethod['rates'] as $rate) {
                                    
                                    $methods = [
                                        'code'              => $rate->method,
                                        'label'             => $rate->method_title,
                                        
                                        'price'             => (Float) core()->convertPrice($rate->base_price, $this->currencyCode),
                                        'formattedPrice'    => (String) core()->formatPrice(core()->convertPrice($rate->base_price, $this->currencyCode), $this->currencyCode),
                                        
                                        'basePrice'         => (Float) core()->convertPrice($rate->base_price, core()->getBaseCurrencyCode()),
                                        'formattedBasePrice'=> (String) core()->formatPrice(core()->convertPrice($rate->base_price, core()->getBaseCurrencyCode()), core()->getBaseCurrencyCode())
                                    ];
                                }

                                $this->shippingMethods[] = [
                                    'title'     => $shippingMethod['carrier_title'],
                                    'methods'   => $methods,
                                ];
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return [
                'success'   => false,
                'message'   => $e->getMessage(),
            ];
        }

        return [
            'success'           => true,
            'message'           => "",
            'cartTotal'         => (string) core()->formatPrice(core()->convertPrice($cart->base_grand_total, $this->currencyCode), $this->currencyCode),
            'cartCount'         => $this->cartItemCount,
            'shippingMethods'   => $this->shippingMethods
        ];
    }
}
