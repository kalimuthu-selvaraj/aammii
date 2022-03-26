<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;

class WishList extends JsonResource
{
    /**
     * Contains the API request parameters.
     *
     * @var array
     */
    protected $requestParams = [];

    /**
     * Contains the wishlist item id.
     *
     * @var int
     */
    protected $wishlistItemId = 0;
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->wishlistRepository = app('Webkul\Customer\Repositories\WishlistRepository');

        $this->productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');
        
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
        $this->localeCode = request()->input('locale');

        $channel = $this->channelRepository->find($this->channel);

        $productFlat = $this->productFlatRepository->findOneWhere([
            'product_id'    => $this['productId'],
            'channel'       => $channel->code,
            'locale'        => $this->localeCode,
        ]);

        if ( $productFlat ) {
            $product = $productFlat->product;
            $customer = $this['customer'];

            $this->requestParams = $this['params'] ?? ['product_id' => $product->id];
            
            $wishlistItems = $this->wishlistRepository->findWhere([
                'customer_id' => $customer->id,
                'product_id'  => $product->id,
            ]);

            if ( $wishlistItems ) {
                foreach ($wishlistItems as $wishlistItem) {
                    $options = $wishlistItem->item_options;
        
                    if (! $options) {
                        $options = ['product_id' => $wishlistItem->product_id];
                    }
        
                    if ($product->getTypeInstance()->compareOptions($this->requestParams, $options)) {
                        $this->wishlistItemId = $wishlistItem->id;
                    }
                }
            }
            
            if (! $this->wishlistItemId ) {
                $wishlistItem = $this->wishlistRepository->create([
                    'channel_id'  => $this->channel,
                    'customer_id' => $customer->id,
                    'product_id'  => $product->id,
                    'additional'  => $this->requestParams,
                ]);

                if ( $wishlistItem ) {
                    $this->wishlistItemId = $wishlistItem->id;
                }
            }
        }
        
        return [
            'success'   => true,
            'message'   => $this->wishlistItemId ? trans('mobikul-api::app.api.catalog.add-to-wishlist.success-added') : trans('mobikul-api::app.api.catalog.add-to-wishlist.error-added'),
            'itemId'    => $this->wishlistItemId,
        ];
    }
}