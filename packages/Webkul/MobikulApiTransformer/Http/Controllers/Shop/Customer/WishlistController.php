<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Customer;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\MobikulApiTransformer\Http\Resources\Customer\Wishlist;
use Webkul\Customer\Repositories\WishlistRepository;
use Webkul\Checkout\Facades\Cart;

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
     * WishlistRepository Object
     *
     * @var \Webkul\Customer\Repositories\WishlistRepository
     */
    protected $wishlistRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\WishlistRepository $wishlistRepository
     * @return void
    */
    public function __construct(
        WishlistRepository $wishlistRepository
    )   {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('auth:' . $this->guard, ['only' => ['list', 'remove', 'moveToCart']]);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');

        $this->wishlistRepository = $wishlistRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token' => 'required',
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

            $wishList = $this->wishlistRepository->findWhere([
                'customer_id'   => $loggedCustomer->id,
                'channel_id'    => $data['storeId']
            ]);

            if ( $wishList )
                return response()->json(new Wishlist([
                    'customer'  => $loggedCustomer,
                    'wishlist'  => $wishList
                ]));
            else
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.customer.wishlist.empty-wishlist'),
                ], 200);
        } else {
            return $authentication;
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(),[
            'token'         => 'required',
            'storeId'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => json_encode($validator->messages()),
            ], 200);
        }

        $data = request()->all();
        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);

        if ( $authentication === true ) {
            $params = [
                'customer_id'   => auth($this->guard)->user()->id
            ];

            if ( isset($data['itemId']) && $data['itemId'] ) {
                $params['id']   = $data['itemId'];
            }

            $wishlistItem = $this->wishlistRepository->findOneWhere($params);

            if ( $wishlistItem ) {
                $wishlistItem->delete();

                return response()->json([
                    'success'   => true,
                    'message'   => trans('mobikul-api::app.api.customer.remove-from-wishlist.success-removed'),
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.customer.remove-from-wishlist.no-item-found'),
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
    public function moveToCart(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(),[
            'token'     => 'required',
            'storeId'   => 'required',
            'itemId'    => 'required|numeric'
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
            $wishlistItem = $this->wishlistRepository->findOneWhere([
                'id'            => $data['itemId'],
                'customer_id'   => auth($this->guard)->user()->id,
            ]);

            if ( $wishlistItem ) {
                $result = Cart::moveToCart($wishlistItem);

                if ( $result ) {
                    $wishlistItem->delete();

                    Cart::collectTotals();

                    $cart = Cart::getCart();

                    return response()->json([
                        'success'   => true,
                        'message'   => trans('mobikul-api::app.api.customer.wishlist-to-cart.success-moved'),
                        'cartCount' => $cart->items->count()
                    ], 200);
                }
            } else {
                return response()->json([
                    'success'    => false,
                    'message'   => trans('mobikul-api::app.api.customer.wishlist-to-cart.invalid-params'),
                ], 200);
            }
        } else {
            return $authentication;
        }
    }
}