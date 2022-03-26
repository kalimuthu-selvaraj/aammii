<?php

namespace Webkul\Mobikul\Listeners;

use Webkul\Mobikul\Repositories\CarouselTranslationRepository;

class CarouselTranslation
{
    /**
     * CarouselTranslationRepository Repository Object
     *
     * @var \Webkul\Mobikul\Repositories\CarouselTranslationRepository
     */
    protected $carouselTranslationRepository;

    /**
     * Create a new listener instance.
     *
     * @param  \Webkul\Mobikul\Repositories\CarouselTranslationRepository  $carouselTranslationRepository
     * @return void
     */
    public function __construct(
        CarouselTranslationRepository $carouselTranslationRepository
    )   {
        $this->carouselTranslationRepository = $carouselTranslationRepository;
    }

    /**
     * Creates carousel translation
     *
     * @param  \Webkul\Mobikul\Contracts\Carousel  $carousel
     * @return void
     */
    public function afterCarouselCreatedUpdated($carousel)
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
                    $carouselTranslation = $this->carouselTranslationRepository->findOneWhere([
                        'mobikul_carousel_id'   => $carousel->id,
                        'channel'               => $channel->code,
                        'locale'                => $locale->code,
                    ]);

                    if (! $carouselTranslation) {
                        $carouselTranslation = $this->carouselTranslationRepository->create([
                            'mobikul_carousel_id'   => $carousel->id,
                            'title'                 => $data['title'],
                            'locale'                => $locale->code,
                            'channel'               => $channel->code,
                        ]);
                    }
                    
                    if ( $channel_request && ($channel->code == $channel_request) && $locale_request && ($locale->code == $locale_request) ) {
                        $carouselTranslation->title = $data['title'];
                        $carouselTranslation->save();
                    }
                }
            } else {
                $route = request()->route() ? request()->route()->getName() : "";

                if ($route == 'mobikul.carousel.update') {
                    $carouselTranslations = $this->carouselTranslationRepository->findWhere([
                        'mobikul_carousel_id'   => $carousel->id,
                        'channel'               => $channel->code,
                    ])->pluck('id')->toArray();
                    
                    if ( $carouselTranslations ) {
                        $this->carouselTranslationRepository->destroy($carouselTranslations);
                    }
                }
            }
        }
    }
}