<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryPage extends JsonResource
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
     * Contains children categories of request category
     *
     * @var array
     */
    protected $childCategories = [];

    /**
     * Contains category's products.
     *
     * @var array
     */
    protected $productListArray = [];

    /**
     * Contains mobikul category's banner images.
     *
     * @var array
     */
    protected $bannerImageList = [];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->categoryRepository = app('Webkul\Category\Repositories\CategoryRepository');

        $this->productRepository = app('Webkul\Product\Repositories\ProductRepository');

        $this->productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');

        $this->wishlistRepository = app('Webkul\Customer\Repositories\WishlistRepository');

        $this->reviewHelper = app('Webkul\Product\Helpers\Review');
        
        $this->bannerImageRepository = app('Webkul\Mobikul\Repositories\BannerImageRepository');
        
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

        $request->merge(['page' => (isset($this['pageNumber']) && $this['pageNumber']) ? $this['pageNumber'] : 1]);

        $limit = isset($this['limit']) ? $this['limit'] : core()->getConfigData('mobikul.mobikul.basicinformation.current_page_size');

        $categoryId = isset($request['categoryId']) ? $request['categoryId'] : $channel->root_category_id;

        $category = $this->categoryRepository->find($categoryId);

        $productListArray = [];
        $bannerImageList = [];
        
        if ( $category ) {
            if (! $category->children->isEmpty()) {
                foreach ( $category->children as $childrenCategory) {
                    $categoryTranslation = $childrenCategory->translations->where('locale', $this->localeCode)->first();
                    
                    if ( $categoryTranslation ) {
                        $this->childCategories[] = [
                            'id'            => $childrenCategory->id,
                            'name'          => $categoryTranslation->name,
                            'hasChildren'   => $childrenCategory->children->isEmpty() ? false : true,
                        ];
                    }
                }
            }
            
            $products = $this->productRepository->where([
                ['type', '=', 'simple'],
                ['parent_id', '=', null],
            ])->with(['categories' => function($query) use($category) {
                $query->where('id', $category->id);
            }])->paginate($limit);
            
            foreach($products as $key => $product ) {
                $productFlat = $product->product_flats->where('channel', $channel->code)->where('locale', $this->localeCode)->first();
                if ( $productFlat ) {

                    if (! $product->categories->isEmpty() ) {
                        $productCategory = $product->categories->first();
                        if ( $category->id == $productCategory->id ) {
                            $productVarients = mobikulApi()->getProductTypeCast($product);
            
                            //check product is in wishlist or not
                            $wishlistItemId = 0;
                            if ( isset($this['customer_id']) && $this['customer_id'] ) {
                                $wishlistProduct = $this->wishlistRepository->findOneWhere([
                                    'product_id'    => $product->id,
                                    'customer_id'   => $this['customer_id'],
                                ]);
                    
                                if ( $wishlistProduct ) {
                                    $wishlistItemId = $wishlistProduct->id;
                                }
                            }
    
                            //get product qty
                            $availability = 'Out Of Stock';
                            if ( $product->getTypeInstance()->totalQuantity() > 0) {
                                $availability = 'In stock';
                            }
    
                            $productBaseImage = productimage()->getProductBaseImage($product);
    
                            $productPrice = mobikulApi()->getProductPrice($product);
                            
                            $this->productListArray[$key] = [
                                'reviewCount'           => $product->reviews()->where('status', 'approved')->count(),
                                'configurableData'      => $productVarients,
                                'isInWishlist'          => $wishlistItemId ? true : false,
                                'wishlistItemId'        => $wishlistItemId,
                                'typeId'                => $product->type,
                                'entityId'              => $product->id,
                                'rating'                => $this->reviewHelper->getAverageRating($product),
                                'isAvailable'           => $product->getTypeInstance()->isSaleable(),
    
                                'price'                 => (float) $productPrice['price'],
                                'formattedPrice'        => (string) core()->formatPrice($productPrice['price'], core()->getBaseCurrencyCode()),

                                'finalPrice'            => (float) core()->convertPrice($productPrice['price'], $this->currencyCode),
                                'formattedFinalPrice'   => (string) core()->currency($productPrice['price'], $this->currencyCode),
    
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
                                
                                'name'                  => isset($productFlat->name) ? $productFlat->name : $product->name,
                                'hasRequiredOptions'    => $product->getTypeInstance()->hasVariants() ? true : false,
                                'groupedPrice'          => '0', //need discussion with android dev
                                'isNew'                 => $product->new,
                                'isInRange'             => false, //need discussion with android dev
                                'thumbNail'             => $productBaseImage['small_image_url'],
                                'dominantColor'         => mobikulApi()->getImageDominantColor($productBaseImage['small_image_url']),
                                'tierPrice'             => '', //need discussion with android dev
                                'formattedTierPrice'    => '', //need discussion with android dev
                                'minAddToCartQty'       => 1, //need discussion with android dev
                                'availability'          => $availability,
                                'arUrl'                 => '', //need discussion with android dev
                                'arType'                => '2D', //need discussion with android dev
                                'arTextureImages'       => [] //need discussion with android dev
                            ];
                        }
                    }
                }
            }
            
            //call the banner images api
            $bannerImages = $this->bannerImageRepository->findWhere([
                'status'                => 1,
                'type'                  => 'category',
                'product_category_id'   => $category->id,
            ]);

            foreach ($bannerImages as $banner) {
                $bannerTranslation = $banner->translations->where('channel', $channel->code)->where('locale', $this->localeCode)->first();
                
                if ( $bannerTranslation ) {
                    $this->bannerImageList[]    = [
                        'url'           => $banner->image_url ? $banner->image_url : '',
                        'dominantColor' => mobikulApi()->getImageDominantColor($banner->image_url),
                        'bannerType'    => $banner->type ? $banner->type : 'category',
                        'id'            => $banner->product_category_id,
                        'name'          => $bannerTranslation->name ?? '',
                    ];
                }
            }
        }

        return [
            'success'       => true,
            'message'       => '',
            'categories'    => $this->childCategories,
            'productList'   => $this->productListArray,
            'hotSeller'     => [], //need discussion
            'bannerImage'   => $this->bannerImageList,
            'eTag'          => '6b5c96bd0e67ab4420e2538b99a2b85e',
        ];
    }
}