<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Extra;

use Illuminate\Http\Resources\Json\JsonResource;

class Notification extends JsonResource
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
     * Contains notification list.
     *
     * @var array
     */
    protected $notificationList = [];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->notificationsRepository = app('Webkul\Mobikul\Repositories\NotificationsRepository');

        $this->notificationTranslationRepository = app('Webkul\Mobikul\Repositories\NotificationTranslationRepository');

        $this->productRepository = app('Webkul\Product\Repositories\ProductRepository');

        $this->categoryRepository = app('Webkul\Category\Repositories\CategoryRepository');

        $this->customCollectionRepository = app('Webkul\Mobikul\Repositories\CustomCollectionRepository');
        
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

        $notifications   = $this->notificationsRepository->findByField('status', 1);
        if ( count($notifications) ) {
            foreach ($notifications as $notification) {
                
                $notificationTranslation = $this->notificationTranslationRepository->findOneWhere([
                    'mobikul_notification_id'   => $notification->id,
                    'channel'                   => $channel->code,
                    'locale'                    => $this->localeCode
                ]);
                
                if ( $notificationTranslation ) {
                    $notificationArray = [
                        'id'                => $notification->id,
                        'title'             => $notificationTranslation->title,
                        'content'           => $notificationTranslation->content,
                        'notificationType'  => $notification->type,
                        'banner'            => $notification->image_url,
                        'dominantColor'     => mobikulApi()->getImageDominantColor($notification->image_url),
                    ];

                    if ( $notification->type == 'product') {
                        $product = $this->productRepository->findOrFail($notification->product_category_id);

                        if ( $product ) {
                            $notificationArray['productName']   = $product->name;
                            $notificationArray['productType']   = $product->type;
                            $notificationArray['productId']     = $product->id;
                        }
                    }

                    if ( $notification->type == 'category') {
                        $category = $this->categoryRepository->findOrFail($notification->product_category_id);

                        if ( $category ) {
                            $notificationArray['categoryName']   = $category->name;
                            $notificationArray['categoryId']     = $category->id;
                        }
                    }

                    if ( $notification->type == 'custom_collection') {
                        $customCollection = $this->customCollectionRepository->findOrFail($notification->product_category_id);

                        if ( $customCollection ) {
                            $notificationArray['notificationType']      = 'custom';
                            $notificationArray['customCollectionName']  = $customCollection->name;
                            $notificationArray['customCollectionId']    = $customCollection->id;
                        }
                    }

                    $this->notificationList[] = $notificationArray;
                }
            }
        } else {

            return [
                'success'   => false,
                'message'   => trans('mobikul-api::app.api.extra.notification.error-not-found'),
            ];
        }
        
        return [
            'success'               => true,
            'message'               => '',
            'notificationList'      => $this->notificationList
       ];
    }
}
