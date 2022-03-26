<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Customer;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\MobikulApiTransformer\Http\Resources\Index\UploadProfilePic;
use Webkul\MobikulApiTransformer\Http\Resources\Index\UploadBannerPic;

/**
 * Customer controlller for the customer basically for the tasks of customers which will be
 * done after customer authentication.
 *
 * @author  Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CustomerController extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * CustomerRepository object
     *
     * @var Object
    */
    protected $customerRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository $customer
     * @return void
    */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->guard = request()->has('token') ? 'api' : 'customer' ;
        
        auth()->setDefaultDriver($this->guard);

        $this->middleware('auth:' . $this->guard);

        $this->middleware('validateAPIHeader', ['only' => ['uploadProfilePic', 'uploadBannerPic']]);
        
        $this->_config = request('_config');

        $this->customerRepository = $customerRepository;
    }

    /**
     * Edit function for editing customer profile.
     *
     * @return response
     */
    public function update()
    {
        $id = auth()->guard($this->guard)->user()->id;

        $this->validate(request(), [
            'first_name'    => 'string',
            'last_name'     => 'string',
            'gender'        => 'required',
            'date_of_birth' => 'date|before:today',
            'email'         => 'email|unique:customers,email,' . $id,
            'oldpassword'   => 'required_with:password',
            'password'      => 'confirmed|min:6'
        ]);

        $data = collect(request()->all())->except('_token')->toArray();
        
        if (isset($data['profile_pic']) && !empty ($data['profile_pic'])) {
            foreach ($data['profile_pic'] as $pic) {
                if ( ! empty($pic)) {
                    $filename = $id. '.' . $pic->getClientOriginalExtension();
                    $pic->move(storage_path('app/public/mobikul_profile_image'), $filename);
                    $data['profile_pic'] = $filename;
                } else {
                    $data['profile_pic'] = '';
                }
            }
        }

        if (! $data['date_of_birth']) {
            unset($data['date_of_birth']);
        }
            

        if ($data['oldpassword'] != "" || $data['oldpassword'] != null) {
            if ( Hash::check($data['oldpassword'], auth()->guard($this->guard)->user()->password)) {
                $data['password'] = bcrypt($data['password']);
            } else {
                session()->flash('warning', trans('shop::app.customer.account.profile.unmatch'));

                return redirect()->back();
            }
        } else {
            unset($data['password']);
        }

        if ( $this->customerRepository->update($data, $id) ) {
            Session()->flash('success', trans('shop::app.customer.account.profile.edit-success'));

            return redirect()->route($this->_config['redirect']);
        } else {
            Session()->flash('success', trans('shop::app.customer.account.profile.edit-fail'));

            return redirect()->back($this->_config['redirect']);
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadProfilePic(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'file'  => 'mimes:jpeg,jpg,bmp,png',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true ) {
            $data = collect(request()->all())->except('_token')->toArray();
            $data['customer_id'] = auth()->guard($this->guard)->user()->id;
            
            return response()->json(new UploadProfilePic($data));
        } else {
            return $authentication;
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadBannerPic(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'file'  => 'mimes:jpeg,jpg,bmp,png',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'    => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true ) {
            $data = collect(request()->all())->except('_token')->toArray();
            $data['customer_id'] = auth()->guard($this->guard)->user()->id;
            return response()->json(new UploadBannerPic($data));
        } else {
            return $authentication;
        }
    }
}
