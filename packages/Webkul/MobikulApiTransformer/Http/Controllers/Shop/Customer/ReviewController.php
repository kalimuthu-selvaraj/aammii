<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Customer;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductReviewRepository;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\ReviewDetails;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\ReviewList;

/**
 * Review Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ReviewController extends Controller
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
     * ProductRepository
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * ProductReviewRepository
     *
     * @var \Webkul\Product\Repositories\ProductReviewRepository
     */
    protected $productReviewRepository;
    
    /**
     * Controller instance
     *
     * @param \Webkul\Product\Repositories\ProductRepository    $productRepository
     * @param \Webkul\Product\Repositories\ProductReviewRepository  $productReviewRepository
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductReviewRepository $productReviewRepository
        )
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('auth:' . $this->guard, ['only' => ['save', 'list', 'view']]);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');

        $this->productRepository = $productRepository;

        $this->productReviewRepository = $productReviewRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'detail'        => 'required',
            'title'         => 'required',
            'productId'     => 'required|numeric',
            'token'         => 'required',
            'ratings'       => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $params = request()->all();
        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true || $token == 0 ) {
            $loggedCustomer = [];
            if ( auth($this->guard)->check() ) {
                $loggedCustomer = auth($this->guard)->user();
            } else if (! core()->getConfigData('catalog.products.review.guest_review') ) {
                return response()->json([
                    'status'    => false,
                    'message'   => trans('mobikul-api::app.api.customer.review.error-guest-review'),
                ], 200);
            }
            
            $params['rating']     = $params['ratings'];
            $params['comment']    = $params['detail'];
            unset($params['ratings']);
            unset($params['detail']);

            if ( $params['rating'] > 5 ) {
                return response()->json([
                    'status'    => false,
                    'message'   => trans('mobikul-api::app.api.customer.review.error-rating'),
                ], 200);
            } else {
                $product = $this->productRepository->find($params['productId']);
                
                if (! $product ) {
                    return response()->json([
                        'success' => false,
                        'message' => trans('mobikul-api::app.api.customer.review.error-no-product'),
                    ], 200);
                }
                $data = array_merge($params, [
                    'customer_id'   => isset($loggedCustomer->id) ? $loggedCustomer->id : 0,
                    'name'          => isset($loggedCustomer->name) ? $loggedCustomer->name : request()->input('name'),
                    'status'        => 'pending',
                    'product_id'    => $params['productId']
                ]);
                
                $review = $this->productReviewRepository->create($data);
                
                if ( $review ) {
                    return response()->json([
                        'success' => true,
                        'message' => trans('mobikul-api::app.api.customer.review.success-save-review'),
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => trans('mobikul-api::app.api.customer.review.error-review-create'),
                    ], 200);
                }
            }
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'storeId'       => 'required',
            'token'         => 'required',
            'reviewId'      => 'required|numeric',
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
            
            $review = $this->productReviewRepository->with('product')->findOneWhere([
                'id'            => $data['reviewId'],
                'customer_id'   => $loggedCustomer->id
                ]);

            if ( $review ) {
                return response()->json(new ReviewDetails($review));
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.customer.review.error-no-review'),
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
    public function list(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'storeId'   => 'required',
            'token'     => 'required'
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
            $data['customer_id'] = auth($this->guard)->user()->id;
            return response()->json(new ReviewList($data));
        } else {
            return $authentication;
        }
    }
}