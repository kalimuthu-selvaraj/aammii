<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\ProductAlert;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\PriceDropAlert\Repositories\PriceDropSubscriptionRepository;

/**
 * Price Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PriceController extends Controller
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
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;
    
    /**
     * PriceDropSubscriptionRepository object
     *
     * @var \Webkul\PriceDropAlert\Repositories\PriceDropSubscriptionRepository
     */
    protected $priceDropSubscriptionRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\PriceDropAlert\Repositories\PriceDropSubscriptionRepository  $priceDropSubscriptionRepository
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        PriceDropSubscriptionRepository $priceDropSubscriptionRepository
    )   {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');

        $this->productRepository = $productRepository;
        
        $this->priceDropSubscriptionRepository = $priceDropSubscriptionRepository;
    }
    
    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'     => 'required',
            'productId' => 'required|integer',
        ]);
        
        if ( $validator->fails() ) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $data = request()->all();
        
        $product = $this->productRepository->findOrFail($data['productId']);
        if (! isset($product->id)) {
            return response()->json([
                'success'   => false,
                'message'   => trans('mobikul-api::app.api.product-alert.price.invalid-product'),
            ], 200);
        }

        $data = request()->all();
        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true ) {
            $loggedCustomer = auth()->guard($this->guard)->user();

            $priceDropSubscriber = $this->priceDropSubscriptionRepository->findOneWhere([
                'product_id'    => $data['productId'],
                'email'         => $loggedCustomer->email
            ]);

            if ( isset($priceDropSubscriber->id)) {
                if ( $priceDropSubscriber->status ) {
                    $data['status'] = 0;
                } else {
                    $data['status'] = 1;
                }
                
                $priceDropSubscriber = $this->priceDropSubscriptionRepository->update($data, $priceDropSubscriber->id);

                if ( $priceDropSubscriber->status ) {
                    return response()->json([
                        'success'   => true,
                        'message'   => trans('mobikul-api::app.api.product-alert.price.subscribe-success'),
                    ], 200);
                } else {
                    return response()->json([
                        'success'   => true,
                        'message'   => trans('mobikul-api::app.api.product-alert.price.un-subscribe-success'),
                    ], 200);
                }
            } else {
                $priceDropSubscriber = $this->priceDropSubscriptionRepository->create([
                    'product_id'    => $data['productId'],
                    'email'         => $loggedCustomer->email,
                ]);

                if ( isset($priceDropSubscriber->id)) {
                    return response()->json([
                        'success'   => true,
                        'message'   => trans('mobikul-api::app.api.product-alert.price.subscribe-success'),
                    ], 200);
                }
            }
        } else {
            return $authentication;
        }
    }
}

