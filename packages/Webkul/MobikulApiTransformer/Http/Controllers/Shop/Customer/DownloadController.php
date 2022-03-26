<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Customer;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\DownloadList;

/**
 * Download Controller 
 *
 * @author  Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2018 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class DownloadController extends Controller
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('auth:' . $this->guard, ['only' => ['list']]);

        $this->middleware('validateAPIHeader');
        
        $this->_config = request('_config');
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'storeId'   => 'required|integer',
            'token'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'    => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $data = request()->all();
        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true ) {
            $data['customer_id'] = auth()->guard($this->guard)->user()->id;

            return response()->json(new DownloadList($data));
        } else {

            return $authentication;
        }
    }
}
