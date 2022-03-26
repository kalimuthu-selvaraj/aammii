<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Storage;

class Login extends JsonResource
{
    /**
     * Contains the current cart's item count.
     *
     * @var int
     */
    protected $cartItemCount = 0;

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = $request->toArray();

        $jwtToken = null;
        if (! $jwtToken = auth()->guard('api')->attempt([
            'email'     => $data['username'],
            'password'  => $data['password'],
        ])) {
            return [
                'success'   => false,
                'message'   => trans('mobikul-api::app.api.customer.login.error-username-password'),
            ];
        }

        $customer = auth()->guard('api')->user();
        $customer->token = $jwtToken;
        
        $this->getCartItemCount();
        
        return [
            'success'               => true,
            'message'               => trans('mobikul-api::app.api.customer.login.success-login'),
            'customerName'          => $customer->name,
            'customerEmail'         => $customer->email,
            'token'                 => $customer->token,
            'bannerImage'           => $customer->banner_pic ? Storage::URL($customer->banner_pic) : '',
            'bannerDominantColor'   => $customer->banner_pic ? mobikulApi()->getImageDominantColor($customer->banner_pic) : '#000000',
            'profileImage'          => $customer->profile_pic ? Storage::URL($customer->profile_pic) : '',
            'profileDominantColor'  => $customer->profile_pic ? mobikulApi()->getImageDominantColor($customer->profile_pic) : '#000000',
            'cartCount'             => $this->cartItemCount,
        ];
    }

    /**
     * Get the Item count of current cart
     *
     * @return void
     */
    public function getCartItemCount()
    {
        $cart = Cart::getCart();
        if ( $cart ) {
            $this->cartItemCount = count($cart->items);
        }
    }
}

