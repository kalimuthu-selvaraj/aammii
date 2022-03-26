<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Checkout;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\MobikulApiTransformer\Http\Resources\Checkout\ChangeOrderStatus;

/**
 * Order Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class OrderController extends Controller
{
    /**
     * Contains current guard.
     *
     * @var array
     */
    protected $guard;

    /**
     * Contains route related configuration.
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
     * Contains allowed order's status
     *
     * @var array
     */
    protected $allow_status = [
        'pending',
        'pending_payment',
        'processing',
        'completed',
        'canceled',
        'closed',
        'fraud',
    ];

    public function __construct(OrderRepository $orderRepository)
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('auth:' . $this->guard, ['only' => ['index']]);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');

        $this->orderRepository = $orderRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'status'            => 'required|integer',
            'storeId'           => 'required|integer',
            'incrementId'       => 'required',
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
        if ( $authentication === true ) {
            $loggedCustomer = auth()->guard($this->guard)->user();

            $order = $this->orderRepository->findOneByField('increment_id', $data['incrementId']);
    
            if (! $order ) {
                return response()->json([
                    'success' => false,
                    'message' => trans('mobikul-api::app.api.checkout.change-order-status.error-order-not-found', [
                        'increment_id' => $data['incrementId']
                    ]),
                ], 200);
            }
    
            if ( $order->customer_id != $loggedCustomer->id ) {
                return response()->json([
                    'success' => false,
                    'message' => trans('mobikul-api::app.api.checkout.change-order-status.error-not-auth-customer')
                ], 200);
            }
    
            if ( ! isset($this->allow_status[$data['status']]) ) {
                return response()->json([
                    'success' => false,
                    'message' => trans('mobikul-api::app.api.checkout.change-order-status.error-order-status'),
                ], 200);
            }
            
            if ( $order->status == $this->allow_status[$data['status']] ) {
                return response()->json([
                    'success' => false,
                    'message' => trans('mobikul-api::app.api.checkout.change-order-status.error-already-set', [
                        'order_status'  => $order->status_label
                    ]),
                ]);
            }

            return response()->json(new ChangeOrderStatus([
                'order' => $order
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
    public function guestView(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'         => 'required',
            'storeId'       => 'required|integer',
            'incrementId'   => 'required|integer',
            'type'          => 'required|string',
            'zipCode'       => 'required|integer',
            'lastName'      => 'required',
            'email'         => 'email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $data = request()->all();
        $params = [
            'id'                    => $data['incrementId'],
            'is_guest'              => 1,
            'customer_id'           => NULL,
            'customer_last_name'    => $data['lastName']
        ];

        if ( isset($data['email']) && $data['email']) {
            $params['customer_email']   = $data['email'];
        }
        
        $order = $this->orderRepository->with('addresses')->findOneWhere($params);

        if ( $order ) {
            $order_address = $order->addresses->where('postcode', $data['zipCode'])->first();

            if ( $order_address ) {
                return response()->json([
                    'success' => true,
                    'message' => trans('mobikul-api::app.api.sales.guest-view.success-valid-details'),
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => trans('mobikul-api::app.api.sales.guest-view.error-incorrect-details'),
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => trans('mobikul-api::app.api.sales.guest-view.error-incorrect-details'),
            ], 200);
        }
    }
}