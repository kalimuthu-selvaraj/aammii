<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Hash;

class SaveAccount extends JsonResource
{
    /**
     * Contains gender values.
     *
     * @var array
     */
    protected $gender = [0 => '', 1 => 'Male', 2 => 'Female', 3 => 'Other'];

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
        $data = $request->toArray();
        
        if ( $data['doChangeEmail'] == 1 ) {
            $validator = Validator::make([
                'email' => $data['email']
            ], [
                'email' => 'required|email|unique:customers,email,' . $this['customer']['id']
            ]);
    
            if ( $validator->fails() ) {
                return [
                    'success'   => false,
                    'message'   => json_encode($validator->messages()),
                ];
            }
        } elseif ( isset($data['email'])) {
            unset($data['email']);
        }

        if ( $data['doChangePassword'] == 1 && isset($data['password']) && $data['password'] && isset($data['oldpassword'])) {
            if ( Hash::check($data['oldpassword'], auth()->guard('api')->user()->password)) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
                unset($data['oldpassword']);
                unset($data['password_confirmation']);
            }
        } elseif ( isset($data['password'])) {
            unset($data['password']);
            unset($data['password_confirmation']);
        }

        $data['gender'] = isset($this->gender[$data['gender']]) ? $this->gender[$data['gender']] : '';
        $data['phone']  = $data['mobile'];

        if (isset ($data['dob']) && $data['dob'] == "") {
            unset($data['dob']);
        }

        $data['date_of_birth'] = (isset($data['dob']) && $data['dob']) ? Carbon::createFromTimeString(str_replace('/', '-', $data['dob']) . '00:00:01')->format('Y-m-d') : '';

        $customer = $this->customerRepository->update($data, $this['customer']['id']);
        if ( isset($customer->id)) {
            return [
                'success'   => true,
                'message'   => trans('mobikul-api::app.api.customer.save-info.success-save'),
            ];
        } else {
            return [
                'success'   => false,
                'message'   => trans('mobikul-api::app.api.customer.save-info.error-save'),
            ];
        }
    }
}