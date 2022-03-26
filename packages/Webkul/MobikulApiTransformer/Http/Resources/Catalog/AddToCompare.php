<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;

class AddToCompare extends JsonResource
{
    /**
     * Contains current channel
     *
     * @var string
     */
    protected $channel;

    /**
     * Contains current locale
     *
     * @var string
     */
    protected $localeCode;

    /**
     * Contains response status.
     *
     * @var boolean
     */
    protected $responseStatus = true;

    /**
     * Contains response message.
     *
     * @var string
     */
    protected $responseMessage = '';

    /**
     * Contains compare item id.
     *
     * @var int
     */
    protected $compareItemId = 0;
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->compareRepository = app('Webkul\Velocity\Repositories\VelocityCustomerCompareProductRepository');

        $this->productRepository = app('Webkul\Product\Repositories\ProductRepository');
        
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
        $this->localeCode = request()->input('locale');

        $channel = $this->channelRepository->find($this->channel);
        
        if ( isset($this['customer_id']) && $this['customer_id'] ) {
            $product = $this->productRepository->find($this['productId']);
            if (! $product ) {
                $this->responseStatus = false;
                $this->responseMessage = trans('mobikul-api::app.api.catalog.add-to-compare.error-request');
            }

            $productFlatIds = $product->product_flats->pluck('id')->toArray();

            $compareProduct = $this->compareRepository->where('customer_id', $this['customer_id'])->whereIn('product_flat_id', $productFlatIds)->first();

            if ( $compareProduct ) {
                $this->compareItemId = $compareProduct->id;
                $this->responseMessage = trans('mobikul-api::app.api.catalog.add-to-compare.already-added');
            } else {
                $productFlat = $product->product_flats->where('channel', $channel->code)->where('locale', $this->localeCode)->first();

                if ( $productFlat ) {
                    $result = $this->compareRepository->create([
                        'customer_id'     => $this['customer_id'],
                        'product_flat_id' => $productFlat->id,
                    ]);
    
                    if ( $result ) {
                        $this->compareItemId = $result->id;
                        $this->responseMessage = trans('mobikul-api::app.api.catalog.add-to-compare.success-added-compare');
                    } else {
                        $this->responseStatus = false;
                        $this->responseMessage = trans('mobikul-api::app.api.catalog.add-to-compare.error-request');
                    }
                }
            }
        }
        
        return [
            'success'   => $this->responseStatus,
            'message'   => $this->responseMessage,
            'itemId'    => $this->compareItemId,
        ];
    }
}