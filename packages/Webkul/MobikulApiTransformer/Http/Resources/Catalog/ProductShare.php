<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Catalog;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Mail;
use Webkul\MobikulApiTransformer\Mail\ProductShareEmail;

class ProductShare extends JsonResource
{
    /**
     * Contains current channel
     *
     * @var string
     */
    protected $channel;

    /**
     * Contains current locale
     *
     * @var string
     */
    protected $localeCode;

    /**
     * Contains the count of receiver.
     *
     * @var int
     */
    protected $receiverCount = 0;

    /**
     * Contains the email send status
     *
     * @var boolean
     */
    protected $responseStatus = false;

    /**
     * Contains the email send message
     *
     * @var string
     */
    protected $responseMessage = '';

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->productFlatRepository = app('Webkul\Product\Repositories\ProductFlatRepository');

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
        $this->localeCode = request()->input('locale');

        $channel = $this->channelRepository->find($this->channel);
        
        $productFlat = $this->productFlatRepository->findOneWhere([
            'product_id'    => $this['productId'],
            'channel'       => $channel->code,
            'locale'        => $this->localeCode
            ]);

        if (! $productFlat ) {
            $this->responseMessage = trans('mobikul-api::app.api.catalog.add-to-compare.error-request');
        }

        if ( $productFlat ) {
            try {
                foreach ($this['recipientEmail'] as $key => $email) {
                    if ( isset($this['recipientName'][$key])) {
                        $data = [
                            'senderName'    => $this['customerName'],
                            'senderEmail'   => $this['customerEmail'],
                            'receiverName'  => trim($this['recipientName'][$key]),
                            'receiverEmail' => trim($email),
                            'productName'   => $productFlat->name,
                            'urlKey'        => $productFlat->url_key
                        ];

                        Mail::queue(new ProductShareEmail($data));

                        $this->receiverCount    += 1;
                    }
                }
                
                if ( $this->receiverCount ) {
                    $this->responseStatus = true;
                    $this->responseMessage = trans('mobikul-api::app.api.catalog.product-share.success-email-send', [
                        'total' => count($this['recipientEmail']),
                        'send'  => $this->receiverCount,
                    ]);
                } else {
                    $this->responseMessage = trans('mobikul-api::app.api.catalog.product-share.error-email-send');
                }
            } catch (\Exception $e) {
                $this->responseMessage = $e->getMessage();
            }
        }
       
        return [
            'success' => $this->responseStatus,
            'message' => $this->responseMessage,
        ];
    }
}