<?php

namespace Webkul\Mobikul\Listeners;

use Webkul\Mobikul\Repositories\NotificationTranslationRepository;

class NotificationTranslation
{
    /**
     * NotificationTranslationRepository Repository Object
     *
     * @var \Webkul\Mobikul\Repositories\NotificationTranslationRepository
     */
    protected $notificationTranslationRepository;

    /**
     * Create a new listener instance.
     *
     * @param  \Webkul\Mobikul\Repositories\NotificationTranslationRepository  $notificationTranslationRepository
     * @return void
     */
    public function __construct(
        NotificationTranslationRepository $notificationTranslationRepository
    )   {
        $this->notificationTranslationRepository = $notificationTranslationRepository;
    }

    /**
     * Creates notification translation
     *
     * @param  \Webkul\Mobikul\Contracts\Notification  $notification
     * @return void
     */
    public function afterNotificationCreatedUpdated($notification)
    {
        $channel_request = request()->get('channel') ?: '';
        $locale_request = request()->get('locale') ?: '';

        $data = request()->all();
        
        if (isset($data['channels'])) {
            $channels = $data['channels'];
        } else {
            $channels[] = core()->getDefaultChannelCode();
        }
        
        foreach (core()->getAllChannels() as $channel) {
            if (in_array($channel->code, $channels)) {
                foreach ($channel->locales as $locale) {
                    $notificationTranslation = $this->notificationTranslationRepository->findOneWhere([
                        'mobikul_notification_id'   => $notification->id,
                        'channel'                   => $channel->code,
                        'locale'                    => $locale->code,
                    ]);

                    if (! $notificationTranslation) {
                        $notificationTranslation = $this->notificationTranslationRepository->create([
                            'mobikul_notification_id'   => $notification->id,
                            'title'                     => $data['title'],
                            'content'                   => $data['content'],
                            'locale'                    => $locale->code,
                            'channel'                   => $channel->code,
                        ]);
                    }

                    if ( $channel_request && ($channel->code == $channel_request) && $locale_request && ($locale->code == $locale_request) ) {
                        $notificationTranslation->title = $data['title'];
                        $notificationTranslation->content = $data['content'];
                        $notificationTranslation->save();
                    }
                }
            } else {
                $route = request()->route() ? request()->route()->getName() : "";

                if ($route == 'mobikul.notification.update') {
                    $notificationTranslation = $this->notificationTranslationRepository->findWhere([
                        'mobikul_notification_id'   => $notification->id,
                        'channel'                   => $channel->code,
                    ])->pluck('id')->toArray();
                    
                    if ( $notificationTranslation ) {
                        $this->notificationTranslationRepository->destroy($notificationTranslation);
                    }
                }
            }
        }
    }
}