<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Checkout;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Validator;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\CreateAccount;
use Webkul\MobikulApiTransformer\Http\Resources\Checkout\Addresses;
use Webkul\MobikulApiTransformer\Http\Resources\Checkout\AddressFormData;
use Webkul\MobikulApiTransformer\Http\Resources\Checkout\ShippingMethod;
use Webkul\MobikulApiTransformer\Http\Resources\Checkout\Payment;
use Webkul\MobikulApiTransformer\Http\Resources\Checkout\PlaceOrder;
use Razorpay\Api\Api;

/**
 * Checkout controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CheckoutController extends Controller
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
     * OrderRepository Object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * CustomerRepository Object
     *
     * @var \Webkul\Customer\Repositories\CustomerRepository
     */
    protected $customerRepository;

    /**
     * CustomerGroupRepository Object
     *
     * @var \Webkul\Customer\Repositories\CustomerGroupRepository
     */
    protected $customerGroupRepository;

    /**
     * CustomerAddressRepository Object
     *
     * @var array
     */
    protected $customerAddressRepository;

    /**
     * Controller instance
     *
     * @param \Webkul\Sales\Repositories\OrderRepository        $orderRepository
     * @param \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @param \Webkul\Customer\Repositories\CustomerGroupRepository $customerGroupRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        CustomerGroupRepository $customerGroupRepository
    )   {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('auth:' . $this->guard);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');

        $this->orderRepository = $orderRepository;

        $this->customerRepository = $customerRepository;

        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'orderId'   => 'required|integer',
            'storeId'   => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $orderRepository = $this->orderRepository->findOneWhere([
            'id'            => request()->orderId,
            'channel_id'    => request()->storeId
            ]);

        if ( $orderRepository ) {
            $customerRepository = $this->customerRepository->findOneByField('email', $orderRepository->customer_email);

            if ( $customerRepository ) {
                $customerRepository->token = JWTAuth::fromUser($customerRepository);
                $customerRepository->save();
                $cartItemCount = 0;
                $cart = Cart::getCart();

                if ( $cart ) {
                    $cartItemCount = count($cart->items);
                }

                return response()->json([
                    'success'           => true,
                    'message'           => trans('mobikul-api::app.api.customer.login.success-login'),
                    'customerName'      => $customerRepository->name,
                    'cartCount'         => $cartItemCount,
                    'customerEmail'     => $customerRepository->email,
                    'token'             => $customerRepository->token,
                ]);

            } else {

                $data = [
                    'firstName'        => $orderRepository['customer_first_name'],
                    'lastName'         => $orderRepository['customer_last_name'],
                    'api_token'         => Str::random(80),
                    'token'             => md5(uniqid(rand(), true)),
                    'email'             => $orderRepository['customer_email'],
                    'password'          => bcrypt($orderRepository['customer_email'])
                ];

                return response()->json(new CreateAccount($data));
            }
        } else {
            return response()->json([
                'success'   => false,
                'message'   => trans('mobikul-api::app.api.checkout.create-account.error-order-not-found'),
            ], 200);
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addresses(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'quoteId'       => 'required|integer',
            'storeId'       => 'required|integer',
            'token'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);
        if ( $authentication === true || $token == 0 ) {
            $cart = Cart::getCart();

            if (! $cart = Cart::getCart()) {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-empty-cart'),
                ], 200);
            }

            if (! auth()->guard($this->guard)->check() && ! $cart->hasGuestCheckoutItems()) {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.checkout.shipping-methods.error-guest-product')
                ], 200);
            }

            if ( auth()->guard($this->guard)->check() ) {
                return response()->json(new Addresses([
                    'customer'  => auth()->guard($this->guard)->user()])
                );
            } else {
                return response()->json(new Addresses([
                    'customer'  => []
                ]));
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
    public function addressFormData(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'storeId'       => 'required|integer',
            'isGuest'       => 'boolean',
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

            return response()->json(new AddressFormData([
                'customer'  => auth()->guard($this->guard)->user()
            ]));
        } else {
            return $authentication;
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function shippingMethods(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'quoteId'       => 'required|integer',
            'storeId'       => 'required|integer',
            'token'         => 'required',
            'shippingData'  => 'required',
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
            $cart = Cart::getCart();

            if (! $cart ) {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-empty-cart'),
                ], 200);
            }

            if (! auth()->guard($this->guard)->check() && ! $cart->hasGuestCheckoutItems()) {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.checkout.shipping-methods.error-guest-product')
                ], 200);
            }

            return response()->json(new ShippingMethod($data));
        } else {
            return $authentication;
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reviewAndPayment(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'quoteId'           => 'required|integer',
            'storeId'           => 'required|integer',
            'shippingMethod'    => "required|string",
            'token'             => 'required',
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
            $data['shipping_method'] = $data['shippingMethod'];
            unset($data['shippingMethod']);

            $cart = Cart::getCart();

            if (! $cart ) {
                return response()->json([
                    'status'    => false,
                    'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-empty-cart'),
                ], 200);
            }

            return response()->json(new Payment($data));
        } else {
            return $authentication;
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function applyCoupon(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'         => 'required',
            'storeId'       => 'required',
            'quoteId'       => 'required',
            'couponCode'    => 'required',
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
            $cart = Cart::getCart();
            if (! $cart ) {
                return response()->json([
                    'status'    => false,
                    'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-empty-cart'),
                ], 200);
            }

            try {
                if ( strlen($data['couponCode']) ) {
                    Cart::setCouponCode($data['couponCode'])->collectTotals();

                    if ( Cart::getCart()->coupon_code == $data['couponCode'] ) {
                        return response()->json([
                            'success' => true,
                            'message' => trans('shop::app.checkout.total.success-coupon'),
                        ], 200);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => trans('shop::app.checkout.total.invalid-coupon'),
                        ], 200);
                    }
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
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
    public function removeCoupon(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'         => 'required',
            'storeId'       => 'required',
            'quoteId'       => 'required',
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
            $cart = Cart::getCart();
            if (! $cart ) {
                return response()->json([
                    'status'    => false,
                    'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-empty-cart'),
                ], 200);
            }
            if ( $cart->coupon_code == null ) {
                return response()->json([
                    'status'    => false,
                    'message'   => trans('mobikul-api::app.api.checkout.coupon.no-coupon-applied'),
                ], 200);
            }

            try {
                if (! isset($data['removeCoupon'])
                || (isset($data['removeCoupon']) && $data['removeCoupon'] == 0) ) {
                    return response()->json([
                        'success' => false,
                        'message' => trans('shop::app.checkout.total.invalid-coupon'),
                    ], 200);
                }

                $couponCode = $cart->coupon_code;

                Cart::removeCouponCode()->collectTotals();

                return response()->json([
                    'success'   => true,
                    'message'   => trans('mobikul-api::app.api.checkout.coupon.remove-success', [
                        'couponCode'    => $couponCode,
                    ])
                ], 200);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
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
    public function razorpayOrder(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'             => 'required',
            'storeId'           => 'required|integer',
            'paymentMethod'     => "required|string",

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

            if (! $cart = Cart::getCart()) {
                return response()->json([
                    'status'    => false,
                    'message'   => trans('mobikul-api::app.api.checkout.place-razorpay-order.error-empty-cart'),
                ], 200);
            }

            if ( $data['paymentMethod'] != 'razorpay_mobile' ) {
                return response()->json([
                    'status'    => false,
                    'message'   => trans('mobikul-api::app.api.checkout.place-razorpay-order.error-payment-method'),
                ], 200);
            }

            //Process Razorpay Order
            $shipping_rate = $cart->selected_shipping_rate ? $cart->selected_shipping_rate->price : 0; // shipping rate
            $discount_amount = $cart->discount_amount; // discount amount
            $total_amount =  ($cart->sub_total + $cart->tax_total + $shipping_rate) - $discount_amount; // total amount

            //include __DIR__ . '/../../../../../../../vendor/wontonee/razorpay/src/razorpay-php/Razorpay.php';

            // include __DIR__ . '/../../razorpay-php/Razorpay.php';
			
			include base_path(). '/packages/Mesk/razorpay/src/razorpay-php/Razorpay.php';
			
            $api = new Api(core()->getConfigData('mobikul.mobikul.razorpay_mobile.merchant_id'), core()->getConfigData('mobikul.mobikul.razorpay_mobile.merchant_secret'));

            $orderData = [
                'receipt'         => $cart->id,
                'amount'          => $total_amount* 100,
                'currency'        => $cart->cart_currency_code,
                'payment_capture' => 1 // auto capture
            ];

            $razorpayOrder = $api->order->create($orderData);
            
            if( isset($razorpayOrder['id']) && $razorpayOrder['id']) {

                return response()->json([
                    'success'          => true,
                    'razorpay_order_id' => $razorpayOrder['id']
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => trans('mobikul-api::app.api.checkout.place-razorpay-order.went-wrong')
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
    public function placeOrder(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'             => 'required',
            'storeId'           => 'required|integer',
            'quoteId'           => 'required|integer',
            // 'fcmToken'          => 'required',
            'paymentMethod'     => "required|string",
            'billingData'       => 'required',
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
            $data['payment'] = ['method' => $data['paymentMethod']];
            unset($data['paymentMethod']);

            if (! $cart = Cart::getCart()) {
                return response()->json([
                    'status'    => false,
                    'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-empty-cart'),
                ], 200);
            }

            return response()->json(new PlaceOrder($data));
        } else {
            return $authentication;
        }
    }
}