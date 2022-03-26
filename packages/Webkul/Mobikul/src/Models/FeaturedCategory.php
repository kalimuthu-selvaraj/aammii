<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Webkul\Category\Models\CategoryProxy;
use Webkul\Mobikul\Contracts\FeaturedCategory as FeaturedCategoryContract;

class FeaturedCategory extends Model implements FeaturedCategoryContract
{
    public $timestamps = true;

    protected $table = 'mobikul_featured_categories';

    protected $guarded = ['_token'];

    protected $fillable = [
        'sort_order',
        'category_id',
        'status'
    ];

    /**
     * Get the featured category channels entries that are associated with featured category.
     * May be one for each locale and each channel.
     */
    public function featured_category_channels()
    {
        return $this->hasMany(FeaturedCategoryChannelProxy::modelClass(), 'featured_category_id');
    }

    /**
     * Category details relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(CategoryProxy::modelClass());
    }

    /**
     * Category details relation.
     *
     * @param string $locale
     * @return array
     */
    public function category_name($locale)
    {
        $categoryTranslation = $this->category->translations->where('locale', $locale)->first();
        
        return $categoryTranslation->name ?? $this->category->name;
    }

    /**
     * Get image url for the featured category image.
     */
    public function image_url()
    {
        if (! $this->image)
            return;

        return Storage::url($this->image);
    }

    /**
     * Get image url for the featured category image.
     */
    public function getImageUrlAttribute()
    {
        return $this->image_url();
    }

    /**
     * Get channels array for the featured category.
     */
    public function featuredCategoryChannelsArray()
    {
        $channels   = [];
        
        foreach ($this->featured_category_channels as $key => $channel) {
            array_push($channels, $channel->channel_id);
        }

        return $channels;
    }
}
