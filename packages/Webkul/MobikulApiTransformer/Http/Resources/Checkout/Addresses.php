<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Checkout;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Facades\Cart;

class Addresses extends JsonResource
{
    /**
     * Contains customer's addresses.
     *
     * @var array
     */
    protected $addresses = [];

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
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
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
        $customer = $this['customer'];

        $customerAddresses = $this->customerAddressRepository->findWhere([
            'customer_id' => $customer->id
        ]);

        $defaultAddress = $this->customerAddressRepository->findOneWhere([
            'customer_id'       => $customer->id,
            'default_address'   => 1,
        ]);
        
        if ( $defaultAddress ) {
            $this->addresses[0] = [
                'value' => "{$defaultAddress->first_name} {$defaultAddress->last_name} 
                {$defaultAddress->address1}, {$defaultAddress->address2}
                {$defaultAddress->city}, {$defaultAddress->state}, {$defaultAddress->postcode}
                {$defaultAddress->country}
                T: {$defaultAddress->phone}",
                'id' => $defaultAddress->id
            ];
        }
        
        foreach ($customerAddresses as $customerAddress) {
            if ( $customerAddress->default_address == 0) {
                $this->addresses[] = [
                    'value' => "{$customerAddress->first_name} {$customerAddress->last_name} 
                    {$customerAddress->address1}, {$customerAddress->address2}
                    {$customerAddress->city}, {$customerAddress->state}, {$customerAddress->postcode}
                    {$customerAddress->country}
                    T: {$customerAddress->phone}",
                    'id' => $customerAddress->id
                ];
            }
        }

        $cart = Cart::getCart();
        if ( $cart ) {
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

        $response = [
            'success'               => true,
            'message'               => '',
            'address'               => $this->addresses,
            'lastName'              => isset($customer->last_name) ? $customer->last_name : '',
            'firstName'             => isset($customer->first_name) ? $customer->first_name : '',
            'middleName'            => '',
            'prefixValue'           => '',
            'suffixValue'           => '',
            'cartCount'             => $this->cartItemCount,
            'isVirtual'             => $this->isVirtualCart,
            'streetLineCount'       => 2,
            'defaultCountry'        => config('app.default_country'),
            'allowToChooseState'    => true
        ];

        if (! isset($customer->id)) {
            unset($response['address']);
        }

        return $response;
    }
}
