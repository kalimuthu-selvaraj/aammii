<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Checkout;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Event;
use Webkul\Checkout\Facades\Cart;
use Webkul\MobikulApiTransformer\Http\Resources\Checkout\CartDetail;
use Webkul\MobikulApiTransformer\Http\Resources\Checkout\RemoveCartItem;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Customer\Repositories\WishlistRepository;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Checkout\Contracts\Cart as CartModel;

/**
 * Cart Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CartController extends Controller
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
     * Contains cart data.
     *
     * @var array
    */
    protected $cartData = [];

    /**
     * Contains cart's virtual status.
     *
     * @var boolean
    */
    protected $isVirtual = true;

    /**
     * Contains cart's item data for update.
     *
     * @var array
    */
    protected $updateCart = [];

    /**
     * Contains wishlist's item move count.
     *
     * @var int
    */
    protected $wishlistMoveCount = 0;

    /**
     * ProductRepository Object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * WishlistRepository Object
     *
     * @var \Webkul\Customer\Repositories\WishlistRepository
     */
    protected $wishlistRepository;

    /**
     * CartRepository Object
     *
     * @var \Webkul\Checkout\Repositories\CartRepository $cartRepository
     */
    protected $cartRepository;
    
    /**
     * Controller instance
     *
     * @param \Webkul\Product\Repositories\ProductRepository    $productRepository
     * @param \Webkul\Customer\Repositories\WishlistRepository  $wishlistRepository
     * @param \Webkul\Checkout\Repositories\CartRepository      $cartRepository
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        WishlistRepository $wishlistRepository,
        CartRepository $cartRepository
    )   {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');

        $this->productRepository = $productRepository;

        $this->wishlistRepository = $wishlistRepository;

        $this->cartRepository = $cartRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'productId'     => 'required|integer',
            'quoteId'       => 'required|integer',
            'storeId'       => 'required|integer',
            'token'         => 'required',
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

        if ( $authentication === true || $token == 0 ) {
            $product =  $this->productRepository->find($data['productId']);
            
            if (! $product ) {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.checkout.add-to-cart.error-invalid-product'),
                ], 200);
            }
            
            try {
                $this->cartData = [
                    'is_buy_now'    => 0,
                    'product_id'    => $product->id,
                    'quantity'      => isset($data['qty']) ? (int) $data['qty']: 1,
                ];
                
                if ( isset($data['params']) && $data['params'] ) {
                    $params = json_decode($data['params'], true);
                    $this->cartData = array_merge($this->cartData, $params);
                    
                    // Configurable Product
                    if ( $product->type == 'configurable' && isset($this->cartData['super_attribute']) && gettype($this->cartData['super_attribute']) == 'string'  ) {
                        $this->cartData['super_attribute'] = json_decode($this->cartData['super_attribute'], true);
                    }

                    // Download Product
                    if ( $product->type == 'downloadable' && isset($this->cartData['links']) && gettype($this->cartData['links']) == 'string' ) {
                        $this->cartData['links'] = json_decode($this->cartData['links'], true);
                    }
                }
                
                Event::dispatch('checkout.cart.item.add.before', $product->id);

                $result = Cart::addProduct($product->id, $this->cartData);

                if ( is_array($result) && isset($result['warning']) ) {
                    return response()->json([
                        'success'   => false,
                        'message'   => $result['warning']
                    ], 400);
                }

                if ($result instanceof CartModel) {
                    if ($customer = auth()->guard($this->guard)->user()) {
                        $this->wishlistRepository->deleteWhere([
                            'product_id'    => $product->id,
                            'customer_id'   => $customer->id
                        ]);
                    }

                    Event::dispatch('checkout.cart.item.add.after', $result);

                    Cart::collectTotals();

                    $cart = Cart::getCart();
                    
                    foreach ($cart->items as $item) {
                        if ( $item->product->type !== 'virtual' ) {
                            $this->isVirtual = false;
                        }
                    }

                    return response()->json([
                        'success'       => true,
                        'message'       => trans('mobikul-api::app.api.checkout.add-to-cart.success-add-to-cart', [
                            'product_name' => $product->name
                            ]),
                        'cartCount'     => count($cart->items),
                        'isVirtual'     => $this->isVirtual,
                        'quoteId'       => 0 //need to discussion
                    ]);
                }
            } catch(\Exception $e) {
                return response()->json([
                    'success'   => false,
                    'message'   => $e->getMessage(),
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
    public function view(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'quoteId'       => 'required|integer',
            'storeId'       => 'required|integer',
            'token'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);
        if ( $authentication === true || $token == 0) {
            $cart = Cart::getCart();
            if (! $cart ) {

                return response()->json([
                    'success'   => true,
                    'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-empty-cart'),
                ], 200);
            }
            $params = [
                'cart'  => $cart
            ];

            if ( auth()->guard($this->guard)->check() ) {
                $params['customer'] = auth()->guard($this->guard)->user();
            }
            
            return response()->json(new CartDetail($params));
        } else {

            return $authentication;
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function empty(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'quoteId'       => 'required|integer',
            'storeId'       => 'required|integer',
            'token'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);
        if ( $authentication === true || $token == 0 ) {
            $cart = Cart::getCart();

            if ( $cart ) {
                $removeAllItems = $this->cartRepository->delete($cart->id);

                if ( $removeAllItems ) {
                    return response()->json([
                        'success'   => true,
                        'message'   => trans('mobikul-api::app.api.checkout.remove-cart-item.success-cart-empty'),
                    ], 200);
                } else {
                    return response()->json([
                        'success'   => false,
                        'message'   => trans('mobikul-api::app.api.checkout.remove-cart-item.error-cart-empty'),
                    ], 200);
                }
            } else {
                return response()->json([
                    'success'   => true,
                    'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-empty-cart'),
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
    public function update(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'quoteId'       => 'required|integer',
            'storeId'       => 'required|integer',
            'token'         => 'required',
            'itemIds'       => 'required',
            'itemQtys'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => json_encode($validator->messages()),
            ], 200);
        }

        $data   = request()->all();
        if ( gettype($data['itemIds']) == 'string' ) {
            $data['itemIds'] = json_decode($data['itemIds'], true);
            $data['itemQtys'] = json_decode($data['itemQtys'], true);
        } else {
            $data['itemIds'] = $data['itemIds'];
            $data['itemQtys'] = $data['itemQtys'];
        }
        $token  = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);
        if ( $authentication === true || $token == 0 )
        {
            foreach ($data['itemIds'] as $key => $itemId) {
                $updateCart['qty'][$itemId] = isset($data['itemQtys'][$key]) ? $data['itemQtys'][$key] : 1;
            }

            try {
                /**
                 * Authour : kaalee
                 * Modified for mobile quantity update and create new function main class
                 */
                $result = Cart::updateMobileItems($updateCart);

                if (! $result) {
                    return response()->json([
                        'success'   => false,
                        'message'   => session()->get('warning') ?? session()->get('error')
                    ], 200);
                }

                $cart = Cart::getCart();

                return response()->json([
                    'success'   => true,
                    'message'   => trans('mobikul-api::app.api.checkout.update-cart.success-update-cart'),
                    'cartCount' => (! is_null($cart)) ? $cart->items->count() : 0 ,
                ], 200);
            } catch(\Exception $e) {
                return response()->json([
                    'success'   => false,
                    'message'   => $e->getMessage()
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
    public function remove(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'quoteId'       => 'required|integer',
            'storeId'       => 'required|integer',
            "itemId"        => "required|integer",
            'token'         => 'required',
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
        if ( $authentication === true || $token == 0 ) {
            
            $cart = Cart::getCart();
            if ( ! $cart ) {
                return response()->json([
                    'success'   => true,
                    'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-empty-cart'),
                ], 200);
            }
            
            $cartItem = null;
            $cartItem = $cart->items->where('id', $data['itemId'])->first();
            if ( ! $cartItem) {
                return response()->json([
                    'success'   => true,
                    'message'   => trans('mobikul-api::app.api.checkout.wishlist-from-cart.error-invalid-item-id'),
                ], 200);
            }   
            
            return response()->json(new RemoveCartItem($data));
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
        $validator = Validator::make(request()->all(), [
            'token'         => 'required',
            'storeId'       => 'required|integer',
            'websiteId'     => 'integer',
            'itemData'      => 'required',
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
            if ( gettype($data['itemData']) == 'string') {
                $itemData = json_decode($data['itemData'], true);
                
                foreach ($itemData as $wishlistItemId) {
                    $wishlistItem = $this->wishlistRepository->findOneWhere([
                        'id'            => $wishlistItemId,
                        'customer_id'   => auth()->guard($this->guard)->user()->id
                    ]);

                    if ( $wishlistItem ) {
                        if ( Cart::moveToCart($wishlistItem) ) {
                            $this->wishlistMoveCount += 1;
                        }
                    } 
                }
            }
            
            return response()->json([
                'success'   => true,
                'message'   => trans('mobikul-api::app.api.sales.all-to-cart.success-move-to-cart', [
                    'count' => $this->wishlistMoveCount
                ]),
            ], 200);
        } else {
            return $authentication;
        }
    }
}