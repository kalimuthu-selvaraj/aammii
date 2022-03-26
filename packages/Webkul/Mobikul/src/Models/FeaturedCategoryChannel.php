<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Mobikul\Contracts\FeaturedCategoryChannel as FeaturedCategoryChannelContract;

class FeaturedCategoryChannel extends Model implements FeaturedCategoryChannelContract
{

    protected $table = 'mobikul_featured_category_channels';

    protected $guarded = ['_token'];

    public $timestamps = false;

    protected $fillable = [
        'channel_id',
        'featured_category_id',
    ];

    /**
     * Get the featured category that owns the attribute value.
     */
    public function featured_category()
    {
        return $this->belongsTo(FeaturedCategoryProxy::modelClass());
    }
}