<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Checkout;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressFormData extends JsonResource
{
    /**
     * Contains address form data.
     *
     * @var array
     */
    protected $addressData = [];
    /**
     * Contains country data.
     *
     * @var array
     */
    protected $countryData = [];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->customerAddressRepository = app('Webkul\Customer\Repositories\CustomerAddressRepository');

        $this->countryStateRepository = app('Webkul\Core\Repositories\CountryStateRepository');
        
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
        $customer   = $this['customer'];
        $params     = [
            'customer_id'   => $customer->id
        ];
        
        if ( $request->addressId ) {
            $params['id']   = $request->addressId;
        }

        $address = $this->customerAddressRepository->findOneWhere($params);
        
        if ( $address ) {
            $countryState = $this->countryStateRepository->findOneWhere([
                'country_code'  => $address->country,
                'code'          => $address->state,
            ]);

            $street = [$address->address1];
            if ( $address->address2 ) {
                array_push($street, $address->address2);
            }
            
            $this->addressData = [
                'isDefaultBilling'      => $address->default_address ? true : false,
                'isDefaultShipping'     => $address->default_address ? true : false,
                'entity_id'             => $address->id,
                'increment_id'          => $address->id,
                'parent_id'             => 0,
                'created_at'            => $address->created_at,
                'updated_at'            => $address->updated_at,
                'is_active'             => 1,
                'city'                  => $address->city,
                'company'               => $address->company_name,
                'country_id'            => $address->country,
                'fax'                   => '',
                'firstname'             => $customer->first_name,
                'lastname'              => $customer->last_name,
                'middlename'            => '',
                'postcode'              => $address->postcode,
                'prefix'                => '',
                'region'                => isset($countryState->default_name) ? $countryState->default_name : $address->state,
                'region_id'             => isset($countryState->id) ? $countryState->id : 0,
                'street'                => $street,
                'suffix'                => "",
                'telephone'             => $address->phone,
                'vat_id'                => $address->vat_id,
                'vat_is_valid'          => "",
                'vat_request_date'      => "",
                'vat_request_id'        => "",
                'vat_request_success'   => ""
            ];
        }
        
        foreach (core()->groupedStatesByCountries() as $groupedCounry) {
            foreach($groupedCounry as $countryList) {
                $seprateStates = core()->states($countryList['code'])->toArray();
                $stateByCountries = [];
                if (! empty($seprateStates)) {
                    foreach($seprateStates as $seprateState) {
                        $statesArray[] = $seprateState['country_code'];
                        if ( in_array($seprateState['country_code'], $statesArray)) {
                            $stateByCountries[] =  [
                                'code' => $seprateState['code'],
                                'name' => $seprateState['default_name'],
                                'region_id' => $seprateState['id']
                            ];
                        }
                    }
                }

                $this->countryData[] = [
                    'name'          => $countryList['default_name'],
                    'country_id'    => $countryList['code'],
                    'states'        => (array)($stateByCountries),
                ];

                foreach($this->countryData as $key => $countryWithStates) {
                    if ( empty ($countryWithStates['states'])) {
                        $this->countryData[$key] = [
                            'name'          => $countryList['default_name'],
                            'country_id'    => $countryList['code']
                        ];
                    }
                }
            }
        }

        return [
            'success'               => true,
            'message'               => '',
            'lastName'              => $customer->last_name,
            'firstName'             => $customer->first_name,
            'addressData'           => $this->addressData,
            'countryData'           => $this->countryData,
            'defaultCountry'        => isset($address->id) ? $address->country : '',
            'streetLineCount'       => count($street),
            'allowToChooseState'    => true, //need discussion
            'eTag'                  => "b39f9ed2d5212b51c1d46fc07e4a5bfe"
        ];
    }
}
