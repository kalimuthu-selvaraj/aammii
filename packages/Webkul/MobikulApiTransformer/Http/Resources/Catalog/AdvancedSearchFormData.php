<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvancedSearchFormData extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'success'       => true,
            'message'       => '',
            'productList'   => [],
        ];
    }
}