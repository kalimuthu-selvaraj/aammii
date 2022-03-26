<?php

namespace Webkul\Mobikul\Listeners;

use Webkul\Mobikul\Repositories\BannerImageTranslationRepository;

class BannerTranslation
{
    /**
     * BannerImageTranslationRepository Repository Object
     *
     * @var \Webkul\Mobikul\Repositories\BannerImageTranslationRepository
     */
    protected $bannerImageTranslationRepository;

    /**
     * Create a new listener instance.
     *
     * @param  \Webkul\Mobikul\Repositories\BannerImageTranslationRepository  $bannerImageTranslationRepository
     * @return void
     */
    public function __construct(
        BannerImageTranslationRepository $bannerImageTranslationRepository
    )   {
        $this->bannerImageTranslationRepository = $bannerImageTranslationRepository;
    }

    /**
     * Creates Banner Translation
     *
     * @param  \Webkul\Mobikul\Contracts\BannerImage  $bannerImage
     * @return void
     */
    public function afterBannerImageCreatedUpdated($bannerImage)
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
                    $bannerTranslation = $this->bannerImageTranslationRepository->findOneWhere([
                        'mobikul_banner_id' => $bannerImage->id,
                        'channel'           => $channel->code,
                        'locale'            => $locale->code,
                    ]);

                    if (! $bannerTranslation) {
                        $bannerTranslation = $this->bannerImageTranslationRepository->create([
                            'name'              => $data['name'],
                            'mobikul_banner_id' => $bannerImage->id,
                            'channel'           => $channel->code,
                            'locale'            => $locale->code,
                        ]);
                    }
                    
                    if ( $channel_request && ($channel->code == $channel_request) && $locale_request && ($locale->code == $locale_request) ) {
                        $bannerTranslation->name = $data['name'];
                        $bannerTranslation->save();
                    }
                }
            } else {
                $route = request()->route() ? request()->route()->getName() : "";

                if ($route == 'mobikul.banner-image.update') {
                    $bannerTranslations = $this->bannerImageTranslationRepository->findWhere([
                        'mobikul_banner_id' => $bannerImage->id,
                        'channel'           => $channel->code,
                    ])->pluck('id')->toArray();
                    
                    if ( $bannerTranslations ) {
                        $this->bannerImageTranslationRepository->destroy($bannerTranslations);
                    }
                }
            }
        }
    }
}