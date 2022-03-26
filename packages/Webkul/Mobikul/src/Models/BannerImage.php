<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Models\Channel;
use Webkul\Mobikul\Contracts\BannerImage as BannerImageContract;

class BannerImage extends Model implements BannerImageContract
{
    public $timestamps = true;

    protected $table = 'mobikul_banners';

    protected $fillable = [
        'sort_order',
        'product_category_id',
        'type',
        'status'
    ];

    /**
     * Get the banner image channels entries that are associated with banner image.
     * May be one for each locale and each channel.
     */
    public function translations()
    {
        return $this->hasMany(BannerImageTranslationProxy::modelClass(),'mobikul_banner_id');
    }
    
    /**
     * Get image url for the Banner image.
     */
    public function image_url()
    {
        if (! $this->image)
            return;

        return Storage::url($this->image);
    }

    /**
     * Get image url for the Banner image.
     */
    public function getImageUrlAttribute()
    {
        return $this->image_url();
    }

    /**
     * Get channels array for the banner image.
     */
    public function bannerImageChannelsArray()
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
