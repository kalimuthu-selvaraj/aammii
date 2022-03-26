<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderList extends JsonResource
{
    /**
     * Contains current channel
     *
     * @var string
     */
    protected $channel;

    /**
     * Contains current currency
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * Contains reorder value.
     *
     * @var boolean
     */
    protected $canReorder = true;

    /**
     * Contains order list.
     *
     * @var array
     */
    protected $orderLists = [];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->orderRepository = app('Webkul\Sales\Repositories\OrderRepository');

        $this->productInventoryRepository = app('Webkul\Product\Repositories\ProductInventoryRepository');
        
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
        $this->channel = request()->input('storeId');
        $this->currencyCode = request()->input('currency');
        
        $customer = $this['customer'];
        $params = [
            'customer_id'   => $customer->id,
            'channel_id'    => $this->channel,
            'limit'         => isset($this['limit']) ? $this['limit'] : core()->getConfigData('mobikul.mobikul.basicinformation.current_page_size'),
        ];

        $request->merge(['page' => (isset($this['pageNumber']) && $this['pageNumber']) ? $this['pageNumber'] : 1]);
        
        $orders = $this->orderRepository->scopeQuery(function($query) use ($params) {
            return $query->distinct()  
                            ->addSelect('orders.*')
                            ->where('orders.customer_id', $params['customer_id'])
                            ->orderBy('orders.id', 'desc');
        })->paginate($params['limit']);
        
        foreach ($orders as $order) {
            $saleable = true;
            foreach ($order->items as $item) {
                if (! $item->product->getTypeInstance()->isStockable()) {
                    $saleable = false;
                }

                $productInventory = $this->productInventoryRepository->findOneByField('product_id', $item->product->id);
                
                if ( isset($productInventory['qty']) && $productInventory['qty'] == 0) {
                    $this->canReorder = false;
                    break;
                }
            }

            $productBaseImage = productimage()->getProductBaseImage($item->product);

            $shipping_address = new \stdClass();
            if ( $order->shipping_address ) {
                $shipping_address = $order->shipping_address->toArray();
            }

            $billing_address = new \stdClass();
            if ( $order->billing_address ) {
                $billing_address = $order->billing_address->toArray();
            }

            $this->orderLists[] = [
                'id'                => $order->id,
                'date'              => $order->created_at,
                'state'             => $order->status,
                'status'            => $order->statusLabel,
                'is_saleable'       => $saleable,
                'ship_to'           => $order->customer_first_name . ' ' .  $order->customer_last_name,
                'customer_email'    => $order->customer_email,
                'shipping_address'  => $shipping_address,
                'billing_address'   => $billing_address,
                'order_id'          => $order->increment_id,
                'item_count'        => $order->items->count(),
                'order_total'       => core()->formatPrice((float)$order->grand_total, core()->getCurrentCurrencyCode()),
                'item_image_url'    => $productBaseImage['medium_image_url'],
                'statusColorCode'   => "#d5d5d5", //need to discussion
                'canReorder'        => $this->canReorder
            ];
        }

        return [
            'success'       => true,
            'message'       => trans('mobikul-api::app.api.customer.order-list.success-order-list'),
            'totalCount'    => $orders->total(),
            'orderList'     => $this->orderLists,
            'eTag'          => '02002b84642b2671e91c9b9481b626d0', //pending
        ];
    }
}