<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Customer;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\ShipmentRepository;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\OrderDetails;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\OrderList;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\Invoice;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\Shipment;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\ReOrder;

/**
 * Order Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class OrderController extends Controller
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
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
    */
    protected $orderRepository;

    /**
     * InvoiceRepository object
     *
     * @var \Webkul\Sales\Repositories\InvoiceRepository
    */
    protected $invoiceRepository;

    /**
     * ShipmentRepository object
     *
     * @var \Webkul\Sales\Repositories\ShipmentRepository
    */
    protected $shipmentRepository;
    
    /**
     * Controller instance
     *
     * @param \Webkul\Sales\Repositories\OrderRepository    $orderRepository
     * @param \Webkul\Sales\Repositories\InvoiceRepository  $invoiceRepository
     * @param \Webkul\Sales\Repositories\ShipmentRepository $shipmentRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        InvoiceRepository $invoiceRepository,
        ShipmentRepository $shipmentRepository
    )   {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('auth:' . $this->guard, ['only' => ['list', 'invoice', 'shipment']]);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');

        $this->orderRepository = $orderRepository;

        $this->invoiceRepository = $invoiceRepository;

        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function view(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'incrementId'   => 'required',
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
            $params = [
                'increment_id'  => $data['incrementId'],
                'channel_id'    => $data['storeId'],
            ];
            if ( auth($this->guard)->check() ) {
                $loggedCustomer = auth($this->guard)->user();
                $params['customer_id'] = auth($this->guard)->user()->id;
                $params['is_guest'] = 0;
            } else {
                $loggedCustomer = [];
                $params['is_guest'] = 1;
            }

            $order = $this->orderRepository->findOneWhere($params);

            if (! isset($order->id)) {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.customer.order-list.order-not-found'),
                ], 200);
            } else {
                return response()->json(new OrderDetails([
                    'order'     => $order,
                    'customer'  => $loggedCustomer
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
    public function list(): ?JsonResponse
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

        $data = request()->all();
        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);
        
        if ( $authentication === true ) {
            $data['customer'] = auth()->guard($this->guard)->user();
           return response()->json(new OrderList($data));
        } else {
            return $authentication;
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function invoice(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'invoiceId'     => 'required|integer',
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
            $invoice = $this->invoiceRepository->with('items')->findOneWhere(['id'   => $data['invoiceId']]);
            
            if ( $invoice ) {
                return response()->json(new Invoice([
                    'invoice'   => $invoice,
                    'customer'  => $loggedCustomer
                ]), 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.customer.invoice-view.error-invalid-invoice'),
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
    public function shipment(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'storeId'       => 'required',
            'shipmentId'    => 'required|integer',
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
            $shipment = $this->shipmentRepository->findOneWhere([
                'id'            => $data['shipmentId'],
                'customer_id'   => $loggedCustomer->id
            ]);

            if ( $shipment ) {
                return response()->json(new Shipment([
                    'shipment'  => $shipment,
                    'customer'  => $loggedCustomer
                ]), 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.customer.shipment-view.error-invalid-shipment'),
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
    public function reorder(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'storeId'       => 'required',
            'incrementId'   => 'required',
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
            
            $order = $this->orderRepository->findOneWhere([
                'increment_id'  => $data['incrementId'],
                'channel_id'    => $data['storeId'],
                'customer_id'   => $loggedCustomer->id
            ]);

            if ( $order ) {
                return response()->json(new ReOrder([
                    'order'     => $order,
                    'customer'  => $loggedCustomer,
                ]));
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.customer.order-list.order-not-found'),
                ], 200);
            }
        } else {
            return $authentication;
        }
    }
}