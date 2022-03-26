<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Index;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UploadBannerPic extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->customerRepository = app('Webkul\Customer\Repositories\CustomerRepository');

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
        $customer = $this->customerRepository->findOrFail($this['customer_id']);
        $type = 'file';
        $field_name = 'banner_pic';
        if (isset($this[$type])) {
            $request = request();

            $file = $type;
            $dir = 'mobikul_banner_image/' . $customer->id;

            if ($request->hasFile($file)) {
                if ($customer->{$field_name}) {
                    Storage::delete($customer->{$field_name});
                }

                $customer->{$field_name} = $request->file($file)->store($dir);
                $customer->save();
                
                return [
                    'success'       => true,
                    'message'       => trans('mobikul-api::app.api.index.upload-banner-pic.success-banner-uploaded'),
                    'banner_pic'   => Storage::url($customer->{$field_name}),
                ];
            }
        } else {
            if ($customer->{$field_name}) {
                Storage::delete($customer->{$field_name});
            }

            $customer->{$field_name} = null;
            $customer->save();
            
            return [
                'success'       => true,
                'message'       => 'Error: no banner pic uploaded.',
            ];
        }
    }
}