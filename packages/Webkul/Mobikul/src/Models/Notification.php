<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Models\Channel;
use Webkul\Mobikul\Contracts\Notification as NotificationContract;

class Notification extends Model implements NotificationContract
{
    public $timestamps = true;

    protected $table = 'mobikul_notifications';

    protected $guarded = ['_token'];

    protected $fillable = [
        'type',
        'product_category_id',
        'status',
    ];

    /**
     * Get the Notification translation and channels entries that are associated with Notification.
     * May be one for each locale and each channel.
     */
    public function translations()
    {
        return $this->hasMany(NotificationTranslationProxy::modelClass(),'mobikul_notification_id');
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
     * Get channels array for the Notification.
     */
    public function notificationChannelsArray()
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
