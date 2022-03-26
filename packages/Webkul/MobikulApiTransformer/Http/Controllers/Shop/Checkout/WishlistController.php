<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Checkout;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Validator;
use Webkul\MobikulApiTransformer\Http\Resources\Checkout\Wishlist;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\WishlistShare;

/**
 * Wishlist Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class WishlistController extends Controller
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
    public function __construct()   {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);
        
        $this->middleware('auth:' . $this->guard, ['only' => ['index', 'share']]);

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
            'token'         => 'required',
            'storeId'       => 'required|integer',
            'itemId'        => 'required|integer'
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
            $cart = Cart::getCart();
            if (! $cart ) {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-empty-cart'),
                ], 200);
            }
            
            if ( Cart::moveToWishlist($data['itemId']) ) {

                Cart::collectTotals();
    
                $cart = Cart::getCart();
                
                return response()->json($cart ? new WishList($cart) : [
                    'success'   => true,
                    'message'   => trans('mobikul-api::app.api.checkout.wishlist-from-cart.success-move-to-wishlist'),
                    'cartCount' => 0,
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.checkout.wishlist-from-cart.error-invalid-item-id'),
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
    public function share(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'         => 'required',
            'emails'        => 'required',
            'message'       => 'required',
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
            
            return response()->json(new WishlistShare($data));
        } else {
            return $authentication;
        }
    }
}