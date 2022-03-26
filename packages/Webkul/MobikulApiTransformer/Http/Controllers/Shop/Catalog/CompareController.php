<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Catalog;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\Velocity\Repositories\VelocityCustomerCompareProductRepository;
use Webkul\MobikulApiTransformer\Http\Resources\Catalog\CompareList;
use Webkul\MobikulApiTransformer\Http\Resources\Catalog\AddToCompare;

/**
 * Compare Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CompareController extends Controller
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
     * VelocityCustomerCompareProduct Repository
     *
     * @var \Webkul\Velocity\Repositories\VelocityCustomerCompareProductRepository $compareRepository
     */
    protected $compareRepository;
    
    /**
     * Controller instance
     *
     * @param \Webkul\Velocity\Repositories\VelocityCustomerCompareProductRepository $compareRepository
     * @return void
     */
    public function __construct(
        VelocityCustomerCompareProductRepository $compareRepository
    )
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');

        $this->compareRepository = $compareRepository;
    }
    
    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): ?JsonResponse
    {
        $data = request()->all();
        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true ) {
            $data['customer_id'] = auth()->guard($this->guard)->user()->id;
            
            return response()->json(new CompareList($data));
        } else {
            return $authentication;
        }
    }
    
    /**
     * add product to To CompareList.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'productId'     => 'required|integer',
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
            $data['customer_id'] = auth()->guard($this->guard)->user()->id;
            
            return response()->json(new AddToCompare($data));
        } else {
            return $authentication;
        }
    }
    
    /**
     * Remove compare product record.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'productId'     => 'required|integer',
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
            $customer_id = auth()->guard($this->guard)->user()->id;

            $compareItem = $this->compareRepository->findOneWhere([
                'customer_id'       => $customer_id,
                'product_flat_id'   => $data['productId'],
            ]);
            
            if ( $compareItem ) {
                if ( $this->compareRepository->delete($compareItem->id) ) {
                    return response()->json([
                        'success'   => true,
                        'message'   => trans('mobikul-api::app.api.catalog.remove-from-compare.success-removed'),
                    ]);
                }
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.catalog.remove-from-compare.error-product-not-found'),
                ]);
            }
        } else {
            return $authentication;
        }
    }
}