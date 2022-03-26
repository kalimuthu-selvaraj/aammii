<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Customer;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Repositories\CustomerAddressRepository;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\AddressFormData;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\AddressBookData;

/**
 * Address controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class AddressController extends Controller
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
     * Customer Repository
     *
     * @var \Webkul\Customer\Repositories\CustomerRepository $customerRepository
     */
    protected $customerRepository;

    /**
     * customerAddress Repository
     *
     * @var \Webkul\Customer\Repositories\CustomerAddressRepository $customerAddressRepository
     */
    protected $customerAddressRepository;
    
    /**
     * Controller instance
     *
     * @param \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @param \Webkul\Customer\Repositories\CustomerAddressRepository $customerAddressRepository
     * @return void
     */
    public function __construct(
        CustomerRepository $customerRepository,
        CustomerAddressRepository $customerAddressRepository
        )
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';
        
        auth()->setDefaultDriver($this->guard);

        $this->middleware('auth:' . $this->guard, ['only' => ['formData', 'bookData']]);

        $this->middleware('validateAPIHeader');
        
        $this->_config = request('_config');

        $this->customerRepository = $customerRepository;

        $this->customerAddressRepository = $customerAddressRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function formData(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(),[
            'storeId'       => 'required|integer',
            'token'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $data = request()->all();
        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true || $token == 0 ) {
            $data['customer'] = auth()->guard($this->guard)->user();
            
            return response()->json(new AddressFormData($data));
        } else {
            return $authentication;
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookData(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'         => 'required',
            'storeId'       => 'required',
            'forDashboard'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }
        
        $data = request()->all();
        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true ) {
            $data['customer'] = auth()->guard($this->guard)->user();
            
            return response()->json(new AddressBookData($data));
        } else {
            return $authentication;
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(),[
            'addressId'     => 'required|integer',
            'addressData'   => 'required',
            'token'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'    => false,
                'message'   => json_encode($validator->messages()),
            ], 200);
        }

        $data = request()->all();
        
        if (gettype($data['addressData']) == 'string') {
            $data['addressData'] = json_decode($data['addressData'], true);
        } else {
            $data['addressData'] = $data['addressData'];
        }
        
        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true ) {
            if ( isset($data['addressId']) && $data['addressId']) {
                $address = $this->customerAddressRepository->findOrFail($data['addressId']);
            
                if ( $address ) {
                    $customerAddress = $this->customerAddressRepository->update($data['addressData'], $address->id);

                    return response()->json([
                        'success' => true,
                        'message' => trans('mobikul-api::app.api.customer.save-address.success-updated'),
                    ], 200);
                }
            }

            $data['addressData']['customer_id'] = auth($this->guard)->user()->id;

            $customerAddress = $this->customerAddressRepository->create($data['addressData']);

            if ( $customerAddress ) {
                return response()->json([
                    'success'   => true,
                    'message'   => trans('mobikul-api::app.api.customer.save-address.success-saved'),
                    'addressId' => $customerAddress->id,
                ], 200);
            }
        } else {
            return $authentication;
        }
    }

     /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function delete(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(),[
            'addressId'     => 'required',
            'token'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $data = request()->all();
        $token = request()->token;
        
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true ) {
            $loggedCustomer = auth($this->guard)->user();
            
            $address = $this->customerAddressRepository->findOneWhere([
                'id'            => $data['addressId'],
                'customer_id'   => $loggedCustomer->id
                ]);

            if ( $address ) {
                $this->customerAddressRepository->find($data['addressId'])
                ->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Success: Customer\'s address has been deleted successfully.',
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.customer.address-form-data.not-found'),
                ], 200);
            }
        } else {
            return $authentication;
        }
    }
}