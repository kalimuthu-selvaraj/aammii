<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CompareList extends JsonResource
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
     * Contains compare products.
     *
     * @var array
     */
    protected $compareProducts = [];
    
    /**
     * Contains product's availability.
     *
     * @var string
     */
    protected $availability = 'Out Of Stock';

    /**
     * Contains total product's review count.
     *
     * @var int
     */
    protected $productReviewCount = 0;
    
    /**
     * Contains product's wishlist item id.
     *
     * @var int
     */
    protected $wishlistItemId = 0;

    /**
     * Contains product's avarage rating.
     *
     * @var float
     */
    protected $productAverageRating = 0.0;

    /**
     * Contains compare product list.
     *
     * @var array
     */
    protected $productList = [];

    /**
     * Contains compare attribute list.
     *
     * @var array
     */
    protected $compareAttributeList = [];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->attributeRepository = app('\Webkul\Attribute\Repositories\AttributeRepository');
        
        $this->compareRepository = app('Webkul\Velocity\Repositories\VelocityCustomerCompareProductRepository');

        $this->configurableOptionHelper = app('Webkul\Product\Helpers\ConfigurableOption');
        
        $this->productTypeHelper = app('Webkul\Product\Helpers\ProductType');

        $this->wishlistRepository = app('Webkul\Customer\Repositories\WishlistRepository');

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
        $comparableAttributes = $this->attributeRepository->findByField('is_comparable', 1);
        
        if ( isset($this['customer_id']) && $this['customer_id'] ) {
            $compareProducts = $this->compareRepository->findByField('customer_id', $this['customer_id']);
            
            if (! $compareProducts->isEmpty() ) {
                $this->getProductList($compareProducts, $channel);
                
                foreach ($comparableAttributes as $attribute) {
                    $compareValue = [];
                    
                    foreach($compareProducts as $compare) {
                        $productFlat = $compare->product_flat;
                        $product = $productFlat->product;
                        if (! $product || (isset($product->url_key) && !$product->url_key)) {
                            continue;
                        }

                        $productFlatTranslation = $product->product_flats->where('channel', $channel->code)->where('locale', $this->localeCode)->first();

                        if ( $productFlatTranslation ) {
                            $productAttrValue = 'N/A';

                            if ( isset($productFlatTranslation[$attribute->code]) && $productFlatTranslation[$attribute->code]) {

                                if ( $attribute->code == 'price' ) {
                                    $productAttrValue = core()->formatPrice(core()->convertPrice($productFlatTranslation['price'], $this->currencyCode), $this->currencyCode);
                                } else if ( $attribute->code == 'color' || $attribute->code == 'size' ) {
                                    $productAttrValue = $productFlatTranslation[$attribute->code . '_label'];
                                } else {
                                    if ( $attribute->type == 'boolean' ) {
                                        $productAttrValue = ($productFlatTranslation[$attribute->code] == 1) ? trans('velocity::app.shop.general.yes') : trans('velocity::app.shop.general.no');
                                    } else {
                                        $productAttrValue = $productFlatTranslation[$attribute->code];
                                    }
                                }
                            }

                            $compareValue[] = $productAttrValue;
                        }
                    }
    
                    $this->compareAttributeList[] = [
                        'attributeName' => $attribute->admin_name,
                        'value'         => $compareValue,
                    ];
                }
            }
        }
        
        return [
            'success'               => true,
            'message'               => 'Success: Compare list.',
            'productList'           => $this->productList,
            'attributeValueList'    => $this->compareAttributeList,
            'eTag'              => "9ab0e5c0839936c923106e9de612b77f",
        ];
    }
    
    /**
     * Returns the Percentage rating of the product
     *
     * @param  \Webkul\Product\Models\Product $product
     * @return object
     */
    public function getProductVariants($product)
    {
        $productVarients = new \stdClass();
        switch ($product->type) {
            case 'configurable':
                if ( $this->productTypeHelper->hasVariants($product->type) ) {
                    $productVarients = $this->configurableOptionHelper->getConfigurationConfig($product);
                    $productVarients['index'] = json_encode($productVarients['index']);
                }
                break;
            case 'bundle':
                $productVarients = app('Webkul\Product\Helpers\BundleOption')->getBundleConfig($product);
                break;
            case 'grouped':
                $productVarients = $product->grouped_products;
                break;
            case 'downloadable':
                $downloadable_data = [];
                if ( $product->downloadable_links->count() ) {
                    $link_data = [];
                    foreach($product->downloadable_links as $downloadable_link) {
                        $link = $downloadable_link->toArray();
                        $link_name = $link['title'];
                        foreach ($link['translations'] as $key => $translation) {
                            if ( $this->localeCode == $translation['locale'] ) {
                                $link_name = $translation['title'];
                            }
                        }
                        $link_data[] = [
                            'id'                => $link['id'],
                            'price'             => (double) core()->convertPrice($link['price'], $this->currencyCode),
                            'linkTitle'         => $link_name,
                            'formattedPrice'    => core()->formatPrice(core()->convertPrice($link['price'], $this->currencyCode), $this->currencyCode),
                        ];
                    }
    
                    $downloadable_data['links'] = [
                        'title'                     => 'Downloads',
                        'linksPurchasedSeparately'  => 1,
                        'linkData'                  => $link_data,
                    ];
                }
    
                if ( $product->downloadable_samples->count() ) {
                    $linkSampleData = [];
                    foreach($product->downloadable_samples as $downloadable_sample) {
                        $sample = $downloadable_sample->toArray();
                        $sample_name = $sample['title'];
                        foreach ($sample['translations'] as $key => $translation) {
                            if ( $this->localeCode == $translation['locale'] ) {
                                $sample_name = $translation['title'];
                            }
                        }
                        $fileUrl = '';
                        $mimeType = '';
                        if ( $sample['type'] == 'file' ) {
                            $fileUrl = Storage::URL($sample['file']);
                            $mimeType = mobikulApi()->from($fileUrl);
                        } else {
                            $fileUrl = $sample['url'];
                            $mimeType = mobikulApi()->from($fileUrl);
                        }
    
                        $linkSampleData[] = [
                            'sampleTitle'   => $sample_name,
                            'url'           => $fileUrl,
                            'mimeType'      => $mimeType,
                            'fileName'      => $sample['file_name'] ?? '',
                        ];
                    }
                    $downloadable_data['samples'] = [
                        'hasSample'         => true,
                        'title'             => 'Samples',
                        'linkSampleData'    => $linkSampleData,
                    ];
                }
                break;
            
            default:
                # code...
                break;
        }

        return $productVarients;
    }
    
    /**
     * Returns the product list based on compare records.
     *
     * @param  array $compareProducts
     * @param  \Webkul\Core\Models\Channel $channel
     * @return object
     */
    public function getProductList($compareProducts, $channel)
    {
        foreach($compareProducts as $compare) {
            $productFlat = $compare->product_flat;
            $product = $productFlat->product;
            if (! $product || (isset($product->url_key) && !$product->url_key)) {
                continue;
            }

            $productFlatTranslation = $product->product_flats->where('channel', $channel->code)->where('locale', $this->localeCode)->first();

            if ( $productFlatTranslation ) {
                $productBaseImage = productimage()->getProductBaseImage($product);
                $productVarients = $this->getProductVariants($product);
                
                $this->productReviewCount = $this->reviewHelper->getTotalReviews($product);
                $this->productAverageRating = $this->reviewHelper->getAverageRating($product);
                
                if ( isset($this['customer_id']) && $this['customer_id'] ) {
                    $wishlistProduct = $this->wishlistRepository->findOneWhere([
                        'product_id'    => $product->id,
                        'customer_id'   => $this['customer_id'],
                    ]);

                    $this->wishlistItemId = 0;
                    if ( $wishlistProduct ) {
                        $this->wishlistItemId = $wishlistProduct->id;
                    }
                }
    
                if ( $product->getTypeInstance()->haveSufficientQuantity(1) ) {
                    $this->availability = 'In stock';
                }
    
                $productPrice = mobikulApi()->getProductPrice($product);
    
                $this->productList[] = [
                    'reviewCount'           => $this->productReviewCount,
                    'configurableData'      => $productVarients,
                    'isInWishlist'          => $this->wishlistItemId ? true : false,
                    'wishlistItemId'        => $this->wishlistItemId,
                    'typeId'                => $product->type,
                    'entityId'              => $product->id,
                    'rating'                => $this->productAverageRating,
                    'isAvailable'           => $product->getTypeInstance()->isSaleable(),

                    'price'                 => (float) $productPrice['price'],
                    'finalPrice'            => $product->getTypeInstance()->haveSpecialPrice() ? (float) core()->convertPrice($product->getTypeInstance()->getSpecialPrice(),$this->currencyCode) : (float) core()->convertPrice($productPrice['price'], $this->currencyCode),
        
                    'formattedPrice'        => (string) core()->formatPrice($productPrice['price'], core()->getBaseCurrencyCode()),
        
                    'formattedFinalPrice'   => $product->getTypeInstance()->haveSpecialPrice() ? (string) core()->formatPrice(core()->convertPrice($product->getTypeInstance()->getSpecialPrice(), $this->currencyCode), $this->currencyCode) : (string) core()->currency($productPrice['price'], $this->currencyCode),
                    'specialPrice'          => $this->when(
                        $product->getTypeInstance()->haveSpecialPrice(),
                        (float) $product->getTypeInstance()->getSpecialPrice()
                    ),
                    'convertedSpecialPrice' => $this->when(
                            $product->getTypeInstance()->haveSpecialPrice(),
                            (float) core()->convertPrice($product->getTypeInstance()->getSpecialPrice(),$this->currencyCode)
                    ),
                    'formatedSpecialPrice'  => $this->when(
                            $product->getTypeInstance()->haveSpecialPrice(),
                            core()->formatPrice($product->getTypeInstance()->getSpecialPrice(), core()->getBaseCurrencyCode())
                    ),
                    'formatedConvertedSpecialPrice'      => $this->when(
                            $product->getTypeInstance()->haveSpecialPrice(),
                            core()->formatPrice(core()->convertPrice($product->getTypeInstance()->getSpecialPrice(), $this->currencyCode), $this->currencyCode)
                    ),

                    'name'                  => $productFlatTranslation->name,
                    'hasRequiredOptions'    => $product->getTypeInstance()->hasVariants() ? true : false,
                    'groupedPrice'          => '0', //need discussion with android dev
                    'isNew'                 => $product->new,
                    'isInRange'             => false, //need discussion with android dev
                    'thumbNail'             => $productBaseImage['medium_image_url'],
                    'dominantColor'         => mobikulApi()->getImageDominantColor($productBaseImage['medium_image_url']),
                    'tierPrice'             => '', //need discussion with android dev
                    'formattedTierPrice'    => '', //need discussion with android dev
                    'minAddToCartQty'       => 1, //need discussion with android dev
                    'availability'          => $this->availability,
                    'arUrl'                 => '', //need discussion with android dev
                    'arType'                => '2D', //need discussion with android dev
                    'arTextureImages'       => [] //need discussion with android dev
                ];
            }
        }
    }
}