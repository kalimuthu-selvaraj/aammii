<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Catalog;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Rating Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class RatingController extends Controller
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
        return response()->json([
            'success'           => true,
            'message'           => "",
            'ratingFormData'    => [
                [
                    'id'            => 1,
                    'name'          => trans('shop::app.reviews.rating-reviews'),
                    'value'         => [ 1, 2, 3, 4, 5],
                ]
            ]
        ]);
    }
}