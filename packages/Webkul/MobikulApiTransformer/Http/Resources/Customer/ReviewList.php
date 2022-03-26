<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewList extends JsonResource
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
     * Contains product review list.
     *
     * @var array
     */
    protected $reviewList = [];

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');

        $this->productReviewRepository = app('Webkul\Product\Repositories\ProductReviewRepository');

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

        $params = [
            'customer_id'   => $this['customer_id'],
            'limit'         => isset($this['limit']) ? $this['limit'] : core()->getConfigData('mobikul.mobikul.basicinformation.current_page_size')
        ];

        $request->merge(['page' => (isset($this['pageNumber']) && $this['pageNumber']) ? $this['pageNumber'] : 1]);

        $customerReviews = $this->productReviewRepository->scopeQuery(function($query) use ($params) {
            return $query->distinct()  
                            ->addSelect('product_reviews.*')
                            ->where('product_reviews.customer_id', $params['customer_id'])
                            ->where('product_reviews.status', 'approved')
                            ->orderBy('product_reviews.id', 'desc');
        })->paginate($params['limit']);
        
        if ( $customerReviews ) {
            foreach ($customerReviews as $review) {
                $productFlat = $this->productFlatRepository->findOneWhere([
                    'channel'       => $channel->code,
                    'locale'        => $this->localeCode,
                    'product_id'    => $review->product_id
                ]);

                if ( $productFlat ) {
                    $product = $productFlat->product;

                    $productBaseImage = productimage()->getProductBaseImage($product);
                    
                    $this->reviewList[] = [
                        'id'            => $review->id,
                        'productName'   => $productFlat->name,
                        'customerRating'=> $review->rating,
                        'productId'     => $review->product_id,
                        'thumbNail'     => $productBaseImage['medium_image_url'],
                        'dominantColor' => mobikulApi()->getImageDominantColor($productBaseImage['medium_image_url']),
                    ];
                }
            }
        }

        return [
            'totalCount' => count($this->reviewList),
            'reviewList' => $this->reviewList,
            'message'    => "",
            'success'    => true,
            'eTag'       => "3b03695b38022647cf1d08cc6998a5c1",
        ];
    }
}