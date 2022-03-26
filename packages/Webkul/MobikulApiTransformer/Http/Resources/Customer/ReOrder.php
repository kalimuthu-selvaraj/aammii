<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Event;

class ReOrder extends JsonResource
{
    /**
     * Contains cart's item success count.
     *
     * @var int
     */
    protected $addCount = 0;

    /**
     * Contains the current cart's item count.
     *
     * @var int
     */
    protected $cartItemCount = 0;

    /**
     * Contains cart item detail.
     *
     * @var array
     */
    protected $cartItemDetail = [];

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
        $order = $this['order'];
        
        foreach ($order->items as $item) {
            try {
                $isConfigurable = false;
                if ($item->type == 'configurable') {
                    $isConfigurable = true;
                }

                $this->cartItemDetail = [
                    'product_id'        => $item->product_id,
                    'quantity'          => $item->qty_ordered,
                    'is_configurable'   => $isConfigurable
                ];
                
                Event::dispatch('checkout.cart.item.add.before', $item->product_id);

                $result = Cart::addProduct($item->product_id, $this->cartItemDetail);

                if (! $result) {
                    return [
                        'success'   => false,
                        'message'   => session()->get('warning') ?? session()->get('error')
                    ];
                } else {
                    $this->addCount += 1;
                }

                Event::dispatch('checkout.cart.item.add.after', $result);

                Cart::collectTotals();
            } catch(\Exception $e) {
                return [
                    'success'   => false,
                    'message'   => $e->getMessage()
                ];
            }
        }
        
        $this->getCartItemCount();

        return [
            'success'   => true,
            'message'   => trans('mobikul-api::app.api.customer.order-list.success-reorder'),
            'cartCount' => $this->cartItemCount
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