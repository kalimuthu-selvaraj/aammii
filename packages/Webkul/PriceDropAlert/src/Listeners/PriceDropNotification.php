<?php

namespace Webkul\PriceDropAlert\Listeners;

use Illuminate\Database\Schema\Blueprint;
use Webkul\PriceDropAlert\Repositories\PriceDropSubscriptionRepository;
use Webkul\Product\Repositories\ProductFlatRepository;
use Illuminate\Support\Facades\Mail;
use Webkul\PriceDropAlert\Mail\PriceDropAlertEmail;
use Illuminate\Support\Facades\Log;

class PriceDropNotification
{
    /**
     * PriceDropSubscriptionRepository Repository Object
     *
     * @var \Webkul\PriceDropAlert\Repositories\PriceDropSubscriptionRepository
     */
    protected $priceDropSubscriptionRepository;
    
    /**
     * ProductFlat Repository Object
     *
     * @var \Webkul\Product\Repositories\ProductFlatRepository
     */
    protected $productFlatRepository;

    /**
     * Create a new listener instance.
     *
     * @param  \Webkul\PriceDropAlert\Repositories\PriceDropSubscriptionRepository  $priceDropSubscriptionRepository
     * @param  \Webkul\Product\Repositories\ProductFlatRepository  $productFlatRepository
     * @return void
     */
    public function __construct(
        PriceDropSubscriptionRepository $priceDropSubscriptionRepository,
        ProductFlatRepository $productFlatRepository
    )   {
        $this->priceDropSubscriptionRepository = $priceDropSubscriptionRepository;
        
        $this->productFlatRepository = $productFlatRepository;
    }

    public function afterProductUpdate($product)
    {
        if ( isset($product->id) ) {
            $subscribers = $this->priceDropSubscriptionRepository->findWhere([
                'product_id'    => $product->id,
                'status'        => 1,
            ])->where('base_price', '>', $product->getTypeInstance()->getMinimalPrice());

            if ( $subscribers ) {
                foreach($subscribers as $subscriber) {
                                
                    $mailData = [
                        'subscriber_email'      => $subscriber->email,
                        'product_name'          => $product->name,
                        'product_price'         => core()->formatPrice($subscriber->base_price, core()->getChannelBaseCurrencyCode()),
                        'current_product_price' => core()->formatPrice($product->price, core()->getChannelBaseCurrencyCode()),
                    ];

                    try {
                        Mail::queue(new PriceDropAlertEmail($mailData));
                    } catch (\Exception $e) {
                        Log::error('PriceDropAlert Email : ' . $e->getMessage());
                    }
                }
            }
        }
    }
}