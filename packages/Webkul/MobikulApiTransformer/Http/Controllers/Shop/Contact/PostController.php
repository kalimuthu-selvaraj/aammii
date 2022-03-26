<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Contact;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\MobikulApiTransformer\Http\Resources\Contact\Post;

/**
 * Post Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PostController extends Controller
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
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'storeId'   => 'required|integer',
            'comment'   => 'required',
            'email'     => 'required|email',
            'name'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'    => false,
                'message'   => $validator->messages(),
            ], 200);
        }
        
        return response()->json(new Post(request()->all()));
    }
}

