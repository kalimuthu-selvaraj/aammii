<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressBookData extends JsonResource
{
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
        $addresses = [];
        $addresses = $this->customerAddressRepository->findWhere([
            'customer_id'   => $customer->id,
            'address_type'  => 'customer',
        ]);
        
        $defaultAddress = $this->customerAddressRepository->findOneWhere([
            'customer_id'       => $customer->id,
            'address_type'      => 'customer',
            'default_address'   => 1,
        ]);

        $billingAddress = new \stdClass();
        $shippingAddress = new \stdClass();
        $additionalAddresses = [];

        if ( $addresses ) {
            foreach( $addresses as $key => $address) {
                if ( ($address->default_address == 1) || (! isset($defaultAddress->id) && $key == 0) ) {
                    $billingAddress = [
                        'id'    => $address['id'],
                        'value' => $address['first_name'] . ' ' . $address['last_name'] . PHP_EOL . $address['address1'] . $address['address2'] . PHP_EOL . $address['country'] . PHP_EOL . $address['state'] . PHP_EOL . $address['city'] . PHP_EOL . $address['postcode'] . PHP_EOL . 'T:' . $address['phone']
                    ];

                    $shippingAddress = [
                        'id'    => $address['id'],
                        'value' => $address['first_name'] . ' ' . $address['last_name'] . PHP_EOL . $address['address1'] . $address['address2'] . PHP_EOL . $address['country'] . PHP_EOL . $address['state'] . PHP_EOL . $address['city'] . PHP_EOL . $address['postcode'] . PHP_EOL . 'T:' . $address['phone']
                    ];
                } else {
                    $additionalAddresses[] = [
                        'id'    => $address['id'],
                        'value' => $address['first_name'] . ' ' . $address['last_name'] . PHP_EOL . $address['address1'] . $address['address2'] . PHP_EOL . $address['country'] . PHP_EOL . $address['state'] . PHP_EOL . $address['city'] . PHP_EOL . $address['postcode'] . PHP_EOL . 'T:' . $address['phone']
                    ];
                }
            }
        }

        return [
            'success'           => true,
            'message'           => '',
            'billingAddress'    => $billingAddress,
            'shippingAddress'   => $shippingAddress,
            'additionalAddress' => $additionalAddresses,
            'addressCount'      => count($addresses),
            'eTag'              => '3fcceed4d844f9646517de88206f3d18', //need discussion
        ];
    }
}