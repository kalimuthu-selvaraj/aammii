<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressFormData extends JsonResource
{
    /**
     * Contains countries with states.
     *
     * @var array
     */
    protected $countryList = [];

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->customerAddressRepository = app('Webkul\Customer\Repositories\CustomerAddressRepository');

        $this->countryRepository = app('Webkul\Core\Repositories\CountryRepository');

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
        $customer = $this['customer'];
        foreach (core()->countries() as $country) {
            $countyStates = [];
            $states = core()->states($country['code']);
            if ( $states ) {
                foreach ($states as $state) {
                    $countyStates[] =   [
                        'code'      => $state->code,
                        'name'      => $state->default_name,
                        'region_id' => $state->id,
                    ];
                }
            }

            $this->countryList[] = [
                'name'              => $country['name'],
                'country_id'        => $country['code'],
                'isStateRequired'   => true,
                'isZipOptional'     => false,
                'states'            => $countyStates,
            ];
        }

        $address = new \stdClass();
        if ( isset($request->addressId) && $request->addressId && isset($customer->id)) {
            $address = $this->customerAddressRepository->findOneWhere([
                'id'            => $request->addressId,
                'address_type'  => 'customer',
                'customer_id'   => $customer->id,
            ]);

            if ( $address ) {
                $country = $this->countryRepository->findOneByField('code', $address->country);

                $countryState = $this->countryStateRepository->with('translations')->findOneWhere([
                    'country_code'  => $address->country,
                    'code'          => $address->state,
                ]);

                $street_address = [$address->address1];
                if ( $address->address2 ) {
                    array_push($street_address, $address->address2);
                }
                $address = [
                    'isDefaultBilling'  => $address->default_address ? true : false,
                    'isDefaultShipping' => $address->default_address ? true : false,
                    'entity_id'         => $address->id,
                    'is_active'         => '1',
                    'first_name'        => $address->first_name,
                    'last_name'         => $address->last_name,
                    'email'             => $customer->email,
                    'company_name'      => $address->company_name,
                    'address1'          => $address->address1,
                    'address2'          => $address->address2,
                    'street'            => $street_address,
                    'postcode'          => $address->postcode,
                    'city'              => $address->city,
                    'state'             => isset($countryState->default_name) ? $countryState->default_name : $address->country,
                    'state_id'          => isset($countryState->id) ? (int) $countryState->id : (int) $address->state,
                    'country'           => isset($country->name) ? $country->name : '',
                    'country_id'        => $address->country,
                    'phone'             => $address->phone,
                    'vat_id'            => $address->vat_id,
                ];
            }
        }

        return [
            'success'               => true,
            'message'               => "",
            'addressData'           => $address,
            'countryData'           => $this->countryList,
            'isCompanyVisible'      => true,
            'isCompanyRequired'     => false,
            'isTelephoneVisible'    => true,
            'isTelephoneRequired'   => true,
            'isFaxVisible'          => false,
            'isPrefixVisible'       => false,
            'isMiddlenameVisible'   => false,
            'isSuffixVisible'       => false,
            'isDOBVisible'          => false,
            'isTaxVisible'          => false,
            'isGenderVisible'       => false,
            'isAddressTitleVisible' => false,
            'isAddressTitleRequired'=> false,
            'lastName'              => $customer->last_name ?? '',
            'firstName'             => $customer->first_name ?? '',
            'middleName'            => '',
            'isMiddlenameVisible'   => false,
            'defaultCountry'        => config('app.default_country'),
            'streetLineCount'       => 2,
            'suffixOptions'         => [],
            'suffixHasOptions'      => false,
            'allowToChooseState'    => true,
            'eTag'                  => "1b41d43c4f3360a969a20e6c58244e2f"
        ];
    }
}