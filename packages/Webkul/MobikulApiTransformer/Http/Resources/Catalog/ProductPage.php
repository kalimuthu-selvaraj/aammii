<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Storage;

class ProductPage extends JsonResource
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
     * Contains total product's review count.
     *
     * @var int
     */
    protected $productReviewCount = 0;

    /**
     * Contains product's reviews.
     *
     * @var array
     */
    protected $productReviews = [];

    /**
     * Contains product's avarage rating.
     *
     * @var float
     */
    protected $productAverageRating = 0.0;

    /**
     * Contains count of each rating
     *
     * @var array
     */
    protected $countEachRating = [];

    /**
     * Contains product review list
     *
     * @var array
     */
    protected $reviewReviewList = [];
    
    /**
     * Contains product's availability.
     *
     * @var string
     */
    protected $availability = 'Out Of Stock';

    /**
     * Contains product's price format.
     *
     * @var array
     */
    protected $priceFormat = [];

    /**
     * Contains product's additional information.
     *
     * @var array
     */
    protected $additionalInformation = [];
    
    /**
     * Contains product's image gallery.
     *
     * @var array
     */
    protected $imageGallery = [];
    
    /**
     * Contains product's wishlist item id.
     *
     * @var int
     */
    protected $wishlistItemId = 0;
    
    /**
     * Contains product's related products.
     *
     * @var array
     */
    protected $relatedProducts = [];
    
    /**
     * Contains product's up-sell products.
     *
     * @var array
     */
    protected $upSellProducts = [];
    
    /**
     * Contains product's mix price.
     *
     * @var float
     */
    protected $minPrice = 0;
    
    /**
     * Contains product's max price
     *
     * @var float
     */
    protected $maxPrice = 0;
    
    /**
     * Contains downloadable product's sample/links.
     *
     * @var array
     */
    protected $downloadableData = [];

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
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->productRepository = app('Webkul\Product\Repositories\ProductRepository');

        $this->reviewHelper = app('Webkul\Product\Helpers\Review');

        $this->productInformation = app('Webkul\Product\Helpers\View');

        $this->configurableOptionHelper = app('Webkul\Product\Helpers\ConfigurableOption');
        
        $this->productTypeHelper = app('Webkul\Product\Helpers\ProductType');

        $this->wishlistRepository = app('Webkul\Customer\Repositories\WishlistRepository');

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
        
        $product = $this->productRepository->findOneWhere([
            'id'        => $this['productId'],
            'parent_id' => null
        ]);
        if (! isset($product->id) || !isset($product->url_key)) {
            return [
                'success'   => false,
                'message'   => 'Error: This product is not available.'
            ];
        }

        $productFlat = $product->product_flats->where('status', 1)->where('channel', $channel->code)->where('locale', $this->localeCode)->first();
        if (! $productFlat ) {
            return [
                'success'   => false,
                'message'   => 'Error: This product is not available.'
            ];
        }

        $this->productReviewCount = $this->reviewHelper->getTotalReviews($product);
        $this->productReviews = $this->reviewHelper->getReviews($product)->paginate(10);
        $this->productAverageRating = $this->reviewHelper->getAverageRating($product);
        $this->countEachRating = $this->getPercentageRating($product);
        
        foreach ($this->productReviews as $review) {
            $reviewArray = [
                [
                    'label'    => 'Rating',
                    'value'     => $review->rating,
                ]
            ];

            $this->reviewReviewList[] = [
                'title'         => $review->title,
                'details'       => $review->comment,
                'avgRatings'    => $review->rating,
                'ratings'       => $reviewArray,
                'reviewBy'      => 'Review by ' . $review->name,
                'reviewOn'      => '(Posted on ' . $review->created_at . ')',
            ];
        }
        
        if ( $product->getTypeInstance()->haveSufficientQuantity(1) ) {
            $this->availability = 'In stock';
        }
        
        $this->priceFormat = [
            'pattern'           => core()->currencySymbol($this->currencyCode) . core()->getAccountJsSymbols()['format'],
            'precision'         => 2,
            'requiredPrecision' => 2,
            'decimalSymbol'     => core()->getAccountJsSymbols()['decimal'],
            'groupSymbol'       => ',',
            'groupLength'       => 3,
            'integerRequired'   => false,
        ];
        
        $productBaseImage = productimage()->getProductBaseImage($product);
        foreach (productimage()->getGalleryImages($product) as $key => $image) {
            $this->imageGallery[] = [
                'smallImage'    => $image['small_image_url'],
                'mediumImage'   => $image['medium_image_url'],
                'largeImage'    => $image['large_image_url'],
            ];
        }
        
        $this->additionalInformation = $this->productInformation->getAdditionalData($product);

        $productVarients = $this->getProductVariants($product);
        
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

        $this->getRelatedProducts($product);
        $this->getUpSellProducts($product);

        if ( $product->type == 'configurable' ) {
            $this->minPrice = $productFlat->min_price;
            $this->maxPrice = $productFlat->max_price;
        }

        $this->getCartItemCount();
        
        $productPrice = mobikulApi()->getProductPrice($product);

        $productValue = [
            'success'                   => true,
            'message'                   => '',
            'arUrl'                     => '',
            'arType'                    => '2D',
            'arTextureImages'           => [],
            'reviewCount'               => $this->productReviewCount,
            'reviewArray'               => $this->countEachRating, //array shown in descending order
            'id'                        => $product->id,
            'name'                      => $productFlat->name,
            'typeId'                    => $product->type,
            'productUrl'                => route('shop.productOrCategory.index', $productFlat->url_key),
            'guestCanReview'            => core()->getConfigData('catalog.products.review.guest_review') ? true : false,
            'showPriceDropAlert'        => false, //need discussion
            'showBackInStockAlert'      => false, //need discussion
            'isAllowedGuestCheckout'    => $product->guest_checkout ? true : false,
            'minPrice'                  => (float) $this->minPrice,
            'maxPrice'                  => (float) $this->maxPrice,
            'formattedMinPrice'         => $this->minPrice ? core()->currency($this->minPrice, $this->currencyCode) : '',
            'formattedMaxPrice'         => $this->maxPrice ? core()->currency($this->maxPrice, $this->currencyCode) : '',

            'price'                     => (float) $productPrice['price'],
            'formattedPrice'            => (string) core()->formatPrice($productPrice['price'], core()->getBaseCurrencyCode()),

            'finalPrice'                => (float) core()->convertPrice($productPrice['price'], $this->currencyCode),
            'formattedFinalPrice'       => (string) core()->currency($productPrice['price'], $this->currencyCode),
            
            'specialPrice'              => $this->when(
                $product->getTypeInstance()->haveSpecialPrice(),
                (float) $product->getTypeInstance()->getSpecialPrice()
            ),
            'formatedSpecialPrice'      => $this->when(
                    $product->getTypeInstance()->haveSpecialPrice(),
                    core()->formatPrice($product->getTypeInstance()->getSpecialPrice(), core()->getBaseCurrencyCode())
            ),

            'convertedSpecialPrice'     => $this->when(
                    $product->getTypeInstance()->haveSpecialPrice(),
                    (float) core()->convertPrice($product->getTypeInstance()->getSpecialPrice(),$this->currencyCode)
            ),
            'formatedConvertedSpecialPrice'      => $this->when(
                    $product->getTypeInstance()->haveSpecialPrice(),
                    core()->formatPrice(core()->convertPrice($product->getTypeInstance()->getSpecialPrice(), $this->currencyCode), $this->currencyCode)
            ),
            'msrp'                      => null, //need discussion
            'msrpEnabled'               => null, //need discussion
            'description'               => $productFlat->description,
            'formattedMsrp'             => '$0.00', //need discussion
            'shortDescription'          => $productFlat->short_description,
            'msrpDisplayActualPriceType' => 0, //need discussion
            'isInRange'                 => false, //need discussion
            'availability'              => $this->availability,
            'isAvailable'               => $product->getTypeInstance()->isSaleable(),
            'priceFormat'               => $this->priceFormat, //need discussion
            'imageGallery'              => $this->imageGallery,
            'thumbNail'                 => $productBaseImage['medium_image_url'],
            'dominantColor'             => mobikulApi()->getImageDominantColor($productBaseImage['medium_image_url']),
            'additionalInformation'     => $this->additionalInformation,
            'ratingFormData'            => "", //pending
            'ratingData'                => [
                'ratingCode'                => 'Rating',
                'ratingValue'               => $this->productAverageRating,
            ],
            'reviewList'                => $this->reviewReviewList,
            'rating'                    => $this->productAverageRating,
            'customOptions'             => [],
            'configurableData'          => $productVarients,
            'is_new'                    => $product->new,
            'relatedProductList'        => $this->relatedProducts,
            'upsellProductList'         => $this->upSellProducts,
            'cartCount'                 => $this->cartItemCount,
            'isInWishlist'              => $this->wishlistItemId ? true : false,
            'wishlistItemId'            => $this->wishlistItemId,
            'canGuestCheckoutDownloadable' => false, //need discussion
            'minAddToCartQty'           => 1,
            'displaySellerInfo'         => true, //need discussion
            'sellerId'                  => 0, //need discussion
            'shoptitle'                 => '', //need discussion
            'sellerRating'              => [],//need discussion
            'reviewDescription'         => '', //need discussion
            'eTag'                      => 'a1fdcfcc328fe4e6e2055d804d6a1c77', //need discussion
        ];

        if ( $product->type == 'downloadable' ) {
            $productValue = array_merge($productValue, $this->downloadableData);
        }

        return $productValue;
    }
    
    /**
     * Returns the Percentage rating of the product
     *
     * @param  \Webkul\Product\Models\Product $product
     * @return array
     */
    public function getPercentageRating($product)
    {
        $productReviews = $product->reviews()
        ->select('rating', DB::raw('count(*) as total'))
        ->where('status', 'approved')
        ->groupBy('rating')
        ->orderBy('rating','desc')
        ->get();
        
        $reviewArray = [];
        for ($i = 5; $i >= 1; $i--) {
            foreach ($productReviews as $review) {
                if ($review->rating == $i) {
                    $reviewArray[$i] = $review->total;
                    break;
                } else {
                    $reviewArray[$i] = 0;
                }
            }
        }
        
        return $reviewArray;
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
                    $this->downloadableData['links'] = [
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
                    $this->downloadableData['samples'] = [
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
     * Get the product's related product array.
     *
     * @param  \Webkul\Product\Models\Product $product
     * @return void
     */
    public function getRelatedProducts($product)
    {
        $relatedProductIds = $product->related_products->pluck('id')->toArray();
        
        if ( $relatedProductIds ) {
            $related_products = $this->productRepository->whereIn('id', $relatedProductIds)->get();

            foreach($related_products as $key =>  $related_product) {
                $rpBaseImage = productimage()->getProductBaseImage($related_product);
                $relatedProduct = $related_product->toArray();
                $this->relatedProducts[$key] = array_merge($relatedProduct, [
                    'finalPrice'            => core()->convertPrice($relatedProduct['price'], $this->currencyCode),
                    'formattedPrice'        => core()->formatPrice($relatedProduct['price'], core()->getBaseCurrencyCode()),
                    'formattedFinalPrice'   => core()->currency($relatedProduct['price'], $this->currencyCode),
                    'thumbNail'             => $rpBaseImage['medium_image_url'],
                    'dominantColor'         => mobikulApi()->getImageDominantColor($rpBaseImage['medium_image_url']),
                ]);
            }
        }
    }
    
    /**
     * Get the product's up-sell product array.
     *
     * @param  \Webkul\Product\Models\Product $product
     * @return void
     */
    public function getUpSellProducts($product)
    {
        $upSellProductIds = $product->up_sells->pluck('id')->toArray();
        
        if ( $upSellProductIds ) {
            $upsell_products = $this->productRepository->whereIn('id', $upSellProductIds)->get();

            foreach($upsell_products as $key =>  $upsell_product) {
                $uspBaseImage = productimage()->getProductBaseImage($upsell_product);
                $upSellProduct = $upsell_product->toArray();

                $this->upSellProducts[$key] = array_merge($upSellProduct, [
                    'price'                 => $upsell_product->getTypeInstance()->getMinimalPrice(),
                    'finalPrice'            => core()->convertPrice($upsell_product->getTypeInstance()->getMinimalPrice(), $this->currencyCode),
                    'formattedPrice'        => core()->formatPrice($upsell_product->getTypeInstance()->getMinimalPrice(), core()->getBaseCurrencyCode()),
                    'formattedFinalPrice'   => core()->currency($upsell_product->getTypeInstance()->getMinimalPrice(), $this->currencyCode),
                    'thumbNail'             => $uspBaseImage['medium_image_url'],
                    'dominantColor'         => mobikulApi()->getImageDominantColor($uspBaseImage['medium_image_url']),
                ]);
            }
        }
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