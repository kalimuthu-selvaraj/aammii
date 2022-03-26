<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Admin\Listeners\Product;

class Wishlist extends JsonResource
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
     * Contains product's availability.
     *
     * @var string
     */
    protected $availability = 'Out Of Stock';
    
    /**
     * Contains product's condition (new/used).
     *
     * @var boolean
     */
    protected $isNew = 'false';

    /**
     * Contains product's avarage rating.
     *
     * @var float
     */
    protected $productAverageRating = 0.0;

    /**
     * Contains total product's review count.
     *
     * @var int
     */
    protected $productReviewCount = 0;

    /**
     * Contains wishlist items.
     *
     * @var array
     */
    protected $wishlist = [];

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');
        
        $this->productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');

        $this->configurableOptionHelper   =   app('Webkul\Product\Helpers\ConfigurableOption');

        $this->productTypeHelper = app('Webkul\Product\Helpers\ProductType');

        $this->reviewHelper = app('Webkul\Product\Helpers\Review');
        
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

        $wishlistProducts = $this['wishlist'];
        
        foreach ($wishlistProducts as $key => $wishlistProduct) {
            $productFlat = $this->productFlatRepository->findOneWhere([
                'product_id'    => $wishlistProduct->product_id,
                'channel'       => $channel->code,
                'locale'        => $this->localeCode,
            ]);

            if ( $productFlat ) {
                $product = $productFlat->product;

                $productVarients = new \stdClass();
                if ( $product->type == 'configurable' && $this->productTypeHelper->hasVariants($product->type)) {
                    $productVarients = $this->configurableOptionHelper->getConfigurationConfig($product);
                    $productVarients['index'] = json_encode($productVarients['index']);
                }
                
                if ( $product->type == 'bundle' ) {
                    $productVarients = app('Webkul\Product\Helpers\BundleOption')->getBundleConfig($product);
                }

                if ( $product->type == 'grouped' ) {
                    $productVarients = $product->grouped_products;
                }

                if ( $product->type == 'downloadable' ) {
                    if ( $product->downloadable_samples->count() ) {
                        $productVarients->samples = $product->downloadable_samples;
                    }
                    if ( $product->downloadable_links->count() ) {
                        $productVarients->attributes = $product->downloadable_links;
                    }
                }
                
                if ( $product->new ) {
                    $this->isNew = true;
                }

                $this->productReviewCount = $this->reviewHelper->getTotalReviews($product);
                $this->productAverageRating = $this->reviewHelper->getAverageRating($product);
                
                if ( $product->getTypeInstance()->haveSufficientQuantity(1) ) {
                    $this->availability = 'In stock';
                }

                $productBaseImage = productimage()->getProductBaseImage($product);
        
                $productPrice = mobikulApi()->getProductPrice($product);
                
                $this->wishlist[] = [
                    'id'                    => $wishlistProduct->id,
                    'sku'                   => $product->sku,
                    'qty'                   => 1,
                    'name'                  => $product->name,
                    'productId'             => $product->id,
                    'thumbNail'             => $productBaseImage['medium_image_url'],
                    'dominantColor'         => mobikulApi()->getImageDominantColor($productBaseImage['medium_image_url']),
                    'description'           => $productFlat->description,
                    'shortDescription'      => $productFlat->short_description,
                    'options'               => [],
                    'configurableData'      => $productVarients,
                    'isInWishlist'          => true,
                    'wishlistItemId'        => $wishlistProduct->id,
                    'typeId'                => $product->type,
                    'reviewCount'           => $this->productReviewCount,
                    'rating'                => $this->productAverageRating,
                            
                    'price'                 => (float) $productPrice['price'],
                    'formattedPrice'            => (string) core()->formatPrice($productPrice['price'], core()->getBaseCurrencyCode()),

                    'finalPrice'            => (float) core()->convertPrice($productPrice['price'], $this->currencyCode),
                    'formattedFinalPrice'       => (string) core()->currency($productPrice['price'], $this->currencyCode),

                    'specialPrice'              => $product->getTypeInstance()->haveSpecialPrice() ? (float) $product->getTypeInstance()->getSpecialPrice() : 0,
                    'formatedSpecialPrice'      => $product->getTypeInstance()->haveSpecialPrice() ? core()->formatPrice($product->getTypeInstance()->getSpecialPrice(), core()->getBaseCurrencyCode()) : '',
                    
                    'convertedSpecialPrice'     => $product->getTypeInstance()->haveSpecialPrice() ? core()->convertPrice($product->getTypeInstance()->getSpecialPrice(),$this->currencyCode) : 0,
                    'formatedConvertedSpecialPrice'      => $product->getTypeInstance()->haveSpecialPrice() ? core()->formatPrice(core()->convertPrice($product->getTypeInstance()->getSpecialPrice(), $this->currencyCode), $this->currencyCode) : '',
                    
                    'hasRequiredOptions'    => false,
                    'isInRange'             => false,
                    'minAddToCartQty'       => 1,
                    'isNew'                 => $this->isNew,
                    'isAvailable'           => $product->getTypeInstance()->isSaleable(),
                    'availability'          => $this->availability,
                ];
            }
        }

        return [
            'success'       => true,
            'message'       => trans('mobikul-api::app.api.customer.wishlist.success-wishlist'),
            'totalCount'    => count($this->wishlist),
            'wishList'      => $this->wishlist,
            'eTag'          => '3fcceed4d844f9646517de88206f3d18',
        ];
    }
}