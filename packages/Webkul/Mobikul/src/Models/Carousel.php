<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Models\Channel;
use Webkul\Mobikul\Contracts\Carousel as CarouselContract;

/**
 * Class Carousel
 *
 * @package Webkul\Mobikul\Models
 *
 */
class Carousel extends Model implements CarouselContract
{
    protected $table = 'mobikul_carousel';

    protected $fillable = [
        'background_color',
        'type',
        'sort_order',
        'status'
    ];

    /**
     * Get the Carousel translation and channels entries that are associated with Carousel.
     * May be one for each locale and each channel.
     */
    public function translations()
    {
        return $this->hasMany(CarouselTranslationProxy::modelClass(),'mobikul_carousel_id');
    }

    /**
     * Get image url for the carousel image.
     */
    public function image_url()
    {
        if (! $this->image)
            return;

        return Storage::url($this->image);
    }

    /**
     * Get image url for the carousel image.
     */
    public function getImageUrlAttribute()
    {
        return $this->image_url();
    }

    /**
     * Get channels array for the Carousel.
     */
    public function carouselChannelsArray()
    {
        $channels   = [];
        
        foreach ($this->translations as $translation) {
            $channelList = Channel::query()->pluck('code')->toArray();
            $channelDetail = Channel::query()->where('code', $translation->channel)->first();
            
            if (in_array($translation->channel, $channelList) && isset($channelDetail->code) && !in_array($channelDetail->code, $channels)) {
                array_push($channels, $channelDetail->code);
            }
        }
        
        return $channels;
    }
}
