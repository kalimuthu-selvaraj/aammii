<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class InfoAccount extends JsonResource
{
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
        return [
            'success'               => true,
            'message'               => '',
            'email'                 => $this->email,
            'lastName'              => $this->last_name,
            'firstName'             => $this->first_name,
            'gender'                => $this->gender,
            'isPrefixVisible'       => false,
            'isPrefixRequired'      => false,
            'prefixValue'           => '',
            'middleName'            => '',
            'isMiddlenameVisible'   => false,
            'isSuffixVisible'       => false,
            'isSuffixRequired'      => false,
            'suffixValue'           => '',
            'isMobileVisible'       => true,
            'isMobileRequired'      => false,
            'mobile'                => $this->phone ? $this->phone : '',
            'isDOBVisible'          => true,
            'isDOBRequired'         => false,
            'DOBValue'              => $this->date_of_birth ? core()->formatDate(Carbon::createFromTimeString(str_replace('-', '/', $this->date_of_birth) . " 00:00:01"), "d/m/Y") : NULL,
            'isTaxVisible'          => false,
            'isTaxRequired'         => false,
            'taxValue'              => "0",
            'isGenderVisible'       => true,
            'isGenderRequired'      => true,
            'genderValue'           => (($this->gender == 'Male') ? 1 : ($this->gender == 'Female')) ? 2 : "3",
            'dateFormat'            => "d/m/Y",
            'eTag'                  => "ad183418054f6672ed7c9dde2663a2f2", //need discussion
        ];
    }
}

