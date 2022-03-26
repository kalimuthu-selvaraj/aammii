<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Catalog;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\MobikulApiTransformer\Http\Resources\Catalog\HomePage as HomePageResource;

/**
 * HomePage controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class HomePageController extends Controller
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
        $loggedInCustomer = [];
        $token = request()->token;
        
        if ( $token ) {
            $authentication = mobikulApi()->customerAuthentication($token, $this->guard);
            
            if ( $authentication === true ) {
                $loggedInCustomer = auth($this->guard)->user();
            } else {
                return $authentication;
            }
        }
        
        return response()->json(new HomePageResource([
            'customer'      => $loggedInCustomer,
        ]));
    }
}