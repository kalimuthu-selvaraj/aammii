<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Catalog;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\MobikulApiTransformer\Http\Resources\Catalog\ProductShare;

/**
 * ProductShare Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductShareController extends Controller
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
     * Controller instance
     *
     * @return void
     */
    public function __construct()
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(),[
            'productId'         => 'required|integer',
            'customerName'      => 'required',
            'customerEmail'     => 'required',
            "recipientName.*"   => "required|string",
            "recipientEmail.*"  => "required|string",
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $data = request()->all();
        $data['recipientName'] = explode(",", $data['recipientName']);
        $data['recipientEmail'] = explode(",", $data['recipientEmail']);

        return response()->json(new ProductShare($data));
    }
}