<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Extra;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchList extends JsonResource
{
    /**
     * Contains current currency
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * Contains searched product list.
     *
     * @var array
     */
    protected $searchResult = [];

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
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
        $this->currencyCode = request()->input('currency');

        $params = $this['data'];
        $results = mobikulApi()->getSearchSuggestion($params);
        
        if ( $results->count() ) {
            foreach($results as $productFlat) {
                
                $product = $productFlat->product;
                $productBaseImage = productimage()->getProductBaseImage($product);

                $this->searchResult[] = [
                    'id'                => $productFlat->product->id,
                    'name'              => $productFlat->name,
                    'price'             => $product->price,
                    'finalPrice'        => core()->convertPrice($product->price, $this->currencyCode),
                    'formattedPrice'    => core()->formatPrice($product->price, core()->getBaseCurrencyCode()),
                    'formattedFinalPrice'=> core()->formatPrice(core()->convertPrice($product->price, $this->currencyCode), $this->currencyCode),
                    'specialPrice'      => $product->getTypeInstance()->haveSpecialPrice() ? $product->getTypeInstance()->getSpecialPrice() : 0,
                    'hasSpecialPrice'   => $product->getTypeInstance()->haveSpecialPrice(),
                    'thumbNail'         => $productBaseImage['original_image_url'],
                    'dominantColor'     => mobikulApi()->getImageDominantColor($productBaseImage['original_image_url']),
                ];
            }

            return [
                'success' => true,
                'message' => 'Success: matching result.',
                'suggestionProductArray'    => [
                    'tags'      => [],
                    'products'  => $this->searchResult,
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Warning: no matching result found.',
            ];
        }
    }
}

