<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Catalog;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\MobikulApiTransformer\Http\Resources\Catalog\ProductPage;
use Webkul\MobikulApiTransformer\Http\Resources\Catalog\ProductCollection;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\DownloadLinkSample;
use Webkul\Velocity\Repositories\VelocityCustomerCompareProductRepository as CustomerCompareProductRepository;

/**
 * ProductPage controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductPageController extends Controller
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
     * currencyRepository object
     *
     * @var Object
     */
    protected $currencyRepository;

    /**
     * compareProductsRepository object
     *
     * @var \Webkul\Velocity\Repositories\VelocityCustomerCompareProductRepository $compareProductsRepository
     */
    protected $compareProductsRepository;

    /**
     * Controller instance
     * 
     * @param \Webkul\Velocity\Repositories\VelocityCustomerCompareProductRepository     $compareProductsRepository
     *
     * @return void
     */
    public function __construct(
        CustomerCompareProductRepository $compareProductsRepository
    )   {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('auth:' . $this->guard, ['only' => ['downloadlinksample']]);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');

        $this->compareProductsRepository = $compareProductsRepository;
    }

    /**
     * Get a product page resource response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): ?JsonResponse
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
        if ( $token ) {
            $token = request()->token;
            $authentication = mobikulApi()->customerAuthentication($token, $this->guard);
            
            if ( $authentication === true ) {
                $data['customer_id'] = auth()->guard($this->guard)->user()->id;
            } else {
                return $authentication;
            }
        }

        return response()->json(new ProductPage($data));
    }

    /**
     * Get a product collection resource response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'type'      => 'required|string',
            'id'        => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $data = request()->all();
        $token = request()->token;
        if ( $token ) {
            $token = request()->token;
            $authentication = mobikulApi()->customerAuthentication($token, $this->guard);
            
            if ( $authentication === true ) {
                $data['customer_id'] = auth()->guard($this->guard)->user()->id;
            } else {
                return $authentication;
            }
        }
        
        return response()->json(new ProductCollection($data));
    }

    /**
     * Get a product collection resource response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadLinkSample(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'     => 'required',
            'storeId'   => 'required|numeric',
            'linkId'    => 'required',
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

            return response()->json(new DownloadLinkSample($data));
        } else {
            return $authentication;
        }
    }
}