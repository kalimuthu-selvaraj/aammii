<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Mail;
use Webkul\MobikulApiTransformer\Mail\WishListShareEmail;

class WishlistShare extends JsonResource
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
     * Contains current locale
     *
     * @var string
     */
    protected $localeCode;

    /**
     * Contains product array.
     *
     * @var array
     */
    protected $products = [];
    
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

        $customer = $this['customer'];
        
        $wishlistProducts = $this->wishlistRepository->findByField('customer_id', $customer->id);
        
        foreach ($wishlistProducts as $wishlist) {
            $productFlat = $this->productFlatRepository->findOneWhere([
                'product_id'    => $wishlist->product_id,
                'channel'       => $channel->code,
                'locale'        => $this->localeCode
            ]);

            if ( $productFlat ) {
                $this->products[$wishlist->product_id] = $productFlat;
            }
        }

        if (! empty($this->products) ) {
            $data = [
                'productData'   => $this->products,
                'storeName'     => $channel->name,
                'customerEmail' => $customer->email,
                'customerName'  => $customer->name
            ];
            
            try {
                foreach (explode(',', $request->emails) as $email) {
                    $data['recipientEmail'] = $email;
                    Mail::queue(new WishListShareEmail($data));
                }
                return [
                    'success'   => true,
                    'message'   => trans('mobikul-api::app.api.sales.share-wishlist.success-shared-wishlist'),
                ];
            } catch (\Exception $e) {
                return [
                    'success'   => false,
                    'message'   => $e->getMessage(),
                ];
            }
        } else {
            return [
                'success'   => false,
                'message'   => trans('mobikul-api::app.api.sales.share-wishlist.error-empty-wishlist'),
            ];
        }
    }
}