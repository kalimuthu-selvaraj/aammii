<?php

namespace Webkul\MobikulApiTransformer;

use Webkul\Core\Repositories\CoreConfigRepository;
use Webkul\Mobikul\Repositories\CarouselRepository;
use Webkul\Mobikul\Repositories\CarouselImagesRepository;
use Webkul\Velocity\Repositories\Product\ProductRepository as VelocityProductRepository;
use Webkul\Velocity\Helpers\Helper as VelocityHelper;
use Webkul\Customer\Repositories\WishlistRepository;
use Webkul\MobikulApiTransformer\Helpers\ProductImage as ProductImageHelper;
use Webkul\Product\Helpers\Review as ReviewHelper;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Checkout\Facades\Cart;
use Webkul\Attribute\Repositories\AttributeOptionRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Illuminate\Support\Str;
use Symfony\Component\Mime\MimeTypes;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class MobikulApi
{    
    /**
     * templateArray
     *
     */
    protected $templateArray = [
        'shop::home.category',
        'shop::home.category-with-custom-option',
        'shop::home.featured-products',
        'shop::home.new-products',
    ];

    /**
     * CoreConfigRepository class
     *
     * @var \Webkul\Core\Repositories\CoreConfigRepository
     */
    protected $coreConfigRepository;
    
    /**
     * CarouselRepository class
     *
     * @var \Webkul\Mobikul\Repositories\CarouselRepository
     */
    protected $carouselRepository;
    
    /**
     * CarouselImagesRepository class
     *
     * @var \Webkul\Mobikul\Repositories\CarouselImagesRepository
     */
    protected $carouselImagesRepository;
    
    /**
     * ProductRepository object of velocity package
     *
     * @var \Webkul\Velocity\Repositories\Product\ProductRepository
     */
    protected $velocityProductRepository;
    
    /**
     * Helper object
     *
     * @var \Webkul\Velocity\Helpers\Helper
     */
    protected $velocityHelper;
    
    /**
     * WishlistRepository object of Customer package
     *
     * @var \Webkul\Customer\Repositories\WishlistRepository
     */
    protected $wishlistRepository;
    
    /**
     * ProductImage Helper object
     *
     * @var \Webkul\Product\Helpers\ProductImage
     */
    protected $productImageHelper;
        
    /**
     * Review Helper object
     *
     * @var \Webkul\Product\Helpers\Review
     */
    protected $reviewHelper;

    /**
     * ProductRepository object of Product package
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;
    
    /**
     * CategoryRepository object of Category package
     *
     * @var \Webkul\Category\Repositories\CategoryRepository
     */
    protected $categoryRepository;
    
    /**
     * AttributeRepository object of Attribute package
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * The mime types instance.
     *
     * @var \Symfony\Component\Mime\MimeTypes|null
     */
    private static $mime;
    
    /**
     * Create a new instance.
     *
     * @param  \Webkul\Core\Repositories\CoreConfigRepository  $coreConfigRepository
     * @param  \Webkul\Mobikul\Repositories\CarouselRepository  $carouselRepository
     * @param  \Webkul\Mobikul\Repositories\CarouselImagesRepository  $carouselImagesRepository
     * @param  \Webkul\Velocity\Repositories\Product\ProductRepository  $velocityProductRepository
     * @param  \Webkul\Velocity\Helpers\Helper  $velocityHelper
     * @param  \Webkul\Customer\Repositories\WishlistRepository $wishlistRepository
     * @param  \Webkul\Product\Helpers\ProductImage  $productImageHelper
     * @param  \Webkul\Product\Repositories\ProductRepository   $productRepository
     * @param  \Webkul\Category\Repositories\CategoryRepository $categoryRepository
     * @param  \Webkul\Attribute\Repositories\AttributeRepository $attributeRepository
     *
     * @return void
     */
    public function __construct(
        CoreConfigRepository $coreConfigRepository,
        CarouselRepository $carouselRepository,
        CarouselImagesRepository $carouselImagesRepository,
        VelocityProductRepository $velocityProductRepository,
        VelocityHelper $velocityHelper,
        WishlistRepository $wishlistRepository,
        ProductImageHelper $productImageHelper,
        ReviewHelper $reviewHelper,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        AttributeRepository $attributeRepository
    )   {
        $this->coreConfigRepository = $coreConfigRepository;

        $this->carouselRepository = $carouselRepository;

        $this->carouselImagesRepository = $carouselImagesRepository;

        $this->velocityProductRepository = $velocityProductRepository;

        $this->velocityHelper = $velocityHelper;

        $this->wishlistRepository = $wishlistRepository;

        $this->productImageHelper = $productImageHelper;

        $this->reviewHelper = $reviewHelper;

        $this->productRepository = $productRepository;

        $this->categoryRepository = $categoryRepository;

        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Add Customer Guard and JWT
     *
     * @param data $value
     * @return mixed
    */
    public function customerAuthentication($token, $guard)
    {
        // $loggedCustomer = auth($guard)->user();
        // if ( (request()->path() == 'mobikulhttp/index/uploadprofilepic' || request()->path() == 'mobikulhttp/index/uploadbannerpic') && isset($loggedCustomer->id) ) {
        //     return true;
        // }

        try {
            $setToken =  \JWTAuth::setToken($token)->authenticate();
            $customerFromToken = \JWTAuth::toUser($setToken);

            if (isset($setToken) && isset($customerFromToken->id)) {
                return true;
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.customer.address-info.error-login'),
                ], 401);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage(),
            ], 401);
        } catch (\Exception $e) {
            //In case customer's session has expired
            if ( $token !== 0 && $loggedCustomer == null ) {
                return response()->json([
                    'success'       => false,
                    'message'       => 'YourSession has expired. Please login again to your account.',
                    'otherError'    => 'customerNotExist'
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => $e->getMessage(),
                ], 401);
            }
        }
    }

    /**
     * Returns image dominant color
     *
     */
    public function getImageDominantColor($filePath)
    {
        $hash = md5($filePath);
        
        $json_file = dirname(__FILE__) . '/color.json';
        $json_data = json_decode(file_get_contents($json_file), true);

        if ( is_array($json_data) && key_exists($hash, $json_data)) {
            return $json_data[$hash];
        }
        
        $total = $blueTotal = $greenTotal = $redTotal = 0;
        if ( pathinfo($filePath, PATHINFO_EXTENSION) == 'jpg' ) {
            $image = @imagecreatefromjpeg($filePath);
        } elseif (pathinfo($filePath, PATHINFO_EXTENSION) == 'png') {
            $image = @imagecreatefrompng($filePath);
        } elseif (pathinfo($filePath, PATHINFO_EXTENSION) == 'gd') {
            $image = @imagecreatefromgd($filePath);
        } elseif (pathinfo($filePath, PATHINFO_EXTENSION) == 'xbm') {
            $image = @imagecreatefromxbm($filePath);
        } elseif (pathinfo($filePath, PATHINFO_EXTENSION) == 'xpm') {
            $image = @imagecreatefromxpm($filePath);
        } else {
            $image = '';
        }

        for ($x = 0; $x < @imagesx($image); $x++) {
            for ($y = 0; $y < @imagesy($image); $y++) {
                $rgb    = @imagecolorat($image, $x, $y);
                $red    = ($rgb >> 16) &0xFF;
                $green  = ($rgb >> 8) &0xFF;
                $blue   = $rgb & 0xFF;

                $redTotal   += $red;
                $greenTotal += $green;
                $blueTotal  += $blue;

                $total++;
            }
        }
        $redAverage     = $total ? round($redTotal/$total) : 0;
        $greenAverage   = $total ? round($greenTotal/$total) : 0;
        $blueAverage    = $total ? round($blueTotal/$total) : 0;

        $json_data[$hash] = sprintf("#%02x%02x%02x", $redAverage, $greenAverage, $blueAverage);
        file_put_contents($json_file, json_encode($json_data));
        
        return sprintf("#%02x%02x%02x", $redAverage, $greenAverage, $blueAverage);
    }
    
    /**
     * Get the mime types instance.
     *
     * @return \Symfony\Component\Mime\MimeTypesInterface
     */
    public static function getMimeTypes()
    {
        if (self::$mime === null) {
            self::$mime = new MimeTypes();
        }

        return self::$mime;
    }

    /**
     * Get the MIME type for a file based on the file's extension.
     *
     * @param  string  $filename
     * @return string
     */
    public static function from($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        return self::get($extension);
    }

    /**
     * Get the MIME type for a given extension or return all mimes.
     *
     * @param  string  $extension
     * @return string
     */
    public static function get($extension)
    {
        return Arr::first(self::getMimeTypes()->getMimeTypes($extension)) ?? 'application/octet-stream';
    }

    public function getFilterData($category)
    {
        $productRepository = app('Webkul\Product\Repositories\ProductRepository');
        $attributeRepository = app('Webkul\Attribute\Repositories\AttributeRepository');
        $productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');

        $filterAttributes = [];
        $maxPrice = 0;

        if (isset($category)) {
            $products = $productRepository->getAll($category->id);

            $filterAttributes = $productFlatRepository->getFilterableAttributes($category, $products);

            $maxPrice = core()->convertPrice($productFlatRepository->getCategoryProductMaximumPrice($category));
        } 

        if (! count($filterAttributes) > 0) {
            $filterAttributes = $attributeRepository->getFilterAttributes();
        }

        foreach ($filterAttributes as $attribute) {
            if ($attribute->code <> 'price') {
                if (! $attribute->options->isEmpty()) {
                    $attributes[] = $attribute;
                }
            } else {
                $attributes[] = $attribute;
            }
        }

        $filterAttributes = collect($attributes);
        
        $formattedFilterArray = [];
        if ( $filterAttributes ) {
            foreach( $filterAttributes as $filterAttribute) {
                $attr = $filterAttribute->toArray();
                
                $options = [];
                if (! empty($attr['options']) ) {
                    foreach($attr['options'] as $option)
                    $options[] = [
                        'id'    => $option['id'],
                        'label' => $option['admin_name'],
                        'count' => 0,
                    ];
                }
                
                $formattedFilterArray[] = [
                    'code'      => $attr['code'],
                    'label'     => $attr['admin_name'],
                    'options'   => $options,
                ];
            }
        }
        return $formattedFilterArray;
    }

    public function getCarouselData()
    {
        $carousel = [];
        $velocityMetaData       = $this->velocityHelper->getVelocityMetaData();
        $home_page_templates    = explode("@include", $velocityMetaData->home_page_content);
        
        foreach ($home_page_templates as $index => $blade) {
            foreach ($this->templateArray as $template) {
                if ( Str::contains($blade, "'" . $template . "'") ) {
                    $blade  = str_replace(")", "", str_replace("(", "", $blade));
                    $formatted_template = "'" . $template . "',";
                    $getTempArray = explode($formatted_template, $blade);

                    // Category Array
                    if ( is_array($getTempArray) && count($getTempArray) > 1 ) {
                        $getTempArray[1]    = str_replace(" ", "", $getTempArray[1]);
                        $category_array     = explode("['category'=>", str_replace("]", "", $getTempArray[1]));

                        $category_index = isset($category_array[1]) ? $category_array[1] : [];
                        $categories = explode(",", $category_index);

                        if ( is_array($categories) && count($categories) > 1) {
                            
                        } else if (isset($categories[0]) ) {
                            $categoryDetails = $this->categoryRepository->findByPath(str_replace("'", "", $categories[0]));
                            
                            if ($categoryDetails) {
                                $products = $this->productRepository->getAll($categoryDetails->id);
                            
                                $carousel[]   = [
                                    'id'            => $index + 1,
                                    'type'          => 'category',
                                    'label'         => $categoryDetails->name,
                                    'color'         => '#000000',
                                    'image'         => '',
                                    'dominantColor' => "#fff2ef",
                                    'productList'   => $this->filterProductIndex($products),
                                ];
                            }
                        }                        
                    } else if ( isset($getTempArray[0]) ) {
                        // Normal Template
                        if ( trim($getTempArray[0]) == "'shop::home.new-products'" ) {
                            $new_products = $this->velocityProductRepository->getNewProducts($velocityMetaData->new_product_count);
                            
                            $carousel[]   = [
                                'id'            => $index + 1,
                                'type'          => 'product',
                                'label'         => 'New Products',
                                'color'         => '#000000',
                                'image'         => '',
                                'dominantColor' => "#fff2ef",
                                'productList'   => $this->filterProductIndex($new_products),
                            ];
                        }
                        
                        if ( trim($getTempArray[0]) == "'shop::home.featured-products'" ) {
                            $featured_products = $this->velocityProductRepository->getFeaturedProducts($velocityMetaData->featured_product_count);

                            $carousel[]   = [
                                'id'            => $index + 1,
                                'type'          => 'product',
                                'label'         => 'Featured Products',
                                'color'         => '#000000',
                                'image'         => '',
                                'dominantColor' => "#fff2ef",
                                'productList'   => $this->filterProductIndex($featured_products),
                            ];
                        }
                    }
                }
            }
        }

        return $carousel;
    }

    public function filterProductIndex($products)
    {
        $formattedProducts = [];

        $configurableOptionHelper   =   app('Webkul\Product\Helpers\ConfigurableOption');
        $productTypeHelper = app('Webkul\Product\Helpers\ProductType');

        foreach ($products as $product) {            
            $galleryImages = $this->productImageHelper->getProductBaseImage($product);

            $variants = new \stdClass();
            if ( $product->type == 'configurable' && $productTypeHelper->hasVariants($product->type)) {
                $variants = $configurableOptionHelper->getConfigurationConfig($product);
                $variants['index'] = json_encode($variants['index']);
            }
            
            if ( $product->type == 'bundle' ) {
                $variants = app('Webkul\Product\Helpers\BundleOption')->getBundleConfig($product);
            }

            if ( $product->type == 'grouped' ) {
                $variants = $product->grouped_products;
            }

            if ( $product->type == 'downloadable' ) {
                if ( $product->downloadable_samples->count() ) {
                    $variants->samples = $product->downloadable_samples;
                }
                if ( $product->downloadable_links->count() ) {
                    $variants->attributes = $product->downloadable_links;
                }
            }

            $isInWishlist = false;
            $wishlistItemId = 0;
            if ( auth('api')->check() ) {
                $wishlistProduct = $this->wishlistRepository->findOneWhere([
                    'channel_id'  => core()->getCurrentChannel()->id,
                    'customer_id' => auth('api')->user()->id,
                    'product_id'    => $product->product_id
                ]);

                if ( isset($wishlistProduct->id)) {
                    $isInWishlist = true;
                    $wishlistItemId = $wishlistProduct->id;
                }
            }
            
            $formattedProducts[] = [
                'configurableData'      => $variants,
                'isInWishlist'          => $isInWishlist,
                'wishlistItemId'        => $wishlistItemId,
                'typeId'                => $product->type,
                'entityId'              => $product->id,
                'shortDescription'      => $product->short_description,
                'rating'                => $this->reviewHelper->getAverageRating($product),
                'isAvailable'           => $product->getTypeInstance()->isSaleable(),
                'price'                 => $product->getTypeInstance()->getMinimalPrice(),
                'finalPrice'            => core()->convertPrice($product->getTypeInstance()->getMinimalPrice(), core()->getCurrentCurrencyCode()),
                'formattedPrice'        => core()->formatPrice($product->getTypeInstance()->getMinimalPrice(), core()->getBaseCurrencyCode()),
                'formattedFinalPrice'   => core()->currency($product->getTypeInstance()->getMinimalPrice()),
                'name'                  => $product->name,
                'hasRequiredOptions'    => false,
                'isNew'                 => $product->new,
                'isInRange'             => false,
                'thumbNail'             => $galleryImages['smallImage'],
                'minAddToCartQty'       =>  1
            ];
        }

        return $formattedProducts;
    }

    public function getProductFormattedArray($product)
    {
        $formattedProducts = [];
        $configurableOptionHelper   =   app('Webkul\Product\Helpers\ConfigurableOption');
        $productTypeHelper = app('Webkul\Product\Helpers\ProductType');
        
        $galleryImages = $this->productImageHelper->getProductBaseImage($product);

        $variants = new \stdClass();
        if ( $product->type == 'configurable' && $productTypeHelper->hasVariants($product->type)) {
            $variants = $configurableOptionHelper->getConfigurationConfig($product);
            $variants['index'] = json_encode($variants['index']);
        }
        
        if ( $product->type == 'bundle' ) {
            $variants = app('Webkul\Product\Helpers\BundleOption')->getBundleConfig($product);
        }

        if ( $product->type == 'grouped' ) {
            $variants = $product->grouped_products;
        }

        if ( $product->type == 'downloadable' ) {
            if ( $product->downloadable_samples->count() ) {
                $variants->samples = $product->downloadable_samples;
            }
            if ( $product->downloadable_links->count() ) {
                $variants->attributes = $product->downloadable_links;
            }
        }

        $isInWishlist = false;
        $wishlistItemId = 0;
        if ( auth('customer')->check() ) {
            $wishlistProduct = $this->wishlistRepository->findOneWhere([
                'channel_id'  => core()->getCurrentChannel()->id,
                'customer_id' => auth('customer')->user()->id,
                'product_id'    => $product->product_id
            ]);

            if ( isset($wishlistProduct->id)) {
                $isInWishlist = true;
                $wishlistItemId = $wishlistProduct->id;
            }
        }
        
        $formattedProducts = [
            'reviewCount'           => $this->reviewHelper->getTotalReviews($product),
            'configurableData'      => $variants,
            'isInWishlist'          => $isInWishlist,
            'wishlistItemId'        => $wishlistItemId,
            'typeId'                => $product->type,
            'entityId'              => $product->id,
            'shortDescription'      => $product->short_description,
            'rating'                => $this->reviewHelper->getAverageRating($product),
            'isAvailable'           => $product->getTypeInstance()->isSaleable(),
            'price'                 => $product->getTypeInstance()->getMinimalPrice(),
            'finalPrice'            => core()->convertPrice($product->getTypeInstance()->getMinimalPrice(), core()->getCurrentCurrencyCode()),
            'formattedPrice'        => core()->formatPrice($product->getTypeInstance()->getMinimalPrice(), core()->getBaseCurrencyCode()),
            'formattedFinalPrice'   => core()->currency($product->getTypeInstance()->getMinimalPrice()),
            'name'                  => $product->name,
            'hasRequiredOptions'    => false,
            'isNew'                 => $product->new,
            'isInRange'             => false,
            'thumbNail'             => $galleryImages['smallImage'],
            'dominantColor'         => $this->getImageDominantColor($galleryImages['smallImage']),
            'tierPrice'             => '',
            'formattedTierPrice'    => '',
            'minAddToCartQty'       =>  1,
            'availability'          => $product->getTypeInstance()->isSaleable() ? 'In Stock' : 'Out Of Stock',
            'arUrl'                 => '',
            'arType'                => '2D',
            'arTextureImages'       => [],
        ];
        
        return $formattedProducts;
    }

    /**
     * Returns latest added product
     *
     * @return \Illuminate\Support\Collection
     */
    public function getLatestProducts($limit)
    {
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            return $query->distinct()
                            ->addSelect('product_flat.*')
                            ->where('product_flat.status', 1)
                            ->where('product_flat.visible_individually', 1)
                            ->where('product_flat.new', 1)
                            ->where('product_flat.channel', $channel)
                            ->where('product_flat.locale', $locale)
                            ->orderBy('product_id', 'desc');
        })->paginate($limit);

        return $results;
    }
        
    /**
     * Returns matched products
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSearchSuggestion($params)
    {
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) use ($params) {
            $channel = core()->getDefaultChannelCode();
            if ( isset($params['storeId']) && $params['storeId'] ) {
                $channelRepository = app('Webkul\Core\Repositories\ChannelRepository')->find($params['storeId']);
                if ( isset($channelRepository->id) ) {
                    $channel = $channelRepository->code;
                }
            }

            $locale = request()->get('locale') ?: app()->getLocale();

            $qb = $query->distinct()
                        ->addSelect('product_flat.*')
                        ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
                        ->leftJoin('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->where('product_flat.channel', $channel)
                        ->where('product_flat.locale', $locale)
                        ->whereNotNull('product_flat.url_key')
                        ->where('product_flat.name', 'like', '%' . urldecode($params['searchQuery']) . '%');

                        if ( isset($params['categoryId']) && $params['categoryId']) {
                            $qb->where('product_categories', $params['categoryId']);
                        }
                return $qb->groupBy('product_flat.id');
            })->paginate(isset($params['limit']) ? $params['limit'] : 9);

        return $results;
    }
    
    /**
     * Returns products list filtered by price range
     *
     * @return \Illuminate\Support\Collection
     */
    public function getProductsByPriceRange($data)
    {
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) use ($data) {
            $channel = request()->get('channel') ?: (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

            $locale = request()->get('locale') ?: app()->getLocale();

            return $query->distinct()
                            ->addSelect('product_flat.*')
                            ->where('product_flat.status', 1)
                            ->where('product_flat.visible_individually', 1)
                            ->where('product_flat.new', 1)
                            ->where('product_flat.channel', $channel)
                            ->where('product_flat.locale', $locale)
                            ->where('product_flat.min_price', '>=', core()->convertToBasePrice($data['price_from']))
                            ->where('product_flat.min_price', '<=', core()->convertToBasePrice($data['price_to']))
                            ->orderBy('min_price', 'asc');
        })->get();

        return $results;
    }

    /**
     * Returns mobikul featured product
     *
     * @param array  $data
     * @return \Illuminate\Support\Collection
     */
    public function isMobikulFeaturedProduct($data = array())
    {
        $products = [];
        $attribute = $this->attributeRepository->findOneByField('code', 'is_mobikul_featured');

        if ( isset($attribute->id)) {

            $productAttributeValues = app('Webkul\Product\Repositories\ProductAttributeValueRepository')->findWhere([
                'attribute_id'  => $attribute->id,
            ]);

            $productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');
            foreach($productAttributeValues as $productAttributeValue) {
                $products[] = $productFlatRepository->findOneWhere([
                    'product_id'    => $productAttributeValue->product_id,
                    'parent_id'     => NULL,
                    'status'        => 1,
                    'channel'       => $data['channel'],
                    'locale'        => $data['locale'],
                ]);
            }
        }
        
        return $products;
    }

    /**
    * Returns top offered products
    *
    * @param int    $count
    * @param array  $data
    * @return \Illuminate\Support\Collection
    */
    public function getTopOfferedProducts($limit, $data = array())
    {
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) use ($data) {
            $now = Carbon::now()->format('Y-m-d');

            return $query->distinct()
                ->addSelect('product_flat.*')
                ->where('product_flat.status', 1)
                ->where('product_flat.visible_individually', 1)
                ->where('product_flat.channel', $data['channel'])
                ->where('product_flat.locale', $data['locale'])
                ->whereNotNull('product_flat.special_price')
                ->where('product_flat.special_price_to', '>=', $now)
                ->where('product_flat.special_price_from', '<=', $now)
                ->orderBy('product_flat.special_price', 'desc');
        })->paginate($limit);

        return $results;
    }

    /**
     * Search Brand by Attribute
     *
     * @param  string  $term
     * @return \Illuminate\Support\Collection
     */
    public function searchBrandAttributes($term)
    {
        $results = app('\Webkul\Attribute\Repositories\AttributeRepository')->scopeQuery(function($query) use($term) {
            
            return $query->distinct()
                        ->select('attributes.id', 'attributes.code', 'attribute_option_translations.label', 'attribute_option_translations.attribute_option_id')
                        
                        ->leftJoin('attribute_options', 'attribute_options.attribute_id', '=', 'attributes.id')
                            
                        ->leftJoin('attribute_option_translations', function($leftJoin) {
                            $leftJoin->on('attribute_options.id', '=', 'attribute_option_translations.attribute_option_id')
                                ->where('attribute_option_translations.locale', app()->getLocale());
                        })
                        ->where('attributes.code', 'brand')
                        ->where(function($sub_query) use ($term) {  
                            $sub_query->where('attribute_option_translations.label', 'like', '%' . urldecode($term) . '%')
                            ->where('attribute_option_translations.locale', app()->getLocale());
                        });
        })->paginate(5);

        return $results;
    }

    /**
     * Search Collection
     *
     * @param  string  $term
     * @return \Illuminate\Support\Collection
     */
    public function searchCollections($term)
    {
        $customCollectionRepository = app('\Webkul\Mobikul\Repositories\CustomCollectionRepository');
        
        $customCollections = $customCollectionRepository->where('name', 'like', '%' . urldecode($term) . '%')->get();

        return $customCollections;
    }    
    
    public function getProductName($product_ids)
    {
        $nameArray = [];
        foreach($product_ids as $product_id) {
            $product = $this->productRepository->findOrFail($product_id);

            if ( isset($product->id)) {
                $nameArray[$product->id] = $product->name;
            }
        }

        return implode(", ", $nameArray);
    }
    
    public function getBrandOptionName($brand_option_id)
    {
        $brand_name = '';
        $attribute = $this->attributeRepository->getAttributeByCode('brand');

        if ( isset($attribute->id)) {
            $option = $attribute->options()->where('id', $brand_option_id)->first();

            if ( isset($option->id)) {
                $brand_name = $option->admin_name;
            }
        }

        return $brand_name;
    }

    /**
     * Validate order before creation
     *
     * @return void|\Exception
     */
    public function validateOrder()
    {
        $cart = Cart::getCart();

        if ($cart->haveStockableItems() && ! $cart->shipping_address) {
            return $result = [
                'success'   => false,
                'message'   => trans('Please check shipping address.'),
            ];
        }

        if (! $cart->billing_address) {
            return $result = [
                'success'   => false,
                'message'   => trans('Please check billing address.'),
            ];
        }

        if ($cart->haveStockableItems() && ! $cart->selected_shipping_rate) {
            return $result = [
                'success'   => false,
                'message'   => trans('Please specify shipping method.'),
            ];
        }

        if (! $cart->payment) {
            return $result = [
                'success'   => false,
                'message'   => trans('Please specify payment method.'),
            ];
        }
        
        return $result = [
            'success'   => true,
            'message'   => '',
        ];
    }
    
    public function getProductTypeCast($product)
    {
        $productVarients = new \stdClass();

        switch ($product->type) {
            case 'configurable':
                $configurableOptionHelper   = app('Webkul\Product\Helpers\ConfigurableOption');
                $productTypeHelper  = app('Webkul\Product\Helpers\ProductType');

                if ( $productTypeHelper->hasVariants($product->type) ) {
                    $productVarients = $configurableOptionHelper->getConfigurationConfig($product);
                    
                    if ( isset($productVarients['regular_price']['price'])) {
                        $productVarients['regular_price']['price'] = (float) $productVarients['regular_price']['price'];
                    }

                    foreach ($productVarients['variant_prices'] as $key => $priceArray) {
                        if ( isset($priceArray['regular_price']['price'])) {
                            $productVarients['variant_prices'][$key]['regular_price']['price'] = (float) $priceArray['regular_price']['price'];
                        }
                        if ( isset($priceArray['final_price']['price'])) {
                            $productVarients['variant_prices'][$key]['final_price']['price'] = (float) $priceArray['final_price']['price'];
                        }
                    }

                    $productVarients['index'] = json_encode($productVarients['index']);
                }
                break;
            
            case 'bundle':
                $productVarients = app('Webkul\Product\Helpers\BundleOption')->getBundleConfig($product);
                break;
            
            case 'grouped':
                $productVarients->attributes = $product->grouped_products;
                break;
        
            case 'downloadable':
                if ( $product->downloadable_samples->count() ) {
                    $productVarients->samples = $product->downloadable_samples;
                }
                if ( $product->downloadable_links->count() ) {
                    $productVarients->attributes = [];
                    foreach ($product->downloadable_links as $key => $downloadable_link) {
                        if ( isset($downloadable_link['price'])) {
                            $downloadable_link['price'] = (float) $downloadable_link['price'];
                        }
                        $productVarients->attributes[] = $downloadable_link;
                    }
                }
                break;

            default:
                $productVarients = new \stdClass();
                break;
        }

        return $productVarients;
    }

    public function getProductPrice($product)
    {
        $result = [
            'price'         => $product->price,
            'specialPrice'  => 0
        ];

        switch ($product->type) {
            case 'simple':
                $result['price'] = $product->price;
                if ( $product->getTypeInstance()->haveSpecialPrice() ) {
                    $result['specialPrice'] = $product->getTypeInstance()->getSpecialPrice();
                }
                break;
            case 'configurable':
                $result['price'] = $product->getTypeInstance()->getMinimalPrice();
                break;
            
            default:
                # code...
                break;
        }
        
        return $result;
    }
}