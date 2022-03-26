<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewDetails extends JsonResource
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
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');

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

        $productId = $this->product_id;
        
        $productFlat = $this->productFlatRepository->findOneWhere([
            'channel'       => $channel->code,
            'locale'        => $this->localeCode,
            'product_id'    => $productId
        ]);

        if (! $productFlat ) {
            return [
                'success'   => false,
                'message'   => trans('mobikul-api::app.api.customer.review.error-no-product'),
            ];
        }

        $product = $productFlat->product;

        $productBaseImage = productimage()->getProductBaseImage($product);
        
        $averageRating = $this->reviewHelper->getAverageRating($product);
        
        $totalReviews = $this->reviewHelper->getTotalReviews($product);

        return [
            'success'               => true,
            'message'               => "",
            'thumbNail'             => $productBaseImage['medium_image_url'],
            'productName'           => $productFlat->name,
            'dominantColor'         => mobikulApi()->getImageDominantColor($productBaseImage['medium_image_url']),
            'ratingData'            => [
                'ratingCode'            => "Rating",
                'ratingValue'           => $this->rating
            ],
            'reviewDate'            => trans('mobikul-api::app.api.customer.review.review-date', ['review_date' => $this->created_at]),
            'reviewTitle'           => $this->title,
            'reviewDetail'          => $this->comment,
            'averageRating'         => $averageRating,
            'totalProductReviews'   => $totalReviews
        ];
    }
}