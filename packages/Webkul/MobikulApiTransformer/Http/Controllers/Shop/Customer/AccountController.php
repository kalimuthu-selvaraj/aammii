<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Customer;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\CreateAccount;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\InfoAccount;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\SaveAccount;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\Login;

/**
 * Account Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class AccountController extends Controller
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
     * Controller instance
     *
     * @param \Webkul\Customer\Repositories\CustomerRepository $customerRepository
     * @return void
     */
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';
        
        auth()->setDefaultDriver($this->guard);

        $this->middleware('auth:' . $this->guard, ['only' => ['get', 'save']]);

        $this->middleware('validateAPIHeader');
        
        $this->_config = request('_config');

        $this->customerRepository = $customerRepository;
    }
    
    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'firstName'     => 'required',
            'lastName'      => 'required',
            'email'         => 'required|email|unique:customers,email',
            'password'      => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }
        
        return response()->json(new CreateAccount(request()->all()));
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function get(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'storeId'   => 'required',
            'token'     => 'required',
        ]);

        if ( $validator->fails() ) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true ) {
            $loggedCustomer = auth()->guard($this->guard)->user();

            return response()->json(new InfoAccount($loggedCustomer));
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
        $validator = Validator::make(request()->all(), [
            'token'                 => 'required',
            'storeId'               => 'required|integer',
            'first_name'            => 'required|string',
            'last_name'             => 'required|string',
            'gender'                => 'required|integer',
            'mobile'                => 'required',
            'dob'                   => 'string|before:today',
            'doChangePassword'      => 'required',
            'oldpassword'           => 'required_if:doChangePassword,1',
            'password'              => 'confirmed|min:6|required_if:doChangePassword,1|required_with:oldpassword',
            'password_confirmation' => 'required_with:password',
            'email'                 => 'required|email',
            'doChangeEmail'         => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => json_encode($validator->messages()),
            ], 200);
        }

        $data = request()->all();

        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true ) {
            $data['customer'] = auth()->guard($this->guard)->user()->toArray();

            return response()->json(new SaveAccount($data));
        } else {
            return $authentication;
        }
    }

    /**
    * Store a newly created resource in storage.
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function forgot(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }
		 
        $response = $this->broker()->sendResetLink(request(['email']));
		 

        if ( $response == Password::RESET_LINK_SENT ) {
            return response()->json([
                'success'   => true,
                'message'   => trans('customer::app.forget_password.reset_link_sent'),
            ]);
        } else {
            return response()->json([
                'success'   => false,
                'message'   => trans($response),
            ]);
        }
    }

    /**
    * Get the broker to be used during password reset.
    *
    * @return \Illuminate\Contracts\Auth\PasswordBroker
    */
    public function broker()
    {
        return Password::broker('customers');
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function checkCustomerByEmail(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(),[
            'email'     => 'email|required',
            'storeId'   => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $data = request()->all();

        $customer = $this->customerRepository->findOneByField('email', $data['email']);

        if ( $customer ) {
            return response()->json([
                'success'           => true,
                'message'           => trans('mobikul-api::app.api.customer.get-customer.success-exists', ['customer_email' => $customer->email]),
                'isCustomerExist'   => true,
            ], 200);
        } else {
            return response()->json([
                'success'           => false,
                'message'           => trans('mobikul-api::app.api.customer.get-customer.error-exists', ['customer_email' => $data['email']]),
                'isCustomerExist'   => false
            ], 200);
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function formData(): ?JsonResponse
    {
        return response()->json([
            'success'               => true,
            'message'               => "",
            'isPrefixVisible'       => false,
            'isPrefixRequired'      => false,
            'isSuffixVisible'       => false,
            'isSuffixRequired'      => false,
            'isMiddlenameVisible'   => false,
            'isMobileVisible'       => false,
            'isMobileRequired'      => false,
            'isDobVisible'          => false,
            'isDobRequired'         => false,
            'isTaxVisible'          => false,
            'isTaxRequired'         => false,
            'isGenderVisible'       => false,
            'isGenderRequired'      => false,
            'dateFormat'            => "d/m/Y",
            'eTag'                  => '3dh73smv4k4qr157a65u6jun46',
        ], 200);
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function login(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'username' => 'required|email',
            'password' => 'required'
        ]);

        if ( $validator->fails() ) {
            return response()->json([
                'success'   => false,
                'message'   => trans('mobikul-api::app.api.customer.login.error-parameters'),
            ], 200);
        }
        
        return response()->json(new Login(request()->all()));
    }
}