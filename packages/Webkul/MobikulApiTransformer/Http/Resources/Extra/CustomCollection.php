<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Extra;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomCollection extends JsonResource
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
     * Contains custom product list.
     *
     * @var array
     */
    protected $customProductList = [];

    /**
     * Contains custom collection list.
     *
     * @var array
     */
    protected $customCollectionList = [];

    /**
     * Contains sorting data.
     *
     * @var array
     */
    protected $sortingData = [];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');
        
        $this->customCollectionRepository = app('Webkul\Mobikul\Repositories\CustomCollectionRepository');

        $this->productRepository = app('Webkul\Product\Repositories\ProductRepository');

        $this->productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');
        
        $this->toolbarHelper = app('Webkul\Product\Helpers\Toolbar');

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

        $customCollections   = $this->customCollectionRepository->findByField('status', 1);

        if ( count($customCollections) == 0 ) {
            return [
                'success'   => false,
                'message'   => trans('mobikul-api::app.api.extra.custom-collection.error-not-found'),
            ];
        }
        
        foreach($customCollections as $customCollection) {
            $customCollectionArray = [
                'id'    => $customCollection->id,
                'name'  => $customCollection->name,
                'type'  => $customCollection->product_collection,
            ];
            
            switch ($customCollection->product_collection) {
                case 'product_ids':
                    $productList = [];
                    $product_ids = json_decode($customCollection->product_ids, true);

                    foreach($product_ids as $product_id) {
                        $productFlat = $this->productFlatRepository->findOneWhere([
                            'product_id'    => $product_id,
                            'channel'       => $channel->code,
                            'locale'        => $this->localeCode,
                            'status'        => 1
                        ]);

                        if ( $productFlat ) {
                            $productList[] = mobikulApi()->getProductFormattedArray($productFlat->product);
                            $this->customProductList[$product_id] = mobikulApi()->getProductFormattedArray($productFlat->product);
                        }
                    }
                    
                    $customCollectionArray['product_ids']   = json_decode($customCollection->product_ids, true);
                    $customCollectionArray['totalCount']    = count($productList);
                    $customCollectionArray['productList']   = $productList;
                    break;
                
                case 'latest_product_count':
                    $productList = [];
                    $products = mobikulApi()->getLatestProducts($customCollection->latest_count);
                    
                    if ( $products ) {
                        foreach($products as $productFlat) {
                            if ( $productFlat ) {
                                $productList[] = mobikulApi()->getProductFormattedArray($productFlat->product);
                                    
                                $this->customProductList[$productFlat->product_id] = mobikulApi()->getProductFormattedArray($productFlat->product);
                            }
                        }
                    }
                    
                    $customCollectionArray['latest_product_count'] = $customCollection->latest_count;
                    $customCollectionArray['totalCount']    = count($productList);
                    $customCollectionArray['productList']   = $productList;
                    break;

                case 'product_attributes':
                    $productList = [];
                    $customCollectionArray['product_attributes'] = $customCollection->attributes;
                    if ( $customCollection->attributes == 'price' ) {
                        $products = mobikulApi()->getProductsByPriceRange([
                            'price_from'    => $customCollection->price_from,
                            'price_to'      => $customCollection->price_to,
                        ]);
                    
                        if ( $products ) {
                            foreach($products as $productFlat) {
                                if ( $productFlat ) {
                                    $productList[] = mobikulApi()->getProductFormattedArray($productFlat->product);
                                    
                                    $this->customProductList[$productFlat->product_id] = mobikulApi()->getProductFormattedArray($productFlat->product);
                                }
                            }
                        }
                        $customCollectionArray['price_from'] = $customCollection->price_from;
                        $customCollectionArray['price_to'] = $customCollection->price_to;
                    } elseif ( $customCollection->attributes == 'brand' ) {
                        $request->merge([
                            'brand' => $customCollection->brand,
                        ]);
                        $products = $this->productRepository->getAll();
                    
                        if ( $products ) {
                            foreach($products as $productFlat) {
                                if ( $productFlat ) {
                                    $productList[] = mobikulApi()->getProductFormattedArray($productFlat->product);
                                    
                                    $this->customProductList[$productFlat->product_id] = mobikulApi()->getProductFormattedArray($productFlat->product);
                                }
                            }
                        }
                    
                        $customCollectionArray['brand'] = $customCollection->brand;
                    } elseif ( $customCollection->attributes == 'sku' ) {
                        $product = $this->productRepository->findOneByField('sku', $customCollection->sku);
                        
                        if ( $product ) {
                            $productList[] = mobikulApi()->getProductFormattedArray($product);
                                    
                            $this->customProductList[$product->id] = mobikulApi()->getProductFormattedArray($product);
                        }
                        
                        $customCollectionArray['sku'] = $customCollection->sku;
                    }
                    
                    $customCollectionArray['attributes_type'] = $customCollection->attributes;
                    $customCollectionArray['totalCount']    = count($productList);
                    $customCollectionArray['productList']   = $productList;
                    
                    break;
                
                default:
                
                    break;
            }

            $this->customCollectionList[] = $customCollectionArray;
        }
        
        foreach($this->toolbarHelper->getAvailableOrders() as $code => $sort) {
            $this->sortingData[] = [
                'code'  => $code,
                'label' => $sort,
            ];
        }
        
        return [
            'success'               => true,
            'message'               => '',
            'customCollectionList'  => $this->customCollectionList,
            'totalCount'            => count($this->customProductList),
            'productList'           => $this->customProductList,
            'layeredData'           => [],
            'sortingData'           => $this->sortingData,
        ];
    }
}

