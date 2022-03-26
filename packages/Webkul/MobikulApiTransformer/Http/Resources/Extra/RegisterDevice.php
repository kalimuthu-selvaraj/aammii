<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Extra;

use Illuminate\Http\Resources\Json\JsonResource;

class RegisterDevice extends JsonResource
{
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->registerDeviceRepository = app('Webkul\Mobikul\Repositories\RegisterDeviceRepository');

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
        $data = [
            'customer_id'   => isset($this['id']) ? $this['id'] : 0,
            'fcmToken'      => $request->fcmToken,
            'os'            => $request->os,
        ];

        $registerDevice = $this->registerDeviceRepository->findOneWhere($data);
        if ( $registerDevice ) {
            
            return [
                'success'       => true,
                'message'       => trans('mobikul-api::app.api.extra.register-device.already-register'),
                'deviceDetails' => $registerDevice,
            ];
        } else {
            $registerDevice = $this->registerDeviceRepository->create($data);

            if ( $registerDevice ) {
                return [
                    'success'       => true,
                    'message'       => trans('mobikul-api::app.api.extra.register-device.success-register'),
                    'deviceDetails' => $registerDevice,
                ];
            }
        }
    }
}

