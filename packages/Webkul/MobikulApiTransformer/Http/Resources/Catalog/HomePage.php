<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Storage;

class HomePage extends JsonResource
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
     * Contains login customer detail
     *
     * @var array
     */
    protected $customerInformation = [
        'id'        => 0,
        'name'      => '',
        'email'     => '',
        'profile'   => '',
        'banner'    => '',
    ];

    /**
     * Contains the current's channel categories
     *
     * @var array
     */
    protected $categoryList = [];

    /**
     * Contains the mobikul featured categories
     *
     * @var array
     */
    protected $featuredCategoryList = [];

    /**
     * Contains the mobikul banner images
     *
     * @var array
     */
    protected $bannerImageList = [];

    /**
     * Contains the channel currencies
     *
     * @var array
     */
    protected $allowedCurrencies = [];

    /**
     * Contains the channels.
     *
     * @var array
     */
    protected $channelList = [];

    /**
     * Contains the current cart's item count.
     *
     * @var int
     */
    protected $cartItemCount = 0;

    /**
     * Contains the allowed CMS pages.
     *
     * @var array
     */
    protected $cmsPages = [];

    /**
     * Contains the mobikul carousel list.
     *
     * @var array
     */
    protected $carouselList = [];

    /**
     * Contains the mobikul carousel allowed type.
     *
     * @var array
     */
    protected $allowCarouselType = [
        'product_type',
        'featured',
        'top_offered',
    ];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->categoryRepository = app('Webkul\Category\Repositories\CategoryRepository');

        $this->featuredCategoryChannelRepository = app('Webkul\Mobikul\Repositories\FeaturedCategoryChannelRepository');

        $this->bannerImageTranslationRepository = app('Webkul\Mobikul\Repositories\BannerImageTranslationRepository');

        $this->cmsRepository = app('Webkul\CMS\Repositories\CmsRepository');

        $this->carouselRepository = app('Webkul\Mobikul\Repositories\CarouselRepository');
        
        $this->carouselImagesRepository = app('Webkul\Mobikul\Repositories\CarouselImagesRepository');
        
        $this->productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');
        
        $this->productRepository = app('Webkul\Product\Repositories\ProductRepository');

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

        $this->getCustomerDetail($this['customer']);

        $this->getCategoryList($channel);

        $this->getFeaturedCategoryList();

        $this->getbannerImageList($channel);

        $this->getAllowedCurrencies($channel);

        $this->getAllowedChannelWithLocales();

        $this->getCartItemCount();

        $this->getAllowedCMSPages();

        $this->getCarouselList($channel);
        
        return [
            'success'                   => true,
            'message'                   => "",
            'defaultStoreId'            => core()->getDefaultChannel()->id,
            'allowedCurrencies'         => $this->allowedCurrencies,
            'defaultCurrency'           => $this->currencyCode,
            'showSwatchOnCollection'    => true,
            'priceFormat'               => [ //need discussion
                'pattern'                       => '$%s',
                'precision'                     => 2,
                'requiredPrecision'             => 2,
                'decimalSymbol'                 => '.',
                'groupSymbol'                   => ',',
                'groupLength'                   => 3,
                'integerRequired'               => 1,
            ],
            'themeCode'                 => themes()->current()->code,
            'categories'                => $this->categoryList,
            'wishlistEnable'            => true,
            'featuredCategories'        => $this->featuredCategoryList,
            'bannerImages'              => $this->bannerImageList,
            'storeData'                 => $this->channelList,
            'carousel'                  => $this->carouselList,
            'customerName'              => $this->customerInformation['name'],
            'customerEmail'             => $this->customerInformation['email'],
            'cartCount'                 => $this->cartItemCount,
            'customerBannerImage'       => $this->customerInformation['banner'],
            'bannerDominantColor'       => $this->customerInformation['banner'] ? mobikulApi()->getImageDominantColor($this->customerInformation['banner']) : '',
            'customerProfileImage'      => $this->customerInformation['profile'],
            'customerDominantColor'     => $this->customerInformation['profile'] ? mobikulApi()->getImageDominantColor($this->customerInformation['profile']) : '',
            'cmsData'                   => $this->cmsPages,
            'eTag'                      => '29276910e3e0b949801c39a1aa7ef334', //need discussion
        ];
    }

    /**
     * Use the customer resource into an array.
     *
     * @param  \Webkul\Customer\Models\Customer $customer
     * @return mixed
     */
    public function getCustomerDetail($customer)
    {
        if (! empty($customer) ) {
            $this->customerInformation  = [
                'id'        =>   $customer->id,
                'name'      =>   $customer->name,
                'email'     =>   $customer->email,
                'profile'   =>   Storage::URL($customer->profile_pic),
                'banner'    =>   Storage::URL($customer->banner_pic),
            ];
        }
    }

    /**
     * Get the category list based on channel.
     *
     * @param  \Webkul\Core\Models\Channel $channel
     * @return mixed
     */
    public function getCategoryList($channel)
    {
        $categories = $this->categoryRepository->getVisibleCategoryTree($channel->root_category_id);
        
        foreach ($categories as $category) {
            $categoryTranslation = $category->translations->where('locale', $this->localeCode)->first();
            
            $this->categoryList[] = [
                'id'                        => $category->id,
                'name'                      => $categoryTranslation->name ?? $category->name ?: '',
                'hasChildren'               => count($category->children) ? true : false,
                'thumbnail'                 => $category->image_url ? $category->image_url : '',
                'thumbnailDominantColor'    => mobikulApi()->getImageDominantColor($category->image_url),
                'banner'                    => $category->image_url ? $category->image_url : '',
                'bannerDominantColor'       => mobikulApi()->getImageDominantColor($category->image_url),
            ];
        }
    }

    /**
     * Get the mobikul featured category list.
     *
     * @return mixed
     */
    public function getFeaturedCategoryList()
    {
        $featuredCategoryChannels = $this->featuredCategoryChannelRepository->findByField('channel_id', $this->channel);

        foreach ($featuredCategoryChannels as $featuredCategoryChannel) {
            $featuredCategory = $featuredCategoryChannel->featured_category;
            $category = $featuredCategory->category;

            if ( $featuredCategory && $category ) {
                if (! $featuredCategory->status )
                    continue;

                $this->featuredCategoryList[]   = [
                    'url'           => $featuredCategory->image_url ?? '',
                    'dominantColor' => mobikulApi()->getImageDominantColor($featuredCategory->image_url),
                    'categoryId'    => $featuredCategory->category_id,
                    'categoryName'  => $featuredCategory->category_name($this->localeCode),
                ];
            }
        }
    }

    /**
     * Get the mobikul banner image list.
     *
     * @param  \Webkul\Core\Models\Channel $channel
     * @return mixed
     */
    public function getbannerImageList($channel)
    {
        $bannerImageTranslations = $this->bannerImageTranslationRepository->findWhere([
            'channel'   => $channel->code,
            'locale'    => $this->localeCode,
        ]);
        
        foreach ($bannerImageTranslations as $bannerImageTranslation) {
            $bannerImage = $bannerImageTranslation->banner_image;
            if (! $bannerImage->status )
                continue;

            if ( $bannerImage->product_category_id ) {
                $this->bannerImageList[]    = [
                    'url'           => $bannerImage->image_url ?? '',
                    'dominantColor' => mobikulApi()->getImageDominantColor($bannerImage->image_url),
                    'bannerType'    => $bannerImage->type ?? 'category',
                    'id'            => $bannerImage->product_category_id,
                    'name'          => $bannerImageTranslation->name,
                ];
            }
        }
    }

    /**
     * Get the mobikul channel's currencies.
     *
     * @param  \Webkul\Core\Models\Channel $channel
     * @return void
     */
    public function getAllowedCurrencies($channel)
    {
        foreach ($channel->currencies as $currency) {
            if ( $currency ) {
                $this->allowedCurrencies[] = [
                    'id'        => $currency->id,
                    'label'     => core()->currencySymbol($currency->code) . ' ' . $currency->name,
                    'symbol'    => (isset($currency->code) && $currency->code) ? core()->currencySymbol($currency->code) : '',
                    'code'      => $currency->code ? $currency->code : '',
                ];
            }
        }
    }

    /**
     * Get the channel with allowed locales.
     *
     * @return void
     */
    public function getAllowedChannelWithLocales()
    {
        foreach (core()->getAllChannels() as $channel) {
            $locales = [];

            if ( $channel ) {
                foreach ($channel->locales as $locale) {
                
                    if ( $locale ) {
                        $locales[] = [
                            'id'        => $channel->id,
                            'locale_id' => $locale->id,
                            'code'      => $locale->code ?? '',
                            'name'      => $locale->name ?? '',
                        ];
                    }
                }

                $this->channelList[] = [
                    'id'        => $channel->id,
                    'name'      => $channel->name ?? '',
                    'code'      => $channel->code ?? '',
                    'stores'    => $locales,
                ];
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

    /**
     * Get the allowed CMS pages
     *
     * @return void
     */
    public function getAllowedCMSPages()
    {
        $allowed_pages = explode(",", core()->getConfigData('mobikul.mobikul.basicinformation.allowed_cms_pages'));
        
        foreach($this->cmsRepository->get() as $cms) {
            $cmsTranslation = $cms->translations->where('locale', $this->localeCode)->first();

            if ( isset($cmsTranslation->url_key) && in_array($cmsTranslation->url_key, $allowed_pages)) {
                $this->cmsPages[] = [
                    'id'    => $cms->id,
                    'title' => $cmsTranslation->page_title ?? '',
                ];
            }
        }
    }

    /**
     * Get the mobikul carousel list.
     *
     * @return void
     */
    public function getCarouselList($channel)
    {
        $carousels = $this->carouselRepository->findByField('status', 1);
        foreach ($carousels as $carousel) {
            $carouselTranslation = $carousel->translations->where('channel', $channel->code)->where('locale', $this->localeCode)->first();

            if ( $carouselTranslation ) {
                $carouselArray = [
                    'id'                => $carousel->id,
                    'type'              => ($carousel->type == 'image_type') ? 'image' : 'product',
                    'label'             => $carouselTranslation->title,
                    'color'             => $carousel->background_color,
                    'image'             => $carousel->image_url,
                    'dominantColor'     => mobikulApi()->getImageDominantColor($carousel->image_url),
                ];
                
                $carouselProducts = [];
                switch ($carousel->type) {
                    case 'image_type':
                        $carouselImages = $this->carouselImagesRepository->getCarouselImages($carousel->id, [
                            'channel'   => $channel->code,
                            'locale'    => $this->localeCode,
                        ]);

                        foreach($carouselImages as $carouselImage) {
                            $banner =   [
                                'url'                   => $carouselImage->image_url,
                                'dominantColor'         => mobikulApi()->getImageDominantColor($carouselImage->image_url),
                                'title'                 => $carouselImage->title,
                                'bannerType'            => $carouselImage->type,
                                'id'                    => $carouselImage->ci_id,
                                'product_category_id'   => $carouselImage->product_category_id,
                                'name'                  => '',
                            ];
                            
                            if ( $carouselImage->type == 'product') {
                                $productFlat = $this->productFlatRepository->findOneWhere([
                                    'channel'       => $channel->code,
                                    'locale'        => $this->localeCode,
                                    'product_id'    => 
                                    $carouselImage->product_category_id
                                ]);
                                
                                if ( $productFlat) {
                                    $banner['name'] = $productFlat->name;
                                }
                            } elseif ( $carouselImage->type == 'category') {
                                $category = $this->categoryRepository->find($carouselImage->product_category_id);
                                
                                if ( $category ) {
                                    $categoryTranlation = $category->tranlations->where('locale', $this->localeCode)->first();
                                    if ( $categoryTranlation ) {
                                        $banner['name'] = $categoryTranlation ?? $category->name;
                                    }
                                }
                            }

                            $carouselArray['banners'][] = $banner;
                        }
                        break;
                    
                    case 'product_type':
                        $carouselArray['type']  = 'product';
                        $carouselProducts = $this->carouselImagesRepository->getCarouselProducts($carousel->id, [
                            'channel'   => $channel->code,
                            'locale'    => $this->localeCode,
                        ]);
                        break;

                    case 'featured':
                        $carouselArray['type']  = 'featured';
                        if ( core()->getConfigData('mobikul.mobikul.basicinformation.enable_random_feature') == 1 ) {
                            $carouselProducts = $this->productRepository->getFeaturedProducts();
                        } else {
                            $carouselProducts = mobikulApi()->isMobikulFeaturedProduct([
                                'channel'   => $channel->code,
                                'locale'    => $this->localeCode,
                            ]);
                        }
                        break;

                    case 'top_offered':
                        $carouselArray['type']  = 'top_offered';
                        $carouselProducts = mobikulApi()->getTopOfferedProducts(5, [
                            'channel'   => $channel->code,
                            'locale'    => $this->localeCode,
                        ]);
                        break;
                    
                    default:
                        # code...
                        break;
                }
                
                if ( in_array($carousel->type, $this->allowCarouselType) && !empty($carouselProducts) ) {
                    foreach($carouselProducts as $carouselProduct) {
                        $productFlat = $this->productFlatRepository->findOneWhere([
                            'product_id'    => $carouselProduct->product_id,
                            'channel'       => $channel->code,
                            'locale'        => $this->localeCode,
                            'parent_id'     => null
                        ]);

                        if (! isset($productFlat->id)) {
                            continue;
                        }
                        $product = $productFlat->product;
                        
                        $productBaseImage = productimage()->getProductBaseImage($product);
                        $productVarients = mobikulApi()->getProductTypeCast($product);
            
                        $isInWishlist = false;
                        $wishlistItemId = 0;
                        if ( $this->customerInformation['id'] ) {
                            $wishlistProduct = $this->wishlistRepository->findOneWhere([
                                'channel_id'    => $channel->id,
                                'customer_id'   => $this->customerInformation['id'],
                                'product_id'    => $product->product_id
                            ]);
            
                            if ( $wishlistProduct ) {
                                $isInWishlist   = true;
                                $wishlistItemId = $wishlistProduct->id;
                            }
                        }

                        $productPrice = mobikulApi()->getProductPrice($product);

                        $carouselArray['productList'][] = [
                            'configurableData'      => $productVarients,
                            'isInWishlist'          => $isInWishlist,
                            'wishlistItemId'        => $wishlistItemId,
                            'typeId'                => $product->type,
                            'entityId'              => $product->id,
                            'shortDescription'      => $product->short_description,
                            'rating'                => $this->reviewHelper->getAverageRating($product),
                            'isAvailable'           => $product->getTypeInstance()->isSaleable(),
                            
                            'price'                 => (float) $productPrice['price'],
                            'formattedPrice'            => (string) core()->formatPrice($productPrice['price'], core()->getBaseCurrencyCode()),

                            'finalPrice'            => (float) core()->convertPrice($productPrice['price'], $this->currencyCode),
                            'formattedFinalPrice'       => (string) core()->currency($productPrice['price'], $this->currencyCode),

                            'specialPrice'              => $product->getTypeInstance()->haveSpecialPrice() ? (float) $product->getTypeInstance()->getSpecialPrice() : 0,
                            'formatedSpecialPrice'      => $product->getTypeInstance()->haveSpecialPrice() ? core()->formatPrice($product->getTypeInstance()->getSpecialPrice(), core()->getBaseCurrencyCode()) : '',
                            
                            'convertedSpecialPrice'     => $product->getTypeInstance()->haveSpecialPrice() ? core()->convertPrice($product->getTypeInstance()->getSpecialPrice(),$this->currencyCode) : 0,
                            'formatedConvertedSpecialPrice'      => $product->getTypeInstance()->haveSpecialPrice() ? core()->formatPrice(core()->convertPrice($product->getTypeInstance()->getSpecialPrice(), $this->currencyCode), $this->currencyCode) : '',
                            
                            'name'                  => $product->name,
                            'hasRequiredOptions'    => false,
                            'isNew'                 => $product->new,
                            'isInRange'             => false,
                            'thumbNail'             => $productBaseImage['small_image_url'],
                            'minAddToCartQty'       =>  1
                        ];
                    }
                }
            }
            
            $this->carouselList[] = $carouselArray;
        }
    }
}