<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Facades\Cart;

class ProductCollection extends JsonResource
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
     * Contains sorting data
     *
     * @var array
     */
    protected $sortData;

    /**
     * Contains the current cart's item count.
     *
     * @var int
     */
    protected $cartItemCount = 0;

    /**
     * Contains the carousel's products.
     *
     * @var array
     */
    protected $products = [];

    /**
     * Contains the product's wishlist item id.
     *
     * @var int
     */
    protected $wishlistItemId = 0;

    /**
     * Contains the banner's images.
     *
     * @var array
     */
    protected $bannerImageList = [];

    /**
     * Contains the product collection.
     *
     * @var array
     */
    protected $productCollection = [];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->toolbarHelper = app('Webkul\Product\Helpers\Toolbar');

        $this->productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');

        $this->wishlistRepository = app('Webkul\Customer\Repositories\WishlistRepository');

        $this->bannerImageRepository = app('Webkul\Mobikul\Repositories\BannerImageRepository');

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

        $this->getSortData();

        $this->getCartItemCount();

        $this->getCarouselProducts($request, $channel);
        
        foreach ($this->products as $key => $productFlat) {
            $product = $productFlat->product;

            $productBaseImage = productimage()->getProductBaseImage($product);
            $productVarients = mobikulApi()->getProductTypeCast($product);
            
            if ( isset($this['customer_id']) && $this['customer_id'] ) {
                $wishlistProduct = $this->wishlistRepository->findOneWhere([
                    'product_id'    => $product->id,
                    'customer_id'   => $this['customer_id'],
                ]);

                $this->wishlistItemId = 0;
                if ( $wishlistProduct) {
                    $this->wishlistItemId = $wishlistProduct->id;
                }
            }

            $this->getBannerImages($product, $channel);
            
            $productPrice = mobikulApi()->getProductPrice($product);
            
            $this->productCollection[$key] = [
                'configurableData'          => $productVarients,
                'isInWishlist'              => $this->wishlistItemId ? true : false,
                'wishlistItemId'            => $this->wishlistItemId,
                'typeId'                    => $product->type,
                'entityId'                  => $product->id,
                'linksPurchasedSeparately'  => 1,
                'shortDescription'          => strip_tags($productFlat->short_description),
                'rating'                    => $this->reviewHelper->getAverageRating($product),
                'isAvailable'               => $product->getTypeInstance()->isSaleable(),

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
                
                'name'                      => $productFlat->name,
                'hasRequiredOptions'        => $product->getTypeInstance()->hasVariants() ? true : false,
                'isNew'                     => $product->new,
                'isInRange'                 => false, //need discussion
                'thumbNail'                 => $productBaseImage['original_image_url'],
                'dominantColor'             => mobikulApi()->getImageDominantColor($productBaseImage['original_image_url']),
                'minAddToCartQty'           => 1,
            ];
        }
        
        return [
            'success'                   => true,
            'message'                   => "",
            'showSwatchOnCollection'    => true,
            'totalCount'                => $this->products->total(),
            'productList'               => $this->productCollection,
            // 'layeredData'               => mobikulApi()->getFilterData(null),
            // 'sortingData'               => $sortData,
            'layeredData'               => [],
            'sortingData'               => [],
            'cartCount'                 => $this->cartItemCount,
            'bannerImage'               => $this->bannerImageList,
            'dominantColor'             => '#a1b4ca', //need discussion
            'eTag'                      => '04716a35ddeba1e68c8882b3a755f20a', //need discussion
        ];
    }

    /**
     * Get the sorting data for collection page.
     *
     * @return mixed
     */
    public function getSortData()
    {
        foreach ($this->toolbarHelper->getAvailableOrders() as $key => $order) {
            $this->sortData[] = [
                'code'  => $this->toolbarHelper->getOrderUrl($key),
                'label' => trans('shop::app.products.' . $order),
            ];
        }
    }

    /**
     * Get the Item count of current cart.
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

    /**
     * Get the carousel products.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Webkul\Core\Models\Channel $channel
     * @return void
     */
    public function getCarouselProducts($request, $channel)
    {
        $request->merge([
            'page'  => (isset($request['pageNumber']) && $request['pageNumber']) ? $request['pageNumber'] : 1,
            'limit' => (isset($request['limit']) && $request['limit']) ? $request['limit'] : core()->getConfigData('mobikul.mobikul.basicinformation.current_page_size'),
        ]);
        
        switch ($request['type']) {
            case 'customCarousel':
                $this->products = $this->productFlatRepository->scopeQuery(function($query) use($request, $channel) {
                    
                    $qb = $query->distinct()
                            ->addSelect('product_flat.*')
                            ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
                            ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                            ->where('products.type', 'simple')
                            ->whereNull('products.parent_id')
                            ->where('product_flat.channel', $channel->code)
                            ->where('product_flat.locale', $this->localeCode)
                            ->whereNotNull('product_flat.url_key');

                    
                    if ( $request['id'] == 'featuredProduct') {
                        $qb->where('product_flat.featured', 1);
                    }

                    if ( $request['id'] == 'newProduct') {
                        $qb->where('product_flat.new', 1);
                    }
                                
                    return $qb->groupBy('product_flat.id');
                })->paginate($request['limit']);

                break;
            case 'search':
            case 'advSearch':
                $this->products = $this->productFlatRepository->scopeQuery(function($query) use($request, $channel) {
                    return $query->distinct()
                                ->addSelect('product_flat.*')
                                ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
                                ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                                ->where('product_flat.channel', $channel->code)
                                ->where('product_flat.locale', $this->localeCode)
                                ->whereNotNull('product_flat.url_key')
                                ->where('product_flat.name', 'like', '%' . urldecode($request['id']) . '%')
                                ->where('products.type', 'simple')
                                ->whereNull('products.parent_id')
                                ->groupBy('product_flat.id');
                })->paginate($request['limit']);
                
                break;

            case 'category':
                $this->products = $this->productFlatRepository->scopeQuery(function($query) use($request, $channel) {
                    return $query->distinct()
                                ->addSelect('product_flat.*')
                                ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
                                ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                                ->where('products.type', 'simple')
                                ->whereNull('products.parent_id')
                                ->where('product_flat.channel', $channel->code)
                                ->where('product_flat.locale', $this->localeCode)
                                ->where('product_categories.category_id', $request['id'])
                                ->whereNotNull('product_flat.url_key')
                                ->groupBy('product_flat.id');
                })->paginate($request['limit']);
                    
                break;
            
            default:
                $this->products = [];
                break;
        }
    }

    /**
     * Get the banner images.
     *
     * @param  \Webkul\Product\Models\Product $product
     * @param  \Webkul\Core\Models\Channel $channel
     * @return void
     */
    public function getBannerImages($product, $channel)
    {
        $bannerImages = $this->bannerImageRepository->findWhere([
            'status'                => 1,
            'type'                  => 'product',
            'product_category_id'   => $product->id,
        ]);
        
        foreach ($bannerImages as $banner) {
            $bannerTranslation = $banner->translations->where('channel', $channel->code)->where('locale', $this->localeCode)->first();

            if ( $bannerTranslation && $banner->product_category_id ) {
                $this->bannerImageList[]    = [
                    'url'           => $banner->image_url ? $banner->image_url : '',
                    'dominantColor' => mobikulApi()->getImageDominantColor($banner->image_url),
                    'bannerType'    => $banner->type ? $banner->type : 'category',
                    'id'            => $banner->product_category_id,
                    'name'          => $bannerTranslation->name,
                ];
            }
        }
    }
}